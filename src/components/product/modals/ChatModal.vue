<template>
    <div class="chat-modal-overlay" @click.self="$emit('close')">
      <div class="chat-modal">
        <div class="chat-header">
          <div class="supplier-info">
            <div class="supplier-avatar">
              <img :src="supplier.logo" :alt="supplier.name" />
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
            v-for="message in chatMessages" 
            :key="message.id"
            class="message"
            :class="{ 'user-message': message.sender === 'user', 'bot-message': message.sender === 'bot' }"
          >
            <div class="message-content">
              <div class="message-text">{{ message.message }}</div>
              <div class="message-time">{{ formatTime(message.timestamp) }}</div>
            </div>
          </div>
        </div>
        
        <div class="chat-input">
          <div class="input-container">
            <input 
              type="text" 
              :value="newMessage"
              @input="$emit('updateMessage', $event.target.value)"
              @keypress.enter="handleSendMessage"
              placeholder="Tapez votre message..."
              class="message-input"
            />
            <button 
              class="send-btn" 
              @click="handleSendMessage"
              :disabled="!newMessage.trim()"
            >
              <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
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
          </div>
        </div>
      </div>
    </div>
  </template>
  
  <script setup>
  import { ref, nextTick, defineProps, defineEmits } from 'vue'
  
  const props = defineProps({
    supplier: Object,
    chatMessages: Array,
    newMessage: String
  })
  
  const emit = defineEmits([
    'close',
    'sendMessage',
    'updateMessage'
  ])
  
  const messagesContainer = ref(null)
  
  const handleSendMessage = () => {
    if (props.newMessage.trim()) {
      emit('sendMessage')
      scrollToBottom()
    }
  }
  
  const sendQuickMessage = (message) => {
    emit('updateMessage', message)
    nextTick(() => {
      emit('sendMessage')
      scrollToBottom()
    })
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
  </script>
  
  <style scoped>
  .chat-modal-overlay {
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(0, 0, 0, 0.5);
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
    justify-content: space-between;
    align-items: center;
    padding: 16px 20px;
    border-bottom: 1px solid #e8e8e8;
    background: #fafafa;
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
    color: #333;
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
    width: 32px;
    height: 32px;
    border: none;
    background: none;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    transition: background-color 0.3s ease;
  }
  
  .close-btn:hover {
    background: #f5f5f5;
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
    background: #1890ff;
    color: white;
    border-bottom-right-radius: 6px;
  }
  
  .bot-message .message-text {
    background: #f0f0f0;
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
  
  .send-btn {
    width: 40px;
    height: 40px;
    background: #1890ff;
    border: none;
    border-radius: 50%;
    color: white;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    transition: all 0.3s ease;
  }
  
  .send-btn:hover:not(:disabled) {
    background: #40a9ff;
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
    border-color: #1890ff;
    color: #1890ff;
  }
  
  @media (max-width: 768px) {
    .chat-modal {
      margin: 0;
      border-radius: 12px 12px 0 0;
      position: fixed;
      bottom: 0;
      left: 0;
      right: 0;
      height: 80vh;
      max-height: 80vh;
    }
  }
  </style>
  