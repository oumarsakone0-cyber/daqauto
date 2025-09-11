<template>
  <div class="min-h-screen bg-cream">
    <!-- Header -->
    <header class="bg-white shadow-sm border-b border-gray-200">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between h-16">
          <!-- Logo -->
          <div class="flex items-center space-x-2">
            <div class="w-8 h-8 bg-orange rounded flex items-center justify-center">
              <svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 20 20">
                <path d="M3 4a1 1 0 011-1h12a1 1 0 011 1v2a1 1 0 01-1 1H4a1 1 0 01-1-1V4zM3 10a1 1 0 011-1h6a1 1 0 011 1v6a1 1 0 01-1 1H4a1 1 0 01-1-1v-6zM14 9a1 1 0 00-1 1v6a1 1 0 001 1h2a1 1 0 001-1v-6a1 1 0 00-1-1h-2z"/>
              </svg>
            </div>
            <span class="text-2xl font-bold text-dark-green">Bazaar Trabi</span>
          </div>

          <!-- Navigation -->
          <nav class="hidden md:flex space-x-8">
            <a href="#" class="text-dark-green hover:text-orange font-medium">Accueil</a>
            <a href="#" class="text-gray-600 hover:text-orange font-medium">Produits</a>
            <a href="#" class="text-gray-600 hover:text-orange font-medium">Catégories</a>
            <a href="#" class="text-gray-600 hover:text-orange font-medium">Contact</a>
          </nav>

          <!-- Search Bar -->
          <div class="flex-1 max-w-md mx-8">
            <div class="relative">
              <svg class="absolute left-3 top-1/2 transform -translate-y-1/2 w-4 h-4 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z" clip-rule="evenodd"/>
              </svg>
              <input 
                v-model="searchQuery"
                type="text" 
                placeholder="Rechercher des produits..." 
                class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-orange focus:border-transparent"
              >
            </div>
          </div>

          <!-- Cart & User -->
          <div class="flex items-center space-x-4">
            <button class="relative p-2 text-gray-600 hover:text-orange">
              <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20">
                <path d="M3 1a1 1 0 000 2h1.22l.305 1.222a.997.997 0 00.01.042l1.358 5.43-.893.892C3.74 11.846 4.632 14 6.414 14H15a1 1 0 000-2H6.414l1-1H14a1 1 0 00.894-.553l3-6A1 1 0 0017 3H6.28l-.31-1.243A1 1 0 005 1H3zM16 16.5a1.5 1.5 0 11-3 0 1.5 1.5 0 013 0zM6.5 18a1.5 1.5 0 100-3 1.5 1.5 0 000 3z"/>
              </svg>
              <span class="absolute -top-1 -right-1 bg-orange text-white text-xs rounded-full w-5 h-5 flex items-center justify-center">{{ cartCount }}</span>
            </button>
            <div class="w-8 h-8 bg-dark-green rounded-full flex items-center justify-center text-white text-sm font-medium">
              U
            </div>
          </div>
        </div>
      </div>
    </header>

    <!-- Hero Section -->
    <section class="bg-dark-green text-white py-16">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between">
          <div class="max-w-2xl">
            <h1 class="text-4xl md:text-5xl font-bold mb-4">
              Découvrez les meilleurs produits pour votre style de vie
            </h1>
            <p class="text-xl text-gray-300 mb-8">
              Une sélection premium de produits tech, mode et lifestyle à des prix imbattables.
            </p>
            <button class="bg-orange text-white px-8 py-3 rounded-lg hover:bg-orange-600 transition-colors flex items-center space-x-2 text-lg font-medium">
              <span>Découvrir</span>
              <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10.293 3.293a1 1 0 011.414 0l6 6a1 1 0 010 1.414l-6 6a1 1 0 01-1.414-1.414L14.586 11H3a1 1 0 110-2h11.586l-4.293-4.293a1 1 0 010-1.414z" clip-rule="evenodd"/>
              </svg>
            </button>
          </div>
          <div class="hidden lg:flex space-x-4">
            <div class="w-20 h-20 bg-white rounded-full flex items-center justify-center">
              <span class="text-4xl">💻</span>
            </div>
            <div class="w-20 h-20 bg-white rounded-full flex items-center justify-center">
              <span class="text-4xl">📱</span>
            </div>
            <div class="w-20 h-20 bg-white rounded-full flex items-center justify-center">
              <span class="text-4xl">🎧</span>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- Categories -->
    <section class="py-16">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <h2 class="text-3xl font-bold text-dark-green mb-8 text-center">Nos Catégories</h2>
        <div class="grid grid-cols-2 md:grid-cols-4 gap-6">
          <div v-for="category in categories" :key="category.id" 
               class="bg-white rounded-lg p-6 text-center hover:shadow-lg transition-shadow cursor-pointer border border-gray-200">
            <div class="text-4xl mb-4">{{ category.icon }}</div>
            <h3 class="font-semibold text-dark-green mb-2">{{ category.name }}</h3>
            <p class="text-sm text-gray-600">{{ category.count }} produits</p>
          </div>
        </div>
      </div>
    </section>

    <!-- Featured Products -->
    <section class="py-16 bg-white">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between mb-8">
          <h2 class="text-3xl font-bold text-dark-green">Produits Populaires</h2>
          <div class="flex space-x-4">
            <button 
              v-for="filter in productFilters" 
              :key="filter"
              @click="activeFilter = filter"
              :class="activeFilter === filter ? 'text-orange border-b-2 border-orange' : 'text-gray-600 hover:text-orange'"
              class="pb-2 font-medium transition-colors"
            >
              {{ filter }}
            </button>
          </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
          <div v-for="product in filteredProducts" :key="product.id" 
               class="bg-cream rounded-lg overflow-hidden hover:shadow-lg transition-shadow">
            <div class="aspect-square bg-gray-200 flex items-center justify-center text-4xl">
              {{ product.icon }}
            </div>
            <div class="p-4">
              <h3 class="font-semibold text-dark-green mb-2">{{ product.name }}</h3>
              <p class="text-sm text-gray-600 mb-3">{{ product.description }}</p>
              <div class="flex items-center justify-between">
                <div class="flex items-center space-x-2">
                  <span class="text-xl font-bold text-dark-green">{{ product.price }}</span>
                  <span v-if="product.originalPrice" class="text-sm text-gray-500 line-through">{{ product.originalPrice }}</span>
                </div>
                <button @click="addToCart(product)" 
                        class="bg-orange text-white px-4 py-2 rounded-lg hover:bg-orange-600 transition-colors text-sm font-medium">
                  Ajouter
                </button>
              </div>
              <div class="flex items-center mt-2">
                <div class="flex text-yellow-400">
                  <span v-for="star in 5" :key="star">⭐</span>
                </div>
                <span class="text-sm text-gray-600 ml-2">({{ product.reviews }})</span>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- Newsletter -->
    <section class="bg-dark-green text-white py-16">
      <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <h2 class="text-3xl font-bold mb-4">Restez informé de nos nouveautés</h2>
        <p class="text-xl text-gray-300 mb-8">
          Inscrivez-vous à notre newsletter et recevez 10% de réduction sur votre première commande
        </p>
        <div class="flex flex-col sm:flex-row gap-4 max-w-md mx-auto">
          <input 
            v-model="email"
            type="email" 
            placeholder="Votre adresse email" 
            class="flex-1 px-4 py-3 rounded-lg text-dark-green focus:outline-none focus:ring-2 focus:ring-orange"
          >
          <button class="bg-orange text-white px-6 py-3 rounded-lg hover:bg-orange-600 transition-colors font-medium">
            S'inscrire
          </button>
        </div>
      </div>
    </section>

    <!-- Footer -->
    <footer class="bg-white border-t border-gray-200 py-12">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
          <div>
            <div class="flex items-center space-x-2 mb-4">
              <div class="w-8 h-8 bg-orange rounded flex items-center justify-center">
                <svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 20 20">
                  <path d="M3 4a1 1 0 011-1h12a1 1 0 011 1v2a1 1 0 01-1 1H4a1 1 0 01-1-1V4zM3 10a1 1 0 011-1h6a1 1 0 011 1v6a1 1 0 01-1 1H4a1 1 0 01-1-1v-6zM14 9a1 1 0 00-1 1v6a1 1 0 001 1h2a1 1 0 001-1v-6a1 1 0 00-1-1h-2z"/>
                </svg>
              </div>
              <span class="text-xl font-bold text-dark-green">Bazaar</span>
            </div>
            <p class="text-gray-600">Votre destination pour les meilleurs produits tech et lifestyle.</p>
          </div>
          
          <div>
            <h3 class="font-semibold text-dark-green mb-4">Produits</h3>
            <ul class="space-y-2 text-gray-600">
              <li><a href="#" class="hover:text-orange">Électronique</a></li>
              <li><a href="#" class="hover:text-orange">Mode</a></li>
              <li><a href="#" class="hover:text-orange">Maison</a></li>
              <li><a href="#" class="hover:text-orange">Sport</a></li>
            </ul>
          </div>
          
          <div>
            <h3 class="font-semibold text-dark-green mb-4">Support</h3>
            <ul class="space-y-2 text-gray-600">
              <li><a href="#" class="hover:text-orange">Centre d'aide</a></li>
              <li><a href="#" class="hover:text-orange">Livraison</a></li>
              <li><a href="#" class="hover:text-orange">Retours</a></li>
              <li><a href="#" class="hover:text-orange">Contact</a></li>
            </ul>
          </div>
          
          <div>
            <h3 class="font-semibold text-dark-green mb-4">Suivez-nous</h3>
            <div class="flex space-x-4">
              <a href="#" class="text-gray-600 hover:text-orange">Facebook</a>
              <a href="#" class="text-gray-600 hover:text-orange">Twitter</a>
              <a href="#" class="text-gray-600 hover:text-orange">Instagram</a>
            </div>
          </div>
        </div>
        
        <div class="border-t border-gray-200 mt-8 pt-8 text-center text-gray-600">
          <p>&copy; 2024 Bazaar. Tous droits réservés.</p>
        </div>
      </div>
    </footer>
  </div>
