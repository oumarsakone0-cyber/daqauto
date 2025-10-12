<template>
  <div class="min-h-screen bg-gray-50 relative overflow-hidden">
    <!-- Animation de fond luxueuse avec couleurs plus intenses -->
    <div class="absolute inset-0 overflow-hidden pointer-events-none z-0">
      <!-- Gradient animé principal -->
      <div class="absolute inset-0 bg-gradient-to-br from-gray-50 via-white to-gray-100"></div>
      
      <!-- Orbes flottants avec couleurs plus intenses -->
      <div class="absolute -top-20 -left-20 w-96 h-96 bg-gradient-to-br from-orange-200/60 to-orange-300/40 rounded-full blur-3xl animate-float-slow"></div>
      <div class="absolute top-1/4 -right-20 w-80 h-80 bg-gradient-to-br from-blue-200/50 to-indigo-200/35 rounded-full blur-3xl animate-float-reverse"></div>
      <div class="absolute -bottom-20 left-1/3 w-72 h-72 bg-gradient-to-br from-purple-200/45 to-pink-200/35 rounded-full blur-3xl animate-float-diagonal"></div>
      <div class="absolute top-1/2 right-1/4 w-64 h-64 bg-gradient-to-br from-emerald-200/40 to-teal-200/30 rounded-full blur-3xl animate-float-slow-reverse"></div>
      
      <!-- Particules plus visibles -->
      <div class="absolute top-1/3 left-1/4 w-3 h-3 bg-orange-400/70 rounded-full animate-pulse-slow shadow-lg"></div>
      <div class="absolute top-2/3 right-1/4 w-2.5 h-2.5 bg-blue-400/60 rounded-full animate-pulse-delayed shadow-lg"></div>
      <div class="absolute top-1/2 left-2/3 w-2 h-2 bg-purple-400/50 rounded-full animate-pulse-slow shadow-lg"></div>
      <div class="absolute top-1/4 right-1/3 w-2 h-2 bg-emerald-400/50 rounded-full animate-pulse-delayed-2 shadow-lg"></div>
      
      <!-- Lignes géométriques plus visibles -->
      <div class="absolute top-0 left-1/4 w-px h-40 bg-gradient-to-b from-transparent via-orange-300/40 to-transparent animate-slide-down"></div>
      <div class="absolute top-1/3 right-1/3 w-32 h-px bg-gradient-to-r from-transparent via-blue-300/35 to-transparent animate-slide-right"></div>
      <div class="absolute bottom-1/4 left-1/2 w-px h-32 bg-gradient-to-b from-transparent via-purple-300/30 to-transparent animate-slide-up"></div>
      
      <!-- Formes géométriques supplémentaires -->
      <div class="absolute top-3/4 left-1/6 w-16 h-16 bg-gradient-to-br from-orange-300/30 to-yellow-300/20 rotate-45 animate-rotate-slow"></div>
      <div class="absolute top-1/6 right-1/6 w-12 h-12 bg-gradient-to-br from-blue-300/25 to-cyan-300/20 rounded-lg animate-float-small"></div>
    </div>

    <!-- Container responsive avec largeur adaptative -->
    <div class="w-full max-w-[1650px] mx-auto px-4 sm:px-6 py-4 sm:py-8 relative z-10">
       <Navbar/>
      <!-- Breadcrumb -->
      <div class="flex items-center text-sm text-gray-500 mb-4 sm:mb-6">
        <router-link to="/" class="hover:text-gray-700">
          <Home class="w-4 h-4 sm:w-5 sm:h-5" />
        </router-link>
        <span class="mx-2">/</span>
        <span class="font-medium text-gray-700 truncate">Gestion des Catégories</span>
      </div>

      <!-- Header Section -->
      <div class="flex flex-col space-y-4 lg:flex-row lg:items-center lg:justify-between lg:space-y-0 mb-6 sm:mb-8">
        <div>
          <h1 class="text-2xl sm:text-3xl font-bold text-gray-900 mb-2">Gestion des Catégories</h1>
          <p class="text-sm sm:text-base text-gray-600">Organisez votre catalogue produits avec une hiérarchie complète</p>
        </div>
      </div>

      <!-- Stats Overview -->
      <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-6 mb-6 sm:mb-8" v-if="!dataLoading">
        <!-- Catégories -->
        <div class="bg-white overflow-hidden shadow-lg rounded-lg border border-gray-100 hover:shadow-xl transition-all duration-300">
          <div class="px-4 sm:px-6 py-4 sm:py-6">
            <div class="flex items-center mb-3 sm:mb-4">
              <div class="w-10 h-10 sm:w-12 sm:h-12 bg-gradient-to-br from-blue-200 to-blue-300 rounded-lg flex items-center justify-center mr-3 sm:mr-4 flex-shrink-0">
                <FolderIcon class="w-5 h-5 sm:w-6 sm:h-6 text-black" />
              </div>
              <div class="min-w-0">\
                <p class="text-xs sm:text-sm text-gray-600">Catégories</p>
                <p class="text-xl sm:text-2xl font-bold text-gray-900">{{ stats.total_categories || 0 }}</p>
              </div>
            </div>
            <div class="w-full bg-gray-200 rounded-full h-2">
              <div class="bg-gradient-to-r from-blue-500 to-blue-600 h-2 rounded-full transition-all duration-300" style="width: 85%"></div>
            </div>
          </div>
        </div>

        <!-- Sous-catégories -->
        <div class="bg-white overflow-hidden shadow-lg rounded-lg border border-gray-100 hover:shadow-xl transition-all duration-300">
          <div class="px-4 sm:px-6 py-4 sm:py-6">
            <div class="flex items-center mb-3 sm:mb-4">
              <div class="w-10 h-10 sm:w-12 sm:h-12 bg-gradient-to-br from-blue-200 to-blue-300 rounded-lg flex items-center justify-center mr-3 sm:mr-4 flex-shrink-0">
                <FolderOpenIcon class="w-5 h-5 sm:w-6 sm:h-6 text-black" />
              </div>
              <div class="min-w-0">
                <p class="text-xs sm:text-sm text-gray-600">Sous-catégories</p>
                <p class="text-xl sm:text-2xl font-bold text-gray-900">{{ stats.total_subcategories || 0 }}</p>
              </div>
            </div>
            <div class="w-full bg-gray-200 rounded-full h-2">
              <div class="bg-gradient-to-r from-blue-400 to-blue-500 h-2 rounded-full transition-all duration-300" style="width: 70%"></div>
            </div>
          </div>
        </div>

        <!-- Sous-sous-catégories -->
        <div class="bg-white overflow-hidden shadow-lg rounded-lg border border-gray-100 hover:shadow-xl transition-all duration-300">
          <div class="px-4 sm:px-6 py-4 sm:py-6">
            <div class="flex items-center mb-3 sm:mb-4">
              <div class="w-10 h-10 sm:w-12 sm:h-12 bg-gradient-to-br from-blue-200 to-blue-300 rounded-lg flex items-center justify-center mr-3 sm:mr-4 flex-shrink-0">
                <FolderTreeIcon class="w-5 h-5 sm:w-6 sm:h-6 text-black" />
              </div>
              <div class="min-w-0">
                <p class="text-xs sm:text-sm text-gray-600">Sous-sous-catégories</p>
                <p class="text-xl sm:text-2xl font-bold text-gray-900">{{ stats.total_sub_subcategories || 0 }}</p>
              </div>
            </div>
            <div class="w-full bg-gray-200 rounded-full h-2">
              <div class="bg-gradient-to-r from-blue-300 to-blue-400 h-2 rounded-full transition-all duration-300" style="width: 60%"></div>
            </div>
          </div>
        </div>

        <!-- Niveau 4 -->
        <div class="bg-white overflow-hidden shadow-lg rounded-lg border border-gray-100 hover:shadow-xl transition-all duration-300">
          <div class="px-4 sm:px-6 py-4 sm:py-6">
            <div class="flex items-center mb-3 sm:mb-4">
              <div class="w-10 h-10 sm:w-12 sm:h-12 bg-gradient-to-br from-blue-200 to-blue-300 rounded-lg flex items-center justify-center mr-3 sm:mr-4 flex-shrink-0">
                <LayersIcon class="w-5 h-5 sm:w-6 sm:h-6 text-black" />
              </div>
              <div class="min-w-0">
                <p class="text-xs sm:text-sm text-gray-600">Niveau 4</p>
                <p class="text-xl sm:text-2xl font-bold text-gray-900">{{ stats.total_level4_categories || 0 }}</p>
              </div>
            </div>
            <div class="w-full bg-gray-200 rounded-full h-2">
              <div class="bg-gradient-to-r from-blue-200 to-blue-300 h-2 rounded-full transition-all duration-300" style="width: 45%"></div>
            </div>
          </div>
        </div>
      </div>

      <!-- Loading State -->
      <div v-if="dataLoading" class="mb-6 sm:mb-8">
        <div class="bg-white rounded-lg p-6 sm:p-8 text-center shadow-lg">
          <div class="inline-block animate-spin rounded-full h-10 w-10 sm:h-12 sm:w-12 border-4 border-orange-500 border-t-transparent"></div>
          <p class="mt-4 text-gray-600 font-medium text-sm sm:text-base">Chargement des catégories...</p>
        </div>
      </div>

      <!-- Categories Management Tabs -->
      <div v-else class="bg-white shadow-lg rounded-lg overflow-hidden border border-gray-100">
        <!-- Filter Tabs responsive -->
        <div class="px-4 sm:px-6 py-3 sm:py-4 border-b border-gray-200 bg-gray-50">
          <div class="flex flex-wrap gap-1 sm:gap-2">
            <button 
              v-for="tab in tabs" 
              :key="tab.id"
              @click="changeTab(tab.id)"
              :class="[
                'px-2 sm:px-4 py-1.5 sm:py-2 rounded-lg transition-all duration-200 text-xs sm:text-sm font-medium flex items-center gap-2',
                activeTab === tab.id 
                  ? 'btn-degrade-orange' 
                  : 'btn-gray'
              ]"
            >
               <FolderIcon v-if="tab.id==='categories'" class="w-4 h-4 sm:w-6 sm:h-5 " />
               <FolderOpenIcon v-if="tab.id==='subcategories'" class="w-4 h-4 sm:w-5 sm:h-5 " />
               <FolderTreeIcon v-if="tab.id==='sub-subcategories'" class="w-4 h-4 sm:w-5 sm:h-5" />
              <LayersIcon v-if="tab.id==='level4'" class="w-4 h-4 sm:w-5 sm:h-5 " />
              <span class="hidden sm:inline">{{ tab.name }}</span>
              <span class="sm:hidden">{{ tab.name.split(' ')[0] }}</span>
              <span class="bg-white/20 px-1.5 py-0.5 rounded-full text-xs">{{ getTabCount(tab.id) }}</span>
            </button>
          </div>
        </div>

        <!-- Tab Content avec le même style que le contenu existant mais dans un container unifié -->
        <div class="p-4 sm:p-6">
          <!-- Categories Tab -->
          <div v-if="activeTab === 'categories'" class="category-section">
            <div class="section-header">
              <h3 class="section-title-small">Catégories Principales</h3>
              <button @click="openModal('category')" class="btn-degrade-orange">
                <PlusIcon/>
                Ajouter une catégorie
              </button>
            </div>

            <div v-if="categories.length === 0" class="empty-state ">
              <FolderIcon />
              <h3>Aucune catégorie</h3>
              <p>Commencez par créer votre première catégorie</p>
              <button @click="openModal('category')" class="btn-degrade-orange" >
                <PlusIcon />
                Créer une catégorie
              </button>
            </div>

            <div v-else class="categories-grid">
              <div 
                v-for="category in categories" 
                :key="category.id"
                class="category-card"
              >
                <div class="category-image">
                  <img :src="category.image_url || '/placeholder.svg?height=120&width=120'" :alt="category.name">
                </div>
                <div class="category-content">
                  <div class="category-header">
                    <div class="category-icon bg-orange">
                      <FolderIcon class="w-4 h-4 sm:w-5 sm:h-5" />
                    </div>
                    <h4 class="category-name">{{ category.name }}</h4>
                  </div>
                  <p class="category-description">{{ category.description || 'Aucune description' }}</p>
                  <div class="category-stats">
                    <span class="stat-item">{{ category.subcategories_count || 0 }} sous-catégories</span>
                    <span class="stat-item">{{ category.products_count || 0 }} produits</span>
                  </div>
                  <div class="category-actions">
                    <button @click="editCategory(category)" class="submit-btn">
                      <EditIcon />
                    </button>
                    <button @click="deleteCategory(category.id)" class="btn-deconnexion">
                      <TrashIcon />
                    </button>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- Subcategories Tab -->
          <div v-if="activeTab === 'subcategories'" class="category-section">
            <div class="section-header">
              <h3 class="section-title-small">Sous-catégories</h3>
              <div class="header-controls">
                <div class="sm:w-55 w-20" >
                  <select v-model="selectedCategoryFilter" class="input-style" @change="loadSubcategories">
                    <option value="">Toutes les catégories</option>
                    <option v-for="cat in categories" :key="cat.id" :value="cat.id">
                      {{ cat.name }}
                    </option>
                  </select>
                </div>
                <button @click="openModal('subcategory')" class="btn-degrade-orange">
                  <PlusIcon />
                  Ajouter une sous-catégorie
                </button>
              </div>
            </div>

            <div v-if="subcategories.length === 0" class="empty-state">
              <FolderOpenIcon />
              <h3>Aucune sous-catégorie</h3>
              <p>Créez des sous-catégories pour organiser vos produits</p>
              <button @click="openModal('subcategory')" class="btn-degrade-orange">
                <PlusIcon />
                Créer une sous-catégorie
              </button>
            </div>

            <div v-else class="subcategories-list">
              <div 
                v-for="subcategory in filteredSubcategories" 
                :key="subcategory.id"
                class="subcategory-item"
              >
                <div class="subcategory-info">
                  
                  <div class="subcategory-icon bg-orange">
                    <FolderOpenIcon class="w-4 h-4 sm:w-5 sm:h-5" />
                  </div>
                  <div class="subcategory-details">
                    <h4 class="subcategory-name">{{ subcategory.name }}</h4>
                    <p class="subcategory-parent">{{ subcategory.category_name }}</p>
                    <p class="subcategory-description">{{ subcategory.description || 'Aucune description' }}</p>
                  </div>
                </div>
                <div class="subcategory-image">
                  <img :src="subcategory.image_url || '/placeholder.svg?height=60&width=60'" :alt="subcategory.name">
                </div>
                <div class="subcategory-stats">
                  <span class="stat-number">{{ subcategory.sub_subcategories_count || 0 }}</span>
                  <span class="stat-label">Sous-sous-cat.</span>
                </div>
                <div class="subcategory-actions">
                  <button @click="editSubcategory(subcategory)" class="submit-btn">
                    <EditIcon />
                  </button>
                  <button @click="deleteSubcategory(subcategory.id)" class="btn-deconnexion">
                    <TrashIcon />
                  </button>
                </div>
              </div>
            </div>
          </div>

          <!-- Sub-subcategories Tab -->
          <div v-if="activeTab === 'sub-subcategories'" class="category-section">
            <div class="section-header">
              <h3 class="section-title-small">Sous-sous-catégories</h3>
              <div class="header-controls">
                <div>
                  <select v-model="selectedSubcategoryFilter" class="input-style" @change="loadSubSubcategories">
                    <option value="">Toutes les sous-catégories</option>
                    <option v-for="subcat in subcategories" :key="subcat.id" :value="subcat.id">
                      {{ subcat.name }}
                    </option>
                  </select>
                </div>
                <button @click="openModal('sub-subcategory')" class="btn-degrade-orange">
                  <PlusIcon />
                  Ajouter une sous-sous-catégorie
                </button>
              </div>
            </div>

            <div v-if="subSubcategories.length === 0" class="empty-state">
              <FolderTreeIcon />
              <h3>Aucune sous-sous-catégorie</h3>
              <p>Créez des sous-sous-catégories pour plus de précision</p>
              <button @click="openModal('sub-subcategory')" class="btn-degrade-orange">
                <PlusIcon />
                Créer une sous-sous-catégorie
              </button>
            </div>

            <div v-else class="sub-subcategories-grid">
              <div 
                v-for="subSubcategory in filteredSubSubcategories" 
                :key="subSubcategory.id"
                class="sub-subcategory-card"
              >
                <div class="sub-subcategory-image">
                  <img :src="subSubcategory.image_url || '/placeholder.svg?height=80&width=80'" :alt="subSubcategory.name">
                </div>
                <div class="sub-subcategory-content">
                  <div class="sub-subcategory-icon">
                    <FolderTreeIcon class="w-4 h-4 sm:w-5 sm:h-5" />
                  </div>
                  <h4 class="sub-subcategory-name">{{ subSubcategory.name }}</h4>
                  <p class="sub-subcategory-parent">{{ subSubcategory.subcategory_name }}</p>
                  <div class="sub-subcategory-actions">
                    <button @click="editSubSubcategory(subSubcategory)" class="submit-btn">
                      <EditIcon />
                    </button>
                    <button @click="deleteSubSubcategory(subSubcategory.id)" class="btn-deconnexion">
                      <TrashIcon />
                    </button>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- Level 4 Categories Tab -->
          <div v-if="activeTab === 'level4'" class="category-section">
            <div class="section-header">
              <h3 class="section-title-small">Catégories Niveau 4</h3>
              <div class="header-controls">
                <div class="sm:w-auto">
                  <select v-model="selectedSubSubcategoryFilter" class="input-style mr-5" @change="loadLevel4Categories">
                    <option value="">Toutes les sous-sous-catégories</option>
                    <option v-for="subSubcat in subSubcategories" :key="subSubcat.id" :value="subSubcat.id">
                      {{ subSubcat.name }}
                    </option>
                  </select>
                </div>
                <button @click="openModal('level4')" class="btn-degrade-orange">
                  <PlusIcon />
                  Ajouter niveau 4
                </button>
              </div>
            </div>

            <div v-if="level4Categories.length === 0" class="empty-state">
              <LayersIcon />
              <h3>Aucune catégorie niveau 4</h3>
              <p>Créez des catégories de niveau 4 pour une classification détaillée</p>
              <button @click="openModal('level4')" class="btn-degrade-orange">
                <PlusIcon />
                Créer niveau 4
              </button>
            </div>

            <div v-else class="level4-list">
              <div 
                v-for="level4 in filteredLevel4Categories" 
                :key="level4.id"
                class="level4-item"
              >
                <div class="level4-icon">
                  <component :is="level4.icon || 'LayersIcon'" />
                </div>
                <div class="level4-info">
                  <h4 class="level4-name">{{ level4.name }}</h4>
                  <p class="level4-parent">{{ level4.sub_subcategory_name }}</p>
                  <p class="level4-description">{{ level4.description || 'Aucune description' }}</p>
                </div>
                <div class="level4-image">
                  <img :src="level4.image_url || '/placeholder.svg?height=50&width=50'" :alt="level4.name">
                </div>
                <div class="level4-actions">
                  <button @click="editLevel4(level4)" class="submit-btn">
                    <EditIcon />
                  </button>
                  <button @click="deleteLevel4(level4.id)" class="btn-deconnexion">
                    <TrashIcon />
                  </button>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Modal for Add/Edit -->
    <div v-if="showModal" class="modal-overlay" @click="closeModal">
      <div class="modal-content" @click.stop>
        <div class="modal-header">
          <h3 class="modal-title">
            {{ isEditing ? 'Modifier' : 'Ajouter' }} {{ getModalTitle() }}
          </h3>
            <XIcon @click="closeModal" class="w-7 h-7 text-gray-700 cursor-pointer" />
        </div>

        <form @submit.prevent="submitForm" class="modal-form">
          <!-- Parent Selection (for subcategories and below) -->
          <div  v-if="modalType !== 'category'" class="form-group">
            <label class="form-label">{{ getParentLabel() }}</label>
            <select v-model="formData.parent_id" class=" input-style" required>
              <option value="">Sélectionner {{ getParentLabel().toLowerCase() }}</option>
              <option v-for="parent in getParentOptions()" :key="parent.id" :value="parent.id">
                {{ parent.name }}
              </option>
            </select>
          </div>

          <!-- Name -->
          <div class="form-group">
            <label class="form-label">Nom</label>
            <input 
              v-model="formData.name" 
              type="text" 
              class="input-style" 
              style="padding-left: 15px;"
              placeholder="Nom de la catégorie"
              required
            >
          </div>

          <!-- Description -->
          <div class="form-group">
            <label class="form-label">Description</label>
            <textarea 
              v-model="formData.description" 
              class="input-style" 
              style="padding-left: 15px;"
              placeholder="Description de la catégorie"
              rows="3"
            ></textarea>
          </div>

          <!-- Icon Selection -->
          <div class="form-group">
            <label class="form-label">Icône</label>
             <input 
              v-model="formData.icon" 
              type="text" 
              class="input-style" 
              style="padding-left: 15px;"
              placeholder="icône ici"
              required
            >
          </div>

          <!-- Image Upload -->
          <div class="form-group">
            <label class="form-label">Image</label>
            <div class="image-upload">
              <div class="image-preview">
                <img 
                  :src="formData.imagePreview || formData.image_url || '/placeholder.svg?height=100&width=100'" 
                  alt="Preview"
                >
                <div v-if="formData.imageUploading" class="upload-overlay">
                  <div class="upload-progress">
                    <div 
                      class="progress-fill" 
                      :style="{ width: formData.uploadProgress + '%' }"
                    ></div>
                  </div>
                  <span class="progress-text">{{ formData.uploadProgress }}%</span>
                </div>
              </div>
              <div class="upload-controls">
                <input 
                  ref="imageInput"
                  type="file" 
                  accept="image/*" 
                  @change="handleImageUpload"
                  class="file-input"
                  :disabled="formData.imageUploading"
                >
                <button 
                  type="button" 
                  @click="$refs.imageInput.click()" 
                  class="upload-btn btn-deconnexion"
                  :disabled="formData.imageUploading"
                >
                  <UploadIcon v-if="!formData.imageUploading" />
                  <LoaderIcon v-else class="animate-spin" />
                  {{ formData.imageUploading ? 'Téléchargement...' : 'Choisir une image' }}
                </button>
                <button 
                  v-if="formData.image_url && !formData.imageUploading" 
                  type="button" 
                  @click="removeImage" 
                  class="remove-btn btn-deconnexion"
                >
                  <TrashIcon />
                  Supprimer
                </button>
              </div>
            </div>
          </div>

          <!-- Form Actions -->
          <div class="form-actions">
            <button type="button" @click="closeModal" class="btn-gray">
              Annuler
            </button>
            <button type="submit" class="btn-degrade-orange" :disabled="loading">
              <LoaderIcon v-if="loading" class="animate-spin" />
              {{ isEditing ? 'Modifier' : 'Ajouter' }}
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script setup>
import Navbar from '../boutiques/Navbar.vue'
import { ref, computed, onMounted } from 'vue'
import axios from 'axios'
// Icônes Lucide Vue Next
import { 
 
  Edit as EditIcon,
  X as XIcon,
  Home,
  FolderIcon,
  FolderOpenIcon,
  FolderTreeIcon,
  LayersIcon,
  PlusIcon,
  TrashIcon,
 
} from 'lucide-vue-next'

