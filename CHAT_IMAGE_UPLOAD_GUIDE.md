# 📸 Guide d'Upload d'Images dans le Chat

## ✅ Modifications Effectuées

### 1. **Composants Frontend**

#### ChatWindow.vue (Chat Client)
- ✅ Ajout du bouton d'upload d'image dans la zone de saisie
- ✅ Input file caché pour sélectionner les images
- ✅ Affichage des messages de type image avec preview
- ✅ Fonction `handleImageUpload()` pour gérer l'upload
- ✅ Fonction `openImage()` pour ouvrir l'image dans un nouvel onglet
- ✅ Intégration avec `useImageUpload` composable

#### AdminChatWindow.vue (Chat Admin)
- ✅ Ajout du bouton d'upload d'image
- ✅ Affichage des messages image
- ✅ Fonction `handleImageUpload()` pour l'admin
- ✅ Styles CSS pour les images

### 2. **Stores Pinia**

#### src/stores/chat.js (Client)
- ✅ Fonction `sendImageMessage(imageUrl, caption)` ajoutée
- ✅ Fonction `sendProductMessage(product)` ajoutée
- ✅ Exportation des deux nouvelles fonctions

#### src/stores/chatAdmin.js (Admin)
- ✅ Fonction `sendImageMessage(imageUrl, caption, supplierId)` ajoutée
- ✅ Mise à jour de `addMessageToConversation()` pour supporter `message_type`
- ✅ Mise à jour de `fetchSupplierSessions()` pour gérer les images
- ✅ Gestion du texte "Image partagée" dans lastMessage

### 3. **Composable**

#### src/composables/useImageUpload.js
- ✅ Fonction `uploadChatImage(file)` pour upload vers **Cloudinary**
- ✅ Fonction `uploadChatImageFromInput(event)` helper
- ✅ Validation des images
- ✅ Support JPEG, PNG, GIF, WebP (max 10MB)
- ✅ Upload direct vers le cloud Cloudinary
- ✅ Stockage dans le dossier `adjame/chat_images`

### 4. **Stockage Cloud**

#### Cloudinary
- ✅ Upload direct vers Cloudinary
- ✅ Configuration:
  - Cloud Name: `dqk65objc`
  - Upload Preset: `sadeal`
  - API Key: `784574272958761`
- ✅ Dossier: `adjame/chat_images`
- ✅ Noms de fichiers: `chat_{timestamp}_{filename}`
- ✅ URLs sécurisées (HTTPS)

### 5. **Migration Base de Données**

#### migration_chat_images.sql
- ⚠️ **À EXÉCUTER** sur votre base de données
- Ajoute la colonne `message_type` (ENUM: 'text', 'image', 'product')
- Ajoute `product_name`, modifie `product_image` et `product_price`

## 🚀 Utilisation

### Côté Client

1. L'utilisateur clique sur l'icône image 📷
2. Sélectionne un fichier depuis son appareil
3. L'image est uploadée automatiquement vers le serveur
4. L'URL de l'image est envoyée dans le chat
5. L'image s'affiche immédiatement dans la conversation

### Côté Admin

1. Le vendeur clique sur l'icône image 📷
2. Sélectionne une image
3. L'image est uploadée et envoyée au client
4. Les deux parties voient l'image dans le chat

## 📁 Structure des Fichiers Modifiés

```
src/
├── components/product/modals/
│   ├── ChatWindow.vue          ✅ Modifié
│   └── AdminChatWindow.vue     ✅ Modifié
├── stores/
│   ├── chat.js                 ✅ Modifié
│   └── chatAdmin.js            ✅ Modifié
├── composables/
│   └── useImageUpload.js       ✅ Modifié
upload_image.php                ✅ Créé
migration_chat_images.sql       ✅ Créé
```

## ⚙️ Configuration

### 1. Cloudinary (Déjà configuré ✅)

Les images sont automatiquement uploadées vers Cloudinary avec la configuration suivante :

```javascript
{
  uploadUrl: 'https://api.cloudinary.com/v1_1/dqk65objc/image/upload',
  uploadPreset: 'sadeal',
  apiKey: '784574272958761',
  folder: 'adjame/chat_images'
}
```

