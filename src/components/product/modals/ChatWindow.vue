<template>
  <!--  Floating chat button - unchanged -->
  <button
    v-if="!isOpen"
    @click="toggleChat"
    class="fixed bottom-6 right-6 w-16 h-16 rounded-full shadow-lg flex items-center justify-center z-50 transition-transform hover:scale-110"
    style="background: linear-gradient(160deg, #fe9700, #fc4618)"
  >
    <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2">
      <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/>
    </svg>
    <span
      v-if="totalUnreadCount > 0"
      class="absolute -top-1 -right-1 bg-red-500 text-white text-xs font-bold rounded-full w-6 h-6 flex items-center justify-center"
    >
      {{ totalUnreadCount > 9 ? '9+' : totalUnreadCount }}
    </span>
  </button>

  <!--  Main chat container with authentication check -->
  <div
    v-if="isOpen"
    style="z-index: 1111;"
    class="fixed inset-0 md:inset-auto md:bottom-6 md:right-6 md:w-[700px] md:h-[600px] bg-white md:rounded-xl shadow-2xl z-50 flex flex-col overflow-hidden"
  >
    <!--  Show login prompt if not authenticated -->
    <div v-if="!isAuthenticated" class="flex flex-col h-full">
      <!-- Header -->
      <div
        class="flex items-center justify-between px-4 py-3 text-white relative overflow-hidden bg-degrade-orange"
      >
        <div class="absolute inset-0 opacity-10">
          <div class="absolute top-0 left-0 w-32 h-32 bg-white rounded-full -translate-x-16 -translate-y-16"></div>
        </div>

        <div class="flex items-center gap-3 flex-1 relative z-10">
          <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2">
            <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/>
          </svg>
          <h3 class="font-semibold text-base">Messagerie</h3>
        </div>

        <button
          @click="toggleChat"
          class="relative z-10 p-2 hover:bg-white/20 rounded-lg transition-colors"
        >
          <XIcon class="h-7 w-7 text-white"/>
        </button>
      </div>

      <!-- Login prompt content -->
      <div class="flex-1 flex items-center justify-center p-8">
        <div class="max-w-md w-full text-center space-y-6">
          <div class="w-20 h-20 mx-auto rounded-full flex items-center justify-center" style="background: linear-gradient(160deg, #fe9700, #fc4618)">
            <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2">
              <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/>
              <circle cx="12" cy="7" r="4"/>
            </svg>
          </div>

          <div>
            <h2 class="text-2xl font-bold text-gray-900 mb-2">Connexion requise</h2>
            <p class="text-gray-600">Vous devez vous connecter avant de discuter avec les fournisseurs</p>
          </div>

          <div class="space-y-3">
            <button
              @click="redirectToLogin"
              class="w-full py-3 px-4 rounded-lg font-medium text-white transition-transform hover:scale-105"
              style="background: linear-gradient(160deg, #fe9700, #fc4618)"
            >
              Se connecter avec Email
            </button>

            <button
              @click="loginWithGoogle"
              class="w-full py-3 px-4 border-2 border-gray-300 rounded-lg font-medium text-gray-700 bg-white hover:bg-gray-50 transition-colors flex items-center justify-center gap-2"
            >
              <svg class="w-5 h-5" viewBox="0 0 24 24">
                <path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/>
                <path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/>
                <path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z"/>
                <path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z"/>
              </svg>
              Connexion avec Google
            </button>
          </div>
        </div>
      </div>
    </div>

    <!--  Show normal chat interface if authenticated -->
    <div v-else class="flex flex-col h-full">
      <div
        class="flex items-center justify-between px-4 py-3 text-white relative overflow-hidden bg-degrade-orange"
      >
        <div class="absolute inset-0 opacity-10">
          <div class="absolute top-0 left-0 w-32 h-32 bg-white rounded-full -translate-x-16 -translate-y-16"></div>
        </div>

        <button
          @click="toggleSidebar"
          class="md:hidden relative z-10 p-2 hover:bg-white/20 rounded-lg transition-colors"
        >
          <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <line x1="3" y1="12" x2="21" y2="12"/>
            <line x1="3" y1="6" x2="21" y2="6"/>
            <line x1="3" y1="18" x2="21" y2="18"/>
          </svg>
        </button>

        <div class="flex items-center gap-3 flex-1 relative z-10">
          <img
            :src="activeConversation.avatar"
            :alt="activeConversation.name"
            class="w-10 h-10 rounded-full object-cover"
          />
          <div>
            <h3 class="font-semibold text-base">{{ activeConversation.name }}</h3>
            <span class="text-xs opacity-90">{{ activeConversation.status }}</span>
          </div>
        </div>

        <button
          @click="toggleChat"
          class="relative z-10 p-2 hover:bg-white/20 rounded-lg transition-colors"
        >
          <XIcon class="h-7 w-7 text-white"/>
        </button>
      </div>

      <div class="flex flex-1 overflow-hidden">
        <!-- ... existing code ... -->
        <div
          :class="[
            'absolute md:relative inset-0 md:inset-auto w-full md:w-64 bg-gray-50 border-r border-gray-200 flex flex-col transition-transform duration-300 z-20',
            showSidebar ? 'translate-x-0' : '-translate-x-full md:translate-x-0'
          ]"
        >
          <div class="md:hidden flex items-center justify-between p-4 border-b border-gray-200 bg-white">
            <h2 class="font-semibold text-gray-800">Conversations</h2>
            <button
              @click="toggleSidebar"
              class="p-2 hover:bg-gray-100 rounded-lg transition-colors"
            >
              <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <line x1="19" y1="12" x2="5" y2="12"/>
                <polyline points="12 19 5 12 12 5"/>
              </svg>
            </button>
          </div>

          <div class="p-3 border-b border-gray-200 bg-white">
            <input
              v-model="searchQuery"
              type="text"
              placeholder="Rechercher..."
              class="w-full px-3 py-2 bg-gray-100 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-orange-500 text-gray-800"
            />
          </div>

          <div class="flex-1 overflow-y-auto">
            <div
              v-for="conv in filteredConversations"
              :key="conv.id"
              @click="selectConversation(conv.id)"
              :class="[
                'flex items-center gap-3 p-3 cursor-pointer transition-colors border-b border-gray-100',
                activeConversation.id === conv.id ? 'bg-orange-50' : 'hover:bg-gray-100'
              ]"
            >
              <div class="relative">
                <img
                  :src="conv.avatar"
                  :alt="conv.name"
                  class="w-12 h-12 rounded-full object-cover"
                />
                <span
                  v-if="conv.online"
                  class="absolute bottom-0 right-0 w-3 h-3 bg-green-500 border-2 border-white rounded-full"
                ></span>
              </div>
              <div class="flex-1 min-w-0">
                <div class="flex items-center justify-between">
                  <h4 class="font-semibold text-sm text-gray-800 truncate">{{ conv.name }}</h4>
                  <span class="text-xs text-gray-500">{{ conv.lastMessageTime }}</span>
                </div>
                <p class="text-xs text-gray-600 truncate">{{ conv.lastMessage }}</p>
              </div>
              <span
                v-if="conv.unreadCount > 0"
                class="bg-orange-500 text-white text-xs font-bold rounded-full w-5 h-5 flex items-center justify-center flex-shrink-0"
              >
                {{ conv.unreadCount }}
              </span>
            </div>
          </div>
        </div>

        <div class="flex-1 flex flex-col bg-white">
          <div ref="messagesContainer" class="flex-1 overflow-y-auto p-4 space-y-4">
            <div
              v-for="message in activeConversation.messages"
              :key="message.id"
              :class="[
                'flex',
                message.sender === 'user' ? 'justify-end' : 'justify-start'
              ]"
            >
              <div v-if="message.type === 'product'" class="max-w-[85%] md:max-w-[70%]">
                <div class="bg-white border border-gray-200 rounded-xl overflow-hidden shadow-sm">
                  <div class="flex gap-3 p-3">
                    <img
                      :src="message.product.image"
                      :alt="message.product.name"
                      class="w-20 h-20 object-cover rounded-lg flex-shrink-0"
                    />
                    <div class="flex-1 min-w-0">
                      <h4 class="font-semibold text-sm text-gray-800 mb-1">{{ message.product.name }}</h4>
                      <p class="text-xs text-gray-600 mb-2">{{ message.product.shop }}</p>
                      <div class="flex items-center justify-between">
                        <span class="text-orange-600 font-bold text-base">{{ formatPrice(message.product.price) }}</span>
                        <div v-if="message.product.rating" class="flex items-center gap-1 text-xs">
                          <span class="text-yellow-500">⭐</span>
                          <span class="text-gray-600 font-medium">{{ message.product.rating }}</span>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
                <span class="text-xs text-gray-500 mt-1 block">{{ formatTime(message.timestamp) }}</span>
              </div>

              <div v-else class="max-w-[85%] md:max-w-[70%]">
                <div
                  :class="[
                    'rounded-2xl px-4 py-2',
                    message.sender === 'user'
                      ? 'text-white'
                      : 'bg-gray-100 text-gray-800'
                  ]"
                  :style="message.sender === 'user' ? 'background: linear-gradient(160deg, #fe9700, #fc4618)' : ''"
                >
                  <p class="text-sm leading-relaxed">{{ message.text }}</p>
                </div>
                <span class="text-xs text-gray-500 mt-1 block">{{ formatTime(message.timestamp) }}</span>
              </div>
            </div>
          </div>

          <div class="border-t border-gray-200 p-4">
            <div class="flex items-center gap-2">
              <input
                v-model="newMessage"
                @keypress.enter="sendMessage"
                type="text"
                placeholder="Tapez votre message..."
                class="flex-1 px-4 py-3 bg-gray-50 border border-gray-200 rounded-full text-sm focus:outline-none focus:ring-2 focus:ring-orange-500 text-gray-800"
              />
              <button
                @click="sendMessage"
                :disabled="!newMessage.trim()"
                class="w-12 h-12 rounded-full flex items-center justify-center transition-transform hover:scale-105 disabled:opacity-50 disabled:cursor-not-allowed"
                style="background: linear-gradient(160deg, #fe9700, #fc4618)"
              >
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2">
                  <line x1="22" y1="2" x2="11" y2="13"/>
                  <polygon points="22,2 15,22 11,13 2,9 22,2"/>
                </svg>
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, computed, nextTick, watch, onMounted } from 'vue'
import { useChatStore } from '../../../stores/chat'
import { X, XIcon } from 'lucide-vue-next'

