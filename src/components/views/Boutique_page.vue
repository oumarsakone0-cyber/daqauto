<template>
  <div class="min-h-screen bg-gray-50">
    <!-- Loading state -->
    <div v-if="isLoading" class="loading-container">
      <div class="loading-spinner"></div>
      <p class="text-gray-600">Loading the shop...</p>
    </div>

    <!-- Error state -->
    <div v-else-if="error" class="error-container">
      <div class="error-icon">⚠️</div>
      <h2>Loading error</h2>
      <p>{{ error }}</p>
      <button @click="loadShopData" class="retry-btn">Retry</button>
    </div>

    <!-- Shop content -->
    <div v-else class="shop-page">
      <!-- Shop Header Banner -->
      <div class="shop-header-banner">
        <div class="shop-banner-bg" :style="{ backgroundImage: `url(${shop.banner_image || 'https://www.cnhtcgroup.com/wp-content/uploads/2024/12/HOWO-light-truck-1920.jpg'})` }"></div>
        <div class="shop-header-overlay"></div>
        
        <div class="container relative z-10">
          <div class="shop-header-content">
            <!-- Shop Logo -->
            <div class="shop-logo-container">
              <img :src="shop.logo || 'https://static.vecteezy.com/system/resources/thumbnails/000/583/708/small/shop.jpg'" :alt="shop.name" class="shop-logo">
              <div class="shop-verified-badge" v-if="shop.is_verified">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="currentColor"><path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
              </div>
            </div>

            <!-- Shop Info -->
            <div class="shop-info">
              <h1 class="shop-name">{{ shop.name }}</h1>
              <p class="shop-description"><span style="background: #ffffffa1; padding: 5px; border-radius: 5px">{{ shop.description || 'No. 128 Zhongshan Road, Yuexiu District, Guangzhou, Guangdong 510000 - China' }}</span></p>
              
              <div class="shop-meta">
                <span style="background: #ffffffa1; padding: 5px; border-radius: 5px; display: flex; gap: inherit;">
                <div class="shop-meta-item">
                  <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 10c0 6-8 12-8 12s-8-6-8-12a8 8 0 0 1 16 0Z"></path><circle cx="12" cy="10" r="3"></circle></svg>
                  <span>{{ shop.location || 'Not specified' }}</span>
                </div>
                <div class="shop-meta-item">
                  <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 2L2 7l10 5 10-5-10-5z"></path><polyline points="2 17 12 22 22 17"></polyline><polyline points="2 12 12 17 22 12"></polyline></svg>
                  <span>{{ pagination.total || 0 }} products</span>
                </div>
                <div class="shop-meta-item">
                  <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 2L2 7l10 5 10-5-10-5z"></path><polyline points="2 17 12 22 22 17"></polyline><polyline points="2 12 12 17 22 12"></polyline></svg>
                  <span>2 Years on Wabili</span>
                </div>
                <div class="shop-meta-item2">
                  
                  <!-- <div v-if="supplier.verify"> -->
                    <BadgeCheckIcon  class="h-4 w-4 blue-color inline-block " />
                    <span  class="badge blue-color">Shop Verified by Wabili</span>
                  
                </div>
                </span>
              </div>

              <div class="shop-actions">
                <button @click="goToOverview()" class="btn-follow">
                  <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24"
                      fill="none" stroke="currentColor" stroke-width="2">
                    <line x1="4" y1="20" x2="20" y2="20"/>
                    <rect x="6" y="10" width="3" height="10"/>
                    <rect x="11" y="6" width="3" height="14"/>
                    <rect x="16" y="13" width="3" height="7"/>
                  </svg>

                  <span>Company Overview</span>
                </button>
                <button @click.stop="handleChatClick" class="btn-follow">
                  <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24"
                      fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M21 15a4 4 0 0 1-4 4H7l-4 4V7a4 4 0 0 1 4-4h10a4 4 0 0 1 4 4z"/>
                    <line x1="8" y1="10" x2="8" y2="10"/>
                    <line x1="12" y1="10" x2="12" y2="10"/>
                    <line x1="16" y1="10" x2="16" y2="10"/>
                  </svg>

                  <span>Chat Now</span>
                </button>
                <button @click="followShop" class="btn-follow" :class="{ 'is-following': isFollowing }">
                  <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle><line x1="19" x2="19" y1="8" y2="14"></line><line x1="22" x2="16" y1="11" y2="11"></line></svg>
                  <span>{{ isFollowing ? 'Subscribed' : 'Follow' }}</span>
                </button>
                
                 <!-- 
                <button @click="contactShop" class="btn-contact">
                  <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"></path></svg>
                  <span>Contacter</span>
                </button>
                -->
                <button @click="shareShop" class="btn-share">
                  <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="18" cy="5" r="3"></circle><circle cx="6" cy="12" r="3"></circle><circle cx="18" cy="19" r="3"></circle><line x1="8.59" x2="15.42" y1="13.51" y2="17.49"></line><line x1="15.41" x2="8.59" y1="6.51" y2="10.49"></line></svg>
                </button>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Shop Stats Cards -->
      <div class="container">
        <div class="shop-stats-section">
          <div class="stats-grid">
            <div class="stat-card">
              <div class="stat-icon bg-gradient-to-br from-orange-500 to-orange-600">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 2L2 7l10 5 10-5-10-5z"></path><polyline points="2 17 12 22 22 17"></polyline><polyline points="2 12 12 17 22 12"></polyline></svg>
              </div>
              <div class="stat-content">
                <p class="stat-label">Products</p>
                <p class="stat-value">{{ shopStats.totalProducts }}</p>
              </div>
              <div class="stat-trend positive">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="23 6 13.5 15.5 8.5 10.5 1 18"></polyline></svg>
                <span>+{{ shopStats.productsGrowth }}%</span>
              </div>
            </div>

             <!-- 
            <div class="stat-card">
              <div class="stat-icon bg-gradient-to-br from-green-500 to-emerald-600">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"></polygon></svg>
              </div>
              <div class="stat-content">
                <p class="stat-label">Average rating</p>
                <p class="stat-value">{{ shop.rating || '0' }}/5</p>
              </div>
              <div class="stat-reviews">{{ shopStats.totalReviews }} reviews</div>
            </div>
             -->

            <div class="stat-card">
              <div class="stat-icon bg-gradient-to-br from-purple-500 to-purple-600">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle><path d="M22 21v-2a4 4 0 0 0-3-3.87"></path><path d="M16 3.13a4 4 0 0 1 0 7.75"></path></svg>
              </div>
              <div class="stat-content">
                <p class="stat-label">Visits</p>
                <p class="stat-value">{{ totalViews }} <span style="color: gray; font-size: 19px">views</span></p>
              </div>
              <div class="stat-trend positive">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="23 6 13.5 15.5 8.5 10.5 1 18"></polyline></svg>
                <span>+{{ shopStats.followersGrowth }}%</span>
              </div>
            </div>
          </div>
        </div>

        <!-- Filters and Sort -->
        <div class="filters-section">
          <div class="filters-header">
            <h2 class="filters-title">All products</h2>
            <div class="filters-actions">
              <button @click="toggleFilters" class="btn-filter">
                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polygon points="22 3 2 3 10 12.46 10 19 14 21 14 12.46 22 3"></polygon></svg>
                <span>Filters</span>
                <span class="filter-badge" v-if="activeFiltersCount > 0">{{ activeFiltersCount }}</span>
              </button>
              
              <div class="sort-dropdown">
                <button @click="toggleSortMenu" class="btn-sort">
                  <span>{{ currentSortLabel }}</span>
                  <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="6 9 12 15 18 9"></polyline></svg>
                </button>
                <div v-if="showSortMenu" class="sort-menu">
                  <button 
                    v-for="option in sortOptions" 
                    :key="option.value"
                    @click="selectSort(option)"
                    class="sort-option"
                    :class="{ active: currentSort === option.value }"
                  >
                    {{ option.label }}
                  </button>
                </div>
              </div>

              <div class="view-toggle">
                <button @click="viewMode = 'grid'" class="view-btn" :class="{ active: viewMode === 'grid' }">
                  <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="7" height="7"></rect><rect x="14" y="3" width="7" height="7"></rect><rect x="14" y="14" width="7" height="7"></rect><rect x="3" y="14" width="7" height="7"></rect></svg>
                </button>
                <button @click="viewMode = 'list'" class="view-btn" :class="{ active: viewMode === 'list' }">
                  <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="8" x2="21" y1="6" y2="6"></line><line x1="8" x2="21" y1="12" y2="12"></line><line x1="8" x2="21" y1="18" y2="18"></line><line x1="3" x2="3.01" y1="6" y2="6"></line><line x1="3" x2="3.01" y1="12" y2="12"></line><line x1="3" x2="3.01" y1="18" y2="18"></line></svg>
                </button>
              </div>
            </div>
          </div>

          <!-- Active Filters -->
          <div v-if="activeFiltersCount > 0" class="active-filters">
            <span class="active-filters-label">Active filters:</span>
            <div class="active-filters-list">
              <button 
                v-for="filter in activeFiltersList" 
                :key="filter.key"
                @click="removeFilter(filter.key)"
                class="filter-tag"
              >
                {{ filter.label }}
                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" x2="6" y1="6" y2="18"></line><line x1="6" x2="18" y1="6" y2="18"></line></svg>
              </button>
              <button @click="clearAllFilters" class="clear-filters-btn">Clear all</button>
            </div>
          </div>
        </div>

        <!-- Products Section -->
        <section class="products-section">
          <!-- Loading state -->
          <div v-if="isLoadingProducts" class="loading-products">
            <div class="loading-product-skeleton" v-for="i in 6" :key="i">
              <div class="skeleton-product-image"></div>
              <div class="skeleton-product-content">
                <div class="skeleton-product-title"></div>
                <div class="skeleton-product-price"></div>
              </div>
            </div>
          </div>

          <!-- Error state -->
          <div v-else-if="productsError" class="error-products">
            <p class="error-message">{{ productsError }}</p>
            <button @click="loadProducts" class="btn-retry">Retry</button>
          </div>

          <!-- No products -->
          <div v-else-if="products.length === 0" class="no-products">
            <div class="no-products-icon">📦</div>
            <h3>No products found</h3>
            <p>This shop doesn’t have any products yet, or your filters are too restrictive.</p>
            <button v-if="activeFiltersCount > 0" @click="clearAllFilters" class="btn-clear">Clear filters</button>
          </div>

          <!-- Products Grid -->
          <div v-else :class="['products-grid', viewMode === 'list' ? 'list-view' : '']">
            <ProductCard
              v-for="(product, index) in products" 
              :key="product.id || index"
              :product="product"
              :is-mobile="false"
              :card-height="250"
              :image-height="200"
              @product-click="navigateToProduct"
              @favorite-click="toggleFavorite"
              @contact-click="contactSupplier"
              @chat-click="chatWithSupplier"
            />
          </div>

          <!-- Pagination -->
          <div v-if="pagination.total_pages > 1" class="pagination">
            <button 
              @click="goToPage(pagination.current_page - 1)" 
              :disabled="pagination.current_page === 1"
              class="pagination-btn"
            >
              <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="15 18 9 12 15 6"></polyline></svg>
              Back
            </button>

            <div class="pagination-numbers">
              <button 
                v-for="page in visiblePages" 
                :key="page"
                @click="goToPage(page)"
                class="pagination-number"
                :class="{ active: page === pagination.current_page, disabled: page === '...' }"
                :disabled="page === '...'"
              >
                {{ page }}
              </button>
            </div>

            <button 
              @click="goToPage(pagination.current_page + 1)" 
              :disabled="pagination.current_page === pagination.total_pages"
              class="pagination-btn"
            >
              Next
              <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="9 18 15 12 9 6"></polyline></svg>
            </button>
          </div>
        </section>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted, watch } from 'vue'
