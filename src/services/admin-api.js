import axios from "axios"

// Configuration de base de l'API Admin
const ADMIN_API_BASE_URL = "https://sastock.com/api_adjame"

// Créer une instance Axios avec configuration par défaut pour l'admin
const adminApiClient = axios.create({
  baseURL: ADMIN_API_BASE_URL,
  timeout: 30000, // 30 secondes
  headers: {
    "Content-Type": "application/json",
    Accept: "application/json",
  },
})

// Fonction utilitaire pour générer un cache buster
const generateCacheBuster = () => {
  return `_cb=${Date.now()}_${Math.random().toString(36).substr(2, 9)}`
}

// Intercepteur pour les requêtes (ajouter token d'auth et cache buster)
adminApiClient.interceptors.request.use(
  (config) => {
    // Ajouter le token d'authentification admin si disponible
    const adminToken = localStorage.getItem("admin_auth_token")
    if (adminToken) {
      config.headers.Authorization = `Bearer ${adminToken}`
    }

    // Ajouter cache buster uniquement pour les requêtes GET
    if (config.method?.toLowerCase() === "get") {
      const cacheBuster = generateCacheBuster()

      if (config.params) {
        config.params._cb = cacheBuster.split("=")[1]
      } else {
        config.params = { _cb: cacheBuster.split("=")[1] }
      }

    }

    return config
  },
  (error) => {
    console.error("❌ Admin Request Error:", error)
    return Promise.reject(error)
  },
)

// Intercepteur pour les réponses
adminApiClient.interceptors.response.use(
  (response) => {
    return response
  },
  (error) => {
    console.error("❌ Admin Response Error:", error.response?.data || error.message)

    // Rediriger vers login admin si non autorisé
    const isAdminLoginRequest = error.config?.url?.includes("action=admin_login")

    if (error.response?.status === 401 && !isAdminLoginRequest) {
      localStorage.removeItem("admin_auth_token")
      localStorage.removeItem("admin_user_data")
      window.location.href = "/admin/login"
    }

    return Promise.reject(error)
  },
)

// Utilitaires pour la gestion admin
export const adminUtils = {
  /**
   * Récupérer les informations de l'admin connecté
   * @returns {Object|null} Données admin
   */
  getCurrentAdmin() {
    try {
      const adminData = localStorage.getItem("admin_user_data")
      return adminData ? JSON.parse(adminData) : null
    } catch (error) {
      console.error("Erreur lors de la récupération des données admin:", error)
      return null
    }
  },

  /**
   * Construire les paramètres de base avec admin_id
   * @param {Object} additionalParams - Paramètres additionnels
   * @returns {Object} Paramètres complets
   */
  buildBaseParams(additionalParams = {}) {
    const admin = this.getCurrentAdmin()

    if (!admin) {
      console.warn("Admin non trouvé. Certaines fonctionnalités pourraient ne pas fonctionner correctement.")
      return additionalParams
    }

    return {
      admin_id: admin.id,
      ...additionalParams,
    }
  },

  /**
   * Vérifier si l'utilisateur a les droits admin
   * @returns {boolean} Droits admin
   */
  hasAdminAccess() {
    const admin = this.getCurrentAdmin()
    return admin && (admin.role === "admin" || admin.role === "super_admin")
  },
}

