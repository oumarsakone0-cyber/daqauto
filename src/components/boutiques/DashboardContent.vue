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
      <Navbar/>
      <!-- Breadcrumb -->
      <div class="flex items-center text-sm text-gray-500 mb-4 sm:mb-6">
        <router-link to="/" class="hover:text-gray-700">
          <HomeIcon class="w-4 h-4 sm:w-5 sm:h-5" />
        </router-link>
        
        <span class="mx-2">/</span>
        <span class="font-medium text-gray-700 truncate">Products Dashboard</span>
      </div>

      <!-- Header responsive -->
      <div class="flex flex-col space-y-4 lg:flex-row lg:items-center lg:justify-between lg:space-y-0 mb-6 sm:mb-8">
        <div>
          <h1 class="text-2xl sm:text-3xl font-bold text-gray-900 mb-2">Product Management</h1>
          <p class="text-sm sm:text-base text-gray-600">Manage your inventory and products</p>
        </div>
        
        <!-- Actions mobiles optimisées -->
        <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-2 sm:gap-3">
          <!-- Export dropdown -->
          <div class="relative" ref="exportDropdownRef">
            <button 
              @click="showExportDropdown = !showExportDropdown"
              class="w-full sm:w-auto inline-flex items-center justify-center px-3 sm:px-4 py-2 rounded-lg shadow-sm text-sm font-medium btn-degrade-orange"
            >
              <DownloadIcon class="w-4 h-4" />
              <span >Export</span>
              <ChevronDownIcon class="w-4 h-4 " />
            </button>
            <div v-if="showExportDropdown" class="origin-top-right absolute right-0 w-50 mt-2 ring-1 ring-gray-400 rounded-md shadow-lg bg-white p-2 ">
              <div role="menu">
                <button @click="exportToPDF" class="flex items-center text-sm mb-2 btn-gray w-full" role="menuitem" >
                  <FileTextIcon class="w-4 h-4  error-color" />
                  Export to PDF
                </button>
                <button @click="exportToExcel" class="flex items-center text-sm btn-gray w-full" role="menuitem">
                  <FileTextIcon class="w-4 h-4 green-color" />
                  Export to Excel
                </button>
              </div>
            </div>
          </div>

          <!-- Add product button -->
          <button 
            @click="showAddProductModal = true"
            :disabled="!canAddMoreProducts()"
            :class="[
              canAddMoreProducts()
                ?'submit-btn'
                : 'btn-gray'
            ]"
          >
            <PlusIcon class="w-4 h-4" />
            <span class="hidden sm:inline">{{ canAddMoreProducts() ? 'Add produit' : 'Limit reached' }}</span>
            <span class="sm:hidden">{{ canAddMoreProducts() ? 'Add' : 'Limit' }}</span>
          </button>

          <!-- Premium button -->
            <!-- <button 
           
              @click="handleLogout()"
              class="w-full sm:w-auto inline-flex items-center  justify-center px-3 sm:px-4 py-2 rounded-lg shadow-sm text-sm font-medium btn-deconnexion"
            >
            <LogoutIcon class="w-4 h-4 text-white-500 cursor-pointer" />
              <span >Logout</span>
            </button> -->
        </div>
      </div>

      <!-- Section Premium Status -->
      <div class=" rounded-lg p-6 mb-6 text-white bg-degrade-orange">
        <div class="flex items-center justify-between">
          <div>
            <h3 class="text-lg font-semibold mb-2">
              {{ hasActiveSubscription() ? 'Active Premium Plan' : 'Free Plan' }}
              <span class="text-sm opacity-75 ml-2">({{ currentBoutique?.name || 'Store' }})</span>
            </h3>
            <div v-if="hasActiveSubscription() && getCurrentPlan()">
              <p class="text-sm opacity-90">
                Plan: {{ getCurrentPlan().plan_name }}
              </p>
              <p class="text-sm opacity-90">
                Products: {{ productCount }} / {{ getCurrentPlan().max_products }}
              </p>
              <p class="text-sm opacity-90" v-if="getCurrentPlan().expires_at">
                Expire on : {{ new Date(getCurrentPlan().expires_at).toLocaleDateString('fr-FR') }}
              </p>
              <div v-if="isExpiringSoon()" class="mt-2 px-3 py-1 bg-yellow-500 text-yellow-900 rounded-full text-xs font-medium inline-block">
                ⚠️ Expire in {{ getDaysRemaining() }} Day(s)
              </div>
            </div>
            <div v-else>
              <p class="text-sm opacity-90">
                Products: {{ productCount }} / 5 (free limit)
              </p>
              <p class="text-sm opacity-90">
                Go Premium for More Features
              </p>
            </div>
          </div>
          <div>
            <button
              @click="openPremiumModal"
              class="  px-4 py-2 rounded-lg font-medium hover:bg-gray-100 transition-colors text-sm btn-premium-style "
            >
              {{ hasActiveSubscription() ? 'Manage Subscription' : 'Upgrade to Premium' }}
            </button>
          </div>
        </div>
      </div>

      <!-- Loading State -->
      <div v-if="loading" class="mb-6 sm:mb-8">
        <div class="bg-white rounded-lg p-6 sm:p-8 text-center shadow-lg">
          <div class="inline-block animate-spin rounded-full h-10 w-10 sm:h-12 sm:w-12 border-4 primary-color border-t-transparent"></div>
          <p class="mt-4 text-gray-600 font-medium text-sm sm:text-base">Loading produits...</p>
        </div>
      </div>

      <!-- Error State -->
      <div v-if="hasError" class="mb-6 sm:mb-8">
        <div class="bg-gradient-to-r from-red-50 to-red-100 border border-red-200 rounded-lg p-4 sm:p-6 shadow-lg">
          <div class="flex flex-col sm:flex-row sm:items-center gap-4">
            <div class="w-10 h-10 bg-red-100 rounded-full flex items-center justify-center flex-shrink-0">
              <AlertCircleIcon class="w-5 h-5 error-color" />
            </div>
            <div class="flex-1 min-w-0">
              <p class="error-color font-medium text-sm sm:text-base">{{ error }}</p>
            </div>
            <button @click="refresh" class="w-full sm:w-auto px-4 py-2 error-background-color rounded-lg transition-colors font-medium text-sm">
              Try again
            </button>
          </div>
        </div>
      </div>

      <!-- Stats Cards responsive -->
      <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4 sm:gap-6 mb-6 sm:mb-8" v-if="!loading">
        <!-- Sélecteur de période -->
        <div class="bg-white overflow-hidden shadow-lg rounded-lg border border-gray-100 hover:shadow-xl transition-all duration-300">
          <div class="px-4 sm:px-6 py-4 sm:py-6">
            <div class="flex items-center justify-center mb-3 sm:mb-4">
              <div class="w-10 h-10 sm:w-12 sm:h-12 bg-gradient-to-br from-orange-200 to-orange-300 rounded-lg flex items-center justify-center">
                <CalendarIcon class="w-5 h-5 sm:w-6 sm:h-6 text-black" />
              </div>
            </div>
            <p class="text-xs sm:text-sm text-gray-600 text-center mb-2 sm:mb-3">Payment Deadline</p>
            <select 
              v-model="selectedPeriod" 
              @change="updateStats"
              class="input-style"
            >
              <option value="all">All</option>
              <option value="today">Today</option>
              <option value="week">This Week</option>
              <option value="month">This Month</option>
            </select>
          </div>
        </div>

        <!-- Total Produits -->
        <div class="bg-white overflow-hidden shadow-lg rounded-lg border border-gray-100 hover:shadow-xl transition-all duration-300">
          <div class="px-4 sm:px-6 py-4 sm:py-6">
            <div class="flex items-center mb-3 sm:mb-4">
              <div class="w-10 h-10 sm:w-12 sm:h-12 bg-gradient-to-br from-blue-200 to-blue-300 rounded-lg flex items-center justify-center mr-3 sm:mr-4 flex-shrink-0">
                <PackageIcon class="w-5 h-5 sm:w-6 sm:h-6 text-black" />
              </div>
              <div class="min-w-0">
                <p class="text-xs sm:text-sm text-gray-600">Product Total</p>
                <p class="text-xl sm:text-2xl font-bold text-gray-900">{{ currentStats.total_products }} / <span style="font-size: 30px; color: blue">{{ hasActiveSubscription() ? getCurrentPlan()?.max_products || '∞' : '5' }}</span></p>
              </div>
            </div>
            <div class="space-y-2">
              <div class="flex justify-between text-xs text-gray-500">
                <span class="truncate">Drafts: {{ detailedStats.draft }}</span>
                <span class="flex-shrink-0">{{ Math.round((currentStats.active_products / Math.max(currentStats.total_products, 1)) * 100) }}% assets</span>
              </div>
              <div class="w-full bg-gray-200 rounded-full h-2">
                <div class="bg-gradient-to-r from-blue-500 to-blue-600 h-2 rounded-full transition-all duration-300" :style="`width: ${Math.round((currentStats.active_products / Math.max(currentStats.total_products, 1)) * 100)}%`"></div>
              </div>
            </div>
          </div>
        </div>

        <!-- Produits Actifs -->
        <div class="bg-white overflow-hidden shadow-lg rounded-lg border border-gray-100 hover:shadow-xl transition-all duration-300">
          <div class="px-4 sm:px-6 py-4 sm:py-6">
            <div class="flex items-center mb-3 sm:mb-4">
              <div class="w-10 h-10 sm:w-12 sm:h-12 bg-gradient-to-br from-green-200 to-green-300 rounded-lg flex items-center justify-center mr-3 sm:mr-4 flex-shrink-0">
                <CheckCircleIcon class="w-5 h-5 sm:w-6 sm:h-6 text-black" />
              </div>
              <div class="min-w-0">
                <p class="text-xs sm:text-sm text-gray-600">Active Products</p>
                <p class="text-xl sm:text-2xl font-bold text-gray-900">{{ currentStats.active_products }}</p>
              </div>
            </div>
            <div class="space-y-2">
              <div class="flex justify-between text-xs text-gray-500">
                <span class="truncate">Average price: {{ formatPrice(detailedStats.averagePrice) }}</span>
                <span class="text-green-600 flex-shrink-0">Online</span>
              </div>
              <div class="w-full bg-gray-200 rounded-full h-2">
                <div class="bg-gradient-to-r from-green-500 to-green-600 h-2 rounded-full transition-all duration-300" style="width: 85%"></div>
              </div>
            </div>
          </div>
        </div>

        <!-- Stock Total -->
        <div class="bg-white overflow-hidden shadow-lg rounded-lg border border-gray-100 hover:shadow-xl transition-all duration-300">
          <div class="px-4 sm:px-6 py-4 sm:py-6">
            <div class="flex items-center mb-3 sm:mb-4">
              <div class="w-10 h-10 sm:w-12 sm:h-12 bg-gradient-to-br from-orange-200 to-orange-300 rounded-lg flex items-center justify-center mr-3 sm:mr-4 flex-shrink-0">
                <WarehouseIcon class="w-5 h-5 sm:w-6 sm:h-6 text-black" />
              </div>
              <div class="min-w-0">
                <p class="text-xs sm:text-sm text-gray-600">Total Stock</p>
                <p class="text-xl sm:text-2xl font-bold text-gray-900">{{ formatNumber(currentStats.total_stock) }}</p>
              </div>
            </div>
            <div class="space-y-2">
              <div class="flex justify-between text-xs text-gray-500">
                <span class="truncate">
                  <span class="error-color">Breakage: {{ detailedStats.outOfStock }}</span> | 
                  <span class="text-yellow-600">Low: {{ detailedStats.lowStock }}</span>
                </span>
                <span :class="detailedStats.outOfStock > 0 ? 'error-color' : detailedStats.lowStock > 0 ? 'text-yellow-600' : 'text-green-600'" class="flex-shrink-0">
                  {{ detailedStats.outOfStock > 0 ? 'Attention' : detailedStats.lowStock > 0 ? 'Surveiller' : 'Bon' }}
                </span>
              </div>
              <div class="w-full bg-gray-200 rounded-full h-2">
                <div 
                  :class="detailedStats.outOfStock > 0 ? 'bg-gradient-to-r from-red-500 to-red-600' : detailedStats.lowStock > 0 ? 'bg-gradient-to-r from-yellow-500 to-yellow-600' : 'bg-gradient-to-r from-green-500 to-green-600'" 
                  class="h-2 rounded-full transition-all duration-300" 
                  :style="`width: ${Math.min(100, Math.max(10, (currentStats.total_stock / Math.max(currentStats.total_products * 10, 1)) * 100))}%`"
                ></div>
              </div>
            </div>
          </div>
        </div>

        <!-- Vues et Ventes -->
        <div class="bg-white overflow-hidden shadow-lg rounded-lg border border-gray-100 hover:shadow-xl transition-all duration-300">
          <div class="px-4 sm:px-6 py-4 sm:py-6">
            <div class="flex items-center mb-3 sm:mb-4">
              <div class="w-10 h-10 sm:w-12 sm:h-12 bg-gradient-to-br from-purple-200 to-purple-300 rounded-lg flex items-center justify-center mr-3 sm:mr-4 flex-shrink-0">
                <EyeIcon class="w-5 h-5 sm:w-6 sm:h-6 text-black" />
              </div>
              <div class="min-w-0">
                <p class="text-xs sm:text-sm text-gray-600">Views Total</p>
                <p class="text-xl sm:text-2xl font-bold text-gray-900">{{ formatNumber(currentStats.total_views) }}</p>
              </div>
            </div>
            <div class="space-y-2">
              <div class="flex justify-between text-xs text-gray-500">
                <span class="truncate">Sales : {{ formatNumber(detailedStats.totalSales) }}</span>
                <span class="text-purple-600 flex-shrink-0">Performance</span>
              </div>
              <div class="w-full bg-gray-200 rounded-full h-2">
                <div class="bg-gradient-to-r from-purple-500 to-purple-600 h-2 rounded-full transition-all duration-300" style="width: 65%"></div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Main Content Card responsive -->
      <div class="bg-white shadow-lg rounded-lg overflow-hidden border border-gray-100" v-if="!loading">
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
                  ? 'shadow-lg btn-degrade-orange' 
                  : ' btn-gray'
              ]"
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
                <SearchIcon class="w-4 h-4 sm:w-5 sm:h-5 text-gray-400" />
              </div>
              <input 
                v-model="searchQuery"
                type="text" 
                class="input-style" 
                placeholder="Research product..."
              >
            </div>
            <div class="flex items-center gap-2 sm:gap-3">
              <button type="button" class="btn-gray">
                <ArrowUpDownIcon class="w-4 h-4" />
                <span class="hidden sm:inline text-sm">Sort</span>
              </button>
              <button type="button" class="btn-gray">
                <FilterIcon class="w-4 h-4" />
                <span class="hidden sm:inline text-sm">Filter</span>
              </button>
            </div>
          </div>
        </div>

        <!-- Products Table responsive -->
        <div class="overflow-x-auto">
        
          <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
              <tr>
                <th scope="col" class="px-3 sm:px-6 py-2 sm:py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                  <div class="flex items-center">
                    <input 
                      id="checkbox-all-search" 
                      type="checkbox" 
                      class="checkbox-style"
                      :checked="selectedProducts.length === paginatedProducts.length && paginatedProducts.length > 0"
                      @change="handleSelectAll($event.target.checked)"
                    >
                    <label for="checkbox-all-search" class="sr-only">checkbox</label>
                  </div>
                </th>
                <th scope="col" class="px-3 sm:px-6 py-2 sm:py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider" style="width: 200px; max-width: 200px; min-width: 200px;">Product</th>
                <th scope="col" class="hidden md:table-cell px-3 sm:px-6 py-2 sm:py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Categorie</th>
                <th scope="col" class="px-3 sm:px-6 py-2 sm:py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Price</th>
                <th scope="col" class="px-3 sm:px-6 py-2 sm:py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Stock</th>
                <th scope="col" class="hidden lg:table-cell px-3 sm:px-6 py-2 sm:py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                <th scope="col" class="hidden xl:table-cell px-3 sm:px-6 py-2 sm:py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Color(s)</th>
                <th scope="col" class="hidden lg:table-cell px-3 sm:px-6 py-2 sm:py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Performance</th>
                <th scope="col" class="px-3 sm:px-6 py-2 sm:py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
              </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
              <tr v-for="product in paginatedProducts" :key="product.id" class="hover:bg-gray-50 transition-colors">
                <td class="w-4 px-3 sm:px-6 py-3 sm:py-4">
                  <div class="flex items-center">
                    <input 
                      :id="`checkbox-table-search-${product.id}`" 
                      type="checkbox" 
                      class="checkbox-style"
                      :checked="selectedProducts.includes(product.id)"
                      @change="handleSelectProduct(product.id, $event.target.checked)"
                    >
                    <label :for="`checkbox-table-search-${product.id}`" class="sr-only">checkbox</label>
                  </div>
                </td>
                <td class="px-3 sm:px-6 py-3 sm:py-4 whitespace-nowrap" style="width: 200px; max-width: 200px;">
                  <div class="flex items-center space-x-2 sm:space-x-3">
                    <div class="w-10 h-10 sm:w-12 sm:h-12 bg-gray-100 rounded-lg flex items-center justify-center overflow-hidden flex-shrink-0">
                      <img v-if="product.primary_image" :src="product.primary_image" :alt="product.name" class="w-full h-full object-cover">
                      <span v-else class="text-lg sm:text-xl">{{ product.icon || '📦' }}</span>
                    </div>
                    <div class="min-w-0 flex-1">
                      <div class="text-xs sm:text-sm font-medium text-gray-900 truncate" :title="product.name">{{ product.name }}</div>
                      <div class="text-xs text-gray-500 truncate">{{ product.stock_number }}</div>
                      <!-- Informations supplémentaires visibles sur mobile -->
                      <div class="md:hidden mt-1">
                        <div class="text-xs text-gray-500">{{ product.category_name || 'Non catégorisé' }}</div>
                        <div class="lg:hidden mt-1">
                          <span :class="getStatusBadgeClass(product.status)" class="px-1.5 inline-flex text-xs leading-4 font-semibold rounded-full">
                            {{ product.status }}
                          </span>
                        </div>
                      </div>
                    </div>
                  </div>
                </td>
                <td class="hidden md:table-cell px-3 sm:px-6 py-3 sm:py-4 whitespace-nowrap">
                  <div>
                    <div class="text-xs sm:text-sm text-gray-900">{{ product.category_name || 'Non catégorisé' }}</div>
                    <div class="text-xs text-gray-500">{{ product.subcategory_name || 'Non sous-catégorisé' }}</div>
                  </div>
                </td>
                <td class="px-3 sm:px-6 py-3 sm:py-4 whitespace-nowrap">
                  <div>
                    <div class="text-xs sm:text-sm font-medium text-gray-900">{{ formatPrice(product.unit_price) }}</div>
                    <div v-if="product.wholesale_price" class="text-xs text-gray-500 hidden sm:block">
                      Gros: {{ formatPrice(product.wholesale_price) }} ({{ product.wholesale_min_qty }}+)
                    </div>
                  </div>
                </td>
                <td class="px-3 sm:px-6 py-3 sm:py-4 whitespace-nowrap">
                  <div class="text-xs sm:text-sm text-gray-900">{{ product.stock }}</div>
                  <div :class="getStockStatusClass(product.stock)" class="text-xs">
                    {{ getStockStatus(product.stock) }}
                  </div>
                </td>
                <td class="hidden lg:table-cell px-3 sm:px-6 py-3 sm:py-4 whitespace-nowrap">
                  <span :class="getStatusBadgeClass(product.status)" class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full">
                    {{ product.status }}
                  </span>
                </td>
                <td class="hidden xl:table-cell px-3 sm:px-6 py-3 sm:py-4 whitespace-nowrap" style="width: 80px;">
                  <div class="flex flex-wrap gap-1" style="width: 80px;">
                    <div 
                      v-for="(color, index) in product.colors" 
                      :key="index" 
                      class="w-5 h-5 sm:w-6 sm:h-6 rounded-lg border border-gray-200 cursor-pointer hover:scale-110 transition-transform shadow-sm" 
                      :style="{ backgroundColor: color }"
                      :title="getColorName(color)"
                    ></div>
                  </div>
                </td>
                <td class="hidden lg:table-cell px-3 sm:px-6 py-3 sm:py-4 whitespace-nowrap">
                  <div class="space-y-1">
                    <div class="text-xs text-gray-500">Sales: {{ product.sales_count || 0 }}</div>
                    <div class="text-xs text-gray-500">Views: {{ product.views_count || 0 }}</div>
                    <div class="text-xs text-gray-500">WhatsApp: {{ product.views_count || 0 }}</div>
                  </div>
                </td>
                <td class="px-3 sm:px-6 py-3 sm:py-4 whitespace-nowrap text-xs sm:text-sm font-medium">
                  <div class="flex items-center space-x-2 sm:space-x-3">
                    <!-- Bouton Booster -->
                    <div v-if="hasActiveBoost(product.id)" class="text-xs green-color font-medium mb-1">
                      <div class="text-xs green-color" v-if="getBoostStatus(product.id) == 'active'">🚀 Activated</div>
                      <div class="text-xs primary-color" v-if="getBoostStatus(product.id) == 'pending'">⏳ Waiting</div>
                      <div class="text-xs text-gray-500" v-if="getBoostStatus(product.id) == 'active'" style="padding-top: 5px;">{{ getBoostExpiration(product.id) }}</div>
                    </div>
                    <button
                    v-else 
                      @click="openBoostModal(product)" 
                      class="inline-flex items-center px-2 py-1 text-xs font-medium rounded-lg shadow-sm btn-degrade-orange"
                    >
                      <ZapIcon class="w-3 h-3 mr-1" />
                      <span class="hidden sm:inline">Boost</span>
                      <span class="sm:hidden">🚀</span>
                    </button>
                    
                    <button @click="viewProduct(product)" class="text-xs sm:text-sm submit-btn">
                      <span class="hidden sm:inline">Details</span>
                      <EyeIcon class="w-4 h-4 sm:hidden" />
                    </button>

                    <div class="relative">
                      <button @click="toggleActionMenu(product.id)" class="btn-gray">
                        <MoreHorizontalIcon class="h-4 w-4 sm:h-5 sm:w-5" />
                      </button>
                      <div v-if="activeActionMenu === product.id" class="origin-top-right absolute right-0 mt-2 w-48 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5 z-20 p-2">
                        <div class="py-1" role="menu">
                          <button @click="viewProduct(product)" class="flex items-center w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 mb-2 btn-gray">
                            <EyeIcon class="w-4 h-4 mr-3" />
                            See details
                          </button>
                          <button @click="editProduct(product)" class="flex items-center w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 mb-2 btn-gray">
                            <EditIcon class="w-4 h-4 mr-3" />
                            Edit
                          </button>
                          <button @click="duplicateProduct(product)" class="flex items-center w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 mb-2 btn-gray">
                            <CopyIcon class="w-4 h-4 mr-3" />
                            Duplicate
                          </button>
                          <button @click="deleteProductAction(product.id)" class="flex items-center w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-red-50 mb-2 btn-gray">
                            <Trash2Icon class="w-4 h-4 mr-3" />
                            Delete
                          </button>
                        </div>
                      </div>
                    </div>
                  </div>
                </td>
              </tr>
            </tbody>
          </table>
        </div>

        <!-- Pagination responsive -->
        <div class="bg-gray-50 px-4 sm:px-6 py-3 sm:py-4 flex flex-col sm:flex-row sm:items-center sm:justify-between border-t border-gray-200 gap-3 sm:gap-0">
          <div class="flex items-center space-x-2 sm:space-x-4 text-xs sm:text-sm">
            <span class="text-gray-700 hidden sm:inline">Items per page</span>
            <span class="text-gray-700 sm:hidden">Per page</span>
            <select 
              v-model="itemsPerPage"
              @change="handleItemsPerPageChange"
              class="bg-white border border-gray-300 text-gray-900 text-xs sm:text-sm rounded-lg focus:ring-orange-500 focus:border-orange-500 p-4 py-2 sm:p-3 w-15"
            >
              
              <option value="25">25</option>
              <option value="50">50</option>
              <option value="100">100</option>
            </select>
            <span class="text-gray-700 text-xs sm:text-sm">
              <span class="hidden sm:inline">
                Showing {{ ((currentPage - 1) * itemsPerPage) + 1 }} à 
                {{ Math.min(currentPage * itemsPerPage, filteredProducts.length) }} 
                of {{ filteredProducts.length }} results
              </span>
              <span class="sm:hidden">
                {{ ((currentPage - 1) * itemsPerPage) + 1 }}-{{ Math.min(currentPage * itemsPerPage, filteredProducts.length) }} 
                / {{ filteredProducts.length }}
              </span>
            </span>
          </div>
          <div class="flex items-center justify-center sm:justify-end space-x-1 sm:space-x-2">
            <button 
              @click="handlePageChange(currentPage - 1)"
              :disabled="isFirstPage"
              class="relative inline-flex items-center px-2 sm:px-3 py-1.5 sm:py-2 border border-gray-300 text-xs sm:text-sm font-medium rounded-lg text-gray-700 bg-white hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed transition-colors"
            >
              <ChevronLeftIcon class="w-3 h-3 sm:w-4 sm:h-4 mr-1" />
              <span class="hidden sm:inline">Previous</span>
              <span class="sm:hidden">Prev</span>
            </button>
            <div class="flex items-center space-x-1">
              <button 
                v-for="page in Math.min(3, totalPages)" 
                :key="page"
                @click="handlePageChange(page)"
                :class="[
                  'relative inline-flex items-center px-2 sm:px-4 py-1.5 sm:py-2 border text-xs sm:text-sm font-medium rounded-lg transition-colors',
                  currentPage === page 
                    ? 'bg-orange' 
                    : 'bg-white border-gray-300 text-gray-500 hover:bg-gray-50'
                ]"
              >
                {{ page }}
              </button>
            </div>
            <button 
              @click="handlePageChange(currentPage + 1)"
              :disabled="isLastPage"
              class="relative inline-flex items-center px-2 sm:px-3 py-1.5 sm:py-2 border border-gray-300 text-xs sm:text-sm font-medium rounded-lg text-gray-700 bg-white hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed transition-colors"
            >
              <span class="hidden sm:inline">Next</span>
              <ChevronRightIcon class="w-3 h-3 sm:w-4 sm:h-4 ml-1" />
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- Modal Premium -->
    <div v-if="showPremiumModal" class="fixed inset-0 bg-black/60 backdrop-blur-sm flex items-center justify-center z-50 p-4">
      <div class="bg-white rounded-2xl shadow-2xl max-w-4xl w-full max-h-[90vh] overflow-y-auto">
        <div class="p-6 border-b border-gray-200">
          <div class="flex items-center justify-between">
            <div class="flex items-center space-x-3">
              <div class="w-12 h-12 bg-orange rounded-lg flex items-center justify-center">
                <CrownIcon class="w-6 h-6 text-white" />
              </div>
              <div>
                <h2 class="text-2xl font-bold text-gray-900">Premium plans</h2>
                <p class="text-gray-600">Choose the plan that fits your needs to <span class="font-bold text-gray-800">{{ currentBoutique?.name || 'Your Store' }}</span> </p>
              </div>
            </div>
              <XIcon @click="showPremiumModal = false" class="w-7 h-7 text-gray-700 cursor-pointer" />
          </div>
        </div>

        <div class="p-6">
          <!-- Abonnement actuel -->
          <div v-if="hasActiveSubscription() && getCurrentPlan()" class="bg-green-50 border border-green-200 rounded-lg p-4 mb-6">
            <h3 class="font-semibold text-green-800 mb-2">Your current subscription for {{ currentBoutique?.name }}</h3>
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4 text-sm">
              <div>
                <span class="text-gray-600">Plan:</span>
                <span class="font-medium ml-2">{{ getCurrentPlan().plan_name }}</span>
              </div>
              <div>
                <span class="text-gray-600">Price:</span>
                <span class="font-medium ml-2">{{ formatPrice(getCurrentPlan().price) }}/Year</span>
              </div>
              <div>
                <span class="text-gray-600">Expire on:</span>
                <span class="font-medium ml-2">{{ new Date(getCurrentPlan().expires_at).toLocaleDateString('us-US') }}</span>
              </div>
              <div>
                <span class="text-gray-600">Remaining Days:</span>
                <span class="font-medium ml-2">{{ getDaysRemaining() }} day(s)</span>
              </div>
            </div>
          </div>

          <!-- Plans disponibles -->
          <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
            <!-- Plan Starter -->
            <div class="bg-gradient-to-br from-blue-50 to-blue-100 rounded-xl p-6 border-2 border-blue-200 hover:border-blue-300 transition-all">
              <div class="text-center">
                <div class="w-16 h-16 bg-gradient-to-br from-blue-500 to-blue-600 rounded-full flex items-center justify-center mx-auto mb-4">
                  <PackageIcon class="w-8 h-8 text-white" />
                </div>
                <h3 class="text-xl font-bold text-gray-900 mb-2">Small Store</h3>
                <div class="text-3xl font-bold text-blue-600 mb-1">$15</div>
                <div class="text-sm text-gray-600 mb-4">Year Subscription</div>
                <div class="text-lg font-semibold text-gray-900 mb-6">15 products</div>
                
                <ul class="space-y-3 mb-6 text-sm text-gray-600">
                  <li class="flex items-center">
                    <CheckCircleIcon class="w-4 h-4 text-blue-500 mr-2" />
                    Up to 15 products
                  </li>
                  <li class="flex items-center">
                    <CheckCircleIcon class="w-4 h-4 text-blue-500 mr-2" />
                    Products management
                  </li>
                  <li class="flex items-center">
                    <CheckCircleIcon class="w-4 h-4 text-blue-500 mr-2" />
                    Email Support
                  </li>
                </ul>
                
                <button 
                  @click="subscribeToPlan('starter')" 
                  :disabled="premiumLoading"
                  class="w-full bg-gradient-to-r from-blue-500 to-blue-600 text-white py-3 rounded-lg font-medium hover:from-blue-600 hover:to-blue-700 transition-all disabled:opacity-50"
                >
                  {{ premiumLoading ? 'treatment...' : 'Choose this plan' }}
                </button>
              </div>
            </div>

            <!-- Plan Business -->
            <div class="bg-gradient-to-br from-green-50 to-green-100 rounded-xl p-6 border-2 border-green-300 hover:border-green-400 transition-all relative">
              <div class="absolute -top-3 left-1/2 transform -translate-x-1/2">
                <span class="bg-gradient-to-r from-green-500 to-green-600 text-white px-4 py-1 rounded-full text-xs font-medium">
                  Popular
                </span>
              </div>
              <div class="text-center">
                <div class="w-16 h-16 bg-gradient-to-br from-green-500 to-green-600 rounded-full flex items-center justify-center mx-auto mb-4">
                  <WarehouseIcon class="w-8 h-8 text-white" />
                </div>
                <h3 class="text-xl font-bold text-gray-900 mb-2">Average Store</h3>
                <div class="text-3xl font-bold text-green-500 mb-1">$15</div>
                <div class="text-sm text-gray-600 mb-4">Year Subscription</div>
                <div class="text-lg font-semibold text-gray-900 mb-6">50 products</div>
                
                <ul class="space-y-3 mb-6 text-sm text-gray-600">
                  <li class="flex items-center">
                    <CheckCircleIcon class="w-4 h-4 text-green-500 mr-2" />
                    Up to 50 products
                  </li>
                  <li class="flex items-center">
                    <CheckCircleIcon class="w-4 h-4 text-green-500 mr-2" />
                    Products managament
                  </li>
                  <li class="flex items-center">
                    <CheckCircleIcon class="w-4 h-4 text-green-500 mr-2" />
                    Detailed Reports
                   </li>
                  <li class="flex items-center">
                    <CheckCircleIcon class="w-4 h-4 text-green-500 mr-2" />
                    Owner Support
                  </li>
                </ul>
                
                <button 
                  @click="subscribeToPlan('business')" 
                  :disabled="premiumLoading"
                  class="w-full bg-gradient-to-r from-green-500 to-green-600 text-white py-3 rounded-lg font-medium hover:from-orange-600 hover:to-orange-700 transition-all disabled:opacity-50 submit-btn h-11"
                >
                  {{ premiumLoading ? 'Treatment...' : 'Choose this plan' }}
                </button>
              </div>
            </div>

            <!-- Plan Enterprise -->
            <div class="bg-gradient-to-br from-orange-50 to-orange-100 rounded-xl p-6 border-2 border-orange-200 hover:border-orange-300 transition-all">
              <div class="text-center">
                <div class="w-16 h-16 bg-orange rounded-full flex items-center justify-center mx-auto mb-4">
                  <CrownIcon class="w-8 h-8 text-white" />
                </div>
                <h3 class="text-xl font-bold text-gray-900 mb-2">Big Store</h3>
                <div class="text-3xl font-bold primary-color mb-1">$25</div>
                <div class="text-sm text-gray-600 mb-4">Year Subscription</div>
                <div class="text-lg font-semibold text-gray-900 mb-6">120 products</div>
                
                <ul class="space-y-3 mb-6 text-sm text-gray-600">
                  <li class="flex items-center">
                    <CheckCircleIcon class="w-4 h-4 primary-color mr-2" />
                    Up to 120 products
                  </li>
                  <li class="flex items-center">
                    <CheckCircleIcon class="w-4 h-4 primary-color mr-2" />
                    All Features
                  </li>
                  <li class="flex items-center">
                    <CheckCircleIcon class="w-4 h-4 primary-color mr-2" />
                    Daily Reports
                  </li>
                  <li class="flex items-center">
                    <CheckCircleIcon class="w-4 h-4 primary-color mr-2" />
                   24/7 Support 
                  </li>
                </ul>
                
                <button 
                  @click="subscribeToPlan('enterprise')" 
                  :disabled="premiumLoading"
                  class="w-full py-3 rounded-lg font-medium  transition-all disabled:opacity-50 btn-degrade-orange h-11"
                >
                  {{ premiumLoading ? 'Treatment...' : 'Choose this plan' }}
                </button>
              </div>
            </div>
          </div>

          <!-- Actions pour l'abonnement actuel -->
          <div v-if="hasActiveSubscription()" class="pt-6 border-t border-gray-200">
            <div class="flex justify-center space-x-4">
              <button
                @click="renewSubscription"
                class="px-6 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors"
                style="background-color: yellowgreen;"
                :disabled="premiumLoading"
              >
                {{ premiumLoading ? 'Treatment...' : 'Renew the subscription' }}
              </button>
              <button
                @click="cancelSubscription"
                class="px-6 py-2 border border-red-300 text-red-600 rounded-lg hover:bg-red-50 transition-colors"
                style="background-color: #e8e8e8;"
                :disabled="premiumLoading"
              >
                {{ premiumLoading ? 'Treatment...' : 'Cancel the subscription' }}
              </button>
            </div>
          </div>

          <!-- Messages d'erreur -->
          <div v-if="premiumError" class="mt-4 p-4 bg-red-50 border border-red-200 rounded-lg">
            <p class="text-red-700 text-sm">{{ premiumError }}</p>
          </div>
        </div>
      </div>
    </div>

    <!-- Modal Boost Améliorée -->
    <div v-if="showBoostModal" class="fixed inset-0 bg-black/60 backdrop-blur-sm flex items-center justify-center z-50 p-4">
      <div class="bg-white rounded-2xl shadow-2xl max-w-3xl w-full max-h-[90vh] overflow-y-auto">
        <div class="p-6 border-b border-gray-200">
          <div class="flex items-center justify-between">
            <div class="flex items-center space-x-3">
              <div class="w-12 h-12 bg-orange rounded-lg flex items-center justify-center">
                <ZapIcon class="w-6 h-6 text-white" />
              </div>
              <div>
                <h2 class="text-2xl font-bold text-gray-900">Boost product</h2>
                <p class="text-gray-600">{{ selectedProductForBoost?.name }}</p>
              </div>
            </div>
              <XIcon @click="closeBoostModal" class="w-7 h-7 text-gray-700 cursor-pointer" />
          </div>
        </div>

        <div class="p-6">
          <!-- Étape 1: Sélection du type de boost -->
          <div v-if="!selectedBoostOption" class="space-y-4">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Choose your boost form</h3>
            
            <!-- Option Boost Standard -->
            <div class="bg-gradient-to-br from-green-50 to-green-100 rounded-xl p-6 border-2 border-green-200 hover:border-green-300 transition-all cursor-pointer" @click="selectBoostOption('basic')">
              <div class="flex items-center justify-between">
                <div class="flex-1">
                  <div class="flex items-center space-x-3 mb-3">
                    <div class="w-10 h-10 bg-gradient-to-br from-green-400 to-green-500 rounded-lg flex items-center justify-center">
                      <TrendingUpIcon class="w-5 h-5 text-white" />
                    </div>
                    <div>
                      <h3 class="text-lg font-bold text-gray-900">Basic Boost </h3>
                      <p class="text-sm text-gray-600">Improved visibility for your product</p>
                    </div>
                  </div>
                  <ul class="space-y-2 text-sm text-gray-600 mb-4">
                    <li class="flex items-center">
                      <CheckCircleIcon class="w-4 h-4 text-green-500 mr-2" />
                      Product highlighted
                    </li>
                    <li class="flex items-center">
                      <CheckCircleIcon class="w-4 h-4 text-green-500 mr-2" />
                      Badge "Boosted Product"
                    </li>
                    <li class="flex items-center">
                      <CheckCircleIcon class="w-4 h-4 text-green-500 mr-2" />
                      Property in searches
                    </li>
                  </ul>
                </div>
                <div class="text-right">
                  <div class="text-2xl font-bold text-green-600">$5</div>
                  <div class="text-sm text-gray-500">By week</div>
                </div>
              </div>
            </div>

            <!-- Option Boost Premium -->
            <div class="bg-gradient-to-br from-orange-50 to-orange-100 rounded-xl p-6 border-2 border-orange-200 hover:border-orange-300 transition-all cursor-pointer relative" @click="selectBoostOption('premium')">
              <div class="absolute -top-3 left-1/2 transform -translate-x-1/2">
                <span class="bg-orange text-white px-4 py-1 rounded-full text-xs font-medium">
                  Recommended
                </span>
              </div>
              <div class="flex items-center justify-between">
                <div class="flex-1">
                  <div class="flex items-center space-x-3 mb-3">
                    <div class="w-10 h-10 bg-orange rounded-lg flex items-center justify-center">
                      <RocketIcon class="w-5 h-5 text-white" />
                    </div>
                    <div>
                      <h3 class="text-lg font-bold text-gray-900">Premium boost</h3>
                      <p class="text-sm text-gray-600">Maximum Visibility + Advertisements</p>
                    </div>
                  </div>
                  <ul class="space-y-2 text-sm text-gray-600 mb-4">
                    <li class="flex items-center">
                      <CheckCircleIcon class="w-4 h-4 primary-color mr-2" />
                      Product in foreground
                    </li>
                    <li class="flex items-center">
                      <CheckCircleIcon class="w-4 h-4 primary-color mr-2" />
                      Home page display
                    </li>
                    <li class="flex items-center">
                      <CheckCircleIcon class="w-4 h-4 primary-color mr-2" />
                      Advertising Social Media
                    </li>
                    <li class="flex items-center">
                      <CheckCircleIcon class="w-4 h-4 primary-color mr-2" />
                      Badge "Featured product"
                    </li>
                  </ul>
                </div>
                <div class="text-right">
                  <div class="text-2xl font-bold primary-color">$12</div>
                  <div class="text-sm text-gray-500">By week</div>
                </div>
              </div>
            </div>
          </div>

          <!-- Étape 2: Configuration du boost -->
          <div v-if="selectedBoostOption" class="space-y-6">
            <!-- Récapitulatif du choix -->
            <div class="bg-gray-50 rounded-lg p-4 border border-gray-200">
              <div class="flex items-center justify-between">
                <div class="flex items-center space-x-3">
                  <div :class="selectedBoostOption === 'basic' ? 'bg-gradient-to-br from-green-400 to-green-500' : 'bg-orange'" class="w-10 h-10 rounded-lg flex items-center justify-center">
                    <component :is="selectedBoostOption === 'basic' ? TrendingUpIcon : RocketIcon" class="w-5 h-5 text-white"  />
                  </div>
                  <div>
                    <h4 class="font-semibold text-gray-900">
                      {{ selectedBoostOption === 'basic' ? 'Basic Boost ' : 'Premium Boost ' }}
                    </h4>
                    <p class="text-sm text-gray-600">
                      {{ selectedBoostOption === 'basic' ? '$5 By week' : '$12 By week' }}
                    </p>
                  </div>
                </div>
                <button @click="resetBoostSelection" class="btn-outline h-11" style="border-radius: 8px;  ">
                  Change
                </button>
              </div>
            </div>

            <!-- Configuration des dates -->
            <div class="space-y-4">
              <h4 class="font-semibold text-gray-900">Period Configuration</h4>
              
              <!-- Durée du boost -->
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Boost duration</label>
                <div class="grid grid-cols-3 gap-3">
                  <button 
                    v-for="duration in durationOptions" 
                    :key="duration.weeks"
                    @click="selectDuration(duration.weeks)"
                    :class="[
                      'h-15',
                      selectedDuration === duration.weeks 
                        ? 'btn-degrade-orange' 
                        : 'btn-gray'
                    ]"
                  >
                    <div class="font-semibold">{{ duration.weeks }} Week{{ duration.weeks > 1 ? 's' : '' }}</div>
                    <div class="text-sm text-gray-800">{{ formatPrice(duration.price) }}</div>
                  </button>
                </div>
              </div>

              <!-- Date de début -->
              <div>
                <label for="start-date" class="block text-sm font-medium text-gray-700 mb-2">Start Date</label>
                <input 
                  id="start-date"
                  v-model="boostStartDate"
                  type="date"
                  :min="minStartDate"
                  class="input-style"
                  @change="updateEndDate"
                >
              </div>

              <!-- Date de fin (calculée automatiquement) -->
              <div>
                <label for="end-date" class="block text-sm font-medium text-gray-700 mb-2">End Date (automatically calculated)</label>
                <input 
                  id="end-date"
                  v-model="boostEndDate"
                  type="date" 
                  readonly
                  class="w-full px-3 py-2 border border-gray-300 rounded-lg bg-gray-50 text-gray-600 cursor-not-allowed"
                >
                <p class="text-xs text-gray-500 mt-1">The end date is calculated automatically according to the chosen duration</p>
              </div>

              <!-- Récapitulatif du coût -->
              <div class="bg-gradient-to-r from-blue-50 to-indigo-50 rounded-lg p-4 border border-blue-200">
                <div class="flex items-center justify-between">
                  <div>
                    <h5 class="font-semibold text-gray-900">Total cost</h5>
                    <p class="text-sm text-gray-600">
                      {{ selectedDuration }} week{{ selectedDuration > 1 ? 's' : '' }} of {{ selectedBoostOption === 'basic' ? 'Basic' : 'Premium' }} boost
                    </p>
                  </div>
                  <div class="text-right">
                    <div class="text-2xl font-bold primary-color">{{ formatPrice(totalCost) }}</div>
                    <div class="text-sm text-gray-500">{{ formatPrice(weeklyPrice) }} / week</div>
                  </div>
                </div>
              </div>
            </div>

            <!-- Boutons d'action -->
            <div class="flex space-x-3 pt-4">
              <button @click="resetBoostSelection" class="flex-1 btn-gray">
                Back
              </button>
              <button @click="proceedToPayment" :disabled="!isConfigurationValid || boostLoading" class="flex-1 submit-btn">
                {{ boostLoading ? 'Treatment...' : 'Validate the request' }}
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Modal de Notification Premium -->
    <div v-if="showPremiumNotificationModal" class="fixed inset-0 bg-black/60 backdrop-blur-sm flex items-center justify-center z-50 p-4">
      <div class="bg-white rounded-2xl shadow-2xl max-w-md w-full">
        <div class="p-6">
          <div class="text-center">
            <!-- Icône de succès -->
            <div class="w-16 h-16 bg-gradient-to-br from-green-500 to-green-600 rounded-full flex items-center justify-center mx-auto mb-4">
              <CheckCircleIcon class="w-8 h-8 text-white" />
            </div>
            
            <!-- Titre -->
            <h2 class="text-xl font-bold text-gray-900 mb-3">Request received</h2>
            
            <!-- Message principal -->
            <p class="text-gray-600 mb-6 leading-relaxed">
              {{ premiumNotificationMessage }}
            </p>
            
            <!-- Section contact -->
            <div class="bg-gradient-to-r from-orange-50 to-orange-100 rounded-lg p-4 mb-6">
              <h3 class="font-semibold text-gray-900 mb-2">Need to speed up the process ?</h3>
              <p class="text-sm text-gray-600 mb-3">Call our team directly :</p>
              <div class="flex items-center justify-center space-x-2">
                <div class="w-8 h-8 bg-green-500 rounded-full flex items-center justify-center">
                  <svg class="w-4 h-4 text-white" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M2 3a1 1 0 011-1h2.153a1 1 0 01.986.836l.74 4.435a1 1 0 01-.54 1.06l-1.548.773a11.037 11.037 0 006.105 6.105l.774-1.548a1 1 0 011.059-.54l4.435.74a1 1 0 01.836.986V17a1 1 0 01-1 1h-2C7.82 18 2 12.18 2 5V3z"></path>
                  </svg>
                </div>
                <a href="tel:+22501536760662" class="text-lg font-bold text-green-600 hover:text-green-700 transition-colors">
                  +225 01 53 67 60 62
                </a>
              </div>
            </div>
            
            <!-- Bouton de fermeture -->
            <button 
              @click="closePremiumNotificationModal"
              class="btn-degrade-orange w-full h-11"
            >
              Thank you
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- Modal de Notification Boost -->
    <div v-if="showBoostNotificationModal" class="fixed inset-0 bg-black/60 backdrop-blur-sm flex items-center justify-center z-50 p-4">
      <div class="bg-white rounded-2xl shadow-2xl max-w-md w-full">
        <div class="p-6">
          <div class="text-center">
            <!-- Icône de succès -->
            <div class="w-16 h-16 bg-gradient-to-br from-green-500 to-green-600 rounded-full flex items-center justify-center mx-auto mb-4">
              <CheckCircleIcon class="w-8 h-8 text-white" />
            </div>
            
            <!-- Titre -->
            <h2 class="text-xl font-bold text-gray-900 mb-3">Boost request received </h2>
            
            <!-- Message principal -->
            <p class="text-gray-600 mb-4 leading-relaxed">
              Your boost for <strong>{{ boostConfirmationData?.product_name }}</strong> request has been received.
            </p>
            <p class="text-gray-600 mb-6 leading-relaxed">
              A Daq Auto agent will contact you within 24 hours to finalize the activation.
            </p>
            
            <!-- Détails du boost -->
            <div class="bg-gray-50 rounded-lg p-3 mb-4 text-xs">
              <div class="flex justify-between mb-1">
                <span>Boost type : </span>
                <span class="font-medium">{{ boostConfirmationData?.boost_type }}</span>
              </div>
              <div class="flex justify-between mb-1">
                <span>Duration :</span>
                <span class="font-medium">{{ boostConfirmationData?.duration_days }} day</span>
              </div>
              <div class="flex justify-between">
                <span>Amount :</span>
                <span class="font-medium">{{ formatPrice(boostConfirmationData?.amount) }}</span>
              </div>
            </div>
            
            <!-- Section contact -->
            <div class="bg-gradient-to-r from-orange-50 to-orange-100 rounded-lg p-4 mb-6">
              <h3 class="font-semibold text-gray-900 mb-2">Need to speed up the process ?</h3>
              <p class="text-sm text-gray-600 mb-3">Call our team directly :</p>
              <div class="flex items-center justify-center space-x-2">
                <div class="w-8 h-8 bg-green-500 rounded-full flex items-center justify-center">
                  <svg class="w-4 h-4 text-white" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M2 3a1 1 0 011-1h2.153a1 1 0 01.986.836l.74 4.435a1 1 0 01-.54 1.06l-1.548.773a11.037 11.037 0 006.105 6.105l.774-1.548a1 1 0 011.059-.54l4.435.74a1 1 0 01.836.986V17a1 1 0 01-1 1h-2C7.82 18 2 12.18 2 5V3z"></path>
                  </svg>
                </div>
                <a href="tel:+22501536760662" class="text-lg font-bold text-green-600 hover:text-green-700 transition-colors">
                  +225 01 53 67 60 62
                </a>
              </div>
            </div>
            
            <!-- Bouton de fermeture -->
            <button 
              @click="closeBoostNotificationModal"
              class="w-full bg-orange text-white py-3 rounded-lg font-medium hover:from-orange-600 hover:to-orange-700 transition-all"
            >
              Thank you
          </button>
          </div>
        </div>
      </div>
    </div>

    <!-- Modales existantes -->
    <AddProductModal 
      v-if="showAddProductModal" 
      :boutique-id="currentBoutique?.id"
      :user-id="currentUser?.id"
      @close="showAddProductModal = false"
      @save="handleAddProduct"
      
    />

    <DuplicateProductModal 
      v-if="showDuplicateProductModal" 
      :boutique-id="currentBoutique?.id"
      :dataduplicate="DataDuplicate"
      :user-id="currentUser?.id"
      @close="showDuplicateProductModal = false"
      @save="handleAddProduct"
    />

    <ViewProductModal 
      v-if="showViewModal" 
      :product="selectedProduct"
      @close="showViewModal = false"
      @edit="editProduct"
    />

    <EditProductModal 
      v-if="showEditModal" 
      :product="selectedProduct"
      :boutique-id="currentBoutique?.id"
      :user-id="currentUser?.id"
      @close="showEditModal = false"
      @save="handleEditProduct"
    />

    <!-- Notification Toast responsive -->
    <div v-if="notification.show" class="fixed bottom-4 right-4 left-4 sm:left-auto z-50 bg-gradient-to-r from-green-500 to-green-600 text-white py-3 sm:py-4 px-4 sm:px-6 rounded-lg shadow-2xl transition-all duration-300 transform">
      <div class="flex items-center gap-2 sm:gap-3">
        <CheckCircleIcon class="w-4 h-4 sm:w-5 sm:h-5 flex-shrink-0" />
        <span class="text-sm sm:text-base">{{ notification.message }}</span>
      </div>
    </div>
  </div>
