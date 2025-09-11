<template>
    <div class="image-modal-overlay" @click.self="$emit('close')">
      <div class="image-modal">
        <div class="modal-header">
          <h3 class="modal-title">{{ productName }}</h3>
          <button class="close-btn" @click="$emit('close')">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <line x1="18" y1="6" x2="6" y2="18"></line>
              <line x1="6" y1="6" x2="18" y2="18"></line>
            </svg>
          </button>
        </div>
        
        <div class="modal-body">
          <div class="image-container">
            <img :src="currentImage" :alt="`Image ${currentIndex + 1}`" class="modal-image" />
            
            <button 
              v-if="productImages.length > 1"
              class="nav-btn prev-btn" 
              @click="$emit('prev')"
            >
              <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <polyline points="15,18 9,12 15,6"></polyline>
              </svg>
            </button>
            
            <button 
              v-if="productImages.length > 1"
              class="nav-btn next-btn" 
              @click="$emit('next')"
            >
              <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <polyline points="9,18 15,12 9,6"></polyline>
              </svg>
            </button>
            
            <div class="image-counter" v-if="productImages.length > 1">
              {{ currentIndex + 1 }} / {{ productImages.length }}
            </div>
          </div>
          
          <div class="thumbnails" v-if="productImages.length > 1">
            <div 
              v-for="(img, index) in productImages" 
              :key="index"
              class="thumbnail" 
              :class="{ active: index === currentIndex }"
              @click="$emit('setImage', index)"
            >
              <img :src="img" :alt="`Thumbnail ${index + 1}`">
            </div>
          </div>
        </div>
      </div>
    </div>
  </template>
  
  <script setup>
  import { computed, defineProps, defineEmits } from 'vue'
  
  const props = defineProps({
    productImages: Array,
    currentIndex: Number,
    productName: String
  })
  
  const emit = defineEmits([
    'close',
    'prev',
    'next',
    'setImage'
  ])
  
  const currentImage = computed(() => {
    return props.productImages[props.currentIndex] || 'https://via.placeholder.com/600x600?text=Image'
  })
  </script>
  
  <style scoped>
  .image-modal-overlay {
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(0, 0, 0, 0.9);
    z-index: 1000;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 20px;
  }
  
  .image-modal {
    background: #fff;
    border-radius: 12px;
    max-width: 90vw;
    max-height: 90vh;
    overflow: hidden;
    display: flex;
    flex-direction: column;
  }
  
  .modal-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 16px 20px;
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
    flex: 1;
    display: flex;
    flex-direction: column;
    overflow: hidden;
  }
  
  .image-container {
    position: relative;
    flex: 1;
    display: flex;
    align-items: center;
    justify-content: center;
    background: #f8f8f8;
    min-height: 400px;
  }
  
  .modal-image {
    max-width: 100%;
    max-height: 100%;
    object-fit: contain;
  }
  
  .nav-btn {
    position: absolute;
    top: 50%;
    transform: translateY(-50%);
    width: 48px;
    height: 48px;
    background: rgba(0, 0, 0, 0.6);
    border: none;
    border-radius: 50%;
    color: white;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: all 0.3s ease;
  }
  
  .nav-btn:hover {
    background: rgba(0, 0, 0, 0.8);
    transform: translateY(-50%) scale(1.1);
  }
  
  .prev-btn {
    left: 20px;
  }
  
  .next-btn {
    right: 20px;
  }
  
  .image-counter {
    position: absolute;
    bottom: 20px;
    left: 50%;
    transform: translateX(-50%);
    background: rgba(0, 0, 0, 0.6);
    color: white;
    padding: 6px 12px;
    border-radius: 16px;
    font-size: 14px;
    font-weight: 500;
  }
  
  .thumbnails {
    display: flex;
    gap: 8px;
    padding: 16px 20px;
    overflow-x: auto;
    background: #fafafa;
  }
  
  .thumbnail {
    flex-shrink: 0;
    width: 60px;
    height: 60px;
    border: 2px solid transparent;
    border-radius: 8px;
    overflow: hidden;
    cursor: pointer;
    transition: all 0.3s ease;
  }
  
  .thumbnail:hover {
    border-color: #1890ff;
  }
  
  .thumbnail.active {
    border-color: #1890ff;
    box-shadow: 0 0 0 2px rgba(24, 144, 255, 0.2);
  }
  
  .thumbnail img {
    width: 100%;
    height: 100%;
    object-fit: cover;
  }
  
  @media (max-width: 768px) {
    .image-modal {
      max-width: 100vw;
      max-height: 100vh;
      border-radius: 0;
    }
    
    .nav-btn {
      width: 40px;
      height: 40px;
    }
    
    .prev-btn {
      left: 10px;
    }
    
    .next-btn {
      right: 10px;
    }
    
    .thumbnails {
      padding: 12px 16px;
    }
    
    .thumbnail {
      width: 50px;
      height: 50px;
    }
  }
  </style>
  