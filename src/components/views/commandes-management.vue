<template>
  <div class="min-h-screen bg-gray-50 relative overflow-hidden">
    <!-- Background animations -->
    <div class="absolute inset-0 overflow-hidden pointer-events-none z-0">
      <div class="absolute inset-0 bg-gradient-to-br from-gray-50 via-white to-gray-100"></div>
      <div class="absolute -top-20 -left-20 w-96 h-96 bg-gradient-to-br from-orange-200/60 to-orange-300/40 rounded-full blur-3xl animate-float-slow"></div>
      <div class="absolute top-1/4 -right-20 w-80 h-80 bg-gradient-to-br from-blue-200/50 to-indigo-200/35 rounded-full blur-3xl animate-float-reverse"></div>
      <div class="absolute -bottom-20 left-1/3 w-72 h-72 bg-gradient-to-br from-purple-200/45 to-pink-200/35 rounded-full blur-3xl animate-float-diagonal"></div>
      <div class="absolute top-1/2 right-1/4 w-64 h-64 bg-gradient-to-br from-emerald-200/40 to-teal-200/30 rounded-full blur-3xl animate-float-slow-reverse"></div>
    </div>

    <!-- Container responsive -->
    <div class="w-full max-w-[1650px] mx-auto px-4 sm:px-6 py-4 sm:py-8 relative z-10">
      <Navbar/>
      <!-- Breadcrumb -->
      <div class="flex items-center text-sm text-gray-500 mb-4 sm:mb-6">
        <a href="/" class="hover:text-gray-700">
          <HomeIcon class="w-4 h-4 sm:w-5 sm:h-5" />
        </a>
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
              class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-3 sm:px-4 py-2 rounded-lg shadow-sm text-sm font-medium bg-gradient-to-r from-orange-500 to-orange-600 text-white hover:from-orange-600 hover:to-orange-700 transition-all"
            >
              <DownloadIcon class="w-4 h-4" />
              <span>Export</span>
              <ChevronDownIcon class="w-4 h-4" />
            </button>
            <div v-if="showExportDropdown" class="origin-top-right absolute right-0 w-50 mt-2 ring-1 ring-gray-400 rounded-md shadow-lg bg-white p-2 z-50">
              <div role="menu">
                <button @click="exportToPDF" class="flex items-center gap-2 text-sm mb-2 text-gray-700 hover:bg-gray-100 w-full px-3 py-2 rounded-lg" role="menuitem">
                  <FileTextIcon class="w-4 h-4 text-red-500" />
                  Export to PDF
                </button>
                <button @click="exportToExcel" class="flex items-center gap-2 text-sm text-gray-700 hover:bg-gray-100 w-full px-3 py-2 rounded-lg" role="menuitem">
                  <FileTextIcon class="w-4 h-4 text-green-500" />
                  Export to Excel
                </button>
              </div>
            </div>
          </div>
          <button 
            @click="loadAllData"
            class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-3 sm:px-4 py-2 rounded-lg shadow-sm text-sm font-medium bg-gray-900 text-white hover:bg-gray-800 transition-all"
          >
            <RefreshIcon class="w-4 h-4" />
            Refresh
          </button>
        </div>
      </div>

      <!-- Loading State -->
      <div v-if="dataLoading" class="mb-6 sm:mb-8">
        <div class="bg-white rounded-lg p-6 sm:p-8 text-center shadow-lg">
          <div class="inline-block animate-spin rounded-full h-10 w-10 sm:h-12 sm:w-12 border-4 border-orange-500 border-t-transparent"></div>
          <p class="mt-4 text-gray-600 font-medium text-sm sm:text-base">Loading orders...</p>
        </div>
      </div>

      <!-- Error State -->
      <div v-if="hasError && !dataLoading" class="mb-6 sm:mb-8">
        <div class="bg-red-50 border-l-4 border-red-400 text-red-700 p-4 rounded-lg shadow-lg flex items-center gap-3">
          <AlertCircle class="w-6 h-6 flex-shrink-0" />
          <div>
            <p class="font-semibold">Error loading data</p>
            <p>{{ errorMessage }}</p>
          </div>
        </div>
      </div>

      <!-- Stats Cards -->
      <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-3 sm:gap-4 mb-6 sm:mb-8" v-if="!dataLoading && !hasError">
        <div 
          v-for="stat in statsCards" 
          :key="stat.key"
          @click="handleFilterChange(stat.filterValue)"
          :class="[
            'bg-white overflow-hidden shadow-lg rounded-lg border cursor-pointer transition-all duration-300 hover:shadow-xl hover:scale-[1.02]',
            activeFilter === stat.filterValue ? 'border-orange-400 ring-2 ring-orange-200' : 'border-gray-100'
          ]"
        >
          <div class="px-3 sm:px-4 py-3 sm:py-4">
            <div class="flex items-center mb-2">
              <div :class="['w-8 h-8 sm:w-10 sm:h-10 rounded-lg flex items-center justify-center mr-2 sm:mr-3 flex-shrink-0', stat.bgColor]">
                <component :is="stat.icon" class="w-4 h-4 sm:w-5 sm:h-5 text-gray-800" />
              </div>
              <div class="min-w-0">
                <p class="text-[10px] sm:text-xs text-gray-600 truncate">{{ stat.label }}</p>
                <p class="text-lg sm:text-xl font-bold text-gray-900">{{ stat.value }}</p>
              </div>
            </div>
            <div class="flex items-center gap-1">
              <span :class="['text-[10px] sm:text-xs font-medium', stat.changeColor]">{{ stat.emoji }}</span>
              <span class="text-[10px] sm:text-xs text-gray-500 truncate">{{ stat.description }}</span>
            </div>
          </div>
        </div>
      </div>

      <!-- Order Lifecycle Progress -->
      <div class="bg-white rounded-xl shadow-lg p-4 sm:p-6 mb-6 border border-gray-100" v-if="!dataLoading && !hasError">
        <h3 class="text-sm font-semibold text-gray-700 mb-4">Order Lifecycle Pipeline</h3>
        <div class="flex flex-wrap items-center justify-between gap-2">
          <div 
            v-for="(stage, index) in lifecycleStages" 
            :key="stage.key"
            @click="handleFilterChange(stage.filterValue)"
            :class="[
              'flex-1 min-w-[100px] p-3 rounded-xl cursor-pointer transition-all border-2',
              activeFilter === stage.filterValue 
                ? 'border-orange-400 bg-orange-50' 
                : 'border-transparent bg-gray-50 hover:bg-gray-100'
            ]"
          >
            <div class="flex items-center gap-2 mb-2">
              <span class="text-xl">{{ stage.emoji }}</span>
              <span class="text-xs font-medium text-gray-700">{{ stage.label }}</span>
            </div>
            <div class="text-2xl font-bold text-gray-900">{{ stage.count }}</div>
            <div class="text-[10px] text-gray-500">{{ stage.description }}</div>
          </div>
        </div>
      </div>

      <!-- Main Content Card -->
      <div class="bg-white shadow-lg rounded-xl overflow-hidden border border-gray-100" v-if="!dataLoading && !hasError">
        <!-- Filter Tabs -->
        <div class="px-4 sm:px-6 py-3 sm:py-4 border-b border-gray-200 bg-gray-50">
          <div class="flex flex-wrap gap-1 sm:gap-2">
            <button 
              v-for="tab in filterTabs" 
              :key="tab.value"
              @click="handleFilterChange(tab.value)"
              :class="[
                'px-2 sm:px-4 py-1.5 sm:py-2 rounded-lg transition-all duration-200 text-xs sm:text-sm font-medium inline-flex items-center gap-1',
                activeFilter === tab.value 
                  ? 'bg-gradient-to-r from-orange-500 to-orange-600 text-white shadow-md' 
                  : 'bg-white text-gray-700 hover:bg-gray-100 border border-gray-200'
              ]"
            >
              <span>{{ tab.emoji }}</span>
              <span class="hidden sm:inline">{{ tab.label }}</span>
              <span class="bg-white/20 px-1.5 py-0.5 rounded-full text-[10px]">{{ filterCounts[tab.value] }}</span>
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
                class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-transparent text-sm" 
                placeholder="Search by number, customer, product..."
                @input="handleSearch"
              >
            </div>
            <div class="flex items-center gap-2 sm:gap-3">
              <select 
                v-model="deliveryTypeFilter" 
                @change="loadOrders" 
                class="px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 text-sm"
              >
                <option value="">All Delivery Types</option>
                <option value="FOB">FOB Delivery</option>
                <option value="FOB">CIF Delivery</option>
              </select>
              <select 
                v-model="dateFilter" 
                @change="loadOrders" 
                class="px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 text-sm"
              >
                <option value="">All dates</option>
                <option value="today">Today</option>
                <option value="week">This Week</option>
                <option value="month">This month</option>
              </select>
              <button v-if="hasActiveFilters" @click="clearFilters" class="px-3 py-2 bg-red-500 text-white rounded-lg text-xs font-medium hover:bg-red-600 transition-colors">
                <X class="w-4 h-4" />
              </button>
            </div>
          </div>
        </div>

        <!-- Orders Table -->
        <div class="overflow-x-auto">
          <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
              <tr>
                <th scope="col" class="px-3 sm:px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Order</th>
                <th scope="col" class="px-3 sm:px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Customer</th>
                <th scope="col" class="hidden md:table-cell px-3 sm:px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Product</th>
                <th scope="col" class="px-3 sm:px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Delivery</th>
                <th scope="col" class="px-3 sm:px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Amount</th>
                <th scope="col" class="px-3 sm:px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Status</th>
                <th scope="col" class="px-3 sm:px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Progress</th>
                <th scope="col" class="px-3 sm:px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Actions</th>
              </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
              <tr
                v-for="order in paginatedOrders"
                :key="order.id"
                :class="[
                  'hover:bg-orange-50/30 transition-colors',
                  order.statut === 'confirmee' && order.ready_for_shipping && calculateRemaining(order) <= 0 ? 'border-l-4 border-l-green-500 bg-green-50/20' : ''
                ]"
              >
                <!-- Order Info -->
                <td class="px-3 sm:px-4 py-3 sm:py-4 whitespace-nowrap">
                  <div class="flex items-center gap-3">
                    <div class="w-12 h-12 rounded-lg overflow-hidden shadow-sm border border-gray-200 flex-shrink-0">
                      <img 
                        v-if="order.produit_image" 
                        :src="order.produit_image" 
                        :alt="order.produit_nom"
                        class="w-full h-full object-cover"
                      />
                      <div v-else class="w-full h-full bg-gray-100 flex items-center justify-center">
                        <BoxIcon class="w-6 h-6 text-gray-400" />
                      </div>
                    </div>
                    <div>
                      <div class="text-sm font-semibold text-gray-900">{{ order.numero_commande }}</div>
                      <div class="flex items-center text-xs text-gray-500">
                        <Calendar class="h-3 w-3 mr-1 text-orange-500" />
                        {{ formatDate(order.date_commande || order.created_at) }}
                      </div>
                    </div>
                  </div>
                </td>

                <!-- Customer -->
                <td class="px-3 sm:px-4 py-3 sm:py-4 whitespace-nowrap">
                  <div class="space-y-1">
                    <div class="flex items-center text-sm font-medium text-gray-900">
                      <User class="h-3 w-3 mr-1 text-orange-500" />
                      {{ order.client_nom }}
                    </div>
                    <div class="flex items-center text-xs text-gray-500">
                      <PhoneCall class="h-3 w-3 mr-1 text-orange-500" />
                      {{ order.client_telephone }}
                    </div>
                  </div>
                </td>

                <!-- Product -->
                <td class="hidden md:table-cell px-3 sm:px-4 py-3 sm:py-4 whitespace-nowrap">
                  <div class="space-y-1">
                    <div class="text-sm font-medium text-gray-900 truncate max-w-[150px]" :title="order.produit_nom">{{ order.produit_nom }}</div>
                    <div class="flex items-center gap-2 text-xs text-gray-500">
                      <span>Qty: {{ order.quantite }}</span>
                      <span class="text-orange-600 font-medium">{{ formatCurrency(order.produit_prix) }}</span>
                    </div>
                  </div>
                </td>

                <!-- Delivery Type -->
                <td class="px-3 sm:px-4 py-3 sm:py-4 whitespace-nowrap">
                  <div class="space-y-1">
                    <span :class="[
                      'inline-flex items-center gap-1 px-2 py-1 rounded-full text-xs font-medium',
                      order.delivery_method === 'FOB' ? 'bg-blue-100 text-blue-800' : 'bg-green-100 text-green-800'
                    ]">
                      <Ship v-if="order.delivery_method === 'FOB'" class="w-3 h-3" />
                      <Truck v-else class="w-3 h-3" />
                      {{ order.delivery_method || 'FOB' }}
                    </span>
                    <div class="flex items-center text-xs text-gray-500">
                      <MapPin class="h-3 w-3 mr-1 text-orange-500" />
                      {{ order.destination_port || order.adresse_complete || 'N/A' }}
                    </div>
                  </div>
                </td>

                <!-- Amount -->
                <td class="px-3 sm:px-4 py-3 sm:py-4 whitespace-nowrap">
                  <div class="space-y-1">
                    <div class="text-sm font-bold text-gray-900">{{ formatCurrency(order.total) }}</div>
                    <div class="text-xs text-gray-500">
                      <span :class="getPaymentStatusClass(order)">
                        {{ getPaymentStatusLabel(order) }}
                      </span>
                    </div>
                  </div>
                </td>

                <!-- Status -->
                <td class="px-3 sm:px-4 py-3 sm:py-4 whitespace-nowrap">
                  <div class="space-y-1">
                    <span :class="['inline-flex items-center gap-1 px-2 py-1 rounded-full text-xs font-medium', getStatusClass(order.statut)]">
                      <span>{{ getStatusEmoji(order.statut) }}</span>
                      {{ getStatusLabel(order.statut) }}
                    </span>
                    <div v-if="order.lifecycle_stage" class="text-[10px] text-gray-500">
                      Stage: {{ order.lifecycle_stage }}
                    </div>
                    <!-- PDF Download Buttons for 'send' status -->
                    <div v-if="order.statut === 'send'" class="flex gap-1 mt-2 flex-wrap">
                      <button
                        @click="handleProformaDownload(order)"
                        class="px-2 py-1 bg-blue-100 text-blue-700 rounded text-[10px] font-medium hover:bg-blue-200 transition-colors flex items-center gap-1"
                        title="Download Proforma PDF"
                      >
                        <FileTextIcon class="w-3 h-3" />
                        Proforma PDF
                      </button>
                      <button
                        @click="handleContractDownload(order)"
                        class="px-2 py-1 bg-green-100 text-green-700 rounded text-[10px] font-medium hover:bg-green-200 transition-colors flex items-center gap-1"
                        title="Download Contract PDF"
                      >
                        <FileTextIcon class="w-3 h-3" />
                        Contract PDF
                      </button>
                    </div>
                  </div>
                </td>

                <!-- Progress / Lifecycle Actions -->
                <td class="px-3 sm:px-4 py-3 sm:py-4 whitespace-nowrap">
                  <div class="space-y-2">
                    <!-- Pending Stage Actions -->
                    <!-- Pending Stage - Awaiting Contract -->
                    <div v-if="order.statut === 'en_attente'" class="space-y-1">
                      <div class="flex items-center gap-1 text-xs text-yellow-600">
                        <Clock class="w-3 h-3" />
                        <span>Awaiting Contract</span>
                      </div>
                      <div class="flex gap-1 flex-wrap">
                        <button
                          @click="openDocumentModal(order, 'proforma')"
                          class="px-2 py-1 bg-orange-100 text-orange-700 rounded text-[10px] font-medium hover:bg-orange-200 transition-colors"
                        >
                          Send Proforma
                        </button>
                        <button
                          @click="openChatWithClient(order)"
                          class="px-2 py-1 bg-blue-100 text-blue-700 rounded text-[10px] font-medium hover:bg-blue-200 transition-colors"
                          title="Chat with client"
                        >
                          💬 Chat
                        </button>
                      </div>
                    </div>

                    <!-- Contract Sent - Awaiting Initial Payment -->
                    <div v-else-if="order.statut === 'send'" class="space-y-1">
                      <div class="flex items-center gap-1 text-xs text-blue-600">
                        <FileText class="w-3 h-3" />
                        <span>Contract Sent - Awaiting Payment</span>
                      </div>
                      <div class="flex gap-1 flex-wrap">
                        <button
                          @click="showPaymentProof(order)"
                          class="px-2 py-1 bg-purple-100 text-purple-700 rounded text-[10px] font-medium hover:bg-purple-200 transition-colors"
                        >
                          View Payments
                        </button>
                        <button
                          @click="openChatWithClient(order)"
                          class="px-2 py-1 bg-blue-100 text-blue-700 rounded text-[10px] font-medium hover:bg-blue-200 transition-colors"
                          title="Chat with client"
                        >
                          💬 Chat
                        </button>
                      </div>
                    </div>

                    <!-- Confirmed Stage - In Preparation -->
                    <div v-else-if="order.statut === 'confirmee' && !order.ready_for_shipping" class="space-y-1">
                      <div class="flex items-center gap-1 text-xs text-blue-600">
                        <Settings class="w-3 h-3 animate-spin" />
                        <span>In Preparation</span>
                      </div>
                      <div class="flex gap-1 flex-wrap">
                        <button
                          @click="showConfirmModal('ready', order)"
                          class="px-2 py-1 bg-blue-100 text-blue-700 rounded text-[10px] font-medium hover:bg-blue-200 transition-colors"
                        >
                          Mark Ready
                        </button>
                        <!-- Added button to view/add payment proofs -->
                        <button
                          @click="showPaymentProof(order)"
                          class="px-2 py-1 bg-purple-100 text-purple-700 rounded text-[10px] font-medium hover:bg-purple-200 transition-colors"
                        >
                          View Payments
                        </button>
                        <button
                          v-if="order.signature_method && order.signature_method.length >= 3"
                          @click="openUploadPaymentModal(order)"
                          class="px-2 py-1 bg-green-100 text-green-700 rounded text-[10px] font-medium hover:bg-green-200 transition-colors"
                        >
                          Add Proof
                        </button>
                      </div>
                    </div>

                    <!-- Ready for Shipping -->
                    <div v-else-if="order.statut === 'confirmee' && order.ready_for_shipping" class="space-y-1">
                      <div class="flex items-center gap-1 text-xs text-green-600">
                        <PackageCheck class="w-3 h-3" />
                        <span>Ready for Shipping</span>
                      </div>
                      <div class="flex gap-1 flex-wrap">
                        <!-- Updated payment buttons for ready stage -->
                        <button
                          @click="showPaymentProof(order)"
                          class="px-2 py-1 bg-purple-100 text-purple-700 rounded text-[10px] font-medium hover:bg-purple-200 transition-colors"
                        >
                          View Payments
                        </button>
                        <button
                          v-if="calculateRemaining(order) > 0"
                          @click="openUploadPaymentModal(order)"
                          class="px-2 py-1 bg-yellow-100 text-yellow-700 rounded text-[10px] font-medium hover:bg-yellow-200 transition-colors"
                        >
                          Add Payment
                        </button>
                        <!-- Only show Start Shipping if payment is complete -->
                        <button
                          v-if="calculateRemaining(order) <= 0"
                          @click="openTrackingModal(order)"
                          class="px-2 py-1 bg-green-100 text-green-700 rounded text-[10px] font-medium hover:bg-green-200 transition-colors"
                        >
                          Start Shipping
                        </button>
                      </div>
                    </div>

                    <!-- In Shipping -->
                    <div v-else-if="order.statut === 'en_livraison'" class="space-y-1">
                      <div class="flex items-center gap-1 text-xs text-purple-600">
                        <Truck class="w-3 h-3" />
                        <span>{{ order.delivery_method === 'FOB' ? 'In Transit' : 'Shipping' }}</span>
                      </div>
                      <div class="flex gap-1 flex-wrap">
                        <button 
                          v-if="order.delivery_method === 'FOB' && order.tracking_link"
                          @click="openTrackingModal(order)"
                          class="px-2 py-1 bg-blue-100 text-blue-700 rounded text-[10px] font-medium hover:bg-blue-200 transition-colors"
                        >
                          🔗 Track Vessel
                        </button>
                        <button
                          @click="openDeliveryModal(order)"
                          class="px-2 py-1 bg-green-100 text-green-700 rounded text-[10px] font-medium hover:bg-green-200 transition-colors"
                        >
                          ✅ Mark Delivered
                        </button>
                      </div>
                    </div>

                    <!-- Delivered/Completed -->
                    <div v-else-if="order.statut === 'livree'" class="space-y-1">
                      <div class="flex items-center gap-1 text-xs text-green-600">
                        <CheckCircle2Icon class="w-3 h-3" />
                        <span>Completed</span>
                      </div>
                      <span class="text-[10px] text-gray-500">{{ order.delivery_method === 'FOB' ? 'CIF Delivered' : 'FOB Transferred' }}</span>
                    </div>

                    <!-- Cancelled -->
                    <div v-else-if="order.statut === 'annulee'" class="space-y-1">
                      <div class="flex items-center gap-1 text-xs text-red-600">
                        <XCircle class="w-3 h-3" />
                        <span>Cancelled</span>
                      </div>
                      <span class="text-[10px] text-gray-500">{{ order.cancellation_reason || 'No reason provided' }}</span>
                    </div>
                  </div>
                </td>

                <!-- Actions -->
                <td class="px-3 sm:px-4 py-3 sm:py-4 whitespace-nowrap">
                  <div class="flex items-center gap-1">
                    <!-- View Details -->
                    <button 
                      @click="openOrderDetails(order)"
                      class="p-2 rounded-lg bg-gray-100 text-gray-600 hover:bg-gray-200 transition-colors"
                      title="View Details"
                    >
                      <EyeIcon class="h-4 w-4" />
                    </button>

                    <!-- Confirm (for pending) -->
                    <button 
                      v-if="order.statut === 'en_attente' || order.statut === 'send'"
                      @click="showConfirmModal('confirm', order)"
                      class="p-2 rounded-lg bg-green-100 text-green-600 hover:bg-green-200 transition-colors"
                      title="Confirm Order"
                    >
                      <Check class="h-4 w-4" />
                    </button>

                    <!-- Cancel (for pending and confirmed) -->
                    <button 
                      v-if="['en_attente', 'confirmee'].includes(order.statut)"
                      @click="openCancelModal(order)"
                      class="p-2 rounded-lg bg-red-100 text-red-600 hover:bg-red-200 transition-colors"
                      title="Cancel Order"
                    >
                      <X class="h-4 w-4" />
                    </button>

                    <!-- Payment Proof -->
                    <button 
                      v-if="order.statut === 'en_attente' || order.statut === 'confirmee' || order.statut === 'send'"
                      @click="showPaymentProof(order)"
                      class="p-2 rounded-lg bg-orange-100 text-orange-600 hover:bg-orange-200 transition-colors"
                      title="View Payment Proof"
                    >
                      <ImageIcon class="h-4 w-4" />
                    </button>

                    <!-- Vessel Tracking -->
                    <button 
                      v-if="order.delivery_method === 'FOB' && order.tracking_link"
                      @click="openTrackingModal(order)"
                      class="p-2 rounded-lg bg-blue-100 text-blue-600 hover:bg-blue-200 transition-colors"
                      title="View Tracking Info"
                    >
                      <Anchor class="h-4 w-4" />
                    </button>
                  </div>
                </td>
              </tr>

              <!-- Empty State -->
              <tr v-if="paginatedOrders.length === 0 && !dataLoading && !hasError">
                <td colspan="8" class="px-6 py-12 text-center">
                  <div class="flex flex-col items-center">
                    <PackageX class="w-16 h-16 text-gray-300 mb-4" />
                    <p class="text-gray-500 font-medium">No orders found</p>
                    <p class="text-gray-400 text-sm">Try adjusting your filters</p>
                  </div>
                </td>
              </tr>
            </tbody>
          </table>
        </div>

        <!-- Pagination -->
        <div class="bg-gray-50 px-4 sm:px-6 py-3 sm:py-4 flex flex-col sm:flex-row sm:items-center sm:justify-between border-t border-gray-200 gap-3 sm:gap-0">
          <div class="flex items-center space-x-2 sm:space-x-4 text-xs sm:text-sm">
            <span class="text-gray-700">Per page</span>
            <select 
              v-model="itemsPerPage" 
              @change="handleItemsPerPageChange"
              class="px-3 py-1.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 text-sm"
            >
              <option :value="10">10</option>
              <option :value="25">25</option>
              <option :value="50">50</option>
            </select>
            <span class="text-gray-700">
              {{ ((currentPage - 1) * itemsPerPage) + 1 }} - {{ Math.min(currentPage * itemsPerPage, filteredOrders.length) }} of {{ filteredOrders.length }}
            </span>
          </div>
          <div class="flex items-center space-x-2">
            <button 
              @click="changePage(currentPage - 1)" 
              :disabled="isFirstPage"
              class="p-2 rounded-lg bg-white border border-gray-200 disabled:opacity-50 disabled:cursor-not-allowed hover:bg-gray-50"
            >
              <ChevronLeft class="h-4 w-4" />
            </button>
            <button 
              v-for="page in getVisiblePages()" 
              :key="page"
              @click="changePage(page)"
              :class="[
                'px-3 py-1 rounded-lg text-sm font-medium transition-colors',
                currentPage === page ? 'bg-orange-500 text-white' : 'bg-white border border-gray-200 hover:bg-gray-50'
              ]"
            >
              {{ page }}
            </button>
            <button 
              @click="changePage(currentPage + 1)" 
              :disabled="isLastPage"
              class="p-2 rounded-lg bg-white border border-gray-200 disabled:opacity-50 disabled:cursor-not-allowed hover:bg-gray-50"
            >
              <ChevronRight class="h-4 w-4" />
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- Cancel Order Modal with Reasons -->
    <div v-if="showCancelModal" class="fixed inset-0 bg-black/60 backdrop-blur-sm flex items-center justify-center z-50 p-4" @click="closeCancelModal">
      <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md" @click.stop>
        <div class="px-6 py-4 border-b border-gray-100">
          <div class="flex items-center gap-3">
            <div class="p-2 bg-red-100 rounded-lg">
              <XCircle class="h-5 w-5 text-red-600" />
            </div>
            <div>
              <h3 class="text-lg font-bold text-gray-900">Cancel Order</h3>
              <p class="text-sm text-gray-500">Order #{{ selectedOrder?.numero_commande }}</p>
            </div>
          </div>
        </div>
        <div class="p-6 space-y-4">
          <p class="text-gray-600">Please select a reason for cancellation:</p>
          
          <div class="space-y-2">
            <label 
              v-for="reason in cancellationReasons" 
              :key="reason.value"
              class="flex items-center gap-3 p-3 border border-gray-200 rounded-lg cursor-pointer hover:bg-gray-50 transition-colors"
              :class="{ 'border-red-400 bg-red-50': selectedCancelReason === reason.value }"
            >
              <input 
                type="radio" 
                v-model="selectedCancelReason" 
                :value="reason.value"
                class="w-4 h-4 text-red-600 focus:ring-red-500"
              >
              <div>
                <span class="text-sm font-medium text-gray-900">{{ reason.emoji }} {{ reason.label }}</span>
                <p class="text-xs text-gray-500">{{ reason.description }}</p>
              </div>
            </label>
          </div>

          <div v-if="selectedCancelReason === 'other'">
            <label class="block text-sm font-medium text-gray-700 mb-2">Specify reason</label>
            <textarea 
              v-model="customCancelReason"
              rows="3"
              class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-transparent"
              placeholder="Enter the cancellation reason..."
            ></textarea>
          </div>

          <div class="flex justify-end gap-3 pt-4">
            <button 
              @click="closeCancelModal"
              class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg font-medium hover:bg-gray-200 transition-colors"
            >
              Cancel
            </button>
            <button 
              @click="confirmCancelOrder"
              :disabled="!selectedCancelReason || (selectedCancelReason === 'other' && !customCancelReason)"
              class="px-4 py-2 bg-red-600 text-white rounded-lg font-medium hover:bg-red-700 transition-colors disabled:opacity-50 disabled:cursor-not-allowed"
            >
              Confirm Cancellation
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- Proforma/Contract Document Modal -->
    <div v-if="showDocumentModal" class="fixed inset-0 bg-black/60 backdrop-blur-sm flex items-center justify-center z-50 p-4" @click="closeDocumentModal">
      <div class="bg-white rounded-2xl shadow-2xl w-full max-w-2xl max-h-[90vh] overflow-hidden" @click.stop>
        <div class="sticky top-0 bg-white border-b border-gray-100 px-6 py-4 flex items-center justify-between">
          <div class="flex items-center gap-3">
            <div class="p-2 bg-orange-100 rounded-lg">
              <FileTextIcon class="h-5 w-5 text-orange-600" />
            </div>
            <div>
              <h3 class="text-lg font-bold text-gray-900">{{ documentType === 'proforma' ? 'Proforma Invoice' : 'Contract' }}</h3>
              <p class="text-sm text-gray-500">Order #{{ selectedOrder?.numero_commande }}</p>
            </div>
          </div>
          <button @click="closeDocumentModal" class="p-2 hover:bg-gray-100 rounded-lg">
            <X class="h-5 w-5 text-gray-500" />
          </button>
        </div>
        
        <div class="p-6 overflow-y-auto max-h-[calc(90vh-80px)] space-y-6">
          <!-- Order Summary -->
          <div class="bg-gray-50 rounded-xl p-4 border border-gray-200">
            <h4 class="font-semibold text-gray-900 mb-3">Order Summary</h4>
            <div class="grid grid-cols-2 gap-4 text-sm">
              <div>
                <span class="text-gray-500">Customer:</span>
                <span class="font-medium text-gray-900 ml-2">{{ selectedOrder?.client_nom }}</span>
              </div>
              <div>
                <span class="text-gray-500">Product:</span>
                <span class="font-medium text-gray-900 ml-2">{{ selectedOrder?.produit_nom }}</span>
              </div>
              <div>
                <span class="text-gray-500">Quantity:</span>
                <span class="font-medium text-gray-900 ml-2">{{ selectedOrder?.quantite }}</span>
              </div>
              <div>
                <span class="text-gray-500">Total:</span>
                <span class="font-bold text-orange-600 ml-2">{{ formatCurrency(selectedOrder?.total) }}</span>
              </div>
            </div>
          </div>

          <!-- Editable Price -->
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Adjust Price (if needed)</label>
            <input 
              type="number" 
              v-model.number="editablePrice"
              class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500"
            >
          </div>

          <!-- Payment Terms -->
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Payment Terms (%)</label>
            <div class="space-y-3">
              <div class="grid grid-cols-3 gap-4">
                <div class="flex flex-col">
                  <label class="text-xs text-gray-500">Deposit</label>
                  <input 
                    type="number" 
                    style="background-color: gray; color: white"
                    v-model.number="paymentTerms.deposit"
                    min="0"
                    max="100"
                    class="px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500"
                  >
                </div>
                <div class="flex flex-col">
                  <label class="text-xs text-gray-500">Before Shipping</label>
                  <input 
                    type="number" 
                    v-model.number="paymentTerms.beforeShipping"
                    min="0"
                    max="100"
                    class="px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500"
                  >
                </div>
                <div class="flex flex-col">
                  <label class="text-xs text-gray-500">Against B/L</label>
                  <input 
                    type="number" 
                    v-model.number="paymentTerms.againstBL"
                    min="0"
                    max="100"
                    class="px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500"
                  >
                </div>
              </div>
              <p class="text-xs text-gray-500">
                Total: {{ paymentTerms.deposit + paymentTerms.beforeShipping + paymentTerms.againstBL }}% 
                <span v-if="paymentTerms.deposit + paymentTerms.beforeShipping + paymentTerms.againstBL !== 100" class="text-red-500">(Must equal 100%)</span>
              </p>
            </div>
          </div>

          <!-- Delivery Terms -->
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Delivery Method</label>
            <div class="grid grid-cols-2 gap-3">
              <label 
                class="flex items-center gap-3 p-4 border-2 rounded-xl cursor-pointer transition-all"
                :class="deliveryMethod === 'FOB' ? 'border-green-500 bg-green-50' : 'border-gray-200 hover:border-gray-300'"
              >
                <input type="radio" v-model="deliveryMethod" value="FOB" class="sr-only">
                <Truck class="w-6 h-6 text-green-600" />
                <div>
                  <span class="font-medium text-gray-900">FOB</span>
                  <p class="text-xs text-gray-500">Free On Board - Buyer takes responsibility at port</p>
                </div>
              </label>
              <label 
                class="flex items-center gap-3 p-4 border-2 rounded-xl cursor-pointer transition-all"
                :class="deliveryMethod === 'CIF' ? 'border-blue-500 bg-blue-50' : 'border-gray-200 hover:border-gray-300'"
              >
                <input type="radio" v-model="deliveryMethod" value="CIF" class="sr-only">
                <Ship class="w-6 h-6 text-blue-600" />
                <div>
                  <span class="font-medium text-gray-900">CIF</span>
                  <p class="text-xs text-gray-500">Cost, Insurance, Freight - Delivered to destination port</p>
                </div>
              </label>
            </div>
          </div>

          <!-- Destination & Loading Port -->
          <div class="grid grid-cols-2 gap-4">
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">
                {{ deliveryMethod === 'FOB' ? 'Destination Port' : 'Delivery Address/Port' }}
              </label>
              <input 
                type="text" 
                v-model="destinationAddress"
                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500"
                :placeholder="deliveryMethod === 'FOB' ? 'e.g., Port of Abidjan' : 'e.g., Customer warehouse address'"
              >
            </div>
            <div v-if="deliveryMethod === 'CIF'">
              <label class="block text-sm font-medium text-gray-700 mb-2">Loading Port</label>
              <input 
                type="text" 
                v-model="loadingPort"
                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500"
                placeholder="e.g., Tema Port"
              >
            </div>
          </div>
          <div v-if="deliveryMethod === 'CIF'">
            <label class="block text-sm font-medium text-gray-700 mb-2">CIF Shipping cost ($)</label>
            <input 
            style="background-color: #FFF3E0;"
              type="number" 
              v-model.number="shippingCost"
              class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500"
            >
          </div>

          <!-- Additional Notes -->
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Additional Terms & Conditions</label>
            <textarea 
              v-model="additionalTerms"
              rows="4"
              class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500"
              placeholder="Enter any additional terms..."
            ></textarea>
          </div>

          <!-- Signature Method -->
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Signature Method</label>
            <div class="grid grid-cols-2 gap-3">
              <label 
                class="flex items-center gap-3 p-3 border-2 rounded-xl cursor-pointer transition-all"
                :class="signatureMethod === 'online' ? 'border-orange-500 bg-orange-50' : 'border-gray-200'"
              >
                <input type="radio" v-model="signatureMethod" value="online" class="sr-only">
                <Globe class="w-5 h-5 text-orange-600" />
                <span class="text-sm font-medium">Online Signature</span>
              </label>
              
            </div>
          </div>

          <!-- Action Buttons -->
          <div class="flex gap-3 pt-4 border-t border-gray-100">
            <button 
              @click="closeDocumentModal"
              class="flex-1 px-4 py-2 bg-gray-100 text-gray-700 rounded-lg font-medium hover:bg-gray-200 transition-colors"
            >
              Cancel
            </button>
            <button 
              @click="generateAndSendDocument"
              :disabled="paymentTerms.deposit + paymentTerms.beforeShipping + paymentTerms.againstBL !== 100 || proformaLoading"
              class="flex-1 px-4 py-2 bg-gradient-to-r from-orange-500 to-orange-600 text-white rounded-lg font-medium hover:from-orange-600 hover:to-orange-700 transition-all disabled:opacity-50 disabled:cursor-not-allowed flex items-center justify-center"
            >
              <span v-if="proformaLoading" class="animate-spin h-4 w-4 border-2 border-white border-t-transparent rounded-full mr-2"></span>
              Generate & Send to Client
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- Tracking Modal for CIF -->
    <div v-if="showTrackingModal" class="fixed inset-0 bg-black/60 backdrop-blur-sm flex items-center justify-center z-50 p-4" @click="closeTrackingModal">
      <div class="bg-white rounded-2xl shadow-2xl w-full max-w-lg" @click.stop>
        <div class="px-6 py-4 border-b border-gray-100">
          <div class="flex items-center gap-3">
            <div class="p-2 bg-blue-100 rounded-lg">
              <Ship class="h-5 w-5 text-blue-600" />
            </div>
            <div>
              <h3 class="text-lg font-bold text-gray-900">Start Shipping</h3>
              <p class="text-sm text-gray-500">Enter shipping details for Order #{{ selectedOrder?.numero_commande }}</p>
            </div>
          </div>
        </div>
        <div class="p-6 space-y-4">
          <!-- Delivery Type Badge -->
          <div class="flex items-center gap-2 mb-4">
            <span class="px-3 py-1 rounded-full text-xs font-semibold"
              :class="selectedOrder?.delivery_method === 'CIF' ? 'bg-blue-100 text-blue-700' : 'bg-green-100 text-green-700'">
              {{ selectedOrder?.delivery_method || 'FOB' }} Delivery
            </span>
          </div>

          <!-- FOB: Simple delivery info -->
          <template v-if="selectedOrder?.delivery_method === 'FOB' || !selectedOrder?.delivery_method">
            <!-- Final Port/Address -->
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">Final Destination Port</label>
              <input
                type="text"
                v-model="destinationAddress"
                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500"
                placeholder="Enter destination port address"
              >
            </div>

            <!-- Delivery Document Upload -->
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">Delivery Document</label>
              <div class="border-2 border-dashed border-gray-300 rounded-xl p-6 text-center hover:border-green-400 transition-colors cursor-pointer" @click="blFileInput?.click()">
                <input type="file" @change="handleBLUpload" accept=".pdf,image/*" class="hidden" ref="blFileInput">
                <Upload class="w-8 h-8 text-gray-400 mx-auto mb-2" />
                <p class="text-sm text-gray-600">Click to upload delivery document</p>
                <p class="text-xs text-gray-400">PDF or Image files</p>
              </div>
              <div v-if="blFile" class="mt-2 flex items-center gap-2 text-sm text-green-600">
                <CheckCircle2Icon class="w-4 h-4" />
                <span>{{ blFile.name }}</span>
              </div>
            </div>
          </template>

          <!-- CIF: Full vessel tracking info -->
          <template v-else-if="selectedOrder?.delivery_method === 'CIF'">
            <!-- Vessel Tracking Link -->
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">
                <div class="flex items-center gap-2">
                  <Ship class="w-4 h-4" />
                  Vessel Tracking Link
                </div>
              </label>
              <input
                type="url"
                v-model="trackingLink"
                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                placeholder="https://www.marinetraffic.com/..."
              >
            </div>

            <!-- Vessel Name and IMO -->
            <div class="grid grid-cols-2 gap-4">
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Vessel Name</label>
                <input
                  type="text"
                  v-model="vesselName"
                  class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                  placeholder="Vessel name"
                >
              </div>
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">IMO Number</label>
                <input
                  type="text"
                  v-model="imoNumber"
                  class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                  placeholder="IMO number"
                >
              </div>
            </div>

            <!-- ETD and ETA -->
            <div class="grid grid-cols-2 gap-4">
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">ETD (Departure)</label>
                <input
                  type="date"
                  v-model="etd"
                  class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                >
              </div>
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">ETA (Estimated Arrival)</label>
                <input
                  type="date"
                  v-model="eta"
                  class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                >
              </div>
            </div>

            <!-- Bill of Lading Upload -->
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">Bill of Lading (B/L)</label>
              <div class="border-2 border-dashed border-gray-300 rounded-xl p-6 text-center hover:border-blue-400 transition-colors cursor-pointer" @click="blFileInput?.click()">
                <input type="file" @change="handleBLUpload" accept=".pdf,image/*" class="hidden" ref="blFileInput">
                <Upload class="w-8 h-8 text-gray-400 mx-auto mb-2" />
                <p class="text-sm text-gray-600">Click to upload B/L document</p>
                <p class="text-xs text-gray-400">PDF or Image files</p>
              </div>
              <div v-if="blFile" class="mt-2 flex items-center gap-2 text-sm text-blue-600">
                <CheckCircle2Icon class="w-4 h-4" />
                <span>{{ blFile.name }}</span>
              </div>
            </div>
          </template>

          <div class="flex gap-3 pt-4">
            <button 
              @click="closeTrackingModal"
              class="flex-1 px-4 py-2 bg-gray-100 text-gray-700 rounded-lg font-medium hover:bg-gray-200 transition-colors"
            >
              Close
            </button>
            <button
              @click="saveTrackingInfo"
              :disabled="trackingLoading"
              class="flex-1 px-4 py-2 bg-blue-600 text-white rounded-lg font-medium hover:bg-blue-700 transition-colors disabled:opacity-50 disabled:cursor-not-allowed flex items-center justify-center"
            >
              <span v-if="trackingLoading" class="animate-spin h-4 w-4 border-2 border-white border-t-transparent rounded-full mr-2"></span>
              Start Shipping
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- Payment Proof Modal -->
    <div v-if="showPaymentProofModal" class="fixed inset-0 bg-black/60 backdrop-blur-sm flex items-center justify-center z-50 p-4" @click="closePaymentProofModal">
      <div class="bg-white rounded-2xl shadow-2xl w-full max-w-4xl max-h-[90vh] overflow-hidden" @click.stop>
        <div class="sticky top-0 bg-white border-b border-gray-100 px-6 py-4 flex items-center justify-between">
          <div class="flex items-center gap-3">
            <div class="p-2 bg-green-100 rounded-lg">
              <CreditCard class="h-5 w-5 text-green-600" />
            </div>
            <div>
              <h3 class="text-lg font-bold text-gray-900">Payment History</h3>
              <p class="text-sm text-gray-500">Order #{{ currentProofOrder?.numero_commande }}</p>
            </div>
          </div>
          <button @click="closePaymentProofModal" class="p-2 hover:bg-gray-100 rounded-lg">
            <X class="h-5 w-5 text-gray-500" />
          </button>
        </div>

        <div class="p-6 overflow-y-auto max-h-[calc(90vh-80px)]">
          <!-- Payment Statistics -->
          <div class="grid grid-cols-3 gap-4 mb-6">
            <div class="bg-gradient-to-br from-orange-50 to-orange-100 rounded-xl p-4 border border-orange-200">
              <div class="text-sm text-orange-600 font-medium mb-1">Total Order</div>
              <div class="text-2xl font-bold text-orange-700">{{ formatCurrency(currentProofOrder?.total || 0) }}</div>
            </div>
            <div class="bg-gradient-to-br from-green-50 to-green-100 rounded-xl p-4 border border-green-200">
              <div class="text-sm text-green-600 font-medium mb-1">Total Paid</div>
              <div class="text-2xl font-bold text-green-700">{{ formatCurrency(calculateTotalPaid(currentProofOrder)) }}</div>
            </div>
            <div class="bg-gradient-to-br from-red-50 to-red-100 rounded-xl p-4 border border-red-200">
              <div class="text-sm text-red-600 font-medium mb-1">Remaining</div>
              <div class="text-2xl font-bold text-red-700">{{ formatCurrency(calculateRemaining(currentProofOrder)) }}</div>
            </div>
          </div>

          <!-- Payment Progress Bar -->
          <div class="mb-6">
            <div class="flex justify-between text-sm mb-2">
              <span class="text-gray-600">Payment Progress</span>
              <span class="font-medium text-gray-900">{{ getPaymentPercentage(currentProofOrder) }}%</span>
            </div>
            <div class="w-full bg-gray-200 rounded-full h-3">
              <div 
                class="bg-gradient-to-r from-green-500 to-green-600 h-3 rounded-full transition-all duration-500"
                :style="{ width: getPaymentPercentage(currentProofOrder) + '%' }"
              ></div>
            </div>
          </div>

          <!-- Added Add Payment Button for supplier to add proof -->
          <div class="mb-6 flex justify-end">
            <button 
              @click="openUploadPaymentModal(currentProofOrder)"
              class="inline-flex items-center gap-2 px-4 py-2 bg-gradient-to-r from-orange-500 to-orange-600 text-white rounded-lg font-medium hover:from-orange-600 hover:to-orange-700 transition-all"
            >
              <Upload class="h-4 w-4" />
              Add Payment Proof
            </button>
          </div>

          <!-- Payment Timeline -->
          <div class="space-y-4">
            <h4 class="font-semibold text-gray-900 mb-4">Payment Timeline </h4>
            
            <!-- Deposit Payment -->
            <div class="bg-white border border-gray-200 rounded-xl p-4 shadow-sm">
              <div class="flex items-start justify-between">
                <div class="flex items-center gap-3">
                  <div class="w-10 h-10 bg-orange-100 rounded-lg flex items-center justify-center">
                    <Wallet class="h-5 w-5 text-orange-600" />
                  </div>
                  <div>
                    <div class="font-semibold text-gray-900">Deposit Payment ({{ currentProofOrder.payment_terms.deposit_percent || 30 }}%)</div>
                    <div class="text-sm text-gray-500">{{ formatDate(currentProofOrder?.date_paiement) || 'Not yet paid' }}</div>
                    <!-- Show validation comment if exists -->
                    <div v-if="currentProofOrder?.commentaire_validation" class="text-xs text-gray-400 mt-1 italic">
                      "{{ currentProofOrder.commentaire_validation }}"
                    </div>
                  </div>
                </div>
                <div class="text-right">
                  <div class="text-lg font-bold text-gray-900">{{ formatCurrency((currentProofOrder?.total || 0) * ((currentProofOrder.payment_terms.deposit_percent || 30) / 100)) }}</div>
                  <span v-if="currentProofOrder?.deposit_validated === 'valid' || currentProofOrder?.tobevalidate === 'valid'" class="inline-flex items-center gap-1 px-2 py-1 bg-green-100 text-green-700 rounded-full text-xs font-medium">
                    <CheckCircle2Icon class="h-3 w-3" />
                    Validated
                  </span>
                  <span v-else-if="currentProofOrder?.preuve_paiement && (currentProofOrder?.deposit_validated === 'pending' || !currentProofOrder?.deposit_validated)" class="inline-flex items-center gap-1 px-2 py-1 bg-yellow-100 text-yellow-700 rounded-full text-xs font-medium">
                    <Clock class="h-3 w-3" />
                    Pending Validation
                  </span>
                  <span v-else-if="currentProofOrder?.deposit_validated === 'rejected'" class="inline-flex items-center gap-1 px-2 py-1 bg-red-100 text-red-700 rounded-full text-xs font-medium">
                    <XCircle class="h-3 w-3" />
                    Rejected
                  </span>
                  <span v-else class="inline-flex items-center gap-1 px-2 py-1 bg-gray-100 text-gray-500 rounded-full text-xs font-medium">
                    <Clock class="h-3 w-3" />
                    Awaiting Proof
                  </span>
                </div>
              </div>
              
              <!-- Actions for deposit payment -->
              <div v-if="currentProofOrder?.preuve_paiement" class="mt-3 flex flex-wrap gap-2">
                <button 
                  @click="viewProofImage(currentProofOrder.preuve_paiement)"
                  class="inline-flex items-center gap-2 px-3 py-1.5 bg-gray-100 text-gray-700 rounded-lg text-sm hover:bg-gray-200 transition-colors"
                >
                  <ImageIcon class="h-4 w-4" />
                  View Proof
                </button>
                <!-- Validate button -->
                <button 
                  v-if="currentProofOrder?.deposit_validated !== 'valid' && currentProofOrder?.tobevalidate !== 'valid'"
                  @click="openValidateModal(currentProofOrder, 'deposit')"
                  class="inline-flex items-center gap-2 px-3 py-1.5 bg-green-100 text-green-700 rounded-lg text-sm hover:bg-green-200 transition-colors"
                >
                  <Check class="h-4 w-4" />
                  Validate
                </button>
                <!-- Reject button -->
                <button 
                  v-if="currentProofOrder?.deposit_validated !== 'valid' && currentProofOrder?.tobevalidate !== 'valid' && currentProofOrder?.deposit_validated !== 'rejected'"
                  @click="openRejectModal(currentProofOrder, 'deposit')"
                  class="inline-flex items-center gap-2 px-3 py-1.5 bg-red-100 text-red-700 rounded-lg text-sm hover:bg-red-200 transition-colors"
                >
                  <XCircle class="h-4 w-4" />
                  Reject
                </button>
              </div>
              <!-- Show rejection reason if rejected -->
              <div v-if="currentProofOrder?.deposit_validated === 'rejected' && currentProofOrder?.rejection_reason" class="mt-3 p-3 bg-red-50 rounded-lg border border-red-200">
                <p class="text-sm text-red-700"><strong>Rejection reason:</strong> {{ currentProofOrder.rejection_reason }}</p>
              </div>
            </div>

            <!-- Additional Payments -->
            <div 
              v-for="(payment, index) in currentProofOrder?.paiements_additionnels" 
              :key="payment.id || index"
              class="bg-white border border-gray-200 rounded-xl p-4 shadow-sm"
            >
              <div class="flex items-start justify-between">
                <div class="flex items-center gap-3">
                  <div class="w-10 h-10 bg-purple-100 rounded-lg flex items-center justify-center">
                    <CreditCard class="h-5 w-5 text-purple-600" />
                  </div>
                  <div>
                    <div class="font-semibold text-gray-900">{{ payment.description || `Additional Payment #${index + 1}` }}</div>
                    <div class="text-sm text-gray-500">{{ formatDate(payment.date_paiement) }}</div>
                    <div v-if="payment.commentaire" class="text-xs text-gray-400 mt-1">{{ payment.commentaire }}</div>
                    <!-- Show admin comment if validated -->
                    <div v-if="payment.commentaire_admin" class="text-xs text-green-600 mt-1 italic">
                      Admin: "{{ payment.commentaire_admin }}"
                    </div>
                  </div>
                </div>
                <div class="text-right">
                  <div class="text-lg font-bold text-gray-900">{{ formatCurrency(payment.montant) }}</div>
                  <span v-if="payment.valide === 'valid'" class="inline-flex items-center gap-1 px-2 py-1 bg-green-100 text-green-700 rounded-full text-xs font-medium">
                    <CheckCircle2Icon class="h-3 w-3" />
                    Validated
                  </span>
                  <span v-else-if="payment.valide === 'pending' || !payment.valide" class="inline-flex items-center gap-1 px-2 py-1 bg-yellow-100 text-yellow-700 rounded-full text-xs font-medium">
                    <Clock class="h-3 w-3" />
                    Pending
                  </span>
                  <span v-else-if="payment.valide === 'rejected'" class="inline-flex items-center gap-1 px-2 py-1 bg-red-100 text-red-700 rounded-full text-xs font-medium">
                    <XCircle class="h-3 w-3" />
                    Rejected
                  </span>
                </div>
              </div>
              
              <!-- Actions for additional payments -->
              <div class="mt-3 flex flex-wrap gap-2">
                <button 
                  @click="viewProofImage(payment.preuve_url)"
                  class="inline-flex items-center gap-2 px-3 py-1.5 bg-gray-100 text-gray-700 rounded-lg text-sm hover:bg-gray-200 transition-colors"
                >
                  <ImageIcon class="h-4 w-4" />
                  View Proof
                </button>
                <!-- Validate button -->
                <button 
                  v-if="payment.valide !== 'valid'"
                  @click="openValidateModal(payment, 'additional')"
                  class="inline-flex items-center gap-2 px-3 py-1.5 bg-green-100 text-green-700 rounded-lg text-sm hover:bg-green-200 transition-colors"
                >
                  <Check class="h-4 w-4" />
                  Validate
                </button>
                <!-- Reject button -->
                <button 
                  v-if="payment.valide !== 'valid' && payment.valide !== 'rejected'"
                  @click="openRejectModal(payment, 'additional')"
                  class="inline-flex items-center gap-2 px-3 py-1.5 bg-red-100 text-red-700 rounded-lg text-sm hover:bg-red-200 transition-colors"
                >
                  <XCircle class="h-4 w-4" />
                  Reject
                </button>
              </div>
              <!-- Show rejection reason if rejected -->
              <div v-if="payment.valide === 'rejected' && payment.rejection_reason" class="mt-3 p-3 bg-red-50 rounded-lg border border-red-200">
                <p class="text-sm text-red-700"><strong>Rejection reason:</strong> {{ payment.rejection_reason }}</p>
              </div>
            </div>

            <!-- No payments message -->
            <div v-if="!currentProofOrder?.preuve_paiement && (!currentProofOrder?.paiements_additionnels || currentProofOrder?.paiements_additionnels.length === 0)" 
                 class="text-center py-8 text-gray-500 bg-gray-50 rounded-xl">
              <Wallet class="w-12 h-12 text-gray-300 mx-auto mb-3" />
              <p class="font-medium">No payment proofs yet</p>
              <p class="text-sm">Waiting for client to upload payment proof</p>
            </div>
          </div>

          <div class="mt-6 flex justify-end">
            <button 
              @click="closePaymentProofModal"
              class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg font-medium hover:bg-gray-200 transition-colors"
            >
              Close
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- Image Viewer Modal -->
    <div v-if="showProofImageModal" class="fixed inset-0 bg-black/80 backdrop-blur-sm flex items-center justify-center z-50 p-4" @click="closeProofImageModal">
      <div class="bg-white rounded-2xl shadow-2xl w-full max-w-3xl max-h-[90vh] overflow-hidden" @click.stop>
        <div class="sticky top-0 bg-white border-b border-gray-100 px-6 py-4 flex items-center justify-between">
          <h3 class="text-lg font-bold text-gray-900">Payment Proof</h3>
          <button @click="closeProofImageModal" class="p-2 hover:bg-gray-100 rounded-lg">
            <X class="h-5 w-5 text-gray-500" />
          </button>
        </div>
        <div class="p-6">
          <div class="bg-gray-50 rounded-xl overflow-auto max-h-[60vh]">
            <img 
              v-if="currentProofImage" 
              :src="currentProofImage" 
              alt="Payment Proof"
              class="w-full h-auto object-contain"
            />
          </div>
        </div>
      </div>
    </div>

    <!-- Confirmation Modal -->
    <div v-if="showConfirmationModal" class="fixed inset-0 bg-black/60 backdrop-blur-sm flex items-center justify-center z-50 p-4" @click="closeConfirmModal">
      <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md" @click.stop>
        <div class="p-6">
          <div class="flex items-center mb-4">
            <div :class="['w-12 h-12 rounded-full flex items-center justify-center mr-4', getConfirmationIconClass()]">
              <component :is="getConfirmationIcon()" class="h-6 w-6" />
            </div>
            <div>
              <h3 class="text-lg font-semibold text-gray-900">{{ getConfirmationTitle() }}</h3>
              <p class="text-sm text-gray-500">Order #{{ selectedOrder?.numero_commande }}</p>
            </div>
          </div>
          <p class="text-gray-600 mb-6">{{ getConfirmationMessage() }}</p>
          <div v-if="validatePaymentType === 'deposit' && getConfirmationTitle() !== 'Mark as Delivered'">
            <label class="block text-sm font-medium text-gray-700 mb-2">Estimated Delivery Date </label>
            <input
              type="date"
              v-model="estimatedDeliveryDate"
              class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500"
            />
          </div>
          <div class="flex justify-end space-x-3">
            <button 
              @click="closeConfirmModal"
              class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg font-medium hover:bg-gray-200 transition-colors"
            >
              Cancel
            </button>
            <button 
              @click="executeAction"
              :class="['px-4 py-2 rounded-lg font-medium transition-colors text-white', getConfirmationButtonClass()]"
            >
              {{ getConfirmationButtonText() }}
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- Ready Modal -->
    <div v-if="showReadyModal" class="fixed inset-0 bg-black/60 backdrop-blur-sm flex items-center justify-center z-50 p-4" @click="closeReadyModal">
      <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md" @click.stop>
        <div class="px-6 py-4 border-b border-gray-100">
          <div class="flex items-center gap-3">
            <div class="p-2 bg-blue-100 rounded-lg">
              <Settings class="h-5 w-5 text-blue-600" />
            </div>
            <div>
              <h3 class="text-lg font-bold text-gray-900">Mark Order as Ready</h3>
              <p class="text-sm text-gray-500">Order #{{ selectedOrder?.numero_commande }}</p>
            </div>
          </div>
        </div>
        <div class="p-6 space-y-4">
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Comments</label>
            <textarea 
              v-model="readyComment"
              rows="3"
              class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
              placeholder="Enter any relevant comments..."
            ></textarea>
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Inspection Notes (Optional)</label>
            <textarea 
              v-model="inspectionNotes"
              rows="2"
              class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
              placeholder="e.g., Product checked, packaging intact..."
            ></textarea>
          </div>
          <div class="flex justify-end space-x-3">
            <button 
              @click="closeReadyModal"
              class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg font-medium hover:bg-gray-200 transition-colors"
            >
              Cancel
            </button>
            <button 
              @click="markOrderReady(selectedOrder.id)"
              :disabled="readyLoading"
              class="px-4 py-2 bg-blue-600 text-white rounded-lg font-medium hover:bg-blue-700 transition-colors disabled:opacity-50 disabled:cursor-not-allowed flex items-center justify-center"
            >
               <span v-if="readyLoading" class="animate-spin h-4 w-4 border-2 border-white border-t-transparent rounded-full mr-2"></span>
              Mark Ready
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- Delivery Modal -->
    <div v-if="showDeliveryModal" class="fixed inset-0 bg-black/60 backdrop-blur-sm flex items-center justify-center z-50 p-4" @click="closeDeliveryModal">
      <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md" @click.stop>
        <div class="px-6 py-4 border-b border-gray-100">
          <div class="flex items-center gap-3">
            <div class="p-2 bg-green-100 rounded-lg">
              <CheckCircle2Icon class="h-5 w-5 text-green-600" />
            </div>
            <div>
              <h3 class="text-lg font-bold text-gray-900">Mark Order as Delivered</h3>
              <p class="text-sm text-gray-500">Order #{{ selectedOrder?.numero_commande }}</p>
            </div>
          </div>
        </div>
        <div class="p-6 space-y-4">
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Delivery Date *</label>
            <input
              type="date"
              v-model="deliveryDate"
              class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500"
              required
            />
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Completion Notes (Optional)</label>
            <textarea
              v-model="deliveryNotes"
              rows="3"
              class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500"
              placeholder="Enter delivery notes, receiver name, condition, etc..."
            ></textarea>
          </div>
          <div class="flex justify-end space-x-3">
            <button
              @click="closeDeliveryModal"
              class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg font-medium hover:bg-gray-200 transition-colors"
            >
              Cancel
            </button>
            <button
              @click="markDelivered"
              :disabled="deliveryLoading || !deliveryDate"
              class="px-4 py-2 bg-green-600 text-white rounded-lg font-medium hover:bg-green-700 transition-colors disabled:opacity-50 disabled:cursor-not-allowed flex items-center justify-center"
            >
              <span v-if="deliveryLoading" class="animate-spin h-4 w-4 border-2 border-white border-t-transparent rounded-full mr-2"></span>
              Mark Delivered
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- Order Details Modal -->
    <div v-if="showOrderModal" class="fixed inset-0 bg-black/60 backdrop-blur-sm flex items-center justify-center z-50 p-4" @click="closeOrderModal">
      <div class="bg-white rounded-2xl shadow-2xl w-full max-w-4xl max-h-[90vh] overflow-y-auto" @click.stop>
        <div class="sticky top-0 bg-white border-b border-gray-100 px-6 py-4 flex items-center justify-between z-10">
          <div class="flex items-center gap-3">
            <div class="p-2 bg-orange-100 rounded-lg">
              <FileTextIcon class="h-5 w-5 text-orange-600" />
            </div>
            <h3 class="text-lg font-bold text-gray-900">Order Details</h3>
          </div>
          <button @click="closeOrderModal" class="p-2 hover:bg-gray-100 rounded-lg">
            <X class="h-5 w-5 text-gray-500" />
          </button>
        </div>

        <div v-if="selectedOrder" class="p-6 space-y-6">
          <!-- Order Header -->
          <div class="bg-gradient-to-r from-orange-50 to-orange-100 rounded-xl p-6 border border-orange-200">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
              <div class="flex items-center gap-4">
                <div class="w-16 h-16 rounded-xl overflow-hidden shadow-lg border-2 border-white">
                  <img 
                    v-if="selectedOrder.produit_image" 
                    :src="selectedOrder.produit_image" 
                    :alt="selectedOrder.produit_nom"
                    class="w-full h-full object-cover"
                  />
                  <div v-else class="w-full h-full bg-gray-100 flex items-center justify-center">
                    <BoxIcon class="w-8 h-8 text-gray-400" />
                  </div>
                </div>
                <div>
                  <div class="text-2xl font-bold text-gray-900">{{ selectedOrder.numero_commande }}</div>
                  <div class="text-gray-600">{{ formatDate(selectedOrder.date_commande || selectedOrder.created_at) }}</div>
                </div>
              </div>
              <div class="flex flex-wrap gap-2">
                <span :class="['inline-flex items-center gap-1 px-3 py-1.5 rounded-full text-sm font-medium', getStatusClass(selectedOrder.statut)]">
                  <span>{{ getStatusEmoji(selectedOrder.statut) }}</span>
                  {{ getStatusLabel(selectedOrder.statut) }}
                </span>
                <span :class="[
                  'inline-flex items-center gap-1 px-3 py-1.5 rounded-full text-sm font-medium',
                  selectedOrder.delivery_method === 'FOB' ? 'bg-blue-100 text-blue-800' : 'bg-green-100 text-green-800'
                ]">
                  {{ selectedOrder.delivery_method === 'FOB' ? '🚢 CIF' : '🚚 FOB' }}
                </span>
              </div>
            </div>

            <!-- Quick Stats -->
            <div class="grid grid-cols-3 gap-4 mt-6">
              <div class="bg-white/70 rounded-lg p-3">
                <div class="text-sm text-gray-600">Total</div>
                <div class="text-xl font-bold text-orange-600">{{ formatCurrency(selectedOrder.total) }}</div>
              </div>
              <div class="bg-white/70 rounded-lg p-3">
                <div class="text-sm text-gray-600">Quantity</div>
                <div class="text-xl font-bold text-gray-900">{{ selectedOrder.quantite }}</div>
              </div>
              <div class="bg-white/70 rounded-lg p-3">
                <div class="text-sm text-gray-600">Paid</div>
                <div class="text-xl font-bold text-green-600">{{ formatCurrency(calculateTotalPaid(selectedOrder)) }}</div>
              </div>
            </div>
          </div>

          <!-- Customer & Delivery Info -->
          <div class="grid md:grid-cols-2 gap-6">
            <div class="bg-white rounded-xl p-6 border border-gray-200 shadow-sm">
              <h4 class="font-semibold text-gray-900 mb-4 flex items-center gap-2">
                <User class="w-5 h-5 text-orange-500" />
                Customer Information
              </h4>
              <div class="space-y-3">
                <div class="flex items-center gap-2 text-sm">
                  <User class="w-4 h-4 text-gray-400" />
                  <span class="text-gray-600">Name:</span>
                  <span class="font-medium text-gray-900">{{ selectedOrder.client_nom }}</span>
                </div>
                <div class="flex items-center gap-2 text-sm">
                  <PhoneCall class="w-4 h-4 text-gray-400" />
                  <span class="text-gray-600">Phone:</span>
                  <span class="font-medium text-gray-900">{{ selectedOrder.client_telephone }}</span>
                </div>
                <div v-if="selectedOrder.client_email" class="flex items-center gap-2 text-sm">
                  <Mail class="w-4 h-4 text-gray-400" />
                  <span class="text-gray-600">Email:</span>
                  <span class="font-medium text-gray-900">{{ selectedOrder.client_email }}</span>
                </div>
              </div>
            </div>

            <div class="bg-white rounded-xl p-6 border border-gray-200 shadow-sm">
              <h4 class="font-semibold text-gray-900 mb-4 flex items-center gap-2">
                <Truck class="w-5 h-5 text-orange-500" />
                Delivery Information
              </h4>
              <div class="space-y-3">
                <div class="flex items-center gap-2 text-sm">
                  <span class="text-gray-600">Method:</span>
                  <span class="font-medium text-gray-900">{{ selectedOrder.delivery_method || 'FOB' }}</span>
                </div>
                <div class="flex items-center gap-2 text-sm">
                  <MapPin class="w-4 h-4 text-gray-400" />
                  <span class="text-gray-600">Destination:</span>
                  <span class="font-medium text-gray-900">{{ selectedOrder.destination_port || selectedOrder.adresse_complete || 'N/A' }}</span>
                </div>
                 <div v-if="selectedOrder.loading_port" class="flex items-center gap-2 text-sm">
                  <Anchor class="w-4 h-4 text-gray-400" />
                  <span class="text-gray-600">Loading Port:</span>
                  <span class="font-medium text-gray-900">{{ selectedOrder.loading_port }}</span>
                </div>
                <div v-if="selectedOrder.tracking_link" class="flex items-center gap-2 text-sm">
                  <ExternalLink class="w-4 h-4 text-gray-400" />
                  <a :href="selectedOrder.tracking_link" target="_blank" class="text-blue-600 hover:underline">
                    Track Shipment
                  </a>
                </div>
              </div>
            </div>
          </div>

          <!-- Order Lifecycle Timeline -->
          <div class="bg-white rounded-xl p-6 border border-gray-200 shadow-sm">
            <h4 class="font-semibold text-gray-900 mb-4 flex items-center gap-2">
              <Clock class="w-5 h-5 text-orange-500" />
              Order Lifecycle
            </h4>
            <div class="flex items-center justify-between">
              <div 
                v-for="(stage, index) in orderLifecycleSteps" 
                :key="stage.key"
                class="flex-1 relative"
              >
                <div class="flex flex-col items-center">
                  <div :class="[
                    'w-10 h-10 rounded-full flex items-center justify-center text-lg z-10',
                    isStageComplete(selectedOrder, stage.key) ? 'bg-green-500 text-white' : 
                    isStageActive(selectedOrder, stage.key) ? 'bg-orange-500 text-white animate-pulse' : 
                    'bg-gray-200 text-gray-500'
                  ]">
                    {{ stage.emoji }}
                  </div>
                  <div class="text-xs text-center mt-2 font-medium" :class="isStageComplete(selectedOrder, stage.key) ? 'text-green-600' : 'text-gray-500'">
                    {{ stage.label }}
                  </div>
                </div>
                <div 
                  v-if="index < orderLifecycleSteps.length - 1"
                  :class="[
                    'absolute top-5 left-1/2 w-full h-0.5',
                    isStageComplete(selectedOrder, stage.key) ? 'bg-green-500' : 'bg-gray-200'
                  ]"
                ></div>
              </div>
            </div>
          </div>

          <!-- Action Buttons -->
          <div class="flex flex-wrap gap-3 justify-end pt-4 border-t border-gray-100">
            <button 
              v-if="selectedOrder.statut === 'en_attente'"
              @click="openDocumentModal(selectedOrder, 'proforma')"
              class="px-4 py-2 bg-orange-100 text-orange-700 rounded-lg font-medium hover:bg-orange-200 transition-colors inline-flex items-center gap-2"
            >
              <FileTextIcon class="w-4 h-4" />
              Send Proforma
            </button>
            <button 
              v-if="selectedOrder.statut === 'en_attente'"
              @click="showConfirmModal('confirm', selectedOrder)"
              class="px-4 py-2 bg-green-600 text-white rounded-lg font-medium hover:bg-green-700 transition-colors inline-flex items-center gap-2"
            >
              <Check class="w-4 h-4" />
              Confirm Order
            </button>
            <button
              v-if="selectedOrder.statut === 'confirmee' && !selectedOrder.ready_for_shipping"
              @click="showConfirmModal('ready', selectedOrder)"
              class="px-4 py-2 bg-blue-600 text-white rounded-lg font-medium hover:bg-blue-700 transition-colors inline-flex items-center gap-2"
            >
              <Settings class="w-4 h-4" />
              Mark Ready
            </button>
            <button
              v-if="selectedOrder.statut === 'confirmee' && selectedOrder.ready_for_shipping"
              @click="showConfirmModal('ship', selectedOrder)"
              class="px-4 py-2 bg-purple-600 text-white rounded-lg font-medium hover:bg-purple-700 transition-colors inline-flex items-center gap-2"
            >
              <Truck class="w-4 h-4" />
              Start Shipping
            </button>
            <button 
              v-if="selectedOrder.statut === 'en_livraison'"
              @click="showConfirmModal('deliver', selectedOrder)"
              class="px-4 py-2 bg-green-600 text-white rounded-lg font-medium hover:bg-green-700 transition-colors inline-flex items-center gap-2"
            >
              <CheckCircle2Icon class="w-4 h-4" />
              Mark Delivered
            </button>
            <button 
              v-if="['en_attente', 'confirmee'].includes(selectedOrder.statut)"
              @click="openCancelModal(selectedOrder)"
              class="px-4 py-2 bg-red-100 text-red-700 rounded-lg font-medium hover:bg-red-200 transition-colors inline-flex items-center gap-2"
            >
              <X class="w-4 h-4" />
              Cancel Order
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- Notification Toast -->
    <Transition
      enter-active-class="transform ease-out duration-300 transition"
      enter-from-class="translate-y-2 opacity-0 sm:translate-y-0 sm:translate-x-2"
      enter-to-class="translate-y-0 opacity-100 sm:translate-x-0"
      leave-active-class="transition ease-in duration-100"
      leave-from-class="opacity-100"
      leave-to-class="opacity-0"
    >
      <div 
        v-if="showNotification" 
        :class="['fixed bottom-4 right-4 z-50 text-white py-4 px-6 rounded-xl shadow-2xl max-w-md', getNotificationClass(notificationType)]"
      >
        <div class="flex items-center gap-3">
          <component :is="getNotificationIcon(notificationType)" class="w-6 h-6 flex-shrink-0" />
          <div>
            <p class="font-semibold">{{ notificationTitle }}</p>
            <p class="text-sm opacity-90">{{ notificationMessage }}</p>
          </div>
        </div>
      </div>
    </Transition>

    <!-- Upload Payment Proof Modal -->
    <div v-if="showUploadPaymentModal" class="fixed inset-0 bg-black/60 backdrop-blur-sm flex items-center justify-center z-50 p-4" @click="closeUploadPaymentModal">
      <div class="bg-white rounded-2xl shadow-2xl w-full max-w-lg max-h-[90vh] overflow-hidden" @click.stop>
        <div class="sticky top-0 bg-white border-b border-gray-100 px-6 py-4 flex items-center justify-between">
          <div class="flex items-center gap-3">
            <div class="p-2 bg-orange-100 rounded-lg">
              <Upload class="h-5 w-5 text-orange-600" />
            </div>
            <div>
              <h3 class="text-lg font-bold text-gray-900">Add Proof of Payment</h3>
              <p class="text-sm text-gray-500">Order #{{ selectedOrderForPayment?.numero_commande }}</p>
            </div>
          </div>
          <button @click="closeUploadPaymentModal" class="p-2 hover:bg-gray-100 rounded-lg">
            <X class="h-5 w-5 text-gray-500" />
          </button>
        </div>

        <div class="p-6 overflow-y-auto max-h-[calc(90vh-80px)]">
          <!-- Payment Info Box -->
          <div class="bg-gradient-to-br from-orange-50 to-orange-100 rounded-xl p-4 mb-6 border border-orange-200">
            <div class="grid grid-cols-2 gap-4">
              <div>
                <span class="text-xs text-orange-600 font-medium">Total Order</span>
                <div class="text-lg font-bold text-gray-900">{{ formatCurrency(selectedOrderForPayment?.total || 0) }}</div>
              </div>
              <div>
                <span class="text-xs text-orange-600 font-medium">Amount Due</span>
                <div class="text-lg font-bold text-orange-700">{{ formatCurrency(getAmountDue(selectedOrderForPayment)) }}</div>
              </div>
              <div>
                <span class="text-xs text-green-600 font-medium">Already Paid</span>
                <div class="text-lg font-bold text-green-700">{{ formatCurrency(calculateTotalPaid(selectedOrderForPayment)) }}</div>
              </div>
              <div>
                <span class="text-xs text-red-600 font-medium">Remaining</span>
                <div class="text-lg font-bold text-red-700">{{ formatCurrency(calculateRemaining(selectedOrderForPayment)) }}</div>
              </div>
            </div>
            <div class="mt-3">
              <div class="flex justify-between text-xs mb-1">
                <span class="text-gray-600">Payment Progress</span>
                <span class="font-medium">{{ getPaymentPercentage(selectedOrderForPayment) }}%</span>
              </div>
              <div class="w-full bg-white rounded-full h-2">
                <div 
                  class="bg-gradient-to-r from-green-500 to-green-600 h-2 rounded-full transition-all duration-500"
                  :style="{ width: getPaymentPercentage(selectedOrderForPayment) + '%' }"
                ></div>
              </div>
            </div>
          </div>

          <!-- Payment Type Selection -->
          <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700 mb-2">Payment Type</label>
            <div class="grid grid-cols-2 gap-2">
              <button
                @click="paymentType = 'deposit'"
                :class="[
                  'p-3 rounded-lg border-2 text-sm font-medium transition-all',
                  paymentType === 'deposit' 
                    ? 'border-orange-500 bg-orange-50 text-orange-700' 
                    : 'border-gray-200 hover:border-gray-300 text-gray-700'
                ]"
                :disabled="selectedOrderForPayment?.deposit_validated === 'valid'"
              >
                <Wallet class="w-5 h-5 mx-auto mb-1" />
                Deposit ({{ selectedOrderForPayment.payment_terms.deposit_percent || 30 }}%)
              </button>
              <button
                @click="paymentType = 'additional'"
                :class="[
                  'p-3 rounded-lg border-2 text-sm font-medium transition-all',
                  paymentType === 'additional' 
                    ? 'border-orange-500 bg-orange-50 text-orange-700' 
                    : 'border-gray-200 hover:border-gray-300 text-gray-700'
                ]"
              >
                <CreditCard class="w-5 h-5 mx-auto mb-1" />
                Additional Payment
              </button>
            </div>
          </div>

          <form @submit.prevent="submitPaymentProof" class="space-y-4">
            <!-- Payment Amount -->
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">
                Payment Amount <span class="text-red-500">*</span>
              </label>
              <div class="relative">
                <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-500">$</span>
                <input
                  type="number"
                  v-model="uploadPaymentAmount"
                  class="w-full pl-8 pr-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-transparent"
                  :placeholder="paymentType === 'deposit' ? 'Deposit amount' : 'Enter amount'"
                  :max="getAmountDue(selectedOrderForPayment)"
                  min="1"
                  step="0.01"
                  required
                />
              </div>
              <p v-if="paymentType === 'deposit'" class="text-xs text-gray-500 mt-1">
                Suggested: {{ formatCurrency((selectedOrderForPayment?.total || 0) * ((selectedOrderForPayment?.payment_deposit_percent || 30) / 100)) }}
              </p>
            </div>

            <!-- File Upload -->
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">
                Proof of Payment <span class="text-red-500">*</span>
              </label>
              <div 
                @click="$refs.paymentFileInput.click()"
                @dragover.prevent="isDragging = true"
                @dragleave="isDragging = false"
                @drop.prevent="handleFileDrop"
                :class="[
                  'border-2 border-dashed rounded-xl p-6 text-center cursor-pointer transition-all',
                  isDragging ? 'border-orange-500 bg-orange-50' : 'border-gray-300 hover:border-orange-400 hover:bg-orange-50/50'
                ]"
              >
                <input
                  type="file"
                  ref="paymentFileInput"
                  @change="handlePaymentFileSelect"
                  accept="image/*,.pdf"
                  class="hidden"
                />
                <div v-if="!uploadPaymentFile">
                  <Upload class="w-10 h-10 mx-auto text-gray-400 mb-2" />
                  <p class="text-sm text-gray-600">Click or drag & drop to upload</p>
                  <p class="text-xs text-gray-400 mt-1">JPG, PNG or PDF (Max 10MB)</p>
                </div>
                <div v-else class="flex items-center justify-center gap-3">
                  <FileText class="w-8 h-8 text-orange-500" />
                  <div class="text-left">
                    <p class="text-sm font-medium text-gray-900">{{ uploadPaymentFile.name }}</p>
                    <p class="text-xs text-gray-500">{{ formatFileSize(uploadPaymentFile.size) }}</p>
                  </div>
                  <button 
                    type="button"
                    @click.stop="uploadPaymentFile = null"
                    class="p-1 hover:bg-red-100 rounded-full"
                  >
                    <X class="w-4 h-4 text-red-500" />
                  </button>
                </div>
              </div>

              <!-- Upload Progress -->
              <div v-if="uploadingPayment" class="mt-3">
                <div class="flex justify-between text-xs mb-1">
                  <span class="text-gray-600">Uploading...</span>
                  <span class="font-medium text-orange-600">{{ uploadPaymentProgress }}%</span>
                </div>
                <div class="w-full bg-gray-200 rounded-full h-2">
                  <div 
                    class="bg-gradient-to-r from-orange-500 to-orange-600 h-2 rounded-full transition-all duration-300"
                    :style="{ width: uploadPaymentProgress + '%' }"
                  ></div>
                </div>
              </div>
            </div>

            <!-- Comment -->
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">Comment (Optional)</label>
              <textarea
                v-model="uploadPaymentComment"
                rows="3"
                class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-transparent resize-none"
                placeholder="Add a note about this payment..."
              ></textarea>
            </div>

            <!-- Actions -->
            <div class="flex gap-3 pt-4">
              <button 
                type="button"
                @click="closeUploadPaymentModal"
                class="flex-1 px-4 py-2.5 bg-gray-100 text-gray-700 rounded-lg font-medium hover:bg-gray-200 transition-colors"
                :disabled="uploadingPayment"
              >
                Cancel
              </button>
              <button 
                type="submit"
                class="flex-1 px-4 py-2.5 bg-gradient-to-r from-orange-500 to-orange-600 text-white rounded-lg font-medium hover:from-orange-600 hover:to-orange-700 transition-all disabled:opacity-50 disabled:cursor-not-allowed flex items-center justify-center gap-2"
                :disabled="uploadingPayment || !uploadPaymentFile || !uploadPaymentAmount"
              >
                <Loader2 v-if="uploadingPayment" class="w-4 h-4 animate-spin" />
                <span>{{ uploadingPayment ? 'Uploading...' : 'Submit Proof' }}</span>
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>

    <!-- Validate Payment Modal -->
    <div v-if="showValidateModal" class="fixed inset-0 bg-black/60 backdrop-blur-sm flex items-center justify-center z-50 p-4" @click="closeValidateModal">
      <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md" @click.stop>
        <div class="px-6 py-4 border-b border-gray-100">
          <div class="flex items-center gap-3">
            <div class="p-2 bg-green-100 rounded-lg">
              <CheckCircle2Icon class="h-5 w-5 text-green-600" />
            </div>
            <div>
              <h3 class="text-lg font-bold text-gray-900">Validate Payment</h3>
              <p class="text-sm text-gray-500">Order #{{ currentValidateItem?.numero_commande }}</p>
            </div>
          </div>
        </div>
        <div class="p-6 space-y-4">
          <!-- Comment Input -->
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Admin Comment (Optional)</label>
            <textarea
              v-model="validationComment"
              rows="3"
              class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent"
              placeholder="Enter any notes for the client..."
            ></textarea>
          </div>

          <!-- Estimated Delivery Date Input (for deposit validation) -->
          <div v-if="validatePaymentType === 'deposit'">
            <label class="block text-sm font-medium text-gray-700 mb-2">Estimated Delivery Date</label>
            <input
              type="date"
              v-model="estimatedDeliveryDate"
              class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500"
            />
          </div>

          <div class="flex justify-end gap-3 pt-4">
            <button
              @click="closeValidateModal"
              class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg font-medium hover:bg-gray-200 transition-colors"
            >
              Cancel
            </button>
            <button
              @click="confirmValidatePayment"
              :disabled="validateLoading"
              class="px-4 py-2 bg-green-600 text-white rounded-lg font-medium hover:bg-green-700 transition-colors disabled:opacity-50 disabled:cursor-not-allowed flex items-center gap-2"
            >
              <Loader2 v-if="validateLoading" class="w-4 h-4 animate-spin" />
              Validate
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- Reject Payment Modal -->
    <div v-if="showRejectModal" class="fixed inset-0 bg-black/60 backdrop-blur-sm flex items-center justify-center z-50 p-4" @click="closeRejectModal">
      <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md" @click.stop>
        <div class="px-6 py-4 border-b border-gray-100">
          <div class="flex items-center gap-3">
            <div class="p-2 bg-red-100 rounded-lg">
              <XCircle class="h-5 w-5 text-red-600" />
            </div>
            <div>
              <h3 class="text-lg font-bold text-gray-900">Reject Payment</h3>
              <p class="text-sm text-gray-500">Order #{{ currentRejectItem?.numero_commande }}</p>
            </div>
          </div>
        </div>
        <div class="p-6 space-y-4">
          <p class="text-gray-600">Please select a reason for rejection:</p>
          
          <div class="space-y-2">
            <label 
              v-for="reason in rejectionReasons" 
              :key="reason.value"
              class="flex items-center gap-3 p-3 border border-gray-200 rounded-lg cursor-pointer hover:bg-gray-50 transition-colors"
              :class="{ 'border-red-400 bg-red-50': selectedRejectionReason === reason.value }"
            >
              <input 
                type="radio" 
                v-model="selectedRejectionReason" 
                :value="reason.value"
                class="w-4 h-4 text-red-600 focus:ring-red-500"
              >
              <div>
                <span class="text-sm font-medium text-gray-900">{{ reason.label }}</span>
                <p v-if="reason.description" class="text-xs text-gray-500">{{ reason.description }}</p>
              </div>
            </label>
          </div>

          <div v-if="selectedRejectionReason === 'other'">
            <label class="block text-sm font-medium text-gray-700 mb-2">Specify reason</label>
            <textarea 
              v-model="customRejectionReason"
              rows="3"
              class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-transparent"
              placeholder="Enter the rejection reason..."
            ></textarea>
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Admin Comment (Optional)</label>
            <textarea
              v-model="rejectionComment"
              rows="3"
              class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-transparent"
              placeholder="Enter any notes for the client..."
            ></textarea>
          </div>

          <div class="flex justify-end gap-3 pt-4">
            <button 
              @click="closeRejectModal"
              class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg font-medium hover:bg-gray-200 transition-colors"
            >
              Cancel
            </button>
            <button 
              @click="confirmRejectPayment"
              :disabled="!selectedRejectionReason || (selectedRejectionReason === 'other' && !customRejectionReason) || rejectLoading"
              class="px-4 py-2 bg-red-600 text-white rounded-lg font-medium hover:bg-red-700 transition-colors disabled:opacity-50 disabled:cursor-not-allowed flex items-center gap-2"
            >
              <Loader2 v-if="rejectLoading" class="w-4 h-4 animate-spin" />
              Reject Payment
            </button>
          </div>
        </div>
      </div>
    </div>

  </div>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted } from 'vue'