interface Product {
  name: string
  price: number
  image: string
  shop: string
  rating?: number
}

interface Message {
  id: number
  text?: string
  sender: 'user' | 'bot'
  timestamp: Date
  type?: 'product'
  product?: Product
}

interface Conversation {
  id: number
  name: string
  avatar: string
  status: string
  online: boolean
  lastMessage: string
  lastMessageTime: string
  unreadCount: number
  messages: Message[]
}

const isOpen = ref(false)
const showSidebar = ref(false)
const newMessage = ref('')
const searchQuery = ref('')
const messagesContainer = ref<HTMLElement | null>(null)
const activeConversationId = ref(1)

//  Add authentication state
const isAuthenticated = ref(false)
const currentUser = ref<any>(null)

//  Check authentication on mount
const checkAuthentication = () => {
  const token = localStorage.getItem('authToken') || sessionStorage.getItem('authToken')
  const userStr = localStorage.getItem('user') || sessionStorage.getItem('user')
  
  if (token && userStr) {
    try {
      currentUser.value = JSON.parse(userStr)
      isAuthenticated.value = true
    } catch (error) {
      console.error('[v0] Error parsing user data:', error)
      isAuthenticated.value = false
    }
  } else {
    isAuthenticated.value = false
  }
}

//  Redirect to login page
const redirectToLogin = () => {
  window.location.href = '/login'
}

