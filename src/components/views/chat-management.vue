<template>
  <div class="min-h-screen bg-gray-50 relative overflow-hidden">
    <!-- Fond & décorations (identique à ce que tu avais) -->
    <div class="absolute inset-0 overflow-hidden pointer-events-none z-0">
      <div class="absolute inset-0 bg-gradient-to-br from-gray-50 via-white to-gray-100"></div>
      <div class="absolute -top-20 -left-20 w-96 h-96 bg-gradient-to-br from-orange-200/60 to-orange-300/40 rounded-full blur-3xl animate-float-slow"></div>
      <div class="absolute top-1/4 -right-20 w-80 h-80 bg-gradient-to-br from-blue-200/50 to-indigo-200/35 rounded-full blur-3xl animate-float-reverse"></div>
      <div class="absolute -bottom-20 left-1/3 w-72 h-72 bg-gradient-to-br from-purple-200/45 to-pink-200/35 rounded-full blur-3xl animate-float-diagonal"></div>
      <div class="absolute top-1/2 right-1/4 w-64 h-64 bg-gradient-to-br from-emerald-200/40 to-teal-200/30 rounded-full blur-3xl animate-float-slow-reverse"></div>
      <div class="absolute top-1/3 left-1/4 w-3 h-3 bg-orange-400/70 rounded-full animate-pulse-slow shadow-lg"></div>
      <div class="absolute top-2/3 right-1/4 w-2.5 h-2.5 bg-blue-400/60 rounded-full animate-pulse-delayed shadow-lg"></div>
      <div class="absolute top-1/2 left-2/3 w-2 h-2 bg-purple-400/50 rounded-full animate-pulse-slow shadow-lg"></div>
      <div class="absolute top-1/4 right-1/3 w-2 h-2 bg-emerald-400/50 rounded-full animate-pulse-delayed-2 shadow-lg"></div>
      <div class="absolute top-0 left-1/4 w-px h-40 bg-gradient-to-b from-transparent via-orange-300/40 to-transparent animate-slide-down"></div>
      <div class="absolute top-1/3 right-1/3 w-32 h-px bg-gradient-to-r from-transparent via-blue-300/35 to-transparent animate-slide-right"></div>
      <div class="absolute bottom-1/4 left-1/2 w-px h-32 bg-gradient-to-b from-transparent via-purple-300/30 to-transparent animate-slide-up"></div>
      <div class="absolute top-3/4 left-1/6 w-16 h-16 bg-gradient-to-br from-orange-300/30 to-yellow-300/20 rotate-45 animate-rotate-slow"></div>
      <div class="absolute top-1/6 right-1/6 w-12 h-12 bg-gradient-to-br from-blue-300/25 to-cyan-300/20 rounded-lg animate-float-small"></div>
    </div>

    <!-- Contenu principal -->
    <div class="relative z-10 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 pb-32">
      <div class="page-header">
        <h1 class="section-title">Administration Chat</h1>
        <p class="section-subtitle">Gérez toutes les conversations clients en temps réel</p>
      </div>

      <div class="stats-grid">
        <div class="stat-card primary">
          <div class="stat-content">
            <div>
              <p class="stat-title">Conversations Actives</p>
              <p class="stat-value">{{ stats.activeConversations }}</p>
            </div>
            <div class="stat-icon">
              <!-- Icone -->
            </div>
          </div>
        </div>
        <div class="stat-card warning">
          <div class="stat-content">
            <div>
              <p class="stat-title">En Attente</p>
              <p class="stat-value">{{ stats.pendingConversations }}</p>
            </div>
            <div class="stat-icon">
            </div>
          </div>
        </div>
        <div class="stat-card success">
          <div class="stat-content">
            <div>
              <p class="stat-title">Messages Aujourd'hui</p>
              <p class="stat-value">{{ stats.todayMessages }}</p>
            </div>
            <div class="stat-icon">
            </div>
          </div>
        </div>
        <div class="stat-card info">
          <div class="stat-content">
            <div>
              <p class="stat-title">Temps Moyen Réponse</p>
              <p class="stat-value">{{ stats.avgResponseTime }} min</p>
            </div>
            <div class="stat-icon">
            </div>
          </div>
        </div>
      </div>

      <!-- Onglets -->
      <div class="tabs-container">
        <div class="tabs-header">
          <button
            v-for="tab in tabs"
            :key="tab.id"
            @click="activeTab = tab.id"
            :class="['tab-btn', { active: activeTab === tab.id }]"
          >
            <!-- Pour l'instant pas d'icône de composant -->
            <span class="tab-label">{{ tab.label }}</span>
            <span class="tab-count">{{ tab.count }}</span>
          </button>
        </div>

        <div class="tab-content">
          

          <div v-if="!loading && filteredConversations.length === 0" class="empty-state">
            <h3>Aucune conversation</h3>
            <p>Il n'y a pas de conversations {{ activeTab }} pour le moment.</p>
          </div>

          <div v-else class="conversations-grid">
            <div
              v-for="conversation in filteredConversations"
              :key="conversation.id"
              class="conversation-card"
              @click="openChatWindow(conversation)"
            >
              <div class="conversation-header">
                <div class="user-info">
                  <div class="user-avatar">
                    <span>{{ conversation.user?.name?.charAt(0).toUpperCase() || "?" }}</span>
                  </div>
                  <div>
                    <h3 class="user-name">{{ conversation.user?.name || "Utilisateur inconnu" }}</h3>
                    <p class="user-email">{{ conversation.user?.email || "" }}</p>
                  </div>
                </div>
                <div class="conversation-status">
                  <span :class="['status-badge', conversation.status]">
                    {{ getStatusLabel(conversation.status) }}
                  </span>
                </div>
              </div>
              <div class="conversation-preview">
                <p>{{ conversation.lastMessage.content }}</p>
                <span class="message-time">{{ formatTime(conversation.lastMessage.timestamp) }}</span>
              </div>
              <div class="conversation-actions">
                <button @click.stop="openChatWindow(conversation)" class="action-btn reply">Répondre</button>
                <button @click.stop="markAsResolved(conversation)" class="action-btn resolve">Résoudre</button>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Chat Windows Container -->
    <div class="chat-windows-container">
      <div
        v-for="chat in openChats"
        :key="chat.id"
        class="chat-window"
        :class="{ minimized: chat.minimized }"
      >
        <!-- Chat Header -->
        <div class="chat-header" @click="toggleChatWindow(chat.id)">
          <div class="chat-user-info">
            <div class="chat-avatar">
              <span>{{ chat.user?.name?.charAt(0).toUpperCase() || "?" }}</span>
            </div>
            <div class="chat-user-details">
              <h4>{{ chat.user?.name || "Utilisateur inconnu" }}</h4>
              <span class="chat-status" :class="chat.status">{{ getStatusLabel(chat.status) }}</span>
            </div>
          </div>
          <div class="chat-controls">
            <button @click.stop="toggleChatWindow(chat.id)" class="minimize-btn">
              {{ chat.minimized ? '▲' : '▼' }}
            </button>
            <button @click.stop="closeChatWindow(chat.id)" class="close-chat-btn">×</button>
          </div>
        </div>

        <!-- Chat Body -->
        <div v-if="!chat.minimized" class="chat-body">
          <!-- Messages Container -->
          <div class="chat-messages" ref="messagesContainer">
            <div
              v-for="msg in chat.messages"
              :key="msg.id"
              :class="['chat-message', msg.sender === 'admin' ? 'admin' : 'user']"
            >
              <div class="message-bubble">{{ msg.message }}</div>
              <div class="message-timestamp">{{ formatTime(msg.created_at) }}</div>
            </div>
          </div>

          <!-- Message Input -->
          <div class="chat-input-container">
            <textarea
              v-model="chat.replyMessage"
              @keydown.enter.prevent="sendChatReply(chat.id)"
              placeholder="Tapez votre message..."
              rows="2"
              class="chat-input"
            ></textarea>
            <button 
              @click="sendChatReply(chat.id)" 
              class="send-chat-btn"
              :disabled="!chat.replyMessage?.trim()"
            >
              Envoyer
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted, nextTick } from 'vue'
import AdminChatApiClient from '../../services/ChatApiClient'

