import { defineStore } from 'pinia'
import { ref } from 'vue'

export const useCurrencyStore = defineStore('currency', () => {
  // Devise globale (par défaut XOF)
  const currency = ref('USD')

  // Fonction pour changer la devise
  const setCurrency = (newCurrency) => {
    currency.value = newCurrency
  }

  return {
    currency,
    setCurrency
  }
})