// Cloudinary configuration
const cloudinaryConfig = {
  uploadUrl: 'https://api.cloudinary.com/v1_1/dqk65objc/image/upload',
  uploadPreset: 'sadeal',
  apiKey: '784574272958761'
}

// API Base URL - Mise à jour pour utiliser categorie.php
const API_BASE_URL = 'https://sastock.com/api_adjame'

// Reactive data
const activeTab = ref('categories')
const showModal = ref(false)
const modalType = ref('')
const isEditing = ref(false)
const loading = ref(false)
const dataLoading = ref(true)

// Filters
const selectedCategoryFilter = ref('')
const selectedSubcategoryFilter = ref('')
const selectedSubSubcategoryFilter = ref('')

// Form data
const formData = ref({
  name: '',
  description: '',
  icon: '',
  image_url: '',
  imagePreview: '',
  imageUploading: false,
  imageUploaded: false,
  uploadProgress: 0,
  parent_id: ''
})

// Stats
const stats = ref({
  total_categories: 0,
  total_subcategories: 0,
  total_sub_subcategories: 0,
  total_level4_categories: 0
})

// Data arrays
const categories = ref([])
const subcategories = ref([])
const subSubcategories = ref([])
const level4Categories = ref([])

// Tabs configuration
const tabs = ref([
  { id: 'categories', name: 'Catégories', icon: 'FolderIcon' },
  { id: 'subcategories', name: 'Sous-catégories', icon: 'FolderOpenIcon' },
  { id: 'sub-subcategories', name: 'Sous-sous-catégories', icon: 'FolderTreeIcon' },
  { id: 'level4', name: 'Niveau 4', icon: 'LayersIcon' }
])

