// Client API pour le système de chat
class ChatApiClient {
  constructor(baseUrl = "https://sastock.com/api_adjame") {
    this.baseUrl = baseUrl
    const storedUser = localStorage.getItem('user') || sessionStorage.getItem('user')
    this.userCode = storedUser ? JSON.parse(storedUser) : null
    this.sessionId = localStorage.getItem("chat_session_id")
  }

  async request(endpoint, options = {}) {
    const randomValue = Math.random().toString(36).substring(2, 10);
    const url = `${this.baseUrl}/chat_UPDATED.php?action=${endpoint}&rand=${randomValue}`

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

  // Créer ou récupérer un utilisateur
async createOrGetUser() {
  console.log("Creating or getting user with data:", this.userCode);

  try {

    // 📨 Envoi complet au backend
    const response = await this.request("create_user", {
      method: "POST",
      body: JSON.stringify({
        user_code: this.userCode.id,
        full_name: this.userCode.full_name,
        email: this.userCode.email,
      }),
    });

    // 💾 Sauvegarde locale (si ton backend renvoie un code unique)
    if (response.user_code) {
      this.userCode = response.user_code;
      localStorage.setItem("chat_user_code", this.userCode);
    }

    return response;
  } catch (error) {
    console.error("❌ Erreur création utilisateur:", error);
    throw error;
  }
}

  // Démarrer une nouvelle session
  async startSession(productId = null) {
    try {
      if (!this.userCode) {
        await this.createOrGetUser()
      }

      const response = await this.request("start_session", {
        method: "POST",
        body: JSON.stringify({
          user_code: this.userCode,
          product_id: productId,
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

  // Vérifier le statut de la session
  async checkSession() {
    try {
      if (!this.sessionId) {
        return { success: false, error: "Aucune session active" }
      }

      const response = await this.request(`check_session&session_id=${this.sessionId}`)

      // Si la session a expiré, la supprimer du localStorage
      if (response.session.status !== "active") {
        localStorage.removeItem("chat_session_id")
        this.sessionId = null
      }

      return response
    } catch (error) {
      console.error("Erreur vérification session:", error)
      throw error
    }
  }

  // Envoyer un message
  // Envoyer un message
async sendMessage(message, sender = "user", productId = null) {
  try {
    // Si aucune session active → on en démarre une
    if (!this.sessionId) {
      console.warn("⚠️ Aucune session active, démarrage automatique...");
      const session = await this.startSession(productId);
      this.sessionId = session.session_id;
      localStorage.setItem("chat_session_id", this.sessionId);
    }

    const response = await this.request("add_session_message", {
      method: "POST",
      body: JSON.stringify({
        session_id: this.sessionId,  // ✅ pas userCode
        message: message,
        sender: sender,
        product_id: productId,       // ✅ transmis depuis Pinia
        prix: null,
        image: null
      }),
    });

    return response;
  } catch (error) {
    console.error("Erreur envoi message:", error);
    throw error;
  }
}


  // Récupérer l'historique des messages

  async getMessages() {
    try {
      if (!this.userCode) {
        return { success: false, sessions: [] }
      }

      const response = await this.request(`get_sessions_chat&user_id=${this.userCode.id}`)
      return response
    } catch (error) {
      console.error("Erreur récupération messages:", error)
      throw error
    }
  }

  // Fermer la session
  async endSession() {
    try {
      if (!this.sessionId) {
        return { success: true }
      }

      const response = await this.request("end_session", {
        method: "POST",
        body: JSON.stringify({
          user_code: this.userCode,
          session_id: this.sessionId,
        }),
      })

      // Nettoyer le localStorage
      localStorage.removeItem("chat_session_id")
      this.sessionId = null

      return response
    } catch (error) {
      console.error("Erreur fermeture session:", error)
      throw error
    }
  }

  // Initialiser le chat (à appeler au démarrage)
  async initializeChat(productId = null) {
    try {
      // Créer ou récupérer l'utilisateur
      await this.createOrGetUser()

      // Vérifier s'il y a une session active
      const sessionCheck = await this.checkSession()

     // if (!sessionCheck.success || sessionCheck.session.status !== "active") {
        // Démarrer une nouvelle session
      //  await this.startSession(productId)
     // }

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

// Export pour utilisation
export default ChatApiClient

// Utilisation globale
window.ChatApiClient = ChatApiClient
