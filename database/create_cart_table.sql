-- ========================================
-- TABLE POUR LE PANIER (CART)
-- ========================================

CREATE TABLE IF NOT EXISTS adjame_cart_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    product_id INT NOT NULL,
    boutique_id INT NOT NULL,
    quantity INT NOT NULL DEFAULT 1,

    -- Snapshot des données produit au moment de l'ajout
    product_name VARCHAR(255) NOT NULL,
    unit_price DECIMAL(10,2) NOT NULL,
    primary_image VARCHAR(500),
    slug VARCHAR(255),

    -- Snapshot des données boutique
    boutique_name VARCHAR(255),
    boutique_logo VARCHAR(500),
    boutique_marche VARCHAR(255),
    boutique_type VARCHAR(100),
    boutique_premium BOOLEAN DEFAULT 0,
    boutique_verified BOOLEAN DEFAULT 0,
    boutique_address TEXT,
    boutique_description TEXT,
    boutique_phone VARCHAR(50),

    -- Spécifications véhicule
    vehicle_make VARCHAR(100),
    vehicle_model VARCHAR(100),
    vehicle_year INT,
    vin_number VARCHAR(100),
    trim_number VARCHAR(100),
    stock_number VARCHAR(100),
    vehicle_mileage VARCHAR(50),
    fuel_type VARCHAR(50),

    -- Sélection de couleur
    color_name VARCHAR(100),
    color_hex VARCHAR(10),

    -- Métadonnées
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    -- Index pour performance
    INDEX idx_user_id (user_id),
    INDEX idx_product_id (product_id),
    INDEX idx_boutique_id (boutique_id),

    -- Contrainte unique : un utilisateur ne peut avoir qu'un seul item pour un produit + boutique + couleur donnés
    UNIQUE KEY unique_cart_item (user_id, product_id, boutique_id, color_hex),

    -- Clés étrangères
    FOREIGN KEY (user_id) REFERENCES adjame_clients(id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES adjame_products(id) ON DELETE CASCADE,
    FOREIGN KEY (boutique_id) REFERENCES adjame_boutique(id) ON DELETE CASCADE

) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Index pour améliorer les performances des requêtes
CREATE INDEX idx_cart_created_at ON adjame_cart_items(created_at);
CREATE INDEX idx_cart_updated_at ON adjame_cart_items(updated_at);
