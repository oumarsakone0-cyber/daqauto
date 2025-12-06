<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

// Gérer les requêtes OPTIONS pour CORS
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

require_once 'config/database.php';

class ChatAPI {
    private $db;

    public function __construct() {
        $this->db = new Database();
    }

    public function handleRequest() {
        $method = $_SERVER['REQUEST_METHOD'];
        $action = $_GET['action'] ?? '';

        try {
            switch ($method) {
                case 'GET':
                    $this->handleGet($action);
                    break;
                case 'POST':
                    $this->handlePost($action);
                    break;
                default:
                    $this->sendResponse(405, ['error' => 'Méthode non autorisée']);
            }
        } catch (Exception $e) {
            $this->sendResponse(500, ['error' => 'Erreur serveur: ' . $e->getMessage()]);
        }
    }

    private function handleGet($action) {
        switch ($action) {
            case 'get_user':
                $this->getUser();
                break;
            case 'get_suppliers':
                $this->getUserSuppliersWithMessages();
                break;
            case 'get_conversations':
                $this->getConversations();
                break;
            case 'get_messages':
                $this->getMessages();
                break;
            case 'check_session':
                $this->checkSession();
                break;
            case 'get_unread_count':
                $this->getUnreadCount();
                break;
            case 'get_sessions_chat':
                $this->getSessionsChat();
                break;
            case 'get_session_messages':
                $this->getSessionMessages();
                break;
            case 'get_supplier_sessions':  // ⬅️ NOUVEAU ENDPOINT
                $this->getSupplierSessions();
                break;
            default:
                $this->sendResponse(404, ['error' => 'Action non trouvée']);
        }
    }

    private function handlePost($action) {
        switch ($action) {
            case 'create_user':
                $this->createUser();
                break;
            case 'create_supplier':
                $this->createNewChat();
                break;
            case 'send_message':
                $this->sendMessage();
                break;
            case 'start_session':
                $this->startSession();
                break;
            case 'end_session':
                $this->endSession();
                break;
            case 'mark_as_read':
                $this->markAsRead();
                break;
            case 'update_supplier_status':
                $this->updateSupplierStatus();
                break;
             case 'create_session_chat':
                $this->createSessionChat();
                break;
            case 'add_session_message':
                $this->addSessionMessage();
                break;
            default:
                $this->sendResponse(404, ['error' => 'Action non trouvée']);
        }
    }

    /**
     * Générer un code utilisateur unique
     */
    private function generateUserCode() {
        return 'USER_' . strtoupper(uniqid() . '_' . bin2hex(random_bytes(4)));
    }

