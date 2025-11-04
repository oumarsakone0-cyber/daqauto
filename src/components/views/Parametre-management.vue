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
          <svg class="w-4 h-4 sm:w-5 sm:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
          </svg>
        </router-link>
        
        <span class="mx-2">/</span>
        <span class="font-medium text-gray-700 truncate">Paramètres</span>
      </div>

      <!-- Header responsive -->
      <div class="flex flex-col space-y-4 lg:flex-row lg:items-center lg:justify-between lg:space-y-0 mb-6 sm:mb-8">
        <div>
          <h1 class="text-2xl sm:text-3xl font-bold text-gray-900 mb-2">Paramètres de la Boutique</h1>
          <p class="text-sm sm:text-base text-gray-600">Gérez vos informations et préférences</p>
        </div>
        
        <!-- Actions -->
        <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-2 sm:gap-3">
          <button 
            @click="saveAllSettings"
            :disabled="!hasUnsavedChanges"
            :class="[
              'w-full sm:w-auto inline-flex items-center justify-center px-4 py-2 rounded-lg shadow-sm text-sm font-medium',
              hasUnsavedChanges ? 'btn-degrade-orange' : 'btn-gray cursor-not-allowed'
            ]"
          >
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
            </svg>
            Enregistrer les modifications
          </button>
        </div>
      </div>

      <!-- Tabs Navigation -->
      <div class="bg-white shadow-lg rounded-lg overflow-hidden border border-gray-100 mb-6">
        <div class="px-4 sm:px-6 py-3 sm:py-4 border-b border-gray-200 bg-gray-50">
          <div class="flex flex-wrap gap-2">
            <button 
              v-for="tab in tabs" 
              :key="tab.id"
              @click="activeTab = tab.id"
              :class="[
                'px-3 sm:px-4 py-2 rounded-lg transition-all duration-200 text-xs sm:text-sm font-medium flex items-center gap-2',
                activeTab === tab.id 
                  ? 'shadow-lg btn-degrade-orange' 
                  : 'btn-gray'
              ]"
            >
              <component :is="tab.icon" class="w-4 h-4" />
              <span class="hidden sm:inline">{{ tab.label }}</span>
              <span class="sm:hidden">{{ tab.shortLabel }}</span>
            </button>
          </div>
        </div>

        <!-- Tab Content -->
        <div class="p-4 sm:p-6">
          
          <!-- Informations de la Boutique -->
          <div v-if="activeTab === 'shop'" class="space-y-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
              
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Nom de la boutique <span class="error-color">*</span></label>
                <input 
                  v-model="shopInfo.name"
                  type="text" 
                  class="input-style"
                  placeholder="Ma Super Boutique"
                  @input="markAsChanged"
                >
              </div>

              <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Email de contact <span class="error-color">*</span></label>
                <input 
                  v-model="shopInfo.email"
                  type="email" 
                  class="input-style"
                  placeholder="contact@boutique.com"
                  @input="markAsChanged"
                >
              </div>

              <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Téléphone <span class="error-color">*</span></label>
                <input 
                  v-model="shopInfo.phone"
                  type="tel" 
                  class="input-style"
                  placeholder="+225 XX XX XX XX XX"
                  @input="markAsChanged"
                >
              </div>

              <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Pays</label>
                <select v-model="shopInfo.country" class="input-style" @change="markAsChanged">
                  <option value="CI">Côte d'Ivoire</option>
                  <option value="SN">Sénégal</option>
                  <option value="BF">Burkina Faso</option>
                  <option value="ML">Mali</option>
                  <option value="BJ">Bénin</option>
                </select>
              </div>

              <div class="md:col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-2">Adresse complète <span class="error-color">*</span></label>
                <input 
                  v-model="shopInfo.address"
                  type="text" 
                  class="input-style"
                  placeholder="Rue, Quartier, Ville"
                  @input="markAsChanged"
                >
              </div>

              <div class="md:col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-2">Description de la boutique</label>
                <textarea 
                  v-model="shopInfo.description"
                  rows="4" 
                  class="input-style"
                  placeholder="Décrivez votre boutique et vos produits..."
                  @input="markAsChanged"
                ></textarea>
              </div>

              <div class="md:col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-2">Logo de la boutique</label>
                <div class="flex items-center gap-4">
                  <div class="w-20 h-20 bg-gray-100 rounded-lg flex items-center justify-center overflow-hidden">
                    <img v-if="shopInfo.logo" :src="shopInfo.logo" alt="Logo" class="w-full h-full object-cover">
                    <svg v-else class="w-10 h-10 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                  </div>
                  <button class="btn-degrade-orange px-4 py-2 rounded-lg text-sm">
                    Changer le logo
                  </button>
                </div>
              </div>
            </div>
          </div>

          <!-- Gestion des Utilisateurs -->
          <div v-if="activeTab === 'users'" class="space-y-6">
            <div class="flex justify-between items-center mb-4">
              <h3 class="text-lg font-semibold text-gray-900">Utilisateurs de la boutique</h3>
              <button 
                @click="showAddUserModal = true"
                class="btn-degrade-orange"
              >
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                Ajouter un utilisateur
              </button>
            </div>

            <!-- Liste des utilisateurs -->
            <div class="bg-white border border-gray-200 rounded-lg overflow-hidden">
              <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                  <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Utilisateur</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Rôle</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Statut</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Ajouté le</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
                  </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">

                  <tr v-for="user in users" :key="user.user_id" class="hover:bg-gray-50">
                    <td class="px-6 py-4 whitespace-nowrap">
                      <div class="flex items-center">
                        <div class="w-10 h-10 bg-orange rounded-full flex items-center justify-center font-semibold">
                          {{ user.name.charAt(0).toUpperCase() }}
                        </div>
                        <div class="ml-4">
                          <div class="text-sm font-medium text-gray-900">{{ user.full_name }}</div>
                          <div class="text-sm text-gray-500">{{ user.email }}</div>
                        </div>
                      </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                      <span :class="getRoleBadgeClass(user.role)" class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full">
                        {{ user.permissions }}
                      </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                      <span :class="user.active ? 'bg-green-100 green-color' : 'bg-gray-100 text-gray-800'" class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full">
                        {{ user.active ? 'Actif' : 'Inactif' }}
                      </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                      {{ formatDate(user.created_at) }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                      <button 
                        v-if="!user.isOwner"
                        @click="removeUser(user.id)" 
                        class="btn-deconnexion"
                      >
                        Supprimer
                      </button>
                      <span v-else class="text-gray-400">Propriétaire</span>
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>

          <!-- Informations Bancaires -->
          <div v-if="activeTab === 'banking'" class="space-y-6">
            <div class="bg-orange-50 border border-orange-200 rounded-lg p-4 mb-6">
              <div class="flex items-start gap-3">
                <svg class="w-5 h-5 primary-color mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <div>
                  <h4 class="text-sm font-medium primary-color">Informations sécurisées</h4>
                  <p class="text-xs primary-color mt-1">Vos informations bancaires sont cryptées et sécurisées.</p>
                </div>
              </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Nom du titulaire <span class="error-color">*</span></label>
                <input 
                  v-model="bankingInfo.accountHolder"
                  type="text" 
                  class="input-style"
                  placeholder="Jean Dupont"
                  @input="markAsChanged"
                >
              </div>

              <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Banque <span class="error-color">*</span></label>
                <select v-model="bankingInfo.bankName" class="input-style" @change="markAsChanged">
                  <option value="">Sélectionnez une banque</option>
                  <option value="SGCI">Société Générale CI</option>
                  <option value="BICICI">BICICI</option>
                  <option value="BOA">Bank of Africa</option>
                  <option value="ECOBANK">Ecobank</option>
                  <option value="UBA">UBA</option>
                  <option value="NSIA">NSIA Banque</option>
                </select>
              </div>

              <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Numéro de compte <span class="error-color">*</span></label>
                <input 
                  v-model="bankingInfo.accountNumber"
                  type="text" 
                  class="input-style"
                  placeholder="CI00 0000 0000 0000 0000 0000"
                  @input="markAsChanged"
                >
              </div>

              <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Code SWIFT/BIC</label>
                <input 
                  v-model="bankingInfo.swiftCode"
                  type="text" 
                  class="input-style"
                  placeholder="SGCIABCD"
                  @input="markAsChanged"
                >
              </div>

              <div class="md:col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-2">Mobile Money</label>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                  <div>
                    <select v-model="bankingInfo.mobileMoneyProvider" class="input-style mb-2" @change="markAsChanged">
                      <option value="">Sélectionnez un opérateur</option>
                      <option value="ORANGE">Orange Money</option>
                      <option value="MTN">MTN Mobile Money</option>
                      <option value="MOOV">Moov Money</option>
                      <option value="WAVE">Wave</option>
                    </select>
                  </div>
                  <div>
                    <input 
                      v-model="bankingInfo.mobileMoneyNumber"
                      type="tel" 
                      class="input-style"
                      placeholder="+225 XX XX XX XX XX"
                      @input="markAsChanged"
                    >
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- Sécurité -->
          <div v-if="activeTab === 'security'" class="space-y-6">
            <div class="bg-orange-50 border border-orange-200 rounded-lg p-4 mb-6">
              <div class="flex items-start gap-3">
                <svg class="w-5 h-5 primary-color mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                </svg>
                <div>
                  <h4 class="text-sm font-medium primary-color">Sécurité du compte</h4>
                  <p class="text-xs primary-color mt-1">Utilisez un mot de passe fort avec au moins 8 caractères.</p>
                </div>
              </div>
            </div>

            <div class="max-w-2xl space-y-6">
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Mot de passe actuel <span class="error-color">*</span></label>
                <input 
                  v-model="securityInfo.currentPassword"
                  type="password" 
                  class="input-style"
                  placeholder="********"
                >
              </div>

              <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Nouveau mot de passe <span class="error-color">*</span></label>
                <input 
                  v-model="securityInfo.newPassword"
                  type="password" 
                  class="input-style"
                  placeholder="********"
                >
                <div class="mt-2 space-y-1">
                  <div class="flex items-center gap-2 text-xs">
                    <div :class="passwordStrength.length ? 'bg-step-color' : 'bg-gray-300'" class="w-2 h-2 rounded-full"></div>
                    <span :class="passwordStrength.length ? 'green-color' : 'text-gray-500'">Au moins 8 caractères</span>
                  </div>
                  <div class="flex items-center gap-2 text-xs">
                    <div :class="passwordStrength.uppercase ? 'bg-step-color' : 'bg-gray-300'" class="w-2 h-2 rounded-full"></div>
                    <span :class="passwordStrength.uppercase ? 'green-color' : 'text-gray-500'">Une majuscule</span>
                  </div>
                  <div class="flex items-center gap-2 text-xs">
                    <div :class="passwordStrength.number ? 'bg-step-color' : 'bg-gray-300'" class="w-2 h-2 rounded-full"></div>
                    <span :class="passwordStrength.number ? 'green-color' : 'text-gray-500'">Un chiffre</span>
                  </div>
                </div>
              </div>

              <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Confirmer le nouveau mot de passe <span class="error-color">*</span></label>
                <input 
                  v-model="securityInfo.confirmPassword"
                  type="password" 
                  class="input-style"
                  placeholder="********"
                >
                <p v-if="securityInfo.newPassword && securityInfo.confirmPassword && securityInfo.newPassword !== securityInfo.confirmPassword" class="mt-1 text-xs error-color">
                  Les mots de passe ne correspondent pas
                </p>
              </div>

              <button 
                @click="changePassword"
                :disabled="!canChangePassword"
                :class="[
                  'px-6 py-2 rounded-lg text-sm font-medium',
                  canChangePassword ? 'btn-degrade-orange' : 'btn-gray cursor-not-allowed'
                ]"
              >
                Changer le mot de passe
              </button>
            </div>

            <!-- Authentification à deux facteurs -->
            <div class="border-t border-gray-200 pt-6 mt-6">
              <h3 class="text-lg font-semibold text-gray-900 mb-4">Authentification à deux facteurs</h3>
              <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                <div>
                  <h4 class="text-sm font-medium text-gray-900">Activer l'authentification à deux facteurs</h4>
                  <p class="text-sm text-gray-500 mt-1">Ajoutez une couche de sécurité supplémentaire à votre compte</p>
                </div>
                <button 
                  @click="toggle2FA"
                  :class="[
                    'relative inline-flex h-6 w-11 items-center rounded-full transition-colors',
                    twoFactorEnabled ? 'bg-orange' : 'bg-gray-300'
                  ]"
                >
                  <span 
                    :class="[
                      'inline-block h-4 w-4 transform rounded-full bg-white transition-transform',
                      twoFactorEnabled ? 'translate-x-6' : 'translate-x-1'
                    ]"
                  ></span>
                </button>
              </div>
            </div>
          </div>

          <!-- Préférences -->
          <div v-if="activeTab === 'preferences'" class="space-y-6">
            <div class="space-y-4">
              <h3 class="text-lg font-semibold text-gray-900">Notifications</h3>
              
              <div class="space-y-3">
                <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                  <div>
                    <h4 class="text-sm font-medium text-gray-900">Notifications par email</h4>
                    <p class="text-sm text-gray-500 mt-1">Recevoir des notifications sur les nouvelles commandes</p>
                  </div>
                  <button 
                    @click="preferences.emailNotifications = !preferences.emailNotifications"
                    :class="[
                      'relative inline-flex h-6 w-11 items-center rounded-full transition-colors',
                      preferences.emailNotifications ? 'bg-orange' : 'bg-gray-300'
                    ]"
                  >
                    <span 
                      :class="[
                        'inline-block h-4 w-4 transform rounded-full bg-white transition-transform',
                        preferences.emailNotifications ? 'translate-x-6' : 'translate-x-1'
                      ]"
                    ></span>
                  </button>
                </div>

                <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                  <div>
                    <h4 class="text-sm font-medium text-gray-900">Notifications SMS</h4>
                    <p class="text-sm text-gray-500 mt-1">Recevoir des alertes importantes par SMS</p>
                  </div>
                  <button 
                    @click="preferences.smsNotifications = !preferences.smsNotifications"
                    :class="[
                      'relative inline-flex h-6 w-11 items-center rounded-full transition-colors',
                      preferences.smsNotifications ? 'bg-orange' : 'bg-gray-300'
                    ]"
                  >
                    <span 
                      :class="[
                        'inline-block h-4 w-4 transform rounded-full bg-white transition-transform',
                        preferences.smsNotifications ? 'translate-x-6' : 'translate-x-1'
                      ]"
                    ></span>
                  </button>
                </div>

                <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                  <div>
                    <h4 class="text-sm font-medium text-gray-900">Notifications marketing</h4>
                    <p class="text-sm text-gray-500 mt-1">Recevoir des conseils et promotions</p>
                  </div>
                  <button 
                    @click="preferences.marketingNotifications = !preferences.marketingNotifications"
                    :class="[
                      'relative inline-flex h-6 w-11 items-center rounded-full transition-colors',
                      preferences.marketingNotifications ? 'bg-orange' : 'bg-gray-300'
                    ]"
                  >
                    <span 
                      :class="[
                        'inline-block h-4 w-4 transform rounded-full bg-white transition-transform',
                        preferences.marketingNotifications ? 'translate-x-6' : 'translate-x-1'
                      ]"
                    ></span>
                  </button>
                </div>
              </div>
            </div>

            <div class="border-t border-gray-200 pt-6 space-y-4">
              <h3 class="text-lg font-semibold text-gray-900">Langue et région</h3>
              
              <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-2">Langue</label>
                  <select v-model="preferences.language" class="input-style" @change="markAsChanged">
                    <option value="fr">Français</option>
                    <option value="en">English</option>
                  </select>
                </div>

                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-2">Fuseau horaire</label>
                  <select v-model="preferences.timezone" class="input-style" @change="markAsChanged">
                    <option value="Africa/Abidjan">Abidjan (GMT+0)</option>
                    <option value="Africa/Dakar">Dakar (GMT+0)</option>
                    <option value="Africa/Lagos">Lagos (GMT+1)</option>
                  </select>
                </div>

                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-2">Devise</label>
                  <select v-model="preferences.currency" class="input-style" @change="markAsChanged">
                    <option value="XOF">Franc CFA (XOF)</option>
                    <option value="USD">Dollar US (USD)</option>
                    <option value="EUR">Euro (EUR)</option>
                  </select>
                </div>
              </div>
            </div>
          </div>

        </div>
      </div>

    </div>

    <!-- Modal Ajouter Utilisateur -->
    <div v-if="showAddUserModal" class="fixed inset-0 bg-black/60 bg-opacity-50 flex items-center justify-center z-50 p-4">
      <div class="bg-white rounded-lg shadow-xl max-w-md w-full p-6">
        <div class="flex justify-between items-center mb-4">
          <h3 class="text-lg font-semibold text-gray-900">Ajouter un utilisateur</h3>
          <XIcon @click="showAddUserModal = false" class="w-7 h-7 text-gray-500 cursor-pointer" />
        </div>

        <div class="space-y-4">
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Nom complet <span class="error-color">*</span></label>
            <input 
              v-model="newUser.name"
              type="text" 
              class="input-style"
              placeholder="Jean Dupont"
            >
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Email <span class="error-color">*</span></label>
            <input 
              v-model="newUser.email"
              type="email" 
              class="input-style"
              placeholder="jean@example.com"
            >
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Rôle <span class="error-color">*</span></label>
            <select v-model="newUser.role" class="input-style">
              <option value="admin">Administrateur</option>
              <option value="manager">Gestionnaire</option>
              <option value="staff">Personnel</option>
            </select>
          </div>

          <div class="flex gap-3 mt-6">
            <button 
              @click="showAddUserModal = false"
              class="flex-1 btn-gray"
            >
              Annuler
            </button>
            <button 
              @click="addUser"
              class="flex-1 btn-degrade-orange"
            >
              Ajouter
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- Notification Toast -->
    <div 
      v-if="showNotification"
      class="fixed bottom-4 right-4 bg-white shadow-lg rounded-lg p-4 border-l-4 border-green-500 z-50 animate-slide-in"
    >
      <div class="flex items-center gap-3">
        <svg class="w-5 h-5 green-color" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
        </svg>
        <p class="text-sm font-medium text-gray-900">{{ notificationMessage }}</p>
      </div>
    </div>

  </div>
