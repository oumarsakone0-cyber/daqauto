<template>
  <div class="admin-chat-container">
    <!-- Sidebar des conversations -->
    <div class="conversations-sidebar">
      <div class="sidebar-header">
        <h3>Conversations Clients</h3>
        <div v-if="unreadCount > 0" class="unread-badge">
          {{ unreadCount }}
        </div>
      </div>

      <div class="conversations-list">
        <div
          v-for="conversation in chatStore.conversations"
          :key="conversation.id"
          :class="['conversation-item', { active: conversation.id === chatStore.activeConversationId }]"
          @click="chatStore.setActiveConversation(conversation.id)"
        >
          <div class="conversation-avatar">
            <div class="user-icon-wrapper">
              <svg class="user-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
              </svg>
            </div>
            <div v-if="conversation.online" class="online-indicator"></div>
          </div>

          <div class="conversation-info">
            <div class="conversation-header">
              <h4 class="conversation-name">{{ conversation.user_name }}</h4>
              <span class="conversation-time">{{ formatTime(conversation.lastMessageTime) }}</span>
            </div>
            <div class="conversation-preview">
              <p class="last-message">{{ conversation.lastMessage }}</p>
              <div v-if="conversation.unread > 0" class="unread-count">{{ conversation.unread }}</div>
            </div>
            <div v-if="conversation.product_name" class="product-info">
              <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M20 7h-9M14 17H5M13 12l-8 5V7l8 5z"/>
              </svg>
              <span>{{ conversation.product_name }}</span>
            </div>
          </div>
        </div>

        <div v-if="chatStore.conversations.length === 0" class="no-conversations">
          <svg width="64" height="64" viewBox="0 0 24 24" fill="none" stroke="#e5e7eb" stroke-width="1.5">
            <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/>
          </svg>
          <p>Aucune conversation</p>
        </div>
      </div>
    </div>

    <!-- Zone de chat principale -->
    <div class="chat-main">
      <div v-if="!chatStore.activeConversationId" class="no-chat-selected">
        <svg width="80" height="80" viewBox="0 0 24 24" fill="none" stroke="#e5e7eb" stroke-width="1.5">
          <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/>
        </svg>
        <h3>Sélectionnez une conversation</h3>
        <p>Choisissez un client dans la liste pour commencer à discuter</p>
      </div>

      <div v-else class="chat-content">
        <!-- En-tête du chat -->
        <div class="chat-header">
          <div class="chat-client-info">
            <div class="client-avatar">
              <div class="user-icon-wrapper-large">
                <svg class="user-icon-large" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                </svg>
              </div>
              <div v-if="activeConversation?.online" class="online-indicator"></div>
            </div>
            <div>
              <h3>{{ activeConversation?.user_name }}</h3>
              <p class="client-status">{{ activeConversation?.status }}</p>
            </div>
          </div>
        </div>

        <!-- Messages -->
        <div class="messages-container" ref="messagesContainer">
          <div v-for="message in chatStore.chatMessages" :key="message.id" :class="['message-wrapper', message.sender]">

            <!-- Message produit -->
            <div v-if="message.type === 'product'" class="product-message-card">
              <div class="product-image">
                <img :src="message.product?.image || JSON.parse(message.message).image" alt="Product">
              </div>
              <div class="product-details">
                <h4>{{ message.product?.name || JSON.parse(message.message).name }}</h4>
                <p class="product-price">{{ formatPrice(message.product?.price || JSON.parse(message.message).price) }}</p>
                <div class="product-meta">
                  <span>⭐ {{ message.product?.rating || JSON.parse(message.message).rating || 'N/A' }}</span>
                  <span>{{ message.product?.shop || JSON.parse(message.message).shop }}</span>
                </div>
              </div>
            </div>

            <!-- Message image -->
            <div v-else-if="message.message_type === 'image'" class="image-wrapper">
              <img
                :src="message.message"
                alt="Image partagée"
                class="chat-image"
                @click="openImage(message.message)"
              />
              <span class="message-timestamp">{{ formatTime(message.timestamp) }}</span>
            </div>

            <!-- Message texte -->
            <div v-else class="message-bubble">
              <p class="message-text">{{ message.message || message.text }}</p>
              <span class="message-timestamp">{{ formatTime(message.timestamp) }}</span>
            </div>
          </div>
        </div>

        <!-- Zone de saisie -->
        <div class="chat-input-area">
          <div class="input-container">
            <!-- Bouton upload image -->
            <button
              @click="triggerFileInput"
              class="upload-button"
              title="Envoyer une image"
            >
              <svg class="upload-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
              </svg>
            </button>

            <input
              ref="fileInput"
              type="file"
              accept="image/*"
              class="hidden-file-input"
              @change="handleImageUpload"
            />

            <textarea
              v-model="newMessage"
              @keydown.enter.exact.prevent="handleSendMessage"
              placeholder="Tapez votre message..."
              rows="1"
              class="message-input"
            ></textarea>
            <button @click="handleSendMessage" :disabled="!newMessage.trim()" class="send-button">
              <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <line x1="22" y1="2" x2="11" y2="13"></line>
                <polygon points="22 2 15 22 11 13 2 9 22 2"></polygon>
              </svg>
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted, nextTick, watch } from 'vue'
import { useChatAdminStore } from '../../../stores/chatAdmin'
import { useImageUpload } from '../../../composables/useImageUpload'