</template>

<script setup>
import { ref, computed } from 'vue'

const searchQuery = ref('')
const email = ref('')
const cartCount = ref(3)
const activeFilter = ref('Tous')

const productFilters = ['Tous', 'Électronique', 'Mode', 'Maison', 'Sport']

const categories = ref([
  { id: 1, name: 'Électronique', icon: '💻', count: 245 },
  { id: 2, name: 'Mode', icon: '👕', count: 189 },
  { id: 3, name: 'Maison', icon: '🏠', count: 156 },
  { id: 4, name: 'Sport', icon: '⚽', count: 98 }
])

const products = ref([
  {
    id: 1,
    name: 'MacBook Air M2',
    description: 'Ordinateur portable ultra-fin et puissant',
    price: '1 299 F cfa',
    originalPrice: '1 499 F cfa',
    icon: '💻',
    category: 'Électronique',
    reviews: 124
  },
  {
    id: 2,
    name: 'iPhone 15 Pro',
    description: 'Le smartphone le plus avancé',
    price: '1 199 F cfa',
    originalPrice: null,
    icon: '📱',
    category: 'Électronique',
    reviews: 89
  },
  {
    id: 3,
    name: 'AirPods Pro',
    description: 'Écouteurs sans fil avec réduction de bruit',
    price: '279 F cfa',
    originalPrice: '329 F cfa',
    icon: '🎧',
    category: 'Électronique',
    reviews: 156
  },
  {
    id: 4,
    name: 'Veste en cuir',
    description: 'Veste en cuir véritable de qualité premium',
    price: '199 F cfa',
    originalPrice: '299 F cfa',
    icon: '🧥',
    category: 'Mode',
    reviews: 67
  },
  {
    id: 5,
    name: 'Canapé moderne',
    description: 'Canapé 3 places design scandinave',
    price: '899 F cfa',
    originalPrice: null,
    icon: '🛋️',
    category: 'Maison',
    reviews: 43
  },
  {
    id: 6,
    name: 'Chaussures de sport',
    description: 'Baskets haute performance pour le running',
    price: '129 F cfa',
    originalPrice: '159 F cfa',
    icon: '👟',
    category: 'Sport',
    reviews: 92
  },
  {
    id: 7,
    name: 'Montre connectée',
    description: 'Smartwatch avec suivi santé avancé',
    price: '399 F cfa',
    originalPrice: null,
    icon: '⌚',
    category: 'Électronique',
    reviews: 78
  },
  {
    id: 8,
    name: 'Sac à dos',
    description: 'Sac à dos urbain imperméable',
    price: '79 F cfa',
    originalPrice: '99 F cfa',
    icon: '🎒',
    category: 'Mode',
    reviews: 134
  }
])

