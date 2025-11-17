<template>
  <div class="product-info">
    <div class="product-badges">
      <span class="badge primary-color " style="background: #fff2e8;" v-if="product.status === 'Actif'">In Stock</span>
      <span class="badge success-color" style="background: #f6ffed;" v-if="product.stock > 0">Available</span>
      <span class="badge blue-color" style="background: #e6f7ff;" v-if="isNewProduct">New</span>
    </div>
    
    <h1 class="product-title">{{ product.name }}</h1>
    
    <!-- <div class="product-rating">
      <div class="stars">
        <span v-for="i in 5" :key="i" class="star" :class="{ filled: i <= Math.floor(productRating) }">★</span>
      </div>
      <span class="rating-value">{{ productRating }}</span>
      <span class="reviews-count">{{ product.views_count || 0 }} Views</span>
      <span class="orders-count">{{ product.sales_count || 0 }} Sales</span>
    </div> -->

    <div class="product-price-section">
      <div class="price-range">
        <div class="price-label">Prix:</div>
        <div class="price-value primary-color">
          <span v-if="product.wholesale_price && product.wholesale_min_qty">
            {{ formatPrice(product.wholesale_price,{showFOB:true}) }} - {{ formatPrice(product.unit_price,{showFOB:true}) }}
          </span>
          <span v-else>
            {{ formatPrice(product.unit_price,{showFOB:true}) }}
          </span>
        </div>
        <!-- ( FOB ) -->
      </div>
      
      <div class="price-table" v-if="product.wholesale_price && product.wholesale_min_qty">
        <div class="price-table-header">
          <div class="price-table-cell">Quantity (pieces)</div>
          <div class="price-table-cell">1 - {{ product.wholesale_min_qty - 1 }}</div>
          <div class="price-table-cell">{{ product.wholesale_min_qty }}+</div>
        </div>
        <div class="price-table-row">
          <div class="price-table-cell">Price</div>
          <div class="price-table-cell">{{ formatPrice(product.unit_price) }}</div>
          <div class="price-table-cell">{{ formatPrice(product.wholesale_price) }}</div>
        </div>
      </div>
    </div>

    <div class="order-section">
      <div class="quantity-selector">
        <div class="quantity-label">Quantity:</div>
        <div class="quantity-controls">
          <button class="quantity-btn" @click="$emit('decreaseQuantity')" :disabled="quantity <= 1">-</button>
          <input type="number" :value="quantity" @input="$emit('updateQuantity', parseInt($event.target.value))" min="1" :max="product.stock" class="quantity-input focus:border-ring-2 focus:ring-0">
          <button class="quantity-btn" @click="$emit('increaseQuantity')" :disabled="quantity >= product.stock">+</button>
        </div>
        <div class="quantity-available">{{ product.stock }} available</div>
      </div>
      
      <div class="total-price">
        <span class="total-label">Total:</span>
        <span class="total-value primary-color">{{ formatPrice(calculateTotal()) }}</span> ( FOB )
      </div>
      
      <div v-if="!hasVariants && currentShippingCost > 0" class="grand-total">
        <span class="grand-total-label">Total including delivery:</span>
        <span class="grand-total-value primary-color">{{ formatPrice(calculateTotal() + currentShippingCost) }}</span>
      </div>
      
      <div class="action-buttons">
        <!-- <button class="btn-whatsapp flex-1" @click="$emit('contactWhatsApp')">
          <svg width="16" height="16" viewBox="0 0 24 24" fill="white">
            <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/>
          </svg>
          Chat now
        </button> -->
        <button class="btn-outline flex-1" @click="chat.isDesktopChatOpen=true">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"></path>
            </svg>
            Chat now
          </button>
        <button class="btn-degrade-orange flex-1" @click="handleOrderClick">
          <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <circle cx="9" cy="21" r="1"></circle>
            <circle cx="20" cy="21" r="1"></circle>
            <path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"></path>
          </svg>
          Order now
        </button>
      </div>
    </div>

    
  </div>
