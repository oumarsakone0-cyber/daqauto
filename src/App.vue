<template>
  <div class="bg-cream">
    <!-- Main Layout -->
    <div class="flex-1 flex flex-col">
      <!-- Auto Translator - Position fixe en haut à droite 
      <div class="fixed top-4 right-4 z-50">
        <AutoTranslator />
      </div>
       -->
      <!-- Navbar -->
      <Navbar 
        v-if="!hideNavbar"
        style="position: fixed; width: 100%; z-index: 1000; height: 121px;"
      />
      
      <!-- Main Content -->
      <div :class="['flex-1']" :style="contentPadding">
        <router-view />
      </div>

      <Footer 
        v-if="!hideNavbar"
        style="width: 100%; z-index: 1000"
      />
    </div>
  </div>
</template>

<script>
import { computed, ref, watch, onMounted } from 'vue'
import { useRoute } from 'vue-router'
import Navbar from './components/menu/Navbar.vue'
import Footer from './components/menu/Footer.vue'
import AutoTranslator from './components/traduction/AutoTranslator.vue'
import { useChatStore } from './stores/chat'
import TabBar from './components/menu/TabBar.vue'

export default {
  name: 'App',
  components: {
    Navbar,
    Footer,
    TabBar,
    AutoTranslator
  },
  setup() {
    const route = useRoute()
    const hideNavbar = ref(false)
    const chatStore = useChatStore()

    const shouldHideNavbar = computed(() => {
      const isAuthPage = route.name === 'login' || route.name === 'register'
      const isDashboardAdmin = route.path.startsWith('/dashboard-admin/')
      const isBoutiquedAdmin = route.path.startsWith('/boutique-admin/')

      return isAuthPage || isDashboardAdmin || isBoutiquedAdmin
    })

    const contentPadding = computed(() => {
      if (hideNavbar.value) {
        return 'padding: 0px;'
      }
      return 'padding: 123px 0px 0px 0px;'
    })

    onMounted(async () => {
      hideNavbar.value = shouldHideNavbar.value

      // Initialiser le chat store si l'utilisateur est connecté
      const userRaw = localStorage.getItem('user') || sessionStorage.getItem('user')
      if (userRaw) {
        try {
          await chatStore.initChatStore()
          console.log('✅ Chat store initialisé')
        } catch (error) {
          console.error('❌ Erreur initialisation chat store:', error)
        }
      }
    })

    watch(shouldHideNavbar, (newValue) => {
      hideNavbar.value = newValue
    }, { immediate: true })

    return {
      hideNavbar,
      contentPadding
    }
  }
}
</script>

<style scoped>
.bg-cream {
  background-color: #f4f4f4;
}
</style>
