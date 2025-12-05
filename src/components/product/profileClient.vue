<template>
  <div class="profile-page">
    <!-- Top Navigation Bar -->
    <div class="top-navbar" style="margin-top: 40px">
      <div class="navbar-container">
        <!-- Logo/Title Section -->
        <div class="navbar-brand">
          <button class="back-btn" @click="goBack">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <polyline points="15,18 9,12 15,6"></polyline>
            </svg>
          </button>
          <h1 class="brand-title">My Profile</h1>
        </div>

        <!-- Navigation Menu -->
        <nav class="nav-menu">
          <a
            href="#"
            class="nav-item"
            :class="{ active: activeTab === 'profile' }"
            @click.prevent="activeTab = 'profile'"
          >
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
              <circle cx="12" cy="7" r="4"></circle>
            </svg>
            <span>Profile</span>
          </a>
          <a
            href="#"
            class="nav-item"
            :class="{ active: activeTab === 'cart' }"
            @click.prevent="activeTab = 'cart'"
          >
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <circle cx="9" cy="21" r="1"></circle>
              <circle cx="20" cy="21" r="1"></circle>
              <path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"></path>
            </svg>
            <span>Cart</span>
            <span v-if="cartItems.length > 0" class="badge">{{ cartItems.length }}</span>
          </a>
          <a
            href="#"
            class="nav-item"
            :class="{ active: activeTab === 'favorites' }"
            @click.prevent="activeTab = 'favorites'"
          >
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"></path>
            </svg>
            <span>Favorites</span>
            <span v-if="userFavorites.length > 0" class="badge">{{ userFavorites.length }}</span>
          </a>
          <a
            href="#"
            class="nav-item"
            :class="{ active: activeTab === 'orders' }"
            @click.prevent="activeTab = 'orders'"
          >
            <ListOrderedIcon class="w-5 h-5" />
            <span>Orders</span>
            <span v-if="orders.length > 0" class="badge">{{ orders.length }}</span>
          </a>
        </nav>

        <!-- User Profile & Actions -->
        <div class="navbar-actions">
          <button
            @click="loadAllData"
            class="refresh-btn"
            title="Refresh data"
          >
            <RefreshIcon class="w-5 h-5" />
          </button>
          <div class="user-profile-mini">
            <div class="user-avatar-mini">
              <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                <circle cx="12" cy="7" r="4"></circle>
              </svg>
            </div>
            <div class="user-info-mini">
              <p class="user-name-mini">{{ profile.full_name }}</p>
              <p class="user-email-mini">{{ profile.email }}</p>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Main Content -->
    <div class="content-container">
      <!-- Loading State -->
      <div v-if="dataLoading" class="loading-state">
        <div class="spinner"></div>
        <p>Loading...</p>
      </div>

      <!-- Content Area -->
      <div class="content-area" v-if="!dataLoading">
        <!-- Profile Tab -->
        <div v-if="activeTab === 'profile'" class="tab-content">
          <!-- Stats Cards -->
          <div class="stats-grid">
            <a href="#" @click.prevent="activeTab = 'cart'">
              <div class="stat-card">
                <div class="stat-icon cart-icon">
                  <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <circle cx="9" cy="21" r="1"></circle>
                    <circle cx="20" cy="21" r="1"></circle>
                    <path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"></path>
                  </svg>
                </div>
                <div class="stat-info">
                  <p class="stat-label">My Cart</p>
                  <p class="stat-value">{{ cartItems.length }}</p>
                </div>
              </div>
            </a>
            <a href="#" @click.prevent="activeTab = 'favorites'">
              <div class="stat-card">
                <div class="stat-icon favorites-icon">
                  <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"></path>
                  </svg>
                </div>
                <div class="stat-info">
                  <p class="stat-label">My Favorites</p>
                  <p class="stat-value">{{ userFavorites.length }}</p>
                </div>
              </div>
            </a>
            <a href="#" @click.prevent="activeTab = 'orders'">
              <div class="stat-card">
                <div class="stat-icon orders-icon">
                  <ListOrderedIcon class="w-7 h-7" />
                </div>
                <div class="stat-info">
                  <p class="stat-label">My Orders</p>
                  <p class="stat-value">{{ orders.length }}</p>
                </div>
              </div>
            </a>
          </div>

          <!-- User Info Card -->
          <div class="section-card">
            <div class="section-header">
              <h2 class="section-title">Personal Information</h2>
              <button
                v-if="!isEditing"
                class="btn-degrade-orange"
                @click="isEditing = true"
              >
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                  <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
                  <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path>
                </svg>
                Edit
              </button>
            </div>

            <form @submit.prevent="saveProfile" class="form-grid">
              <div class="form-group">
                <label class="form-label">Full Name</label>
                <input
                  type="text"
                  class="input-style"
                  v-model="profile.full_name"
                  :readonly="!isEditing"
                >
              </div>

              <div class="form-group">
                <label class="form-label">Email</label>
                <input
                  type="email"
                  class="input-style"
                  v-model="profile.email"
                  :readonly="!isEditing"
                >
              </div>

              <div class="form-group">
                <label class="form-label">Country</label>
                <input
                  type="text"
                  class="input-style"
                  v-model="profile.pays"
                  :readonly="!isEditing"
                  placeholder="Enter your Country"
                >
              </div>

              <div class="form-group">
                <label class="form-label">Contact</label>
                <input
                  type="text"
                  class="input-style"
                  v-model="profile.contact"
                  :readonly="!isEditing"
                  placeholder="Enter your contact"
                >
              </div>

              <div class="form-group full-width">
                <label class="form-label">Address</label>
                <textarea
                  class="input-style"
                  v-model="profile.adresse"
                  :readonly="!isEditing"
                  placeholder="Enter your full address"
                  rows="3"
                ></textarea>
              </div>

              <div v-if="isEditing" class="form-actions full-width">
                <button type="button" class="btn-gray" @click="cancelEdit">
                  Cancel
                </button>
                <button type="submit" class="btn-degrade-orange">
                  <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"></path>
                    <polyline points="17 21 17 13 7 13 7 21"></polyline>
                    <polyline points="7 3 7 8 15 8"></polyline>
                  </svg>
                  Save Changes
                </button>
              </div>
            </form>
          </div>
        </div>

        <!-- Cart Tab -->
        <div v-if="activeTab === 'cart'" class="tab-content">
          <div class="section-card">
            <div class="flex justify-between items-center mb-5">
              <h2 class="section-title">My Cart</h2>
              <button
                @click="cart.clear"
                class="btn-deconnexion h-10"
              >
                <Trash2Icon class="w-4 h-4" />
                Clear
              </button>
            </div>

            <div v-if="cartItems.length === 0" class="empty-state flex flex-col">
              <svg width="64" height="64" viewBox="0 0 24 24" fill="none" stroke="#ccc" stroke-width="2" class="opacity-50">
                <circle cx="9" cy="21" r="1"></circle>
                <circle cx="20" cy="21" r="1"></circle>
                <path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"></path>
              </svg>
              <p>Your cart is currently empty.</p>
            </div>

            <div v-else class="space-y-6">
              <div v-for="group in groupedCart" :key="group.boutique_id" class="section-card">
                <div class="flex items-center justify-between mb-3">
                  <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-lg overflow-hidden border border-gray-200 flex-shrink-0 cursor-pointer">
                      <img :src="group.boutique_logo" :alt="group.boutique_logo" class="w-full h-full object-contain">
                    </div>
                    <div class="rounded-lg bg-gray-100 text-black py-1 px-2 flex items-center justify-center text-base font-semibold">
                      <span v-if="group.boutique_name">{{ group.boutique_name }}</span>
                      <span v-else>Boutique {{ group.boutique_id }}</span>
                    </div>
                    <div class="text-xs text-gray-500">Items: {{ group.items.length }}</div>
                  </div>
                  <div class="text-sm text-gray-600 font-bold">Subtotal: {{ formatPrice(group.subtotal) }}</div>
                </div>

                <div class="space-y-3">
                  <div v-for="item in group.items" :key="item.item_id" class="cart-item flex items-center justify-between">
                    <div class="flex items-center gap-4">
                      <div class="w-20 h-20 rounded-lg overflow-hidden border border-gray-200 flex-shrink-0 cursor-pointer" @click="goToProduct(item.product_slug)">
                        <img :src="item.primary_image" :alt="item.name" class="w-full h-full object-contain">
                      </div>
                      <div class="flex flex-col">
                        <h3 class="favorite-name">{{ item.name }}</h3>
                        <div class="flex gap-2 text-xs text-gray-600">
                          <span class="bg-orange-200 px-2 py-1 rounded-lg font-bold">VIN: {{ item.vin_number || "N/A" }}</span>
                          <span class="bg-blue-200 px-2 py-1 rounded-lg font-bold">Trim: {{ item.trim_number || "N/A" }}</span>
                          <span class="bg-green-200 px-2 py-1 rounded-lg font-bold">Stock number: {{ item.stock_number || "N/A" }}</span>
                        </div>
                        <div class="mt-2 flex items-center gap-4">
                          <div class="text-sm text-gray-600">Unit Price:</div>
                          <div class="favorite-price">{{ formatPrice(item.unit_price) }}</div>
                        </div>
                      </div>
                    </div>

                    <div class="flex items-center gap-2">
                      <div class="product-quantity">
                        <div class="quantity-controls">
                          <button @click="decreaseQty(item)" :disabled="item.quantity <= 1">-</button>
                          <input type="number" v-model.number="item.quantity" min="1" @input="validateQuantity" class="quantity-input focus:border-ring-2 focus:ring-0">
                          <button @click="increaseQty(item)">+</button>
                        </div>
                      </div>
                      <button class="remove-btn ml-3" @click="removeCartItem(item)" title="Remove item from cart">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                          <line x1="18" y1="6" x2="6" y2="18"></line>
                          <line x1="6" y1="6" x2="18" y2="18"></line>
                        </svg>
                      </button>
                    </div>
                  </div>
                </div>

                <div class="flex justify-end gap-3 mt-3">
                  <button class="btn-gray" @click="continueShopping">Continue Shopping</button>
                  <button class="btn-degrade-orange text-white" @click="checkoutBoutique(group)">Order from this Shop</button>
                </div>
              </div>

              <!-- Total Global -->
              <div class="section-card flex items-center text-xl justify-between">
                <div class="text-gray-600 font-bold">Total ({{ cartItems.length }} Items):</div>
                <div class="font-bold primary-color">{{ formatPrice(grandTotal) }}</div>
              </div>

              <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <button class="btn-gray w-full" @click="continueShopping">Continue Shopping</button>
                <button class="btn-degrade-orange w-full text-white" @click="proceedToCheckout">Order Now (All)</button>
              </div>
            </div>
          </div>
        </div>

        <!-- Favorites Tab -->
        <div v-if="activeTab === 'favorites'" class="tab-content">
          <div class="section-card">
            <h2 class="section-title">My Favorites</h2>
            <div v-if="userFavorites.length === 0" class="empty-state flex flex-col">
              <svg width="64" height="64" viewBox="0 0 24 24" fill="none" stroke="#ccc" stroke-width="2" class="opacity-50">
                <path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"></path>
              </svg>
              <p>You don't have any items in your favorites yet.</p>
            </div>
            <div v-else class="favorites-list">
              <div v-for="item in userFavorites" :key="item.favorite_id" class="favorite-item">
                <div @click="goToProduct(item.slug)" style="display: flex; align-items: center; flex: 1; cursor: pointer;">
                  <img :src="item.primary_image" :alt="item.name" class="favorite-image">
                  <div class="favorite-info">
                    <h3 class="favorite-name">{{ item.name }}</h3>
                    <p class="favorite-price">{{ formatPrice(item.unit_price, {showFOB:true}) }}</p>
                  </div>
                </div>
                <button class="remove-btn" @click="removeFavorite(item.product_id)">
                  <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <line x1="18" y1="6" x2="6" y2="18"></line>
                    <line x1="6" y1="6" x2="18" y2="18"></line>
                  </svg>
                </button>
              </div>
            </div>
          </div>
        </div>

        <!-- Orders Tab -->
        <div v-if="activeTab === 'orders'" class="tab-content">
          <Commandes/>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, computed, watch } from 'vue'
