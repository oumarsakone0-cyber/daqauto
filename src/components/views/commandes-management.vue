<template>
  <div class="min-h-screen bg-gray-50 relative overflow-hidden">
    <!-- ... existing code ... -->
    
    <!-- Background animations -->
    <div class="absolute inset-0 overflow-hidden pointer-events-none z-0">
      <div class="absolute inset-0 bg-gradient-to-br from-gray-50 via-white to-gray-100"></div>
      <div class="absolute -top-20 -left-20 w-96 h-96 bg-gradient-to-br from-orange-200/60 to-orange-300/40 rounded-full blur-3xl animate-float-slow"></div>
      <div class="absolute top-1/4 -right-20 w-80 h-80 bg-gradient-to-br from-blue-200/50 to-indigo-200/35 rounded-full blur-3xl animate-float-reverse"></div>
      <div class="absolute -bottom-20 left-1/3 w-72 h-72 bg-gradient-to-br from-purple-200/45 to-pink-200/35 rounded-full blur-3xl animate-float-diagonal"></div>
      <div class="absolute top-1/2 right-1/4 w-64 h-64 bg-gradient-to-br from-emerald-200/40 to-teal-200/30 rounded-full blur-3xl animate-float-slow-reverse"></div>
      <div class="absolute top-1/3 left-1/4 w-3 h-3 bg-orange-400/70 rounded-full animate-pulse-slow shadow-lg"></div>
      <div class="absolute top-2/3 right-1/4 w-2.5 h-2.5 bg-blue-400/60 rounded-full animate-pulse-delayed shadow-lg"></div>
      <div class="absolute top-1/2 left-2/3 w-2 h-2 bg-purple-400/50 rounded-full animate-pulse-slow shadow-lg"></div>
      <div class="absolute top-1/4 right-1/3 w-2 h-2 bg-emerald-400/50 rounded-full animate-pulse-delayed-2 shadow-lg"></div>
      <div class="absolute top-0 left-1/4 w-px h-40 bg-gradient-to-b from-transparent via-orange-300/40 to-transparent animate-slide-down"></div>
      <div class="absolute top-1/3 right-1/3 w-32 h-px bg-gradient-to-r from-transparent via-blue-300/35 to-transparent animate-slide-right"></div>
      <div class="absolute bottom-1/4 left-1/2 w-px h-32 bg-gradient-to-b from-transparent via-purple-300/30 to-transparent animate-slide-up"></div>
      <div class="absolute top-3/4 left-1/6 w-16 h-16 bg-gradient-to-br from-orange-300/30 to-yellow-300/20 rotate-45 animate-rotate-slow"></div>
      <div class="absolute top-1/6 right-1/6 w-12 h-12 bg-gradient-to-br from-blue-300/25 to-cyan-300/20 rounded-lg animate-float-small"></div>
    </div>

    <!-- Container responsive -->
    <div class="w-full max-w-[1650px] mx-auto px-4 sm:px-6 py-4 sm:py-8 relative z-10">
      <Navbar/>
      
      <!-- Breadcrumb -->
      <div class="flex items-center text-sm text-gray-500 mb-4 sm:mb-6">
        <router-link to="/" class="hover:text-gray-700">
          <HomeIcon class="w-4 h-4 sm:w-5 sm:h-5" />
        </router-link>
        <span class="mx-2">/</span>
        <span class="font-medium text-gray-700 truncate">Order Management</span>
      </div>

      <!-- Header -->
      <div class="flex flex-col space-y-4 lg:flex-row lg:items-center lg:justify-between lg:space-y-0 mb-6 sm:mb-8">
        <div>
          <h1 class="text-2xl sm:text-3xl font-bold text-gray-900 mb-2">Order Management</h1>
          <p class="text-sm sm:text-base text-gray-600">Real-time order tracking and processing</p>
        </div>
        
        <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-2 sm:gap-3">
          <div class="relative" ref="exportDropdownRef">
            <button 
              @click="showExportDropdown = !showExportDropdown"
              class="w-full sm:w-auto inline-flex items-center justify-center px-3 sm:px-4 py-2 rounded-lg shadow-sm text-sm font-medium btn-degrade-orange"
            >
              <DownloadIcon class="w-4 h-4" />
              <span>Export</span>
              <ChevronDownIcon class="w-4 h-4" />
            </button>
            <div v-if="showExportDropdown" class="origin-top-right absolute right-0 w-50 mt-2 ring-1 ring-gray-400 rounded-md shadow-lg bg-white p-2">
              <div role="menu">
                <button @click="exportToPDF" class="flex items-center text-sm mb-2 text-gray-700 hover:bg-gray-100 btn-gray" role="menuitem">
                  <FileTextIcon class="w-4 h-4  error-color" />
                  Export to PDF
                </button>
                <button @click="exportToExcel" class="flex items-center text-sm text-gray-700 hover:bg-gray-100 btn-gray" role="menuitem">
                  <FileTextIcon class="w-4 h-4 green-color" />
                  Export to Excel
                </button>
              </div>
            </div>
          </div>

          <button 
            @click="loadAllData"
            class="w-full sm:w-auto inline-flex items-center justify-center px-3 sm:px-4 py-2 rounded-lg shadow-sm text-sm font-medium transition-all submit-btn"
          >
            <RefreshIcon class="w-4 h-4" />
            Refresh
          </button>
        </div>
      </div>

      <!-- Loading State -->
      <div v-if="dataLoading" class="mb-6 sm:mb-8">
        <div class="bg-white rounded-lg p-6 sm:p-8 text-center shadow-lg">
          <div class="inline-block animate-spin rounded-full h-10 w-10 sm:h-12 sm:w-12 border-4 primary-color border-t-transparent"></div>
          <p class="mt-4 text-gray-600 font-medium text-sm sm:text-base">Loading orders...</p>
        </div>
      </div>

      <!-- Error State -->
      <div v-if="hasError" class="mb-6 sm:mb-8">
        <div class="bg-gradient-to-r from-red-50 to-red-100 border border-red-200 rounded-lg p-4 sm:p-6 shadow-lg">
          <div class="flex flex-col sm:flex-row sm:items-center gap-4">
            <div class="w-10 h-10 bg-red-100 rounded-full flex items-center justify-center flex-shrink-0">
              <WarningIcon class="w-6 h-6 error-color" />
            </div>
            <div class="flex-1 min-w-0">
              <p class="error-color font-medium text-sm sm:text-base">{{ error }}</p>
            </div>
            <button @click="loadAllData" class="w-full sm:w-auto px-4 py-2 error-background-color rounded-lg transition-colors font-medium text-sm">
              Try Again
            </button>
          </div>
        </div>
      </div>

      <!-- Stats Cards -->
      <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-6 mb-6 sm:mb-8" v-if="!dataLoading">
        <div class="bg-white overflow-hidden shadow-lg rounded-lg border border-gray-100 hover:shadow-xl transition-all duration-300">
          <div class="px-4 sm:px-6 py-4 sm:py-6">
            <div class="flex items-center mb-3 sm:mb-4">
              <div class="w-10 h-10 sm:w-12 sm:h-12 bg-gradient-to-br from-orange-200 to-orange-300 rounded-lg flex items-center justify-center mr-3 sm:mr-4 flex-shrink-0">
                <ShoppingCartIcon class="w-5 h-5 text-black" />
              </div>
              <div class="min-w-0">
                <p class="text-xs sm:text-sm text-gray-600">Total Orders</p>
                <p class="text-xl sm:text-2xl font-bold text-gray-900">{{ stats.total_orders || 0 }}</p>
              </div>
            </div>
            <div class="space-y-2">
              <div class="flex justify-between text-xs text-gray-500">
                <span class="truncate">Growth: +{{ stats.orders_growth || 0 }}%</span>
                <span class="primary-color flex-shrink-0">This month</span>
              </div>
              <div class="w-full bg-gray-200 rounded-full h-2">
                <div class="bg-gradient-to-r from-orange-500 to-orange-600 h-2 rounded-full transition-all duration-300" style="width: 75%"></div>
              </div>
            </div>
          </div>
        </div>

        <div class="bg-white overflow-hidden shadow-lg rounded-lg border border-gray-100 hover:shadow-xl transition-all duration-300">
          <div class="px-4 sm:px-6 py-4 sm:py-6">
            <div class="flex items-center mb-3 sm:mb-4">
              <div class="w-10 h-10 sm:w-12 sm:h-12 bg-gradient-to-br from-yellow-200 to-yellow-300 rounded-lg flex items-center justify-center mr-3 sm:mr-4 flex-shrink-0">
                <Clock class="w-5 h-5 text-black" />
              </div>
              <div class="min-w-0">
                <p class="text-xs sm:text-sm text-gray-600">Pending</p>
                <p class="text-xl sm:text-2xl font-bold text-gray-900">{{ stats.pending_orders || 0 }}</p>
              </div>
            </div>
            <div class="space-y-2">
              <div class="flex justify-between text-xs text-gray-500">
                <span class="truncate">Reduction: -{{ stats.pending_reduction || 0 }}%</span>
                <span class="text-yellow-600 flex-shrink-0">vs yesterday</span>
              </div>
              <div class="w-full bg-gray-200 rounded-full h-2">
                <div class="bg-gradient-to-r from-yellow-500 to-yellow-600 h-2 rounded-full transition-all duration-300" style="width: 60%"></div>
              </div>
            </div>
          </div>
        </div>

        <div class="bg-white overflow-hidden shadow-lg rounded-lg border border-gray-100 hover:shadow-xl transition-all duration-300">
          <div class="px-4 sm:px-6 py-4 sm:py-6">
            <div class="flex items-center mb-3 sm:mb-4">
              <div class="w-10 h-10 sm:w-12 sm:h-12 bg-gradient-to-br from-green-200 to-green-300 rounded-lg flex items-center justify-center mr-3 sm:mr-4 flex-shrink-0">
                <CheckCircle2Icon class="w-5 h-5 text-black" />
              </div>
              <div class="min-w-0">
                <p class="text-xs sm:text-sm text-gray-600">Confirmed</p>
                <p class="text-xl sm:text-2xl font-bold text-gray-900">{{ stats.confirmed_orders || 0 }}</p>
              </div>
            </div>
            <div class="space-y-2">
              <div class="flex justify-between text-xs text-gray-500">
                <span class="truncate">Growth: +{{ stats.confirmed_growth || 0 }}%</span>
                <span class="green-color flex-shrink-0">Today</span>
              </div>
              <div class="w-full bg-gray-200 rounded-full h-2">
                <div class="bg-gradient-to-r from-green-500 to-green-600 h-2 rounded-full transition-all duration-300" style="width: 85%"></div>
              </div>
            </div>
          </div>
        </div>

        <div class="bg-white overflow-hidden shadow-lg rounded-lg border border-gray-100 hover:shadow-xl transition-all duration-300">
          <div class="px-4 sm:px-6 py-4 sm:py-6">
            <div class="flex items-center mb-3 sm:mb-4">
              <div class="w-10 h-10 sm:w-12 sm:h-12 bg-gradient-to-br from-purple-200 to-purple-300 rounded-lg flex items-center justify-center mr-3 sm:mr-4 flex-shrink-0">
                <BadgeEuroIcon class="w-5 h-5 text-black" />
              </div>
              <div class="min-w-0">
                <p class="text-xs sm:text-sm text-gray-600">Daily Income</p>
                <p class="text-xl sm:text-2xl font-bold text-gray-900">{{ formatCurrency(stats.daily_revenue || 0) }}</p>
              </div>
            </div>
            <div class="space-y-2">
              <div class="flex justify-between text-xs text-gray-500">
                <span class="truncate">Growth: +{{ stats.revenue_growth || 0 }}%</span>
                <span class="purple-color flex-shrink-0">vs yesterday</span>
              </div>
              <div class="w-full bg-gray-200 rounded-full h-2">
                <div class="bg-gradient-to-r from-purple-500 to-purple-600 h-2 rounded-full transition-all duration-300" style="width: 70%"></div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Main Content Card -->
      <div class="bg-white shadow-lg rounded-lg overflow-hidden border border-gray-100" v-if="!dataLoading">
        <!-- Filter Tabs -->
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
            >
              <span class="hidden sm:inline">{{ tab.label }} ({{ filterCounts[tab.value] }})</span>
              <span class="sm:hidden">{{ tab.label.split(' ')[0] }} ({{ filterCounts[tab.value] }})</span>
            </button>
          </div>
        </div>

        <!-- Search and Filters -->
        <div class="px-4 sm:px-6 py-3 sm:py-4 border-b border-gray-200 bg-white">
          <div class="flex flex-col space-y-3 sm:space-y-0 sm:flex-row sm:items-center sm:justify-between sm:gap-4">
            <div class="relative flex-1 max-w-full sm:max-w-md">
              <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                <Search class="w-4 h-4 text-gray-500" />
              </div>
              <input 
                v-model="searchQuery"
                type="text" 
                class="input-style" 
                placeholder="Search by number, customer, product..."
                @input="handleSearch"
              >
            </div>
            <div class="flex items-center gap-2 sm:gap-3">
              <div class="w-60 overflow-ellipsis">
                <select 
                  v-model="statusFilter" 
                  @change="loadOrders" 
                  class="input-style pr-2">
                  <option value="" class="px-4">All status</option>
                  <option value="en_attente">Pending</option>
                  <option value="confirmee">Confirmed</option>
                  <option value="en_livraison">Available for delivery</option>
                  <option value="livree">Deliveries</option>
                  <option value="annulee">Cancelled</option> 
                </select>
              </div>
              <div class="w-45">
                <select 
                  v-model="dateFilter" 
                  @change="loadOrders" 
                  class="input-style">
                  <option value="">All dates</option>
                  <option value="today">Today</option>
                  <option value="yesterday">Yesterday</option>
                  <option value="week">This Week</option>
                  <option value="month">This month</option>
                </select>
              </div>
              <button v-if="hasActiveFilters" @click="clearFilters" class="btn-deconnexion">
                <X class="w-4 h-4 text-white  sm:hidden" />
                <span class="hidden sm:inline text-xs">Clear Filters</span>
              </button>
            </div>
          </div>
        </div>

        <!-- Orders Table -->
        <div class="overflow-x-auto">
          <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
              <tr>
                <th scope="col" class="px-3 sm:px-6 py-2 sm:py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Image</th>
                <th scope="col" class="px-3 sm:px-6 py-2 sm:py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Order</th>
                <th scope="col" class="px-3 sm:px-6 py-2 sm:py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Customer</th>
                <th scope="col" class="hidden md:table-cell px-3 sm:px-6 py-2 sm:py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Product</th>
                <th scope="col" class="px-3 sm:px-6 py-2 sm:py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Delivery</th>
                <th scope="col" class="px-3 sm:px-6 py-2 sm:py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Amount</th>
                <th scope="col" class="hidden lg:table-cell px-3 sm:px-6 py-2 sm:py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                <th scope="col" class="px-3 sm:px-6 py-2 sm:py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Payment</th>
                <th scope="col" class="px-3 sm:px-6 py-2 sm:py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
              </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
              <tr v-for="order in paginatedOrders" :key="order.id" class="hover:bg-gray-50 transition-colors">
                <!--  Added product image column -->
                <td class="px-3 sm:px-6 py-3 sm:py-4 whitespace-nowrap">
                  <div class="w-16 h-16 rounded-lg overflow-hidden shadow-sm border border-gray-200">
                    <img 
                      v-if="order.produit_image" 
                      :src="order.produit_image" 
                      :alt="order.produit_nom"
                      class="w-full h-full object-cover"
                      @error="handleImageError"
                    />
                    <div v-else class="w-full h-full bg-gray-100 flex items-center justify-center">
                      <BoxIcon class="w-8 h-8 text-gray-400" />
                    </div>
                  </div>
                </td>
                
                <td class="px-3 sm:px-6 py-3 sm:py-4 whitespace-nowrap">
                  <div class="space-y-1">
                    <div class="text-xs sm:text-sm font-medium text-gray-900">{{ order.numero_commande }}</div>
                    <div class="flex items-center text-xs text-gray-500">
                      <Calendar class="h-3 w-3 mr-1 primary-color" />
                      {{ formatDate(order.date_commande) }}
                    </div>
                    <div class="text-xs text-gray-400">{{ formatTime(order.date_commande) }}</div>
                  </div>
                </td>
                
                <td class="px-3 sm:px-6 py-3 sm:py-4 whitespace-nowrap">
                  <div class="space-y-1">
                    <div class="flex items-center text-xs sm:text-sm font-medium text-gray-900">
                      <User class="h-3 w-3 mr-1 primary-color" />
                      {{ order.client_nom }}
                    </div>
                    <div class="flex items-center text-xs text-gray-500">
                      <PhoneCall class="h-3 w-3 mr-1 primary-color" />
                      {{ order.client_telephone }}
                    </div>
                  </div>
                </td>
                
                <td class="hidden md:table-cell px-3 sm:px-6 py-3 sm:py-4 whitespace-nowrap" style="width: 150px; max-width: 150px;">
                  <div class="space-y-1">
                    <div class="text-xs sm:text-sm font-medium text-gray-900 truncate" style="max-width: 140px;" :title="order.produit_nom">{{ order.produit_nom }}</div>
                    <div class="flex items-center gap-2 text-xs text-gray-500">
                      <span>Qty: {{ order.quantite }}</span>
                      <span>{{ formatCurrency(order.produit_prix) }}</span>
                    </div>
                    <div class="flex items-center text-xs text-gray-600">
                      <Building class="h-3 w-3 mr-1 primary-color" />
                      <span class="truncate" style="max-width: 100px;" :title="order.boutique_name || order.boutique_nom">{{ order.boutique_name || order.boutique_nom }}</span>
                    </div>
                  </div>
                </td>
                
                <td class="px-3 sm:px-6 py-3 sm:py-4 whitespace-nowrap">
                  <div class="space-y-1">
                    <div class="flex items-center text-xs sm:text-sm font-medium text-gray-900">
                      <Truck class="h-3 w-3 mr-1 primary-color" />
                      {{ getDeliveryTypeLabel(order.type_livraison) }}
                    </div>
                    <div class="flex items-center text-xs text-gray-500">
                      <MapPin class="h-3 w-3 mr-1 primary-color" />
                      {{ order.commune || order.ville }}
                    </div>
                  </div>
                </td>
                
                <td class="px-3 sm:px-6 py-3 sm:py-4 whitespace-nowrap text-right">
                  <div class="space-y-1">
                    <div class="text-sm sm:text-base font-bold text-gray-900">{{ formatCurrency(order.total) }}</div>
                    <div class="text-xs text-gray-500">
                      <div>Sub-total: {{ formatCurrency(order.sous_total) }}</div>
                      <div>Delivery: {{ formatCurrency(order.frais_livraison) }}</div>
                    </div>
                  </div>
                </td>
                
                <td class="hidden lg:table-cell px-3 sm:px-6 py-3 sm:py-4 whitespace-nowrap">
                  <span :class="['inline-flex items-center gap-1 px-2 py-1 rounded-full text-xs font-medium', getStatusClass(order.statut)]">
                    <component :is="getStatusIcon(order.statut)" class="h-3 w-3" />
                    {{ getStatusLabel(order.statut) }}
                  </span>
                </td>
                
                <!--  Added payment proof column -->
                <td class="px-2 sm:px-4 py-2 sm:py-2 whitespace-nowrap">
                  <div v-if="order.preuve_paiement" class="flex flex-col gap-1">
                    <button 
                      @click="showPaymentProof(order)"
                      class="submit-btn h-7 text-xs"
                      title="View payment proof"
                    >
                      <ImageIcon class="h-3 w-3" />
                      <span>View Proof</span>
                    </button>
                    <button 
                      v-if="!order.preuve_validee && order.tobevalidate !== 'valid'"
                      @click="openValidateProofModal(order)"
                      class="btn-degrade-orange h-7 text-xs"
                      title="Validate proof"
                    >
                      <CheckCircleIcon class="h-3 w-3" />
                      <span>To be Validate</span>
                    </button>
                    <div v-else style="display: grid;">
                      <span 
                      style="margin: 0"
                      class="inline-flex items-center justify-center gap-1 px-2 py-1 bg-green-100 green-color rounded-lg text-xs font-medium"
                    >
                      <CheckCircle2Icon class="h-3 w-3" />
                      <span>Validated</span>
                    </span>
                    <br/>
                    <span class="h-1"></span>
                    <button 
                      v-if="order.prepare !== 'valid'"
                      @click="openValidatePrepareModal(order)"
                      class="btn-degrade-orange h-7 text-xs"
                      title="Validate proof"
                    >
                      <CheckCircleIcon class="h-3 w-3" />
                      <span>Terminate Prepare</span>
                    </button>
                    <span 
                      v-if="order.prepare === 'valid'"
                      class="inline-flex items-center justify-center gap-1 px-2 py-1 bg-green-100 green-color rounded-lg text-xs font-medium mb-1"
                    >
                      <CheckCircle2Icon class="h-3 w-3" />
                      <span>Prepare Terminated</span>
                    </span>

                    <button 
                      v-if="order.delivered !== 'valid' && order.prepare === 'valid'"
                      @click="openValidateDeliveredModal(order)"
                      class="btn-degrade-orange h-7 text-xs "
                      title="Validate proof"
                    >
                      <CheckCircleIcon class="h-3 w-3" />
                      <span>Delivered </span>
                    </button>
                    <span 
                      v-if="order.delivered === 'valid'"
                      class="inline-flex items-center justify-center gap-1 px-2 py-1 bg-green-100 green-color rounded-lg text-xs font-medium"
                    >
                      <CheckCircle2Icon class="h-3 w-3" />
                      <span>delivered to customer</span>
                    </span>

                    </div>
                    
                  </div>
                  <span v-else class="flex text-xs text-gray-400 items-center justify-center">No Proof</span>
                </td>
                
                <td class="px-3 sm:px-6 py-3 sm:py-4 whitespace-nowrap text-xs sm:text-sm font-medium">
                  <div class="flex items-center space-x-1 sm:space-x-2">
                    <button 
                      v-if="order.statut === 'en_attente'" 
                      @click.stop="showConfirmModal('confirm', order)"
                      class="p-1.5 sm:p-2 rounded-lg transition-colors text-xs"
                      title="Confirm"
                      style="background-color: rgba(74, 222, 128, 0.1); color: rgb(22, 163, 74);"
                    >
                      <Check class="h-3 w-3 sm:h-4 sm:w-4 text-black" />
                    </button>
                    <button 
                      v-if="order.statut === 'confirmee'" 
                      @click.stop="showConfirmModal('ship', order)"
                      class="p-1.5 sm:p-2 rounded-lg transition-colors text-xs"
                      title="Ship"
                      style="background-color: rgba(147, 197, 253, 0.1); color: rgb(37, 99, 235);"
                    >
                      <Truck class="h-3 w-3 sm:h-4 sm:w-4 text-black" />
                    </button>
                    <button 
                      v-if="order.statut === 'en_livraison'" 
                      @click.stop="showConfirmModal('deliver', order)"
                      class="p-1.5 sm:p-2 rounded-lg transition-colors text-xs"
                      title="Deliver"
                      style="background-color: rgba(191, 144, 255, 0.1); color: rgb(99, 37, 235);"
                    >
                      <ArchiveIcon class="h-3 w-3 sm:h-4 sm:w-4 text-black" />
                    </button>
                    <button 
                      v-if="['en_attente', 'confirmee'].includes(order.statut)" 
                      @click.stop="showConfirmModal('cancel', order)"
                      class="p-1.5 sm:p-2 rounded-lg transition-colors text-xs"
                      title="Cancel"
                      style="background-color: rgba(248, 113, 113, 0.1); color: rgb(220, 38, 38);"
                    >
                      <BookmarkXIcon class="h-3 w-3 sm:h-4 sm:w-4 text-black" />
                    </button>
                    <button 
                      @click.stop="openOrderDetails(order)" 
                      class="p-1.5 sm:p-2 rounded-lg transition-colors text-xs"
                      title="See details"
                      style="background-color: rgba(209, 213, 219, 0.1); color: rgb(71, 85, 105);"
                    >
                      <EyeIcon class="h-3 w-3 sm:h-4 sm:w-4 text-black" />
                    </button>
                  </div>
                </td>
              </tr>
            </tbody>
          </table>
        </div>

        <!-- Pagination -->
        <div class="bg-gray-50 px-4 sm:px-6 py-3 sm:py-4 flex flex-col sm:flex-row sm:items-center sm:justify-between border-t border-gray-200 gap-3 sm:gap-0">
          <div class="flex items-center space-x-2 sm:space-x-4 text-xs sm:text-sm">
            <span class="text-gray-700 hidden sm:inline">Items per page</span>
            <span class="text-gray-700 sm:hidden">Per page</span>
            <div class="w-20">
              <select 
                v-model="itemsPerPage" 
                @change="handleItemsPerPageChange"
                style="padding-left:20px;"
                class="input-style text-xs sm:text-sm"
              >
                <option :value="10">10</option>
                <option :value="25">25</option>
                <option :value="50">50</option>
                <option :value="100">100</option>
              </select>
            </div>
            <span class="text-gray-700">
              {{ ((currentPage - 1) * itemsPerPage) + 1 }} - {{ Math.min(currentPage * itemsPerPage, filteredOrders.length) }} of {{ filteredOrders.length }}
            </span>
          </div>
          <div class="flex items-center space-x-2">
            <button 
              @click="changePage(currentPage - 1)" 
              :disabled="isFirstPage"
              class="p-2 rounded-lg btn-gray disabled:opacity-50 disabled:cursor-not-allowed"
            >
              <ChevronLeft class="h-4 w-4" />
            </button>
            <div class="flex space-x-1">
              <button 
                v-for="page in getVisiblePages()" 
                :key="page"
                @click="changePage(page)"
                :class="[
                  'px-3 py-1 rounded-lg text-sm font-medium transition-colors',
                  currentPage === page ? 'btn-degrade-orange' : 'btn-gray'
                ]"
              >
                {{ page }}
              </button>
            </div>
            <button 
              @click="changePage(currentPage + 1)" 
              :disabled="isLastPage"
              class="p-2 rounded-lg btn-gray disabled:opacity-50 disabled:cursor-not-allowed"
            >
              <ChevronRight class="h-4 w-4" />
            </button>
          </div>
        </div>
      </div>
    </div>

    <!--  Payment Proof Modal -->
    <div v-if="showPaymentProofModal" class="fixed inset-0 bg-black/60 backdrop-blur-sm flex items-center justify-center z-50 p-4" @click="closePaymentProofModal">
      <div class="bg-white rounded-2xl shadow-2xl w-full max-w-3xl max-h-[90vh] overflow-hidden" @click.stop>
        <div class="sticky top-0 bg-white border-b border-gray-100 px-6 py-4 flex items-center justify-between">
          <div class="flex items-center gap-3">
            <div class="p-2 bg-blue-100 rounded-lg">
              <ImageIcon class="h-5 w-5 green-color" />
            </div>
            <div>
              <h3 class="text-lg font-bold text-gray-900">Payment Proof</h3>
              <p class="text-sm text-gray-500">Order #{{ currentProofOrder?.numero_commande }}</p>
            </div>
          </div>
            <XIcon class="h-7 w-7 text-gray-500 cursor-pointer" @click="closePaymentProofModal"/>
        </div>
        <div class="p-6 overflow-y-scroll max-h-[80vh]">
          <div class="bg-gray-50 rounded-xl overflow-hidden">
            <img 
              v-if="currentProofOrder?.preuve_paiement" 
              :src="currentProofOrder.preuve_paiement" 
              alt="Payment Proof"
              class="w-full h-auto max-h-[60vh] object-contain"
            />
          </div>
          <div class="mt-4 flex justify-end gap-3">
            <button 
              @click="closePaymentProofModal"
              class="px-4 py-2 btn-gray"
            >
              Close
            </button>
            <button 
              v-if="!currentProofOrder?.preuve_validee"
              @click="openValidateProofModal(currentProofOrder)"
              class="submit-btn"
            >
              Validate Proof
            </button>
          </div>
        </div>
      </div>
    </div>

    <!--  Validate Proof Modal with comment -->
    <div v-if="showValidateProofModal" class="fixed inset-0 bg-black/60 backdrop-blur-sm flex items-center justify-center z-50 p-4" @click="closeValidateProofModal">
      <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md" @click.stop>
        <div class="px-6 py-4 border-b border-gray-100">
          <div class="flex items-center gap-3">
            <div class="p-2 bg-orange-100 rounded-lg">
              <CheckCircleIcon class="h-5 w-5 primary-color" />
            </div>
            <div>
              <h3 class="text-lg font-bold text-gray-900">Validate Payment Proof</h3>
              <p class="text-sm text-gray-500">Order #{{ currentValidateOrder?.numero_commande }}</p>
            </div>
          </div>
        </div>
        <div class="p-6 space-y-4">
          <p class="text-gray-600">Are you sure you want to validate this payment proof?</p>
          
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">
              Add a comment (optional)
            </label>
            <textarea 
              v-model="validationComment"
              rows="4"
              class="input-style"
              placeholder="Enter your comment here..."
            ></textarea>
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">
              Estimated delivery date <span class="text-red-500">*</span>
            </label>
            <input 
              type="date"
              v-model="estimatedDeliveryDate"
              class="input-style"
              required
            />
          </div>

          <div class="flex justify-end gap-3 pt-4">
            <button 
              @click="closeValidateProofModal"
              class="px-4 py-2 btn-gray"
            >
              Cancel
            </button>
            <button 
            style="background-color: #16a34a;"
              @click="validatePaymentProof"
              class="btn-degrade-orange"
            >
              Validate
            </button>
          </div>
        </div>
      </div>
    </div>

    <div v-if="showValidateProofModal2" class="fixed inset-0 bg-black/60 backdrop-blur-sm flex items-center justify-center z-50 p-4" @click="closeValidateProofModal">
      <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md" @click.stop>
        <div class="px-6 py-4 border-b border-gray-100">
          <div class="flex items-center gap-3">
            <div class="p-2 bg-green-100 rounded-lg">
              <CheckCircleIcon class="h-5 w-5 text-green-600" />
            </div>
            <div>
              <h3 class="text-lg font-bold text-gray-900">Validate Payment Proof</h3>
              <p class="text-sm text-gray-500">Order #{{ currentValidatePayment?.montant }} $</p>
            </div>
          </div>
        </div>
        <div class="p-6 space-y-4">
          <p class="text-gray-600">Are you sure you want to validate this payment proof?</p>
          
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">
              Add a comment (optional)
            </label>
            <textarea 
              v-model="validationComment"
              rows="4"
              class="input-style"
              placeholder="Enter your comment here..."
            ></textarea>
          </div>

          <div class="flex justify-end gap-3 pt-4">
            <button 
              @click="showValidateProofModal2 = false"
              class="px-4 py-2 btn-gray"
            >
              Cancel
            </button>
            <button 
            style="background-color: #16a34a;"
              @click="validatePaymentProof2"
              class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg font-medium transition-colors"
            >
              Validate
            </button>
          </div>
        </div>
      </div>
    </div>

    <div v-if="showValidateprepareModal" class="fixed inset-0 bg-black/60 backdrop-blur-sm flex items-center justify-center z-50 p-4" @click="closeValidateProofModal">
      <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md" @click.stop>
        <div class="px-6 py-4 border-b border-gray-100">
          <div class="flex items-center gap-3">
            <div class="p-2 bg-green-100 rounded-lg">
              <CheckCircleIcon class="h-5 w-5 text-green-600" />
            </div>
            <div>
              <h3 class="text-lg font-bold text-gray-900">Validate Payment Proof</h3>
              <p class="text-sm text-gray-500">Order #{{ currentValidatePayment?.montant }} $</p>
            </div>
          </div>
        </div>
        <div class="p-6 space-y-4">
          <p class="text-gray-600">Are you sure you want to validate this payment proof?</p>
          
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">
              Add a comment (optional)
            </label>
            <textarea 
              v-model="validationComment"
              rows="4"
              class="input-style"
              placeholder="Enter your comment here..."
            ></textarea>
          </div>

          <div class="flex justify-end gap-3 pt-4">
            <button 
              @click="showValidateProofModal2 = false"
              class="px-4 py-2 btn-gray"
            >
              Cancel
            </button>
            <button 
            style="background-color: #16a34a;"
              @click="validatePaymentProof2"
              class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg font-medium transition-colors"
            >
              Validate
            </button>
          </div>
        </div>
      </div>
    </div>

    <div v-if="showValidateprepareModal" class="fixed inset-0 bg-black/60 backdrop-blur-sm flex items-center justify-center z-50 p-4" @click="closeValidateprepareModal">
      <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md" @click.stop>
        <div class="px-6 py-4 border-b border-gray-100">
          <div class="flex items-center gap-3">
            <div class="p-2 bg-orange-100 rounded-lg">
              <CheckCircleIcon class="h-5 w-5 primary-color" />
            </div>
            <div>
              <h3 class="text-lg font-bold text-gray-900">Preparation completed</h3>
              <p class="text-sm text-gray-500">Order #{{ currentValidateOrder?.numero_commande }}</p>
            </div>
          </div>
        </div>
        <div class="p-6 space-y-4">
          <p class="text-gray-600">Are you sure you want to terminate the preparation?</p>
          
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">
              Add a comment (optional)
            </label>
            <textarea 
              v-model="validationComment"
              rows="4"
              class="input-style"
              placeholder="Enter your comment here..."
            ></textarea>
          </div>

          <div class="flex justify-end gap-3 pt-4">
            <button 
              @click="closeValidateprepareModal"
              class="px-4 py-2 btn-gray"
            >
              Cancel
            </button>
            <button 
            style="background-color: #16a34a;"
              @click="validatePreparation"
              class="btn-degrade-orange"
            >
              Validate
            </button>
          </div>
        </div>
      </div>
    </div>
    
    <div v-if="showValidatedeliveredModal" class="fixed inset-0 bg-black/60 backdrop-blur-sm flex items-center justify-center z-50 p-4" @click="closeValidateProofModal">
      <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md" @click.stop>
        <div class="px-6 py-4 border-b border-gray-100">
          <div class="flex items-center gap-3">
            <div class="p-2 bg-green-100 rounded-lg">
              <CheckCircleIcon class="h-5 w-5 text-green-600" />
            </div>
            <div>
              <h3 class="text-lg font-bold text-gray-900">Delivered completed</h3>
              <p class="text-sm text-gray-500">Order #{{ currentValidateOrder?.numero_commande }}</p>
            </div>
          </div>
        </div>
        <div class="p-6 space-y-4">
          <p class="text-gray-600">Are you sure you want to terminate the preparation?</p>
          
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">
              Add a comment (optional)
            </label>
            <textarea 
              v-model="validationComment"
              rows="4"
              class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-transparent resize-none"
              placeholder="Enter your comment here..."
            ></textarea>
          </div>

          <div class="flex justify-end gap-3 pt-4">
            <button 
              @click="showValidatedeliveredModal = false"
              class="px-4 py-2 btn-gray"
            >
              Cancel
            </button>
            <button 
            style="background-color: #16a34a;"
              @click="validateDelivered"
              class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg font-medium transition-colors"
            >
              Validate
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- Payment Proof Modal showing all payments with stats -->
<div v-if="showPaymentProofModal" class="fixed inset-0 bg-black/60 backdrop-blur-sm flex items-center justify-center z-50 p-4" @click="closePaymentProofModal">
  <div class="bg-white rounded-2xl shadow-2xl w-full max-w-4xl max-h-[90vh] overflow-hidden" @click.stop>
    <div class="sticky top-0 bg-white border-b border-gray-100 px-6 py-4 flex items-center justify-between">
      <div class="flex items-center gap-3">
        <div class="p-2 bg-green-100 rounded-lg">
          <ImageIcon class="h-5 w-5 green-color" />
        </div>
        <div>
          <h3 class="text-lg font-bold text-gray-900">Payment History</h3>
          <p class="text-sm text-gray-500">Order #{{ currentProofOrder?.numero_commande }}</p>
        </div>
      </div>
        <XIcon class="h-7 w-7 text-gray-500 cursor-pointer" @click="closePaymentProofModal" />
    </div>
    
    <div class="p-6 overflow-y-auto max-h-[calc(90vh-80px)]">
      <!-- Payment Statistics -->
      <div class="grid grid-cols-3 gap-4 mb-6">
        <div class="bg-gradient-to-br from-orange-50 to-orange-100 rounded-xl p-4 border border-orange-200">
          <div class="text-sm primary-color font-medium mb-1">Total Order</div>
          <div class="text-2xl font-bold primary-color">{{ formatCurrency(currentProofOrder?.total || 0) }}</div>
        </div>
        <div class="bg-gradient-to-br from-green-50 to-green-100 rounded-xl p-4 border border-green-200">
          <div class="text-sm green-color font-medium mb-1">Total Paid</div>
          <div class="text-2xl font-bold green-color">{{ formatCurrency(calculateTotalPaid(currentProofOrder)) }}</div>
        </div>
        <div class="bg-gradient-to-br from-red-50 to-red-100 rounded-xl p-4 border border-red-200">
          <div class="text-sm error-color font-medium mb-1">Remaining</div>
          <div class="text-2xl font-bold error-color">{{ formatCurrency(calculateRemaining(currentProofOrder)) }}</div>
        </div>
      </div>

      <!-- Payment List -->
      <div class="space-y-4">
        <!-- Initial Payment -->
        <div class="bg-white border border-gray-200 rounded-xl p-4 shadow-sm">
          <div class="flex items-start justify-between mb-3">
            <div class="flex items-center gap-3">
              <div class="w-12 h-12 bg-orange-100 rounded-lg flex items-center justify-center">
                <CreditCardIcon class="h-6 w-6 primary-color" />
              </div>
              <div>
                <div class="font-semibold text-gray-900">Initial Payment (30%)</div>
                <div class="text-sm text-gray-500">{{ formatDate(currentProofOrder?.date_paiement) }}</div>
              </div>
            </div>
            <div class="text-right">
              <div class="text-xl font-bold text-gray-900">{{ formatCurrency((currentProofOrder?.total || 0) * 0.3) }}</div>
              <span v-if="currentProofOrder?.estimate_prepare" class="inline-flex items-center gap-1 px-2 py-1 bg-green-100 green-color rounded-full text-xs font-medium mt-1">
                <CheckCircle2Icon class="h-3 w-3" />
                Validated
              </span>
              <span v-else class="inline-flex items-center gap-1 px-2 py-1 bg-yellow-100 text-yellow-800 rounded-full text-xs font-medium mt-1">
                <Clock class="h-3 w-3" />
                Pending
              </span>
            </div>
          </div>
          <button 
            @click="viewProofImage(currentProofOrder?.preuve_paiement)"
            class="w-full submit-btn"
          >
            <ImageIcon class="h-4 w-4" />
            View Proof
          </button>
        </div>

        <!-- Additional Payments -->
        <div v-if="currentProofOrder?.paiements_additionnels && currentProofOrder.paiements_additionnels  .length > 0" 
             v-for="(payment, index) in currentProofOrder.paiements_additionnels" 
             :key="payment.id || index"
             class="bg-white border border-gray-200 rounded-xl p-4 shadow-sm">
          <div class="flex items-start justify-between mb-3">
            <div class="flex items-center gap-3">
              <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center">
                <CreditCardIcon class="h-6 w-6 purple-color" />
              </div>
              <div>
                <div class="font-semibold text-gray-900">Additional Payment #{{ index + 1 }}</div>
                <div class="text-sm text-gray-500">{{ formatDate(payment.date_paiement) }}</div>
                <div v-if="payment.commentaire" class="text-sm text-gray-600 mt-1">{{ payment.commentaire }}</div>
              </div>
            </div>
            <div class="text-right">
              <div class="text-xl font-bold text-gray-900">{{ formatCurrency(payment.montant) }}</div>
              <span v-if="payment.valide === 'valid'" class="inline-flex items-center gap-1 px-2 py-1 bg-green-100 green-color rounded-full text-xs font-medium mt-1">
                <CheckCircle2Icon class="h-3 w-3" />
                Validated
              </span>
              <span v-else class="inline-flex items-center gap-1 px-2 py-1 bg-yellow-100 text-yellow-800 rounded-full text-xs font-medium mt-1">
                <Clock class="h-3 w-3" />
                Pending
              </span>
            </div>
          </div>
          <div style="display: flex; gap: 10px;">
            <button 
              @click="viewProofImage(payment.preuve_url)"
              class="w-full submit-btn"
            >
              <ImageIcon class="h-4 w-4" />
              View Proof
            </button>
            <button 
            style="background-color: green; color: white;"
                      v-if="payment.valide !== 'valid'"
                      @click="openValidateProofModal2(payment)"
                      class="inline-flex btn-degrade-orange"
                      title="Validate proof"
                    >
                      <CheckCircleIcon class="h-3 w-3" />
                      <span>Validate</span>
            </button>
          </div>
        </div>

        <!-- No additional payments message -->
        <div v-if="!currentProofOrder?.paiements || currentProofOrder.paiements.length === 0" 
             class="text-center py-8 text-gray-500">
          <p>No additional payments yet</p>
        </div>
      </div>

      <div class="mt-6 flex justify-end">
        <button 
          @click="closePaymentProofModal"
          class="px-4 py-2 btn-gray"
        >
          Close
        </button>
      </div>
    </div>
  </div>
