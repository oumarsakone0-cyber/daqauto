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
          <h2 class="text-2xl font-bold text-gray-900">Modifier le client</h2>
          <button @click="$emit('close')" class="text-gray-400 hover:text-gray-600">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
            </svg>
          </button>
        </div>
  
        <form @submit.prevent="handleSubmit" v-if="editData" class="space-y-6">
          <!-- Type de client -->
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Type de client</label>
            <div class="flex space-x-4">
              <label class="flex items-center">
                <input 
                  v-model="editData.type"
                  type="radio" 
                  value="registered"
                  class="w-4 h-4 text-orange bg-gray-100 border-gray-300 focus:ring-orange focus:ring-2"
                >
                <span class="ml-2 text-sm text-gray-700">Client inscrit (e-commerce)</span>
              </label>
              <label class="flex items-center">
                <input 
                  v-model="editData.type"
                  type="radio" 
                  value="prospect"
                  class="w-4 h-4 text-orange bg-gray-100 border-gray-300 focus:ring-orange focus:ring-2"
                >
                <span class="ml-2 text-sm text-gray-700">Prospect (contact direct)</span>
              </label>
            </div>
          </div>
  
          <!-- Informations personnelles -->
          <div class="grid grid-cols-2 gap-4">
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">Nom complet *</label>
              <input 
                v-model="editData.name"
                type="text" 
                required
                class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-orange focus:border-orange"
              >
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">Email *</label>
              <input 
                v-model="editData.email"
                type="email" 
                required
                class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-orange focus:border-orange"
              >
            </div>
          </div>
  
          <div class="grid grid-cols-2 gap-4">
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">Téléphone *</label>
              <input 
                v-model="editData.phone"
                type="tel" 
                required
                class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-orange focus:border-orange"
              >
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">Adresse</label>
              <input 
                v-model="editData.address"
                type="text" 
                class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-orange focus:border-orange"
              >
            </div>
          </div>
  
          <!-- Source et segment -->
          <div class="grid grid-cols-2 gap-4">
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">Source</label>
              <select 
                v-model="editData.source"
                class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-orange focus:border-orange"
              >
                <option value="">Sélectionner une source</option>
                <option value="Site web">Site web</option>
                <option value="WhatsApp">WhatsApp</option>
                <option value="Facebook">Facebook</option>
                <option value="Instagram">Instagram</option>
                <option value="Contact direct">Contact direct</option>
                <option value="Recommandation">Recommandation</option>
                <option value="Autre">Autre</option>
              </select>
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">Segment</label>
              <select 
                v-model="editData.segment"
                class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-orange focus:border-orange"
              >
                <option value="new">Nouveau client</option>
                <option value="regular">Client régulier</option>
                <option value="vip">Client VIP</option>
                <option value="inactive">Client inactif</option>
              </select>
            </div>
          </div>
  
          <!-- Tags -->
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Tags</label>
            <div class="flex flex-wrap gap-2 mb-3">
              <span 
                v-for="tag in editData.tags" 
                :key="tag"
                class="px-3 py-1 bg-orange-100 text-orange-800 text-sm rounded-full flex items-center"
              >
                {{ tag }}
                <button 
                  @click="removeTag(tag)"
                  type="button"
                  class="ml-2 text-orange-600 hover:text-orange-800"
                >
                  ×
                </button>
              </span>
            </div>
            <div class="flex gap-2">
              <input 
                v-model="newTag"
                type="text" 
                @keypress.enter.prevent="addTag"
                class="flex-1 border border-gray-300 rounded-lg px-3 py-2 focus:ring-orange focus:border-orange"
                placeholder="Ajouter un tag..."
              >
              <button 
                @click="addTag"
                type="button"
                class="px-4 py-2 bg-orange text-white rounded-lg hover:bg-orange-600 transition-colors"
              >
                Ajouter
              </button>
            </div>
          </div>
  
          <!-- Notes -->
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Notes</label>
            <textarea 
              v-model="editData.notes"
              rows="3"
              class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-orange focus:border-orange"
              placeholder="Notes sur le client..."
            ></textarea>
          </div>
  
          <!-- Statut -->
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Statut</label>
            <select 
              v-model="editData.status"
              class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-orange focus:border-orange"
            >
              <option value="Actif">Actif</option>
              <option value="Prospect">Prospect</option>
              <option value="Inactif">Inactif</option>
            </select>
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
  
  const props = defineProps(['client'])
  const emit = defineEmits(['close', 'save'])
  
  const editData = ref(null)
  const newTag = ref('')
  
  watch(() => props.client, (newClient) => {
    if (newClient) {
      editData.value = {
        ...newClient,
        tags: [...(newClient.tags || [])]
      }
    }
  }, { immediate: true })
  
  const addTag = () => {
    if (newTag.value && !editData.value.tags.includes(newTag.value)) {
      editData.value.tags.push(newTag.value)
      newTag.value = ''
    }
  }
  
  const removeTag = (tag) => {
    const index = editData.value.tags.indexOf(tag)
    if (index !== -1) {
      editData.value.tags.splice(index, 1)
    }
  }
  
  const handleSubmit = () => {
    emit('save', editData.value)
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
  
  .bg-orange-100 {
    background-color: rgba(246, 90, 17, 0.1);
  }
  
  .text-orange-800 {
    color: #9a3412;
  }
  
  .text-orange-600 {
    color: #ea580c;
  }
  
  .hover\:text-orange-800:hover {
    color: #9a3412;
  }
  </style>
  