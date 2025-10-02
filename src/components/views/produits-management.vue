<template>
    <div class="min-h-screen bg-gray-50 relative overflow-hidden">
      <!-- Animation de fond luxueuse avec couleurs plus intenses -->
      <div class="absolute inset-0 overflow-hidden pointer-events-none z-0">
        <!-- Gradient animé principal -->
        <div class="absolute inset-0 bg-gradient-to-br from-gray-50 via-white to-gray-100"></div>
        
        <!-- Orbes flottants avec couleurs plus intenses -->
        <div class="absolute -top-20 -left-20 w-96 h-96 bg-gradient-to-br from-orange-200/60 to-orange-300/40 rounded-full blur-3xl animate-float-slow"></div>
        <div class="absolute top-1/4 -right-20 w-80 h-80 bg-gradient-to-br from-blue-200/50 to-indigo-200/35 rounded-full blur-3xl animate-float-reverse"></div>
        <div class="absolute -bottom-20 left-1/3 w-72 h-72 bg-gradient-to-br from-purple-200/45 to-pink-200/35 rounded-full blur-3xl animate-float-diagonal"></div>
        <div class="absolute top-1/2 right-1/4 w-64 h-64 bg-gradient-to-br from-emerald-200/40 to-teal-200/30 rounded-full blur-3xl animate-float-slow-reverse"></div>
        
        <!-- Particules plus visibles -->
        <div class="absolute top-1/3 left-1/4 w-3 h-3 bg-orange-400/70 rounded-full animate-pulse-slow shadow-lg"></div>
        <div class="absolute top-2/3 right-1/4 w-2.5 h-2.5 bg-blue-400/60 rounded-full animate-pulse-delayed shadow-lg"></div>
        <div class="absolute top-1/2 left-2/3 w-2 h-2 bg-purple-400/50 rounded-full animate-pulse-slow shadow-lg"></div>
        <div class="absolute top-1/4 right-1/3 w-2 h-2 bg-emerald-400/50 rounded-full animate-pulse-delayed-2 shadow-lg"></div>
        
        <!-- Lignes géométriques plus visibles -->
        <div class="absolute top-0 left-1/4 w-px h-40 bg-gradient-to-b from-transparent via-orange-300/40 to-transparent animate-slide-down"></div>
        <div class="absolute top-1/3 right-1/3 w-32 h-px bg-gradient-to-r from-transparent via-blue-300/35 to-transparent animate-slide-right"></div>
        <div class="absolute bottom-1/4 left-1/2 w-px h-32 bg-gradient-to-b from-transparent via-purple-300/30 to-transparent animate-slide-up"></div>
        
        <!-- Formes géométriques supplémentaires -->
        <div class="absolute top-3/4 left-1/6 w-16 h-16 bg-gradient-to-br from-orange-300/30 to-yellow-300/20 rotate-45 animate-rotate-slow"></div>
        <div class="absolute top-1/6 right-1/6 w-12 h-12 bg-gradient-to-br from-blue-300/25 to-cyan-300/20 rounded-lg animate-float-small"></div>
      </div>
  
      <!-- Container responsive avec largeur adaptative -->
      <div class="w-full max-w-[1650px] mx-auto px-4 sm:px-6 py-4 sm:py-8 relative z-10">
        <!-- Breadcrumb -->
        <div class="flex items-center text-sm text-gray-500 mb-4 sm:mb-6">
          <router-link to="/" class="hover:text-gray-700">
          <HomeIcon  class="w-4 h-4 sm:w-5 sm:h-5" />
        </router-link>
          <span class="mx-2">/</span>
          <span class="font-medium text-gray-700 truncate">Gestion des Produits</span>
        </div>
  
        <!-- Header responsive -->
        <div class="flex flex-col space-y-4 lg:flex-row lg:items-center lg:justify-between lg:space-y-0 mb-6 sm:mb-8">
          <div>
            <h1 class="text-2xl sm:text-3xl font-bold text-gray-900 mb-2">Gestion des Produits</h1>
            <p class="text-sm sm:text-base text-gray-600">Validation et modération des produits et boosts</p>
          </div>
          
          <!-- Actions mobiles optimisées -->
          <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-2 sm:gap-3">
            <!-- Export dropdown -->
            <div class="relative">
              <button 
                @click="showExportDropdown = !showExportDropdown"
                class="w-full sm:w-auto inline-flex items-center justify-center px-3 sm:px-4 py-2 rounded-lg text-sm font-medium btn-degrade-orange"
              >
                 <DownloadIcon class="w-4 h-4 mr-2" />
                <span >Exporter</span>
                <ChevronDownIcon class="w-4 h-4 ml-2" />
              </button>
              <div v-if="showExportDropdown" class="origin-top-right absolute right-0 w-50 mt-2 ring-1 ring-gray-400 rounded-md shadow-lg bg-white p-2">
                <div class="py-1" role="menu">
                </div>
                <div  role="menu">
                <button @click="exportToPDF" class="flex items-center text-sm mb-2 text-gray-700 hover:bg-gray-100 btn-gray" role="menuitem">
                   <FileTextIcon   class="w-4 h-4 mr-2 error-color" />
                  Exporter en PDF
                </button>
                <button @click="exportToExcel" class="flex items-center text-sm text-gray-700 hover:bg-gray-100 btn-gray" role="menuitem">
                  <FileTextIcon class="w-4 h-4 mr-1 green-color" />
                  Exporter en Excel
                </button>
              </div>
              </div>
            </div>
  
            <!-- Refresh button -->
            <button 
              @click="loadAllData"
              :disabled="dataLoading"
              class="w-full sm:w-auto inline-flex items-center justify-center px-3 sm:px-4 py-2 rounded-lg shadow-sm text-sm font-medium transition-all submit-btn"
            >
              <RefreshCcw class="w-4 h-4 mr-2" />
              <span class="hidden sm:inline">{{ dataLoading ? 'Chargement...' : 'Actualiser' }}</span>
              <span class="sm:hidden">{{ dataLoading ? 'Chargement...' : 'Actualiser' }}</span>
            </button>
  
            <!-- Boutiques Management Button -->
            <router-link 
              to="/admin/boutiques"
              class="w-full sm:w-auto inline-flex items-center justify-center px-3 sm:px-4 py-2  font-medium   transition-colors btn-whatsapp"
            >
              <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
              </svg>
              <span class="hidden sm:inline">Boutiques</span>
              <span class="sm:hidden">Boutiques</span>
            </router-link>
          </div>
        </div>
  
        <!-- Loading State -->
        <div v-if="dataLoading" class="mb-6 sm:mb-8">
          <div class="bg-white rounded-lg p-6 sm:p-8 text-center shadow-lg">
            <div class="inline-block animate-spin rounded-full h-10 w-10 sm:h-12 sm:w-12 border-4 border-orange-500 border-t-transparent"></div>
            <p class="mt-4 text-gray-600 font-medium text-sm sm:text-base">Chargement des données...</p>
          </div>
        </div>
  
        <!-- Error State -->
        <div v-if="hasError" class="mb-6 sm:mb-8">
          <div class="bg-gradient-to-r from-red-50 to-red-100 border border-red-200 rounded-lg p-4 sm:p-6 shadow-lg">
            <div class="flex flex-col sm:flex-row sm:items-center gap-4">
              <div class="w-10 h-10 bg-red-100 rounded-full flex items-center justify-center flex-shrink-0">
                <svg class="w-5 h-5 error-color" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"/>
                </svg>
              </div>
              <div class="flex-1 min-w-0">
                <p class="error-color font-medium text-sm sm:text-base">{{ error }}</p>
              </div>
              <button @click="loadAllData" class="w-full sm:w-auto px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors font-medium text-sm">
                Réessayer
              </button>
            </div>
          </div>
        </div>
  
        <!-- Stats Cards responsive -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-6 mb-6 sm:mb-8" v-if="!dataLoading">
          <!-- Produits en Attente -->
          <div class="bg-white overflow-hidden shadow-lg rounded-lg border border-gray-100 hover:shadow-xl transition-all duration-300">
            <div class="px-4 sm:px-6 py-4 sm:py-6">
              <div class="flex items-center mb-3 sm:mb-4">
                <div class="w-10 h-10 sm:w-12 sm:h-12 bg-gradient-to-br from-yellow-200 to-yellow-300 rounded-lg flex items-center justify-center mr-3 sm:mr-4 flex-shrink-0">
                  <svg class="w-5 h-5 sm:w-6 sm:h-6 text-black" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                  </svg>
                </div>
                <div class="min-w-0">
                  <p class="text-xs sm:text-sm text-gray-600">Produits en Attente</p>
                  <p class="text-xl sm:text-2xl font-bold text-gray-900">{{ stats.pending_products || 0 }}</p>
                </div>
              </div>
              <div class="space-y-2">
                <div class="flex justify-between text-xs text-gray-500">
                  <span class="truncate">À valider</span>
                  <span class="text-yellow-600 flex-shrink-0">Urgent</span>
                </div>
                <div class="w-full bg-gray-200 rounded-full h-2">
                  <div class="bg-gradient-to-r from-yellow-500 to-yellow-600 h-2 rounded-full transition-all duration-300" :style="{ width: getPendingPercentage() + '%' }"></div>
                </div>
              </div>
            </div>
          </div>
  
          <!-- Boosts en Attente -->
          <div class="bg-white overflow-hidden shadow-lg rounded-lg border border-gray-100 hover:shadow-xl transition-all duration-300">
            <div class="px-4 sm:px-6 py-4 sm:py-6">
              <div class="flex items-center mb-3 sm:mb-4">
                <div class="w-10 h-10 sm:w-12 sm:h-12 bg-gradient-to-br from-blue-200 to-blue-300 rounded-lg flex items-center justify-center mr-3 sm:mr-4 flex-shrink-0">
                  <svg class="w-5 h-5 sm:w-6 sm:h-6 text-black" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                  </svg>
                </div>
                <div class="min-w-0">
                  <p class="text-xs sm:text-sm text-gray-600">Boosts en Attente</p>
                  <p class="text-xl sm:text-2xl font-bold text-gray-900">{{ stats.pending_boosts || 0 }}</p>
                </div>
              </div>
              <div class="space-y-2">
                <div class="flex justify-between text-xs text-gray-500">
                  <span class="truncate">À valider</span>
                  <span class="blue-color flex-shrink-0">Priorité</span>
                </div>
                <div class="w-full bg-gray-200 rounded-full h-2">
                  <div class="bg-gradient-to-r from-blue-500 to-blue-600 h-2 rounded-full transition-all duration-300" :style="{ width: getBoostPercentage() + '%' }"></div>
                </div>
              </div>
            </div>
          </div>
  
          <!-- Produits Validés -->
          <div class="bg-white overflow-hidden shadow-lg rounded-lg border border-gray-100 hover:shadow-xl transition-all duration-300">
            <div class="px-4 sm:px-6 py-4 sm:py-6">
              <div class="flex items-center mb-3 sm:mb-4">
                <div class="w-10 h-10 sm:w-12 sm:h-12 bg-gradient-to-br from-green-200 to-green-300 rounded-lg flex items-center justify-center mr-3 sm:mr-4 flex-shrink-0">
                  <svg class="w-5 h-5 sm:w-6 sm:h-6 text-black" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                  </svg>
                </div>
                <div class="min-w-0">
                  <p class="text-xs sm:text-sm text-gray-600">Produits Validés</p>
                  <p class="text-xl sm:text-2xl font-bold text-gray-900">{{ stats.active_products || 0 }}</p>
                </div>
              </div>
              <div class="space-y-2">
                <div class="flex justify-between text-xs text-gray-500">
                  <span class="truncate">Ce mois: +{{ stats.approved_growth || 0 }}%</span>
                  <span class="text-green-600 flex-shrink-0">Croissance</span>
                </div>
                <div class="w-full bg-gray-200 rounded-full h-2">
                  <div class="bg-gradient-to-r from-green-500 to-green-600 h-2 rounded-full transition-all duration-300" style="width: 85%"></div>
                </div>
              </div>
            </div>
          </div>
  
          <!-- Produits Rejetés -->
          <div class="bg-white overflow-hidden shadow-lg rounded-lg border border-gray-100 hover:shadow-xl transition-all duration-300">
            <div class="px-4 sm:px-6 py-4 sm:py-6">
              <div class="flex items-center mb-3 sm:mb-4">
                <div class="w-10 h-10 sm:w-12 sm:h-12 bg-gradient-to-br from-red-200 to-red-300 rounded-lg flex items-center justify-center mr-3 sm:mr-4 flex-shrink-0">
                  <svg class="w-5 h-5 sm:w-6 sm:h-6 text-black" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                  </svg>
                </div>
                <div class="min-w-0">
                  <p class="text-xs sm:text-sm text-gray-600">Produits Rejetés</p>
                  <p class="text-xl sm:text-2xl font-bold text-gray-900">{{ stats.rejected_products || 0 }}</p>
                </div>
              </div>
              <div class="space-y-2">
                <div class="flex justify-between text-xs text-gray-500">
                  <span class="truncate">Taux: {{ stats.rejection_rate || 0 }}%</span>
                  <span class="text-red-600 flex-shrink-0">Qualité</span>
                </div>
                <div class="w-full bg-gray-200 rounded-full h-2">
                  <div class="bg-gradient-to-r from-red-500 to-red-600 h-2 rounded-full transition-all duration-300" style="width: 25%"></div>
                </div>
              </div>
            </div>
          </div>
        </div>
  
        <!-- Main Content Card responsive -->
        <div class="bg-white shadow-lg rounded-lg overflow-hidden border border-gray-100" v-if="!dataLoading">
          <!-- Filter Tabs responsive -->
          <div class="px-4 sm:px-6 py-3 sm:py-4 border-b border-gray-200 bg-gray-50">
            <div class="flex flex-wrap gap-1 sm:gap-2">
              <button 
                v-for="tab in filterTabs" 
                :key="tab.value"
                @click="handleFilterChange(tab.value)"
                :class="[
                  'px-2 sm:px-4 py-1.5 sm:py-2 rounded-lg transition-all duration-200 text-xs sm:text-sm font-medium',
                  activeFilter === tab.value 
                    ? 'btn-degrade-orange' 
                  : 'btn-gray'
                ]"
                :style="activeFilter === tab.value ? 'btn-degrade-orange' : ''"
              >
                <span class="hidden sm:inline">{{ tab.label }} ({{ filterCounts[tab.value] }})</span>
                <span class="sm:hidden">{{ tab.label.split(' ')[0] }} ({{ filterCounts[tab.value] }})</span>
              </button>
            </div>
          </div>
  
          <!-- Search and Filters responsive -->
          <div class="px-4 sm:px-6 py-3 sm:py-4 border-b border-gray-200 bg-white">
            <div class="flex flex-col space-y-3 sm:space-y-0 sm:flex-row sm:items-center sm:justify-between sm:gap-4">
              <div class="relative flex-1 max-w-full sm:max-w-md">
                <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                  <Search  class="w-4 h-4 text-gray-500" />
                </div>
                <input 
                  v-model="searchQuery"
                  type="text" 
                  class="input-style"
                  placeholder="Rechercher par nom, boutique, catégorie..."
                  @input="handleSearch"
                >
              </div>
              <div class="flex items-center gap-2 sm:gap-3">
              <div class="w-45">
                <select v-model="statusFilter" @change="loadProducts" class="input-style"style="padding-right: 30px; padding-left: 10px;">
                  <option value="">Tous les statuts</option>
                  <option value="En attente">En attente</option>
                  <option value="Actif">Validé</option>
                  <option value="Rejeté">Rejeté</option>
                  <option value="Brouillon">Brouillon</option>
                </select>
              </div>
              <div>
  
                <select v-model="boutiqueFilter" @change="loadProducts" class="input-style" style="padding-right: 30px; padding-left: 10px;">
                  <option value="">Toutes les boutiques</option>
                  <option v-for="boutique in availableBoutiques" :key="boutique.id" :value="boutique.id">
                    {{ boutique.name }}
                  </option>
                </select>
              </div>
                <button v-if="hasActiveFilters" @click="clearFilters" class="btn-deconnexion w-30">
                  <X class="w-4 h-4 sm:hidden" />
                  <span class="hidden sm:inline">Effacer filtres</span>
                </button>
              </div>
            </div>
          </div>
  
          <!-- Products Table responsive avec colonnes ajustées -->
          <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
              <thead class="bg-gray-50">
                <tr>
                  <th scope="col" class="px-3 sm:px-4 py-2 sm:py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-1/6">Produit</th>
                  <th scope="col" class="px-3 sm:px-4 py-2 sm:py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Boutique</th>
                  <th scope="col" class="hidden md:table-cell px-3 sm:px-4 py-2 sm:py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Catégorie</th>
                  <th scope="col" class="px-3 sm:px-4 py-2 sm:py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Prix</th>
                  <th scope="col" class="px-3 sm:px-4 py-2 sm:py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Boost</th>
                  <th scope="col" class="hidden lg:table-cell px-3 sm:px-4 py-2 sm:py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                  <th scope="col" class="px-3 sm:px-4 py-2 sm:py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Statut</th>
                  <th scope="col" class="px-3 sm:px-4 py-2 sm:py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                </tr>
              </thead>
              <tbody class="bg-white divide-y divide-gray-200">
                <tr v-for="product in paginatedProducts" :key="product.id" class="hover:bg-gray-50 transition-colors">
                  <!-- Colonne Produit réduite -->
                  <td class="px-3 sm:px-4 py-3 sm:py-4 whitespace-nowrap w-1/6">
                    <div class="space-y-1" style="width: 300px">
                      <div class="w-10 h-10 sm:w-12 sm:h-12 bg-gray-200 rounded-lg overflow-hidden flex-shrink-0">
                        <img v-if="product.primary_image" :src="product.primary_image" :alt="product.name" class="w-full h-full object-cover">
                        <div v-else class="w-full h-full flex items-center justify-center">
                          <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                          </svg>
                        </div>
                      </div>
                      <div class="min-w-0 flex-1">
                        <div class="text-xs sm:text-sm font-medium text-gray-900 truncate">{{ product.name }}</div>
                        <div class="text-xs text-gray-500">{{ product.sku }}</div>
                      </div>
                    </div>
                  </td>
                  <td class="px-3 sm:px-4 py-3 sm:py-4 whitespace-nowrap">
                    <div class="space-y-1">
                      <div class="flex items-center text-xs sm:text-sm font-medium text-gray-900">
                        <svg class="h-3 w-3 mr-1 primary-color" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                        </svg>
                        {{ product.boutique_name }}
                      </div>
                      <div class="text-xs text-gray-500">{{ product.created_by_name }}</div>
                    </div>
                  </td>
                  <td class="hidden md:table-cell px-3 sm:px-4 py-3 sm:py-4 whitespace-nowrap">
                    <div class="space-y-1">
                      <div class="text-xs sm:text-sm font-medium text-gray-900">{{ product.category_name }}</div>
                      <div class="text-xs text-gray-500">{{ product.subcategory_name }}</div>
                    </div>
                  </td>
                  <td class="px-3 sm:px-4 py-3 sm:py-4 whitespace-nowrap text-right">
                    <div class="space-y-1">
                      <div class="text-sm sm:text-base font-bold text-gray-900">{{ formatCurrency(product.unit_price) }}</div>
                      <div class="text-xs text-gray-500">Stock: {{ product.stock }}</div>
                    </div>
                  </td>
                  <!-- Nouvelle colonne Boost améliorée -->
                  <td class="px-3 sm:px-4 py-3 sm:py-4 whitespace-nowrap">
                    <div class="space-y-2">
                      <!-- Boost actif -->
                      <div v-if="product.is_boosted && product.boost_status === 'active'" class="space-y-1">
                        <span class="inline-flex items-center gap-1 px-2 py-1 rounded-full text-xs font-medium bg-purple-100 primary-color">
                          <svg class="h-3 w-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                          </svg>
                          Boosté Actif
                        </span>
                        <div class="text-xs text-gray-500">
                          Expire: {{ formatDate(product.boost_expires_at) }}
                        </div>
                        <button 
                          @click="viewBoostDetails(product)"
                          class="btn-outline h-10 mt-1  items-center justify-center"
                        >
                          Voir détails
                        </button>
                      </div>
                      
                      <!-- Boost en attente -->
                      <div v-else-if="product.boost_status === 'pending'" class="space-y-1">
                        <span class="inline-flex items-center gap-1 px-2 py-1 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                          <svg class="h-3 w-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                          </svg>
                          Boost en attente
                        </span>
                        <div class="text-xs text-gray-500">
                          Boutique: #{{ product.boutique_id }}
                        </div>
                        <div class="text-xs text-gray-500">
                          Contact: {{ product.created_by_name }}
                        </div>
                        <div class="flex gap-1 mt-2">
                          <button 
                            @click="openBoostValidation(product)"
                            class="submit-btn h-10 flex-1"
                          >
                            Valider
                          </button>
                          <button 
                            @click="viewBoostDetails(product)"
                            class="btn-outline h-10 flex-1 "
                          >
                            Détails
                          </button>
                        </div>
                      </div>
                      
                      <!-- Boost rejeté -->
                      <div v-else-if="product.boost_status === 'rejected'" class="space-y-1">
                        <span class="inline-flex items-center gap-1 px-2 py-1 rounded-full text-xs font-medium bg-red-100 error-color">
                          <svg class="h-3 w-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                          </svg>
                          Boost rejeté
                        </span>
                        <div class="text-xs text-gray-500">
                          Raison: {{ product.boost_rejection_reason || 'Non spécifiée' }}
                        </div>
                      </div>
                      
                      <!-- Pas de boost -->
                      <div v-else class="text-xs text-gray-400">
                        Aucun boost
                      </div>
                    </div>
                  </td>
                  <td class="hidden lg:table-cell px-3 sm:px-4 py-3 sm:py-4 whitespace-nowrap">
                    <div class="space-y-1">
                      <div class="text-xs sm:text-sm text-gray-900">{{ formatDate(product.created_at) }}</div>
                      <div class="text-xs text-gray-500">{{ formatTime(product.created_at) }}</div>
                    </div>
                  </td>
                  <td class="px-3 sm:px-4 py-3 sm:py-4 whitespace-nowrap">
                    <span :class="['inline-flex items-center gap-1 px-2 py-1 rounded-full text-xs font-medium', getStatusClass(product.status)]">
                      <component :is="getStatusIcon(product.status)" class="h-3 w-3" />
                      {{ getStatusLabel(product.status) }}
                    </span> 
                  </td>
                  <td class="px-3 sm:px-4 py-3 sm:py-4 whitespace-nowrap text-xs sm:text-sm font-medium">
                    <div class="flex items-center space-x-1 sm:space-x-2">
                      <button 
                        v-if="product.status === 'En attente'" 
                        @click.stop="showConfirmModal('approve', product)"
                        class="p-1.5 sm:p-2 rounded-lg transition-colors text-xs"
                        title="Valider"
                        style="background-color: rgba(74, 222, 128, 0.1); color: rgb(22, 163, 74);"
                      >
                        <svg class="h-3 w-3 sm:h-4 sm:w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                      </button>
                      <button 
                        v-if="product.status === 'En attente'" 
                        @click.stop="showConfirmModal('reject', product)"
                        class="p-1.5 sm:p-2 rounded-lg transition-colors text-xs"
                        title="Rejeter"
                        style="background-color: rgba(248, 113, 113, 0.1); color: rgb(220, 38, 38);"
                      >
                        <svg class="h-3 w-3 sm:h-4 sm:w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                      </button>
                      <button 
                        @click.stop="openProductDetails(product)" 
                        class="p-1.5 sm:p-2 rounded-lg transition-colors text-xs"
                        title="Voir détails"
                        style="background-color: rgba(209, 213, 219, 0.1); color: rgb(71, 85, 105);"
                      >
                        <svg class="h-3 w-3 sm:h-4 sm:w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                        </svg>
                      </button>
                      <button 
                        @click.stop="toggleProductActive(product)" 
                        class="p-1.5 sm:p-2 rounded-lg transition-colors text-xs"
                        :title="product.is_active ? 'Désactiver' : 'Activer'"
                        :style="product.is_active ? 'background-color: rgba(248, 113, 113, 0.1); color: rgb(220, 38, 38);' : 'background-color: rgba(74, 222, 128, 0.1); color: rgb(22, 163, 74);'"
                      >
                        <svg v-if="product.is_active" class="h-3 w-3 sm:h-4 sm:w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728L5.636 5.636m12.728 12.728L18.364 5.636M5.636 18.364l12.728-12.728"/>
                        </svg>
                        <svg v-else class="h-3 w-3 sm:h-4 sm:w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                      </button>
                      <button 
                        @click.stop="showConfirmModal('delete', product)" 
                        class="p-1.5 sm:p-2 rounded-lg transition-colors text-xs"
                        title="Supprimer"
                        style="background-color: rgba(248, 113, 113, 0.1); color: rgb(220, 38, 38);"
                      >
                        <svg class="h-3 w-3 sm:h-4 sm:w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                        </svg>
                      </button>
                    </div>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
  
          <!-- Pagination responsive -->
          <div class="bg-gray-50 px-4 sm:px-6 py-3 sm:py-4 flex flex-col sm:flex-row sm:items-center sm:justify-between border-t border-gray-200 gap-3 sm:gap-0">
            <div class="flex items-center space-x-2 sm:space-x-4 text-xs sm:text-sm">
              <span class="text-gray-700 hidden sm:inline">Éléments par page</span>
              <span class="text-gray-700 sm:hidden">Par page</span>
              <div class="w-15">
                <select 
                  v-model="itemsPerPage"
                  @change="handleItemsPerPageChange"
                  class="input-style p-0 m-0"
                  style="padding-left: 10px;"
                >
                  <option value="10">10</option>
                  <option value="25">25</option>
                  <option value="50">50</option>
                </select>
              </div>
              <span class="text-gray-700 text-xs sm:text-sm">
                <span class="hidden sm:inline">
                  Affichage {{ ((currentPage - 1) * itemsPerPage) + 1 }} à 
                  {{ Math.min(currentPage * itemsPerPage, filteredProducts.length) }} 
                  sur {{ filteredProducts.length }} résultats
                </span>
                <span class="sm:hidden">
                  {{ ((currentPage - 1) * itemsPerPage) + 1 }}-{{ Math.min(currentPage * itemsPerPage, filteredProducts.length) }} 
                  / {{ filteredProducts.length }}
                </span>
              </span>
            </div>
            <div class="flex items-center justify-center sm:justify-end space-x-1 sm:space-x-2">
              <button 
                @click="changePage(currentPage - 1)"
                :disabled="isFirstPage"
                class="relative inline-flex items-center px-2 sm:px-3 py-1.5 sm:py-2 border border-gray-300 text-xs sm:text-sm font-medium rounded-lg text-gray-700 bg-white hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed transition-colors"
              >
                <svg class="h-3 w-3 sm:h-4 sm:w-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
                <span class="hidden sm:inline">Précédent</span>
              </button>
              
              <div class="flex space-x-1">
                <button 
                  v-for="page in visiblePages" 
                  :key="page"
                  @click="changePage(page)"
                  :class="[
                    'relative inline-flex items-center px-2 sm:px-3 py-1.5 sm:py-2 border text-xs sm:text-sm font-medium rounded-lg transition-colors',
                    page === currentPage 
                      ? 'bg-orange' 
                    : 'bg-white border-gray-300 text-gray-500 hover:bg-gray-50'
                  ]"
                >
                  {{ page }}
                </button>
              </div>
              
              <button 
                @click="changePage(currentPage + 1)"
                :disabled="isLastPage"
                class="relative inline-flex items-center px-2 sm:px-3 py-1.5 sm:py-2 border border-gray-300 text-xs sm:text-sm font-medium rounded-lg text-gray-700 bg-white hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed transition-colors"
              >
                <span class="hidden sm:inline mr-1">Suivant</span>
                <svg class="h-3 w-3 sm:h-4 sm:w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round"stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                </svg>
              </button>
            </div>
          </div>
        </div>
  
        <!-- Empty State -->
        <div v-if="!dataLoading && filteredProducts.length === 0" class="bg-white shadow-lg rounded-lg p-8 sm:p-12 text-center">
          <div class="w-16 h-16 sm:w-20 sm:h-20 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4 sm:mb-6">
            <svg class="w-8 h-8 sm:w-10 sm:h-10 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
            </svg>
          </div>
          <h3 class="text-lg sm:text-xl font-semibold text-gray-900 mb-2">Aucun produit trouvé</h3>
          <p class="text-sm sm:text-base text-gray-500 mb-4 sm:mb-6">
            {{ hasActiveFilters ? 'Aucun produit ne correspond à vos critères de recherche.' : 'Aucun produit n\'a été soumis pour validation.' }}
          </p>
          <button v-if="hasActiveFilters" @click="clearFilters" class="btn-degrade-orange inline-flex items-center mx-auto">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
            </svg>
            Réinitialiser les filtres
          </button>
        </div>
      </div>
  
      <!-- Boost Validation Modal Amélioré -->
      <div v-if="showBoostModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center p-4 z-50">
        <div class="bg-white rounded-xl shadow-2xl max-w-4xl w-full max-h-[90vh] overflow-y-auto">
          <div class="p-6">
            <div class="flex justify-between items-center mb-6">
              <h3 class="text-xl font-bold text-gray-900 flex items-center">
                <div class="w-10 h-10 bg-purple-100 rounded-lg flex items-center justify-center mr-3">
                  <svg class="w-5 h-5 primary-color" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                  </svg>
                </div>
                {{ selectedBoost?.boost_status === 'pending' ? 'Validation de Boost' : 'Détails du Boost' }}
              </h3>
              <button @click="closeBoostModal" class="text-gray-400 hover:text-gray-600 transition-colors">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
              </button>
            </div>
            
            <div v-if="selectedBoost" class="space-y-6">
              <!-- Product Info avec image -->
              <div class="bg-gradient-to-r from-gray-50 to-gray-100 rounded-xl p-6">
                <h4 class="font-semibold text-gray-900 mb-4 flex items-center">
                  <svg class="w-5 h-5 mr-2 primary-color" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                  </svg>
                  Produit à booster
                </h4>
                <div class="flex items-center space-x-6">
                  <div class="w-24 h-24 bg-gray-200 rounded-xl overflow-hidden flex-shrink-0 shadow-md">
                    <img v-if="selectedBoost.primary_image" :src="selectedBoost.primary_image" :alt="selectedBoost.name" class="w-full h-full object-cover">
                    <div v-else class="w-full h-full flex items-center justify-center">
                      <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                      </svg>
                    </div>
                  </div>
                  <div class="flex-1">
                    <h5 class="text-lg font-bold text-gray-900 mb-2">{{ selectedBoost.name }}</h5>
                    <div class="grid grid-cols-2 gap-4 text-sm">
                      <div>
                        <p class="text-gray-600">SKU</p>
                        <p class="font-medium text-gray-900">{{ selectedBoost.sku }}</p>
                      </div>
                      <div>
                        <p class="text-gray-600">Prix</p>
                        <p class="font-medium primary-color text-lg">{{ formatCurrency(selectedBoost.unit_price) }}</p>
                      </div>
                      <div>
                        <p class="text-gray-600">Stock</p>
                        <p class="font-medium text-gray-900">{{ selectedBoost.stock }} unités</p>
                      </div>
                      <div>
                        <p class="text-gray-600">Catégorie</p>
                        <p class="font-medium text-gray-900">{{ selectedBoost.category_name }}</p>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
  
              <!-- Boutique Info Détaillée -->
              <div class="bg-gradient-to-r from-blue-50 to-indigo-50 rounded-xl p-6">
                <h4 class="font-semibold text-gray-900 mb-4 flex items-center">
                  <svg class="w-5 h-5 mr-2 primary-color" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                  </svg>
                  Informations de la boutique
                </h4>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                  <div class="bg-white rounded-lg p-4 shadow-sm">
                    <p class="text-sm text-gray-600 mb-1">ID Boutique</p>
                    <p class="font-bold primary-color text-xl">#{{ selectedBoost.boutique_id }}</p>
                  </div>
                  <div class="bg-white rounded-lg p-4 shadow-sm">
                    <p class="text-sm text-gray-600 mb-1">Nom de la boutique</p>
                    <p class="font-medium text-gray-900">{{ selectedBoost.boutique_name }}</p>
                  </div>
                  <div class="bg-white rounded-lg p-4 shadow-sm">
                    <p class="text-sm text-gray-600 mb-1">Marché</p>
                    <p class="font-medium text-gray-900">{{ selectedBoost.boutique_market }}</p>
                  </div>
                  <div class="bg-white rounded-lg p-4 shadow-sm">
                    <p class="text-sm text-gray-600 mb-1">Contact Principal</p>
                    <p class="font-medium text-gray-900">{{ selectedBoost.created_by_name }}</p>
                    <p class="text-sm primary-color">{{ selectedBoost.created_by_email }}</p>
                  </div>
                  <div class="bg-white rounded-lg p-4 shadow-sm">
                    <p class="text-sm text-gray-600 mb-1">Téléphone</p>
                    <p class="font-medium text-gray-900">{{ selectedBoost.created_by_phone || 'Non renseigné' }}</p>
                  </div>
                  <div class="bg-white rounded-lg p-4 shadow-sm">
                    <p class="text-sm text-gray-600 mb-1">Statut Boutique</p>
                    <span :class="['inline-flex items-center px-2 py-1 rounded-full text-xs font-medium', selectedBoost.boutique_verified ? 'bg-green-100 success-color' : 'bg-red-100 error-color']">
                      {{ selectedBoost.boutique_verified ? 'Vérifiée' : 'Non vérifiée' }}
                    </span>
                  </div>
                </div>
              </div>
  
              <!-- Boost Details Améliorés -->
              <div class="bg-gradient-to-r from-purple-50 to-pink-50 rounded-xl p-6">
                <h4 class="font-semibold text-gray-900 mb-4 flex items-center">
                  <svg class="w-5 h-5 mr-2 primary-color" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                  </svg>
                  Détails du boost demandé
                </h4>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
                  <div class="bg-white rounded-lg p-4 shadow-sm">
                    <p class="text-sm text-gray-600 mb-1">Type de boost</p>
                    <p class="font-bold primary-color text-lg">{{ getBoostTypeLabel(selectedBoost.boost_type) }}</p>
                  </div>
                  <div class="bg-white rounded-lg p-4 shadow-sm">
                    <p class="text-sm text-gray-600 mb-1">Durée</p>
                    <p class="font-bold blue-color text-lg">{{ selectedBoost.boost_duration }} jours</p>
                  </div>
                  <div class="bg-white rounded-lg p-4 shadow-sm">
                    <p class="text-sm text-gray-600 mb-1">Prix du boost</p>
                    <p class="font-bold primary-color text-lg">{{ formatCurrency(selectedBoost.boost_price) }}</p>
                  </div>
                  <div class="bg-white rounded-lg p-4 shadow-sm">
                    <p class="text-sm text-gray-600 mb-1">Statut</p>
                    <span :class="['inline-flex items-center px-2 py-1 rounded-full text-xs font-medium', getBoostStatusClass(selectedBoost.boost_status)]">
                      {{ getBoostStatusLabel(selectedBoost.boost_status) }}
                    </span>
                  </div>
                </div>
                
                <div class="bg-white rounded-lg p-4 shadow-sm">
                  <p class="text-sm text-gray-600 mb-2">Période de boost</p>
                  <div class="flex items-center space-x-4">
                    <div class="flex items-center">
                      <svg class="w-4 h-4 success-color mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                      </svg>
                      <span class="font-medium text-gray-900">Du {{ formatDate(selectedBoost.boost_start_date) }}</span>
                    </div>
                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/>
                    </svg>
                    <div class="flex items-center">
                      <svg class="w-4 h-4 error-color mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                      </svg>
                      <span class="font-medium text-gray-900">Au {{ formatDate(selectedBoost.boost_end_date) }}</span>
                    </div>
                  </div>
                </div>
              </div>
  
              <!-- Actions -->
              <div v-if="selectedBoost.boost_status === 'pending'" class="flex justify-end space-x-3 pt-4 border-t">
                <button 
                  @click="closeBoostModal"
                  class="btn-gray"
                >
                  Annuler
                </button>
                <button 
                  @click="rejectBoost"
                  class="btn-deconnexion"
                >
                  Rejeter le boost
                </button>
                <button 
                  @click="approveBoost"
                  :disabled="actionLoading"
                  class="submit-btn"
                >
                  {{ actionLoading ? 'Validation...' : 'Valider le boost' }}
                </button>
              </div>
              
              <!-- Actions pour boost actif/rejeté -->
              <div v-else class="flex justify-end pt-4 border-t">
                <button 
                  @click="closeBoostModal"
                  class="btn-gray"
                >
                  Fermer
                </button>
              </div>
            </div>
          </div>
        </div>
      </div>
  
      <!-- Confirmation Modal -->
      <div v-if="showConfirmationModal" class="fixed inset-0 bg-black/60 bg-opacity-50 flex items-center justify-center p-4 z-50">
        <div class="bg-white rounded-lg shadow-xl max-w-md w-full">
          <div class="p-6">
            <div class="flex items-center mb-4">
              <div :class="['w-12 h-12 rounded-full flex items-center justify-center mr-4', getConfirmationIconClass()]">
                <component :is="getConfirmationIcon()" class="w-6 h-6" />
              </div>
              <div>
                <h3 class="text-lg font-semibold text-gray-900">{{ getConfirmationTitle() }}</h3>
                <p class="text-sm text-gray-500">{{ selectedProduct?.name }}</p>
              </div>
            </div>
            <p class="text-gray-600 mb-6">{{ getConfirmationMessage() }}</p>
            <div v-if="confirmationAction === 'reject'" class="mb-4">
              <label class="block text-sm font-medium text-gray-700 mb-2">Raison du rejet</label>
              <textarea 
                v-model="rejectionReason"
                rows="3"
                class="input-style w-full"
                placeholder="Expliquez pourquoi ce produit est rejeté..."
              ></textarea>
            </div>
            <div class="flex justify-end space-x-3">
              <button 
                @click="closeConfirmModal"
                class="btn-gray"
              >
                Annuler
              </button>
              <button 
                @click="executeAction"
                :disabled="actionLoading"
                :class="['btn-degrade-orange', getConfirmationButtonClass()]"
              >
                {{ actionLoading ? 'Traitement...' : getConfirmationButtonText() }}
              </button>
            </div>
          </div>
        </div>
      </div>
  
      <!-- Product Details Modal Améliorée avec toutes les nouvelles données -->
      <div v-if="showProductModal" class="fixed inset-0 bg-black/60 bg-opacity-50 flex items-center justify-center p-4 z-50" @click="closeProductModal">
        <div class="bg-white rounded-xl shadow-2xl max-w-6xl w-full max-h-[90vh] overflow-y-auto">
          <div class="relative">
            <!-- Header avec gradient -->
            <div class= "sticky top-0 bg-white border-b border-gray-100 px-6 py-4 rounded-t-xl">
              <div class="flex justify-between items-center">
                <h3 class="text-xl font-bold text-black flex items-center">
                  <div class="w-10 h-10 bg-orange rounded-lg flex items-center justify-center mr-3">
                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                    </svg>
                  </div>
                  Détails du produit
                </h3>
               
                 <XIcon @click="closeProductModal" class="h-7 w-7 cursor-pointer text-gray-700" />
              </div>
            </div>
  
            <div class="p-6">
              <div v-if="selectedProduct" class="space-y-6">
                <!-- Product Header avec image principale -->
                <div class="flex flex-col lg:flex-row lg:items-start gap-6">
                  <div class="lg:w-1/3">
                    <div class="aspect-square bg-gray-100 rounded-xl overflow-hidden shadow-lg">
                      <img v-if="selectedProduct.primary_image" :src="selectedProduct.primary_image" :alt="selectedProduct.name" class="w-full h-full object-cover">
                      <div v-else class="w-full h-full flex items-center justify-center">
                        <svg class="w-16 h-16 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                      </div>
                    </div>
                  </div>
                  
                  <div class="lg:w-2/3 space-y-4">
                    <div>
                      <h4 class="text-3xl font-bold text-gray-900 mb-3">{{ selectedProduct.name }}</h4>
                      <div class="flex flex-wrap items-center gap-3 mb-4">
                        <span :class="['inline-flex items-center gap-1 px-3 py-1 rounded-full text-sm font-medium', getStatusClass(selectedProduct.status)]">
                          <component :is="getStatusIcon(selectedProduct.status)" class="h-4 w-4" />
                          {{ getStatusLabel(selectedProduct.status) }}
                        </span>
                        <span v-if="selectedProduct.is_boosted" class="inline-flex items-center gap-1 px-3 py-1 rounded-full text-sm font-medium bg-orange-100 primary-color">
                          <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                          </svg>
                          Boosté
                        </span>
                        <span v-if="selectedProduct.is_featured" class="inline-flex items-center gap-1 px-3 py-1 rounded-full text-sm font-medium bg-yellow-100 primary-color">
                          <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/>
                          </svg>
                          Mis en avant
                        </span>
                        <span :class="['inline-flex items-center gap-1 px-2 py-1 rounded-full text-xs font-medium', selectedProduct.is_active ? 'bg-green-100 success-color' : 'bg-red-100 error-color']">
                          {{ selectedProduct.is_active ? 'Actif' : 'Inactif' }}
                        </span>
                      </div>
                      
                      <div class="grid grid-cols-2 gap-4 mb-4">
                        <div>
                          <div class="text-4xl font-bold primary-color mb-1">{{ formatCurrency(selectedProduct.unit_price) }}</div>
                          <div v-if="selectedProduct.wholesale_price" class="text-lg text-gray-600">
                            Gros: {{ formatCurrency(selectedProduct.wholesale_price) }}
                            <span v-if="selectedProduct.min_wholesale_qty" class="text-sm">(min. {{ selectedProduct.min_wholesale_qty }})</span>
                          </div>
                        </div>
                        <div class="text-right">
                          <div class="text-2xl font-bold text-gray-900">{{ selectedProduct.stock }} unités</div>
                          <div class="text-sm text-gray-500">En stock</div>
                        </div>
                      </div>
                      
                      <div class="grid grid-cols-3 gap-4 text-center">
                        <div class="bg-blue-50 rounded-lg p-3">
                          <div class="text-2xl font-bold primary-color">{{ selectedProduct.views_count || 0 }}</div>
                          <div class="text-sm text-gray-600">Vues</div>
                        </div>
                        <div class="bg-green-50 rounded-lg p-3">
                          <div class="text-2xl font-bold success-color">{{ selectedProduct.sales_count || 0 }}</div>
                          <div class="text-sm text-gray-600">Ventes</div>
                        </div>
                        <div class="bg-purple-50 rounded-lg p-3">
                          <div class="text-2xl font-bold blue-color">{{ selectedProduct.image_count || 0 }}</div>
                          <div class="text-sm text-gray-600">Images</div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
  
                <!-- Galerie d'images complète -->
                <div v-if="selectedProduct.images && selectedProduct.images.length > 0" class="bg-gray-50 rounded-xl p-6">
                  <h4 class="font-semibold text-gray-900 mb-4 flex items-center">
                    <svg class="w-5 h-5 mr-2 primary-color" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                    Galerie d'images ({{ selectedProduct.images.length }})
                  </h4>
                  <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-6 gap-4">
                    <div v-for="(image, index) in selectedProduct.images" :key="index" class="relative aspect-square bg-white rounded-lg overflow-hidden shadow-sm hover:shadow-md transition-shadow group">
                      <img :src="image.image_url || image" :alt="image.alt_text || `Image ${index + 1}`" class="w-full h-full object-cover">
                      <div v-if="image.is_primary" class="absolute top-2 left-2 bg-orange-500 text-white text-xs px-2 py-1 rounded-full font-medium">
                        Principale
                      </div>
                      <div class="absolute inset-0 bg-black/50 bg-opacity-0 group-hover:bg-opacity-20 transition-all flex items-center justify-center">
                        <svg class="w-6 h-6 text-white opacity-0 group-hover:opacity-100 transition-opacity" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                      </div>
                    </div>
                  </div>
                </div>
  
                <!-- Couleurs et Tailles -->
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                  <!-- Couleurs -->
                  <div v-if="selectedProduct.colors && selectedProduct.colors.length > 0" class="bg-purple-50 rounded-xl p-6">
                    <h4 class="font-semibold text-gray-900 mb-4 flex items-center">
                      <svg class="w-5 h-5 mr-2 primary-color" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zM21 5a2 2 0 00-2-2h-4a2 2 0 00-2 2v12a4 4 0 004 4h4a2 2 0 002-2V5z"/>
                      </svg>
                      Couleurs disponibles ({{ selectedProduct.colors.length }})
                    </h4>
                    <div class="flex flex-wrap gap-3">
                      <div v-for="color in selectedProduct.colors" :key="color.id" class="flex items-center bg-white rounded-lg px-3 py-2 shadow-sm">
                        <div class="w-6 h-6 rounded-full border-2 border-gray-300 mr-2" :style="{ backgroundColor: color.hex_value }"></div>
                        <span class="text-sm font-medium text-gray-900">{{ color.name }}</span>
                      </div>
                    </div>
                  </div>
  
                  <!-- Tailles -->
                  <div v-if="selectedProduct.sizes && selectedProduct.sizes.length > 0" class="bg-blue-50 rounded-xl p-6">
                    <h4 class="font-semibold text-gray-900 mb-4 flex items-center">
                      <svg class="w-5 h-5 mr-2 primary-color" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8V4m0 0h4M4 4l5 5m11-1V4m0 0h-4m4 0l-5 5M4 16v4m0 0h4m-4 0l5-5m11 5l-5-5m5 5v-4m0 4h-4"/>
                      </svg>
                      Tailles disponibles ({{ selectedProduct.sizes.length }})
                    </h4>
                    <div class="flex flex-wrap gap-2">
                      <div v-for="size in selectedProduct.sizes" :key="size.id" class="bg-white rounded-lg px-3 py-2 shadow-sm border">
                        <div class="text-sm font-medium text-gray-900">{{ size.name }}</div>
                        <div v-if="size.category" class="text-xs text-gray-500">{{ size.category }}</div>
                      </div>
                    </div>
                  </div>
                </div>
  
                <!-- Informations détaillées en grille -->
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                  <!-- Informations générales -->
                  <div class="bg-blue-50 rounded-xl p-6">
                    <h4 class="font-semibold text-gray-900 mb-4 flex items-center">
                      <svg class="w-5 h-5 mr-2 primary-color" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                      </svg>
                      Informations générales
                    </h4>
                    <div class="space-y-3">
                      <div class="flex justify-between">
                        <span class="text-sm text-gray-600">SKU:</span>
                        <span class="font-medium text-gray-900">{{ selectedProduct.sku }}</span>
                      </div>
                      <div class="flex justify-between">
                        <span class="text-sm text-gray-600">Slug:</span>
                        <span class="font-medium text-gray-900 text-xs">{{ selectedProduct.slug }}</span>
                      </div>
                      <div class="flex justify-between">
                        <span class="text-sm text-gray-600">Catégorie:</span>
                        <span class="font-medium text-gray-900">{{ selectedProduct.category_name }}</span>
                      </div>
                      <div class="flex justify-between">
                        <span class="text-sm text-gray-600">Sous-catégorie:</span>
                        <span class="font-medium text-gray-900">{{ selectedProduct.subcategory_name || 'N/A' }}</span>
                      </div>
                      <div class="flex justify-between">
                        <span class="text-sm text-gray-600">Poids:</span>
                        <span class="font-medium text-gray-900">{{ selectedProduct.weight || 'N/A' }} kg</span>
                      </div>
                      <div class="flex justify-between">
                        <span class="text-sm text-gray-600">Dimensions:</span>
                        <span class="font-medium text-gray-900 text-xs">
                          {{ selectedProduct.length || 'N/A' }} × {{ selectedProduct.width || 'N/A' }} × {{ selectedProduct.height || 'N/A' }} cm
                        </span>
                      </div>
                      <div class="flex justify-between">
                        <span class="text-sm text-gray-600">Créé le:</span>
                        <span class="font-medium text-gray-900">{{ formatDate(selectedProduct.created_at) }}</span>
                      </div>
                      <div class="flex justify-between">
                        <span class="text-sm text-gray-600">Modifié le:</span>
                        <span class="font-medium text-gray-900">{{ formatDate(selectedProduct.updated_at) }}</span>
                      </div>
                    </div>
                  </div>
  
                  <!-- Informations boutique -->
                  <div class="bg-green-50 rounded-xl p-6">
                    <h4 class="font-semibold text-gray-900 mb-4 flex items-center">
                      <svg class="w-5 h-5 mr-2 primary-color" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                      </svg>
                      Boutique
                    </h4>
                    <div class="space-y-3">
                      <div class="flex items-center gap-3 mb-3">
                        <img :src="selectedProduct.boutique_info?.logo || selectedProduct.boutique_logo" :alt="selectedProduct.boutique_name" class="w-12 h-12 rounded-lg object-cover">
                        <div>
                          <div class="font-medium text-gray-900">{{ selectedProduct.boutique_name }}</div>
                          <div class="text-sm text-gray-500">#{{ selectedProduct.boutique_id }}</div>
                        </div>
                      </div>
                      <div class="flex justify-between">
                        <span class="text-sm text-gray-600">Type:</span>
                        <span class="font-medium text-gray-900">{{ selectedProduct.boutique_info?.type || selectedProduct.boutique_type || 'N/A' }}</span>
                      </div>
                      <div class="flex justify-between">
                        <span class="text-sm text-gray-600">Marché:</span>
                        <span class="font-medium text-gray-900">{{ selectedProduct.boutique_info?.market || selectedProduct.boutique_marche || 'N/A' }}</span>
                      </div>
                      <div class="flex justify-between">
                        <span class="text-sm text-gray-600">Téléphone:</span>
                        <span class="font-medium text-gray-900">{{ selectedProduct.boutique_info?.phone || selectedProduct.boutique_phone || 'N/A' }}</span>
                      </div>
                      <div class="flex justify-between">
                        <span class="text-sm text-gray-600">Email:</span>
                        <span class="font-medium text-gray-900 text-xs">{{ selectedProduct.boutique_info?.email || selectedProduct.boutique_email || 'N/A' }}</span>
                      </div>
                      <div class="flex justify-between">
                        <span class="text-sm text-gray-600">Plan:</span>
                        <span :class="['font-medium', (selectedProduct.boutique_info?.subscription_plan || selectedProduct.boutique_premium) === 'premium' ? 'blue-color' : 'text-gray-600']">
                          {{ (selectedProduct.boutique_info?.subscription_plan || selectedProduct.boutique_premium) === 'premium' ? 'Premium' : 'Gratuit' }}
                        </span>
                      </div>
                      <div class="flex justify-between">
                        <span class="text-sm text-gray-600">Vérifié:</span>
                        <span :class="['font-medium', (selectedProduct.boutique_info?.is_verified ?? selectedProduct.boutique_verified) ? 'success-color' : 'error-color']">
                          {{ (selectedProduct.boutique_info?.is_verified ?? selectedProduct.boutique_verified) ? 'Oui' : 'Non' }}
                        </span>
                      </div>
                    </div>
                  </div>
  
                  <!-- Informations créateur -->
                  <div class="bg-yellow-50 rounded-xl p-6">
                    <h4 class="font-semibold text-gray-900 mb-4 flex items-center">
                      <svg class="w-5 h-5 mr-2 primary-color" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                      </svg>
                      Créateur
                    </h4>
                    <div class="space-y-3">
                      <div class="flex justify-between">
                        <span class="text-sm text-gray-600">Nom:</span>
                        <span class="font-medium text-gray-900">{{ selectedProduct.creator_info?.name || selectedProduct.created_by_name || 'N/A' }}</span>
                      </div>
                      <div class="flex justify-between">
                        <span class="text-sm text-gray-600">Email:</span>
                        <span class="font-medium text-gray-900 text-xs">{{ selectedProduct.creator_info?.email || selectedProduct.created_by_email || 'N/A' }}</span>
                      </div>
                      <div class="flex justify-between">
                        <span class="text-sm text-gray-600">ID Créateur:</span>
                        <span class="font-medium text-gray-900">{{ selectedProduct.created_by }}</span>
                      </div>
                    </div>
                  </div>
                </div>
  
                <!-- Description -->
                <div class="bg-gray-50 rounded-xl p-6">
                  <h4 class="font-semibold text-gray-900 mb-4 flex items-center">
                    <svg class="w-5 h-5 mr-2 primary-color" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                    Description
                  </h4>
                  <div class="bg-white rounded-lg p-4 border">
                    <p class="text-gray-700 leading-relaxed whitespace-pre-wrap">{{ selectedProduct.description || 'Aucune description disponible.' }}</p>
                  </div>
                </div>
  
                <!-- Informations de boost -->
                <div v-if="selectedProduct.boost_info || selectedProduct.is_boosted" class="bg-purple-50 rounded-xl p-6">
                  <h4 class="font-semibold text-gray-900 mb-4 flex items-center">
                    <svg class="w-5 h-5 mr-2 primary-color" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                    </svg>
                    Informations de boost
                  </h4>
                  <div class="bg-white rounded-lg p-4 border border-purple-200">
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                      <div>
                        <p class="text-sm text-gray-600">ID Boost</p>
                        <p class="font-medium primary-color">{{ selectedProduct.boost_info?.id || selectedProduct.boost_id || 'N/A' }}</p>
                      </div>
                      <div>
                        <p class="text-sm text-gray-600">Type de boost</p>
                        <p class="font-medium blue-color">{{ getBoostTypeLabel(selectedProduct.boost_info?.type || selectedProduct.boost_type) }}</p>
                      </div>
                      <div>
                        <p class="text-sm text-gray-600">Statut</p>
                        <span :class="['inline-flex items-center gap-1 px-2 py-1 rounded-full text-xs font-medium', getBoostStatusClass(selectedProduct.boost_info?.status || selectedProduct.boost_status)]">
                          {{ getBoostStatusLabel(selectedProduct.boost_info?.status || selectedProduct.boost_status) }}
                        </span>
                      </div>
                      <div>
                        <p class="text-sm text-gray-600">Jours restants</p>
                        <p class="font-medium blue-color">{{ selectedProduct.boost_info?.days_remaining || selectedProduct.boost_days_remaining || 0 }} jours</p>
                      </div>
                    </div>
                    <div class="mt-4 pt-4 border-t border-purple-200">
                      <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                          <p class="text-sm text-gray-600">Créé le</p>
                          <p class="font-medium primary-color">{{ formatDate(selectedProduct.boost_info?.created_at || selectedProduct.boost_created_at) }}</p>
                        </div>
                        <div>
                          <p class="text-sm text-gray-600">Expire le</p>
                          <p class="font-medium error-color">{{ formatDate(selectedProduct.boost_info?.expires_at || selectedProduct.boost_expires_at || selectedProduct.boost_expires_at_detail) }}</p>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
  
                <!-- Adresse de la boutique -->
                <div v-if="selectedProduct.boutique_info?.address || selectedProduct.boutique_address" class="bg-indigo-50 rounded-xl p-6">
                  <h4 class="font-semibold text-gray-900 mb-4 flex items-center">
                    <svg class="w-5 h-5 mr-2 primary-color" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                    Adresse de la boutique
                  </h4>
                  <div class="bg-white rounded-lg p-4 border">
                    <p class="text-gray-700">{{ selectedProduct.boutique_info?.address || selectedProduct.boutique_address }}</p>
                  </div>
                </div>
  
                <!-- Description de la boutique -->
                <div v-if="selectedProduct.boutique_info?.description || selectedProduct.boutique_description" class="bg-teal-50 rounded-xl p-6">
                  <h4 class="font-semibold text-gray-900 mb-4 flex items-center">
                    <svg class="w-5 h-5 mr-2 primary-color" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                    </svg>
                    À propos de la boutique
                  </h4>
                  <div class="bg-white rounded-lg p-4 border">
                    <p class="text-gray-700 leading-relaxed whitespace-pre-wrap">{{ selectedProduct.boutique_info?.description || selectedProduct.boutique_description }}</p>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
  
      <!-- Notification -->
      <div v-if="notification.show" :class="['fixed top-4 right-4 p-4 rounded-lg shadow-lg z-50 transition-all duration-300', notification.type === 'success' ? 'bg-green-100 border border-green-200 success-color' : 'bg-red-100 border border-red-200 error-color']">
        <div class="flex items-center">
          <svg v-if="notification.type === 'success'" class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
          </svg>
          <svg v-else class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"/>
          </svg>
          <span class="font-medium">{{ notification.message }}</span>
        </div>
      </div>
    </div>
  </template>
  
  <script setup>
  import { ref, computed, onMounted } from 'vue'
  import * as XLSX from 'xlsx'
  import jsPDF from 'jspdf'
  import 'jspdf-autotable'
  
  // Import des APIs admin
  import { 
    adminProductsApi, 
    adminBoutiquesApi, 
    adminBoostApi,
    adminApiUtils 
  } from '../../services/admin-api.js'
