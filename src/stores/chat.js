import { defineStore } from 'pinia'
import { ref } from 'vue'
import ChatApiClient from '../services/chat-api-client'

export const useChatStore = defineStore('chat', () => {
  const isChatOpen = ref(false)
  const isDesktopChatOpen = ref(false)
  const activeProductId = ref(null)
  const activeConversationId = ref(null)
  const chatMessages = ref([])
  const conversations = ref([])
  const unreadCount = ref(0)
  const isMobile = ref(false)
  const chatClient = ref(new ChatApiClient())
  const hasWelcomeMessageSent = ref(false)
  let pollingInterval = null

  const supplier = ref({
    name: 'Support Client DAQ AUTO',
    logo: 'https://static.vecteezy.com/ti/vecteur-libre/p1/5005788-user-icon-in-trendy-flat-style-isolated-on-grey-background-user-symbol-for-your-web-site-design-logo-app-ui-vector-illustration-eps10-gratuit-vectoriel.jpg',
    status: 'En ligne'
  })

  const checkMobile = () => {
    isMobile.value = window.innerWidth < 768
  }

  // 🔹 Crée un message "carte produit" pour affichage spécial
  const sendProductIntroMessage = async (product, orderNumber = null) => {
    if (!product || !activeConversationId.value) return

    // Créer le message avec ou sans numéro de commande
    const messageText = orderNumber
      ? `Je suis intéressé par: ${product.name} (Commande #${orderNumber})`
      : `Je suis intéressé par: ${product.name}`

    const productMessage = {
      id: Date.now(),
      message: messageText,
      text: messageText,
      sender: 'user',
      message_type: 'product',
      timestamp: new Date(),
      product: {
        id: product.id,
        name: product.name,
        price: product.unit_price,
        image: product.primary_image,
        shop: product.boutique_name,
        rating: product.rating,
        slug: product.slug
      },
      order_number: orderNumber
    }

    // Ajouter localement
    chatMessages.value.push(productMessage)
    addMessageToConversation(activeConversationId.value, productMessage)

    // Envoyer au backend
    try {
      const payload = {
        session_id: activeConversationId.value,
        message: messageText,
        sender: 'user',
        message_type: 'product',
        product_id: product.id,
        product_name: product.name,
        product_price: product.unit_price,
        product_image: product.primary_image,
        product_slug: product.slug
      }

      // Ajouter le numéro de commande si présent
      if (orderNumber) {
        payload.order_number = orderNumber
      }

      const response = await fetch('https://sastock.com/api_adjame/chat_UPDATED.php?action=send_message', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(payload)
      })

      const data = await response.json()
      if (data.success && data.message) {
        const messageIndex = chatMessages.value.findIndex(m => m.id === productMessage.id)
        if (messageIndex !== -1) {
          chatMessages.value[messageIndex].id = data.message.id
        }
      }
    } catch (error) {
      console.error("❌ Erreur envoi produit intro:", error)
    }
  }

  // 🔹 Ajoute un message à la bonne conversation
  const addMessageToConversation = (conversationId, msg) => {
    if (!conversationId || !msg) return

    const conversation = conversations.value.find(c => c.id === conversationId)
    if (!conversation) return console.warn("Session non trouvée", conversationId)

    const exists = conversation.messages.some(m => m.id === msg.id)
    if (exists) return

    conversation.messages.push({
      id: msg.id,
      message: msg.text || msg.message,
      sender: msg.sender,
      timestamp: new Date(msg.timestamp || msg.created_at),
      type: msg.type || msg.message_type || 'text',
      message_type: msg.message_type || msg.type || 'text',
      product: msg.product || null,
      order_number: msg.order_number || null
    })

    // Met à jour dernier message
    const lastMsg = conversation.messages[conversation.messages.length - 1]
    let lastMsgText = "Nouveau message"
    if (lastMsg?.message_type === 'image') {
      lastMsgText = "Image partagée"
    } else if (lastMsg?.message_type === 'product') {
      lastMsgText = "Produit partagé"
    } else {
      lastMsgText = lastMsg?.text || lastMsg?.message || "Nouveau message"
    }
    conversation.lastMessage = lastMsgText
    conversation.lastMessageTime = lastMsg?.timestamp || new Date()

    // Si c'est la session active, met à jour chatMessages
    if (activeConversationId.value === conversationId) {
      chatMessages.value = conversation.messages
    }
  }

  // 🔹 Envoie fournisseur + user + produit au backend
  const sendSupplierToBackend = async (product, user) => {
    // D'abord, chercher si une session existe déjà avec ce vendeur
    const existingConversation = conversations.value.find(
      conv => conv.supplier_id === product.boutique_id
    )

    if (existingConversation) {
      console.log('✅ Session existante trouvée avec ce vendeur:', existingConversation.id)

      // Activer la conversation existante
      activeProductId.value = product.id
      activeConversationId.value = existingConversation.id
      localStorage.setItem("chat_session_id", existingConversation.id)
      chatClient.value.sessionId = existingConversation.id

      setActiveConversation(existingConversation.id)

      // Envoyer le message produit si c'est un nouveau produit dans cette conversation
      sendProductIntroMessage(product)

      return { success: true, session_id: existingConversation.id }
    }

    // Sinon, créer une nouvelle session
    const payload = {
      supplier_id: product.boutique_id || product.supplier_id || 10,
      supplier_name: product.boutique_name,
      user_id: user.id,
      user_email: user.email || user.contact,
      product_id: product.id,
      product_name: product.name,
      product_price: product.unit_price,
      product_image: product.primary_image,
      product_rating: product.rating || 0
    }

    console.log('📤 Envoi nouvelle session au backend:', payload)

    try {
      const response = await fetch('https://sastock.com/api_adjame/chat_UPDATED.php?action=create_session_chat', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(payload)
      })
      const data = await response.json()

      console.log('📥 Réponse du backend:', data)

      if (data.success && data.session_id) {
        localStorage.setItem("chat_session_id", data.session_id)
        chatClient.value.sessionId = data.session_id
        activeProductId.value = product.id
        activeConversationId.value = data.session_id

        // Récupérer immédiatement les sessions pour mettre à jour l'interface
        await fetchSupplierSessions()

        // Activer la conversation qui vient d'être créée
        setActiveConversation(data.session_id)
      }

      return data
    } catch (error) {
      console.error('❌ Erreur backend:', error)
      throw error
    }
  }

  // 🔹 Récupère toutes les sessions pour l'utilisateur
  const fetchSupplierSessions = async () => {
    const userRaw = localStorage.getItem('user') || sessionStorage.getItem('user')
    if (!userRaw) return console.error("❌ Aucun utilisateur connecté")

    let currentUser
    try {
      currentUser = JSON.parse(userRaw)
    } catch (err) {
      return console.error("❌ Impossible de parser l'utilisateur :", err)
    }

    try {
      const random = Math.floor(Math.random() * 1000000)
      const response = await fetch(
        `https://sastock.com/api_adjame/chat_UPDATED.php?action=get_sessions_chat&user_id=${currentUser.id}&_=${random}`
      )
      const data = await response.json()

      if (!data.success || !Array.isArray(data.sessions) || data.sessions.length === 0) {
        conversations.value = []
        return
      }

      // 🧩 Crée toutes les conversations
      conversations.value = data.sessions.map(session => {
        const lastMsg = session.messages?.length ? session.messages[session.messages.length - 1] : null
        let lastMsgText = "Aucun message"
        if (lastMsg) {
          if (lastMsg.message_type === 'image') {
            lastMsgText = "Image partagée"
          } else if (lastMsg.message_type === 'product' || lastMsg.product?.id) {
            lastMsgText = "Produit partagé"
          } else {
            lastMsgText = lastMsg.text || lastMsg.message
          }
        }

        return {
          id: session.id,
          supplier_id: session.supplier_id,
          name: session.supplier_name || "Fournisseur inconnu",
          avatar: session.product_image || "/placeholder.svg?height=40&width=40",
          status: "En ligne",
          online: true,
          lastMessage: lastMsgText,
          lastMessageTime: lastMsg?.timestamp || session.created_at,
          messages: (session.messages || []).map(msg => ({
            id: msg.id,
            message: msg.text || msg.message,
            sender: msg.sender,
            timestamp: new Date(msg.timestamp || msg.created_at),
            type: msg.message_type || (msg.product?.id ? "product" : "text"),
            message_type: msg.message_type || (msg.product?.id ? "product" : "text"),
            product: msg.product || null,
            order_number: msg.order_number || null
          }))
        }
      })

      // Active automatiquement la dernière session
      if (conversations.value.length > 0 && !activeConversationId.value) {
        setActiveConversation(conversations.value[0].id)
      }

    } catch (error) {
      console.error("❌ Erreur récupération sessions:", error)
    }
  }

  // 🔹 Définir la conversation active
  const setActiveConversation = (conversationId) => {
    activeConversationId.value = conversationId
    const conversation = conversations.value.find(c => c.id === conversationId)
    if (conversation) {
      chatMessages.value = conversation.messages
      supplier.value = {
        name: conversation.name,
        logo: conversation.avatar,
        status: conversation.status
      }
    }
  }

  // 🔹 Définit le fournisseur et envoie le message produit
  const setSupplier = async (product, orderNumber = null) => {
    const userRaw = localStorage.getItem('user') || sessionStorage.getItem('user')
    if (!userRaw) return console.error("❌ Aucun utilisateur connecté")

    let currentUser
    try {
      currentUser = JSON.parse(userRaw)
    } catch (err) {
      return console.error("❌ Impossible de parser l'utilisateur :", err)
    }

    if (!currentUser.id) return console.error("❌ L'utilisateur n'a pas d'ID :", currentUser)

    try {
      await sendSupplierToBackend(product, currentUser)
      activeProductId.value = product.id

      supplier.value = {
        name: product?.boutique_name || 'Fournisseur inconnu',
        logo: product?.primary_image || '/placeholder.svg?height=40&width=40',
        status: 'En ligne'
      }

      sendProductIntroMessage(product, orderNumber)
    } catch (error) {
      console.error('❌ setSupplier failed:', error)
    }
  }

  // 🔹 Initialise le chat avec message de bienvenue
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

  const initChatStore = async () => {
    checkMobile()
    await fetchSupplierSessions()  // charge les sessions existantes
    startPolling() // si tu veux récupérer les nouveaux messages en continu
  }
  
  const updateConversationsIfChanged = (serverSessions) => {
  serverSessions.forEach(serverSession => {
    const conversation = conversations.value.find(c => c.id == serverSession.id)

    const serverMessagesCount = serverSession.messages?.length || 0
    const localMessagesCount = conversation?.messages?.length || 0

    // Si le nombre de messages diffère, on met à jour
    if (!conversation) {
      // Nouvelle session => ajouter
      const lastMsg = serverSession.messages?.length ? serverSession.messages[serverSession.messages.length - 1] : null
      let lastMsgText = "Aucun message"
      if (lastMsg) {
        if (lastMsg.message_type === 'image') {
          lastMsgText = "Image partagée"
        } else if (lastMsg.message_type === 'product' || lastMsg.product?.id) {
          lastMsgText = "Produit partagé"
        } else {
          lastMsgText = lastMsg.text || lastMsg.message
        }
      }

      conversations.value.push({
        id: serverSession.id,
        supplier_id: serverSession.supplier_id,
        name: serverSession.supplier_name || "Fournisseur inconnu",
        avatar: serverSession.product_image || "/placeholder.svg?height=40&width=40",
        status: "En ligne",
        online: true,
        lastMessage: lastMsgText,
        lastMessageTime: lastMsg?.timestamp || serverSession.created_at,
        messages: (serverSession.messages || []).map(msg => ({
          id: msg.id,
          message: msg.text || msg.message,
          sender: msg.sender,
          timestamp: new Date(msg.timestamp || msg.created_at),
          type: msg.message_type || (msg.product?.id ? "product" : "text"),
          message_type: msg.message_type || (msg.product?.id ? "product" : "text"),
          product: msg.product || null,
          order_number: msg.order_number || null
        }))
      })
    } else if (serverMessagesCount !== localMessagesCount) {
      // Mise à jour des messages
      conversation.messages = (serverSession.messages || []).map(msg => ({
        id: msg.id,
        message: msg.text || msg.message,
        sender: msg.sender,
        timestamp: new Date(msg.timestamp || msg.created_at),
        type: msg.message_type || (msg.product?.id ? "product" : "text"),
        message_type: msg.message_type || (msg.product?.id ? "product" : "text"),
        product: msg.product || null,
        order_number: msg.order_number || null
      }))

      // Mettre à jour le dernier message
      const lastMsg = conversation.messages[conversation.messages.length - 1]
      let lastMsgText = "Nouveau message"
      if (lastMsg?.message_type === 'image') {
        lastMsgText = "Image partagée"
      } else if (lastMsg?.message_type === 'product') {
        lastMsgText = "Produit partagé"
      } else {
        lastMsgText = lastMsg?.message || "Nouveau message"
      }
      conversation.lastMessage = lastMsgText
      conversation.lastMessageTime = lastMsg?.timestamp || new Date()

      // Si c'est la session active, mettre à jour chatMessages
      if (activeConversationId.value === conversation.id) {
        chatMessages.value = conversation.messages
      }
    }
  })
}


  const sendMessage = async (text) => {
    if (!text || !text.trim()) return

    if (!activeConversationId.value) {
      console.error("❌ Aucune conversation active")
      return
    }

    const newMessage = {
      id: Date.now(),
      message: text,
      sender: 'user',
      timestamp: new Date()
    }

    // Ajouter le message à l'interface immédiatement
    chatMessages.value.push(newMessage)
    addMessageToConversation(activeConversationId.value, newMessage)

    try {
      // Envoyer le message au backend
      const response = await fetch('https://sastock.com/api_adjame/chat_UPDATED.php?action=send_message', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({
          session_id: activeConversationId.value,
          message: text,
          sender: 'user'
        })
      })

      const data = await response.json()
      console.log('📤 Message envoyé:', data)

      if (data.success && data.message) {
        // Mettre à jour l'ID du message local avec celui du serveur
        const messageIndex = chatMessages.value.findIndex(m => m.id === newMessage.id)
        if (messageIndex !== -1) {
          chatMessages.value[messageIndex].id = data.message.id
        }
      }
    } catch (error) {
      console.error("❌ Erreur lors de l'envoi du message:", error)
    }
  }

  // 🔹 Polling automatique

  const loadMessages = async () => {
  try {
    const result = await chatClient.value.getMessages()
    if (result.success && result.sessions) {
      // Compare et met à jour seulement si nécessaire
      updateConversationsIfChanged(result.sessions)
    }
  } catch (e) {
    console.error(e)
  }
}

