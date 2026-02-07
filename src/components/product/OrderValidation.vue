<template>
  <div class="order-validation-page">
    <!-- Animation de confirmation de commande -->
    <transition name="fade">
      <div v-if="orderConfirmed" class="confirmation-overlay">
        <div class="confirmation-animation">
          <!-- Cercle animé avec icône de succès -->
          <div class="success-circle">
            <svg class="checkmark" width="80" height="80" viewBox="0 0 80 80">
              <circle class="checkmark-circle" cx="40" cy="40" r="35" fill="none" stroke="#52c41a" stroke-width="4"/>
              <path class="checkmark-check" fill="none" stroke="#52c41a" stroke-width="4" d="M25 40 L35 50 L55 30"/>
            </svg>
          </div>

          <!-- Message de confirmation -->
          <div class="confirmation-content">
            <h2 class="confirmation-title">Order confirmed successfully!</h2>
            <p class="confirmation-message">
              Your order has been successfully registered with reference <strong>{{ orderReference }}</strong>.
              When the order is processed by the supplier, you will receive an email notification.
            </p>
            <p class="confirmation-submessage">
              You can also check your order status in your "My Orders" section at any time.
            </p>

            <!-- Boutons d'action -->
            <div class="confirmation-actions">
              <button class="btn-orders" @click="goToOrders">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                  <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                  <circle cx="12" cy="7" r="4"></circle>
                </svg>
                View My Orders
              </button>
              <button class="btn-home" @click="goToHome">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                  <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path>
                </svg>
                Back to Home
              </button>
            </div>
          </div>
        </div>
      </div>
    </transition>

    <!-- Contenu principal (masqué après confirmation) -->
    <div v-show="!orderConfirmed">
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
          <!-- Colonne de gauche - Informations utilisateur -->
          <div class="left-column">
            <div class="section-card">
              <h2 class="section-title">Your Information</h2>
              <div class="user-info-form">
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
                  <label class="form-label">First Name</label>
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
                    type="email"
                    class="input-style"
                    :value="user.contact"
                    readonly
                  >
                </div>
              </div>
            </div>
          </div>

          <!-- Colonne de droite - Résumé de la commande -->
          <div class="right-column">
            <div class="section-card order-summary-card">
              <h2 class="section-title">Order Summary</h2>

              <!-- Résumé des articles -->
              <div class="summary-items">
                <div class="summary-row">
                  <div class="summary-label">
                    <ShoppingCart class="w-5 h-5 mr-2" />
                    <span>Number of items</span>
                  </div>
                  <span class="summary-value">{{ totalItems }}</span>
                </div>

                <div class="summary-divider"></div>

                <div class="summary-row">
                  <div class="summary-label">
                    <CalculatorIcon class="w-5 h-5 mr-2" />
                    <span>Subtotal</span>
                  </div>
                  <span class="summary-value">{{ formatPrice(subtotal) }}</span>
                </div>

                <div class="summary-row">
                  <div class="summary-label">
                    <TruckIcon class="w-5 h-5 mr-2" />
                    <span>Delivery costs</span>
                  </div>
                  <span class="summary-value">{{ formatPrice(shippingCost) }}</span>
                </div>

                <div class="summary-divider"></div>

                <div class="summary-row total-row">
                  <div class="summary-label">
                    <Banknote class="w-6 h-6 mr-2" />
                    <span>Total</span>
                  </div>
                  <span class="summary-total-value">{{ formatPrice(totalAmount) }}</span>
                </div>
              </div>

              <!-- Bouton de confirmation -->
              <button
                type="submit"
                class="btn-confirm-order"
                :disabled="isSubmitting || !canSubmitOrder"
                @click="submitOrder"
              >
                <Loader2 v-if="isSubmitting" class="w-5 h-5 animate-spin mr-2" />
                <svg v-else width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                  <polyline points="20 6 9 17 4 12"></polyline>
                </svg>
                {{ isSubmitting ? 'Processing...' : 'Confirm Order' }}
              </button>

              <!-- Note de sécurité -->
              <div class="security-note">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#52c41a" stroke-width="2">
                  <rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect>
                  <path d="M7 11V7a5 5 0 0 1 10 0v4"></path>
                </svg>
                <span>Your payment is secure and encrypted</span>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Modal de connexion -->
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
import { useRouter } from 'vue-router'
import { productsApi } from '../../services/api.js'
import { formatPrice } from '../../services/formatPrice'
import { Banknote, CalculatorIcon, TruckIcon, ShoppingCart, Loader2 } from 'lucide-vue-next'
import { useCartStore } from '../../stores/cart.js'

const router = useRouter()
const cart = useCartStore()

const user = ref({
  nom: '',
  prenom: '',
  contact: '',
  pays: 'Côte d\'Ivoire'
})

const isAuthenticated = ref(false)
const showLoginModal = ref(false)
const orderConfirmed = ref(false)
const isSubmitting = ref(false)
const orderReference = ref('')
const hasOwnProvider = ref(false)

const totalItems = computed(() => {
  return cart.totalQuantity
})

