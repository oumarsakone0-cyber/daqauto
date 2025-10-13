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
              <h2 class="text-xl sm:text-2xl font-bold text-gray-900">Modifier le produit</h2>
              <p class="text-sm text-gray-600 hidden sm:block">Modifiez les informations de votre produit</p>
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
                class="ml-2 px-2 py-1 error-background-color text-white rounded text-xs hover:bg-red-700 transition-colors"
              >
                Réessayer
              </button>
            </div>
          </div>
  
          <div v-if="isLoading" class="mb-3 p-3 bg-blue-50 border border-blue-200 primary-color rounded-lg flex items-center space-x-2">
            <div class="animate-spin w-4 h-4 border-2 border-orange-500 border-t-transparent rounded-full flex-shrink-0"></div>
            <span class="text-sm">{{ loadingMessage }}</span>
          </div>
  
          <div v-if="categoriesLoading" class="mb-3 p-3 bg-blue-50 border border-blue-200 primary-color rounded-lg flex items-center space-x-2">
            <div class="animate-spin w-4 h-4 border-2 border-orange-500 border-t-transparent rounded-full flex-shrink-0"></div>
            <span class="text-sm">Chargement des catégories...</span>
          </div>
        </div>
      </div>
  
      <div class="overflow-y-auto h-[calc(100vh-200px)] sm:h-auto sm:max-h-[calc(60vh)] px-4 sm:px-6 py-6 relative z-5">
        <form @submit.prevent="handleSubmit" class="space-y-6 sm:space-y-8" v-if="editData">
           
          <div class="bg-white/80 backdrop-blur-sm rounded-xl p-4 sm:p-6 border border-gray-100 shadow-sm">
            <div class="flex items-center space-x-3 mb-4 sm:mb-6">
              <div class="w-8 h-8 bg-orange rounded-lg flex items-center justify-center">
                <InfoIcon class="w-4 h-4 text-white" />
              </div>
              <h3 class="text-lg sm:text-xl font-semibold text-gray-900">Informations de base</h3>
            </div>
  
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 sm:gap-6">
             
              <div class="sm:col-span-2">
                <label for="name" class="block text-sm font-medium text-gray-700 mb-2">
                  Nom du produit <span class="error-color">*</span>
                </label>
                <input
                  id="name"
                  v-model="editData.name"
                  type="text"
                  required
                  class="input-style"
                  placeholder="Ex: T-shirt Premium Coton"
                >
              </div>
  
              <!-- <div class="sm:col-span-2">
                <label for="description" class="block text-sm font-medium text-gray-700 mb-2">
                  Description courte
                </label>
                <textarea
                  id="description"
                  v-model="editData.description_plus"
                  rows="3"
                  class="input-style"
                  placeholder="Décrivez votre produit..."
                ></textarea>
              </div> -->

              
   
              <div>
                <label for="category" class="block text-sm font-medium text-gray-700 mb-2">
                  Catégorie <span class="error-color">*</span>
                </label>
                <select
                  id="category"
                  v-model="editData.category_id"
                  @change="updateSubcategories"
                  required
                  :disabled="categoriesLoading"
                  class="text-sm sm:text-base input-style"
                >
                  <option value="">{{ categoriesLoading ? 'Chargement...' : 'Sélectionner une catégorie' }}</option>
                  <option v-for="category in categories" :key="category.id" :value="category.id">
                    {{ category.name, console.log("editData",editData) }}
                  </option>
                </select>
              </div>
   
              <div>
                <label for="subcategory" class="block text-sm font-medium text-gray-700 mb-2">
                  Sous-catégorie <span class="error-color">*</span>
                </label>
                <select
                  id="subcategory"
                  v-model="editData.subcategory_id"
                  @change="updateSubSubcategories"
                  required
                  :disabled="!editData.category_id || categoriesLoading"
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
                  v-model="editData.subsubcategory_id"
                  @change="updateSubSubSubcategories"
                  :disabled="!editData.subcategory_id || categoriesLoading"
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
                  v-model="editData.subsubsubcategory_id"
                  :disabled="!editData.subsubcategory_id || categoriesLoading"
                  class="text-sm sm:text-base input-style"
                >
                  <option value="">Sélectionner une sous-sous-sous-catégorie (optionnel)</option>
                  <option v-for="subsubsubcategory in availableSubSubSubcategories" :key="subsubsubcategory.id" :value="subsubsubcategory.id">
                    {{ subsubsubcategory.name  }}
                  </option>
                </select>
              </div>
              <div class="sm:col-span-2">
                <div class="flex items-center mb-3">
                 
                  <label for="detailed-description-toggle" class="ml-3 text-sm font-medium text-gray-700 flex items-center">
                    <EditIcon class="w-4 h-4 primary-color mr-1" />
                    Activer la description détaillée (WYSIWYG)
                  </label>
                </div>
                
                <div >
                  <div class="border border-gray-300 rounded-lg focus-within:ring-1 focus-within:ring-orange-400 focus-within:border-orange-400 transition-all duration-200">
                      
                    <div class="flex items-center gap-1 p-2 border-b border-gray-200 bg-gray-50 rounded-t-lg flex-wrap">
                      <button type="button" @click="formatText('bold')" class="p-2 hover:bg-gray-200 rounded text-sm font-bold" title="Gras" style="background-color: lightgray; color: black;">B</button>
                      <button type="button" @click="formatText('italic')" class="p-2 hover:bg-gray-200 rounded text-sm italic " title="Italique" style="background-color: lightgray; color: black;">I</button>
                      <button type="button" @click="formatText('underline')" class="p-2 hover:bg-gray-200 rounded text-sm underline " title="Souligné" style="background-color: lightgray; color: black;">U</button>
                      <div class="w-px h-6 bg-gray-300 mx-1"></div>
                      <button type="button" @click="formatText('insertUnorderedList')" class="p-2 hover:bg-gray-200 rounded text-sm " title="Liste à puces" style="background-color: lightgray; color: black;">•</button>
                      <button type="button" @click="formatText('insertOrderedList')" class="p-2 hover:bg-gray-200 rounded text-sm " title="Liste numérotée" style="background-color: lightgray; color: black;">1.</button>
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
                  v-model="editData.tags"
                  type="text"
                  class="text-sm sm:text-base input-style"
                  placeholder="Ex: nouveau, tendance, promotion (séparés par des virgules)"
                >
              </div>
            </div>
          </div>

          <div class="bg-white/80 backdrop-blur-sm rounded-xl p-4 sm:p-6 border border-gray-100 shadow-sm">
            <div class="flex items-center space-x-3 mb-4 sm:mb-6">
              <div class="w-8 h-8  rounded-lg flex items-center justify-center bg-orange">
                <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                </svg>
              </div>
              <h3 class="text-lg sm:text-xl font-semibold text-gray-900">Spécifications Véhicule</h3>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 sm:gap-6">
               
              <div>
                <label for="vehicle_condition" class="block text-sm font-medium text-gray-700 mb-2">
                  État du véhicule 
                </label>
                <select
                  id="vehicle_condition"
                  v-model="editData.vehicle_condition"
                  class="text-sm sm:text-base input-style"
                >
                  <option value="">Selectionner l'état du véhicule</option>
                  <option value="new">Neuf</option>
                  <option value="used">Occasion</option>
                  <option value="refurbished">Reconditionné</option>
                </select>
              </div>

              <div>
                <label for="vehicle_make" class="block text-sm font-medium text-gray-700 mb-2">
                  Marque
                </label>
                <input
                  id="vehicle_make"
                  v-model="editData.vehicle_make"
                  type="text"
                  class="text-sm sm:text-base input-style"
                  placeholder="Ex: Mercedes, Volvo, Scania"
                >
              </div>

              <div>
                <label for="vehicle_model" class="block text-sm font-medium text-gray-700 mb-2">
                  Modèle
                </label>
                <input
                  id="vehicle_model"
                  v-model="editData.vehicle_model"
                  type="text"
                  class="text-sm sm:text-base input-style"
                  placeholder="Ex: Actros, FH, R-Series"
                >
              </div>

              <div>
                <label for="drive_type" class="block text-sm font-medium text-gray-700 mb-2">
                  Type de transmission
                </label>
                <select
                  id="drive_type"
                  v-model="editData.drive_type"
                  class="text-sm sm:text-base input-style"
                >
                  <option value="">Tous les types</option>
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
                <label for="vehicle_year" class="block text-sm font-medium text-gray-700 mb-2">
                  Année
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
                <label for="fuel_type" class="block text-sm font-medium text-gray-700 mb-2">
                  Type de carburant
                </label>
                <select
                  id="fuel_type"
                  v-model="editData.fuel_type"
                  class="text-sm sm:text-base input-style"
                >
                  <option value="">Tous les carburants</option>
                  <option value="diesel">Diesel</option>
                  <option value="electric">Électrique</option>
                  <option value="hybrid">Hybride</option>
                  <option value="cng">CNG</option>
                  <option value="lng">LNG</option>
                  <option value="hydrogen">Hydrogène</option>
                  <option value="unknown">Inconnu</option>
                </select>
              </div>

              <div>
                <label for="production_date" class="block text-sm font-medium text-gray-700 mb-2">
                  Date de production
                </label>
                <input
                  id="production_date"
                  v-model="editData.production_date"
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
                  v-model="editData.country_of_origin"
                  type="text"
                  class="text-sm sm:text-base input-style"
                  placeholder="Ex: Chine, Allemagne, Suède"
                >
              </div>

              <div>
                <label for="wheelbase" class="block text-sm font-medium text-gray-700 mb-2">
                  Empattement (mm)
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
                <label for="gvw" class="block text-sm font-medium text-gray-700 mb-2">
                  PTAC (kg)
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
                <label for="payload_capacity" class="block text-sm font-medium text-gray-700 mb-2">
                  Capacité de charge utile (kg)
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
                  Type de cabine
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
                <label for="suspension_type" class="block text-sm font-medium text-gray-700 mb-2">
                  Type de suspension
                </label>
                <input
                  id="suspension_type"
                  v-model="editData.suspension_type"
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
                  v-model="editData.brake_system"
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
                  v-model="editData.type_size"
                  type="text"
                  class="text-sm sm:text-base input-style"
                  placeholder="Ex: 295/80R22.5"
                >
              </div>

              <div class="sm:col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-2">
                  Dimensions (L x l x H) en mm
                </label>
                <div class="grid grid-cols-3 gap-3">
                  <input
                    v-model="editData.dimension_length"
                    type="number"
                    min="0"
                    class="text-sm sm:text-base input-style"
                    placeholder="Longueur"
                  >
                  <input
                    v-model="editData.dimension_width"
                    type="number"
                    min="0"
                    class="text-sm sm:text-base input-style"
                    placeholder="Largeur"
                  >
                  <input
                    v-model="editData.dimension_height"
                    type="number"
                    min="0"
                    class="text-sm sm:text-base input-style"
                    placeholder="Hauteur"
                  >
                </div>
              </div>

              <div>
                <label for="curb_weight" class="block text-sm font-medium text-gray-700 mb-2">
                  Poids à vide (kg)
                </label>
                <input
                  id="curb_weight"
                  v-model="editData.curb_weight"
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
                  v-model="editData.fuel_tank_capacity"
                  type="number"
                  min="0"
                  class="text-sm sm:text-base input-style"
                  placeholder="Ex: 400"
                >
              </div>
            </div>
          </div>

          <div class="bg-white/80 backdrop-blur-sm rounded-xl p-4 sm:p-6 border border-gray-100 shadow-sm">
            <div class="flex items-center space-x-3 mb-4 sm:mb-6">
              <div class="w-8 h-8 bg-orange rounded-lg flex items-center justify-center">
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
                  Transmission
                </label>
                <select
                  id="transmission_type"
                  v-model="editData.transmission_type"
                  class="text-sm sm:text-base input-style"
                >
                  <option value="">Toutes les transmissions</option>
                  <option value="automatic">Automatique</option>
                  <option value="manual">Manuelle</option>
                </select>
              </div>

              <div>
                <label for="engine_brand" class="block text-sm font-medium text-gray-700 mb-2">
                  Marque du moteur
                </label>
                <select
                  id="engine_brand"
                  v-model="editData.engine_brand"
                  class="text-sm sm:text-base input-style"
                >
                  <option value="">Sélectionnez une marque</option>
                  <option value="weichai">Weichai</option>
                  <option value="yuchai">Yuchai</option>
                  <option value="sinotruck">Sinotruck</option>
                  <option value="man">MAN</option>
                </select>
              </div>

              <div class="sm:col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-2">
                  Numéros de moteur
                </label>
                <div class="space-y-2">
                  <div 
                    v-for="(engineNumber, index) in editData.engine_numbers" 
                    :key="index"
                    class="flex gap-2"
                  >
                    <input
                      v-model="editData.engine_numbers[index]"
                      type="text"
                      class="flex-1 text-sm sm:text-base input-style"
                      placeholder="Ex: ENG123456789"
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
                    class="w-full submit-btn"
                  >
                    + Ajouter un numéro de moteur
                  </button>
                </div>
              </div>

              <div class="sm:col-span-2">
                <label for="vehicle_mileage" class="block text-sm font-medium text-gray-700 mb-2">
                  Kilométrage (km)
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
                <p class="text-xs text-gray-500 mt-1">Entre 0 et 200,000 km</p>
              </div>
              <div class="sm:col-span-2">
                <label for="other_description" class="block text-sm font-medium text-gray-700 mb-2">
                  Autres descriptions
                </label>
                <textarea
                  id="other_description"
                  v-model="editData.description_plus"
                  rows="4"
                  class="input-style"
                  placeholder="Ajoutez d'autres informations pertinentes sur le véhicule..."
                ></textarea>
              </div>
            </div>
          </div>
  
          <div class="bg-white/80 backdrop-blur-sm rounded-xl p-4 sm:p-6 border border-gray-100 shadow-sm">
            <div class="flex items-center space-x-3 mb-4 sm:mb-6">
              <div class="w-8 h-8 bg-orange rounded-lg flex items-center justify-center">
                <DollarSignIcon class="w-4 h-4 text-white" />
              </div>
              <h3 class="text-lg sm:text-xl font-semibold text-gray-900">Prix et Stock</h3>
            </div>
  
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 sm:gap-6">
              
              <div>
                <label for="unit_price" class="block text-sm font-medium text-gray-700 mb-2">
                  Prix unitaire ($) <span class="error-color">*</span>
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
                <label for="stock" class="block text-sm font-medium text-gray-700 mb-2">
                  Stock <span class="error-color">*</span>
                </label>
                <input
                  id="stock"
                  v-model.number="editData.stock"
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
                  Type d'unité
                </label>
                <select
                  id="unit_type"
                  v-model="editData.unit_type"
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
                  v-model="hasWholesalePrice"
                  id="wholesale-price"
                  type="checkbox"
                  class="checkbox-style"
                >
                <label for="wholesale-price" class="ml-3 text-sm font-medium text-gray-700 flex items-center">
                  <ZapIcon class="w-4 h-4 primary-color mr-1" />
                  Activer le prix de gros
                </label>
              </div>
              
              <div v-if="hasWholesalePrice" class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                  <label for="wholesale_price" class="block text-sm font-medium text-gray-700 mb-2">
                    Prix de gros (FCFA)
                  </label>
                  <input
                    id="wholesale_price"
                    v-model.number="editData.wholesale_price"
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
                    v-model.number="editData.wholesale_min_qty"
                    type="number"
                    min="1"
                    class="text-sm sm:text-base input-style"
                    placeholder="10"
                  >
                </div>
              </div>
            </div>
          </div>
  
          <div class="bg-white/80 backdrop-blur-sm rounded-xl p-4 sm:p-6 border border-gray-100 shadow-sm">
            <div class="flex items-center space-x-3 mb-4 sm:mb-6">
              <div class="w-8 h-8 bg-orange rounded-lg flex items-center justify-center">
                <PaletteIcon class="w-4 h-4 text-white" />
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
              <label class="block text-sm font-medium text-gray-700 mb-2">Ajouter une couleur personnalisée</label>
              <div class="flex gap-2">
                <input 
                  v-model="customColor.name"
                  type="text" 
                  placeholder="Nom de la couleur"
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
                  class="px-4 py-2  text-white rounded-lg   transition-all font-medium text-sm btn-degrade-orange"
                >
                  Ajouter
                </button>
              </div>
            </div>
 
            <div v-if="colorsToAdd.length > 0 || colorsToRemove.length > 0" class="mt-6 space-y-4">
                Couleurs à ajouter  
              <div v-if="colorsToAdd.length > 0" class="bg-green-50 border border-green-200 rounded-lg p-4">
                <h4 class="text-sm font-medium text-green-800 mb-2">Couleurs à ajouter :</h4>
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
                <h4 class="text-sm font-medium error-color mb-2">Couleurs à supprimer :</h4>
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
  
          <div class="bg-white/80 backdrop-blur-sm rounded-xl p-4 sm:p-6 border border-gray-100 shadow-sm">
            <div class="flex items-center space-x-3 mb-4 sm:mb-6">
              <div class="w-8 h-8 bg-orange rounded-lg flex items-center justify-center">
                <RulerIcon class="w-4 h-4 text-white" />
              </div>
              <h3 class="text-lg sm:text-xl font-semibold text-gray-900">Tailles</h3>
            </div>

            <div class="mb-4">
              <label class="block text-sm font-medium text-gray-700 mb-2">Type de taille</label>
              <select 
                v-model="editData.sizeType"
                @change="updateAvailableSizes"
                class="text-sm sm:text-base input-style"
              >
                <option value="">Sélectionner le type de taille</option>
                <option v-for="sizeType in sizeTypes" :key="sizeType.value" :value="sizeType.value">
                  {{ sizeType.label }}
                </option>
              </select>
            </div>
  
            <div v-if="editData.sizeType" class="grid grid-cols-4 sm:grid-cols-6 lg:grid-cols-8 gap-2 mb-4">
              <button
                v-for="size in currentAvailableSizes"
                :key="size"
                type="button"
                @click="toggleSize(size)"
                :class="[
                  'px-3 py-2 sm:px-4 sm:py-3 rounded-lg border-2 transition-all duration-200 text-xs sm:text-sm font-medium',
                  editData.sizes.includes(size)
                    ? 'border-orange-500 bg-orange primary-color'
                    : 'border-gray-300 bg-white text-gray-700 hover:border-gray-400'
                ]"
              >
                {{ size }}
              </button>
            </div>

            <div v-if="editData.sizeType" class="border-t border-gray-200 pt-4">
              <label class="block text-sm font-medium text-gray-700 mb-2">Ajouter une taille personnalisée</label>
              <div class="flex gap-2">
                <input 
                  v-model="customSize"
                  type="text" 
                  :placeholder="getSizePlaceholder(editData.sizeType)"
                  class="flex-1 transition-all text-sm input-style"
                >
                <button 
                  @click="addCustomSize"
                  type="button"
                  class="px-4 py-2 btn-degrade-orange rounded-lg  transition-all font-medium text-sm"
                >
                  Ajouter
                </button>
              </div>
            </div>

            <div v-if="sizesToAdd.length > 0 || sizesToRemove.length > 0" class="mt-6 space-y-4">
                Tailles à ajouter  
              <div v-if="sizesToAdd.length > 0" class="bg-green-50 border border-green-200 rounded-lg p-4">
                <h4 class="text-sm font-medium text-green-800 mb-2">Tailles à ajouter :</h4>
                <div class="flex flex-wrap gap-2">
                  <span 
                    v-for="size in sizesToAdd" 
                    :key="size"
                    class="inline-flex items-center px-2 py-1 rounded-md text-xs font-medium bg-green-100 text-green-800"
                  >
                    {{ size }}
                  </span>
                </div>
              </div>

              <div v-if="sizesToRemove.length > 0" class="bg-red-50 border border-red-200 rounded-lg p-4">
                <h4 class="text-sm font-medium error-color mb-2">Tailles à supprimer :</h4>
                <div class="flex flex-wrap gap-2">
                  <span 
                    v-for="size in sizesToRemove" 
                    :key="size"
                    class="inline-flex items-center px-2 py-1 rounded-md text-xs font-medium bg-red-100 error-color"
                  >
                    {{ size }}
                  </span>
                </div>
              </div>
            </div>
          </div>
  
          <div class="bg-white/80 backdrop-blur-sm rounded-xl p-4 sm:p-6 border border-gray-100 shadow-sm">
            <div class="flex items-center space-x-3 mb-4 sm:mb-6">
              <div class="w-8 h-8 bg-orange rounded-lg flex items-center justify-center">
                <ImageIcon class="w-4 h-4 text-white" />
              </div>
              <h3 class="text-lg sm:text-xl font-semibold text-gray-900">Images du produit</h3>
              <span class="text-sm text-gray-500">({{ editData.images.length }}/8)</span>
            </div>

            <div class="mb-6">
              <div class="flex items-center justify-center w-full">
                <label 
                  for="image-upload" 
                  class="flex flex-col items-center justify-center w-full h-32 border-2 border-gray-300 border-dashed rounded-lg cursor-pointer bg-gray-50 hover:bg-gray-100 transition-colors"
                  :class="{ 'opacity-50 cursor-not-allowed': editData.images.length >= 8 }"
                >
                  <div class="flex flex-col items-center justify-center pt-5 pb-6">
                    <ImageIcon class="w-8 h-8 mb-4 text-gray-500" />
                    <p class="mb-2 text-sm text-gray-500">
                      <span class="font-semibold">Cliquez pour ajouter</span> des images
                    </p>
                    <p class="text-xs text-gray-500">PNG, JPG jusqu'à 10MB par image</p>
                  </div>
                  <input 
                    id="image-upload"
                    ref="fileInput"
                    type="file"
                    multiple
                    accept="image/*"
                    @change="handleImageUpload"
                    class="hidden"
                    :disabled="editData.images.length >= 8"
                  >
                </label>
              </div>
            </div>

            <div v-if="editData.images.length > 0" class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-4 mb-6">
              <div 
                v-for="(image, index) in editData.images" 
                :key="index"
                class="relative group"
              >
                <img 
                  :src="typeof image === 'string' ? image : image.preview || image.url" 
                  :alt="`Image ${index + 1}`"
                  class="w-full h-24 sm:h-32 object-cover rounded-lg border border-gray-200"
                >
                
                 
                <div 
                  v-if="index === 0"
                  class="absolute bottom-2 left-2 px-2 py-1 bg-orange text-white text-xs rounded-md"
                >
                  Principale
                </div>
                <div v-if="image.uploading" class="absolute inset-0 bg-black/50 flex items-center justify-center rounded-lg">
                  <div class="text-white text-sm font-medium">{{ image.uploadProgress }}%</div>
                </div>
                
                <div v-if="image.uploaded && image.url" class="absolute top-2 left-2 bg-green-500 text-white rounded-full w-6 h-6 flex items-center justify-center">
                  <CheckIcon class="h-4 w-4" />
                </div>
                
              <XIcon class="absolute -top-1 -right-1 w-4 h-4 error-color opacity-0 group-hover:opacity-100 transition-opacity"  @click="removeImage(index)" />
                
                <div class="absolute bottom-2 right-2 flex space-x-1 opacity-0 group-hover:opacity-100 transition-opacity">
                  
                <div class="w-6 h-6 bg-white rounded-full flex items-center justify-between shadow cursor-pointer" title="Déplacer vers la gauche">
                  <ChevronLeftIcon class="w-4 h-4 text-black cursor-pointer" @click="moveImageUp(index)" />
                </div>
                  
                  
                <div class="w-6 h-6 bg-white rounded-full flex items-center justify-center shadow cursor-pointer" title="Déplacer vers la droite">

                  <ChevronRightIcon class="w-4 h-4 text-black cursor-pointer"  @click="moveImageDown(index)" />
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
                <div class="mt-2 text-xs text-green-700">
                  <strong>Liens :</strong>
                  <ul class="list-disc list-inside mt-1 space-y-1">
                    <li v-for="(image, index) in imagesToAdd" :key="index" class="break-all">
                      {{ image.url || 'En attente d\'upload...' }}
                    </li>
                  </ul>
                </div>
              </div>

              <div v-if="imagesToRemove.length > 0" class="bg-red-50 border border-red-200 rounded-lg p-4">
                <h4 class="text-sm font-medium error-color mb-3">Images à supprimer ({{ imagesToRemove.length }}) :</h4>
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
                <div class="mt-2 text-xs error-color">
                  <strong>Liens :</strong>
                  <ul class="list-disc list-inside mt-1 space-y-1">
                    <li v-for="(imageUrl, index) in imagesToRemove" :key="index" class="break-all">
                      {{ imageUrl }}
                    </li>
                  </ul>
                </div>
              </div>
            </div>

            <div v-if="editData.images.length === 0" class="text-center py-8 text-gray-500">
              <ImageIcon class="w-12 h-12 mx-auto mb-4 text-gray-300" />
              <p>Aucune image ajoutée</p>
              <p class="text-sm">Ajoutez jusqu'à 8 images pour votre produit</p>
            </div>
          </div>

          <div class="bg-white/80 backdrop-blur-sm rounded-xl p-4 sm:p-6 border border-gray-100 shadow-sm">
            <div class="flex items-center space-x-3 mb-4 sm:mb-6">
              <div class="w-8 h-8 bg-orange rounded-lg flex items-center justify-center">
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
            
            <div v-if="editData.video" class="mt-6">
              <video :src="editData.video.preview || editData.video.url" controls class="w-full max-w-md mx-auto rounded-lg shadow-lg">
                Votre navigateur ne supporte pas la lecture de vidéos.
              </video>
              
              <div v-if="editData.video.uploading" class="mt-4">
                <div class="flex items-center justify-center space-x-2">
                  <div class="animate-spin rounded-full h-5 w-5 border-2 border-orange-500 border-t-transparent"></div>
                  <span class="text-sm text-gray-600">Upload en cours... {{ editData.video.uploadProgress }}%</span>
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
                <span class="text-sm text-green-700">Vidéo téléchargée avec succès</span>
              </div>

              <button 
                @click="removeVideo"
                type="button"
                class="mt-4 px-4 py-2 error-background-color text-white rounded-lg hover:bg-red-700 transition-colors font-medium"
              >
                Supprimer la vidéo
              </button>
            </div>
          </div>
  
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
                Produit actif
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
            Annuler
          </button>
          
          <button
            type="button"
            @click="handleSubmit"
            :disabled="isLoading || !canSubmit"
            class="flex-1 px-4 py-3 sm:px-6 sm:py-3 border border-transparent rounded-lg text-sm font-medium transition-all disabled:opacity-50 disabled:cursor-not-allowed btn-degrade-orange"
          >
            <div v-if="isLoading" class="flex items-center justify-center">
              <div class="animate-spin rounded-full h-4 w-4 border-2 border-white border-t-transparent mr-2"></div>
              Modification...
            </div>
            <div v-else class="flex items-center justify-center">
              <CheckIcon class="w-4 h-4 mr-2" />
              Modifier le produit
            </div>
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, reactive, computed, onMounted, watch, nextTick } from 'vue'
import { categoriesApi } from '../../services/api'
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
  Zap as ZapIcon,
  Ruler as RulerIcon,
  ChevronLeft as ChevronLeftIcon,
  ChevronRight as ChevronRightIcon,
  Video as VideoIcon
} from 'lucide-vue-next'