const startPolling = () => {
  if (pollingInterval) clearInterval(pollingInterval)
  pollingInterval = setInterval(() => {
    if (isChatOpen.value || isDesktopChatOpen.value) loadMessages()
  }, 2000)
}

  const stopPolling = () => {
    if (pollingInterval) {
      clearInterval(pollingInterval)
      pollingInterval = null
    }
  }

  // 🔹 Envoyer une image
  const sendImageMessage = async (imageUrl, caption = '') => {
    if (!activeConversationId.value) {
      console.error("❌ Aucune conversation active")
      return
    }

    const imageMessage = {
      id: Date.now(),
      message: imageUrl,
      sender: 'user',
      message_type: 'image',
      timestamp: new Date()
    }

    // Ajouter localement
    chatMessages.value.push(imageMessage)
    addMessageToConversation(activeConversationId.value, imageMessage)

    try {
      const response = await fetch('https://sastock.com/api_adjame/chat_UPDATED.php?action=send_message', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({
          session_id: activeConversationId.value,
          message: caption || imageUrl,
          sender: 'user',
          message_type: 'image',
          image_url: imageUrl
        })
      })

      const data = await response.json()
      console.log('📤 Image envoyée:', data)

      if (data.success && data.message) {
        const messageIndex = chatMessages.value.findIndex(m => m.id === imageMessage.id)
        if (messageIndex !== -1) {
          chatMessages.value[messageIndex].id = data.message.id
        }
      }
    } catch (error) {
      console.error("❌ Erreur envoi image:", error)
    }
  }

  // 🔹 Envoyer un produit
  const sendProductMessage = async (product) => {
    if (!activeConversationId.value) {
      console.error("❌ Aucune conversation active")
      return
    }

    const productMessage = {
      id: Date.now(),
      message: 'Produit partagé',
      sender: 'bot',
      message_type: 'product',
      timestamp: new Date(),
      product: {
        id: product.id,
        name: product.name,
        price: product.unit_price,
        image: product.primary_image
      }
    }

    chatMessages.value.push(productMessage)
    addMessageToConversation(activeConversationId.value, productMessage)

    try {
      const response = await fetch('https://sastock.com/api_adjame/chat_UPDATED.php?action=send_message', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({
          session_id: activeConversationId.value,
          message: 'Produit partagé',
          sender: 'bot',
          message_type: 'product',
          product_id: product.id,
          product_name: product.name,
          product_price: product.unit_price,
          product_image: product.primary_image
        })
      })

      const data = await response.json()
      if (data.success && data.message) {
        const messageIndex = chatMessages.value.findIndex(m => m.id === productMessage.id)
        if (messageIndex !== -1) {
          chatMessages.value[messageIndex].id = data.message.id
        }
      }
    } catch (error) {
      console.error("❌ Erreur envoi produit:", error)
    }
  }

  const openChat = async () => {
    if (isMobile.value) {
      isChatOpen.value = true
      unreadCount.value = 0
      if (chatMessages.value.length === 0) await initializeChat()
      await fetchSupplierSessions()
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
    if (chatMessages.value.length === 0) await initializeChat()
    await fetchSupplierSessions()
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
    conversations,
    activeConversationId,
    checkMobile,
    openChat,
    closeChat,
    openDesktopChat,
    closeDesktopChat,
    setSupplier,
    sendProductIntroMessage,
    fetchSupplierSessions,
    sendMessage,
    sendImageMessage,
    sendProductMessage,
    setActiveConversation,
    initChatStore
  }
})