const subtotal = computed(() => {
  return cart.totalPrice
})

const shippingCost = computed(() => {
  if (hasOwnProvider.value) return 0
  return 0 // Vous pouvez ajouter la logique de calcul des frais de livraison ici
})

const totalAmount = computed(() => {
  return subtotal.value + shippingCost.value
})

const canSubmitOrder = computed(() => {
  return isAuthenticated.value && totalItems.value > 0
})

const submitOrder = async () => {
  if (!isAuthenticated.value) {
    showLoginModal.value = true
    return
  }

  if (!canSubmitOrder.value) {
    return
  }

  isSubmitting.value = true

  try {
    // Préparer les données de commande pour chaque article du panier
    const orderPromises = cart.items.map(async (item) => {
      const orderData = {
        numero_commande: `CMD-${Date.now()}-${Math.random().toString(36).substr(2, 5).toUpperCase()}`,
        produit_id: item.product_id || item.id,
        produit_nom: item.name,
        produit_prix: item.unit_price,
        quantite: item.quantity,
        client_nom: `${user.value.nom} ${user.value.prenom}`.trim(),
        client_telephone: user.value.contact,
        type_livraison: hasOwnProvider.value ? 1 : 0,
        sous_total: item.unit_price * item.quantity,
        frais_livraison: 0,
        total: item.unit_price * item.quantity,
        boutique_id: item.boutique_id,
        boutique_nom: item.boutique_name,
        statut: "en_attente",
      }

      return productsApi.createOrder(orderData)
    })

    // Attendre que toutes les commandes soient créées
    const results = await Promise.all(orderPromises)

    // Vérifier si toutes les commandes ont réussi
    const allSuccess = results.every(result => result.success)

    if (allSuccess) {
      // Générer une référence de commande unique
      orderReference.value = `CMD-${Date.now()}`

      // Vider le panier
      await cart.clear()

      // Afficher l'animation de confirmation
      orderConfirmed.value = true
    } else {
      throw new Error("Certaines commandes n'ont pas pu être créées")
    }
  } catch (error) {
    console.error("❌ Erreur lors de la confirmation:", error)
    alert("Une erreur s'est produite lors de la confirmation de votre commande. Veuillez réessayer.")
  } finally {
    isSubmitting.value = false
  }
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

const goToOrders = () => {
  router.push('/profile_client?tab=orders')
}

const goToHome = () => {
  router.push('/')
}

onMounted(() => {
  // Charger le panier depuis la base de données
  cart.loadCartFromDB()

  // Vérifier si l'utilisateur est connecté
  const userData = localStorage.getItem('user') || sessionStorage.getItem('user')

  if (userData) {
    try {
      const parsedUser = JSON.parse(userData)
      user.value = {
        nom: parsedUser.full_name?.split(' ')[0] || '',
        prenom: parsedUser.full_name?.split(' ').slice(1).join(' ') || '',
        contact: parsedUser.email || parsedUser.phone || '',
        pays: "Côte d'Ivoire"
      }
      isAuthenticated.value = true
    } catch (error) {
      console.error('Error parsing user data:', error)
      showLoginModal.value = true
    }
  } else {
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

/* Animation de confirmation */
.confirmation-overlay {
  position: fixed;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  z-index: 9999;
  display: flex;
  align-items: center;
  justify-content: center;
  padding: 20px;
}

.confirmation-animation {
  background: white;
  border-radius: 24px;
  padding: 60px 40px;
  max-width: 600px;
  width: 100%;
  text-align: center;
  box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
  animation: slideInUp 0.6s ease-out;
}

@keyframes slideInUp {
  from {
    opacity: 0;
    transform: translateY(50px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

.success-circle {
  width: 120px;
  height: 120px;
  margin: 0 auto 30px;
  position: relative;
  animation: scaleIn 0.5s ease-out 0.3s both;
}

@keyframes scaleIn {
  from {
    transform: scale(0);
    opacity: 0;
  }
  to {
    transform: scale(1);
    opacity: 1;
  }
}

.checkmark-circle {
  stroke-dasharray: 220;
  stroke-dashoffset: 220;
  animation: drawCircle 1s ease-out 0.5s forwards;
}

@keyframes drawCircle {
  to {
    stroke-dashoffset: 0;
  }
}

.checkmark-check {
  stroke-dasharray: 50;
  stroke-dashoffset: 50;
  animation: drawCheck 0.5s ease-out 1.2s forwards;
}

@keyframes drawCheck {
  to {
    stroke-dashoffset: 0;
  }
}

.confirmation-content {
  animation: fadeIn 0.6s ease-out 1.5s both;
}

@keyframes fadeIn {
  from {
    opacity: 0;
  }
  to {
    opacity: 1;
  }
}

.confirmation-title {
  font-size: 32px;
  font-weight: 700;
  color: #52c41a;
  margin: 0 0 20px 0;
}

.confirmation-message {
  font-size: 16px;
  color: #666;
  line-height: 1.6;
  margin: 0 0 15px 0;
}

.confirmation-message strong {
  color: #fe9700;
  font-weight: 600;
}

.confirmation-submessage {
  font-size: 14px;
  color: #999;
  margin: 0 0 40px 0;
  line-height: 1.5;
}

.confirmation-actions {
  display: flex;
  gap: 16px;
  justify-content: center;
}

.btn-orders,
.btn-home {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  gap: 10px;
  padding: 14px 28px;
  border-radius: 10px;
  font-size: 15px;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.3s ease;
  border: none;
}

.btn-orders {
  background: linear-gradient(135deg, #fe9700 0%, #ff7a00 100%);
  color: white;
  box-shadow: 0 4px 16px rgba(254, 151, 0, 0.3);
}

.btn-orders:hover {
  transform: translateY(-2px);
  box-shadow: 0 6px 20px rgba(254, 151, 0, 0.4);
}

.btn-home {
  background: #f5f5f5;
  color: #666;
}

.btn-home:hover {
  background: #e8e8e8;
  color: #333;
}

/* Transition fade */
.fade-enter-active,
.fade-leave-active {
  transition: opacity 0.3s ease;
}

.fade-enter-from,
.fade-leave-to {
  opacity: 0;
}

/* Header */
.page-header {
  background: #fff;
  border-bottom: 1px solid #e8e8e8;
  padding: 20px 0;
  margin-bottom: 24px;
}

.container {
  max-width: 1200px;
  margin: 0 auto;
  padding: 0 16px;
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

/* Grid layout */
.validation-grid {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 24px;
}

.section-card {
  background: #fff;
  border-radius: 12px;
  padding: 32px;
  box-shadow: 0 2px 12px rgba(0, 0, 0, 0.08);
}

.section-title {
  font-size: 20px;
  font-weight: 600;
  color: #333;
  margin: 0 0 24px 0;
  padding-bottom: 16px;
  border-bottom: 2px solid #f0f0f0;
}

/* Formulaire utilisateur */
.user-info-form {
  display: flex;
  flex-direction: column;
  gap: 20px;
}

.form-group {
  display: flex;
  flex-direction: column;
}

.form-label {
  font-size: 14px;
  font-weight: 500;
  color: #666;
  margin-bottom: 8px;
}

.input-style {
  padding: 12px 16px;
  border: 2px solid #e8e8e8;
  border-radius: 8px;
  font-size: 15px;
  color: #333;
  transition: all 0.3s ease;
}

.input-style:focus {
  outline: none;
  border-color: #fe9700;
}

.input-style[readonly] {
  background: #f5f5f5;
  color: #666;
  cursor: not-allowed;
}

/* Résumé de commande */
.order-summary-card {
  position: sticky;
  top: 100px;
}

.summary-items {
  margin-bottom: 24px;
}

.summary-row {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 16px 0;
  font-size: 15px;
}

.summary-label {
  display: flex;
  align-items: center;
  color: #666;
  font-weight: 500;
}

.summary-value {
  font-weight: 600;
  color: #333;
  font-size: 16px;
}

.summary-divider {
  height: 1px;
  background: #e8e8e8;
  margin: 12px 0;
}

.total-row {
  padding: 20px 0;
  border-top: 2px solid #f0f0f0;
  margin-top: 8px;
}

.total-row .summary-label {
  font-size: 18px;
  font-weight: 600;
  color: #333;
}

.summary-total-value {
  font-size: 28px;
  font-weight: 700;
  color: #fe9700;
}

/* Bouton de confirmation */
.btn-confirm-order {
  width: 100%;
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 10px;
  padding: 16px;
  background: linear-gradient(135deg, #fe9700 0%, #ff7a00 100%);
  color: white;
  border: none;
  border-radius: 10px;
  font-size: 16px;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.3s ease;
  box-shadow: 0 4px 16px rgba(254, 151, 0, 0.3);
}

.btn-confirm-order:hover:not(:disabled) {
  transform: translateY(-2px);
  box-shadow: 0 6px 20px rgba(254, 151, 0, 0.4);
}

.btn-confirm-order:disabled {
  opacity: 0.5;
  cursor: not-allowed;
  transform: none;
}

/* Note de sécurité */
.security-note {
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 8px;
  margin-top: 20px;
  padding: 12px;
  background: #f6ffed;
  border: 1px solid #b7eb8f;
  border-radius: 8px;
  font-size: 13px;
  color: #52c41a;
  font-weight: 500;
}

/* Modal */
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

/* Responsive */
@media (max-width: 1024px) {
  .validation-grid {
    grid-template-columns: 1fr;
  }

  .order-summary-card {
    position: static;
  }
}

@media (max-width: 768px) {
  .confirmation-animation {
    padding: 40px 24px;
  }

  .confirmation-title {
    font-size: 24px;
  }

  .confirmation-actions {
    flex-direction: column;
  }

  .btn-orders,
  .btn-home {
    width: 100%;
  }

  .page-title {
    font-size: 18px;
  }

  .section-card {
    padding: 20px;
  }
}
</style>
