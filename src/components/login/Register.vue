<template>
    <div class="min-h-screen bg-gradient-to-br from-gray-50 to-gray-100 flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
      <div class="max-w-md w-full space-y-8">
        <!-- Header -->
        <div class="text-center">
          <div class="mx-auto h-16 w-38  rounded-full flex items-center justify-center mb-4 ">
            <img src="../../assets/logo.png" alt="DaqAuto Logo" class="logo" />
          </div>
          <h2 class="text-3xl font-bold text-gray-900 mb-2">Create an account</h2>
          <p class="text-gray-600">Join Wabili</p>
        </div>
  
        <!-- Form -->
        <div class="bg-white rounded-xl shadow-lg p-8">
          <!-- Error Message -->
          <div v-if="error" class="mb-6 p-4 bg-red-50 border border-red-200 rounded-lg">
            <div class="flex items-center">
              <svg class="w-5 h-5 text-red-400 mr-2" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
              </svg>
              <span class="error-color text-sm">{{ error }}</span>
            </div>
          </div>
          
  
          <!-- Email Already Exists Message -->
          <div v-if="emailExists" class="mb-6 p-4 bg-yellow-50 border border-yellow-200 rounded-lg">
            <div class="flex items-start">
              <svg class="w-5 h-5  mr-2 mt-0.5" fill="#ffb300" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
              </svg>
              <div class="flex-1">
                <h3 class="text-sm font-medium text-yellow-800 mb-2">
                  Existing account detected
                </h3>
                <p class="text-sm text-yellow-700 mb-4">
                  An account with the <strong>{{ registerData.email }}</strong> email address already exists. 
                  You can login or reset your password.
                </p>
                <div class="flex gap-x-2 text-xs">
                  <button
                    @click="goToLogin"
                    class="btn-degrade-orange flex-1"
                  >
                    Login
                  </button>
                  <button
                    @click="showForgotPassword = true"
                    class="bg-lightgray flex-1"
                  >
                    Forgot password ?
                  </button>
                  <button
                    @click="emailExists = false"
                    class="bg-lightgray flex-1"
                  >
                    Edit email
                  </button>
                </div>
              </div>
            </div>
          </div>
  
          <!-- Success Message -->
          <div v-if="successMessage" class="mb-6 p-4 bg-green-50 border border-green-200 rounded-lg">
            <div class="flex items-center">
              <svg class="w-5 h-5 text-green-400 mr-2" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
              </svg>
              <span class="success-color text-sm">{{ successMessage }}</span>
            </div>
          </div>
  
          <!-- Progress Steps -->
          <div class="mb-6">
            <div class="flex items-center justify-between">
              <div v-for="(step, index) in steps" :key="index" class="flex items-center">
                <div :class="[
                  'w-8 h-8 rounded-full flex items-center justify-center text-sm font-medium',
                  currentStep > index ? 'bg-step-color text-white' : 
                  currentStep === index ? 'bg-orange text-white' : 'bg-gray-200 text-gray-600'
                ]">
                  <svg v-if="currentStep > index" class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                  </svg>
                  <span v-else>{{ index + 1 }}</span>
                </div>
                <div v-if="index < steps.length - 1" :class="[
                  'w-12 h-1 mx-2',
                  currentStep > index ? 'bg-step-color' : 'bg-gray-200'
                ]"></div>
              </div>
            </div>
            <div class="flex justify-between mt-2">
              <span v-for="(step, index) in steps" :key="index" :class="[
                'text-xs',
                currentStep >= index ? 'text-gray-900 font-medium' : 'text-gray-500'
              ]">
                {{ step.title }}
              </span>
            </div>
          </div>
  
          <form @submit.prevent="handleSubmit" class="space-y-6">
            <!-- Étape 1: Informations personnelles -->
            <div v-if="currentStep === 0" class="space-y-4">
              <label class="flex text-sm font-medium text-gray-700 mb-2">Profile picture</label>
               <div v-if="registerData.profileImage.length > 0" class="mb-4" >
                  <div class="w-16 h-16 sm:w-18 sm:h-18  rounded-full flex items-center justify-center mx-auto mb-4 relative">

                    <div class="absolute flex items-center justify-center mx-auto">
                          <img
                            :src="registerData.profileImage[0].preview"
                            alt="Image de profil"
                            class="w-16 h-16 sm:w-18 sm:h-18 object-cover rounded-full "
                          >
                        <XIcon
                          @click="registerData.profileImage = []"
                          class="absolute -top-2 -right-2 w-5 h-5 bg-red-500 text-white rounded-full p-1 cursor-pointer"
                          title="Supprimer l'image"
                        />
                    </div>
                  </div>
              </div>
             <div v-else class="text-center">
              <div class="w-12 h-12 sm:w-16 sm:h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                  <User class="w-6 h-6 sm:w-8 sm:h-8 text-gray-400 cursor-pointer" @click="$refs.fileInput.click()"  />
                </div>
              </div>
              <input
                  ref="fileInput"
                  type="file"
                  multiple
                  accept="image/*"
                  @change="handleProfileImageUpload"
                  class="hidden"
                >
                <button
                  type="button"
                  @click="$refs.fileInput.click()"
                  class="flex items-center justify-center text-sm font-medium mx-auto mb-4 btn-degrade-orange h-10"
                >
                  Import photo
                </button>
              <!-- Nom complet -->
              <div>
                <label for="fullName" class="block text-sm font-medium text-gray-700 mb-2 mt-3">
                  Full name <span class="error-color">*</span>
                </label>
                <div class="relative">
                  <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                    </svg>
                  </div>
                  <input
                    id="fullName"
                    v-model="registerData.fullName"
                    type="text"
                    required
                    :class="[
                      'input-style',
                      validationErrors.fullName ? 'border-red-300 bg-red-50' : 'border-gray-300'
                    ]"
                    placeholder="Full name"
                  >
                </div>
                <div v-if="validationErrors.fullName" class="mt-1 text-sm error-color">
                  {{ validationErrors.fullName }}
                </div>
              </div>
  
              <!-- Email -->
              <div>
                <label for="email" class="block text-sm font-medium text-gray-700 mb-2">
                  Email address <span class="error-color">*</span>
                </label>
                <div class="relative">
                  <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207"/>
                    </svg>
                  </div>
                  <input
                    id="email"
                    v-model="registerData.email"
                    type="email"
                    required
                    :class="[
                      'input-style',
                      validationErrors.email ? 'border-red-300 bg-red-50' : 'border-gray-300'
                    ]"
                    placeholder="exemple@email.com"
                    @blur="clearEmailExistsError"
                  >
                </div>
                <div v-if="validationErrors.email" class="mt-1 text-sm error-color">
                  {{ validationErrors.email }}
                </div>
              </div>
  
              <!-- Numéro de téléphone -->
              <div>
                <label for="phone" class="block text-sm font-medium text-gray-700 mb-2">
                  Phone number <span class="error-color">*</span>
                </label>
                <div class="relative">
                  <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                    </svg>
                  </div>
                  <input
                    id="phone"
                    v-model="registerData.phone"
                    type="tel"
                    required
                    :class="[
                      'input-style',
                      validationErrors.phone ? 'border-red-300 bg-red-50' : 'border-gray-300'
                    ]"
                    placeholder="+86 XX XX XX XX XX"
                  >
                </div>
                <div v-if="validationErrors.phone" class="mt-1 text-sm error-color">
                  {{ validationErrors.phone }}
                </div>
              </div>
            </div>
  
            <!-- Étape 2: Informations du magasin 
            <div v-if="currentStep === 1" class="space-y-4">
              <!-- Nom du magasin 
              <div>
                <label for="companyName" class="block text-sm font-medium text-gray-700 mb-2">
                  Nom de votre entreprise <span class="error-color">*</span>
                </label>
                <div class="relative">
                  <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                    </svg>
                  </div>
                  <input
                    id="companyName"
                    v-model="registerData.companyName"
                    type="text"
                    required
                    :class="[
                      'input-style',
                      validationErrors.companyName ? 'border-red-300 bg-red-50' : 'border-gray-300'
                    ]"
                    placeholder="Nom de votre entreprise"
                  >
                </div>
                <div v-if="validationErrors.companyName" class="mt-1 text-sm error-color">
                  {{ validationErrors.companyName }}
                </div>
              </div>
  
              <!-- Pays 
              <div>
                <label for="country" class="block text-sm font-medium text-gray-700 mb-2">
                  Votre Pays <span class="error-color">*</span>
                </label>
                <select
                  id="country"
                  v-model="registerData.country"
                  required
                  :class="[
                    'input-style',
                    validationErrors.country ? 'border-red-300 bg-red-50' : 'border-gray-300'
                  ]"
                >

                  <option value="">Sélectionnez votre pays</option>
                   <option v-for="country in countryList" :key="country" :value="country">
                      {{ country }}
                    </option>
                </select>
                <div v-if="validationErrors.country" class="mt-1 text-sm error-color">
                  {{ validationErrors.country }}
                </div>
              </div>
  
              <!-- Ville 
              <div>
                <label for="city" class="block text-sm font-medium text-gray-700 mb-2">
                  Votre Ville
                </label>
                <div class="relative">
                  
                  <input
                    id="city"
                    type="text"
                    v-model="registerData.city"
                    :class="[
                      'input-style',
                      validationErrors.city ? 'border-red-300 bg-red-50' : 'border-gray-300'
                    ]"
                    placeholder="Ville"
                  />
                </div>
              </div>
            </div>
             -->
  
            <!-- Étape 3: Sécurité et finalisation -->
            <div v-if="currentStep === 1" class="space-y-4">
              <!-- Mot de passe -->
              <div>
                <label for="password" class="block text-sm font-medium text-gray-700 mb-2">
                  Password 
                  <span class="error-color">*</span>
                </label>
                <div class="relative">
                  <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                    </svg>
                  </div>
                  <input
                    id="password"
                    v-model="registerData.password"
                    :type="showPassword ? 'text' : 'password'"
                    required
                    :class="[
                      'input-style',
                      validationErrors.password ? 'border-red-300 bg-red-50' : 'border-gray-300'
                    ]"
                    placeholder="Your password"
                  >
                  <button
                    type="button"
                    @click="showPassword = !showPassword"
                    class="absolute inset-y-0 right-0 px-3 flex items-center bg-orange"
                  >
                    <svg v-if="showPassword" class="h-5 w-5 text-white hover:text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.878 9.878L3 3m6.878 6.878L21 21"/>
                    </svg>
                    <svg v-else class="h-5 w-5 text-white hover:text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                    </svg>
                  </button>
                </div>
                <div v-if="validationErrors.password" class="mt-1 text-sm error-color">
                  {{ validationErrors.password }}
                </div>
                <!-- Indicateur de force du mot de passe -->
                <div v-if="registerData.password" class="mt-2">
                  <div class="flex items-center space-x-2">
                    <div class="flex-1 bg-gray-200 rounded-full h-2">
                      <div 
                        :class="[
                          'h-2 rounded-full transition-all duration-300',
                          passwordStrength.color
                        ]"
                        :style="{ width: passwordStrength.width }"
                      ></div>
                    </div>
                    <span :class="['text-xs font-medium', passwordStrength.textColor]">
                      {{ passwordStrength.text }}
                    </span>
                  </div>
                </div>
              </div>
  
              <!-- Confirmation du mot de passe -->
              <div>
                <label for="confirmPassword" class="block text-sm font-medium text-gray-700 mb-2">
                  Confirm password <span class="error-color">*</span>
                </label>
                <div class="relative">
                  <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                  </div>
                  <input
                    id="confirmPassword"
                    v-model="registerData.confirmPassword"
                    :type="showConfirmPassword ? 'text' : 'password'"
                    required
                    :class="[
                      'input-style',
                      validationErrors.confirmPassword ? 'border-red-300 bg-red-50' : 'border-gray-300'
                    ]"
                    placeholder="Confirm your pasword"
                  >
                  <button
                    type="button"
                    @click="showConfirmPassword = !showConfirmPassword"
                    class="absolute inset-y-0 right-0 px-3 flex items-center bg-orange"
                  >
                    <svg v-if="showConfirmPassword" class="h-5 w-5 text-white hover:text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.878 9.878L3 3m6.878 6.878L21 21"/>
                    </svg>
                    <svg v-else class="h-5 w-5 text-white hover:text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                    </svg>
                  </button>
                </div>
                <div v-if="validationErrors.confirmPassword" class="mt-1 text-sm error-color">
                  {{ validationErrors.confirmPassword }}
                </div>
              </div>
  
              <!-- Conditions d'utilisation -->
              <div class="space-y-4">
                <div class="flex items-start">
                  <input
                    id="acceptTerms"
                    v-model="registerData.acceptTerms"
                    type="checkbox"
                    class="checkbox-style"
                    required
                  >
                  <label for="acceptTerms" class="ml-2 block text-sm text-gray-700">
                    I agree to the  
                    <button type="button" @click="showTerms = true" class="font-medium bg-lightgray px-2" >
                      Terms of Use
                    </button>
                    and 
                    <button type="button" @click="showPrivacy = true" class="font-medium bg-lightgray  px-2" >
                      Privacy Policy
                    </button>
                  </label>
                </div>
  
                <div class="flex items-start">
                  <input
                    id="acceptMarketing"
                    v-model="registerData.acceptMarketing"
                    type="checkbox"
                    class="checkbox-style"
                  >
                  <label for="acceptMarketing" class="ml-2 block text-sm text-gray-700">
                    I agree to receive marketing email and notifications (optional)
                  </label>
                </div>
              </div>
  
              <!-- Résumé du compte -->
            </div>
  
            <!-- Navigation Buttons -->
            <div class="flex justify-between mt-8">
              <button 
                v-if="currentStep > 0"
                @click="currentStep--"
                type="button"
                class="btn-gray"
              >
                Previous
              </button>
              <div v-else></div>
  
              <button 
                v-if="currentStep < steps.length - 1"
                @click="nextStep"
                type="button"
                :disabled="isCheckingEmail"
                class="btn-degrade-orange"
              >
                <svg v-if="isCheckingEmail" class="animate-spin -ml-1 mr-3 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                  <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                  <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                {{ isCheckingEmail ? 'Checking...' : 'Next' }}
              </button>
              <button 
                v-else
                type="submit"
                :disabled="isLoading || !registerData.acceptTerms"
                style="background-color: #10b981;"
                class="disabled:opacity-50 disabled:cursor-not-allowed submit-btn"
              >
                <svg v-if="isLoading" class="animate-spin -ml-1 mr-3 h-5 w-5 text-white inline" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                  <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                  <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                {{ isLoading ? 'Creating' : 'Create' }}
              </button>
            </div>
          </form>
  
          <!-- Login Link -->
          <div class="mt-6 text-center">
            <p class="text-sm text-gray-600">
              Do you already have an account ?
              <button
                type="button"
                @click="handleLogin"
                class="bg-lightgray px-2"
              >
                Login
              </button>
            </p>
          </div>
        </div>
      </div>
  
      <!-- Forgot Password Modal -->
      <div v-if="showForgotPassword" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4" @click="showForgotPassword = false">
        <div class="bg-white rounded-lg p-6 max-w-md w-full mx-4" @click.stop>
          <h3 class="text-lg font-medium text-gray-900 mb-4">Reset password</h3>
          <p class="text-sm text-gray-600 mb-4">
            Enter your email to receive a reset link.
          </p>
          <form @submit.prevent="handleForgotPassword">
            <input
              v-model="forgotPasswordEmail"
              type="email"
              required
              class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-orange focus:border-orange mb-4"
              :placeholder="registerData.email || 'Votre adresse email'"
            >
            <div class="flex space-x-3">
              <button
                type="button"
                @click="showForgotPassword = false"
                class="flex-1 py-2 px-4 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50"
              >
                Cancel
              </button>
              <button
                type="submit"
                :disabled="isLoadingForgot"
                class="flex-1 py-2 px-4 bg-orange text-white rounded-lg hover:bg-orange-600 disabled:opacity-50"
              >
                {{ isLoadingForgot ? 'Sending...' : 'Send' }}
              </button>
            </div>
          </form>
        </div>
      </div>
  
      <!-- Terms Modal -->
      <div v-if="showTerms" class="fixed inset-0 bg-black/60 bg-opacity-50 flex items-center justify-center z-50 p-4" @click="showTerms = false">
        <div class="bg-white rounded-lg p-6 max-w-2xl w-full max-h-[80vh] overflow-y-auto" @click.stop>
          <h3 class="text-lg font-medium text-gray-900 mb-4">Terms of Use</h3>
          <div class="text-sm text-gray-600 space-y-4">
            <p>By using Wabili, you agree to the following conditions...</p>
            <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.</p>
            <!-- Ajoutez ici le contenu complet des conditions -->
          </div>
          <div class="flex justify-end mt-6">
            <button
              @click="showTerms = false"
              class="px-4 py-2 bg-orange text-white rounded-lg hover:bg-orange-600"
            >
              Close
            </button>
          </div>
        </div>
      </div>
  
      <!-- Privacy Modal -->
      <div v-if="showPrivacy" class="fixed inset-0 bg-black/60 bg-opacity-50 flex items-center justify-center z-50 p-4" @click="showPrivacy = false">
        <div class="bg-white rounded-lg p-6 max-w-2xl w-full max-h-[80vh] overflow-y-auto" @click.stop>
          <h3 class="text-lg font-medium text-gray-900 mb-4">Privacy Policy</h3>
          <div class="text-sm text-gray-600 space-y-4">
            <p>Your privacy is important to us...</p>
            <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.</p>
            <!-- Ajoutez ici le contenu complet de la politique -->
          </div>
          <div class="flex justify-end mt-6">
            <button
              @click="showPrivacy = false"
              class="px-4 py-2 bg-orange text-white rounded-lg hover:bg-orange-600"
            >
              Close
            </button>
          </div>
        </div>
      </div>
    </div>
  </template>
  
  <script setup>
  import { ref, reactive, computed } from 'vue'
  import { useRouter } from 'vue-router'
  import { usersApi } from '../../services/api.js'
  import countries from '/src/assets/countries.json'
