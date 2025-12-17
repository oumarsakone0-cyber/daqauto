<template >
    <div v-if="showMobileSearch" class="mobile-search-modal">
      <div class="mobile-modal-header">
        <button class="mobile-back-btn" @click="closeMobileSearch">
          <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <line x1="19" y1="12" x2="5" y2="12"/>
            <polyline points="12,19 5,12 12,5"/>
          </svg>
        </button>
        
        <div class="mobile-search-input-container">
          <input 
            type="text" 
            placeholder="Research..." 
            class="input-style"
            v-model="searchQuery"
            @input="handleSearchInput"
            ref="mobileSearchInput"
            @keydown.enter="performSearch"
            @keydown.escape="closeMobileSearch"
          />
          
          <button v-if="searchQuery" class="mobile-clear-btn" @click="clearSearch">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <circle cx="12" cy="12" r="10"/>
              <line x1="15" y1="9" x2="9" y2="15"/>
              <line x1="9" y1="9" x2="15" y2="15"/>
            </svg>
          </button>
        </div>
        
        <button class="mobile-camera-search-btn" @click="toggleImageSearch">
          <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <path d="M14.5 4h-5L7 7H4a2 2 0 0 0-2 2v9a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2V9a2 2 0 0 0-2-2h-3l-2.5-3z"/>
            <circle cx="12" cy="13" r="3"/>
          </svg>
        </button>
      </div>
      
      <div class="mobile-search-content">
        <!-- Indicateur de chargement -->
        <div v-if="isSearching" class="mobile-search-loading">
          <div class="loading-spinner"></div>
          <span>Researching...</span>
        </div>
        
        <!-- Résultats de recherche -->
        <div v-else-if="searchResults.length > 0" class="mobile-search-results">
          <div class="mobile-results-count">{{ searchResults.length }} resultats found</div>
          
          <div 
            v-for="result in searchResults" 
            :key="result.id"
            class="mobile-result-item"
            @click="goToProduct(result)"
          >
            <div class="mobile-result-image">
              <img 
                :src="result.primary_image" 
                :alt="result.product_name"
                @error="handleImageError"
              />
            </div>
            <div class="mobile-result-content">
              <!-- Using method instead of inline logic to avoid linter confusion -->
              <div class="mobile-result-name" v-html="getResultDisplayName(result)"></div>
              <div class="mobile-result-category">
                <span class="mobile-category-badge">{{ result.category_name }}</span>
                <span v-if="result.subcategory_name" class="mobile-category-badge">{{ result.subcategory_name }}</span>
              </div>
            </div>
          </div>
          
          <button @click="viewAllResults" class="mobile-view-all-btn">
            See all results for "{{ searchQuery }}"
          </button>
        </div>
        
        <!-- Suggestions de recherche -->
        <div v-else-if="filteredSuggestions.length > 0" class="mobile-suggestions">
          <div class="mobile-suggestions-header">Popular Suggestions</div>
          
          <div 
            v-for="(suggestion, index) in filteredSuggestions" 
            :key="index"
            class="mobile-suggestion-item"
            @click="selectSuggestion(suggestion)"
          >
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <circle cx="11" cy="11" r="8"/>
              <path d="m21 21-4.35-4.35"/>
            </svg>
            <span v-html="highlightMatch(suggestion)"></span>
          </div>
        </div>
        
        <!-- Aucun résultat -->
        <div v-else-if="searchQuery && searchQuery.length >= 2" class="mobile-no-results">
          <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
            <circle cx="11" cy="11" r="8"/>
            <path d="m21 21-4.35-4.35"/>
          </svg>
          <span>No results for "{{ searchQuery }}"</span>
          <small>Try again with other keywords</small>
        </div>
        
        <!-- Recherches récentes et populaires -->
        <div v-else class="mobile-search-suggestions">
          <div class="mobile-section-title">Recent Research</div>
          <div class="mobile-recent-searches">
            <div v-for="(search, index) in recentSearches" :key="index" class="mobile-recent-item" @click="selectSuggestion(search)">
              <svg width="20" height="20"  viewBox="0 0 24 24" fill="none" stroke="#fe7900" stroke-width="2">
                <circle cx="12" cy="12" r="10"/>
                <polyline points="12,6 12,12 16,14"/>
              </svg>
              <span>{{ search }}</span>
            </div>
            <div v-if="recentSearches.length === 0" class="mobile-no-recent">
              <small>No recent Research</small>
            </div>
          </div>
          
          <div class="mobile-section-title">Popular searches</div>
          <div class="mobile-popular-searches">
            <div v-for="(search, index) in popularSearches" :key="index" class="mobile-popular-item" @click="selectSuggestion(search)">
              <span class="mobile-popular-rank">{{ index + 1 }}</span>
              <span>{{ search }}</span>
            </div>
          </div>
        </div>
      </div>
    </div>
</template>
<script setup>
import { ref, computed, watch, onMounted, nextTick } from 'vue'
import { useRouter } from 'vue-router'

