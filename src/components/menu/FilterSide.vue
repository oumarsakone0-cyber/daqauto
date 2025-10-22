<template>
  <div class="filter-sidebar">
    <div class="filter-header">
      <h3 class="filter-title">Filtres</h3>
      <button v-if="hasActiveFilters" @click="resetAllFilters" class="reset-btn">
        Réinitialiser
      </button>
    </div>

    <!-- Sous-catégories -->
    <div class="filter-section">
      <div class="filter-section-header" @click="toggleSection('subcategories')">
        <h4 class="filter-section-title">Sous-catégories</h4>
        <svg 
          class="chevron-icon" 
          :class="{ 'rotated': expandedSections.subcategories }"
          width="16" 
          height="16" 
          viewBox="0 0 24 24" 
          fill="none" 
          stroke="currentColor" 
          stroke-width="2"
        >
          <polyline points="6 9 12 15 18 9"></polyline>
        </svg>
      </div>
      <div v-show="expandedSections.subcategories" class="filter-section-content">
        <label 
          v-for="subcategory in subcategories" 
          :key="subcategory" 
          class="filter-checkbox-label"
        >
          <input 
            type="checkbox" 
            :value="subcategory" 
            v-model="selectedSubcategories"
            @change="emitFilters"
          />
          <span>{{ subcategory }}</span>
        </label>
      </div>
    </div>

    <!-- Marchés / Localisation -->
    <div class="filter-section">
      <div class="filter-section-header" @click="toggleSection('markets')">
        <h4 class="filter-section-title">Marchés</h4>
        <svg 
          class="chevron-icon" 
          :class="{ 'rotated': expandedSections.markets }"
          width="16" 
          height="16" 
          viewBox="0 0 24 24" 
          fill="none" 
          stroke="currentColor" 
          stroke-width="2"
        >
          <polyline points="6 9 12 15 18 9"></polyline>
        </svg>
      </div>
      <div v-show="expandedSections.markets" class="filter-section-content">
        <label 
          v-for="market in markets" 
          :key="market" 
          class="filter-checkbox-label"
        >
          <input 
            type="checkbox" 
            :value="market" 
            v-model="selectedMarkets"
            @change="emitFilters"
          />
          <span>{{ market }}</span>
        </label>
      </div>
    </div>

    <!-- Prix -->
    <div class="filter-section">
      <div class="filter-section-header" @click="toggleSection('price')">
        <h4 class="filter-section-title">Prix (FCFA)</h4>
        <svg 
          class="chevron-icon" 
          :class="{ 'rotated': expandedSections.price }"
          width="16" 
          height="16" 
          viewBox="0 0 24 24" 
          fill="none" 
          stroke="currentColor" 
          stroke-width="2"
        >
          <polyline points="6 9 12 15 18 9"></polyline>
        </svg>
      </div>
      <div v-show="expandedSections.price" class="filter-section-content">
        <!-- Presets de prix -->
        <div class="price-presets">
          <button 
            v-for="preset in pricePresets" 
            :key="preset.label"
            @click="applyPricePreset(preset)"
            class="price-preset-btn"
            :class="{ 'active': isPricePresetActive(preset) }"
          >
            {{ preset.label }}
          </button>
        </div>
        
        <!-- Plage de prix personnalisée -->
        <div class="price-range">
          <div class="price-input-group">
            <label class="price-label">Min</label>
            <input 
              type="number" 
              v-model.number="priceMin" 
              @input="emitFilters"
              placeholder="0"
              class="price-input"
            />
          </div>
          <span class="price-separator">-</span>
          <div class="price-input-group">
            <label class="price-label">Max</label>
            <input 
              type="number" 
              v-model.number="priceMax" 
              @input="emitFilters"
              placeholder="∞"
              class="price-input"
            />
          </div>
        </div>
      </div>
    </div>

    <!-- Marque de véhicule -->
    <div class="filter-section">
      <div class="filter-section-header" @click="toggleSection('vehicleMake')">
        <h4 class="filter-section-title">Marque</h4>
        <svg 
          class="chevron-icon" 
          :class="{ 'rotated': expandedSections.vehicleMake }"
          width="16" 
          height="16" 
          viewBox="0 0 24 24" 
          fill="none" 
          stroke="currentColor" 
          stroke-width="2"
        >
          <polyline points="6 9 12 15 18 9"></polyline>
        </svg>
      </div>
      <div v-show="expandedSections.vehicleMake" class="filter-section-content">
        <div class="search-box">
          <input 
            type="text" 
            v-model="vehicleMakeSearch" 
            placeholder="Rechercher une marque..."
            class="search-input"
          />
        </div>
        <div class="scrollable-list">
          <label 
            v-for="make in filteredVehicleMakes" 
            :key="make" 
            class="filter-checkbox-label"
          >
            <input 
              type="checkbox" 
              :value="make" 
              v-model="selectedVehicleMakes"
              @change="emitFilters"
            />
            <span>{{ make }}</span>
          </label>
        </div>
      </div>
    </div>

    <!-- État du véhicule -->
    <div class="filter-section">
      <div class="filter-section-header" @click="toggleSection('vehicleCondition')">
        <h4 class="filter-section-title">État</h4>
        <svg 
          class="chevron-icon" 
          :class="{ 'rotated': expandedSections.vehicleCondition }"
          width="16" 
          height="16" 
          viewBox="0 0 24 24" 
          fill="none" 
          stroke="currentColor" 
          stroke-width="2"
        >
          <polyline points="6 9 12 15 18 9"></polyline>
        </svg>
      </div>
      <div v-show="expandedSections.vehicleCondition" class="filter-section-content">
        <label 
          v-for="condition in vehicleConditions" 
          :key="condition.value" 
          class="filter-checkbox-label"
        >
          <input 
            type="checkbox" 
            :value="condition.value" 
            v-model="selectedVehicleConditions"
            @change="emitFilters"
          />
          <span>{{ condition.label }}</span>
        </label>
      </div>
    </div>

    <!-- Type de carburant -->
    <div class="filter-section">
      <div class="filter-section-header" @click="toggleSection('fuelType')">
        <h4 class="filter-section-title">Carburant</h4>
        <svg 
          class="chevron-icon" 
          :class="{ 'rotated': expandedSections.fuelType }"
          width="16" 
          height="16" 
          viewBox="0 0 24 24" 
          fill="none" 
          stroke="currentColor" 
          stroke-width="2"
        >
          <polyline points="6 9 12 15 18 9"></polyline>
        </svg>
      </div>
      <div v-show="expandedSections.fuelType" class="filter-section-content">
        <label 
          v-for="fuel in fuelTypes" 
          :key="fuel" 
          class="filter-checkbox-label"
        >
          <input 
            type="checkbox" 
            :value="fuel" 
            v-model="selectedFuelTypes"
            @change="emitFilters"
          />
          <span>{{ fuel }}</span>
        </label>
      </div>
    </div>

    <!-- Type de transmission -->
    <div class="filter-section">
      <div class="filter-section-header" @click="toggleSection('transmission')">
        <h4 class="filter-section-title">Transmission</h4>
        <svg 
          class="chevron-icon" 
          :class="{ 'rotated': expandedSections.transmission }"
          width="16" 
          height="16" 
          viewBox="0 0 24 24" 
          fill="none" 
          stroke="currentColor" 
          stroke-width="2"
        >
          <polyline points="6 9 12 15 18 9"></polyline>
        </svg>
      </div>
      <div v-show="expandedSections.transmission" class="filter-section-content">
        <label 
          v-for="transmission in transmissionTypes" 
          :key="transmission" 
          class="filter-checkbox-label"
        >
          <input 
            type="checkbox" 
            :value="transmission" 
            v-model="selectedTransmissionTypes"
            @change="emitFilters"
          />
          <span>{{ transmission }}</span>
        </label>
      </div>
    </div>

    <!-- Type de traction -->
    <div class="filter-section">
      <div class="filter-section-header" @click="toggleSection('driveType')">
        <h4 class="filter-section-title">Traction</h4>
        <svg 
          class="chevron-icon" 
          :class="{ 'rotated': expandedSections.driveType }"
          width="16" 
          height="16" 
          viewBox="0 0 24 24" 
          fill="none" 
          stroke="currentColor" 
          stroke-width="2"
        >
          <polyline points="6 9 12 15 18 9"></polyline>
        </svg>
      </div>
      <div v-show="expandedSections.driveType" class="filter-section-content">
        <label 
          v-for="drive in driveTypes" 
          :key="drive" 
          class="filter-checkbox-label"
        >
          <input 
            type="checkbox" 
            :value="drive" 
            v-model="selectedDriveTypes"
            @change="emitFilters"
          />
          <span>{{ drive }}</span>
        </label>
      </div>
    </div>

    <!-- Marque du moteur -->
    <div class="filter-section">
      <div class="filter-section-header" @click="toggleSection('engineBrand')">
        <h4 class="filter-section-title">Marque du moteur</h4>
        <svg 
          class="chevron-icon" 
          :class="{ 'rotated': expandedSections.engineBrand }"
          width="16" 
          height="16" 
          viewBox="0 0 24 24" 
          fill="none" 
          stroke="currentColor" 
          stroke-width="2"
        >
          <polyline points="6 9 12 15 18 9"></polyline>
        </svg>
      </div>
      <div v-show="expandedSections.engineBrand" class="filter-section-content">
        <div class="scrollable-list">
          <label 
            v-for="brand in engineBrands" 
            :key="brand" 
            class="filter-checkbox-label"
          >
            <input 
              type="checkbox" 
              :value="brand" 
              v-model="selectedEngineBrands"
              @change="emitFilters"
            />
            <span>{{ brand }}</span>
          </label>
        </div>
      </div>
    </div>

    <!-- Année -->
    <div class="filter-section">
      <div class="filter-section-header" @click="toggleSection('year')">
        <h4 class="filter-section-title">Année</h4>
        <svg 
          class="chevron-icon" 
          :class="{ 'rotated': expandedSections.year }"
          width="16" 
          height="16" 
          viewBox="0 0 24 24" 
          fill="none" 
          stroke="currentColor" 
          stroke-width="2"
        >
          <polyline points="6 9 12 15 18 9"></polyline>
        </svg>
      </div>
      <div v-show="expandedSections.year" class="filter-section-content">
        <div class="year-range">
          <div class="year-input-group">
            <label class="year-label">De</label>
            <input 
              type="number" 
              v-model.number="yearMin" 
              @input="emitFilters"
              :min="1990"
              :max="currentYear"
              placeholder="1990"
              class="year-input"
            />
          </div>
          <span class="year-separator">-</span>
          <div class="year-input-group">
            <label class="year-label">À</label>
            <input 
              type="number" 
              v-model.number="yearMax" 
              @input="emitFilters"
              :min="1990"
              :max="currentYear"
              :placeholder="currentYear.toString()"
              class="year-input"
            />
          </div>
        </div>
      </div>
    </div>

    <!-- Capacité de charge -->
    <div class="filter-section">
      <div class="filter-section-header" @click="toggleSection('payload')">
        <h4 class="filter-section-title">Capacité de charge (tonnes)</h4>
        <svg 
          class="chevron-icon" 
          :class="{ 'rotated': expandedSections.payload }"
          width="16" 
          height="16" 
          viewBox="0 0 24 24" 
          fill="none" 
          stroke="currentColor" 
          stroke-width="2"
        >
          <polyline points="6 9 12 15 18 9"></polyline>
        </svg>
      </div>
      <div v-show="expandedSections.payload" class="filter-section-content">
        <div class="payload-range">
          <div class="payload-input-group">
            <label class="payload-label">Min</label>
            <input 
              type="number" 
              v-model.number="payloadMin" 
              @input="emitFilters"
              placeholder="0"
              step="0.5"
              class="payload-input"
            />
          </div>
          <span class="payload-separator">-</span>
          <div class="payload-input-group">
            <label class="payload-label">Max</label>
            <input 
              type="number" 
              v-model.number="payloadMax" 
              @input="emitFilters"
              placeholder="∞"
              step="0.5"
              class="payload-input"
            />
          </div>
        </div>
      </div>
    </div>

    <!-- GVW (Poids total en charge) -->
    <div class="filter-section">
      <div class="filter-section-header" @click="toggleSection('gvw')">
        <h4 class="filter-section-title">GVW (tonnes)</h4>
        <svg 
          class="chevron-icon" 
          :class="{ 'rotated': expandedSections.gvw }"
          width="16" 
          height="16" 
          viewBox="0 0 24 24" 
          fill="none" 
          stroke="currentColor" 
          stroke-width="2"
        >
          <polyline points="6 9 12 15 18 9"></polyline>
        </svg>
      </div>
      <div v-show="expandedSections.gvw" class="filter-section-content">
        <div class="gvw-range">
          <div class="gvw-input-group">
            <label class="gvw-label">Min</label>
            <input 
              type="number" 
              v-model.number="gvwMin" 
              @input="emitFilters"
              placeholder="0"
              step="0.5"
              class="gvw-input"
            />
          </div>
          <span class="gvw-separator">-</span>
          <div class="gvw-input-group">
            <label class="gvw-label">Max</label>
            <input 
              type="number" 
              v-model.number="gvwMax" 
              @input="emitFilters"
              placeholder="∞"
              step="0.5"
              class="gvw-input"
            />
          </div>
        </div>
      </div>
    </div>

    <!-- Note minimum -->
    <div class="filter-section">
      <div class="filter-section-header" @click="toggleSection('rating')">
        <h4 class="filter-section-title">Note minimum</h4>
        <svg 
          class="chevron-icon" 
          :class="{ 'rotated': expandedSections.rating }"
          width="16" 
          height="16" 
          viewBox="0 0 24 24" 
          fill="none" 
          stroke="currentColor" 
          stroke-width="2"
        >
          <polyline points="6 9 12 15 18 9"></polyline>
        </svg>
      </div>
      <div v-show="expandedSections.rating" class="filter-section-content">
        <div class="rating-options">
          <label 
            v-for="rating in [5, 4, 3, 2, 1]" 
            :key="rating" 
            class="rating-option"
          >
            <input 
              type="radio" 
              :value="rating" 
              v-model="minRating"
              @change="emitFilters"
              name="rating"
            />
            <div class="rating-stars">
              <span v-for="star in 5" :key="star" class="star" :class="{ 'filled': star <= rating }">
                ★
              </span>
              <span class="rating-text">{{ rating }}+ étoiles</span>
            </div>
          </label>
        </div>
      </div>
    </div>

    <!-- Options supplémentaires -->
    <div class="filter-section">
      <div class="filter-section-header" @click="toggleSection('options')">
        <h4 class="filter-section-title">Options</h4>
        <svg 
          class="chevron-icon" 
          :class="{ 'rotated': expandedSections.options }"
          width="16" 
          height="16" 
          viewBox="0 0 24 24" 
          fill="none" 
          stroke="currentColor" 
          stroke-width="2"
        >
          <polyline points="6 9 12 15 18 9"></polyline>
        </svg>
      </div>
      <div v-show="expandedSections.options" class="filter-section-content">
        <label class="filter-checkbox-label">
          <input 
            type="checkbox" 
            v-model="freeShipping"
            @change="emitFilters"
          />
          <span>Livraison gratuite</span>
        </label>
        <label class="filter-checkbox-label">
          <input 
            type="checkbox" 
            v-model="inStock"
            @change="emitFilters"
          />
          <span>En stock</span>
        </label>
        <label class="filter-checkbox-label">
          <input 
            type="checkbox" 
            v-model="verifiedSupplier"
            @change="emitFilters"
          />
          <span>Fournisseur vérifié</span>
        </label>
      </div>
    </div>
  </div>