const filteredProducts = computed(() => {
  if (activeFilter.value === 'Tous') {
    return products.value
  }
  return products.value.filter(product => product.category === activeFilter.value)
})

const addToCart = (product) => {
  cartCount.value++
  // Animation ou notification d'ajout au panier
  console.log(`${product.name} ajouté au panier`)
}
</script>

<style scoped>
@layer base {
  :root {
    --color-dark-green: #22331D;
    --color-cream: #EFEDE7;
    --color-orange: #F65A11;
    --color-gray-green: #616D69;
  }
}

@layer utilities {
  .bg-dark-green {
    background-color: var(--color-dark-green);
  }
  
  .bg-cream {
    background-color: var(--color-cream);
  }
  
  .bg-orange {
    background-color: var(--color-orange);
  }
  
  .bg-gray-green {
    background-color: var(--color-gray-green);
  }
  
  .text-dark-green {
    color: var(--color-dark-green);
  }
  
  .text-cream {
    color: var(--color-cream);
  }
  
  .text-orange {
    color: var(--color-orange);
  }
  
  .text-gray-green {
    color: var(--color-gray-green);
  }
  
  .border-orange {
    border-color: var(--color-orange);
  }
  
  .ring-orange {
    --tw-ring-color: var(--color-orange);
  }
  
  .hover\:bg-orange-600:hover {
    background-color: #e54a0a;
  }
  
  .hover\:text-orange:hover {
    color: var(--color-orange);
  }
}
</style>