</template>

<script setup>
import { useRouter } from 'vue-router'
import { ref, computed, onMounted, watch } from 'vue'
import Navbar from '../boutiques/Navbar.vue'
import { XIcon } from 'lucide-vue-next'

// Icons components (vous pouvez utiliser lucide-vue-next ou heroicons)
const StoreIcon = { template: '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" /></svg>' }
const UsersIcon = { template: '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" /></svg>' }
const CreditCardIcon = { template: '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" /></svg>' }
const LockIcon = { template: '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" /></svg>' }
const SettingsIcon = { template: '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /></svg>' }

// États
const activeTab = ref('shop')
const hasUnsavedChanges = ref(false)
const successMessage = ref('')
const showAddUserModal = ref(false)
const showNotification = ref(false)
const notificationMessage = ref('') 
const currentUser = ref(null)
const twoFactorEnabled = ref(false)
const currentBoutique = ref(null)

const isLoading = ref(false)
const error = ref('')
const users = ref([])
const boutique = ref(null)

// Tabs configuration
const tabs = [
  { id: 'shop', label: 'Boutique', shortLabel: 'Boutique', icon: StoreIcon },
  { id: 'users', label: 'Utilisateurs', shortLabel: 'Users', icon: UsersIcon },
  { id: 'banking', label: 'Informations bancaires', shortLabel: 'Banque', icon: CreditCardIcon },
  { id: 'security', label: 'Sécurité', shortLabel: 'Sécurité', icon: LockIcon },
  { id: 'preferences', label: 'Préférences', shortLabel: 'Préfs', icon: SettingsIcon }
]