import Commandes from '../product/MesCommandes.vue'
import { productsApi, ordersApi } from '../../services/api'
import { useRouter, useRoute } from 'vue-router'
import { ElMessageBox, ElMessage } from 'element-plus'
import { ListOrderedIcon, RefreshCcw as RefreshIcon, Trash2Icon } from 'lucide-vue-next'
import { formatPrice } from '../../services/formatPrice'
import { useCartStore } from '../../stores/cart'
import { useOrdersStore } from '../../stores/orders.js'

const cart = useCartStore()
const ordersStore = useOrdersStore()
const router = useRouter()
const route = useRoute()

const activeTab = ref('profile')
const isFavorite = ref(true)
const isEditing = ref(false)
const dataLoading = ref(false)
const currentUser = ref(null)
const orders = ref([])

const userFavorites = ref([])

const profile = ref({
  id: 1,
  full_name: 'Doe',
  email: 'john.doe@example.com',
  pays: 'Côte d\'Ivoire',
  adresse: '123 Rue de la Paix, Abidjan',
  boutique: [],
  contact: '+225 0153676062',
  picture: '',
  phone: ''
})

const showNotification = ref(false)
const notificationMessage = ref('')
const notificationTitle = ref('')
const notificationType = ref('success')

const saveProfile = () => {
  console.log('Profile saved:', profile.value)
  isEditing.value = false
}

