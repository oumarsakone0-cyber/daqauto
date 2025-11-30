<template>
  <!-- Overlay avec arrière-plan sombre -->
  <div 
    class="fixed inset-0 bg-black/60 backdrop-blur-sm z-50 p-0 sm:p-4 sm:flex sm:items-center sm:justify-center"
    @click="handleBackdropClick"
  >
    <div 
      class="bg-white w-full h-screen sm:h-auto sm:max-h-[90vh] sm:max-w-4xl sm:rounded-2xl shadow-2xl overflow-hidden transform transition-all duration-300 ease-out sm:mx-auto"
      @click.stop
    >
      <!-- Animation de fond luxueuse -->
      <div class="absolute inset-0 overflow-hidden pointer-events-none sm:rounded-2xl " style="background-color: #ffffff00;">
        <div class="absolute -top-10 -right-10 w-32 h-32 bg-gradient-to-br from-orange-200/30 to-orange-300/20 rounded-full blur-2xl animate-pulse"></div>
        <div class="absolute -bottom-10 -left-10 w-40 h-40 bg-gradient-to-br from-blue-200/25 to-indigo-200/15 rounded-full blur-2xl animate-pulse" style="animation-delay: 1s;"></div>
      </div>

      <!-- Header fixe -->
      <div class="sticky top-0 z-10 bg-white/95 backdrop-blur-sm border-b border-gray-200 px-4 sm:px-6 py-4 sm:py-6 sm:rounded-t-2xl">
        <div class="flex items-center justify-between">
          <div class="flex items-center space-x-3">
            <div class="w-10 h-10  rounded-lg flex items-center justify-center bg-orange">
              <PackageIcon class="w-5 h-5 text-white" />
            </div>
            <div>
              <h2 class="text-xl sm:text-2xl font-bold text-gray-900">Product details</h2>
              <p class="text-sm text-gray-600 hidden sm:block">Full information and media</p>
            </div>
          </div>
         
            <XIcon @click="$emit('close')" class="w-7 h-7 text-gray-500 cursor-pointer" />
        </div>

        <!-- Onglets modernes -->
        <div class="mt-4 sm:mt-6">
          <nav class="flex space-x-1 bg-white rounded-xl p-1 shadow-sm">
            <!-- Ajout de l'onglet Spécifications Véhicule -->
            <button
              v-if="hasVehicleSpecs"
              @click="activeTab = 'vehicle'"
              :class="[
                'flex-1 flex items-center justify-center space-x-2 py-2 sm:py-3 px-2 sm:px-4 rounded-lg font-medium text-xs sm:text-sm transition-all duration-200',
                activeTab === 'vehicle'
                  ? '  btn-degrade-orange'
                  : 'btn-gray'
              ]"
            >
              <TruckIcon class="w-4 h-4 sm:w-5 sm:h-5" />
              <span class="hidden sm:inline">Specifications</span>
              <span class="sm:hidden">Specs</span>
            </button>
            <button
              @click="activeTab = 'details'"
              :class="[
                'flex-1 flex items-center justify-center space-x-2 py-2 sm:py-3 px-2 sm:px-4 rounded-lg font-medium text-xs sm:text-sm ',
                activeTab === 'details'
                  ? '  btn-degrade-orange'
                  : 'btn-gray'
              ]"
            >
              <InfoIcon class="w-4 h-4 sm:w-5 sm:h-5" />
              <span class="hidden sm:inline">Product details</span>
              <span class="sm:hidden">Details</span>
            </button>
            
            <button
              @click="activeTab = 'media'"
              :class="[
                'flex-1 flex items-center justify-center space-x-2 py-2 sm:py-3 px-2 sm:px-4 rounded-lg font-medium text-xs sm:text-sm transition-all duration-200',
                activeTab === 'media'
                  ? '  btn-degrade-orange'
                  : 'btn-gray'  
              ]"
            >
              <ImageIcon class="w-4 h-4 sm:w-5 sm:h-5" />
              <span class="hidden sm:inline">Gallery & Media</span>
              <span class="sm:hidden">Media</span>
              <span v-if="mediaCount > 0" class="bg-white text-black bg-opacity-20 text-xs px-1.5 sm:px-2 py-0.5 sm:py-1 rounded-full font-medium">
                {{ mediaCount }}
              </span>
            </button>
          </nav>
        </div>
      </div>

      <!-- Contenu scrollable -->
      <div class="overflow-y-auto h-[calc(100vh-140px)] sm:h-auto sm:max-h-[calc(95vh-200px)] px-4 sm:px-6 py-4 sm:py-6 relative z-5">
        <div v-if="product">
          <!-- Onglet 3: Spécifications Véhicule -->
          <div v-if="activeTab === 'vehicle'" class="space-y-4 sm:space-y-8">
            <!-- Informations générales du véhicule -->
            <div class="bg-white/80 backdrop-blur-sm rounded-xl p-4 sm:p-6 border border-gray-100 shadow-sm">
             <div class="grid grid-cols-2">
               <h4 class="text-lg sm:text-xl font-semibold text-gray-900 mb-4 flex items-center">
                 <TruckIcon class="w-4 h-4 sm:w-5 sm:h-5 mr-2 primary-color" />
                 Vehicle Specifications
               </h4>
               <apn class="text-gray-400">vehicle availability : 
                <div class="inline-flex">
                  <span v-if="product.disponibility === 'available'" class="text-green-600">Available</span>
                    <span v-else-if="product.disponibility === 'unavailable'" class="text-red-600">Unavailable</span>
                    <span v-else-if="product.disponibility === 'on_order'" class="text-orange-600"> Made to Order</span>
                    <span v-else>{{ product.disponibility }}</span>
               </div>
              </apn>
             </div>
              <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                <div  class="bg-gray-50 rounded-xl p-4">
                  <div class="text-sm text-gray-500 mb-1">Vehicle brand</div>
                  <div class="font-semibold text-gray-900">{{ product.vehicle_make || 'N/A' }} </div>
                </div>
                <div class="bg-gray-50 rounded-xl p-4">
                  <div class="text-sm text-gray-500 mb-1">Vehicle condition</div>
                  <div class="font-semibold text-gray-900 capitalize">
                    <span v-if="product.vehicle_condition === 'new'" class="text-green-600">New</span>
                    <span v-else-if="product.vehicle_condition === 'used'" class="text-red-600">Used</span>
                    <span v-else-if="product.vehicle_condition === 'refurbished'" class="text-orange-600">Refurbished</span>
                    <span v-else>{{ product.vehicle_condition || 'N/A'}}</span>
                  </div>
                </div>
                <div class="bg-gray-50 rounded-xl p-4">
                  <div class="text-sm text-gray-500 mb-1">Year</div>
                  <div class="font-semibold text-gray-900">{{ product.vehicle_year ||"N/A" }}</div>
                </div>
                <div class="bg-gray-50 rounded-xl p-4">
                  <div class="text-sm text-gray-500 mb-1">Vehicle model</div>
                  <div class="font-semibold text-gray-900">{{ product.vehicle_model ||"N/A"}}</div>
                </div>
                <div  class="bg-gray-50 rounded-xl p-4">
                  <div class="text-sm text-gray-500 mb-1">Mileage</div>
                  <div class="font-semibold text-gray-900">
                    {{ formatMileage(product.vehicle_mileage)  ||"N/A" }}
                  </div>
                </div>
                 <div class="bg-gray-50 rounded-xl p-4">
                  <div class="text-sm text-gray-500 mb-1">Production date</div>
                  <div class="font-semibold text-gray-900">
                    {{product.production_date || "N/A"}}
                  </div>
                </div>
                <div class="bg-gray-50 rounded-xl p-4">
                  <div class="text-sm text-gray-500 mb-1">Categorie</div>
                  <div class="font-semibold text-gray-900">
                    {{product.category_name  ||"N/A"}}
                  </div>
                </div>
               
                <div class="bg-gray-50 rounded-xl p-4">
                  <div class="text-sm text-gray-500 mb-1">Country of origin</div>
                  <div class="font-semibold text-gray-900">
                    {{product.country_of_origin || "N/A"}}
                  </div>
                </div>
                <div class="bg-gray-50 rounded-xl p-4">
                  <div class="text-sm text-gray-500 mb-1">Available colors</div>
                  <div class=" flex font-semibold text-gray-900 gaps-2">
                    <div v-if="product.colors.length !== 0" v-for="color in product.colors" :key="color.id" >
                        <div  class="px-2">
                          <div>
                            <span 
                              class="inline-block w-6 h-6 rounded-full border border-gray-300 mr-2" 
                              :style="{ backgroundColor: color || '#FFFFFF' }"
                            ></span>
                          </div>
                        </div>
                    </div>
                    <span v-else class="text-gray-500">N/A</span>
                  </div>
                </div>
                <div class="bg-gray-50 rounded-xl p-4">
                  <div class="text-sm text-gray-500 mb-1">Fuel type</div>
                  <div class="font-semibold text-gray-900">
                    {{capitalizeFirst(product.fuel_type) || "N/A"}}
                  </div>
                </div>
                <div class="bg-gray-50 rounded-xl p-4">
                  <div class="text-sm text-gray-500 mb-1">Fuel tank capacity (L) </div>
                  <div class="font-semibold text-gray-900">
                    {{product.fuel_tank_capacity || "N/A"}}L
                  </div>
                </div>
                
              </div>
            </div>

            <!-- Caractéristiques techniques -->
            <div class="bg-white/80 backdrop-blur-sm rounded-xl p-4 sm:p-6 border border-gray-100 shadow-sm ">
              <h4 class="text-lg sm:text-xl font-semibold text-gray-900 mb-4 flex items-center">
                <SettingsIcon class="w-4 h-4 sm:w-5 sm:h-5 mr-2 primary-color" />
                Technical Specifications
              </h4>
              <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                <div v-if="product.drive_type" class="bg-gray-50 rounded-xl p-4">
                  <div class="text-sm text-gray-500 mb-1">Stock number</div>
                  <div class="font-semibold text-gray-900">{{ product.stock_number }}</div>
                </div>
                <div v-if="product.drive_type" class="bg-gray-50 rounded-xl p-4">
                  <div class="text-sm text-gray-500 mb-1">Transmission type</div>
                  <div class="font-semibold text-gray-900">{{ product.drive_type }}</div>
                </div>
                <div v-if="product.fuel_type" class="bg-gray-50 rounded-xl p-4">
                  <div class="text-sm text-gray-500 mb-1">Fuel type</div>
                  <div class="font-semibold text-gray-900 capitalize flex items-center">
                    <div class="w-3 h-3 rounded-full mr-2" :class="getFuelTypeColor(product.fuel_type)"></div>
                    {{ getFuelTypeLabel(product.fuel_type) }}
                  </div>
                </div>
                <div v-if="product.transmission_type" class="bg-gray-50 rounded-xl p-4">
                  <div class="text-sm text-gray-500 mb-1">Gearbox</div>
                  <div class="font-semibold text-gray-900 capitalize">
                    {{ product.transmission_type === 'automatic' ? 'Automatique' : 'Manuelle' }}
                  </div>
                </div>
                <div v-if="product.engine_brand" class="bg-gray-50 rounded-xl p-4">
                  <div class="text-sm text-gray-500 mb-1">Engine brand</div>
                  <div class="font-semibold text-gray-900 uppercase">{{ product.engine_brand }}</div>
                </div>
                <div class="bg-gray-50 rounded-xl p-4">
                  <div class="text-sm text-gray-500 mb-1">Engine power</div>
                  <div class="font-semibold text-gray-900 uppercase">{{ product.power || "N/A"}}</div>
                </div>
                <div class="bg-gray-50 rounded-xl p-4">
                  <div class="text-sm text-gray-500 mb-1">Engine number</div>
                  <div class="font-semibold text-gray-900 uppercase">{{ product.engine_number || "N/A"}}</div>
                </div>
                
                <div class="bg-gray-50 rounded-xl p-4">
                  <div class="text-sm text-gray-500 mb-1">VIN numbers / Chassis</div>
                  <div class="font-semibold text-gray-900 uppercase">
                    <ul v-if="product.vin_numbers && product.vin_numbers.length">
                          <li v-for="vin in product.vin_numbers" :key="vin">
                            {{ vin }}
                          </li>
                        </ul>
                        <span v-else>N/A</span>
                  </div>
                </div>
                <div class="bg-gray-50 rounded-xl p-4">
                  <div class="text-sm text-gray-500 mb-1">Trim numbers</div>
                  <div class="font-semibold text-gray-900 uppercase">
                    <ul v-if="product.trim_numbers && product.trim_numbers.length">
                          <li v-for="trim_number in product.trim_numbers" :key="trim_number">                          
                            {{ trim_number , console.log(product.trim_numbers)}}
                          </li>
                        </ul>
                        <span v-else>N/A</span>
                  </div>
                </div>
                <div class="bg-gray-50 rounded-xl p-4">
                  <div class="text-sm text-gray-500 mb-1">Engine emissions</div>
                  <div class="font-semibold text-gray-900 uppercase">{{ product.engine_emissions || "N/A"}}</div>
                </div>
                <div class="bg-gray-50 rounded-xl p-4">
                  <div class="text-sm text-gray-500 mb-1">Wheelbase</div>
                  <div class="font-semibold text-gray-900">
                    {{product.wheelbase || "N/A"}}
                  </div>
                </div>
                <div class="bg-gray-50 rounded-xl p-4">
                  <div class="text-sm text-gray-500 mb-1">GVW - Gross Vehicle Weight (kg)</div>
                  <div class="font-semibold text-gray-900">
                    {{product.gvw || "N/A"}}
                  </div>
              </div>
                <div class="bg-gray-50 rounded-xl p-4">
                  <div class="text-sm text-gray-500 mb-1">Payload capacity (kg)</div>
                  <div class="font-semibold text-gray-900">
                    {{product.payload_capacity || "N/A"}}
                  </div>
              </div>
                <div class="bg-gray-50 rounded-xl p-4">
                  <div class="text-sm text-gray-500 mb-1">Cabin type</div>
                  <div class="font-semibold text-gray-900">
                    {{product.cabin_type || "N/A"}}
                  </div>
              </div>
                <div class="bg-gray-50 rounded-xl p-4">
                  <div class="text-sm text-gray-500 mb-1">Suspension type</div>
                  <div class="font-semibold text-gray-900">
                    {{product.suspension_type || "N/A"}}
                  </div>
              </div>
                <div class="bg-gray-50 rounded-xl p-4">
                  <div class="text-sm text-gray-500 mb-1">Curb weight (T)</div>
                  <div class="font-semibold text-gray-900">
                    {{product.curb_weight || "N/A"}}
                  </div>
              </div>
                <div class="bg-gray-50 rounded-xl p-4">
                  <div class="text-sm text-gray-500 mb-1">Fuel tank capacity (L)</div>
                  <div class="font-semibold text-gray-900">
                    {{product.fuel_tank_capacity || "N/A"}}
                  </div>
              </div>
                <div class="bg-gray-50 rounded-xl p-4">
                  <div class="text-sm text-gray-500 mb-1">Brake system</div>
                  <div class="font-semibold text-gray-900">
                    {{product.brake_system || "N/A"}}
                  </div>
              </div>
                <div class="bg-gray-50 rounded-xl p-4">
                  <div class="text-sm text-gray-500 mb-1">Dimensions</div>
                  <div class="font-semibold text-gray-900">
                    {{product.dimensions || "N/A"}}
                  </div>
                </div>
                <div class="bg-gray-50 rounded-xl p-4">
                  <div class="text-sm text-gray-500 mb-1">Tire sizes</div>
                  <div class="font-semibold text-gray-900">
                    {{product.tyre_size || "N/A"}}
                  </div>
                </div>
                <div class="bg-gray-50 rounded-xl p-4">
                  <div class="text-sm text-gray-500 mb-1">Unit</div>
                  <div class="font-semibold text-gray-900">
                    {{product.unit_type || "N/A"}}
                  </div>
                </div>
                
              </div>
            </div>

            <!-- Spécifications avancées -->
            <div v-if="hasAdvancedSpecs" class="bg-white/80 backdrop-blur-sm rounded-xl p-4 sm:p-6 border border-gray-100 shadow-sm mb-25">
              <h4 class="text-lg sm:text-xl font-semibold text-gray-900 mb-4 flex items-center">
                <CogIcon class="w-4 h-4 sm:w-5 sm:h-5 mr-2 primary-color" />
                Advanced Specifications
              </h4>
              <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Graphique de performance (simulé) -->
                <div class="bg-gradient-to-br from-blue-50 to-indigo-50 rounded-xl p-6">
                  <h5 class="font-semibold text-gray-900 mb-4">Performance</h5>
                  <div class="space-y-3">
                    <div class="flex justify-between items-center">
                      <span class="text-sm text-gray-600">Energy efficiency</span>
                      <div class="flex items-center space-x-2">
                        <div class="w-20 h-2 bg-gray-200 rounded-full overflow-hidden">
                          <div class="h-full bg-green-500 rounded-full" :style="{ width: getEfficiencyPercentage() + '%' }"></div>
                        </div>
                        <span class="text-sm font-medium text-gray-900">{{ getEfficiencyPercentage() }}%</span>
                      </div>
                    </div>
                    <div class="flex justify-between items-center">
                      <span class="text-sm text-gray-600">Durability</span>
                      <div class="flex items-center space-x-2">
                        <div class="w-20 h-2 bg-gray-200 rounded-full overflow-hidden">
                          <div class="h-full bg-blue-500 rounded-full" :style="{ width: getDurabilityPercentage() + '%' }"></div>
                        </div>
                        <span class="text-sm font-medium text-gray-900">{{ getDurabilityPercentage() }}%</span>
                      </div>
                    </div>
                  </div>
                </div>

                <!-- Résumé des caractéristiques -->
                <div class="bg-gradient-to-br from-orange-50 to-red-50 rounded-xl p-6">
                  <h5 class="font-semibold text-gray-900 mb-4">Summary</h5>
                  <div class="space-y-2 text-sm">
                    <div class="flex justify-between">
                      <span class="text-gray-600">Vehicle age</span>
                      <span class="font-medium">{{ getVehicleAge() }}</span>
                    </div>
                    <div class="flex justify-between">
                      <span class="text-gray-600">Category</span>
                      <span class="font-medium">{{ getVehicleCategory() }}</span>
                    </div>
                    <div class="flex justify-between">
                      <span class="text-gray-600">Recommended usage</span>
                      <span class="font-medium">{{ getRecommendedUsage() }}</span>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <!-- Message si aucune spécification véhicule -->
            <div v-if="!hasVehicleSpecs" class="text-center py-12 sm:py-16">
              <div class="w-16 h-16 sm:w-24 sm:h-24 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4 sm:mb-6">
                <TruckIcon class="w-8 h-8 sm:w-12 sm:h-12 primary-color" />
              </div>
              <h3 class="text-lg sm:text-xl font-semibold text-gray-900 mb-2">No vehicle specifications</h3>
              <p class="text-gray-500 max-w-md mx-auto text-sm sm:text-base px-4">This product has no associated vehicle specifications.</p>
            </div>
          </div>
          <!-- Onglet 1: Détails du produit -->
          <div v-else-if="activeTab === 'details'" class="space-y-4 sm:space-y-8 mb-25">
            <!-- Hero Section avec image principale -->
            <div class="bg-white/80 backdrop-blur-sm rounded-xl p-4 sm:p-8 border border-gray-100 shadow-sm">
              <div class="flex flex-col lg:flex-row items-start space-y-4 sm:space-y-6 lg:space-y-0 lg:space-x-8">
                <!-- Image principale -->
                <div class="w-full lg:w-80 flex-shrink-0">
                  <div class="relative group">
                    <div class="w-full h-60 sm:h-80 bg-white rounded-2xl shadow-lg overflow-hidden border border-gray-200">
                      <img 
                        v-if="primaryImage" 
                        :src="primaryImage" 
                        :alt="product.name" 
                        class="w-full h-full object-cover cursor-pointer transition-transform duration-300 group-hover:scale-105"
                        @click="openImageModal"
                      >
                      <div v-else class="w-full h-full flex flex-col items-center justify-center text-gray-400">
                        <ImageIcon class="w-12 h-12 sm:w-16 sm:h-16 mb-4 primary-color" />
                        <span class="text-sm sm:text-lg font-medium">No image</span>
                      </div>
                    </div>
                    <!-- Badge image count -->
                    <div v-if="product.images && product.images.length > 1" class="absolute -top-2 -right-2 sm:-top-3 sm:-right-3 bg-orange  text-xs sm:text-sm px-2 sm:px-3 py-1 rounded-full font-medium shadow-lg">
                      +{{ product.images.length - 1 }}
                    </div>
                  </div>
                </div>

                <!-- Informations principales -->
                <div class="flex-1 space-y-4 sm:space-y-6">
                  <div>
                    <h3 class="text-2xl sm:text-3xl font-bold text-gray-900 mb-3">{{ product.name }}</h3>
                    <div class="flex flex-col sm:flex-row sm:items-center space-y-2 sm:space-y-0 sm:space-x-4 mb-4">
                      <span class="text-sm sm:text-lg text-gray-600 bg-gray-100 px-3 py-1 rounded-lg font-mono">{{ product.sku }}</span>
                      <span :class="getStatusBadgeClass(product.status)" class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium w-fit">
                        <div class="w-2 h-2 rounded-full mr-2" :class="getStatusDotClass(product.status)"></div>
                        {{ product.status }}
                      </span>
                    </div>
                  </div>

                  <!-- Prix et stock -->
                  <div class="bg-white rounded-2xl p-4 sm:p-6 shadow-sm border border-gray-100">
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 sm:gap-6">
                      <div>
                        <h4 class="text-sm font-medium text-gray-500 mb-2">Selling price</h4>
                        <div class="text-2xl sm:text-3xl font-bold primary-color">
                          {{ formatPrice(product.unit_price) }} 
                        </div>
                        <div v-if="product.wholesale_price" class="mt-2 text-gray-600">
                          <span class="text-sm">Wholesale price: </span>
                          <span class="font-semibold">{{ formatPrice(product.wholesale_price) }} </span>
                          <span class="text-sm text-gray-500"> ({{ product.wholesale_min_qty }}+ units)</span>
                        </div>
                      </div>
                      <div>
                        <h4 class="text-sm font-medium text-gray-500 mb-2">Stock available</h4>
                        <div class="flex items-center space-x-3">
                          <span class="text-2xl sm:text-3xl font-bold text-gray-900">{{ product.stock }}</span>
                          <div>
                            <div class="text-sm text-gray-500">units</div>
                            <div :class="getStockStatusClass(product.stock)" class="text-sm font-medium">
                              {{ getStockStatus(product.stock) }}
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>

                  <!-- Performance -->
                  <div class="grid grid-cols-2 gap-3 sm:gap-4">
                    <div class="bg-blue-50 rounded-xl p-3 sm:p-4 text-center">
                      <div class="text-xl sm:text-2xl font-bold primary-color">{{ product.sales_count || 0 }}</div>
                      <div class="text-xs sm:text-sm primary-color font-medium">Sales</div>
                    </div>
                    <div class="bg-purple-50 rounded-xl p-3 sm:p-4 text-center">
                      <div class="text-xl sm:text-2xl font-bold primary-color">{{ product.views_count || 0 }}</div>
                      <div class="text-xs sm:text-sm primary-color font-medium">Views</div>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <!-- Description -->
            <div v-if="product.description" class="bg-white/80 backdrop-blur-sm rounded-xl p-4 sm:p-6 border border-gray-100 shadow-sm">
              <h4 class="text-lg sm:text-xl font-semibold text-gray-900 mb-4 flex items-center">
                <FileTextIcon class="w-4 h-4 sm:w-5 sm:h-5 mr-2 primary-color" />
                Description
              </h4>
              <p class="text-gray-700 leading-relaxed text-sm sm:text-lg">{{ product.description }}</p>
              <br/>
              <div v-if="product.description_plus">
                <h4 class="text-lg sm:text-xl font-semibold text-gray-900 mb-4 flex items-center">Other Description</h4>
                              
                    <div class="text-gray-700 leading-relaxed text-sm sm:text-lg">
                       <p>{{ product.description_plus }}</p>
                    </div>
              </div>
             
              
            </div>

            <!-- Grille d'informations -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 sm:gap-6">
              <!-- Catégorie -->
              <div class="bg-white/80 backdrop-blur-sm rounded-xl p-4 sm:p-6 border border-gray-100 shadow-sm">
                <h4 class="text-lg sm:text-xl font-semibold text-gray-900 mb-4 flex items-center">
                  <TagIcon class="w-4 h-4 sm:w-5 sm:h-5 mr-2 primary-color" />
                  Category
                </h4>
                <div class="space-y-3">
                  <div class="flex items-center justify-between p-3 bg-gray-50 rounded-xl">
                    <span class="text-gray-600 text-sm sm:text-base">Main category</span>
                    <span class="font-semibold text-gray-900 text-sm sm:text-base">{{ product.category_name || 'Non définie' }}</span>
                  </div>
                  <div class="flex items-center justify-between p-3 bg-gray-50 rounded-xl">
                    <span class="text-gray-600 text-sm sm:text-base">Subcategory</span>
                    <span class="font-semibold text-gray-900 text-sm sm:text-base">{{ product.subcategory_name || 'Non définie' }}</span>
                  </div>
                </div>
              </div>

              <!-- Informations système -->
              <div class="bg-white/80 backdrop-blur-sm rounded-xl p-4 sm:p-6 border border-gray-100 shadow-sm">
                <h4 class="text-lg sm:text-xl font-semibold text-gray-900 mb-4 flex items-center">
                  <InfoIcon class="w-4 h-4 sm:w-5 sm:h-5 mr-2 primary-color" />
                  Informations
                </h4>
                <div class="space-y-3">
                  <div class="flex items-center justify-between p-3 bg-gray-50 rounded-xl">
                    <span class="text-gray-600 text-sm sm:text-base">Created on</span>
                    <span class="font-semibold text-gray-900 text-sm sm:text-base">{{ formatDate(product.created_at) }}</span>
                  </div>
                  <div class="flex items-center justify-between p-3 bg-gray-50 rounded-xl">
                    <span class="text-gray-600 text-sm sm:text-base">Edited on</span>
                    <span class="font-semibold text-gray-900 text-sm sm:text-base">{{ formatDate(product.updated_at) }}</span>
                  </div>
                  <div class="flex items-center justify-between p-3 bg-gray-50 rounded-xl">
                    <span class="text-gray-600 text-sm sm:text-base">Created by</span>
                    <span class="font-semibold text-gray-900 text-sm sm:text-base">{{ product.created_by_name || 'Unknown' }}</span>
                  </div>
                </div>
              </div>
            </div>

            <!-- Couleurs et tailles -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 sm:gap-6">
              <!-- Couleurs -->
              <div v-if="product.colors && product.colors.length > 0" class="bg-white/80 backdrop-blur-sm rounded-xl p-4 sm:p-6 border border-gray-100 shadow-sm">
                <h4 class="text-lg sm:text-xl font-semibold text-gray-900 mb-4 flex items-center">
                  <PaletteIcon class="w-4 h-4 sm:w-5 sm:h-5 mr-2 primary-color" />
                  Available colors
                  <span class="ml-2 text-sm bg-gray-100 text-gray-600 px-2 py-1 rounded-full">{{ product.colors.length }}</span>
                </h4>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                  <div 
                    v-for="(color, index) in product.colors" 
                    :key="index" 
                    class="flex items-center space-x-3 p-3 bg-gray-50 rounded-xl hover:bg-gray-100 transition-colors"
                  >
                    <div 
                      class="w-6 h-6 sm:w-8 sm:h-8 rounded-xl border-2 border-white shadow-lg flex-shrink-0" 
                      :style="{ backgroundColor: color }"
                    ></div>
                    <span class="text-sm font-medium text-gray-700 truncate">{{ getColorName(color) }}</span>
                  </div>
                </div>
              </div>
            </div>

            <!-- Tags -->
            <div  class="bg-white/80 backdrop-blur-sm rounded-xl p-4 sm:p-6 border border-gray-100 shadow-sm">
              <h4 class="text-lg sm:text-xl font-semibold text-gray-900 mb-4 flex items-center">
                <TagIcon class="w-4 h-4 sm:w-5 sm:h-5 mr-2 primary-color" />
                Tags
              </h4>
              <div class="flex flex-wrap gap-2">
                <span 
                v-if="product.tags"
                  v-for="(tag, index) in getTagsArray(product.tags)" 
                  :key="index"
                  class="px-3 py-2 bg-blue-100 primary-color text-sm rounded-xl font-medium hover:bg-blue-200 transition-colors"
                >
                  {{ tag }}
                </span>
                <span v-else class="px-3 py-2 bg-blue-100 primary-color text-sm rounded-xl font-medium transition-colors" >
                  No tags available</span>
              </div>
            </div>
          </div>

          <!-- Onglet 2: Galerie et médias -->
          <div v-else-if="activeTab === 'media'" class="space-y-4 sm:space-y-8 mb-30">
            <!-- Galerie d'images -->
            <div v-if="product.all_images && product.all_images.length > 0" class="bg-white/80 backdrop-blur-sm rounded-xl p-4 sm:p-6 border border-gray-100 shadow-sm">
              <div class="flex items-center justify-between mb-4 sm:mb-6">
                <h4 class="text-lg sm:text-xl font-semibold text-gray-900 flex items-center">
                  <ImageIcon class="w-4 h-4 sm:w-5 sm:h-5 mr-2 primary-color" />
                  Image gallery
                </h4>
                <span class="text-sm bg-gray-100 text-gray-600 px-3 py-1 rounded-full font-medium">
                  {{ product.all_images.length }} image{{ product.all_images.length > 1 ? 's' : '' }}
                </span>
              </div>
              
              <!-- Image principale de la galerie -->
              <div class="mb-6 sm:mb-8">
                <div class="relative w-full h-64 sm:h-96 bg-gray-100 rounded-2xl overflow-hidden shadow-lg">
                  <img 
                    :src="selectedImage" 
                    :alt="product.name" 
                    class="w-full h-full object-cover cursor-pointer transition-transform duration-300 hover:scale-105"
                    @click="openImageModal"
                  >
                  <!-- Boutons de navigation -->
                  <button 
                    v-if="product.all_images.length > 1"
                    @click="previousImage"
                    class="absolute left-2 sm:left-4 top-1/2 transform -translate-y-1/2 bg-black bg-opacity-50 text-white p-2 sm:p-3 rounded-full hover:bg-opacity-70 transition-all backdrop-blur-sm"
                  >
                    <ChevronLeftIcon class="w-4 h-4 sm:w-6 sm:h-6" />
                  </button>
                  <button 
                    v-if="product.all_images.length > 1"
                    @click="nextImage"
                    class="absolute right-2 sm:right-4 top-1/2 transform -translate-y-1/2 bg-black bg-opacity-50 text-white p-2 sm:p-3 rounded-full hover:bg-opacity-70 transition-all backdrop-blur-sm"
                  >
                    <ChevronRightIcon class="w-4 h-4 sm:w-6 sm:h-6" />
                  </button>
                  <!-- Indicateur de position -->
                  <div class="absolute bottom-2 sm:bottom-4 right-2 sm:right-4 bg-black bg-opacity-50 text-white px-2 sm:px-3 py-1 rounded-full text-xs sm:text-sm font-medium backdrop-blur-sm">
                    {{ currentImageIndex + 1 }} / {{ product.all_images.length }}
                  </div>
                  <!-- Badge image principale -->
                  <div v-if="currentImageIndex === 0" class="absolute top-2 sm:top-4 left-2 sm:left-4 primary-color text-white px-2 sm:px-3 py-1 rounded-full text-xs sm:text-sm font-medium shadow-lg">
                    Main image
                  </div>
                </div>
              </div>
              
              <!-- Grille de miniatures -->
              <div class="grid grid-cols-4 sm:grid-cols-6 md:grid-cols-8 lg:grid-cols-10 gap-2 sm:gap-3">
                <div 
                  v-for="(image, index) in product.all_images" 
                  :key="index"
                  class="relative cursor-pointer group"
                  @click="selectImage(index)"
                >
                  <img 
                    :src="image" 
                    :alt="`${product.name} - Image ${index + 1}`" 
                    :class="[
                      'w-full h-16 sm:h-20 object-cover rounded-xl border-2 transition-all duration-200',
                      currentImageIndex === index 
                        ? 'border-orange-500 shadow-lg ring-2 ring-orange-500 ring-opacity-50 scale-105' 
                        : 'border-gray-200 hover:border-gray-300 group-hover:shadow-md group-hover:scale-105'
                    ]"
                  >
                  <!-- Badge pour l'image principale -->
                  <div 
                    v-if="index === 0" 
                    class="absolute -top-1 -right-1 sm:-top-2 sm:-right-2 bg-orange text-white text-xs px-1.5 py-0.5 sm:px-2 sm:py-1 rounded-full font-medium shadow-lg"
                  >
                    
                  </div>
                  <!-- Overlay au survol -->
                  <div class="absolute inset-0 group-hover:bg-opacity-20 transition-all rounded-xl"></div>
                </div>
              </div>
            </div>

            <!-- Vidéo du produit -->
            <div v-if="product.all_images?.length || product.video_url"  class="bg-white/80 backdrop-blur-sm rounded-xl p-4 sm:p-6 border border-gray-100 shadow-sm">
              <div class="flex items-center justify-between mb-4 sm:mb-6">
                <h4 class="text-lg sm:text-xl font-semibold text-gray-900 flex items-center">
                  <VideoIcon class="w-4 h-4 sm:w-5 sm:h-5 mr-2 primary-color" />
                  Product video
                </h4>
                <span class="text-sm bg-gray-100 text-gray-600 px-3 py-1 rounded-full font-medium">{{ product.video_url }} video</span>
              </div>
              <div v-if="product.video_url" class="relative w-full h-64 sm:h-96 bg-gray-100 rounded-2xl overflow-hidden shadow-lg">
                <video 
                  :src="product.video_url" 
                  controls 
                  class="w-full h-full object-cover"
                  preload="metadata"
                >
                  Your browser does not support video playback.
                </video>
              </div>   
              <div v-else class="w-full h-64 sm:h-96 flex flex-col items-center justify-center bg-gray-100 rounded-2xl shadow-lg text-gray-400">
                <VideoIcon class="w-12 h-12 sm:w-16 sm:h-16 mb-4 primary-color" />
                <span class="text-sm sm:text-lg font-medium text-gray-500">No video available</span>
            </div>
            </div>

            <!-- Message si aucun média -->
            <div v-if="!product.all_images?.length && !product.video_url" class="text-center py-12 sm:py-16">
              <div class="w-16 h-16 sm:w-24 sm:h-24 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4 sm:mb-6">
                <ImageIcon class="w-8 h-8 sm:w-12 sm:h-12 primary-color" />
              </div>
              <h3 class="text-lg sm:text-xl font-semibold text-gray-900 mb-2">No media available</h3>
              <p class="text-gray-500 max-w-md mx-auto text-sm sm:text-base px-4">This product has no associated images or videos. You can add them by editing the product.</p>
            </div>
          </div>

          
        </div>
      </div>

      <!-- Actions modernes -->
      <div class="sticky bottom-0 bg-white/95 backdrop-blur-sm border-t border-gray-200 px-4 sm:px-6 py-4 sm:rounded-b-2xl z-50">
        <div class="flex flex-col sm:flex-row gap-3 sm:gap-0 sm:justify-between sm:items-center">
          <div class="flex gap-2 sm:gap-3 order-2 sm:order-1">
            <button
              @click="editProduct(product)"
              class="flex-1 btn-degrade-orange"
            >
              <EditIcon class="w-4 h-4 " />
              Edit
            </button>
            <button
              @click="$emit('duplicate', product)"
              class="flex-1 btn-gray"
            >
              <CopyIcon class="w-4 h-4" />
              Duplicate
            </button>
            <router-link to="/dashboard-admin/factures">
              <button 
                class="flex-1 btn-gray"
              >
                <!-- utilisation directe du composant importé -->
                <FileTextIcon class="w-4 h-4 " />
                Proforma invoice
              </button>
            </router-link>
          </div>

          <div class="flex gap-2 sm:gap-3 order-1 sm:order-2">
            <button
              @click="$emit('delete', product)"
              class="flex-1  btn-deconnexion "
            >
              <TrashIcon class="w-4 h-4" />
              Delete
            </button>
            <button
              @click="$emit('close')"
              class="flex-1 btn-gray"
            >
              Close
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- Modal pour affichage plein écran de l'image -->
    <div 
      v-if="showImageModal" 
      class="fixed inset-0 bg-black bg-opacity-95 flex items-center justify-center z-[60] p-4"
      @click="closeImageModal"
    >
      <div class="relative max-w-7xl max-h-full">
        <img 
          :src="modalImage" 
          :alt="product.name" 
          class="max-w-full max-h-full object-contain rounded-2xl shadow-2xl"
        >
        <button 
          @click="closeImageModal"
          class="absolute top-2 right-2 sm:top-4 sm:right-4 text-white bg-black bg-opacity-50 p-2 sm:p-3 rounded-full hover:bg-opacity-70 transition-all backdrop-blur-sm"
        >
          <XIcon class="w-4 h-4 sm:w-6 sm:h-6" />
        </button>
        <!-- Navigation dans le modal -->
        <button 
          v-if="product.images && product.images.length > 1"
          @click.stop="previousImage"
          class="absolute left-2 sm:left-4 top-1/2 transform -translate-y-1/2 text-white bg-black bg-opacity-50 p-2 sm:p-3 rounded-full hover:bg-opacity-70 transition-all backdrop-blur-sm"
        >
          <ChevronLeftIcon class="w-4 h-4 sm:w-6 sm:h-6" />
        </button>
        <button 
          v-if="product.images && product.images.length > 1"
          @click.stop="nextImage"
          class="absolute right-2 sm:right-4 top-1/2 transform -translate-y-1/2 text-white bg-black bg-opacity-50 p-2 sm:p-3 rounded-full hover:bg-opacity-70 transition-all backdrop-blur-sm"
        >
          <ChevronRightIcon class="w-4 h-4 sm:w-6 sm:h-6" />
        </button>
        <!-- Indicateur dans le modal -->
        <div v-if="product.images && product.images.length > 1" class="absolute bottom-2 sm:bottom-4 left-1/2 transform -translate-x-1/2 text-white bg-black bg-opacity-50 px-3 sm:px-4 py-1 sm:py-2 rounded-full text-sm font-medium backdrop-blur-sm">
          {{ currentImageIndex + 1 }} / {{ product.images.length }}
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, watch } from 'vue'
import {capitalizeFirst ,formatNumber} from "../../composables/utils"
import { 
  Package as PackageIcon,
  X as XIcon,
  Info as InfoIcon,
  Image as ImageIcon,
  FileText as FileTextIcon,
  Tag as TagIcon,
  Palette as PaletteIcon,
  Ruler as RulerIcon,
  Video as VideoIcon,
  Edit as EditIcon,
  Copy as CopyIcon,
  Trash as TrashIcon,
  ChevronLeft as ChevronLeftIcon,
  ChevronRight as ChevronRightIcon,
  Truck as TruckIcon,
  Settings as SettingsIcon,
  Cog as CogIcon,
} from 'lucide-vue-next'

