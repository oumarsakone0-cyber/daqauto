<template>
  <div class="filter-sidebar">
    <!-- Sous-catégories -->
    <div class="filter-section">
      <h3 class="filter-title">Sous-catégories</h3>
      <div class="filter-content">
        <div v-if="isLoading" class="loading-indicator">
          <div class="spinner"></div>
          <span>Chargement...</span>
        </div>
        <div v-else-if="subcategories.length === 0" class="empty-state">
          Aucune sous-catégorie disponible
        </div>
        <template v-else>
          <label class="checkbox-item" v-for="subcategory in subcategories" :key="subcategory.id">
            <input 
              type="checkbox" 
              :value="subcategory.id" 
              v-model="filters.subcategories"
              @change="applyFilters"
            >
            <span class="checkmark"></span>
            <span class="checkbox-label">{{ subcategory.name }}</span>
            <span class="checkbox-count">({{ subcategory.count || 0 }})</span>
          </label>
        </template>
      </div>
    </div>

    <!-- Prix -->
    <div class="filter-section">
      <h3 class="filter-title">Prix</h3>
      <div class="filter-content">
        <!-- Tri par prix -->
        <div class="price-sort-buttons">
          <button 
            @click="setSortPrice('ASC')" 
            class="sort-btn"
            :class="{ active: filters.sortBy === 'unit_price' && filters.sortOrder === 'ASC' }"
          >
            Prix croissant
          </button>
          <button 
            @click="setSortPrice('DESC')" 
            class="sort-btn"
            :class="{ active: filters.sortBy === 'unit_price' && filters.sortOrder === 'DESC' }"
          >
            Prix décroissant
          </button>
        </div>

        <!-- Plage de prix -->
        <div class="price-range">
          <div class="price-inputs">
            <input 
              type="number" 
              placeholder="Min" 
              v-model="filters.minPrice" 
              class="price-input"
              @input="debounceApplyFilters"
            >
            <span class="price-separator">-</span>
            <input 
              type="number" 
              placeholder="Max" 
              v-model="filters.maxPrice" 
              class="price-input"
              @input="debounceApplyFilters"
            >
          </div>
        </div>

        <!-- Plages prédéfinies -->
        <div class="price-presets">
          <button 
            v-for="preset in pricePresets" 
            :key="preset.label"
            @click="setPricePreset(preset)"
            class="preset-btn"
            :class="{ active: isPricePresetActive(preset) }"
          >
            {{ preset.label }}
          </button>
        </div>
      </div>
    </div>

    <!-- Options d'achat -->
    <div class="filter-section">
      <h3 class="filter-title">Options d'achat</h3>
      <div class="filter-content">
        <label class="checkbox-item">
          <input 
            type="checkbox" 
            v-model="filters.wholesaleAvailable"
            @change="applyFilters"
          >
          <span class="checkmark"></span>
          <span class="checkbox-label">Prix de gros disponible</span>
        </label>
        <label class="checkbox-item">
          <input 
            type="checkbox" 
            v-model="filters.freeShipping"
            @change="applyFilters"
          >
          <span class="checkmark"></span>
          <span class="checkbox-label">Livraison gratuite</span>
        </label>
        <label class="checkbox-item">
          <input 
            type="checkbox" 
            v-model="filters.inStock"
            @change="applyFilters"
          >
          <span class="checkmark"></span>
          <span class="checkbox-label">En stock</span>
        </label>
        <label class="checkbox-item">
          <input 
            type="checkbox" 
            v-model="filters.newProducts"
            @change="applyFilters"
          >
          <span class="checkmark"></span>
          <span class="checkbox-label">Nouveaux produits</span>
        </label>
      </div>
    </div>

    <!-- Note et avis -->
    <div class="filter-section">
      <h3 class="filter-title">Note minimum</h3>
      <div class="filter-content">
        <div class="rating-filter">
          <label class="radio-item" v-for="rating in ratingOptions" :key="rating.value">
            <input 
              type="radio" 
              name="minRating" 
              :value="rating.value" 
              v-model="filters.minRating"
              @change="applyFilters"
            >
            <span class="radio-mark"></span>
            <div class="rating-display">
              <div class="stars">
                <span v-for="i in 5" :key="i" class="star" :class="{ filled: i <= rating.value }">★</span>
              </div>
              <span class="rating-text">{{ rating.label }}</span>
            </div>
          </label>
        </div>
      </div>
    </div>

    <!-- Marchés -->
    <div class="filter-section">
      <h3 class="filter-title">Marchés</h3>
      <div class="filter-content">
        <div v-if="markets.length === 0" class="empty-state">
          Aucun marché disponible
        </div>
        <template v-else>
          <label class="checkbox-item" v-for="market in markets" :key="market.id">
            <input 
              type="checkbox" 
              :value="market.id" 
              v-model="filters.markets"
              @change="applyFilters"
            >
            <span class="checkmark"></span>
            <span class="checkbox-label">{{ market.name }}</span>
            <span class="checkbox-count">({{ market.count || 0 }})</span>
          </label>
        </template>
      </div>
    </div>

    <!-- Fournisseur -->
    <div class="filter-section">
      <h3 class="filter-title">Fournisseur</h3>
      <div class="filter-content">
        <label class="checkbox-item">
          <input 
            type="checkbox" 
            v-model="filters.verifiedSupplier"
            @change="applyFilters"
          >
          <span class="checkmark"></span>
          <span class="checkbox-label">Fournisseur vérifié</span>
        </label>
        <label class="checkbox-item">
          <input 
            type="checkbox" 
            v-model="filters.experiencedSupplier"
            @change="applyFilters"
          >
          <span class="checkmark"></span>
          <span class="checkbox-label">+ de 2 ans d'expérience</span>
        </label>
        <label class="checkbox-item">
          <input 
            type="checkbox" 
            v-model="filters.topSupplier"
            @change="applyFilters"
          >
          <span class="checkmark"></span>
          <span class="checkbox-label">Fournisseur top</span>
        </label>
      </div>
    </div>

    <!-- Quantité minimum -->
    <div class="filter-section">
      <h3 class="filter-title">Quantité minimum</h3>
      <div class="filter-content">
        <div class="quantity-filter">
          <label class="radio-item">
            <input 
              type="radio" 
              name="minQuantity" 
              value="" 
              v-model="filters.minQuantity"
              @change="applyFilters"
            >
            <span class="radio-mark"></span>
            <span class="radio-label">Toutes quantités</span>
          </label>
          <label class="radio-item">
            <input 
              type="radio" 
              name="minQuantity" 
              value="1" 
              v-model="filters.minQuantity"
              @change="applyFilters"
            >
            <span class="radio-mark"></span>
            <span class="radio-label">1 pièce minimum</span>
          </label>
          <label class="radio-item">
            <input 
              type="radio" 
              name="minQuantity" 
              value="10" 
              v-model="filters.minQuantity"
              @change="applyFilters"
            >
            <span class="radio-mark"></span>
            <span class="radio-label">10+ pièces</span>
          </label>
          <label class="radio-item">
            <input 
              type="radio" 
              name="minQuantity" 
              value="50" 
              v-model="filters.minQuantity"
              @change="applyFilters"
            >
            <span class="radio-mark"></span>
            <span class="radio-label">50+ pièces</span>
          </label>
        </div>
      </div>
    </div>

    <!-- Actions -->
    <div class="filter-section">
      <div class="filter-content">
        <button class="reset-btn" @click="resetFilters">
          <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <path d="M3 12a9 9 0 0 1 9-9 9.75 9.75 0 0 1 6.74 2.74L21 8"></path>
            <path d="M21 3v5h-5"></path>
            <path d="M21 12a9 9 0 0 1-9 9 9.75 9.75 0 0 1-6.74-2.74L3 16"></path>
            <path d="M3 21v-5h5"></path>
          </svg>
          Réinitialiser
        </button>
      </div>
    </div>

    <!-- Debug info (à supprimer en production) -->
    <div v-if="showDebug" class="filter-section debug-section">
      <h3 class="filter-title">Debug</h3>
      <div class="filter-content">
        <pre class="debug-info">{{ JSON.stringify(filters, null, 2) }}</pre>
      </div>
    </div>
  </div>
