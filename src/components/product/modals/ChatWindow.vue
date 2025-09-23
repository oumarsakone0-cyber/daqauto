<template>
  <!-- Chat Window Desktop (petite fenêtre comme Facebook) -->
   
  <div class="chat-window-desktop" v-if="isOpen">
     <div class=" inset-0  opacity-10">
      <div class="absolute top-0 left-0 w-70 h-70 bg-white rounded-full -translate-x-48 -translate-y-48"></div>
      </div>
      <div class="chat-header bg-gradient-to-br">
        
      <div class="supplier-info">
        <img src="https://cdn-icons-png.flaticon.com/512/4526/4526832.png" alt="supplier" class="supplier-logo" />
        <div class="supplier-details">
          <h3>{{ supplier.name }}</h3>
          <span class="status-indicator">En ligne</span>
        </div>
      </div>
      <div class="chat-actions">
        <button v-if="isMinimized" @click="toggleMinimize" class="minimize-btn">
         <svg width="16" height="16" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3" fill="none">
            <rect x="6" y="6" width="16" height="12" />
          </svg>
        </button>
        <button v-else-if="!isMinimized" @click="toggleMinimize" class="minimize-btn">
          <svg width="16" height="16" viewBox="0 0 24 24"  stroke="currentColor" stroke-width="4" fill="none">
            <line x1="5" y1="12" x2="19" y2="12"/>
          </svg>
        </button>
        <button  @click="$emit('close')" class="close-btn">
          <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="4">
            <line x1="18" y1="6" x2="6" y2="18"/>
            <line x1="6" y1="6" x2="18" y2="18"/>
          </svg>
        </button>
      </div>
   
    </div>
    

    <div class="chat-body" v-show="!isMinimized">
      <div class="messages-container" ref="messagesContainer">
        <div 
          v-for="message in persistedMessages" 
          :key="message.id"
          :class="['message', message.sender === 'user' ? 'user-message' : 'bot-message']"
        >
          <!-- Message produit avec disposition spéciale -->
          <div v-if="message.type === 'product'" class="product-message-card">
            <div class="product-image-container">
              <img :src="getProductData(message).image || '/placeholder.svg?height=80&width=80'" 
                   :alt="getProductData(message).name" 
                   class="product-image" />
            </div>
            <div class="product-details">
              <h4 class="product-name">{{ getProductData(message).name }}</h4>
              <p class="product-shop">{{ getProductData(message).shop }}</p>
              <div class="product-price-rating">
                <span class="product-price">{{ formatPrice(getProductData(message).price) }}</span>
                <div v-if="getProductData(message).rating" class="product-rating">
                  <span class="rating-stars">⭐</span>
                  <span class="rating-value">{{ getProductData(message).rating }}</span>
                </div>
              </div>
            </div>
          </div>
          
          <!-- Message normal -->
          <div v-else class="message-content">
            <p>{{ message.message }}</p>
            <span class="message-time">
              {{ formatTime(message.timestamp) }}
            </span>
          </div>
        </div>
      </div>

      <div class="message-input-container">
        <div class="input-wrapper">
          <input
            v-model="newMessage"
            @keypress.enter="sendMessage"
            type="text"
            placeholder="Tapez votre message..."
            class="input-style"
          />
          <button @click="sendMessage" class="send-btn" :disabled="!newMessage.trim()">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <line x1="22" y1="2" x2="11" y2="13"/>
              <polygon points="22,2 15,22 11,13 2,9 22,2"/>
            </svg>
          </button>
        </div>
      </div>

      <!-- Ajout de boutons de test pour démonstration -->
      <div class="test-actions">
        <button @click="addTestProduct" class="test-btn">
          🛍️ Ajouter Produit Test
        </button>
        <button @click="clearAllMessages" class="test-btn clear-btn">
          🗑️ Vider le Chat
        </button>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, nextTick, watch, onMounted } from 'vue'

const props = defineProps({
  isOpen: {
    type: Boolean,
    default: false
  },
  supplier: {
    type: Object,
    required: true
  },
  chatMessages: {
    type: Array,
    default: () => []
  }
})

const emit = defineEmits(['close', 'send-message'])

const newMessage = ref('')
const isMinimized = ref(false)
const messagesContainer = ref(null)
const persistedMessages = ref([])

const quickActions = ref([
  { id: 1, text: '👋 Salut!' },
  { id: 2, text: '💰 Prix?' },
  { id: 3, text: '📦 Livraison?' },
  { id: 4, text: '❓ Question' }
])

const getStorageKey = () => `chat_messages_${props.supplier.id || 'default'}`

