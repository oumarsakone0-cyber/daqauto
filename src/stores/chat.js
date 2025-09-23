import { defineStore } from 'pinia'
import { ref } from 'vue'
import ChatApiClient from '../services/chat-api-client'

export const useChatStore = defineStore('chat', () => {
  const isChatOpen = ref(false)
  const isDesktopChatOpen = ref(false)
  const chatMessages = ref([])
  const unreadCount = ref(0)
  const isMobile = ref(false)
  const chatClient = ref(new ChatApiClient())
  const hasWelcomeMessageSent = ref(false)
  let pollingInterval = null

  const supplier = ref({
    name: 'Support Client DAQ AUTO',
    logo: '/placeholder.svg?height=40&width=40',
    status: 'En ligne'
  })

  const checkMobile = () => {
    isMobile.value = window.innerWidth < 768
  }

  // ✅ Injecte dynamiquement le produit comme "carte message" dans le chat
  const sendProductIntroMessage = (product) => {
    if (!product) return

    const productMessage = {
      id: Date.now(),
      message: JSON.stringify({
        name: product.name,
        price: product.unit_price,
        image: product.primary_image,
        shop: product.boutique_name,
        rating: product.rating
      }),
      sender: 'bot',
      timestamp: new Date(),
      type: 'product' // type personnalisé pour affichage spécial
    }

    chatMessages.value.push(productMessage)
  }

  // ✅ Définit le fournisseur et envoie automatiquement un message produit
  const setSupplier = (product) => {
    supplier.value = {
      name: product?.boutique_name || 'Fournisseur inconnu',
      logo: product?.primary_image || '/placeholder.svg?height=40&width=40',
      status: 'En ligne'
    }

    sendProductIntroMessage(product)
  }

  const initializeChat = async () => {
    try {
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
        await chatClient.value.sendMessage(welcomeMessage.message, 'bot')
      }
    } catch (e) {
      chatMessages.value = [{
        id: Date.now(),
        message: "Désolé, une erreur s'est produite. Veuillez réessayer plus tard.",
        sender: 'bot',
        timestamp: new Date()
      }]
    }
  }

  const loadMessages = async () => {
    try {
      const result = await chatClient.value.getMessages()
      if (result.success && result.messages) {
        chatMessages.value = result.messages.map(msg => ({
          id: msg.id,
          message: msg.message,
          sender: msg.sender,
          timestamp: new Date(msg.timestamp || msg.created_at)
        }))
      }
    } catch (e) {
      console.error(e)
    }
  }

  const startPolling = () => {
    if (pollingInterval) clearInterval(pollingInterval)
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

  const openChat = () => {
    if (isMobile.value) {
      isChatOpen.value = true
      unreadCount.value = 0
      if (chatMessages.value.length === 0) initializeChat()
      startPolling()
    } else {
      openDesktopChat()
    }
  }

  const closeChat = () => {
    isChatOpen.value = false
    if (!isDesktopChatOpen.value) stopPolling()
  }

  const openDesktopChat = () => {
    isDesktopChatOpen.value = true
    unreadCount.value = 0
    if (chatMessages.value.length === 0) initializeChat()
    startPolling()
  }

  const closeDesktopChat = () => {
    isDesktopChatOpen.value = false
    if (!isChatOpen.value) stopPolling()
  }

  return {
    isChatOpen,
    isDesktopChatOpen,
    chatMessages,
    unreadCount,
    isMobile,
    supplier,
    checkMobile,
    openChat,
    closeChat,
    openDesktopChat,
    closeDesktopChat,
    setSupplier,
    sendProductIntroMessage
  }
})
