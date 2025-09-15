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
    <div class="w-full max-w-[1600px] mx-auto px-4 sm:px-6 py-4 sm:py-8 relative z-10">
      <!-- Header responsive -->
      <div class="flex flex-col space-y-4 lg:flex-row lg:items-center lg:justify-between lg:space-y-0 mb-6 sm:mb-8">
        <div>
          <h1 class="text-2xl sm:text-3xl font-bold text-gray-900 mb-2">Dashboard Administrateur</h1>
          <p class="text-sm sm:text-base text-gray-600">Vue d'ensemble globale de la plateforme</p>
        </div>
        
        <!-- Actions mobiles optimisées -->
        <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-2 sm:gap-3">
          <!-- Export dropdown -->
          <div class="relative">
            <button 
              class="w-full sm:w-auto inline-flex items-center justify-center px-3 sm:px-4 py-2 hover:opacity-80 bg-degrade-orange"
            >
              <span class="mr-2">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path><polyline points="7 10 12 15 17 10"></polyline><line x1="12" x2="12" y1="15" y2="3"></line></svg>
              </span>

              <span >Exporter</span>
            </button>
          </div>

          <!-- Refresh button -->
          <button 
            class="w-full sm:w-auto inline-flex items-center justify-center px-3 sm:px-4 py-2 hover:opacity-80 bg-degrade-ring"
          >
            <span class="mr-2">
              <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 12a9 9 0 0 1 9-9 9.75 9.75 0 0 1 6.74 2.74L21 8"></path><path d="M21 3v5h-5"></path><path d="M21 12a9 9 0 0 1-9 9 9.75 9.75 0 0 1-6.74-2.74L3 16"></path><path d="M3 21v-5h5"></path></svg>
              </span>
            <span >Actualiser</span>
          </button>
        </div>
      </div>

      <!-- Vue d'Ensemble - 3 lignes de 4 cards avec le style du dashboard client -->
      <section class="dashboard-section">
        <!-- Première ligne - Métriques principales -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-6 mb-6 sm:mb-8">
          <div 
            v-for="kpi in firstRowKpis" 
            :key="kpi.title"
            class="bg-white overflow-hidden shadow-lg rounded-lg border border-gray-100 hover:shadow-xl transition-all duration-300"
          >
            <div class="px-4 sm:px-6 py-4 sm:py-6">
              <div class="flex items-center mb-3 sm:mb-4">
                <div :class="['w-10 h-10 sm:w-12 sm:h-12 rounded-lg flex items-center justify-center mr-3 sm:mr-4 flex-shrink-0', getKpiIconClass(kpi.color)]">
                  <component :is="kpi.icon" class="w-5 h-5 sm:w-6 sm:h-6 text-white" />
                </div>
                <div class="min-w-0">
                  <p class="text-xs sm:text-sm text-gray-600">{{ kpi.title }}</p>
                  <p class="text-xl sm:text-2xl font-bold text-gray-900">{{ kpi.value }}</p>
                </div>
              </div>
              <div class="space-y-2">
                <div class="flex justify-between text-xs text-gray-500">
                  <span class="truncate">{{ kpi.description }}</span>
                  <span :class="['flex-shrink-0', kpi.trend.direction === 'up' ? 'text-green-600' : 'text-red-600']">
                    <component :is="kpi.trend.direction === 'up' ? 'TrendUpIcon' : 'TrendDownIcon'" class="w-3 h-3 inline mr-1" />
                    {{ kpi.trend.value }}%
                  </span>
                </div>
                <div class="w-full bg-gray-200 rounded-full h-2">
                  <div :class="['h-2 rounded-full transition-all duration-300', getKpiProgressClass(kpi.color)]" :style="`width: ${Math.min(100, kpi.trend.value)}%`"></div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Deuxième ligne -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-6 mb-6 sm:mb-8">
          <div 
            v-for="kpi in secondRowKpis" 
            :key="kpi.title"
            class="bg-white overflow-hidden shadow-lg rounded-lg border border-gray-100 hover:shadow-xl transition-all duration-300"
          >
            <div class="px-4 sm:px-6 py-4 sm:py-6">
              <div class="flex items-center mb-3 sm:mb-4">
                <div :class="['w-10 h-10 sm:w-12 sm:h-12 rounded-lg flex items-center justify-center mr-3 sm:mr-4 flex-shrink-0', getKpiIconClass(kpi.color)]">
                  <component :is="kpi.icon" class="w-5 h-5 sm:w-6 sm:h-6 text-white" />
                </div>
                <div class="min-w-0">
                  <p class="text-xs sm:text-sm text-gray-600">{{ kpi.title }}</p>
                  <p class="text-xl sm:text-2xl font-bold text-gray-900">{{ kpi.value }}</p>
                </div>
              </div>
              <div class="space-y-2">
                <div class="flex justify-between text-xs text-gray-500">
                  <span class="truncate">{{ kpi.description }}</span>
                  <span :class="['flex-shrink-0', kpi.trend.direction === 'up' ? 'text-green-600' : 'text-red-600']">
                    <component :is="kpi.trend.direction === 'up' ? 'TrendUpIcon' : 'TrendDownIcon'" class="w-3 h-3 inline mr-1" />
                    {{ kpi.trend.value }}%
                  </span>
                </div>
                <div class="w-full bg-gray-200 rounded-full h-2">
                  <div :class="['h-2 rounded-full transition-all duration-300', getKpiProgressClass(kpi.color)]" :style="`width: ${Math.min(100, kpi.trend.value)}%`"></div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Troisième ligne -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-6 mb-6 sm:mb-8">
          <div 
            v-for="kpi in thirdRowKpis" 
            :key="kpi.title"
            class="bg-white overflow-hidden shadow-lg rounded-lg border border-gray-100 hover:shadow-xl transition-all duration-300"
          >
            <div class="px-4 sm:px-6 py-4 sm:py-6">
              <div class="flex items-center mb-3 sm:mb-4">
                <div :class="['w-10 h-10 sm:w-12 sm:h-12 rounded-lg flex items-center justify-center mr-3 sm:mr-4 flex-shrink-0', getKpiIconClass(kpi.color)]">
                  <component :is="kpi.icon" class="w-5 h-5 sm:w-6 sm:h-6 text-white" />
                </div>
                <div class="min-w-0">
                  <p class="text-xs sm:text-sm text-gray-600">{{ kpi.title }}</p>
                  <p class="text-xl sm:text-2xl font-bold text-gray-900">{{ kpi.value }}</p>
                </div>
              </div>
              <div class="space-y-2">
                <div class="flex justify-between text-xs text-gray-500">
                  <span class="truncate">{{ kpi.description }}</span>
                  <span :class="['flex-shrink-0', kpi.trend.direction === 'up' ? 'text-green-600' : 'text-red-600']">
                    <component :is="kpi.trend.direction === 'up' ? 'TrendUpIcon' : 'TrendDownIcon'" class="w-3 h-3 inline mr-1" />
                    {{ kpi.trend.value }}%
                  </span>
                </div>
                <div class="w-full bg-gray-200 rounded-full h-2">
                  <div :class="['h-2 rounded-full transition-all duration-300', getKpiProgressClass(kpi.color)]" :style="`width: ${Math.min(100, kpi.trend.value)}%`"></div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </section>

      <!-- Analytics Professionnelles avec style tableau du dashboard client -->
      <section class="dashboard-section">
        <div class="bg-white shadow-lg rounded-lg overflow-hidden border border-gray-100 mb-6 sm:mb-8">
          <!-- Header avec style du dashboard client -->
          <div class="px-4 sm:px-6 py-3 sm:py-4 border-b border-gray-200 bg-gray-50">
            <div class="flex flex-wrap gap-1 sm:gap-2">
              <button class="px-2 sm:px-4 py-1.5 sm:py-2 rounded-lg  duration-200 text-xs sm:text-sm font-medium  shadow-lg bg-degrade-orange ">
                Revenus ({{ formatCurrency(2847650) }})
              </button>
              <button class="px-2 sm:px-4 py-1.5 sm:py-2 rounded-lg transition-all duration-200 text-xs sm:text-sm font-medium  hover:text-orange-500 hover:bg-orange-50 ">
                Commissions ({{ formatCurrency(427148) }})
              </button>
              <button class="px-2 sm:px-4 py-1.5 sm:py-2 rounded-lg transition-all duration-200 text-xs sm:text-sm font-medium  hover:text-orange-500 hover:bg-orange-50">
                Contacts (8,456)
              </button>
            </div>
          </div>

          <!-- Tableau avec style du dashboard client -->
          <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
              <thead class="bg-gray-50">
                <tr>
                  <th scope="col" class="px-3 sm:px-6 py-2 sm:py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Marché</th>
                  <th scope="col" class="hidden md:table-cell px-3 sm:px-6 py-2 sm:py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Boutiques</th>
                  <th scope="col" class="px-3 sm:px-6 py-2 sm:py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Revenus</th>
                  <th scope="col" class="px-3 sm:px-6 py-2 sm:py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Score</th>
                  <th scope="col" class="hidden lg:table-cell px-3 sm:px-6 py-2 sm:py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Performance</th>
                  <th scope="col" class="px-3 sm:px-6 py-2 sm:py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                </tr>
              </thead>
              <tbody class="bg-white divide-y divide-gray-200">
                <tr v-for="market in topMarkets" :key="market.id" class="hover:bg-gray-50 transition-colors">
                  <td class="px-3 sm:px-6 py-3 sm:py-4 whitespace-nowrap">
                    <div class="flex items-center space-x-2 sm:space-x-3">
                      <div class="w-10 h-10 sm:w-12 sm:h-12 bg-gradient-to-br from-orange-200 to-orange-300 rounded-lg flex items-center justify-center overflow-hidden flex-shrink-0">
                        <component :is="market.icon" class="w-5 h-5 sm:w-6 sm:h-6 text-orange-700" />
                      </div>
                      <div class="min-w-0 flex-1">
                        <div class="text-xs sm:text-sm font-medium text-gray-900 truncate">{{ market.name }}</div>
                        <div class="text-xs text-gray-500 truncate">{{ market.products }} produits</div>
                      </div>
                    </div>
                  </td>
                  <td class="hidden md:table-cell px-3 sm:px-6 py-3 sm:py-4 whitespace-nowrap">
                    <div class="text-xs sm:text-sm text-gray-900">{{ market.shops }}</div>
                    <div class="text-xs text-gray-500">boutiques</div>
                  </td>
                  <td class="px-3 sm:px-6 py-3 sm:py-4 whitespace-nowrap">
                    <div class="text-xs sm:text-sm font-medium text-gray-900">{{ formatCurrency(market.revenue) }}</div>
                    <div class="text-xs text-green-600">+{{ market.revenueGrowth }}%</div>
                  </td>
                  <td class="px-3 sm:px-6 py-3 sm:py-4 whitespace-nowrap">
                    <span :class="['px-2 inline-flex text-xs leading-5 font-semibold rounded-full', getScoreBadgeClass(market.score)]">
                      {{ market.score }}/10
                    </span>
                  </td>
                  <td class="hidden lg:table-cell px-3 sm:px-6 py-3 sm:py-4 whitespace-nowrap">
                    <div class="space-y-1">
                      <div class="text-xs text-gray-500">{{ market.performance }}%</div>
                      <div class="w-full bg-gray-200 rounded-full h-2">
                        <div class="bg-gradient-to-r from-orange-500 to-orange-600 h-2 rounded-full transition-all duration-300" :style="`width: ${market.performance}%`"></div>
                      </div>
                    </div>
                  </td>
                  <td class="px-3 sm:px-6 py-3 sm:py-4 whitespace-nowrap text-xs sm:text-sm font-medium">
                    <button class="text-orange-600 hover:text-orange-900 transition-colors text-xs sm:text-sm mr-3">
                      Détails
                    </button>
                    <button class="text-gray-400 hover:text-gray-500 transition-colors">
                      <MoreHorizontalIcon class="h-4 w-4 sm:h-5 sm:w-5" />
                    </button>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </section>

      <!-- Activités Récentes avec style du dashboard client -->
      <section class="dashboard-section">
        <div class="bg-white shadow-lg rounded-lg overflow-hidden border border-gray-100">
          <div class="px-4 sm:px-6 py-3 sm:py-4 border-b border-gray-200 bg-gray-50">
            <h3 class="text-lg font-semibold text-gray-900">Activités Récentes</h3>
            <p class="text-sm text-gray-600">Dernières interactions et transactions</p>
          </div>
          
          <div class="p-4 sm:p-6">
            <div class="space-y-4">
              <div v-for="activity in recentActivities" :key="activity.id" class="flex items-start space-x-3 p-3 rounded-lg hover:bg-gray-50 transition-colors">
                <div :class="['w-8 h-8 rounded-full flex items-center justify-center flex-shrink-0', getActivityIconClass(activity.type)]">
                  <component :is="activity.icon" class="w-4 h-4 text-white" />
                </div>
                <div class="flex-1 min-w-0">
                  <div class="flex items-center justify-between">
                    <p class="text-sm font-medium text-gray-900 truncate">{{ activity.title }}</p>
                    <span class="text-xs text-gray-500">{{ activity.time }}</span>
                  </div>
                  <p class="text-sm text-gray-600">{{ activity.description }}</p>
                  <div class="flex items-center space-x-2 mt-1">
                    <span class="text-xs text-gray-500">{{ activity.user }}</span>
                    <span v-if="activity.amount" class="text-xs font-medium text-green-600">{{ formatCurrency(activity.amount) }}</span>
                    <span :class="['text-xs px-2 py-1 rounded-full', getStatusClass(activity.status)]">{{ activity.statusLabel }}</span>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </section>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue'
