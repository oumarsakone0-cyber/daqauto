<template>
  <div class="bg-cream">
    <!-- Main Layout -->
    <div class="flex-1 flex flex-col">
      <!-- Navbar - masquée sur les pages d'authentification et dashboard admin -->
      <Navbar 
        v-if="!hideNavbar"
        style="position: fixed; width: 100%; z-index: 1000; height: 121px;"
      />
      
      <!-- Main Content -->
      <div :class="[
        'flex-1'
      ]" :style="contentPadding">
        <router-view />
      </div>

       <Footer 
        v-if="!hideNavbar"
        style="width: 100%; z-index: 1000"
      />
    </div>
  </div>
</template>

<script setup>
import { computed, ref, watch } from 'vue'
import { useRoute } from 'vue-router'
import Navbar from './components/menu/Navbar.vue'
import Footer from './components/menu/Footer.vue'

const route = useRoute()

// Computed pour vérifier si on est sur une page d'authentification ou dashboard admin
const shouldHideNavbar = computed(() => {
  const isAuthPage = route.name === 'login' || route.name === 'register'
  const isDashboardAdmin = route.path.startsWith('/dashboard-admin/')
  const isBoutiquedAdmin = route.path.startsWith('/boutique-admin/')
  
  return isAuthPage || isDashboardAdmin || isBoutiquedAdmin
})

// Computed pour le padding du contenu
const contentPadding = computed(() => {
  if (shouldHideNavbar.value) {
    return 'padding: 0px;'
  }
  return 'padding: 123px 0px 0px 0px;'
})

// Use a ref to store the hideNavbar value, so it's reactive
const hideNavbar = ref(shouldHideNavbar.value)

// Watch the route and update hideNavbar
watch(shouldHideNavbar, (newValue) => {
  hideNavbar.value = newValue
})
</script>

<style scoped>
.bg-cream {
  background-color: #f4f4f4;
}
</style>