// Données de la boutique
const shopInfo = ref({
  name: 'Ma Super Boutique',
  email: 'contact@boutique.com',
  phone: '+225 07 XX XX XX XX',
  country: 'CI',
  address: 'Cocody, Abidjan',
  description: 'Nous vendons des produits de qualité...',
  logo: null
})

const router = useRouter()

// Utilisateurs

const newUser = ref({
  name: '',
  email: '',
  role: 'staff'
})

// Informations bancaires
const bankingInfo = ref({
  accountHolder: '',
  bankName: '',
  accountNumber: '',
  swiftCode: '',
  mobileMoneyProvider: '',
  mobileMoneyNumber: ''
})

// Sécurité
const securityInfo = ref({
  currentPassword: '',
  newPassword: '',
  confirmPassword: ''
})

// Préférences
const preferences = ref({
  emailNotifications: true,
  smsNotifications: false,
  marketingNotifications: true,
  language: 'fr',
  timezone: 'Africa/Abidjan',
  currency: 'XOF'
})

// Computed
const passwordStrength = computed(() => {
  const password = securityInfo.value.newPassword
  return {
    length: password.length >= 8,
    uppercase: /[A-Z]/.test(password),
    number: /[0-9]/.test(password)
  }
})

const handleGetUsersByBoutique = async (boutiqueId) => {
  if (!boutiqueId) {
    ElNotification({
      title: 'Erreur',
      message: 'ID de boutique requis',
      type: 'error'
    })
    return
  }

  try {
    isLoading.value = true
    error.value = ''

    const response = await usersApi.getUsersByBoutique(boutiqueId)
    console.log('Réponse API getUsersByBoutique:', response)

    if (response.success) {
      users.value = response.data.users
      boutique.value = response.data.boutique

      ElNotification({
        title: 'Succès',
        message: `Liste des utilisateurs pour ${boutique.value.name} récupérée`,
        type: 'success'
      })
    } else {
      error.value = response.error || 'Erreur lors de la récupération des utilisateurs'
      users.value = []
      boutique.value = null

      ElNotification({
        title: 'Erreur',
        message: error.value,
        type: 'error'
      })
    }

  } catch (err) {
    console.error('Erreur API getUsersByBoutique:', err)
    error.value = err.response?.data?.error || 'Erreur réseau. Veuillez réessayer.'
    users.value = []
    boutique.value = null

    ElNotification({
      title: 'Erreur',
      message: error.value,
      type: 'error'
    })
  } finally {
    isLoading.value = false
  }
}

