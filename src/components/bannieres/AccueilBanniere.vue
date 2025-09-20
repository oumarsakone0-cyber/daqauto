<template>
  <div class="relative w-full bg-gradient-to-br overflow-hidden degrade-fond">
    <!-- Background Pattern -->
    <div class="absolute inset-0 opacity-10">
      <div class="absolute top-0 left-0 w-96 h-96 bg-white rounded-full -translate-x-48 -translate-y-48"></div>
      <div class="absolute bottom-0 right-0 w-96 h-96 bg-white rounded-full translate-x-48 translate-y-48"></div>
    </div>

    <div class="relative max-w-7xl mx-auto px-4 py-8 ">
      <div class="grid lg:grid-cols-3 gap-8">
        <!-- Section Gauche - Timer et Offre Principale -->
        <div class="text-white space-y-6">
          <div class="space-y-2">
            <div class="inline-flex items-center bg-yellow-400 text-black font-semibold px-3 py-1 rounded-full text-sm">
              <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v13m0-13V6a2 2 0 112 2h-2zm0 0V5.5A2.5 2.5 0 109.5 8H12zm-7 4h14M5 12a2 2 0 110-4h14a2 2 0 110 4M5 12v7a2 2 0 002 2h10a2 2 0 002-2v-7"></path>
              </svg>
              Vente Flash
            </div>
            <h1 class="text-4xl lg:text-5xl font-bold leading-tight">
              Jusqu'à <span class="text-yellow-400">70%</span> de réduction
            </h1>
            <p class="text-lg text-blue-100">Offres limitées sur une sélection de produits premium</p>
          </div>

          <!-- Timer -->
          <div class="bg-white/10 backdrop-blur-sm rounded-2xl p-6 border border-white/20">
            <div class="flex items-center gap-2 mb-4">
              <svg class="w-5 h-5 text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <circle cx="12" cy="12" r="10"></circle>
                <polyline points="12,6 12,12 16,14"></polyline>
              </svg>
              <span class="text-sm font-medium">Offre expire dans :</span>
            </div>
            <div class="flex gap-3">
              <div class="text-center">
                <div class="bg-white text-orange-400 rounded-lg px-3 py-2 font-bold text-xl min-w-[50px]">
                  {{ timeLeft.hours.toString().padStart(2, '0') }}
                </div>
                <div class="text-xs mt-1 text-blue-200">Heures</div>
              </div>
              <div class="text-center">
                <div class="bg-white text-orange-400 rounded-lg px-3 py-2 font-bold text-xl min-w-[50px]">
                  {{ timeLeft.minutes.toString().padStart(2, '0') }}
                </div>
                <div class="text-xs mt-1 text-blue-200">Minutes</div>
              </div>
              <div class="text-center">
                <div class="bg-white text-orange-400 rounded-lg px-3 py-2 font-bold text-xl min-w-[50px]">
                  {{ timeLeft.seconds.toString().padStart(2, '0') }}
                </div>
                <div class="text-xs mt-1 text-blue-200">Secondes</div>
              </div>
            </div>
          </div>
         
            <button class="offer-button font-semibold px-8 py-3 rounded-full transition-colors flex items-center">
              Voir toutes les offres
              <svg class="w-4 h-4 ml-2 " fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <line x1="5" y1="12" x2="19" y2="12"></line>
                <polyline points="12,5 19,12 12,19"></polyline>
              </svg>
            </button>
         
        </div>

        <!-- Section Centre - Produits Populaires -->
        <div class="space-y-4">
          <h3 class="text-white text-xl font-semibold text-center mb-6">Produits Populaires</h3>

          <!-- État de chargement -->
          <div v-if="isLoading" class="space-y-4">
            <div v-for="i in 3" :key="i" class="bg-white/95 backdrop-blur-sm rounded-2xl p-4 border border-white/20 animate-pulse">
              <div class="flex gap-4">
                <div class="w-20 h-20 bg-gray-300 rounded-lg"></div>
                <div class="flex-1 space-y-2">
                  <div class="h-4 bg-gray-300 rounded w-3/4"></div>
                  <div class="h-3 bg-gray-300 rounded w-1/2"></div>
                  <div class="h-4 bg-gray-300 rounded w-1/3"></div>
                </div>
              </div>
            </div>
          </div>

          <!-- Produits chargés -->
          <div v-else-if="!error && popularProducts.length > 0" class="space-y-4">
            <div
              v-for="product in popularProducts"
              :key="product.id"
              class="bg-white/95 backdrop-blur-sm rounded-2xl p-4 border border-white/20 hover:scale-105 transition-transform cursor-pointer"
              @click="navigateToProduct(product)"
            >
              <div class="flex gap-4">
                <div class="relative">
                  <img
                    :src="product.image"
                    :alt="product.name"
                    class="w-20 h-20 object-cover rounded-lg"
                    @error="$event.target.src = 'https://images.unsplash.com/photo-1572569511254-d8f925fe2cbb?w=200&h=200&fit=crop&auto=format'"
                  />
                  <div class="absolute -top-2 -right-2 text-white text-xs px-2 py-1 rounded-full" style="background-color: #fe7900;">
                    {{ product.badge }}
                  </div>
                </div>
                <div class="flex-1 space-y-1">
                  <h4 class="font-semibold text-sm text-gray-800 line-clamp-2">{{ product.name }}</h4>
                  <div class="flex items-center gap-1">
                    <svg class="w-3 h-3 fill-yellow-400 text-yellow-400" viewBox="0 0 20 20">
                      <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                    </svg>
                    <span class="text-xs text-gray-600">{{ product.rating.toFixed(1) }}</span>
                  </div>
                  <div class="flex items-center gap-2">
                    <span class="font-bold " style="color: #fe7900;">{{ formatPrice(product.price) }}</span>
                    <span class="text-xs text-gray-500 line-through">{{ formatPrice(product.originalPrice) }}</span>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- État d'erreur -->
          <div v-else-if="error" class="bg-white/95 backdrop-blur-sm rounded-2xl p-4 border border-white/20 text-center">
            <p class="text-red-600 text-sm">{{ error }}</p>
            <button @click="fetchMostViewedProducts" class="mt-2 text-sm " style="background-color: lightgray; color: black;">
              Réessayer
            </button>
          </div>
        </div>

        <!-- Section Droite - Produits en Vedette -->
        <div class="space-y-4">
          <h3 class="text-white text-xl font-semibold text-center mb-6">Produits en Vedette</h3>

          <!-- État de chargement -->
          <div v-if="isLoading" class="space-y-4">
            <div v-for="i in 4" :key="i" class="bg-white/95 backdrop-blur-sm rounded-2xl p-4 border border-white/20 animate-pulse">
              <div class="flex gap-4">
                <div class="w-20 h-20 bg-gray-300 rounded-lg"></div>
                <div class="flex-1 space-y-2">
                  <div class="h-4 bg-gray-300 rounded w-3/4"></div>
                  <div class="h-3 bg-gray-300 rounded w-1/2"></div>
                  <div class="h-4 bg-gray-300 rounded w-1/3"></div>
                </div>
              </div>
            </div>
          </div>

          <!-- Produits chargés -->
          <div v-else-if="!error && featuredProducts.length > 0" class="space-y-4">
            <div
              v-for="product in featuredProducts"
              :key="product.id"
              @click="navigateToProduct(product)"
              class="bg-white/95 backdrop-blur-sm rounded-2xl p-4 border border-white/20 hover:scale-105 transition-transform cursor-pointer"
            >
              <div class="flex gap-4">
                <div class="relative">
                  <img
                    :src="product.image"
                    :alt="product.name"
                    class="w-20 h-20 object-cover rounded-lg"
                    @error="$event.target.src = 'https://images.unsplash.com/photo-1505740420928-5e560c06d30e?w=200&h=200&fit=crop&auto=format'"
                  />
                  <div class="absolute -top-3 -right-4  text-white text-xs px-2 py-1 rounded-full" style="background-color: #fe7900;">
                    {{ product.badge }}
                  </div>
                </div>
                <div class="flex-1 space-y-1">
                  <h4 class="font-semibold text-sm text-gray-800 line-clamp-2">{{ product.name }}</h4>
                  <div class="flex items-center gap-1">
                    <svg class="w-3 h-3 fill-yellow-400 text-yellow-400" viewBox="0 0 20 20">
                      <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                    </svg>
                    <span class="text-xs text-gray-600">{{ product.rating.toFixed(1) }}</span>
                  </div>
                  <div class="flex items-center gap-2">
                    <span class="font-bold " style="color: #fe7900;">{{ formatPrice(product.price) }}</span>
                    <span class="text-xs text-gray-500 line-through">{{ formatPrice(product.originalPrice) }}</span>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- État d'erreur -->
          <div v-else-if="error" class="bg-white/95 backdrop-blur-sm rounded-2xl p-4 border border-white/20 text-center">
            <p class="text-red-600 text-sm">{{ error }}</p>
            <button @click="fetchMostViewedProducts" class="mt-2" style="background-color: lightgray; color: black;">
              Réessayer
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, onBeforeUnmount } from 'vue'
import { useRouter } from 'vue-router'
import { formatPrice } from '../../services/formatPrice.js';
import { productsApi } from '../../services/api.js' // Ajuster le chemin selon votre structure

