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
          <span class="breadcrumb-current">{{ appliedFilters.filter_name || getCategoryTitle }}</span>
        </div>
      </div>
    </div>

    <!-- Titre principal -->
    <div class="page-title-section" :style="{ backgroundImage: bannerBackgroundImage }">
      <div class="container">
        <h1 class="main-title">{{ getPageTitle() }}</h1>
        <p class="main-subtitle">{{ getSubtitle() }}</p>
      </div>
    </div>

    <!-- Contenu principal -->
    <div class="main-content">
      <div class="container">
        <div class="content-wrapper">
          <!-- Sidebar des filtres - visible uniquement sur desktop -->
          <div class="desktop-only">
            <FilterSide @filter-change="handleFilterChange" />
          </div>

          <!-- Zone des produits -->
          <div class="products-area">
            <!-- Mobile Filter & Sort Bar -->
            <div class="mobile-filter-bar mobile-only desktop-hidden">
              <button class="mobile-filter-btn input-style" @click="showFilterDialog = true">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                  <path d="M22 3H2l8 9.46V19l4 2v-8.54L22 3z"></path>
                </svg>
                <span>Filtres</span>
                <span v-if="activeFiltersCount > 0" class="mobile-filter-count">{{ activeFiltersCount }}</span>
              </button>
              
              <div class="mobile-sort-dropdown">
                <select v-model="sortBy" @change="handleSortChange" class="mobile-sort-select input-style">
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
                  <FilterSide @filter-change="handleMobileFilterChange" />
                </div>
                
                <div class="filter-sidebar-actions">
                  <button class="filter-sidebar-reset btn-gray" @click="clearAllFilters">Réinitialiser</button>
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
                  <!-- Type de véhicule -->
                  <div v-if="activeFilters.vehicleType" class="filter-tag">
                    <span>Type: {{ activeFilters.vehicleType === 'truck' ? 'Camions' : 'Voitures' }}</span>
                    <button @click="removeFilter('vehicleType')" class="remove-filter">×</button>
                  </div>

                  <!-- Sous-catégories -->
                  <div v-if="activeFilters.subcategories" class="filter-tag">
                    <span>Sous-catégories: {{ activeFilters.subcategories }}</span>
                    <button @click="removeFilter('subcategories')" class="remove-filter">×</button>
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
                  <!-- Model de véhicules -->
                  <div v-if="activeFilters.carModel" class="filter-tag">
                    <span>Models: {{ activeFilters.carModel }}</span>
                    <button @click="removeFilter('carModel')" class="remove-filter">×</button>
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
                  
                  <!-- Type de traction -->
                  <div v-if="activeFilters.driveType" class="filter-tag">
                    <span>Traction: {{ activeFilters.driveType }}</span>
                    <button @click="removeFilter('driveType')" class="remove-filter">×</button>
                  </div>
                  
                  <!-- Année -->
                  <div v-if="activeFilters.vehicleYearMin || activeFilters.vehicleYearMax" class="filter-tag">
                    <span>Année: {{ formatRange(activeFilters.vehicleYearMin, activeFilters.vehicleYearMax) }}</span>
                    <button @click="removeFilter('year')" class="remove-filter">×</button>
                  </div>
                  
                  <!-- Kilométrage -->
                  <div v-if="activeFilters.vehicleMileageMin || activeFilters.vehicleMileageMax" class="filter-tag">
                    <span>Kilométrage: {{ formatRange(activeFilters.vehicleMileageMin, activeFilters.vehicleMileageMax) }} km</span>
                    <button @click="removeFilter('mileage')" class="remove-filter">×</button>
                  </div>

                  <!-- Couleur extérieure (voitures) -->
                  <div v-if="activeFilters.carExteriorColor" class="filter-tag">
                    <span>Couleur ext.: {{ activeFilters.carExteriorColor }}</span>
                    <button @click="removeFilter('carExteriorColor')" class="remove-filter">×</button>
                  </div>

                  <!-- Couleur intérieure (voitures) -->
                  <div v-if="activeFilters.carInteriorColor" class="filter-tag">
                    <span>Couleur int.: {{ activeFilters.carInteriorColor }}</span>
                    <button @click="removeFilter('carInteriorColor')" class="remove-filter">×</button>
                  </div>

                  <!-- Type de carrosserie (voitures) -->
                  <div v-if="activeFilters.carBodyType" class="filter-tag">
                    <span>Carrosserie: {{ activeFilters.carBodyType }}</span>
                    <button @click="removeFilter('carBodyType')" class="remove-filter">×</button>
                  </div>
                </div>
              </div>

              <!-- Barre d'outils - visible uniquement sur desktop -->
              <div class="toolbar desktop-only">
                <div class="toolbar-left">
                  <h2 class="section-title">{{ getCategoryTitle }}</h2>
                  <span class="results-count">{{ totalProducts.toLocaleString() }} produits trouvés</span>
                </div>
                
                <div class="toolbar-right">
                  <!-- Tri -->
                  <div class="sort-dropdown">
                    <select v-model="sortBy" @change="handleSortChange" class="sort-select input-style">
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
                <div class="pagination" v-if="totalPages > 1" style="position: absolute;">
                  <button class="page-btn prev-btn" :disabled="currentPage === 1" @click="goToPage(currentPage - 1)">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                      <polyline points="15,18 9,12 15,6"></polyline>
                    </svg>
                  </button>
                  
                  <template v-for="page in visiblePages" :key="page">
                    <button 
                      v-if="page !== '...'"
                      class="page-btn"
                      :class="{ active: page === currentPage }"
                      @click="goToPage(page)"
                    >
                      {{ page }}
                    </button>
                    <span v-else class="page-ellipsis">...</span>
                  </template>
                  
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
import FilterSide from '../menu/FilterSide.vue'
import { productsApi, categoriesApi, boutiqueUtils } from '../../services/api.js'
import ProductCard from '../home/ProductCard.vue'
import { formatPrice } from '../../services/formatPrice'

