<template>
  <div class="product-gallery">
    <!-- Desktop Gallery -->
    <div class="desktop-gallery desktop-only">
      <div class="main-image-container">
        <div class="image-container">
          <img 
            :src="currentImage" 
            :alt="product.name" 
            class="main-image" 
            @click="$emit('openImageModal', currentImageIndex)"
          >
          <!-- Made navigation buttons always visible and improved styling -->
          <div class="image-nav-arrows" v-if="productImages.length > 1">
            <button class="image-nav-btn prev" @click="prevImage" type="button">
              <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <polyline points="15,18 9,12 15,6"></polyline>
              </svg>
            </button>
            <button class="image-nav-btn next" @click="nextImage" type="button">
              <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <polyline points="9,18 15,12 9,6"></polyline>
              </svg>
            </button>
          </div>
          <div class="zoom-hint">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <circle cx="11" cy="11" r="8"></circle>
              <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
              <line x1="11" y1="8" x2="11" y2="14"></line>
              <line x1="8" y1="11" x2="14" y2="11"></line>
            </svg>
            <span>Click to enlarge</span>
          </div>
        </div>
      </div>
      
      <div class="thumbnail-container" v-if="productImages.length > 1">
  <button 
    class="thumbnail-nav prev" 
    @click="prevThumbnailSet" 
    :disabled="thumbnailStartIndex === 0" 
    type="button"
    :style="{ opacity: thumbnailStartIndex === 0 ? 0.3 : 0.8 }"
  >
    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
      <polyline points="15,18 9,12 15,6"></polyline>
    </svg>
  </button>
  <div class="thumbnails">
    <div 
      v-for="(img, index) in visibleThumbnails" 
      :key="index + thumbnailStartIndex"
      class="thumbnail" 
      :class="{ active: index + thumbnailStartIndex === currentImageIndex }"
      @click="setCurrentImage(index + thumbnailStartIndex)"
    >
      <img :src="img" :alt="`Thumbnail ${index + thumbnailStartIndex + 1}`">
    </div>
  </div>
  <button 
    class="thumbnail-nav next" 
    @click="nextThumbnailSet" 
    :disabled="thumbnailStartIndex + thumbnailsPerView >= productImages.length" 
    type="button"
    :style="{ opacity: thumbnailStartIndex + thumbnailsPerView >= productImages.length ? 0.3 : 0.8 }"
  >
    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
      <polyline points="9,18 15,12 9,6"></polyline>
    </svg>
  </button>
</div>
      
      <div class="gallery-actions">
        <button class="gallery-action-btn" @click="$emit('toggleFavorite')" :class="{ 'is-favorite': isFavorite }">
          <svg width="16" height="16" viewBox="0 0 24 24" :fill="isFavorite ? '#ff4d4f' : 'none'" stroke="currentColor" stroke-width="2">
            <path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"></path>
          </svg>
          <span>{{ isFavorite ? 'Favorited' : 'Favorite' }}</span>
        </button>
        <button class="gallery-action-btn" @click="$emit('openShareModal')">
          <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <path d="M4 12v8a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2v-8"></path>
            <polyline points="16 6 12 2 8 6"></polyline>
            <line x1="12" y1="2" x2="12" y2="15"></line>
          </svg>
          <span>Share</span>
        </button>
        <button class="gallery-action-btn" @click="$emit('addToCompare')">
          <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <polyline points="16 3 21 3 21 8"></polyline>
            <polyline points="8 21 3 21 3 16"></polyline>
            <line x1="21" y1="3" x2="14" y2="10"></line>
            <line x1="3" y1="21" x2="10" y2="14"></line>
          </svg>
          <span>Benchmark</span>
        </button>
      </div>
    </div>

    <!-- Mobile Gallery -->
    <div class="mobile-gallery mobile-only">
      <div class="mobile-main-image" @click="$emit('openImageModal', currentImageIndex)">
        <img :src="currentImage" :alt="product.name" class="mobile-image">
        <div class="image-indicator">{{ currentImageIndex + 1 }} / {{ productImages.length }}</div>
        <div class="zoom-icon">
          <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2">
            <circle cx="11" cy="11" r="8"></circle>
            <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
            <line x1="11" y1="8" x2="11" y2="14"></line>
            <line x1="8" y1="11" x2="14" y2="11"></line>
          </svg>
        </div>
      </div>
      
      <div class="mobile-thumbnails" v-if="productImages.length > 1">
        <div class="thumbnails-scroll">
          <div 
            v-for="(img, index) in productImages" 
            :key="index"
            class="mobile-thumbnail" 
            :class="{ active: index === currentImageIndex }"
            @click="setCurrentImage(index)"
          >
            <img :src="img" :alt="`Image ${index + 1}`">
          </div>
        </div>
      </div>
    </div>
    
    <div class="trade-assurance">
      <div class="trade-assurance-icon">
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#fe9700" stroke-width="2">
          <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path>
        </svg>
      </div>
      <div class="trade-assurance-text">
        <div class="trade-assurance-title">Approved Product</div>
        <div class="trade-assurance-desc">Protecting your orders</div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, defineProps, defineEmits } from 'vue'
  
