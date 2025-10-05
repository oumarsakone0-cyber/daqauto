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
        <span class="font-medium text-gray-700 truncate">Gestion des Commandes</span>
      </div>

      <!-- Header responsive -->
      <div class="flex flex-col space-y-4 lg:flex-row lg:items-center lg:justify-between lg:space-y-0 mb-6 sm:mb-8">
        <div>
          <h1 class="text-2xl sm:text-3xl font-bold text-gray-900 mb-2">Gestion des Commandes</h1>
          <p class="text-sm sm:text-base text-gray-600">Suivi et traitement des commandes en temps réel</p>
        </div>
        
        <!-- Actions mobiles optimisées -->
        <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-2 sm:gap-3">
          <!-- Export dropdown -->
          <div class="relative">
            <button 
              @click="showExportDropdown = !showExportDropdown"
              class="w-full sm:w-auto inline-flex items-center justify-center px-3 sm:px-4 py-2 rounded-lg shadow-sm text-sm font-medium btn-degrade-orange"
            >
               <DownloadIcon class="w-4 h-4 mr-2" />
              <span >Exporter</span>
              <ChevronDownIcon class="w-4 h-4 ml-2" />
            </button>
            <div v-if="showExportDropdown" class="origin-top-right absolute right-0 w-50 mt-2 ring-1 ring-gray-400 rounded-md shadow-lg bg-white p-2">
              <div  role="menu">
                <button @click="exportToPDF" class="flex items-center text-sm mb-2 text-gray-700 hover:bg-gray-100 btn-gray" role="menuitem">
                   <FileTextIcon class="w-4 h-4 mr-2 error-color" />
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
            class="w-full sm:w-auto inline-flex items-center justify-center px-3 sm:px-4 py-2 rounded-lg shadow-sm text-sm font-medium transition-all submit-btn"
          >
            <RefreshIcon class="w-4 h-4 mr-2" />
            Actualiser
          </button>
        </div>
      </div>

      <!-- Loading State -->
      <div v-if="dataLoading" class="mb-6 sm:mb-8">
        <div class="bg-white rounded-lg p-6 sm:p-8 text-center shadow-lg">
          <div class="inline-block animate-spin rounded-full h-10 w-10 sm:h-12 sm:w-12 border-4 border-orange-500 border-t-transparent"></div>
          <p class="mt-4 text-gray-600 font-medium text-sm sm:text-base">Chargement des commandes...</p>
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
            <button @click="loadAllData" class="w-full sm:w-auto px-4 py-2 error-background-color  rounded-lg  transition-colors font-medium text-sm">
              Réessayer
            </button>
          </div>
        </div>
      </div>

      <!-- Stats Cards responsive -->
      <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-6 mb-6 sm:mb-8" v-if="!dataLoading">
        <!-- Commandes Totales -->
        <div class="bg-white overflow-hidden shadow-lg rounded-lg border border-gray-100 hover:shadow-xl transition-all duration-300">
          <div class="px-4 sm:px-6 py-4 sm:py-6">
            <div class="flex items-center mb-3 sm:mb-4">
              <div class="w-10 h-10 sm:w-12 sm:h-12 bg-gradient-to-br from-orange-200 to-orange-300 rounded-lg flex items-center justify-center mr-3 sm:mr-4 flex-shrink-0">
                <ShoppingCartIcon class="w-5 h-5 text-black" />
              </div>
              <div class="min-w-0">
                <p class="text-xs sm:text-sm text-gray-600">Commandes Totales</p>
                <p class="text-xl sm:text-2xl font-bold text-gray-900">{{ stats.total_orders || 0 }}</p>
              </div>
            </div>
            <div class="space-y-2">
              <div class="flex justify-between text-xs text-gray-500">
                <span class="truncate">Croissance: +{{ stats.orders_growth || 0 }}%</span>
                <span class="text-orange-600 flex-shrink-0">Ce mois</span>
              </div>
              <div class="w-full bg-gray-200 rounded-full h-2">
                <div class="bg-gradient-to-r from-orange-500 to-orange-600 h-2 rounded-full transition-all duration-300" style="width: 75%"></div>
              </div>
            </div>
          </div>
        </div>

        <!-- En Attente -->
        <div class="bg-white overflow-hidden shadow-lg rounded-lg border border-gray-100 hover:shadow-xl transition-all duration-300">
          <div class="px-4 sm:px-6 py-4 sm:py-6">
            <div class="flex items-center mb-3 sm:mb-4">
              <div class="w-10 h-10 sm:w-12 sm:h-12 bg-gradient-to-br from-yellow-200 to-yellow-300 rounded-lg flex items-center justify-center mr-3 sm:mr-4 flex-shrink-0">
                <Clock class="w-5 h-5 text-black" />
              </div>
              <div class="min-w-0">
                <p class="text-xs sm:text-sm text-gray-600">En Attente</p>
                <p class="text-xl sm:text-2xl font-bold text-gray-900">{{ stats.pending_orders || 0 }}</p>
              </div>
            </div>
            <div class="space-y-2">
              <div class="flex justify-between text-xs text-gray-500">
                <span class="truncate">Réduction: -{{ stats.pending_reduction || 0 }}%</span>
                <span class="text-yellow-600 flex-shrink-0">Vs hier</span>
              </div>
              <div class="w-full bg-gray-200 rounded-full h-2">
                <div class="bg-gradient-to-r from-yellow-500 to-yellow-600 h-2 rounded-full transition-all duration-300" style="width: 60%"></div>
              </div>
            </div>
          </div>
        </div>

        <!-- Confirmées -->
        <div class="bg-white overflow-hidden shadow-lg rounded-lg border border-gray-100 hover:shadow-xl transition-all duration-300">
          <div class="px-4 sm:px-6 py-4 sm:py-6">
            <div class="flex items-center mb-3 sm:mb-4">
              <div class="w-10 h-10 sm:w-12 sm:h-12 bg-gradient-to-br from-green-200 to-green-300 rounded-lg flex items-center justify-center mr-3 sm:mr-4 flex-shrink-0">
                <CheckCircle2Icon class="w-5 h-45 text-black" />
              </div>
              <div class="min-w-0">
                <p class="text-xs sm:text-sm text-gray-600">Confirmées</p>
                <p class="text-xl sm:text-2xl font-bold text-gray-900">{{ stats.confirmed_orders || 0 }}</p>
              </div>
            </div>
            <div class="space-y-2">
              <div class="flex justify-between text-xs text-gray-500">
                <span class="truncate">Croissance: +{{ stats.confirmed_growth || 0 }}%</span>
                <span class="green-color flex-shrink-0">Aujourd'hui</span>
              </div>
              <div class="w-full bg-gray-200 rounded-full h-2">
                <div class="bg-gradient-to-r from-green-500 to-green-600 h-2 rounded-full transition-all duration-300" style="width: 85%"></div>
              </div>
            </div>
          </div>
        </div>

        <!-- Revenus du Jour -->
        <div class="bg-white overflow-hidden shadow-lg rounded-lg border border-gray-100 hover:shadow-xl transition-all duration-300">
          <div class="px-4 sm:px-6 py-4 sm:py-6">
            <div class="flex items-center mb-3 sm:mb-4">
              <div class="w-10 h-10 sm:w-12 sm:h-12 bg-gradient-to-br from-purple-200 to-purple-300 rounded-lg flex items-center justify-center mr-3 sm:mr-4 flex-shrink-0">
                <BadgeEuroIcon class="w-5 h-45 text-black" />
              </div>
              <div class="min-w-0">
                <p class="text-xs sm:text-sm text-gray-600">Revenus du Jour</p>
                <p class="text-xl sm:text-2xl font-bold text-gray-900">{{ formatCurrency(stats.daily_revenue || 0) }}</p>
              </div>
            </div>
            <div class="space-y-2">
              <div class="flex justify-between text-xs text-gray-500">
                <span class="truncate">Croissance: +{{ stats.revenue_growth || 0 }}%</span>
                <span class="purple-color flex-shrink-0">Vs hier</span>
              </div>
              <div class="w-full bg-gray-200 rounded-full h-2">
                <div class="bg-gradient-to-r from-purple-500 to-purple-600 h-2 rounded-full transition-all duration-300" style="width: 70%"></div>
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
          <div class="flex space-y-3 sm:space-y-0 sm:flex-row sm:items-center sm:justify-between sm:gap-4">
            <div class="relative flex-1 max-w-full sm:max-w-md">
              <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                <Search  class="w-4 h-4 text-gray-500" />
              </div>
              <input 
                v-model="searchQuery"
                type="text" 
                class="input-style" 
                placeholder="Rechercher par numéro, client, produit..."
                @input="handleSearch"
              >
            </div>
            <div class="flex items-center gap-2 sm:gap-3">
              <div class="w-45">

                <select 
                  v-model="statusFilter" 
                  @change="loadOrders" 
                  class=" input-style">
                  <option value="" class="px-4">Tous les statuts</option>
                  <option value="en_attente">En attente</option>
                  <option value="confirmee">Confirmée</option>
                  <option value="en_livraison">En livraison</option>
                  <option value="livree">Livrée</option>
                  <option value="annulee">Annulée</option> 
                </select>
              </div>
              <div class="w-45">

                <select 
                  v-model="dateFilter" 
                  @change="loadOrders" 
                  class="input-style">
                  <option value="">Toutes les dates</option>
                  <option value="today">Aujourd'hui</option>
                  <option value="yesterday">Hier</option>
                  <option value="week">Cette semaine</option>
                  <option value="month">Ce mois</option>
                </select>
              </div>
              <button v-if="hasActiveFilters" @click="clearFilters" class="btn-deconnexion w-30">
                <X class="w-4 h-4 sm:hidden" />
                <span class="hidden sm:inline text-xs">Effacer Filtres</span>
              </button>
            </div>
          </div>
        </div>

        <!-- Orders Table responsive -->
        <div class="overflow-x-auto">
          <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
              <tr>
                <th scope="col" class="px-3 sm:px-6 py-2 sm:py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Commande</th>
                <th scope="col" class="px-3 sm:px-6 py-2 sm:py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Client</th>
                <th scope="col" class="hidden md:table-cell px-3 sm:px-6 py-2 sm:py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider" style="width: 150px; max-width: 150px;">Produit</th>
                <th scope="col" class="px-3 sm:px-6 py-2 sm:py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Livraison</th>
                <th scope="col" class="px-3 sm:px-6 py-2 sm:py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Montant</th>
                <th scope="col" class="hidden lg:table-cell px-3 sm:px-6 py-2 sm:py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Statut</th>
                <th scope="col" class="px-3 sm:px-6 py-2 sm:py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
              </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
              <tr v-for="order in paginatedOrders" :key="order.id" class="hover:bg-gray-50 transition-colors">
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
                      <span>Qté: {{ order.quantite }}</span>
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
                      <div>Sous-total: {{ formatCurrency(order.sous_total) }}</div>
                      <div>Livraison: {{ formatCurrency(order.frais_livraison) }}</div>
                    </div>
                  </div>
                </td>
                <td class="hidden lg:table-cell px-3 sm:px-6 py-3 sm:py-4 whitespace-nowrap">
                  <span :class="['inline-flex items-center gap-1 px-2 py-1 rounded-full text-xs font-medium', getStatusClass(order.statut)]">
                    <component :is="getStatusIcon(order.statut)" class="h-3 w-3" />
                    {{ getStatusLabel(order.statut), console.log(order.statut)  }}
                  </span>
                </td>
                <td class="px-3 sm:px-6 py-3 sm:py-4 whitespace-nowrap text-xs sm:text-sm font-medium">
                  <div class="flex items-center space-x-1 sm:space-x-2">
                    <button 
                      v-if="order.statut === 'en_attente'" 
                      @click.stop="showConfirmModal('confirm', order)"
                      class="p-1.5 sm:p-2 rounded-lg transition-colors text-xs"
                      title="Confirmer"
                      style="background-color: rgba(74, 222, 128, 0.1); color: rgb(22, 163, 74);"
                    >
                    <Check class="h-3 w-3 sm:h-4 sm:w-4 text-black" />
                    </button>
                    <button 
                      v-if="order.statut === 'confirmee'" 
                      @click.stop="showConfirmModal('ship', order)"
                      class="p-1.5 sm:p-2 rounded-lg transition-colors text-xs"
                      title="Expédier"
                      style="background-color: rgba(147, 197, 253, 0.1); color: rgb(37, 99, 235);"
                    >
                    <Truck class="h-3 w-3 sm:h-4 sm:w-4  text-black" />
                    </button>
                    <button 
                      v-if="order.statut === 'en_livraison'" 
                      @click.stop="showConfirmModal('deliver', order)"
                      class="p-1.5 sm:p-2 rounded-lg transition-colors text-xs"
                      title="Livrer"
                      style="background-color: rgba(191, 144, 255, 0.1); color: rgb(99, 37, 235);"
                    >
                    <ArchiveIcon class="h-3 w-3 sm:h-4 sm:w-4 text-black" />
                    </button>
                    <button 
                      v-if="['en_attente', 'confirmee'].includes(order.statut)" 
                      @click.stop="showConfirmModal('cancel', order)"
                      class="p-1.5 sm:p-2 rounded-lg transition-colors text-xs"
                      title="Annuler"
                      style="background-color: rgba(248, 113, 113, 0.1); color: rgb(220, 38, 38);"
                    >
                    <BookmarkXIcon class="h-3 w-3 sm:h-4 sm:w-4 text-black" />
                    </button>
                    <button 
                      @click.stop="openOrderDetails(order)" 
                      class="p-1.5 sm:p-2 rounded-lg transition-colors text-xs"
                      title="Voir détails"
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
                {{ Math.min(currentPage * itemsPerPage, filteredOrders.length) }} 
                sur {{ filteredOrders.length }} résultats
              </span>
              <span class="sm:hidden">
                {{ ((currentPage - 1) * itemsPerPage) + 1 }}-{{ Math.min(currentPage * itemsPerPage, filteredOrders.length) }} 
                / {{ filteredOrders.length }}
              </span>
            </span>
          </div>
          <div class="flex items-center justify-center sm:justify-end space-x-1 sm:space-x-2">
            <button 
              @click="changePage(currentPage - 1)"
              :disabled="isFirstPage"
              class="relative inline-flex items-center px-2 sm:px-3 py-1.5 sm:py-2 border border-gray-300 text-xs sm:text-sm font-medium rounded-lg text-gray-700 bg-white hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed transition-colors"
            >
            <ChevronLeft class="h-3 w-3 sm:h-4 sm:w-4 mr-1 text-black" />
              <span class="hidden sm:inline">Précédent</span>
              <span class="sm:hidden">Préc</span>
            </button>
            <div class="flex items-center space-x-1">
              <button 
                v-for="page in Math.min(3, totalPages)" 
                :key="page"
                @click="changePage(page)"
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
              @click="changePage(currentPage + 1)"
              :disabled="isLastPage"
              class="relative inline-flex items-center px-2 sm:px-3 py-1.5 sm:py-2 border border-gray-300 text-xs sm:text-sm font-medium rounded-lg text-gray-700 bg-white hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed transition-colors"
            >
              <span class="hidden sm:inline">Suivant</span>
              <span class="sm:hidden">Suiv</span>
              <ChevronRight class="h-3 w-3 sm:h-4 sm:w-4 mr-1 text-black" />
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
            <div :class="['w-12 h-12 rounded-full flex items-center justify-center mr-4', getConfirmationIconClass()]">
              <!-- < :src="getConfirmationIcon()" alt="Icon" class="w-6 h-6" /> -->
               <CheckCircle class="h-3 w-3" />
               <!-- TODO: Ajouter les icones -->
              <!-- <component :is="getConfirmationIcon()" class="h-3 w-3" /> -->
            </div>
            <div>
              <h3 class="text-lg font-semibold text-gray-900">{{ getConfirmationTitle() }}</h3>
              <p class="text-sm text-gray-500">Commande #{{ selectedOrder?.numero_commande }}</p>
            </div>
          </div>
          <p class="text-gray-600 mb-6">{{ getConfirmationMessage() }}</p>
          <div class="flex justify-end space-x-3">
            <button 
              @click="closeConfirmModal"
              class="btn-gray"
            >
              Annuler
            </button>
            <button 
              @click="executeAction"
              :class="['btn-degrade-orange', getConfirmationButtonClass()]"
            >
              {{ getConfirmationButtonText() }}
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- Enhanced Order Details Modal -->
    <div v-if="showOrderModal" class="fixed inset-0 bg-black/60 backdrop-blur-sm flex items-center justify-center z-50 p-4" @click="closeOrderModal">
      <div class="bg-white rounded-2xl shadow-2xl w-full max-w-4xl max-h-[90vh] overflow-y-auto" @click.stop>
        <div class="sticky top-0 bg-white border-b border-gray-100 px-8 py-6 rounded-t-2xl">
          <div class="flex items-center justify-between">
            <div class="flex items-center gap-4">
              <div class="p-3 bg-orange rounded-xl">
                <FileTextIcon class="h-6 w-6 text-white" />
              </div>
              <h3 class="text-2xl font-bold text-gray-900">Détails de la Commande</h3>
            </div>
            <XIcon @click="closeOrderModal" class="h-7 w-7 cursor-pointer text-gray-700" />
          </div>
        </div>

        <div v-if="selectedOrder" class="p-8 space-y-8">
          <!-- Enhanced Order Summary -->
          <div class="bg-gradient-to-r from-orange-50 to-orange-100 rounded-2xl p-6 border border-orange-200">
            <div class="flex items-center justify-between mb-6">
              <div class="flex items-center gap-4">
                <div class="p-4 bg-orange rounded-2xl">
                  <FileCheck class="h-7 w-7 text-white" />
                </div>
                <div>
                  <div class="text-2xl font-bold text-gray-900">{{ selectedOrder.numero_commande }}</div>
                  <div class="text-gray-600">{{ formatDate(selectedOrder.date_commande) }} à {{ formatTime(selectedOrder.date_commande) }}</div>
                </div>
              </div>
              <span :class="['inline-flex items-center gap-2 px-4 py-2 rounded-full text-sm font-medium', getStatusClass(selectedOrder.statut)]">
                <component :is="getStatusIcon(selectedOrder.statut)" class="h-3 w-3" />
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
                  <div class="text-sm text-gray-600">Quantité</div>
                  <div class="text-xl font-bold text-gray-900">{{ selectedOrder.quantite }}</div>
                </div>
              </div>
              <div class="bg-white/70 rounded-xl p-4 flex items-center gap-3">
                <Truck class="h-6 w-6 primary-color" />
                <div>
                  <div class="text-sm text-gray-600">Livraison</div>
                  <div class="text-xl font-bold text-gray-900">{{ formatCurrency(selectedOrder.frais_livraison) }}</div>
                </div>
              </div>
            </div>
          </div>

          <!-- Enhanced Info Sections -->
          <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            <!-- Customer Information -->
            <div class="bg-white rounded-2xl p-6 border border-gray-100 shadow-sm">
              <div class="flex items-center gap-3 mb-4">
                <User class="h-6 w-6 primary-color" />
                <h4 class="text-lg font-semibold text-gray-900">Informations Client</h4>
              </div>
              <div class="space-y-3">
                <div class="flex items-center gap-3">
                  <User2 class="h-4 w-4 text-gray-500" />
                  <div>
                    <span class="text-sm text-gray-500">Nom complet</span>
                    <div class="font-medium text-gray-900">{{ selectedOrder.client_nom }}</div>
                  </div>
                </div>
                <div class="flex items-center gap-3">
                  <PhoneCall class="h-4 w-4 text-gray-400" />
                  <div>
                    <span class="text-sm text-gray-500">Téléphone</span>
                    <div class="font-medium text-gray-900">{{ selectedOrder.client_telephone }}</div>
                  </div>
                </div>
              </div>
            </div>

            <!-- Delivery Information -->
            <div class="bg-white rounded-2xl p-6 border border-gray-100 shadow-sm">
              <div class="flex items-center gap-3 mb-4">
                <Truck class="h-6 w-6 primary-color" />
                <h4 class="text-lg font-semibold text-gray-900">Informations de Livraison</h4>
              </div>
              <div class="space-y-3">
                <div class="flex items-center gap-3">
                  <Truck class="h-4 w-4 text-gray-400" />
                  <div>
                    <span class="text-sm text-gray-500">Type de livraison</span>
                    <div class="font-medium text-gray-900">{{ getDeliveryTypeLabel(selectedOrder.type_livraison) }}</div>
                  </div>
                </div>
                <div v-if="selectedOrder.commune" class="flex items-center gap-3">
                  <MapPin class="h-4 w-4 text-gray-400" />
                  <div>
                    <span class="text-sm text-gray-500">Commune</span>
                    <div class="font-medium text-gray-900">{{ selectedOrder.commune }}</div>
                  </div>
                </div>
                <div class="flex items-start gap-3">
                  <Home class="h-4 w-4 text-gray-400 mt-1" />
                  <div>
                    <span class="text-sm text-gray-500">Adresse complète</span>
                    <div class="font-medium text-gray-900">{{ selectedOrder.adresse_complete }}</div>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- Product Information Enhanced -->
          <div class="bg-white rounded-2xl p-6 border border-gray-100 shadow-sm">
            <div class="flex items-center gap-3 mb-6">
              <BoxIcon class="h-6 w-6 primary-color" />
              <h4 class="text-lg font-semibold text-gray-900">Produit Commandé</h4>
            </div>
            <div class="flex items-start gap-6">
              <div class="w-20 h-20 bg-gray-100 rounded-xl flex items-center justify-center">
                <BoxIcon class="h-8 w-8 text-gray-500" />
              </div>
              <div class="flex-1">
                <h5 class="text-xl font-semibold text-gray-900 mb-3">{{ selectedOrder.produit_nom }}</h5>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-sm">
                  <div class="flex items-center gap-2 text-gray-400">
                    <BoxIcon class="h-4 w-4 text-gray-400" />
                    <span>Quantité: {{ selectedOrder.quantite }}</span>
                  </div>
                  <div class="flex items-center gap-2 text-gray-400">
                    <Banknote class="h-4 w-4 text-gray-400" />
                    <span>Prix unitaire: {{ formatCurrency(selectedOrder.produit_prix) }}</span>
                  </div>
                  <div class="flex items-center gap-2 text-gray-400">
                    <Building class="h-4 w-4 text-gray-400" />
                    <span>Boutique: {{ selectedOrder.boutique_nom }}</span>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- Boutique Information Enhanced -->
          <div class="bg-white rounded-2xl p-6 border border-gray-100 shadow-sm">
            <div class="flex items-center gap-3 mb-6">
                <Building class="h-6 w-6 primary-color" />
              <h4 class="text-lg font-semibold text-gray-900">Informations de la Boutique</h4>
            </div>
            <div class="space-y-4">
              <div class="flex items-center gap-4">
                <div class="w-12 h-12 bg-gray-100 rounded-xl flex items-center justify-center">
                  <Building class="h-8 w-8 text-gray-500" />
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
                  <h6 class="text-sm font-semibold text-gray-700 mb-3">Contact</h6>
                  <div class="space-y-2">
                    <div v-if="selectedOrder.boutique_telephone" class="flex items-center gap-2 text-sm text-gray-400">
                      <PhoneCall class="h-4 w-4 text-gray-400" />
                      <span>{{ selectedOrder.boutique_telephone }}</span>
                    </div>
                    <div v-if="selectedOrder.boutique_email" class="flex items-center gap-2 text-sm text-gray-400">
                      <Mail class="h-4 w-4 text-gray-400" />
                      <span>{{ selectedOrder.boutique_email }}</span>
                    </div>
                  </div>
                </div>
                
                <div v-if="selectedOrder.boutique_address || selectedOrder.boutique_commune">
                  <h6 class="text-sm font-semibold text-gray-700 mb-3">Localisation</h6>
                  <div class="space-y-2">
                    <div v-if="selectedOrder.boutique_address" class="flex items-center gap-2 text-sm">
                      <MapPin class="h-4 w-4 text-gray-400" />
                      <span>{{ selectedOrder.boutique_address }}</span>
                    </div>
                    <div v-if="selectedOrder.boutique_commune" class="flex items-center gap-2 text-sm">
                      <Building class="h-4 w-4 text-gray-400" />
                      <span>{{ selectedOrder.boutique_commune }}</span>
                    </div>
                  </div>
                </div>
              </div>
              
              <!-- Actions boutique -->
              <div class="flex flex-wrap gap-3 pt-4 border-t border-gray-100">
                <button v-if="selectedOrder.boutique_telephone" @click="callBoutique(selectedOrder.boutique_telephone)" class="flex items-center gap-2 px-4 py-2  rounded-lg  transition-colors duration-200">
                  <PhoneCall class="h-4 w-4" />
                  <span>Appeler</span>
                </button>
                <button v-if="selectedOrder.boutique_email" @click="emailBoutique(selectedOrder.boutique_email)" class="flex items-center gap-2 px-4 py-2 rounded-lg  transition-colors duration-200">
                  <Mail class="h-4 w-4" />
                  <span>Email</span>
                </button>
                <button @click="notifyBoutiqueNewOrder(selectedOrder)" class="flex items-center gap-2 px-4 py-2  rounded-lg  transition-colors duration-200">
                 <Bell class="h-4 w-4" />
                  <span>Notifier Commande</span>
                </button>
                <button @click="notifyClientOrderStatus(selectedOrder)" class="flex items-center gap-2 px-4 py-2  rounded-lg  transition-colors duration-200">
                 <MessageSquare class="h-4 w-4" />
                  <span>Notifier Client</span>
                </button>
                <button @click="generateReceiptPDF(selectedOrder)" class="flex items-center gap-2 px-4 py-2 rounded-lg  transition-colors duration-200">
                  <FileDownIcon class="h-4 w-4" />
                  <span>Reçu PDF</span>
                </button>
              </div>
            </div>
          </div>

          <!-- Financial Summary Enhanced -->
          <div class="bg-white rounded-2xl p-6 border border-gray-100 shadow-sm">
            <div class="flex items-center gap-3 mb-6">
              <Banknote class="h-6 w-6 primary-color" />
              <h4 class="text-lg font-semibold text-gray-900">Récapitulatif Financier</h4>
            </div>
            <div class="space-y-4">
              <div class="flex justify-between items-center py-2">
                <div class="flex items-center gap-2">
                  <CalculatorIcon class="h-4 w-4 text-gray-400" />
                  <span class="text-gray-600">Sous-total</span>
                </div>
                <span class="font-medium text-gray-900">{{ formatCurrency(selectedOrder.sous_total) }}</span>
              </div>
              <div class="flex justify-between items-center py-2">
                <div class="flex items-center gap-2">
                  <Truck class="h-4 w-4 text-gray-400" />
                  <span class="text-gray-600">Frais de livraison</span>
                </div>
                <span class="font-medium text-gray-900">{{ formatCurrency(selectedOrder.frais_livraison) }}</span>
              </div>
              <div class="flex justify-between items-center py-3 border-t border-gray-100">
                <div class="flex items-center gap-2">
                  <Banknote class="h-6 w-6 text-black font-bold" />
                  <span class="text-lg font-semibold text-gray-900">Total</span>
                </div>
                <span class="text-xl font-bold text-gray-900">{{ formatCurrency(selectedOrder.total) }}</span>
              </div>
            </div>
          </div>

          <!-- Enhanced Order Actions -->
          <div class="flex flex-wrap gap-4 justify-end pt-6 border-t border-gray-100">
            <button 
              v-if="selectedOrder.statut === 'en_attente'" 
              @click="showConfirmModal('confirm', selectedOrder)"
              class="submit-btn"
            >
              <span>Confirmer la commande</span>
            </button>
            <button 
              v-if="selectedOrder.statut === 'confirmee'" 
              @click="showConfirmModal('ship', selectedOrder)"
              class="submit-btn"
            >
              <span>Marquer comme expédiée</span>
            </button>
            <button 
              v-if="selectedOrder.statut === 'en_livraison'" 
              @click="showConfirmModal('deliver', selectedOrder)"
              class="submit-btn"
            >
              <span>Marquer comme livrée</span>
            </button>
            <button 
              v-if="['en_attente', 'confirmee'].includes(selectedOrder.statut)" 
              @click="showConfirmModal('cancel', selectedOrder)"
              class="btn-deconnexion"
            >
              <span>Annuler la commande</span>
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- Notification Toast responsive -->
    <div v-if="showNotification" :class="['fixed bottom-4 right-4 left-4 sm:left-auto z-50 bg-gradient-to-r text-white py-3 sm:py-4 px-4 sm:px-6 rounded-lg shadow-2xl transition-all duration-300 transform', getNotificationClass(notificationType)]">
      <div class="flex items-center gap-2 sm:gap-3">
        <img :src="getNotificationIcon(notificationType)" :alt="notificationType" class="w-4 h-4 sm:w-5 sm:h-5 flex-shrink-0" />
        <span class="text-sm sm:text-base">{{ notificationMessage }}</span>
        <button @click="closeNotification" class="ml-auto p-1 hover:bg-white/20 rounded transition-colors duration-200">
        </button>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted, watch } from 'vue'