const canChangePassword = computed(() => {
  return securityInfo.value.currentPassword &&
         securityInfo.value.newPassword &&
         securityInfo.value.confirmPassword &&
         securityInfo.value.newPassword === securityInfo.value.confirmPassword &&
         passwordStrength.value.length &&
         passwordStrength.value.uppercase &&
         passwordStrength.value.number
})

// Méthodes
const markAsChanged = () => {
  hasUnsavedChanges.value = true
}

const handleAddUserToBoutique = async () => {
  console.log('Ajout de l\'utilisateur:', newUser.value)
  if (!newUser.value.name || !newUser.value.email) {
    ElNotification({
      title: 'Erreur',
      message: 'Veuillez remplir tous les champs requis.',
      type: 'error'
    })
    return
  }

  try {
    //isLoading.value = true
    error.value = ''

    const payload = {
      full_name: newUser.value.name,
      email: newUser.value.email,
      boutique_id: currentUser.value.boutiques[0].id  // Assurez-vous que la boutique est sélectionnée correctement
    }

    const response = await usersApi.addUserToBoutique(payload)
    console.log('Réponse API ajout utilisateur:', response)

    if (response.success) {
      ElNotification({
        title: 'Succès',
        message: 'Utilisateur ajouté et invitation envoyée avec succès.',
        type: 'success'
      })

      successMessage.value = 'Utilisateur ajouté avec succès.'
      // Si tu veux, tu peux vider le formulaire
      newUser.value.name = ''
      newUser.value.email = ''

      showAddUserModal.value = false
    } else {
      error.value = response.error || 'Erreur lors de l’ajout de l’utilisateur.'
      ElNotification({
        title: 'Erreur',
        message: error.value,
        type: 'error'
      })
    }
  } catch (err) {
    console.error('Erreur lors de l’ajout d’un utilisateur:', err)
    error.value = err.response?.data?.error || 'Erreur réseau. Veuillez réessayer.'

    ElNotification({
      title: 'Erreur',
      message: error.value,
      type: 'error'
    })
  } finally {
    //isLoading.value = false
  }
}