</div>

<!-- Modal for viewing individual proof images -->
<div v-if="showProofImageModal" class="fixed inset-0 bg-black/80 backdrop-blur-sm flex items-center justify-center z-50 p-4" @click="closeProofImageModal">
  <div class="bg-white rounded-2xl shadow-2xl w-full max-w-3xl max-h-[90vh] overflow-hidden" @click.stop>
    <div class="sticky top-0 bg-white border-b border-gray-100 px-6 py-4 flex items-center justify-between">
      <h3 class="text-lg font-bold text-gray-900">Payment Proof</h3>
        <XIcon class="h-5 w-5 text-gray-500 cursor-pointer" @click="closeProofImageModal" />
    </div>
    <div class="p-6">
      <div class="bg-gray-50 rounded-xl overflow-scroll">
        <img 
          v-if="currentProofImage" 
          :src="currentProofImage" 
          alt="Payment Proof"
          class="w-full h-auto object-contain max-h-[60vh]"
        />
      </div>
      <div class="mt-4 flex justify-end">
        <button 
          @click="closeProofImageModal"
          class="px-4 py-2 btn-gray"
        >
          Close
        </button>
      </div>
    </div>
  </div>
</div>

    <!-- Confirmation Modal -->
    <div v-if="showConfirmationModal" class="fixed inset-0 bg-black/60 backdrop-blur-sm flex items-center justify-center z-50 p-4" @click="closeConfirmModal">
      <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md" @click.stop>
        <div class="p-6">
          <div class="flex items-center mb-4">
            <div :class="['w-12 h-12 rounded-full flex items-center justify-center mr-4 ', getConfirmationIconClass()]">
              <CheckCircle class="h-6 w-6" />
            </div>
            <div>
              <h3 class="text-lg font-semibold text-gray-900">{{ getConfirmationTitle() }}</h3>
              <p class="text-sm text-gray-500">Order #{{ selectedOrder?.numero_commande }}</p>
            </div>
          </div>
          <p class="text-gray-600 mb-6">{{ getConfirmationMessage() }}</p>
          <div class="flex justify-end space-x-3">
            <button 
              @click="closeConfirmModal"
              class="btn-gray"
            >
              Cancel
            </button>
            <button 
              @click="executeAction"
              :class="['px-4 py-2 rounded-lg font-medium transition-colors', getConfirmationButtonClass()]"
            >
              {{ getConfirmationButtonText() }}
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- Order Details Modal -->
    <div v-if="showOrderModal" class="fixed inset-0 bg-black/60 backdrop-blur-sm flex items-center justify-center z-50 p-4" @click="closeOrderModal">
      <div class="bg-white rounded-2xl shadow-2xl w-full max-w-4xl max-h-[90vh] overflow-y-auto" @click.stop>
        <div class="sticky top-0 bg-white border-b border-gray-100 px-8 py-6 rounded-t-2xl">
          <div class="flex items-center justify-between">
            <div class="flex items-center gap-4">
              <div class="p-3 bg-orange rounded-xl">
                <FileTextIcon class="h-6 w-6 text-white" />
              </div>
              <h3 class="text-2xl font-bold text-gray-900">Order Details</h3>
            </div>
            
              <XIcon class="h-7 w-7 cursor-pointer text-gray-500" @click="closeOrderModal" />
          </div>
        </div>

        <div v-if="selectedOrder" class="p-8 space-y-8">
          <!-- Order Summary -->
          <div class="bg-gradient-to-r from-orange-50 to-orange-100 rounded-2xl p-6 border border-orange-200">
            <div class="flex items-center justify-between mb-6">
              <div class="flex items-center gap-4">
                <div class="p-4 bg-orange rounded-2xl">
                  <FileCheck class="h-7 w-7 text-white" />
                </div>
                <div>
                  <div class="text-2xl font-bold text-gray-900">{{ selectedOrder.numero_commande }}</div>
                  <div class="text-gray-600">{{ formatDate(selectedOrder.date_commande) }} at {{ formatTime(selectedOrder.date_commande) }}</div>
                </div>
              </div>
              <span :class="['inline-flex items-center gap-2 px-4 py-2 rounded-full text-sm font-medium', getStatusClass(selectedOrder.statut)]">
                <component :is="getStatusIcon(selectedOrder.statut)" class="h-4 w-4" />
                {{ getStatusLabel(selectedOrder.statut) }}
              </span>
            </div>
            
            <!-- Quick Stats -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
              <div class="bg-white/70 rounded-xl p-4 flex items-center gap-3">
                <Banknote class="h-6 w-6 primary-color" />
                <div>
                  <div class="text-sm text-gray-600">Total</div>
                  <div class="text-xl font-bold text-gray-900">{{ formatCurrency(selectedOrder.total) }}</div>
                </div>
              </div>
              <div class="bg-white/70 rounded-xl p-4 flex items-center gap-3">
                <BoxIcon class="h-6 w-6 primary-color" />
                <div>
                  <div class="text-sm text-gray-600">Quantity</div>
                  <div class="text-xl font-bold text-gray-900">{{ selectedOrder.quantite }}</div>
                </div>
              </div>
              <div class="bg-white/70 rounded-xl p-4 flex items-center gap-3">
                <Truck class="h-6 w-6 primary-color" />
                <div>
                  <div class="text-sm text-gray-600">Delivery</div>
                  <div class="text-xl font-bold text-gray-900">{{ formatCurrency(selectedOrder.frais_livraison) }}</div>
                </div>
              </div>
            </div>
          </div>

          <!-- Info Sections -->
          <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            <!-- Customer Information -->
            <div class="bg-white rounded-2xl p-6 border border-gray-100 shadow-sm">
              <div class="flex items-center gap-3 mb-4">
                <User class="h-6 w-6 primary-color" />
                <h4 class="text-lg font-semibold text-gray-900">Customer Information</h4>
              </div>
              <div class="space-y-3">
                <div class="flex items-center gap-3">
                  <User2 class="h-4 w-4 text-gray-500" />
                  <div>
                    <span class="text-sm text-gray-500">Full name</span>
                    <div class="font-medium text-gray-900">{{ selectedOrder.client_nom }}</div>
                  </div>
                </div>
                <div class="flex items-center gap-3">
                  <PhoneCall class="h-4 w-4 text-gray-500" />
                  <div>
                    <span class="text-sm text-gray-500">Phone number</span>
                    <div class="font-medium text-gray-900">{{ selectedOrder.client_telephone }}</div>
                  </div>
                </div>
              </div>
            </div>

            <!-- Delivery Information -->
            <div class="bg-white rounded-2xl p-6 border border-gray-100 shadow-sm">
              <div class="flex items-center gap-3 mb-4">
                <Truck class="h-6 w-6 primary-color" />
                <h4 class="text-lg font-semibold text-gray-900">Delivery Information</h4>
              </div>
              <div class="space-y-3">
                <div class="flex items-center gap-3">
                  <Truck class="h-4 w-4 text-gray-500" />
                  <div>
                    <span class="text-sm text-gray-500">Delivery type</span>
                    <div class="font-medium text-gray-900">{{ getDeliveryTypeLabel(selectedOrder.type_livraison) }}</div>
                  </div>
                </div>
                <div v-if="selectedOrder.commune" class="flex items-center gap-3">
                  <MapPin class="h-4 w-4 text-gray-500" />
                  <div>
                    <span class="text-sm text-gray-500">Municipality</span>
                    <div class="font-medium text-gray-900">{{ selectedOrder.commune }}</div>
                  </div>
                </div>
                <div class="flex items-start gap-3">
                  <Home class="h-4 w-4 text-gray-500 mt-1" />
                  <div>
                    <span class="text-sm text-gray-500">Full Address</span>
                    <div class="font-medium text-gray-900">{{ selectedOrder.adresse_complete }}</div>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- Product Information -->
          <div class="bg-white rounded-2xl p-6 border border-gray-100 shadow-sm">
            <div class="flex items-center gap-3 mb-6">
              <BoxIcon class="h-6 w-6 primary-color" />
              <h4 class="text-lg font-semibold text-gray-900">Product Ordered</h4>
            </div>
            <div class="flex items-start gap-6">
              <!--  Show product image in modal -->
              <div class="w-24 h-24 rounded-xl overflow-hidden shadow-md border border-gray-200 flex-shrink-0">
                <img 
                  v-if="selectedOrder.produit_image" 
                  :src="selectedOrder.produit_image" 
                  :alt="selectedOrder.produit_nom"
                  class="w-full h-full object-cover"
                  @error="handleImageError"
                />
                <div v-else class="w-full h-full bg-gray-100 flex items-center justify-center">
                  <BoxIcon class="h-12 w-12 text-gray-400" />
                </div>
              </div>
              <div class="flex-1">
                <h5 class="text-xl font-semibold text-gray-900 mb-3">{{ selectedOrder.produit_nom }}</h5>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-sm">
                  <div class="flex items-center gap-2">
                    <BoxIcon class="h-4 w-4 text-gray-500" />
                    <span class="text-gray-600">Quantity: {{ selectedOrder.quantite }}</span>
                  </div>
                  <div class="flex items-center gap-2">
                    <Banknote class="h-4 w-4 text-gray-500" />
                    <span class="text-gray-600">Unit Price: {{ formatCurrency(selectedOrder.produit_prix) }}</span>
                  </div>
                  <div class="flex items-center gap-2">
                    <Building class="h-4 w-4 text-gray-500" />
                    <span class="text-gray-600">Store: {{ selectedOrder.boutique_nom }}</span>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!--  Payment Information Section -->
          <div v-if="selectedOrder.preuve_paiement" class="bg-white rounded-2xl p-6 border border-gray-100 shadow-sm">
            <div class="flex items-center gap-3 mb-6">
              <CreditCardIcon class="h-6 w-6 primary-color" />
              <h4 class="text-lg font-semibold text-gray-900">Payment Information</h4>
            </div>
            <!-- Payment Statistics -->
            <div class="grid grid-cols-3 gap-4 mb-6">
              <div class="bg-gradient-to-br from-orange-50 to-orange-100 rounded-xl p-4 border border-orange-200">
                <div class="text-xs primary-color font-medium mb-1">Total Order</div>
                <div class="text-lg font-bold primary-color">{{ formatCurrency(selectedOrder.total) }}</div>
              </div>
              <div class="bg-gradient-to-br from-green-50 to-green-100 rounded-xl p-4 border border-green-200">
                <div class="text-xs gree-color font-medium mb-1">Total Paid</div>
                <div class="text-lg font-bold green-color">{{ formatCurrency(calculateTotalPaid(selectedOrder)) }}</div>
              </div>
              <div class="bg-gradient-to-br from-orange-50 to-orange-100 rounded-xl p-4 border border-orange-200">
                <div class="text-xs error-color font-medium mb-1">Remaining</div>
                <div class="text-lg font-bold error-color">{{ formatCurrency(calculateRemaining(selectedOrder)) }}</div>
              </div>
            </div>
            <div class="space-y-4">
              <div class="flex items-center justify-between">
                <span class="text-sm text-gray-600">Payment date</span>
                <span class="font-medium text-gray-900">{{ formatDate(selectedOrder.date_paiement) }}</span>
              </div>
              <div class="flex items-center justify-between">
                <span class="text-sm text-gray-600">Proof status</span>
                <span v-if="selectedOrder.preuve_validee" class="inline-flex items-center gap-1 px-3 py-1 bg-green-100 green-color rounded-full text-xs font-medium">
                  <CheckCircle2Icon class="h-3 w-3" />
                  Validated
                </span>
                <span v-else class="inline-flex items-center gap-1 px-3 py-1 bg-yellow-100 text-yellow-800 rounded-full text-xs font-medium">
                  <Clock class="h-3 w-3" />
                  Pending validation
                </span>
              </div>
              <div class="pt-4 border-t border-gray-100">
                <button 
                  @click="showPaymentProof(selectedOrder)"
                  class="submit-btn w-full"
                >
                  <ImageIcon class="h-4 w-4" />
                  View Payment Proof
                </button>
              </div>
            </div>
          </div>

          <!-- Boutique Information -->
          <div class="bg-white rounded-2xl p-6 border border-gray-100 shadow-sm">
            <div class="flex items-center gap-3 mb-6">
              <Building class="h-6 w-6 primary-color" />
              <h4 class="text-lg font-semibold text-gray-900">Store Information</h4>
            </div>
            <div class="space-y-4">
              <div class="flex items-center gap-4">
                <div class="w-12 h-12 bg-gray-100 rounded-xl flex items-center justify-center">
                  <Building class="h-6 w-6 text-gray-500" />
                </div>
                <div>
                  <h5 class="text-lg font-semibold text-gray-900">{{ selectedOrder.boutique_name || selectedOrder.boutique_nom }}</h5>
                  <div v-if="selectedOrder.boutique_statut" class="mt-1">
                    <span :class="['px-2 py-1 text-xs rounded-full', getBoutiqueStatusClass(selectedOrder.boutique_statut)]">
                      {{ getBoutiqueStatusLabel(selectedOrder.boutique_statut) }}
                    </span>
                  </div>
                </div>
              </div>
              
              <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div v-if="selectedOrder.boutique_telephone || selectedOrder.boutique_email">
                  <h6 class="text-sm font-semibold text-gray-700 mb-3">Contacts</h6>
                  <div class="space-y-2">
                    <div v-if="selectedOrder.boutique_telephone" class="flex items-center gap-2 text-sm text-gray-600">
                      <PhoneCall class="h-4 w-4 text-gray-500" />
                      <span>{{ selectedOrder.boutique_telephone }}</span>
                    </div>
                    <div v-if="selectedOrder.boutique_email" class="flex items-center gap-2 text-sm text-gray-600">
                      <Mail class="h-4 w-4 text-gray-500" />
                      <span>{{ selectedOrder.boutique_email }}</span>
                    </div>
                  </div>
                </div>
                
                <div v-if="selectedOrder.boutique_adresse || selectedOrder.boutique_commune">
                  <h6 class="text-sm font-semibold text-gray-700 mb-3">Location</h6>
                  <div class="space-y-2">
                    <div v-if="selectedOrder.boutique_adresse" class="flex items-center gap-2 text-sm text-gray-600">
                      <MapPin class="h-4 w-4 text-gray-500" />
                      <span>{{ selectedOrder.boutique_adresse }}</span>
                    </div>
                    <div v-if="selectedOrder.boutique_commune" class="flex items-center gap-2 text-sm text-gray-600">
                      <Building class="h-4 w-4 text-gray-500" />
                      <span>{{ selectedOrder.boutique_commune }}</span>
                    </div>
                  </div>
                </div>
              </div>
              
              <div class="flex flex-wrap gap-3 pt-4 border-t border-gray-100">
                <button v-if="selectedOrder.boutique_telephone" @click="callBoutique(selectedOrder.boutique_telephone)" class="btn-degrade-orange">
                  <PhoneCall class="h-4 w-4" />
                  <span>Call</span>
                </button>
                <button v-if="selectedOrder.boutique_email" @click="emailBoutique(selectedOrder.boutique_email)" class="submit-btn">
                  <Mail class="h-4 w-4" />
                  <span>Email</span>
                </button>
                <button @click="notifyBoutiqueNewOrder(selectedOrder)" class="btn-gray">
                  <Bell class="h-4 w-4" />
                  <span>Notify Order</span>
                </button>
                <button @click="generateReceiptPDF(selectedOrder)" class="btn-gray">
                  <FileDownIcon class="h-4 w-4" />
                  <span>PDF Receipt</span>
                </button>
              </div>
            </div>
          </div>

          <!-- Financial Summary -->
          <div class="bg-white rounded-2xl p-6 border border-gray-100 shadow-sm">
            <div class="flex items-center gap-3 mb-6">
              <Banknote class="h-6 w-6 primary-color" />
              <h4 class="text-lg font-semibold text-gray-900">Financial Summary</h4>
            </div>
            <div class="space-y-4">
              <div class="flex justify-between items-center py-2">
                <div class="flex items-center gap-2">
                  <CalculatorIcon class="h-4 w-4 text-gray-500" />
                  <span class="text-gray-600">Subtotal</span>
                </div>
                <span class="font-medium text-gray-900">{{ formatCurrency(selectedOrder.sous_total) }}</span>
              </div>
              <div class="flex justify-between items-center py-2">
                <div class="flex items-center gap-2">
                  <Truck class="h-4 w-4 text-gray-500" />
                  <span class="text-gray-600">Delivery costs</span>
                </div>
                <span class="font-medium text-gray-900">{{ formatCurrency(selectedOrder.frais_livraison) }}</span>
              </div>
              <div class="flex justify-between items-center py-3 border-t border-gray-100">
                <div class="flex items-center gap-2">
                  <Banknote class="h-6 w-6 primary-color" />
                  <span class="text-lg font-semibold primary-color">Total</span>
                </div>
                <span class="text-xl font-bold primary-color">{{ formatCurrency(selectedOrder.total) }}</span>
              </div>
            </div>
          </div>

          <!-- Order Actions -->
          <div class="flex flex-wrap gap-4 justify-end pt-6 border-t border-gray-100">
            <button 
              v-if="selectedOrder.statut === 'en_attente'" 
              @click="showConfirmModal('confirm', selectedOrder)"
              class="submit-btn"
            >
              Confirm Order
            </button>
            <button 
              v-if="selectedOrder.statut === 'confirmee'" 
              @click="showConfirmModal('ship', selectedOrder)"
              class="btn-gray"
            >
              Mark as Shipped
            </button>
            <button 
              v-if="selectedOrder.statut === 'en_livraison'" 
              @click="showConfirmModal('deliver', selectedOrder)"
              class="btn-degrade-orange"
            >
              Mark as Delivered
            </button>
            <button 
              v-if="['en_attente', 'confirmee'].includes(selectedOrder.statut)" 
              @click="showConfirmModal('cancel', selectedOrder)"
              class="btn-deconnexion"
            >
              Cancel Order
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- Notification Toast -->
    <div v-if="showNotification" :class="['fixed bottom-4 right-4 left-4 sm:left-auto z-50 text-white py-3 sm:py-4 px-4 sm:px-6 rounded-lg shadow-2xl transition-all duration-300 transform', getNotificationClass(notificationType)]">
      <div class="flex items-center gap-2 sm:gap-3">
        <component :is="getNotificationIcon(notificationType)" class="w-5 h-5 flex-shrink-0" />
        <div class="flex-1 min-w-0">
          <p class="font-semibold text-sm sm:text-base">{{ notificationTitle }}</p>
          <p class="text-xs sm:text-sm opacity-90">{{ notificationMessage }}</p>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted } from 'vue'