const saveMessagesToStorage = (messages) => {
  try {
    localStorage.setItem(getStorageKey(), JSON.stringify(messages))
  } catch (error) {
    console.error('Erreur lors de la sauvegarde des messages:', error)
  }
}

const loadMessagesFromStorage = () => {
  try {
    const stored = localStorage.getItem(getStorageKey())
    return stored ? JSON.parse(stored) : []
  } catch (error) {
    console.error('Erreur lors du chargement des messages:', error)
    return []
  }
}

const mergeMessages = () => {
  const storedMessages = loadMessagesFromStorage()
  const allMessages = [...storedMessages]
  
  // Ajouter les nouveaux messages des props s'ils n'existent pas déjà
  props.chatMessages.forEach(propMessage => {
    const exists = allMessages.some(msg => msg.id === propMessage.id)
    if (!exists) {
      allMessages.push(propMessage)
    }
  })
  
  // Trier par timestamp
  allMessages.sort((a, b) => new Date(a.timestamp) - new Date(b.timestamp))
  
  persistedMessages.value = allMessages
  saveMessagesToStorage(allMessages)
}

onMounted(() => {
  mergeMessages()
})

watch(() => props.chatMessages, () => {
  mergeMessages()
}, { deep: true })

watch(persistedMessages, (newMessages) => {
  saveMessagesToStorage(newMessages)
}, { deep: true })

const toggleMinimize = () => {
  isMinimized.value = !isMinimized.value
}

const sendMessage = () => {
  if (!newMessage.value.trim()) return
  
  const message = {
    id: Date.now(),
    message: newMessage.value,
    sender: 'user',
    timestamp: new Date()
  }
  
  persistedMessages.value.push(message)
  emit('send-message', message)
  newMessage.value = ''
}

const sendQuickAction = (action) => {
  const message = {
    id: Date.now(),
    message: action.text,
    sender: 'user',
    timestamp: new Date()
  }
  
  persistedMessages.value.push(message)
  emit('send-message', message)
}

const addTestProduct = () => {
  const productData = {
    name: "iPhone 15 Pro Max",
    price: 1299,
    image: "https://images.unsplash.com/photo-1592750475338-74b7b21085ab?w=400",
    shop: "Apple Store",
    rating: 4.8
  }
  
  const message = {
    id: Date.now(),
    message: JSON.stringify(productData),
    sender: 'bot',
    type: 'product',
    timestamp: new Date()
  }
  
  persistedMessages.value.push(message)
}

const clearAllMessages = () => {
  persistedMessages.value = []
  localStorage.removeItem(getStorageKey())
}


const getProductData = (message) => {
  try {
    return JSON.parse(message.message)
  } catch (e) {
    return {
      name: 'Produit',
      price: 0,
      image: '/placeholder.svg?height=80&width=80',
      shop: 'Boutique',
      rating: null
    }
  }
}

const formatPrice = (price) => {
  if (!price) return 'Prix non disponible'
  return new Intl.NumberFormat('fr-FR', {
    style: 'currency',
    currency: 'EUR'
  }).format(price)
}

const formatTime = (timestamp) => {
  return new Date(timestamp).toLocaleTimeString('fr-FR', {
    hour: '2-digit',
    minute: '2-digit'
  })
}

const scrollToBottom = () => {
  nextTick(() => {
    if (messagesContainer.value) {
      messagesContainer.value.scrollTop = messagesContainer.value.scrollHeight
    }
  })
}

watch(persistedMessages, scrollToBottom, { deep: true })
</script>

<style scoped>
.chat-window-desktop {
  position: fixed;
  bottom: 20px;
  right: 20px;
  width: 350px;
  background: white;
  border-radius: 12px;
  box-shadow: 0 8px 30px rgba(0, 0, 0, 0.15);
  z-index: 1000;
  overflow: hidden;
  border: 1px solid #e0e0e0;
}

