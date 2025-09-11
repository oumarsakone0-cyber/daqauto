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
          <h2 class="text-2xl font-bold text-gray-900">Profil Client</h2>
          <button @click="$emit('close')" class="text-gray-400 hover:text-gray-600">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
            </svg>
          </button>
        </div>
  
        <div v-if="client" class="space-y-6">
          <!-- Informations principales -->
          <div class="bg-gray-50 rounded-lg p-6">
            <div class="flex items-start justify-between">
              <div class="flex items-start space-x-4">
                <div class="w-16 h-16 bg-gray-200 rounded-full flex items-center justify-center">
                  <span class="text-2xl font-bold text-gray-700">{{ client.name.charAt(0) }}</span>
                </div>
                <div>
                  <h3 class="text-xl font-semibold text-gray-900 mb-1">{{ client.name }}</h3>
                  <p class="text-gray-600 mb-2">ID: {{ client.id }}</p>
                  <div class="flex items-center space-x-3">
                    <span :class="getTypeClass(client.type)" class="px-2 py-1 text-xs font-medium rounded-full">
                      {{ getTypeLabel(client.type) }}
                    </span>
                    <span :class="getStatusClass(client.status)" class="px-2 py-1 text-xs font-medium rounded-full">
                      {{ client.status }}
                    </span>
                  </div>
                </div>
              </div>
              <div class="flex space-x-2">
                <button class="px-4 py-2 bg-orange text-white rounded-lg hover:bg-orange-600 transition-colors">
                  Envoyer message
                </button>
                <button class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition-colors">
                  Modifier
                </button>
              </div>
            </div>
          </div>
  
          <!-- Statistiques du client -->
          <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div class="bg-white border border-gray-200 rounded-lg p-4 text-center">
              <div class="text-2xl font-bold text-gray-900">{{ client.totalOrders }}</div>
              <div class="text-sm text-gray-600">Commandes</div>
            </div>
            <div class="bg-white border border-gray-200 rounded-lg p-4 text-center">
              <div class="text-2xl font-bold text-green-600">{{ client.totalValue.toLocaleString() }}</div>
              <div class="text-sm text-gray-600">FCFA dépensés</div>
            </div>
            <div class="bg-white border border-gray-200 rounded-lg p-4 text-center">
              <div class="text-2xl font-bold text-blue-600">{{ client.totalOrders > 0 ? Math.round(client.totalValue / client.totalOrders).toLocaleString() : 0 }}</div>
              <div class="text-sm text-gray-600">FCFA/commande</div>
            </div>
            <div class="bg-white border border-gray-200 rounded-lg p-4 text-center">
              <div class="text-2xl font-bold text-orange">{{ getActivityDays(client.lastActivity) }}</div>
              <div class="text-sm text-gray-600">Jours depuis dernière activité</div>
            </div>
          </div>
  
          <!-- Informations de contact -->
          <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div class="bg-white border border-gray-200 rounded-lg p-6">
              <h4 class="text-lg font-medium text-gray-900 mb-4">Informations de contact</h4>
              <div class="space-y-3">
                <div class="flex items-center">
                  <svg class="w-5 h-5 text-gray-400 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 4.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                  </svg>
                  <span class="text-gray-900">{{ client.email }}</span>
                </div>
                <div class="flex items-center">
                  <svg class="w-5 h-5 text-gray-400 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path>
                  </svg>
                  <span class="text-gray-900">{{ client.phone }}</span>
                </div>
                <div class="flex items-center">
                  <svg class="w-5 h-5 text-gray-400 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                  </svg>
                  <span class="text-gray-900">{{ client.address || 'Non renseigné' }}</span>
                </div>
              </div>
            </div>
  
            <div class="bg-white border border-gray-200 rounded-lg p-6">
              <h4 class="text-lg font-medium text-gray-900 mb-4">Informations supplémentaires</h4>
              <div class="space-y-3">
                <div>
                  <span class="text-sm text-gray-600">Source :</span>
                  <span class="ml-2 text-gray-900">{{ client.source || 'Non renseigné' }}</span>
                </div>
                <div>
                  <span class="text-sm text-gray-600">Segment :</span>
                  <span class="ml-2 text-gray-900">{{ getSegmentLabel(client.segment) }}</span>
                </div>
                <div>
                  <span class="text-sm text-gray-600">Date d'inscription :</span>
                  <span class="ml-2 text-gray-900">{{ client.registrationDate ? formatDate(client.registrationDate) : 'Non inscrit' }}</span>
                </div>
                <div>
                  <span class="text-sm text-gray-600">Dernière activité :</span>
                  <span class="ml-2 text-gray-900">{{ formatDate(client.lastActivity) }}</span>
                </div>
              </div>
            </div>
          </div>
  
          <!-- Tags -->
          <div class="bg-white border border-gray-200 rounded-lg p-6">
            <h4 class="text-lg font-medium text-gray-900 mb-4">Tags</h4>
            <div class="flex flex-wrap gap-2">
              <span 
                v-for="tag in client.tags" 
                :key="tag"
                class="px-3 py-1 bg-orange-100 text-orange-800 text-sm rounded-full"
              >
                {{ tag }}
              </span>
              <button class="px-3 py-1 bg-gray-100 text-gray-600 text-sm rounded-full hover:bg-gray-200 transition-colors">
                + Ajouter un tag
              </button>
            </div>
          </div>
  
          <!-- Historique des commandes -->
          <div class="bg-white border border-gray-200 rounded-lg p-6">
            <div class="flex items-center justify-between mb-4">
              <h4 class="text-lg font-medium text-gray-900">Historique des commandes</h4>
              <button class="text-orange hover:text-orange-600 text-sm font-medium">
                Voir toutes les commandes
              </button>
            </div>
            
            <div v-if="client.totalOrders > 0" class="space-y-3">
              <div v-for="order in mockOrders" :key="order.id" class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                <div class="flex items-center space-x-3">
                  <div class="w-8 h-8 bg-orange-100 rounded-full flex items-center justify-center">
                    <span class="text-xs font-medium text-orange-800">#{{ order.id }}</span>
                  </div>
                  <div>
                    <div class="font-medium text-gray-900">{{ order.products }}</div>
                    <div class="text-sm text-gray-500">{{ formatDate(order.date) }}</div>
                  </div>
                </div>
                <div class="text-right">
                  <div class="font-medium text-gray-900">{{ order.total.toLocaleString() }} FCFA</div>
                  <span :class="getOrderStatusClass(order.status)" class="text-xs px-2 py-1 rounded-full">
                    {{ order.status }}
                  </span>
                </div>
              </div>
            </div>
            
            <div v-else class="text-center py-8">
              <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path>
              </svg>
              <h3 class="mt-2 text-sm font-medium text-gray-900">Aucune commande</h3>
              <p class="mt-1 text-sm text-gray-500">Ce client n'a pas encore passé de commande.</p>
            </div>
          </div>
  
          <!-- Notes -->
          <div class="bg-white border border-gray-200 rounded-lg p-6">
            <h4 class="text-lg font-medium text-gray-900 mb-4">Notes</h4>
            <div v-if="client.notes" class="text-gray-700">
              {{ client.notes }}
            </div>
            <div v-else class="text-gray-500 italic">
              Aucune note pour ce client.
            </div>
            <button class="mt-3 text-orange hover:text-orange-600 text-sm font-medium">
              Ajouter une note
            </button>
          </div>
        </div>
      </div>
    </div>
  </template>
  
  <script setup>
  import { computed } from 'vue'
  
  const props = defineProps(['client'])
  const emit = defineEmits(['close'])
  
  // Données mockées pour les commandes
  const mockOrders = computed(() => {
    if (!props.client || props.client.totalOrders === 0) return []
    
    return [
      {
        id: 12345,
        products: 'MacBook Air M1, iPhone 14',
        date: '2024-01-10T00:00:00',
        total: 450000,
        status: 'Livré'
      },
      {
        id: 12344,
        products: 'T-shirt Premium x3',
        date: '2024-01-05T00:00:00',
        total: 45000,
        status: 'Livré'
      },
      {
        id: 12343,
        products: 'Nike Air Max 270',
        date: '2023-12-28T00:00:00',
        total: 85000,
        status: 'Livré'
      }
    ].slice(0, Math.min(3, props.client.totalOrders))
  })
  
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
        return 'Client inscrit'
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
  
  const getSegmentLabel = (segment) => {
    switch (segment) {
      case 'vip':
        return 'Client VIP'
      case 'regular':
        return 'Client régulier'
      case 'new':
        return 'Nouveau client'
      case 'inactive':
        return 'Client inactif'
      default:
        return segment
    }
  }
  
  const getOrderStatusClass = (status) => {
    switch (status) {
      case 'Livré':
        return 'bg-green-100 text-green-800'
      case 'En cours':
        return 'bg-blue-100 text-blue-800'
      case 'Annulé':
        return 'bg-red-100 text-red-800'
      default:
        return 'bg-gray-100 text-gray-800'
    }
  }
  
  const formatDate = (dateString) => {
    return new Date(dateString).toLocaleDateString('fr-FR')
  }
  
  const getActivityDays = (lastActivity) => {
    const date = new Date(lastActivity)
    const now = new Date()
    return Math.floor((now - date) / (1000 * 60 * 60 * 24))
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
  
  .hover\:text-orange-600:hover {
    color: #ea580c;
  }
  
  .bg-orange-100 {
    background-color: rgba(246, 90, 17, 0.1);
  }
  
  .text-orange-800 {
    color: #9a3412;
  }
  </style>
  