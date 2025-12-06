<template>
  <div class="chat-modal-overlay" @click.self="$emit('close')">
    <div class="chat-modal">
      <div class=" inset-0  opacity-10">
      <div class="absolute top-0 left-0 w-70 h-70 bg-white rounded-full -translate-x-48 -translate-y-48"></div>
      </div>
      <div class="chat-header">
        <div class="supplier-info">
          <div class="supplier-avatar">
            <img src="https://cdn-icons-png.flaticon.com/512/4526/4526832.png" :alt="supplier.name" />
          </div>
          <div class="supplier-details">
            <h3 class="supplier-name">{{ supplier.name }}</h3>
            <div class="supplier-status">
              <div class="status-indicator online"></div>
              <span>En ligne</span>
            </div>
          </div>
        </div>
        <button class="close-btn" @click="$emit('close')">
          <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <line x1="18" y1="6" x2="6" y2="18"></line>
            <line x1="6" y1="6" x2="18" y2="18"></line>
          </svg>
        </button>
      </div>
      
      <div class="chat-messages" ref="messagesContainer">
        <div 
          v-for="message in persistedMessages" 
          :key="message.id"
          class="message"
          :class="{ 'user-message': message.sender === 'user', 'bot-message': message.sender === 'bot' }"
        >
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
          
          <div v-else class="message-content">
            <div class="message-text">{{ message.message || message.text }}</div>
            <div class="message-time">{{ formatTime(message.timestamp) }}</div>
          </div>
        </div>
      </div>
      
      <div class="chat-input">
        <div class="input-container">
          <input 
            type="text" 
            v-model="newMessage"
            @keypress.enter="handleSendMessage"
            placeholder="Tapez votre message..."
            class="input-style"
          />
          <button 
            class="send-btn" 
            @click="handleSendMessage"
            :disabled="!newMessage.trim()"
          >
            <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor" stroke="currentColor" stroke-width="2">
              <line x1="22" y1="2" x2="11" y2="13"></line>
              <polygon points="22,2 15,22 11,13 2,9 22,2"></polygon>
            </svg>
          </button>
        </div>
        
        <div class="quick-actions">
          <button class="quick-action" @click="sendQuickMessage('Bonjour, je suis intéressé par ce produit.')">
            👋 Saluer
          </button>
          <button class="quick-action" @click="sendQuickMessage('Quel est le prix de gros ?')">
            💰 Prix de gros
          </button>
          <button class="quick-action" @click="sendQuickMessage('Avez-vous ce produit en stock ?')">
            📦 Stock
          </button>
          <button class="quick-action test-btn" @click="addTestProduct">
            🛍️ Ajouter Produit
          </button>
          <button class="quick-action clear-btn" @click="clearAllMessages">
            🗑️ Vider Chat
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, nextTick, defineProps, defineEmits, onMounted, watch } from 'vue'

const props = defineProps({
  supplier: {
    type: Object,
    default: () => ({
      name: 'Support Client',
      logo: '/placeholder.svg?height=40&width=40'
    })
  },
  chatMessages: {
    type: Array,
    default: () => []
  }
})

const emit = defineEmits(['close', 'sendMessage'])

const messagesContainer = ref(null)
const newMessage = ref('')
const persistedMessages = ref([])

const getStorageKey = () => `chat_messages_mobile_${props.supplier.id || 'default'}`

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
  
  props.chatMessages.forEach(propMessage => {
    const exists = allMessages.some(msg => msg.id === propMessage.id)
    if (!exists) {
      allMessages.push(propMessage)
    }
  })
  
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

const handleSendMessage = () => {
  if (newMessage.value.trim()) {
    const message = {
      id: Date.now(),
      message: newMessage.value,
      sender: 'user',
      timestamp: new Date()
    }
    
    persistedMessages.value.push(message)
    emit('sendMessage', message)
    newMessage.value = ''
    scrollToBottom()
  }
}

const sendQuickMessage = (message) => {
  newMessage.value = message
  nextTick(() => {
    handleSendMessage()
  })
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
  scrollToBottom()
}