const props = defineProps({
  product: Object,
  productImages: Array,
  isFavorite: Boolean
})
  
const emit = defineEmits([
  'openImageModal',
  'toggleFavorite', 
  'openShareModal',
  'addToCompare'
])
  
const currentImageIndex = ref(0)
const thumbnailStartIndex = ref(0)
const thumbnailsPerView = ref(6)
  
const currentImage = computed(() => {
  const images = props.productImages
  if (!images || images.length === 0) {
    return 'https://via.placeholder.com/400x400?text=Produit'
  }
    
  const image = images[currentImageIndex.value]
    
  if (typeof image === 'object' && image.image_url) {
    return image.image_url
  }
    
  if (typeof image === 'string') {
    return image
  }
    
  return 'https://via.placeholder.com/400x400?text=Produit'
})
  
const visibleThumbnails = computed(() => {
  return props.productImages.slice(thumbnailStartIndex.value, thumbnailStartIndex.value + thumbnailsPerView.value)
})
  
const setCurrentImage = (index) => {
  currentImageIndex.value = index
}
  
const nextImage = () => {
  if (currentImageIndex.value < props.productImages.length - 1) {
    currentImageIndex.value++
  } else {
    currentImageIndex.value = 0
  }
    
  if (currentImageIndex.value >= thumbnailStartIndex.value + thumbnailsPerView.value) {
    thumbnailStartIndex.value = currentImageIndex.value - thumbnailsPerView.value + 1
  }
}
  
const prevImage = () => {
  if (currentImageIndex.value > 0) {
    currentImageIndex.value--
  } else {
    currentImageIndex.value = props.productImages.length - 1
  }
    
  if (currentImageIndex.value < thumbnailStartIndex.value) {
    thumbnailStartIndex.value = currentImageIndex.value
  }
}
  
const nextThumbnailSet = () => {
  if (thumbnailStartIndex.value + thumbnailsPerView.value < props.productImages.length) {
    thumbnailStartIndex.value++
  }
}
  
const prevThumbnailSet = () => {
  if (thumbnailStartIndex.value > 0) {
    thumbnailStartIndex.value--
  }
}
</script>

<style scoped>
/* Styles pour la galerie d'images */
.product-gallery {
  position: relative;
}

.desktop-only {
  display: block;
}

.mobile-only {
  display: none;
}

@media (max-width: 768px) {
  .desktop-only {
    display: none !important;
  }
    
  .mobile-only {
    display: block;
  }
}

.main-image-container {
  position: relative;
  width: 100%;
  height: 400px;
  margin-bottom: 16px;
  border: 1px solid #e8e8e8;
  border-radius: 8px;
  overflow: hidden;
  transition: transform 0.3s ease;
}

.main-image-container:hover {
  border-color: #fe9700;
}

.image-container {
  width: 100%;
  height: 100%;
  position: relative;
}

.main-image {
  width: 100%;
  height: 100%;
  object-fit: contain;
  cursor: pointer;
  transition: transform 0.3s ease;
}

.image-container:hover .main-image {
  transform: scale(1.05);
}

.zoom-hint {
  position: absolute;
  bottom: 10px;
  left: 10px;
  display: flex;
  align-items: center;
  gap: 6px;
  padding: 6px 12px;
  background: rgba(0, 0, 0, 0.6);
  color: white;
  border-radius: 20px;
  font-size: 12px;
  opacity: 0;
  transition: opacity 0.3s ease;
}

.image-container:hover .zoom-hint {
  opacity: 1;
}

/* Made navigation buttons always visible and improved styling */
.image-nav-arrows {
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  display: flex;
  justify-content: space-between;
  align-items: center;
  pointer-events: none;
}

.image-nav-btn {
  width: 40px;
  height: 40px;
  background: rgba(0, 0, 0, 0.6);
  border: none;
  border-radius: 50%;
  color: white;
  cursor: pointer;
  display: flex;
  align-items: center;
  justify-content: center;
  opacity: 0.7; /* Made buttons semi-visible by default instead of hidden */
  transition: all 0.3s ease;
  pointer-events: auto;
  z-index: 3;
}

