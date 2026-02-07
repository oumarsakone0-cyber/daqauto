<template>
  <div class="alibaba-product-card">
    <div @click="handleProductClick" class="alibaba-image-area" :style="{ height: cardImageHeight }">
      <div class="alibaba-slider">
        <div class="alibaba-slider-wrapper">
          <div class="alibaba-slider-link">
            <img 
              :src="currentImage" 
              :alt="product.name" 
              class="alibaba-slider-img" 
              loading="lazy"
            >
          </div>
          
          <div class="alibaba-arrow-left primary-color" @click.stop="previousImage">
            <svg width="10" height="17" viewBox="0 0 10 17" fill="none">
              <path d="M9 1L1 8.5L9 16" stroke="#fff" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
          </div>
          <div class="alibaba-arrow-right primary-color" @click.stop="nextImage">
            <svg width="10" height="17" viewBox="0 0 10 17" fill="none">
              <path d="M1 1L9 8.5L1 16" stroke="#fff" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
          </div>
          
          <div v-if="productImages.length > 1" class="alibaba-slider-indicators">
            <div 
              v-for="(img, imgIndex) in productImages" 
              :key="imgIndex"
              class="alibaba-indicator" 
              :class="{ 'active': imgIndex === currentImageIndex }"
              @click.stop="setImage(imgIndex)"
            ></div>
          </div>
        </div>
      </div>
      
      <!-- Updated favorite button to show liked state based on likedByUser -->
      <div 
        class="alibaba-favorite-btn bg-orange" 
        :class="{ 'is-liked': isFavorite }"
        @click.stop="toggleFavorite"
      >
        <svg width="16" height="16" viewBox="0 0 24 24" :fill="isFavorite ? 'currentColor' : 'none'" stroke="currentColor" stroke-width="2">
          <path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"></path>
        </svg>
      </div>
      
      <div v-if="product.discount" class="discount-badge">-{{ product.discount }}%</div>
      
      <div class="vehicle-type-badge" :class="isTrailer ? 'trailer-badge' : 'truck-badge'">
        <svg v-if="!isTrailer" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
          <rect x="1" y="3" width="15" height="13"></rect>
          <polygon points="16 8 20 8 23 11 23 16 16 16 16 8"></polygon>
          <circle cx="5.5" cy="18.5" r="2.5"></circle>
          <circle cx="18.5" cy="18.5" r="2.5"></circle>
        </svg>
        <svg v-else width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
          <rect x="1" y="7" width="18" height="10" rx="1"></rect>
          <circle cx="6" cy="20" r="2"></circle>
          <circle cx="14" cy="20" r="2"></circle>
          <path d="M19 7V4a1 1 0 0 0-1-1H6a1 1 0 0 0-1 1v3"></path>
        </svg>
        <span>{{ isTrailer ? 'Trailer' : 'Truck' }}</span>
      </div>
    </div>

    <div class="alibaba-card-content" :style="{ height: cardContentHeight }">
      <div class="product-info-section">
        <h2 class="alibaba-title">{{ product.name }}</h2>

        <div class="product-pricing">
          <div class="price-row">
            <span class="current-price primary-color">{{ formatPrice(product.unit_price || product.unitPrice) }}</span>
            <span v-if="product.originalPrice" class="original-price">{{ formatPrice(product.originalPrice) }}</span>
          </div>
        </div>

        <div v-if="product.freeShipping" class="shipping-badge">
          <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <rect x="1" y="3" width="15" height="13"></rect>
            <polygon points="16 8 20 8 23 11 23 16 16 16 16 8"></polygon>
            <circle cx="5.5" cy="18.5" r="2.5"></circle>
            <circle cx="18.5" cy="18.5" r="2.5"></circle>
          </svg>
          <span>Free delivery</span>
        </div>

        <div class="vehicle-specs-container">
          <div class="vehicle-spec-row">
            <div class="spec-icon-wrapper">
              <svg class="spec-icon" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M9 11l3 3L22 4"></path>
                <path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"></path>
              </svg>
            </div>
            <div class="spec-content">
              <span class="spec-label">Condition</span>
              <span class="spec-value">{{ product.vehicle_condition || product.trailer_condition || 'New' }}</span>
            </div>
          </div>

          <div v-if="isTrailer" class="vehicle-spec-row">
            <div class="spec-icon-wrapper">
              <svg class="spec-icon" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M21 16V8a2 2 0 0 0-2-2H5a2 2 0 0 0-2 2v8a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2z"></path>
                <polyline points="12 2 12 6"></polyline>
                <polyline points="12 18 12 22"></polyline>
              </svg>
            </div>
            <div class="spec-content">
              <span class="spec-label">Max Payload</span>
              <span class="spec-value">{{ product.trailer_max_payload || product.max_payload || 'N/A' }} Tons</span>
            </div>
          </div>

          <template v-else>
            <div class="vehicle-spec-row">
              <div class="spec-icon-wrapper">
                <svg class="spec-icon" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                  <circle cx="12" cy="12" r="10"></circle>
                  <polyline points="12 6 12 12 16 14"></polyline>
                </svg>
              </div>
              <div class="spec-content">
                <span class="spec-label">Mileage</span>
                <span class="spec-value">{{ product.vehicle_mileage || '0' }} Km</span>
              </div>
            </div>

            <div class="vehicle-spec-row">
              <div class="spec-icon-wrapper">
                <svg class="spec-icon" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                  <path d="M12 2v4m0 12v4M4.93 4.93l2.83 2.83m8.48 8.48l2.83 2.83M2 12h4m12 0h4M4.93 19.07l2.83-2.83m8.48-8.48l2.83-2.83"></path>
                </svg>
              </div>
              <div class="spec-content">
                <span class="spec-label">Fuel Type</span>
                <span class="spec-value">{{ product.fuel_type || 'Diesel' }}</span>
              </div>
            </div>
          </template>
        </div>
      </div>

      <div class="alibaba-action-buttons">
        <button class="btn-view" @click.stop="handleProductClick">
          <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
            <circle cx="12" cy="12" r="3"></circle>
          </svg>
          View
        </button>
        <button class="btn-chat" @click.stop="handleChatClick">
          <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"></path>
          </svg>
          <!-- Texte différent selon mobile/desktop -->
          <span v-if="isMobile" class="btn-text-mobile">Chat</span>
          <span v-else class="btn-text-desktop">Chat Now</span>
        </button>
      </div>
    </div>

    <ChatModal
      v-if="chatStore.isChatOpen && isMobile"
      :supplier="chatStore.supplier"
      :chatMessages="chatStore.chatMessages"
      @close="chatStore.closeChat"
      @sendMessage="chatStore.sendMessage"
    />

    <ChatDesktop
      v-if="chatStore.isChatOpen && !isMobile"
      :supplier="chatStore.supplier"
      :chat-messages="chatStore.chatMessages"
      @close="chatStore.closeChat"
      @send-message="chatStore.sendMessage"
    />

    <NotificationToast 
      v-if="showNotification"
      :type="notificationType"
      :title="notificationTitle"
      :message="notificationMessage"
      @close="closeNotification"
    />
  </div>
