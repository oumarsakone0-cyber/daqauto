<template>
    <div 
      class="fixed inset-0 bg-black bg-opacity-30 flex items-center justify-center z-50"
      @click="handleBackdropClick"
    >
      <div 
        class="bg-white rounded-lg p-8 max-w-6xl w-full mx-4 max-h-[90vh] overflow-y-auto"
        @click.stop
      >
        <div class="flex justify-between items-center mb-6">
          <h2 class="text-2xl font-bold text-gray-900">Analytics & Rapports</h2>
          <button @click="$emit('close')" class="text-gray-400 hover:text-gray-600">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
            </svg>
          </button>
        </div>
  
        <!-- Période de sélection -->
        <div class="mb-6">
          <div class="flex items-center space-x-4">
            <label class="text-sm font-medium text-gray-700">Période d'analyse :</label>
            <select 
              v-model="selectedPeriod" 
              @change="updateAnalytics"
              class="border border-gray-300 rounded-lg px-3 py-2 focus:ring-orange focus:border-orange"
            >
              <option value="7days">7 derniers jours</option>
              <option value="30days">30 derniers jours</option>
              <option value="3months">3 derniers mois</option>
              <option value="year">Cette année</option>
            </select>
          </div>
        </div>
  
        <!-- Métriques principales -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
          <div class="bg-gradient-to-r from-blue-500 to-blue-600 rounded-lg p-6 text-white">
            <div class="flex items-center justify-between">
              <div>
                <p class="text-blue-100 text-sm">Ventes Totales</p>
                <p class="text-2xl font-bold">{{ analytics.totalSales.toLocaleString() }} FCFA</p>
                <p class="text-blue-100 text-xs mt-1">
                  <span :class="analytics.salesGrowth >= 0 ? 'text-green-200' : 'text-red-200'">
                    {{ analytics.salesGrowth >= 0 ? '+' : '' }}{{ analytics.salesGrowth }}%
                  </span> vs période précédente
                </p>
              </div>
              <div class="w-12 h-12 bg-blue-400 rounded-full flex items-center justify-center">
                <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20">
                  <path d="M4 4a2 2 0 00-2 2v1h16V6a2 2 0 00-2-2H4zM18 9H2v5a2 2 0 002 2h12a2 2 0 002-2V9zM4 13a1 1 0 011-1h1a1 1 0 110 2H5a1 1 0 01-1-1zm5-1a1 1 0 100 2h1a1 1 0 100-2H9z"/>
                </svg>
              </div>
            </div>
          </div>
  
          <div class="bg-gradient-to-r from-green-500 to-green-600 rounded-lg p-6 text-white">
            <div class="flex items-center justify-between">
              <div>
                <p class="text-green-100 text-sm">Commandes</p>
                <p class="text-2xl font-bold">{{ analytics.totalOrders }}</p>
                <p class="text-green-100 text-xs mt-1">
                  <span :class="analytics.ordersGrowth >= 0 ? 'text-green-200' : 'text-red-200'">
                    {{ analytics.ordersGrowth >= 0 ? '+' : '' }}{{ analytics.ordersGrowth }}%
                  </span> vs période précédente
                </p>
              </div>
              <div class="w-12 h-12 bg-green-400 rounded-full flex items-center justify-center">
                <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20">
                  <path fill-rule="evenodd" d="M10 2L3 7v11a2 2 0 002 2h10a2 2 0 002-2V7l-7-5zM8 15a1 1 0 011-1h2a1 1 0 110 2H9a1 1 0 01-1-1z" clip-rule="evenodd"/>
                </svg>
              </div>
            </div>
          </div>
  
          <div class="bg-gradient-to-r from-purple-500 to-purple-600 rounded-lg p-6 text-white">
            <div class="flex items-center justify-between">
              <div>
                <p class="text-purple-100 text-sm">Produits Vus</p>
                <p class="text-2xl font-bold">{{ analytics.totalViews.toLocaleString() }}</p>
                <p class="text-purple-100 text-xs mt-1">
                  <span :class="analytics.viewsGrowth >= 0 ? 'text-green-200' : 'text-red-200'">
                    {{ analytics.viewsGrowth >= 0 ? '+' : '' }}{{ analytics.viewsGrowth }}%
                  </span> vs période précédente
                </p>
              </div>
              <div class="w-12 h-12 bg-purple-400 rounded-full flex items-center justify-center">
                <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20">
                  <path d="M10 12a2 2 0 100-4 2 2 0 000 4z"/>
                  <path fill-rule="evenodd" d="M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7S1.732 14.057.458 10zM14 10a4 4 0 11-8 0 4 4 0 018 0z" clip-rule="evenodd"/>
                </svg>
              </div>
            </div>
          </div>
  
          <div class="bg-gradient-to-r from-orange-500 to-orange-600 rounded-lg p-6 text-white">
            <div class="flex items-center justify-between">
              <div>
                <p class="text-orange-100 text-sm">Taux de Conversion</p>
                <p class="text-2xl font-bold">{{ analytics.conversionRate }}%</p>
                <p class="text-orange-100 text-xs mt-1">
                  <span :class="analytics.conversionGrowth >= 0 ? 'text-green-200' : 'text-red-200'">
                    {{ analytics.conversionGrowth >= 0 ? '+' : '' }}{{ analytics.conversionGrowth }}%
                  </span> vs période précédente
                </p>
              </div>
              <div class="w-12 h-12 bg-orange-400 rounded-full flex items-center justify-center">
                <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20">
                  <path fill-rule="evenodd" d="M3 3a1 1 0 000 2v8a2 2 0 002 2h2.586l-1.293 1.293a1 1 0 101.414 1.414L10 15.414l2.293 2.293a1 1 0 001.414-1.414L12.414 15H15a2 2 0 002-2V5a1 1 0 100-2H3zm11.707 4.707a1 1 0 00-1.414-1.414L10 9.586 8.707 8.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                </svg>
              </div>
            </div>
          </div>
        </div>
  
        <!-- Graphiques -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
          <!-- Graphique des ventes -->
          <div class="bg-white border border-gray-200 rounded-lg p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Évolution des Ventes</h3>
            <div class="h-64">
              <canvas ref="salesChart" class="w-full h-full"></canvas>
            </div>
          </div>
  
          <!-- Graphique des catégories -->
          <div class="bg-white border border-gray-200 rounded-lg p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Ventes par Catégorie</h3>
            <div class="space-y-4">
              <div v-for="category in analytics.categorySales" :key="category.name" class="flex items-center justify-between">
                <div class="flex items-center space-x-3">
                  <div class="w-4 h-4 rounded" :style="{ backgroundColor: category.color }"></div>
                  <span class="text-sm font-medium text-gray-900">{{ category.name }}</span>
                </div>
                <div class="text-right">
                  <div class="text-sm font-semibold text-gray-900">{{ category.sales.toLocaleString() }} FCFA</div>
                  <div class="text-xs text-gray-500">{{ category.percentage }}%</div>
                </div>
              </div>
            </div>
          </div>
        </div>
  
        <!-- Top produits -->
        <div class="bg-white border border-gray-200 rounded-lg p-6 mb-8">
          <h3 class="text-lg font-semibold text-gray-900 mb-4">Top 10 Produits</h3>
          <div class="overflow-x-auto">
            <table class="w-full text-sm">
              <thead class="text-xs text-gray-700 uppercase bg-gray-50">
                <tr>
                  <th class="px-4 py-3 text-left">Rang</th>
                  <th class="px-4 py-3 text-left">Produit</th>
                  <th class="px-4 py-3 text-left">Ventes</th>
                  <th class="px-4 py-3 text-left">Revenus</th>
                  <th class="px-4 py-3 text-left">Vues</th>
                  <th class="px-4 py-3 text-left">Conversion</th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="(product, index) in analytics.topProducts" :key="product.id" class="border-b hover:bg-gray-50">
                  <td class="px-4 py-3">
                    <div class="flex items-center">
                      <span class="w-6 h-6 bg-orange text-white rounded-full flex items-center justify-center text-xs font-bold">
                        {{ index + 1 }}
                      </span>
                    </div>
                  </td>
                  <td class="px-4 py-3">
                    <div class="flex items-center space-x-3">
                      <div class="w-10 h-10 bg-gray-100 rounded-md flex items-center justify-center">
                        <span class="text-lg">{{ product.icon }}</span>
                      </div>
                      <div>
                        <div class="font-medium text-gray-900">{{ product.name }}</div>
                        <div class="text-gray-500 text-xs">{{ product.sku }}</div>
                      </div>
                    </div>
                  </td>
                  <td class="px-4 py-3 font-medium text-gray-900">{{ product.sales }} unités</td>
                  <td class="px-4 py-3 font-medium text-gray-900">{{ product.revenue.toLocaleString() }} FCFA</td>
                  <td class="px-4 py-3 text-gray-600">{{ product.views.toLocaleString() }}</td>
                  <td class="px-4 py-3">
                    <span :class="getConversionClass(product.conversion)" class="px-2 py-1 rounded-full text-xs font-medium">
                      {{ product.conversion }}%
                    </span>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
  
        <!-- Actions d'export -->
        <div class="flex justify-end space-x-3">
          <button 
            @click="exportReport('pdf')"
            class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors flex items-center"
          >
            <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
              <path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4z" clip-rule="evenodd"/>
            </svg>
            Exporter PDF
          </button>
          <button 
            @click="exportReport('excel')"
            class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors flex items-center"
          >
            <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
              <path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4z" clip-rule="evenodd"/>
            </svg>
            Exporter Excel
          </button>
        </div>
      </div>
    </div>
  </template>
  
  <script setup>
  import { ref, reactive, onMounted, nextTick } from 'vue'
  
  const emit = defineEmits(['close'])
  
  const selectedPeriod = ref('30days')
  
  const analytics = reactive({
    totalSales: 2450000,
    salesGrowth: 12.5,
    totalOrders: 156,
    ordersGrowth: 8.3,
    totalViews: 4250,
    viewsGrowth: 15.2,
    conversionRate: 3.7,
    conversionGrowth: 2.1,
    categorySales: [
      { name: 'Électronique', sales: 1200000, percentage: 49, color: '#3B82F6' },
      { name: 'Vêtements', sales: 650000, percentage: 27, color: '#10B981' },
      { name: 'Chaussures', sales: 400000, percentage: 16, color: '#F59E0B' },
      { name: 'Accessoires', sales: 200000, percentage: 8, color: '#EF4444' }
    ],
    topProducts: [
      {
        id: 1,
        name: 'MacBook Air M1',
        sku: 'MBA-M1-2022',
        icon: '💻',
        sales: 45,
        revenue: 850000,
        views: 1250,
        conversion: 3.6
      },
      {
        id: 2,
        name: 'iPhone 14 Pro',
        sku: 'IP14-PRO',
        icon: '📱',
        sales: 32,
        revenue: 720000,
        views: 980,
        conversion: 3.3
      },
      {
        id: 3,
        name: 'Nike Air Max 270',
        sku: 'NAM-270',
        icon: '👟',
        sales: 28,
        revenue: 420000,
        views: 750,
        conversion: 3.7
      },
      {
        id: 4,
        name: 'T-shirt Premium',
        sku: 'TSH-PREM',
        icon: '👕',
        sales: 85,
        revenue: 380000,
        views: 1100,
        conversion: 7.7
      },
      {
        id: 5,
        name: 'Sac à dos Cuir',
        sku: 'SAC-CUIR',
        icon: '🎒',
        sales: 15,
        revenue: 225000,
        views: 450,
        conversion: 3.3
      }
    ]
  })
  
  const salesChart = ref(null)
  
  const updateAnalytics = () => {
    console.log('Mise à jour des analytics pour:', selectedPeriod.value)
    // Ici vous pourriez faire un appel API pour récupérer les nouvelles données
  }
  
  const getConversionClass = (rate) => {
    if (rate >= 5) return 'bg-green-100 text-green-800'
    if (rate >= 3) return 'bg-yellow-100 text-yellow-800'
    return 'bg-red-100 text-red-800'
  }
  
  const exportReport = (format) => {
    console.log(`Export du rapport en ${format}`)
    // Logique d'export
  }
  
  const handleBackdropClick = (event) => {
    if (event.target === event.currentTarget) {
      emit('close')
    }
  }
  
  onMounted(() => {
    nextTick(() => {
      // Ici vous pourriez initialiser Chart.js ou une autre librairie de graphiques
      if (salesChart.value) {
        const ctx = salesChart.value.getContext('2d')
        // Exemple de graphique simple sans librairie externe
        drawSimpleChart(ctx)
      }
    })
  })
  
  const drawSimpleChart = (ctx) => {
    const data = [120, 150, 180, 200, 170, 220, 250]
    const labels = ['Lun', 'Mar', 'Mer', 'Jeu', 'Ven', 'Sam', 'Dim']
    
    const canvas = ctx.canvas
    const width = canvas.width
    const height = canvas.height
    
    ctx.clearRect(0, 0, width, height)
    
    // Dessiner les barres
    const barWidth = width / data.length * 0.8
    const maxValue = Math.max(...data)
    
    data.forEach((value, index) => {
      const barHeight = (value / maxValue) * (height - 40)
      const x = (index * width / data.length) + (width / data.length - barWidth) / 2
      const y = height - barHeight - 20
      
      ctx.fillStyle = '#F65A11'
      ctx.fillRect(x, y, barWidth, barHeight)
      
      // Labels
      ctx.fillStyle = '#374151'
      ctx.font = '12px sans-serif'
      ctx.textAlign = 'center'
      ctx.fillText(labels[index], x + barWidth / 2, height - 5)
      ctx.fillText(value + 'k', x + barWidth / 2, y - 5)
    })
  }
  </script>
  
  <style scoped>
  .bg-orange {
    background-color: #F65A11;
  }
  
  .text-orange {
    color: #F65A11;
  }
  
  .focus\:ring-orange:focus {
    --tw-ring-color: rgba(246, 90, 17, 0.5);
  }
  
  .focus\:border-orange:focus {
    border-color: #F65A11;
  }
  </style>
  