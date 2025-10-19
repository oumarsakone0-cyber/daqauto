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
                    <option v-for="brand in brands" :key="brand.id" :value="brand.id">
                      {{ brand.name }}
                    </option>
                  </select>
                </div>
                
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
                    class="text-sm sm:text-base input-style  cursor-not-allowed overflow-ellipsis"
                    placeholder="Le type de carburant sera défini automatiquement"
                  >
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
                  <label for="production_date" class="block text-sm font-medium text-gray-700 mb-2">
                    Date de production <span class="error-color">*</span>
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
                    Pays d'origine <span class="error-color">*</span>
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
                    Empattement <span class="error-color">*</span>
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
                    GVW - Poids total en charge (kg) <span class="error-color">*</span>
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
                    Capacité de charge utile (kg) <span class="error-color">*</span>
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
                    Type de cabine <span class="error-color">*</span>
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
                    Poids à vide (Tonnes) <span class="error-color">*</span>
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
                    Capacité du réservoir (L) <span class="error-color">*</span>
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
                    Dimensions (L x l x H) (m) <span class="error-color">*</span>
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

                <div class="sm:col-span-2">
                  <label for="wysiwygEditor" class="block text-sm font-medium text-gray-700 mb-2">
                   Autres spécifications (WYSIWYG)
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
                   Boîte de Vitesse
                  </label>
                  <select
                    id="transmission_type"
                    v-model="productData.transmission_type"
                    class="text-sm sm:text-base input-style"
                  >
                    <option value="">Selectionner le type de transmission</option>
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
                    Modèle du véhicule ( Trim )
                  </label>
                  <div class="space-y-2">
                    <div v-for="(trim, index) in productData.trim_number" :key="index" class="flex gap-2">
                      <div class="font-semibold text-gray-700 items-center justify-center">{{index + 1}}</div>
                      <input
                        v-model="productData.trim_number[index]"
                        type="text"
                        class="flex-1 text-sm sm:text-base input-style"
                        :placeholder="`Numéro trim ${index + 1}`"
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
                      + Ajouter un numéro Trim
                    </button>
                  </div>
                </div>
                <div >
                  <label for="vehicle_number" class="block text-sm font-medium text-gray-700 mb-2">
                    Numéro de moteur
                  </label>
                  <input
                    id="vehicle_number"
                    v-model="productData.engine_number"
                    type="text"
                    class="text-sm sm:text-base input-style"
                    placeholder="Ex: FHEGEJGE776JH8"
                  >
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
                  <label for="wysiwygEditor2" class="block text-sm font-medium text-gray-700 mb-2">
                   Autres Description (WYSIWYG)
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
                        ref="wysiwygEditor2"
                        contenteditable="true"
                        @input="updateOtherDescription"
                        class="min-h-[200px] p-4 focus:outline-none text-black rounded-b-lg"
                        style="white-space: pre-wrap;"
                        placeholder="Décrivez votre produit en détail..."
                      ></div>
                    </div>
                  </div>
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

              <div class="grid grid-cols-1 sm:grid-cols-2 gap-1 sm:gap-6">
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
                  <label for="available" class="block text-sm font-medium text-gray-700 mb-2">
                    Disponibilité du produit
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
                    <p><span class="font-medium text-gray-700">Disponibilité du produit:</span> <span class="text-gray-900">{{ productData.disponibility }}</span></p>
                    <p><span class="font-medium text-gray-700">Tags:</span> <span class="text-gray-900">{{ productData.tags }}</span></p>
                    <p><span class="font-medium text-gray-700">Unité:</span> <span class="text-gray-900">{{ productData.unit_type || 'Non défini' }}</span></p>
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
                      <div class="spec-value">  {{ productData.drive_type || 'N/A'}}</div>
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
                      <div class="spec-value">{{ productData.transmission_type || 'N/A' }}</div>
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
                    <!-- verifié -->
                    <div class="spec-row">
                      <div class="spec-name">Numéro du moteur</div>
                      <div class="spec-value">{{ productData.engine_number || 'N/A' }}</div>
                    </div>
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
                      <div class="spec-name">Numéros Trim</div>
                      <div class="spec-value">
                        <ul v-if="productData.trim_number && productData.trim_number.length">
                          <li v-for="trim in productData.trim_number" :key="trim.id">
                            {{ trim }}
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
const isLoading = ref(false)
const loadingMessage = ref('')
const error = ref(null)
const wysiwygEditor = ref(null)
const wysiwygEditor2 = ref(null)

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
  { title: 'Couleurs' },
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
  trim_number: [],
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
  type_size: '',
  dimensions_length: null,
  dimensions_width: null,
  dimensions_height: null,
  curb_weight: null,
  fuel_tank_capacity: null
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
      return !!(productData.category_id && productData.subcategory_id)
    case 1:
      return !!(productData.vehicle_condition && productData.vehicle_brand_id 
      // && productData.vehicle_model_id && productData.vehicle_year && productData.drive_type 
      // && productData.fuel_type && productData.country_of_origin 
      // && productData.dimensions_height && productData.dimensions_width 
      // && productData.dimensions_length && productData.fuel_tank_capacity 
      // && productData.curb_weight && productData.cabin_type && productData.payload_capacity
      // && productData.gvw && productData.production_date && productData.wheelbase
    )
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
      
      return hasValidPrice && hasValidStock && !!productData.disponibility
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

const availability = ref([
  { value: 'available', label: 'Disponible' },
  { value: 'unavailable', label: 'Indisponible' },
  { value: 'on_order', label: 'Sur Commande' },
])



//  Added engine number management functions
const addEngineNumber = () => {
  productData.trim_number.push('')
}

const removeEngineNumber = (index) => {
  productData.trim_number.splice(index, 1)
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
    disponibility:productData.disponibility,
    tags: productData.tags,
    is_active: productData.is_active,
    colors: productData.colors,
    vehicle_condition: productData.vehicle_condition,
    vehicle_make:getBrandName( productData.vehicle_brand_id),
    vehicle_model:getModelName( productData.vehicle_model_id) ,
    drive_type: productData.drive_type,
    vehicle_year: productData.vehicle_year,
    fuel_type: productData.fuel_type,
    transmission_type: productData.transmission_type,
    engine_brand: productData.engine_brand,
    trim_number: productData.trim_number.filter(num => num.trim() !== ''),
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
    tyre_size: productData.type_size,
    curb_weight: productData.curb_weight,
    fuel_tank_capacity: productData.fuel_tank_capacity,
    dimensions: productData.dimensions_length || productData.dimensions_width || productData.dimensions_height
    ? `${productData.dimensions_length || 0}x${productData.dimensions_width || 0}x${productData.dimensions_height || 0}`
    : null,
  
  
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
