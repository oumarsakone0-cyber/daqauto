<template>
    <div class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
      <div class="bg-white rounded-lg p-8 max-w-2xl w-full mx-4 max-h-[90vh] overflow-y-auto">
        <div class="flex justify-between items-center mb-6">
          <h2 class="text-2xl font-bold text-gray-900">Modifier la commande</h2>
          <button @click="$emit('close')" class="text-gray-400 hover:text-gray-600">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
            </svg>
          </button>
        </div>
  
        <form @submit.prevent="handleSubmit" v-if="editData" class="space-y-6">
          <!-- Informations de base -->
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Nom du produit</label>
            <input 
              v-model="editData.product"
              type="text" 
              required
              class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-orange focus:border-orange"
            >
          </div>
  
          <div class="grid grid-cols-2 gap-4">
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">Client</label>
              <input 
                v-model="editData.customer"
                type="text" 
                required
                class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-orange focus:border-orange"
              >
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">Statut</label>
              <select 
                v-model="editData.status"
                required
                class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-orange focus:border-orange"
              >
                <option value="Pending">En attente</option>
                <option value="Completed">Terminé</option>
                <option value="Failed">Échoué</option>
              </select>
            </div>
          </div>
  
          <div class="grid grid-cols-2 gap-4">
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">Prix total</label>
              <input 
                v-model="editData.total"
                type="text" 
                required
                class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-orange focus:border-orange"
              >
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">Quantité</label>
              <input 
                v-model="editData.items"
                type="text" 
                required
                class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-orange focus:border-orange"
              >
            </div>
          </div>
  
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Type de livraison</label>
            <select 
              v-model="editData.delivery"
              required
              class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-orange focus:border-orange"
            >
              <option value="Free Shipping">Livraison gratuite</option>
              <option value="Express Shipping">Livraison express</option>
              <option value="Standard Shipping">Livraison standard</option>
            </select>
          </div>
  
          <!-- Couleurs -->
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Couleurs disponibles</label>
            <div class="grid grid-cols-5 gap-2">
              <div v-for="color in availableColors" :key="color.value" class="flex items-center">
                <input 
                  :id="`edit-color-${color.value}`"
                  v-model="editData.colors"
                  :value="color.value"
                  type="checkbox"
                  class="w-4 h-4 text-orange bg-gray-100 border-gray-300 rounded focus:ring-orange focus:ring-2"
                >
                <label :for="`edit-color-${color.value}`" class="ml-2 flex items-center">
                  <div 
                    class="w-4 h-4 rounded border border-gray-300 mr-1"
                    :style="{ backgroundColor: color.value }"
                  ></div>
                </label>
              </div>
            </div>
          </div>
  
          <!-- Tailles -->
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Tailles disponibles</label>
            <div class="grid grid-cols-5 gap-2">
              <div v-for="size in availableSizes" :key="size" class="flex items-center">
                <input 
                  :id="`edit-size-${size}`"
                  v-model="editData.sizes"
                  :value="size"
                  type="checkbox"
                  class="w-4 h-4 text-orange bg-gray-100 border-gray-300 rounded focus:ring-orange focus:ring-2"
                >
                <label :for="`edit-size-${size}`" class="ml-2 text-sm text-gray-700">{{ size }}</label>
              </div>
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
              type="submit"
              class="flex-1 bg-orange text-white py-2 px-4 rounded-lg hover:bg-orange-600 transition-colors"
            >
              Sauvegarder
            </button>
          </div>
        </form>
      </div>
    </div>
  </template>
  
  <script setup>
  import { ref, watch } from 'vue'
  
  const props = defineProps(['order'])
  const emit = defineEmits(['close', 'save'])
  
  const editData = ref(null)
  
  const availableColors = [
    { name: 'Noir', value: '#000000' },
    { name: 'Blanc', value: '#FFFFFF' },
    { name: 'Gris', value: '#808080' },
    { name: 'Rouge', value: '#FF0000' },
    { name: 'Bleu', value: '#0000FF' },
    { name: 'Vert', value: '#008000' },
    { name: 'Jaune', value: '#FFFF00' },
    { name: 'Orange', value: '#FFA500' },
    { name: 'Rose', value: '#FFC0CB' },
    { name: 'Violet', value: '#800080' }
  ]
  
  const availableSizes = ['XS', 'S', 'M', 'L', 'XL', 'XXL', '36', '37', '38', '39', '40', '41', '42', '43', '44']
  
  watch(() => props.order, (newOrder) => {
    if (newOrder) {
      editData.value = {
        ...newOrder,
        colors: [...(newOrder.colors || [])],
        sizes: [...(newOrder.sizes || [])]
      }
    }
  }, { immediate: true })
  
  const handleSubmit = () => {
    emit('save', editData.value)
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
  