import NavbarDashboard from '../menu/navbar-dashboard.vue'
import Navbar from '../boutiques/Navbar.vue'
import axios from 'axios'
import jsPDF from 'jspdf'
import * as XLSX from 'xlsx'
import { useRouter } from 'vue-router'
// Icônes Lucide Vue Next
import { 
  Home as HomeIcon,
  Download as DownloadIcon,
  ChevronDown as ChevronDownIcon,
  FileText as FileTextIcon,
  ShoppingCart as ShoppingCartIcon,
  Package as PackageIcon,
  Eye as EyeIcon,
  X as XIcon,
  RefreshCcw as RefreshIcon,
  CircleAlertIcon as WarningIcon,
  Clock,
  CheckCircle2Icon,
  BadgeEuroIcon,
  Search,
  X,
  Calendar,
  User,
  PhoneCall,
  Building,
  Truck,
  MapPin,
  Check,
  ArchiveIcon,
  BookmarkXIcon,
  ChevronLeft,
  ChevronRight,
  FileCheck,
  Banknote,
  BoxIcon,
  User2,
  Home,
  Mail,
  Bell,
  MessageSquare,
  FileDownIcon,
  CalculatorIcon,
  CheckCircle
} from 'lucide-vue-next'

const router = useRouter()

// API Base URL
const API_BASE_URL = 'https://sastock.com/api_adjame'