const showMobileSearch = ref(false)
const searchQuery = ref('')
const searchResults = ref([])
const isSearching = ref(false)
const recentSearches = ref([])
const popularSearches = ref([
  'Brake Pads',
  'Oil Filter',
  'Car Battery',
  'Air Filter',
  'Spark Plugs',
  'Headlights',
  'Tires',
  'Windshield Wipers'
])
const router = useRouter()
const mobileSearchInput = ref(null)
const filteredSuggestions = computed(() => {
  if (!searchQuery.value) return []
  const query = searchQuery.value.toLowerCase()
  return popularSearches.value.filter(suggestion =>
    suggestion.toLowerCase().includes(query)
  )
})
const openMobileSearch = () => {
  showMobileSearch.value = true
  nextTick(() => {
    mobileSearchInput.value.focus()
  })
}
const closeMobileSearch = () => {
  showMobileSearch.value = false
  searchQuery.value = ''
  searchResults.value = []
}
const clearSearch = () => {
  searchQuery.value = ''
  searchResults.value = []
  mobileSearchInput.value.focus()
}
const handleSearchInput = () => {
  if (searchQuery.value.length < 2) {
    searchResults.value = []
    return
  }
  performSearch()
}
const performSearch = async () => {
  isSearching.value = true
  // Simuler une requête API avec un délai
  setTimeout(() => {
    // Exemple de résultats simulés
    searchResults.value = [
      {
        id: 1,
        product_name: 'Brake Pads Set',
        category_name: 'Brakes',
        subcategory_name: 'Brake Pads',
        primary_image: 'https://via.placeholder.com/150'
      },
      {
        id: 2,
        product_name: 'Oil Filter Premium',
        category_name: 'Engine',
        subcategory_name: 'Filters',
        primary_image: 'https://via.placeholder.com/150'
      }
    ]
    isSearching.value = false
  }, 1000)
}
const goToProduct = (product) => {
  closeMobileSearch()
  router.push({ name: 'product_details', params: { id: product.id } })
}
const getResultDisplayName = (result) => {
  const query = searchQuery.value.trim()
  if (!query) return result.product_name
  const regex = new RegExp(`(${query})`, 'gi')
  return result.product_name.replace(regex, '<strong>$1</strong>')
}
const highlightMatch = (suggestion) => {
  const query = searchQuery.value.trim()
  if (!query) return suggestion
  const regex = new RegExp(`(${query})`, 'gi')
  return suggestion.replace(regex, '<strong>$1</strong>')
}
const selectSuggestion = (suggestion) => {
  searchQuery.value = suggestion
  performSearch()
}
const viewAllResults = () => {
  closeMobileSearch()
  router.push({ name: 'search_results', query: { q: searchQuery.value } })
}
const handleImageError = (event) => {
  event.target.src = 'https://via.placeholder.com/150?text=No+Image'
}
const toggleImageSearch = () => {
  // Logique pour la recherche par image
  alert('Image search feature coming soon!')
}
onMounted(() => {
  // Charger les recherches récentes depuis le localStorage
  const storedSearches = localStorage.getItem('recentSearches')
  if (storedSearches) {
    recentSearches.value = JSON.parse(storedSearches)
  }
})
watch(searchQuery, (newQuery) => {
  if (newQuery && !recentSearches.value.includes(newQuery)) {
    recentSearches.value.unshift(newQuery)
    if (recentSearches.value.length > 5) {
      recentSearches.value.pop()
    }
    localStorage.setItem('recentSearches', JSON.stringify(recentSearches.value))
  }
})
// Expose la fonction pour ouvrir la recherche mobile
defineExpose({
  openMobileSearch
})


</script>
<style scoped>

/* Mobile Search Modal */
.mobile-search-modal {
  position: fixed;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: white;
  z-index: 10000;
  display: flex;
  flex-direction: column;
}

.mobile-modal-header {
  display: flex;
  align-items: center;
  gap: 12px;
  padding: 16px;
  border-bottom: 1px solid #f0f0f0;
  background: white;
  position: sticky;
  top: 0;
  z-index: 10001;
}

.mobile-back-btn {
  background: none;
  border: none;
  color: #333;
  padding: 8px;
  border-radius: 50%;
  cursor: pointer;
  transition: background 0.2s ease;
}

.mobile-back-btn:hover {
  background: #f5f5f5;
}

.mobile-search-input-container {
  flex: 1;
  position: relative;
  display: flex;
  align-items: center;
}

.mobile-search-input {
  width: 100%;
  padding: 12px 16px;
  border: 1px solid #e0e0e0;
  border-radius: 25px;
  font-size: 16px;
  outline: none;
  background: #f8f9fa;
}

.mobile-search-input:focus {
  border-color: #fe7900;
  background: white;
}

.mobile-clear-btn {
  position: absolute;
  right: 8px;
  background: none;
  border: none;
  color: #999;
  padding: 4px;
  border-radius: 50%;
  cursor: pointer;
  transition: all 0.2s ease;
}

