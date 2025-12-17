<template>
    <div class="min-h-screen bg-gradient-to-br from-gray-50 to-gray-100 flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
      <div class="max-w-md w-full space-y-8">
        <!-- Header -->
        <div class="text-center">
          <div class="mx-auto h-16 w-45  rounded-full flex items-center justify-center mb-4 ">
            <img src="../../assets/logo.png" alt="DaqAuto Logo" class="logo" />
          </div>
          <h2 class="text-3xl font-bold text-gray-900 mb-2">Login</h2>
          <p class="text-gray-600">Login to your DaqAuto account</p>
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
  
          <!-- Success Message -->
          <div v-if="successMessage" class="mb-6 p-4 bg-green-50 border border-green-200 rounded-lg">
            <div class="flex items-center">
              <svg class="w-5 h-5 text-green-400 mr-2" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
              </svg>
              <span class="success-color text-sm">{{ successMessage }}</span>
            </div>
          </div>
  
          <form @submit.prevent="handleLogin" class="space-y-6">
            <!-- Login Field (Email or Phone) -->
            <div>
              <label for="login" class="block text-sm font-medium text-gray-700 mb-2">
                Email or phone number
              </label>
              <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                  <svg v-if="loginType === 'email'" class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207"/>
                  </svg>
                  <svg v-else class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                  </svg>
                </div>
                <input
                  id="login"
                  v-model="loginData.identifier"
                  type="text"
                  required
                  :class="[
                    ' input-style',
                    validationErrors.identifier ? 'border-red-300 bg-red-50' : 'border-gray-300'
                  ]"
                  :placeholder="loginType === 'email' ? 'exemple@email.com' : '+225 XX XX XX XX XX'"
                  @input="detectLoginType"
                >
              </div>
              <div v-if="validationErrors.identifier" class="mt-1 text-sm error-color">
                {{ validationErrors.identifier }}
              </div>
              <div v-if="loginData.identifier" class="mt-1 text-xs text-gray-500">
                Detect as  : {{ loginType === 'email' ? 'Adresse email' : 'Numéro de téléphone' }}
              </div>
            </div>
  
            <!-- Password Field -->
            <div>
              <label for="password" class="block text-sm font-medium text-gray-700 mb-2">
                Password
              </label>
              <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                  <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                  </svg>
                </div>
                <input
                  id="password"
                  v-model="loginData.password"
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
            </div>
  
            <!-- Remember Me & Forgot Password -->
            <div class="flex items-center justify-between">
              <div class="flex items-center">
                <input
                  id="remember-me"
                  v-model="loginData.rememberMe"
                  type="checkbox"
                  class="checkbox-style"
                >
                <label for="remember-me" class="ml-2 block text-sm text-gray-700">
                  Remember me
                </label>
              </div>
  
              <button
                type="button"
                @click="showForgotPassword = true"

                class="text-sm font-medium bg-lightgray px-2"
              >
                Forgot password ?
              </button>
            </div>
  
            <!-- Submit Button -->
            <button
              type="submit"
              :disabled="isLoading"
              class="shadow-sm text-sm w-full disabled:opacity-50 disabled:cursor-not-allowed btn-degrade-orange px-4 py-4"
            >
              <svg v-if="isLoading" class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
              </svg>
              {{ isLoading ? 'Loging...' : 'Login' }}
            </button>
          </form>
  
          <!-- Divider -->
          <!-- <div class="mt-6">
            <div class="relative">
              <div class="absolute inset-0 flex items-center">
                <div class="w-full border-t border-gray-300"></div>
              </div>
              <div class="relative flex justify-center text-sm">
                <span class="px-2 bg-white text-gray-500">Ou</span>
              </div>
            </div>
          </div> -->
  
          <!-- Social Login 
          <div class="mt-6 grid grid-cols-2 gap-3">
            <button
              type="button"
              @click="loginWithGoogle"
              class="w-full inline-flex justify-center py-2 px-4 border border-gray-300 rounded-lg shadow-sm bg-white text-sm font-medium text-gray-500 hover:bg-gray-50 transition-colors"
            >
              <svg class="w-5 h-5" viewBox="0 0 24 24">
                <path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/>
                <path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/>
                <path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z"/>
                <path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z"/>
              </svg>
              <span class="ml-2">Google</span>
            </button>
  
            <button
              type="button"
              @click="loginWithFacebook"
              class="w-full inline-flex justify-center py-2 px-4 border border-gray-300 rounded-lg shadow-sm bg-white text-sm font-medium text-gray-500 hover:bg-gray-50 transition-colors"
            >
              <svg class="w-5 h-5" fill="#1877F2" viewBox="0 0 24 24">
                <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>
              </svg>
              <span class="ml-2">Facebook</span>
            </button>
          </div>

          -->
  
          <!-- Sign Up Link -->
          <div class="mt-6 text-center">
            <p class="text-sm text-gray-600">
              Don't you have an account ?
              <button
                type="button"
                @click="handleSignup"
                class="bg-lightgray"
              >
                Register
              </button>
            </p>
          </div>
        </div>
      </div>
  
      <!-- Forgot Password Modal -->
      <div v-if="showForgotPassword" class="fixed inset-0 bg-gray-300 bg-opacity-50 flex items-center justify-center z-50" @click="showForgotPassword = false">
        <div class="bg-white rounded-lg p-6 max-w-md w-full mx-4" @click.stop>
          <h3 class="text-lg font-medium text-gray-900 mb-4">Reset password</h3>
          <p class="text-sm text-gray-600 mb-4">
            Enter your email to receive a reset link.
          </p>
          <form @submit.prevent="handleForgotPassword">
           <div class="relative justify-center items-center">
            <<div class="absolute inset-y-0 left-0 pl-3 pt-2 flex items-center pointer-events-none">
                  <svg  class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207"/>
                  </svg>
                 
                </div>
              <input
              v-model="forgotPasswordData.identifier"
              type="text"
              required
              class="mb-4 input-style"
              placeholder="Email or phone number"
            >
           </div> 
           
            <div class="flex space-x-3">
              <button
                type="button"
                @click="showForgotPassword = false"
                class="flex-1 bg-lightgray"
              >
                Cancel
              </button>
              <button
                type="submit"
                :disabled="isLoadingForgot"
                class="flex-1 disabled:opacity-50 btn-degrade-orange"
              >
                {{ isLoadingForgot ? 'Sending...' : 'Send' }}
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </template>
  
  <script setup>
  import { ref, reactive, computed, onMounted } from 'vue'
  import { useRouter } from 'vue-router'
  import { ElNotification } from 'element-plus'
  import { usersApi } from '../../services/api.js'
  
  const router = useRouter()
  
  // Reactive data
  const loginData = reactive({
    identifier: '',
    password: '',
    rememberMe: false
  })
  
  const forgotPasswordData = reactive({
    identifier: ''
  })
  
  const isLoading = ref(false)
  const isLoadingForgot = ref(false)
  const showPassword = ref(false)
  const showForgotPassword = ref(false)
  const error = ref('')
  const successMessage = ref('')
  const validationErrors = reactive({
    identifier: '',
    password: ''
  })
  
  // Fonctions de validation
  const isValidEmail = (email) => /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)
  const isValidPhone = (phone) => /^(\+?[1-9]\d{1,14}|0[1-9]\d{8,9})$/.test(phone.replace(/\s/g, ''))
  
  // Computed properties
  const loginType = computed(() => {
    if (!loginData.identifier) return 'email'
    const clean = loginData.identifier.replace(/\s/g, '')
    return isValidEmail(loginData.identifier) ? 'email' : isValidPhone(clean) ? 'phone' : 'email'
  })
  
  // Methods
  const detectLoginType = () => {
    validationErrors.identifier = ''
    error.value = ''
  }
  
  const validateForm = () => {
    let isValid = true
  
    validationErrors.identifier = ''
    validationErrors.password = ''
  
    if (!loginData.identifier) {
      validationErrors.identifier = 'Ce champ est requis'
      isValid = false
    } else {
      const cleanIdentifier = loginData.identifier.replace(/\s/g, '')
      if (!isValidEmail(loginData.identifier) && !isValidPhone(cleanIdentifier)) {
        validationErrors.identifier = 'Veuillez entrer un email ou un numéro de téléphone valide'
        isValid = false
      }
    }
  
    if (!loginData.password) {
      validationErrors.password = 'Le mot de passe est requis'
      isValid = false
    } else if (loginData.password.length < 6) {
      validationErrors.password = 'Le mot de passe doit contenir au moins 6 caractères'
      isValid = false
    }
  
    return isValid
  }
  
  const handleLogin = async () => {
    if (!validateForm()) return
  
    try {
      isLoading.value = true
      error.value = ''
  
      const loginPayload = {
        identifier: loginData.identifier.trim(),
        password: loginData.password
      }
  
      const response = await usersApi.login(loginPayload)
  
      if (response.success) {
        const userData = response.data
  
        // Choisir le bon stockage
        const storage = loginData.rememberMe ? localStorage : sessionStorage
  
        storage.setItem('authToken', userData.session_token)
        storage.setItem('user', JSON.stringify({
          id: userData.id,
          full_name: userData.full_name,
          email: userData.email,
          boutiques: userData.boutiques
        }))
  
        if (loginData.rememberMe) {
          localStorage.setItem('rememberMe', 'true')
        } else {
          localStorage.removeItem('rememberMe')
        }
  
        successMessage.value = 'Connexion réussie ! Redirection en cours...'
  
        ElNotification({
          title: 'Succès',
          message: 'Connexion réussie !',
          type: 'success'
        })
  
        setTimeout(() => {
            router.push('/boutique-admin/dashboard')
        }, 1500)
  
      } else {
        error.value = response.error || 'Erreur de connexion. Veuillez réessayer.'
        localStorage.removeItem('authToken')
        localStorage.removeItem('user')
  
        ElNotification({
          title: 'Erreur',
          message: error.value,
          type: 'error'
        })
      }
  
    } catch (err) {
      console.error('Erreur de connexion:', err)
      error.value = err.response?.data?.error || 'Erreur de connexion. Vérifiez vos identifiants.'
      localStorage.removeItem('authToken')
      localStorage.removeItem('user')
  
      ElNotification({
        title: 'Erreur',
        message: error.value,
        type: 'error'
      })
    } finally {
      isLoading.value = false
    }
  }
  