import SidebarDashboard from '../menu/sidebar-dashboard.vue'
import NavbarDashboard from '../menu/navbar-dashboard.vue'

// KPIs Data - 3 lignes de 4 cards
const firstRowKpis = ref([
  {
    title: 'Revenus Totaux',
    value: '€2,847,650',
    description: 'Chiffre d\'affaires global',
    icon: 'EuroIcon',
    color: 'primary',
    trend: { value: 18.5, direction: 'up' }
  },
  {
    title: 'Commandes Directes',
    value: '3,247',
    description: 'Commandes via la plateforme',
    icon: 'ShoppingCartIcon',
    color: 'success',
    trend: { value: 12.3, direction: 'up' }
  },
  {
    title: 'Contacts Vendeurs',
    value: '8,456',
    description: 'Interactions directes',
    icon: 'MessageSquareIcon',
    color: 'info',
    trend: { value: 24.7, direction: 'up' }
  },
  {
    title: 'Taux Conversion',
    value: '4.8%',
    description: 'Contacts → Ventes',
    icon: 'TrendingUpIcon',
    color: 'warning',
    trend: { value: 3.2, direction: 'down' }
  }
])

const secondRowKpis = ref([
  {
    title: 'Marchés Actifs',
    value: '12',
    description: 'Secteurs d\'activité',
    icon: 'BuildingIcon',
    color: 'purple',
    trend: { value: 8.3, direction: 'up' }
  },
  {
    title: 'Boutiques',
    value: '1,567',
    description: 'Vendeurs enregistrés',
    icon: 'StoreIcon',
    color: 'indigo',
    trend: { value: 15.7, direction: 'up' }
  },
  {
    title: 'Produits',
    value: '45,678',
    description: 'Catalogue total',
    icon: 'PackageIcon',
    color: 'pink',
    trend: { value: 22.1, direction: 'up' }
  },
  {
    title: 'Utilisateurs',
    value: '89,234',
    description: 'Clients actifs',
    icon: 'UsersIcon',
    color: 'cyan',
    trend: { value: 11.9, direction: 'up' }
  }
])

