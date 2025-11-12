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
            <div class="product-section">
              <div class="product-info">
                <img
                  :src="order.produit_image || '/placeholder.svg?height=100&width=100'"
                  :alt="order.produit_nom"
                  class="product-image"
                  @error="handleImageError"
                >
                <div class="product-details">
                  <h4 class="product-name">{{ order.produit_nom }}</h4>
                  <div class="product-meta">
                    <span class="meta-item">
                      <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <rect x="1" y="4" width="22" height="16" rx="2" ry="2"></rect>
                        <line x1="1" y1="10" x2="23" y2="10"></line>
                      </svg>
                      Quantité: {{ order.quantite }}
                    </span>
                    <span class="meta-item">
                      <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <line x1="12" y1="1" x2="12" y2="23"></line>
                        <path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"></path>
                      </svg>
                      Prix: {{ formatPrice(order.produit_prix) }}
                    </span>
                  </div>
                  <div class="product-seller">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#666" stroke-width="2">
                      <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path>
                      <polyline points="9 22 9 12 15 12 15 22"></polyline>
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
                  <span class="summary-label">Acompte versé (30%):</span>
                  <span class="summary-value">{{ formatPrice(order.total * 0.3) }}</span>
                </div>
              </div>
            </div>

            <!-- New Payment Validation Status Section -->
            <div v-if="order.preuve_paiement" class="payment-validation-section">
              <div class="validation-header">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                  <rect x="2" y="7" width="20" height="14" rx="2" ry="2"></rect>
                  <path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"></path>
                </svg>
                <h4>Statut du paiement</h4>
              </div>

              <div v-if="order.tobevalidate === 'valid'" class="validation-status validated">
                <div class="status-icon">
                  <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#52c41a" stroke-width="3">
                    <polyline points="20 6 9 17 4 12"></polyline>
                  </svg>
                </div>
                <div class="status-content">
                  <h5>Preuve de paiement validée</h5>
                  <p>Votre preuve de paiement a été vérifiée et approuvée par le vendeur</p>
                  <span class="validation-date">Validé le {{ formatDate(order.date_paiement) }}</span>
                  <div v-if="order.commentaire_paiement" class="vendor-comment">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#52c41a" stroke-width="2">
                      <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"></path>
                    </svg>
                    <span>{{ order.commentaire_paiement }}</span>
                  </div>
                </div>
              </div>

              <div v-else-if="order.tobevalidate === 'pending'" class="validation-status pending">
                <div class="status-icon">
                  <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#faad14" stroke-width="2">
                    <circle cx="12" cy="12" r="10"></circle>
                    <polyline points="12 6 12 12 16 14"></polyline>
                  </svg>
                </div>
                <div class="status-content">
                  <h5>En attente de validation</h5>
                  <p>Votre preuve de paiement est en cours de vérification par le vendeur</p>
                  <span class="validation-date">Envoyé le {{ formatDate(order.date_paiement) }}</span>
                </div>
              </div>

              <div v-else class="validation-status pending">
                <div class="status-icon">
                  <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#1890ff" stroke-width="2">
                    <circle cx="12" cy="12" r="10"></circle>
                    <line x1="12" y1="16" x2="12" y2="12"></line>
                    <line x1="12" y1="8" x2="12.01" y2="8"></line>
                  </svg>
                </div>
                <div class="status-content">
                  <h5>Preuve envoyée</h5>
                  <p>Votre preuve de paiement attend la validation du vendeur</p>
                </div>
              </div>
            </div>

            <!-- New Production Progress Section -->
            <div v-if="order.tobevalidate === 'valid' && order.statut !== 'livree' && order.statut !== 'annule'" class="production-section">
              <div class="production-header">
                <div class="header-left">
                  <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <rect x="2" y="3" width="20" height="14" rx="2" ry="2"></rect>
                    <line x1="8" y1="21" x2="16" y2="21"></line>
                    <line x1="12" y1="17" x2="12" y2="21"></line>
                  </svg>
                  <h4>Préparation de votre commande</h4>
                </div>
                <span class="production-badge">En cours</span>
              </div>

              <div class="production-content">
                <div class="production-message">
                  <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#52c41a" stroke-width="2">
                    <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path>
                    <polyline points="22 4 12 14.01 9 11.01"></polyline>
                  </svg>
                  <p>La préparation de votre produit a démarré ! Notre équipe travaille activement sur votre commande.</p>
                </div>

                <div class="timeline-info">
                  <div class="timeline-item">
                    <div class="timeline-icon completed">
                      <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="3">
                        <polyline points="20 6 9 17 4 12"></polyline>
                      </svg>
                    </div>
                    <div class="timeline-content">
                      <h5>Paiement validé</h5>
                      <span>{{ formatDate(order.date_paiement) }}</span>
                    </div>
                  </div>

                  <div class="timeline-line active"></div>

                  <div class="timeline-item">
                    <div class="timeline-icon active">
                      <div class="pulse"></div>
                    </div>
                    <div class="timeline-content">
                      <h5>En préparation</h5>
                      <span>Depuis le {{ formatDate(order.updated_at) }}</span>
                    </div>
                  </div>

                  <div class="timeline-line"></div>

                  <div class="timeline-item">
                    <div class="timeline-icon">
                      <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#999" stroke-width="2">
                        <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path>
                      </svg>
                    </div>
                    <div class="timeline-content">
                      <h5>Prêt pour livraison</h5>
                      <span>Estimé: {{ getEstimatedDeliveryDate(order.updated_at) }}</span>
                    </div>
                  </div>
                </div>

                <div class="countdown-section">
                  <div class="countdown-header">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#1890ff" stroke-width="2">
                      <circle cx="12" cy="12" r="10"></circle>
                      <polyline points="12 6 12 12 16 14"></polyline>
                    </svg>
                    <span>Temps restant avant la livraison</span>
                  </div>

                  <div class="countdown-display">
                    <div class="countdown-item">
                      <span class="countdown-value">{{ getRemainingTime(order.updated_at).days }}</span>
                      <span class="countdown-label">jours</span>
                    </div>
                    <div class="countdown-separator">:</div>
                    <div class="countdown-item">
                      <span class="countdown-value">{{ getRemainingTime(order.updated_at).hours }}</span>
                      <span class="countdown-label">heures</span>
                    </div>
                    <div class="countdown-separator">:</div>
                    <div class="countdown-item">
                      <span class="countdown-value">{{ getRemainingTime(order.updated_at).minutes }}</span>
                      <span class="countdown-label">minutes</span>
                    </div>
                  </div>

                  <div class="progress-bar-wrapper">
                    <div class="progress-bar">
                      <div class="progress-fill" :style="{ width: getProgressPercentage(order.updated_at) + '%' }">
                        <span class="progress-text">{{ Math.floor(getProgressPercentage(order.updated_at)) }}%</span>
                      </div>
                    </div>
                    <p class="progress-label">Progression de la préparation</p>
                  </div>

                  <div class="delivery-estimate">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#52c41a" stroke-width="2">
                      <rect x="1" y="3" width="15" height="13"></rect>
                      <polygon points="16 8 20 8 23 11 23 16 16 16 16 8"></polygon>
                      <circle cx="5.5" cy="18.5" r="2.5"></circle>
                      <circle cx="18.5" cy="18.5" r="2.5"></circle>
                    </svg>
                    <span>Livraison prévue le <strong>{{ getEstimatedDeliveryDate(order.updated_at) }}</strong> (dans max. 2 semaines)</span>
                  </div>
                </div>
              </div>
            </div>

            <div v-if="order.adresse_complete" class="order-delivery">
              <div class="delivery-info">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#666" stroke-width="2">
                  <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path>
                  <circle cx="12" cy="10" r="3"></circle>
                </svg>
                <div>
                  <strong>Adresse de livraison:</strong>
                  <p>{{ order.adresse_complete }}</p>
                  <p v-if="order.ville">{{ order.ville }}, {{ order.commune }}</p>
                  <p v-if="order.instructions_livraison" class="delivery-note">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#999" stroke-width="2">
                      <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                      <polyline points="14 2 14 8 20 8"></polyline>
                      <line x1="16" y1="13" x2="8" y2="13"></line>
                      <line x1="16" y1="17" x2="8" y2="17"></line>
                      <polyline points="10 9 9 9 8 9"></polyline>
                    </svg>
                    Note: {{ order.instructions_livraison }}
                  </p>
                </div>
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
                <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                <circle cx="12" cy="12" r="3"></circle>
              </svg>
              Voir la preuve
            </button>

            <button
              class="action-btn secondary"
              @click="handleChatClick(order)"
            >
              <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"></path>
              </svg>
              Contacter le vendeur
            </button>

            <button
              v-if="order.statut === 'en_attente' && !order.preuve_paiement"
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
            <span class="info-value">{{ formatPrice(selectedOrder?.total * 0.3) }}</span>
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
import { ref, computed, onMounted, onUnmounted } from 'vue'
import { useRouter } from 'vue-router'
import { ordersApi } from '../../services/api.js'
import axios from 'axios'
import { useChatStore } from '../../stores/chat'