export default {
  name: 'ResultatView',
  components: {
    FilterSide,
    ProductCard
  },
  setup() {
    const route = useRoute()
    const router = useRouter()

    // Constants - CATEGORY IDs
    const TRUCK_CATEGORY_ID = '13'
    const CAR_CATEGORY_ID = '20'

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

    // Paramètres de filtrage depuis l'URL
    const categoryId = ref(null)
    const subcategoryId = ref(null)
    const subSubcategoryId = ref(null)
    const productSelect = ref('')

    // État du panneau latéral de filtres
    const showFilterDialog = ref(false)
    const mobileFilters = ref({})

    // Computed
    const hasActiveFilters = computed(() => {
      return Object.keys(activeFilters.value).some(key => {
        const value = activeFilters.value[key]
        return value !== null && value !== undefined && value !== '' && key !== 'category' && key !== 'vehicleType'
      })
    })

    const activeFiltersCount = computed(() => {
      let count = 0
      const f = activeFilters.value
      
      if (f.subcategories) count++
      if (f.minPrice || f.maxPrice) count++
      if (f.vehicleMake) count++
      if (f.carModel) count++
      if (f.vehicleCondition) count++
      if (f.fuelType) count++
      if (f.transmissionType) count++
      if (f.driveType) count++
      if (f.vehicleYearMin || f.vehicleYearMax) count++
      if (f.vehicleMileageMin || f.vehicleMileageMax) count++
      if (f.carExteriorColor) count++
      if (f.carInteriorColor) count++
      if (f.carBodyType) count++
      
      return count
    })

    const getCategoryTitle = computed(() => {
      const catId = activeFilters.value.category || route.query.category
      
      if (catId === TRUCK_CATEGORY_ID) {
        return 'Camions (Trucks)'
      } else if (catId === CAR_CATEGORY_ID) {
        return 'Voitures (Cars)'
      }
      
      return 'Tous les véhicules'
    })

    const getSubtitle = () => {
      const catId = activeFilters.value.category || route.query.category
      
      if (catId === TRUCK_CATEGORY_ID) {
        return 'Découvrez notre sélection de camions lourds de qualité'
      } else if (catId === CAR_CATEGORY_ID) {
        return 'Explorez notre sélection de voitures de qualité'
      }
      
      return 'Découvrez notre sélection de véhicules de qualité'
    }

    // Construire les options de filtrage pour l'API
    const buildFilterOptions = () => {
      const options = {
        page: currentPage.value,
        limit: itemsPerPage.value,
        sort: sortBy.value.includes('_desc') ? sortBy.value.replace('_desc', '') : sortBy.value,
        order: sortBy.value.includes('_desc') ? 'DESC' : 'ASC'
      }

      const userId = boutiqueUtils.getCurrentUserId()
      if (userId) {
        options.user_id = userId
      }

      // IMPORTANT: Toujours utiliser la catégorie depuis activeFilters (qui vient du FilterSide)
      // Cela garantit que le switch entre Truck et Car fonctionne correctement
      if (activeFilters.value.category) {
        options.category = activeFilters.value.category
      } else if (categoryId.value) {
        options.category = categoryId.value
      }

      // Autres paramètres de base depuis l'URL
      if (subcategoryId.value) options.subcategory = subcategoryId.value
      if (subSubcategoryId.value) options.sub_subcategory = subSubcategoryId.value
      if (productSelect.value) options.product_select = productSelect.value

      // Sous-catégories (depuis FilterSide)
      if (activeFilters.value.subcategories) {
        options.subcategories = activeFilters.value.subcategories
      }

      // Prix
      if (activeFilters.value.minPrice) options.min_price = activeFilters.value.minPrice
      if (activeFilters.value.maxPrice) options.max_price = activeFilters.value.maxPrice

      // Marques de véhicules
      if (activeFilters.value.vehicleMake) {
        options.vehicle_make = activeFilters.value.vehicleMake
      }
      if (activeFilters.value.carModel) {
        options.car_model = activeFilters.value.carModel
      }

      // État du véhicule
      if (activeFilters.value.vehicleCondition) {
        options.vehicle_condition = activeFilters.value.vehicleCondition
      }

      // Type de carburant
      if (activeFilters.value.fuelType) {
        options.fuel_type = activeFilters.value.fuelType
      }

      // Type de transmission
      if (activeFilters.value.transmissionType) {
        options.transmission_type = activeFilters.value.transmissionType
      }

      // Type de traction
      if (activeFilters.value.driveType) {
        options.drive_type = activeFilters.value.driveType
      }

      // Année du véhicule
      if (activeFilters.value.vehicleYearMin) options.vehicle_year_min = activeFilters.value.vehicleYearMin
      if (activeFilters.value.vehicleYearMax) options.vehicle_year_max = activeFilters.value.vehicleYearMax

      // Kilométrage
      if (activeFilters.value.vehicleMileageMin) options.vehicle_mileage_min = activeFilters.value.vehicleMileageMin
      if (activeFilters.value.vehicleMileageMax) options.vehicle_mileage_max = activeFilters.value.vehicleMileageMax

      // Filtres spécifiques aux voitures
      if (activeFilters.value.carExteriorColor) {
        options.car_exterior_color = activeFilters.value.carExteriorColor
      }
      if (activeFilters.value.carInteriorColor) {
        options.car_interior_color = activeFilters.value.carInteriorColor
      }
      if (activeFilters.value.carBodyType) {
        options.car_body_type = activeFilters.value.carBodyType
      }

      console.log('[ResultatView] Options de requête construites:', options)
      return options
    }

    // Chargement des produits depuis l'API
    const loadProducts = async () => {
      loading.value = true
      error.value = null

      try {
        const options = buildFilterOptions()
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

          console.log('[ResultatView] Produits chargés:', formattedProducts.value.length)
        } else {
          throw new Error('Format de réponse invalide')
        }
      } catch (err) {
        console.error('[ResultatView] Erreur:', err)
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
      console.log('[ResultatView] Filtres reçus:', filters)
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
        case 'vehicleType':
          delete newFilters.vehicleType
          // Remettre la catégorie par défaut (trucks)
          newFilters.category = TRUCK_CATEGORY_ID
          break
        case 'subcategories':
          delete newFilters.subcategories
          break
        case 'price':
          delete newFilters.minPrice
          delete newFilters.maxPrice
          break
        case 'vehicleMake':
          delete newFilters.vehicleMake
          break
        case 'carModel':
          delete newFilters.carModel
          break
        case 'vehicleCondition':
          delete newFilters.vehicleCondition
          break
        case 'fuelType':
          delete newFilters.fuelType
          break
        case 'transmissionType':
          delete newFilters.transmissionType
          break
        case 'driveType':
          delete newFilters.driveType
          break
        case 'year':
          delete newFilters.vehicleYearMin
          delete newFilters.vehicleYearMax
          break
        case 'mileage':
          delete newFilters.vehicleMileageMin
          delete newFilters.vehicleMileageMax
          break
        case 'carExteriorColor':
          delete newFilters.carExteriorColor
          break
        case 'carInteriorColor':
          delete newFilters.carInteriorColor
          break
        case 'carBodyType':
          delete newFilters.carBodyType
          break
      }

      activeFilters.value = newFilters
      
      // Mettre à jour l'URL
      updateURLFromFilters(newFilters)
      
      loadProducts()
    }

    // Effacer tous les filtres
    const clearAllFilters = () => {
      // Garder seulement la catégorie actuelle
      const currentCategory = activeFilters.value.category || TRUCK_CATEGORY_ID
      activeFilters.value = { category: currentCategory }
      mobileFilters.value = {}
      currentPage.value = 1
      
      // Nettoyer l'URL
      router.replace({ 
        path: route.path, 
        query: { category: currentCategory, page: '1' } 
      })
      
      loadProducts()
      
      if (showFilterDialog.value) {
        showFilterDialog.value = false
      }
    }

    // Mettre à jour l'URL depuis les filtres actifs
    const updateURLFromFilters = (filters) => {
      const query = {}
      
      if (filters.category) query.category = filters.category
      if (filters.minPrice) query.min_price = filters.minPrice
      if (filters.maxPrice) query.max_price = filters.maxPrice
      if (filters.vehicleMake) query.vehicle_make = filters.vehicleMake
      if (filters.carModel) query.car_model = filters.carModel
      if (filters.vehicleCondition) query.vehicle_condition = filters.vehicleCondition
      if (filters.fuelType) query.fuel_type = filters.fuelType
      if (filters.transmissionType) query.transmission_type = filters.transmissionType
      if (filters.driveType) query.drive_type = filters.driveType
      if (filters.vehicleYearMin) query.vehicle_year_min = filters.vehicleYearMin
      if (filters.vehicleYearMax) query.vehicle_year_max = filters.vehicleYearMax
      if (filters.vehicleMileageMin) query.vehicle_mileage_min = filters.vehicleMileageMin
      if (filters.vehicleMileageMax) query.vehicle_mileage_max = filters.vehicleMileageMax
      if (filters.subcategories) query.subcategories = filters.subcategories
      if (filters.carExteriorColor) query.car_exterior_color = filters.carExteriorColor
      if (filters.carInteriorColor) query.car_interior_color = filters.carInteriorColor
      if (filters.carBodyType) query.car_body_type = filters.carBodyType
      
      query.page = '1'
      router.replace({ path: route.path, query })
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

    const getPageTitle = () => {
      if (route.query.search) {
        return `Résultats pour "${route.query.search}"`
      } else if (appliedFilters.value.filter_name) {
        return appliedFilters.value.filter_name
      }
      return getCategoryTitle.value
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

      // Initialiser les filtres actifs depuis l'URL
      const filters = {}
      
      if (route.query.category) filters.category = route.query.category
      if (route.query.subcategories) filters.subcategories = route.query.subcategories
      if (route.query.min_price) filters.minPrice = parseFloat(route.query.min_price)
      if (route.query.max_price) filters.maxPrice = parseFloat(route.query.max_price)
      if (route.query.vehicle_make) filters.vehicleMake = route.query.vehicle_make
      if (route.query.car_model) filters.carModel = route.query.car_model
      if (route.query.vehicle_condition) filters.vehicleCondition = route.query.vehicle_condition
      if (route.query.fuel_type) filters.fuelType = route.query.fuel_type
      if (route.query.transmission_type) filters.transmissionType = route.query.transmission_type
      if (route.query.drive_type) filters.driveType = route.query.drive_type
      if (route.query.vehicle_year_min) filters.vehicleYearMin = parseInt(route.query.vehicle_year_min)
      if (route.query.vehicle_year_max) filters.vehicleYearMax = parseInt(route.query.vehicle_year_max)
      if (route.query.vehicle_mileage_min) filters.vehicleMileageMin = parseInt(route.query.vehicle_mileage_min)
      if (route.query.vehicle_mileage_max) filters.vehicleMileageMax = parseInt(route.query.vehicle_mileage_max)
      if (route.query.car_exterior_color) filters.carExteriorColor = route.query.car_exterior_color
      if (route.query.car_interior_color) filters.carInteriorColor = route.query.car_interior_color
      if (route.query.car_body_type) filters.carBodyType = route.query.car_body_type

      // Déterminer le type de véhicule depuis la catégorie
      if (filters.category === TRUCK_CATEGORY_ID) {
        filters.vehicleType = 'truck'
      } else if (filters.category === CAR_CATEGORY_ID) {
        filters.vehicleType = 'car'
      }
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
      const catId = activeFilters.value.category || route.query.category
      
      const categoryImages = {
        [TRUCK_CATEGORY_ID]: 'linear-gradient(rgba(0, 0, 0, 0.5), rgba(0, 0, 0, 0.5)), url("https://images.unsplash.com/photo-1601584115197-04ecc0da31d7?ixlib=rb-4.0.3&auto=format&fit=crop&w=2000&q=80")',
        [CAR_CATEGORY_ID]: 'linear-gradient(rgba(0, 0, 0, 0.5), rgba(0, 0, 0, 0.5)), url("https://images.unsplash.com/photo-1494976388531-d1058494cdd8?ixlib=rb-4.0.3&auto=format&fit=crop&w=2000&q=80")'
      }

      const defaultImage = 'linear-gradient(rgba(0, 0, 0, 0.5), rgba(0, 0, 0, 0.5)), url("https://images.unsplash.com/photo-1518709268805-4e9042af2176?ixlib=rb-4.0.3&auto=format&fit=crop&w=2000&q=80")'

      return categoryImages[catId] || defaultImage
    })

    // Adapter les données du produit pour le composant ProductCard
    const adaptProductData = (product) => {
      return {
        id: product.id,
        name: product.name,
        slug: product.slug,
        unit_price: product.unitPrice || product.unit_price,
        wholesale_price: product.wholesalePrice || product.wholesale_price,
        wholesale_min_qty: product.minQuantity || product.wholesale_min_qty,
        primary_image: product.images?.[0] || product.primary_image,
        images: product.images || [],
        views: product.reviews || product.views,
        market: product.supplier?.country?.toLowerCase() || product.boutique_market || 'local',
        boutique_name: product.supplier?.name || product.boutique_name || product.shopName,
        boutique_verified: product.supplier?.verified || product.boutique_verified,
        stock: product.stock,
        rating: product.rating,
        sales_count: product.sales_count,
        free_shipping: product.freeShipping || product.free_shipping,
        category_name: product.category || product.category_name,
        vehicle_make: product.vehicle_make || product.vehicleMake,
        vehicle_model: product.vehicle_model || product.vehicleModel,
        vehicle_year: product.vehicle_year || product.vehicleYear,
        car_make: product.car_make,
        car_model: product.car_model,
        car_year: product.car_year,
        car_mileage: product.car_mileage,
        car_condition: product.car_condition
      }
    }

    // Charger les catégories
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

    // Surveiller les changements de route
    watch(() => route.query, () => {
      initializeFromRoute()
      loadProducts()
    }, { deep: true })

    onMounted(() => {
      loadCategoryMap()
      initializeFromRoute()
      loadProducts()
      // Les produits seront chargés automatiquement quand FilterSide émet filter-change
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
      showFilterDialog,
      
      // Données
      formattedProducts,
      appliedFilters,
      activeFilters,
      
      // Computed
      hasActiveFilters,
      activeFiltersCount,
      getCategoryTitle,
      visiblePages,
      bannerBackgroundImage,
      
      // Méthodes
      loadProducts,
      handleFilterChange,
      handleMobileFilterChange,
      applyMobileFilters,
      removeFilter,
      clearAllFilters,
      formatPriceRange,
      formatRange,
      getPageTitle,
      getSubtitle,
      handleSortChange,
      goToPage,
      goToProduct,
      toggleFavorite,
      addToCart,
      contactSupplier,
      adaptProductData
    }
  }
}
</script>

