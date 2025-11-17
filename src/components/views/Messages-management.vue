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

    <div class="w-full max-w-[1650px] mx-auto px-4 sm:px-6 py-4 sm:py-8 relative z-10">
      <Navbar/>
      
      <div class="flex items-center text-sm text-gray-500 mb-4 sm:mb-6">
        <router-link to="/" class="hover:text-gray-700">
          <svg class="w-4 h-4 sm:w-5 sm:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
          </svg>
        </router-link>
        <span class="mx-2">/</span>
        <span class="font-medium text-gray-700 truncate">Message Management</span>
      </div>

      <div class="flex flex-col space-y-4 lg:flex-row lg:items-center lg:justify-between lg:space-y-0 mb-6 sm:mb-8">
        <div>
          <h1 class="text-2xl sm:text-3xl font-bold text-gray-900 mb-2">Message Management</h1>
          <p class="text-sm sm:text-base text-gray-600">Suivi et réponses aux messages clients en temps réel</p>
        </div>
        
        <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-2 sm:gap-3">
          <button @click="refreshMessages" class="submit-btn">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
            </svg>
            <span>Refresh</span>
          </button>
        </div>
      </div>

      <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-6 mb-6 sm:mb-8">
        <div class="bg-white overflow-hidden shadow-lg rounded-lg border border-gray-100 hover:shadow-xl transition-all duration-300">
          <div class="px-4 sm:px-6 py-4 sm:py-6">
            <div class="flex items-center mb-3 sm:mb-4">
              <div class="w-10 h-10 sm:w-12 sm:h-12 bg-gradient-to-br from-orange-200 to-orange-300 rounded-lg flex items-center justify-center mr-3 sm:mr-4 flex-shrink-0">
                <svg class="w-5 h-5 text-black" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z" />
                </svg>
              </div>
              <div class="min-w-0">
                <p class="text-xs sm:text-sm text-gray-600">Total Messages</p>
                <p class="text-xl sm:text-2xl font-bold text-gray-900">{{ stats.total_messages }}</p>
              </div>
            </div>
            <div class="space-y-2">
              <div class="flex justify-between text-xs text-gray-500">
                <span class="truncate">Croissance: +12%</span>
                <span class="text-orange-600 flex-shrink-0">Ce mois</span>
              </div>
              <div class="w-full bg-gray-200 rounded-full h-2">
                <div class="bg-gradient-to-r from-orange-500 to-orange-600 h-2 rounded-full transition-all duration-300" style="width: 75%"></div>
              </div>
            </div>
          </div>
        </div>

        <div class="bg-white overflow-hidden shadow-lg rounded-lg border border-gray-100 hover:shadow-xl transition-all duration-300">
          <div class="px-4 sm:px-6 py-4 sm:py-6">
            <div class="flex items-center mb-3 sm:mb-4">
              <div class="w-10 h-10 sm:w-12 sm:h-12 bg-gradient-to-br from-yellow-200 to-yellow-300 rounded-lg flex items-center justify-center mr-3 sm:mr-4 flex-shrink-0">
                <svg class="w-5 h-5 text-black" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                </svg>
              </div>
              <div class="min-w-0">
                <p class="text-xs sm:text-sm text-gray-600">Non Lus</p>
                <p class="text-xl sm:text-2xl font-bold text-gray-900">{{ stats.unread_messages }}</p>
              </div>
            </div>
            <div class="space-y-2">
              <div class="flex justify-between text-xs text-gray-500">
                <span class="truncate">Réduction: -8%</span>
                <span class="text-yellow-600 flex-shrink-0">vs hier</span>
              </div>
              <div class="w-full bg-gray-200 rounded-full h-2">
                <div class="bg-gradient-to-r from-yellow-500 to-yellow-600 h-2 rounded-full transition-all duration-300" style="width: 60%"></div>
              </div>
            </div>
          </div>
        </div>

        <div class="bg-white overflow-hidden shadow-lg rounded-lg border border-gray-100 hover:shadow-xl transition-all duration-300">
          <div class="px-4 sm:px-6 py-4 sm:py-6">
            <div class="flex items-center mb-3 sm:mb-4">
              <div class="w-10 h-10 sm:w-12 sm:h-12 bg-gradient-to-br from-green-200 to-green-300 rounded-lg flex items-center justify-center mr-3 sm:mr-4 flex-shrink-0">
                <svg class="w-5 h-5 text-black" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8h2a2 2 0 012 2v6a2 2 0 01-2 2h-2v4l-4-4H9a1.994 1.994 0 01-1.414-.586m0 0L11 14h4a2 2 0 002-2V6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2v4l.586-.586z" />
                </svg>
              </div>
              <div class="min-w-0">
                <p class="text-xs sm:text-sm text-gray-600">Conversations Actives</p>
                <p class="text-xl sm:text-2xl font-bold text-gray-900">{{ stats.active_conversations }}</p>
              </div>
            </div>
            <div class="space-y-2">
              <div class="flex justify-between text-xs text-gray-500">
                <span class="truncate">Croissance: +15%</span>
                <span class="text-green-600 flex-shrink-0">Aujourd'hui</span>
              </div>
              <div class="w-full bg-gray-200 rounded-full h-2">
                <div class="bg-gradient-to-r from-green-500 to-green-600 h-2 rounded-full transition-all duration-300" style="width: 85%"></div>
              </div>
            </div>
          </div>
        </div>

        <div class="bg-white overflow-hidden shadow-lg rounded-lg border border-gray-100 hover:shadow-xl transition-all duration-300">
          <div class="px-4 sm:px-6 py-4 sm:py-6">
            <div class="flex items-center mb-3 sm:mb-4">
              <div class="w-10 h-10 sm:w-12 sm:h-12 bg-gradient-to-br from-purple-200 to-purple-300 rounded-lg flex items-center justify-center mr-3 sm:mr-4 flex-shrink-0">
                <svg class="w-5 h-5 text-black" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
              </div>
              <div class="min-w-0">
                <p class="text-xs sm:text-sm text-gray-600">Temps de Réponse Moy.</p>
                <p class="text-xl sm:text-2xl font-bold text-gray-900">{{ stats.avg_response_time }}</p>
              </div>
            </div>
            <div class="space-y-2">
              <div class="flex justify-between text-xs text-gray-500">
                <span class="truncate">Amélioration: -10%</span>
                <span class="text-purple-600 flex-shrink-0">vs hier</span>
              </div>
              <div class="w-full bg-gray-200 rounded-full h-2">
                <div class="bg-gradient-to-r from-purple-500 to-purple-600 h-2 rounded-full transition-all duration-300" style="width: 70%"></div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Remplacer le tableau par un layout à deux colonnes -->
      <div class="bg-white shadow-lg rounded-lg overflow-hidden border border-gray-200 flex" style="height: calc(100vh - 400px); min-height: 600px;">
        <!-- Sidebar des conversations -->
        <div class="w-full md:w-96 border-r border-gray-200 flex flex-col bg-gray-50">
          <!-- Filtres et recherche -->
          <div class="px-4 py-3 border-b border-gray-200 bg-white">
            <div class="flex flex-wrap gap-1 mb-3">
              <button 
                v-for="tab in filterTabs" 
                :key="tab.value"
                @click="handleFilterChange(tab.value)"
                :class="[
                  'px-3 py-1.5 rounded-lg transition-all duration-200 text-xs font-medium',
                  activeFilter === tab.value 
                    ? 'btn-degrade-orange' 
                    : 'btn-gray'
                ]"
              >
                {{ tab.label }} ({{ filterCounts[tab.value] }})
              </button>
            </div>
            
            <div class="relative mb-2">
              <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                </svg>
              </div>
              <input 
                v-model="searchQuery"
                type="text" 
                class="w-full pl-10 pr-3 py-2 bg-gray-50 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-orange-500 text-gray-800"
                placeholder="Rechercher..."
              >
            </div>

            <div class="flex gap-2">
              <select 
                v-model="boutiqueFilter" 
                class="flex-1 px-2 py-1.5 bg-gray-50 border border-gray-200 rounded-lg text-xs focus:outline-none focus:ring-2 focus:ring-orange-500 text-gray-800">
                <option value="">Toutes boutiques</option>
                <option v-for="boutique in boutiques" :key="boutique.id" :value="boutique.id">
                  {{ boutique.name }}
                </option>
              </select>
              <select 
                v-model="dateFilter" 
                class="flex-1 px-2 py-1.5 bg-gray-50 border border-gray-200 rounded-lg text-xs focus:outline-none focus:ring-2 focus:ring-orange-500 text-gray-800">
                <option value="">Toutes dates</option>
                <option value="today">Aujourd'hui</option>
                <option value="yesterday">Hier</option>
                <option value="week">Cette semaine</option>
                <option value="month">Ce mois</option>
              </select>
            </div>
          </div>

          <!-- Liste des conversations -->
          <div class="flex-1 overflow-y-auto">
            <div
              v-for="conversation in filteredConversations"
              :key="conversation.id"
              @click="selectConversation(conversation)"
              :class="[
                'flex items-center gap-3 p-4 cursor-pointer transition-colors border-b border-gray-100',
                selectedConversation?.id === conversation.id ? 'bg-orange-50 border-l-4 border-l-orange-500' : 'hover:bg-gray-100'
              ]"
            >
              <div class="relative flex-shrink-0">
                <img :src="conversation.client_avatar" :alt="conversation.client_name" class="w-12 h-12 rounded-full object-cover" />
                <img :src="conversation.boutique_logo" :alt="conversation.boutique_name" class="absolute -bottom-1 -right-1 w-5 h-5 rounded-full object-cover border-2 border-white" />
              </div>
              <div class="flex-1 min-w-0">
                <div class="flex items-center justify-between mb-1">
                  <h4 class="font-semibold text-sm text-gray-800 truncate">{{ conversation.client_name }}</h4>
                  <span class="text-xs text-gray-500 flex-shrink-0 ml-2">{{ formatTime(conversation.last_message_date) }}</span>
                </div>
                <p class="text-xs text-gray-600 truncate mb-1">{{ conversation.boutique_name }}</p>
                <p class="text-xs text-gray-500 truncate">{{ conversation.last_message }}</p>
              </div>
              <div class="flex flex-col items-end gap-1 flex-shrink-0">
                <span v-if="conversation.unread_count > 0" class="bg-orange-500 text-white text-xs font-bold rounded-full w-5 h-5 flex items-center justify-center">
                  {{ conversation.unread_count }}
                </span>
                <span :class="['w-2 h-2 rounded-full', getStatusDotClass(conversation.status)]"></span>
              </div>
            </div>
          </div>
        </div>

        <!-- Zone de conversation -->
        <div class="flex-1 flex flex-col bg-white">
          <div v-if="!selectedConversation" class="flex-1 flex flex-col items-center justify-center text-gray-400">
            <svg class="w-16 h-16 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
            </svg>
            <p class="text-lg font-medium">Sélectionnez une conversation</p>
            <p class="text-sm">Choisissez une conversation pour voir les messages</p>
          </div>

          <template v-else>
            <!-- En-tête de la conversation -->
            <div class="flex items-center justify-between px-6 py-4 border-b border-gray-200" style="background: linear-gradient(160deg, #0c0c0c, #fc4618, #0c0c0c)">
              <div class="flex items-center gap-3 text-white">
                <img :src="selectedConversation.client_avatar" :alt="selectedConversation.client_name" class="w-10 h-10 rounded-full object-cover" />
                <div>
                  <h3 class="font-semibold text-base">{{ selectedConversation.client_name }}</h3>
                  <p class="text-xs opacity-90">{{ selectedConversation.boutique_name }} • {{ selectedConversation.client_phone }}</p>
                </div>
              </div>
              <div class="flex items-center gap-2">
                <button 
                  v-if="selectedConversation.status === 'unread'"
                  @click="markAsRead(selectedConversation.id)"
                  class="p-2 hover:bg-white/20 rounded-lg transition-colors text-white"
                  title="Marquer comme lu"
                >
                  <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                  </svg>
                </button>
                <button 
                  @click="archiveConversation(selectedConversation.id)"
                  class="p-2 hover:bg-white/20 rounded-lg transition-colors text-white"
                  title="Archiver"
                >
                  <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4" />
                  </svg>
                </button>
              </div>
            </div>

            <!-- Messages -->
            <div ref="messagesContainer" class="flex-1 overflow-y-auto p-6 space-y-4 bg-gray-50">
              <div v-for="message in selectedConversation.messages" :key="message.id" :class="['flex', message.sender === 'client' ? 'justify-start' : 'justify-end']">
                <div :class="['max-w-[70%] rounded-2xl px-4 py-2', message.sender === 'client' ? 'bg-white text-gray-800 shadow-sm' : 'text-white']" :style="message.sender !== 'client' ? 'background: linear-gradient(160deg, #fe9700, #fc4618)' : ''">
                  <p class="text-sm leading-relaxed">{{ message.text }}</p>
                  <span class="text-xs opacity-70 mt-1 block">{{ formatTime(message.timestamp) }}</span>
                </div>
              </div>
            </div>

            <!-- Zone de saisie -->
            <div class="border-t border-gray-200 p-4 bg-white">
              <div class="flex items-center gap-2">
                <input 
                  v-model="newMessage"
                  @keypress.enter="sendMessage"
                  type="text" 
                  placeholder="Tapez votre message..."
                  class="flex-1 px-4 py-3 bg-gray-50 border border-gray-200 rounded-full text-sm focus:outline-none focus:ring-2 focus:ring-orange-500 text-gray-800"
                />
                <button 
                  @click="sendMessage"
                  :disabled="!newMessage.trim()"
                  class="w-12 h-12 rounded-full flex items-center justify-center transition-transform hover:scale-105 disabled:opacity-50 disabled:cursor-not-allowed"
                  style="background: linear-gradient(160deg, #fe9700, #fc4618)"
                >
                  <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8" />
                  </svg>
                </button>
              </div>
            </div>
          </template>
        </div>
      </div>
    </div>

  </div>