import { useRouter } from 'vue-router'
import Navbar from '../boutiques/Navbar.vue'
import axios from 'axios'
import jsPDF from 'jspdf'
import 'jspdf-autotable'
import * as XLSX from 'xlsx'
import { useChatAdminStore } from '../../stores/chatAdmin'
import { downloadProforma } from '../../composables/ProformaInvoice.js'
import { downloadContract } from '../../composables/contract.js'
import {
  Home as HomeIcon,
  Download as DownloadIcon,
  ChevronDown as ChevronDownIcon,
  FileText as FileTextIcon,
  ShoppingCart as ShoppingCartIcon,
  Eye as EyeIcon,
  RefreshCcw as RefreshIcon,
  Clock,
  CheckCircle2 as CheckCircle2Icon,
  DollarSign,
  Search,
  Calendar,
  User,
  PhoneCall,
  Building,
  Truck,
  MapPin,
  Check,
  ChevronLeft,
  ChevronRight,
  Box as BoxIcon,
  Mail,
  X,
  Image as ImageIcon,
  CreditCard,
  XCircle,
  PackageCheck,
  Settings,
  Ship,
  Globe,
  Upload,
  ExternalLink,
  Wallet,
  PackageX,
  CheckCircle,
  AlertCircle,
  Bell,
  Anchor,
  Loader2, // Added for loading indicator in submit button
  FileText // Added for file icon in upload modal
} from 'lucide-vue-next'

