<template>
  <div class="product-detail-page">
    <!-- Loading state -->
    <div v-if="loading" class="loading-container">
      <div class="loading-spinner"></div>
      <p class="text-black">Loading produit details...</p>
    </div>
  
    <!-- Error state -->
    <div v-else-if="error" class="error-container">
      <div class="error-icon">⚠️</div>
      <h2>Error to load</h2>
      <p>{{ error }}</p>
      <button @click="loadProduct" class="retry-btn">Try again</button>
    </div>
  
    <!-- Product content -->
    <div v-else-if="product">
      <div class="page-header">
        <div class="container">
          <div class="breadcrumb">
            <router-link to="/" class="breadcrumb-link">Home</router-link>
            <span class="breadcrumb-separator">></span>
            <a href="#" class="breadcrumb-link">{{ product.category_name || 'Catégorie' }}</a>
            <span class="breadcrumb-separator">></span>
            <a href="#" class="breadcrumb-link">{{ product.subcategory_name || 'Sous-catégorie' }}</a>
            <span class="breadcrumb-separator">></span>
            <span class="breadcrumb-current">{{ product.name }}</span>
          </div>
        </div>
      </div>
  
      <div class="main-product-section ">
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
              @toggle-cart="toggleCart"
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
  
      <div class="product-details-section ">
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
  
      <!-- <ChatModal 
        v-if="showChatModal"
        :supplier="supplier"
        :chat-messages="chatMessages"
        :new-message="newMessage"
        @close="closeChatModal"
        @send-message="sendMessage"
        @update-message="updateNewMessage"
      /> -->
     
      
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
    <div v-else>
        <h1 class="text-black">no product</h1>
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
import ImageModal from '../product/modals/ImageModal.vue'
import ShareModal from '../product/modals/ShareModal.vue'
import OrderModal from '../product/modals/OrderModal.vue'
import OrderSuccessModal from '../product/modals/OrderSuccessModal.vue'
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
  const addedCart = ref(false)
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
      name: color.name || 'Unknown Color',
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
      error.value = err.message || 'Error to load product'
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
    displayNotification('success', 'share successfully', `Produit shared on ${platform}.`)
  }
  
  const copyShareLink = () => {
    navigator.clipboard.writeText(shareLink.value).then(() => {
      displayNotification('success', 'Link copied', 'Product link copied to clipboard.')
    })
  }
  
  // Méthodes d'actions
  const toggleFavorite = async () => {
  // On inverse l'état local
  isFavorite.value = !isFavorite.value;

  try {
    const userData = localStorage.getItem('user') || sessionStorage.getItem('user')
    const user = JSON.parse(userData)
    // Préparer les données pour l'API
    const likeData = { id_produit: product.value.id, user_id: user.id };

    // Appeler l'API pour ajouter ou retirer le like
    const result = await productsApi.addLike(likeData);

    if (result.success) {
      // Notification selon le nouvel état
      displayNotification(
        isFavorite.value ? 'success' : 'info',
        isFavorite.value ? 'Added to favorites' : 'Removed from favorites',
        isFavorite.value
          ? 'Product added to your favorites.'
          : 'Product removed from your favorites.'
      );
    } else {
      // Si erreur backend, on remet l'état à l'inverse
      isFavorite.value = !isFavorite.value;
      displayNotification('error', 'Error', result.error || 'Impossible to update favorites.');
    }
  } catch (error) {
    console.error('Erreur toggleFavorite:', error);
    // Annuler le changement local en cas d'erreur
    isFavorite.value = !isFavorite.value;
    displayNotification('error', 'Error', 'error to update.');
  }
};

const toggleCart = () => {
  // On inverse l'état local
  addedCart.value = !addedCart.value;
  try {
    const userData = localStorage.getItem('user') || sessionStorage.getItem('user')
    const user = JSON.parse(userData)
    // Préparer les données pour l'API
    const likeData = { id_produit: product.value.id, user_id: user.id };

    // Appeler l'API pour ajouter ou retirer le like
    // const result = await productsApi.addLike(likeData);

    // if (result.success) {
      // Notification selon le nouvel état
      displayNotification(
        addedCart.value ? 'success' : 'info',
        addedCart.value ? 'Added to Cart' : 'Removed from Cart',
        addedCart.value
          ? 'Product added to your Cart.'
          : 'Product removed from your Cart.'
      );
    // } else {
    //   // Si erreur backend, on remet l'état à l'inverse
    //   addedCart.value = !addedCart.value;
    //   displayNotification('error', 'Error', result.error || 'Impossible to update Cart.');
    // }
  } catch (error) {
    addedCart.value = !addedCart.value;
    displayNotification('error', 'Error', 'error to update.');
  }
};

  
  const addToCompare = () => {
    displayNotification('success', 'Added to benchmark', 'Produit addedon benchmark list.')
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
      displayNotification('info', 'Visit store', `Redirect to the store ${supplier.value.name}.`)
    }
  }
  
  const contactSupplier = () => {
    displayNotification('info', 'contact form', 'The contact form of supplier will open.')
  }
  
  const viewStoreProduct = (storeProduct) => {
    router.push(`/detail_resultat_produit/${storeProduct.slug || storeProduct.id}`)
    scrollToTop()
  }
  
  const viewCart = () => {
    displayNotification('info', 'See the cart', 'Redirect tothe cart.')
  }
  
  const openReviewForm = () => {
    displayNotification('info', 'Write a review', 'The review form will open.')
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
    padding-top: 40px;
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
  