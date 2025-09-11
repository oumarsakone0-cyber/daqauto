<template>
    <main class="flex-1 min-h-screen bg-cream" style="margin-top: 50px">
      <!-- Hero Section -->
      <div class="bg-dark-green rounded-lg p-8 mb-6 text-white shadow-lg">
        <div class="flex items-center justify-between">
          <div>
            <h1 class="text-3xl font-bold mb-2">Gestion <span class="text-orange">Clients</span></h1>
            <p class="text-gray-300 mb-6">Gérez vos clients inscrits et vos prospects</p>
            <div class="flex space-x-3">
              <button 
                @click="showAddClientModal = true"
                type="button" 
                class="text-white bg-orange hover:bg-orange-600 focus:ring-4 focus:ring-orange-300 font-medium rounded-lg text-sm px-6 py-3 focus:outline-none flex items-center transition-all duration-200 shadow-md hover:shadow-lg"
              >
                Ajouter Client
                <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                </svg>
              </button>
              <button 
                @click="showImportModal = true"
                type="button" 
                class="text-white bg-white/20 hover:bg-white/30 focus:ring-4 focus:ring-white/20 font-medium rounded-lg text-sm px-6 py-3 focus:outline-none flex items-center transition-all duration-200"
              >
                Importer
                <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path>
                </svg>
              </button>
            </div>
          </div>
          <div class="flex space-x-4">
            <div class="w-14 h-14 bg-white/10 backdrop-blur-sm rounded-full flex items-center justify-center shadow-lg hover:bg-white/20 transition-all cursor-pointer">
              <span class="text-2xl">👥</span>
            </div>
            <div class="w-14 h-14 bg-white/10 backdrop-blur-sm rounded-full flex items-center justify-center shadow-lg hover:bg-white/20 transition-all cursor-pointer">
              <span class="text-2xl">🎯</span>
            </div>
            <div class="w-14 h-14 bg-white/10 backdrop-blur-sm rounded-full flex items-center justify-center shadow-lg hover:bg-white/20 transition-all cursor-pointer">
              <span class="text-2xl">📊</span>
            </div>
          </div>
        </div>
      </div>
  
      <!-- Stats Cards -->
      <div class="bg-white border border-gray-200 rounded-lg shadow-md mb-6">
        <div class="p-6 border-b border-gray-200">
          <div class="grid grid-cols-1 md:grid-cols-6 gap-6">
            <div class="text-center p-4 rounded-lg bg-gray-50 hover:bg-gray-100 transition-all">
              <div class="text-sm text-gray-600 mb-1 flex items-center justify-center">
                <svg class="w-5 h-5 mr-1 text-orange" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                </svg>
                Période
              </div>
              <select 
                v-model="selectedPeriod" 
                @change="updateStats"
                class="text-sm font-semibold text-gray-900 bg-transparent border-none focus:ring-0 cursor-pointer"
              >
                <option value="all">Tout</option>
                <option value="today">Aujourd'hui</option>
                <option value="week">Cette semaine</option>
                <option value="month">Ce mois</option>
              </select>
            </div>
            <div class="text-center p-4 rounded-lg bg-gray-50 hover:bg-gray-100 transition-all">
              <div class="text-sm text-gray-600 mb-1">Total Clients</div>
              <div class="text-2xl font-bold text-gray-900">{{ currentStats.totalClients }}</div>
              <div class="text-xs text-green-600 mt-1">+{{ currentStats.newClients }} nouveaux</div>
            </div>
            <div class="text-center p-4 rounded-lg bg-gray-50 hover:bg-gray-100 transition-all">
              <div class="text-sm text-gray-600 mb-1">Clients Inscrits</div>
              <div class="text-2xl font-bold text-blue-600">{{ currentStats.registeredClients }}</div>
              <div class="text-xs text-gray-500 mt-1">{{ Math.round((currentStats.registeredClients / currentStats.totalClients) * 100) }}% du total</div>
            </div>
            <div class="text-center p-4 rounded-lg bg-gray-50 hover:bg-gray-100 transition-all">
              <div class="text-sm text-gray-600 mb-1">Prospects</div>
              <div class="text-2xl font-bold text-orange">{{ currentStats.prospects }}</div>
              <div class="text-xs text-gray-500 mt-1">{{ Math.round((currentStats.prospects / currentStats.totalClients) * 100) }}% du total</div>
            </div>
            <div class="text-center p-4 rounded-lg bg-gray-50 hover:bg-gray-100 transition-all">
              <div class="text-sm text-gray-600 mb-1">Clients Actifs</div>
              <div class="text-2xl font-bold text-green-600">{{ currentStats.activeClients }}</div>
              <div class="text-xs text-green-600 mt-1">{{ currentStats.activityRate }}% d'activité</div>
            </div>
            <div class="text-center p-4 rounded-lg bg-gray-50 hover:bg-gray-100 transition-all">
              <div class="text-sm text-gray-600 mb-1">Valeur Moyenne</div>
              <div class="text-2xl font-bold text-gray-900">{{ currentStats.avgOrderValue.toLocaleString() }}</div>
              <div class="text-xs text-blue-600 mt-1">FCFA par client</div>
            </div>
          </div>
        </div>
      </div>
  
      <!-- Actions Bar -->
      <div class="rounded-lg mb-6">
        <div class="flex items-center justify-between p-6 border-b border-gray-200 bg-white rounded-lg shadow-md">
          <h2 class="text-2xl font-bold text-gray-900">Base Clients</h2>
          <div class="flex items-center space-x-3">
            <!-- Export Button -->
            <div class="relative">
              <button 
                @click="showExportDropdown = !showExportDropdown"
                type="button" 
                class="text-gray-700 bg-white border border-gray-300 focus:outline-none hover:bg-gray-50 focus:ring-4 focus:ring-gray-200 font-medium rounded-lg text-sm px-5 py-2.5 transition-all flex items-center"
              >
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                </svg>
                Exporter
                <svg class="w-4 h-4 ml-2" fill="currentColor" viewBox="0 0 20 20">
                  <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd"/>
                </svg>
              </button>
              <div v-if="showExportDropdown" class="absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg z-20 border border-gray-200">
                <div class="py-1">
                  <button @click="exportClients('pdf')" class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 flex items-center">
                    <svg class="w-4 h-4 mr-2 text-red-500" fill="currentColor" viewBox="0 0 20 20">
                      <path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4z" clip-rule="evenodd"/>
                    </svg>
                    Exporter en PDF
                  </button>
                  <button @click="exportClients('excel')" class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 flex items-center">
                    <svg class="w-4 h-4 mr-2 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                      <path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4z" clip-rule="evenodd"/>
                    </svg>
                    Exporter en Excel
                  </button>
                </div>
              </div>
            </div>
            
            <button 
              @click="showSegmentModal = true"
              type="button" 
              class="text-gray-700 bg-white border border-gray-300 focus:outline-none hover:bg-gray-50 focus:ring-4 focus:ring-gray-200 font-medium rounded-lg text-sm px-5 py-2.5 transition-all flex items-center"
            >
              <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.99 1.99 0 013 12V7a4 4 0 014-4z"></path>
              </svg>
              Segmenter
            </button>
          </div>
        </div>
      </div>
  
      <!-- Clients Section -->
      <div class="border border-gray-200 rounded-lg shadow-md overflow-hidden">
        <!-- Filter Tabs -->
        <div class="px-6 py-4 border-b border-gray-200 bg-white">
          <ul class="flex flex-wrap -mb-px text-sm font-medium text-center text-gray-500 overflow-x-auto">
            <li class="mr-2" v-for="tab in filterTabs" :key="tab.value">
              <a 
                href="#" 
                @click.prevent="activeFilter = tab.value"
                :class="[
                  'inline-block p-4 border-b-2 rounded-t-lg transition-all',
                  activeFilter === tab.value 
                    ? 'text-orange border-orange font-semibold' 
                    : 'border-transparent hover:text-orange hover:border-orange/30'
                ]"
              >
                {{ tab.label }} ({{ filterCounts[tab.value] }})
              </a>
            </li>
          </ul>
        </div>
  
        <!-- Search and Filters -->
        <div class="px-6 py-4 border-b border-gray-200 bg-white">
          <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div class="relative w-full md:w-96">
              <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                <svg class="w-4 h-4 text-gray-500" fill="currentColor" viewBox="0 0 20 20">
                  <path fill-rule="evenodd" d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z" clip-rule="evenodd"/>
                </svg>
              </div>
              <input 
                v-model="searchQuery"
                type="text" 
                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-orange focus:border-orange block w-full pl-10 p-2.5 transition-all" 
                placeholder="Rechercher un client..."
              >
            </div>
            <div class="flex items-center space-x-2">
              <select 
                v-model="selectedSegment"
                class="border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-orange focus:border-orange"
              >
                <option value="">Tous les segments</option>
                <option value="vip">Clients VIP</option>
                <option value="regular">Clients réguliers</option>
                <option value="new">Nouveaux clients</option>
                <option value="inactive">Clients inactifs</option>
              </select>
              <button type="button" class="text-gray-500 bg-white border border-gray-300 focus:outline-none hover:bg-gray-100 focus:ring-4 focus:ring-gray-200 font-medium rounded-lg text-sm px-3 py-2.5 flex items-center transition-all">
                <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                  <path fill-rule="evenodd" d="M3 3a1 1 0 011-1h12a1 1 0 011 1v3a1 1 0 01-.293.707L12 11.414V15a1 1 0 01-.293.707l-2 2A1 1 0 018 17v-5.586L3.293 6.707A1 1 0 013 6V3z" clip-rule="evenodd"/>
                </svg>
                Filtres avancés
              </button>
            </div>
          </div>
        </div>
  
        <!-- Clients Table -->
        <div class="relative overflow-x-auto">
          <table class="w-full text-sm text-left text-gray-500">
            <thead class="text-xs text-gray-700 uppercase bg-gray-50">
              <tr>
                <th scope="col" class="p-4">
                  <div class="flex items-center">
                    <input id="checkbox-all" type="checkbox" class="w-4 h-4 text-orange bg-gray-100 border-gray-300 rounded focus:ring-orange focus:ring-2">
                    <label for="checkbox-all" class="sr-only">checkbox</label>
                  </div>
                </th>
                <th scope="col" class="px-6 py-3">Client</th>
                <th scope="col" class="px-6 py-3">Type</th>
                <th scope="col" class="px-6 py-3">Contact</th>
                <th scope="col" class="px-6 py-3">Commandes</th>
                <th scope="col" class="px-6 py-3">Valeur totale</th>
                <th scope="col" class="px-6 py-3">Dernière activité</th>
                <th scope="col" class="px-6 py-3">Statut</th>
                <th scope="col" class="px-6 py-3">Tags</th>
                <th scope="col" class="px-6 py-3">Action</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="client in filteredClients" :key="client.id" class="bg-white border-b hover:bg-gray-50 transition-colors">
                <td class="w-4 p-4">
                  <div class="flex items-center">
                    <input :id="`checkbox-${client.id}`" type="checkbox" class="w-4 h-4 text-orange bg-gray-100 border-gray-300 rounded focus:ring-orange focus:ring-2">
                    <label :for="`checkbox-${client.id}`" class="sr-only">checkbox</label>
                  </div>
                </td>
                <td class="px-6 py-4">
                  <div class="flex items-center space-x-3">
                    <div class="w-10 h-10 bg-gray-100 rounded-full flex items-center justify-center">
                      <span class="text-sm font-medium text-gray-700">{{ client.name.charAt(0) }}</span>
                    </div>
                    <div>
                      <div class="text-base font-semibold text-gray-900">{{ client.name }}</div>
                      <div class="font-normal text-gray-500">ID: {{ client.id }}</div>
                    </div>
                  </div>
                </td>
                <td class="px-6 py-4">
                  <span :class="getTypeClass(client.type)" class="px-2 py-1 text-xs font-medium rounded-full">
                    {{ getTypeLabel(client.type) }}
                  </span>
                </td>
                <td class="px-6 py-4">
                  <div>
                    <div class="text-gray-900">{{ client.email }}</div>
                    <div class="text-gray-500 text-sm">{{ client.phone }}</div>
                  </div>
                </td>
                <td class="px-6 py-4">
                  <div class="text-center">
                    <div class="text-gray-900 font-medium">{{ client.totalOrders }}</div>
                    <div class="text-gray-500 text-xs">commandes</div>
                  </div>
                </td>
                <td class="px-6 py-4">
                  <div class="text-gray-900 font-medium">{{ client.totalValue.toLocaleString() }} FCFA</div>
                  <div class="text-gray-500 text-xs">{{ Math.round(client.totalValue / client.totalOrders).toLocaleString() }} FCFA/cmd</div>
                </td>
                <td class="px-6 py-4">
                  <div class="text-gray-900">{{ formatDate(client.lastActivity) }}</div>
                  <div class="text-gray-500 text-xs">{{ getActivityStatus(client.lastActivity) }}</div>
                </td>
                <td class="px-6 py-4">
                  <span :class="getStatusClass(client.status)" class="px-2 py-1 text-xs font-medium rounded-full">
                    {{ client.status }}
                  </span>
                </td>
                <td class="px-6 py-4">
                  <div class="flex flex-wrap gap-1">
                    <span 
                      v-for="tag in client.tags" 
                      :key="tag"
                      class="px-2 py-1 bg-gray-100 text-gray-700 text-xs rounded-full"
                    >
                      {{ tag }}
                    </span>
                  </div>
                </td>
                <td class="px-6 py-4">
                  <div class="relative">
                    <button 
                      @click="toggleActionMenu(client.id)"
                      class="font-medium text-gray-600 hover:text-gray-900 p-1 rounded-md hover:bg-gray-100 transition-colors"
                    >
                      <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M10 6a2 2 0 110-4 2 2 0 010 4zM10 12a2 2 0 110-4 2 2 0 010 4zM10 18a2 2 0 110-4 2 2 0 010 4z"/>
                      </svg>
                    </button>
                    <div v-if="activeActionMenu === client.id" class="absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg z-10 border border-gray-200">
                      <div class="py-1">
                        <button @click="viewClient(client)" class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Voir détails</button>
                        <button @click="editClient(client)" class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Modifier</button>
                        <button @click="sendMessage(client)" class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Envoyer message</button>
                        <button @click="viewOrders(client)" class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Voir commandes</button>
                        <button @click="deleteClient(client.id)" class="block w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-gray-100">Supprimer</button>
                      </div>
                    </div>
                  </div>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
  
        <!-- Pagination -->
        <nav class="flex flex-col md:flex-row md:items-center md:justify-between p-6 bg-white" aria-label="Table navigation">
          <span class="text-sm font-normal text-gray-500 mb-4 md:mb-0">
            Affichage de <span class="font-semibold text-gray-900">{{ (currentPage - 1) * itemsPerPage + 1 }}</span> à 
            <span class="font-semibold text-gray-900">{{ Math.min(currentPage * itemsPerPage, filteredClients.length) }}</span> 
            sur <span class="font-semibold text-gray-900">{{ filteredClients.length }}</span> clients
          </span>
          <ul class="inline-flex -space-x-px text-sm h-8">
            <li>
              <button 
                @click="currentPage > 1 && currentPage--"
                :disabled="currentPage === 1"
                class="flex items-center justify-center px-3 h-8 ml-0 leading-tight text-gray-500 bg-white border border-gray-300 rounded-l-lg hover:bg-gray-100 hover:text-gray-700 transition-colors disabled:opacity-50"
              >
                Précédent
              </button>
            </li>
            <li v-for="page in totalPages" :key="page">
              <button 
                @click="currentPage = page"
                :class="[
                  'flex items-center justify-center px-3 h-8 leading-tight border border-gray-300 transition-colors',
                  currentPage === page 
                    ? 'text-white bg-orange border-orange' 
                    : 'text-gray-500 bg-white hover:bg-gray-100 hover:text-gray-700'
                ]"
              >
                {{ page }}
              </button>
            </li>
            <li>
              <button 
                @click="currentPage < totalPages && currentPage++"
                :disabled="currentPage === totalPages"
                class="flex items-center justify-center px-3 h-8 leading-tight text-gray-500 bg-white border border-gray-300 rounded-r-lg hover:bg-gray-100 hover:text-gray-700 transition-colors disabled:opacity-50"
              >
                Suivant
              </button>
            </li>
          </ul>
        </nav>
      </div>
  
      <!-- Modals -->
      <AddClientModal 
        v-if="showAddClientModal" 
        @close="showAddClientModal = false"
        @save="handleAddClient"
      />
  
      <ViewClientModal 
        v-if="showViewModal" 
        :client="selectedClient"
        @close="showViewModal = false"
      />
  
      <EditClientModal 
        v-if="showEditModal" 
        :client="selectedClient"
        @close="showEditModal = false"
        @save="handleEditClient"
      />
  
      <ImportClientsModal 
        v-if="showImportModal" 
        @close="showImportModal = false"
        @import="handleImportClients"
      />
  
      <SegmentModal 
        v-if="showSegmentModal" 
        @close="showSegmentModal = false"
        @segment="handleSegmentation"
      />
  
      <!-- Notification Toast -->
      <div v-if="notification.show" class="fixed bottom-4 right-4 z-50 bg-green-500 text-white py-3 px-5 rounded-md shadow-lg transition-all duration-300">
        {{ notification.message }}
      </div>
    </main>
  </template>
  
  <script setup>
  import { ref, computed, onMounted, onUnmounted } from 'vue'
  import AddClientModal from './AddClientModal.vue'
  import ViewClientModal from './ViewClientModal.vue'
  import EditClientModal from './EditClientModal.vue'
  import ImportClientsModal from './ImportClientsModal.vue'
  import SegmentModal from './SegmentModal.vue'
  
  // États réactifs
  const showAddClientModal = ref(false)
  const showViewModal = ref(false)
  const showEditModal = ref(false)
  const showImportModal = ref(false)
  const showSegmentModal = ref(false)
  const showExportDropdown = ref(false)
  const selectedClient = ref(null)
  const activeActionMenu = ref(null)
  const searchQuery = ref('')
  const activeFilter = ref('all')
  const selectedSegment = ref('')
  const selectedPeriod = ref('month')
  const currentPage = ref(1)
  const itemsPerPage = ref(10)
  const notification = ref({ show: false, message: '' })
  
  // Statistiques
  const stats = ref({
    all: { totalClients: 1250, registeredClients: 890, prospects: 360, activeClients: 750, newClients: 45, activityRate: 60, avgOrderValue: 85000 },
    today: { totalClients: 8, registeredClients: 5, prospects: 3, activeClients: 6, newClients: 2, activityRate: 75, avgOrderValue: 92000 },
    week: { totalClients: 156, registeredClients: 112, prospects: 44, activeClients: 98, newClients: 12, activityRate: 63, avgOrderValue: 88000 },
    month: { totalClients: 642, registeredClients: 456, prospects: 186, activeClients: 385, newClients: 67, activityRate: 60, avgOrderValue: 85000 }
  })
  
  const currentStats = computed(() => stats.value[selectedPeriod.value])
  
  // Filtres
  const filterTabs = [
    { value: 'all', label: 'Tous' },
    { value: 'registered', label: 'Inscrits' },
    { value: 'prospects', label: 'Prospects' },
    { value: 'active', label: 'Actifs' },
    { value: 'inactive', label: 'Inactifs' },
    { value: 'vip', label: 'VIP' }
  ]
  
  // Données des clients
  const clients = ref([
    {
      id: 1,
      name: 'Marie Dubois',
      email: 'marie.dubois@email.com',
      phone: '+225 07 12 34 56 78',
      type: 'registered',
      status: 'Actif',
      totalOrders: 12,
      totalValue: 850000,
      lastActivity: '2024-01-15T10:30:00',
      registrationDate: '2023-08-15T00:00:00',
      tags: ['VIP', 'Fidèle'],
      segment: 'vip',
      address: 'Cocody, Abidjan',
      source: 'Site web'
    },
    {
      id: 2,
      name: 'Jean Martin',
      email: 'jean.martin@email.com',
      phone: '+225 05 98 76 54 32',
      type: 'prospect',
      status: 'Prospect',
      totalOrders: 0,
      totalValue: 0,
      lastActivity: '2024-01-10T14:20:00',
      registrationDate: null,
      tags: ['Nouveau', 'Intéressé'],
      segment: 'new',
      address: 'Marcory, Abidjan',
      source: 'Contact direct'
    },
    {
      id: 3,
      name: 'Sophie Laurent',
      email: 'sophie.laurent@email.com',
      phone: '+225 01 23 45 67 89',
      type: 'registered',
      status: 'Actif',
      totalOrders: 8,
      totalValue: 420000,
      lastActivity: '2024-01-12T09:15:00',
      registrationDate: '2023-11-20T00:00:00',
      tags: ['Régulier'],
      segment: 'regular',
      address: 'Plateau, Abidjan',
      source: 'Site web'
    },
    {
      id: 4,
      name: 'Pierre Durand',
      email: 'pierre.durand@email.com',
      phone: '+225 07 89 01 23 45',
      type: 'registered',
      status: 'Inactif',
      totalOrders: 3,
      totalValue: 125000,
      lastActivity: '2023-11-28T16:45:00',
      registrationDate: '2023-06-10T00:00:00',
      tags: ['Inactif'],
      segment: 'inactive',
      address: 'Yopougon, Abidjan',
      source: 'Site web'
    },
    {
      id: 5,
      name: 'Claire Moreau',
      email: 'claire.moreau@email.com',
      phone: '+225 05 67 89 01 23',
      type: 'prospect',
      status: 'Prospect',
      totalOrders: 0,
      totalValue: 0,
      lastActivity: '2024-01-14T11:30:00',
      registrationDate: null,
      tags: ['WhatsApp', 'Intéressé'],
      segment: 'new',
      address: 'Adjamé, Abidjan',
      source: 'WhatsApp'
    },
    {
      id: 6,
      name: 'Ahmed Koné',
      email: 'ahmed.kone@email.com',
      phone: '+225 07 45 67 89 01',
      type: 'registered',
      status: 'Actif',
      totalOrders: 25,
      totalValue: 1250000,
      lastActivity: '2024-01-15T08:20:00',
      registrationDate: '2023-03-12T00:00:00',
      tags: ['VIP', 'Gros acheteur'],
      segment: 'vip',
      address: 'Treichville, Abidjan',
      source: 'Site web'
    }
  ])
  
  // Clients filtrés
  const filteredClients = computed(() => {
    let filtered = clients.value
  
    // Filtre par type/statut
    if (activeFilter.value !== 'all') {
      switch (activeFilter.value) {
        case 'registered':
          filtered = filtered.filter(client => client.type === 'registered')
          break
        case 'prospects':
          filtered = filtered.filter(client => client.type === 'prospect')
          break
        case 'active':
          filtered = filtered.filter(client => client.status === 'Actif')
          break
        case 'inactive':
          filtered = filtered.filter(client => client.status === 'Inactif')
          break
        case 'vip':
          filtered = filtered.filter(client => client.segment === 'vip')
          break
      }
    }
  
    // Filtre par segment
    if (selectedSegment.value) {
      filtered = filtered.filter(client => client.segment === selectedSegment.value)
    }
  
    // Filtre par recherche
    if (searchQuery.value) {
      const query = searchQuery.value.toLowerCase()
      filtered = filtered.filter(client => 
        client.name.toLowerCase().includes(query) ||
        client.email.toLowerCase().includes(query) ||
        client.phone.includes(query)
      )
    }
  
    return filtered
  })
  
  // Compteurs pour les filtres
  const filterCounts = computed(() => {
    return {
      'all': clients.value.length,
      'registered': clients.value.filter(client => client.type === 'registered').length,
      'prospects': clients.value.filter(client => client.type === 'prospect').length,
      'active': clients.value.filter(client => client.status === 'Actif').length,
      'inactive': clients.value.filter(client => client.status === 'Inactif').length,
      'vip': clients.value.filter(client => client.segment === 'vip').length
    }
  })
  
  // Pagination
  const totalPages = computed(() => Math.ceil(filteredClients.value.length / itemsPerPage.value))
  
  // Fonctions
  const getTypeClass = (type) => {
    switch (type) {
      case 'registered':
        return 'bg-blue-100 text-blue-800'
      case 'prospect':
        return 'bg-orange-100 text-orange-800'
      default:
        return 'bg-gray-100 text-gray-800'
    }
  }
  
  const getTypeLabel = (type) => {
    switch (type) {
      case 'registered':
        return 'Inscrit'
      case 'prospect':
        return 'Prospect'
      default:
        return type
    }
  }
  
  const getStatusClass = (status) => {
    switch (status) {
      case 'Actif':
        return 'bg-green-100 text-green-800'
      case 'Inactif':
        return 'bg-red-100 text-red-800'
      case 'Prospect':
        return 'bg-yellow-100 text-yellow-800'
      default:
        return 'bg-gray-100 text-gray-800'
    }
  }
  
  const formatDate = (dateString) => {
    return new Date(dateString).toLocaleDateString('fr-FR')
  }
  
  const getActivityStatus = (lastActivity) => {
    const date = new Date(lastActivity)
    const now = new Date()
    const diffInDays = Math.floor((now - date) / (1000 * 60 * 60 * 24))
    
    if (diffInDays === 0) return 'Aujourd\'hui'
    if (diffInDays === 1) return 'Hier'
    if (diffInDays < 7) return `Il y a ${diffInDays} jours`
    if (diffInDays < 30) return `Il y a ${Math.floor(diffInDays / 7)} semaines`
    return `Il y a ${Math.floor(diffInDays / 30)} mois`
  }
  
  const updateStats = () => {
    console.log('Mise à jour des statistiques pour:', selectedPeriod.value)
  }
  
  const toggleActionMenu = (clientId) => {
    activeActionMenu.value = activeActionMenu.value === clientId ? null : clientId
  }
  
  const viewClient = (client) => {
    selectedClient.value = client
    showViewModal.value = true
    activeActionMenu.value = null
  }
  
  const editClient = (client) => {
    selectedClient.value = client
    showEditModal.value = true
    activeActionMenu.value = null
  }
  
  const sendMessage = (client) => {
    console.log('Envoyer message à:', client.name)
    activeActionMenu.value = null
  }
  
  const viewOrders = (client) => {
    console.log('Voir commandes de:', client.name)
    activeActionMenu.value = null
  }
  
  const deleteClient = (clientId) => {
    if (confirm('Êtes-vous sûr de vouloir supprimer ce client ?')) {
      clients.value = clients.value.filter(client => client.id !== clientId)
      showNotification('Client supprimé avec succès!')
    }
    activeActionMenu.value = null
  }
  
  const exportClients = (format) => {
    console.log(`Export des clients en ${format}`)
    showExportDropdown.value = false
    showNotification(`Export ${format.toUpperCase()} en cours...`)
  }
  
  const handleAddClient = (clientData) => {
    const newClient = {
      ...clientData,
      id: Date.now(),
      totalOrders: 0,
      totalValue: 0,
      lastActivity: new Date().toISOString(),
      registrationDate: clientData.type === 'registered' ? new Date().toISOString() : null
    }
    clients.value.push(newClient)
    showAddClientModal.value = false
    showNotification('Client ajouté avec succès!')
  }
  
  const handleEditClient = (clientData) => {
    const index = clients.value.findIndex(c => c.id === clientData.id)
    if (index !== -1) {
      clients.value[index] = { ...clientData }
    }
    showEditModal.value = false
    showNotification('Client modifié avec succès!')
  }
  
  const handleImportClients = (importData) => {
    console.log('Import de clients:', importData)
    showImportModal.value = false
    showNotification('Clients importés avec succès!')
  }
  
  const handleSegmentation = (segmentData) => {
    console.log('Segmentation:', segmentData)
    showSegmentModal.value = false
    showNotification('Segmentation appliquée avec succès!')
  }
  
  const showNotification = (message) => {
    notification.value.message = message
    notification.value.show = true
    setTimeout(() => {
      notification.value.show = false
    }, 3000)
  }
  
  // Fermer les dropdowns en cliquant à l'extérieur
  const handleClickOutside = (event) => {
    if (!event.target.closest('.relative')) {
      showExportDropdown.value = false
      activeActionMenu.value = null
    }
  }
  
  onMounted(() => {
    document.addEventListener('click', handleClickOutside)
  })
  
  onUnmounted(() => {
    document.removeEventListener('click', handleClickOutside)
  })
  </script>
  
  <style scoped>
  .bg-dark-green {
    background-color: #22331D;
  }
  
  .bg-cream {
    background-color: #EFEDE7;
  }
  
  .bg-orange {
    background-color: #F65A11;
  }
  
  .text-orange {
    color: #F65A11;
  }
  
  .border-orange {
    border-color: #F65A11;
  }
  
  .hover\:bg-orange-600:hover {
    background-color: #e54a0a;
  }
  
  .focus\:ring-orange:focus {
    --tw-ring-color: rgba(246, 90, 17, 0.5);
  }
  
  .focus\:border-orange:focus {
    border-color: #F65A11;
  }
  
  .focus\:ring-orange-300:focus {
    --tw-ring-color: rgba(246, 90, 17, 0.3);
  }
  </style>
  