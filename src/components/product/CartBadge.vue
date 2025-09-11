<template>
    <div class="cart-badge" @click="$emit('click')">
      <div class="cart-icon">
        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
          <circle cx="9" cy="21" r="1"></circle>
          <circle cx="20" cy="21" r="1"></circle>
          <path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"></path>
        </svg>
        <div class="cart-count">{{ count }}</div>
      </div>
    </div>
  </template>
  
  <script setup>
  import { defineProps, defineEmits } from 'vue'
  
  const props = defineProps({
    count: {
      type: Number,
      default: 0
    }
  })
  
  const emit = defineEmits(['click'])
  </script>
  
  <style scoped>
  .cart-badge {
    position: fixed;
    bottom: 100px;
    right: 20px;
    width: 56px;
    height: 56px;
    background: #ff4d4f;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    box-shadow: 0 4px 20px rgba(255, 77, 79, 0.4);
    z-index: 999;
    transition: all 0.3s ease;
  }
  
  .cart-badge:hover {
    transform: scale(1.1);
    box-shadow: 0 6px 25px rgba(255, 77, 79, 0.5);
  }
  
  .cart-badge:active {
    transform: scale(0.95);
  }
  
  .cart-icon {
    position: relative;
    color: white;
  }
  
  .cart-count {
    position: absolute;
    top: -8px;
    right: -8px;
    background: #fff;
    color: #ff4d4f;
    border-radius: 50%;
    width: 20px;
    height: 20px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 11px;
    font-weight: 700;
    border: 2px solid #ff4d4f;
  }
  
  @media (max-width: 768px) {
    .cart-badge {
      bottom: 120px;
      right: 16px;
      width: 48px;
      height: 48px;
    }
    
    .cart-count {
      width: 18px;
      height: 18px;
      font-size: 10px;
    }
  }
  </style>
  