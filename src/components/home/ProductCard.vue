<template>
    <!-- Version Mobile -->
    <div 
      v-if="isMobile"
      class="mobile-grid-product"
      @click="handleProductClick"
    >
      <div class="mobile-grid-image">
        <img :src="product.primary_image || product.images?.[0] || '/placeholder.svg?height=200&width=200'" :alt="product.name" />
        <button class="favorite-btn-mobile" @click.stop="handleFavoriteClick">
          <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"/>
          </svg>
        </button>
      </div>
      <div class="mobile-grid-info">
        <h3 class="mobile-grid-title">{{ product.name }}</h3>
        <div class="mobile-grid-price">{{ formatPrice(product.unit_price) }}</div>
        <div class="wholesale-price" style="margin-bottom: 8px;" v-if="product.wholesale_price || product.wholesalePrice">
            <span class="min-quantity">≥ {{ product.wholesale_min_qty || product.minQuantity || 10 }} pcs :</span>
            <span class="wholesale-amount">{{ formatPrice(product.wholesale_price || product.wholesalePrice) }}</span>
        </div>
        <div v-if="product.freeShipping" class="shipping-info">
          <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <rect x="1" y="3" width="15" height="13"></rect>
            <polygon points="16 8 20 8 23 11 23 16 16 16 16 8"></polygon>
            <circle cx="5.5" cy="18.5" r="2.5"></circle>
            <circle cx="18.5" cy="18.5" r="2.5"></circle>
          </svg>
          <span>Livraison gratuite</span>
        </div>
    
        <div class="alibaba-supplier-info">
          <div class="alibaba-supplier-years">
            <span>{{ product.market || 'CI' }}</span>
          </div>
          <div class="alibaba-review">
            <span v-if="product.reviews">({{ product.reviews }} reviews)</span>
          </div>
        </div>
        <div class="mobile-grid-shop">{{ product.boutique_name || product.shopName }}</div>
        <div class="mobile-grid-rating">
          <span class="stars">⭐⭐⭐⭐⭐</span>
          <span class="rating-count">({{ product.views || product.reviews || 0 }})</span>
        </div>
        <div class="alibaba-action-buttons">
          <button class="alibaba-contact-btn" @click.stop="handleContactClick">View</button>
          <button class="alibaba-chat-btn" @click.stop="handleChatClick">Chat now</button>
        </div>
      </div>
    </div>
    
    <!-- Version Desktop -->
    <div 
      v-else
      class="alibaba-product-card"
      
    >
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
            
            <!-- Flèches de navigation (seulement si plusieurs images) -->
            <div 
              v-if="productImages.length > 1"
              class="alibaba-arrow-left" 
              @click.stop="previousImage"
            >
              <svg width="10" height="17" viewBox="0 0 10 17" fill="none">
                <path d="M9 1L1 8.5L9 16" stroke="#fff" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
              </svg>
            </div>
            <div 
              v-if="productImages.length > 1"
              class="alibaba-arrow-right" 
              @click.stop="nextImage"
            >
              <svg width="10" height="17" viewBox="0 0 10 17" fill="none"  >
                <path d="M1 1L9 8.5L1 16" stroke="#fff" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
              </svg>
            </div>
            
            <!-- Indicateurs (seulement si plusieurs images) -->
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
        
        <div class="alibaba-favorite-btn" @click.stop="handleFavoriteClick">
          <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"></path>
          </svg>
        </div>
        
        <!-- Badge de réduction (si applicable) -->
        <div v-if="product.discount" class="discount-badge">-{{ product.discount }}%</div>
      </div>
    
      <div class="alibaba-card-content" :style="{ height: cardContentHeight }">
        <div class="product-info-section">
          <h2 class="alibaba-title">
            <span>{{ product.name }}</span>
          </h2>
    
          <div class="product-pricing">
            <div class="unit-price">
              <span class="current-price">{{ formatPrice(product.unit_price || product.unitPrice) }}</span>
              <span v-if="product.originalPrice" class="original-price">{{ formatPrice(product.originalPrice) }}</span>
            </div>
            <div class="wholesale-price" v-if="product.wholesale_price || product.wholesalePrice">
              <span class="price-label">En gros:</span>
              <span class="wholesale-amount">{{ formatPrice(product.wholesale_price || product.wholesalePrice) }}</span>
              <span class="min-quantity">≥ {{ product.wholesale_min_qty || product.minQuantity || 10 }} pcs</span>
            </div>
          </div>
    
          <!-- Info livraison gratuite -->
          <div v-if="product.freeShipping" class="shipping-info">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <rect x="1" y="3" width="15" height="13"></rect>
              <polygon points="16 8 20 8 23 11 23 16 16 16 16 8"></polygon>
              <circle cx="5.5" cy="18.5" r="2.5"></circle>
              <circle cx="18.5" cy="18.5" r="2.5"></circle>
            </svg>
            <span>Livraison gratuite</span>
          </div>
    
          <div class="alibaba-company">
            <span>{{ product.boutique_name || product.shopName || 'Boutique' }}</span>
          </div>
    
          <div class="alibaba-supplier-info">
            <div class="alibaba-supplier-years">
              <span>{{ product.market || 'CI' }}</span>
            </div>
            <div class="alibaba-review">
              <span v-if="product.reviews">({{ product.reviews }} reviews)</span>
            </div>
          </div>
    
          <!-- Images similaires (si disponibles) -->
          <div v-if="productImages.length > 1" class="alibaba-similar-products">
            <div 
              v-for="(img, imgIndex) in productImages.slice(0, 3)" 
              :key="imgIndex"
              class="alibaba-mini-product"
            >
              <img :src="img" alt="Similar product" loading="lazy">
            </div>
          </div>
        </div>
    
       <div class="alibaba-action-buttons">
          <button class="alibaba-contact-btn" @click.stop="handleProductClick">View</button>
          <button class="alibaba-chat-btn" @click.stop="handleChatClick">Chat now</button>
        </div>
    
        <!-- Badge publicitaire (si applicable) -->
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
    </div>
    </template>
    
    <script setup>
    import { ref, computed, defineProps, defineEmits } from 'vue'
    import ChatModal from '../product/modals/ChatModal2.vue'