</template>

<script setup>
import { useRouter } from 'vue-router'
import { ref, computed, onMounted, onUnmounted, watch } from 'vue'
import { productsApi, premiumApi, boostApi } from '../../services/api'
// Dans les imports
import Navbar from './Navbar.vue'
import AddProductModal from './AddProductModal.vue'
import DuplicateProductModal from './DuplicateProductModal.vue'
import ViewProductModal from './ViewProductModal.vue'
import EditProductModal from './EditProductModal.vue'

// Icônes Lucide Vue Next
import { 
  Home as HomeIcon,
  Download as DownloadIcon,
  Plus as PlusIcon,
  ChevronDown as ChevronDownIcon,
  FileText as FileTextIcon,
  AlertCircle as AlertCircleIcon,
  Calendar as CalendarIcon,
  Package as PackageIcon,
  CheckCircle as CheckCircleIcon,
  Warehouse as WarehouseIcon,
  Eye as EyeIcon,
  Search as SearchIcon,
  ArrowUpDown as ArrowUpDownIcon,
  Filter as FilterIcon,
  MoreHorizontal as MoreHorizontalIcon,
  Edit as EditIcon,
  Copy as CopyIcon,
  Trash2 as Trash2Icon,
  ChevronLeft as ChevronLeftIcon,
  ChevronRight as ChevronRightIcon,
  Crown as CrownIcon,
  X as XIcon,
  Zap as ZapIcon,
  TrendingUp as TrendingUpIcon,
  Rocket as RocketIcon
} from 'lucide-vue-next'

