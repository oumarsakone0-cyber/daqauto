# 📸 Implémentation Images et Cartes Produit dans le Chat

## 🗄️ Étape 1 : Migration de la base de données

**IMPORTANT** : Exécutez d'abord le script SQL `migration_chat_images.sql` sur votre base de données.

```sql
-- Migration pour ajouter le support des images et produits dans le chat

-- 1. Ajouter la colonne message_type
ALTER TABLE `session_messages`
ADD COLUMN IF NOT EXISTS `message_type` ENUM('text', 'image', 'product') DEFAULT 'text' AFTER `sender`;

-- 2. Ajouter la colonne product_name
ALTER TABLE `session_messages`
ADD COLUMN IF NOT EXISTS `product_name` VARCHAR(255) NULL AFTER `product_id`;

-- 3. Modifier product_image pour accepter des URLs longues
ALTER TABLE `session_messages`
MODIFY COLUMN `product_image` TEXT NULL;

-- 4. Modifier product_price pour accepter des décimales
ALTER TABLE `session_messages`
MODIFY COLUMN `product_price` DECIMAL(10,2) NULL;
```

## 📡 Étape 2 : Modifications API

### ✅ Déjà fait dans `chat_UPDATED.php` :
1. ✅ `sendMessage()` modifié pour accepter images et produits
2. ✅ `get_sessions_chat` retourne `message_type` et infos produit
3. ✅ `get_supplier_sessions` retourne `message_type` et infos produit

### 🔧 À faire dans `chat.php` sur le serveur :
Copiez les mêmes modifications de `chat_UPDATED.php` vers `chat.php` :
- La fonction `sendMessage()` complète (lignes 621-713)
- Les modifications dans `getSessionsChat()` (SELECT avec message_type)
- Les modifications dans `getSupplierSessions()` (SELECT avec message_type)

## 📤 Étape 3 : Formats d'envoi

### 1. **Message texte** (déjà fonctionnel)
```json
{
  "session_id": 123,
  "message": "Bonjour",
  "sender": "user",
  "message_type": "text"
}
```

### 2. **Message image**
```json
{
  "session_id": 123,
  "message": "Voici une image",
  "sender": "user",
  "message_type": "image",
  "image_url": "https://example.com/image.jpg"
}
```

### 3. **Carte produit**
```json
{
  "session_id": 123,
  "message": "Produit partagé",
  "sender": "bot",
  "message_type": "product",
  "product_id": 789,
  "product_name": "iPhone 15",
  "product_price": 150000,
  "product_image": "https://example.com/iphone.jpg"
}
```

## 🎨 Étape 4 : Composants Frontend

### A. Modifier `chat.js` (store client)

Ajoutez une fonction pour envoyer des produits :

```javascript
const sendProductMessage = async (product) => {
  if (!activeConversationId.value) {
    console.error("❌ Aucune conversation active")
    return
  }

  const productMessage = {
    id: Date.now(),
    message: 'Produit partagé',
    sender: 'bot',
    message_type: 'product',
    timestamp: new Date(),
    product: {
      id: product.id,
      name: product.name,
      price: product.unit_price,
      image: product.primary_image
    }
  }

  // Ajouter localement
  chatMessages.value.push(productMessage)
  addMessageToConversation(activeConversationId.value, productMessage)

  try {
    const response = await fetch('https://sastock.com/api_adjame/chat_UPDATED.php?action=send_message', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({
        session_id: activeConversationId.value,
        message: 'Produit partagé',
        sender: 'bot',
        message_type: 'product',
        product_id: product.id,
        product_name: product.name,
        product_price: product.unit_price,
        product_image: product.primary_image
      })
    })

    const data = await response.json()
    if (data.success && data.message) {
      const messageIndex = chatMessages.value.findIndex(m => m.id === productMessage.id)
      if (messageIndex !== -1) {
        chatMessages.value[messageIndex].id = data.message.id
      }
    }
  } catch (error) {
    console.error("❌ Erreur envoi produit:", error)
  }
}

// Fonction pour envoyer des images
const sendImageMessage = async (imageUrl, caption = '') => {
  if (!activeConversationId.value) {
    console.error("❌ Aucune conversation active")
    return
  }

  const imageMessage = {
    id: Date.now(),
    message: imageUrl,
    sender: 'user',
    message_type: 'image',
    timestamp: new Date()
  }

  chatMessages.value.push(imageMessage)
  addMessageToConversation(activeConversationId.value, imageMessage)

  try {
    const response = await fetch('https://sastock.com/api_adjame/chat_UPDATED.php?action=send_message', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({
        session_id: activeConversationId.value,
        message: caption || imageUrl,
        sender: 'user',
        message_type: 'image',
        image_url: imageUrl
      })
    })

    const data = await response.json()
    if (data.success && data.message) {
      const messageIndex = chatMessages.value.findIndex(m => m.id === imageMessage.id)
      if (messageIndex !== -1) {
        chatMessages.value[messageIndex].id = data.message.id
      }
    }
  } catch (error) {
    console.error("❌ Erreur envoi image:", error)
  }
}

// Exporter les nouvelles fonctions
return {
  // ... existing exports
  sendProductMessage,
  sendImageMessage
}
```