import Navbar from '../boutiques/Navbar.vue'
import axios from 'axios'
import jsPDF from 'jspdf'
import * as XLSX from 'xlsx'
import { useRouter } from 'vue-router'

// Icons
import { 
  Home as HomeIcon,
  Download as DownloadIcon,
  ChevronDown as ChevronDownIcon,
  FileText as FileTextIcon,
  ShoppingCart as ShoppingCartIcon,
  Eye as EyeIcon,
  X as XIcon,
  RefreshCcw as RefreshIcon,
  AlertCircle as WarningIcon,
  Clock,
  CheckCircle2 as CheckCircle2Icon,
  BadgeDollarSign as BadgeEuroIcon,
  Search,
  Calendar,
  User,
  PhoneCall,
  Building,
  Truck,
  MapPin,
  Check,
  Archive as ArchiveIcon,
  BookmarkX as BookmarkXIcon,
  ChevronLeft,
  ChevronRight,
  FileCheck,
  Banknote,
  Box as BoxIcon,
  User2,
  Home,
  Mail,
  Bell,
  FileDown as FileDownIcon,
  Calculator as CalculatorIcon,
  CheckCircle,
  Image as ImageIcon,
  CheckCircle as CheckCircleIcon,
  CreditCard as CreditCardIcon,
  X
} from 'lucide-vue-next'

