<?php
/**
 * 
 * @version 2.0.0
 * @author AdjamE Team
 */

// ============================================================================
// CONFIGURATION CORS ET HEADERS
// ============================================================================

header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With, Accept, Origin');
header('Access-Control-Allow-Credentials: true');
header('Access-Control-Max-Age: 86400');

// Gérer les requêtes OPTIONS pour CORS (preflight)
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

require_once 'config/database.php';

// Inclure PHPMailer depuis votre dossier existant
require_once 'phpmailer/Exception.php';
require_once 'phpmailer/PHPMailer.php';
require_once 'phpmailer/SMTP.php';
require_once 'utils/EmailService.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

class ProductsAPI {
    private $db;
    private $emailService;
    private $currentUser;
    private $currentBoutique;
    
    public function __construct() {
        $this->db = new Database();
        $this->emailService = new EmailService();
        $this->authenticateUser();
    }
    
    /**
     * Authentifier l'utilisateur à partir du token ou des paramètres
     */
    private function authenticateUser() {
        // Récupérer le token depuis les headers ou les paramètres
        $authHeader = $_SERVER['HTTP_AUTHORIZATION'] ?? '';
        $token = '';
        
        if (preg_match('/Bearer\s+(.*)$/i', $authHeader, $matches)) {
            $token = $matches[1];
        } else {
            $token = $_GET['token'] ?? $_POST['token'] ?? '';
        }
        
        // Récupérer l'ID utilisateur et boutique depuis les paramètres (pour les tests)
        $userId = $_GET['user_id'] ?? $_POST['user_id'] ?? null;
        $boutiqueId = $_GET['boutique_id'] ?? $_POST['boutique_id'] ?? null;
        
        if ($userId) {
            $this->currentUser = $this->getUserById($userId);
        }
        
        if ($boutiqueId) {
            $this->currentBoutique = $this->getBoutiqueById($boutiqueId);
        }
        
        // Si pas d'utilisateur trouvé, utiliser des valeurs par défaut pour les tests
        if (!$this->currentUser) {
            $this->currentUser = [
                'id' => 1,
                'full_name' => 'Utilisateur Test',
                'email' => 'test@adjame.com'
            ];
        }
        
        if (!$this->currentBoutique) {
            $this->currentBoutique = [
                'id' => 1,
                'name' => 'Boutique Test',
                'slug' => 'boutique-test'
            ];
        }
    }
    
    /**
     * Récupérer un utilisateur par son ID
     */
    private function getUserById($userId) {
        try {
            $sql = "SELECT id, full_name, email, phone FROM adjame_users WHERE id = ? AND is_active = 1";
            $users = $this->db->query($sql, [$userId]);
            return $users[0] ?? null;
        } catch (Exception $e) {
            error_log("Erreur récupération utilisateur: " . $e->getMessage());
            return null;
        }
    }
    
    /**
     * Récupérer une boutique par son ID
     */
    private function getBoutiqueById($boutiqueId) {
        try {
            $sql = "SELECT id, name, slug, business_type, market FROM adjame_boutique WHERE id = ? AND is_active = 1";
            $boutiques = $this->db->query($sql, [$boutiqueId]);
            return $boutiques[0] ?? null;
        } catch (Exception $e) {
            error_log("Erreur récupération boutique: " . $e->getMessage());
            return null;
        }
    }
    
    /**
     * Vérifier si l'utilisateur a accès à une boutique
     */
    private function hasAccessToBoutique($userId, $boutiqueId) {
        try {
            $sql = "
                SELECT ub.role, ub.permissions 
                FROM adjame_user_boutique ub 
                WHERE ub.user_id = ? AND ub.boutique_id = ? AND ub.is_active = 1
            ";
            $result = $this->db->query($sql, [$userId, $boutiqueId]);
            return !empty($result);
        } catch (Exception $e) {
            return false;
        }
    }
    
