<template>
    <div class="mobile-product-info">
      <!-- Product Basic Info -->
      <div class="product-basic-info">
        <div class="product-badges">
          <span class="badge primary-color" style="background: #fff2e8;" v-if="product.status === 'Actif'">In Stock</span>
          <span class="badge success-color" style="background:   #f6ffed;" v-if="product.stock > 0">Available </span>
          <span class="badge blue-color" style="background: #e6f7ff;" v-if="isNewProduct">New</span>
        </div>
        
        <h1 class="product-title">{{ product.name }}</h1>
        
        <div class="product-rating">
          <div class="stars">
            <span v-for="i in 5" :key="i" class="star" :class="{ filled: i <= Math.floor(productRating) }">★</span>
          </div>
          <span class="rating-value">{{ productRating }}</span>
          <span class="reviews-count">{{ product.views_count || 0 }} Views</span>
        </div>
  
        <div class="product-price-section">
          <div class="price-range">
            <div class="price-value primary-color">
              <span v-if="product.wholesale_price && product.wholesale_min_qty">
                {{ formatPrice(product.wholesale_price) }} - {{ formatPrice(product.unit_price) }}
              </span>
              <span v-else>
                {{ formatPrice(product.unit_price) }}
              </span>
            </div>
          </div>
          
          <div class="price-breakdown" v-if="product.wholesale_price && product.wholesale_min_qty">
            <div class="price-item">
              <span class="price-qty">1-{{ product.wholesale_min_qty - 1 }} pcs:</span>
              <span class="price-amount">{{ formatPrice(product.unit_price) }}</span>
            </div>
            <div class="price-item">
              <span class="price-qty">{{ product.wholesale_min_qty }}+ pcs:</span>
              <span class="price-amount">{{ formatPrice(product.wholesale_price) }}</span>
            </div>
          </div>
        </div>
      </div>
  
      <!-- Supplier Info -->
      <div class="mobile-supplier-section">
        <div class="supplier-header">
          <div class="supplier-logo">
            <img :src="supplier.logo" :alt="supplier.name" />
          </div>
          <div class="supplier-info">
            <h3 class="supplier-name">{{ supplier.name }}</h3>
            <div class="supplier-location">
              <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/>
                <circle cx="12" cy="10" r="3"/>
              </svg>
              <span>{{ supplier.market }}</span>
            </div>
          </div>
          <button class="visit-store-btn" @click="$emit('visitStore')">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <polyline points="9,18 15,12 9,6"/>
            </svg>
          </button>
        </div>
      </div>
  
     
  
      <!-- Quantity Section -->
      <div class="mobile-quantity-section" >
        <div class="quantity-header">
          <span class="quantity-label">Quantité:</span>
          <span class="stock-info">{{ product.stock }} disponibles</span>
        </div>
        <div class="quantity-controls">
          <button class="quantity-btn" @click="$emit('decreaseQuantity')" :disabled="quantity <= 1">-</button>
          <input type="number" :value="quantity" @input="$emit('updateQuantity', parseInt($event.target.value))" min="1" :max="product.stock" class="quantity-input">
          <button class="quantity-btn" @click="$emit('increaseQuantity')" :disabled="quantity >= product.stock">+</button>
        </div>
      </div>
  
      <!-- Shipping Section -->
      <div class="mobile-shipping-section">
        <ShippingSection 
          :selected-shipping="selectedShipping"
          :selected-commune="selectedCommune"
          :selected-ville="selectedVille"
          :tarifs-abidjan="tarifsAbidjan"
          :tarifs-interieur="tarifsInterieur"
          @select-shipping="$emit('selectShipping', $event)"
          @update-commune="$emit('updateCommune', $event)"
          @update-ville="$emit('updateVille', $event)"
        />
      </div>
  
      <!-- Total Section -->
      <div class="mobile-total-section">
        <div class="total-breakdown">
          <div class="total-item">
            <span>Sous-total:</span>
            <span>{{ formatPrice(calculateTotal()) }}</span>
          </div>
          <div class="total-item" v-if="currentShippingCost > 0">
            <span>Livraison:</span>
            <span>{{ formatPrice(currentShippingCost) }}</span>
          </div>
          <div class="total-item total primary-color">
            <span>Total:</span>
            <span>{{ formatPrice(calculateTotal() + currentShippingCost) }}</span>
          </div>
        </div>
      </div>
    </div>
  </template>
  
  <script setup>
  import { computed, defineProps, defineEmits } from 'vue'
  import ProductVariants from './ProductVariants.vue'
  import { formatPrice } from '../../services/formatPrice'
  import ShippingSection from './ShippingSection.vue'
  
  const props = defineProps({
    product: Object,
    quantity: Number,
    selectedVariants: Array,
    productColors: Array,
    productSizes: Array,
    selectedShipping: String,
    selectedCommune: String,
    selectedVille: String,
    tarifsAbidjan: Array,
    tarifsInterieur: Array,
    supplier: Object,
    currentShippingCost: Number
  })
  
  const emit = defineEmits([
    'addVariant',
    'removeVariant',
    'updateVariantSize',
    'updateVariantColor',
    'updateVariantQuantity',
    'selectShipping',
    'updateCommune',
    'updateVille',
    'decreaseQuantity',
    'increaseQuantity',
    'updateQuantity',
    'visitStore',
    'contactSupplier'
  ])
  
  const hasVariants = computed(() => {
    return props.productColors.length > 0 || props.productSizes.length > 0
  })
  
  const productRating = computed(() => {
    if (!props.product) return 0
    
    const views = props.product.views_count || 0
    const sales = props.product.sales_count || 0
    
    const rating = 3 + Math.min(2, (views + sales * 10) / 1000)
    return Math.round(rating * 10) / 10
  })
  
  const isNewProduct = computed(() => {
    if (!props.product || !props.product.created_at) return false
    
    const createdDate = new Date(props.product.created_at)
    const now = new Date()
    const daysDiff = (now - createdDate) / (1000 * 60 * 60 * 24)
    
    return daysDiff <= 30
  })
  
  const calculateTotal = () => {
    if (!props.product) return 0
  
    const unitPrice = getUnitPrice()
  
    if (hasVariants.value && props.selectedVariants.length > 0) {
      return props.selectedVariants.reduce((total, variant) => {
        return total + (unitPrice * variant.quantity)
      }, 0)
    }
  
    return unitPrice * props.quantity
  }
  
  const getUnitPrice = () => {
    if (!props.product) return 0
    
    let price = props.product.unit_price
    
    let totalQuantity = hasVariants.value 
      ? props.selectedVariants.reduce((sum, variant) => sum + variant.quantity, 0)
      : props.quantity
    
    if (props.product.wholesale_price && 
        props.product.wholesale_min_qty && 
        totalQuantity >= props.product.wholesale_min_qty) {
      price = props.product.wholesale_price
    }
    
    return price
  }
  
  </script>
  
  <style scoped>
  .mobile-product-info {
    background: #fff;
  }
  
  .product-basic-info {
    padding: 16px;
    border-bottom: 8px solid #f5f5f5;
  }
  
  .product-badges {
    display: flex;
    gap: 8px;
    margin-bottom: 12px;
    flex-wrap: wrap;
  }
  
  .badge {
    padding: 4px 8px;
    border-radius: 12px;
    font-size: 11px;
    font-weight: 600;
  }
  
  .badge.hot {
    background: #fff2e8;
    color: #fa541c;
  }
  
  .badge.ready {
    background: #f6ffed;
    color: #52c41a;
  }
  
  .badge.new {
    background: #e6f7ff;
    color: #1890ff;
  }
  
  .product-title {
    font-size: 18px;
    font-weight: 600;
    color: #333;
    line-height: 1.4;
    margin: 0 0 12px 0;
  }
  
  .product-rating {
    display: flex;
    align-items: center;
    gap: 8px;
    margin-bottom: 16px;
    flex-wrap: wrap;
  }
  
  .stars {
    display: flex;
  }
  
  .star {
    color: #d9d9d9;
    font-size: 14px;
  }
  
  .star.filled {
    color: #fe9700;
  }
  
  .rating-value {
    font-weight: 600;
    color: #333;
    font-size: 14px;
  }
  
  .reviews-count {
    color: #666;
    font-size: 12px;
  }
  
  .product-price-section {
    background: #fafafa;
    padding: 12px;
    border-radius: 8px;
  }
  
  .price-value {
    font-size: 20px;
    font-weight: 700;
    margin-bottom: 8px;
  }
  
  .price-breakdown {
    display: flex;
    flex-direction: column;
    gap: 4px;
  }
  
  .price-item {
    display: flex;
    justify-content: space-between;
    font-size: 13px;
  }
  
  .price-qty {
    color: #666;
  }
  
  .price-amount {
    font-weight: 600;
    color: #333;
  }
  
  .mobile-supplier-section {
    padding: 16px;
    border-bottom: 8px solid #f5f5f5;
  }
  
  .supplier-header {
    display: flex;
    align-items: center;
    gap: 12px;
  }
  
  .supplier-logo {
    width: 48px;
    height: 48px;
    border-radius: 8px;
    overflow: hidden;
    flex-shrink: 0;
  }
  
  .supplier-logo img {
    width: 100%;
    height: 100%;
    object-fit: cover;
  }
  
  .supplier-info {
    flex: 1;
  }
  
  .supplier-name {
    font-size: 15px;
    font-weight: 600;
    color: #333;
    margin: 0 0 4px 0;
  }
  
  .supplier-location {
    display: flex;
    align-items: center;
    gap: 4px;
    font-size: 12px;
    color: #666;
  }
  
  .visit-store-btn {
    width: 32px;
    height: 32px;
    border: 1px solid #d9d9d9;
    background: #fff;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #666;
  }
  
  .mobile-variants-section,
  .mobile-quantity-section,
  .mobile-shipping-section {
    padding: 16px;
    border-bottom: 8px solid #f5f5f5;
  }
  
  .quantity-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 12px;
  }
  
  .quantity-label {
    font-size: 15px;
    font-weight: 600;
    color: #333;
  }
  
  .stock-info {
    font-size: 13px;
    color: #666;
  }
  
  .quantity-controls {
    display: flex;
    align-items: center;
    height: 44px;
  }
  
  .quantity-btn {
    width: 44px;
    height: 44px;
    border: 1px solid #d9d9d9;
    background: #fff;
    font-size: 18px;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
  }
  
  .quantity-btn:first-child {
    border-radius: 8px 0 0 8px;
  }
  
  .quantity-btn:last-child {
    border-radius: 0 8px 8px 0;
  }
  
  .quantity-btn:disabled {
    opacity: 0.5;
    cursor: not-allowed;
  }
  
  .quantity-input {
    width: 80px;
    height: 44px;
    color: black;
    border: 1px solid #d9d9d9;
    border-left: none;
    border-right: none;
    text-align: center;
    font-size: 16px;
    font-weight: 500;
  }
  
  .mobile-total-section {
    padding: 16px;
    background: #fafafa;
  }
  
  .total-breakdown {
    display: flex;
    flex-direction: column;
    gap: 8px;
  }
  
  .total-item {
    display: flex;
    justify-content: space-between;
    font-size: 14px;
  }
  
  .total-item.total {
    font-size: 16px;
    font-weight: 700;
    padding-top: 8px;
    border-top: 1px solid #e8e8e8;
  }
  </style>
  