// ========== API GESTION DES BOUTIQUES ==========
export const adminBoutiquesApi = {
  /**
   * Récupérer toutes les boutiques avec leurs souscriptions
   * @param {Object} params - Paramètres de filtrage et pagination
   * @returns {Promise} Liste des boutiques
   */
  async getAllBoutiques(params = {}) {
    try {
      const enhancedParams = adminUtils.buildBaseParams(params)

      const response = await adminApiClient.get("/admin-products.php", {
        params: {
          action: "boutiques_list",
          ...enhancedParams,
        },
      })
      return response.data
    } catch (error) {
      throw this.handleError(error, "Erreur lors de la récupération des boutiques")
    }
  },

  /**
   * Récupérer les boutiques qui expirent bientôt
   * @param {Object} params - Paramètres (days, etc.)
   * @returns {Promise} Boutiques expirant
   */
  async getBoutiquesExpiring(params = {}) {
    try {
      const enhancedParams = adminUtils.buildBaseParams(params)

      const response = await adminApiClient.get("/admin-products.php", {
        params: {
          action: "boutiques_expiring",
          days: params.days || 30,
          ...enhancedParams,
        },
      })
      return response.data
    } catch (error) {
      throw this.handleError(error, "Erreur lors de la récupération des boutiques expirant")
    }
  },

  /**
   * Récupérer les détails d'une boutique
   * @param {number} boutiqueId - ID de la boutique
   * @returns {Promise} Détails de la boutique
   */
  async getBoutiqueDetails(boutiqueId) {
    try {
      const baseParams = adminUtils.buildBaseParams()

      const response = await adminApiClient.get("/admin-products.php", {
        params: {
          action: "boutique_details",
          id: boutiqueId,
          ...baseParams,
        },
      })
      return response.data
    } catch (error) {
      throw this.handleError(error, "Erreur lors de la récupération des détails de la boutique")
    }
  },

  /**
   * Récupérer les statistiques globales des boutiques
   * @returns {Promise} Statistiques
   */
  async getBoutiquesStats() {
    try {
      const baseParams = adminUtils.buildBaseParams()

      const response = await adminApiClient.get("/admin-products.php", {
        params: {
          action: "boutiques_stats",
          ...baseParams,
        },
      })
      return response.data
    } catch (error) {
      throw this.handleError(error, "Erreur lors de la récupération des statistiques des boutiques")
    }
  },

  /**
   * Mettre à jour le statut d'une souscription
   * @param {number} subscriptionId - ID de la souscription
   * @param {Object} statusData - Nouveau statut
   * @returns {Promise} Résultat de la mise à jour
   */
  async updateSubscriptionStatus(subscriptionId, statusData) {
    try {
      const baseParams = adminUtils.buildBaseParams()

      const response = await adminApiClient.post("/admin-products.php", statusData, {
        params: {
          action: "update_subscription_status",
          id: subscriptionId,
          ...baseParams,
        },
      })
      return response.data
    } catch (error) {
      throw this.handleError(error, "Erreur lors de la mise à jour du statut de souscription")
    }
  },

  /**
   * Prolonger une souscription
   * @param {number} subscriptionId - ID de la souscription
   * @param {Object} extensionData - Données de prolongation
   * @returns {Promise} Résultat de la prolongation
   */
  async extendSubscription(subscriptionId, extensionData) {
    try {
      const baseParams = adminUtils.buildBaseParams()

      const response = await adminApiClient.post("/admin-products.php", extensionData, {
        params: {
          action: "extend_subscription",
          id: subscriptionId,
          ...baseParams,
        },
      })
      return response.data
    } catch (error) {
      throw this.handleError(error, "Erreur lors de la prolongation de la souscription")
    }
  },

  /**
   * Supprimer une boutique
   * @param {number} boutiqueId - ID de la boutique
   * @returns {Promise} Résultat de la suppression
   */
  async deleteBoutique(boutiqueId) {
    try {
      const baseParams = adminUtils.buildBaseParams()

      const response = await adminApiClient.delete("/admin-products.php", {
        params: {
          action: "delete_boutique",
          id: boutiqueId,
          ...baseParams,
        },
      })
      return response.data
    } catch (error) {
      throw this.handleError(error, "Erreur lors de la suppression de la boutique")
    }
  },

  /**
   * Gestion des erreurs
   * @param {Error} error - Erreur capturée
   * @param {string} defaultMessage - Message par défaut
   * @returns {Error} Erreur formatée
   */
  handleError(error, defaultMessage) {
    const message = error.response?.data?.error || error.message || defaultMessage
    const statusCode = error.response?.status || 500

    const formattedError = new Error(message)
    formattedError.statusCode = statusCode
    formattedError.originalError = error

    return formattedError
  },
}

