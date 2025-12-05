import { ref, computed, watch } from "vue"
import { defineStore } from "pinia"
import axios from "axios"

const API_URL = "https://alidjam.com/api/products.php"

const useCartStore = defineStore("cart", () => {
  const items = ref([])
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
    console.log("[v0] isUserLoggedIn check - user:", user)
    return !!(user && user.id)
  }

  const addItem = async (product) => {
    const productId = product.id || product.product_id
    const colorHex = product.colorHex || (product.colors && product.colors[0]?.hex_value)

    // Vérifier si le produit existe déjà
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
        boutique_logo:
          product.boutique_logo ||
          "https://images.unsplash.com/photo-1560472354-b33ff0c44a43?w=100&h=100&fit=crop&auto=format",
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

    console.log("[v0] addItem - isUserLoggedIn:", isUserLoggedIn())
    console.log("[v0] addItem - getCurrentUser:", getCurrentUser())

    // Synchroniser avec le serveur si connecté
    if (isUserLoggedIn()) {
      const user = getCurrentUser()
      console.log("[v0] Envoi de la requête API - user_id:", user.id)
      try {
        const response = await axios.post(`${API_URL}?action=add_to_cart`, {
          user_id: user.id,
          product_id: productId,
          quantity: product.quantity || 1,
          color_hex: colorHex,
          vin_number: product.vin_numbers?.[0],
          trim_number: product.trim_numbers?.[0],
        })
        console.log("[v0] ✅ Item ajouté au serveur - Response:", response.data)
      } catch (error) {
        console.error("[v0] ❌ Erreur ajout serveur:", error)
      }
    } else {
      console.warn("[v0] ⚠️ Utilisateur non connecté - pas de sync serveur")
    }
  }

  const totalQuantity = computed(() => {
    return items.value.reduce((acc, item) => acc + item.quantity, 0)
  })

  const totalPrice = computed(() => {
    return items.value.reduce((acc, item) => acc + item.unit_price * item.quantity, 0)
  })

  watch(items, (newItems) => {
    saveLocal()
  })

  return {
    items,
    addItem,
    totalQuantity,
    totalPrice,
  }
})

export { useCartStore }
export default useCartStore