const props = defineProps(['product'])
const emit = defineEmits(['close', 'edit', 'duplicate', 'delete'])

const activeTab = ref('vehicle')
const currentImageIndex = ref(0)
const showImageModal = ref(false)

// Image principale (première image ou image par défaut)
const primaryImage = computed(() => {
  if (props.product?.images && props.product.images.length > 0) {
    return props.product.images[0]
  }
  return props.product?.primary_image || null
})

// Image sélectionnée dans la galerie
const selectedImage = computed(() => {
  if (props.product?.all_images && props.product.all_images.length > 0) {
    return props.product.all_images[currentImageIndex.value]
  }
  return null
})

// Image à afficher dans le modal
const modalImage = computed(() => {
  if (activeTab.value === 'media' && selectedImage.value) {
    return selectedImage.value
  }
  return primaryImage.value
})

// Nombre total de médias
const mediaCount = computed(() => {
  let count = 0
  if (props.product?.images) count += props.product.images.length
  if (props.product?.video_url) count += 1
  return count
})


// Réinitialiser l'index quand le produit change
watch(() => props.product, () => {
  currentImageIndex.value = 0
  activeTab.value = 'details'
  console.log("produit details:",product)
})

const getTagsArray = (tags) =>{
  if (!tags) return [];
  if (Array.isArray(tags)) return tags;
  return tags.split(',').map(tag => tag.trim()).filter(tag => tag.length > 0);
}

