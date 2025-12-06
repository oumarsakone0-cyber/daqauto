-- Migration pour ajouter le support des images et produits dans le chat
-- Exécutez ce script SQL sur votre base de données

-- 1. Vérifier et ajouter la colonne message_type si elle n'existe pas
ALTER TABLE `session_messages`
ADD COLUMN IF NOT EXISTS `message_type` ENUM('text', 'image', 'product') DEFAULT 'text' AFTER `sender`;

-- 2. Vérifier et ajouter la colonne product_name si elle n'existe pas
ALTER TABLE `session_messages`
ADD COLUMN IF NOT EXISTS `product_name` VARCHAR(255) NULL AFTER `product_id`;

-- 3. Modifier product_image pour accepter des URLs longues (si nécessaire)
ALTER TABLE `session_messages`
MODIFY COLUMN `product_image` TEXT NULL;

-- 4. Modifier product_price pour accepter des décimales
ALTER TABLE `session_messages`
MODIFY COLUMN `product_price` DECIMAL(10,2) NULL;

-- 5. Vérifier la structure finale
DESCRIBE `session_messages`;