</template>

<script setup>
import { ref, computed, defineProps, defineEmits, onMounted } from 'vue'
import ChatModal from '../product/modals/ChatModal2.vue'
import ChatDesktop from '../product/modals/ChatWindow.vue'
import { formatPrice } from '../../services/formatPrice'
import { useChatStore } from '../../stores/chat'
import { productsApi } from '../../services/api.js'
import NotificationToast from '../product/NotificationToast.vue'
import { useFavoritesStore } from '../../stores/favorites'

const favoritesStore = useFavoritesStore()

const notificationType = ref('success')
const notificationTitle = ref('')
const notificationMessage = ref('')
const showNotification = ref(false)

const props = defineProps({
  product: { type: Object, required: true },
  isMobile: { type: Boolean, default: false },
  showAdBadge: { type: Boolean, default: false },
  cardHeight: { type: [String, Number], default: 230 },
  imageHeight: { type: [String, Number], default: 200 },
})

const emit = defineEmits(['product-click', 'favorite-click', 'contact-click', 'chat-click'])
const chatStore = useChatStore()

const currentImageIndex = ref(0)

const isTrailer = computed(() => {
  return props.product.category_name?.toLowerCase().includes('trailer') ||
         props.product.trailer_type ||
         props.product.trailer_condition ||
         props.product.trailer_axle
})

