<template>
  <div class="my-orders-page">
    <div class="page-header">
      <div class="container">
        <h1 class="page-title">Mes commandes</h1>
        <p class="page-subtitle">Suivez l'état de vos commandes et ajoutez vos preuves de paiement</p>
      </div>
    </div>

    <div class="container">
      <!-- Filtres -->
      <div class="filters-section">
        <div class="filter-tabs">
          <button 
            v-for="status in orderStatuses" 
            :key="status.value"
            :class="['filter-tab', { active: selectedStatus === status.value }]"
            @click="selectedStatus = status.value"
          >
            <span class="tab-label">{{ status.label }}</span>
            <span v-if="getOrderCountByStatus(status.value) > 0" class="tab-badge">
              {{ getOrderCountByStatus(status.value) }}
            </span>
          </button>
        </div>
      </div>

      <!-- Loading State -->
      <div v-if="loading" class="loading-state">
        <div class="spinner"></div>
        <p>Chargement de vos commandes...</p>
      </div>

      <!-- Empty State -->
      <div v-else-if="filteredOrders.length === 0" class="empty-state">
        <svg width="120" height="120" viewBox="0 0 24 24" fill="none" stroke="#d9d9d9" stroke-width="1">
          <circle cx="9" cy="21" r="1"></circle>
          <circle cx="20" cy="21" r="1"></circle>
          <path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"></path>
        </svg>
        <h3>Aucune commande trouvée</h3>
        <p>{{ selectedStatus === 'all' ? 'Vous n\'avez pas encore passé de commande' : 'Aucune commande avec ce statut' }}</p>
        <button class="primary-btn" @click="goToShop">
          <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <circle cx="9" cy="21" r="1"></circle>
            <circle cx="20" cy="21" r="1"></circle>
            <path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"></path>
          </svg>
          Découvrir nos produits
        </button>
      </div>

      <!-- Orders List -->
      <div v-else class="orders-list">
        <div v-for="order in filteredOrders" :key="order.id" class="order-card">
          <div class="order-header">
            <div class="order-info">
              <h3 class="order-number">Commande #{{ order.numero_commande }}</h3>
              <span class="order-date">{{ formatDate(order.created_at) }}</span>
            </div>
            <span :class="['order-status', getStatusClass(order.statut)]">
              {{ getStatusLabel(order.statut) }}
            </span>
          </div>

          <div class="order-body">
            <div class="product-info">
              <img 
                :src="order.produit_image || '/placeholder.svg?height=80&width=80'" 
                :alt="order.produit_nom"
                class="product-image"
              >
              <div class="product-details">
                <h4 class="product-name">{{ order.produit_nom }}</h4>
                <div class="product-meta">
                  <span class="meta-item">Quantité: {{ order.quantite }}</span>
                  <span class="meta-item">Prix unitaire: {{ formatPrice(order.produit_prix) }}</span>
                </div>
                <div class="product-seller">
                  <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#666" stroke-width="2">
                    <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                    <circle cx="12" cy="7" r="4"></circle>
                  </svg>
                  <span>{{ order.boutique_nom }}</span>
                </div>
              </div>
            </div>

            <div class="order-summary">
              <div class="summary-row">
                <span class="summary-label">Sous-total:</span>
                <span class="summary-value">{{ formatPrice(order.sous_total) }}</span>
              </div>
              <div v-if="order.frais_livraison > 0" class="summary-row">
                <span class="summary-label">Livraison:</span>
                <span class="summary-value">{{ formatPrice(order.frais_livraison) }}</span>
              </div>
              <div class="summary-row total">
                <span class="summary-label">Total:</span>
                <span class="summary-value">{{ formatPrice(order.total) }}</span>
              </div>
              <div class="summary-row deposit">
                <span class="summary-label">Acompte (30%):</span>
                <span class="summary-value">{{ formatPrice(order.total * 0.3) }}</span>
              </div>
            </div>
          </div>

          <div class="order-delivery" v-if="order.adresse_complete">
            <div class="delivery-info">
              <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#666" stroke-width="2">
                <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path>
                <circle cx="12" cy="10" r="3"></circle>
              </svg>
              <div>
                <strong>Adresse de livraison:</strong>
                <p>{{ order.adresse_complete }}</p>
                <p v-if="order.ville">{{ order.ville }}, {{ order.commune }}</p>
              </div>
            </div>
          </div>

          <div class="order-actions">
            <button 
              v-if="order.statut === 'en_attente' && !order.preuve_paiement"
              class="action-btn primary"
              @click="openPaymentProofModal(order)"
            >
              <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
                <polyline points="17 8 12 3 7 8"></polyline>
                <line x1="12" y1="3" x2="12" y2="15"></line>
              </svg>
              Ajouter preuve de paiement
            </button>

            <button 
              v-if="order.preuve_paiement"
              class="action-btn success"
              @click="viewPaymentProof(order)"
            >
              <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <polyline points="20 6 9 17 4 12"></polyline>
              </svg>
              Preuve envoyée
            </button>

            <button 
              class="action-btn secondary"
              @click="contactSeller(order)"
            >
              <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"></path>
              </svg>
              Contacter le vendeur
            </button>

            <button 
              v-if="order.statut === 'en_attente'"
              class="action-btn danger"
              @click="openCancelModal(order)"
            >
              <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <circle cx="12" cy="12" r="10"></circle>
                <line x1="15" y1="9" x2="9" y2="15"></line>
                <line x1="9" y1="9" x2="15" y2="15"></line>
              </svg>
              Annuler
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- Modal Upload Payment Proof -->
    <div v-if="showPaymentModal" class="modal-overlay" @click="closePaymentModal">
      <div class="modal-content" @click.stop>
        <button class="modal-close" @click="closePaymentModal">
          <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <line x1="18" y1="6" x2="6" y2="18"></line>
            <line x1="6" y1="6" x2="18" y2="18"></line>
          </svg>
        </button>

        <div class="modal-header">
          <h2 class="modal-title">Ajouter une preuve de paiement</h2>
          <p class="modal-subtitle">Commande #{{ selectedOrder?.numero_commande }}</p>
        </div>

        <div class="payment-info-box">
          <div class="info-row">
            <span class="info-label">Montant à payer (acompte):</span>
            <span class="info-value">{{ formatPrice(selectedOrder?.total*0.3) }}</span>
          </div>
          <p class="info-note">Veuillez télécharger une capture d'écran ou photo de votre preuve de paiement</p>
        </div>

        <form @submit.prevent="uploadPaymentProof" class="upload-form">
          <div class="form-group">
            <label class="form-label">Sélectionner un fichier</label>
            <div class="file-input-wrapper">
              <input 
                type="file" 
                ref="fileInput"
                @change="handleFileSelect"
                accept="image/*"
                class="file-input"
                required
              >
              <div class="file-input-display">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#666" stroke-width="2">
                  <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
                  <polyline points="17 8 12 3 7 8"></polyline>
                  <line x1="12" y1="3" x2="12" y2="15"></line>
                </svg>
                <span v-if="!selectedFile">Cliquez pour sélectionner un fichier</span>
                <span v-else class="file-name">{{ selectedFile.name }}</span>
              </div>
            </div>
            <p class="file-hint">Formats acceptés: JPG, PNG (Max 10MB)</p>
            
            <div v-if="uploading" class="upload-progress">
              <div class="progress-bar">
                <div class="progress-fill" :style="{ width: uploadProgress + '%' }"></div>
              </div>
              <p class="progress-text">Upload en cours: {{ uploadProgress }}%</p>
            </div>
          </div>

          <div class="form-group">
            <label class="form-label">Commentaire (optionnel)</label>
            <textarea 
              v-model="paymentComment"
              class="form-textarea"
              placeholder="Ajoutez un commentaire sur votre paiement..."
              rows="3"
            ></textarea>
          </div>

          <div class="modal-actions">
            <button type="submit" class="modal-btn primary" :disabled="uploading || !selectedFile">
              <span v-if="!uploading">Envoyer la preuve</span>
              <span v-else>Envoi en cours...</span>
            </button>
            <button type="button" class="modal-btn secondary" @click="closePaymentModal" :disabled="uploading">
              Annuler
            </button>
          </div>
        </form>
      </div>
    </div>

    <!-- Modal Cancel Order -->
    <div v-if="showCancelModal" class="modal-overlay" @click="closeCancelModal">
      <div class="modal-content" @click.stop>
        <button class="modal-close" @click="closeCancelModal">
          <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <line x1="18" y1="6" x2="6" y2="18"></line>
            <line x1="6" y1="6" x2="18" y2="18"></line>
          </svg>
        </button>

        <div class="modal-icon warning">
          <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="#ff4d4f" stroke-width="2">
            <circle cx="12" cy="12" r="10"></circle>
            <line x1="15" y1="9" x2="9" y2="15"></line>
            <line x1="9" y1="9" x2="15" y2="15"></line>
          </svg>
        </div>

        <h2 class="modal-title">Annuler la commande</h2>
        <p class="modal-message">
          Êtes-vous sûr de vouloir annuler la commande #{{ selectedOrder?.numero_commande }} ?
          Cette action est irréversible.
        </p>

        <form @submit.prevent="cancelOrder" class="cancel-form">
          <div class="form-group">
            <label class="form-label">Raison de l'annulation</label>
            <textarea 
              v-model="cancelReason"
              class="form-textarea"
              placeholder="Expliquez pourquoi vous souhaitez annuler cette commande..."
              rows="4"
              required
            ></textarea>
          </div>

          <div class="modal-actions">
            <button type="submit" class="modal-btn danger" :disabled="cancelling">
              <span v-if="!cancelling">Confirmer l'annulation</span>
              <span v-else>Annulation en cours...</span>
            </button>
            <button type="button" class="modal-btn secondary" @click="closeCancelModal">
              Retour
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import { ordersApi } from '../../services/api.js'
import axios from 'axios'