    public function createSessionChat() {
        try {
            $input = json_decode(file_get_contents('php://input'), true);

            $productId = $input['product_id'] ?? null;
            $productName = $input['product_name'] ?? null;
            $productImage = $input['product_image'] ?? null;
            $productPrice = $input['product_price'] ?? null;
            $productRating = $input['product_rating'] ?? null;
            $supplierId = $input['supplier_id'] ?? null;
            $supplierName = $input['supplier_name'] ?? null;
            $userId = $input['user_id'] ?? null;
            $userEmail = $input['user_email'] ?? null;

            // Validation des champs obligatoires
            if (!$productId || !$supplierId || !$userId || !$userEmail) {
                $this->sendResponse(400, [
                    'success' => false,
                    'error' => 'Champs requis manquants (product_id, supplier_id, user_id, user_email)'
                ]);
                return;
            }

            // Vérifier si une session existe déjà pour ce produit, fournisseur et utilisateur
            $existingSession = $this->db->query("
                SELECT * FROM sessions_chat
                WHERE product_id = ? AND supplier_id = ? AND user_id = ?
                ORDER BY created_at DESC LIMIT 1
            ", [$productId, $supplierId, $userId]);

            if ($existingSession && count($existingSession) > 0) {
                // Session existante trouvée
                $session = $existingSession[0];

                $this->sendResponse(200, [
                    'success' => true,
                    'session_id' => $session['id'],
                    'message' => 'Session existante récupérée',
                    'session' => $session
                ]);
                return;
            }

            // Créer une nouvelle session_chat
            $this->db->query("
                INSERT INTO sessions_chat
                (product_id, product_name, product_image, product_price, product_rating,
                 supplier_id, supplier_name, user_id, user_email, created_at)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())
            ", [
                $productId,
                $productName,
                $productImage,
                $productPrice,
                $productRating,
                $supplierId,
                $supplierName,
                $userId,
                $userEmail
            ]);

            $sessionId = $this->db->lastInsertId();

            // Récupérer la session créée
            $newSession = $this->db->query("
                SELECT * FROM sessions_chat WHERE id = ?
            ", [$sessionId]);

            $this->sendResponse(201, [
                'success' => true,
                'session_id' => $sessionId,
                'message' => 'Nouvelle session créée avec succès',
                'session' => $newSession[0] ?? null
            ]);

        } catch (Exception $e) {
            $this->sendResponse(500, [
                'success' => false,
                'error' => 'Erreur serveur: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Récupérer toutes les sessions_chat d'un utilisateur
     */
    public function getSessionsChat() {
    try {
        $userEmail = $_GET['user_email'] ?? null;
        $userId = $_GET['user_id'] ?? null;

        if (!$userEmail && !$userId) {
            $this->sendResponse(400, [
                'success' => false,
                'error' => 'user_email ou user_id requis'
            ]);
            return;
        }

        // 🔹 Récupère toutes les sessions avec leurs messages en une seule requête
        $rows = $this->db->query("
            SELECT
                sc.id AS session_id,
                sc.product_id,
                sc.product_name,
                sc.product_image,
                sc.product_price,
                sc.product_rating,
                sc.supplier_id,
                sc.supplier_name,
                sc.user_id,
                sc.user_email,
                sc.created_at AS session_created_at,
                sm.id AS message_id,
                sm.message AS message_text,
                sm.sender AS message_sender,
                sm.message_type AS message_type,
                sm.created_at AS message_timestamp,
                sm.product_id AS message_product_id,
                sm.product_name AS message_product_name,
                sm.product_price AS message_product_price,
                sm.product_image AS message_product_image
            FROM sessions_chat sc
            LEFT JOIN session_messages sm ON sm.session_id = sc.id
            WHERE sc.user_id = ?
            ORDER BY sc.created_at DESC, sm.created_at ASC
        ", [$userId]);

        if (!$rows || count($rows) === 0) {
            $this->sendResponse(200, [
                'success' => true,
                'total' => 0,
                'sessions' => [],
                'sessions_by_supplier' => []
            ]);
            return;
        }

        // 🔹 Regrouper par session
        $sessions = [];
        foreach ($rows as $row) {
            $sessionId = $row['session_id'];

            if (!isset($sessions[$sessionId])) {
                $sessions[$sessionId] = [
                    'id' => $sessionId,
                    'product_id' => $row['product_id'],
                    'product_name' => $row['product_name'],
                    'product_image' => $row['product_image'],
                    'product_price' => $row['product_price'],
                    'product_rating' => $row['product_rating'],
                    'supplier_id' => $row['supplier_id'],
                    'supplier_name' => $row['supplier_name'],
                    'user_id' => $row['user_id'],
                    'user_email' => $row['user_email'],
                    'created_at' => $row['session_created_at'],
                    'messages' => []
                ];
            }

            // Ajouter le message si présent
            if ($row['message_id']) {
                $message = [
                    'id' => (int)$row['message_id'],
                    'text' => $row['message_text'],
                    'sender' => $row['message_sender'],
                    'message_type' => $row['message_type'] ?? 'text',
                    'timestamp' => $row['message_timestamp']
                ];

                // Ajouter les infos produit si présentes
                if ($row['message_product_id']) {
                    $message['product'] = [
                        'id' => $row['message_product_id'],
                        'name' => $row['message_product_name'],
                        'price' => $row['message_product_price'],
                        'image' => $row['message_product_image']
                    ];
                }

                $sessions[$sessionId]['messages'][] = $message;
            }
        }

        // Réindexer pour un tableau simple
        $sessions = array_values($sessions);

        // 🔹 Grouper par fournisseur
        $sessionsBySupplier = [];
        foreach ($sessions as $session) {
            $supplierId = $session['supplier_id'];
            if (!isset($sessionsBySupplier[$supplierId])) {
                $sessionsBySupplier[$supplierId] = [
                    'supplier_id' => $supplierId,
                    'supplier_name' => $session['supplier_name'],
                    'sessions' => []
                ];
            }
            $sessionsBySupplier[$supplierId]['sessions'][] = $session;
        }

        // 🔹 Réponse finale
        $this->sendResponse(200, [
            'success' => true,
            'total' => count($sessions),
            'sessions' => $sessions,
            'sessions_by_supplier' => array_values($sessionsBySupplier)
        ]);

    } catch (Exception $e) {
        $this->sendResponse(500, [
            'success' => false,
            'error' => 'Erreur serveur: ' . $e->getMessage()
        ]);
    }
}

    /**
     * ⬇️ NOUVEAU ENDPOINT POUR LE CHAT VENDEUR ⬇️
     * Récupérer toutes les sessions de chat pour un vendeur/fournisseur spécifique
     */
    public function getSupplierSessions() {
        try {
            $supplierId = $_GET['supplier_id'] ?? null;

            if (!$supplierId) {
                $this->sendResponse(400, [
                    'success' => false,
                    'error' => 'supplier_id requis'
                ]);
                return;
            }

            $rows = $this->db->query("
                SELECT
                    sc.id AS session_id,
                    sc.product_id,
                    sc.product_name,
                    sc.product_image,
                    sc.product_price,
                    sc.product_rating,
                    sc.supplier_id,
                    sc.supplier_name,
                    sc.user_id,
                    sc.user_email,
                    sc.created_at AS session_created_at,
                    sm.id AS message_id,
                    sm.message AS message_text,
                    sm.sender AS message_sender,
                    sm.message_type AS message_type,
                    sm.created_at AS message_timestamp,
                    sm.read_at AS message_read_at,
                    sm.product_id AS message_product_id,
                    sm.product_name AS message_product_name,
                    sm.product_price AS message_product_price,
                    sm.product_image AS message_product_image
                FROM sessions_chat sc
                LEFT JOIN session_messages sm ON sm.session_id = sc.id
                WHERE sc.supplier_id = ?
                ORDER BY sc.created_at DESC, sm.created_at ASC
            ", [$supplierId]);

            if (!$rows || count($rows) === 0) {
                $this->sendResponse(200, [
                    'success' => true,
                    'total' => 0,
                    'sessions' => []
                ]);
                return;
            }

            $sessions = [];
            $unreadCounts = [];

            foreach ($rows as $row) {
                $sessionId = $row['session_id'];

                if (!isset($sessions[$sessionId])) {
                    $sessions[$sessionId] = [
                        'id' => $sessionId,
                        'product_id' => $row['product_id'],
                        'product_name' => $row['product_name'],
                        'product_image' => $row['product_image'],
                        'product_price' => $row['product_price'],
                        'product_rating' => $row['product_rating'],
                        'supplier_id' => $row['supplier_id'],
                        'supplier_name' => $row['supplier_name'],
                        'user_id' => $row['user_id'],
                        'user_name' => $row['user_email'],
                        'user_email' => $row['user_email'],
                        'created_at' => $row['session_created_at'],
                        'messages' => []
                    ];
                    $unreadCounts[$sessionId] = 0;
                }

                if ($row['message_id']) {
                    $message = [
                        'id' => (int)$row['message_id'],
                        'text' => $row['message_text'],
                        'message' => $row['message_text'],
                        'sender' => $row['message_sender'],
                        'message_type' => $row['message_type'] ?? 'text',
                        'timestamp' => $row['message_timestamp'],
                        'created_at' => $row['message_timestamp'],
                        'product' => null
                    ];

                    // Ajouter les infos produit si présentes
                    if ($row['message_product_id']) {
                        $message['product'] = [
                            'id' => $row['message_product_id'],
                            'name' => $row['message_product_name'],
                            'price' => $row['message_product_price'],
                            'image' => $row['message_product_image']
                        ];
                    }

                    $sessions[$sessionId]['messages'][] = $message;

                    // Count as unread only if sender is 'user' AND read_at is NULL
                    if ($row['message_sender'] === 'user' && empty($row['message_read_at'])) {
                        $unreadCounts[$sessionId]++;
                    }
                }
            }

            $sessionsArray = [];
            foreach ($sessions as $sessionId => $session) {
                $session['unread_count'] = $unreadCounts[$sessionId];
                $sessionsArray[] = $session;
            }

            $this->sendResponse(200, [
                'success' => true,
                'total' => count($sessionsArray),
                'sessions' => $sessionsArray
            ]);

        } catch (Exception $e) {
            $this->sendResponse(500, [
                'success' => false,
                'error' => 'Erreur serveur: ' . $e->getMessage()
            ]);
        }
    }

    // ... Le reste du code reste identique ...

    /**
     * Créer ou récupérer un utilisateur
     */
    public function createUser() {
        try {
            $input = json_decode(file_get_contents('php://input'), true);
            $existingCode = $input['user_code'] ?? null;
            $email = $input['email'] ?? null;
            $name = $input['name'] ?? null;

            // Si un code existe, vérifier s'il est valide
            if ($existingCode) {
                $user = $this->db->query("SELECT * FROM chat_users WHERE user_code = ?", [$existingCode]);
                $user = $user[0] ?? null; // car query() retourne toujours un tableau

                if ($user) {
                    $this->sendResponse(200, [
                        'success' => true,
                        'user_code' => $input['user_code'],
                        'user_id' => $user['id'],
                        'email' => $user['email'],
                        'name' => $user['name'],
                        'message' => 'Utilisateur existant récupéré'
                    ]);
                    return;
                }
            }

            // Créer un nouveau utilisateur
            $userCode = $this->generateUserCode();
            $userAgent = $_SERVER['HTTP_USER_AGENT'] ?? '';
            $ipAddress = $_SERVER['REMOTE_ADDR'] ?? '';

            $userId = $this->db->query("
                INSERT INTO chat_users (user_code, email, name, user_agent, ip_address, created_at)
                VALUES (?, ?, ?, ?, ?, NOW())
            ", [$input['user_code'], $email, $name, $userAgent, $ipAddress]);

            $this->sendResponse(201, [
                'success' => true,
                'user_code' => $existingCode,
                'user_id' => $input['user_code'],
                'email' => $input['email'],
                'name' => $input['full_name'],
                'message' => 'Nouvel utilisateur créé'
            ]);

        } catch (Exception $e) {
            $this->sendResponse(500, ['error' => 'Erreur lors de la création de l\'utilisateur: ' . $e->getMessage()]);
        }
    }

    /**
     * Récupérer un utilisateur
     */
    public function getUser() {
        try {
            $userCode = $_GET['user_code'] ?? null;

            if (!$userCode) {
                $this->sendResponse(400, ['error' => 'Code utilisateur requis']);
                return;
            }

            // Utilisation de Database::query
            $users = $this->db->query(
                "SELECT id, user_code, email, name, created_at FROM chat_users WHERE user_code = ?",
                [$userCode]
            );

            $user = $users[0] ?? null; // Récupérer le premier résultat

            if ($user) {
                $this->sendResponse(200, [
                    'success' => true,
                    'user' => $user
                ]);
            } else {
                $this->sendResponse(404, ['error' => 'Utilisateur non trouvé']);
            }

        } catch (Exception $e) {
            $this->sendResponse(500, ['error' => 'Erreur: ' . $e->getMessage()]);
        }
    }

    public function getSuppliers() {
        try {
            $input = json_decode(file_get_contents('php://input'), true);
            $userMail = $input['user_mail'] ?? null;

            if (!$userMail) {
                $this->sendResponse(400, ['success' => false, 'error' => 'user_mail requis']);
                return;
            }

            $suppliers = $this->db->query("
                SELECT id, supplier_name, supplier_logo, supplier_status, boutique_id, created_at
                FROM chats_suppliers
                WHERE user_mail = ?
                ORDER BY created_at DESC
            ", [$userMail]);

            $this->sendResponse(200, [
                'success' => true,
                'suppliers' => $suppliers
            ]);
        } catch (Exception $e) {
            $this->sendResponse(500, ['success' => false, 'error' => 'Erreur serveur: ' . $e->getMessage()]);
        }
    }

    public function getUserSuppliersWithMessages() {
        try {
            $input = json_decode(file_get_contents('php://input'), true);
            $userMail = $input['user_mail'] ?? null;

            if (!$userMail) {
                $this->sendResponse(400, ['success' => false, 'error' => 'user_mail requis']);
                return;
            }

            // 🔹 Récupérer tous les fournisseurs du user
            $suppliers = $this->db->query("
                SELECT id, supplier_name, supplier_logo, supplier_status, boutique_id
                FROM chats_suppliers
                WHERE user_mail = ?
                ORDER BY created_at DESC
            ", [$userMail]);

            $result = [];

            foreach ($suppliers as $s) {
                // 🔹 Récupérer les messages associés
                $messages = $this->db->query("
                    SELECT id, message, message_type, sender, created_at
                    FROM chat_messages
                    WHERE supplier_id = ?
                    ORDER BY created_at ASC
                ", [$s['id']]);

                // 🔹 Dernier message + nombre non lus
                $lastMessage = end($messages);
                $unreadCount = count(array_filter($messages, fn($m) => $m['is_read'] == 0));

                $result[] = [
                    'id' => $s['id'],
                    'supplier_name' => $s['supplier_name'],
                    'supplier_logo' => $s['supplier_logo'],
                    'supplier_status' => $s['supplier_status'],
                    'boutique_id' => $s['boutique_id'],
                    'messages' => $messages,
                    'last_message' => $lastMessage ?: null,
                    'unread_count' => $unreadCount
                ];
            }

            $this->sendResponse(200, [
                'success' => true,
                'suppliers' => $result
            ]);

        } catch (Exception $e) {
            $this->sendResponse(500, ['success' => false, 'error' => 'Erreur serveur: ' . $e->getMessage()]);
        }
    }

    // ... Le reste des fonctions sont identiques au fichier original ...
    // Je n'ai ajouté QUE l'endpoint get_supplier_sessions et son case dans handleGet()

    public function sendMessage() {
        try {
            $input = json_decode(file_get_contents('php://input'), true);

            $sessionId = $input['session_id'] ?? null;
            $sender = $input['sender'] ?? 'user';
            $message = $input['message'] ?? null;
            $messageType = $input['message_type'] ?? 'text'; // text, image, product
            $productId = $input['product_id'] ?? null;
            $productName = $input['product_name'] ?? null;
            $productPrice = $input['product_price'] ?? null;
            $productImage = $input['product_image'] ?? null;
            $imageUrl = $input['image_url'] ?? null;

            // Pour les messages texte, le message est obligatoire
            // Pour les images et produits, on peut avoir un message vide
            if (!$sessionId) {
                $this->sendResponse(400, ['error' => 'session_id est obligatoire']);
                return;
            }

            if ($messageType === 'text' && !$message) {
                $this->sendResponse(400, ['error' => 'message est obligatoire pour un message texte']);
                return;
            }

            $sessionResult = $this->db->query("
                SELECT * FROM sessions_chat
                WHERE id = ?
            ", [$sessionId]);

            $session = $sessionResult[0] ?? null;

            if (!$session) {
                $this->sendResponse(400, ['error' => 'Session invalide ou expirée']);
                return;
            }

            $this->db->beginTransaction();

            try {
                // Déterminer le contenu du message selon le type
                $finalMessage = $message;

                // Pour les images, on stocke l'URL dans le message
                if ($messageType === 'image' && $imageUrl) {
                    $finalMessage = $imageUrl;
                }

                // Pour les produits, on crée un message descriptif
                if ($messageType === 'product') {
                    $finalMessage = $message ?? 'Produit partagé';
                }

                $this->db->query("
                    INSERT INTO session_messages
                        (session_id, message, sender, message_type, product_id, product_name, product_price, product_image, created_at)
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, NOW())
                ", [
                    $sessionId,
                    $finalMessage,
                    $sender,
                    $messageType,
                    $productId,
                    $productName,
                    $productPrice,
                    $productImage ?? $imageUrl
                ]);

                $messageId = $this->db->lastInsertId();

                $this->db->commit();

                $newMessageResult = $this->db->query("
                    SELECT id, session_id, message, sender, message_type,
                        product_id, product_name, product_price, product_image,
                        DATE_FORMAT(created_at, '%Y-%m-%d %H:%i:%s') as created_at
                    FROM session_messages
                    WHERE id = ?
                ", [$messageId]);

                $newMessage = $newMessageResult[0] ?? null;

                $this->sendResponse(201, [
                    'success' => true,
                    'message' => $newMessage
                ]);

            } catch (Exception $e) {
                $this->db->rollBack();
                throw $e;
            }

        } catch (Exception $e) {
            $this->sendResponse(500, ['error' => 'Erreur serveur: ' . $e->getMessage()]);
        }
    }
 
    private function sendResponse($statusCode, $data) {
        http_response_code($statusCode);
        echo json_encode($data, JSON_UNESCAPED_UNICODE);
        exit;
    }
}

// Initialiser et traiter la requête
$api = new ChatAPI();
$api->handleRequest();
?>
