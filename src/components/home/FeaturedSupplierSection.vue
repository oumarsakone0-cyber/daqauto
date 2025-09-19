<template>
    <!-- Version Mobile -->
    <section class="mobile-featured-supplier mobile-only" v-if="featuredSupplier">
      <div class="section-header-mobile">
        <h2>🌟 Fournisseur Vedette</h2>
        <button class="see-all-btn">Voir profil</button>
      </div>
      
      <div class="supplier-card-mobile">
        <div class="supplier-header-mobile">
          <img :src="featuredSupplier.logo" :alt="featuredSupplier.name" class="supplier-logo-mobile" />
          <div class="supplier-info-mobile">
            <h3>{{ featuredSupplier.name }}</h3>
            <div class="supplier-stats-mobile">
              <span>{{ featuredSupplier.experience }} ans</span>
              <span>{{ featuredSupplier.products_count }}+ produits</span>
            </div>
          </div>
          <button class="contact-supplier-mobile">Contacter</button>
        </div>
        
        <div class="supplier-products-mobile">
          <div 
            v-for="(product, index) in featuredSupplier.products.slice(0, 4)" 
            :key="index"
            class="supplier-product-mobile"
            @click="navigateToProduct(product)"
          >
            <img :src="product.primary_image" :alt="product.name" />
            <div class="supplier-product-price-mobile">{{ formatPrice(product.unit_price) }}</div>
          </div>
        </div>
      </div>
    </section>
  
    <!-- Version Desktop -->
    <section class="featured-supplier-section desktop-only">
      <div class="section-content">
        <div class="section-header">
          <h2 class="section-title">🌟 Fournisseur Vedette</h2>
          <a href="#" class="view-all">Voir tous les fournisseurs →</a>
        </div>
        
        <!-- État de chargement du fournisseur -->
        <div v-if="isLoadingFeaturedSupplier" class="loading-supplier">
          <div class="loading-supplier-skeleton">
            <div class="skeleton-media"></div>
            <div class="skeleton-content">
              <div class="skeleton-title"></div>
              <div class="skeleton-products">
                <div class="skeleton-product" v-for="i in 6" :key="i"></div>
              </div>
            </div>
          </div>
        </div>
  
        <!-- État d'erreur du fournisseur -->
        <div v-else-if="featuredSupplierError" class="error-supplier">
          <p class="error-message">{{ featuredSupplierError }}</p>
          <button @click="loadFeaturedSupplier" class="retry-button">Réessayer</button>
        </div>
  
        <!-- Fournisseur chargé -->
        <div v-else-if="featuredSupplier" class="supplier-showcase">
          <div class="supplier-media">
            <div class="media-container">
              <!-- Vidéo ou image de présentation du fournisseur -->
              <div class="supplier-video-container">
                <!-- Afficher la vidéo si elle existe -->
                <video 
                  v-if="featuredSupplier.video_url"
                  class="supplier-video" 
                  autoplay 
                  muted 
                  loop 
                  playsinline
                  :poster="featuredSupplier.banner || getFirstProductImage()"
                >
                  <source :src="featuredSupplier.video_url" type="video/mp4">
                  <!-- Fallback vers l'image si la vidéo ne charge pas -->
                  <img :src="featuredSupplier.banner || getFirstProductImage()" :alt="featuredSupplier.name" class="supplier-banner" />
                </video>
                
                <!-- Afficher l'image si pas de vidéo -->
                <img 
                  v-else
                  :src="featuredSupplier.banner || getFirstProductImage()" 
                  :alt="featuredSupplier.name" 
                  class="supplier-banner" 
                />
                
                <!-- Contrôles vidéo personnalisés (seulement si vidéo) -->
                <div v-if="featuredSupplier.video_url" class="video-controls">
                  <button @click="toggleVideo" class="video-play-btn">
                    <svg v-if="isVideoPaused" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                      <polygon points="5,3 19,12 5,21"/>
                    </svg>
                    <svg v-else width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                      <rect x="6" y="4" width="4" height="16"/>
                      <rect x="14" y="4" width="4" height="16"/>
                    </svg>
                  </button>
                  <button @click="toggleMute" class="video-mute-btn">
                    <svg v-if="isVideoMuted" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                      <polygon points="11,5 6,9 2,9 2,15 6,15 11,19"/>
                      <line x1="23" y1="9" x2="17" y2="15"/>
                      <line x1="17" y1="9" x2="23" y2="15"/>
                    </svg>
                    <svg v-else width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                      <polygon points="11,5 6,9 2,9 2,15 6,15 11,19"/>
                      <path d="M19.07 4.93a10 10 0 0 1 0 14.14M15.54 8.46a5 5 0 0 1 0 7.07"/>
                    </svg>
                  </button>
                </div>
              </div>
              
              <div class="supplier-overlay">
                <div class="supplier-info">
                  <div class="supplier-logo">
                    <img :src="featuredSupplier.logo || 'https://images.unsplash.com/photo-1560472354-b33ff0c44a43?w=60&h=60&fit=crop&auto=format'" :alt="featuredSupplier.name" />
                  </div>
                  <div class="supplier-details">
                    <h3 class="supplier-name">{{ featuredSupplier.name }}</h3>
                    <div class="supplier-category">{{ featuredSupplier.category || 'Boutique générale' }}</div>
                    <div class="supplier-stats">
                      <span>{{ featuredSupplier.experience || 'N/A' }} ans</span>
                      <span>{{ featuredSupplier.products_count || 0 }}+ produits</span>
                    </div>
                  </div>
                </div>
                <div class="supplier-actions">
                  <button class="chat-now-btn">💬 Chat maintenant</button>
                  <button class="contact-supplier-btn">📞 Contacter</button>
                </div>
              </div>
            </div>
          </div>
  
          <div class="supplier-content">
            <h4 class="products-title">Produits populaires</h4>
            <div class="supplier-products">
              <div 
                v-for="(product, index) in featuredSupplier.products" 
                :key="product.id || index" 
                class="supplier-product-card"
                @click="navigateToProduct(product)"
              >
                <div class="supplier-product-image">
                  <img :src="product.primary_image || 'https://via.placeholder.com/120x120?text=Produit'" :alt="product.name" />
                  <div class="product-badge" v-if="product.badge">{{ product.badge }}</div>
                </div>
                <div class="supplier-product-info">
                  <h4 class="supplier-product-name">{{ product.name }}</h4>
                  <div class="supplier-product-price">{{ formatPrice(product.unit_price) }}</div>
                  <div class="supplier-product-moq">MOQ: {{ product.wholesale_min_qty || 10 }} pcs</div>
                  <div class="supplier-product-rating">
                    <span class="rating-stars">⭐⭐⭐⭐⭐</span>
                    <span class="rating-text">({{ product.views || 0 }})</span>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
        
        <div class="sponsored-badge">Sponsorisé</div>
      </div>
    </section>
  </template>
  
  <script setup>
  import { ref, onMounted } from 'vue'
  import { useRouter } from 'vue-router'
  import { productsApi } from '../../services/api.js'
  
  const router = useRouter()
  
  // États pour le fournisseur vedette
  const featuredSupplier = ref(null)
  const isLoadingFeaturedSupplier = ref(true)
  const featuredSupplierError = ref(null)
  
  // États pour la vidéo
  const isVideoPaused = ref(false)
  const isVideoMuted = ref(true)
  
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
  
  // Fonction pour obtenir la première image de produit du fournisseur
  const getFirstProductImage = () => {
    if (featuredSupplier.value?.products?.length > 0) {
      return featuredSupplier.value.products[0].primary_image || 'https://images.unsplash.com/photo-1441986300917-64674bd600d8?w=400&h=250&fit=crop&auto=format'
    }
    return 'https://images.unsplash.com/photo-1441986300917-64674bd600d8?w=400&h=250&fit=crop&auto=format'
  }
  
  // Fonctions de contrôle vidéo
  const toggleVideo = () => {
    const video = document.querySelector('.supplier-video')
    if (video) {
      if (video.paused) {
        video.play()
        isVideoPaused.value = false
      } else {
        video.pause()
        isVideoPaused.value = true
      }
    }
  }
  
  const toggleMute = () => {
    const video = document.querySelector('.supplier-video')
    if (video) {
      video.muted = !video.muted
      isVideoMuted.value = video.muted
    }
  }
  
  // Fonction pour charger un fournisseur vedette aléatoire
  const loadFeaturedSupplier = async () => {
    try {
      isLoadingFeaturedSupplier.value = true;
      featuredSupplierError.value = null;
      
      console.log('🔄 Chargement du fournisseur vedette...');
      const response = await productsApi.getRandomSupplier();
      
      if (response.success && response.data) {
        featuredSupplier.value = {
          id: response.data.boutique.id,
          name: response.data.boutique.name,
          category: response.data.boutique.category || 'Boutique générale',
          experience: response.data.boutique.experience || Math.floor(Math.random() * 10) + 1,
          products_count: response.data.boutique.products_count || 0,
          logo: response.data.boutique.logo,
          banner: response.data.boutique.banner,
          video_url: response.data.boutique.video_url,
          products: (response.data.products || []).map(product => ({
            ...product,
            slug: product.slug || generateSlug(product.name)
          }))
        };
        
        console.log('✅ Fournisseur vedette chargé:', featuredSupplier.value);
      } else {
        throw new Error(response.message || 'Erreur lors du chargement du fournisseur vedette');
      }
    } catch (error) {
      console.error('❌ Erreur lors du chargement du fournisseur vedette:', error);
      featuredSupplierError.value = error.message;
      
      // Fallback avec des données par défaut
      featuredSupplier.value = {
        id: 1,
        name: 'Boutique Exemple',
        category: 'Électronique & Gadgets',
        experience: 5,
        products_count: 150,
        logo: 'https://images.unsplash.com/photo-1560472354-b33ff0c44a43?w=60&h=60&fit=crop&auto=format',
        banner: 'https://images.unsplash.com/photo-1441986300917-64674bd600d8?w=400&h=250&fit=crop&auto=format',
        video_url: null,
        products: []
      };
    } finally {
      isLoadingFeaturedSupplier.value = false;
    }
  };
  
  onMounted(() => {
    loadFeaturedSupplier()
  })
  </script>
  
  <style scoped>
  /* Styles mobile */
  .mobile-featured-supplier {
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
  
  .section-header-mobile h2 {
    font-size: 18px;
    font-weight: 700;
    color: #333;
    margin: 0;
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
  
  .supplier-card-mobile {
    border: 1px solid #f0f0f0;
    border-radius: 12px;
    overflow: hidden;
  }
  
  .supplier-header-mobile {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 16px;
    background: linear-gradient(135deg, #667eea, #764ba2);
    color: white;
  }
  
  .supplier-logo-mobile {
    width: 48px;
    height: 48px;
    border-radius: 50%;
    border: 2px solid white;
  }
  
  .supplier-info-mobile {
    flex: 1;
  }
  
  .supplier-info-mobile h3 {
    font-size: 16px;
    font-weight: 600;
    margin: 0 0 4px 0;
  }
  
  .supplier-stats-mobile {
    display: flex;
    gap: 12px;
    font-size: 12px;
    opacity: 0.9;
  }
  
  .contact-supplier-mobile {
    background: rgba(255, 255, 255, 0.2);
    border: 1px solid rgba(255, 255, 255, 0.3);
    color: white;
    padding: 8px 16px;
    border-radius: 20px;
    font-size: 12px;
    font-weight: 600;
  }
  
  .supplier-products-mobile {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 1px;
    background: #f0f0f0;
  }
  
  .supplier-product-mobile {
    background: white;
    aspect-ratio: 1;
    position: relative;
    overflow: hidden;
    cursor: pointer;
  }
  
  .supplier-product-mobile img {
    width: 100%;
    height: 100%;
    object-fit: cover;
  }
  
  .supplier-product-price-mobile {
    position: absolute;
    bottom: 0;
    left: 0;
    right: 0;
    background: linear-gradient(transparent, rgba(0, 0, 0, 0.7));
    color: white;
    padding: 8px 4px 4px;
    font-size: 10px;
    font-weight: 600;
    text-align: center;
  }
  
  /* Styles desktop */
  .featured-supplier-section {
    padding: 40px 0;
    background: #ffffff;
    position: relative;
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
  
  .section-title {
    font-size: 28px;
    font-weight: 700;
    color: #333;
    margin: 0;
    display: flex;
    align-items: center;
    gap: 8px;
  }
  
  .view-all {
    color: #ff4747;
    text-decoration: none;
    font-weight: 600;
    font-size: 14px;
    transition: all 0.3s ease;
    padding: 8px 16px;
    border-radius: 20px;
    border: 2px solid #ff4747;
  }
  
  .view-all:hover {
    background: #ff4747;
    color: white;
    transform: translateX(4px);
  }
  
  .supplier-showcase {
    display: flex;
    background: #ffffff;
    border-radius: 12px;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
    overflow: hidden;
    position: relative;
  }
  
  .supplier-media {
    width: 350px;
    min-width: 350px;
    height: 350px;
    position: relative;
  }
  
  .media-container {
    width: 100%;
    height: 100%;
    position: relative;
  }
  
  .supplier-video-container {
    width: 100%;
    height: 100%;
    position: relative;
    overflow: hidden;
  }
  
  .supplier-video {
    width: 100%;
    height: 100%;
    object-fit: cover;
  }
  
  .video-controls {
    position: absolute;
    top: 15px;
    right: 15px;
    display: flex;
    gap: 8px;
    z-index: 10;
  }
  
  .video-play-btn,
  .video-mute-btn {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    background: rgba(0, 0, 0, 0.7);
    border: none;
    color: white;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: all 0.3s ease;
  }
  
  .video-play-btn:hover,
  .video-mute-btn:hover {
    background: rgba(0, 0, 0, 0.9);
    transform: scale(1.1);
  }
  
  .supplier-banner {
    width: 100%;
    height: 100%;
    object-fit: cover;
  }
  
  .supplier-overlay {
    position: absolute;
    bottom: 0;
    left: 0;
    right: 0;
    background: linear-gradient(transparent, rgba(0, 0, 0, 0.8));
    padding: 30px 20px 20px;
    color: white;
  }
  
  .supplier-info {
    display: flex;
    align-items: center;
    gap: 15px;
    margin-bottom: 15px;
  }
  
  .supplier-logo {
    width: 50px;
    height: 50px;
    border-radius: 50%;
    overflow: hidden;
    border: 2px solid white;
  }
  
  .supplier-logo img {
    width: 100%;
    height: 100%;
    object-fit: cover;
  }
  
  .supplier-name {
    font-size: 18px;
    font-weight: 700;
    margin: 0 0 4px 0;
  }
  
  .supplier-category {
    font-size: 14px;
    opacity: 0.9;
    margin-bottom: 8px;
  }
  
  .supplier-stats {
    display: flex;
    gap: 15px;
    font-size: 12px;
    opacity: 0.8;
  }
  
  .supplier-actions {
    display: flex;
    gap: 10px;
  }
  
  .chat-now-btn,
  .contact-supplier-btn {
    padding: 8px 16px;
    border: none;
    border-radius: 20px;
    font-size: 12px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s ease;
  }
  
  .chat-now-btn {
    background: #25D366;
    color: white;
  }
  
  .contact-supplier-btn {
    background: white;
    color: #333;
  }
  
  .supplier-content {
    flex: 1;
    padding: 30px;
  }
  
  .products-title {
    font-size: 18px;
    font-weight: 600;
    color: #333;
    margin: 0 0 20px 0;
  }
  
  .supplier-products {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 15px;
  }
  
  .supplier-product-card {
    background: #f8f9fa;
    border-radius: 8px;
    overflow: hidden;
    cursor: pointer;
    transition: all 0.3s ease;
  }
  
  .supplier-product-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
  }
  
  .supplier-product-image {
    width: 100%;
    height: 120px;
    position: relative;
  }
  
  .supplier-product-image img {
    width: 100%;
    height: 100%;
    object-fit: cover;
  }
  
  .product-badge {
    position: absolute;
    top: 8px;
    left: 8px;
    background: #ff4747;
    color: white;
    padding: 2px 8px;
    border-radius: 12px;
    font-size: 10px;
    font-weight: 600;
  }
  
  .supplier-product-info {
    padding: 12px;
  }
  
  .supplier-product-name {
    font-size: 14px;
    font-weight: 600;
    color: #333;
    margin: 0 0 8px 0;
    line-height: 1.3;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
  }
  
  .supplier-product-price {
    font-size: 16px;
    font-weight: 700;
    color: #ff4747;
    margin-bottom: 4px;
  }
  
  .supplier-product-moq {
    font-size: 12px;
    color: #666;
    margin-bottom: 8px;
  }
  
  .supplier-product-rating {
    display: flex;
    align-items: center;
    gap: 4px;
    font-size: 12px;
  }
  
  .rating-stars {
    color: #ffa500;
  }
  
  .rating-text {
    color: #666;
  }
  
  .sponsored-badge {
    position: absolute;
    top: 15px;
    right: 15px;
    background: rgba(255, 255, 255, 0.9);
    color: #666;
    padding: 4px 8px;
    border-radius: 4px;
    font-size: 10px;
    font-weight: 600;
  }
  
  /* États de chargement */
  .loading-supplier {
    display: flex;
  }
  
  .loading-supplier-skeleton {
    display: flex;
    background: white;
    border-radius: 12px;
    overflow: hidden;
    height: 350px;
    width: 100%;
  }
  
  .skeleton-media {
    width: 350px;
    min-width: 350px;
    background: linear-gradient(90deg, #f0f0f0 25%, #e0e0e0 50%, #f0f0f0 75%);
    background-size: 200% 100%;
    animation: loading 1.5s infinite;
  }
  
  .skeleton-content {
    flex: 1;
    padding: 30px;
  }
  
  .skeleton-title {
    height: 24px;
    width: 200px;
    background: linear-gradient(90deg, #f0f0f0 25%, #e0e0e0 50%, #f0f0f0 75%);
    background-size: 200% 100%;
    animation: loading 1.5s infinite;
    border-radius: 4px;
    margin-bottom: 20px;
  }
  
  .skeleton-products {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 15px;
  }
  
  .skeleton-product {
    height: 120px;
    background: linear-gradient(90deg, #f0f0f0 25%, #e0e0e0 50%, #f0f0f0 75%);
    background-size: 200% 100%;
    animation: loading 1.5s infinite;
    border-radius: 8px;
  }
  
  @keyframes loading {
    0% {
      background-position: 200% 0;
    }
    100% {
      background-position: -200% 0;
    }
  }
  
  .error-supplier {
    text-align: center;
    padding: 40px;
    color: #666;
  }
  
  .error-message {
    font-size: 16px;
    margin-bottom: 16px;
  }
  
  .retry-button {
    padding: 10px 20px;
    background: #ff4747;
    color: white;
    border: none;
    border-radius: 6px;
    cursor: pointer;
    font-weight: 600;
    transition: all 0.3s ease;
  }
  
  .retry-button:hover {
    background: #e63946;
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
  }
  </style>
  