const router = useRouter()

// Cloudinary configuration
const cloudinaryConfig = {
  cloudName: 'daaavha4z',
  uploadPreset: 'aliadjame',
  apiKey: 'wy0Eh-uA0Y0Ci3nyODix0b3WejA',
  imageUploadUrl: 'https://api.cloudinary.com/v1_1/daaavha4z/image/upload'
}

const orders = ref([])
const loading = ref(true)
const selectedStatus = ref('all')
const showPaymentModal = ref(false)
const showCancelModal = ref(false)
const selectedOrder = ref(null)
const selectedFile = ref(null)
const paymentComment = ref('')
const cancelReason = ref('')
const uploading = ref(false)
const uploadProgress = ref(0)
const cancelling = ref(false)
const fileInput = ref(null)

const orderStatuses = [
  { value: 'all', label: 'Toutes' },
  { value: 'en_attente', label: 'En attente' },
  { value: 'confirmee', label: 'Confirmée' },
  { value: 'en_cours', label: 'En cours' },
  { value: 'livree', label: 'Livrée' },
  { value: 'annule', label: 'Annulée' }
]

const filteredOrders = computed(() => {
  if (selectedStatus.value === 'all') {
    return orders.value
  }
  return orders.value.filter(order => order.statut === selectedStatus.value)
})

