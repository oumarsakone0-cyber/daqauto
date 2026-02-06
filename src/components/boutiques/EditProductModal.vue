<template> 
  <div 
    class="fixed inset-0 bg-black/60 backdrop-blur-sm z-50 p-0 sm:p-4 sm:flex sm:items-center sm:justify-center"
    @click="handleBackdropClick"
  >
    <div 
      class="bg-white w-full h-screen sm:h-auto sm:max-h-[90vh] sm:max-w-4xl sm:rounded-2xl shadow-2xl overflow-hidden transform transition-all duration-300 ease-out sm:mx-auto"
      @click.stop
    >
      <div class="absolute inset-0 overflow-hidden pointer-events-none sm:rounded-2xl">
        <div class="absolute -top-10 -right-10 w-32 h-32 bg-gradient-to-br from-orange-200/30 to-orange-300/20 rounded-full blur-2xl animate-pulse"></div>
        <div class="absolute -bottom-10 -left-10 w-40 h-40 bg-gradient-to-br from-blue-200/25 to-indigo-200/15 rounded-full blur-2xl animate-pulse" style="animation-delay: 1s;"></div>
      </div>
  
      <div class="sticky top-0 z-10 bg-white/95 backdrop-blur-sm border-b border-gray-200 px-4 sm:px-6 py-4 sm:rounded-t-2xl">
        <div class="flex items-center justify-between">
          <div class="flex items-center space-x-3">
            <div class="w-10 h-10 bg-orange rounded-lg flex items-center justify-center">
              <EditIcon class="w-5 h-5 text-white" />
            </div>
            <div>
              <h2 class="text-xl sm:text-2xl font-bold text-gray-900">Edit the product</h2>
              <p class="text-sm text-gray-600 hidden sm:block">Edit your product information</p>
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
                @click="fetchMethodes" 
                class="ml-2 px-2 py-1 error-background-color text-white rounded text-xs hover:bg-red-700 transition-colors"
              >
                Try again
              </button>
            </div>
          </div>
  
          <div v-if="isLoading" class="mb-3 p-3 bg-blue-50 border border-blue-200 primary-color rounded-lg flex items-center space-x-2">
            <div class="animate-spin w-4 h-4 border-2 border-orange-500 border-t-transparent rounded-full flex-shrink-0"></div>
            <span class="text-sm">{{ loadingMessage }}</span>
          </div>
  
          <div v-if="categoriesLoading" class="mb-3 p-3 bg-blue-50 border border-blue-200 primary-color rounded-lg flex items-center space-x-2">
            <div class="animate-spin w-4 h-4 border-2 border-orange-500 border-t-transparent rounded-full flex-shrink-0"></div>
            <span class="text-sm">Loading...</span>
          </div>
        </div>
      </div>
  
      <div class="overflow-y-auto h-[calc(100vh-200px)] sm:h-auto sm:max-h-[calc(60vh)] px-4 sm:px-6 py-6 relative z-5">
        <form @submit.prevent="handleSubmit" class="space-y-6 sm:space-y-8" v-if="editData">
  
  <!-- ============================================ -->
  <!-- SECTION 1: INFORMATIONS DE BASE -->
  <!-- ============================================ -->
  <div class="bg-white/80 backdrop-blur-sm rounded-xl p-4 sm:p-6 border border-gray-100 shadow-sm">
    <div class="flex items-center space-x-3 mb-4 sm:mb-6">
      <div class="w-8 h-8 bg-orange rounded-lg flex items-center justify-center">
        <InfoIcon class="w-4 h-4 text-white" />
      </div>
      <h3 class="text-lg sm:text-xl font-semibold text-gray-900">Basic information</h3>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 sm:gap-6">
      <div class="sm:col-span-2">
        <label for="name" class="block text-sm font-medium text-gray-700 mb-2">
          Product name <span class="error-color">*</span>
        </label>
        <input
          id="name"
          v-model="editData.name"
          type="text"
          required
          class="input-style"
          placeholder="Ex: New 2025 DAYUN DAYUN N8E Camions 4x2"
        >
      </div>

      <div>
        <label for="category" class="block text-sm font-medium text-gray-700 mb-2">
          Category <span class="error-color">*</span>
        </label>
        <select
          id="category"
          v-model="editData.category_id"
          @change="updateSubcategories"
          required
          :disabled="categoriesLoading"
          class="text-sm sm:text-base input-style"
        >
          <option value="">{{ categoriesLoading ? 'loading...' : 'Select a category' }}</option>
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
          v-model="editData.subcategory_id"
          @change="updateSubSubcategories"
          required
          :disabled="!editData.category_id || categoriesLoading"
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
          v-model="editData.subsubcategory_id"
          @change="updateSubSubSubcategories"
          :disabled="!editData.subcategory_id || categoriesLoading"
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
          v-model="editData.subsubsubcategory_id"
          :disabled="!editData.subsubcategory_id || categoriesLoading"
          class="text-sm sm:text-base input-style"
        >
          <option value="">Select a subsubsubcategory (optional)</option>
          <option v-for="subsubsubcategory in availableSubSubSubcategories" :key="subsubsubcategory.id" :value="subsubsubcategory.id">
            {{ subsubsubcategory.name  }}
          </option>
        </select>
      </div>

      <div class="sm:col-span-2">
        <label for="tags" class="block text-sm font-medium text-gray-700 mb-2">
          Tags (optional)
        </label>
        <input
          id="tags"
          v-model="editData.tags"
          type="text"
          class="text-sm sm:text-base input-style"
          placeholder="Ex: New, trend, promotion (separated by commas)"
        >
      </div>
    </div>
  </div>

  <!-- ============================================ -->
  <!-- SECTION 2: VEHICLE/TRAILER SPECIFICATIONS -->
  <!-- ============================================ -->
  <div class="bg-white/80 backdrop-blur-sm rounded-xl p-4 sm:p-6 border border-gray-100 shadow-sm">
    <div class="flex items-center space-x-3 mb-4 sm:mb-6">
      <div class="w-8 h-8 rounded-lg flex items-center justify-center bg-orange">
        <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
        </svg>
      </div>
      <h3 class="text-lg sm:text-xl font-semibold text-gray-900">
        {{ isTrailerCategory ? 'Trailer Specifications' : 'Vehicle Specifications' }}
      </h3>
    </div>

    <!-- TRAILER SPECIFICATIONS -->
    <div v-if="isTrailerCategory" class="grid grid-cols-1 sm:grid-cols-2 gap-4 sm:gap-6">
      <div>
        <label for="trailer_condition" class="block text-sm font-medium text-gray-700 mb-2">
          Condition <span class="error-color">*</span>
        </label>
        <select
          id="trailer_condition"
          v-model="editData.trailer_condition"
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
        <input
          id="trailer_type"
          v-model="editData.trailer_type"
          type="text"
          required
          class="text-sm sm:text-base input-style"
          placeholder="Ex: Flatbed, Box Van"
        >
      </div>

      <div>
        <label for="trailer_brand" class="block text-sm font-medium text-gray-700 mb-2">
          Brand <span class="error-color">*</span>
        </label>
        <input
          id="trailer_brand"
          v-model="editData.trailer_brand"
          type="text"
          required
          class="text-sm sm:text-base input-style"
          placeholder="Ex: CIMC, FUWA"
        >
      </div>

      <div>
        <label for="trailer_use" class="block text-sm font-medium text-gray-700 mb-2">
          Use <span class="error-color">*</span>
        </label>
        <input
          id="trailer_use"
          v-model="editData.trailer_use"
          type="text"
          required
          class="text-sm sm:text-base input-style"
          placeholder="Ex: Cargo, Container"
        >
      </div>

      <div>
        <label for="trailer_size" class="block text-sm font-medium text-gray-700 mb-2">
          Size <span class="error-color">*</span>
        </label>
        <input
          id="trailer_size"
          v-model="editData.trailer_size"
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
          v-model="editData.trailer_axle"
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
          v-model="editData.trailer_suspension"
          type="text"
          required
          class="text-sm sm:text-base input-style"
          placeholder="Ex: Air suspension"
        >
      </div>

      <div>
        <label for="trailer_tire" class="block text-sm font-medium text-gray-700 mb-2">
          Tire <span class="error-color">*</span>
        </label>
        <input
          id="trailer_tire"
          v-model="editData.trailer_tire"
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
          v-model="editData.trailer_king_pin"
          type="text"
          class="text-sm sm:text-base input-style"
          placeholder="Ex: 2 inch"
        >
      </div>

      <div>
        <label for="trailer_main_beam" class="block text-sm font-medium text-gray-700 mb-2">
          Main Beam
        </label>
        <input
          id="trailer_main_beam"
          v-model="editData.trailer_main_beam"
          type="text"
          class="text-sm sm:text-base input-style"
          placeholder="Ex: Q345B steel"
        >
      </div>

      <div>
        <label for="trailer_max_payload" class="block text-sm font-medium text-gray-700 mb-2">
          Max Payload (tons) <span class="error-color">*</span>
        </label>
        <input
          id="trailer_max_payload"
          v-model="editData.trailer_max_payload"
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
          v-model="editData.trailer_place_of_origin"
          type="text"
          required
          class="text-sm sm:text-base input-style"
          placeholder="Ex: China"
        >
      </div>

      <div>
        <label for="trailer_material" class="block text-sm font-medium text-gray-700 mb-2">
          Material
        </label>
        <input
          id="trailer_material"
          v-model="editData.trailer_material"
          type="text"
          class="text-sm sm:text-base input-style"
          placeholder="Ex: Q345B Steel"
        >
      </div>

      <div>
        <label for="trailer_landing_gear" class="block text-sm font-medium text-gray-700 mb-2">
          Landing Gear
        </label>
        <input
          id="trailer_landing_gear"
          v-model="editData.trailer_landing_gear"
          type="text"
          class="text-sm sm:text-base input-style"
          placeholder="Ex: JOST"
        >
      </div>

      <div>
        <label for="trailer_axle_brand" class="block text-sm font-medium text-gray-700 mb-2">
          Axle Brand
        </label>
        <input
          id="trailer_axle_brand"
          v-model="editData.trailer_axle_brand"
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
          v-model="editData.trailer_function"
          rows="3"
          class="text-sm sm:text-base input-style"
          placeholder="Describe the trailer's primary function..."
        ></textarea>
      </div>
    </div>

    <!-- TRUCK SPECIFICATIONS -->
    <div v-else class="grid grid-cols-1 sm:grid-cols-2 gap-4 sm:gap-6">
      <div>
        <label for="vehicle_make" class="block text-sm font-medium text-gray-700 mb-2">
          Vehicle brand <span class="error-color">*</span>
        </label>
        <select
          id="vehicle_make"
          required
          @change="updateModelid"
          :disabled="brandsLoading"
          v-model="editData.vehicle_make"
          class="text-sm sm:text-base input-style"
        >
          <option value="">{{ brandsLoading ? 'Loading...' : 'Select a vehicle brand' }}</option>
          <option v-for="brand in brands" :key="brand.id" :value="brand.name">
            {{ brand.name }}
          </option>
        </select>
      </div>

      <div>
        <label for="vehicle_condition" class="block text-sm font-medium text-gray-700 mb-2">
          Vehicle Condition
        </label>
        <select
          id="vehicle_condition"
          v-model="editData.vehicle_condition"
          class="text-sm sm:text-base input-style"
        >
          <option value="">Select a vehicle condition</option>
          <option value="new">New</option>
          <option value="used">Used</option>
          <option value="refurbished">Refurbished</option>
        </select>
      </div>

      <div>
        <label for="vehicle_model" class="block text-sm font-medium text-gray-700 mb-2">
          Vehicle Model
        </label>
        <select
          id="vehicle_model"
          v-model="editData.vehicle_model"
          required
          @change="updateFuelType"
          :disabled="!editData.vehicle_make || brandsLoading"
          class="text-sm sm:text-base input-style"
        >
          <option value="">Select a vehicle model</option>
          <option v-for="model in availableModels" :key="model.name" :value="model.name">
            {{ model.name }}
          </option>
        </select>
      </div>

      <div>
        <label for="fuel_type" class="block text-sm font-medium text-gray-700 mb-2">
          Fuel Type
        </label>
        <input
          type="text"
          v-model="editData.fuel_type"
          disabled
          class="text-sm sm:text-base input-style cursor-not-allowed overflow-ellipsis"
          placeholder="Le type de carburant sera défini automatiquement"
        >
      </div>

      <div>
        <label for="vehicle_year" class="block text-sm font-medium text-gray-700 mb-2">
          Year
        </label>
        <input
          id="vehicle_year"
          v-model="editData.vehicle_year"
          type="number"
          min="1990"
          :max="new Date().getFullYear() + 1"
          class="text-sm sm:text-base input-style"
          placeholder="Ex: 2020"
        >
      </div>

      <div>
        <label for="fuel_tank_capacity" class="block text-sm font-medium text-gray-700 mb-2">
          Fuel Tank Capacity (L)
        </label>
        <input
          id="fuel_tank_capacity"
          v-model="editData.fuel_tank_capacity"
          type="number"
          min="0"
          class="text-sm sm:text-base input-style"
          placeholder="Ex: 400"
        >
      </div>

      <div>
        <label for="production_date" class="block text-sm font-medium text-gray-700 mb-2">
          Production Date
        </label>
        <input
          id="production_date"
          v-model="editData.production_date"
          type="date"
          class="text-sm sm:text-base input-style"
        >
      </div>

      <div>
        <label for="drive_type" class="block text-sm font-medium text-gray-700 mb-2">
          Transmission Type
        </label>
        <select
          id="drive_type"
          v-model="editData.drive_type"
          class="text-sm sm:text-base input-style"
        >
          <option value="">All Types</option>
          <option value="4X2">4x2</option>
          <option value="6X2">6x2</option>
          <option value="6X4">6x4</option>
          <option value="6X6">6x6</option>
          <option value="8X4">8x4</option>
          <option value="8X6">8x6</option>
          <option value="8X8">8x8</option>
        </select>
      </div>

      <div>
        <label for="country_of_origin" class="block text-sm font-medium text-gray-700 mb-2">
          Country of Origin
        </label>
        <input
          id="country_of_origin"
          v-model="editData.country_of_origin"
          type="text"
          class="text-sm sm:text-base input-style"
          placeholder="Ex: Chine, Allemagne, Suède"
        >
      </div>

      <div>
        <label for="wheelbase" class="block text-sm font-medium text-gray-700 mb-2">
          Wheelbases(mm)
        </label>
        <input
          id="wheelbase"
          v-model="editData.wheelbase"
          type="number"
          min="0"
          class="text-sm sm:text-base input-style"
          placeholder="Ex: 3800"
        >
      </div>

      <div>
        <label for="curb_weight" class="block text-sm font-medium text-gray-700 mb-2">
          Curb Weight (Tonnes)
        </label>
        <input
          id="curb_weight"
          v-model="editData.curb_weight"
          type="text"
          class="text-sm sm:text-base input-style"
          placeholder="Ex: 2500"
        >
      </div>

      <div>
        <label for="speed" class="block text-sm font-medium text-gray-700 mb-2">
          Economic speed / Maximum speed (km/h)
        </label>
        <input
          id="speed"
          v-model="editData.speed"
          type="text"
          class="text-sm sm:text-base input-style"
          placeholder="Ex: 58 ~ 72/90"
        >
      </div>

      <div>
        <label for="payload_capacity" class="block text-sm font-medium text-gray-700 mb-2">
          Payload Capacity (kg)
        </label>
        <input
          id="payload_capacity"
          v-model="editData.payload_capacity"
          type="number"
          min="0"
          class="text-sm sm:text-base input-style"
          placeholder="Ex: 15000"
        >
      </div>

      <div>
        <label for="cabin_type" class="block text-sm font-medium text-gray-700 mb-2">
          Cabin Type
        </label>
        <input
          id="cabin_type"
          v-model="editData.cabin_type"
          type="text"
          class="text-sm sm:text-base input-style"
          placeholder="Ex: Cabine courte, Cabine longue"
        >
      </div>

      <div>
        <label for="gvw" class="block text-sm font-medium text-gray-700 mb-2">
          GVW - Gross Vehicle Weight (kg)
        </label>
        <input
          id="gvw"
          v-model="editData.gvw"
          type="number"
          min="0"
          class="text-sm sm:text-base input-style"
          placeholder="Ex: 25000"
        >
      </div>

      <div>
        <label for="chassis_dimensions" class="block text-sm font-medium text-gray-700 mb-2">
          Chassis Dimensions (mm) <span class="error-color">*</span>
        </label>
        <input
          id="chassis_dimensions"
          v-model="editData.chassis_dimensions"
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
          v-model="editData.frame_rear_suspension"
          type="number"
          min="0"
          class="text-sm sm:text-base input-style"
          placeholder="Ex: 725"
        >
      </div>

      <div class="sm:col-span-2">
        <label class="block text-sm font-medium text-gray-700 mb-2">
          Overall Dimensions LxlxH (mm)
        </label>
        <div class="grid grid-cols-3 gap-3">
          <input
            v-model="editData.dimension_length"
            type="number"
            min="0"
            class="text-sm sm:text-base input-style"
            placeholder="Length"
          >
          <input
            v-model="editData.dimension_width"
            type="number"
            min="0"
            class="text-sm sm:text-base input-style"
            placeholder="Width"
          >
          <input
            v-model="editData.dimension_height"
            type="number"
            min="0"
            class="text-sm sm:text-base input-style"
            placeholder="Height"
          >
        </div>
      </div>
    </div>

    <!-- Cab textarea - only for trucks -->
    <div v-if="!isTrailerCategory" class="w-full h-30 mb-10 mt-5">
      <label for="cab" class="block text-sm font-medium text-gray-700 mb-2">
        Cab <span class="error-color">*</span>
      </label>
      <textarea 
        id="cab"
        v-model="editData.cab"
        class="text-sm sm:text-base input-style h-full w-full"
        placeholder="Ex: F3000 extended flat-top cab, without roof deflector, hydraulic main seat,....">  
      </textarea>
    </div>
  </div>

  <!-- ============================================ -->
  <!-- SECTION 3: TECHNICAL SPECIFICATIONS (TRUCKS ONLY) -->
  <!-- ============================================ -->
  <div v-if="!isTrailerCategory" class="bg-white/80 backdrop-blur-sm rounded-xl p-4 sm:p-6 border border-gray-100 shadow-sm">
    <div class="flex items-center space-x-3 mb-4 sm:mb-6">
      <div class="w-8 h-8 bg-orange rounded-lg flex items-center justify-center">
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
          v-model="editData.engine_brand"
          class="text-sm sm:text-base input-style"
        >
          <option value="">Select a brand</option>
          <option value="weichai">Weichai</option>
          <option value="yuchai">Yuchai</option>
          <option value="sinotruck">Sinotruck</option>
          <option value="man">MAN</option>
        </select>
      </div>

      <div>
        <label for="gearbox_brand" class="block text-sm font-medium text-gray-700 mb-2">
          Gearbox Brand
        </label>
        <input
          id="gearbox_brand"
          v-model="editData.gearbox_brand"
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
          v-model="editData.engine_number"
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
          v-model="editData.gearbox_model"
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
          v-model="editData.power"
          type="number"
          min="0"
          class="text-sm sm:text-base input-style"
          placeholder="Ex: 400"
        >
      </div>

      <div>
        <label for="transmission_type" class="block text-sm font-medium text-gray-700 mb-2">
          Gearbox Type
        </label>
        <select
          id="transmission_type"
          v-model="editData.transmission_type"
          class="text-sm sm:text-base input-style"
        >
          <option value="">All transmissions</option>
          <option value="automatic">Automatic</option>
          <option value="manual">Manual</option>
        </select>
      </div>

      <div>
        <label for="engine_emissions" class="block text-sm font-medium text-gray-700 mb-2">
          Emission Standards
        </label>
        <input
          id="engine_emissions"
          v-model="editData.engine_emissions"
          type="text"
          class="text-sm sm:text-base input-style"
          placeholder="Ex: EURO II"
        >
      </div>

      <div>
        <label for="vin" class="block text-sm font-medium text-gray-700 mb-2">
          Vin numbers / Num Chassis
        </label>
        <input
          v-model="editData.vin_numbers[0]"
          type="text"
          class="text-sm sm:text-base input-style"
          placeholder="Ex: ENG123456789"
        />
      </div>

      <div>
        <label for="trim" class="block text-sm font-medium text-gray-700 mb-2">
          Vehicle Model (Trim)
        </label>
        <input
          v-model="editData.trim_numbers[0]"
          type="text"
          class="text-sm sm:text-base input-style"
          placeholder="Ex: ENG123456789"
        />
      </div>

      <div>
        <label for="vehicle_mileage" class="block text-sm font-medium text-gray-700 mb-2">
          Mileage (km)
        </label>
        <input
          id="vehicle_mileage"
          v-model="editData.vehicle_mileage"
          type="number"
          min="0"
          max="200000"
          class="text-sm sm:text-base input-style"
          placeholder="Ex: 150000"
        >
        <p class="text-xs text-gray-500 mt-1">Between 0 and 200,000 km</p>
      </div>

      <div>
        <label for="axle_brand" class="block text-sm font-medium text-gray-700 mb-2">
          Axle Brand
        </label>
        <input
          id="axle_brand"
          v-model="editData.axle_brand"
          type="text"
          class="text-sm sm:text-base input-style"
          placeholder="Ex: HANDE"
        >
      </div>

      <div>
        <label for="suspension_type" class="block text-sm font-medium text-gray-700 mb-2">
          Suspension type
        </label>
        <input
          id="suspension_type"
          v-model="editData.suspension_type"
          type="text"
          class="text-sm sm:text-base input-style"
          placeholder="Ex: Pneumatics, Mechanics"
        >
      </div>

      <div>
        <label for="axle_front" class="block text-sm font-medium text-gray-700 mb-2">
          Front axle
        </label>
        <input
          id="axle_front"
          v-model="editData.axle_front"
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
          v-model="editData.suspension_front"
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
          v-model="editData.axle_rear"
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
          v-model="editData.suspension_rear"
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
          v-model="editData.axle_speed_ratio"
          type="number"
          min="0"
          step="0.001"
          class="text-sm sm:text-base input-style"
          placeholder="Ex: 4.266"
        >
      </div>

      <div>
        <label for="brake_system" class="block text-sm font-medium text-gray-700 mb-2">
          Braking system
        </label>
        <input
          id="brake_system"
          v-model="editData.brake_system"
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
          v-model="editData.type_size"
          type="text"
          class="text-sm sm:text-base input-style"
          placeholder="Ex: 295/80R22.5"
        >
      </div>

      <div>
        <label for="air_filter" class="block text-sm font-medium text-gray-700 mb-2">
          Air filter
        </label>
        <input
          id="air_filter"
          v-model="editData.air_filter"
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
          v-model="editData.electrics"
          type="text"
          class="text-sm sm:text-base input-style"
          placeholder="Ex: 165Ah maintenance free battery"
        >
      </div>

      <div class="sm:col-span-2">
        <div class="flex items-center mb-3">
          <label for="wysiwygEditor2" class="ml-3 text-sm font-medium text-gray-700 flex items-center">
            <EditIcon class="w-4 h-4 primary-color mr-1" />
            Other Terms associated with the product (WYSIWYG)
          </label>
        </div>
        
        <div>
          <div class="border border-gray-300 rounded-lg focus-within:ring-1 focus-within:ring-orange-400 focus-within:border-orange-400 transition-all duration-200">
            <div class="flex items-center gap-1 p-2 border-b border-gray-200 bg-gray-50 rounded-t-lg flex-wrap">
              <button type="button" @click="formatText2('bold')" class="p-2 hover:bg-gray-200 rounded text-sm font-bold" title="Gras" style="background-color: lightgray; color: black;">B</button>
              <button type="button" @click="formatText2('italic')" class="p-2 hover:bg-gray-200 rounded text-sm italic" title="Italique" style="background-color: lightgray; color: black;">I</button>
              <button type="button" @click="formatText2('underline')" class="p-2 hover:bg-gray-200 rounded text-sm underline" title="Souligné" style="background-color: lightgray; color: black;">U</button>
              <div class="w-px h-6 bg-gray-300 mx-1"></div>
              <button type="button" @click="formatText2('insertUnorderedList')" class="p-2 hover:bg-gray-200 rounded text-sm" title="Liste à puces" style="background-color: lightgray; color: black;">•</button>
              <button type="button" @click="formatText2('insertOrderedList')" class="p-2 hover:bg-gray-200 rounded text-sm" title="Liste numérotée" style="background-color: lightgray; color: black;">1.</button>
              <div class="w-px h-6 bg-gray-300 mx-1"></div>
              <select @change="formatHeading2($event)" class="text-sm border border-gray-300 rounded px-4 py-2 text-black">
                <option value="">Titre</option>
                <option value="h1">Titre 1</option>
                <option value="h2">Titre 2</option>
                <option value="h3">Titre 3</option>
              </select>
            </div>
            <div 
              ref="wysiwygEditor2"
              contenteditable="true"
              @input="updateDescriptionPlus"
              class="min-h-[200px] p-4 focus:outline-none text-black rounded-b-lg"
              style="white-space: pre-wrap;"
              placeholder="Décrivez votre produit en détail..."
            ></div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- ============================================ -->
  <!-- SECTION 4: PRICE AND STOCK -->
  <!-- ============================================ -->
  <div class="bg-white/80 backdrop-blur-sm rounded-xl p-4 sm:p-6 border border-gray-100 shadow-sm">
    <div class="flex items-center space-x-3 mb-4 sm:mb-6">
      <div class="w-8 h-8 bg-orange rounded-lg flex items-center justify-center">
        <DollarSignIcon class="w-4 h-4 text-white" />
      </div>
      <h3 class="text-lg sm:text-xl font-semibold text-gray-900">Price and Stock</h3>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 sm:gap-6">
      <div>
        <label for="unit_price" class="block text-sm font-medium text-gray-700 mb-2">
          Unit price ($) <span class="error-color">*</span>
        </label>
        <input
          id="unit_price"
          v-model.number="editData.unit_price"
          type="number"
          min="0"
          step="1"
          required
          class="text-sm sm:text-base input-style"
          placeholder="15000"
        >
      </div>

      <div>
        <label for="available" class="block text-sm font-medium text-gray-700 mb-2">
          Product availability
        </label>
        <select
          id="available"
          v-model="editData.disponibility"
          class="text-sm sm:text-base input-style"
        >
          <option v-for="available in availability" :key="available.value" :value="available.value">
            {{ available.label }}
          </option>
        </select>
      </div>
    </div>
  </div>

  <!-- ============================================ -->
  <!-- SECTION 5: COLORS -->
  <!-- ============================================ -->
  <div class="bg-white/80 backdrop-blur-sm rounded-xl p-4 sm:p-6 border border-gray-100 shadow-sm">
    <div class="flex items-center space-x-3 mb-4 sm:mb-6">
      <div class="w-8 h-8 bg-orange rounded-lg flex items-center justify-center">
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
            :class="editData.colors.includes(color.value) ? 'border-orange-500 ring-2 ring-orange-200' : 'border-gray-300'"
          ></div>
          <CheckIcon 
            v-if="editData.colors.includes(color.value)"
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
          class="w-12 h-10 border border-gray-300 rounded-lg cursor-pointer"
        >
        <button 
          @click="addCustomColor"
          type="button"
          class="px-4 py-2 text-white rounded-lg transition-all font-medium text-sm btn-degrade-orange"
        >
          Add
        </button>
      </div>
    </div>

    <div v-if="colorsToAdd.length > 0 || colorsToRemove.length > 0" class="mt-6 space-y-4">
      <div v-if="colorsToAdd.length > 0" class="bg-green-50 border border-green-200 rounded-lg p-4">
        <h4 class="text-sm font-medium text-green-800 mb-2">Colors to add :</h4>
        <div class="flex flex-wrap gap-2">
          <span 
            v-for="color in colorsToAdd" 
            :key="color"
            class="inline-flex items-center px-2 py-1 rounded-md text-xs font-medium bg-green-100 text-green-800"
          >
            <div 
              class="w-3 h-3 rounded-full mr-1 border border-green-300"
              :style="{ backgroundColor: color }"
            ></div>
            {{ getColorName(color) }}
          </span>
        </div>
      </div>

      <div v-if="colorsToRemove.length > 0" class="bg-red-50 border border-red-200 rounded-lg p-4">
        <h4 class="text-sm font-medium error-color mb-2">Colors to delete :</h4>
        <div class="flex flex-wrap gap-2">
          <span 
            v-for="color in colorsToRemove" 
            :key="color"
            class="inline-flex items-center px-2 py-1 rounded-md text-xs font-medium bg-red-100 error-color"
          >
            <div 
              class="w-3 h-3 rounded-full mr-1 border border-red-300"
              :style="{ backgroundColor: color }"
            ></div>
            {{ getColorName(color) }}
          </span>
        </div>
      </div>
    </div>
  </div>

  <!-- ============================================ -->
  <!-- SECTION 6: PRODUCT IMAGES -->
  <!-- ============================================ -->
  <div class="bg-white/80 backdrop-blur-sm rounded-xl p-4 sm:p-6 border border-gray-100 shadow-sm">
    <div class="flex items-center space-x-3 mb-4 sm:mb-6">
      <div class="w-8 h-8 bg-orange rounded-lg flex items-center justify-center">
        <ImageIcon class="w-4 h-4 text-white" />
      </div>
      <h3 class="text-lg sm:text-xl font-semibold text-gray-900">Product images</h3>
      <span class="text-sm text-gray-500">({{ editData.all_images.length }}/8)</span>
    </div>

    <div class="mb-6">
      <div class="flex items-center justify-center w-full">
        <label 
          for="image-upload" 
          class="flex flex-col items-center justify-center w-full h-32 border-2 border-gray-300 border-dashed rounded-lg cursor-pointer bg-gray-50 hover:bg-gray-100 transition-colors"
          :class="{ 'opacity-50 cursor-not-allowed': editData.all_images.length >= 8 }"
        >
          <div class="flex flex-col items-center justify-center pt-5 pb-6">
            <ImageIcon class="w-8 h-8 mb-4 text-gray-500" />
            <p class="mb-2 text-sm text-gray-500">
              <span class="font-semibold">Click to add</span> images
            </p>
            <p class="text-xs text-gray-500">PNG, JPG up to 10MB per image</p>
          </div>
          <input 
            id="image-upload"
            ref="fileInput"
            type="file"
            multiple
            accept="image/*"
            @change="handleImageUpload"
            class="hidden"
            :disabled="editData.all_images.length >= 8"
          >
        </label>
      </div>
    </div>

    <div v-if="editData.all_images.length > 0" class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-4 mb-6">
      <div 
        v-for="(image, index) in editData.all_images" 
        :key="index"
        class="relative group"
      >
        <img 
          :src="typeof image === 'string' ? image : image.preview || image.url" 
          :alt="`Image ${index + 1}`"
          class="w-full h-24 sm:h-32 object-cover rounded-lg border border-gray-200"
        >

        <button
          style="background-color: red;"
          type="button"
          @click="removeImage(index)"
          class="absolute top-0 right-0 text-white text-xs rounded-md p-1"
        >
          <XIcon class="w-4 h-4" />
        </button>

        <button
          style="background-color: orange; font-size: 10px"
          v-if="index !== 0"
          type="button"
          @click="setMainImages(index)"
          class="absolute flex bottom-2 right-2 px-2 py-1 bg-blue-600 text-white text-xs rounded-md group-hover:opacity-100 transition"
        >
          Set Main
        </button>
        
        <div 
          v-if="index === 0"
          class="absolute bottom-2 left-2 px-2 py-1 bg-orange text-white text-xs rounded-md"
        >
          Main
        </div>

        <div v-if="image.uploading" class="absolute inset-0 bg-black/50 flex items-center justify-center rounded-lg">
          <div class="text-white text-sm font-medium">{{ image.uploadProgress }}%</div>
        </div>
        
        <div v-if="image.uploaded && image.url" class="absolute top-2 left-2 bg-green-500 text-white rounded-full w-6 h-6 flex items-center justify-center">
          <CheckIcon class="h-4 w-4" />
        </div>
        
        <div class="absolute bottom-2 left-15 flex space-x-1 opacity-0 group-hover:opacity-100 transition-opacity">
          <div class="w-6 h-6 bg-white rounded-full flex items-center justify-between shadow cursor-pointer" title="Déplacer vers la gauche">
            <ChevronLeftIcon class="w-4 h-4 text-black cursor-pointer" @click="moveImageUp(index)" />
          </div>
          <div class="w-6 h-6 bg-white rounded-full flex items-center justify-center shadow cursor-pointer" title="Déplacer vers la droite">
            <ChevronRightIcon class="w-4 h-4 text-black cursor-pointer" @click="moveImageDown(index)" />
          </div>
        </div>
      </div>
    </div>

    <div v-if="imagesToAdd.length > 0 || imagesToRemove.length > 0" class="space-y-4">
      <div v-if="imagesToAdd.length > 0" class="bg-green-50 border border-green-200 rounded-lg p-4">
        <h4 class="text-sm font-medium text-green-800 mb-3">Images à ajouter ({{ imagesToAdd.length }}) :</h4>
        <div class="grid grid-cols-3 sm:grid-cols-4 gap-3">
          <div 
            v-for="(image, index) in imagesToAdd" 
            :key="index"
            class="relative"
          >
            <img 
              :src="image.preview || image.url" 
              :alt="`Nouvelle image ${index + 1}`"
              class="w-full h-16 object-cover rounded border border-green-300"
            >
            <div class="absolute -top-1 -right-1 w-4 h-4 bg-green-500 text-white rounded-full flex items-center justify-center">
              <span class="text-xs">+</span>
            </div>
          </div>
        </div>
      </div>

      <div v-if="imagesToRemove.length > 0" class="bg-red-50 border border-red-200 rounded-lg p-4">
        <h4 class="text-sm font-medium error-color mb-3">Images to delete ({{ imagesToRemove.length }}) :</h4>
        <div class="grid grid-cols-3 sm:grid-cols-4 gap-3">
          <div 
            v-for="(imageUrl, index) in imagesToRemove" 
            :key="index"
            class="relative"
          >
            <img 
              :src="imageUrl" 
              :alt="`Image supprimée ${index + 1}`"
              class="w-full h-16 object-cover rounded border border-red-300 opacity-75"
            >
            <div class="absolute -top-1 -right-1 w-4 h-4 error-color text-white rounded-full flex items-center justify-center">
              <span class="text-xs">-</span>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div v-if="editData.all_images.length === 0" class="text-center py-8 text-gray-500">
      <ImageIcon class="w-12 h-12 mx-auto mb-4 text-gray-300" />
      <p>No image added</p>
      <p class="text-sm">Add up to 8 images for your product</p>
    </div>
  </div>

  <!-- ============================================ -->
  <!-- SECTION 7: VIDEO (OPTIONAL) -->
  <!-- ============================================ -->
  <div class="bg-white/80 backdrop-blur-sm rounded-xl p-4 sm:p-6 border border-gray-100 shadow-sm">
    <div class="flex items-center space-x-3 mb-4 sm:mb-6">
      <div class="w-8 h-8 bg-orange rounded-lg flex items-center justify-center">
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
            MP4, MOV, AVI files up to 50 MB
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
    
    <div v-if="editData.video" class="mt-6">
      <video :src="editData.video.preview || editData.video.url" controls class="w-full max-w-md mx-auto rounded-lg shadow-lg">
        Your browser does not support video playback.
      </video>
      
      <div v-if="editData.video.uploading" class="mt-4">
        <div class="flex items-center justify-center space-x-2">
          <div class="animate-spin rounded-full h-5 w-5 border-2 border-orange-500 border-t-transparent"></div>
          <span class="text-sm text-gray-600">uploading... {{ editData.video.uploadProgress }}%</span>
        </div>
        <div class="w-full bg-gray-200 rounded-full h-2 mt-2">
          <div 
            class="bg-orange h-2 rounded-full transition-all duration-300"
            :style="{ width: `${editData.video.uploadProgress}%` }"
          ></div>
        </div>
      </div>

      <div v-if="editData.video.uploaded" class="mt-4 p-3 bg-green-50 border border-green-200 rounded-lg flex items-center space-x-2">
        <CheckCircleIcon class="w-5 h-5 text-green-600" />
        <span class="text-sm text-green-700">Video successfully downloaded</span>
      </div>

      <button 
        @click="removeVideo"
        type="button"
        class="mt-4 px-4 py-2 error-background-color text-white rounded-lg hover:bg-red-700 transition-colors font-medium"
      >
        delete video
      </button>
    </div>
  </div>

  <!-- ============================================ -->
  <!-- SECTION 8: ACTIVE PRODUCT -->
  <!-- ============================================ -->
  <div class="bg-white/80 backdrop-blur-sm rounded-xl p-4 sm:p-6 border border-gray-100 shadow-sm">
    <div class="flex items-center mb-6">
      <input 
        v-model="editData.is_active"
        id="is-active"
        type="checkbox"
        class="checkbox-style"
      >
      <label for="is-active" class="ml-3 text-sm font-medium text-gray-700 flex items-center">
        <CheckCircleIcon class="w-4 h-4 text-green-600 mr-1" />
        Active product
      </label>
    </div>
  </div>