const initializeUserData = () => {
  try {
    // ✅ Récupérer le token d'authentification
    const authToken = localStorage.getItem('authToken') || sessionStorage.getItem('authToken')
    console.log('Token d\'authentification récupéré:',)
    if (!authToken) {
      error.value = 'Token d\'authentification manquant. Veuillez vous reconnecter.'
      // Rediriger vers la page de login
      window.location.href = '/boutique-admin/login'
      return
    }

    // ✅ Récupérer les données utilisateur
    const userData = localStorage.getItem('user') || sessionStorage.getItem('user')
    
    if (!userData) {
      error.value = 'Données utilisateur manquantes. Veuillez vous reconnecter.'
      window.location.href = '/boutique-admin/login'
      return
    }

    // ✅ Parser les données utilisateur
    const user = JSON.parse(userData)
    
    // ✅ Valider la structure des données
    if (!user.id || !user.email) {
      error.value = 'Données utilisateur invalides. Veuillez vous reconnecter.'
      window.location.href = '/boutique-admin/login'
      return
    }

    // ✅ Assigner les données utilisateur
    currentUser.value = {
      id: user.id,
      full_name: user.full_name,
      email: user.email,
      boutiques: user.boutiques || []
    }

    // ✅ Sélectionner la boutique active
    if (user.boutiques && user.boutiques.length > 0) {
      // Prendre la première boutique par défaut
      currentBoutique.value = user.boutiques[0]
      
      // Ou récupérer la boutique sélectionnée précédemment
      const savedBoutiqueId = localStorage.getItem('selectedBoutiqueId')
      if (savedBoutiqueId) {
        const savedBoutique = user.boutiques.find(b => b.id == savedBoutiqueId)
        if (savedBoutique) {
          currentBoutique.value = savedBoutique
        }
      }
    } else {
      error.value = 'Aucune boutique associée à ce compte.'
      return
    }

    // ✅ Vérifier si "Se souvenir de moi" est activé
    const rememberMe = localStorage.getItem('rememberMe') === 'true'
    
    console.log('✅ Données utilisateur chargées:', {
      user: currentUser.value,
      boutique: currentBoutique.value,
      rememberMe,
      token: authToken ? 'Présent' : 'Absent'
    })

  } catch (err) {
    console.error('Erreur lors de la récupération des données utilisateur:', err)
    error.value = 'Erreur lors du chargement des données utilisateur.'
    
    // Nettoyer le stockage en cas d'erreur
    localStorage.removeItem('authToken')
    localStorage.removeItem('user')
    sessionStorage.removeItem('authToken')
    sessionStorage.removeItem('user')
    
    // Rediriger vers la page de login
    window.location.href = '/boutique-admin/login'
  }
}

