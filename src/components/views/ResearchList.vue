<template>
  <div class="product-list-page">
    <!-- Header de navigation -->
    <div class="page-header">
      <div class="container">
        <div class="breadcrumb">
          <router-link to="/" class="breadcrumb-link">Accueil</router-link>
          <span class="breadcrumb-separator">></span>
          <a href="#" class="breadcrumb-link">Toutes les catégories</a>
          <span class="breadcrumb-separator">></span>
          <span class="breadcrumb-current">{{ appliedFilters.filter_name || 'Produits' }}</span>
        </div>
      </div>
    </div>

    <!-- Titre principal -->
    <div class="page-title-section" :style="{ backgroundImage: bannerBackgroundImage }">
      <div class="container">
        <h1 class="main-title">{{ getPageTitle() }}</h1>
        <p class="main-subtitle">Découvrez notre sélection de produits de qualité avec les meilleurs prix de gros</p>
      </div>
    </div>

    <!-- Contenu principal -->
    <div class="main-content">
      <div class="container">
        <div class="content-wrapper">
          <!-- Sidebar des filtres - visible uniquement sur desktop -->
          <div class="desktop-only">
            <SideBar @filter-change="handleFilterChange" />
          </div>

          <!-- Zone des produits -->
          <div class="products-area">
            <!-- Mobile Filter & Sort Bar -->
            <div class="mobile-filter-bar mobile-only desktop-hidden">
              <button class="mobile-filter-btn" @click="showFilterDialog = true">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                  <path d="M22 3H2l8 9.46V19l4 2v-8.54L22 3z"></path>
                </svg>
                <span>Filtres</span>
              </button>
              
              <div class="mobile-sort-dropdown">
                <select v-model="sortBy" @change="handleSortChange" class="mobile-sort-select">
                  <option value="created_at">Plus récents</option>
                  <option value="unit_price">Prix croissant</option>
                  <option value="unit_price_desc">Prix décroissant</option>
                  <option value="views_count">Plus populaires</option>
                  <option value="name">Nom A-Z</option>
                </select>
              </div>
            </div>

            <!-- Panneau latéral pour les filtres mobiles -->
            <div v-if="showFilterDialog" class="filter-sidebar-overlay" @click.self="showFilterDialog = false">
              <div class="filter-sidebar-panel">
                <div class="filter-sidebar-header">
                  <h3 class="filter-sidebar-title">Filtres</h3>
                  <button class="filter-sidebar-close" @click="showFilterDialog = false">&times;</button>
                </div>
                
                <div class="filter-sidebar-content">
                  <SideBar @filter-change="handleMobileFilterChange" />
                </div>
                
                <div class="filter-sidebar-actions">
                  <button class="filter-sidebar-reset" @click="clearAllFilters">Réinitialiser</button>
                  <button class="filter-sidebar-apply" @click="applyMobileFilters">Appliquer</button>
                </div>
              </div>
            </div>

            <!-- Loading -->
            <div v-if="loading" class="loading-container">
              <div class="loading-spinner"></div>
              <span>Chargement des produits...</span>
            </div>

            <!-- Erreur -->
            <div v-else-if="error" class="error-container">
              <h3>Erreur de chargement</h3>
              <p>{{ error }}</p>
              <button @click="loadProducts" class="retry-btn">Réessayer</button>
            </div>

            <!-- Contenu normal -->
            <div v-else>
              <!-- Filtres actifs -->
              <div v-if="hasActiveFilters" class="active-filters">
                <div class="filters-header">
                  <span class="filters-title">Filtres actifs:</span>
                  <button @click="clearAllFilters" class="clear-all-btn">Effacer tous</button>
                </div>
                <div class="filters-list">
                  <!-- Sous-catégories -->
                  <div v-if="activeFilters.subcategories" class="filter-tag">
                    <span>Sous-catégories: {{ activeFilters.subcategories }}</span>
                    <button @click="removeFilter('subcategories')" class="remove-filter">×</button>
                  </div>
                  
                  <!-- Marchés -->
                  <div v-if="activeFilters.boutiqueMarket" class="filter-tag">
                    <span>Marchés: {{ activeFilters.boutiqueMarket }}</span>
                    <button @click="removeFilter('boutiqueMarket')" class="remove-filter">×</button>
                  </div>
                  
                  <!-- Prix -->
                  <div v-if="activeFilters.minPrice || activeFilters.maxPrice" class="filter-tag">
                    <span>Prix: {{ formatPriceRange(activeFilters.minPrice, activeFilters.maxPrice) }}</span>
                    <button @click="removeFilter('price')" class="remove-filter">×</button>
                  </div>
                  
                  <!-- Marques de véhicules -->
                  <div v-if="activeFilters.vehicleMake" class="filter-tag">
                    <span>Marques: {{ activeFilters.vehicleMake }}</span>
                    <button @click="removeFilter('vehicleMake')" class="remove-filter">×</button>
                  </div>
                  
                  <!-- État du véhicule -->
                  <div v-if="activeFilters.vehicleCondition" class="filter-tag">
                    <span>État: {{ activeFilters.vehicleCondition }}</span>
                    <button @click="removeFilter('vehicleCondition')" class="remove-filter">×</button>
                  </div>
                  
                  <!-- Type de carburant -->
                  <div v-if="activeFilters.fuelType" class="filter-tag">
                    <span>Carburant: {{ activeFilters.fuelType }}</span>
                    <button @click="removeFilter('fuelType')" class="remove-filter">×</button>
                  </div>
                  
                  <!-- Transmission -->
                  <div v-if="activeFilters.transmissionType" class="filter-tag">
                    <span>Transmission: {{ activeFilters.transmissionType }}</span>
                    <button @click="removeFilter('transmissionType')" class="remove-filter">×</button>
                  </div>
                  
                  <!-- Type de conduite -->
                  <div v-if="activeFilters.driveType" class="filter-tag">
                    <span>Conduite: {{ activeFilters.driveType }}</span>
                    <button @click="removeFilter('driveType')" class="remove-filter">×</button>
                  </div>
                  
                  <!-- Marque du moteur -->
                  <div v-if="activeFilters.engineBrand" class="filter-tag">
                    <span>Moteur: {{ activeFilters.engineBrand }}</span>
                    <button @click="removeFilter('engineBrand')" class="remove-filter">×</button>
                  </div>
                  
                  <!-- Année -->
                  <div v-if="activeFilters.vehicleYearMin || activeFilters.vehicleYearMax" class="filter-tag">
                    <span>Année: {{ formatRange(activeFilters.vehicleYearMin, activeFilters.vehicleYearMax) }}</span>
                    <button @click="removeFilter('year')" class="remove-filter">×</button>
                  </div>
                  
                  <!-- Capacité de charge -->
                  <div v-if="activeFilters.payloadCapacityMin || activeFilters.payloadCapacityMax" class="filter-tag">
                    <span>Charge: {{ formatRange(activeFilters.payloadCapacityMin, activeFilters.payloadCapacityMax) }} kg</span>
                    <button @click="removeFilter('payload')" class="remove-filter">×</button>
                  </div>
                  
                  <!-- GVW -->
                  <div v-if="activeFilters.gvwMin || activeFilters.gvwMax" class="filter-tag">
                    <span>GVW: {{ formatRange(activeFilters.gvwMin, activeFilters.gvwMax) }} kg</span>
                    <button @click="removeFilter('gvw')" class="remove-filter">×</button>
                  </div>
                  
                  <!-- Livraison gratuite -->
                  <div v-if="activeFilters.freeShipping" class="filter-tag">
                    <span>Livraison gratuite</span>
                    <button @click="removeFilter('freeShipping')" class="remove-filter">×</button>
                  </div>
                  
                  <!-- En stock -->
                  <div v-if="activeFilters.stock" class="filter-tag">
                    <span>En stock</span>
                    <button @click="removeFilter('stock')" class="remove-filter">×</button>
                  </div>
                  
                  <!-- Fournisseur vérifié -->
                  <div v-if="activeFilters.boutiqueVerified" class="filter-tag">
                    <span>Fournisseur vérifié</span>
                    <button @click="removeFilter('boutiqueVerified')" class="remove-filter">×</button>
                  </div>
                  
                  <!-- Note minimum -->
                  <div v-if="activeFilters.minRating" class="filter-tag">
                    <span>Note: {{ activeFilters.minRating }}+ étoiles</span>
                    <button @click="removeFilter('minRating')" class="remove-filter">×</button>
                  </div>
                </div>
              </div>

              <AlibabaStyleSection 
                  @product-click="goToProduct"
                  @chat-click="handleChatClick"
                  @contact-click="handleContactClick"
                  :categories="categoryMap"
                />

              <!-- Barre d'outils - visible uniquement sur desktop -->
              <div class="toolbar desktop-only">
                <div class="toolbar-left">
                  <h2 class="section-title">{{ appliedFilters.filter_name || 'Produits' }}</h2>
                  <span class="results-count">{{ totalProducts.toLocaleString() }} produits trouvés</span>
                </div>
                
                <div class="toolbar-right">
                  <!-- Tri -->
                  <div class="sort-dropdown">
                    <select v-model="sortBy" @change="handleSortChange" class="sort-select">
                      <option value="created_at">Plus récents</option>
                      <option value="unit_price">Prix croissant</option>
                      <option value="unit_price_desc">Prix décroissant</option>
                      <option value="views_count">Plus populaires</option>
                      <option value="name">Nom A-Z</option>
                    </select>
                  </div>

                  <!-- Boutons vue grille/liste -->
                  <div class="view-toggle">
                    <button 
                      class="view-btn" 
                      :class="{ active: viewMode === 'grid' }"
                      @click="viewMode = 'grid'"
                    >
                      <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <rect x="3" y="3" width="7" height="7"></rect>
                        <rect x="14" y="3" width="7" height="7"></rect>
                        <rect x="14" y="14" width="7" height="7"></rect>
                        <rect x="3" y="14" width="7" height="7"></rect>
                      </svg>
                    </button>
                    <button 
                      class="view-btn" 
                      :class="{ active: viewMode === 'list' }"
                      @click="viewMode = 'list'"
                    >
                      <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <line x1="8" y1="6" x2="21" y2="6"></line>
                        <line x1="8" y1="12" x2="21" y2="12"></line>
                        <line x1="8" y1="18" x2="21" y2="18"></line>
                        <line x1="3" y1="6" x2="3.01" y2="6"></line>
                        <line x1="3" y1="12" x2="3.01" y2="12"></line>
                        <line x1="3" y1="18" x2="3.01" y2="18"></line>
                      </svg>
                    </button>
                  </div>
                </div>
              </div>

              <!-- Aucun résultat -->
              <div v-if="formattedProducts.length === 0" class="no-results">
                <svg class="no-results-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <h3>Aucun produit trouvé</h3>
                <p>Essayez de modifier vos critères de recherche ou vos filtres.</p>
                <button @click="clearAllFilters" class="clear-filters-btn">Effacer les filtres</button>
              </div>

              <!-- Grille de produits -->
              <div v-else>
                <!-- Version Mobile - Grille de produits -->
                <div class="mobile-products-grid mobile-only desktop-hidden">
                  <ProductCard
                    v-for="(product, index) in formattedProducts" 
                    :key="product.id"
                    :product="adaptProductData(product)"
                    :is-mobile="true"
                    :show-ad-badge="index % 5 === 0"
                    @product-click="goToProduct"
                    @favorite-click="toggleFavorite"
                    @contact-click="contactSupplier"
                    @chat-click="addToCart"
                  />
                </div>

                <!-- Version Desktop - Vue unifiée des produits qui s'adapte au format -->
                <div class="products-container desktop-only" :class="{ 'list-view': viewMode === 'list' }">
                  <ProductCard
                    v-for="(product, index) in formattedProducts" 
                    :key="product.id"
                    :product="adaptProductData(product)"
                    :is-mobile="false"
                    :card-height="300"
                    :image-height="250"
                    :show-ad-badge="index % 5 === 0"
                    @product-click="goToProduct"
                    @favorite-click="toggleFavorite"
                    @contact-click="contactSupplier"
                    @chat-click="addToCart"
                    class="search-product-card"
                    :class="{ 'list-card': viewMode === 'list' }"
                  />
                </div>

                <!-- Pagination -->
                <div class="pagination" v-if="totalPages > 1">
                  <button class="page-btn prev-btn" :disabled="currentPage === 1" @click="goToPage(currentPage - 1)">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                      <polyline points="15,18 9,12 15,6"></polyline>
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
                      <polyline points="9,18 15,12 9,6"></polyline>
                    </svg>
                  </button>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { ref, computed, onMounted, watch } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import SideBar from '../menu/FilterSide.vue'