const router = useRouter()
const chatStore = useChatStore()

// Cloudinary configuration
const cloudinaryConfig = {
  cloudName: 'daaavha4z',
  uploadPreset: 'aliadjame',
  apiKey: 'wy0Eh-uA0Y0Ci3nyODix0b3WejA',
  imageUploadUrl: 'https://api.cloudinary.com/v1_1/daaavha4z/image/upload'
}

// State
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
const currentTime = ref(new Date())
let countdownInterval = null

const orderStatuses = [
  { value: 'all', label: 'Toutes' },
  { value: 'en_attente', label: 'En attente' },
  { value: 'confirmee', label: 'Confirmée' },
  { value: 'en_cours', label: 'En cours' },
  { value: 'livree', label: 'Livrée' },
  { value: 'annule', label: 'Annulée' }
]

// Computed
const filteredOrders = computed(() => {
  if (selectedStatus.value === 'all') {
    return orders.value
  }
  return orders.value.filter(order => order.statut === selectedStatus.value)
})

// Methods
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

const getRemainingTime = (updatedAt) => {
  const updateDate = new Date(updatedAt)
  const deliveryDate = new Date(updateDate.getTime() + (14 * 24 * 60 * 60 * 1000)) // Add 14 days
  const now = currentTime.value
  const diff = deliveryDate - now

  if (diff <= 0) {
    return { days: 0, hours: 0, minutes: 0, seconds: 0 }
  }

  const days = Math.floor(diff / (1000 * 60 * 60 * 24))
  const hours = Math.floor((diff % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60))
  const minutes = Math.floor((diff % (1000 * 60 * 60)) / (1000 * 60))
  const seconds = Math.floor((diff % (1000 * 60)) / 1000)

  return { days, hours, minutes, seconds }
}

