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
  gap: 24px;
}

.conversation-card {
  background: white;
  border-radius: 16px;
  padding: 20px;
  cursor: pointer;
  transition: all 0.3s ease;
  box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
}

.conversation-card:hover {
  transform: translateY(-4px);
  box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
}

.conversation-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 16px;
}

.user-info {
  display: flex;
  align-items: center;
  gap: 12px;
}

.user-avatar {
  width: 40px;
  height: 40px;
  border-radius: 50%;
  background: linear-gradient(135deg, #667eea, #764ba2);
  color: white;
  display: flex;
  align-items: center;
  justify-content: center;
  font-weight: 600;
}

.user-name {
  font-size: 16px;
  font-weight: 600;
  color: #1e293b;
  margin: 0 0 4px 0;
}

.user-email {
  font-size: 14px;
  color: #64748b;
  margin: 0;
}

.status-badge {
  padding: 4px 12px;
  border-radius: 20px;
  font-size: 12px;
  font-weight: 500;
}

.status-badge.active {
  background: rgba(34, 197, 94, 0.1);
  color: #22c55e;
}

.status-badge.pending {
  background: rgba(245, 158, 11, 0.1);
  color: #f59e0b;
}

.status-badge.resolved {
  background: rgba(100, 116, 139, 0.1);
  color: #64748b;
}

.conversation-preview {
  margin-bottom: 16px;
}

.conversation-preview p {
  font-size: 14px;
  color: #475569;
  margin: 0 0 8px 0;
  line-height: 1.5;
}

.message-time {
  font-size: 12px;
  color: #94a3b8;
}

.conversation-actions {
  display: flex;
  gap: 8px;
  justify-content: flex-end;
}

.action-btn {
  width: 32px;
  height: 32px;
  border: none;
  border-radius: 8px;
  cursor: pointer;
  transition: all 0.3s ease;
  display: flex;
  align-items: center;
  justify-content: center;
}

.action-btn.reply {
  background: rgba(59, 130, 246, 0.1);
  color: #3b82f6;
}

.action-btn.reply:hover {
  background: rgba(59, 130, 246, 0.2);
}

.action-btn.resolve {
  background: rgba(34, 197, 94, 0.1);
  color: #22c55e;
}

.action-btn.resolve:hover {
  background: rgba(34, 197, 94, 0.2);
}

.chat-windows-container {
  position: fixed;
  bottom: 0;
  right: 20px;
  display: flex;
  gap: 10px;
  z-index: 1000;
  max-width: calc(100vw - 40px);
  overflow-x: auto;
}

.chat-window {
  width: 350px;
  background: white;
  border-radius: 12px 12px 0 0;
  box-shadow: 0 -4px 20px rgba(0, 0, 0, 0.15);
  border: 1px solid #e2e8f0;
  border-bottom: none;
  transition: all 0.3s ease;
}

.chat-window.minimized {
  height: 50px;
}

.chat-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 12px 16px;
  background: linear-gradient(135deg, #667eea, #764ba2);
  color: white;
  cursor: pointer;
  border-radius: 12px 12px 0 0;
}

.chat-user-info {
  display: flex;
  align-items: center;
  gap: 10px;
}

.chat-avatar {
  width: 32px;
  height: 32px;
  border-radius: 50%;
  background: rgba(255, 255, 255, 0.2);
  display: flex;
  align-items: center;
  justify-content: center;
  font-weight: 600;
  font-size: 14px;
}

.chat-user-details h4 {
  margin: 0;
  font-size: 14px;
  font-weight: 600;
}

.chat-status {
  font-size: 11px;
  opacity: 0.8;
}

.chat-controls {
  display: flex;
  gap: 8px;
}

.minimize-btn,
.close-chat-btn {
  background: rgba(255, 255, 255, 0.2);
  border: none;
  color: white;
  width: 24px;
  height: 24px;
  border-radius: 4px;
  cursor: pointer;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 12px;
  transition: background 0.2s;
}

.minimize-btn:hover,
.close-chat-btn:hover {
  background: rgba(255, 255, 255, 0.3);
}

.chat-body {
  height: 400px;
  display: flex;
  flex-direction: column;
}

.chat-messages {
  flex: 1;
  padding: 16px;
  overflow-y: auto;
  max-height: 300px;
}

.chat-message {
  margin-bottom: 12px;
  display: flex;
  flex-direction: column;
}

.chat-message.admin {
  align-items: flex-end;
}

.chat-message.user {
  align-items: flex-start;
}

.message-bubble {
  background: #f1f5f9;
  padding: 8px 12px;
  border-radius: 12px;
  max-width: 80%;
  font-size: 14px;
  line-height: 1.4;
  word-wrap: break-word;
}

.chat-message.admin .message-bubble {
  background: linear-gradient(135deg, #667eea, #764ba2);
  color: white;
}

.message-timestamp {
  font-size: 11px;
  color: #94a3b8;
  margin-top: 4px;
}

.chat-input-container {
  padding: 12px 16px;
  border-top: 1px solid #e2e8f0;
  display: flex;
  gap: 8px;
  align-items: flex-end;
}

.chat-input {
  flex: 1;
  border: 1px solid #e2e8f0;
  border-radius: 8px;
  padding: 8px 12px;
  font-size: 14px;
  resize: none;
  min-height: 36px;
  max-height: 80px;
}

.chat-input:focus {
  outline: none;
  border-color: #667eea;
}

.send-chat-btn {
  background: linear-gradient(135deg, #667eea, #764ba2);
  color: white;
  border: none;
  padding: 8px 16px;
  border-radius: 8px;
  font-size: 12px;
  font-weight: 500;
  cursor: pointer;
  transition: all 0.2s;
  white-space: nowrap;
}

.send-chat-btn:hover:not(:disabled) {
  transform: translateY(-1px);
  box-shadow: 0 4px 12px rgba(102, 126, 234, 0.3);
}

.send-chat-btn:disabled {
  opacity: 0.5;
  cursor: not-allowed;
}

@media (max-width: 768px) {
  .chat-windows-container {
    right: 10px;
    left: 10px;
    max-width: none;
  }
  
  .chat-window {
    width: 100%;
    max-width: 300px;
  }
}

@keyframes float-slow {
  0%, 100% { transform: translateY(0px) translateX(0px) rotate(0deg); }
  25% { transform: translateY(-30px) translateX(15px) rotate(2deg); }
  50% { transform: translateY(-15px) translateX(-20px) rotate(-1deg); }
  75% { transform: translateY(-40px) translateX(8px) rotate(1deg); }
}

@keyframes float-reverse {
  0%, 100% { transform: translateY(0px) translateX(0px) rotate(0deg); }
  25% { transform: translateY(20px) translateX(-15px) rotate(-2deg); }
  50% { transform: translateY(35px) translateX(25px) rotate(1deg); }
  75% { transform: translateY(8px) translateX(-8px) rotate(-1deg); }
}

@keyframes float-diagonal {
  0%, 100% { transform: translateY(0px) translateX(0px) scale(1) rotate(0deg); }
  33% { transform: translateY(-25px) translateX(20px) scale(1.1) rotate(1deg); }
  66% { transform: translateY(15px) translateX(-15px) scale(0.9) rotate(-1deg); }
}

@keyframes float-slow-reverse {
  0%, 100% { transform: translateY(0px) translateX(0px) rotate(0deg); }
  25% { transform: translateY(-15px) translateX(-20px) rotate(-1deg); }
  50% { transform: translateY(25px) translateX(10px) rotate(2deg); }
  75% { transform: translateY(-10px) translateX(-5px) rotate(-0.5deg); }
}

@keyframes pulse-slow {
  0%, 100% { opacity: 0.4; transform: scale(1); }
  50% { opacity: 0.8; transform: scale(1.4); }
}

@keyframes pulse-delayed {
  0%, 100% { opacity: 0.3; transform: scale(1); }
  50% { opacity: 0.7; transform: scale(1.5); }
}

@keyframes pulse-delayed-2 {
  0%, 100% { opacity: 0.25; transform: scale(1); }
  50% { opacity: 0.6; transform: scale(1.3); }
}

@keyframes slide-down {
  0% { transform: translateY(-100%); opacity: 0; }
  50% { opacity: 1; }
  100% { transform: translateY(300%); opacity: 0; }
}

@keyframes slide-right {
  0% { transform: translateX(-100%); opacity: 0; }
  50% { opacity: 1; }
  100% { transform: translateX(300%); opacity: 0; }
}

@keyframes slide-up {
  0% { transform: translateY(100%); opacity: 0; }
  50% { opacity: 1; }
  100% { transform: translateY(-200%); opacity: 0; }
}

@keyframes rotate-slow {
  0% { transform: rotate(0deg); }
  100% { transform: rotate(360deg); }
}

@keyframes float-small {
  0%, 100% { transform: translateY(0px); }
  50% { transform: translateY(-10px); }
}

.animate-float-slow { animation: float-slow 20s ease-in-out infinite; }
.animate-float-reverse { animation: float-reverse 25s ease-in-out infinite; }
.animate-float-diagonal { animation: float-diagonal 18s ease-in-out infinite; }
.animate-float-slow-reverse { animation: float-slow-reverse 22s ease-in-out infinite; }
.animate-pulse-slow { animation: pulse-slow 4s ease-in-out infinite; }
.animate-pulse-delayed { animation: pulse-delayed 5s ease-in-out infinite 1s; }
.animate-pulse-delayed-2 { animation: pulse-delayed-2 6s ease-in-out infinite 2s; }
.animate-slide-down { animation: slide-down 8s linear infinite; }
.animate-slide-right { animation: slide-right 10s linear infinite; }
.animate-slide-up { animation: slide-up 9s linear infinite; }
.animate-rotate-slow { animation: rotate-slow 30s linear infinite; }
.animate-float-small { animation: float-small 3s ease-in-out infinite; }
</style>