const api = new AdminChatApiClient()

const loading = ref(false)
const activeTab = ref('active')
const openChats = ref([])
const pollingInterval = ref(null)

const stats = ref({
  activeConversations: 0,
  pendingConversations: 0,
  resolvedConversations: 0,
  todayMessages: 0,
  avgResponseTime: 0
})

const conversations = ref([])

const tabs = ref([
  { id: 'active', label: 'Actives', count: 0 },
  { id: 'pending', label: 'En attente', count: 0 },
  { id: 'resolved', label: 'Résolues', count: 0 }
])

const startPolling = () => {
  pollingInterval.value = setInterval(async () => {
    await loadConversations()
    for (const chat of openChats.value) {
      await refreshChatMessages(chat.id)
    }
  }, 3000)
}

const stopPolling = () => {
  if (pollingInterval.value) {
    clearInterval(pollingInterval.value)
    pollingInterval.value = null
  }
}

const loadConversations = async () => {
  loading.value = true
  try {
    const sessions = await api.getActiveSessions()

    if (!Array.isArray(sessions)) {
      if (conversations.value.length > 0) {
        conversations.value = []
      }
    } else {
      if (sessions.length !== conversations.value.length) {
        conversations.value = sessions.map(s => {
          const normalizedStatus = ['active', 'pending', 'resolved'].includes(s.status)
            ? s.status
            : 'active'

          return {
            id: s.session_id,
            user: {
              name: s.user_code || `Code: ${s.session_id}`,
              email: s.ip_address || ''
            },
            status: normalizedStatus,
            lastMessage: {
              content: s.last_message_content || '',
              timestamp: s.created_at ? new Date(s.created_at) : new Date()
            },
            messages: []
          }
        })

        tabs.value.forEach(tab => {
          tab.count = conversations.value.filter(c => c.status === tab.id).length
        })
        stats.value.activeConversations = tabs.value.find(t => t.id === 'active')?.count || 0
        stats.value.pendingConversations = tabs.value.find(t => t.id === 'pending')?.count || 0
        stats.value.resolvedConversations = tabs.value.find(t => t.id === 'resolved')?.count || 0
      }
    }

  } catch (e) {
    console.error('Erreur chargement des conversations:', e)
  } finally {
    loading.value = false
  }
}

