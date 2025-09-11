<template>
    <main class="flex-1 min-h-screen bg-gradient-to-br from-gray-50 to-gray-100" style="margin-top: 50px">
      <!-- Hero Section redesigné -->
      <div class="relative overflow-hidden bg-gradient-to-r from-dark-green via-dark-green to-emerald-800 rounded-2xl p-8 mb-8 text-white shadow-2xl" style="background: #22331d;">
        <!-- Motif de fond décoratif -->
        <div class="absolute inset-0 opacity-10">
          <div class="absolute top-0 left-0 w-40 h-40 bg-white rounded-full -translate-x-20 -translate-y-20"></div>
          <div class="absolute bottom-0 right-0 w-32 h-32 bg-orange rounded-full translate-x-16 translate-y-16"></div>
          <div class="absolute top-1/2 right-1/4 w-24 h-24 bg-white rounded-full opacity-20"></div>
        </div>
        
        <div class="relative z-10">
          <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-8">
            <!-- Titre et description -->
            <div class="flex-1">
              <h1 class="text-3xl lg:text-4xl font-bold mb-2">
                Messages <span class="text-orange-300">&amp; Communications</span>
              </h1>
              <p class="text-emerald-100 text-lg mb-6">Gérez vos conversations avec vos clients</p>
              
              <button 
                @click="showNewMessageModal = true"
                type="button" 
                class="group bg-gradient-to-r from-orange to-orange-600 hover:from-orange-600 hover:to-orange-700 text-white font-semibold rounded-xl px-6 py-3 transition-all duration-300 shadow-lg hover:shadow-xl transform hover:-translate-y-0.5 flex items-center gap-3"
              >
                Nouveau Message
                <svg class="w-4 h-4 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                </svg>
              </button>
            </div>
  
            <!-- Actions rapides -->
            <div class="flex gap-4">
              <div class="group cursor-pointer">
                <div class="w-16 h-16 bg-white/15 backdrop-blur-sm rounded-2xl flex items-center justify-center shadow-lg hover:bg-white/25 transition-all duration-300 hover:scale-105">
                  <span class="text-2xl">💬</span>
                </div>
                <p class="text-xs text-emerald-200 text-center mt-2">Chat</p>
              </div>
              <div class="group cursor-pointer">
                <div class="w-16 h-16 bg-white/15 backdrop-blur-sm rounded-2xl flex items-center justify-center shadow-lg hover:bg-white/25 transition-all duration-300 hover:scale-105">
                  <span class="text-2xl">📧</span>
                </div>
                <p class="text-xs text-emerald-200 text-center mt-2">Email</p>
              </div>
              <div class="group cursor-pointer">
                <div class="w-16 h-16 bg-white/15 backdrop-blur-sm rounded-2xl flex items-center justify-center shadow-lg hover:bg-white/25 transition-all duration-300 hover:scale-105">
                  <span class="text-2xl">🔔</span>
                </div>
                <p class="text-xs text-emerald-200 text-center mt-2">Notifications</p>
              </div>
            </div>
          </div>
        </div>
      </div>
  
      <!-- Stats Cards redesignées -->
      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-6 mb-8">
        <!-- Sélecteur de période -->
        <div class="bg-white rounded-2xl p-6 shadow-lg border border-gray-100 hover:shadow-xl transition-all duration-300">
          <div class="flex items-center justify-center mb-3">
            <div class="w-10 h-10 bg-orange/10 rounded-xl flex items-center justify-center">
              <svg class="w-5 h-5 text-orange" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
              </svg>
            </div>
          </div>
          <p class="text-sm text-gray-600 text-center mb-2">Période</p>
          <select 
            v-model="selectedPeriod" 
            @change="updateStats"
            class="w-full text-sm font-semibold text-gray-900 bg-transparent border border-gray-200 rounded-lg px-3 py-2 focus:ring-2 focus:ring-orange focus:border-orange cursor-pointer"
          >
            <option value="all">Tout</option>
            <option value="today">Aujourd'hui</option>
            <option value="week">Cette semaine</option>
            <option value="month">Ce mois</option>
          </select>
        </div>
  
        <!-- Total Messages -->
        <div class="bg-white rounded-2xl p-6 shadow-lg border border-gray-100 hover:shadow-xl transition-all duration-300 group">
          <div class="flex items-center justify-between mb-3">
            <div class="w-10 h-10 bg-blue-100 rounded-xl flex items-center justify-center group-hover:scale-110 transition-transform">
              <svg class="w-5 h-5 text-blue-600" fill="currentColor" viewBox="0 0 20 20">
                <path d="M2.003 5.884L10 9.882l7.997-3.998A2 2 0 0016 4H4a2 2 0 00-1.997 1.884z"/>
                <path d="M18 8.118l-8 4-8-4V14a2 2 0 002 2h12a2 2 0 002-2V8.118z"/>
              </svg>
            </div>
            <span class="text-xs text-green-600 bg-green-100 px-2 py-1 rounded-full font-medium">+{{ currentStats.newMessages }}</span>
          </div>
          <p class="text-sm text-gray-600 mb-1">Total Messages</p>
          <p class="text-2xl font-bold text-gray-900">{{ currentStats.totalMessages }}</p>
        </div>
  
        <!-- Messages Non Lus -->
        <div class="bg-white rounded-2xl p-6 shadow-lg border border-gray-100 hover:shadow-xl transition-all duration-300 group">
          <div class="flex items-center justify-between mb-3">
            <div class="w-10 h-10 bg-orange-100 rounded-xl flex items-center justify-center group-hover:scale-110 transition-transform">
              <svg class="w-5 h-5 text-orange-600" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M18 5v8a2 2 0 01-2 2h-5l-5 4v-4H4a2 2 0 01-2-2V5a2 2 0 012-2h12a2 2 0 012 2zM7 8H5v2h2V8zm2 0h2v2H9V8zm6 0h-2v2h2V8z" clip-rule="evenodd"/>
              </svg>
            </div>
            <span class="text-xs text-orange-600 bg-orange-100 px-2 py-1 rounded-full font-medium">{{ currentStats.unreadPercentage }}%</span>
          </div>
          <p class="text-sm text-gray-600 mb-1">Non Lus</p>
          <p class="text-2xl font-bold text-orange">{{ currentStats.unreadMessages }}</p>
        </div>
  
        <!-- Temps de Réponse -->
        <div class="bg-white rounded-2xl p-6 shadow-lg border border-gray-100 hover:shadow-xl transition-all duration-300 group">
          <div class="flex items-center justify-between mb-3">
            <div class="w-10 h-10 bg-purple-100 rounded-xl flex items-center justify-center group-hover:scale-110 transition-transform">
              <svg class="w-5 h-5 text-purple-600" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"/>
              </svg>
            </div>
            <span class="text-xs text-blue-600 bg-blue-100 px-2 py-1 rounded-full font-medium">-15min</span>
          </div>
          <p class="text-sm text-gray-600 mb-1">Temps Réponse</p>
          <p class="text-2xl font-bold text-gray-900">{{ currentStats.avgResponseTime }}</p>
        </div>
  
        <!-- Satisfaction -->
        <div class="bg-white rounded-2xl p-6 shadow-lg border border-gray-100 hover:shadow-xl transition-all duration-300 group">
          <div class="flex items-center justify-between mb-3">
            <div class="w-10 h-10 bg-green-100 rounded-xl flex items-center justify-center group-hover:scale-110 transition-transform">
              <svg class="w-5 h-5 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                <path d="M2 10.5a1.5 1.5 0 113 0v6a1.5 1.5 0 01-3 0v-6zM6 10.333v5.43a2 2 0 001.106 1.79l.05.025A4 4 0 008.943 18h5.416a2 2 0 001.962-1.608l1.2-6A2 2 0 0015.56 8H12V4a2 2 0 00-2-2 1 1 0 00-1 1v.667a4 4 0 01-.8 2.4L6.8 7.933a4 4 0 00-.8 2.4z"/>
              </svg>
            </div>
            <span class="text-xs text-green-600 bg-green-100 px-2 py-1 rounded-full font-medium">+2%</span>
          </div>
          <p class="text-sm text-gray-600 mb-1">Satisfaction</p>
          <p class="text-2xl font-bold text-gray-900">{{ currentStats.satisfaction }}%</p>
        </div>
      </div>
  
      <!-- Messages Interface redesignée -->
      <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Liste des conversations -->
        <div class="lg:col-span-1">
          <div class="bg-white rounded-2xl shadow-lg border border-gray-100 overflow-hidden">
            <!-- Header avec filtres -->
            <div class="px-6 py-4 border-b border-gray-100 bg-gray-50/50">
              <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold text-gray-900">Conversations</h3>
                <button 
                  @click="markAllAsRead"
                  class="text-sm text-orange hover:text-orange-600 font-medium flex items-center gap-1"
                >
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                  </svg>
                  Tout marquer lu
                </button>
              </div>
              
              <!-- Recherche -->
              <div class="relative mb-4">
                <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                  <svg class="w-4 h-4 text-gray-500" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z" clip-rule="evenodd"/>
                  </svg>
                </div>
                <input 
                  v-model="searchQuery"
                  type="text" 
                  class="w-full pl-10 pr-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:ring-2 focus:ring-orange focus:border-orange transition-all" 
                  placeholder="Rechercher une conversation..."
                >
              </div>
  
              <!-- Filtres -->
              <div class="flex flex-wrap gap-2">
                <button 
                  v-for="filter in messageFilters" 
                  :key="filter.value"
                  @click="activeMessageFilter = filter.value"
                  :class="[
                    'px-3 py-2 text-sm font-medium rounded-xl transition-all duration-200',
                    activeMessageFilter === filter.value 
                      ? 'bg-orange text-white shadow-lg' 
                      : 'text-gray-600 hover:text-orange hover:bg-orange/10'
                  ]"
                >
                  {{ filter.label }}
                  <span v-if="filter.count" class="ml-1">({{ filter.count }})</span>
                </button>
              </div>
            </div>
  
            <!-- Liste des conversations -->
            <div class="max-h-[500px] overflow-y-auto">
              <div 
                v-for="conversation in filteredConversations" 
                :key="conversation.id"
                @click="selectConversation(conversation)"
                :class="[
                  'p-4 border-b border-gray-100 cursor-pointer transition-all hover:bg-gray-50',
                  selectedConversation?.id === conversation.id ? 'bg-orange-50 border-l-4 border-l-orange' : ''
                ]"
              >
                <div class="flex items-start space-x-3">
                  <div class="relative">
                    <div class="w-12 h-12 bg-gradient-to-br from-gray-200 to-gray-300 rounded-xl flex items-center justify-center shadow-sm">
                      <span class="text-base font-semibold text-gray-700">{{ conversation.customer.charAt(0) }}</span>
                    </div>
                    <div v-if="conversation.unread" class="absolute -top-1 -right-1 w-3 h-3 bg-orange rounded-full border-2 border-white"></div>
                  </div>
                  <div class="flex-1 min-w-0">
                    <div class="flex items-center justify-between">
                      <p :class="['text-sm font-medium', conversation.unread ? 'text-gray-900' : 'text-gray-700']">
                        {{ conversation.customer }}
                      </p>
                      <p class="text-xs text-gray-500">{{ formatTime(conversation.lastMessage.time) }}</p>
                    </div>
                    <p :class="['text-sm mt-1 truncate', conversation.unread ? 'text-gray-900 font-medium' : 'text-gray-500']">
                      {{ conversation.lastMessage.text }}
                    </p>
                    <div class="flex items-center mt-2 space-x-2">
                      <span :class="getStatusClass(conversation.status)" class="px-2.5 py-1 text-xs font-medium rounded-full">
                        {{ conversation.status }}
                      </span>
                      <span v-if="conversation.priority === 'high'" class="px-2.5 py-1 text-xs font-medium bg-red-100 text-red-800 rounded-full">
                        Urgent
                      </span>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
  
        <!-- Zone de chat -->
        <div class="lg:col-span-2">
          <div class="bg-white rounded-2xl shadow-lg border border-gray-100 overflow-hidden h-[600px] flex flex-col">
            <div v-if="selectedConversation">
              <!-- Header du chat -->
              <div class="px-6 py-4 border-b border-gray-100 bg-gray-50/50">
                <div class="flex items-center justify-between">
                  <div class="flex items-center space-x-3">
                    <div class="w-12 h-12 bg-gradient-to-br from-gray-200 to-gray-300 rounded-xl flex items-center justify-center shadow-sm">
                      <span class="text-base font-semibold text-gray-700">{{ selectedConversation.customer.charAt(0) }}</span>
                    </div>
                    <div>
                      <h4 class="text-lg font-semibold text-gray-900">{{ selectedConversation.customer }}</h4>
                      <p class="text-sm text-gray-500">{{ selectedConversation.email }}</p>
                    </div>
                  </div>
                  <div class="flex items-center space-x-2">
                    <button class="p-2 text-gray-400 hover:text-gray-600 rounded-lg hover:bg-gray-100 transition-colors">
                      <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path>
                      </svg>
                    </button>
                    <button class="p-2 text-gray-400 hover:text-gray-600 rounded-lg hover:bg-gray-100 transition-colors">
                      <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                      </svg>
                    </button>
                    <button 
                      @click="toggleConversationStatus"
                      :class="[
                        'px-4 py-2 text-sm font-medium rounded-xl transition-all',
                        selectedConversation.status === 'Ouvert' 
                          ? 'bg-red-100 text-red-800 hover:bg-red-200' 
                          : 'bg-green-100 text-green-800 hover:bg-green-200'
                      ]"
                    >
                      {{ selectedConversation.status === 'Ouvert' ? 'Fermer' : 'Rouvrir' }}
                    </button>
                  </div>
                </div>
              </div>
  
              <!-- Messages -->
              <div class="flex-1 p-6 space-y-6 overflow-y-auto bg-gray-50/30">
                <div 
                  v-for="message in selectedConversation.messages" 
                  :key="message.id"
                  :class="[
                    'flex',
                    message.sender === 'client' ? 'justify-start' : 'justify-end'
                  ]"
                >
                  <div :class="[
                    'max-w-xs lg:max-w-md px-5 py-3 rounded-2xl shadow-sm',
                    message.sender === 'client' 
                      ? 'bg-white text-gray-900 border border-gray-100' 
                      : 'bg-gradient-to-r from-orange to-orange-600 text-white'
                  ]">
                    <p class="text-sm">{{ message.text }}</p>
                    <p :class="[
                      'text-xs mt-2 text-right',
                      message.sender === 'client' ? 'text-gray-500' : 'text-orange-100'
                    ]">
                      {{ formatTime(message.time) }}
                    </p>
                  </div>
                </div>
              </div>
  
              <!-- Zone de saisie -->
              <div class="px-6 py-4 border-t border-gray-100 bg-white">
                <div class="flex items-center space-x-3">
                  <button class="p-2 text-gray-400 hover:text-gray-600 rounded-lg hover:bg-gray-100 transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"></path>
                    </svg>
                  </button>
                  <div class="relative flex-1">
                    <input 
                      v-model="newMessage"
                      @keypress.enter="sendMessage"
                      type="text" 
                      placeholder="Tapez votre message..."
                      class="w-full border border-gray-200 rounded-xl px-4 py-3 focus:ring-2 focus:ring-orange focus:border-orange pr-12"
                    >
                    <button 
                      @click="sendMessage"
                      :disabled="!newMessage.trim()"
                      class="absolute right-2 top-1/2 -translate-y-1/2 p-2 bg-orange text-white rounded-lg hover:bg-orange-600 transition-colors disabled:opacity-50 disabled:cursor-not-allowed"
                    >
                      <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path>
                      </svg>
                    </button>
                  </div>
                </div>
              </div>
            </div>
            
            <!-- État vide -->
            <div v-else class="flex items-center justify-center h-full">
              <div class="text-center p-8">
                <div class="w-20 h-20 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                  <svg class="w-10 h-10 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                  </svg>
                </div>
                <h3 class="text-xl font-semibold text-gray-900 mb-2">Aucune conversation sélectionnée</h3>
                <p class="text-gray-500 mb-6">Choisissez une conversation dans la liste pour commencer à discuter</p>
                <button 
                  @click="showNewMessageModal = true"
                  class="px-5 py-2.5 bg-orange text-white rounded-xl hover:bg-orange-600 transition-colors shadow-md hover:shadow-lg"
                >
                  Démarrer une nouvelle conversation
                </button>
              </div>
            </div>
          </div>
        </div>
      </div>
  
      <!-- Modal nouveau message -->
      <NewMessageModal 
        v-if="showNewMessageModal" 
        @close="showNewMessageModal = false"
        @send="handleNewMessage"
      />
  
      <!-- Notification Toast -->
      <div v-if="notification.show" class="fixed bottom-4 right-4 z-50 bg-gradient-to-r from-green-500 to-green-600 text-white py-4 px-6 rounded-xl shadow-2xl transition-all duration-300 transform" :class="{'opacity-0 translate-y-2': !notification.show}">
        <div class="flex items-center gap-3">
          <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
          </svg>
          {{ notification.message }}
        </div>
      </div>
    </main>
  </template>
  
  <script setup>
  import { ref, computed, onMounted } from 'vue'
  import NewMessageModal from './NewMessageModal.vue'
  
  // États réactifs
  const showNewMessageModal = ref(false)
  const selectedPeriod = ref('today')
  const searchQuery = ref('')
  const activeMessageFilter = ref('all')
  const selectedConversation = ref(null)
  const newMessage = ref('')
  const notification = ref({ show: false, message: '' })
  
  // Statistiques
  const stats = ref({
    all: { totalMessages: 1250, unreadMessages: 45, newMessages: 12, avgResponseTime: '2h 15min', satisfaction: 94, unreadPercentage: 3.6 },
    today: { totalMessages: 28, unreadMessages: 8, newMessages: 5, avgResponseTime: '45min', satisfaction: 96, unreadPercentage: 28.6 },
    week: { totalMessages: 156, unreadMessages: 23, newMessages: 18, avgResponseTime: '1h 30min', satisfaction: 95, unreadPercentage: 14.7 },
    month: { totalMessages: 642, unreadMessages: 45, newMessages: 67, avgResponseTime: '2h 15min', satisfaction: 94, unreadPercentage: 7.0 }
  })
  
  const currentStats = computed(() => stats.value[selectedPeriod.value])
  
  // Filtres de messages
  const messageFilters = [
    { value: 'all', label: 'Tous', count: 28 },
    { value: 'unread', label: 'Non lus', count: 8 },
    { value: 'urgent', label: 'Urgent', count: 3 },
    { value: 'closed', label: 'Fermés', count: 12 }
  ]
  
  // Conversations
  const conversations = ref([
    {
      id: 1,
      customer: 'Marie Dubois',
      email: 'marie.dubois@email.com',
      unread: true,
      status: 'Ouvert',
      priority: 'high',
      lastMessage: {
        text: 'Bonjour, j\'ai un problème avec ma commande...',
        time: '2024-01-15T10:30:00'
      },
      messages: [
        { id: 1, sender: 'client', text: 'Bonjour, j\'ai un problème avec ma commande #12345', time: '2024-01-15T10:30:00' },
        { id: 2, sender: 'admin', text: 'Bonjour Marie, je vais vérifier votre commande immédiatement.', time: '2024-01-15T10:32:00' },
        { id: 3, sender: 'client', text: 'Merci beaucoup pour votre réactivité !', time: '2024-01-15T10:35:00' }
      ]
    },
    {
      id: 2,
      customer: 'Jean Martin',
      email: 'jean.martin@email.com',
      unread: false,
      status: 'Fermé',
      priority: 'normal',
      lastMessage: {
        text: 'Parfait, merci pour votre aide !',
        time: '2024-01-15T09:15:00'
      },
      messages: [
        { id: 1, sender: 'client', text: 'Quand sera expédiée ma commande ?', time: '2024-01-15T09:00:00' },
        { id: 2, sender: 'admin', text: 'Votre commande sera expédiée demain matin.', time: '2024-01-15T09:10:00' },
        { id: 3, sender: 'client', text: 'Parfait, merci pour votre aide !', time: '2024-01-15T09:15:00' }
      ]
    },
    {
      id: 3,
      customer: 'Sophie Laurent',
      email: 'sophie.laurent@email.com',
      unread: true,
      status: 'Ouvert',
      priority: 'normal',
      lastMessage: {
        text: 'Pouvez-vous me confirmer les tailles disponibles ?',
        time: '2024-01-15T08:45:00'
      },
      messages: [
        { id: 1, sender: 'client', text: 'Pouvez-vous me confirmer les tailles disponibles pour le T-shirt bleu ?', time: '2024-01-15T08:45:00' }
      ]
    },
    {
      id: 4,
      customer: 'Pierre Durand',
      email: 'pierre.durand@email.com',
      unread: true,
      status: 'Ouvert',
      priority: 'high',
      lastMessage: {
        text: 'URGENT: Problème de livraison',
        time: '2024-01-15T08:20:00'
      },
      messages: [
        { id: 1, sender: 'client', text: 'URGENT: Ma commande devait arriver hier mais je n\'ai rien reçu !', time: '2024-01-15T08:20:00' }
      ]
    }
  ])
  
  // Conversations filtrées
  const filteredConversations = computed(() => {
    let filtered = conversations.value
  
    // Filtre par statut
    if (activeMessageFilter.value !== 'all') {
      switch (activeMessageFilter.value) {
        case 'unread':
          filtered = filtered.filter(conv => conv.unread)
          break
        case 'urgent':
          filtered = filtered.filter(conv => conv.priority === 'high')
          break
        case 'closed':
          filtered = filtered.filter(conv => conv.status === 'Fermé')
          break
      }
    }
  
    // Filtre par recherche
    if (searchQuery.value) {
      const query = searchQuery.value.toLowerCase()
      filtered = filtered.filter(conv => 
        conv.customer.toLowerCase().includes(query) ||
        conv.lastMessage.text.toLowerCase().includes(query)
      )
    }
  
    return filtered
  })
  
  // Fonctions
  const updateStats = () => {
    console.log('Mise à jour des statistiques pour:', selectedPeriod.value)
  }
  
  const selectConversation = (conversation) => {
    selectedConversation.value = conversation
    if (conversation.unread) {
      conversation.unread = false
    }
  }
  
  const getStatusClass = (status) => {
    switch (status) {
      case 'Ouvert':
        return 'bg-green-100 text-green-800 border border-green-200'
      case 'Fermé':
        return 'bg-gray-100 text-gray-800 border border-gray-200'
      case 'En attente':
        return 'bg-yellow-100 text-yellow-800 border border-yellow-200'
      default:
        return 'bg-gray-100 text-gray-800 border border-gray-200'
    }
  }
  
  const formatTime = (timeString) => {
    const date = new Date(timeString)
    const now = new Date()
    const diffInHours = Math.floor((now - date) / (1000 * 60 * 60))
    
    if (diffInHours < 1) {
      const diffInMinutes = Math.floor((now - date) / (1000 * 60))
      return `${diffInMinutes}min`
    } else if (diffInHours < 24) {
      return `${diffInHours}h`
    } else {
      return date.toLocaleDateString('fr-FR')
    }
  }
  
  const sendMessage = () => {
    if (!newMessage.value.trim() || !selectedConversation.value) return
  
    const message = {
      id: Date.now(),
      sender: 'admin',
      text: newMessage.value,
      time: new Date().toISOString()
    }
  
    selectedConversation.value.messages.push(message)
    selectedConversation.value.lastMessage = {
      text: newMessage.value,
      time: message.time
    }
  
    newMessage.value = ''
    showNotification('Message envoyé avec succès!')
  }
  
  const toggleConversationStatus = () => {
    if (selectedConversation.value) {
      selectedConversation.value.status = selectedConversation.value.status === 'Ouvert' ? 'Fermé' : 'Ouvert'
      showNotification(`Conversation ${selectedConversation.value.status.toLowerCase()}e`)
    }
  }
  
  const markAllAsRead = () => {
    conversations.value.forEach(conv => {
      conv.unread = false
    })
    showNotification('Toutes les conversations marquées comme lues')
  }
  
  const handleNewMessage = (messageData) => {
    console.log('Nouveau message:', messageData)
    showNewMessageModal.value = false
    showNotification('Message envoyé avec succès!')
  }
  
  const showNotification = (message) => {
    notification.value.message = message
    notification.value.show = true
    setTimeout(() => {
      notification.value.show = false
    }, 3000)
  }
  
  onMounted(() => {
    // Sélectionner la première conversation par défaut
    if (conversations.value.length > 0) {
      selectConversation(conversations.value[0])
    }
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
  
  .border-l-orange {
    border-left-color: #F65A11;
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
  
  .bg-orange-50 {
    background-color: rgba(246, 90, 17, 0.05);
  }
  
  .text-orange-100 {
    color: rgba(255, 255, 255, 0.9);
  }
  
  .text-orange-300 {
    color: #FF8A50;
  }
  
  .text-emerald-100 {
    color: #D1FAE5;
  }
  
  .text-emerald-200 {
    color: #A7F3D0;
  }
  
  /* Gradients personnalisés */
  .bg-gradient-to-br {
    background-image: linear-gradient(to bottom right, var(--tw-gradient-stops));
  }
  
  .bg-gradient-to-r {
    background-image: linear-gradient(to right, var(--tw-gradient-stops));
  }
  
  /* Effets de glassmorphism */
  .backdrop-blur-sm {
    backdrop-filter: blur(4px);
  }
  
  /* Ombres personnalisées */
  .shadow-2xl {
    box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
  }
  
  /* Transitions fluides */
  .transition-all {
    transition-property: all;
    transition-timing-function: cubic-bezier(0.4, 0, 0.2, 1);
    transition-duration: 300ms;
  }
  
  /* Hover effects */
  .hover\:scale-105:hover {
    transform: scale(1.05);
  }
  
  .hover\:-translate-y-0\.5:hover {
    transform: translateY(-2px);
  }
  </style>
  