const chatStore = useChatAdminStore()
const newMessage = ref('')
const messagesContainer = ref(null)
const supplierId = ref(null)
const fileInput = ref(null)

const { uploadChatImage } = useImageUpload()

const activeConversation = computed(() => {
  return chatStore.conversations.find(c => c.id === chatStore.activeConversationId)
})

const unreadCount = computed(() => {
  return chatStore.unreadCount
})

// Récupérer l'ID du fournisseur depuis localStorage
const initSupplier = () => {
  const userRaw = localStorage.getItem('user') || sessionStorage.getItem('user')
  if (userRaw) {
    try {
      const user = JSON.parse(userRaw)
      supplierId.value = user.id
      return user.id
    } catch (error) {
      console.error('❌ Erreur parsing user:', error)
    }
  }
  return null
}

const triggerFileInput = () => {
  fileInput.value?.click()
}

const handleImageUpload = async (event) => {
  const file = event.target.files?.[0]
  if (!file || !supplierId.value) return

  try {
    // Upload l'image vers le serveur
    const imageUrl = await uploadChatImage(file)

    if (imageUrl) {
      // Envoyer l'image dans le chat via le store admin
      await chatStore.sendImageMessage(imageUrl, file.name, supplierId.value)

      await nextTick()
      scrollToBottom()
    }
  } catch (error) {
    console.error('❌ Erreur upload image:', error)
    alert('Erreur lors de l\'upload de l\'image. Veuillez réessayer.')
  }

  // Reset l'input
  event.target.value = ''
}

const openImage = (url) => {
  window.open(url, '_blank')
}

const handleSendMessage = async () => {
  if (!newMessage.value.trim() || !supplierId.value) return

  await chatStore.sendMessage(newMessage.value, supplierId.value)
  newMessage.value = ''

  await nextTick()
  scrollToBottom()
}

const scrollToBottom = () => {
  if (messagesContainer.value) {
    messagesContainer.value.scrollTop = messagesContainer.value.scrollHeight
  }
}

const formatTime = (timestamp) => {
  if (!timestamp) return ''
  const date = new Date(timestamp)
  const now = new Date()
  const diff = now - date

  if (diff < 60000) return 'À l\'instant'
  if (diff < 3600000) return `${Math.floor(diff / 60000)} min`
  if (diff < 86400000) return date.toLocaleTimeString('fr-FR', { hour: '2-digit', minute: '2-digit' })
  return date.toLocaleDateString('fr-FR', { day: 'numeric', month: 'short' })
}

const formatPrice = (price) => {
  if (!price) return 'N/A'
  return new Intl.NumberFormat('fr-FR', {
    style: 'currency',
    currency: 'XOF',
    minimumFractionDigits: 0
  }).format(price)
}

// Watch pour scroll automatique
watch(() => chatStore.chatMessages.length, async () => {
  await nextTick()
  scrollToBottom()
})

onMounted(async () => {
  const id = initSupplier()
  if (id) {
    await chatStore.fetchSupplierSessions(id)
    chatStore.startPolling(id)
    await nextTick()
    scrollToBottom()
  }
})

onUnmounted(() => {
  chatStore.stopPolling()
})
</script>

<style scoped>
.admin-chat-container {
  display: grid;
  grid-template-columns: 380px 1fr;
  height: calc(100vh - 200px);
  background: #fff;
  border-radius: 16px;
  overflow: hidden;
  box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
}

/* ========== SIDEBAR ========== */
.conversations-sidebar {
  border-right: 1px solid #e5e7eb;
  display: flex;
  flex-direction: column;
  background: #fafafa;
}

.sidebar-header {
  padding: 20px;
  border-bottom: 1px solid #e5e7eb;
  display: flex;
  justify-content: space-between;
  align-items: center;
  background: #fff;
}

.sidebar-header h3 {
  font-size: 18px;
  font-weight: 600;
  color: #111827;
  margin: 0;
}

