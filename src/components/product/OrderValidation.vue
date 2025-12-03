<template>
  <div class="order-validation-page">
    <div class="page-header">
      <div class="container" style="margin-top: 40px">
        <button class="back-btn" @click="goBack">
          <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <polyline points="15,18 9,12 15,6"></polyline>
          </svg>
          Back
        </button>
        <h1 class="page-title" style="margin-left: 15px; margin-top: 8px;">Order confirmation</h1>
      </div>
    </div>

    <div class="container">
      <div class="validation-grid">
        <div class="left-column">
          <div class="section-card product-summary">
            <h2 class="section-title">Product Summary</h2>
            <div>

            </div>
            <div v-for="group in groupedOrders" :key="group.boutique_id" class="px-3">
              <div class="flex text-black font-bold py-3 ">
                <Box class="w-5 h-5 primary-color mr-2"/>
                Product Ordered
              </div>
              <div v-if="group.items.length>1" class="flex items-center justify-between mb-3">
                  <div class="flex items-center gap-3">
                    <div class="text-xl font-bold text-gray-500">Items: {{ group.items.length }}</div>
                  </div>
                  <div class="text-sm text-gray-600 font-bold">Subtotal: {{ formatPrice(group.subtotal) }}</div>
              </div>
              <div v-for="item in group.items" :key="item.item_id" class="sub-section-card items-center justify-between">
                <div class="product-info">
                  <img :src="item.primary_image" :alt="item.name" class="product-image">
                  <div class="product-details">
                    <h3 class="product-name">{{ item.name  }}</h3>
                    <div class="product-specs" >
                    <span class="bg-orange-200 px-2 py-1 rounded-lg font-bold">VIN: {{ item.vin_number || "N/A"}}</span>
                    <span  class="bg-blue-200 px-2 py-1 rounded-lg font-bold">Trim: {{ item.trim_number || "N/A"}}</span>
                    <span class="bg-green-200 px-2 py-1 rounded-lg font-bold" >Stock number: {{ item.stock_number|| "N/A" }}</span>
                      <span class=" px-2 py-1 rounded-lg font-bold" :style="{backgroundColor: item.colorHex || '#ffffff' }">Color: {{ item.color || "N/A" }}</span>
                    </div>
                  </div>
                </div>
                <div class="product-price">
                  <span class="text-gray-500 text-xl font-bold">Unit price</span>
                  <span class="price-value">{{ formatPrice(item.unit_price,{showFOB:true}) }}</span>
                </div>
                <!-- <div v-if="quantity >= 1">
                  <div class="product-quantity">
                    <span class="quantity-label">Quantity:</span>
                    <div class="quantity-controls">
                      <button @click="decreaseQuantity" :disabled="quantity <= 1">-</button>
                      <input type="number" v-model.number="quantity" min="1" @input="validateQuantity" class="quantity-input focus:border-ring-2 focus:ring-0">
                      <button @click="increaseQuantity">+</button>
                    </div>
                  </div>
                  <div  class="stock-info">
                    <span>{{ product.stock }} parts available</span>
                  </div>
                </div> -->
                <div class="flex text-black font-bold py-3 ">
                    <Banknote class="w-6 h-6 primary-color mt-0.5 mr-2"/>
                    Financial Summary
                </div>
                <div class="flex justify-between">
                  <div class="flex text-gray-400 font-bold py-3 ">
                    <CalculatorIcon class="w-5= h-5 mt-0.5 mr-2"/>
                    Subtotal
                  </div>
                  <div>
                    <span class="price-value">{{ formatPrice(group.subtotal) }}</span>
                  </div>
                </div>
                <div class="flex justify-between border-gray-200 border-b-2  text-gray-400 font-bold py-3 ">
                  <div class="flex text-gray-400  py-3 ">
                    <TruckIcon class="w-5= h-5 mt-0.5 mr-2"/>
                    Delivery Costs
                  </div>
                  <div>
                    <span class="price-value">{{ formatPrice(shippingCost) }}</span>
                  </div>
                </div>
                <div v-if="group.items.length<=1" class="flex justify-between primary-color font-bold py-4 text-xl">
                    <div class="flex  ">
                        <Banknote class="w-7 h-7  mr-3"/>
                        Total
                    </div>
                    <div>
                      <span >{{ formatPrice(totalAmount) }}</span>
                    </div>
                </div>
              </div>
              <div v-if="group.items.length>1" class="flex justify-between primary-color font-bold py-4 text-xl">
                  <div class="flex  ">
                      <Banknote class="w-7 h-7  mr-3"/>
                      Total
                  </div>
                  <div>
                    <span >{{ formatPrice(totalAmount) }}</span>
                  </div>
              </div>
            </div>
          </div>

          <div class="section-card shipping-section">
            <h2 class="section-title">Delivery options</h2>
            
            <div class="own-provider-option">
              <label class="checkbox-label">
                <input 
                  type="checkbox" 
                  v-model="hasOwnProvider"
                  class="checkbox-style"
                >
                <span class="checkbox-text">I have my own freight forwarder for the delivery in China</span>
              </label>
              <p class="checkbox-hint">If you select this option, the delivery charges will be waived.</p>
            </div>
            <div v-if="hasOwnProvider" class="own-provider-message">
              <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#52c41a" stroke-width="2">
                <polyline points="20 6 9 17 4 12"></polyline>
              </svg>
              <span>The delivery will be made to your own freight forwarder in China.</span>
            </div>
          </div>
        </div>

        <div class="right-column">
           <!-- Informations du fournisseur -->
           
          <div class="section-card">
            <!-- <h2 class="section-title">Delivery information</h2> -->
            <!-- <form @submit.prevent="submitOrder">
              <div class="form-grid">
                <div class="form-group">
                  <label class="form-label">Last Name</label>
                  <input 
                    type="text" 
                    class="input-style" 
                    :value="user.nom" 
                    readonly
                  >
                </div>

                <div class="form-group">
                  <label class="form-label">First name</label>
                  <input 
                    type="text" 
                    class="input-style" 
                    :value="user.prenom" 
                    readonly
                  >
                </div>

                <div class="form-group full-width">
                  <label class="form-label">Email</label>
                  <input 
                    type="text" 
                    class="input-style" 
                    :value="user.contact" 
                    readonly
                  >
                </div>

                <div class="form-group full-width">
                  <label class="form-label">Country</label>
                  <input 
                    type="text" 
                    class="input-style" 
                    :value="user.pays" 
                    readonly
                  >
                </div>

                <div class="form-group full-width">
                  <label class="form-label required">City</label>
                  <input 
                    type="text" 
                    class="input-style" 
                    v-model="orderForm.ville"
                    placeholder="Enter your city"
                    required
                  >
                </div>

                <div class="form-group full-width">
                  <label class="form-label required">Full address</label>
                  <textarea 
                    class="input-style" 
                    v-model="orderForm.adresse"
                    placeholder="Enter your full delivery address"
                    rows="3"
                    required
                  ></textarea>
                </div>

                <div class="form-group full-width">
                  <label class="form-label">Delivery instructions (optional)</label>
                  <textarea 
                    class="input-style" 
                    v-model="orderForm.instructions"
                    placeholder="Add special delivery instructions"
                    rows="2"
                  ></textarea>
                </div>
              </div>
            </form> -->
            <h2 class="section-title">Supplier Information</h2>
            <SupplierCard 
              v-if="supplier"
              :supplier="supplier"
              @visit-store="visitStore"
            />
            <button 
              type="submit"
              class="btn-degrade-orange mt-6 w-full" 
              :disabled="!canSubmitOrder"
              @click="submitOrder"
            >
              <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <circle cx="9" cy="21" r="1"></circle>
                <circle cx="20" cy="21" r="1"></circle>
                <path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"></path>
              </svg>
              Confirm the order
            </button>
          </div>
        </div>
      </div>
    </div>

    <div v-if="showSuccessModal" class="modal-overlay" @click="closeSuccessModal">
      <div class="modal-content success-modal" @click.stop>
        <button class="modal-close" @click="closeSuccessModal">
          <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <line x1="18" y1="6" x2="6" y2="18"></line>
            <line x1="6" y1="6" x2="18" y2="18"></line>
          </svg>
        </button>
        
        <div class="modal-icon success-icon">
          <svg width="64" height="64" viewBox="0 0 24 24" fill="none" stroke="#52c41a" stroke-width="2">
            <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path>
            <polyline points="22 4 12 14.01 9 11.01"></polyline>
          </svg>
        </div>
        
        <h2 class="modal-title">Order confirmed</h2>
        <p class="modal-message">
          Your order has been successfully registered ! 
          Please make the deposit payment according to the contract and add the proofs from your "My Orders" area.
        </p>
        
        <div class="modal-actions">
          <button class="flex-1 btn-gray text-xs" @click="goToOrders">
            View my orders
          </button>
          <button class="flex-1 btn-degrade-orange text-xs" @click="contactSeller">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"></path>
            </svg>
            Communicate with the Seller
          </button>
          
        </div>
      </div>
    </div>

    <div v-if="showLoginModal" class="modal-overlay" @click="closeLoginModal">
      <div class="modal-content" @click.stop>
        <button class="modal-close" @click="closeLoginModal">
          <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <line x1="18" y1="6" x2="6" y2="18"></line>
            <line x1="6" y1="6" x2="18" y2="18"></line>
          </svg>
        </button>
        
        <div class="modal-icon">
          <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="#fe9700" stroke-width="2">
            <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
            <circle cx="12" cy="7" r="4"></circle>
          </svg>
        </div>
        
        <h2 class="modal-title">Login required</h2>
        <p class="modal-message">
          You must be logged in to place an order.
          Please log in or create an account to continue.
        </p>
        
        <div class="modal-actions">
          <button class="flex-1 btn-degrade-orange" @click="redirectToLogin">
            Login
          </button>
          <button class="flex-1 btn-gray" @click="closeLoginModal">
            Cancel
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { useRouter, useRoute } from 'vue-router'
import { productsApi } from '../../services/api.js'
import { formatPrice } from '../../services/formatPrice'
import SupplierCard from './SupplierCard.vue'
import { Banknote, Box, CalculatorIcon, TruckIcon } from 'lucide-vue-next'
import { useOrdersStore } from '../../stores/orders.js'