</template>

<script setup lang="ts">
import { ref, computed, nextTick } from 'vue'
import Navbar from '../boutiques/Navbar.vue'

interface Message {
  id: number
  text: string
  sender: 'client' | 'boutique'
  timestamp: Date
}

interface Conversation {
  id: number
  client_name: string
  client_phone: string
  client_avatar: string
  boutique_name: string
  boutique_logo: string
  boutique_id: number
  last_message: string
  last_message_date: Date
  message_count: number
  unread_count: number
  status: 'unread' | 'read' | 'archived'
  messages: Message[]
}

// State
const searchQuery = ref('')
const boutiqueFilter = ref('')
const dateFilter = ref('')
const activeFilter = ref('all')
const currentPage = ref(1)
const itemsPerPage = ref(10)
// const showConversationModal = ref(false) // Removed as modal is replaced by split view
const selectedConversation = ref<Conversation | null>(null)
const newMessage = ref('')
const messagesContainer = ref<HTMLElement | null>(null)

// Stats
const stats = ref({
  total_messages: 156,
  unread_messages: 23,
  active_conversations: 45,
  avg_response_time: '2h 15m'
})

// Boutiques list
const boutiques = ref([
  { id: 1, name: 'Adidas Official' },
  { id: 2, name: 'Nike Store' },
  { id: 3, name: 'Puma Shop' }
])

