# Intégration Rapide - Chat Vendeur

## 🚀 3 Étapes Simples

### Étape 1 : Ouvrir chat.php

Ouvrez le fichier `/api_adjame/chat.php`

### Étape 2 : Ajouter une ligne dans handleGet()

Trouvez la fonction `handleGet()` (ligne ~50) et ajoutez ce case :

```php
case 'get_supplier_sessions':
    $this->getSupplierSessions();
    break;
```

**Exemple complet :**
```php
private function handleGet($action) {
    switch ($action) {
        case 'get_user':
            $this->getUser();
            break;
        case 'get_suppliers':
            $this->getUserSuppliersWithMessages();
            break;
        // ... autres cases ...
        case 'get_sessions_chat':
            $this->getSessionsChat();
            break;

        // ⬇️ AJOUTER ICI ⬇️
        case 'get_supplier_sessions':
            $this->getSupplierSessions();
            break;
        // ⬆️ AJOUTER ICI ⬆️

        default:
            $this->sendResponse(404, ['error' => 'Action non trouvée']);
    }
}
```

### Étape 3 : Copier-coller la fonction

Trouvez la fin de la fonction `getSessionsChat()` (ligne ~330) et collez juste après :

```php
/**
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
                sm.created_at AS message_timestamp,
                sm.product_id AS message_product_id,
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
                    'timestamp' => $row['message_timestamp'],
                    'created_at' => $row['message_timestamp'],
                    'product' => null
                ];

                if ($row['message_product_id']) {
                    $message['product'] = [
                        'id' => $row['message_product_id'],
                        'price' => $row['message_product_price'],
                        'image' => $row['message_product_image']
                    ];
                }

                $sessions[$sessionId]['messages'][] = $message;

                if ($row['message_sender'] === 'user') {
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
```

## ✅ C'est terminé !

Testez avec :
```
https://sastock.com/api_adjame/chat.php?action=get_supplier_sessions&supplier_id=123
```

Remplacez `123` par un vrai ID de fournisseur.

---

## 📝 Note importante

Le système côté frontend est **déjà entièrement fonctionnel** et attend juste cet endpoint !

Une fois cette modification faite dans chat.php, le chat vendeur sera 100% opérationnel.

## 🔍 Vérification rapide

L'endpoint doit retourner quelque chose comme :

```json
{
  "success": true,
  "total": 2,
  "sessions": [
    {
      "id": 1,
      "user_name": "john@example.com",
      "supplier_id": 123,
      "messages": [...]
    }
  ]
}
```

## 📚 Documentation complète

Pour plus de détails, voir :
- `INTEGRATION_BACKEND_GUIDE.md` - Guide détaillé
- `GET_SUPPLIER_SESSIONS_FUNCTION.php` - Code source complet
- `CHAT_API_REQUIREMENTS.md` - Spécifications complètes