//  Google login handler
const loginWithGoogle = () => {
  console.log('[v0] Google login not yet implemented')
  alert('La connexion avec Google n\'est pas encore disponible.')
}

const conversations = ref<Conversation[]>([
  {
    id: 1,
    name: 'Apple Store',
    avatar: 'https://cdn-icons-png.flaticon.com/512/4526/4526832.png',
    status: 'En ligne',
    online: true,
    lastMessage: 'Voici le produit que vous cherchez',
    lastMessageTime: '10:30',
    unreadCount: 2,
    messages: [
      {
        id: 1,
        text: 'Bonjour, je cherche un iPhone',
        sender: 'user',
        timestamp: new Date('2024-01-15T10:00:00')
      },
      {
        id: 2,
        text: 'Bonjour! Je peux vous aider avec ça',
        sender: 'bot',
        timestamp: new Date('2024-01-15T10:01:00')
      },
      {
        id: 3,
        sender: 'bot',
        timestamp: new Date('2024-01-15T10:02:00'),
        type: 'product',
        product: {
          name: 'iPhone 15 Pro Max',
          price: 1299,
          image: 'https://images.unsplash.com/photo-1592750475338-74b7b21085ab?w=400',
          shop: 'Apple Store',
          rating: 4.8
        }
      }
    ]
  },
  {
    id: 2,
    name: 'Samsung Electronics',
    avatar: 'https://cdn-icons-png.flaticon.com/512/732/732221.png',
    status: 'En ligne',
    online: true,
    lastMessage: 'Merci pour votre commande',
    lastMessageTime: '09:15',
    unreadCount: 0,
    messages: [
      {
        id: 1,
        text: 'Bonjour',
        sender: 'user',
        timestamp: new Date('2024-01-15T09:00:00')
      },
      {
        id: 2,
        text: 'Bonjour! Comment puis-je vous aider?',
        sender: 'bot',
        timestamp: new Date('2024-01-15T09:01:00')
      }
    ]
  },
  {
    id: 3,
    name: 'Nike Store',
    avatar: 'https://cdn-icons-png.flaticon.com/512/732/732084.png',
    status: 'Hors ligne',
    online: false,
    lastMessage: 'Nous vous répondrons bientôt',
    lastMessageTime: 'Hier',
    unreadCount: 1,
    messages: [
      {
        id: 1,
        text: 'Avez-vous des chaussures de sport?',
        sender: 'user',
        timestamp: new Date('2024-01-14T15:00:00')
      }
    ]
  },
  {
    id: 4,
    name: 'Adidas Official',
    avatar: 'https://cdn-icons-png.flaticon.com/512/732/732190.png',
    status: 'En ligne',
    online: true,
    lastMessage: 'Oui, nous avons en stock',
    lastMessageTime: '08:45',
    unreadCount: 0,
    messages: [
      {
        id: 1,
        text: 'Bonjour, avez-vous des baskets?',
        sender: 'user',
        timestamp: new Date('2024-01-15T08:30:00')
      },
      {
        id: 2,
        text: 'Oui, nous avons en stock',
        sender: 'bot',
        timestamp: new Date('2024-01-15T08:45:00')
      }
    ]
  }
])

