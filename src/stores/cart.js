// stores/cart.js
import { ref, computed, watch } from "vue"
import { defineStore } from "pinia"
import axios from "axios"

const API_URL = "https://sastock.com/api_adjame/products.php"

const useCartStore = defineStore("cart", () => {
  const items = ref([])
  const isLoading = ref(false) // <CHANGE> Ajout état de chargement

  const saveLocal = () => {
    localStorage.setItem("cart", JSON.stringify(items.value))
  }

  const getCurrentUser = () => {
    try {
      const userData =
        localStorage.getItem("user") ||
        sessionStorage.getItem("user") ||
        localStorage.getItem("user_data") ||
        sessionStorage.getItem("user_data")
      return userData ? JSON.parse(userData) : null
    } catch (error) {
      console.error("Erreur lors de la récupération de l'utilisateur:", error)
      return null
    }
  }

  const isUserLoggedIn = () => {
    const user = getCurrentUser()
    return !!(user && user.id)
  }

  // <CHANGE> Nouvelle fonction pour charger le panier depuis la BD
const loadCartFromDB = async () => {
  const user = getCurrentUser()
  if (!user?.id) {
    // Si pas connecté, charger depuis localStorage
    const localCart = localStorage.getItem("cart")
    if (localCart) {
      items.value = JSON.parse(localCart)
    }
    return
  }

  isLoading.value = true
  try {
    const random = Math.random() // 🔥 variable anti-cache
    const response = await axios.get(
      `${API_URL}?action=get_cart&user_id=${user.id}&_=${random}`
    )

    if (response.data.success) {
      items.value = response.data.data || []
      saveLocal()
    }
  } catch (error) {
    console.error("Erreur chargement panier:", error)
    const localCart = localStorage.getItem("cart")
    if (localCart) {
      items.value = JSON.parse(localCart)
    }
  } finally {
    isLoading.value = false
  }
}


  const addItem = async (product) => {
    const productId = product.id || product.product_id
    const colorHex = product.colorHex || (product.colors && product.colors[0]?.hex_value)

    const idx = items.value.findIndex(
      (i) => i.product_id === productId && i.boutique_id === product.boutique_id && i.colorHex === colorHex,
    )

    if (idx !== -1) {
      items.value[idx].quantity = Number(items.value[idx].quantity || 0) + Number(product.quantity || 1)
    } else {
      items.value.push({
        item_id: product.id || `ci_${Date.now()}`,
        product_id: productId,
        boutique_id: product.boutique_id ?? null,
        boutique_name: product.boutique_name || "",
        boutique_logo: product.boutique_logo || "https://images.unsplash.com/photo-1560472354-b33ff0c44a43?w=100&h=100&fit=crop&auto=format",
        boutique_marche: product.boutique_marche,
        boutique_type: product.boutique_type,
        boutique_premium: product.boutique_premium,
        boutique_verified: product.boutique_verified,
        boutique_address: product.boutique_address,
        boutique_description: product.boutique_description,
        boutique_phone: product.boutique_phone,
        id: productId,
        name: product.name,
        unit_price: Number(product.unit_price || 0),
        stock: product.stock,
        primary_image: product.primary_image || "",
        vehicle_make: product.vehicle_make || "",
        vehicle_model: product.vehicle_model || "",
        vehicle_year: product.vehicle_year || "",
        vin_number: product.vin_numbers?.[0] || "",
        trim_number: product.trim_numbers?.[0] || "",
        stock_number: product.stock_number || "",
        color: product.colors?.[0]?.name,
        colorHex: colorHex,
        quantity: Number(product.quantity || 1),
        slug: product.slug,
        vehicle_mileage: product.vehicle_mileage || "",
        fuel_type: product.fuel_type || "",
      })
    }

    saveLocal()

    if (isUserLoggedIn()) {
      const user = getCurrentUser()
      try {
        await axios.post(`${API_URL}?action=add_to_cart`, {
          user_id: user.id,
          product_id: productId,
          quantity: product.quantity || 1,
          color_hex: colorHex,
          vin_number: product.vin_numbers?.[0],
          trim_number: product.trim_numbers?.[0],
        })
      } catch (error) {
        console.error("Erreur ajout serveur:", error)
      }
    }
  }

  // <CHANGE> Nouvelle fonction pour supprimer un item
  const removeItem = async (itemId) => {
    items.value = items.value.filter(item => item.item_id !== itemId && item.id !== itemId)
    saveLocal()

    if (isUserLoggedIn()) {
      const user = getCurrentUser()
      try {
        await axios.delete(
          `${API_URL}?action=remove_from_cart&user_id=${user.id}&item_id=${itemId}`
        )
      } catch (error) {
        console.error("Erreur suppression serveur:", error)
      }
    }
  }

  const increaseQty = async (itemId, newQuantity) => {
  const idx = items.value.findIndex(i => i.item_id === itemId || i.id === itemId)
  if (idx !== -1) {
    items.value[idx].quantity = newQuantity
    saveLocal()

    if (isUserLoggedIn()) {
      try {
        await axios.put(`${API_URL}?action=update_cart_quantity&item_id=${itemId}`, {
          quantity: newQuantity
        })
      } catch (error) {
        console.error("Erreur mise à jour quantité:", error)
      }
    }
  }
}

const decreaseQty = async (itemId, newQuantity) => {
  const idx = items.value.findIndex(i => i.item_id === itemId || i.id === itemId)
  if (idx !== -1) {
    items.value[idx].quantity = newQuantity
    saveLocal()

    if (isUserLoggedIn()) {
      try {
        await axios.put(`${API_URL}?action=update_cart_quantity&item_id=${itemId}`, {
          quantity: newQuantity
        })
      } catch (error) {
        console.error("Erreur mise à jour quantité:", error)
      }
    }
  }
}

  // <CHANGE> Nouvelle fonction pour mettre à jour la quantité
  const updateQuantity = async (itemId, quantity) => {
    const idx = items.value.findIndex(i => i.item_id === itemId || i.id === itemId)
    if (idx !== -1) {
      items.value[idx].quantity = quantity
      saveLocal()

      if (isUserLoggedIn()) {
        const user = getCurrentUser()
        try {
          await axios.post(`${API_URL}?action=update_cart_quantity`, {
            user_id: user.id,
            item_id: itemId,
            quantity: quantity
          })
        } catch (error) {
          console.error("Erreur mise à jour quantité:", error)
        }
      }
    }
  }

  // <CHANGE> Nouvelle fonction pour vider le panier
  const clear = async () => {
    items.value = []
    saveLocal()

    if (isUserLoggedIn()) {
      const user = getCurrentUser()
      try {
        await axios.delete(`${API_URL}?action=clear_cart&user_id=${user.id}`)
      } catch (error) {
        console.error("Erreur vidage panier serveur:", error)
      }
    }
  }

  // <CHANGE> Computed pour grouper par boutique
  const groupedByBoutique = computed(() => {
    const groups = {}
    items.value.forEach(item => {
      const boutiqueId = item.boutique_id || 'unknown'
      if (!groups[boutiqueId]) {
        groups[boutiqueId] = {
          boutique_id: boutiqueId,
          boutique_name: item.boutique_name,
          boutique_logo: item.boutique_logo,
          items: [],
          subtotal: 0
        }
      }
      groups[boutiqueId].items.push(item)
      groups[boutiqueId].subtotal += (item.unit_price || 0) * (item.quantity || 1)
    })
    return Object.values(groups)
  })

  const totalQuantity = computed(() => {
    return items.value.reduce((acc, item) => acc + (item.quantity || 0), 0)
  })

  const totalPrice = computed(() => {
    return items.value.reduce((acc, item) => acc + (item.unit_price || 0) * (item.quantity || 1), 0)
  })

  watch(items, () => {
    saveLocal()
  }, { deep: true })

  return {
    items,
    isLoading,
    addItem,
    removeItem,
    updateQuantity,
    increaseQty,      // <CHANGE> Export increaseQty
    decreaseQty,      // <CHANGE> Export decreaseQty
    clear,            // <CHANGE> Export clear
    loadCartFromDB,
    groupedByBoutique,
    totalQuantity,
    totalPrice,
  }
})

export { useCartStore }
export default useCartStore