const thirdRowKpis = ref([
  {
    title: 'Commissions',
    value: '€427,148',
    description: 'Revenus plateforme',
    icon: 'CreditCardIcon',
    color: 'emerald',
    trend: { value: 16.4, direction: 'up' }
  },
  {
    title: 'Réclamations',
    value: '23',
    description: 'En cours de traitement',
    icon: 'AlertTriangleIcon',
    color: 'red',
    trend: { value: 5.2, direction: 'down' }
  },
  {
    title: 'Satisfaction',
    value: '94.2%',
    description: 'Score global clients',
    icon: 'StarIcon',
    color: 'yellow',
    trend: { value: 2.8, direction: 'up' }
  },
  {
    title: 'Temps Réponse',
    value: '2.3h',
    description: 'Moyenne support',
    icon: 'ClockIcon',
    color: 'gray',
    trend: { value: 12.5, direction: 'down' }
  }
])

// Revenue Data
const revenueData = ref([
  { month: 'Jan', value: 450000, percentage: 60, growth: 12.5 },
  { month: 'Fév', value: 520000, percentage: 70, growth: 15.6 },
  { month: 'Mar', value: 480000, percentage: 65, growth: 6.7 },
  { month: 'Avr', value: 610000, percentage: 82, growth: 27.1 },
  { month: 'Mai', value: 550000, percentage: 74, growth: 14.6 },
  { month: 'Jun', value: 670000, percentage: 90, growth: 21.8 }
])