// ========== API GESTION DES PRODUITS ==========
export const adminProductsApi = {
  /**
   * Récupérer tous les produits avec filtres avancés
   * @param {Object} params - Paramètres de filtrage et pagination
   * @returns {Promise} Liste des produits
   */
  async getAllProducts(params = {}) {
    try {
      const enhancedParams = adminUtils.buildBaseParams(params)

      const response = await adminApiClient.get("/admin-products.php", {
        params: {
          action: "products_list",
          ...enhancedParams,
        },
      })
      return response.data
    } catch (error) {
      throw this.handleError(error, "Erreur lors de la récupération des produits")
    }
  },

  /**
   * Récupérer les statistiques globales des produits
   * @param {Object} params - Paramètres (period, etc.)
   * @returns {Promise} Statistiques
   */
  async getProductsStats(params = {}) {
    try {
      const enhancedParams = adminUtils.buildBaseParams(params)

      const response = await adminApiClient.get("/admin-products.php", {
        params: {
          action: "products_stats",
          ...enhancedParams,
        },
      })
      return response.data
    } catch (error) {
      throw this.handleError(error, "Erreur lors de la récupération des statistiques des produits")
    }
  },

  /**
   * Récupérer les produits boostés
   * @param {Object} params - Paramètres de filtrage
   * @returns {Promise} Produits boostés
   */
  async getBoostedProducts(params = {}) {
    try {
      const enhancedParams = adminUtils.buildBaseParams(params)

      const response = await adminApiClient.get("/admin-products.php", {
        params: {
          action: "products_boosted",
          status: params.status || "active",
          ...enhancedParams,
        },
      })
      return response.data
    } catch (error) {
      throw this.handleError(error, "Erreur lors de la récupération des produits boostés")
    }
  },

  /**
   * Récupérer les détails d'un produit
   * @param {number} productId - ID du produit
   * @returns {Promise} Détails du produit
   */
  async getProductDetails(productId) {
    try {
      const baseParams = adminUtils.buildBaseParams()

      const response = await adminApiClient.get("/admin-products.php", {
        params: {
          action: "product_details",
          id: productId,
          ...baseParams,
        },
      })
      return response.data
    } catch (error) {
      throw this.handleError(error, "Erreur lors de la récupération des détails du produit")
    }
  },

  /**
   * Activer/Désactiver un produit (is_active)
   * @param {number} productId - ID du produit
   * @param {Object} activeData - Données d'activation (is_active: 1 ou 0)
   * @returns {Promise} Résultat de la mise à jour
   */
  async toggleProductActive(productId, activeData) {
    try {
      const baseParams = adminUtils.buildBaseParams()

      const response = await adminApiClient.put("/admin-products.php", activeData, {
        params: {
          action: "toggle_product_active",
          id: productId,
          ...baseParams,
        },
      })
      return response.data
    } catch (error) {
      throw this.handleError(error, "Erreur lors de l'activation/désactivation du produit")
    }
  },

  /**
   * Mettre à jour le statut d'un produit
   * @param {number} productId - ID du produit
   * @param {Object} statusData - Nouveau statut
   * @returns {Promise} Résultat de la mise à jour
   */
  async updateProductStatus(productId, statusData) {
    try {
      const baseParams = adminUtils.buildBaseParams()

      const response = await adminApiClient.put("/admin-products.php", statusData, {
        params: {
          action: "update_product_status",
          id: productId,
          ...baseParams,
        },
      })
      return response.data
    } catch (error) {
      throw this.handleError(error, "Erreur lors de la mise à jour du statut du produit")
    }
  },

  /**
   * Supprimer un produit définitivement
   * @param {number} productId - ID du produit
   * @returns {Promise} Résultat de la suppression
   */
  async deleteProduct(productId) {
    try {
      const baseParams = adminUtils.buildBaseParams()

      const response = await adminApiClient.delete("/admin-products.php", {
        params: {
          action: "delete_product",
          id: productId,
          ...baseParams,
        },
      })
      return response.data
    } catch (error) {
      throw this.handleError(error, "Erreur lors de la suppression du produit")
    }
  },

  /**
   * Approuver un produit en attente
   * @param {number} productId - ID du produit
   * @param {Object} approvalData - Données d'approbation
   * @returns {Promise} Résultat de l'approbation
   */
  async approveProduct(productId, approvalData = {}) {
    try {
      const baseParams = adminUtils.buildBaseParams()

      const response = await adminApiClient.post("/admin-products.php", approvalData, {
        params: {
          action: "approve_product",
          id: productId,
          ...baseParams,
        },
      })
      return response.data
    } catch (error) {
      throw this.handleError(error, "Erreur lors de l'approbation du produit")
    }
  },

  /**
   * Rejeter un produit en attente
   * @param {number} productId - ID du produit
   * @param {Object} rejectionData - Données de rejet (reason obligatoire)
   * @returns {Promise} Résultat du rejet
   */
  async rejectProduct(productId, rejectionData) {
    try {
      const baseParams = adminUtils.buildBaseParams()

      const response = await adminApiClient.post("/admin-products.php", rejectionData, {
        params: {
          action: "reject_product",
          id: productId,
          ...baseParams,
        },
      })
      return response.data
    } catch (error) {
      throw this.handleError(error, "Erreur lors du rejet du produit")
    }
  },

  /**
   * Récupérer les produits en attente de validation
   * @param {Object} params - Paramètres de pagination
   * @returns {Promise} Produits en attente
   */
  async getPendingProducts(params = {}) {
    try {
      const enhancedParams = adminUtils.buildBaseParams(params)

      const response = await adminApiClient.get("/admin-products.php", {
        params: {
          action: "pending_products",
          ...enhancedParams,
        },
      })
      return response.data
    } catch (error) {
      throw this.handleError(error, "Erreur lors de la récupération des produits en attente")
    }
  },

  /**
   * Mise à jour forcée du statut par admin
   * @param {number} productId - ID du produit
   * @param {Object} statusData - Nouveau statut et commentaire
   * @returns {Promise} Résultat de la mise à jour
   */
  async adminUpdateProductStatus(productId, statusData) {
    try {
      const baseParams = adminUtils.buildBaseParams()

      const response = await adminApiClient.post("/admin-products.php", statusData, {
        params: {
          action: "admin_update_status",
          id: productId,
          ...baseParams,
        },
      })
      return response.data
    } catch (error) {
      throw this.handleError(error, "Erreur lors de la mise à jour forcée du statut")
    }
  },

  /**
   * Rechercher des produits avec filtres admin
   * @param {string} query - Terme de recherche
   * @param {Object} filters - Filtres additionnels
   * @returns {Promise} Résultats de recherche
   */
  async searchProducts(query, filters = {}) {
    try {
      const baseParams = adminUtils.buildBaseParams()

      const params = {
        action: "products_list",
        search: query,
        ...baseParams,
        ...filters,
      }

      const response = await adminApiClient.get("/admin-products.php", { params })
      return response.data
    } catch (error) {
      throw this.handleError(error, "Erreur lors de la recherche de produits")
    }
  },

  /**
   * Exporter les produits en CSV
   * @param {Object} filters - Filtres d'export
   * @returns {Promise} Données CSV
   */
  async exportProducts(filters = {}) {
    try {
      const baseParams = adminUtils.buildBaseParams()

      const response = await adminApiClient.get("/admin-products.php", {
        params: {
          action: "export_products",
          ...baseParams,
          ...filters,
        },
        responseType: "blob",
      })
      return response.data
    } catch (error) {
      throw this.handleError(error, "Erreur lors de l'export des produits")
    }
  },

  /**
   * Gestion des erreurs
   * @param {Error} error - Erreur capturée
   * @param {string} defaultMessage - Message par défaut
   * @returns {Error} Erreur formatée
   */
  handleError(error, defaultMessage) {
    const message = error.response?.data?.error || error.message || defaultMessage
    const statusCode = error.response?.status || 500

    const formattedError = new Error(message)
    formattedError.statusCode = statusCode
    formattedError.originalError = error

    return formattedError
  },
}