import { productsApi, categoriesApi } from '../../services/api.js'
import ProductCard from '../home/ProductCard.vue'
import AlibabaStyleSection from '../pages/AlibabaStyleSection.vue'
import { formatPrice } from '../../services/formatPrice'

export default {
  name: 'ResultatView',
  components: {
    SideBar,
    ProductCard,
    AlibabaStyleSection
  },
  setup() {
    const route = useRoute()
    const router = useRouter()

    // États
    const loading = ref(false)
    const error = ref(null)
    const viewMode = ref('grid')
    const sortBy = ref('created_at')
    const currentPage = ref(1)
    const totalPages = ref(0)
    const totalProducts = ref(0)
    const itemsPerPage = ref(20)

    // Données des produits
    const rawProducts = ref([])
    const formattedProducts = ref([])
    const appliedFilters = ref({})
    const activeFilters = ref({})
    const categoryMap = ref({})

    // Paramètres de filtrage selon votre API
    const categoryId = ref(null)
    const subcategoryId = ref(null)
    const subSubcategoryId = ref(null)
    const productSelect = ref('')

    // État du panneau latéral de filtres
    const showFilterDialog = ref(false)
    const mobileFilters = ref({})

    // Computed
    const hasActiveFilters = computed(() => {
      return Object.keys(activeFilters.value).length > 0
    })

    const handleImageError = (event) => {
      event.target.src = 'https://t3.ftcdn.net/jpg/00/38/91/48/360_F_38914811_jQRpNpxUHrAlJs6lVzKYZPQAPE0A3CKc.jpg'
    }

    const buildFilterOptions = () => {
      const options = {
        page: currentPage.value,
        limit: itemsPerPage.value,
        sort: sortBy.value.includes('_desc') ? sortBy.value.replace('_desc', '') : sortBy.value,
        order: sortBy.value.includes('_desc') ? 'DESC' : 'ASC'
      }

      // Ajouter les filtres de base depuis l'URL
      if (categoryId.value) options.category = categoryId.value
      if (subcategoryId.value) options.subcategory = subcategoryId.value
      if (subSubcategoryId.value) options.sub_subcategory = subSubcategoryId.value
      if (productSelect.value) options.product_select = productSelect.value

      
      // Sous-catégories (déjà en comma-separated depuis FilterSide)
      if (activeFilters.value.subcategories) {
        options.subcategories = activeFilters.value.subcategories
      }

      // Marchés (déjà en comma-separated depuis FilterSide) -> boutique_market
      if (activeFilters.value.boutiqueMarket) {
        options.boutique_market = activeFilters.value.boutiqueMarket
      }

      // Prix
      if (activeFilters.value.minPrice) options.min_price = activeFilters.value.minPrice
      if (activeFilters.value.maxPrice) options.max_price = activeFilters.value.maxPrice

      // Marques de véhicules (déjà en comma-separated depuis FilterSide) -> vehicle_make
      if (activeFilters.value.vehicleMake) {
        options.vehicle_make = activeFilters.value.vehicleMake
      }

      // État du véhicule (single value depuis FilterSide) -> vehicle_condition
      if (activeFilters.value.vehicleCondition) {
        options.vehicle_condition = activeFilters.value.vehicleCondition
      }

      // Type de carburant (déjà en comma-separated depuis FilterSide) -> fuel_type
      if (activeFilters.value.fuelType) {
        options.fuel_type = activeFilters.value.fuelType
      }

      // Type de transmission (déjà en comma-separated depuis FilterSide) -> transmission_type
      if (activeFilters.value.transmissionType) {
        options.transmission_type = activeFilters.value.transmissionType
      }

      // Type de traction (déjà en comma-separated depuis FilterSide) -> drive_type
      if (activeFilters.value.driveType) {
        options.drive_type = activeFilters.value.driveType
      }

      // Marque du moteur (déjà en comma-separated depuis FilterSide) -> engine_brand
      if (activeFilters.value.engineBrand) {
        options.engine_brand = activeFilters.value.engineBrand
      }

      // Année du véhicule -> vehicle_year_min / vehicle_year_max
      if (activeFilters.value.vehicleYearMin) options.vehicle_year_min = activeFilters.value.vehicleYearMin
      if (activeFilters.value.vehicleYearMax) options.vehicle_year_max = activeFilters.value.vehicleYearMax

      // Capacité de charge -> payload_capacity_min / payload_capacity_max
      if (activeFilters.value.payloadCapacityMin) options.payload_capacity_min = activeFilters.value.payloadCapacityMin
      if (activeFilters.value.payloadCapacityMax) options.payload_capacity_max = activeFilters.value.payloadCapacityMax

      // GVW -> gvw_min / gvw_max
      if (activeFilters.value.gvwMin) options.gvw_min = activeFilters.value.gvwMin
      if (activeFilters.value.gvwMax) options.gvw_max = activeFilters.value.gvwMax

      // Note minimum -> min_rating
      if (activeFilters.value.minRating) options.min_rating = activeFilters.value.minRating

      // Options booléennes
      if (activeFilters.value.freeShipping) options.free_shipping = true
      if (activeFilters.value.stock) options.stock = true
      if (activeFilters.value.boutiqueVerified) options.boutique_verified = true

      console.log('[v0] Active Filters:', activeFilters.value)
      console.log('[v0] Built Options for API:', options)
      return options
    }

    // Chargement des produits depuis l'API
    const loadProducts = async () => {
      loading.value = true
      error.value = null

      try {
        const options = buildFilterOptions()
        console.log('🔄 Chargement des produits avec paramètres:', options)

        const response = await productsApi.getProductsForResults(options)

        if (response && response.data) {
          const products = response.data.products || response.data || []
          rawProducts.value = Array.isArray(products) ? products : []
          
          // Pagination
          if (response.pagination) {
            totalProducts.value = response.pagination.total || 0
            totalPages.value = response.pagination.total_pages || 1
            currentPage.value = response.pagination.current_page || 1
          } else {
            totalProducts.value = rawProducts.value.length
            totalPages.value = Math.ceil(totalProducts.value / itemsPerPage.value)
          }

          appliedFilters.value = response.applied_filters || {}

          // Formater les produits
          formattedProducts.value = productsApi.formatProductsForResults(rawProducts.value)

          // Initialiser currentImageIndex pour chaque produit
          formattedProducts.value.forEach(product => {
            if (!product.currentImageIndex) {
              product.currentImageIndex = 0
            }
          })

          console.log('✅ Produits chargés:', {
            raw: rawProducts.value.length,
            formatted: formattedProducts.value.length,
            total: totalProducts.value,
            filters: appliedFilters.value
          })
        } else {
          throw new Error('Format de réponse invalide')
        }
      } catch (err) {
        console.error('❌ Erreur chargement produits:', err)
        error.value = err.message || 'Erreur lors du chargement des produits'
        rawProducts.value = []
        formattedProducts.value = []
        totalProducts.value = 0
      } finally {
        loading.value = false
      }
    }

    // Gérer le changement de filtres depuis la sidebar
    const handleFilterChange = (filters) => {
      console.log('[v0] Filtres reçus de FilterSide:', filters)
      activeFilters.value = { ...filters }
      currentPage.value = 1
      loadProducts()
    }

    // Handle mobile filter changes
    const handleMobileFilterChange = (filters) => {
      mobileFilters.value = { ...filters }
    }

    // Apply mobile filters and close the filter dialog
    const applyMobileFilters = () => {
      handleFilterChange(mobileFilters.value)
      showFilterDialog.value = false
    }

    const removeFilter = (filterType) => {
      const newFilters = { ...activeFilters.value }
      
      switch(filterType) {
        case 'subcategories':
          delete newFilters.subcategories
          break
        case 'boutiqueMarket': // Changed from 'markets' to 'boutiqueMarket'
          delete newFilters.boutiqueMarket
          break
        case 'price':
          delete newFilters.minPrice
          delete newFilters.maxPrice
          break
        case 'vehicleMake': // Changed from 'vehicleMakes' to 'vehicleMake'
          delete newFilters.vehicleMake
          break
        case 'vehicleCondition':
          delete newFilters.vehicleCondition
          break
        case 'fuelType': // Changed from 'fuelTypes' to 'fuelType'
          delete newFilters.fuelType
          break
        case 'transmissionType': // Changed from 'transmissionTypes' to 'transmissionType'
          delete newFilters.transmissionType
          break
        case 'driveType': // Changed from 'driveTypes' to 'driveType'
          delete newFilters.driveType
          break
        case 'engineBrand': // Changed from 'engineBrands' to 'engineBrand'
          delete newFilters.engineBrand
          break
        case 'year':
          delete newFilters.vehicleYearMin
          delete newFilters.vehicleYearMax
          break
        case 'payload':
          delete newFilters.payloadCapacityMin
          delete newFilters.payloadCapacityMax
          break
        case 'gvw':
          delete newFilters.gvwMin
          delete newFilters.gvwMax
          break
        case 'freeShipping':
          delete newFilters.freeShipping
          break
        case 'stock': // Changed from 'inStock' to 'stock'
          delete newFilters.stock
          break
        case 'boutiqueVerified': // Changed from 'verifiedSupplier' to 'boutiqueVerified'
          delete newFilters.boutiqueVerified
          break
        case 'minRating':
          delete newFilters.minRating
          break
      }

      activeFilters.value = newFilters
      loadProducts()
    }

    // Effacer tous les filtres
    const clearAllFilters = () => {
      activeFilters.value = {}
      mobileFilters.value = {}
      currentPage.value = 1
      loadProducts()
      
      if (showFilterDialog.value) {
        showFilterDialog.value = false
      }
    }

    // Formater la plage de prix
    const formatPriceRange = (min, max) => {
      if (min && max) {
        return `${formatPrice(min)} - ${formatPrice(max)}`
      } else if (min) {
        return `Min: ${formatPrice(min)}`
      } else if (max) {
        return `Max: ${formatPrice(max)}`
      }
      return ''
    }

    const formatRange = (min, max) => {
      if (min && max) {
        return `${min} - ${max}`
      } else if (min) {
        return `Min: ${min}`
      } else if (max) {
        return `Max: ${max}`
      }
      return ''
    }

    // Obtenir les noms des catégories
    const getCategoryNames = (categoryIds) => {
      if (!categoryIds) return ''
      
      const ids = categoryIds.split(',')
      const names = ids.map(id => categoryMap.value[id] || `Cat. ${id}`)
      return names.join(', ')
    }

    // Charger la correspondance des catégories
    const loadCategoryMap = async () => {
      try {
        const response = await categoriesApi.getCategories()
        
        if (response && response.data) {
          response.data.forEach(category => {
            categoryMap.value[category.id] = category.name
          })
        }
      } catch (err) {
        console.error('Erreur lors du chargement des catégories:', err)
      }
    }

    const getPageTitle = () => {
      if (route.query.search) {
        return `Résultats pour "${route.query.search}"`
      } else if (appliedFilters.value.filter_name) {
        return appliedFilters.value.filter_name
      }
      return 'Tous les produits'
    }

    const handleSortChange = () => {
      currentPage.value = 1
      loadProducts()
    }

    const goToPage = (page) => {
      if (page >= 1 && page <= totalPages.value) {
        currentPage.value = page
        
        const query = { ...route.query, page }
        router.push({ path: route.path, query })
        
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
      console.log('Toggle favori:', product.name, product.isFavorite)
    }

    const addToCart = (product) => {
      console.log('Ajout au panier:', product.name)
    }

    const contactSupplier = (product) => {
      console.log('Contact fournisseur:', product.shopName)
    }

    // Initialiser les paramètres depuis l'URL
    const initializeFromRoute = () => {
      categoryId.value = route.query.category || null
      subcategoryId.value = route.query.subcategory || null
      subSubcategoryId.value = route.query.sub_subcategory || null
      productSelect.value = route.query.search || ''
      currentPage.value = parseInt(route.query.page) || 1

      // Initialiser les filtres actifs depuis l'URL si nécessaire
      const filters = {}
      if (route.query.subcategories) filters.subcategories = route.query.subcategories
      if (route.query.boutiqueMarket) filters.boutiqueMarket = route.query.boutiqueMarket // Updated key
      if (route.query.minPrice) filters.minPrice = route.query.minPrice
      if (route.query.maxPrice) filters.maxPrice = route.query.maxPrice
      if (route.query.vehicleMake) filters.vehicleMake = route.query.vehicleMake // Updated key
      if (route.query.vehicleCondition) filters.vehicleCondition = route.query.vehicleCondition
      if (route.query.fuelType) filters.fuelType = route.query.fuelType // Updated key
      if (route.query.transmissionType) filters.transmissionType = route.query.transmissionType // Updated key
      if (route.query.driveType) filters.driveType = route.query.driveType // Updated key
      if (route.query.engineBrand) filters.engineBrand = route.query.engineBrand // Updated key
      if (route.query.vehicleYearMin) filters.vehicleYearMin = route.query.vehicleYearMin
      if (route.query.vehicleYearMax) filters.vehicleYearMax = route.query.vehicleYearMax
      if (route.query.payloadCapacityMin) filters.payloadCapacityMin = route.query.payloadCapacityMin
      if (route.query.payloadCapacityMax) filters.payloadCapacityMax = route.query.payloadCapacityMax
      if (route.query.gvwMin) filters.gvwMin = route.query.gvwMin
      if (route.query.gvwMax) filters.gvwMax = route.query.gvwMax
      if (route.query.freeShipping) filters.freeShipping = route.query.freeShipping === 'true' // Ensure boolean conversion
      if (route.query.stock) filters.stock = route.query.stock === 'true' // Updated key and ensure boolean
      if (route.query.boutiqueVerified) filters.boutiqueVerified = route.query.boutiqueVerified === 'true' // Updated key and ensure boolean
      if (route.query.minRating) filters.minRating = route.query.minRating

      activeFilters.value = filters
    }

    // Pages visibles pour la pagination
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

    // Computed pour l'image de bannière
    const bannerBackgroundImage = computed(() => {
      const categoryImages = {
        'smartphones': 'linear-gradient(rgba(0, 0, 0, 0.5), rgba(0, 0, 0, 0.5)), url("https://images.unsplash.com/photo-1511707171634-5f897ff02aa9?ixlib=rb-4.0.3&auto=format&fit=crop&w=2000&q=80")',
        'ordinateurs': 'linear-gradient(rgba(0, 0, 0, 0.5), rgba(0, 0, 0, 0.5)), url("https://images.unsplash.com/photo-1496181133206-80ce9b88a853?ixlib=rb-4.0.3&auto=format&fit=crop&w=2000&q=80")',
        'tablettes': 'linear-gradient(rgba(0, 0, 0, 0.5), rgba(0, 0, 0, 0.5)), url("https://images.unsplash.com/photo-1544244015-0df4b3ffc6b0?ixlib=rb-4.0.3&auto=format&fit=crop&w=2000&q=80")',
        'accessoires': 'linear-gradient(rgba(0, 0, 0, 0.5), rgba(0, 0, 0, 0.5)), url("https://images.unsplash.com/photo-1583394838336-acd977736f90?ixlib=rb-4.0.3&auto=format&fit=crop&w=2000&q=80")',
        'audio': 'linear-gradient(rgba(0, 0, 0, 0.5), rgba(0, 0, 0, 0.5)), url("https://images.unsplash.com/photo-1505740420928-5e560c06d30e?ixlib=rb-4.0.3&auto=format&fit=crop&w=2000&q=80")',
        'gaming': 'linear-gradient(rgba(0, 0, 0, 0.5), rgba(0, 0, 0, 0.5)), url("https://images.unsplash.com/photo-1542751371-adc38448a05e?ixlib=rb-4.0.3&auto=format&fit=crop&w=2000&q=80")',
        'montres': 'linear-gradient(rgba(0, 0, 0, 0.5), rgba(0, 0, 0, 0.5)), url("https://images.unsplash.com/photo-1523275335684-37898b6baf30?ixlib=rb-4.0.3&auto=format&fit=crop&w=2000&q=80")',
        'cameras': 'linear-gradient(rgba(0, 0, 0, 0.5), rgba(0, 0, 0, 0.5)), url("https://images.unsplash.com/photo-1502920917128-1aa500764cbd?ixlib=rb-4.0.3&auto=format&fit=crop&w=2000&q=80")',
        'drones': 'linear-gradient(rgba(0, 0, 0, 0.5), rgba(0, 0, 0, 0.5)), url("https://images.unsplash.com/photo-1473968512647-3e447244af8f?ixlib=rb-4.0.3&auto=format&fit=crop&w=2000&q=80")',
        'electronique': 'linear-gradient(rgba(0, 0, 0, 0.5), rgba(0, 0, 0, 0.5)), url("https://images.unsplash.com/photo-1518709268805-4e9042af2176?ixlib=rb-4.0.3&auto=format&fit=crop&w=2000&q=80")'
      }

      const defaultImage = 'linear-gradient(rgba(0, 0, 0, 0.5), rgba(0, 0, 0, 0.5)), url("https://images.unsplash.com/photo-1518709268805-4e9042af2176?ixlib=rb-4.0.3&auto=format&fit=crop&w=2000&q=80")'

      if (formattedProducts.value.length > 0) {
        const firstProduct = formattedProducts.value[0]
        const category = firstProduct.category?.toLowerCase() || firstProduct.categoryName?.toLowerCase()
        
        for (const [key, image] of Object.entries(categoryImages)) {
          if (category && category.includes(key)) {
            return image
          }
        }
      }

      if (appliedFilters.value.filter_name) {
        const filterName = appliedFilters.value.filter_name.toLowerCase()
        for (const [key, image] of Object.entries(categoryImages)) {
          if (filterName.includes(key)) {
            return image
          }
        }
      }

      return defaultImage
    })

    // Adapter les données du produit pour le composant ProductCard
    const adaptProductData = (product) => {
      return {
        id: product.id,
        name: product.name,
        slug: product.slug,
        unit_price: product.unitPrice,
        wholesale_price: product.wholesalePrice,
        wholesale_min_qty: product.minQuantity,
        primary_image: product.images?.[0],
        images: product.images || [],
        views: product.reviews,
        market: product.supplier?.country?.toLowerCase() || 'ci',
        boutique_name: product.shopName,
        rating: product.rating || 4.5,
        experience: product.supplier?.years || 3,
        freeShipping: product.freeShipping,
        originalPrice: product.originalPrice
      }
    }

    const handleChatClick = (supplier) => {
      console.log('Chat avec le fournisseur:', supplier.name)
    }

    const handleContactClick = (supplier) => {
      console.log('Contact fournisseur:', supplier.name)
    }

    // Watchers
    watch(() => route.query, () => {
      initializeFromRoute()
      loadProducts()
    }, { deep: true })

    // Lifecycle
    onMounted(() => {
      loadCategoryMap()
      initializeFromRoute()
      loadProducts()
    })

    return {
      // États
      loading,
      error,
      viewMode,
      sortBy,
      currentPage,
      totalPages,
      totalProducts,
      formattedProducts,
      appliedFilters,
      activeFilters,
      hasActiveFilters,
      showFilterDialog,

      // Computed
      visiblePages,
      bannerBackgroundImage,

      // Méthodes
      handleImageError,
      loadProducts,
      handleFilterChange,
      handleMobileFilterChange,
      applyMobileFilters,
      removeFilter,
      clearAllFilters,
      formatPriceRange,
      formatRange,
      getCategoryNames,
      getPageTitle,
      handleSortChange,
      goToPage,
      goToProduct,
      toggleFavorite,
      addToCart,
      contactSupplier,
      adaptProductData,
      handleChatClick,
      handleContactClick
    }
  }
}
</script>

<style scoped>
.product-list-page {
  background: #f5f5f5;
  min-height: 100vh;
  font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
}

.page-header {
  background: #fff;
  border-bottom: 1px solid #e8e8e8;
  padding: 12px 0;
}

.container {
  max-width: 1500px;
  min-width: 1200px;
  margin: 0 auto;
  padding: 0 16px;
}

.breadcrumb {
  font-size: 13px;
  color: #666;
}

.breadcrumb-link {
  color: #ff6b35;
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

.page-title-section {
  background-size: cover;
  background-position: center;
  background-repeat: no-repeat;
  background-attachment: fixed;
  color: white;
  padding: 60px 0;
  text-align: center;
  position: relative;
}

.page-title-section::before {
  content: '';
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: rgba(0, 0, 0, 0.3);
  z-index: 1;
}

.page-title-section .container {
  position: relative;
  z-index: 2;
}

.main-title {
  font-size: 36px;
  font-weight: 700;
  margin: 0 0 12px 0;
  text-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
}

.main-subtitle {
  font-size: 18px;
  margin: 0;
  opacity: 0.9;
}

.main-content {
  padding: 16px 0;
  background: #f5f5f5;
}

.content-wrapper {
  display: flex;
  gap: 16px;
  background: #f5f5f5;
  border-radius: 4px;
  overflow: hidden;
}

.products-area {
  flex: 1;
  padding: 16px;
  background: #f5f5f5fa;
}

.active-filters {
  background: #fff;
  border: 1px solid #e8e8e8;
  border-radius: 8px;
  padding: 16px;
  margin-bottom: 20px;
}

.filters-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 12px;
}

.filters-title {
  font-weight: 600;
  color: #333;
  font-size: 14px;
}

.clear-all-btn {
  background: none;
  border: none;
  color: #fe9700;
  cursor: pointer;
  font-size: 12px;
  text-decoration: underline;
}

.clear-all-btn:hover {
  color: #ff8c61;
}

.filters-list {
  display: flex;
  flex-wrap: wrap;
  gap: 8px;
}

.filter-tag {
  display: flex;
  align-items: center;
  background: #f0f0f0;
  border: 1px solid #d9d9d9;
  border-radius: 16px;
  padding: 4px 12px;
  font-size: 12px;
  color: #333;
}

.filter-tag span {
  margin-right: 6px;
}

.remove-filter {
  background: none;
  border: none;
  color: #999;
  cursor: pointer;
  font-size: 16px;
  line-height: 1;
  padding: 0;
  width: 16px;
  height: 16px;
  display: flex;
  align-items: center;
  justify-content: center;
  border-radius: 50%;
}

.remove-filter:hover {
  background: #e6e6e6;
  color: #666;
}

.loading-container {
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  padding: 60px 20px;
  color: #666;
}

.loading-spinner {
  width: 40px;
  height: 40px;
  border: 3px solid #f3f3f3;
  border-top: 3px solid #ff6b35;
  border-radius: 50%;
  animation: spin 1s linear infinite;
  margin-bottom: 16px;
}

@keyframes spin {
  0% { transform: rotate(0deg); }
  100% { transform: rotate(360deg); }
}

.error-container {
  text-align: center;
  padding: 60px 20px;
  color: #666;
}

.retry-btn {
  padding: 8px 16px;
  background: #ff6b35;
  color: white;
  border: none;
  border-radius: 4px;
  cursor: pointer;
  margin-top: 16px;
}

.no-results {
  text-align: center;
  padding: 60px 20px;
  color: #666;
}

.no-results-icon {
  width: 64px;
  height: 64px;
  margin: 0 auto 16px;
  color: #ccc;
}

.clear-filters-btn {
  padding: 8px 16px;
  background: #ff6b35;
  color: white;
  border: none;
  border-radius: 4px;
  cursor: pointer;
  margin-top: 16px;
}

.toolbar {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 20px;
  padding-bottom: 16px;
  border-bottom: 1px solid #f0f0f0;
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

.results-count {
  font-size: 14px;
  color: #666;
}

.toolbar-right {
  display: flex;
  align-items: center;
  gap: 16px;
}

.sort-select {
  padding: 8px 12px;
  border: 1px solid #d9d9d9;
  border-radius: 4px;
  font-size: 14px;
  background: #fff;
}

.view-toggle {
  display: flex;
  border: 1px solid #d9d9d9;
  border-radius: 4px;
  overflow: hidden;
}

.view-btn {
  padding: 8px 12px;
  border: none;
  background: #fff;
  cursor: pointer;
  color: #666;
  transition: all 0.3s ease;
}

.view-btn:hover {
  background: #f5f5f5;
}

.view-btn.active {
  background: #ff6b35;
  color: #fff;
}

.products-container {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(260px, 1fr));
  gap: 16px;
  margin-bottom: 32px;
}

.products-container.list-view {
  grid-template-columns: 1fr;
}

.product-card {
  background: white;
  border-radius: 12px;
  overflow: hidden;
  transition: all 0.3s ease;
  cursor: pointer;
  position: relative;
  height: 100%;
  display: flex;
  flex-direction: column;
}

.product-card:hover {
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
  transform: translateY(-2px);
}

.product-card.list-card {
  flex-direction: row;
  padding: 16px;
  align-items: flex-start;
}

.product-card.list-card :deep(.alibaba-image-area) {
  width: 200px;
  height: 200px;
  margin-right: 16px;
  flex-shrink: 0;
}

.product-card.list-card :deep(.alibaba-card-content) {
  flex: 1;
  height: auto;
}

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
  border-color: #ff6b35;
  color: #ff6b35;
}