// États réactifs
const router = useRouter()
const products = ref([])
const loading = ref(false)
const error = ref('')
const currentPage = ref(1)
const itemsPerPage = ref(100)
const selectedProducts = ref([])

const currentUser = ref(null)
const currentBoutique = ref(null)

const productBoosts = ref([])
const boutiqueBoosts = ref([])

const showAddProductModal = ref(false)
const showDuplicateProductModal = ref(false)
const DataDuplicate = ref(false)
const showViewModal = ref(false)
const showEditModal = ref(false)
const showExportDropdown = ref(false)
const showPremiumModal = ref(false)
const showBoostModal = ref(false)
const selectedProduct = ref(null)
const selectedProductForBoost = ref(null)
const selectedBoostOption = ref(null)
const activeActionMenu = ref(null)
const searchQuery = ref('')
const activeFilter = ref('all')
const selectedPeriod = ref('all')
const notification = ref({ show: false, message: '' })
const exportDropdownRef = ref(null)

// États pour la configuration du boost
const selectedDuration = ref(1) // Durée en semaines
const boostStartDate = ref('')
const boostEndDate = ref('')
const boostLoading = ref(false)

// États Premium réels - ✅ NOUVEAU: Basé sur les boutiques
const premiumSubscription = ref(null)
const premiumPlans = ref([])
const premiumLoading = ref(false)
const premiumError = ref(null)