const router = useRouter()
const route = useRoute()
const orders = useOrdersStore()

const product = ref([
  {
    id: null,
    name: '',
    unit_price: 0,
    primary_image: '',
    vin_number: '',
    trim_number: '',
    stock_number: '',
    color: '',
    colorHex: '',
    stock: 0,
    boutique_id: null,
    boutique_name: '',
    boutique_logo: '',
    boutique_marche: '',
    boutique_type: '',
    boutique_premium: '',
    boutique_verified: 0,
    boutique_address: '',
    boutique_description: '',
    boutique_phone: ''
  }
])

const user = ref({
  nom: '',
  prenom: '',
  contact: '',
  pays: 'Côte d\'Ivoire'
})

const isAuthenticated = ref(false)
const showLoginModal = ref(false)
const showSuccessModal = ref(false)
const quantity = ref(1)
const selectedShipping = ref('')
const selectedCommune = ref('')
const selectedVille = ref('')
const hasOwnProvider = ref(false)
const otherFees = ref(5000)


const supplier = computed(() => {
    if (!product.value) return null
  console.log("supplier: ",product.value)
    return {
      name: (product.value[0].boutique_name || 'Boutique Adjamé').toUpperCase(),
      logo: product.value[0].boutique_logo || 'https://images.unsplash.com/photo-1560472354-b33ff0c44a43?w=100&h=100&fit=crop&auto=format',
      market: (product.value[0].boutique_marche || 'Adjamé').toUpperCase(),
      business_type: (product.value[0].boutique_type || 'Commerce').toUpperCase(),
      experience: 5,
      premium: product.value[0].boutique_premium !== 'free',
      verify: product.value[0].boutique_verified !== 0,
      adresse: (product.value[0].boutique_address || 'Abidjan, Adjamé'),
      description: (product.value[0].boutique_description || ''),
      contact: (product.value[0].boutique_phone || '').toUpperCase()
    }
  })

  const groupedOrders = computed(() => {
  
  return orders.grouped;
})
  
  const visitStore = () => {
    if (product.value && product.value.boutique_id) {
      router.push(`/boutique_resultat/${product.value.boutique_id}`)
    } else {
      displayNotification('info', 'Visit store', `Redirect to the store ${supplier.value.name}.`)
    }
  }