// Filter tabs
const filterTabs = [
  { value: 'all', label: 'Tous' },
  { value: 'unread', label: 'Non lus' },
  { value: 'read', label: 'Lus' },
  { value: 'archived', label: 'Archivés' }
]

// Mock data - conversations groupées par boutique
const conversations = ref<Conversation[]>([
  {
    id: 1,
    client_name: 'Jean Kouassi',
    client_phone: '+225 07 12 34 56 78',
    client_avatar: 'https://api.dicebear.com/7.x/avataaars/svg?seed=Jean',
    boutique_name: 'Adidas Official',
    boutique_logo: 'https://cdn-icons-png.flaticon.com/512/732/732190.png',
    boutique_id: 1,
    last_message: 'Bonjour, avez-vous des baskets en taille 42?',
    last_message_date: new Date('2024-01-15T14:30:00'),
    message_count: 8,
    unread_count: 2,
    status: 'unread',
    messages: [
      { id: 1, text: 'Bonjour, avez-vous des baskets en taille 42?', sender: 'client', timestamp: new Date('2024-01-15T14:30:00') },
      { id: 2, text: 'Oui, nous avons plusieurs modèles disponibles en taille 42.', sender: 'boutique', timestamp: new Date('2024-01-15T14:32:00') },
      { id: 3, text: 'Quel est le prix?', sender: 'client', timestamp: new Date('2024-01-15T14:35:00') }
    ]
  },
  {
    id: 2,
    client_name: 'Marie Diallo',
    client_phone: '+225 05 98 76 54 32',
    client_avatar: 'https://api.dicebear.com/7.x/avataaars/svg?seed=Marie',
    boutique_name: 'Nike Store',
    boutique_logo: 'https://cdn-icons-png.flaticon.com/512/732/732084.png',
    boutique_id: 2,
    last_message: 'Merci pour votre aide!',
    last_message_date: new Date('2024-01-15T13:20:00'),
    message_count: 5,
    unread_count: 0,
    status: 'read',
    messages: [
      { id: 1, text: 'Bonjour, je cherche des chaussures de running', sender: 'client', timestamp: new Date('2024-01-15T13:00:00') },
      { id: 2, text: 'Nous avons une excellente sélection. Quelle est votre pointure?', sender: 'boutique', timestamp: new Date('2024-01-15T13:05:00') },
      { id: 3, text: 'Merci pour votre aide!', sender: 'client', timestamp: new Date('2024-01-15T13:20:00') }
    ]
  },
  {
    id: 3,
    client_name: 'Koffi Yao',
    client_phone: '+225 01 23 45 67 89',
    client_avatar: 'https://api.dicebear.com/7.x/avataaars/svg?seed=Koffi',
    boutique_name: 'Adidas Official',
    boutique_logo: 'https://cdn-icons-png.flaticon.com/512/732/732190.png',
    boutique_id: 1,
    last_message: 'Livraison possible à Cocody?',
    last_message_date: new Date('2024-01-15T12:45:00'),
    message_count: 12,
    unread_count: 3,
    status: 'unread',
    messages: [
      { id: 1, text: 'Livraison possible à Cocody?', sender: 'client', timestamp: new Date('2024-01-15T12:45:00') },
      { id: 2, text: 'Oui, nous livrons à Cocody sous 24h', sender: 'boutique', timestamp: new Date('2024-01-15T12:50:00') }
    ]
  }
])