const router = useRouter()

// API Base URL
const API_BASE_URL = 'https://sastock.com/api_adjame'

// Reactive data
const dataLoading = ref(true)
const estimatedDeliveryDate = ref('')
const orders = ref([])
const selectedOrder = ref(null)
const showOrderModal = ref(false)
const showConfirmationModal = ref(false)
const confirmationAction = ref(null)
const hasError = ref(false)
const error = ref('')

// Search and filters
const searchQuery = ref('')
const statusFilter = ref('')
const dateFilter = ref('')
const sortBy = ref('date_desc')

// Pagination
const currentPage = ref(1)
const itemsPerPage = ref(10)

// Notifications
const showNotification = ref(false)
const notificationType = ref('success')
const notificationTitle = ref('')
const notificationMessage = ref('')

//  Payment proof modals
const showPaymentProofModal = ref(false)
const showProofImageModal = ref(false)
const showValidateProofModal = ref(false)
const showValidateProofModal2 = ref(false)
const showValidateprepareModal = ref(false)
const showValidatedeliveredModal = ref(false)
const currentProofOrder = ref(null)
const currentValidateOrder = ref(null)
const currentProofImage = ref(null)
const currentValidatePayment = ref(null)
const validationComment = ref('')

// Stats
const stats = ref({
  total_orders: 0,
  pending_orders: 0,
  confirmed_orders: 0,
  daily_revenue: 0,
  orders_growth: 0,
  pending_reduction: 0,
  confirmed_growth: 0,
  revenue_growth: 0
})

