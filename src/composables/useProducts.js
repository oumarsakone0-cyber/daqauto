import { ref, reactive, computed } from 'vue';
import { productsApi, apiUtils } from '../services/api.js';

/**
 * Composable pour la gestion des produits
 * @returns {Object} État et méthodes pour gérer les produits
 */
export function useProducts() {
  // État réactif
  const products = ref([]);
  const currentProduct = ref(null);
  const loading = ref(false);
  const error = ref(null);
  
  // État de pagination
  const pagination = reactive({
    currentPage: 1,
    perPage: 10,
    total: 0,
    totalPages: 0
  });
  
  // État de filtrage
  const filters = reactive({
    search: '',
    category: '',
    status: '',
    sort: 'created_at',
    order: 'DESC'
  });

  // Computed
  const hasProducts = computed(() => products.value.length > 0);
  const hasError = computed(() => error.value !== null);
  const isFirstPage = computed(() => pagination.currentPage === 1);
  const isLastPage = computed(() => pagination.currentPage >= pagination.totalPages);

  /**
   * Récupérer la liste des produits
   * @param {Object} params - Paramètres de requête
   */
  const fetchProducts = async (params = {}) => {
    try {
      loading.value = true;
      error.value = null;
      
      const queryParams = {
        ...apiUtils.buildPaginationParams(pagination.currentPage, pagination.perPage),
        ...apiUtils.buildSortParams(filters.sort, filters.order),
        ...apiUtils.buildFilterParams(filters),
        ...params
      };
      
      const response = await productsApi.getProducts(queryParams);
      
      if (response.success) {
        products.value = response.data;
        
        // Mettre à jour la pagination
        if (response.pagination) {
          Object.assign(pagination, response.pagination);
        }
      } else {
        throw new Error(response.error || 'Erreur lors de la récupération des produits');
      }
    } catch (err) {
      error.value = err.message;
      console.error('Erreur fetchProducts:', err);
    } finally {
      loading.value = false;
    }
  };

  /**
   * Récupérer un produit par ID
   * @param {number} id - ID du produit
   */
  const fetchProduct = async (id) => {
    try {
      loading.value = true;
      error.value = null;
      
      const response = await productsApi.getProduct(id);
      
      if (response.success) {
        currentProduct.value = response.data;
      } else {
        throw new Error(response.error || 'Produit non trouvé');
      }
    } catch (err) {
      error.value = err.message;
      console.error('Erreur fetchProduct:', err);
    } finally {
      loading.value = false;
    }
  };

  /**
   * Créer un nouveau produit
   * @param {Object} productData - Données du produit
   */
  const createProduct = async (productData) => {
    try {
      loading.value = true;
      error.value = null;
      
      const formattedData = apiUtils.formatProductData(productData);
      const response = await productsApi.createProduct(formattedData);
      
      if (response.success) {
        // Ajouter le nouveau produit à la liste
        products.value.unshift(response.data);
        return response.data;
      } else {
        throw new Error(response.error || 'Erreur lors de la création du produit');
      }
    } catch (err) {
      error.value = err.message;
      console.error('Erreur createProduct:', err);
      throw err;
    } finally {
      loading.value = false;
    }
  };

  /**
   * Mettre à jour un produit
   * @param {number} id - ID du produit
   * @param {Object} productData - Nouvelles données
   */
  const updateProduct = async (id, productData) => {
    try {
      loading.value = true;
      error.value = null;
      
      const formattedData = apiUtils.formatProductData(productData);
      const response = await productsApi.updateProduct(id, formattedData);
      
      if (response.success) {
        // Mettre à jour le produit dans la liste
        const index = products.value.findIndex(p => p.id === id);
        if (index !== -1) {
          products.value[index] = response.data;
        }
        
        // Mettre à jour le produit courant si c'est le même
        if (currentProduct.value?.id === id) {
          currentProduct.value = response.data;
        }
        
        return response.data;
      } else {
        throw new Error(response.error || 'Erreur lors de la mise à jour du produit');
      }
    } catch (err) {
      error.value = err.message;
      console.error('Erreur updateProduct:', err);
      throw err;
    } finally {
      loading.value = false;
    }
  };

  /**
   * Supprimer un produit
   * @param {number} id - ID du produit
   */
  const deleteProduct = async (id) => {
    try {
      loading.value = true;
      error.value = null;
      
      const response = await productsApi.deleteProduct(id);
      
      if (response.success) {
        // Retirer le produit de la liste
        products.value = products.value.filter(p => p.id !== id);
        
        // Réinitialiser le produit courant si c'est le même
        if (currentProduct.value?.id === id) {
          currentProduct.value = null;
        }
        
        return true;
      } else {
        throw new Error(response.error || 'Erreur lors de la suppression du produit');
      }
    } catch (err) {
      error.value = err.message;
      console.error('Erreur deleteProduct:', err);
      throw err;
    } finally {
      loading.value = false;
    }
  };

  /**
   * Rechercher des produits
   * @param {string} query - Terme de recherche
   */
  const searchProducts = async (query) => {
    filters.search = query;
    pagination.currentPage = 1;
    await fetchProducts();
  };

  /**
   * Appliquer des filtres
   * @param {Object} newFilters - Nouveaux filtres
   */
  const applyFilters = async (newFilters) => {
    Object.assign(filters, newFilters);
    pagination.currentPage = 1;
    await fetchProducts();
  };

  /**
   * Changer de page
   * @param {number} page - Numéro de page
   */
  const changePage = async (page) => {
    pagination.currentPage = page;
    await fetchProducts();
  };

  /**
   * Changer le tri
   * @param {string} sortBy - Champ de tri
   * @param {string} order - Ordre de tri
   */
  const changeSort = async (sortBy, order = 'DESC') => {
    filters.sort = sortBy;
    filters.order = order;
    pagination.currentPage = 1;
    await fetchProducts();
  };

  /**
   * Réinitialiser les filtres
   */
  const resetFilters = async () => {
    Object.assign(filters, {
      search: '',
      category: '',
      status: '',
      sort: 'created_at',
      order: 'DESC'
    });
    pagination.currentPage = 1;
    await fetchProducts();
  };

  /**
   * Rafraîchir les données
   */
  const refresh = async () => {
    await fetchProducts();
  };

  /**
   * Réinitialiser l'état
   */
  const reset = () => {
    products.value = [];
    currentProduct.value = null;
    error.value = null;
    pagination.currentPage = 1;
    pagination.total = 0;
    pagination.totalPages = 0;
  };

  return {
    // État
    products,
    currentProduct,
    loading,
    error,
    pagination,
    filters,
    
    // Computed
    hasProducts,
    hasError,
    isFirstPage,
    isLastPage,
    
    // Méthodes
    fetchProducts,
    fetchProduct,
    createProduct,
    updateProduct,
    deleteProduct,
    searchProducts,
    applyFilters,
    changePage,
    changeSort,
    resetFilters,
    refresh,
    reset
  };
}