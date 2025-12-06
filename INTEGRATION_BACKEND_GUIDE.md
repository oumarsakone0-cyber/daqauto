# Guide d'intégration Backend - Système de Chat Vendeur

## Étapes d'intégration dans chat.php

### 1. Ajouter l'action dans handleGet()

Dans la méthode `handleGet()` de la classe `ChatAPI`, ajouter ce case dans le switch :

```php
private function handleGet($action) {
    switch ($action) {
        // ... autres cases existants ...

        case 'get_supplier_sessions':
            $this->getSupplierSessions();
            break;

        // ... reste du code ...
    }
}
```

### 2. Ajouter la fonction getSupplierSessions()

Copier la fonction complète depuis le fichier `GET_SUPPLIER_SESSIONS_FUNCTION.php` dans la classe `ChatAPI`.

```php
/**
 * Récupérer toutes les sessions de chat pour un vendeur/fournisseur spécifique
 */
public function getSupplierSessions() {
    // Voir le code complet dans GET_SUPPLIER_SESSIONS_FUNCTION.php
}
```

### 3. (OPTIONNEL) Ajouter le système de messages lus

Si vous voulez gérer les messages non lus :

#### a) Ajouter une colonne à la table session_messages

```sql
ALTER TABLE session_messages
ADD COLUMN is_read TINYINT(1) DEFAULT 0 AFTER created_at,
ADD COLUMN read_at DATETIME NULL AFTER is_read;
```

#### b) Ajouter l'action dans handlePost()

```php
private function handlePost($action) {
    switch ($action) {
        // ... autres cases existants ...

        case 'mark_supplier_messages_read':
            $this->markSupplierMessagesRead();
            break;

        // ... reste du code ...
    }
}
```

#### c) Ajouter la fonction markSupplierMessagesRead()

```php
public function markSupplierMessagesRead() {
    // Voir le code complet dans GET_SUPPLIER_SESSIONS_FUNCTION.php
}
```

## Modifications à faire dans la base de données

### Vérification de la structure des tables

#### Table `sessions_chat`

Assurez-vous que votre table contient au moins ces colonnes :

```sql
CREATE TABLE IF NOT EXISTS `sessions_chat` (
  `id` INT(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `product_id` INT(11) NOT NULL,
  `product_name` VARCHAR(255),
  `product_image` TEXT,
  `product_price` DECIMAL(10,2),
  `product_rating` DECIMAL(3,2),
  `supplier_id` INT(11) NOT NULL,
  `supplier_name` VARCHAR(255),
  `user_id` INT(11) NOT NULL,
  `user_email` VARCHAR(255),
  `created_at` DATETIME NOT NULL,
  `updated_at` DATETIME DEFAULT NULL,
  INDEX `idx_supplier` (`supplier_id`),
  INDEX `idx_user` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
```

#### Table `session_messages`

```sql
CREATE TABLE IF NOT EXISTS `session_messages` (
  `id` INT(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `session_id` INT(11) NOT NULL,
  `message` TEXT NOT NULL,
  `sender` ENUM('user', 'supplier', 'bot') NOT NULL,
  `product_id` VARCHAR(255) DEFAULT NULL,
  `product_price` DECIMAL(10,2) DEFAULT NULL,
  `product_image` TEXT DEFAULT NULL,
  `created_at` DATETIME NOT NULL,
  `is_read` TINYINT(1) DEFAULT 0,
  `read_at` DATETIME DEFAULT NULL,
  INDEX `idx_session` (`session_id`),
  INDEX `idx_sender` (`sender`),
  FOREIGN KEY (`session_id`) REFERENCES `sessions_chat`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
```

## Tests de l'endpoint

### Test 1 : Récupérer les sessions d'un fournisseur

```bash
# GET Request
curl -X GET "https://sastock.com/api_adjame/chat.php?action=get_supplier_sessions&supplier_id=123"
```

**Réponse attendue (succès) :**

