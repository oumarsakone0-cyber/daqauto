# API Requirements pour le Chat Vendeur/Admin

## Endpoints nécessaires dans `chat.php`

### 1. **get_supplier_sessions** (NOUVEAU - À IMPLÉMENTER)

Récupère toutes les sessions de chat pour un vendeur spécifique.

**Request:**
```
GET /api_adjame/chat.php?action=get_supplier_sessions&supplier_id={supplier_id}&_={timestamp}
```

**Parameters:**
- `supplier_id` (required): ID du fournisseur/vendeur
- `_` (optional): Timestamp pour éviter le cache

**Response Success:**
```json
{
  "success": true,
  "sessions": [
    {
      "id": 1,
      "user_id": 123,
      "user_name": "John Doe",
      "user_email": "john@example.com",
      "supplier_id": 456,
      "supplier_name": "Ma Boutique",
      "product_id": 789,
      "product_name": "Product Name",
      "product_image": "https://...",
      "created_at": "2025-01-15 10:30:00",
      "unread_count": 2,
      "messages": [
        {
          "id": 1,
          "text": "Bonjour",
          "message": "Bonjour",
          "sender": "user",
          "timestamp": "2025-01-15 10:30:00",
          "created_at": "2025-01-15 10:30:00",
          "product": null
        },
        {
          "id": 2,
          "text": "Bonjour, comment puis-je vous aider?",
          "message": "Bonjour, comment puis-je vous aider?",
          "sender": "supplier",
          "timestamp": "2025-01-15 10:31:00",
          "created_at": "2025-01-15 10:31:00",
          "product": null
        }
      ]
    }
  ]
}
```

**Response Error:**
```json
{
  "success": false,
  "error": "Supplier ID required"
}
```

### 2. **send_message** (EXISTANT - VÉRIFIER COMPATIBILITÉ)

Envoie un message dans une session de chat.

**Request:**
```
POST /api_adjame/chat.php?action=send_message
Content-Type: application/json

{
  "session_id": 1,
  "message": "Message text",
  "sender": "supplier"  // ⚠️ Assurez-vous que "supplier" est supporté
}
```

**Response Success:**
```json
{
  "success": true,
  "message": {
    "id": 123,
    "text": "Message text",
    "sender": "supplier",
    "timestamp": "2025-01-15 10:35:00"
  }
}
```

### 3. **get_sessions_chat** (EXISTANT - POUR CLIENT)

Utilisé côté client pour récupérer ses sessions.

**Request:**
```
GET /api_adjame/chat.php?action=get_sessions_chat&user_id={user_id}&_={timestamp}
```

## Modifications à faire dans la base de données

### Table `sessions_chat`

Assurez-vous que la table contient les colonnes suivantes:

```sql
- id (PRIMARY KEY)
- user_id (ID du client)
- user_email (Email du client)
- supplier_id (ID du vendeur/fournisseur)
- supplier_name (Nom de la boutique)
- product_id (ID du produit initial)
- product_name (Nom du produit)
- product_image (Image du produit)
- product_price (Prix du produit)
- product_rating (Note du produit)
- created_at (Date de création)
- updated_at (Date de mise à jour)
```

### Table `session_messages`

```sql
- id (PRIMARY KEY)
- session_id (Foreign key vers sessions_chat)
- message/text (Texte du message) - les deux colonnes peuvent exister
- sender (enum: 'user', 'supplier', 'bot')
- timestamp/created_at (Date du message)
- product (JSON optionnel pour les messages produits)
- read_at (Date de lecture - optionnel)
```

## Logique métier importante

### Groupement par vendeur (côté client)

Le système côté client groupe maintenant **toutes les conversations avec le même vendeur** dans une seule session, peu importe le produit. Cela signifie que:

1. Quand un client clique sur "Chat now" pour un produit:
   - Le système vérifie si une session existe déjà avec ce vendeur
   - Si oui, il réutilise cette session
   - Si non, il crée une nouvelle session

2. Un message "carte produit" est envoyé à chaque fois qu'on ouvre le chat depuis un nouveau produit

### Côté vendeur/admin

Le vendeur voit:
- Une liste de toutes ses conversations avec les clients
- Chaque conversation affiche le dernier message
- Le nombre de messages non lus par conversation
- Les informations du client (nom, avatar)
- Le produit concerné par la conversation

## Endpoints existants utilisés

### create_session_chat
```
POST /api_adjame/chat.php?action=create_session_chat
```
Utilisé pour créer une nouvelle session de chat.

### get_session_messages
```
GET /api_adjame/chat.php?action=get_session_messages&session_id={id}
```
Récupère les messages d'une session spécifique.

## Notes d'implémentation

1. **Polling**: Les deux interfaces (client et admin) utilisent un polling toutes les 2-3 secondes pour récupérer les nouveaux messages

2. **Senders**: Trois types de sender possibles:
   - `user`: Le client
   - `supplier`: Le vendeur/admin
   - `bot`: Messages automatiques du système

3. **Messages produits**: Les messages de type "product" contiennent un JSON avec les infos du produit:
   ```json
   {
     "name": "Product Name",
     "price": 150000,
     "image": "https://...",
     "shop": "Boutique Name",
     "rating": 4.5
   }
   ```

4. **Compteur de non-lus**:
   - Côté client: géré dans le store `chat.js`
   - Côté admin: géré dans le store `chatAdmin.js`
   - Le compteur doit être mis à jour quand une conversation devient active

## Fichiers créés/modifiés

### Nouveaux fichiers:
- `src/stores/chatAdmin.js` - Store Pinia pour le chat admin
- `src/components/product/modals/AdminChatWindow.vue` - Interface de chat admin
- `CHAT_API_REQUIREMENTS.md` - Ce document

### Fichiers modifiés:
- `src/stores/chat.js` - Ajout du groupement par vendeur
- `src/components/product/modals/ChatWindow.vue` - Fix d'affichage des messages
- `src/components/product/modals/ChatModal2.vue` - Fix d'affichage des messages
- `src/components/product/MesCommandes.vue` - Bouton "Contact the seller"
- `src/components/views/Messages-management.vue` - Page de gestion des messages admin
- `src/components/views/commandes-management.vue` - Bouton "Chat" sur les lignes de commandes

## Routes

- **Client**: Chat accessible via les icônes flottantes (mobile & desktop)
- **Admin**: `/dashboard-admin/messages` - Page dédiée avec interface complète
