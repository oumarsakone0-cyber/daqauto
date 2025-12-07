<template>
  <div class="navbar-container" >
    <!-- Top navbar -->
    <div class="top-navbar">
      
      <div class="bg-gray-900 text-white text-sm py-2">
        <div class="container mx-auto px-4 flex justify-between items-center">
          <span class="hidden sm:block">We're offering free shipping on all orders over $50! </span>
          <span class="sm:hidden">Free shipping over $50!</span>
          <!-- <div class="flex items-center gap-4 text-sm">
            <span class="hidden md:block">{{languet.Langue}}</span>
            <span class="hidden lg:block">({{ languet.code }})</span>
            <span class="lg:hidden">USD $</span>
          </div> -->
        </div>
      </div>

      <div class="navbar-content">
        <!-- Version Desktop -->
        <div class="desktop-navbar">
          <div class="logo-container">
            <router-link to="/">
              <img src="../../assets/logo.png" alt="DaqAuto Logo" class="logo" />
            </router-link>
          </div>
          <div class="search-container">
            <input 
              type="text" 
              placeholder="Research..." 
              class="search-input"
              v-model="searchQuery"
              @input="handleSearchInput"
              @focus="handleFocus"
              @blur="hideSuggestions"
              @keydown.enter="performSearch"
              @keydown.escape="clearSearch"
            />
            
            <!-- Search Results -->
            <div v-if="showSearchResults && (searchResults.length > 0 || isSearching)" class="search-suggestions">
              <!-- Indicateur de chargement -->
              <div v-if="isSearching" class="search-loading">
                <div class="loading-spinner-small"></div>
                <span>Researching</span>
              </div>
              
              <!-- Résultats de recherche -->
              <div v-else-if="searchResults.length > 0">
                <div class="search-results-header">
                  <span>{{ searchResults.length }} results Found</span>
                  <button @click="clearSearch" class="clear-search-btn">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                      <line x1="18" y1="6" x2="6" y2="18"/>
                      <line x1="6" y1="6" x2="18" y2="18"/>
                    </svg>
                  </button>
                </div>
                
                <div 
                  v-for="result in searchResults" 
                  :key="result.id"
                  class="search-result-item"
                  @mousedown.prevent="goToProduct(result)"
                >
                  <div class="result-image">
                    <img 
                      :src="result.primary_image" 
                      :alt="result.product_name"
                      @error="handleImageError"
                    />
                  </div>
                  <div class="result-content">
                    <!-- Using method instead of inline logic to avoid linter confusion -->
                    <div class="result-name" v-html="getResultDisplayName(result)"></div>
                    <div class="result-category">
                      <span class="category-badge">{{ result.category_name }}</span>
                      <span v-if="result.subcategory_name" class="category-badge">{{ result.subcategory_name }}</span>
                      <span v-if="result.subsubcategory_name" class="category-badge">{{ result.subsubcategory_name }}</span>
                    </div>
                  </div>
                </div>
                
                <div class="search-results-footer">
                  <button @click="viewAllResults" class="view-all-btn">
                   See all results for "{{ searchQuery }}"
                  </button>
                </div>
              </div>
              
              <!-- Aucun résultat -->
              <div v-else-if="searchQuery && searchQuery.length >= 2" class="no-results">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                  <circle cx="11" cy="11" r="8"/>
                  <path d="m21 21-4.35-4.35"/>
                </svg>
                <span>No results for"{{ searchQuery }}"</span>
                <small>Try again with other keywords</small>
              </div>
            </div>
            
            <!-- Search Suggestions (suggestions statiques quand pas de recherche active) -->
            <div v-else-if="showSuggestions && filteredSuggestions.length > 0" class="search-suggestions">
              <div class="suggestions-header">Popular suggestions</div>
              <div 
                v-for="(suggestion, index) in filteredSuggestions" 
                :key="index"
                class="suggestion-item"
                @mousedown.prevent="selectSuggestion(suggestion)"
              >
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                  <circle cx="11" cy="11" r="8"/>
                  <path d="m21 21-4.35-4.35"/>
                </svg>
                <span v-html="highlightMatch(suggestion)"></span>
              </div>
            </div>
            
            <button class="search-image-btn" @click="toggleImageSearch" title="Recherche par image">
              <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M14.5 4h-5L7 7H4a2 2 0 0 0-2 2v9a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2V9a2 2 0 0 0-2-2h-3l-2.5-3z"/>
                <circle cx="12" cy="13" r="3"/>
              </svg>
            </button>
            <button class="search-btn" @click="performSearch">
              <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <circle cx="11" cy="11" r="8"/>
                <path d="m21 21-4.35-4.35"/>
              </svg>
            </button>
          </div>
          
          <div class="user-actions">
            <div class="app-download">
              <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <rect x="5" y="2" width="14" height="20" rx="2" ry="2"/>
                <line x1="12" y1="18" x2="12" y2="18"/>
              </svg>
              <span>Download the app</span>
            </div>

            <!-- Updated language selector with MyMemory translation logic -->
            <!-- Sélecteur de langue -->
            <div class="language-selector relative" :class="{ open: showLanguageDropdown }" ref="languageSelectorRef">
              <div class="flex items-center cursor-pointer gap-2" @click="toggleLanguageDropdown">
                <img :src="selectedLanguage.flag" :alt="selectedLanguage.code" class="w-6 h-4" />
                <span v-if="isTranslating">⏳ Translating...</span>
                <span v-else>{{ selectedLanguage.Langue }}</span>
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                  <polyline points="6,9 12,15 18,9"/>
                </svg>
              </div>
              
              <div v-if="showLanguageDropdown && !isTranslating" class="absolute mt-2 bg-white shadow-md rounded-lg w-40 z-50">
                <ul class="divide-y divide-gray-100">
                  <li v-for="lang in uniqueLanguages" :key="lang.code" @click="selectLanguage(lang)" class="flex items-center gap-2 px-3 py-2 cursor-pointer hover:bg-gray-100">
                    <img :src="lang.flag" :alt="lang.code" class="w-6 h-4" />
                    <span>{{ lang.Langue }}</span>
                  </li>
                </ul>
              </div>
            </div>

            <!-- Sélecteur de devise (NOUVEAU) -->
            <div class="language-selector relative ml-4" :class="{ open: showCurrencyDropdown }" ref="currencySelectorRef">
              <div class="flex items-center cursor-pointer gap-2" @click="toggleCurrencyDropdown">
                <span>{{ selectedCurrency }}</span>
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                  <polyline points="6,9 12,15 18,9"/>
                </svg>
              </div>
              
              <div v-if="showCurrencyDropdown" class="absolute mt-2 bg-white shadow-md rounded-lg w-32 z-50">
                <ul class="divide-y divide-gray-100">
                  <li v-for="curr in availableCurrencies" :key="curr" @click="selectCurrency(curr)" class="px-3 py-2 cursor-pointer hover:bg-gray-100">
                    {{ curr }}
                  </li>
                </ul>
              </div>
            </div>
            
            <!-- <div class="user-account" @click="goToBoutique">
              <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/>
                <circle cx="12" cy="7" r="4"/>
              </svg>
              <span>Se connecter / S'inscrire</span>
              <span>Mon Dashboard</span>
            </div> -->
            <div class="user-account">
              <div class="flex items-center">
                <!-- Si l'utilisateur n'a pas de photo -->
                <svg v-if="!currentUser?.picture || currentUser.picture === '0'" 
                    width="18" height="18" viewBox="0 0 24 24" 
                    fill="none" stroke="currentColor" stroke-width="2">
                  <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/>
                  <circle cx="12" cy="7" r="4"/>
                </svg>

                <!-- Si l'utilisateur a une photo -->
                <img v-else 
                    :src="currentUser.picture" 
                    alt="Profile" 
                    class="h-8 w-8 rounded-full object-cover" />
              </div>
              <div @click="goToProfile" v-if="currentUser">
                
                <span v-if="currentUser && currentUser.email">
                  {{ currentUser.full_name }}
                </span>
              </div>
              <div v-else>
                 <span @click="goToAuthentication">Login / Register</span>
              </div>
            </div>
            
            <router-link :to="{ name: 'profile_client', query: { tab: 'cart' } }" class="cart" @click="goToCart">
              <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <circle cx="9" cy="21" r="1"/>
                <circle cx="20" cy="21" r="1"/>
                <path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"/>
              </svg>
              <span>Cart</span>
              
              <div v-if="cartCount > 0" class="cart-badge"><div v-if="cartCount > 99" class="cart-count">99+</div>
                  <div v-else >
                   {{ cartCount  }}
                   </div></div>
            </router-link>

          </div>
          <div class="user-account" @click="handleLogout()" >
            
            <LogOut  v-if="currentUser && currentUser.email" class="h-6 w-6 text-gray-600 cursor-pointer" />
          </div>
        </div>
        
        <!-- Version Mobile -->
        <div class="mobile-navbar">
          <div class="mobile-top-row">
            <button class="mobile-menu-btn" @click="toggleMobileMenu">
              <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <line x1="3" y1="6" x2="21" y2="6"/>
                <line x1="3" y1="12" x2="21" y2="12"/>
                <line x1="3" y1="18" x2="21" y2="18"/>
              </svg>
            </button>
            
            <div class="logo-container">
              <router-link to="/">
                <img src="../../assets/logo.png" alt="Daq Auto logo" class="logo" />
              </router-link>
            </div>
            
            <div class="mobile-actions">
              <button class="mobile-cart-btn">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                  <circle cx="9" cy="21" r="1"/>
                  <circle cx="20" cy="21" r="1"/>
                  <path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"/>
                </svg>
                <div class="cart-badge">
                  <div v-if="cartCount > 99" class="cart-count">99+</div>
                  <div v-else >
                   {{ cartCount }}
                   </div>
                  </div>
              </button>
            </div>
          </div>
          
          <div class="mobile-search-row" @click="openMobileSearch">
            <div class="mobile-search-bar">
              <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <circle cx="11" cy="11" r="8"/>
                <path d="m21 21-4.35-4.35"/>
              </svg>
              <span class="mobile-search-placeholder">Research...</span>
              <button class="mobile-camera-btn">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                  <path d="M14.5 4h-5L7 7H4a2 2 0 0 0-2 2v9a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2V9a2 2 0 0 0-2-2h-3l-2.5-3z"/>
                  <circle cx="12" cy="13" r="3"/>
                </svg>
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>
    
    <!-- Main categories navbar -->
    <div class="categories-navbar">
      <div class="categories-content">
        <div 
          class="all-categories"
          @mouseenter="showCategoriesMenu = true"
          @mouseleave="showCategoriesMenu = false"
        >
          <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <line x1="3" y1="6" x2="21" y2="6"/>
            <line x1="3" y1="12" x2="21" y2="12"/>
            <line x1="3" y1="18" x2="21" y2="18"/>
          </svg>
          <span>All categories</span>
          <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <polyline points="6,9 12,15 18,9"/>
          </svg>
          
          <!-- Categories Dropdown - Unified Card -->
          <div v-if="showCategoriesMenu" class="unified-categories-dropdown">
            <div class="categories-section">
              <!-- Indicateur de chargement -->
              <div v-if="isLoadingCategories" class="loading-categories">
                <div class="loading-spinner"></div>
                <span>Loading categories...</span>
              </div>
              
              <!-- Message d'erreur -->
              <div v-else-if="categoriesError" class="categories-error">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                  <circle cx="12" cy="12" r="10"></circle>
                  <line x1="12" y1="8" x2="12" y2="12"></line>
                  <line x1="12" y1="16" x2="12.01" y2="16"></line>
                </svg>
                <span>{{ categoriesError }}</span>
                <button @click="reloadCategories" class="retry-btn">Try again</button>
              </div>
              
              <!-- Liste des catégories -->
              <div v-else>
                <div 
                  v-for="category in categories" 
                  :key="category.id"
                  class="category-item"
                  :class="{ active: activeCategory?.id === category.id }"
                  @mouseenter="setActiveCategory(category)"
                  @click="navigateToCategory(category)"
                >
                  <div class="category-info">
                    <span class="category-icon">{{ category.icon }}</span>
                    <span class="category-name">{{ category.name }}</span>
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                      <polyline points="9,18 15,12 9,6"/>
                    </svg>
                  </div>
                </div>
              </div>
            </div>
            
            <!-- Subcategories Section -->
            <div class="subcategories-section">
              <div v-if="activeCategory && activeCategory.subcategories && activeCategory.subcategories.length > 0" class="subcategories-content">
                <h4>{{ activeCategory.name }}</h4>
                <div class="subcategories-columns">
                  <div 
                    v-for="subcategory in activeCategory.subcategories" 
                    :key="subcategory.id"
                    class="subcategory-column"
                  >
                    <div class="subcategory-title" @click="navigateToSubcategory(subcategory)">{{getBeforeChar(subcategory.name, '→') }}</div>
                    <ul class="sub-subcategories-list">
                      <li 
                        v-for="subSubcategory in subcategory.sub_subcategories" 
                        :key="subSubcategory.id" 
                        class="sub-subcategory-item"
                        @click="navigateToSubSubcategory(subSubcategory)"
                      >
                        {{ subSubcategory.name }}
                      </li>
                    </ul>
                  </div>
                </div>
              </div>
              <div v-else class="subcategories-placeholder">
                <div class="placeholder-content">
                  <svg width="64" height="64" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1">
                    <rect x="3" y="3" width="18" height="18" rx="2" ry="2"/>
                    <circle cx="8.5" cy="8.5" r="1.5"/>
                    <polyline points="21,15 16,10 5,21"/>
                  </svg>
                  <p>{{ isLoadingCategories ? 'Loading...' : 'Hover over a category to see the subcategories' }}</p>
                </div>
              </div>
            </div>
          </div>
        </div>
        
        <nav class="main-nav">
          <a href="#" class="nav-item featured">Buy on Daq Auto (via our service)</a>
          <a href="#" class="nav-item hot">Top Deals</a>
          <a href="#" class="nav-item">Track an order</a>
          <a href="#" class="nav-item">complain</a>
          <a href="#" class="nav-item">Sell on Daq Auto</a>
          <a href="/boutique-admin/dashboard" class="nav-item">Supplier area</a>
        </nav>
      </div>
    </div>
    
    <!-- Image search modal -->
    <div v-if="showImageSearch" class="image-search-modal" @click.self="toggleImageSearch">
      <div class="modal-content">
        <div class="modal-header">
          <h3>Image Search</h3>
          <button @click="toggleImageSearch" class="close-btn">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <line x1="18" y1="6" x2="6" y2="18"/>
              <line x1="6" y1="6" x2="18" y2="18"/>
            </svg>
          </button>
        </div>
        <div class="modal-body">
          <p>Find what you like at a great price on Daq Auto in using image seach</p>
          <div class="upload-area" @dragover.prevent @drop.prevent="handleDrop">
            <div class="upload-placeholder">
              <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                <rect x="3" y="3" width="18" height="18" rx="2" ry="2"/>
                <circle cx="8.5" cy="8.5" r="1.5"/>
                <polyline points="21,15 16,10 5,21"/>
              </svg>
              <p class="upload-text">Drag an image here</p>
              <p class="upload-or">or</p>
              <button class="upload-btn">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                  <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/>
                  <polyline points="7,10 12,15 17,10"/>
                  <line x1="12" y1="15" x2="12" y2="3"/>
                </svg>
                Import photo
              </button>
            </div>
          </div>
          <p class="search-tip"> For a quick search, press Ctrl + V to paste an image into the Research box</p>
        </div>
      </div>
    </div>
    
    <!-- Mobile Search Modal -->
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
    
    <!-- Mobile Menu Modal -->
    <div v-if="showMobileMenu" class="mobile-menu-modal">
      <div class="mobile-menu-header">
        <button class="mobile-close-btn" @click="toggleMobileMenu">
          <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <line x1="18" y1="6" x2="6" y2="18"/>
            <line x1="6" y1="6" x2="18" y2="18"/>
          </svg>
        </button>
        <h3>Menu</h3>
      </div>
      
      <div class="mobile-menu-user">
        <div class="mobile-user-avatar">
          <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/>
            <circle cx="12" cy="7" r="4"/>
          </svg>
        </div>
        <div class="mobile-user-info">
          <div class="mobile-user-welcome">Welcome</div>
          <div class="mobile-user-actions">
            <a href="#" class="mobile-login-btn">Login</a>
            <span class="mobile-separator">/</span>
            <a href="#" class="mobile-register-btn">Register</a>
          </div>
        </div>
      </div>
      
      <div class="mobile-menu-categories">
        <div class="mobile-menu-title">Categories</div>
        
        <!-- Navigation par niveaux -->
        <div class="mobile-categories-navigation">
          <!-- Niveau 1: Catégories principales -->
          <div v-if="mobileMenuLevel === 1" class="mobile-category-level">
            <div 
              v-for="category in categories" 
              :key="category.id"
              class="mobile-category-item"
              @click="selectMobileCategory(category)"
            >
              <div class="mobile-category-content">
                <span class="mobile-category-icon">{{ category.icon }}</span>
                <span class="mobile-category-name">{{ category.name }}</span>
              </div>
              <svg v-if="category.subcategories && category.subcategories.length > 0" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <polyline points="9,18 15,12 9,6"/>
              </svg>
            </div>
          </div>
          
          <!-- Niveau 2: Sous-catégories -->
          <div v-else-if="mobileMenuLevel === 2" class="mobile-category-level">
            <div class="mobile-category-back" @click="mobileMenuBack">
              <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <line x1="19" y1="12" x2="5" y2="12"/>
                <polyline points="12,19 5,12 12,5"/>
              </svg>
              <span>Back</span>
            </div>
            
            <div class="mobile-subcategory-title">{{ selectedMobileCategory.name }}</div>
            
            <div 
              v-for="subcategory in selectedMobileCategory.subcategories" 
              :key="subcategory.id"
              class="mobile-category-item"
              @click="selectMobileSubcategory(subcategory)"
            >
              <div class="mobile-category-content">
                <span class="mobile-category-name">{{ subcategory.name }}</span>
              </div>
              <svg v-if="subcategory.sub_subcategories && subcategory.sub_subcategories.length > 0" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <polyline points="9,18 15,12 9,6"/>
              </svg>
            </div>
          </div>
          
          <!-- Niveau 3: Sous-sous-catégories -->
          <div v-else-if="mobileMenuLevel === 3" class="mobile-category-level">
            <div class="mobile-category-back" @click="mobileMenuBack">
              <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <line x1="19" y1="12" x2="5" y2="12"/>
                <polyline points="12,19 5,12 12,5"/>
              </svg>
              <span>Back</span>
            </div>
            
            <div class="mobile-subcategory-title">{{ selectedMobileSubcategory.name }}</div>
            
            <div 
              v-for="subSubcategory in selectedMobileSubcategory.sub_subcategories" 
              :key="subSubcategory.id"
              class="mobile-category-item"
              @click="navigateToSubSubcategory(subSubcategory)"
            >
              <div class="mobile-category-content">
                <span class="mobile-category-name">{{ subSubcategory.name }}</span>
              </div>
            </div>
          </div>
        </div>
      </div>
      
      <div class="mobile-menu-links">
        <router-link to="/" class="mobile-menu-link">
          <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/>
            <polyline points="9,22 9,12 15,12 15,22"/>
          </svg>
          <span>Welcome</span>
        </router-link>
        <a href="#" class="mobile-menu-link">
          <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <path d="M22 12h-4l-3 9L9 3l-3 9H2"/>
          </svg>
          <span>Offers of the day</span>
        </a>
        <a href="#" class="mobile-menu-link">
          <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/>
            <circle cx="12" cy="7" r="4"/>
          </svg>
          <span>My Account</span>
        </a>
        <a href="#" class="mobile-menu-link">
          <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <circle cx="9" cy="21" r="1"/>
            <circle cx="20" cy="21" r="1"/>
            <path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"/>
          </svg>
          <span>Cart</span>
        </a>
        <a href="#" class="mobile-menu-link">
          <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <path d="M4 11a9 9 0 0 1 9 9"/>
            <path d="M4 4a16 16 0 0 1 16 16"/>
            <circle cx="5" cy="19" r="1"/>
          </svg>
          <span>Blog</span>
        </a>
        <a href="#" class="mobile-menu-link">
          <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <circle cx="12" cy="12" r="10"/>
            <path d="M9.09 9a3 3 0 0 1 5.83 1c0 2-3 3-3 3"/>
            <line x1="12" y1="17" x2="12.01" y2="17"/>
          </svg>
          <span>Help</span>
        </a>
      </div>
      
      <div class="mobile-menu-footer">
        <div class="mobile-language">
          <img src="https://ae-pic-a1.aliexpress-media.com/kf/Sb900db0ad7604a83b297a51d9222905bm/624x160.png" alt="FR" class="flag" />
          <span>Français / EUR</span>
        </div>
        <div class="mobile-app-download">
          <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <rect x="5" y="2" width="14" height="20" rx="2" ry="2"/>
            <line x1="12" y1="18" x2="12" y2="18"/>
          </svg>
          <span>Download the app</span>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { computed, nextTick, onMounted, onUnmounted, ref, watch } from 'vue';
