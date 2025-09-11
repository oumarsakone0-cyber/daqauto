<template>
    <div 
      class="fixed inset-0 bg-black bg-opacity-30 flex items-center justify-center z-50"
      @click="handleBackdropClick"
    >
      <div 
        class="bg-white rounded-lg p-8 max-w-2xl w-full mx-4 max-h-[90vh] overflow-y-auto"
        @click.stop
      >
        <div class="flex justify-between items-center mb-6">
          <h2 class="text-2xl font-bold text-gray-900">Segmentation des clients</h2>
          <button @click="$emit('close')" class="text-gray-400 hover:text-gray-600">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
            </svg>
          </button>
        </div>
  
        <div class="space-y-6">
          <!-- Type de segmentation -->
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-3">Type de segmentation</label>
            <div class="grid grid-cols-2 gap-4">
              <label class="flex items-center p-4 border border-gray-200 rounded-lg cursor-pointer hover:bg-gray-50">
                <input 
                  v-model="segmentData.type"
                  type="radio" 
                  value="automatic"
                  class="w-4 h-4 text-orange bg-gray-100 border-gray-300 focus:ring-orange focus:ring-2"
                >
                <div class="ml-3">
                  <div class="font-medium text-gray-900">Automatique</div>
                  <div class="text-sm text-gray-500">Basé sur des critères prédéfinis</div>
                </div>
              </label>
              <label class="flex items-center p-4 border border-gray-200 rounded-lg cursor-pointer hover:bg-gray-50">
                <input 
                  v-model="segmentData.type"
                  type="radio" 
                  value="manual"
                  class="w-4 h-4 text-orange bg-gray-100 border-gray-300 focus:ring-orange focus:ring-2"
                >
                <div class="ml-3">
                  <div class="font-medium text-gray-900">Manuel</div>
                  <div class="text-sm text-gray-500">Sélection manuelle des clients</div>
                </div>
              </label>
            </div>
          </div>
  
          <!-- Segmentation automatique -->
          <div v-if="segmentData.type === 'automatic'" class="space-y-4">
            <h3 class="text-lg font-medium text-gray-900">Critères de segmentation</h3>
            
            <!-- Critère par valeur -->
            <div class="border border-gray-200 rounded-lg p-4">
              <div class="flex items-center mb-3">
                <input 
                  v-model="segmentData.criteria.byValue"
                  id="by-value"
                  type="checkbox"
                  class="w-4 h-4 text-orange bg-gray-100 border-gray-300 rounded focus:ring-orange focus:ring-2"
                >
                <label for="by-value" class="ml-2 font-medium text-gray-900">
                  Segmenter par valeur des achats
                </label>
              </div>
              <div v-if="segmentData.criteria.byValue" class="grid grid-cols-3 gap-4 ml-6">
                <div>
                  <label class="block text-sm text-gray-600 mb-1">VIP (> FCFA)</label>
                  <input 
                    v-model="segmentData.thresholds.vip"
                    type="number" 
                    class="w-full border border-gray-300 rounded px-3 py-2 text-sm"
                    placeholder="500000"
                  >
                </div>
                <div>
                  <label class="block text-sm text-gray-600 mb-1">Régulier (> FCFA)</label>
                  <input 
                    v-model="segmentData.thresholds.regular"
                    type="number" 
                    class="w-full border border-gray-300 rounded px-3 py-2 text-sm"
                    placeholder="100000"
                  >
                </div>
                <div>
                  <label class="block text-sm text-gray-600 mb-1">Nouveau (< FCFA)</label>
                  <input 
                    v-model="segmentData.thresholds.new"
                    type="number" 
                    class="w-full border border-gray-300 rounded px-3 py-2 text-sm"
                    placeholder="50000"
                  >
                </div>
              </div>
            </div>
  
            <!-- Critère par fréquence -->
            <div class="border border-gray-200 rounded-lg p-4">
              <div class="flex items-center mb-3">
                <input 
                  v-model="segmentData.criteria.byFrequency"
                  id="by-frequency"
                  type="checkbox"
                  class="w-4 h-4 text-orange bg-gray-100 border-gray-300 rounded focus:ring-orange focus:ring-2"
                >
                <label for="by-frequency" class="ml-2 font-medium text-gray-900">
                  Segmenter par fréquence d'achat
                </label>
              </div>
              <div v-if="segmentData.criteria.byFrequency" class="grid grid-cols-2 gap-4 ml-6">
                <div>
                  <label class="block text-sm text-gray-600 mb-1">Fidèle (> commandes/mois)</label>
                  <input 
                    v-model="segmentData.frequency.loyal"
                    type="number" 
                    class="w-full border border-gray-300 rounded px-3 py-2 text-sm"
                    placeholder="2"
                  >
                </div>
                <div>
                  <label class="block text-sm text-gray-600 mb-1">Inactif (< jours depuis dernière commande)</label>
                  <input 
                    v-model="segmentData.frequency.inactive"
                    type="number" 
                    class="w-full border border-gray-300 rounded px-3 py-2 text-sm"
                    placeholder="90"
                  >
                </div>
              </div>
            </div>
  
            <!-- Critère par source -->
            <div class="border border-gray-200 rounded-lg p-4">
              <div class="flex items-center mb-3">
                <input 
                  v-model="segmentData.criteria.bySource"
                  id="by-source"
                  type="checkbox"
                  class="w-4 h-4 text-orange bg-gray-100 border-gray-300 rounded focus:ring-orange focus:ring-2"
                >
                <label for="by-source" class="ml-2 font-medium text-gray-900">
                  Segmenter par source d'acquisition
                </label>
              </div>
              <div v-if="segmentData.criteria.bySource" class="ml-6">
                <div class="grid grid-cols-2 gap-2">
                  <label v-for="source in availableSources" :key="source" class="flex items-center">
                    <input 
                      v-model="segmentData.selectedSources"
                      :value="source"
                      type="checkbox"
                      class="w-4 h-4 text-orange bg-gray-100 border-gray-300 rounded focus:ring-orange focus:ring-2"
                    >
                    <span class="ml-2 text-sm text-gray-700">{{ source }}</span>
                  </label>
                </div>
              </div>
            </div>
          </div>
  
          <!-- Segmentation manuelle -->
          <div v-if="segmentData.type === 'manual'" class="space-y-4">
            <h3 class="text-lg font-medium text-gray-900">Sélection manuelle</h3>
            
            <div class="border border-gray-200 rounded-lg p-4">
              <div class="mb-3">
                <label class="block text-sm font-medium text-gray-700 mb-2">Rechercher des clients</label>
                <input 
                  v-model="searchQuery"
                  type="text" 
                  class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-orange focus:border-orange"
                  placeholder="Nom, email ou téléphone..."
                >
              </div>
              
              <div class="max-h-40 overflow-y-auto">
                <div v-for="client in filteredClients" :key="client.id" class="flex items-center p-2 hover:bg-gray-50 rounded">
                  <input 
                    v-model="segmentData.selectedClients"
                    :value="client.id"
                    type="checkbox"
                    class="w-4 h-4 text-orange bg-gray-100 border-gray-300 rounded focus:ring-orange focus:ring-2"
                  >
                  <div class="ml-3 flex-1">
                    <div class="font-medium text-gray-900">{{ client.name }}</div>
                    <div class="text-sm text-gray-500">{{ client.email }}</div>
                  </div>
                </div>
              </div>
            </div>
          </div>
  
          <!-- Nouveau segment -->
          <div>
            <h3 class="text-lg font-medium text-gray-900 mb-4">Créer un nouveau segment</h3>
            
            <div class="grid grid-cols-2 gap-4">
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Nom du segment</label>
                <input 
                  v-model="segmentData.name"
                  type="text" 
                  required
                  class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-orange focus:border-orange"
                  placeholder="Ex: Clients Premium"
                >
              </div>
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Couleur</label>
                <div class="flex space-x-2">
                  <input 
                    v-model="segmentData.color"
                    type="color" 
                    class="w-12 h-10 border border-gray-300 rounded"
                  >
                  <input 
                    v-model="segmentData.color"
                    type="text" 
                    class="flex-1 border border-gray-300 rounded-lg px-3 py-2 focus:ring-orange focus:border-orange"
                    placeholder="#FF5722"
                  >
                </div>
              </div>
            </div>
  
            <div class="mt-4">
              <label class="block text-sm font-medium text-gray-700 mb-2">Description</label>
              <textarea 
                v-model="segmentData.description"
                rows="3"
                class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-orange focus:border-orange"
                placeholder="Description du segment..."
              ></textarea>
            </div>
          </div>
  
          <!-- Aperçu -->
          <div v-if="previewCount > 0" class="bg-blue-50 border border-blue-200 rounded-lg p-4">
            <div class="flex items-center">
              <svg class="w-5 h-5 text-blue-400 mr-2" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
              </svg>
              <span class="text-sm font-medium text-blue-800">
                {{ previewCount }} clients seront ajoutés à ce segment
              </span>
            </div>
          </div>
  
          <!-- Boutons d'action -->
          <div class="flex space-x-3 pt-4 border-t border-gray-200">
            <button 
              type="button"
              @click="$emit('close')"
              class="flex-1 bg-gray-100 text-gray-700 py-2 px-4 rounded-lg hover:bg-gray-200 transition-colors"
            >
              Annuler
            </button>
            <button 
              @click="createSegment"
              :disabled="!segmentData.name || previewCount === 0"
              class="flex-1 bg-orange text-white py-2 px-4 rounded-lg hover:bg-orange-600 transition-colors disabled:opacity-50 disabled:cursor-not-allowed"
            >
              Créer le segment
            </button>
          </div>
        </div>
      </div>
    </div>
  </template>
  
  <script setup>
  import { ref, reactive, computed } from 'vue'
  
  const emit = defineEmits(['close', 'segment'])
  
  const searchQuery = ref('')
  
  const segmentData = reactive({
    type: 'automatic',
    name: '',
    color: '#F65A11',
    description: '',
    criteria: {
      byValue: false,
      byFrequency: false,
      bySource: false
    },
    thresholds: {
      vip: 500000,
      regular: 100000,
      new: 50000
    },
    frequency: {
      loyal: 2,
      inactive: 90
    },
    selectedSources: [],
    selectedClients: []
  })
  
  const availableSources = ['Site web', 'WhatsApp', 'Facebook', 'Instagram', 'Contact direct', 'Recommandation']
  
  // Clients mockés pour la sélection manuelle
  const mockClients = [
    { id: 1, name: 'Marie Dubois', email: 'marie.dubois@email.com' },
    { id: 2, name: 'Jean Martin', email: 'jean.martin@email.com' },
    { id: 3, name: 'Sophie Laurent', email: 'sophie.laurent@email.com' },
    { id: 4, name: 'Pierre Durand', email: 'pierre.durand@email.com' },
    { id: 5, name: 'Claire Moreau', email: 'claire.moreau@email.com' }
  ]
  
  const filteredClients = computed(() => {
    if (!searchQuery.value) return mockClients
    const query = searchQuery.value.toLowerCase()
    return mockClients.filter(client => 
      client.name.toLowerCase().includes(query) || 
      client.email.toLowerCase().includes(query)
    )
  })
  
  const previewCount = computed(() => {
    if (segmentData.type === 'manual') {
      return segmentData.selectedClients.length
    } else {
      // Simulation du nombre de clients qui correspondent aux critères
      let count = 0
      if (segmentData.criteria.byValue) count += 15
      if (segmentData.criteria.byFrequency) count += 8
      if (segmentData.criteria.bySource) count += segmentData.selectedSources.length * 3
      return Math.min(count, 50) // Maximum 50 pour la démo
    }
  })
  
  const createSegment = () => {
    emit('segment', segmentData)
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
  </style>
  