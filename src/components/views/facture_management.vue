<template>
  <div class="min-h-screen bg-gray-50 relative overflow-hidden">
    <!-- Animation de fond luxueuse avec couleurs plus intenses -->
    <div class="absolute inset-0 overflow-hidden pointer-events-none z-0">
      <!-- Gradient animé principal -->
      <div class="absolute inset-0 bg-gradient-to-br from-gray-50 via-white to-gray-100"></div>
      
      <!-- Orbes flottants avec couleurs plus intenses -->
      <div class="absolute -top-20 -left-20 w-96 h-96 bg-gradient-to-br from-orange-200/60 to-orange-300/40 rounded-full blur-3xl animate-float-slow"></div>
      <div class="absolute top-1/4 -right-20 w-80 h-80 bg-gradient-to-br from-blue-200/50 to-indigo-200/35 rounded-full blur-3xl animate-float-reverse"></div>
      <div class="absolute -bottom-20 left-1/3 w-72 h-72 bg-gradient-to-br from-purple-200/45 to-pink-200/35 rounded-full blur-3xl animate-float-diagonal"></div>
      <div class="absolute top-1/2 right-1/4 w-64 h-64 bg-gradient-to-br from-emerald-200/40 to-teal-200/30 rounded-full blur-3xl animate-float-slow-reverse"></div>
      
      <!-- Particules plus visibles -->
      <div class="absolute top-1/3 left-1/4 w-3 h-3 bg-orange-400/70 rounded-full animate-pulse-slow shadow-lg"></div>
      <div class="absolute top-2/3 right-1/4 w-2.5 h-2.5 bg-blue-400/60 rounded-full animate-pulse-delayed shadow-lg"></div>
      <div class="absolute top-1/2 left-2/3 w-2 h-2 bg-purple-400/50 rounded-full animate-pulse-slow shadow-lg"></div>
      
      <!-- Lignes géométriques plus visibles -->
      <div class="absolute top-0 left-1/4 w-px h-40 bg-gradient-to-b from-transparent via-orange-300/40 to-transparent animate-slide-down"></div>
      <div class="absolute top-1/3 right-1/3 w-32 h-px bg-gradient-to-r from-transparent via-blue-300/35 to-transparent animate-slide-right"></div>
    </div>

    <!-- Container principal -->
    <div class="w-full max-w-[1650px] mx-auto px-4 sm:px-6 py-4 sm:py-8 relative z-10">
      <!-- Breadcrumb -->
      <div class="flex items-center text-sm text-gray-500 mb-4 sm:mb-6">
        <Home class="w-4 h-4 sm:w-5 sm:h-5" />
        <span class="mx-2">/</span>
        <span class="font-medium text-gray-700">Gestion des Factures</span>
      </div>

      <!-- Header -->
      <div class="flex flex-col space-y-4 lg:flex-row lg:items-center lg:justify-between lg:space-y-0 mb-6 sm:mb-8">
        <div>
          <h1 class="text-2xl sm:text-3xl font-bold text-gray-900 mb-2">Gestion des Factures</h1>
          <p class="text-sm sm:text-base text-gray-600">Créez et gérez vos factures en temps réel</p>
        </div>
        
        <div class="flex items-center gap-3">
          <button 
            @click="resetForm"
            class="inline-flex items-center px-4 py-2 rounded-lg shadow-sm text-sm font-medium text-gray-700 bg-white border border-gray-300 hover:bg-gray-50 transition-all"
          >
            <RefreshCw class="w-4 h-4 mr-2" />
            Réinitialiser
          </button>
          <button 
            @click="downloadPDF"
            class="inline-flex items-center px-4 py-2 rounded-lg shadow-sm text-sm font-medium text-white bg-gradient-to-r from-orange-500 to-orange-600 hover:from-orange-600 hover:to-orange-700 transition-all"
          >
            <Download class="w-4 h-4 mr-2" />
            Télécharger PDF
          </button>
        </div>
      </div>

      <!-- Contenu principal : Formulaire + Aperçu -->
      <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Formulaire (1/3) -->
        <div class="lg:col-span-1">
          <div class="bg-white shadow-lg rounded-lg border border-gray-100 p-6 sticky top-6">
            <h2 class="text-lg font-bold text-gray-900 mb-6 flex items-center">
              <FileText class="w-5 h-5 mr-2 text-orange-500" />
              Informations de la facture
            </h2>

            <!-- Informations générales -->
            <div class="space-y-4 mb-6">
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Numéro de facture</label>
                <input 
                  v-model="invoice.number" 
                  type="text" 
                  class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-transparent"
                  placeholder="FAC-2025-001"
                >
              </div>

              <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Date de facture</label>
                <input 
                  v-model="invoice.date" 
                  type="date" 
                  class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-transparent"
                >
              </div>

              <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Date d'échéance</label>
                <input 
                  v-model="invoice.dueDate" 
                  type="date" 
                  class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-transparent"
                >
              </div>
            </div>

            <!-- Informations client -->
            <div class="border-t border-gray-200 pt-6 mb-6">
              <h3 class="text-md font-semibold text-gray-900 mb-4 flex items-center">
                <User class="w-4 h-4 mr-2 text-orange-500" />
                Informations client
              </h3>
              <div class="space-y-4">
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-2">Nom du client</label>
                  <input 
                    v-model="invoice.client.name" 
                    type="text" 
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-transparent"
                    placeholder="Nom complet"
                  >
                </div>

                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-2">Email</label>
                  <input 
                    v-model="invoice.client.email" 
                    type="email" 
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-transparent"
                    placeholder="email@exemple.com"
                  >
                </div>

                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-2">Téléphone</label>
                  <input 
                    v-model="invoice.client.phone" 
                    type="tel" 
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-transparent"
                    placeholder="+225 XX XX XX XX XX"
                  >
                </div>

                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-2">Adresse</label>
                  <textarea 
                    v-model="invoice.client.address" 
                    rows="3"
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-transparent"
                    placeholder="Adresse complète"
                  ></textarea>
                </div>
              </div>
            </div>

            <!-- Articles -->
            <div class="border-t border-gray-200 pt-6">
              <div class="flex items-center justify-between mb-4">
                <h3 class="text-md font-semibold text-gray-900 flex items-center">
                  <ShoppingCart class="w-4 h-4 mr-2 text-orange-500" />
                  Articles
                </h3>
                <button 
                  @click="addItem"
                  class="inline-flex items-center px-3 py-1.5 rounded-lg text-xs font-medium text-white bg-gradient-to-r from-orange-500 to-orange-600 hover:from-orange-600 hover:to-orange-700 transition-all"
                >
                  <Plus class="w-3 h-3 mr-1" />
                  Ajouter
                </button>
              </div>

              <!-- Loading state for products -->
              <div v-if="loadingProducts" class="text-center py-4 text-sm text-gray-500">
                Chargement des produits...
              </div>

              <div class="space-y-4 max-h-96 overflow-y-auto">
                <div 
                  v-for="(item, index) in invoice.items" 
                  :key="index"
                  class="p-4 bg-gray-50 rounded-lg border border-gray-200 relative"
                >
                  <button 
                    @click="removeItem(index)"
                    class="absolute top-2 right-2 p-1 text-red-500 hover:bg-red-50 rounded transition-colors"
                  >
                    <X class="w-4 h-4" />
                  </button>

                  <div class="space-y-3">
                    <!-- Replace text input with select dropdown for products -->
                    <div>
                      <label class="block text-xs font-medium text-gray-700 mb-1">Produit</label>
                      <select 
                        v-model="item.productId"
                        @change="onProductSelect(index)"
                        class="w-full px-2 py-1.5 text-sm border border-gray-300 rounded focus:ring-2 focus:ring-orange-500 focus:border-transparent bg-white"
                      >
                        <option value="">Sélectionner un produit</option>
                        <option 
                          v-for="product in products" 
                          :key="product.id" 
                          :value="product.id"
                        >
                          {{ product.name }}
                        </option>
                      </select>
                    </div>

                    <div class="grid grid-cols-2 gap-2">
                      <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1">Quantité</label>
                        <input 
                          v-model.number="item.quantity" 
                          type="number" 
                          min="1"
                          class="w-full px-2 py-1.5 text-sm border border-gray-300 rounded focus:ring-2 focus:ring-orange-500 focus:border-transparent"
                        >
                      </div>

                      <!-- Price is now editable after auto-population -->
                      <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1">Prix unitaire (FCFA)</label>
                        <input 
                          v-model.number="item.price" 
                          type="number" 
                          min="0"
                          class="w-full px-2 py-1.5 text-sm border border-gray-300 rounded focus:ring-2 focus:ring-orange-500 focus:border-transparent"
                        >
                      </div>
                    </div>

                    <div class="pt-2 border-t border-gray-300">
                      <div class="flex justify-between items-center">
                        <span class="text-xs font-medium text-gray-600">Total:</span>
                        <span class="text-sm font-bold text-gray-900">{{ formatCurrency(item.quantity * item.price) }}</span>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <!-- TVA -->
            <div class="border-t border-gray-200 pt-6 mt-6">
              <label class="block text-sm font-medium text-gray-700 mb-2">TVA (%)</label>
              <input 
                v-model.number="invoice.taxRate" 
                type="number" 
                min="0"
                max="100"
                step="0.01"
                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-transparent"
                placeholder="18"
              >
            </div>

            <!-- Notes -->
            <div class="border-t border-gray-200 pt-6 mt-6">
              <label class="block text-sm font-medium text-gray-700 mb-2">Notes / Conditions</label>
              <textarea 
                v-model="invoice.notes" 
                rows="3"
                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-transparent text-sm"
                placeholder="Conditions de paiement, notes supplémentaires..."
              ></textarea>
            </div>
          </div>
        </div>

        <!-- Aperçu de la facture (2/3) -->
        <div class="lg:col-span-2">
          <div class="bg-white shadow-2xl rounded-lg border border-gray-100 p-8 sm:p-12" id="invoice-preview">
            <!-- En-tête de la facture -->
            <div class="flex justify-between items-start mb-12 pb-8 border-b-2 border-gray-200">
              <div>
                <div class="flex items-center mb-4">
                  <div class="w-12 h-12 bg-gradient-to-br from-orange-500 to-orange-600 rounded-lg flex items-center justify-center mr-4">
                    <FileText class="w-6 h-6 text-white" />
                  </div>
                  <div>
                    <h2 class="text-3xl font-bold text-gray-900">FACTURE</h2>
                    <p class="text-sm text-gray-500">AliAdjamé Marketplace</p>
                  </div>
                </div>
                <div class="text-sm text-gray-600 space-y-1">
                  <p class="font-medium">Daq Auto</p>
                  <p>Abidjan, Côte d'Ivoire</p>
                  <p>📞 +225 01 53 67 60 62</p>
                  <p>✉️ commandes@daqauto.com</p>
                </div>
              </div>

              <div class="text-right">
                <div class="inline-block px-4 py-2 bg-gradient-to-r from-orange-500 to-orange-600 rounded-lg mb-4">
                  <p class="text-white font-bold text-lg">{{ invoice.number || 'FAC-XXXX-XXX' }}</p>
                </div>
                <div class="text-sm text-gray-600 space-y-1">
                  <p><span class="font-medium">Date:</span> {{ formatDate(invoice.date) }}</p>
                  <p><span class="font-medium">Échéance:</span> {{ formatDate(invoice.dueDate) }}</p>
                </div>
              </div>
            </div>

            <!-- Informations client -->
            <div class="mb-12">
              <div class="bg-gradient-to-r from-orange-50 to-orange-100 rounded-lg p-6 border border-orange-200">
                <h3 class="text-sm font-bold text-orange-800 mb-3 flex items-center">
                  <User class="w-4 h-4 mr-2" />
                  FACTURÉ À
                </h3>
                <div class="text-gray-900 space-y-1">
                  <p class="font-bold text-lg">{{ invoice.client.name || 'Nom du client' }}</p>
                  <p class="text-sm">{{ invoice.client.email || 'email@exemple.com' }}</p>
                  <p class="text-sm">{{ invoice.client.phone || '+225 XX XX XX XX XX' }}</p>
                  <p class="text-sm">{{ invoice.client.address || 'Adresse du client' }}</p>
                </div>
              </div>
            </div>

            <!-- Tableau des articles -->
            <div class="mb-12">
              <table class="w-full">
                <thead>
                  <tr class="bg-gray-100 border-b-2 border-gray-300">
                    <th class="text-left py-3 px-4 text-sm font-bold text-gray-700">DESCRIPTION</th>
                    <th class="text-center py-3 px-4 text-sm font-bold text-gray-700">QTÉ</th>
                    <th class="text-right py-3 px-4 text-sm font-bold text-gray-700">PRIX UNIT.</th>
                    <th class="text-right py-3 px-4 text-sm font-bold text-gray-700">TOTAL</th>
                  </tr>
                </thead>
                <tbody>
                  <!-- Display product name from selected product -->
                  <tr 
                    v-for="(item, index) in invoice.items" 
                    :key="index"
                    class="border-b border-gray-200 hover:bg-gray-50 transition-colors"
                  >
                    <td class="py-4 px-4 text-sm text-gray-900">{{ getProductName(item.productId) || 'Article sans nom' }}</td>
                    <td class="py-4 px-4 text-sm text-gray-700 text-center">{{ item.quantity }}</td>
                    <td class="py-4 px-4 text-sm text-gray-700 text-right">{{ formatCurrency(item.price) }}</td>
                    <td class="py-4 px-4 text-sm font-semibold text-gray-900 text-right">{{ formatCurrency(item.quantity * item.price) }}</td>
                  </tr>
                  <tr v-if="invoice.items.length === 0">
                    <td colspan="4" class="py-8 text-center text-gray-400 text-sm italic">
                      Aucun article ajouté
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>

            <!-- Totaux -->
            <div class="flex justify-end mb-12">
              <div class="w-full max-w-sm space-y-3">
                <div class="flex justify-between items-center py-2 border-b border-gray-200">
                  <span class="text-sm font-medium text-gray-600">Sous-total:</span>
                  <span class="text-sm font-semibold text-gray-900">{{ formatCurrency(subtotal) }}</span>
                </div>
                <div class="flex justify-between items-center py-2 border-b border-gray-200">
                  <span class="text-sm font-medium text-gray-600">TVA ({{ invoice.taxRate }}%):</span>
                  <span class="text-sm font-semibold text-gray-900">{{ formatCurrency(tax) }}</span>
                </div>
                <div class="flex justify-between items-center py-4 bg-gradient-to-r from-orange-500 to-orange-600 rounded-lg px-4">
                  <span class="text-base font-bold text-white">TOTAL:</span>
                  <span class="text-2xl font-bold text-white">{{ formatCurrency(total) }}</span>
                </div>
              </div>
            </div>

            <!-- Notes -->
            <div v-if="invoice.notes" class="border-t-2 border-gray-200 pt-8">
              <h3 class="text-sm font-bold text-gray-700 mb-3">NOTES / CONDITIONS</h3>
              <p class="text-sm text-gray-600 whitespace-pre-line">{{ invoice.notes }}</p>
            </div>

            <!-- Pied de page -->
            <div class="mt-12 pt-8 border-t border-gray-200 text-center">
              <p class="text-xs text-gray-500">
                Merci pour votre confiance ! Pour toute question, contactez-nous à commandes@daqauto.com
              </p>
              <p class="text-xs text-gray-400 mt-2">
                AliAdjamé Marketplace - Votre partenaire de confiance
              </p>
              <p class="text-xs text-orange-500 font-medium mt-3">
                Facture générée sur DaqAuto.com
              </p>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { 
  Home, FileText, User, ShoppingCart, Download, RefreshCw, 
  Plus, X, Calendar, Phone, Mail, MapPin 
} from 'lucide-vue-next'
import jsPDF from 'jspdf'
import { productsApi } from '../../services/api'