// Contact Channels
const contactChannels = ref([
  { name: 'WhatsApp Business', count: 3247, percentage: 45, type: 'whatsapp', icon: 'MessageCircleIcon', color: '#25d366' },
  { name: 'Email Direct', count: 2156, percentage: 30, type: 'email', icon: 'MailIcon', color: '#f59e0b' },
  { name: 'Téléphone', count: 1234, percentage: 17, type: 'phone', icon: 'PhoneIcon', color: '#3b82f6' },
  { name: 'Chat Live', count: 567, percentage: 8, type: 'chat', icon: 'MessageSquareIcon', color: '#8b5cf6' }
])

// Top Markets
const topMarkets = ref([
  { 
    id: 1, 
    name: 'Électronique & Tech', 
    shops: 45, 
    products: 12450, 
    revenue: 847650, 
    commission: 84765,
    directContacts: 2340,
    score: 9.2,
    performance: 92,
    revenueGrowth: 18.5,
    commissionGrowth: 15.2,
    contactsGrowth: 24.7,
    icon: 'SmartphoneIcon'
  },
  { 
    id: 2, 
    name: 'Mode & Textile', 
    shops: 78, 
    products: 8900, 
    revenue: 654320, 
    commission: 65432,
    directContacts: 1890,
    score: 8.7,
    performance: 87,
    revenueGrowth: 12.3,
    commissionGrowth: 11.8,
    contactsGrowth: 19.4,
    icon: 'ShirtIcon'
  },
  { 
    id: 3, 
    name: 'Maison & Jardin', 
    shops: 34, 
    products: 5670, 
    revenue: 432100, 
    commission: 43210,
    directContacts: 1456,
    score: 8.9,
    performance: 89,
    revenueGrowth: 16.7,
    commissionGrowth: 14.3,
    contactsGrowth: 21.2,
    icon: 'HomeIcon'
  }
])

