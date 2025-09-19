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
      <div class="absolute inset-0 overflow-hidden pointer-events-none sm:rounded-2xl">
        <div class="absolute inset-0 bg-gradient-to-br from-gray-50 via-white to-gray-100"></div>
        <div class="absolute -top-10 -right-10 w-32 h-32 bg-gradient-to-br from-orange-200/30 to-orange-300/20 rounded-full blur-2xl animate-pulse"></div>
        <div class="absolute -bottom-10 -left-10 w-40 h-40 bg-gradient-to-br from-blue-200/25 to-indigo-200/15 rounded-full blur-2xl animate-pulse" style="animation-delay: 1s;"></div>
      </div>
  
      <!-- Header fixe -->
      <div class="sticky top-0 z-10 bg-white/95 backdrop-blur-sm border-b border-gray-200 px-4 sm:px-6 py-4 sm:rounded-t-2xl">
        <div class="flex items-center justify-between">
          <div class="flex items-center space-x-3">
            <div class="w-10 h-10 bg-gradient-to-br from-orange-500 to-orange-600 rounded-lg flex items-center justify-center">
              <EditIcon class="w-5 h-5 text-white" />
            </div>
            <div>
              <h2 class="text-xl sm:text-2xl font-bold text-gray-900">Modifier le produit</h2>
              <p class="text-sm text-gray-600 hidden sm:block">Modifiez les informations de votre produit</p>
            </div>
          </div>
          <button 
            @click="closeModal"
            class="w-10 h-10 rounded-lg bg-gray-100 hover:bg-gray-200 flex items-center justify-center transition-colors"
          >
            <XIcon class="w-5 h-5 text-gray-500" />
          </button>
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
  
          <div v-if="isLoading" class="mb-3 p-3 bg-blue-50 border border-blue-200 text-blue-700 rounded-lg flex items-center space-x-2">
            <div class="animate-spin w-4 h-4 border-2 border-blue-500 border-t-transparent rounded-full flex-shrink-0"></div>
            <span class="text-sm">{{ loadingMessage }}</span>
          </div>
  
          <div v-if="categoriesLoading" class="mb-3 p-3 bg-blue-50 border border-blue-200 text-blue-700 rounded-lg flex items-center space-x-2">
            <div class="animate-spin w-4 h-4 border-2 border-blue-500 border-t-transparent rounded-full flex-shrink-0"></div>
            <span class="text-sm">Chargement des catégories...</span>
          </div>
        </div>
      </div>
  
      <!-- Contenu scrollable -->
      <div class="overflow-y-auto h-[calc(100vh-200px)] sm:h-auto sm:max-h-[calc(60vh)] px-4 sm:px-6 py-6 relative z-5">
        <form @submit.prevent="handleSubmit" class="space-y-6 sm:space-y-8" v-if="editData">
          
          <!-- Informations de base -->
          <div class="bg-white/80 backdrop-blur-sm rounded-xl p-4 sm:p-6 border border-gray-100 shadow-sm">
            <div class="flex items-center space-x-3 mb-4 sm:mb-6">
              <div class="w-8 h-8 bg-gradient-to-br from-blue-500 to-blue-600 rounded-lg flex items-center justify-center">
                <InfoIcon class="w-4 h-4 text-white" />
              </div>
              <h3 class="text-lg sm:text-xl font-semibold text-gray-900">Informations de base</h3>
            </div>
  
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 sm:gap-6">
              <!-- Nom du produit -->
              <div class="sm:col-span-2">
                <label for="name" class="block text-sm font-medium text-gray-700 mb-2">
                  Nom du produit <span class="text-red-500">*</span>
                </label>
                <input
                  id="name"
                  v-model="editData.name"
                  type="text"
                  required
                  class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition-all text-sm sm:text-base"
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
                  v-model="editData.description"
                  rows="3"
                  class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition-all text-sm sm:text-base resize-none"
                  placeholder="Décrivez votre produit..."
                ></textarea>
              </div>
  
              <!-- Catégorie -->
              <div>
                <label for="category" class="block text-sm font-medium text-gray-700 mb-2">
                  Catégorie <span class="text-red-500">*</span>
                </label>
                <select
                  id="category"
                  v-model="editData.category_id"
                  @change="updateSubcategories"
                  required
                  :disabled="categoriesLoading"
                  class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition-all text-sm sm:text-base disabled:bg-gray-100"
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
                  Sous-catégorie <span class="text-red-500">*</span>
                </label>
                <select
                  id="subcategory"
                  v-model="editData.subcategory_id"
                  @change="updateSubSubcategories"
                  required
                  :disabled="!editData.category_id || categoriesLoading"
                  class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition-all text-sm sm:text-base disabled:bg-gray-100"
                >
                  <option value="">Sélectionner une sous-catégorie</option>
                  <option v-for="subcategory in availableSubcategories" :key="subcategory.id" :value="subcategory.id">
                    {{ subcategory.name }}
                  </option>
                </select>
              </div>
  
              <!-- Sous-sous-catégorie -->
              <div v-if="availableSubSubcategories.length > 0" class="sm:col-span-2">
                <label for="subsubcategory" class="block text-sm font-medium text-gray-700 mb-2">
                  Sous-sous-catégorie
                </label>
                <select
                  id="subsubcategory"
                  v-model="editData.subsubcategory_id"
                  @change="updateSubSubSubcategories"
                  :disabled="!editData.subcategory_id || categoriesLoading"
                  class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition-all text-sm sm:text-base disabled:bg-gray-100"
                >
                  <option value="">Sélectionner une sous-sous-catégorie (optionnel)</option>
                  <option v-for="subsubcategory in availableSubSubcategories" :key="subsubcategory.id" :value="subsubcategory.id">
                    {{ subsubcategory.name }}
                  </option>
                </select>
              </div>
  
              <!-- Sous-sous-sous-catégorie -->
              <div v-if="availableSubSubSubcategories.length > 0" class="sm:col-span-2">
                <label for="subsubsubcategory" class="block text-sm font-medium text-gray-700 mb-2">
                  Sous-sous-sous-catégorie
                </label>
                <select
                  id="subsubsubcategory"
                  v-model="editData.subsubsubcategory_id"
                  :disabled="!editData.subsubcategory_id || categoriesLoading"
                  class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition-all text-sm sm:text-base disabled:bg-gray-100"
                >
                  <option value="">Sélectionner une sous-sous-sous-catégorie (optionnel)</option>
                  <option v-for="subsubsubcategory in availableSubSubSubcategories" :key="subsubsubcategory.id" :value="subsubsubcategory.id">
                    {{ subsubsubcategory.name }}
                  </option>
                </select>
              </div>
            </div>
          </div>
  
          <!-- Prix et Stock -->
          <div class="bg-white/80 backdrop-blur-sm rounded-xl p-4 sm:p-6 border border-gray-100 shadow-sm">
            <div class="flex items-center space-x-3 mb-4 sm:mb-6">
              <div class="w-8 h-8 bg-gradient-to-br from-green-500 to-green-600 rounded-lg flex items-center justify-center">
                <DollarSignIcon class="w-4 h-4 text-white" />
              </div>
              <h3 class="text-lg sm:text-xl font-semibold text-gray-900">Prix et Stock</h3>
            </div>
  
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 sm:gap-6">
              <!-- Prix unitaire -->
              <div>
                <label for="unit_price" class="block text-sm font-medium text-gray-700 mb-2">
                  Prix unitaire (FCFA) <span class="text-red-500">*</span>
                </label>
                <input
                  id="unit_price"
                  v-model.number="editData.unit_price"
                  type="number"
                  min="0"
                  step="1"
                  required
                  class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition-all text-sm sm:text-base"
                  placeholder="15000"
                >
              </div>
  
              <!-- Stock initial -->
              <div>
                <label for="stock" class="block text-sm font-medium text-gray-700 mb-2">
                  Stock <span class="text-red-500">*</span>
                </label>
                <input
                  id="stock"
                  v-model.number="editData.stock"
                  type="number"
                  min="0"
                  step="1"
                  required
                  class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition-all text-sm sm:text-base"
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
                  v-model="editData.unit_type"
                  class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition-all text-sm sm:text-base"
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
                  v-model="hasWholesalePrice"
                  id="wholesale-price"
                  type="checkbox"
                  class="w-5 h-5 text-orange-600 bg-gray-100 border-gray-300 rounded focus:ring-orange-500 focus:ring-2"
                >
                <label for="wholesale-price" class="ml-3 text-sm font-medium text-gray-700 flex items-center">
                  <ZapIcon class="w-4 h-4 text-orange-600 mr-1" />
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
                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition-all text-sm sm:text-base"
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
                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition-all text-sm sm:text-base"
                    placeholder="10"
                  >
                </div>
              </div>
            </div>
          </div>
  
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

            <!-- Ajout des tableaux de tracking pour les couleurs -->
            <div v-if="colorsToAdd.length > 0 || colorsToRemove.length > 0" class="mt-6 space-y-4">
              <!-- Couleurs à ajouter -->
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

              <!-- Couleurs à supprimer -->
              <div v-if="colorsToRemove.length > 0" class="bg-red-50 border border-red-200 rounded-lg p-4">
                <h4 class="text-sm font-medium text-red-800 mb-2">Couleurs à supprimer :</h4>
                <div class="flex flex-wrap gap-2">
                  <span 
                    v-for="color in colorsToRemove" 
                    :key="color"
                    class="inline-flex items-center px-2 py-1 rounded-md text-xs font-medium bg-red-100 text-red-800"
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
  
          <!-- Tailles -->
          <div class="bg-white/80 backdrop-blur-sm rounded-xl p-4 sm:p-6 border border-gray-100 shadow-sm">
            <div class="flex items-center space-x-3 mb-4 sm:mb-6">
              <div class="w-8 h-8 bg-gradient-to-br from-indigo-500 to-indigo-600 rounded-lg flex items-center justify-center">
                <RulerIcon class="w-4 h-4 text-white" />
              </div>
              <h3 class="text-lg sm:text-xl font-semibold text-gray-900">Tailles</h3>
            </div>
  
            <div class="grid grid-cols-4 sm:grid-cols-6 lg:grid-cols-8 gap-2 mb-4">
              <button
                v-for="size in availableSizes"
                :key="size"
                type="button"
                @click="toggleSize(size)"
                :class="[
                  'px-3 py-2 sm:px-4 sm:py-3 rounded-lg border-2 transition-all duration-200 text-xs sm:text-sm font-medium',
                  editData.sizes.includes(size)
                    ? 'border-orange-500 bg-orange-50 text-orange-700'
                    : 'border-gray-300 bg-white text-gray-700 hover:border-gray-400'
                ]"
              >
                {{ size }}
              </button>
            </div>

            <!-- Ajout des tableaux de tracking pour les tailles -->
            <div v-if="sizesToAdd.length > 0 || sizesToRemove.length > 0" class="mt-6 space-y-4">
              <!-- Tailles à ajouter -->
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

              <!-- Tailles à supprimer -->
              <div v-if="sizesToRemove.length > 0" class="bg-red-50 border border-red-200 rounded-lg p-4">
                <h4 class="text-sm font-medium text-red-800 mb-2">Tailles à supprimer :</h4>
                <div class="flex flex-wrap gap-2">
                  <span 
                    v-for="size in sizesToRemove" 
                    :key="size"
                    class="inline-flex items-center px-2 py-1 rounded-md text-xs font-medium bg-red-100 text-red-800"
                  >
                    {{ size }}
                  </span>
                </div>
              </div>
            </div>
          </div>
  
          <!-- Gestion des images -->
          <div class="bg-white/80 backdrop-blur-sm rounded-xl p-4 sm:p-6 border border-gray-100 shadow-sm">
            <div class="flex items-center space-x-3 mb-4 sm:mb-6">
              <div class="w-8 h-8 bg-gradient-to-br from-indigo-500 to-indigo-600 rounded-lg flex items-center justify-center">
                <ImageIcon class="w-4 h-4 text-white" />
              </div>
              <h3 class="text-lg sm:text-xl font-semibold text-gray-900">Images du produit</h3>
              <span class="text-sm text-gray-500">({{ editData.images.length }}/8)</span>
            </div>

            <!-- Ajout de la zone d'upload d'images -->
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

            <!-- Grille d'images avec possibilité de suppression -->
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
                
                <!-- Indicateur d'image principale -->
                <div 
                  v-if="index === 0"
                  class="absolute bottom-2 left-2 px-2 py-1 bg-orange-500 text-white text-xs rounded-md"
                >
                  Principal
                </div>
                
                <!-- Indicateur de progression d'upload pour nouvelles images -->
                <div v-if="image.uploading" class="absolute inset-0 bg-black/50 flex items-center justify-center rounded-lg">
                  <div class="text-white text-sm font-medium">{{ image.uploadProgress }}%</div>
                </div>
                
                <!-- Indicateur de succès d'upload -->
                <div v-if="image.uploaded && image.url" class="absolute top-2 left-2 bg-green-500 text-white rounded-full w-6 h-6 flex items-center justify-center">
                  <CheckIcon class="h-4 w-4" />
                </div>
                
                <!-- Bouton de suppression -->
                <button
                  type="button"
                  @click="removeImage(index)"
                  class="absolute -top-2 -right-2 w-6 h-6 bg-red-500 text-white rounded-full flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity hover:bg-red-600"
                >
                  <XIcon class="w-4 h-4" />
                </button>
                
                <!-- Boutons de réorganisation -->
                <div class="absolute bottom-2 right-2 flex space-x-1 opacity-0 group-hover:opacity-100 transition-opacity">
                  <button
                    v-if="index > 0"
                    type="button"
                    @click="moveImageUp(index)"
                    class="w-6 h-6 bg-blue-500 text-white rounded-full flex items-center justify-center hover:bg-blue-600"
                    title="Déplacer vers la gauche"
                  >
                    <ChevronLeftIcon class="w-3 h-3" />
                  </button>
                  <button
                    v-if="index < editData.images.length - 1"
                    type="button"
                    @click="moveImageDown(index)"
                    class="w-6 h-6 bg-blue-500 text-white rounded-full flex items-center justify-center hover:bg-blue-600"
                    title="Déplacer vers la droite"
                  >
                    <ChevronRightIcon class="w-3 h-3" />
                  </button>
                </div>
              </div>
            </div>

            <!-- Ajout des tableaux de tracking pour les images -->
            <div v-if="imagesToAdd.length > 0 || imagesToRemove.length > 0" class="space-y-4">
              <!-- Images à ajouter -->
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

              <!-- Images à supprimer -->
              <div v-if="imagesToRemove.length > 0" class="bg-red-50 border border-red-200 rounded-lg p-4">
                <h4 class="text-sm font-medium text-red-800 mb-3">Images à supprimer ({{ imagesToRemove.length }}) :</h4>
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
                    <div class="absolute -top-1 -right-1 w-4 h-4 bg-red-500 text-white rounded-full flex items-center justify-center">
                      <span class="text-xs">-</span>
                    </div>
                  </div>
                </div>
                <div class="mt-2 text-xs text-red-700">
                  <strong>Liens :</strong>
                  <ul class="list-disc list-inside mt-1 space-y-1">
                    <li v-for="(imageUrl, index) in imagesToRemove" :key="index" class="break-all">
                      {{ imageUrl }}
                    </li>
                  </ul>
                </div>
              </div>
            </div>

            <!-- Message si aucune image -->
            <div v-else-if="editData.images.length === 0" class="text-center py-8 text-gray-500">
              <ImageIcon class="w-12 h-12 mx-auto mb-4 text-gray-300" />
              <p>Aucune image ajoutée</p>
              <p class="text-sm">Ajoutez jusqu'à 8 images pour votre produit</p>
            </div>
          </div>
  
          <!-- Statut -->
          <div class="bg-white/80 backdrop-blur-sm rounded-xl p-4 sm:p-6 border border-gray-100 shadow-sm">
            <div class="flex items-center mb-6">
              <input 
                v-model="editData.is_active"
                id="is-active"
                type="checkbox"
                class="w-5 h-5 text-orange-600 bg-gray-100 border-gray-300 rounded focus:ring-orange-500 focus:ring-2"
              >
              <label for="is-active" class="ml-3 text-sm font-medium text-gray-700 flex items-center">
                <CheckCircleIcon class="w-4 h-4 text-green-600 mr-1" />
                Produit actif
              </label>
            </div>
          </div>
  
        </form>
      </div>
  
      <!-- Footer fixe avec navigation -->
      <div class="sticky bottom-0 bg-white/95 backdrop-blur-sm border-t border-gray-200 px-4 sm:px-6 py-4 sm:rounded-b-2xl z-50">
        <div class="flex gap-2 sm:gap-3">
          <button
            type="button"
            @click="closeModal"
            :disabled="isLoading"
            class="flex-1 px-4 py-3 sm:px-6 sm:py-3 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 transition-colors disabled:opacity-50 disabled:cursor-not-allowed"
          >
            Annuler
          </button>
          
          <button
            type="button"
            @click="handleSubmit"
            :disabled="isLoading || !canSubmit"
            class="flex-1 px-4 py-3 sm:px-6 sm:py-3 border border-transparent rounded-lg text-sm font-medium text-white bg-gradient-to-r from-orange-500 to-orange-600 hover:from-orange-600 hover:to-orange-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-orange-500 transition-all disabled:opacity-50 disabled:cursor-not-allowed"
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
import { ref, reactive, computed, onMounted, watch } from 'vue'
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
  ChevronRight as ChevronRightIcon
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
  description: '',
  category_id: '',
  subcategory_id: '',
  subsubcategory_id: '',
  subsubsubcategory_id: '',
  unit_price: null,
  stock: null,
  unit_type: 'quantity',
  colors: [],
  sizes: [],
  images: [],
  is_active: true,
  wholesale_price: null,
  wholesale_min_qty: null
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