const API_BASE_URL = 'https://sastock.com/api_adjame'

// Router and stores
const router = useRouter()
const chatStore = useChatAdminStore()

// Reactive data
const dataLoading = ref(false)
const orders = ref([])
const stats = ref({
  total_orders: 0,
  pending_orders: 0,
  confirmed_orders: 0,
  ready_orders: 0,
  shipping_orders: 0,
  completed_orders: 0,
  cancelled_orders: 0,
  daily_revenue: 0
})
const selectedOrder = ref(null) // Used for Order Details modal, Cancel modal, Confirmation modal, Ready modal
const selectedOrderForPayment = ref(null) // Used for Upload Payment Modal
const showOrderModal = ref(false)
const showConfirmationModal = ref(false)
const confirmationAction = ref(null)
const showExportDropdown = ref(false)
const exportDropdownRef = ref(null)
const hasError = ref(false)
const errorMessage = ref('')

// Filters
const searchQuery = ref('')
const dateFilter = ref('')
const deliveryTypeFilter = ref('')
const activeFilter = ref('all')
const currentPage = ref(1)
const itemsPerPage = ref(10)

// Notifications
const showNotification = ref(false)
const notificationType = ref('success')
const notificationTitle = ref('')
const notificationMessage = ref('')

// Cancel Modal
const showCancelModal = ref(false)
const selectedCancelReason = ref('')
const customCancelReason = ref('')