const filterTabs = [
  { value: 'all', label: 'All' },
  { value: 'en_attente', label: 'Pending' },
  { value: 'confirmee', label: 'Confirmed' },
  { value: 'en_livraison', label: 'Available for delivery' },
  { value: 'livree', label: 'Deliveries' },
  { value: 'annulee', label: 'Cancelled' }
]

const activeFilter = ref('all')
const showExportDropdown = ref(false)

// Computed
const filterCounts = computed(() => {
  if (!orders.value) return { all: 0, en_attente: 0, confirmee: 0, en_livraison: 0, livree: 0, annulee: 0 }

  return {
    'all': orders.value.length,
    'en_attente': orders.value.filter(order => order.statut === 'en_attente').length,
    'confirmee': orders.value.filter(order => order.statut === 'confirmee').length,
    'en_livraison': orders.value.filter(order => order.statut === 'en_livraison').length,
    'livree': orders.value.filter(order => order.statut === 'livree').length,
    'annulee': orders.value.filter(order => order.statut === 'annulee').length
  }
})

const calculateTotalPaid = (order) => {
  if (!order) return 0;

  // Paiement initial 30%
  let total = order.total * 0.3;

  // Paiements additionnels filtrés
  if (order.paiements_additionnels && order.paiements_additionnels.length > 0) {
    total += order.paiements_additionnels
      .filter(payment => payment.valide === 'valid') // 👈 filtre ici
      .reduce((sum, payment) => sum + parseFloat(payment.montant || 0), 0);
  }

  return total;
};