const chatStore = useChatStore()

const activeConversation = computed(() => {
  return conversations.value.find(c => c.id === activeConversationId.value) || conversations.value[0]
})

const filteredConversations = computed(() => {
  if (!searchQuery.value) return conversations.value
  return conversations.value.filter(c =>
    c.name.toLowerCase().includes(searchQuery.value.toLowerCase())
  )
})

const totalUnreadCount = computed(() => {
  return conversations.value.reduce((sum, conv) => sum + conv.unreadCount, 0)
})

watch(() => chatStore.chatMessages, (newMessages) => {
  if (newMessages.length === 0) return
  
  const lastStoreMessage = newMessages[newMessages.length - 1]
  
  if (lastStoreMessage.type === 'product') {
    try {
      const productData = JSON.parse(lastStoreMessage.message)
      
      let conversation = conversations.value.find(c => c.name === chatStore.supplier.name)
      
      if (!conversation) {
        const newConv = {
          id: Date.now(),
          name: chatStore.supplier.name,
          avatar: chatStore.supplier.logo,
          status: chatStore.supplier.status,
          online: true,
          lastMessage: 'Nouveau produit partagé',
          lastMessageTime: formatTime(new Date()),
          unreadCount: 0,
          messages: []
        }
        conversations.value.unshift(newConv)
        conversation = newConv
      }
      
      const productMessage = {
        id: lastStoreMessage.id,
        sender: 'bot' as const,
        timestamp: lastStoreMessage.timestamp,
        type: 'product' as const,
        product: {
          name: productData.name,
          price: productData.price,
          image: productData.image,
          shop: productData.shop,
          rating: productData.rating
        }
      }
      
      const messageExists = conversation.messages.some(m => m.id === productMessage.id)
      if (!messageExists) {
        conversation.messages.push(productMessage)
        conversation.lastMessage = 'Nouveau produit partagé'
        conversation.lastMessageTime = formatTime(new Date())
        
        activeConversationId.value = conversation.id
        scrollToBottom()
      }
    } catch (error) {
      console.error('[v0] Error parsing product message:', error)
    }
  }
}, { deep: true })