// États pour le modal de notification Premium
const showPremiumNotificationModal = ref(false)
const premiumNotificationMessage = ref('')

// ✅ NOUVEAU: États pour le modal de notification Boost
const showBoostNotificationModal = ref(false)
const boostConfirmationData = ref(null)

const stats = ref({
  all: { total_products: 0, active_products: 0, total_views: 0, total_stock: 0 },
  today: { total_products: 0, active_products: 0, total_views: 0, total_stock: 0 },
  week: { total_products: 0, active_products: 0, total_views: 0, total_stock: 0 },
  month: { total_products: 0, active_products: 0, total_views: 0, total_stock: 0 }
})

// Mock data pour la démo
const mockProducts = [
  {
    id: "1",
    name: "T-shirt Premium Coton",
    sku: "TSH-001",
    category_name: "Vêtements",
    subcategory_name: "T-shirts",
    unit_price: 15000,
    wholesale_price: 12000,
    wholesale_min_qty: 10,
    stock: 45,
    status: "Actif",
    colors: ["#000000", "#FFFFFF", "#FF0000"],
    sizes: ["S", "M", "L", "XL"],
    sales_count: 23,
    views_count: 156,
    icon: "👕",
    created_at: "2024-01-15T10:00:00Z",
  },
  {
    id: "2",
    name: "Sneakers Sport Pro",
    sku: "SNK-002",
    category_name: "Chaussures",
    subcategory_name: "Sport",
    unit_price: 45000,
    stock: 3,
    status: "Stock faible",
    colors: ["#000000", "#FFFFFF"],
    sizes: ["38", "39", "40", "41", "42"],
    sales_count: 12,
    views_count: 89,
    icon: "👟",
    created_at: "2024-01-10T14:30:00Z",
  },
  {
    id: "3",
    name: "Sac à Main Cuir",
    sku: "SAC-003",
    category_name: "Accessoires",
    subcategory_name: "Sacs",
    unit_price: 35000,
    stock: 0,
    status: "Rupture",
    colors: ["#8B4513", "#000000"],
    sales_count: 8,
    views_count: 67,
    icon: "👜",
    created_at: "2024-01-08T09:15:00Z",
  },
  {
    id: "4",
    name: "Montre Élégante",
    sku: "MON-004",
    category_name: "Accessoires",
    subcategory_name: "Montres",
    unit_price: 85000,
    stock: 12,
    status: "Actif",
    colors: ["#C0C0C0", "#FFD700"],
    sales_count: 5,
    views_count: 134,
    icon: "⌚",
    created_at: "2024-01-05T16:45:00Z",
  },
  {
    id: "5",
    name: "Robe d'Été Florale",
    sku: "ROB-005",
    category_name: "Vêtements",
    subcategory_name: "Robes",
    unit_price: 25000,
    stock: 18,
    status: "Brouillon",
    colors: ["#FFC0CB", "#FFFF00"],
    sizes: ["S", "M", "L"],
    sales_count: 0,
    views_count: 23,
    icon: "👗",
    created_at: "2024-01-20T11:20:00Z",
  },
]