const calculateRemaining = (order) => {
  if (!order) return 0
  return order.total - calculateTotalPaid(order)
}

const showPaymentProof = (order) => {
  currentProofOrder.value = order
  showOrderModal.value = false
  showPaymentProofModal.value = true

}

const closePaymentProofModal = () => {
  showPaymentProofModal.value = false
  currentProofOrder.value = null
}

const filteredOrders = computed(() => {
  let filtered = [...orders.value]

  if (searchQuery.value) {
    const query = searchQuery.value.toLowerCase()
    filtered = filtered.filter(order => 
      order.numero_commande.toLowerCase().includes(query) ||
      order.client_nom.toLowerCase().includes(query) ||
      order.client_telephone.includes(query) ||
      order.produit_nom.toLowerCase().includes(query) ||
      (order.boutique_nom && order.boutique_nom.toLowerCase().includes(query))
    )
  }

  if (statusFilter.value) {
    filtered = filtered.filter(order => order.statut === statusFilter.value)
  } else if (activeFilter.value !== 'all') {
    filtered = filtered.filter(order => order.statut === activeFilter.value)
  }

  if (dateFilter.value) {
    const now = new Date()
    const today = new Date(now.getFullYear(), now.getMonth(), now.getDate())
    
    filtered = filtered.filter(order => {
      const orderDate = new Date(order.date_commande)
      
      switch (dateFilter.value) {
        case 'today':
          return orderDate >= today
        case 'yesterday':
          const yesterday = new Date(today)
          yesterday.setDate(yesterday.getDate() - 1)
          return orderDate >= yesterday && orderDate < today
        case 'week':
          const weekAgo = new Date(today)
          weekAgo.setDate(weekAgo.getDate() - 7)
          return orderDate >= weekAgo
        case 'month':
          const monthAgo = new Date(today)
          monthAgo.setMonth(monthAgo.getMonth() - 1)
          return orderDate >= monthAgo
        default:
          return true
      }
    })
  }

  filtered.sort((a, b) => {
    switch (sortBy.value) {
      case 'date_desc':
        return new Date(b.date_commande) - new Date(a.date_commande)
      case 'date_asc':
        return new Date(a.date_commande) - new Date(b.date_commande)
      case 'amount_desc':
        return b.total - a.total
      case 'amount_asc':
        return a.total - b.total
      default:
        return 0
    }
  })

  return filtered
})

const paginatedOrders = computed(() => {
  const start = (currentPage.value - 1) * itemsPerPage.value
  const end = start + itemsPerPage.value
  return filteredOrders.value.slice(start, end)
})

const totalPages = computed(() => {
  return Math.ceil(filteredOrders.value.length / itemsPerPage.value)
})

const hasActiveFilters = computed(() => {
  return searchQuery.value || statusFilter.value || dateFilter.value || sortBy.value !== 'date_desc'
})

const isFirstPage = computed(() => currentPage.value <= 1)
const isLastPage = computed(() => currentPage.value >= totalPages.value)

// Methods
const handleFilterChange = (filterValue) => {
  activeFilter.value = filterValue
  currentPage.value = 1
}

const handleItemsPerPageChange = () => {
  currentPage.value = 1
}