```json
{
  "success": true,
  "total": 2,
  "sessions": [
    {
      "id": 1,
      "product_id": 456,
      "product_name": "Produit ABC",
      "product_image": "https://...",
      "product_price": 150000,
      "product_rating": 4.5,
      "supplier_id": 123,
      "supplier_name": "Ma Boutique",
      "user_id": 789,
      "user_name": "john@example.com",
      "user_email": "john@example.com",
      "created_at": "2025-01-15 10:00:00",
      "unread_count": 2,
      "messages": [
        {
          "id": 1,
          "text": "Bonjour, je suis intéressé par ce produit",
          "message": "Bonjour, je suis intéressé par ce produit",
          "sender": "user",
          "timestamp": "2025-01-15 10:30:00",
          "created_at": "2025-01-15 10:30:00",
          "product": null
        },
        {
          "id": 2,
          "text": "Bonjour ! Je suis là pour vous aider",
          "message": "Bonjour ! Je suis là pour vous aider",
          "sender": "supplier",
          "timestamp": "2025-01-15 10:32:00",
          "created_at": "2025-01-15 10:32:00",
          "product": null
        }
      ]
    }
  ]
}
```

**Réponse attendue (erreur - supplier_id manquant) :**

```json
{
  "success": false,
  "error": "supplier_id requis"
}
```

**Réponse attendue (aucune session) :**

```json
{
  "success": true,
  "total": 0,
  "sessions": []
}
```

### Test 2 : Vérifier que send_message fonctionne avec sender='supplier'

```bash
# POST Request
curl -X POST "https://sastock.com/api_adjame/chat.php?action=send_message" \
  -H "Content-Type: application/json" \
  -d '{
    "session_id": 1,
    "message": "Bonjour, comment puis-je vous aider ?",
    "sender": "supplier"
  }'
```

**Réponse attendue :**

```json
{
  "success": true,
  "message": {
    "id": 123,
    "session_id": 1,
    "message": "Bonjour, comment puis-je vous aider ?",
    "sender": "supplier",
    "product_id": "",
    "product_price": "",
    "product_image": "",
    "created_at": "2025-01-15 14:30:00"
  }
}
```

## Points importants à vérifier

### 1. Compatibilité des données

- ✅ Le champ `sender` doit accepter les valeurs : `'user'`, `'supplier'`, `'bot'`
- ✅ Les sessions doivent être identifiables par `supplier_id`
- ✅ Les messages doivent avoir à la fois `text` et `message` (ou l'un ou l'autre)
- ✅ Le timestamp doit être au format : `'Y-m-d H:i:s'`

### 2. Performance

La requête utilise un `LEFT JOIN` pour optimiser :
- ✅ Une seule requête SQL au lieu de N+1 requêtes
- ✅ Indexation sur `supplier_id` recommandée
- ✅ Indexation sur `session_id` dans `session_messages`

### 3. Sécurité

- ✅ Validation du `supplier_id`
- ✅ Protection contre les injections SQL (requêtes préparées)
- ✅ Vérification d'appartenance de la session au fournisseur (optionnel)

## Intégration dans le code existant

### Ligne à ajouter dans handleGet() (environ ligne 64)

```php
case 'get_supplier_sessions':
    $this->getSupplierSessions();
    break;
```

### Position de la nouvelle fonction

Ajouter la fonction `getSupplierSessions()` après la fonction `getSessionsChat()` (environ ligne 330).

## Dépannage

### Problème : "supplier_id requis"

**Cause :** Le paramètre `supplier_id` n'est pas envoyé dans l'URL.

**Solution :** Vérifier que l'URL est bien :
```
/chat.php?action=get_supplier_sessions&supplier_id=123
```

### Problème : Sessions retournées mais sans messages

**Cause :** Aucun message dans `session_messages` pour ces sessions.

**Solution :** Normal si les sessions viennent d'être créées. Les messages apparaîtront après le premier envoi.

### Problème : "Erreur serveur"

**Cause :** Problème de connexion à la base de données ou structure de table incorrecte.

**Solution :**
1. Vérifier les logs PHP
2. Vérifier que les tables existent
3. Vérifier les permissions sur les tables

## Vérification finale

Une fois l'intégration terminée, testez :

1. ✅ Créer une session avec `create_session_chat`
2. ✅ Envoyer un message client avec `send_message` (sender='user')
3. ✅ Récupérer les sessions avec `get_supplier_sessions`
4. ✅ Vérifier que le message apparaît dans la liste
5. ✅ Envoyer une réponse vendeur avec `send_message` (sender='supplier')
6. ✅ Vérifier que la réponse apparaît dans la conversation

## Support

Si vous rencontrez des problèmes :

1. Vérifier les logs d'erreur PHP
2. Vérifier la structure de la base de données
3. Tester avec Postman ou curl
4. Consulter le fichier `CHAT_API_REQUIREMENTS.md` pour la documentation complète
