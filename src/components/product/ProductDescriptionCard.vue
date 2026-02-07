<template>
  <div class="product-details-section">
    <div class="container">
      <div class="tabs-container">
        <div class="tabs-header">
          <button 
            v-for="(tab, index) in productTabs" 
            :key="index"
            class="tab-button sm:mx-4 sm:py-2 px-1.5" 
            :class="{ active: activeTab === index }"
            @click="activeTab = index"
          >
            {{ tab.label }}
          </button>
        </div>
       
        <div class="tab-content">
          <!-- Onglet Overview -->
          <div v-if="activeTab === 0">
            <!-- Car Overview -->
            <div v-if="isCar" class="specifications-table grid grid-cols-1 sm:grid-cols-2 gap-6">
              <!-- Left Column for Cars -->
              <div class="spec-group">
                <h3 class="spec-group-title">Overview</h3>
                <div class="spec-row">
                  <div class="spec-name">Exterior Color</div>
                  <div class="spec-value">
                    <div v-if="product.colors && product.colors.length" class="flex items-center gap-2">
                      <span 
                        class="inline-block w-5 h-5 rounded-full border border-gray-300" 
                        :style="{ backgroundColor: product.colors[0]?.hex_value || '#FFFFFF' }"
                      ></span>
                      {{ product.colors[0]?.name || 'N/A' }}
                    </div>
                    <span v-else>{{ product.exterior_color || 'N/A' }}</span>
                  </div>
                </div>
                <div class="spec-row">
                  <div class="spec-name">Interior Color</div>
                  <div class="spec-value">{{ product.car_interior_color || 'N/A' }}</div>
                </div>
                <div class="spec-row">
                  <div class="spec-name">Mileage</div>
                  <div class="spec-value">
                    <span v-if="product.vehicle_mileage">{{ formatNumber(product.car_mileage) }} km</span>
                    <span v-else>N/A</span>
                  </div>
                </div>
                <div class="spec-row">
                  <div class="spec-name">MPG</div>
                  <div class="spec-value">{{ product.car_mpg_city || 'N/A' }}</div>
                </div>
              </div>

              <!-- Right Column for Cars -->
              <div class="spec-group">
                <h3 class="spec-group-title">Specifications</h3>
                <div class="spec-row">
                  <div class="spec-name">Transmission</div>
                  <div class="spec-value">{{ capitalizeFirst(product.car_transmission) || 'N/A' }}</div>
                </div>
                <div class="spec-row">
                  <div class="spec-name">Drivetrain</div>
                  <div class="spec-value">{{ product.car_drivetrain || product.drivetrain || 'N/A' }}</div>
                </div>
                <div class="spec-row">
                  <div class="spec-name">Engine</div>
                  <div class="spec-value">{{ product.car_engine_size || product.engine_number || 'N/A' }}</div>
                </div>
                <div class="spec-row">
                  <div class="spec-name">Fuel Type</div>
                  <div class="spec-value">{{ capitalizeFirst(product.car_fuel_type) || 'N/A' }}</div>
                </div>
              </div>
            </div>

            <!-- Trailer Overview -->
            <div v-else-if="isTrailer" class="specifications-table grid grid-cols-1 sm:grid-cols-2 gap-6">
              <!-- Left Column for Trailers -->
              <div class="spec-group">
                <h3 class="spec-group-title">Overview</h3>
                <div class="spec-row">
                  <div class="spec-name">Type</div>
                  <div class="spec-value">{{ product.trailer_type || 'N/A' }}</div>
                </div>
                <div class="spec-row">
                  <div class="spec-name">Brand Name</div>
                  <div class="spec-value">{{ product.vehicle_make || 'N/A' }}</div>
                </div>
                <div class="spec-row">
                  <div class="spec-name">Use</div>
                  <div class="spec-value">{{ product.trailer_use || 'N/A' }}</div>
                </div>
                <div class="spec-row">
                  <div class="spec-name">Size</div>
                  <div class="spec-value">{{ product.trailer_size || product.dimensions || 'N/A' }}</div>
                </div>
                <div class="spec-row">
                  <div class="spec-name">Axle</div>
                  <div class="spec-value">{{ product.trailer_axle || 'N/A' }}</div>
                </div>
                <div class="spec-row">
                  <div class="spec-name">Suspension</div>
                  <div class="spec-value">{{ product.trailer_suspension || 'N/A' }}</div>
                </div>
                <div class="spec-row">
                  <div class="spec-name">Tire</div>
                  <div class="spec-value">{{ product.trailer_tire || product.tyre_size || 'N/A' }}</div>
                </div>
                <div class="spec-row">
                  <div class="spec-name">King Pin</div>
                  <div class="spec-value">{{ product.trailer_king_pin || 'N/A' }}</div>
                </div>
                <div class="spec-row">
                  <div class="spec-name">Main Beam</div>
                  <div class="spec-value">{{ product.trailer_main_beam || 'N/A' }}</div>
                </div>
              </div>

              <!-- Right Column for Trailers -->
              <div class="spec-group">
                <h3 class="spec-group-title">Technical Specification</h3>
                <div class="spec-row">
                  <div class="spec-name">Max Payload</div>
                  <div class="spec-value">
                    <span v-if="product.trailer_max_payload">{{ formatNumber(product.trailer_max_payload) }} kg</span>
                    <span v-else>N/A</span>
                  </div>
                </div>
                <div class="spec-row">
                  <div class="spec-name">Place of Origin</div>
                  <div class="spec-value">{{ product.country_of_origin || 'N/A' }}</div>
                </div>
                <div class="spec-row">
                  <div class="spec-name">Material</div>
                  <div class="spec-value">{{ product.trailer_material || 'N/A' }}</div>
                </div>
                <div class="spec-row">
                  <div class="spec-name">Product Name</div>
                  <div class="spec-value">{{ product.name || 'N/A' }}</div>
                </div>
                <div class="spec-row">
                  <div class="spec-name">Function</div>
                  <div class="spec-value">{{ product.trailer_function || 'N/A' }}</div>
                </div>
                <div class="spec-row">
                  <div class="spec-name">Landing Gear</div>
                  <div class="spec-value">{{ product.trailer_landing_gear || 'N/A' }}</div>
                </div>
                <div class="spec-row">
                  <div class="spec-name">Color</div>
                  <div class="spec-value">
                    <div v-if="product.colors && product.colors.length">
                      <div v-for="color in product.colors" :key="color.id" class="flex items-center mb-1">
                        <span 
                          class="inline-block w-6 h-6 rounded-full border border-gray-300 mr-2" 
                          :style="{ backgroundColor: color.hex_value || '#FFFFFF' }"
                        ></span>
                        {{ color.name || 'N/A' }}
                      </div>
                    </div>
                    <span v-else>N/A</span>
                  </div>
                </div>
                <div class="spec-row">
                  <div class="spec-name">Axle Brand</div>
                  <div class="spec-value">{{ product.trailer_axle_brand || product.axle_brand || 'N/A' }}</div>
                </div>
              </div>
            </div>

            <!-- Truck Overview (default) -->
            <div v-else class="specifications-table grid grid-cols-1 sm:grid-cols-2 gap-6">
              <div class="spec-group">
                <!-- <h3 class="spec-group-title">Overview</h3> -->
                 <div class="spec-row">
                  <div class="spec-name">Brand</div>
                  <div class="spec-value">{{ product.vehicle_make || 'N/A' }}</div>
                </div>
                <div class="spec-row">
                  <div class="spec-name">Model</div>
                  <div class="spec-value">{{ product.vehicle_model || 'N/A' }}</div>
                </div>
                <div class="spec-row">
                  <div class="spec-name">Category</div>
                  <div class="spec-value">{{ product.category_name || 'N/A'}}</div>
                </div>
                <div class="spec-row">
                  <div class="spec-name">Vehicle Model (Trim)</div>
                  <div class="spec-value">
                    <ul v-if="product.trim_numbers && product.trim_numbers.length">
                      <li v-for="vin in product.trim_numbers" :key="vin.id">
                        {{ vin}}
                      </li>
                    </ul>
                    <span v-else>N/A</span>
                  </div>
                </div>
                <div class="spec-row">
                  <div class="spec-name">Drive Type</div>
                  <div class="spec-value">{{ product.drive_type || 'N/A'}}</div>
                </div>
                <div class="spec-row">
                  <div class="spec-name">Conditions</div>
                  <div class="spec-value">{{ capitalizeFirst(product.vehicle_condition) || 'N/A'}}</div>
                </div>
                
              </div>
              <div class="spec-group">
                <!-- <h3 class="spec-group-title">Specification</h3> -->
                <div class="spec-row">
                  <div class="spec-name">Mileage</div>
                  <div v-if="product.vehicle_mileage" class="spec-value">{{formatNumber(product.vehicle_mileage)}} km </div>
                  <div v-else class="spec-value"> N/A</div>
                </div>
                <div class="spec-row">
                  <div class="spec-name">VIN / Chassis NO.</div>
                  <div class="spec-value">
                    <ul v-if="product.vin_numbers && product.vin_numbers.length">
                      <li v-for="vin in product.vin_numbers" :key="vin.id">
                        {{ vin || 'N/A' }}
                      </li>
                    </ul>
                    <span v-else>N/A</span>
                  </div>
                </div>
                <div class="spec-row overflow-scroll">
                  <div class="spec-name">Colors</div>
                  <div v-for="color in product.colors" :key="color.id" >
                    <div class="spec-value">
                      <div class="pl-2 flex items-center">
                        <span 
                          class="inline-block w-6 h-6 rounded-full border border-gray-300 mr-2" 
                          :style="{ backgroundColor: color.hex_value || '#FFFFFF' }"
                        ></span>
                      </div>
                      {{ color.name || 'N/A' }}
                    </div>
                  </div>
                </div>
                <!-- <div class="spec-row">
                  <div class="spec-name">Production date</div>
                  <div class="spec-value">{{ product.production_date || 'N/A' }}</div>
                </div> -->
                <div class="spec-row">
                  <div class="spec-name">Year</div>
                  <div class="spec-value">{{ product.vehicle_year || 'N/A'}}</div>
                </div>
                <div class="spec-row">
                  <div class="spec-name">Country of Origin</div>
                  <div class="spec-value">{{ product.country_of_origin || 'N/A' }}</div>
                </div>
                <div class="spec-row">
                  <div class="spec-name">Stock Number</div>
                  <div class="spec-value">{{ product.stock_number || 'N/A' }}</div>
                </div>
                <!-- <div class="spec-row">
                  <div class="spec-name">Cabin Type</div>
                  <div class="spec-value">{{ product.cabin_type || 'N/A' }}</div>
                </div> -->
                <!-- <div class="spec-row">
                  <div class="spec-name">Wheelbase (mm)</div>
                  <div class="spec-value">{{ product.wheelbase || 'N/A' }}</div>
                </div> -->
                <!-- <div class="spec-row">
                  <div class="spec-name">Economic speed/Maximum speed (km/h)</div>
                  <div class="spec-value">{{ product.speed || 'N/A' }}</div>
                </div> -->
              </div>
            </div>
          </div>

          <!-- Onglet Feature -->
          <div v-if="activeTab === 1" class="product-features">
            <!-- Car Features - Wait for API -->
            <div v-if="isCar" class="wait-for-api">
              <div class="wait-message">
                <svg class="wait-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                  <circle cx="12" cy="12" r="10"></circle>
                  <polyline points="12,6 12,12 16,14"></polyline>
                </svg>
                <h3>Wait for API</h3>
                <p>Feature details for cars will be available soon.</p>
              </div>
            </div>

            <!-- Trailer Features -->
            <div v-else-if="isTrailer" class="specifications-table grid grid-cols-1 sm:grid-cols-2 gap-6">
              <div class="spec-group">
                <h3 class="spec-group-title">Additional Details</h3>
                <div class="spec-row">
                  <div class="spec-name">Max Payload</div>
                  <div class="spec-value">
                    <span v-if="product.trailer_max_payload">{{ formatNumber(product.trailer_max_payload) }} kg</span>
                    <span v-else>N/A</span>
                  </div>
                </div>
                <div class="spec-row">
                  <div class="spec-name">Material</div>
                  <div class="spec-value">{{ product.trailer_material || 'N/A' }}</div>
                </div>
                <div class="spec-row">
                  <div class="spec-name">Function</div>
                  <div class="spec-value">{{ product.trailer_function || 'N/A' }}</div>
                </div>
                <div class="spec-row">
                  <div class="spec-name">Landing Gear</div>
                  <div class="spec-value">{{ product.trailer_landing_gear || 'N/A' }}</div>
                </div>
              </div>
            </div>

            <!-- Truck Features (all technical specs) -->
            <div v-else class="specifications-table grid grid-cols-1 sm:grid-cols-2 gap-6">
              <div class="spec-group">
                <h3 class="spec-group-title">Engine</h3>
                <div class="spec-row">
                  <div class="spec-name">Brand</div>
                  <div class="spec-value">{{ capitalizeFirst(product.engine_brand) || 'N/A' }}</div>
                </div>
                <div class="spec-row">
                  <div class="spec-name">Model</div>
                  <div class="spec-value">{{ product.engine_number || 'N/A' }}</div>
                </div>
                <div class="spec-row">
                  <div class="spec-name">Horse Power (hp)</div>
                  <div v-if="product.power" class="spec-value">{{ product.power }}HP</div>
                  <div v-else class="spec-value">N/A</div>
                </div>
                <div class="spec-row">
                  <div class="spec-name">Emission standards</div>
                  <div class="spec-value">{{ product.engine_emissions || 'N/A' }}</div>
                </div>
              </div>

              <div class="spec-group">
                <h3 class="spec-group-title">Gearbox</h3>
                <div class="spec-row">
                  <div class="spec-name">Brand</div>
                  <div class="spec-value">{{ product.gearbox_brand || 'N/A' }}</div>
                </div>
                <div class="spec-row">
                  <div class="spec-name">Model</div>
                  <div class="spec-value">{{ product.gearbox_model || 'N/A' }}</div>
                </div>
                <div class="spec-row">
                  <div class="spec-name">Type</div>
                  <div class="spec-value">{{ capitalizeFirst(product.transmission_type) || 'N/A' }}</div>
                </div>
              </div>

              <div class="spec-group">
                <h3 class="spec-group-title">Vehicle Weight</h3>
                <div class="spec-row">
                  <div class="spec-name">Curb Weight (kg)</div>
                  <div v-if="product.curb_weight" class="spec-value">{{ formatNumber(product.curb_weight) }} kg</div>
                  <div v-else class="spec-value">N/A</div>
                </div>
                <div class="spec-row">
                  <div class="spec-name">Payload Capacity (kg)</div>
                  <div v-if="product.payload_capacity" class="spec-value">{{ formatNumber(product.payload_capacity) }} kg</div>
                  <div v-else class="spec-value">N/A</div>
                </div>
                <div class="spec-row">
                  <div class="spec-name">Gross Vehicle Weight (GVW) (kg)</div>
                  <div v-if="product.gvw" class="spec-value">≤{{ formatNumber(product.gvw) }} kg</div>
                  <div v-else class="spec-value">N/A</div>
                </div>
              </div>

              <div class="spec-group">
                <h3 class="spec-group-title">Dimensions (mm)</h3>
                <div class="spec-row">
                  <div class="spec-name">Chassis (mm)</div>
                  <div class="spec-value">{{ product.chassis_dimensions || 'N/A' }}</div>
                </div>
                <div class="spec-row">
                  <div class="spec-name">Frame rear suspension (mm)</div>
                  <div class="spec-value">{{ product.frame_rear_suspension || 'N/A' }}</div>
                </div>
                <div class="spec-row">
                  <div class="spec-name">Overall Dimensions LxlxH (mm)</div>
                  <div class="spec-value">{{ product.dimensions || 'N/A' }}</div>
                </div>
              </div>

              <div class="spec-group">
                <h3 class="spec-group-title">Suspension</h3>
                <div class="spec-row">
                  <div class="spec-name">Suspension Type</div>
                  <div class="spec-value">{{ product.suspension_type || 'N/A' }}</div>
                </div>
                <div class="spec-row">
                  <div class="spec-name">Front</div>
                  <div class="spec-value">{{ product.suspension_front || 'N/A' }}</div>
                </div>
                <div class="spec-row">
                  <div class="spec-name">Rear</div>
                  <div class="spec-value">{{ product.suspension_rear || 'N/A' }}</div>
                </div>
              </div>

              <div class="spec-group">
                <h3 class="spec-group-title">Axles</h3>
                <div class="spec-row">
                  <div class="spec-name">Brand</div>
                  <div class="spec-value">{{ product.axle_brand || 'N/A' }}</div>
                </div>
                <div class="spec-row">
                  <div class="spec-name">Front axle</div>
                  <div class="spec-value">{{ product.axle_front || 'N/A' }}</div>
                </div>
                <div class="spec-row">
                  <div class="spec-name">Rear axle</div>
                  <div class="spec-value">{{ product.axle_rear || 'N/A' }}</div>
                </div>
                <div class="spec-row">
                  <div class="spec-name">Speed ratio</div>
                  <div class="spec-value">{{ product.axle_speed_ratio || 'N/A' }}</div>
                </div>
              </div>

              <div class="spec-group">
                <h3 class="spec-group-title">Fuel Tank</h3>
                <div class="spec-row">
                  <div class="spec-name">Fuel Tank Capacity (L)</div>
                  <div v-if="product.fuel_tank_capacity" class="spec-value">{{ product.fuel_tank_capacity }}L</div>
                  <div v-else class="spec-value">N/A</div>
                </div>
                <div class="spec-row">
                  <div class="spec-name">Fuel Type</div>
                  <div class="spec-value">{{ capitalizeFirst(product.fuel_type) || 'N/A' }}</div>
                </div>
              </div>

              <div class="spec-group">
                <h3 class="spec-group-title">Brake System</h3>
                <div class="spec-row">
                  <div class="spec-name">Type</div>
                  <div class="spec-value">{{ product.brake_system || 'N/A' }}</div>
                </div>
              </div>

              <div class="spec-group">
                <h3 class="spec-group-title">Tires</h3>
                <div class="spec-row">
                  <div class="spec-name">Type</div>
                  <div class="spec-value">{{ product.tyre_size || 'N/A' }}</div>
                </div>
              </div>

              <div class="spec-group">
                <h3 class="spec-group-title">Air Filter</h3>
                <div class="spec-row">
                  <div class="spec-name">Type</div>
                  <div class="spec-value">{{ product.air_filter || 'N/A' }}</div>
                </div>
              </div>

              <div class="spec-group">
                <h3 class="spec-group-title">Electrics</h3>
                <div class="spec-row">
                  <div class="spec-name">Type</div>
                  <div class="spec-value">{{ product.electrics || 'N/A' }}</div>
                </div>
              </div>

              <div class="spec-group">
                <h3 class="spec-group-title">Cab</h3>
                <div class="spec-row">
                  <div v-if="product.cab" class="text-black font-medium text-sm p-2">{{ product.cab }}</div>
                  <div v-else class="spec-value">N/A</div>
                </div>
              </div>
            </div>
          </div>

          <!-- Onglet Seller Note -->
          <div v-if="activeTab === 2" class="product-description">
            <h2 class="section-title">Seller Note</h2>
            
            <div class="description-content">
              <p>{{ product.description_plus || 'No seller notes available for this product.'}}</p>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, defineProps, defineEmits, computed } from 'vue'
