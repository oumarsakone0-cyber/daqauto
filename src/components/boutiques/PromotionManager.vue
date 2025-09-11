<template>
    <div 
      class="fixed inset-0 bg-black bg-opacity-30 flex items-center justify-center z-50"
      @click="handleBackdropClick"
    >
      <div 
        class="bg-white rounded-lg p-8 max-w-4xl w-full mx-4 max-h-[90vh] overflow-y-auto"
        @click.stop
      >
        <div class="flex justify-between items-center mb-6">
          <h2 class="text-2xl font-bold text-gray-900">Gestion des Promotions</h2>
          <div class="flex items-center space-x-3">
            <button 
              @click="showCreatePromotion = true"
              class="px-4 py-2 bg-orange text-white rounded-lg hover:bg-orange-600 transition-colors flex items-center"
            >
              <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
              </svg>
              Nouvelle Promotion
            </button>
            <button @click="$emit('close')" class="text-gray-400 hover:text-gray-600">
              <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
              </svg>
            </button>
          </div>
        </div>
  
        <!-- Statistiques des promotions -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
          <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
            <div class="flex items-center">
              <div class="w-8 h-8 bg-blue-500 rounded-full flex items-center justify-center mr-3">
                <svg class="w-4 h-4 text-white" fill="currentColor" viewBox="0 0 20 20">
                  <path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z" clip-rule="evenodd"/>
                </svg>
              </div>
              <div>
                <p class="text-sm text-gray-600">Promotions Actives</p>
                <p class="text-xl font-bold text-gray-900">{{ activePromotions.length }}</p>
              </div>
            </div>
          </div>
  
          <div class="bg-green-50 border border-green-200 rounded-lg p-4">
            <div class="flex items-center">
              <div class="w-8 h-8 bg-green-500 rounded-full flex items-center justify-center mr-3">
                <svg class="w-4 h-4 text-white" fill="currentColor" viewBox="0 0 20 20">
                  <path fill-rule="evenodd" d="M4 4a2 2 0 00-2 2v4a2 2 0 002 2V6h10a2 2 0 00-2-2H4zm2 6a2 2 0 012-2h8a2 2 0 012 2v4a2 2 0 01-2 2H8a2 2 0 01-2-2v-4zm6 4a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd"/>
                </svg>
              </div>
              <div>
                <p class="text-sm text-gray-600">Économies Générées</p>
                <p class="text-xl font-bold text-gray-900">{{ totalSavings.toLocaleString() }} FCFA</p>
              </div>
            </div>
          </div>
  
          <div class="bg-purple-50 border border-purple-200 rounded-lg p-4">
            <div class="flex items-center">
              <div class="w-8 h-8 bg-purple-500 rounded-full flex items-center justify-center mr-3">
                <svg class="w-4 h-4 text-white" fill="currentColor" viewBox="0 0 20 20">
                  <path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
              </div>
              <div>
                <p class="text-sm text-gray-600">Codes Utilisés</p>
                <p class="text-xl font-bold text-gray-900">{{ usedCodes }}</p>
              </div>
            </div>
          </div>
  
          <div class="bg-orange-50 border border-orange-200 rounded-lg p-4">
            <div class="flex items-center">
              <div class="w-8 h-8 bg-orange-500 rounded-full flex items-center justify-center mr-3">
                <svg class="w-4 h-4 text-white" fill="currentColor" viewBox="0 0 20 20">
                  <path fill-rule="evenodd" d="M3 3a1 1 0 000 2v8a2 2 0 002 2h2.586l-1.293 1.293a1 1 0 101.414 1.414L10 15.414l2.293 2.293a1 1 0 001.414-1.414L12.414 15H15a2 2 0 002-2V5a1 1 0 100-2H3zm11.707 4.707a1 1 0 00-1.414-1.414L10 9.586 8.707 8.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                </svg>
              </div>
              <div>
                <p class="text-sm text-gray-600">Taux de Conversion</p>
                <p class="text-xl font-bold text-gray-900">{{ conversionRate }}%</p>
              </div>
            </div>
          </div>
        </div>
  
        <!-- Liste des promotions -->
        <div class="bg-white border border-gray-200 rounded-lg overflow-hidden">
          <div class="px-6 py-4 border-b border-gray-200">
            <div class="flex items-center justify-between">
              <h3 class="text-lg font-medium text-gray-900">Promotions Actives</h3>
              <div class="flex items-center space-x-2">
                <select 
                  v-model="filterStatus"
                  class="border border-gray-300 rounded-lg px-3 py-1 text-sm focus:ring-orange focus:border-orange"
                >
                  <option value="all">Toutes</option>
                  <option value="active">Actives</option>
                  <option value="scheduled">Programmées</option>
                  <option value="expired">Expirées</option>
                </select>
              </div>
            </div>
          </div>
  
          <div class="overflow-x-auto">
            <table class="w-full text-sm">
              <thead class="text-xs text-gray-700 uppercase bg-gray-50">
                <tr>
                  <th class="px-6 py-3 text-left">Promotion</th>
                  <th class="px-6 py-3 text-left">Type</th>
                  <th class="px-6 py-3 text-left">Réduction</th>
                  <th class="px-6 py-3 text-left">Période</th>
                  <th class="px-6 py-3 text-left">Utilisations</th>
                  <th class="px-6 py-3 text-left">Statut</th>
                  <th class="px-6 py-3 text-left">Actions</th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="promotion in filteredPromotions" :key="promotion.id" class="border-b hover:bg-gray-50">
                  <td class="px-6 py-4">
                    <div>
                      <div class="font-medium text-gray-900">{{ promotion.name }}</div>
                      <div class="text-gray-500 text-xs">{{ promotion.code }}</div>
                    </div>
                  </td>
                  <td class="px-6 py-4">
                    <span :class="getTypeClass(promotion.type)" class="px-2 py-1 rounded-full text-xs font-medium">
                      {{ getTypeLabel(promotion.type) }}
                    </span>
                  </td>
                  <td class="px-6 py-4 font-medium text-gray-900">
                    {{ promotion.type === 'percentage' ? promotion.value + '%' : promotion.value.toLocaleString() + ' FCFA' }}
                  </td>
                  <td class="px-6 py-4 text-gray-600">
                    <div class="text-xs">
                      <div>Du {{ formatDate(promotion.startDate) }}</div>
                      <div>Au {{ formatDate(promotion.endDate) }}</div>
                    </div>
                  </td>
                  <td class="px-6 py-4">
                    <div class="text-center">
                      <div class="font-medium text-gray-900">{{ promotion.used }}</div>
                      <div class="text-xs text-gray-500">/ {{ promotion.maxUses || '∞' }}</div>
                    </div>
                  </td>
                  <td class="px-6 py-4">
                    <span :class="getStatusClass(promotion.status)" class="px-2 py-1 rounded-full text-xs font-medium">
                      {{ getStatusLabel(promotion.status) }}
                    </span>
                  </td>
                  <td class="px-6 py-4">
                    <div class="flex items-center space-x-2">
                      <button 
                        @click="editPromotion(promotion)"
                        class="text-blue-600 hover:text-blue-800 text-xs"
                      >
                        Modifier
                      </button>
                      <button 
                        @click="togglePromotion(promotion)"
                        class="text-orange-600 hover:text-orange-800 text-xs"
                      >
                        {{ promotion.status === 'active' ? 'Désactiver' : 'Activer' }}
                      </button>
                      <button 
                        @click="deletePromotion(promotion.id)"
                        class="text-red-600 hover:text-red-800 text-xs"
                      >
                        Supprimer
                      </button>
                    </div>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
  
        <!-- Modal de création/édition de promotion -->
        <div v-if="showCreatePromotion" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-60">
          <div class="bg-white rounded-lg p-6 max-w-2xl w-full mx-4" @click.stop>
            <div class="flex justify-between items-center mb-4">
              <h3 class="text-lg font-semibold text-gray-900">
                {{ editingPromotion ? 'Modifier la promotion' : 'Nouvelle promotion' }}
              </h3>
              <button @click="closePromotionModal" class="text-gray-400 hover:text-gray-600">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
              </button>
            </div>
  
            <form @submit.prevent="savePromotion" class="space-y-4">
              <div class="grid grid-cols-2 gap-4">
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-1">Nom de la promotion</label>
                  <input 
                    v-model="promotionForm.name"
                    type="text" 
                    required
                    class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-orange focus:border-orange"
                    placeholder="Ex: Soldes d'été"
                  >
                </div>
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-1">Code promo</label>
                  <input 
                    v-model="promotionForm.code"
                    type="text" 
                    required
                    class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-orange focus:border-orange"
                    placeholder="Ex: SUMMER2024"
                  >
                </div>
              </div>
  
              <div class="grid grid-cols-2 gap-4">
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-1">Type de réduction</label>
                  <select 
                    v-model="promotionForm.type"
                    required
                    class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-orange focus:border-orange"
                  >
                    <option value="percentage">Pourcentage</option>
                    <option value="fixed">Montant fixe</option>
                    <option value="free_shipping">Livraison gratuite</option>
                  </select>
                </div>
                <div v-if="promotionForm.type !== 'free_shipping'">
                  <label class="block text-sm font-medium text-gray-700 mb-1">
                    {{ promotionForm.type === 'percentage' ? 'Pourcentage (%)' : 'Montant (FCFA)' }}
                  </label>
                  <input 
                    v-model="promotionForm.value"
                    type="number" 
                    required
                    class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-orange focus:border-orange"
                    :placeholder="promotionForm.type === 'percentage' ? '10' : '5000'"
                  >
                </div>
              </div>
  
              <div class="grid grid-cols-2 gap-4">
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-1">Date de début</label>
                  <input 
                    v-model="promotionForm.startDate"
                    type="datetime-local" 
                    required
                    class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-orange focus:border-orange"
                  >
                </div>
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-1">Date de fin</label>
                  <input 
                    v-model="promotionForm.endDate"
                    type="datetime-local" 
                    required
                    class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-orange focus:border-orange"
                  >
                </div>
              </div>
  
              <div class="grid grid-cols-2 gap-4">
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-1">Nombre d'utilisations max</label>
                  <input 
                    v-model="promotionForm.maxUses"
                    type="number" 
                    class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-orange focus:border-orange"
                    placeholder="Laisser vide pour illimité"
                  >
                </div>
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-1">Montant minimum</label>
                  <input 
                    v-model="promotionForm.minAmount"
                    type="number" 
                    class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-orange focus:border-orange"
                    placeholder="Montant minimum de commande"
                  >
                </div>
              </div>
  
              <div class="flex items-center">
                <input 
                  v-model="promotionForm.isActive"
                  id="promo-active"
                  type="checkbox"
                  class="w-4 h-4 text-orange bg-gray-100 border-gray-300 rounded focus:ring-orange focus:ring-2"
                >
                <label for="promo-active" class="ml-2 text-sm text-gray-700">
                  Activer immédiatement cette promotion
                </label>
              </div>
  
              <div class="flex justify-end space-x-3 pt-4">
                <button 
                  type="button"
                  @click="closePromotionModal"
                  class="px-4 py-2 text-gray-600 border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors"
                >
                  Annuler
                </button>
                <button 
                  type="submit"
                  class="px-4 py-2 bg-orange text-white rounded-lg hover:bg-orange-600 transition-colors"
                >
                  {{ editingPromotion ? 'Modifier' : 'Créer' }}
                </button>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
  </template>
  
  <script setup>
  import { ref, reactive, computed } from 'vue'
  
  const emit = defineEmits(['close'])
  
  const showCreatePromotion = ref(false)
  const editingPromotion = ref(null)
  const filterStatus = ref('all')
  
  const promotionForm = reactive({
    name: '',
    code: '',
    type: 'percentage',
    value: '',
    startDate: '',
    endDate: '',
    maxUses: '',
    minAmount: '',
    isActive: true
  })
  
  const promotions = ref([
    {
      id: 1,
      name: 'Soldes d\'été 2024',
      code: 'SUMMER2024',
      type: 'percentage',
      value: 20,
      startDate: '2024-06-01T00:00',
      endDate: '2024-08-31T23:59',
      used: 45,
      maxUses: 100,
      status: 'active'
    },
    {
      id: 2,
      name: 'Livraison gratuite',
      code: 'FREESHIP',
      type: 'free_shipping',
      value: 0,
      startDate: '2024-01-01T00:00',
      endDate: '2024-12-31T23:59',
      used: 128,
      maxUses: null,
      status: 'active'
    },
    {
      id: 3,
      name: 'Réduction Black Friday',
      code: 'BLACKFRIDAY',
      type: 'fixed',
      value: 50000,
      startDate: '2024-11-29T00:00',
      endDate: '2024-11-29T23:59',
      used: 0,
      maxUses: 50,
      status: 'scheduled'
    }
  ])
  
  const activePromotions = computed(() => 
    promotions.value.filter(p => p.status === 'active')
  )
  
  const filteredPromotions = computed(() => {
    if (filterStatus.value === 'all') return promotions.value
    return promotions.value.filter(p => p.status === filterStatus.value)
  })
  
  const totalSavings = ref(1250000)
  const usedCodes = ref(173)
  const conversionRate = ref(12.5)
  
  const getTypeClass = (type) => {
    switch (type) {
      case 'percentage': return 'bg-blue-100 text-blue-800'
      case 'fixed': return 'bg-green-100 text-green-800'
      case 'free_shipping': return 'bg-purple-100 text-purple-800'
      default: return 'bg-gray-100 text-gray-800'
    }
  }
  
  const getTypeLabel = (type) => {
    switch (type) {
      case 'percentage': return 'Pourcentage'
      case 'fixed': return 'Montant fixe'
      case 'free_shipping': return 'Livraison gratuite'
      default: return type
    }
  }
  
  const getStatusClass = (status) => {
    switch (status) {
      case 'active': return 'bg-green-100 text-green-800'
      case 'scheduled': return 'bg-yellow-100 text-yellow-800'
      case 'expired': return 'bg-red-100 text-red-800'
      default: return 'bg-gray-100 text-gray-800'
    }
  }
  
  const getStatusLabel = (status) => {
    switch (status) {
      case 'active': return 'Active'
      case 'scheduled': return 'Programmée'
      case 'expired': return 'Expirée'
      default: return status
    }
  }
  
  const formatDate = (dateString) => {
    return new Date(dateString).toLocaleDateString('fr-FR')
  }
  
  const editPromotion = (promotion) => {
    editingPromotion.value = promotion
    Object.assign(promotionForm, promotion)
    showCreatePromotion.value = true
  }
  
  const togglePromotion = (promotion) => {
    promotion.status = promotion.status === 'active' ? 'expired' : 'active'
  }
  
  const deletePromotion = (id) => {
    if (confirm('Êtes-vous sûr de vouloir supprimer cette promotion ?')) {
      const index = promotions.value.findIndex(p => p.id === id)
      if (index !== -1) {
        promotions.value.splice(index, 1)
      }
    }
  }
  
  const savePromotion = () => {
    if (editingPromotion.value) {
      // Modifier la promotion existante
      Object.assign(editingPromotion.value, promotionForm)
    } else {
      // Créer une nouvelle promotion
      const newPromotion = {
        ...promotionForm,
        id: Date.now(),
        used: 0,
        status: promotionForm.isActive ? 'active' : 'scheduled'
      }
      promotions.value.push(newPromotion)
    }
    closePromotionModal()
  }
  
  const closePromotionModal = () => {
    showCreatePromotion.value = false
    editingPromotion.value = null
    Object.keys(promotionForm).forEach(key => {
      promotionForm[key] = key === 'type' ? 'percentage' : key === 'isActive' ? true : ''
    })
  }
  
  const handleBackdropClick = (event) => {
    if (event.target === event.currentTarget) {
      emit('close')
    }
  }
  </script>
  
  <style scoped>
  .bg-orange {
    background-color: #F65A11;
  }
  
  .text-orange {
    color: #F65A11;
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
  
  .z-60 {
    z-index: 60;
  }
  </style>  