.unread-badge {
  background: #fe9700;
  color: #fff;
  padding: 4px 10px;
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
  padding: 16px 20px;
  cursor: pointer;
  transition: all 0.2s ease;
  border-bottom: 1px solid #f3f4f6;
  background: #fff;
}

.conversation-item:hover {
  background: #f9fafb;
}

.conversation-item.active {
  background: #fff7ed;
  border-left: 3px solid #fe9700;
}

.conversation-avatar {
  position: relative;
  flex-shrink: 0;
}

.user-icon-wrapper {
  width: 48px;
  height: 48px;
  border-radius: 50%;
  background: linear-gradient(135deg, #fe9700 0%, #fc4618 100%);
  display: flex;
  align-items: center;
  justify-content: center;
}

.user-icon {
  width: 28px;
  height: 28px;
  color: white;
}

.online-indicator {
  position: absolute;
  bottom: 2px;
  right: 2px;
  width: 12px;
  height: 12px;
  background: #10b981;
  border: 2px solid #fff;
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

.conversation-name {
  font-size: 14px;
  font-weight: 600;
  color: #111827;
  margin: 0;
  overflow: hidden;
  text-overflow: ellipsis;
  white-space: nowrap;
}

.conversation-time {
  font-size: 11px;
  color: #9ca3af;
  flex-shrink: 0;
  margin-left: 8px;
}

.conversation-preview {
  display: flex;
  justify-content: space-between;
  align-items: center;
  gap: 8px;
}

.last-message {
  font-size: 13px;
  color: #6b7280;
  margin: 0;
  overflow: hidden;
  text-overflow: ellipsis;
  white-space: nowrap;
  flex: 1;
}

.unread-count {
  background: #fe9700;
  color: #fff;
  padding: 2px 8px;
  border-radius: 10px;
  font-size: 11px;
  font-weight: 600;
  flex-shrink: 0;
}

.product-info {
  display: flex;
  align-items: center;
  gap: 6px;
  margin-top: 6px;
  font-size: 11px;
  color: #9ca3af;
}

.product-info svg {
  flex-shrink: 0;
}

.product-info span {
  overflow: hidden;
  text-overflow: ellipsis;
  white-space: nowrap;
}

.no-conversations {
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  padding: 60px 20px;
  text-align: center;
}

.no-conversations p {
  color: #9ca3af;
  font-size: 14px;
  margin-top: 16px;
}

/* ========== CHAT MAIN ========== */
/* Added min-height: 0 for proper flex overflow */
.chat-main {
  display: flex;
  flex-direction: column;
  background: #fff;
  min-height: 0;
}

.no-chat-selected {
  flex: 1;
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  text-align: center;
  padding: 40px;
}

.no-chat-selected h3 {
  color: #111827;
  font-size: 20px;
  font-weight: 600;
  margin: 20px 0 8px 0;
}

.no-chat-selected p {
  color: #6b7280;
  font-size: 14px;
  margin: 0;
}

/* Added min-height: 0 for proper flex overflow */
.chat-content {
  display: flex;
  flex-direction: column;
  height: 100%;
  min-height: 0;
}

/* Added flex-shrink: 0 to prevent header from shrinking */
.chat-header {
  padding: 20px 24px;
  border-bottom: 1px solid #e5e7eb;
  background: #fafafa;
  flex-shrink: 0;
}

.chat-client-info {
  display: flex;
  align-items: center;
  gap: 12px;
}

.client-avatar {
  position: relative;
}

.user-icon-wrapper-large {
  width: 48px;
  height: 48px;
  border-radius: 50%;
  background: linear-gradient(135deg, #fe9700 0%, #fc4618 100%);
  display: flex;
  align-items: center;
  justify-content: center;
}

.user-icon-large {
  width: 30px;
  height: 30px;
  color: white;
}

.chat-client-info h3 {
  font-size: 16px;
  font-weight: 600;
  color: #111827;
  margin: 0 0 4px 0;
}

.client-status {
  font-size: 13px;
  color: #10b981;
  margin: 0;
}

/* ========== MESSAGES ========== */
/* Removed max-height, added flex: 1 and min-height: 0 for proper scrolling */
.messages-container {
  flex: 1;
  overflow-y: auto;
  overflow-x: hidden;
  padding: 24px;
  background: #f9fafb;
  min-height: 0;
}

.message-wrapper {
  display: flex;
  margin-bottom: 16px;
  width: 100%;
}

/* User messages align LEFT */
.message-wrapper.user {
  justify-content: flex-start;
}

/* Supplier messages align RIGHT */
.message-wrapper.supplier {
  justify-content: flex-end;
}

.message-bubble {
  max-width: 60%;
  padding: 12px 16px;
  border-radius: 12px;
  position: relative;
}

.message-wrapper.user .message-bubble {
  background: #fff;
  border: 1px solid #e5e7eb;
  border-bottom-left-radius: 4px;
}

.message-wrapper.supplier .message-bubble {
  background: #fe9700;
  color: #fff;
  border-bottom-right-radius: 4px;
}

.message-text {
  font-size: 14px;
  line-height: 1.5;
  margin: 0 0 4px 0;
  word-wrap: break-word;
}

.message-timestamp {
  font-size: 11px;
  opacity: 0.7;
}

.message-wrapper.supplier .message-timestamp {
  color: rgba(255, 255, 255, 0.9);
}

.message-wrapper.user .message-timestamp {
  color: #9ca3af;
}

/* Product Message */
.product-message-card {
  max-width: 60%;
  display: flex;
  gap: 12px;
  background: #fff;
  border: 1px solid #e5e7eb;
  border-radius: 12px;
  padding: 12px;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
}

.product-image img {
  width: 80px;
  height: 80px;
  border-radius: 8px;
  object-fit: cover;
}

.product-details {
  flex: 1;
  display: flex;
  flex-direction: column;
  gap: 4px;
}

.product-details h4 {
  font-size: 14px;
  font-weight: 600;
  color: #111827;
  margin: 0;
  line-height: 1.3;
}

.product-price {
  font-size: 15px;
  font-weight: 700;
  color: #fe9700;
  margin: 0;
}

.product-meta {
  display: flex;
  gap: 12px;
  font-size: 12px;
  color: #6b7280;
}

/* Image Message */
.image-wrapper {
  display: flex;
  flex-direction: column;
  gap: 4px;
  max-width: 60%;
}

.chat-image {
  max-width: 300px;
  max-height: 400px;
  width: 100%;
  border-radius: 12px;
  cursor: pointer;
  transition: transform 0.2s;
  display: block;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
}

.chat-image:hover {
  transform: scale(1.02);
}

/* ========== INPUT AREA ========== */
/* Added flex-shrink: 0 to keep input area always visible */
.chat-input-area {
  padding: 20px 24px;
  border-top: 1px solid #e5e7eb;
  background: #fff;
  flex-shrink: 0;
}

.input-container {
  display: flex;
  gap: 12px;
  align-items: flex-end;
  width: 100%;
}

.upload-button {
  width: 40px;
  height: 40px;
  background: #f3f4f6;
  border: 1px solid #e5e7eb;
  border-radius: 10px;
  display: flex;
  align-items: center;
  justify-content: center;
  cursor: pointer;
  transition: all 0.2s;
  flex-shrink: 0;
  color: #6b7280;
}

.upload-button:hover {
  background: #e5e7eb;
  color: #374151;
}

.upload-icon {
  width: 20px;
  height: 20px;
}

.hidden-file-input {
  display: none;
}

.message-input {
  flex: 1;
  padding: 12px 16px;
  border: 2px solid #e5e7eb;
  border-radius: 12px;
  font-size: 14px;
  font-family: inherit;
  resize: none;
  max-height: 120px;
  transition: border-color 0.2s;
}

.message-input:focus {
  outline: none;
  border-color: #fe9700;
}

.send-button {
  width: 48px;
  height: 48px;
  background: linear-gradient(135deg, #fe9700 0%, #ff7a00 100%);
  color: #fff;
  border: none;
  border-radius: 12px;
  cursor: pointer;
  display: flex;
  align-items: center;
  justify-content: center;
  transition: all 0.3s ease;
  flex-shrink: 0;
}

.send-button:hover:not(:disabled) {
  transform: translateY(-2px);
  box-shadow: 0 6px 16px rgba(254, 151, 0, 0.4);
}

.send-button:disabled {
  opacity: 0.5;
  cursor: not-allowed;
}

/* Scrollbar */
.conversations-list::-webkit-scrollbar,
.messages-container::-webkit-scrollbar {
  width: 6px;
}

.conversations-list::-webkit-scrollbar-track,
.messages-container::-webkit-scrollbar-track {
  background: #f3f4f6;
}

.conversations-list::-webkit-scrollbar-thumb,
.messages-container::-webkit-scrollbar-thumb {
  background: #d1d5db;
  border-radius: 3px;
}

.conversations-list::-webkit-scrollbar-thumb:hover,
.messages-container::-webkit-scrollbar-thumb:hover {
  background: #9ca3af;
}

/* Responsive */
@media (max-width: 1024px) {
  .admin-chat-container {
    grid-template-columns: 320px 1fr;
  }
}

@media (max-width: 768px) {
  .admin-chat-container {
    grid-template-columns: 1fr;
    height: calc(100vh - 100px);
  }

  .conversations-sidebar {
    display: none;
  }
}
</style>