.image-nav-btn.prev {
  margin-left: 10px;
}

.image-nav-btn.next {
  margin-right: 10px;
}

/* Enhanced hover effects for better visibility */
.main-image-container:hover .image-nav-btn,
.image-nav-btn:hover {
  opacity: 1;
  background: rgba(0, 0, 0, 0.8);
}

.image-nav-btn:hover {
  transform: scale(1.1);
}

/* Ensure SVG icons are properly styled */
.image-nav-btn svg,
.thumbnail-nav svg {
  pointer-events: none;
  stroke: currentColor;
}

.thumbnail-container {
  display: flex;
  align-items: center;
  gap: 8px;
}

.thumbnails {
  display: flex;
  gap: 8px;
  flex: 1;
  overflow: hidden;
}

.thumbnail {
  width: 60px;
  height: 60px;
  border: 2px solid transparent;
  border-radius: 8px;
  overflow: hidden;
  cursor: pointer;
  transition: all 0.3s ease;
  position: relative;
}

.thumbnail:hover {
  border-color: #fe9700;
  transform: translateY(-2px);
  box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
}

.thumbnail.active {
  border-color: #fe9700;
  box-shadow: 0 0 0 2px rgba(24, 144, 255, 0.2);
}

.thumbnail img {
  width: 100%;
  height: 100%;
  object-fit: cover;
}

.thumbnail-nav {
  width: 28px;
  height: 28px;
  border: 1px solid #d9d9d9;
  border-radius: 50%;
  background: #fff;
  display: flex;
  align-items: center;
  justify-content: center;
  cursor: pointer;
  transition: all 0.3s ease;
  box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
}

.thumbnail-nav:hover:not(:disabled) {
  border-color: #fe9700;
  color: #fe9700;
  transform: scale(1.1);
}

.thumbnail-nav:disabled {
  opacity: 0.5;
  cursor: not-allowed;
}

.gallery-actions {
  display: flex;
  justify-content: center;
  gap: 16px;
  margin-top: 16px;
}

.gallery-action-btn {
  display: flex;
  align-items: center;
  gap: 6px;
  padding: 8px 16px;
  border: 1px solid #d9d9d9;
  border-radius: 20px;
  background: #fff;
  color: #666;
  font-size: 13px;
  cursor: pointer;
  transition: all 0.3s ease;
}

.gallery-action-btn:hover {
  border-color: #fe9700;
  color: #fe9700;
}

.gallery-action-btn.is-favorite {
  color: #ff4d4f;
  border-color: #ff4d4f;
}

/* Mobile Gallery */
.mobile-gallery {
  background: #fff;
}

.mobile-main-image {
  position: relative;
  width: 100%;
  height: 400px;
  overflow: hidden;
}

.mobile-image {
  width: 100%;
  height: 100%;
  object-fit: contain;
  background: #f8f8f8;
}

.image-indicator {
  position: absolute;
  top: 16px;
  right: 16px;
  background: rgba(0, 0, 0, 0.6);
  color: white;
  padding: 4px 12px;
  border-radius: 12px;
  font-size: 12px;
  font-weight: 500;
}

.zoom-icon {
  position: absolute;
  bottom: 16px;
  right: 16px;
  width: 40px;
  height: 40px;
  background: rgba(0, 0, 0, 0.6);
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
}

.mobile-thumbnails {
  padding: 12px 16px;
  background: #fff;
}

.thumbnails-scroll {
  display: flex;
  gap: 8px;
  overflow-x: auto;
  padding-bottom: 4px;
  -webkit-overflow-scrolling: touch;
}

.mobile-thumbnail {
  flex-shrink: 0;
  width: 60px;
  height: 60px;
  border: 2px solid transparent;
  border-radius: 8px;
  overflow: hidden;
  transition: all 0.3s ease;
}

.mobile-thumbnail.active {
  border-color: #fe9700;
}

.mobile-thumbnail img {
  width: 100%;
  height: 100%;
  object-fit: cover;
}

.trade-assurance {
  display: flex;
  align-items: center;
  gap: 12px;
  margin-top: 24px;
  padding: 16px;
  background: #f9f9f9;
  border-radius: 8px;
}

.trade-assurance-icon {
  display: flex;
  align-items: center;
  justify-content: center;
  width: 40px;
  height: 40px;
  background: rgba(24, 144, 255, 0.1);
  border-radius: 50%;
  color: #fe9700;
}

.trade-assurance-title {
  font-size: 15px;
  font-weight: 600;
  color: #333;
}

.trade-assurance-desc {
  font-size: 13px;
  color: #666;
}
</style>
