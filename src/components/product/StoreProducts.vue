<template>
    <div class="store-products-section" v-if="storeProducts.length > 0">
      <h2 class="section-title">Plus de cette boutique</h2>
      
      <div class="store-products">
        <div class="store-product" v-for="(storeProduct, index) in storeProducts" :key="index" @click="$emit('viewStoreProduct', storeProduct)">
          <div class="store-product-image">
            <img :src="storeProduct.primary_image" :alt="storeProduct.name">
          </div>
          <div class="store-product-info">
            <div class="store-product-title">{{ storeProduct.name }}</div>
            <div class="store-product-price">{{ formatFCFA(storeProduct.unit_price) }}</div>
          </div>
        </div>
      </div>
    </div>
  </template>
  
  <script setup>
  import { defineProps, defineEmits } from 'vue'
  
  const props = defineProps({
    storeProducts: Array
  })
  
  const emit = defineEmits([
    'viewStoreProduct'
  ])
  
  const formatFCFA = (montant) => {
    return Number(montant).toLocaleString('fr-FR', { minimumFractionDigits: 0 }) + ' FCFA'
  }
  </script>
  
  <style scoped>
  .store-products-section {
    background: #fff;
    border-radius: 8px;
    padding: 20px;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
  }
  
  .section-title {
    font-size: 18px;
    font-weight: 600;
    color: #333;
    margin-bottom: 20px;
  }
  
  .store-products {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 16px;
  }
  
  .store-product {
    cursor: pointer;
    transition: transform 0.3s ease;
  }
  
  .store-product:hover {
    transform: translateY(-4px);
  }
  
  .store-product-image {
    width: 100%;
    height: 120px;
    border-radius: 8px;
    overflow: hidden;
    margin-bottom: 8px;
  }
  
  .store-product-image img {
    width: 100%;
    height: 100%;
    object-fit: cover;
  }
  
  .store-product-title {
    font-size: 14px;
    font-weight: 500;
    color: #333;
    margin-bottom: 4px;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
    text-overflow: ellipsis;
  }
  
  .store-product-price {
    font-size: 14px;
    font-weight: 600;
    color: #fe9700;
  }
  
  @media (max-width: 992px) {
    .store-products {
      grid-template-columns: repeat(3, 1fr);
    }
  }
  
  @media (max-width: 768px) {
    .store-products {
      grid-template-columns: repeat(2, 1fr);
    }
  }
  
  @media (max-width: 480px) {
    .store-products {
      grid-template-columns: 1fr;
    }
  }
  </style>
  