const selectImage = (index) => {
  currentImageIndex.value = index
}

const nextImage = () => {
  if (props.product?.all_images && props.product.all_images.length > 0) {
    currentImageIndex.value = (currentImageIndex.value + 1) % props.product.all_images.length
  }
}

const previousImage = () => {
  if (props.product?.all_images && props.product.all_images.length > 0) {
    currentImageIndex.value = currentImageIndex.value === 0 
      ? props.product.all_images.length - 1 
      : currentImageIndex.value - 1
  }
}

const openImageModal = () => {
  showImageModal.value = true
}

const closeImageModal = () => {
  showImageModal.value = false
}

const formatDate = (dateString) => {
  if (!dateString) return 'N/A'
  return new Date(dateString).toLocaleDateString('en-US', {
    year: 'numeric',
    month: 'long',
    day: 'numeric'
  })
}

const formatPrice = (price) => {
  return new Intl.NumberFormat('en-US').format(price || 0)
}

const getStatusBadgeClass = (status) => {
  switch (status) {
    case 'Actif':
      return 'bg-green-100 text-green-800 border border-green-200'
    case 'Stock faible':
      return 'bg-yellow-100 text-yellow-800 border border-yellow-200'
    case 'Rupture':
      return 'bg-red-100 text-red-800 border border-red-200'
    case 'Brouillon':
      return 'bg-gray-100 text-gray-800 border border-gray-200'
    default:
      return 'bg-gray-100 text-gray-800 border border-gray-200'
  }
}