const filterTabs = [
  { value: 'all', label: 'All' },
  { value: 'active', label: 'Active' },
  { value: 'low-stock', label: 'Low Stock' },
  { value: 'out-of-stock', label: 'Out of Stock' },
  { value: 'draft', label: 'Draft' }
]

// Options de durée pour le boost
const durationOptions = computed(() => {
  const basePrice = selectedBoostOption.value === 'basic' ? 1000 : 2500
  return [
    { weeks: 1, price: basePrice },
    { weeks: 2, price: basePrice * 2 },
    { weeks: 3, price: basePrice * 3 }
  ]
})

// Computed properties
const filteredProducts = computed(() => {
  if (!products.value) return []

  let filtered = [...products.value]

  if (activeFilter.value !== 'all') {
    const statusMap = {
      'active': 'Actif',
      'low-stock': 'Stock faible', 
      'out-of-stock': 'Rupture',
      'draft': 'Brouillon'
    }
    const targetStatus = statusMap[activeFilter.value]
    if (targetStatus) {
      if (activeFilter.value === 'low-stock') {
        filtered = filtered.filter(product => product.stock > 0 && product.stock <= 5)
      } else {  
      filtered = filtered.filter(product => product.status === targetStatus)
      }
    }
  }

  if (searchQuery.value.trim()) {
    const query = searchQuery.value.toLowerCase()
    filtered = filtered.filter(product => 
      product.name.toLowerCase().includes(query) ||
      product.sku.toLowerCase().includes(query) ||
      product.description?.toLowerCase().includes(query)
    )
  }

  return filtered
})