const getOrderCountByStatus = (status) => {
  if (status === 'all') return orders.value.length
  return orders.value.filter(order => order.statut === status).length
}

const getStatusClass = (status) => {
  const statusMap = {
    'en_attente': 'pending',
    'confirmee': 'confirmed',
    'en_cours': 'processing',
    'livree': 'delivered',
    'annule': 'cancelled'
  }
  return statusMap[status] || 'pending'
}

const getStatusLabel = (status) => {
  const labelMap = {
    'en_attente': 'En attente',
    'confirmee': 'Confirmée',
    'en_cours': 'En cours de livraison',
    'livree': 'Livrée',
    'annule': 'Annulée'
  }
  return labelMap[status] || status
}

const formatPrice = (price) => {
  return Number(price).toLocaleString('fr-FR', { minimumFractionDigits: 0 }) + ' FCFA'
}

const formatDate = (dateString) => {
  const date = new Date(dateString)
  return date.toLocaleDateString('fr-FR', {
    year: 'numeric',
    month: 'long',
    day: 'numeric',
    hour: '2-digit',
    minute: '2-digit'
  })
}

const fetchOrders = async () => {
  loading.value = true

  try {
    const userData = localStorage.getItem('user') || sessionStorage.getItem('user')
    if (!userData) {
      console.warn('[v0] Aucun utilisateur trouvé dans le storage.')
      orders.value = []
      loading.value = false
      return
    }

    const parsedUser = JSON.parse(userData)
    const userId = parsedUser.id
    console.log('[v0] Current user ID:', userId)

    console.log('[v0] Fetching user orders...')
    const response = await ordersApi.getMyOrders(userId)

    if (response.success) {
      orders.value = response.data || []
      console.log('[v0] Orders loaded:', orders.value)
    } else {
      console.error('[v0] Error loading orders:', response.message)
      orders.value = []
    }
  } catch (error) {
    console.error('[v0] Error fetching orders:', error)
    orders.value = []
  } finally {
    loading.value = false
  }
}