</form>
      </div>
  
      <div class="sticky bottom-0 bg-white/95 backdrop-blur-sm border-t border-gray-200 px-4 sm:px-6 py-4 sm:rounded-b-2xl z-50">
        <div class="flex gap-2 sm:gap-3">
          <button
            type="button"
            @click="closeModal"
            :disabled="isLoading"
            class="flex-1 px-4 py-3 sm:px-6 sm:py-3 rounded-lg text-sm font-medium    transition-colors disabled:opacity-50 disabled:cursor-not-allowed btn-gray"
          >
            Cancel
          </button>
          
          <button
            type="button"
            @click="handleSubmit"
            :disabled="isLoading || !canSubmit"
            class="flex-1 px-4 py-3 sm:px-6 sm:py-3 border border-transparent rounded-lg text-sm font-medium transition-all disabled:opacity-50 disabled:cursor-not-allowed btn-degrade-orange"
          >
            <div v-if="isLoading" class="flex items-center justify-center">
              <div class="animate-spin rounded-full h-4 w-4 border-2 border-white border-t-transparent mr-2"></div>
              Editing...
            </div>
            <div v-else class="flex items-center justify-center">
              <CheckIcon class="w-4 h-4 mr-2" />
              Edit product
            </div>
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, reactive, computed, onMounted, watch, nextTick } from 'vue'
import { categoriesApi,brandsApi } from '../../services/api'
import { 
  Edit as EditIcon,
  X as XIcon,
  Info as InfoIcon,
  DollarSign as DollarSignIcon,
  Palette as PaletteIcon,
  Image as ImageIcon,
  Check as CheckIcon,
  CheckCircle as CheckCircleIcon,
  AlertCircle as AlertCircleIcon,
  ChevronLeft as ChevronLeftIcon,
  ChevronRight as ChevronRightIcon,
  Video as VideoIcon
} from 'lucide-vue-next'