import { UploadIcon, User, XIcon } from 'lucide-vue-next'
  
  const router = useRouter()
  
  // Reactive data
  const currentStep = ref(0)
  const isLoading = ref(false)
  const isCheckingEmail = ref(false)
  const isLoadingForgot = ref(false)
  const showPassword = ref(false)
  const showConfirmPassword = ref(false)
  const showTerms = ref(false)
  const showPrivacy = ref(false)
  const showForgotPassword = ref(false)
  const emailExists = ref(false)
  const error = ref('')
  const successMessage = ref('')
  const forgotPasswordEmail = ref('')
  let countryList = ref([])
  
  
  const steps = [
    { title: 'Personal informations' },
    { title: 'Security and finalization' }
  ]
  
  const registerData = reactive({
    profileImage:[],
    fullName: '',
    email: '',
    phone: '',
    companyName: '',
    country: '',
    city: '',
    password: '',
    confirmPassword: '',
    acceptTerms: false,
    acceptMarketing: false
  })
  
  const validationErrors = reactive({
    fullName: '',
    email: '',
    phone: '',
    companyName: '',
    country: '',
    password: '',
    confirmPassword: ''
  })
  
   
  // Computed properties
   const handleLogin = () => {
    router.push('/login')
  }
  const passwordStrength = computed(() => {
    
    const password = registerData.password
    if (!password) return { width: '0%', color: 'bg-gray-300', text: '', textColor: 'text-gray-500' }
    
    let score = 0
    let feedback = []
    
    // Longueur
    if (password.length >= 8) score += 1
    else feedback.push('8+ caractères')
    
    // Majuscules
    if (/[A-Z]/.test(password)) score += 1
    else feedback.push('majuscule')
    
    // Minuscules
    if (/[a-z]/.test(password)) score += 1
    else feedback.push('minuscule')
    
    // Chiffres
    if (/\d/.test(password)) score += 1
    else feedback.push('chiffre')
    
    // Caractères spéciaux
    if (/[!@#$%^&*(),.?":{}|<>]/.test(password)) score += 1
    else feedback.push('caractère spécial')
    
    if (score <= 2) {
      return { 
        width: '33%', 
        color: 'bg-red-500', 
        text: 'Faible', 
        textColor: 'text-red-500' 
      }
    } else if (score <= 3) {
      return { 
        width: '66%', 
        color: 'bg-yellow-500', 
        text: 'Moyen', 
        textColor: 'text-yellow-600' 
      }
    } else {
      return { 
        width: '100%', 
        color: 'bg-green-500', 
        text: 'Fort', 
        textColor: 'text-green-600' 
      }
    }
  })
  
  // Methods
 const getCountryList=()=> {
  for (let index = 0; index < countries.length; index++) {

     countryList.value.push(countries[index].name);
    
  }
  return countries.map(c => c.name);
}


  // Fonction pour vérifier si l'email existe
  const checkEmailExists = async (email) => {
    getCountryList()
    
    try {
      isCheckingEmail.value = true
      
      // Appel à l'API pour vérifier l'email
      const response = await usersApi.checkEmail2(email);
      return response.data.exists;
      
    } catch (err) {
      console.error('Erreur lors de la vérification de l\'email:', err)
      error.value = err.message || 'Error to check email'
      return false
    } finally {
      isCheckingEmail.value = false
    }
  }
  
  const clearEmailExistsError = () => {
    emailExists.value = false
    error.value = ''
  }
  
  const goToLogin = () => {
    router.push('/login')
  }

  //Function pour chnger la photo de profile
  const handleProfileImageUpload = (event) => {
  const files = Array.from(event.target.files)
  const remainingSlots = 8 - registerData.profileImage.length
  
  files.slice(0, remainingSlots).forEach(file => {
    if (file.size <= 10 * 1024 * 1024) {
      const reader = new FileReader()
      reader.onload = (e) => {
        registerData.profileImage= [{
        file: file,
        preview: e.target.result,
        uploading: false,
        uploadProgress: 0,
        uploaded: false,
        url: null
      }]
      }
      reader.readAsDataURL(file)
    }
  })
}
  
  // Fonction pour réinitialiser le mot de passe
  const handleForgotPassword = async () => {
    if (!forgotPasswordEmail.value) {
      forgotPasswordEmail.value = registerData.email
    }
    
    if (!forgotPasswordEmail.value) {
      error.value = 'Enter your email'
      return
    }
    
    try {
      isLoadingForgot.value = true
      
      // Appel à l'API pour réinitialiser le mot de passe
      const response = await usersApi.forgotPassword(forgotPasswordEmail.value);
      
      successMessage.value = response.data?.message || 'Reset instruction sent !'
      showForgotPassword.value = false
      forgotPasswordEmail.value = ''
      
    } catch (err) {
      error.value = err.message || 'Error to send. Try again.'
    } finally {
      isLoadingForgot.value = false
    }
  }
  
  // Fonction pour soumettre le formulaire d'inscription
  const handleSubmit = async () => {
    console.log('lala')
    if (!validateCurrentStep()) return
    
    try {
      isLoading.value = true
      error.value = ''
      
      // Préparer les données pour l'API
      const userData = {
        profile_image: registerData.profileImage,
        full_name: registerData.fullName,
        email: registerData.email,
        phone: registerData.phone,
        password: registerData.password,
        pays: 'aucun'
      }
      
      // Appel à l'API pour créer le compte
      const response = await usersApi.register_client(userData);
      
      successMessage.value = response.message || 'Create account success ! Redirecting to login...'
      
      // Rediriger vers la page de connexion après 2 secondes
      setTimeout(() => {
        router.push('/login')
      }, 2000)
      
    } catch (err) {
      error.value = err.message || 'Error to create account'
    } finally {
      isLoading.value = false
    }
  }
  
  const validateCurrentStep = () => {
    // Reset errors
    Object.keys(validationErrors).forEach(key => {
      validationErrors[key] = ''
    })
    
    let isValid = true
    
    if (currentStep.value === 0) {
      // Validation étape 1
      if (!registerData.fullName.trim()) {
        validationErrors.fullName = 'Full name is required'
        isValid = false
      }
      
      if (!registerData.email.trim()) {
        validationErrors.email = 'Email is required'
        isValid = false
      } else if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(registerData.email)) {
        validationErrors.email = 'Invalid Format email '
        isValid = false
      }
      
      if (!registerData.phone.trim()) {
        validationErrors.phone = 'Phone number is required'
        isValid = false
      } else if (!/^(\+?[1-9]\d{1,14}|0[1-9]\d{8,9})$/.test(registerData.phone.replace(/\s/g, ''))) {
        validationErrors.phone = 'invalid phone number'
        isValid = false
      }
    } else if (currentStep.value === 1) {
      // Validation étape 3
      if (!registerData.password) {
        validationErrors.password = 'Password is required'
        isValid = false
      } else if (registerData.password.length < 8) {
        validationErrors.password = 'Password must contain at least 8 caracters'
        isValid = false
      }
      
      if (!registerData.confirmPassword) {
        validationErrors.confirmPassword = 'Confirm password is required'
        isValid = false
      } else if (registerData.password !== registerData.confirmPassword) {
        validationErrors.confirmPassword = "Passwords don't match"
        isValid = false
      }
      
      if (!registerData.acceptTerms) {
        error.value = 'You must accept the Terms of Use'
        isValid = false
      }
    }
    
    return isValid
  }
  
  const nextStep = async () => {
    if (!validateCurrentStep()) return
    
    // Si on passe de l'étape 0 à l'étape 1, vérifier l'email
    if (currentStep.value === 0) {
      const emailExistsInDb = await checkEmailExists(registerData.email)
      
      if (emailExistsInDb) {
        emailExists.value = true
        return // Ne pas passer à l'étape suivante
      }
    }
    
    // Passer à l'étape suivante
    currentStep.value++
    error.value = ''
    emailExists.value = false
  }
  
  </script>
  
  <style scoped>

  </style>