const orderForm = ref({
  ville: '',
  adresse: '',
  quartier: '',
  instructions: ''
})

const tarifsAbidjan = ref([
  { id: 1, commune: 'Cocody', tarif_min: 1500, tarif_max: 2500, delai_livraison: '1-2 jours' },
  { id: 2, commune: 'Plateau', tarif_min: 1500, tarif_max: 2000, delai_livraison: '1-2 jours' },
  { id: 3, commune: 'Adjamé', tarif_min: 2000, tarif_max: 3000, delai_livraison: '1-2 jours' },
  { id: 4, commune: 'Yopougon', tarif_min: 2500, tarif_max: 3500, delai_livraison: '1-2 jours' }
])

const tarifsInterieur = ref([
  { id: 1, ville: 'Bouaké', tarif: 5000, delai_livraison: '3-5 jours' },
  { id: 2, ville: 'Yamoussoukro', tarif: 4500, delai_livraison: '3-5 jours' },
  { id: 3, ville: 'San-Pedro', tarif: 6000, delai_livraison: '5-7 jours' }
])

const subtotal = computed(() => {
  return orders.total;
})

const shippingCost = computed(() => {
  if (hasOwnProvider.value) return 0
  if (selectedShipping.value === 'retrait') return 0
  
  if (selectedShipping.value === 'abidjan' && selectedCommune.value) {
    const tarif = tarifsAbidjan.value.find(t => t.commune === selectedCommune.value)
    return tarif ? tarif.tarif_max : 0
  }
  
  if (selectedShipping.value === 'interieur' && selectedVille.value) {
    const tarif = tarifsInterieur.value.find(t => t.ville === selectedVille.value)
    return tarif ? tarif.tarif : 0
  }
  
  return 0
})