</template>

<script>
import { ref, reactive, onMounted, watch } from 'vue';
import { useRoute, useRouter } from 'vue-router';

export default {
  name: 'FilterSide',
  
  props: {
    initialProducts: {
      type: Array,
      required: false,
      default: () => []
    },
    showDebug: {
      type: Boolean,
      default: false
    }
  },
  
  emits: ['filter-change'],
  
  setup(props, { emit }) {
    const route = useRoute();
    const router = useRouter();
    const isLoading = ref(false);
    
    // Données des filtres
    const subcategories = ref([]);
    const markets = ref([]);

    // Options de prix prédéfinies
    const pricePresets = ref([
      { label: '< 10K', min: 0, max: 10000 },
      { label: '10K - 50K', min: 10000, max: 50000 },
      { label: '50K - 100K', min: 50000, max: 100000 },
      { label: '100K - 500K', min: 100000, max: 500000 },
      { label: '> 500K', min: 500000, max: null }
    ]);

    // Options de note
    const ratingOptions = ref([
      { value: '', label: 'Toutes notes' },
      { value: 4, label: '4+ étoiles' },
      { value: 3, label: '3+ étoiles' },
      { value: 2, label: '2+ étoiles' }
    ]);
    
    // États des filtres
    const filters = reactive({
      subcategories: [],
      markets: [], // Remplace locations
      minPrice: '',
      maxPrice: '',
      wholesaleAvailable: false,
      freeShipping: false,
      inStock: false,
      newProducts: false,
      verifiedSupplier: false,
      experiencedSupplier: false,
      topSupplier: false,
      minRating: '',
      minQuantity: '',
      sortBy: 'created_at',
      sortOrder: 'DESC'
    });

    // Debounce timer
    let debounceTimer = null;
    
    // Extraire les filtres à partir des produits
    const extractFiltersFromProducts = (products) => {
      if (!products || products.length === 0) {
        console.log('📊 Aucun produit pour extraire les filtres');
        return;
      }
      
      console.log('📊 Extraction des filtres depuis', products.length, 'produits');
      console.log('📊 Premier produit exemple:', products[0]);
      
      // Extraire les sous-catégories avec les nouvelles variables
      const subcatsMap = new Map();
      products.forEach(product => {
        // Utiliser les nouvelles variables demandées
        let subcatId = product.sub_subcategory_id || product.subcategory_id || product.subcategoryId;
        let subcatName = product.sub_subcategory_name || product.subcategory_name || product.subcategoryName;
        
        // Fallback sur d'autres propriétés si nécessaire
        if (!subcatName && product.category) {
          subcatName = product.category.sub_subcategory_name || product.category.subcategory_name || product.category.name;
          subcatId = product.category.sub_subcategory_id || product.category.subcategory_id || product.category.id;
        }
        
        if (subcatId && subcatName) {
          const key = `${subcatId}`;
          if (!subcatsMap.has(key)) {
            subcatsMap.set(key, {
              id: subcatId,
              name: subcatName,
              count: 1
            });
          } else {
            subcatsMap.get(key).count++;
          }
        }
      });
      
      subcategories.value = Array.from(subcatsMap.values());
      console.log('📂 Sous-catégories extraites:', subcategories.value);

      // Extraire les marchés avec la variable boutique_market
      const marketsMap = new Map();
      products.forEach(product => {
        // Utiliser la variable boutique_market demandée
        const marketName = product.boutique_market || 
                          product.market || 
                          product.boutique?.market ||
                          product.supplier?.market;
        
        if (marketName) {
          const key = marketName.toLowerCase().trim();
          if (!marketsMap.has(key)) {
            marketsMap.set(key, {
              id: key,
              name: marketName,
              count: 1
            });
          } else {
            marketsMap.get(key).count++;
          }
        }
      });

      markets.value = Array.from(marketsMap.values());
      console.log('🏪 Marchés extraits:', markets.value);
    };
    
    // Initialiser les filtres à partir de l'URL
    const initFiltersFromQuery = () => {
      const query = route.query;
      console.log('🔗 Initialisation depuis URL:', query);
      
      if (query.subcategories) {
        filters.subcategories = query.subcategories.split(',').map(id => {
          const parsed = parseInt(id);
          return isNaN(parsed) ? id : parsed;
        });
      }
      if (query.markets) {
        filters.markets = query.markets.split(',');
      }
      if (query.minPrice) filters.minPrice = query.minPrice;
      if (query.maxPrice) filters.maxPrice = query.maxPrice;
      if (query.wholesaleAvailable) filters.wholesaleAvailable = query.wholesaleAvailable === 'true';
      if (query.freeShipping) filters.freeShipping = query.freeShipping === 'true';
      if (query.inStock) filters.inStock = query.inStock === 'true';
      if (query.newProducts) filters.newProducts = query.newProducts === 'true';
      if (query.verifiedSupplier) filters.verifiedSupplier = query.verifiedSupplier === 'true';
      if (query.experiencedSupplier) filters.experiencedSupplier = query.experiencedSupplier === 'true';
      if (query.topSupplier) filters.topSupplier = query.topSupplier === 'true';
      if (query.minRating) filters.minRating = query.minRating;
      if (query.minQuantity) filters.minQuantity = query.minQuantity;
      if (query.sortBy) filters.sortBy = query.sortBy;
      if (query.sortOrder) filters.sortOrder = query.sortOrder;
      
      console.log('🔗 Filtres initialisés:', filters);
    };

    // Définir le tri par prix
    const setSortPrice = (order) => {
      console.log('💰 Définition tri prix:', order);
      filters.sortBy = 'unit_price';
      filters.sortOrder = order;
      applyFilters();
    };

    // Définir une plage de prix prédéfinie
    const setPricePreset = (preset) => {
      console.log('💰 Plage de prix prédéfinie:', preset);
      filters.minPrice = preset.min || '';
      filters.maxPrice = preset.max || '';
      applyFilters();
    };

    // Vérifier si une plage de prix est active
    const isPricePresetActive = (preset) => {
      const minMatch = (preset.min === null || preset.min === '') ? 
        (filters.minPrice === '' || filters.minPrice === null) : 
        Number(filters.minPrice) === Number(preset.min);
        
      const maxMatch = (preset.max === null || preset.max === '') ? 
        (filters.maxPrice === '' || filters.maxPrice === null) : 
        Number(filters.maxPrice) === Number(preset.max);
        
      return minMatch && maxMatch;
    };

    // Appliquer les filtres avec debounce
    const debounceApplyFilters = () => {
      if (debounceTimer) {
        clearTimeout(debounceTimer);
      }
      debounceTimer = setTimeout(() => {
        applyFilters();
      }, 500);
    };
    
    // Appliquer les filtres
    const applyFilters = () => {
      console.log('🔄 Application des filtres...');
      console.log('🔄 État actuel des filtres:', filters);
      
      const filterParams = {};
      
      // Sous-catégories
      if (filters.subcategories.length > 0) {
        filterParams.subcategories = filters.subcategories.join(',');
      }
      
      // Marchés (remplace locations)
      if (filters.markets.length > 0) {
        filterParams.markets = filters.markets.join(',');
      }
      
      // Prix - Convertir en nombres et valider
      if (filters.minPrice && !isNaN(filters.minPrice)) {
        filterParams.minPrice = Number(filters.minPrice);
      }
      if (filters.maxPrice && !isNaN(filters.maxPrice)) {
        filterParams.maxPrice = Number(filters.maxPrice);
      }
      
      // Options d'achat
      if (filters.wholesaleAvailable) filterParams.wholesaleAvailable = true;
      if (filters.freeShipping) filterParams.freeShipping = true;
      if (filters.inStock) filterParams.inStock = true;
      if (filters.newProducts) filterParams.newProducts = true;
      
      // Fournisseur
      if (filters.verifiedSupplier) filterParams.verifiedSupplier = true;
      if (filters.experiencedSupplier) filterParams.experiencedSupplier = true;
      if (filters.topSupplier) filterParams.topSupplier = true;
      
      // Note minimum
      if (filters.minRating && filters.minRating !== '') {
        filterParams.minRating = Number(filters.minRating);
      }
      
      // Quantité minimum
      if (filters.minQuantity && filters.minQuantity !== '') {
        filterParams.minQuantity = Number(filters.minQuantity);
      }
      
      // Tri
      filterParams.sortBy = filters.sortBy;
      filterParams.sortOrder = filters.sortOrder;
      
      console.log('🔄 Paramètres de filtres émis:', filterParams);
      
      emit('filter-change', filterParams);
      updateURL(filterParams);
    };
    
    // Mettre à jour l'URL avec les filtres
    const updateURL = (filterParams) => {
      const existingQuery = { ...route.query };
      
      // Supprimer les anciens paramètres de filtre
      const filterKeys = ['subcategories', 'markets', 'minPrice', 'maxPrice', 'wholesaleAvailable', 
                         'freeShipping', 'inStock', 'newProducts', 'verifiedSupplier', 
                         'experiencedSupplier', 'topSupplier', 'minRating', 'minQuantity', 
                         'sortBy', 'sortOrder'];
      
      filterKeys.forEach(key => {
        delete existingQuery[key];
      });
      
      // Ajouter les nouveaux paramètres (seulement ceux qui ont des valeurs)
      Object.keys(filterParams).forEach(key => {
        if (filterParams[key] !== undefined && filterParams[key] !== '' && filterParams[key] !== false) {
          existingQuery[key] = filterParams[key];
        }
      });
      
      console.log('🔗 Mise à jour URL:', existingQuery);
      
      router.push({
        path: route.path,
        query: existingQuery
      }).catch(err => {
        // Ignorer les erreurs de navigation redondante
        if (err.name !== 'NavigationDuplicated') {
          console.error('Erreur navigation:', err);
        }
      });
    };
    
    // Réinitialiser les filtres
    const resetFilters = () => {
      console.log('🔄 Réinitialisation des filtres');
      
      Object.keys(filters).forEach(key => {
        if (Array.isArray(filters[key])) {
          filters[key] = [];
        } else if (typeof filters[key] === 'boolean') {
          filters[key] = false;
        } else {
          filters[key] = '';
        }
      });
      
      filters.sortBy = 'created_at';
      filters.sortOrder = 'DESC';
      
      applyFilters();
    };
    
    // Surveiller les produits initiaux
    watch(() => props.initialProducts, (newProducts) => {
      console.log('👀 Nouveaux produits reçus:', newProducts?.length || 0);
      if (newProducts && newProducts.length > 0) {
        extractFiltersFromProducts(newProducts);
      }
    }, { deep: true });
    
    // Lifecycle hooks
    onMounted(() => {
      console.log('🚀 Montage du composant FilterSide');
      initFiltersFromQuery();
      if (props.initialProducts && props.initialProducts.length > 0) {
        extractFiltersFromProducts(props.initialProducts);
      }
    });
    
    return {
      isLoading,
      subcategories,
      markets, // Remplace locations
      pricePresets,
      ratingOptions,
      filters,
      setSortPrice,
      setPricePreset,
      isPricePresetActive,
      applyFilters,
      debounceApplyFilters,
      resetFilters
    };
  }
}
</script>

