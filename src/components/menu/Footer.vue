<template>
  <!-- Footer principal -->
  <footer class="bg-gray-900 text-white">
    <!-- Main Footer Content -->
    <div class="py-8 md:py-12">
      <div class="container mx-auto px-4">
        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-6 md:gap-8">
          <!-- Parts Collection -->
          <div class="col-span-2 md:col-span-1">
            <h4 class="text-base md:text-lg font-semibold mb-3 md:mb-4 text-orange-500">Parts Collection</h4>
            <ul class="space-y-1 md:space-y-2 text-xs md:text-sm">
              <li><a href="#" class="hover:text-orange-500 transition-colors">Maintenance parts</a></li>
              <li><a href="#" class="hover:text-orange-500 transition-colors">Oil and Grease</a></li>
              <li><a href="#" class="hover:text-orange-500 transition-colors">Engine</a></li>
              <li><a href="#" class="hover:text-orange-500 transition-colors">Transmission</a></li>
              <li><a href="#" class="hover:text-orange-500 transition-colors">Drivetrain & Suspension</a></li>
              <li><a href="#" class="hover:text-orange-500 transition-colors">Electrical System</a></li>
              <li><a href="#" class="hover:text-orange-500 transition-colors">Driver Cab & Body</a></li>
            </ul>
          </div>

          <!-- Truck Collection -->
          <div>
            <h4 class="text-base md:text-lg font-semibold mb-3 md:mb-4 text-orange-500">Truck Collection</h4>
            <ul class="space-y-1 md:space-y-2 text-xs md:text-sm">
              <li><a href="#" class="hover:text-orange-500 transition-colors">Dump Truck</a></li>
              <li><a href="#" class="hover:text-orange-500 transition-colors">Tractor</a></li>
              <li><a href="#" class="hover:text-orange-500 transition-colors">Concrete Mixer Truck</a></li>
              <li><a href="#" class="hover:text-orange-500 transition-colors">Crane Truck</a></li>
              <li><a href="#" class="hover:text-orange-500 transition-colors">Mining Truck</a></li>
              <li><a href="#" class="hover:text-orange-500 transition-colors">Wrecker Truck</a></li>
            </ul>
          </div>

          <!-- About Us -->
          <div>
            <h4 class="text-base md:text-lg font-semibold mb-3 md:mb-4 text-orange-500">About US</h4>
            <ul class="space-y-1 md:space-y-2 text-xs md:text-sm">
              <li><a href="#" class="hover:text-orange-500 transition-colors">Who We Are ?</a></li>
              <li><a href="#" class="hover:text-orange-500 transition-colors">Our Core Values</a></li>
              <li><a href="#" class="hover:text-orange-500 transition-colors">Why Choose Us?</a></li>
            </ul>
          </div>

          <!-- Help Center -->
          <div>
            <h4 class="text-base md:text-lg font-semibold mb-3 md:mb-4 text-orange-500">Help Center</h4>
            <ul class="space-y-1 md:space-y-2 text-xs md:text-sm">
              <li><a href="#" class="hover:text-orange-500 transition-colors">Guide</a></li>
              <li><a href="#" class="hover:text-orange-500 transition-colors">FAQ</a></li>
              <li><a href="#" class="hover:text-orange-500 transition-colors">Delivery Policy</a></li>
              <li><a href="#" class="hover:text-orange-500 transition-colors">Terms of Service</a></li>
            </ul>
          </div>

          <!-- Contact & Language -->
          <div class="col-span-2 md:col-span-1">
            <h4 class="text-base md:text-lg font-semibold mb-3 md:mb-4 text-orange-500">Contact us</h4>
            <div class="space-y-3 md:space-y-4 text-xs md:text-sm">
              <div>
                <p class="mb-2">Language:</p>
                <select class="bg-gray-800 border border-gray-700 rounded px-2 py-1 md:px-3 text-white text-xs md:text-sm w-full">
                  <option>English</option>
                  <option>Français</option>
                </select>
              </div>
              <div>
                <p class="mb-2">Country:</p>
                <select class="bg-gray-800 border border-gray-700 rounded px-2 py-1 md:px-3 text-white text-xs md:text-sm w-full">
                  <option>Côte d'Ivoire (USD $)</option>
                </select>
              </div>
              <!-- Ajout du lien chat dans le footer -->
              <div>
                <button 
                  @click="openChat"
                  class="flex items-center gap-2 text-orange-500 hover:text-orange-400 transition-colors"
                >
                  <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/>
                  </svg>
                  Chat Support
                </button>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Bottom Footer -->
    <div class="border-t border-gray-800 py-18 md:py-6">
      <div class="container mx-auto px-4">
        <div class="flex flex-col md:flex-row items-center justify-between gap-3 md:gap-4">
          <div class="flex items-center gap-2">
            <div class="w-6 h-6 md:w-8 md:h-8  rounded" style="background-color: #fe7900;"></div>
            <span class="font-bold text-lg md:text-xl">DAQ AUTO</span>
          </div>
          <div class="text-xs md:text-sm text-gray-400 text-center md:text-left">
            DAQ are responsible for Daq offers.
          </div>
          <div class="flex items-center gap-4">
            <button 
              @click="openChat"
              class="flex items-center gap-1 hover:scale-105 text-xs md:text-sm transition-colors bg-degrade"
            >
              💬 Chat
            </button>
          </div>
        </div>
      </div>
    </div>
  </footer>

  <!-- Bouton flottant mobile uniquement -->
  <button 
    v-if="!isChatOpen && isMobile"
    @click="openChat"
    class="fixed bottom-18 right-2 w-14 h-14  rounded-full shadow-lg z-50 flex items-center justify-center text-white hover:scale-105 transition-transform md:hidden bg-degrade"
  >
    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
      <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/>
    </svg>
    <!-- Badge de notification -->
    <span 
       v-if="unreadCount > 0"
      class="absolute -top-2 -right-2 text-white text-xs rounded-full w-6 h-6 flex items-center justify-center font-bold " style="background-color: #fe7900;"
    >
      {{ unreadCount > 9 ? '9+' : unreadCount }}
    </span>
  </button>

  <!-- Bouton flottant desktop style Facebook -->
  <button 
    v-if="!isDesktopChatOpen && !isMobile"
    @click="openDesktopChat"
    class="fixed bottom-6 right-4  text-white px-4 py-3 rounded-full shadow-lg z-50 flex items-center gap-2 hover:scale-105 transition-transform  md:flex bg-degrade"
  >
    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
      <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/>
    </svg>
    <span class="text-sm font-medium">Chat</span>
    <!-- Badge de notification -->
    <span 
      v-if="unreadCount > 0" 
      class="absolute -top-2 -right-2  text-white text-xs rounded-full w-6 h-6 flex items-center justify-center font-bold" 
      style="background-color: #fe7900;"
    >
      {{ unreadCount > 9 ? '9+' : unreadCount }}
    </span>
  </button>

  <!-- Modal mobile (votre composant existant) -->
  <ChatModal
    v-if="isChatOpen && isMobile"
    :supplier="supplier"
    :chatMessages="chatMessages"
    @close="closeChat"
    @sendMessage="handleSendMessage"
  />

  <!-- Fenêtre desktop style Facebook (votre composant existant) -->
  <ChatDesktop
    :is-open="isDesktopChatOpen"
    :supplier="supplier"
    :chat-messages="chatMessages"
    @close="closeDesktopChat"
    @send-message="handleSendMessage"
  />
