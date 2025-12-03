<template>
  <div class="product-info">
    <div class="product-badges">
      <span class="badge success-color capitalize" style="background: #f6ffed;">{{ product.disponibility }}</span>
      <span class="badge blue-color capitalize" style="background: #e6f7ff;" v-if="product.vehicle_condition==='new'">{{ product.vehicle_condition }}</span>
    </div>
    
    <h1 class="product-title">{{ product.name }}</h1>
    <div class="product-price-section">
      <div class="price-range">
        <div class="price-label">Price:</div>
        <div class="price-value primary-color">
          <span >
            {{ formatPrice(product.unit_price,{showFOB:true}) }}
          </span>
        </div>
      </div>
    </div>
    <div class="order-section">
      <div class=" w-full bg-gray-50 justify-end py-6 mb-4 rounded-lg">
        <div class="quantity-selector ">
          <span class="capitalize bg-white py-2 px-10 rounded-full">{{ product.vehicle_condition || "N/A" }}</span>
          <span v-if="product.vehicle_mileage" class="bg-white py-2 px-10 rounded-full" >{{ product.vehicle_mileage }} km</span> <span v-else class="bg-white py-2 px-10 rounded-full">N/A</span>
          <span class="capitalize bg-white py-2 px-10 rounded-full">{{ product.fuel_type || "N/A" }}</span>
      </div>
      </div>
      
      <div class="quantity-selector mb-15">
        <span class="flex-1 capitalize"><span class="text-gray-500">VIN: </span>  {{  product.vin_numbers[0] || "N/A" }}</span>
        <span class="flex-1" ><span class="text-gray-500">Stock ID: </span> {{ product.stock_number || "N/A"}}</span>
      </div>
      <div class="action-buttons">
        <button class="btn-outline flex-1" @click="$emit('toggleCart')">
            <PlusIcon class="w-5 h-5" />
            Add to cart
          </button>
        <button class="btn-outline-with-background flex-1" @click="handleOrderClick">
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
import { defineProps, defineEmits } from 'vue'
import { useRouter } from 'vue-router'
import { formatPrice } from '../../services/formatPrice'
import { PlusIcon } from 'lucide-vue-next'

const router = useRouter()



const props = defineProps({
  product: {
    type: Object,
    required: true
  }
})

const emit = defineEmits([
  'toggleCart',
])


const goToProfile = () => {
  router.push('/profile_client')
};

const handleOrderClick = () => {
  const productData =[ {
    id: props.product.id,
    name: props.product.name,
    unit_price: props.product.unit_price,
    stock: props.product.stock,
    primary_image: props.product.primary_image || props.product.images?.[0],
    image: props.product.primary_image || props.product.images?.[0],
    vehicle_make: props.product.vehicle_make,
    vehicle_model: props.product.vehicle_model,
    vehicle_year: props.product.vehicle_year,
    boutique_name: props.product.boutique_name,
    boutique_id: props.product.boutique_id,
    boutique_logo: props.product.boutique_logo,
    boutique_marche: props.product.boutique_marche,
    boutique_type: props.product.boutique_type,
    boutique_premium: props.product.boutique_premium,
    boutique_verified: props.product.boutique_verified,
    boutique_address: props.product.boutique_address,
    boutique_description: props.product.boutique_description,
    boutique_phone: props.product.boutique_phone,
    vin_number:props.product.vin_numbers[0],
    trim_number:props.product.trim_numbers[0],
    stock_number: props.product.stock_number,
    color: props.product.colors[0].name,
    colorHex: props.product.colors[0].hex_value,
    quantity: 1,
  }]

  const orderState = {
    product: productData,
  }
  console.log('Navigating to order validation with state:', orderState)

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
  margin-bottom: 30px;
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
  margin-top: 30px;
}

.quantity-selector {
  display: flex;
  justify-content: space-around;
  color: #333;
  /* margin-bottom: 16px; */
  gap: 20px;
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
  width: 50px;
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
.quantity-btn:hover:not(:disabled) {
  background: #fe9700;
  color: #fff;
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