    public function handleRequest() {
        $method = $_SERVER['REQUEST_METHOD'];
        $path = $_GET['action'] ?? '';
        
        try {
            switch ($method) {
                case 'GET':
                    $this->handleGet($path);
                    break;
                case 'POST':
                    $this->handlePost($path);
                    break;
                case 'PUT':
                    $this->handlePut($path);
                    break;
                case 'DELETE':
                    $this->handleDelete($path);
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
            case 'list':
                $this->getProducts();  
                break;
            case 'decode-vin':
                $this->decodeVIN();  
                break;
            case 'get_cart':
                $this->getCartItems();
                break;
            case 'get_my_orders':
                $this->getMyOrders();
                break;
            case 'model_list':
                $this->getTruckBrands();
                break; 
            case 'model_list':
                $this->getTruckBrands();
                break;
            case 'trailer_brands':
                $this->getTrailerBrands();
                break;
            case 'trailer_types':
                $this->getTrailerTypes();
                break;
            case 'lists':
                $this->getProductsForResults();
                break;
            case 'show':
                $this->getProduct();
                break;
            case 'categories':
                $this->getCategories();
                break;
            case 'colors':
                $this->getColors();
                break;
            case 'sizes':
                $this->getSizes();
                break;
            case 'stats':
                $this->getStats();
                break;
            case 'search':
                $this->searchProducts();
                break;
            case 'most_viewed_products':
                $this->getMostViewedProductsOld(); // Ancienne version pour compatibilité
                break;
            case 'most_viewed':
                $this->getMostViewedProducts(); // Nouvelle version pour la page d'accueil
                break;
            case 'random_supplier':
                $this->getRandomSupplier();
                break;
            // NOUVELLES ACTIONS BOUTIQUES
            case 'boutique_info':
                $this->getBoutiqueInfo();
                break;
            case 'boutique_products':
                $this->getBoutiqueProducts();
                break;
            case 'tarifs_abidjan':
                $this->getTarifsAbidjan();
                break;
            case 'tarifs_interieur':
                $this->getTarifsInterieur();
                break;
            case 'premium_plans':
                $this->getPremiumPlans();
                break;
            case 'premium_subscription':
                $this->getBoutiqueSubscription();
                break;
            case 'premium_check_limits':
                $this->checkBoutiquePremiumLimits();
                break;
            case 'premium_subscribe':
                $this->subscribeBoutiqueToPremium();
                break;
            case 'premium_cancel':
                $this->cancelBoutiqueSubscription();
                break;
            case 'product_boosts':
                $this->getProductBoosts();
                break;
            default:
                $this->getProducts();
        }
    }
    
    private function handlePost($action) {
        switch ($action) {
            case 'create':
                $this->createProduct();
                break;
            case 'decodeVIN2':
                $this->decodeVIN2();
                break;
            case 'add_to_cart':
                $this->addToCart();
                break;
            case 'sync_cart':
                $this->syncCart();
                break;
            case 'get_my_favorite':
                $this->getUserFavorites();
                break;
            case 'addProductLike':
                $this->addLike();
                break;
            case 'upload_payment_proof':
                $this->uploadPaymentProof();
                break;
             case 'add_new_payment':
                $this->addNewPayment();
                break;
            case 'cancel_order':
                $this->cancelOrder();
                break;
            case 'upload-image':
                $this->uploadImage();
                break;
            // NOUVELLES ACTIONS BOUTIQUES POST
            case 'follow_boutique':
                $this->followBoutique();
                break;
            case 'unfollow_boutique':
                $this->unfollowBoutique();
                break;
            case 'contact_boutique':
                $this->contactBoutique();
                break;
            case 'create_order':
                $this->createOrder();
                break;
            case 'premium_subscribe':
                $this->subscribeToPremium();
                break;
            case 'premium_cancel':
                $this->cancelSubscription();
                break;
            case 'premium_renew':
                $this->renewSubscription();
                break;
            case 'boostproduct':
                $this->boostProduct();
                break;
            case 'activate_boost':
                $this->activateBoost();
                break;
            default:
                $this->sendResponse(404, ['error' => 'Action non trouvée']);
        }
    }
    
    private function handlePut($action) {
        switch ($action) {
            case 'update-boutique':
                $this->updateBoutique();
                break;
            case 'update_cart_quantity':
                $this->updateCartQuantity();
                break;
            case 'update':
                $this->updateProduct();
                break;
            case 'update-stock':
                $this->updateStock();
                break;
            case 'update-status':
                $this->updateStatus();
                break;
            default:
                $this->sendResponse(404, ['error' => 'Action non trouvée']);
        }
    }
    
    private function handleDelete($action) {
        switch ($action) {
            case 'delete':
                $this->deleteProduct();
                break;
            case 'delete-image':
                $this->deleteImage();
                break;
            case 'remove_from_cart':
                $this->removeFromCart();
                break;
            case 'clear_cart':
                $this->clearCart();
                break;
            default:
                $this->sendResponse(404, ['error' => 'Action non trouvée']);
        }
    }
    
    // ========== NOUVELLES FONCTIONS BOUTIQUES ==========
    
    /**
     * Récupérer les informations d'une boutique
     * GET /api/products.php?action=boutique_info&boutique_id=1
     */
    private function getBoutiqueInfo() {
        $boutiqueId = $_GET['boutique_id'] ?? $_GET['id'] ?? null;
        
        if (!$boutiqueId) {
            $this->sendResponse(400, ['error' => 'ID de boutique requis']);
            return;
        }
        
        try {
            $sql = "
                SELECT 
                    b.id,
                    b.name,
                    b.slug,
                    b.business_type,
                    b.market,
                    b.description,
                    b.logo_url,
                    b.phone,
                    b.email,
                    b.address,
                    b.is_verified,
                    b.subscription_plan,
                    b.created_at,
                    b.updated_at,
                    TIMESTAMPDIFF(YEAR, b.created_at, NOW()) as years_experience,
                    COUNT(DISTINCT p.id) as total_products,
                    COUNT(DISTINCT CASE WHEN p.status = 'Actif' THEN p.id END) as active_products,
                    AVG(CASE WHEN pr.rating IS NOT NULL THEN pr.rating END) as rating,
                    COUNT(DISTINCT pr.id) as total_reviews,
                    SUM(p.sales_count) as total_sales
                FROM adjame_boutique b
                LEFT JOIN adjame_products p ON b.id = p.boutique_id AND p.is_active = 1
                LEFT JOIN adjame_product_reviews pr ON p.id = pr.product_id
                WHERE b.id = ? AND b.is_active = 1
                GROUP BY b.id
            ";
            
            $boutiques = $this->db->query($sql, [$boutiqueId]);
            
            if (empty($boutiques)) {
                $this->sendResponse(404, ['error' => 'Boutique introuvable']);
                return;
            }
            
            $boutique = $boutiques[0];
            
            // Formater les données
            $boutique['id'] = (int)$boutique['id'];
            $boutique['total_products'] = (int)$boutique['total_products'];
            $boutique['active_products'] = (int)$boutique['active_products'];
            $boutique['total_reviews'] = (int)$boutique['total_reviews'];
            $boutique['total_sales'] = (int)$boutique['total_sales'];
            $boutique['years_experience'] = max(1, (int)$boutique['years_experience']);
            $boutique['rating'] = $boutique['rating'] ? round((float)$boutique['rating'], 1) : 4.5;
            $boutique['is_verified'] = (bool)$boutique['is_verified'];
            $boutique['premium'] = $boutique['subscription_plan'] === 'premium';
            
            // Valeurs par défaut
            $boutique['logo'] = $boutique['logo_url'] ?: 'https://via.placeholder.com/120x120?text=Logo';
            $boutique['location'] = $boutique['market'] ?: 'Côte d\'Ivoire';
            
            // Informations supplémentaires simulées
            $boutique['followers_count'] = rand(100, 10000);
            $boutique['response_rate'] = rand(85, 99);
            $boutique['response_time'] = 'Quelques heures';
            
            $this->sendResponse(200, [
                'success' => true,
                'data' => $boutique
            ]);
            
        } catch (Exception $e) {
            error_log("Erreur dans getBoutiqueInfo: " . $e->getMessage());
            $this->sendResponse(500, [
                'success' => false,
                'error' => 'Erreur lors de la récupération des informations de la boutique: ' . $e->getMessage()
            ]);
        }
    }

/**
 * Récupérer le panier d'un utilisateur
 * GET /api/products.php?action=get_cart&user_id=1
 */
private function getCartItems() {
    $userId = $_GET['user_id'] ?? null;
    
    if (!$userId) {
        $this->sendResponse(401, [
            'success' => false,
            'error' => 'user_id requis'
        ]);
        return;
    }
    
    try {
        $sql = "
            SELECT 
                c.*,
                p.stock as current_stock,
                p.status as product_status,
                p.is_active as product_active
            FROM adjame_cart_items c
            LEFT JOIN adjame_products p ON c.product_id = p.id
            WHERE c.user_id = ?
            ORDER BY c.created_at DESC
        ";
        
        $items = $this->db->query($sql, [$userId]);
        
        if (!is_array($items)) {
            $items = [];
        }
        
        // Formater et nettoyer les données
        $validItems = [];
        foreach ($items as $item) {
            // Vérifier si le produit existe toujours
            if ($item['product_active'] != 1 || $item['product_status'] != 'Actif') {
                // Supprimer l'item du panier si le produit n'est plus disponible
                $this->db->query("DELETE FROM adjame_cart_items WHERE id = ?", [$item['id']]);
                continue;
            }
            
            // Ajuster la quantité si le stock est insuffisant
            if ($item['quantity'] > $item['current_stock']) {
                $item['quantity'] = max(1, $item['current_stock']);
                $this->db->query(
                    "UPDATE adjame_cart_items SET quantity = ? WHERE id = ?",
                    [$item['quantity'], $item['id']]
                );
            }
            
            // Formater les données
            $item['id'] = (int)$item['id'];
            $item['product_id'] = (int)$item['product_id'];
            $item['boutique_id'] = (int)$item['boutique_id'];
            $item['quantity'] = (int)$item['quantity'];
            $item['unit_price'] = (float)$item['unit_price'];
            $item['boutique_premium'] = (bool)$item['boutique_premium'];
            $item['boutique_verified'] = (bool)$item['boutique_verified'];
            
            // Créer l'item_id pour compatibilité avec le store Pinia
            $item['item_id'] = $item['id'];
            
            // Compatibilité avec les noms de champs du store
            $item['name'] = $item['product_name'];
            $item['colors'] = [['name' => $item['color_name'], 'hex_value' => $item['color_hex']]];
            $item['vin_numbers'] = [$item['vin_number']];
            $item['trim_numbers'] = [$item['trim_number']];
            $item['colorHex'] = $item['color_hex'];
            $item['color'] = $item['color_name'];
            
            $validItems[] = $item;
        }
        
        $this->sendResponse(200, [
            'success' => true,
            'data' => $validItems,
            'total' => count($validItems)
        ]);
        
    } catch (Exception $e) {
        error_log("Erreur dans getCartItems: " . $e->getMessage());
        $this->sendResponse(500, [
            'success' => false,
            'error' => 'Erreur lors de la récupération du panier: ' . $e->getMessage()
        ]);
    }
}

/**
 * Ajouter un produit au panier
 * POST /api/products.php?action=add_to_cart
 */
private function addToCart() {
    $input = json_decode(file_get_contents('php://input'), true);
    
    $userId = $input['user_id'] ?? null;
    $productId = $input['product_id'] ?? null;
    $quantity = $input['quantity'] ?? 1;
    $colorHex = $input['color_hex'] ?? null;
    
    if (!$userId || !$productId) {
        $this->sendResponse(400, [
            'success' => false,
            'error' => 'user_id et product_id requis'
        ]);
        return;
    }
    
    try {
        // Récupérer les informations du produit
        $productSql = "
            SELECT 
                p.*,
                b.name as boutique_name,
                b.logo_url as boutique_logo,
                b.market as boutique_marche,
                b.business_type as boutique_type,
                b.subscription_plan as boutique_premium,
                b.is_verified as boutique_verified,
                b.address as boutique_address,
                b.description as boutique_description,
                b.phone as boutique_phone,
                (SELECT image_url FROM adjame_product_images WHERE product_id = p.id AND is_primary = 1 LIMIT 1) as primary_image
            FROM adjame_products p
            LEFT JOIN adjame_boutique b ON p.boutique_id = b.id
            WHERE p.id = ? AND p.is_active = 1 AND p.status = 'Actif'
        ";
        
        $products = $this->db->query($productSql, [$productId]);
        
        if (empty($products)) {
            $this->sendResponse(404, [
                'success' => false,
                'error' => 'Produit non trouvé ou indisponible'
            ]);
            return;
        }
        
        $product = $products[0];
        
        // Vérifier le stock
        if ($product['stock'] < $quantity) {
            $this->sendResponse(400, [
                'success' => false,
                'error' => 'Stock insuffisant',
                'available_stock' => (int)$product['stock']
            ]);
            return;
        }
        
        // Récupérer la couleur si spécifiée
        $colorName = null;
        if ($colorHex) {
            $colorSql = "SELECT name FROM adjame_colors WHERE hex_value = ?";
            $colors = $this->db->query($colorSql, [$colorHex]);
            $colorName = $colors[0]['name'] ?? null;
        }
        
        // Vérifier si l'item existe déjà
        $checkSql = "
            SELECT id, quantity 
            FROM adjame_cart_items 
            WHERE user_id = ? AND product_id = ? AND boutique_id = ? AND (color_hex = ? OR (color_hex IS NULL AND ? IS NULL))
        ";
        $existing = $this->db->query($checkSql, [$userId, $productId, $product['boutique_id'], $colorHex, $colorHex]);
        
        if (!empty($existing)) {
            // Mettre à jour la quantité
            $newQuantity = $existing[0]['quantity'] + $quantity;
            
            if ($newQuantity > $product['stock']) {
                $newQuantity = $product['stock'];
            }
            
            $updateSql = "UPDATE adjame_cart_items SET quantity = ?, updated_at = NOW() WHERE id = ?";
            $this->db->query($updateSql, [$newQuantity, $existing[0]['id']]);
            
            $cartItemId = $existing[0]['id'];
            $message = 'Quantité mise à jour dans le panier';
        } else {
            // Insérer un nouvel item
            $insertSql = "
                INSERT INTO adjame_cart_items (
                    user_id, product_id, boutique_id, quantity,
                    product_name, unit_price, primary_image, slug,
                    boutique_name, boutique_logo, boutique_marche, boutique_type,
                    boutique_premium, boutique_verified, boutique_address, boutique_description, boutique_phone,
                    vehicle_make, vehicle_model, vehicle_year, vin_number, trim_number, stock_number,
                    vehicle_mileage, fuel_type, color_name, color_hex,
                    created_at
                ) VALUES (
                    ?, ?, ?, ?,
                    ?, ?, ?, ?,
                    ?, ?, ?, ?,
                    ?, ?, ?, ?, ?,
                    ?, ?, ?, ?, ?, ?,
                    ?, ?, ?, ?,
                    NOW()
                )
            ";
            
            $this->db->query($insertSql, [
                $userId,
                $productId,
                $product['boutique_id'],
                $quantity,
                $product['name'],
                $product['unit_price'],
                $product['primary_image'],
                $product['slug'],
                $product['boutique_name'],
                $product['boutique_logo'],
                $product['boutique_marche'],
                $product['boutique_type'],
                $product['boutique_premium'] === 'premium' ? 1 : 0,
                $product['boutique_verified'],
                $product['boutique_address'],
                $product['boutique_description'],
                $product['boutique_phone'],
                $product['vehicle_make'],
                $product['vehicle_model'],
                $product['vehicle_year'],
                $input['vin_number'] ?? null,
                $input['trim_number'] ?? null,
                $product['stock_number'],
                $product['vehicle_mileage'],
                $product['fuel_type'],
                $colorName,
                $colorHex
            ]);
            
            $cartItemId = $this->db->lastInsertId();
            $message = 'Produit ajouté au panier';
        }
        
        // Récupérer l'item créé/mis à jour
        $itemSql = "SELECT * FROM adjame_cart_items WHERE id = ?";
        $cartItem = $this->db->query($itemSql, [$cartItemId])[0];
        
        $this->sendResponse(200, [
            'success' => true,
            'message' => $message,
            'data' => $cartItem
        ]);
        
    } catch (Exception $e) {
        error_log("Erreur dans addToCart: " . $e->getMessage());
        $this->sendResponse(500, [
            'success' => false,
            'error' => 'Erreur lors de l\'ajout au panier: ' . $e->getMessage()
        ]);
    }
}

/**
 * Synchroniser le panier local avec la base de données
 * POST /api/products.php?action=sync_cart
 */
private function syncCart() {
    $input = json_decode(file_get_contents('php://input'), true);
    
    $userId = $input['user_id'] ?? null;
    $localItems = $input['items'] ?? [];
    
    if (!$userId) {
        $this->sendResponse(400, [
            'success' => false,
            'error' => 'user_id requis'
        ]);
        return;
    }
    
    try {
        $this->db->beginTransaction();
        
        // Pour chaque item local, l'ajouter ou mettre à jour
        foreach ($localItems as $item) {
            $productId = $item['product_id'] ?? $item['id'];
            $quantity = $item['quantity'] ?? 1;
            $colorHex = $item['colorHex'] ?? $item['color_hex'] ?? null;
            
            // Vérifier si l'item existe déjà en BD
            $checkSql = "
                SELECT id, quantity 
                FROM adjame_cart_items 
                WHERE user_id = ? AND product_id = ? AND (color_hex = ? OR (color_hex IS NULL AND ? IS NULL))
            ";
            $existing = $this->db->query($checkSql, [$userId, $productId, $colorHex, $colorHex]);
            
            if (!empty($existing)) {
                // Garder la quantité la plus élevée
                $maxQuantity = max($existing[0]['quantity'], $quantity);
                $updateSql = "UPDATE adjame_cart_items SET quantity = ?, updated_at = NOW() WHERE id = ?";
                $this->db->query($updateSql, [$maxQuantity, $existing[0]['id']]);
            } else {
                // Créer un nouvel item (utiliser addToCart en interne)
                // Pour simplifier, on fait juste un INSERT basique
                $insertSql = "
                    INSERT INTO adjame_cart_items (
                        user_id, product_id, boutique_id, quantity,
                        product_name, unit_price, primary_image, slug,
                        color_hex, created_at
                    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())
                ";
                
                $this->db->query($insertSql, [
                    $userId,
                    $productId,
                    $item['boutique_id'],
                    $quantity,
                    $item['name'],
                    $item['unit_price'],
                    $item['primary_image'],
                    $item['slug'],
                    $colorHex
                ]);
            }
        }
        
        $this->db->commit();
        
        // Retourner le panier complet mis à jour
        $this->getCartItems();
        
    } catch (Exception $e) {
        $this->db->rollback();
        error_log("Erreur dans syncCart: " . $e->getMessage());
        $this->sendResponse(500, [
            'success' => false,
            'error' => 'Erreur lors de la synchronisation: ' . $e->getMessage()
        ]);
    }
}

/**
 * Mettre à jour la quantité d'un item
 * PUT /api/products.php?action=update_cart_quantity&item_id=1
 */
private function updateCartQuantity() {
    $itemId = $_GET['item_id'] ?? null;
    $input = json_decode(file_get_contents('php://input'), true);
    $quantity = $input['quantity'] ?? null;
    
    if (!$itemId || !$quantity) {
        $this->sendResponse(400, [
            'success' => false,
            'error' => 'item_id et quantity requis'
        ]);
        return;
    }
    
    try {
        $sql = "UPDATE adjame_cart_items SET quantity = ?, updated_at = NOW() WHERE id = ?";
        $this->db->query($sql, [(int)$quantity, $itemId]);
        
        $this->sendResponse(200, [
            'success' => true,
            'message' => 'Quantité mise à jour'
        ]);
        
    } catch (Exception $e) {
        $this->sendResponse(500, [
            'success' => false,
            'error' => 'Erreur lors de la mise à jour: ' . $e->getMessage()
        ]);
    }
}

/**
 * Supprimer un item du panier
 * DELETE /api/products.php?action=remove_from_cart&item_id=1
 */
private function removeFromCart() {
    $itemId = $_GET['item_id'] ?? null;
    
    if (!$itemId) {
        $this->sendResponse(400, [
            'success' => false,
            'error' => 'item_id requis'
        ]);
        return;
    }
    
    try {
        $sql = "DELETE FROM adjame_cart_items WHERE id = ?";
        $this->db->query($sql, [$itemId]);
        
        $this->sendResponse(200, [
            'success' => true,
            'message' => 'Item supprimé du panier'
        ]);
        
    } catch (Exception $e) {
        $this->sendResponse(500, [
            'success' => false,
            'error' => 'Erreur lors de la suppression: ' . $e->getMessage()
        ]);
    }
}

/**
 * Vider le panier
 * DELETE /api/products.php?action=clear_cart&user_id=1
 */
private function clearCart() {
    $userId = $_GET['user_id'] ?? null;
    
    if (!$userId) {
        $this->sendResponse(400, [
            'success' => false,
            'error' => 'user_id requis'
        ]);
        return;
    }
    
    try {
        $sql = "DELETE FROM adjame_cart_items WHERE user_id = ?";
        $this->db->query($sql, [$userId]);
        
        $this->sendResponse(200, [
            'success' => true,
            'message' => 'Panier vidé'
        ]);
        
    } catch (Exception $e) {
        $this->sendResponse(500, [
            'success' => false,
            'error' => 'Erreur lors du vidage du panier: ' . $e->getMessage()
        ]);
    }
}

    /**
 * Booster un produit (mettre en avant)
 * POST /api/products.php?action=boostproduct&product_id=1&boutique_id=1
 */
    /**
 * Booster un produit (mettre en avant)
 * POST /api/products.php?action=boostproduct&product_id=1&boutique_id=1
 */
private function boostProduct() {
    $input = json_decode(file_get_contents('php://input'), true);
    $productId = $_GET['product_id'] ?? $input['product_id'] ?? null;
    $boutiqueId = $_GET['boutique_id'] ?? $this->currentBoutique['id'];
    
    if (!$productId || !$boutiqueId) {
        $this->sendResponse(400, ['error' => 'ID produit et ID boutique requis']);
        return;
    }
    
    // Vérifier que le produit appartient à la boutique
    if (!$this->productBelongsToBoutique($productId, $boutiqueId)) {
        $this->sendResponse(403, ['error' => 'Produit non trouvé dans cette boutique']);
        return;
    }
    
    try {
        $this->db->beginTransaction();
        
        // Vérifier si le produit a déjà un boost actif ou en cours
        $checkSql = "SELECT id, status FROM adjame_product_boosts WHERE product_id = ? AND status IN ('active', 'pending')";
        $existing = $this->db->query($checkSql, [$productId]);
        
        if (!empty($existing)) {
            $currentStatus = $existing[0]['status'];
            $message = $currentStatus === 'pending' ? 'Ce produit a déjà une demande de boost en cours' : 'Ce produit est déjà boosté';
            
            $this->sendResponse(400, [
                'success' => false,
                'error' => $message
            ]);
            return;
        }
        
        // Calculer la date d'expiration (7 jours par défaut)
        $duration = $input['duration_days'] ?? 7;
        $expiresAt = date('Y-m-d H:i:s', strtotime("+{$duration} days"));
        
        // Créer le boost avec statut "pending"
        $insertSql = "
            INSERT INTO adjame_product_boosts (
                product_id, boutique_id, boost_type, duration_days, 
                expires_at, status, created_by, created_at
            ) VALUES (?, ?, ?, ?, ?, 'pending', ?, NOW())
        ";
        
        $this->db->query($insertSql, [
            $productId,
            $boutiqueId,
            $input['boost_type'] ?? 'basic',
            $duration,
            $expiresAt,
            $this->currentUser['id']
        ]);
        
        $boostId = $this->db->lastInsertId();
        
        // Enregistrer la transaction de paiement avec status "pending"
        $amount = $this->getBoostPrice($input['boost_type'] ?? 'basic', $duration);
        $transactionId = 'BOOST_' . time() . '_' . $productId;
        
        $paymentSql = "
            INSERT INTO adjame_boost_payments 
            (boost_id, boutique_id, amount, payment_method, status, transaction_id, created_at)
            VALUES (?, ?, ?, ?, 'pending', ?, NOW())
        ";
        
        $this->db->query($paymentSql, [
            $boostId,
            $boutiqueId,
            $amount,
            $input['payment_method'] ?? 'card',
            $transactionId
        ]);
        
        $this->db->commit();
        
        // Récupérer les infos du produit pour la réponse
        $productSql = "SELECT name FROM adjame_products WHERE id = ?";
        $productInfo = $this->db->query($productSql, [$productId]);
        $productName = $productInfo[0]['name'] ?? 'Produit';
        
        $this->sendResponse(200, [
            'success' => true,
            'status' => 'pending',
            'message' => 'Votre demande de boost a été reçue. Un agent AliAdjame vous contactera sous 24h pour finaliser l\'activation.',
            'data' => [
                'boost_id' => $boostId,
                'product_id' => (int)$productId,
                'product_name' => $productName,
                'boost_type' => $input['boost_type'] ?? 'basic',
                'duration_days' => $duration,
                'amount' => $amount,
                'transaction_id' => $transactionId,
                'status' => 'pending',
                'contact_phone' => '+225 01 53 67 60 62'
            ]
        ]);
        echo 'Merde';
        
    } catch (Exception $e) {
        $this->db->rollback();
        error_log("Erreur dans boostProduct: " . $e->getMessage());
        $this->sendResponse(500, [
            'success' => false,
            'error' => 'Erreur lors de la demande de boost: ' . $e->getMessage()
        ]);
    }
}

private function getMyOrders() {
    try {
        // Récupérer l'ID utilisateur depuis les paramètres ou l'utilisateur connecté
        $userId = $_GET['user_id'];
        
        if (!$userId) {
            $this->sendResponse(401, [
                'success' => false,
                'error' => 'Utilisateur non authentifié'
            ]);
            return;
        }
        
        // Récupérer le filtre de statut si fourni
        $statut = $_GET['statut'] ?? null;
        
        // Construire la requête SQL
       $sql = "
            SELECT 
                c.*,

                -- Image du produit
                (
                    SELECT pi.image_url
                    FROM adjame_product_images pi
                    WHERE pi.product_id = c.produit_id
                    ORDER BY pi.is_primary DESC, pi.sort_order ASC, pi.id ASC
                    LIMIT 1
                ) AS produit_image,

                -- Paiements supplémentaires liés à la commande
                (
                    SELECT 
                        JSON_ARRAYAGG(
                            JSON_OBJECT(
                                'id', p.id,
                                'montant', p.montant,
                                'preuve_paiement', p.preuve_paiement,
                                'commentaire', p.commentaire,
                                'date_paiement', p.date_paiement,
                                'valide', p.valide,
                                'valid_date', p.valid_date,
                                'commentaire_admin', p.commentaire_admin
                            )
                        )
                    FROM adjame_commandes_paiements p
                    WHERE p.commande_id = c.id
                ) AS paiements

            FROM adjame_commandes c
            WHERE c.client_telephone = (
                SELECT email FROM adjame_clients WHERE id = ?
            )
        ";

        
        $params = [$userId];
        
        // Ajouter le filtre de statut si fourni
        if ($statut && $statut !== 'tous') {
            $sql .= " AND c.statut = ?";
            $params[] = $statut;
        }
        
        $sql .= " ORDER BY c.created_at DESC";
        
        error_log("🔍 SQL getMyOrders: " . $sql);
        error_log("🔍 Params: " . json_encode($params));
        
        $commandes = $this->db->query($sql, $params);
        
        // Formater les données
        foreach ($commandes as &$commande) {
            $commande['id'] = (int)$commande['id'];
            $commande['produit_id'] = (int)$commande['produit_id'];
            $commande['produit_prix'] = (float)$commande['produit_prix'];
            $commande['quantite'] = (int)$commande['quantite'];
            $commande['sous_total'] = (float)$commande['sous_total'];
            $commande['frais_livraison'] = (float)$commande['frais_livraison'];
            $commande['total'] = (float)$commande['total'];
            $commande['boutique_id'] = $commande['boutique_id'] ? (int)$commande['boutique_id'] : null;
            
            // Décoder les options variantes JSON si présentes
            if ($commande['options_variantes']) {
                $commande['options_variantes'] = json_decode($commande['options_variantes'], true);
            }
            if ($commande['paiements']) {
                $commande['paiements'] = json_decode($commande['paiements'], true);
            } else {
                $commande['paiements'] = [];
            }
            
        }
        
        $this->sendResponse(200, [
            'success' => true,
            'data' => $commandes,
            'autre' =>  $userId,
            'total' => count($commandes)
        ]);
        
    } catch (Exception $e) {
        error_log("❌ Erreur dans getMyOrders: " . $e->getMessage());
        
        $this->sendResponse(500, [
            'success' => false,
            'error' => 'Erreur lors de la récupération des commandes: ' . $e->getMessage()
        ]);
    }
}


private function uploadPaymentProof()
{
    // 🔹 Récupérer les paramètres depuis l’URL (GET)
    $commandeId = $_GET['commande_id'] ?? null;
    $action     = $_GET['action'] ?? null;

    // 🔹 Récupérer les données envoyées en form-data (POST)
    $preuveUrl  = $_POST['preuve_paiement'] ?? null;
    $commentaire = $_POST['comment'] ?? null;

    if (!$commandeId || !$preuveUrl) {
        $this->sendResponse(400, [
            'success' => false,
            'error' => "L’ID de la commande et l’URL de la preuve sont requis."
        ]);
        return;
    }

    try {
        // Vérifier que la commande existe et est active
        $check = $this->db->query("SELECT id FROM adjame_commandes WHERE id = ?", [$commandeId]);

        // ✅ Si ta fonction DB renvoie un tableau
        if (!$check || count($check) === 0) {
            $this->sendResponse(404, [
                'success' => false,
                'error' => "Commande introuvable."
            ]);
            return;
        }

        // ✅ Mettre à jour la commande
        $sql = "
            UPDATE adjame_commandes
            SET 
                preuve_paiement = ?,
                commentaire = ?,
                date_paiement = NOW(),
                updated_at = NOW()
            WHERE id = ?
        ";

        $this->db->query($sql, [
            $preuveUrl,
            $commentaire,
            (int)$commandeId
        ]);

        // ✅ Journaliser l’activité
        $this->logActivity(
            $this->currentUser['id'] ?? null,
            null,
            'Téléchargement preuve de paiement',
            "Preuve de paiement ajoutée pour la commande #$commandeId",
            $commandeId
        );

        // ✅ Réponse de succès
        $this->sendResponse(200, [
            'success' => true,
            'message' => "Preuve de paiement ajoutée avec succès.",
            'data' => [
                'commande_id' => (int)$commandeId,
                'preuve_url' => $preuveUrl,
                'commentaire' => $commentaire,
                'statut' => 'confirmee'
            ]
        ]);

    } catch (Exception $e) {
        error_log("❌ Erreur uploadPaymentProof: " . $e->getMessage());
        $this->sendResponse(500, [
            'success' => false,
            'error' => "Erreur lors de l’enregistrement de la preuve: " . $e->getMessage()
        ]);
    }
}

private function addNewPayment()
{
    
    // 🔹 Récupérer commande_id depuis POST ou GET
   $commandeId = $_GET['commande_id'] ?? $_POST['commande_id'] ?? null;

$data = $_GET['data'] ?? $_POST['data'] ?? [];

$montantC     = $data['montant'] ?? null;
$preuveC      = isset($data['preuve_paiement']) ? urldecode($data['preuve_paiement']) : null;
$commentaireC = $data['commentaire'] ?? null;

    // Validation
    if (!$commandeId || !$preuveC) {
        $this->sendResponse(400, [
            'success' => false,
            'error' => "L’ID de la commande et l’URL de la preuve sont requis."
        ]);
        return;
    }

    try {
        // Vérifier que la commande existe
        $check = $this->db->query("SELECT id FROM adjame_commandes WHERE id = ?", [$commandeId]);

        if (!$check || count($check) === 0) {
            $this->sendResponse(404, [
                'success' => false,
                'error' => "Commande introuvable."
            ]);
            return;
        }

        // Enregistrer le paiement dans la table dédiée
        $sql = "
            INSERT INTO adjame_commandes_paiements (
                commande_id,
                montant,
                preuve_paiement,
                commentaire,
                date_paiement,
                created_at
            ) VALUES (?, ?, ?, ?, NOW(), NOW())
        ";

        $this->db->query($sql, [
            (int)$commandeId,
            $montantC,
            $preuveC,
            $commentaireC
        ]);

        // Journalisation
        $this->logActivity(
            $this->currentUser['id'] ?? null,
            null,
            'Téléchargement preuve de paiement',
            "Preuve de paiement ajoutée pour la commande #$commandeId",
            $commandeId
        );

        // Réponse
        $this->sendResponse(200, [
            'success' => true,
            'message' => "Preuve de paiement ajoutée avec succès.",
            'data' => [
                'commande_id' => (int)$commandeId,
                'montant' => $montantC,
                'preuve_url' => $preuveC,
                'commentaire' => $commentaireC,
                'statut' => 'confirmee'
            ]
        ]);

    } catch (Exception $e) {
        error_log("❌ Erreur addNewPayment: " . $e->getMessage());
        $this->sendResponse(500, [
            'success' => false,
            'error' => "Erreur lors de l’enregistrement: " . $e->getMessage()
        ]);
    }
}

private function logActivity($userId, $boutiqueId, $action, $details, $commandeId = null)
{
    try {
        $sql = "
            INSERT INTO adjame_logs (user_id, boutique_id, commande_id, action, details, ip_address, user_agent, created_at)
            VALUES (?, ?, ?, ?, ?, ?, ?, NOW())
        ";

        $this->db->query($sql, [
            $userId,
            $boutiqueId,
            $commandeId,
            $action,
            $details,
            $_SERVER['REMOTE_ADDR'] ?? null,
            $_SERVER['HTTP_USER_AGENT'] ?? null
        ]);

        error_log("📝 Log activity: $action - $details (commande: $commandeId)");

    } catch (Exception $e) {
        error_log("⚠️ Erreur logActivity: " . $e->getMessage());
    }
}



private function cancelOrder() {
    $input = json_decode(file_get_contents('php://input'), true);
    
    if (!isset($input['commande_id'])) {
        $this->sendResponse(400, [
            'success' => false,
            'error' => 'ID de commande requis'
        ]);
        return;
    }
    
    try {
        $sql = "
            UPDATE adjame_commandes 
            SET statut = 'annule',
                updated_at = NOW()
            WHERE id = ? AND statut = 'en_attente'
        ";
        
        $result = $this->db->query($sql, [(int)$input['commande_id']]);
        
        $this->sendResponse(200, [
            'success' => true,
            'message' => 'Commande annulée avec succès'
        ]);
        
    } catch (Exception $e) {
        $this->sendResponse(500, [
            'success' => false,
            'error' => 'Erreur lors de l\'annulation: ' . $e->getMessage()
        ]);
    }
}

/**
 * Activer un boost (pour l'admin après paiement)
 * POST /api/products.php?action=activate_boost&boost_id=1
 */
private function activateBoost() {
    $boostId = $_GET['boost_id'] ?? null;
    
    if (!$boostId) {
        $this->sendResponse(400, ['error' => 'ID du boost requis']);
        return;
    }
    
    try {
        $this->db->beginTransaction();
        
        // Récupérer les infos du boost
        $boostSql = "SELECT * FROM adjame_product_boosts WHERE id = ? AND status = 'pending'";
        $boosts = $this->db->query($boostSql, [$boostId]);
        
        if (empty($boosts)) {
            $this->sendResponse(404, ['error' => 'Boost non trouvé ou déjà activé']);
            return;
        }
        
        $boost = $boosts[0];
        
        // Activer le boost
        $updateBoostSql = "UPDATE adjame_product_boosts SET status = 'active', updated_at = NOW() WHERE id = ?";
        $thisthis->db->query($updateBoostSql, [$boostId]);
        
        // Mettre à jour le produit
        $updateProductSql = "UPDATE adjame_products SET is_boosted = 1, boost_expires_at = ? WHERE id = ?";
        $this->db->query($updateProductSql, [$boost['expires_at'], $boost['product_id']]);
        
        // Marquer le paiement comme complété
        $updatePaymentSql = "UPDATE adjame_boost_payments SET status = 'completed', updated_at = NOW() WHERE boost_id = ?";
        $this->db->query($updatePaymentSql, [$boostId]);
        
        $this->db->commit();
        
        $this->sendResponse(200, [
            'success' => true,
            'message' => 'Boost activé avec succès'
        ]);
        
    } catch (Exception $e) {
        $this->db->rollback();
        error_log("Erreur dans activateBoost: " . $e->getMessage());
        $this->sendResponse(500, [
            'success' => false,
            'error' => 'Erreur lors de l\'activation: ' . $e->getMessage()
        ]);
    }
}

/**
 * Calculer le prix du boost selon le type et la durée
 */
private function getBoostPrice($boostType, $duration) {
    $basePrices = [
        'basic' => 1000,    // 1000 FCFA
        'premium' => 2500,  // 2500 FCFA  
        'super' => 5000     // 5000 FCFA
    ];
    
    $basePrice = $basePrices[$boostType] ?? 1000;
    
    // Ajuster selon la durée (prix de base pour 7 jours)
    $pricePerDay = $basePrice / 7;
    return round($pricePerDay * $duration);
}

    /**
     * Récupérer les boosts d'une boutique
     * GET /api/products.php?action=product_boosts&boutique_id=1
     */
    private function getProductBoosts() {
        $boutiqueId = $_GET['boutique_id'] ?? $this->currentBoutique['id'];
        
        if (!$boutiqueId) {
            $this->sendResponse(400, ['error' => 'ID de boutique requis']);
            return;
        }
        
        try {
            $sql = "
                SELECT 
                    pb.id,
                    pb.product_id,
                    pb.boost_type,
                    pb.duration_days,
                    pb.expires_at,
                    pb.status,
                    pb.created_at,
                    p.name as product_name,
                    p.unit_price
                FROM adjame_product_boosts pb
                JOIN adjame_products p ON pb.product_id = p.id
                WHERE pb.boutique_id = ?
                ORDER BY pb.created_at DESC
            ";
            
            $boosts = $this->db->query($sql, [$boutiqueId]);
            
            $this->sendResponse(200, [
                'success' => true,
                'data' => $boosts
            ]);
            
        } catch (Exception $e) {
            error_log("Erreur dans getProductBoosts: " . $e->getMessage());
            $this->sendResponse(500, [
                'success' => false,
                'error' => 'Erreur lors de la récupération des boosts: ' . $e->getMessage()
            ]);
        }
    }
    
    /**
     * Récupérer les produits d'une boutique
     * GET /api/products.php?action=boutique_products&id=1&page=1&limit=20&search=keyword&sort=created_at&order=DESC
     */
    private function getBoutiqueProducts() {
        $boutiqueId = $_GET['id'] ?? $_GET['boutique_id'] ?? null;
        $page = (int)($_GET['page'] ?? 1);
        $limit = (int)($_GET['limit'] ?? 20);
        $search = $_GET['search'] ?? '';
        $sort = $_GET['sort'] ?? 'created_at';
        $order = $_GET['order'] ?? 'DESC';
        
        if (!$boutiqueId) {
            $this->sendResponse(400, ['error' => 'ID de boutique requis']);
            return;
        }
        
        $offset = ($page - 1) * $limit;
        
        try {
            // Conditions WHERE
            $whereConditions = ['p.boutique_id = ?', 'p.is_active = 1', 'p.status = ?'];
            $params = [$boutiqueId, 'Actif'];
            
            // Filtre de recherche
            if ($search) {
                $whereConditions[] = '(p.name LIKE ? OR p.description LIKE ? OR p.sku LIKE ?)';
                $searchTerm = "%$search%";
                $params[] = $searchTerm;
                $params[] = $searchTerm;
                $params[] = $searchTerm;
            }
            
            $whereClause = implode(' AND ', $whereConditions);
            
            // Requête principale
            $sql = "
                SELECT 
                    p.id,
                    p.name,
                    p.description,
                    p.sku,
                    p.unit_price,
                    p.wholesale_price,
                    p.wholesale_min_qty,
                    p.stock,
                    p.status,
                    p.slug,
                    p.created_at,
                    p.updated_at,
                    c.name as category_name,
                    sc.name as subcategory_name,
                    (SELECT image_url FROM adjame_product_images WHERE product_id = p.id AND is_primary = 1 ORDER BY sort_order ASC LIMIT 1) AS primary_image,
                    (SELECT GROUP_CONCAT(image_url ORDER BY sort_order SEPARATOR '||') FROM adjame_product_images WHERE product_id = p.id LIMIT 5) AS images,
                    (SELECT COUNT(*) FROM adjame_product_images WHERE product_id = p.id) AS image_count
                FROM adjame_products p
                LEFT JOIN adjame_categories c ON p.category_id = c.id
                LEFT JOIN adjame_subcategories sc ON p.subcategory_id = sc.id
                WHERE $whereClause
                ORDER BY p.$sort $order
                LIMIT ? OFFSET ?
            ";
            
            $params[] = $limit;
            $params[] = $offset;
            
            $products = $this->db->query($sql, $params);
            
            if (!is_array($products)) {
                $products = [];
            }
            
            // Compter le total pour la pagination
            $countSql = "SELECT COUNT(DISTINCT p.id) as total FROM adjame_products p WHERE $whereClause";
            $countParams = array_slice($params, 0, -2); // Enlever limit et offset
            $totalResult = $this->db->query($countSql, $countParams);
            
            $total = 0;
            if (is_array($totalResult) && isset($totalResult[0]['total'])) {
                $total = $totalResult[0]['total'];
            }
            
            // Formater les données
            foreach ($products as &$product) {
                $product['id'] = (int)$product['id'];
                $product['unit_price'] = (float)$product['unit_price'];
                $product['wholesale_price'] = $product['wholesale_price'] ? (float)$product['wholesale_price'] : null;
                $product['wholesale_min_qty'] = $product['wholesale_min_qty'] ? (int)$product['wholesale_min_qty'] : 1;
                $product['stock'] = (int)$product['stock'];
                $product['image_count'] = (int)$product['image_count'];
                
                // Traitement des images
                $product['images'] = !empty($product['images']) ? explode('||', $product['images']) : [];
                if (empty($product['images']) && $product['primary_image']) {
                    $product['images'] = [$product['primary_image']];
                }
                if (empty($product['images'])) {
                    $product['images'] = ['https://via.placeholder.com/280x280?text=Produit'];
                }
                $product['primary_image'] = $product['images'][0];
                
                // Valeurs par défaut
                $product['category_name'] = $product['category_name'] ?? 'Non catégorisé';
                $product['subcategory_name'] = $product['subcategory_name'] ?? null;
                
                // Données simulées pour l'affichage
                $product['rating'] = round(4.0 + (rand(0, 10) / 10), 1);
                $product['reviews'] = rand(10, 500);
                $product['free_shipping'] = rand(0, 1) == 1;
                $product['isFavorite'] = false; // Par défaut
                $product['tradeAssurance'] = true;
                $product['minQuantity'] = $product['wholesale_min_qty'] ?: 1;
                $product['unitPrice'] = $product['unit_price'];
                $product['wholesalePrice'] = $product['wholesale_price'];
                
                // Index d'image actuelle pour le slider
                $product['currentImageIndex'] = 0;
            }
            
            $this->sendResponse(200, [
                'success' => true,
                'data' => [
                    'products' => $products,
                    'total' => (int)$total
                ],
                'pagination' => [
                    'current_page' => $page,
                    'per_page' => $limit,
                    'total' => (int)$total,
                    'total_pages' => $total > 0 ? ceil($total / $limit) : 0
                ],
                'filters' => [
                    'boutique_id' => (int)$boutiqueId,
                    'search' => $search,
                    'sort' => $sort,
                    'order' => $order
                ]
            ]);
            
        } catch (Exception $e) {
            error_log("Erreur dans getBoutiqueProducts: " . $e->getMessage());
            $this->sendResponse(500, [
                'success' => false,
                'error' => 'Erreur lors de la récupération des produits de la boutique: ' . $e->getMessage()
            ]);
        }
    }

    /**
 * Récupérer les tarifs de livraison pour Abidjan
 * GET /api/products.php?action=tarifs_abidjan
 */
private function getTarifsAbidjan() {
    try {
        $sql = "
            SELECT 
                id,
                commune,
                tarif_min,
                tarif_max,
                delai_livraison,
                actif,
                created_at,
                updated_at
            FROM adjame_tarifs_abidjan 
            WHERE actif = 1 
            ORDER BY commune ASC
        ";
        
        $tarifs = $this->db->query($sql);
        
        if (!is_array($tarifs)) {
            $tarifs = [];
        }
        
        // Formater les données
        foreach ($tarifs as &$tarif) {
            $tarif['id'] = (int)$tarif['id'];
            $tarif['tarif_min'] = (float)$tarif['tarif_min'];
            $tarif['tarif_max'] = (float)$tarif['tarif_max'];
            $tarif['actif'] = (bool)$tarif['actif'];
        }
        
        $this->sendResponse(200, [
            'success' => true,
            'data' => $tarifs,
            'count' => count($tarifs)
        ]);
        
    } catch (Exception $e) {
        error_log("Erreur dans getTarifsAbidjan: " . $e->getMessage());
        $this->sendResponse(500, [
            'success' => false,
            'error' => 'Erreur lors de la récupération des tarifs Abidjan: ' . $e->getMessage()
        ]);
    }
}

/**
 * Récupérer les tarifs de livraison pour l'intérieur
 * GET /api/products.php?action=tarifs_interieur
 */
private function getTarifsInterieur() {
    try {
        $sql = "
            SELECT 
                id,
                ville,
                tarif,
                delai_livraison,
                actif,
                created_at,
                updated_at
            FROM adjame_tarifs_interieur 
            WHERE actif = 1 
            ORDER BY ville ASC
        ";
        
        $tarifs = $this->db->query($sql);
        
        if (!is_array($tarifs)) {
            $tarifs = [];
        }
        
        // Formater les données
        foreach ($tarifs as &$tarif) {
            $tarif['id'] = (int)$tarif['id'];
            $tarif['tarif'] = (float)$tarif['tarif'];
            $tarif['actif'] = (bool)$tarif['actif'];
        }
        
        $this->sendResponse(200, [
            'success' => true,
            'data' => $tarifs,
            'count' => count($tarifs)
        ]);
        
    } catch (Exception $e) {
        error_log("Erreur dans getTarifsInterieur: " . $e->getMessage());
        $this->sendResponse(500, [
            'success' => false,
            'error' => 'Erreur lors de la récupération des tarifs intérieur: ' . $e->getMessage()
        ]);
    }
}

/**
 * Créer une nouvelle commande
 * POST /api/products.php?action=create_order
 */
/**
 * Créer une nouvelle commande
 * POST /api/products.php?action=create_order
 */
private function createOrder() {
    $input = json_decode(file_get_contents('php://input'), true);
    
    // Log pour débugger
    error_log("📦 Données reçues pour createOrder: " . json_encode($input));
    
    // Validation des données requises
    $required = [
        'numero_commande', 'produit_id', 'produit_nom', 'produit_prix', 
        'quantite', 'client_telephone', 'type_livraison', 'sous_total', 
        'frais_livraison', 'total'
    ];
    
    foreach ($required as $field) {
        if (!isset($input[$field]) || (is_string($input[$field]) && empty($input[$field]))) {
            $this->sendResponse(400, [
                'success' => false,
                'error' => "Le champ $field est requis"
            ]);
            return;
        }
    }
    
    try {
        // Vérifier d'abord si le numéro de commande existe déjà
        $checkSql = "SELECT id FROM adjame_commandes WHERE numero_commande = ?";
        $existing = $this->db->query($checkSql, [$input['numero_commande']]);
        
        if (!empty($existing)) {
            $this->sendResponse(400, [
                'success' => false,
                'error' => 'Ce numéro de commande existe déjà'
            ]);
            return;
        }
        
        // ✅ NOUVELLE STRUCTURE - Traitement des variantes
        $optionsVariantes = null;
        if (isset($input['options_variantes']) && is_array($input['options_variantes'])) {
            // Nettoyer et structurer les données des variantes
            $variantes = [];
            foreach ($input['options_variantes'] as $variante) {
                $varianteData = [
                    'quantite' => (int)$variante['quantite'],
                    'taille' => $variante['taille'] ?? null,
                    'couleur' => [
                        'nom' => $variante['couleur']['nom'] ?? null,
                        'hex' => $variante['couleur']['hex'] ?? null
                    ]
                ];
                $variantes[] = $varianteData;
            }
            $optionsVariantes = json_encode($variantes);
            error_log("🎨 Options variantes traitées: " . $optionsVariantes);
        }
        
        // ✅ SQL MISE À JOUR avec la nouvelle colonne
        $sql = "
            INSERT INTO adjame_commandes SET
                numero_commande = ?,
                produit_id = ?,
                produit_nom = ?,
                produit_prix = ?,
                quantite = ?,
                client_nom = ?,
                client_telephone = ?,
                type_livraison = ?,
                commune = ?,
                ville = ?,
                adresse_complete = ?,
                instructions_livraison = ?,
                sous_total = ?,
                frais_livraison = ?,
                total = ?,
                options_variantes = ?,
                boutique_id = ?,
                boutique_nom = ?,
                statut = ?,
                created_at = NOW()
        ";
        
        $params = [
            $input['numero_commande'],
            (int)$input['produit_id'],
            $input['produit_nom'],
            (float)$input['produit_prix'],
            (int)$input['quantite'],
            $input['client_nom'] ?? 'Client',
            $input['client_telephone'],
            $input['type_livraison'],
            $input['commune'] ?? null,
            $input['ville'] ?? 'Abidjan',
            $input['adresse_complete'] ?? '',
            $input['instructions_livraison'] ?? '',
            (float)$input['sous_total'],
            (float)$input['frais_livraison'],
            (float)$input['total'],
            $optionsVariantes, // ✅ Nouvelle colonne
            isset($input['boutique_id']) ? (int)$input['boutique_id'] : null,
            $input['boutique_nom'] ?? '',
            $input['statut'] ?? 'en_attente'
        ];
        
        error_log("🔍 SQL: " . $sql);
        error_log("🔍 Params: " . json_encode($params));
        
        $result = $this->db->query($sql, $params);
        
        if ($result === false) {
            throw new Exception('La requête a retourné false');
        }
        
        // Récupérer l'ID de la commande créée
        $commandeId = $this->db->lastInsertId();
        
        if (!$commandeId) {
            // Essayer de récupérer la commande par son numéro
            $findSql = "SELECT id FROM adjame_commandes WHERE numero_commande = ? ORDER BY id DESC LIMIT 1";
            $found = $this->db->query($findSql, [$input['numero_commande']]);
            
            if (!empty($found)) {
                $commandeId = $found[0]['id'];
            } else {
                throw new Exception('Impossible de récupérer l\'ID de la commande créée');
            }
        }
        
        error_log("✅ Commande créée avec ID: " . $commandeId);
        
        // Récupérer la commande créée
        $createdOrder = $this->getOrderById($commandeId);
        $createdProductid = $this->getProductSelect($createdOrder['produit_id']);

        $this->sendEmailOrderEmail([
                'email' => $input['client_telephone'],
                'full_name' => $input['client_nom'],
                'order' => $createdOrder,
                'product' => $createdProductid
        ]);
        
        $this->sendResponse(201, [
            'success' => true,
            'message' => 'Commande créée avec succès',
            'data' => $createdOrder,
            'product' => $createdProductid
        ]);
        
    } catch (Exception $e) {
        error_log("❌ Erreur dans createOrder: " . $e->getMessage());
        error_log("❌ Stack trace: " . $e->getTraceAsString());
        
        $this->sendResponse(500, [
            'success' => false,
            'error' => 'Erreur lors de la création de la commande: ' . $e->getMessage()
        ]);
    }
}

/**
 * Récupérer les marques de trailers
 * GET /api/products.php?action=trailer_brands
 */
private function getTrailerBrands() {
    try {
        $sql = "
            SELECT 
                id, 
                name, 
                country, 
                logo_url 
            FROM adjame_trailer_marque 
            WHERE is_active = 1 
            ORDER BY name ASC
        ";
        $brands = $this->db->query($sql);

        if (!$brands || !is_array($brands)) {
            $brands = [];
        }

        $this->sendResponse(200, [
            'success' => true,
            'count'   => count($brands),
            'data'    => $brands
        ]);

    } catch (Exception $e) {
        error_log("❌ Erreur dans getTrailerBrands: " . $e->getMessage());
        $this->sendResponse(500, [
            'success' => false,
            'error'   => 'Erreur lors de la récupération des marques: ' . $e->getMessage()
        ]);
    }
}

/**
 * Récupérer les types de trailers
 * GET /api/products.php?action=trailer_types
 */
private function getTrailerTypes() {
    try {
        $sql = "
            SELECT 
                id, 
                name, 
                description 
            FROM adjame_trailer_types 
            WHERE is_active = 1 
            ORDER BY name ASC
        ";
        $types = $this->db->query($sql);

        if (!$types || !is_array($types)) {
            $types = [];
        }

        $this->sendResponse(200, [
            'success' => true,
            'count' => count($types),
            'data'  => $types
        ]);

    } catch (Exception $e) {
        error_log("❌ Erreur dans getTrailerTypes: " . $e->getMessage());
        $this->sendResponse(500, [
            'success' => false,
            'error'   => 'Erreur lors de la récupération des types: ' . $e->getMessage()
        ]);
    }
}

private function sendEmailOrderEmail($userData) {
        try {
            $emailData = [
                'email' => $userData['email'],
                'full_name' => $userData['full_name'],
                'order' => $userData['order'],
                'product' => $userData['product'],
            ];
            
            return $this->emailService->sendEmailOrderEmail($emailData);
            
        } catch (Exception $e) {
            error_log("Erreur lors de l'envoi de l'email de vérification: " . $e->getMessage());
            return false;
        }
    }

/**
 * Récupérer les données des couleurs sélectionnées (pour le frontend)
 */
private function getSelectedColorsData() {
    // Cette fonction devrait être appelée depuis le frontend
    // Retourner un tableau vide pour l'instant
    return [];
}

/**
 * Récupérer les données des tailles sélectionnées (pour le frontend)
 */
private function getSelectedSizesData() {
    // Cette fonction devrait être appelée depuis le frontend
    // Retourner un tableau vide pour l'instant
    return [];
}

/**
 * Afficher une notification (pour le frontend)
 */
private function displayNotification($type, $title, $message) {
    // Cette fonction sera gérée côté frontend
    error_log("📢 Notification $type: $title - $message");
}

/**
 * Récupérer une commande par son ID
 */
private function getOrderById($commandeId) {
    try {
        $sql = "
            SELECT 
                id,
                numero_commande,
                produit_id,
                produit_nom,
                produit_prix,
                quantite,
                client_nom,
                client_telephone,
                type_livraison,
                commune,
                ville,
                adresse_complete,
                instructions_livraison,
                sous_total,
                frais_livraison,
                total,
                options_couleurs,
                options_tailles,
                boutique_id,
                boutique_nom,
                statut,
                created_at,
                updated_at
            FROM adjame_commandes 
            WHERE id = ?
        ";
        
        $commandes = $this->db->query($sql, [$commandeId]);
        
        if (!empty($commandes)) {
            $commande = $commandes[0];
            
            // Formater les données
            $commande['id'] = (int)$commande['id'];
            $commande['produit_id'] = (int)$commande['produit_id'];
            $commande['produit_prix'] = (float)$commande['produit_prix'];
            $commande['quantite'] = (int)$commande['quantite'];
            $commande['sous_total'] = (float)$commande['sous_total'];
            $commande['frais_livraison'] = (float)$commande['frais_livraison'];
            $commande['total'] = (float)$commande['total'];
            $commande['boutique_id'] = $commande['boutique_id'] ? (int)$commande['boutique_id'] : null;
            
            // Décoder les options JSON si présentes
            if ($commande['options_couleurs']) {
                $commande['options_couleurs'] = json_decode($commande['options_couleurs'], true);
            }
            if ($commande['options_tailles']) {
                $commande['options_tailles'] = json_decode($commande['options_tailles'], true);
            }
            
            return $commande;
        }
        
        return null;
        
    } catch (Exception $e) {
        error_log("Erreur dans getOrderById: " . $e->getMessage());
        return null;
    }
}
    
    /**
     * Suivre une boutique
     * POST /api/products.php?action=follow_boutique&boutique_id=1
     */
    private function followBoutique() {
        $boutiqueId = $_GET['boutique_id'] ?? null;
        $userId = $this->currentUser['id'];
        
        if (!$boutiqueId) {
            $this->sendResponse(400, ['error' => 'ID de boutique requis']);
            return;
        }
        
        try {
            // Vérifier si déjà suivi
            $checkSql = "SELECT id FROM adjame_boutique_followers WHERE user_id = ? AND boutique_id = ?";
            $existing = $this->db->query($checkSql, [$userId, $boutiqueId]);
            
            if (!empty($existing)) {
                $this->sendResponse(400, ['error' => 'Vous suivez déjà cette boutique']);
                return;
            }
            
            // Ajouter le suivi
            $insertSql = "INSERT INTO adjame_boutique_followers (user_id, boutique_id, created_at) VALUES (?, ?, NOW())";
            $result = $this->db->query($insertSql, [$userId, $boutiqueId]);
            
            if ($result) {
                $this->sendResponse(200, [
                    'success' => true,
                    'message' => 'Boutique suivie avec succès'
                ]);
            } else {
                $this->sendResponse(500, ['error' => 'Erreur lors du suivi de la boutique']);
            }
            
        } catch (Exception $e) {
            error_log("Erreur dans followBoutique: " . $e->getMessage());
            $this->sendResponse(500, [
                'success' => false,
                'error' => 'Erreur lors du suivi de la boutique: ' . $e->getMessage()
            ]);
        }
    }
    
    /**
     * Ne plus suivre une boutique
     * POST /api/products.php?action=unfollow_boutique&boutique_id=1
     */
    private function unfollowBoutique() {
        $boutiqueId = $_GET['boutique_id'] ?? null;
        $userId = $this->currentUser['id'];
        
        if (!$boutiqueId) {
            $this->sendResponse(400, ['error' => 'ID de boutique requis']);
            return;
        }
        
        try {
            $deleteSql = "DELETE FROM adjame_boutique_followers WHERE user_id = ? AND boutique_id = ?";
            $result = $this->db->query($deleteSql, [$userId, $boutiqueId]);
            
            $this->sendResponse(200, [
                'success' => true,
                'message' => 'Vous ne suivez plus cette boutique'
            ]);
            
        } catch (Exception $e) {
            error_log("Erreur dans unfollowBoutique: " . $e->getMessage());
            $this->sendResponse(500, [
                'success' => false,
                'error' => 'Erreur lors du désabonnement: ' . $e->getMessage()
            ]);
        }
    }
    
    /**
     * Contacter une boutique
     * POST /api/products.php?action=contact_boutique&boutique_id=1
     */
    private function contactBoutique() {
        $boutiqueId = $_GET['boutique_id'] ?? null;
        $input = json_decode(file_get_contents('php://input'), true);
        
        if (!$boutiqueId) {
            $this->sendResponse(400, ['error' => 'ID de boutique requis']);
            return;
        }
        
        if (empty($input['subject']) || empty($input['message'])) {
            $this->sendResponse(400, ['error' => 'Sujet et message requis']);
            return;
        }
        
        try {
            // Enregistrer le message en base
            $insertSql = "
                INSERT INTO adjame_boutique_messages (
                    boutique_id, sender_id, subject, message, created_at
                ) VALUES (?, ?, ?, ?, NOW())
            ";
            
            $result = $this->db->query($insertSql, [
                $boutiqueId,
                $this->currentUser['id'],
                $input['subject'],
                $input['message']
            ]);
            
            if ($result) {
                $this->sendResponse(200, [
                    'success' => true,
                    'message' => 'Message envoyé avec succès'
                ]);
            } else {
                $this->sendResponse(500, ['error' => 'Erreur lors de l\'envoi du message']);
            }
            
        } catch (Exception $e) {
            error_log("Erreur dans contactBoutique: " . $e->getMessage());
            $this->sendResponse(500, [
                'success' => false,
                'error' => 'Erreur lors de l\'envoi du message: ' . $e->getMessage()
            ]);
        }
    }

    private function addLike() {
        $input = json_decode(file_get_contents('php://input'), true);

        // Vérification des paramètres
        if (!isset($input['id_produit'])) {
            $this->sendResponse(400, ['error' => 'ID du produit requis']);
            return;
        }

        $productId = $input['id_produit'];
        $userId = $input['user_id'];

        if (!$userId) {
            $this->sendResponse(401, ['error' => 'Utilisateur non connecté']);
            return;
        }

        try {
            // Vérifier si ce like existe déjà
            $checkSql = "SELECT id FROM adjame_product_likes WHERE id_produit = ? AND user_id = ?";
            $existing = $this->db->query($checkSql, [$productId, $userId]);

            if (!empty($existing)) {
                // Déjà liké -> On retire le like
                $deleteSql = "DELETE FROM adjame_product_likes WHERE id_produit = ? AND user_id = ?";
                $this->db->query($deleteSql, [$productId, $userId]);

                $this->sendResponse(200, [
                    'success' => true,
                    'liked' => false,
                    'message' => 'Like retiré'
                ]);
                return;
            }

            // Ajout du like
            $sql = "INSERT INTO adjame_product_likes (id_produit, user_id, liked_at) VALUES (?, ?, NOW())";
            $this->db->query($sql, [$productId, $userId]);

            $this->sendResponse(201, [
                'success' => true,
                'liked' => true,
                'message' => 'Produit liké avec succès'
            ]);

        } catch (Exception $e) {
            error_log("Erreur like produit : " . $e->getMessage());
            $this->sendResponse(500, ['error' => 'Erreur lors du like : ' . $e->getMessage()]);
        }
    }

    private function getUserFavorites() {
        $input = json_decode(file_get_contents('php://input'), true);
        
        if (!isset($input['user'])) {
            $this->sendResponse(400, ['error' => 'ID utilisateur requis']);
            return;
        }

        $userId = $input['user'];

        try {
            $sql = "
                SELECT 
                    apl.id AS favorite_id,
                    p.id AS product_id,
                    p.name,
                    p.slug,
                    p.unit_price,
                    pi.image_url AS primary_image
                FROM adjame_product_likes AS apl
                INNER JOIN adjame_products AS p ON apl.id_produit = p.id
                LEFT JOIN adjame_product_images AS pi 
                    ON pi.product_id = p.id AND pi.is_primary = 1
                WHERE apl.user_id = ?
                ORDER BY apl.liked_at DESC
            ";

            $favorites = $this->db->query($sql, [$userId]);

            $this->sendResponse(200, [
                'success' => true,
                'data' => $favorites
            ]);
        } catch (Exception $e) {
            $this->sendResponse(500, ['error' => 'Erreur lors de la récupération des favoris: ' . $e->getMessage()]);
        }
    }




    
    // ========== FIN NOUVELLES FONCTIONS BOUTIQUES ==========
    
    // GET /api/products.php?action=list&boutique_id=1
   private function getProducts() {
    $page = (int)($_GET['page'] ?? 1);
    $limit = (int)($_GET['limit'] ?? 10);
    $status = $_GET['status'] ?? '';
    $category = $_GET['category'] ?? '';
    $search = $_GET['search'] ?? '';
    $sort = $_GET['sort'] ?? 'created_at';
    $order = $_GET['order'] ?? 'DESC';

    $boutiqueId = $_GET['boutique_id'] ?? $this->currentBoutique['id'];

    if (!$boutiqueId) {
        $this->sendResponse(400, ['error' => 'ID de boutique requis']);
        return;
    }

    $offset = ($page - 1) * $limit;

    $whereConditions = ['p.is_active = 1', 'p.boutique_id = ?'];
    $params = [$boutiqueId];

    if ($status) {
        $whereConditions[] = 'p.status = ?';
        $params[] = $status;
    }

    if ($search) {
        $whereConditions[] = '(p.name LIKE ? OR p.description LIKE ? OR p.sku LIKE ?)';
        $searchTerm = "%$search%";
        $params[] = $searchTerm;
        $params[] = $searchTerm;
        $params[] = $searchTerm;
    }

    $whereClause = implode(' AND ', $whereConditions);

    $sql = "
    SELECT 
        p.*,

        -- Catégories et boutique
        c.name AS category_name,
        su.name AS subcategory_name,
        ssu.name AS sub_subcategory_name,
        sssu.name AS sub_sub_subcategory_name,
        b.name AS boutique_name,
        u.full_name AS created_by_name,
        u.email AS created_by_email,
        uu.full_name AS updated_by_name,

        -- Boost et plan produit
        pb.boost_type AS boost_type,
        pb.status AS boost_status,
        pb.expires_at AS boost_expires_at,

        -- Statistiques
        COALESCE(p.sales_count, 0) AS sales_count,
        COALESCE(p.views_count, 0) AS views_count,

        -- Couleurs, tailles, VINs, trims
        GROUP_CONCAT(DISTINCT col.hex_value ORDER BY col.id SEPARATOR ',') AS colors,
        GROUP_CONCAT(DISTINCT col.name ORDER BY col.id SEPARATOR ',') AS color_names,
        GROUP_CONCAT(DISTINCT sz.name ORDER BY sz.sort_order SEPARATOR ',') AS sizes,
        GROUP_CONCAT(DISTINCT en.engine_number ORDER BY en.id SEPARATOR ',') AS trim_numbers,
        GROUP_CONCAT(DISTINCT vn.engine_number ORDER BY vn.id SEPARATOR ',') AS vin_numbers,

        -- Images
        (SELECT image_url 
         FROM adjame_product_images 
         WHERE product_id = p.id AND is_primary = 1 
         ORDER BY sort_order ASC LIMIT 1
        ) AS primary_image,

        (SELECT GROUP_CONCAT(image_url ORDER BY sort_order SEPARATOR '||')
         FROM adjame_product_images 
         WHERE product_id = p.id
        ) AS all_images,

        (SELECT COUNT(*) FROM adjame_product_images WHERE product_id = p.id) AS image_count,

        -- Calcul de disponibilité
        CASE 
            WHEN p.stock = 0 THEN 'Rupture'
            WHEN p.stock <= 5 THEN 'Stock faible'
            WHEN p.is_active = 0 THEN 'Brouillon'
            ELSE 'Actif'
        END AS computed_status

    FROM adjame_products p
    LEFT JOIN adjame_categories c ON p.category_id = c.id
    LEFT JOIN adjame_subcategories su ON p.subcategory_id = su.id
    LEFT JOIN adjame_sub_subcategories ssu ON p.sub_subcategory_id = ssu.id
    LEFT JOIN adjame_sub_sub_subcategories sssu ON p.sub_sub_subcategory_id = sssu.id
    LEFT JOIN adjame_boutique b ON p.boutique_id = b.id
    LEFT JOIN adjame_users u ON p.created_by = u.id
    LEFT JOIN adjame_users uu ON p.updated_by = uu.id
    LEFT JOIN adjame_product_colors pc ON p.id = pc.product_id
    LEFT JOIN adjame_colors col ON pc.color_id = col.id
    LEFT JOIN adjame_product_sizes ps ON p.id = ps.product_id
    LEFT JOIN adjame_sizes sz ON ps.size_id = sz.id
    LEFT JOIN adjame_product_trim_numbers en ON en.product_id = p.id
    LEFT JOIN adjame_product_vin_numbers vn ON vn.product_id = p.id
    LEFT JOIN (
        SELECT product_id, boost_type, status, expires_at
        FROM adjame_product_boosts
        GROUP BY product_id
    ) pb ON pb.product_id = p.id

    WHERE p.is_active = 1
      AND p.boutique_id = ?
      " . ($status ? " AND p.status = ?" : "") . "
      " . ($search ? " AND (p.name LIKE ? OR p.description LIKE ? OR p.sku LIKE ?)" : "") . "
      " . ($category ? " AND p.category_id = ?" : "") . "

    GROUP BY p.id
    ORDER BY p.$sort $order
    LIMIT ? OFFSET ?
";

    $params[] = $limit;
    $params[] = $offset;

    try {
        $products = $this->db->query($sql, $params);
        if (!is_array($products)) {
            $products = [];
        }

        $countSql = "SELECT COUNT(DISTINCT p.id) AS total FROM adjame_products p WHERE $whereClause";
        $countParams = array_slice($params, 0, -2);
        $totalResult = $this->db->query($countSql, $countParams);
        $total = isset($totalResult[0]['total']) ? (int)$totalResult[0]['total'] : 0;

        foreach ($products as &$product) {
    // ✅ Conversion des listes en tableaux (protection contre les valeurs déjà en array)
    foreach (['colors', 'color_names', 'sizes', 'trim_numbers', 'vin_numbers'] as $field) {
        if (!empty($product[$field])) {
            if (is_string($product[$field])) {
                $product[$field] = explode(',', $product[$field]);
            } elseif (!is_array($product[$field])) {
                $product[$field] = [$product[$field]];
            }
        } else {
            $product[$field] = [];
        }
    }

    // ✅ Images (all_images + primary_image)
    if (!empty($product['all_images'])) {
        if (is_string($product['all_images'])) {
            $product['all_images'] = explode('||', $product['all_images']);
        } elseif (!is_array($product['all_images'])) {
            $product['all_images'] = [$product['all_images']];
        }
    } else {
        $product['all_images'] = [];
    }

    // ✅ Images (séparation et fallback)
    $product['images'] = !empty($product['images'])
        ? (is_string($product['images']) ? explode('||', $product['images']) : $product['images'])
        : [];

    $product['primary_image'] = $product['primary_image'] ?: 'https://www.svgrepo.com/show/422038/product.svg';
    $product['image_count'] = isset($product['image_count']) ? (int)$product['image_count'] : 0;

    // ✅ Normalisation des types numériques
    $product['unit_price'] = isset($product['unit_price']) ? (float)$product['unit_price'] : 0.0;
    $product['wholesale_price'] = !empty($product['wholesale_price']) ? (float)$product['wholesale_price'] : null;
    $product['wholesale_min_qty'] = isset($product['wholesale_min_qty']) ? (int)$product['wholesale_min_qty'] : null;
    $product['stock'] = isset($product['stock']) ? (int)$product['stock'] : 0;
    $product['is_active'] = (bool)$product['is_active'];

    // ✅ Valeurs par défaut
    $product['category_name'] = $product['category_name'] ?? 'Non catégorisé';
    $product['subcategory_name'] = $product['subcategory_name'] ?? 'Non sous-catégorisé';
    $product['boutique_name'] = $product['boutique_name'] ?? 'Boutique inconnue';
    $product['created_by_name'] = $product['created_by_name'] ?? 'Utilisateur inconnu';

    // ✅ Statuts
    if (empty($product['status'])) {
        if ($product['stock'] == 0) {
            $product['status'] = 'Rupture';
        } elseif ($product['stock'] <= 5) {
            $product['status'] = 'Stock faible';
        } else {
            $product['status'] = $product['is_active'] ? 'Actif' : 'Brouillon';
        }
    }

    // ✅ Informations Boost
    $product['is_boosted'] = !empty($product['boost_type']);
    $product['boost_type'] = $product['boost_type'] ?? null;
    $product['boost_status'] = $product['boost_status'] ?? null;
    $product['boost_expires_at'] = $product['boost_expires_at'] ?? null;

    // ✅ Spécifications véhicule
    $vehicleFields = [
        'vehicle_condition', 'vehicle_make', 'vehicle_model', 'drive_type',
        'vehicle_year', 'fuel_type', 'transmission_type', 'engine_brand',
        'vehicle_mileage', 'brake_system', 'cabin_type', 'country_of_origin',
        'curb_weight', 'dimensions', 'engine_emissions', 'fuel_tank_capacity',
        'gvw', 'payload_capacity', 'production_date', 'size_type', 'suspension_type',
        'tyre_size', 'wheelbase', 'engine_number', 'disponibility'
    ];

    foreach ($vehicleFields as $vf) {
        $product[$vf] = $product[$vf] ?? null;
    }

    $product['vehicle_year'] = !empty($product['vehicle_year']) ? (int)$product['vehicle_year'] : null;
    $product['vehicle_mileage'] = !empty($product['vehicle_mileage']) ? (int)$product['vehicle_mileage'] : null;

    // ✅ Indicateur de spécifications véhicule
    $product['has_vehicle_specs'] = array_reduce($vehicleFields, function ($carry, $field) use ($product) {
        return $carry || !empty($product[$field]);
    }, false);
}


        $this->sendResponse(200, [
            'success' => true,
            'data' => $products,
            'pagination' => [
                'current_page' => $page,
                'per_page' => $limit,
                'total' => $total,
                'total_pages' => $total > 0 ? ceil($total / $limit) : 0
            ],
            'boutique' => [
                'id' => $boutiqueId,
                'name' => $this->currentBoutique['name']
            ]
        ]);

    } catch (Exception $e) {
        error_log("❌ Erreur dans getProducts: " . $e->getMessage());
        $this->sendResponse(500, [
            'success' => false,
            'error' => 'Erreur lors de la récupération des produits: ' . $e->getMessage()
        ]);
    }
}

    

    // GET /api/products.php?action=lists&category=1&subcategory=2&sub_subcategory=3&product_select=search&page=1
   private function getProductsForResults() {
    $page = (int)($_GET['page'] ?? 1);
    $limit = (int)($_GET['limit'] ?? 20);
    $search = $_GET['product_select'] ?? $_GET['search'] ?? '';
    $sort = $_GET['sort'] ?? 'created_at';
    $order = $_GET['order'] ?? 'DESC';
    $userId = null;
    if (isset($_GET['user_id']) && is_numeric($_GET['user_id'])) {
        $userId = (int) $_GET['user_id'];
    }

    $categoryId = $_GET['category'] ?? $_GET['category_id'] ?? null;
    $subcategoryId = $_GET['subcategory'] ?? $_GET['subcategory_id'] ?? null;
    $subSubcategoryId = $_GET['sub_subcategory'] ?? $_GET['sub_subcategory_id'] ?? null;

    // Filtres de prix
    $minPrice = isset($_GET['min_price']) ? (float)$_GET['min_price'] : null;
    $maxPrice = isset($_GET['max_price']) ? (float)$_GET['max_price'] : null;
    
    // Filtres communs aux camions et voitures
    $vehicleMake = $_GET['vehicle_make'] ?? null;
    $vehicleCondition = $_GET['vehicle_condition'] ?? null;
    $fuelType = $_GET['fuel_type'] ?? null;
    $transmissionType = $_GET['transmission_type'] ?? null;
    $driveType = $_GET['drive_type'] ?? null;
    $engineBrand = $_GET['engine_brand'] ?? null;
    
    $vehicleYearMin = isset($_GET['vehicle_year_min']) ? (int)$_GET['vehicle_year_min'] : null;
    $vehicleYearMax = isset($_GET['vehicle_year_max']) ? (int)$_GET['vehicle_year_max'] : null;
    
    // Filtres spécifiques aux camions
    $payloadCapacityMin = isset($_GET['payload_capacity_min']) ? (float)$_GET['payload_capacity_min'] : null;
    $payloadCapacityMax = isset($_GET['payload_capacity_max']) ? (float)$_GET['payload_capacity_max'] : null;
    
    $gvwMin = isset($_GET['gvw_min']) ? (float)$_GET['gvw_min'] : null;
    $gvwMax = isset($_GET['gvw_max']) ? (float)$_GET['gvw_max'] : null;
    
    // NOUVEAUX FILTRES POUR VOITURES
    $carExteriorColor = $_GET['car_exterior_color'] ?? null;
    $carInteriorColor = $_GET['car_interior_color'] ?? null;
    $carBodyType = $_GET['car_body_type'] ?? null;
    $vehicleMileageMin = isset($_GET['vehicle_mileage_min']) ? (int)$_GET['vehicle_mileage_min'] : null;
    $vehicleMileageMax = isset($_GET['vehicle_mileage_max']) ? (int)$_GET['vehicle_mileage_max'] : null;
    
    // Autres filtres
    $minRating = isset($_GET['min_rating']) ? (float)$_GET['min_rating'] : null;
    
    $freeShipping = isset($_GET['free_shipping']) && $_GET['free_shipping'] === 'true';
    $inStock = isset($_GET['stock']) && $_GET['stock'] === 'true';
    $verifiedSupplier = isset($_GET['boutique_verified']) && $_GET['boutique_verified'] === 'true';
    
    $boutiqueMarket = $_GET['boutique_market'] ?? null;
    $subcategories = $_GET['subcategories'] ?? null;

    $offset = ($page - 1) * $limit;

    $whereConditions = ['p.is_active = 1', 'p.status = ?'];
    $params = ['Actif'];

    // Filtres de catégories
    if ($subSubcategoryId) {
        $whereConditions[] = 'p.sub_subcategory_id = ?';
        $params[] = $subSubcategoryId;
    } elseif ($subcategoryId) {
        $whereConditions[] = 'p.subcategory_id = ?';
        $params[] = $subcategoryId;
    } elseif ($categoryId) {
        $whereConditions[] = 'p.category_id = ?';
        $params[] = $categoryId;
    }

    // Filtre par noms de sous-catégories
    if ($subcategories) {
        $subcatList = explode(',', $subcategories);
        $subcatPlaceholders = implode(',', array_fill(0, count($subcatList), '?'));
        $whereConditions[] = "(sc.name IN ($subcatPlaceholders) OR ssc.name IN ($subcatPlaceholders))";
        $params = array_merge($params, $subcatList, $subcatList);
    }

    // Recherche
    if ($search) {
        $whereConditions[] = '(p.name LIKE ? OR p.description LIKE ? OR p.sku LIKE ?)';
        $searchTerm = "%$search%";
        $params[] = $searchTerm;
        $params[] = $searchTerm;
        $params[] = $searchTerm;
    }

    // Filtre par prix
    if ($minPrice !== null) {
        $whereConditions[] = 'p.unit_price >= ?';
        $params[] = $minPrice;
    }
    if ($maxPrice !== null) {
        $whereConditions[] = 'p.unit_price <= ?';
        $params[] = $maxPrice;
    }

    // Filtre par marque de véhicule
    if ($vehicleMake) {
        $makeList = explode(',', $vehicleMake);
        $makePlaceholders = implode(',', array_fill(0, count($makeList), '?'));
        $whereConditions[] = "p.vehicle_make IN ($makePlaceholders)";
        $params = array_merge($params, $makeList);
    }

    // Filtre par état du véhicule
    if ($vehicleCondition) {
        $conditionList = explode(',', $vehicleCondition);
        $conditionPlaceholders = implode(',', array_fill(0, count($conditionList), '?'));
        $whereConditions[] = "p.vehicle_condition IN ($conditionPlaceholders)";
        $params = array_merge($params, $conditionList);
    }

    // Filtre par type de carburant
    if ($fuelType) {
        $fuelList = explode(',', $fuelType);
        $fuelPlaceholders = implode(',', array_fill(0, count($fuelList), '?'));
        $whereConditions[] = "p.fuel_type IN ($fuelPlaceholders)";
        $params = array_merge($params, $fuelList);
    }

    // Filtre par type de transmission
    if ($transmissionType) {
        $transmissionList = explode(',', $transmissionType);
        $transmissionPlaceholders = implode(',', array_fill(0, count($transmissionList), '?'));
        $whereConditions[] = "p.transmission_type IN ($transmissionPlaceholders)";
        $params = array_merge($params, $transmissionList);
    }

    // Filtre par type de traction
    if ($driveType) {
        $driveList = explode(',', $driveType);
        $drivePlaceholders = implode(',', array_fill(0, count($driveList), '?'));
        $whereConditions[] = "p.drive_type IN ($drivePlaceholders)";
        $params = array_merge($params, $driveList);
    }

    // Filtre par marque du moteur
    if ($engineBrand) {
        $engineList = explode(',', $engineBrand);
        $enginePlaceholders = implode(',', array_fill(0, count($engineList), '?'));
        $whereConditions[] = "p.engine_brand IN ($enginePlaceholders)";
        $params = array_merge($params, $engineList);
    }

    // Filtre par année
    if ($vehicleYearMin !== null) {
        $whereConditions[] = 'p.vehicle_year >= ?';
        $params[] = $vehicleYearMin;
    }
    if ($vehicleYearMax !== null) {
        $whereConditions[] = 'p.vehicle_year <= ?';
        $params[] = $vehicleYearMax;
    }

    // Filtre par capacité de charge (camions)
    if ($payloadCapacityMin !== null) {
        $whereConditions[] = 'p.payload_capacity >= ?';
        $params[] = $payloadCapacityMin;
    }
    if ($payloadCapacityMax !== null) {
        $whereConditions[] = 'p.payload_capacity <= ?';
        $params[] = $payloadCapacityMax;
    }

    // Filtre par GVW (camions)
    if ($gvwMin !== null) {
        $whereConditions[] = 'p.gvw >= ?';
        $params[] = $gvwMin;
    }
    if ($gvwMax !== null) {
        $whereConditions[] = 'p.gvw <= ?';
        $params[] = $gvwMax;
    }

    // NOUVEAUX FILTRES POUR VOITURES

    // Filtre par couleur extérieure
    if ($carExteriorColor) {
        $colorList = explode(',', $carExteriorColor);
        $colorPlaceholders = implode(',', array_fill(0, count($colorList), '?'));
        $whereConditions[] = "p.car_exterior_color IN ($colorPlaceholders)";
        $params = array_merge($params, $colorList);
    }

    // Filtre par couleur intérieure
    if ($carInteriorColor) {
        $colorList = explode(',', $carInteriorColor);
        $colorPlaceholders = implode(',', array_fill(0, count($colorList), '?'));
        $whereConditions[] = "p.car_interior_color IN ($colorPlaceholders)";
        $params = array_merge($params, $colorList);
    }

    // Filtre par type de carrosserie
    if ($carBodyType) {
        $bodyList = explode(',', $carBodyType);
        $bodyPlaceholders = implode(',', array_fill(0, count($bodyList), '?'));
        $whereConditions[] = "p.car_body_type IN ($bodyPlaceholders)";
        $params = array_merge($params, $bodyList);
    }

    // Filtre par kilométrage
    if ($vehicleMileageMin !== null) {
        $whereConditions[] = 'p.vehicle_mileage >= ?';
        $params[] = $vehicleMileageMin;
    }
    if ($vehicleMileageMax !== null) {
        $whereConditions[] = 'p.vehicle_mileage <= ?';
        $params[] = $vehicleMileageMax;
    }

    // Filtre par marché
    if ($boutiqueMarket) {
        $marketList = explode(',', $boutiqueMarket);
        $marketPlaceholders = implode(',', array_fill(0, count($marketList), '?'));
        $whereConditions[] = "b.market IN ($marketPlaceholders)";
        $params = array_merge($params, $marketList);
    }

    // Filtre par livraison gratuite
    if ($freeShipping) {
        $whereConditions[] = 'p.free_shipping = 1';
    }

    // Filtre par stock
    if ($inStock) {
        $whereConditions[] = 'p.stock > 0';
    }

    // Filtre par fournisseur vérifié
    if ($verifiedSupplier) {
        $whereConditions[] = 'b.is_verified = 1';
    }

    $whereClause = implode(' AND ', $whereConditions);

    $sql = "
        SELECT 
            p.*,
            c.name AS category_name,
            sc.name AS subcategory_name,
            ssc.name AS sub_subcategory_name,
            b.name AS boutique_name,
            b.market AS boutique_market,
            b.phone AS boutique_phone,
            b.is_verified AS boutique_verified,
            pb.boost_type,
            pb.status AS boost_status,
            pb.expires_at AS boost_expires_at,
            (SELECT image_url 
                FROM adjame_product_images 
                WHERE product_id = p.id AND is_primary = 1 
                ORDER BY sort_order ASC 
                LIMIT 1
            ) AS primary_image,
            (SELECT GROUP_CONCAT(image_url ORDER BY sort_order SEPARATOR '||') 
                FROM adjame_product_images 
                WHERE product_id = p.id LIMIT 5
            ) AS images,
            (SELECT COUNT(*) 
                FROM adjame_product_images 
                WHERE product_id = p.id
            ) AS image_count,
            CASE 
                WHEN pl.id IS NOT NULL THEN 1
                ELSE 0
            END AS likedByUser
        FROM adjame_products p
        LEFT JOIN adjame_categories c ON p.category_id = c.id
        LEFT JOIN adjame_subcategories sc ON p.subcategory_id = sc.id
        LEFT JOIN adjame_sub_subcategories ssc ON p.sub_subcategory_id = ssc.id
        LEFT JOIN adjame_boutique b ON p.boutique_id = b.id
        LEFT JOIN (
            SELECT product_id, boost_type, status, expires_at
            FROM adjame_product_boosts
            WHERE status = 'Actif'
            GROUP BY product_id
        ) pb ON pb.product_id = p.id
        LEFT JOIN adjame_product_likes pl
        ON p.id = pl.id_produit
        AND pl.user_id = " . ($userId !== null ? $userId : 0) . "
        WHERE $whereClause
        ORDER BY p.$sort $order
        LIMIT ? OFFSET ?
    ";

    $params[] = $limit;
    $params[] = $offset;

    try {
        $products = $this->db->query($sql, $params);
        if (!is_array($products)) {
            $products = [];
        }

        // Filtre par note minimum (côté application car la note est simulée)
        if ($minRating !== null && count($products) > 0) {
            $products = array_filter($products, function($product) use ($minRating) {
                $rating = round(4.0 + (rand(0, 10) / 10), 1);
                return $rating >= $minRating;
            });
            $products = array_values($products);
        }

        // Compte total
        $countSql = "SELECT COUNT(DISTINCT p.id) as total FROM adjame_products p 
                     LEFT JOIN adjame_categories c ON p.category_id = c.id
                     LEFT JOIN adjame_subcategories sc ON p.subcategory_id = sc.id
                     LEFT JOIN adjame_sub_subcategories ssc ON p.sub_subcategory_id = ssc.id
                     LEFT JOIN adjame_boutique b ON p.boutique_id = b.id
                     WHERE $whereClause";
        $countParams = array_slice($params, 0, -2);
        $totalResult = $this->db->query($countSql, $countParams);

        $total = 0;
        if (is_array($totalResult) && isset($totalResult[0]['total'])) {
            $total = (int)$totalResult[0]['total'];
        }

        // Format final
        foreach ($products as &$product) {
            $product['id'] = (int)$product['id'];
            $product['unit_price'] = (float)$product['unit_price'];
            $product['wholesale_price'] = $product['wholesale_price'] ? (float)$product['wholesale_price'] : null;
            $product['wholesale_min_qty'] = $product['wholesale_min_qty'] ? (int)$product['wholesale_min_qty'] : 1;
            $product['stock'] = (int)$product['stock'];
            $product['views'] = (int)$product['views_count'];
            $product['views_count'] = (int)$product['views_count'];
            $product['sales_count'] = (int)$product['sales_count'];
            $product['likedByUser'] = isset($product['likedByUser']) ? (int)$product['likedByUser'] : 0;
            $product['image_count'] = (int)$product['image_count'];
            $product['boutique_id'] = (int)$product['boutique_id'];
            $product['category_id'] = (int)$product['category_id'];
            $product['subcategory_id'] = $product['subcategory_id'] ? (int)$product['subcategory_id'] : null;
            $product['sub_subcategory_id'] = $product['sub_subcategory_id'] ? (int)$product['sub_subcategory_id'] : null;

            // Champs communs aux camions et voitures
            $product['vehicle_make'] = $product['vehicle_make'] ?? null;
            $product['vehicle_model'] = $product['vehicle_model'] ?? null;
            $product['vehicle_year'] = $product['vehicle_year'] ? (int)$product['vehicle_year'] : null;
            $product['vehicle_condition'] = $product['vehicle_condition'] ?? null;
            $product['fuel_type'] = $product['fuel_type'] ?? null;
            $product['transmission_type'] = $product['transmission_type'] ?? null;
            $product['drive_type'] = $product['drive_type'] ?? null;
            $product['engine_brand'] = $product['engine_brand'] ?? null;
            $product['vehicle_mileage'] = $product['vehicle_mileage'] ? (int)$product['vehicle_mileage'] : null;

            // Champs spécifiques aux camions
            $product['payload_capacity'] = $product['payload_capacity'] ? (float)$product['payload_capacity'] : null;
            $product['gvw'] = $product['gvw'] ? (float)$product['gvw'] : null;

            // NOUVEAUX CHAMPS POUR VOITURES
            $product['car_exterior_color'] = $product['car_exterior_color'] ?? null;
            $product['car_interior_color'] = $product['car_interior_color'] ?? null;
            $product['car_body_type'] = $product['car_body_type'] ?? null;

            $product['images'] = !empty($product['images']) ? explode('||', $product['images']) : [];
            if (empty($product['images']) && $product['primary_image']) {
                $product['images'] = [$product['primary_image']];
            }
            if (empty($product['images'])) {
                $product['images'] = ['https://via.placeholder.com/300x300?text=Pas+d%27image'];
            }
            $product['primary_image'] = $product['images'][0];

            $product['category_name'] = $product['category_name'] ?? 'Non catégorisé';
            $product['subcategory_name'] = $product['subcategory_name'] ?? null;
            $product['sub_subcategory_name'] = $product['sub_subcategory_name'] ?? null;
            $product['boutique_name'] = $product['boutique_name'] ?? 'Boutique';
            $product['boutique_market'] = $product['boutique_market'] ?? 'Marché';
            $product['seller_name'] = $product['boutique_name'];

            $product['rating'] = round(4.0 + (rand(0, 10) / 10), 1);
            $product['reviews'] = rand(10, 1000);
            $product['free_shipping'] = isset($product['free_shipping']) ? (bool)$product['free_shipping'] : (rand(0, 1) == 1);

            // Boost info
            $product['is_boosted'] = !empty($product['boost_type']);
            $product['boost_type'] = $product['boost_type'] ?? null;
            $product['boost_status'] = $product['boost_status'] ?? null;
            $product['boost_expires_at'] = $product['boost_expires_at'] ?? null;

            // Simulated location
            $cities = ['Abidjan', 'Bouaké', 'Daloa', 'Yamoussoukro', 'San-Pédro'];
            $communes = ['Cocody', 'Plateau', 'Adjamé', 'Yopougon', 'Marcory'];
            $product['city_name'] = $cities[array_rand($cities)];
            $product['commune_name'] = $communes[array_rand($communes)];
        }

        // Construire les filtres appliqués pour le frontend
        $appliedFilters = [];
        if ($subSubcategoryId) {
            $appliedFilters['filter_type'] = 'sub_subcategory';
            $appliedFilters['filter_id'] = $subSubcategoryId;
            $appliedFilters['filter_name'] = $products[0]['sub_subcategory_name'] ?? 'Sous-sous-catégorie';
        } elseif ($subcategoryId) {
            $appliedFilters['filter_type'] = 'subcategory';
            $appliedFilters['filter_id'] = $subcategoryId;
            $appliedFilters['filter_name'] = $products[0]['subcategory_name'] ?? 'Sous-catégorie';
        } elseif ($categoryId) {
            $appliedFilters['filter_type'] = 'category';
            $appliedFilters['filter_id'] = $categoryId;
            $appliedFilters['filter_name'] = $products[0]['category_name'] ?? 'Catégorie';
        }

        if ($search) $appliedFilters['search_query'] = $search;
        if ($minPrice !== null) $appliedFilters['min_price'] = $minPrice;
        if ($maxPrice !== null) $appliedFilters['max_price'] = $maxPrice;
        if ($vehicleMake) $appliedFilters['vehicle_make'] = $vehicleMake;
        if ($vehicleCondition) $appliedFilters['vehicle_condition'] = $vehicleCondition;
        if ($fuelType) $appliedFilters['fuel_type'] = $fuelType;
        if ($transmissionType) $appliedFilters['transmission_type'] = $transmissionType;
        if ($driveType) $appliedFilters['drive_type'] = $driveType;
        if ($engineBrand) $appliedFilters['engine_brand'] = $engineBrand;
        if ($vehicleYearMin !== null) $appliedFilters['vehicle_year_min'] = $vehicleYearMin;
        if ($vehicleYearMax !== null) $appliedFilters['vehicle_year_max'] = $vehicleYearMax;
        if ($payloadCapacityMin !== null) $appliedFilters['payload_capacity_min'] = $payloadCapacityMin;
        if ($payloadCapacityMax !== null) $appliedFilters['payload_capacity_max'] = $payloadCapacityMax;
        if ($gvwMin !== null) $appliedFilters['gvw_min'] = $gvwMin;
        if ($gvwMax !== null) $appliedFilters['gvw_max'] = $gvwMax;
        
        // Nouveaux filtres voitures dans les applied filters
        if ($carExteriorColor) $appliedFilters['car_exterior_color'] = $carExteriorColor;
        if ($carInteriorColor) $appliedFilters['car_interior_color'] = $carInteriorColor;
        if ($carBodyType) $appliedFilters['car_body_type'] = $carBodyType;
        if ($vehicleMileageMin !== null) $appliedFilters['vehicle_mileage_min'] = $vehicleMileageMin;
        if ($vehicleMileageMax !== null) $appliedFilters['vehicle_mileage_max'] = $vehicleMileageMax;
        
        if ($minRating !== null) $appliedFilters['min_rating'] = $minRating;
        if ($freeShipping) $appliedFilters['free_shipping'] = true;
        if ($inStock) $appliedFilters['in_stock'] = true;
        if ($verifiedSupplier) $appliedFilters['verified_supplier'] = true;
        if ($boutiqueMarket) $appliedFilters['boutique_market'] = $boutiqueMarket;
        if ($subcategories) $appliedFilters['subcategories'] = $subcategories;

        $this->sendResponse(200, [
            'success' => true,
            'data' => [
                'products' => $products,
                'total' => $total,
                'applied_filters' => $appliedFilters
            ],
            'pagination' => [
                'current_page' => $page,
                'per_page' => $limit,
                'total' => $total,
                'total_pages' => $total > 0 ? ceil($total / $limit) : 0
            ],
            'filters' => [
                'category_id' => $categoryId,
                'subcategory_id' => $subcategoryId,
                'sub_subcategory_id' => $subSubcategoryId,
                'search' => $search,
                'sort' => $sort,
                'order' => $order,
                'min_price' => $minPrice,
                'max_price' => $maxPrice,
                'vehicle_make' => $vehicleMake,
                'vehicle_condition' => $vehicleCondition,
                'fuel_type' => $fuelType,
                'transmission_type' => $transmissionType,
                'drive_type' => $driveType,
                'engine_brand' => $engineBrand,
                'vehicle_year_min' => $vehicleYearMin,
                'vehicle_year_max' => $vehicleYearMax,
                'payload_capacity_min' => $payloadCapacityMin,
                'payload_capacity_max' => $payloadCapacityMax,
                'gvw_min' => $gvwMin,
                'gvw_max' => $gvwMax,
                // Nouveaux filtres voitures
                'car_exterior_color' => $carExteriorColor,
                'car_interior_color' => $carInteriorColor,
                'car_body_type' => $carBodyType,
                'vehicle_mileage_min' => $vehicleMileageMin,
                'vehicle_mileage_max' => $vehicleMileageMax,
                'min_rating' => $minRating,
                'free_shipping' => $freeShipping,
                'in_stock' => $inStock,
                'verified_supplier' => $verifiedSupplier,
                'boutique_market' => $boutiqueMarket,
                'subcategories' => $subcategories
            ]
        ]);
    } catch (Exception $e) {
        error_log("Erreur dans getProductsForResults: " . $e->getMessage());
        $this->sendResponse(500, [
            'success' => false,
            'error' => 'Erreur lors de la récupération des produits: ' . $e->getMessage()
        ]);
    }
}
    
    
    // GET /api/products.php?action=show&id=1&boutique_id=1
    private function getProduct() {
        $id = $_GET['id'] ?? null;
        $slug = $_GET['slug'] ?? null;
        
        if (!$id && !$slug) {
            $this->sendResponse(400, ['error' => 'ID ou slug requis']);
            return;
        }
        
        // Détermination du champ de recherche
        if ($slug) {
            $whereClause = 'p.slug = ?';
            $searchParam = $slug;
        } else {
            $whereClause = 'p.id = ?';
            $searchParam = $id;
        }

        $sql = "
            SELECT 
                p.*,
                c.name AS category_name,
                sc.name AS subcategory_name,
                b.name AS boutique_name,
                b.business_type AS boutique_type,
                b.market AS boutique_marche,
                b.logo_url AS boutique_logo,
                b.phone AS boutique_phone,
                b.is_verified AS boutique_verified,
                b.subscription_plan AS boutique_premium,
                b.address AS boutique_address,
                b.description AS boutique_description,
                u.full_name AS created_by_name,
                u.email AS created_by_email
            FROM adjame_products p
            LEFT JOIN adjame_categories c ON p.category_id = c.id
            LEFT JOIN adjame_subcategories sc ON p.subcategory_id = sc.id
            LEFT JOIN adjame_boutique b ON p.boutique_id = b.id
            LEFT JOIN adjame_users u ON p.created_by = u.id
            WHERE $whereClause AND p.is_active = 1
        ";

        $products = $this->db->query($sql, [$searchParam]);

        if (empty($products)) {
            $this->sendResponse(404, ['error' => 'Produit non trouvé']);
            return;
        }

        $product = $products[0];
        $productId = $product['id'];
        $boutiqueId = $product['boutique_id'];

        // ✅ Récupération des couleurs
        $colorSql = "
            SELECT c.id, c.name, c.hex_value
            FROM adjame_product_colors pc
            JOIN adjame_colors c ON pc.color_id = c.id
            WHERE pc.product_id = ?
        ";
        $product['colors'] = $this->db->query($colorSql, [$productId]) ?? [];

        // ✅ Récupération des tailles
        $sizeSql = "
            SELECT s.id, s.name, s.category
            FROM adjame_product_sizes ps
            JOIN adjame_sizes s ON ps.size_id = s.id
            WHERE ps.product_id = ?
            ORDER BY s.sort_order
        ";
        $product['sizes'] = $this->db->query($sizeSql, [$productId]) ?? [];

        // ✅ Récupération des images
        $imageSql = "
            SELECT id, image_url, alt_text, is_primary, sort_order
            FROM adjame_product_images
            WHERE product_id = ?
            ORDER BY is_primary DESC, sort_order ASC
        ";
        $product['images'] = $this->db->query($imageSql, [$productId]) ?? [];

        // ✅ Récupération des numéros de moteur (nouveau)
        $engineSql = "
            SELECT id, engine_number
            FROM adjame_product_trim_numbers
            WHERE product_id = ?
            ORDER BY id ASC
        ";
        $engineNumbers = $this->db->query($engineSql, [$productId]);
        $product['trim_numbers'] = array_column($engineNumbers ?? [], 'engine_number');

         // ✅ Récupération des numéros vin (nouveau)
        $vinSql = "
            SELECT id, engine_number
            FROM adjame_product_vin_numbers
            WHERE product_id = ?
            ORDER BY id ASC
        ";
        $vinNumbers = $this->db->query($vinSql, [$productId]);
        $product['vin_numbers'] = array_column($vinNumbers ?? [], 'engine_number');

        // ✅ Récupération du boost si applicable
        $boostSql = "
            SELECT boost_type, status, expires_at
            FROM adjame_product_boosts
            WHERE product_id = ?
            ORDER BY created_at DESC
            LIMIT 1
        ";
        $boost = $this->db->query($boostSql, [$productId]);
        if (!empty($boost)) {
            $product['is_boosted'] = true;
            $product['boost_type'] = $boost[0]['boost_type'];
            $product['boost_status'] = $boost[0]['status'];
            $product['boost_expires_at'] = $boost[0]['expires_at'];
        } else {
            $product['is_boosted'] = false;
        }

        // ✅ Incrémentation des vues
        $this->incrementViews($productId);

        // ✅ Normalisation des types et valeurs par défaut
        $product['unit_price'] = (float)$product['unit_price'];
        $product['wholesale_price'] = $product['wholesale_price'] ? (float)$product['wholesale_price'] : null;
        $product['stock'] = (int)$product['stock'];
        $product['sales_count'] = (int)($product['sales_count'] ?? 0);
        $product['views_count'] = (int)($product['views_count'] ?? 0) + 1;
        $product['boutique_id'] = (int)$boutiqueId;

        // ✅ Champs véhicule
        $product['vehicle_condition'] = $product['vehicle_condition'] ?? null;
        $product['vehicle_make'] = $product['vehicle_make'] ?? null;
        $product['vehicle_model'] = $product['vehicle_model'] ?? null;
        $product['drive_type'] = $product['drive_type'] ?? null;
        $product['vehicle_year'] = !empty($product['vehicle_year']) ? (int)$product['vehicle_year'] : null;
        $product['fuel_type'] = $product['fuel_type'] ?? null;
        $product['transmission_type'] = $product['transmission_type'] ?? null;
        $product['engine_brand'] = $product['engine_brand'] ?? null;
        $product['vehicle_mileage'] = !empty($product['vehicle_mileage']) ? (int)$product['vehicle_mileage'] : null;

        $product['has_vehicle_specs'] = !empty($product['vehicle_condition']) ||
                                    !empty($product['vehicle_make']) ||
                                    !empty($product['vehicle_model']) ||
                                    !empty($product['drive_type']) ||
                                    !empty($product['vehicle_year']) ||
                                    !empty($product['fuel_type']) ||
                                    !empty($product['transmission_type']) ||
                                    !empty($product['engine_brand']) ||
                                    !empty($product['vehicle_mileage']);

        // ✅ Description détaillée
        $product['description_plus'] = $product['description_plus'] ?? '';

        // ✅ Image principale par défaut
        $primaryImage = array_values(array_filter($product['images'], fn($img) => $img['is_primary'] == 1));
        $product['primary_image'] = $primaryImage[0]['image_url'] ?? 'https://www.svgrepo.com/show/422038/product.svg';

        // ✅ Envoi de la réponse
        $this->sendResponse(200, [
            'success' => true,
            'data' => $product,
            'boutique_id' => $boutiqueId
        ]);
    }

    private function getProductSelect($idselect) {
        $id = $idselect;
        $slug = null;
        
        if (!$id && !$slug) {
            $this->sendResponse(400, ['error' => 'ID ou slug requis']);
            return;
        }
        
        // Détermination du champ de recherche
        if ($slug) {
            $whereClause = 'p.slug = ?';
            $searchParam = $slug;
        } else {
            $whereClause = 'p.id = ?';
            $searchParam = $id;
        }

        $sql = "
            SELECT 
                p.*,
                c.name AS category_name,
                sc.name AS subcategory_name,
                b.name AS boutique_name,
                b.business_type AS boutique_type,
                b.market AS boutique_marche,
                b.logo_url AS boutique_logo,
                b.phone AS boutique_phone,
                b.is_verified AS boutique_verified,
                b.subscription_plan AS boutique_premium,
                b.address AS boutique_address,
                b.description AS boutique_description,
                u.full_name AS created_by_name,
                u.email AS created_by_email
            FROM adjame_products p
            LEFT JOIN adjame_categories c ON p.category_id = c.id
            LEFT JOIN adjame_subcategories sc ON p.subcategory_id = sc.id
            LEFT JOIN adjame_boutique b ON p.boutique_id = b.id
            LEFT JOIN adjame_users u ON p.created_by = u.id
            WHERE $whereClause AND p.is_active = 1
        ";

        $products = $this->db->query($sql, [$searchParam]);

        if (empty($products)) {
            $this->sendResponse(404, ['error' => 'Produit non trouvé']);
            return;
        }

        $product = $products[0];
        $productId = $product['id'];
        $boutiqueId = $product['boutique_id'];

        // ✅ Récupération des couleurs
        $colorSql = "
            SELECT c.id, c.name, c.hex_value
            FROM adjame_product_colors pc
            JOIN adjame_colors c ON pc.color_id = c.id
            WHERE pc.product_id = ?
        ";
        $product['colors'] = $this->db->query($colorSql, [$productId]) ?? [];

        // ✅ Récupération des tailles
        $sizeSql = "
            SELECT s.id, s.name, s.category
            FROM adjame_product_sizes ps
            JOIN adjame_sizes s ON ps.size_id = s.id
            WHERE ps.product_id = ?
            ORDER BY s.sort_order
        ";
        $product['sizes'] = $this->db->query($sizeSql, [$productId]) ?? [];

        // ✅ Récupération des images
        $imageSql = "
            SELECT id, image_url, alt_text, is_primary, sort_order
            FROM adjame_product_images
            WHERE product_id = ?
            ORDER BY is_primary DESC, sort_order ASC
        ";
        $product['images'] = $this->db->query($imageSql, [$productId]) ?? [];

        // ✅ Récupération des numéros de moteur (nouveau)
        $engineSql = "
            SELECT id, engine_number
            FROM adjame_product_trim_numbers
            WHERE product_id = ?
            ORDER BY id ASC
        ";
        $engineNumbers = $this->db->query($engineSql, [$productId]);
        $product['trim_numbers'] = array_column($engineNumbers ?? [], 'engine_number');

         // ✅ Récupération des numéros vin (nouveau)
        $vinSql = "
            SELECT id, engine_number
            FROM adjame_product_vin_numbers
            WHERE product_id = ?
            ORDER BY id ASC
        ";
        $vinNumbers = $this->db->query($vinSql, [$productId]);
        $product['vin_numbers'] = array_column($vinNumbers ?? [], 'engine_number');

        // ✅ Récupération du boost si applicable
        $boostSql = "
            SELECT boost_type, status, expires_at
            FROM adjame_product_boosts
            WHERE product_id = ?
            ORDER BY created_at DESC
            LIMIT 1
        ";
        $boost = $this->db->query($boostSql, [$productId]);
        if (!empty($boost)) {
            $product['is_boosted'] = true;
            $product['boost_type'] = $boost[0]['boost_type'];
            $product['boost_status'] = $boost[0]['status'];
            $product['boost_expires_at'] = $boost[0]['expires_at'];
        } else {
            $product['is_boosted'] = false;
        }

        // ✅ Incrémentation des vues
        $this->incrementViews($productId);

        // ✅ Normalisation des types et valeurs par défaut
        $product['unit_price'] = (float)$product['unit_price'];
        $product['wholesale_price'] = $product['wholesale_price'] ? (float)$product['wholesale_price'] : null;
        $product['stock'] = (int)$product['stock'];
        $product['sales_count'] = (int)($product['sales_count'] ?? 0);
        $product['views_count'] = (int)($product['views_count'] ?? 0) + 1;
        $product['boutique_id'] = (int)$boutiqueId;

        // ✅ Champs véhicule
        $product['vehicle_condition'] = $product['vehicle_condition'] ?? null;
        $product['vehicle_make'] = $product['vehicle_make'] ?? null;
        $product['vehicle_model'] = $product['vehicle_model'] ?? null;
        $product['drive_type'] = $product['drive_type'] ?? null;
        $product['vehicle_year'] = !empty($product['vehicle_year']) ? (int)$product['vehicle_year'] : null;
        $product['fuel_type'] = $product['fuel_type'] ?? null;
        $product['transmission_type'] = $product['transmission_type'] ?? null;
        $product['engine_brand'] = $product['engine_brand'] ?? null;
        $product['vehicle_mileage'] = !empty($product['vehicle_mileage']) ? (int)$product['vehicle_mileage'] : null;

        $product['has_vehicle_specs'] = !empty($product['vehicle_condition']) ||
                                    !empty($product['vehicle_make']) ||
                                    !empty($product['vehicle_model']) ||
                                    !empty($product['drive_type']) ||
                                    !empty($product['vehicle_year']) ||
                                    !empty($product['fuel_type']) ||
                                    !empty($product['transmission_type']) ||
                                    !empty($product['engine_brand']) ||
                                    !empty($product['vehicle_mileage']);

        // ✅ Description détaillée
        $product['description_plus'] = $product['description_plus'] ?? '';

        // ✅ Image principale par défaut
        $primaryImage = array_values(array_filter($product['images'], fn($img) => $img['is_primary'] == 1));
        $product['primary_image'] = $primaryImage[0]['image_url'] ?? 'https://www.svgrepo.com/show/422038/product.svg';

        // ✅ Envoi de la réponse
        $this->sendResponse(200, [
            'success' => true,
            'data' => $product,
            'boutique_id' => $boutiqueId
        ]);
    }

    
    // ANCIENNE VERSION - Garde la compatibilité avec l'autre page
    // GET /api/products.php?action=most_viewed_products&boutique_id=1
    private function getMostViewedProductsOld() {
        try {
            // Requête pour récupérer les 7 produits les plus vus (ancienne logique)
            $sql = "
                SELECT 
                    p.id,
                    p.name,
                    p.unit_price,
                    p.stock,
                    p.status,
                    p.slug,
                    p.fuel_type,
                    p.vehicle_mileage,
                    p.vehicle_condition,
                    p.views_count,
                    p.sales_count,
                    c.name as category_name,
                    sc.name as subcategory_name,
                    b.name as boutique_name,
                    (SELECT image_url FROM adjame_product_images WHERE product_id = p.id AND is_primary = 1 ORDER BY sort_order ASC LIMIT 1) AS primary_image,
                    (SELECT COUNT(*) FROM adjame_product_images WHERE product_id = p.id) AS image_count
                FROM adjame_products p
                LEFT JOIN adjame_categories c ON p.category_id = c.id
                LEFT JOIN adjame_subcategories sc ON p.subcategory_id = sc.id
                LEFT JOIN adjame_boutique b ON p.boutique_id = b.id
                WHERE p.is_active = 1
                    AND p.status = 'Actif'
                ORDER BY p.views_count DESC, p.sales_count DESC
                LIMIT 7
            ";
            
            $products = $this->db->query($sql);
            
            if (!is_array($products)) {
                $products = [];
            }
            
            // Formater les données (ancienne logique)
            foreach ($products as &$product) {
                $product['unit_price'] = (float)$product['unit_price'];
                $product['stock'] = (int)$product['stock'];
                $product['views_count'] = (int)$product['views_count'];
                $product['sales_count'] = (int)$product['sales_count'];
                $product['image_count'] = (int)$product['image_count'];
                $product['primary_image'] = $product['primary_image'] ?: 'https://www.svgrepo.com/show/422038/product.svg';
                
                // Valeurs par défaut
                $product['category_name'] = $product['category_name'] ?? 'Non catégorisé';
                $product['subcategory_name'] = $product['subcategory_name'] ?? 'Non sous-catégorisé';
            }
            
            // Diviser les produits en deux groupes (ancienne logique)
            $top3Products = array_slice($products, 0, 3);
            $next4Products = array_slice($products, 3, 4);
            
            $this->sendResponse(200, [
                'success' => true,
                'data' => [
                    'top_3_products' => $top3Products,
                    'next_4_products' => $next4Products,
                    'total_products' => count($products)
                ]
            ]);
            
        } catch (Exception $e) {
            error_log("Erreur dans getMostViewedProductsOld: " . $e->getMessage());
            $this->sendResponse(500, [
                'success' => false,
                'error' => 'Erreur lors de la récupération des produits les plus vus: ' . $e->getMessage()
            ]);
        }
    }
    
    /**
 * Récupérer tous les plans premium disponibles
 * GET /api/products.php?action=premium_plans
 */
    /**
 * Souscrire une boutique à un plan premium
 * POST /api/products.php?action=premium_subscribe&boutique_id=1
 */
private function subscribeBoutiqueToPremium() {
    $input = json_decode(file_get_contents('php://input'), true);
    $boutiqueId = $_GET['boutique_id'] ?? $this->currentBoutique['id'];
    
    if (!$boutiqueId) {
        $this->sendResponse(400, ['error' => 'ID de boutique requis']);
        return;
    }
    
    if (!isset($input['plan_id'])) {
        $this->sendResponse(400, ['error' => 'ID du plan requis']);
        return;
    }
    
    try {
        $this->db->beginTransaction();
        
        // Vérifier que le plan existe
        $planSql = "SELECT * FROM adjame_premium_plans WHERE id = ? AND is_active = 1";
        $plans = $this->db->query($planSql, [$input['plan_id']]);
        
        if (empty($plans)) {
            $this->sendResponse(404, ['error' => 'Plan premium non trouvé']);
            return;
        }
        
        $plan = $plans[0];
        
        // Calculer la date d'expiration
        $expiresAt = date('Y-m-d H:i:s', strtotime("+{$plan['duration_months']} months"));
        
        // Vérifier s'il y a déjà un abonnement actif pour cette boutique
        $existingSql = "SELECT id FROM adjame_boutique_subscriptions WHERE boutique_id = ? AND status = 'active'";
        $existing = $this->db->query($existingSql, [$boutiqueId]);
        
        if (!empty($existing)) {
            // Mettre à jour l'abonnement existant
            $updateSql = "
                UPDATE adjame_boutique_subscriptions SET
                    plan_id = ?,
                    plan_name = ?,
                    price = ?,
                    max_products = ?,
                    max_images = ?,
                    expires_at = ?,
                    updated_at = NOW()
                WHERE boutique_id = ? AND status = 'active'
            ";
            
            $this->db->query($updateSql, [
                $plan['id'],
                $plan['plan_name'],
                $plan['price'],
                $plan['max_products'],
                $plan['max_images'],
                $expiresAt,
                $boutiqueId
            ]);
        } else {
            // Créer un nouvel abonnement
            $insertSql = "
                INSERT INTO adjame_boutique_subscriptions (
                    boutique_id, plan_id, plan_name, price, max_products, max_images,
                    status, expires_at, created_by, created_at
                ) VALUES (?, ?, ?, ?, ?, ?, 'active', ?, ?, NOW())
            ";
            
            $this->db->query($insertSql, [
                $boutiqueId,
                $plan['id'],
                $plan['plan_name'],
                $plan['price'],
                $plan['max_products'],
                $plan['max_images'],
                $expiresAt,
                $this->currentUser['id']
            ]);
        }
        
        // Mettre à jour le statut premium de la boutique
        $updateBoutiqueSql = "UPDATE adjame_boutique SET subscription_plan = 'premium' WHERE id = ?";
        $this->db->query($updateBoutiqueSql, [$boutiqueId]);
        
        $this->db->commit();
        
        $this->sendResponse(200, [
            'success' => true,
            'message' => 'Abonnement premium activé pour la boutique',
            'data' => [
                'boutique_id' => (int)$boutiqueId,
                'plan_name' => $plan['plan_name'],
                'expires_at' => $expiresAt
            ]
        ]);
        
    } catch (Exception $e) {
        $this->db->rollback();
        error_log("Erreur dans subscribeBoutiqueToPremium: " . $e->getMessage());
        $this->sendResponse(500, [
            'success' => false,
            'error' => 'Erreur lors de la souscription: ' . $e->getMessage()
        ]);
    }
}

/**
 * Récupérer l'abonnement actuel d'une boutique
 * GET /api/products.php?action=premium_subscription&boutique_id=1
 */
private function getBoutiqueSubscription() {
    $boutiqueId = $_GET['boutique_id'] ?? $this->currentBoutique['id'];
    
    if (!$boutiqueId) {
        $this->sendResponse(400, ['error' => 'ID de boutique requis']);
        return;
    }
    
    try {
        $sql = "
            SELECT 
                bs.id,
                bs.boutique_id,
                bs.plan_id,
                bs.plan_name,
                bs.price,
                bs.max_products,
                bs.max_images,
                bs.status,
                bs.expires_at,
                bs.created_at,
                bs.updated_at,
                b.name as boutique_name
            FROM adjame_boutique_subscriptions bs
            LEFT JOIN adjame_boutique b ON bs.boutique_id = b.id
            WHERE bs.boutique_id = ? AND bs.status = 'active'
            ORDER BY bs.created_at DESC 
            LIMIT 1
        ";
        
        $subscriptions = $this->db->query($sql, [$boutiqueId]);
        
        if (empty($subscriptions)) {
            $this->sendResponse(200, [
                'success' => true,
                'data' => null,
                'message' => 'Aucun abonnement premium actif pour cette boutique'
            ]);
            return;
        }
        
        $subscription = $subscriptions[0];
        
        // Vérifier si l'abonnement a expiré
        if (strtotime($subscription['expires_at']) < time()) {
            // Marquer comme expiré
            $updateSql = "UPDATE adjame_boutique_subscriptions SET status = 'expired' WHERE id = ?";
            $this->db->query($updateSql, [$subscription['id']]);
            
            // Mettre à jour le statut de la boutique
            $updateBoutiqueSql = "UPDATE adjame_boutique SET subscription_plan = 'free' WHERE id = ?";
            $this->db->query($updateBoutiqueSql, [$boutiqueId]);
            
            $this->sendResponse(200, [
                'success' => true,
                'data' => null,
                'message' => 'Abonnement premium expiré pour cette boutique'
            ]);
            return;
        }
        
        // Formater les données
        $subscription['id'] = (int)$subscription['id'];
        $subscription['boutique_id'] = (int)$subscription['boutique_id'];
        $subscription['plan_id'] = (int)$subscription['plan_id'];
        $subscription['price'] = (float)$subscription['price'];
        $subscription['max_products'] = (int)$subscription['max_products'];
        $subscription['max_images'] = (int)$subscription['max_images'];
        
        $this->sendResponse(200, [
            'success' => true,
            'data' => $subscription
        ]);
        
    } catch (Exception $e) {
        error_log("Erreur dans getBoutiqueSubscription: " . $e->getMessage());
        $this->sendResponse(500, [
            'success' => false,
            'error' => 'Erreur lors de la récupération de l\'abonnement: ' . $e->getMessage()
        ]);
    }
}

/**
 * Annuler l'abonnement premium d'une boutique
 * POST /api/products.php?action=premium_cancel&boutique_id=1
 */
private function cancelBoutiqueSubscription() {
    $boutiqueId = $_GET['boutique_id'] ?? $this->currentBoutique['id'];
    $input = json_decode(file_get_contents('php://input'), true);
    
    if (!$boutiqueId) {
        $this->sendResponse(400, ['error' => 'ID de boutique requis']);
        return;
    }
    
    try {
        $this->db->beginTransaction();
        
        // Annuler l'abonnement
        $sql = "
            UPDATE adjame_boutique_subscriptions 
            SET status = 'cancelled', 
                cancellation_reason = ?,
                updated_at = NOW()
            WHERE boutique_id = ? AND status = 'active'
        ";
        
        $result = $this->db->query($sql, [
            $input['reason'] ?? 'Demande utilisateur',
            $boutiqueId
        ]);
        
        // Mettre à jour le statut de la boutique
        $updateBoutiqueSql = "UPDATE adjame_boutique SET subscription_plan = 'free' WHERE id = ?";
        $this->db->query($updateBoutiqueSql, [$boutiqueId]);
        
        $this->db->commit();
        
        if ($result) {
            $this->sendResponse(200, [
                'success' => true,
                'message' => 'Abonnement premium annulé pour la boutique'
            ]);
        } else {
            $this->sendResponse(404, [
                'success' => false,
                'error' => 'Aucun abonnement actif trouvé pour cette boutique'
            ]);
        }
        
    } catch (Exception $e) {
        $this->db->rollback();
        error_log("Erreur dans cancelBoutiqueSubscription: " . $e->getMessage());
        $this->sendResponse(500, [
            'success' => false,
            'error' => 'Erreur lors de l\'annulation: ' . $e->getMessage()
        ]);
    }
}

/**
 * Vérifier les limites du plan premium d'une boutique
 * GET /api/products.php?action=premium_check_limits&feature=products&boutique_id=1
 */
private function checkBoutiquePremiumLimits() {
    $boutiqueId = $_GET['boutique_id'] ?? $this->currentBoutique['id'];
    $feature = $_GET['feature'] ?? 'products';
    
    if (!$boutiqueId) {
        $this->sendResponse(400, ['error' => 'ID de boutique requis']);
        return;
    }
    
    try {
        // Récupérer l'abonnement actuel de la boutique
        $subSql = "
            SELECT max_products, max_images 
            FROM adjame_boutique_subscriptions 
            WHERE boutique_id = ? AND status = 'active' AND expires_at > NOW()
        ";
        $subscriptions = $this->db->query($subSql, [$boutiqueId]);
        
        if (empty($subscriptions)) {
            // Plan gratuit
            $limits = [
                'products' => ['max' => 5, 'current' => 0],
                'images' => ['max' => 20, 'current' => 0]
            ];
        } else {
            $subscription = $subscriptions[0];
            $limits = [
                'products' => ['max' => (int)$subscription['max_products'], 'current' => 0],
                'images' => ['max' => (int)$subscription['max_images'], 'current' => 0]
            ];
        }
        
        // Compter l'utilisation actuelle de la boutique
        if ($feature === 'products' || $feature === 'all') {
            $productCountSql = "SELECT COUNT(*) as count FROM adjame_products WHERE boutique_id = ? AND is_active = 1";
            $productCount = $this->db->query($productCountSql, [$boutiqueId]);
            $limits['products']['current'] = (int)$productCount[0]['count'];
        }
        
        if ($feature === 'images' || $feature === 'all') {
            $imageCountSql = "
                SELECT COUNT(*) as count 
                FROM adjame_product_images pi
                JOIN adjame_products p ON pi.product_id = p.id
                WHERE p.boutique_id = ? AND p.is_active = 1
            ";
            $imageCount = $this->db->query($imageCountSql, [$boutiqueId]);
            $limits['images']['current'] = (int)$imageCount[0]['count'];
        }
        
        $this->sendResponse(200, [
            'success' => true,
            'data' => [
                'boutique_id' => (int)$boutiqueId,
                'limits' => $limits
            ]
        ]);
        
    } catch (Exception $e) {
        error_log("Erreur dans checkBoutiquePremiumLimits: " . $e->getMessage());
        $this->sendResponse(500, [
            'success' => false,
            'error' => 'Erreur lors de la vérification des limites: ' . $e->getMessage()
        ]);
    }
}
    // NOUVELLE VERSION - Pour la page d'accueil
    // GET /api/products.php?action=most_viewed&limit=15
    private function getMostViewedProducts() {

        // =========================
        // PARAMÈTRES
        // =========================
        $limit  = isset($_GET['limit']) ? (int) $_GET['limit'] : 15;
        $limit  = max(1, min($limit, 50)); // sécurité
        $period = $_GET['period'] ?? 'all'; // all | week | month

        $userId = null;
        if (isset($_GET['user_id']) && is_numeric($_GET['user_id'])) {
            $userId = (int) $_GET['user_id'];
        }

        try {

            // =========================
            // CONDITION DE PÉRIODE
            // =========================
            $periodCondition = '';
            if ($period === 'week') {
                $periodCondition = "AND p.created_at >= DATE_SUB(NOW(), INTERVAL 1 WEEK)";
            } elseif ($period === 'month') {
                $periodCondition = "AND p.created_at >= DATE_SUB(NOW(), INTERVAL 1 MONTH)";
            }

            // =========================
            // SQL BASE
            // =========================
            $sql = "
                SELECT 
                    p.id,
                    p.name,
                    p.unit_price,
                    p.wholesale_price,
                    p.wholesale_min_qty,
                    p.stock,
                    p.status,
                    p.fuel_type,
                    p.vehicle_mileage,
                    p.vehicle_condition,
                    p.slug,
                    p.views_count,
                    p.sales_count,
                    p.created_at,
                    c.name AS category_name,
                    sc.name AS subcategory_name,
                    b.name AS boutique_name,
                    b.id AS boutique_id,
                    b.market AS boutique_market,
                    (
                        SELECT image_url 
                        FROM adjame_product_images 
                        WHERE product_id = p.id 
                        AND is_primary = 1 
                        ORDER BY sort_order ASC 
                        LIMIT 1
                    ) AS primary_image,
                    (
                        SELECT COUNT(*) 
                        FROM adjame_product_images 
                        WHERE product_id = p.id
                    ) AS image_count,
                    CASE 
                        WHEN pl.id IS NOT NULL THEN 1 
                        ELSE 0 
                    END AS likedByUser
                FROM adjame_products p
                LEFT JOIN adjame_categories c ON p.category_id = c.id
                LEFT JOIN adjame_subcategories sc ON p.subcategory_id = sc.id
                LEFT JOIN adjame_boutique b ON p.boutique_id = b.id
                LEFT JOIN adjame_product_likes pl 
                    ON p.id = pl.id_produit 
                    AND pl.user_id = " . ($userId !== null ? $userId : 0) . "
                WHERE p.is_active = 1
                AND p.status = 'Actif'
                $periodCondition
                ORDER BY p.views_count DESC, p.sales_count DESC, p.created_at DESC
                LIMIT $limit
            ";

            // =========================
            // EXÉCUTION (AUCUN PARAM BIND)
            // =========================
            $products = $this->db->query($sql);

            if (!is_array($products)) {
                $products = [];
            }

            // =========================
            // FORMATAGE
            // =========================
            foreach ($products as &$product) {
                $product['unit_price']       = (float) $product['unit_price'];
                $product['wholesale_price']  = $product['wholesale_price'] !== null ? (float) $product['wholesale_price'] : null;
                $product['wholesale_min_qty'] = $product['wholesale_min_qty'] !== null ? (int) $product['wholesale_min_qty'] : null;
                $product['stock']            = (int) $product['stock'];
                $product['views']            = (int) $product['views_count']; // alias frontend
                $product['views_count']      = (int) $product['views_count'];
                $product['sales_count']      = (int) $product['sales_count'];
                $product['image_count']      = (int) $product['image_count'];
                $product['boutique_id']      = (int) $product['boutique_id'];
                $product['likedByUser']      = (int) $product['likedByUser'];
                
                $product['primary_image']    = $product['primary_image'] ?: 'https://via.placeholder.com/300x300?text=Produit';
                $product['category_name']    = $product['category_name'] ?? 'Non catégorisé';
                $product['subcategory_name'] = $product['subcategory_name'] ?? 'Non sous-catégorisé';
                $product['boutique_name']    = $product['boutique_name'] ?? 'Boutique';
                $product['boutique_market']  = $product['boutique_market'] ?? 'Marché';

                // Rating (démo)
                $product['rating']           = round(min(5.0, 3.0 + ($product['views_count'] / 1000)), 1);
                // Expérience boutique (démo)
                $product['experience']       = rand(2, 10);
            }
            unset($product);

            // =========================
            // RESPONSE
            // =========================
            $this->sendResponse(200, [
                'success' => true,
                'data'    => $products,
                'count'   => count($products),
                'period'  => $period,
                'limit'   => $limit
            ]);

        } catch (Throwable $e) {
            error_log("Erreur getMostViewedProducts: " . $e->getMessage());
            $this->sendResponse(500, [
                'success' => false,
                'error'   => 'Erreur lors de la récupération des produits'
            ]);
        }
    }


    
    // GET /api/products.php?action=random_supplier&limit=6
    private function getRandomSupplier() {
        $limit = (int)($_GET['limit'] ?? 6);
        
        try {
            // Étape 1: Sélectionner une boutique aléatoire qui a des produits actifs
            // CORRECTION: Utiliser les colonnes qui existent vraiment dans votre table
            $boutiqueSql = "
                SELECT 
                    b.id,
                    b.name,
                    b.slug,
                    b.business_type as category,
                    b.market,
                    b.description,
                    b.created_at,
                    COUNT(p.id) as products_count,
                    TIMESTAMPDIFF(YEAR, b.created_at, NOW()) as experience
                FROM adjame_boutique b
                INNER JOIN adjame_products p ON b.id = p.boutique_id
                WHERE b.is_active = 1 
                    AND p.is_active = 1 
                    AND p.status = 'Actif'
                GROUP BY b.id
                HAVING products_count >= 3
                ORDER BY RAND()
                LIMIT 1
            ";
            
            $boutiques = $this->db->query($boutiqueSql);
            
            if (empty($boutiques)) {
                $this->sendResponse(404, [
                    'success' => false,
                    'error' => 'Aucune boutique avec des produits actifs trouvée'
                ]);
                return;
            }
            
            $boutique = $boutiques[0];
            
            // Étape 2: Récupérer les produits de cette boutique
            $productsSql = "
                SELECT 
                    p.id,
                    p.name,
                    p.unit_price,
                    p.wholesale_price,
                    p.wholesale_min_qty,
                    p.stock,
                    p.status,
                    p.views_count,
                    p.sales_count,
                    c.name as category_name,
                    sc.name as subcategory_name,
                    (SELECT image_url FROM adjame_product_images WHERE product_id = p.id AND is_primary = 1 ORDER BY sort_order ASC LIMIT 1) AS primary_image,
                    (SELECT COUNT(*) FROM adjame_product_images WHERE product_id = p.id) AS image_count
                FROM adjame_products p
                LEFT JOIN adjame_categories c ON p.category_id = c.id
                LEFT JOIN adjame_subcategories sc ON p.subcategory_id = sc.id
                WHERE p.boutique_id = ?
                    AND p.is_active = 1
                    AND p.status = 'Actif'
                ORDER BY p.views_count DESC, p.sales_count DESC
                LIMIT ?
            ";
            
            $products = $this->db->query($productsSql, [$boutique['id'], $limit]);
            
            if (!is_array($products)) {
                $products = [];
            }
            
            // Formater les données de la boutique
            $boutique['id'] = (int)$boutique['id'];
            $boutique['products_count'] = (int)$boutique['products_count'];
            $boutique['experience'] = max(1, (int)$boutique['experience']); // Au moins 1 an
            
            // URLs par défaut (puisque les colonnes n'existent pas encore)
            $boutique['logo'] = 'https://images.unsplash.com/photo-1560472354-b33ff0c44a43?w=60&h=60&fit=crop&auto=format';
            $boutique['banner'] = 'https://images.unsplash.com/photo-1441986300917-64674bd600d8?w=400&h=250&fit=crop&auto=format';
            $boutique['video_url'] = null; // Pas de vidéo pour l'instant
            
            // Formater les données des produits
            foreach ($products as &$product) {
                $product['id'] = (int)$product['id'];
                $product['unit_price'] = (float)$product['unit_price'];
                $product['wholesale_price'] = $product['wholesale_price'] ? (float)$product['wholesale_price'] : null;
                $product['wholesale_min_qty'] = $product['wholesale_min_qty'] ? (int)$product['wholesale_min_qty'] : null;
                $product['stock'] = (int)$product['stock'];
                $product['views'] = (int)$product['views_count'];
                $product['views_count'] = (int)$product['views_count'];
                $product['sales_count'] = (int)$product['sales_count'];
                $product['image_count'] = (int)$product['image_count'];
                
                // Image par défaut si pas d'image
                $product['primary_image'] = $product['primary_image'] ?: 'https://via.placeholder.com/120x120?text=Produit';
                
                // Valeurs par défaut
                $product['category_name'] = $product['category_name'] ?? 'Non catégorisé';
                $product['subcategory_name'] = $product['subcategory_name'] ?? 'Non sous-catégorisé';
                
                // Ajouter des badges aléatoires pour la démo
                $badges = ['Hot', 'Bestseller', 'Nouveau', 'Promo', 'Premium', 'Exclusif'];
                $product['badge'] = $badges[array_rand($badges)];
                
                // Reviews aléatoires pour la démo
                $product['reviews'] = rand(50, 5000);
            }
            
            $this->sendResponse(200, [
                'success' => true,
                'data' => [
                    'boutique' => $boutique,
                    'products' => $products
                ],
                'message' => 'Fournisseur aléatoire récupéré avec succès'
            ]);
            
        } catch (Exception $e) {
            error_log("Erreur dans getRandomSupplier: " . $e->getMessage());
            $this->sendResponse(500, [
                'success' => false,
                'error' => 'Erreur lors de la récupération du fournisseur aléatoire: ' . $e->getMessage()
            ]);
        }
    }
    
    // POST /api/products.php?action=create&boutique_id=1&user_id=1
   private function createProduct() {
    $input = json_decode(file_get_contents('php://input'), true);

    // Récupérer la boutique et l'utilisateur
    $boutiqueId = $_GET['boutique_id'] ?? $_POST['boutique_id'] ?? $this->currentBoutique['id'];
    $userId = $this->currentUser['id'];

    if (!$boutiqueId) {
        $this->sendResponse(400, ['error' => 'ID de boutique requis']);
        return;
    }

    // Vérifier l'accès à la boutique
    if (!$this->hasAccessToBoutique($userId, $boutiqueId)) {
        $this->sendResponse(403, ['error' => 'Accès non autorisé à cette boutique']);
        return;
    }

    // Validation des données requises
    $required = ['name', 'category_id', 'subcategory_id', 'unit_price', 'stock'];
    foreach ($required as $field) {
        if (!isset($input[$field]) || empty($input[$field])) {
            $this->sendResponse(400, ['error' => "Le champ $field est requis"]);
            return;
        }
    }

    // ✅ Détecter la catégorie (Trailer, Car, ou Truck)
    $isTrailer = false;
    $isCar = false;

    if (!empty($input['category_id'])) {
        $catSql = "SELECT name FROM adjame_categories WHERE id = ?";
        $catResult = $this->db->query($catSql, [$input['category_id']]);
        if (!empty($catResult)) {
            $catName = strtolower($catResult[0]['name']);

            $isTrailer = strpos($catName, 'trailer') !== false ||
                        strpos($catName, 'semi') !== false ||
                        strpos($catName, 'remorque') !== false;

            $isCar = strpos($catName, 'car') !== false ||
                     strpos($catName, 'voiture') !== false ||
                     strpos($catName, 'auto') !== false ||
                     (strpos($catName, 'vehicle') !== false && !$isTrailer);
        }
    }

    // ========================================
    // Champs TRUCKS (véhicules lourds)
    // ========================================
    $vehicle_condition = $input['vehicle_condition'] ?? '';
    $vehicle_make = $input['vehicle_make'] ?? '';
    $vehicle_model = $input['vehicle_model'] ?? '';
    $drive_type = $input['drive_type'] ?? '';
    $vehicle_year = $input['vehicle_year'] ?? '';
    $fuel_type = $input['fuel_type'] ?? '';
    $transmission_type = $input['transmission_type'] ?? '';
    $engine_brand = $input['engine_brand'] ?? '';
    $vehicle_mileage = $input['vehicle_mileage'] ?? '';

    // Champs techniques trucks
    $brake_system = $input['brake_system'] ?? '';
    $cabin_type = $input['cabin_type'] ?? '';
    $country_of_origin = $input['country_of_origin'] ?? '';
    $curb_weight = $input['curb_weight'] ?? '';
    $dimensions = $input['dimensions'] ?? '';
    $fuel_tank_capacity = $input['fuel_tank_capacity'] ?? '';
    $gvw = $input['gvw'] ?? '';
    $other_description = $input['description_plus'] ?? '';
    $payload_capacity = $input['payload_capacity'] ?? '';
    $car_availability = $input['car_availability'] ?? '';
    $production_date = $input['production_date'] ?? '';
    $size_type = $input['size_type'] ?? '';
    $suspension_type = $input['suspension_type'] ?? '';
    $tyre_size = $input['tyre_size'] ?? '';
    $wheelbase = $input['wheelbase'] ?? '';
    $engine_n = $input['engine_number'] ?? '';
    $disponibility = $input['disponibility'] ?? '';
    $stockNumber = $this->generateStockNumber();
    $video = $input['video'] ?? '';

    // ========================================
    // Champs TRAILERS
    // ========================================
    $trailer_type = $input['trailer_type'] ?? '';
    $trailer_brand = $input['trailer_brand'] ?? '';
    $trailer_use = $input['trailer_use'] ?? '';
    $trailer_size = $input['trailer_size'] ?? '';
    $trailer_axle = $input['trailer_axle'] ?? '';
    $trailer_suspension = $input['trailer_suspension'] ?? '';
    $trailer_tire = $input['trailer_tire'] ?? '';
    $trailer_king_pin = $input['trailer_king_pin'] ?? '';
    $trailer_main_beam = $input['trailer_main_beam'] ?? '';
    $trailer_max_payload = $input['trailer_max_payload'] ?? '';
    $trailer_place_of_origin = $input['trailer_place_of_origin'] ?? '';
    $trailer_material = $input['trailer_material'] ?? '';
    $trailer_function = $input['trailer_function'] ?? '';
    $trailer_landing_gear = $input['trailer_landing_gear'] ?? '';
    $trailer_color = $input['trailer_color'] ?? '';
    $trailer_condition = $input['trailer_condition'] ?? '';
    $trailer_axle_brand = $input['trailer_axle_brand'] ?? '';

    // ========================================
    // NOUVEAUX CHAMPS CAR (41 champs)
    // ========================================

    // Basic Info (6)
    $car_make = $input['car_make'] ?? '';
    $car_model = $input['car_model'] ?? '';
    $car_year = $input['car_year'] ?? '';
    $car_condition = $input['car_condition'] ?? '';
    $car_vin = $input['car_vin'] ?? '';
    $car_mileage = $input['car_mileage'] ?? '';

    // Battery & Electric (4)
    $car_battery_range = $input['car_battery_range'] ?? '';
    $car_charge_time_full = $input['car_charge_time_full'] ?? '';
    $car_quick_charge_time = $input['car_quick_charge_time'] ?? '';
    $car_battery_capacity = $input['car_battery_capacity'] ?? '';

    // Dimensions (5)
    $car_height = $input['car_height'] ?? '';
    $car_length = $input['car_length'] ?? '';
    $car_width = $input['car_width'] ?? '';
    $car_kerb_weight = $input['car_kerb_weight'] ?? '';
    $car_wheelbase = $input['car_wheelbase'] ?? '';

    // Engine & Drivetrain (5)
    $car_fuel_type = $input['car_fuel_type'] ?? '';
    $car_transmission = $input['car_transmission'] ?? '';
    $car_engine_size = $input['car_engine_size'] ?? '';
    $car_engine_cylinders = $input['car_engine_cylinders'] ?? '';
    $car_drivetrain = $input['car_drivetrain'] ?? '';

    // General (5)
    $car_doors = $input['car_doors'] ?? '';
    $car_seats = $input['car_seats'] ?? '';
    $car_warranty_miles = $input['car_warranty_miles'] ?? '';
    $car_warranty_years = $input['car_warranty_years'] ?? '';
    $car_insurance_group = $input['car_insurance_group'] ?? '';

    // Performance (5)
    $car_top_speed = $input['car_top_speed'] ?? '';
    $car_engine_power_bhp = $input['car_engine_power_bhp'] ?? '';
    $car_engine_power_kw = $input['car_engine_power_kw'] ?? '';
    $car_engine_torque = $input['car_engine_torque'] ?? '';
    $car_acceleration = $input['car_acceleration'] ?? '';

    // Colors & Interior (3)
    $car_exterior_color = $input['car_exterior_color'] ?? '';
    $car_interior_color = $input['car_interior_color'] ?? '';
    $car_interior_material = $input['car_interior_material'] ?? '';

    // Efficiency (4)
    $car_mpg_city = $input['car_mpg_city'] ?? '';
    $car_mpg_highway = $input['car_mpg_highway'] ?? '';
    $car_mpg_combined = $input['car_mpg_combined'] ?? '';
    $car_co2_emissions = $input['car_co2_emissions'] ?? '';

    // Additional (4)
    $car_body_type = $input['car_body_type'] ?? '';
    $car_trim_level = $input['car_trim_level'] ?? '';
    $car_previous_owners = $input['car_previous_owners'] ?? '';
    $car_service_history = $input['car_service_history'] ?? '';

    // Data Source (3)
    $car_data_source = $input['car_data_source'] ?? 'manual';
    $car_vin_decoded_at = $input['car_vin_decoded_at'] ?? '';
    $car_api_provider = $input['car_api_provider'] ?? '';

    // ========================================
    // Génération automatique du nom
    // ========================================

    if ($isTrailer && $trailer_condition && $trailer_type && $trailer_axle && $trailer_max_payload) {
        $conditionFormatted = ucfirst(strtolower($trailer_condition));
        $input['name'] = $conditionFormatted . ' - ' . $trailer_type . ' - ' . $trailer_axle . ' Axles - ' . $trailer_max_payload . 'T';
    }

    if ($isCar && $car_year && $car_make && $car_model) {
        $input['name'] = $car_year . ' ' . $car_make . ' ' . $car_model;
        if ($car_trim_level) {
            $input['name'] .= ' ' . $car_trim_level;
        }
    }

    try {
        $this->db->beginTransaction();

        $slug = $this->generateSlug($input['name']);

        // Validation hiérarchie catégorie
        $subSubcategoryId = null;
        if (!empty($input['subsubcategory_id'])) {
            $check = $this->db->query("SELECT id FROM adjame_sub_subcategories WHERE id = ?", [$input['subsubcategory_id']]);
            if (!empty($check)) {
                $subSubcategoryId = $input['subsubcategory_id'];
            }
        }

        $subSubSubcategoryId = 0;
        if (!empty($input['subsubsubcategory_id'])) {
            $check = $this->db->query("SELECT id FROM adjame_sub_sub_subcategories WHERE id = ?", [$input['subsubsubcategory_id']]);
            if (!empty($check)) {
                $subSubSubcategoryId = $input['subsubsubcategory_id'];
            }
        }

        // ========================================
        // REQUÊTE INSERT avec TOUS les champs (122 paramètres)
        // ========================================
        $productSql = "
            INSERT INTO adjame_products (
                name, slug, sku, description, category_id, subcategory_id,
                unit_price, wholesale_price, wholesale_min_qty, stock,
                status, icon, tags, description_plus, video,
                is_active, unit_type, boutique_id, created_by,
                sub_subcategory_id, sub_sub_subcategory_id,

                -- Champs Trucks (9)
                vehicle_condition, vehicle_make, vehicle_model, drive_type, vehicle_year,
                fuel_type, transmission_type, engine_brand, vehicle_mileage,

                -- Champs Trailers (17)
                trailer_type, trailer_brand, trailer_use, trailer_size, trailer_axle,
                trailer_suspension, trailer_tire, trailer_king_pin, trailer_main_beam,
                trailer_max_payload, trailer_place_of_origin, trailer_material,
                trailer_function, trailer_landing_gear, trailer_color, trailer_axle_brand,
                trailer_condition,

                -- Champs Techniques Trucks (34)
                brake_system, cabin_type, country_of_origin,
                curb_weight, dimensions, power, car_availability, engine_emissions,
                fuel_tank_capacity, gvw, other_description, payload_capacity,
                production_date, size_type, suspension_type, tyre_size, wheelbase,
                engine_number, stock_number, disponibility, speed, gearbox_brand,
                gearbox_model, chassis_dimensions, frame_rear_suspension,
                suspension_front, suspension_rear, axle_brand, axle_front,
                axle_rear, axle_speed_ratio, air_filter, electrics, cab,

                -- NOUVEAUX CHAMPS CAR (41)
                car_make, car_model, car_year, car_condition, car_vin, car_mileage,
                car_battery_range, car_charge_time_full, car_quick_charge_time, car_battery_capacity,
                car_height, car_length, car_width, car_kerb_weight, car_wheelbase,
                car_fuel_type, car_transmission, car_engine_size, car_engine_cylinders, car_drivetrain,
                car_doors, car_seats, car_warranty_miles, car_warranty_years, car_insurance_group,
                car_top_speed, car_engine_power_bhp, car_engine_power_kw, car_engine_torque, car_acceleration,
                car_exterior_color, car_interior_color, car_interior_material,
                car_mpg_city, car_mpg_highway, car_mpg_combined, car_co2_emissions,
                car_body_type, car_trim_level, car_previous_owners, car_service_history,
                car_data_source, car_vin_decoded_at, car_api_provider
            ) VALUES (
                ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?,
                ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?,
                ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?,
                ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?,
                ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?,
                ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?,
                ?, ?, ?, ?, ?
            )
        ";

        $sku = $input['sku'] ?? $this->generateSKU();
        $status = ($input['is_active'] ?? true) ? 'Actif' : 'Brouillon';

        // ========================================
        // PARAMÈTRES (122 total)
        // ========================================
        $productParams = [
            // Champs de base (15)
            $input['name'], $slug, $sku, $input['description'] ?? '',
            $input['category_id'], $input['subcategory_id'],
            $input['unit_price'], $input['wholesale_price'] ?? null, $input['wholesale_min_qty'] ?? null,
            $input['stock'], $status, $input['icon'] ?? '', $input['tags'] ?? '',
            $other_description, $video,

            // Champs système (4)
            $input['is_active'] ?? true, $input['unit_type'] ?? 'unité',
            $boutiqueId, $userId,

            // Hiérarchie catégorie (2)
            $subSubcategoryId, $subSubSubcategoryId,

            // Champs Trucks (9)
            $vehicle_condition, $vehicle_make, $vehicle_model, $drive_type, $vehicle_year,
            $fuel_type, $transmission_type, $engine_brand, $vehicle_mileage,

            // Champs Trailers (17)
            $trailer_type, $trailer_brand, $trailer_use, $trailer_size, $trailer_axle,
            $trailer_suspension, $trailer_tire, $trailer_king_pin, $trailer_main_beam,
            $trailer_max_payload, $trailer_place_of_origin, $trailer_material,
            $trailer_function, $trailer_landing_gear, $trailer_color, $trailer_axle_brand,
            $trailer_condition,

            // Champs Techniques Trucks (34)
            $brake_system, $cabin_type, $country_of_origin,
            $curb_weight, $dimensions, $input['power'] ?? null, $car_availability, $input['engine_emissions'] ?? null,
            $fuel_tank_capacity, $gvw, $other_description, $payload_capacity,
            $production_date, $size_type, $suspension_type, $tyre_size, $wheelbase,
            $engine_n, $stockNumber, $disponibility, $input['speed'] ?? null, $input['gearbox_brand'] ?? null,
            $input['gearbox_model'] ?? null, $input['chassis_dimensions'] ?? null,
            $input['frame_rear_suspension'] ?? null, $input['suspension_front'] ?? null,
            $input['suspension_rear'] ?? null, $input['axle_brand'] ?? null,
            $input['axle_front'] ?? null, $input['axle_rear'] ?? null,
            $input['axle_speed_ratio'] ?? null, $input['air_filter'] ?? null,
            $input['electrics'] ?? null, $input['cab'] ?? null,

            // NOUVEAUX CHAMPS CAR (41)
            $car_make, $car_model, $car_year, $car_condition, $car_vin, $car_mileage,
            $car_battery_range, $car_charge_time_full, $car_quick_charge_time, $car_battery_capacity,
            $car_height, $car_length, $car_width, $car_kerb_weight, $car_wheelbase,
            $car_fuel_type, $car_transmission, $car_engine_size, $car_engine_cylinders, $car_drivetrain,
            $car_doors, $car_seats, $car_warranty_miles, $car_warranty_years, $car_insurance_group,
            $car_top_speed, $car_engine_power_bhp, $car_engine_power_kw, $car_engine_torque, $car_acceleration,
            $car_exterior_color, $car_interior_color, $car_interior_material,
            $car_mpg_city, $car_mpg_highway, $car_mpg_combined, $car_co2_emissions,
            $car_body_type, $car_trim_level, $car_previous_owners, $car_service_history,
            $car_data_source, $car_vin_decoded_at, $car_api_provider
        ];

        $this->db->query($productSql, $productParams);
        $productId = $this->db->lastInsertId();

        // Ajout trim_numbers
        if (!empty($input['trim_numbers'])) {
            foreach ($input['trim_numbers'] as $trimNumber) {
                $this->db->query(
                    "INSERT INTO adjame_product_trim_numbers (product_id, engine_number) VALUES (?, ?)",
                    [$productId, $trimNumber]
                );
            }
        }

        // Ajout VIN numbers
        if (!empty($input['vin'])) {
            foreach ($input['vin'] as $vinNumber) {
                $this->db->query(
                    "INSERT INTO adjame_product_vin_numbers (product_id, engine_number) VALUES (?, ?)",
                    [$productId, $vinNumber]
                );
            }
        }

        // Couleurs
        if (!empty($input['colors'])) {
            foreach ($input['colors'] as $hexValue) {
                $existingColor = $this->db->query("SELECT id FROM adjame_colors WHERE hex_value = ?", [$hexValue]);
                if (!empty($existingColor)) {
                    $colorId = $existingColor[0]['id'];
                } else {
                    $colorName = 'Couleur ' . strtoupper(substr($hexValue, 1));
                    $this->db->query("INSERT INTO adjame_colors (name, hex_value) VALUES (?, ?)", [$colorName, $hexValue]);
                    $colorId = $this->db->lastInsertId();
                }
                $this->db->query("INSERT INTO adjame_product_colors (product_id, color_id) VALUES (?, ?)", [$productId, $colorId]);
            }
        }

        // Images
        if (!empty($input['images'])) {
            foreach ($input['images'] as $index => $image) {
                $this->db->query(
                    "INSERT INTO adjame_product_images (product_id, image_url, alt_text, is_primary, sort_order)
                    VALUES (?, ?, ?, ?, ?)",
                    [
                        $productId,
                        $image['url'],
                        $image['alt_text'] ?? $input['name'],
                        $index === 0 ? 1 : 0,
                        $index
                    ]
                );
            }
        }

        $this->db->commit();

        $createdProduct = $this->getProductByIdWithDetails($productId);
        $this->sendProductCreationEmail($createdProduct, $this->currentUser, $this->currentBoutique);

        $this->sendResponse(201, [
            'success' => true,
            'message' => 'Produit créé avec succès',
            'data' => $createdProduct
        ]);

    } catch (Exception $e) {
        $this->db->rollback();
        error_log("❌ Erreur dans createProduct: " . $e->getMessage());
        $this->sendResponse(500, ['error' => 'Erreur lors de la création: ' . $e->getMessage()]);
    }
}

private function decodeVIN() {
    $vin = $_GET['vin'] ?? '';

    if (empty($vin) || strlen($vin) !== 17) {
        $this->sendResponse(400, ['error' => 'VIN invalide. Le VIN doit contenir 17 caractères.']);
        return;
    }

    try {
        // Utiliser l'API NHTSA (gratuite, pas de clé API requise)
        $url = "https://vpic.nhtsa.dot.gov/api/vehicles/decodevinvalues/{$vin}?format=json";

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($httpCode !== 200) {
            throw new Exception('Erreur lors de la récupération des données VIN');
        }

        $data = json_decode($response, true);

        if (empty($data['Results'])) {
            throw new Exception('Aucune donnée trouvée pour ce VIN');
        }

        $result = $data['Results'][0];

        // Mapper les données de l'API aux champs de notre base
        // L'API NHTSA peut retourner des valeurs vides, on utilise trim() et vérifie
        $carData = [
            'car_make' => !empty(trim($result['Make'])) ? trim($result['Make']) : null,
            'car_model' => !empty(trim($result['Model'])) ? trim($result['Model']) : null,
            'car_year' => !empty($result['ModelYear']) ? intval($result['ModelYear']) : null,
            'car_body_type' => !empty(trim($result['BodyClass'])) ? trim($result['BodyClass']) : null,
            'car_trim_level' => !empty(trim($result['Trim'])) ? trim($result['Trim']) : null,
            'car_fuel_type' => $this->mapFuelType($result['FuelTypePrimary'] ?? ''),
            'car_transmission' => $this->mapTransmission($result['TransmissionStyle'] ?? ''),
            'car_engine_size' => $this->parseEngineSize($result['DisplacementL'] ?? ''),
            'car_engine_cylinders' => !empty($result['EngineCylinders']) ? intval($result['EngineCylinders']) : null,
            'car_drivetrain' => $this->mapDrivetrain($result['DriveType'] ?? ''),
            'car_doors' => !empty($result['Doors']) ? intval($result['Doors']) : null,
            'car_seats' => !empty($result['Seats']) ? intval($result['Seats']) : null,
            'car_vin' => $vin,
            'car_data_source' => 'api',
            'car_vin_decoded_at' => date('Y-m-d H:i:s'),
            'car_api_provider' => 'NHTSA',
        ];

        // Générer un nom automatique
        $suggestedName = '';
        if ($carData['car_year'] && $carData['car_make']) {
            $suggestedName = $carData['car_year'] . ' ' . $carData['car_make'];

            if ($carData['car_model']) {
                $suggestedName .= ' ' . $carData['car_model'];
            }

            if ($carData['car_trim_level']) {
                $suggestedName .= ' ' . $carData['car_trim_level'];
            }
        }

        if (!empty($suggestedName)) {
            $carData['suggested_name'] = $suggestedName;
        }

        $this->sendResponse(200, [
            'success' => true,
            'message' => 'VIN décodé avec succès',
            'data' => $carData,
            // Garde raw_data en mode debug pour voir toutes les données disponibles
            'raw_data' => $result
        ]);

    } catch (Exception $e) {
        $this->sendResponse(500, [
            'error' => 'Erreur lors du décodage du VIN: ' . $e->getMessage()
        ]);
    }
}

private function decodeVIN2() {
    // Set CORS headers / 设置CORS头
    header('Access-Control-Allow-Origin: *');
    header('Access-Control-Allow-Methods: POST, OPTIONS');
    header('Access-Control-Allow-Headers: Content-Type');
    header('Content-Type: application/json; charset=utf-8');
    
    // Handle preflight OPTIONS request / 处理预检OPTIONS请求
    if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
        http_response_code(200);
        exit();
    }
    
    // Only accept POST requests / 只接受POST请求
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        $this->sendResponse(405, ['error' => 'Method not allowed. Use POST.']);
        return;
    }
    