const getStatusDotClass = (status) => {
  switch (status) {
    case 'Actif':
      return 'bg-green-500'
    case 'Stock faible':
      return 'bg-yellow-500'
    case 'Rupture':
      return 'bg-red-500'
    case 'Brouillon':
      return 'bg-gray-500'
    default:
      return 'bg-gray-500'
  }
}

const getStockStatus = (stock) => {
  if (stock === 0) return 'Out of stock'
  if (stock <= 5) return 'Low stock'
  return 'In stock'
}

const getStockStatusClass = (stock) => {
  if (stock === 0) return 'text-red-600'
  if (stock <= 5) return 'text-yellow-600'
  return 'text-green-600'
}

const getColorName = (color) => {
  const colorMap = {
    '#000000': 'Noir',
    '#FFFFFF': 'Blanc',
    '#C0C0C0': 'Argent',
    '#808080': 'Gris',
    '#FF0000': 'Rouge',
    '#0000FF': 'Bleu',
    '#008000': 'Vert',
    '#FFFF00': 'Jaune',
    '#FFA500': 'Orange',
    '#FFC0CB': 'Rose',
    '#800080': 'Violet',
    '#FFD700': 'Or',
    '#8B4513': 'Marron',
    '#654321': 'Brun'
  }
  return colorMap[color] || color
}