// Reactive data
const dataLoading = ref(true)
const orders = ref([])
const selectedOrder = ref(null)
const showOrderModal = ref(false)
const showConfirmationModal = ref(false)
const confirmationAction = ref(null)
const hasError = ref(null)
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

// Computed properties pour les filtres
const filterTabs = [
  { value: 'all', label: 'Toutes' },
  { value: 'en_attente', label: 'En attente' },
  { value: 'confirmee', label: 'Confirmées' },
  { value: 'en_livraison', label: 'En livraison' },
  { value: 'livree', label: 'Livrées' },
  { value: 'annulee', label: 'Annulées' }
]

const activeFilter = ref('all')
const showExportDropdown = ref(false)

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

const isFirstPage = computed(() => currentPage.value <= 1)
const isLastPage = computed(() => currentPage.value >= totalPages.value)

const handleFilterChange = (filterValue) => {
  activeFilter.value = filterValue
  currentPage.value = 1
}

const handleItemsPerPageChange = () => {
  currentPage.value = 1
}

const getNotificationClass = (type) => {
  const classes = {
    'success': 'from-green-500 to-green-600',
    'warning': 'from-yellow-500 to-yellow-600',
    'error': 'from-red-500 to-red-600',
    'info': 'from-blue-500 to-blue-600'
  }
  return classes[type] || 'from-gray-500 to-gray-600'
}