const totalAmount = computed(() => {
  const base = orders.total + shippingCost.value
  return base;
})

const depositAmount = computed(() => Math.round(totalAmount.value * 0.3))

const canSubmitOrder = computed(() => {
  return quantity.value > 0
})

const increaseQuantity = () => {
  quantity.value++
}

const decreaseQuantity = () => {
  if (quantity.value > 1) {
    quantity.value--
  }
}

const validateQuantity = () => {
  if (quantity.value < 1) {
    quantity.value = 1
  }
}

const selectShipping = (option) => {
  selectedShipping.value = option
  if (option !== 'abidjan') selectedCommune.value = ''
  if (option !== 'interieur') selectedVille.value = ''
}

const updateCommune = (commune) => {
  selectedCommune.value = commune
}

const updateVille = (ville) => {
  selectedVille.value = ville
}

const copyToClipboard = (text) => {
  navigator.clipboard.writeText(text).then(() => {
    alert('Copied to clipboard!')
  }).catch(err => {
    console.error('Erreur lors de la copie:', err)
  })
}

const submitOrder = async () => {
  console.log("📦 ===== DÉBUT handleConfirmOrder =====")

  if (!isAuthenticated.value) {
    showLoginModal.value = true
    return
  }

  if (!canSubmitOrder.value) {
    alert('Please fill in all required fields.')
    return
  }

  //formSubmitted.value = true
  //orderLoading.value = true

  try {
    // ✅ 1️⃣ Récupérer toutes les infos dans l'ordre du frontend
    const orderData = {
      product: product.value,
      quantity: quantity.value,
      shipping: {
        type: hasOwnProvider.value ? 'own_provider' : selectedShipping.value,
        cost: shippingCost.value,
        hasOwnProvider: hasOwnProvider.value
      },
      user: {
        ...user.value,
        ...orderForm.value
      },
      pricing: {
        subtotal: subtotal.value,
        shippingCost: shippingCost.value,
        otherFees: hasOwnProvider.value ? 0 : otherFees.value,
        total: totalAmount.value,
        deposit: depositAmount.value
      }
    }

    console.log('[v0] Données initiales (frontend):', orderData)

    // ✅ 2️⃣ Reformater selon la structure attendue par confirmOrder()
    const finalOrderData = {
      numero_commande: `CMD-${Date.now()}-${Math.random().toString(36).substr(2, 5).toUpperCase()}`,
      produit_id: orderData.product.id,
      produit_nom: orderData.product.name,
      produit_prix: orderData.product.price || orderData.product.unit_price,
      quantite: orderData.quantity || 1,
      client_nom: `${orderData.user.nom || ''} ${orderData.user.prenom || ''}`.trim(),
      client_telephone: orderData.user.contact,
      type_livraison: hasOwnProvider ? 1 : 0,
      commune: orderData.user.pays || '',
      // ville: orderData.shipping.ville || 'Abidjan',
      // adresse_complete: orderData.user.adresse || orderData.user.adresse_complete || '',
      // instructions_livraison: orderData.user.instructions || orderData.user.instructions_livraison || '',
      sous_total: orderData.pricing.subtotal,
      frais_livraison: orderData.pricing.shippingCost || 0,
      total: orderData.pricing.total,
      acompte: orderData.pricing.deposit || 0,
     // options_variantes: selectedVariants.value.length > 0 ? getSelectedVariants() : null,
      boutique_id: orderData.product.boutique_id,
      boutique_nom: orderData.product.boutique_name || supplier.value.name,
      statut: "en_attente",
    }

    console.log("📤 Données finales envoyées à l'API:", finalOrderData)

    // ✅ 3️⃣ Envoi à ton API
    const result = await productsApi.createOrder(finalOrderData)

    if (result.success) {
      console.log("✅ Commande créée avec succès:", result)
     // orderLoading.value = false
      closeOrderModal()
      showOrderSuccessNotification(finalOrderData)

      // 🧹 Nettoyage du stockage temporaire
      sessionStorage.removeItem('lastProduct')
    } else {
      throw new Error(result.error || result.message || "Erreur lors de la création de la commande")
    }
  } catch (error) {
   // orderLoading.value = false
    console.error("❌ Erreur lors de la confirmation:", error)
    //displayNotification("error", "Erreur", error.message)
  }

  console.log("📦 ===== FIN handleConfirmOrder =====")
  showSuccessModal.value = true
}



