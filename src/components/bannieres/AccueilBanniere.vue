<template>
  <div class="relative w-full bg-gradient-to-r from-orange-500 via-orange-600 to-orange-500 overflow-hidden">
    <!-- Subtle Pattern Overlay -->
    <div class="absolute inset-0 opacity-10">
      <div class="absolute inset-0" style="background-image: url('data:image/svg+xml,%3Csvg width=\'60\' height=\'60\' viewBox=\'0 0 60 60\' xmlns=\'http://www.w3.org/2000/svg\'%3E%3Cg fill=\'none\' fill-rule=\'evenodd\'%3E%3Cg fill=\'%23ffffff\' fill-opacity=\'1\'%3E%3Cpath d=\'M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z\'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E');"></div>
    </div>

    <!-- Decorative Truck Silhouette -->
    <div class="absolute right-0 top-0 bottom-0 w-1/3 opacity-5">
      <svg class="h-full w-full" viewBox="0 0 200 100" fill="white">
        <path d="M20,60 L60,60 L60,40 L80,40 L100,50 L100,60 L180,60 L180,70 L20,70 Z M70,65 A5,5 0 1,1 70,75 A5,5 0 1,1 70,65 M160,65 A5,5 0 1,1 160,75 A5,5 0 1,1 160,65"/>
      </svg>
    </div>

    <div class="relative max-w-7xl mx-auto px-6 py-8">
      <div class="flex flex-col lg:flex-row items-center justify-between gap-6">
        <!-- Left Content -->
        <div class="flex-1 text-center lg:text-left">
          <div class="inline-flex items-center gap-2 bg-white/20 backdrop-blur-sm px-4 py-1.5 rounded-full mb-3">
            <span class="w-2 h-2 bg-white rounded-full animate-pulse"></span>
            <span class="text-white text-sm font-semibold uppercase tracking-wide">Offre Limitée</span>
          </div>
          
          <h2 class="text-3xl lg:text-4xl font-bold text-white mb-2 leading-tight">
            Up to <span class="text-yellow-300">70%</span> ff on our trucks
          </h2>
          
          <p class="text-white/90 text-base lg:text-lg max-w-xl">
            Enjoy our best deals on a premium selection of trucks 🚛🔥
          </p>
        </div>

        <!-- Right Products Showcase -->
        <div class="flex-shrink-0 w-full lg:w-auto">
          <div class="flex gap-3 overflow-x-auto pb-2 lg:pb-0 scrollbar-hide">
            
            <!-- Loading State -->
            <template v-if="isLoadingCategories">
              
              <div v-for="i in 2" :key="'loading-' + i" 
                   class="flex-shrink-0 w-48 bg-white/95 backdrop-blur-sm rounded-xl p-3 shadow-lg animate-pulse">
                <div class="w-full h-24 bg-gray-200 rounded-lg mb-2"></div>
                <div class="h-3 bg-gray-200 rounded w-3/4 mb-2"></div>
                <div class="h-4 bg-gray-200 rounded w-1/2"></div>
              </div>
            </template>

            <!-- Products -->
            <template v-else-if="categories.length > 0">
              <div
                v-for="product in categories"
                :key="product.id"
                 @click="navigateToCategory(product)"
                class="group flex-shrink-0 w-48 bg-white/95 backdrop-blur-sm rounded-xl p-3 shadow-lg hover:shadow-xl hover:scale-105 transition-all duration-300 cursor-pointer"
              >
                <div class="relative mb-2 overflow-hidden rounded-lg">
                  <img
                    :src="product.image"
                    :alt="product.name"
                    class="w-full h-24 object-cover group-hover:scale-110 transition-transform duration-300"
                    @error="$event.target.src = 'https://images.unsplash.com/photo-1601584115197-04ecc0da31d7?w=300&h=200&fit=crop&auto=format'"
                  />
                  <div class="absolute top-1 right-1 bg-orange-500 text-white text-xs font-bold px-2 py-0.5 rounded-full shadow">
                    {{ product.icon }}
                  </div>
                </div>
                
                <h4 class="text-sm font-semibold text-gray-800 line-clamp-1 mb-1" style="font-size: 20px;">
                  <span v-if="product.name == 'Camions'">Trucks </span>
                  <span v-else>{{ product.name }} </span>
                </h4>
                {{ product.subcategories.length }} Models
                
                <div class="flex items-center gap-1 mb-1">
                  <svg class="w-3 h-3 fill-yellow-400" viewBox="0 0 20 20">
                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                  </svg>
                  <span class="text-xs text-gray-600">{{ product.product_count }}</span>
                </div>
                
              </div>
            </template>

            <!-- Error State -->
            <template v-else-if="error">
              <div class="flex-shrink-0 w-48 bg-white/95 backdrop-blur-sm rounded-xl p-4 shadow-lg text-center">
                <p class="text-red-500 text-sm mb-2">{{ error }}</p>
                <button @click="fetchProducts" class="text-xs text-orange-500 hover:text-orange-600 font-medium">
                  Réessayer
                </button>
              </div>
            </template>
          </div>
        </div>

        <!-- CTA Button -->
        <div class="flex-shrink-0">
          <button class="group bg-white text-orange-500 hover:bg-orange-50 font-semibold px-8 py-3 rounded-full transition-all duration-300 shadow-lg hover:shadow-xl flex items-center gap-2 hover:scale-105">
            <span>All Products</span>
            <svg class="w-4 h-4 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
            </svg>
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import { formatPrice } from '../../services/formatPrice.js'
import { categoriesApi } from '../../services/api.js'