// Available icons
const availableIcons = ref([
  { name: 'FolderIcon', component: 'FolderIcon' },
  { name: 'SmartphoneIcon', component: 'SmartphoneIcon' },
  { name: 'ShirtIcon', component: 'ShirtIcon' },
  { name: 'HomeIcon', component: 'HomeIcon' },
  { name: 'CarIcon', component: 'CarIcon' },
  { name: 'BookIcon', component: 'BookIcon' },
  { name: 'GamepadIcon', component: 'GamepadIcon' },
  { name: 'HeartIcon', component: 'HeartIcon' }
])

// Computed properties
const filteredSubcategories = computed(() => {
  if (!selectedCategoryFilter.value) return subcategories.value
  return subcategories.value.filter(sub => sub.category_id == selectedCategoryFilter.value)
})

const filteredSubSubcategories = computed(() => {
  if (!selectedSubcategoryFilter.value) return subSubcategories.value
  return subSubcategories.value.filter(subSub => subSub.subcategory_id == selectedSubcategoryFilter.value)
})

const filteredLevel4Categories = computed(() => {
  if (!selectedSubSubcategoryFilter.value) return level4Categories.value
  return level4Categories.value.filter(level4 => level4.sub_subcategory_id == selectedSubSubcategoryFilter.value)
})