</template>

<script setup>
import { ref, onMounted, onBeforeUnmount, computed } from 'vue'
import ChatModal from '../product/modals/ChatModal2.vue'
import ChatDesktop from '../product/modals/ChatWindow.vue'
import ChatApiClient from '../../services/chat-api-client'

// États réactifs
const isChatOpen = ref(false)
const isDesktopChatOpen = ref(false)
const chatMessages = ref([])
const unreadCount = ref(0)
const isLoading = ref(false)
const chatClient = ref(null)
const hasWelcomeMessageSent = ref(false)

let pollingInterval = null

// Détection mobile
const isMobile = ref(false)

// Données du fournisseur
const supplier = ref({
  name: 'Support Client DAQ AUTO',
  logo: '/placeholder.svg?height=40&width=40',
  status: 'En ligne'
})

const checkMobile = () => {
  isMobile.value = window.innerWidth < 768
}

const initializeChat = async () => {
  try {
    isLoading.value = true
    const result = await chatClient.value.initializeChat()

    if (result.success && result.messages) {
      chatMessages.value = result.messages.map(msg => ({
        id: msg.id,
        message: msg.message,
        sender: msg.sender,
        timestamp: new Date(msg.timestamp || msg.created_at)
      }))
    }

    if (chatMessages.value.length === 0 && !hasWelcomeMessageSent.value) {
      const welcomeMessage = {
        id: Date.now(),
        message: "Merci pour votre message ! Un agent va vous répondre sous peu.",
        sender: 'bot',
        timestamp: new Date()
      }
      
      chatMessages.value.push(welcomeMessage)
      hasWelcomeMessageSent.value = true
      
      // Envoyer le message de bienvenue via l'API
      await chatClient.value.sendMessage(welcomeMessage.message, 'bot')
    }
  } catch (error) {
    console.error('Erreur initialisation chat:', error)
    // Message d'erreur de fallback
    chatMessages.value = [{
      id: Date.now(),
      message: "Désolé, une erreur s'est produite. Veuillez réessayer plus tard.",
      sender: 'bot',
      timestamp: new Date()
    }]
  } finally {
    isLoading.value = false
  }
}