const refreshChatMessages = async (chatId) => {
  const chat = openChats.value.find(c => c.id === chatId)
  if (!chat) return

  try {
    const resp = await api.getMessages(chatId)
    const msgs = Array.isArray(resp) ? resp : []
    
    if (msgs.length !== chat.messages.length) {
      const formatted = msgs.map(msg => ({
        ...msg,
        timestamp: msg.created_at ? new Date(msg.created_at) : new Date()
      }))

      chat.messages = formatted
      await nextTick()
      scrollToBottom(chatId)
    }
  } catch (e) {
    console.error('Erreur refresh messages:', e)
  }
}

const loadConversationMessages = async (conversation) => {
  if (!conversation || !conversation.id) return
  try {
    const resp = await api.getMessages(conversation.id)
    const msgs = Array.isArray(resp) ? resp : []
    
    if (msgs.length !== conversation.messages.length) {
      const formatted = msgs.map(msg => ({
        ...msg,
        timestamp: msg.created_at ? new Date(msg.created_at) : new Date()
      }))

      conversation.messages = formatted

      if (formatted.length > 0) {
        const last = formatted[formatted.length - 1]
        conversation.lastMessage = {
          content: last.message,
          timestamp: last.timestamp
        }
      }
    }
  } catch (e) {
    console.error('Erreur chargement des messages:', e)
  }
}

const openChatWindow = async (conversation) => {
  const existingChat = openChats.value.find(c => c.id === conversation.id)
  if (existingChat) {
    existingChat.minimized = false
    return
  }

  await loadConversationMessages(conversation)

  openChats.value.push({
    ...conversation,
    minimized: false,
    replyMessage: ''
  })

  await nextTick()
  scrollToBottom(conversation.id)
}

const closeChatWindow = (chatId) => {
  const index = openChats.value.findIndex(c => c.id === chatId)
  if (index !== -1) {
    openChats.value.splice(index, 1)
  }
}

const toggleChatWindow = (chatId) => {
  const chat = openChats.value.find(c => c.id === chatId)
  if (chat) {
    chat.minimized = !chat.minimized
  }
}

