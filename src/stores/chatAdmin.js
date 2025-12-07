import { defineStore } from 'pinia'
import { ref } from 'vue'

export const useChatAdminStore = defineStore('chatAdmin', () => {
  const activeConversationId = ref(null)
  const chatMessages = ref([])
  const conversations = ref([])
  const unreadCount = ref(0)
  let pollingInterval = null

  // 🔹 Récupère toutes les sessions pour le vendeur
  const fetchSupplierSessions = async (supplierId) => {
    if (!supplierId) {
      console.error("❌ Aucun ID de fournisseur fourni")
      return
    }

    try {
      const random = Math.floor(Math.random() * 1000000)
      const response = await fetch(
        `https://sastock.com/api_adjame/chat_UPDATED.php?action=get_supplier_sessions&supplier_id=${supplierId}&_=${random}`
      )
      const data = await response.json()

      console.log('📥 Sessions reçues pour le vendeur:', data)

      // Debug: Log les messages bruts du backend
      if (data.sessions && data.sessions.length > 0) {
        data.sessions.forEach(session => {
          console.log(`Session ${session.id}:`)
          session.messages?.forEach(msg => {
            console.log(`  Message ${msg.id}: sender="${msg.sender}", type="${msg.message_type}", text="${msg.text || msg.message}"`)
          })
        })
      }

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
          user_id: session.user_id,
          user_name: session.user_name || session.user_email || "Client inconnu",
          user_avatar: "/placeholder.svg?height=40&width=40",
          status: "En ligne",
          online: true,
          product_name: session.product_name,
          product_image: session.product_image,
          lastMessage: lastMsgText,
          lastMessageTime: lastMsg?.timestamp || session.created_at,
          unread: session.unread_count || 0,
          messages: (session.messages || []).map(msg => {
            const messageType = msg.message_type || (msg.product?.id ? "product" : "text")
            // Si sender est vide ou "supplier", c'est le supplier, sinon c'est user
            let sender = 'user'
            if (msg.sender === 'supplier' || msg.sender === '') {
              sender = 'supplier'
            } else if (msg.sender === 'user') {
              sender = 'user'
            }

            return {
              id: msg.id,
              message: msg.message || msg.text,
              text: msg.text || msg.message,
              sender: sender,
              timestamp: new Date(msg.timestamp || msg.created_at),
              type: messageType,
              message_type: messageType,
              product: msg.product ? {
                ...msg.product,
                slug: msg.product.slug || msg.product_slug
              } : null,
              order_number: msg.order_number || null
            }
          })
        }
      })

      // Calculer le nombre total de messages non lus
      unreadCount.value = conversations.value.reduce((acc, conv) => acc + (conv.unread || 0), 0)

      // Active automatiquement la dernière session si aucune n'est active
      if (conversations.value.length > 0 && !activeConversationId.value) {
        setActiveConversation(conversations.value[0].id)
      }

    } catch (error) {
      console.error("❌ Erreur récupération sessions vendeur:", error)
    }
  }

  // 🔹 Définir la conversation active
  const setActiveConversation = (conversationId) => {
    activeConversationId.value = conversationId
    const conversation = conversations.value.find(c => c.id === conversationId)
    if (conversation) {
      chatMessages.value = conversation.messages
      // Marquer comme lu
      conversation.unread = 0
      unreadCount.value = conversations.value.reduce((acc, conv) => acc + (conv.unread || 0), 0)
    }
  }

  // 🔹 Envoyer un message en tant que vendeur
  const sendMessage = async (text, supplierId) => {
    if (!text || !text.trim()) return

    if (!activeConversationId.value) {
      console.error("❌ Aucune conversation active")
      return
    }

    const newMessage = {
      id: Date.now(),
      message: text,
      sender: 'supplier',  // Le vendeur envoie
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
          sender: 'supplier'
        })
      })

      const data = await response.json()
      console.log('📤 Message vendeur envoyé:', data)

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

  // 🔹 Envoyer une image en tant que vendeur
  const sendImageMessage = async (imageUrl, caption = '', supplierId) => {
    if (!activeConversationId.value) {
      console.error("❌ Aucune conversation active")
      return
    }

    const imageMessage = {
      id: Date.now(),
      message: imageUrl,
      sender: 'supplier',
      message_type: 'image',
      timestamp: new Date()
    }

    // Ajouter le message à l'interface immédiatement
    chatMessages.value.push(imageMessage)
    addMessageToConversation(activeConversationId.value, imageMessage)

    try {
      const response = await fetch('https://sastock.com/api_adjame/chat_UPDATED.php?action=send_message', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({
          session_id: activeConversationId.value,
          message: caption || imageUrl,
          sender: 'supplier',
          message_type: 'image',
          image_url: imageUrl
        })
      })

      const data = await response.json()
      console.log('📤 Image vendeur envoyée:', data)

      if (data.success && data.message) {
        const messageIndex = chatMessages.value.findIndex(m => m.id === imageMessage.id)
        if (messageIndex !== -1) {
          chatMessages.value[messageIndex].id = data.message.id
        }
      }
    } catch (error) {
      console.error("❌ Erreur lors de l'envoi de l'image:", error)
      throw error
    }
  }

  // 🔹 Ajoute un message à la bonne conversation
  const addMessageToConversation = (conversationId, msg) => {
    if (!conversationId || !msg) return

    const conversation = conversations.value.find(c => c.id === conversationId)
    if (!conversation) return console.warn("Session non trouvée", conversationId)

    const exists = conversation.messages.some(m => m.id === msg.id)
    if (exists) return

    const messageType = msg.message_type || msg.type || 'text'

    // Si sender est vide ou "supplier", c'est le supplier, sinon c'est user
    let sender = 'user'
    if (msg.sender === 'supplier' || msg.sender === '') {
      sender = 'supplier'
    } else if (msg.sender === 'user') {
      sender = 'user'
    }

    conversation.messages.push({
      id: msg.id,
      message: msg.message || msg.text,
      text: msg.text || msg.message,
      sender: sender,
      timestamp: new Date(msg.timestamp || msg.created_at),
      type: messageType,
      message_type: messageType,
      product: msg.product ? {
        ...msg.product,
        slug: msg.product.slug || msg.product_slug
      } : null,
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

  // 🔹 Mise à jour des conversations si changement
  const updateConversationsIfChanged = (serverSessions) => {
    serverSessions.forEach(serverSession => {
      const conversation = conversations.value.find(c => c.id == serverSession.id)

      const serverMessagesCount = serverSession.messages?.length || 0
      const localMessagesCount = conversation?.messages?.length || 0

      // Si le nombre de messages diffère, on met à jour
      if (!conversation) {
        // Nouvelle session => ajouter
        conversations.value.push({
          id: serverSession.id,
          user_id: serverSession.user_id,
          user_name: serverSession.user_name || serverSession.user_email || "Client inconnu",
          user_avatar: "/placeholder.svg?height=40&width=40",
          status: "En ligne",
          online: true,
          product_name: serverSession.product_name,
          product_image: serverSession.product_image,
          lastMessage: serverSession.messages?.length
            ? serverSession.messages[serverSession.messages.length - 1].message
            : "Aucun message",
          lastMessageTime: serverSession.messages?.length
            ? serverSession.messages[serverSession.messages.length - 1].timestamp
            : serverSession.created_at,
          unread: serverSession.unread_count || 0,
          messages: (serverSession.messages || []).map(msg => {
            const messageType = msg.message_type || (msg.product?.id ? "product" : "text")
            // Si sender est vide ou "supplier", c'est le supplier, sinon c'est user
            let sender = 'user'
            if (msg.sender === 'supplier' || msg.sender === '') {
              sender = 'supplier'
            } else if (msg.sender === 'user') {
              sender = 'user'
            }

            return {
              id: msg.id,
              message: msg.message || msg.text,
              text: msg.text || msg.message,
              sender: sender,
              timestamp: new Date(msg.timestamp || msg.created_at),
              type: messageType,
              message_type: messageType,
              product: msg.product ? {
                ...msg.product,
                slug: msg.product.slug || msg.product_slug
              } : null,
              order_number: msg.order_number || null
            }
          })
        })
      } else if (serverMessagesCount !== localMessagesCount) {
        // Mise à jour des messages
        conversation.messages = (serverSession.messages || []).map(msg => {
          const messageType = msg.message_type || (msg.product?.id ? "product" : "text")
          // Si sender est vide ou "supplier", c'est le supplier, sinon c'est user
          let sender = 'user'
          if (msg.sender === 'supplier' || msg.sender === '') {
            sender = 'supplier'
          } else if (msg.sender === 'user') {
            sender = 'user'
          }

          return {
            id: msg.id,
            message: msg.message || msg.text,
            text: msg.text || msg.message,
            sender: sender,
            timestamp: new Date(msg.timestamp || msg.created_at),
            type: messageType,
            message_type: messageType,
            product: msg.product ? {
              ...msg.product,
              slug: msg.product.slug || msg.product_slug
            } : null,
            order_number: msg.order_number || null
          }
        })

        // Mettre à jour le dernier message
        const lastMsg = conversation.messages[conversation.messages.length - 1]
        conversation.lastMessage = lastMsg?.message || "Nouveau message"
        conversation.lastMessageTime = lastMsg?.timestamp || new Date()

        // Mettre à jour unread
        conversation.unread = serverSession.unread_count || 0

        // Si c'est la session active, mettre à jour chatMessages
        if (activeConversationId.value === conversation.id) {
          chatMessages.value = conversation.messages
        }
      }
    })

    // Recalculer le nombre total de non lus
    unreadCount.value = conversations.value.reduce((acc, conv) => acc + (conv.unread || 0), 0)
  }

  // 🔹 Polling automatique
  const loadMessages = async (supplierId) => {
    if (!supplierId) return

    try {
      const random = Math.floor(Math.random() * 1000000)
      const response = await fetch(
        `https://sastock.com/api_adjame/chat_UPDATED.php?action=get_supplier_sessions&supplier_id=${supplierId}&_=${random}`
      )
      const data = await response.json()

      if (data.success && data.sessions) {
        updateConversationsIfChanged(data.sessions)
      }
    } catch (e) {
      console.error(e)
    }
  }

  const startPolling = (supplierId) => {
    if (pollingInterval) clearInterval(pollingInterval)
    pollingInterval = setInterval(() => {
      loadMessages(supplierId)
    }, 3000) // Toutes les 3 secondes
  }

  const stopPolling = () => {
    if (pollingInterval) {
      clearInterval(pollingInterval)
      pollingInterval = null
    }
  }

  return {
    chatMessages,
    conversations,
    activeConversationId,
    unreadCount,
    fetchSupplierSessions,
    setActiveConversation,
    sendMessage,
    sendImageMessage,
    startPolling,
    stopPolling
  }
})