onMounted(() => {
  if (props.product.likedByUser === 1) {
    favoritesStore.addLocalFavorite({
      id_produit: props.product.id
    })
  }
})

const isFavorite = computed(() =>
  favoritesStore.favorites.some(f => f.id_produit === props.product.id)
)

const toggleFavorite = async () => {
  try {
    const userData = localStorage.getItem('user') || sessionStorage.getItem('user')
    const user = JSON.parse(userData)

    const result = await favoritesStore.toggleFavorite(props.product.id, user.id)

    if (result.success) {
      displayNotification(
        isFavorite.value ? 'success' : 'info',
        isFavorite.value ? 'Added to favorites' : 'Removed from favorites',
        isFavorite.value
          ? 'This vehicle was saved in Favorite cars in your account.'
          : 'Product removed from your favorites.'
      )
    } else {
      displayNotification('error', 'Error', result.error || 'Impossible to update favorites.')
    }
  } catch (error) {
    console.error('Erreur toggleFavorite:', error)
    displayNotification('error', 'Error', 'Error to update favorites.')
  }
}

const displayNotification = (type, title, message) => {
  notificationType.value = type
  notificationTitle.value = title
  notificationMessage.value = message
  showNotification.value = true

  setTimeout(() => {
    closeNotification()
  }, 5000)
}

const closeNotification = () => {
  showNotification.value = false
}

const cardContentHeight = computed(() => typeof props.cardHeight === 'number' ? `${props.cardHeight}px` : props.cardHeight)
const cardImageHeight = computed(() => typeof props.imageHeight === 'number' ? `${props.imageHeight}px` : props.imageHeight)

const productImages = computed(() => {
  if (props.product.images && Array.isArray(props.product.images)) return props.product.images
  if (props.product.primary_image) return [props.product.primary_image]
  return ['/placeholder.svg?height=300&width=300']
})

const currentImage = computed(() => productImages.value[currentImageIndex.value] || '/placeholder.svg?height=300&width=300')

const nextImage = () => {
  currentImageIndex.value = (currentImageIndex.value + 1) % productImages.value.length
}
const previousImage = () => {
  currentImageIndex.value = (currentImageIndex.value - 1 + productImages.value.length) % productImages.value.length
}
const setImage = (imageIndex) => { currentImageIndex.value = imageIndex }

const handleProductClick = () => emit('product-click', props.product)
const handleFavoriteClick = () => emit('favorite-click', props.product)
const handleContactClick = () => emit('contact-click', props.product)

const handleChatClick = async () => {
  try {
    await chatStore.setSupplier(props.product)
    if (chatStore.isMobile) {
      chatStore.openChat()
    } else {
      chatStore.openDesktopChat()
    }
    emit('chat-click', props.product)
  } catch (error) {
    console.error("❌ Erreur ouverture chat:", error)
  }
}
</script>

<style scoped>
/* Card Container - Style du filtre */
.alibaba-product-card {
  padding-bottom: 5px;
  background: #ffffff;
  border: 1px solid #e5e7eb;
  border-radius: 12px;
  overflow: hidden;
  cursor: pointer;
  transition: all 0.3s ease;
  position: relative;
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
}

.alibaba-product-card:hover {
  box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
  transform: translateY(-2px);
  border-color: #d1d5db;
}

/* Image Area */
.alibaba-image-area {
  position: relative;
  width: 100%;
  height: 200px;
  overflow: hidden;
  background: #f9fafb;
}

.alibaba-slider {
  width: 100%;
  height: 100%;
  position: relative;
}

.alibaba-slider-wrapper {
  width: 100%;
  height: 100%;
  position: relative;
}

.alibaba-slider-link {
  display: block;
  width: 100%;
  height: 100%;
}

.alibaba-slider-img {
  padding: 10px;
  width: 100%;
  height: 100%;
  object-fit: cover;
  transition: transform 0.3s ease;
  border-radius: 16px;
}

.alibaba-product-card:hover .alibaba-slider-img {
  transform: scale(1.03);
}

/* Navigation Arrows */
.alibaba-arrow-left,
.alibaba-arrow-right {
  position: absolute;
  top: 50%;
  transform: translateY(-50%);
  width: 28px;
  height: 28px;
  background: rgba(0, 0, 0, 0.6);
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  cursor: pointer;
  opacity: 0;
  transition: all 0.3s ease;
  z-index: 5;
  backdrop-filter: blur(4px);
}