// Computed
const filterCounts = computed(() => ({
  all: conversations.value.length,
  unread: conversations.value.filter(c => c.status === 'unread').length,
  read: conversations.value.filter(c => c.status === 'read').length,
  archived: conversations.value.filter(c => c.status === 'archived').length
}))

const filteredConversations = computed(() => {
  let filtered = [...conversations.value]

  // Filter by status
  if (activeFilter.value !== 'all') {
    filtered = filtered.filter(c => c.status === activeFilter.value)
  }

  // Filter by search
  if (searchQuery.value) {
    const query = searchQuery.value.toLowerCase()
    filtered = filtered.filter(c =>
      c.client_name.toLowerCase().includes(query) ||
      c.boutique_name.toLowerCase().includes(query) ||
      c.last_message.toLowerCase().includes(query)
    )
  }

  // Filter by boutique
  if (boutiqueFilter.value) {
    filtered = filtered.filter(c => c.boutique_id === parseInt(boutiqueFilter.value))
  }

  // Filter by date
  if (dateFilter.value) {
    const now = new Date()
    const today = new Date(now.getFullYear(), now.getMonth(), now.getDate())
    
    filtered = filtered.filter(c => {
      const messageDate = new Date(c.last_message_date)
      
      switch (dateFilter.value) {
        case 'today':
          return messageDate >= today
        case 'yesterday':
          const yesterday = new Date(today)
          yesterday.setDate(yesterday.getDate() - 1)
          return messageDate >= yesterday && messageDate < today
        case 'week':
          const weekAgo = new Date(today)
          weekAgo.setDate(weekAgo.getDate() - 7)
          return messageDate >= weekAgo
        case 'month':
          const monthAgo = new Date(today)
          monthAgo.setMonth(monthAgo.getMonth() - 1)
          return messageDate >= monthAgo
        default:
          return true
      }
    })
  }

  return filtered
})