<style scoped>
.filter-sidebar {
  width: 240px;
  background: #fff;
  padding: 0;
  border-radius: 8px;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.06);
}

.filter-section {
  border-bottom: 1px solid #f0f0f0;
  padding: 16px 0;
}

.filter-section:last-child {
  border-bottom: none;
}

.filter-title {
  font-size: 14px;
  font-weight: 600;
  color: #333;
  margin: 0 0 12px 16px;
}

.filter-content {
  padding: 0 16px;
}

/* Debug section */
.debug-section {
  background: #f8f9fa;
  border: 1px solid #e9ecef;
  border-radius: 4px;
  margin: 8px;
}

.debug-info {
  font-size: 10px;
  color: #666;
  background: #fff;
  padding: 8px;
  border-radius: 4px;
  overflow-x: auto;
  max-height: 200px;
}

/* Checkboxes et radios */
.checkbox-item, .radio-item {
  display: flex;
  align-items: center;
  padding: 6px 0;
  cursor: pointer;
  font-size: 13px;
  transition: background-color 0.2s ease;
}

.checkbox-item:hover, .radio-item:hover {
  background-color: #f8f9fa;
  margin: 0 -8px;
  padding-left: 8px;
  padding-right: 8px;
  border-radius: 4px;
}

.checkbox-item input[type="checkbox"],
.radio-item input[type="radio"] {
  display: none;
}