const products = ref([])
const loadingProducts = ref(false)

const currentUser = ref(null)
const currentBoutique = ref(null)

// État de la facture
const invoice = ref({
  number: 'FAC-2025-001',
  date: new Date().toISOString().split('T')[0],
  dueDate: new Date(Date.now() + 30 * 24 * 60 * 60 * 1000).toISOString().split('T')[0],
  taxRate: 18,
  client: {
    name: '',
    email: '',
    phone: '',
    address: ''
  },
  items: [
    {
      productId: '',
      quantity: 1,
      price: 0
    }
  ],
  notes: 'Paiement à effectuer dans les 30 jours.\nMerci de mentionner le numéro de facture lors du paiement.'
})

const initializeUserData = () => {
  try {
    const authToken = localStorage.getItem('authToken') || sessionStorage.getItem('authToken')
    
    if (!authToken) {
      console.error('[v0] Token d\'authentification manquant')
      return false
    }

    const userData = localStorage.getItem('user') || sessionStorage.getItem('user')
    
    if (!userData) {
      console.error('[v0] Données utilisateur manquantes')
      return false
    }

    const user = JSON.parse(userData)
    
    if (!user.id || !user.email) {
      console.error('[v0] Données utilisateur invalides')
      return false
    }

    currentUser.value = {
      id: user.id,
      full_name: user.full_name,
      email: user.email,
      boutiques: user.boutiques || []
    }

    if (user.boutiques && user.boutiques.length > 0) {
      const savedBoutiqueId = localStorage.getItem('selectedBoutiqueId')
      if (savedBoutiqueId) {
        const savedBoutique = user.boutiques.find(b => b.id == savedBoutiqueId)
        if (savedBoutique) {
          currentBoutique.value = savedBoutique
        } else {
          currentBoutique.value = user.boutiques[0]
        }
      } else {
        currentBoutique.value = user.boutiques[0]
      }
    } else {
      console.error('[v0] Aucune boutique associée')
      return false
    }

    console.log('[v0] Utilisateur initialisé:', {
      user: currentUser.value.full_name,
      boutique: currentBoutique.value.name
    })

    return true
  } catch (err) {
    console.error('[v0] Erreur lors de l\'initialisation:', err)
    return false
  }
}

