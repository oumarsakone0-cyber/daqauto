<template>
  <div class="product-detail-page">
    <!-- Loading state -->
    <div v-if="loading" class="loading-container">
      <div class="loading-spinner"></div>
      <p>Chargement du produit...</p>
    </div>
  
    <!-- Error state -->
    <div v-else-if="error" class="error-container">
      <div class="error-icon">⚠️</div>
      <h2>Erreur de chargement</h2>
      <p>{{ error }}</p>
      <button @click="loadProduct" class="retry-btn">Réessayer</button>
    </div>
  
    <!-- Product content -->
    <div v-else-if="product">
      <!-- Mobile Header -->
      <div class="mobile-header">
        <button class="back-btn" @click="$router.go(-1)">
          <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <polyline points="15,18 9,12 15,6"></polyline>
          </svg>
        </button>
        <div class="header-actions">
          <button class="header-action-btn" @click="openShareModal">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <path d="M4 12v8a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2v-8"></path>
              <polyline points="16 6 12 2 8 6"></polyline>
              <line x1="12" y1="2" x2="12" y2="15"></line>
            </svg>
          </button>
          <button class="header-action-btn" @click="toggleFavorite" :class="{ 'is-favorite': isFavorite }">
            <svg width="20" height="20" viewBox="0 0 24 24" :fill="isFavorite ? '#ff4d4f' : 'none'" stroke="currentColor" stroke-width="2">
              <path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"></path>
            </svg>
          </button>
        </div>
      </div>
  
      <!-- Desktop Header de navigation -->
      <div class="page-header desktop-only">
        <div class="container">
          <div class="breadcrumb">
            <router-link to="/" class="breadcrumb-link">Accueil</router-link>
            <span class="breadcrumb-separator">></span>
            <a href="#" class="breadcrumb-link">{{ product.category_name || 'Catégorie' }}</a>
            <span class="breadcrumb-separator">></span>
            <a href="#" class="breadcrumb-link">{{ product.subcategory_name || 'Sous-catégorie' }}</a>
            <span class="breadcrumb-separator">></span>
            <span class="breadcrumb-current">{{ product.name }}</span>
          </div>
        </div>
      </div>
  
      <!-- Desktop Section principale du produit -->
      <div class="main-product-section desktop-only">
        <div class="container">
          <div class="product-main-content">
            <!-- Galerie d'images -->
            <ProductImageGallery 
              :product="product"
              :product-images="productImages"
              :is-favorite="isFavorite"
              @open-image-modal="openImageModal"
              @toggle-favorite="toggleFavorite"
              @open-share-modal="openShareModal"
              @add-to-compare="addToCompare"
            />
  
            <!-- Informations du produit -->
            <ProductDetails 
              :product="product"
              :quantity="quantity"
              :selected-variants="selectedVariants"
              :product-colors="productColors"
              :product-sizes="productSizes"
              :selected-shipping="selectedShipping"
              :selected-commune="selectedCommune"
              :selected-ville="selectedVille"
              :tarifs-abidjan="tarifsAbidjan"
              :tarifs-interieur="tarifsInterieur"
              :current-shipping-cost="currentShippingCost"
              @add-variant="addVariant"
              @remove-variant="removeVariant"
              @update-variant-size="updateVariantSize"
              @update-variant-color="updateVariantColor"
              @update-variant-quantity="updateVariantQuantity"
              @select-shipping="selectShippingOption"
              @update-commune="updateCommune"
              @update-ville="updateVille"
              @decrease-quantity="decreaseQuantity"
              @increase-quantity="increaseQuantity"
              @update-quantity="updateQuantity"
              @contact-whats-app="contactWhatsApp"
              @open-chat="openChat"
              @start-order="startOrder"
            />
  
            <!-- Informations du fournisseur -->
            <SupplierCard 
              v-if="supplier"
              :supplier="supplier"
              @visit-store="visitStore"
              @contact-supplier="contactSupplier"
            />
          </div>
        </div>
      </div>
  
      <!-- Mobile Product Gallery -->
      <ProductImageGallery 
        class="mobile-only"
        :product="product"
        :product-images="productImages"
        :is-favorite="isFavorite"
        @open-image-modal="openImageModal"
        @toggle-favorite="toggleFavorite"
        @open-share-modal="openShareModal"
        @add-to-compare="addToCompare"
      />
  
      <!-- Mobile Product Info -->
      <MobileProductInfo 
        class="mobile-only"
        :product="product"
        :quantity="quantity"
        :selected-variants="selectedVariants"
        :product-colors="productColors"
        :product-sizes="productSizes"
        :selected-shipping="selectedShipping"
        :selected-commune="selectedCommune"
        :selected-ville="selectedVille"
        :tarifs-abidjan="tarifsAbidjan"
        :tarifs-interieur="tarifsInterieur"
        :supplier="supplier"
        :current-shipping-cost="currentShippingCost"
        @add-variant="addVariant"
        @remove-variant="removeVariant"
        @update-variant-size="updateVariantSize"
        @update-variant-color="updateVariantColor"
        @update-variant-quantity="updateVariantQuantity"
        @select-shipping="selectShippingOption"
        @update-commune="updateCommune"
        @update-ville="updateVille"
        @decrease-quantity="decreaseQuantity"
        @increase-quantity="increaseQuantity"
        @update-quantity="updateQuantity"
        @visit-store="visitStore"
        @contact-supplier="contactSupplier"
      />
  
      <!-- Desktop Section des détails du produit -->
      <div class="product-details-section desktop-only">
        <div class="container">
          <div class="two-column-layout">
            <!-- Colonne de gauche: Détails du produit -->
            <div class="left-column">
              <ProductDescriptionCard 
                :product="product"
                :product-images="productImages"
                :product-rating="productRating"
                @open-image-modal="openImageModal"
                @open-review-form="openReviewForm"
              />
            </div>
            
            <!-- Colonne de droite: Produits de la boutique -->
            <div class="right-column">
              <StoreProducts 
                :store-products="storeProducts"
                @view-store-product="viewStoreProduct"
              />
            </div>
          </div>
        </div>
      </div>
  
      <!-- Mobile Details Tabs -->
      <MobileDetailsTabs 
        class="mobile-only"
        :product="product"
        :product-rating="productRating"
        @open-review-form="openReviewForm"
      />
  
      <!-- Mobile Bottom Actions -->
      <div class="mobile-bottom-actions mobile-only">
        <button class="mobile-action-btn secondary" @click="contactWhatsApp">
          <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor">
            <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/>
          </svg>
          WhatsApp
        </button>
        <button class="mobile-action-btn secondary" @click="openChat">
          <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"></path>
          </svg>
          Chat
        </button>
        <button class="mobile-action-btn primary" @click="startOrder">
          <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <circle cx="9" cy="21" r="1"></circle>
            <circle cx="20" cy="21" r="1"></circle>
            <path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"></path>
          </svg>
          Commander
          <span class="mobile-action-price">{{ formatFCFA(calculateTotalWithShipping()) }}</span>
        </button>
      </div>
  
      <!-- Modals -->
      <ImageModal 
        v-if="showImageModal"
        :product-images="productImages"
        :current-index="modalImageIndex"
        :product-name="product.name"
        @close="closeImageModal"
        @prev="prevModalImage"
        @next="nextModalImage"
      />
      
      <ShareModal 
        v-if="showShareModal"
        :product="product"
        :share-link="shareLink"
        @close="closeShareModal"
        @share="shareOn"
        @copy-link="copyShareLink"
      />
  
      <OrderModal 
        v-if="showOrderModal"
        :product="product"
        :selected-variants="selectedVariants"
        :quantity="quantity"
        :current-image="currentImage"
        :selected-shipping="selectedShipping"
        :selected-commune="selectedCommune"
        :selected-ville="selectedVille"
        :tarifs-abidjan="tarifsAbidjan"
        :tarifs-interieur="tarifsInterieur"
        :current-shipping-cost="currentShippingCost"
        :order-form="orderForm"
        :form-submitted="formSubmitted"
        :order-loading="orderLoading"
        :can-confirm-order="canConfirmOrder"
        @close="closeOrderModal"
        @confirm-order="confirmOrder"
        @update-form="updateOrderForm"
      />
  
      <OrderSuccessModal 
        v-if="showOrderSuccessModal"
        :order-data="orderSuccessData"
        @close="closeOrderSuccessModal"
      />
  
      <ChatModal 
        v-if="showChatModal"
        :supplier="supplier"
        :chat-messages="chatMessages"
        :new-message="newMessage"
        @close="closeChatModal"
        @send-message="sendMessage"
        @update-message="updateNewMessage"
      />
      
      <!-- Notification Toast -->
      <NotificationToast 
        v-if="showNotification"
        :type="notificationType"
        :title="notificationTitle"
        :message="notificationMessage"
        @close="closeNotification"
      />
      
      <!-- Cart Badge -->
      <CartBadge 
        v-if="cartCount > 0"
        :count="cartCount"
        @click="viewCart"
      />
    </div>
  </div>
  </template>
  
  <script setup>
  import { ref, computed, onMounted, watch, onUnmounted } from 'vue'
  import { useRoute, useRouter } from 'vue-router'
  import { productsApi } from '../../services/api.js'
  
  // Import des composants
  import ProductImageGallery from '../product/ProductImageGallery.vue'
