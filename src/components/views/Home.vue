  <template>
  <div class="home-page">
    <!-- Header Mobile (affiché seulement sur mobile) -->
    <!--
    <header class="mobile-header">
      <div class="header-top">
        <div class="location-selector">
          <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/>
            <circle cx="12" cy="10" r="3"/>
          </svg>
          <span>Abidjan, CI</span>
          <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <polyline points="6,9 12,15 18,9"/>
          </svg>
        </div>
        <div class="header-actions">
          <button class="notification-btn">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"/>
              <path d="M13.73 21a2 2 0 0 1-3.46 0"/>
            </svg>
            <span class="notification-badge">3</span>
          </button>
          <button class="message-btn">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/>
            </svg>
          </button>
        </div>
      </div>
      
      <div class="search-container">
        <div class="search-bar">
          <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <circle cx="11" cy="11" r="8"/>
            <path d="M21 21l-4.35-4.35"/>
          </svg>
          <input type="text" placeholder="Rechercher des produits..." />
          <button class="camera-search">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <path d="M23 19a2 2 0 0 1-2 2H3a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h4l2-3h6l2 3h4a2 2 0 0 1 2 2z"/>
              <circle cx="12" cy="13" r="4"/>
            </svg>
          </button>
        </div>
      </div>
    </header>
  -->
    <!-- Header Mobile (si nécessaire) -->
    <!-- 
    <header class="mobile-header">
      // Votre header mobile ici
    </header>
    -->

    <!-- Contenu Principal -->
    <main class="home-main">
      <!-- Bannière Hero -->
      <section class="hero-section">
        <MarketingBanner />
      </section>

      <!-- Section des avantages compacte (Desktop uniquement) -->
      <section class="benefits-section desktop-only">
        <div class="section-content">
          <div class="benefits-grid">
            <div class="benefit-item" v-for="(benefit, index) in benefits" :key="index">
              <div class="benefit-icon">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" v-html="benefit.icon"></svg>
              </div>
              <div class="benefit-content">
                <h3 class="benefit-title">{{ benefit.title }}</h3>
                <p class="benefit-text">{{ benefit.description }}</p>
              </div>
            </div>
          </div>
        </div>
      </section>

      <!-- 1. Section Catégories -->
      <CategoriesSection />

      <!-- 2. Section Fournisseur Vedette 
      <FeaturedSupplierSection />
      -->

      <!-- 3. Section Deal du Jour -->
      <DealsSection />

      <!-- 4. Section Produits Recommandés -->
      <RecommendedProductsSection />

      <!-- Section newsletter compacte (Desktop uniquement) -->
      <section class="newsletter-section desktop-only degrade-fond">
        <div class="section-content">
          <div class="newsletter-container">
            <div class="newsletter-content">
              <h2>📧 Restez informé de nos offres</h2>
              <p>Inscrivez-vous à notre newsletter et recevez en exclusivité nos meilleures promotions</p>
              <div class="newsletter-form">
                <input type="email" placeholder="Votre adresse email" v-model="newsletterEmail" />
                <button @click="subscribeNewsletter">S'inscrire</button>
              </div>
            </div>
          </div>
        </div>
      </section>

      <!-- Services Mobile -->
      <section class="mobile-services mobile-only">
        <div class="services-grid-mobile">
          <div class="service-item-mobile" v-for="(service, index) in mobileServices" :key="index">
            <div class="service-icon-mobile" v-html="service.icon"></div>
            <div class="service-text-mobile">
              <h4>{{ service.title }}</h4>
              <p>{{ service.description }}</p>
            </div>
          </div>
        </div>
      </section>
    </main>
  
    <!-- Navigation Bottom Mobile -->
    <nav class="mobile-bottom-nav mobile-only">
      <div class="nav-item active">
        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
          <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V6l-3-4z"/>
          <polyline points="9,22 9,12 15,12 15,22"/>
        </svg>
        <span>Accueil</span>
      </div>
      <div class="nav-item">
        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
          <circle cx="11" cy="11" r="8"/>
          <path d="M21 21l-4.35-4.35"/>
        </svg>
        <span>Recherche</span>
      </div>
      <div class="nav-item">
        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
          <path d="M6 2L3 6v14a2 2 0 0 0-2 2H5a2 2 0 0 0-2-2V6l-3-4z"/>
          <line x1="3" y1="6" x2="21" y2="6"/>
          <path d="M16 10a4 4 0 0 1-8 0"/>
        </svg>
        <span>Panier</span>
        <span class="cart-badge">2</span>
      </div>
      <div class="nav-item">
        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
          <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/>
        </svg>
        <span>Messages</span>
      </div>
      <div class="nav-item">
        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
          <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/>
          <circle cx="12" cy="7" r="4"/>
        </svg>
        <span>Profil</span>
      </div>
    </nav>
  
    <!-- Bouton Flottant Mobile 
    <button 
      class="floating-chat-btn mobile-only"
      @click="openChatModal"
    >
      <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2">
        <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/>
      </svg>
    </button>

    <!-- Bouton Flottant Desktop 
    <button 
      class="floating-chat-btn-desktop desktop-only"
      @click="toggleDesktopChat"
    >
      <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2">
        <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/>
      </svg>
      <span class="chat-btn-text">Chat</span>
    </button>

    <!-- Modal mobile (existante) 
    <ChatModal
      v-if="isChatModalOpen"
      :supplier="currentSupplier"
      :chat-messages="chatMessages"
      @close="closeChatModal"
      @send-message="handleSendMessage"
    />

    <!-- Fenêtre de chat desktop 
    <ChatWindow
      :is-open="isDesktopChatOpen"
      :supplier="currentSupplier"
      :chat-messages="chatMessages"
      @close="closeDesktopChat"
      @send-message="handleSendMessage"
    />
    -->

  </div>
