<template>
  <div class="category-source-section">
    <div class="scroll-container">
      {{categories}}
      <button
        class="scroll-button left"
        @click="scrollLeft"
        :disabled="!canScrollLeft"
      >
        &lt;
      </button>
      <div class="categories-container" ref="categoriesContainer">
        <div
          class="categories"
          :style="{ transform: `translateX(${scrollOffset}px)` }"
        >
          <div
            v-for="category in categories"
            :key="category.id"
            class="category-item"
            @click="goToCategory(category)"
          >
            <img
              :src="category.image"
              alt="category.name"
              @error="handleImageError"
            />
            <p>{{ category.name }}</p>
          </div>
          <div class="category-item show-more" @click="showMoreCategories">
            <p>Plus</p>
          </div>
        </div>
      </div>
      <button
        class="scroll-button right"
        @click="scrollRight"
        :disabled="!canScrollRight"
      >
        &gt;
      </button>
    </div>
  </div>
</template>

<script>
import { ref, computed, onMounted, onUnmounted } from 'vue'
import { useRouter } from 'vue-router'

export default {
  name: 'CategorySourceSection',
  emits: ['category-click'],
  // Ajoutez 'props' ici pour recevoir les données
  props: {
    categories: {
      type: Array,
      required: true,
      default: () => []
    }
  },
  setup(props, { emit }) { // 'props' est maintenant disponible ici
    const router = useRouter()
    const scrollOffset = ref(0)
    const itemWidth = ref(160) // Largeur d'un élément + marge
    const visibleItems = ref(8)
    const maxScrollOffset = ref(0)

    // Supprimez la déclaration de rawCategoriesData car les données viennent des props
    // const rawCategoriesData = { ... };

    // Fonction pour extraire toutes les sub_sub_subcategories
    const extractSubSubSubCategories = (data) => {
      const allSubSubSubCategories = [];
      // Assurez-vous que 'data' est bien l'array de catégories directement
      if (data && Array.isArray(data)) {
        data.forEach(category => {
          if (category.subcategories && Array.isArray(category.subcategories)) {
            category.subcategories.forEach(subcategory => {
              if (subcategory.sub_subcategories && Array.isArray(subcategory.sub_subcategories)) {
                subcategory.sub_subcategories.forEach(subSubCategory => {
                  if (subSubCategory.sub_sub_subcategories && Array.isArray(subSubCategory.sub_sub_subcategories)) {
                    subSubCategory.sub_sub_subcategories.forEach(subSubSubCategory => {
                      allSubSubSubCategories.push({
                        id: subSubSubCategory.id,
                        name: subSubSubCategory.name,
                        image: subSubSubCategory.image || '/placeholder.svg?height=100&width=100', // Use provided image or a placeholder
                        link: `/search?sub_sub_subcategory_id=${subSubSubCategory.id}` // Generate a link based on ID
                      });
                    });
                  }
                });
              }
            });
          }
        });
      }
      return allSubSubSubCategories;
    };

    // Initialisez categories avec les sub_sub_subcategories extraites des props
    const categories = ref(extractSubSubSubCategories(props.categories)); // Utilisez props.categories ici

    const totalWidth = computed(() => {
      return (categories.value.length + 1) * itemWidth.value
    })

    const canScrollLeft = computed(() => scrollOffset.value < 0) // Correction pour canScrollLeft
    const canScrollRight = computed(() => scrollOffset.value > -maxScrollOffset.value) // Correction pour canScrollRight

    const updateScrollLimits = () => {
      const containerWidth = visibleItems.value * itemWidth.value
      maxScrollOffset.value = Math.max(0, totalWidth.value - containerWidth)
    }

    const scrollLeft = () => {
      const scrollAmount = itemWidth.value * 3
      scrollOffset.value = Math.min(scrollOffset.value + scrollAmount, 0)
    }

    const scrollRight = () => {
      const scrollAmount = itemWidth.value * 3
      const newOffset = scrollOffset.value - scrollAmount
      scrollOffset.value = Math.max(newOffset, -maxScrollOffset.value)
    }

    const goToCategory = (category) => {
      emit('category-click', category)
      router.push(category.link)
    }

    const showMoreCategories = () => {
      console.log('Afficher plus de catégories')
    }

    const handleImageError = (event) => {
      const img = event.target;
      if (!img.dataset.fallback) {
        img.dataset.fallback = 'true';
        img.src = 'https://via.placeholder.com/100x100/f0f0f0/666?text=Cat';
      } else {
        img.style.display = 'none';
      }
    };

    const handleResize = () => {
      const width = window.innerWidth
      if (width < 768) {
        visibleItems.value = 3
        itemWidth.value = 120
      } else if (width < 1024) {
        visibleItems.value = 5
        itemWidth.value = 140
      } else {
        visibleItems.value = 8
        itemWidth.value = 160
      }
      updateScrollLimits()
    }

    onMounted(() => {
      handleResize()
      updateScrollLimits()
      window.addEventListener('resize', handleResize)
      console.log(categories)
    })

    onUnmounted(() => {
      window.removeEventListener('resize', handleResize)
    })

    return {
      categories,
      scrollOffset,
      totalWidth,
      canScrollLeft,
      canScrollRight,
      scrollLeft,
      scrollRight,
      goToCategory,
      showMoreCategories,
      handleImageError
    }
  }
}
</script>

<style scoped>
.category-source-section {
  overflow: hidden;
  padding: 20px 0;
}

.scroll-container {
  display: flex;
  align-items: center;
  position: relative;
}

.scroll-button {
  background-color: #f0f0f0;
  border: none;
  padding: 10px;
  cursor: pointer;
  position: absolute;
  top: 50%;
  transform: translateY(-50%);
  z-index: 10;
}

.scroll-button.left {
  left: 0;
}

.scroll-button.right {
  right: 0;
}

.categories-container {
  overflow: hidden;
  width: 100%;
}

.categories {
  display: flex;
  transition: transform 0.3s ease;
}

.category-item {
  flex: 0 0 auto;
  width: 160px;
  text-align: center;
  padding: 10px;
  cursor: pointer;
}

.category-item img {
  width: 100px;
  height: 100px;
  object-fit: cover;
  border-radius: 8px;
  margin-bottom: 5px;
}

.category-item p {
  margin: 0;
}

.category-item.show-more img {
  background-color: #f0f0f0;
  display: flex;
  align-items: center;
  justify-content: center;
}

.category-item.show-more p {
  color: #666;
}

/* Responsive adjustments */
@media (max-width: 768px) {
  .category-item {
    width: 120px;
  }

  .category-item img {
    width: 80px;
    height: 80px;
  }
}

@media (min-width: 768px) and (max-width: 1024px) {
  .category-item {
    width: 140px;
  }

  .category-item img {
    width: 90px;
    height: 90px;
  }
}
</style>