import { capitalizeFirst, formatNumber } from "../../composables/utils"

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

const isTrailer = computed(() => {
  return (
    props.product.category_name?.toLowerCase().includes('trailer') ||
    props.product.trailer_type ||
    props.product.trailer_axle ||
    props.product.trailer_use
  )
})

const isCar = computed(() => {
  return (
    props.product.category_name?.toLowerCase().includes('car') ||
    props.product.category_name?.toLowerCase().includes('voiture') ||
    props.product.category_name?.toLowerCase().includes('sedan') ||
    props.product.category_name?.toLowerCase().includes('suv') ||
    props.product.category_name?.toLowerCase().includes('hatchback') ||
    props.product.interior_color ||
    props.product.mpg
  )
})

const productTabs = [
  { label: "Overview" },
  { label: "Feature" },
  { label: "Seller Note" }
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
  width: 100%;
  border-bottom: 2px solid #f0f0f0;
  margin-bottom: 24px;
}

.tab-button {
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
  gap: 24px;
}

.spec-group {
  flex: 1 1 0;
  min-width: 0;
  border: 1px solid #e8e8e8;
  border-radius: 8px;
  overflow: hidden;
  align-items: center;
  justify-content: center;
}

.spec-group-title {
  display: flex;
  justify-content: center;
  align-items: center;
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
  display: flex;
  justify-content: center;
  align-items: center;
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

/* Wait for API styles */
.wait-for-api {
  display: flex;
  justify-content: center;
  align-items: center;
  min-height: 300px;
}

.wait-message {
  text-align: center;
  padding: 40px;
  background: #fafafa;
  border-radius: 12px;
  border: 1px dashed #e0e0e0;
}

.wait-icon {
  width: 48px;
  height: 48px;
  stroke: #fe9700;
  margin-bottom: 16px;
}

.wait-message h3 {
  font-size: 18px;
  font-weight: 600;
  color: #333;
  margin-bottom: 8px;
}

.wait-message p {
  font-size: 14px;
  color: #666;
}

.product-features {
  margin-top: 16px;
}
</style>
