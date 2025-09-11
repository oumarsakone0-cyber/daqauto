<template>
    <!-- Version Mobile -->
    <section class="mobile-recommended mobile-only">
      <div class="section-header-mobile">
        <h2>🔥 Recommandés pour vous</h2>
        <button class="see-all-btn">Voir tout</button>
      </div>
      
      <div class="mobile-products-grid">
        <ProductCard
          v-for="(product, index) in recommendedProducts.slice(0, 10)" 
          :key="product.id"
          :product="product"
          :is-mobile="true"
          @product-click="navigateToProduct"
          @favorite-click="toggleFavorite"
          @contact-click="contactSupplier"
          @chat-click="chatWithSupplier"
        />
      </div>
    </section>
  
    <!-- Version Desktop -->
    <section class="more-products-section desktop-only">
      <div class="section-content">
        <div class="section-header">
          <h2 class="section-title">🔥 Produits Recommandés</h2>
          <a href="#" class="view-all">Voir tout →</a>
        </div>
        
        <!-- État de chargement des produits recommandés -->
        <div v-if="isLoadingRecommendedProducts" class="loading-products">
          <div class="loading-product-skeleton" v-for="i in 15" :key="i">
            <div class="skeleton-product-image"></div>
            <div class="skeleton-product-content">
              <div class="skeleton-product-title"></div>
              <div class="skeleton-product-price"></div>
            </div>
          </div>
        </div>
  
        <!-- État d'erreur des produits recommandés -->
        <div v-else-if="recommendedProductsError" class="error-products">
          <p class="error-message">{{ recommendedProductsError }}</p>
          <button @click="loadRecommendedProducts" class="retry-button">Réessayer</button>
        </div>
  
        <!-- Produits recommandés chargés -->
        <div v-else class="products-grid-dense">
          <ProductCard
            v-for="(product, index) in recommendedProducts" 
            :key="product.id || index"
            :product="product"
            :is-mobile="false"
            :card-height="240"
            :image-height="200"
            @product-click="navigateToProduct"
            @favorite-click="toggleFavorite"
            @contact-click="contactSupplier"
            @chat-click="chatWithSupplier"
          />
        </div>
      </div>
    </section>
  </template>
  
  <script setup>
  import { ref, onMounted } from 'vue'
  import { useRouter } from 'vue-router'
  import { productsApi } from '../../services/api.js'
  import ProductCard from './ProductCard.vue'
  
  const router = useRouter()
  
  // États pour les produits recommandés
  const recommendedProducts = ref([])
  const isLoadingRecommendedProducts = ref(true)
  const recommendedProductsError = ref(null)
  
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
  
  // Fonction pour charger les produits recommandés (15 derniers avec plus de vues)
  const loadRecommendedProducts = async () => {
    try {
      isLoadingRecommendedProducts.value = true;
      recommendedProductsError.value = null;
      
      console.log('🔄 Chargement des produits recommandés...');
      const response = await productsApi.getMostViewedProductsForHomepage({ limit: 18 });
      
      if (response.success && response.data) {
        recommendedProducts.value = response.data.map(product => ({
          id: product.id,
          name: product.name,
          slug: product.slug || generateSlug(product.name),
          unit_price: product.unit_price,
          wholesale_price: product.wholesale_price,
          wholesale_min_qty: product.wholesale_min_qty,
          primary_image: product.primary_image,
          views: product.views,
          market: product.boutique_market.toLowerCase(),
          boutique_name: product.boutique_name,
          rating: product.rating || (Math.random() * 2 + 3).toFixed(1),
          experience: product.experience || Math.floor(Math.random() * 8) + 2
        }));
        
        console.log('✅ Produits recommandés chargés:', recommendedProducts.value);
      } else {
        throw new Error(response.message || 'Erreur lors du chargement des produits recommandés');
      }
    } catch (error) {
      console.error('❌ Erreur lors du chargement des produits recommandés:', error);
      recommendedProductsError.value = error.message;
      
      // Fallback avec des produits par défaut
      recommendedProducts.value = [];
    } finally {
      isLoadingRecommendedProducts.value = false;
    }
  };
  
  onMounted(() => {
    loadRecommendedProducts()
  })
  </script>
  
  <style scoped>
  /* Styles mobile */
  .mobile-recommended {
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
  
  .mobile-products-grid {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 12px;
  }
  
  /* Styles desktop */
  .more-products-section {
    padding: 40px 0;
    background: #f8f9fa;
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
  
  /* Grille de produits dense */
  .products-grid-dense {
    display: grid;
    grid-template-columns: repeat(5, 1fr);
    gap: 20px;
  }
  
  /* États de chargement */
  .loading-products {
    display: grid;
    grid-template-columns: repeat(5, 1fr);
    gap: 20px;
  }
  
  .loading-product-skeleton {
    background: #f0f0f0;
    border-radius: 8px;
    overflow: hidden;
    position: relative;
    height: 350px;
  }
  
  .skeleton-product-image {
    width: 100%;
    height: 200px;
    background: linear-gradient(90deg, #f0f0f0 25%, #e0e0e0 50%, #f0f0f0 75%);
    background-size: 200% 100%;
    animation: loading 1.5s infinite;
  }
  
  .skeleton-product-content {
    padding: 16px;
  }
  
  .skeleton-product-title {
    height: 20px;
    background: linear-gradient(90deg, #f0f0f0 25%, #e0e0e0 50%, #f0f0f0 75%);
    background-size: 200% 100%;
    animation: loading 1.5s infinite;
    margin-bottom: 12px;
    border-radius: 4px;
  }
  
  .skeleton-product-price {
    height: 18px;
    width: 60%;
    background: linear-gradient(90deg, #f0f0f0 25%, #e0e0e0 50%, #f0f0f0 75%);
    background-size: 200% 100%;
    animation: loading 1.5s infinite;
    border-radius: 4px;
  }
  
  @keyframes loading {
    0% {
      background-position: 200% 0;
    }
    100% {
      background-position: -200% 0;
    }
  }
  
  .error-products {
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
  
  @media (min-width: 1400px) {
    .products-grid-dense {
      grid-template-columns: repeat(6, 1fr);
    }
  }
  </style>
  