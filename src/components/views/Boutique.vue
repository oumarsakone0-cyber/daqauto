<template>
    <div class="boutique-page">
      <!-- Loading state -->
      <div v-if="loading" class="loading-container">
        <div class="loading-spinner"></div>
        <span>Chargement de la boutique...</span>
      </div>
  
      <!-- Error state -->
      <div v-else-if="error" class="error-container">
        <div class="error-content">
          <svg width="64" height="64" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
            <circle cx="12" cy="12" r="10"/>
            <line x1="15" y1="9" x2="9" y2="15"/>
            <line x1="9" y1="9" x2="15" y2="15"/>
          </svg>
          <h2>Boutique introuvable</h2>
          <p>{{ error }}</p>
          <div class="error-actions">
            <button @click="retryLoad" class="retry-btn">
              <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <polyline points="23,4 23,10 17,10"/>
                <polyline points="1,20 1,14 7,14"/>
                <path d="M20.49,9A9,9,0,0,0,5.64,5.64L1,10"/>
                <path d="M3.51,15A9,9,0,0,0,18.36,18.36L23,14"/>
              </svg>
              Réessayer
            </button>
            <router-link to="/" class="back-home-btn">
              <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/>
                <polyline points="9,22 9,12 15,12 15,22"/>
              </svg>
              Retour à l'accueil
            </router-link>
          </div>
        </div>
      </div>
  
      <!-- Main content -->
      <div v-else class="boutique-content">
        <!-- Boutique Header -->
        <div class="boutique-header">
          <div class="container">
            <!-- Breadcrumb -->
            <nav class="breadcrumb">
              <router-link to="/" class="breadcrumb-link">Accueil</router-link>
              <span class="breadcrumb-separator">></span>
              <span class="breadcrumb-current">{{ boutiqueInfo.name }}</span>
            </nav>
  
            <!-- Boutique Info Card -->
            <div class="boutique-info-card">
              <div class="boutique-avatar">
                <img 
                  :src="boutiqueInfo.logo_url || '/placeholder.svg?height=120&width=120'" 
                  :alt="boutiqueInfo.name"
                  @error="handleImageError"
                />
              </div>
              
              <div class="boutique-details">
                <div class="boutique-main-info">
                  <h1 class="boutique-name">{{ boutiqueInfo.name }}</h1>
                  <div class="boutique-badges">
                    <span v-if="boutiqueInfo.is_verified" class="verified-badge">
                      <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                      </svg>
                      Boutique vérifiée
                    </span>
                    <span v-if="boutiqueInfo.subscription_plan === 'premium'" class="premium-badge">
                      <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <polygon points="12,2 15.09,8.26 22,9.27 17,14.14 18.18,21.02 12,17.77 5.82,21.02 7,14.14 2,9.27 8.91,8.26"/>
                      </svg>
                      Premium
                    </span>
                  </div>
                </div>
  
                <div class="boutique-stats">
                  <div class="stat-item">
                    <div class="stat-value">{{ boutiqueInfo.rating || '4.5' }}</div>
                    <div class="stat-label">Note moyenne</div>
                    <div class="star-rating">
                      <svg v-for="i in 5" :key="i" :class="['star', i <= Math.floor(boutiqueInfo.rating || 4.5) ? 'filled' : '']" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <polygon points="12,2 15.09,8.26 22,9.27 17,14.14 18.18,21.02 12,17.77 5.82,21.02 7,14.14 2,9.27 8.91,8.26"/>
                      </svg>
                    </div>
                  </div>
                  
                  <div class="stat-item">
                    <div class="stat-value">{{ formatNumber(boutiqueInfo.total_products || 0) }}</div>
                    <div class="stat-label">Produits</div>
                  </div>
                  
                  <div class="stat-item">
                    <div class="stat-value">{{ formatNumber(boutiqueInfo.total_reviews || 0) }}</div>
                    <div class="stat-label">Avis clients</div>
                  </div>
                  
                  <div class="stat-item">
                    <div class="stat-value">{{ boutiqueInfo.years_experience || '2' }}</div>
                    <div class="stat-label">Années d'expérience</div>
                  </div>
                </div>
  
                <div class="boutique-description" v-if="boutiqueInfo.description">
                  <p>{{ boutiqueInfo.description }}</p>
                </div>
  
                <div class="boutique-location" v-if="boutiqueInfo.market || boutiqueInfo.address">
                  <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/>
                    <circle cx="12" cy="10" r="3"/>
                  </svg>
                  <span>{{ boutiqueInfo.market || boutiqueInfo.address || 'Abidjan, Côte d\'Ivoire' }}</span>
                </div>
              </div>
  
              <div class="boutique-actions">
                <button class="contact-btn" @click="contactBoutique">
                  <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/>
                    <polyline points="22,6 12,13 2,6"/>
                  </svg>
                  Contacter
                </button>
                
                <button class="follow-btn" @click="toggleFollow" :class="{ following: isFollowing }">
                  <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path v-if="!isFollowing" d="M19 14c1.49-1.46 3-3.21 3-5.5A5.5 5.5 0 0 0 16.5 3c-1.76 0-3 .5-4.5 2-1.5-1.5-2.74-2-4.5-2A5.5 5.5 0 0 0 2 8.5c0 2.29 1.51 4.04 3 5.5l7 7z"/>
                    <path v-else d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"/>
                  </svg>
                  {{ isFollowing ? 'Suivi' : 'Suivre' }}
                </button>
              </div>
            </div>
          </div>
        </div>
  
        <!-- Products Section -->
        <div class="products-section">
          <div class="container">
            <!-- Filters and Sort -->
            <div class="products-toolbar">
              <div class="toolbar-left">
                <h2 class="section-title">Produits de la boutique</h2>
                <span class="products-count">{{ totalProducts }} produit{{ totalProducts > 1 ? 's' : '' }}</span>
              </div>
              
              <div class="toolbar-right">
                <!-- Search in boutique -->
                <div class="boutique-search">
                  <input 
                    type="text" 
                    placeholder="Rechercher dans cette boutique..."
                    v-model="searchQuery"
                    @input="handleSearch"
                    @keydown.enter="performSearch"
                    class="search-input"
                  />
                  <button @click="performSearch" class="search-btn">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                      <circle cx="11" cy="11" r="8"/>
                      <path d="m21 21-4.35-4.35"/>
                    </svg>
                  </button>
                </div>
  
                <!-- Sort dropdown -->
                <select v-model="sortBy" @change="handleSortChange" class="sort-select">
                  <option value="created_at">Plus récents</option>
                  <option value="unit_price">Prix croissant</option>
                  <option value="unit_price_desc">Prix décroissant</option>
                  <option value="views_count">Plus populaires</option>
                  <option value="name">Nom A-Z</option>
                </select>
  
                <!-- View toggle -->
                <div class="view-toggle">
                  <button 
                    class="view-btn" 
                    :class="{ active: viewMode === 'grid' }"
                    @click="viewMode = 'grid'"
                  >
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                      <rect x="3" y="3" width="7" height="7"/>
                      <rect x="14" y="3" width="7" height="7"/>
                      <rect x="14" y="14" width="7" height="7"/>
                      <rect x="3" y="14" width="7" height="7"/>
                    </svg>
                  </button>
                  <button 
                    class="view-btn" 
                    :class="{ active: viewMode === 'list' }"
                    @click="viewMode = 'list'"
                  >
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                      <line x1="8" y1="6" x2="21" y2="6"/>
                      <line x1="8" y1="12" x2="21" y2="12"/>
                      <line x1="8" y1="18" x2="21" y2="18"/>
                      <line x1="3" y1="6" x2="3.01" y2="6"/>
                      <line x1="3" y1="12" x2="3.01" y2="12"/>
                      <line x1="3" y1="18" x2="3.01" y2="18"/>
                    </svg>
                  </button>
                </div>
              </div>
            </div>
  
            <!-- Loading products -->
            <div v-if="loadingProducts" class="products-loading">
              <div class="loading-spinner"></div>
              <span>Chargement des produits...</span>
            </div>
  
            <!-- No products -->
            <div v-else-if="products.length === 0" class="no-products">
              <svg width="64" height="64" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                <rect x="3" y="3" width="18" height="18" rx="2" ry="2"/>
                <circle cx="8.5" cy="8.5" r="1.5"/>
                <polyline points="21,15 16,10 5,21"/>
              </svg>
              <h3>Aucun produit trouvé</h3>
              <p v-if="searchQuery">Aucun produit ne correspond à votre recherche "{{ searchQuery }}"</p>
              <p v-else>Cette boutique n'a pas encore de produits.</p>
              <button v-if="searchQuery" @click="clearSearch" class="clear-search-btn">
                Voir tous les produits
              </button>
            </div>
  
            <!-- Products Grid/List -->
            <div v-else class="products-container" :class="{ 'list-view': viewMode === 'list' }">
              <div 
                v-for="(product, index) in products" 
                :key="product.id" 
                class="alibaba-product-card"
                :class="{ 'list-card': viewMode === 'list' }"
                @click="goToProduct(product)"
              >
                <!-- Image principale avec slider -->
                <div class="alibaba-image-area">
                  <div class="alibaba-slider">
                    <div class="alibaba-slider-wrapper">
                      <a href="#" class="alibaba-slider-link" @click.prevent>
                        <img 
                          :src="product.images[product.currentImageIndex || 0] || '/placeholder.svg?height=280&width=280'" 
                          :alt="product.name" 
                          class="alibaba-slider-img" 
                          loading="lazy"
                          @error="handleImageError"
                        >
                      </a>
                      <div class="alibaba-arrow-left" @click.stop="previousImage(index)" v-if="product.images.length > 1">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                          <polyline points="15,18 9,12 15,6"/>
                        </svg>
                      </div>
                      <div class="alibaba-arrow-right" @click.stop="nextImage(index)" v-if="product.images.length > 1">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                          <polyline points="9,18 15,12 9,6"/>
                        </svg>
                      </div>
                      <div class="alibaba-slider-indicators" v-if="product.images.length > 1">
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
                  
                  <!-- Boutons favoris et comparaison -->
                  <div class="alibaba-favorite-btn" @click.stop="toggleFavorite(product)">
                    <svg width="16" height="16" viewBox="0 0 24 24" :fill="product.isFavorite ? 'red' : 'none'" stroke="currentColor" stroke-width="2">
                      <path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"/>
                    </svg>
                  </div>
                </div>
  
                <!-- Contenu de la carte -->
                <div class="alibaba-card-content">
                  <!-- Titre du produit -->
                  <h2 class="alibaba-title">
                    <a href="#" target="_blank" @click.prevent>
                      <span>{{ product.name }}</span>
                    </a>
                  </h2>
  
                  <!-- Système de prix -->
                  <div class="product-pricing">
                    <div class="unit-price">
                      <span class="current-price">{{ formatFCFA(product.unit_price) }}</span>
                    </div>
                    <div class="wholesale-price" v-if="product.wholesale_price && product.wholesale_price < product.unit_price">
                      <span class="price-label">Prix de gros:</span>
                      <span class="wholesale-amount">{{ formatFCFA(product.wholesale_price) }}</span>
                      <span class="min-quantity">≥ {{ product.wholesale_min_qty || 1 }} pcs</span>
                    </div>
                  </div>
                  
                  <div class="shipping-info" v-if="product.free_shipping">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                      <rect x="1" y="3" width="15" height="13"/>
                      <polygon points="16 8 20 8 23 11 23 16 16 16 16 8"/>
                      <circle cx="5.5" cy="18.5" r="2.5"/>
                      <circle cx="18.5" cy="18.5" r="2.5"/>
                    </svg>
                    <span>Livraison gratuite</span>
                  </div>
  
                  <!-- Informations de vente -->
                  <div class="alibaba-sale-features">
                    <div class="alibaba-sale-item">Min. commande: {{ product.wholesale_min_qty || 1 }} pièces</div>
                    <div class="alibaba-sale-item" v-if="product.free_shipping">Livraison gratuite</div>
                    <div class="alibaba-sale-item">Stock: {{ product.stock }}</div>
                  </div>
  
                  <!-- Étoiles et évaluations -->
                  <div class="alibaba-supplier-info">
                    <div class="alibaba-review">
                      <div class="stars">
                        <svg v-for="i in 5" :key="i" :class="['star', i <= Math.floor(product.rating || 4.2) ? 'filled' : '']" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                          <polygon points="12,2 15.09,8.26 22,9.27 17,14.14 18.18,21.02 12,17.77 5.82,21.02 7,14.14 2,9.27 8.91,8.26"/>
                        </svg>
                      </div>
                      <span>{{ product.rating || '4.2' }}/5.0 ({{ product.reviews || Math.floor(Math.random() * 500) + 50 }} avis)</span>
                    </div>
                  </div>
  
                  <!-- Produits similaires en miniature -->
                  <div class="alibaba-similar-products" v-if="product.images.length > 1">
                    <div class="alibaba-mini-product" v-for="(img, imgIndex) in product.images.slice(0, 3)" :key="imgIndex">
                      <img :src="img" alt="Produit similaire" loading="lazy" @error="handleImageError">
                    </div>
                  </div>
  
                  <!-- Boutons d'action -->
                  <div class="alibaba-action-buttons">
                    <button class="alibaba-contact-btn" @click.stop="contactSupplier(product)">Contacter</button>
                    <button class="alibaba-chat-btn" @click.stop="addToCart(product)">Ajouter au panier</button>
                  </div>
                </div>
              </div>
            </div>
  
            <!-- Pagination -->
            <div v-if="totalPages > 1" class="pagination">
              <button class="page-btn prev-btn" :disabled="currentPage === 1" @click="goToPage(currentPage - 1)">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                  <polyline points="15,18 9,12 15,6"/>
                </svg>
              </button>
              
              <button 
                v-for="page in visiblePages" 
                :key="page"
                class="page-btn"
                :class="{ active: page === currentPage }"
                @click="goToPage(page)"
                v-if="page !== '...'"
              >
                {{ page }}
              </button>
              <span v-else class="page-ellipsis">...</span>
              
              <button class="page-btn next-btn" :disabled="currentPage === totalPages" @click="goToPage(currentPage + 1)">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                  <polyline points="9,18 15,12 9,6"/>
                </svg>
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>
  </template>
  
  <script>
  import { ref, computed, onMounted, watch } from 'vue'
  import { useRoute, useRouter } from 'vue-router'
  import { boutiquesApi } from '../../services/api.js'
  
  export default {
    name: 'BoutiqueView',
    setup() {
      const route = useRoute()
      const router = useRouter()
      
      // États principaux
      const loading = ref(true)
      const error = ref(null)
      const boutiqueInfo = ref({})
      const products = ref([])
      const totalProducts = ref(0)
      const totalPages = ref(0)
      const currentPage = ref(1)
      const itemsPerPage = ref(20)
      
      // États des produits
      const loadingProducts = ref(false)
      const searchQuery = ref('')
      const sortBy = ref('created_at')
      const viewMode = ref('grid')
      
      // États d'interaction
      const isFollowing = ref(false)
  
      // ID de la boutique depuis l'URL
      const boutiqueId = computed(() => route.params.id)
  
      // Computed pour les pages visibles
      const visiblePages = computed(() => {
        const pages = []
        const total = totalPages.value
        const current = currentPage.value
        
        if (total <= 7) {
          for (let i = 1; i <= total; i++) {
            pages.push(i)
          }
        } else {
          if (current <= 4) {
            for (let i = 1; i <= 5; i++) pages.push(i)
            pages.push('...')
            pages.push(total)
          } else if (current >= total - 3) {
            pages.push(1)
            pages.push('...')
            for (let i = total - 4; i <= total; i++) pages.push(i)
          } else {
            pages.push(1)
            pages.push('...')
            for (let i = current - 1; i <= current + 1; i++) pages.push(i)
            pages.push('...')
            pages.push(total)
          }
        }
        
        return pages
      })
  
      // Fonction pour charger les informations de la boutique
      const loadBoutiqueInfo = async () => {
        try {
          loading.value = true
          error.value = null
          
          if (!boutiqueId.value) {
           // router.push('/')
            return
          }
  
          
          const response = await boutiquesApi.getBoutiqueInfo(boutiqueId.value)
          
          if (response.success) {
            boutiqueInfo.value = response.data
            isFollowing.value = response.data.is_following || false
          } else {
            throw new Error(response.error || 'Boutique introuvable')
          }
        } catch (err) {
          console.error('❌ Erreur lors du chargement de la boutique:', err)
          error.value = err.message || 'Erreur lors du chargement de la boutique'
        } finally {
          loading.value = false
        }
      }
  
      // Fonction pour charger les produits de la boutique
      const loadProducts = async () => {
        try {
          loadingProducts.value = true
          
          const params = {
            boutique_id: boutiqueId.value,
            search: searchQuery.value,
            page: currentPage.value,
            limit: itemsPerPage.value,
            sort: sortBy.value.includes('_desc') ? sortBy.value.replace('_desc', '') : sortBy.value,
            order: sortBy.value.includes('_desc') ? 'DESC' : 'ASC'
          }
  
          
          const response = await boutiquesApi.getBoutiqueProducts(params)
          
          if (response.success) {
            // Traiter les données reçues de l'API
            let productsData = response.data
            
            // Si les produits sont dans response.data.products
            if (response.data && Array.isArray(response.data.products)) {
              productsData = response.data.products
            } else if (Array.isArray(response.data)) {
              productsData = response.data
            } else {
              productsData = []
            }
  
            // Formater les produits pour l'affichage
            products.value = productsData.map(product => ({
              id: product.id,
              name: product.name,
              slug: product.slug,
              unit_price: parseFloat(product.unit_price) || 0,
              wholesale_price: product.wholesale_price ? parseFloat(product.wholesale_price) : null,
              wholesale_min_qty: product.wholesale_min_qty || 1,
              stock: parseInt(product.stock) || 0,
              status: product.status,
              description: product.description,
              category_name: product.category_name,
              subcategory_name: product.subcategory_name,
              views_count: parseInt(product.views_count) || 0,
              sales_count: parseInt(product.sales_count) || 0,
              rating: parseFloat(product.rating) || (4.0 + Math.random() * 1),
              reviews: product.reviews || Math.floor(Math.random() * 500) + 50,
              free_shipping: product.free_shipping || Math.random() > 0.5,
              images: formatProductImages(product),
              currentImageIndex: 0,
              isFavorite: false
            }))
            
            // Pagination
            if (response.pagination) {
              totalProducts.value = response.pagination.total || 0
              totalPages.value = response.pagination.total_pages || 0
            } else {
              totalProducts.value = products.value.length
              totalPages.value = Math.ceil(totalProducts.value / itemsPerPage.value)
            }
            
          } else {
            throw new Error(response.error || 'Erreur lors du chargement des produits')
          }
        } catch (err) {
          console.error('❌ Erreur lors du chargement des produits:', err)
          products.value = []
          totalProducts.value = 0
          totalPages.value = 0
        } finally {
          loadingProducts.value = false
        }
      }
  
      // Fonction pour formater les images des produits
      const formatProductImages = (product) => {
        let images = []
        
        // Si le produit a un tableau d'images
        if (product.images && Array.isArray(product.images)) {
          images = product.images
        }
        // Si le produit a une image principale
        else if (product.primary_image) {
          images = [product.primary_image]
        }
        // Si le produit a une image_url
        else if (product.image_url) {
          images = [product.image_url]
        }
        
        // Si pas d'images, utiliser un placeholder
        if (images.length === 0) {
          images = ['/placeholder.svg?height=280&width=280']
        }
        
        return images
      }
      
      const toggleFollow = async () => {
        try {
          const action = isFollowing.value ? 'unfollowBoutique' : 'followBoutique'
          const response = await boutiquesApi[action](boutiqueId.value)
          
          if (response.success) {
            isFollowing.value = !isFollowing.value
          } else {
            throw new Error(response.error)
          }
        } catch (err) {
          console.error(`❌ Erreur lors du ${isFollowing.value ? 'désabonnement' : 'suivi'} de la boutique:`, err)
          // Afficher une notification d'erreur ici si nécessaire
        }
      }
  
      const contactBoutique = async () => {
        try {
          // Ici vous pourriez ouvrir une modal pour saisir le message
          // Pour l'exemple, nous utilisons un message prédéfini
          const messageData = {
            subject: "Demande d'information",
            message: "Bonjour, je souhaiterais avoir plus d'informations sur vos produits."
          }
          
          const response = await boutiquesApi.contactBoutique(boutiqueId.value, messageData)
          
          if (response.success) {
            alert('Message envoyé avec succès!')
          } else {
            throw new Error(response.error)
          }
        } catch (err) {
          console.error('❌ Erreur lors de l\'envoi du message:', err)
          alert('Erreur lors de l\'envoi du message')
        }
      }
  
      // Gestion des images
      const handleImageError = (event) => {
        event.target.src = 'https://t3.ftcdn.net/jpg/00/38/91/48/360_F_38914811_jQRpNpxUHrAlJs6lVzKYZPQAPE0A3CKc.jpg'
      }
  
      const nextImage = (productIndex) => {
        const product = products.value[productIndex]
        if (product.currentImageIndex < product.images.length - 1) {
          product.currentImageIndex++
        } else {
          product.currentImageIndex = 0
        }
      }
  
      const previousImage = (productIndex) => {
        const product = products.value[productIndex]
        if (product.currentImageIndex > 0) {
          product.currentImageIndex--
        } else {
          product.currentImageIndex = product.images.length - 1
        }
      }
  
      const setImage = (productIndex, imageIndex) => {
        products.value[productIndex].currentImageIndex = imageIndex
      }
  
      // Formatage des données
      const formatFCFA = (montant) => {
        return Number(montant).toLocaleString('fr-FR', { minimumFractionDigits: 0 }) + ' FCFA'
      }
  
      const formatNumber = (number) => {
        return Number(number).toLocaleString('fr-FR')
      }
  
      // Actions
      const retryLoad = () => {
        loadBoutiqueInfo()
      }
  
      const handleSearch = () => {
        // Debounce la recherche
        clearTimeout(window.searchTimeout)
        window.searchTimeout = setTimeout(() => {
          currentPage.value = 1
          loadProducts()
        }, 300)
      }
  
      const performSearch = () => {
        currentPage.value = 1
        loadProducts()
      }
  
      const clearSearch = () => {
        searchQuery.value = ''
        currentPage.value = 1
        loadProducts()
      }
  
      const handleSortChange = () => {
        currentPage.value = 1
        loadProducts()
      }
  
      const goToPage = (page) => {
        if (page >= 1 && page <= totalPages.value) {
          currentPage.value = page
          loadProducts()
          window.scrollTo({ top: 0, behavior: 'smooth' })
        }
      }
  
      const goToProduct = (product) => {
        const slug = product.slug || `product-${product.id}`
        router.push(`/detail_resultat_produit/${slug}`)
      }
  
      const toggleFavorite = (product) => {
        product.isFavorite = !product.isFavorite
      }
  
      const addToCart = (product) => {
        // Implémenter la logique d'ajout au panier
      }
  
      const contactSupplier = (product) => {
        // Implémenter la logique de contact fournisseur
      }
  
      // Watchers
      watch(() => route.params.id, (newId) => {
        if (newId) {
          loadBoutiqueInfo().then(() => {
            loadProducts()
          })
        } else {
         // router.push('/')
        }
      })
  
      // Lifecycle
      onMounted(() => {
        if (boutiqueId.value) {
          loadBoutiqueInfo().then(() => {
            loadProducts()
          })
        } else {
         // router.push('/')
        }
      })
  
      return {
        // États
        loading,
        error,
        boutiqueInfo,
        products,
        totalProducts,
        totalPages,
        currentPage,
        loadingProducts,
        searchQuery,
        sortBy,
        viewMode,
        isFollowing,
        
        // Computed
        visiblePages,
        
        // Méthodes
        retryLoad,
        contactBoutique,
        toggleFollow,
        handleSearch,
        performSearch,
        clearSearch,
        handleSortChange,
        goToPage,
        goToProduct,
        toggleFavorite,
        addToCart,
        contactSupplier,
        handleImageError,
        nextImage,
        previousImage,
        setImage,
        formatFCFA,
        formatNumber
      }
    }
  }
  </script>
  
  <style scoped>
  .boutique-page {
    background: #f5f5f5;
    min-height: 100vh;
  }
  
  .container {
    max-width: 1500px;
    margin: 0 auto;
    padding: 0 16px;
  }
  
  /* Loading and Error States */
  .loading-container {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    min-height: 60vh;
    color: #666;
  }
  
  .loading-spinner {
    width: 40px;
    height: 40px;
    border: 3px solid #f3f3f3;
    border-top: 3px solid #1890ff;
    border-radius: 50%;
    animation: spin 1s linear infinite;
    margin-bottom: 16px;
  }
  
  @keyframes spin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
  }
  
  .error-container {
    display: flex;
    align-items: center;
    justify-content: center;
    min-height: 60vh;
    padding: 20px;
  }
  
  .error-content {
    text-align: center;
    max-width: 400px;
  }
  
  .error-content svg {
    color: #ff4d4f;
    margin-bottom: 16px;
  }
  
  .error-content h2 {
    color: #333;
    margin-bottom: 8px;
  }
  
  .error-content p {
    color: #666;
    margin-bottom: 24px;
  }
  
  .error-actions {
    display: flex;
    gap: 12px;
    justify-content: center;
  }
  
  .retry-btn, .back-home-btn {
    display: flex;
    align-items: center;
    gap: 8px;
    padding: 8px 16px;
    border: 1px solid #d9d9d9;
    border-radius: 6px;
    background: white;
    color: #333;
    text-decoration: none;
    transition: all 0.3s ease;
  }
  
  .retry-btn:hover, .back-home-btn:hover {
    border-color: #1890ff;
    color: #1890ff;
  }
  
  /* Boutique Header */
  .boutique-header {
    background: white;
    border-bottom: 1px solid #f0f0f0;
    padding: 20px 0;
  }
  
  .breadcrumb {
    margin-bottom: 20px;
    font-size: 14px;
    color: #666;
  }
  
  .breadcrumb-link {
    color: #1890ff;
    text-decoration: none;
  }
  
  .breadcrumb-link:hover {
    text-decoration: underline;
  }
  
  .breadcrumb-separator {
    margin: 0 8px;
    color: #ccc;
  }
  
  .breadcrumb-current {
    color: #333;
  }
  
  .boutique-info-card {
    display: flex;
    gap: 24px;
    background: white;
    border-radius: 12px;
    padding: 24px;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
  }
  
  .boutique-avatar {
    flex-shrink: 0;
  }
  
  .boutique-avatar img {
    width: 120px;
    height: 120px;
    border-radius: 12px;
    object-fit: cover;
    border: 1px solid #f0f0f0;
  }
  
  .boutique-details {
    flex: 1;
  }
  
  .boutique-main-info {
    margin-bottom: 16px;
  }
  
  .boutique-name {
    font-size: 28px;
    font-weight: 700;
    color: #333;
    margin: 0 0 8px 0;
  }
  
  .boutique-badges {
    display: flex;
    gap: 8px;
  }
  
  .verified-badge, .premium-badge {
    display: flex;
    align-items: center;
    gap: 4px;
    padding: 4px 8px;
    border-radius: 16px;
    font-size: 12px;
    font-weight: 500;
  }
  
  .verified-badge {
    background: #f6ffed;
    color: #52c41a;
    border: 1px solid #d9f7be;
  }
  
  .premium-badge {
    background: #fff7e6;
    color: #fa8c16;
    border: 1px solid #ffd591;
  }
  
  .boutique-stats {
    display: flex;
    gap: 32px;
    margin-bottom: 16px;
  }
  
  .stat-item {
    text-align: center;
  }
  
  .stat-value {
    font-size: 24px;
    font-weight: 700;
    color: #333;
    margin-bottom: 4px;
  }
  
  .stat-label {
    font-size: 14px;
    color: #666;
    margin-bottom: 4px;
  }
  
  .star-rating {
    display: flex;
    gap: 2px;
    justify-content: center;
  }
  
  .star {
    color: #d9d9d9;
  }
  
  .star.filled {
    color: #fadb14;
    fill: currentColor;
  }
  
  .boutique-description {
    margin-bottom: 16px;
  }
  
  .boutique-description p {
    color: #666;
    line-height: 1.6;
    margin: 0;
  }
  
  .boutique-location {
    display: flex;
    align-items: center;
    gap: 8px;
    color: #666;
    font-size: 14px;
  }
  
  .boutique-actions {
    display: flex;
    flex-direction: column;
    gap: 12px;
    flex-shrink: 0;
  }
  
  .contact-btn, .follow-btn {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    padding: 12px 24px;
    border-radius: 8px;
    font-weight: 500;
    transition: all 0.3s ease;
    min-width: 140px;
  }
  
  .contact-btn {
    background: #1890ff;
    color: white;
    border: 1px solid #1890ff;
  }
  
  .contact-btn:hover {
    background: #40a9ff;
    border-color: #40a9ff;
  }
  
  .follow-btn {
    background: white;
    color: #333;
    border: 1px solid #d9d9d9;
  }
  
  .follow-btn:hover {
    border-color: #1890ff;
    color: #1890ff;
  }
  
  .follow-btn.following {
    background: #f6ffed;
    border-color: #52c41a;
    color: #52c41a;
  }
  
  /* Products Section */
  .products-section {
    padding: 24px 0;
  }
  
  .products-toolbar {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 24px;
    padding: 16px 24px;
    background: white;
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
  }
  
  .toolbar-left {
    display: flex;
    align-items: baseline;
    gap: 16px;
  }
  
  .section-title {
    font-size: 24px;
    font-weight: 600;
    color: #333;
    margin: 0;
  }
  
  .products-count {
    font-size: 14px;
    color: #666;
  }
  
  .toolbar-right {
    display: flex;
    align-items: center;
    gap: 16px;
  }
  
  .boutique-search {
    display: flex;
    position: relative;
  }
  
  .search-input {
    padding: 8px 12px 8px 16px;
    border: 1px solid #d9d9d9;
    border-radius: 6px 0 0 6px;
    font-size: 14px;
    outline: none;
    width: 200px;
  }
  
  .search-input:focus {
    border-color: #1890ff;
  }
  
  .search-btn {
    padding: 8px 12px;
    background: #1890ff;
    color: white;
    border: 1px solid #1890ff;
    border-radius: 0 6px 6px 0;
    cursor: pointer;
    transition: background 0.3s ease;
  }
  
  .search-btn:hover {
    background: #40a9ff;
  }
  
  .sort-select {
    padding: 8px 12px;
    border: 1px solid #d9d9d9;
    border-radius: 6px;
    font-size: 14px;
    background: white;
    min-width: 150px;
  }
  
  .view-toggle {
    display: flex;
    border: 1px solid #d9d9d9;
    border-radius: 6px;
    overflow: hidden;
  }
  
  .view-btn {
    padding: 8px 12px;
    border: none;
    background: white;
    cursor: pointer;
    color: #666;
    transition: all 0.3s ease;
  }
  
  .view-btn:hover {
    background: #f5f5f5;
  }
  
  .view-btn.active {
    background: #1890ff;
    color: white;
  }
  
  .products-loading {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    padding: 60px 20px;
    color: #666;
  }
  
  .no-products {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    padding: 60px 20px;
    text-align: center;
    color: #666;
  }
  
  .no-products svg {
    margin-bottom: 16px;
    opacity: 0.5;
  }
  
  .no-products h3 {
    margin-bottom: 8px;
    color: #333;
  }
  
  .clear-search-btn {
    margin-top: 16px;
    padding: 8px 16px;
    background: #1890ff;
    color: white;
    border: none;
    border-radius: 6px;
    cursor: pointer;
    transition: background 0.3s ease;
  }
  
  .clear-search-btn:hover {
    background: #40a9ff;
  }
  
  /* Products Grid */
  .products-container {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
    gap: 16px;
    margin-bottom: 32px;
  }
  
  .products-container.list-view {
    grid-template-columns: 1fr;
  }
  
  /* Styles des cartes produits - repris de la page résultats */
  .alibaba-product-card {
    background: #ffffff;
    border-radius: 12px;
    overflow: hidden;
    transition: all 0.3s ease;
    cursor: pointer;
    position: relative;
    height: 100%;
    display: flex;
    flex-direction: column;
    padding: 12px;
  }
  
  .alibaba-product-card:hover {
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
  }
  
  .alibaba-product-card.list-card {
    flex-direction: row;
    padding: 16px;
  }
  
  .alibaba-product-card.list-card .alibaba-image-area {
    width: 200px;
    height: 200px;
    margin-right: 16px;
    flex-shrink: 0;
  }
  
  .alibaba-product-card.list-card .alibaba-card-content {
    flex: 1;
  }
  
  .alibaba-image-area {
    position: relative;
    margin-bottom: 8px;
  }
  
  .alibaba-slider {
    position: relative;
  }
  
  .alibaba-slider-wrapper {
    position: relative;
  }
  
  .alibaba-slider-img {
    width: 100%;
    height: 280px;
    object-fit: cover;
    border-radius: 12px;
  }
  
  .alibaba-arrow-left,
  .alibaba-arrow-right {
    position: absolute;
    top: 50%;
    transform: translateY(-50%);
    width: 32px;
    height: 32px;
    background: rgba(0, 0, 0, 0.5);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    opacity: 0;
    transition: opacity 0.3s ease;
    z-index: 2;
    color: white;
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
    z-index: 2;
  }
  
  .alibaba-indicator {
    width: 6px;
    height: 6px;
    border-radius: 50%;
    background: rgba(255, 255, 255, 0.5);
    cursor: pointer;
  }
  
  .alibaba-indicator.active {
    background: #ffffff;
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
    opacity: 0;
    transition: opacity 0.3s ease;
    z-index: 3;
  }
  
  .alibaba-product-card:hover .alibaba-favorite-btn {
    opacity: 1;
  }
  
  .alibaba-card-content {
    flex: 1;
    display: flex;
    flex-direction: column;
  }
  
  .alibaba-title {
    margin: 0 0 8px 0;
    font-size: 15px;
    font-weight: 400;
    color: #222;
    line-height: 1.4;
    height: 40px;
    overflow: hidden;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
  }
  
  .product-pricing {
    margin-bottom: 12px;
  }
  
  .current-price {
    font-size: 22px;
    font-weight: 700;
    color: black;
  }
  
  .wholesale-price {
    font-size: 14px;
    color: #555;
    margin-top: 4px;
  }
  
  .price-label {
    font-weight: 600;
    margin-right: 4px;
  }
  
  .wholesale-amount {
    font-weight: 600;
    color: #555;
    font-size: 16px;
  }
  
  .min-quantity {
    font-size: 12px;
    color: #888;
    margin-left: 4px;
  }
  
  .shipping-info {
    display: flex;
    align-items: center;
    gap: 6px;
    font-size: 13px;
    color: #4CAF50;
    margin-bottom: 8px;
  }
  
  .alibaba-sale-features {
    display: flex;
    flex-wrap: wrap;
    gap: 8px;
    margin-bottom: 8px;
  }
  
  .alibaba-sale-item {
    font-size: 12px;
    color: #666;
    background: #f5f5f5;
    padding: 2px 6px;
    border-radius: 3px;
  }
  
  .alibaba-supplier-info {
    margin-bottom: 12px;
  }
  
  .alibaba-review {
    display: flex;
    align-items: center;
    gap: 8px;
    font-size: 12px;
    color: #666;
  }
  
  .stars {
    display: flex;
    gap: 2px;
  }
  
  .star {
    color: #d9d9d9;
  }
  
  .star.filled {
    color: #fadb14;
    fill: currentColor;
  }
  
  .alibaba-similar-products {
    display: flex;
    gap: 8px;
    margin-bottom: 12px;
    height: 60px;
  }
  
  .alibaba-mini-product {
    width: 60px;
    height: 60px;
    border-radius: 8px;
    overflow: hidden;
    border: 1px solid #e8e8e8;
  }
  
  .alibaba-mini-product img {
    width: 100%;
    height: 100%;
    object-fit: cover;
  }
  
  .alibaba-action-buttons {
    display: flex;
    gap: 8px;
    margin-top: auto;
  }
  
  .alibaba-contact-btn,
  .alibaba-chat-btn {
    flex: 1;
    padding: 8px 12px;
    border: 1px solid #d9d9d9;
    border-radius: 4px;
    background: #ffffff;
    color: #333;
    font-size: 13px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s ease;
  }
  
  .alibaba-contact-btn:hover {
    border-color: #1890ff;
    color: #1890ff;
  }
  
  .alibaba-chat-btn {
    background: #1890ff;
    color: white;
    border-color: #1890ff;
  }
  
  .alibaba-chat-btn:hover {
    background: #40a9ff;
  }
  
  /* Pagination */
  .pagination {
    display: flex;
    justify-content: center;
    align-items: center;
    gap: 8px;
    margin-top: 32px;
  }
  
  .page-btn {
    padding: 8px 12px;
    border: 1px solid #d9d9d9;
    background: #fff;
    color: #333;
    border-radius: 4px;
    cursor: pointer;
    transition: all 0.3s ease;
  }
  
  .page-btn:hover:not(:disabled) {
    border-color: #1890ff;
    color: #1890ff;
  }
  
  .page-btn.active {
    background: #1890ff;
    color: #fff;
    border-color: #1890ff;
  }
  
  .page-btn:disabled {
    opacity: 0.5;
    cursor: not-allowed;
  }
  
  .page-ellipsis {
    padding: 8px 4px;
    color: #666;
  }
  
  /* Responsive */
  @media (max-width: 768px) {
    .boutique-info-card {
      flex-direction: column;
      text-align: center;
    }
    
    .boutique-stats {
      justify-content: center;
    }
    
    .boutique-actions {
      flex-direction: row;
      justify-content: center;
    }
    
    .products-toolbar {
      flex-direction: column;
      gap: 16px;
      align-items: stretch;
    }
    
    .toolbar-right {
      flex-direction: column;
      gap: 12px;
    }
    
    .products-container {
      grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
      gap: 12px;
    }
    
    .alibaba-product-card.list-card {
      flex-direction: column;
    }
    
    .alibaba-product-card.list-card .alibaba-image-area {
      width: 100%;
      margin-right: 0;
      margin-bottom: 12px;
    }
  }
  </style>
  