<style scoped>
/* Container */
.product-list-page {
  min-height: 100vh;
  background: #f5f5f5;
}

.container {
  max-width: 1500px;
  margin: 0 auto;
  padding: 0 20px;
}

/* Page Header */
.page-header {
  background: white;
  padding: 15px 0;
  border-bottom: 1px solid #e5e7eb;
}

.breadcrumb {
  display: flex;
  align-items: center;
  gap: 8px;
  font-size: 14px;
}

.breadcrumb-link {
  color: #6b7280;
  text-decoration: none;
  transition: color 0.2s;
}

.breadcrumb-link:hover {
  color: #fe7900;
}

.breadcrumb-separator {
  color: #d1d5db;
}

.breadcrumb-current {
  color: #111827;
  font-weight: 500;
}

/* Page Title Section */
.page-title-section {
  background-size: cover;
  background-position: center;
  padding: 60px 0;
  text-align: center;
  color: white;
}

.main-title {
  font-size: 36px;
  font-weight: 700;
  margin-bottom: 12px;
}

.main-subtitle {
  font-size: 16px;
  opacity: 0.9;
}

/* Main Content */
.main-content {
  padding: 30px 0;
}

.content-wrapper {
  display: flex;
  gap: 30px;
  align-items: flex-start;
}

/* Desktop filter container - stretches with content */
.desktop-only {
  position: sticky;
  top: 20px;
  flex-shrink: 0;
}