</template>

<script setup>
import { computed, defineProps, defineEmits } from 'vue'
import { useRouter } from 'vue-router'
import { formatPrice } from '../../services/formatPrice'
import { useChatStore } from '../../stores/chat'

const router = useRouter()
const chat = useChatStore()

const props = defineProps({
  product: {
    type: Object,
    required: true
  },
  quantity: {
    type: Number,
    default: 1
  },
  selectedVariants: {
    type: Array,
    default: () => []
  },
  productColors: {
    type: Array,
    default: () => []
  },
  productSizes: {
    type: Array,
    default: () => []
  },
  selectedShipping: {
    type: String,
    default: 'adjame'
  },
  selectedCommune: {
    type: String,
    default: ''
  },
  selectedVille: {
    type: String,
    default: ''
  },
  tarifsAbidjan: {
    type: Array,
    default: () => []
  },
  tarifsInterieur: {
    type: Array,
    default: () => []
  },
  currentShippingCost: {
    type: Number,
    default: 0
  }
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
  'contactWhatsApp',
  'openChat',
  'startOrder'
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

const handleOrderClick = () => {
  const productData = {
    id: props.product.id,
    name: props.product.name,
    unit_price: props.product.unit_price,
    price: props.product.unit_price,
    wholesale_price: props.product.wholesale_price,
    wholesale_min_qty: props.product.wholesale_min_qty,
    stock: props.product.stock,
    primary_image: props.product.primary_image || props.product.images?.[0],
    image: props.product.primary_image || props.product.images?.[0],
    vehicle_make: props.product.vehicle_make,
    vehicle_model: props.product.vehicle_model,
    vehicle_year: props.product.vehicle_year,
    boutique_name: props.product.boutique_name,
    boutique_id: props.product.boutique_id
  }

  const orderState = {
    product: productData,
    quantity: props.quantity
  }

  // 🧠 Sauvegarde dans le sessionStorage (fallback pour refresh ou anciennes versions de Vue Router)
  try {
    sessionStorage.setItem('lastProduct', JSON.stringify(orderState))
  } catch (error) {
    console.error('[v0] Error saving product to sessionStorage:', error)
  }

  // 🚀 Navigation avec route.state (si Vue Router ≥ 4.2)
  router.push({
    path: '/order-validation',
    state: orderState
  })
}
</script>

<style scoped>
.product-info {
  padding: 0 16px;
}

.product-badges {
  display: flex;
  gap: 8px;
  margin-bottom: 16px;
  flex-wrap: wrap;
}

.badge {
  padding: 4px 12px;
  border-radius: 16px;
  font-size: 12px;
  font-weight: 600;
  
}

.product-title {
  font-size: 24px;
  font-weight: 600;
  color: #333;
  line-height: 1.4;
  margin: 0 0 16px 0;
}

.product-rating {
  display: flex;
  align-items: center;
  gap: 12px;
  margin-bottom: 24px;
  flex-wrap: wrap;
}

.stars {
  display: flex;
}

.star {
  color: #d9d9d9;
  font-size: 16px;
}

.star.filled {
  color: #fe7900;
}

.rating-value {
  font-weight: 600;
  color: #333;
}

.reviews-count,
.orders-count {
  color: #666;
  font-size: 14px;
  position: relative;
  padding-left: 12px;
}

.reviews-count::before,
.orders-count::before {
  content: '';
  position: absolute;
  left: 4px;
  top: 50%;
  width: 4px;
  height: 4px;
  background: #d9d9d9;
  border-radius: 50%;
  transform: translateY(-50%);
}

.product-price-section {
  background: #fafafa;
  padding: 16px;
  border-radius: 8px;
  margin-bottom: 24px;
}

.price-range {
  display: flex;
  align-items: center;
  gap: 12px;
  margin-bottom: 16px;
}

.price-label {
  font-size: 16px;
  color: #666;
}

.price-value {
  font-size: 24px;
  font-weight: 700;
}

.price-table {
  border: 1px solid #e8e8e8;
  border-radius: 8px;
  overflow: hidden;
}

.price-table-header,
.price-table-row {
  display: grid;
  grid-template-columns: 1fr 1fr 1fr;
  color: #666;
}

.price-table-header {
  background: #f5f5f5;
}

.price-table-cell {
  padding: 10px 16px;
  border-right: 1px solid #e8e8e8;
  border-bottom: 1px solid #e8e8e8;
  text-align: center;
  font-size: 14px;
}

.price-table-cell:last-child {
  border-right: none;
}

.price-table-row .price-table-cell {
  border-bottom: none;
  font-weight: 600;
}

.order-section {
  margin-bottom: 24px;
}

.quantity-selector {
  margin-bottom: 16px;
}

.quantity-label {
  font-size: 16px;
  font-weight: 600;
  color: #333;
  margin-bottom: 12px;
}

.quantity-input {
  -webkit-appearance: none;
  -moz-appearance: textfield;
  appearance: none;
  width: auto;
  height: 50px;
  border: 1px solid #d9d9d9;
  color: #333;
  border-left: none;
  border-right: none;
  text-align: center;
  font-size: 18px;
}

.quantity-input::-webkit-outer-spin-button,
.quantity-input::-webkit-inner-spin-button {
  -webkit-appearance: none;
  margin: 0;
}

.quantity-input[type="number"] {
  -moz-appearance: textfield;
  appearance: textfield;
}

.quantity-controls {
  display: flex;
  align-items: center;
  height: auto;
  margin-bottom: 8px;
  color: gray;
}

.quantity-btn {
  width: auto;
  height: 50px;
  font-size: 26px;
  border: 1px solid #d9d9d9;
  background: #fff;
  display: flex;
  align-items: center;
  justify-content: center;
  cursor: pointer;
  transition: all 0.3s ease;
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

.quantity-available {
  font-size: 14px;
  color: #666;
}

.total-price {
  display: flex;
  align-items: center;
  gap: 12px;
  margin-bottom: 16px;
}

.total-label {
  font-size: 16px;
  font-weight: 600;
  color: #333;
}

.total-value {
  font-size: 24px;
  font-weight: 700;
}

.grand-total {
  display: flex;
  align-items: center;
  gap: 12px;
  margin-bottom: 24px;
  padding: 12px;
  background: #fff7e6;
  border: 1px solid #ffd591;
  border-radius: 6px;
}

.grand-total-label {
  font-size: 16px;
  font-weight: 600;
  color: #333;
}

.grand-total-value {
  font-size: 24px;
  font-weight: 700;
}

.action-buttons {
  display: flex;
  gap: 8px;
  margin-bottom: 16px;
}


@media (max-width: 768px) {
  .product-info {
    padding: 0 12px;
  }
  
  .product-title {
    font-size: 20px;
  }
  
  .price-value {
    font-size: 20px;
  }
  
  .total-value {
    font-size: 20px;
  }
  
  .grand-total-value {
    font-size: 20px;
  }
  
  .action-buttons {
    flex-direction: column;
    gap: 12px;
  }
  
  .btn-whatsapp,
  .btn-degrade-orange {
    flex: none;
    width: 100%;
  }
  
  .price-table-cell {
    padding: 8px 12px;
    font-size: 12px;
  }
  
  .product-rating {
    gap: 8px;
  }
  
  .reviews-count,
  .orders-count {
    color: #666;
    font-size: 14px;
    position: relative;
    padding-left: 12px;
  }
  
  .reviews-count::before,
  .orders-count::before {
    content: '';
    position: absolute;
    left: 4px;
    top: 50%;
    width: 4px;
    height: 4px;
    background: #d9d9d9;
    border-radius: 50%;
    transform: translateY(-50%);
  }
  
  .product-price-section {
    background: #fafafa;
    padding: 16px;
    border-radius: 8px;
    margin-bottom: 24px;
  }
  
  .price-range {
    display: flex;
    align-items: center;
    gap: 12px;
    margin-bottom: 16px;
  }
  
  .price-label {
    font-size: 16px;
    color: #666;
  }
  
  .price-value {
    font-size: 24px;
    font-weight: 700;
  }
  
  .price-table {
    border: 1px solid #e8e8e8;
    border-radius: 8px;
    overflow: hidden;
  }
  
  .price-table-header,
  .price-table-row {
    display: grid;
    grid-template-columns: 1fr 1fr 1fr;
    color: #666;
  }
  
  .price-table-header {
    background: #f5f5f5;
  }
  
  .price-table-cell {
    padding: 10px 16px;
    border-right: 1px solid #e8e8e8;
    border-bottom: 1px solid #e8e8e8;
    text-align: center;
    font-size: 14px;
  }
  
  .price-table-cell:last-child {
    border-right: none;
  }
  
  .price-table-row .price-table-cell {
    border-bottom: none;
    font-weight: 600;
  }
  
  .order-section {
    margin-bottom: 24px;
  }
  
  .quantity-selector {
    margin-bottom: 16px;
  }
  
  .quantity-label {
    font-size: 16px;
    font-weight: 600;
    color: #333;
    margin-bottom: 12px;
  }
  .quantity-input {
    -webkit-appearance: none; /* Chrome, Safari, Edge */
    -moz-appearance: textfield; /* Firefox */
    appearance: none;
    width: auto;
    height: 30px;
    border: 1px solid #d9d9d9;
    color: #333;
    border-left: none;
    border-right: none;
    text-align: center;
    font-size: 18px;
  }

  /* WebKit (Chrome, Safari, Edge) - supprime les deux boutons */
  .quantity-input::-webkit-outer-spin-button,
  .quantity-input::-webkit-inner-spin-button {
    -webkit-appearance: none;
    margin: 0;
  }

  /* Firefox - assure l'affichage comme champ texte */
  .quantity-input[type="number"] {
    -moz-appearance: textfield;
    appearance: textfield;
  }
  
  .quantity-controls {
    -webkit-appearance: none; /* Chrome, Safari, Edge */
    -moz-appearance: textfield; /* Firefox */
    appearance: none;
    display: flex;
    align-items: center;
    height: auto;
    margin-bottom: 8px;
    color: gray;
  }
  
  .quantity-btn {
    width: auto;
    height: 30px;
    font-size: 26px;
    border: 1px solid #d9d9d9;
    background: #fff;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    transition: all 0.3s ease;
    padding: 0 12px;
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
  
  /* .quantity-input {
    width: 60px;
    height: 40px;
    border: 1px solid #d9d9d9;
    border-left: none;
    border-right: none;
    text-align: center;
    font-size: 16px;
    font-weight: 500;
  } */
  
  .quantity-available {
    font-size: 14px;
    color: #666;
  }
  
  .total-price {
    display: flex;
    align-items: center;
    gap: 12px;
    margin-bottom: 16px;
  }
  
  .total-label {
    font-size: 16px;
    font-weight: 600;
    color: #333;
  }
  
  .total-value {
    font-size: 24px;
    font-weight: 700;
  }
  
  .shipping-cost-simple {
    margin-bottom: 16px;
    padding: 12px;
    background: #f0f8ff;
    border: 1px solid #b3d9ff;
    border-radius: 6px;
  }
  
  .shipping-cost-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 4px;
  }
  
  .shipping-cost-label {
    font-size: 14px;
    font-weight: 500;
    color: #333;
  }
  
  .shipping-cost-details {
    display: flex;
    justify-content: space-between;
    font-size: 12px;
  }
}
</style>
