<template>
  <nav class="border-b border-gray-200" style="margin-bottom: 10px">
    <div class="mx-auto px-4 sm:px-6 lg:px-8">
      <div class="flex h-16 items-center justify-between">
        <div class="flex items-center">
          <h1 class="text-xl font-bold text-gray-900"><img src="../../assets/logo.png" style="max-width: 250px;"/></h1>
        </div>

        <div class="hidden md:flex md:items-center md:gap-6">
          <a
            v-for="item in menuItems"
            :key="item.name"
            :href="item.href"
            @click.prevent="handleNavigation(item)"
            :class="['text-sm font-medium text-gray-700 transition-colors',
             isActive(item.href)?'primary-color':'' ]"
          >
            {{ item.name }}
          </a>
          
        </div>

        <div class="flex items-center gap-4">
          <div v-if="user" class="hidden md:flex items-center gap-2">
            <div class="h-8 w-8 rounded-full bg-blue-600 flex items-center justify-center">
              <span class="text-sm font-medium text-white">
                {{ getUserInitials() }}
              </span>
            </div>
            <span class="text-sm font-medium text-gray-700">{{ user.full_name }}</span>
          </div>

          <button
            @click="toggleMenu"
            class="md:hidden inline-flex items-center justify-center p-2 rounded-md text-gray-700 hover:bg-gray-100 focus:outline-none"
            aria-label="Menu"
          >
            <svg
              class="h-6 w-6"
              fill="none"
              viewBox="0 0 24 24"
              stroke="white"
            >
              <path
                stroke-linecap="round"
                stroke-linejoin="round"
                stroke-width="2"
                d="M4 6h16M4 12h16M4 18h16"
              />
            </svg>
          </button>
        </div>
      </div>
    </div>
 
    <Transition name="overlay">
      <div
        v-if="isMenuOpen"
        class="fixed inset-0 bg-black/50 bg-opacity-50 z-40 md:hidden"
        @click="closeMenu"
      />
    </Transition>

    <Transition name="slide">
      <div
        v-if="isMenuOpen"
        class="fixed top-0 left-0 bottom-0 w-64 bg-white shadow-xl z-50 md:hidden"
      >
        <div class="flex flex-col h-full">
          <div class="flex items-center justify-between p-4 border-b border-gray-200">
            <h2 class="text-lg font-bold text-gray-900">Menu</h2>
            <button
              @click="closeMenu"
              class="p-2 rounded-md text-gray-700 hover:bg-gray-100"
              aria-label="Fermer"
            >
              <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="white">
                <path
                  stroke-linecap="round"
                  stroke-linejoin="round"
                  stroke-width="2"
                  d="M6 18L18 6M6 6l12 12"
                />
              </svg>
            </button>
          </div>

          <div v-if="user" class="p-4 border-b border-gray-200">
            <div class="flex items-center gap-3">
              <div class="h-10 w-10 rounded-full bg-blue-600 flex items-center justify-center">
                <span class="text-base font-medium text-white">
                  {{ getUserInitials() }}
                </span>
              </div>
              <div>
                <p class="text-sm font-medium text-gray-900">{{ user.full_name }}</p>
                <p class="text-xs text-gray-500">{{ user.email }}</p>
              </div>
            </div>
          </div>

          <nav class="flex-1 overflow-y-auto p-4">
            <a
              v-for="item in menuItems"
              :key="item.name"
              :href="item.href"
              @click.prevent="handleNavigation(item)"
              class="flex items-center gap-3 px-4 py-3 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-100 transition-colors mb-1"
              :class="{ 'bg-blue-50 text-blue-600': isActive(item.href) }"
            >
              <component :is="item.icon" class="h-5 w-5" />
              <span>{{ item.name }}</span>
            </a>
          </nav>
        </div>
      </div>
    </Transition>
  </nav>
</template>

<script setup>
import { ref, onMounted, h } from 'vue'
import { ElMessageBox, ElMessage } from 'element-plus'

// Menu state
const isMenuOpen = ref(false)
const user = ref(null)
const currentPath = ref('/')