const saveAllSettings = () => {
  // Logique de sauvegarde
  console.log('Sauvegarde des paramètres...')
  showNotificationToast('Paramètres enregistrés avec succès!')
  hasUnsavedChanges.value = false
}

const addUser = () => {
  if (!newUser.value.name || !newUser.value.email) {
    alert('Veuillez remplir tous les champs obligatoires')
    return
  }

  users.value.push({
    id: users.value.length + 1,
    name: newUser.value.name,
    email: newUser.value.email,
    role: newUser.value.role,
    active: true,
    isOwner: false,
    created_at: new Date().toISOString()
  })

  showAddUserModal.value = false
  newUser.value = { name: '', email: '', role: 'staff' }
  showNotificationToast('Utilisateur ajouté avec succès!')
}

const removeUser = (userId) => {
  if (confirm('Êtes-vous sûr de vouloir supprimer cet utilisateur ?')) {
    users.value = users.value.filter(u => u.id !== userId)
    showNotificationToast('Utilisateur supprimé')
  }
}

const changePassword = () => {
  if (!canChangePassword.value) return
  
  // Logique de changement de mot de passe
  console.log('Changement de mot de passe...')
  securityInfo.value = {
    currentPassword: '',
    newPassword: '',
    confirmPassword: ''
  }
  showNotificationToast('Mot de passe modifié avec succès!')
}