.mobile-clear-btn:hover {
  background: #f0f0f0;
  color: #fe7900;
}

.mobile-camera-search-btn {
  background: none;
  border: none;
  color: #666;
  padding: 8px;
  border-radius: 50%;
  cursor: pointer;
  transition: background 0.2s ease;
}

.mobile-camera-search-btn:hover {
  background: #f5f5f5;
}

.mobile-search-content {
  flex: 1;
  overflow-y: auto;
  padding: 16px;
  margin-bottom: 55px;
}

.mobile-search-loading {
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  padding: 40px 20px;
  color: #666;
}

.loading-spinner {
  border: 3px solid #f3f3f3;
  border-top: 3px solid #fe7900;
  border-radius: 50%;
  width: 32px;
  height: 32px;
  animation: spin 1s linear infinite;
  margin-bottom: 16px;
}

.mobile-search-results {
  display: flex;
  flex-direction: column;
  gap: 16px;
}

.mobile-results-count {
  font-size: 14px;
  color: #666;
  padding: 8px 0;
  border-bottom: 1px solid #f0f0f0;
}

.mobile-result-item {
  display: flex;
  gap: 12px;
  padding: 12px;
  border-radius: 12px;
  background: #f8f9fa;
  cursor: pointer;
  transition: background 0.2s ease;
}

.mobile-result-item:hover {
  background: #f0f0f0;
}

.mobile-result-image {
  width: 60px;
  height: 60px;
  border-radius: 8px;
  overflow: hidden;
  flex-shrink: 0;
  background: white;
}

.mobile-result-image img {
  width: 100%;
  height: 100%;
  object-fit: cover;
}

.mobile-result-content {
  flex: 1;
  min-width: 0;
}

.mobile-result-name {
  font-weight: 500;
  font-size: 14px;
  color: #333;
  margin-bottom: 6px;
  line-height: 1.3;
}

.mobile-result-category {
  display: flex;
  gap: 4px;
  flex-wrap: wrap;
}

.mobile-category-badge {
  background: #e9ecef;
  color: #666;
  font-size: 11px;
  padding: 2px 6px;
  border-radius: 8px;
}

.mobile-view-all-btn {
  width: 100%;
  background: none;
  border: 1px solid #fe7900;
  color: #fe7900;
  padding: 12px 16px;
  border-radius: 25px;
  cursor: pointer;
  font-size: 14px;
  font-weight: 500;
  transition: all 0.2s ease;
  margin-top: 8px;
}

.mobile-view-all-btn:hover {
  background: #fe7900;
  color: white;
}

.mobile-suggestions {
  display: flex;
  flex-direction: column;
  gap: 8px;
}

.mobile-suggestions-header {
  font-size: 16px;
  font-weight: 600;
  color: #333;
  padding: 8px 0;
  border-bottom: 1px solid #f0f0f0;
  margin-bottom: 8px;
}

.mobile-suggestion-item {
  display: flex;
  align-items: center;
  gap: 12px;
  padding: 12px;
  border-radius: 12px;
  background: #f8f9fa;
  cursor: pointer;
  transition: background 0.2s ease;
}

.mobile-suggestion-item:hover {
  background: #f0f0f0;
}

.mobile-suggestion-item svg {
  color: #999;
  flex-shrink: 0;
}

.mobile-no-results {
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  padding: 40px 20px;
  color: #666;
  text-align: center;
}

.mobile-no-results svg {
  margin-bottom: 16px;
  opacity: 0.5;
}

.mobile-no-results small {
  margin-top: 8px;
  color: #999;
  font-size: 12px;
}

.mobile-search-suggestions {
  display: flex;
  flex-direction: column;
  gap: 20px;
}

.mobile-section-title {
  font-size: 16px;
  font-weight: 600;
  color: #333;
  padding: 8px 0;
  border-bottom: 1px solid #f0f0f0;
}

.mobile-recent-searches, .mobile-popular-searches {
  display: flex;
  flex-direction: column;
  gap: 8px;
  color: #333 ;
}

.mobile-recent-item, .mobile-popular-item {
  display: flex;
  align-items: center;
  gap: 12px;
  padding: 12px;
  border-radius: 12px;
  background: #f8f9fa;
  cursor: pointer;
  transition: background 0.2s ease;
}

.mobile-recent-item:hover, .mobile-popular-item:hover {
  background: #f0f0f0;
}

.mobile-recent-item svg {
  color: #999;
  flex-shrink: 0;
}

.mobile-popular-rank {
  background: #fe7900;
  color: white;
  font-size: 12px;
  font-weight: bold;
  padding: 4px 8px;
  border-radius: 50%;
  min-width: 24px;
  height: 24px;
  display: flex;
  align-items: center;
  justify-content: center;
  flex-shrink: 0;
}

.mobile-no-recent {
  padding: 20px;
  text-align: center;
  color: #999;
}
    
</style>