.checkmark, .radio-mark {
  width: 14px;
  height: 14px;
  border: 1px solid #d9d9d9;
  margin-right: 8px;
  position: relative;
  background: #fff;
  transition: all 0.2s ease;
}

.checkmark {
  border-radius: 2px;
}

.radio-mark {
  border-radius: 50%;
}

.checkbox-item input[type="checkbox"]:checked + .checkmark {
  background: #fe9700;
  border-color: #fe9700;
}

.checkbox-item input[type="checkbox"]:checked + .checkmark::after {
  content: '';
  position: absolute;
  left: 4px;
  top: 1px;
  width: 4px;
  height: 8px;
  border: solid white;
  border-width: 0 2px 2px 0;
  transform: rotate(45deg);
}

.radio-item input[type="radio"]:checked + .radio-mark::after {
  content: '';
  position: absolute;
  left: 3px;
  top: 3px;
  width: 6px;
  height: 6px;
  border-radius: 50%;
  background: #fe9700;
}

.checkbox-label, .radio-label {
  flex: 1;
  color: #333;
}

.checkbox-count {
  font-size: 12px;
  color: #999;
  margin-left: 4px;
}

/* Prix */
.price-sort-buttons {
  display: flex;
  gap: 4px;
  margin-bottom: 12px;
}