const handleSearch = () => {
  currentPage.value = 1
}

const clearFilters = () => {
  searchQuery.value = ''
  statusFilter.value = ''
  dateFilter.value = ''
  sortBy.value = 'date_desc'
  currentPage.value = 1
}

const changePage = (page) => {
  if (page >= 1 && page <= totalPages.value) {
    currentPage.value = page
  }
}

const getVisiblePages = () => {
  const pages = []
  const maxVisible = 5
  let start = Math.max(1, currentPage.value - Math.floor(maxVisible / 2))
  let end = Math.min(totalPages.value, start + maxVisible - 1)
  
  if (end - start + 1 < maxVisible) {
    start = Math.max(1, end - maxVisible + 1)
  }
  
  for (let i = start; i <= end; i++) {
    pages.push(i)
  }
  
  return pages
}

//  Payment proof methods
const viewProofImage = (imageUrl) => {
  currentProofImage.value = imageUrl
  showProofImageModal.value = true
}

const closeProofImageModal = () => {
  showProofImageModal.value = false
  currentProofImage.value = null
}

const openValidateProofModal = (order) => {
  currentValidateOrder.value = order
  validationComment.value = ''
  showValidateProofModal.value = true
  showPaymentProofModal.value = false
}

const openValidateProofModal2 = (payment) => {
  console.log('Opening validate proof modal for payment:', payment)
  currentValidatePayment.value = payment
  validationComment.value = ''
  showValidateProofModal2.value = true
  showPaymentProofModal.value = false
}

const openValidatePrepareModal = (order) => {
  currentValidateOrder.value = order
  validationComment.value = ''
  showValidateprepareModal.value = true
  showPaymentProofModal.value = false
}

const openValidateDeliveredModal = (order) => {
  currentValidateOrder.value = order
  validationComment.value = ''
  showValidatedeliveredModal.value = true
  showPaymentProofModal.value = false
}

const closeValidateProofModal = () => {
  showValidateProofModal.value = false
  currentValidateOrder.value = null
  validationComment.value = ''
}

const closeValidateprepareModal = () => {
  showValidateprepareModal.value = false
  currentValidateOrder.value = null
  validationComment.value = ''
  estimatedDeliveryDate.value = ''
}

const validatePaymentProof = async () => {
  if (!estimatedDeliveryDate.value) {
    showNotificationMessage('error', 'Error', 'Please enter an estimated delivery date')
    return
  }
  if (!currentValidateOrder.value) return

  try {
    const response = await axios.put(
      `${API_BASE_URL}/commandes.php?action=validate_proof&id=${currentValidateOrder.value.id}`,
      {
        commentaire: validationComment.value,
        date_livraison_estimee: estimatedDeliveryDate.value
      }
    )
    
    if (response.data.success) {
      showNotificationMessage('success', 'Proof Validated', 'Payment proof has been successfully validated.')
      
      // Update order in list
      const orderIndex = orders.value.findIndex(o => o.id === currentValidateOrder.value.id)
      if (orderIndex !== -1) {
        orders.value[orderIndex].preuve_validee = true
        orders.value[orderIndex].commentaire_validation = validationComment.value
      }
      
      // Update selected order if open
      if (selectedOrder.value && selectedOrder.value.id === currentValidateOrder.value.id) {
        selectedOrder.value.preuve_validee = true
        selectedOrder.value.commentaire_validation = validationComment.value
      }
      
      closeValidateProofModal()
    } else {
      showNotificationMessage('error', 'Error', response.data.error || 'Error validating proof')
    }
  } catch (error) {
    console.error('Error validating proof:', error)
    showNotificationMessage('error', 'Error', 'Error validating payment proof')
  }
}

const validatePaymentProof2 = async () => {
  if (!currentValidatePayment.value) return

  try {
    const response = await axios.put(
      `${API_BASE_URL}/commandes.php?action=validate_other_proof&id=${currentValidatePayment.value.id}`,
      {
        commentaire_admin: validationComment.value
      }
    )
    
    if (response.data.success) {
      showNotificationMessage('success', 'Proof Validated', 'Payment proof has been successfully validated.')
      
      showValidateProofModal2.value = false
      loadAllData()
    } else {
      showNotificationMessage('error', 'Error', response.data.error || 'Error validating proof')
    }
  } catch (error) {
    console.error('Error validating proof:', error)
    showNotificationMessage('error', 'Error', 'Error validating payment proof')
  }
}

const validatePreparation = async () => {
  if (!currentValidateOrder.value) return

  try {
    const response = await axios.put(
      `${API_BASE_URL}/commandes.php?action=validate_prepare&id=${currentValidateOrder.value.id}`,
      {
        commentaire: validationComment.value
      }
    )
    
    if (response.data.success) {
      showNotificationMessage('success', 'Prepare Validated', 'Payment proof has been successfully validated.')
      
      // Update order in list
      loadAllData()
      
      closeValidateprepareModal()
    } else {
      showNotificationMessage('error', 'Error', response.data.error || 'Error validating proof')
    }
  } catch (error) {
    console.error('Error validating proof:', error)
    showNotificationMessage('error', 'Error', 'Error validating payment proof')
  }
}

const validateDelivered = async () => {
  if (!currentValidateOrder.value) return

  try {
    const response = await axios.put(
      `${API_BASE_URL}/commandes.php?action=validate_delivered&id=${currentValidateOrder.value.id}`,
      {
        commentaire_delivered: validationComment.value
      }
    )
    
    if (response.data.success) {
      showNotificationMessage('success', 'Delivered Validated', 'The order has been successfully delivered..')
      
      
      showValidatedeliveredModal.value = false
      loadAllData()
    } else {
      showNotificationMessage('error', 'Error', response.data.error || 'Error validating proof')
    }
  } catch (error) {
    console.error('Error validating proof:', error)
    showNotificationMessage('error', 'Error', 'Error validating payment proof')
  }
}

const handleImageError = (event) => {
  event.target.src = '/placeholder.svg?height=64&width=64'
}

// API Methods
const loadStats = async () => {
  try {
    const response = await axios.get(`${API_BASE_URL}/commandes.php?action=stats&_=${Date.now()}`)
    
    if (response.data.success) {
      stats.value = response.data.data
    }
  } catch (error) {
    console.error('Error loading stats:', error)
  }
}

const loadOrders = async () => {
  try {
    const response = await axios.get(`${API_BASE_URL}/commandes.php?action=list&_=${Date.now()}`)
    
    if (response.data.success) {
      orders.value = response.data.data
      hasError.value = false
    } else {
      hasError.value = true
      error.value = response.data.error || 'Error loading orders'
    }
  } catch (err) {
    console.error('Error loading orders:', err)
    hasError.value = true
    error.value = 'Connection error. Please try again.'
  }
}

const loadAllData = async () => {
  dataLoading.value = true
  try {
    await Promise.all([loadStats(), loadOrders()])
  } finally {
    dataLoading.value = false
  }
}

// Confirmation Modal Methods
const showConfirmModal = (action, order) => {
  confirmationAction.value = action
  selectedOrder.value = order
  showConfirmationModal.value = true
  showOrderModal.value = false
}

const closeConfirmModal = () => {
  showConfirmationModal.value = false
  confirmationAction.value = null
}

const getConfirmationTitle = () => {
  switch (confirmationAction.value) {
    case 'confirm': return 'Confirm Order'
    case 'ship': return 'Ship Order'
    case 'deliver': return 'Deliver Order'
    case 'cancel': return 'Cancel Order'
    default: return ''
  }
}

const getConfirmationMessage = () => {
  switch (confirmationAction.value) {
    case 'confirm': return 'Are you sure you want to confirm this order?'
    case 'ship': return 'Are you sure you want to mark this order as shipped?'
    case 'deliver': return 'Are you sure you want to mark this order as delivered?'
    case 'cancel': return 'Are you sure you want to cancel this order? This action is irreversible.'
    default: return ''
  }
}

const getConfirmationIconClass = () => {
  switch (confirmationAction.value) {
    case 'confirm': return 'bg-orange-100 primary-color'
    case 'ship': return 'bg-orange-100 primary-color'
    case 'deliver': return 'bg-purple-100 text-purple-600'
    case 'cancel': return 'bg-red-100 error-color'
    default: return 'bg-gray-100 text-gray-600'
  }
}

const getConfirmationButtonClass = () => {
  switch (confirmationAction.value) {
    case 'confirm': return 'btn-degrade-orange'
    case 'ship': return 'btn-degrade-orange'
    case 'deliver': return 'btn-degrade-orange'
    case 'cancel': return 'btn-deconnexion'
    default: return 'bg-gray-600 hover:bg-gray-700'
  }
}

const getConfirmationButtonText = () => {
  switch (confirmationAction.value) {
    case 'confirm': return 'Confirm'
    case 'ship': return 'Ship'
    case 'deliver': return 'Deliver'
    case 'cancel': return 'Cancel Order'
    default: return 'Confirm'
  }
}

const executeAction = async () => {
  if (!selectedOrder.value || !confirmationAction.value) return

  try {
    switch (confirmationAction.value) {
      case 'confirm':
        await confirmOrder(selectedOrder.value.id)
        break
      case 'ship':
        await markAsShipped(selectedOrder.value.id)
        break
      case 'deliver':
        await markAsDelivered(selectedOrder.value.id)
        break
      case 'cancel':
        await cancelOrder(selectedOrder.value.id)
        break
    }
    
    closeConfirmModal()
  } catch (error) {
    showNotificationMessage('error', 'Error', 'Error updating order')
  }
}

// Order Actions
const confirmOrder = async (orderId) => {
  try {
    const response = await axios.put(`${API_BASE_URL}/commandes.php?action=confirm&id=${orderId}&_=${Date.now()}`)
    
    if (response.data.success) {
      showNotificationMessage('success', 'Order Confirmed', 'Order has been successfully confirmed.')
      await loadAllData()
    } else {
      showNotificationMessage('error', 'Error', response.data.error || 'Error confirming order')
    }
  } catch (error) {
    showNotificationMessage('error', 'Error', 'Error confirming order')
  }
}

const confirmPaiement = async (orderId, commentaire) => {
  try {
    const payload = {
      id: orderId,
      commentaire: commentaire
    }

    const response = await axios.put(
      `${API_BASE_URL}/commandes.php?action=confirm&_=${Date.now()}`,
      payload,
      {
        headers: {
          'Content-Type': 'application/json'
        }
      }
    )

    if (response.data.success) {
      showNotificationMessage('success', 'Paiement confirmé', 'Le paiement a été confirmé avec succès.')
      await loadAllData()
    } else {
      showNotificationMessage('error', 'Erreur', response.data.error || 'Erreur lors de la confirmation du paiement.')
    }
  } catch (error) {
    console.error(error)
    showNotificationMessage('error', 'Erreur', 'Une erreur est survenue lors de la confirmation.')
  }
}

const markAsShipped = async (orderId) => {
  try {
    const response = await axios.put(`${API_BASE_URL}/commandes.php?action=ship&id=${orderId}&_=${Date.now()}`)
    
    if (response.data.success) {
      showNotificationMessage('success', 'Order Shipped', 'Order has been marked as shipped.')
      await loadAllData()
    } else {
      showNotificationMessage('error', 'Error', response.data.error || 'Error shipping order')
    }
  } catch (error) {
    showNotificationMessage('error', 'Error', 'Error shipping order')
  }
}