const availableSizes = ref(['XS', 'S', 'M', 'L', 'XL', 'XXL', '36', '37', '38', '39', '40', '41', '42', '43', '44'])

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

const toggleSize = (size) => {
  const index = editData.value.sizes.indexOf(size)
  if (index > -1) {
    editData.value.sizes.splice(index, 1)
  } else {
    editData.value.sizes.push(size)
  }
}

const closeModal = () => {
  error.value = null
  emit('close')
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
    
    const uploadedUrls = await Promise.all(uploadPromises)
    
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
      description: editData.value.description,
      category_id: editData.value.category_id,
      subcategory_id: editData.value.subcategory_id,
      subsubcategory_id: editData.value.subsubcategory_id || null,
      subsubsubcategory_id: editData.value.subsubsubcategory_id || null,
      unit_price: parseFloat(editData.value.unit_price),
      stock: parseInt(editData.value.stock),
      unit_type: editData.value.unit_type,
      is_active: editData.value.is_active,
      colors: editData.value.colors,
      sizes: editData.value.sizes,
      images: finalImageUrls,
      image_add: newImages.value.filter(img => img.uploaded && img.url).map(img => img.url),
      image_remove: imagesToRemove.value,
      imagesToRemove: imagesToRemove.value,
      colorsToAdd: colorsToAdd.value,
      colorsToRemove: colorsToRemove.value,
      sizesToAdd: sizesToAdd.value,
      sizesToRemove: sizesToRemove.value
    }

    if (hasWholesalePrice.value && editData.value.wholesale_price) {
      formData.wholesale_price = parseFloat(editData.value.wholesale_price)
      formData.wholesale_min_qty = parseInt(editData.value.wholesale_min_qty)
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

const cloudinaryConfig = {
  cloudName: 'daaavha4z',
  uploadPreset: 'aliadjame',
  apiKey: 'wy0Eh-uA0Y0Ci3nyODix0b3WejA',
  uploadUrl: 'https://api.cloudinary.com/v1_1/daaavha4z/image/upload'
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
    
    const response = await fetch(cloudinaryConfig.uploadUrl, {
      method: 'POST',
      body: formData
    })
    
    const data = await response.json()
    
    if (data && data.secure_url) {
      image.url = data.secure_url
      image.uploading = false
      image.uploaded = true
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

watch(() => props.product, (newProduct) => {
  if (newProduct) {
    // Convertir les images existantes en format uniforme
    const existingImages = (newProduct.images || []).map(imageUrl => {
      if (typeof imageUrl === 'string') {
        return imageUrl
      }
      return imageUrl.url || imageUrl
    })
    
    editData.value = {
      id: newProduct.id,
      name: newProduct.name || '',
      description: newProduct.description || '',
      category_id: newProduct.category_id || '',
      subcategory_id: newProduct.subcategory_id || '',
      subsubcategory_id: newProduct.subsubcategory_id || '',
      subsubsubcategory_id: newProduct.subsubsubcategory_id || '',
      unit_price: newProduct.unit_price || null,
      stock: newProduct.stock || null,
      unit_type: newProduct.unit_type || 'quantity',
      colors: [...(newProduct.colors || [])],
      sizes: [...(newProduct.sizes || [])],
      images: existingImages, // Images existantes
      is_active: newProduct.is_active !== undefined ? newProduct.is_active : true,
      wholesale_price: newProduct.wholesale_price || null,
      wholesale_min_qty: newProduct.wholesale_min_qty || null
    }
    
    originalColors.value = [...(newProduct.colors || [])]
    originalSizes.value = [...(newProduct.sizes || [])]
    originalImages.value = [...existingImages]
    
    // Reset des tableaux de tracking
    imagesToRemove.value = []
    newImages.value = []
    
    // Vérifier si le produit a un prix de gros
    hasWholesalePrice.value = !!(newProduct.wholesale_price && newProduct.wholesale_price > 0)
  }
}, { immediate: true })

// Charger les catégories au montage du composant
onMounted(() => {
  fetchCategories()
})
</script>

<style scoped>
/* Couleurs personnalisées */
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
</style>