const filterCounts = computed(() => {
  if (!products.value) return { all: 0, active: 0, 'low-stock': 0, 'out-of-stock': 0, draft: 0 }


  return {
    'all': products.value.length,
    'active': products.value.filter(product => product.status === 'Actif').length,
    'low-stock': products.value.filter(product => product.stock > 0 && product.stock <= 5).length,
    'out-of-stock': products.value.filter(product => product.status === 'Rupture').length,
    'draft': products.value.filter(product => product.status === 'Brouillon').length
  }
})

const currentStats = computed(() => {
  if (!products.value || products.value.length === 0) {
    return { total_products: 0, active_products: 0, total_views: 0, total_stock: 0 }
  }

  const totalProducts = products.value.length
  const activeProducts = products.value.filter(product => product.status === 'Actif').length
  const totalStock = products.value.reduce((sum, product) => sum + (product.stock || 0), 0)
  const totalViews = products.value.reduce((sum, product) => sum + (product.views_count || 0), 0)

  return {
    total_products: totalProducts,
    active_products: activeProducts,
    total_stock: totalStock,
    total_views: totalViews
  }
})

const detailedStats = computed(() => {
  if (!products.value || products.value.length === 0) {
    return {
      lowStock: 0,
      outOfStock: 0,
      draft: 0,
      totalSales: 0,
      averagePrice: 0
    }
  }

  const lowStock = products.value.filter(product => product.stock > 0 && product.stock <= 5).length
  const outOfStock = products.value.filter(product => product.stock === 0).length
  const draft = products.value.filter(product => product.status === 'Brouillon').length
  const totalSales = products.value.reduce((sum, product) => sum + (product.sales_count || 0), 0)
  
  const totalPrice = products.value.reduce((sum, product) => sum + (product.unit_price || 0), 0)
  const averagePrice = products.value.length > 0 ? totalPrice / products.value.length : 0

  return {
    lowStock,
    outOfStock,
    draft,
    totalSales,
    averagePrice
  }
})

const totalPages = computed(() => Math.ceil(filteredProducts.value.length / itemsPerPage.value))
const paginatedProducts = computed(() => {
  const start = (currentPage.value - 1) * itemsPerPage.value
  const end = start + itemsPerPage.value
  return filteredProducts.value.slice(start, end)
})

const hasError = computed(() => !!error.value)
const isFirstPage = computed(() => currentPage.value <= 1)
const isLastPage = computed(() => currentPage.value >= totalPages.value)

// Computed properties pour le boost
const minStartDate = computed(() => {
  const today = new Date()
  return today.toISOString().split('T')[0]
})

const weeklyPrice = computed(() => {
  return selectedBoostOption.value === 'basic' ? 1000 : 2500
})

const totalCost = computed(() => {
  return weeklyPrice.value * selectedDuration.value
})

const isConfigurationValid = computed(() => {
  return selectedBoostOption.value && 
         selectedDuration.value && 
         boostStartDate.value && 
         boostEndDate.value
})

// Computed properties pour le nombre de produits
const productCount = computed(() => {
  return products.value ? products.value.length : 0
})

// Charger l'historique des boosts d'un produit
const loadProductBoosts = async (productId) => {
  try {
    const response = await boostApi.getProductBoosts(productId, {
      boutique_id: currentBoutique.value.id
    })
    if (response.success) {
      productBoosts.value = response.data
    }
  } catch (error) {
    console.error('Erreur chargement boosts produit:', error)
  }
}

// Charger tous les boosts de la boutique
const loadBoutiqueBoosts = async () => {
  try {
    const response = await boostApi.getBoutiqueBoosts({
      boutique_id: currentBoutique.value.id
    })
    if (response.success) {
      boutiqueBoosts.value = response.data
    }
  } catch (error) {
    console.error('Erreur chargement boosts boutique:', error)
  }
}

// Annuler une demande de boost
const cancelBoost = async (boostId) => {
  if (!confirm('Are you sure you want cancel this boost request ?')) return
  
  try {
    const response = await boostApi.cancelBoostRequest(boostId, {
      boutique_id: currentBoutique.value.id
    })
    if (response.success) {
      showNotification('Boost request canceled')
      await loadProductBoosts()
    }
  } catch (error) {
    showNotification('Erreur: ' + error.message)
  }
}

// Fonctions utilitaires
const getStatusBadgeClass = (status) => {
  switch (status) {
    case 'Actif':
      return 'bg-green-100 text-green-800'
    case 'Stock faible':
      return 'bg-yellow-100 text-yellow-800'
    case 'Rupture':
      return 'bg-red-100 text-red-800'
    case 'Brouillon':
      return 'bg-gray-100 text-gray-800'
    default:
      return 'bg-gray-100 text-gray-800'
  }
}

const getStockStatus = (stock) => {
  if (stock === 0) return 'Out Of Stock'
  if (stock <= 5) return 'Low Stock'
  return 'In stock'
}

const getStockStatusClass = (stock) => {
  if (stock === 0) return 'text-red-600'
  if (stock <= 5) return 'text-yellow-600'
  return 'text-green-600'
}

const getColorName = (color) => {
  const colorMap = {
    '#000000': 'Noir', '#FFFFFF': 'Blanc', '#C0C0C0': 'Argent',
    '#FF0000': 'Rouge', '#0000FF': 'Bleu', '#008000': 'Vert',
    '#FFFF00': 'Jaune', '#FFA500': 'Orange', '#FFC0CB': 'Rose',
    '#800080': 'Violet', '#FFD700': 'Or', '#8B4513': 'Marron'
  }
  return colorMap[color] || color
}

const formatPrice = (price) => {
  return new Intl.NumberFormat('us-US', {
      style: 'currency',
      currency: 'usd',
      minimumFractionDigits: 0,
      maximumFractionDigits: 0
  })
  .format(price || 0)
  .replace(/\s/g, ' ')
}

const formatNumber = (number) => {
  return new Intl.NumberFormat('us-US').format(number || 0)
}

const formatDate = (dateString) => {
  if (!dateString) return ''
  return new Date(dateString).toLocaleDateString('us-US')
}

// Fonctions principales
const initializeUserData = () => {
  try {
    // ✅ Récupérer le token d'authentification
    const authToken = localStorage.getItem('authToken') || sessionStorage.getItem('authToken')
    
    if (!authToken) {
      error.value = 'Authentication token Missing. Please reconnect.'
      // Rediriger vers la page de login
      window.location.href = '/boutique-admin/login'
      return
    }

    // ✅ Récupérer les données utilisateur
    const userData = localStorage.getItem('user') || sessionStorage.getItem('user')
    
    if (!userData) {
      error.value = 'Data user missing. Please reconnect.'
      window.location.href = '/boutique-admin/login'
      return
    }

    // ✅ Parser les données utilisateur
    const user = JSON.parse(userData)
    
    // ✅ Valider la structure des données
    if (!user.id || !user.email) {
      error.value = 'Invalid data user. Please reconnect'
      window.location.href = '/boutique-admin/login'
      return
    }

    // ✅ Assigner les données utilisateur
    currentUser.value = {
      id: user.id,
      full_name: user.full_name,
      email: user.email,
      boutiques: user.boutiques || []
    }

    // ✅ Sélectionner la boutique active
    if (user.boutiques && user.boutiques.length > 0) {
      // Prendre la première boutique par défaut
      currentBoutique.value = user.boutiques[0]
      
      // Ou récupérer la boutique sélectionnée précédemment
      const savedBoutiqueId = localStorage.getItem('selectedBoutiqueId')
      if (savedBoutiqueId) {
        const savedBoutique = user.boutiques.find(b => b.id == savedBoutiqueId)
        if (savedBoutique) {
          currentBoutique.value = savedBoutique
        }
      }
    } else {
      error.value = 'No store associated with this account.'
      return
    }

    // ✅ Vérifier si "Se souvenir de moi" est activé
    const rememberMe = localStorage.getItem('rememberMe') === 'true'
    
    console.log('✅ Données utilisateur chargées:', {
      user: currentUser.value,
      boutique: currentBoutique.value,
      rememberMe,
      token: authToken ? 'Présent' : 'Absent'
    })

  } catch (err) {
    console.error('Erreur lors de la récupération des données utilisateur:', err)
    error.value = 'Error to load data user.'
    
    // Nettoyer le stockage en cas d'erreur
    localStorage.removeItem('authToken')
    localStorage.removeItem('user')
    sessionStorage.removeItem('authToken')
    sessionStorage.removeItem('user')
    
    // Rediriger vers la page de login
    window.location.href = '/boutique-admin/login'
  }
}

const initializeDates = () => {
  const today = new Date()
  boostStartDate.value = today.toISOString().split('T')[0]
  updateEndDate()
}

const fetchProducts = async () => {
  if (!currentBoutique.value?.id || !currentUser.value?.id) {
    error.value = 'User informations missing'
    return
  }

  try {
    loading.value = true
    error.value = ''
    
    const response = await productsApi.getProducts({
      boutique_id: currentBoutique.value.id,
      user_id: currentUser.value.id,
      page: currentPage.value,
      limit: itemsPerPage.value,
      search: searchQuery.value.trim() || undefined
    })
    
    if (response.success) {
      products.value = response.data || []
      console.log("Produits chargés:", products.value)
    } else {
      error.value = response.error || 'Error to load products'
    }
  } catch (err) {
    console.error('Erreur fetchProducts:', err)
    error.value = 'Error server connexion'
  } finally {
    loading.value = false
  }
}

const loadStats = async (period = 'all') => {
  if (!currentBoutique.value?.id) return

  try {
    const response = await productsApi.getStats({
      boutique_id: currentBoutique.value.id,
      period
    })
    
    if (response.success) {
      stats.value[period] = response.data
    }
  } catch (err) {
    console.error('Erreur chargement stats:', err)
  }
}

const updateStats = async () => {
  await loadStats(selectedPeriod.value)
}

// ✅ NOUVEAU: Fonction pour charger les données Premium de la boutique
const loadPremiumData = async () => {
  if (!currentBoutique.value?.id) {
    console.warn(' no store selected to load the premium data')
    return
  }

  try {
    premiumLoading.value = true
    premiumError.value = null

    // ✅ NOUVEAU: Charger l'abonnement de la boutique (pas de l'utilisateur)
    const subscriptionResponse = await premiumApi.getBoutiqueSubscription({
      boutique_id: currentBoutique.value.id
    })
    
    if (subscriptionResponse.success) {
      premiumSubscription.value = subscriptionResponse.data
      console.log('✅ Abonnement boutique chargé:', premiumSubscription.value)
    } else {
      console.log('ℹ️ Aucun abonnement Premium pour cette boutique')
      premiumSubscription.value = null
    }

    // Charger les plans disponibles
    const plansResponse = await premiumApi.getPlans()
    if (plansResponse.success) {
      premiumPlans.value = plansResponse.data
    }
  } catch (error) {
    premiumError.value = error.message
    console.error('Erreur lors du chargement des données Premium:', error)
  } finally {
    premiumLoading.value = false
  }
}

// ✅ NOUVEAU: Fonctions Premium basées sur les boutiques
const hasActiveSubscription = () => {
  return premiumSubscription.value && 
         premiumSubscription.value.status === 'active' && 
         new Date(premiumSubscription.value.expires_at) > new Date()
}

const getCurrentPlan = () => {
  return premiumSubscription.value
}