const getEstimatedDeliveryDate = (updatedAt) => {
  const updateDate = new Date(updatedAt)
  const deliveryDate = new Date(updateDate.getTime() + (14 * 24 * 60 * 60 * 1000))
  return deliveryDate.toLocaleDateString('fr-FR', {
    weekday: 'long',
    year: 'numeric',
    month: 'long',
    day: 'numeric'
  })
}

const getProgressPercentage = (updatedAt) => {
  const updateDate = new Date(updatedAt)
  const deliveryDate = new Date(updateDate.getTime() + (14 * 24 * 60 * 60 * 1000))
  const now = currentTime.value

  const totalTime = deliveryDate - updateDate
  const elapsed = now - updateDate

  if (elapsed <= 0) return 0
  if (elapsed >= totalTime) return 100
  if (totalTime <= 0) return 0

  return Math.min(Math.floor((elapsed / totalTime) * 100), 100)
}

const handleImageError = (event) => {
  event.target.src = '/placeholder.svg?height=100&width=100'
}

const fetchOrders = async () => {
  loading.value = true

  try {
    const userData = localStorage.getItem('user') || sessionStorage.getItem('user')
    if (!userData) {
      orders.value = []
      loading.value = false
      return
    }

    const parsedUser = JSON.parse(userData)
    const userId = parsedUser.id

    const response = await ordersApi.getMyOrders(userId)

    if (response.success) {
      orders.value = response.data || []
    } else {
      orders.value = []
    }
  } catch (error) {
    console.error('Error fetching orders:', error)
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

const handleChatClick = async (order) => {
  const productData = {
    id: order.produit_id,
    name: order.produit_nom,
    slug: order.produit_slug || '',
    unit_price: order.produit_prix,
    boutique_id: order.boutique_id,
    boutique_name: order.boutique_nom,
    market: order.ville || 'Abidjan',
    experience: order.boutique_experience || 0,
    primary_image: order.produit_image,
    rating: order.rating || 0,
    views: order.views || 0,
    wholesale_min_qty: order.wholesale_min_qty || null,
    wholesale_price: order.wholesale_price || null,
  }

  try {
    await chatStore.setSupplier(productData)

    if (chatStore.isMobile) {
      chatStore.openChat()
    } else {
      chatStore.openDesktopChat()
    }
  } catch (error) {
    console.error('Error opening chat:', error)
  }
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
  }
}

const uploadToCloudinary = async (file) => {
  try {
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
      }
    })

    if (response.data && response.data.secure_url) {
      return response.data.secure_url
    } else {
      throw new Error('Réponse Cloudinary invalide')
    }
  } catch (error) {
    console.error('Cloudinary upload error:', error)
    throw error
  }
}

const uploadPaymentProof = async () => {
  if (!selectedFile.value || !selectedOrder.value) return

  uploading.value = true
  try {
    const cloudinaryUrl = await uploadToCloudinary(selectedFile.value)

    const response = await ordersApi.uploadPaymentProof(selectedOrder.value.id, {
      payment_proof_url:cloudinaryUrl,
      comment: paymentComment.value
    })

    if (response.success) {
      alert('Preuve de paiement envoyée avec succès!')
      closePaymentModal()
      fetchOrders()
    } else {
      throw new Error(response.message || 'Erreur lors de l\'envoi')
    }
  } catch (error) {
    console.error('Error uploading payment proof:', error)
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
    const response = await ordersApi.cancelOrder(selectedOrder.value.id, cancelReason.value)

    if (response.success) {
      alert('Commande annulée avec succès')
      closeCancelModal()
      fetchOrders()
    } else {
      throw new Error(response.message || 'Erreur lors de l\'annulation')
    }
  } catch (error) {
    console.error('Error cancelling order:', error)
    alert('Erreur lors de l\'annulation de la commande')
  } finally {
    cancelling.value = false
  }
}

const goToShop = () => {
  router.push('/')
}

onMounted(() => {
  fetchOrders()

  countdownInterval = setInterval(() => {
    currentTime.value = new Date()
  }, 1000)
})

onUnmounted(() => {
  if (countdownInterval) {
    clearInterval(countdownInterval)
  }
})
</script>