onMounted(async () => {
  const initialized = initializeUserData()
  if (initialized) {
    await fetchProducts()
  } else {
    console.error('[v0] Impossible de charger les produits sans authentification')
  }
})

const fetchProducts = async () => {
  if (!currentBoutique.value?.id || !currentUser.value?.id) {
    console.error('[v0] Informations utilisateur manquantes pour charger les produits')
    return
  }

  try {
    loadingProducts.value = true
    
    const response = await productsApi.getProducts({
      boutique_id: currentBoutique.value.id,
      user_id: currentUser.value.id,
      page: 1,
      limit: 1000
    })
    
    if (response.success) {
      products.value = response.data || []
      console.log('[v0] Produits chargés:', products.value.length, 'produits')
    } else {
      console.error('[v0] Erreur API:', response.error)
    }
  } catch (err) {
    console.error('[v0] Erreur fetchProducts:', err)
  } finally {
    loadingProducts.value = false
  }
}

const onProductSelect = (index) => {
  const item = invoice.value.items[index]
  const selectedProduct = products.value.find(p => p.id === item.productId)
  
  if (selectedProduct) {
    item.price = selectedProduct.unit_price || selectedProduct.price || 0
    console.log('[v0] Produit sélectionné:', selectedProduct.name, 'Prix:', item.price)
  }
}

