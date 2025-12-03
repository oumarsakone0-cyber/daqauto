import { ref, computed, watch } from 'vue'
import { defineStore } from 'pinia'

export const useOrdersStore = defineStore('orders', () => {
  // charge depuis localStorage au démarrage
  const stored = localStorage.getItem('items_ordered')
  const itemsOrdered = ref(stored ? JSON.parse(stored) : [])

  // getters
  const itemCount = computed(() => itemsOrdered.value.reduce((acc, i) => acc + (Number(i.quantity || 0)), 0))
  const total = computed(() => itemsOrdered.value.reduce((acc, i) => acc + (Number(i.unit_price || 0) * Number(i.quantity || 0)), 0))

  // groupe par boutique
  const grouped = computed(() => {
    const byBoutique = new Map()
      ;(itemsOrdered.value || []).forEach((item) => {
        const key = item.boutique_id ?? 'unknown'
        if (!byBoutique.has(key)) {
          byBoutique.set(key, {
            boutique_id: key,
            boutique_name: item.boutique_name || `Boutique ${key}`,
            boutique_logo: item.boutique_logo,
            boutique_marche: item.boutique_marche,
            boutique_type: item.boutique_type,
            boutique_premium: item.boutique_premium,
            boutique_verified: item.boutique_verified,
            boutique_address: item.boutique_address,
            boutique_description: item.boutique_description,
            boutique_phone: item.boutique_phone,
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
  const save = () => localStorage.setItem('items_ordered', JSON.stringify(itemsOrdered.value))

  const addOrder = (product) => {
    itemsOrdered.value = []
   
    for (let index = 0; index < product.length; index++) {
     itemsOrdered.value.push({
       item_id: product[index].id || `ci_${Date.now()}`,

       boutique_id: product[index].boutique_id ?? null,
       boutique_name: product[index].boutique_name || '',
       boutique_logo: product[index].boutique_logo || 'https://images.unsplash.com/photo-1560472354-b33ff0c44a43?w=100&h=100&fit=crop&auto=format',
       boutique_marche: product[index].boutique_marche,
       boutique_type: product[index].boutique_type,
       boutique_premium: product[index].boutique_premium,
       boutique_verified: product[index].boutique_verified,
       boutique_address: product[index].boutique_address,
       boutique_description: product[index].boutique_description,
       boutique_phone: product[index].boutique_phone,
      
       id: product[index].id,
       name: product[index].name,
       unit_price: Number(product[index].unit_price || 0),
       stock: product[index].stock,
       primary_image: product[index].primary_image || '',
       vehicle_make: product[index].vehicle_make || '',
       vehicle_model: product[index].vehicle_model || '',
       vehicle_year: product[index].vehicle_year || '',
       vin_number: product[index].vin_number || '',
       trim_number: product[index].trim_number || '',
       stock_number: product[index].stock_number || '',
       color: product[index].color || '',
       colorHex: product[index].colorHex || '',
       quantity: Number(product[index].quantity || 1),
       slug: product[index].slug,
       vehicle_mileage: product[index].vehicle_mileage || '',
       fuel_type: product[index].fuel_type || '',
     })
    
   }
    
    save()
  }

  const removeOrder = (itemId) => {
    itemsOrdered.value = itemsOrdered.value.filter(i => i.item_id !== itemId)
    save()
  }

  const increaseQty = (itemId, qty) => {
    const i = itemsOrdered.value.find(it => it.item_id === itemId)
    if (i) {
      i.quantity = (Number(qty) || 0)
      save()
    }
}

const decreaseQty = (itemId, qty) => {
    const i = itemsOrdered.value.find(it => it.item_id === itemId)
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
    itemsOrdered.value = []
    save()
  }

  // persistance automatique si modification manuelle du tableau
  watch(itemsOrdered, save, { deep: true })

  return {
    itemsOrdered,
    itemCount,
    total,
    grouped,
    addOrder,
    removeOrder,
    decreaseQty,
    increaseQty,
    clear,
    save
  }
})