import ProductDetails from '../product/ProductDetails.vue'
import ProductDescriptionCard from '../product/ProductDescriptionCard.vue'
import StoreProducts from '../product/StoreProducts.vue'
import SupplierCard from '../product/SupplierCard.vue'
import MobileProductInfo from '../product/MobileProductInfo.vue'
import MobileDetailsTabs from '../product/MobileDetailsTabs.vue'
import ImageModal from '../product/modals/ImageModal.vue'
import ShareModal from '../product/modals/ShareModal.vue'
import OrderModal from '../product/modals/OrderModal.vue'
import OrderSuccessModal from '../product/modals/OrderSuccessModal.vue'
import ChatModal from '../product/modals/ChatModal.vue'
import NotificationToast from '../product/NotificationToast.vue'
import CartBadge from '../product/CartBadge.vue'
  
  // Router
  const route = useRoute()
  const router = useRouter()
  
  // États de chargement
  const loading = ref(true)
  const error = ref(null)
  const product = ref(null)
  const storeProducts = ref([])
  
  // États de l'interface
  const quantity = ref(1)
  const selectedVariants = ref([])
  const isFavorite = ref(false)
  const selectedShipping = ref('abidjan')
  const selectedCommune = ref('')
  const selectedVille = ref('')
  const currentShippingCost = ref(0)
  
  // États des modals
  const showImageModal = ref(false)
  const modalImageIndex = ref(0)
  const showShareModal = ref(false)
  const showOrderModal = ref(false)
  const showOrderSuccessModal = ref(false)
  const showChatModal = ref(false)
  const showNotification = ref(false)
  
  // États des notifications
  const notificationType = ref('success')
  const notificationTitle = ref('')
  const notificationMessage = ref('')
  
  // États du panier et commande
  const cartCount = ref(0)
  const formSubmitted = ref(false)
  const orderLoading = ref(false)
  const orderSuccessData = ref(null)
  
  // Données de livraison
  const tarifsAbidjan = ref([])
  const tarifsInterieur = ref([])
  
  // Formulaire de commande
  const orderForm = ref({
    customerName: '',
    customerContact: '',
    destination: '',
    commune: '',
    ville: '',
    adresse_complete: '',
    instructions_livraison: ''
  })
  
  // Chat
  const chatMessages = ref([])
  const newMessage = ref('')
  
  // Computed properties
  const productSlug = computed(() => route.params.slug)
  
  const productImages = computed(() => {
    if (!product.value) return []
  
    let images = []
  
    if (product.value.images && Array.isArray(product.value.images) && product.value.images.length > 0) {
      images = product.value.images.map(img => {
        if (typeof img === 'object' && img.image_url) {
          return img.image_url
        }
        return img
      })
    } else if (product.value.primary_image) {
      images = [product.value.primary_image]
    }
  
    if (images.length === 0) {
      images = ['https://via.placeholder.com/400x400?text=Produit']
    }
  
    return images.filter(img => img && img !== '')
  })
  
  const currentImage = computed(() => {
    const images = productImages.value
    if (!images || images.length === 0) {
      return 'https://via.placeholder.com/400x400?text=Produit'
    }
    return images[0]
  })
  
  const productColors = computed(() => {
    if (!product.value || !product.value.colors) return []
    return product.value.colors.map(color => ({
      name: color.name || 'Couleur inconnue',
      hex_value: color.hex_value || '#000000'
    }))
  })
  
  const productSizes = computed(() => {
    if (!product.value || !product.value.sizes) return []
    return product.value.sizes.map(size => ({ name: size }))
  })
  
  const productRating = computed(() => {
    if (!product.value) return 0
  
    const views = product.value.views_count || 0
    const sales = product.value.sales_count || 0
  
    const rating = 3 + Math.min(2, (views + sales * 10) / 1000)
    return Math.round(rating * 10) / 10
  })
  
  const supplier = computed(() => {
    if (!product.value) return null
  
    return {
      name: (product.value.boutique_name || 'Boutique Adjamé').toUpperCase(),
      logo: product.value.boutique_logo || 'https://images.unsplash.com/photo-1560472354-b33ff0c44a43?w=100&h=100&fit=crop&auto=format',
      market: (product.value.boutique_marche || 'Adjamé').toUpperCase(),
      business_type: (product.value.boutique_type || 'Commerce').toUpperCase(),
      experience: 5,
      premium: product.value.boutique_premium !== 'free',
      verify: product.value.boutique_verified !== 0,
      adresse: (product.value.boutique_address || 'Abidjan, Adjamé'),
      description: (product.value.boutique_description || ''),
      contact: (product.value.boutique_phone || '').toUpperCase()
    }
  })
  
  const shareLink = computed(() => {
    return `${window.location.origin}/produit/${productSlug.value}`
  })
  
  const canConfirmOrder = computed(() => {
    return orderForm.value.customerContact && 
           orderForm.value.adresse_complete && 
           !orderLoading.value
  })
  
  // Méthodes de chargement des données
  const loadProduct = async () => {
    try {
      loading.value = true
      error.value = null
      
      if (!productSlug.value) {
        throw new Error('Slug du produit manquant')
      }
      
      console.log('🔍 Chargement du produit avec slug:', productSlug.value)
      
      const response = await productsApi.getProduct(productSlug.value, 'slug')
      
      if (response.success && response.data) {
        product.value = response.data
        console.log('✅ Produit chargé:', product.value)
        await loadStoreProducts()
        await loadTarifs()
      } else {
        throw new Error('Produit non trouvé')
      }
    } catch (err) {
      console.error('❌ Erreur lors du chargement du produit:', err)
      error.value = err.message || 'Erreur lors du chargement du produit'
    } finally {
      loading.value = false
    }
  }
  
  const loadStoreProducts = async () => {
    try {
      if (!product.value || !product.value.boutique_id) return
      
      console.log('🏪 Chargement des produits de la boutique:', product.value.boutique_id)
      
      const response = await productsApi.getProducts({
        boutique_id: product.value.boutique_id,
        limit: 6,
        page: 1
      })
      
      if (response.success && response.data) {
        storeProducts.value = response.data.filter(p => p.id !== product.value.id)
        console.log('✅ Produits de la boutique chargés:', storeProducts.value.length)
      }
    } catch (err) {
      console.error('❌ Erreur lors du chargement des produits de la boutique:', err)
    }
  }
  
  const loadTarifs = async () => {
    try {
      console.log('🔄 Chargement des tarifs de livraison...')
      
      const responseAbidjan = await productsApi.getTarifsAbidjan()
      if (responseAbidjan.success) {
        tarifsAbidjan.value = responseAbidjan.data.filter(tarif => tarif.actif === true)
      }
  
      const responseInterieur = await productsApi.getTarifsInterieur()
      if (responseInterieur.success) {
        tarifsInterieur.value = responseInterieur.data.filter(tarif => tarif.actif === true)
      }
  
      console.log('✅ Tarifs chargés:', { 
        abidjan: tarifsAbidjan.value.length, 
        interieur: tarifsInterieur.value.length 
      })
      
      // Initialiser une commune par défaut si pas encore définie
      if (!selectedCommune.value && selectedShipping.value === 'abidjan' && tarifsAbidjan.value.length > 0) {
        selectedCommune.value = tarifsAbidjan.value[0].commune
        console.log('🏙️ Commune par défaut définie:', selectedCommune.value)
        updateShippingCost()
      }
      
    } catch (error) {
      console.error('❌ Erreur lors du chargement des tarifs:', error)
    }
  }
  
  // Méthodes de gestion des variantes
  const addVariant = () => {
    const newVariant = {
      id: Date.now(),
      sizeIndex: productSizes.value.length > 0 ? 0 : null,
      colorIndex: productColors.value.length > 0 ? 0 : null,
      quantity: 1
    }
    selectedVariants.value.push(newVariant)
  }
  
  const removeVariant = (index) => {
    selectedVariants.value.splice(index, 1)
  }
  
  const updateVariantSize = (variantIndex, sizeIndex) => {
    selectedVariants.value[variantIndex].sizeIndex = sizeIndex
  }
  
  const updateVariantColor = (variantIndex, colorIndex) => {
    selectedVariants.value[variantIndex].colorIndex = colorIndex
  }
  
  const updateVariantQuantity = (variantIndex, quantity) => {
    if (quantity >= 1 && quantity <= product.value.stock) {
      selectedVariants.value[variantIndex].quantity = quantity
      // Forcer le recalcul des frais de livraison
      updateShippingCost()
    }
  }
  
  // Méthodes de gestion de la quantité
  const increaseQuantity = () => {
    if (quantity.value < product.value.stock) {
      quantity.value++
      updateShippingCost()
    }
  }
  
  const decreaseQuantity = () => {
    if (quantity.value > 1) {
      quantity.value--
      updateShippingCost()
    }
  }
  
  const updateQuantity = (newQuantity) => {
    if (newQuantity >= 1 && newQuantity <= product.value.stock) {
      quantity.value = newQuantity
      updateShippingCost()
    }
  }
  
  // Méthodes de livraison
  const selectShippingOption = (option) => {
    selectedShipping.value = option
    selectedCommune.value = ''
    selectedVille.value = ''
    currentShippingCost.value = 0
    updateOrderFormDestination()
  }
  
  const updateCommune = (commune) => {
    selectedCommune.value = commune
    updateShippingCost()
  }
  
  const updateVille = (ville) => {
    selectedVille.value = ville
    updateShippingCost()
  }
  
  const updateShippingCost = () => {
    console.log('🔄 Mise à jour des frais de livraison...')
    console.log('Selected shipping:', selectedShipping.value)
    console.log('Selected commune:', selectedCommune.value)
    console.log('Selected ville:', selectedVille.value)
    console.log('Product tp/qtp:', product.value?.tp, product.value?.qtp)
    
    if (selectedShipping.value === 'retrait') {
      currentShippingCost.value = 0
      return
    }
  
    currentShippingCost.value = getShippingCost(selectedShipping.value)
    console.log('✅ Nouveaux frais calculés:', currentShippingCost.value)
    updateOrderFormDestination()
  }
  
  const updateOrderFormDestination = () => {
    if (selectedShipping.value === 'abidjan') {
      orderForm.value.destination = 'abidjan'
      orderForm.value.commune = selectedCommune.value
      orderForm.value.ville = 'Abidjan'
    } else if (selectedShipping.value === 'interieur') {
      orderForm.value.destination = 'interieur'
      orderForm.value.ville = selectedVille.value
      orderForm.value.commune = ''
    }
  }
  
  const getShippingCost = (type = selectedShipping.value) => {
    if (type === 'retrait') {
      return 0
    }
  
    // Calculer la quantité totale
    let totalQuantity = selectedVariants.value.length > 0
      ? selectedVariants.value.reduce((sum, variant) => sum + variant.quantity, 0)
      : quantity.value
  
    if (type === 'abidjan') {
      if (selectedCommune.value) {
        const tarif = tarifsAbidjan.value.find(t => t.commune === selectedCommune.value)
        if (tarif) {
          return calculateShippingCostWithProductRules(tarif, totalQuantity)
        }
      }
      // Tarif par défaut Abidjan
      const defaultTarif = { tarif_min: 1500, tarif_max: 3000 }
      return calculateShippingCostWithProductRules(defaultTarif, totalQuantity)
      
    } else if (type === 'interieur') {
      if (selectedVille.value) {
        const tarif = tarifsInterieur.value.find(t => t.ville === selectedVille.value)
        if (tarif) {
          return calculateShippingCostWithProductRules(tarif, totalQuantity)
        }
      }
      // Tarif par défaut intérieur
      const defaultTarif = { tarif_min: 3000, tarif_max: 6000 }
      return calculateShippingCostWithProductRules(defaultTarif, totalQuantity)
    }
  
    return 0
  }
  
  const calculateShippingCostWithProductRules = (tarif, totalQuantity) => {
    if (!product.value) return tarif.tarif_min || tarif.tarif || 0
  
    const tp = product.value.tp || 1  // Type de transport (1=moto, 2=mini-camion, 3=gros camion)
    const qtp = product.value.qtp || 1  // Quantité transportable au tarif normal
  
    // Déterminer le tarif de base selon le type de transport
    let baseTarif = 0
  
    if (tp === 1) {
      // Moto - tarif minimum
      baseTarif = tarif.tarif_min || tarif.tarif || 0
    } else if (tp === 2) {
      // Mini-camion - tarif maximum
      baseTarif = tarif.tarif_max || tarif.tarif || 0
    } else if (tp === 3) {
      // Gros camion - tarif maximum × 2
      baseTarif = (tarif.tarif_max || tarif.tarif || 0) * 2
    }
  
    // Calculer le coût selon la règle de quantité (qtp)
    let finalCost = 0
  
    if (qtp === 1) {
      // 1 article = 1 fois le tarif, 2 articles = 2 fois le tarif, etc.
      finalCost = baseTarif * totalQuantity
    } else if (qtp === 2) {
      // Tarif valide pour 1-2 articles, au-delà on divise par 2 et multiplie
      if (totalQuantity <= 2) {
        finalCost = baseTarif
      } else {
        const multiplier = Math.ceil(totalQuantity / 2)
        finalCost = baseTarif * multiplier
      }
    } else {
      // Pour qtp > 2, même logique : tarif valide pour 1 à qtp articles
      if (totalQuantity <= qtp) {
        finalCost = baseTarif
      } else {
        const multiplier = Math.ceil(totalQuantity / qtp)
        finalCost = baseTarif * multiplier
      }
    }
  
    return finalCost
  }
  
  // Méthodes de calcul
  const calculateTotal = () => {
    if (!product.value) return 0
  
    const unitPrice = getUnitPrice()
  
    if (selectedVariants.value.length > 0) {
      return selectedVariants.value.reduce((total, variant) => {
        return total + (unitPrice * variant.quantity)
      }, 0)
    }
  
    return unitPrice * quantity.value
  }
  
  const calculateTotalWithShipping = () => {
    return calculateTotal() + currentShippingCost.value
  }
  
  const getUnitPrice = () => {
    if (!product.value) return 0
  
    let price = product.value.unit_price
  
    let totalQuantity = selectedVariants.value.length > 0
      ? selectedVariants.value.reduce((sum, variant) => sum + variant.quantity, 0)
      : quantity.value
  
    if (product.value.wholesale_price && 
        product.value.wholesale_min_qty && 
        totalQuantity >= product.value.wholesale_min_qty) {
      price = product.value.wholesale_price
    }
  
    return price
  }
  
  // Méthodes des modals
  const openImageModal = (index) => {
    modalImageIndex.value = index
    showImageModal.value = true
    document.body.style.overflow = 'hidden'
  }
  
  const closeImageModal = () => {
    showImageModal.value = false
    document.body.style.overflow = 'auto'
  }
  
  const nextModalImage = () => {
    if (modalImageIndex.value < productImages.value.length - 1) {
      modalImageIndex.value++
    } else {
      modalImageIndex.value = 0
    }
  }
  
  const prevModalImage = () => {
    if (modalImageIndex.value > 0) {
      modalImageIndex.value--
    } else {
      modalImageIndex.value = productImages.value.length - 1
    }
  }
  
  const openShareModal = () => {
    showShareModal.value = true
  }
  
  const closeShareModal = () => {
    showShareModal.value = false
  }
  
  const shareOn = (platform) => {
    let shareUrl = ''
    const productUrl = shareLink.value
    const productTitle = product.value.name
  
    switch (platform) {
      case 'facebook':
        shareUrl = `https://www.facebook.com/sharer/sharer.php?u=${encodeURIComponent(productUrl)}`
        break
      case 'whatsapp':
        shareUrl = `https://wa.me/?text=${encodeURIComponent(productTitle + ' ' + productUrl)}`
        break
    }
  
    if (shareUrl) {
      window.open(shareUrl, '_blank')
    }
  
    closeShareModal()
    displayNotification('success', 'Partagé avec succès', `Produit partagé sur ${platform}.`)
  }
  
  const copyShareLink = () => {
    navigator.clipboard.writeText(shareLink.value).then(() => {
      displayNotification('success', 'Lien copié', 'Lien du produit copié dans le presse-papiers.')
    })
  }
  
  // Méthodes d'actions
  const toggleFavorite = () => {
    isFavorite.value = !isFavorite.value
    displayNotification(
      isFavorite.value ? 'success' : 'info',
      isFavorite.value ? 'Ajouté aux favoris' : 'Retiré des favoris',
      isFavorite.value ? 'Produit ajouté à vos favoris.' : 'Produit retiré de vos favoris.'
    )
  }
  
  const addToCompare = () => {
    displayNotification('success', 'Ajouté à la comparaison', 'Produit ajouté à la liste de comparaison.')
  }
  
  const contactWhatsApp = () => {
    let phoneNumber = supplier.value?.contact || ''
  
    phoneNumber = phoneNumber.replace(/\s+/g, '').replace(/-/g, '')
  
    if (!phoneNumber.startsWith('+225')) {
      phoneNumber = '+225' + phoneNumber
    }
  
    const productUrl = shareLink.value
    let message = `🛍️ *${product.value.name}*\n\n`
    message += `💰 *Prix unitaire:* ${formatFCFA(product.value.unit_price)}\n`
  
    if (product.value.wholesale_price && product.value.wholesale_min_qty) {
      message += `💰 *Prix de gros:* ${formatFCFA(product.value.wholesale_price)} (à partir de ${product.value.wholesale_min_qty} pièces)\n`
    }
  
    message += `🏪 *Boutique:* ${supplier.value.name}\n`
    message += `📍 *Marché:* ${supplier.value.market}\n\n`
    message += `🔗 *Lien du produit:* ${productUrl}\n\n`
    message += `Bonjour, je suis intéressé par ce produit. Pouvez-vous me donner plus d'informations ?`
  
    const whatsappUrl = `https://wa.me/${phoneNumber}?text=${encodeURIComponent(message)}`
    window.open(whatsappUrl, '_blank')
  }
  
  const openChat = () => {
    showChatModal.value = true
    if (chatMessages.value.length === 0) {
      chatMessages.value.push({
        id: 1,
        sender: 'bot',
        message: `Bonjour ! Je suis là pour vous aider concernant ${product.value.name}. Comment puis-je vous assister ?`,
        timestamp: new Date()
      })
    }
  }
  
  const closeChatModal = () => {
    showChatModal.value = false
  }
  
  const sendMessage = () => {
    if (!newMessage.value.trim()) return
  
    chatMessages.value.push({
      id: Date.now(),
      sender: 'user',
      message: newMessage.value,
      timestamp: new Date()
    })
  
    setTimeout(() => {
      chatMessages.value.push({
        id: Date.now() + 1,
        sender: 'bot',
        message: 'Merci pour votre message. Un de nos conseillers vous répondra dans les plus brefs délais.',
        timestamp: new Date()
      })
    }, 1000)
  
    newMessage.value = ''
  }
  
  const updateNewMessage = (message) => {
    newMessage.value = message
  }
  
  const startOrder = () => {
    console.log('🛒 [ProductDetailPage] Ouverture du modal de commande')
    console.log('Selected shipping:', selectedShipping.value)
    console.log('Selected commune:', selectedCommune.value)
    console.log('Selected ville:', selectedVille.value)
    console.log('Current shipping cost:', currentShippingCost.value)
    console.log('Tarifs Abidjan:', tarifsAbidjan.value)
    console.log('Tarifs Intérieur:', tarifsInterieur.value)
    console.log('Product tp/qtp:', product.value?.tp, product.value?.qtp)
    
    showOrderModal.value = true
    formSubmitted.value = false
  }
  
  const closeOrderModal = () => {
    showOrderModal.value = false
    formSubmitted.value = false
    orderLoading.value = false
  }
  
  const updateOrderForm = (field, value) => {
    orderForm.value[field] = value
  }
  
  const confirmOrder = async (orderDataFromModal) => {
  console.log("📦 ===== DÉBUT confirmOrder =====")
  console.log("📦 Données reçues du modal:", orderDataFromModal)

  formSubmitted.value = true

  // Validation des champs obligatoires
  if (!orderForm.value.customerContact || !orderForm.value.adresse_complete) {
    console.log("❌ Validation échouée - champs manquants")
    return
  }

  orderLoading.value = true

  try {
    // Si on reçoit des données du modal, les utiliser directement
    let finalOrderData

    if (orderDataFromModal && typeof orderDataFromModal === "object") {
      console.log("✅ Utilisation des données du modal")
      console.log("💰 frais_livraison reçu du modal:", orderDataFromModal.frais_livraison)
      console.log("💰 total reçu du modal:", orderDataFromModal.total)

      finalOrderData = orderDataFromModal
    } else {
      console.log("⚠️ Pas de données du modal, reconstruction manuelle")

      // Fallback : reconstruction manuelle (comme avant)
      finalOrderData = {
        numero_commande: `CMD-${Date.now()}-${Math.random().toString(36).substr(2, 5).toUpperCase()}`,
        produit_id: product.value.id,
        produit_nom: product.value.name,
        produit_prix: getUnitPrice(),
        quantite:
          selectedVariants.value.length > 0
            ? selectedVariants.value.reduce((sum, variant) => sum + variant.quantity, 0)
            : quantity.value,
        client_nom: orderForm.value.customerName || "Client",
        client_telephone: orderForm.value.customerContact,
        type_livraison: selectedShipping.value,
        commune: selectedCommune.value,
        ville: selectedVille.value || "Abidjan",
        adresse_complete: orderForm.value.adresse_complete,
        instructions_livraison: orderForm.value.instructions_livraison,
        sous_total: calculateTotal(),
        frais_livraison: currentShippingCost.value,
        total: calculateTotal() + currentShippingCost.value,
        options_variantes: selectedVariants.value.length > 0 ? getSelectedVariants() : null,
        boutique_id: product.value.boutique_id,
        boutique_nom: supplier.value.name,
        statut: "en_attente",
      }

      console.log("💰 frais_livraison calculé manuellement:", finalOrderData.frais_livraison)
    }

    console.log("📤 Données finales à envoyer:", finalOrderData)
    console.log("🔍 Vérification finale:")
    console.log("- frais_livraison:", finalOrderData.frais_livraison)
    console.log("- total:", finalOrderData.total)

    // Envoi à l'API
    const result = await productsApi.createOrder(finalOrderData)

    if (result.success) {
      console.log("✅ Commande créée avec succès:", result)
      orderLoading.value = false
      closeOrderModal()
      showOrderSuccessNotification(finalOrderData)
    } else {
      throw new Error(result.error || result.message || "Erreur lors de la création de la commande")
    }
  } catch (error) {
    orderLoading.value = false
    console.error("❌ Erreur lors de la confirmation:", error)
    displayNotification("error", "Erreur", error.message)
  }

  console.log("📦 ===== FIN confirmOrder =====")
}

  
  const getSelectedVariants = () => {
    return selectedVariants.value.map(variant => {
      const result = {
        quantite: variant.quantity
      }
      
      if (variant.sizeIndex !== null && productSizes.value[variant.sizeIndex]) {
        result.taille = productSizes.value[variant.sizeIndex].name.name || productSizes.value[variant.sizeIndex].name
      }
      
      if (variant.colorIndex !== null && productColors.value[variant.colorIndex]) {
        result.couleur = {
          nom: productColors.value[variant.colorIndex].name,
          hex: productColors.value[variant.colorIndex].hex_value
        }
      }
      
      return result
    })
  }
  
  const showOrderSuccessNotification = (orderData) => {
    orderSuccessData.value = orderData
    showOrderSuccessModal.value = true
  }
  
  const closeOrderSuccessModal = () => {
    showOrderSuccessModal.value = false
    orderSuccessData.value = null
  }
  
  const visitStore = () => {
    if (product.value && product.value.boutique_id) {
      router.push(`/boutique_resultat/${product.value.boutique_id}`)
    } else {
      displayNotification('info', 'Visite de la boutique', `Redirection vers la boutique ${supplier.value.name}.`)
    }
  }
  
  const contactSupplier = () => {
    displayNotification('info', 'Formulaire de contact', 'Le formulaire de contact du fournisseur va s\'ouvrir.')
  }
  
  const viewStoreProduct = (storeProduct) => {
    router.push(`/detail_resultat_produit/${storeProduct.slug || storeProduct.id}`)
    scrollToTop()
  }
  
  const viewCart = () => {
    displayNotification('info', 'Voir le panier', 'Redirection vers le panier.')
  }
  
  const openReviewForm = () => {
    displayNotification('info', 'Écrire un avis', 'Le formulaire d\'avis va s\'ouvrir.')
  }
  
  // Méthodes utilitaires
  const displayNotification = (type, title, message) => {
    notificationType.value = type
    notificationTitle.value = title
    notificationMessage.value = message
    showNotification.value = true
  
    setTimeout(() => {
      closeNotification()
    }, 5000)
  }
  
  const closeNotification = () => {
    showNotification.value = false
  }
  
  const formatFCFA = (montant) => {
   return Number(montant).toLocaleString('en-US', { 
    style: 'currency', 
    currency: 'USD',
    minimumFractionDigits: 2 
  })
  }
  
  const scrollToTop = () => {
    window.scrollTo({
      top: 0,
      behavior: 'smooth'
    })
  }
  
  // Watchers
  watch(productSlug, (newSlug) => {
    if (newSlug) {
      loadProduct()
    }
  }, { immediate: true })
  
  // Nouveau watcher pour recalculer les frais quand les variantes changent
  watch(selectedVariants, () => {
    updateShippingCost()
  }, { deep: true })
  
  // Nouveau watcher pour recalculer les frais quand la quantité simple change
  watch(quantity, () => {
    updateShippingCost()
  })
  
  // Lifecycle hooks
  onMounted(async () => {
    await loadProduct()
    
    // Initialiser avec des valeurs par défaut si pas de tarifs chargés
    if (tarifsAbidjan.value.length === 0) {
      tarifsAbidjan.value = [
        { commune: 'Cocody', tarif_min: 1500, tarif_max: 3000, actif: true },
        { commune: 'Plateau', tarif_min: 1500, tarif_max: 3000, actif: true },
        { commune: 'Marcory', tarif_min: 2000, tarif_max: 4000, actif: true },
        { commune: 'Treichville', tarif_min: 2000, tarif_max: 4000, actif: true },
        { commune: 'Adjamé', tarif_min: 1500, tarif_max: 3000, actif: true },
        { commune: 'Attécoubé', tarif_min: 2500, tarif_max: 5000, actif: true },
        { commune: 'Yopougon', tarif_min: 3000, tarif_max: 6000, actif: true },
        { commune: 'Abobo', tarif_min: 3500, tarif_max: 7000, actif: true }
      ]
      console.log('📦 Tarifs Abidjan par défaut chargés')
    }
    
    if (tarifsInterieur.value.length === 0) {
      tarifsInterieur.value = [
        { ville: 'Bouaké', tarif_min: 5000, tarif_max: 10000, actif: true },
        { ville: 'Daloa', tarif_min: 6000, tarif_max: 12000, actif: true },
        { ville: 'Yamoussoukro', tarif_min: 4500, tarif_max: 9000, actif: true }
      ]
      console.log('🏘️ Tarifs Intérieur par défaut chargés')
    }
    
    // Initialiser une commune par défaut pour tester
    if (!selectedCommune.value && selectedShipping.value === 'abidjan') {
      selectedCommune.value = 'Cocody'
      console.log('🏙️ Commune par défaut: Cocody')
      updateShippingCost()
    }
    
    scrollToTop()
  })
  
  onUnmounted(() => {
    document.body.style.overflow = 'auto'
  })
  </script>
  
  <style scoped>
  .product-detail-page {
    background: #f5f5f5;
    min-height: 100vh;
    font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
    position: relative;
  }
  
  /* Responsive utilities */
  .desktop-only {
    display: block;
  }
  
  .mobile-only {
    display: none;
  }
  
  @media (max-width: 768px) {
    .desktop-only {
      display: none !important;
    }
  
    .mobile-only {
      display: block;
    }
  }
  
  /* Loading et Error states */
  .loading-container,
  .error-container {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    min-height: 50vh;
    padding: 40px;
    text-align: center;
  }
  
  .loading-spinner {
    width: 40px;
    height: 40px;
    border: 4px solid #f3f3f3;
    border-top: 4px solid #fe9700;
    border-radius: 50%;
    animation: spin 1s linear infinite;
    margin-bottom: 20px;
  }
  
  @keyframes spin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
  }
  
  .error-container {
    color: #ff4d4f;
  }
  
  .error-icon {
    font-size: 48px;
    margin-bottom: 16px;
  }
  
  .retry-btn {
    margin-top: 16px;
    padding: 10px 20px;
    background: #fe9700;
    color: white;
    border: none;
    border-radius: 6px;
    cursor: pointer;
    transition: background-color 0.3s ease;
  }
  
  .retry-btn:hover {
    background: #fe9700;
  }
  
  /* Mobile Header */
  .mobile-header {
    display: none;
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    height: 56px;
    background: #fff;
    border-bottom: 1px solid #e8e8e8;
    z-index: 100;
    align-items: center;
    justify-content: space-between;
    padding: 0 16px;
  }
  
  .back-btn {
    width: 40px;
    height: 40px;
    border: none;
    background: none;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 50%;
    transition: background-color 0.3s ease;
  }
  
  .back-btn:hover {
    background: #f5f5f5;
  }
  
  .header-actions {
    display: flex;
    gap: 8px;
  }
  
  .header-action-btn {
    width: 40px;
    height: 40px;
    border: none;
    background: none;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 50%;
    transition: all 0.3s ease;
  }
  
  .header-action-btn:hover {
    background: #f5f5f5;
  }
  
  .header-action-btn.is-favorite {
    color: #fe9700;
  }
  
  /* Desktop Header */
  .page-header {
    background: #fff;
    border-bottom: 1px solid #e8e8e8;
    padding: 12px 0;
  }
  
  .container {
    max-width: 1500px;
    margin: 0 auto;
    padding: 0 16px;
  }
  
  .breadcrumb {
    font-size: 13px;
    color: #666;
  }
  
  .breadcrumb-link {
    color: #fe9700;
    text-decoration: none;
    transition: color 0.3s ease;
  }
  
  .breadcrumb-link:hover {
    text-decoration: underline;
    color: #fe9700;
  }
  
  .breadcrumb-separator {
    margin: 0 8px;
    color: #ccc;
  }
  
  .breadcrumb-current {
    color: #333;
  }
  
  /* Main product section */
  .main-product-section {
    padding: 24px 0;
  }
  
  .product-main-content {
    display: grid;
    grid-template-columns: 1fr 1.5fr 0.8fr;
    gap: 24px;
    background: #fff;
    border-radius: 8px;
    padding: 24px;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
  }
  
  /* Product details section */
  .product-details-section {
    padding: 24px 0;
  }
  
  .two-column-layout {
    display: grid;
    grid-template-columns: 3fr 1fr;
    gap: 24px;
  }
  
  .left-column {
    background: #fff;
    border-radius: 8px;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
  }
  
  .right-column {
    display: flex;
    flex-direction: column;
    gap: 24px;
  }
  
  /* Mobile Bottom Actions */
  .mobile-bottom-actions {
    position: fixed;
    bottom: 0;
    left: 0;
    right: 0;
    background: #fff;
    border-top: 1px solid #e8e8e8;
    padding: 12px 16px;
    gap: 18px;
    z-index: 100;
    box-shadow: 0 -2px 10px rgba(0, 0, 0, 0.1);
    display: none; /* Masqué par défaut */
    height: 70px;
  }
  
  .mobile-action-btn {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    gap: 4px;
    padding: 8px 12px;
    border: none;
    border-radius: 8px;
    font-size: 12px;
    font-weight: 600;
    transition: all 0.3s ease;
    /* min-height: 56px; */
    height: 60px;
  }
  
  .mobile-action-btn.secondary {
    flex: 1;
    background: #f5f5f5;
    color: #666;
    border: 1px solid #e8e8e8;
  }
  
  .mobile-action-btn.primary {
    flex: 2;
    background: #fe9700;
    color: #fff;
    position: relative;
  }
  
  .mobile-action-btn:active {
    transform: scale(0.98);
  }
  
  .mobile-action-price {
    font-size: 11px;
    opacity: 0.9;
  }
  
  /* Responsive */
  @media (max-width: 1200px) {
    .product-main-content {
      grid-template-columns: 1fr 1.5fr;
    }
  
    .two-column-layout {
      grid-template-columns: 2fr 1fr;
    }
  }
  
  @media (max-width: 992px) {
    .product-main-content {
      grid-template-columns: 1fr;
    }
  
    .two-column-layout {
      grid-template-columns: 1fr;
    }
  
    .right-column {
      margin-top: 24px;
    }
  }
  
  @media (max-width: 768px) {
    .mobile-header {
      display: flex;
    }
  
    .mobile-bottom-actions {
      display: flex; /* Affiché seulement sur mobile */
    }
  
    /* Ajouter du padding bottom au contenu pour éviter que le bottom bar cache le contenu */
    .mobile-only:last-of-type {
      margin-bottom: -1px;
    }
  }
  </style>
  