const getProductName = (productId) => {
  if (!productId) return ''
  const product = products.value.find(p => p.id === productId)
  return product ? product.name : ''
}

// Calculs
const subtotal = computed(() => {
  return invoice.value.items.reduce((sum, item) => sum + (item.quantity * item.price), 0)
})

const tax = computed(() => {
  return subtotal.value * (invoice.value.taxRate / 100)
})

const total = computed(() => {
  return subtotal.value + tax.value
})

// Méthodes
const formatCurrency = (amount) => {
  return new Intl.NumberFormat('fr-FR', {
    style: 'currency',
    currency: 'XOF',
    minimumFractionDigits: 0,
    maximumFractionDigits: 0
  }).format(amount || 0)
}

const formatDate = (dateString) => {
  if (!dateString) return 'JJ/MM/AAAA'
  const date = new Date(dateString)
  return new Intl.DateTimeFormat('fr-FR', {
    day: '2-digit',
    month: '2-digit',
    year: 'numeric'
  }).format(date)
}

const addItem = () => {
  invoice.value.items.push({
    productId: '',
    quantity: 1,
    price: 0
  })
}

const removeItem = (index) => {
  if (invoice.value.items.length > 1) {
    invoice.value.items.splice(index, 1)
  }
}

const resetForm = () => {
  invoice.value = {
    number: 'FAC-2025-' + String(Math.floor(Math.random() * 1000)).padStart(3, '0'),
    date: new Date().toISOString().split('T')[0],
    dueDate: new Date(Date.now() + 30 * 24 * 60 * 60 * 1000).toISOString().split('T')[0],
    taxRate: 18,
    client: {
      name: '',
      email: '',
      phone: '',
      address: ''
    },
    items: [
      {
        productId: '',
        quantity: 1,
        price: 0
      }
    ],
    notes: 'Paiement à effectuer dans les 30 jours.\nMerci de mentionner le numéro de facture lors du paiement.'
  }
}