const props = defineProps({
  'product': Object,
  'boutique-id': [String, Number],
  'user-id': [String, Number]
})

//Models
const brands = ref([])
const brandsLoading = ref(false)
const brandsError = ref(null)

const emit = defineEmits(['close', 'save'])

const isLoading = ref(false)
const loadingMessage = ref('')
const error = ref(null)
const wysiwygEditor = ref(null)
const wysiwygEditor2 = ref(null)
const customColor = ref({ name: '', value: '#000000' })
const customSize = ref('')

// États pour les catégories API
const categories = ref([])
const categoriesLoading = ref(false)
const categoriesError = ref(null)

const originalColors = ref([])
const originalSizes = ref([])
const originalImages = ref([])
const imagesToRemove = ref([])
const newImages = ref([])

// Données du produit à éditer
const editData = ref({
  id: null,
  name: '',
  description_plus: '',
  description: '',
  category_id: '',
  subcategory_id: '',
  subsubcategory_id: '',
  subsubsubcategory_id: '',
  tags: '',
  unit_price: null,
  stock: null,
  unit_type: 'quantity',
  colors: [],
  sizes: [],
  sizeType: '',
  all_images: [],
  video: null,
  is_active: true,
  wholesale_price: null,
  wholesale_min_qty: null,
  // Vehicle specific fields
  vehicle_condition: '',
  vehicle_make: '',
  vehicle_model: '',
  drive_type: '',
  vehicle_year: null,
  fuel_type: '',
  transmission_type: '',
  engine_brand: '',
  vehicle_mileage: null,
  power:"",
  engine_emissions:'',
  // New fields
  vin_numbers: [],
  trim_numbers:[],
  engine_number:"",
  disponibility:"",
  production_date: '',
  country_of_origin: '',
  wheelbase: null,
  gvw: null,
  payload_capacity: null,
  cabin_type: '',
  suspension_type: '',
  brake_system: '',
  type_size: '',
  dimensions:'',
  dimension_length: '',
  dimension_width: '',
  dimension_height: '',
  curb_weight: null,
  fuel_tank_capacity: null,
  other_description: '',
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
  trailer_landing_gear: '',
  trailer_axle_brand: '',
  trailer_function: ''
})

