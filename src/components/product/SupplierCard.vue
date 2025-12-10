<template>
    <div class="supplier-card">
      <div class="supplier-header">
        <div class="supplier-logo">
          <img :src="supplier.logo" :alt="supplier.name" />
        </div>
        <div class="supplier-info">
          <h3 class="supplier-name">{{ supplier.name }}</h3>
          <div class="supplier-badges">
            
            <!-- <div v-if="supplier.verify"> -->
              <BadgeCheckIcon  class="h-4 w-4 blue-color inline-block " />
              <span  class="badge blue-color">Verified</span>
            <!-- </div> -->
            <!-- <div v-if="supplier.premium"> -->
              <img src="../../assets/premium.png" alt="Premium" class="inline-block h-4 w-4" />
              <span  class="badge primary-color">Premium</span>
            <!-- </div> -->
            
          </div>
          <div class="supplier-location">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/>
              <circle cx="12" cy="10" r="3"/>
            </svg>
            <span>{{ supplier.market }}</span>
          </div>
        </div>
      </div>
  
      <div class="supplier-stats">
        <div class="stat-item">
          <div class="stat-value">{{ supplier.experience }}+</div>
          <div class="stat-label">Year</div>
        </div>
        <div class="stat-item">
          <div class="stat-value">{{ supplier.business_type }}</div>
          <div class="stat-label">Type</div>
        </div>
      </div>
  
      <div class="supplier-description" v-if="supplier.description">
        <p>{{ supplier.description }}</p>
      </div>
  

  
      <div class="supplier-actions">
        <button class="btn-outline text-[14px] sm:text-base" @click="openChat">
          <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"></path>
            </svg>
          Chat now
        </button>
        <button class="btn-outline-with-background text-[14px] sm:text-base" @click="$emit('visitStore')">
          <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/>
            <polyline points="9,22 9,12 15,12 15,22"/>
          </svg>
          Visit Shop
        </button>
      </div>
  
      <div class="supplier-guarantee">
        <div class="guarantee-icon">
          <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#52c41a" stroke-width="2">
            <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/>
          </svg>
        </div>
        <div class="guarantee-text">
          <div class="guarantee-title">Trusted Provider</div>
          <div class="guarantee-desc">Buyer protection guaranteed</div>
        </div>
      </div>
    </div>
  </template>
  
  <script setup>
  import { BadgeCheckIcon } from 'lucide-vue-next';
import { defineProps, defineEmits } from 'vue'
import { useChatStore } from '../../stores/chat'
  
const chat = useChatStore()
  const props = defineProps({
    supplier: Object
  })
  
  const emit = defineEmits([
    'visitStore',
  ])


const openChat = ()=>{
  chat.isDesktopChatOpen = true
  chat.chatMessages.value.push("salut")
}

  </script>
  
  <style scoped>
  .supplier-card {
    background: #fff;
    border-radius: 8px;
    padding: 20px;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
    border: 1px solid #e8e8e8;
  }
  
  .supplier-header {
    display: flex;
    gap: 12px;
    margin-bottom: 16px;
  }
  
  .supplier-logo {
    width: 60px;
    height: 60px;
    border-radius: 8px;
    overflow: hidden;
    flex-shrink: 0;
  }
  
  .supplier-logo img {
    width: 100%;
    height: 100%;
    object-fit: cover;
  }
  
  .supplier-info {
    flex: 1;
  }
  
  .supplier-name {
    font-size: 16px;
    font-weight: 600;
    color: #333;
    margin: 0 0 8px 0;
  }
  
  .supplier-badges {
    display: flex;
    gap: 6px;
    margin-bottom: 8px;
  }
  
  .badge {
    padding-right: 10px;
    border-radius: 12px;
    font-size: 11px;
    font-weight: 600;
  }
  
  .supplier-location {
    display: flex;
    align-items: center;
    gap: 4px;
    font-size: 13px;
    color: #666;
  }
  
  .supplier-stats {
    display: flex;
    gap: 16px;
    margin-bottom: 16px;
    padding: 12px;
    background: #fafafa;
    border-radius: 6px;
  }
  
  .stat-item {
    text-align: center;
  }
  
  .stat-value {
    font-size: 14px;
    font-weight: 600;
    color: #333;
  }
  
  .stat-label {
    font-size: 12px;
    color: #666;
    margin-top: 2px;
  }
  
  .supplier-description {
    margin-bottom: 16px;
    font-size: 14px;
    color: #666;
    line-height: 1.5;
  }
  
  .supplier-contact {
    margin-bottom: 16px;
  }
  
  .contact-info {
    display: flex;
    align-items: center;
    gap: 8px;
    font-size: 14px;
    color: #666;
  }
  
  .supplier-actions {
    display: flex;
    gap: 8px;
    justify-content: space-evenly;
    margin-bottom: 16px;
  }
  
  .supplier-guarantee {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 12px;
    background: #f6ffed;
    border-radius: 8px;
    border: 1px solid #b7eb8f;
  }
  
  .guarantee-icon {
    display: flex;
    align-items: center;
    justify-content: center;
    width: 32px;
    height: 32px;
    background: rgba(82, 196, 26, 0.1);
    border-radius: 50%;
  }
  
  .guarantee-title {
    font-size: 13px;
    font-weight: 600;
    color: #52c41a;
  }
  
  .guarantee-desc {
    font-size: 12px;
    color: #73d13d;
  }
  </style>
  