const openPaymentProofModal = (order) => {
  selectedOrder.value = order
  showPaymentModal.value = true
  selectedFile.value = null
  paymentComment.value = ''
  uploadProgress.value = 0
}

const closePaymentModal = () => {
  showPaymentModal.value = false
  selectedOrder.value = null
  selectedFile.value = null
  paymentComment.value = ''
  uploadProgress.value = 0
}

const handleFileSelect = (event) => {
  const file = event.target.files[0]
  if (file) {
    if (file.size > 10 * 1024 * 1024) {
      alert('Le fichier est trop volumineux. Taille maximale: 10MB')
      event.target.value = ''
      return
    }
    
    if (!file.type.startsWith('image/')) {
      alert('Veuillez sélectionner une image (JPG, PNG, etc.)')
      event.target.value = ''
      return
    }
    
    selectedFile.value = file
    console.log('[v0] File selected:', file.name, 'Size:', (file.size / 1024 / 1024).toFixed(2), 'MB')
  }
}

const uploadToCloudinary = async (file) => {
  try {
    console.log('[v0] Starting Cloudinary upload...')
    uploadProgress.value = 0
    
    const fileName = `payment_proof_${selectedOrder.value.id}_${Date.now()}_${file.name.replace(/\s+/g, '_')}`
    
    const formData = new FormData()
    formData.append('file', file)
    formData.append('upload_preset', cloudinaryConfig.uploadPreset)
    formData.append('api_key', cloudinaryConfig.apiKey)
    formData.append('public_id', fileName)
    
    const response = await axios.post(cloudinaryConfig.imageUploadUrl, formData, {
      headers: {
        'Content-Type': 'multipart/form-data'
      },
      onUploadProgress: (progressEvent) => {
        const percentCompleted = Math.round((progressEvent.loaded * 100) / progressEvent.total)
        uploadProgress.value = percentCompleted
        console.log('[v0] Upload progress:', percentCompleted + '%')
      }
    })
    
    if (response.data && response.data.secure_url) {
      console.log('[v0] Cloudinary upload successful:', response.data.secure_url)
      return response.data.secure_url
    } else {
      throw new Error('Réponse Cloudinary invalide')
    }
  } catch (error) {
    console.error('[v0] Cloudinary upload error:', error)
    throw error
  }
}

const uploadPaymentProof = async () => {
  if (!selectedFile.value || !selectedOrder.value) return

  uploading.value = true
  try {
    console.log('[v0] Uploading payment proof for order:', selectedOrder.value.id)
    
    // Upload to Cloudinary first
    const cloudinaryUrl = await uploadToCloudinary(selectedFile.value)
    
    console.log('[v0] Sending payment proof URL to backend:', cloudinaryUrl)
    
    // Send the Cloudinary URL to your backend
    const response = await ordersApi.uploadPaymentProof(selectedOrder.value.id, {
      payment_proof_url: cloudinaryUrl,
      comment: paymentComment.value
    })
    
    if (response.success) {
      console.log('[v0] Payment proof saved successfully')
      alert('Preuve de paiement envoyée avec succès!')
      closePaymentModal()
      fetchOrders()
    } else {
      throw new Error(response.message || 'Erreur lors de l\'envoi')
    }
  } catch (error) {
    console.error('[v0] Error uploading payment proof:', error)
    alert('Erreur lors de l\'envoi de la preuve de paiement: ' + (error.message || 'Erreur inconnue'))
  } finally {
    uploading.value = false
    uploadProgress.value = 0
  }
}

const viewPaymentProof = (order) => {
  if (order.preuve_paiement) {
    window.open(order.preuve_paiement, '_blank')
  }
}

const openCancelModal = (order) => {
  selectedOrder.value = order
  showCancelModal.value = true
  cancelReason.value = ''
}

const closeCancelModal = () => {
  showCancelModal.value = false
  selectedOrder.value = null
  cancelReason.value = ''
}