// Helper function pour convertir les icônes
const getValidIconName = (iconValue) => {
  // Si c'est déjà un nom de composant valide, le retourner
  if (typeof iconValue === 'string' && iconValue.match(/^[A-Z][a-zA-Z]*Icon$/)) {
    return iconValue
  }
  
  // Mapper les emojis vers des noms de composants
  const emojiToIcon = {
    '🐶': 'HeartIcon',
    '📱': 'SmartphoneIcon', 
    '👕': 'ShirtIcon',
    '🏠': 'HomeIcon',
    '🚗': 'CarIcon',
    '📚': 'BookIcon',
    '🎮': 'GamepadIcon',
    '❤️': 'HeartIcon'
  }
  
  // Retourner l'icône correspondante ou une icône par défaut
  return emojiToIcon[iconValue] || 'FolderIcon'
}

// API Methods - Mise à jour pour utiliser categorie.php
const loadStats = async () => {
  try {
    console.log('🔄 Chargement des statistiques...')
    const response = await axios.get(`${API_BASE_URL}/categorie.php?action=stats&_=${Date.now()}`)
    
    if (response.data.success) {
      stats.value = response.data.data
      console.log('✅ Statistiques chargées:', stats.value)
    } else {
      console.error('❌ Erreur lors du chargement des statistiques:', response.data.error)
      alert('Erreur lors du chargement des statistiques: ' + (response.data.error || 'Erreur inconnue'))
    }
  } catch (error) {
    console.error('❌ Erreur lors du chargement des statistiques:', error)
    alert('Erreur lors du chargement des statistiques: ' + error.message)
  }
}
  
  const loadCategories = async () => {
    try {
      console.log('🔄 Chargement des catégories...')
      const response = await axios.get(`${API_BASE_URL}/categorie.php?action=categories&limit=100&_=${Date.now()}`)
    
      console.log('📡 Réponse API categories:', response.data)
    
      if (response.data && response.data.success) {
        // Transformer les données pour correspondre à la structure attendue
        const rawCategories = response.data.data || []
        categories.value = rawCategories.map(category => ({
          ...category,
          image_url: category.image || category.image_url, // Adapter le nom du champ
          products_count: category.product_count || 0, // Adapter le nom du champ
          subcategories_count: category.subcategories ? category.subcategories.length : 0, // Calculer le nombre
          // Convertir les emojis en noms de composants valides ou utiliser une icône par défaut
          icon: getValidIconName(category.icon)
        }))
        console.log('✅ Catégories transformées:', categories.value.length)
      } else {
        console.error('❌ Réponse API invalide pour les catégories:', response.data)
        categories.value = []
      }
    } catch (error) {
      console.error('❌ Erreur lors du chargement des catégories:', error)
      categories.value = []
      // Ne pas bloquer l'interface, juste logger l'erreur
    }
  }
  
  const loadSubcategories = async () => {
    try {
      console.log('🔄 Chargement des sous-catégories...')
      const url = selectedCategoryFilter.value 
        ? `${API_BASE_URL}/categorie.php?action=subcategories&category_id=${selectedCategoryFilter.value}&limit=1000&_=${Date.now()}`
        : `${API_BASE_URL}/categorie.php?action=subcategories&limit=1000&_=${Date.now()}`
    
      const response = await axios.get(url)
    
      console.log('📡 Réponse API subcategories:', response.data)
    
      if (response.data && response.data.success) {
        // Transformer les données pour correspondre à la structure attendue
        const rawSubcategories = response.data.data || []
        subcategories.value = rawSubcategories.map(subcategory => ({
          ...subcategory,
          image_url: subcategory.image || subcategory.image_url,
          products_count: subcategory.product_count || 0,
          sub_subcategories_count: subcategory.sub_subcategories ? subcategory.sub_subcategories.length : 0,
          category_name: subcategory.category_name || 'Catégorie inconnue',
          icon: getValidIconName(subcategory.icon)
        }))
        console.log('✅ Sous-catégories transformées:', subcategories.value.length)
      } else {
        console.error('❌ Réponse API invalide pour les sous-catégories:', response.data)
        subcategories.value = []
      }
    } catch (error) {
      console.error('❌ Erreur lors du chargement des sous-catégories:', error)
      subcategories.value = []
      // Ne pas bloquer l'interface, juste logger l'erreur
    }
  }
  
  const loadSubSubcategories = async () => {
    try {
      console.log('🔄 Chargement des sous-sous-catégories...')
      const url = selectedSubcategoryFilter.value 
        ? `${API_BASE_URL}/categorie.php?action=sub_subcategories&subcategory_id=${selectedSubcategoryFilter.value}&limit=1000&_=${Date.now()}`
        : `${API_BASE_URL}/categorie.php?action=sub_subcategories&limit=1000&_=${Date.now()}`
    
      const response = await axios.get(url)
    
      console.log('📡 Réponse API sub_subcategories:', response.data)
    
      if (response.data && response.data.success) {
        // Transformer les données pour correspondre à la structure attendue
        const rawSubSubcategories = response.data.data || []
        subSubcategories.value = rawSubSubcategories.map(subSubcategory => ({
          ...subSubcategory,
          image_url: subSubcategory.image || subSubcategory.image_url,
          products_count: subSubcategory.product_count || 0,
          subcategory_name: subSubcategory.subcategory_name || 'Sous-catégorie inconnue',
          icon: getValidIconName(subSubcategory.icon)
        }))
        console.log('✅ Sous-sous-catégories transformées:', subSubcategories.value.length)
      } else {
        console.error('❌ Réponse API invalide pour les sous-sous-catégories:', response.data)
        subSubcategories.value = []
      }
    } catch (error) {
      console.error('❌ Erreur lors du chargement des sous-sous-catégories:', error)
      subSubcategories.value = []
      // Ne pas bloquer l'interface, juste logger l'erreur
    }
  }
  
  const loadLevel4Categories = async () => {
    try {
      console.log('🔄 Chargement des catégories niveau 4...')
      const url = selectedSubSubcategoryFilter.value 
        ? `${API_BASE_URL}/categorie.php?action=sub_sub_subcategories&sub_subcategory_id=${selectedSubSubcategoryFilter.value}&limit=1000&_=${Date.now()}`
        : `${API_BASE_URL}/categorie.php?action=sub_sub_subcategories&limit=1000&_=${Date.now()}`
    
      const response = await axios.get(url)
    
      console.log('📡 Réponse API level4:', response.data)
    
      if (response.data && response.data.success) {
        // Transformer les données pour correspondre à la structure attendue
        const rawLevel4Categories = response.data.data || []
        level4Categories.value = rawLevel4Categories.map(level4 => ({
          ...level4,
          image_url: level4.image || level4.image_url,
          products_count: level4.product_count || 0,
          sub_subcategory_name: level4.sub_subcategory_name || 'Sous-sous-catégorie inconnue',
          icon: getValidIconName(level4.icon)
        }))
        console.log('✅ Catégories niveau 4 transformées:', level4Categories.value.length)
      } else {
        console.error('❌ Réponse API invalide pour les catégories niveau 4:', response.data)
        level4Categories.value = []
      }
    } catch (error) {
      console.error('❌ Erreur lors du chargement des catégories niveau 4:', error)
      level4Categories.value = []
      // Ne pas bloquer l'interface, juste logger l'erreur
    }
  }
  
  const loadAllData = async () => {
    dataLoading.value = true
    console.log('🚀 Début du chargement de toutes les données...')
    
    try {
      // Charger les statistiques en premier
      await loadStats()
      
      // Charger les données principales en parallèle
      const promises = [
        loadCategories(),
        loadSubcategories(), 
        loadSubSubcategories(),
        loadLevel4Categories()
      ]
      
      // Attendre que toutes les promesses se terminent (même en cas d'erreur)
      const results = await Promise.allSettled(promises)
      
      // Log des résultats pour debug
      results.forEach((result, index) => {
        const names = ['Categories', 'Subcategories', 'SubSubcategories', 'Level4Categories']
        if (result.status === 'rejected') {
          console.error(`❌ Erreur lors du chargement de ${names[index]}:`, result.reason)
        } else {
          console.log(`✅ ${names[index]} chargées avec succès`)
        }
      })
      
      console.log('✅ Chargement de toutes les données terminé')
    } catch (error) {
      console.error('❌ Erreur générale lors du chargement des données:', error)
    } finally {
      // S'assurer que le loading se termine toujours
      dataLoading.value = false
      console.log('🏁 dataLoading mis à false')
    }
  }
  
  // Methods
  const getTabCount = (tabId) => {
    switch (tabId) {
      case 'categories': return categories.value.length
      case 'subcategories': return subcategories.value.length
      case 'sub-subcategories': return subSubcategories.value.length
      case 'level4': return level4Categories.value.length
      default: return 0
    }
  }
  
  const changeTab = async (tabId) => {
    activeTab.value = tabId
    
    // Charger les données spécifiques à l'onglet si nécessaire
    switch (tabId) {
      case 'subcategories':
        if (subcategories.value.length === 0) {
          await loadSubcategories()
        }
        break
      case 'sub-subcategories':
        if (subSubcategories.value.length === 0) {
          await loadSubSubcategories()
        }
        break
      case 'level4':
        if (level4Categories.value.length === 0) {
          await loadLevel4Categories()
        }
        break
    }
  }
  
  const openModal = (type) => {
    modalType.value = type
    isEditing.value = false
    showModal.value = true
    resetForm()
  }
  
  const closeModal = () => {
    showModal.value = false
    resetForm()
  }
  
  const resetForm = () => {
    formData.value = {
      name: '',
      description: '',
      icon: '',
      image_url: '',
      imagePreview: '',
      imageUploading: false,
      imageUploaded: false,
      uploadProgress: 0,
      parent_id: ''
    }
  }
  
  const getModalTitle = () => {
    const titles = {
      'category': 'une catégorie',
      'subcategory': 'une sous-catégorie',
      'sub-subcategory': 'une sous-sous-catégorie',
      'level4': 'une catégorie niveau 4'
    }
    return titles[modalType.value] || ''
  }
  
  const getParentLabel = () => {
    const labels = {
      'subcategory': 'Catégorie parente',
      'sub-subcategory': 'Sous-catégorie parente',
      'level4': 'Sous-sous-catégorie parente'
    }
    return labels[modalType.value] || ''
  }
  
  const getParentOptions = () => {
    switch (modalType.value) {
      case 'subcategory': return categories.value
      case 'sub-subcategory': return subcategories.value
      case 'level4': return subSubcategories.value
      default: return []
    }
  }
  
  const imageInput = ref(null)
  
  const handleImageUpload = async (event) => {
    const file = event.target.files[0]
    if (!file) return
    
    // Validate file type
    if (!file.type.startsWith('image/')) {
      alert('Veuillez sélectionner un fichier image valide')
      return
    }
    
    // Validate file size (max 5MB)
    if (file.size > 5 * 1024 * 1024) {
      alert('La taille de l\'image ne doit pas dépasser 5MB')
      return
    }
    
    try {
      // Show loading state
      formData.value.imageUploading = true
      formData.value.uploadProgress = 0
      
      // Create preview
      const reader = new FileReader()
      reader.onload = (e) => {
        formData.value.imagePreview = e.target.result
      }
      reader.readAsDataURL(file)
      
      // Upload to Cloudinary
      const uploadedUrl = await uploadImageToCloudinary(file)
      
      if (uploadedUrl) {
        formData.value.image_url = uploadedUrl
        formData.value.imageUploaded = true
      }
    } catch (error) {
      console.error('Erreur lors du téléchargement:', error)
      alert('Erreur lors du téléchargement de l\'image')
    } finally {
      formData.value.imageUploading = false
    }
  }
  
  const uploadImageToCloudinary = async (file) => {
    try {
      const fileName = `category_${Date.now()}_${file.name.replace(/\s+/g, '_')}`
      
      const formData = new FormData()
      formData.append('file', file)
      formData.append('upload_preset', cloudinaryConfig.uploadPreset)
      formData.append('api_key', cloudinaryConfig.apiKey)
      formData.append('public_id', fileName)
      formData.append('folder', `adjame/categories_${Date.now()}`)
      
      const response = await axios.post(cloudinaryConfig.uploadUrl, formData, {
        headers: {
          'Content-Type': 'multipart/form-data'
        },
        onUploadProgress: (progressEvent) => {
          const percentCompleted = Math.round((progressEvent.loaded * 100) / progressEvent.total)
          formData.value.uploadProgress = percentCompleted
        }
      })
      
      if (response.data && response.data.secure_url) {
        return response.data.secure_url
      } else {
        throw new Error('Réponse Cloudinary invalide')
      }
    } catch (error) {
      console.error('Erreur lors du téléchargement Cloudinary:', error)
      throw error
    }
  }
  
  const removeImage = () => {
    formData.value.image_url = ''
    formData.value.imagePreview = ''
    formData.value.imageUploaded = false
    if (imageInput.value) {
      imageInput.value.value = ''
    }
  }
  
  // Mise à jour de submitForm pour utiliser les nouvelles actions de categorie.php
  const submitForm = async () => {
    // Validation côté client
    if (!formData.value.name || formData.value.name.trim().length < 2) {
      alert('Le nom doit contenir au moins 2 caractères')
      return
    }

    if (modalType.value !== 'category' && !formData.value.parent_id) {
      alert('Veuillez sélectionner une catégorie parente')
      return
    }

    loading.value = true
    try {
      const apiData = {
        name: formData.value.name,
        description: formData.value.description,
        icon: formData.value.icon,
        image: formData.value.image_url
      }
  
      // Ajouter l'ID parent selon le type
      if (modalType.value !== 'category') {
        switch (modalType.value) {
          case 'subcategory':
            apiData.category_id = formData.value.parent_id
            break
          case 'sub-subcategory':
            apiData.subcategory_id = formData.value.parent_id
            break
          case 'level4':
            apiData.sub_subcategory_id = formData.value.parent_id
            break
        }
      }
  
      let response
      const actionMap = {
        'category': 'create_category',
        'subcategory': 'create_subcategory',
        'sub-subcategory': 'create_sub_subcategory',
        'level4': 'create_sub_sub_subcategory'
      }
  
      if (isEditing.value) {
        // Mode édition
        const updateActionMap = {
          'category': 'update_category',
          'subcategory': 'update_subcategory',
          'sub-subcategory': 'update_sub_subcategory',
          'level4': 'update_sub_sub_subcategory'
        }
        
        response = await axios.put(
          `${API_BASE_URL}/categorie.php?action=${updateActionMap[modalType.value]}&id=${formData.value.id}&_=${Date.now()}`,
          apiData
        )
      } else {
        // Mode création
        response = await axios.post(
          `${API_BASE_URL}/categorie.php?action=${actionMap[modalType.value]}&_=${Date.now()}`,
          apiData
        )
      }
  
      if (response.data.success) {
        alert(isEditing.value ? 'Modifié avec succès!' : 'Créé avec succès!')
        closeModal()
        
        // Recharger les données
        await loadAllData()
      } else {
        alert('Erreur: ' + (response.data.error || 'Erreur inconnue'))
      }
    } catch (error) {
      console.error('Error submitting form:', error)
      alert('Erreur lors de la sauvegarde: ' + (error.response?.data?.error || error.message))
    } finally {
      loading.value = false
    }
  }
  
  // Edit functions
  const editCategory = (category) => {
    modalType.value = 'category'
    isEditing.value = true
    formData.value = { 
      ...category,
      image_url: category.image_url || '',
      imagePreview: '',
      imageUploading: false,
      imageUploaded: false,
      uploadProgress: 0
    }
    showModal.value = true
  }
  
  const editSubcategory = (subcategory) => {
    modalType.value = 'subcategory'
    isEditing.value = true
    formData.value = { 
      ...subcategory, 
      parent_id: subcategory.category_id,
      image_url: subcategory.image_url || '',
      imagePreview: '',
      imageUploading: false,
      imageUploaded: false,
      uploadProgress: 0
    }
    showModal.value = true
  }
  
  const editSubSubcategory = (subSubcategory) => {
    modalType.value = 'sub-subcategory'
    isEditing.value = true
    formData.value = { 
      ...subSubcategory, 
      parent_id: subSubcategory.subcategory_id,
      image_url: subSubcategory.image_url || '',
      imagePreview: '',
      imageUploading: false,
      imageUploaded: false,
      uploadProgress: 0
    }
    showModal.value = true
  }
  
  const editLevel4 = (level4) => {
    modalType.value = 'level4'
    isEditing.value = true
    formData.value = { 
      ...level4, 
      parent_id: level4.sub_subcategory_id,
      image: level4.image_url || '',
      imagePreview: '',
      imageUploading: false,
      imageUploaded: false,
      uploadProgress: 0
    }
    showModal.value = true
  }
  
  // Delete functions - Mise à jour pour utiliser categorie.php
  const deleteCategory = async (id) => {
    if (confirm('Êtes-vous sûr de vouloir supprimer cette catégorie ?')) {
      try {
        const response = await axios.delete(`${API_BASE_URL}/categorie.php?action=delete_category&id=${id}&_=${Date.now()}`)
        if (response.data.success) {
          alert('Catégorie supprimée avec succès!')
          await loadAllData()
        } else {
          alert('Erreur: ' + (response.data.error || 'Erreur inconnue lors de la suppression'))
        }
      } catch (error) {
        console.error('Erreur lors de la suppression:', error)
        alert('Erreur lors de la suppression: ' + (error.response?.data?.error || error.message))
      }
    }
  }
  
  const deleteSubcategory = async (id) => {
    if (confirm('Êtes-vous sûr de vouloir supprimer cette sous-catégorie ?')) {
      try {
        const response = await axios.delete(`${API_BASE_URL}/categorie.php?action=delete_subcategory&id=${id}&_=${Date.now()}`)
        if (response.data.success) {
          alert('Sous-catégorie supprimée avec succès!')
          await loadAllData()
        } else {
          alert('Erreur: ' + (response.data.error || 'Erreur inconnue lors de la suppression'))
        }
      } catch (error) {
        console.error('Erreur lors de la suppression:', error)
        alert('Erreur lors de la suppression: ' + (error.response?.data?.error || error.message))
      }
    }
  }
  
  const deleteSubSubcategory = async (id) => {
    if (confirm('Êtes-vous sûr de vouloir supprimer cette sous-sous-catégorie ?')) {
      try {
        const response = await axios.delete(`${API_BASE_URL}/categorie.php?action=delete_sub_subcategory&id=${id}&_=${Date.now()}`)
        if (response.data.success) {
          alert('Sous-sous-catégorie supprimée avec succès!')
          await loadAllData()
        } else {
          alert('Erreur: ' + (response.data.error || 'Erreur inconnue lors de la suppression'))
        }
      } catch (error) {
        console.error('Erreur lors de la suppression:', error)
        alert('Erreur lors de la suppression: ' + (error.response?.data?.error || error.message))
      }
    }
  }
  
  const deleteLevel4 = async (id) => {
    if (confirm('Êtes-vous sûr de vouloir supprimer cette catégorie niveau 4 ?')) {
      try {
        const response = await axios.delete(`${API_BASE_URL}/categorie.php?action=delete_sub_sub_subcategory&id=${id}&_=${Date.now()}`)
        if (response.data.success) {
          alert('Catégorie niveau 4 supprimée avec succès!')
          await loadAllData()
        } else {
          alert('Erreur: ' + (response.data.error || 'Erreur inconnue lors de la suppression'))
        }
      } catch (error) {
        console.error('Erreur lors de la suppression:', error)
        alert('Erreur lors de la suppression: ' + (error.response?.data?.error || error.message))
      }
    }
  }
  
  onMounted(() => {
    console.log('🚀 Initialisation de la page de gestion des catégories')
    loadAllData()
  })