// Removed paginatedConversations and pagination logic as the table is replaced

// const isFirstPage = computed(() => currentPage.value <= 1)
// const isLastPage = computed(() => currentPage.value >= totalPages.value)

const hasActiveFilters = computed(() => {
  return searchQuery.value || boutiqueFilter.value || dateFilter.value || activeFilter.value !== 'all'
})

// Methods
const handleFilterChange = (filterValue: string) => {
  activeFilter.value = filterValue
  currentPage.value = 1
}

// const handleItemsPerPageChange = () => {
//   currentPage.value = 1
// }

const clearFilters = () => {
  searchQuery.value = ''
  boutiqueFilter.value = ''
  dateFilter.value = ''
  activeFilter.value = 'all'
  currentPage.value = 1
}

const refreshMessages = () => {
  // Simulate refresh
  console.log('Refreshing messages...')
}

// Removed openConversation as modal is replaced by split view

const selectConversation = (conversation: Conversation) => {
  selectedConversation.value = conversation
  if (conversation.status === 'unread') {
    markAsRead(conversation.id)
  }
  nextTick(() => {
    scrollToBottom()
  })
}

const sendMessage = () => {
  if (!newMessage.value.trim() || !selectedConversation.value) return

  const message: Message = {
    id: Date.now(),
    text: newMessage.value,
    sender: 'boutique',
    timestamp: new Date()
  }

  selectedConversation.value.messages.push(message)
  selectedConversation.value.last_message = newMessage.value
  selectedConversation.value.last_message_date = new Date()

  const conv = conversations.value.find(c => c.id === selectedConversation.value?.id)
  if (conv) {
    conv.last_message = newMessage.value
    conv.last_message_date = new Date()
  }

  newMessage.value = ''
  scrollToBottom()
}