const cancelEdit = () => {
  isEditing.value = false
}

const cartItems = computed(() => {
  return cart.items
})

const groupedCart = computed(() => {
  return cart.grouped
})

const grandTotal = computed(() => {
  return cart.grandTotal
})

const checkoutBoutique = (group) => {
  sessionStorage.setItem('checkoutItems', JSON.stringify(group.items))
  ordersStore.addOrder(group.items)
  router.push({
    path: '/order-validation',
  })
}

const increaseQty = (item) => {
  cart.increaseQty(item.item_id, Number(item.quantity) + 1)
}

const decreaseQty = (item) => {
  if ((Number(item.quantity) || 0) > 1) {
    cart.decreaseQty(item.item_id, Number(item.quantity) - 1)
  }
}

const removeCartItem = (item) => {
  cart.removeItem(item.item_id)
}

const continueShopping = () => {
  router.push('/produits').catch(()=>{})
}

const proceedToCheckout = () => {
  router.push({ path: '/checkout' }).catch(()=>{})
}

const fetchCartItems = async () => {
  cartItems.value = cart.items
  groupedCart.value = cart.grouped
  grandTotal.value = cart.grandTotal
}

const fetchFavorites = async () => {
  try {
    const userId = profile.value.id
    const result = await productsApi.getFavorites(userId)

    if (result.success) {
      userFavorites.value = result.data
    } else {
      console.error('Erreur backend:', result.error)
    }
  } catch (error) {
    console.error('Erreur fetchFavorites:', error)
  }
}