const goBack = () => {
  router.back()
}

const closeLoginModal = () => {
  showLoginModal.value = false
}

const redirectToLogin = () => {
  router.push('/login')
}

const closeSuccessModal = () => {
  showSuccessModal.value = false
}

const contactSeller = () => {
  console.log('[v0] Contact seller')
  alert('Messaging functionality coming soon')
  showSuccessModal.value = false
}

const goToOrders = () => {
  showSuccessModal.value = false
  router.push('/profile_client')
} 

onMounted(() => {
  // 🔐 Vérifier si l'utilisateur est connecté
  const authToken = localStorage.getItem('authToken') || sessionStorage.getItem('authToken')
  isAuthenticated.value = !!authToken

  // 👤 Charger les infos utilisateur
  const userData = localStorage.getItem('user') || sessionStorage.getItem('user')
  if (userData) {
    try {
      const parsedUser = JSON.parse(userData)
      user.value = {
        nom: parsedUser.full_name?.split(' ')[0] || '',
        prenom: parsedUser.full_name?.split(' ')[1] || '',
        contact: parsedUser.email || '',
        pays: "Côte d'Ivoire"
      }
    } catch (error) {
      console.error('[v0] Error parsing user data:', error)
    }
  }

  // 🧾 Récupération du produit depuis route.state (mémoire)
  console.log('[v0] Route state on mount:', orders.itemsOrdered)
  
    product.value = orders.itemsOrdered 
    quantity.value = orders.itemsOrdered[0].quantity || 1
    console.log('[v0] Product loaded from Order store:', product.value)
  
  // 🔁 Si actualisation, récupérer depuis sessionStorage
  // else {
  //   const savedData = sessionStorage.getItem('lastProduct')
  //   if (savedData) {
  //     try {
  //       const parsed = JSON.parse(savedData)
  //       product.value = { ...product.value, ...parsed.product }
  //       quantity.value = parsed.quantity || 1
  //       console.log('[v0] Product loaded from sessionStorage:', product.value)
  //     } catch (error) {
  //       console.error('[v0] Error parsing product from sessionStorage:', error)
  //     }
  //   } 
  //   // 🧩 Fallback : route.query si partagé via lien
  //   else if (route.query.productData) {
  //     try {
  //       const productData = JSON.parse(decodeURIComponent(route.query.productData))
  //       product.value = { ...product.value, ...productData }
  //       quantity.value = parseInt(route.query.quantity) || 1
  //       console.log('[v0] Product loaded from query params:', product.value)
  //     } catch (error) {
  //       console.error('[v0] Error parsing product data from query:', error)
  //     }
  //   }
  // }

  // 🔒 Si non connecté, afficher le modal de connexion
  if (!isAuthenticated.value) {
    showLoginModal.value = true
  }
})