import { useRouter } from 'vue-router';
import { categoriesApi, productsApi } from '../../services/api.js';
import { LogOut } from 'lucide-vue-next';
import { ElMessageBox, ElMessage } from 'element-plus'
import { useCurrencyStore } from '../../stores/currency.js'
import { useCartStore } from '../../stores/cart'

const currencyStore = useCurrencyStore()
const cart = useCartStore()

const MYMEMORY_API_KEY = 'f8d4739abb435aefc95f'
const MYMEMORY_EMAIL = 'oumarsakone0@gmail.com'

// Translation states
const currentLanguage = ref('fr')
const isTranslating = ref(false)
const showCurrencyDropdown = ref(false)
const selectedCurrency = ref('XOF')
const showStats = ref(false)
const originalTexts = new Map()
const translationCache = new Map()
const languageSelectorRef = ref(null)
const currencySelectorRef = ref(null)

const cacheStats = ref({
  cached: 0,
  new: 0,
  saved: 0
})

// === États pour la recherche ===
const searchQuery = ref('')
const showSearchResults = ref(false)
const searchResults = ref([])
const isSearching = ref(false)
const showSuggestions = ref(false)
const searchTimeout = ref(null)
const searchAbortController = ref(null)

const showImageSearch = ref(false)

// === États pour les catégories ===
const showCategoriesMenu = ref(false)
const categories = ref([])
const isLoadingCategories = ref(false)
const categoriesError = ref('')
const activeCategory = ref(null)
const languet = ref('English')