const handleBackdropClick = (event) => {
  if (event.target === event.currentTarget) {
    emit('close')
  }
}

const editProduct = (product)=>{
  emit('close');
  emit('edit', product);
}



const hasVehicleSpecs = computed(() => {
  return props.product && (
    props.product.vehicle_condition ||
    props.product.vehicle_make ||
    props.product.vehicle_model ||
    props.product.drive_type ||
    props.product.vehicle_year ||
    props.product.fuel_type ||
    props.product.transmission_type ||
    props.product.engine_brand ||
    (props.product.vehicle_mileage !== null && props.product.vehicle_mileage !== undefined)
  )
})

const hasAdvancedSpecs = computed(() => {
  return props.product && (
    props.product.fuel_type ||
    props.product.vehicle_year ||
    props.product.engine_brand
  )
})

const formatMileage = (mileage) => {
  if (mileage === 0) return 'Neuf (0 km)'
  if (mileage >= 200000) return '200,000+ km'
  return new Intl.NumberFormat('en-US').format(mileage) + ' km'
}

const getFuelTypeLabel = (fuelType) => {
  const labels = {
    'diesel': 'Diesel',
    'electric': 'Électrique',
    'hybrid': 'Hybride',
    'cng': 'GNC',
    'lng': 'GNL',
    'hydrogen': 'Hydrogène',
    'unknown': 'Inconnu'
  }
  return labels[fuelType] || fuelType
}