const cancelOrder = async () => {
  if (!cancelReason.value.trim() || !selectedOrder.value) return

  cancelling.value = true
  try {
    console.log('[v0] Cancelling order:', selectedOrder.value.id)
    
    const response = await ordersApi.cancelOrder(selectedOrder.value.id, cancelReason.value)
    
    if (response.success) {
      console.log('[v0] Order cancelled successfully')
      alert('Commande annulée avec succès')
      closeCancelModal()
      fetchOrders()
    } else {
      throw new Error(response.message || 'Erreur lors de l\'annulation')
    }
  } catch (error) {
    console.error('[v0] Error cancelling order:', error)
    alert('Erreur lors de l\'annulation de la commande')
  } finally {
    cancelling.value = false
  }
}

const contactSeller = (order) => {
  console.log('[v0] Contact seller for order:', order.id)
  alert('Fonctionnalité de messagerie à venir')
}

const goToShop = () => {
  router.push('/')
}

onMounted(() => {
  console.log('[v0] My Orders page mounted')
  fetchOrders()
})
</script>

<style scoped>
.my-orders-page {
  min-height: 100vh;
  background: #f5f5f5;
  padding-bottom: 40px;
}

.page-header {
  background: linear-gradient(135deg, #fe9700 0%, #ff8c00 100%);
  color: #fff;
  padding: 40px 0;
  margin-bottom: 32px;
}

.container {
  max-width: 1500px;
  margin: 0 auto;
  padding: 0 16px;
}

.page-title {
  font-size: 32px;
  font-weight: 700;
  margin: 0 0 8px 0;
}

.page-subtitle {
  font-size: 16px;
  opacity: 0.9;
  margin: 0;
}

.filters-section {
  background: #fff;
  border-radius: 8px;
  padding: 16px;
  margin-bottom: 24px;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
}

.filter-tabs {
  display: flex;
  gap: 8px;
  flex-wrap: wrap;
}

.filter-tab {
  display: flex;
  align-items: center;
  gap: 8px;
  padding: 10px 16px;
  background: #f5f5f5;
  border: 2px solid transparent;
  border-radius: 8px;
  color: #666;
  font-size: 14px;
  font-weight: 500;
  cursor: pointer;
  transition: all 0.3s ease;
}

.filter-tab:hover {
  background: #fff7e6;
  color: #fe9700;
}

.filter-tab.active {
  background: #fff7e6;
  border-color: #fe9700;
  color: #fe9700;
}

.tab-badge {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  min-width: 20px;
  height: 20px;
  padding: 0 6px;
  background: #fe9700;
  color: #fff;
  font-size: 12px;
  font-weight: 600;
  border-radius: 10px;
}

.filter-tab.active .tab-badge {
  background: #fff;
  color: #fe9700;
}

.loading-state {
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  padding: 80px 20px;
  text-align: center;
}

.spinner {
  width: 48px;
  height: 48px;
  border: 4px solid #f0f0f0;
  border-top-color: #fe9700;
  border-radius: 50%;
  animation: spin 1s linear infinite;
  margin-bottom: 16px;
}

@keyframes spin {
  to { transform: rotate(360deg); }
}

.loading-state p {
  color: #666;
  font-size: 16px;
}

.empty-state {
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  padding: 80px 20px;
  text-align: center;
  background: #fff;
  border-radius: 8px;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
}

.empty-state h3 {
  font-size: 24px;
  font-weight: 600;
  color: #333;
  margin: 24px 0 8px 0;
}

.empty-state p {
  font-size: 16px;
  color: #666;
  margin: 0 0 24px 0;
}

.primary-btn {
  display: inline-flex;
  align-items: center;
  gap: 8px;
  padding: 12px 24px;
  background: #fe9700;
  color: #fff;
  border: none;
  border-radius: 8px;
  font-size: 15px;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.3s ease;
}

.primary-btn:hover {
  background: #e68900;
  transform: translateY(-2px);
  box-shadow: 0 4px 12px rgba(254, 151, 0, 0.3);
}

.orders-list {
  display: flex;
  flex-direction: column;
  gap: 16px;
}

.order-card {
  background: #fff;
  border-radius: 8px;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
  overflow: hidden;
  transition: all 0.3s ease;
}

.order-card:hover {
  box-shadow: 0 4px 16px rgba(0, 0, 0, 0.12);
}

.order-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 20px 24px;
  background: #fafafa;
  border-bottom: 1px solid #e8e8e8;
}

.order-info {
  display: flex;
  flex-direction: column;
  gap: 4px;
}