// === États pour la liste déroulante langue/devise ===
const showLanguageDropdown = ref(false)
const selectedLanguage = ref({
  code: 'FR',
  currency: 'XOF',
  label: 'FR / F CFA',
  flag: 'https://flagcdn.com/w20/us.png'
})

const languages = ref([
  {
    code: 'FR-XOF',
    langCode: 'FR',
    Langue: 'Francais',
    currency: 'XOF',
    label: 'FR / XOF',
    flag: 'https://flagcdn.com/w20/ci.png'
  },
  {
    code: 'EN-USD',
    langCode: 'EN',
    Langue: 'English',
    currency: 'USD',
    label: 'EN / USD',
    flag: 'https://flagcdn.com/w20/us.png'
  },
  {
    code: 'EN-NGN',
    currency: 'NGN',
    label: 'EN / NGN',
  },
  {
    code: 'ZH-CNY',
    langCode: 'ZH',
    Langue: '中文 (Chinois)',
    currency: 'CNY',
    label: 'ZH / CNY',
    flag: 'https://flagcdn.com/w20/cn.png'
  }
  
])

// === États pour la version mobile ===
const showMobileSearch = ref(false)
const showMobileMenu = ref(false)
const mobileMenuLevel = ref(1)
const selectedMobileCategory = ref(null)
const selectedMobileSubcategory = ref(null)
const mobileSearchInput = ref(null)
const currentUser = ref(null)

// Recherches récentes et populaires pour mobile
const recentSearches = ref(['iPhone 15', 'Écouteurs sans fil', 'Montre connectée'])
const popularSearches = ref([
  'iPhone 15 Pro Max',
  'Samsung Galaxy S24',
  'Écouteurs Bluetooth',
  'Chargeur sans fil',
  'Montre connectée',
  'Caméra surveillance',
  'Drone',
  'Tablette Android',
  'Clavier mécanique',
  'Souris gaming'
])

// Search suggestions data (gardé statique pour les suggestions rapides)
const searchSuggestions = ref([
  'iPhone 15 Pro Max',
  'Samsung Galaxy S24',
  'MacBook Air M2',
  'AirPods Pro',
  'iPad Pro',
  'Nintendo Switch',
  'PlayStation 5',
  'Xbox Series X',
  'Apple Watch',
  'Casque Bluetooth',
  'Chargeur sans fil',
  'Écouteurs gaming',
  'Clavier mécanique',
  'Souris gaming',
  'Webcam 4K',
  'Moniteur gaming',
  'SSD externe',
  'Powerbank',
  'Câble USB-C',
  'Adaptateur HDMI',
  'Smartphone Android',
  'Tablette Samsung',
  'Ordinateur portable',
  'Écran PC',
  'Imprimante',
  'Router WiFi',
  'Disque dur',
  'Mémoire RAM',
  'Carte graphique',
  'Processeur Intel'
])

const router = useRouter(); // Declare router variable

const uniqueLanguages = computed(() => {
  const seen = new Set()
  return languages.value.filter(lang => {
    // 🔹 Ignorer si pas de langue définie
    if (!lang.langCode || !lang.Langue) return false

    if (seen.has(lang.code)) return false
    seen.add(lang.code)
    return true
  })
})

// === États pour le panier ===
const cartCount = computed(() => cart.items.length)

// Devises disponibles selon la langue sélectionnée
const availableCurrencies = computed(() => {
  // Récupérer toutes les devises uniques sans filtrer par langue
  const allCurrencies = languages.value.map(lang => lang.currency)
  return [...new Set(allCurrencies)] // Supprimer les doublons
})

const getResultDisplayName = (result) => {
  return result.highlighted_name || result.product_name
}

const loadCache = () => {
  try {
    const saved = localStorage.getItem('mymemory-cache')
    if (saved) {
      const parsed = JSON.parse(saved)
      Object.entries(parsed).forEach(([key, value]) => {
        translationCache.set(key, value)
      })
      console.log('[v0] Cache loaded:', translationCache.size, 'entries')
    }
  } catch (error) {
    console.error('[v0] Error loading cache:', error)
  }
}