</script>


<style scoped>

/* Empty State */
.empty-state {
  text-align: center;
  padding: 60px 20px;
  color: #64748b;
}

.empty-state svg {
  width: 64px;
  height: 64px;
  margin-bottom: 24px;
  color: #cbd5e1;
}

.empty-state h3 {
  font-size: 20px;
  font-weight: 600;
  margin-bottom: 8px;
  color: #475569;
}

.empty-state p {
  font-size: 16px;
  margin-bottom: 24px;
  color: #64748b;
}

/* Page Header */
.page-header {
  text-align: center;
  margin-bottom: 32px;
}

.section-title {
  font-size: 36px;
  font-weight: 700;
  margin-bottom: 8px;
  color: #1e293b;
}

.section-subtitle {
  font-size: 18px;
  color: #64748b;
  margin: 0;
}

/* Stats Grid */
.stats-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
  gap: 24px;
  margin-bottom: 32px;
}

.stat-card {
  padding: 24px;
  border-radius: 16px;
  background: rgba(255, 255, 255, 0.9);
  backdrop-filter: blur(20px);
  border: 1px solid rgba(255, 255, 255, 0.3);
  transition: all 0.3s ease;
  box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
}

.stat-card:hover {
  transform: translateY(-4px);
  box-shadow: 0 12px 35px rgba(0, 0, 0, 0.15);
}