const loadMessages = async () => {
  try {
    const result = await chatClient.value.getMessages()
    
    if (result.success && result.messages) {
      const newMessages = result.messages.map(msg => ({
        id: msg.id,
        message: msg.message,
        sender: msg.sender,
        timestamp: new Date(msg.timestamp || msg.created_at)
      }))
      
      // Comparer le nombre de messages avant de remplacer
      if (newMessages.length !== chatMessages.value.length) {
        chatMessages.value = newMessages
        
        // Mettre à jour le compteur de notifications si chat fermé
        if (!isChatOpen.value && !isDesktopChatOpen.value) {
          const newUnreadCount = newMessages.filter(msg => 
            msg.sender === 'bot' && 
            msg.timestamp > (chatMessages.value[chatMessages.value.length - 1]?.timestamp || new Date(0))
          ).length
          
          if (newUnreadCount > 0) {
            unreadCount.value += newUnreadCount
          }
        }
      }
    }
  } catch (error) {
    console.error('Erreur chargement messages:', error)
  }
}

const startPolling = () => {
  if (pollingInterval) {
    clearInterval(pollingInterval)
  }
  
  pollingInterval = setInterval(() => {
    if (isChatOpen.value || isDesktopChatOpen.value) {
      loadMessages()
    }
  }, 2000)
}

const stopPolling = () => {
  if (pollingInterval) {
    clearInterval(pollingInterval)
    pollingInterval = null
  }
}

const handleSendMessage = async (message) => {
  try {
    // Ajouter le message utilisateur
    const userMessage = {
      id: Date.now(),
      message: message.message || message,
      sender: 'user',
      timestamp: new Date()
    }
    
    chatMessages.value.push(userMessage)

    // Envoyer via l'API
    await chatClient.value.sendMessage(userMessage.message, 'user')

    // Le message de bienvenue n'est envoyé qu'à l'initialisation

  } catch (error) {
    console.error('Erreur envoi message:', error)
  }
}