// Recent Activities
const recentActivities = ref([
  {
    id: 1,
    type: 'contact',
    icon: 'MessageCircleIcon',
    title: 'Nouveau contact direct',
    description: 'Client intéressé par iPhone 15 Pro - TechWorld Pro',
    user: 'Marie Dubois',
    time: '2 min',
    status: 'active',
    statusLabel: 'En cours'
  },
  {
    id: 2,
    type: 'order',
    icon: 'ShoppingCartIcon',
    title: 'Commande confirmée',
    description: 'Achat direct via la plateforme - Fashion Elite',
    user: 'Jean Martin',
    time: '5 min',
    amount: 1299,
    status: 'success',
    statusLabel: 'Confirmée'
  },
  {
    id: 3,
    type: 'shop',
    icon: 'StoreIcon',
    title: 'Nouvelle boutique',
    description: 'Inscription dans le marché Automobile',
    user: 'Auto Parts Premium',
    time: '15 min',
    status: 'pending',
    statusLabel: 'En validation'
  }
])

// Methods
const formatCurrency = (amount) => {
  return new Intl.NumberFormat('fr-FR', {
    style: 'currency',
    currency: 'EUR',
    minimumFractionDigits: 0
  }).format(amount)
}

const getScoreClass = (score) => {
  if (score >= 9) return 'excellent'
  if (score >= 8) return 'good'
  if (score >= 7) return 'average'
  return 'poor'
}

const getKpiIconClass = (color) => {
  const classes = {
    primary: 'bg-gradient-to-br from-blue-200 to-blue-300',
    success: 'bg-gradient-to-br from-green-200 to-green-300',
    info: 'bg-gradient-to-br from-purple-200 to-purple-300',
    warning: 'bg-gradient-to-br from-orange-200 to-orange-300',
    purple: 'bg-gradient-to-br from-purple-200 to-purple-300',
    indigo: 'bg-gradient-to-br from-indigo-200 to-indigo-300',
    pink: 'bg-gradient-to-br from-pink-200 to-pink-300',
    cyan: 'bg-gradient-to-br from-cyan-200 to-cyan-300',
    emerald: 'bg-gradient-to-br from-emerald-200 to-emerald-300',
    red: 'bg-gradient-to-br from-red-200 to-red-300',
    yellow: 'bg-gradient-to-br from-yellow-200 to-yellow-300',
    gray: 'bg-gradient-to-br from-gray-200 to-gray-300'
  }
  return classes[color] || classes.primary
}