import {  CheckCircleIcon, ChevronDownIcon,  ClockIcon, DownloadIcon, FileTextIcon, HomeIcon, RefreshCcw, Search, X, XCircleIcon, XIcon } from 'lucide-vue-next'
  
  // Remplacer toutes les références aux icônes par des composants SVG simples
  // const ClockIcon = { template: '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>' }
  // const CheckCircleIcon = { template: '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>' }
  // const XCircleIcon = { template: '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>' }
  
  // Data
  const dataLoading = ref(true)
  const hasError = ref(false)
  const error = ref('')
  const actionLoading = ref(false)
  
  const products = ref([])
  const stats = ref({})
  const availableBoutiques = ref([])
  
  // Filters and search
  const activeFilter = ref('all')
  const searchQuery = ref('')
  const statusFilter = ref('')
  const boutiqueFilter = ref('')
  
  // Pagination
  const currentPage = ref(1)
  const itemsPerPage = ref(25)
  
  // UI state
  const showExportDropdown = ref(false)
  const showProductModal = ref(false)
  const showConfirmationModal = ref(false)
  const showBoostModal = ref(false)
  const selectedProduct = ref(null)
  const selectedBoost = ref(null)
  const confirmationAction = ref(null)
  const rejectionReason = ref('')
  
  // Notification
  const notification = ref({
    show: false,
    message: '',
    type: 'success'
  })
  
  // Filter tabs
  const filterTabs = ref([
    { label: 'Tous', value: 'all' },
    { label: 'En Attente', value: 'pending' },
    { label: 'Validés', value: 'approved' },
    { label: 'Rejetés', value: 'rejected' },
    { label: 'Boosts', value: 'boost' }
  ])
  
  // Computed properties
  const filteredProducts = computed(() => {
    let filtered = [...products.value]
    
    // Filter by active tab
    if (activeFilter.value !== 'all') {
      if (activeFilter.value === 'boost') {
        filtered = filtered.filter(product => product.is_boosted || product.boost_status === 'pending')
      } else if (activeFilter.value === 'pending') {
        filtered = filtered.filter(product => product.status === 'En attente')
      } else if (activeFilter.value === 'approved') {
        filtered = filtered.filter(product => product.status === 'Actif')
      } else if (activeFilter.value === 'rejected') {
        filtered = filtered.filter(product => product.status === 'Rejeté')
      }
    }
    
    // Filter by status dropdown
    if (statusFilter.value) {
      filtered = filtered.filter(product => product.status === statusFilter.value)
    }
    
    // Filter by boutique
    if (boutiqueFilter.value) {
      filtered = filtered.filter(product => product.boutique_id == boutiqueFilter.value)
    }
    
    // Filter by search query
    if (searchQuery.value) {
      const query = searchQuery.value.toLowerCase()
      filtered = filtered.filter(product => 
        product.name.toLowerCase().includes(query) ||
        product.boutique_name.toLowerCase().includes(query) ||
        product.category_name.toLowerCase().includes(query) ||
        product.sku.toLowerCase().includes(query)
      )
    }
    
    return filtered
  })
  
  const paginatedProducts = computed(() => {
    const start = (currentPage.value - 1) * itemsPerPage.value
    const end = start + itemsPerPage.value
    return filteredProducts.value.slice(start, end)
  })
  
  const totalPages = computed(() => {
    return Math.ceil(filteredProducts.value.length / itemsPerPage.value)
  })
  
  const isFirstPage = computed(() => {
    return currentPage.value === 1
  })
  
  const isLastPage = computed(() => {
    return currentPage.value === totalPages.value
  })
  
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
        for (let i = 1; i <= 5; i++) {
          pages.push(i)
        }
        pages.push('...')
        pages.push(total)
      } else if (current >= total - 3) {
        pages.push(1)
        pages.push('...')
        for (let i = total - 4; i <= total; i++) {
          pages.push(i)
        }
      } else {
        pages.push(1)
        pages.push('...')
        for (let i = current - 1; i <= current + 1; i++) {
          pages.push(i)
        }
        pages.push('...')
        pages.push(total)
      }
    }
    
    return pages
  })
  
  const filterCounts = computed(() => {
    const counts = {
      all: products.value.length,
      pending: 0,
      approved: 0,
      rejected: 0,
      boost: 0
    }
    
    products.value.forEach(product => {
      if (product.status === 'En attente') {
        counts.pending++
      } else if (product.status === 'Actif') {
        counts.approved++
      } else if (product.status === 'Rejeté') {
        counts.rejected++
      }
      
      if (product.is_boosted || product.boost_status === 'pending') {
        counts.boost++
      }
    })
    
    return counts
  })
  
  const hasActiveFilters = computed(() => {
    return searchQuery.value || statusFilter.value || boutiqueFilter.value || activeFilter.value !== 'all'
  })
  
  // Methods
  const loadAllData = async () => {
    dataLoading.value = true
    hasError.value = false
    
    try {
      await Promise.all([
        loadProducts(),
        loadStats(),
        loadBoutiques()
      ])
    } catch (error) {
      console.error('Erreur lors du chargement des données:', error)
      hasError.value = true
      error.value = 'Erreur lors du chargement des données'
    } finally {
      dataLoading.value = false
    }
  }
  
  const loadProducts = async () => {
    try {
      const params = adminApiUtils.buildPaginationParams(1, 1000) // Charger tous les produits
      
      if (statusFilter.value) {
        params.status = statusFilter.value
      }
      
      if (boutiqueFilter.value) {
        params.boutique_id = boutiqueFilter.value
      }
      
      if (searchQuery.value) {
        params.search = searchQuery.value
      }
      
      const response = await adminProductsApi.getAllProducts(params)
      console.log('🧾 Liste des produits:', response.data)
      
      if (response.success) {
        products.value = response.data || []
        console.log('✅ Produits chargés depuis l\'API:', products.value.length)
      } else {
        throw new Error(response.error || 'Erreur lors du chargement des produits')
      }
    } catch (error) {
      console.error('❌ Erreur lors du chargement des produits:', error)
      showNotification('Erreur lors du chargement des produits: ' + error.message, 'error')
      throw error
    }
  }
  
  const loadStats = async () => {
    try {
      const response = await adminProductsApi.getProductsStats()
      
      if (response.success) {
        const data = response.data
        stats.value = {
          pending_products: data.products?.status_pending || 0,
          active_products: data.products?.status_active || 0,
          rejected_products: data.products?.status_rejected || 0,
          pending_boosts: data.boosts?.pending_boosts || 0,
          approved_growth: 15, // Calculé séparément
          rejection_rate: data.products?.total_products > 0 
            ? Math.round((data.products.status_rejected / data.products.total_products) * 100) 
            : 0
        }
        console.log('✅ Statistiques chargées depuis l\'API:', stats.value)
      } else {
        throw new Error(response.error || 'Erreur lors du chargement des statistiques')
      }
    } catch (error) {
      console.error('❌ Erreur lors du chargement des statistiques:', error)
      stats.value = {
        pending_products: 0,
        active_products: 0,
        rejected_products: 0,
        pending_boosts: 0,
        approved_growth: 0,
        rejection_rate: 0
      }
    }
  }
  
  const loadBoutiques = async () => {
    try {
      const response = await adminBoutiquesApi.getAllBoutiques({ limit: 1000 })
      
      if (response.success) {
        availableBoutiques.value = response.data || []
        console.log('✅ Boutiques chargées depuis l\'API:', availableBoutiques.value.length)
      } else {
        console.warn('⚠️ Erreur lors du chargement des boutiques:', response.error)
        availableBoutiques.value = []
      }
    } catch (error) {
      console.error('❌ Erreur lors du chargement des boutiques:', error)
      availableBoutiques.value = []
    }
  }
  
  // Filter and search methods
  const handleFilterChange = (filter) => {
    activeFilter.value = filter
    currentPage.value = 1
  }
  
  const handleSearch = () => {
    currentPage.value = 1
  }
  
  const clearFilters = () => {
    activeFilter.value = 'all'
    searchQuery.value = ''
    statusFilter.value = ''
    boutiqueFilter.value = ''
    currentPage.value = 1
  }
  
  // Pagination methods
  const changePage = (page) => {
    if (page >= 1 && page <= totalPages.value) {
      currentPage.value = page
    }
  }
  
  const handleItemsPerPageChange = () => {
    currentPage.value = 1
  }
  
  // Modal methods
  const openProductDetails = (product) => {
    selectedProduct.value = product
    showProductModal.value = true
  }
  
  const closeProductModal = () => {
    showProductModal.value = false
    selectedProduct.value = null
  }
  
  const openBoostValidation = (product) => {
    selectedBoost.value = product
    showBoostModal.value = true
  }
  
  const closeBoostModal = () => {
    showBoostModal.value = false
    selectedBoost.value = null
  }
  
  const approveBoost = async () => {
    if (!selectedBoost.value) return
    
    actionLoading.value = true
    
    try {
      const response = await adminBoostApi.approveBoost(selectedBoost.value.id, {
        comment: 'Boost approuvé par l\'administrateur'
      })
      
      if (response.success) {
        // Update local data
        const productIndex = products.value.findIndex(p => p.id === selectedBoost.value.id)
        if (productIndex !== -1) {
          products.value[productIndex].is_boosted = true
          products.value[productIndex].boost_status = 'active'
        }
        
        showNotification('Boost validé avec succès', 'success')
        closeBoostModal()
        await loadStats()
      } else {
        throw new Error(response.error || 'Erreur lors de la validation du boost')
      }
      
    } catch (error) {
      console.error('Erreur lors de la validation du boost:', error)
      showNotification(error.message || 'Erreur lors de la validation du boost', 'error')
    } finally {
      actionLoading.value = false
    }
  }
  
  const rejectBoost = async () => {
    if (!selectedBoost.value) return
    
    actionLoading.value = true
    
    try {
      const response = await adminBoostApi.rejectBoost(selectedBoost.value.id, {
        reason: 'Boost rejeté par l\'administrateur'
      })
      
      if (response.success) {
        // Update local data
        const productIndex = products.value.findIndex(p => p.id === selectedBoost.value.id)
        if (productIndex !== -1) {
          products.value[productIndex].boost_status = 'rejected'
        }
        
        showNotification('Boost rejeté avec succès', 'success')
        closeBoostModal()
        await loadStats()
      } else {
        throw new Error(response.error || 'Erreur lors du rejet du boost')
      }
      
    } catch (error) {
      console.error('Erreur lors du rejet du boost:', error)
      showNotification(error.message || 'Erreur lors du rejet du boost', 'error')
    } finally {
      actionLoading.value = false
    }
  }
  
  const viewBoostDetails = (product) => {
    selectedBoost.value = product
    showBoostModal.value = true
  }
  
  const showConfirmModal = (action, product) => {
    confirmationAction.value = action
    selectedProduct.value = product
    rejectionReason.value = ''
    showConfirmationModal.value = true
  }
  
  const closeConfirmModal = () => {
    showConfirmationModal.value = false
    confirmationAction.value = null
    selectedProduct.value = null
    rejectionReason.value = ''
  }
  
  const executeAction = async () => {
    if (!selectedProduct.value || !confirmationAction.value) return
    
    actionLoading.value = true
    
    try {
      let response
      let successMessage = ''
      
      switch (confirmationAction.value) {
        case 'approve':
          response = await adminProductsApi.approveProduct(selectedProduct.value.id, {
            comment: 'Produit approuvé par l\'administrateur'
          })
          successMessage = 'Produit validé avec succès'
          break
          
        case 'reject':
          if (!rejectionReason.value.trim()) {
            showNotification('Veuillez indiquer une raison pour le rejet', 'error')
            return
          }
          response = await adminProductsApi.rejectProduct(selectedProduct.value.id, {
            reason: rejectionReason.value
          })
          successMessage = 'Produit rejeté avec succès'
          break
          
        case 'delete':
          response = await adminProductsApi.deleteProduct(selectedProduct.value.id)
          successMessage = 'Produit supprimé avec succès'
          break
      }
      
      if (response.success) {
        showNotification(successMessage, 'success')
        closeConfirmModal()
        
        // Reload data
        await loadAllData()
      } else {
        throw new Error(response.error || 'Erreur lors de l\'opération')
      }
      
    } catch (error) {
      console.error('Erreur lors de l\'exécution de l\'action:', error)
      showNotification(error.message || 'Erreur lors de l\'opération', 'error')
    } finally {
      actionLoading.value = false
    }
  }
  
  const toggleProductActive = async (product) => {
    try {
      const newActiveState = product.is_active ? 0 : 1
      
      const response = await adminProductsApi.toggleProductActive(product.id, {
        is_active: newActiveState
      })
      
      if (response.success) {
        // Update local data
        const productIndex = products.value.findIndex(p => p.id === product.id)
        if (productIndex !== -1) {
          products.value[productIndex].is_active = newActiveState
        }
        
        const message = newActiveState ? 'Produit activé avec succès' : 'Produit désactivé avec succès'
        showNotification(message, 'success')
      } else {
        throw new Error(response.error || 'Erreur lors de la mise à jour')
      }
      
    } catch (error) {
      console.error('Erreur lors du changement de statut:', error)
      showNotification(error.message || 'Erreur lors de la mise à jour du produit', 'error')
    }
  }
  
  // Confirmation modal helpers
  const getConfirmationTitle = () => {
    switch (confirmationAction.value) {
      case 'approve': return 'Valider le produit'
      case 'reject': return 'Rejeter le produit'
      case 'delete': return 'Supprimer le produit'
      default: return ''
    }
  }
  
  const getConfirmationMessage = () => {
    switch (confirmationAction.value) {
      case 'approve': return 'Êtes-vous sûr de vouloir valider ce produit ? Il sera publié sur la plateforme.'
      case 'reject': return 'Êtes-vous sûr de vouloir rejeter ce produit ? Le vendeur sera notifié.'
      case 'delete': return 'Êtes-vous sûr de vouloir supprimer définitivement ce produit ? Cette action est irréversible.'
      default: return ''
    }
  }
  
  const getConfirmationIcon = () => {
    switch (confirmationAction.value) {
      case 'approve': return CheckCircleIcon
      case 'reject': return XCircleIcon
      case 'delete': return XCircleIcon
      default: return CheckCircleIcon
    }
  }
  
  const getConfirmationIconClass = () => {
    switch (confirmationAction.value) {
      case 'approve': return 'bg-green-100 text-green-600'
      case 'reject': return 'bg-red-100 text-red-600'
      case 'delete': return 'bg-red-100 text-red-600'
      default: return 'bg-gray-100 text-gray-600'
    }
  }
  
  const getConfirmationButtonClass = () => {
    switch (confirmationAction.value) {
      case 'approve': return 'bg-green-600 hover:bg-green-700'
      case 'reject': return 'bg-red-600 hover:bg-red-700'
      case 'delete': return 'bg-red-600 hover:bg-red-700'
      default: return 'bg-gray-600 hover:bg-gray-700'
    }
  }
  
  const getConfirmationButtonText = () => {
    switch (confirmationAction.value) {
      case 'approve': return 'Valider'
      case 'reject': return 'Rejeter'
      case 'delete': return 'Supprimer'
      default: return 'Confirmer'
    }
  }
  
  // Export methods
  const exportToExcel = async () => {
    try {
      showNotification('Export Excel en cours...', 'success')
      showExportDropdown.value = false
      
      // Prepare data for Excel
      const excelData = filteredProducts.value.map(product => ({
        'ID': product.id,
        'Nom': product.name,
        'SKU': product.sku,
        'Catégorie': product.category_name,
        'Sous-catégorie': product.subcategory_name,
        'Prix': product.unit_price,
        'Stock': product.stock,
        'Statut': getStatusLabel(product.status),
        'Actif': product.is_active ? 'Oui' : 'Non',
        'Boosté': product.is_boosted ? 'Oui' : 'Non',
        'Statut Boost': getBoostStatusLabel(product.boost_status),
        'Boutique': product.boutique_name,
        'Marché': product.boutique_market,
        'Créé par': product.created_by_name,
        'Email créateur': product.created_by_email,
        'Date de création': formatDate(product.created_at),
        'Vues': product.views_count,
        'Ventes': product.sales_count
      }))
      
      // Create workbook and worksheet
      const wb = XLSX.utils.book_new()
      const ws = XLSX.utils.json_to_sheet(excelData)
      
      // Set column widths
      const colWidths = [
        { wch: 8 },  // ID
        { wch: 30 }, // Nom
        { wch: 15 }, // SKU
        { wch: 15 }, // Catégorie
        { wch: 20 }, // Sous-catégorie
        { wch: 10 }, // Prix
        { wch: 8 },  // Stock
        { wch: 12 }, // Statut
        { wch: 8 },  // Actif
        { wch: 8 },  // Boosté
        { wch: 12 }, // Statut Boost
        { wch: 20 }, // Boutique
        { wch: 15 }, // Marché
        { wch: 20 }, // Créé par
        { wch: 25 }, // Email
        { wch: 15 }, // Date
        { wch: 8 },  // Vues
        { wch: 8 }   // Ventes
      ]
      ws['!cols'] = colWidths
      
      // Add worksheet to workbook
      XLSX.utils.book_append_sheet(wb, ws, 'Produits')
      
      // Generate filename with current date
      const now = new Date()
      const dateStr = now.toISOString().split('T')[0]
      const filename = `admin_produits_${dateStr}.xlsx`
      
      // Save file
      XLSX.writeFile(wb, filename)
      
      showNotification(`Export Excel terminé ! ${filteredProducts.value.length} produits exportés.`, 'success')
      
    } catch (error) {
      console.error('Erreur lors de l\'export Excel:', error)
      showNotification('Erreur lors de l\'export Excel', 'error')
    }
  }
  
  const exportToPDF = async () => {
    try {
      showNotification('Export PDF en cours...', 'success')
      showExportDropdown.value = false
      
      const doc = new jsPDF('landscape', 'mm', 'a4')
      
      // Add title and header
      doc.setFontSize(20)
      doc.setTextColor(246, 90, 17) // Orange color
      doc.text('AliAdjamé - Gestion Admin des Produits', 20, 20)
      
      // Add date
      doc.setFontSize(12)
      doc.setTextColor(100, 100, 100)
      doc.text(`Généré le ${new Date().toLocaleDateString('fr-FR')}`, 20, 30)
      
      // Add stats summary
      doc.setFontSize(14)
      doc.setTextColor(0, 0, 0)
      doc.text('Résumé:', 20, 45)
      
      doc.setFontSize(10)
      doc.text(`Total des produits: ${filteredProducts.value.length}`, 20, 55)
      doc.text(`En attente: ${stats.value.pending_products}`, 20, 62)
      doc.text(`Validés: ${stats.value.active_products}`, 20, 69)
      doc.text(`Rejetés: ${stats.value.rejected_products}`, 20, 76)
      doc.text(`Boosts en attente: ${stats.value.pending_boosts}`, 20, 83)
      
      // Prepare table data (limit to 50 products for PDF size)
      const tableData = filteredProducts.value.slice(0, 50).map(product => [
        product.name.substring(0, 25) + (product.name.length > 25 ? '...' : ''),
        product.sku,
        product.category_name,
        formatCurrency(product.unit_price),
        product.stock,
        getStatusLabel(product.status),
        product.is_boosted ? 'Oui' : 'Non',
        product.boutique_name.substring(0, 20) + (product.boutique_name.length > 20 ? '...' : '')
      ])
      
      // Add table
      doc.autoTable({
        head: [['Produit', 'SKU', 'Catégorie', 'Prix', 'Stock', 'Statut', 'Boosté', 'Boutique']],
        body: tableData,
        startY: 90,
        styles: {
          fontSize: 8,
          cellPadding: 2
        },
        headStyles: {
          fillColor: [246, 90, 17],
          textColor: 255,
          fontStyle: 'bold'
        },
        alternateRowStyles: {
          fillColor: [245, 245, 245]
        },
        columnStyles: {
          0: { cellWidth: 35 },
          1: { cellWidth: 20 },
          2: { cellWidth: 25 },
          3: { cellWidth: 20 },
          4: { cellWidth: 15 },
          5: { cellWidth: 20 },
          6: { cellWidth: 15 },
          7: { cellWidth: 30 }
        }
      })
      
      // Add footer
      const pageCount = doc.internal.getNumberOfPages()
      for (let i = 1; i <= pageCount; i++) {
        doc.setPage(i)
        doc.setFontSize(8)
        doc.setTextColor(150, 150, 150)
        doc.text(`Page ${i} sur ${pageCount}`, doc.internal.pageSize.width - 30, doc.internal.pageSize.height - 10)
      }
      
      // Add note if more than 50 products
      if (filteredProducts.value.length > 50) {
        doc.setFontSize(10)
        doc.setTextColor(200, 0, 0)
        doc.text(`Note: Seuls les 50 premiers produits sont affichés (${filteredProducts.value.length} au total)`, 20, doc.lastAutoTable.finalY + 10)
      }
      
      // Generate filename with current date
      const now = new Date()
      const dateStr = now.toISOString().split('T')[0]
      const filename = `admin_produits_${dateStr}.pdf`
      
      // Save PDF
      doc.save(filename)
      
      showNotification(`Export PDF terminé ! ${Math.min(filteredProducts.value.length, 50)} produits exportés.`, 'success')
      
    } catch (error) {
      console.error('Erreur lors de l\'export PDF:', error)
      showNotification('Erreur lors de l\'export PDF', 'error')
    }
  }
  
  // Utility methods
  const formatCurrency = (amount) => {
    return new Intl.NumberFormat('fr-FR', {
      style: 'currency',
      currency: 'XOF',
      minimumFractionDigits: 0
    }).format(amount)
  }
  
  const formatDate = (dateString) => {
    return new Date(dateString).toLocaleDateString('fr-FR')
  }
  
  const formatTime = (dateString) => {
    return new Date(dateString).toLocaleTimeString('fr-FR', {
      hour: '2-digit',
      minute: '2-digit'
    })
  }
  
  const getStatusLabel = (status) => {
    const labels = {
      'En attente': 'En attente',
      'Actif': 'Validé',
      'Rejeté': 'Rejeté',
      'Brouillon': 'Brouillon',
      'Rupture': 'Rupture'
    }
    return labels[status] || status
  }
  
  const getStatusClass = (status) => {
    const classes = {
      'En attente': 'bg-yellow-100 text-yellow-800',
      'Actif': 'bg-green-100 success-color',
      'Rejeté': 'bg-red-100 error-color',
      'Brouillon': 'bg-gray-100 text-gray-800',
      'Rupture': 'bg-red-100 error-color'
    }
    return classes[status] || 'bg-gray-100 text-gray-800'
  }
  
  const getStatusIcon = (status) => {
    const icons = {
      'En attente': ClockIcon,
      'Actif': CheckCircleIcon,
      'Rejeté': XCircleIcon,
      'Brouillon': ClockIcon,
      'Rupture': XCircleIcon
    }
    return icons[status] || ClockIcon
  }
  
  const getBoostTypeLabel = (boostType) => {
    const labels = {
      'premium': 'Premium',
      'featured': 'Mis en avant',
      'urgent': 'Urgent',
      'basic': 'Basique'
    }
    return labels[boostType] || boostType
  }
  
  const getBoostStatusLabel = (boostStatus) => {
    const labels = {
      'pending': 'En attente',
      'active': 'Actif',
      'expired': 'Expiré',
      'rejected': 'Rejeté'
    }
    return labels[boostStatus] || boostStatus
  }
  
  const getBoostStatusClass = (boostStatus) => {
    const classes = {
      'pending': 'bg-yellow-100 text-yellow-800',
      'active': 'bg-green-100 text-green-800',
      'expired': 'bg-gray-100 text-gray-800',
      'rejected': 'bg-red-100 text-red-800'
    }
    return classes[boostStatus] || 'bg-gray-100 text-gray-800'
  }
  
  const getPendingPercentage = () => {
    const total = products.value.length
    if (total === 0) return 0
    return Math.round((stats.value.pending_products / total) * 100)
  }
  
  const getBoostPercentage = () => {
    const total = products.value.length
    if (total === 0) return 0
    return Math.round((stats.value.pending_boosts / total) * 100)
  }
  
  const showNotification = (message, type = 'success') => {
    notification.value = {
      show: true,
      message,
      type
    }
    
    setTimeout(() => {
      notification.value.show = false
    }, 5000)
  }
  
  // Lifecycle
  onMounted(() => {
    loadAllData()
  })
  </script>
  
  <style scoped>
  /* Animations personnalisées */
  @keyframes float-slow {
    0%, 100% { transform: translateY(0px) rotate(0deg); }
    50% { transform: translateY(-20px) rotate(5deg); }
  }
  
  @keyframes float-reverse {
    0%, 100% { transform: translateY(0px) rotate(0deg); }
    50% { transform: translateY(15px) rotate(-3deg); }
  }
  
  @keyframes float-diagonal {
    0%, 100% { transform: translate(0px, 0px) rotate(0deg); }
    50% { transform: translate(10px, -15px) rotate(2deg); }
  }
  
  @keyframes float-slow-reverse {
    0%, 100% { transform: translateY(0px) rotate(0deg); }
    50% { transform: translateY(25px) rotate(-4deg); }
  }
  
  @keyframes pulse-slow {
    0%, 100% { opacity: 0.4; transform: scale(1); }
    50% { opacity: 0.8; transform: scale(1.1); }
  }
  
  @keyframes pulse-delayed {
    0%, 100% { opacity: 0.3; transform: scale(1); }
    50% { opacity: 0.7; transform: scale(1.05); }
  }
  
  @keyframes pulse-delayed-2 {
    0%, 100% { opacity: 0.5; transform: scale(1); }
    50% { opacity: 0.9; transform: scale(1.15); }
  }
  
  @keyframes slide-down {
    0% { transform: translateY(-100%); opacity: 0; }
    50% { opacity: 1; }
    100% { transform: translateY(100%); opacity: 0; }
  }
  
  @keyframes slide-right {
    0% { transform: translateX(-100%); opacity: 0; }
    50% { opacity: 1; }
    100% { transform: translateX(100%); opacity: 0; }
  }
  
  @keyframes slide-up {
    0% { transform: translateY(100%); opacity: 0; }
    50% { opacity: 1; }
    100% { transform: translateY(-100%); opacity: 0; }
  }
  
  @keyframes rotate-slow {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
  }
  
  @keyframes float-small {
    0%, 100% { transform: translateY(0px); }
    50% { transform: translateY(-8px); }
  }
  
  .animate-float-slow {
    animation: float-slow 8s ease-in-out infinite;
  }
  
  .animate-float-reverse {
    animation: float-reverse 10s ease-in-out infinite;
  }
  
  .animate-float-diagonal {
    animation: float-diagonal 12s ease-in-out infinite;
  }
  
  .animate-float-slow-reverse {
    animation: float-slow-reverse 9s ease-in-out infinite;
  }
  
  .animate-pulse-slow {
    animation: pulse-slow 4s ease-in-out infinite;
  }
  
  .animate-pulse-delayed {
    animation: pulse-delayed 4s ease-in-out infinite 1s;
  }
  
  .animate-pulse-delayed-2 {
    animation: pulse-delayed-2 4s ease-in-out infinite 2s;
  }
  
  .animate-slide-down {
    animation: slide-down 6s linear infinite;
  }
  
  .animate-slide-right {
    animation: slide-right 8s linear infinite;
  }
  
  .animate-slide-up {
    animation: slide-up 7s linear infinite;
  }
  
  .animate-rotate-slow {
    animation: rotate-slow 20s linear infinite;
  }
  
  .animate-float-small {
    animation: float-small 3s ease-in-out infinite;
  }
  
  /* Responsive utilities */
  @media (max-width: 640px) {
    .table-responsive {
      font-size: 0.875rem;
    }
    
    .table-responsive th,
    .table-responsive td {
      padding: 0.5rem 0.75rem;
    }
  }
  
  /* Custom scrollbar */
  ::-webkit-scrollbar {
    width: 6px;
    height: 6px;
  }
  
  ::-webkit-scrollbar-track {
    background: #f1f1f1;
    border-radius: 3px;
  }
  
  ::-webkit-scrollbar-thumb {
    background: #c1c1c1;
    border-radius: 3px;
  }
  
  ::-webkit-scrollbar-thumb:hover {
    background: #a8a8a8;
  }
  
  /* Smooth transitions */
  * {
    transition: all 0.2s ease-in-out;
  }
  
  /* Print styles */
  @media print {
    .no-print {
      display: none !important;
    }
    
    .print-full-width {
      width: 100% !important;
      max-width: none !important;
    }
  }
  </style>