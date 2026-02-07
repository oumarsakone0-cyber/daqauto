"use client"

import { ref } from "vue"
import { useRouter } from "vue-router"
import { usersApi, authUtils, boutiqueUtils } from "@/services/api"

export const useAuth = () => {
  const router = useRouter()
  const isLoading = ref(false)
  const error = ref("")
  const successMessage = ref("")

  const handleLogin = async (loginData) => {
    if (!validateForm(loginData)) return false

    try {
      isLoading.value = true
      error.value = ""

      const loginPayload = {
        identifier: loginData.identifier.trim(),
        password: loginData.password,
      }


      // Appel à l'API de connexion
      const response = await usersApi.login(loginPayload)

      if (response.success) {
        // Stocker les informations d'authentification
        const userData = response.data

        // Utiliser les clés cohérentes avec votre API
        localStorage.setItem("auth_token", userData.session_token || userData.token)
        localStorage.setItem(
          "user_data",
          JSON.stringify({
            id: userData.id,
            full_name: userData.full_name,
            email: userData.email,
            phone: userData.phone,
            role: userData.role,
            avatar: userData.avatar,
            boutiques: userData.boutiques || [],
          }),
        )

        // Si l'option "Se souvenir de moi" est cochée
        if (loginData.rememberMe) {
          localStorage.setItem("rememberMe", "true")
        } else {
          localStorage.removeItem("rememberMe")
        }

        successMessage.value = "Connexion réussie ! Redirection en cours..."


        // Rediriger vers le tableau de bord ou la page d'accueil
        setTimeout(() => {
          if (userData.boutiques && userData.boutiques.length > 0) {
            const firstBoutique = userData.boutiques[0]
            router.push(`/dashboard/${firstBoutique.slug || firstBoutique.id}`)
          } else {
            router.push("/dashboard")
          }
        }, 1000)

        return true
      } else {
        error.value = response.error || "Erreur de connexion. Veuillez réessayer."
        return false
      }
    } catch (err) {
      console.error("❌ Erreur de connexion:", err)

      // Gestion des différents types d'erreurs
      if (err.response?.status === 401) {
        error.value = "Identifiants incorrects. Veuillez vérifier votre email/téléphone et mot de passe."
      } else if (err.response?.status === 429) {
        error.value = "Trop de tentatives de connexion. Veuillez patienter quelques minutes."
      } else if (err.response?.status >= 500) {
        error.value = "Erreur serveur. Veuillez réessayer plus tard."
      } else {
        error.value = err.response?.data?.error || err.message || "Erreur de connexion. Vérifiez vos identifiants."
      }

      return false
    } finally {
      isLoading.value = false
    }
  }

  const validateForm = (loginData) => {
    if (!loginData.identifier?.trim()) {
      error.value = "Veuillez saisir votre email ou numéro de téléphone"
      return false
    }

    if (!loginData.password) {
      error.value = "Veuillez saisir votre mot de passe"
      return false
    }

    if (loginData.password.length < 6) {
      error.value = "Le mot de passe doit contenir au moins 6 caractères"
      return false
    }

    return true
  }

  const handleLogout = async () => {
    try {
      isLoading.value = true

      // Appeler l'API de déconnexion si nécessaire
      await usersApi.logout()

      // Nettoyer le localStorage
      authUtils.logout()

      successMessage.value = "Déconnexion réussie"

      // Rediriger vers la page de connexion
      router.push("/login")

    } catch (err) {
      console.error("❌ Erreur lors de la déconnexion:", err)
      // Même en cas d'erreur, on nettoie les données locales
      authUtils.logout()
      router.push("/login")
    } finally {
      isLoading.value = false
    }
  }

  const checkAuthStatus = () => {
    const token = authUtils.getToken()
    const user = authUtils.getUser()

    if (!token || !user) {
      return false
    }

    // Vérifier si le token n'est pas expiré (si vous avez cette info)
    // const tokenExpiry = localStorage.getItem('token_expiry')
    // if (tokenExpiry && new Date() > new Date(tokenExpiry)) {
    //   authUtils.logout()
    //   return false
    // }

    return true
  }

  const getCurrentUser = () => {
    return boutiqueUtils.getCurrentUser()
  }

  const getCurrentBoutique = () => {
    return boutiqueUtils.getCurrentBoutique()
  }

  return {
    isLoading,
    error,
    successMessage,
    handleLogin,
    handleLogout,
    validateForm,
    checkAuthStatus,
    getCurrentUser,
    getCurrentBoutique,
  }
}