const router = useRouter()
const isLoading = ref(true)
const error = ref(null)
const products = ref([])
const categories = ref([])
const isLoadingCategories = ref(true)
const categoriesError = ref(null)

const navigateToProduct = (product) => {
  router.push(`/detail_resultat_produit/${product.slug}`)
}

const loadCategories = async () => {
  console.log('🔄 Chargement des catégories...')
  try {
    categoriesError.value = null

    const response = await categoriesApi.getCategories()

    if (response.success && response.data) {

      // 🔥 FILTRE : exclure "Trailer"
      categories.value = response.data
        .filter(category => category.name?.trim().toLowerCase() !== 'trailer')
        .map(category => ({
          id: category.id,
          name: category.name,
          icon: category.icon || '📦',
          image: category.image,
          count: category.product_count || Math.floor(Math.random() * 10000) + 1000,
          subcategories: category.subcategories || []
        }))

    } else {
      throw new Error(response.message || 'Error to load categories')
    }

  } catch (error) {
    console.error('❌ Erreur lors du chargement des catégories:', error)
    categoriesError.value = error.message

    // ⚠️ Fallback (aucun "Trailer" ici non plus)
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
    ]
  } finally {
    isLoadingCategories.value = false
  }
}

const navigateToCategory = (category) => {
      router.push({
        path: '/recherche_de_produit_list',
        query: { category: category.id }
      });
    };


onMounted(() => {
  loadCategories()
})
</script>

<style scoped>
.line-clamp-1 {
  display: -webkit-box;
  -webkit-line-clamp: 1;
  line-clamp: 1;
  -webkit-box-orient: vertical;
  overflow: hidden;
}

/* Hide scrollbar but keep functionality */
.scrollbar-hide {
  -ms-overflow-style: none;
  scrollbar-width: none;
}

.scrollbar-hide::-webkit-scrollbar {
  display: none;
}

/* Responsive adjustments */
@media (max-width: 1024px) {
  .flex-col {
    text-align: center;
  }
}

@media (max-width: 768px) {
  .text-3xl {
    font-size: 1.75rem;
  }
  
  .lg\:text-4xl {
    font-size: 2rem;
  }
  
  .px-6 {
    padding-left: 1rem;
    padding-right: 1rem;
  }
}
</style>