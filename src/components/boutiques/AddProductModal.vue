<template>
  <div 
    class="fixed inset-0 bg-black/60 backdrop-blur-sm z-50 p-0 sm:p-4 sm:flex sm:items-center sm:justify-center"
    @click="handleBackdropClick"
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
              <h2 class="text-xl sm:text-2xl font-bold text-gray-900">Ajouter un produit</h2>
              <p class="text-sm text-gray-600 hidden sm:block">Créez un nouveau produit pour votre boutique</p>
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
                Réessayer
              </button>
            </div>
          </div>

          <div v-if="isLoading" class="mb-3 p-3 bg-blue-50 border border-blue-200 text-orange-400 rounded-lg flex items-center space-x-2">
            <div class="animate-spin w-4 h-4 border-2 border-orange-400 border-t-transparent rounded-full flex-shrink-0"></div>
            <span class="text-sm">{{ loadingMessage }}</span>
          </div>

          <div v-if="categoriesLoading" class="mb-3 p-3 bg-blue-50 border border-blue-200 text-orange-400 rounded-lg flex items-center space-x-2">
            <div class="animate-spin w-4 h-4 border-2 border-orange-400 border-t-transparent rounded-full flex-shrink-0"></div>
            <span class="text-sm">Chargement des catégories...</span>
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
                    'w-8 h-8 sm:w-10 sm:h-10 rounded-full flex items-center justify-center text-xs sm:text-sm font-medium transition-all duration-200',
                    currentStep > index 
                      ? 'bg-gradient-to-r from-green-400 to-green-500 text-white' 
                      : currentStep === index 
                        ? 'btn-degrade-orange shadow-lg' 
                        : 'bg-gray-200 text-gray-500'
                  ]"
                >
                  <CheckIcon v-if="currentStep > index" class="w-4 h-4 sm:w-5 sm:h-5" />
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
              <div 
                v-if="index < steps.length - 1"
                :class="[
                  'flex-1 h-0.5 mx-2 sm:mx-4 transition-colors',
                  currentStep > index ? 'bg-green-400' : 'bg-gray-200'
                ]"
              ></div>
            </div>
          </div>
        </div>
      </div>

      <div class="overflow-y-auto h-[calc(100vh-200px)] sm:h-auto sm:max-h-[calc(60vh)] px-4 sm:px-6 py-6 relative z-5" style="color: white;">
        <form @submit.prevent="handleSubmit" class="space-y-6 sm:space-y-8">
          
          <div v-show="currentStep === 0" class="space-y-6">
            <div class="bg-white/80 backdrop-blur-sm rounded-xl p-4 sm:p-6 border border-gray-100 shadow-sm">
              <div class="flex items-center space-x-3 mb-4 sm:mb-6">
                <div class="w-8 h-8  rounded-lg flex items-center justify-center bg-orange">
                  <InfoIcon class="w-4 h-4 text-white" />
                </div>
                <h3 class="text-lg sm:text-xl font-semibold text-gray-900">Informations de base</h3>
              </div>

              <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 sm:gap-6">
                <div class="sm:col-span-2">
                  <label for="name" class="block text-sm font-medium text-gray-700 mb-2">
                    Nom du produit 
                  </label>
                  <input
                    id="name"
                    v-model="productData.name"
                    type="text"
                    required
                    disabled="true"
                    class=" text-sm sm:text-base  input-style"
                    placeholder="Le nom du produit sera généré automatiquement"
                  >
                </div>

                <div>
                  <label for="category" class="block text-sm font-medium text-gray-700 mb-2">
                    Catégorie <span class="error-color">*</span>
                  </label>
                  <select
                    id="category"
                    v-model="productData.category_id"
                    @change="updateSubcategories"
                    required
                    :disabled="categoriesLoading"
                    class="text-sm sm:text-base input-style"
                    placeholder=""
                  >
                    <option value="">{{ categoriesLoading ? 'Chargement...' : 'Sélectionner une catégorie' }}</option>
                    <option v-for="category in categories" :key="category.id" :value="category.id">
                      {{ category.name }}
                    </option>
                  </select>
                </div>

                <div>
                  <label for="subcategory" class="block text-sm font-medium text-gray-700 mb-2">
                    Sous-catégorie <span class="error-color">*</span>
                  </label>
                  <select
                    id="subcategory"
                    v-model="productData.subcategory_id"
                    @change="updateSubSubcategories"
                    required
                    :disabled="!productData.category_id || categoriesLoading"
                    class="text-sm sm:text-base input-style"
                  >
                    <option value="">Sélectionner une sous-catégorie</option>
                    <option v-for="subcategory in availableSubcategories" :key="subcategory.id" :value="subcategory.id">
                      {{ subcategory.name }}
                    </option>
                  </select>
                </div>

                <div v-if="availableSubSubcategories.length > 0" class="sm:col-span-1">
                  <label for="subsubcategory" class="block text-sm font-medium text-gray-700 mb-2">
                    Sous-sous-catégorie
                  </label>
                  <select
                    id="subsubcategory"
                    v-model="productData.subsubcategory_id"
                    @change="updateSubSubSubcategories"
                    :disabled="!productData.subcategory_id || categoriesLoading"
                    class="text-sm sm:text-base input-style"
                  >
                    <option value="">Sélectionner une sous-sous-catégorie (optionnel)</option>
                    <option v-for="subsubcategory in availableSubSubcategories" :key="subsubcategory.id" :value="subsubcategory.id">
                      {{ subsubcategory.name }}
                    </option>
                  </select>
                </div>

                <div v-if="availableSubSubSubcategories.length > 0" class="sm:col-span-1">
                  <label for="subsubsubcategory" class="block text-sm font-medium text-gray-700 mb-2">
                    Sous-sous-sous-catégorie
                  </label>
                  <select
                    id="subsubsubcategory"
                    v-model="productData.subsubsubcategory_id"
                    :disabled="!productData.subsubcategory_id || categoriesLoading"
                    class="text-sm sm:text-base input-style"
                    style="color: black"
                  >
                    <option value="">Sélectionner une sous-sous-sous-catégorie (optionnel)</option>
                    <option v-for="subsubsubcategory in availableSubSubSubcategories" :key="subsubsubcategory.id" :value="subsubsubcategory.id">
                      {{ subsubsubcategory.name }}
                    </option>
                  </select>
                </div>
                <div class="sm:col-span-2">
                  <label for="wysiwygEditor" class="block text-sm font-medium text-gray-700 mb-2">
                    Description détaillée (WYSIWYG) <span class="error-color">*</span>
                  </label>
                  <div >
                    <div class="border border-gray-300 rounded-lg focus-within:ring-1 focus-within:ring-orange-400 focus-within:border-orange-400 transition-all duration-200">
                       Barre d'outils WYSIWYG 
                      <div class="flex items-center gap-1 p-2 border-b border-gray-200 bg-gray-50 rounded-t-lg flex-wrap">
                        <button type="button" @click="formatText('bold')" class="p-2 hover:bg-gray-200 rounded text-sm font-bold" title="Gras" style="background-color: lightgray; color: black;">B</button>
                        <button type="button" @click="formatText('italic')" class="p-2 hover:bg-gray-200 rounded text-sm italic" title="Italique" style="background-color: lightgray; color: black;">I</button>
                        <button type="button" @click="formatText('underline')" class="p-2 hover:bg-gray-200 rounded text-sm underline" title="Souligné" style="background-color: lightgray; color: black;">U</button>
                        <div class="w-px h-6 bg-gray-300 mx-1"></div>
                        <button type="button" @click="formatText('insertUnorderedList')" class="p-2 hover:bg-gray-200 rounded text-sm" title="Liste à puces" style="background-color: lightgray; color: black;">•</button>
                        <button type="button" @click="formatText('insertOrderedList')" class="p-2 hover:bg-gray-200 rounded text-sm" title="Liste numérotée" style="background-color: lightgray; color: black;">1.</button>
                        <div class="w-px h-6 bg-gray-300 mx-1"></div>
                        <select @change="formatHeading($event)" class="text-sm border border-gray-300 rounded px-4 py-2 text-black">
                          <option value="">Titre</option>
                          <option value="h1">Titre 1</option>
                          <option value="h2">Titre 2</option>
                          <option value="h3">Titre 3</option>
                        </select>
                      </div>
                      <div 
                        ref="wysiwygEditor"
                        contenteditable="true"
                        @input="updateDetailedDescription"
                        class="min-h-[200px] p-4 focus:outline-none text-black rounded-b-lg"
                        style="white-space: pre-wrap;"
                        placeholder="Décrivez votre produit en détail..."
                      ></div>
                    </div>
                  </div>
                </div>

                <div class="sm:col-span-2">
                  <label for="tags" class="block text-sm font-medium text-gray-700 mb-2">
                    Tags (optionnel)
                  </label>
                  <input
                    id="tags"
                    v-model="productData.tags"
                    type="text"
                    class="text-sm sm:text-base input-style"
                    placeholder="Ex: nouveau, tendance, promotion (séparés par des virgules)"
                  >
                </div>
              </div>
            </div>
          </div>

          <div v-show="currentStep === 1" class="space-y-6">
            <div class="bg-white/80 backdrop-blur-sm rounded-xl p-4 sm:p-6 border border-gray-100 shadow-sm">
              <div class="flex items-center space-x-3 mb-4 sm:mb-6">
                <div class="w-8 h-8 rounded-lg flex items-center justify-center bg-gradient-to-br bg-orange">
                  <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                  </svg>
                </div>
                <h3 class="text-lg sm:text-xl font-semibold text-gray-900">Spécifications Véhicule</h3>
              </div>

              <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 sm:gap-6">
                
                <div>
                  <label for="vehicle_condition" class="block text-sm font-medium text-gray-700 mb-2">
                    État du véhicule <span class="error-color">*</span>
                  </label>
                  <select
                    id="vehicle_condition"
                    v-model="productData.vehicle_condition"
                    class="text-sm sm:text-base input-style"
                    required
                  >
                    <option value="">Selectionner l'état du véhicule</option>
                    <option value="New">Neuf</option>
                    <option value="Used">Occasion</option>
                    <option value="Refurbished">Reconditionné</option>
                  </select>
                </div>

                <div>
                  <label for="vehicle_make" class="block text-sm font-medium text-gray-700 mb-2">
                    Marque du véhicule <span class="error-color">*</span>
                  </label>
                  <select
                    id="vehicle_make"
                    required
                    @change="updateModelid"
                    :disabled="brandsLoading"
                    v-model="productData.vehicle_brand_id"
                    class="text-sm sm:text-base input-style"
                  >
                    <option value="">{{ brandsLoading ? 'Chargement...' : 'Selectionner la marque du véhicule' }}</option>
                    <option v-for="make in brands" :key="make.id" :value="make.id">
                      {{ make.name }}
                    </option>
                  </select>
                </div>

                <div>
                  <label for="vehicle_model" class="block text-sm font-medium text-gray-700 mb-2">
                    Modèle du véhicule <span class="error-color">*</span>
                  </label>
                  <select
                    id="vehicle_model"
                    v-model="productData.vehicle_model_id"
                    required
                    @change="updateFuelType"
                    :disabled="!productData.vehicle_brand_id || brandsLoading"
                    class="text-sm sm:text-base input-style"
                  >
                    <option value="">Selectionner le modèle du véhicule</option>
                    <option v-for="model in availableModels" :key="model.id" :value="model.id">
                      {{ model.name  }}
                    </option>
                  </select>
                </div>

                <div>
                  <label for="drive_type" class="block text-sm font-medium text-gray-700 mb-2">
                    Type de transmission <span class="error-color">*</span>
                  </label>
                  <select
                    id="drive_type"
                    v-model="productData.drive_type"
                    class="text-sm sm:text-base input-style"
                  >
                    <option value="">Selectionner le type de transmission</option>
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
                  <label for="vehicle_year" class="block text-sm font-medium text-gray-700 mb-2">
                    Année <span class="error-color">*</span>
                  </label>
                  <input
                    id="vehicle_year"
                    v-model="productData.vehicle_year"
                    type="number"
                    min="1990"
                    :max="new Date().getFullYear() + 1"
                    class="text-sm sm:text-base input-style"
                    placeholder="Ex: 2020"
                    required
                  >
                </div>

                <div>
                  <label for="fuel_type" class="block text-sm font-medium text-gray-700 mb-2">
                    Type de carburant
                  </label>
                  <input
                    type="text"
                    :value="productData.fuel_type"
                    disabled
                    class="text-sm sm:text-base input-style bg-gray-100 cursor-not-allowed"
                    placeholder="Le type de carburant sera défini automatiquement"
                  >
                  <!-- <input v-else
                    type="text"
                    value="Le type de carburant sera défini automatiquement"
                    disabled
                    class="text-sm sm:text-base input-style bg-gray-100 cursor-not-allowed"
                  > -->
                  <!-- <select
                    id="fuel_type"
                    v-model="productData.fuel_type"
                    :disabled="!productData.vehicle_model_id || brandsLoading"
                    class="text-sm sm:text-base input-style"
                  >
                    <option value="">{{ brandsLoading ? 'Chargement...' : 'Type de carburant' }}</option>
                    <option value=availableFuelType :key="model.id" :value="productData.fuel_type">
                      {{ productData.fuel_type }}
                    </option>
                    
                  </select> -->
                </div>

                <div>
                  <label for="production_date" class="block text-sm font-medium text-gray-700 mb-2">
                    Date de production
                  </label>
                  <input
                    id="production_date"
                    v-model="productData.production_date"
                    type="date"
                    class="text-sm sm:text-base input-style"
                  >
                </div>

                <div>
                  <label for="country_of_origin" class="block text-sm font-medium text-gray-700 mb-2">
                    Pays d'origine
                  </label>
                  <input
                    id="country_of_origin"
                    v-model="productData.country_of_origin"
                    type="text"
                    class="text-sm sm:text-base input-style"
                    placeholder="Ex: Allemagne, Chine, Japon"
                  >
                </div>

                <div>
                  <label for="wheelbase" class="block text-sm font-medium text-gray-700 mb-2">
                    Empattement
                  </label>
                  <input
                    id="wheelbase"
                    v-model="productData.wheelbase"
                    type="number"
                    min="0"
                    class="text-sm sm:text-base input-style"
                    placeholder="Ex: 3800"
                  >
                </div>

                <div>
                  <label for="gvw" class="block text-sm font-medium text-gray-700 mb-2">
                    GVW - Poids total en charge (kg)
                  </label>
                  <input
                    id="gvw"
                    v-model="productData.gvw"
                    type="number"
                    min="0"
                    class="text-sm sm:text-base input-style"
                    placeholder="Ex: 25000"
                  >
                </div>

                <div>
                  <label for="payload_capacity" class="block text-sm font-medium text-gray-700 mb-2">
                    Capacité de charge utile (kg)
                  </label>
                  <input
                    id="payload_capacity"
                    v-model="productData.payload_capacity"
                    type="number"
                    min="0"
                    class="text-sm sm:text-base input-style"
                    placeholder="Ex: 15000"
                  >
                </div>

                <div>
                  <label for="cabin_type" class="block text-sm font-medium text-gray-700 mb-2">
                    Type de cabine
                  </label>
                  <input
                    id="cabin_type"
                    v-model="productData.cabin_type"
                    type="text"
                    class="text-sm sm:text-base input-style"
                    placeholder="Ex: Cabine courte, Cabine longue"
                  >
                </div>

                <div>
                  <label for="curb_weight" class="block text-sm font-medium text-gray-700 mb-2">
                    Poids à vide (Tonnes)
                  </label>
                  <input
                    id="curb_weight"
                    v-model="productData.curb_weight"
                    type="number"
                    min="0"
                    class="text-sm sm:text-base input-style"
                    placeholder="Ex: 10000"
                  >
                </div>

                <div>
                  <label for="fuel_tank_capacity" class="block text-sm font-medium text-gray-700 mb-2">
                    Capacité du réservoir (L)
                  </label>
                  <input
                    id="fuel_tank_capacity"
                    v-model="productData.fuel_tank_capacity"
                    type="number"
                    min="0"
                    class="text-sm sm:text-base input-style"
                    placeholder="Ex: 400"
                  >
                </div>

                <div class="sm:col-span-2">
                  <label class="block text-sm font-medium text-gray-700 mb-2">
                    Dimensions (L x l x H) (m)
                  </label>
                  <div class="grid grid-cols-3 gap-2">
                    <input
                      v-model="productData.dimensions_length"
                      type="number"
                      min="0"
                      class="text-sm sm:text-base input-style"
                      placeholder="Longueur"
                    >
                    <input
                      v-model="productData.dimensions_width"
                      type="number"
                      min="0"
                      class="text-sm sm:text-base input-style"
                      placeholder="Largeur"
                    >
                    <input
                      v-model="productData.dimensions_height"
                      type="number"
                      min="0"
                      class="text-sm sm:text-base input-style"
                      placeholder="Hauteur"
                    >
                  </div>
                </div>
              </div>
            </div>
          </div>

          <div v-show="currentStep === 2" class="space-y-6">
            <div class="bg-white/80 backdrop-blur-sm rounded-xl p-4 sm:p-6 border border-gray-100 shadow-sm">
              <div class="flex items-center space-x-3 mb-4 sm:mb-6">
                <div class="w-8 h-8 rounded-lg flex items-center justify-center bg-gradient-to-br bg-orange">
                  <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                  </svg>
                </div>
                <h3 class="text-lg sm:text-xl font-semibold text-gray-900">Caractéristiques Techniques</h3>
              </div>

              <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 sm:gap-6">
                <div>
                  <label for="transmission_type" class="block text-sm font-medium text-gray-700 mb-2">
                   Type de Transmission
                  </label>
                  <select
                    id="transmission_type"
                    v-model="productData.transmission_type"
                    class="text-sm sm:text-base input-style"
                  >
                    <option value="">Toutes les transmissions</option>
                    <option value="Automatic">Automatique</option>
                    <option value="Manual">Manuelle</option>
                  </select>
                </div>

                <div>
                  <label for="engine_brand" class="block text-sm font-medium text-gray-700 mb-2">
                    Marque du moteur
                  </label>
                  <select
                    id="engine_brand"
                    v-model="productData.engine_brand"
                    class="text-sm sm:text-base input-style"
                  >
                    <option value="">Sélectionnez une marque</option>
                    <option value="Weichai">Weichai</option>
                    <option value="Yuchai">Yuchai</option>
                    <option value="Sinotruck">Sinotruck</option>
                    <option value="Man">MAN</option>
                  </select>
                </div>
                <div>
                  <label for="fuel_tank_capacity" class="block text-sm font-medium text-gray-700 mb-2">
                    Puissance du Moteur (ch/kW)
                  </label>
                  <input
                    id="power"
                    v-model="productData.power"
                    type="number"
                    min="0"
                    class="text-sm sm:text-base input-style"
                    placeholder="Ex: 400"
                  >
                </div>
                <div>
                  <label for="fuel_tank_capacity" class="block text-sm font-medium text-gray-700 mb-2">
                    Émissions du moteur (g/km)
                  </label>
                  <input
                    id="fuel_tank_capacity"
                    v-model="productData.engine_emissions"
                    type="number"
                    min="0"
                    class="text-sm sm:text-base input-style"
                    placeholder="Ex: 40"
                  >
                </div>
                <div class="sm:col-span-2">
                  <label class="block text-sm font-medium text-gray-700 mb-2">
                    Numéros VIN / Num Chassis
                  </label>
                  <div class="space-y-2">
                    <div v-for="(vin, index) in productData.vin" :key="index" class="flex gap-2">
                      <div class="font-semibold text-gray-700 items-center justify-center">{{index + 1}}</div>
                      <input
                        v-model="productData.vin[index]"
                        type="text"
                        class="flex-1 text-sm sm:text-base input-style"
                        :placeholder="`Numéro VIN ${index + 1}`"
                      >
                      <button
                        type="button"
                        @click="removeEngineVin(index)"
                        class="px-3 py-2 btn-deconnexion"
                      >
                        <XIcon class="w-4 h-4" />
                      </button>
                    </div>
                    <button
                      type="button"
                      @click="addEngineVin"
                      class="submit-btn w-full"
                    >
                      + Ajouter un numéro VIN
                    </button>
                  </div>
                </div>

                <div class="sm:col-span-2">
                  <label class="block text-sm font-medium text-gray-700 mb-2">
                    Numéros de moteur
                  </label>
                  <div class="space-y-2">
                    <div v-for="(engineNumber, index) in productData.engine_numbers" :key="index" class="flex gap-2">
                      <div class="font-semibold text-gray-700 items-center justify-center">{{index + 1}}</div>
                      <input
                        v-model="productData.engine_numbers[index]"
                        type="text"
                        class="flex-1 text-sm sm:text-base input-style"
                        :placeholder="`Numéro de moteur ${index + 1}`"
                      >
                      <button
                        type="button"
                        @click="removeEngineNumber(index)"
                        class="px-3 py-2 btn-deconnexion"
                      >
                        <XIcon class="w-4 h-4" />
                      </button>
                    </div>
                    <button
                      type="button"
                      @click="addEngineNumber"
                      class="submit-btn w-full"
                    >
                      + Ajouter un numéro de moteur
                    </button>
                  </div>
                </div>

                <div >
                  <label for="vehicle_mileage" class="block text-sm font-medium text-gray-700 mb-2">
                    Kilométrage (km)
                  </label>
                  <input
                    id="vehicle_mileage"
                    v-model="productData.vehicle_mileage"
                    type="number"
                    min="0"
                    max="200000"
                    class="text-sm sm:text-base input-style"
                    placeholder="Ex: 150000"
                  >
                  <p class="text-xs text-gray-500 mt-1">Entre 0 et 200,000 km</p>
                </div>

                <div>
                  <label for="suspension_type" class="block text-sm font-medium text-gray-700 mb-2">
                    Type de suspension
                  </label>
                  <input
                    id="suspension_type"
                    v-model="productData.suspension_type"
                    type="text"
                    class="text-sm sm:text-base input-style"
                    placeholder="Ex: Pneumatique, Mécanique"
                  >
                </div>

                <div>
                  <label for="brake_system" class="block text-sm font-medium text-gray-700 mb-2">
                    Système de freinage
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
                    Taille des pneus
                  </label>
                  <input
                    id="tyre_size"
                    v-model="productData.type_size"
                    type="text"
                    class="text-sm sm:text-base input-style"
                    placeholder="Ex: 315/80R22.5"
                  >
                </div>

                <div class="sm:col-span-2">
                  <label for="other_description" class="block text-sm font-medium text-gray-700 mb-2">
                    Autres descriptions
                  </label>
                  <textarea
                    id="other_description"
                    v-model="productData.description_plus"
                    rows="4"
                    class="text-sm sm:text-base input-style resize-none"
                    placeholder="Ajoutez d'autres informations pertinentes..."
                  ></textarea>
                </div>
              </div>
            </div>
          </div>

          <div v-show="currentStep === 3" class="space-y-6">
            <div class="bg-white/80 backdrop-blur-sm rounded-xl p-4 sm:p-6 border border-gray-100 shadow-sm">
              <div class="flex items-center space-x-3 mb-4 sm:mb-6">
                <div class="w-8 h-8  rounded-lg flex items-center justify-center bg-orange">
                  <DollarSignIcon class="w-4 h-4 text-white" />
                </div>
                <h3 class="text-lg sm:text-xl font-semibold text-gray-900">Prix et Stock</h3>
              </div>

              <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-1 sm:gap-6">
                 <!-- Prix unitaire  -->
                <div>
                  <label for="unit_price" class="block text-sm font-medium text-gray-700 mb-2">
                    Prix unitaire ($) <span class="error-color">*</span>
                  </label>
                  <input
                    id="unit_price"
                    v-model.number="productData.unit_price"
                    type="number"
                    min="0"
                    step="1"
                    required
                    class="text-sm sm:text-base input-style"
                    placeholder="15000"
                  >
                </div>

                <div>
                  <label for="stock" class="block text-sm font-medium text-gray-700 mb-2">
                    Stock initial <span class="error-color">*</span>
                  </label>
                  <input
                    id="stock"
                    v-model.number="productData.stock"
                    type="number"
                    min="0"
                    step="1"
                    required
                    class="text-sm sm:text-base input-style"
                    placeholder="50"
                  >
                </div>

                <div>
                  <label for="unit_type" class="block text-sm font-medium text-gray-700 mb-2">
                    Unité
                  </label>
                  <select
                    id="unit_type"
                    v-model="productData.unit_type"
                    class="text-sm sm:text-base input-style"
                  >
                    <option v-for="unit in availableUnitTypes" :key="unit.value" :value="unit.value">
                      {{ unit.label }}
                    </option>
                  </select>
                </div>
              </div>

              <div class="mt-6">
                <div class="flex items-center mb-4">
                  <input 
                    v-model="productData.hasWholesalePrice"
                    id="wholesale-price"
                    type="checkbox"
                    class="checkbox-style"
                  >
                  <label for="wholesale-price" class="ml-3 text-sm font-medium text-gray-700 flex items-center">
                    <ZapIcon class="w-4 h-4  mr-1 primary-color" />
                    Activer le prix de gros
                  </label>
                </div>
                
                <div v-if="productData.hasWholesalePrice" class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                  <div>
                    <label for="wholesale_price" class="block text-sm font-medium text-gray-700 mb-2">
                      Prix de gros
                    </label>
                    <input
                      id="wholesale_price"
                      v-model.number="productData.wholesale_price"
                      type="number"
                      min="0"
                      class="text-sm sm:text-base input-style"
                      placeholder="12000"
                    >
                  </div>
                  <div>
                    <label for="wholesale_min_qty" class="block text-sm font-medium text-gray-700 mb-2">
                      Quantité minimale
                    </label>
                    <input
                      id="wholesale_min_qty"
                      v-model.number="productData.wholesale_min_qty"
                      type="number"
                      min="1"
                      class="text-sm sm:text-base input-style"
                      placeholder="10"
                    >
                  </div>
                </div>
              </div>
            </div>
          </div>

          <div v-show="currentStep === 4" class="space-y-6">
             Couleurs 
            <div class="bg-white/80 backdrop-blur-sm rounded-xl p-4 sm:p-6 border border-gray-100 shadow-sm">
              <div class="flex items-center space-x-3 mb-4 sm:mb-6">
                <div class="w-8 h-8  rounded-lg flex items-center justify-center bg-orange">
                  <PaletteIcon class="w-4 h-4 text-white " />
                </div>
                <h3 class="text-lg sm:text-xl font-semibold text-gray-900">Couleurs</h3>
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
                <label class="block text-sm font-medium text-gray-700 mb-2">Ajouter une couleur personnalisée</label>
                <div class="flex gap-2">
                  <input 
                    v-model="customColor.name"
                    type="text" 
                    placeholder="Nom de la couleur"
                    class="flex-1  text-sm input-style"
                  >
                  <input 
                    v-model="customColor.value"
                    type="color" 
                    class="w-12 h-10 border text-orange-500  border-gray-300 rounded-lg cursor-pointer"
                  >
                  <button 
                    @click="addCustomColor"
                    type="button"
                    class="px-4 py-2 font-medium text-sm btn-degrade-orange"
                  >
                    Ajouter
                  </button>
                </div>
              </div>
            </div>

            <div class="bg-white/80 backdrop-blur-sm rounded-xl p-4 sm:p-6 border border-gray-100 shadow-sm">
              <div class="flex items-center space-x-3 mb-4 sm:mb-6">
                <div class="w-8 h-8 rounded-lg flex items-center justify-center bg-orange">
                  <RulerIcon class="w-4 h-4 text-white" />
                </div>
                <h3 class="text-lg sm:text-xl font-semibold text-gray-900">Tailles</h3>
              </div>

              <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">Type de taille</label>
                <select 
                  v-model="productData.sizeType"
                  @change="updateAvailableSizes"
                  class="text-sm sm:text-base input-style"
                >
                  <option value="">Sélectionner le type de taille</option>
                  <option v-for="sizeType in sizeTypes" :key="sizeType.value" :value="sizeType.value">
                    {{ sizeType.label }}
                  </option>
                </select>
              </div>
              
              <div v-if="productData.sizeType" class="grid grid-cols-4 sm:grid-cols-6 lg:grid-cols-8 gap-2 mb-4">
                <button
                  v-for="size in currentAvailableSizes"
                  :key="size"
                  type="button"
                  @click="toggleSize(size)"
                  :class="[
                    'px-3 py-2 sm:px-4 sm:py-3 rounded-lg border-2 transition-all duration-200 text-xs sm:text-sm font-medium',
                    productData.sizes.includes(size)
                      ? ' btn-degrade-orange'
                      : 'border-gray-300  text-gray-700 hover:border-orange-400 bg-lightgray'
                  ]"
                >
                  {{ size }}
                </button>
              </div>
              
              <div v-if="productData.sizeType" class="border-t border-gray-200 pt-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">Ajouter une taille personnalisée</label>
                <div class="flex gap-2">
                  <input 
                    v-model="customSize"
                    type="text" 
                    :placeholder="getSizePlaceholder(productData.sizeType)"
                    class="flex-1  text-sm input-style"
                  >
                  <button 
                    @click="addCustomSize"
                    type="button"
                    class="font-medium text-sm btn-degrade-orange"
                  >
                    Ajouter
                  </button>
                </div>
              </div>
            </div>
          </div>

          <div v-show="currentStep === 5" class="space-y-6">
             Images 
            <div class="bg-white/80 backdrop-blur-sm rounded-xl p-4 sm:p-6 border border-gray-100 shadow-sm">
              <div class="flex items-center space-x-3 mb-4 sm:mb-6">
                <div class="w-8 h-8 rounded-lg flex items-center justify-center bg-orange">
                  <ImageIcon class="w-4 h-4 text-white" />
                </div>
                <h3 class="text-lg sm:text-xl font-semibold text-gray-900">Images du produit</h3>
              </div>

              <div class="border-2 border-dashed border-gray-300 rounded-xl p-6 sm:p-8 text-center hover:border-orange-400 transition-colors">
                <div class="w-12 h-12 sm:w-16 sm:h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                  <UploadIcon class="w-6 h-6 sm:w-8 sm:h-8 text-gray-400" />
                </div>
                <p class="text-sm sm:text-base text-gray-600 mb-2">Glissez vos images ici ou</p>
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
                  class="inline-flex items-center  text-sm font-medium    btn-degrade-orange"
                >
                  Parcourir les fichiers
                </button>
                <p class="text-xs text-gray-500 mt-2">PNG, JPG jusqu'à 10MB par image (max 8)</p>
              </div>

              <div v-if="productData.images.length > 0" class="mt-6">
                <h4 class="text-sm font-medium text-gray-700 mb-3">Images sélectionnées</h4>
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
                    
                    <div v-if="image.uploading" class="absolute inset-0 bg-black/50 flex items-center justify-center rounded-lg">
                      <div class="text-white text-sm font-medium">{{ image.uploadProgress }}%</div>
                    </div>
                    
                    <div v-if="image.uploaded && image.url" class="absolute top-2 right-2 bg-green-500 text-white rounded-full w-6 h-6 flex items-center justify-center">
                      <CheckIcon class="h-4 w-4" />
                    </div>
                    
                    <button
                      type="button"
                      @click="removeImage(index)"
                      class="absolute -top-2 -right-2 w-6 h-6 bg-red-500 text-white rounded-full flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity"
                    >
                      <XIcon class="w-4 h-4" />
                    </button>
                    <div 
                      v-if="index === 0"
                      class="absolute bottom-2 left-2 px-2 py-1 bg-orange text-white text-xs rounded-md "
                    >
                      Principal
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <div class="bg-white/80 backdrop-blur-sm rounded-xl p-4 sm:p-6 border border-gray-100 shadow-sm">
              <div class="flex items-center space-x-3 mb-4 sm:mb-6">
                <div class="w-8 h-8  rounded-lg flex items-center justify-center bg-orange">
                  <VideoIcon class="w-4 h-4 text-white" />
                </div>
                <h3 class="text-lg sm:text-xl font-semibold text-gray-900">Vidéo (optionnel)</h3>
              </div>

              <div class="border-2 border-dashed border-gray-300 rounded-xl p-6 text-center hover:border-orange-400 transition-colors">
                <VideoIcon class="mx-auto h-12 w-12 text-gray-400 mb-4" />
                <div>
                  <label for="video-upload" class="cursor-pointer">
                    <span class="block text-sm text-gray-600 mb-2">
                      Cliquez pour télécharger une vidéo
                    </span>
                    <span class="block text-xs text-gray-500">
                      MP4, MOV, AVI jusqu'à 50MB
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
                  Votre navigateur ne supporte pas la lecture de vidéos.
                </video>
                
                <div v-if="productData.video.uploading" class="mt-4 bg-blue-50 border border-blue-200 rounded-lg p-3">
                  <div class="flex items-center space-x-2">
                    <div class="animate-spin w-4 h-4 border-2 border-orange-400 border-t-transparent rounded-full"></div>
                    <span class="text-sm text-gray-700">Téléchargement de la vidéo: {{ productData.video.uploadProgress }}%</span>
                  </div>
                </div>
                
                <div v-if="productData.video.uploaded" class="mt-4 bg-green-50 border border-green-200 rounded-lg p-3 flex items-center space-x-2">
                  <CheckIcon class="w-4 h-4 text-green-600" />
                  <span class="text-sm text-green-700">Vidéo téléchargée avec succès</span>
                </div>
                
                <button 
                  @click="removeVideo"
                  type="button"
                  class="mt-4 px-4 py-2 bg-red-500 text-white rounded-lg hover:bg-red-600 transition-colors font-medium"
                >
                  Supprimer la vidéo
                </button>
              </div>
            </div>

            <div class="bg-white/80 backdrop-blur-sm rounded-xl p-4 sm:p-6 border border-gray-100 shadow-sm">
              <div class="flex items-center mb-6">
                <input 
                  v-model="productData.is_active"
                  id="is-active"
                  type="checkbox"
                  class="checkbox-style "
                >
                <label for="is-active" class="ml-3 text-sm font-medium text-gray-700 flex items-center">
                  <CheckCircleIcon class="w-4 h-4 text-green-600 mr-1" />
                  Publier immédiatement ce produit
                </label>
              </div>

              <div class="bg-gray-50 rounded-lg p-4">
                <h4 class="font-semibold text-gray-900 mb-4 flex items-center">
                  <FileTextIcon class="w-5 h-5  mr-2 primary-color" />
                  Résumé du produit
                </h4>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-sm">
                  <div class="space-y-2">
                    <p><span class="font-medium text-gray-700">Nom du produit:</span> <span class="text-gray-900">{{ productData.name || 'Non défini' }}</span></p>
                    <p><span class="font-medium text-gray-700">Catégorie:</span> <span class="text-gray-900">{{ getCategoryName(productData.category_id) || 'Non définie' }}</span></p>
                    <p><span class="font-medium text-gray-700">Prix unitaire ($):</span> <span class="text-gray-900">{{ productData.unit_price ? productData.unit_price  : 'Non défini' }}</span></p>
                    <p><span class="font-medium text-gray-700">Stock initial:</span> <span class="text-gray-900">{{ productData.stock || 'Non défini' }}</span></p>
                    <p><span class="font-medium text-gray-700">Prix de gros:</span> <span class="text-gray-900">{{ productData.wholesale_price || 'Non défini' }}</span></p>
                    <p><span class="font-medium text-gray-700">Quantité minimale:</span> <span class="text-gray-900">{{ productData.wholesale_min_qty || 'Non défini' }}</span></p>
                  </div>
                  <div class="space-y-2">
                    <p><span class="font-medium text-gray-700">Tags:</span> <span class="text-gray-900">{{ productData.tags }}</span></p>
                    <p><span class="font-medium text-gray-700">Unité:</span> <span class="text-gray-900">{{ productData.unit_type || 'Non défini' }}</span></p>
                    <p><span class="font-medium text-gray-700">Tailles:</span> <span class="text-gray-900">{{ productData.sizes.length }} sélectionnée(s)</span></p>
                    <p><span class="font-medium text-gray-700">Images:</span> <span class="text-gray-900">{{ productData.images.length }}/8</span></p>
                    <p><span class="font-medium text-gray-700">Vidéo:</span> <span class="text-gray-900">{{ productData.video ? 'Oui' : 'Non' }}</span></p>
                  </div>
                </div>
              </div>


              <div class="specifications-table two-columns">
                  <div class="spec-group">
                    <h3 class="spec-group-title">Informations générales</h3>
                    <!-- verifié -->
                    <div class="spec-row">
                      <div class="spec-name">Condition du véhicule</div>
                      <div class="spec-value">{{ productData.vehicle_condition || 'N/A'}}</div>
                    </div>
                    <!-- verifié -->
                    <div class="spec-row">
                      <div class="spec-name">Marque du véhicule</div>
                      <div class="spec-value">{{getBrandName( productData.vehicle_brand_id) || 'N/A' }}</div>
                    </div>
                    <!-- vérifié -->
                    <div class="spec-row">
                      <div class="spec-name">Modèle du véhicule</div>
                      <div class="spec-value">{{getModelName( productData.vehicle_model_id) || 'N/A' }}</div>
                    </div>
                    <!-- verifié -->
                    <div class="spec-row">
                      <div class="spec-name">Type de transmission</div>
                      <div class="spec-value">{{ productData.drive_type || 'N/A'}}</div>
                    </div>
                    <!-- verifié -->
                    <div class="spec-row">
                      <div class="spec-name">Année</div>
                      <div class="spec-value">{{ productData.vehicle_year || 'N/A'}}</div>
                    </div>
                    <!-- verifié -->
                    <div class="spec-row">
                      <div class="spec-name">Type de carburant</div>
                      <div class="spec-value">{{ productData.fuel_type || 'N/A' }}</div>
                    </div>
                    <!-- verifié -->
                    <div class="spec-row">
                      <div class="spec-name">Type de Transmission</div>
                      <div class="spec-value">{{ productData.transmission_type || 'N/A'}}</div>
                    </div>
                    <!-- verifié -->
                    <div class="spec-row">
                      <div class="spec-name">Marque du moteur</div>
                      <div class="spec-value">{{ productData.engine_brand || 'N/A' }}</div>
                    </div>
                    <div class="spec-row">
                      <div class="spec-name">Puissance du Moteur (ch/kW)</div>
                      <div class="spec-value">{{ productData.power || 'N/A' }}</div>
                    </div>
                    <div class="spec-row">
                      <div class="spec-name">Émissions du moteur (g/km)</div>
                      <div class="spec-value">{{ productData.engine_emissions || 'N/A' }}</div>
                    </div>
                    <!-- vérifié -->
                    <div class="spec-row">
                      <div class="spec-name">Kilométrage (km)</div>
                      <div class="spec-value">{{ productData.vehicle_mileage || 'N/A' }}</div>
                    </div>
                    <div class="spec-row">
                      <div class="spec-name">Système de freinage</div>
                      <div class="spec-value">{{ productData.brake_system || 'N/A' }}</div>
                    </div>
                    <!-- vérifié -->
                    <div class="spec-row">
                      <div class="spec-name">Taille des pneus</div>
                      <div class="spec-value">{{ productData.type_size || 'N/A' }}</div>
                    </div>
                    <!-- vérifié -->
                    <div class="spec-row">
                      <div class="spec-name">Dimensions (L x l x H) (m)</div>
                      <div class="spec-value">{{ `${productData.dimensions_length || 0}x${productData.dimensions_width || 0}x${productData.dimensions_height || 0}` }}</div>
                    </div>
                    <!-- verifié -->
                    <div class="spec-row overflow-x-auto items-center">
                      <div class="spec-name">Couleurs</div>
                      <div v-for="color in productData.colors" :key="color.hex_value" >
                        <div  class="spec-value ">
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
                    <h3 class="spec-group-title">Détails techniques</h3>
                    <div class="spec-row">
                      <div class="spec-name"> Numéros VIN / Num Chassis</div>
                      <div class="spec-value">
                        <ul v-if="productData.vin && productData.vin.length">
                          <li v-for="vin in productData.vin" :key="vin.id">
                            {{ vin }}
                          </li>
                        </ul>
                        <span v-else>N/A</span>
                      </div>
                    </div>
                    <!-- TODO: parcourir la liste des numéros de moteur -->
                    <div class="spec-row">
                      <div class="spec-name">Numéros de moteur</div>
                      <div class="spec-value">
                        <ul v-if="productData.engine_numbers && productData.engine_numbers.length">
                          <li v-for="engineNumber in productData.engine_numbers" :key="engineNumber.id">
                            {{ engineNumber }}
                          </li>
                        </ul>
                        <span v-else>N/A</span>
                      </div>
                    </div>
                    <!-- verifié -->
                    <div class="spec-row">
                      <div class="spec-name">Date de production</div>
                      <div class="spec-value">{{ productData.production_date || 'N/A' }}</div>
                    </div>
                    <!-- vérifié -->
                    <div class="spec-row">
                      <div class="spec-name">Pays d'origine</div>
                      <div class="spec-value">{{ productData.country_of_origin || 'N/A' }}</div>
                    </div>
                    <!-- vérifié -->
                    <div class="spec-row">
                      <div class="spec-name">Empattement</div>
                      <div class="spec-value">{{ productData.wheelbase || 'N/A' }}</div>
                    </div>
                    <!-- vérifié -->
                    <div class="spec-row">
                      <div class="spec-name">GVW - Poids total en charge (kg)</div>
                      <div class="spec-value">{{ productData.gvw || 'N/A' }}</div>
                    </div>
                    <!-- vérifié -->
                    <div class="spec-row">
                      <div class="spec-name">Capacité de charge utile (kg)</div>
                      <div class="spec-value">{{ productData.payload_capacity || 'N/A' }}</div>
                    </div>
                    <!-- vérifié -->
                    <div class="spec-row">
                      <div class="spec-name">Type de cabine</div>
                      <div class="spec-value">{{ productData.cabin_type || 'N/A' }}</div>
                    </div>
                    <!-- vérifié -->
                    <div class="spec-row">
                      <div class="spec-name">Type de suspension</div>
                      <div class="spec-value">{{ productData.suspension_type || 'N/A' }}</div>
                    </div>
                    <!-- vérifié -->
                    <div class="spec-row">
                      <div class="spec-name">Poids à vide (Tonnes)</div>
                      <div class="spec-value">{{ productData.curb_weight || 'N/A' }}</div>
                    </div>
                    <!-- vérifié -->
                    <div class="spec-row">
                      <div class="spec-name">Capacité du réservoir (L)</div>
                      <div class="spec-value">{{ productData.fuel_tank_capacity || 'N/A' }}</div>
                    </div>
                  </div>
                </div>
            </div>
          </div>

        </form>
      </div>

      <div class="sticky bottom-0 bg-white/95 backdrop-blur-sm border-t border-gray-200 px-4 sm:px-6 py-4 sm:rounded-b-2xl z-50">
        <div class="flex flex-col sm:flex-row gap-3 sm:gap-0 sm:justify-between sm:items-center">
          <div class="flex gap-2 sm:gap-3 order-2 sm:order-1">
            <button
              v-if="currentStep > 0"
              type="button"
              @click.prevent="handlePreviousStep"
              :disabled="isLoading"
              class="flex-1 sm:flex-none px-4 py-3 sm:px-6 sm:py-3 text-sm font-medium  touch-manipulation btn-gray"
            >
              <ChevronLeftIcon class="w-4 h-4 mr-2 inline" />
              Précédent
            </button>
          </div>

          <div class="flex gap-2 sm:gap-3 order-1 sm:order-2">
            <button
              type="button"
              @click.prevent="handleCloseModal"
              :disabled="isLoading"
              class="flex-1 sm:flex-none px-4 py-3 sm:px-6 sm:py-3 text-sm font-medium  touch-manipulation btn-gray"
            >
              Annuler
            </button>
            
            <button
              v-if="currentStep < steps.length - 1"
              type="button"
              @click.prevent="handleNextStep"
              :disabled="!canProceedToNextStep || isLoading"
              class="flex-1 sm:flex-none px-4 py-3 sm:px-6 sm:py-3 border border-transparent rounded-lg text-sm font-medium  focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-orange-400  disabled:opacity-50 disabled:cursor-not-allowed active:scale-95 touch-manipulation btn-degrade-orange"
            >
              Suivant ({{ currentStep + 1 }}/{{ steps.length }})
              <ChevronRightIcon class="w-4 h-4 ml-2 inline" />
            </button>

            <button
              v-else
              type="button"
              @click.prevent="handleSubmit"
              :disabled="isLoading || !canSubmit"
              class="flex-1 sm:flex-none px-4 py-3 sm:px-6 sm:py-3 border border-transparent rounded-lg text-sm font-medium text-white bg-gradient-to-r from-green-500 to-green-600 hover:from-green-600 hover:to-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition-all disabled:opacity-50 disabled:cursor-not-allowed active:scale-95 touch-manipulation"
            >
              <div v-if="isLoading" class="flex items-center justify-center">
                <div class="animate-spin rounded-full h-4 w-4 border-2 border-white border-t-transparent mr-2"></div>
                Création...
              </div>
              <div v-else class="flex items-center justify-center">
                <CheckIcon class="w-4 h-4 mr-2" />
                Créer le produit
              </div>
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, reactive, computed, onMounted, nextTick } from 'vue'
import axios from 'axios'
import { categoriesApi , brandsApi } from '../../services/api'
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
const customSize = ref('')
const isLoading = ref(false)
const loadingMessage = ref('')
const error = ref(null)
const wysiwygEditor = ref(null)