const saveCache = () => {
  try {
    const cacheObj = {}
    translationCache.forEach((value, key) => {
      cacheObj[key] = value
    })
    localStorage.setItem('mymemory-cache', JSON.stringify(cacheObj))
    console.log('[v0] Cache saved:', translationCache.size, 'entries')
  } catch (error) {
    console.error('[v0] Error saving cache:', error)
  }
}

const getAllTextNodes = (element = document.body) => {
  const textNodes = []
  const walker = document.createTreeWalker(
    element,
    NodeFilter.SHOW_TEXT,
    {
      acceptNode: (node) => {
        const parent = node.parentElement
        if (!parent || 
            parent.tagName === 'SCRIPT' || 
            parent.tagName === 'STYLE' ||
            parent.tagName === 'NOSCRIPT' ||
            parent.closest('[data-no-translate]') ||
            node.textContent.trim() === '' ||
            node.textContent.trim().length < 3) {
          return NodeFilter.FILTER_REJECT
        }
        return NodeFilter.FILTER_ACCEPT
      }
    }
  )
  
  let node
  while (node = walker.nextNode()) {
    textNodes.push(node)
  }
  
  console.log('[v0] Found text nodes:', textNodes.length)
  return textNodes
}

const translateWithMyMemory = async (texts, sourceLang = 'fr', targetLang = 'en') => {
  try {
    console.log('[v0] Translating with MyMemory API...')
    const translations = []
    
    const batchSize = 5
    for (let i = 0; i < texts.length; i += batchSize) {
      const batch = texts.slice(i, i + batchSize)
      const batchPromises = batch.map(async (text) => {
        const cacheKey = `${text}_${sourceLang}_${targetLang}`
        
        if (translationCache.has(cacheKey)) {
          cacheStats.value.cached++
          return translationCache.get(cacheKey)
        }
        
        try {
          let url = `https://api.mymemory.translated.net/get?q=${encodeURIComponent(text)}&langpair=${sourceLang}|${targetLang}`
          url += `&key=${MYMEMORY_API_KEY}`
          url += `&de=${encodeURIComponent(MYMEMORY_EMAIL)}`

          console.log('[v0] API call for:', text.substring(0, 50) + '...')
          const response = await fetch(url)
          
          if (!response.ok) {
            throw new Error(`API error: ${response.status}`)
          }
          
          const data = await response.json()
          const translation = data.responseData?.translatedText || text
          
          translationCache.set(cacheKey, translation)
          cacheStats.value.new++
          
          return translation
        } catch (error) {
          console.error('[v0] Translation error for text:', text, error)
          return text
        }
      })
      
      const batchResults = await Promise.all(batchPromises)
      translations.push(...batchResults)
      
      if (i + batchSize < texts.length) {
        await new Promise(resolve => setTimeout(resolve, 200))
      }
    }
    
    saveCache()
    
    const total = cacheStats.value.cached + cacheStats.value.new
    cacheStats.value.saved = total > 0 ? Math.round((cacheStats.value.cached / total) * 100) : 0
    
    return translations
  } catch (error) {
    console.error('[v0] MyMemory translation failed:', error)
    return texts
  }
}

const translatePage = async () => {
  isTranslating.value = true
  cacheStats.value = { cached: 0, new: 0, saved: 0 }
  
  console.log('[v0] Starting MyMemory page translation...')
  
  await nextTick()
  
  const textNodes = getAllTextNodes()
  console.log('[v0] Processing', textNodes.length, 'text nodes')
  
  const textsToTranslate = []
  const nodeTextMap = new Map()
  
  textNodes.forEach(node => {
    const originalText = node.textContent.trim()
    if (originalText && originalText.length > 2) {
      if (!originalTexts.has(node)) {
        originalTexts.set(node, originalText)
      }
      
      textsToTranslate.push(originalText)
      nodeTextMap.set(node, originalText)
    }
  })
  
  if (textsToTranslate.length > 0) {
    console.log('[v0] Translating', textsToTranslate.length, 'texts')
    const translations = await translateWithMyMemory(textsToTranslate, 'fr', targetLang)
    
    let processedCount = 0
    textNodes.forEach((node, index) => {
      if (translations[index] && translations[index] !== textsToTranslate[index]) {
        node.textContent = translations[index]
        processedCount++
      }
    })
    
    console.log('[v0] Translation completed. Processed:', processedCount, 'nodes')
    
    showStats.value = true
    setTimeout(() => {
      showStats.value = false
    }, 3000)
  }
  
  isTranslating.value = false
}

const restoreOriginalTexts = () => {
  console.log('[v0] Restoring original texts...')
  let restoredCount = 0
  originalTexts.forEach((originalText, node) => {
    if (node.parentElement) {
      node.textContent = originalText
      restoredCount++
    }
  })
  console.log('[v0] Restored', restoredCount, 'text nodes')
}

const toggleLanguageWithTranslation = async () => {
  if (isTranslating.value) return
  
  console.log('[v0] Toggling language from', currentLanguage.value)
  
  if (currentLanguage.value === 'fr') {
    currentLanguage.value = 'en'
    await translatePage()
  } else {
    currentLanguage.value = 'fr'
    restoreOriginalTexts()
  }
  
  localStorage.setItem('preferred-language', currentLanguage.value)
  showLanguageDropdown.value = false
}

// Nouvelle fonction pour sélectionner la langue
const selectLanguage = async (langOption) => {
  if (isTranslating.value) return
  
  selectedLanguage.value = langOption
  showLanguageDropdown.value = false
  
  // Gestion de la traduction
  if (langOption.langCode !== 'FR' && currentLanguage.value === 'fr') {
    currentLanguage.value = langOption.langCode.toLowerCase()
    await translatePage(langOption.langCode.toLowerCase()) // Passer la langue cible
  } else if (langOption.langCode === 'FR' && currentLanguage.value !== 'fr') {
    currentLanguage.value = 'fr'
    restoreOriginalTexts()
  }
  
  localStorage.setItem('preferred-language', currentLanguage.value)
  localStorage.setItem('selected-language', JSON.stringify(langOption))
}

// Nouvelle fonction pour sélectionner la devise
const selectCurrency = (currency) => {
  selectedCurrency.value = currency
  currencyStore.currency = currency
  showCurrencyDropdown.value = false
  localStorage.setItem('preferred-currency', currency)
}

// Nouveaux toggles
const toggleCurrencyDropdown = () => {
  showCurrencyDropdown.value = !showCurrencyDropdown.value
}


const toggleLanguageDropdown = () => {
  if (!isTranslating.value) {
    showLanguageDropdown.value = !showLanguageDropdown.value
  }
}

// Function to load categories from API
const loadCategories = async () => {
  try {
    isLoadingCategories.value = true;
    categoriesError.value = '';
    
    console.log('🔄 Chargement des catégories depuis l\'API...');
    const response = await categoriesApi.getCategories();
    
    if (response.success && response.data) {
      categories.value = response.data.map(category => ({
        id: category.id,
        name: category.name,
        icon: category.icon || '📦',
        subcategories: category.subcategories || []
      }));
      
      console.log('✅ Catégories chargées:', categories.value);
    } else {
      throw new Error(response.message || 'Error to load categories');
    }
  } catch (error) {
    console.error('❌ Erreur lors du chargement des catégories:', error);
    categoriesError.value = error.message;
    
    // Fallback avec des catégories par défaut en cas d'erreur
    categories.value = [
      {
        id: 1,
        name: 'Électronique',
        icon: '📱',
        subcategories: [
          { 
            id: 1, 
            name: 'Smartphones', 
            sub_subcategories: [
              { id: 1, name: 'iPhone' },
              { id: 2, name: 'Samsung' },
              { id: 3, name: 'Xiaomi' }
            ]
          },
          { 
            id: 2, 
            name: 'Ordinateurs', 
            sub_subcategories: [
              { id: 4, name: 'Laptops' },
              { id: 5, name: 'PC Bureau' },
              { id: 6, name: 'Tablettes' }
            ]
          }
        ]
      },
      {
        id: 2,
        name: 'Mode & Vêtements',
        icon: '👕',
        subcategories: [
          { 
            id: 3, 
            name: 'Homme', 
            sub_subcategories: [
              { id: 7, name: 'T-shirts' },
              { id: 8, name: 'Pantalons' },
              { id: 9, name: 'Chaussures' }
            ]
          },
          { 
            id: 4, 
            name: 'Femme', 
            sub_subcategories: [
              { id: 10, name: 'Robes' },
              { id: 11, name: 'Sacs' },
              { id: 12, name: 'Bijoux' }
            ]
          }
        ]
      }
    ];
  } finally {
    isLoadingCategories.value = false;
  }
};

