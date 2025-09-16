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
      <div class="absolute inset-0 overflow-hidden pointer-events-none sm:rounded-2xl" style="background-color: #ffffff00;">
        <div class="absolute -top-10 -right-10 w-32 h-32 bg-gradient-to-br from-orange-200/30 to-orange-300/20 rounded-full blur-2xl animate-pulse"></div>
        <div class="absolute -bottom-10 -left-10 w-40 h-40 bg-gradient-to-br from-blue-200/25 to-indigo-200/15 rounded-full blur-2xl animate-pulse" style="animation-delay: 1s;"></div>
      </div>

      <!-- Header fixe -->
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

        <!-- Messages d'état -->
        <div v-if="error || isLoading || categoriesError || categoriesLoading" class="mt-4">
          <div v-if="error" class="mb-3 p-3 bg-red-50 border border-red-200 text-red-700 rounded-lg flex items-center space-x-2">
            <AlertCircleIcon class="w-4 h-4 text-red-500 flex-shrink-0" />
            <span class="text-sm">{{ error }}</span>
          </div>

          <div v-if="categoriesError" class="mb-3 p-3 bg-red-50 border border-red-200 text-red-700 rounded-lg flex items-center space-x-2">
            <AlertCircleIcon class="w-4 h-4 text-red-500 flex-shrink-0" />
            <div class="flex-1">
              <span class="text-sm">{{ categoriesError }}</span>
              <button 
                @click="fetchCategories" 
                class="ml-2 px-2 py-1 bg-red-600 text-white rounded text-xs hover:bg-red-700 transition-colors"
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

        <!-- Indicateur d'étapes -->
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

      <!-- Contenu scrollable -->
      <div class="overflow-y-auto h-[calc(100vh-200px)] sm:h-auto sm:max-h-[calc(60vh)] px-4 sm:px-6 py-6 relative z-5" style="color: white;">
        <form @submit.prevent="handleSubmit" class="space-y-6 sm:space-y-8">
          
          <!-- Étape 1: Informations de base -->
          <div v-show="currentStep === 0" class="space-y-6">
            <div class="bg-white/80 backdrop-blur-sm rounded-xl p-4 sm:p-6 border border-gray-100 shadow-sm">
              <div class="flex items-center space-x-3 mb-4 sm:mb-6">
                <div class="w-8 h-8  rounded-lg flex items-center justify-center bg-degrade-orange">
                  <InfoIcon class="w-4 h-4 text-white" />
                </div>
                <h3 class="text-lg sm:text-xl font-semibold text-gray-900">Informations de base</h3>
              </div>

              <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 sm:gap-6">
                <!-- Nom du produit -->
                <div class="sm:col-span-2">
                  <label for="name" class="block text-sm font-medium text-gray-700 mb-2">
                    Nom du produit <span class="primary-color">*</span>
                  </label>
                  <input
                    id="name"
                    v-model="productData.name"
                    type="text"
                    required
                    class=" text-sm sm:text-base  input-style"
                    placeholder="Ex: T-shirt Premium Coton"
                  >
                </div>

                <!-- Description courte -->
                <div class="sm:col-span-2">
                  <label for="description" class="block text-sm font-medium text-gray-700 mb-2">
                    Description courte
                  </label>
                  <textarea
                    id="description"
                    v-model="productData.description"
                    rows="3"
                    class="text-sm sm:text-base  input-style resize-none"
                    placeholder="Décrivez votre produit..."
                  ></textarea>
                </div>

                <!-- Description détaillée WYSIWYG -->
                <div class="sm:col-span-2">
                  <div class="flex items-center mb-3">
                    <input 
                      v-model="productData.hasDetailedDescription"
                      id="detailed-description-toggle"
                      type="checkbox"
                      class="checkbox-style"
                    >
                    <label for="detailed-description-toggle" class="ml-3 text-sm font-medium text-gray-700 flex items-center">
                      <EditIcon class="w-4 h-4  mr-1" style="color:#fe7900" />
                      Activer la description détaillée (WYSIWYG)
                    </label>
                  </div>
                  
                  <div v-if="productData.hasDetailedDescription">
                    <div class="border border-gray-300 rounded-lg focus-within:ring-1 focus-within:ring-orange-400 focus-within:border-orange-400 transition-all duration-200">
                      <!-- Barre d'outils WYSIWYG -->
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
                      <!-- Zone d'édition -->
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

                <!-- Catégorie -->
                <div>
                  <label for="category" class="block text-sm font-medium text-gray-700 mb-2">
                    Catégorie <span class="primary-color">*</span>
                  </label>
                  <select
                    id="category"
                    v-model="productData.category_id"
                    @change="updateSubcategories"
                    required
                    :disabled="categoriesLoading"
                    class="text-sm sm:text-base input-style"
                  >
                    <option value="">{{ categoriesLoading ? 'Chargement...' : 'Sélectionner une catégorie' }}</option>
                    <option v-for="category in categories" :key="category.id" :value="category.id">
                      {{ category.name }}
                    </option>
                  </select>
                </div>

                <!-- Sous-catégorie -->
                <div>
                  <label for="subcategory" class="block text-sm font-medium text-gray-700 mb-2">
                    Sous-catégorie <span class="primary-color">*</span>
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

                <!-- Sous-sous-catégorie -->
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

                <!-- Sous-sous-sous-catégorie -->
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

                <!-- Tags -->
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

          <!-- Étape 2: Prix et Stock -->
          <div v-show="currentStep === 1" class="space-y-6">
            <div class="bg-white/80 backdrop-blur-sm rounded-xl p-4 sm:p-6 border border-gray-100 shadow-sm">
              <div class="flex items-center space-x-3 mb-4 sm:mb-6">
                <div class="w-8 h-8 bg-gradient-to-br from-green-400 to-green-500 rounded-lg flex items-center justify-center">
                  <DollarSignIcon class="w-4 h-4 text-white" />
                </div>
                <h3 class="text-lg sm:text-xl font-semibold text-gray-900">Prix et Stock</h3>
              </div>

              <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 sm:gap-6">
                <!-- Prix unitaire -->
                <div>
                  <label for="unit_price" class="block text-sm font-medium text-gray-700 mb-2">
                    Prix unitaire (FCFA) <span class="primary-color">*</span>
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

                <!-- Stock initial -->
                <div>
                  <label for="stock" class="block text-sm font-medium text-gray-700 mb-2">
                    Stock initial <span class="primary-color">*</span>
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

                <!-- Type d'unité -->
                <div>
                  <label for="unit_type" class="block text-sm font-medium text-gray-700 mb-2">
                    Type d'unité
                  </label>
                  <select
                    id="unit_type"
                    v-model="productData.unit_type"
                    class="text-sm sm:text-base input-style"
                    style="color: black;"
                  >
                    <option v-for="unit in availableUnitTypes" :key="unit.value" :value="unit.value">
                      {{ unit.label }}
                    </option>
                  </select>
                </div>
              </div>

              <!-- Prix de gros -->
              <div class="mt-6">
                <div class="flex items-center mb-4">
                  <input 
                    v-model="productData.hasWholesalePrice"
                    id="wholesale-price"
                    type="checkbox"
                    class="checkbox-style"
                  >
                  <label for="wholesale-price" class="ml-3 text-sm font-medium text-gray-700 flex items-center">
                    <ZapIcon class="w-4 h-4  mr-1" />
                    Activer le prix de gros
                  </label>
                </div>
                
                <div v-if="productData.hasWholesalePrice" class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                  <div>
                    <label for="wholesale_price" class="block text-sm font-medium text-gray-700 mb-2">
                      Prix de gros (FCFA)
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

          <!-- Étape 3: Variantes -->
          <div v-show="currentStep === 2" class="space-y-6">
            <!-- Couleurs -->
            <div class="bg-white/80 backdrop-blur-sm rounded-xl p-4 sm:p-6 border border-gray-100 shadow-sm">
              <div class="flex items-center space-x-3 mb-4 sm:mb-6">
                <div class="w-8 h-8 bg-gradient-to-br from-purple-500 to-purple-600 rounded-lg flex items-center justify-center">
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

              <!-- Couleur personnalisée -->
              <div class="border-t border-gray-200 pt-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">Ajouter une couleur personnalisée</label>
                <div class="flex gap-2">
                  <input 
                    v-model="customColor.name"
                    type="text" 
                    placeholder="Nom de la couleur"
                    class="flex-1 px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition-all text-sm"
                  >
                  <input 
                    v-model="customColor.value"
                    type="color" 
                    class="w-12 h-10 border border-gray-300 rounded-lg cursor-pointer"
                  >
                  <button 
                    @click="addCustomColor"
                    type="button"
                    class="px-4 py-2 bg-gradient-to-r from-orange-500 to-orange-600 text-white rounded-lg hover:from-orange-600 hover:to-orange-700 transition-all duration-200 font-medium text-sm"
                  >
                    Ajouter
                  </button>
                </div>
              </div>
            </div>

            <!-- Tailles -->
            <div class="bg-white/80 backdrop-blur-sm rounded-xl p-4 sm:p-6 border border-gray-100 shadow-sm">
              <div class="flex items-center space-x-3 mb-4 sm:mb-6">
                <div class="w-8 h-8 bg-gradient-to-br from-indigo-500 to-indigo-600 rounded-lg flex items-center justify-center">
                  <RulerIcon class="w-4 h-4 text-white" />
                </div>
                <h3 class="text-lg sm:text-xl font-semibold text-gray-900">Tailles</h3>
              </div>

              <!-- Type de taille -->
              <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">Type de taille</label>
                <select 
                  v-model="productData.sizeType"
                  @change="updateAvailableSizes"
                  class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition-all text-sm sm:text-base"
                  style="color: black;"
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
                      ? 'border-orange-500 bg-orange-50 text-orange-700'
                      : 'border-gray-300 bg-white text-gray-700 hover:border-gray-400'
                  ]"
                >
                  {{ size }}
                </button>
              </div>
              
              <!-- Taille personnalisée -->
              <div v-if="productData.sizeType" class="border-t border-gray-200 pt-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">Ajouter une taille personnalisée</label>
                <div class="flex gap-2">
                  <input 
                    v-model="customSize"
                    type="text" 
                    :placeholder="getSizePlaceholder(productData.sizeType)"
                    class="flex-1 px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition-all text-sm"
                  >
                  <button 
                    @click="addCustomSize"
                    type="button"
                    class="px-4 py-2 bg-gradient-to-r from-orange-500 to-orange-600 text-white rounded-lg hover:from-orange-600 hover:to-orange-700 transition-all duration-200 font-medium text-sm"
                  >
                    Ajouter
                  </button>
                </div>
              </div>
            </div>
          </div>

          <!-- Étape 4: Images -->
          <div v-show="currentStep === 3" class="space-y-6">
            <!-- Images -->
            <div class="bg-white/80 backdrop-blur-sm rounded-xl p-4 sm:p-6 border border-gray-100 shadow-sm">
              <div class="flex items-center space-x-3 mb-4 sm:mb-6">
                <div class="w-8 h-8 bg-gradient-to-br from-indigo-500 to-indigo-600 rounded-lg flex items-center justify-center">
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
                  class="inline-flex items-center px-4 py-2 border border-orange-500 rounded-lg text-sm font-medium text-orange-600 hover:bg-orange-50 transition-colors"
                >
                  Parcourir les fichiers
                </button>
                <p class="text-xs text-gray-500 mt-2">PNG, JPG jusqu'à 10MB par image (max 8)</p>
              </div>

              <!-- Aperçu des images -->
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
                      class="absolute bottom-2 left-2 px-2 py-1 bg-orange-500 text-white text-xs rounded-md "
                    >
                      Principal
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <!-- Vidéo -->
            <div class="bg-white/80 backdrop-blur-sm rounded-xl p-4 sm:p-6 border border-gray-100 shadow-sm">
              <div class="flex items-center space-x-3 mb-4 sm:mb-6">
                <div class="w-8 h-8 bg-gradient-to-br from-purple-500 to-purple-600 rounded-lg flex items-center justify-center">
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
                <button 
                  @click="removeVideo"
                  type="button"
                  class="mt-4 px-4 py-2 bg-red-500 text-white rounded-lg hover:bg-red-600 transition-colors font-medium"
                >
                  Supprimer la vidéo
                </button>
              </div>
            </div>

            <!-- Statut et résumé -->
            <div class="bg-white/80 backdrop-blur-sm rounded-xl p-4 sm:p-6 border border-gray-100 shadow-sm">
              <div class="flex items-center mb-6">
                <input 
                  v-model="productData.is_active"
                  id="is-active"
                  type="checkbox"
                  class="w-5 h-5 text-orange-600 bg-gray-100 border-gray-300 rounded focus:ring-orange-500 focus:ring-2"
                >
                <label for="is-active" class="ml-3 text-sm font-medium text-gray-700 flex items-center">
                  <CheckCircleIcon class="w-4 h-4 text-green-600 mr-1" />
                  Publier immédiatement ce produit
                </label>
              </div>

              <!-- Résumé -->
              <div class="bg-gray-50 rounded-lg p-4">
                <h4 class="font-semibold text-gray-900 mb-4 flex items-center">
                  <FileTextIcon class="w-5 h-5 text-blue-600 mr-2" />
                  Résumé du produit
                </h4>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-sm">
                  <div class="space-y-2">
                    <p><span class="font-medium text-gray-700">Nom:</span> <span class="text-gray-900">{{ productData.name || 'Non défini' }}</span></p>
                    <p><span class="font-medium text-gray-700">Catégorie:</span> <span class="text-gray-900">{{ getCategoryName(productData.category_id) || 'Non définie' }}</span></p>
                    <p><span class="font-medium text-gray-700">Prix:</span> <span class="text-gray-900">{{ productData.unit_price ? productData.unit_price + ' FCFA' : 'Non défini' }}</span></p>
                    <p><span class="font-medium text-gray-700">Stock:</span> <span class="text-gray-900">{{ productData.stock || 'Non défini' }}</span></p>
                  </div>
                  <div class="space-y-2">
                    <p><span class="font-medium text-gray-700">Couleurs:</span> <span class="text-gray-900">{{ productData.colors.length }} sélectionnée(s)</span></p>
                    <p><span class="font-medium text-gray-700">Tailles:</span> <span class="text-gray-900">{{ productData.sizes.length }} sélectionnée(s)</span></p>
                    <p><span class="font-medium text-gray-700">Images:</span> <span class="text-gray-900">{{ productData.images.length }}/8</span></p>
                    <p><span class="font-medium text-gray-700">Vidéo:</span> <span class="text-gray-900">{{ productData.video ? 'Oui' : 'Non' }}</span></p>
                  </div>
                </div>
              </div>
            </div>
          </div>

        </form>
      </div>

      <!-- Footer fixe avec navigation -->
      <div class="sticky bottom-0 bg-white/95 backdrop-blur-sm border-t border-gray-200 px-4 sm:px-6 py-4 sm:rounded-b-2xl z-50">
        <div class="flex flex-col sm:flex-row gap-3 sm:gap-0 sm:justify-between sm:items-center">
          <!-- Navigation étapes -->
          <div class="flex gap-2 sm:gap-3 order-2 sm:order-1">
            <button
              v-if="currentStep > 0"
              type="button"
              @click.prevent="handlePreviousStep"
              :disabled="isLoading"
              class="flex-1 sm:flex-none px-4 py-3 sm:px-6 sm:py-3  rounded-lg text-sm font-medium text-gray-700 touch-manipulation btn-cancel"
            >
              <ChevronLeftIcon class="w-4 h-4 mr-2 inline" />
              Précédent
            </button>
          </div>

          <!-- Actions principales -->
          <div class="flex gap-2 sm:gap-3 order-1 sm:order-2">
            <button
              type="button"
              @click.prevent="handleCloseModal"
              :disabled="isLoading"
              class="flex-1 sm:flex-none px-4 py-3 sm:px-6 sm:py-3 rounded-lg text-sm font-medium  touch-manipulation btn-cancel"
            >
              Annuler
            </button>
            
            <button
              v-if="currentStep < steps.length - 1"
              type="button"
              @click.prevent="handleNextStep"
              :disabled="!canProceedToNextStep || isLoading"
              class="flex-1 sm:flex-none px-4 py-3 sm:px-6 sm:py-3 border border-transparent rounded-lg text-sm font-medium text-white bg-gradient-to-r from-orange-500 to-orange-600 hover:from-orange-600 hover:to-orange-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-orange-500 transition-all disabled:opacity-50 disabled:cursor-not-allowed active:scale-95 touch-manipulation"
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
import { categoriesApi } from '../../services/api'
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