import { useRouter, useRoute } from 'vue-router'
import { productsApi } from '../../services/api.js'
import { BadgeCheckIcon } from 'lucide-vue-next';
import ProductCard from '../../components/home/ProductCard.vue'
import { useChatStore } from '../../stores/chat'

const router = useRouter()
const route = useRoute()

const boutiqueId = computed(() => route.params.boutique_id)

const chatStore = useChatStore()

// Shop data
const shop = ref({
  id: null,
  name: '',
  description: '',
  logo: '',
  banner_image: '',
  location: '',
  market: '',
  rating: 0,
  is_verified: false,
  phone: '',
  address: '',
  business_type: '',
  experience: 0,
  premium: false
})

const shopStats = ref({
  totalProducts: 0,
  totalSales: 0,
  totalReviews: 0,
  followers: 0,
  productsGrowth: 0,
  salesGrowth: 0,
  followersGrowth: 0
})

const products = ref([])
const pagination = ref({
  current_page: 1,
  per_page: 12,
  total: 0,
  total_pages: 1
})

const isLoading = ref(true)
const isLoadingProducts = ref(false)
const error = ref(null)
const productsError = ref(null)

const showFilters = ref(false)
const showSortMenu = ref(false)
const viewMode = ref('grid')
const currentSort = ref('recent')
const activeFiltersCount = ref(0)
const activeFiltersList = ref([])