/* Products Area */
.products-area {
  flex: 1;
  min-width: 0;
}

/* Mobile Filter Bar */
.mobile-filter-bar {
  display: flex;
  gap: 12px;
  margin-bottom: 20px;
}

.mobile-filter-btn {
  display: flex;
  align-items: center;
  gap: 8px;
  padding: 12px 16px;
  background: white;
  border: 1px solid #e5e7eb;
  border-radius: 8px;
  font-size: 14px;
  font-weight: 500;
  cursor: pointer;
  transition: all 0.2s;
}

.mobile-filter-btn:hover {
  border-color: #fe7900;
  color: #fe7900;
}

.mobile-filter-count {
  background: #fe7900;
  color: white;
  font-size: 11px;
  padding: 2px 6px;
  border-radius: 10px;
}

.mobile-sort-dropdown {
  flex: 1;
}

.mobile-sort-select {
  width: 100%;
  padding: 12px 16px;
  background: white;
  /* border: 1px solid #e5e7eb; */
  border-radius: 8px;
  font-size: 14px;
  cursor: pointer;
}

/* Filter Sidebar Panel (Mobile) */
.filter-sidebar-overlay {
  position: fixed;
  inset: 0;
  background: rgba(0, 0, 0, 0.5);
  z-index: 1000;
  display: flex;
  justify-content: flex-end;
}