watch(() => chatStore.isDesktopChatOpen, (newValue) => {
  if (newValue && !isOpen.value) {
    isOpen.value = true
  }
})

watch(() => chatStore.isChatOpen, (newValue) => {
  if (newValue && !isOpen.value) {
    isOpen.value = true
  }
})

const toggleChat = () => {
  isOpen.value = !isOpen.value
  if (!isOpen.value) {
    showSidebar.value = false
    chatStore.closeDesktopChat()
    chatStore.closeChat()
  } else {
    if (chatStore.isMobile) {
      chatStore.openChat()
    } else {
      chatStore.openDesktopChat()
    }
  }
}

const toggleSidebar = () => {
  showSidebar.value = !showSidebar.value
}

const selectConversation = (id: number) => {
  activeConversationId.value = id
  const conv = conversations.value.find(c => c.id === id)
  if (conv) {
    conv.unreadCount = 0
  }
  showSidebar.value = false
  scrollToBottom()
}

const sendMessage = () => {
  if (!newMessage.value.trim()) return

  const message: Message = {
    id: Date.now(),
    text: newMessage.value,
    sender: 'user',
    timestamp: new Date()
  }

  activeConversation.value.messages.push(message)
  activeConversation.value.lastMessage = newMessage.value
  activeConversation.value.lastMessageTime = formatTime(new Date())

  newMessage.value = ''
  scrollToBottom()
}

const formatPrice = (price: number) => {
  return new Intl.NumberFormat('fr-FR', {
    style: 'currency',
    currency: 'EUR'
  }).format(price)
}