    // Get VIN from POST data / 从POST数据获取VIN
    $input = file_get_contents('php://input');
    $data = json_decode($input, true);
    
    $vin = $data['vin'] ?? '';
    
    // Validate VIN / 验证VIN
    if (empty($vin) || strlen($vin) !== 17) {
        $this->sendResponse(400, [
            'success' => false,
            'error' => 'VIN invalide. Le VIN doit contenir 17 caractères.'
        ]);
        return;
    }
    
    try {
        // Database configuration for VIN API (独立配置 configApi.php)
        $configFile = __DIR__ . '/configApi.php';
        if (!file_exists($configFile)) {
            throw new Exception('VIN API config file configApi.php not found');
        }
        $dbConfig = require $configFile;

        $db_host  = $dbConfig['host']     ?? '193.203.239.73';
        $db_name  = $dbConfig['dbname']   ?? 'sasto2574403';
        $db_user  = $dbConfig['username'] ?? '';
        $db_pass  = $dbConfig['password'] ?? '';
        $charset  = $dbConfig['charset']  ?? 'utf8mb4';

        // Connect to database
        $pdo = new PDO(
            "mysql:host=$db_host;dbname=$db_name;charset=$charset",
            $db_user,
            $db_pass,
            [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES   => false,
                PDO::ATTR_TIMEOUT            => 5,
            ]
        );

        // Check if VIN exists in database / 检查数据库中是否存在VIN
        $stmt = $pdo->prepare("SELECT * FROM vin_decoded_data WHERE vin = :vin LIMIT 1");
        $stmt->bindValue(':vin', $vin, PDO::PARAM_STR);
        $stmt->execute();
        $dbData = $stmt->fetch();
        
        if ($dbData) {
            // VIN found in database, return database data / 在数据库中找到VIN，返回数据库数据
            // Parse model_list if it's a JSON string / 如果model_list是JSON字符串，解析它
            if (!empty($dbData['model_list']) && is_string($dbData['model_list'])) {
                $decodedModelList = json_decode($dbData['model_list'], true);
                if ($decodedModelList !== null) {
                    $dbData['model_list'] = $decodedModelList;
                } else {
                    $dbData['model_list'] = null;
                }
            }
            
            $this->sendResponse(200, [
                'success' => true,
                'message' => 'VIN data retrieved from database',
                'data' => $dbData,
                'source' => 'database'
            ]);
            return;
        }
        
        // VIN not found in database, call external API / 数据库中未找到VIN，调用外部API
        // Use Tanshu API / 使用Tanshu API
        $apiKey = 'c5d10bc2b3e40f8a17998f8a5b7ce156';
        $apiUrl = "https://api.tanshuapi.com/api/vin/v2/index?key={$apiKey}&vin=" . urlencode($vin);
        
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $apiUrl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Accept: application/json'
        ]);
        
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curlError = curl_error($ch);
        curl_close($ch);
        
        if ($httpCode !== 200 || !$response) {
            throw new Exception('External API error: ' . ($curlError ?: "HTTP $httpCode"));
        }
        
        $apiData = json_decode($response, true);
        
        if (empty($apiData) || !isset($apiData['data'])) {
            throw new Exception('Invalid response from external API');
        }
        
        // Extract car data from API response / 从API响应中提取车辆数据
        $carData = $apiData['data'];
        
        // Map API response to our format (similar to fillCarDataFromAPI logic) / 将API响应映射到我们的格式（类似于fillCarDataFromAPI逻辑）
        $mappedData = [
            'vin' => $vin,
            'brand_name' => $carData['brand_name'] ?? $carData['brand'] ?? '',
            'series_name' => $carData['series_name'] ?? $carData['series'] ?? '',
            'model_name' => $carData['model_name'] ?? $carData['model'] ?? '',
            'version' => $carData['version'] ?? $carData['car_trim_level'] ?? '',
            'year' => !empty($carData['year']) ? intval($carData['year']) : null,
            'fuel_type' => $carData['fuel_type'] ?? '',
            'transmission' => $carData['transmission'] ?? '',
            'drivetrain' => $carData['drivetrain'] ?? '',
            'engine_size' => !empty($carData['engine_size']) ? floatval($carData['engine_size']) : null,
            'engine_cylinders' => !empty($carData['engine_cylinders']) ? intval($carData['engine_cylinders']) : null,
            'body_type' => $carData['body_type'] ?? '',
            'doors' => !empty($carData['doors']) ? intval($carData['doors']) : null,
            'seats' => !empty($carData['seats']) ? intval($carData['seats']) : null,
            'length' => !empty($carData['length']) ? floatval($carData['length']) : null,
            'width' => !empty($carData['width']) ? floatval($carData['width']) : null,
            'height' => !empty($carData['height']) ? floatval($carData['height']) : null,
            'wheelbase' => !empty($carData['wheelbase']) ? floatval($carData['wheelbase']) : null,
            'curb_weight' => !empty($carData['curb_weight']) ? floatval($carData['curb_weight']) : null,
            'gross_weight' => !empty($carData['gross_weight']) ? floatval($carData['gross_weight']) : null,
            'max_power' => !empty($carData['max_power']) ? floatval($carData['max_power']) : null,
            'max_torque' => !empty($carData['max_torque']) ? floatval($carData['max_torque']) : null,
            'acceleration' => !empty($carData['acceleration']) ? floatval($carData['acceleration']) : null,
            'mpg_city' => !empty($carData['mpg_city']) ? floatval($carData['mpg_city']) : null,
            'mpg_highway' => !empty($carData['mpg_highway']) ? floatval($carData['mpg_highway']) : null,
            'mpg_combined' => !empty($carData['mpg_combined']) ? floatval($carData['mpg_combined']) : null,
            'co2_emissions' => !empty($carData['co2_emissions']) ? floatval($carData['co2_emissions']) : null,
            'exterior_color' => $carData['exterior_color'] ?? '',
            'interior_color' => $carData['interior_color'] ?? '',
            'interior_material' => $carData['interior_material'] ?? '',
            'manufacturer' => $carData['manufacturer'] ?? '',
            'market_date' => $carData['market_date'] ?? null,
            'stop_date' => $carData['stop_date'] ?? null,
            'front_tyre_size' => $carData['front_tyre_size'] ?? '',
            'rear_tyre_size' => $carData['rear_tyre_size'] ?? '',
            'front_brake_type' => $carData['front_brake_type'] ?? '',
            'rear_brake_type' => $carData['rear_brake_type'] ?? '',
            'parking_brake_type' => $carData['parking_brake_type'] ?? '',
            'data_source' => 'api',
            'api_provider' => 'TanshuAPI',
            'decoded_at' => date('Y-m-d H:i:s'),
            'language' => 'zh' // API returns Chinese data / API返回中文数据
        ];
        
        // Handle model_list if present / 如果存在model_list，处理它
        if (isset($carData['model_list']) && is_array($carData['model_list'])) {
            $mappedData['model_list'] = $carData['model_list'];
        } elseif (isset($carData['model_list']) && is_object($carData['model_list'])) {
            $mappedData['model_list'] = (array)$carData['model_list'];
        }
        
        // Save mapped VIN data into vin_decoded_data table (cache for future requests)
        // 将映射后的VIN数据写入 vin_decoded_data 表，供下次直接使用
        $sql = "
            INSERT INTO vin_decoded_data (
                vin, brand_name, series_name, model_name, version, year,
                fuel_type, transmission, drivetrain,
                engine_size, engine_cylinders,
                body_type, doors, seats,
                length, width, height, wheelbase,
                curb_weight, gross_weight,
                max_power, max_torque, acceleration,
                mpg_city, mpg_highway, mpg_combined, co2_emissions,
                exterior_color, interior_color, interior_material,
                manufacturer, market_date, stop_date,
                front_tyre_size, rear_tyre_size,
                front_brake_type, rear_brake_type, parking_brake_type,
                data_source, api_provider, decoded_at, language, model_list
            ) VALUES (
                :vin, :brand_name, :series_name, :model_name, :version, :year,
                :fuel_type, :transmission, :drivetrain,
                :engine_size, :engine_cylinders,
                :body_type, :doors, :seats,
                :length, :width, :height, :wheelbase,
                :curb_weight, :gross_weight,
                :max_power, :max_torque, :acceleration,
                :mpg_city, :mpg_highway, :mpg_combined, :co2_emissions,
                :exterior_color, :interior_color, :interior_material,
                :manufacturer, :market_date, :stop_date,
                :front_tyre_size, :rear_tyre_size,
                :front_brake_type, :rear_brake_type, :parking_brake_type,
                :data_source, :api_provider, :decoded_at, :language, :model_list
            )
            ON DUPLICATE KEY UPDATE
                brand_name = VALUES(brand_name),
                series_name = VALUES(series_name),
                model_name = VALUES(model_name),
                version = VALUES(version),
                year = VALUES(year),
                fuel_type = VALUES(fuel_type),
                transmission = VALUES(transmission),
                drivetrain = VALUES(drivetrain),
                engine_size = VALUES(engine_size),
                engine_cylinders = VALUES(engine_cylinders),
                body_type = VALUES(body_type),
                doors = VALUES(doors),
                seats = VALUES(seats),
                length = VALUES(length),
                width = VALUES(width),
                height = VALUES(height),
                wheelbase = VALUES(wheelbase),
                curb_weight = VALUES(curb_weight),
                gross_weight = VALUES(gross_weight),
                max_power = VALUES(max_power),
                max_torque = VALUES(max_torque),
                acceleration = VALUES(acceleration),
                mpg_city = VALUES(mpg_city),
                mpg_highway = VALUES(mpg_highway),
                mpg_combined = VALUES(mpg_combined),
                co2_emissions = VALUES(co2_emissions),
                exterior_color = VALUES(exterior_color),
                interior_color = VALUES(interior_color),
                interior_material = VALUES(interior_material),
                manufacturer = VALUES(manufacturer),
                market_date = VALUES(market_date),
                stop_date = VALUES(stop_date),
                front_tyre_size = VALUES(front_tyre_size),
                rear_tyre_size = VALUES(rear_tyre_size),
                front_brake_type = VALUES(front_brake_type),
                rear_brake_type = VALUES(rear_brake_type),
                parking_brake_type = VALUES(parking_brake_type),
                data_source = VALUES(data_source),
                api_provider = VALUES(api_provider),
                decoded_at = VALUES(decoded_at),
                language = VALUES(language),
                model_list = VALUES(model_list)
        ";

        $stmtSave = $pdo->prepare($sql);
        $stmtSave->bindValue(':vin', $mappedData['vin'], PDO::PARAM_STR);
        $stmtSave->bindValue(':brand_name', $mappedData['brand_name'] ?? null, PDO::PARAM_STR);
        $stmtSave->bindValue(':series_name', $mappedData['series_name'] ?? null, PDO::PARAM_STR);
        $stmtSave->bindValue(':model_name', $mappedData['model_name'] ?? null, PDO::PARAM_STR);
        $stmtSave->bindValue(':version', $mappedData['version'] ?? null, PDO::PARAM_STR);
        $stmtSave->bindValue(':year', isset($mappedData['year']) ? $mappedData['year'] : null, PDO::PARAM_INT);
        $stmtSave->bindValue(':fuel_type', $mappedData['fuel_type'] ?? null, PDO::PARAM_STR);
        $stmtSave->bindValue(':transmission', $mappedData['transmission'] ?? null, PDO::PARAM_STR);
        $stmtSave->bindValue(':drivetrain', $mappedData['drivetrain'] ?? null, PDO::PARAM_STR);
        $stmtSave->bindValue(':engine_size', isset($mappedData['engine_size']) ? $mappedData['engine_size'] : null, PDO::PARAM_STR);
        $stmtSave->bindValue(':engine_cylinders', isset($mappedData['engine_cylinders']) ? $mappedData['engine_cylinders'] : null, PDO::PARAM_INT);
        $stmtSave->bindValue(':body_type', $mappedData['body_type'] ?? null, PDO::PARAM_STR);
        $stmtSave->bindValue(':doors', isset($mappedData['doors']) ? $mappedData['doors'] : null, PDO::PARAM_INT);
        $stmtSave->bindValue(':seats', isset($mappedData['seats']) ? $mappedData['seats'] : null, PDO::PARAM_INT);
        $stmtSave->bindValue(':length', isset($mappedData['length']) ? $mappedData['length'] : null, PDO::PARAM_STR);
        $stmtSave->bindValue(':width', isset($mappedData['width']) ? $mappedData['width'] : null, PDO::PARAM_STR);
        $stmtSave->bindValue(':height', isset($mappedData['height']) ? $mappedData['height'] : null, PDO::PARAM_STR);
        $stmtSave->bindValue(':wheelbase', isset($mappedData['wheelbase']) ? $mappedData['wheelbase'] : null, PDO::PARAM_STR);
        $stmtSave->bindValue(':curb_weight', isset($mappedData['curb_weight']) ? $mappedData['curb_weight'] : null, PDO::PARAM_STR);
        $stmtSave->bindValue(':gross_weight', isset($mappedData['gross_weight']) ? $mappedData['gross_weight'] : null, PDO::PARAM_STR);
        $stmtSave->bindValue(':max_power', isset($mappedData['max_power']) ? $mappedData['max_power'] : null, PDO::PARAM_STR);
        $stmtSave->bindValue(':max_torque', isset($mappedData['max_torque']) ? $mappedData['max_torque'] : null, PDO::PARAM_STR);
        $stmtSave->bindValue(':acceleration', isset($mappedData['acceleration']) ? $mappedData['acceleration'] : null, PDO::PARAM_STR);
        $stmtSave->bindValue(':mpg_city', isset($mappedData['mpg_city']) ? $mappedData['mpg_city'] : null, PDO::PARAM_STR);
        $stmtSave->bindValue(':mpg_highway', isset($mappedData['mpg_highway']) ? $mappedData['mpg_highway'] : null, PDO::PARAM_STR);
        $stmtSave->bindValue(':mpg_combined', isset($mappedData['mpg_combined']) ? $mappedData['mpg_combined'] : null, PDO::PARAM_STR);
        $stmtSave->bindValue(':co2_emissions', isset($mappedData['co2_emissions']) ? $mappedData['co2_emissions'] : null, PDO::PARAM_STR);
        $stmtSave->bindValue(':exterior_color', $mappedData['exterior_color'] ?? null, PDO::PARAM_STR);
        $stmtSave->bindValue(':interior_color', $mappedData['interior_color'] ?? null, PDO::PARAM_STR);
        $stmtSave->bindValue(':interior_material', $mappedData['interior_material'] ?? null, PDO::PARAM_STR);
        $stmtSave->bindValue(':manufacturer', $mappedData['manufacturer'] ?? null, PDO::PARAM_STR);
        $stmtSave->bindValue(':market_date', $mappedData['market_date'] ?? null, PDO::PARAM_STR);
        $stmtSave->bindValue(':stop_date', $mappedData['stop_date'] ?? null, PDO::PARAM_STR);
        $stmtSave->bindValue(':front_tyre_size', $mappedData['front_tyre_size'] ?? null, PDO::PARAM_STR);
        $stmtSave->bindValue(':rear_tyre_size', $mappedData['rear_tyre_size'] ?? null, PDO::PARAM_STR);
        $stmtSave->bindValue(':front_brake_type', $mappedData['front_brake_type'] ?? null, PDO::PARAM_STR);
        $stmtSave->bindValue(':rear_brake_type', $mappedData['rear_brake_type'] ?? null, PDO::PARAM_STR);
        $stmtSave->bindValue(':parking_brake_type', $mappedData['parking_brake_type'] ?? null, PDO::PARAM_STR);
        $stmtSave->bindValue(':data_source', $mappedData['data_source'] ?? 'api', PDO::PARAM_STR);
        $stmtSave->bindValue(':api_provider', $mappedData['api_provider'] ?? 'TanshuAPI', PDO::PARAM_STR);
        $stmtSave->bindValue(':decoded_at', $mappedData['decoded_at'] ?? date('Y-m-d H:i:s'), PDO::PARAM_STR);
        $stmtSave->bindValue(':language', $mappedData['language'] ?? 'zh', PDO::PARAM_STR);

        $modelListJson = null;
        if (isset($mappedData['model_list'])) {
            $modelListJson = json_encode($mappedData['model_list'], JSON_UNESCAPED_UNICODE);
        }
        $stmtSave->bindValue(':model_list', $modelListJson, PDO::PARAM_STR);

        $stmtSave->execute();
        
        // Return API data (without saving to database) / 返回API数据（不保存到数据库）
        $this->sendResponse(200, [
            'success' => true,
            'message' => 'VIN data retrieved from external API',
            'data' => $mappedData,
            'source' => 'api',
            'raw_data' => $carData // Include raw data for reference / 包含原始数据以供参考
        ]);
        
    } catch (PDOException $e) {
        error_log("Database error in decodeVIN2: " . $e->getMessage());
        $this->sendResponse(500, [
            'success' => false,
            'error' => 'Database error: ' . $e->getMessage()
        ]);
    } catch (Exception $e) {
        error_log("Error in decodeVIN2: " . $e->getMessage());
        $this->sendResponse(500, [
            'success' => false,
            'error' => 'Error decoding VIN: ' . $e->getMessage()
        ]);
    }
}