const toggle2FA = () => {
  twoFactorEnabled.value = !twoFactorEnabled.value
  showNotificationToast(
    twoFactorEnabled.value 
      ? 'Authentification à deux facteurs activée' 
      : 'Authentification à deux facteurs désactivée'
  )
}

const showNotificationToast = (message) => {
  notificationMessage.value = message
  showNotification.value = true
  setTimeout(() => {
    showNotification.value = false
  }, 3000)
}

const getRoleBadgeClass = (role) => {
  const classes = {
    'Propriétaire': 'bg-purple-100 text-purple-800',
    'Administrateur': 'bg-blue-100 text-blue-800',
    'Gestionnaire': 'bg-green-100 text-green-800',
    'Personnel': 'bg-gray-100 text-gray-800'
  }
  return classes[role] || 'bg-gray-100 text-gray-800'
}

const formatDate = (dateString) => {
  return new Date(dateString).toLocaleDateString('fr-FR', {
    year: 'numeric',
    month: 'long',
    day: 'numeric'
  })
}

onMounted(async () => {

  initializeUserData()

  handleGetUsersByBoutique(currentUser.value.boutiques[0].id)
  
    const token = localStorage.getItem('authToken') || sessionStorage.getItem('authToken')
    console.log(token)
    if (!token) {
      router.replace('/boutique-admin/login')
      return
    }

  // Initialiser avec les données mock pour la démo
  // products.value = mockProducts
  
})
</script>