</script>

<style scoped>
.order-validation-page {
  min-height: 100vh;
  background: #f5f5f5;
  padding-bottom: 40px;
}

.page-header {
  background: #fff;
  border-bottom: 1px solid #e8e8e8;
  padding: 20px 0;
  margin-bottom: 24px;
}

.container {
  max-width: 1400px;
  margin: 0 auto;
  padding: 0 16px;
  display: flex
}

.back-btn {
  display: inline-flex;
  align-items: center;
  gap: 8px;
  padding: 8px 16px;
  background: transparent;
  border: 1px solid #d9d9d9;
  border-radius: 6px;
  color: #666;
  font-size: 14px;
  cursor: pointer;
  transition: all 0.3s ease;
  margin-bottom: 12px;
}

.back-btn:hover {
  border-color: #fe9700;
  color: #fe9700;
}

.page-title {
  font-size: 22px;
  font-weight: 600;
  color: #333;
  margin: 0;
}

.validation-grid {
  display: grid;
  grid-template-columns: 2fr 1fr;
  gap: 24px;
}

.section-card {
  background: #fff;
  border-radius: 8px;
  padding: 24px;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
  margin-bottom: 24px;
}

.sub-section-card{
  background: #fff;
  border-radius: 8px;
  padding:10px 24px;
  margin-bottom: 16px;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
}

.section-title {
  font-size: 18px;
  font-weight: 600;
  color: #333;
  /* margin: 0 0 20px 0; */
  padding-bottom: 12px;
}