const props = defineProps({
  'product': Object,
  'boutique-id': [String, Number],
  'user-id': [String, Number]
})

const emit = defineEmits(['close', 'save'])

const isLoading = ref(false)
const loadingMessage = ref('')
const error = ref(null)
const wysiwygEditor = ref(null)
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
  images: [],
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
  // New fields
  engine_numbers: [],
  production_date: '',
  country_of_origin: '',
  wheelbase: null,
  gvw: null,
  payload_capacity: null,
  cabin_type: '',
  suspension_type: '',
  brake_system: '',
  type_size: '',
  dimension_length: null,
  dimension_width: null,
  dimension_height: null,
  curb_weight: null,
  fuel_tank_capacity: null,
  other_description: ''
})

const hasWholesalePrice = ref(false)

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
  const sizeType = sizeTypes.value.find(type => type.value === editData.value.sizeType)
  return sizeType ? sizeType.sizes : []
})

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

const colorsToRemove = computed(() => {
  return originalColors.value.filter(color => !editData.value.colors.includes(color))
})

const sizesToAdd = computed(() => {
  return editData.value.sizes.filter(size => !originalSizes.value.includes(size))
})

const sizesToRemove = computed(() => {
  return originalSizes.value.filter(size => !editData.value.sizes.includes(size))
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
  console.log('Description mise à jour:', editData.value.description)
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

const updateAvailableSizes = () => {
  editData.value.sizes = []
}

const toggleColor = (color) => {
  const index = editData.value.colors.indexOf(color)
  if (index > -1) {
    editData.value.colors.splice(index, 1)
  } else {
    editData.value.colors.push(color)
  }
}

const toggleSize = (size) => {
  const index = editData.value.sizes.indexOf(size)
  if (index > -1) {
    editData.value.sizes.splice(index, 1)
  } else {
    editData.value.sizes.push(size)
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

const addCustomSize = () => {
  if (customSize.value && !currentAvailableSizes.value.includes(customSize.value)) {
    const sizeType = sizeTypes.value.find(type => type.value === editData.value.sizeType)
    if (sizeType) {
      sizeType.sizes.push(customSize.value)
      editData.value.sizes.push(customSize.value)
    }
    customSize.value = ''
  }
}

const addEngineNumber = () => {
  editData.value.engine_numbers.push('')
}

const removeEngineNumber = (index) => {
  editData.value.engine_numbers.splice(index, 1)
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
    loadingMessage.value = 'Téléchargement des nouvelles images...'
    
    // Upload des nouvelles images
    const uploadPromises = newImages.value.map((image, index) => 
      uploadImageToCloudinary(image, index)
    )
    
    await Promise.all(uploadPromises)

    // Upload de la vidéo si nécessaire
    if (editData.value.video && editData.value.video.file && !editData.value.video.uploaded) {
      loadingMessage.value = 'Téléchargement de la vidéo...'
      await uploadVideoToCloudinary(editData.value.video)
    }
    
    // Préparer les URLs finales des images
    const finalImageUrls = editData.value.images.map(image => {
      if (typeof image === 'string') {
        return image // Image existante
      } else if (image.url) {
        return image.url // Nouvelle image uploadée
      }
      return null
    }).filter(Boolean)
    
    loadingMessage.value = 'Modification en cours...'
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
      sizesToAdd: sizesToAdd.value,
      sizesToRemove: sizesToRemove.value,
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
      // New fields
      engine_numbers: editData.value.engine_numbers.filter(num => num.trim() !== ''),
      production_date: editData.value.production_date,
      country_of_origin: editData.value.country_of_origin,
      wheelbase: editData.value.wheelbase,
      gvw: editData.value.gvw,
      payload_capacity: editData.value.payload_capacity,
      cabin_type: editData.value.cabin_type,
      suspension_type: editData.value.suspension_type,
      brake_system: editData.value.brake_system,
      tyre_size: editData.value.type_size,
      dimension_length: editData.value.dimension_length,
      dimension_width: editData.value.dimension_width,
      dimension_height: editData.value.dimension_height,
      curb_weight: editData.value.curb_weight,
      fuel_tank_capacity: editData.value.fuel_tank_capacity,
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
    console.error('Erreur lors de la modification:', err)
    error.value = 'Erreur lors de la modification du produit'
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
  const remainingSlots = 8 - editData.value.images.length
  
  if (remainingSlots <= 0) {
    error.value = 'Vous avez déjà atteint la limite de 8 images'
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
        editData.value.images.push(imageObj)
        newImages.value.push(imageObj)
      }
      reader.readAsDataURL(file)
    } else {
      error.value = 'Certaines images sont trop volumineuses (max 10MB)'
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
    error.value = 'La vidéo est trop volumineuse (max 50MB)'
  }
}

const removeVideo = () => {
  editData.value.video = null
}

const removeImage = (index) => {
  const imageToRemove = editData.value.images[index]
  
  if (typeof imageToRemove === 'string') {
    imagesToRemove.value.push(imageToRemove)
  } else if (imageToRemove.isNew) {
    // Si c'est une nouvelle image, la retirer du tableau des nouvelles images
    const newImageIndex = newImages.value.findIndex(img => img === imageToRemove)
    if (newImageIndex > -1) {
      newImages.value.splice(newImageIndex, 1)
    }
  }
  
  editData.value.images.splice(index, 1)
}

const moveImageUp = (index) => {
  if (index > 0) {
    const images = editData.value.images
    const temp = images[index]
    images[index] = images[index - 1]
    images[index - 1] = temp
  }
}

const moveImageDown = (index) => {
  const images = editData.value.images
  if (index < images.length - 1) {
    const temp = images[index]
    images[index] = images[index + 1]
    images[index + 1] = temp
  }
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
    console.error(`Erreur lors du téléchargement de l'image ${index}:`, error)
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
    console.error('Erreur lors du téléchargement de la vidéo:', error)
    video.uploading = false
    throw error
  }
}

watch(() => props.product, (newProduct) => {
  if (newProduct) {
    // Convertir les images existantes en format uniforme
    const existingImages = (newProduct.images || []).map(imageUrl => {
      if (typeof imageUrl === 'string') {
        return imageUrl
      }
      return imageUrl.url || imageUrl
    })
    console.log("newProduct:", newProduct)
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
      images: existingImages,
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
      // New fields
      engine_numbers: Array.isArray(newProduct.engine_numbers) ? [...newProduct.engine_numbers] : [],
      production_date: newProduct.production_date || '',
      country_of_origin: newProduct.country_of_origin || '',
      wheelbase: newProduct.wheelbase || null,
      gvw: newProduct.gvw || null,
      payload_capacity: newProduct.payload_capacity || null,
      cabin_type: newProduct.cabin_type || '',
      suspension_type: newProduct.suspension_type || '',
      brake_system: newProduct.brake_system || '',
      type_size: newProduct.tyre_size || '',
      dimension_length: newProduct.dimension_length || null,
      dimension_width: newProduct.dimension_width || null,
      dimension_height: newProduct.dimension_height || null,
      curb_weight: newProduct.curb_weight || null,
      fuel_tank_capacity: newProduct.fuel_tank_capacity || null,
      other_description: newProduct.other_description || ''
    }
    
    originalColors.value = [...(newProduct.colors || [])]
    originalSizes.value = [...(newProduct.sizes || [])]
    originalImages.value = [...existingImages]
    
    // Reset des tableaux de tracking
    imagesToRemove.value = []
    newImages.value = []
    
    // Vérifier si le produit a un prix de gros
    hasWholesalePrice.value = !!(newProduct.wholesale_price && newProduct.wholesale_price > 0)

    // Mettre à jour l'éditeur WYSIWYG si nécessaire
    if (wysiwygEditor.value) {
      nextTick(() => {
        wysiwygEditor.value.innerHTML = editData.value.description || ''
      })
    }
  }
  console.log("editData après mise à jour:", editData.value)
}, { immediate: true })

// Charger les catégories au montage du composant
onMounted(() => {
  fetchCategories()
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