.sort-btn {
  flex: 1;
  background: #f5f5f5;
  border: 1px solid #e0e0e0;
  border-radius: 4px;
  padding: 6px 8px;
  font-size: 11px;
  cursor: pointer;
  color: #666;
  transition: all 0.2s ease;
}

.sort-btn:hover {
  border-color: #fe9700;
  color: #fe9700;
}

.sort-btn.active {
  background: #fe9700;
  border-color: #fe9700;
  color: white;
}

.price-range {
  margin-bottom: 12px;
}

.price-inputs {
  display: inline-block;
  align-items: center;
  gap: 8px;
}

.price-input {
  flex: 1;
  color: black;
  padding: 6px 8px;
  margin-bottom: 0.75rem;
  border: 1px solid #d9d9d9;
  border-radius: 4px;
  font-size: 12px;
  transition: border-color 0.2s ease;
}

.price-input:focus {
  outline: none;
  border-color: #fe9700;
}

.price-separator {
  color: #999;
  font-size: 12px;
}

.price-presets {
  display: flex;
  flex-wrap: wrap;
  gap: 4px;
}

.preset-btn {
  background: #f8f9fa;
  border: 1px solid #e9ecef;
  border-radius: 12px;
  padding: 4px 8px;
  font-size: 11px;
  cursor: pointer;
  color: #666;
  transition: all 0.2s ease;
}