// New functions for product search
const handleSearchInput = () => {
  // Clear previous timeout
  if (searchTimeout.value) {
    clearTimeout(searchTimeout.value);
  }
  
  // Abort previous request if it exists
  if (searchAbortController.value) {
    searchAbortController.value.abort();
  }
  
  const query = searchQuery.value.trim();
  
  // Show results if the field is not empty
  showSearchResults.value = query.length > 0;
  showSuggestions.value = false;
  
  // Do not search if less than 2 characters
  if (query.length < 2) {
    searchResults.value = [];
    isSearching.value = false;
    showSuggestions.value = query.length > 0;
    return;
  }
  
  // Set a delay before initiating the search (debouncing)
  isSearching.value = true;
  searchTimeout.value = setTimeout(async () => {
    await performProductSearch(query);
  }, 300);
};

const performProductSearch = async (query) => {
  try {
    // Create a new AbortController for this request
    searchAbortController.value = new AbortController();
    
    console.log('🔍 Recherche de produits pour:', query);
    
    const response = await productsApi.searchProducts(query, {
      limit: 8, // Limit to 8 results for preview
    });
    
    if (response.success && response.data) {
      searchResults.value = response.data;
      console.log('✅ Résultats de recherche:', searchResults.value);
    } else {
      searchResults.value = [];
      console.warn('⚠️ Aucun résultat trouvé');
    }
  } catch (error) {
    if (error.name !== 'AbortError') {
      console.error('❌ Erreur lors de la recherche:', error);
      searchResults.value = [];
    }
  } finally {
    isSearching.value = false;
  }
};

const performSearch = () => {
  if (searchQuery.value.trim()) {
    // Redirect to the search results page with the search term
    router.push({
      path: '/recherche_de_produit_list',
      query: { search: searchQuery.value.trim() }
    });
    clearSearch();
    closeMobileSearch();
  }
};

const clearSearch = () => {
  searchQuery.value = '';
  searchResults.value = [];
  showSearchResults.value = false;
  showSuggestions.value = false;
  
  if (searchTimeout.value) {
    clearTimeout(searchTimeout.value);
  }
  
  if (searchAbortController.value) {
    searchAbortController.value.abort();
  }
};

// Modified function to redirect to results page with product categories
const goToProduct = (product) => {
  console.log('Navigation vers le produit:', product);
  
  // Construct query parameters with product categories and search term
  const queryParams = {
    search: searchQuery.value.trim()
  };
  
  // Add categories if available
  if (product.category_id) {
    queryParams.category = product.category_id;
  }
  if (product.subcategory_id) {
    queryParams.subcategory = product.subcategory_id;
  }
  if (product.subsubcategory_id) {
    queryParams.sub_subcategory = product.subsubcategory_id;
  }
  
  router.push({
    path: '/recherche_de_produit_list',
    query: queryParams
  });
  
  clearSearch();
  closeMobileSearch();
};

const viewAllResults = () => {
  router.push({
    path: '/recherche_de_produit_list',
    query: { search: searchQuery.value.trim() }
  });
  clearSearch();
  closeMobileSearch();
};

function getBeforeChar(str, char) {
  const idx = str.indexOf(char);
  return idx !== -1 ? str.slice(0, idx) : str;
}

// New navigation functions for categories
const navigateToCategory = (category) => {
  console.log('Navigation vers catégorie:', category);
  router.push({
    path: '/recherche_de_produit_list',
    query: { category: category.id }
  });
  showCategoriesMenu.value = false;
};

const navigateToSubcategory = (subcategory) => {
  console.log('Navigation vers sous-catégorie:', subcategory);
  
  // Find parent category
  const parentCategory = activeCategory.value;
  
  const queryParams = {
    subcategory: subcategory.id
  };
  
  if (parentCategory) {
    queryParams.category = parentCategory.id;
  }
  
  router.push({
    path: '/recherche_de_produit_list',
    query: queryParams
  });
  showCategoriesMenu.value = false;
};

const navigateToSubSubcategory = (subSubcategory) => {
  console.log('Navigation vers sous-sous-catégorie:', subSubcategory);
  
  // Find parent category and subcategory
  const parentCategory = activeCategory.value;
  let parentSubcategory = null;
  
  if (parentCategory && parentCategory.subcategories) {
    parentSubcategory = parentCategory.subcategories.find(sub => 
      sub.sub_subcategories && sub.sub_subcategories.some(subsub => subsub.id === subSubcategory.id)
    );
  }
  
  const queryParams = {
    sub_subcategory: subSubcategory.id
  };
  
  if (parentCategory) {
    queryParams.category = parentCategory.id;
  }
  if (parentSubcategory) {
    queryParams.subcategory = parentSubcategory.id;
  }
  
  router.push({
    path: '/recherche_de_produit_list',
    query: queryParams
  });
  
  showCategoriesMenu.value = false;
  if (showMobileMenu.value) {
    toggleMobileMenu();
  }
};

const handleImageError = (event) => {
  event.target.src = 'https://www.svgrepo.com/show/422038/product.svg';
};

const getMatchTypeLabel = (matchType) => {
  const labels = {
    'product': 'Produit',
    'category': 'Catégorie',
    'subcategory': 'Sous-catégorie',
    'subsubcategory': 'Sous-sous-catégorie'
  };
  return labels[matchType] || 'Correspondance';
};

// Computed for filtered suggestions (static suggestions)
const filteredSuggestions = computed(() => {
  if (!searchQuery.value || searchQuery.value.length < 3) {
    return [];
  }
  
  const query = searchQuery.value.toLowerCase().trim();
  return searchSuggestions.value
    .filter(suggestion => 
      suggestion.toLowerCase().includes(query)
    )
    .slice(0, 8);
});

// Watch for searchQuery changes (for static suggestions)
watch(searchQuery, (newValue) => {
  if (newValue && newValue.length >= 3 && !showSearchResults.value) {
    showSuggestions.value = true;
  } else if (newValue.length < 3) {
    showSuggestions.value = false;
  }
});

// Handling clicks outside and escape keys
const handleClickOutside = (event) => {
  if (!event.target.closest('.search-container')) {
    showSearchResults.value = false;
    showSuggestions.value = false;
  }
  
  if (!event.target.closest('.all-categories')) {
    showCategoriesMenu.value = false;
  }
  // fermer dropdown langue si clic en dehors du conteneur
  if (languageSelectorRef.value && !languageSelectorRef.value.contains(event.target)) {
    showLanguageDropdown.value = false;
  }
  if (currencySelectorRef.value && !currencySelectorRef.value.contains(event.target)) {
    showCurrencyDropdown.value = false;
  }
};

const goToBoutique = () => {
  router.push('/boutique-admin/dashboard')
};
const goToAuthentication = () => {
  router.push('/login')
};


const handleEscapeKey = (event) => {
  if (event.key === 'Escape') {
    showSearchResults.value = false;
    showSuggestions.value = false;
    showCategoriesMenu.value = false;
    closeMobileSearch();
    if (showMobileMenu.value) {
      toggleMobileMenu();
    }
  }
};

const toggleImageSearch = () => {
  showImageSearch.value = !showImageSearch.value;
};

const handleFocus = () => {
  if (searchQuery.value.length >= 3 && searchResults.value.length === 0 && !isSearching.value) {
    showSuggestions.value = true;
  }
};

const hideSuggestions = () => {
  setTimeout(() => {
    showSuggestions.value = false;
    showSearchResults.value = false;
  }, 150);
};

const selectSuggestion = (suggestion) => {
  searchQuery.value = suggestion;
  showSuggestions.value = false;
  handleSearchInput(); // Trigger search with selected suggestion
  
  // Add to recent searches if mobile
  if (showMobileSearch.value && !recentSearches.value.includes(suggestion)) {
    recentSearches.value.unshift(suggestion);
    if (recentSearches.value.length > 5) {
      recentSearches.value.pop();
    }
  }
};

const handleLogout = async () => {
  try {
    await ElMessageBox.confirm(
      'Are you sure to logout ?',
      'Confirmation',
      {
        confirmButtonText: 'Confirm',
        cancelButtonText: 'Cancel',
        type: 'warning',
      },
    )

    localStorage.removeItem('authToken')
    sessionStorage.removeItem('authToken')
    localStorage.removeItem('user')
    sessionStorage.removeItem('user')
    localStorage.removeItem('rememberMe')

    ElMessage({
      type: 'success',
      message: 'Succesfully disconnect'
    })

    window.location.href = '/'
  } catch (error) {
    // Annulé
  }
}

const highlightMatch = (suggestion) => {
  if (!searchQuery.value) return suggestion;
  
  const query = searchQuery.value.toLowerCase().trim();
  const suggestionLower = suggestion.toLowerCase();
  const index = suggestionLower.indexOf(query);
  
  if (index === -1) return suggestion;
  
  const before = suggestion.substring(0, index);
  const match = suggestion.substring(index, index + query.length);
  const after = suggestion.substring(index + query.length);
  
  return `${before}<strong style="color: #fe7900; font-weight: 600;">${match}</strong>${after}`;
};

const setActiveCategory = (category) => {
  activeCategory.value = category;
};

const handleDrop = (event) => {
  console.log('File dropped:', event.dataTransfer.files);
};

const reloadCategories = () => {
  loadCategories();
};