.alibaba-product-card:hover .alibaba-arrow-left,
.alibaba-product-card:hover .alibaba-arrow-right {
  opacity: 1;
}

.alibaba-arrow-left:hover,
.alibaba-arrow-right:hover {
  background: rgba(0, 0, 0, 0.8);
  transform: translateY(-50%) scale(1.1);
}

.alibaba-arrow-left {
  left: 12px;
}

.alibaba-arrow-right {
  right: 12px;
}

/* Slider Indicators */
.alibaba-slider-indicators {
  position: absolute;
  bottom: 12px;
  left: 50%;
  transform: translateX(-50%);
  display: flex;
  gap: 6px;
  z-index: 5;
}

.alibaba-indicator {
  width: 6px;
  height: 6px;
  border-radius: 50%;
  background: rgba(255, 255, 255, 0.6);
  cursor: pointer;
  transition: all 0.3s ease;
}

.alibaba-indicator:hover {
  background: rgba(255, 255, 255, 0.8);
  transform: scale(1.2);
}

.alibaba-indicator.active {
  background: white;
  width: 20px;
  border-radius: 3px;
}

/* Favorite Button */
.alibaba-favorite-btn {
  position: absolute;
  top: 12px;
  right: 12px;
  width: 36px;
  height: 36px;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  cursor: pointer;
  transition: all 0.3s ease;
  z-index: 5;
  background: rgba(255, 255, 255, 0.95);
  color: #6b7280;
  backdrop-filter: blur(8px);
}

.alibaba-favorite-btn:hover {
  transform: scale(1.15);
  background: #fee2e2;
  color: #dc2626;
}

.alibaba-favorite-btn.is-liked {
  background: #dc2626 !important;
  color: white !important;
}

.alibaba-favorite-btn.is-liked:hover {
  background: #b91c1c !important;
}

/* Discount Badge */
.discount-badge {
  position: absolute;
  top: 12px;
  left: 12px;
  background: #dc2626;
  color: white;
  padding: 6px 10px;
  border-radius: 8px;
  font-size: 11px;
  font-weight: 600;
  z-index: 5;
  box-shadow: 0 2px 8px rgba(220, 38, 38, 0.3);
}

/* Vehicle Type Badge */
.vehicle-type-badge {
  position: absolute;
  bottom: 12px;
  left: 12px;
  display: flex;
  align-items: center;
  gap: 4px;
  padding: 4px 10px;
  border-radius: 6px;
  font-size: 11px;
  font-weight: 600;
  z-index: 5;
  backdrop-filter: blur(8px);
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.15);
}

.truck-badge {
  background: rgba(59, 130, 246, 0.95);
  color: white;
}

.trailer-badge {
  background: rgba(139, 92, 246, 0.95);
  color: white;
}

.vehicle-type-badge svg {
  width: 14px;
  height: 14px;
}

/* Card Content - Desktop par défaut: 230px */
.alibaba-card-content {
  padding: 12px;
  display: flex;
  flex-direction: column;
  gap: 8px;
  min-height: 230px;
}

.product-info-section {
  flex: 1;
  display: flex;
  flex-direction: column;
  gap: 8px;
}

/* Title */
.alibaba-title {
  font-size: 14px;
  font-weight: 500;
  color: #111827;
  margin: 0;
  line-height: 1.4;
  display: -webkit-box;
  -webkit-line-clamp: 2;
  line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
  transition: color 0.2s ease;
}

.alibaba-product-card:hover .alibaba-title {
  color: #fe7900;
}

/* Pricing */
.product-pricing {
  margin: 0;
}

.price-row {
  display: flex;
  align-items: center;
  gap: 8px;
}

.current-price {
  font-size: 18px;
  font-weight: 700;
  color: #fe7900;
}

.original-price {
  font-size: 14px;
  color: #9ca3af;
  text-decoration: line-through;
}

/* Shipping Badge */
.shipping-badge {
  display: inline-flex;
  align-items: center;
  gap: 4px;
  font-size: 11px;
  font-weight: 500;
  color: #059669;
  background: #d1fae5;
  padding: 4px 8px;
  border-radius: 6px;
  width: fit-content;
}