**Aucune configuration serveur nécessaire !** Les images sont stockées directement dans le cloud.

### 2. Exécuter la migration SQL

```sql
-- Connectez-vous à votre base de données
mysql -u username -p database_name < migration_chat_images.sql
```

Ou exécutez directement :

```sql
ALTER TABLE `session_messages`
ADD COLUMN IF NOT EXISTS `message_type` ENUM('text', 'image', 'product') DEFAULT 'text' AFTER `sender`;
```

## 🔧 Modifications Backend Requises

### Dans `chat.php` ou `chat_UPDATED.php`

Assurez-vous que la fonction `sendMessage()` gère bien les paramètres suivants :

```php
$messageType = $input['message_type'] ?? 'text';
$imageUrl = $input['image_url'] ?? null;
```

Et que l'insertion SQL inclut :

```php
INSERT INTO session_messages
(session_id, sender, message, message_type, created_at)
VALUES (?, ?, ?, ?, NOW())
```

## 🎨 Fonctionnalités

- ✅ Upload d'images depuis le client
- ✅ Upload d'images depuis l'admin
- ✅ Preview des images dans le chat
- ✅ Ouverture en plein écran (nouvel onglet)
- ✅ **Stockage cloud via Cloudinary** (URLs HTTPS sécurisées)
- ✅ Validation de format (JPEG, PNG, GIF, WebP)
- ✅ Limite de taille : 10MB
- ✅ Noms de fichiers uniques (`chat_{timestamp}_{filename}`)
- ✅ Affichage "Image partagée" dans la liste des conversations
- ✅ Aucun stockage serveur local nécessaire

## 🧪 Tests

### Test côté client
1. Ouvrir le chat client
2. Cliquer sur l'icône image
3. Sélectionner une image (< 5MB)
4. Vérifier que l'image s'affiche
5. Vérifier que l'image est cliquable

### Test côté admin
1. Ouvrir le dashboard admin
2. Sélectionner une conversation
3. Cliquer sur l'icône image
4. Uploader une image
5. Vérifier l'affichage et la réception côté client

## 🔒 Sécurité

- ✅ Validation du type MIME côté serveur
- ✅ Vérification de la taille du fichier
- ✅ Noms de fichiers randomisés (évite l'écrasement)
- ✅ Restriction des types de fichiers autorisés
- ✅ Headers CORS configurés

## 📝 Notes Importantes

1. **Migration BDD** : Exécutez `migration_chat_images.sql` AVANT de tester
2. **Cloudinary** : Les images sont stockées directement sur Cloudinary (pas de configuration serveur nécessaire)
3. **URLs** : Toutes les images ont des URLs HTTPS sécurisées de Cloudinary
4. **Dossier Cloud** : Les images sont dans `adjame/chat_images` sur Cloudinary
5. **Optimisation** : Cloudinary optimise automatiquement les images pour le web

## 🐛 Dépannage

### L'image ne s'uploade pas
- Vérifier la connexion Internet (upload vers Cloudinary)
- Vérifier la console du navigateur pour les erreurs
- Vérifier que le fichier est bien une image (JPEG, PNG, GIF, WebP)
- Vérifier que la taille est inférieure à 10MB

### L'image ne s'affiche pas
- Vérifier que l'URL Cloudinary retournée est correcte (commence par `https://res.cloudinary.com/`)
- Vérifier que le `message_type` est bien `'image'` dans la base de données
- Vérifier la console pour les erreurs de chargement

### Erreur d'upload Cloudinary
- Vérifier que les credentials Cloudinary sont corrects
- Vérifier que l'upload preset `sadeal` existe dans votre compte Cloudinary
- Vérifier les quotas de votre compte Cloudinary

## ✨ Prochaines Étapes (Optionnel)

- [ ] Ajouter un indicateur de progression pendant l'upload
- [ ] Permettre l'upload de plusieurs images à la fois
- [ ] Ajouter un modal de prévisualisation avant envoi
- [ ] Implémenter la suppression d'images
- [ ] Ajouter des miniatures pour économiser la bande passante