const sortOptions = [
  { value: 'recent', label: 'Plus récents' },
  { value: 'price-asc', label: 'Prix croissant' },
  { value: 'price-desc', label: 'Prix décroissant' },
  { value: 'popular', label: 'Plus populaires' },
  { value: 'rating', label: 'Mieux notés' }
]

const currentSortLabel = computed(() => {
  return sortOptions.find(opt => opt.value === currentSort.value)?.label || 'Trier par'
})

const visiblePages = computed(() => {
  const pages = []
  const total = pagination.value.total_pages
  const current = pagination.value.current_page
  
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

const isFollowing = ref(false)

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

const navigateToProduct = (product) => {
  let slug = product.slug
  if (!slug && product.name) {
    slug = generateSlug(product.name)
  }
  
  if (!slug) {
    slug = `produit-${product.id || Date.now()}`
  }
  
  router.push(`/detail_resultat_produit/${slug}`)
}

const toggleFavorite = async (product) => {
  try {
    const userData = localStorage.getItem('user') || sessionStorage.getItem('user')
    if (!userData) {
      console.log('Utilisateur non connecté')
      return
    }
    
    const user = JSON.parse(userData)
    const likeData = { id_produit: product.id, user_id: user.id }
    
    const result = await productsApi.addLike(likeData)
    
    if (result.success) {
      console.log('Favori mis à jour avec succès')
    } else {
      console.error('Erreur lors de la mise à jour du favori:', result.error)
    }
  } catch (error) {
    console.error('Erreur toggleFavorite:', error)
  }
}

const contactSupplier = (product) => {
  console.log('Contact supplier:', product)
}

const chatWithSupplier = (product) => {
  console.log('Chat with supplier:', product)
}

const followShop = () => {
  isFollowing.value = !isFollowing.value
}

const handleChatClick = async () => {
  try {
    await chatStore.setSupplier(products[0])
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

const contactShop = () => {
  console.log('Contact shop')
}

const shareShop = () => {
  console.log('Share shop')
}

const toggleFilters = () => {
  showFilters.value = !showFilters.value
}

const toggleSortMenu = () => {
  showSortMenu.value = !showSortMenu.value
}

const selectSort = (option) => {
  currentSort.value = option.value
  showSortMenu.value = false
  sortProducts()
}

const removeFilter = (key) => {
  console.log('Remove filter:', key)
}

const clearAllFilters = () => {
  activeFiltersCount.value = 0
  activeFiltersList.value = []
}

const goToPage = (page) => {
  if (page === '...' || page < 1 || page > pagination.value.total_pages) return
  loadProducts(page)
}

const sortProducts = () => {
  console.log('Sort products by:', currentSort.value)
}

const loadShopData = async () => {
  if (!boutiqueId.value) {
    error.value = 'ID de boutique manquant'
    isLoading.value = false
    return
  }

  try {
    isLoading.value = true
    error.value = null
    
    // Load products with boutique_id
    await loadProducts(1)
    
  } catch (err) {
    console.error('Erreur lors du chargement de la boutique:', err)
    error.value = 'Impossible de charger les données de la boutique'
  } finally {
    isLoading.value = false
  }
}

const goToOverview = () => {
  router.push(`/boutique_overview/${boutiqueId.value}`)
}

const totalViews = computed(() => {
  return products.value.reduce((sum, product) => {
    return sum + (Number(product.views_count) || 0)
  }, 0)
})

const loadProducts = async (page = 1) => {
  if (!boutiqueId.value) {
    productsError.value = 'ID de boutique manquant'
    return
  }

  try {
    isLoadingProducts.value = true
    productsError.value = null

    // 🔥 Appel API unifié (comme loadStoreProducts)
    const result = await productsApi.getProducts({
      action: 'list',
      boutique_id: boutiqueId.value,
      limit: 12,
      page,
      _cb: `${Date.now()}_${Math.random().toString(36).substring(7)}`
    })

    if (result.success) {

      /* ==========================
         INFOS BOUTIQUE
      ========================== */
      if (result.boutique) {
        shop.value = {
          ...shop.value,
          id: result.boutique.id,
          name: result.boutique.name,
          description: result.boutique.description,
          logo: result.boutique.logo,
          banner_image: result.boutique.banner_image,
          location: result.boutique.address,
          market: result.boutique.market,
          rating: result.boutique.rating,
          is_verified: result.boutique.is_verified,
          phone: result.boutique.phone,
          address: result.boutique.address,
          business_type: result.boutique.business_type,
          experience: result.boutique.experience,
          premium: result.boutique.premium,
        }
      }

      /* ==========================
         PRODUITS
      ========================== */
      products.value = (result.data || []).map(product => {
        const { all_images, ...rest } = product

        return {
            ...rest,
            images: Array.isArray(all_images) ? all_images : [],
        }
        })

        console.log('ismoooooo products.value', products.value)

      /* ==========================
         PAGINATION
      ========================== */
      pagination.value = result.pagination || {
        current_page: page,
        per_page: 12,
        total: 0,
        total_pages: 1,
      }

      /* ==========================
         STATS BOUTIQUE
      ========================== */
      shopStats.value = {
        ...shopStats.value,
        totalProducts: pagination.value.total || 0,
        totalSales: Math.floor(products.value.length * 5.2),
        totalReviews: Math.floor(products.value.length * 3.1),
        followers: Math.floor(products.value.length * 7.8),
        productsGrowth: 12.5,
        salesGrowth: 18.3,
        followersGrowth: 24.7,
      }

    } else {
      productsError.value = result.message || 'Erreur lors du chargement des produits'
    }
  } catch (err) {
    console.error('❌ Erreur lors du chargement des produits:', err)
    productsError.value = 'Impossible de charger les produits'
  } finally {
    isLoadingProducts.value = false
  }
}


watch(boutiqueId, (newId) => {
  if (newId) {
    loadShopData()
  }
})

onMounted(() => {
  loadShopData()
})
</script>

<style scoped>
.container {
  max-width: 1500px;
  margin: 0 auto;
  padding: 0 20px;
}

.loading-container,
.error-container {
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  min-height: 400px;
  padding: 40px;
}

.loading-spinner {
  width: 40px;
  height: 40px;
  border: 4px solid #f3f3f3;
  border-top: 4px solid #fe7900;
  border-radius: 50%;
  animation: spin 1s linear infinite;
}

@keyframes spin {
  0% { transform: rotate(0deg); }
  100% { transform: rotate(360deg); }
}

.error-icon {
  font-size: 48px;
  margin-bottom: 16px;
}

.retry-btn,
.btn-retry {
  margin-top: 16px;
  padding: 10px 24px;
  background: linear-gradient(90deg, #fe7900, #ff5a01);
  color: white;
  border: none;
  border-radius: 8px;
  cursor: pointer;
  font-weight: 600;
  transition: all 0.3s ease;
}

.retry-btn:hover,
.btn-retry:hover {
  opacity: 0.9;
  transform: translateY(-2px);
}

.shop-header-banner {
  position: relative;
  background: #f8f9fa;
  padding: 60px 0 40px;
  margin-bottom: 32px;
  overflow: hidden;
}

.shop-banner-bg {
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background-size: cover;
  background-position: center;
  opacity: 0.4;
}

.shop-header-overlay {
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: linear-gradient(135deg, rgba(254, 121, 0, 0.1) 0%, rgba(24, 189, 133, 0.1) 100%);
}

.shop-header-content {
  display: flex;
  gap: 32px;
  align-items: flex-start;
}

.shop-logo-container {
  position: relative;
  flex-shrink: 0;
}

.shop-logo {
  width: 120px;
  height: 120px;
  border-radius: 16px;
  border: 4px solid white;
  background: white;
  object-fit: cover;
  box-shadow: 0 4px 16px rgba(0, 0, 0, 0.1);
}

.shop-verified-badge {
  position: absolute;
  bottom: -4px;
  right: -4px;
  width: 32px;
  height: 32px;
  background: linear-gradient(135deg, #18bd85 0%, #10976a 100%);
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  color: white;
  border: 3px solid white;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.15);
}

.shop-info {
  flex: 1;
}

.shop-name {
  font-size: 32px;
  font-weight: 700;
  color: #333;
  margin: 0 0 8px 0;
}

.shop-description {
  font-size: 16px;
  color: #666;
  margin: 0 0 16px 0;
}

.shop-meta {
  display: flex;
  flex-wrap: wrap;
  gap: 24px;
  margin-bottom: 24px;
}

.shop-meta-item {
  display: flex;
  align-items: center;
  gap: 6px;
  font-size: 14px;
  color: #666;
}

.shop-meta-item2 {
  display: flex;
  align-items: center;
  gap: 6px;
  font-size: 14px;
  color: #666;
}

.shop-meta-item svg {
  color: #fe7900;
}

.shop-meta-item2 svg {
  color: #1890ff;
}

.shop-actions {
  display: flex;
  gap: 12px;
}

.btn-follow,
.btn-contact {
  display: flex;
  align-items: center;
  gap: 8px;
  padding: 10px 20px;
  border-radius: 8px;
  font-size: 14px;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.3s ease;
  border: none;
}

.btn-follow {
  background: linear-gradient(135deg, #fe9700 0%, #ff8c00 100%);
  color: white;
}

.btn-follow.is-following {
  background: #18bd85;
}

.btn-follow:hover {
  opacity: 0.9;
  transform: translateY(-2px);
}

.btn-contact {
  background: white;
  color: #333;
  border: 2px solid #e8e8e8;
}

.btn-contact:hover {
  border-color: #fe7900;
  color: #fe7900;
}

.btn-share {
  display: flex;
  align-items: center;
  justify-content: center;
  width: 44px;
  height: 44px;
  border-radius: 8px;
  background: white;
  border: 2px solid #e8e8e8;
  cursor: pointer;
  transition: all 0.3s ease;
}

.btn-share:hover {
  border-color: #fe7900;
  color: #fe7900;
}

.shop-stats-section {
  margin: -60px 0 32px 0;
}

.stats-grid {
  display: grid;
  grid-template-columns: repeat(4, 1fr);
  gap: 20px;
}

.stat-card {
  background: white;
  border-radius: 12px;
  padding: 24px;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
  border: 1px solid #f0f0f0;
  transition: all 0.3s ease;
  position: relative;
}

.stat-card:hover {
  box-shadow: 0 4px 16px rgba(0, 0, 0, 0.12);
  transform: translateY(-4px);
}

.stat-icon {
  width: 48px;
  height: 48px;
  border-radius: 12px;
  display: flex;
  align-items: center;
  justify-content: center;
  margin-bottom: 16px;
  color: white;
}

.stat-content {
  margin-bottom: 12px;
}

.stat-label {
  font-size: 13px;
  color: #666;
  margin: 0 0 4px 0;
}

.stat-value {
  font-size: 28px;
  font-weight: 700;
  color: #333;
  margin: 0;
}

.stat-trend {
  display: flex;
  align-items: center;
  gap: 4px;
  font-size: 13px;
  font-weight: 600;
}

.stat-trend.positive {
  color: #18bd85;
}

.stat-trend.negative {
  color: #ff5a01;
}

.stat-reviews {
  font-size: 13px;
  color: #666;
}

.filters-section {
  background: white;
  border-radius: 12px;
  padding: 20px 24px;
  margin-bottom: 24px;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
}

.filters-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.filters-title {
  font-size: 20px;
  font-weight: 700;
  color: #333;
  margin: 0;
}

.filters-actions {
  display: flex;
  gap: 12px;
  align-items: center;
}

.btn-filter {
  display: flex;
  align-items: center;
  gap: 8px;
  padding: 8px 16px;
  border: 2px solid #e8e8e8;
  border-radius: 8px;
  background: white;
  color: #333;
  font-size: 14px;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.3s ease;
  position: relative;
}

.btn-filter:hover {
  border-color: #fe7900;
  color: #fe7900;
}

.filter-badge {
  position: absolute;
  top: -8px;
  right: -8px;
  background: #fe7900;
  color: white;
  font-size: 11px;
  font-weight: 700;
  padding: 2px 6px;
  border-radius: 10px;
  min-width: 20px;
  text-align: center;
}

.sort-dropdown {
  position: relative;
}

.btn-sort {
  display: flex;
  align-items: center;
  gap: 8px;
  padding: 8px 16px;
  border: 2px solid #e8e8e8;
  border-radius: 8px;
  background: white;
  color: #333;
  font-size: 14px;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.3s ease;
}

.btn-sort:hover {
  border-color: #fe7900;
}

.sort-menu {
  position: absolute;
  top: calc(100% + 8px);
  right: 0;
  background: white;
  border-radius: 8px;
  box-shadow: 0 4px 16px rgba(0, 0, 0, 0.15);
  padding: 8px;
  min-width: 200px;
  z-index: 10;
}

.sort-option {
  display: block;
  width: 100%;
  text-align: left;
  padding: 10px 12px;
  border: none;
  background: none;
  color: #333;
  font-size: 14px;
  cursor: pointer;
  border-radius: 6px;
  transition: all 0.2s ease;
}

.sort-option:hover {
  background: #f8f9fa;
}

.sort-option.active {
  background: #fff5ed;
  color: #fe7900;
  font-weight: 600;
}

.view-toggle {
  display: flex;
  gap: 4px;
  background: #f8f9fa;
  padding: 4px;
  border-radius: 8px;
}

.view-btn {
  display: flex;
  align-items: center;
  justify-content: center;
  width: 36px;
  height: 36px;
  border: none;
  background: transparent;
  color: #666;
  border-radius: 6px;
  cursor: pointer;
  transition: all 0.2s ease;
}

.view-btn:hover {
  background: white;
  color: #fe7900;
}

.view-btn.active {
  background: white;
  color: #fe7900;
  box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
}

.active-filters {
  display: flex;
  align-items: center;
  gap: 12px;
  margin-top: 16px;
  padding-top: 16px;
  border-top: 1px solid #f0f0f0;
}

.active-filters-label {
  font-size: 13px;
  color: #666;
  font-weight: 600;
}

.active-filters-list {
  display: flex;
  flex-wrap: wrap;
  gap: 8px;
}

.filter-tag {
  display: flex;
  align-items: center;
  gap: 6px;
  padding: 6px 12px;
  background: #fff5ed;
  color: #fe7900;
  border: 1px solid #ffd4b3;
  border-radius: 20px;
  font-size: 13px;
  font-weight: 500;
  cursor: pointer;
  transition: all 0.2s ease;
}

.filter-tag:hover {
  background: #ffe8d6;
}

.clear-filters-btn {
  padding: 6px 12px;
  background: #f8f9fa;
  color: #666;
  border: 1px solid #e8e8e8;
  border-radius: 20px;
  font-size: 13px;
  font-weight: 500;
  cursor: pointer;
  transition: all 0.2s ease;
}

.clear-filters-btn:hover {
  background: #e8e8e8;
}

.products-section {
  padding: 24px 0;
}

.products-grid {
  display: grid;
  grid-template-columns: repeat(6, 1fr);
  gap: 20px;
}

.products-grid.list-view {
  grid-template-columns: 1fr;
}

.loading-products {
  display: grid;
  grid-template-columns: repeat(6, 1fr);
  gap: 20px;
}

.loading-product-skeleton {
  background: white;
  border-radius: 8px;
  overflow: hidden;
  height: 300px;
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

.error-products,
.no-products {
  text-align: center;
  padding: 60px 20px;
}

.no-products-icon {
  font-size: 64px;
  margin-bottom: 16px;
}

.no-products h3 {
  font-size: 20px;
  font-weight: 700;
  color: #333;
  margin: 0 0 8px 0;
}

.no-products p {
  font-size: 14px;
  color: #666;
  margin: 0 0 24px 0;
}

.btn-clear {
  padding: 10px 24px;
  background: linear-gradient(90deg, #fe7900, #ff5a01);
  color: white;
  border: none;
  border-radius: 8px;
  cursor: pointer;
  font-weight: 600;
  transition: all 0.3s ease;
}

.btn-clear:hover {
  opacity: 0.9;
  transform: translateY(-2px);
}

.pagination {
  display: flex;
  justify-content: center;
  align-items: center;
  gap: 8px;
  margin-top: 40px;
}

.pagination-btn {
  display: flex;
  align-items: center;
  gap: 6px;
  padding: 10px 16px;
  border: 2px solid #e8e8e8;
  border-radius: 8px;
  background: white;
  color: #333;
  font-size: 14px;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.3s ease;
}

.pagination-btn:hover:not(:disabled) {
  border-color: #fe7900;
  color: #fe7900;
}

.pagination-btn:disabled {
  opacity: 0.5;
  cursor: not-allowed;
}

.pagination-numbers {
  display: flex;
  gap: 4px;
}

.pagination-number {
  width: 40px;
  height: 40px;
  border: 2px solid #e8e8e8;
  border-radius: 8px;
  background: white;
  color: #333;
  font-size: 14px;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.3s ease;
}

.pagination-number:hover {
  border-color: #fe7900;
  color: #fe7900;
}

.pagination-number.active {
  background: linear-gradient(90deg, #fe7900, #ff5a01);
  border-color: #fe7900;
  color: white;
}

.pagination-number.disabled {
  cursor: not-allowed;
  opacity: 0.5;
}

@media (max-width: 1400px) {
  .products-grid,
  .loading-products {
    grid-template-columns: repeat(5, 1fr);
  }
}

@media (max-width: 1200px) {
  .products-grid,
  .loading-products {
    grid-template-columns: repeat(4, 1fr);
  }
  
  .stats-grid {
    grid-template-columns: repeat(2, 1fr);
  }
}

@media (max-width: 992px) {
  .products-grid,
  .loading-products {
    grid-template-columns: repeat(3, 1fr);
  }
  
  .shop-header-content {
    flex-direction: column;
    align-items: center;
    text-align: center;
  }
  
  .shop-meta {
    justify-content: center;
  }
  
  .shop-actions {
    justify-content: center;
  }
}

.supplier-badges {
    display: flex;
    gap: 6px;
    margin-bottom: 8px;
  }

@media (max-width: 768px) {
  .products-grid,
  .loading-products {
    grid-template-columns: repeat(2, 1fr);
  }
  
  .stats-grid {
    grid-template-columns: 1fr;
  }
  
  .filters-header {
    flex-direction: column;
    gap: 16px;
    align-items: stretch;
  }
  
  .filters-actions {
    flex-wrap: wrap;
  }
  
  .shop-header-banner {
    padding: 40px 0 24px;
  }
  
  .shop-name {
    font-size: 24px;
  }
  
  .shop-logo {
    width: 100px;
    height: 100px;
  }
}

@media (max-width: 480px) {
  .products-grid,
  .loading-products {
    grid-template-columns: 1fr;
  }
  
  .pagination-numbers {
    gap: 2px;
  }
  
  .pagination-number {
    width: 36px;
    height: 36px;
    font-size: 13px;
  }
}
</style>