<style scoped>
.my-orders-page {
  min-height: 100vh;
  background: linear-gradient(135deg, #f5f7fa 0%, #f0f2f5 100%);
  padding-bottom: 60px;
}

.page-header {
  background: linear-gradient(135deg, #fe9700 0%, #ff8c00 100%);
  color: #fff;
  padding: 50px 0;
  margin-bottom: 40px;
  box-shadow: 0 4px 20px rgba(254, 151, 0, 0.3);
}

.container {
  max-width: 1400px;
  margin: 0 auto;
  padding: 0 20px;
}

.page-title {
  font-size: 36px;
  font-weight: 700;
  margin: 0 0 10px 0;
  letter-spacing: -0.5px;
}

.page-subtitle {
  font-size: 17px;
  opacity: 0.95;
  margin: 0;
  font-weight: 400;
}

.filters-section {
  background: #fff;
  border-radius: 12px;
  padding: 20px;
  margin-bottom: 30px;
  box-shadow: 0 2px 12px rgba(0, 0, 0, 0.06);
}

.filter-tabs {
  display: flex;
  gap: 12px;
  flex-wrap: wrap;
}

.filter-tab {
  display: flex;
  align-items: center;
  gap: 10px;
  padding: 12px 20px;
  background: #f8f9fa;
  border: 2px solid transparent;
  border-radius: 10px;
  color: #666;
  font-size: 15px;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.3s ease;
}

.filter-tab:hover {
  background: #fff7e6;
  color: #fe9700;
  transform: translateY(-2px);
}

.filter-tab.active {
  background: linear-gradient(135deg, #fff7e6 0%, #ffe7ba 100%);
  border-color: #fe9700;
  color: #fe9700;
  box-shadow: 0 4px 12px rgba(254, 151, 0, 0.2);
}

.tab-badge {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  min-width: 24px;
  height: 24px;
  padding: 0 8px;
  background: #fe9700;
  color: #fff;
  font-size: 13px;
  font-weight: 700;
  border-radius: 12px;
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
  padding: 100px 20px;
  text-align: center;
}

.spinner {
  width: 56px;
  height: 56px;
  border: 5px solid #f0f0f0;
  border-top-color: #fe9700;
  border-radius: 50%;
  animation: spin 0.8s linear infinite;
  margin-bottom: 20px;
}

@keyframes spin {
  to { transform: rotate(360deg); }
}

.loading-state p {
  color: #666;
  font-size: 17px;
  font-weight: 500;
}

.empty-state {
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  padding: 100px 20px;
  text-align: center;
  background: #fff;
  border-radius: 16px;
  box-shadow: 0 4px 20px rgba(0, 0, 0, 0.06);
}

.empty-state h3 {
  font-size: 26px;
  font-weight: 700;
  color: #333;
  margin: 30px 0 10px 0;
}

.empty-state p {
  font-size: 17px;
  color: #666;
  margin: 0 0 30px 0;
}

.primary-btn {
  display: inline-flex;
  align-items: center;
  gap: 10px;
  padding: 14px 28px;
  background: linear-gradient(135deg, #fe9700 0%, #ff8c00 100%);
  color: #fff;
  border: none;
  border-radius: 10px;
  font-size: 16px;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.3s ease;
}

.primary-btn:hover {
  background: linear-gradient(135deg, #ff8c00 0%, #e68900 100%);
  transform: translateY(-3px);
  box-shadow: 0 6px 20px rgba(254, 151, 0, 0.4);
}

.orders-list {
  display: flex;
  flex-direction: column;
  gap: 24px;
}

.order-card {
  background: #fff;
  border-radius: 16px;
  box-shadow: 0 4px 20px rgba(0, 0, 0, 0.06);
  overflow: hidden;
  transition: all 0.3s ease;
  border: 1px solid #f0f0f0;
}

.order-card:hover {
  box-shadow: 0 8px 30px rgba(0, 0, 0, 0.1);
  transform: translateY(-4px);
}

.order-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 24px 28px;
  background: linear-gradient(135deg, #fafbfc 0%, #f5f7fa 100%);
  border-bottom: 1px solid #e8eaed;
}

.order-info {
  display: flex;
  flex-direction: column;
  gap: 6px;
}

.order-number {
  font-size: 20px;
  font-weight: 700;
  color: #1a1a1a;
  margin: 0;
}

.order-date {
  font-size: 14px;
  color: #666;
  font-weight: 500;
}

.order-status {
  padding: 8px 16px;
  border-radius: 8px;
  font-size: 13px;
  font-weight: 700;
  text-transform: uppercase;
  letter-spacing: 0.8px;
}

.order-status.pending {
  background: linear-gradient(135deg, #fff7e6 0%, #ffe7ba 100%);
  color: #d46b08;
  border: 1px solid #ffd591;
}

.order-status.confirmed {
  background: linear-gradient(135deg, #e6f7ff 0%, #bae7ff 100%);
  color: #0050b3;
  border: 1px solid #91d5ff;
}

.order-status.processing {
  background: linear-gradient(135deg, #f0f5ff 0%, #d6e4ff 100%);
  color: #1890ff;
  border: 1px solid #adc6ff;
}

.order-status.delivered {
  background: linear-gradient(135deg, #f6ffed 0%, #d9f7be 100%);
  color: #389e0d;
  border: 1px solid #b7eb8f;
}

.order-status.cancelled {
  background: linear-gradient(135deg, #fff1f0 0%, #ffccc7 100%);
  color: #cf1322;
  border: 1px solid #ffa39e;
}

.order-body {
  padding: 28px;
}

.product-section {
  display: grid;
  grid-template-columns: 1fr 320px;
  gap: 28px;
  padding-bottom: 28px;
  border-bottom: 2px solid #f0f0f0;
  margin-bottom: 24px;
}

.product-info {
  display: flex;
  gap: 20px;
}

.product-image {
  width: 100px;
  height: 100px;
  object-fit: cover;
  border-radius: 12px;
  border: 2px solid #e8eaed;
  flex-shrink: 0;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
}

.product-details {
  flex: 1;
}

.product-name {
  font-size: 18px;
  font-weight: 700;
  color: #1a1a1a;
  margin: 0 0 12px 0;
  line-height: 1.4;
}

.product-meta {
  display: flex;
  flex-direction: column;
  gap: 8px;
  margin-bottom: 12px;
}

.meta-item {
  display: flex;
  align-items: center;
  gap: 8px;
  font-size: 15px;
  color: #555;
  font-weight: 500;
}

.product-seller {
  display: flex;
  align-items: center;
  gap: 8px;
  font-size: 15px;
  color: #666;
  padding: 10px 14px;
  background: linear-gradient(135deg, #f8f9fa 0%, #f0f2f5 100%);
  border-radius: 8px;
  width: fit-content;
  font-weight: 600;
}

.order-summary {
  display: flex;
  flex-direction: column;
  gap: 10px;
  padding: 20px;
  background: linear-gradient(135deg, #fafbfc 0%, #f5f7fa 100%);
  border-radius: 12px;
  border: 1px solid #e8eaed;
}

.summary-row {
  display: flex;
  justify-content: space-between;
  align-items: center;
  font-size: 15px;
}

.summary-label {
  color: #666;
  font-weight: 500;
}

.summary-value {
  font-weight: 700;
  color: #333;
}

.summary-row.total {
  padding-top: 12px;
  margin-top: 10px;
  border-top: 2px solid #e8eaed;
}

.summary-row.total .summary-label {
  font-size: 17px;
  font-weight: 700;
  color: #1a1a1a;
}

.summary-row.total .summary-value {
  font-size: 20px;
  color: #fe9700;
}

.summary-row.deposit {
  padding: 12px;
  background: linear-gradient(135deg, #fff7e6 0%, #ffe7ba 100%);
  border-radius: 8px;
  margin-top: 6px;
  border: 1px solid #ffd591;
}

.summary-row.deposit .summary-label {
  color: #d46b08;
  font-weight: 700;
}

.summary-row.deposit .summary-value {
  color: #d46b08;
  font-size: 17px;
}

/* New styles for payment validation section */
.payment-validation-section {
  background: linear-gradient(135deg, #f8fafb 0%, #f0f4f7 100%);
  border-radius: 12px;
  padding: 24px;
  margin-bottom: 24px;
  border: 1px solid #e0e6ed;
}

.validation-header {
  display: flex;
  align-items: center;
  gap: 12px;
  margin-bottom: 20px;
}

.validation-header h4 {
  margin: 0;
  font-size: 18px;
  font-weight: 700;
  color: #1a1a1a;
}

.validation-status {
  display: flex;
  gap: 16px;
  padding: 20px;
  border-radius: 10px;
}

.validation-status.validated {
  background: linear-gradient(135deg, #f6ffed 0%, #d9f7be 100%);
  border: 2px solid #b7eb8f;
}

.validation-status.pending {
  background: linear-gradient(135deg, #fffbe6 0%, #fff1b8 100%);
  border: 2px solid #ffe58f;
}

.status-icon {
  width: 48px;
  height: 48px;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  background: #d9d9d9;
  flex-shrink: 0;
}

.validation-status.validated .status-icon {
  background: #52c41a;
}

.validation-status.pending .status-icon {
  background: #faad14;
}

.status-content {
  flex: 1;
}

.status-content h5 {
  margin: 0 0 8px 0;
  font-size: 17px;
  font-weight: 700;
  color: #1a1a1a;
}

.status-content p {
  margin: 0 0 10px 0;
  font-size: 15px;
  color: #555;
  line-height: 1.5;
}

.validation-date {
  display: inline-block;
  font-size: 13px;
  color: #666;
  font-weight: 600;
  padding: 4px 10px;
  background: rgba(255, 255, 255, 0.7);
  border-radius: 6px;
}

.vendor-comment {
  display: flex;
  align-items: flex-start;
  gap: 10px;
  margin-top: 12px;
  padding: 12px;
  background: rgba(255, 255, 255, 0.8);
  border-radius: 8px;
  font-size: 14px;
  color: #333;
  line-height: 1.5;
  border-left: 3px solid #52c41a;
}

/* New styles for production progress section */
.production-section {
  background: linear-gradient(135deg, #fff 0%, #f8fafb 100%);
  border-radius: 12px;
  padding: 24px;
  margin-bottom: 24px;
  border: 2px solid #1890ff;
  box-shadow: 0 4px 16px rgba(24, 144, 255, 0.1);
}

.production-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 24px;
}

.header-left {
  display: flex;
  align-items: center;
  gap: 12px;
}

.header-left h4 {
  margin: 0;
  font-size: 19px;
  font-weight: 700;
  color: #1a1a1a;
}

.production-badge {
  padding: 6px 14px;
  background: linear-gradient(135deg, #1890ff 0%, #096dd9 100%);
  color: #fff;
  font-size: 13px;
  font-weight: 700;
  border-radius: 20px;
  text-transform: uppercase;
  letter-spacing: 0.5px;
  animation: pulse-badge 2s ease-in-out infinite;
}

@keyframes pulse-badge {
  0%, 100% {
    transform: scale(1);
    box-shadow: 0 0 0 0 rgba(24, 144, 255, 0.7);
  }
  50% {
    transform: scale(1.05);
    box-shadow: 0 0 0 6px rgba(24, 144, 255, 0);
  }
}

.production-content {
  display: flex;
  flex-direction: column;
  gap: 24px;
}

.production-message {
  display: flex;
  align-items: flex-start;
  gap: 12px;
  padding: 16px;
  background: linear-gradient(135deg, #f6ffed 0%, #d9f7be 100%);
  border-radius: 10px;
  border-left: 4px solid #52c41a;
}

.production-message p {
  margin: 0;
  font-size: 15px;
  color: #333;
  line-height: 1.6;
  font-weight: 500;
}

.timeline-info {
  display: flex;
  align-items: center;
  gap: 0;
  padding: 20px;
  background: linear-gradient(135deg, #fafbfc 0%, #f5f7fa 100%);
  border-radius: 10px;
}

.timeline-item {
  display: flex;
  align-items: center;
  gap: 12px;
  flex: 1;
}

.timeline-icon {
  width: 40px;
  height: 40px;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  background: #d9d9d9;
  flex-shrink: 0;
  position: relative;
}

.timeline-icon.completed {
  background: #52c41a;
}

.timeline-icon.active {
  background: #1890ff;
}

.timeline-icon.active .pulse {
  position: absolute;
  width: 100%;
  height: 100%;
  border-radius: 50%;
  background: #1890ff;
  animation: pulse-icon 2s ease-in-out infinite;
}

@keyframes pulse-icon {
  0%, 100% {
    transform: scale(1);
    opacity: 1;
  }
  50% {
    transform: scale(1.4);
    opacity: 0;
  }
}

.timeline-content {
  display: flex;
  flex-direction: column;
  gap: 4px;
}

.timeline-content h5 {
  margin: 0;
  font-size: 15px;
  font-weight: 700;
  color: #1a1a1a;
}

.timeline-content span {
  font-size: 13px;
  color: #666;
  font-weight: 500;
}

.timeline-line {
  width: 50px;
  height: 2px;
  background: #d9d9d9;
  flex-shrink: 0;
}

.timeline-line.active {
  background: linear-gradient(90deg, #1890ff 0%, #d9d9d9 100%);
}

.countdown-section {
  display: flex;
  flex-direction: column;
  gap: 20px;
  padding: 24px;
  background: linear-gradient(135deg, #e6f7ff 0%, #bae7ff 100%);
  border-radius: 12px;
  border: 2px solid #91d5ff;
}

.countdown-header {
  display: flex;
  align-items: center;
  gap: 10px;
  font-size: 16px;
  font-weight: 700;
  color: #0050b3;
}

.countdown-display {
  display: flex;
  justify-content: center;
  align-items: center;
  gap: 12px;
}

.countdown-item {
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 8px;
  padding: 16px 24px;
  background: #fff;
  border-radius: 10px;
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
  min-width: 80px;
}

.countdown-value {
  font-size: 32px;
  font-weight: 800;
  color: #1890ff;
  line-height: 1;
}

.countdown-label {
  font-size: 13px;
  font-weight: 600;
  color: #666;
  text-transform: uppercase;
  letter-spacing: 0.5px;
}

.countdown-separator {
  font-size: 32px;
  font-weight: 800;
  color: #1890ff;
}

.progress-bar-wrapper {
  display: flex;
  flex-direction: column;
  gap: 8px;
}

.progress-bar {
  width: 100%;
  height: 32px;
  background: rgba(255, 255, 255, 0.8);
  border-radius: 16px;
  overflow: hidden;
  box-shadow: inset 0 2px 8px rgba(0, 0, 0, 0.1);
}

.progress-fill {
  height: 100%;
  background: linear-gradient(90deg, #52c41a 0%, #73d13d 100%);
  border-radius: 16px;
  display: flex;
  align-items: center;
  justify-content: flex-end;
  padding-right: 12px;
  transition: width 0.5s ease;
  position: relative;
  overflow: hidden;
}

.progress-fill::before {
  content: '';
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.3), transparent);
  animation: shimmer 2s infinite;
}

@keyframes shimmer {
  0% {
    transform: translateX(-100%);
  }
  100% {
    transform: translateX(100%);
  }
}

.progress-text {
  font-size: 14px;
  font-weight: 800;
  color: #fff;
  z-index: 1;
}

.progress-label {
  margin: 0;
  font-size: 14px;
  color: #0050b3;
  font-weight: 600;
  text-align: center;
}

.delivery-estimate {
  display: flex;
  align-items: center;
  gap: 10px;
  padding: 14px;
  background: rgba(255, 255, 255, 0.9);
  border-radius: 8px;
  font-size: 15px;
  color: #333;
  font-weight: 500;
  border-left: 4px solid #52c41a;
}

.delivery-estimate strong {
  color: #52c41a;
  font-weight: 700;
}

.order-delivery {
  padding: 20px;
  background: linear-gradient(135deg, #fafbfc 0%, #f5f7fa 100%);
  border-radius: 10px;
  border: 1px solid #e8eaed;
  margin-bottom: 24px;
}

.delivery-info {
  display: flex;
  gap: 14px;
  font-size: 15px;
}

.delivery-info svg {
  flex-shrink: 0;
  margin-top: 3px;
}

.delivery-info strong {
  display: block;
  margin-bottom: 8px;
  color: #1a1a1a;
  font-size: 16px;
}

.delivery-info p {
  margin: 0 0 6px 0;
  color: #555;
  line-height: 1.6;
}

.delivery-note {
  display: flex;
  align-items: flex-start;
  gap: 8px;
  margin-top: 12px !important;
  padding: 10px;
  background: rgba(255, 255, 255, 0.8);
  border-radius: 6px;
  font-size: 14px;
  color: #666;
  font-style: italic;
}

.order-actions {
  display: flex;
  gap: 12px;
  padding: 20px 28px;
  background: #fafbfc;
  border-top: 1px solid #e8eaed;
  flex-wrap: wrap;
}

.action-btn {
  display: inline-flex;
  align-items: center;
  gap: 8px;
  padding: 12px 20px;
  border: none;
  border-radius: 8px;
  font-size: 15px;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.3s ease;
}

.action-btn.primary {
  background: linear-gradient(135deg, #fe9700 0%, #ff8c00 100%);
  color: #fff;
}

.action-btn.primary:hover {
  background: linear-gradient(135deg, #ff8c00 0%, #e68900 100%);
  transform: translateY(-2px);
  box-shadow: 0 4px 12px rgba(254, 151, 0, 0.3);
}

.action-btn.success {
  background: linear-gradient(135deg, #52c41a 0%, #389e0d 100%);
  color: #fff;
}

.action-btn.success:hover {
  background: linear-gradient(135deg, #389e0d 0%, #237804 100%);
  transform: translateY(-2px);
  box-shadow: 0 4px 12px rgba(82, 196, 26, 0.3);
}

.action-btn.secondary {
  background: #fff;
  color: #666;
  border: 2px solid #d9d9d9;
}

.action-btn.secondary:hover {
  background: #f5f5f5;
  border-color: #fe9700;
  color: #fe9700;
}

.action-btn.danger {
  background: #fff;
  color: #ff4d4f;
  border: 2px solid #ffccc7;
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
  background: rgba(0, 0, 0, 0.65);
  display: flex;
  align-items: center;
  justify-content: center;
  z-index: 1000;
  padding: 20px;
  backdrop-filter: blur(4px);
}

.modal-content {
  background: #fff;
  border-radius: 16px;
  padding: 36px;
  max-width: 540px;
  width: 100%;
  max-height: 90vh;
  overflow-y: auto;
  position: relative;
  box-shadow: 0 12px 40px rgba(0, 0, 0, 0.3);
}

.modal-close {
  position: absolute;
  top: 20px;
  right: 20px;
  width: 36px;
  height: 36px;
  border: none;
  background: #f5f5f5;
  color: #999;
  cursor: pointer;
  transition: all 0.3s ease;
  display: flex;
  align-items: center;
  justify-content: center;
  border-radius: 50%;
}

.modal-close:hover {
  background: #ff4d4f;
  color: #fff;
  transform: rotate(90deg);
}

.modal-header {
  margin-bottom: 28px;
}

.modal-title {
  font-size: 26px;
  font-weight: 700;
  color: #1a1a1a;
  margin: 0 0 10px 0;
}

.modal-subtitle {
  font-size: 15px;
  color: #666;
  margin: 0;
  font-weight: 500;
}

.modal-icon {
  display: flex;
  justify-content: center;
  margin-bottom: 24px;
}

.modal-icon.warning {
  background: linear-gradient(135deg, #fff1f0 0%, #ffccc7 100%);
  width: 90px;
  height: 90px;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  margin: 0 auto 24px;
  border: 3px solid #ffa39e;
}

.modal-message {
  font-size: 16px;
  color: #555;
  text-align: center;
  line-height: 1.7;
  margin: 0 0 28px 0;
}

.payment-info-box {
  padding: 20px;
  background: linear-gradient(135deg, #fff7e6 0%, #ffe7ba 100%);
  border: 2px solid #ffd591;
  border-radius: 10px;
  margin-bottom: 28px;
}

.info-row {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 10px;
}

.info-label {
  font-size: 15px;
  color: #666;
  font-weight: 600;
}

.info-value {
  font-size: 22px;
  font-weight: 800;
  color: #d46b08;
}

.info-note {
  margin: 0;
  font-size: 14px;
  color: #ad6800;
  line-height: 1.6;
  font-weight: 500;
}

.upload-form,
.cancel-form {
  display: flex;
  flex-direction: column;
  gap: 24px;
}

.form-group {
  display: flex;
  flex-direction: column;
}

.form-label {
  font-size: 15px;
  font-weight: 600;
  color: #1a1a1a;
  margin-bottom: 10px;
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
  gap: 10px;
  padding: 40px 20px;
  border: 3px dashed #d9d9d9;
  border-radius: 10px;
  background: #fafafa;
  transition: all 0.3s ease;
  cursor: pointer;
}

.file-input-wrapper:hover .file-input-display {
  border-color: #fe9700;
  background: #fff7e6;
}

.file-name {
  font-weight: 700;
  color: #fe9700;
  font-size: 15px;
}

.file-hint {
  margin: 10px 0 0 0;
  font-size: 13px;
  color: #999;
  font-weight: 500;
}

.upload-progress {
  margin-top: 20px;
  padding: 16px;
  background: #f5f5f5;
  border-radius: 10px;
}

.progress-bar {
  width: 100%;
  height: 10px;
  background: #e8e8e8;
  border-radius: 5px;
  overflow: hidden;
  margin-bottom: 10px;
}

.progress-fill {
  height: 100%;
  background: linear-gradient(90deg, #fe9700 0%, #ff8c00 100%);
  transition: width 0.3s ease;
  border-radius: 5px;
}

.progress-text {
  margin: 0;
  font-size: 14px;
  color: #666;
  text-align: center;
  font-weight: 600;
}

.form-textarea {
  padding: 12px 14px;
  border: 2px solid #d9d9d9;
  border-radius: 8px;
  font-size: 15px;
  font-family: inherit;
  resize: vertical;
  transition: all 0.3s ease;
  line-height: 1.6;
}

.form-textarea:focus {
  outline: none;
  border-color: #fe9700;
  box-shadow: 0 0 0 3px rgba(254, 151, 0, 0.1);
}

.modal-actions {
  display: flex;
  gap: 14px;
  margin-top: 10px;
}

.modal-btn {
  flex: 1;
  padding: 14px 28px;
  border: none;
  border-radius: 10px;
  font-size: 16px;
  font-weight: 700;
  cursor: pointer;
  transition: all 0.3s ease;
}

.modal-btn.primary {
  background: linear-gradient(135deg, #fe9700 0%, #ff8c00 100%);
  color: #fff;
}

.modal-btn.primary:hover:not(:disabled) {
  background: linear-gradient(135deg, #ff8c00 0%, #e68900 100%);
  transform: translateY(-2px);
  box-shadow: 0 6px 20px rgba(254, 151, 0, 0.4);
}

.modal-btn.primary:disabled {
  opacity: 0.5;
  cursor: not-allowed;
}

.modal-btn.danger {
  background: linear-gradient(135deg, #ff4d4f 0%, #cf1322 100%);
  color: #fff;
}

.modal-btn.danger:hover:not(:disabled) {
  background: linear-gradient(135deg, #cf1322 0%, #a8071a 100%);
  transform: translateY(-2px);
  box-shadow: 0 6px 20px rgba(255, 77, 79, 0.4);
}

.modal-btn.danger:disabled {
  opacity: 0.5;
  cursor: not-allowed;
}

.modal-btn.secondary {
  background: #f5f5f5;
  color: #666;
  border: 2px solid #d9d9d9;
}

.modal-btn.secondary:hover {
  background: #e8e8e8;
  border-color: #bfbfbf;
}

@media (max-width: 1024px) {
  .product-section {
    grid-template-columns: 1fr;
  }

  .timeline-info {
    flex-direction: column;
    gap: 16px;
  }

  .timeline-line {
    width: 2px;
    height: 30px;
    margin-left: 19px;
  }
}

@media (max-width: 768px) {
  .page-title {
    font-size: 28px;
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
    padding-bottom: 10px;
  }

  .filter-tab {
    white-space: nowrap;
  }

  .countdown-item {
    min-width: 70px;
    padding: 12px 16px;
  }

  .countdown-value {
    font-size: 26px;
  }
}
</style>