const scrollToBottom = () => {
  nextTick(() => {
    if (messagesContainer.value) {
      messagesContainer.value.scrollTop = messagesContainer.value.scrollHeight
    }
  })
}

const markAsRead = (conversationId: number) => {
  const conversation = conversations.value.find(c => c.id === conversationId)
  if (conversation) {
    conversation.status = 'read'
    // Update stats only if it was actually unread
    if (conversation.unread_count > 0) {
      stats.value.unread_messages -= conversation.unread_count
      conversation.unread_count = 0
    }
  }
  // If the selected conversation is marked as read, update its status directly
  if (selectedConversation.value?.id === conversationId) {
    selectedConversation.value.status = 'read'
    selectedConversation.value.unread_count = 0
  }
}

const archiveConversation = (conversationId: number) => {
  const conversation = conversations.value.find(c => c.id === conversationId)
  if (conversation) {
    conversation.status = 'archived'
    // Update stats if it was unread
    if (conversation.unread_count > 0) {
      stats.value.unread_messages -= conversation.unread_count
    }
  }
  // If the selected conversation is archived, deselect it
  if (selectedConversation.value?.id === conversationId) {
    selectedConversation.value = null
  }
}

const formatDate = (date: Date) => {
  return new Date(date).toLocaleDateString('fr-FR', {
    day: '2-digit',
    month: 'short',
    year: 'numeric'
  })
}

const formatTime = (date: Date) => {
  return new Date(date).toLocaleTimeString('fr-FR', {
    hour: '2-digit',
    minute: '2-digit'
  })
}

const getStatusLabel = (status: string) => {
  const labels = {
    unread: 'Non lu',
    read: 'Lu',
    archived: 'Archivé'
  }
  return labels[status as keyof typeof labels] || status
}

const getStatusClass = (status: string) => {
  const classes = {
    unread: 'bg-orange-100 text-orange-800',
    read: 'bg-green-100 text-green-800',
    archived: 'bg-gray-100 text-gray-800'
  }
  return classes[status as keyof typeof classes] || 'bg-gray-100 text-gray-800'
}

const getStatusDotClass = (status: string) => {
  const classes = {
    unread: 'bg-orange-500',
    read: 'bg-green-500',
    archived: 'bg-gray-500'
  }
  return classes[status as keyof typeof classes] || 'bg-gray-500'
}
</script>

<style scoped>
@keyframes float-slow {
  0%, 100% { transform: translate(0, 0) rotate(0deg); }
  33% { transform: translate(30px, -30px) rotate(5deg); }
  66% { transform: translate(-20px, 20px) rotate(-5deg); }
}

@keyframes float-reverse {
  0%, 100% { transform: translate(0, 0) rotate(0deg); }
  33% { transform: translate(-30px, 30px) rotate(-5deg); }
  66% { transform: translate(20px, -20px) rotate(5deg); }
}

