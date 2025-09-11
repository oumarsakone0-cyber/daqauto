<template>
    <div class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
      <div class="bg-white rounded-lg p-8 max-w-2xl w-full mx-4 max-h-[90vh] overflow-y-auto">
        <div class="flex justify-between items-center mb-6">
          <h2 class="text-2xl font-bold text-gray-900">Détails de la commande</h2>
          <button @click="$emit('close')" class="text-gray-400 hover:text-gray-600">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
            </svg>
          </button>
        </div>
  
        <div v-if="order" class="space-y-6">
          <!-- Informations principales -->
          <div class="bg-gray-50 rounded-lg p-4">
            <div class="flex items-center space-x-4 mb-4">
              <div class="w-16 h-16 bg-gray-200 rounded-lg flex items-center justify-center">
                <span class="text-2xl">{{ order.icon }}</span>
              </div>
              <div>
                <h3 class="text-lg font-semibold text-gray-900">{{ order.product }}</h3>
                <p class="text-gray-600">{{ order.orderNumber }}</p>
                <span :class="getStatusBadgeClass(order.status)" class="inline-block text-xs font-medium px-2.5 py-0.5 rounded-full mt-1">
                  {{ order.status }}
                </span>
              </div>
            </div>
          </div>
  
          <!-- Informations client -->
          <div>
            <h4 class="text-lg font-medium text-gray-900 mb-3">Informations client</h4>
            <div class="bg-white border border-gray-200 rounded-lg p-4">
              <div class="flex items-center space-x-3 mb-2">
                <div class="w-8 h-8 bg-gray-200 rounded-full flex items-center justify-center text-sm font-medium text-gray-700">
                  {{ order.customer.charAt(0) }}
                </div>
                <span class="font-medium text-gray-900">{{ order.customer }}</span>
              </div>
              <p class="text-sm text-gray-600">Date de commande: {{ order.date }}</p>
            </div>
          </div>
  
          <!-- Détails du produit -->
          <div>
            <h4 class="text-lg font-medium text-gray-900 mb-3">Détails du produit</h4>
            <div class="bg-white border border-gray-200 rounded-lg p-4 space-y-3">
              <div class="flex justify-between">
                <span class="text-gray-600">Prix total:</span>
                <span class="font-medium text-gray-900">{{ order.total }}</span>
              </div>
              <div class="flex justify-between">
                <span class="text-gray-600">Quantité:</span>
                <span class="font-medium text-gray-900">{{ order.items }}</span>
              </div>
              <div class="flex justify-between">
                <span class="text-gray-600">Livraison:</span>
                <span class="font-medium text-gray-900">{{ order.delivery }}</span>
              </div>
            </div>
          </div>
  
          <!-- Couleurs disponibles -->
          <div>
            <h4 class="text-lg font-medium text-gray-900 mb-3">Couleurs disponibles</h4>
            <div class="flex flex-wrap gap-2">
              <div 
                v-for="(color, index) in order.colors" 
                :key="index" 
                class="w-8 h-8 rounded-md border border-gray-200 shadow-sm" 
                :style="{ backgroundColor: color }"
                :title="getColorName(index)"
              ></div>
            </div>
          </div>
  
          <!-- Tailles disponibles -->
          <div>
            <h4 class="text-lg font-medium text-gray-900 mb-3">Tailles disponibles</h4>
            <div class="flex flex-wrap gap-2">
              <div 
                v-for="(size, index) in order.sizes" 
                :key="index" 
                class="px-3 py-1 bg-gray-100 border border-gray-300 rounded-md text-sm font-medium text-gray-700"
              >
                {{ size }}
              </div>
            </div>
          </div>
  
          <!-- Actions -->
          <div class="flex space-x-3 pt-4 border-t border-gray-200">
            <button class="flex-1 bg-orange text-white py-2 px-4 rounded-lg hover:bg-orange-600 transition-colors">
              Traiter la commande
            </button>
            <button class="flex-1 bg-gray-100 text-gray-700 py-2 px-4 rounded-lg hover:bg-gray-200 transition-colors">
              Contacter le client
            </button>
          </div>
        </div>
      </div>
    </div>
  </template>
  
  <script setup>
  defineProps(['order'])
  defineEmits(['close'])
  
  const getStatusBadgeClass = (status) => {
    switch (status) {
      case 'Completed':
        return 'bg-green-100 text-green-800 border border-green-200'
      case 'Pending':
        return 'bg-yellow-100 text-yellow-800 border border-yellow-200'
      case 'Failed':
        return 'bg-red-100 text-red-800 border border-red-200'
      default:
        return 'bg-gray-100 text-gray-800 border border-gray-200'
    }
  }
  
  const getColorName = (index) => {
    const colorNames = ['Noir', 'Blanc', 'Gris', 'Rouge', 'Bleu', 'Vert', 'Jaune', 'Orange', 'Rose', 'Violet']
    return colorNames[index] || 'Couleur'
  }
  </script>
  
  <style scoped>
  .bg-orange {
    background-color: #F65A11;
  }
  
  .hover\:bg-orange-600:hover {
    background-color: #e54a0a;
  }
  </style>
  