.page-btn.active {
  background: #ff4757;
  color: #fff;
  border-color: #ff4757;
}

.page-btn:disabled {
  opacity: 0.5;
  cursor: not-allowed;
}

.prev-btn,
.next-btn {
  display: flex;
  align-items: center;
  justify-content: center;
}

.page-ellipsis {
  padding: 8px 4px;
  color: #666;
}

.mobile-only {
  display: none;
}

.desktop-only {
  display: grid;
}

.mobile-filter-bar {
  display: flex;
  justify-content: space-between;
  align-items: center;
  background: white;
  padding: 12px;
  margin-bottom: 16px;
  border-radius: 8px;
  border-bottom: 1px solid #8080803b;
}

.mobile-filter-btn {
  display: flex;
  align-items: center;
  gap: 8px;
  background: #f5f5f5;
  border: 1px solid #e0e0e0;
  border-radius: 20px;
  padding: 8px 16px;
  font-size: 14px;
  font-weight: 500;
  color: #333;
}

.mobile-sort-dropdown {
  position: relative;
}

.mobile-sort-select {
  padding: 8px 12px;
  border: 1px solid #e0e0e0;
  border-radius: 20px;
  background: #f5f5f5;
  font-size: 14px;
  color: #333;
  appearance: none;
  padding-right: 28px;
  background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='16' height='16' viewBox='0 0 24 24' fill='none' stroke='%23333' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpolyline points='6 9 12 15 18 9'%3E%3C/polyline%3E%3C/svg%3E");
  background-repeat: no-repeat;
  background-position: right 10px center;
}

