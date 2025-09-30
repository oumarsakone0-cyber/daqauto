<template>
  <div v-if="isOpen" class="fixed bottom-4 right-4 w-96 bg-white rounded-lg shadow-2xl border border-gray-200 z-50 font-sans">
     Header 
    <div class="flex items-center justify-between p-4 bg-gradient-to-r from-blue-500 to-blue-600 text-white rounded-t-lg">
      <div class="flex items-center space-x-3">
        <div class="w-10 h-10 bg-white/20 rounded-full flex items-center justify-center">
          <span class="text-sm font-semibold">{{ supplier.name?.charAt(0) || 'S' }}</span>
        </div>
        <div>
          <h3 class="font-semibold text-sm">{{ supplier.name || 'Fournisseur' }}</h3>
          <p class="text-xs opacity-90">
            <span v-if="connectionStatus === 'connected'" class="flex items-center">
              <span class="w-2 h-2 bg-green-400 rounded-full mr-1"></span>
              En ligne
            </span>
            <span v-else-if="connectionStatus === 'connecting'" class="flex items-center">
              <span class="w-2 h-2 bg-yellow-400 rounded-full mr-1 animate-pulse"></span>
              Connexion...
            </span>
            <span v-else class="flex items-center">
              <span class="w-2 h-2 bg-red-400 rounded-full mr-1"></span>
              Hors ligne
            </span>
          </p>
        </div>
      </div>
      <div class="flex items-center space-x-2">
        <button @click="toggleMinimize" class="p-1 hover:bg-white/20 rounded">
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" :d="isMinimized ? 'M19 9l-7 7-7-7' : 'M5 15l7-7 7 7'"></path>
          </svg>
        </button>
        <button @click="$emit('close')" class="p-1 hover:bg-white/20 rounded">
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
          </svg>
        </button>
      </div>
    </div>

     Chat Body 
    <div v-show="!isMinimized" class="flex flex-col h-96">
       Messages Container 
      <div ref="messagesContainer" class="flex-1 p-4 overflow-y-auto space-y-3 bg-gray-50">
        <div v-for="message in persistedMessages" :key="message.id" 
             :class="['flex', message.sender === 'user' ? 'justify-end' : 'justify-start']">
          
           User Message 
          <div v-if="message.sender === 'user'" 
               class="max-w-xs bg-blue-500 text-white px-4 py-2 rounded-lg rounded-br-sm">
            <p class="text-sm">{{ message.message }}</p>
            <span class="text-xs opacity-75 block mt-1">{{ formatTime(message.timestamp) }}</span>
          </div>
          
           <!-- Bot Message  -->
          <div v-else class="max-w-xs">
            <div v-if="message.type === 'product'" class="bg-white border border-gray-200 rounded-lg p-3 shadow-sm">
              <div class="flex space-x-3">
                <img :src="getProductData(message).image" :alt="getProductData(message).name" 
                     class="w-16 h-16 object-cover rounded-lg">
                <div class="flex-1 min-w-0">
                  <h4 class="font-semibold text-sm text-gray-900 truncate">{{ getProductData(message).name }}</h4>
                  <p class="text-xs text-gray-500 mb-1">{{ getProductData(message).shop }}</p>
                  <p class="font-bold text-blue-600 text-sm">{{ formatPrice(getProductData(message).price) }}</p>
                  <div v-if="getProductData(message).rating" class="flex items-center mt-1">
                    <div class="flex text-yellow-400 text-xs">
                      <span v-for="i in 5" :key="i">
                        {{ i <= Math.floor(getProductData(message).rating) ? '★' : '☆' }}
                      </span>
                    </div>
                    <span class="text-xs text-gray-500 ml-1">({{ getProductData(message).rating }})</span>
                  </div>
                </div>
              </div>
            </div>
            
             <!-- Text Message  -->
            <div v-else class="bg-white border border-gray-200 px-4 py-2 rounded-lg rounded-bl-sm">
              <p class="text-sm text-gray-800">{{ message.message }}</p>
              <span class="text-xs text-gray-500 block mt-1">{{ formatTime(message.timestamp) }}</span>
            </div>
          </div>
        </div>

         Typing Indicator 
        <div v-if="isTyping" class="flex justify-start">
          <div class="bg-white border border-gray-200 px-4 py-2 rounded-lg rounded-bl-sm">
            <div class="flex space-x-1">
              <div class="w-2 h-2 bg-gray-400 rounded-full animate-bounce"></div>
              <div class="w-2 h-2 bg-gray-400 rounded-full animate-bounce" style="animation-delay: 0.1s"></div>
              <div class="w-2 h-2 bg-gray-400 rounded-full animate-bounce" style="animation-delay: 0.2s"></div>
            </div>
          </div>
        </div>
      </div>

       Quick Actions 
      <div class="px-4 py-2 bg-white border-t border-gray-100">
        <div class="flex flex-wrap gap-2">
          <button v-for="action in quickActions" :key="action.id"
                  @click="sendQuickAction(action)"
                  class="px-3 py-1 text-xs bg-gray-100 hover:bg-gray-200 rounded-full transition-colors">
            {{ action.text }}
          </button>
        </div>
      </div>

       Input Area 
      <div class="p-4 bg-white border-t border-gray-100 rounded-b-lg">
        <div class="flex space-x-2">
          <input v-model="newMessage" 
                 @keypress.enter="sendMessage"
                 @input="handleTyping"
                 type="text" 
                 placeholder="Tapez votre message..."
                 class="flex-1 px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm">
          <button @click="sendMessage" 
                  :disabled="!newMessage.trim() || connectionStatus !== 'connected'"
                  class="px-4 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600 disabled:opacity-50 disabled:cursor-not-allowed transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path>
            </svg>
          </button>
        </div>
        
         Debug Actions 
        <div class="flex justify-between mt-2">
          <button @click="addTestProduct" 
                  class="text-xs text-gray-500 hover:text-gray-700">
            + Produit test
          </button>
          <button @click="clearAllMessages" 
                  class="text-xs text-red-500 hover:text-red-700">
            Effacer tout
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, nextTick, watch, onMounted, onUnmounted } from 'vue'

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
  },
  websocketUrl: {
    type: String,
    default: 'ws://localhost:8080'
  },
  userId: {
    type: [String, Number],
    required: true
  }
})