.filter-sidebar-panel {
  width: 340px;
  max-width: 90vw;
  background: white;
  height: 100%;
  display: flex;
  flex-direction: column;
  animation: slideIn 0.3s ease;
}

@keyframes slideIn {
  from {
    transform: translateX(100%);
  }
  to {
    transform: translateX(0);
  }
}

.filter-sidebar-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 20px;
  border-bottom: 1px solid #e5e7eb;
}

.filter-sidebar-title {
  font-size: 18px;
  font-weight: 600;
  margin: 0;
  color: black;
}

.filter-sidebar-close {
  width: 32px;
  height: 30px;
  display: flex;
  align-items: center;
  justify-content: center;
  background: #f3f4f6;
  color: black;
  border: none;
  padding-bottom: 5px;
  border-radius: 8px;
  font-size: 24px;
  cursor: pointer;
}

.filter-sidebar-content {
  flex: 1;
  overflow-y: auto;
  padding: 20px;
}

.filter-sidebar-actions {
  display: flex;
  gap: 12px;
  padding: 20px;
  border-top: 1px solid #e5e7eb;
}

.filter-sidebar-reset {
  flex: 1;
  padding: 14px;
  background: white;
  border: 1px solid #e5e7eb;
  border-radius: 8px;
  font-weight: 500;
  cursor: pointer;
}