@keyframes float-diagonal {
  0%, 100% { transform: translate(0, 0) scale(1); }
  50% { transform: translate(40px, -40px) scale(1.1); }
}

@keyframes float-slow-reverse {
  0%, 100% { transform: translate(0, 0) rotate(0deg); }
  50% { transform: translate(-25px, 25px) rotate(10deg); }
}

@keyframes pulse-slow {
  0%, 100% { opacity: 0.3; transform: scale(1); }
  50% { opacity: 0.8; transform: scale(1.2); }
}

@keyframes pulse-delayed {
  0%, 100% { opacity: 0.2; transform: scale(1); }
  50% { opacity: 0.7; transform: scale(1.3); }
}

@keyframes pulse-delayed-2 {
  0%, 100% { opacity: 0.25; transform: scale(1); }
  50% { opacity: 0.75; transform: scale(1.15); }
}

@keyframes slide-down {
  0% { transform: translateY(-100%); opacity: 0; }
  50% { opacity: 0.5; }
  100% { transform: translateY(100%); opacity: 0; }
}

@keyframes slide-right {
  0% { transform: translateX(-100%); opacity: 0; }
  50% { opacity: 0.5; }
  100% { transform: translateX(100%); opacity: 0; }
}

@keyframes slide-up {
  0% { transform: translateY(100%); opacity: 0; }
  50% { opacity: 0.5; }
  100% { transform: translateY(-100%); opacity: 0; }
}

@keyframes rotate-slow {
  0% { transform: rotate(45deg); }
  100% { transform: rotate(405deg); }
}

@keyframes float-small {
  0%, 100% { transform: translateY(0); }
  50% { transform: translateY(-20px); }
}

.animate-float-slow { animation: float-slow 20s ease-in-out infinite; }
.animate-float-reverse { animation: float-reverse 18s ease-in-out infinite; }
.animate-float-diagonal { animation: float-diagonal 22s ease-in-out infinite; }
.animate-float-slow-reverse { animation: float-slow-reverse 25s ease-in-out infinite; }
.animate-pulse-slow { animation: pulse-slow 4s ease-in-out infinite; }
.animate-pulse-delayed { animation: pulse-delayed 5s ease-in-out infinite 1s; }
.animate-pulse-delayed-2 { animation: pulse-delayed-2 6s ease-in-out infinite 2s; }
.animate-slide-down { animation: slide-down 8s linear infinite; }
.animate-slide-right { animation: slide-right 10s linear infinite; }
.animate-slide-up { animation: slide-up 9s linear infinite; }
.animate-rotate-slow { animation: rotate-slow 30s linear infinite; }
.animate-float-small { animation: float-small 6s ease-in-out infinite; }

.btn-degrade-orange {
  background: linear-gradient(135deg, #fb923c 0%, #f97316 100%);
  color: white;
  font-weight: 600;
  box-shadow: 0 4px 6px -1px rgba(251, 146, 60, 0.3);
}

.btn-degrade-orange:hover {
  box-shadow: 0 10px 15px -3px rgba(251, 146, 60, 0.4);
  transform: translateY(-1px);
}

.btn-gray {
  background-color: #f3f4f6;
  color: #374151;
  font-weight: 500;
}

.btn-gray:hover {
  background-color: #e5e7eb;
}

.btn-deconnexion {
  background-color: #fee2e2;
  color: #dc2626;
  font-weight: 500;
  padding: 0.5rem 1rem;
  border-radius: 0.5rem;
}

.btn-deconnexion:hover {
  background-color: #fecaca;
}

.input-style {
  width: 100%;
  padding: 0.5rem 0.75rem;
  padding-left: 2.5rem;
  border: 1px solid #e5e7eb;
  border-radius: 0.5rem;
  font-size: 0.875rem;
}

.input-style:focus {
  outline: none;
  border-color: #fb923c;
  box-shadow: 0 0 0 3px rgba(251, 146, 60, 0.1);
}

.overflow-y-auto::-webkit-scrollbar {
  width: 6px;
}

.overflow-y-auto::-webkit-scrollbar-track {
  background: #f1f1f1;
}

.overflow-y-auto::-webkit-scrollbar-thumb {
  background: #c1c1c1;
  border-radius: 3px;
}

.overflow-y-auto::-webkit-scrollbar-thumb:hover {
  background: #a8a8a8;
}
</style>