// États pour les catégories API
const categories = ref([])
const categoriesLoading = ref(false)
const categoriesError = ref(null)

//Models
const brands = ref([])
const brandsLoading = ref(false)
const brandsError = ref(null)

// Configuration Cloudinary
const cloudinaryConfig = {
  cloudName: 'daaavha4z',
  uploadPreset: 'aliadjame',
  apiKey: 'wy0Eh-uA0Y0Ci3nyODix0b3WejA',
  imageUploadUrl: 'https://api.cloudinary.com/v1_1/daaavha4z/image/upload',
  videoUploadUrl: 'https://api.cloudinary.com/v1_1/daaavha4z/video/upload'
}

const steps = [
  { title: 'Informations' },
  { title: 'Spécifications' },
  { title: 'Techniques' },
  { title: 'Prix & Stock' },
  { title: 'Variantes' },
  { title: 'Images' }
]

//  Added new fields to productData
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
  hasWholesalePrice: false,
  wholesale_price: null,
  wholesale_min_qty: null,
  colors_names: [],
  colors: [],
  sizes: [],
  sizeType: '',
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
  engine_numbers: [],
  vin: [],
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
  type_size: '',
  dimensions_length: null,
  dimensions_width: null,
  dimensions_height: null,
  curb_weight: null,
  fuel_tank_capacity: null
})

