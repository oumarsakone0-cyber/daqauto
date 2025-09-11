<template>
    <div class="fixed top-4 right-4 z-50 space-y-2">
      <div 
        v-for="notification in notifications" 
        :key="notification.id"
        :class="[
          'max-w-sm w-full bg-white shadow-lg rounded-lg pointer-events-auto ring-1 ring-black ring-opacity-5 overflow-hidden transform transition-all duration-300 ease-in-out',
          notification.show ? 'translate-x-0 opacity-100' : 'translate-x-full opacity-0'
        ]"
      >
        <div class="p-4">
          <div class="flex items-start">
            <div class="flex-shrink-0">
              <svg 
                v-if="notification.type === 'success'" 
                class="h-6 w-6 text-green-400" 
                fill="none" 
                viewBox="0 0 24 24" 
                stroke="currentColor"
              >
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
              </svg>
              <svg 
                v-else-if="notification.type === 'error'" 
                class="h-6 w-6 text-red-400" 
                fill="none" 
                viewBox="0 0 24 24" 
                stroke="currentColor"
              >
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16c-.77.833.192 2.5 1.732 2.5z"/>
              </svg>
              <svg 
                v-else-if="notification.type === 'warning'" 
                class="h-6 w-6 text-yellow-400" 
                fill="none" 
                viewBox="0 0 24 24" 
                stroke="currentColor"
              >
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16c-.77.833.192 2.5 1.732 2.5z"/>
              </svg>
              <svg 
                v-else 
                class="h-6 w-6 text-blue-400" 
                fill="none" 
                viewBox="0 0 24 24" 
                stroke="currentColor"
              >
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
              </svg>
            </div>
            <div class="ml-3 w-0 flex-1 pt-0.5">
              <p class="text-sm font-medium text-gray-900">{{ notification.title }}</p>
              <p class="mt-1 text-sm text-gray-500">{{ notification.message }}</p>
            </div>
            <div class="ml-4 flex-shrink-0 flex">
              <button 
                @click="removeNotification(notification.id)"
                class="bg-white rounded-md inline-flex text-gray-400 hover:text-gray-500 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-orange"
              >
                <span class="sr-only">Fermer</span>
                <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                  <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/>
                </svg>
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>
  </template>
  
  <script setup>
  import { ref, onMounted } from 'vue'
  
  const notifications = ref([])
  
  const addNotification = (notification) => {
    const id = Date.now()
    const newNotification = {
      id,
      show: false,
      ...notification
    }
    
    notifications.value.push(newNotification)
    
    // Animer l'entrée
    setTimeout(() => {
      newNotification.show = true
    }, 100)
    
    // Auto-suppression après 5 secondes
    setTimeout(() => {
      removeNotification(id)
    }, 5000)
  }
  
  const removeNotification = (id) => {
    const notification = notifications.value.find(n => n.id === id)
    if (notification) {
      notification.show = false
      setTimeout(() => {
        const index = notifications.value.findIndex(n => n.id === id)
        if (index !== -1) {
          notifications.value.splice(index, 1)
        }
      }, 300)
    }
  }
  
  // Exposer les méthodes pour utilisation externe
  defineExpose({
    addNotification,
    removeNotification
  })
  
  onMounted(() => {
    // Écouter les événements de notification globaux
    window.addEventListener('show-notification', (event) => {
      addNotification(event.detail)
    })
  })
  </script>
  
  <style scoped>
  .focus\:ring-orange:focus {
    --tw-ring-color: rgba(246, 90, 17, 0.5);
  }
  </style>
  