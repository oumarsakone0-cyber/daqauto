// Admin API Client pour gérer les sessions de chat
class AdminChatApiClient {
  constructor(baseUrl = "https://sastock.com/api_adjame") {
    this.baseUrl = baseUrl;
  }

  async request(endpoint, options = {}) {
    const randomValue = Math.random().toString(36).substring(2, 10);
    const url = `${this.baseUrl}/chat_admin.php?action=${endpoint}&rand=${randomValue}`;

    const config = {
      headers: {
        "Content-Type": "application/json",
        ...options.headers,
      },
      ...options,
    };

    try {
      const response = await fetch(url, config);
      const data = await response.json();

      if (!response.ok) {
        throw new Error(data.error || "Erreur API (admin)");
      }

      return data;
    } catch (error) {
      console.error("Erreur API Admin Chat:", error);
      throw error;
    }
  }

  // Récupérer toutes les sessions actives
  async getActiveSessions() {
    try {
      const response = await this.request("sessions");
      return response.sessions || [];
    } catch (error) {
      console.error("Erreur récupération sessions:", error);
      throw error;
    }
  }

  // Récupérer les messages d'une session
  async getMessages(sessionId) {
    try {
      if (!sessionId) throw new Error("Session ID requis");

      const response = await this.request(`messages&session_id=${sessionId}`);
      return response.messages || [];
    } catch (error) {
      console.error("Erreur récupération messages admin:", error);
      throw error;
    }
  }

  // Envoyer une réponse admin à une session
  async sendAdminReply(sessionId, message) {
    try {
      if (!sessionId || !message) {
        throw new Error("sessionId et message sont requis");
      }

      const response = await this.request("reply", {
        method: "POST",
        body: JSON.stringify({
          session_id: sessionId,
          message: message,
        }),
      });

      return response;
    } catch (error) {
      console.error("Erreur envoi message admin:", error);
      throw error;
    }
  }

  // Fermer une session
  async closeSession(sessionId) {
    try {
      if (!sessionId) throw new Error("Session ID requis");

      const response = await this.request("close_session", {
        method: "POST",
        body: JSON.stringify({ session_id: sessionId }),
      });

      return response;
    } catch (error) {
      console.error("Erreur fermeture session admin:", error);
      throw error;
    }
  }
}

// Export pour module JS
export default AdminChatApiClient;

// Utilisation globale dans navigateur
window.AdminChatApiClient = AdminChatApiClient;