// Types de tailles
const sizeTypes = ref([
  { 
    value: 'clothing', 
    label: 'Tailles de vêtements',
    sizes: ['XS', 'S', 'M', 'L', 'XL', 'XXL', 'XXXL']
  },
  { 
    value: 'shoes', 
    label: 'Pointures de chaussures',
    sizes: ['35', '36', '37', '38', '39', '40', '41', '42', '43', '44', '45', '46', '47']
  },
  { 
    value: 'storage', 
    label: 'Capacité de stockage',
    sizes: ['16GB', '32GB', '64GB', '128GB', '256GB', '512GB', '1TB', '2TB']
  },
  { 
    value: 'screen', 
    label: 'Taille d\'écran',
    sizes: ['5.5"', '6.1"', '6.7"', '13"', '15"', '17"', '21"', '24"', '27"', '32"']
  },
  { 
    value: 'ring', 
    label: 'Taille de bague',
    sizes: ['48', '50', '52', '54', '56', '58', '60', '62', '64', '66', '68']
  },
  { 
    value: 'watch', 
    label: 'Taille de bracelet de montre',
    sizes: ['38mm', '40mm', '42mm', '44mm', '45mm', '49mm']
  },
  { 
    value: 'custom', 
    label: 'Tailles personnalisées',
    sizes: []
  }
])

