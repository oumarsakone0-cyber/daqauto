// src/router/index.js
import { createRouter, createWebHistory } from "vue-router"
import Home from "../components/views/Home.vue"
import Messages from "../components/pages/Messages.vue"
import About from "../components/clients/Clients.vue"
import Login from "../components/login/Login.vue"
import Register from "../components/login/Register.vue"
import SearchProduct from "../components/views/ResearchList.vue"
import DetailProduct from "../components/views/ProductDetail.vue"
import Boutique from "../components/views/Boutique.vue"
import Dashboard from "../components/views/Dashboard.vue"
import CategoriesManagement from "../components/views/categories-management.vue"
import ChatsManagement from "../components/views/chat-management.vue"
import CommandesManagement from "../components/views/commandes-management.vue"
import ProduitsManagement from "../components/views/produits-management.vue"
import BoutiquesManagement from "../components/views/boutiques-management.vue"

import BoutiqueAdminLogin from "../components/boutiques/Login.vue"
import BoutiqueAdminReset from "../components/boutiques/Reset_password.vue"
import BoutiqueAdminRegister from "../components/boutiques/Register.vue"
import BoutiqueAdminDashboard from "../components/boutiques/DashboardContent.vue"
import Boutiquee from "../components/boutiques/Boutique.vue"


const routes = [
  {
    path: "/",
    name: "home",
    component: Home,
    meta: {
      requiresAuth: false,
      hideNavigation: false,
    },
  },
  {
    path: "/dashboard-admin/dashboard",
    name: "dashboard-admin",
    component: Dashboard,
    meta: {
      requiresAuth: false,
      hideNavigation: false,
    },
  },
  {
    path: "/trabi",
    name: "trabi",
    component: Boutiquee,
    meta: {
      requiresAuth: false,
      hideNavigation: false,
    },
  },
  {
    path: "/dashboard-admin/categories",
    name: "dashboard-categories",
    component: CategoriesManagement,
    meta: {
      requiresAuth: false,
      hideNavigation: false,
    },
  },
  {
    path: "/dashboard-admin/chats",
    name: "dashboard-chats",
    component: ChatsManagement,
    meta: {
      requiresAuth: false,
      hideNavigation: false,
    },
  },
  {
    path: "/dashboard-admin/commandes",
    name: "dashboard-commandes",
    component: CommandesManagement,
    meta: {
      requiresAuth: false,
      hideNavigation: false,
    },
  },
  {
    path: "/dashboard-admin/produits",
    name: "dashboard-produits",
    component: ProduitsManagement,
    meta: {
      requiresAuth: false,
      hideNavigation: false,
    },
  },
  {
    path: "/dashboard-admin/boutiques",
    name: "dashboard-boutiques",
    component: BoutiquesManagement,
    meta: {
      requiresAuth: false,
      hideNavigation: false,
    },
  },
  {
    path: "/recherche_de_produit_list",
    name: "recherche_de_produit_list",
    component: SearchProduct,
    meta: {
      requiresAuth: false,
      hideNavigation: false,
    },
  },
  {
    path: "/detail_resultat_produit/:slug",
    name: "detail_resultat_produit",
    component: DetailProduct,
    meta: {
      requiresAuth: false,
      hideNavigation: false,
    },
  },
  {
    path: "/boutique_commercant/:id",
    name: "boutique_commercant",
    component: Boutique,
    meta: {
      requiresAuth: false,
      hideNavigation: false,
    },
  },
  {
    path: "/messages",
    name: "messages",
    component: Messages,
    meta: {
      requiresAuth: true,
      hideNavigation: false,
    },
  },
  {
    path: "/clients",
    name: "clients",
    component: About,
    meta: {
      requiresAuth: true,
      hideNavigation: false,
    },
  },
  {
    path: "/login",
    name: "login",
    component: Login,
    meta: {
      requiresAuth: false,
      hideNavigation: true,
    },
  },
  {
    path: "/register",
    name: "register",
    component: Register,
    meta: {
      requiresAuth: false,
      hideNavigation: true,
    },
  },
 
  //POUR LES BOUTIQUES
  {
    path: "/boutique-admin/login",
    name: "boutique-login",
    component: BoutiqueAdminLogin,
    meta: {
      requiresAuth: false,
      hideNavigation: true,
    },
  },
  {
    path: "/boutique-admin/reset_password",
    name: "boutique-reset_password",
    component: BoutiqueAdminReset,
    meta: {
      requiresAuth: false,
      hideNavigation: true,
    },
  },
  {
    path: "/boutique-admin/register",
    name: "boutique-register",
    component: BoutiqueAdminRegister,
    meta: {
      requiresAuth: false,
      hideNavigation: true,
    },
  },
  {
    path: "/boutique-admin/dashboard",
    name: "boutique-dashboard",
    component: BoutiqueAdminDashboard,
    meta: {
      requiresAuth: false,
      hideNavigation: true,
    },
  },

  // Route de redirection pour les chemins non trouvés
  {
    path: "/:pathMatch(.*)*",
    redirect: "/",
  },
]

const router = createRouter({
  history: createWebHistory(),
  routes,
})

// Guard de navigation pour protéger les routes
router.beforeEach((to, from, next) => {
  // Vérifier si l'utilisateur est connecté
  const isAuthenticated = checkAuthStatus()

  // Si la route nécessite une authentification
  if (to.meta.requiresAuth && !isAuthenticated) {
    // Rediriger vers la page de connexion
    next("/login")
  }
  // Si l'utilisateur est connecté et essaie d'accéder aux pages d'auth
  else if ((to.name === "login" || to.name === "register") && isAuthenticated) {
    // Rediriger vers le dashboard
    next("/")
  }
  // Sinon, continuer normalement
  else {
    next()
  }
})

// Fonction pour vérifier le statut d'authentification
function checkAuthStatus() {
  const token = localStorage.getItem("authToken")
  const user = localStorage.getItem("user")
  return !!(token && user)
}

export default router
