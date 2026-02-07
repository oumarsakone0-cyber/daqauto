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
    return Promise.reject(error)
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
      return additionalParams
    }

    return {
      user_id: user.id,
      boutique_id: boutique.id,
      ...additionalParams,
    }
  },
}

/**
 * API dédiée pour la page de résultats
 */
export const resultsApi = {
  /**
   * Récupérer les produits avec filtres et pagination pour la page de résultats
   * @param {Object} options - Options de filtrage et pagination
   * @returns {Promise} Réponse de l'API
   */
  async getFilteredProducts(options = {}) {
    try {
      const {
        category,
        subcategory,
        sub_subcategory,
        product_select,
        page = 1,
        limit = 20,
        sort = "created_at",
        order = "DESC"
      } = options;

      // Construire les paramètres pour l'API
      const params = {
        action: "lists",
        page,
        limit
      };

      // Appliquer les filtres par priorité (sous-sous-catégorie > sous-catégorie > catégorie)
      if (sub_subcategory) {
        params.sub_subcategory_id = sub_subcategory;
      } else if (subcategory) {
        params.subcategory_id = subcategory;
      } else if (category) {
        params.category_id = category;
      }

      // Filtre par nom de produit
      if (product_select) {
        params.search = product_select;
      }

      // Tri
      params.sort = sort;
      params.order = order;


      const response = await apiClient.get("/products.php", { params });
      return response.data;
    } catch (error) {
      console.error('❌ Erreur lors de la récupération des produits:', error);
      throw this.handleError(error, "Erreur lors de la récupération des produits");
    }
  },

  /**
   * Récupérer les catégories pour la navigation et les filtres
   * @returns {Promise} Liste des catégories avec sous-catégories
   */
  async getCategories() {
    try {
      const response = await apiClient.get("/products.php", {
        params: { action: "categories" }
      });
      return response.data;
    } catch (error) {
      console.error('❌ Erreur lors de la récupération des catégories:', error);
      throw this.handleError(error, "Erreur lors de la récupération des catégories");
    }
  },

  /**
   * Récupérer les détails d'une catégorie spécifique
   * @param {number} categoryId - ID de la catégorie
   * @returns {Promise} Détails de la catégorie
   */
  async getCategoryDetails(categoryId) {
    try {
      const response = await apiClient.get("/products.php", {
        params: {
          action: "category_details",
          category_id: categoryId
        }
      });
      return response.data;
    } catch (error) {
      console.error('❌ Erreur lors de la récupération des détails de la catégorie:', error);
      throw this.handleError(error, "Erreur lors de la récupération des détails de la catégorie");
    }
  },

  /**
   * Récupérer les détails d'une sous-catégorie spécifique
   * @param {number} subcategoryId - ID de la sous-catégorie
   * @returns {Promise} Détails de la sous-catégorie
   */
  async getSubcategoryDetails(subcategoryId) {
    try {
      const response = await apiClient.get("/products.php", {
        params: {
          action: "subcategory_details",
          subcategory_id: subcategoryId
        }
      });
      return response.data;
    } catch (error) {
      console.error('❌ Erreur lors de la récupération des détails de la sous-catégorie:', error);
      throw this.handleError(error, "Erreur lors de la récupération des détails de la sous-catégorie");
    }
  },

  /**
   * Récupérer les détails d'une sous-sous-catégorie spécifique
   * @param {number} subSubcategoryId - ID de la sous-sous-catégorie
   * @returns {Promise} Détails de la sous-sous-catégorie
   */
  async getSubSubcategoryDetails(subSubcategoryId) {
    try {
      const response = await apiClient.get("/products.php", {
        params: {
          action: "sub_subcategory_details",
          sub_subcategory_id: subSubcategoryId
        }
      });
      return response.data;
    } catch (error) {
      console.error('❌ Erreur lors de la récupération des détails de la sous-sous-catégorie:', error);
      throw this.handleError(error, "Erreur lors de la récupération des détails de la sous-sous-catégorie");
    }
  },

  /**
   * Rechercher des produits par nom
   * @param {string} query - Terme de recherche
   * @param {Object} options - Options additionnelles (pagination, tri)
   * @returns {Promise} Résultats de recherche
   */
  async searchProducts(query, options = {}) {
    try {
      const {
        page = 1,
        limit = 20,
        sort = "created_at",
        order = "DESC"
      } = options;

      const params = {
        action: "search",
        search: query,
        page,
        limit,
        sort,
        order
      };


      const response = await apiClient.get("/products.php", { params });
      return response.data;
    } catch (error) {
      console.error('❌ Erreur lors de la recherche de produits:', error);
      throw this.handleError(error, "Erreur lors de la recherche de produits");
    }
  },

  /**
   * Formater les données de produit pour l'affichage
   * @param {Array} apiProducts - Produits bruts de l'API
   * @returns {Array} Produits formatés pour l'affichage
   */
  formatProducts(apiProducts) {
    return apiProducts.map(product => ({
      id: product.id,
      name: product.name,
      slug: product.slug || this.generateSlug(product.name),
      images: this.getProductImages(product),
      currentImageIndex: 0,
      unitPrice: parseFloat(product.unit_price) || 0,
      wholesalePrice: parseFloat(product.wholesale_price) || parseFloat(product.unit_price) * 0.8,
      minQuantity: product.wholesale_min_qty || 1,
      freeShipping: Math.random() > 0.5, // Simulation
      isAd: product.is_promoted || false,
      tradeAssurance: true,
      rating: (4.0 + Math.random() * 1).toFixed(1), // Simulation entre 4.0 et 5.0
      reviews: Math.floor(Math.random() * 5000) + 100,
      shopName: product.boutique_name || 'Boutique Partenaire',
      supplier: {
        name: product.boutique_name || 'Fournisseur',
        years: Math.floor(Math.random() * 10) + 1,
        country: 'CI',
        flag: 'https://flagcdn.com/w20/ci.png'
      },
      description: product.description,
      location: this.getProductLocation(product),
      createdAt: product.created_at,
      views: product.views || 0
    }));
  },

  /**
   * Extraire les images d'un produit
   * @param {Object} product - Produit brut de l'API
   * @returns {Array} Liste des URLs d'images
   */
  getProductImages(product) {
    if (product.images && Array.isArray(product.images) && product.images.length > 0) {
      return product.images.map(img => img.image_url || img);
    } else if (product.primary_image) {
      return [product.primary_image];
    } else if (product.image_url) {
      return [product.image_url];
    }
    return ['https://via.placeholder.com/300x300?text=Pas+d%27image'];
  },

  /**
   * Obtenir la localisation d'un produit
   * @param {Object} product - Produit brut de l'API
   * @returns {string} Localisation formatée
   */
  getProductLocation(product) {
    if (product.city_name) {
      return product.commune_name ? `${product.city_name}, ${product.commune_name}` : product.city_name;
    }
    return 'Côte d\'Ivoire';
  },

  /**
   * Générer un slug à partir d'un nom
   * @param {string} name - Nom du produit
   * @returns {string} Slug généré
   */
  generateSlug(name) {
    if (!name) return null;
    
    return name
      .toLowerCase()
      .trim()
      .replace(/[àáâãäå]/g, 'a')
      .replace(/[èéêë]/g, 'e')
      .replace(/[ìíîï]/g, 'i')
      .replace(/[òóôõö]/g, 'o')
      .replace(/[ùúûü]/g, 'u')
      .replace(/[ç]/g, 'c')
      .replace(/[ñ]/g, 'n')
      .replace(/[^a-z0-9\s-]/g, '')
      .replace(/\s+/g, '-')
      .replace(/-+/g, '-')
      .replace(/^-|-$/g, '');
  },

  /**
   * Gestion des erreurs
   * @param {Error} error - Erreur capturée
   * @param {string} defaultMessage - Message par défaut
   * @returns {Error} Erreur formatée
   */
  handleError(error, defaultMessage) {
    const message = error.response?.data?.error || error.message || defaultMessage;
    const statusCode = error.response?.status || 500;

    const formattedError = new Error(message);
    formattedError.statusCode = statusCode;
    formattedError.originalError = error;

    return formattedError;
  }
};

export default resultsApi;