const getFuelTypeColor = (fuelType) => {
  const colors = {
    'diesel': 'bg-gray-500',
    'electric': 'bg-green-500',
    'hybrid': 'bg-blue-500',
    'cng': 'bg-yellow-500',
    'lng': 'bg-purple-500',
    'hydrogen': 'bg-cyan-500',
    'unknown': 'bg-gray-400'
  }
  return colors[fuelType] || 'bg-gray-400'
}

const getVehicleAge = () => {
  if (!props.product?.vehicle_year) return 'N/A'
  const currentYear = new Date().getFullYear()
  const age = currentYear - props.product.vehicle_year
  if (age === 0) return 'Neuf'
  if (age === 1) return '1 an'
  return `${age} ans`
}

const getVehicleCategory = () => {
  if (!props.product?.drive_type) return 'Standard'
  if (props.product.drive_type.includes('8X')) return 'Poids lourd'
  if (props.product.drive_type.includes('6X')) return 'Camion moyen'
  if (props.product.drive_type.includes('4X')) return 'Camion léger'
  return 'Véhicule utilitaire'
}

const getRecommendedUsage = () => {
  if (!props.product?.drive_type) return 'Usage général'
  if (props.product.drive_type.includes('8X')) return 'Transport lourd'
  if (props.product.drive_type.includes('6X')) return 'Livraison urbaine'
  if (props.product.drive_type.includes('4X')) return 'Distribution'
  return 'Usage polyvalent'
}