.preset-btn:hover {
  border-color: #fe9700;
  color: #fe9700;
}

.preset-btn.active {
  background: #fe9700;
  border-color: #fe9700;
  color: white;
}

/* Rating */
.rating-display {
  display: flex;
  align-items: center;
  gap: 6px;
  flex: 1;
}

.stars {
  display: flex;
  gap: 1px;
}

.star {
  color: #e0e0e0;
  font-size: 12px;
}

.star.filled {
  color: #ffc107;
}

.rating-text {
  font-size: 12px;
  color: #666;
}

/* Actions */
.reset-btn {
  background: #f5f5f5;
  color: #666;
  border: 1px solid #e0e0e0;
  padding: 8px 16px;
  border-radius: 4px;
  font-size: 13px;
  cursor: pointer;
  width: 100%;
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 6px;
  transition: all 0.2s ease;
}

.reset-btn:hover {
  border-color: #ff4d4f;
  color: #ff4d4f;
}

/* Loading et états vides */
.loading-indicator {
  display: flex;
  align-items: center;
  gap: 8px;
  font-size: 13px;
  color: #999;
  padding: 10px 0;
}

.spinner {
  width: 14px;
  height: 14px;
  border: 2px solid #f3f3f3;
  border-top: 2px solid #fe9700;
  border-radius: 50%;
  animation: spin 0.8s linear infinite;
}

@keyframes spin {
  0% { transform: rotate(0deg); }
  100% { transform: rotate(360deg); }
}

.empty-state {
  color: #999;
  font-size: 13px;
  padding: 10px 0;
  text-align: center;
  font-style: italic;
}

/* Mobile */
@media (max-width: 768px) {
  .filter-sidebar {
    width: 100%;
    box-shadow: none;
  }

  .filter-section {
    padding: 12px 0;
  }

  .filter-title {
    margin: 0 0 10px 12px;
    font-size: 15px;
  }

  .filter-content {
    padding: 0 12px;
  }

  .checkbox-item, .radio-item {
    padding: 8px 0;
  }

  .price-sort-buttons {
    flex-direction: column;
    gap: 6px;
  }

  .sort-btn {
    padding: 8px;
    font-size: 12px;
  }

  .reset-btn {
    padding: 10px;
    border-radius: 20px;
  }
}
</style>