// Fonctions helper pour mapper les données (CORRIGÉES)
private function mapFuelType($fuel) {
    if (empty(trim($fuel))) return null;

    $fuel = strtolower(trim($fuel));

    if (strpos($fuel, 'gasoline') !== false || strpos($fuel, 'gas') !== false) {
        return 'Gasoline';
    } else if (strpos($fuel, 'diesel') !== false) {
        return 'Diesel';
    } else if (strpos($fuel, 'electric') !== false) {
        return 'Electric';
    } else if (strpos($fuel, 'flex') !== false || strpos($fuel, 'hybrid') !== false) {
        return 'Hybrid';
    } else if (strpos($fuel, 'hydrogen') !== false) {
        return 'Hydrogen';
    }

    return null;
}

private function mapTransmission($trans) {
    if (empty(trim($trans))) return null;

    $trans = strtolower(trim($trans));

    if (strpos($trans, 'auto') !== false || strpos($trans, 'cvt') !== false) {
        return 'Automatic';
    } else if (strpos($trans, 'manual') !== false) {
        return 'Manual';
    } else if (strpos($trans, 'semi') !== false) {
        return 'Semi-Automatic';
    }

    return null;
}

private function mapDrivetrain($drive) {
    if (empty(trim($drive))) return null;

    $drive = trim($drive);

    $map = [
        'FWD' => 'FWD',
        'RWD' => 'RWD',
        'AWD' => 'AWD',
        '4WD' => '4WD',
        '4X4' => '4WD',
        'Four Wheel Drive' => '4WD',
        'All Wheel Drive' => 'AWD',
        'Front Wheel Drive' => 'FWD',
        'Rear Wheel Drive' => 'RWD'
    ];

    // Recherche exacte
    if (isset($map[$drive])) {
        return $map[$drive];
    }

    // Recherche partielle
    $driveLower = strtolower($drive);
    if (strpos($driveLower, 'front') !== false) return 'FWD';
    if (strpos($driveLower, 'rear') !== false) return 'RWD';
    if (strpos($driveLower, 'all') !== false) return 'AWD';
    if (strpos($driveLower, '4wd') !== false || strpos($driveLower, 'four') !== false) return '4WD';

    return null;
}