const getKpiProgressClass = (color) => {
  const classes = {
    primary: 'bg-gradient-to-r from-blue-500 to-blue-600',
    success: 'bg-gradient-to-r from-green-500 to-green-600',
    info: 'bg-gradient-to-r from-purple-500 to-purple-600',
    warning: 'bg-gradient-to-r from-orange-500 to-orange-600',
    purple: 'bg-gradient-to-r from-purple-500 to-purple-600',
    indigo: 'bg-gradient-to-r from-indigo-500 to-indigo-600',
    pink: 'bg-gradient-to-r from-pink-500 to-pink-600',
    cyan: 'bg-gradient-to-r from-cyan-500 to-cyan-600',
    emerald: 'bg-gradient-to-r from-emerald-500 to-emerald-600',
    red: 'bg-gradient-to-r from-red-500 to-red-600',
    yellow: 'bg-gradient-to-r from-yellow-500 to-yellow-600',
    gray: 'bg-gradient-to-r from-gray-500 to-gray-600'
  }
  return classes[color] || classes.primary
}

const getScoreBadgeClass = (score) => {
  if (score >= 9) return 'bg-green-100 text-green-800'
  if (score >= 8) return 'bg-blue-100 text-blue-800'
  if (score >= 7) return 'bg-yellow-100 text-yellow-800'
  return 'bg-red-100 text-red-800'
}

const getActivityIconClass = (type) => {
  const classes = {
    contact: 'bg-gradient-to-br from-blue-500 to-blue-600',
    order: 'bg-gradient-to-br from-green-500 to-green-600',
    shop: 'bg-gradient-to-br from-orange-500 to-orange-600'
  }
  return classes[type] || classes.contact
}

const getStatusClass = (status) => {
  const classes = {
    active: 'bg-blue-100 text-blue-800',
    success: 'bg-green-100 text-green-800',
    pending: 'bg-yellow-100 text-yellow-800'
  }
  return classes[status] || classes.active
}
</script>

<script>
// Icon Components (ajout des nouveaux icons)
const EuroIcon = {
  template: `<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 10h12"></path><path d="M4 14h9"></path><path d="m19 6-7 6 7 6"></path></svg>`
}

const ShoppingCartIcon = {
  template: `<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="8" cy="21" r="1"></circle><circle cx="19" cy="21" r="1"></circle><path d="M2.05 2.05h2l2.66 12.42a2 2 0 0 0 2 1.58h9.78a2 2 0 0 0 1.95-1.57l1.65-7.43H5.12"></path></svg>`
}

const MessageSquareIcon = {
  template: `<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"></path></svg>`
}

const TrendingUpIcon = {
  template: `<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="22 7 13.5 15.5 8.5 10.5 2 17"></polyline><polyline points="16 7 22 7 22 13"></polyline></svg>`
}

const BuildingIcon = {
  template: `<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect width="16" height="20" x="4" y="2" rx="2" ry="2"></rect><path d="M9 22v-4h6v4"></path><path d="M8 6h.01"></path><path d="M16 6h.01"></path><path d="M12 6h.01"></path><path d="M12 10h.01"></path><path d="M12 14h.01"></path><path d="M16 10h.01"></path><path d="M16 14h.01"></path><path d="M8 10h.01"></path><path d="M8 14h.01"></path></svg>`
}

const StoreIcon = {
  template: `<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="m2 7 4.41-4.41A2 2 0 0 1 7.83 2h8.34a2 2 0 0 1 1.42.59L22 7"></path><path d="M4 12v8a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2v-8"></path><path d="M15 22v-4a2 2 0 0 0-2-2h-2a2 2 0 0 0-2 2v4"></path><path d="M2 7h20"></path></svg>`
}

const PackageIcon = {
  template: `<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M16.5 9.4 7.55 4.24"></path><path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"></path><polyline points="3.29 7 12 12 20.71 7"></polyline><line x1="12" x2="12" y1="22" y2="12"></line></svg>`
}

const UsersIcon = {
  template: `<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle><path d="M22 21v-2a4 4 0 0 0-3-3.87"></path><path d="M16 3.13a4 4 0 0 1 0 7.75"></path></svg>`
}