</template>
<script setup>
import { ref, onMounted, onBeforeUnmount, computed } from 'vue'
import { useRouter } from 'vue-router'
import { categoriesApi, productsApi } from '../../services/api.js'

// Composants UI
import MarketingBanner from '../bannieres/AccueilBanniere.vue'
import CategoriesSection from '../home/CategoriesSection.vue'
import DealsSection from '../home/DealsSection.vue'
// import FeaturedSupplierSection from '../home/FeaturedSupplierSection.vue'
import RecommendedProductsSection from '../home/RecommendedProductsSection.vue'

// Chat
import ChatModal from '../product/modals/ChatModal2.vue'
import ChatWindow from '../product/modals/ChatWindow.vue' // Composant desktop

// État du chat
const isChatModalOpen = ref(false)
const isDesktopChatOpen = ref(false)
const chatMessages = ref([
  {
    id: 1,
    message: "Bonjour ! Comment puis-je vous aider aujourd'hui ?",
    sender: 'bot',
    timestamp: new Date()
  }
])

const currentSupplier = ref({
  name: 'Support Client',
  logo: '/placeholder.svg?height=40&width=40'
})

// Fonctions pour gérer le chat
const openChatModal = () => {
  isChatModalOpen.value = true
}

const closeChatModal = () => {
  isChatModalOpen.value = false
}

const toggleDesktopChat = () => {
  isDesktopChatOpen.value = !isDesktopChatOpen.value
}

const closeDesktopChat = () => {
  isDesktopChatOpen.value = false
}