const hasWholesalePrice = ref(false)


const availableColors = ref([
  { name: 'Noir', value: '#000000' },
  { name: 'Blanc', value: '#FFFFFF' },
  { name: 'Gris', value: '#808080' },
  { name: 'Rouge', value: '#FF0000' },
  { name: 'Bleu', value: '#0000FF' },
  { name: 'Vert', value: '#008000' },
  { name: 'Jaune', value: '#FFFF00' },
  { name: 'Orange', value: '#FFA500' },
  { name: 'Rose', value: '#FFC0CB' },
  { name: 'Violet', value: '#800080' },
  { name: 'Marron', value: '#8B4513' },
  { name: 'Or', value: '#FFD700' }
])

const availableUnitTypes = ref([
  { value: 'quantity', label: 'Quantité (unités)' },
  { value: 'weight_t', label: 'Poids (T)' },
  { value: 'weight_kg', label: 'Poids (kg)' },
  { value: 'weight_g', label: 'Poids (g)' },
  { value: 'volume_l', label: 'Volume (L)' },
  { value: 'volume_ml', label: 'Volume (mL)' },
  { value: 'length_m', label: 'Longueur (m)' },
  { value: 'length_cm', label: 'Longueur (cm)' },
  { value: 'area_m2', label: 'Surface (m²)' }
])