const downloadPDF = () => {
  const doc = new jsPDF()
  
  // Configuration
  const pageWidth = doc.internal.pageSize.getWidth()
  const pageHeight = doc.internal.pageSize.getHeight()
  let yPos = 20
  
  // En-tête
  doc.setFillColor(249, 115, 22) // Orange
  doc.rect(0, 0, pageWidth, 40, 'F')
  
  doc.setTextColor(255, 255, 255)
  doc.setFontSize(24)
  doc.setFont('helvetica', 'bold')
  doc.text('FACTURE', 20, 25)
  
  doc.setFontSize(10)
  doc.setFont('helvetica', 'normal')
  doc.text('AliAdjamé Marketplace', 20, 32)
  
  // Numéro de facture
  doc.setFontSize(12)
  doc.setFont('helvetica', 'bold')
  doc.text(invoice.value.number || 'FAC-XXXX-XXX', pageWidth - 20, 25, { align: 'right' })
  
  yPos = 55
  
  // Informations entreprise
  doc.setTextColor(0, 0, 0)
  doc.setFontSize(10)
  doc.setFont('helvetica', 'bold')
  doc.text('Daq Auto', 20, yPos)
  doc.setFont('helvetica', 'normal')
  doc.text('Abidjan, Côte d\'Ivoire', 20, yPos + 5)
  doc.text('+225 01 53 67 60 62', 20, yPos + 10)
  doc.text('commandes@daqauto.com', 20, yPos + 15)
  
  // Dates
  doc.setFont('helvetica', 'bold')
  doc.text('Date:', pageWidth - 70, yPos, { align: 'left' })
  doc.setFont('helvetica', 'normal')
  doc.text(formatDate(invoice.value.date), pageWidth - 20, yPos, { align: 'right' })
  
  doc.setFont('helvetica', 'bold')
  doc.text('Échéance:', pageWidth - 70, yPos + 5, { align: 'left' })
  doc.setFont('helvetica', 'normal')
  doc.text(formatDate(invoice.value.dueDate), pageWidth - 20, yPos + 5, { align: 'right' })
  
  yPos += 30
  
  // Informations client
  doc.setFillColor(254, 243, 199) // Orange clair
  doc.rect(20, yPos, pageWidth - 40, 30, 'F')
  
  doc.setFontSize(9)
  doc.setFont('helvetica', 'bold')
  doc.setTextColor(194, 65, 12) // Orange foncé
  doc.text('FACTURÉ À', 25, yPos + 7)
  
  doc.setTextColor(0, 0, 0)
  doc.setFontSize(10)
  doc.text(invoice.value.client.name || 'Nom du client', 25, yPos + 13)
  doc.setFont('helvetica', 'normal')
  doc.setFontSize(9)
  doc.text(invoice.value.client.email || 'email@exemple.com', 25, yPos + 18)
  doc.text(invoice.value.client.phone || '+225 XX XX XX XX XX', 25, yPos + 23)
  
  yPos += 40
  
  // Tableau des articles
  doc.setFontSize(9)
  doc.setFont('helvetica', 'bold')
  doc.setFillColor(243, 244, 246) // Gris clair
  doc.rect(20, yPos, pageWidth - 40, 8, 'F')
  
  doc.text('DESCRIPTION', 25, yPos + 5)
  doc.text('QTÉ', pageWidth - 100, yPos + 5, { align: 'center' })
  doc.text('PRIX UNIT.', pageWidth - 70, yPos + 5, { align: 'right' })
  doc.text('TOTAL', pageWidth - 25, yPos + 5, { align: 'right' })
  
  yPos += 12
  
  // Articles - Use product name from products list
  doc.setFont('helvetica', 'normal')
  invoice.value.items.forEach((item) => {
    if (yPos > pageHeight - 60) {
      doc.addPage()
      yPos = 20
    }
    
    const productName = getProductName(item.productId) || 'Article sans nom'
    doc.text(productName, 25, yPos)
    doc.text(String(item.quantity), pageWidth - 100, yPos, { align: 'center' })
    doc.text(formatCurrency(item.price), pageWidth - 70, yPos, { align: 'right' })
    doc.text(formatCurrency(item.quantity * item.price), pageWidth - 25, yPos, { align: 'right' })
    
    yPos += 7
  })
  
  yPos += 10
  
  // Totaux
  doc.setFont('helvetica', 'normal')
  doc.text('Sous-total:', pageWidth - 80, yPos)
  doc.text(formatCurrency(subtotal.value), pageWidth - 25, yPos, { align: 'right' })
  
  yPos += 7
  doc.text(`TVA (${invoice.value.taxRate}%):`, pageWidth - 80, yPos)
  doc.text(formatCurrency(tax.value), pageWidth - 25, yPos, { align: 'right' })
  
  yPos += 10
  doc.setFillColor(249, 115, 22)
  doc.rect(pageWidth - 90, yPos - 5, 70, 10, 'F')
  
  doc.setTextColor(255, 255, 255)
  doc.setFont('helvetica', 'bold')
  doc.setFontSize(11)
  doc.text('TOTAL:', pageWidth - 85, yPos + 2)
  doc.text(formatCurrency(total.value), pageWidth - 25, yPos + 2, { align: 'right' })
  
  yPos += 20
  
  // Notes
  if (invoice.value.notes) {
    doc.setTextColor(0, 0, 0)
    doc.setFontSize(9)
    doc.setFont('helvetica', 'bold')
    doc.text('NOTES / CONDITIONS', 20, yPos)
    doc.setFont('helvetica', 'normal')
    
    const splitNotes = doc.splitTextToSize(invoice.value.notes, pageWidth - 40)
    doc.text(splitNotes, 20, yPos + 5)
    yPos += splitNotes.length * 5 + 10
  }
  
  // Pied de page
  doc.setFontSize(8)
  doc.setTextColor(107, 114, 128)
  doc.text('Merci pour votre confiance ! Pour toute question, contactez-nous à commandes@daqauto.com', pageWidth / 2, pageHeight - 20, { align: 'center' })
  doc.text('AliAdjamé Marketplace - Votre partenaire de confiance', pageWidth / 2, pageHeight - 15, { align: 'center' })
  
  doc.setTextColor(249, 115, 22)
  doc.setFont('helvetica', 'bold')
  doc.text('Facture générée sur DaqAuto.com', pageWidth / 2, pageHeight - 10, { align: 'center' })
  
  // Téléchargement
  doc.save(`Facture_${invoice.value.number || 'XXXX'}.pdf`)
}
</script>