// Timer state
const router = useRouter()

const timeLeft = ref({
  hours: 8,
  minutes: 45,
  seconds: 32,
})

// États de chargement et données
const isLoading = ref(true)
const error = ref(null)

// Products data - Remplacer les données statiques
const popularProducts = ref([])
const featuredProducts = ref([])

const navigateToProduct = (product) => {
    console.log('🔄 Navigation vers le produit:', product)
    
    let slug = product.slug
    
    router.push(`/detail_resultat_produit/${slug}`)
  }

// Fonction pour récupérer les produits les plus vus
const fetchMostViewedProducts = async () => {
  try {
    isLoading.value = true
    error.value = null
    
    const response = await productsApi.getMostViewedProducts()
    
    if (response.success && response.data) {
      // Assigner les groupes de produits
      popularProducts.value = response.data.top_3_products.map(product => ({
        id: product.id,
        name: product.name || product.product_name,
        price: product.unit_price.toFixed(2),
        slug: product.slug,
        originalPrice: (product.unit_price * 1.5).toFixed(2), // Prix original simulé
        image: product.primary_image || 'https://images.unsplash.com/photo-1572569511254-d8f925fe2cbb?w=200&h=200&fit=crop&auto=format',
        rating: 4.5 + (Math.random() * 0.5), // Rating simulé
        badge: product.views_count > 1000 ? 'Hot' : 'Top',
      }))
      
      featuredProducts.value = response.data.next_4_products.map(product => ({
        id: product.id,
        slug: product.slug,
        name: product.name || product.product_name,
        price: product.unit_price.toFixed(2),
        originalPrice: (product.unit_price * 1.4).toFixed(2), // Prix original simulé
        image: product.primary_image || 'https://images.unsplash.com/photo-1505740420928-5e560c06d30e?w=200&h=200&fit=crop&auto=format',
        rating: 4.5 + (Math.random() * 0.5), // Rating simulé
        badge: product.sales_count > 20 ? 'Bestseller' : 'Promo',
      }))
    }
  } catch (err) {
    console.error('Erreur lors du chargement des produits:', err)
    error.value = 'Impossible de charger les produits'
    
    // Fallback vers des données par défaut en cas d'erreur
    popularProducts.value = [
      {
        id: 1,
        name: "Produit en vedette",
        price: "29.99",
        originalPrice: "49.99",
        image: "https://images.unsplash.com/photo-1572569511254-d8f925fe2cbb?w=200&h=200&fit=crop&auto=format",
        rating: 4.7,
        badge: "Hot",
      }
    ]
    
    featuredProducts.value = [
      {
        id: 2,
        name: "Produit populaire",
        price: "39.99",
        originalPrice: "59.99",
        image: "https://images.unsplash.com/photo-1505740420928-5e560c06d30e?w=200&h=200&fit=crop&auto=format",
        rating: 4.8,
        badge: "Bestseller",
      }
    ]
  } finally {
    isLoading.value = false
  }
}

