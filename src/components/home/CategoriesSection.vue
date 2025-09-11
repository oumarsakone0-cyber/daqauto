<template>
    <!-- Version Mobile -->
    <section class="mobile-categories mobile-only">
      <div class="section-header-mobile">
        <h2>Catégories</h2>
        <button class="see-all-btn">Voir tout</button>
      </div>
      
      <div class="categories-grid-mobile">
        <div 
          v-for="(category, index) in categories.slice(0, 8)" 
          :key="category.id"
          @click="navigateToCategory(category)"
          class="category-item-mobile"
        >
          <div class="category-image-mobile">
            <img :src="category.image" :alt="category.name" />
          </div>
          <span class="category-name-mobile">{{ category.name }}</span>
        </div>
      </div>
    </section>
  
    <!-- Version Desktop -->
    <section class="amazon-carousel-section desktop-only">
      <div class="section-content">
        <div class="amazon-title-block">
          <h2 class="amazon-title-left">
            <span class="amazon-title-text">Catégories populaires</span>
          </h2>
          <span class="amazon-title-right">
            <a href="#" class="amazon-discover-link">Découvrir tout →</a>
          </span>
        </div>
        
        <div class="amazon-carousel-container">
          <!-- État de chargement -->
          <div v-if="isLoadingCategories" class="loading-categories">
            <div class="loading-skeleton" v-for="i in 6" :key="i">
              <div class="skeleton-image"></div>
              <div class="skeleton-text"></div>
              <div class="skeleton-count"></div>
            </div>
          </div>
  
          <!-- État d'erreur -->
          <div v-else-if="categoriesError" class="error-categories">
            <p class="error-message">{{ categoriesError }}</p>
            <button @click="loadCategories" class="retry-button">Réessayer</button>
          </div>
  
          <!-- Catégories chargées -->
          <div v-else class="amazon-carousel-viewport" ref="carouselViewport">
            <ul class="amazon-carousel-shelf" :style="{ transform: `translateX(-${currentSlide * slideWidth}px)` }">
              <li 
                v-for="(category, index) in categories" 
                :key="category.id"
                @click="navigateToCategory(category)"
                class="amazon-carousel-card"
              >
                <a href="#" class="amazon-category-link">
                  <div class="category-card-content">
                    <img :src="category.image" :alt="category.name" class="amazon-product-image" />
                    <div class="category-info">
                      <h3 class="category-name">{{ category.name }}</h3>
                      <p class="category-count">{{ category.count }}+ produits</p>
                    </div>
                  </div>
                </a>
              </li>
            </ul>
          </div>
          
          <a 
            v-if="!isLoadingCategories && !categoriesError && canScrollLeft"
            @click.prevent="scrollLeft" 
            class="amazon-carousel-control amazon-carousel-left"
          >
            <span class="amazon-arrow"></span>
          </a>
          
          <a 
            v-if="!isLoadingCategories && !categoriesError && canScrollRight"
            @click.prevent="scrollRight" 
            class="amazon-carousel-control amazon-carousel-right"
          >
            <span class="amazon-arrow"></span>
          </a>
        </div>
      </div>
    </section>
  </template>
  
  <script setup>
  import { ref, computed, onMounted } from 'vue'
  import { useRouter } from 'vue-router';
  import { categoriesApi } from '../../services/api.js'

  const router = useRouter();
  
  // États pour les catégories
  const categories = ref([])
  const isLoadingCategories = ref(true)
  const categoriesError = ref(null)
  
  // Variables pour le carrousel
  const carouselViewport = ref(null)
  const currentSlide = ref(0)
  const slideWidth = ref(220)
  const visibleSlides = ref(6)
  
  // Computed pour le carrousel
  const totalSlides = computed(() => categories.value.length)
  const maxSlide = computed(() => Math.max(0, totalSlides.value - visibleSlides.value))
  const canScrollLeft = computed(() => currentSlide.value > 0)
  const canScrollRight = computed(() => currentSlide.value < maxSlide.value)
  
  // Fonctions de navigation du carrousel
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

  const navigateToCategory = (category) => {
      router.push({
        path: '/recherche_de_produit_list',
        query: { category: category.id }
      });
    };
  
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
  
  onMounted(() => {
    loadCategories()
  })
  </script>
  
  <style scoped>
  /* Styles pour les catégories - repris du fichier original */
  .mobile-categories {
    background: white;
    padding: 16px;
    margin-bottom: 8px;
  }
  
  .section-header-mobile {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 16px;
  }
  
  .section-header-mobile h2 {
    font-size: 18px;
    font-weight: 700;
    color: #333;
    margin: 0;
  }
  
  .see-all-btn {
    background: none;
    border: 1px solid #ff6b35;
    color: #ff6b35;
    padding: 6px 12px;
    border-radius: 16px;
    font-size: 12px;
    font-weight: 600;
  }
  
  .categories-grid-mobile {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 16px;
  }
  
  .category-item-mobile {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 8px;
    padding: 12px 8px;
    border-radius: 12px;
    transition: background 0.2s;
  }
  
  .category-item-mobile:active {
    background: #f5f5f5;
  }
  
  .category-image-mobile {
    width: 56px;
    height: 56px;
    border-radius: 50%;
    overflow: hidden;
    border: 2px solid #f0f0f0;
  }
  
  .category-image-mobile img {
    width: 100%;
    height: 100%;
    object-fit: cover;
  }
  
  .category-name-mobile {
    font-size: 12px;
    color: #333;
    text-align: center;
    font-weight: 500;
  }
  
  /* Styles desktop */
  .amazon-carousel-section {
    padding: 30px 0;
    background: #ffffff;
    border-bottom: 1px solid #e7e7e7;
  }
  
  .section-content {
    max-width: 1500px;
    margin: 0 auto;
    padding: 0 20px;
  }
  
  .amazon-title-block {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 20px;
  }
  
  .amazon-title-text {
    font-size: 24px;
    font-weight: 700;
    color: #0F1111;
  }
  
  .amazon-discover-link {
    color: #007185;
    text-decoration: none;
    font-size: 14px;
    font-weight: 500;
    transition: all 0.15s ease;
  }
  
  .amazon-discover-link:hover {
    color: #C7511F;
    text-decoration: underline;
  }
  
  .amazon-carousel-container {
    position: relative;
    height: 280px;
  }
  
  .amazon-carousel-viewport {
    overflow: hidden;
    width: 100%;
    height: 100%;
  }
  
  .amazon-carousel-shelf {
    display: flex;
    list-style: none;
    margin: 0;
    padding: 0;
    height: 100%;
    transition: transform 0.3s ease;
  }
  
  .amazon-carousel-card {
    margin-right: 20px;
    flex-shrink: 0;
    width: 200px;
  }
  
  .amazon-category-link {
    display: block;
    text-decoration: none;
    height: 100%;
    transition: all 0.15s ease;
  }
  
  .category-card-content {
    background: white;
    border-radius: 12px;
    overflow: hidden;
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08);
    height: 100%;
    transition: all 0.3s ease;
  }
  
  .amazon-category-link:hover .category-card-content {
    transform: translateY(-5px);
    box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
  }
  
  .amazon-product-image {
    width: 100%;
    height: 180px;
    object-fit: cover;
  }
  
  .category-info {
    padding: 20px;
  }
  
  .category-name {
    font-size: 16px;
    font-weight: 600;
    color: #333;
    margin: 0 0 8px 0;
  }
  
  .category-count {
    font-size: 14px;
    color: #666;
    margin: 0;
  }
  
  .amazon-carousel-control {
    position: absolute;
    top: 50%;
    transform: translateY(-50%);
    width: 45px;
    height: 45px;
    background: rgba(255, 255, 255, 0.95);
    border: 1px solid #D5D9D9;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    transition: all 0.15s ease;
    z-index: 10;
    text-decoration: none;
    color: #0F1111;
  }
  
  .amazon-carousel-control:hover {
    background: #F7F8F8;
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
  }
  
  .amazon-carousel-left {
    left: -22px;
  }
  
  .amazon-carousel-right {
    right: -22px;
  }
  
  .amazon-arrow {
    width: 0;
    height: 0;
    border-style: solid;
  }
  
  .amazon-carousel-left .amazon-arrow {
    border-width: 6px 8px 6px 0;
    border-color: transparent #0F1111 transparent transparent;
    margin-left: -2px;
  }
  
  .amazon-carousel-right .amazon-arrow {
    border-width: 6px 0 6px 8px;
    border-color: transparent transparent transparent #0F1111;
    margin-right: -2px;
  }
  
  /* États de chargement */
  .loading-categories {
    display: flex;
    gap: 20px;
  }
  
  .loading-skeleton {
    background: #f0f0f0;
    border-radius: 8px;
    width: 200px;
    height: 280px;
    position: relative;
    overflow: hidden;
  }
  
  .skeleton-image {
    width: 100%;
    height: 180px;
    background: linear-gradient(90deg, #f0f0f0 25%, #e0e0e0 50%, #f0f0f0 75%);
    background-size: 200% 100%;
    animation: loading 1.5s infinite;
  }
  
  .skeleton-text,
  .skeleton-count {
    height: 16px;
    background: linear-gradient(90deg, #f0f0f0 25%, #e0e0e0 50%, #f0f0f0 75%);
    background-size: 200% 100%;
    animation: loading 1.5s infinite;
    margin: 8px 12px;
    border-radius: 4px;
  }
  
  @keyframes loading {
    0% {
      background-position: 200% 0;
    }
    100% {
      background-position: -200% 0;
    }
  }
  
  .error-categories {
    text-align: center;
    padding: 40px;
    color: #666;
  }
  
  .error-message {
    font-size: 16px;
    margin-bottom: 16px;
  }
  
  .retry-button {
    padding: 10px 20px;
    background: #ff4747;
    color: white;
    border: none;
    border-radius: 6px;
    cursor: pointer;
    font-weight: 600;
    transition: all 0.3s ease;
  }
  
  .retry-button:hover {
    background: #e63946;
  }
  
  /* Responsive */
  .mobile-only {
    display: none;
  }
  
  .desktop-only {
    display: block;
  }
  
  @media (max-width: 768px) {
    .mobile-only {
      display: block;
    }
    
    .desktop-only {
      display: none;
    }
  }
  </style>
  