<style scoped>
/* Animations luxueuses */
@keyframes float-slow {
  0%, 100% {
    transform: translateY(0px) translateX(0px) rotate(0deg);
  }
  25% {
    transform: translateY(-30px) translateX(15px) rotate(2deg);
  }
  50% {
    transform: translateY(-15px) translateX(-20px) rotate(-1deg);
  }
  75% {
    transform: translateY(-40px) translateX(8px) rotate(1deg);
  }
}

@keyframes float-reverse {
  0%, 100% {
    transform: translateY(0px) translateX(0px) rotate(0deg);
  }
  25% {
    transform: translateY(20px) translateX(-15px) rotate(-2deg);
  }
  50% {
    transform: translateY(35px) translateX(25px) rotate(1deg);
  }
  75% {
    transform: translateY(8px) translateX(-8px) rotate(-1deg);
  }
}

@keyframes float-diagonal {
  0%, 100% {
    transform: translateY(0px) translateX(0px) scale(1) rotate(0deg);
  }
  33% {
    transform: translateY(-25px) translateX(20px) scale(1.1) rotate(1deg);
  }
  66% {
    transform: translateY(15px) translateX(-15px) scale(0.9) rotate(-1deg);
  }
}

@keyframes float-slow-reverse {
  0%, 100% {
    transform: translateY(0px) translateX(0px) rotate(0deg);
  }
  25% {
    transform: translateY(-15px) translateX(-20px) rotate(-1deg);
  }
  50% {
    transform: translateY(25px) translateX(10px) rotate(2deg);
  }
  75% {
    transform: translateY(-10px) translateX(-5px) rotate(-0.5deg);
  }
}