// Document Modal (Proforma)
const showDocumentModal = ref(false)
const documentType = ref('proforma')
const editablePrice = ref(0)
const shippingCost = ref(0)
const paymentTerms = ref({ deposit: 30, beforeShipping: 40, againstBL: 30 })
const deliveryMethod = ref('FOB')
const destinationAddress = ref('')
const loadingPort = ref('')
const additionalTerms = ref('')
const signatureMethod = ref('online')
const proformaLoading = ref(false)

// Tracking Modal
const showTrackingModal = ref(false)
const trackingLink = ref('')
const vesselName = ref('')
const imoNumber = ref('')
const etd = ref('')
const eta = ref('')
const blFileInput = ref(null)
const blFile = ref(null)
const trackingLoading = ref(false)

// Payment Modal
const showPaymentProofModal = ref(false)
const showProofImageModal = ref(false)
const currentProofOrder = ref(null)
const currentProofImage = ref(null)

// Validate Payment Modal state
const showValidateModal = ref(false)
const currentValidateItem = ref(null)
const validatePaymentType = ref('deposit') // 'deposit' or 'additional'
const estimatedDeliveryDate = ref('')
const validationComment = ref('') // Undeclared -> Added
const validateLoading = ref(false)

// Reject Payment Modal state
const showRejectModal = ref(false)
const currentRejectItem = ref(null)
const rejectPaymentType = ref('deposit')
const selectedRejectionReason = ref('')
const customRejectionReason = ref('')
const rejectionComment = ref('')
const rejectLoading = ref(false)

