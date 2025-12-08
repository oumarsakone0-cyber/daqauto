<template >
    <SearchPageMobile ref="searchMobileRef" />
    <nav class="mobile-bottom-nav ">
       <div
          v-for="(tab, index) in navTabs"
          :key="tab.label"
          :class="['nav-item cursor-pointer', { active: activeTab === index }]"
          @click="handleNavClick(index)"
        >
        <component :is="tab.icon" class="h-6 w-6 " />
            
            <!-- Place ici ton SVG selon tab.icon ou garde les SVG existants -->
            <span>{{ tab.label }}</span>
            <!-- Exemple pour le badge du panier -->
            <span v-if="tab.label === 'Panier'" class="cart-badge bg-orange">2</span>
        </div>
      
    </nav>
</template>
<script setup>
import { ref } from 'vue'
import { useRouter } from 'vue-router'
import { HomeIcon, MessageSquareText, SearchIcon, ShoppingCartIcon, User } from 'lucide-vue-next'
import { useChatStore } from '../../stores/chat'
import SearchPageMobile from './SearchPageMobile.vue'

const activeTab = ref(0)
const chat = useChatStore()
const router = useRouter()
const searchMobileRef = ref(null)


      // Liste des onglets de navigation
const navTabs = [
  { label: 'Home', route: '/', icon: HomeIcon },
  { label: 'Search', route: '/search', icon: SearchIcon },
  { label: 'Cart', route: '/profile_client', query: { tab: 'cart' }, icon: ShoppingCartIcon },
  { label: 'Messages', route: chat, icon: MessageSquareText },
  { label: 'Profile', route: '/profile_client', query: {tab: 'profile'}, icon: User }
]

// Fonction pour changer d’onglet et naviguer
const handleNavClick =(index) =>{
  activeTab.value = index
  const tab = navTabs[index]
  
  if (tab.label === 'Messages') {
    chat.openChat()
    return
  }
  if (tab.label === 'Search') {
    searchMobileRef.value.openMobileSearch()
    return
  }
  
  // Si la route a des query params, les inclure
  if (tab.query) {
    router.push({ path: tab.route, query: tab.query })
  } else if (typeof tab.route === 'string') {
    router.push(tab.route)
  } else {
    router.push(tab.route)
  }
}

</script>
<style scoped>


.mobile-bottom-nav {
  position: fixed;
  bottom: 0;
  left: 0;
  right: 0;
  background: white;
  border-top: 1px solid #e0e0e0;
  display: flex;
  z-index: 1000;
   padding: 12px 16px;
    gap: 8px;
    z-index: 100;
    box-shadow: 0 -2px 10px rgba(0, 0, 0, 0.1);
}
.nav-item {
  flex: 1;
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 4px;
  padding: 8px 4px;
  color: #999;
  font-size: 10px;
  font-weight: 500;
  position: relative;
  transition: color 0.2s;
}
.nav-item.active {
  color: #fe7900;
}

.nav-item svg {
  width: 24px;
  height: 24px;
}

.cart-badge {
  position: absolute;
  top: 4px;
  right: 20px;
  font-size: 10px;
  font-weight: bold;
  padding: 2px 6px;
  border-radius: 10px;
  min-width: 16px;
  text-align: center;
}


    
</style>