const getEfficiencyPercentage = () => {
  if (!props.product) return 70
  let efficiency = 70
  if (props.product.fuel_type === 'electric') efficiency += 20
  if (props.product.fuel_type === 'hybrid') efficiency += 15
  if (props.product.vehicle_year && props.product.vehicle_year > 2020) efficiency += 10
  if (props.product.engine_brand === 'weichai') efficiency += 5
  return Math.min(efficiency, 95)
}

const getDurabilityPercentage = () => {
  if (!props.product) return 75
  let durability = 75
  if (props.product.vehicle_condition === 'new') durability += 20
  if (props.product.vehicle_condition === 'refurbished') durability += 10
  if (props.product.engine_brand === 'man') durability += 10
  if (props.product.vehicle_mileage && props.product.vehicle_mileage < 50000) durability += 5
  return Math.min(durability, 95)
}
</script>

<style scoped>

/* Effet de verre dépoli */
.backdrop-blur-sm {
  backdrop-filter: blur(4px);
}

/* Scrollbar personnalisée */
.overflow-y-auto::-webkit-scrollbar {
  width: 6px;
}

.overflow-y-auto::-webkit-scrollbar-track {
  background: #f1f1f1;
  border-radius: 3px;
}

.overflow-y-auto::-webkit-scrollbar-thumb {
  background: #c1c1c1;
  border-radius: 3px;
}

.overflow-y-auto::-webkit-scrollbar-thumb:hover {
  background: #a8a8a8;
}

/* Animation pour l'overlay */
@keyframes fadeIn {
  from { opacity: 0; }
  to { opacity: 1; }
}

.bg-black\/60 {
  animation: fadeIn 0.3s ease-out;
}

/* Responsive pour très petits écrans */
@media (max-width: 375px) {
  .px-4 {
    padding-left: 1rem;
    padding-right: 1rem;
  }

  .py-3 {
    padding-top: 0.75rem;
    padding-bottom: 0.75rem;
  }
}
</style>
