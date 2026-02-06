import { defineStore } from 'pinia'
import { productsApi } from '../services/api'

export const useFavoritesStore = defineStore('favorites', {
  state: () => ({
    favorites: [],
    loading: false,
  }),

  getters: {
    count: (state) => state.favorites.length,
  },

  actions: {
    async fetchFavorites(userId) {
      this.loading = true
      try {
        const res = await productsApi.getFavorites(userId)
        if (res.success) {
          this.favorites = res.data
        } else {
          this.favorites = []
        }
      } catch (e) {
        console.error('fetchFavorites error', e)
        this.favorites = []
      } finally {
        this.loading = false
      }
    },

    addLocalFavorite(product) {
        const exists = this.favorites.some(f => f.id_produit === product.id_produit)
        if (!exists) {
            this.favorites.push(product)
        }
    },

    async toggleFavorite(productId, userId) {
      const res = await productsApi.addLike({
        id_produit: productId,
        user_id: userId,
      })

      if (res.success) {
        await this.fetchFavorites(userId)
      }

      return res
    },
  },
})
