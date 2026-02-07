<template>
    <div class="notification-toast" :class="[type, { 'show': true }]">
      <div class="toast-icon">
        <svg v-if="type === 'success'" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
          <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path>
          <polyline points="22,4 12,14.01 9,11.01"></polyline>
        </svg>
        <svg v-else-if="type === 'error'" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
          <circle cx="12" cy="12" r="10"></circle>
          <line x1="15" y1="9" x2="9" y2="15"></line>
          <line x1="9" y1="9" x2="15" y2="15"></line>
        </svg>
        <svg v-else-if="type === 'warning'" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
          <path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"></path>
          <line x1="12" y1="9" x2="12" y2="13"></line>
          <line x1="12" y1="17" x2="12.01" y2="17"></line>
        </svg>
        <svg v-else width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
          <circle cx="12" cy="12" r="10"></circle>
          <path d="M12 16v-4"></path>
          <path d="M12 8h.01"></path>
        </svg>
      </div>
      
      <div class="toast-content">
        <div class="toast-title">{{ title }}</div>
        <div class="toast-message">{{ message }}</div>
      </div>
      
      <button class="toast-close" @click="$emit('close')">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
          <line x1="18" y1="6" x2="6" y2="18"></line>
          <line x1="6" y1="6" x2="18" y2="18"></line>
        </svg>
      </button>
    </div>
  </template>
  
  <script setup>
  import { defineProps, defineEmits } from 'vue'
  
  const props = defineProps({
    type: {
      type: String,
      default: 'info',
      validator: (value) => ['success', 'error', 'warning', 'info'].includes(value)
    },
    title: String,
    message: String
  })
  
  const emit = defineEmits(['close'])
  </script>
  
  <style scoped>
  .notification-toast {
    position: fixed;
    top: 20px;
    right: 20px;
    display: flex;
    align-items: flex-start;
    gap: 12px;
    padding: 16px;
    background: #fff;
    border-radius: 8px;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.15);
    border-left: 4px solid;
    max-width: 400px;
    z-index: 1001;
    transform: translateX(100%);
    transition: transform 0.3s ease;
  }
  
  .notification-toast.show {
    transform: translateX(0);
  }
  
  .notification-toast.success {
    border-left-color: #52c41a;
  }
  
  .notification-toast.error {
    border-left-color: #ff4d4f;
  }
  
  .notification-toast.warning {
    border-left-color: #faad14;
  }
  
  .notification-toast.info {
    border-left-color: #1890ff;
  }
  
  .toast-icon {
    display: flex;
    align-items: center;
    justify-content: center;
    width: 32px;
    height: 32px;
    border-radius: 50%;
    flex-shrink: 0;
  }
  
  .success .toast-icon {
    background: rgba(82, 196, 26, 0.1);
    color: #52c41a;
  }
  
  .error .toast-icon {
    background: rgba(255, 77, 79, 0.1);
    color: #ff4d4f;
  }
  
  .warning .toast-icon {
    background: rgba(250, 173, 20, 0.1);
    color: #faad14;
  }
  
  .info .toast-icon {
    background: rgba(24, 144, 255, 0.1);
    color: #1890ff;
  }
  
  .toast-content {
    flex: 1;
  }
  
  .toast-title {
    font-size: 14px;
    font-weight: 600;
    color: #333;
    margin-bottom: 4px;
  }
  
  .toast-message {
    font-size: 13px;
    color: #666;
    line-height: 1.4;
  }
  
  .toast-close {
    width: 24px;
    height: 24px;
    border: none;
    background: none;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    color: #999;
    transition: all 0.3s ease;
    flex-shrink: 0;
  }
  
  .toast-close:hover {
    background: #f5f5f5;
    color: #666;
  }
  
  @media (max-width: 768px) {
    .notification-toast {
      top: 10px;
      right: 10px;
      left: 10px;
      max-width: none;
    }
  }
  </style>
  