.cart-item { background: #fff; padding: 12px; border-radius: 8px; box-shadow: 0 2px 6px rgba(0,0,0,0.03); align-items: center; }
.product-info {
  display: flex;
  gap: 16px;
}

.product-image {
  width: 100px;
  height: 100px;
  object-fit: cover;
  border-radius: 8px;
  border: 1px solid #e8e8e8;
}

.product-details {
  flex: 1;
}

.product-name {
  font-size: 18px;
  font-weight: 600;
  color: #333;
  margin: 0 0 8px 0;
  font-weight: bold;
  font-size: 25px;
}

.product-specs {
  display: flex;
  gap: 10px;
  margin: 25px auto;
  font-size: 12px;
  color: #666;
}


.product-price {
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 8px;
  margin-bottom: 12px;
  margin-top: 20px;
}

.price-value {
  font-size: 20px;
  font-weight: 600;
  color: #333;
}

.product-quantity {
  display: flex;
  align-items: center;
  gap: 12px;
}

.quantity-label {
  font-size: 14px;
  color: #666;
}

.quantity-controls {
  display: flex;
  align-items: center;
  border: 1px solid #d9d9d9;
  border-radius: 6px;
  overflow: hidden;
}

.quantity-controls button {
  width: 32px;
  height: 32px;
  border: none;
  background: white;
  color: #333;
  font-size: 18px;
  cursor: pointer;
  transition: all 0.3s ease;
}

.quantity-controls button:hover:not(:disabled) {
  background: #fe9700;
  color: #fff;
}

.quantity-controls button:disabled {
  opacity: 0.5;
  cursor: not-allowed;
}
.quantity-input {
  -webkit-appearance: none;
  -moz-appearance: textfield;
  appearance: none;
  width: auto;
  height: 50px;
  border: 1px solid #d9d9d9;
  color: #333;
  border-left: none;
  border-right: none;
  text-align: center;
  font-size: 18px;
}

.quantity-input::-webkit-outer-spin-button,
.quantity-input::-webkit-inner-spin-button {
  -webkit-appearance: none;
  margin: 0;
}

.quantity-input[type="number"] {
  -moz-appearance: textfield;
  appearance: textfield;
}

.quantity-controls input {
  width: 50px;
  height: 32px;
  border: none;
  text-align: center;
  font-size: 14px;
  font-weight: 500;
}

.form-grid {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 16px;
}

.form-group {
  display: flex;
  flex-direction: column;
}

.form-group.full-width {
  grid-column: 1 / -1;
}

.form-label {
  font-size: 14px;
  font-weight: 500;
  color: #333;
  margin-bottom: 8px;
}

.form-label.required::after {
  content: ' *';
  color: #ff4d4f;
}

.input-style[readonly] {
  background: #f5f5f5;
  color: #666;
  cursor: not-allowed;
}

.summary-row {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 12px 0;
  font-size: 15px;
}

.summary-label {
  color: #666;
}

.summary-value {
  font-weight: 600;
  color: #333;
}

.summary-value.free-shipping {
  color: #52c41a;
}

.summary-divider {
  height: 1px;
  background: #e8e8e8;
  margin: 12px 0;
}

.total-row {
  padding: 16px 0;
  border-top: 2px solid #f0f0f0;
  margin-top: 8px;
}

.total-row .summary-label {
  font-size: 18px;
  font-weight: 600;
  color: #333;
}

/* .btn-degrade-orange:hover:not(:disabled) {
  transform: translateY(-2px);
  box-shadow: 0 4px 12px rgba(254, 151, 0, 0.3);
}

.btn-degrade-orange:disabled {
  opacity: 0.5;
  cursor: not-allowed;
  transform: none;
} */

.security-badges {
  display: flex;
  flex-direction: column;
  gap: 8px;
  margin-top: 16px;
  padding-top: 16px;
  border-top: 1px solid #e8e8e8;
}

.badge {
  display: flex;
  align-items: center;
  gap: 8px;
  font-size: 13px;
  color: #52c41a;
}

.modal-overlay {
  position: fixed;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: rgba(0, 0, 0, 0.6);
  display: flex;
  align-items: center;
  justify-content: center;
  z-index: 1000;
  padding: 20px;
}

.modal-content {
  background: #fff;
  border-radius: 12px;
  padding: 32px;
  max-width: 450px;
  width: 100%;
  position: relative;
  box-shadow: 0 8px 24px rgba(0, 0, 0, 0.2);
}

.modal-close {
  position: absolute;
  top: 16px;
  right: 16px;
  width: 32px;
  height: 32px;
  border: none;
  background: transparent;
  color: #999;
  cursor: pointer;
  transition: all 0.3s ease;
  display: flex;
  align-items: center;
  justify-content: center;
  border-radius: 50%;
}

.modal-close:hover {
  background: #f5f5f5;
  color: #333;
}

.modal-icon {
  display: flex;
  justify-content: center;
  margin-bottom: 20px;
}

.modal-title {
  font-size: 24px;
  font-weight: 600;
  color: #333;
  text-align: center;
  margin: 0 0 12px 0;
}

.modal-message {
  font-size: 15px;
  color: #666;
  text-align: center;
  line-height: 1.6;
  margin: 0 0 24px 0;
}

.modal-actions {
  display: flex;
  gap: 12px;
}

.modal-btn {
  flex: 1;
  padding: 12px 24px;
  border: none;
  border-radius: 8px;
  font-size: 15px;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.3s ease;
}

.modal-btn.primary {
  background: #fe9700;
  color: #fff;
}

.modal-btn.primary:hover {
  background: #e68900;
}

.modal-btn.secondary {
  background: #f5f5f5;
  color: #666;
}

.modal-btn.secondary:hover {
  background: #e8e8e8;
}

.own-provider-option {
  margin-bottom: 20px;
  padding: 16px;
  background: #f9f9f9;
  border: 1px solid #e8e8e8;
  border-radius: 8px;
}

.checkbox-label {
  display: flex;
  align-items: center;
  gap: 10px;
  cursor: pointer;
  font-size: 15px;
  font-weight: 500;
  color: #333;
}

.checkbox-text {
  user-select: none;
}

.checkbox-hint {
  margin: 8px 0 0 28px;
  font-size: 13px;
  color: #666;
  line-height: 1.5;
}

.own-provider-message {
  display: flex;
  align-items: center;
  gap: 10px;
  padding: 16px;
  background: #f6ffed;
  border: 1px solid #b7eb8f;
  border-radius: 8px;
  color: #52c41a;
  font-size: 14px;
  font-weight: 500;
}

.deposit-info {
  margin-top: 16px;
  padding: 16px;
  background: #fff7e6;
  border: 1px solid #ffd591;
  border-radius: 8px;
}

.deposit-row {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 8px;
}

.deposit-label {
  font-size: 15px;
  font-weight: 600;
  color: #d46b08;
}

.deposit-value {
  font-size: 20px;
  font-weight: 700;
  color: #d46b08;
}

.deposit-note {
  margin: 0;
  font-size: 13px;
  color: #ad6800;
  line-height: 1.5;
}

.bank-info {
  margin-top: 20px;
  padding: 16px;
  background: #f0f5ff;
  border: 1px solid #adc6ff;
  border-radius: 8px;
}

.bank-title {
  font-size: 15px;
  font-weight: 600;
  color: #1890ff;
  margin: 0 0 12px 0;
}

.bank-details {
  display: flex;
  flex-direction: column;
  gap: 10px;
}

.bank-row {
  display: flex;
  justify-content: space-between;
  align-items: center;
  font-size: 14px;
}

.bank-label {
  color: #666;
  font-weight: 500;
}

.bank-value {
  color: #333;
  font-weight: 600;
}

.copy-value {
  display: flex;
  align-items: center;
  gap: 6px;
  cursor: pointer;
  color: #1890ff;
  transition: all 0.3s ease;
}

.copy-value:hover {
  color: #096dd9;
}

.copy-value svg {
  flex-shrink: 0;
}

.bank-note {
  display: flex;
  align-items: flex-start;
  gap: 8px;
  margin: 12px 0 0 0;
  padding-top: 12px;
  border-top: 1px solid #d6e4ff;
  font-size: 13px;
  color: #0050b3;
  line-height: 1.5;
}

.bank-note svg {
  flex-shrink: 0;
  margin-top: 2px;
}

.success-modal {
  text-align: center;
}

.success-icon {
  background: #f6ffed;
  width: 96px;
  height: 96px;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  margin: 0 auto 24px;
}

.success-title {
  color: #52c41a;
}

.modal-message strong {
  color: #fe9700;
  font-weight: 700;
}

.modal-btn svg {
  width: 20px;
  height: 20px;
}

.modal-btn.primary {
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 8px;
}

.wholesale-info {
  display: flex;
  align-items: center;
  gap: 6px;
  padding: 8px 12px;
  background: #f6ffed;
  border: 1px solid #b7eb8f;
  border-radius: 6px;
  font-size: 13px;
  color: #52c41a;
  margin-bottom: 12px;
}

.stock-info {
  font-size: 13px;
  color: #666;
  margin-top: 8px;
}

@media (max-width: 1024px) {
  .validation-grid {
    grid-template-columns: 1fr;
  }
}

@media (max-width: 768px) {
  .page-title {
    font-size: 18px;
  }

  .product-info {
    flex-direction: column;
  }

  .product-image {
    width: 100%;
    height: 200px;
  }

  .form-grid {
    grid-template-columns: 1fr;
  }
}
</style>