const colorsToAdd = computed(() => {
  return editData.value.colors.filter(color => !originalColors.value.includes(color))
})

const isTrailerCategory = computed(() => {
  const category = categories.value.find(cat => cat.id === editData.value.category_id)
  return category && (
    category.name.toLowerCase().includes('trailer') || 
    category.name.toLowerCase().includes('semi')
  )
})

const colorsToRemove = computed(() => {
  return originalColors.value.filter(color => !editData.value.colors.includes(color))
})

const imagesToAdd = computed(() => {
  return newImages.value
})

// Computed properties pour les sous-catégories
const availableSubcategories = computed(() => {
  const category = categories.value.find(cat => cat.id === editData.value.category_id)
  return category ? category.subcategories || [] : []
})

const availableSubSubcategories = computed(() => {
  for (const category of categories.value) {
    const subcategory = (category.subcategories || []).find(sub => sub.id === editData.value.subcategory_id)
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
        const subsubcategory = subcategory.sub_subcategories.find(subsub => subsub.id === editData.value.subsubcategory_id)
        if (subsubcategory && subsubcategory.sub_sub_subcategories) {
          return subsubcategory.sub_sub_subcategories
        }
      }
    }
  }
  return []
})

const canSubmit = computed(() => {
  return !!(editData.value.name && 
             editData.value.category_id && 
             editData.value.subcategory_id && 
             editData.value.unit_price !== null && 
             editData.value.unit_price !== '' && 
             Number(editData.value.unit_price) > 0 &&
             editData.value.stock !== null && 
             editData.value.stock !== '' && 
             Number(editData.value.stock) >= 0)
})

