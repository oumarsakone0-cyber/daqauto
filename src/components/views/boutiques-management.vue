<template>
    <div class="min-h-screen bg-gray-50 relative overflow-hidden">
      <!-- Animation de fond luxueuse -->
      <div class="absolute inset-0 overflow-hidden pointer-events-none z-0">
        <div class="absolute inset-0 bg-gradient-to-br from-gray-50 via-white to-gray-100"></div>
        <div class="absolute -top-20 -left-20 w-96 h-96 bg-gradient-to-br from-blue-200/60 to-blue-300/40 rounded-full blur-3xl animate-float-slow"></div>
        <div class="absolute top-1/4 -right-20 w-80 h-80 bg-gradient-to-br from-green-200/50 to-emerald-200/35 rounded-full blur-3xl animate-float-reverse"></div>
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
          <router-link to="/admin/produits" class="hover:text-gray-700">Produits</router-link>
          <span class="mx-2">/</span>
          <span class="font-medium text-gray-700 truncate">Gestion des Boutiques</span>
        </div>
  
        <!-- Header -->
        <div class="flex flex-col space-y-4 lg:flex-row lg:items-center lg:justify-between lg:space-y-0 mb-6 sm:mb-8">
          <div>
            <h1 class="text-2xl sm:text-3xl font-bold text-gray-900 mb-2">Gestion des Boutiques</h1>
            <p class="text-sm sm:text-base text-gray-600">Validation et modération des boutiques et leurs boosts</p>
          </div>
          
          <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-2 sm:gap-3">
            <button 
              @click="loadAllData"
              :disabled="dataLoading"
              class="submit-btn"
              >
              <RefreshCcw class="w-4 h-4 mr-2" />
              {{ dataLoading ? 'Chargement...' : 'Actualiser' }}
            </button>
          </div>
        </div>
  
        <!-- Loading State -->
        <div v-if="dataLoading" class="mb-6 sm:mb-8">
          <div class="bg-white rounded-lg p-6 sm:p-8 text-center shadow-lg">
            <div class="inline-block animate-spin rounded-full h-10 w-10 sm:h-12 sm:w-12 border-4 border-orange-500 border-t-transparent"></div>
            <p class="mt-4 text-gray-600 font-medium text-sm sm:text-base">Chargement des boutiques...</p>
          </div>
        </div>
  
        <!-- Stats Cards -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-6 mb-6 sm:mb-8" v-if="!dataLoading">
          <div class="bg-white overflow-hidden shadow-lg rounded-lg border border-gray-100 hover:shadow-xl transition-all duration-300">
            <div class="px-4 sm:px-6 py-4 sm:py-6">
              <div class="flex items-center mb-3 sm:mb-4">
                <div class="w-10 h-10 sm:w-12 sm:h-12 bg-gradient-to-br from-blue-200 to-blue-300 rounded-lg flex items-center justify-center mr-3 sm:mr-4 flex-shrink-0">
                 <Building class="w-5 h-5 sm:w-6 sm:h-6 text-black" />
                </div>
                <div class="min-w-0">
                  <p class="text-xs sm:text-sm text-gray-600">Total Boutiques</p>
                  <p class="text-xl sm:text-2xl font-bold text-gray-900">{{ stats.total_boutiques || 0 }}</p>
                </div>
              </div>
            </div>
          </div>
  
          <div class="bg-white overflow-hidden shadow-lg rounded-lg border border-gray-100 hover:shadow-xl transition-all duration-300">
            <div class="px-4 sm:px-6 py-4 sm:py-6">
              <div class="flex items-center mb-3 sm:mb-4">
                <div class="w-10 h-10 sm:w-12 sm:h-12 bg-gradient-to-br from-green-200 to-green-300 rounded-lg flex items-center justify-center mr-3 sm:mr-4 flex-shrink-0">
                  <CheckCircle2 class="w-5 h-5 sm:w-6 sm:h-6 text-black" />
                </div>
                <div class="min-w-0">
                  <p class="text-xs sm:text-sm text-gray-600">Boutiques Vérifiées</p>
                  <p class="text-xl sm:text-2xl font-bold text-gray-900">{{ stats.verified_boutiques || 0 }}</p>
                </div>
              </div>
            </div>
          </div>
  
          <div class="bg-white overflow-hidden shadow-lg rounded-lg border border-gray-100 hover:shadow-xl transition-all duration-300">
            <div class="px-4 sm:px-6 py-4 sm:py-6">
              <div class="flex items-center mb-3 sm:mb-4">
                <div class="w-10 h-10 sm:w-12 sm:h-12 bg-gradient-to-br from-purple-200 to-purple-300 rounded-lg flex items-center justify-center mr-3 sm:mr-4 flex-shrink-0">
                  <ZapIcon class="w-5 h-5 sm:w-6 sm:h-6 text-black" />
                </div>
                <div class="min-w-0">
                  <p class="text-xs sm:text-sm text-gray-600">Boutiques Boostées</p>
                  <p class="text-xl sm:text-2xl font-bold text-gray-900">{{ stats.boosted_boutiques || 0 }}</p>
                </div>
              </div>
            </div>
          </div>
  
          <div class="bg-white overflow-hidden shadow-lg rounded-lg border border-gray-100 hover:shadow-xl transition-all duration-300">
            <div class="px-4 sm:px-6 py-4 sm:py-6">
              <div class="flex items-center mb-3 sm:mb-4">
                <div class="w-10 h-10 sm:w-12 sm:h-12 bg-gradient-to-br from-yellow-200 to-yellow-300 rounded-lg flex items-center justify-center mr-3 sm:mr-4 flex-shrink-0">
                  <Clock class="w-5 h-5 sm:w-6 sm:h-6 text-black" />
                </div>
                <div class="min-w-0">
                  <p class="text-xs sm:text-sm text-gray-600">Boosts en Attente</p>
                  <p class="text-xl sm:text-2xl font-bold text-gray-900">{{ stats.pending_boosts || 0 }}</p>
                </div>
              </div>
            </div>
          </div>
        </div>
  
        <!-- Main Content Card -->
        <div class="bg-white shadow-lg rounded-lg overflow-hidden border border-gray-100" v-if="!dataLoading">
          <!-- Search and Filters -->
          <div class="px-4 sm:px-6 py-3 sm:py-4 border-b border-gray-200 bg-white">
            <div class="flex flex-col space-y-3 sm:space-y-0 sm:flex-row sm:items-center sm:justify-between sm:gap-4">
              <div class="relative flex-1 max-w-full sm:max-w-md">
                <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                  <SearchIcon class="ww-4 h-4 sm:w-5 sm:h-5 text-gray-400" />
                </div>
                <input 
                  v-model="searchQuery"
                  type="text" 
                  class="input-style" 
                  placeholder="Rechercher par nom, marché, contact..."
                  @input="handleSearch"
                >
              </div>
              <div class="flex items-center gap-2 sm:gap-3">
                <select v-model="statusFilter" @change="loadBoutiques" class=" input-style" style="padding-left: 15px; padding-right: 30px;">
                  <option value="">Tous les statuts</option>
                  <option value="verified">Vérifiées</option>
                  <option value="unverified">Non vérifiées</option>
                  <option value="boosted">Boostées</option>
                </select>
              </div>
            </div>
          </div>
  
          <!-- Boutiques Table -->
          <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
              <thead class="bg-gray-50">
                <tr>
                  <th scope="col" class="px-3 sm:px-4 py-2 sm:py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Boutique</th>
                  <th scope="col" class="px-3 sm:px-4 py-2 sm:py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Contact</th>
                  <th scope="col" class="hidden md:table-cell px-3 sm:px-4 py-2 sm:py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Marché</th>
                  <th scope="col" class="px-3 sm:px-4 py-2 sm:py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Produits</th>
                  <th scope="col" class="px-3 sm:px-4 py-2 sm:py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Statut Boost</th>
                  <th scope="col" class="hidden lg:table-cell px-3 sm:px-4 py-2 sm:py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                  <th scope="col" class="px-3 sm:px-4 py-2 sm:py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Statut</th>
                  <th scope="col" class="px-3 sm:px-4 py-2 sm:py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                </tr>
              </thead>
              <tbody class="bg-white divide-y divide-gray-200">
                <tr v-for="boutique in paginatedBoutiques" :key="boutique.id" class="hover:bg-gray-50 transition-colors">
                  <!-- Boutique Info -->
                  <td class="px-3 sm:px-4 py-3 sm:py-4 whitespace-nowrap">
                    <div class="flex items-center space-x-3">
                      <div class="w-10 h-10 sm:w-12 sm:h-12 bg-gradient-to-br from-blue-100 to-blue-200 rounded-lg flex items-center justify-center flex-shrink-0">
                        <svg class="w-5 h-5 sm:w-6 sm:h-6 text-black" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                        </svg>  
                      </div>
                      <div class="min-w-0 flex-1">
                        <div class="text-xs sm:text-sm font-medium text-gray-900 truncate">{{ boutique.name }}</div>
                        <div class="text-xs text-gray-500">#{{ boutique.id }}</div>
                      </div>
                    </div>
                  </td>
                  
                  <!-- Contact -->
                  <td class="px-3 sm:px-4 py-3 sm:py-4 whitespace-nowrap">
                    <div class="space-y-1">
                      <div class="text-xs sm:text-sm font-medium text-gray-900">{{ boutique.owner_name }}</div>
                      <div class="text-xs text-gray-500">{{ boutique.owner_email }}</div>
                      <div class="text-xs text-gray-500">{{ boutique.owner_phone || 'N/A' }}</div>
                    </div>
                  </td>
                  
                  <!-- Marché -->
                  <td class="hidden md:table-cell px-3 sm:px-4 py-3 sm:py-4 whitespace-nowrap">
                    <div class="text-xs sm:text-sm font-medium text-gray-900">{{ boutique.market_name }}</div>
                    <div class="text-xs text-gray-500">{{ boutique.market_location }}</div>
                  </td>
                  
                  <!-- Produits -->
                  <td class="px-3 sm:px-4 py-3 sm:py-4 whitespace-nowrap text-center">
                    <div class="space-y-1">
                      <div class="text-sm sm:text-base font-bold text-gray-900">{{ boutique.products_count || 0 }}</div>
                      <div class="text-xs text-gray-500">produits</div>
                    </div>
                  </td>
                  
                  <!-- Statut Boost -->
                  <td class="px-3 sm:px-4 py-3 sm:py-4 whitespace-nowrap">
                    <div class="space-y-2">
                      <!-- Boutique boostée -->
                      <div v-if="boutique.is_boosted && boutique.boost_status === 'active'" class="space-y-1 items-center justify-center">
                        <span class="inline-flex items-center gap-1 px-2 py-1 rounded-full text-xs font-medium bg-purple-100 primary-color">
                          <svg class="h-3 w-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                          </svg>
                          Boutique Boostée
                        </span>
                        <div class="text-xs text-gray-500">
                          Expire: {{ formatDate(boutique.boost_expires_at) }}
                        </div>
                        <button 
                          @click="viewBoutiqueBoostDetails(boutique)"
                          style="border-radius: 8px; margin-top:10px"
                          class="btn-outline h-10 mt-1  items-center justify-center"
                        >
                          Voir détails
                        </button>
                      </div>
                      
                      <!-- Boost en attente -->
                      <div v-else-if="boutique.boost_status === 'pending'" class="space-y-1">
                        <span class="inline-flex items-center gap-1 px-2 py-1 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                          <svg class="h-3 w-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                          </svg>
                          Boost en attente
                        </span>
                        <div class="text-xs text-gray-500">
                          Type: {{ getBoostTypeLabel(boutique.boost_type) }}
                        </div>
                        <div class="flex gap-1 mt-2">
                          <button 
                            @click="openBoutiqueBoostValidation(boutique)"
                            style="font-size: 12px;"
                            class="submit-btn h-10 flex-1"
                          >
                            Valider
                          </button>
                          <button 
                            @click="viewBoutiqueBoostDetails(boutique)"
                            style="border-radius: 8px"
                            class="btn-outline h-10 flex-1 "
                          >
                            Détails
                          </button>
                        </div>
                      </div>
                      
                      <!-- Pas de boost -->
                      <div v-else class="text-xs text-gray-400">
                       <span class="inline-flex items-center gap-1 px-2 py-1 rounded-full text-xs font-medium bg-purple-100 error-color">
                          <svg class="h-3 w-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                          </svg>
                        Aucun boost
                        </span>
                      </div>
                    </div>
                  </td>
                  
                  <!-- Date -->
                  <td class="hidden lg:table-cell px-3 sm:px-4 py-3 sm:py-4 whitespace-nowrap">
                    <div class="space-y-1">
                      <div class="text-xs sm:text-sm text-gray-900">{{ formatDate(boutique.created_at) }}</div>
                      <div class="text-xs text-gray-500">{{ formatTime(boutique.created_at) }}</div>
                    </div>
                  </td>
                  
                  <!-- Statut -->
                  <td class="px-3 sm:px-4 py-3 sm:py-4 whitespace-nowrap">
                    <span :class="['inline-flex items-center gap-1 px-2 py-1 rounded-full text-xs font-medium', boutique.is_verified ? 'bg-green-100 success-color' : 'bg-red-100 error-color']">
                      <svg v-if="boutique.is_verified" class="h-3 w-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                      </svg>
                      <svg v-else class="h-3 w-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"/>
                      </svg>
                      {{ boutique.is_verified ? 'Vérifiée' : 'Non vérifiée' }}
                    </span>
                  </td>
                  
                  <!-- Actions -->
                  <td class="px-3 sm:px-4 py-3 sm:py-4 whitespace-nowrap text-xs sm:text-sm font-medium">
                    <div class="flex items-center space-x-1 sm:space-x-2">
                      <button 
                        @click="openBoutiqueDetails(boutique)" 
                        class="p-1.5 sm:p-2 rounded-lg transition-colors text-xs"
                        title="Voir détails"
                        style="background-color: rgba(59, 130, 246, 0.1); color: black;"
                      >
                        <svg class="h-3 w-3 sm:h-4 sm:w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                        </svg>
                      </button>
                      <button 
                        @click="toggleBoutiqueVerification(boutique)" 
                        class="p-1.5 sm:p-2 rounded-lg transition-colors text-xs"
                        :title="boutique.is_verified ? 'Retirer vérification' : 'Vérifier boutique'"
                        :style="boutique.is_verified ? 'background-color: rgba(248, 113, 113, 0.1); color: red;' : 'background-color: rgba(74, 222, 128, 0.1); color: rgb(22, 163, 74);'"
                      >
                        <svg v-if="boutique.is_verified" class="h-3 w-3 sm:h-4 sm:w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                        <svg v-else class="h-3 w-3 sm:h-4 sm:w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
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
              <span class="text-gray-700 hidden sm:inline">Éléments par page</span>
              <span class="text-gray-700 sm:hidden">Par page</span>
              <select 
                v-model="itemsPerPage"
                @change="handleItemsPerPageChange"
                class="input-style"
                style="width: 60px; height: 45px; padding-left: 10px;"
              >
                <option value="10">10</option>
                <option value="25">25</option>
                <option value="50">50</option>
              </select>
              <span class="text-gray-700 text-xs sm:text-sm">
                <span class="hidden sm:inline">
                  Affichage {{ ((currentPage - 1) * itemsPerPage) + 1 }} à 
                  {{ Math.min(currentPage * itemsPerPage, filteredBoutiques.length) }} 
                  sur {{ filteredBoutiques.length }} résultats
                </span>
                <span class="sm:hidden">
                  {{ ((currentPage - 1) * itemsPerPage) + 1 }}-{{ Math.min(currentPage * itemsPerPage, filteredBoutiques.length) }} 
                  / {{ filteredBoutiques.length }}
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
                      : 'text-gray-500 border-gray-300 bg-white hover:bg-gray-50'
                  ]"
                  :style="page === currentPage ? 'bg-orange' : ''"
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
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                </svg>
              </button>
            </div>
          </div>
        </div>
  
        <!-- Empty State -->
        <div v-if="!dataLoading && filteredBoutiques.length === 0" class="bg-white shadow-lg rounded-lg p-8 sm:p-12 text-center">
          <div class="w-16 h-16 sm:w-20 sm:h-20 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4 sm:mb-6">
            <svg class="w-8 h-8 sm:w-10 sm:h-10 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
            </svg>
          </div>
          <h3 class="text-lg sm:text-xl font-semibold text-gray-900 mb-2">Aucune boutique trouvée</h3>
          <p class="text-sm sm:text-base text-gray-500 mb-4 sm:mb-6">
            Aucune boutique ne correspond à vos critères de recherche.
          </p>
        </div>
      </div>
  
      <!-- Boutique Details Modal -->
      <div v-if="showBoutiqueModal" class="fixed inset-0 bg-black/60  backdrop-blur-sm bg-opacity-50 flex items-center justify-center p-4 z-50">
        <div class="bg-white rounded-xl shadow-2xl max-w-4xl w-full max-h-[90vh] overflow-y-auto">
          <div class="relative">
            <!-- Header avec gradient -->
            <div class="bg-white px-8 py-6 rounded-t-xl border-b border-gray-100">
              <div class="flex justify-between items-center">
                <h3 class="text-xl font-bold text-black flex items-center">
                  <div class="w-10 h-10 bg-orange rounded-lg flex items-center justify-center mr-3">
                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                    </svg>
                  </div>
                  Détails de la boutique
                </h3>
                <XIcon @click="closeBoutiqueModal" class="h-7 w-7 cursor-pointer text-gray-700" />
              </div>
            </div>
  
            <div class="p-6">
              <div v-if="selectedBoutique" class="space-y-6">
                <!-- Boutique Header -->
                <div class="bg-gradient-to-r from-orange-50 to-orange-100 rounded-2xl p-6 border border-orange-200">
                  <div class="flex items-center space-x-6">
                    <div class="w-20 h-20 bg-orange rounded-xl flex items-center justify-center flex-shrink-0">
                      <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                      </svg>
                    </div>
                    <div class="flex-1">
                      <h4 class="text-2xl font-bold text-gray-900 mb-2">{{ selectedBoutique.name }}</h4>
                      <div class="flex items-center gap-4 mb-4">
                        <span :class="['inline-flex items-center gap-1 px-3 py-1 rounded-full text-sm font-medium', selectedBoutique.is_verified ? 'bg-green-100 success-color' : 'bg-red-100 error-color']">
                          <svg v-if="selectedBoutique.is_verified" class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                          </svg>
                          <svg v-else class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"/>
                          </svg>
                          {{ selectedBoutique.is_verified ? 'Boutique Vérifiée' : 'Non Vérifiée' }}
                        </span>
                        <span v-if="selectedBoutique.is_boosted" class="inline-flex items-center gap-1 px-3 py-1 rounded-full text-sm font-medium bg-purple-100 primary-color">
                          <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                          </svg>
                          Boostée
                        </span>
                      </div>
                      <div class="text-lg font-medium primary-color">ID: #{{ selectedBoutique.id }}</div>
                    </div>
                  </div>
                </div>
  
                <!-- Info Grid -->
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                  <!-- Informations générales -->
                  <div class="bg-gray-50 rounded-xl p-6">
                    <h4 class="font-semibold text-gray-900 mb-4 flex items-center">
                      <svg class="w-5 h-5 mr-2 primary-color" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                      </svg>
                      Informations générales
                    </h4>
                    <div class="space-y-3">
                      <div class="flex justify-between">
                        <span class="text-sm text-gray-600">Nom:</span>
                        <span class="font-medium text-gray-900">{{ selectedBoutique.name }}</span>
                      </div>
                      <div class="flex justify-between">
                        <span class="text-sm text-gray-600">Marché:</span>
                        <span class="font-medium text-gray-900">{{ selectedBoutique.market_name }}</span>
                      </div>
                      <div class="flex justify-between">
                        <span class="text-sm text-gray-600">Localisation:</span>
                        <span class="font-medium text-gray-900">{{ selectedBoutique.market_location }}</span>
                      </div>
                      <div class="flex justify-between">
                        <span class="text-sm text-gray-600">Produits:</span>
                        <span class="font-medium text-gray-900">{{ selectedBoutique.products_count }} produits</span>
                      </div>
                      <div class="flex justify-between">
                        <span class="text-sm text-gray-600">Créée le:</span>
                        <span class="font-medium text-gray-900">{{ formatDate(selectedBoutique.created_at) }}</span>
                      </div>
                    </div>
                  </div>
  
                  <!-- Informations contact -->
                  <div class="bg-green-50 rounded-xl p-6">
                    <h4 class="font-semibold text-gray-900 mb-4 flex items-center">
                      <svg class="w-5 h-5 mr-2 primary-color" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                      </svg>
                      Contact
                    </h4>
                    <div class="space-y-3">
                      <div class="flex justify-between">
                        <span class="text-sm text-gray-600">Propriétaire:</span>
                        <span class="font-medium text-gray-900">{{ selectedBoutique.owner_name }}</span>
                      </div>
                      <div class="flex justify-between">
                        <span class="text-sm text-gray-600">Email:</span>
                        <span class="font-medium text-gray-900">{{ selectedBoutique.owner_email }}</span>
                      </div>
                      <div class="flex justify-between">
                        <span class="text-sm text-gray-600">Téléphone:</span>
                        <span class="font-medium text-gray-900">{{ selectedBoutique.owner_phone || 'Non renseigné' }}</span>
                      </div>
                    </div>
                  </div>
                </div>
  
                <!-- Boost Info -->
                <div v-if="selectedBoutique.is_boosted" class="bg-purple-50 rounded-xl p-6">
                  <h4 class="font-semibold text-gray-900 mb-4 flex items-center">
                    <svg class="w-5 h-5 mr-2 primary-color" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                    </svg>
                    Informations de boost
                  </h4>
                  <div class="bg-white rounded-lg p-4 border border-purple-200">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                      <div>
                        <p class="text-sm text-gray-600">Type de boost</p>
                        <p class="font-medium primary-color">{{ getBoostTypeLabel(selectedBoutique.boost_type) }}</p>
                      </div>
                      <div>
                        <p class="text-sm text-gray-600">Statut</p>
                        <span :class="['inline-flex items-center gap-1 px-2 py-1 rounded-full text-xs font-medium', getBoostStatusClass(selectedBoutique.boost_status)]">
                          {{ getBoostStatusLabel(selectedBoutique.boost_status) }}
                        </span>
                      </div>
                      <div>
                        <p class="text-sm text-gray-600">Expire le</p>
                        <p class="font-medium error-color">{{ formatDate(selectedBoutique.boost_expires_at) }}</p>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
  
      <!-- Boutique Boost Modal -->
      <div v-if="showBoutiqueBoostModal" class="fixed inset-0 bg-black/60 bg-opacity-50 flex items-center justify-center p-4 z-50">
        <div class="bg-white rounded-xl shadow-2xl max-w-4xl w-full max-h-[90vh] overflow-y-auto">
          <div class="p-6">
            <div class="flex justify-between items-center mb-6 border-b py-6 border-gray-100">
              <h3 class="text-xl font-bold text-gray-900 flex items-center">
                <div class="w-10 h-10 bg-orange rounded-lg flex items-center justify-center mr-3">
                  <svg class="w-5 h-5 " fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                  </svg>
                </div>
                {{ selectedBoutiqueBoost?.boost_status === 'pending' ? 'Validation de Boost Boutique' : 'Détails du Boost Boutique' }}
              </h3>
              <XIcon @click="closeBoutiqueBoostModal" class="h-7 w-7 cursor-pointer text-gray-700" />
            </div>
            
            <div v-if="selectedBoutiqueBoost" class="space-y-6">
              <!-- Boutique Info -->
              <div class="bg-gradient-to-r from-orange-50 to-red-50 rounded-xl p-6">
                <h4 class="font-semibold text-gray-900 mb-4 flex items-center">
                  <svg class="w-5 h-5 mr-2 primary-color" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                  </svg>
                  Boutique à booster
                </h4>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                  <div class="bg-white rounded-lg p-4 shadow-sm">
                    <p class="text-sm text-gray-600 mb-1">Nom de la boutique</p>
                    <p class="font-bold primary-color text-xl">{{ selectedBoutiqueBoost.name }}</p>
                  </div>
                  <div class="bg-white rounded-lg p-4 shadow-sm">
                    <p class="text-sm text-gray-600 mb-1">ID Boutique</p>
                    <p class="font-bold primary-color text-xl">#{{ selectedBoutiqueBoost.id }}</p>
                  </div>
                  <div class="bg-white rounded-lg p-4 shadow-sm">
                    <p class="text-sm text-gray-600 mb-1">Marché</p>
                    <p class="font-medium text-gray-900">{{ selectedBoutiqueBoost.market_name }}</p>
                  </div>
                  <div class="bg-white rounded-lg p-4 shadow-sm">
                    <p class="text-sm text-gray-600 mb-1">Produits</p>
                    <p class="font-medium text-gray-900">{{ selectedBoutiqueBoost.products_count }} produits</p>
                  </div>
                </div>
              </div>
  
              <!-- Contact Info -->
              <div class="bg-gradient-to-r from-green-50 to-emerald-50 rounded-xl p-6">
                <h4 class="font-semibold text-gray-900 mb-4 flex items-center">
                  <svg class="w-5 h-5 mr-2 primary-color" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                  </svg>
                  Informations de contact
                </h4>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                  <div class="bg-white rounded-lg p-4 shadow-sm">
                    <p class="text-sm text-gray-600 mb-1">Propriétaire</p>
                    <p class="font-medium text-gray-900">{{ selectedBoutiqueBoost.owner_name }}</p>
                  </div>
                  <div class="bg-white rounded-lg p-4 shadow-sm">
                    <p class="text-sm text-gray-600 mb-1">Email</p>
                    <p class="font-medium text-gray-900">{{ selectedBoutiqueBoost.owner_email }}</p>
                  </div>
                  <div class="bg-white rounded-lg p-4 shadow-sm">
                    <p class="text-sm text-gray-600 mb-1">Téléphone</p>
                    <p class="font-medium text-gray-900">{{ selectedBoutiqueBoost.owner_phone || 'Non renseigné' }}</p>
                  </div>
                </div>
              </div>
  
              <!-- Boost Details -->
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
                    <p class="font-bold primary-color text-lg">{{ getBoostTypeLabel(selectedBoutiqueBoost.boost_type) }}</p>
                  </div>
                  <div class="bg-white rounded-lg p-4 shadow-sm">
                    <p class="text-sm text-gray-600 mb-1">Durée</p>
                    <p class="font-bold blue-color text-lg">{{ selectedBoutiqueBoost.boost_duration }} jours</p>
                  </div>
                  <div class="bg-white rounded-lg p-4 shadow-sm">
                    <p class="text-sm text-gray-600 mb-1">Prix du boost</p>
                    <p class="font-bold primary-color text-lg">{{ formatCurrency(selectedBoutiqueBoost.boost_price) }}</p>
                  </div>
                  <div class="bg-white rounded-lg p-4 shadow-sm">
                    <p class="text-sm text-gray-600 mb-1">Statut</p>
                    <span :class="['inline-flex items-center px-2 py-1 rounded-full text-xs font-medium', getBoostStatusClass(selectedBoutiqueBoost.boost_status)]">
                      {{ getBoostStatusLabel(selectedBoutiqueBoost.boost_status) }}
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
                      <span class="font-medium text-gray-900">Du {{ formatDate(selectedBoutiqueBoost.boost_start_date) }}</span>
                    </div>
                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/>
                    </svg>
                    <div class="flex items-center">
                      <svg class="w-4 h-4 error-color mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                      </svg>
                      <span class="font-medium text-gray-900">Au {{ formatDate(selectedBoutiqueBoost.boost_end_date) }}</span>
                    </div>
                  </div>
                </div>
              </div>
  
              <!-- Actions -->
              <div v-if="selectedBoutiqueBoost.boost_status === 'pending'" class="flex justify-end space-x-3 pt-4 border-t">
                <button 
                  @click="closeBoutiqueBoostModal"
                  class="btn-gray"
                >
                  Annuler
                </button>
                <button 
                  @click="rejectBoutiqueBoost"
                  class="btn-deconnexion"
                >
                  Rejeter le boost
                </button>
                <button 
                  @click="approveBoutiqueBoost"
                  :disabled="actionLoading"
                  class="submit-btn"
                >
                  {{ actionLoading ? 'Validation...' : 'Valider le boost' }}
                </button>
              </div>
              
              <!-- Actions pour boost actif/rejeté -->
              <div v-else class="flex justify-end pt-4 border-t">
                <button 
                  @click="closeBoutiqueBoostModal"
                  class="btn-gray"
                >
                  Fermer
                </button>
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
  import Navbar from '../boutiques/Navbar.vue'
  import { ref, computed, onMounted } from 'vue'
  
  // Import des APIs admin
  import { 
    adminBoutiquesApi, 
    adminBoostApi,
    adminApiUtils 
  } from '../../services/admin-api.js'
