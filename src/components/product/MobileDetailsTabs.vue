<template>
    <div class="mobile-details-tabs">
      <div class="tabs-header">
        <button 
          v-for="(tab, index) in tabs" 
          :key="index"
          class="tab-button" 
          :class="{ active: activeTab === index } "
          @click="activeTab = index"
        >
          {{ tab.label }}
        </button>
      </div>
      
      <div class="tab-content">
        <!-- Spécifications -->
        <div v-if="activeTab === 0" class="tab-panel">
          <div class="specs-list">
            <div class="spec-item">
              <span class="spec-label">SKU:</span>
              <span class="spec-value">{{ product.sku || 'N/A' }}</span>
            </div>
            <div class="spec-item">
              <span class="spec-label">Catégorie:</span>
              <span class="spec-value">{{ product.category_name || 'N/A' }}</span>
            </div>
            <div class="spec-item">
              <span class="spec-label">Stock:</span>
              <span class="spec-value">{{ product.stock }} pièces</span>
            </div>
            <div class="spec-item">
              <span class="spec-label">Statut:</span>
              <span class="spec-value">{{ product.status }}</span>
            </div>
          </div>
        </div>
        <!-- Description -->
        <div v-if="activeTab === 1" class="tab-panel">
          <div class="description-content">
            <p>{{ product.description || 'Aucune description disponible.' }}</p>
          </div>
        </div>
        
        
        
        <!-- Avis -->
        <div v-if="activeTab === 2" class="tab-panel">
          <div class="reviews-summary">
            <div class="rating-display">
              <div class="rating-number">{{ productRating }}</div>
              <div class="rating-stars">
                <span v-for="i in 5" :key="i" class="star" :class="{ filled: i <= Math.floor(productRating) }">★</span>
              </div>
              <div class="rating-count">{{ product.views_count || 0 }} Vues</div>
            </div>
          </div>
          
          <div class="no-reviews">
            <p>Aucun avis pour le moment.</p>
            <button style="display: inline;" class="btn-degrade-orange mt-4" @click="$emit('openReviewForm')">
              Écrire un avis
            </button>
          </div>
        </div>
        
      </div>
    </div>
  </template>
  
  <script setup>
  import { ref, defineProps, defineEmits } from 'vue'
  
  const props = defineProps({
    product: Object,
    productRating: Number
  })
  
  const emit = defineEmits([
    'openReviewForm'
  ])
  
  const activeTab = ref(0)
  
  const tabs = [
    { label: "Spécifications" },
    { label: "Description" },
    { label: "Avis" },
  ]
  </script>
  
  <style scoped>
  .mobile-details-tabs {
    background: #fff;
  }
  
  .tabs-header {
    display: flex;
    border-bottom: 1px solid #e8e8e8;
    background: #fff;
    position: sticky;
    top: 0;
    z-index: 10;
  }
  
  .tab-button {
    flex: 1;
    padding: 12px 8px;
    border: none;
    background: transparent;
    font-size: 13px;
    font-weight: 500;
    color: #666;
    cursor: pointer;
    transition: all 0.3s ease;
    position: relative;
  }
  
  .tab-button.active {
    color: #fe7900;
    font-weight: 600;
  }
  
  .tab-button.active::after {
    content: '';
    position: absolute;
    bottom: 0;
    left: 0;
    width: 100%;
    height: 2px;
    background: #fe7900;
  }
  
  .tab-content {
    min-height: 200px;
  }
  
  .tab-panel {
    padding: 16px;
  }
  
  .description-content {
    line-height: 1.6;
    color: #666;
    font-size: 14px;
  }
  
  .specs-list {
    display: flex;
    flex-direction: column;
    gap: 12px;
  }
  
  .spec-item {
    display: flex;
    justify-content: space-between;
    padding: 8px 0;
    border-bottom: 1px solid #f0f0f0;
  }
  
  .spec-label {
    font-size: 14px;
    color: #666;
  }
  
  .spec-value {
    font-size: 14px;
    font-weight: 500;
    color: #333;
  }
  
  .reviews-summary {
    text-align: center;
    margin-bottom: 24px;
  }
  
  .rating-display {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 8px;
    padding: 20px;
    background: #fafafa;
    border-radius: 8px;
  }
  
  .rating-number {
    font-size: 32px;
    font-weight: 700;
    color: #fe7900;
  }
  
  .rating-stars {
    display: flex;
  }
  
  .star {
    color: #d9d9d9;
    font-size: 16px;
  }
  
  .star.filled {
    color: #fadb14;
  }
  
  .rating-count {
    font-size: 13px;
    color: #666;
  }
  
  .no-reviews {
    align-items: center;
    justify-content:center ;
    text-align: center;
    color: #666;
  }
  
  
  .shipping-info {
    display: flex;
    flex-direction: column;
    gap: 16px;
  }
  
  .shipping-method {
    padding: 12px;
    border: 1px solid #e8e8e8;
    border-radius: 8px;
  }
  
  .method-header {
    display: flex;
    align-items: center;
    gap: 8px;
    font-size: 14px;
    font-weight: 500;
    margin-bottom: 8px;
    color: #333;
  }
  
  .method-details {
    display: flex;
    justify-content: space-between;
    font-size: 13px;
  }
  
  .method-time {
    color: #666;
  }
  
  .payment-methods h4 {
    font-size: 15px;
    font-weight: 600;
    color: #333;
    margin-bottom: 12px;
  }
  
  .payment-list {
    display: flex;
    flex-direction: column;
    gap: 8px;
  }
  
  .payment-item {
    padding: 8px 12px;
    background: #fafafa;
    border-radius: 6px;
    font-size: 14px;
    color: #333;
  }
  </style>
  