const getColorName = (colorValue) => {
  const color = availableColors.value.find(c => c.value === colorValue)
  return color ? color.name : colorValue
}

const availability = ref([
  { value: 'available', label: 'Available' },
  { value: 'unavailable', label: 'Unavailable' },
  { value: 'on_order', label: 'On order' },
])
// Méthodes
const fetchMethodes = async ()=>{
  await fetchCategories()
 await  fetchBrands()
}

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

const updateDetailedDescription = () => {
  editData.value.description = wysiwygEditor.value.innerHTML
}

const formatText2 = (command) => {
  document.execCommand(command, false, null)
  wysiwygEditor2.value.focus()
}

const formatHeading2 = (event) => {
  const heading = event.target.value
  if (heading) {
    document.execCommand('formatBlock', false, heading)
    event.target.value = ''
    wysiwygEditor2.value.focus()
  }
}

const updateDescriptionPlus = () => {
  editData.value.description_plus = wysiwygEditor2.value.innerHTML
}

const updateSubcategories = () => {
  editData.value.subcategory_id = ''
  editData.value.subsubcategory_id = ''
  editData.value.subsubsubcategory_id = ''
}

const updateSubSubcategories = () => {
  editData.value.subsubcategory_id = ''
  editData.value.subsubsubcategory_id = ''
}

const updateSubSubSubcategories = () => {
  editData.value.subsubsubcategory_id = ''
}


const toggleColor = (color) => {
  const index = editData.value.colors.indexOf(color)
  if (index > -1) {
    editData.value.colors.splice(index, 1)
  } else {
    editData.value.colors.push(color)
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
      editData.value.colors.push(customColor.value.value)
    }
    
    customColor.value = { name: '', value: '#000000' }
  }
}


// Les fonctions pour le chargement des marques, modèles et type de carburant des vehicules

const updateModelid = () => {
  editData.value.vehicle_model = ""
  editData.value.fuel_type = ""
}

const updateFuelType = () => {
  editData.value.fuel_type = availableFuelType.value
}

const setMainImages = (index) => {
  if (index === 0) return; // déjà l'image principale

  const selected = editData.value.all_images[index]

  // Retirer l'image de sa position
  editData.value.all_images.splice(index, 1)
  // La mettre en première position
  editData.value.all_images.unshift(selected)
}

const availableModels = computed(() => {
 const  brand = brands.value.find(cat => cat.name === editData.value.vehicle_make)
  return brand ? brand.models || [] : []
})

const availableFuelType = computed(() => {
 const  model =availableModels.value.find(model => model.name === editData.value.vehicle_model)
  return model ? model.fuel_type || '' : ''
})

const fetchBrands = async () => {
  try {
    brandsLoading.value = true
    brandsError.value = null
    
    const response = await brandsApi.getBrands()
    brands.value = response.data || []
  } catch (err) {
    brandsError.value = 'Impossible to load. Try again.'
  } finally {
    brandsLoading.value = false
  }
}

// Methode pour recuperer les longueurs, largeurs et hauteurs

function parseDimensions(dimensions) {
  if (!dimensions && dimensions !== 0) return { length: null, width: null, height: null }
  const parts = String(dimensions)
    .trim()
    .replace(/\s+/g, '')         // supprime les espaces
    .split(/[x×\*X]/)            // sépare par x, ×, *, X
    .filter(Boolean)

  const toNum = (s) => {
    const n = parseFloat(String(s).replace(',', '.'))
    return Number.isFinite(n) ? n : null
  }

  return {
    length: parts[0] ? toNum(parts[0]) : null,
    width:  parts[1] ? toNum(parts[1]) : null,
    height: parts[2] ? toNum(parts[2]) : null
  }
}

const closeModal = () => {
  error.value = null
  emit('close')
}

const cloudinaryConfig = {
  cloudName: 'daaavha4z',
  uploadPreset: 'aliadjame',
  apiKey: 'wy0Eh-uA0Y0Ci3nyODix0b3WejA',
  imageUploadUrl: 'https://api.cloudinary.com/v1_1/daaavha4z/image/upload',
  videoUploadUrl: 'https://api.cloudinary.com/v1_1/daaavha4z/video/upload'
}