</template>

<script>
import { ref, computed } from 'vue'

export default {
  name: 'FilterSide',
  emits: ['filter-change'],
  setup(props, { emit }) {
    // Année actuelle
    const currentYear = new Date().getFullYear()

    // États des sections (ouvertes/fermées)
    const expandedSections = ref({
      subcategories: true,
      markets: true,
      price: true,
      vehicleMake: true,
      vehicleCondition: true,
      fuelType: false,
      transmission: false,
      driveType: false,
      engineBrand: false,
      year: false,
      payload: false,
      gvw: false,
      rating: false,
      options: true
    })

    // Filtres - Sous-catégories
    const subcategories = ref([
      'Cargo Truck',
      'Dump Truck',
      'Tractor Truck',
      'Concrete Mixer Truck',
      'Tanker Truck',
      'Refrigerated Truck',
      'Flatbed Truck',
      'Box Truck'
    ])
    const selectedSubcategories = ref([])

    // Filtres - Marchés
    const markets = ref([
      'Abidjan',
      'Bouaké',
      'Daloa',
      'Yamoussoukro',
      'San-Pédro',
      'Korhogo',
      'Man',
      'Gagnoa'
    ])
    const selectedMarkets = ref([])

    // Filtres - Prix
    const priceMin = ref(null)
    const priceMax = ref(null)
    const pricePresets = ref([
      { label: '< 5M', min: null, max: 5000000 },
      { label: '5M - 10M', min: 5000000, max: 10000000 },
      { label: '10M - 20M', min: 10000000, max: 20000000 },
      { label: '20M - 50M', min: 20000000, max: 50000000 },
      { label: '> 50M', min: 50000000, max: null }
    ])

    // Filtres - Marque de véhicule
    const vehicleMakes = ref([
      'Shacman',
      'Sinotruk',
      'FAW',
      'Dongfeng',
      'Foton',
      'JAC',
      'Howo',
      'Beiben',
      'Isuzu',
      'Mercedes-Benz',
      'Volvo',
      'Scania',
      'MAN',
      'DAF',
      'Iveco'
    ])
    const selectedVehicleMakes = ref([])
    const vehicleMakeSearch = ref('')

    // Filtres - État du véhicule
    const vehicleConditions = ref([
      { value: 'new', label: 'Neuf' },
      { value: 'used', label: 'Occasion' },
      { value: 'refurbished', label: 'Reconditionné' }
    ])
    const selectedVehicleConditions = ref([])

    // Filtres - Type de carburant
    const fuelTypes = ref([
      'Diesel',
      'Essence',
      'Électrique',
      'Hybride',
      'GNV (Gaz Naturel)',
      'GPL'
    ])
    const selectedFuelTypes = ref([])

    // Filtres - Type de transmission
    const transmissionTypes = ref([
      'Manuelle',
      'Automatique',
      'Semi-automatique'
    ])
    const selectedTransmissionTypes = ref([])

    // Filtres - Type de traction
    const driveTypes = ref([
      '4x2',
      '4x4',
      '6x2',
      '6x4',
      '6x6',
      '8x4',
      '8x8'
    ])
    const selectedDriveTypes = ref([])

    // Filtres - Marque du moteur
    const engineBrands = ref([
      'Weichai',
      'Cummins',
      'Deutz',
      'Yuchai',
      'Sinotruk',
      'Mercedes-Benz',
      'Volvo',
      'Scania',
      'MAN',
      'DAF'
    ])
    const selectedEngineBrands = ref([])

    // Filtres - Année
    const yearMin = ref(null)
    const yearMax = ref(null)

    // Filtres - Capacité de charge
    const payloadMin = ref(null)
    const payloadMax = ref(null)

    // Filtres - GVW
    const gvwMin = ref(null)
    const gvwMax = ref(null)

    // Filtres - Note minimum
    const minRating = ref(null)

    // Filtres - Options
    const freeShipping = ref(false)
    const inStock = ref(false)
    const verifiedSupplier = ref(false)

    // Computed - Marques filtrées par recherche
    const filteredVehicleMakes = computed(() => {
      if (!vehicleMakeSearch.value) {
        return vehicleMakes.value
      }
      const search = vehicleMakeSearch.value.toLowerCase()
      return vehicleMakes.value.filter(make => 
        make.toLowerCase().includes(search)
      )
    })

    // Computed - Vérifier si des filtres sont actifs
    const hasActiveFilters = computed(() => {
      return selectedSubcategories.value.length > 0 ||
             selectedMarkets.value.length > 0 ||
             priceMin.value !== null ||
             priceMax.value !== null ||
             selectedVehicleMakes.value.length > 0 ||
             selectedVehicleConditions.value.length > 0 ||
             selectedFuelTypes.value.length > 0 ||
             selectedTransmissionTypes.value.length > 0 ||
             selectedDriveTypes.value.length > 0 ||
             selectedEngineBrands.value.length > 0 ||
             yearMin.value !== null ||
             yearMax.value !== null ||
             payloadMin.value !== null ||
             payloadMax.value !== null ||
             gvwMin.value !== null ||
             gvwMax.value !== null ||
             minRating.value !== null ||
             freeShipping.value ||
             inStock.value ||
             verifiedSupplier.value
    })

    // Méthodes
    const toggleSection = (section) => {
      expandedSections.value[section] = !expandedSections.value[section]
    }

    const applyPricePreset = (preset) => {
      priceMin.value = preset.min
      priceMax.value = preset.max
      emitFilters()
    }

    const isPricePresetActive = (preset) => {
      return priceMin.value === preset.min && priceMax.value === preset.max
    }

    const emitFilters = () => {
      const filters = {}

      // Sous-catégories (comma-separated pour le backend)
      if (selectedSubcategories.value.length > 0) {
        filters.subcategories = selectedSubcategories.value.join(',')
      }

      // Marchés (comma-separated pour le backend)
      if (selectedMarkets.value.length > 0) {
        filters.boutiqueMarket = selectedMarkets.value.join(',')
      }

      // Prix
      if (priceMin.value !== null && priceMin.value !== '') {
        filters.minPrice = priceMin.value
      }
      if (priceMax.value !== null && priceMax.value !== '') {
        filters.maxPrice = priceMax.value
      }

      // Marque de véhicule (comma-separated pour le backend)
      if (selectedVehicleMakes.value.length > 0) {
        filters.vehicleMake = selectedVehicleMakes.value.join(',')
      }

      // État du véhicule (single value pour le backend)
      if (selectedVehicleConditions.value.length > 0) {
        filters.vehicleCondition = selectedVehicleConditions.value[0]
      }

      // Type de carburant (comma-separated pour le backend)
      if (selectedFuelTypes.value.length > 0) {
        filters.fuelType = selectedFuelTypes.value.join(',')
      }

      // Type de transmission (comma-separated pour le backend)
      if (selectedTransmissionTypes.value.length > 0) {
        filters.transmissionType = selectedTransmissionTypes.value.join(',')
      }

      // Type de traction (comma-separated pour le backend)
      if (selectedDriveTypes.value.length > 0) {
        filters.driveType = selectedDriveTypes.value.join(',')
      }

      // Marque du moteur (comma-separated pour le backend)
      if (selectedEngineBrands.value.length > 0) {
        filters.engineBrand = selectedEngineBrands.value.join(',')
      }

      // Année
      if (yearMin.value !== null && yearMin.value !== '') {
        filters.vehicleYearMin = yearMin.value
      }
      if (yearMax.value !== null && yearMax.value !== '') {
        filters.vehicleYearMax = yearMax.value
      }

      // Capacité de charge
      if (payloadMin.value !== null && payloadMin.value !== '') {
        filters.payloadCapacityMin = payloadMin.value
      }
      if (payloadMax.value !== null && payloadMax.value !== '') {
        filters.payloadCapacityMax = payloadMax.value
      }

      // GVW
      if (gvwMin.value !== null && gvwMin.value !== '') {
        filters.gvwMin = gvwMin.value
      }
      if (gvwMax.value !== null && gvwMax.value !== '') {
        filters.gvwMax = gvwMax.value
      }

      // Note minimum
      if (minRating.value !== null) {
        filters.minRating = minRating.value
      }

      // Options
      if (freeShipping.value) {
        filters.freeShipping = true
      }
      if (inStock.value) {
        filters.stock = true
      }
      if (verifiedSupplier.value) {
        filters.boutiqueVerified = true
      }

      console.log('🔄 Émission des filtres:', filters)
      emit('filter-change', filters)
    }

    const resetAllFilters = () => {
      // Réinitialiser tous les filtres
      selectedSubcategories.value = []
      selectedMarkets.value = []
      priceMin.value = null
      priceMax.value = null
      selectedVehicleMakes.value = []
      selectedVehicleConditions.value = []
      selectedFuelTypes.value = []
      selectedTransmissionTypes.value = []
      selectedDriveTypes.value = []
      selectedEngineBrands.value = []
      yearMin.value = null
      yearMax.value = null
      payloadMin.value = null
      payloadMax.value = null
      gvwMin.value = null
      gvwMax.value = null
      minRating.value = null
      freeShipping.value = false
      inStock.value = false
      verifiedSupplier.value = false
      vehicleMakeSearch.value = ''

      emitFilters()
    }

    return {
      // États
      currentYear,
      expandedSections,
      
      // Données des filtres
      subcategories,
      selectedSubcategories,
      markets,
      selectedMarkets,
      priceMin,
      priceMax,
      pricePresets,
      vehicleMakes,
      selectedVehicleMakes,
      vehicleMakeSearch,
      vehicleConditions,
      selectedVehicleConditions,
      fuelTypes,
      selectedFuelTypes,
      transmissionTypes,
      selectedTransmissionTypes,
      driveTypes,
      selectedDriveTypes,
      engineBrands,
      selectedEngineBrands,
      yearMin,
      yearMax,
      payloadMin,
      payloadMax,
      gvwMin,
      gvwMax,
      minRating,
      freeShipping,
      inStock,
      verifiedSupplier,

      // Computed
      filteredVehicleMakes,
      hasActiveFilters,

      // Méthodes
      toggleSection,
      applyPricePreset,
      isPricePresetActive,
      emitFilters,
      resetAllFilters
    }
  }
}
</script>