const handleSendMessage = (message) => {
  chatMessages.value.push(message)
  setTimeout(() => {
    const botResponse = {
      id: Date.now() + 1,
      message: "Merci pour votre message ! Un agent va vous répondre sous peu.",
      sender: 'bot',
      timestamp: new Date()
    }
    chatMessages.value.push(botResponse)
  }, 1000)
}
  // Router pour la navigation
  const router = useRouter()
  
  // États pour les catégories
  const categories = ref([])
  const isLoadingCategories = ref(true)
  const categoriesError = ref(null)
  
  // États pour le fournisseur vedette
  const featuredSupplier = ref(null)
  const isLoadingFeaturedSupplier = ref(true)
  const featuredSupplierError = ref(null)
  
  // États pour les produits recommandés
  const recommendedProducts = ref([])
  const isLoadingRecommendedProducts = ref(true)
  const recommendedProductsError = ref(null)
  
  // États pour la vidéo
  const isVideoPaused = ref(false)
  const isVideoMuted = ref(true)
  
  // Variables pour le carrousel
  const carouselViewport = ref(null)
  const currentSlide = ref(0)
  const slideWidth = ref(220)
  const visibleSlides = ref(6)
  
  // Avantages
  const benefits = ref([
  {
    title: 'Livraison Rapide',
    description: 'Livraison en 24/48h',
    icon: '<path d="M16 3h5v5M21 3l-7 7M4 20h5v-5M9 20l-7-7"></path>'
  },
  {
    title: 'Assistance garantie',
    description: 'Commandez en toute confiance',
    icon: '<path d="M3 12a9 9 0 1 0 9-9 9.75 9.75 0 0 0-6.74 2.74L3 8"></path><path d="M3 3v5h5"></path>'
  },
  {
    title: 'Paiement Sécurisé',
    description: 'Transactions 100% sécurisées',
    icon: '<path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path>'
  },
  {
    title: 'Support 24/7',
    description: 'Service client disponible',
    icon: '<path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"></path>'
  }
  ])
  
  // Compte à rebours
  const countdown = ref({
  hours: 12,
  minutes: 34,
  seconds: 56
  })
  
  // Produits en promotion (données statiques conservées avec slugs)
  const dealProducts = ref([
  {
    id: 1,
    name: 'Écouteurs sans fil Bluetooth 5.0 avec réduction de bruit active et étui de charge rapide',
    slug: 'ecouteurs-sans-fil-bluetooth-5-0-reduction-bruit',
    market: 'Guangzhou, Chine',
    shopName: 'TechWorld Electronics Co.',
    unitPrice: 19.99,
    originalPrice: 39.99,
    wholesalePrice: 12.50,
    minQuantity: 50,
    discount: 50,
    rating: 4.5,
    reviews: 2453,
    images: [
      'https://images.unsplash.com/photo-1606400082777-ef05f3c5d38c?w=300&h=300&fit=crop&auto=format',
      'https://images.unsplash.com/photo-1599669454699-248893623440?w=300&h=300&fit=crop&auto=format',
      'https://images.unsplash.com/photo-1608043152269-423dbba4e7e1?w=300&h=300&fit=crop&auto=format'
    ],
    currentImageIndex: 0,
    freeShipping: true
  },
  {
    id: 2,
    name: 'Montre connectée étanche IP68 avec moniteur de fréquence cardiaque et GPS intégré',
    slug: 'montre-connectee-etanche-ip68-gps',
    market: 'Shenzhen, Chine',
    shopName: 'SmartWatch Pro Ltd.',
    unitPrice: 29.99,
    originalPrice: 59.99,
    wholesalePrice: 18.90,
    minQuantity: 30,
    discount: 50,
    rating: 4.2,
    reviews: 1287,
    images: [
      'https://images.unsplash.com/photo-1523275335684-37898b6baf30?w=300&h=300&fit=crop&auto=format',
      'https://images.unsplash.com/photo-1434493789847-2f02dc6ca35d?w=300&h=300&fit=crop&auto=format',
      'https://images.unsplash.com/photo-1508685096489-7aacd43bd3b1?w=300&h=300&fit=crop&auto=format'
    ],
    currentImageIndex: 0,
    freeShipping: true
  },
  {
    id: 3,
    name: 'Chargeur sans fil 15W compatible iPhone Samsung avec indicateur LED',
    slug: 'chargeur-sans-fil-15w-iphone-samsung',
    market: 'Dongguan, Chine',
    shopName: 'PowerTech Solutions',
    unitPrice: 12.99,
    originalPrice: 25.99,
    wholesalePrice: 8.20,
    minQuantity: 100,
    discount: 50,
    rating: 4.7,
    reviews: 856,
    images: [
      'https://images.unsplash.com/photo-1609592806787-3d9c1b8e5e8e?w=300&h=300&fit=crop&auto=format',
      'https://images.unsplash.com/photo-1558618666-fcd25c85cd64?w=300&h=300&fit=crop&auto=format'
    ],
    currentImageIndex: 0,
    freeShipping: false
  },
  {
    id: 4,
    name: 'Caméra de surveillance WiFi 1080P avec vision nocturne infrarouge',
    slug: 'camera-surveillance-wifi-1080p-vision-nocturne',
    market: 'Hangzhou, Chine',
    shopName: 'SecureCam International',
    unitPrice: 34.99,
    originalPrice: 69.99,
    wholesalePrice: 22.40,
    minQuantity: 20,
    discount: 50,
    rating: 4.3,
    reviews: 1542,
    images: [
      'https://images.unsplash.com/photo-1558618666-fcd25c85cd64?w=300&h=300&fit=crop&auto=format',
      'https://images.unsplash.com/photo-1567593810070-7a3d471af022?w=300&h=300&fit=crop&auto=format'
    ],
    currentImageIndex: 0,
    freeShipping: true
  },
  {
    id: 5,
    name: 'Enceinte Bluetooth portable 20W avec basses profondes et résistance à l\'eau IPX7',
    slug: 'enceinte-bluetooth-portable-20w-ipx7',
    market: 'Xiamen, Chine',
    shopName: 'AudioMax Trading Co.',
    unitPrice: 24.99,
    originalPrice: 49.99,
    wholesalePrice: 16.80,
    minQuantity: 40,
    discount: 50,
    rating: 4.6,
    reviews: 2156,
    images: [
      'https://images.unsplash.com/photo-1608043152269-423dbba4e7e1?w=300&h=300&fit=crop&auto=format',
      'https://images.unsplash.com/photo-1545454675-3531b543be5d?w=300&h=300&fit=crop&auto=format'
    ],
    currentImageIndex: 0,
    freeShipping: true
  }
  ])
  
  // Newsletter
  const newsletterEmail = ref('')
  
  // Navigation rapide mobile
  const quickNavItems = ref([
    { icon: '🛍️', label: 'Super Deals' },
    { icon: '🏪', label: 'Boutiques' },
    { icon: '🚚', label: 'Livraison' },
    { icon: '💳', label: 'Paiement' },
    { icon: '🎁', label: 'Cadeaux' },
    { icon: '📱', label: 'Électronique' },
    { icon: '👕', label: 'Mode' },
    { icon: '🏠', label: 'Maison' }
  ])
  
  const mobileServices = ref([
    {
      icon: '🚚',
      title: 'Livraison Rapide',
      description: 'Livraison en 24h dans Abidjan'
    },
    {
      icon: '🔒',
      title: 'Paiement Sécurisé',
      description: 'Transactions 100% sécurisées'
    },
    {
      icon: '💬',
      title: 'Support 24/7',
      description: 'Assistance client disponible'
    },
    {
      icon: '↩️',
      title: 'Retours Gratuits',
      description: '30 jours pour changer d\'avis'
    }
  ])
  
  // Méthodes pour la navigation des images
  const nextImage = (productIndex) => {
  const product = dealProducts.value[productIndex]
  if (product.currentImageIndex < product.images.length - 1) {
    product.currentImageIndex++
  } else {
    product.currentImageIndex = 0
  }
  }
  
  const previousImage = (productIndex) => {
  const product = dealProducts.value[productIndex]
  if (product.currentImageIndex > 0) {
    product.currentImageIndex--
  } else {
    product.currentImageIndex = product.images.length - 1
  }
  }
  
  const setImage = (productIndex, imageIndex) => {
  dealProducts.value[productIndex].currentImageIndex = imageIndex
  }
  
  // Timer
  let countdownInterval = null
  
  const updateCountdown = () => {
  if (countdown.value.seconds > 0) {
    countdown.value.seconds--
  } else if (countdown.value.minutes > 0) {
    countdown.value.minutes--
    countdown.value.seconds = 59
  } else if (countdown.value.hours > 0) {
    countdown.value.hours--
    countdown.value.minutes = 59
    countdown.value.seconds = 59
  } else {
    countdown.value = { hours: 23, minutes: 59, seconds: 59 }
  }
  }
  
  // Fonction pour naviguer vers la page de détail du produit
  const navigateToProduct = (product) => {
  console.log('🔄 Navigation vers le produit:', product)
  
  // Générer un slug si le produit n'en a pas
  let slug = product.slug
  if (!slug && product.name) {
    slug = generateSlug(product.name)
  }
  
  // Fallback si pas de slug générable
  if (!slug) {
    slug = `produit-${product.id || Date.now()}`
  }
  
  console.log('🔗 Slug généré:', slug)
  
  // Navigation vers la page de détail
  router.push(`/detail_resultat_produit/${slug}`)
  }
  
  // Fonction pour générer un slug à partir du nom
  const generateSlug = (name) => {
  if (!name) return null
  
  return name
    .toLowerCase()
    .trim()
    .replace(/[àáâãäå]/g, 'a')
    .replace(/[èéêë]/g, 'e')
    .replace(/[ìíîï]/g, 'i')
    .replace(/[òóôõö]/g, 'o')
    .replace(/[ùúûü]/g, 'u')
    .replace(/[ç]/g, 'c')
    .replace(/[ñ]/g, 'n')
    .replace(/[^a-z0-9\s-]/g, '') // Supprimer les caractères spéciaux
    .replace(/\s+/g, '-') // Remplacer les espaces par des tirets
    .replace(/-+/g, '-') // Supprimer les tirets multiples
    .replace(/^-|-$/g, '') // Supprimer les tirets en début/fin
  }
  
  // Fonctions pour les actions des boutons
  const toggleFavorite = (product) => {
  console.log('❤️ Toggle favorite pour:', product.name)
  // Ici vous pouvez ajouter la logique pour gérer les favoris
  }
  
  const contactSupplier = (product) => {
  console.log('📞 Contact supplier pour:', product.name)
  // Ici vous pouvez ajouter la logique pour contacter le fournisseur
  }
  
  const chatWithSupplier = (product) => {
  console.log('💬 Chat avec supplier pour:', product.name)
  // Ici vous pouvez ajouter la logique pour le chat
  }
  
  // Fonction pour formater les prix en FCFA
  const formatPrice = (price, currency = 'XOF') => {
  if (!price) return '0 FCFA'

  const exchangeRates = {
    EUR: 655.957, // 1 EUR = 655.957 FCFA
    XOF: 1,       // Pas de conversion nécessaire
    USD: 600      // Exemple : 1 USD = 600 FCFA (ajuste selon les taux actuels)
  }

  const convertToFcfa = (amount, fromCurrency) => {
    const rate = exchangeRates[fromCurrency]
    if (!rate) throw new Error(`Taux de conversion manquant pour ${fromCurrency}`)
    return amount * rate
  }

  let priceInFcfa

  if (currency === 'XOF') {
    priceInFcfa = price
  } else {
    priceInFcfa = convertToFcfa(price, currency)
  }

  return new Intl.NumberFormat('fr-FR', {
    style: 'currency',
    currency: 'XOF',
    minimumFractionDigits: 0
  }).format(priceInFcfa)
}
  
  // Fonction pour obtenir la première image de produit du fournisseur
  const getFirstProductImage = () => {
  if (featuredSupplier.value?.products?.length > 0) {
    return featuredSupplier.value.products[0].primary_image || 'https://images.unsplash.com/photo-1441986300917-64674bd600d8?w=400&h=250&fit=crop&auto=format'
  }
  return 'https://images.unsplash.com/photo-1441986300917-64674bd600d8?w=400&h=250&fit=crop&auto=format'
  }
  
  // Fonctions de contrôle vidéo
  const toggleVideo = () => {
  const video = document.querySelector('.supplier-video')
  if (video) {
    if (video.paused) {
      video.play()
      isVideoPaused.value = false
    } else {
      video.pause()
      isVideoPaused.value = true
    }
  }
  }
  
  const toggleMute = () => {
  const video = document.querySelector('.supplier-video')
  if (video) {
    video.muted = !video.muted
    isVideoMuted.value = video.muted
  }
  }
  
  // Fonction pour charger les catégories depuis l'API
  const loadCategories = async () => {
  try {
    isLoadingCategories.value = true;
    categoriesError.value = null;
    
    console.log('🔄 Chargement des catégories depuis l\'API...');
    const response = await categoriesApi.getCategories();
    
    if (response.success && response.data) {
      categories.value = response.data.map(category => ({
        id: category.id,
        name: category.name,
        icon: category.icon || '📦',
        image: category.image,
        count: category.product_count || Math.floor(Math.random() * 10000) + 1000,
        subcategories: category.subcategories || []
      }));
      
      console.log('✅ Catégories chargées:', categories.value);
    } else {
      throw new Error(response.message || 'Erreur lors du chargement des catégories');
    }
  } catch (error) {
    console.error('❌ Erreur lors du chargement des catégories:', error);
    categoriesError.value = error.message;
    
    // Fallback avec des catégories par défaut en cas d'erreur
    categories.value = [
      {
        id: 1,
        name: 'Électronique',
        icon: '📱',
        image: 'https://images.unsplash.com/photo-1550009158-9ebf69173e03?w=200&h=200&fit=crop&auto=format',
        count: 15420,
        subcategories: []
      },
      {
        id: 2,
        name: 'Mode & Vêtements',
        icon: '👕',
        image: 'https://images.unsplash.com/photo-1483985988355-763728e1935b?w=200&h=200&fit=crop&auto=format',
        count: 8930,
        subcategories: []
      },
      {
        id: 3,
        name: 'Maison & Jardin',
        icon: '🏠',
        image: 'https://images.unsplash.com/photo-1586023492125-27b2c045efd7?w=200&h=200&fit=crop&auto=format',
        count: 6540,
        subcategories: []
      },
      {
        id: 4,
        name: 'Beauté & Santé',
        icon: '💄',
        image: 'https://images.unsplash.com/photo-1596462502278-27bfdc403348?w=200&h=200&fit=crop&auto=format',
        count: 4200,
        subcategories: []
      }
    ];
  } finally {
    isLoadingCategories.value = false;
  }
  };
  
  // Fonction pour charger un fournisseur vedette aléatoire
  const loadFeaturedSupplier = async () => {
  try {
    isLoadingFeaturedSupplier.value = true;
    featuredSupplierError.value = null;
    
    console.log('🔄 Chargement du fournisseur vedette...');
    const response = await productsApi.getRandomSupplier();
    
    if (response.success && response.data) {
      featuredSupplier.value = {
        id: response.data.boutique.id,
        name: response.data.boutique.name,
        category: response.data.boutique.category || 'Boutique générale',
        experience: response.data.boutique.experience || Math.floor(Math.random() * 10) + 1,
        products_count: response.data.boutique.products_count || 0,
        logo: response.data.boutique.logo,
        banner: response.data.boutique.banner,
        video_url: response.data.boutique.video_url, // Vidéo optionnelle
        products: (response.data.products || []).map(product => ({
          ...product,
          slug: product.slug || generateSlug(product.name) // Ajouter le slug
        }))
      };
      
      console.log('✅ Fournisseur vedette chargé:', featuredSupplier.value);
    } else {
      throw new Error(response.message || 'Erreur lors du chargement du fournisseur vedette');
    }
  } catch (error) {
    console.error('❌ Erreur lors du chargement du fournisseur vedette:', error);
    featuredSupplierError.value = error.message;
    
    // Fallback avec des données par défaut
    featuredSupplier.value = {
      id: 1,
      name: 'Boutique Exemple',
      category: 'Électronique & Gadgets',
      experience: 5,
      products_count: 150,
      logo: 'https://images.unsplash.com/photo-1560472354-b33ff0c44a43?w=60&h=60&fit=crop&auto=format',
      banner: 'https://images.unsplash.com/photo-1441986300917-64674bd600d8?w=400&h=250&fit=crop&auto=format',
      video_url: null,
      products: []
    };
  } finally {
    isLoadingFeaturedSupplier.value = false;
  }
  };
  
  // Fonction pour charger les produits recommandés (15 derniers avec plus de vues)
  const loadRecommendedProducts = async () => {
  try {
    isLoadingRecommendedProducts.value = true;
    recommendedProductsError.value = null;
    
    console.log('🔄 Chargement des produits recommandés...');
    const response = await productsApi.getMostViewedProductsForHomepage({ limit: 15 });
    
    if (response.success && response.data) {
      recommendedProducts.value = response.data.map(product => ({
        id: product.id,
        name: product.name,
        slug: product.slug || generateSlug(product.name), // Ajouter le slug
        unit_price: product.unit_price,
        wholesale_price: product.wholesale_price,
        wholesale_min_qty: product.wholesale_min_qty,
        primary_image: product.primary_image,
        views: product.views,
        market: product.boutique_market.toLowerCase(),
        boutique_name: product.boutique_name,
        rating: product.rating || (Math.random() * 2 + 3).toFixed(1), // Rating entre 3 et 5
        experience: product.experience || Math.floor(Math.random() * 8) + 2
      }));
      
      console.log('✅ Produits recommandés chargés:', recommendedProducts.value);
    } else {
      throw new Error(response.message || 'Erreur lors du chargement des produits recommandés');
    }
  } catch (error) {
    console.error('❌ Erreur lors du chargement des produits recommandés:', error);
    recommendedProductsError.value = error.message;
    
    // Fallback avec des produits par défaut
    recommendedProducts.value = [];
  } finally {
    isLoadingRecommendedProducts.value = false;
  }
  };
  
  const totalSlides = computed(() => categories.value.length)
  const maxSlide = computed(() => Math.max(0, totalSlides.value - visibleSlides.value))
  
  const canScrollLeft = computed(() => currentSlide.value > 0)
  const canScrollRight = computed(() => currentSlide.value < maxSlide.value)
  
  const scrollLeft = () => {
  if (canScrollLeft.value) {
    currentSlide.value = Math.max(0, currentSlide.value - 1)
  }
  }
  
  const scrollRight = () => {
  if (canScrollRight.value) {
    currentSlide.value = Math.min(maxSlide.value, currentSlide.value + 1)
  }
  }
  
  loadCategories()
  loadFeaturedSupplier()
  loadRecommendedProducts()
  
  onMounted(() => {
  countdownInterval = setInterval(updateCountdown, 1000)
  })
  
  onBeforeUnmount(() => {
  if (countdownInterval) {
    clearInterval(countdownInterval)
  }
  })

