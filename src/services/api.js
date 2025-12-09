import axios from "axios"

// Configuration de base de l'API
const API_BASE_URL = "https://sastock.com/api_adjame"

// Créer une instance Axios avec configuration par défaut
const apiClient = axios.create({
  baseURL: API_BASE_URL,
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
apiClient.interceptors.request.use(
  (config) => {
    // Ajouter le token d'authentification si disponible
    const token = localStorage.getItem("auth_token")
    if (token) {
      config.headers.Authorization = `Bearer ${token}`
    }

    // Ajouter cache buster uniquement pour les requêtes GET
    if (config.method?.toLowerCase() === "get") {
      const cacheBuster = generateCacheBuster()

      if (config.params) {
        // Si des paramètres existent déjà, ajouter le cache buster
        config.params._cb = cacheBuster.split("=")[1] // Extraire juste la valeur
      } else {
        // Si pas de paramètres, créer l'objet params avec le cache buster
        config.params = { _cb: cacheBuster.split("=")[1] }
      }

    }

    return config
  },
  (error) => {
    console.error("❌ Request Error:", error)
    return Promise.reject(error)
  },
)

// Intercepteur pour les réponses
apiClient.interceptors.response.use(
  (response) => {
    return response
  },
  (error) => {
    console.error("❌ Response Error:", error.response?.data || error.message)

    // Rediriger uniquement si ce n'est pas une tentative de login
    const isLoginRequest = error.config?.url?.includes("action=login")

    if (error.response?.status === 401 && !isLoginRequest) {
      localStorage.removeItem("auth_token")
      localStorage.removeItem("user_data")
      window.location.href = "/login"
    }

    return Promise.reject(error) // important pour laisser le .catch() du frontend gérer
  },
)

// Utilitaires pour la gestion des boutiques et utilisateurs
export const boutiqueUtils = {
  /**
   * Récupérer les informations de l'utilisateur connecté
   * @returns {Object|null} Données utilisateur
   */
  getCurrentUser() {
    try {
      const userData = localStorage.getItem("user_data")
      return userData ? JSON.parse(userData) : null
    } catch (error) {
      console.error("Erreur lors de la récupération des données utilisateur:", error)
      return null
    }
  },

  /**
   * Récupérer la boutique active de l'utilisateur
   * @returns {Object|null} Données de la boutique
   */
  getCurrentBoutique() {
    const user = this.getCurrentUser()
    if (user && user.boutiques && user.boutiques.length > 0) {
      // Retourner la première boutique par défaut
      return user.boutiques[0]
    }
    return null
  },

  /**
   * Construire les paramètres de base avec boutique_id et user_id
   * @param {Object} additionalParams - Paramètres additionnels
   * @returns {Object} Paramètres complets
   */
  buildBaseParams(additionalParams = {}) {
    const user = this.getCurrentUser()
    const boutique = this.getCurrentBoutique()

    if (!user || !boutique) {
      console.warn(
        "Utilisateur ou boutique non trouvé. Certaines fonctionnalités pourraient ne pas fonctionner correctement.",
      )
      return additionalParams
    }

    return {
      user_id: user.id,
      boutique_id: boutique.id,
      ...additionalParams,
    }
  },

  /**
   * Vérifier si l'utilisateur a accès à une boutique
   * @param {number} boutiqueId - ID de la boutique
   * @returns {boolean} Accès autorisé
   */
  hasAccessToBoutique(boutiqueId) {
    const user = this.getCurrentUser()
    if (!user || !user.boutiques) return false

    return user.boutiques.some((boutique) => boutique.id === boutiqueId)
  },
}

// Service API pour les produits (VERSION MISE À JOUR AVEC FILTRES)
export const productsApi = {
  /**
   * Récupérer la liste des produits avec filtres et pagination
   * @param {Object} params - Paramètres de filtrage et pagination
   * @returns {Promise} Réponse de l'API
   */
  async getProducts(params = {}) {
    try {
      // Ajouter boutique_id et user_id aux paramètres
      const enhancedParams = boutiqueUtils.buildBaseParams(params)

      const response = await apiClient.get("/products.php", {
        params: {
          action: "list",
          ...enhancedParams,
        },
      })
      return response.data
    } catch (error) {
      throw this.handleError(error, "Erreur lors de la récupération des produits")
    }
  },

  /**
   * Récupérer les produits pour la page de résultats avec filtres avancés
   * @param {Object} options - Options de filtrage et pagination
   * @returns {Promise} Réponse de l'API
   */
  async getProductsForResults(options = {}) {
    try {
      const {
        category,
        subcategory,
        subcategories,
        sub_subcategory,
        product_select,
        page = 1,
        limit = 20,
        sort = "created_at",
        order = "DESC",
        min_price,
        max_price,
        minPrice,
        maxPrice,
        free_shipping,
        freeShipping,
        min_rating,
        categories,
        color,
        size,
        brand,
        condition,
        location,
        markets,
        wholesaleAvailable,
        viewsSort,
        inStock,
        // Ajouter les nouveaux paramètres de filtrage de camions
        boutique_market,
        vehicle_make,
        vehicle_condition,
        fuel_type,
        transmission_type,
        drive_type,
        engine_brand,
        vehicle_year_min,
        vehicle_year_max,
        payload_capacity_min,
        payload_capacity_max,
        gvw_min,
        gvw_max,
        stock,
        boutique_verified,
      } = options

      // Construire les paramètres pour l'API
      const params = {
        action: "lists",
        page,
        limit,
        sort: sort || "created_at",
        order: order || "DESC",
      }

      // Appliquer les filtres par priorité (sous-sous-catégorie > sous-catégorie > catégorie)
      if (sub_subcategory) {
        params.sub_subcategory = sub_subcategory
      } else if (subcategory) {
        params.subcategory = subcategory
      } else if (subcategories) {
        params.subcategories = subcategories
      } else if (category) {
        params.category = category
      } else if (categories) {
        params.categories = categories
      }

      // Filtre par nom de produit
      if (product_select) {
        params.product_select = product_select
      }

      // Filtres de prix
      if (minPrice || min_price) {
        params.min_price = minPrice || min_price
      }

      if (maxPrice || max_price) {
        params.max_price = maxPrice || max_price
      }

      // Filtre de livraison gratuite
      if (freeShipping || free_shipping) {
        params.free_shipping = "true"
      }

      // Filtre de note minimale
      if (min_rating) {
        params.min_rating = min_rating
      }

      // Filtres additionnels standard
      if (color) {
        params.color = color
      }

      if (size) {
        params.size = size
      }

      if (brand) {
        params.brand = brand
      }

      if (condition) {
        params.condition = condition
      }

      if (location) {
        params.location = location
      }

      // Nouveaux filtres

      // Filtre par marchés
      if (markets) {
        params.markets = markets
      }

      // Filtre par disponibilité en gros
      if (wholesaleAvailable) {
        params.wholesale_available = "true"
      }

      // Tri par vues
      if (viewsSort) {
        params.views_sort = viewsSort
      }

      // Filtre par stock disponible
      if (inStock) {
        params.in_stock = "true"
      }

      if (boutique_market) {
        params.boutique_market = boutique_market
      }

      if (vehicle_make) {
        params.vehicle_make = vehicle_make
      }

      if (vehicle_condition) {
        params.vehicle_condition = vehicle_condition
      }

      if (fuel_type) {
        params.fuel_type = fuel_type
      }

      if (transmission_type) {
        params.transmission_type = transmission_type
      }

      if (drive_type) {
        params.drive_type = drive_type
      }

      if (engine_brand) {
        params.engine_brand = engine_brand
      }

      if (vehicle_year_min) {
        params.vehicle_year_min = vehicle_year_min
      }

      if (vehicle_year_max) {
        params.vehicle_year_max = vehicle_year_max
      }

      if (payload_capacity_min) {
        params.payload_capacity_min = payload_capacity_min
      }

      if (payload_capacity_max) {
        params.payload_capacity_max = payload_capacity_max
      }

      if (gvw_min) {
        params.gvw_min = gvw_min
      }

      if (gvw_max) {
        params.gvw_max = gvw_max
      }

      if (stock) {
        params.stock = "true"
      }

      if (boutique_verified) {
        params.boutique_verified = "true"
      }


      const response = await apiClient.get("/products.php", { params })
      return response.data
    } catch (error) {
      console.error("❌ Erreur lors de la récupération des produits pour résultats:", error)
      throw this.handleError(error, "Erreur lors de la récupération des produits pour les résultats")
    }
  },

  /**
   * Récupérer les données pour les filtres (sous-catégories, marchés, etc.)
   * @returns {Promise} Données pour les filtres
   */
  async getFiltersData() {
    try {
      const response = await apiClient.get("/products.php", {
        params: {
          action: "get_filters_data",
        },
      })

      return response.data
    } catch (error) {
      console.error("❌ Erreur lors de la récupération des données de filtre:", error)
      throw this.handleError(error, "Erreur lors de la récupération des données de filtre")
    }
  },

  /**
   * Formater les produits pour l'affichage dans la page de résultats
   * @param {Array} apiProducts - Produits bruts de l'API
   * @returns {Array} Produits formatés pour l'affichage
   */
  formatProductsForResults(apiProducts) {
    // Vérifier que apiProducts est un tableau
    if (!Array.isArray(apiProducts)) {
      console.error("formatProductsForResults: apiProducts n'est pas un tableau", apiProducts)
      return []
    }

    return apiProducts.map((product) => ({
      id: product.id,
      name: product.name,
      slug: product.slug || this.generateSlug(product.name),
      images: this.getProductImages(product),
      currentImageIndex: 0,
      unitPrice: Number.parseFloat(product.unit_price) || 0,
      wholesalePrice: Number.parseFloat(product.wholesale_price) || Number.parseFloat(product.unit_price) * 0.8,
      minQuantity: product.wholesale_min_qty || 1,
      freeShipping: product.free_shipping || Math.random() > 0.5, // Simulation si pas de données
      isAd: product.is_promoted || false,
      tradeAssurance: true,
      rating: product.rating || (4.0 + Math.random() * 1).toFixed(1), // Utiliser la note réelle ou simuler
      reviews: product.reviews || Math.floor(Math.random() * 5000) + 100, // Utiliser les avis réels ou simuler
      shopName: product.boutique_name || "Boutique Partenaire",
      supplier: {
        name: product.boutique_name || "Fournisseur",
        years: Math.floor(Math.random() * 10) + 1,
        country: "CI",
        flag: "https://flagcdn.com/w20/ci.png",
      },
      description: product.description,
      location: this.getProductLocation(product),
      createdAt: product.created_at,
      views: product.views || 0,
      color: product.color,
      size: product.size,
      brand: product.brand,
      condition: product.condition || "Neuf",
    }))
  },

  /**
   * Filtrer les produits côté client (fallback si l'API ne supporte pas certains filtres)
   * @param {Array} products - Liste des produits
   * @param {Object} filters - Filtres à appliquer
   * @returns {Array} Produits filtrés
   */
  filterProducts(products, filters) {
    if (!Array.isArray(products)) {
      console.error("filterProducts: products n'est pas un tableau", products)
      return []
    }

    return products.filter((product) => {
      // Filtre de prix
      if (filters.minPrice && product.unitPrice < Number.parseFloat(filters.minPrice)) {
        return false
      }

      if (filters.maxPrice && product.unitPrice > Number.parseFloat(filters.maxPrice)) {
        return false
      }

      // Filtre de livraison gratuite
      if (filters.freeShipping && !product.freeShipping) {
        return false
      }

      // Filtre de note minimale
      if (filters.minRating && Number.parseFloat(product.rating) < Number.parseFloat(filters.minRating)) {
        return false
      }

      // Filtre par couleur
      if (filters.color && product.color && !product.color.toLowerCase().includes(filters.color.toLowerCase())) {
        return false
      }

      // Filtre par taille
      if (filters.size && product.size && !product.size.toLowerCase().includes(filters.size.toLowerCase())) {
        return false
      }

      // Filtre par marque
      if (filters.brand && product.brand && !product.brand.toLowerCase().includes(filters.brand.toLowerCase())) {
        return false
      }

      // Filtre par condition
      if (
        filters.condition &&
        product.condition &&
        product.condition.toLowerCase() !== filters.condition.toLowerCase()
      ) {
        return false
      }

      return true
    })
  },

  /**
   * Extraire les images d'un produit
   * @param {Object} product - Produit brut de l'API
   * @returns {Array} Liste des URLs d'images
   */
  getProductImages(product) {
    if (product.images && Array.isArray(product.images) && product.images.length > 0) {
      return product.images.map((img) => img.image_url || img)
    } else if (product.primary_image) {
      return [product.primary_image]
    } else if (product.image_url) {
      return [product.image_url]
    }
    return ["https://via.placeholder.com/300x300?text=Pas+d%27image"]
  },

  /**
   * Obtenir la localisation d'un produit
   * @param {Object} product - Produit brut de l'API
   * @returns {string} Localisation formatée
   */
  getProductLocation(product) {
    if (product.city_name) {
      return product.commune_name ? `${product.city_name}, ${product.commune_name}` : product.city_name
    }
    return "Côte d'Ivoire"
  },

  /**
   * Générer un slug à partir d'un nom
   * @param {string} name - Nom du produit
   * @returns {string} Slug généré
   */
  generateSlug(name) {
    if (!name) return null

    return name
      .toLowerCase()
      .trim()
      .replace(/[àáâãäå]/g, "a")
      .replace(/[èéêë]/g, "e")
      .replace(/[ìíîï]/g, "i")
      .replace(/[òóôõö]/g, "o")
      .replace(/[ùúûü]/g, "u")
      .replace(/[ç]/g, "c")
      .replace(/[ñ]/g, "n")
      .replace(/[^a-z0-9\s-]/g, "")
      .replace(/\s+/g, "-")
      .replace(/-+/g, "-")
      .replace(/^-|-$/g, "")
  },

  /**
   * Récupérer un produit par ID ou slug
   * @param {string|number} identifier - ID ou slug du produit
   * @param {string} type - 'id' ou 'slug'
   * @returns {Promise} Données du produit
   */
  async getProduct(identifier, type = "id") {
    try {
      // Ajouter boutique_id et user_id aux paramètres
      const baseParams = boutiqueUtils.buildBaseParams()

      const params = {
        action: "show",
        [type]: identifier,
        ...baseParams,
      }

      const response = await apiClient.get("/products.php", { params })
      return response.data
    } catch (error) {
      throw this.handleError(error, "Erreur lors de la récupération du produit")
    }
  },

  /**
   * Récupérer un fournisseur aléatoire avec ses produits
   * @param {Object} params - Paramètres optionnels
   * @returns {Promise} Données du fournisseur et ses produits
   */
  async getRandomSupplier(params = {}) {
    try {
      const response = await apiClient.get("/products.php", {
        params: {
          action: "random_supplier",
          limit: params.limit || 3, // Limite de produits à récupérer
          ...params,
        },
      })
      return response.data
    } catch (error) {
      throw this.handleError(error, "Erreur lors de la récupération du fournisseur aléatoire")
    }
  },

  /**
   * Récupérer les produits les plus vus (ANCIENNE VERSION - pour compatibilité)
   * @param {Object} params - Paramètres optionnels (limit, period, etc.)
   * @returns {Promise} Produits les plus vus dans l'ancien format
   */
  async getMostViewedProducts(params = {}) {
    try {
      // Déterminer quelle action utiliser selon les paramètres
      const action = params.useNewFormat ? "most_viewed" : "most_viewed_products"

      const response = await apiClient.get("/products.php", {
        params: {
          action: action,
          limit: params.limit || (params.useNewFormat ? 15 : 7),
          period: params.period || "all", // all, week, month
          ...params,
        },
      })
      return response.data
    } catch (error) {
      throw this.handleError(error, "Erreur lors de la récupération des produits les plus vus")
    }
  },

  /**
   * Récupérer les produits les plus vus pour la page d'accueil (NOUVEAU FORMAT)
   * @param {Object} params - Paramètres optionnels (limit, period, etc.)
   * @returns {Promise} Produits les plus vus avec rating et expérience
   */
  async getMostViewedProductsForHomepage(params = {}) {
    try {
      const response = await apiClient.get("/products.php", {
        params: {
          action: "most_viewed",
          limit: params.limit || 15,
          period: params.period || "all", // all, week, month
          ...params,
        },
      })
      return response.data
    } catch (error) {
      throw this.handleError(error, "Erreur lors de la récupération des produits les plus vus")
    }
  },

  /**
   * Récupérer la liste des produits avec filtres et pagination
   * @param {Object} params - Paramètres de filtrage et pagination
   * @returns {Promise} Réponse de l'API
   */
  async getProducts(params = {}) {
    try {
      // Ajouter boutique_id et user_id aux paramètres
      const enhancedParams = boutiqueUtils.buildBaseParams(params)

      const response = await apiClient.get("/products.php", {
        params: {
          action: "list",
          ...enhancedParams,
        },
      })
      return response.data
    } catch (error) {
      throw this.handleError(error, "Erreur lors de la récupération des produits")
    }
  },

  /**
   * Booster un produit
   * @param {Object} boostData - Données du boost
   * @param {Object} getParams - Paramètres GET optionnels (boutique_id, user_id)
   * @returns {Promise} Résultat du boost
   */
  async boostProduct(boostData, getParams = {}) {
    try {
      // Utiliser les paramètres passés ou les paramètres de base par défaut
      const baseParams = Object.keys(getParams).length > 0 ? getParams : boutiqueUtils.buildBaseParams()


      const response = await apiClient.post("/products.php", boostData, {
        params: {
          action: "boostproduct",
          ...baseParams,
        },
      })
      return response.data
    } catch (error) {
      throw this.handleError(error, "Erreur lors du boost du produit")
    }
  },

  /**
   * Créer un nouveau produit
   * @param {Object} productData - Données du produit
   * @param {Object} getParams - Paramètres GET optionnels (boutique_id, user_id)
   * @returns {Promise} Produit créé
   */
  async createProduct(productData, getParams = {}) {
    try {
      // Utiliser les paramètres passés ou les paramètres de base par défaut
      const baseParams = Object.keys(getParams).length > 0 ? getParams : boutiqueUtils.buildBaseParams()

      const response = await apiClient.post("/products.php", productData, {
        params: {
          action: "create",
          ...baseParams,
        },
      })
      return response.data
    } catch (error) {
      throw this.handleError(error, "Erreur lors de la création du produit")
    }
  },

  /**
   * Créer une nouvelle commande
   * @param {Object} orderData - Données de la commande
   * @returns {Promise} Commande créée
   */
  async createOrder(orderData) {
    try {
      const response = await apiClient.post("/products.php", orderData, {
        params: { action: "create_order" },
      })
      return response.data
    } catch (error) {
      throw this.handleError(error, "Erreur lors de la création de la commande")
    }
  },

  /**
   * Mettre à jour un produit existant
   * @param {number} id - ID du produit
   * @param {Object} productData - Nouvelles données du produit
   * @param {Object} getParams - Paramètres GET optionnels (boutique_id, user_id)
   * @returns {Promise} Produit mis à jour
   */
  async updateProduct(id, productData, getParams = {}) {
    try {
      // Utiliser les paramètres passés ou les paramètres de base par défaut
      const baseParams = Object.keys(getParams).length > 0 ? getParams : boutiqueUtils.buildBaseParams()


      const response = await apiClient.put("/products.php", productData, {
        params: {
          action: "update",
          id,
          ...baseParams,
        },
      })
      return response.data
    } catch (error) {
      throw this.handleError(error, "Erreur lors de la mise à jour du produit")
    }
  },

  /**
   * Mettre à jour le stock d'un produit
   * @param {number} id - ID du produit
   * @param {Object} stockData - Données de stock
   * @param {Object} getParams - Paramètres GET optionnels
   * @returns {Promise} Réponse de l'API
   */
  async updateStock(id, stockData, getParams = {}) {
    try {
      // Utiliser les paramètres passés ou les paramètres de base par défaut
      const baseParams = Object.keys(getParams).length > 0 ? getParams : boutiqueUtils.buildBaseParams()

      const response = await apiClient.put("/products.php", stockData, {
        params: {
          action: "update-stock",
          id,
          ...baseParams,
        },
      })
      return response.data
    } catch (error) {
      throw this.handleError(error, "Erreur lors de la mise à jour du stock")
    }
  },

  /**
   * Mettre à jour le statut d'un produit
   * @param {number} id - ID du produit
   * @param {Object} statusData - Nouveau statut
   * @param {Object} getParams - Paramètres GET optionnels
   * @returns {Promise} Réponse de l'API
   */
  async updateStatus(id, statusData, getParams = {}) {
    try {
      // Utiliser les paramètres passés ou les paramètres de base par défaut
      const baseParams = Object.keys(getParams).length > 0 ? getParams : boutiqueUtils.buildBaseParams()

      const response = await apiClient.put("/products.php", statusData, {
        params: {
          action: "update-status",
          id,
          ...baseParams,
        },
      })
      return response.data
    } catch (error) {
      throw this.handleError(error, "Erreur lors de la mise à jour du statut")
    }
  },

  /**
   * Supprimer un produit (soft delete)
   * @param {number} id - ID du produit
   * @param {Object} getParams - Paramètres GET optionnels
   * @returns {Promise} Réponse de l'API
   */
  async deleteProduct(id, getParams = {}) {
    try {
      // Utiliser les paramètres passés ou les paramètres de base par défaut
      const baseParams = Object.keys(getParams).length > 0 ? getParams : boutiqueUtils.buildBaseParams()

      const response = await apiClient.delete("/products.php", {
        params: {
          action: "delete",
          id,
          ...baseParams,
        },
      })
      return response.data
    } catch (error) {
      throw this.handleError(error, "Erreur lors de la suppression du produit")
    }
  },

  /**
 * Ajouter un like à un produit
 * @param {Object} likeData - Données du like { id_produit, user }
 * @returns {Promise} Réponse du backend
 */
async addLike(likeData) {
  try {
    const response = await apiClient.post("/products.php", likeData, {
      params: { action: "addProductLike" },
    });
    return response.data;
  } catch (error) {
    throw this.handleError(error, "Erreur lors de l'ajout du like");
  }
},

/**
 * Récupérer les favoris d'un utilisateur
 * @param {number|string} userId - ID de l'utilisateur
 * @returns {Promise} Liste des favoris
 */
async getFavorites(userId) {
  try {
    const response = await apiClient.post("/products.php", { user: userId }, {
      params: { action: "get_my_favorite" }
    });
    return response.data;
  } catch (error) {
    throw this.handleError(error, "Erreur lors de la récupération des favoris");
  }
},

  /**
   * Rechercher des produits
   * @param {string} query - Terme de recherche
   * @param {Object} filters - Filtres additionnels
   * @returns {Promise} Résultats de recherche
   */
  async searchProducts(query, filters = {}) {y
    try {
      // Ajouter boutique_id et user_id aux paramètres
      const baseParams = boutiqueUtils.buildBaseParams()

      const params = {
        action: "search",
        search: query,
        ...baseParams,
        ...filters,
      }

      const response = await apiClient.get("/products.php", { params })
      return response.data
    } catch (error) {
      throw this.handleError(error, "Erreur lors de la recherche")
    }
  },

  /**
   * Upload d'une image de produit
   * @param {File} file - Fichier image
   * @param {number} productId - ID du produit (optionnel)
   * @param {string} altText - Texte alternatif (optionnel)
   * @returns {Promise} URL de l'image uploadée
   */
  async uploadImage(file, productId = null, altText = "") {
    try {
      // Ajouter boutique_id et user_id aux paramètres
      const baseParams = boutiqueUtils.buildBaseParams()

      const formData = new FormData()
      formData.append("image", file)

      if (productId) {
        formData.append("product_id", productId)
      }

      if (altText) {
        formData.append("alt_text", altText)
      }

      const response = await apiClient.post("/products.php", formData, {
        params: {
          action: "upload-image",
          ...baseParams,
        },
        headers: {
          "Content-Type": "multipart/form-data",
        },
        onUploadProgress: (progressEvent) => {
          const percentCompleted = Math.round((progressEvent.loaded * 100) / progressEvent.total)
        },
      })

      return response.data
    } catch (error) {
      throw this.handleError(error, "Erreur lors de l'upload de l'image")
    }
  },

  /**
   * Supprimer une image de produit
   * @param {number} imageId - ID de l'image
   * @returns {Promise} Réponse de l'API
   */
  async deleteImage(imageId) {
    try {
      // Ajouter boutique_id et user_id aux paramètres
      const baseParams = boutiqueUtils.buildBaseParams()

      const response = await apiClient.delete("/products.php", {
        params: {
          action: "delete-image",
          id: imageId,
          ...baseParams,
        },
      })
      return response.data
    } catch (error) {
      throw this.handleError(error, "Erreur lors de la suppression de l'image")
    }
  },

  /**
   * Récupérer les statistiques des produits
   * @param {Object} params - Paramètres (period, boutique_id, etc.)
   * @returns {Promise} Statistiques
   */
  async getStats(params = {}) {
    try {
      // Ajouter boutique_id aux paramètres
      const baseParams = boutiqueUtils.buildBaseParams(params)

      const response = await apiClient.get("/products.php", {
        params: {
          action: "stats",
          ...baseParams,
        },
      })
      return response.data
    } catch (error) {
      throw this.handleError(error, "Erreur lors de la récupération des statistiques")
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

// Service API pour les commandes utilisateur (NOUVELLES MÉTHODES AJOUTÉES)
export const ordersApi = {
  /**
   * Récupérer les commandes de l'utilisateur connecté
   * @returns {Promise} Liste des commandes
   */
  async getMyOrders(userId) {
    try {
      const response = await apiClient.get("/products.php", {
        params: {
          action: "get_my_orders",
          user_id: userId // ✅ on envoie bien l'ID utilisateur
        }
      })
      return response.data
    } catch (error) {
      throw productsApi.handleError(error, "Erreur lors de la récupération des commandes")
    }
  },

  /**
   * Récupérer les détails d'une commande spécifique
   * @param {number} orderId - ID de la commande
   * @returns {Promise} Détails de la commande
   */
  async getOrderDetails(orderId) {
    try {
      const response = await apiClient.get("/products.php", {
        params: {
          action: "get_order_details",
          order_id: orderId,
        },
      })
      return response.data
    } catch (error) {
      throw productsApi.handleError(error, "Erreur lors de la récupération des détails de la commande") // Utilisation de productsApi.handleError pour la cohérence
    }
  },

  /**
   * Ajouter une preuve de paiement à une commande
   * @param {number} orderId - ID de la commande
   * @param {FormData} formData - Données du formulaire avec le fichier
   * @returns {Promise} Résultat de l'upload
   */
  async uploadPaymentProof(orderId, formData) {
    try {
      const response = await apiClient.post("/products.php", formData, {
        params: {
          action: "upload_payment_proof",
          commande_id: orderId,
        },
        headers: {
          "Content-Type": "multipart/form-data",
        },
      })
      return response.data
    } catch (error) {
      throw productsApi.handleError(error, "Erreur lors de l'envoi de la preuve de paiement") // Utilisation de productsApi.handleError pour la cohérence
    }
  },

    /**
   * Ajouter une preuve de paiement à une commande
   * @param {number} orderId - ID de la commande
   * @param {FormData} formData - Données du formulaire avec le fichier
   * @returns {Promise} Résultat de l'upload
   */
  async uploadAdditionalPayment(orderId, formData) {
    try {
      const response = await apiClient.post("/products.php", formData, {
        params: {
          action: "add_new_payment",
          commande_id: orderId,
          data: formData
        },
        headers: {
          "Content-Type": "multipart/form-data",
        },
      })
      return response.data
    } catch (error) {
      throw productsApi.handleError(error, "Erreur lors de l'envoi de la preuve de paiement") // Utilisation de productsApi.handleError pour la cohérence
    }
  },

  /**
   * Annuler une commande
   * @param {number} orderId - ID de la commande
   * @param {string} reason - Raison de l'annulation
   * @returns {Promise} Résultat de l'annulation
   */
  async cancelOrder(orderId, reason) {
    try {
      const response = await apiClient.post(
        "/products.php",
        { commande_id: orderId, reason },
        { params: { action: "cancel_order" } },
      )
      return response.data
    } catch (error) {
      throw productsApi.handleError(error, "Erreur lors de l'annulation de la commande") // Utilisation de productsApi.handleError pour la cohérence
    }
  },
}

export const premiumApi = {
  // ✅ NOUVEAU: Récupérer l'abonnement d'une boutique
  async getBoutiqueSubscription(params = {}) {
    try {
      const response = await apiClient.get("/products.php", {
        params: {
          action: "premium_subscription",
          ...params,
        },
      })
      return response.data
    } catch (error) {
      console.error("Erreur getBoutiqueSubscription:", error)
      throw error
    }
  },

  // ✅ NOUVEAU: Souscrire une boutique à un plan Premium
  async subscribeBoutiqueToPremium(data, params = {}) {
    try {
      const response = await apiClient.post("/products.php", data, {
        params: {
          action: "premium_subscribe",
          ...params,
        },
      })
      return response.data
    } catch (error) {
      console.error("Erreur subscribeBoutiqueToPremium:", error)
      throw error
    }
  },

  // ✅ NOUVEAU: Annuler l'abonnement d'une boutique
  async cancelBoutiqueSubscription(data, params = {}) {
    try {
      const response = await apiClient.post("/products.php", data, {
        params: {
          action: "premium_cancel",
          ...params,
        },
      })
      return response.data
    } catch (error) {
      console.error("Erreur cancelBoutiqueSubscription:", error)
      throw error
    }
  },

  // ✅ NOUVEAU: Vérifier les limites Premium d'une boutique
  async checkBoutiquePremiumLimits(params = {}) {
    try {
      const response = await apiClient.get("/products.php", {
        params: {
          action: "premium_check_limits",
          ...params,
        },
      })
      return response.data
    } catch (error) {
      console.error("Erreur checkBoutiquePremiumLimits:", error)
      throw error
    }
  },

  // Garder les méthodes existantes
  async getPlans() {
    try {
      const response = await apiClient.get("/products.php", {
        params: { action: "premium_plans" },
      })
      return response.data
    } catch (error) {
      console.error("Erreur getPlans:", error)
      throw error
    }
  },
}

// Utilitaires Premium
export const premiumUtils = {
  /**
   * Vérifier si l'utilisateur a un abonnement actif
   * @returns {boolean} État de l'abonnement
   */
  hasActiveSubscription() {
    const user = boutiqueUtils.getCurrentUser()
    return user?.premium?.status === "active" && new Date(user.premium.expires_at) > new Date()
  },

  /**
   * Récupérer le plan actuel de l'utilisateur
   * @returns {Object|null} Plan actuel
   */
  getCurrentPlan() {
    const user = boutiqueUtils.getCurrentUser()
    return user?.premium || null
  },

  /**
   * Vérifier si l'utilisateur peut ajouter plus de produits
   * @param {number} currentProductCount - Nombre actuel de produits
   * @returns {boolean} Peut ajouter des produits
   */
  canAddMoreProducts(currentProductCount) {
    const plan = this.getCurrentPlan()
    if (!plan) return currentProductCount < 5 // Plan gratuit: 5 produits max

    return currentProductCount < plan.max_products
  },

  /**
   * Formater le prix d'un plan
   * @param {number} price - Prix en FCFA
   * @returns {string} Prix formaté
   */
  formatPrice(price) {
    return new Intl.NumberFormat("fr-FR", {
      style: "currency",
      currency: "XOF",
      minimumFractionDigits: 0,
      maximumFractionDigits: 0,
    })
      .format(price || 0)
      .replace(/\s/g, " ")
  },
}

// Service API pour les catégories (VERSION COMPLÈTE AVEC 4 NIVEAUX)
export const categoriesApi = {
  /**
   * Récupérer toutes les catégories avec sous-catégories (hiérarchie complète)
   * @returns {Promise} Liste des catégories avec hiérarchie
   */
  async getCategories() {
    try {
      const response = await apiClient.get("/products.php", {
        params: { action: "categories" },
      })
      return response.data
    } catch (error) {
      throw productsApi.handleError(error, "Erreur lors de la récupération des catégories")
    }
  },

  /**
   * Récupérer les statistiques des catégories
   * @returns {Promise} Statistiques par niveau
   */
  async getCategoriesStats() {
    try {
      const response = await apiClient.get("/products.php", {
        params: { action: "categories_stats" },
      })
      return response.data
    } catch (error) {
      throw productsApi.handleError(error, "Erreur lors de la récupération des statistiques")
    }
  },

  // ========== NIVEAU 1: CATÉGORIES ==========

  /**
   * Récupérer toutes les catégories principales
   * @param {Object} params - Paramètres de filtrage et pagination
   * @returns {Promise} Liste des catégories
   */
  async getMainCategories(params = {}) {
    try {
      const response = await apiClient.get("/products.php", {
        params: {
          action: "main_categories",
          page: params.page || 1,
          limit: params.limit || 50,
          search: params.search || "",
        },
      })
      return response.data
    } catch (error) {
      throw productsApi.handleError(error, "Erreur lors de la récupération des catégories principales")
    }
  },

  /**
   * Créer une nouvelle catégorie principale
   * @param {Object} categoryData - Données de la catégorie
   * @returns {Promise} Catégorie créée
   */
  async createMainCategory(categoryData) {
    try {
      const response = await apiClient.post("/products.php", categoryData, {
        params: { action: "create_main_category" },
      })
      return response.data
    } catch (error) {
      throw productsApi.handleError(error, "Erreur lors de la création de la catégorie")
    }
  },

  /**
   * Mettre à jour une catégorie principale
   * @param {number} id - ID de la catégorie
   * @param {Object} categoryData - Nouvelles données
   * @returns {Promise} Catégorie mise à jour
   */
  async updateMainCategory(id, categoryData) {
    try {
      const response = await apiClient.put("/products.php", categoryData, {
        params: { action: "update_main_category", id },
      })
      return response.data
    } catch (error) {
      throw productsApi.handleError(error, "Erreur lors de la mise à jour de la catégorie")
    }
  },

  /**
   * Supprimer une catégorie principale
   * @param {number} id - ID de la catégorie
   * @returns {Promise} Résultat de la suppression
   */
  async deleteMainCategory(id) {
    try {
      const response = await apiClient.delete("/products.php", {
        params: { action: "delete_main_category", id },
      })
      return response.data
    } catch (error) {
      throw productsApi.handleError(error, "Erreur lors de la suppression de la catégorie")
    }
  },

  // ========== NIVEAU 2: SOUS-CATÉGORIES ==========

  /**
   * Récupérer les sous-catégories d'une catégorie
   * @param {number} categoryId - ID de la catégorie parente
   * @param {Object} params - Paramètres de filtrage et pagination
   * @returns {Promise} Liste des sous-catégories
   */
  async getSubcategories(categoryId, params = {}) {
    try {
      const response = await apiClient.get("/products.php", {
        params: {
          action: "subcategories",
          category_id: categoryId,
          page: params.page || 1,
          limit: params.limit || 50,
          search: params.search || "",
        },
      })
      return response.data
    } catch (error) {
      throw productsApi.handleError(error, "Erreur lors de la récupération des sous-catégories")
    }
  },

  /**
   * Créer une nouvelle sous-catégorie
   * @param {Object} subcategoryData - Données de la sous-catégorie
   * @returns {Promise} Sous-catégorie créée
   */
  async createSubcategory(subcategoryData) {
    try {
      const response = await apiClient.post("/products.php", subcategoryData, {
        params: { action: "create_subcategory" },
      })
      return response.data
    } catch (error) {
      throw productsApi.handleError(error, "Erreur lors de la création de la sous-catégorie")
    }
  },

  /**
   * Mettre à jour une sous-catégorie
   * @param {number} id - ID de la sous-catégorie
   * @param {Object} subcategoryData - Nouvelles données
   * @returns {Promise} Sous-catégorie mise à jour
   */
  async updateSubcategory(id, subcategoryData) {
    try {
      const response = await apiClient.put("/products.php", subcategoryData, {
        params: { action: "update_subcategory", id },
      })
      return response.data
    } catch (error) {
      throw productsApi.handleError(error, "Erreur lors de la mise à jour de la sous-catégorie")
    }
  },

  /**
   * Supprimer une sous-catégorie
   * @param {number} id - ID de la sous-catégorie
   * @returns {Promise} Résultat de la suppression
   */
  async deleteSubcategory(id) {
    try {
      const response = await apiClient.delete("/products.php", {
        params: { action: "delete_subcategory", id },
      })
      return response.data
    } catch (error) {
      throw productsApi.handleError(error, "Erreur lors de la suppression de la sous-catégorie")
    }
  },

  // ========== NIVEAU 3: SOUS-SOUS-CATÉGORIES ==========

  /**
   * Récupérer les sous-sous-catégories d'une sous-catégorie
   * @param {number} subcategoryId - ID de la sous-catégorie parente
   * @param {Object} params - Paramètres de filtrage et pagination
   * @returns {Promise} Liste des sous-sous-catégories
   */
  async getSubSubcategories(subcategoryId, params = {}) {
    try {
      const response = await apiClient.get("/products.php", {
        params: {
          action: "sub_subcategories",
          subcategory_id: subcategoryId,
          page: params.page || 1,
          limit: params.limit || 50,
          search: params.search || "",
        },
      })
      return response.data
    } catch (error) {
      throw productsApi.handleError(error, "Erreur lors de la récupération des sous-sous-catégories")
    }
  },

  /**
   * Créer une nouvelle sous-sous-catégorie
   * @param {Object} subSubcategoryData - Données de la sous-sous-catégorie
   * @returns {Promise} Sous-sous-catégorie créée
   */
  async createSubSubcategory(subSubcategoryData) {
    try {
      const response = await apiClient.post("/products.php", subSubcategoryData, {
        params: { action: "create_sub_subcategory" },
      })
      return response.data
    } catch (error) {
      throw productsApi.handleError(error, "Erreur lors de la création de la sous-sous-catégorie")
    }
  },

  /**
   * Mettre à jour une sous-sous-catégorie
   * @param {number} id - ID de la sous-sous-catégorie
   * @param {Object} subSubcategoryData - Nouvelles données
   * @returns {Promise} Sous-sous-catégorie mise à jour
   */
  async updateSubSubcategory(id, subSubcategoryData) {
    try {
      const response = await apiClient.put("/products.php", subSubcategoryData, {
        params: { action: "update_sub_subcategory", id },
      })
      return response.data
    } catch (error) {
      throw productsApi.handleError(error, "Erreur lors de la mise à jour de la sous-sous-catégorie")
    }
  },

  /**
   * Supprimer une sous-sous-catégorie
   * @param {number} id - ID de la sous-sous-catégorie
   * @returns {Promise} Résultat de la suppression
   */
  async deleteSubSubcategory(id) {
    try {
      const response = await apiClient.delete("/products.php", {
        params: { action: "delete_sub_subcategory", id },
      })
      return response.data
    } catch (error) {
      throw productsApi.handleError(error, "Erreur lors de la suppression de la sous-sous-catégorie")
    }
  },

  // ========== NIVEAU 4: SOUS-SOUS-SOUS-CATÉGORIES ==========

  /**
   * Récupérer les sous-sous-sous-catégories d'une sous-sous-catégorie
   * @param {number} subSubcategoryId - ID de la sous-sous-catégorie parente
   * @param {Object} params - Paramètres de filtrage et pagination
   * @returns {Promise} Liste des sous-sous-sous-catégories
   */
  async getSubSubSubcategories(subSubcategoryId, params = {}) {
    try {
      const response = await apiClient.get("/products.php", {
        params: {
          action: "sub_sub_subcategories",
          sub_subcategory_id: subSubcategoryId,
          page: params.page || 1,
          limit: params.limit || 50,
          search: params.search || "",
        },
      })
      return response.data
    } catch (error) {
      throw productsApi.handleError(error, "Erreur lors de la récupération des sous-sous-sous-catégories")
    }
  },

  /**
   * Créer une nouvelle sous-sous-sous-catégorie
   * @param {Object} subSubSubcategoryData - Données de la sous-sous-sous-catégorie
   * @returns {Promise} Sous-sous-sous-catégorie créée
   */
  async createSubSubSubcategory(subSubSubcategoryData) {
    try {
      const response = await apiClient.post("/products.php", subSubSubcategoryData, {
        params: { action: "create_sub_sub_subcategory" },
      })
      return response.data
    } catch (error) {
      throw productsApi.handleError(error, "Erreur lors de la création de la sous-sous-sous-catégorie")
    }
  },

  /**
   * Mettre à jour une sous-sous-sous-catégorie
   * @param {number} id - ID de la sous-sous-sous-catégorie
   * @param {Object} subSubSubcategoryData - Nouvelles données
   * @returns {Promise} Sous-sous-sous-catégorie mise à jour
   */
  async updateSubSubSubcategory(id, subSubSubcategoryData) {
    try {
      const response = await apiClient.put("/products.php", subSubSubcategoryData, {
        params: { action: "update_sub_sub_subcategory", id },
      })
      return response.data
    } catch (error) {
      throw productsApi.handleError(error, "Erreur lors de la mise à jour de la sous-sous-sous-catégorie")
    }
  },

  /**
   * Supprimer une sous-sous-sous-catégorie
   * @param {number} id - ID de la sous-sous-sous-catégorie
   * @returns {Promise} Résultat de la suppression
   */
  async deleteSubSubSubcategory(id) {
    try {
      const response = await apiClient.delete("/products.php", {
        params: { action: "delete_sub_sub_subcategory", id },
      })
      return response.data
    } catch (error) {
      throw productsApi.handleError(error, "Erreur lors de la suppression de la sous-sous-sous-catégorie")
    }
  },

  // ========== MÉTHODES UTILITAIRES ==========

  /**
   * Upload d'une image pour une catégorie
   * @param {File} file - Fichier image
   * @param {string} level - Niveau de catégorie ('main', 'sub', 'sub_sub', 'sub_sub_sub')
   * @param {number} categoryId - ID de la catégorie (optionnel)
   * @returns {Promise} URL de l'image uploadée
   */
  async uploadCategoryImage(file, level, categoryId = null) {
    try {
      const formData = new FormData()
      formData.append("image", file)
      formData.append("level", level)

      if (categoryId) {
        formData.append("category_id", categoryId)
      }

      const response = await apiClient.post("/products.php", formData, {
        params: { action: "upload_category_image" },
        headers: { "Content-Type": "multipart/form-data" },
      })
      return response.data
    } catch (error) {
      throw productsApi.handleError(error, "Erreur lors de l'upload de l'image")
    }
  },

  /**
   * Récupérer les catégories pour les filtres (format simplifié)
   * @returns {Promise} Liste des catégories simplifiée pour les filtres
   */
  async getCategoriesForFilters() {
    try {
      const response = await apiClient.get("/products.php", {
        params: { action: "categories_for_filters" },
      })
      return response.data
    } catch (error) {
      console.error("Erreur lors de la récupération des catégories pour filtres:", error)
      return {
        success: false,
        error: error.message || "Erreur lors de la récupération des catégories",
        data: [],
      }
    }
  },

  /**
   * Rechercher dans toutes les catégories
   * @param {string} query - Terme de recherche
   * @param {string} level - Niveau spécifique ('all', 'main', 'sub', 'sub_sub', 'sub_sub_sub')
   * @returns {Promise} Résultats de recherche
   */
  async searchCategories(query, level = "all") {
    try {
      const response = await apiClient.get("/products.php", {
        params: {
          action: "search_categories",
          search: query,
          level: level,
        },
      })
      return response.data
    } catch (error) {
      throw productsApi.handleError(error, "Erreur lors de la recherche de catégories")
    }
  },

  /**
   * Réorganiser l'ordre des catégories
   * @param {Array} orderedIds - Liste des IDs dans le nouvel ordre
   * @param {string} level - Niveau de catégorie
   * @returns {Promise} Résultat de la réorganisation
   */
  async reorderCategories(orderedIds, level) {
    try {
      const response = await apiClient.put(
        "/products.php",
        { ordered_ids: orderedIds },
        {
          params: { action: "reorder_categories", level },
        },
      )
      return response.data
    } catch (error) {
      throw productsApi.handleError(error, "Erreur lors de la réorganisation")
    }
  },

  /**
   * Exporter les catégories en CSV
   * @param {string} level - Niveau à exporter ('all', 'main', 'sub', 'sub_sub', 'sub_sub_sub')
   * @returns {Promise} Données CSV
   */
  async exportCategories(level = "all") {
    try {
      const response = await apiClient.get("/products.php", {
        params: { action: "export_categories", level },
        responseType: "blob",
      })
      return response.data
    } catch (error) {
      throw productsApi.handleError(error, "Erreur lors de l'export")
    }
  },

  /**
   * Importer des catégories depuis un CSV
   * @param {File} file - Fichier CSV
   * @param {string} level - Niveau d'import
   * @returns {Promise} Résultat de l'import
   */
  async importCategories(file, level) {
    try {
      const formData = new FormData()
      formData.append("csv_file", file)
      formData.append("level", level)

      const response = await apiClient.post("/products.php", formData, {
        params: { action: "import_categories" },
        headers: { "Content-Type": "multipart/form-data" },
      })
      return response.data
    } catch (error) {
      throw productsApi.handleError(error, "Erreur lors de l'import")
    }
  },
}

// Service API pour les boutiques (VERSION CORRIGÉE)
export const boutiquesApi = {
  /**
   * Récupérer les informations d'une boutique
   * @param {number} boutiqueId - ID de la boutique
   * @returns {Promise} Informations de la boutique
   */
  async getBoutiqueInfo(boutiqueId) {
    try {

      const response = await apiClient.get("/products.php", {
        params: {
          action: "boutique_info",
          boutique_id: boutiqueId,
        },
      })


      return {
        success: true,
        data: response.data.data || response.data,
      }
    } catch (error) {
      console.error("❌ Erreur lors de la récupération des informations de la boutique:", error)
      return {
        success: false,
        error: error.response?.data?.error || error.message || "Boutique introuvable",
        data: null,
      }
    }
  },

  /**
   * Récupérer les produits d'une boutique
   * @param {Object} params - Paramètres de filtrage et pagination
   * @returns {Promise} Produits de la boutique
   */
  async getBoutiqueProducts(params = {}) {
    try {

      const queryParams = {
        action: "boutique_products",
        id: params.boutique_id,
        page: params.page || 1,
        limit: params.limit || 20,
      }

      // Ajouter les paramètres de recherche et tri si présents
      if (params.search) queryParams.search = params.search
      if (params.sort) queryParams.sort = params.sort
      if (params.order) queryParams.order = params.order

      const response = await apiClient.get("/products.php", { params: queryParams })


      return {
        success: true,
        data: response.data.data || response.data,
        pagination: response.data.pagination || {
          total: response.data.data?.length || 0,
          total_pages: Math.ceil((response.data.data?.length || 0) / (params.limit || 20)),
          current_page: params.page || 1,
          per_page: params.limit || 20,
        },
      }
    } catch (error) {
      console.error("❌ Erreur lors de la récupération des produits de la boutique:", error)
      return {
        success: false,
        error: error.response?.data?.error || error.message || "Erreur lors de la récupération des produits",
        data: [],
      }
    }
  },

  /**
   * Suivre une boutique
   * @param {number} boutiqueId - ID de la boutique à suivre
   * @returns {Promise} Résultat de l'opération
   */
  async followBoutique(boutiqueId) {
    try {
      const response = await apiClient.post(
        "/products.php",
        {},
        {
          params: {
            action: "follow_boutique",
            boutique_id: boutiqueId,
          },
        },
      )

      return {
        success: true,
        data: response.data,
      }
    } catch (error) {
      return {
        success: false,
        error: error.response?.data?.error || error.message || "Erreur lors du suivi de la boutique",
      }
    }
  },

  /**
   * Ne plus suivre une boutique
   * @param {number} boutiqueId - ID de la boutique à ne plus suivre
   * @returns {Promise} Résultat de l'opération
   */
  async unfollowBoutique(boutiqueId) {
    try {
      const response = await apiClient.post(
        "/products.php",
        {},
        {
          params: {
            action: "unfollow_boutique",
            boutique_id: boutiqueId,
          },
        },
      )

      return {
        success: true,
        data: response.data,
      }
    } catch (error) {
      return {
        success: false,
        error: error.response?.data?.error || error.message || "Erreur lors du désabonnement de la boutique",
      }
    }
  },

  /**
   * Contacter une boutique
   * @param {number} boutiqueId - ID de la boutique
   * @param {Object} messageData - Données du message
   * @returns {Promise} Résultat de l'opération
   */
  async contactBoutique(boutiqueId, messageData) {
    try {
      const response = await apiClient.post("/products.php", messageData, {
        params: {
          action: "contact_boutique",
          boutique_id: boutiqueId,
        },
      })

      return {
        success: true,
        data: response.data,
      }
    } catch (error) {
      return {
        success: false,
        error: error.response?.data?.error || error.message || "Erreur lors de l'envoi du message",
      }
    }
  },

  /**
   * Récupérer les boutiques populaires
   * @param {Object} params - Paramètres de filtrage et pagination
   * @returns {Promise} Boutiques populaires
   */
  async getPopularBoutiques(params = {}) {
    try {
      const response = await apiClient.get("/products.php", {
        params: {
          action: "popular_boutiques",
          limit: params.limit || 10,
          page: params.page || 1,
        },
      })

      return {
        success: true,
        data: response.data.data || response.data,
      }
    } catch (error) {
      return {
        success: false,
        error:
          error.response?.data?.error || error.message || "Erreur lors de la récupération des boutiques populaires",
      }
    }
  },
}

// ✅ NOUVEAU: APIs pour le système de boost
export const boostApi = {
  // Créer une demande de boost
  async createBoostRequest(productId, boostData, params = {}) {
    try {
      const url = `https://sastock.com/api_adjame/products.php?action=boostproduct&product_id=${productId}&boutique_id=${params.boutique_id}`
      const response = await fetch(url, {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify(boostData),
      })
      return await response.json()
    } catch (error) {
      return { success: false, error: error.message }
    }
  },

  // Récupérer l'historique des boosts d'un produit
  async getProductBoosts(productId, params = {}) {
    try {
      const url = `/api/products.php?action=getproductboosts&product_id=${productId}&boutique_id=${params.boutique_id}`
      const response = await fetch(url)
      return await response.json()
    } catch (error) {
      return { success: false, error: error.message }
    }
  },

  // Récupérer tous les boosts d'une boutique
  async getBoutiqueBoosts(params = {}) {
    try {
      const url = `/api/products.php?action=getboutiqueboosts&boutique_id=${params.boutique_id}`
      const response = await fetch(url)
      return await response.json()
    } catch (error) {
      return { success: false, error: error.message }
    }
  },

  // Annuler une demande de boost
  async cancelBoostRequest(boostId, params = {}) {
    try {
      const url = `/api/products.php?action=cancelboost&boost_id=${boostId}&boutique_id=${params.boutique_id}`
      const response = await fetch(url, { method: "DELETE" })
      return await response.json()
    } catch (error) {
      return { success: false, error: error.message }
    }
  },
}

// Service API pour les couleurs
export const colorsApi = {
  /**
   * Récupérer toutes les couleurs disponibles
   * @returns {Promise} Liste des couleurs
   */
  async getColors() {
    try {
      const response = await apiClient.get("/products.php", {
        params: { action: "colors" },
      })
      return response.data
    } catch (error) {
      throw productsApi.handleError(error, "Erreur lors de la récupération des couleurs")
    }
  },
}

// Service API pour les tailles
export const sizesApi = {
  /**
   * Récupérer toutes les tailles disponibles
   * @returns {Promise} Liste des tailles groupées par catégorie
   */
  async getSizes() {
    try {
      const response = await apiClient.get("/products.php", {
        params: { action: "sizes" },
      })
      return response.data
    } catch (error) {
      throw productsApi.handleError(error, "Erreur lors de la récupération des tailles")
    }
  },
}

// Service API pour les marques
export const brandsApi = {
  /**
   * Récupérer toutes les marques disponibles
   * @returns {Promise} Liste des tailles groupées par catégorie
   */
  async getBrands() {
    try {
      const response = await apiClient.get("/products.php", {
        params: { action: "model_list" },
      })
      return response.data
    } catch (error) {
      throw productsApi.handleError(error, "Erreur lors de la récupération des Marques")
    }
  },
}

// Service API pour les statistiques (VERSION MISE À JOUR)
export const statsApi = {
  /**
   * Récupérer les statistiques des produits
   * @param {string} period - Période ('all', 'today', 'week', 'month')
   * @returns {Promise} Statistiques
   */
  async getStats(period = "all") {
    try {
      // Ajouter boutique_id aux paramètres
      const baseParams = boutiqueUtils.buildBaseParams()

      const response = await apiClient.get("/products.php", {
        params: {
          action: "stats",
          period,
          ...baseParams,
        },
      })
      return response.data
    } catch (error) {
      throw productsApi.handleError(error, "Erreur lors de la récupération des statistiques")
    }
  },
}

// Service API pour les utilisateurs (VERSION COMPLÈTE)
export const usersApi = {
  /**
   * Vérifier si un email existe déjà
   * @param {string} email - Email à vérifier
   * @returns {Promise} Résultat de la vérification
   */
  async checkEmail(email) {
    try {
      const response = await apiClient.get("/users.php", {
        params: {
          action: "check-email",
          email,
        },
      })
      return response.data
    } catch (error) {
      throw this.handleError(error, "Erreur lors de la vérification de l'email")
    }
  },

  /**
   * Vérifier si un email existe déjà
   * @param {string} email - Email à vérifier
   * @returns {Promise} Résultat de la vérification
   */
  async checkEmail2(email) {
    try {
      const response = await apiClient.get("/users.php", {
        params: {
          action: "check-client",
          email,
        },
      })
      return response.data
    } catch (error) {
      throw this.handleError(error, "Erreur lors de la vérification de l'email")
    }
  },

  /**
   * Enregistrer un nouvel utilisateur
   * @param {Object} userData - Données de l'utilisateur
   * @returns {Promise} Utilisateur créé
   */
  async register(userData) {
    try {
      const response = await apiClient.post("/users.php?action=register", userData)
      return response.data
    } catch (error) {
      throw this.handleError(error, "Erreur lors de la création du compte")
    }
  },

  /**
   * Enregistrer un nouvel utilisateur
   * @param {Object} userData - Données de l'utilisateur
   * @returns {Promise} Utilisateur créé
   */
  async addUserToBoutique(userData) {
    try {
      const response = await apiClient.post("/users.php?action=add_user", userData)
      return response.data
    } catch (error) {
      throw this.handleError(error, "Erreur lors de la création du compte")
    }
  },

  /**
   * Enregistrer un nouvel utilisateur
   * @param {Object} userData - Données de l'utilisateur
   * @returns {Promise} Utilisateur créé
   */
  async register_client(userData) {
    try {
      const response = await apiClient.post("/users.php?action=register_client", userData)
      return response.data
    } catch (error) {
      throw this.handleError(error, "Erreur lors de la création du compte")
    }
  },

  /**
   * Connecter un utilisateur
   * @param {Object} loginData - Données de connexion
   * @returns {Promise} Informations de l'utilisateur connecté
   */
  async login(loginData) {
    try {
      const response = await apiClient.post("/users.php?action=login", loginData)

      // Sauvegarder le token et les données utilisateur si la connexion réussit
      if (response.data.success && response.data.data?.token) {
        localStorage.setItem("auth_token", response.data.data.token)
        localStorage.setItem("user_data", JSON.stringify(response.data.data.user))
      }

      return response.data
    } catch (error) {
      throw this.handleError(error, "Erreur lors de la connexion")
    }
  },

  /**
   * Connecter un utilisateur
   * @param {Object} loginData - Données de connexion
   * @returns {Promise} Informations de l'utilisateur connecté
   */
  async login_client(loginData) {
    try {
      const response = await apiClient.post("/users.php?action=login_client", loginData)

      // Sauvegarder le token et les données utilisateur si la connexion réussit
      if (response.data.success && response.data.data?.token) {
        localStorage.setItem("auth_token", response.data.data.token)
        localStorage.setItem("user_data", JSON.stringify(response.data.data.user))
      }

      return response.data
    } catch (error) {
      throw this.handleError(error, "Erreur lors de la connexion")
    }
  },

  /**
   * Déconnecter un utilisateur
   * @returns {Promise} Résultat de la déconnexion
   */
  async logout() {
    try {
      const response = await apiClient.post("/users.php?action=logout")

      // Supprimer les données locales
      localStorage.removeItem("auth_token")
      localStorage.removeItem("user_data")

      return response.data
    } catch (error) {
      // Même en cas d'erreur, on supprime les données locales
      localStorage.removeItem("auth_token")
      localStorage.removeItem("user_data")
      throw this.handleError(error, "Erreur lors de la déconnexion")
    }
  },

  /**
   * Demander une réinitialisation de mot de passe
   * @param {string} email - Email de l'utilisateur
   * @returns {Promise} Résultat de la demande
   */
  async forgotPassword(email) {
    try {
      const response = await apiClient.post("/users.php?action=forgot-password", { email })
      return response.data
    } catch (error) {
      throw this.handleError(error, "Erreur lors de la demande de réinitialisation")
    }
  },

  /**
   * Réinitialiser le mot de passe
   * @param {Object} resetData - Données de réinitialisation
   * @returns {Promise} Résultat de la réinitialisation
   */
  async resetPassword(resetData) {
    try {
      const response = await apiClient.post("/users.php?action=reset-password", resetData)
      return response.data
    } catch (error) {
      throw this.handleError(error, "Erreur lors de la réinitialisation du mot de passe")
    }
  },

  /**
   * Récupérer le profil utilisateur
   * @param {number} userId - ID de l'utilisateur
   * @returns {Promise} Données du profil
   */
  async getProfile(userId) {
    try {
      const response = await apiClient.get("/users.php", {
        params: {
          action: "profile",
          user_id: userId,
        },
      })
      return response.data
    } catch (error) {
      throw this.handleError(error, "Erreur lors de la récupération du profil")
    }
  },

  async getUsersByBoutique(boutique_id) {
    try {
      const response = await apiClient.get("/users.php", {
        params: {
          action: "boutiques-user",
          boutique: boutique_id,
        },
      })
      return response.data
    } catch (error) {
      throw this.handleError(error, "Erreur lors de la récupération du profil")
    }
  },

  /**
   * Mettre à jour le profil utilisateur
   * @param {number} userId - ID de l'utilisateur
   * @param {Object} profileData - Nouvelles données du profil
   * @returns {Promise} Profil mis à jour
   */
  async updateProfile(userId, profileData) {
    try {
      const response = await apiClient.put(`/users.php?action=update-profile&user_id=${userId}`, profileData)
      return response.data
    } catch (error) {
      throw this.handleError(error, "Erreur lors de la mise à jour du profil")
    }
  },

  /**
   * Récupérer les boutiques de l'utilisateur
   * @param {number} userId - ID de l'utilisateur
   * @returns {Promise} Liste des boutiques
   */
  async getUserBoutiques(userId) {
    try {
      const response = await apiClient.get("/users.php", {
        params: {
          action: "boutiques",
          user_id: userId,
        },
      })
      return response.data
    } catch (error) {
      throw this.handleError(error, "Erreur lors de la récupération des boutiques")
    }
  },

  /**
   * Inviter un utilisateur à rejoindre une boutique
   * @param {Object} invitationData - Données de l'invitation
   * @returns {Promise} Résultat de l'invitation
   */
  async inviteUser(invitationData) {
    try {
      const response = await apiClient.post("/users.php?action=invite", invitationData)
      return response.data
    } catch (error) {
      throw this.handleError(error, "Erreur lors de l'envoi de l'invitation")
    }
  },

  /**
   * Accepter une invitation
   * @param {Object} acceptData - Données d'acceptation
   * @returns {Promise} Résultat de l'acceptation
   */
  async acceptInvitation(acceptData) {
    try {
      const response = await apiClient.post("/users.php?action=accept-invitation", acceptData)
      return response.data
    } catch (error) {
      throw this.handleError(error, "Erreur lors de l'acceptation de l'invitation")
    }
  },

  /**
   * Refuser une invitation
   * @param {Object} rejectData - Données de refus
   * @returns {Promise} Résultat du refus
   */
  async rejectInvitation(rejectData) {
    try {
      const response = await apiClient.post("/users.php?action=reject-invitation", rejectData)
      return response.data
    } catch (error) {
      throw this.handleError(error, "Erreur lors du refus de l'invitation")
    }
  },

  /**
   * Récupérer les invitations en attente
   * @param {number} userId - ID de l'utilisateur
   * @returns {Promise} Liste des invitations
   */
  async getPendingInvitations(userId) {
    try {
      const response = await apiClient.get("/users.php", {
        params: {
          action: "pending-invitations",
          user_id: userId,
        },
      })
      return response.data
    } catch (error) {
      throw this.handleError(error, "Erreur lors de la récupération des invitations")
    }
  },

  /**
   * Changer le mot de passe
   * @param {Object} passwordData - Données du changement de mot de passe
   * @returns {Promise} Résultat du changement
   */
  async changePassword(passwordData) {
    try {
      const response = await apiClient.post("/users.php?action=change-password", passwordData)
      return response.data
    } catch (error) {
      throw this.handleError(error, "Erreur lors du changement de mot de passe")
    }
  },

  /**
   * Vérifier le token d'authentification
   * @returns {Promise} Résultat de la vérification
   */
  async verifyToken() {
    try {
      const response = await apiClient.get("/users.php", {
        params: { action: "verify-token" },
      })
      return response.data
    } catch (error) {
      throw this.handleError(error, "Token invalide")
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

// Utilitaires pour les requêtes
export const apiUtils = {
  /**
   * Construire les paramètres de pagination
   * @param {number} page - Numéro de page
   * @param {number} limit - Nombre d'éléments par page
   * @returns {Object} Paramètres de pagination
   */
  buildPaginationParams(page = 1, limit = 10) {
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
   * Construire les paramètres de filtrage
   * @param {Object} filters - Filtres à appliquer
   * @returns {Object} Paramètres de filtrage nettoyés
   */
  buildFilterParams(filters = {}) {
    const cleanFilters = {}

    Object.keys(filters).forEach((key) => {
      const value = filters[key]
      if (value !== null && value !== undefined && value !== "") {
        cleanFilters[key] = value
      }
    })

    return cleanFilters
  },

  /**
   * Formater les données de produit pour l'envoi
   * @param {Object} productData - Données brutes du produit
   * @returns {Object} Données formatées
   */
  formatProductData(productData) {
    return {
      ...productData,
      unit_price: Number.parseFloat(productData.unit_price) || 0,
      wholesale_price: productData.wholesale_price ? Number.parseFloat(productData.wholesale_price) : null,
      stock: Number.parseInt(productData.stock) || 0,
      wholesale_min_qty: productData.wholesale_min_qty ? Number.parseInt(productData.wholesale_min_qty) : null,
      is_active: Boolean(productData.is_active),
    }
  },

  /**
   * Générer un cache buster unique
   * @returns {string} Cache buster
   */
  generateCacheBuster() {
    return generateCacheBuster()
  },
}

// Utilitaires d'authentification
export const authUtils = {
  /**
   * Vérifier si l'utilisateur est connecté
   * @returns {boolean} État de connexion
   */
  isAuthenticated() {
    return !!localStorage.getItem("auth_token")
  },

  /**
   * Récupérer le token d'authentification
   * @returns {string|null} Token
   */
  getToken() {
    return localStorage.getItem("auth_token")
  },

  /**
   * Récupérer les données utilisateur
   * @returns {Object|null} Données utilisateur
   */
  getUser() {
    const userData = localStorage.getItem("user_data")
    return userData ? JSON.parse(userData) : null
  },

  /**
   * Déconnecter l'utilisateur (côté client)
   */
  logout() {
    localStorage.removeItem("auth_token")
    localStorage.removeItem("user_data")
  },
}

// Export par défaut de l'instance Axios configurée
export default apiClient

// Export de toutes les APIs
export { apiClient }