// Configuration Cloudinary
const cloudinaryConfig = {
  cloudName: 'daaavha4z',
  uploadPreset: 'aliadjame',
  apiKey: 'wy0Eh-uA0Y0Ci3nyODix0b3WejA',
  uploadUrl: 'https://api.cloudinary.com/v1_1/daaavha4z/image/upload'
}

const steps = [
  { title: 'Informations' },
  { title: 'Prix & Stock' },
  { title: 'Variantes' },
  { title: 'Images' }
]

const productData = reactive({
  name: '',
  description: '',
  detailed_description: '',
  hasDetailedDescription: false,
  category_id: '',
  subcategory_id: '',
  subsubcategory_id: '',
  subsubsubcategory_id: '', // Nouveau champ pour le 4ème niveau
  tags: '',
  unit_price: null,
  stock: null,
  unit_type: 'quantity',
  hasWholesalePrice: false,
  wholesale_price: null,
  wholesale_min_qty: null,
  colors: [],
  sizes: [],
  sizeType: '',
  images: [],
  imageUrls: [],
  video: null,
  videoUrl: '',
  is_active: true
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

// Validation améliorée pour l'étape 2
const canProceedToNextStep = computed(() => {
  switch (currentStep.value) {
    case 0:
      return !!(productData.name && productData.category_id && productData.subcategory_id)
    case 1:
      // Validation plus stricte pour l'étape 2
      const hasValidPrice = productData.unit_price !== null && 
                           productData.unit_price !== '' && 
                           productData.unit_price !== undefined && 
                           Number(productData.unit_price) > 0
      
      const hasValidStock = productData.stock !== null && 
                           productData.stock !== '' && 
                           productData.stock !== undefined && 
                           Number(productData.stock) >= 0
      
      console.log('Validation étape 2:', {
        unit_price: productData.unit_price,
        stock: productData.stock,
        hasValidPrice,
        hasValidStock,
        canProceed: hasValidPrice && hasValidStock
      })
      
      return hasValidPrice && hasValidStock
    case 2:
      return true // Les variantes sont optionnelles
    case 3:
      return true // Les images sont optionnelles
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

const getSubcategoryName = (id) => {
  for (const category of categories.value) {
    const subcategory = (category.subcategories || []).find(sub => sub.id === id)
    if (subcategory) return subcategory.name
  }
  return ''
}

const getSubSubcategoryName = (id) => {
  for (const category of categories.value) {
    for (const subcategory of (category.subcategories || [])) {
      if (subcategory.sub_subcategories) {
        const subsubcategory = subcategory.sub_subcategories.find(subsub => subsub.id === id)
        if (subsubcategory) return subsubcategory.name
      }
    }
  }
  return ''
}

const getSubSubSubcategoryName = (id) => {
  for (const category of categories.value) {
    for (const subcategory of (category.subcategories || [])) {
      if (subcategory.sub_subcategories) {
        for (const subsubcategory of subcategory.sub_subcategories) {
          if (subsubcategory.sub_sub_subcategories) {
            const subsubsubcategory = subsubcategory.sub_sub_subcategories.find(subsubsub => subsubsub.id === id)
            if (subsubsubcategory) return subsubsubcategory.name
          }
        }
      }
    }
  }
  return ''
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
  { value: 'weight_kg', label: 'Poids (kg)' },
  { value: 'weight_g', label: 'Poids (g)' },
  { value: 'volume_l', label: 'Volume (L)' },
  { value: 'volume_ml', label: 'Volume (mL)' },
  { value: 'length_m', label: 'Longueur (m)' },
  { value: 'length_cm', label: 'Longueur (cm)' },
  { value: 'area_m2', label: 'Surface (m²)' }
])

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
  productData.detailed_description = wysiwygEditor.value.innerHTML
}

const updateSubcategories = () => {
  productData.subcategory_id = ''
  productData.subsubcategory_id = ''
  productData.subsubsubcategory_id = '' // Réinitialiser aussi le 4ème niveau
}

const updateSubSubcategories = () => {
  productData.subsubcategory_id = ''
  productData.subsubsubcategory_id = '' // Réinitialiser aussi le 4ème niveau
}

const updateSubSubSubcategories = () => {
  productData.subsubsubcategory_id = ''
}

const updateAvailableSizes = () => {
  productData.sizes = []
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
  } else {
    productData.colors.push(color)
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

// Méthodes de navigation améliorées
const handleNextStep = async () => {
  console.log('handleNextStep appelé, currentStep:', currentStep.value)
  console.log('Données actuelles:', {
    unit_price: productData.unit_price,
    stock: productData.stock,
    canProceed: canProceedToNextStep.value
  })
  
  // Effacer les erreurs précédentes
  error.value = null
  
  if (!canProceedToNextStep.value) {
    if (currentStep.value === 1) {
      error.value = 'Veuillez remplir correctement le prix unitaire et le stock avant de continuer'
    } else {
      error.value = 'Veuillez remplir tous les champs obligatoires'
    }
    
    // Effacer l'erreur après 3 secondes
    setTimeout(() => {
      error.value = null
    }, 3000)
    
    return
  }
  
  if (currentStep.value < steps.length - 1) {
    currentStep.value++
    console.log('Nouveau currentStep:', currentStep.value)
    
    // Attendre le prochain tick pour s'assurer que le DOM est mis à jour
    await nextTick()
  }
}

const handlePreviousStep = async () => {
  console.log('handlePreviousStep appelé, currentStep:', currentStep.value)
  
  if (currentStep.value > 0) {
    currentStep.value--
    console.log('Nouveau currentStep:', currentStep.value)
    
    // Effacer les erreurs
    error.value = null
    
    // Attendre le prochain tick pour s'assurer que le DOM est mis à jour
    await nextTick()
  }
}

const handleCloseModal = () => {
  console.log('handleCloseModal appelé')
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
    
    const response = await axios.post(cloudinaryConfig.uploadUrl, formData, {
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
    
    const response = await axios.post(
      `https://api.cloudinary.com/v1_1/${cloudinaryConfig.cloudName}/video/upload`, 
      formData, 
      {
        headers: {
          'Content-Type': 'multipart/form-data'
        },
        onUploadProgress: (progressEvent) => {
          const percentCompleted = Math.round((progressEvent.loaded * 100) / progressEvent.total)
          productData.video.uploadProgress = percentCompleted
        }
      }
    )
    
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

const prepareDataForSubmission = () => {
  const formData = {
    name: productData.name,
    description: productData.description,
    category_id: productData.category_id,
    subcategory_id: productData.subcategory_id,
    subsubcategory_id: productData.subsubcategory_id,
    subsubsubcategory_id: productData.subsubsubcategory_id,
    subsubsubcategory_id: productData.subsubsubcategory_id,
    unit_price: parseFloat(productData.unit_price),
    stock: parseInt(productData.stock),
    unit_type: productData.unit_type,
    tags: productData.tags,
    is_active: productData.is_active,
    colors: productData.colors,
    sizes: productData.sizes,
    size_type: productData.sizeType
  }
  
  // N'inclure la description détaillée que si elle est activée
  if (productData.hasDetailedDescription && productData.detailed_description) {
    formData.detailed_description = productData.detailed_description
  }
  
  if (productData.hasWholesalePrice) {
    formData.wholesale_price = parseFloat(productData.wholesale_price)
    formData.wholesale_min_qty = parseInt(productData.wholesale_min_qty)
  }
  
  formData.images = productData.imageUrls.map((url, index) => ({
    url: url,
    alt_text: `${productData.name} - Image ${index + 1}`,
    is_primary: index === 0 ? 1 : 0,
    sort_order: index
  }))
  
  if (productData.videoUrl) {
    formData.video_url = productData.videoUrl
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

// Charger les catégories au montage du composant
onMounted(() => {
  fetchCategories()
})
</script>

<style scoped>

/* Couleurs personnalisées */
.primary-color-text {
  color: #fe7900;
}
.primary-background {
  background-color: #fe7900;
}
.bg-degrade-orange {
  background: linear-gradient(90deg, #fe7900, #ff5a01);
 
}
.btn-degrade-orange {
  background: linear-gradient(90deg, #fe7900, #ff5a01);
  color: white;
  transition: background 0.3s;
}
.btn-degrade-orange:hover {
  background: linear-gradient(90deg, #ff5a01, #fe7900);
  color: white;
}

.input-style {
  width: 100%;
  color: black ; 
  border: 1px solid #d1d5db; 
  border-radius: 0.5rem; 
  padding: 0.80rem 0.75rem; 
  transition: border-color 0.2s, box-shadow 0.2s; 
}
.input-style:focus {
  border-color: #fe7900; 
  box-shadow: 0 0 0 0.5px #fe7900; 
}

.checkbox-style {
  width: 1.25rem; 
  height: 1.25rem; 
  border: 1px solid #d1d5db; 
  border-radius: 0.25rem; 
  color: #fe7900 ;
  transition: background-color 0.2s, border-color 0.2s; 
}
.checkbox-style:focus {
  box-shadow: 0 0 0 1px #fe7900;
  border-color: #fe7900; 
}
.checkbox-style:checked {
  background-color: #fe7900; 
  border-color: #fe7900; 
}


.bg-orange-500 {
  background-color: #F65A11;
}

.bg-orange-600 {
  background-color: #e54a0a;
}

.hover\:bg-orange-600:hover {
  background-color: #e54a0a;
}

.hover\:bg-orange-700:hover {
  background-color: #d1440a;
}

.focus\:ring-orange-500:focus {
  --tw-ring-color: rgba(246, 90, 17, 0.5);
  background-color: #d1440a;
}

.focus\:ring-orange-500:focus {
  --tw-ring-color: rgba(246, 90, 17, 0.5);
}

.focus\:border-orange-500:focus {
  border-color: #F65A11;
}

.text-orange-600 {
  color: #e54a0a;
}

.border-orange-500 {
  border-color: #F65A11;
}

/* Amélioration pour les interactions tactiles */
.touch-manipulation {
  touch-action: manipulation;
}
.btn-cancel {
  background-color: #d5dee7;
  color: #374151;
  border: 1px solid #d1d5db;      /* Bordure gris */
  transition: background 0.2s, color 0.2s;
}
.btn-cancel:hover {
  background-color: #cbd5e1;      /* Gris plus foncé au survol */
  color: #1f2937;                 /* Texte plus foncé au survol */
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