.order-number {
  font-size: 18px;
  font-weight: 600;
  color: #333;
  margin: 0;
}

.order-date {
  font-size: 14px;
  color: #666;
}

.order-status {
  padding: 6px 12px;
  border-radius: 6px;
  font-size: 13px;
  font-weight: 600;
  text-transform: uppercase;
  letter-spacing: 0.5px;
}

.order-status.pending {
  background: #fff7e6;
  color: #d46b08;
}

.order-status.confirmed {
  background: #e6f7ff;
  color: #0050b3;
}

.order-status.processing {
  background: #f0f5ff;
  color: #1890ff;
}

.order-status.delivered {
  background: #f6ffed;
  color: #389e0d;
}

.order-status.cancelled {
  background: #fff1f0;
  color: #cf1322;
}

.order-body {
  display: grid;
  grid-template-columns: 1fr 300px;
  gap: 24px;
  padding: 24px;
}

.product-info {
  display: flex;
  gap: 16px;
}

.product-image {
  width: 80px;
  height: 80px;
  object-fit: cover;
  border-radius: 8px;
  border: 1px solid #e8e8e8;
  flex-shrink: 0;
}

.product-details {
  flex: 1;
}

.product-name {
  font-size: 16px;
  font-weight: 600;
  color: #333;
  margin: 0 0 8px 0;
}

.product-meta {
  display: flex;
  flex-direction: column;
  gap: 4px;
  margin-bottom: 8px;
}

.meta-item {
  font-size: 14px;
  color: #666;
}

.product-seller {
  display: flex;
  align-items: center;
  gap: 6px;
  font-size: 14px;
  color: #666;
  padding: 6px 10px;
  background: #f5f5f5;
  border-radius: 6px;
  width: fit-content;
}

.order-summary {
  display: flex;
  flex-direction: column;
  gap: 8px;
  padding: 16px;
  background: #fafafa;
  border-radius: 8px;
}

.summary-row {
  display: flex;
  justify-content: space-between;
  align-items: center;
  font-size: 14px;
}

.summary-label {
  color: #666;
}

.summary-value {
  font-weight: 600;
  color: #333;
}

.summary-row.total {
  padding-top: 8px;
  margin-top: 8px;
  border-top: 2px solid #e8e8e8;
}

.summary-row.total .summary-label {
  font-size: 16px;
  font-weight: 600;
  color: #333;
}

.summary-row.total .summary-value {
  font-size: 18px;
  color: #fe9700;
}

.summary-row.deposit {
  padding: 8px;
  background: #fff7e6;
  border-radius: 6px;
  margin-top: 4px;
}

.summary-row.deposit .summary-label {
  color: #d46b08;
  font-weight: 600;
}

.summary-row.deposit .summary-value {
  color: #d46b08;
  font-size: 16px;
}

.order-delivery {
  padding: 16px 24px;
  background: #f9f9f9;
  border-top: 1px solid #e8e8e8;
}

.delivery-info {
  display: flex;
  gap: 12px;
  font-size: 14px;
}

.delivery-info svg {
  flex-shrink: 0;
  margin-top: 2px;
}

.delivery-info strong {
  display: block;
  margin-bottom: 4px;
  color: #333;
}

.delivery-info p {
  margin: 0;
  color: #666;
  line-height: 1.5;
}

.order-actions {
  display: flex;
  gap: 8px;
  padding: 16px 24px;
  background: #fafafa;
  border-top: 1px solid #e8e8e8;
  flex-wrap: wrap;
}

.action-btn {
  display: inline-flex;
  align-items: center;
  gap: 6px;
  padding: 10px 16px;
  border: none;
  border-radius: 6px;
  font-size: 14px;
  font-weight: 500;
  cursor: pointer;
  transition: all 0.3s ease;
}

.action-btn.primary {
  background: #fe9700;
  color: #fff;
}

.action-btn.primary:hover {
  background: #e68900;
}

.action-btn.success {
  background: #52c41a;
  color: #fff;
  cursor: default;
}

.action-btn.secondary {
  background: #f5f5f5;
  color: #666;
  border: 1px solid #d9d9d9;
}

.action-btn.secondary:hover {
  background: #fff;
  border-color: #fe9700;
  color: #fe9700;
}

.action-btn.danger {
  background: #fff1f0;
  color: #ff4d4f;
  border: 1px solid #ffccc7;
}