const handleForgotPassword = async () => {
  const email = forgotPasswordData.identifier?.trim()

  if (!email) {
    error.value = 'Veuillez entrer votre email ou numéro de téléphone'
    return
  }

  try {
    isLoadingForgot.value = true
    error.value = ''


    // Appel API -> on envoie juste la valeur
    const response = await usersApi.forgotPassword(email)

    successMessage.value =
      response.data?.message || 'Instructions de réinitialisation envoyées !'

    // Reset UI
    showForgotPassword.value = false
    forgotPasswordData.identifier = ''

    // Notification de succès
    ElNotification({
      title: 'Succès',
      message: successMessage.value,
      type: 'success',
    })
  } catch (err) {
    console.error('Erreur mot de passe oublié:', err)

    error.value =
      err.response?.data?.error ||
      err.message ||
      "Erreur lors de l'envoi. Veuillez réessayer."

    ElNotification({
      title: 'Erreur',
      message: error.value,
      type: 'error',
    })
  } finally {
    isLoadingForgot.value = false
  }
}

  const handleSignup = () => {
    router.push('/boutique-admin/register')
  }
  
  const loginWithGoogle = () => {
    error.value = 'La connexion avec Google n\'est pas encore disponible.'
    ElNotification({
      title: 'Info',
      message: error.value,
      type: 'warning'
    })
  }
  
  const loginWithFacebook = () => {
    error.value = 'La connexion avec Facebook n\'est pas encore disponible.'
    ElNotification({
      title: 'Info',
      message: error.value,
      type: 'warning'
    })
  }
  
  onMounted(() => {
    const token = localStorage.getItem('authToken') || sessionStorage.getItem('authToken')
    if (token) {
     // router.push('/boutique-admin/dashboard')
    }
  })
  </script>  
  
  <style scoped>

  </style>