// New functions for mobile version
const openMobileSearch = () => {
  showMobileSearch.value = true;
  document.body.style.overflow = 'hidden';
  
  // Focus on input after DOM render
  nextTick(() => {
    if (mobileSearchInput.value) {
      mobileSearchInput.value.focus();
    }
  });
};

const closeMobileSearch = () => {
  showMobileSearch.value = false;
  document.body.style.overflow = '';
};

const toggleMobileMenu = () => {
  showMobileMenu.value = !showMobileMenu.value;
  
  if (showMobileMenu.value) {
    document.body.style.overflow = 'hidden';
    // Reset menu level
    mobileMenuLevel.value = 1;
    selectedMobileCategory.value = null;
    selectedMobileSubcategory.value = null;
  } else {
    document.body.style.overflow = '';
  }
};

const selectMobileCategory = (category) => {
  if (category.subcategories && category.subcategories.length > 0) {
    selectedMobileCategory.value = category;
    mobileMenuLevel.value = 2;
  } else {
    // If no subcategories, navigate directly
    navigateToCategory(category);
    toggleMobileMenu();
  }
};

const selectMobileSubcategory = (subcategory) => {
  if (subcategory.sub_subcategories && subcategory.sub_subcategories.length > 0) {
    selectedMobileSubcategory.value = subcategory;
    mobileMenuLevel.value = 3;
  } else {
    // If no sub-subcategories, navigate directly
    navigateToSubcategory(subcategory);
    toggleMobileMenu();
  }
};

const mobileMenuBack = () => {
  if (mobileMenuLevel.value === 3) {
    mobileMenuLevel.value = 2;
    selectedMobileSubcategory.value = null;
  } else if (mobileMenuLevel.value === 2) {
    mobileMenuLevel.value = 1;
    selectedMobileCategory.value = null;
  }
};

const goToProfile = () => {
  router.push('/profile_client')
};  

// Lifecycle - Load categories on component mount
onMounted(async () => {
  console.log('[v0] Navbar with MyMemory Translation mounted')
  cart.loadCartFromDB();
  loadCache();
   loadCategories();
  
  const savedLang = localStorage.getItem('preferred-language')
  const userData = localStorage.getItem('user') || sessionStorage.getItem('user')
  const user = JSON.parse(userData)
  currentUser.value = {
      id: user.id,
      full_name: user.full_name,
      email: user.email,
      picture: user.picture,
      phone: user.phone,
      boutiques: user.boutiques || []
    }
    console.log('[v0] Current user:', currentUser.value)
  const savedSelectedLang = localStorage.getItem('selected-language')
  
  if (savedSelectedLang) {
    try {
      selectedLanguage.value = JSON.parse(savedSelectedLang)
    } catch (error) {
      console.error('[v0] Error parsing saved language:', error)
    }
  }
  
  if (savedLang === 'en') {
    currentLanguage.value = 'en'
    setTimeout(async () => {
      await translatePage()
    }, 2000)
  }
  
  
  // Listen for clicks to close dropdowns
  document.addEventListener('click', handleClickOutside);
  document.addEventListener('keydown', handleEscapeKey);
});

onUnmounted(() => {
  loadCategories();
  // Clean up event listeners
  document.removeEventListener('click', handleClickOutside);
  document.removeEventListener('keydown', handleEscapeKey);
  
  // Clean up timeouts and requests
  if (searchTimeout.value) {
    clearTimeout(searchTimeout.value);
  }
  
  if (searchAbortController.value) {
    searchAbortController.value.abort();
  }
  
  // Reset body overflow
  document.body.style.overflow = '';
});

// Expose functions for debugging
window.navbarDebug = {
  reloadCategories,
  categories: categories.value,
  isLoading: isLoadingCategories.value,
  error: categoriesError.value,
  searchResults: searchResults.value,
  performSearch: () => performProductSearch(searchQuery.value)
};
</script>

<style scoped>


.navbar-container {
  font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, sans-serif;
  width: 100%;
  position: relative;
  background: white;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
}

.top-navbar {
  background: white;
  border-bottom: 1px solid #f0f0f0;
}

.navbar-content {
  max-width: 1500px;
  margin: 0 auto;
  padding: 12px 20px;
  background: var(--primary-orange)
}

/* Version Desktop */
.desktop-navbar {
  display: flex;
  align-items: center;
  gap: 24px;
}

.logo-container {
  min-width: 150px;
}

.logo {
  height: 40px;
  width: 100%;
  object-fit: cover;

}

.search-container {
  flex: 1;
  display: flex;
  position: relative;
  max-width: 700px;
  box-shadow: 0 2px 8px rgba(255, 71, 71, 0.15);
  border-radius: 20px;
  overflow: visible;
}

.search-input {
  flex: 1;
  padding: 10px 20px;
  border: 2px solid #fe7900;
  border-right: none;
  font-size: 14px;
  outline: none;
  background: white;
  height: 40px;
  border-radius: 20px 0 0 20px;
  color: #333;
}
.search-input:focus{
  border: 2px solid #fe7900;
  box-shadow: 0 0 0 0.5px #fe7900;
}

.search-input::placeholder {
  color: #999;
}

.search-suggestions {
  position: absolute;
  top: 100%;
  left: 0;
  right: 0;
  background: white;
  border: 1px solid #e0e0e0;
  border-top: none;
  border-radius: 0 0 12px 12px;
  box-shadow: 0 8px 24px rgba(0, 0, 0, 0.15);
  z-index: 9999;
  max-height: 400px;
  overflow-y: auto;
  margin-top: -1px;
}

/* Styles pour les résultats de recherche de produits */
.search-loading {
  display: flex;
  align-items: center;
  gap: 12px;
  padding: 16px 20px;
  color: #666;
}

.loading-spinner-small {
  border: 2px solid #f3f3f3;
  border-top: 2px solid #fe7900;
  border-radius: 50%;
  width: 16px;
  height: 16px;
  animation: spin 1s linear infinite;
}

.search-results-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 12px 20px;
  background: #f8f9fa;
  border-bottom: 1px solid #e9ecef;
  font-size: 12px;
  color: #666;
}

.clear-search-btn {
  background: none;
  border: none;
  cursor: pointer;
  color: #999;
  padding: 4px;
  border-radius: 50%;
  transition: all 0.2s ease;
}

.clear-search-btn:hover {
  background: #f0f0f0;
  color: #fe7900;
}

.search-result-item {
  display: flex;
  align-items: center;
  gap: 12px;
  padding: 12px 20px;
  cursor: pointer;
  transition: background 0.2s ease;
  border-bottom: 1px solid #f5f5f5;
}

.search-result-item:last-child {
  border-bottom: none;
}

.search-result-item:hover {
  background: #f8f9fa;
}

.result-image {
  width: 48px;
  height: 48px;
  border-radius: 8px;
  overflow: hidden;
  flex-shrink: 0;
  background: #f5f5f5;
}

.result-image img {
  width: 100%;
  height: 100%;
  object-fit: cover;
}

.result-content {
  flex: 1;
  min-width: 0;
}

.result-name {
  font-weight: 500;
  font-size: 14px;
  color: #333;
  margin-bottom: 4px;
  line-height: 1.3;
}

.result-category {
  display: flex;
  gap: 4px;
  margin-bottom: 4px;
}

.category-badge {
  background: #e9ecef;
  color: #666;
  font-size: 11px;
  padding: 2px 6px;
  border-radius: 10px;
}

.search-results-footer {
  padding: 12px 20px;
  background: #f8f9fa;
  border-top: 1px solid #e9ecef;
}

.view-all-btn {
  width: 100%;
  background: none;
  border: 1px solid #fe7900;
  color: #fe7900;
  padding: 8px 16px;
  border-radius: 20px;
  cursor: pointer;
  font-size: 13px;
  transition: all 0.2s ease;
}

.view-all-btn:hover {
  background: #fe7900;
  color: white;
}

.no-results {
  display: flex;
  flex-direction: column;
  align-items: center;
  padding: 24px 20px;
  color: #666;
  text-align: center;
}

.no-results svg {
  margin-bottom: 8px;
  opacity: 0.5;
}

.no-results small {
  margin-top: 4px;
  color: #999;
  font-size: 12px;
}

/* Styles pour les suggestions statiques */
.suggestions-header {
  padding: 8px 20px;
  background: #f8f9fa;
  border-bottom: 1px solid #e9ecef;
  font-size: 12px;
  color: #666;
  font-weight: 500;
}

.suggestion-item {
  display: flex;
  align-items: center;
  gap: 12px;
  padding: 12px 20px;
  cursor: pointer;
  transition: background 0.2s ease;
  color: #333;
  border-bottom: 1px solid #f5f5f5;
}

.suggestion-item:last-child {
  border-bottom: none;
}

.suggestion-item:hover {
  background: #f8f9fa;
}

.suggestion-item svg {
  color: #999;
  flex-shrink: 0;
}

.search-image-btn {
  position: absolute;
  right: 15%;
  top: 25%;
  background: none;
  border: none;
  cursor: pointer;
  color: #666;
  border-radius: 50%;
  transition: all 0.2s ease;
  z-index: 10;
}

