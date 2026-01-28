<template>
  <div 
    class="fixed inset-0 bg-black/60 backdrop-blur-sm z-50 p-0 sm:p-4 sm:flex sm:items-center sm:justify-center"
    
  >
    <div 
      class="bg-white w-full h-screen sm:h-auto sm:max-h-[90vh] sm:max-w-4xl sm:rounded-2xl shadow-2xl overflow-hidden transform transition-all duration-300 ease-out sm:mx-auto"
      @click.stop
    >
      <div class="absolute inset-0 overflow-hidden pointer-events-none sm:rounded-2xl" style="background-color: #ffffff00;">
        <div class="absolute -top-10 -right-10 w-32 h-32 bg-gradient-to-br from-orange-200/30 to-orange-300/20 rounded-full blur-2xl animate-pulse"></div>
        <div class="absolute -bottom-10 -left-10 w-40 h-40 bg-gradient-to-br from-blue-200/25 to-indigo-200/15 rounded-full blur-2xl animate-pulse" style="animation-delay: 1s;"></div>
      </div>

      <div class="sticky top-0 z-10 bg-white/95 backdrop-blur-sm border-b border-gray-200 px-4 sm:px-6 py-4 sm:rounded-t-2xl">
        <div class="flex items-center justify-between">
          <div class="flex items-center space-x-3">
            <div class="w-10 h-10 rounded-lg flex items-center justify-center btn-degrade-orange">
              <PlusIcon class="w-5 h-5 text-white" />
            </div>
            <div>
              <h2 class="text-xl sm:text-2xl font-bold text-gray-900">Add a product</h2>
              <p class="text-sm text-gray-600 hidden sm:block">Create a new product for your store</p>
            </div>
          </div>
          
          <XIcon @click="closeModal" class="w-7 h-7 text-gray-500 cursor-pointer" />
        </div>

        <div v-if="error || isLoading || categoriesError || categoriesLoading" class="mt-4">
          <div v-if="error" class="mb-3 p-3 bg-red-50 border border-red-200 error-color rounded-lg flex items-center space-x-2">
            <AlertCircleIcon class="w-4 h-4 error-color flex-shrink-0" />
            <span class="text-sm">{{ error }}</span>
          </div>

          <div v-if="categoriesError" class="mb-3 p-3 bg-red-50 border border-red-200 error-color rounded-lg flex items-center space-x-2">
            <AlertCircleIcon class="w-4 h-4 error-color flex-shrink-0" />
            <div class="flex-1">
              <span class="text-sm">{{ categoriesError }}</span>
              <button 
                @click="fetchCategories" 
                class="ml-2 px-2 py-1 text-xs error-background-color"
              >
                Try again
              </button>
            </div>
          </div>

          <div v-if="isLoading" class="mb-3 p-3 bg-blue-50 border border-blue-200 text-orange-400 rounded-lg flex items-center space-x-2">
            <div class="animate-spin w-4 h-4 border-2 border-orange-400 border-t-transparent rounded-full flex-shrink-0"></div>
            <span class="text-sm">{{ loadingMessage }}</span>
          </div>

          <div v-if="categoriesLoading" class="mb-3 p-3 bg-blue-50 border border-blue-200 text-orange-400 rounded-lg flex items-center space-x-2">
            <div class="animate-spin w-4 h-4 border-2 border-orange-400 border-t-transparent rounded-full flex-shrink-0"></div>
            <span class="text-sm">Loading categories...</span>
          </div>
        </div>

        <div class="mt-4">
          <div class="flex items-center justify-between">
            <div 
              v-for="(step, index) in steps" 
              :key="index"
              class="flex items-center"
              :class="{ 'flex-1': index < steps.length - 1 }"
            >
              <div class="flex items-center">
                <div 
                  :class="[
                    'w-10 h-10 rounded-full flex items-center justify-center text-xs sm:text-sm font-medium transition-all duration-200',
                    currentStep > index 
                      ? 'bg-step-color text-white shadow-lg' 
                      : currentStep === index 
                        ? 'bg-orange shadow-lg' 
                        : 'bg-gray-200 text-gray-500'
                  ]"
                >
                  <CheckIcon v-if="currentStep > index" class="w-4 h-4 sm:w-4 sm:h-4" />
                  <span v-else>{{ index + 1 }}</span>
                </div>
                <span 
                  :class="[
                    'ml-2 text-xs sm:text-sm font-medium transition-colors hidden sm:inline',
                    currentStep >= index ? 'text-gray-900' : 'text-gray-500'
                  ]"
                >
                  {{ step.title }}
                </span>
              </div>
             
            </div>
          </div>
        </div>
      </div>

      <div class="overflow-y-auto h-[calc(100vh-200px)] sm:h-auto sm:max-h-[calc(60vh)] px-4 sm:px-6 py-6 relative z-5" style="color: white;">
        <form @submit.prevent="handleSubmit" class="space-y-6 sm:space-y-8">
  
          <!-- ÉTAPE 0: Informations de base -->
          <div v-show="currentStep === 0" class="space-y-6">
            <div class="bg-white/80 backdrop-blur-sm rounded-xl p-4 sm:p-6 border border-gray-100 shadow-sm">
              <div class="flex items-center space-x-3 mb-4 sm:mb-6">
                <div class="w-8 h-8 rounded-lg flex items-center justify-center bg-orange">
                  <InfoIcon class="w-4 h-4 text-white" />
                </div>
                <h3 class="text-lg sm:text-xl font-semibold text-gray-900">Basic Informations</h3>
              </div>

              <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 sm:gap-6">
                <div class="sm:col-span-2">
                  <label for="name" class="block text-sm font-medium text-gray-700 mb-2">
                    Product name 
                  </label>
                  <input
                    id="name"
                    v-model="productData.name"
                    type="text"
                    required
                    disabled="true"
                    class="text-sm sm:text-base input-style"
                    placeholder="The product name will be generated automatically"
                  >
                </div>

                <div>
                  <label for="category" class="block text-sm font-medium text-gray-700 mb-2">
                    Category <span class="error-color">*</span>
                  </label>
                  <select
                    id="category"
                    v-model="productData.category_id"
                    @change="updateSubcategories"
                    required
                    :disabled="categoriesLoading"
                    class="text-sm sm:text-base input-style"
                  >
                    <option value="">{{ categoriesLoading ? 'Loading...' : 'Select a category' }}</option>
                    <option v-for="category in categories" :key="category.id" :value="category.id">
                      {{ category.name }}
                    </option>
                  </select>
                </div>

                <div>
                  <label for="subcategory" class="block text-sm font-medium text-gray-700 mb-2">
                    Subcategory <span class="error-color">*</span>
                  </label>
                  <select
                    id="subcategory"
                    v-model="productData.subcategory_id"
                    @change="updateSubSubcategories"
                    required
                    :disabled="!productData.category_id || categoriesLoading"
                    class="text-sm sm:text-base input-style"
                  >
                    <option value="">Select a subcategory</option>
                    <option v-for="subcategory in availableSubcategories" :key="subcategory.id" :value="subcategory.id">
                      {{ subcategory.name }}
                    </option>
                  </select>
                </div>

                <div v-if="availableSubSubcategories.length > 0" class="sm:col-span-1">
                  <label for="subsubcategory" class="block text-sm font-medium text-gray-700 mb-2">
                    Subsubcategory
                  </label>
                  <select
                    id="subsubcategory"
                    v-model="productData.subsubcategory_id"
                    @change="updateSubSubSubcategories"
                    :disabled="!productData.subcategory_id || categoriesLoading"
                    class="text-sm sm:text-base input-style"
                  >
                    <option value="">Select a subsubcategory (optional)</option>
                    <option v-for="subsubcategory in availableSubSubcategories" :key="subsubcategory.id" :value="subsubcategory.id">
                      {{ subsubcategory.name }}
                    </option>
                  </select>
                </div>

                <div v-if="availableSubSubSubcategories.length > 0" class="sm:col-span-1">
                  <label for="subsubsubcategory" class="block text-sm font-medium text-gray-700 mb-2">
                    Subsubsubcategory
                  </label>
                  <select
                    id="subsubsubcategory"
                    v-model="productData.subsubsubcategory_id"
                    :disabled="!productData.subsubcategory_id || categoriesLoading"
                    class="text-sm sm:text-base input-style"
                    style="color: black"
                  >
                    <option value="">Select a subsubsubcategory (optional)</option>
                    <option v-for="subsubsubcategory in availableSubSubSubcategories" :key="subsubsubcategory.id" :value="subsubsubcategory.id">
                      {{ subsubsubcategory.name }}
                    </option>
                  </select>
                </div>

                <!-- 🚗 CAR CATEGORY: Data Entry Method Selection -->
                <div v-if="isCarCategory" class="sm:col-span-2">
                  <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-4">
                    <h4 class="text-base font-semibold text-gray-900 mb-3">
                      How would you like to enter the vehicle data?
                    </h4>
                    <div class="space-y-3">
                      <label class="flex items-start cursor-pointer">
                        <input
                          type="radio"
                          v-model="productData.car_data_entry_mode"
                          value="vin"
                          class="mt-1 mr-3"
                        >
                        <div>
                          <div class="font-medium text-gray-900">Retrieve data via VIN (Automatic)</div>
                          <div class="text-sm text-gray-600">Enter the 17-character VIN to automatically fill vehicle information</div>
                        </div>
                      </label>

                      <label class="flex items-start cursor-pointer">
                        <input
                          type="radio"
                          v-model="productData.car_data_entry_mode"
                          value="manual"
                          class="mt-1 mr-3"
                        >
                        <div>
                          <div class="font-medium text-gray-900">Enter data manually</div>
                          <div class="text-sm text-gray-600">Fill all vehicle fields manually</div>
                        </div>
                      </label>
                    </div>
                  </div>

                  <!-- VIN Input Section (only shown if VIN mode selected) -->
                  <div v-if="productData.car_data_entry_mode === 'vin'" class="bg-white border border-gray-200 rounded-lg p-4">
                    <label for="car_vin_input" class="block text-sm font-medium text-gray-700 mb-2">
                      Vehicle Identification Number (VIN) <span class="error-color">*</span>
                    </label>
                    <div class="flex gap-2">
                      <input
                        id="car_vin_input"
                        v-model="productData.car_vin_input"
                        type="text"
                        maxlength="17"
                        :disabled="productData.car_vin_decoding"
                        class="text-sm sm:text-base input-style flex-1"
                        placeholder="Enter 17-character VIN"
                      >
                      <button
                        type="button"
                        @click="decodeVIN(productData.car_vin_input)"
                        :disabled="productData.car_vin_input.length !== 17 || productData.car_vin_decoding"
                        class="px-4 py-2 bg-orange text-white rounded-lg hover:bg-orange-600 disabled:opacity-50 disabled:cursor-not-allowed flex items-center gap-2"
                      >
                        <svg v-if="!productData.car_vin_decoding" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                        <svg v-else class="w-5 h-5 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                        </svg>
                        {{ productData.car_vin_decoding ? 'Decoding...' : 'Decode' }}
                      </button>
                    </div>

                    <!-- VIN Status Messages -->
                    <div v-if="productData.car_vin_decoding" class="mt-2 text-sm text-blue-600 flex items-center gap-2">
                      <svg class="w-4 h-4 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                      </svg>
                      Decoding VIN...
                    </div>

                    <div v-if="productData.car_vin_decoded" class="mt-2 text-sm text-green-600 flex items-center gap-2">
                      <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                      </svg>
                      VIN decoded successfully! Vehicle information has been filled automatically.
                    </div>

                    <div v-if="productData.car_vin_error" class="mt-2 text-sm text-red-600 flex items-center gap-2">
                      <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                      </svg>
                      {{ productData.car_vin_error }}
                    </div>
                  </div>
                </div>

                <div class="sm:col-span-2">
                  <label for="tags" class="block text-sm font-medium text-gray-700 mb-2">
                    Tags (optional)
                  </label>
                  <input
                    id="tags"
                    v-model="productData.tags"
                    type="text"
                    class="text-sm sm:text-base input-style"
                    placeholder="Ex: new, trending, promotion (separated by commas)"
                  >
                </div>
              </div>
            </div>
          </div>

          <!-- ÉTAPE 1: Vehicle Specifications (Trucks) OU Trailer Specifications (Trailers) OU Car Info (Cars) -->
          <div v-show="currentStep === 1" class="space-y-6">
            <!-- TRUCKS: Vehicle Specifications -->
            <div v-if="!isTrailerCategory && !isCarCategory" class="bg-white/80 backdrop-blur-sm rounded-xl p-4 sm:p-6 border border-gray-100 shadow-sm">
              <div class="flex items-center space-x-3 mb-4 sm:mb-6">
                <div class="w-8 h-8 rounded-lg flex items-center justify-center bg-orange">
                  <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                  </svg>
                </div>
                <h3 class="text-lg sm:text-xl font-semibold text-gray-900">Vehicle Specifications</h3>
              </div>

              <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 sm:gap-6">
                <div>
                  <label for="vehicle_make" class="block text-sm font-medium text-gray-700 mb-2">
                    Vehicle brand <span class="error-color">*</span>
                  </label>
                  <select
                    id="vehicle_make"
                    required
                    @change="updateModelid"
                    :disabled="brandsLoading"
                    v-model="productData.vehicle_brand_id"
                    class="text-sm sm:text-base input-style"
                  >
                    <option value="">{{ brandsLoading ? 'Loading...' : 'Select a vehicle brand' }}</option>
                    <option v-for="brand in brands" :key="brand.id" :value="brand.id">
                      {{ brand.name }}
                    </option>
                  </select>
                </div>
                
                <div>
                  <label for="vehicle_condition" class="block text-sm font-medium text-gray-700 mb-2">
                    Vehicle condition <span class="error-color">*</span>
                  </label>
                  <select
                    id="vehicle_condition"
                    v-model="productData.vehicle_condition"
                    class="text-sm sm:text-base input-style"
                    required
                  >
                    <option value="">Select vehicle condition</option>
                    <option value="New">New</option>
                    <option value="Used">Used</option>
                    <option value="Refurbished">Refurbished</option>
                  </select>
                </div>

                <div>
                  <label for="vehicle_model" class="block text-sm font-medium text-gray-700 mb-2">
                    Vehicle model <span class="error-color">*</span>
                  </label>
                  <select
                    id="vehicle_model"
                    v-model="productData.vehicle_model_id"
                    required
                    @change="updateFuelType"
                    :disabled="!productData.vehicle_brand_id || brandsLoading"
                    class="text-sm sm:text-base input-style"
                  >
                    <option value="">Select a vehicle model</option>
                    <option v-for="model in availableModels" :key="model.id" :value="model.id">
                      {{ model.name }}
                    </option>
                  </select>
                </div>
                
                <div>
                  <label for="fuel_type" class="block text-sm font-medium text-gray-700 mb-2">
                    Fuel type
                  </label>
                  <input
                    type="text"
                    :value="productData.fuel_type"
                    disabled
                    class="text-sm sm:text-base input-style cursor-not-allowed overflow-ellipsis"
                    placeholder="The fuel type will be set automatically"
                  >
                </div>

                <div>
                  <label for="production_date" class="block text-sm font-medium text-gray-700 mb-2">
                    Production date <span class="error-color">*</span>
                  </label>
                  <input
                    id="production_date"
                    v-model="productData.production_date"
                    type="date"
                    class="text-sm sm:text-base input-style"
                  >
                </div>

                <div>
                  <label for="fuel_tank_capacity" class="block text-sm font-medium text-gray-700 mb-2">
                    Fuel tank capacity (L) <span class="error-color">*</span>
                  </label>
                  <input
                    id="fuel_tank_capacity"
                    v-model="productData.fuel_tank_capacity"
                    type="number"
                    min="0"
                    class="text-sm sm:text-base input-style"
                    placeholder="Ex: 600"
                  >
                </div>

                <div>
                  <label for="country_of_origin" class="block text-sm font-medium text-gray-700 mb-2">
                    Country of origin <span class="error-color">*</span>
                  </label>
                  <input
                    id="country_of_origin"
                    v-model="productData.country_of_origin"
                    type="text"
                    class="text-sm sm:text-base input-style"
                    placeholder="Ex: Germany, China, Japan"
                  >
                </div>

                <div>
                  <label for="drive_type" class="block text-sm font-medium text-gray-700 mb-2">
                    Transmission type <span class="error-color">*</span>
                  </label>
                  <select
                    id="drive_type"
                    v-model="productData.drive_type"
                    class="text-sm sm:text-base input-style"
                  >
                    <option value="">Select a Transmission type</option>
                    <option value="4x2">4X2</option>
                    <option value="6x2">6X2</option>
                    <option value="6x4">6X4</option>
                    <option value="6x6">6X6</option>
                    <option value="8x4">8X4</option>
                    <option value="8x6">8X6</option>
                    <option value="8x8">8X8</option>
                  </select>
                </div>

                <div>
                  <label for="curb_weight" class="block text-sm font-medium text-gray-700 mb-2">
                    Curb weight (Kg) <span class="error-color">*</span>
                  </label>
                  <input
                    id="curb_weight"
                    v-model="productData.curb_weight"
                    type="number"
                    min="0"
                    class="text-sm sm:text-base input-style"
                    placeholder="Ex: 8000"
                  >
                </div>
                
                <div>
                  <label for="wheelbase" class="block text-sm font-medium text-gray-700 mb-2">
                    Wheelbases (mm) <span class="error-color">*</span>
                  </label>
                  <input
                    id="wheelbase"
                    v-model="productData.wheelbase"
                    type="number"
                    min="0"
                    class="text-sm sm:text-base input-style"
                    placeholder="Ex: 3175"
                  >
                </div>

                <div>
                  <label for="payload_capacity" class="block text-sm font-medium text-gray-700 mb-2">
                    Payload capacity (kg) <span class="error-color">*</span>
                  </label>
                  <input
                    id="payload_capacity"
                    v-model="productData.payload_capacity"
                    type="number"
                    min="0"
                    class="text-sm sm:text-base input-style"
                    placeholder="Ex: 32000"
                  >
                </div>

                <div>
                  <label for="speed" class="block text-sm font-medium text-gray-700 mb-2">
                    Economic speed / Maximum speed (km/h)
                  </label>
                  <input
                    id="speed"
                    v-model="productData.speed"
                    type="text"
                    class="text-sm sm:text-base input-style"
                    placeholder="Ex: 58 ~ 72/90"
                  >
                </div>

                <div>
                  <label for="gvw" class="block text-sm font-medium text-gray-700 mb-2">
                    GVW - Gross Vehicle Weight (kg) <span class="error-color">*</span>
                  </label>
                  <input
                    id="gvw"
                    v-model="productData.gvw"
                    type="number"
                    min="0"
                    class="text-sm sm:text-base input-style"
                    placeholder="Ex: 40000"
                  >
                </div>
                
                <div>
                  <label for="cabin_type" class="block text-sm font-medium text-gray-700 mb-2">
                    Cabin type <span class="error-color">*</span>
                  </label>
                  <input
                    id="cabin_type"
                    v-model="productData.cabin_type"
                    type="text"
                    class="text-sm sm:text-base input-style"
                    placeholder="Ex: Short cabin, Long cabin"
                  >
                </div>

                <div>
                  <label for="chassis_dimensions" class="block text-sm font-medium text-gray-700 mb-2">
                    Chassis Dimensions (mm) <span class="error-color">*</span>
                  </label>
                  <input
                    id="chassis_dimensions"
                    v-model="productData.chassis_dimensions"
                    type="text"
                    class="text-sm sm:text-base input-style"
                    placeholder="Ex: 850x300 (8+5)"
                  >
                </div>

                <div>
                  <label for="frame_rear_suspension" class="block text-sm font-medium text-gray-700 mb-2">
                    Frame rear suspension (mm) <span class="error-color">*</span>
                  </label>
                  <input
                    id="frame_rear_suspension"
                    v-model="productData.frame_rear_suspension"
                    type="number"
                    min="0"
                    class="text-sm sm:text-base input-style"
                    placeholder="Ex: 725"
                  >
                </div>

                <div class="sm:col-span-2">
                  <label class="block text-sm font-medium text-gray-700 mb-2">
                    Overall Dimensions LxlxH (mm) <span class="error-color">*</span>
                  </label>
                  <div class="grid grid-cols-3 gap-2">
                    <input
                      v-model="productData.dimensions_length"
                      type="number"
                      min="0"
                      class="text-sm sm:text-base input-style"
                      placeholder="Length"
                    >
                    <input
                      v-model="productData.dimensions_width"
                      type="number"
                      min="0"
                      class="text-sm sm:text-base input-style"
                      placeholder="Width"
                    >
                    <input
                      v-model="productData.dimensions_height"
                      type="number"
                      min="0"
                      class="text-sm sm:text-base input-style"
                      placeholder="Height"
                    >
                  </div>
                </div>
              </div>

              <div class="w-full h-30 mb-10 mt-5">
                <label for="cab" class="block text-sm font-medium text-gray-700 mb-2">
                  Cab <span class="error-color">*</span>
                </label>
                <textarea 
                  id="cab"
                  v-model="productData.cab"
                  class="text-sm sm:text-base input-style h-full w-full"
                  placeholder="Ex: F3000 extended flat-top cab, without roof deflector, hydraulic main seat,...."
                ></textarea>
              </div>
            </div>

            <!-- 🚗 CARS: Vehicle Information & Specifications -->
            <div v-if="isCarCategory">
              <!-- Section 1: Basic Vehicle Information -->
              <div class="bg-white/80 backdrop-blur-sm rounded-xl p-4 sm:p-6 border border-gray-100 shadow-sm mb-6">
                <div class="flex items-center space-x-3 mb-4 sm:mb-6">
                  <div class="w-8 h-8 rounded-lg flex items-center justify-center bg-orange">
                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                  </div>
                  <h3 class="text-lg sm:text-xl font-semibold text-gray-900">Basic Vehicle Information</h3>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 sm:gap-6">
                  <div>
                    <label for="car_make" class="block text-sm font-medium text-gray-700 mb-2">
                      Make <span class="error-color">*</span>
                    </label>
                    <input
                      id="car_make"
                      v-model="productData.car_make"
                      type="text"
                      required
                      :readonly="productData.car_vin_decoded && productData.car_data_entry_mode === 'vin'"
                      class="text-sm sm:text-base input-style"
                      :class="{ 'bg-gray-50': productData.car_vin_decoded && productData.car_data_entry_mode === 'vin' }"
                      placeholder="Ex: Tesla, Toyota, BMW"
                    >
                  </div>

                  <div>
                    <label for="car_model" class="block text-sm font-medium text-gray-700 mb-2">
                      Model <span class="error-color">*</span>
                    </label>
                    <input
                      id="car_model"
                      v-model="productData.car_model"
                      type="text"
                      required
                      class="text-sm sm:text-base input-style"
                      :class="{ 'bg-gray-50': productData.car_vin_decoded && productData.car_data_entry_mode === 'vin' }"
                      placeholder="Ex: Model 3, Camry, X5JJJJJJ"
                    >
                  </div>

                  <div>
                    <label for="car_year" class="block text-sm font-medium text-gray-700 mb-2">
                      Year <span class="error-color">*</span>
                    </label>
                    <input
                      id="car_year"
                      v-model.number="productData.car_year"
                      type="number"
                      min="1900"
                      :max="new Date().getFullYear() + 1"
                      required
                      :readonly="productData.car_vin_decoded && productData.car_data_entry_mode === 'vin'"
                      class="text-sm sm:text-base input-style"
                      :class="{ 'bg-gray-50': productData.car_vin_decoded && productData.car_data_entry_mode === 'vin' }"
                      placeholder="Ex: 2024"
                    >
                  </div>

                  <div>
                    <label for="car_condition" class="block text-sm font-medium text-gray-700 mb-2">
                      Condition <span class="error-color">*</span>
                    </label>
                    <select
                      id="car_condition"
                      v-model="productData.car_condition"
                      required
                      class="text-sm sm:text-base input-style"
                    >
                      <option value="">Select Condition</option>
                      <option value="New">New</option>
                      <option value="Used">Used</option>
                      <option value="Certified Pre-Owned">Certified Pre-Owned</option>
                    </select>
                  </div>

                  <div>
                    <label for="car_mileage" class="block text-sm font-medium text-gray-700 mb-2">
                      Mileage (km)
                    </label>
                    <input
                      id="car_mileage"
                      v-model.number="productData.car_mileage"
                      type="number"
                      min="0"
                      class="text-sm sm:text-base input-style"
                      placeholder="Ex: 15000"
                    >
                  </div>

                  <div>
                    <label for="car_body_type" class="block text-sm font-medium text-gray-700 mb-2">
                      Body Type
                    </label>
                    <select
                      id="car_body_type"
                      v-model="productData.car_body_type"
                      class="text-sm sm:text-base input-style"
                    >
                      <option value="">Select Body Type</option>
                      <option value="Sedan">Sedan</option>
                      <option value="SUV">SUV</option>
                      <option value="Coupe">Coupe</option>
                      <option value="Hatchback">Hatchback</option>
                      <option value="Convertible">Convertible</option>
                      <option value="Wagon">Wagon</option>
                      <option value="Pickup">Pickup</option>
                      <option value="Van">Van</option>
                    </select>
                  </div>

                  <div>
                    <label for="car_trim_level" class="block text-sm font-medium text-gray-700 mb-2">
                      Trim Level
                    </label>
                    <input
                      id="car_trim_level"
                      v-model="productData.car_trim_level"
                      type="text"
                      class="text-sm sm:text-base input-style"
                      placeholder="Ex: Sport, Premium, Limited"
                    >
                  </div>
                </div>
              </div>

              <!-- Section 2: Dimensions -->
              <div class="bg-white/80 backdrop-blur-sm rounded-xl p-4 sm:p-6 border border-gray-100 shadow-sm mb-6">
                <div class="flex items-center space-x-3 mb-4 sm:mb-6">
                  <div class="w-8 h-8 rounded-lg flex items-center justify-center bg-orange">
                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8V4m0 0h4M4 4l5 5m11-1V4m0 0h-4m4 0l-5 5M4 16v4m0 0h4m-4 0l5-5m11 5l-5-5m5 5v-4m0 4h-4"></path>
                    </svg>
                  </div>
                  <h3 class="text-lg sm:text-xl font-semibold text-gray-900">Dimensions</h3>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 sm:gap-6">
                  <div>
                    <label for="car_length" class="block text-sm font-medium text-gray-700 mb-2">
                      Length (mm)
                    </label>
                    <input
                      id="car_length"
                      v-model.number="productData.car_length"
                      type="number"
                      min="0"
                      class="text-sm sm:text-base input-style"
                      placeholder="Ex: 4695"
                    >
                  </div>

                  <div>
                    <label for="car_width" class="block text-sm font-medium text-gray-700 mb-2">
                      Width (mm)
                    </label>
                    <input
                      id="car_width"
                      v-model.number="productData.car_width"
                      type="number"
                      min="0"
                      class="text-sm sm:text-base input-style"
                      placeholder="Ex: 1850"
                    >
                  </div>

                  <div>
                    <label for="car_height" class="block text-sm font-medium text-gray-700 mb-2">
                      Height (mm)
                    </label>
                    <input
                      id="car_height"
                      v-model.number="productData.car_height"
                      type="number"
                      min="0"
                      class="text-sm sm:text-base input-style"
                      placeholder="Ex: 1443"
                    >
                  </div>

                  <div>
                    <label for="car_wheelbase" class="block text-sm font-medium text-gray-700 mb-2">
                      Wheelbase (mm)
                    </label>
                    <input
                      id="car_wheelbase"
                      v-model.number="productData.car_wheelbase"
                      type="number"
                      min="0"
                      class="text-sm sm:text-base input-style"
                      placeholder="Ex: 2875"
                    >
                  </div>

                  <div>
                    <label for="car_kerb_weight" class="block text-sm font-medium text-gray-700 mb-2">
                      Kerb Weight (kg)
                    </label>
                    <input
                      id="car_kerb_weight"
                      v-model.number="productData.car_kerb_weight"
                      type="number"
                      min="0"
                      class="text-sm sm:text-base input-style"
                      placeholder="Ex: 1645"
                    >
                  </div>
                </div>
              </div>

              <!-- Section 3: Engine & Drivetrain -->
              <div class="bg-white/80 backdrop-blur-sm rounded-xl p-4 sm:p-6 border border-gray-100 shadow-sm mb-6">
                <div class="flex items-center space-x-3 mb-4 sm:mb-6">
                  <div class="w-8 h-8 rounded-lg flex items-center justify-center bg-orange">
                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                    </svg>
                  </div>
                  <h3 class="text-lg sm:text-xl font-semibold text-gray-900">Engine & Drivetrain</h3>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 sm:gap-6">
                  <div>
                    <label for="car_fuel_type" class="block text-sm font-medium text-gray-700 mb-2">
                      Fuel Type <span class="error-color">*</span>
                    </label>
                    <select
                      id="car_fuel_type"
                      v-model="productData.car_fuel_type"
                      required
                      :readonly="productData.car_vin_decoded && productData.car_data_entry_mode === 'vin'"
                      class="text-sm sm:text-base input-style"
                    >
                      <option value="">Select Fuel Type</option>
                      <option value="Gasoline">Gasoline</option>
                      <option value="Diesel">Diesel</option>
                      <option value="Electric">Electric</option>
                      <option value="Hybrid">Hybrid</option>
                      <option value="Plug-in Hybrid">Plug-in Hybrid</option>
                    </select>
                  </div>

                  <div>
                    <label for="car_transmission" class="block text-sm font-medium text-gray-700 mb-2">
                      Transmission <span class="error-color">*</span>
                    </label>
                    <select
                      id="car_transmission"
                      v-model="productData.car_transmission"
                      required
                      :readonly="productData.car_vin_decoded && productData.car_data_entry_mode === 'vin'"
                      class="text-sm sm:text-base input-style"
                    >
                      <option value="">Select Transmission</option>
                      <option value="Automatic">Automatic</option>
                      <option value="Manual">Manual</option>
                      <option value="CVT">CVT</option>
                      <option value="Semi-Automatic">Semi-Automatic</option>
                    </select>
                  </div>

                  <div>
                    <label for="car_engine_size" class="block text-sm font-medium text-gray-700 mb-2">
                      Engine Size (L)
                    </label>
                    <input
                      id="car_engine_size"
                      v-model="productData.car_engine_size"
                      type="text"
                      class="text-sm sm:text-base input-style"
                      placeholder="Ex: 2.0"
                    >
                  </div>

                  <div>
                    <label for="car_engine_cylinders" class="block text-sm font-medium text-gray-700 mb-2">
                      Cylinders
                    </label>
                    <input
                      id="car_engine_cylinders"
                      v-model.number="productData.car_engine_cylinders"
                      type="number"
                      min="0"
                      class="text-sm sm:text-base input-style"
                      placeholder="Ex: 4"
                    >
                  </div>

                  <div>
                    <label for="car_drivetrain" class="block text-sm font-medium text-gray-700 mb-2">
                      Drivetrain
                    </label>
                    <select
                      id="car_drivetrain"
                      v-model="productData.car_drivetrain"
                      class="text-sm sm:text-base input-style"
                    >
                      <option value="">Select Drivetrain</option>
                      <option value="FWD">FWD (Front-Wheel Drive)</option>
                      <option value="RWD">RWD (Rear-Wheel Drive)</option>
                      <option value="AWD">AWD (All-Wheel Drive)</option>
                      <option value="4WD">4WD (Four-Wheel Drive)</option>
                    </select>
                  </div>
                </div>
              </div>

              <!-- Section 4: Battery & Electric (conditional - only if Electric/Hybrid) -->
              <div v-if="productData.car_fuel_type === 'Electric' || productData.car_fuel_type === 'Hybrid' || productData.car_fuel_type === 'Plug-in Hybrid'" class="bg-blue-50 border border-blue-200 rounded-xl p-4 sm:p-6 mb-6">
                <div class="flex items-center space-x-3 mb-4 sm:mb-6">
                  <div class="w-8 h-8 rounded-lg flex items-center justify-center bg-blue-500">
                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                    </svg>
                  </div>
                  <h3 class="text-lg sm:text-xl font-semibold text-gray-900">Battery & Electric</h3>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 sm:gap-6">
                  <div>
                    <label for="car_battery_range" class="block text-sm font-medium text-gray-700 mb-2">
                      Range (km)
                    </label>
                    <input
                      id="car_battery_range"
                      v-model.number="productData.car_battery_range"
                      type="number"
                      min="0"
                      class="text-sm sm:text-base input-style"
                      placeholder="Ex: 500"
                    >
                  </div>

                  <div>
                    <label for="car_battery_capacity" class="block text-sm font-medium text-gray-700 mb-2">
                      Battery Capacity (kWh)
                    </label>
                    <input
                      id="car_battery_capacity"
                      v-model.number="productData.car_battery_capacity"
                      type="number"
                      min="0"
                      step="0.1"
                      class="text-sm sm:text-base input-style"
                      placeholder="Ex: 75"
                    >
                  </div>

                  <div>
                    <label for="car_charge_time_full" class="block text-sm font-medium text-gray-700 mb-2">
                      Full Charge Time
                    </label>
                    <input
                      id="car_charge_time_full"
                      v-model="productData.car_charge_time_full"
                      type="text"
                      class="text-sm sm:text-base input-style"
                      placeholder="Ex: 10h 30m"
                    >
                  </div>

                  <div>
                    <label for="car_quick_charge_time" class="block text-sm font-medium text-gray-700 mb-2">
                      Quick Charge Time (0-80%)
                    </label>
                    <input
                      id="car_quick_charge_time"
                      v-model="productData.car_quick_charge_time"
                      type="text"
                      class="text-sm sm:text-base input-style"
                      placeholder="Ex: 1h 15m"
                    >
                  </div>
                </div>
              </div>
            </div>

            <!-- TRAILERS: Trailer Specifications -->
            <div v-if="isTrailerCategory" class="bg-white/80 backdrop-blur-sm rounded-xl p-4 sm:p-6 border border-gray-100 shadow-sm">
              <div class="flex items-center space-x-3 mb-4 sm:mb-6">
                <div class="w-8 h-8 rounded-lg flex items-center justify-center bg-orange">
                  <InfoIcon class="w-4 h-4 text-white" />
                </div>
                <h3 class="text-lg sm:text-xl font-semibold text-gray-900">Trailer Specifications</h3>
              </div>

              <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 sm:gap-6">
                <div>
                  <label for="trailer_condition" class="block text-sm font-medium text-gray-700 mb-2">
                    Condition <span class="error-color">*</span>
                  </label>
                  <select
                    id="trailer_condition"
                    v-model="productData.trailer_condition"
                    required
                    class="text-sm sm:text-base input-style"
                  >
                    <option value="">Select condition</option>
                    <option value="New">New</option>
                    <option value="Used">Used</option>
                    <option value="Refurbished">Refurbished</option>
                  </select>
                </div>

                <div>
                  <label for="trailer_type" class="block text-sm font-medium text-gray-700 mb-2">
                    Type <span class="error-color">*</span>
                  </label>
                  <select
                    id="trailer_type"
                    v-model="productData.trailer_type"
                    required
                    :disabled="trailerTypesLoading"
                    class="text-sm sm:text-base input-style"
                  >
                    <option value="">{{ trailerTypesLoading ? 'Loading...' : 'Select trailer type' }}</option>
                    <option v-for="type in trailerTypes" :key="type.id" :value="type.name">
                      {{ type.name }}
                    </option>
                  </select>
                </div>

                <div>
                  <label for="trailer_brand" class="block text-sm font-medium text-gray-700 mb-2">
                    Brand Name <span class="error-color">*</span>
                  </label>
                  <select
                    id="trailer_brand"
                    v-model="productData.trailer_brand"
                    required
                    :disabled="trailerBrandsLoading"
                    class="text-sm sm:text-base input-style"
                  >
                    <option value="">{{ trailerBrandsLoading ? 'Loading...' : 'Select brand' }}</option>
                    <option v-for="brand in trailerBrands" :key="brand.id" :value="brand.name">
                      {{ brand.name }}
                    </option>
                  </select>
                </div>

                <div>
                  <label for="trailer_use" class="block text-sm font-medium text-gray-700 mb-2">
                    Use <span class="error-color">*</span>
                  </label>
                  <input
                    id="trailer_use"
                    v-model="productData.trailer_use"
                    type="text"
                    required
                    class="text-sm sm:text-base input-style"
                    placeholder="Ex: Cargo, Container, Flatbed"
                  >
                </div>

                <div>
                  <label for="trailer_size" class="block text-sm font-medium text-gray-700 mb-2">
                    Size <span class="error-color">*</span>
                  </label>
                  <input
                    id="trailer_size"
                    v-model="productData.trailer_size"
                    type="text"
                    required
                    class="text-sm sm:text-base input-style"
                    placeholder="Ex: 13m, 40ft"
                  >
                </div>

                <div>
                  <label for="trailer_axle" class="block text-sm font-medium text-gray-700 mb-2">
                    Axle <span class="error-color">*</span>
                  </label>
                  <select
                    id="trailer_axle"
                    v-model="productData.trailer_axle"
                    required
                    class="text-sm sm:text-base input-style"
                  >
                    <option value="">Select axle</option>
                    <option value="2">2 Axles</option>
                    <option value="3">3 Axles</option>
                    <option value="4">4 Axles</option>
                  </select>
                </div>

                <div>
                  <label for="trailer_suspension" class="block text-sm font-medium text-gray-700 mb-2">
                    Suspension <span class="error-color">*</span>
                  </label>
                  <input
                    id="trailer_suspension"
                    v-model="productData.trailer_suspension"
                    type="text"
                    required
                    class="text-sm sm:text-base input-style"
                    placeholder="Ex: Air suspension, Mechanical"
                  >
                </div>

                <div>
                  <label for="trailer_tire" class="block text-sm font-medium text-gray-700 mb-2">
                    Tire <span class="error-color">*</span>
                  </label>
                  <input
                    id="trailer_tire"
                    v-model="productData.trailer_tire"
                    type="text"
                    required
                    class="text-sm sm:text-base input-style"
                    placeholder="Ex: 12R22.5"
                  >
                </div>

                <div>
                  <label for="trailer_king_pin" class="block text-sm font-medium text-gray-700 mb-2">
                    King Pin
                  </label>
                  <input
                    id="trailer_king_pin"
                    v-model="productData.trailer_king_pin"
                    type="text"
                    class="text-sm sm:text-base input-style"
                    placeholder="Ex: 2 inch, 3.5 inch"
                  >
                </div>

                <div>
                  <label for="trailer_main_beam" class="block text-sm font-medium text-gray-700 mb-2">
                    Main Beam
                  </label>
                  <input
                    id="trailer_main_beam"
                    v-model="productData.trailer_main_beam"
                    type="text"
                    class="text-sm sm:text-base input-style"
                    placeholder="Ex: Q345B steel, 500mm height"
                  >
                </div>

                <div>
                  <label for="trailer_max_payload" class="block text-sm font-medium text-gray-700 mb-2">
                    Max Payload (tons) <span class="error-color">*</span>
                  </label>
                  <input
                    id="trailer_max_payload"
                    v-model="productData.trailer_max_payload"
                    type="number"
                    min="0"
                    step="0.1"
                    required
                    class="text-sm sm:text-base input-style"
                    placeholder="Ex: 50"
                  >
                </div>

                <div>
                  <label for="trailer_place_of_origin" class="block text-sm font-medium text-gray-700 mb-2">
                    Place of Origin <span class="error-color">*</span>
                  </label>
                  <input
                    id="trailer_place_of_origin"
                    v-model="productData.trailer_place_of_origin"
                    type="text"
                    required
                    class="text-sm sm:text-base input-style"
                    placeholder="Ex: China, Germany"
                  >
                </div>

                <div>
                  <label for="trailer_material" class="block text-sm font-medium text-gray-700 mb-2">
                    Material
                  </label>
                  <input
                    id="trailer_material"
                    v-model="productData.trailer_material"
                    type="text"
                    class="text-sm sm:text-base input-style"
                    placeholder="Ex: Q345B Steel, Aluminum"
                  >
                </div>

                <div>
                  <label for="trailer_landing_gear" class="block text-sm font-medium text-gray-700 mb-2">
                    Landing Gear
                  </label>
                  <input
                    id="trailer_landing_gear"
                    v-model="productData.trailer_landing_gear"
                    type="text"
                    class="text-sm sm:text-base input-style"
                    placeholder="Ex: JOST, SAF"
                  >
                </div>

                <div>
                  <label for="trailer_axle_brand" class="block text-sm font-medium text-gray-700 mb-2">
                    Axle Brand
                  </label>
                  <input
                    id="trailer_axle_brand"
                    v-model="productData.trailer_axle_brand"
                    type="text"
                    class="text-sm sm:text-base input-style"
                    placeholder="Ex: BPW, FUWA"
                  >
                </div>

                <div class="sm:col-span-2">
                  <label for="trailer_function" class="block text-sm font-medium text-gray-700 mb-2">
                    Function / Description
                  </label>
                  <textarea
                    id="trailer_function"
                    v-model="productData.trailer_function"
                    rows="3"
                    class="text-sm sm:text-base input-style"
                    placeholder="Describe the trailer's primary function and features..."
                  ></textarea>
                </div>
              </div>
            </div>
          </div>

          <!-- ÉTAPE 2: Technical Specifications (UNIQUEMENT POUR TRUCKS) OU Price & Stock (POUR TRAILERS) -->
          <div v-show="currentStep === 2" class="space-y-6">
            <!-- TRUCKS: Technical Specifications -->
            <div v-if="!isTrailerCategory && !isCarCategory" class="bg-white/80 backdrop-blur-sm rounded-xl p-4 sm:p-6 border border-gray-100 shadow-sm">
              <div class="flex items-center space-x-3 mb-4 sm:mb-6">
                <div class="w-8 h-8 rounded-lg flex items-center justify-center bg-orange">
                  <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                  </svg>
                </div>
                <h3 class="text-lg sm:text-xl font-semibold text-gray-900">Technical Specifications</h3>
              </div>

              <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 sm:gap-6">
                <div>
                  <label for="engine_brand" class="block text-sm font-medium text-gray-700 mb-2">
                    Engine brand
                  </label>
                  <select
                    id="engine_brand"
                    v-model="productData.engine_brand"
                    class="text-sm sm:text-base input-style"
                  >
                    <option value="">Select a brand</option>
                    <option value="Weichai">Weichai</option>
                    <option value="Yuchai">Yuchai</option>
                    <option value="Sinotruck">Sinotruck</option>
                    <option value="Man">MAN</option>
                  </select>
                </div>

                <div>
                  <label for="gearbox_brand" class="block text-sm font-medium text-gray-700 mb-2">
                    Gearbox Brand
                  </label>
                  <input
                    id="gearbox_brand"
                    v-model="productData.gearbox_brand"
                    type="text"
                    class="text-sm sm:text-base input-style"
                    placeholder="Ex: FAST"
                  >
                </div>

                <div>
                  <label for="engine_number" class="block text-sm font-medium text-gray-700 mb-2">
                    Engine Model
                  </label>
                  <input
                    id="engine_number"
                    v-model="productData.engine_number"
                    type="text"
                    class="text-sm sm:text-base input-style"
                    placeholder="Ex: WP12.430E201"
                  >
                </div>

                <div>
                  <label for="gearbox_model" class="block text-sm font-medium text-gray-700 mb-2">
                    Gearbox Model
                  </label>
                  <input
                    id="gearbox_model"
                    v-model="productData.gearbox_model"
                    type="text"
                    class="text-sm sm:text-base input-style"
                    placeholder="Ex: 12JSD200T-B"
                  >
                </div>

                <div>
                  <label for="power" class="block text-sm font-medium text-gray-700 mb-2">
                    Horse Power (HP)
                  </label>
                  <input
                    id="power"
                    v-model="productData.power"
                    type="number"
                    min="0"
                    class="text-sm sm:text-base input-style"
                    placeholder="Ex: 430"
                  >
                </div>

                <div>
                  <label for="transmission_type" class="block text-sm font-medium text-gray-700 mb-2">
                    Gearbox Type
                  </label>
                  <select
                    id="transmission_type"
                    v-model="productData.transmission_type"
                    class="text-sm sm:text-base input-style"
                  >
                    <option value="">Select Gearbox Type</option>
                    <option value="Automatic">Automatic</option>
                    <option value="Manual">Manual</option>
                  </select>
                </div>

                <div>
                  <label for="engine_emissions" class="block text-sm font-medium text-gray-700 mb-2">
                    Emission Standards
                  </label>
                  <input
                    id="engine_emissions"
                    v-model="productData.engine_emissions"
                    type="text"
                    class="text-sm sm:text-base input-style"
                    placeholder="Ex: Euro II"
                  >
                </div>

                <div>
                  <label for="vin_number" class="block text-sm font-medium text-gray-700 mb-2">
                    VIN Number / Chassis Number
                  </label>
                  <input
                    id="vin_number"
                    v-model="singleVin"
                    type="text"
                    class="text-sm sm:text-base input-style"
                    placeholder="Ex: 1HGCM82633A123456"
                  >
                </div>

                <div>
                  <label for="trim_number" class="block text-sm font-medium text-gray-700 mb-2">
                    Vehicle Model (Trim)
                  </label>
                  <input
                    id="trim_number"
                    v-model="singleTrim"
                    type="text"
                    class="text-sm sm:text-base input-style"
                    placeholder="Ex: SX4255JV324"
                  >
                </div>

                <div>
                  <label for="vehicle_mileage" class="block text-sm font-medium text-gray-700 mb-2">
                    Mileage (km)
                  </label>
                  <input
                    id="vehicle_mileage"
                    v-model="productData.vehicle_mileage"
                    type="number"
                    min="0"
                    max="200000"
                    class="text-sm sm:text-base input-style"
                    placeholder="Ex: 200"
                  >
                  <p class="text-xs text-gray-500 mt-1">Between 0 and 200,000 km</p>
                </div>

                <div>
                  <label for="axle_brand" class="block text-sm font-medium text-gray-700 mb-2">
                    Axle Brand
                  </label>
                  <input
                    id="axle_brand"
                    v-model="productData.axle_brand"
                    type="text"
                    class="text-sm sm:text-base input-style"
                    placeholder="Ex: HANDE"
                  >
                </div>

                <div>
                  <label for="suspension_type" class="block text-sm font-medium text-gray-700 mb-2">
                    Suspension Type
                  </label>
                  <input
                    id="suspension_type"
                    v-model="productData.suspension_type"
                    type="text"
                    class="text-sm sm:text-base input-style"
                    placeholder="Ex: Pneumatic, Mechanical"
                  >
                </div>

                <div>
                  <label for="axle_front" class="block text-sm font-medium text-gray-700 mb-2">
                    Front axle
                  </label>
                  <input
                    id="axle_front"
                    v-model="productData.axle_front"
                    type="text"
                    class="text-sm sm:text-base input-style"
                    placeholder="Ex: 7.5 T front axle HD technology"
                  >
                </div>

                <div>
                  <label for="suspension_front" class="block text-sm font-medium text-gray-700 mb-2">
                    Front Suspension
                  </label>
                  <input
                    id="suspension_front"
                    v-model="productData.suspension_front"
                    type="text"
                    class="text-sm sm:text-base input-style"
                    placeholder="Ex: Front and rear multi-plate springs"
                  >
                </div>

                <div>
                  <label for="axle_rear" class="block text-sm font-medium text-gray-700 mb-2">
                    Rear axle
                  </label>
                  <input
                    id="axle_rear"
                    v-model="productData.axle_rear"
                    type="text"
                    class="text-sm sm:text-base input-style"
                    placeholder="Ex: 16T HD Double-stage Bridge"
                  >
                </div>

                <div>
                  <label for="suspension_rear" class="block text-sm font-medium text-gray-700 mb-2">
                    Rear Suspension
                  </label>
                  <input
                    id="suspension_rear"
                    v-model="productData.suspension_rear"
                    type="text"
                    class="text-sm sm:text-base input-style"
                    placeholder="Ex: Front and rear multi-plate springs"
                  >
                </div>

                <div>
                  <label for="axle_speed_ratio" class="block text-sm font-medium text-gray-700 mb-2">
                    Speed ratio
                  </label>
                  <input
                    id="axle_speed_ratio"
                    v-model="productData.axle_speed_ratio"
                    type="number"
                    min="0"
                    step="0.001"
                    class="text-sm sm:text-base input-style"
                    placeholder="Ex: 4.266"
                  >
                </div>

                <div>
                  <label for="brake_system" class="block text-sm font-medium text-gray-700 mb-2">
                    Brake System
                  </label>
                  <input
                    id="brake_system"
                    v-model="productData.brake_system"
                    type="text"
                    class="text-sm sm:text-base input-style"
                    placeholder="Ex: ABS, EBS"
                  >
                </div>

                <div>
                  <label for="tyre_size" class="block text-sm font-medium text-gray-700 mb-2">
                    Tires
                  </label>
                  <input
                    id="tyre_size"
                    v-model="productData.tire_size"
                    type="text"
                    class="text-sm sm:text-base input-style"
                    placeholder="Ex: 12.00R20 mixed pattern"
                  >
                </div>

                <div>
                  <label for="air_filter" class="block text-sm font-medium text-gray-700 mb-2">
                    Air filter
                  </label>
                  <input
                    id="air_filter"
                    v-model="productData.air_filter"
                    type="text"
                    class="text-sm sm:text-base input-style"
                    placeholder="Ex: Ordinary side-mounted air filter"
                  >
                </div>

                <div>
                  <label for="electrics" class="block text-sm font-medium text-gray-700 mb-2">
                    Electrics
                  </label>
                  <input
                    id="electrics"
                    v-model="productData.electrics"
                    type="text"
                    class="text-sm sm:text-base input-style"
                    placeholder="Ex: 165Ah maintenance free battery"
                  >
                </div>

                <div class="sm:col-span-2">
                  <label for="wysiwygEditor2" class="block text-sm font-medium text-gray-700 mb-2">
                    Other Terms associated with the product (WYSIWYG)
                  </label>
                  <div>
                    <div class="border border-gray-300 rounded-lg focus-within:ring-1 focus-within:ring-orange-400 focus-within:border-orange-400 transition-all duration-200">
                      <div class="flex items-center gap-1 p-2 border-b border-gray-200 bg-gray-50 rounded-t-lg flex-wrap">
                        <button type="button" @click="formatText('bold')" class="p-2 hover:bg-gray-200 rounded text-sm font-bold" title="Bold" style="background-color: lightgray; color: black;">B</button>
                        <button type="button" @click="formatText('italic')" class="p-2 hover:bg-gray-200 rounded text-sm italic" title="Italic" style="background-color: lightgray; color: black;">I</button>
                        <button type="button" @click="formatText('underline')" class="p-2 hover:bg-gray-200 rounded text-sm underline" title="Underline" style="background-color: lightgray; color: black;">U</button>
                        <div class="w-px h-6 bg-gray-300 mx-1"></div>
                        <button type="button" @click="formatText('insertUnorderedList')" class="p-2 hover:bg-gray-200 rounded text-sm" title="Bullet list" style="background-color: lightgray; color: black;">•</button>
                        <button type="button" @click="formatText('insertOrderedList')" class="p-2 hover:bg-gray-200 rounded text-sm" title="Numbered list" style="background-color: lightgray; color: black;">1.</button>
                        <div class="w-px h-6 bg-gray-300 mx-1"></div>
                        <select @change="formatHeading($event)" class="text-sm border border-gray-300 rounded px-4 py-2 text-black">
                          <option value="">Title</option>
                          <option value="h1">Title 1</option>
                          <option value="h2">Title 2</option>
                          <option value="h3">Title 3</option>
                        </select>
                      </div>
                      <div
                        ref="wysiwygEditor2"
                        contenteditable="true"
                        @input="updateOtherDescription"
                        class="min-h-[200px] p-4 focus:outline-none text-black rounded-b-lg"
                        style="white-space: pre-wrap;"
                        placeholder="Provide other terms associated with your product for the search..."
                      ></div>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <!-- 🚗 CARS: Performance & Characteristics -->
            <div v-if="isCarCategory">
              <!-- Section 1: Performance -->
              <div class="bg-white/80 backdrop-blur-sm rounded-xl p-4 sm:p-6 border border-gray-100 shadow-sm mb-6">
                <div class="flex items-center space-x-3 mb-4 sm:mb-6">
                  <div class="w-8 h-8 rounded-lg flex items-center justify-center bg-orange">
                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                    </svg>
                  </div>
                  <h3 class="text-lg sm:text-xl font-semibold text-gray-900">Performance</h3>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 sm:gap-6">
                  <div>
                    <label for="car_engine_power_bhp" class="block text-sm font-medium text-gray-700 mb-2">
                      Engine Power (bhp)
                    </label>
                    <input
                      id="car_engine_power_bhp"
                      v-model.number="productData.car_engine_power_bhp"
                      type="number"
                      min="0"
                      class="text-sm sm:text-base input-style"
                      placeholder="Ex: 280"
                    >
                  </div>

                  <div>
                    <label for="car_engine_power_kw" class="block text-sm font-medium text-gray-700 mb-2">
                      Engine Power (kW)
                    </label>
                    <input
                      id="car_engine_power_kw"
                      v-model.number="productData.car_engine_power_kw"
                      type="number"
                      min="0"
                      class="text-sm sm:text-base input-style"
                      placeholder="Ex: 210"
                    >
                  </div>

                  <div>
                    <label for="car_engine_torque" class="block text-sm font-medium text-gray-700 mb-2">
                      Torque (Nm)
                    </label>
                    <input
                      id="car_engine_torque"
                      v-model.number="productData.car_engine_torque"
                      type="number"
                      min="0"
                      class="text-sm sm:text-base input-style"
                      placeholder="Ex: 450"
                    >
                  </div>

                  <div>
                    <label for="car_top_speed" class="block text-sm font-medium text-gray-700 mb-2">
                      Top Speed (km/h)
                    </label>
                    <input
                      id="car_top_speed"
                      v-model.number="productData.car_top_speed"
                      type="number"
                      min="0"
                      class="text-sm sm:text-base input-style"
                      placeholder="Ex: 250"
                    >
                  </div>

                  <div>
                    <label for="car_acceleration" class="block text-sm font-medium text-gray-700 mb-2">
                      0-100 km/h (seconds)
                    </label>
                    <input
                      id="car_acceleration"
                      v-model.number="productData.car_acceleration"
                      type="number"
                      min="0"
                      step="0.1"
                      class="text-sm sm:text-base input-style"
                      placeholder="Ex: 5.8"
                    >
                  </div>
                </div>
              </div>

              <!-- Section 2: Fuel Efficiency -->
              <div class="bg-white/80 backdrop-blur-sm rounded-xl p-4 sm:p-6 border border-gray-100 shadow-sm mb-6">
                <div class="flex items-center space-x-3 mb-4 sm:mb-6">
                  <div class="w-8 h-8 rounded-lg flex items-center justify-center bg-orange">
                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 6l3 1m0 0l-3 9a5.002 5.002 0 006.001 0M6 7l3 9M6 7l6-2m6 2l3-1m-3 1l-3 9a5.002 5.002 0 006.001 0M18 7l3 9m-3-9l-6-2m0-2v2m0 16V5m0 16H9m3 0h3"></path>
                    </svg>
                  </div>
                  <h3 class="text-lg sm:text-xl font-semibold text-gray-900">Fuel Efficiency</h3>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 sm:gap-6">
                  <div>
                    <label for="car_mpg_city" class="block text-sm font-medium text-gray-700 mb-2">
                      MPG City
                    </label>
                    <input
                      id="car_mpg_city"
                      v-model.number="productData.car_mpg_city"
                      type="number"
                      min="0"
                      step="0.1"
                      class="text-sm sm:text-base input-style"
                      placeholder="Ex: 28"
                    >
                  </div>

                  <div>
                    <label for="car_mpg_highway" class="block text-sm font-medium text-gray-700 mb-2">
                      MPG Highway
                    </label>
                    <input
                      id="car_mpg_highway"
                      v-model.number="productData.car_mpg_highway"
                      type="number"
                      min="0"
                      step="0.1"
                      class="text-sm sm:text-base input-style"
                      placeholder="Ex: 35"
                    >
                  </div>

                  <div>
                    <label for="car_mpg_combined" class="block text-sm font-medium text-gray-700 mb-2">
                      MPG Combined
                    </label>
                    <input
                      id="car_mpg_combined"
                      v-model.number="productData.car_mpg_combined"
                      type="number"
                      min="0"
                      step="0.1"
                      class="text-sm sm:text-base input-style"
                      placeholder="Ex: 31"
                    >
                  </div>

                  <div>
                    <label for="car_co2_emissions" class="block text-sm font-medium text-gray-700 mb-2">
                      CO2 Emissions (g/km)
                    </label>
                    <input
                      id="car_co2_emissions"
                      v-model.number="productData.car_co2_emissions"
                      type="number"
                      min="0"
                      class="text-sm sm:text-base input-style"
                      placeholder="Ex: 120"
                    >
                  </div>
                </div>
              </div>

              <!-- Section 3: Colors & Interior -->
              <div class="bg-white/80 backdrop-blur-sm rounded-xl p-4 sm:p-6 border border-gray-100 shadow-sm mb-6">
                <div class="flex items-center space-x-3 mb-4 sm:mb-6">
                  <div class="w-8 h-8 rounded-lg flex items-center justify-center bg-orange">
                    <PaletteIcon class="w-4 h-4 text-white" />
                  </div>
                  <h3 class="text-lg sm:text-xl font-semibold text-gray-900">Colors & Interior</h3>
                </div>

                <!-- Color Selector -->
                <div class="mb-6">
                  <label class="block text-sm font-medium text-gray-700 mb-3">Select Colors</label>
                  <div class="grid grid-cols-3 sm:grid-cols-4 lg:grid-cols-6 gap-3 mb-4">
                    <div
                      v-for="color in availableColors"
                      :key="color.value"
                      class="flex flex-col items-center space-y-2 cursor-pointer p-2 rounded-lg hover:bg-gray-50 transition-colors"
                      @click="toggleColor(color.value)"
                    >
                      <div class="relative">
                        <div
                          class="w-10 h-10 sm:w-12 sm:h-12 rounded-lg border-2 transition-all duration-200 hover:scale-110"
                          :style="{ backgroundColor: color.value }"
                          :class="productData.colors.includes(color.value) ? 'border-orange-500 ring-2 ring-orange-200' : 'border-gray-300'"
                        ></div>
                        <CheckIcon
                          v-if="productData.colors.includes(color.value)"
                          class="absolute inset-0 w-5 h-5 m-auto text-white drop-shadow-lg"
                        />
                      </div>
                      <span class="text-xs text-gray-700 text-center">{{ color.name }}</span>
                    </div>
                  </div>

                  <div class="border-t border-gray-200 pt-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Add a custom color</label>
                    <div class="flex gap-2">
                      <input
                        v-model="customColor.name"
                        type="text"
                        placeholder="Color name"
                        class="flex-1 text-sm input-style"
                      >
                      <input
                        v-model="customColor.value"
                        type="color"
                        class="w-12 h-10 border text-orange-500 border-gray-300 rounded-lg cursor-pointer"
                      >
                      <button
                        @click="addCustomColor"
                        type="button"
                        class="px-4 py-2 font-medium text-sm btn-degrade-orange"
                      >
                        Add
                      </button>
                    </div>
                  </div>
                </div>

                <!-- Interior Details -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 sm:gap-6 mt-6 pt-6 border-t border-gray-200">
                  <div>
                    <label for="car_interior_color" class="block text-sm font-medium text-gray-700 mb-2">
                      Interior Color
                    </label>
                    <input
                      id="car_interior_color"
                      v-model="productData.car_interior_color"
                      type="text"
                      class="text-sm sm:text-base input-style"
                      placeholder="Ex: Black, Beige, Gray"
                    >
                  </div>

                  <div>
                    <label for="car_interior_material" class="block text-sm font-medium text-gray-700 mb-2">
                      Interior Material
                    </label>
                    <input
                      id="car_interior_material"
                      v-model="productData.car_interior_material"
                      type="text"
                      class="text-sm sm:text-base input-style"
                      placeholder="Ex: Premium Leather, Fabric, Alcantara"
                    >
                  </div>
                </div>
              </div>

              <!-- Section 4: General Information -->
              <div class="bg-white/80 backdrop-blur-sm rounded-xl p-4 sm:p-6 border border-gray-100 shadow-sm mb-6">
                <div class="flex items-center space-x-3 mb-4 sm:mb-6">
                  <div class="w-8 h-8 rounded-lg flex items-center justify-center bg-orange">
                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                  </div>
                  <h3 class="text-lg sm:text-xl font-semibold text-gray-900">General Information</h3>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 sm:gap-6">
                  <div>
                    <label for="car_doors" class="block text-sm font-medium text-gray-700 mb-2">
                      Number of Doors
                    </label>
                    <input
                      id="car_doors"
                      v-model.number="productData.car_doors"
                      type="number"
                      min="2"
                      max="5"
                      class="text-sm sm:text-base input-style"
                      placeholder="Ex: 4"
                    >
                  </div>

                  <div>
                    <label for="car_seats" class="block text-sm font-medium text-gray-700 mb-2">
                      Number of Seats
                    </label>
                    <input
                      id="car_seats"
                      v-model.number="productData.car_seats"
                      type="number"
                      min="2"
                      max="9"
                      class="text-sm sm:text-base input-style"
                      placeholder="Ex: 5"
                    >
                  </div>

                  <div>
                    <label for="car_warranty_years" class="block text-sm font-medium text-gray-700 mb-2">
                      Warranty (Years)
                    </label>
                    <input
                      id="car_warranty_years"
                      v-model.number="productData.car_warranty_years"
                      type="number"
                      min="0"
                      class="text-sm sm:text-base input-style"
                      placeholder="Ex: 3"
                    >
                  </div>

                  <div>
                    <label for="car_warranty_miles" class="block text-sm font-medium text-gray-700 mb-2">
                      Warranty (Miles/km)
                    </label>
                    <input
                      id="car_warranty_miles"
                      v-model.number="productData.car_warranty_miles"
                      type="number"
                      min="0"
                      class="text-sm sm:text-base input-style"
                      placeholder="Ex: 60000"
                    >
                  </div>

                  <div>
                    <label for="car_insurance_group" class="block text-sm font-medium text-gray-700 mb-2">
                      Insurance Group
                    </label>
                    <input
                      id="car_insurance_group"
                      v-model="productData.car_insurance_group"
                      type="text"
                      class="text-sm sm:text-base input-style"
                      placeholder="Ex: 29E"
                    >
                  </div>

                  <div>
                    <label for="car_previous_owners" class="block text-sm font-medium text-gray-700 mb-2">
                      Previous Owners
                    </label>
                    <input
                      id="car_previous_owners"
                      v-model.number="productData.car_previous_owners"
                      type="number"
                      min="0"
                      class="text-sm sm:text-base input-style"
                      placeholder="Ex: 0"
                    >
                  </div>

                  <div class="sm:col-span-2">
                    <label for="car_service_history" class="block text-sm font-medium text-gray-700 mb-2">
                      Service History
                    </label>
                    <textarea
                      id="car_service_history"
                      v-model="productData.car_service_history"
                      rows="3"
                      class="text-sm sm:text-base input-style"
                      placeholder="Ex: Full service history available. Last service: January 2024."
                    ></textarea>
                  </div>
                </div>
              </div>
            </div>

            <!-- TRAILERS: Price & Stock (à l'étape 2 pour les trailers) -->
            <div v-if="isTrailerCategory" class="bg-white/80 backdrop-blur-sm rounded-xl p-4 sm:p-6 border border-gray-100 shadow-sm">
              <div class="flex items-center space-x-3 mb-4 sm:mb-6">
                <div class="w-8 h-8 rounded-lg flex items-center justify-center bg-orange">
                  <DollarSignIcon class="w-4 h-4 text-white" />
                </div>
                <h3 class="text-lg sm:text-xl font-semibold text-gray-900">Price and Stock</h3>
              </div>

              <div class="grid grid-cols-1 sm:grid-cols-2 gap-1 sm:gap-6">
                <div>
                  <label for="unit_price" class="block text-sm font-medium text-gray-700 mb-2">
                    Unit Price (FOB) <span class="error-color">*</span>
                  </label>
                  <input
                    id="unit_price"
                    v-model.number="productData.unit_price"
                    title="Enter a number"
                    type="number"
                    min="0"
                    step="1"
                    required
                    class="text-sm sm:text-base input-style"
                    placeholder="15000"
                  >
                  <p v-if="errors.message" class="text-xs text-red-600 ml-5">{{ errors.message }}</p>
                </div>

                <div>
                  <label for="available" class="block text-sm font-medium text-gray-700 mb-2">
                    Product availability
                  </label>
                  <select
                    id="available"
                    v-model="productData.disponibility"
                    class="text-sm sm:text-base input-style"
                  >
                    <option v-for="available in availability" :key="available.value" :value="available.value">
                      {{ available.label }}
                    </option>
                  </select>
                </div>
              </div>
            </div>
          </div>

          <!-- ÉTAPE 3: Price & Stock (TRUCKS) OU Colors (TRAILERS) -->
          <div v-show="currentStep === 3" class="space-y-6">
            <!-- TRUCKS & CARS: Price & Stock -->
            <div v-if="!isTrailerCategory" class="bg-white/80 backdrop-blur-sm rounded-xl p-4 sm:p-6 border border-gray-100 shadow-sm">
              <div class="flex items-center space-x-3 mb-4 sm:mb-6">
                <div class="w-8 h-8 rounded-lg flex items-center justify-center bg-orange">
                  <DollarSignIcon class="w-4 h-4 text-white" />
                </div>
                <h3 class="text-lg sm:text-xl font-semibold text-gray-900">Price and Stock</h3>
              </div>

              <div class="grid grid-cols-1 sm:grid-cols-2 gap-1 sm:gap-6">
                <div>
                  <label for="unit_price_truck" class="block text-sm font-medium text-gray-700 mb-2">
                    Unit Price (FOB) <span class="error-color">*</span>
                  </label>
                  <input
                    id="unit_price_truck"
                    v-model.number="productData.unit_price"
                    title="Enter a number"
                    type="number"
                    min="0"
                    step="1"
                    required
                    class="text-sm sm:text-base input-style"
                    placeholder="15000"
                  >
                  <p v-if="errors.message" class="text-xs text-red-600 ml-5">{{ errors.message }}</p>
                </div>

                <div>
                  <label for="available_truck" class="block text-sm font-medium text-gray-700 mb-2">
                    Product availability
                  </label>
                  <select
                    id="available_truck"
                    v-model="productData.disponibility"
                    class="text-sm sm:text-base input-style"
                  >
                    <option v-for="available in availability" :key="available.value" :value="available.value">
                      {{ available.label }}
                    </option>
                  </select>
                </div>
              </div>
            </div>

            <!-- 🚗 CARS: Images (à l'étape 3 pour les voitures) -->
            <div v-if="isCarCategory" class="bg-white/80 backdrop-blur-sm rounded-xl p-4 sm:p-6 border border-gray-100 shadow-sm">
              <div class="flex items-center space-x-3 mb-4 sm:mb-6">
                <div class="w-8 h-8 rounded-lg flex items-center justify-center bg-orange">
                  <ImageIcon class="w-4 h-4 text-white" />
                </div>
                <h3 class="text-lg sm:text-xl font-semibold text-gray-900">Product images</h3>
              </div>

              <div class="border-2 border-dashed border-gray-300 rounded-xl p-6 sm:p-8 text-center hover:border-orange-400 transition-colors">
                <div class="w-12 h-12 sm:w-16 sm:h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                  <UploadIcon class="w-6 h-6 sm:w-8 sm:h-8 text-gray-400" />
                </div>
                <p class="text-sm sm:text-base text-gray-600 mb-2">Drag your images here or</p>
                <input
                  ref="fileInput"
                  type="file"
                  multiple
                  accept="image/*"
                  @change="handleImageUpload"
                  class="hidden"
                >
                <button
                  type="button"
                  @click="$refs.fileInput.click()"
                  style="display: inline-block;"
                  class="btn-degrade-orange"
                >
                  Browse the files
                </button>
                <p class="text-xs text-gray-500 mt-2">PNG, JPG up to 10MB per image (max 35)</p>
              </div>

              <div v-if="productData.images.length > 0" class="mt-6">
                <h4 class="text-sm font-medium text-gray-700 mb-3">Selected images</h4>
                <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-4">
                  <div
                    v-for="(image, index) in productData.images"
                    :key="index"
                    class="relative group"
                  >
                    <img
                      :src="image.preview"
                      :alt="`Image ${index + 1}`"
                      class="w-full h-24 sm:h-32 object-cover rounded-lg border border-gray-200"
                    >

                    <button
                      style="background-color: red;"
                      type="button"
                      @click="removeImage(index)"
                      class="absolute top-2 right-2 bg-red-500 text-white text-xs rounded-md p-1"
                    >
                      <XIcon class="w-4 h-4" />
                    </button>

                    <button
                      style="background-color: gray; font-size: 10px"
                      v-if="index !== 0"
                      @click="setMainImage(index)"
                      type="button"
                      class="absolute bottom-2 right-2 px-2 py-1 bg-blue-600 text-white text-xs rounded-md group-hover:opacity-100 transition"
                    >
                      Set Main
                    </button>

                    <div
                      v-if="index === 0"
                      class="absolute bottom-2 left-2 px-2 py-1 bg-orange text-white text-xs rounded-md"
                    >
                      Main
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <!-- 🚗 CARS: Video (à l'étape 3 pour les voitures) -->
            <div v-if="isCarCategory" class="bg-white/80 backdrop-blur-sm rounded-xl p-4 sm:p-6 border border-gray-100 shadow-sm">
              <div class="flex items-center space-x-3 mb-4 sm:mb-6">
                <div class="w-8 h-8 rounded-lg flex items-center justify-center bg-orange">
                  <VideoIcon class="w-4 h-4 text-white" />
                </div>
                <h3 class="text-lg sm:text-xl font-semibold text-gray-900">Video (optional)</h3>
              </div>

              <div class="border-2 border-dashed border-gray-300 rounded-xl p-6 text-center hover:border-orange-400 transition-colors">
                <VideoIcon class="mx-auto h-12 w-12 text-gray-400 mb-4" />
                <div>
                  <label for="video-upload-car" class="cursor-pointer">
                    <span class="block text-sm text-gray-600 mb-2">
                      Click to download a video
                    </span>
                    <span class="block text-xs text-gray-500">
                      MP4, MOV, AVI files up to 50MB
                    </span>
                  </label>
                  <input
                    id="video-upload-car"
                    type="file"
                    accept="video/*"
                    @change="handleVideoUpload"
                    class="sr-only"
                  >
                </div>
              </div>

              <div v-if="productData.video" class="mt-6">
                <video :src="productData.video.preview" controls class="w-full max-w-md mx-auto rounded-lg shadow-lg">
                  Your browser does not support video playback.
                </video>

                <div v-if="productData.video.uploading" class="mt-4 bg-blue-50 border border-blue-200 rounded-lg p-3">
                  <div class="flex items-center space-x-2">
                    <div class="animate-spin w-4 h-4 border-2 border-orange-400 border-t-transparent rounded-full"></div>
                    <span class="text-sm text-gray-700">Downloading the video: {{ productData.video.uploadProgress }}%</span>
                  </div>
                </div>

                <div v-if="productData.video.uploaded" class="mt-4 bg-green-50 border border-green-200 rounded-lg p-3 flex items-center space-x-2">
                  <CheckIcon class="w-4 h-4 text-green-600" />
                  <span class="text-sm text-green-700">Video successfully downloaded</span>
                </div>

                <button
                  @click="removeVideo"
                  type="button"
                  class="mt-4 px-4 py-2 bg-red-500 text-white rounded-lg hover:bg-red-600 transition-colors font-medium"
                >
                  Delete video
                </button>
              </div>
            </div>

            <!-- 🚗 CARS: Publish Option (à l'étape 3 pour les voitures) -->
            <div v-if="isCarCategory" class="bg-white/80 backdrop-blur-sm rounded-xl p-4 sm:p-6 border border-gray-100 shadow-sm">
              <div class="flex items-center mb-6">
                <input
                  v-model="productData.is_active"
                  id="is-active-car"
                  type="checkbox"
                  class="checkbox-style"
                >
                <label for="is-active-car" class="ml-3 text-sm font-medium text-gray-700 flex items-center">
                  <CheckCircleIcon class="w-4 h-4 text-green-600 mr-1" />
                  Publish this product immediately
                </label>
              </div>
            </div>

            <!-- TRAILERS: Colors -->
            <div v-if="isTrailerCategory" class="bg-white/80 backdrop-blur-sm rounded-xl p-4 sm:p-6 border border-gray-100 shadow-sm">
              <div class="flex items-center space-x-3 mb-4 sm:mb-6">
                <div class="w-8 h-8 rounded-lg flex items-center justify-center bg-orange">
                  <PaletteIcon class="w-4 h-4 text-white" />
                </div>
                <h3 class="text-lg sm:text-xl font-semibold text-gray-900">Colors</h3>
              </div>

              <div class="grid grid-cols-3 sm:grid-cols-4 lg:grid-cols-6 gap-3 mb-6">
                <div
                  v-for="color in availableColors"
                  :key="color.value"
                  class="flex flex-col items-center space-y-2 cursor-pointer p-2 rounded-lg hover:bg-gray-50 transition-colors"
                  @click="toggleColor(color.value)"
                >
                  <div class="relative">
                    <div
                      class="w-10 h-10 sm:w-12 sm:h-12 rounded-lg border-2 transition-all duration-200 hover:scale-110"
                      :style="{ backgroundColor: color.value }"
                      :class="productData.colors.includes(color.value) ? 'border-orange-500 ring-2 ring-orange-200' : 'border-gray-300'"
                    ></div>
                    <CheckIcon
                      v-if="productData.colors.includes(color.value)"
                      class="absolute inset-0 w-5 h-5 m-auto text-white drop-shadow-lg"
                    />
                  </div>
                  <span class="text-xs text-gray-700 text-center">{{ color.name }}</span>
                </div>
              </div>

              <div class="border-t border-gray-200 pt-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">Add a custom color</label>
                <div class="flex gap-2">
                  <input
                    v-model="customColor.name"
                    type="text"
                    placeholder="Color name"
                    class="flex-1 text-sm input-style"
                  >
                  <input
                    v-model="customColor.value"
                    type="color"
                    class="w-12 h-10 border text-orange-500 border-gray-300 rounded-lg cursor-pointer"
                  >
                  <button
                    @click="addCustomColor"
                    type="button"
                    class="px-4 py-2 font-medium text-sm btn-degrade-orange"
                  >
                    Add
                  </button>
                </div>
              </div>
            </div>
          </div>

          <!-- ÉTAPE 4: Colors (TRUCKS) OU Pictures (TRAILERS) -->
          <div v-show="currentStep === 4" class="space-y-6">
            <!-- TRUCKS: Colors -->
            <div v-if="!isTrailerCategory" class="bg-white/80 backdrop-blur-sm rounded-xl p-4 sm:p-6 border border-gray-100 shadow-sm">
              <div class="flex items-center space-x-3 mb-4 sm:mb-6">
                <div class="w-8 h-8 rounded-lg flex items-center justify-center bg-orange">
                  <PaletteIcon class="w-4 h-4 text-white" />
                </div>
                <h3 class="text-lg sm:text-xl font-semibold text-gray-900">Colors</h3>
              </div>

              <div class="grid grid-cols-3 sm:grid-cols-4 lg:grid-cols-6 gap-3 mb-6">
                <div
                  v-for="color in availableColors"
                  :key="color.value"
                  class="flex flex-col items-center space-y-2 cursor-pointer p-2 rounded-lg hover:bg-gray-50 transition-colors"
                  @click="toggleColor(color.value)"
                >
                  <div class="relative">
                    <div
                      class="w-10 h-10 sm:w-12 sm:h-12 rounded-lg border-2 transition-all duration-200 hover:scale-110"
                      :style="{ backgroundColor: color.value }"
                      :class="productData.colors.includes(color.value) ? 'border-orange-500 ring-2 ring-orange-200' : 'border-gray-300'"
                    ></div>
                    <CheckIcon
                      v-if="productData.colors.includes(color.value)"
                      class="absolute inset-0 w-5 h-5 m-auto text-white drop-shadow-lg"
                    />
                  </div>
                  <span class="text-xs text-gray-700 text-center">{{ color.name }}</span>
                </div>
              </div>

              <div class="border-t border-gray-200 pt-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">Add a custom color</label>
                <div class="flex gap-2">
                  <input
                    v-model="customColor.name"
                    type="text"
                    placeholder="Color name"
                    class="flex-1 text-sm input-style"
                  >
                  <input
                    v-model="customColor.value"
                    type="color"
                    class="w-12 h-10 border text-orange-500 border-gray-300 rounded-lg cursor-pointer"
                  >
                  <button
                    @click="addCustomColor"
                    type="button"
                    class="px-4 py-2 font-medium text-sm btn-degrade-orange"
                  >
                    Add
                  </button>
                </div>
              </div>
            </div>

            <!-- TRAILERS: Pictures (images, video, summary) -->
            <div v-if="isTrailerCategory">
              <!-- Images -->
              <div class="bg-white/80 backdrop-blur-sm rounded-xl p-4 sm:p-6 border border-gray-100 shadow-sm mb-6">
                <div class="flex items-center space-x-3 mb-4 sm:mb-6">
                  <div class="w-8 h-8 rounded-lg flex items-center justify-center bg-orange">
                    <ImageIcon class="w-4 h-4 text-white" />
                  </div>
                  <h3 class="text-lg sm:text-xl font-semibold text-gray-900">Product images</h3>
                </div>

                <div class="border-2 border-dashed border-gray-300 rounded-xl p-6 sm:p-8 text-center hover:border-orange-400 transition-colors">
                  <div class="w-12 h-12 sm:w-16 sm:h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <UploadIcon class="w-6 h-6 sm:w-8 sm:h-8 text-gray-400" />
                  </div>
                  <p class="text-sm sm:text-base text-gray-600 mb-2">Drag your images here or</p>
                  <input
                    ref="fileInput"
                    type="file"
                    multiple
                    accept="image/*"
                    @change="handleImageUpload"
                    class="hidden"
                  >
                  <button
                    type="button"
                    @click="$refs.fileInput.click()"
                    style="display: inline-block;"
                    class="btn-degrade-orange"
                  >
                    Browse the files
                  </button>
                  <p class="text-xs text-gray-500 mt-2">PNG, JPG up to 10MB per image (max 35)</p>
                </div>

                <div v-if="productData.images.length > 0" class="mt-6">
                  <h4 class="text-sm font-medium text-gray-700 mb-3">Selected images</h4>
                  <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-4">
                    <div
                      v-for="(image, index) in productData.images"
                      :key="index"
                      class="relative group"
                    >
                      <img
                        :src="image.preview"
                        :alt="`Image ${index + 1}`"
                        class="w-full h-24 sm:h-32 object-cover rounded-lg border border-gray-200"
                      >

                      <button
                        style="background-color: red;"
                        type="button"
                        @click="removeImage(index)"
                        class="absolute top-2 right-2 bg-red-500 text-white text-xs rounded-md p-1"
                      >
                        <XIcon class="w-4 h-4" />
                      </button>

                      <button
                        style="background-color: gray; font-size: 10px"
                        v-if="index !== 0"
                        @click="setMainImage(index)"
                        type="button"
                        class="absolute bottom-2 right-2 px-2 py-1 bg-blue-600 text-white text-xs rounded-md group-hover:opacity-100 transition"
                      >
                        Set Main
                      </button>

                      <div
                        v-if="index === 0"
                        class="absolute bottom-2 left-2 px-2 py-1 bg-orange text-white text-xs rounded-md"
                      >
                        Main
                      </div>
                    </div>
                  </div>
                </div>
              </div>

              <!-- Video -->
              <div class="bg-white/80 backdrop-blur-sm rounded-xl p-4 sm:p-6 border border-gray-100 shadow-sm mb-6">
                <div class="flex items-center space-x-3 mb-4 sm:mb-6">
                  <div class="w-8 h-8 rounded-lg flex items-center justify-center bg-orange">
                    <VideoIcon class="w-4 h-4 text-white" />
                  </div>
                  <h3 class="text-lg sm:text-xl font-semibold text-gray-900">Video (optional)</h3>
                </div>

                <div class="border-2 border-dashed border-gray-300 rounded-xl p-6 text-center hover:border-orange-400 transition-colors">
                  <VideoIcon class="mx-auto h-12 w-12 text-gray-400 mb-4" />
                  <div>
                    <label for="video-upload" class="cursor-pointer">
                      <span class="block text-sm text-gray-600 mb-2">
                        Click to download a video
                      </span>
                      <span class="block text-xs text-gray-500">
                        MP4, MOV, AVI files up to 50MB
                      </span>
                    </label>
                    <input
                      id="video-upload"
                      type="file"
                      accept="video/*"
                      @change="handleVideoUpload"
                      class="sr-only"
                    >
                  </div>
                </div>

                <div v-if="productData.video" class="mt-6">
                  <video :src="productData.video.preview" controls class="w-full max-w-md mx-auto rounded-lg shadow-lg">
                    Your browser does not support video playback.
                  </video>

                  <div v-if="productData.video.uploading" class="mt-4 bg-blue-50 border border-blue-200 rounded-lg p-3">
                    <div class="flex items-center space-x-2">
                      <div class="animate-spin w-4 h-4 border-2 border-orange-400 border-t-transparent rounded-full"></div>
                      <span class="text-sm text-gray-700">Downloading the video: {{ productData.video.uploadProgress }}%</span>
                    </div>
                  </div>

                  <div v-if="productData.video.uploaded" class="mt-4 bg-green-50 border border-green-200 rounded-lg p-3 flex items-center space-x-2">
                    <CheckIcon class="w-4 h-4 text-green-600" />
                    <span class="text-sm text-green-700">Video successfully downloaded</span>
                  </div>

                  <button
                    @click="removeVideo"
                    type="button"
                    class="mt-4 px-4 py-2 bg-red-500 text-white rounded-lg hover:bg-red-600 transition-colors font-medium"
                  >
                    Delete video
                  </button>
                </div>
              </div>

              <!-- Summary -->
              <div class="bg-white/80 backdrop-blur-sm rounded-xl p-4 sm:p-6 border border-gray-100 shadow-sm">
                <div class="flex items-center mb-6">
                  <input
                    v-model="productData.is_active"
                    id="is-active-trailer"
                    type="checkbox"
                    class="checkbox-style"
                  >
                  <label for="is-active-trailer" class="ml-3 text-sm font-medium text-gray-700 flex items-center">
                    <CheckCircleIcon class="w-4 h-4 text-green-600 mr-1" />
                    Publish this product immediately
                  </label>
                </div>

                <div class="bg-gray-50 rounded-lg p-4">
                  <h4 class="font-semibold text-gray-900 mb-4 flex items-center">
                    <FileTextIcon class="w-5 h-5 mr-2 primary-color" />
                    Product summary
                  </h4>
                  <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-sm">
                    <div class="space-y-2">
                      <p><span class="font-medium text-gray-700">Product name:</span> <span class="text-gray-900">{{ productData.name || 'Non défini' }}</span></p>
                      <p><span class="font-medium text-gray-700">Category:</span> <span class="text-gray-900">{{ getCategoryName(productData.category_id) || 'Non définie' }}</span></p>
                      <p><span class="font-medium text-gray-700">Unit price (FOB):</span> <span class="text-gray-900">{{ productData.unit_price ? formatPrice(productData.unit_price) : 'Non défini' }}</span></p>
                    </div>
                    <div class="space-y-2">
                      <p><span class="font-medium text-gray-700">Product availability:</span> <span class="text-gray-900">{{ productData.disponibility }}</span></p>
                      <p><span class="font-medium text-gray-700">Tags:</span> <span class="text-gray-900">{{ productData.tags }}</span></p>
                      <p><span class="font-medium text-gray-700">Images:</span> <span class="text-gray-900">{{ productData.images.length }}/35</span></p>
                      <p><span class="font-medium text-gray-700">Video:</span> <span class="text-gray-900">{{ productData.video ? 'Oui' : 'Non' }}</span></p>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- ÉTAPE 5: Pictures (UNIQUEMENT POUR TRUCKS) -->
          <div v-show="currentStep === 5 && !isTrailerCategory" class="space-y-6">
            <!-- Images -->
            <div class="bg-white/80 backdrop-blur-sm rounded-xl p-4 sm:p-6 border border-gray-100 shadow-sm">
              <div class="flex items-center space-x-3 mb-4 sm:mb-6">
                <div class="w-8 h-8 rounded-lg flex items-center justify-center bg-orange">
                  <ImageIcon class="w-4 h-4 text-white" />
                </div>
                <h3 class="text-lg sm:text-xl font-semibold text-gray-900">Product images</h3>
              </div>

              <div class="border-2 border-dashed border-gray-300 rounded-xl p-6 sm:p-8 text-center hover:border-orange-400 transition-colors">
                <div class="w-12 h-12 sm:w-16 sm:h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                  <UploadIcon class="w-6 h-6 sm:w-8 sm:h-8 text-gray-400" />
                </div>
                <p class="text-sm sm:text-base text-gray-600 mb-2">Drag your images here or</p>
                <input
                  ref="fileInput"
                  type="file"
                  multiple
                  accept="image/*"
                  @change="handleImageUpload"
                  class="hidden"
                >
                <button
                  type="button"
                  @click="$refs.fileInput.click()"
                  style="display: inline-block;"
                  class="btn-degrade-orange"
                >
                  Browse the files
                </button>
                <p class="text-xs text-gray-500 mt-2">PNG, JPG up to 10MB per image (max 35)</p>
              </div>

              <div v-if="productData.images.length > 0" class="mt-6">
                <h4 class="text-sm font-medium text-gray-700 mb-3">Selected images</h4>
                <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-4">
                  <div
                    v-for="(image, index) in productData.images"
                    :key="index"
                    class="relative group"
                  >
                    <img
                      :src="image.preview"
                      :alt="`Image ${index + 1}`"
                      class="w-full h-24 sm:h-32 object-cover rounded-lg border border-gray-200"
                    >

                    <button
                      style="background-color: red;"
                      type="button"
                      @click="removeImage(index)"
                      class="absolute top-2 right-2 bg-red-500 text-white text-xs rounded-md p-1"
                    >
                      <XIcon class="w-4 h-4" />
                    </button>

                    <button
                      style="background-color: gray; font-size: 10px"
                      v-if="index !== 0"
                      @click="setMainImage(index)"
                      type="button"
                      class="absolute bottom-2 right-2 px-2 py-1 bg-blue-600 text-white text-xs rounded-md group-hover:opacity-100 transition"
                    >
                      Set Main
                    </button>

                    <div
                      v-if="index === 0"
                      class="absolute bottom-2 left-2 px-2 py-1 bg-orange text-white text-xs rounded-md"
                    >
                      Main
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <!-- Video -->
            <div class="bg-white/80 backdrop-blur-sm rounded-xl p-4 sm:p-6 border border-gray-100 shadow-sm">
              <div class="flex items-center space-x-3 mb-4 sm:mb-6">
                <div class="w-8 h-8 rounded-lg flex items-center justify-center bg-orange">
                  <VideoIcon class="w-4 h-4 text-white" />
                </div>
                <h3 class="text-lg sm:text-xl font-semibold text-gray-900">Video (optional)</h3>
              </div>

              <div class="border-2 border-dashed border-gray-300 rounded-xl p-6 text-center hover:border-orange-400 transition-colors">
                <VideoIcon class="mx-auto h-12 w-12 text-gray-400 mb-4" />
                <div>
                  <label for="video-upload-truck" class="cursor-pointer">
                    <span class="block text-sm text-gray-600 mb-2">
                      Click to download a video
                    </span>
                    <span class="block text-xs text-gray-500">
                      MP4, MOV, AVI files up to 50MB
                    </span>
                  </label>
                  <input
                    id="video-upload-truck"
                    type="file"
                    accept="video/*"
                    @change="handleVideoUpload"
                    class="sr-only"
                  >
                </div>
              </div>

              <div v-if="productData.video" class="mt-6">
                <video :src="productData.video.preview" controls class="w-full max-w-md mx-auto rounded-lg shadow-lg">
                  Your browser does not support video playback.
                </video>

                <div v-if="productData.video.uploading" class="mt-4 bg-blue-50 border border-blue-200 rounded-lg p-3">
                  <div class="flex items-center space-x-2">
                    <div class="animate-spin w-4 h-4 border-2 border-orange-400 border-t-transparent rounded-full"></div>
                    <span class="text-sm text-gray-700">Downloading the video: {{ productData.video.uploadProgress }}%</span>
                  </div>
                </div>

                <div v-if="productData.video.uploaded" class="mt-4 bg-green-50 border border-green-200 rounded-lg p-3 flex items-center space-x-2">
                  <CheckIcon class="w-4 h-4 text-green-600" />
                  <span class="text-sm text-green-700">Video successfully downloaded</span>
                </div>

                <button
                  @click="removeVideo"
                  type="button"
                  class="mt-4 px-4 py-2 bg-red-500 text-white rounded-lg hover:bg-red-600 transition-colors font-medium"
                >
                  Delete video
                </button>
              </div>
            </div>

            <!-- Summary (Trucks) -->
            <div class="bg-white/80 backdrop-blur-sm rounded-xl p-4 sm:p-6 border border-gray-100 shadow-sm">
              <div class="flex items-center mb-6">
                <input
                  v-model="productData.is_active"
                  id="is-active"
                  type="checkbox"
                  class="checkbox-style"
                >
                <label for="is-active" class="ml-3 text-sm font-medium text-gray-700 flex items-center">
                  <CheckCircleIcon class="w-4 h-4 text-green-600 mr-1" />
                  Publish this product immediately
                </label>
              </div>

              <div class="bg-gray-50 rounded-lg p-4 mb-4">
                <h4 class="font-semibold text-gray-900 mb-4 flex items-center">
                  <FileTextIcon class="w-5 h-5 mr-2 primary-color" />
                  Product summary
                </h4>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-sm">
                  <div class="space-y-2">
                    <p><span class="font-medium text-gray-700">Product name:</span> <span class="text-gray-900">{{ productData.name || 'Non défini' }}</span></p>
                    <p><span class="font-medium text-gray-700">Category:</span> <span class="text-gray-900">{{ getCategoryName(productData.category_id) || 'Non définie' }}</span></p>
                    <p><span class="font-medium text-gray-700">Unit price (FOB):</span> <span class="text-gray-900">{{ productData.unit_price ? formatPrice(productData.unit_price) : 'Non défini' }}</span></p>
                  </div>
                  <div class="space-y-2">
                    <p><span class="font-medium text-gray-700">Product availability:</span> <span class="text-gray-900">{{ productData.disponibility }}</span></p>
                    <p><span class="font-medium text-gray-700">Tags:</span> <span class="text-gray-900">{{ productData.tags }}</span></p>
                    <p><span class="font-medium text-gray-700">Images:</span> <span class="text-gray-900">{{ productData.images.length }}/35</span></p>
                    <p><span class="font-medium text-gray-700">Video:</span> <span class="text-gray-900">{{ productData.video ? 'Oui' : 'Non' }}</span></p>
                  </div>
                </div>
              </div>

              <!-- Specifications Table (Trucks only) -->
              <div class="specifications-table two-columns">
                <div class="spec-group">
                  <h3 class="spec-group-title">Overviews</h3>
                  <div class="spec-row">
                    <div class="spec-name">Vehicle Brand</div>
                    <div class="spec-value">{{ getBrandName(productData.vehicle_brand_id) || 'N/A' }}</div>
                  </div>
                  <div class="spec-row">
                    <div class="spec-name">Vehicle Model</div>
                    <div class="spec-value">{{ getModelName(productData.vehicle_model_id) || 'N/A' }}</div>
                  </div>
                  <div class="spec-row">
                    <div class="spec-name">Vehicle Condition</div>
                    <div class="spec-value">{{ productData.vehicle_condition || 'N/A' }}</div>
                  </div>
                  <div class="spec-row">
                    <div class="spec-name">Year</div>
                    <div class="spec-value">{{ productData.vehicle_year || 'N/A' }}</div>
                  </div>
                  <div class="spec-row">
                    <div class="spec-name">Production date</div>
                    <div class="spec-value">{{ productData.production_date || 'N/A' }}</div>
                  </div>
                  <div class="spec-row">
                    <div class="spec-name">Country of origin</div>
                    <div class="spec-value">{{ productData.country_of_origin || 'N/A' }}</div>
                  </div>
                  <div class="spec-row">
                    <div class="spec-name">Fuel type</div>
                    <div class="spec-value">{{ productData.fuel_type || 'N/A' }}</div>
                  </div>
                  <div class="spec-row">
                    <div class="spec-name">Fuel Tank Capacity (L)</div>
                    <div class="spec-value">{{ productData.fuel_tank_capacity || 'N/A' }}</div>
                  </div>
                  <div class="spec-row">
                    <div class="spec-name">Transmission Type</div>
                    <div class="spec-value">{{ productData.drive_type || 'N/A' }}</div>
                  </div>
                  <div class="spec-row">
                    <div class="spec-name">Curb Weight (kg)</div>
                    <div class="spec-value">{{ productData.curb_weight || 'N/A' }}</div>
                  </div>
                  <div class="spec-row">
                    <div class="spec-name">Payload Capacity (kg)</div>
                    <div class="spec-value">{{ productData.payload_capacity || 'N/A' }}</div>
                  </div>
                  <div class="spec-row">
                    <div class="spec-name">GVW - Gross Vehicle Weight (kg)</div>
                    <div class="spec-value">{{ productData.gvw || 'N/A' }}</div>
                  </div>
                  <div class="spec-row">
                    <div class="spec-name">Wheelbases (mm)</div>
                    <div class="spec-value">{{ productData.wheelbase || 'N/A' }}</div>
                  </div>
                  <div class="spec-row">
                    <div class="spec-name">Economic speed / Maximum speed (km/h)</div>
                    <div class="spec-value">{{ productData.speed || 'N/A' }}</div>
                  </div>
                  <div class="spec-row">
                    <div class="spec-name">Cabin Type</div>
                    <div class="spec-value">{{ productData.cabin_type || 'N/A' }}</div>
                  </div>
                  <div class="spec-row">
                    <div class="spec-name">Chassis Dimensions (mm)</div>
                    <div class="spec-value">{{ productData.chassis_dimensions || 'N/A' }}</div>
                  </div>
                  <div class="spec-row">
                    <div class="spec-name">Frame rear suspension (mm)</div>
                    <div class="spec-value">{{ productData.frame_rear_suspension || 'N/A' }}</div>
                  </div>
                  <div class="spec-row">
                    <div class="spec-name">Overall Dimensions LxlxH (mm)</div>
                    <div class="spec-value">{{ `${productData.dimensions_length || 0}x${productData.dimensions_width || 0}x${productData.dimensions_height || 0}` }}</div>
                  </div>
                  <div class="spec-row overflow-x-auto items-center">
                    <div class="spec-name">Colors</div>
                    <div v-for="color in productData.colors" :key="color.hex_value">
                      <div class="spec-value">
                        <div>
                          <span
                            class="inline-block w-6 h-6 rounded-full border border-gray-300 mr-2"
                            :style="{ backgroundColor: color || '#FFFFFF' }"
                          ></span>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>

                <div class="spec-group">
                  <h3 class="spec-group-title">Technical Specifications</h3>
                  <div class="spec-row">
                    <div class="spec-name">Engine brand</div>
                    <div class="spec-value">{{ productData.engine_brand || 'N/A' }}</div>
                  </div>
                  <div class="spec-row">
                    <div class="spec-name">Engine Model</div>
                    <div class="spec-value">{{ productData.engine_number || 'N/A' }}</div>
                  </div>
                  <div class="spec-row">
                    <div class="spec-name">Horse Power (HP)</div>
                    <div class="spec-value">{{ productData.power || 'N/A' }}</div>
                  </div>
                  <div class="spec-row">
                    <div class="spec-name">Emission Standards</div>
                    <div class="spec-value">{{ productData.engine_emissions || 'N/A' }}</div>
                  </div>
                  <div class="spec-row">
                    <div class="spec-name">Gearbox Brand</div>
                    <div class="spec-value">{{ productData.gearbox_brand || 'N/A' }}</div>
                  </div>
                  <div class="spec-row">
                    <div class="spec-name">Gearbox Model</div>
                    <div class="spec-value">{{ productData.gearbox_model || 'N/A' }}</div>
                  </div>
                  <div class="spec-row">
                    <div class="spec-name">Gearbox Type</div>
                    <div class="spec-value">{{ productData.transmission_type || 'N/A' }}</div>
                  </div>
                  <div class="spec-row">
                    <div class="spec-name">VIN Number / Chassis Number</div>
                    <div class="spec-value">
                      <ul v-if="productData.vin && productData.vin.length">
                        <li v-for="vin in productData.vin" :key="vin.id">
                          {{ vin }}
                        </li>
                      </ul>
                      <span v-else>N/A</span>
                    </div>
                  </div>
                  <div class="spec-row">
                    <div class="spec-name">Vehicle Model (Trim)</div>
                    <div class="spec-value">
                      <ul v-if="productData.trim_numbers && productData.trim_numbers.length">
                        <li v-for="trim in productData.trim_numbers" :key="trim.id">
                          {{ trim }}
                        </li>
                      </ul>
                      <span v-else>N/A</span>
                    </div>
                  </div>
                  <div class="spec-row">
                    <div class="spec-name">Mileage (km)</div>
                    <div class="spec-value">{{ productData.vehicle_mileage || 'N/A' }}</div>
                  </div>
                  <div class="spec-row">
                    <div class="spec-name">Axle Brand</div>
                    <div class="spec-value">{{ productData.axle_brand || 'N/A' }}</div>
                  </div>
                  <div class="spec-row">
                    <div class="spec-name">Front axle</div>
                    <div class="spec-value">{{ productData.axle_front || 'N/A' }}</div>
                  </div>
                  <div class="spec-row">
                    <div class="spec-name">Rear axle</div>
                    <div class="spec-value">{{ productData.axle_rear || 'N/A' }}</div>
                  </div>
                  <div class="spec-row">
                    <div class="spec-name">Speed ratio</div>
                    <div class="spec-value">{{ productData.axle_speed_ratio || 'N/A' }}</div>
                  </div>
                  <div class="spec-row">
                    <div class="spec-name">Suspension Type</div>
                    <div class="spec-value">{{ productData.suspension_type || 'N/A' }}</div>
                  </div>
                  <div class="spec-row">
                    <div class="spec-name">Front Suspension</div>
                    <div class="spec-value">{{ productData.suspension_front || 'N/A' }}</div>
                  </div>
                  <div class="spec-row">
                    <div class="spec-name">Rear Suspension</div>
                    <div class="spec-value">{{ productData.suspension_rear || 'N/A' }}</div>
                  </div>
                  <div class="spec-row">
                    <div class="spec-name">Brake System</div>
                    <div class="spec-value">{{ productData.brake_system || 'N/A' }}</div>
                  </div>
                  <div class="spec-row">
                    <div class="spec-name">Tires</div>
                    <div class="spec-value">{{ productData.tire_size || 'N/A' }}</div>
                  </div>
                  <div class="spec-row">
                    <div class="spec-name">Air filter</div>
                    <div class="spec-value">{{ productData.air_filter || 'N/A' }}</div>
                  </div>
                  <div class="spec-row">
                    <div class="spec-name">Electrics</div>
                    <div class="spec-value">{{ productData.electrics || 'N/A' }}</div>
                  </div>
                </div>
              </div>
            </div>
          </div>

        </form>
      </div>

      <div class="sticky bottom-0 bg-white/95 backdrop-blur-sm border-t border-gray-200 px-4 sm:px-6 py-4 sm:rounded-b-2xl z-50">
        <div class="flex flex-row gap-3 justify-between">
          <div class="flex gap-2">
            <button
              v-if="currentStep > 0"
              type="button"
              @click.prevent="handlePreviousStep"
              :disabled="isLoading"
              class=" btn-gray"
            >
              <ChevronLeftIcon class="w-4 h-4 inline" />
              Previous
            </button>
          </div>

          <div class="flex gap-2  ">
            <button
              type="button"
              @click.prevent="handleCloseModal"
              :disabled="isLoading"
              class="btn-gray"
            >
              Cancel
            </button>
            
            <button
              v-if="currentStep < steps.length - 1"
              type="button"
              @click.prevent="handleNextStep"
              :disabled="!canProceedToNextStep || isLoading"
              class="btn-degrade-orange block"
            >
              Next ({{ currentStep + 1 }}/{{ steps.length }})
              <ChevronRightIcon class="w-4 h-4 inline" />
            </button>

            <button
              v-else
              type="button"
              @click.prevent="handleSubmit"
              :disabled="isLoading || !canSubmit"
              class="submit-btn"
            >
              <div v-if="isLoading" class="flex items-center justify-center">
                <div class="animate-spin rounded-full h-4 w-4 border-2 border-white border-t-transparent mr-2"></div>
                Creation...
              </div>
              <div v-else class="flex items-center justify-center">
                <CheckIcon class="w-4 h-4" />
                Create produit
              </div>
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, reactive, computed, onMounted, nextTick, watch } from 'vue'
import axios from 'axios'
import { categoriesApi , brandsApi, trailersApi  } from '../../services/api'
import {formatPrice} from '../../services/formatPrice'
import { 
  Plus as PlusIcon,
  X as XIcon,
  Info as InfoIcon,
  DollarSign as DollarSignIcon,
  Palette as PaletteIcon,
  Image as ImageIcon,
  Upload as UploadIcon,
  Check as CheckIcon,
  CheckCircle as CheckCircleIcon,
  ChevronLeft as ChevronLeftIcon,
  ChevronRight as ChevronRightIcon,
  AlertCircle as AlertCircleIcon,
  Edit as EditIcon,
  Zap as ZapIcon,
  Ruler as RulerIcon,
  Video as VideoIcon,
  FileText as FileTextIcon
} from 'lucide-vue-next'

const emit = defineEmits(['close', 'save'])

const currentStep = ref(0)
const customColor = ref({ name: '', value: '#000000' })
const isLoading = ref(false)
const loadingMessage = ref('')
const error = ref(null)
const wysiwygEditor = ref(null)
const wysiwygEditor2 = ref(null)
const singleVin = ref('')
const singleTrim = ref('')
const trailerTypes = ref([])
const trailerBrands = ref([])
const trailerTypesLoading = ref(false)
const trailerBrandsLoading = ref(false)

// États pour les catégories API
const categories = ref([])
const categoriesLoading = ref(false)
const categoriesError = ref(null)

//Models
const brands = ref([])
const brandsLoading = ref(false)
const brandsError = ref(null)

// errors
const errors = reactive({})

const isTrailerCategory = computed(() => {
  const category = categories.value.find(cat => cat.id === productData.category_id)
  return category && (category.name.toLowerCase().includes('trailer') || category.name.toLowerCase().includes('semi'))
})

// ✅ NOUVEAU: Détection de la catégorie Car
const isCarCategory = computed(() => {
  if (!productData.category_id) return false

  const category = categories.value.find(cat => cat.id === productData.category_id)
  if (!category) return false

  const categoryName = category.name.toLowerCase()
  return (categoryName.includes('car') ||
          categoryName.includes('voiture') ||
          categoryName.includes('auto') ||
          (categoryName.includes('vehicle') && !isTrailerCategory.value))
})

// Configuration Cloudinary
const cloudinaryConfig = {
  cloudName: 'daaavha4z',
  uploadPreset: 'aliadjame',
  apiKey: 'wy0Eh-uA0Y0Ci3nyODix0b3WejA',
  imageUploadUrl: 'https://api.cloudinary.com/v1_1/daaavha4z/image/upload',
  videoUploadUrl: 'https://api.cloudinary.com/v1_1/daaavha4z/video/upload'
}

const productData = reactive({
  name: '',
  description: '',
  hasDetailedDescription: false,
  description_plus: '',
  category_id: '',
  subcategory_id: '',
  subsubcategory_id: '',
  subsubsubcategory_id: '',
  tags: '',
  unit_price: '',
  stock: '',
  unit_type: 'quantity',
  disponibility: 'available',
  hasWholesalePrice: false,
  wholesale_price: null,
  wholesale_min_qty: null,
  colors_names: [],
  colors: [],
  images: [],
  imageUrls: [],
  video: null,
  videoUrl: '',
  is_active: true,
  vehicle_condition: '',
  vehicle_brand_id: '',
  vehicle_model_id: '',
  drive_type: '',
  vehicle_year: null,
  fuel_type: '',
  transmission_type: '',
  engine_brand: '',
  trim_numbers: [],
  vin: [],
  engine_number: "",
  power: '',
  engine_emissions: '',
  vehicle_mileage: null,
  production_date: '',
  country_of_origin: '',
  wheelbase: null,
  gvw: null,
  payload_capacity: null,
  cabin_type: '',
  suspension_type: '',
  brake_system: '',
  tire_size: '',
  dimensions_length: null,
  dimensions_width: null,
  dimensions_height: null,
  curb_weight: null,
  fuel_tank_capacity: null,
  speed:'',
  gearbox_brand:'',
  gearbox_model:'',
  chassis_dimensions:'',
  frame_rear_suspension:'',
  suspension_rear:'',
  suspension_front:'',
  axle_brand:'',
  axle_front:'',
  axle_rear:'',
  axle_speed_ratio:'',
  air_filter:'',
  electrics:'',
  cab:'',
  trailer_condition: '',
  trailer_type: '',
  trailer_brand: '',
  trailer_use: '',
  trailer_size: '',
  trailer_axle: '',
  trailer_suspension: '',
  trailer_tire: '',
  trailer_king_pin: '',
  trailer_main_beam: '',
  trailer_max_payload: null,
  trailer_place_of_origin: '',
  trailer_material: '',
  trailer_function: '',
  trailer_landing_gear: '',
  trailer_color: '',
  trailer_axle_brand: '',

  // ========================================
  // NOUVEAUX CHAMPS CAR (41)
  // ========================================
  // Data Entry Mode
  car_data_entry_mode: 'manual', // 'manual' | 'vin'
  car_vin_input: '',
  car_vin_decoding: false,
  car_vin_decoded: false,
  car_vin_error: '',

  // Basic Info (6)
  car_make: '',
  car_model: '',
  car_year: null,
  car_condition: 'Used',
  car_vin: '',
  car_mileage: null,

  // Battery & Electric (4)
  car_battery_range: null,
  car_charge_time_full: '',
  car_quick_charge_time: '',
  car_battery_capacity: null,

  // Dimensions (5)
  car_height: null,
  car_length: null,
  car_width: null,
  car_kerb_weight: null,
  car_wheelbase: null,

  // Engine & Drivetrain (5)
  car_fuel_type: '',
  car_transmission: '',
  car_engine_size: null,
  car_engine_cylinders: null,
  car_drivetrain: '',

  // General (5)
  car_doors: null,
  car_seats: null,
  car_warranty_miles: null,
  car_warranty_years: null,
  car_insurance_group: '',

  // Performance (5)
  car_top_speed: null,
  car_engine_power_bhp: null,
  car_engine_power_kw: null,
  car_engine_torque: null,
  car_acceleration: null,

  // Colors & Interior (3)
  car_exterior_color: '',
  car_interior_color: '',
  car_interior_material: '',

  // Efficiency (4)
  car_mpg_city: null,
  car_mpg_highway: null,
  car_mpg_combined: null,
  car_co2_emissions: null,

  // Additional (4)
  car_body_type: '',
  car_trim_level: '',
  car_previous_owners: null,
  car_service_history: '',

  // Data Source (3)
  car_data_source: 'manual',
  car_vin_decoded_at: null,
  car_api_provider: null,
})

const availableSubcategories = computed(() => {
  const category = categories.value.find(cat => cat.id === productData.category_id)
  return category ? category.subcategories || [] : []
})

const steps = computed(() => {
  if (isCarCategory.value) {
    // ✅ Étapes pour Car
    return [
      { title: 'Basic Info', subtitle: 'Category & Method' },
      { title: 'Vehicle Info', subtitle: 'Specs & Dimensions' },
      { title: 'Performance', subtitle: 'Power & Efficiency' },
      { title: 'Finalize', subtitle: 'Price & Images' }
    ]
  } else if (isTrailerCategory.value) {
    return [
      { title: 'Informations' },
      { title: 'Overviews' },
      { title: 'Price & Stock' },
      { title: 'Color' },
      { title: 'Pictures' }
    ]
  } else {
    // Trucks
    return [
      { title: 'Informations' },
      { title: 'Overviews' },
      { title: 'Technical Specification' },
      { title: 'Price & Stock' },
      { title: 'Color' },
      { title: 'Pictures' }
    ]
  }
})

const fetchTrailerData = async () => {
  try {
    trailerTypesLoading.value = true
    trailerBrandsLoading.value = true
    
    const [typesResponse, brandsResponse] = await Promise.all([
      trailersApi.getTrailerTypes(),
      trailersApi.getTrailerBrands()
    ])
    
    trailerTypes.value = typesResponse.data || []
    trailerBrands.value = brandsResponse.data || []
    
  } catch (err) {
    console.error('Erreur lors du chargement des données trailers:', err)
  } finally {
    trailerTypesLoading.value = false
    trailerBrandsLoading.value = false
  }
}

const availableSubSubcategories = computed(() => {
  for (const category of categories.value) {
    const subcategory = (category.subcategories || []).find(sub => sub.id === productData.subcategory_id)
    if (subcategory && subcategory.sub_subcategories) {
      return subcategory.sub_subcategories
    }
  }
  return []
})

const availableSubSubSubcategories = computed(() => {
  for (const category of categories.value) {
    for (const subcategory of (category.subcategories || [])) {
      if (subcategory.sub_subcategories) {
        const subsubcategory = subcategory.sub_subcategories.find(subsub => subsub.id === productData.subsubcategory_id)
        if (subsubcategory && subsubcategory.sub_sub_subcategories) {
          return subsubcategory.sub_sub_subcategories
        }
      }
    }
  }
  return []
})

const setMainImage = (index) => {
  if (index === 0) return
  const selected = productData.images[index]
  productData.images.splice(index, 1)
  productData.images.unshift(selected)
}

const availableModels = computed(() => {
  const models = brands.value.find(cat => cat.id === productData.vehicle_brand_id)
  return models ? models.models || [] : []
})

const availableFuelType = computed(() => {
  const modelsObject = availableModels.value 
  const fuelType = modelsObject.find(model => model.id === productData.vehicle_model_id)
  productData.fuel_type = fuelType ? fuelType.fuel_type || '' : ''
  return fuelType ? fuelType.fuel_type || '' : ''
})

// ✅ CORRECTION: Validation des étapes adaptée au flow trailer/truck
const canProceedToNextStep = computed(() => {
  switch (currentStep.value) {
    case 0:
      // Étape 0: Informations (identique pour tous)
      return !!(productData.category_id && productData.subcategory_id)
    
    case 1:
      // Étape 1: Overviews
      if (isTrailerCategory.value) {
        // Pour trailer: vérifier les champs trailer
        return !!(
          productData.trailer_condition &&
          productData.trailer_type &&
          productData.trailer_brand &&
          productData.trailer_use &&
          productData.trailer_size &&
          productData.trailer_axle &&
          productData.trailer_suspension &&
          productData.trailer_tire &&
          productData.trailer_max_payload &&
          productData.trailer_place_of_origin
        )
      } else if (isCarCategory.value) {
        // Pour car: vérifier les champs obligatoires
        return !!(
          productData.car_make &&
          productData.car_model &&
          productData.car_year &&
          productData.car_condition &&
          productData.car_fuel_type &&
          productData.car_transmission
        )
      } else {
        // Pour truck: vérifier les champs truck
        return !!(productData.vehicle_condition && productData.vehicle_brand_id)
      }
    
    case 2:
      if (isTrailerCategory.value) {
        // Pour trailer à l'étape 2 = Price & Stock
        // ✅ Générer le nom avant de valider
        if (productData.trailer_type && productData.trailer_axle && productData.trailer_max_payload) {
          getProductName()
        }
        return !!(
          productData.unit_price &&
          productData.unit_price > 0 &&
          productData.disponibility
        )
      } else if (isCarCategory.value) {
        // Pour car à l'étape 2 = Performance & Characteristics + Colors
        // Générer le nom du produit automatiquement
        if (productData.car_make && productData.car_model && productData.car_year) {
          productData.name = `${productData.car_year} ${productData.car_make} ${productData.car_model}`
          if (productData.car_trim_level) {
            productData.name += ` ${productData.car_trim_level}`
          }
        }
        // Vérifier qu'au moins une couleur est sélectionnée
        return !!productData.colors.length
      } else {
        // Pour truck à l'étape 2 = Technical Specification
        getProductName()
        return !!(productData.engine_brand && productData.gearbox_brand)
      }
    
    case 3:
      if (isTrailerCategory.value) {
        // Pour trailer à l'étape 3 = Color
        return !!productData.colors.length
      } else if (isCarCategory.value) {
        // Pour car à l'étape 3 = Price & Stock + Images (dernière étape pour les voitures)
        return !!(
          productData.unit_price &&
          productData.unit_price > 0 &&
          productData.disponibility
        )
      } else {
        // Pour truck à l'étape 3 = Price & Stock
        return !!(
          productData.unit_price &&
          productData.unit_price > 0 &&
          productData.disponibility
        )
      }
    
    case 4:
      if (isTrailerCategory.value) {
        // Pour trailer à l'étape 4 = Pictures (dernière étape)
        return true
      } else {
        // Pour truck à l'étape 4 = Color
        return !!productData.colors.length
      }
    
    case 5:
      // Étape 5 = Pictures (uniquement pour truck)
      return true
    
    default:
      return false
  }
})

const canSubmit = computed(() => {
  return !!(
    productData.name && 
    productData.category_id && 
    productData.subcategory_id && 
    productData.unit_price !== null && 
    productData.unit_price !== '' && 
    Number(productData.unit_price) > 0
  )
})

productData.vehicle_year = computed(() => {
  const s = String(productData.production_date || '').trim()
  const m = s.match(/^(\d{4})-/)
  return m ? Number(m[1]) : null
})

const isNumeric = (value) => {
  if (value === null || value === undefined || value === '') return false
  if (typeof value === 'number') return Number.isFinite(value)
  const s = String(value).trim().replace(/\s+/g, '').replace(',', '.')
  return /^-?\d+(\.\d+)?$/.test(s)
}

const inputValidator = (data) => {
  errors.message = isNumeric(data) ? '' : 'Please enter a number'
}

const getCategoryName = (id) => {
  const category = categories.value.find(cat => cat.id === id)
  return category ? category.name : ''
}

const getBrandName = (id) => {
  const brand = brands.value.find(cat => cat.id === id)
  return brand ? brand.name : ''
}

const getModelName = (id) => {
  const model = availableModels.value.find(cat => cat.id === id)
  return model ? model.name : ''
}

const availableColors = ref([
  { name: 'Black', value: '#000000' },
  { name: 'White', value: '#FFFFFF' },
  { name: 'Gray', value: '#808080' },
  { name: 'Red', value: '#FF0000' },
  { name: 'Blue', value: '#0000FF' },
  { name: 'Green', value: '#008000' },
  { name: 'Yellow', value: '#FFFF00' },
  { name: 'Orange', value: '#FFA500' },
  { name: 'Pink', value: '#FFC0CB' },
  { name: 'Purple', value: '#800080' },
  { name: 'Brown', value: '#8B4513' },
  { name: 'Gold', value: '#FFD700' }
])

const availability = ref([
  { value: 'available', label: 'Available' },
  { value: 'on_order', label: 'On Order' },
])

watch(singleVin, (newVal) => {
  productData.vin = newVal ? [newVal] : []
})

watch(singleTrim, (newVal) => {
  productData.trim_numbers = newVal ? [newVal] : []
})

const fetchCategories = async () => {
  try {
    categoriesLoading.value = true
    categoriesError.value = null
    const response = await categoriesApi.getCategories()
    categories.value = response.data || []
  } catch (err) {
    categoriesError.value = 'Impossible to load categories. Try again please.'
  } finally {
    categoriesLoading.value = false
  }
}

const calculateYearFromProductionDate = () => {
  if (productData.production_date) {
    const date = new Date(productData.production_date)
    return date.getFullYear()
  }
  return null
}

const fetchMakes = async () => {
  try {
    brandsLoading.value = true
    brandsError.value = null
    const response = await brandsApi.getBrands()
    brands.value = response.data || []
  } catch (err) {
    brandsError.value = 'Impossible to load categories. try again please.'
  } finally {
    brandsLoading.value = false
  }
}

const formatText = (command) => {
  document.execCommand(command, false, null)
  wysiwygEditor.value.focus()
}

const formatHeading = (event) => {
  const heading = event.target.value
  if (heading) {
    document.execCommand('formatBlock', false, heading)
    event.target.value = ''
    wysiwygEditor.value.focus()
  }
}

const updateOtherDescription = () => {
  productData.description_plus = wysiwygEditor2.value.innerHTML
}

const updateSubcategories = () => {
  productData.subcategory_id = ''
  productData.subsubcategory_id = ''
  productData.subsubsubcategory_id = ''
}

const updateSubSubcategories = () => {
  productData.subsubcategory_id = ''
  productData.subsubsubcategory_id = ''
}

const updateSubSubSubcategories = () => {
  productData.subsubsubcategory_id = ''
}

const updateModelid = () => {
  productData.vehicle_model_id = ""
  productData.fuel_type = ""
}

const updateFuelType = () => {
  productData.fuel_type = availableFuelType.value
}

const toggleColor = (color) => {
  const index = productData.colors.indexOf(color)
  if (index > -1) {
    productData.colors.splice(index, 1)
    productData.colors_names.splice(index, 1)
  } else {
    productData.colors.push(color)
    const colorObj = availableColors.value.find(c => c.value === color)
    if (colorObj) {
      productData.colors_names.push(colorObj.name)
    } else {
      productData.colors_names.push('Personnalisé')
    }
  }
}

const addCustomColor = () => {
  if (customColor.value.name && customColor.value.value) {
    const exists = availableColors.value.some(color => 
      color.value === customColor.value.value || 
      color.name.toLowerCase() === customColor.value.name.toLowerCase()
    )
    
    if (!exists) {
      availableColors.value.push({
        name: customColor.value.name,
        value: customColor.value.value
      })
      productData.colors.push(customColor.value.value)
      productData.colors_names.push(customColor.value.name)
    }
    
    customColor.value = { name: '', value: '#000000' }
  }
}

const handleImageUpload = (event) => {
  const files = Array.from(event.target.files)
  const remainingSlots = 35 - productData.images.length
  
  files.slice(0, remainingSlots).forEach(file => {
    if (file.size <= 10 * 1024 * 1024) {
      const reader = new FileReader()
      reader.onload = (e) => {
        productData.images.push({
          file: file,
          preview: e.target.result,
          uploading: false,
          uploadProgress: 0,
          uploaded: false,
          url: null
        })
      }
      reader.readAsDataURL(file)
    }
  })
}

const removeImage = (index) => {
  productData.images.splice(index, 1)
  if (productData.imageUrls[index]) {
    productData.imageUrls.splice(index, 1)
  }
}

const handleVideoUpload = (event) => {
  const file = event.target.files[0]
  if (file && file.size <= 50 * 1024 * 1024) {
    const reader = new FileReader()
    reader.onload = (e) => {
      productData.video = {
        file: file,
        preview: e.target.result,
        uploading: false,
        uploadProgress: 0,
        uploaded: false,
        url: null
      }
    }
    reader.readAsDataURL(file)
  }
}

const removeVideo = () => {
  productData.video = null
  productData.videoUrl = ''
}

const getProductName = () => {
  if (isTrailerCategory.value) {
    const condition = productData.trailer_condition 
      ? productData.trailer_condition.charAt(0).toUpperCase() + productData.trailer_condition.slice(1).toLowerCase() 
      : ''
    productData.name = [
      condition,
      productData.trailer_type,
      productData.trailer_axle ? `${productData.trailer_axle} Axles` : '',
      productData.trailer_max_payload ? `${productData.trailer_max_payload}T` : ''
    ].filter(Boolean).join(' - ')
  } else {
    productData.vehicle_condition = productData.vehicle_condition.charAt(0).toUpperCase() + productData.vehicle_condition.slice(1).toLowerCase()
    
    productData.name = [
      productData.vehicle_condition,
      productData.vehicle_year,
      getBrandName(productData.vehicle_brand_id),
      getModelName(productData.vehicle_model_id),
      getCategoryName(productData.category_id),
      productData.drive_type
    ].filter(Boolean).join(' ')
  }
  return productData.name
}

const handleNextStep = async () => {
  error.value = null
  
  if (!canProceedToNextStep.value) {
    error.value = 'Please fill in all required fields.'
    setTimeout(() => {
      error.value = null
    }, 3000)
    return
  }
  
  if (currentStep.value < steps.value.length - 1) {
    currentStep.value++
    await nextTick()
  }
}

const handlePreviousStep = async () => {
  if (currentStep.value > 0) {
    currentStep.value--
    error.value = null
    await nextTick()
  }
}

const handleCloseModal = () => {
  closeModal()
}

const closeModal = () => {
  currentStep.value = 0
  error.value = null
  emit('close')
}

const uploadImageToCloudinary = async (image, index) => {
  try {
    productData.images[index].uploading = true
    productData.images[index].uploadProgress = 0
    
    const fileName = `product_${Date.now()}_${index}_${image.file.name.replace(/\s+/g, '_')}`
    
    const formData = new FormData()
    formData.append('file', image.file)
    formData.append('upload_preset', cloudinaryConfig.uploadPreset)
    formData.append('api_key', cloudinaryConfig.apiKey)
    formData.append('public_id', fileName)
    
    const response = await axios.post(cloudinaryConfig.imageUploadUrl, formData, {
      headers: {
        'Content-Type': 'multipart/form-data'
      },
      onUploadProgress: (progressEvent) => {
        const percentCompleted = Math.round((progressEvent.loaded * 100) / progressEvent.total)
        productData.images[index].uploadProgress = percentCompleted
      }
    })
    
    if (response.data && response.data.secure_url) {
      productData.images[index].url = response.data.secure_url
      productData.imageUrls[index] = response.data.secure_url
    } else {
      throw new Error('Réponse Cloudinary invalide')
    }
    
    productData.images[index].uploading = false
    productData.images[index].uploaded = true
    
    return true
  } catch (error) {
    productData.images[index].uploading = false
    return false
  }
}

const uploadVideoToCloudinary = async (video) => {
  try {
    productData.video.uploading = true
    productData.video.uploadProgress = 0
    
    const fileName = `product_video_${Date.now()}_${video.file.name.replace(/\s+/g, '_')}`
    
    const formData = new FormData()
    formData.append('file', video.file)
    formData.append('upload_preset', cloudinaryConfig.uploadPreset)
    formData.append('api_key', cloudinaryConfig.apiKey)
    formData.append('public_id', fileName)
    formData.append('resource_type', 'video')
    
    const response = await axios.post(cloudinaryConfig.videoUploadUrl, formData, {
      headers: {
        'Content-Type': 'multipart/form-data'
      },
      onUploadProgress: (progressEvent) => {
        const percentCompleted = Math.round((progressEvent.loaded * 100) / progressEvent.total)
        productData.video.uploadProgress = percentCompleted
      }
    })
    
    if (response.data && response.data.secure_url) {
      productData.video.url = response.data.secure_url
      productData.videoUrl = response.data.secure_url
    } else {
      throw new Error('Réponse Cloudinary invalide')
    }
    
    productData.video.uploading = false
    productData.video.uploaded = true
    
    return true
  } catch (error) {
    productData.video.uploading = false
    return false
  }
}

const uploadAllMedia = async () => {
  loadingMessage.value = 'Loading media...'
  
  const imagePromises = []
  for (let i = 0; i < productData.images.length; i++) {
    imagePromises.push(uploadImageToCloudinary(productData.images[i], i))
  }
  
  await Promise.all(imagePromises)
  
  if (productData.video && productData.video.file) {
    await uploadVideoToCloudinary(productData.video)
  }
  
  loadingMessage.value = 'Media successfully downloaded!'
}

// 🚗 Decode VIN using NHTSA API (Car category only)
const decodeVIN = async (vin) => {
  if (!vin || vin.length !== 17) {
    productData.car_vin_error = 'VIN must be exactly 17 characters'
    return
  }

  productData.car_vin_decoding = true
  productData.car_vin_error = ''
  productData.car_vin_decoded = false

  try {
    const response = await fetch(
      `https://sastock.com/api_adjame/products.php?action=decode-vin&vin=${encodeURIComponent(vin)}`,
      {
        method: 'GET',
        headers: {
          'Accept': 'application/json'
        }
      }
    )

    if (!response.ok) {
      throw new Error(`HTTP error ${response.status}`)
    }

    const result = await response.json()

    if (result && result.success) {
      const carData = result.data || {}

      // Pré-remplissage des champs
      productData.car_make = carData.car_make || ''
      productData.car_model = carData.car_model || ''
      productData.car_year = carData.car_year || null
      productData.car_fuel_type = carData.car_fuel_type || ''
      productData.car_transmission = carData.car_transmission || ''
      productData.car_drivetrain = carData.car_drivetrain || ''
      productData.car_engine_size = carData.car_engine_size || ''
      productData.car_engine_cylinders = carData.car_engine_cylinders || null
      productData.car_body_type = carData.car_body_type || ''
      productData.car_doors = carData.car_doors || null
      productData.car_seats = carData.car_seats || null
      productData.car_vin = vin

      // Nom auto du produit
      if (productData.car_make && productData.car_model && productData.car_year) {
        productData.name = `${productData.car_year} ${productData.car_make} ${productData.car_model}`
        if (productData.car_trim_level) {
          productData.name += ` ${productData.car_trim_level}`
        }
      }

      productData.car_vin_decoded = true
      productData.car_data_source = 'api'
      productData.car_vin_decoded_at = new Date().toISOString()
      productData.car_api_provider = 'NHTSA'
    } else {
      productData.car_vin_error =
        'Unable to decode VIN. Please enter details manually.'
    }
  } catch (error) {
    console.error('VIN decode error:', error)
    productData.car_vin_error =
      'VIN decode failed. Please enter details manually.'
  } finally {
    productData.car_vin_decoding = false
  }
}


const prepareDataForSubmission = () => {
  const formData = {
    name: productData.name,
    description: productData.description,
    description_plus: productData.description_plus,
    category_id: productData.category_id,
    subcategory_id: productData.subcategory_id,
    subsubcategory_id: productData.subsubcategory_id,
    subsubsubcategory_id: productData.subsubsubcategory_id,
    unit_price: parseFloat(productData.unit_price),
    wholesale_price: productData.hasWholesalePrice ? parseFloat(productData.wholesale_price) : null,
    wholesale_min_qty: productData.hasWholesalePrice ? parseInt(productData.wholesale_min_qty) : null,
    stock: parseInt(productData.stock),
    unit_type: productData.unit_type,
    disponibility: productData.disponibility,
    tags: productData.tags,
    is_active: productData.is_active,
    colors: productData.colors,
    vehicle_condition: productData.vehicle_condition,
    vehicle_make: getBrandName(productData.vehicle_brand_id),
    vehicle_model: getModelName(productData.vehicle_model_id),
    drive_type: productData.drive_type,
    vehicle_year: productData.vehicle_year,
    fuel_type: productData.fuel_type,
    transmission_type: productData.transmission_type,
    engine_brand: productData.engine_brand,
    trim_numbers: productData.trim_numbers.filter(num => num.trim() !== ''),
    vin: productData.vin.filter(num => num.trim() !== ''),
    engine_number: productData.engine_number,
    power: productData.power,
    engine_emissions: productData.engine_emissions,
    vehicle_mileage: productData.vehicle_mileage,
    production_date: productData.production_date,
    country_of_origin: productData.country_of_origin,
    wheelbase: productData.wheelbase,
    gvw: productData.gvw,
    payload_capacity: productData.payload_capacity,
    cabin_type: productData.cabin_type,
    suspension_type: productData.suspension_type,
    brake_system: productData.brake_system,
    tyre_size: productData.tire_size,
    curb_weight: productData.curb_weight,
    fuel_tank_capacity: productData.fuel_tank_capacity,
    dimensions: productData.dimensions_length || productData.dimensions_width || productData.dimensions_height
      ? `${productData.dimensions_length || 0}x${productData.dimensions_width || 0}x${productData.dimensions_height || 0}`
      : null,
    speed: productData.speed,
    gearbox_brand: productData.gearbox_brand,
    gearbox_model: productData.gearbox_model,
    chassis_dimensions: productData.chassis_dimensions,
    frame_rear_suspension: productData.frame_rear_suspension,
    suspension_rear: productData.suspension_rear,
    suspension_front: productData.suspension_front,
    axle_brand: productData.axle_brand,
    axle_front: productData.axle_front,
    axle_rear: productData.axle_rear,
    axle_speed_ratio: productData.axle_speed_ratio,
    air_filter: productData.air_filter,
    electrics: productData.electrics,
    cab: productData.cab,
    // ✅ Champs 
    trailer_condition: productData.trailer_condition,
    trailer_type: productData.trailer_type,
    trailer_brand: productData.trailer_brand,
    trailer_use: productData.trailer_use,
    trailer_size: productData.trailer_size,
    trailer_axle: productData.trailer_axle,
    trailer_suspension: productData.trailer_suspension,
    trailer_tire: productData.trailer_tire,
    trailer_king_pin: productData.trailer_king_pin,
    trailer_main_beam: productData.trailer_main_beam,
    trailer_max_payload: productData.trailer_max_payload,
    trailer_place_of_origin: productData.trailer_place_of_origin,
    trailer_material: productData.trailer_material,
    trailer_function: productData.trailer_function,
    trailer_landing_gear: productData.trailer_landing_gear,
    trailer_color: productData.trailer_color,
    trailer_axle_brand: productData.trailer_axle_brand,
  }

  // 🚗 Add Car fields if category is Car
  if (isCarCategory.value) {
    Object.assign(formData, {
      car_data_entry_mode: productData.car_data_entry_mode,
      car_make: productData.car_make,
      car_model: productData.car_model,
      car_year: productData.car_year,
      car_condition: productData.car_condition,
      car_vin: productData.car_vin,
      car_mileage: productData.car_mileage,
      car_battery_range: productData.car_battery_range,
      car_charge_time_full: productData.car_charge_time_full,
      car_quick_charge_time: productData.car_quick_charge_time,
      car_battery_capacity: productData.car_battery_capacity,
      car_height: productData.car_height,
      car_length: productData.car_length,
      car_width: productData.car_width,
      car_kerb_weight: productData.car_kerb_weight,
      car_wheelbase: productData.car_wheelbase,
      car_fuel_type: productData.car_fuel_type,
      car_transmission: productData.car_transmission,
      car_engine_size: productData.car_engine_size,
      car_engine_cylinders: productData.car_engine_cylinders,
      car_drivetrain: productData.car_drivetrain,
      car_doors: productData.car_doors,
      car_seats: productData.car_seats,
      car_warranty_miles: productData.car_warranty_miles,
      car_warranty_years: productData.car_warranty_years,
      car_insurance_group: productData.car_insurance_group,
      car_top_speed: productData.car_top_speed,
      car_engine_power_bhp: productData.car_engine_power_bhp,
      car_engine_power_kw: productData.car_engine_power_kw,
      car_engine_torque: productData.car_engine_torque,
      car_acceleration: productData.car_acceleration,
      car_exterior_color: productData.car_exterior_color,
      car_interior_color: productData.car_interior_color,
      car_interior_material: productData.car_interior_material,
      car_mpg_city: productData.car_mpg_city,
      car_mpg_highway: productData.car_mpg_highway,
      car_mpg_combined: productData.car_mpg_combined,
      car_co2_emissions: productData.car_co2_emissions,
      car_body_type: productData.car_body_type,
      car_trim_level: productData.car_trim_level,
      car_previous_owners: productData.car_previous_owners,
      car_service_history: productData.car_service_history,
      car_data_source: productData.car_data_source,
      car_vin_decoded_at: productData.car_vin_decoded_at,
      car_api_provider: productData.car_api_provider,
    })
  }

  formData.images = productData.imageUrls.map((url, index) => ({
    url: url,
    alt_text: `${productData.name} - Image ${index + 1}`,
    is_primary: index === 0 ? 1 : 0,
    sort_order: index
  }))
  
  if (productData.videoUrl) {
    formData.video = productData.videoUrl
  }
  return formData
}

const handleSubmit = async () => {
  if (!canSubmit.value) return
  
  try {
    isLoading.value = true
    error.value = null
    
    await uploadAllMedia()
    
    loadingMessage.value = 'Préparation des données...'
    const formData = prepareDataForSubmission()
    
    formData.vehicle_year = calculateYearFromProductionDate()
    formData.stock = 1
    formData.unit_type = 'unité'
    
    loadingMessage.value = 'Finalisation...'
    emit('save', formData)
    
  } catch (err) {
    error.value = 'Error to load medias. Please try again.'
  } finally {
    isLoading.value = false
  }
}

const handleBackdropClick = (event) => {
  if (event.target === event.currentTarget) {
    handleCloseModal()
  }
}

onMounted(async () => {
  await fetchCategories()
  await fetchMakes()
  await fetchTrailerData()
})
</script>

<style scoped>

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

/* Amélioration pour les interactions tactiles */
.touch-manipulation {
  touch-action: manipulation;
}

/* Styles pour l'éditeur WYSIWYG */
[contenteditable="true"]:empty:before {
  content: attr(placeholder);
  color: #9CA3AF;
  font-style: italic;
}

[contenteditable="true"]:focus {
  outline: none;
}

/* Styles pour le contenu formaté */
[contenteditable="true"] h1 {
  font-size: 1.5rem;
  font-weight: bold;
  margin: 0.5rem 0;
}

[contenteditable="true"] h2 {
  font-size: 1.25rem;
  font-weight: bold;
  margin: 0.5rem 0;
}

[contenteditable="true"] h3 {
  font-size: 1.125rem;
  font-weight: bold;
  margin: 0.5rem 0;
}

[contenteditable="true"] ul {
  list-style-type: disc;
  margin-left: 1.5rem;
  margin: 0.5rem 0 0.5rem 1.5rem;
}

[contenteditable="true"] ol {
  list-style-type: decimal;
  margin-left: 1.5rem;
  margin: 0.5rem 0 0.5rem 1.5rem;
}

[contenteditable="true"] li {
  margin: 0.25rem 0;
}

[contenteditable="true"] strong {
  font-weight: bold;
}

[contenteditable="true"] em {
  font-style: italic;
}

[contenteditable="true"] u {
  text-decoration: underline;
}

/* Transitions fluides */
.transition-all {
  transition-property: all;
  transition-timing-function: cubic-bezier(0.4, 0, 0.2, 1);
  transition-duration: 300ms;
}

.transition-colors {
  transition-property: color, background-color, border-color, text-decoration-color, fill, stroke;
  transition-timing-function: cubic-bezier(0.4, 0, 0.2, 1);
  transition-duration: 150ms;
}

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

/* Amélioration pour les boutons sur mobile */
@media (max-width: 640px) {
  button {
    min-height: 44px; /* Taille minimale recommandée pour les boutons tactiles */
  }
}
</style>