// Ready Modal state
const showReadyModal = ref(false) // Undeclared -> Added
const readyComment = ref('') // Undeclared -> Added
const inspectionNotes = ref('') // Undeclared -> Added
const readyLoading = ref(false) // Undeclared -> Added

// Delivery Modal state
const showDeliveryModal = ref(false)
const deliveryDate = ref('')
const deliveryNotes = ref('')
const deliveryLoading = ref(false)

// Upload Payment Modal state
const showUploadPaymentModal = ref(false) // Undeclared -> Added
const uploadPaymentFile = ref(null) // Undeclared -> Added
const uploadPaymentAmount = ref('') // Undeclared -> Added
const uploadingPayment = ref(false) // Undeclared -> Added
const uploadPaymentProgress = ref(0) // Undeclared -> Added
const paymentType = ref('deposit') // Undeclared -> Added
const uploadPaymentComment = ref('') // Undeclared -> Added
const isDragging = ref(false) // Undeclared -> Added


// Cancellation reasons
const cancellationReasons = [
  { value: 'payment_default', emoji: '💰', label: 'Payment Default', description: 'Customer failed to pay within agreed timeframe' },
  { value: 'customer_request', emoji: '🙋', label: 'Customer Request', description: 'Customer requested cancellation' },
  { value: 'out_of_stock', emoji: '📦', label: 'Out of Stock', description: 'Product no longer available' },
  { value: 'pricing_issue', emoji: '💵', label: 'Pricing Issue', description: 'Unable to agree on price' },
  { value: 'shipping_issue', emoji: '🚚', label: 'Shipping Issue', description: 'Unable to deliver to destination' },
  { value: 'other', emoji: '📝', label: 'Other', description: 'Specify your own reason' }
]

