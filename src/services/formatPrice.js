import { useCurrencyStore } from '../stores/currency'

// Taux et symboles
const exchangeRates = { USD: 1, XOF: 600, EUR: 0.001524, NGN: 450 }
const currencySymbols = { USD: '$', XOF: 'F CFA', EUR: '€', NGN: '₦' }

export function formatPrice(priceInUSD, options = { showFOB: false }) {
  const currencyStore = useCurrencyStore() // <-- pas de destructuration
  const cur = currencyStore.currency || 'USD'

  if (!priceInUSD) return `0 ${currencySymbols[cur]}`

  const rate = exchangeRates[cur] || 1
  const converted = priceInUSD * rate

  let formatted = ''
  if (['USD', 'EUR'].includes(cur)) {
    formatted = new Intl.NumberFormat('en-US', {
      style: 'currency',
      currency: cur,
      minimumFractionDigits: 0
    }).format(converted)
  } else {
    formatted = `${Math.round(converted).toLocaleString('en-US')} ${currencySymbols[cur]}`
  }

  if (options.showFOB) formatted += ' FOB'

  return formatted
}