// ========== API GESTION DES BOOSTS ==========
export const adminBoostApi = {
  /**
   * Récupérer tous les boosts avec filtres
   * @param {Object} params - Paramètres de filtrage
   * @returns {Promise} Liste des boosts
   */
  async getAllBoosts(params = {}) {
    try {
      const enhancedParams = adminUtils.buildBaseParams(params)

      const response = await adminApiClient.get("/admin-products.php", {
        params: {
          action: "boosts_list",
          ...enhancedParams,
        },
      })
      return response.data
    } catch (error) {
      throw adminProductsApi.handleError(error, "Erreur lors de la récupération des boosts")
    }
  },

  /**
   * Approuver une demande de boost
   * @param {number} boostId - ID du boost
   * @param {Object} approvalData - Données d'approbation
   * @returns {Promise} Résultat de l'approbation
   */
  async approveBoost(boostId, approvalData = {}) {
    try {
      const baseParams = adminUtils.buildBaseParams()

      const response = await adminApiClient.post("/admin-products.php", approvalData, {
        params: {
          action: "approve_boost",
          id: boostId,
          ...baseParams,
        },
      })
      return response.data
    } catch (error) {
      throw adminProductsApi.handleError(error, "Erreur lors de l'approbation du boost")
    }
  },

  /**
   * Rejeter une demande de boost
   * @param {number} boostId - ID du boost
   * @param {Object} rejectionData - Données de rejet
   * @returns {Promise} Résultat du rejet
   */
  async rejectBoost(boostId, rejectionData) {
    try {
      const baseParams = adminUtils.buildBaseParams()

      const response = await adminApiClient.post("/admin-products.php", rejectionData, {
        params: {
          action: "reject_boost",
          id: boostId,
          ...baseParams,
        },
      })
      return response.data
    } catch (error) {
      throw adminProductsApi.handleError(error, "Erreur lors du rejet du boost")
    }
  },

  /**
   * Annuler un boost actif
   * @param {number} boostId - ID du boost
   * @param {Object} cancellationData - Données d'annulation
   * @returns {Promise} Résultat de l'annulation
   */
  async cancelBoost(boostId, cancellationData = {}) {
    try {
      const baseParams = adminUtils.buildBaseParams()

      const response = await adminApiClient.post("/admin-products.php", cancellationData, {
        params: {
          action: "cancel_boost",
          id: boostId,
          ...baseParams,
        },
      })
      return response.data
    } catch (error) {
      throw adminProductsApi.handleError(error, "Erreur lors de l'annulation du boost")
    }
  },
}