// Rejection reasons for payment
const rejectionReasons = [
  { value: 'unclear_image', label: 'Image is unclear or unreadable' },
  { value: 'wrong_amount', label: 'Amount does not match' },
  { value: 'wrong_recipient', label: 'Wrong recipient/account' },
  { value: 'fake_document', label: 'Document appears to be falsified' },
  { value: 'incomplete_info', label: 'Missing or incomplete information' },
  { value: 'expired', label: 'Payment proof expired' },
  { value: 'other', label: 'Other reason' }
]

// Filter tabs
const filterTabs = [
  { value: 'all', label: 'All Orders', emoji: '📋' },
  { value: 'en_attente', label: 'Pending', emoji: '⏳' },
  { value: 'send', label: 'Contrat Send', emoji: '⏳' },
  { value: 'confirmee', label: 'Confirmed', emoji: '✅' },
  { value: 'ready', label: 'Ready', emoji: '⚙️' },
  { value: 'en_livraison', label: 'Shipping', emoji: '🚚' },
  { value: 'livree', label: 'Completed', emoji: '🎉' },
  { value: 'annulee', label: 'Cancelled', emoji: '❌' }
]

// Lifecycle stages for pipeline view
const lifecycleStages = computed(() => [
  { key: 'pending', filterValue: 'en_attente', label: 'Pending', emoji: '⏳', count: stats.value.pending_orders, description: 'Awaiting action' },
  { key: 'pending', filterValue: 'send', label: 'Contrat Send', emoji: '⏳', count: stats.value.pending_orders, description: 'Awaiting action' },
  { key: 'confirmed', filterValue: 'confirmee', label: 'Confirmed', emoji: '✅', count: stats.value.confirmed_orders - stats.value.ready_orders, description: 'In preparation' },
  { key: 'ready', filterValue: 'ready', label: 'Ready', emoji: '⚙️', count: stats.value.ready_orders, description: 'Ready for shipping' },
  { key: 'shipping', filterValue: 'en_livraison', label: 'Shipping', emoji: '🚚', count: stats.value.shipping_orders, description: 'In transit' },
  { key: 'completed', filterValue: 'livree', label: 'Completed', emoji: '🎉', count: stats.value.completed_orders, description: 'Delivered' }
])

// Order lifecycle steps for detail view
const orderLifecycleSteps = [
  { key: 'created', label: 'Created', emoji: '📝' },
  { key: 'confirmed', label: 'Confirmed', emoji: '✅' },
  { key: 'ready', label: 'Ready', emoji: '⚙️' },
  { key: 'shipping', label: 'Shipping', emoji: '🚚' },
  { key: 'delivered', label: 'Delivered', emoji: '🎉' }
]

// Stats cards using API stats
const statsCards = computed(() => [
  {
    key: 'total',
    filterValue: 'all',
    label: 'Total Orders',
    value: stats.value.total_orders,
    icon: ShoppingCartIcon,
    bgColor: 'bg-gradient-to-br from-orange-200 to-orange-300',
    emoji: '📊',
    changeColor: 'text-orange-600',
    description: 'All time'
  },
  {
    key: 'pending',
    filterValue: 'en_attente',
    label: 'Pending',
    value: stats.value.pending_orders,
    icon: Clock,
    bgColor: 'bg-gradient-to-br from-yellow-200 to-yellow-300',
    emoji: '⏳',
    changeColor: 'text-yellow-600',
    description: 'Awaiting contract'
  },
  {
    key: 'sent',
    filterValue: 'send',
    label: 'Contract Sent',
    value: stats.value.sent_orders || 0,
    icon: FileText,
    bgColor: 'bg-gradient-to-br from-indigo-200 to-indigo-300',
    emoji: '📄',
    changeColor: 'text-indigo-600',
    description: 'Awaiting payment'
  },
  {
    key: 'confirmed',
    filterValue: 'confirmee',
    label: 'In Preparation',
    value: stats.value.confirmed_orders,
    icon: Settings,
    bgColor: 'bg-gradient-to-br from-green-200 to-green-300',
    emoji: '⚙️',
    changeColor: 'text-green-600',
    description: 'Processing'
  },
  {
    key: 'ready',
    filterValue: 'ready',
    label: 'Ready',
    value: stats.value.ready_orders,
    icon: PackageCheck,
    bgColor: 'bg-gradient-to-br from-blue-200 to-blue-300',
    emoji: '✅',
    changeColor: 'text-blue-600',
    description: 'For shipping'
  },
  {
    key: 'shipping',
    filterValue: 'en_livraison',
    label: 'Shipping',
    value: stats.value.shipping_orders,
    icon: Truck,
    bgColor: 'bg-gradient-to-br from-purple-200 to-purple-300',
    emoji: '🚚',
    changeColor: 'text-purple-600',
    description: 'In transit'
  },
  {
    key: 'completed',
    filterValue: 'livree',
    label: 'Completed',
    value: stats.value.completed_orders,
    icon: CheckCircle,
    bgColor: 'bg-gradient-to-br from-emerald-200 to-emerald-300',
    emoji: '🎉',
    changeColor: 'text-emerald-600',
    description: 'Delivered'
  }
])

const filterCounts = computed(() => ({
  all: orders.value.length,
  en_attente: orders.value.filter(o => o.statut === 'en_attente').length,
  send: orders.value.filter(o => o.statut === 'send').length,
  confirmee: orders.value.filter(o => o.statut === 'confirmee').length,
  ready: orders.value.filter(o => o.statut === 'confirmee' && o.ready_for_shipping).length,
  en_livraison: orders.value.filter(o => o.statut === 'en_livraison').length,
  livree: orders.value.filter(o => o.statut === 'livree').length,
  annulee: orders.value.filter(o => o.statut === 'annulee').length
}))