const openChat = () => {
  if (isMobile.value) {
    isChatOpen.value = true
    unreadCount.value = 0
    if (chatMessages.value.length === 0) {
      initializeChat()
    }
    startPolling()
  } else {
    openDesktopChat()
  }
}

const closeChat = () => {
  isChatOpen.value = false
  if (!isDesktopChatOpen.value) {
    stopPolling()
  }
}

const openDesktopChat = () => {
  isDesktopChatOpen.value = true
  unreadCount.value = 0
  if (chatMessages.value.length === 0) {
    initializeChat()
  }
  startPolling()
}

const closeDesktopChat = () => {
  isDesktopChatOpen.value = false
  if (!isChatOpen.value) {
    stopPolling()
  }
}

onMounted(() => {
  // Initialiser le client API
  chatClient.value = new ChatApiClient()
  
  // Détecter la taille d'écran
  checkMobile()
  window.addEventListener('resize', checkMobile)
})

onBeforeUnmount(() => {
  window.removeEventListener('resize', checkMobile)
  stopPolling()
})
</script>

<style scoped>
/* Styles pour les animations et transitions */
.transition-transform {
  transition: transform 0.2s ease-in-out;
}

.transition-colors {
  transition: color 0.2s ease-in-out;
}

.bg-degrade {
  background: linear-gradient(90deg, #fe7900, #f96204);
}

/* Styles pour le bouton flottant */
.hover\:scale-105:hover {
  transform: scale(1.05);
}

/* Styles pour les badges de notification */
.animate-pulse {
  animation: pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
}

@keyframes pulse {
  0%, 100% {
    opacity: 1;
  }
  50% {
    opacity: .5;
  }
}

.footer {
  background: #2c3e50;
  color: white;
  padding: 40px 0 20px;
  position: relative;
}

.footer-content {
  max-width: 1200px;
  margin: 0 auto;
  padding: 0 20px;
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
  gap: 30px;
}

.footer-section h3 {
  font-size: 18px;
  font-weight: 600;
  margin-bottom: 15px;
  color: #ecf0f1;
}

.footer-section p {
  color: #bdc3c7;
  line-height: 1.6;
}

.footer-section ul {
  list-style: none;
  padding: 0;
}

.footer-section ul li {
  margin-bottom: 8px;
}

.footer-section ul li a {
  color: #bdc3c7;
  text-decoration: none;
  transition: color 0.3s ease;
  cursor: pointer;
}

.footer-section ul li a:hover,
.chat-link:hover {
  color: #3498db !important;
}

.footer-bottom {
  border-top: 1px solid #34495e;
  margin-top: 30px;
  padding-top: 20px;
  text-align: center;
  color: #95a5a6;
}

.chat-fab {
  position: fixed;
  bottom: 20px;
  right: 20px;
  width: 60px;
  height: 60px;
  background: #3498db;
  border: none;
  border-radius: 50%;
  color: white;
  cursor: pointer;
  box-shadow: 0 4px 12px rgba(52, 152, 219, 0.4);
  transition: all 0.3s ease;
  z-index: 999;
  display: flex;
  align-items: center;
  justify-content: center;
  position: relative;
}

.chat-fab:hover {
  background: #2980b9;
  transform: scale(1.1);
  box-shadow: 0 6px 20px rgba(52, 152, 219, 0.6);
}

.chat-fab.has-notification {
  animation: pulse 2s infinite;
}

.notification-badge {
  position: absolute;
  top: -5px;
  right: -5px;
  background: #fe7900;
  color: white;
  border-radius: 50%;
  width: 20px;
  height: 20px;
  font-size: 12px;
  font-weight: bold;
  display: flex;
  align-items: center;
  justify-content: center;
}

@media (max-width: 768px) {
  .footer-content {
    grid-template-columns: 1fr;
    gap: 20px;
  }
  
  .chat-fab {
    width: 50px;
    height: 50px;
    bottom: 15px;
    right: 15px;
  }
}
</style>