const handleSubmit = async () => {
  if (!canSubmit.value) return

  try {
    isLoading.value = true
    error.value = null
    loadingMessage.value = 'Loading new images...'
    
    // Upload des nouvelles images
    const uploadPromises = newImages.value.map((image, index) => 
      uploadImageToCloudinary(image, index)
    )
    
    await Promise.all(uploadPromises)

    // Upload de la vidéo si nécessaire
    if (editData.value.video && editData.value.video.file && !editData.value.video.uploaded) {
      loadingMessage.value = 'Loading video...'
      await uploadVideoToCloudinary(editData.value.video)
    }
    
    // Préparer les URLs finales des images
    const finalImageUrls = editData.value.all_images.map(image => {
      if (typeof image === 'string') {
        return image // Image existante
      } else if (image.url) {
        return image.url // Nouvelle image uploadée
      }
      return null
    }).filter(Boolean)
    
    loadingMessage.value = 'Editing...'
    const formData = {
      id: editData.value.id,
      name: editData.value.name,
      description_plus: editData.value.description_plus,
      description: editData.value.description,
      category_id: editData.value.category_id,
      subcategory_id: editData.value.subcategory_id,
      subsubcategory_id: editData.value.subsubcategory_id || null,
      subsubsubcategory_id: editData.value.subsubsubcategory_id || null,
      tags: editData.value.tags,
      unit_price: parseFloat(editData.value.unit_price),
      stock: parseInt(editData.value.stock),
      unit_type: editData.value.unit_type,
      is_active: editData.value.is_active,
      colors: editData.value.colors,
      sizes: editData.value.sizes,
      size_type: editData.value.sizeType,
      images: finalImageUrls,
      image_add: newImages.value.filter(img => img.uploaded && img.url).map(img => img.url),
      image_remove: imagesToRemove.value,
      imagesToRemove: imagesToRemove.value,
      colorsToAdd: colorsToAdd.value,
      colorsToRemove: colorsToRemove.value,
      // Vehicle specific fields
      vehicle_condition: editData.value.vehicle_condition,
      vehicle_make: editData.value.vehicle_make,
      vehicle_model: editData.value.vehicle_model,
      drive_type: editData.value.drive_type,
      vehicle_year: editData.value.vehicle_year,
      fuel_type: editData.value.fuel_type,
      transmission_type: editData.value.transmission_type,
      engine_brand: editData.value.engine_brand,
      vehicle_mileage: editData.value.vehicle_mileage,
      power: editData.value.power,
      engine_emissions: editData.value.engine_emissions,
      // New fields
      vin_numbers: editData.value.vin_numbers.filter(num => num.trim() !== ''),
      trim_numbers: editData.value.trim_numbers.filter(num => num.trim() !== ''),
      engine_number: editData.value.engine_number,
      disponibility: editData.value.disponibility,
      production_date: editData.value.production_date,
      country_of_origin: editData.value.country_of_origin,
      wheelbase: editData.value.wheelbase,
      gvw: editData.value.gvw,
      payload_capacity: editData.value.payload_capacity,
      cabin_type: editData.value.cabin_type,
      suspension_type: editData.value.suspension_type,
      brake_system: editData.value.brake_system,
      tyre_size: editData.value.type_size,
      curb_weight: editData.value.curb_weight,
      fuel_tank_capacity: editData.value.fuel_tank_capacity,
      dimensions: editData.value.dimension_length || editData.value.dimension_width || editData.value.dimension_height
        ? `${editData.value.dimension_length || 0}x${editData.value.dimension_width || 0}x${editData.value.dimension_height || 0}`
        : null,
      trailer_condition: editData.value.trailer_condition,
      trailer_type: editData.value.trailer_type,
      trailer_brand: editData.value.trailer_brand,
      trailer_use: editData.value.trailer_use,
      trailer_size: editData.value.trailer_size,
      trailer_axle: editData.value.trailer_axle,
      trailer_suspension: editData.value.trailer_suspension,
      trailer_tire: editData.value.trailer_tire,
      trailer_king_pin: editData.value.trailer_king_pin,
      trailer_main_beam: editData.value.trailer_main_beam,
      trailer_max_payload: editData.value.trailer_max_payload,
      trailer_place_of_origin: editData.value.trailer_place_of_origin,
      trailer_material: editData.value.trailer_material,
      trailer_landing_gear: editData.value.trailer_landing_gear,
      trailer_axle_brand: editData.value.trailer_axle_brand,
      trailer_function: editData.value.trailer_function
    }


    if (hasWholesalePrice.value && editData.value.wholesale_price) {
      formData.wholesale_price = parseFloat(editData.value.wholesale_price)
      formData.wholesale_min_qty = parseInt(editData.value.wholesale_min_qty)
    }

    if (editData.value.video && editData.value.video.url) {
      formData.video_url = editData.value.video.url
    }

    emit('save', formData)
    
  } catch (err) {
    error.value = 'Error to edit product. Please try again.'
  } finally {
    isLoading.value = false
  }
}

const handleBackdropClick = (event) => {
  if (event.target === event.currentTarget) {
    closeModal()
  }
}

const handleImageUpload = (event) => {
  const files = Array.from(event.target.files)
  const remainingSlots = 8 - editData.value.all_images.length
  
  if (remainingSlots <= 0) {
    error.value = 'You have already reached the limit of 8 images'
    return
  }
  
  files.slice(0, remainingSlots).forEach(file => {
    if (file.size <= 10 * 1024 * 1024) {
      const reader = new FileReader()
      reader.onload = (e) => {
        const imageObj = {
          file: file,
          preview: e.target.result,
          uploading: false,
          uploadProgress: 0,
          uploaded: false,
          url: null,
          isNew: true
        }
        editData.value.all_images.push(imageObj)
        newImages.value.push(imageObj)
      }
      reader.readAsDataURL(file)
    } else {
      error.value = 'Some images are too large (max 10MB)'
    }
  })
  
  // Reset input
  event.target.value = ''
}

const handleVideoUpload = (event) => {
  const file = event.target.files[0]
  if (file && file.size <= 50 * 1024 * 1024) {
    const reader = new FileReader()
    reader.onload = (e) => {
      editData.value.video = {
        file: file,
        preview: e.target.result,
        uploading: false,
        uploadProgress: 0,
        uploaded: false,
        url: null
      }
    }
    reader.readAsDataURL(file)
  } else if (file) {
    error.value = 'The video is too large (max 50MB)'
  }
}

const removeVideo = () => {
  editData.value.video = null
}

const removeImage = (index) => {
  const imageToRemove = editData.value.all_images[index]
  
  if (typeof imageToRemove === 'string') {
    imagesToRemove.value.push(imageToRemove)
  } else if (imageToRemove.isNew) {
    // Si c'est une nouvelle image, la retirer du tableau des nouvelles images
    const newImageIndex = newImages.value.findIndex(img => img === imageToRemove)
    if (newImageIndex > -1) {
      newImages.value.splice(newImageIndex, 1)
    }
  }
  
  editData.value.all_images.splice(index, 1)
}

const moveImageUp = (index) => {
  const images = editData.value.all_images
  if (index > 0) {
    const temp = images[index]
    images[index] = images[index - 1]
    images[index - 1] = temp
  }
  editData.value.all_images = images
}

const moveImageDown = (index) => {
  const images = editData.value.all_images
  if (index < images.length - 1) {
    const temp = images[index]
    images[index] = images[index + 1]
    images[index + 1] = temp
  }
  editData.value.all_images = images
}

const uploadImageToCloudinary = async (image, index) => {
  try {
    image.uploading = true
    image.uploadProgress = 0
    
    const fileName = `product_${Date.now()}_${index}_${image.file.name.replace(/\s+/g, '_')}`
    
    const formData = new FormData()
    formData.append('file', image.file)
    formData.append('upload_preset', cloudinaryConfig.uploadPreset)
    formData.append('api_key', cloudinaryConfig.apiKey)
    formData.append('public_id', fileName)
    
    const response = await fetch(cloudinaryConfig.imageUploadUrl, {
      method: 'POST',
      body: formData
    })
    
    const data = await response.json()
    
    if (data && data.secure_url) {
      image.url = data.secure_url
      image.uploading = false
      image.uploaded = true
      image.uploadProgress = 100
      return data.secure_url
    } else {
      throw new Error('Réponse Cloudinary invalide')
    }
  } catch (error) {
    image.uploading = false
    return null
  }
}