const fetchOrders = async () => {
  try {
    const userData = localStorage.getItem('user') || sessionStorage.getItem('user')
    if (!userData) {
      orders.value = []
      return
    }

    const parsedUser = JSON.parse(userData)
    const userId = parsedUser.id

    const response = await ordersApi.getMyOrders(userId)

    if (response.success) {
      orders.value = response.data || []
    } else {
      orders.value = []
    }
  } catch (error) {
    console.error('Error fetching orders:', error)
    orders.value = []
  }
}

const goToProduct = (product) => {
  const slug = product
  router.push(`/detail_resultat_produit/${slug}`)
}

const removeFavorite = async (idProduit) => {
  try {
    if (isFavorite.value) {
      await ElMessageBox.confirm(
        'Are you sure you want to remove this product from your favorites?',
        'Confirmation',
        {
          confirmButtonText: 'Confirm',
          cancelButtonText: 'Cancel',
          type: 'warning',
        }
      )
    }

    isFavorite.value = !isFavorite.value

    const userData = localStorage.getItem('user') || sessionStorage.getItem('user')
    const user = JSON.parse(userData)

    const likeData = { id_produit: idProduit, user_id: user.id }
    const result = await productsApi.addLike(likeData)

    if (result.success) {
      ElMessage({
        type: isFavorite.value ? 'success' : 'info',
        message: isFavorite.value ? 'Product added to favorites.' : 'Product removed from favorites.',
      })

      fetchFavorites()
    } else {
      isFavorite.value = !isFavorite.value
      ElMessage({ type: 'error', message: result.error || 'Unable to update favorites.' })
    }
  } catch (error) {
    if (error !== 'cancel') {
      console.error('Erreur toggleFavorite:', error)
      isFavorite.value = !isFavorite.value
      ElMessage({ type: 'error', message: 'An error occurred during the update.' })
    }
  }
}

const goBack = () => {
  window.history.back()
}

const loadAllData = async () => {
  dataLoading.value = true
  try {
    await fetchCartItems()
    await fetchFavorites()
    await fetchOrders()
  } finally {
    dataLoading.value = false
  }
}