const canAddMoreProducts = () => {
  if (!hasActiveSubscription()) {
    return productCount.value < 5 // Limite gratuite
  }
  
  const plan = getCurrentPlan()
  if (!plan || plan.max_products === -1) return true // Illimité
  
  return productCount.value < plan.max_products
}

const getDaysRemaining = () => {
  if (!hasActiveSubscription()) return 0
  
  const plan = getCurrentPlan()
  if (!plan || !plan.expires_at) return 0
  
  const expiryDate = new Date(plan.expires_at)
  const today = new Date()
  const diffTime = expiryDate - today
  const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24))
  
  return Math.max(0, diffDays)
}

const isExpiringSoon = () => {
  return getDaysRemaining() <= 7 && getDaysRemaining() > 0
}

const openPremiumModal = () => {
  showPremiumModal.value = true
}

const closePremiumModal = () => {
  showPremiumModal.value = false
  premiumError.value = null
}

// ✅ NOUVEAU: Souscrire la boutique à un plan Premium
const subscribeToPlan = async (planType) => {
  if (!currentBoutique.value?.id) {
    showNotification('Error : No Store selected ')
    return
  }

  try {
    premiumLoading.value = true
    premiumError.value = null

    // Trouver le plan correspondant
    const planMap = {
      'starter': 1,
      'business': 2, 
      'enterprise': 3
    }
    
    const planId = planMap[planType]
    if (!planId) {
      throw new Error('Plan non trouvé')
    }

    const response = await premiumApi.subscribeBoutiqueToPremium({
      plan_id: planId,
      payment_method: 'card'
    }, {
      boutique_id: currentBoutique.value.id
    })

    if (response.status === 'OK') {
      // Fermer le modal Premium
      showPremiumModal.value = false
      
      // Afficher le modal de notification
      premiumNotificationMessage.value = 'Your boost request has been received. A Daq Auto agent will contact you within 24 hours to finalize the activation.'
      showPremiumNotificationModal.value = true
      
      await loadPremiumData()
    } else {
      throw new Error(response.error || 'Error to subscribe')
    }
  } catch (error) {
    premiumError.value = error.message
    showNotification('Erreur: ' + error.message)
  } finally {
    premiumLoading.value = false
  }
}

const closePremiumNotificationModal = () => {
  showPremiumNotificationModal.value = false
  premiumNotificationMessage.value = ''
}

// ✅ NOUVEAU: Fermer le modal de notification Boost
const closeBoostNotificationModal = () => {
  showBoostNotificationModal.value = false
  boostConfirmationData.value = null
}

// ✅ NOUVEAU: Renouveler l'abonnement de la boutique
const renewSubscription = async () => {
  if (!currentBoutique.value?.id) {
    showNotification('Error: No store selected')
    return
  }

  try {
    premiumLoading.value = true
    premiumError.value = null

    const response = await premiumApi.renewBoutiqueSubscription({
      payment_method: 'card'
    }, {
      boutique_id: currentBoutique.value.id
    })

    if (response.success) {
      showNotification('the subscription renew with success!')
      await loadPremiumData()
    } else {
      throw new Error(response.error || 'Error to renew')
    }
  } catch (error) {
    premiumError.value = error.message
    showNotification('Erreur: ' + error.message)
  } finally {
    premiumLoading.value = false
  }
}

// ✅ NOUVEAU: Annuler l'abonnement de la boutique
const cancelSubscription = async () => {
  if (!confirm('Are you sure you want cancel the premium subscription for this store?')) {
    return
  }

  if (!currentBoutique.value?.id) {
    showNotification('Error : No store selected')
    return
  }

  try {
    premiumLoading.value = true
    premiumError.value = null

    const response = await premiumApi.cancelBoutiqueSubscription({
      reason: 'Demande utilisateur'
    }, {
      boutique_id: currentBoutique.value.id
    })

    if (response.success) {
      showNotification('The premium subscription canceled for this store')
      await loadPremiumData()
      closePremiumModal()
    } else {
      throw new Error(response.error || 'Error to cancel')
    }
  } catch (error) {
    premiumError.value = error.message
    showNotification('Erreur: ' + error.message)
  } finally {
    premiumLoading.value = false
  }
}

const toggleActionMenu = (productId) => {
  activeActionMenu.value = activeActionMenu.value === productId ? null : productId
}

const viewProduct = (product) => {
  selectedProduct.value = product
  showViewModal.value = true
  activeActionMenu.value = null
}

const editProduct = (product) => {
  selectedProduct.value = {
    ...product,
    // Ajouter les données manquantes :
    category_id: product.category_id,
    subcategory_id: product.subcategory_id, 
    subsubcategory_id: product.subsubcategory_id,
    subsubsubcategory_id: product.subsubsubcategory_id, // 4ème niveau
    colors: product.colors || [],
    sizes: product.sizes || []
  }
  showEditModal.value = true
  activeActionMenu.value = null
}

// Fonctions pour le boost
const openBoostModal = (product) => {
  selectedProductForBoost.value = product
  selectedBoostOption.value = null
  selectedDuration.value = 1
  initializeDates()
  showBoostModal.value = true
  activeActionMenu.value = null
}

const closeBoostModal = () => {
  showBoostModal.value = false
  selectedProductForBoost.value = null
  selectedBoostOption.value = null
  selectedDuration.value = 1
}

const selectBoostOption = (option) => {
  selectedBoostOption.value = option
}

const resetBoostSelection = () => {
  selectedBoostOption.value = null
}

const selectDuration = (weeks) => {
  selectedDuration.value = weeks
  updateEndDate()
}

const updateEndDate = () => {
  if (boostStartDate.value && selectedDuration.value) {
    const startDate = new Date(boostStartDate.value)
    const endDate = new Date(startDate)
    endDate.setDate(startDate.getDate() + (selectedDuration.value * 7))
    boostEndDate.value = endDate.toISOString().split('T')[0]
  }
}

// ✅ NOUVEAU: Fonction mise à jour pour appeler l'API boost
const proceedToPayment = async () => {
  if (!currentBoutique.value?.id || !currentUser.value?.id) {
    showNotification('Error: Data user missing')
    return
  }

  try {
    boostLoading.value = true

    const boostData = {
      boost_type: selectedBoostOption.value,
      duration_days: selectedDuration.value * 7,
      payment_method: 'card'
    }

    // ✅ UTILISER LA NOUVELLE API
    const result = await boostApi.createBoostRequest(
      selectedProductForBoost.value.id, 
      boostData, 
      { boutique_id: currentBoutique.value.id }
    )

    if (result.success && result.status === 'pending') {
      closeBoostModal()
      
      boostConfirmationData.value = {
        product_name: result.data.product_name,
        boost_type: result.data.boost_type,
        duration_days: result.data.duration_days,
        amount: result.data.amount
      }
      
      showBoostNotificationModal.value = true
    } else {
      throw new Error(result.error || 'Erreur lors de la demande de boost')
    }
  } catch (error) {
    showNotification('Erreur: ' + error.message)
  } finally {
    boostLoading.value = false
  }
}

const duplicateProduct = async (product) => {
  DataDuplicate.value = product
  console.log('Duplicate data:', DataDuplicate.value)
  showDuplicateProductModal.value = true
}

const deleteProductAction = async (productId) => {
  if (confirm('Are you sure you want delete this product ?')) {
    try {
      const response = await productsApi.deleteProduct(productId, {
        boutique_id: currentBoutique.value.id,
        user_id: currentUser.value.id
      })
      
      if (response.success) {
        await fetchProducts()
        await loadStats(selectedPeriod.value)
        showNotification('Product dleleted with success!')
      } else {
        showNotification('Error to delete: ' + response.error)
      }
    } catch (err) {
      showNotification('Error to delete: ' + err.message)
    }
  }
  activeActionMenu.value = null
}

const hasActiveBoost = (productId) => {
  return products.value.some(boost =>
    boost.id === productId &&
    ['active', 'pending'].includes(boost.boost_status) &&
    new Date(boost.boost_expires_at) > new Date()
  )
}

const getBoostStatus = (productId) => {
  const product = products.value.find(
    (p) =>
      p.id === productId &&
      ['active', 'pending'].includes(p.boost_status) &&
      new Date(p.boost_expires_at) > new Date()
  )
  return product ? product.boost_status : null
}

const getBoostExpiration = (productId) => {
  const product = products.value.find(
    (p) =>
      p.id === productId &&
      ['active', 'pending'].includes(p.boost_status) &&
      new Date(p.boost_expires_at) > new Date()
  )

  if (!product) return null

  const date = new Date(product.boost_expires_at)

  // Formater la date : ex. "mardi 25"
  let formatted = date.toLocaleDateString('us-US', {
    weekday: 'long',
    day: 'numeric'
  })

  // Mettre la première lettre en majuscule
  return formatted.charAt(0).toUpperCase() + formatted.slice(1)
}

const handleFilterChange = async (filterValue) => {
  activeFilter.value = filterValue
  currentPage.value = 1
  await fetchProducts()
}

const handlePageChange = async (page) => {
  if (page >= 1 && page <= totalPages.value) {
    currentPage.value = page
    await fetchProducts()
  }
}

const handleItemsPerPageChange = async () => {
  currentPage.value = 1
  await fetchProducts()
}

const handleSelectAll = (checked) => {
  if (checked) {
    selectedProducts.value = paginatedProducts.value.map(p => p.id)
  } else {
    selectedProducts.value = []
  }
}

const handleSelectProduct = (productId, checked) => {
  if (checked) {
    selectedProducts.value.push(productId)
  } else {
    selectedProducts.value = selectedProducts.value.filter(id => id !== productId)
  }
}

// Fonctions d'export
async function exportToPDF() {
  try {
    showExportDropdown.value = false
    showNotification('Exporting to PDF...')
    
    const { jsPDF } = await import('jspdf')
    const { default: autoTable } = await import('jspdf-autotable')
    
    const doc = new jsPDF()
    
    doc.setFontSize(18)
    doc.setTextColor(246, 90, 17)
    doc.text('Liste des Produits', 14, 32)
    
    doc.setFontSize(11)
    doc.setTextColor(0, 0, 0)
    doc.text(`Date d'exportation: ${new Date().toLocaleDateString('fr-FR')}`, 14, 40)
    doc.text(`Boutique: ${currentBoutique.value?.name || 'N/A'}`, 14, 46)
    doc.text(`Filtre: ${filterTabs.find(tab => tab.value === activeFilter.value)?.label || 'Tous'}`, 14, 52)
    doc.text(`Nombre de produits: ${filteredProducts.value.length}`, 14, 58)
    
    const tableData = filteredProducts.value.map(product => [
      product.name,
      product.category_name || 'Non catégorisé',
      `${formatPrice(product.unit_price)}`,
      product.stock,
      product.status,
      (product.colors || []).length > 0 ? `${product.colors.length} couleur(s)` : 'Aucune',
      (product.sizes || []).length > 0 ? product.sizes.join(', ') : 'Unique'
    ])
    
    autoTable(doc, {
      head: [['Produit', 'Catégorie', 'Prix', 'Stock', 'Statut', 'Couleurs', 'Tailles']],
      body: tableData,
      startY: 65,
      styles: { 
        fontSize: 9, 
        cellPadding: 3,
        lineColor: [200, 200, 200],
        lineWidth: 0.1
      },
      headStyles: { 
        fillColor: [246, 90, 17],
        textColor: [255, 255, 255],
        fontStyle: 'bold'
      },
      alternateRowStyles: {
        fillColor: [248, 249, 250]
      },
      margin: { top: 65 }
    })
    
    const fileName = `produits-${currentBoutique.value?.name || 'boutique'}-${new Date().toISOString().split('T')[0]}.pdf`
    doc.save(fileName)
    showNotification('Export to PDF success!')
  } catch (error) {
    console.error('Erreur lors de l\'export PDF:', error)
    showNotification('Error to export to PDF. Veuillez réessayer.')
  }
}

