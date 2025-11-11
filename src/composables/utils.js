

const formatDate = (dateString) => {
  if (!dateString) return 'JJ/MM/AAAA'
  const date = new Date(dateString)
  return new Intl.DateTimeFormat('fr-FR', {
    day: '2-digit',
    month: '2-digit',
    year: 'numeric'
  }).format(date)
}
const formatCurrency = (amount) => {
  return new Intl.NumberFormat('en-US', {
    style: 'currency',
    currency: 'USD',
    minimumFractionDigits: 0,
    maximumFractionDigits: 0
  }).format(amount || 0)
}
const capitalizeFirst=(str)=> {
  if (!str) return ''
  const s = String(str).trim()
  return s ? s[0].toUpperCase() + s.slice(1) : ''
}

export { formatDate, formatCurrency, capitalizeFirst }