import { Building, Building2, CheckCircle, CheckCircle2, Clock, FileWarning, Home, HomeIcon, LockIcon, RefreshCcw, SearchIcon, XIcon, ZapIcon } from 'lucide-vue-next'
  
  // Data
  const dataLoading = ref(true)
  const actionLoading = ref(false)
  
  const boutiques = ref([])
  const stats = ref({})
  
  // Filters and search
  const searchQuery = ref('')
  const statusFilter = ref('')
  
  // Pagination
  const currentPage = ref(1)
  const itemsPerPage = ref(25)
  
  // UI state
  const showBoutiqueModal = ref(false)
  const showBoutiqueBoostModal = ref(false)
  const selectedBoutique = ref(null)
  const selectedBoutiqueBoost = ref(null)
  
  // Notification
  const notification = ref({
    show: false,
    message: '',
    type: 'success'
  })
  
  // Computed properties
  const filteredBoutiques = computed(() => {
    let filtered = [...boutiques.value]
    
    // Filter by status dropdown
    if (statusFilter.value) {
      if (statusFilter.value === 'verified') {
        filtered = filtered.filter(boutique => boutique.is_verified)
      } else if (statusFilter.value === 'unverified') {
        filtered = filtered.filter(boutique => !boutique.is_verified)
      } else if (statusFilter.value === 'boosted') {
        filtered = filtered.filter(boutique => boutique.is_boosted)
      }
    }
    
    // Filter by search query
    if (searchQuery.value) {
      const query = searchQuery.value.toLowerCase()
      filtered = filtered.filter(boutique => 
        boutique.name.toLowerCase().includes(query) ||
        boutique.market_name.toLowerCase().includes(query) ||
        boutique.owner_name.toLowerCase().includes(query) ||
        boutique.owner_email.toLowerCase().includes(query)
      )
    }
    
    return filtered
  })
  
  const paginatedBoutiques = computed(() => {
    const start = (currentPage.value - 1) * itemsPerPage.value
    const end = start + itemsPerPage.value
    return filteredBoutiques.value.slice(start, end)
  })
  
  const totalPages = computed(() => {
    return Math.ceil(filteredBoutiques.value.length / itemsPerPage.value)
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
  
  // Methods
  const loadAllData = async () => {
    dataLoading.value = true
    
    try {
      await Promise.all([
        loadBoutiques(),
        loadStats()
      ])
    } catch (error) {
      console.error('Erreur lors du chargement des données:', error)
      showNotification('Erreur lors du chargement des données', 'error')
    } finally {
      dataLoading.value = false
    }
  }
  
  const loadBoutiques = async () => {
    try {
      const params = adminApiUtils.buildPaginationParams(1, 1000)
      
      if (statusFilter.value) {
        params.status = statusFilter.value
      }
      
      if (searchQuery.value) {
        params.search = searchQuery.value
      }
      
      const response = await adminBoutiquesApi.getAllBoutiques(params)
      
      if (response.success) {
        // Simuler des données de boost pour les boutiques
        boutiques.value = (response.data || []).map(boutique => ({
          ...boutique,
          // Simuler des données de boost
          is_boosted: Math.random() > 0.8,
          boost_status: Math.random() > 0.7 ? 'pending' : (Math.random() > 0.5 ? 'active' : null),
          boost_type: ['premium', 'featured', 'urgent'][Math.floor(Math.random() * 3)],
          boost_expires_at: new Date(Date.now() + 30 * 24 * 60 * 60 * 1000).toISOString(),
          boost_duration: 30,
          boost_price: 50000,
          boost_start_date: new Date().toISOString(),
          boost_end_date: new Date(Date.now() + 30 * 24 * 60 * 60 * 1000).toISOString(),
          // Données de contact simulées
          owner_name: boutique.created_by_name || 'Propriétaire',
          owner_email: boutique.created_by_email || 'contact@boutique.com',
          owner_phone: '+225 07 00 00 00 00',
          market_name: boutique.market || 'Marché d\'Adjamé',
          market_location: 'Adjamé, Abidjan',
          products_count: boutique.total_products || 0
        }))
        
        console.log('✅ Boutiques chargées depuis l\'API:', boutiques.value.length)
      } else {
        throw new Error(response.error || 'Erreur lors du chargement des boutiques')
      }
    } catch (error) {
      console.error('❌ Erreur lors du chargement des boutiques:', error)
      showNotification('Erreur lors du chargement des boutiques: ' + error.message, 'error')
      throw error
    }
  }
  
  const loadStats = async () => {
    try {
      const response = await adminBoutiquesApi.getBoutiquesStats()
      
      if (response.success) {
        const data = response.data
        stats.value = {
          total_boutiques: data.boutiques?.total_boutiques || 0,
          verified_boutiques: data.boutiques?.verified_boutiques || 0,
          boosted_boutiques: Math.floor((data.boutiques?.total_boutiques || 0) * 0.2), // 20% boostées
          pending_boosts: Math.floor((data.boutiques?.total_boutiques || 0) * 0.1) // 10% en attente
        }
        console.log('✅ Statistiques boutiques chargées:', stats.value)
      } else {
        throw new Error(response.error || 'Erreur lors du chargement des statistiques')
      }
    } catch (error) {
      console.error('❌ Erreur lors du chargement des statistiques:', error)
      stats.value = {
        total_boutiques: 0,
        verified_boutiques: 0,
        boosted_boutiques: 0,
        pending_boosts: 0
      }
    }
  }
  
  // Filter and search methods
  const handleSearch = () => {
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
  const openBoutiqueDetails = (boutique) => {
    selectedBoutique.value = boutique
    showBoutiqueModal.value = true
  }
  
  const closeBoutiqueModal = () => {
    showBoutiqueModal.value = false
    selectedBoutique.value = null
  }
  
  const openBoutiqueBoostValidation = (boutique) => {
    selectedBoutiqueBoost.value = boutique
    showBoutiqueBoostModal.value = true
  }
  
  const viewBoutiqueBoostDetails = (boutique) => {
    selectedBoutiqueBoost.value = boutique
    showBoutiqueBoostModal.value = true
  }
  
  const closeBoutiqueBoostModal = () => {
    showBoutiqueBoostModal.value = false
    selectedBoutiqueBoost.value = null
  }
  
  const approveBoutiqueBoost = async () => {
    if (!selectedBoutiqueBoost.value) return
    
    actionLoading.value = true
    
    try {
      // Simuler l'approbation du boost boutique
      await new Promise(resolve => setTimeout(resolve, 1000))
      
      // Update local data
      const boutiqueIndex = boutiques.value.findIndex(b => b.id === selectedBoutiqueBoost.value.id)
      if (boutiqueIndex !== -1) {
        boutiques.value[boutiqueIndex].is_boosted = true
        boutiques.value[boutiqueIndex].boost_status = 'active'
      }
      
      showNotification('Boost boutique validé avec succès', 'success')
      closeBoutiqueBoostModal()
      await loadStats()
      
    } catch (error) {
      console.error('Erreur lors de la validation du boost boutique:', error)
      showNotification('Erreur lors de la validation du boost boutique', 'error')
    } finally {
      actionLoading.value = false
    }
  }
  
  const rejectBoutiqueBoost = async () => {
    if (!selectedBoutiqueBoost.value) return
    
    actionLoading.value = true
    
    try {
      // Simuler le rejet du boost boutique
      await new Promise(resolve => setTimeout(resolve, 1000))
      
      // Update local data
      const boutiqueIndex = boutiques.value.findIndex(b => b.id === selectedBoutiqueBoost.value.id)
      if (boutiqueIndex !== -1) {
        boutiques.value[boutiqueIndex].boost_status = 'rejected'
      }
      
      showNotification('Boost boutique rejeté avec succès', 'success')
      closeBoutiqueBoostModal()
      await loadStats()
      
    } catch (error) {
      console.error('Erreur lors du rejet du boost boutique:', error)
      showNotification('Erreur lors du rejet du boost boutique', 'error')
    } finally {
      actionLoading.value = false
    }
  }
  
  const toggleBoutiqueVerification = async (boutique) => {
    try {
      // Simuler la mise à jour de la vérification
      await new Promise(resolve => setTimeout(resolve, 500))
      
      // Update local data
      const boutiqueIndex = boutiques.value.findIndex(b => b.id === boutique.id)
      if (boutiqueIndex !== -1) {
        boutiques.value[boutiqueIndex].is_verified = !boutiques.value[boutiqueIndex].is_verified
      }
      
      const message = boutique.is_verified ? 'Vérification retirée avec succès' : 'Boutique vérifiée avec succès'
      showNotification(message, 'success')
      
    } catch (error) {
      console.error('Erreur lors du changement de vérification:', error)
      showNotification('Erreur lors de la mise à jour de la vérification', 'error')
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
  
  .animate-float-slow {
    animation: float-slow 8s ease-in-out infinite;
  }
  
  .animate-float-reverse {
    animation: float-reverse 10s ease-in-out infinite;
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
  </style>
  