const emit = defineEmits(['close', 'send-message', 'message-received'])

const ws = ref(null)
const connectionStatus = ref('disconnected') // 'disconnected', 'connecting', 'connected'
const reconnectAttempts = ref(0)
const maxReconnectAttempts = 5
const reconnectTimeout = ref(null)
const isTyping = ref(false)
const typingTimeout = ref(null)

// Existing reactive variables
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

const connectWebSocket = () => {
  if (ws.value && ws.value.readyState === WebSocket.OPEN) {
    return
  }

  connectionStatus.value = 'connecting'
  
  try {
    ws.value = new WebSocket(props.websocketUrl)
    
    ws.value.onopen = () => {
      console.log('[v0] WebSocket connected')
      connectionStatus.value = 'connected'
      reconnectAttempts.value = 0
      
      // Join the chat room
      sendWebSocketMessage({
        type: 'join_chat',
        supplier_id: props.supplier.id,
        user_id: props.userId
      })
    }
    
    ws.value.onmessage = (event) => {
      try {
        const data = JSON.parse(event.data)
        handleWebSocketMessage(data)
      } catch (error) {
        console.error('[v0] Error parsing WebSocket message:', error)
      }
    }
    
    ws.value.onclose = (event) => {
      console.log('[v0] WebSocket disconnected:', event.code, event.reason)
      connectionStatus.value = 'disconnected'
      
      // Attempt to reconnect if not manually closed
      if (event.code !== 1000 && reconnectAttempts.value < maxReconnectAttempts) {
        scheduleReconnect()
      }
    }
    
    ws.value.onerror = (error) => {
      console.error('[v0] WebSocket error:', error)
      connectionStatus.value = 'disconnected'
    }
    
  } catch (error) {
    console.error('[v0] Error creating WebSocket connection:', error)
    connectionStatus.value = 'disconnected'
    scheduleReconnect()
  }
}

const scheduleReconnect = () => {
  if (reconnectAttempts.value >= maxReconnectAttempts) {
    console.log('[v0] Max reconnection attempts reached')
    return
  }
  
  const delay = Math.min(1000 * Math.pow(2, reconnectAttempts.value), 30000)
  reconnectAttempts.value++
  
  console.log(`[v0] Scheduling reconnect attempt ${reconnectAttempts.value} in ${delay}ms`)
  
  reconnectTimeout.value = setTimeout(() => {
    connectWebSocket()
  }, delay)
}