<style scoped>
.filter-sidebar {
  background: #fff;
  border-radius: 8px;
  padding: 16px;
  width: 280px;
  overflow-y: auto;
  position: sticky;
  top: 20px;
}

.filter-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 20px;
  padding-bottom: 12px;
  border-bottom: 2px solid #f0f0f0;
}

.filter-title {
  font-size: 18px;
  font-weight: 700;
  color: #333;
  margin: 0;
}

.reset-btn {
  background: none;
  border: none;
  color: #ff6b35;
  font-size: 13px;
  font-weight: 600;
  cursor: pointer;
  text-decoration: underline;
}

.reset-btn:hover {
  color: #ff8c61;
}

.filter-section {
  margin-bottom: 16px;
  border-bottom: 1px solid #f0f0f0;
  padding-bottom: 12px;
}

.filter-section:last-child {
  border-bottom: none;
}

.filter-section-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  cursor: pointer;
  padding: 8px 0;
  user-select: none;
}

.filter-section-header:hover {
  background: #f9f9f9;
}

.filter-section-title {
  font-size: 14px;
  font-weight: 600;
  color: #333;
  margin: 0;
}

.chevron-icon {
  transition: transform 0.3s ease;
  color: #666;
}

.chevron-icon.rotated {
  transform: rotate(180deg);
}