import ChatDesktop from '../product/modals/ChatWindow.vue'
import ChatApiClient from '../../services/chat-api-client'
    
    // Props
    const props = defineProps({
      product: {
        type: Object,
        required: true
      },
      isMobile: {
        type: Boolean,
        default: false
      },
      showAdBadge: {
        type: Boolean,
        default: false
      },
      cardHeight: {
        type: [String, Number],
        default: 230,
        validator: (value) => {
          // Accepter les nombres ou les chaînes avec unités CSS
          return typeof value === 'number' || /^\d+(px|rem|em|vh|%)$/.test(value)
        }
      },
      imageHeight: {
        type: [String, Number],
        default: 200,
        validator: (value) => {
          // Accepter les nombres ou les chaînes avec unités CSS
          return typeof value === 'number' || /^\d+(px|rem|em|vh|%)$/.test(value)
        }
      }
    })

    import { useChatStore } from '../../stores/chat'

    const chatStore = useChatStore()
    
    // Emits
    const emit = defineEmits([
      'product-click',
      'favorite-click', 
      'contact-click',
      'chat-click'
    ])
    
    // État local pour la navigation des images
    const currentImageIndex = ref(0)
    
    // Computed pour la hauteur dynamique
    const cardContentHeight = computed(() => {
      if (typeof props.cardHeight === 'number') {
        return `${props.cardHeight}px`
      }
      return props.cardHeight
    })
    
    const cardImageHeight = computed(() => {
      if (typeof props.imageHeight === 'number') {
        return `${props.imageHeight}px`
      }
      return props.imageHeight
    })
    
    // Computed
    const productImages = computed(() => {
      if (props.product.images && Array.isArray(props.product.images)) {
        return props.product.images
      }
      if (props.product.primary_image) {
        return [props.product.primary_image]
      }
      return ['/placeholder.svg?height=300&width=300']
    })
    
    const currentImage = computed(() => {
      return productImages.value[currentImageIndex.value] || '/placeholder.svg?height=300&width=300'
    })
    
    const countryFlag = computed(() => {
      const market = props.product.market?.toLowerCase() || 'ci'
      return `https://flagcdn.com/w20/${market}.png`
    })
    
    // Méthodes pour la navigation des images
    const nextImage = () => {
      if (currentImageIndex.value < productImages.value.length - 1) {
        currentImageIndex.value++
      } else {
        currentImageIndex.value = 0
      }
    }
    
    const previousImage = () => {
      if (currentImageIndex.value > 0) {
        currentImageIndex.value--
      } else {
        currentImageIndex.value = productImages.value.length - 1
      }
    }

    
    const setImage = (imageIndex) => {
      currentImageIndex.value = imageIndex
    }
    
    // Fonction pour formater les prix en FCFA
    const formatPrice = (price, currency = 'USD') => {
      if (!price) return '0 FCFA'
    
      const exchangeRates = {
        EUR: 655.957,
        XOF: 1,
        USD: 600
      }
    
      const convertToFcfa = (amount, fromCurrency) => {
        const rate = exchangeRates[fromCurrency]
        if (!rate) throw new Error(`Taux de conversion manquant pour ${fromCurrency}`)
        return amount * rate
      }
    
      let priceInFcfa
    
      if (currency === 'USD') {
        priceInFcfa = price
      } else {
        priceInFcfa = convertToFcfa(price, currency)
      }
    
      return new Intl.NumberFormat('fr-FR', {
        style: 'currency',
        currency: 'USD',
        minimumFractionDigits: 0
      }).format(priceInFcfa)
    }
    
    // Gestionnaires d'événements
    const handleProductClick = () => {
      emit('product-click', props.product)
    }
    
    const handleFavoriteClick = () => {
      emit('favorite-click', props.product)
    }
    
    const handleContactClick = () => {
      emit('contact-click', props.product)
    }
    
    const handleChatClick = () => {
      console.log("✅ handleChatClick triggered", props.product)
      chatStore.setSupplier?.(props.product) // safe call
      chatStore.openChat?.() // safe call
      emit('chat-click', props.product)
    }
    </script>
    
    <style scoped>
    /* Styles mobile */
    .mobile-grid-product {
      background: #f8f8f8;
      border-radius: 12px;
      overflow: hidden;
      transition: transform 0.2s;
      cursor: pointer;
    }
    
    .mobile-grid-product:active {
      transform: scale(0.98);
    }
    
    .mobile-grid-image {
      position: relative;
      width: 100%;
      aspect-ratio: 1;
    }
    
    .mobile-grid-image img {
      width: 100%;
      height: 100%;
      object-fit: cover;
    }
    
    .favorite-btn-mobile {
      position: absolute;
      top: 8px;
      right: 8px;
      width: 32px;
      height: 32px;
      background: rgba(255, 255, 255, 0.9);
      border: none;
      border-radius: 50%;
      display: flex;
      align-items: center;
      justify-content: center;
      color: #999;
    }
    
    .mobile-grid-info {
      padding: 12px;
    }
    
    .mobile-grid-title {
      font-size: 14px;
      color: #333;
      margin: 0 0 8px 0;
      line-height: 1.3;
      height: 36px;
      overflow: hidden;
      display: -webkit-box;
      -webkit-line-clamp: 2;
      line-clamp: 2;
      -webkit-box-orient: vertical;
    }
    
    .mobile-grid-price {
      font-size: 16px;
      font-weight: 700;
      color: #fe7900;
      margin-bottom: 4px;
    }
    
    .mobile-grid-shop {
      font-size: 12px;
      color: #666;
      margin-bottom: 6px;
    }
    
    .mobile-grid-rating {
      display: flex;
      align-items: center;
      gap: 4px;
      font-size: 12px;
    }
    
    .stars {
      color: #ffa500;
      font-size: 10px;
    }
    
    .rating-count {
      color: #999;
    }
    
    /* Styles desktop */
    .alibaba-product-card {
      background: #ffffff;
      border: 1px solid #e8e8e8;
      border-radius: 8px;
      overflow: hidden;
      cursor: pointer;
      transition: all 0.3s ease;
      position: relative;
    }
    
    .alibaba-product-card:hover {
      box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
      transform: translateY(-2px);
    }
    
    .alibaba-image-area {
      position: relative;
      width: 100%;
      height: 200px;
      overflow: hidden;
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
      border-radius: 20px;
    }
    
    .alibaba-product-card:hover .alibaba-slider-img {
      transform: scale(1.05);
    }
    
    .alibaba-arrow-left,
    .alibaba-arrow-right {
      position: absolute;
      top: 50%;
      transform: translateY(-50%);
      width: 24px;
      height: 24px;
      background: rgba(0, 0, 0, 0.5);
      color: #fe7900;
      border-radius: 50%;
      display: flex;
      align-items: center;
      justify-content: center;
      cursor: pointer;
      opacity: 0;
      transition: all 0.3s ease;
      z-index: 5;
    }
    
    .alibaba-product-card:hover .alibaba-arrow-left,
    .alibaba-product-card:hover .alibaba-arrow-right {
      opacity: 1;
    }
    
    .alibaba-arrow-left {
      left: 8px;
    }
    
    .alibaba-arrow-right {
      right: 8px;
    }
    
    .alibaba-slider-indicators {
      position: absolute;
      bottom: 8px;
      left: 50%;
      transform: translateX(-50%);
      display: flex;
      gap: 4px;
      z-index: 5;
    }
    
    .alibaba-indicator {
      width: 6px;
      height: 6px;
      border-radius: 50%;
      background: rgba(255, 255, 255, 0.5);
      cursor: pointer;
      transition: all 0.3s ease;
    }
    
    .alibaba-indicator.active {
      background: white;
    }
    
    .alibaba-favorite-btn {
      position: absolute;
      top: 8px;
      right: 8px;
      width: 32px;
      height: 32px;
      background: #fe7900;
      border-radius: 50%;
      display: flex;
      align-items: center;
      justify-content: center;
      cursor: pointer;
      transition: all 0.3s ease;
      z-index: 5;
    }
    
    .alibaba-favorite-btn:hover {
      background: #ff4704;
      transform: scale(1.1);
    }
    
    .discount-badge {
      position: absolute;
      top: 8px;
      left: 8px;
      background: #ff4747;
      color: white;
      padding: 4px 8px;
      border-radius: 12px;
      font-size: 10px;
      font-weight: 600;
      z-index: 5;
    }
    
    .alibaba-card-content {
      padding: 16px;
      position: relative;
    }
    
    .alibaba-title {
      font-size: 14px;
      font-weight: 500;
      color: #333;
      margin: 0 0 12px 0;
      line-height: 1.4;
      display: -webkit-box;
      -webkit-line-clamp: 2;
      line-clamp: 2;
      -webkit-box-orient: vertical;
      overflow: hidden;
    }
    
    .product-pricing {
      margin-bottom: 12px;
    }
    
    .unit-price {
      display: flex;
      align-items: center;
      gap: 8px;
      margin-bottom: 4px;
    }
    
    .current-price {
      font-size: 16px;
      font-weight: 700;
      color: #fe7900;
    }
    
    .original-price {
      font-size: 14px;
      color: #999;
      text-decoration: line-through;
    }
    
    .wholesale-price {
      display: flex;
      align-items: center;
      gap: 4px;
      font-size: 12px;
      color: #666;
    }
    
    .price-label {
      color: #999;
    }
    
    .wholesale-amount {
      font-weight: 600;
      color: #007185;
    }
    
    .min-quantity {
      color: #999;
    }
    
    .shipping-info {
      display: flex;
      align-items: center;
      gap: 4px;
      font-size: 12px;
      color: #007185;
      margin-bottom: 8px;
    }
    
    .alibaba-company {
      font-size: 12px;
      color: #333;
      font-weight: 500;
      margin-bottom: 8px;
    }
    
    .alibaba-supplier-info {
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin-bottom: 12px;
      font-size: 12px;
    }
    
    .alibaba-supplier-years {
      display: flex;
      align-items: center;
      gap: 4px;
      color: #666;
    }
    
    .alibaba-country-flag {
      display: flex;
      align-items: center;
    }
    
    .alibaba-review {
      color: #666;
    }
    
    .alibaba-similar-products {
      display: flex;
      gap: 4px;
      margin-bottom: 12px;
    }
    
    .alibaba-mini-product {
      width: 30px;
      height: 30px;
      border-radius: 4px;
      overflow: hidden;
    }
    
    .alibaba-mini-product img {
      width: 100%;
      height: 100%;
      object-fit: cover;
    }
    
    .alibaba-action-buttons {
      display: flex;
      gap: 8px;
      position: absolute;
      bottom: 16px;
      left: 16px;
      right: 16px;
    }
    
    .alibaba-contact-btn,
    .alibaba-chat-btn {
      flex: 1;
      padding: 8px 12px;
      border: 1px solid #fe7900;
      border-radius: 4px;
      font-size: 12px;
      font-weight: 500;
      cursor: pointer;
      transition: all 0.3s ease;
    }
    
    .alibaba-contact-btn {
      background: white;
      color: #fe7900;
    }
    
    .alibaba-contact-btn:hover {
      background: #fe7900;
      color: white;
    }
    
    .alibaba-chat-btn {
      background: #fe7900;
      color: white;
    }
    
    .alibaba-chat-btn:hover {
      background: white;
      color: #fd342db8;
    }
    
    .alibaba-ad-badge {
      position: absolute;
      top: 8px;
      right: 8px;
      background: rgba(0, 0, 0, 0.7);
      color: white;
      padding: 2px 6px;
      border-radius: 2px;
      font-size: 10px;
    }
    
    /* Styles spécifiques pour le mode liste */
    .search-product-card.list-card {
      height: 280px;
      padding: 0px !important;
    }
    
    .search-product-card.list-card .alibaba-card-content {
      display: flex;
      flex-direction: row;
      gap: 16px;
      height: 200px;
    }
    
    .search-product-card.list-card .alibaba-action-buttons {
      position: static;
      margin-left: auto;
      width: auto;
      min-width: 200px;
      flex-direction: column;
      align-self: flex-start;
      margin-top: 16px;
    }
    
    .search-product-card.list-card .product-info-section {
      flex: 1;
    }
    
    .search-product-card.list-card .alibaba-action-buttons {
      flex-shrink: 0;
    }

    .search-product-card.list-card .alibaba-slider-wrapper {
        width: 300px; /* ou la taille que tu veux */
    }

    .search-product-card.list-card .alibaba-slider-link {
        width: 300px; /* ou la taille que tu veux */
    }

    .search-product-card.list-card .alibaba-slider{
        width: 300px; /* ou la taille que tu veux */
    }
    .search-product-card.list-card .alibaba-image-area {
        width: 300px !important;
        height: 275px !important; 
    }
    .search-product-card.list-card .alibaba-mini-product {
        width: 50px; /* au lieu de 30px */
        height: 50px; /* au lieu de 30px */
        margin: 5px
    }

    .mobile-grid-product .alibaba-action-buttons {
  display: flex;
  gap: 8px;
  margin-top: 10px;
}

.mobile-grid-product .alibaba-contact-btn,
.mobile-grid-product .alibaba-chat-btn {
  flex: 1;
  padding: 8px;
  font-size: 12px;
  border-radius: 4px;
  border: 1px solid #fe7900;
  font-weight: 500;
  cursor: pointer;
}

.mobile-grid-product .alibaba-contact-btn {
  background-color: white;
  color: #fe7900;
}

.mobile-grid-product .alibaba-chat-btn {
  background-color: #fe7900;
  color: white;
}

.mobile-grid-product .alibaba-action-buttons {
  display: flex;
  flex-direction: row; /* ou column si tu veux vertical */
  gap: 8px;
  margin-top: 12px;
  position: static; /* important pour éviter les problèmes d'absolute */
}
    
    </style>