const formatTime = (timestamp: Date) => {
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

watch(() => activeConversation.value.messages, scrollToBottom, { deep: true })

onMounted(() => {
  //  Check authentication when component mounts
  checkAuthentication()
  
  chatStore.checkMobile()
  window.addEventListener('resize', chatStore.checkMobile)
})
</script>

<style scoped>
/* Custom scrollbar */
.overflow-y-auto::-webkit-scrollbar {
  width: 6px;
}

.overflow-y-auto::-webkit-scrollbar-track {
  background: #f1f1f1;
}

.overflow-y-auto::-webkit-scrollbar-thumb {
  background: #c1c1c1;
  border-radius: 3px;
}

.overflow-y-auto::-webkit-scrollbar-thumb:hover {
  background: #a8a8a8;
}

.bg-degrade-orange {
  background: linear-gradient(160deg, #0c0c0c, #fc4618, #0c0c0c);
}
/* Custom scrollbar */
.overflow-y-auto::-webkit-scrollbar {
  width: 6px;
}

.overflow-y-auto::-webkit-scrollbar-track {
  background: #f1f1f1;
}

.overflow-y-auto::-webkit-scrollbar-thumb {
  background: #c1c1c1;
  border-radius: 3px;
}

.overflow-y-auto::-webkit-scrollbar-thumb:hover {
  background: #a8a8a8;
}

.multi-chat-container {
  display: flex;
  height: 600px;
  max-width: 1200px;
  margin: 20px auto;
  background: white;
  border-radius: 12px;
  box-shadow: 0 8px 30px rgba(0, 0, 0, 0.15);
  overflow: hidden;
}

/* Sidebar des conversations */
.conversations-sidebar {
  width: 320px;
  border-right: 1px solid #e0e0e0;
  display: flex;
  flex-direction: column;
  background: #f8f9fa;
}

.sidebar-header {
  background: linear-gradient(160deg, #0c0c0c, #fc4618, #0c0c0c);
  color: white;
  padding: 20px;
}

.header-title {
  display: flex;
  align-items: center;
  gap: 12px;
}

.messenger-icon {
  width: 32px;
  height: 32px;
  border-radius: 50%;
}

.header-title h2 {
  margin: 0;
  font-size: 18px;
  font-weight: 600;
  flex: 1;
}

.total-unread-badge {
  background: #fe9700;
  color: white;
  padding: 2px 8px;
  border-radius: 12px;
  font-size: 12px;
  font-weight: 600;
}

.conversations-list {
  flex: 1;
  overflow-y: auto;
}

.conversation-item {
  display: flex;
  gap: 12px;
  padding: 16px;
  cursor: pointer;
  transition: background 0.2s;
  border-bottom: 1px solid #e9ecef;
  background: white;
}

.conversation-item:hover {
  background: #f1f3f5;
}

.conversation-item.active {
  background: linear-gradient(90deg, #fff5eb, #ffffff);
  border-left: 3px solid #fc4618;
}

.conversation-avatar {
  position: relative;
  flex-shrink: 0;
}

.conversation-avatar img {
  width: 48px;
  height: 48px;
  border-radius: 50%;
  object-fit: cover;
}

.online-indicator {
  position: absolute;
  bottom: 2px;
  right: 2px;
  width: 12px;
  height: 12px;
  background: #4caf50;
  border: 2px solid white;
  border-radius: 50%;
}

.conversation-info {
  flex: 1;
  min-width: 0;
}

.conversation-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 4px;
}

.supplier-name {
  margin: 0;
  font-size: 14px;
  font-weight: 600;
  color: #333;
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
}

.last-message-time {
  font-size: 11px;
  color: #999;
  flex-shrink: 0;
}

.conversation-preview {
  display: flex;
  justify-content: space-between;
  align-items: center;
  gap: 8px;
}

.last-message {
  margin: 0;
  font-size: 13px;
  color: #666;
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
  flex: 1;
}

.unread-badge {
  background: #fc4618;
  color: white;
  padding: 2px 6px;
  border-radius: 10px;
  font-size: 11px;
  font-weight: 600;
  flex-shrink: 0;
}

/* Zone de chat */
.chat-area {
  flex: 1;
  display: flex;
  flex-direction: column;
  background: white;
}

.chat-window {
  display: flex;
  flex-direction: column;
  height: 100%;
}

.chat-header {
  background: linear-gradient(160deg, #0c0c0c, #fc4618, #0c0c0c);
  color: white;
  padding: 16px 20px;
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

.action-btn {
  background: #ffffff33;
  border: none;
  color: white;
  width: 32px;
  height: 32px;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  cursor: pointer;
  transition: background 0.2s;
}

.action-btn:hover {
  background: #ffffff4d;
}

.chat-body {
  flex: 1;
  display: flex;
  flex-direction: column;
  overflow: hidden;
}

.messages-container {
  flex: 1;
  padding: 20px;
  overflow-y: auto;
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
  max-width: 70%;
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

.product-message-card {
  max-width: 80%;
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

.message-input-container {
  padding: 16px 20px;
  border-top: 1px solid #f0f0f0;
  background: white;
}

.input-wrapper {
  display: flex;
  gap: 8px;
  align-items: center;
}

.input-style {
  flex: 1;
  color: black;
  border: 1px solid #d1d5db;
  border-radius: 25px;
  padding: 12px 16px;
  transition: border-color 0.2s, box-shadow 0.2s;
  font-size: 14px;
}

.input-style:focus {
  outline: none;
  border-color: #fe7900;
  box-shadow: 0 0 0 0.5px #fe7900;
}

.send-btn {
  background: linear-gradient(160deg, #fe9700, #fc4618);
  border: none;
  color: white;
  width: 40px;
  height: 40px;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  cursor: pointer;
  transition: transform 0.2s;
  flex-shrink: 0;
}

.send-btn:hover:not(:disabled) {
  transform: scale(1.05);
}

.send-btn:disabled {
  opacity: 0.5;
  cursor: not-allowed;
}

.no-conversation-selected {
  flex: 1;
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  color: #999;
  gap: 16px;
}

.no-conversation-selected p {
  font-size: 16px;
  margin: 0;
}

/* Scrollbar */
.messages-container::-webkit-scrollbar,
.conversations-list::-webkit-scrollbar {
  width: 6px;
}

.messages-container::-webkit-scrollbar-track,
.conversations-list::-webkit-scrollbar-track {
  background: #f1f1f1;
}

.messages-container::-webkit-scrollbar-thumb,
.conversations-list::-webkit-scrollbar-thumb {
  background: #c1c1c1;
  border-radius: 3px;
}

.messages-container::-webkit-scrollbar-thumb:hover,
.conversations-list::-webkit-scrollbar-thumb:hover {
  background: #a8a8a8;
}
</style>