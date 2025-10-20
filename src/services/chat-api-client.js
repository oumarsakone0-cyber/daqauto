class ChatApiClient {
  constructor(baseUrl = "https://sastock.com/api_adjame") {
    this.baseUrl = baseUrl
    this.userCode = localStorage.getItem("chat_user_code")
    this.sessionId = localStorage.getItem("chat_session_id")
  }

  async request(endpoint, options = {}) {
    const randomValue = Math.random().toString(36).substring(2, 10)
    const url = `${this.baseUrl}/chat.php?action=${endpoint}&rand=${randomValue}`

    const config = {
      headers: {
        "Content-Type": "application/json",
        ...options.headers,
      },
      ...options,
    }

    try {
      const response = await fetch(url, config)
      const data = await response.json()

      if (!response.ok) {
        throw new Error(data.error || "Erreur API")
      }

      return data
    } catch (error) {
      console.error("Erreur API Chat:", error)
      throw error
    }
  }

  async createSupplier(supplierName, supplierLogo = null, supplierStatus = "online") {
    try {
      const response = await this.request("create_supplier", {
        method: "POST",
        body: JSON.stringify({
          supplier_name: supplierName,
          supplier_logo: supplierLogo,
          supplier_status: supplierStatus,
        }),
      })

      return response
    } catch (error) {
      console.error("Erreur création fournisseur:", error)
      throw error
    }
  }

  async startSession(productId = null, supplierId = null) {
    try {
      if (!this.userCode) {
        await this.createOrGetUser()
      }

      const response = await this.request("start_session", {
        method: "POST",
        body: JSON.stringify({
          user_code: this.userCode,
          product_id: productId,
          supplier_id: supplierId,
        }),
      })

      this.sessionId = response.session_id
      localStorage.setItem("chat_session_id", this.sessionId)

      return response
    } catch (error) {
      console.error("Erreur démarrage session:", error)
      throw error
    }
  }

  async sendMessage(message, sender = "user", messageType = "text", productId = null) {
    try {
      if (!this.sessionId) {
        await this.startSession(productId)
      }

      const payload = {
        user_code: this.userCode,
        session_id: this.sessionId,
        message: message,
        sender: sender,
        message_type: messageType,
      }

      if (messageType === "product") {
        try {
          payload.product_data = JSON.parse(message)
        } catch (e) {
          console.warn("Message produit invalide:", e)
        }
      }

      const response = await this.request("send_message", {
        method: "POST",
        body: JSON.stringify(payload),
      })

      return response
    } catch (error) {
      console.error("Erreur envoi message:", error)
      throw error
    }
  }

  async initializeChat(productId = null, supplierId = null) {
    try {
      // Créer ou récupérer l'utilisateur
      await this.createOrGetUser()

      // Vérifier s'il y a une session active
      const sessionCheck = await this.checkSession()

      if (!sessionCheck.success || sessionCheck.session?.status !== "active") {
        // Démarrer une nouvelle session
        await this.startSession(productId, supplierId)
      }

      // Récupérer l'historique des messages
      const messages = await this.getMessages()

      return {
        success: true,
        userCode: this.userCode,
        sessionId: this.sessionId,
        messages: messages.messages || [],
      }
    } catch (error) {
      console.error("Erreur initialisation chat:", error)
      throw error
    }
  }
}

export default ChatApiClient