.filter-sidebar-overlay {
  position: fixed;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: rgba(0, 0, 0, 0.5);
  z-index: 1000;
  display: flex;
}

.filter-sidebar-panel {
  width: 85%;
  max-width: 320px;
  background: white;
  height: 100%;
  overflow-y: auto;
  animation: slideIn 0.3s ease-out;
}

@keyframes slideIn {
  from {
    transform: translateX(-100%);
  }
  to {
    transform: translateX(0);
  }
}

.filter-sidebar-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 16px;
  border-bottom: 1px solid #f0f0f0;
  position: sticky;
  top: 0;
  background: white;
  z-index: 10;
}

.filter-sidebar-title {
  font-size: 18px;
  font-weight: 600;
  margin: 0;
}

.filter-sidebar-close {
  background: none;
  border: none;
  font-size: 24px;
  color: #666;
  cursor: pointer;
  width: 32px;
  height: 32px;
  display: flex;
  align-items: center;
  justify-content: center;
  border-radius: 50%;
}

.filter-sidebar-close:active {
  background: #f5f5f5;
}

.filter-sidebar-content {
  padding-bottom: 80px;
}

.filter-sidebar-actions {
  position: fixed;
  bottom: 0;
  left: 0;
  width: 85%;
  max-width: 320px;
  display: flex;
  gap: 12px;
  padding: 16px;
  background: white;
  border-top: 1px solid #f0f0f0;
  z-index: 10;
}