let timerInterval = null

// Timer functions
const updateTimer = () => {
  if (timeLeft.value.seconds > 0) {
    timeLeft.value.seconds--
  } else if (timeLeft.value.minutes > 0) {
    timeLeft.value.minutes--
    timeLeft.value.seconds = 59
  } else if (timeLeft.value.hours > 0) {
    timeLeft.value.hours--
    timeLeft.value.minutes = 59
    timeLeft.value.seconds = 59
  } else {
    // Reset timer
    timeLeft.value = { hours: 23, minutes: 59, seconds: 59 }
  }
}
// Lifecycle hooks
onMounted(() => {
  timerInterval = setInterval(updateTimer, 1000)
  fetchMostViewedProducts() // Charger les produits au montage du composant
})

onBeforeUnmount(() => {
  if (timerInterval) {
    clearInterval(timerInterval)
  }
})
</script>

<style scoped>
.degrade-fond {
  background: linear-gradient(160deg, black , #fc4618 , black );
  
}

.offer-button{
  background-color: white;
  color: #fe7900 ;

}
.offer-button:hover {
  background-color: #e66e00;
}
.line-clamp-2 {
  display: -webkit-box;
  -webkit-line-clamp: 2;
  line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
}

/* Animations d'entrée */
@keyframes fadeInUp {
  from {
    opacity: 0;
    transform: translateY(30px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

.space-y-6 > * {
  animation: fadeInUp 0.6s ease-out forwards;
}

.space-y-6 > *:nth-child(2) {
  animation-delay: 0.1s;
}

.space-y-6 > *:nth-child(3) {
  animation-delay: 0.2s;
}

.space-y-6 > *:nth-child(4) {
  animation-delay: 0.3s;
}

/* Responsive */
@media (max-width: 1024px) {
  .grid {
    grid-template-columns: 1fr;
    gap: 2rem;
  }
  
  .text-4xl {
    font-size: 2.5rem;
  }
  
  .lg\:text-5xl {
    font-size: 3rem;
  }
}

@media (max-width: 768px) {
  .px-4 {
    padding-left: 1rem;
    padding-right: 1rem;
  }
  
  .py-8 {
    padding-top: 2rem;
    padding-bottom: 2rem;
  }
  
  .text-4xl {
    font-size: 2rem;
  }
  
  .gap-8 {
    gap: 1.5rem;
  }
}
</style>