const markAsDelivered = async (orderId) => {
  try {
    const response = await axios.put(`${API_BASE_URL}/commandes.php?action=deliver&id=${orderId}&_=${Date.now()}`)
    
    if (response.data.success) {
      showNotificationMessage('success', 'Order Delivered', 'Order has been marked as delivered.')
      await loadAllData()
    } else {
      showNotificationMessage('error', 'Error', response.data.error || 'Error delivering order')
    }
  } catch (error) {
    showNotificationMessage('error', 'Error', 'Error delivering order')
  }
}

const cancelOrder = async (orderId) => {
  try {
    const response = await axios.put(`${API_BASE_URL}/commandes.php?action=cancel&id=${orderId}&_=${Date.now()}`)
    
    if (response.data.success) {
      showNotificationMessage('success', 'Order Cancelled', 'Order has been successfully cancelled.')
      await loadAllData()
    } else {
      showNotificationMessage('error', 'Error', response.data.error || 'Error cancelling order')
    }
  } catch (error) {
    showNotificationMessage('error', 'Error', 'Error cancelling order')
  }
}

// UI Methods
const openOrderDetails = (order) => {
  selectedOrder.value = order
  showOrderModal.value = true
}

const closeOrderModal = () => {
  showOrderModal.value = false
}

// Export Methods
const exportToPDF = () => {
  try {
    showNotificationMessage('info', 'Export PDF', 'Generating PDF file...')
    showExportDropdown.value = false
    
    const doc = new jsPDF()
    
    doc.setFontSize(20)
    doc.text('Order Management', 14, 22)
    
    doc.setFontSize(10)
    doc.text(`Generated: ${new Date().toLocaleString()}`, 14, 30)
    doc.text(`Total Orders: ${filteredOrders.value.length}`, 14, 36)
    
    const tableData = filteredOrders.value.map(order => [
      order.numero_commande,
      formatDate(order.date_commande),
      order.client_nom,
      order.produit_nom,
      formatCurrency(order.total),
      getStatusLabel(order.statut)
    ])
    
    doc.autoTable({
      startY: 45,
      head: [['Order #', 'Date', 'Customer', 'Product', 'Total', 'Status']],
      body: tableData,
      theme: 'grid',
      headStyles: { fillColor: [249, 115, 22] },
      styles: { fontSize: 8 },
      columnStyles: {
        0: { cellWidth: 30 },
        1: { cellWidth: 25 },
        2: { cellWidth: 30 },
        3: { cellWidth: 40 },
        4: { cellWidth: 25 },
        5: { cellWidth: 25 }
      }
    })
    
    doc.save(`orders_${Date.now()}.pdf`)
    showNotificationMessage('success', 'Export Successful', 'PDF file generated successfully!')
  } catch (error) {
    console.error('PDF export error:', error)
    showNotificationMessage('error', 'Export Error', 'Error generating PDF file')
  }
}

const exportToExcel = () => {
  try {
    showNotificationMessage('info', 'Export Excel', 'Generating Excel file...')
    showExportDropdown.value = false
    
    const excelData = filteredOrders.value.map(order => ({
      'Order Number': order.numero_commande,
      'Date': formatDate(order.date_commande),
      'Time': formatTime(order.date_commande),
      'Customer': order.client_nom,
      'Phone': order.client_telephone,
      'Product': order.produit_nom,
      'Quantity': order.quantite,
      'Unit Price': order.produit_prix,
      'Subtotal': order.sous_total,
      'Delivery Fee': order.frais_livraison,
      'Total': order.total,
      'Status': getStatusLabel(order.statut),
      'Delivery Type': getDeliveryTypeLabel(order.type_livraison),
      'Municipality': order.commune || order.ville,
      'Address': order.adresse_complete,
      'Store': order.boutique_name || order.boutique_nom
    }))

    const wb = XLSX.utils.book_new()
    const ws = XLSX.utils.json_to_sheet(excelData)
    
    const colWidths = [
      { wch: 15 }, { wch: 12 }, { wch: 8 }, { wch: 20 }, { wch: 15 },
      { wch: 30 }, { wch: 8 }, { wch: 12 }, { wch: 12 }, { wch: 12 },
      { wch: 12 }, { wch: 12 }, { wch: 15 }, { wch: 20 }, { wch: 30 }, { wch: 20 }
    ]
    ws['!cols'] = colWidths
    
    XLSX.utils.book_append_sheet(wb, ws, 'Orders')
    XLSX.writeFile(wb, `orders_${Date.now()}.xlsx`)
    
    showNotificationMessage('success', 'Export Successful', 'Excel file generated successfully!')
  } catch (error) {
    console.error('Excel export error:', error)
    showNotificationMessage('error', 'Export Error', 'Error generating Excel file')
  }
}

// Helper Functions
const formatCurrency = (value) => {
  return new Intl.NumberFormat('fr-FR', {
    style: 'currency',
    currency: 'USD',
    minimumFractionDigits: 0
  }).format(value)
}

const formatDate = (dateString) => {
  const date = new Date(dateString)
  return new Intl.DateTimeFormat('fr-FR', {
    year: 'numeric',
    month: 'short',
    day: 'numeric'
  }).format(date)
}

const formatTime = (dateString) => {
  const date = new Date(dateString)
  return new Intl.DateTimeFormat('fr-FR', {
    hour: '2-digit',
    minute: '2-digit'
  }).format(date)
}

const getStatusLabel = (status) => {
  const labels = {
    'en_attente': 'Pending',
    'confirmee': 'Confirmed',
    'en_livraison': 'In Delivery',
    'livree': 'Delivered',
    'annulee': 'Cancelled'
  }
  return labels[status] || status
}

const getStatusClass = (status) => {
  const classes = {
    'en_attente': 'bg-yellow-100 text-yellow-800',
    'confirmee': 'bg-green-100 text-green-800',
    'en_livraison': 'bg-blue-100 text-blue-800',
    'livree': 'bg-purple-100 text-purple-800',
    'annulee': 'bg-red-100 text-red-800'
  }
  return classes[status] || 'bg-gray-100 text-gray-800'
}

const getStatusIcon = (status) => {
  const icons = {
    'en_attente': Clock,
    'confirmee': CheckCircle2Icon,
    'en_livraison': Truck,
    'livree': CheckCircle,
    'annulee': XIcon
  }
  return icons[status] || Clock
}

const getDeliveryTypeLabel = (type) => {
  const labels = {
    'abidjan': 'Abidjan',
    'interieur': 'Interior',
    'pickup': 'Pickup'
  }
  return labels[type] || type
}

const getBoutiqueStatusLabel = (status) => {
  const labels = {
    'active': 'Active',
    'pending': 'Pending',
    'suspended': 'Suspended'
  }
  return labels[status] || status
}

const getBoutiqueStatusClass = (status) => {
  const classes = {
    'active': 'bg-green-100 text-green-800',
    'pending': 'bg-yellow-100 text-yellow-800',
    'suspended': 'bg-red-100 text-red-800'
  }
  return classes[status] || 'bg-gray-100 text-gray-800'
}

const getNotificationClass = (type) => {
  const classes = {
    'success': 'bg-gradient-to-r from-green-500 to-green-600',
    'error': 'bg-gradient-to-r from-red-500 to-red-600',
    'info': 'bg-gradient-to-r from-blue-500 to-blue-600',
    'warning': 'bg-gradient-to-r from-yellow-500 to-yellow-600'
  }
  return classes[type] || 'bg-gradient-to-r from-gray-500 to-gray-600'
}

const getNotificationIcon = (type) => {
  const icons = {
    'success': CheckCircle,
    'error': XIcon,
    'info': Bell,
    'warning': WarningIcon
  }
  return icons[type] || Bell
}

const showNotificationMessage = (type, title, message) => {
  notificationType.value = type
  notificationTitle.value = title
  notificationMessage.value = message
  showNotification.value = true
  
  setTimeout(() => {
    showNotification.value = false
  }, 5000)
}

// Boutique Actions
const callBoutique = (phone) => {
  window.location.href = `tel:${phone}`
}

const emailBoutique = (email) => {
  window.location.href = `mailto:${email}`
}

const notifyBoutiqueNewOrder = (order) => {
  showNotificationMessage('info', 'Notification', 'Store notification sent successfully')
}

const generateReceiptPDF = (order) => {
  try {
    const doc = new jsPDF()
    
    doc.setFontSize(18)
    doc.text('Order Receipt', 14, 22)
    
    doc.setFontSize(12)
    doc.text(`Order #: ${order.numero_commande}`, 14, 35)
    doc.text(`Date: ${formatDate(order.date_commande)}`, 14, 42)
    doc.text(`Customer: ${order.client_nom}`, 14, 49)
    doc.text(`Phone: ${order.client_telephone}`, 14, 56)
    
    doc.text(`Product: ${order.produit_nom}`, 14, 70)
    doc.text(`Quantity: ${order.quantite}`, 14, 77)
    doc.text(`Unit Price: ${formatCurrency(order.produit_prix)}`, 14, 84)
    
    doc.text(`Subtotal: ${formatCurrency(order.sous_total)}`, 14, 98)
    doc.text(`Delivery: ${formatCurrency(order.frais_livraison)}`, 14, 105)
    doc.setFontSize(14)
    doc.text(`Total: ${formatCurrency(order.total)}`, 14, 115)
    
    doc.save(`receipt_${order.numero_commande}.pdf`)
    showNotificationMessage('success', 'Receipt Generated', 'PDF receipt generated successfully')
  } catch (error) {
    showNotificationMessage('error', 'Error', 'Error generating receipt')
  }
}
const handleClickOutside = (event) => {
  if (exportDropdownRef.value && !exportDropdownRef.value.contains(event.target)) {
    showExportDropdown.value = false
  }
}

// Lifecycle
onMounted(() => {
  loadAllData()
  document.addEventListener('click', handleClickOutside)
})


onUnmounted(() => {
  document.removeEventListener('click', handleClickOutside)
})
</script>

<style scoped>
/* Animations */
@keyframes float-slow {
  0%, 100% {
    transform: translateY(0px) translateX(0px);
  }
  50% {
    transform: translateY(-20px) translateX(10px);
  }
}

@keyframes float-reverse {
  0%, 100% {
    transform: translateY(0px) translateX(0px);
  }
  50% {
    transform: translateY(15px) translateX(-15px);
  }
}

@keyframes float-diagonal {
  0%, 100% {
    transform: translateY(0px) translateX(0px);
  }
  50% {
    transform: translateY(-15px) translateX(-10px);
  }
}

@keyframes float-slow-reverse {
  0%, 100% {
    transform: translateY(0px) translateX(0px);
  }
  50% {
    transform: translateY(10px) translateX(15px);
  }
}

@keyframes pulse-slow {
  0%, 100% {
    opacity: 0.6;
    transform: scale(1);
  }
  50% {
    opacity: 1;
    transform: scale(1.1);
  }
}

@keyframes pulse-delayed {
  0%, 100% {
    opacity: 0.5;
    transform: scale(1);
  }
  50% {
    opacity: 0.9;
    transform: scale(1.15);
  }
}

@keyframes pulse-delayed-2 {
  0%, 100% {
    opacity: 0.4;
    transform: scale(1);
  }
  50% {
    opacity: 0.8;
    transform: scale(1.2);
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

/* Scrollbar */
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
</style>