@keyframes pulse-slow {
  0%, 100% {
    opacity: 0.4;
    transform: scale(1);
  }
  50% {
    opacity: 0.8;
    transform: scale(1.4);
  }
}

@keyframes pulse-delayed {
  0%, 100% {
    opacity: 0.3;
    transform: scale(1);
  }
  50% {
    opacity: 0.7;
    transform: scale(1.5);
  }
}

@keyframes slide-down {
  0% {
    transform: translateY(-100%);
    opacity: 0;
  }
  50% {
    opacity: 1;
  }
  100% {
    transform: translateY(100%);
    opacity: 0;
  }
}

@keyframes slide-right {
  0% {
    transform: translateX(-100%);
    opacity: 0;
  }
  50% {
    opacity: 1;
  }
  100% {
    transform: translateX(100%);
    opacity: 0;
  }
}

.animate-float-slow {
  animation: float-slow 20s ease-in-out infinite;
}

.animate-float-reverse {
  animation: float-reverse 18s ease-in-out infinite;
}

.animate-float-diagonal {
  animation: float-diagonal 22s ease-in-out infinite;
}

.animate-float-slow-reverse {
  animation: float-slow-reverse 24s ease-in-out infinite;
}

.animate-pulse-slow {
  animation: pulse-slow 4s ease-in-out infinite;
}

.animate-pulse-delayed {
  animation: pulse-delayed 5s ease-in-out infinite 1s;
}

.animate-slide-down {
  animation: slide-down 8s linear infinite;
}

.animate-slide-right {
  animation: slide-right 10s linear infinite;
}

/* Scrollbar personnalisée */
::-webkit-scrollbar {
  width: 8px;
}

::-webkit-scrollbar-track {
  background: #f1f1f1;
  border-radius: 10px;
}

::-webkit-scrollbar-thumb {
  background: linear-gradient(to bottom, #f97316, #ea580c);
  border-radius: 10px;
}

::-webkit-scrollbar-thumb:hover {
  background: linear-gradient(to bottom, #ea580c, #c2410c);
}
</style>