<style scoped>
@keyframes float-slow {
  0%, 100% { transform: translate(0, 0) scale(1); }
  50% { transform: translate(30px, -30px) scale(1.1); }
}

@keyframes float-reverse {
  0%, 100% { transform: translate(0, 0) scale(1); }
  50% { transform: translate(-30px, 30px) scale(1.1); }
}

@keyframes float-diagonal {
  0%, 100% { transform: translate(0, 0) scale(1); }
  50% { transform: translate(20px, 20px) scale(1.05); }
}

@keyframes float-slow-reverse {
  0%, 100% { transform: translate(0, 0) scale(1); }
  50% { transform: translate(-20px, -20px) scale(1.05); }
}

@keyframes pulse-slow {
  0%, 100% { opacity: 0.3; transform: scale(1); }
  50% { opacity: 0.8; transform: scale(1.2); }
}

@keyframes pulse-delayed {
  0%, 100% { opacity: 0.3; transform: scale(1); }
  50% { opacity: 0.7; transform: scale(1.15); }
}

@keyframes pulse-delayed-2 {
  0%, 100% { opacity: 0.3; transform: scale(1); }
  50% { opacity: 0.6; transform: scale(1.1); }
}

@keyframes slide-down {
  0% { transform: translateY(-100%); opacity: 0; }
  50% { opacity: 0.5; }
  100% { transform: translateY(100%); opacity: 0; }
}

@keyframes slide-right {
  0% { transform: translateX(-100%); opacity: 0; }
  50% { opacity: 0.5; }
  100% { transform: translateX(100%); opacity: 0; }
}

@keyframes slide-up {
  0% { transform: translateY(100%); opacity: 0; }
  50% { opacity: 0.5; }
  100% { transform: translateY(-100%); opacity: 0; }
}

@keyframes rotate-slow {
  0% { transform: rotate(0deg); }
  100% { transform: rotate(360deg); }
}

@keyframes float-small {
  0%, 100% { transform: translateY(0); }
  50% { transform: translateY(-10px); }
}

@keyframes slide-in {
  from {
    transform: translateX(100%);
    opacity: 0;
  }
  to {
    transform: translateX(0);
    opacity: 1;
  }
}

.animate-float-slow {
  animation: float-slow 20s ease-in-out infinite;
}

.animate-float-reverse {
  animation: float-reverse 18s ease-in-out infinite;
}

.animate-float-diagonal {
  animation: float-diagonal 25s ease-in-out infinite;
}

.animate-float-slow-reverse {
  animation: float-slow-reverse 22s ease-in-out infinite;
}

.animate-pulse-slow {
  animation: pulse-slow 4s ease-in-out infinite;
}

.animate-pulse-delayed {
  animation: pulse-delayed 5s ease-in-out infinite 1s;
}

.animate-pulse-delayed-2 {
  animation: pulse-delayed-2 6s ease-in-out infinite 2s;
}

.animate-slide-down {
  animation: slide-down 8s linear infinite;
}

.animate-slide-right {
  animation: slide-right 10s linear infinite;
}

.animate-slide-up {
  animation: slide-up 9s linear infinite;
}

.animate-rotate-slow {
  animation: rotate-slow 30s linear infinite;
}

.animate-float-small {
  animation: float-small 3s ease-in-out infinite;
}

.animate-slide-in {
  animation: slide-in 0.3s ease-out;
}

@media (max-width: 640px) {
  .text-2xl {
    font-size: 1.5rem;
    line-height: 2rem;
  }
  
  .text-3xl {
    font-size: 1.875rem;
    line-height: 2.25rem;
  }
}
</style>
