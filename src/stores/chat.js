import { defineStore } from "pinia"
import { ref } from "vue"
import ChatApiClient from "../services/chat-api-client"

export const useChatStore = defineStore("chat", () => {
  const isChatOpen = ref(false)
  const isDesktopChatOpen = ref(false)
  const chatMessages = ref([])
  const unreadCount = ref(0)
  const isMobile = ref(false)
  const chatClient = ref(new ChatApiClient())
  const hasWelcomeMessageSent = ref(false)
  const currentProduct = ref(null)
  const currentSupplierId = ref(null)
  let pollingInterval = null

  const supplier = ref({
    name: "Support Client DAQ AUTO",
    logo: "/placeholder.svg?height=40&width=40",
    status: "En ligne",
  })

  const checkMobile = () => {
    isMobile.value = window.innerWidth < 768
  }

  const setSupplier = async (product) => {
    if (!product) return

    currentProduct.value = product

    supplier.value = {
      name: product?.boutique_name || "Fournisseur inconnu",
      logo: product?.primary_image || "/placeholder.svg?height=40&width=40",
      status: "En ligne",
    }

    try {
      // Créer ou récupérer le fournisseur dans la BD
      const result = await chatClient.value.createSupplier(supplier.value.name, supplier.value.logo, "online")

      if (result.success) {
        currentSupplierId.value = result.supplier_id
        console.log("✅ Fournisseur créé/récupéré:", result)
      }
    } catch (error) {
      console.error("❌ Erreur création fournisseur:", error)
    }
  }

  const initializeChat = async () => {
    try {
      // Initialiser le chat (créer user + vérifier session)
      const result = await chatClient.value.initializeChat(currentProduct.value?.id)

      if (result.success && result.messages) {
        chatMessages.value = result.messages.map((msg) => ({
          id: msg.id,
          message: msg.message,
          sender: msg.sender,
          timestamp: new Date(msg.timestamp || msg.created_at),
          type: msg.message_type || "text",
          productData: msg.product_data ? JSON.parse(msg.product_data) : null,
        }))
      }

      // Si pas de messages et qu'on a un produit, envoyer le message produit
      if (chatMessages.value.length === 0 && currentProduct.value) {
        await sendProductIntroMessage(currentProduct.value)
      }

      // Envoyer le message de bienvenue si nécessaire
      if (chatMessages.value.length === 0 && !hasWelcomeMessageSent.value) {
        await sendMessage("Merci pour votre message ! Un agent va vous répondre sous peu.", "bot")
        hasWelcomeMessageSent.value = true
      }
    } catch (e) {
      console.error("❌ Erreur initialisation chat:", e)
      chatMessages.value = [
        {
          id: Date.now(),
          message: "Désolé, une erreur s'est produite. Veuillez réessayer plus tard.",
          sender: "bot",
          timestamp: new Date(),
          type: "text",
        },
      ]
    }
  }

  const sendProductIntroMessage = async (product) => {
    if (!product) return

    const productData = {
      name: product.name,
      price: product.unit_price,
      image: product.primary_image,
      shop: product.boutique_name,
      rating: product.rating,
    }

    try {
      const result = await chatClient.value.sendMessage(JSON.stringify(productData), "bot", "product", product.id)

      if (result.success && result.message) {
        chatMessages.value.push({
          id: result.message.id,
          message: result.message.message,
          sender: result.message.sender,
          timestamp: new Date(result.message.timestamp || result.message.created_at),
          type: "product",
          productData: productData,
        })
      }
    } catch (error) {
      console.error("❌ Erreur envoi message produit:", error)
    }
  }

  const loadMessages = async () => {
    try {
      const result = await chatClient.value.getMessages()
      if (result.success && result.messages) {
        chatMessages.value = result.messages.map((msg) => ({
          id: msg.id,
          message: msg.message,
          sender: msg.sender,
          timestamp: new Date(msg.timestamp || msg.created_at),
          type: msg.message_type || "text",
          productData: msg.product_data ? JSON.parse(msg.product_data) : null,
        }))
      }
    } catch (e) {
      console.error("❌ Erreur chargement messages:", e)
    }
  }

  const sendMessage = async (message, sender = "user", messageType = "text") => {
    if (!message.trim()) return

    try {
      const result = await chatClient.value.sendMessage(message, sender, messageType, currentProduct.value?.id)

      if (result.success && result.message) {
        // Ajouter le message à la liste locale
        const newMessage = {
          id: result.message.id,
          message: result.message.message,
          sender: result.message.sender,
          timestamp: new Date(result.message.timestamp || result.message.created_at),
          type: messageType,
        }

        chatMessages.value.push(newMessage)
        return newMessage
      }
    } catch (error) {
      console.error("❌ Erreur envoi message:", error)
      throw error
    }
  }

  const startPolling = () => {
    if (pollingInterval) clearInterval(pollingInterval)
    pollingInterval = setInterval(() => {
      if (isChatOpen.value || isDesktopChatOpen.value) {
        loadMessages()
      }
    }, 5000) // Réduit à 5 secondes au lieu de 100 secondes
  }

  const stopPolling = () => {
    if (pollingInterval) {
      clearInterval(pollingInterval)
      pollingInterval = null
    }
  }

  const openChat = async () => {
    if (isMobile.value) {
      isChatOpen.value = true
      unreadCount.value = 0

      if (chatMessages.value.length === 0) {
        await initializeChat()
      }
      startPolling()
    } else {
      await openDesktopChat()
    }
  }

  const closeChat = () => {
    isChatOpen.value = false
    if (!isDesktopChatOpen.value) stopPolling()
  }

  const openDesktopChat = async () => {
    isDesktopChatOpen.value = true
    unreadCount.value = 0

    if (chatMessages.value.length === 0) {
      await initializeChat()
    }
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
    sendMessage,
    loadMessages,
  }
})
