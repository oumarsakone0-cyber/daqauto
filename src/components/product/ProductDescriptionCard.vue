<template>
    <div class="product-details-section">
      <div class="container">
        <div class="tabs-container">
          <div class="tabs-header">
            <button 
              v-for="(tab, index) in productTabs" 
              :key="index"
              class="tab-button" 
              :class="{ active: activeTab === index }"
              @click="activeTab = index"
            >
              {{ tab.label }}
            </button>
          </div>
         
          <div class="tab-content">
             <!-- Onglet Spécifications -->
           
            <div v-if="activeTab === 0" class="product-specifications">
                <div class="specifications-table two-columns">
                  <div class="spec-group">
                    <h3 class="spec-group-title">Informations générales</h3>
                    <!-- verifié -->
                    <div class="spec-row">
                      <div class="spec-name">Conditions</div>
                      <div class="spec-value">{{ product.vehicle_condition || 'N/A'}}</div>
                    </div>
                    <!-- verifié -->
                    <div class="spec-row">
                      <div class="spec-name">Make</div>
                      <div class="spec-value">{{ product.vehicle_make || 'N/A' }}</div>
                    </div>
                    <!-- vérifié -->
                    <div class="spec-row">
                      <div class="spec-name">Model</div>
                      <div class="spec-value">{{ product.vehicle_model || 'N/A' }}</div>
                    </div>
                    <!-- vérifié -->
                    <div class="spec-row">
                      <div class="spec-name">Category</div>
                      <div class="spec-value">{{ product.category_name || 'N/A'}}</div>
                    </div>
                    <!-- verifié -->
                    <div class="spec-row">
                      <div class="spec-name">Drive Type</div>
                      <div class="spec-value">{{ product.drive_type || 'N/A'}}</div>
                    </div>
                    <!-- verifié -->
                    <div class="spec-row">
                      <div class="spec-name">Year</div>
                      <div class="spec-value">{{ product.vehicle_year || 'N/A'}}</div>
                    </div>
                    <!-- verifié -->
                    <div class="spec-row">
                      <div class="spec-name">Fuel Type</div>
                      <div class="spec-value">{{ product.fuel_type || 'N/A' }}</div>
                    </div>
                    <!-- verifié -->
                    <div class="spec-row">
                      <div class="spec-name">Transmission</div>
                      <div class="spec-value">{{ product.transmission_type || 'N/A'}}</div>
                    </div>
                    <!-- verifié -->
                    <div class="spec-row">
                      <div class="spec-name">Engine</div>
                      <div class="spec-value">{{ product.engine_brand || 'N/A' }}</div>
                    </div>
                    <div class="spec-row">
                      <div class="spec-name">Engine Power</div>
                      <div class="spec-value">{{ product.power || 'N/A' }}</div>
                    </div>
                    <div class="spec-row">
                      <div class="spec-name">Engine Emmissions</div>
                      <div class="spec-value">{{ product.engine_emissions || 'N/A' }}</div>
                    </div>
                    <!-- vérifié -->
                    <div class="spec-row">
                      <div class="spec-name">Mileage</div>
                      <div class="spec-value">{{ product.vehicle_mileage || 'N/A' }}</div>
                    </div>
                    <!-- verifié -->
                    <div class="spec-row">
                      <div class="spec-name">Couleurs</div>
                      <div v-for="color in product.colors" :key="color.id" >
                        <div  class="spec-value">
                          <div>
                            <span 
                              class="inline-block w-6 h-6 rounded-full border border-gray-300 mr-2" 
                              :style="{ backgroundColor: color.hex_value || '#FFFFFF' }"
                            ></span>
                          </div>
                          {{ color.name || 'N/A' }}
                        </div>
                      </div>
                    </div>
                  </div>
                  <div class="spec-group">
                    <h3 class="spec-group-title">Détails techniques</h3>
                    <div class="spec-row">
                      <div class="spec-name">VIN / Chassis NO.</div>
                      <div class="spec-value">{{ product.vin || 'N/A'  }}</div>
                    </div>
                    <div class="spec-row">
                      <div class="spec-name">Engine Number</div>
                      <div class="spec-value">{{ product.engine_number || 'N/A' }}</div>
                    </div>
                    <div class="spec-row">
                      <div class="spec-name">Production date</div>
                      <div class="spec-value">{{ product.production_date || 'N/A' }}</div>
                    </div>
                    <div class="spec-row">
                      <div class="spec-name">Country of Origin</div>
                      <div class="spec-value">{{ product.country_of_origin || 'N/A' }}</div>
                    </div>
                    <div class="spec-row">
                      <div class="spec-name">Wheelbase</div>
                      <div class="spec-value">{{ product.wheelbase || 'N/A' }}</div>
                    </div>
                    <div class="spec-row">
                      <div class="spec-name">GVW</div>
                      <div class="spec-value">{{ product.gvw || 'N/A' }}</div>
                    </div>
                    <div class="spec-row">
                      <div class="spec-name">Payload Capacity</div>
                      <div class="spec-value">{{ product.payload_capacity || 'N/A' }}</div>
                    </div>
                    <div class="spec-row">
                      <div class="spec-name">Cabin Type</div>
                      <div class="spec-value">{{ product.cabin_type || 'N/A' }}</div>
                    </div>
                    <div class="spec-row">
                      <div class="spec-name">Suspension Type</div>
                      <div class="spec-value">{{ product.suspension_type || 'N/A' }}</div>
                    </div>
                    <div class="spec-row">
                      <div class="spec-name">Brake System</div>
                      <div class="spec-value">{{ product.brake_system || 'N/A' }}</div>
                    </div>
                    <div class="spec-row">
                      <div class="spec-name">Type Size</div>
                      <div class="spec-value">{{ product.type_size || 'N/A' }}</div>
                    </div>
                    <div class="spec-row">
                      <div class="spec-name">Dimensions (L x l x H)</div>
                      <div class="spec-value">{{ product.dimensions || 'N/A' }}</div>
                    </div>
                    <div class="spec-row">
                      <div class="spec-name">Curb Weight</div>
                      <div class="spec-value">{{ product.curb_weight || 'N/A' }}</div>
                    </div>
                    <div class="spec-row">
                      <div class="spec-name">Fuel Tank Capacity</div>
                      <div class="spec-value">{{ product.fuel_tank_capacity || 'N/A' }}</div>
                    </div>
                  </div>
                </div>
            </div>
            <!-- Onglet Description du produit -->
            <div v-if="activeTab === 1" class="product-description">
              <h2 class="section-title">Description du produit</h2>
              
              <div class="description-content">
                <p>{{ product.description || 'Aucune description disponible.' }}</p>
              </div>
              <div v-if="product.description_plus" >

                <h2 class="section-title">Plus de Description</h2>
                
                <div class="description-content">
                  <p>{{ product.description_plus || 'Aucune description disponible.' }}</p>
                </div>
              </div>
              
            </div>
            <!-- Onglet Avis -->
            <div v-if="activeTab === 2" class="product-reviews">
              <h2 class="section-title">Avis clients</h2>
              
              <div class="reviews-summary">
                <div class="rating-summary">
                  <div class="average-rating">{{ productRating }}</div>
                  <div class="rating-stars">
                    <span v-for="i in 5" :key="i" class="star" :class="{ filled: i <= Math.floor(productRating) }">★</span>
                  </div>
                  <div class="total-reviews">{{ product.views_count || 0 }} Vues</div>
                </div>
              </div>
              
              <div class="no-reviews">
                <p>Aucun avis client pour le moment. Soyez le premier à laisser un avis !</p>
              </div>
              
              <div class="write-review">
                <button class="write-review-btn" @click="$emit('openReviewForm')">
                  <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
                    <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path>
                  </svg>
                  Écrire un avis
                </button>
              </div>
            </div>
            
            <!-- Onglet Expédition et paiement 
            <div v-if="activeTab === 3" class="shipping-payment">
              <h2 class="section-title">Expédition & Paiement</h2>
              
              <div class="shipping-info-section">
                <h3 class="subsection-title">Informations d'expédition</h3>
                <div class="shipping-methods">
                  <div class="shipping-method-item">
                    <div class="method-name">Livraison Abidjan</div>
                    <div class="method-time">1-2 jours</div>
                    <div class="method-cost">1500 - 3000 FCFA</div>
                  </div>
                  <div class="shipping-method-item">
                    <div class="method-name">Livraison Intérieur</div>
                    <div class="method-time">3-7 jours</div>
                    <div class="method-cost">3000 - 8000 FCFA</div>
                  </div>
                </div>
              </div>
              
              <div class="payment-info-section">
                <h3 class="subsection-title">Méthodes de paiement</h3>
                <div class="payment-methods">
                  <div class="payment-method-item">
                    <div class="payment-name">Paiement à la livraison</div>
                  </div>
                  <div class="payment-method-item">
                    <div class="payment-name">Mobile Money</div>
                  </div>
                  <div class="payment-method-item">
                    <div class="payment-name">Virement bancaire</div>
                  </div>
                </div>
              </div>
            </div>
            -->
          </div>
        </div>
      </div>
    </div>
  </template>
  
  <script setup>
  import { ref, computed, defineProps, defineEmits } from 'vue'
  
  const props = defineProps({
    product: Object,
    productImages: Array,
    productRating: Number
  })
  
  const emit = defineEmits([
    'openImageModal',
    'openReviewForm'
  ])
  
  const activeTab = ref(0)
  
  const productTabs = [
    { label: "Spécifications" },
    { label: "Autres Spécifications" },
    
    { label: "Avis" }
  ]
  </script>
  
  <style scoped>
  .product-details-section {
    padding: 24px 0;
  }
  
  .container {
    max-width: 1500px;
    margin: 0 auto;
    padding: 0 16px;
  }
  
  .tabs-container {
    background: #fff;
    border-radius: 8px;
    padding: 24px;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
  }
  
  .tabs-header {
    display: flex;
    border-bottom: 2px solid #f0f0f0;
    margin-bottom: 24px;
  }
  
  .tab-button {
    padding: 12px 24px;
    border: none;
    background: transparent;
    font-size: 16px;
    font-weight: 500;
    color: #666;
    cursor: pointer;
    transition: all 0.3s ease;
    position: relative;
  }
  
  .tab-button:hover {
    color: #fe9700;
  }
  
  .tab-button.active {
    color: #fe9700;
    border: none;
    font-weight: 600;
  }
  
  .tab-button.active::after {
    content: '';
    position: absolute;
    bottom: -2px;
    left: 0;
    width: 100%;
    height: 2px;
    background: #fe9700;
  }
  
  .tab-content {
    min-height: 400px;
  }
  
  .section-title {
    font-size: 20px;
    font-weight: 600;
    color: #333;
    margin-bottom: 24px;
  }
  
  .description-content {
    line-height: 1.8;
    color: #666;
    margin-bottom: 24px;
  }
  
  .specifications-table {
    display: flex;
    gap: 24px;
  }
  
  
  .spec-group {
    flex: 1 1 0;
    min-width: 0;
    border: 1px solid #e8e8e8;
    border-radius: 8px;
    overflow: hidden;
  }
  
  .spec-group-title {
    padding: 12px 16px;
    background: #f5f5f5;
    font-size: 16px;
    font-weight: 600;
    color: #333;
    margin: 0;
  }
  
  .spec-row {
    display: flex;
    border-top: 1px solid #e8e8e8;
  }
  
  .spec-row:first-of-type {
    border-top: none;
  }
  
  .spec-name,
  .spec-value {
    padding: 12px 16px;
    font-size: 14px;
  }
  
  .spec-name {
    width: 40%;
    background: #fafafa;
    color: #666;
    border-right: 1px solid #e8e8e8;
  }
  
  .spec-value {
    width: 60%;
    color: #333;
  }
  
  .reviews-summary {
    display: flex;
    margin-bottom: 24px;
  }
  
  .rating-summary {
    display: flex;
    flex-direction: column;
    align-items: center;
    padding: 24px;
    background: #fafafa;
    border-radius: 8px;
  }
  
  .average-rating {
    font-size: 48px;
    font-weight: 700;
    color: #fe9700;
    line-height: 1;
    margin-bottom: 8px;
  }
  
  .rating-stars {
    margin-bottom: 8px;
  }
  
  .star {
    color: #d9d9d9;
    font-size: 16px;
  }
  
  .star.filled {
    color: #fe9700;
  }
  
  .total-reviews {
    font-size: 14px;
    color: #666;
  }
  
  .no-reviews {
    text-align: center;
    padding: 40px;
    color: #666;
  }
  
  .write-review {
    display: flex;
    justify-content: center;
    margin-top: 24px;
  }
  
  .write-review-btn {
    display: flex;
    align-items: center;
    gap: 8px;
    padding: 10px 20px;
    background: #fe9700;
    color: white;
    border: none;
    border-radius: 6px;
    font-size: 14px;
    font-weight: 500;
    cursor: pointer;
    transition: all 0.3s ease;
  }
  
  .write-review-btn:hover {
    background: #fe9800e6;
  }
  
  .shipping-info-section,
  .payment-info-section {
    margin-bottom: 24px;
  }
  
  .subsection-title {
    font-size: 18px;
    font-weight: 600;
    color: #333;
    margin-bottom: 16px;
  }
  
  .shipping-methods {
    display: flex;
    flex-direction: column;
    gap: 12px;
    margin-bottom: 16px;
  }
  
  .shipping-method-item {
    display: flex;
    align-items: center;
    padding: 16px;
    background: #fafafa;
    border-radius: 8px;
  }
  
  .method-name {
    flex: 1;
    font-size: 15px;
    font-weight: 500;
  }
  
  .method-time {
    margin: 0 16px;
    font-size: 14px;
    color: #666;
  }
  
  .method-cost {
    font-size: 15px;
    font-weight: 600;
    color: #fe9700;
  }
  
  .payment-methods {
    display: flex;
    flex-wrap: wrap;
    gap: 12px;
  }
  
  .payment-method-item {
    padding: 12px 16px;
    background: #fafafa;
    border-radius: 8px;
    font-size: 14px;
  }
  </style>
  