.action-btn.danger:hover {
  background: #ff4d4f;
  color: #fff;
  border-color: #ff4d4f;
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
  max-width: 500px;
  width: 100%;
  max-height: 90vh;
  overflow-y: auto;
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

.modal-header {
  margin-bottom: 24px;
}

.modal-title {
  font-size: 24px;
  font-weight: 600;
  color: #333;
  margin: 0 0 8px 0;
}

.modal-subtitle {
  font-size: 14px;
  color: #666;
  margin: 0;
}

.modal-icon {
  display: flex;
  justify-content: center;
  margin-bottom: 20px;
}

.modal-icon.warning {
  background: #fff1f0;
  width: 80px;
  height: 80px;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  margin: 0 auto 20px;
}

.modal-message {
  font-size: 15px;
  color: #666;
  text-align: center;
  line-height: 1.6;
  margin: 0 0 24px 0;
}

.payment-info-box {
  padding: 16px;
  background: #fff7e6;
  border: 1px solid #ffd591;
  border-radius: 8px;
  margin-bottom: 24px;
}

.info-row {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 8px;
}

.info-label {
  font-size: 14px;
  color: #666;
}

.info-value {
  font-size: 18px;
  font-weight: 700;
  color: #d46b08;
}

.info-note {
  margin: 0;
  font-size: 13px;
  color: #ad6800;
  line-height: 1.5;
}

.upload-form,
.cancel-form {
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
  color: #333;
  margin-bottom: 8px;
}

.file-input-wrapper {
  position: relative;
}

.file-input {
  position: absolute;
  width: 100%;
  height: 100%;
  opacity: 0;
  cursor: pointer;
  z-index: 2;
}

.file-input-display {
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  gap: 8px;
  padding: 32px 16px;
  border: 2px dashed #d9d9d9;
  border-radius: 8px;
  background: #fafafa;
  transition: all 0.3s ease;
  cursor: pointer;
}

.file-input-wrapper:hover .file-input-display {
  border-color: #fe9700;
  background: #fff7e6;
}

.file-name {
  font-weight: 600;
  color: #fe9700;
}

.file-hint {
  margin: 8px 0 0 0;
  font-size: 12px;
  color: #999;
}

.upload-progress {
  margin-top: 16px;
  padding: 12px;
  background: #f5f5f5;
  border-radius: 8px;
}

.progress-bar {
  width: 100%;
  height: 8px;
  background: #e8e8e8;
  border-radius: 4px;
  overflow: hidden;
  margin-bottom: 8px;
}

.progress-fill {
  height: 100%;
  background: linear-gradient(90deg, #fe9700 0%, #ff8c00 100%);
  transition: width 0.3s ease;
  border-radius: 4px;
}

.progress-text {
  margin: 0;
  font-size: 13px;
  color: #666;
  text-align: center;
  font-weight: 500;
}

.form-textarea {
  padding: 10px 12px;
  border: 1px solid #d9d9d9;
  border-radius: 6px;
  font-size: 14px;
  font-family: inherit;
  resize: vertical;
  transition: all 0.3s ease;
}

.form-textarea:focus {
  outline: none;
  border-color: #fe9700;
  box-shadow: 0 0 0 2px rgba(254, 151, 0, 0.1);
}

.modal-actions {
  display: flex;
  gap: 12px;
  margin-top: 8px;
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

.modal-btn.primary:hover:not(:disabled) {
  background: #e68900;
}

.modal-btn.primary:disabled {
  opacity: 0.5;
  cursor: not-allowed;
}

.modal-btn.danger {
  background: #ff4d4f;
  color: #fff;
}

.modal-btn.danger:hover:not(:disabled) {
  background: #d9363e;
}

.modal-btn.danger:disabled {
  opacity: 0.5;
  cursor: not-allowed;
}

.modal-btn.secondary {
  background: #f5f5f5;
  color: #666;
}

.modal-btn.secondary:hover {
  background: #e8e8e8;
}

@media (max-width: 768px) {
  .page-title {
    font-size: 24px;
  }

  .order-body {
    grid-template-columns: 1fr;
  }

  .order-actions {
    flex-direction: column;
  }

  .action-btn {
    width: 100%;
    justify-content: center;
  }

  .filter-tabs {
    overflow-x: auto;
    flex-wrap: nowrap;
    padding-bottom: 8px;
  }

  .filter-tab {
    white-space: nowrap;
  }
}
</style>