const sendWebSocketMessage = (message) => {
  if (ws.value && ws.value.readyState === WebSocket.OPEN) {
    ws.value.send(JSON.stringify(message))
    return true
  }
  return false
}

const handleWebSocketMessage = (data) => {
  console.log('[v0] Received WebSocket message:', data)
  
  switch (data.type) {
    case 'connection_confirmed':
      console.log('[v0] Connection confirmed for room:', data.room)
      break
      
    case 'new_message':
      // Add the new message to our local messages
      const newMsg = data.message
      const exists = persistedMessages.value.some(msg => msg.id === newMsg.id)
      if (!exists) {
        persistedMessages.value.push(newMsg)
        emit('message-received', newMsg)
      }
      break
      
    case 'message_status':
      // Update message status (sent/failed)
      const messageIndex = persistedMessages.value.findIndex(msg => msg.id === data.message_id)
      if (messageIndex !== -1) {
        persistedMessages.value[messageIndex].status = data.status
      }
      break
      
    case 'typing':
      if (data.user_id !== props.userId) {
        isTyping.value = data.is_typing
        if (data.is_typing) {
          // Clear typing indicator after 3 seconds
          setTimeout(() => {
            isTyping.value = false
          }, 3000)
        }
      }
      break
  }
}

const disconnectWebSocket = () => {
  if (reconnectTimeout.value) {
    clearTimeout(reconnectTimeout.value)
    reconnectTimeout.value = null
  }
  
  if (ws.value) {
    ws.value.close(1000, 'Manual disconnect')
    ws.value = null
  }
  
  connectionStatus.value = 'disconnected'
}

const handleTyping = () => {
  // Send typing indicator via WebSocket
  sendWebSocketMessage({
    type: 'typing',
    supplier_id: props.supplier.id,
    is_typing: true
  })
  
  // Clear previous timeout
  if (typingTimeout.value) {
    clearTimeout(typingTimeout.value)
  }
  
  // Stop typing indicator after 1 second of inactivity
  typingTimeout.value = setTimeout(() => {
    sendWebSocketMessage({
      type: 'typing',
      supplier_id: props.supplier.id,
      is_typing: false
    })
  }, 1000)
}

// Existing functions with WebSocket integration
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

const toggleMinimize = () => {
  isMinimized.value = !isMinimized.value
}

const sendMessage = () => {
  if (!newMessage.value.trim()) return
  
  const message = {
    id: Date.now(),
    message: newMessage.value,
    sender: 'user',
    timestamp: new Date(),
    supplier_id: props.supplier.id,
    user_id: props.userId,
    status: 'sending'
  }
  
  // Add to local messages immediately
  persistedMessages.value.push(message)
  
  // Try to send via WebSocket first
  const sent = sendWebSocketMessage({
    type: 'send_message',
    message: message
  })
  
  if (!sent) {
    // Fallback to emit if WebSocket is not available
    message.status = 'failed'
    emit('send-message', message)
  }
  
  newMessage.value = ''
}

const sendQuickAction = (action) => {
  const message = {
    id: Date.now(),
    message: action.text,
    sender: 'user',
    timestamp: new Date(),
    supplier_id: props.supplier.id,
    user_id: props.userId,
    status: 'sending'
  }
  
  persistedMessages.value.push(message)
  
  const sent = sendWebSocketMessage({
    type: 'send_message',
    message: message
  })
  
  if (!sent) {
    message.status = 'failed'
    emit('send-message', message)
  }
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

onMounted(() => {
  mergeMessages()
  connectWebSocket()
})

onUnmounted(() => {
  disconnectWebSocket()
  if (typingTimeout.value) {
    clearTimeout(typingTimeout.value)
  }
})

watch(() => props.isOpen, (newValue) => {
  if (newValue && connectionStatus.value === 'disconnected') {
    connectWebSocket()
  } else if (!newValue) {
    disconnectWebSocket()
  }
})

watch(() => props.chatMessages, () => {
  mergeMessages()
}, { deep: true })

watch(persistedMessages, (newMessages) => {
  saveMessagesToStorage(newMessages)
  scrollToBottom()
}, { deep: true })
</script>