.vehicle-specs-container {
  display: flex;
  flex-direction: column;
  gap: 4px;
  background: #f9fafb;
  padding: 8px;
  border-radius: 8px;
  border: 1px solid #f3f4f6;
}

.vehicle-spec-row {
  display: flex;
  align-items: center;
  gap: 8px;
  padding: 2px 0;
}

.spec-icon-wrapper {
  width: 24px;
  height: 24px;
  display: flex;
  align-items: center;
  justify-content: center;
  background: #ffffff;
  border-radius: 6px;
  flex-shrink: 0;
  border: 1px solid #e5e7eb;
}

.spec-icon {
  flex-shrink: 0;
  color: #6b7280;
  width: 12px;
  height: 12px;
}

.spec-content {
  display: flex;
  align-items: center;
  gap: 6px;
  flex: 1;
}

.spec-label {
  font-size: 11px;
  color: #6b7280;
  font-weight: 500;
  min-width: 65px;
}

.spec-value {
  font-size: 11px;
  color: #111827;
  font-weight: 600;
  text-transform: capitalize;
}

.alibaba-action-buttons {
  display: flex;
  gap: 8px;
  margin-top: auto;
}

.btn-view,
.btn-chat {
  flex: 1;
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 6px;
  padding: 8px 12px;
  border: none;
  border-radius: 8px;
  font-size: 12px;
  font-weight: 500;
  cursor: pointer;
  transition: all 0.2s ease;
}

.btn-view {
  background: #ffffff;
  color: #374151;
  border: 1px solid #e5e7eb;
}

.btn-view:hover {
  background: #f9fafb;
  border-color: #d1d5db;
  transform: translateY(-1px);
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
}

.btn-chat {
  background: #fe7900;
  color: #ffffff;
  border: 1px solid #fe7900;
}

.btn-chat:hover {
  background: #e66e00;
  border-color: #e66e00;
  transform: translateY(-1px);
  box-shadow: 0 4px 12px rgba(254, 121, 0, 0.3);
}

.btn-view svg,
.btn-chat svg {
  width: 16px;
  height: 16px;
}

/* List View Mode */
.search-product-card.list-card {
  height: auto;
  padding: 0 !important;
}

.search-product-card.list-card .alibaba-card-content {
  flex-direction: row;
  gap: 20px;
  padding: 20px;
}

.search-product-card.list-card .alibaba-image-area {
  width: 280px !important;
  height: 280px !important;
  flex-shrink: 0;
}

.search-product-card.list-card .product-info-section {
  flex: 1;
  min-width: 0;
}

.search-product-card.list-card .alibaba-action-buttons {
  flex-direction: column;
  width: 140px;
  flex-shrink: 0;
  margin-top: 0;
}

.search-product-card.list-card .vehicle-specs-container {
  flex-direction: row;
  flex-wrap: wrap;
  gap: 12px;
}

.search-product-card.list-card .vehicle-spec-row {
  flex: 0 0 calc(50% - 6px);
  min-width: 0;
}

/* Mobile Styles - Hauteur augmentée à 250px + bouton "Chat" */
@media (max-width: 768px) {
  .alibaba-product-card {
    border-radius: 8px;
  }

  /* Mobile: hauteur augmentée de 230px à 250px */
  .alibaba-card-content {
    padding: 12px;
    min-height: 250px;
  }

  .alibaba-title {
    font-size: 13px;
  }

  .current-price {
    font-size: 16px;
  }

  .vehicle-specs-container {
    padding: 10px;
  }

  .spec-label {
    min-width: 60px;
  }

  .alibaba-action-buttons {
    flex-direction: row;
  }

  .btn-view,
  .btn-chat {
    padding: 8px 12px;
    font-size: 12px;
  }

  /* Cache "Chat Now" en mobile */
  .btn-text-desktop {
    display: none;
  }

  /* Affiche "Chat" en mobile */
  .btn-text-mobile {
    display: inline;
  }
}

/* Desktop - Cache "Chat" et affiche "Chat Now" */
@media (min-width: 769px) {
  .btn-text-mobile {
    display: none;
  }

  .btn-text-desktop {
    display: inline;
  }
}
</style>