private function parseEngineSize($displacement) {
    if (empty($displacement)) return null;

    $value = floatval($displacement);
    return $value > 0 ? $value : null;
}

    private function generateStockNumber() {
        $year = date('y'); // 2 chiffres de l'année
        $month = date('m'); // 2 chiffres du mois
        $prefix = "DAQ{$year}{$month}";

        // Récupérer le dernier stock_number pour le mois courant
        $result = $this->db->query("
            SELECT stock_number 
            FROM adjame_products 
            WHERE stock_number LIKE ? 
            ORDER BY id DESC 
            LIMIT 1
        ", ["{$prefix}%"]);

        if (!empty($result) && isset($result[0]['stock_number'])) {
            // Extrait la partie numérique (4 derniers chiffres)
            $lastStock = $result[0]['stock_number'];
            $lastId = (int) substr($lastStock, -4);
        } else {
            $lastId = 0;
        }

        // Incrémentation
        $newId = $lastId + 1;

        // Formate avec 4 chiffres minimum (0001, 0002, ...)
        $idPadded = str_pad($newId, 4, '0', STR_PAD_LEFT);

        return "{$prefix}{$idPadded}";
    }



    private function subscribeToPremium() {
        try {
            $input = json_decode(file_get_contents('php://input'), true);
    
            // Vérifie boutique_id et plan_id
            if (!isset($_GET['boutique_id']) || !isset($input['plan_id'])) {
                http_response_code(400);
                echo json_encode([
                    'status' => 'KO',
                    'message' => 'boutique_id et plan_id sont requis'
                ]);
                return;
            }
    
            $boutique_id = (int)$_GET['boutique_id'];
            $plan_id = (int)$input['plan_id'];
            $payment_method = $input['payment_method'] ?? 'card';
    
            // Récupère user_id depuis GET ou valeur par défaut
            $created_by = isset($_GET['user_id']) ? (int)$_GET['user_id'] : 1;  // 1 = ID par défaut si non fourni
    
            // Récupérer le plan depuis la base de données
            $plans = $this->db->query("SELECT * FROM adjame_premium_plans WHERE id = ? AND is_active = 1", [$plan_id]);
            $plan = $plans[0] ?? null;
    
            if (!$plan) {
                http_response_code(400);
                echo json_encode([
                    'status' => 'KO',
                    'message' => 'Plan invalide ou inactif'
                ]);
                return;
            }
    
            $expires_at = date('Y-m-d H:i:s', strtotime('+' . $plan['duration_months'] . ' months'));
    
            // Insérer nouvel abonnement avec status "pending"
            $this->db->query("
                INSERT INTO adjame_boutique_subscriptions 
                (boutique_id, plan_id, plan_name, price, max_products, max_images, status, expires_at, created_by, created_at, updated_at)
                VALUES (?, ?, ?, ?, ?, ?, 'pending', ?, ?, NOW(), NOW())
            ", [
                $boutique_id,
                $plan['id'],
                $plan['plan_name'],
                $plan['price'],
                $plan['max_products'],
                $plan['max_images'],
                $expires_at,
                $created_by
            ]);
            $subscription_id = $this->db->lastInsertId();
    
            // Enregistrer la transaction de paiement avec status "pending"
            $transaction_id = 'TXN_' . time() . '_' . $boutique_id;
            $this->db->query("
                INSERT INTO adjame_premium_payments 
                (subscription_id, boutique_id, amount, payment_method, status, transaction_id, created_at)
                VALUES (?, ?, ?, ?, 'pending', ?, NOW())
            ", [
                $subscription_id,
                $boutique_id,
                $plan['price'],
                $payment_method,
                $transaction_id
            ]);
    
            // Réponse succès avec message utilisateur
            http_response_code(200);
            echo json_encode([
                'status' => 'OK',
                'message' => "Votre demande d'abonnement a été reçue. Un agent AliAdjame vous contactera sous 24h pour finaliser la souscription.",
                'data' => [
                    'subscription_id' => $subscription_id,
                    'plan_name' => $plan['plan_name'],
                    'expires_at' => $expires_at,
                    'transaction_id' => $transaction_id,
                    'status' => 'pending'
                ]
            ]);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode([
                'status' => 'KO',
                'message' => $e->getMessage()
            ]);
        }
    }
    
    
      
    
    /**
     * Obtenir l'abonnement Premium d'une boutique
     */
    private function getPremiumSubscription() {
        try {
            $boutique_id = $_GET['boutique_id'] ?? null;
            
            if (!$boutique_id) {
                throw new Exception('boutique_id est requis');
            }
            
            $stmt = $this->pdo->prepare("
                SELECT id, boutique_id, plan_name, price, max_products, status, expires_at, created_at, updated_at
                FROM premium_subscriptions 
                WHERE boutique_id = ? AND status = 'active' AND expires_at > NOW()
                ORDER BY created_at DESC 
                LIMIT 1
            ");
            $stmt->execute([$boutique_id]);
            $subscription = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if (!$subscription) {
                return $this->sendResponse(null);
            }
            
            return $this->sendResponse($subscription);
            
        } catch (Exception $e) {
            return $this->sendError($e->getMessage());
        }
    }
    
    /**
     * Renouveler un abonnement Premium
     */
    private function renewPremiumSubscription() {
        try {
            $input = json_decode(file_get_contents('php://input'), true);
            
            if (!isset($input['boutique_id'])) {
                throw new Exception('boutique_id est requis');
            }
            
            $boutique_id = (int)$input['boutique_id'];
            $payment_method = $input['payment_method'] ?? 'card';
            
            // Récupérer l'abonnement actuel
            $stmt = $this->pdo->prepare("
                SELECT id, plan_name, price, max_products, expires_at
                FROM premium_subscriptions 
                WHERE boutique_id = ? AND status = 'active'
                ORDER BY created_at DESC 
                LIMIT 1
            ");
            $stmt->execute([$boutique_id]);
            $current = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if (!$current) {
                throw new Exception('Aucun abonnement actif trouvé');
            }
            
            // Calculer la nouvelle date d'expiration (12 mois à partir de l'expiration actuelle)
            $new_expires_at = date('Y-m-d H:i:s', strtotime($current['expires_at'] . ' +12 months'));
            
            // Mettre à jour l'abonnement
            $stmt = $this->pdo->prepare("
                UPDATE premium_subscriptions 
                SET expires_at = ?, updated_at = NOW()
                WHERE id = ?
            ");
            $stmt->execute([$new_expires_at, $current['id']]);
            
            // Enregistrer le paiement de renouvellement
            $stmt = $this->pdo->prepare("
                INSERT INTO adjame_premium_payments 
                (subscription_id, boutique_id, amount, payment_method, status, transaction_id, created_at)
                VALUES (?, ?, ?, ?, 'completed', ?, NOW())
            ");
            $transaction_id = 'RNW_' . time() . '_' . $boutique_id;
            $stmt->execute([
                $current['id'],
                $boutique_id,
                $current['price'],
                $payment_method,
                $transaction_id
            ]);
            
            return $this->sendResponse([
                'subscription_id' => $current['id'],
                'expires_at' => $new_expires_at,
                'transaction_id' => $transaction_id
            ]);
            
        } catch (Exception $e) {
            return $this->sendError($e->getMessage());
        }
    }
    
    /**
     * Annuler un abonnement Premium
     */
    private function cancelPremiumSubscription() {
        try {
            $input = json_decode(file_get_contents('php://input'), true);
            
            if (!isset($input['boutique_id'])) {
                throw new Exception('boutique_id est requis');
            }
            
            $boutique_id = (int)$input['boutique_id'];
            $reason = $input['reason'] ?? 'Demande utilisateur';
            
            // Mettre à jour le statut de l'abonnement
            $stmt = $this->pdo->prepare("
                UPDATE premium_subscriptions 
                SET status = 'cancelled', cancellation_reason = ?, updated_at = NOW()
                WHERE boutique_id = ? AND status = 'active'
            ");
            $stmt->execute([$reason, $boutique_id]);
            
            if ($stmt->rowCount() === 0) {
                throw new Exception('Aucun abonnement actif trouvé');
            }
            
            return $this->sendResponse([
                'message' => 'Abonnement annulé avec succès',
                'cancelled_at' => date('Y-m-d H:i:s')
            ]);
            
        } catch (Exception $e) {
            return $this->sendError($e->getMessage());
        }
    }
    
    /**
     * Obtenir les plans Premium disponibles
     */
    private function getPremiumPlans() {
        try {
            $plans = [
                [
                    'id' => 'starter',
                    'name' => 'Petite Boutique',
                    'price' => 3000,
                    'max_products' => 15,
                    'duration_months' => 3,
                    'features' => [
                        'Jusqu\'à 15 produits',
                        'Gestion des produits',
                        'Support par email'
                    ]
                ],
                [
                    'id' => 'business',
                    'name' => 'Boutique Moyenne',
                    'price' => 5000,
                    'max_products' => 50,
                    'duration_months' => 3,
                    'features' => [
                        'Jusqu\'à 50 produits',
                        'Gestion des produits',
                        'Rapports détaillés',
                        'Support prioritaire'
                    ]
                ],
                [
                    'id' => 'enterprise',
                    'name' => 'Grande Boutique',
                    'price' => 10000,
                    'max_products' => 120,
                    'duration_months' => 3,
                    'features' => [
                        'Jusqu\'à 120 produits',
                        'Toutes les fonctionnalités',
                        'Rapport journalier',
                        'Support 24/7'
                    ]
                ]
            ];
            
            return $this->sendResponse(200, $plans);
            
        } catch (Exception $e) {
            return $this->sendError($e->getMessage());
        }
    }
    
    /**
     * Vérifier si une boutique peut ajouter plus de produits
     */
    private function canAddMoreProducts() {
        try {
            $boutique_id = $_GET['boutique_id'] ?? null;
            
            if (!$boutique_id) {
                throw new Exception('boutique_id est requis');
            }
            
            // Compter les produits actuels
            $stmt = $this->pdo->prepare("
                SELECT COUNT(*) as product_count 
                FROM products 
                WHERE boutique_id = ? AND deleted_at IS NULL
            ");
            $stmt->execute([$boutique_id]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            $current_count = (int)$result['product_count'];
            
            // Vérifier l'abonnement Premium
            $stmt = $this->pdo->prepare("
                SELECT max_products 
                FROM premium_subscriptions 
                WHERE boutique_id = ? AND status = 'active' AND expires_at > NOW()
                ORDER BY created_at DESC 
                LIMIT 1
            ");
            $stmt->execute([$boutique_id]);
            $subscription = $stmt->fetch(PDO::FETCH_ASSOC);
            
            $max_products = $subscription ? $subscription['max_products'] : 5; // Limite gratuite
            $can_add = $current_count < $max_products;
            
            return $this->sendResponse([
                'can_add' => $can_add,
                'current_count' => $current_count,
                'max_products' => $max_products,
                'has_premium' => (bool)$subscription
            ]);
            
        } catch (Exception $e) {
            return $this->sendError($e->getMessage());
        }
    }

    private function getTruckBrands() {
    try {
        // 1️⃣ Récupérer les marques actives
        $sqlBrands = "
            SELECT 
                id, 
                name, 
                country, 
                logo_url 
            FROM adjame_camion_marque 
            WHERE is_active = 1 
            ORDER BY name ASC
        ";
        $brands = $this->db->query($sqlBrands);

        if (!$brands || !is_array($brands)) {
            $brands = [];
        }

        $result = [];

        // 2️⃣ Pour chaque marque, récupérer les modèles
        foreach ($brands as $brand) {
            $sqlModels = "
                SELECT 
                    id, 
                    name, 
                    fuel_type 
                FROM adjame_camion_model 
                WHERE marque_id = ? 
                AND is_active = 1 
                ORDER BY name ASC
            ";
            $models = $this->db->query($sqlModels, [$brand['id']]);

            $brand['models'] = $models ?: [];
            $result[] = $brand;
        }

        // 3️⃣ Envoyer la réponse standardisée
        $this->sendResponse(200, [
            'success' => true,
            'count'   => count($result),
            'data'    => $result
        ]);

    } catch (Exception $e) {
        error_log("❌ Erreur dans getTruckBrands: " . $e->getMessage());
        $this->sendResponse(500, [
            'success' => false,
            'error'   => 'Erreur lors de la récupération des marques: ' . $e->getMessage()
        ]);
    }
}

    private function updateBoutique() {
        $data = json_decode(file_get_contents("php://input"), true);

        if (!$data || !isset($data['id'])) {
            $this->sendResponse(400, ["error" => "ID de la boutique manquant"]);
            return;
        }

        $id = $data['id'];
        $nom = $data['name'] ?? null;
        $email = $data['email'] ?? null;
        $telephone = $data['phone'] ?? null;
        $pays = $data['country'] ?? null;
        $adresse = $data['address'] ?? null;
        $description = $data['description'] ?? null;
        $logo = $data['logo'] ?? null;

        try {
            $query = "UPDATE boutiques SET 
                        nom = :nom,
                        email = :email,
                        telephone = :telephone,
                        pays = :pays,
                        adresse = :adresse,
                        description = :description,
                        logo = :logo
                    WHERE id = :id";

            $stmt = $this->db->prepare($query);
            $stmt->bindValue(":nom", $nom);
            $stmt->bindValue(":email", $email);
            $stmt->bindValue(":telephone", $telephone);
            $stmt->bindValue(":pays", $pays);
            $stmt->bindValue(":adresse", $adresse);
            $stmt->bindValue(":description", $description);
            $stmt->bindValue(":logo", $logo);
            $stmt->bindValue(":id", $id);

            if ($stmt->execute()) {
                $this->sendResponse(200, ["message" => "Boutique mise à jour avec succès"]);
            } else {
                $this->sendResponse(500, ["error" => "La mise à jour a échoué"]);
            }

        } catch (Exception $e) {
            $this->sendResponse(500, ["error" => $e->getMessage()]);
        }
    }

    
    // PUT /api/products.php?action=update&id=1&boutique_id=1
    private function updateProduct() {
    $id = $_GET['id'] ?? null;
    $boutiqueId = $_GET['boutique_id'] ?? $this->currentBoutique['id'];

    if (!$id || !$boutiqueId) {
        $this->sendResponse(400, ['error' => 'ID produit et ID boutique requis']);
        return;
    }

    // Vérifier que le produit appartient à la boutique
    if (!$this->productBelongsToBoutique($id, $boutiqueId)) {
        $this->sendResponse(403, ['error' => 'Produit non trouvé dans cette boutique']);
        return;
    }

    $input = json_decode(file_get_contents('php://input'), true);

    error_log("🔄 UpdateProduct - ID: $id, Boutique: $boutiqueId");
    error_log("📤 UpdateProduct - Input reçu: " . json_encode($input));

    // ========================================
    // Champs TRUCKS
    // ========================================
    $vehicle_condition = $input['vehicle_condition'] ?? null;
    $vehicle_make = $input['vehicle_make'] ?? null;
    $vehicle_model = $input['vehicle_model'] ?? null;
    $drive_type = $input['drive_type'] ?? null;
    $vehicle_year = $input['vehicle_year'] ?? null;
    $fuel_type = $input['fuel_type'] ?? null;
    $transmission_type = $input['transmission_type'] ?? null;
    $engine_brand = $input['engine_brand'] ?? null;
    $vehicle_mileage = $input['vehicle_mileage'] ?? null;

    // Champs techniques trucks
    $brake_system = $input['brake_system'] ?? null;
    $cabin_type = $input['cabin_type'] ?? null;
    $country_of_origin = $input['country_of_origin'] ?? null;
    $curb_weight = $input['curb_weight'] ?? null;
    $dimensions = $input['dimensions'] ?? null;
    $fuel_tank_capacity = $input['fuel_tank_capacity'] ?? null;
    $gvw = $input['gvw'] ?? null;
    $other_description = $input['other_description'] ?? null;
    $payload_capacity = $input['payload_capacity'] ?? null;
    $production_date = $input['production_date'] ?? null;
    $size_type = $input['size_type'] ?? null;
    $suspension_type = $input['suspension_type'] ?? null;
    $tyre_size = $input['tyre_size'] ?? null;
    $wheelbase = $input['wheelbase'] ?? null;

    // ========================================
    // Champs TRAILERS
    // ========================================
    $trailer_type = $input['trailer_type'] ?? null;
    $trailer_brand = $input['trailer_brand'] ?? null;
    $trailer_use = $input['trailer_use'] ?? null;
    $trailer_size = $input['trailer_size'] ?? null;
    $trailer_axle = $input['trailer_axle'] ?? null;
    $trailer_suspension = $input['trailer_suspension'] ?? null;
    $trailer_tire = $input['trailer_tire'] ?? null;
    $trailer_king_pin = $input['trailer_king_pin'] ?? null;
    $trailer_main_beam = $input['trailer_main_beam'] ?? null;
    $trailer_max_payload = $input['trailer_max_payload'] ?? null;
    $trailer_place_of_origin = $input['trailer_place_of_origin'] ?? null;
    $trailer_material = $input['trailer_material'] ?? null;
    $trailer_function = $input['trailer_function'] ?? null;
    $trailer_landing_gear = $input['trailer_landing_gear'] ?? null;
    $trailer_color = $input['trailer_color'] ?? null;
    $trailer_condition = $input['trailer_condition'] ?? null;
    $trailer_axle_brand = $input['trailer_axle_brand'] ?? null;

    // ========================================
    // NOUVEAUX CHAMPS CAR (41)
    // ========================================

    // Basic Info
    $car_make = $input['car_make'] ?? null;
    $car_model = $input['car_model'] ?? null;
    $car_year = $input['car_year'] ?? null;
    $car_condition = $input['car_condition'] ?? null;
    $car_vin = $input['car_vin'] ?? null;
    $car_mileage = $input['car_mileage'] ?? null;

    // Battery & Electric
    $car_battery_range = $input['car_battery_range'] ?? null;
    $car_charge_time_full = $input['car_charge_time_full'] ?? null;
    $car_quick_charge_time = $input['car_quick_charge_time'] ?? null;
    $car_battery_capacity = $input['car_battery_capacity'] ?? null;

    // Dimensions
    $car_height = $input['car_height'] ?? null;
    $car_length = $input['car_length'] ?? null;
    $car_width = $input['car_width'] ?? null;
    $car_kerb_weight = $input['car_kerb_weight'] ?? null;
    $car_wheelbase = $input['car_wheelbase'] ?? null;

    // Engine & Drivetrain
    $car_fuel_type = $input['car_fuel_type'] ?? null;
    $car_transmission = $input['car_transmission'] ?? null;
    $car_engine_size = $input['car_engine_size'] ?? null;
    $car_engine_cylinders = $input['car_engine_cylinders'] ?? null;
    $car_drivetrain = $input['car_drivetrain'] ?? null;

    // General
    $car_doors = $input['car_doors'] ?? null;
    $car_seats = $input['car_seats'] ?? null;
    $car_warranty_miles = $input['car_warranty_miles'] ?? null;
    $car_warranty_years = $input['car_warranty_years'] ?? null;
    $car_insurance_group = $input['car_insurance_group'] ?? null;

    // Performance
    $car_top_speed = $input['car_top_speed'] ?? null;
    $car_engine_power_bhp = $input['car_engine_power_bhp'] ?? null;
    $car_engine_power_kw = $input['car_engine_power_kw'] ?? null;
    $car_engine_torque = $input['car_engine_torque'] ?? null;
    $car_acceleration = $input['car_acceleration'] ?? null;

    // Colors & Interior
    $car_exterior_color = $input['car_exterior_color'] ?? null;
    $car_interior_color = $input['car_interior_color'] ?? null;
    $car_interior_material = $input['car_interior_material'] ?? null;

    // Efficiency
    $car_mpg_city = $input['car_mpg_city'] ?? null;
    $car_mpg_highway = $input['car_mpg_highway'] ?? null;
    $car_mpg_combined = $input['car_mpg_combined'] ?? null;
    $car_co2_emissions = $input['car_co2_emissions'] ?? null;

    // Additional
    $car_body_type = $input['car_body_type'] ?? null;
    $car_trim_level = $input['car_trim_level'] ?? null;
    $car_previous_owners = $input['car_previous_owners'] ?? null;
    $car_service_history = $input['car_service_history'] ?? null;

    // Data Source
    $car_data_source = $input['car_data_source'] ?? null;
    $car_vin_decoded_at = $input['car_vin_decoded_at'] ?? null;
    $car_api_provider = $input['car_api_provider'] ?? null;

    try {
        $this->db->beginTransaction();

        // Vérifier la sous-sous-catégorie
        $subSubcategoryId = null;
        if (!empty($input['subsubcategory_id'])) {
            $checkSubSubSql = "SELECT id FROM adjame_sub_subcategories WHERE id = ?";
            $subSubExists = $this->db->query($checkSubSubSql, [$input['subsubcategory_id']]);
            if (!empty($subSubExists)) {
                $subSubcategoryId = $input['subsubcategory_id'];
            }
        }

        // Vérifier la sous-sous-sous-catégorie
        $subSubSubcategoryId = 0;
        if (!empty($input['subsubsubcategory_id'])) {
            $checkSubSubSubSql = "SELECT id FROM adjame_sub_sub_subcategories WHERE id = ?";
            $subSubSubExists = $this->db->query($checkSubSubSubSql, [$input['subsubsubcategory_id']]);
            if (!empty($subSubSubExists)) {
                $subSubSubcategoryId = $input['subsubsubcategory_id'];
            }
        }

        // Gestion des images à supprimer
        if (!empty($input['image_remove'])) {
            foreach ($input['image_remove'] as $imageUrl) {
                $this->db->query("DELETE FROM adjame_product_images WHERE product_id = ? AND image_url = ?", [$id, $imageUrl]);
                if (strpos($imageUrl, '/uploads/') === 0) {
                    $filePath = '../' . ltrim($imageUrl, '/');
                    if (file_exists($filePath)) unlink($filePath);
                }
            }
        }

        // Gestion des images à ajouter
        if (!empty($input['image_add'])) {
            $maxSortSql = "SELECT COALESCE(MAX(sort_order), -1) as max_sort FROM adjame_product_images WHERE product_id = ?";
            $maxSortResult = $this->db->query($maxSortSql, [$id]);
            $currentMaxSort = $maxSortResult[0]['max_sort'];

            foreach ($input['image_add'] as $index => $imageUrl) {
                $isPrimary = ($currentMaxSort == -1 && $index == 0) ? 1 : 0;
                $this->db->query("
                    INSERT INTO adjame_product_images (product_id, image_url, alt_text, is_primary, sort_order)
                    VALUES (?, ?, ?, ?, ?)
                ", [
                    $id,
                    $imageUrl,
                    $input['name'] ?? 'Image produit',
                    $isPrimary,
                    $currentMaxSort + $index + 1
                ]);
            }
        }

        // Mise à jour des couleurs
        if (isset($input['colors'])) {
            $this->db->query("DELETE FROM adjame_product_colors WHERE product_id = ?", [$id]);
            foreach ($input['colors'] as $hexValue) {
                $existingColor = $this->db->query("SELECT id FROM adjame_colors WHERE hex_value = ?", [$hexValue]);
                if (!empty($existingColor)) {
                    $colorId = $existingColor[0]['id'];
                } else {
                    $colorName = 'Couleur ' . strtoupper(substr($hexValue, 1));
                    $this->db->query("INSERT INTO adjame_colors (name, hex_value) VALUES (?, ?)", [$colorName, $hexValue]);
                    $colorId = $this->db->lastInsertId();
                }
                $this->db->query("INSERT INTO adjame_product_colors (product_id, color_id) VALUES (?, ?)", [$id, $colorId]);
            }
        }

        // Mise à jour des tailles
        if (isset($input['sizes'])) {
            $this->db->query("DELETE FROM adjame_product_sizes WHERE product_id = ?", [$id]);
            foreach ($input['sizes'] as $sizeName) {
                $existingSize = $this->db->query("SELECT id FROM adjame_sizes WHERE name = ?", [$sizeName]);
                if (!empty($existingSize)) {
                    $sizeId = $existingSize[0]['id'];
                } else {
                    $this->db->query("INSERT INTO adjame_sizes (name) VALUES (?)", [$sizeName]);
                    $sizeId = $this->db->lastInsertId();
                }
                $this->db->query("INSERT INTO adjame_product_sizes (product_id, size_id) VALUES (?, ?)", [$id, $sizeId]);
            }
        }

        // Mise à jour des trim_numbers
        if (isset($input['trim_numbers'])) {
            $this->db->query("DELETE FROM adjame_product_trim_numbers WHERE product_id = ?", [$id]);
            foreach ($input['trim_numbers'] as $engineNumber) {
                $this->db->query(
                    "INSERT INTO adjame_product_trim_numbers (product_id, engine_number) VALUES (?, ?)",
                    [$id, $engineNumber]
                );
            }
        }

        // Mise à jour des vin_numbers
        if (isset($input['vin'])) {
            $this->db->query("DELETE FROM adjame_product_vin_numbers WHERE product_id = ?", [$id]);
            foreach ($input['vin'] as $vinNumber) {
                $this->db->query(
                    "INSERT INTO adjame_product_vin_numbers (product_id, engine_number) VALUES (?, ?)",
                    [$id, $vinNumber]
                );
            }
        }

        // ========================================
        // REQUÊTE UPDATE avec TOUS les champs
        // ========================================
        $updateSql = "
            UPDATE adjame_products SET
                name = ?, description = ?, category_id = ?, subcategory_id = ?,
                sub_subcategory_id = ?, sub_sub_subcategory_id = ?, unit_price = ?, stock = ?,
                unit_type = ?, wholesale_price = ?, wholesale_min_qty = ?, description_plus = ?,
                status = ?, icon = ?, tags = ?, is_active = ?,

                -- Champs Trucks
                vehicle_condition = ?, vehicle_make = ?, vehicle_model = ?, drive_type = ?,
                vehicle_year = ?, fuel_type = ?, transmission_type = ?, engine_brand = ?, vehicle_mileage = ?,

                -- Champs Techniques
                brake_system = ?, cabin_type = ?, country_of_origin = ?, curb_weight = ?, dimensions = ?,
                fuel_tank_capacity = ?, gvw = ?, car_availability = ?, other_description = ?, payload_capacity = ?,
                production_date = ?, size_type = ?, suspension_type = ?, tyre_size = ?, power = ?,
                engine_emissions = ?, wheelbase = ?,

                -- Champs Trailers
                trailer_type = ?, trailer_brand = ?, trailer_use = ?, trailer_size = ?, trailer_axle = ?,
                trailer_suspension = ?, trailer_tire = ?, trailer_king_pin = ?, trailer_main_beam = ?,
                trailer_max_payload = ?, trailer_place_of_origin = ?, trailer_material = ?,
                trailer_function = ?, trailer_landing_gear = ?, trailer_color = ?, trailer_axle_brand = ?,
                trailer_condition = ?,

                -- NOUVEAUX CHAMPS CAR
                car_make = ?, car_model = ?, car_year = ?, car_condition = ?, car_vin = ?, car_mileage = ?,
                car_battery_range = ?, car_charge_time_full = ?, car_quick_charge_time = ?, car_battery_capacity = ?,
                car_height = ?, car_length = ?, car_width = ?, car_kerb_weight = ?, car_wheelbase = ?,
                car_fuel_type = ?, car_transmission = ?, car_engine_size = ?, car_engine_cylinders = ?, car_drivetrain = ?,
                car_doors = ?, car_seats = ?, car_warranty_miles = ?, car_warranty_years = ?, car_insurance_group = ?,
                car_top_speed = ?, car_engine_power_bhp = ?, car_engine_power_kw = ?, car_engine_torque = ?, car_acceleration = ?,
                car_exterior_color = ?, car_interior_color = ?, car_interior_material = ?,
                car_mpg_city = ?, car_mpg_highway = ?, car_mpg_combined = ?, car_co2_emissions = ?,
                car_body_type = ?, car_trim_level = ?, car_previous_owners = ?, car_service_history = ?,
                car_data_source = ?, car_vin_decoded_at = ?, car_api_provider = ?,

                updated_at = CURRENT_TIMESTAMP, updated_by = ?
            WHERE id = ? AND boutique_id = ?
        ";

        $params = [
            // Base (16)
            $input['name'] ?? '',
            $input['description'] ?? '',
            $input['category_id'] ?? null,
            $input['subcategory_id'] ?? null,
            $subSubcategoryId,
            $subSubSubcategoryId,
            $input['unit_price'] ?? 0,
            $input['stock'] ?? 0,
            $input['unit_type'] ?? 'pièce',
            $input['wholesale_price'] ?? null,
            $input['wholesale_min_qty'] ?? null,
            $input['description_plus'] ?? '',
            $input['status'] ?? 'Actif',
            $input['icon'] ?? '',
            $input['tags'] ?? '',
            $input['is_active'] ?? true,

            // Trucks (9)
            $vehicle_condition,
            $vehicle_make,
            $vehicle_model,
            $drive_type,
            $vehicle_year,
            $fuel_type,
            $transmission_type,
            $engine_brand,
            $vehicle_mileage,

            // Techniques (21)
            $brake_system,
            $cabin_type,
            $country_of_origin,
            $curb_weight,
            $dimensions,
            $fuel_tank_capacity,
            $gvw,
            $input['car_availability'] ?? '',
            $other_description,
            $payload_capacity,
            $production_date,
            $size_type,
            $suspension_type,
            $tyre_size,
            $input['power'] ?? '',
            $input['engine_emissions'] ?? '',
            $wheelbase,

            // Trailers (17)
            $trailer_type,
            $trailer_brand,
            $trailer_use,
            $trailer_size,
            $trailer_axle,
            $trailer_suspension,
            $trailer_tire,
            $trailer_king_pin,
            $trailer_main_beam,
            $trailer_max_payload,
            $trailer_place_of_origin,
            $trailer_material,
            $trailer_function,
            $trailer_landing_gear,
            $trailer_color,
            $trailer_axle_brand,
            $trailer_condition,

            // CAR (41)
            $car_make, $car_model, $car_year, $car_condition, $car_vin, $car_mileage,
            $car_battery_range, $car_charge_time_full, $car_quick_charge_time, $car_battery_capacity,
            $car_height, $car_length, $car_width, $car_kerb_weight, $car_wheelbase,
            $car_fuel_type, $car_transmission, $car_engine_size, $car_engine_cylinders, $car_drivetrain,
            $car_doors, $car_seats, $car_warranty_miles, $car_warranty_years, $car_insurance_group,
            $car_top_speed, $car_engine_power_bhp, $car_engine_power_kw, $car_engine_torque, $car_acceleration,
            $car_exterior_color, $car_interior_color, $car_interior_material,
            $car_mpg_city, $car_mpg_highway, $car_mpg_combined, $car_co2_emissions,
            $car_body_type, $car_trim_level, $car_previous_owners, $car_service_history,
            $car_data_source, $car_vin_decoded_at, $car_api_provider,

            // System
            $this->currentUser['id'],
            $id,
            $boutiqueId
        ];

        $result = $this->db->query($updateSql, $params);

        $this->db->commit();

        $updatedProduct = $this->getProductByIdWithDetails($id);

        $this->sendResponse(200, [
            'success' => true,
            'message' => 'Produit mis à jour avec succès',
            'data' => $updatedProduct
        ]);

    } catch (Exception $e) {
        $this->db->rollback();
        error_log("❌ Erreur dans updateProduct: " . $e->getMessage());
        $this->sendResponse(500, ['error' => 'Erreur lors de la mise à jour: ' . $e->getMessage()]);
    }
}

        
    // DELETE /api/products.php?action=delete&id=1&boutique_id=1
    private function deleteProduct() {
        $id = $_GET['id'] ?? null;
        $boutiqueId = $_GET['boutique_id'] ?? $this->currentBoutique['id'];
        
        if (!$id || !$boutiqueId) {
            $this->sendResponse(400, ['error' => 'ID produit et ID boutique requis']);
            return;
        }
        
        // Vérifier que le produit appartient à la boutique
        if (!$this->productBelongsToBoutique($id, $boutiqueId)) {
            $this->sendResponse(403, ['error' => 'Produit non trouvé dans cette boutique']);
            return;
        }
        
        try {
            // Soft delete - marquer comme inactif
            $sql = "UPDATE adjame_products SET is_active = 0, updated_at = CURRENT_TIMESTAMP, updated_by = ? WHERE id = ? AND boutique_id = ?";
            $result = $this->db->query($sql, [$this->currentUser['id'], $id, $boutiqueId]);
            
            if ($result) {
                $this->sendResponse(200, [
                    'success' => true,
                    'message' => 'Produit supprimé avec succès'
                ]);
            } else {
                $this->sendResponse(404, ['error' => 'Produit non trouvé']);
            }
            
        } catch (Exception $e) {
            $this->sendResponse(500, ['error' => 'Erreur lors de la suppression: ' . $e->getMessage()]);
        }
    }
    
    // GET /api/products.php?action=stats&boutique_id=1
    private function getStats() {
        $period = $_GET['period'] ?? 'all';
        $boutiqueId = $_GET['boutique_id'] ?? $this->currentBoutique['id'];
        
        if (!$boutiqueId) {
            $this->sendResponse(400, ['error' => 'ID de boutique requis']);
            return;
        }
        
        $dateCondition = '';
        switch ($period) {
            case 'today':
                $dateCondition = "AND DATE(p.created_at) = CURDATE()";
                break;
            case 'week':
                $dateCondition = "AND p.created_at >= DATE_SUB(NOW(), INTERVAL 1 WEEK)";
                break;
            case 'month':
                $dateCondition = "AND p.created_at >= DATE_SUB(NOW(), INTERVAL 1 MONTH)";
                break;
        }
        
        $sql = "
            SELECT 
                COUNT(*) as total_products,
                COUNT(CASE WHEN status = 'Actif' THEN 1 END) as active_products,
                COUNT(CASE WHEN status = 'Stock faible' THEN 1 END) as low_stock_products,
                COUNT(CASE WHEN status = 'Rupture' THEN 1 END) as out_of_stock_products,
                SUM(stock) as total_stock,
                SUM(sales_count) as total_sales,
                SUM(views_count) as total_views,
                AVG(unit_price) as average_price
            FROM adjame_products p
            WHERE is_active = 1 AND boutique_id = ? $dateCondition
        ";
        
        $stats = $this->db->query($sql, [$boutiqueId])[0];
        
        // Formater les données
        foreach ($stats as $key => $value) {
            $stats[$key] = $key === 'average_price' ? round((float)$value, 2) : (int)$value;
        }
        
        $this->sendResponse(200, [
            'success' => true,
            'data' => $stats,
            'boutique_id' => $boutiqueId
        ]);
    }

    // GET /api/products.php?action=search&query=keyword
    private function searchProducts() {
    $query = $_GET['search'] ?? '';
    $boutiqueId = $_GET['boutique_id'] ?? null;
    $limit = (int)($_GET['limit'] ?? 10);
    
    if (empty($query) || strlen($query) < 2) {
        $this->sendResponse(200, [
            'success' => true,
            'data' => [],
            'message' => 'Veuillez saisir au moins 2 caractères pour la recherche'
        ]);
        return;
    }
    
    try {
        $searchTerm = "%$query%";
        $params = [$searchTerm, $searchTerm, $searchTerm, $searchTerm];
        
        // Ajouter le filtre par boutique si spécifié
        $boutiqueFilter = '';
        if ($boutiqueId) {
            $boutiqueFilter = 'AND p.boutique_id = ?';
            $params[] = $boutiqueId;
        }
        
        $sql = "
            SELECT 
                p.id,
                p.name AS product_name,
                p.unit_price,
                p.stock,
                p.status,
                p.category_id,              -- ✅ AJOUTÉ
                p.subcategory_id,           -- ✅ AJOUTÉ
                p.sub_subcategory_id,       -- ✅ AJOUTÉ (si la colonne existe)
                c.name AS category_name,
                sc.name AS subcategory_name,
                ssc.name AS subsubcategory_name,
                (SELECT image_url FROM adjame_product_images WHERE product_id = p.id AND is_primary = 1 LIMIT 1) AS primary_image,
                'product' AS result_type
            FROM adjame_products p
            LEFT JOIN adjame_categories c ON p.category_id = c.id
            LEFT JOIN adjame_subcategories sc ON p.subcategory_id = sc.id
            LEFT JOIN adjame_sub_subcategories ssc ON p.sub_subcategory_id = ssc.id
            WHERE 
                (p.name LIKE ? OR c.name LIKE ? OR sc.name LIKE ? OR ssc.name LIKE ?)
                AND p.is_active = 1
                $boutiqueFilter
            ORDER BY 
                CASE 
                    WHEN p.name LIKE ? THEN 1
                    WHEN c.name LIKE ? THEN 2
                    WHEN sc.name LIKE ? THEN 3
                    WHEN ssc.name LIKE ? THEN 4
                    ELSE 5
                END
            LIMIT ?
        ";
        
        // Ajouter les paramètres pour l'ordre de tri
        $params[] = "$query%"; // Commence par (priorité plus élevée)
        $params[] = "$query%";
        $params[] = "$query%";
        $params[] = "$query%";
        $params[] = $limit;
        
        $results = $this->db->query($sql, $params);
        
        if (!is_array($results)) {
            $results = [];
        }
        
        // Formater les résultats
        foreach ($results as &$result) {
            $result['unit_price'] = (float)$result['unit_price'];
            $result['stock'] = (int)$result['stock'];
            
            // ✅ AJOUTÉ: Convertir les IDs en entiers
            $result['category_id'] = $result['category_id'] ? (int)$result['category_id'] : null;
            $result['subcategory_id'] = $result['subcategory_id'] ? (int)$result['subcategory_id'] : null;
            $result['sub_subcategory_id'] = isset($result['sub_subcategory_id']) && $result['sub_subcategory_id'] ? (int)$result['sub_subcategory_id'] : null;
            
            $result['primary_image'] = $result['primary_image'] ?: 'https://www.svgrepo.com/show/422038/product.svg';
            
            // Mettre en évidence les termes de recherche
            $highlightedName = preg_replace('/(' . preg_quote($query, '/') . ')/i', '<strong>$1</strong>', $result['product_name']);
            $result['highlighted_name'] = $highlightedName;
            
            // Déterminer le type de correspondance
            if (stripos($result['product_name'], $query) !== false) {
                $result['match_type'] = 'product';
            } elseif (stripos($result['category_name'], $query) !== false) {
                $result['match_type'] = 'category';
            } elseif (stripos($result['subcategory_name'], $query) !== false) {
                $result['match_type'] = 'subcategory';
            } elseif (stripos($result['subsubcategory_name'], $query) !== false) {
                $result['match_type'] = 'subsubcategory';
            }
        }
        
        $this->sendResponse(200, [
            'success' => true,
            'data' => $results,
            'query' => $query,
            'count' => count($results)
        ]);
        
    } catch (Exception $e) {
        error_log("Erreur dans searchProducts: " . $e->getMessage());
        $this->sendResponse(500, [
            'success' => false,
            'error' => 'Erreur lors de la recherche: ' . $e->getMessage()
        ]);
    }
}
    
    // Méthodes utilitaires améliorées
    
    /**
     * Vérifier si un produit appartient à une boutique
     */
    private function productBelongsToBoutique($productId, $boutiqueId) {
        try {
            $sql = "SELECT id FROM adjame_products WHERE id = ? AND boutique_id = ? AND is_active = 1";
            $result = $this->db->query($sql, [$productId, $boutiqueId]);
            return !empty($result);
        } catch (Exception $e) {
            return false;
        }
    }
    
    /**
     * Récupérer un produit par ID avec tous les détails
     */
    private function getProductByIdWithDetails($id) {
        $sql = "
            SELECT 
                p.*,
                c.name as category_name,
                sc.name as subcategory_name,
                b.name as boutique_name,
                u.full_name as created_by_name,
                u.email as created_by_email
            FROM adjame_products p
            LEFT JOIN adjame_categories c ON p.category_id = c.id
            LEFT JOIN adjame_subcategories sc ON p.subcategory_id = sc.id
            LEFT JOIN adjame_boutique b ON p.boutique_id = b.id
            LEFT JOIN adjame_users u ON p.created_by = u.id
            WHERE p.id = ?
        ";
        
        $products = $this->db->query($sql, [$id]);
        return $products[0] ?? null;
    }
    
    private function generateSlug($name) {
        $slug = strtolower(trim($name));
        $slug = preg_replace('/[^a-z0-9-]/', '-', $slug);
        $slug = preg_replace('/-+/', '-', $slug);
        $slug = trim($slug, '-');
        
        // Vérifier l'unicité
        $counter = 0;
        $originalSlug = $slug;
        while ($this->slugExists($slug)) {
            $counter++;
            $slug = $originalSlug . '-' . $counter;
        }
        
        return $slug;
    }
    
    private function slugExists($slug) {
        $sql = "SELECT id FROM adjame_products WHERE slug = ? AND is_active = 1";
        $result = $this->db->query($sql, [$slug]);
        return !empty($result);
    }
    
    private function generateSKU() {
        return 'ADJ-' . strtoupper(uniqid());
    }
    
    private function incrementViews($productId) {
    $userIP = $_SERVER['REMOTE_ADDR'] ?? '';
    $userAgent = $_SERVER['HTTP_USER_AGENT'] ?? '';
    
    try {
        // Mettre à jour directement le compteur de vues au lieu d'utiliser une procédure stockée
        $sql = "UPDATE adjame_products SET views_count = views_count + 1 WHERE id = ?";
        $this->db->query($sql, [$productId]);
        
        // Optionnel : enregistrer la vue dans une table de logs si elle existe
        try {
            $logSql = "INSERT INTO adjame_product_views (product_id, user_ip, user_agent, viewed_at) 
                      VALUES (?, ?, ?, NOW())";
            $this->db->query($logSql, [$productId, $userIP, $userAgent]);
        } catch (Exception $e) {
            // Ignorer l'erreur si la table n'existe pas
            error_log("Note: Impossible d'enregistrer la vue dans le journal: " . $e->getMessage());
        }
    } catch (Exception $e) {
        error_log("Erreur lors de l'incrémentation des vues: " . $e->getMessage());
        // Ne pas propager l'erreur pour ne pas bloquer l'affichage du produit
    }
}
    
    /**
     * Envoyer l'email de création de produit avec les vraies données utilisateur
     */
    private function sendProductCreationEmail($product, $user, $boutique) {
        try {
            $userData = [
                'email' => $user['email'],
                'first_name' => explode(' ', $user['full_name'])[0],
                'last_name' => implode(' ', array_slice(explode(' ', $user['full_name']), 1))
            ];
            
            $productData = [
                'id' => $product['id'],
                'title' => $product['name'],
                'slug' => $product['slug'],
                'description' => $product['description'],
                'price' => $product['unit_price'],
                'status' => strtolower($product['status']),
                'category_name' => $product['category_name'],
                'condition' => 'new',
                'city_name' => 'Abidjan',
                'neighborhood' => $boutique['market'] ?? 'Adjamé',
                'phone' => $user['phone'] ?? '+225 XX XX XX XX',
                'image_count' => 1
            ];
            
            $this->emailService->sendProductConfirmationEmail($productData, $userData);
            
        } catch (Exception $e) {
            error_log("Erreur envoi email produit: " . $e->getMessage());
        }
    }
    
    // Méthodes existantes pour les catégories, couleurs, tailles...
    private function getCategories() {
        $sql = "
            SELECT 
                c.*,
                COUNT(p.id) as product_count
            FROM adjame_categories c
            LEFT JOIN adjame_products p ON c.id = p.category_id AND p.is_active = 1
            GROUP BY c.id
            ORDER BY c.name
        ";
    
        $categories = $this->db->query($sql);
    
        foreach ($categories as &$category) {
            $subcatSql = "
                SELECT 
                    sc.*,
                    COUNT(p.id) as product_count
                FROM adjame_subcategories sc
                LEFT JOIN adjame_products p ON sc.id = p.subcategory_id AND p.is_active = 1
                WHERE sc.category_id = ?
                GROUP BY sc.id
                ORDER BY sc.name
            ";
    
            $subcategories = $this->db->query($subcatSql, [$category['id']]);
    
            foreach ($subcategories as &$subcat) {
                $subsubSql = "
                    SELECT 
                        ssc.*,
                        COUNT(p.id) as product_count
                    FROM adjame_sub_subcategories ssc
                    LEFT JOIN adjame_products p ON ssc.id = p.sub_subcategory_id AND p.is_active = 1
                    WHERE ssc.subcategory_id = ?
                    GROUP BY ssc.id
                    ORDER BY ssc.name
                ";
    
                $sub_subcategories = $this->db->query($subsubSql, [$subcat['id']]);
    
                // 🔽 Ajouter le 4e niveau ici
                foreach ($sub_subcategories as &$sub_subcat) {
                    $subsubsubSql = "
                        SELECT 
                            sssc.*,
                            COUNT(p.id) as product_count
                        FROM adjame_sub_sub_subcategories sssc
                        LEFT JOIN adjame_products p ON sssc.id = p.sub_sub_subcategory_id AND p.is_active = 1
                        WHERE sssc.sub_subcategory_id = ?
                        GROUP BY ssc.id
                        ORDER BY ssc.name
                    ";
    
                    $sub_subcat['sub_sub_subcategories'] = $this->db->query($subsubsubSql, [$sub_subcat['id']]);
                    $sub_subcat['product_count'] = (int)$sub_subcat['product_count'];
                }
    
                $subcat['sub_subcategories'] = $sub_subcategories;
                $subcat['product_count'] = (int)$subcat['product_count'];
            }
    
            $category['subcategories'] = $subcategories;
            $category['product_count'] = (int)$category['product_count'];
        }
    
        $this->sendResponse(200, [
            'success' => true,
            'data' => $categories
        ]);
    }    
    
    
    private function getColors() {
        $sql = "SELECT * FROM adjame_colors ORDER BY name";
        $colors = $this->db->query($sql);
        
        $this->sendResponse(200, [
            'success' => true,
            'data' => $colors
        ]);
    }
    
    private function getSizes() {
        $sql = "SELECT * FROM adjame_sizes ORDER BY category, sort_order";
        $sizes = $this->db->query($sql);
        
        // Grouper par catégorie
        $groupedSizes = [];
        foreach ($sizes as $size) {
            $category = $size['category'] ?: 'general';
            if (!isset($groupedSizes[$category])) {
                $groupedSizes[$category] = [];
            }
            $groupedSizes[$category][] = $size;
        }
        
        $this->sendResponse(200, [
            'success' => true,
            'data' => $groupedSizes
        ]);
    }
    
    private function updateStock() {
        $id = $_GET['id'] ?? null;
        $input = json_decode(file_get_contents('php://input'), true);
        
        if (!$id || !isset($input['stock'])) {
            $this->sendResponse(400, ['error' => 'ID et stock requis']);
            return;
        }
        
        try {
            $sql = "CALL adjame_UpdateProductStock(?, ?, ?, ?)";
            $this->db->query($sql, [
                $id,
                $input['stock'],
                $input['reason'] ?? 'Mise à jour manuelle',
                $this->currentUser['full_name']
            ]);
            
            $this->sendResponse(200, [
                'success' => true,
                'message' => 'Stock mis à jour avec succès'
            ]);
            
        } catch (Exception $e) {
            $this->sendResponse(500, ['error' => 'Erreur lors de la mise à jour du stock: ' . $e->getMessage()]);
        }
    }
    
    private function uploadImage() {
        if (!isset($_FILES['image'])) {
            $this->sendResponse(400, ['error' => 'Aucune image fournie']);
            return;
        }
        
        $file = $_FILES['image'];
        $productId = $_POST['product_id'] ?? null;
        
        // Validation
        $allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
        if (!in_array($file['type'], $allowedTypes)) {
            $this->sendResponse(400, ['error' => 'Type de fichier non autorisé']);
            return;
        }
        
        if ($file['size'] > 10 * 1024 * 1024) { // 10MB max
            $this->sendResponse(400, ['error' => 'Fichier trop volumineux (max 10MB)']);
            return;
        }
        
        try {
            // Créer le dossier s'il n'existe pas
            $uploadDir = '../uploads/products/';
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0755, true);
            }
            
            // Générer un nom unique
            $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
            $filename = uniqid() . '_' . time() . '.' . $extension;
            $filepath = $uploadDir . $filename;
            
            if (move_uploaded_file($file['tmp_name'], $filepath)) {
                $imageUrl = '/uploads/products/' . $filename;
                
                // Enregistrer en base si product_id fourni
                if ($productId) {
                    $sql = "
                        INSERT INTO adjame_product_images (product_id, image_url, alt_text, sort_order)
                        VALUES (?, ?, ?, (SELECT COALESCE(MAX(sort_order), 0) + 1 FROM adjame_product_images WHERE product_id = ?))
                    ";
                    $this->db->query($sql, [$productId, $imageUrl, $_POST['alt_text'] ?? '', $productId]);
                }
                
                $this->sendResponse(200, [
                    'success' => true,
                    'message' => 'Image uploadée avec succès',
                    'data' => ['url' => $imageUrl]
                ]);
            } else {
                $this->sendResponse(500, ['error' => 'Erreur lors de l\'upload']);
            }
            
        } catch (Exception $e) {
            $this->sendResponse(500, ['error' => 'Erreur lors de l\'upload: ' . $e->getMessage()]);
        }
    }
    
    private function updateStatus() {
        $id = $_GET['id'] ?? null;
        $input = json_decode(file_get_contents('php://input'), true);
        
        if (!$id || !isset($input['status'])) {
            $this->sendResponse(400, ['error' => 'ID et statut requis']);
            return;
        }
        
        try {
            $sql = "UPDATE adjame_products SET status = ?, updated_at = CURRENT_TIMESTAMP, updated_by = ? WHERE id = ?";
            $result = $this->db->query($sql, [$input['status'], $this->currentUser['id'], $id]);
            
            if ($result) {
                $this->sendResponse(200, [
                    'success' => true,
                    'message' => 'Statut mis à jour avec succès'
                ]);
            } else {
                $this->sendResponse(404, ['error' => 'Produit non trouvé']);
            }
            
        } catch (Exception $e) {
            $this->sendResponse(500, ['error' => 'Erreur lors de la mise à jour du statut: ' . $e->getMessage()]);
        }
    }
    
    private function deleteImage() {
        $id = $_GET['id'] ?? null;
        
        if (!$id) {
            $this->sendResponse(400, ['error' => 'ID de l\'image requis']);
            return;
        }
        
        try {
            // Récupérer l'URL de l'image avant suppression
            $imageSql = "SELECT image_url FROM adjame_product_images WHERE id = ?";
            $images = $this->db->query($imageSql, [$id]);
            
            if (empty($images)) {
                $this->sendResponse(404, ['error' => 'Image non trouvée']);
                return;
            }
            
            $imageUrl = $images[0]['image_url'];
            
            // Supprimer de la base de données
            $deleteSql = "DELETE FROM adjame_product_images WHERE id = ?";
            $result = $this->db->query($deleteSql, [$id]);
            
            if ($result) {
                // Supprimer le fichier physique
                $filePath = '../' . ltrim($imageUrl, '/');
                if (file_exists($filePath)) {
                    unlink($filePath);
                }
                
                $this->sendResponse(200, [
                    'success' => true,
                    'message' => 'Image supprimée avec succès'
                ]);
            } else {
                $this->sendResponse(500, ['error' => 'Erreur lors de la suppression']);
            }
            
        } catch (Exception $e) {
            $this->sendResponse(500, ['error' => 'Erreur lors de la suppression: ' . $e->getMessage()]);
        }
    }
    
    private function sendResponse($statusCode, $data) {
        http_response_code($statusCode);
        echo json_encode($data, JSON_UNESCAPED_UNICODE);
        exit;
    }
}

// Initialiser et traiter la requête
$api = new ProductsAPI();
$api->handleRequest();
?>