const sendChatReply = async (chatId) => {
  const chat = openChats.value.find(c => c.id === chatId)
  if (!chat || !chat.replyMessage?.trim()) return

  try {
    await api.sendAdminReply(chatId, chat.replyMessage)
    
    chat.messages.push({
      id: Date.now(),
      message: chat.replyMessage,
      sender: 'admin',
      timestamp: new Date(),
      created_at: new Date().toISOString()
    })

    chat.lastMessage = {
      content: chat.replyMessage,
      timestamp: new Date()
    }

    chat.replyMessage = ''
    
    await nextTick()
    scrollToBottom(chatId)
  } catch (e) {
    console.error('Erreur envoi réponse admin:', e)
  }
}

const scrollToBottom = (chatId) => {
  const messagesContainer = document.querySelector(`[data-chat-id="${chatId}"] .chat-messages`)
  if (messagesContainer) {
    messagesContainer.scrollTop = messagesContainer.scrollHeight
  }
}

const filteredConversations = computed(() => {
  return conversations.value.filter(conv => conv.status === activeTab.value)
})

const markAsResolved = async (conversation) => {
  try {
    await api.closeSession(conversation.id)
    conversation.status = 'resolved'
    tabs.value.forEach(tab => {
      tab.count = conversations.value.filter(c => c.status === tab.id).length
    })
    
    closeChatWindow(conversation.id)
  } catch (e) {
    console.error('Erreur fermeture session:', e)
  }
}

const getStatusLabel = (status) => {
  const labels = {
    active: 'Active',
    pending: 'En attente',
    resolved: 'Résolue'
  }
  return labels[status] || status
}

const formatTime = (timestamp) => {
  if (!timestamp) return ''
  const d = new Date(timestamp)
  return new Intl.DateTimeFormat('fr-FR', {
    hour: '2-digit',
    minute: '2-digit'
  }).format(d)
}

onMounted(async () => {
  await loadConversations()
  startPolling()
})

onUnmounted(() => {
  stopPolling()
})
</script>

<style scoped>
.page-header {
  text-align: center;
  margin-bottom: 32px;
}

.section-title {
  font-size: 36px;
  font-weight: 700;
  margin-bottom: 8px;
  color: #1e293b;
}

.section-subtitle {
  font-size: 18px;
  color: #64748b;
  margin: 0;
}

.stats-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
  gap: 24px;
  margin-bottom: 32px;
}

.stat-card {
  padding: 24px;
  border-radius: 16px;
  background: rgba(255, 255, 255, 0.9);
  backdrop-filter: blur(20px);
  border: 1px solid rgba(255, 255, 255, 0.3);
  transition: all 0.3s ease;
  box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
}

.stat-card:hover {
  transform: translateY(-4px);
  box-shadow: 0 12px 35px rgba(0, 0, 0, 0.15);
}

.stat-card.primary {
  background: linear-gradient(135deg, rgba(59, 130, 246, 0.1) 0%, rgba(59, 130, 246, 0.05) 100%);
  border-color: rgba(59, 130, 246, 0.3);
}

.stat-card.success {
  background: linear-gradient(135deg, rgba(34, 197, 94, 0.1) 0%, rgba(34, 197, 94, 0.05) 100%);
  border-color: rgba(34, 197, 94, 0.3);
}

.stat-card.warning {
  background: linear-gradient(135deg, rgba(245, 158, 11, 0.1) 0%, rgba(245, 158, 11, 0.05) 100%);
  border-color: rgba(245, 158, 11, 0.3);
}

.stat-card.info {
  background: linear-gradient(135deg, rgba(102, 126, 234, 0.1) 0%, rgba(102, 126, 234, 0.05) 100%);
  border-color: rgba(102, 126, 234, 0.3);
}

.stat-content {
  display: flex;
  align-items: center;
  justify-content: space-between;
}

.stat-title {
  font-size: 14px;
  font-weight: 500;
  color: #64748b;
  margin: 0 0 8px 0;
}

.stat-value {
  font-size: 32px;
  font-weight: 700;
  color: #1e293b;
}