const currentAvailableSizes = computed(() => {
  const sizeType = sizeTypes.value.find(type => type.value === productData.sizeType)
  return sizeType ? sizeType.sizes : []
})

const availableSubcategories = computed(() => {
  const category = categories.value.find(cat => cat.id === productData.category_id)
  return category ? category.subcategories || [] : []
})


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

// Les types de models en focntion de la marque
const availableModels = computed(() => {
 const  models = brands.value.find(cat => cat.id === productData.vehicle_brand_id)
 
  return models ? models.models || [] : []
})

const availableFuelType = computed(() => {
  const  modelsObject = availableModels.value 
 const  fuelType =modelsObject.find(model => model.id === productData.vehicle_model_id)
 productData.fuel_type= fuelType ? fuelType.fuel_type || '' : ''
  return fuelType ? fuelType.fuel_type || '' : ''
})

// Validation des étapes
const canProceedToNextStep = computed(() => {
  switch (currentStep.value) {
    case 0:
      return !!(productData.description && productData.category_id && productData.subcategory_id)
    case 1:
      return !!(productData.vehicle_condition && productData.vehicle_brand_id && productData.vehicle_model_id && productData.vehicle_year && productData.drive_type)
    case 2:
      getProductName();
      return true
    case 3:
      const hasValidPrice = productData.unit_price !== null && 
                           productData.unit_price !== '' && 
                           productData.unit_price !== undefined && 
                           Number(productData.unit_price) > 0
      
      const hasValidStock = productData.stock !== null && 
                           productData.stock !== '' && 
                           productData.stock !== undefined && 
                           Number(productData.stock) >= 0
      
      return hasValidPrice && hasValidStock
    case 4:
      return true
    case 5:
      return true
    default:
      return false
  }
})