.stat-card.primary {
  background: linear-gradient(135deg, rgba(59, 130, 246, 0.1) 0%, rgba(59, 130, 246, 0.05) 100%);
  border-color: rgba(59, 130, 246, 0.3);
}

.stat-card.success {
  background: linear-gradient(135deg, rgba(34, 197, 94, 0.1) 0%, rgba(34, 197, 94, 0.05) 100%);
  border-color: rgba(34, 197, 94, 0.3);
}

.stat-card.warning {
  background: linear-gradient(135deg, rgba(245, 158, 11, 0.1) 0%, rgba(245, 158, 11, 0.05) 100%);
  border-color: rgba(245, 158, 11, 0.3);
}

.stat-card.info {
  background: linear-gradient(135deg, rgba(102, 126, 234, 0.1) 0%, rgba(102, 126, 234, 0.05) 100%);
  border-color: rgba(102, 126, 234, 0.3);
}

.stat-content {
  display: flex;
  align-items: center;
  justify-content: space-between;
}

.stat-title {
  font-size: 14px;
  font-weight: 500;
  color: #64748b;
  margin: 0 0 8px 0;
}

.stat-value {
  font-size: 32px;
  font-weight: 700;
  color: #1e293b;
}

.stat-icon {
  padding: 12px;
  border-radius: 12px;
  background: linear-gradient(135deg, #667eea, #764ba2);
  color: white;
  display: flex;
  align-items: center;
  justify-content: center;
}

/* Tabs */
.tabs-container {
  background: rgba(255, 255, 255, 0.95);
  backdrop-filter: blur(20px);
  border-radius: 20px;
  border: 1px solid rgba(255, 255, 255, 0.3);
  box-shadow: 0 12px 35px rgba(0, 0, 0, 0.1);
  overflow: hidden;
}

.tabs-header {
  display: flex;
  background: rgba(0, 0, 0, 0.02);
  border-bottom: 1px solid rgba(0, 0, 0, 0.05);
}

.tab-btn {
  flex: 1;
  padding: 16px 24px;
  border: none;
  background: transparent;
  color: #64748b;
  font-size: 14px;
  font-weight: 500;
  cursor: pointer;
  transition: all 0.3s ease;
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 8px;
  position: relative;
}

.tab-btn:hover {
  background: rgba(102, 126, 234, 0.05);
  color: #667eea;
}

.tab-btn.active {
  background: linear-gradient(135deg, #667eea, #764ba2);
  color: white;
}

.tab-count {
  background: rgba(255, 255, 255, 0.2);
  padding: 2px 8px;
  border-radius: 12px;
  font-size: 12px;
  font-weight: 600;
}

.tab-btn.active .tab-count {
  background: rgba(255, 255, 255, 0.3);
}

.tab-content {
  padding: 32px;
}

/* Section Headers */
.section-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 24px;
}

.section-title-small {
  font-size: 24px;
  font-weight: 600;
  color: #1e293b;
  margin: 0;
}

.header-controls {
  display: flex;
  align-items: center;
  gap: 16px;
}


/* Categories Grid */
.categories-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
  gap: 24px;
}

