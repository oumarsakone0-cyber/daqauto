<template>
    <!-- Version Mobile -->
    <section class="mobile-flash-deals mobile-only">
      <div class="section-header-mobile">
        <div class="section-title-mobile">
          <span class="flash-icon">⚡</span>
          <h2>Flash Deals</h2>
          <div class="countdown-mobile">
            <span>{{ countdown.hours.toString().padStart(2, '0') }}:</span>
            <span>{{ countdown.minutes.toString().padStart(2, '0') }}:</span>
            <span>{{ countdown.seconds.toString().padStart(2, '0') }}</span>
          </div>
        </div>
        <button class="see-all-btn">Voir tout</button>
      </div>
      
      <div class="mobile-products-scroll">
        <div 
          v-for="(product, index) in dealProducts.slice(0, 6)" 
          :key="index"
          class="mobile-product-card"
          @click="navigateToProduct(product)"
        >
          <div class="mobile-product-image">
            <img :src="product.images[0]" :alt="product.name" />
            <div class="discount-badge-mobile">-{{ product.discount }}%</div>
          </div>
          <div class="mobile-product-info">
            <div class="mobile-product-price">{{ formatPrice(product.unitPrice) }}</div>
            <div class="mobile-product-original">{{ formatPrice(product.originalPrice) }}</div>
          </div>
        </div>
      </div>
    </section>
  
    <!-- Version Desktop -->
    <section class="deals-section desktop-only">
      <div class="section-content">
        <div class="section-header">
          <div class="title-countdown-group">
            <h2 class="section-title">⚡ Super Deals - Offres Flash</h2>
            <div class="countdown">
              <span class="countdown-label">Se termine dans :</span>
              <div class="countdown-timer">
                <div class="countdown-unit">
                  <span class="countdown-value">{{ countdown.hours.toString().padStart(2, '0') }}</span>
                  <span class="countdown-label">h</span>
                </div>
                <span class="countdown-separator">:</span>
                <div class="countdown-unit">
                  <span class="countdown-value">{{ countdown.minutes.toString().padStart(2, '0') }}</span>
                  <span class="countdown-label">m</span>
                </div>
                <span class="countdown-separator">:</span>
                <div class="countdown-unit">
                  <span class="countdown-value">{{ countdown.seconds.toString().padStart(2, '0') }}</span>
                  <span class="countdown-label">s</span>
                </div>
              </div>
            </div>
          </div>
          <a href="#" class="view-all">Voir tout →</a>
        </div>
        
        <div class="products-grid-dense">
          <div 
            v-for="(product, index) in dealProducts" 
            :key="index" 
            class="alibaba-product-card"
            @click="navigateToProduct(product)"
          >
            <!-- Image principale avec slider -->
            <div class="alibaba-image-area">
              <div class="alibaba-slider">
                <div class="alibaba-slider-wrapper">
                  <div class="alibaba-slider-link">
                    <img :src="product.images[product.currentImageIndex || 0]" :alt="product.name" class="alibaba-slider-img" loading="lazy">
                  </div>
                  <div class="alibaba-arrow-left" @click.stop="previousImage(index)">
                    <svg width="10" height="17" viewBox="0 0 10 17" fill="none">
                      <path d="M9 1L1 8.5L9 16" stroke="#fff" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                  </div>
                  <div class="alibaba-arrow-right" @click.stop="nextImage(index)">
                    <svg width="10" height="17" viewBox="0 0 10 17" fill="none">
                      <path d="M1 1L9 8.5L1 16" stroke="#fff" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                  </div>
                  <div class="alibaba-slider-indicators">
                    <div 
                      v-for="(img, imgIndex) in product.images" 
                      :key="imgIndex"
                      class="alibaba-indicator" 
                      :class="{ 'active': imgIndex === (product.currentImageIndex || 0) }"
                      @click.stop="setImage(index, imgIndex)"
                    ></div>
                  </div>
                </div>
              </div>
              
              <div class="alibaba-favorite-btn" @click.stop="toggleFavorite(product)">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                  <path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"></path>
                </svg>
              </div>
              <div class="discount-badge">-{{ product.discount }}%</div>
            </div>
  
            <!-- Contenu de la carte -->
            <div class="alibaba-card-content">
              <h2 class="alibaba-title">
                <span>{{ product.name }}</span>
              </h2>
  
              <div class="product-pricing">
                <div class="unit-price">
                  <span class="current-price">{{ formatPrice(product.unitPrice) }}</span>
                  <span class="original-price" v-if="product.originalPrice">{{ formatPrice(product.originalPrice) }}</span>
                </div>
                <div class="wholesale-price">
                  <span class="price-label">Prix de gros:</span>
                  <span class="wholesale-amount">{{ formatPrice(product.wholesalePrice) }}</span>
                  <span class="min-quantity">≥ {{ product.minQuantity }} pcs</span>
                </div>
              </div>
  
              <div class="shipping-info" v-if="product.freeShipping">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                  <rect x="1" y="3" width="15" height="13"></rect>
                  <polygon points="16 8 20 8 23 11 23 16 16 16 16 8"></polygon>
                  <circle cx="5.5" cy="18.5" r="2.5"></circle>
                  <circle cx="18.5" cy="18.5" r="2.5"></circle>
                </svg>
                <span>Livraison gratuite</span>
              </div>
  
              <div class="alibaba-sale-features">
                <div class="alibaba-sale-item">Min. order: {{ product.minQuantity }} pieces</div>
                <div class="alibaba-sale-item" v-if="product.freeShipping">Free Shipping</div>
              </div>
  
              <div class="alibaba-company">
                <span>{{ product.shopName }}</span>
              </div>
  
              <div class="alibaba-supplier-info">
                <div class="alibaba-supplier-years">
                  <span>5 yrs</span>
                  <div class="alibaba-country-flag">
                    <img src="https://flagcdn.com/w20/cn.png" alt="cn" style="width:16px">
                  </div>
                  <span>CN</span>
                </div>
                <div class="alibaba-review">
                  <span>{{ product.rating }}</span>/5.0 (<span>{{ product.reviews }} reviews</span>)
                </div>
              </div>
  
              <div class="alibaba-similar-products">
                <div class="alibaba-mini-product" v-for="(img, imgIndex) in product.images.slice(0, 3)" :key="imgIndex">
                  <img :src="img" alt="Similar product" loading="lazy">
                </div>
              </div>
  
              <div class="alibaba-action-buttons">
                <button class="alibaba-contact-btn" @click.stop="contactSupplier(product)">Contacter</button>
                <button class="alibaba-chat-btn" @click.stop="chatWithSupplier(product)">Chat now</button>
              </div>
  
              <div class="alibaba-ad-badge">Ad</div>
            </div>
          </div>
        </div>
      </div>
    </section>
  </template>
  
  <script setup>
  import { ref, onMounted, onBeforeUnmount } from 'vue'
  import { useRouter } from 'vue-router'
  
  const router = useRouter()
  
  // Compte à rebours
  const countdown = ref({
    hours: 12,
    minutes: 34,
    seconds: 56
  })
  
  // Produits en promotion (données statiques avec slugs)
  const dealProducts = ref([
    {
      id: 1,
      name: 'Écouteurs sans fil Bluetooth 5.0 avec réduction de bruit active et étui de charge rapide',
      slug: 'ecouteurs-sans-fil-bluetooth-5-0-reduction-bruit',
      market: 'Guangzhou, Chine',
      shopName: 'TechWorld Electronics Co.',
      unitPrice: 19.99,
      originalPrice: 39.99,
      wholesalePrice: 12.50,
      minQuantity: 50,
      discount: 50,
      rating: 4.5,
      reviews: 2453,
      images: [
        'https://images.unsplash.com/photo-1606400082777-ef05f3c5d38c?w=300&h=300&fit=crop&auto=format',
        'https://images.unsplash.com/photo-1599669454699-248893623440?w=300&h=300&fit=crop&auto=format',
        'https://images.unsplash.com/photo-1608043152269-423dbba4e7e1?w=300&h=300&fit=crop&auto=format'
      ],
      currentImageIndex: 0,
      freeShipping: true
    },
    {
      id: 2,
      name: 'Montre connectée étanche IP68 avec moniteur de fréquence cardiaque et GPS intégré',
      slug: 'montre-connectee-etanche-ip68-gps',
      market: 'Shenzhen, Chine',
      shopName: 'SmartWatch Pro Ltd.',
      unitPrice: 29.99,
      originalPrice: 59.99,
      wholesalePrice: 18.90,
      minQuantity: 30,
      discount: 50,
      rating: 4.2,
      reviews: 1287,
      images: [
        'https://images.unsplash.com/photo-1523275335684-37898b6baf30?w=300&h=300&fit=crop&auto=format',
        'https://images.unsplash.com/photo-1434493789847-2f02dc6ca35d?w=300&h=300&fit=crop&auto=format',
        'https://images.unsplash.com/photo-1508685096489-7aacd43bd3b1?w=300&h=300&fit=crop&auto=format'
      ],
      currentImageIndex: 0,
      freeShipping: true
    },
    {
      id: 3,
      name: 'Chargeur sans fil 15W compatible iPhone Samsung avec indicateur LED',
      slug: 'chargeur-sans-fil-15w-iphone-samsung',
      market: 'Dongguan, Chine',
      shopName: 'PowerTech Solutions',
      unitPrice: 12.99,
      originalPrice: 25.99,
      wholesalePrice: 8.20,
      minQuantity: 100,
      discount: 50,
      rating: 4.7,
      reviews: 856,
      images: [
        'https://images.unsplash.com/photo-1609592806787-3d9c1b8e5e8e?w=300&h=300&fit=crop&auto=format',
        'https://images.unsplash.com/photo-1558618666-fcd25c85cd64?w=300&h=300&fit=crop&auto=format'
      ],
      currentImageIndex: 0,
      freeShipping: false
    },
    {
      id: 4,
      name: 'Caméra de surveillance WiFi 1080P avec vision nocturne infrarouge',
      slug: 'camera-surveillance-wifi-1080p-vision-nocturne',
      market: 'Hangzhou, Chine',
      shopName: 'SecureCam International',
      unitPrice: 34.99,
      originalPrice: 69.99,
      wholesalePrice: 22.40,
      minQuantity: 20,
      discount: 50,
      rating: 4.3,
      reviews: 1542,
      images: [
        'https://images.unsplash.com/photo-1558618666-fcd25c85cd64?w=300&h=300&fit=crop&auto=format',
        'https://images.unsplash.com/photo-1567593810070-7a3d471af022?w=300&h=300&fit=crop&auto=format'
      ],
      currentImageIndex: 0,
      freeShipping: true
    },
    {
      id: 5,
      name: 'Enceinte Bluetooth portable 20W avec basses profondes et résistance à l\'eau IPX7',
      slug: 'enceinte-bluetooth-portable-20w-ipx7',
      market: 'Xiamen, Chine',
      shopName: 'AudioMax Trading Co.',
      unitPrice: 24.99,
      originalPrice: 49.99,
      wholesalePrice: 16.80,
      minQuantity: 40,
      discount: 50,
      rating: 4.6,
      reviews: 2156,
      images: [
        'https://images.unsplash.com/photo-1608043152269-423dbba4e7e1?w=300&h=300&fit=crop&auto=format',
        'https://images.unsplash.com/photo-1545454675-3531b543be5d?w=300&h=300&fit=crop&auto=format'
      ],
      currentImageIndex: 0,
      freeShipping: true
    }
  ])
  
  // Timer
  const countdownInterval = ref(null)
  
  const updateCountdown = () => {
    if (countdown.value.seconds > 0) {
      countdown.value.seconds--
    } else if (countdown.value.minutes > 0) {
      countdown.value.minutes--
      countdown.value.seconds = 59
    } else if (countdown.value.hours > 0) {
      countdown.value.hours--
      countdown.value.minutes = 59
      countdown.value.seconds = 59
    } else {
      countdown.value = { hours: 23, minutes: 59, seconds: 59 }
    }
  }
  
  // Méthodes pour la navigation des images
  const nextImage = (productIndex) => {
    const product = dealProducts.value[productIndex]
    if (product.currentImageIndex < product.images.length - 1) {
      product.currentImageIndex++
    } else {
      product.currentImageIndex = 0
    }
  }
  
  const previousImage = (productIndex) => {
    const product = dealProducts.value[productIndex]
    if (product.currentImageIndex > 0) {
      product.currentImageIndex--
    } else {
      product.currentImageIndex = product.images.length - 1
    }
  }
  
  const setImage = (productIndex, imageIndex) => {
    dealProducts.value[productIndex].currentImageIndex = imageIndex
  }
  
  // Fonction pour formater les prix en FCFA
  const formatPrice = (price, currency = 'XOF') => {
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
  
    if (currency === 'XOF') {
      priceInFcfa = price
    } else {
      priceInFcfa = convertToFcfa(price, currency)
    }
  
    return new Intl.NumberFormat('fr-FR', {
      style: 'currency',
      currency: 'XOF',
      minimumFractionDigits: 0
    }).format(priceInFcfa)
  }
  
  // Fonction pour générer un slug à partir du nom
  const generateSlug = (name) => {
    if (!name) return null
    
    return name
      .toLowerCase()
      .trim()
      .replace(/[àáâãäå]/g, 'a')
      .replace(/[èéêë]/g, 'e')
      .replace(/[ìíîï]/g, 'i')
      .replace(/[òóôõö]/g, 'o')
      .replace(/[ùúûü]/g, 'u')
      .replace(/[ç]/g, 'c')
      .replace(/[ñ]/g, 'n')
      .replace(/[^a-z0-9\s-]/g, '')
      .replace(/\s+/g, '-')
      .replace(/-+/g, '-')
      .replace(/^-|-$/g, '')
  }
  
  // Fonction pour naviguer vers la page de détail du produit
  const navigateToProduct = (product) => {
    console.log('🔄 Navigation vers le produit:', product)
    
    let slug = product.slug
    if (!slug && product.name) {
      slug = generateSlug(product.name)
    }
    
    if (!slug) {
      slug = `produit-${product.id || Date.now()}`
    }
    
    router.push(`/detail_resultat_produit/${slug}`)
  }
  
  // Fonctions pour les actions des boutons
  const toggleFavorite = (product) => {
    console.log('❤️ Toggle favorite pour:', product.name)
  }
  
  const contactSupplier = (product) => {
    console.log('📞 Contact supplier pour:', product.name)
  }
  
  const chatWithSupplier = (product) => {
    console.log('💬 Chat avec supplier pour:', product.name)
  }
  
  onMounted(() => {
    countdownInterval.value = setInterval(updateCountdown, 1000)
  })
  
  onBeforeUnmount(() => {
    if (countdownInterval.value) {
      clearInterval(countdownInterval.value)
    }
  })
  </script>
  
  <style scoped>
  /* Styles mobile */
  .mobile-flash-deals {
    background: white;
    padding: 16px;
    margin-bottom: 8px;
  }
  
  .section-header-mobile {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 16px;
  }
  
  .section-title-mobile {
    display: flex;
    align-items: center;
    gap: 8px;
  }
  
  .section-title-mobile h2 {
    font-size: 18px;
    font-weight: 700;
    color: #333;
    margin: 0;
  }
  
  .flash-icon {
    font-size: 20px;
  }
  
  .countdown-mobile {
    background: #ff4757;
    color: white;
    padding: 4px 8px;
    border-radius: 12px;
    font-size: 12px;
    font-weight: bold;
  }
  
  .see-all-btn {
    background: none;
    border: 1px solid #ff6b35;
    color: #ff6b35;
    padding: 6px 12px;
    border-radius: 16px;
    font-size: 12px;
    font-weight: 600;
  }
  
  .mobile-products-scroll {
    display: flex;
    gap: 12px;
    overflow-x: auto;
    padding-bottom: 8px;
    -webkit-overflow-scrolling: touch;
  }
  
  .mobile-products-scroll::-webkit-scrollbar {
    display: none;
  }
  
  .mobile-product-card {
    flex-shrink: 0;
    width: 120px;
    background: #f8f8f8;
    border-radius: 12px;
    overflow: hidden;
    cursor: pointer;
  }
  
  .mobile-product-image {
    position: relative;
    width: 100%;
    height: 120px;
  }
  
  .mobile-product-image img {
    width: 100%;
    height: 100%;
    object-fit: cover;
  }
  
  .discount-badge-mobile {
    position: absolute;
    top: 8px;
    left: 8px;
    background: #ff4757;
    color: white;
    font-size: 10px;
    font-weight: bold;
    padding: 2px 6px;
    border-radius: 8px;
  }
  
  .mobile-product-info {
    padding: 12px 8px;
  }
  
  .mobile-product-price {
    font-size: 14px;
    font-weight: 700;
    color: #ff4757;
    margin-bottom: 4px;
  }
  
  .mobile-product-original {
    font-size: 12px;
    color: #999;
    text-decoration: line-through;
  }
  
  /* Styles desktop */
  .deals-section {
    padding: 40px 0;
    background: #ffffff;
  }
  
  .section-content {
    max-width: 1500px;
    margin: 0 auto;
    padding: 0 20px;
  }
  
  .section-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 24px;
  }
  
  .title-countdown-group {
    display: flex;
    align-items: center;
    gap: 20px;
  }
  
  .section-title {
    font-size: 28px;
    font-weight: 700;
    color: #333;
    margin: 0;
    display: flex;
    align-items: center;
    gap: 8px;
  }
  
  .countdown {
    display: flex;
    align-items: center;
    gap: 8px;
  }
  
  .countdown-label {
    font-size: 14px;
    color: #666;
  }
  
  .countdown-timer {
    display: flex;
    align-items: center;
    gap: 4px;
  }
  
  .countdown-unit {
    background: #fb542f;
    color: white;
    padding: 4px 8px;
    border-radius: 4px;
    text-align: center;
    min-width: 35px;
  }
  
  .countdown-value {
    font-size: 16px;
    font-weight: 700;
    display: block;
  }
  
  .countdown-unit .countdown-label {
    font-size: 10px;
    color: rgba(255, 255, 255, 0.8);
  }
  
  .countdown-separator {
    font-size: 18px;
    font-weight: 700;
    color: #fb542f;
  }
  
  .view-all {
    color: #fb542f;
    text-decoration: none;
    font-weight: 600;
    font-size: 14px;
    transition: all 0.3s ease;
    padding: 8px 16px;
    border-radius: 20px;
    border: 2px solid #fb542f;
  }
  
  .view-all:hover {
    background: #fb542f;
    color: white;
    transform: translateX(4px);
  }
  
  /* Grille de produits dense */
  .products-grid-dense {
    display: grid;
    grid-template-columns: repeat(5, 1fr);
    gap: 20px;
  }
  
  /* Carte produit Alibaba style */
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
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: transform 0.3s ease;
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
    background: rgba(255, 255, 255, 0.9);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    transition: all 0.3s ease;
    z-index: 5;
  }
  
  .alibaba-favorite-btn:hover {
    background: white;
    transform: scale(1.1);
  }
  
  .discount-badge {
    position: absolute;
    top: 8px;
    left: 8px;
    background: #fb542f;
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
    height: 230px;
  }
  
  .alibaba-title {
    font-size: 14px;
    font-weight: 500;
    color: #333;
    margin: 0 0 12px 0;
    line-height: 1.4;
    display: -webkit-box;
    -webkit-line-clamp: 2;
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
    color: #ff4757;
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
  
  .alibaba-sale-features {
    margin-bottom: 8px;
  }
  
  .alibaba-sale-item {
    font-size: 12px;
    color: #666;
    margin-bottom: 2px;
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
    border: 1px solid #fb542fe8;
    border-radius: 4px;
    font-size: 12px;
    font-weight: 500;
    cursor: pointer;
    transition: all 0.3s ease;
  }
  
  .alibaba-contact-btn {
    background: white;
    color: #fb542f;
  }
  
  .alibaba-contact-btn:hover {
    background: #fb542fe8;
    color: white;
  }
  
  .alibaba-chat-btn {
    background: #fb542fe8;
    color: white;
  }
  
  .alibaba-chat-btn:hover {
    background: white;
    color: #fb542fe8;
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
  
  /* Responsive */
  .mobile-only {
    display: none;
  }
  
  .desktop-only {
    display: block;
  }
  
  @media (max-width: 768px) {
    .mobile-only {
      display: block;
    }
    
    .desktop-only {
      display: none;
    }
    
    .products-grid-dense {
      grid-template-columns: repeat(2, 1fr);
      gap: 12px;
    }
  }
  
  @media (min-width: 1400px) {
    .products-grid-dense {
      grid-template-columns: repeat(6, 1fr);
    }
  }
  </style>
  