// Menu items with icons
const menuItems = [
  {
    name: 'Dashboard',
    href: '/boutique-admin/dashboard',
    icon: () => h('svg', { class: 'h-5 w-5', fill: 'none', viewBox: '0 0 24 24', stroke: 'currentColor' }, [
      h('path', { 'stroke-linecap': 'round', 'stroke-linejoin': 'round', 'stroke-width': '2', d: 'M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6' })
    ])
  },
  {
    name: 'My Sells',
    href: '/dashboard-admin/commandes',
    icon: () => h('svg', { class: 'h-5 w-5', fill: 'none', viewBox: '0 0 24 24', stroke: 'currentColor' }, [
      h('path', { 'stroke-linecap': 'round', 'stroke-linejoin': 'round', 'stroke-width': '2', d: 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2' })
    ])
  },
//  {
  //  name: 'Fournisseurs',
    //href: '/dashboard-admin/boutiques',
    //icon: () => h('svg', { class: 'h-5 w-5', fill: 'none', viewBox: '0 0 24 24', stroke: 'currentColor' }, [
      //h('path', { 'stroke-linecap': 'round', 'stroke-linejoin': 'round', 'stroke-width': '2', d: 'M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z' })
    //])
//  },
  //  {
  //  name: 'Produits',
  //  href: '/dashboard-admin/produits',
  //  icon: () => h('svg', { class: 'h-5 w-5', fill: 'none', viewBox: '0 0 24 24', stroke: 'currentColor' }, [
  //    h('path', { 'stroke-linecap': 'round', 'stroke-linejoin': 'round', 'stroke-width': '2', d: 'M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4' })
  //  ])
  //},
  //{
  //  name: 'Catégories',
   // href: '/dashboard-admin/categories',
   // icon: () => h('svg', { class: 'h-5 w-5', fill: 'none', viewBox: '0 0 24 24', stroke: 'currentColor' }, [
   //   h('path', { 'stroke-linecap': 'round', 'stroke-linejoin': 'round', 'stroke-width': '2', d: 'M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7' })
   // ])
//  },
  {
    name: 'My Messages',
    href: '/dashboard-admin/messages',
    icon: () => h('svg', { class: 'h-5 w-5', fill: 'none', viewBox: '0 0 24 24', stroke: 'currentColor' }, [
      h('path', { 'stroke-linecap': 'round', 'stroke-linejoin': 'round', 'stroke-width': '2', d: 'M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7' })
    ])
  },
  {
    name: 'My Performances',
    href: '/visites',
    icon: () => h('svg', { class: 'h-5 w-5', fill: 'none', viewBox: '0 0 24 24', stroke: 'currentColor' }, [
      h('path', { 'stroke-linecap': 'round', 'stroke-linejoin': 'round', 'stroke-width': '2', d: 'M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7' })
    ])
  },
  {
    name: 'My parameters',
    href: '/dashboard-admin/parametres',
    icon: () => h('svg', { class: 'h-5 w-5', fill: 'none', viewBox: '0 0 24 24', stroke: 'currentColor' }, [
      h('path', { 'stroke-linecap': 'round', 'stroke-linejoin': 'round', 'stroke-width': '2', d: 'M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z' }),
      h('path', { 'stroke-linecap': 'round', 'stroke-linejoin': 'round', 'stroke-width': '2', d: 'M15 12a3 3 0 11-6 0 3 3 0 016 0z' })
    ])
  },
  {
    name: 'Logout',
    href: '/logout',
    icon: () => h('svg', { class: 'h-5 w-5', fill: 'none', viewBox: '0 0 24 24', stroke: 'currentColor' }, [
      h('path', { 'stroke-linecap': 'round', 'stroke-linejoin': 'round', 'stroke-width': '2', d: 'M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1' })
    ])
  }
]


const handleLogout = async () => {
  try {
    await ElMessageBox.confirm(
      'Are you sure you logout ?',
      'Confirmation',
      {
        confirmButtonText: 'Yes',
        cancelButtonText: 'Cancel',
        type: 'warning',
        confirmButtonColor: "#fe9700",

      }
    )

    localStorage.removeItem('authToken')
    sessionStorage.removeItem('authToken')
    localStorage.removeItem('user')
    sessionStorage.removeItem('user')
    localStorage.removeItem('rememberMe')

    ElMessage({
      type: 'success',
      message: 'Logout success'
    })

    window.location.href = '/boutique-admin/login'
  } catch (error) {
    // Annulé
  }
}

// Initialize user data from storage (similar to the provided file)
const initializeUserData = () => {
  try {
    const authToken = localStorage.getItem('authToken') || sessionStorage.getItem('authToken')
    const storedUser = localStorage.getItem('user') || sessionStorage.getItem('user')

    if (!authToken || !storedUser) {
      return null
    }

    const parsedUser = JSON.parse(storedUser)
    user.value = parsedUser
    
    // Validate user object
    if (!parsedUser.id || !parsedUser.full_name) {
      return null
    }

    return parsedUser
  } catch (error) {
    console.error('[v0] Error initializing user data:', error)
    return null
  }
}

// Toggle menu
const toggleMenu = () => {
  isMenuOpen.value = !isMenuOpen.value
}

// Close menu
const closeMenu = () => {
  isMenuOpen.value = false
}

// Handle navigation
const handleNavigation = async(item) => {
  
  if (item.href === '/logout') {
    await handleLogout()
  } else {
    currentPath.value = item.href
    // Navigate to the page (you can use Vue Router here)
    window.location.href = item.href
  }
  closeMenu()
}

// Check if route is active
const isActive = (href) => {
  return currentPath.value === href
}

// Get user initials
const getUserInitials = () => {
  if (!user.value || !user.value.full_name) return '?'
  
  const names = user.value.full_name.split(' ')
  if (names.length >= 2) {
    return (names[0][0] + names[1][0]).toUpperCase()
  }
  return names[0][0].toUpperCase()
}

// Initialize on mount
onMounted(() => {
  user.value = initializeUserData()
  currentPath.value = window.location.pathname
})
</script>

<style scoped>
/* Overlay transition */
.overlay-enter-active,
.overlay-leave-active {
  transition: opacity 0.3s ease;
}

.overlay-enter-from,
.overlay-leave-to {
  opacity: 0;
}

/* Slide transition (left to right) */
.slide-enter-active,
.slide-leave-active {
  transition: transform 0.3s ease-out;
}

.slide-enter-from {
  transform: translateX(-100%);
}

.slide-leave-to {
  transform: translateX(-100%);
}
</style>