watch(
  () => [route.query.tab, route.hash],
  ([newTab, newHash]) => {
    if (newTab) activeTab.value = String(newTab)
    else if (newHash) activeTab.value = String(newHash).replace('#', '')
  }
)

onMounted(async () => {
  const qTab = route.query.tab
  const h = route.hash ? route.hash.replace('#', '') : null

  if (qTab) {
    activeTab.value = String(qTab)
  } else if (h) {
    activeTab.value = String(h)
  }

  const userData = localStorage.getItem('user') || sessionStorage.getItem('user')
  const user = JSON.parse(userData)
  profile.value = {
    id: user.id,
    full_name: user.full_name,
    email: user.email,
    picture: user.picture || '',
    phone: user.phone || '',
    boutiques: user.boutiques || []
  }

  loadAllData()
})
</script>

<style scoped>
.profile-page {
  min-height: 100vh;
  background: linear-gradient(135deg, #f5f7fa 0%, #f0f2f5 100%);
  padding-bottom: 40px;
}

/* Top Navigation Bar */
.top-navbar {
  background: linear-gradient(135deg, #ffffff 0%, #fafbfc 100%);
  border-bottom: 2px solid #e8ecf1;
  box-shadow: 0 4px 20px rgba(0, 0, 0, 0.06);
  position: sticky;
  top: 0;
  z-index: 100;
  backdrop-filter: blur(10px);
}

.navbar-container {
  max-width: 1400px;
  margin: 0 auto;
  padding: 0 24px;
  display: flex;
  align-items: center;
  justify-content: space-between;
  height: 80px;
  gap: 32px;
}

.navbar-brand {
  display: flex;
  align-items: center;
  gap: 16px;
  min-width: 250px;
}

.back-btn {
  display: inline-flex;
  align-items: center;
  gap: 8px;
  padding: 10px 14px;
  background: #fff;
  border: 2px solid #e8ecf1;
  border-radius: 10px;
  color: #666;
  font-size: 14px;
  font-weight: 500;
  cursor: pointer;
  transition: all 0.3s ease;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.04);
}

.back-btn:hover {
  border-color: #fe9700;
  color: #fe9700;
  transform: translateX(-2px);
  box-shadow: 0 4px 12px rgba(254, 151, 0, 0.15);
}

.brand-title {
  font-size: 24px;
  font-weight: 700;
  color: #1a1a1a;
  margin: 0;
  background: linear-gradient(135deg, #fe9700 0%, #ff6b00 100%);
  -webkit-background-clip: text;
  -webkit-text-fill-color: transparent;
  background-clip: text;
}

/* Navigation Menu */
.nav-menu {
  display: flex;
  align-items: center;
  gap: 8px;
  flex: 1;
  justify-content: center;
}

.nav-item {
  display: flex;
  align-items: center;
  gap: 8px;
  padding: 12px 20px;
  color: #666;
  text-decoration: none;
  border-radius: 12px;
  transition: all 0.3s ease;
  cursor: pointer;
  font-size: 15px;
  font-weight: 500;
  position: relative;
  background: transparent;
}

.nav-item:hover {
  background: #fff7e6;
  color: #fe9700;
  transform: translateY(-2px);
}

.nav-item.active {
  background: linear-gradient(135deg, #fe9700 0%, #ff8c00 100%);
  color: #fff;
  box-shadow: 0 4px 16px rgba(254, 151, 0, 0.3);
}

.nav-item.active svg {
  stroke: #fff;
}

.badge {
  background: #ff4d4f;
  color: #fff;
  font-size: 11px;
  font-weight: 700;
  padding: 2px 7px;
  border-radius: 12px;
  min-width: 20px;
  text-align: center;
}

.nav-item.active .badge {
  background: #fff;
  color: #fe9700;
}

/* Navbar Actions */
.navbar-actions {
  display: flex;
  align-items: center;
  gap: 16px;
  min-width: 250px;
  justify-content: flex-end;
}

.refresh-btn {
  display: flex;
  align-items: center;
  justify-content: center;
  width: 44px;
  height: 44px;
  background: #fff;
  border: 2px solid #e8ecf1;
  border-radius: 12px;
  color: #666;
  cursor: pointer;
  transition: all 0.3s ease;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.04);
}

.refresh-btn:hover {
  background: #fe9700;
  border-color: #fe9700;
  color: #fff;
  transform: rotate(180deg);
  box-shadow: 0 4px 12px rgba(254, 151, 0, 0.2);
}

.user-profile-mini {
  display: flex;
  align-items: center;
  gap: 12px;
  padding: 8px 16px 8px 8px;
  background: #fff;
  border: 2px solid #e8ecf1;
  border-radius: 50px;
  cursor: pointer;
  transition: all 0.3s ease;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.04);
}

.user-profile-mini:hover {
  border-color: #fe9700;
  box-shadow: 0 4px 12px rgba(254, 151, 0, 0.15);
}

.user-avatar-mini {
  width: 40px;
  height: 40px;
  background: linear-gradient(135deg, #fe9700 0%, #ff8c00 100%);
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  color: #fff;
  flex-shrink: 0;
}

.user-info-mini {
  display: flex;
  flex-direction: column;
  gap: 2px;
}

.user-name-mini {
  font-size: 14px;
  font-weight: 600;
  color: #1a1a1a;
  margin: 0;
  line-height: 1.2;
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
  max-width: 150px;
}

.user-email-mini {
  font-size: 12px;
  color: #999;
  margin: 0;
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
  max-width: 150px;
}

/* Content Container */
.content-container {
  max-width: 1400px;
  margin: 32px auto;
  padding: 0 24px;
}

.content-area {
  display: flex;
  flex-direction: column;
}

.tab-content {
  animation: fadeIn 0.4s ease;
}

@keyframes fadeIn {
  from {
    opacity: 0;
    transform: translateY(20px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

/* Loading State */
.loading-state {
  text-align: center;
  padding: 80px 20px;
  background: #fff;
  border-radius: 16px;
  color: #fe9700;
  box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
}

.loading-state .spinner {
  border: 4px solid #f3f3f3;
  border-top: 4px solid #fe9700;
  border-radius: 50%;
  width: 50px;
  height: 50px;
  animation: spin 1s linear infinite;
  margin: 0 auto 20px auto;
}

@keyframes spin {
  0% { transform: rotate(0deg); }
  100% { transform: rotate(360deg); }
}

/* Stats Grid */
.stats-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
  gap: 24px;
  margin-bottom: 32px;
}

.stat-card {
  background: #fff;
  border-radius: 16px;
  padding: 28px;
  box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
  display: flex;
  align-items: center;
  gap: 20px;
  transition: all 0.3s ease;
  border: 2px solid transparent;
}

.stat-card:hover {
  transform: translateY(-4px);
  box-shadow: 0 8px 30px rgba(0, 0, 0, 0.12);
  border-color: #fe9700;
}

.stat-icon {
  width: 64px;
  height: 64px;
  border-radius: 16px;
  display: flex;
  align-items: center;
  justify-content: center;
  flex-shrink: 0;
}

.cart-icon {
  background: linear-gradient(135deg, #fff7e6 0%, #ffe8c2 100%);
  color: #fe9700;
}

.favorites-icon {
  background: linear-gradient(135deg, #ffe5e7 0%, #ffd1d6 100%);
  color: #ff4d4f;
}

.orders-icon {
  background: linear-gradient(135deg, #e6f7ff 0%, #bae7ff 100%);
  color: #1890ff;
}

.stat-info {
  flex: 1;
}

.stat-label {
  font-size: 14px;
  color: #999;
  margin: 0 0 6px 0;
  font-weight: 500;
}

.stat-value {
  font-size: 32px;
  font-weight: 700;
  color: #1a1a1a;
  margin: 0;
}

/* Section Card */
.section-card {
  background: #fff;
  border-radius: 16px;
  padding: 32px;
  box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
  margin-bottom: 24px;
  border: 2px solid #f5f5f5;
  transition: all 0.3s ease;
}

.section-card:hover {
  border-color: #fe9700;
}

.section-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 24px;
  padding-bottom: 20px;
  border-bottom: 2px solid #f5f5f5;
}

.section-title {
  font-size: 20px;
  font-weight: 700;
  color: #1a1a1a;
  margin: 0;
}

/* Form Styles */
.form-grid {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 20px;
}

.form-group {
  display: flex;
  flex-direction: column;
}

.form-group.full-width {
  grid-column: 1 / -1;
}

.form-label {
  font-size: 14px;
  font-weight: 600;
  color: #333;
  margin-bottom: 8px;
}

.input-style[readonly] {
  background: #f8f9fa;
  color: #666;
  cursor: not-allowed;
  border-color: #e8ecf1;
}

.form-actions {
  display: flex;
  gap: 16px;
  margin-top: 24px;
  padding-top: 24px;
  border-top: 2px solid #f5f5f5;
}

/* Empty State */
.empty-state {
  align-items: center;
  padding: 80px 20px;
  color: #999;
  text-align: center;
}

.empty-state p {
  font-size: 16px;
  margin: 16px 0 0 0;
}

/* Cart & Favorites Items */
.cart-item {
  background: #f9fafb;
  padding: 16px;
  border-radius: 12px;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.04);
  align-items: center;
  transition: all 0.3s ease;
}

.cart-item:hover {
  box-shadow: 0 4px 16px rgba(0, 0, 0, 0.08);
  transform: translateY(-2px);
}

.favorites-list {
  display: flex;
  flex-direction: column;
  gap: 16px;
}

.favorite-item {
  display: flex;
  align-items: center;
  gap: 20px;
  padding: 20px;
  background: #f9fafb;
  border-radius: 12px;
  transition: all 0.3s ease;
  border: 2px solid transparent;
}

.favorite-item:hover {
  background: #fff;
  border-color: #fe9700;
  box-shadow: 0 4px 16px rgba(254, 151, 0, 0.1);
}

.favorite-image {
  width: 90px;
  height: 90px;
  object-fit: cover;
  border-radius: 12px;
  border: 2px solid #e8ecf1;
}

.favorite-info {
  flex: 1;
  padding-left: 20px;
}

.favorite-name {
  font-size: 16px;
  font-weight: 600;
  color: #1a1a1a;
  margin: 0 0 8px 0;
}

.favorite-price {
  font-size: 16px;
  color: #fe9700;
  font-weight: 700;
  margin: 0;
}

.remove-btn {
  width: 40px;
  height: 40px;
  border: 2px solid #e8ecf1;
  background: #fff;
  border-radius: 10px;
  color: #999;
  cursor: pointer;
  display: flex;
  align-items: center;
  justify-content: center;
  transition: all 0.3s ease;
}

.remove-btn:hover {
  border-color: #ff4d4f;
  color: #ff4d4f;
  background: #fff1f0;
  transform: scale(1.1);
}

/* Product Quantity */
.product-quantity {
  display: flex;
  align-items: center;
  gap: 12px;
}

.quantity-controls {
  display: flex;
  align-items: center;
  border: 2px solid #e8ecf1;
  border-radius: 10px;
  overflow: hidden;
  background: #fff;
}

.quantity-controls button {
  width: 36px;
  height: 36px;
  border: none;
  background: white;
  color: #333;
  font-size: 20px;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.3s ease;
}

.quantity-controls button:hover:not(:disabled) {
  background: #fe9700;
  color: #fff;
}

.quantity-controls button:disabled {
  opacity: 0.4;
  cursor: not-allowed;
}

.quantity-input {
  width: 60px;
  height: 36px;
  border: none;
  border-left: 2px solid #e8ecf1;
  border-right: 2px solid #e8ecf1;
  text-align: center;
  font-size: 16px;
  font-weight: 600;
  color: #1a1a1a;
}

.quantity-input::-webkit-outer-spin-button,
.quantity-input::-webkit-inner-spin-button {
  -webkit-appearance: none;
  margin: 0;
}

/* Responsive */
@media (max-width: 1024px) {
  .navbar-container {
    flex-wrap: wrap;
    height: auto;
    padding: 16px 20px;
  }

  .navbar-brand {
    order: 1;
    min-width: auto;
  }

  .navbar-actions {
    order: 2;
    min-width: auto;
  }

  .nav-menu {
    order: 3;
    width: 100%;
    margin-top: 16px;
    justify-content: flex-start;
    overflow-x: auto;
    padding-bottom: 8px;
  }

  .nav-item span {
    display: none;
  }

  .nav-item {
    padding: 12px;
    min-width: 44px;
    justify-content: center;
  }
}

@media (max-width: 768px) {
  .brand-title {
    font-size: 18px;
  }

  .user-info-mini {
    display: none;
  }

  .stats-grid {
    grid-template-columns: 1fr;
  }

  .form-grid {
    grid-template-columns: 1fr;
  }

  .content-container {
    padding: 0 16px;
  }
}
</style>