.filter-section-content {
  padding: 12px 0 0 0;
  animation: slideDown 0.3s ease;
}

@keyframes slideDown {
  from {
    opacity: 0;
    transform: translateY(-10px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

.filter-checkbox-label {
  display: flex;
  align-items: center;
  padding: 6px 0;
  cursor: pointer;
  font-size: 13px;
  color: #555;
}

.filter-checkbox-label:hover {
  color: #ff6b35;
}

.filter-checkbox-label input[type="checkbox"],
.filter-checkbox-label input[type="radio"] {
  margin-right: 8px;
  cursor: pointer;
  width: 16px;
  height: 16px;
}

.search-box {
  margin-bottom: 12px;
}

.search-input {
  width: 100%;
  padding: 8px 12px;
  border: 1px solid #d9d9d9;
  border-radius: 4px;
  font-size: 13px;
}

.search-input:focus {
  outline: none;
  border-color: #ff6b35;
}

.scrollable-list {
  max-height: 200px;
  overflow-y: auto;
}

.scrollable-list::-webkit-scrollbar {
  width: 6px;
}

.scrollable-list::-webkit-scrollbar-track {
  background: #f1f1f1;
  border-radius: 3px;
}

.scrollable-list::-webkit-scrollbar-thumb {
  background: #ccc;
  border-radius: 3px;
}

.scrollable-list::-webkit-scrollbar-thumb:hover {
  background: #999;
}

/* Prix */
.price-presets {
  display: flex;
  flex-wrap: wrap;
  gap: 8px;
  margin-bottom: 12px;
}

.price-preset-btn {
  padding: 6px 12px;
  border: 1px solid #d9d9d9;
  background: #fff;
  border-radius: 16px;
  font-size: 12px;
  cursor: pointer;
  transition: all 0.3s ease;
}

.price-preset-btn:hover {
  border-color: #ff6b35;
  color: #ff6b35;
}

.price-preset-btn.active {
  background: #ff6b35;
  color: #fff;
  border-color: #ff6b35;
}

.price-range,
.year-range,
.payload-range,
.gvw-range {
  display: flex;
  align-items: center;
  gap: 8px;
}

.price-input-group,
.year-input-group,
.payload-input-group,
.gvw-input-group {
  flex: 1;
  display: flex;
  flex-direction: column;
  gap: 4px;
}

.price-label,
.year-label,
.payload-label,
.gvw-label {
  font-size: 11px;
  color: #666;
  font-weight: 600;
}

.price-input,
.year-input,
.payload-input,
.gvw-input {
  width: 100%;
  padding: 6px 8px;
  border: 1px solid #d9d9d9;
  border-radius: 4px;
  font-size: 13px;
}

.price-input:focus,
.year-input:focus,
.payload-input:focus,
.gvw-input:focus {
  outline: none;
  border-color: #ff6b35;
}

.price-separator,
.year-separator,
.payload-separator,
.gvw-separator {
  color: #999;
  font-weight: 600;
  margin-top: 16px;
}

/* Note */
.rating-options {
  display: flex;
  flex-direction: column;
  gap: 8px;
}

.rating-option {
  display: flex;
  align-items: center;
  cursor: pointer;
  padding: 6px 0;
}

.rating-option input[type="radio"] {
  margin-right: 8px;
  cursor: pointer;
}

.rating-stars {
  display: flex;
  align-items: center;
  gap: 4px;
}

.star {
  color: #ddd;
  font-size: 16px;
}

.star.filled {
  color: #ffb800;
}

.rating-text {
  font-size: 13px;
  color: #555;
  margin-left: 4px;
}

/* Responsive */
@media (max-width: 768px) {
  .filter-sidebar {
    width: 100%;
    max-height: none;
    position: static;
    border-radius: 0;
    padding: 12px;
  }
}
</style>