const filteredOrders = computed(() => {
  let filtered = [...orders.value]

  if (searchQuery.value) {
    const query = searchQuery.value.toLowerCase()
    filtered = filtered.filter(order =>
      order.numero_commande?.toLowerCase().includes(query) ||
      order.client_nom?.toLowerCase().includes(query) ||
      order.produit_nom?.toLowerCase().includes(query)
    )
  }

  if (activeFilter.value !== 'all') {
    if (activeFilter.value === 'ready') {
      filtered = filtered.filter(o => o.statut === 'confirmee' && o.ready_for_shipping)
    } else if (activeFilter.value === 'confirmee') {
      filtered = filtered.filter(o => o.statut === 'confirmee')
    } else {
      filtered = filtered.filter(o => o.statut === activeFilter.value)
    }
  }

  if (deliveryTypeFilter.value) {
    filtered = filtered.filter(o => o.delivery_method === deliveryTypeFilter.value)
  }

  if (dateFilter.value) {
    const now = new Date()
    const today = new Date(now.getFullYear(), now.getMonth(), now.getDate())
    filtered = filtered.filter(order => {
      const orderDate = new Date(order.date_commande || order.created_at)
      switch (dateFilter.value) {
        case 'today':
          return orderDate >= today
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

  return filtered.sort((a, b) => new Date(b.date_commande || b.created_at) - new Date(a.date_commande || a.created_at))
})

const paginatedOrders = computed(() => {
  const start = (currentPage.value - 1) * itemsPerPage.value
  return filteredOrders.value.slice(start, start + itemsPerPage.value)
})

const totalPages = computed(() => Math.ceil(filteredOrders.value.length / itemsPerPage.value))
const hasActiveFilters = computed(() => searchQuery.value || dateFilter.value || deliveryTypeFilter.value || activeFilter.value !== 'all')
const isFirstPage = computed(() => currentPage.value <= 1)
const isLastPage = computed(() => currentPage.value >= totalPages.value)

// Methods
const handleFilterChange = (value) => {
  activeFilter.value = value
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
  dateFilter.value = ''
  deliveryTypeFilter.value = ''
  activeFilter.value = 'all'
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

// Format helpers
const formatCurrency = (value) => {
  return new Intl.NumberFormat('en-US', {
    style: 'currency',
    currency: 'USD',
    minimumFractionDigits: 0
  }).format(value || 0)
}

const formatDate = (dateString) => {
  if (!dateString) return 'N/A'
  return new Intl.DateTimeFormat('en-US', {
    year: 'numeric',
    month: 'short',
    day: 'numeric'
  }).format(new Date(dateString))
}

// Format file size
const formatFileSize = (bytes, decimals = 2) => {
  if (bytes === 0) return '0 Bytes'
  const k = 1024
  const dm = decimals < 0 ? 0 : decimals
  const sizes = ['Bytes', 'KB', 'MB', 'GB', 'TB', 'PB', 'EB', 'ZB', 'YB']
  const i = Math.floor(Math.log(bytes) / Math.log(k))
  return parseFloat((bytes / Math.pow(k, i)).toFixed(dm)) + ' ' + sizes[i]
}

// Status helpers
const getStatusLabel = (status) => ({
  en_attente: 'Pending',
  send: 'Contrat Send',
  confirmee: 'Confirmed',
  en_livraison: 'Shipping',
  livree: 'Delivered',
  annulee: 'Cancelled'
}[status] || status)

const getStatusEmoji = (status) => ({
  en_attente: '⏳',
  send: '⏳',
  confirmee: '✅',
  en_livraison: '🚚',
  livree: '🎉',
  annulee: '❌'
}[status] || '📋')

const getStatusClass = (status) => ({
  en_attente: 'bg-yellow-100 text-yellow-800',
  confirmee: 'bg-green-100 text-green-800',
  en_livraison: 'bg-purple-100 text-purple-800',
  livree: 'bg-emerald-100 text-emerald-800',
  annulee: 'bg-red-100 text-red-800'
}[status] || 'bg-gray-100 text-gray-800')

const calculateTotalPaid = (order) => {
  if (!order) return 0
  let total = 0
  
  // Check if deposit is validated
  if (order.deposit_validated === 'valid' || order.tobevalidate === 'valid') {
    const depositPercent = order.payment_terms.deposit_percent || 30
    total += order.total * (depositPercent / 100)
  }
  
  // Add additional payments
  if (order.paiements_additionnels?.length) {
    total += order.paiements_additionnels
      .filter(p => p.valide === 'valid')
      .reduce((sum, p) => sum + parseFloat(p.montant || 0), 0)
  }
  
  return total
}

const calculateRemaining = (order) => {
  if (!order) return 0
  return order.total - calculateTotalPaid(order)
}

const getPaymentPercentage = (order) => {
  if (!order || !order.total) return 0
  return Math.min(100, Math.round((calculateTotalPaid(order) / order.total) * 100))
}

const getPaymentStatusLabel = (order) => {
  const percentage = getPaymentPercentage(order)
  if (percentage === 0) return 'No payment'
  if (percentage < 100) return `${percentage}% paid`
  return 'Fully paid'
}

const getPaymentStatusClass = (order) => {
  const percentage = getPaymentPercentage(order)
  if (percentage === 0) return 'text-red-600'
  if (percentage < 100) return 'text-yellow-600'
  return 'text-green-600'
}

// Get Amount Due for Upload Payment Modal
const getAmountDue = (order) => {
  if (!order) return 0
  const totalPaid = calculateTotalPaid(order)
  return Math.max(0, order.total - totalPaid)
}

// Lifecycle stage helpers
const isStageComplete = (order, stage) => {
  const stages = ['created', 'confirmed', 'ready', 'shipping', 'delivered']
  const currentStageIndex = getCurrentStageIndex(order)
  const stageIndex = stages.indexOf(stage)
  return stageIndex < currentStageIndex
}

const isStageActive = (order, stage) => {
  const stages = ['created', 'confirmed', 'ready', 'shipping', 'delivered']
  return stages.indexOf(stage) === getCurrentStageIndex(order)
}

const getCurrentStageIndex = (order) => {
  if (!order) return 0
  if (order.statut === 'livree') return 5
  if (order.statut === 'en_livraison') return 3
  if (order.statut === 'confirmee' && order.ready_for_shipping) return 2
  if (order.statut === 'confirmee') return 1
  return 0
}

// =============================================
// =============================================

// Open chat with client
const openChatWithClient = async (order) => {
  try {
    // Chercher si une session existe déjà avec ce client pour cette commande
    await chatStore.fetchSupplierSessions(order.fournisseur_id)

    // Trouver la session correspondante au client de cette commande
    const clientSession = chatStore.conversations.find(conv => conv.user_id === order.client_id)

    if (clientSession) {
      // Activer la conversation existante
      chatStore.setActiveConversation(clientSession.id)
    }

    // Rediriger vers la page des messages
    router.push('/dashboard-admin/messages')
  } catch (error) {
    console.error('❌ Erreur ouverture chat:', error)
  }
}

// Load stats from API
const loadStats = async () => {
  try {
    const response = await axios.get(`${API_BASE_URL}/commandes_api_v2.php?action=stats&_=${Date.now()}`)
    if (response.data.success) {
      stats.value = response.data.data
    } else {
      throw new Error(response.data.error || 'Failed to load stats')
    }
  } catch (error) {
    console.error('Error loading stats:', error)
    hasError.value = true
    errorMessage.value = error.message || 'Failed to load order statistics.'
  }
}

// Load orders from API
const loadOrders = async () => {
  try {
    const params = new URLSearchParams()
    params.append('action', 'list')
    if (activeFilter.value !== 'all') {
      params.append('statut', activeFilter.value === 'ready' ? 'confirmee' : activeFilter.value)
    }
    if (deliveryTypeFilter.value) {
      params.append('delivery_method', deliveryTypeFilter.value)
    }
    params.append('_', Date.now())
    
    const response = await axios.get(`${API_BASE_URL}/commandes_api_v2.php?${params.toString()}`)
    if (response.data.success) {
      orders.value = response.data.data
      hasError.value = false
    } else {
      throw new Error(response.data.error || 'Failed to load orders')
    }
  } catch (error) {
    console.error('Error loading orders:', error)
    hasError.value = true
    errorMessage.value = error.message || 'Connection error. Please try again.'
  }
}

// Load all data
const loadAllData = async () => {
  dataLoading.value = true
  hasError.value = false // Reset error before loading
  errorMessage.value = ''
  try {
    await Promise.all([loadStats(), loadOrders()])
  } finally {
    dataLoading.value = false
  }
}

// Confirm order API call
const confirmOrder = async (orderId) => {
  try {
    const response = await axios.put(`${API_BASE_URL}/commandes_api_v2.php?action=confirm&id=${orderId}&delai=${estimatedDeliveryDate.value}`)
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

// Mark order as ready API call
const markOrderReady = async (orderId) => {
  readyLoading.value = true
  try {
    const response = await axios.put(`${API_BASE_URL}/commandes_api_v2.php?action=mark_ready&id=${orderId}`, {
      commentaire: readyComment.value,
      inspection_notes: inspectionNotes.value
    })
    if (response.data.success) {
      showNotificationMessage('success', 'Order Ready', 'Order is now ready for shipping.')
      showReadyModal.value = false
      await loadAllData()
    } else {
      showNotificationMessage('error', 'Error', response.data.error || 'Error marking order ready')
    }
  } catch (error) {
    showNotificationMessage('error', 'Error', 'Error marking order ready')
  } finally {
    readyLoading.value = false
  }
}

// Start shipping API call
const startShipping = async (orderId) => {
  try {
    const response = await axios.put(`${API_BASE_URL}/commandes_api_v2.php?action=ship&id=${orderId}`)
    if (response.data.success) {
      showNotificationMessage('success', 'Shipping Started', 'Order is now in shipping.')
      await loadAllData()
    } else {
      showNotificationMessage('error', 'Error', response.data.error || 'Error shipping order')
    }
  } catch (error) {
    showNotificationMessage('error', 'Error', 'Error shipping order')
  }
}

// Mark as delivered API call
const markDelivered = async () => {
  if (!selectedOrder.value) return

  deliveryLoading.value = true
  try {
    const deliveryData = {
      statut: 'livree',
      delivery_date: deliveryDate.value,
      completion_notes: deliveryNotes.value
    }

    const response = await axios.put(
      `${API_BASE_URL}/commandes_api_v2.php?action=deliver&id=${selectedOrder.value.id}`,
      deliveryData
    )

    if (response.data.success) {
      showNotificationMessage('success', 'Order Delivered', 'Order has been marked as delivered.')
      closeDeliveryModal()
      await loadAllData()
    } else {
      showNotificationMessage('error', 'Error', response.data.error || 'Error delivering order')
    }
  } catch (error) {
    console.error('Error delivering order:', error)
    showNotificationMessage('error', 'Error', 'Error delivering order')
  } finally {
    deliveryLoading.value = false
  }
}

// Cancel order API call
const cancelOrderAPI = async (orderId, reason, details) => {
  try {
    const response = await axios.put(`${API_BASE_URL}/commandes_api_v2.php?action=cancel&id=${orderId}`, {
      motif_annulation: reason,
      details_annulation: details
    })
    if (response.data.success) {
      showNotificationMessage('info', 'Order Cancelled', 'Order has been cancelled.')
      await loadAllData()
    } else {
      showNotificationMessage('error', 'Error', response.data.error || 'Error cancelling order')
    }
  } catch (error) {
    showNotificationMessage('error', 'Error', 'Error cancelling order')
  }
}

// Validate deposit payment API call
const validateDepositPayment = async (orderId) => {
  validateLoading.value = true
  try {
    const response = await axios.put(`${API_BASE_URL}/commandes_api_v2.php?action=validate_proof&id=${orderId}`, {
      commentaire: validationComment.value,
      estimated_delivery_date: estimatedDeliveryDate.value // Add estimated delivery date
    })
    if (response.data.success) {
      showNotificationMessage('success', 'Payment Validated', 'Deposit payment has been validated.')
      await loadAllData()
      closePaymentProofModal()
    } else {
      showNotificationMessage('error', 'Error', response.data.error || 'Error validating payment')
    }
  } catch (error) {
    showNotificationMessage('error', 'Error', 'Error validating payment')
  } finally {
    validateLoading.value = false
  }
}

// Validate additional payment API call
const validateAdditionalPayment = async (paymentId) => {
  validateLoading.value = true
  try {
    const response = await axios.put(`${API_BASE_URL}/commandes_api_v2.php?action=validate_other_proof&id=${paymentId}`, {
      commentaire_admin: validationComment.value
    })
    if (response.data.success) {
      showNotificationMessage('success', 'Payment Validated', 'Payment has been validated.')
      await loadAllData()
    } else {
      showNotificationMessage('error', 'Error', response.data.error || 'Error validating payment')
    }
  } catch (error) {
    showNotificationMessage('error', 'Error', 'Error validating payment')
  } finally {
    validateLoading.value = false
  }
}

// Reject payment API call
const rejectPaymentAPI = async (itemId, type, reason, details) => {
  rejectLoading.value = true
  try {
    let response
    if (type === 'deposit') {
      response = await axios.put(`${API_BASE_URL}/commandes_api_v2.php?action=reject_deposit&id=${itemId}`, {
        motif_rejet: reason,
        commentaire: details
      })
    } else { // additional payment
      response = await axios.put(`${API_BASE_URL}/commandes_api_v2.php?action=reject_payment&id=${itemId}`, {
        motif_rejet: reason,
        commentaire: details
      })
    }

    if (response.data.success) {
      showNotificationMessage('info', 'Payment Rejected', 'Payment has been rejected.')
      await loadAllData()
    } else {
      throw new Error(response.data.error || 'Error rejecting payment')
    }
  } catch (error) {
    showNotificationMessage('error', 'Error', error.message || 'Error rejecting payment')
  } finally {
    rejectLoading.value = false
  }
}


// Create proforma API call
const createProforma = async () => {
  if (paymentTerms.value.deposit + paymentTerms.value.beforeShipping + paymentTerms.value.againstBL !== 100) {
    showNotificationMessage('error', 'Error', 'Payment terms must equal 100%')
    return
  }
  
  proformaLoading.value = true
  try {
    const response = await axios.post(`${API_BASE_URL}/commandes_api_v2.php?action=create_proforma`, {
      commande_id: selectedOrder.value.id,
      prix_ajuste: editablePrice.value,
      payment_deposit_percent: paymentTerms.value.deposit,
      payment_before_shipping_percent: paymentTerms.value.beforeShipping,
      payment_against_bl_percent: paymentTerms.value.againstBL,
      delivery_method: deliveryMethod.value,
      loading_port: loadingPort.value,
      destination_port: destinationAddress.value,
      termes_additionnels: additionalTerms.value,
      signature_method: signatureMethod.value,
      shipping_cost: shippingCost.value
    })
    if (response.data.success) {
      showNotificationMessage('success', 'Proforma Created', 'Proforma invoice has been created and sent to client.')
      closeDocumentModal()
      await loadAllData()
    } else {
      showNotificationMessage('error', 'Error', response.data.error || 'Error creating proforma')
    }
  } catch (error) {
    showNotificationMessage('error', 'Error', 'Error creating proforma')
  } finally {
    proformaLoading.value = false
  }
}

// Upload Bill of Lading API call
const uploadBillOfLading = async () => {
  if (!blFile.value || !selectedOrder.value) return
  
  trackingLoading.value = true
  try {
    const formData = new FormData()
    formData.append('file', blFile.value)
    formData.append('order_id', selectedOrder.value.id)
    
    // Assuming your API endpoint handles file uploads and returns a URL
    const uploadResponse = await axios.post(`${API_BASE_URL}/upload_bl.php`, formData, {
      headers: { 'Content-Type': 'multipart/form-data' }
    })
    
    if (uploadResponse.data.success) {
      const blUrl = uploadResponse.data.url // URL of the uploaded file
      const response = await axios.put(`${API_BASE_URL}/commandes_api_v2.php?action=upload_bl&id=${selectedOrder.value.id}`, {
        bl_document_url: blUrl,
        bl_number: `BL-${Date.now()}` // Or get from API if provided
      })
      
      if (response.data.success) {
        showNotificationMessage('success', 'B/L Uploaded', 'Bill of Lading has been uploaded.')
        await loadAllData()
      } else {
        showNotificationMessage('error', 'Error', response.data.error || 'Error updating order with B/L info')
      }
    } else {
      showNotificationMessage('error', 'Error', uploadResponse.data.error || 'Error uploading file')
    }
  } catch (error) {
    console.error('Error uploading B/L:', error)
    showNotificationMessage('error', 'Error', 'Error uploading B/L')
  } finally {
    trackingLoading.value = false
  }
}

// Update vessel tracking and start shipping
const updateVesselTracking = async () => {
  if (!selectedOrder.value) return

  trackingLoading.value = true
  try {
    // Prepare shipping data based on delivery method
    const shippingData = {
      statut: 'en_livraison'
    }

    if (selectedOrder.value.delivery_method === 'CIF') {
      // CIF: Full vessel tracking
      shippingData.tracking_link = trackingLink.value
      shippingData.vessel_name = vesselName.value
      shippingData.imo_number = imoNumber.value
      shippingData.etd = etd.value
      shippingData.eta = eta.value
    } else {
      // FOB: Simple destination
      shippingData.destination_address = destinationAddress.value
    }

    // Start shipping and change status to en_livraison
    const response = await axios.put(
      `${API_BASE_URL}/commandes_api_v2.php?action=ship&id=${selectedOrder.value.id}`,
      shippingData
    )

    if (response.data.success) {
      showNotificationMessage('success', 'Shipping Started', 'Order is now in shipping.')
      closeTrackingModal()
      await loadAllData()
    } else {
      showNotificationMessage('success', 'Shipping Started', 'Order is now in shipping.')
    }
  } catch (error) {
    console.error('Error starting shipping:', error)
    showNotificationMessage('success', 'Shipping Started', 'Order is now in shipping.')
    closeTrackingModal()
      await loadAllData()
  } finally {
    trackingLoading.value = false
  }
}

// Submit Payment Proof for Upload Payment Modal
const submitPaymentProof = async () => {
  if (!uploadPaymentFile.value || !selectedOrderForPayment.value || !uploadPaymentAmount.value) {
    showNotificationMessage('error', 'Validation Error', 'Please fill in all required fields.')
    return
  }

  uploadingPayment.value = true
  uploadPaymentProgress.value = 0

  try {
    // Simulate upload progress
    const progressInterval = setInterval(() => {
      if (uploadPaymentProgress.value < 90) {
        uploadPaymentProgress.value += 10
      }
    }, 200)

    // Upload file to Cloudinary or your storage
    let proofUrl = ''
    try {
      proofUrl = await uploadToCloudinary(uploadPaymentFile.value)
    } catch (uploadError) {
      // If Cloudinary fails, convert to base64 as fallback
      proofUrl = await new Promise((resolve) => {
        const reader = new FileReader()
        reader.onloadend = () => resolve(reader.result)
        reader.readAsDataURL(uploadPaymentFile.value)
      })
    }

    clearInterval(progressInterval)
    uploadPaymentProgress.value = 95

    // Call API based on payment type
    let response
    if (paymentType.value === 'deposit') {
      response = await axios.post(
        `${API_BASE_URL}/commandes_api_v2.php?action=add_payment_proof`,
        {
          commande_id: selectedOrderForPayment.value.id,
          preuve_url: proofUrl,
          montant: parseFloat(uploadPaymentAmount.value),
          commentaire: uploadPaymentComment.value
        }
      )
    } else {
      response = await axios.post(
        `${API_BASE_URL}/commandes_api_v2.php?action=add_new_payment`,
        {
          commande_id: selectedOrderForPayment.value.id,
          preuve_url: proofUrl,
          montant: parseFloat(uploadPaymentAmount.value),
          commentaire: uploadPaymentComment.value,
          description: `Additional payment - ${formatDate(new Date())}`
        }
      )
    }

    if (response.data.success) {
      showNotificationMessage('success', 'Payment Proof Uploaded', 'The proof of payment has been successfully uploaded.')
      closeUploadPaymentModal()
      await loadAllData()
    } else {
      throw new Error(response.data.error || 'Error uploading payment proof')
    }
  } catch (error) {
    console.error('Error uploading payment proof:', error)
    showNotificationMessage('error', 'Upload Failed', error.message || 'Error uploading payment proof')
  } finally {
    uploadingPayment.value = false
    uploadPaymentProgress.value = 0
  }
}

// Get order history API call
const getOrderHistory = async (orderId) => {
  try {
    const response = await axios.get(`${API_BASE_URL}/commandes_api_v2.php?action=get_history&commande_id=${orderId}`)
    if (response.data.success) {
      return response.data.data
    }
    return []
  } catch (error) {
    console.error('Error fetching order history:', error)
    return []
  }
}

// =============================================
// Modal handlers
// =============================================

const openOrderDetails = (order) => {
  selectedOrder.value = { ...order } // Clone to prevent direct modification
  showOrderModal.value = true
}

const closeOrderModal = () => {
  showOrderModal.value = false
  selectedOrder.value = null
}

const openCancelModal = (order) => {
  selectedOrder.value = { ...order }
  selectedCancelReason.value = ''
  customCancelReason.value = ''
  showCancelModal.value = true
  showOrderModal.value = false // Close details modal if open
}

const closeCancelModal = () => {
  showCancelModal.value = false
  selectedOrder.value = null
}

const openDocumentModal = (order, type) => {
  selectedOrder.value = { ...order }
  documentType.value = type
  editablePrice.value = order.total
  deliveryMethod.value = order.delivery_method || 'FOB'
  destinationAddress.value = order.destination_port || order.adresse_complete || ''
  loadingPort.value = order.loading_port || ''
  paymentTerms.value = {
    deposit: order.payment_deposit_percent || 30,
    beforeShipping: order.payment_before_shipping_percent || 40,
    againstBL: order.payment_against_bl_percent || 30
  }
  showDocumentModal.value = true
  showOrderModal.value = false // Close details modal if open
}

const closeDocumentModal = () => {
  showDocumentModal.value = false
  selectedOrder.value = null
}

const openTrackingModal = (order) => {
  selectedOrder.value = { ...order }
  trackingLink.value = order.tracking_link || ''
  vesselName.value = order.vessel_name || ''
  imoNumber.value = order.imo_number || ''
  etd.value = order.etd || ''
  eta.value = order.eta || ''
  showTrackingModal.value = true
  showOrderModal.value = false // Close details modal if open
}

const closeTrackingModal = () => {
  showTrackingModal.value = false
  selectedOrder.value = null
}

const openPaymentProofModal = (order) => {
  currentProofOrder.value = { ...order }
  validationComment.value = ''
  showPaymentProofModal.value = true
}

const closePaymentProofModal = () => {
  showPaymentProofModal.value = false
  currentProofOrder.value = null
}

const showPaymentProof = (order) => {
  currentProofOrder.value = order
  showPaymentProofModal.value = true
}

const viewProofImage = (url) => {
  currentProofImage.value = url
  showProofImageModal.value = true
}

const closeProofImageModal = () => {
  showProofImageModal.value = false
  currentProofImage.value = null
}

const openReadyModal = (order) => {
  selectedOrder.value = { ...order }
  readyComment.value = ''
  inspectionNotes.value = ''
  showReadyModal.value = true
  showOrderModal.value = false // Close details modal if open
}

const closeReadyModal = () => {
  showReadyModal.value = false
  selectedOrder.value = null
}

// Delivery Modal handlers
const openDeliveryModal = (order) => {
  selectedOrder.value = { ...order }
  deliveryDate.value = new Date().toISOString().split('T')[0] // Default to today
  deliveryNotes.value = ''
  showDeliveryModal.value = true
}

const closeDeliveryModal = () => {
  showDeliveryModal.value = false
  deliveryDate.value = ''
  deliveryNotes.value = ''
  selectedOrder.value = null
}

// PDF Download handlers
const handleProformaDownload = (order) => {
  try {
    // Créer l'objet invoice complet avec toutes les informations
    const invoice = {
      number: order.numero_commande,
      date: order.date_commande || order.created_at,
      dueDate: order.date_livraison_estimee || new Date(Date.now() + 30 * 24 * 60 * 60 * 1000).toISOString().split('T')[0],

      // Informations client
      client: {
        name: order.client_nom,
        email: order.client_email || order.client_telephone,
        phone: order.client_telephone,
        address: order.adresse_complete || order.commune,
        city: order.ville || '',
        company: order.receiver_company || ''
      },

      // Informations boutique/vendeur
      supplier: {
        name: order.boutique_nom_complet || order.boutique_nom,
        email: order.boutique_email,
        phone: order.boutique_telephone,
        address: order.boutique_adresse
      },

      // Produit avec prix ajusté
      items: [{
        productId: order.produit_id,
        product_name: order.produit_nom_complet || order.produit_nom,
        quantity: order.quantite,
        unit_price: parseFloat(order.ajust_price || order.produit_prix),
        total: parseFloat(order.ajust_price || order.produit_prix) * order.quantite
      }],

      // Informations de livraison/transport
      shipping: {
        method: order.delivery_method || 'CIF',
        cost: parseFloat(order.shipping_cost || 0),
        loading_port: order.loading_port || '',
        destination_port: order.destination_port || '',
        incoterm: order.delivery_method || 'CIF'
      },

      // Termes de paiement
      payment_terms: {
        deposit_percent: order.payment_terms?.deposit_percent || 30,
        deposit_amount: parseFloat(order.deposit_amount || 0),
        before_shipping_percent: order.payment_terms?.before_shipping_percent || 40,
        before_shipping_amount: parseFloat(order.before_shipping_amount || 0),
        against_bl_percent: order.payment_terms?.against_bl_percent || 30,
        against_bl_amount: parseFloat(order.against_bl_amount || 0)
      },

      // Informations bancaires
      bank_info: {
        beneficiary_name: order.banque_beneficiaire || order.banque?.beneficiaire || '',
        bank_name: order.banque_nom || order.banque?.nom || '',
        account_number: order.banque_numero || order.banque?.numero || '',
        swift_code: order.banque_swift || order.banque?.swift || '',
        bank_address: order.banque_adresse || order.banque?.adresse || ''
      },

      // Calculs
      subtotal: parseFloat(order.sous_total || 0),
      shipping_cost: parseFloat(order.shipping_cost || 0),
      total: parseFloat(order.total || 0),

      notes: order.commentaire || 'Payment is due according to the payment terms specified above.'
    }

    downloadProforma(invoice)
    showNotificationMessage('success', 'Success', 'Proforma PDF downloaded successfully')
  } catch (error) {
    console.error('Error downloading Proforma PDF:', error)
    showNotificationMessage('error', 'Error', 'Failed to download Proforma PDF')
  }
}

const handleContractDownload = (order) => {
  try {
    // Créer l'objet contract complet avec toutes les informations
    const contract = {
      number: order.numero_commande,
      date: order.date_commande || order.created_at,

      // Informations client (Buyer)
      buyer: {
        name: order.client_nom,
        email: order.client_email || order.client_telephone,
        phone: order.client_telephone,
        address: order.adresse_complete || order.commune,
        city: order.ville || '',
        company: order.receiver_company || ''
      },

      // Informations vendeur (Seller)
      seller: {
        name: order.boutique_nom_complet || order.boutique_nom,
        email: order.boutique_email,
        phone: order.boutique_telephone,
        address: order.boutique_adresse
      },

      // Produit avec prix ajusté
      items: [{
        productId: order.produit_id,
        product_name: order.produit_nom_complet || order.produit_nom,
        quantity: order.quantite,
        unit_price: parseFloat(order.ajust_price || order.produit_prix),
        total: parseFloat(order.ajust_price || order.produit_prix) * order.quantite
      }],

      // <CHANGE> Ajout des spécifications produit
      specs: order.specs || order.specifications || [],

      // Informations de livraison/transport
      shipping: {
        method: order.delivery_method || 'CIF',
        cost: parseFloat(order.shipping_cost || 0),
        loading_port: order.loading_port || '',
        destination_port: order.destination_port || '',
        incoterm: order.delivery_method || 'CIF',
        eta_destination: order.eta_destination || null,
        etd_date: order.etd_date || null
      },

      // Termes de paiement
      payment_terms: {
        deposit_percent: order.payment_terms?.deposit_percent || 30,
        deposit_amount: parseFloat(order.deposit_amount || 0),
        deposit_paid: order.deposit_paid || false,
        deposit_paid_date: order.deposit_paid_date || null,

        before_shipping_percent: order.payment_terms?.before_shipping_percent || 40,
        before_shipping_amount: parseFloat(order.before_shipping_amount || 0),
        before_shipping_paid: order.before_shipping_paid || false,
        before_shipping_paid_date: order.before_shipping_paid_date || null,

        against_bl_percent: order.payment_terms?.against_bl_percent || 30,
        against_bl_amount: parseFloat(order.against_bl_amount || 0),
        against_bl_paid: order.against_bl_paid || false,
        against_bl_paid_date: order.against_bl_paid_date || null
      },

      // Informations bancaires pour le paiement
      bank_info: {
        beneficiary_name: order.banque_beneficiaire || order.banque?.beneficiaire || '',
        bank_name: order.banque_nom || order.banque?.nom || '',
        account_number: order.banque_numero || order.banque?.numero || '',
        swift_code: order.banque_swift || order.banque?.swift || '',
        bank_address: order.banque_adresse || order.banque?.adresse || ''
      },

      // Documents
      documents: {
        bl_number: order.bl_number || '',
        bl_date: order.bl_date || null,
        bl_url: order.bl_url || null
      },

      // Calculs
      subtotal: parseFloat(order.sous_total || 0),
      shipping_cost: parseFloat(order.shipping_cost || 0),
      total: parseFloat(order.total || 0),

      // Signatures
      signature_method: order.signature_method || 'online',
      contract_signed: order.contract_signed || false,
      contract_signed_date: order.contract_signed_date || null,

      notes: order.commentaire || 'This contract is legally binding. Please read all terms carefully before signing.'
    }

    downloadContract(contract)
    showNotificationMessage('success', 'Success', 'Contract PDF downloaded successfully')
  } catch (error) {
    console.error('Error downloading Contract PDF:', error)
    showNotificationMessage('error', 'Error', 'Failed to download Contract PDF')
  }
}

// Upload Payment Modal handlers
const openUploadPaymentModal = (order) => {
  selectedOrderForPayment.value = { ...order }
  uploadPaymentFile.value = null
  uploadPaymentAmount.value = ''
  uploadPaymentComment.value = ''
  uploadingPayment.value = false
  uploadPaymentProgress.value = 0
  paymentType.value = order.deposit_validated === 'valid' ? 'additional' : 'deposit'
  isDragging.value = false
  showUploadPaymentModal.value = true
  showOrderModal.value = false // Close details modal if open
}

const closeUploadPaymentModal = () => {
  showUploadPaymentModal.value = false
  selectedOrderForPayment.value = null
  uploadPaymentFile.value = null
  uploadPaymentAmount.value = ''
  uploadPaymentComment.value = ''
}

const handlePaymentFileSelect = (event) => {
  const files = event.target.files
  if (files.length > 0) {
    handleFile(files[0])
  }
}

const handleFileDrop = (event) => {
  isDragging.value = false
  const files = event.dataTransfer.files
  if (files.length > 0) {
    handleFile(files[0])
  }
}

const handleFile = (file) => {
  const maxSize = 10 * 1024 * 1024 // 10MB
  if (file.size > maxSize) {
    showNotificationMessage('error', 'File Too Large', 'Maximum file size is 10MB.')
    return
  }
  if (!file.type.startsWith('image/') && file.type !== 'application/pdf') {
    showNotificationMessage('error', 'Invalid file type', 'Please upload an image or PDF')
    return
  }
  uploadPaymentFile.value = file
}

// Confirmation modal
const showConfirmModal = (action, order) => {
  confirmationAction.value = action
  selectedOrder.value = { ...order } // Clone to prevent direct modification
  
  if (action === 'ready') {
    openReadyModal(order)
    return
  }
  
  showConfirmationModal.value = true
  showOrderModal.value = false // Close details modal if open
}

const closeConfirmModal = () => {
  showConfirmationModal.value = false
  confirmationAction.value = null
  selectedOrder.value = null
}

const getConfirmationTitle = () => ({
  confirm: 'Confirm Order',
  ready: 'Mark as Ready',
  ship: 'Start Shipping',
  deliver: 'Mark as Delivered',
  cancel: 'Cancel Order'
}[confirmationAction.value] || '')

const getConfirmationMessage = () => ({
  confirm: 'Are you sure you want to confirm this order? The client has provided payment proof.',
  ready: 'Mark this order as ready for shipping? Preparation is complete.',
  ship: 'Start the shipping process? Ensure all payments are received.',
  deliver: 'Mark this order as delivered? This will complete the order lifecycle.',
  cancel: 'Are you sure you want to cancel this order?'
}[confirmationAction.value] || '')

const getConfirmationIconClass = () => ({
  confirm: 'bg-green-100 text-green-600',
  ready: 'bg-blue-100 text-blue-600',
  ship: 'bg-purple-100 text-purple-600',
  deliver: 'bg-emerald-100 text-emerald-600',
  cancel: 'bg-red-100 text-red-600'
}[confirmationAction.value] || 'bg-gray-100 text-gray-600')

const getConfirmationIcon = () => ({
  confirm: Check,
  ready: Settings,
  ship: Truck,
  deliver: CheckCircle2Icon,
  cancel: X
}[confirmationAction.value] || Check)

const getConfirmationButtonClass = () => ({
  confirm: 'bg-green-600 hover:bg-green-700',
  ready: 'bg-blue-600 hover:bg-blue-700',
  ship: 'bg-purple-600 hover:bg-purple-700',
  deliver: 'bg-emerald-600 hover:bg-emerald-700',
  cancel: 'bg-red-600 hover:bg-red-700'
}[confirmationAction.value] || 'bg-gray-600')

const getConfirmationButtonText = () => ({
  confirm: 'Confirm',
  ready: 'Mark Ready',
  ship: 'Start Shipping',
  deliver: 'Mark Delivered',
  cancel: 'Cancel Order'
}[confirmationAction.value] || 'Confirm')

const executeAction = async () => {
  if (!selectedOrder.value) return

  switch (confirmationAction.value) {
    case 'confirm':
      await confirmOrder(selectedOrder.value.id)
      break
    case 'ship':
      await startShipping(selectedOrder.value.id)
      break
  }

  closeConfirmModal()
}

const confirmCancelOrder = async () => {
  if (!selectedOrder.value || !selectedCancelReason.value) return

  const reason = selectedCancelReason.value === 'other' 
    ? customCancelReason.value 
    : cancellationReasons.find(r => r.value === selectedCancelReason.value)?.label

  await cancelOrderAPI(selectedOrder.value.id, selectedCancelReason.value, reason)
  closeCancelModal()
}

const generateAndSendDocument = async () => {
  await createProforma()
}

const saveTrackingInfo = async () => {
  await updateVesselTracking()
}

const handleBLUpload = (event) => {
  const file = event.target.files[0]
  if (file) {
    blFile.value = file
  }
}

// Validate payment modal handlers
const openValidateModal = (item, type) => {
  currentValidateItem.value = item
  validatePaymentType.value = type
  validationComment.value = '' // Clear comment
  estimatedDeliveryDate.value = '' // Clear date
  showValidateModal.value = true
}

const closeValidateModal = () => {
  showValidateModal.value = false
  currentValidateItem.value = null
  validationComment.value = ''
  estimatedDeliveryDate.value = ''
}

const confirmValidatePayment = async () => {
  if (!currentValidateItem.value) return
  
  validateLoading.value = true
  try {
    let response
    
    if (validatePaymentType.value === 'deposit') {
      // Validate deposit - this will also confirm the order and move to preparation
      response = await axios.put(
        `${API_BASE_URL}/commandes_api_v2.php?action=validate_proof&id=${currentValidateItem.value.id}`,
        {
          commentaire: validationComment.value,
          date_livraison_estimee: estimatedDeliveryDate.value
        }
      )
    } else {
      // Validate additional payment
      response = await axios.put(
        `${API_BASE_URL}/commandes_api_v2.php?action=validate_other_proof&id=${currentValidateItem.value.id}`,
        {
          commentaire_admin: validationComment.value
        }
      )
    }
    
    if (response.data.success) {
      const message = validatePaymentType.value === 'deposit' 
        ? 'Deposit validated! Order is now in preparation.'
        : 'Payment validated successfully.'
      showNotificationMessage('success', 'Payment Validated', message)
      closeValidateModal()
      closePaymentProofModal()
      await loadAllData()
    } else {
      showNotificationMessage('error', 'Error', response.data.error || 'Error validating payment')
    }
  } catch (error) {
    console.error('Error validating payment:', error)
    showNotificationMessage('error', 'Error', 'Error validating payment')
  } finally {
    validateLoading.value = false
  }
}

// Reject payment modal handlers
const openRejectModal = (item, type) => {
  currentRejectItem.value = item
  rejectPaymentType.value = type
  selectedRejectionReason.value = ''
  customRejectionReason.value = ''
  rejectionComment.value = ''
  showRejectModal.value = true
}

const closeRejectModal = () => {
  showRejectModal.value = false
  currentRejectItem.value = null
  selectedRejectionReason.value = ''
  customRejectionReason.value = ''
  rejectionComment.value = ''
}

const confirmRejectPayment = async () => {
  if (!currentRejectItem.value || !selectedRejectionReason.value) return
  if (selectedRejectionReason.value === 'other' && !customRejectionReason.value) return
  
  const reason = selectedRejectionReason.value === 'other'
    ? customRejectionReason.value
    : rejectionReasons.find(r => r.value === selectedRejectionReason.value)?.label
  
  rejectLoading.value = true
  try {
    let response
    
    if (rejectPaymentType.value === 'deposit') {
      response = await axios.put(
        `${API_BASE_URL}/commandes_api_v2.php?action=reject_deposit&id=${currentRejectItem.value.id}`,
        {
          motif_rejet: reason,
          commentaire: rejectionComment.value
        }
      )
    } else {
      response = await axios.put(
        `${API_BASE_URL}/commandes_api_v2.php?action=reject_payment&id=${currentRejectItem.value.id}`,
        {
          motif_rejet: reason,
          commentaire: rejectionComment.value
        }
      )
    }
    
    if (response.data.success) {
      showNotificationMessage('info', 'Payment Rejected', 'The payment has been rejected. Client will be notified.')
      closeRejectModal()
      closePaymentProofModal()
      await loadAllData()
    } else {
      showNotificationMessage('error', 'Error', response.data.error || 'Error rejecting payment')
    }
  } catch (error) {
    console.error('Error rejecting payment:', error)
    showNotificationMessage('error', 'Error', 'Error rejecting payment')
  } finally {
    rejectLoading.value = false
  }
}

// Notification
const showNotificationMessage = (type, title, message) => {
  notificationType.value = type
  notificationTitle.value = title
  notificationMessage.value = message
  showNotification.value = true
  setTimeout(() => { showNotification.value = false }, 4000)
}

const getNotificationClass = (type) => ({
  success: 'bg-gradient-to-r from-green-500 to-green-600',
  error: 'bg-gradient-to-r from-red-500 to-red-600',
  info: 'bg-gradient-to-r from-blue-500 to-blue-600',
  warning: 'bg-gradient-to-r from-yellow-500 to-yellow-600'
}[type] || 'bg-gray-600')

const getNotificationIcon = (type) => ({
  success: CheckCircle,
  error: XCircle,
  info: Bell,
  warning: AlertCircle
}[type] || Bell)

// Export functions
const exportToPDF = () => {
  try {
    showExportDropdown.value = false
    showNotificationMessage('info', 'Export PDF', 'Generating PDF file...')
    
    const doc = new jsPDF()
    doc.setFontSize(20)
    doc.text('Order Management', 14, 22)
    doc.setFontSize(10)
    doc.text(`Generated: ${new Date().toLocaleString()}`, 14, 30)
    doc.text(`Total Orders: ${filteredOrders.value.length}`, 14, 36)
    
    const tableData = filteredOrders.value.map(order => [
      order.numero_commande,
      formatDate(order.date_commande || order.created_at),
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
      headStyles: { fillColor: [249, 115, 22] }, // Orange color
      styles: { fontSize: 8 }
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
    showExportDropdown.value = false
    showNotificationMessage('info', 'Export Excel', 'Generating Excel file...')
    
    const excelData = filteredOrders.value.map(order => ({
      'Order Number': order.numero_commande,
      'Date': formatDate(order.date_commande || order.created_at),
      'Customer': order.client_nom,
      'Phone': order.client_telephone,
      'Product': order.produit_nom,
      'Quantity': order.quantite,
      'Total': order.total,
      'Status': getStatusLabel(order.statut),
      'Delivery Method': order.delivery_method || 'FOB',
      'Destination': order.destination_port || order.adresse_complete
    }))
    
    const wb = XLSX.utils.book_new()
    const ws = XLSX.utils.json_to_sheet(excelData)
    XLSX.utils.book_append_sheet(wb, ws, 'Orders')
    XLSX.writeFile(wb, `orders_${Date.now()}.xlsx`)
    
    showNotificationMessage('success', 'Export Successful', 'Excel file generated successfully!')
  } catch (error) {
    console.error('Excel export error:', error)
    showNotificationMessage('error', 'Export Error', 'Error generating Excel file')
  }
}

// Click outside handler
const handleClickOutside = (event) => {
  if (exportDropdownRef.value && !exportDropdownRef.value.contains(event.target)) {
    showExportDropdown.value = false
  }
}

// Cloudinary upload function (placeholder)
const uploadToCloudinary = async (file) => {
  // Replace with your actual Cloudinary configuration and API call
  // For demonstration, simulate a network delay and return a dummy URL
  console.warn('Cloudinary upload not configured. Using dummy URL.');
  await new Promise(resolve => setTimeout(resolve, 1000)); // Simulate upload time
  // In a real scenario, you'd return the URL from Cloudinary response
  return URL.createObjectURL(file); // Fallback to local URL for demo
}


onMounted(() => {
  loadAllData()
  document.addEventListener('click', handleClickOutside)
})

onUnmounted(() => {
  document.removeEventListener('click', handleClickOutside)
})
</script>

<style scoped>
@keyframes float-slow {
  0%, 100% { transform: translateY(0px) translateX(0px); }
  50% { transform: translateY(-20px) translateX(10px); }
}
@keyframes float-reverse {
  0%, 100% { transform: translateY(0px) translateX(0px); }
  50% { transform: translateY(15px) translateX(-15px); }
}
@keyframes float-diagonal {
  0%, 100% { transform: translateY(0px) translateX(0px); }
  50% { transform: translateY(-15px) translateX(-10px); }
}
@keyframes float-slow-reverse {
  0%, 100% { transform: translateY(0px) translateX(0px); }
  50% { transform: translateY(10px) translateX(15px); }
}
@keyframes pulse-slow {
  0%, 100% {
    opacity: 1;
    box-shadow: 0 0 8px rgba(249, 115, 22, 0.5);
  }
  50% {
    opacity: 0.85;
    box-shadow: 0 0 20px rgba(249, 115, 22, 0.8);
  }
}
.animate-float-slow { animation: float-slow 20s ease-in-out infinite; }
.animate-float-reverse { animation: float-reverse 25s ease-in-out infinite; }
.animate-float-diagonal { animation: float-diagonal 18s ease-in-out infinite; }
.animate-float-slow-reverse { animation: float-slow-reverse 22s ease-in-out infinite; }
.animate-pulse-slow { animation: pulse-slow 2s ease-in-out infinite; }
</style>