.search-image-btn:hover {
  color: #fe7900;
}

.search-btn {
  background: linear-gradient(135deg, #fe7900, #fe4810);
  color: white;
  border: none;
  padding: 0 20px;
  cursor: pointer;
  display: flex;
  align-items: center;
  justify-content: center;
  transition: all 0.2s ease;
  height: 40px;
  border-radius: 0 20px 20px 0;
}

.search-btn:hover {
  background: linear-gradient(135deg, #fe5900, #f93d0a);
}

.user-actions {
  display: flex;
  align-items: center;
  gap: 20px;
  font-size: 13px;
}

.app-download, .language-selector, .user-account, .cart {
  display: flex;
  align-items: center;
  gap: 6px;
  cursor: pointer;
  padding: 8px 22px;
  border-radius: 25px;
  transition: all 0.2s ease;
  color: #333;
}

.app-download:hover, .language-selector:hover, .user-account:hover, .cart:hover {
  background: #f8f8f8;
  color: #fe7900;
}

.flag {
  width: 24px;
  height: 18px;
  border-radius: 2px;
  object-fit: cover;
}

.cart {
  position: relative;
  font-weight: 500;
}

.cart-badge {
  position: absolute;
  top: -8px;
  right: 0px;
  background: linear-gradient(135deg, #fe7900, #ff6b6b);
  color: white;
  border-radius: 50%;
  width: 20px;
  height: 20px;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 11px;
  font-weight: bold;
  box-shadow: 0 2px 4px rgba(255, 71, 71, 0.3);
}

.cart-count {
  padding-left: 6px;
  padding-right: 4px;
  width: 30px;

}

/* Version Mobile */
.mobile-navbar {
  display: none;
  flex-direction: column;
  gap: 12px;
}

.mobile-top-row {
  display: flex;
  align-items: center;
  justify-content: space-between;
}

.mobile-menu-btn {
  background: none;
  border: none;
  color: #333;
  padding: 8px;
  border-radius: 8px;
  cursor: pointer;
  transition: background 0.2s ease;
}

.mobile-menu-btn:hover {
  background: #f5f5f5;
}

.mobile-actions {
  display: flex;
  align-items: center;
  gap: 12px;
}

.mobile-cart-btn {
  background: none;
  border: none;
  color: #333;
  padding: 8px;
  border-radius: 8px;
  cursor: pointer;
  position: relative;
  transition: background 0.2s ease;
}

.mobile-cart-btn:hover {
  background: #f5f5f5;
}

.mobile-search-row {
  cursor: pointer;
}

.mobile-search-bar {
  display: flex;
  align-items: center;
  gap: 12px;
  background: #f5f5f5;
  color: #666;
  border-radius: 25px;
  padding: 12px 16px;
  transition: background 0.2s ease;
}

.mobile-search-bar:hover {
  background: #eeeeee;
}

.mobile-search-placeholder {
  flex: 1;
  color: #999;
  font-size: 14px;
}

.mobile-camera-btn {
  background: none;
  border: none;
  color: #666;
  padding: 4px;
  cursor: pointer;
}

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

/* Mobile Menu Modal */
.mobile-menu-modal {
  position: fixed;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: white;
  z-index: 10000;
  display: flex;
  flex-direction: column;
  overflow-y: auto;
}

.mobile-menu-header {
  display: flex;
  align-items: center;
  gap: 16px;
  padding: 16px;
  border-bottom: 1px solid #f0f0f0;
  background: white;
  position: sticky;
  top: 0;
  z-index: 10001;
}

.mobile-close-btn {
  background: none;
  border: none;
  color: #333;
  padding: 8px;
  border-radius: 50%;
  cursor: pointer;
  transition: background 0.2s ease;
}

.mobile-close-btn:hover {
  background: #f5f5f5;
}

.mobile-menu-header h3 {
  font-size: 18px;
  font-weight: 600;
  color: #333;
  margin: 0;
}

.mobile-menu-user {
  display: flex;
  align-items: center;
  gap: 16px;
  padding: 20px 16px;
  background: linear-gradient(135deg, #fe7900, #ff6b6b);
  color: white;
}

.mobile-user-avatar {
  width: 48px;
  height: 48px;
  background: rgba(255, 255, 255, 0.2);
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  flex-shrink: 0;
}

.mobile-user-info {
  flex: 1;
}

.mobile-user-welcome {
  font-size: 16px;
  font-weight: 500;
  margin-bottom: 4px;
}

.mobile-user-actions {
  display: flex;
  align-items: center;
  gap: 8px;
}

.mobile-login-btn, .mobile-register-btn {
  color: white;
  text-decoration: none;
  font-size: 14px;
  font-weight: 500;
}

.mobile-separator {
  color: rgba(255, 255, 255, 0.7);
}

.mobile-menu-categories {
  flex: 1;
  padding: 16px;
}

.mobile-menu-title {
  font-size: 18px;
  font-weight: 600;
  color: #333;
  margin-bottom: 16px;
  padding-bottom: 8px;
  border-bottom: 1px solid #f0f0f0;
}

.mobile-categories-navigation {
  min-height: 300px;
}

.mobile-category-level {
  display: flex;
  flex-direction: column;
  gap: 8px;
}

.mobile-category-back {
  display: flex;
  align-items: center;
  gap: 12px;
  padding: 12px;
  color: #fe7900;
  cursor: pointer;
  border-radius: 12px;
  transition: background 0.2s ease;
  margin-bottom: 8px;
}

.mobile-category-back:hover {
  background: #f8f9fa;
}

.mobile-subcategory-title {
  font-size: 16px;
  font-weight: 600;
  color: #333;
  padding: 12px;
  background: #f8f9fa;
  border-radius: 12px;
  margin-bottom: 8px;
}

.mobile-category-item {
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 16px 12px;
  border-radius: 12px;
  background: #f8f9fa;
  cursor: pointer;
  transition: background 0.2s ease;
}

.mobile-category-item:hover {
  background: #f0f0f0;
}

.mobile-category-content {
  display: flex;
  align-items: center;
  gap: 12px;
  flex: 1;
}

.mobile-category-icon {
  font-size: 20px;
  width: 24px;
  text-align: center;
}

.navbar-h {
  background-color: black;
  padding: 3px;
  color: white
}

.mobile-category-name {
  font-size: 14px;
  font-weight: 500;
  color: #333;
}

.mobile-menu-links {
  padding: 16px;
  border-top: 1px solid #f0f0f0;
}

.mobile-menu-link {
  display: flex;
  align-items: center;
  gap: 16px;
  padding: 16px 12px;
  color: #333;
  text-decoration: none;
  border-radius: 12px;
  transition: background 0.2s ease;
  margin-bottom: 4px;
}

.mobile-menu-link:hover {
  background: #f8f9fa;
}

.mobile-menu-footer {
  padding: 16px;
  border-top: 1px solid #f0f0f0;
  background: #f8f9fa;
}

.mobile-language, .mobile-app-download {
  display: flex;
  align-items: center;
  gap: 12px;
  padding: 12px;
  margin-bottom: 8px;
  color: #666;
  font-size: 14px;
}

.categories-navbar {
  background: #f8f9fa;
  border-bottom: 1px solid #e9ecef;
  width: 100%;
}

.categories-content {
  max-width: 1500px;
  margin: 0 auto;
  display: flex;
  padding: 0 20px;
}

.all-categories {
  display: flex;
  align-items: center;
  gap: 8px;
  font-weight: 600;
  cursor: pointer;
  padding: 16px 20px 16px 0;
  color: #333;
  border-right: 1px solid #e9ecef;
  margin-right: 20px;
  transition: color 0.2s ease;
  position: relative;
}

.all-categories:hover {
  color: #fe7900;
}

/* Unified Categories Dropdown */
.unified-categories-dropdown {
  position: absolute;
  top: 100%;
  background: white;
  border: 1px solid #e0e0e0;
  border-radius: 12px;
  box-shadow: 0 12px 32px rgba(0, 0, 0, 0.15);
  z-index: 1000;
  display: flex;
  overflow: hidden;
  animation: slideDown 0.3s ease;
}

@keyframes slideDown {
  from {
    opacity: 0;
    transform: translateX(0%) translateY(-10px);
  }
  to {
    opacity: 1;
    transform: translateX(0%) translateY(0);
  }
}

@keyframes spin {
  0% { transform: rotate(0deg); }
  100% { transform: rotate(360deg); }
}

.categories-section {
  width: 320px;
  background: #f8f9fa;
  border-right: 1px solid #e9ecef;
  display: flex;
  flex-direction: column;
}

.category-item {
  padding: 0;
  cursor: pointer;
  border-bottom: 1px solid #e9ecef;
}

.category-item:last-child {
  border-bottom: none;
}

.category-item.active .category-info {
  background: white;
  color: #fe7900;
  border-right: 3px solid #fe7900;
}

.category-info {
  display: flex;
  align-items: center;
  gap: 12px;
  padding: 16px 20px;
  transition: all 0.2s ease;
}

.category-item:hover .category-info {
  background: white;
  color: #fe7900;
}

.category-icon {
  flex: 1;
  font-size: 20px;
  width: 24px;
  text-align: center;
}

.category-name {
  flex: 1;
  font-weight: 500;
}

.subcategories-section {
  flex: 1;
  background: white;
  padding: 24px;
  min-height: 400px;
  display: flex;
  align-items: flex-start;
  justify-content: flex-start;
}

.subcategories-content {
  width: 100%;
}

.subcategories-content h4 {
  margin: 0 0 24px 0;
  font-size: 20px;
  font-weight: 600;
  color: #333;
  border-bottom: 2px solid #f0f0f0;
  padding-bottom: 16px;
}

.subcategories-columns {
  display: flex;
  flex-wrap: wrap;
  gap: 30px;
}

.subcategory-column {
  flex: 1;
  min-width: 200px;
  max-width: 300px;
}

.subcategory-title {
  font-weight: 600;
  font-size: 16px;
  color: #333;
  margin-bottom: 12px;
  padding-bottom: 8px;
  border-bottom: 1px solid #f0f0f0;
  cursor: pointer;
  transition: color 0.2s ease;
}

.subcategory-title:hover {
  color: #fe7900;
}

.sub-subcategories-list {
  list-style: none;
  padding: 0;
  margin: 0;
}

.sub-subcategory-item {
  padding: 8px 0;
  font-size: 14px;
  color: #666;
  cursor: pointer;
  transition: all 0.2s ease;
  border-radius: 4px;
  padding-left: 8px;
  margin-left: -8px;
}

.sub-subcategory-item:hover {
  color: #fe7900;
  background: rgba(255, 71, 71, 0.05);
  transform: translateX(5px);
}

.subcategories-placeholder {
  display: flex;
  align-items: center;
  justify-content: center;
  height: 100%;
  width: 100%;
}

.placeholder-content {
  text-align: center;
  color: #999;
}

.placeholder-content svg {
  margin-bottom: 16px;
  opacity: 0.5;
}

.placeholder-content p {
  margin: 0;
  font-size: 16px;
  font-weight: 500;
}

.main-nav {
  display: flex;
  gap: 0;
  overflow-x: auto;
  white-space: nowrap;
  flex: 1;
  scrollbar-width: none;
}

.main-nav::-webkit-scrollbar {
  display: none;
}

.nav-item {
  text-decoration: none;
  color: #555;
  font-size: 14px;
  padding: 16px 20px;
  display: flex;
  align-items: center;
  gap: 4px;
  transition: all 0.2s ease;
  position: relative;
  font-weight: 500;
}

.nav-item:hover {
  color: #fe7900;
  background: rgba(255, 71, 71, 0.05);
}

.nav-item.featured::after {
  content: 'New';
  position: absolute;
  top: 4px;
  right: 8px;
  background: #fe7900;
  color: white;
  font-size: 10px;
  padding: 2px 6px;
  border-radius: 8px;
  font-weight: bold;
}

.nav-item.hot::after {
  content: '🔥';
  position: absolute;
  top: 8px;
  right: 8px;
  font-size: 12px;
}

.image-search-modal {
  position: fixed;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: rgba(0, 0, 0, 0.5);
  display: flex;
  align-items: center;
  justify-content: center;
  z-index: 10000;
  backdrop-filter: blur(4px);
}

.modal-content {
  background: white;
  border-radius: 16px;
  width: 90%;
  max-width: 500px;
  box-shadow: 0 20px 40px rgba(0, 0, 0, 0.15);
  overflow: hidden;
}

.modal-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 20px 24px;
  border-bottom: 1px solid #f0f0f0;
  background: #f8f9fa;
}

.modal-header h3 {
  margin: 0;
  font-size: 18px;
  font-weight: 600;
  color: #333;
}

.close-btn {
  background: none;
  border: none;
  cursor: pointer;
  padding: 8px;
  border-radius: 50%;
  transition: background 0.2s ease;
  color: #666;
}

.close-btn:hover {
  background: #e9ecef;
}

.modal-body {
  padding: 24px;
}

.modal-body p {
  margin: 0 0 20px 0;
  color: #666;
  line-height: 1.5;
}

.upload-area {
  margin: 20px 0;
  border: 2px dashed #ddd;
  border-radius: 12px;
  padding: 40px 20px;
  transition: all 0.2s ease;
}

.upload-area:hover {
  border-color: #fe7900;
  background: rgba(255, 71, 71, 0.02);
}

.upload-placeholder {
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 12px;
  color: #666;
}

.upload-text {
  font-size: 16px;
  margin: 0;
}

.upload-or {
  margin: 0;
  color: #999;
}

.upload-btn {
  background: linear-gradient(135deg, #fe7900, #ff6b6b);
  color: white;
  border: none;
  border-radius: 24px;
  padding: 12px 24px;
  cursor: pointer;
  font-weight: 600;
  display: flex;
  align-items: center;
  gap: 8px;
  transition: all 0.2s ease;
  box-shadow: 0 4px 12px rgba(255, 71, 71, 0.3);
}

.upload-btn:hover {
  transform: translateY(-2px);
  box-shadow: 0 6px 16px rgba(255, 71, 71, 0.4);
}

.search-tip {
  font-size: 12px;
  color: #999;
  margin: 20px 0 0 0;
  text-align: center;
}

/* Styles spécifiques pour le chargement, l'erreur et le bouton de réessai */
.loading-categories {
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  padding: 20px;
  color: #999;
}

.categories-error {
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  padding: 20px;
  color: #fe7900;
  text-align: center;
}

.categories-error svg {
  margin-bottom: 10px;
}

.retry-btn {
  background: none;
  border: 1px solid #fe7900;
  color: #fe7900;
  padding: 8px 16px;
  border-radius: 20px;
  cursor: pointer;
  transition: all 0.2s ease;
  margin-top: 10px;
}

.retry-btn:hover {
  background: #ffe6e6;
}

/* Media Queries */
@media (max-width: 768px) {
  .desktop-navbar {
    display: none;
  }
  
  .mobile-navbar {
    display: flex;
  }
  
  .categories-navbar {
    display: none;
  }
  
  .navbar-content {
    padding: 12px 16px;
  }
}

@media (min-width: 769px) {
  .mobile-navbar {
    display: none !important;
  }
  
  .desktop-navbar {
    display: flex;
  }
}

@media (max-width: 1700px) {
  .unified-categories-dropdown {
    width: 1400px;
  }
  
  .subcategories-columns {
    gap: 20px;
  }
  
  .subcategory-column {
    min-width: 180px;
    max-width: 250px;
  }
}

@media (max-width: 1500px) {
  .unified-categories-dropdown {
    width: 1200px;
  }
  
  .subcategories-columns {
    gap: 15px;
  }
  
  .subcategory-column {
    min-width: 160px;
    max-width: 220px;
  }
}

@media (max-width: 1200px) {
  .navbar-content {
    max-width: 100%;
    padding: 12px 16px;
  }
  
  .categories-content {
    max-width: 100%;
    padding: 0 16px;
  }
  
  .app-download {
    display: none;
  }
  
  .unified-categories-dropdown {
    width: 95vw;
    left: 2.5vw;
    transform: none;
  }
  
  .categories-section {
    width: 280px;
  }
  
  .subcategories-columns {
    flex-direction: column;
    gap: 20px;
  }
  
  .subcategory-column {
    max-width: 100%;
  }
}

.language-selector {
  position: relative;
  display: inline-flex;
  align-items: center;
  gap: 6px;
  padding: 6px 10px;
  background: #f8f8f8;
  border: 1px solid #ddd;
  border-radius: 25px;
  cursor: pointer;
  user-select: none;
  transition: opacity 0.3s ease;
}

.language-selector img.flag {
  width: 20px;
  height: auto;
}

.language-selector svg {
  margin-left: 4px;
  transition: transform 0.3s;
}

.language-selector.open svg {
  transform: rotate(180deg);
}

/* Added disabled state styling */
.language-selector.opacity-50 {
  opacity: 0.5;
  cursor: not-allowed;
}

/* Dropdown */
.language-dropdown {
  position: absolute;
  top: 110%;
  left: 0;
  width: 160px;
  background: white;
  border: 1px solid #ddd;
  border-radius: 6px;
  box-shadow: 0 2px 8px rgba(0,0,0,0.15);
  display: none;
  z-index: 10;
}

.language-selector.open .language-dropdown {
  display: block;
}

.language-dropdown div {
  display: flex;
  align-items: center;
  gap: 6px;
  padding: 8px 10px;
  cursor: pointer;
}

.language-dropdown div:hover {
  background: #f2f2f2;
}

.language-dropdown img.flag {
  width: 18px;
}

/* Added styles for translation stats tooltip */
.absolute.top-full {
  background: white;
  border: 1px solid #e5e7eb;
  border-radius: 8px;
  box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
  font-size: 12px;
  color: #6b7280;
  z-index: 1000;
}
</style>