.filter-sidebar-apply {
  flex: 1;
  padding: 14px;
  background: #fe7900;
  color: white;
  border: none;
  border-radius: 8px;
  font-weight: 600;
  cursor: pointer;
}

/* Loading */
.loading-container {
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  padding: 60px 20px;
  gap: 16px;
  color: #6b7280;
}

.loading-spinner {
  width: 40px;
  height: 40px;
  border: 3px solid #e5e7eb;
  border-top-color: #fe7900;
  border-radius: 50%;
  animation: spin 1s linear infinite;
}

@keyframes spin {
  to {
    transform: rotate(360deg);
  }
}

/* Error */
.error-container {
  text-align: center;
  padding: 60px 20px;
}

.retry-btn {
  margin-top: 16px;
  padding: 12px 24px;
  background: #fe7900;
  color: white;
  border: none;
  border-radius: 8px;
  font-weight: 600;
  cursor: pointer;
}

/* Active Filters */
.active-filters {
  background: white;
  border-radius: 12px;
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
  font-size: 14px;
  font-weight: 600;
  color: #374151;
}

.clear-all-btn {
  font-size: 13px;
  color: #dc2626;
  background: none;
  border: none;
  cursor: pointer;
  font-weight: 500;
}

.filters-list {
  display: flex;
  flex-wrap: wrap;
  gap: 8px;
}