// Newsletter
// Avantages

</script>

<style scoped>

.home-page {
  width: 100%;
  background: #ffffff;
  overflow-x: hidden;
}

.home-main {
  width: 100%;
}

.hero-section {
  margin-bottom: 16px;
  margin-top: 16px;
}

.section-content {
  max-width: 1500px;
  margin: 0 auto;
  padding: 0 20px;
}

/* Section des avantages */
.benefits-section {
  padding: 30px 0;
  background: #f8f9fa;
}

.benefits-grid {
  display: grid;
  grid-template-columns: repeat(4, 1fr);
  gap: 30px;
}

.benefit-item {
  display: flex;
  align-items: center;
  gap: 16px;
  background: white;
  padding: 20px;
  border-radius: 12px;
  box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
}

.benefit-icon {
  width: 40px;
  height: 40px;
  border-radius: 50%;
  background: #fe7900;
  color: white;
  display: flex;
  align-items: center;
  justify-content: center;
  flex-shrink: 0;
}

.benefit-title {
  font-size: 16px;
  font-weight: 600;
  color: #333;
  margin: 0 0 4px 0;
}

.benefit-text {
  font-size: 14px;
  color: #666;
  margin: 0;
}

/* Section newsletter */
.newsletter-section {
  padding: 40px 0;
  background: linear-gradient(160deg, black , #fc4618 , black);
}

.newsletter-container {
  text-align: center;
  color: white;
}

.newsletter-content h2 {
  font-size: 28px;
  font-weight: 700;
  margin: 0 0 12px 0;
}

.newsletter-content p {
  font-size: 16px;
  opacity: 0.9;
  margin: 0 0 30px 0;
}

.newsletter-form {
  display: flex;
  max-width: 400px;
  margin: 0 auto;
  gap: 12px;
}

.newsletter-form input {
  flex: 1;
  padding: 12px 16px;
  border: none;
  border-radius: 25px;
  font-size: 14px;
  color: #333;
}

.newsletter-form button {
  padding: 12px 24px;
  background: #fe7900;
  color: white;
  border: none;
  border-radius: 25px;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.3s ease;
}

.newsletter-form button:hover {
  background: #fe4c00;
  transform: translateY(-2px);
}

/* Services Mobile */
.mobile-services {
  background: white;
  padding: 16px;
  margin-bottom: 8px;
}

.services-grid-mobile {
  display: grid;
  grid-template-columns: repeat(2, 1fr);
  gap: 16px;
}

.service-item-mobile {
  display: flex;
  align-items: center;
  gap: 12px;
  padding: 16px;
  background: #f8f9fa;
  border-radius: 12px;
}

.service-icon-mobile {
  width: 40px;
  height: 40px;
  background: linear-gradient(135deg, #ff6b35, #ff8c42);
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 20px;
  flex-shrink: 0;
}

.service-text-mobile h4 {
  font-size: 14px;
  font-weight: 600;
  color: #333;
  margin: 0 0 4px 0;
}

.service-text-mobile p {
  font-size: 12px;
  color: #666;
  margin: 0;
  line-height: 1.3;
}

/* Navigation Bottom Mobile */
.mobile-bottom-nav {
  position: fixed;
  bottom: 0;
  left: 0;
  right: 0;
  background: white;
  border-top: 1px solid #e0e0e0;
  display: flex;
  z-index: 1000;
}

.nav-item {
  flex: 1;
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 4px;
  padding: 8px 4px;
  color: #999;
  font-size: 10px;
  font-weight: 500;
  position: relative;
  transition: color 0.2s;
}

.nav-item.active {
  color: #ff6b35;
}

.nav-item svg {
  width: 24px;
  height: 24px;
}

.cart-badge {
  position: absolute;
  top: 4px;
  right: 20px;
  background: #ff4757;
  color: white;
  font-size: 10px;
  font-weight: bold;
  padding: 2px 6px;
  border-radius: 10px;
  min-width: 16px;
  text-align: center;
}

/* Bouton Flottant */
.floating-chat-btn {
  position: fixed;
  bottom: 100px;
  right: 16px;
  width: 56px;
  height: 56px;
  background: linear-gradient(135deg, #667eea, #764ba2);
  border: none;
  border-radius: 50%;
  color: white;
  display: flex;
  align-items: center;
  justify-content: center;
  box-shadow: 0 4px 20px rgba(102, 126, 234, 0.4);
  z-index: 999;
  transition: transform 0.2s;
}

.floating-chat-btn:active {
  transform: scale(0.95);
}

/* Styles pour le bouton de chat desktop */
.floating-chat-btn-desktop {
  position: fixed;
  bottom: 30px;
  right: 30px;
  background: linear-gradient(135deg, #667eea, #764ba2);
  border: none;
  border-radius: 50px;
  color: white;
  display: flex;
  align-items: center;
  gap: 12px;
  padding: 16px 24px;
  box-shadow: 0 4px 20px rgba(102, 126, 234, 0.4);
  z-index: 999;
  transition: all 0.3s ease;
  cursor: pointer;
  font-weight: 600;
  font-size: 14px;
}

.floating-chat-btn-desktop:hover {
  transform: translateY(-2px);
  box-shadow: 0 6px 25px rgba(102, 126, 234, 0.5);
}

.chat-btn-text {
  white-space: nowrap;
}

/* Styles mobiles cachés par défaut */
.mobile-only {
  display: none;
}

.desktop-only {
  display: block;
}

/* Responsive Mobile */
@media (max-width: 768px) {
  .mobile-only {
    display: flex;
  }
  
  .desktop-only {
    display: none;
  }
  
  .home-main {
    padding-bottom: 80px;
  }
  
  .section-content {
    padding: 0 16px;
  }
  
  .benefits-grid {
    grid-template-columns: repeat(2, 1fr);
    gap: 16px;
  }
  
  .benefit-item {
    padding: 16px;
    flex-direction: column;
    text-align: center;
    gap: 8px;
  }
  
  .benefit-icon {
    width: 35px;
    height: 35px;
  }
  
  .benefit-title {
    font-size: 14px;
  }
  
  .benefit-text {
    font-size: 12px;
  }
  
  .newsletter-content h2 {
    font-size: 22px;
  }
  
  .newsletter-form {
    flex-direction: column;
    max-width: 300px;
  }
  
  .newsletter-form input,
  .newsletter-form button {
    width: 100%;
  }
  
  .services-grid-mobile {
    grid-template-columns: 1fr;
  }
}

@media (max-width: 480px) {
  .services-grid-mobile {
    grid-template-columns: 1fr;
  }
}
</style>