// ========== API STATISTIQUES ADMIN ==========
export const adminStatsApi = {
  /**
   * Récupérer les statistiques globales de la plateforme
   * @param {Object} params - Paramètres (period, etc.)
   * @returns {Promise} Statistiques globales
   */
  async getGlobalStats(params = {}) {
    try {
      const enhancedParams = adminUtils.buildBaseParams(params)

      const response = await adminApiClient.get("/admin-products.php", {
        params: {
          action: "global_stats",
          period: params.period || "all",
          ...enhancedParams,
        },
      })
      return response.data
    } catch (error) {
      throw adminProductsApi.handleError(error, "Erreur lors de la récupération des statistiques globales")
    }
  },

  /**
   * Récupérer les statistiques des revenus
   * @param {Object} params - Paramètres de période
   * @returns {Promise} Statistiques des revenus
   */
  async getRevenueStats(params = {}) {
    try {
      const enhancedParams = adminUtils.buildBaseParams(params)

      const response = await adminApiClient.get("/admin-products.php", {
        params: {
          action: "revenue_stats",
          ...enhancedParams,
        },
      })
      return response.data
    } catch (error) {
      throw adminProductsApi.handleError(error, "Erreur lors de la récupération des statistiques de revenus")
    }
  },

  /**
   * Récupérer les statistiques d'utilisation
   * @param {Object} params - Paramètres de période
   * @returns {Promise} Statistiques d'utilisation
   */
  async getUsageStats(params = {}) {
    try {
      const enhancedParams = adminUtils.buildBaseParams(params)

      const response = await adminApiClient.get("/admin-products.php", {
        params: {
          action: "usage_stats",
          ...enhancedParams,
        },
      })
      return response.data
    } catch (error) {
      throw adminProductsApi.handleError(error, "Erreur lors de la récupération des statistiques d'utilisation")
    }
  },
}