const canSubmit = computed(() => {
  return !!(productData.name && 
           productData.category_id && 
           productData.subcategory_id && 
           productData.unit_price !== null && 
           productData.unit_price !== '' && 
           Number(productData.unit_price) > 0 &&
           productData.stock !== null && 
           productData.stock !== '' && 
           Number(productData.stock) >= 0)
})

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

//  Added engine number management functions
const addEngineNumber = () => {
  productData.engine_numbers.push('')
}

const removeEngineNumber = (index) => {
  productData.engine_numbers.splice(index, 1)
}
//  Added engine VIN management functions
const addEngineVin = () => {
  productData.vin.push('')
}

const removeEngineVin = (index) => {
  productData.vin.splice(index, 1)
}

// Méthodes
const fetchCategories = async () => {
  try {
    categoriesLoading.value = true
    categoriesError.value = null
    
    const response = await categoriesApi.getCategories()
    categories.value = response.data || []
    
  } catch (err) {
    console.error('Erreur lors du chargement des catégories:', err)
    categoriesError.value = 'Impossible de charger les catégories. Veuillez réessayer.'
  } finally {
    categoriesLoading.value = false
  }
}

const fetchMakes = async () => {
  try {
    brandsLoading.value = true
    brandsError.value = null
    
    const response = await brandsApi.getBrands()
    brands.value = response.data || []
  } catch (err) {
    console.error('Erreur lors du chargement des catégories:', err)
    brandsError.value = 'Impossible de charger les catégories. Veuillez réessayer.'
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

const updateDetailedDescription = () => {
  productData.description = wysiwygEditor.value.innerHTML
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

const updateAvailableSizes = () => {
  productData.sizes = []
}

const updateModelid = () => {
  productData.vehicle_model_id = ""
  productData.fuel_type = ""
}
const updateFuelType = () => {
  productData.fuel_type = availableFuelType.value
}

const getSizePlaceholder = (sizeType) => {
  const placeholders = {
    'clothing': 'Ex: M, L, XL...',
    'shoes': 'Ex: 42, 43, 44...',
    'storage': 'Ex: 128GB, 256GB...',
    'screen': 'Ex: 15", 17"...',
    'ring': 'Ex: 54, 56, 58...',
    'watch': 'Ex: 42mm, 44mm...',
    'custom': 'Ex: Taille personnalisée...'
  }
  return placeholders[sizeType] || 'Ex: Taille personnalisée...'
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

const toggleSize = (size) => {
  const index = productData.sizes.indexOf(size)
  if (index > -1) {
    productData.sizes.splice(index, 1)
  } else {
    productData.sizes.push(size)
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

const addCustomSize = () => {
  if (customSize.value && !currentAvailableSizes.value.includes(customSize.value)) {
    const sizeType = sizeTypes.value.find(type => type.value === productData.sizeType)
    if (sizeType) {
      sizeType.sizes.push(customSize.value)
      productData.sizes.push(customSize.value)
    }
    customSize.value = ''
  }
}

const handleImageUpload = (event) => {
  const files = Array.from(event.target.files)
  const remainingSlots = 8 - productData.images.length
  
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
  productData.vehicle_condition= productData.vehicle_condition.charAt(0).toUpperCase() + productData.vehicle_condition.slice(1).toLowerCase();
  
  productData.name = [
                    productData.vehicle_condition,
                    productData.vehicle_year,
                    getBrandName( productData.vehicle_brand_id),
                    getModelName(  productData.vehicle_model_id),
                    getCategoryName(productData.category_id),
                    productData.drive_type
                  ].filter(Boolean).join(' ');

  return productData.name
}

const handleNextStep = async () => {
  error.value = null
  
  if (!canProceedToNextStep.value) {
    if (currentStep.value === 3) {
      error.value = 'Veuillez remplir correctement le prix unitaire et le stock avant de continuer'
    } else {
      error.value = 'Veuillez remplir tous les champs obligatoires'
    }
      
    
    setTimeout(() => {
      error.value = null
    }, 3000)
    
    return
  }
  
  if (currentStep.value < steps.length - 1) {
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
    console.error(`Erreur lors du téléchargement de l'image ${index}:`, error)
    productData.images[index].uploading = false
    return false
  }
}

//  Updated video upload to use Cloudinary
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
    console.error('Erreur lors du téléchargement de la vidéo:', error)
    productData.video.uploading = false
    return false
  }
}

const uploadAllMedia = async () => {
  loadingMessage.value = 'Téléchargement des médias en cours...'
  
  const imagePromises = []
  for (let i = 0; i < productData.images.length; i++) {
    imagePromises.push(uploadImageToCloudinary(productData.images[i], i))
  }
  
  await Promise.all(imagePromises)
  
  if (productData.video && productData.video.file) {
    await uploadVideoToCloudinary(productData.video)
  }
  
  loadingMessage.value = 'Médias téléchargés avec succès!'
}

//  Updated prepareDataForSubmission to include new fields
const prepareDataForSubmission = () => {
  const formData = {
    name: productData.name,
    description : productData.description,
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
    tags: productData.tags,
    is_active: productData.is_active,
    colors: productData.colors,
    sizes: productData.sizes,
    size_type: productData.sizeType,
    vehicle_condition: productData.vehicle_condition,
    vehicle_make:getBrandName( productData.vehicle_brand_id),
    vehicle_model:getModelName( productData.vehicle_model_id) ,
    drive_type: productData.drive_type,
    vehicle_year: productData.vehicle_year,
    fuel_type: productData.fuel_type,
    transmission_type: productData.transmission_type,
    engine_brand: productData.engine_brand,
    engine_numbers: productData.engine_numbers.filter(num => num.trim() !== ''),
    vin: productData.vin.filter(num => num.trim() !== ''),
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
    tyre_size: productData.type_size,
    curb_weight: productData.curb_weight,
    fuel_tank_capacity: productData.fuel_tank_capacity,
    dimensions: productData.dimensions_length || productData.dimensions_width || productData.dimensions_height
    ? `${productData.dimensions_length || 0}x${productData.dimensions_width || 0}x${productData.dimensions_height || 0}`
    : null,
  
  
  }
  
  // if (productData.hasWholesalePrice) {
  //   formData.wholesale_price = parseFloat(productData.wholesale_price)
  //   formData.wholesale_min_qty = parseInt(productData.wholesale_min_qty)
  // }
  
  formData.images = productData.imageUrls.map((url, index) => ({
    url: url,
    alt_text: `${productData.name} - Image ${index + 1}`,
    is_primary: index === 0 ? 1 : 0,
    sort_order: index
  }))
  
  if (productData.videoUrl) {
    formData.video = productData.videoUrl
  }
  console.log('Données préparées pour la soumission:', formData)
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
    
    loadingMessage.value = 'Finalisation...'
    emit('save', formData)
    
  } catch (err) {
    console.error('Erreur lors de la préparation:', err)
    error.value = 'Erreur lors du téléchargement des médias'
  } finally {
    isLoading.value = false
  }
}

const handleBackdropClick = (event) => {
  if (event.target === event.currentTarget) {
    handleCloseModal()
  }
}

onMounted(() => {
  fetchCategories(),
  fetchMakes()
})

// Données du nouveau produit:
// {
//   "name": "Refurbished 2025 Mercedes Actros Camions 8x8",
//   "description": "Description détaillées du produit.",
//   "hasDetailedDescription": false,
//   "description_plus": "Autres descriptions du produits",
//   "category_id": 13,
//   "subcategory_id": 51,
//   "subsubcategory_id": "",
//   "subsubsubcategory_id": "",
//   "tags": "Tendance, Promotionnel",
//   "unit_price": 10000,
//   "stock": 4,
//   "unit_type": "weight_kg",
//   "hasWholesalePrice": true,
//   "wholesale_price": 9000,
//   "wholesale_min_qty": 2,
//   "colors_names": [
//     "Blanc",
//     "Orange",
//     "Violet",
//     "Bleu"
//   ],
//   "colors": [
//     "#FFFFFF",
//     "#FFA500",
//     "#800080",
//     "#0000FF"
//   ],
//   "sizes": [
//     "6.1\"",
//     "32\""
//   ],
//   "sizeType": "screen",
//   "images": [
//     {
//       "file": {},
//       "preview": "data:image/webp;base64,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",
//       "uploading": false,
//       "uploadProgress": 0,
//       "uploaded": false,
//       "url": null
//     },
//     {
//       "file": {},
//       "preview": "data:image/jpeg;base64,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",
//       "uploading": false,
//       "uploadProgress": 0,
//       "uploaded": false,
//       "url": null
//     },
//     {
//       "file": {},
//       "preview": "data:image/jpeg;base64,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",
//       "uploading": false,
//       "uploadProgress": 0,
//       "uploaded": false,
//       "url": null
//     },
//     {
//       "file": {},
//       "preview": "data:image/jpeg;base64,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",
//       "uploading": false,
//       "uploadProgress": 0,
//       "uploaded": false,
//       "url": null
//     }
//   ],
//   "imageUrls": [],
//   "video": null,
//   "videoUrl": "",
//   "is_active": true,
//   "vehicle_condition": "Refurbished",
//   "vehicle_make": "Mercedes",
//   "vehicle_model": "Actros",
//   "drive_type": "8x8",
//   "vehicle_year": 2025,
//   "fuel_type": "Hybrid",
//   "transmission_type": "Automatic",
//   "engine_brand": "Man",
//   "engine_numbers": [
//     "5353636SS",
//     "T6Y666YHF",
//     "3RG3G4G4",
//     "34TH5H46"
//   ],
//   "vin": [
//     "FEJ455EE",
//     "EFEHI3P4",
//     "FB2IFHINZ",
//     "ZHFP333F"
//   ],
//   "power": 200,
//   "engine_emissions": 3,
//   "vehicle_mileage": 20,
//   "production_date": "2025-10-10",
//   "country_of_origin": "Allemagne",
//   "wheelbase": 300,
//   "gvw": 12000,
//   "payload_capacity": 10000,
//   "cabin_type": "Longue",
//   "suspension_type": "Mécanique",
//   "brake_system": "ABS",
//   "type_size": "315/80R14",
//   "dimensions_length": 5,
//   "dimensions_width": 2,
//   "dimensions_height": 3,
//   "curb_weight": 10000,
//   "fuel_tank_capacity": 200
// }

//Produit créé avec succès:
// {
//   "id": 101,
//   "name": "Refurbished 2025 Mercedes Actros Camions 8x8",
//   "slug": "refurbished-2025-mercedes-actros-camions-8x8",
//   "sku": "ADJ-68EA6084142E1",
//   "description": "",
//   "category_id": 13,
//   "subcategory_id": 51,
//   "unit_price": "10000.00",
//   "wholesale_price": "9000.00",
//   "wholesale_min_qty": 2,
//   "stock": 4,
//   "status": "Actif",
//   "icon": "",
//   "tags": "Tendance, Promotionnel",
//   "description_plus": "",
//   "video": "",
//   "sales_count": 0,
//   "views_count": 0,
//   "is_active": 1,
//   "created_at": "2025-10-11 15:49:56",
//   "updated_at": "2025-10-11 15:49:56",
//   "unit_type": "weight_kg",
//   "boutique_id": 14,
//   "created_by": 16,
//   "updated_by": null,
//   "sub_subcategory_id": null,
//   "free_shipping": 0,
//   "tp": "1",
//   "qtp": "1",
//   "sub_sub_subcategory_id": 0,
//   "boost": "",
//   "debut_boost": "2025-10-11 15:49:56",
//   "fin_boost": "0000-00-00 00:00:00",
//   "is_boosted": 0,
//   "boost_expires_at": null,
//   "vehicle_condition": "refurbished",
//   "vehicle_make": "Mercedes",
//   "vehicle_model": "Actros",
//   "drive_type": "8X8",
//   "vehicle_year": 2025,
//   "fuel_type": "hybrid",
//   "transmission_type": "automatic",
//   "engine_brand": "man",
//   "vehicle_mileage": 20,
//   "brake_system": "ABS",
//   "cabin_type": "Longue",
//   "country_of_origin": "Allemagne",
//   "curb_weight": 10000,
//   "dimensions": "5x2x3",
//   "fuel_tank_capacity": 200,
//   "gvw": 12000,
//   "other_description": null,
//   "payload_capacity": 10000,
//   "production_date": "2025-10-10",
//   "size_type": "screen",
//   "suspension_type": "Mécanique",
//   "tyre_size": "315/80R14",
//   "wheelbase": 300,
//   "category_name": "Camions",
//   "subcategory_name": "Dump Truck",
//   "boutique_name": "sackoba",
//   "created_by_name": "sacko allou",
//   "created_by_email": "badrasacko11@gmail.com"
// }

// Produits chargés:
// {
//   "id": 101,
//   "name": "Refurbished 2025 Mercedes Actros Camions 8x8",
//   "description": "",
//   "description_plus": "",
//   "sku": "ADJ-68EA6084142E1",
//   "unit_price": 10000,
//   "unit_type": "weight_kg",
//   "wholesale_price": 9000,
//   "wholesale_min_qty": 2,
//   "stock": 4,
//   "status": "Actif",
//   "slug": "refurbished-2025-mercedes-actros-camions-8x8",
//   "is_active": true,
//   "category_id": 13,
//   "subcategory_id": 51,
//   "sub_subcategory_id": null,
//   "sub_sub_subcategory_id": 0,
//   "boutique_id": 14,
//   "created_by": 16,
//   "created_at": "2025-10-11 15:49:56",
//   "updated_at": "2025-10-11 15:49:56",
//   "vehicle_condition": "refurbished",
//   "vehicle_make": "Mercedes",
//   "vehicle_model": "Actros",
//   "drive_type": "8X8",
//   "vehicle_year": 2025,
//   "fuel_type": "hybrid",
//   "transmission_type": "automatic",
//   "engine_brand": "man",
//   "vehicle_mileage": 20,
//   "category_name": "Camions",
//   "subcategory_name": "Dump Truck",
//   "boutique_name": "sackoba",
//   "created_by_name": "sacko allou",
//   "created_by_email": "badrasacko11@gmail.com",
//   "boost_type": null,
//   "boost_status": null,
//   "boost_expires_at": null,
//   "colors": [
//     "#0000FF",
//     "#800080",
//     "#FFA500",
//     "#FFFFFF"
//   ],
//   "color_names": [
//     "Blanc",
//     "Bleu",
//     "Orange",
//     "Violet"
//   ],
//   "sizes": [
//     "32\"",
//     "6.1\""
//   ],
//   "engine_numbers": [
//     "34TH5H46",
//     "3RG3G4G4",
//     "5353636SS",
//     "T6Y666YHF"
//   ],
//   "primary_image": "https://res.cloudinary.com/daaavha4z/image/upload/v1760190594/product_1760190591865_0_image.webp.webp",
//   "image_count": 4,
//   "images": [
//     "https://res.cloudinary.com/daaavha4z/image/upload/v1760190594/product_1760190591865_0_image.webp.webp",
//     "https://res.cloudinary.com/daaavha4z/image/upload/v1760190593/product_1760190591877_1_images.jpeg.jpg",
//     "https://res.cloudinary.com/daaavha4z/image/upload/v1760190594/product_1760190591882_2_images1.jpeg.jpg",
//     "https://res.cloudinary.com/daaavha4z/image/upload/v1760190593/product_1760190591891_3_images2.jpeg.jpg"
//   ],
//   "is_boosted": false,
//   "has_vehicle_specs": true
// }

// Produit chargé sur l'édition:
// {
//   "id": 101,
//   "name": "Refurbished 2025 Mercedes Actros Camions 8x8",
//   "description": "",
//   "description_plus": "",
//   "sku": "ADJ-68EA6084142E1",
//   "unit_price": 10000,
//   "unit_type": "weight_kg",
//   "wholesale_price": 9000,
//   "wholesale_min_qty": 2,
//   "stock": 4,
//   "status": "Actif",
//   "slug": "refurbished-2025-mercedes-actros-camions-8x8",
//   "is_active": true,
//   "category_id": 13,
//   "subcategory_id": 51,
//   "sub_subcategory_id": null,
//   "sub_sub_subcategory_id": 0,
//   "boutique_id": 14,
//   "created_by": 16,
//   "created_at": "2025-10-11 15:49:56",
//   "updated_at": "2025-10-11 15:49:56",
//   "vehicle_condition": "refurbished",
//   "vehicle_make": "Mercedes",
//   "vehicle_model": "Actros",
//   "drive_type": "8X8",
//   "vehicle_year": 2025,
//   "fuel_type": "hybrid",
//   "transmission_type": "automatic",
//   "engine_brand": "man",
//   "vehicle_mileage": 20,
//   "category_name": "Camions",
//   "subcategory_name": "Dump Truck",
//   "boutique_name": "sackoba",
//   "created_by_name": "sacko allou",
//   "created_by_email": "badrasacko11@gmail.com",
//   "boost_type": null,
//   "boost_status": null,
//   "boost_expires_at": null,
//   "colors": [
//     "#0000FF",
//     "#800080",
//     "#FFA500",
//     "#FFFFFF"
//   ],
//   "color_names": [
//     "Blanc",
//     "Bleu",
//     "Orange",
//     "Violet"
//   ],
//   "sizes": [
//     "32\"",
//     "6.1\""
//   ],
//   "engine_numbers": [
//     "34TH5H46",
//     "3RG3G4G4",
//     "5353636SS",
//     "T6Y666YHF"
//   ],
//   "primary_image": "https://res.cloudinary.com/daaavha4z/image/upload/v1760190594/product_1760190591865_0_image.webp.webp",
//   "image_count": 4,
//   "images": [
//     "https://res.cloudinary.com/daaavha4z/image/upload/v1760190594/product_1760190591865_0_image.webp.webp",
//     "https://res.cloudinary.com/daaavha4z/image/upload/v1760190593/product_1760190591877_1_images.jpeg.jpg",
//     "https://res.cloudinary.com/daaavha4z/image/upload/v1760190594/product_1760190591882_2_images1.jpeg.jpg",
//     "https://res.cloudinary.com/daaavha4z/image/upload/v1760190593/product_1760190591891_3_images2.jpeg.jpg"
//   ],
//   "is_boosted": false,
//   "has_vehicle_specs": true
// }
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
