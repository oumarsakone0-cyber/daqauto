<template>
    <div class="share-modal-overlay" @click.self="$emit('close')">
      <div class="share-modal">
        <div class="modal-header">
          <h3 class="modal-title">Partager ce produit</h3>
          <button class="close-btn" @click="$emit('close')">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <line x1="18" y1="6" x2="6" y2="18"></line>
              <line x1="6" y1="6" x2="18" y2="18"></line>
            </svg>
          </button>
        </div>
        
        <div class="modal-body">
          <div class="product-preview">
            <div class="product-image">
              <img :src="product.primary_image || 'https://via.placeholder.com/80x80'" :alt="product.name" />
            </div>
            <div class="product-info">
              <h4 class="product-name">{{ product.name }}</h4>
              <div class="product-price">{{ formatPrice(product.unit_price) }}</div>
            </div>
          </div>
          
          <div class="share-options">
            <button class="share-option whatsapp" @click="$emit('share', 'whatsapp')">
              <div class="share-icon">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="#25D366">
                  <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/>
                </svg>
              </div>
              <span>WhatsApp</span>
            </button>
            
            <button class="share-option facebook" @click="$emit('share', 'facebook')">
              <div class="share-icon">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="#1877F2">
                  <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>
                </svg>
              </div>
              <span>Facebook</span>
            </button>
            
            <button class="share-option telegram" @click="$emit('share', 'telegram')">
              <div class="share-icon">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="#0088CC">
                  <path d="M11.944 0A12 12 0 0 0 0 12a12 12 0 0 0 12 12 12 12 0 0 0 12-12A12 12 0 0 0 12 0a12 12 0 0 0-.056 0zm4.962 7.224c.1-.002.321.023.465.14a.506.506 0 0 1 .171.325c.016.093.036.306.02.472-.18 1.898-.962 6.502-1.36 8.627-.168.9-.499 1.201-.82 1.23-.696.065-1.225-.46-1.9-.902-1.056-.693-1.653-1.124-2.678-1.8-1.185-.78-.417-1.21.258-1.91.177-.184 3.247-2.977 3.307-3.23.007-.032.014-.15-.056-.212s-.174-.041-.249-.024c-.106.024-1.793 1.14-5.061 3.345-.48.33-.913.49-1.302.48-.428-.008-1.252-.241-1.865-.44-.752-.245-1.349-.374-1.297-.789.027-.216.325-.437.893-.663 3.498-1.524 5.83-2.529 6.998-3.014 3.332-1.386 4.025-1.627 4.476-1.635z"/>
                </svg>
              </div>
              <span>Telegram</span>
            </button>
            
            <button class="share-option email" @click="$emit('share', 'email')">
              <div class="share-icon">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="#EA4335">
                  <path d="M24 5.457v13.909c0 .904-.732 1.636-1.636 1.636h-3.819V11.73L12 16.64l-6.545-4.91v9.273H1.636A1.636 1.636 0 0 1 0 19.366V5.457c0-.904.732-1.636 1.636-1.636h3.819l6.545 4.91 6.545-4.91h3.819A1.636 1.636 0 0 1 24 5.457z"/>
                </svg>
              </div>
              <span>Email</span>
            </button>
          </div>
          
          <div class="link-section">
            <div class="link-input-group">
              <input 
                type="text" 
                :value="shareLink" 
                readonly 
                class="link-input"
                ref="linkInput"
              />
              <button class="copy-btn" @click="$emit('copyLink')">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                  <rect x="9" y="9" width="13" height="13" rx="2" ry="2"></rect>
                  <path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"></path>
                </svg>
                Copier
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>
  </template>
  
  <script setup>
  import { defineProps, defineEmits } from 'vue'
  import { formatPrice } from '../../../services/formatPrice'
  
  const props = defineProps({
    product: Object,
    shareLink: String
  })
  
  const emit = defineEmits([
    'close',
    'share',
    'copyLink'
  ])
  
  </script>
  
  <style scoped>
  .share-modal-overlay {
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
  
  .share-modal {
    background: #fff;
    border-radius: 12px;
    width: 100%;
    max-width: 480px;
    max-height: 90vh;
    overflow: hidden;
  }
  
  .modal-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 20px;
    border-bottom: 1px solid #e8e8e8;
  }
  
  .modal-title {
    font-size: 18px;
    font-weight: 600;
    color: #333;
    margin: 0;
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
  
  .modal-body {
    padding: 20px;
  }
  
  .product-preview {
    display: flex;
    gap: 12px;
    padding: 16px;
    background: #fafafa;
    border-radius: 8px;
    margin-bottom: 24px;
  }
  
  .product-image {
    width: 60px;
    height: 60px;
    border-radius: 8px;
    overflow: hidden;
    flex-shrink: 0;
  }
  
  .product-image img {
    width: 100%;
    height: 100%;
    object-fit: cover;
  }
  
  .product-info {
    flex: 1;
  }
  
  .product-name {
    font-size: 14px;
    font-weight: 500;
    color: #333;
    margin: 0 0 8px 0;
    line-height: 1.3;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
  }
  
  .product-price {
    font-size: 16px;
    font-weight: 700;
    color: #ff4d4f;
  }
  
  .share-options {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 12px;
    margin-bottom: 24px;
  }
  
  .share-option {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 12px 16px;
    border: 1px solid #e8e8e8;
    border-radius: 8px;
    background: #fff;
    cursor: pointer;
    transition: all 0.3s ease;
    font-size: 14px;
    font-weight: 500;
  }
  
  .share-option:hover {
    border-color: #1890ff;
    background: rgba(24, 144, 255, 0.05);
  }
  
  .share-option.whatsapp:hover {
    border-color: #25D366;
    background: rgba(37, 211, 102, 0.05);
  }
  
  .share-option.facebook:hover {
    border-color: #1877F2;
    background: rgba(24, 119, 242, 0.05);
  }
  
  .share-option.telegram:hover {
    border-color: #0088CC;
    background: rgba(0, 136, 204, 0.05);
  }
  
  .share-option.email:hover {
    border-color: #EA4335;
    background: rgba(234, 67, 53, 0.05);
  }
  
  .share-icon {
    display: flex;
    align-items: center;
    justify-content: center;
    width: 32px;
    height: 32px;
    border-radius: 50%;
    background: #f5f5f5;
  }
  
  .link-section {
    border-top: 1px solid #e8e8e8;
    padding-top: 20px;
  }
  
  .link-input-group {
    display: flex;
    gap: 8px;
  }
  
  .link-input {
    flex: 1;
    padding: 10px 12px;
    border: 1px solid #d9d9d9;
    border-radius: 6px;
    font-size: 14px;
    background: #fafafa;
    color: #666;
  }
  
  .copy-btn {
    display: flex;
    align-items: center;
    gap: 6px;
    padding: 10px 16px;
    background: #1890ff;
    color: white;
    border: none;
    border-radius: 6px;
    font-size: 14px;
    font-weight: 500;
    cursor: pointer;
    transition: background-color 0.3s ease;
    white-space: nowrap;
  }
  
  .copy-btn:hover {
    background: #40a9ff;
  }
  
  @media (max-width: 768px) {
    .share-modal {
      margin: 0;
      border-radius: 12px 12px 0 0;
      position: fixed;
      bottom: 0;
      left: 0;
      right: 0;
      max-height: 80vh;
    }
    
    .share-options {
      grid-template-columns: 1fr;
    }
    
    .link-input-group {
      flex-direction: column;
    }
    
    .copy-btn {
      justify-content: center;
    }
  }
  </style>
  