.stat-icon {
  padding: 12px;
  border-radius: 12px;
  background: linear-gradient(135deg, #667eea, #764ba2);
  color: white;
  display: flex;
  align-items: center;
  justify-content: center;
}

.tabs-container {
  background: rgba(255, 255, 255, 0.95);
  backdrop-filter: blur(20px);
  border-radius: 20px;
  border: 1px solid rgba(255, 255, 255, 0.3);
  box-shadow: 0 12px 35px rgba(0, 0, 0, 0.1);
  overflow: hidden;
}

.tabs-header {
  display: flex;
  background: rgba(0, 0, 0, 0.02);
  border-bottom: 1px solid rgba(0, 0, 0, 0.05);
}

.tab-btn {
  flex: 1;
  padding: 16px 24px;
  border: none;
  background: transparent;
  color: #64748b;
  font-size: 14px;
  font-weight: 500;
  cursor: pointer;
  transition: all 0.3s ease;
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 8px;
}

.tab-btn:hover {
  background: rgba(102, 126, 234, 0.05);
  color: #667eea;
}

.tab-btn.active {
  background: linear-gradient(135deg, #667eea, #764ba2);
  color: white;
}

.tab-count {
  background: rgba(255, 255, 255, 0.2);
  padding: 2px 8px;
  border-radius: 12px;
  font-size: 12px;
  font-weight: 600;
}

.tab-btn.active .tab-count {
  background: rgba(255, 255, 255, 0.3);
}

.tab-content {
  padding: 32px;
}

.loading-container {
  display: flex;
  justify-content: center;
  align-items: center;
  min-height: 400px;
}

.loading-spinner {
  text-align: center;
  color: #64748b;
}

.loading-spinner svg {
  width: 48px;
  height: 48px;
  margin-bottom: 16px;
  color: #667eea;
}

.empty-state {
  text-align: center;
  padding: 60px 20px;
  color: #64748b;
}

.empty-state svg {
  width: 64px;
  height: 64px;
  margin-bottom: 24px;
  color: #cbd5e1;
}

.empty-state h3 {
  font-size: 20px;
  font-weight: 600;
  margin-bottom: 8px;
  color: #475569;
}

.conversations-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
  gap: 20px;
}

.conversation-card {
  background: white;
  border-radius: 16px;
  padding: 20px;
  cursor: pointer;
  transition: all 0.3s ease;
  box-shadow: 0 2px 12px rgba(0, 0, 0, 0.08);
  border: 1px solid #f1f5f9;
}

.conversation-card:hover {
  transform: translateY(-4px);
  box-shadow: 0 8px 24px rgba(0, 0, 0, 0.12);
  border-color: #667eea;
}

.conversation-header {
  display: flex;
  justify-content: space-between;
  align-items: flex-start;
  margin-bottom: 16px;
}

.user-info {
  display: flex;
  align-items: center;
  gap: 12px;
  flex: 1;
  min-width: 0;
}

.user-avatar {
  width: 44px;
  height: 44px;
  border-radius: 50%;
  background: linear-gradient(135deg, #667eea, #764ba2);
  color: white;
  display: flex;
  align-items: center;
  justify-content: center;
  font-weight: 600;
  font-size: 16px;
  flex-shrink: 0;
}

.user-name {
  font-size: 16px;
  font-weight: 600;
  color: #1e293b;
  margin: 0 0 4px 0;
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
}

.user-email {
  font-size: 13px;
  color: #64748b;
  margin: 0;
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
}

.conversation-status {
  flex-shrink: 0;
}

.status-badge {
  padding: 6px 12px;
  border-radius: 20px;
  font-size: 12px;
  font-weight: 600;
  text-transform: capitalize;
}

.status-badge.active {
  background: linear-gradient(135deg, rgba(34, 197, 94, 0.15) 0%, rgba(34, 197, 94, 0.1) 100%);
  color: #16a34a;
}

.status-badge.pending {
  background: linear-gradient(135deg, rgba(245, 158, 11, 0.15) 0%, rgba(245, 158, 11, 0.1) 100%);
  color: #d97706;
}

.status-badge.resolved {
  background: linear-gradient(135deg, rgba(100, 116, 139, 0.15) 0%, rgba(100, 116, 139, 0.1) 100%);
  color: #475569;
}

.conversation-preview {
  margin-bottom: 16px;
  padding: 12px;
  background: #f8fafc;
  border-radius: 12px;
}

.conversation-preview p {
  font-size: 14px;
  color: #475569;
  margin: 0 0 8px 0;
  line-height: 1.5;
  display: -webkit-box;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
}

.message-time {
  font-size: 12px;
  color: #94a3b8;
  font-weight: 500;
}

.conversation-actions {
  display: flex;
  gap: 8px;
}

.action-btn {
  flex: 1;
  padding: 10px 16px;
  border: none;
  border-radius: 10px;
  font-size: 13px;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.2s ease;
}

.action-btn.reply {
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  color: white;
}

.action-btn.reply:hover {
  background: linear-gradient(135deg, #5568d3 0%, #6a3f8f 100%);
  transform: translateY(-1px);
  box-shadow: 0 4px 12px rgba(102, 126, 234, 0.3);
}

.action-btn.resolve {
  background: #f1f5f9;
  color: #475569;
}

.action-btn.resolve:hover {
  background: #e2e8f0;
  color: #1e293b;
}

.chat-windows-container {
  position: fixed;
  bottom: 0;
  right: 24px;
  z-index: 1000;
  display: flex;
  gap: 16px;
  flex-direction: row-reverse;
  max-width: calc(100vw - 48px);
  overflow-x: auto;
  padding-bottom: 0;
}

.chat-window {
  width: 380px;
  background: white;
  border-radius: 16px 16px 0 0;
  box-shadow: 0 -4px 24px rgba(0, 0, 0, 0.15);
  display: flex;
  flex-direction: column;
  transition: all 0.3s ease;
  border: 1px solid #e2e8f0;
  border-bottom: none;
}

.chat-window.minimized {
  height: 60px;
}

.chat-window:not(.minimized) {
  height: 500px;
}

.chat-header {
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  color: white;
  padding: 16px;
  cursor: pointer;
  display: flex;
  justify-content: space-between;
  align-items: center;
  border-radius: 16px 16px 0 0;
  user-select: none;
}

.chat-header:hover {
  background: linear-gradient(135deg, #5568d3 0%, #6a3f8f 100%);
}

.chat-user-info {
  display: flex;
  align-items: center;
  gap: 12px;
  flex: 1;
  min-width: 0;
}

.chat-avatar {
  width: 36px;
  height: 36px;
  border-radius: 50%;
  background: rgba(255, 255, 255, 0.3);
  display: flex;
  align-items: center;
  justify-content: center;
  font-weight: 600;
  font-size: 14px;
  flex-shrink: 0;
}

.chat-user-details {
  flex: 1;
  min-width: 0;
}

.chat-user-details h4 {
  font-size: 15px;
  font-weight: 600;
  margin: 0 0 2px 0;
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
}

.chat-status {
  font-size: 12px;
  opacity: 0.9;
  padding: 2px 8px;
  border-radius: 10px;
  background: rgba(255, 255, 255, 0.2);
  display: inline-block;
}

.chat-controls {
  display: flex;
  gap: 8px;
  align-items: center;
}

.minimize-btn,
.close-chat-btn {
  background: rgba(255, 255, 255, 0.2);
  border: none;
  color: white;
  width: 28px;
  height: 28px;
  border-radius: 8px;
  cursor: pointer;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 18px;
  font-weight: 600;
  transition: all 0.2s ease;
  flex-shrink: 0;
}

.minimize-btn:hover,
.close-chat-btn:hover {
  background: rgba(255, 255, 255, 0.3);
  transform: scale(1.05);
}

.chat-body {
  display: flex;
  flex-direction: column;
  flex: 1;
  overflow: hidden;
}

.chat-messages {
  flex: 1;
  overflow-y: auto;
  padding: 20px;
  display: flex;
  flex-direction: column;
  gap: 16px;
  background: #f8fafc;
}

.chat-message {
  display: flex;
  flex-direction: column;
  max-width: 70%;
  animation: slideIn 0.3s ease;
}

.chat-message.user {
  align-self: flex-start;
}

.chat-message.user .message-bubble {
  background: white;
  color: #1e293b;
  border: 1px solid #e2e8f0;
  border-radius: 18px 18px 18px 4px;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
}

.chat-message.admin {
  align-self: flex-end;
}

.chat-message.admin .message-bubble {
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  color: white;
  border-radius: 18px 18px 4px 18px;
  box-shadow: 0 2px 12px rgba(102, 126, 234, 0.3);
}

.message-bubble {
  padding: 12px 16px;
  font-size: 14px;
  line-height: 1.5;
  word-wrap: break-word;
  transition: all 0.2s ease;
}

.message-bubble:hover {
  transform: translateY(-1px);
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.12);
}

.message-timestamp {
  font-size: 12px;
  color: #94a3b8;
  margin-top: 4px;
  padding: 0 4px;
}

.chat-message.user .message-timestamp {
  align-self: flex-start;
}

.chat-message.admin .message-timestamp {
  align-self: flex-end;
}

.chat-input-container {
  padding: 16px;
  background: white;
  border-top: 1px solid #e2e8f0;
  display: flex;
  gap: 12px;
  align-items: flex-end;
}

.chat-input {
  flex: 1;
  padding: 12px 16px;
  border: 1px solid #e2e8f0;
  border-radius: 12px;
  font-size: 14px;
  resize: none;
  font-family: inherit;
  transition: all 0.2s ease;
  background: #f8fafc;
}

.chat-input:focus {
  outline: none;
  border-color: #667eea;
  background: white;
  box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
}

.send-chat-btn {
  padding: 12px 20px;
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  color: white;
  border: none;
  border-radius: 12px;
  font-size: 14px;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.2s ease;
  white-space: nowrap;
}

.send-chat-btn:hover:not(:disabled) {
  background: linear-gradient(135deg, #5568d3 0%, #6a3f8f 100%);
  transform: translateY(-1px);
  box-shadow: 0 4px 12px rgba(102, 126, 234, 0.3);
}

.send-chat-btn:disabled {
  opacity: 0.5;
  cursor: not-allowed;
}

@media (max-width: 768px) {
  .conversations-grid {
    grid-template-columns: 1fr;
  }
  
  .chat-window {
    width: 100%;
    max-width: 100vw;
  }
  
  .chat-windows-container {
    right: 0;
    left: 0;
    max-width: 100vw;
  }
}

@keyframes float-slow {
  0%, 100% { transform: translate(0, 0) rotate(0deg); }
  33% { transform: translate(30px, -30px) rotate(120deg); }
  66% { transform: translate(-20px, 20px) rotate(240deg); }
}

@keyframes float-reverse {
  0%, 100% { transform: translate(0, 0) rotate(0deg); }
  33% { transform: translate(-30px, 30px) rotate(-120deg); }
  66% { transform: translate(20px, -20px) rotate(-240deg); }
}

@keyframes float-diagonal {
  0%, 100% { transform: translate(0, 0); }
  50% { transform: translate(40px, 40px); }
}

@keyframes float-slow-reverse {
  0%, 100% { transform: translate(0, 0); }
  50% { transform: translate(-30px, -30px); }
}

@keyframes pulse-slow {
  0%, 100% { opacity: 0.4; transform: scale(1); }
  50% { opacity: 1; transform: scale(1.5); }
}

@keyframes pulse-delayed {
  0%, 100% { opacity: 0.3; transform: scale(1); }
  50% { opacity: 0.8; transform: scale(1.4); }
}

@keyframes pulse-delayed-2 {
  0%, 100% { opacity: 0.5; transform: scale(1); }
  50% { opacity: 1; transform: scale(1.3); }
}

@keyframes slide-down {
  0% { transform: translateY(-100%); opacity: 0; }
  50% { opacity: 1; }
  100% { transform: translateY(100%); opacity: 0; }
}

@keyframes slide-right {
  0% { transform: translateX(-100%); opacity: 0; }
  50% { opacity: 1; }
  100% { transform: translateX(100%); opacity: 0; }
}

@keyframes slide-up {
  0% { transform: translateY(100%); opacity: 0; }
  50% { opacity: 1; }
  100% { transform: translateY(-100%); opacity: 0; }
}

@keyframes rotate-slow {
  from { transform: rotate(0deg); }
  to { transform: rotate(360deg); }
}

@keyframes float-small {
  0%, 100% { transform: translateY(0); }
  50% { transform: translateY(-20px); }
}

@keyframes slideIn {
  from {
    opacity: 0;
    transform: translateY(10px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}
</style>