const clearAllMessages = () => {
  if (confirm('Êtes-vous sûr de vouloir vider tout le chat ?')) {
    persistedMessages.value = []
    localStorage.removeItem(getStorageKey())
  }
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

const scrollToBottom = () => {
  nextTick(() => {
    if (messagesContainer.value) {
      messagesContainer.value.scrollTop = messagesContainer.value.scrollHeight
    }
  })
}

const formatTime = (timestamp) => {
  return new Date(timestamp).toLocaleTimeString('fr-FR', {
    hour: '2-digit',
    minute: '2-digit'
  })
}

watch(persistedMessages, scrollToBottom, { deep: true })
</script>

<style scoped>
.chat-modal-overlay {
  position: fixed;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  z-index: 1000;
  display: flex;
  align-items: center;
  justify-content: center;
  padding: 20px;
}

.chat-modal {
  background: #fff;
  border-radius: 12px;
  width: 100%;
  max-width: 400px;
  height: 600px;
  max-height: 90vh;
  display: flex;
  flex-direction: column;
  overflow: hidden;
}

.chat-header {
  display: flex;
  background: linear-gradient(160deg, #0c0c0c , #fc4618 , #0c0c0c);
  justify-content: space-between;
  align-items: center;
  padding: 16px 20px;
  border-bottom: 1px solid #e8e8e8;
  /* background: #fafafa; */
}

.supplier-info {
  display: flex;
  align-items: center;
  gap: 12px;
}

.supplier-avatar {
  width: 40px;
  height: 40px;
  border-radius: 50%;
  overflow: hidden;
}

.supplier-avatar img {
  width: 100%;
  height: 100%;
  object-fit: cover;
}

.supplier-name {
  font-size: 16px;
  font-weight: 600;
  color: white;
  margin: 0 0 4px 0;
}

.supplier-status {
  display: flex;
  align-items: center;
  gap: 6px;
  font-size: 12px;
  color: #52c41a;
}

.status-indicator {
  width: 8px;
  height: 8px;
  border-radius: 50%;
}

.status-indicator.online {
  background: #52c41a;
}

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

.close-btn:hover {
  background: #ffffff4d;
}

.chat-messages {
  flex: 1;
  overflow-y: auto;
  padding: 16px;
  display: flex;
  flex-direction: column;
  gap: 12px;
}

.message {
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
  display: flex;
  flex-direction: column;
  gap: 4px;
}

.message-text {
  padding: 10px 14px;
  border-radius: 18px;
  font-size: 14px;
  line-height: 1.4;
}

.user-message .message-text {
   background: linear-gradient(160deg, #fe9700, #fc4618);
  color: white;
  border-bottom-right-radius: 6px;
}

.bot-message .message-text {
 background: #f1f3f4;
  color: #333;
  border-bottom-left-radius: 6px;
}

.message-time {
  font-size: 11px;
  color: #999;
  padding: 0 4px;
}

.user-message .message-time {
  text-align: right;
}

.bot-message .message-time {
  text-align: left;
}

.chat-input {
  border-top: 1px solid #e8e8e8;
  padding: 16px;
  background: #fafafa;
}

.input-container {
  display: flex;
  gap: 8px;
  margin-bottom: 12px;
}

.message-input {
  flex: 1;
  padding: 10px 14px;
  border: 1px solid #d9d9d9;
  border-radius: 20px;
  font-size: 14px;
  outline: none;
  transition: border-color 0.3s ease;
}

.message-input:focus {
  border-color: #1890ff;
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

.quick-actions {
  display: flex;
  gap: 8px;
  flex-wrap: wrap;
}

.quick-action {
  padding: 6px 12px;
  background: #fff;
  border: 1px solid #d9d9d9;
  border-radius: 16px;
  font-size: 12px;
  color: #666;
  cursor: pointer;
  transition: all 0.3s ease;
}

.quick-action:hover {
  border-color: #fe7900;
  color: #fe9700;
}

.test-btn {
  background: #fe9700 !important;
  color: white !important;
  border-color: #fe9700 !important;
}

.test-btn:hover {
  background: #fe9700 !important;
  border-color: #fe9700 !important;
}

.clear-btn {
  background: #ff4d4f !important;
  color: white !important;
  border-color: #ff4d4f !important;
}

.clear-btn:hover {
  background: #ff7875 !important;
  border-color: #ff7875 !important;
}

.product-message-card {
  max-width: 95%;
  background: white;
  border: 1px solid #e0e0e0;
  border-radius: 12px;
  overflow: hidden;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
  display: flex;
  gap: 12px;
  padding: 12px;
  margin-bottom: 8px;
}

.product-image-container {
  flex-shrink: 0;
}

.product-image {
  width: 70px;
  height: 70px;
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
  font-size: 13px;
  font-weight: 600;
  color: #333;
  line-height: 1.3;
}

.product-shop {
  margin: 0 0 8px 0;
  font-size: 11px;
  color: #666;
}

.product-price-rating {
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 8px;
}

.product-price {
  font-size: 14px;
  font-weight: 700;
  color: #1890ff;
}

.product-rating {
  display: flex;
  align-items: center;
  gap: 4px;
  font-size: 11px;
}

.rating-stars {
  color: #ffc107;
}

.rating-value {
  color: #666;
  font-weight: 500;
}

@media (max-width: 768px) {
  .chat-modal-overlay {
    padding: 0;
  }
  
  .chat-modal {
    margin: 0;
    position: fixed;
    bottom: 0;
    left: 0;
    right: 0;
    height: 100vh;
    max-height: 100vh;
    max-width: none;
  }
}
</style>