// ========== UTILITAIRES ADMIN ==========
export const adminApiUtils = {
  /**
   * Construire les paramètres de pagination
   * @param {number} page - Numéro de page
   * @param {number} limit - Nombre d'éléments par page
   * @returns {Object} Paramètres de pagination
   */
  buildPaginationParams(page = 1, limit = 20) {
    return {
      page: Math.max(1, page),
      limit: Math.min(100, Math.max(1, limit)),
    }
  },

  /**
   * Construire les paramètres de tri
   * @param {string} sortBy - Champ de tri
   * @param {string} order - Ordre ('ASC' ou 'DESC')
   * @returns {Object} Paramètres de tri
   */
  buildSortParams(sortBy = "created_at", order = "DESC") {
    return {
      sort: sortBy,
      order: order.toUpperCase(),
    }
  },

  /**
   * Construire les paramètres de filtrage admin
   * @param {Object} filters - Filtres à appliquer
   * @returns {Object} Paramètres de filtrage nettoyés
   */
  buildFilterParams(filters = {}) {
    const cleanFilters = {}

    Object.keys(filters).forEach((key) => {
      const value = filters[key]
      if (value !== null && value !== undefined && value !== "" && value !== "all") {
        cleanFilters[key] = value
      }
    })

    return cleanFilters
  },

  /**
   * Formater les données pour l'affichage admin
   * @param {Array} data - Données brutes
   * @returns {Array} Données formatées
   */
  formatAdminData(data) {
    if (!Array.isArray(data)) return []

    return data.map((item) => ({
      ...item,
      formatted_created_at: this.formatDate(item.created_at),
      formatted_updated_at: this.formatDate(item.updated_at),
      formatted_price: this.formatPrice(item.unit_price || item.price),
    }))
  },

  /**
   * Formater une date
   * @param {string} dateString - Date à formater
   * @returns {string} Date formatée
   */
  formatDate(dateString) {
    if (!dateString) return "N/A"

    try {
      return new Intl.DateTimeFormat("fr-FR", {
        year: "numeric",
        month: "2-digit",
        day: "2-digit",
        hour: "2-digit",
        minute: "2-digit",
      }).format(new Date(dateString))
    } catch (error) {
      return dateString
    }
  },

  /**
   * Formater un prix
   * @param {number} price - Prix à formater
   * @returns {string} Prix formaté
   */
  formatPrice(price) {
    if (!price) return "0 FCFA"

    return new Intl.NumberFormat("fr-FR", {
      style: "currency",
      currency: "XOF",
      minimumFractionDigits: 0,
      maximumFractionDigits: 0,
    })
      .format(price)
      .replace(/\s/g, " ")
  },

  /**
   * Générer un cache buster unique
   * @returns {string} Cache buster
   */
  generateCacheBuster() {
    return generateCacheBuster()
  },
}

// ========== UTILITAIRES D'AUTHENTIFICATION ADMIN ==========
export const adminAuthUtils = {
  /**
   * Vérifier si l'admin est connecté
   * @returns {boolean} État de connexion admin
   */
  isAdminAuthenticated() {
    return !!localStorage.getItem("admin_auth_token")
  },

  /**
   * Récupérer le token d'authentification admin
   * @returns {string|null} Token admin
   */
  getAdminToken() {
    return localStorage.getItem("admin_auth_token")
  },

  /**
   * Récupérer les données admin
   * @returns {Object|null} Données admin
   */
  getAdmin() {
    const adminData = localStorage.getItem("admin_user_data")
    return adminData ? JSON.parse(adminData) : null
  },

  /**
   * Déconnecter l'admin (côté client)
   */
  logoutAdmin() {
    localStorage.removeItem("admin_auth_token")
    localStorage.removeItem("admin_user_data")
  },

  /**
   * Sauvegarder les données admin après connexion
   * @param {string} token - Token d'authentification
   * @param {Object} adminData - Données admin
   */
  saveAdminSession(token, adminData) {
    localStorage.setItem("admin_auth_token", token)
    localStorage.setItem("admin_user_data", JSON.stringify(adminData))
  },
}

// Export par défaut de l'instance Axios configurée pour l'admin
export default adminApiClient

// Export de toutes les APIs admin
export { adminApiClient }