.filter-sidebar-reset,
.filter-sidebar-apply {
  flex: 1;
  padding: 12px;
  border-radius: 20px;
  font-size: 14px;
  font-weight: 600;
  cursor: pointer;
}

.filter-sidebar-reset {
  background: white;
  border: 1px solid #d9d9d9;
  color: #666;
}

.filter-sidebar-apply {
  background: #ff6b35;
  border: none;
  color: white;
}

@media (min-width: 769px) {
  .desktop-hidden {
    display: none !important;
  }
}

@media (max-width: 768px) {
  .container {
    max-width: 100%;
    min-width: auto;
    padding: 0 12px;
  }

  .mobile-only {
    display: block;
  }

  .desktop-only {
    display: none;
  }

  .content-wrapper {
    flex-direction: column;
  }

  .page-header {
    padding: 8px 0;
  }

  .breadcrumb {
    font-size: 11px;
    white-space: nowrap;
    overflow-x: auto;
    padding-bottom: 4px;
  }

  .page-title-section {
    padding: 20px 0;
  }

  .main-title {
    font-size: 20px;
    margin-bottom: 8px;
  }

  .main-subtitle {
    font-size: 13px;
  }

  .products-area {
    padding: 0px;
  }

  .products-container {
    grid-template-columns: repeat(2, 1fr);
    gap: 12px;
  }
}

@media (max-width: 480px) {
  .products-container {
    grid-template-columns: repeat(2, 1fr);
    gap: 12px;
  }
}

.search-product-card {
  transition: all 0.3s ease;
}

.search-product-card.list-card {
  display: flex;
  flex-direction: row;
  padding: 16px;
  align-items: flex-start;
}

.search-product-card.list-card :deep(.alibaba-image-area) {
  width: 200px;
  height: 200px;
  margin-right: 16px;
  flex-shrink: 0;
}

.search-product-card.list-card :deep(.alibaba-card-content) {
  flex: 1;
  height: auto;
}

.mobile-products-grid {
  display: grid;
  grid-template-columns: repeat(2, 1fr);
  gap: 12px;
  margin-bottom: 32px;
}

@media (max-width: 768px) {
  .mobile-products-grid {
    grid-template-columns: repeat(2, 1fr);
    gap: 12px;
  }
}

@media (max-width: 480px) {
  .mobile-products-grid {
    grid-template-columns: repeat(2, 1fr);
    gap: 10px;
  }
}
</style>