.category-card {
  background: white;
  border-radius: 16px;
  overflow: hidden;
  transition: all 0.3s ease;
  box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
}

.category-card:hover {
  transform: translateY(-4px);
  box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
}

.category-image {
  height: 120px;
  overflow: hidden;
}

.category-image img {
  width: 100%;
  height: 100%;
  object-fit: cover;
}

.category-content {
  padding: 20px;
}

.category-header {
  display: flex;
  align-items: center;
  gap: 12px;
  margin-bottom: 12px;
}

.category-icon {
  width: 30px;
  height: 30px;
  border-radius: 10px;
  /* background: linear-gradient(135deg, #667eea, #764ba2); */
  color: white;
  display: flex;
  align-items: center;
  justify-content: center;
}

.category-name {
  font-size: 18px;
  font-weight: 600;
  color: #1e293b;
  margin: 0;
}

.category-description {
  font-size: 14px;
  color: #64748b;
  margin: 0 0 16px 0;
  line-height: 1.5;
}

.category-stats {
  display: flex;
  gap: 16px;
  margin-bottom: 16px;
}

.stat-item {
  font-size: 12px;
  color: #64748b;
  background: rgba(0, 0, 0, 0.05);
  padding: 4px 8px;
  border-radius: 6px;
}

.category-actions {
  display: flex;
  gap: 8px;
  justify-content: flex-end;
}

.action-btn {
  width: 32px;
  height: 32px;
  border: none;
  border-radius: 8px;
  cursor: pointer;
  transition: all 0.3s ease;
  display: flex;
  align-items: center;
  justify-content: center;
}

.action-btn.edit {
  background: rgba(59, 130, 246, 0.1);
  color: #3b82f6;
}

.action-btn.edit:hover {
  background: rgba(59, 130, 246, 0.2);
}

.action-btn.delete {
  background: rgba(239, 68, 68, 0.1);
  color: #ef4444;
}

.action-btn.delete:hover {
  background: rgba(239, 68, 68, 0.2);
}

/* Subcategories List */
.subcategories-list {
  display: flex;
  flex-direction: column;
  gap: 16px;
}

.subcategory-item {
  display: flex;
  align-items: center;
  gap: 20px;
  padding: 20px;
  background: white;
  border-radius: 12px;
  box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
  transition: all 0.3s ease;
}

.subcategory-item:hover {
  transform: translateX(4px);
  box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
}

.subcategory-info {
  display: flex;
  align-items: center;
  gap: 16px;
  flex: 1;
}

.subcategory-icon {
  width: 48px;
  height: 48px;
  border-radius: 12px;
  color: white;
  display: flex;
  align-items: center;
  justify-content: center;
}

.subcategory-name {
  font-size: 16px;
  font-weight: 600;
  color: #1e293b;
  margin: 0 0 4px 0;
}

.subcategory-parent {
  font-size: 12px;
  color: #667eea;
  margin: 0 0 4px 0;
  font-weight: 500;
}

.subcategory-description {
  font-size: 14px;
  color: #64748b;
  margin: 0;
}

.subcategory-image {
  width: 60px;
  height: 60px;
  border-radius: 8px;
  overflow: hidden;
}

.subcategory-image img {
  width: 100%;
  height: 100%;
  object-fit: cover;
}

.subcategory-stats {
  text-align: center;
  min-width: 80px;
}

.stat-number {
  font-size: 20px;
  font-weight: 700;
  color: #1e293b;
  display: block;
}

.stat-label {
  font-size: 11px;
  color: #64748b;
  text-transform: uppercase;
}

.subcategory-actions {
  display: flex;
  gap: 8px;
}

/* Sub-subcategories Grid */
.sub-subcategories-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
  gap: 20px;
}

.sub-subcategory-card {
  background: white;
  border-radius: 12px;
  padding: 20px;
  text-align: center;
  transition: all 0.3s ease;
  box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
}

.sub-subcategory-card:hover {
  transform: translateY(-4px);
  box-shadow: 0 6px 20px rgba(0, 0, 0, 0.1);
}

.sub-subcategory-image {
  width: 80px;
  height: 80px;
  border-radius: 12px;
  overflow: hidden;
  margin: 0 auto 16px;
}

.sub-subcategory-image img {
  width: 100%;
  height: 100%;
  object-fit: cover;
}

.sub-subcategory-icon {
  width: 32px;
  height: 32px;
  border-radius: 8px;
  background: linear-gradient(135deg, #667eea, #764ba2);
  color: white;
  display: flex;
  align-items: center;
  justify-content: center;
  margin: 0 auto 12px;
}

.sub-subcategory-name {
  font-size: 16px;
  font-weight: 600;
  color: #1e293b;
  margin: 0 0 4px 0;
}

.sub-subcategory-parent {
  font-size: 12px;
  color: #667eea;
  margin: 0 0 16px 0;
}

.sub-subcategory-actions {
  display: flex;
  gap: 8px;
  justify-content: center;
}

/* Level 4 List */
.level4-list {
  display: flex;
  flex-direction: column;
  gap: 12px;
}

.level4-item {
  display: flex;
  align-items: center;
  gap: 16px;
  padding: 16px;
  background: white;
  border-radius: 10px;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
  transition: all 0.3s ease;
}

.level4-item:hover {
  transform: translateX(4px);
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
}

.level4-icon {
  width: 40px;
  height: 40px;
  border-radius: 10px;
  background: linear-gradient(135deg, #667eea, #764ba2);
  color: white;
  display: flex;
  align-items: center;
  justify-content: center;
}

.level4-info {
  flex: 1;
}

.level4-name {
  font-size: 16px;
  font-weight: 600;
  color: #1e293b;
  margin: 0 0 4px 0;
}

.level4-parent {
  font-size: 12px;
  color: #667eea;
  margin: 0 0 4px 0;
}

.level4-description {
  font-size: 14px;
  color: #64748b;
  margin: 0;
}

.level4-image {
  width: 50px;
  height: 50px;
  border-radius: 8px;
  overflow: hidden;
}

.level4-image img {
  width: 100%;
  height: 100%;
  object-fit: cover;
}

.level4-actions {
  display: flex;
  gap: 8px;
}

/* Modal */
.modal-overlay {
  position: fixed;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: rgba(0, 0, 0, 0.5);
  backdrop-filter: blur(4px);
  display: flex;
  align-items: center;
  justify-content: center;
  z-index: 1000;
}

.modal-content {
  background: white;
  border-radius: 20px;
  width: 90%;
  max-width: 600px;
  max-height: 90vh;
  overflow-y: auto;
  box-shadow: 0 20px 40px rgba(0, 0, 0, 0.2);
}

.modal-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 24px 32px;
  border-bottom: 1px solid rgba(0, 0, 0, 0.1);
}

.modal-title {
  font-size: 20px;
  font-weight: 600;
  color: #1e293b;
  margin: 0;
}

.modal-close {
  width: 32px;
  height: 32px;
  border: none;
  background: rgba(0, 0, 0, 0.05);
  border-radius: 8px;
  cursor: pointer;
  display: flex;
  align-items: center;
  justify-content: center;
  color: #64748b;
  transition: all 0.3s ease;
}

.modal-close:hover {
  background: rgba(0, 0, 0, 0.1);
  color: #1e293b;
}

.modal-form {
  padding: 32px;
}

.form-group {
  margin-bottom: 24px;
}

.form-label {
  display: block;
  font-size: 14px;
  font-weight: 500;
  color: #374151;
  margin-bottom: 8px;
}

.form-input,
.form-select,
.form-textarea {
  width: 100%;
  padding: 12px 16px;
  border: 1px solid #d1d5db;
  border-radius: 8px;
  font-size: 14px;
  transition: all 0.3s ease;
}

.form-input:focus,
.form-select:focus,
.form-textarea:focus {
  outline: none;
  border-color: #667eea;
  box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
}

.form-textarea {
  resize: vertical;
  min-height: 80px;
}

/* Icon Selector */
.icon-selector {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(50px, 1fr));
  gap: 8px;
  margin-top: 8px;
}