const CreditCardIcon = {
  template: `<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect width="20" height="14" x="2" y="5" rx="2"></rect><line x1="2" x2="22" y1="10" y2="10"></line></svg>`
}

const AlertTriangleIcon = {
  template: `<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="m21.73 18-8-14a2 2 0 0 0-3.48 0l-8 14A2 2 0 0 0 4 21h16a2 2 0 0 0 1.73-3"></path><path d="M12 9v4"></path><path d="M12 17h.01"></path></svg>`
}

const StarIcon = {
  template: `<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="currentColor" stroke="currentColor" stroke-width="2"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"></polygon></svg>`
}

const ClockIcon = {
  template: `<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline></svg>`
}

const TrendUpIcon = {
  template: `<svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="22 7 13.5 15.5 8.5 10.5 2 17"></polyline><polyline points="16 7 22 7 22 13"></polyline></svg>`
}

const TrendDownIcon = {
  template: `<svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="22 17 13.5 8.5 8.5 13.5 2 7"></polyline><polyline points="16 17 22 17 22 11"></polyline></svg>`
}


const MessageCircleIcon = {
  template: `<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M7.9 20A9 9 0 1 0 4 16.1L2 22Z"></path></svg>`
}

const MailIcon = {
  template: `<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"></path><polyline points="22 6 12 13 2 6"></polyline></svg>`
}

const PhoneIcon = {
  template: `<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"></path></svg>`
}

const SmartphoneIcon = {
  template: `<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect width="14" height="20" x="5" y="2" rx="2" ry="2"></rect><path d="M12 18h.01"></path></svg>`
}

const ShirtIcon = {
  template: `<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20.38 3.46 16 2a4 4 0 0 1-8 0L3.62 3.46a2 2 0 0 0-1.34 2.23l.58 3.47a1 1 0 0 0 .99.84H6v10c0 1.1.9 2 2 2h8c1.1 0 2-.9 2-2V10h2.15a1 1 0 0 0 .99-.84l.58-3.47a2 2 0 0 0-1.34-2.23Z"></path></svg>`
}

const HomeIcon = {
  template: `<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path><polyline points="9 22 9 12 15 12 15 22"></polyline></svg>`
}


const MoreHorizontalIcon = {
  template: `<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-more-horizontal"><circle cx="12" cy="12" r="1"></circle><circle cx="19" cy="12" r="1"></circle><circle cx="5" cy="12" r="1"></circle></svg>`
}

export default {
  components: {
    EuroIcon,
    ShoppingCartIcon,
    MessageSquareIcon,
    TrendingUpIcon,
    BuildingIcon,
    StoreIcon,
    PackageIcon,
    UsersIcon,
    CreditCardIcon,
    AlertTriangleIcon,
    StarIcon,
    ClockIcon,
    TrendUpIcon,
    TrendDownIcon,
    MessageCircleIcon,
    MailIcon,
    PhoneIcon,
    SmartphoneIcon,
    ShirtIcon,
    HomeIcon,
    MoreHorizontalIcon
  }
}
</script>

<style scoped>
/* Supprimer les styles dashboard-container et main-content avec sidebar */
.dashboard-container {
  min-height: 100vh;
  background: transparent;
  font-family: ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, "Noto Sans", sans-serif;
}

/* Supprimer margin-left: 280px et ajuster pour centrage */
.dashboard-main {
  padding: 32px;
}

/* Ajouter les styles du dashboard client pour les couleurs orange */
.bg-degrade-orange {
  background: linear-gradient(90deg, #fe7900, #ff5a01);
  color: white;
}

.bg-degrade-ring {
  background: linear-gradient(90deg, #7d3aec, #620cec);
  color: white;
}
.bg-orange-500 {
  background-color: #F65A11;
}

.hover\:bg-orange-600:hover {
  background-color: #e54a0a;
}

.focus\:ring-orange-500:focus {
  --tw-ring-color: rgba(246, 90, 17, 0.5);
}

.focus\:border-orange-500:focus {
  border-color: #F65A11;
}

.text-orange-500 {
  color: #F65A11;
}

.border-orange-500 {
  border-color: #F65A11;
}

.hover\:bg-orange-50:hover {
  background-color: rgba(246, 90, 17, 0.05);
}

.bg-orange-100 {
  background-color: rgba(246, 90, 17, 0.1);
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
</style>