async function exportToExcel() {
  try {
    showExportDropdown.value = false
    showNotification('Exporting to Excel')
    
    const XLSX = await import('xlsx')
    
    const excelData = filteredProducts.value.map(product => ({
      'Nom du produit': product.name,
      'SKU': product.sku,
      'Catégorie': product.category_name || 'Non catégorisé',
      'Sous-catégorie': product.subcategory_name || 'Non sous-catégorisé',
      'Prix unitaire': product.unit_price,
      'Prix de gros': product.wholesale_price || '',
      'Quantité min. gros': product.wholesale_min_qty || '',
      'Stock': product.stock,
      'Statut': product.status,
      'Couleurs': (product.colors || []).length > 0 ? product.colors.join(', ') : 'Aucune',
      'Tailles': (product.sizes || []).length > 0 ? product.sizes.join(', ') : 'Unique',
      'Ventes': product.sales_count || 0,
      'Vues': product.views_count || 0,
      'Date de création': formatDate(product.created_at)
    }))
    
    const worksheet = XLSX.utils.json_to_sheet(excelData)
    const workbook = XLSX.utils.book_new()
    XLSX.utils.book_append_sheet(workbook, worksheet, 'Produits')
    
    XLSX.writeFile(workbook, `produits-${new Date().toISOString().split('T')[0]}.xlsx`)
    showNotification('Export to Excel success!')
  } catch (error) {
    console.error('Erreur lors de l\'export Excel:', error)
    showNotification('Error to export to Excel. Veuillez réessayer.')
  }
}

// const handleLogout = async () => {
//   try {
//     await ElMessageBox.confirm(
//       'Are you sure you logout ?',
//       'Confirmation',
//       {
//         confirmButtonText: 'Yes',
//         cancelButtonText: 'Cancel',
//         type: 'warning',
//         confirmButtonColor: "#fe9700",

//       }
//     )

//     localStorage.removeItem('authToken')
//     sessionStorage.removeItem('authToken')
//     localStorage.removeItem('user')
//     sessionStorage.removeItem('user')
//     localStorage.removeItem('rememberMe')

//     ElMessage({
//       type: 'success',
//       message: 'Logout success'
//     })

//     window.location.href = '/boutique-admin/login'
//   } catch (error) {
//     // Annulé
//   }
// }

const handleAddProduct = async (productData) => {
  try {


    const response = await productsApi.createProduct(productData, {
      boutique_id: currentBoutique.value.id,
      user_id: currentUser.value.id
    })
    
    if (response.success) {
      console.log('Produit créé avec succès:', response.data);
      showAddProductModal.value = false
      await fetchProducts()
      await loadStats(selectedPeriod.value)
      showNotification('Product added with success!')
    } else {
      showNotification('Error to add: ' + response.error)
    }
  } catch (err) {
    showNotification('Error to add: ' + err.message)
  }
}

const handleEditProduct = async (productData) => {
  try {
    const response = await productsApi.updateProduct(productData.id, productData, {
      boutique_id: currentBoutique.value.id,
      user_id: currentUser.value.id
    })
    
    if (response.success) {
      showEditModal.value = false
      await fetchProducts()
      await loadStats(selectedPeriod.value)
      showNotification('Product edited with success!')
    } else {
      showNotification('Error to edit: ' + response.error)
    }
  } catch (err) {
    showNotification('Error to edit : ' + err.message)
  }
}

const showNotification = (message) => {
  notification.value.message = message
  notification.value.show = true
  setTimeout(() => {
    notification.value.show = false
  }, 3000)
}

const refresh = async () => {
  await fetchProducts()
  await loadStats(selectedPeriod.value)
}

const handleClickOutside = (event) => {
  if (!event.target.closest('.relative')) {
    showExportDropdown.value = false
    activeActionMenu.value = null
  }
  if (exportDropdownRef.value && !exportDropdownRef.value.contains(event.target)) {
    showExportDropdown.value = false;
  }
}

// Watchers
watch(searchQuery, () => {
  clearTimeout(searchQuery.timeoutId)
  searchQuery.timeoutId = setTimeout(() => {
    currentPage.value = 1
    fetchProducts()
  }, 500)
})

watch(selectedPeriod, updateStats)

watch(boostStartDate, updateEndDate)

// Lifecycle hooks
onMounted(async () => {
  
    const token = localStorage.getItem('authToken') || sessionStorage.getItem('authToken')
    console.log(token)
    if (!token) {
      router.replace('/boutique-admin/login')
      return
    }

  document.addEventListener('click', handleClickOutside)

  // Initialiser avec les données mock pour la démo
  // products.value = mockProducts
  initializeUserData()
  initializeDates()

  await loadBoutiqueBoosts()

  // ✅ NOUVEAU: Charger les données Premium après l'initialisation
  await loadPremiumData()

  // Si on a des vraies données utilisateur, charger les vrais produits
  if (currentUser.value && currentBoutique.value) {
    await Promise.all([
      fetchProducts(), // ✅ Décommenter cette ligne
      loadStats('all'),
      loadStats('today'),
      loadStats('week'), 
      loadStats('month')
    ])
  }
})

onUnmounted(() => {
  document.removeEventListener('click', handleClickOutside)
})
</script>

<style scoped>
/* Transitions fluides */
.transition-all {
  transition-property: all;
  transition-timing-function: cubic-bezier(0.4, 0, 0.2, 1);
  transition-duration: 300ms;
}


.transition-transform {
  transition-property: transform;
  transition-timing-function: cubic-bezier(0.4, 0, 0.2, 1);
  transition-duration: 150ms;
}

.hover\:scale-110:hover {
  transform: scale(1.1);
}

/* Animations */
@keyframes spin {
  to {
    transform: rotate(360deg);
  }
}

.animate-spin {
  animation: spin 1s linear infinite;
}

/* Animations luxueuses avec couleurs plus intenses */
@keyframes float-slow {
  0%, 100% {
    transform: translateY(0px) translateX(0px) rotate(0deg);
  }
  25% {
    transform: translateY(-30px) translateX(15px) rotate(2deg);
  }
  50% {
    transform: translateY(-15px) translateX(-20px) rotate(-1deg);
  }
  75% {
    transform: translateY(-40px) translateX(8px) rotate(1deg);
  }
}

@keyframes float-reverse {
  0%, 100% {
    transform: translateY(0px) translateX(0px) rotate(0deg);
  }
  25% {
    transform: translateY(20px) translateX(-15px) rotate(-2deg);
  }
  50% {
    transform: translateY(35px) translateX(25px) rotate(1deg);
  }
  75% {
    transform: translateY(8px) translateX(-8px) rotate(-1deg);
  }
}

@keyframes float-diagonal {
  0%, 100% {
    transform: translateY(0px) translateX(0px) scale(1) rotate(0deg);
  }
  33% {
    transform: translateY(-25px) translateX(20px) scale(1.1) rotate(1deg);
  }
  66% {
    transform: translateY(15px) translateX(-15px) scale(0.9) rotate(-1deg);
  }
}

@keyframes float-slow-reverse {
  0%, 100% {
    transform: translateY(0px) translateX(0px) rotate(0deg);
  }
  25% {
    transform: translateY(-15px) translateX(-20px) rotate(-1deg);
  }
  50% {
    transform: translateY(25px) translateX(10px) rotate(2deg);
  }
  75% {
    transform: translateY(-10px) translateX(-5px) rotate(-0.5deg);
  }
}

@keyframes pulse-slow {
  0%, 100% {
    opacity: 0.4;
    transform: scale(1);
  }
  50% {
    opacity: 0.8;
    transform: scale(1.4);
  }
}

@keyframes pulse-delayed {
  0%, 100% {
    opacity: 0.3;
    transform: scale(1);
  }
  50% {
    opacity: 0.7;
    transform: scale(1.5);
  }
}

@keyframes pulse-delayed-2 {
  0%, 100% {
    opacity: 0.25;
    transform: scale(1);
  }
  50% {
    opacity: 0.6;
    transform: scale(1.3);
  }
}

@keyframes slide-down {
  0% {
    transform: translateY(-100%);
    opacity: 0;
  }
  50% {
    opacity: 1;
  }
  100% {
    transform: translateY(300%);
    opacity: 0;
  }
}

@keyframes slide-right {
  0% {
    transform: translateX(-100%);
    opacity: 0;
  }
  50% {
    opacity: 1;
  }
  100% {
    transform: translateX(300%);
    opacity: 0;
  }
}

@keyframes slide-up {
  0% {
    transform: translateY(100%);
    opacity: 0;
  }
  50% {
    opacity: 1;
  }
  100% {
    transform: translateY(-200%);
    opacity: 0;
  }
}

@keyframes rotate-slow {
  0% {
    transform: rotate(0deg);
  }
  100% {
    transform: rotate(360deg);
  }
}

@keyframes float-small {
  0%, 100% {
    transform: translateY(0px);
  }
  50% {
    transform: translateY(-10px);
  }
}

.animate-float-slow {
  animation: float-slow 20s ease-in-out infinite;
}

.animate-float-reverse {
  animation: float-reverse 25s ease-in-out infinite;
}

.animate-float-diagonal {
  animation: float-diagonal 18s ease-in-out infinite;
}

.animate-float-slow-reverse {
  animation: float-slow-reverse 22s ease-in-out infinite;
}

.animate-pulse-slow {
  animation: pulse-slow 4s ease-in-out infinite;
}

.animate-pulse-delayed {
  animation: pulse-delayed 5s ease-in-out infinite 1s;
}

.animate-pulse-delayed-2 {
  animation: pulse-delayed-2 6s ease-in-out infinite 2s;
}

.animate-slide-down {
  animation: slide-down 8s linear infinite;
}

.animate-slide-right {
  animation: slide-right 10s linear infinite;
}

.animate-slide-up {
  animation: slide-up 9s linear infinite;
}

.animate-rotate-slow {
  animation: rotate-slow 30s linear infinite;
}

.animate-float-small {
  animation: float-small 3s ease-in-out infinite;
}

/* Responsive improvements */
@media (max-width: 640px) {
  .text-2xl {
    font-size: 1.5rem;
    line-height: 2rem;
  }
  
  .text-3xl {
    font-size: 1.875rem;
    line-height: 2.25rem;
  }
  
  .px-6 {
    padding-left: 1rem;
    padding-right: 1rem;
  }
  
  .py-8 {
    padding-top: 1.5rem;
    padding-bottom: 1.5rem;
  }
}

/* Scrollbar styling */
::-webkit-scrollbar {
  width: 6px;
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

/* Focus states */
.focus\:ring-2:focus {
  --tw-ring-offset-shadow: var(--tw-ring-inset) 0 0 0 var(--tw-ring-offset-width) var(--tw-ring-offset-color);
  --tw-ring-shadow: var(--tw-ring-inset) 0 0 0 calc(2px + var(--tw-ring-offset-width)) var(--tw-ring-color);
  box-shadow: var(--tw-ring-offset-shadow), var(--tw-ring-shadow), var(--tw-shadow, 0 0 #0000);
}

.focus\:ring-offset-2:focus {
  --tw-ring-offset-width: 2px;
}

/* Backdrop blur support */
.backdrop-blur-sm {
  backdrop-filter: blur(4px);
}

@supports not (backdrop-filter: blur(4px)) {
  .backdrop-blur-sm {
    background-color: rgba(0, 0, 0, 0.8);
  }
}

/* Print styles */
@media print {
  .no-print {
    display: none !important;
  }
}
</style>