.chat-header {
  background: linear-gradient(160deg, #0c0c0c , #fc4618 , #0c0c0c);
  color: white;
  padding: 16px;
  display: flex;
  align-items: center;
  justify-content: space-between;
}

.supplier-info {
  display: flex;
  align-items: center;
  gap: 12px;
}

.supplier-logo {
  width: 40px;
  height: 40px;
  border-radius: 50%;
  object-fit: cover;
}

.supplier-details h3 {
  margin: 0;
  font-size: 16px;
  font-weight: 600;
}

.status-indicator {
  font-size: 12px;
  opacity: 0.9;
}

.chat-actions {
  display: flex;
  gap: 8px;
}

.minimize-btn,
.close-btn {
  background: #ffffff33;
  border: none;
  color: white;
  height: auto;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  cursor: pointer;
  transition: background 0.2s;
}

.minimize-btn:hover,
.close-btn:hover {
  background: #ffffff4d;
}

.chat-body {
  height: 400px;
  display: flex;
  flex-direction: column;
}

.messages-container {
  flex: 1;
  padding: 16px;
  overflow-y: auto;
  max-height: 250px;
}

.message {
  margin-bottom: 16px;
  display: flex;
}

.user-message {
  justify-content: flex-end;
}

.bot-message {
  justify-content: flex-start;
}

.message-content {
  max-width: 80%;
  padding: 12px 16px;
  border-radius: 18px;
  position: relative;
}

.user-message .message-content {
  background: linear-gradient(160deg, #fe9700, #fc4618);
  color: white;
}

.bot-message .message-content {
  background: #f1f3f4;
  color: #333;
}

.message-content p {
  margin: 0 0 4px 0;
  font-size: 14px;
  line-height: 1.4;
}

.message-time {
  font-size: 11px;
  opacity: 0.7;
}

/* Styles pour la carte produit */
.product-message-card {
  max-width: 90%;
  background: white;
  border: 1px solid #e0e0e0;
  border-radius: 12px;
  overflow: hidden;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
  display: flex;
  gap: 12px;
  padding: 12px;
}

.product-image-container {
  flex-shrink: 0;
}

.product-image {
  width: 80px;
  height: 80px;
  object-fit: cover;
  border-radius: 8px;
  background: #f8f9fa;
}

.product-details {
  flex: 1;
  display: flex;
  flex-direction: column;
  justify-content: space-between;
}

.product-name {
  margin: 0 0 4px 0;
  font-size: 14px;
  font-weight: 600;
  color: #333;
  line-height: 1.3;
}

.product-shop {
  margin: 0 0 8px 0;
  font-size: 12px;
  color: #666;
}

.product-price-rating {
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 8px;
}

.product-price {
  font-size: 16px;
  font-weight: 700;
  color: #fe9700;
}

.product-rating {
  display: flex;
  align-items: center;
  gap: 4px;
  font-size: 12px;
}

.rating-stars {
  color: #ffc107;
}

.rating-value {
  color: #666;
  font-weight: 500;
}

.quick-actions {
  padding: 12px 16px;
  display: flex;
  gap: 8px;
  flex-wrap: wrap;
  border-top: 1px solid #f0f0f0;
}

.quick-action-btn {
  background: #f8f9fa;
  border: 1px solid #e9ecef;
  color: #495057;
  padding: 6px 12px;
  border-radius: 16px;
  font-size: 12px;
  cursor: pointer;
  transition: all 0.2s;
}

.quick-action-btn:hover {
  background: #e9ecef;
  transform: translateY(-1px);
}

.message-input-container {
  padding: 16px;
  border-top: 1px solid #f0f0f0;
}

.input-wrapper {
  display: flex;
  gap: 8px;
  align-items: center;
  border: none;
}

.input-style {
  width: 100%;
  color: black ; 
  border: 1px solid #d1d5db; 
  border-radius: 25px; 
  padding: 12px 16px; 
  transition: border-color 0.2s, box-shadow 0.2s; 
}
.input-style:focus {
  border-color: #fe7900; 
  box-shadow: 0 0 0 0.5px #fe7900; 
}

.send-btn {
  background: linear-gradient(160deg, #fe9700, #fc4618);
  border: none;
  color: white;
  height: auto;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  cursor: pointer;
  transition: transform 0.2s;
}

.send-btn:hover:not(:disabled) {
  transform: scale(1.05);
}

.send-btn:disabled {
  opacity: 0.5;
  cursor: not-allowed;
}

/* Scrollbar personnalisée */
.messages-container::-webkit-scrollbar {
  width: 4px;
}

.messages-container::-webkit-scrollbar-track {
  background: #f1f1f1;
}

.messages-container::-webkit-scrollbar-thumb {
  background: #c1c1c1;
  border-radius: 2px;
}

.messages-container::-webkit-scrollbar-thumb:hover {
  background: #a8a8a8;
}

/* Ajout de styles pour les boutons de test */
.test-actions {
  padding: 8px 16px;
  display: flex;
  gap: 8px;
  border-top: 1px solid #f0f0f0;
  background: #f8f9fa;
}

.test-btn {
  background: #fe9700;
  border: none;
  color: white;
  padding: 8px 12px;
  border-radius: 16px;
  font-size: 12px;
  cursor: pointer;
  transition: all 0.2s;
  flex: 1;
}

.test-btn:hover {
  background: #fc4618;
  transform: translateY(-1px);
}

.clear-btn {
  background: #ff4d4f;
}

.clear-btn:hover {
  background: #ff7875;
}
</style>