.icon-option {
  width: 50px;
  height: 50px;
  border: 2px solid #cbd5e1;
  border-radius: 8px;
  display: flex;
  align-items: center;
  justify-content: center;
  cursor: pointer;
  transition: all 0.3s ease;
  color: #6b7280;
}

.icon-option:hover {
  border-color: #fe7900;
}

.icon-option.selected {
  border-color: #fe7900;
  background: rgba(102, 126, 234, 0.1);
  color: #fe7900;
}

/* Image Upload */
.image-upload {
  display: flex;
  gap: 16px;
  align-items: center;
}

.image-preview {
  width: 100px;
  height: 100px;
  border-radius: 8px;
  overflow: hidden;
  border: 2px dashed #d1d5db;
  display: flex;
  align-items: center;
  justify-content: center;
  position: relative;
}

.image-preview img {
  width: 100%;
  height: 100%;
  object-fit: cover;
}

.upload-controls {
  flex: 1;
  display: flex;
  flex-direction: column;
  gap: 8px;
}

.file-input {
  display: none;
}

.upload-btn {
  display: flex;
  align-items: center;
  gap: 8px;
  padding: 10px 16px;
  border: 1px solid #d1d5db;
  border-radius: 8px;
  background: white;
  color: #374151;
  cursor: pointer;
  transition: all 0.3s ease;
}

.upload-btn:hover {
  border-color: #fe7900;
  color: #fe7900;
}

.upload-btn:disabled {
  opacity: 0.6;
  cursor: not-allowed;
}

.remove-btn {
  display: flex;
  align-items: center;
  gap: 8px;
  padding: 8px 12px;
  border-radius: 6px;
  cursor: pointer;
  transition: all 0.3s ease;
  font-size: 12px;
}


/* Upload overlay and progress */
.upload-overlay {
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: rgba(0, 0, 0, 0.7);
  display: flex;
  align-items: center;
  justify-content: center;
  border-radius: 8px;
}

.upload-progress {
  text-align: center;
  color: white;
}

.progress-bar {
  width: 60px;
  height: 4px;
  background: rgba(255, 255, 255, 0.3);
  border-radius: 2px;
  overflow: hidden;
  margin-bottom: 8px;
}

.progress-fill {
  height: 100%;
  background: #667eea;
  transition: width 0.3s ease;
}

.progress-text {
  font-size: 12px;
  font-weight: 500;
}

/* Form Actions */
.form-actions {
  display: flex;
  gap: 12px;
  justify-content: flex-end;
  margin-top: 32px;
  padding-top: 24px;
  border-top: 1px solid rgba(0, 0, 0, 0.1);
}

.btn-cancel {
  padding: 10px 20px;
  border: 1px solid #d1d5db;
  border-radius: 8px;
  background: white;
  color: #374151;
  cursor: pointer;
  transition: all 0.3s ease;
}

.btn-cancel:hover {
  background: #f9fafb;
}

.btn-submit {
  display: flex;
  align-items: center;
  gap: 8px;
  padding: 10px 20px;
  background: linear-gradient(135deg, #667eea, #764ba2);
  color: white;
  border: none;
  border-radius: 8px;
  cursor: pointer;
  transition: all 0.3s ease;
}

.btn-submit:hover:not(:disabled) {
  transform: translateY(-2px);
  box-shadow: 0 8px 20px rgba(102, 126, 234, 0.3);
}

.btn-submit:disabled {
  opacity: 0.6;
  cursor: not-allowed;
}

.animate-spin {
  animation: spin 1s linear infinite;
}

@keyframes spin {
  from {
    transform: rotate(0deg);
  }
  to {
    transform: rotate(360deg);
  }
}

/* Animations luxueuses */
@keyframes float-slow {
  0%, 100% { transform: translateY(0px) translateX(0px) rotate(0deg); }
  25% { transform: translateY(-30px) translateX(15px) rotate(2deg); }
  50% { transform: translateY(-15px) translateX(-20px) rotate(-1deg); }
  75% { transform: translateY(-40px) translateX(8px) rotate(1deg); }
}

@keyframes float-reverse {
  0%, 100% { transform: translateY(0px) translateX(0px) rotate(0deg); }
  25% { transform: translateY(20px) translateX(-15px) rotate(-2deg); }
  50% { transform: translateY(35px) translateX(25px) rotate(1deg); }
  75% { transform: translateY(8px) translateX(-8px) rotate(-1deg); }
}

@keyframes float-diagonal {
  0%, 100% { transform: translateY(0px) translateX(0px) scale(1) rotate(0deg); }
  33% { transform: translateY(-25px) translateX(20px) scale(1.1) rotate(1deg); }
  66% { transform: translateY(15px) translateX(-15px) scale(0.9) rotate(-1deg); }
}

@keyframes float-slow-reverse {
  0%, 100% { transform: translateY(0px) translateX(0px) rotate(0deg); }
  25% { transform: translateY(-15px) translateX(-20px) rotate(-1deg); }
  50% { transform: translateY(25px) translateX(10px) rotate(2deg); }
  75% { transform: translateY(-10px) translateX(-5px) rotate(-0.5deg); }
}

@keyframes pulse-slow {
  0%, 100% { opacity: 0.4; transform: scale(1); }
  50% { opacity: 0.8; transform: scale(1.4); }
}

@keyframes pulse-delayed {
  0%, 100% { opacity: 0.3; transform: scale(1); }
  50% { opacity: 0.7; transform: scale(1.5); }
}

@keyframes pulse-delayed-2 {
  0%, 100% { opacity: 0.25; transform: scale(1); }
  50% { opacity: 0.6; transform: scale(1.3); }
}

@keyframes slide-down {
  0% { transform: translateY(-100%); opacity: 0; }
  50% { opacity: 1; }
  100% { transform: translateY(300%); opacity: 0; }
}

@keyframes slide-right {
  0% { transform: translateX(-100%); opacity: 0; }
  50% { opacity: 1; }
  100% { transform: translateX(300%); opacity: 0; }
}

@keyframes slide-up {
  0% { transform: translateY(100%); opacity: 0; }
  50% { opacity: 1; }
  100% { transform: translateY(-200%); opacity: 0; }
}

@keyframes rotate-slow {
  0% { transform: rotate(0deg); }
  100% { transform: rotate(360deg); }
}

@keyframes float-small {
  0%, 100% { transform: translateY(0px); }
  50% { transform: translateY(-10px); }
}

.animate-float-slow { animation: float-slow 20s ease-in-out infinite; }
.animate-float-reverse { animation: float-reverse 25s ease-in-out infinite; }
.animate-float-diagonal { animation: float-diagonal 18s ease-in-out infinite; }
.animate-float-slow-reverse { animation: float-slow-reverse 22s ease-in-out infinite; }
.animate-pulse-slow { animation: pulse-slow 4s ease-in-out infinite; }
.animate-pulse-delayed { animation: pulse-delayed 5s ease-in-out infinite 1s; }
.animate-pulse-delayed-2 { animation: pulse-delayed-2 6s ease-in-out infinite 2s; }
.animate-slide-down { animation: slide-down 8s linear infinite; }
.animate-slide-right { animation: slide-right 10s linear infinite; }
.animate-slide-up { animation: slide-up 9s linear infinite; }
.animate-rotate-slow { animation: rotate-slow 30s linear infinite; }
.animate-float-small { animation: float-small 3s ease-in-out infinite; }
</style>