// Computed properties
const filteredOrders = computed(() => {
  let filtered = [...orders.value]

  // Search filter
  if (searchQuery.value) {
    const query = searchQuery.value.toLowerCase()
    filtered = filtered.filter(order => 
      order.numero_commande.toLowerCase().includes(query) ||
      order.client_nom.toLowerCase().includes(query) ||
      order.client_telephone.includes(query) ||
      order.produit_nom.toLowerCase().includes(query) ||
      order.boutique_nom.toLowerCase().includes(query)
    )
  }

  // Status filter
  if (statusFilter.value) {
    filtered = filtered.filter(order => order.statut === statusFilter.value)
  } else if (activeFilter.value !== 'all') {
    filtered = filtered.filter(order => order.statut === activeFilter.value)
  }

  // Date filter
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

  // Sort
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

// API Methods
const loadStats = async () => {
  try {
    console.log('🔄 Chargement des statistiques des commandes...')
    const response = await axios.get(`${API_BASE_URL}/commandes.php?action=stats&_=${Date.now()}`)
    
    if (response.data.success) {
      stats.value = response.data.data
      console.log('✅ Statistiques chargées:', stats.value)
    } else {
      console.error('❌ Erreur lors du chargement des statistiques:', response.data.error)
    }
  } catch (error) {
    console.error('❌ Erreur lors du chargement des statistiques:', error)
    hasError.value = true
    error.value = 'Erreur lors du chargement des statistiques. Veuillez réessayer.'
  }
}

const loadOrders = async () => {
  try {
    console.log('🔄 Chargement des commandes...')
    const params = new URLSearchParams({
      action: 'list',
      limit: 100,
      _: Date.now()
    })

    if (statusFilter.value) params.append('status', statusFilter.value)
    if (dateFilter.value) params.append('date_filter', dateFilter.value)
    if (sortBy.value) params.append('sort', sortBy.value)

    const response = await axios.get(`${API_BASE_URL}/commandes.php?${params}`)
    
    if (response.data.success) {
      orders.value = response.data.data || []
      console.log('✅ Commandes chargées:', orders.value.length)
    } else {
      console.error('❌ Erreur lors du chargement des commandes:', response.data.error)
      orders.value = []
    }
  } catch (error) {
    console.error('❌ Erreur lors du chargement des commandes:', error)
    hasError.value = true
    error.value = 'Erreur lors du chargement des commandes. Veuillez réessayer.'
    orders.value = []
  }
}

const loadAllData = async () => {
  dataLoading.value = true
  hasError.value = false
  error.value = ''
  try {
    await Promise.all([loadStats(), loadOrders()])
  } catch (e) {
    hasError.value = true
    error.value = 'Erreur lors du chargement des données. Veuillez réessayer.'
  } finally {
    dataLoading.value = false
  }
}

// Confirmation Modal Methods
const showConfirmModal = (action, order) => {
  confirmationAction.value = action
  selectedOrder.value = order
  showConfirmationModal.value = true
}

const closeConfirmModal = () => {
  showConfirmationModal.value = false
  confirmationAction.value = null
  selectedOrder.value = null
}

const getConfirmationTitle = () => {
  switch (confirmationAction.value) {
    case 'confirm': return 'Confirmer la commande'
    case 'ship': return 'Expédier la commande'
    case 'deliver': return 'Livrer la commande'
    case 'cancel': return 'Annuler la commande'
    default: return ''
  }
}

const getConfirmationMessage = () => {
  switch (confirmationAction.value) {
    case 'confirm': return 'Êtes-vous sûr de vouloir confirmer cette commande ?'
    case 'ship': return 'Êtes-vous sûr de vouloir marquer cette commande comme expédiée ?'
    case 'deliver': return 'Êtes-vous sûr de vouloir marquer cette commande comme livrée ?'
    case 'cancel': return 'Êtes-vous sûr de vouloir annuler cette commande ? Cette action est irréversible.'
    default: return ''
  }
}

const getConfirmationIcon = () => {
  switch (confirmationAction.value) {
    default: return 'CheckCircle2Icon'
  }
}

const getConfirmationIconClass = () => {
  switch (confirmationAction.value) {
    case 'confirm': return 'bg-green-100 text-green-600'
    case 'ship': return 'bg-blue-100 text-blue-600'
    case 'deliver': return 'bg-purple-100 text-purple-600'
    case 'cancel': return 'bg-red-100 text-red-600'
    default: return 'bg-gray-100 text-gray-600'
  }
}

const getConfirmationButtonClass = () => {
  switch (confirmationAction.value) {
    case 'confirm': return 'bg-green-600 hover:bg-green-700'
    case 'ship': return 'bg-blue-600 hover:bg-blue-700'
    case 'deliver': return 'bg-purple-600 hover:bg-purple-700'
    case 'cancel': return 'bg-red-600 hover:bg-red-700'
    default: return 'bg-gray-600 hover:bg-gray-700'
  }
}

const getConfirmationButtonText = () => {
  switch (confirmationAction.value) {
    case 'confirm': return 'Confirmer'
    case 'ship': return 'Expédier'
    case 'deliver': return 'Livrer'
    case 'cancel': return 'Annuler Commande'
    default: return 'Confirmer'
  }
}

const executeAction = async () => {
  if (!selectedOrder.value || !confirmationAction.value) return

  try {
    let newStatus = ''
    let successMessage = ''
    
    switch (confirmationAction.value) {
      case 'confirm':
        newStatus = 'confirmee'
        successMessage = 'Commande confirmée avec succès'
        await confirmOrder(selectedOrder.value.id)
        break
      case 'ship':
        newStatus = 'en_livraison'
        successMessage = 'Commande expédiée avec succès'
        await markAsShipped(selectedOrder.value.id)
        break
      case 'deliver':
        newStatus = 'livree'
        successMessage = 'Commande livrée avec succès'
        await markAsDelivered(selectedOrder.value.id)
        break
      case 'cancel':
        newStatus = 'annulee'
        successMessage = 'Commande annulée avec succès'
        await cancelOrder(selectedOrder.value.id)
        break
    }
    
    closeConfirmModal()
    
  } catch (error) {
    console.error('Erreur lors de la mise à jour:', error)
    showNotificationMessage('error', 'Erreur', 'Erreur lors de la mise à jour de la commande')
  }
}

// Order Actions
const confirmOrder = async (orderId) => {
  try {
    const response = await axios.put(`${API_BASE_URL}/commandes.php?action=confirm&id=${orderId}&_=${Date.now()}`)
    
    if (response.data.success) {
      showNotificationMessage('success', 'Commande confirmée', 'La commande a été confirmée avec succès')
      await loadAllData()
      if (selectedOrder.value && selectedOrder.value.id === orderId) {
        selectedOrder.value.statut = 'confirmee'
      }
    } else {
      showNotificationMessage('error', 'Erreur', response.data.error || 'Erreur lors de la confirmation')
    }
  } catch (error) {
    console.error('Erreur lors de la confirmation:', error)
    showNotificationMessage('error', 'Erreur', 'Erreur lors de la confirmation de la commande')
  }
}

const markAsShipped = async (orderId) => {
  try {
    const response = await axios.put(`${API_BASE_URL}/commandes.php?action=ship&id=${orderId}&_=${Date.now()}`)
    
    if (response.data.success) {
      showNotificationMessage('success', 'Commande expédiée', 'La commande a été marquée comme expédiée')
      await loadAllData()
      if (selectedOrder.value && selectedOrder.value.id === orderId) {
        selectedOrder.value.statut = 'en_livraison'
      }
    } else {
      showNotificationMessage('error', 'Erreur', response.data.error || 'Erreur lors de l\'expédition')
    }
  } catch (error) {
    console.error('Erreur lors de l\'expédition:', error)
    showNotificationMessage('error', 'Erreur', 'Erreur lors de l\'expédition de la commande')
  }
}

const markAsDelivered = async (orderId) => {
  try {
    const response = await axios.put(`${API_BASE_URL}/commandes.php?action=deliver&id=${orderId}&_=${Date.now()}`)
    
    if (response.data.success) {
      showNotificationMessage('success', 'Commande livrée', 'La commande a été marquée comme livrée')
      await loadAllData()
      if (selectedOrder.value && selectedOrder.value.id === orderId) {
        selectedOrder.value.statut = 'livree'
      }
    } else {
      showNotificationMessage('error', 'Erreur', response.data.error || 'Erreur lors de la livraison')
    }
  } catch (error) {
    console.error('Erreur lors de la livraison:', error)
    showNotificationMessage('error', 'Erreur', 'Erreur lors de la livraison de la commande')
  }
}

const cancelOrder = async (orderId) => {
  try {
    const response = await axios.put(`${API_BASE_URL}/commandes.php?action=cancel&id=${orderId}&_=${Date.now()}`)
    
    if (response.data.success) {
      showNotificationMessage('success', 'Commande annulée', 'La commande a été annulée avec succès')
      await loadAllData()
      if (selectedOrder.value && selectedOrder.value.id === orderId) {
        selectedOrder.value.statut = 'annulee'
      }
    } else {
      showNotificationMessage('error', 'Erreur', response.data.error || 'Erreur lors de l\'annulation')
    }
  } catch (error) {
    console.error('Erreur lors de l\'annulation:', error)
    showNotificationMessage('error', 'Erreur', 'Erreur lors de l\'annulation de la commande')
  }
}

// UI Methods
const openOrderDetails = (order) => {
  selectedOrder.value = order
  showOrderModal.value = true
}

const closeOrderModal = () => {
  showOrderModal.value = false
  selectedOrder.value = null
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

// Export Excel (XLSX)
const exportToExcel = () => {
  try {
    showNotificationMessage('info', 'Export Excel', 'Génération du fichier Excel en cours...')
    showExportDropdown.value = false
    
    // Préparer les données pour Excel
    const excelData = filteredOrders.value.map(order => ({
      'Numéro Commande': order.numero_commande,
      'Date': formatDate(order.date_commande),
      'Heure': formatTime(order.date_commande),
      'Client': order.client_nom,
      'Téléphone': order.client_telephone,
      'Produit': order.produit_nom,
      'Quantité': order.quantite,
      'Prix Unitaire': order.produit_prix,
      'Sous-total': order.sous_total,
      'Frais Livraison': order.frais_livraison,
      'Total': order.total,
      'Statut': getStatusLabel(order.statut),
      'Type Livraison': getDeliveryTypeLabel(order.type_livraison),
      'Commune': order.commune || order.ville,
      'Adresse': order.adresse_complete,
      'Boutique': order.boutique_name || order.boutique_nom
    }))

    // Créer le workbook et worksheet
    const wb = XLSX.utils.book_new()
    const ws = XLSX.utils.json_to_sheet(excelData)
    
    // Définir les largeurs de colonnes
    const colWidths = [
      { wch: 15 }, // Numéro Commande
      { wch: 12 }, // Date
      { wch: 8 },  // Heure
      { wch: 20 }, // Client
      { wch: 15 }, // Téléphone
      { wch: 30 }, // Produit
      { wch: 8 },  // Quantité
      { wch: 12 }, // Prix Unitaire
      { wch: 12 }, // Sous-total
      { wch: 12 }, // Frais Livraison
      { wch: 12 }, // Total
      { wch: 12 }, // Statut
      { wch: 15 }, // Type Livraison
      { wch: 15 }, // Commune
      { wch: 30 }, // Adresse
      { wch: 20 }  // Boutique
    ]
    ws['!cols'] = colWidths
    
    // Ajouter la feuille au workbook
    XLSX.utils.book_append_sheet(wb, ws, 'Commandes')
    
    // Générer le nom de fichier avec la date
    const now = new Date()
    const dateStr = now.toISOString().split('T')[0]
    const filename = `Commandes_DaqAuto_${dateStr}.xlsx`
    
    // Sauvegarder le fichier
    XLSX.writeFile(wb, filename)
    
    showNotificationMessage('success', 'Export Excel réussi', `${excelData.length} commandes exportées en Excel`)
    
  } catch (error) {
    console.error('Erreur lors de l\'export Excel:', error)
    showNotificationMessage('error', 'Erreur Export', 'Erreur lors de l\'export Excel')
  }
}

// Export PDF
const exportToPDF = () => {
  try {
    showNotificationMessage('info', 'Export PDF', 'Génération du fichier PDF en cours...')
    showExportDropdown.value = false
    
    const doc = new jsPDF('l', 'mm', 'a4') // Format paysage
    
    // Configuration des couleurs
    const primaryColor = [246, 90, 17] // #F65A11
    const darkColor = [51, 51, 51]
    const lightGray = [248, 249, 250]
    
    // En-tête du document
    doc.setFillColor(...primaryColor)
    doc.rect(0, 0, 297, 25, 'F')
    
    // Logo et titre
    doc.setTextColor(255, 255, 255)
    doc.setFontSize(18)
    doc.setFont('helvetica', 'bold')
    doc.text('DaqAuto - Export des Commandes', 15, 16)
    
    // Date d'export
    doc.setFontSize(10)
    doc.setFont('helvetica', 'normal')
    doc.text(`Généré le ${new Date().toLocaleDateString('fr-FR')} à ${new Date().toLocaleTimeString('fr-FR')}`, 200, 16)
    
    // Statistiques rapides
    doc.setTextColor(...darkColor)
    doc.setFontSize(12)
    doc.setFont('helvetica', 'bold')
    doc.text(`Total des commandes: ${filteredOrders.value.length}`, 15, 35)
    
    const totalRevenue = filteredOrders.value.reduce((sum, order) => sum + (order.total || 0), 0)
    doc.text(`Chiffre d'affaires total: ${formatCurrency(totalRevenue)}`, 15, 45)
    
    // Tableau des commandes
    const tableData = filteredOrders.value.slice(0, 50).map(order => [
      order.numero_commande,
      formatDate(order.date_commande),
      order.client_nom,
      order.produit_nom.length > 25 ? order.produit_nom.substring(0, 25) + '...' : order.produit_nom,
      order.quantite.toString(),
      formatCurrency(order.total),
      getStatusLabel(order.statut),
      (order.boutique_name || order.boutique_nom).length > 20 ? 
        (order.boutique_name || order.boutique_nom).substring(0, 20) + '...' : 
        (order.boutique_name || order.boutique_nom)
    ])
    
    // En-têtes du tableau
    const headers = [
      'N° Commande', 'Date', 'Client', 'Produit', 'Qté', 'Total', 'Statut', 'Boutique'
    ]
    
    // Configuration du tableau
    let yPosition = 55
    const rowHeight = 8
    const colWidths = [25, 25, 35, 50, 15, 25, 25, 35]
    let xPosition = 15
    
    // Dessiner les en-têtes
    doc.setFillColor(...lightGray)
    doc.rect(xPosition, yPosition, colWidths.reduce((a, b) => a + b, 0), rowHeight, 'F')
    
    doc.setTextColor(...darkColor)
    doc.setFontSize(9)
    doc.setFont('helvetica', 'bold')
    
    headers.forEach((header, index) => {
      doc.text(header, xPosition + 2, yPosition + 5)
      xPosition += colWidths[index]
    })
    
    yPosition += rowHeight
    
    // Dessiner les données
    doc.setFont('helvetica', 'normal')
    doc.setFontSize(8)
    
    tableData.forEach((row, rowIndex) => {
      if (yPosition > 180) { // Nouvelle page si nécessaire
        doc.addPage()
        yPosition = 20
      }
      
      // Alternance de couleurs pour les lignes
      if (rowIndex % 2 === 0) {
        doc.setFillColor(250, 250, 250)
        doc.rect(15, yPosition, colWidths.reduce((a, b) => a + b, 0), rowHeight, 'F')
      }
      
      xPosition = 15
      row.forEach((cell, cellIndex) => {
        doc.text(cell.toString(), xPosition + 2, yPosition + 5)
        xPosition += colWidths[cellIndex]
      })
      
      yPosition += rowHeight
    })
    
    // Note si plus de 50 commandes
    if (filteredOrders.value.length > 50) {
      doc.setTextColor(220, 38, 38)
      doc.setFontSize(10)
      doc.setFont('helvetica', 'italic')
      doc.text(`Note: Seules les 50 premières commandes sont affichées (${filteredOrders.value.length} au total)`, 15, yPosition + 10)
    }
    
    // Pied de page
    doc.setTextColor(...darkColor)
    doc.setFontSize(8)
    doc.setFont('helvetica', 'normal')
    doc.text('DaqAuto - Système de gestion des commandes', 15, 200)
    doc.text(`Page 1 - Généré le ${new Date().toLocaleDateString('fr-FR')}`, 200, 200)
    
    // Sauvegarder le PDF
    doc.save(`Commandes_DaqAuto_${new Date().toISOString().split('T')[0]}.pdf`)
    
    showNotificationMessage('success', 'Export PDF réussi', `PDF généré avec ${Math.min(filteredOrders.value.length, 50)} commandes`)
    
  } catch (error) {
    console.error('Erreur lors de l\'export PDF:', error)
    showNotificationMessage('error', 'Erreur Export', 'Erreur lors de l\'export PDF')
  }
}

const showNotificationMessage = (type, title, message) => {
  notificationType.value = type
  notificationTitle.value = title
  notificationMessage.value = message
  showNotification.value = true

  setTimeout(() => {
    closeNotification()
  }, 5000)
}

const closeNotification = () => {
  showNotification.value = false
}

// Utility Methods
const formatCurrency = (amount) => {
  return new Intl.NumberFormat('fr-FR', {
    style: 'currency',
    currency: 'USD',
    minimumFractionDigits: 0
  }).format(amount) 
}

const formatDate = (dateString) => {
  return new Date(dateString).toLocaleDateString('fr-FR', {
    day: '2-digit',
    month: '2-digit',
    year: 'numeric'
  })
}

const formatTime = (dateString) => {
  return new Date(dateString).toLocaleTimeString('fr-FR', {
    hour: '2-digit',
    minute: '2-digit'
  })
}

const getStatusLabel = (status) => {
  const labels = {
    'en_attente': 'En attente',
    'confirmee': 'Confirmée',
    'en_livraison': 'Expédiée',
    'livree': 'Livrée',
    'annulee': 'Annulée'
  }
  return labels[status] || status
}
const getStatusIcon = (status) => {
    const icons = {
      'en_attente': Clock,
      'confirmee': CheckCircle,
      'en_livraison': Truck,
      'livree': PackageIcon,
      'annulee': XIcon
    }
    return icons[status] 
  }

const getStatusClass = (status) => {
  const classes = {
    'en_attente': 'bg-yellow-100 text-yellow-800',
    'confirmee': 'bg-green-100 success-color',
    'en_livraison': 'bg-orange-100 blue-color',
    'livree': 'bg-orange-100 primary-color',
    'annulee': 'bg-red-100 error-color'
  }
  return classes[status] || 'bg-gray-100 text-gray-800'
}

const getDeliveryTypeLabel = (type) => {
  const labels = {
    'abidjan': 'Livraison Abidjan',
    'interieur': 'Livraison Intérieur',
    'retrait': 'Retrait en boutique'
  }
  return labels[type] || type
}

const getNotificationIcon = (type) => {
  const icons = {
  }
}

const getBoutiqueStatusLabel = (status) => {
  const labels = {
    'active': 'Active',
    'inactive': 'Inactive',
    'suspended': 'Suspendue',
    'pending': 'En attente'
  }
  return labels[status] || status
}

const getBoutiqueStatusClass = (status) => {
  const classes = {
    'active': 'bg-green-100 text-green-800',
    'inactive': 'bg-gray-100 text-gray-800',
    'suspended': 'bg-red-100 text-red-800',
    'pending': 'bg-yellow-100 text-yellow-800'
  }
  return classes[status] || 'bg-gray-100 text-gray-800'
}

// Actions de contact boutique
const callBoutique = (phone) => {
  if (phone) {
    window.open(`tel:${phone}`, '_self')
    showNotificationMessage('info', 'Appel en cours', `Appel vers ${phone}`)
  }
}

const emailBoutique = (email) => {
  if (email) {
    const subject = `Commande ${selectedOrder.value?.numero_commande} - Contact`
    const body = `Bonjour,\n\nJe vous contacte concernant la commande ${selectedOrder.value?.numero_commande}.\n\nCordialement`
    window.open(`mailto:${email}?subject=${encodeURIComponent(subject)}&body=${encodeURIComponent(body)}`, '_self')
    showNotificationMessage('info', 'Email ouvert', `Email vers ${email}`)
  }
}

const whatsappBoutique = (phone) => {
  if (phone) {
    const message = `Bonjour, je vous contacte concernant la commande ${selectedOrder.value?.numero_commande}.`
    const cleanPhone = phone.replace(/[^\d]/g, '')
    window.open(`https://wa.me/${cleanPhone}?text=${encodeURIComponent(message)}`, '_blank')
    showNotificationMessage('info', 'WhatsApp ouvert', `Message vers ${phone}`)
  }
}

// Notifier la boutique d'une nouvelle commande via WhatsApp
const notifyBoutiqueNewOrder = (order) => {
  const phone = order.boutique_whatsapp || order.boutique_telephone
  if (phone) {
    const message = `🛍️ *NOUVELLE COMMANDE - ${order.numero_commande}*

📋 *Détails de la commande :*
• Produit : ${order.produit_nom}
• Quantité : ${order.quantite}
• Prix unitaire : ${formatCurrency(order.produit_prix)}
• Total : ${formatCurrency(order.total)}

👤 *Client :*
• Nom : ${order.client_nom}
• Téléphone : ${order.client_telephone}

🚚 *Livraison :*
• Type : ${getDeliveryTypeLabel(order.type_livraison)}
• Adresse : ${order.adresse_complete}
${order.commune ? `• Commune : ${order.commune}` : ''}
${order.ville ? `• Ville : ${order.ville}` : ''}

💰 *Récapitulatif :*
• Sous-total : ${formatCurrency(order.sous_total)}
• Frais livraison : ${formatCurrency(order.frais_livraison)}
• *TOTAL : ${formatCurrency(order.total)}*

📅 Date : ${formatDate(order.date_commande)} à ${formatTime(order.date_commande)}

⚠️ Merci de confirmer la disponibilité du produit et de préparer la commande.

---
*Message automatique du système de gestion des commandes*`

    const cleanPhone = phone.replace(/[^\d]/g, '')
    window.open(`https://wa.me/${cleanPhone}?text=${encodeURIComponent(message)}`, '_blank')
    showNotificationMessage('success', 'Notification envoyée', `Notification WhatsApp envoyée à la boutique ${order.boutique_name || order.boutique_nom}`)
  } else {
    showNotificationMessage('error', 'Erreur', 'Aucun numéro WhatsApp ou téléphone trouvé pour cette boutique')
  }
}

// Notifier le client de l'état de sa commande via WhatsApp
const notifyClientOrderStatus = (order) => {
  const phone = order.client_telephone
  if (phone) {
    let message = ''
    const statusMessages = {
      'en_attente': {
        title: '⏳ COMMANDE EN ATTENTE',
        content: `Bonjour ${order.client_nom},

Votre commande ${order.numero_commande} a été reçue et est en cours de traitement.

📋 *Récapitulatif de votre commande :*
• Produit : ${order.produit_nom}
• Quantité : ${order.quantite}
• Prix : ${formatCurrency(order.produit_prix)}
• Total : ${formatCurrency(order.total)}

🏪 *Boutique :* ${order.boutique_name || order.boutique_nom}
${order.boutique_telephone ? `📞 Contact boutique : ${order.boutique_telephone}` : ''}

🚚 *Livraison :*
• Type : ${getDeliveryTypeLabel(order.type_livraison)}
• Adresse : ${order.adresse_complete}
• Frais : ${formatCurrency(order.frais_livraison)}

⏰ Nous vous tiendrons informé de l'évolution de votre commande.

Merci pour votre confiance ! 🙏`
      },
      'confirmee': {
        title: '✅ COMMANDE CONFIRMÉE',
        content: `Excellente nouvelle ${order.client_nom} !

Votre commande ${order.numero_commande} a été confirmée par la boutique.

📦 *Votre commande :*
• ${order.produit_nom}
• Quantité : ${order.quantite}
• Total : ${formatCurrency(order.total)}

🏪 *Boutique :* ${order.boutique_name || order.boutique_nom}
${order.boutique_telephone ? `📞 ${order.boutique_telephone}` : ''}

🚚 *Livraison prévue :*
• ${getDeliveryTypeLabel(order.type_livraison)}
• ${order.adresse_complete}

📱 Vous recevrez une notification dès l'expédition de votre commande.

Merci pour votre patience ! ✨`
      },
      'en_livraison': {
        title: '🚚 COMMANDE EN LIVRAISON',
        content: `Bonne nouvelle ${order.client_nom} !

Votre commande ${order.numero_commande} est maintenant en cours de livraison ! 🎉

📦 *Détails :*
• ${order.produit_nom}
• Destination : ${order.adresse_complete}
• Total : ${formatCurrency(order.total)}

🏪 *Boutique :* ${order.boutique_name || order.boutique_nom}

📍 *Livraison :*
• Type : ${getDeliveryTypeLabel(order.type_livraison)}
• Adresse : ${order.adresse_complete}

⏰ Votre commande devrait arriver bientôt !

Préparez-vous à la réceptionner. Merci ! 🙏`
      },
      'livree': {
        title: '🎉 COMMANDE LIVRÉE',
        content: `Félicitations ${order.client_nom} !

Votre commande ${order.numero_commande} a été livrée avec succès ! ✅

📦 *Commande livrée :*
• ${order.produit_nom}
• Quantité : ${order.quantite}
• Total : ${formatCurrency(order.total)}

🏪 *Boutique :* ${order.boutique_name || order.boutique_nom}

😊 Nous espérons que vous êtes satisfait(e) de votre achat !

⭐ N'hésitez pas à nous faire part de votre avis.

Merci de nous avoir fait confiance ! 🙏✨`
      },
      'annulee': {
        title: '❌ COMMANDE ANNULÉE',
        content: `Bonjour ${order.client_nom},

Nous sommes désolés de vous informer que votre commande ${order.numero_commande} a été annulée.

📋 *Commande annulée :*
• ${order.produit_nom}
• Quantité : ${order.quantite}
• Montant : ${formatCurrency(order.total)}

🏪 *Boutique :* ${order.boutique_name || order.boutique_nom}
${order.boutique_telephone ? `📞 Contact : ${order.boutique_telephone}` : ''}

💰 Si un paiement a été effectué, le remboursement sera traité dans les plus brefs délais.

📞 Pour plus d'informations, contactez-nous ou la boutique directement.

Nous nous excusons pour ce désagrément. 🙏`
      }
    }

    const statusInfo = statusMessages[order.statut] || statusMessages['en_attente']
    
    message = `${statusInfo.title}

${statusInfo.content}

📅 ${formatDate(order.date_commande)} à ${formatTime(order.date_commande)}

---
*Message automatique du système de commandes*`

    const cleanPhone = phone.replace(/[^\d]/g, '')
    window.open(`https://wa.me/${cleanPhone}?text=${encodeURIComponent(message)}`, '_blank')
    showNotificationMessage('success', 'Notification client envoyée', `Notification WhatsApp envoyée à ${order.client_nom}`)
  } else {
    showNotificationMessage('error', 'Erreur', 'Aucun numéro de téléphone trouvé pour ce client')
  }
}

// Générer un reçu PDF pour la commande
const generateReceiptPDF = (order) => {
  try {
    // Créer un PDF en format compact (demi-page)
    const doc = new jsPDF({
      orientation: 'portrait',
      unit: 'mm',
      format: [105, 148] // Format A6 (moitié d'A4)
    })
    
    // Configuration des couleurs avec #F65A11 comme base
    const primaryColor = [246, 90, 17] // #F65A11
    const secondaryColor = [255, 102, 102] // Version plus claire
    const accentColor = [230, 74, 10] // Version plus foncée
    const darkColor = [51, 51, 51] // #333333
    const lightGray = [248, 249, 250] // #f8f9fa
    
    // Fond dégradé moderne
    doc.setFillColor(...lightGray)
    doc.rect(0, 0, 105, 148, 'F')
    
    // En-tête avec dégradé orange moderne
    doc.setFillColor(...primaryColor)
    doc.rect(0, 0, 105, 35, 'F')
    
    // Effet dégradé simulé avec des rectangles transparents
    doc.setFillColor(255, 255, 255)
    doc.setGState(new doc.GState({opacity: 0.1}))
    doc.rect(0, 0, 105, 15, 'F')
    doc.setGState(new doc.GState({opacity: 1}))
    
    // Logo AliAdjamé (cercle moderne)
    doc.setFillColor(255, 255, 255)
    doc.circle(15, 17, 8, 'F')
    
    // Simuler le logo avec du texte stylé en attendant l'image
    doc.setTextColor(...primaryColor)
    doc.setFontSize(10)
    doc.setFont('helvetica', 'bold')
    doc.text('ALI', 11, 19)
    
    // Titre principal moderne
    doc.setTextColor(255, 255, 255)
    doc.setFontSize(16)
    doc.setFont('helvetica', 'bold')
    doc.text('REÇU DE LIVRAISON', 28, 12)
    
    // Sous-titre élégant
    doc.setFontSize(8)
    doc.setFont('helvetica', 'normal')
    doc.text('Daq Auto - Marketplace Premium', 28, 18)
    doc.text('📞 +225 01 53 67 60 62', 28, 23)
    doc.text('✉️ commandes@daqauto.com', 28, 28)
    
    // Ligne décorative
    doc.setDrawColor(...accentColor)
    doc.setLineWidth(0.5)
    doc.line(5, 32, 100, 32)
    
    // Section commande avec style moderne
    doc.setFillColor(255, 255, 255)
    doc.roundedRect(5, 38, 95, 18, 3, 3, 'F')
    
    // Ombre simulée
    doc.setFillColor(0, 0, 0)
    doc.setGState(new doc.GState({opacity: 0.1}))
    doc.roundedRect(6, 39, 95, 18, 3, 3, 'F')
    doc.setGState(new doc.GState({opacity: 1}))
    
    // Numéro de commande stylé
    doc.setTextColor(...darkColor)
    doc.setFontSize(12)
    doc.setFont('helvetica', 'bold')
    doc.text(`#${order.numero_commande}`, 8, 45)
    
    // Badge statut moderne
    const statusColors = {
      'en_attente': [255, 193, 7],
      'confirmee': [40, 167, 69],
      'en_livraison': [0, 123, 255],
      'livree': [40, 167, 69],
      'annulee': [220, 53, 69]
    }
    
    const statusColor = statusColors[order.statut] || [108, 117, 125]
    doc.setFillColor(...statusColor)
    doc.roundedRect(65, 40, 32, 8, 2, 2, 'F')
    doc.setTextColor(255, 255, 255)
    doc.setFontSize(7)
    doc.setFont('helvetica', 'bold')
    doc.text(getStatusLabel(order.statut).toUpperCase(), 67, 45)
    
    // Date avec icône
    doc.setTextColor(...darkColor)
    doc.setFontSize(8)
    doc.setFont('helvetica', 'normal')
    doc.text(`📅 ${formatDate(order.date_commande)} ⏰ ${formatTime(order.date_commande)}`, 8, 52)
    
    // Section Client avec style moderne
    doc.setFillColor(...primaryColor)
    doc.setGState(new doc.GState({opacity: 0.1}))
    doc.roundedRect(5, 60, 45, 25, 3, 3, 'F')
    doc.setGState(new doc.GState({opacity: 1}))
    
    doc.setTextColor(...primaryColor)
    doc.setFontSize(9)
    doc.setFont('helvetica', 'bold')
    doc.text('👤 CLIENT', 8, 67)
    
    doc.setTextColor(...darkColor)
    doc.setFontSize(8)
    doc.setFont('helvetica', 'bold')
    doc.text(order.client_nom, 8, 73)
    doc.setFont('helvetica', 'normal')
    doc.text(`📱 ${order.client_telephone}`, 8, 78)
    
    // Section Boutique avec design moderne
    doc.setFillColor(...accentColor)
    doc.setGState(new doc.GState({opacity: 0.1}))
    doc.roundedRect(55, 60, 45, 25, 3, 3, 'F')
    doc.setGState(new doc.GState({opacity: 1}))
    
    doc.setTextColor(...accentColor)
    doc.setFontSize(9)
    doc.setFont('helvetica', 'bold')
    doc.text('🏪 BOUTIQUE', 58, 67)
    
    doc.setTextColor(...darkColor)
    doc.setFontSize(8)
    doc.setFont('helvetica', 'bold')
    const boutiqueName = (order.boutique_name || order.boutique_nom)
    const boutiqueShort = boutiqueName.length > 18 ? boutiqueName.substring(0, 18) + '...' : boutiqueName
    doc.text(boutiqueShort, 58, 73)
    
    if (order.boutique_telephone) {
      doc.setFont('helvetica', 'normal')
      doc.text(`📞 ${order.boutique_telephone}`, 58, 78)
    }
    
    // Section Livraison moderne
    doc.setFillColor(255, 255, 255)
    doc.roundedRect(5, 90, 95, 22, 3, 3, 'F')
    
    // Bordure colorée
    doc.setDrawColor(...primaryColor)
    doc.setLineWidth(1)
    doc.roundedRect(5, 90, 95, 22, 3, 3, 'S')
    
    doc.setTextColor(...primaryColor)
    doc.setFontSize(9)
    doc.setFont('helvetica', 'bold')
    doc.text('🚚 LIVRAISON', 8, 97)
    
    doc.setTextColor(...darkColor)
    doc.setFontSize(8)
    doc.setFont('helvetica', 'normal')
    doc.text(getDeliveryTypeLabel(order.type_livraison), 8, 102)
    
    // Adresse sur plusieurs lignes avec style
    const adresse = order.adresse_complete
    if (adresse.length > 35) {
      const words = adresse.split(' ')
      let line1 = '', line2 = ''
      let currentLine = 1
      
      words.forEach(word => {
        if (currentLine === 1 && (line1 + word).length < 35) {
          line1 += (line1 ? ' ' : '') + word
        } else {
          currentLine = 2
          line2 += (line2 ? ' ' : '') + word
        }
      })
      
      doc.text(`📍 ${line1}`, 8, 106)
      if (line2) doc.text(`   ${line2}`, 8, 110)
    } else {
      doc.text(`📍 ${adresse}`, 8, 106)
    }
    
    // Section Produit moderne
    doc.setFillColor(...secondaryColor)
    doc.setGState(new doc.GState({opacity: 0.1}))
    doc.roundedRect(5, 117, 65, 18, 3, 3, 'F')
    doc.setGState(new doc.GState({opacity: 1}))
    
    doc.setTextColor(...primaryColor)
    doc.setFontSize(9)
    doc.setFont('helvetica', 'bold')
    doc.text('📦 PRODUIT', 8, 124)
    
    doc.setTextColor(...darkColor)
    doc.setFontSize(8)
    doc.setFont('helvetica', 'bold')
    const produitNom = order.produit_nom.length > 25 ? 
      order.produit_nom.substring(0, 25) + '...' : order.produit_nom
    doc.text(produitNom, 8, 129)
    
    doc.setFont('helvetica', 'normal')
    doc.text(`Qté: ${order.quantite} × ${formatCurrency(order.produit_prix)}`, 8, 133)
    
    // Section Total avec design premium
    doc.setFillColor(...primaryColor)
    doc.roundedRect(75, 117, 25, 18, 3, 3, 'F')
    
    doc.setTextColor(255, 255, 255)
    doc.setFontSize(8)
    doc.setFont('helvetica', 'bold')
    doc.text('TOTAL', 78, 123)
    
    doc.setFontSize(10)
    doc.setFont('helvetica', 'bold')
    const totalText = formatCurrency(order.total)
    const totalShort = totalText.length > 12 ? totalText.replace(' FCFA', '') : totalText
    doc.text(totalShort, 77, 130)
    
    // QR Code moderne avec bordure
    doc.setFillColor(255, 255, 255)
    doc.roundedRect(75, 138, 22, 8, 2, 2, 'F')
    doc.setDrawColor(...primaryColor)
    doc.setLineWidth(0.5)
    doc.roundedRect(75, 138, 22, 8, 2, 2, 'S')
    
    doc.setTextColor(...primaryColor)
    doc.setFontSize(6)
    doc.setFont('helvetica', 'bold')
    doc.text('🔗 SUIVI COMMANDE', 77, 143)
    
    // Pied de page élégant
    doc.setTextColor(...darkColor)
    doc.setFontSize(6)
    doc.setFont('helvetica', 'normal')
    doc.text('Merci pour votre confiance ! 💝', 8, 143)
    
    // Ligne décorative finale
    doc.setDrawColor(...primaryColor)
    doc.setLineWidth(0.3)
    doc.line(5, 145, 100, 145)
    
    // Sauvegarder le PDF
    doc.save(`Recu_DaqAuto_${order.numero_commande}.pdf`)
    
    showNotificationMessage('success', 'Reçu généré', `Le reçu stylé pour la commande ${order.numero_commande} a été téléchargé`)
    
  } catch (error) {
    console.error('Erreur lors de la génération du PDF:', error)
    showNotificationMessage('error', 'Erreur PDF', 'Erreur lors de la génération du reçu PDF')
  }
}

// Watchers
watch([statusFilter, dateFilter, sortBy], () => {
  currentPage.value = 1
})

// Lifecycle
onMounted(() => {
  loadAllData()
})
</script>

<style scoped>

/* Transitions fluides */
.transition-all {
  transition-property: all;
  transition-timing-function: cubic-bezier(0.4, 0, 0.2, 1);
  transition-duration: 300ms;
}

.transition-colors {
  transition-property: color, background-color, border-color, text-decoration-color, fill, stroke;
  transition-timing-function: cubic-bezier(0.4, 0, 0.2, 1);
  transition-duration: 150ms;
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
