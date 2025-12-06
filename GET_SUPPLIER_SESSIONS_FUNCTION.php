<?php
/**
 * FONCTION À AJOUTER DANS chat.php
 *
 * Cette fonction doit être ajoutée dans la classe ChatAPI
 * et l'action 'get_supplier_sessions' doit être ajoutée dans le switch case de handleGet()
 */

/**
 * Dans handleGet(), ajouter ce case :
 *
 * case 'get_supplier_sessions':
 *     $this->getSupplierSessions();
 *     break;
 */

/**
 * Récupérer toutes les sessions de chat pour un vendeur/fournisseur spécifique
 * Utilisé par l'interface admin pour afficher toutes les conversations avec les clients
 */
public function getSupplierSessions() {
    try {
        $supplierId = $_GET['supplier_id'] ?? null;

        // Validation du paramètre obligatoire
        if (!$supplierId) {
            $this->sendResponse(400, [
                'success' => false,
                'error' => 'supplier_id requis'
            ]);
            return;
        }

        // 🔹 Récupère toutes les sessions avec leurs messages en une seule requête
        // Jointure entre sessions_chat et session_messages pour optimiser les performances
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
                sm.created_at AS message_timestamp,
                sm.product_id AS message_product_id,
                sm.product_price AS message_product_price,
                sm.product_image AS message_product_image
            FROM sessions_chat sc
            LEFT JOIN session_messages sm ON sm.session_id = sc.id
            WHERE sc.supplier_id = ?
            ORDER BY sc.created_at DESC, sm.created_at ASC
        ", [$supplierId]);

        // Si aucune session trouvée, retourner un tableau vide
        if (!$rows || count($rows) === 0) {
            $this->sendResponse(200, [
                'success' => true,
                'total' => 0,
                'sessions' => []
            ]);
            return;
        }

        // 🔹 Regrouper les résultats par session
        $sessions = [];
        $unreadCounts = []; // Pour calculer les messages non lus par session

        foreach ($rows as $row) {
            $sessionId = $row['session_id'];

            // Initialiser la session si elle n'existe pas encore dans le tableau
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
                    'user_name' => $row['user_email'], // Vous pouvez ajouter user_name si disponible
                    'user_email' => $row['user_email'],
                    'created_at' => $row['session_created_at'],
                    'messages' => []
                ];
                $unreadCounts[$sessionId] = 0;
            }

            // Ajouter le message si présent
            if ($row['message_id']) {
                $message = [
                    'id' => (int)$row['message_id'],
                    'text' => $row['message_text'],
                    'message' => $row['message_text'], // Compatibilité avec le frontend
                    'sender' => $row['message_sender'],
                    'timestamp' => $row['message_timestamp'],
                    'created_at' => $row['message_timestamp'],
                    'product' => null
                ];

                // Si le message contient des infos produit
                if ($row['message_product_id']) {
                    $message['product'] = [
                        'id' => $row['message_product_id'],
                        'price' => $row['message_product_price'],
                        'image' => $row['message_product_image']
                    ];
                }

                $sessions[$sessionId]['messages'][] = $message;

                // Compter les messages non lus (messages du client non marqués comme lus)
                // Pour l'instant, on considère tous les messages du client comme non lus
                // Vous pouvez ajouter un champ 'is_read' dans session_messages si nécessaire
                if ($row['message_sender'] === 'user') {
                    $unreadCounts[$sessionId]++;
                }
            }
        }

        // 🔹 Réindexer pour un tableau simple et ajouter unread_count
        $sessionsArray = [];
        foreach ($sessions as $sessionId => $session) {
            $session['unread_count'] = $unreadCounts[$sessionId];
            $sessionsArray[] = $session;
        }

        // 🔹 Réponse finale
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

/**
 * OPTIONNEL : Fonction pour marquer les messages comme lus côté fournisseur
 *
 * À ajouter dans handlePost() :
 *
 * case 'mark_supplier_messages_read':
 *     $this->markSupplierMessagesRead();
 *     break;
 */

/**
 * Marquer tous les messages d'une session comme lus côté fournisseur
 */
public function markSupplierMessagesRead() {
    try {
        $input = json_decode(file_get_contents('php://input'), true);

        $sessionId = $input['session_id'] ?? null;
        $supplierId = $input['supplier_id'] ?? null;

        if (!$sessionId || !$supplierId) {
            $this->sendResponse(400, [
                'success' => false,
                'error' => 'session_id et supplier_id requis'
            ]);
            return;
        }

        // Vérifier que la session appartient bien au fournisseur
        $sessionCheck = $this->db->query("
            SELECT id FROM sessions_chat
            WHERE id = ? AND supplier_id = ?
        ", [$sessionId, $supplierId]);

        if (!$sessionCheck || count($sessionCheck) === 0) {
            $this->sendResponse(403, [
                'success' => false,
                'error' => 'Accès non autorisé à cette session'
            ]);
            return;
        }

        // Si vous avez un champ is_read dans session_messages :
        // Marquer tous les messages du client comme lus
        /*
        $this->db->query("
            UPDATE session_messages
            SET is_read = 1
            WHERE session_id = ? AND sender = 'user' AND is_read = 0
        ", [$sessionId]);
        */

        $this->sendResponse(200, [
            'success' => true,
            'message' => 'Messages marqués comme lus'
        ]);

    } catch (Exception $e) {
        $this->sendResponse(500, [
            'success' => false,
            'error' => 'Erreur serveur: ' . $e->getMessage()
        ]);
    }
}

?>
