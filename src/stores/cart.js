import { ref, computed, watch } from 'vue'
import { defineStore } from 'pinia'

export const useCartStore = defineStore('cart', () => {
  // charge depuis localStorage au démarrage
  const stored = localStorage.getItem('cart_items')
  const items = ref(stored ? JSON.parse(stored) : [])

  // getters
  const itemCount = computed(() => items.value.reduce((acc, i) => acc + (Number(i.quantity || 0)), 0))
  const grandTotal = computed(() => items.value.reduce((acc, i) => acc + (Number(i.unit_price || 0) * Number(i.quantity || 0)), 0))

  // groupe par boutique
  const grouped = computed(() => {
    const byBoutique = new Map()
      ;(items.value || []).forEach((item) => {
        const key = item.boutique_id ?? 'unknown'
        if (!byBoutique.has(key)) {
          byBoutique.set(key, {
            boutique_id: key,
            boutique_name: item.boutique_name || `Boutique ${key}`,
            boutique_logo: item.boutique_logo,
            items: [],
            subtotal: 0
          })
        }
        const g = byBoutique.get(key)
        g.items.push(item)
        g.subtotal += Number(item.unit_price || 0) * Number(item.quantity || 0)
      })
      return Array.from(byBoutique.values())
  })

  // helpers
  const save = () => localStorage.setItem('cart_items', JSON.stringify(items.value))

  const addItem = (product) => {
    const idx = items.value.findIndex(i => i.product_id === product.id && i.boutique_id === product.boutique_id)
    if (idx !== -1) {
      // incrémente la quantité si même produit/boutique
      items.value[idx].quantity = Number(items.value[idx].quantity || 0) + Number(product.quantity || 1)
    } else {
      items.value.push({
        boutique_id: product.boutique_id ?? null,
        boutique_name: product.boutique_name || '',
        boutique_logo: product.boutique_logo || 'https://images.unsplash.com/photo-1560472354-b33ff0c44a43?w=100&h=100&fit=crop&auto=format',
        item_id: product.id || `ci_${Date.now()}`,
        product_id: product.id,
        product_slug: product.slug,
        product_name: product.name,
        product_image: product.primary_image || '',
        unit_price: Number(product.unit_price || 0),
        quantity: Number(product.quantity || 1),
        stock_number: product.stock_number || '',
        trim_numbers: product.trim_numbers[0] || '',
        vin_numbers: product.vin_numbers[0] || '',
        vehicle_year: product.vehicle_year || '',
        vehicle_make: product.vehicle_make || '',
        vehicle_model: product.vehicle_model || '',
        vehicle_condition: product.vehicle_condition || '',
        vehicle_mileage: product.vehicle_mileage || '',
        transmission_type: product.transmission_type || '',
        fuel_type: product.fuel_type || '',
        power: product.power || '',
        country_of_origin: product.country_of_origin || '',
      })
    }
    save()
  }

  const removeItem = (itemId) => {
    items.value = items.value.filter(i => i.item_id !== itemId)
    save()
  }

  const increaseQty = (itemId, qty) => {
    const i = items.value.find(it => it.item_id === itemId)
    if (i) {
      i.quantity = (Number(qty) || 0)
      save()
    }
}

const decreaseQty = (itemId, qty) => {
    const i = items.value.find(it => it.item_id === itemId)
    if (i) {
        if (qty!==0) {
            i.quantity = (Number(qty) || 0)
        } else {
            i.quantity = 1
        }
      save()
    }
}

  const clear = () => {
    items.value = []
    save()
  }

  // persistance automatique si modification manuelle du tableau
  watch(items, save, { deep: true })

  return {
    items,
    itemCount,
    grandTotal,
    grouped,
    addItem,
    removeItem,
    decreaseQty,
    increaseQty,
    clear,
    save
  }
})