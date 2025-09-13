<template>
  <!-- Chat Window Desktop (petite fenêtre comme Facebook) -->
  <div class="chat-window-desktop" v-if="isOpen">
    <div class="chat-header">
      <div class="supplier-info">
        <img src="https://cdn-icons-png.flaticon.com/512/4526/4526832.png" alt="supplier" class="supplier-logo" />
        <div class="supplier-details">
          <h3>{{ supplier.name }}</h3>
          <span class="status-indicator">En ligne</span>
        </div>
      </div>
      <div class="chat-actions">
        <button @click="toggleMinimize" class="minimize-btn">
          <svg width="16" height="16" viewBox="0 0 24 24"  stroke="currentColor" stroke-width="4" fill="none">
            <line x1="5" y1="12" x2="19" y2="12"/>
          </svg>
        </button>
        <button @click="$emit('close')" class="close-btn">
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
          v-for="message in chatMessages" 
          :key="message.id"
          :class="['message', message.sender === 'user' ? 'user-message' : 'bot-message']"
        >
          <div class="message-content">
            <p>{{ message.message }}</p>
            <span class="message-time">
              {{ formatTime(message.timestamp) }}
            </span>
          </div>
        </div>
      </div>

      <div class="quick-actions">
        <button 
          v-for="action in quickActions" 
          :key="action.id"
          @click="sendQuickAction(action)"
          class="quick-action-btn"
        >
          {{ action.text }}
        </button>
      </div>

      <div class="message-input-container">
        <div class="input-wrapper">
          <input
            v-model="newMessage"
            @keypress.enter="sendMessage"
            type="text"
            placeholder="Tapez votre message..."
            class="message-input"
          />
          <button @click="sendMessage" class="send-btn" :disabled="!newMessage.trim()">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <line x1="22" y1="2" x2="11" y2="13"/>
              <polygon points="22,2 15,22 11,13 2,9 22,2"/>
            </svg>
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, nextTick, watch } from 'vue'

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

const quickActions = ref([
  { id: 1, text: '👋 Salut!' },
  { id: 2, text: '💰 Prix?' },
  { id: 3, text: '📦 Livraison?' },
  { id: 4, text: '❓ Question' }
])

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
  
  emit('send-message', message)
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

watch(() => props.chatMessages, scrollToBottom, { deep: true })
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
  background: linear-gradient(160deg,#6878e2, #70499e);
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
  background: rgba(255, 255, 255, 0.2);
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
  background: rgba(255, 255, 255, 0.3);
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
  background: linear-gradient(160deg, #6878e2, #70499e);
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

.message-input {
  flex: 1;
  padding: 12px 16px;
  border: 1px solid #e0e0e0;
  border-radius: 25px;
  font-size: 14px;
  outline: none;
  color: #333;
  transition: border-color 0.2s;
}

.message-input:focus {
  border: 1px solid #333
}

.send-btn {
  background: linear-gradient(135deg, #6878e2, #70499e);
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
</style>