const uploadVideoToCloudinary = async (video) => {
  try {
    video.uploading = true
    video.uploadProgress = 0
    
    const fileName = `product_video_${Date.now()}_${video.file.name.replace(/\s+/g, '_')}`
    
    const formData = new FormData()
    formData.append('file', video.file)
    formData.append('upload_preset', cloudinaryConfig.uploadPreset)
    formData.append('api_key', cloudinaryConfig.apiKey)
    formData.append('public_id', fileName)
    formData.append('resource_type', 'video')
    
    const xhr = new XMLHttpRequest()
    
    return new Promise((resolve, reject) => {
      xhr.upload.addEventListener('progress', (e) => {
        if (e.lengthComputable) {
          video.uploadProgress = Math.round((e.loaded / e.total) * 100)
        }
      })
      
      xhr.addEventListener('load', () => {
        if (xhr.status === 200) {
          const data = JSON.parse(xhr.responseText)
          if (data && data.secure_url) {
            video.url = data.secure_url
            video.uploading = false
            video.uploaded = true
            video.uploadProgress = 100
            resolve(data.secure_url)
          } else {
            reject(new Error('Réponse Cloudinary invalide'))
          }
        } else {
          reject(new Error(`Erreur HTTP: ${xhr.status}`))
        }
      })
      
      xhr.addEventListener('error', () => {
        reject(new Error('Erreur réseau'))
      })
      
      xhr.open('POST', cloudinaryConfig.videoUploadUrl)
      xhr.send(formData)
    })
  } catch (error) {
    video.uploading = false
    throw error
  }
}

watch(() => props.product, (newProduct) => {
  if (newProduct) {
    // Convertir les images existantes en format uniforme
    const existingImages = (newProduct.all_images || []).map(imageUrl => {
      if (typeof imageUrl === 'string') {
        return imageUrl
      }
      return imageUrl.url || imageUrl
    })
    editData.value = {
      id: newProduct.id,
      name: newProduct.name || '',
      description_plus: newProduct.description_plus || '',
      description: newProduct.description || '',
      category_id: newProduct.category_id || '',
      subcategory_id: newProduct.subcategory_id || '',
      subsubcategory_id: newProduct.subsubcategory_id || '',
      subsubsubcategory_id: newProduct.subsubsubcategory_id || '',
      tags: newProduct.tags || '',
      unit_price: newProduct.unit_price || null,
      stock: newProduct.stock || null,
      unit_type: newProduct.unit_type || 'quantity',
      colors: [...(newProduct.colors || [])],
      sizes: [...(newProduct.sizes || [])],
      sizeType: newProduct.size_type || '',
      all_images: existingImages,
      video: newProduct.video_url ? { url: newProduct.video_url, preview: newProduct.video_url, uploaded: true } : null,
      is_active: newProduct.is_active !== undefined ? newProduct.is_active : true,
      wholesale_price: newProduct.wholesale_price || null,
      wholesale_min_qty: newProduct.wholesale_min_qty || null,
      // Vehicle specific fields
      vehicle_condition: newProduct.vehicle_condition || '',
      vehicle_make: newProduct.vehicle_make || '',
      vehicle_model: newProduct.vehicle_model || '',
      drive_type: newProduct.drive_type || '',
      vehicle_year: newProduct.vehicle_year || null,
      fuel_type: newProduct.fuel_type || '',
      transmission_type: newProduct.transmission_type || '',
      engine_brand: newProduct.engine_brand || '',
      vehicle_mileage: newProduct.vehicle_mileage || null,
      engine_emissions:newProduct.engine_emissions,
      power: newProduct.power,
      // New fields
      vin_numbers: Array.isArray(newProduct.vin_numbers) ? [...newProduct.vin_numbers] : [],
      trim_numbers:Array.isArray(newProduct.trim_numbers) ? [...newProduct.trim_numbers] : [],
      engine_number: newProduct.engine_number,
      disponibility: newProduct.disponibility,
      production_date: newProduct.production_date || '',
      country_of_origin: newProduct.country_of_origin || '',
      wheelbase: newProduct.wheelbase || null,
      gvw: newProduct.gvw || null,
      payload_capacity: newProduct.payload_capacity || null,
      cabin_type: newProduct.cabin_type || '',
      suspension_type: newProduct.suspension_type || '',
      brake_system: newProduct.brake_system || '',
      type_size: newProduct.tyre_size || '',
      dimensions: newProduct.dimensions || null,
      curb_weight: newProduct.curb_weight || null,
      fuel_tank_capacity: newProduct.fuel_tank_capacity || null,
      speed: newProduct.speed,
      gearbox_brand: newProduct.gearbox_brand,
      gearbox_model: newProduct.gearbox_model,
      chassis_dimensions: newProduct.chassis_dimensions,
      frame_rear_suspension: newProduct.frame_rear_suspension,
      suspension_rear: newProduct.suspension_rear,
      suspension_front: newProduct.suspension_front,
      axle_brand: newProduct.axle_brand,
      axle_front: newProduct.axle_front,
      axle_rear: newProduct.axle_rear,
      axle_speed_ratio: newProduct.axle_speed_ratio,
      air_filter: newProduct.air_filter,
      electrics: newProduct.electrics,
      cab: newProduct.cab
    }

    const { length, width, height } = parseDimensions(newProduct.dimensions)
    editData.value.dimension_length = length
    editData.value.dimension_width  = width
    editData.value.dimension_height = height
    editData.value.trailer_condition = newProduct.trailer_condition || ''
    editData.value.trailer_type = newProduct.trailer_type || ''
    editData.value.trailer_brand = newProduct.trailer_brand || ''
    editData.value.trailer_use = newProduct.trailer_use || ''
    editData.value.trailer_size = newProduct.trailer_size || ''
    editData.value.trailer_axle = newProduct.trailer_axle || ''
    editData.value.trailer_suspension = newProduct.trailer_suspension || ''
    editData.value.trailer_tire = newProduct.trailer_tire || ''
    editData.value.trailer_king_pin = newProduct.trailer_king_pin || ''
    editData.value.trailer_main_beam = newProduct.trailer_main_beam || ''
    editData.value.trailer_max_payload = newProduct.trailer_max_payload || null
    editData.value.trailer_place_of_origin = newProduct.trailer_place_of_origin || ''
    editData.value.trailer_material = newProduct.trailer_material || ''
    editData.value.trailer_landing_gear = newProduct.trailer_landing_gear || ''
    editData.value.trailer_axle_brand = newProduct.trailer_axle_brand || ''
    editData.value.trailer_function = newProduct.trailer_function || ''
    
    originalColors.value = [...(newProduct.colors || [])]
    originalSizes.value = [...(newProduct.sizes || [])]
    originalImages.value = [...existingImages]
    
    // Reset des tableaux de tracking
    imagesToRemove.value = []
    newImages.value = []
    
    // Vérifier si le produit a un prix de gros
    hasWholesalePrice.value = !!(newProduct.wholesale_price && newProduct.wholesale_price > 0)

    
  }
  // Mettre à jour l'éditeur WYSIWYG si nécessaire
  nextTick(() => {
    if (wysiwygEditor.value) {
      const html = editData.value.description || ''
      if (wysiwygEditor.value.innerHTML !== html) {
        wysiwygEditor.value.innerHTML = html
      }
    }
    if (wysiwygEditor2.value) {
      const html2 = editData.value.description_plus || ''
      if (wysiwygEditor2.value.innerHTML !== html2) {
        wysiwygEditor2.value.innerHTML = html2
      }
    }
  })
}, { immediate: true })

watch(() => editData.value.description_plus, (val) => {
  if (!wysiwygEditor2.value) return
  const html = val || ''
  if (wysiwygEditor2.value.innerHTML !== html) {
    wysiwygEditor2.value.innerHTML = html
  }
})

// Charger les catégories au montage du composant
onMounted(() => {
  fetchMethodes()
})
</script>

<style scoped>
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
</style>