.filter-tag {
  display: flex;
  align-items: center;
  gap: 6px;
  padding: 6px 12px;
  background: linear-gradient(135deg, #fff7ed, #ffedd5);
  border: 1px solid #fed7aa;
  border-radius: 20px;
  font-size: 13px;
  color: #9a3412;
}

.remove-filter {
  background: none;
  border: none;
  font-size: 16px;
  cursor: pointer;
  color: #9a3412;
  line-height: 1;
}

/* Toolbar */
.toolbar {
  display: flex;
  justify-content: space-between;
  align-items: center;
  background: white;
  border-radius: 12px;
  padding: 16px 20px;
  margin-bottom: 20px;
}

.toolbar-left {
  display: flex;
  align-items: center;
  gap: 16px;
}

.section-title {
  font-size: 20px;
  font-weight: 600;
  color: #111827;
  margin: 0;
}

.results-count {
  font-size: 14px;
  color: #6b7280;
}

.toolbar-right {
  display: flex;
  align-items: center;
  gap: 16px;
}

.sort-select {
  padding: 10px 16px;
  border-radius: 8px;
  font-size: 14px;
  cursor: pointer;
  background: white;
}

.view-toggle {
  display: flex;
  gap: 4px;
}

.view-btn {
  padding: 10px;
  background: white;
  color: black;
  border: 1px solid #e5e7eb;
  border-radius: 8px;
  cursor: pointer;
  transition: all 0.2s;
}

.view-btn:hover,
.view-btn.active {
  background: #fe7900;
  border-color: #fe7900;
  color: white;
}

/* No Results */
.no-results {
  text-align: center;
  padding: 60px 20px;
  background: white;
  border-radius: 12px;
}

.no-results-icon {
  width: 64px;
  height: 64px;
  color: #d1d5db;
  margin-bottom: 16px;
}

.no-results h3 {
  font-size: 20px;
  font-weight: 600;
  color: #111827;
  margin-bottom: 8px;
}

.no-results p {
  color: #6b7280;
  margin-bottom: 20px;
}

.clear-filters-btn {
  padding: 12px 24px;
  background: #fe7900;
  color: white;
  border: none;
  border-radius: 8px;
  font-weight: 600;
  cursor: pointer;
}

/* Products Container - Desktop: 4 columns */
.products-container {
  display: grid;
  grid-template-columns: repeat(4, 1fr);
  gap: 16px;
  margin-bottom: 32px;
}

.products-container.list-view {
  grid-template-columns: 1fr;
}

/* Mobile Products Grid - 2 columns */
.mobile-products-grid {
  display: grid;
  grid-template-columns: repeat(2, 1fr);
  gap: 12px;
  margin-bottom: 32px;
}

/* Pagination */
.pagination {
  display: flex;
  justify-content: center;
  align-items: center;
  gap: 8px;
  margin-top: 30px;
}

.page-btn {
  min-width: 40px;
  height: 40px;
  display: flex;
  align-items: center;
  justify-content: center;
  background: white;
  border: 1px solid #e5e7eb;
  border-radius: 8px;
  font-size: 14px;
  font-weight: 500;
  cursor: pointer;
  transition: all 0.2s;
}

.page-btn:hover:not(:disabled) {
  border-color: #fe7900;
  color: #fe7900;
}

.page-btn.active {
  background: #fe7900;
  border-color: #fe7900;
  color: white;
}

.page-btn:disabled {
  opacity: 0.5;
  cursor: not-allowed;
}

.page-ellipsis {
  padding: 0 8px;
  color: #6b7280;
}

/* Responsive - Desktop first */
.desktop-only {
  display: block;
}

.mobile-only {
  display: none !important;
}

.desktop-hidden {
  display: none !important;
}

/* Products grid - desktop only, always grid display */
.products-container.desktop-only {
  display: grid !important;
}

@media (max-width: 1200px) {
  .products-container {
    grid-template-columns: repeat(3, 1fr);
  }
}

@media (max-width: 900px) {
  .products-container {
    grid-template-columns: repeat(2, 1fr);
  }
}

/* Tablet and Mobile breakpoint */
@media (max-width: 768px) {
  .content-wrapper {
    flex-direction: column;
  }
  
  /* Hide desktop elements */
  .desktop-only {
    display: none !important;
  }
  
  /* Show mobile elements */
  .mobile-only {
    display: flex !important;
  }
  
  .desktop-hidden {
    display: grid !important;
  }
  
  /* Mobile products grid */
  .mobile-products-grid {
    display: grid !important;
    grid-template-columns: repeat(2, 1fr);
    gap: 12px;
  }
}

@media (max-width: 640px) {
  .main-title {
    font-size: 24px;
  }
  
  .page-title-section {
    padding: 40px 0;
  }
  
  .mobile-products-grid {
    grid-template-columns: repeat(2, 1fr);
    gap: 10px;
  }
}

@media (max-width: 480px) {
  .mobile-products-grid {
    grid-template-columns: repeat(2, 1fr);
    gap: 8px;
  }
}
</style>