### B. Créer le composant `ProductCard.vue`

```vue
<template>
  <div class="product-card bg-white rounded-lg shadow-md p-4 max-w-sm">
    <img
      v-if="product.image"
      :src="product.image"
      :alt="product.name"
      class="w-full h-48 object-cover rounded-lg mb-3"
    />
    <h3 class="font-semibold text-lg mb-2">{{ product.name }}</h3>
    <p class="text-orange-600 font-bold text-xl mb-2">
      {{ formatPrice(product.price) }} FCFA
    </p>
    <button
      @click="viewProduct"
      class="w-full bg-orange-500 hover:bg-orange-600 text-white px-4 py-2 rounded-lg transition-colors"
    >
      Voir le produit
    </button>
  </div>
</template>

<script setup>
import { useRouter } from 'vue-router'

const props = defineProps({
  product: {
    type: Object,
    required: true
  }
})

const router = useRouter()

const formatPrice = (price) => {
  return new Intl.NumberFormat('fr-FR').format(price)
}

const viewProduct = () => {
  if (props.product.id) {
    router.push(`/product/${props.product.id}`)
  }
}
</script>
```

### C. Modifier `ChatWindow.vue` pour afficher les différents types

```vue
<template>
  <div v-for="message in messages" :key="message.id" class="message-item">
    <!-- Message texte normal -->
    <div v-if="!message.message_type || message.message_type === 'text'"
         :class="['message-bubble', message.sender === 'user' ? 'user-message' : 'supplier-message']">
      {{ message.message }}
    </div>

    <!-- Message image -->
    <div v-else-if="message.message_type === 'image'"
         :class="['message-bubble', message.sender === 'user' ? 'user-message' : 'supplier-message']">
      <img
        :src="message.message"
        alt="Image partagée"
        class="max-w-xs rounded-lg cursor-pointer"
        @click="openImage(message.message)"
      />
    </div>

    <!-- Carte produit -->
    <div v-else-if="message.message_type === 'product' && message.product">
      <ProductCard :product="message.product" />
    </div>
  </div>
</template>

<script setup>
import ProductCard from './ProductCard.vue'

const openImage = (url) => {
  window.open(url, '_blank')
}
</script>
```

### D. Ajouter un bouton pour images dans le chat

```vue
<template>
  <div class="chat-input-container">
    <!-- Bouton upload image -->
    <button
      @click="triggerFileInput"
      class="p-2 rounded-lg hover:bg-gray-100"
      title="Envoyer une image"
    >
      <svg class="w-6 h-6 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
      </svg>
    </button>

    <input
      ref="fileInput"
      type="file"
      accept="image/*"
      class="hidden"
      @change="handleImageUpload"
    />

    <input
      v-model="newMessage"
      type="text"
      placeholder="Tapez votre message..."
      @keyup.enter="sendMessage"
    />

    <button @click="sendMessage">Envoyer</button>
  </div>
</template>

<script setup>
import { ref } from 'vue'
import { useChatStore } from '@/stores/chat'

const chatStore = useChatStore()
const fileInput = ref(null)
const newMessage = ref('')

const triggerFileInput = () => {
  fileInput.value.click()
}

const handleImageUpload = async (event) => {
  const file = event.target.files[0]
  if (!file) return

  // Créer un FormData pour l'upload
  const formData = new FormData()
  formData.append('image', file)

  try {
    // Upload vers votre serveur d'images
    const response = await fetch('https://sastock.com/api_adjame/upload_image.php', {
      method: 'POST',
      body: formData
    })

    const data = await response.json()
    if (data.success && data.url) {
      // Envoyer l'image dans le chat
      await chatStore.sendImageMessage(data.url)
    }
  } catch (error) {
    console.error('Erreur upload image:', error)
  }

  // Reset input
  event.target.value = ''
}

const sendMessage = async () => {
  if (!newMessage.value.trim()) return
  await chatStore.sendMessage(newMessage.value)
  newMessage.value = ''
}
</script>
```

## 📝 Résumé des étapes

1. ✅ Exécuter le script SQL de migration
2. ✅ Copier les modifications de `chat_UPDATED.php` vers `chat.php` sur le serveur
3. ⏳ Créer le composant `ProductCard.vue`
4. ⏳ Modifier `ChatWindow.vue` et `AdminChatWindow.vue` pour afficher les différents types
5. ⏳ Ajouter les fonctions `sendProductMessage` et `sendImageMessage` dans les stores
6. ⏳ Ajouter le bouton upload image dans l'interface
7. ⏳ Créer `upload_image.php` pour gérer l'upload d'images

## 🎯 Utilisation

### Envoyer un produit dans le chat :
```javascript
const product = {
  id: 123,
  name: 'iPhone 15',
  unit_price: 150000,
  primary_image: 'https://...'
}

chatStore.sendProductMessage(product)
```

### Envoyer une image :
L'utilisateur clique sur le bouton image, sélectionne un fichier, et le système l'uploade puis l'envoie automatiquement.
