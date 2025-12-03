<template>
  <div class="profile-page">
    <div class="page-header">
      <div class="container" style="margin-top: 40px">
        <button class="back-btn" @click="goBack">
          <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <polyline points="15,18 9,12 15,6"></polyline>
          </svg>
          Back
        </button>
        <h1 class="page-title" style="margin-left: 15px; margin-top: 8px;">My Profile</h1>
      </div>
        <button 
              @click="loadAllData"
              class="submit-btn mt-8 sm:mr-21 mr-4 h-10"
            >
              <RefreshIcon class="w-4 h-4" />
              Refresh
        </button>
    </div>

    <div class="container main-content">
      <!-- Sidebar Menu -->
      <div class="sidebar-menu">
        <!-- Added user profile section at top of sidebar -->
        <div class="user-profile-header">
          <div class="user-avatar">
            <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
              <circle cx="12" cy="7" r="4"></circle>
            </svg>
          </div>
          <div class="user-info">
            <p class="user-name">{{ profile.prenom }} {{ profile.nom }}</p>
            <p class="user-email">{{ profile.email }}</p>
          </div>
        </div>

        <nav class="menu-list">
          <a 
            href="#" 
            class="menu-item" 
            :class="{ active: activeTab === 'profile' }"
            @click.prevent="activeTab = 'profile'"
          >
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
              <circle cx="12" cy="7" r="4"></circle>
            </svg>
            My Profile
          </a>
          <a 
            href="#" 
            class="menu-item" 
            :class="{ active: activeTab === 'cart' }"
            @click.prevent="activeTab = 'cart'"
          >
           <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <circle cx="9" cy="21" r="1"></circle>
              <circle cx="20" cy="21" r="1"></circle>
              <path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"></path>
            </svg>
            My Cart
          </a>
          <a 
            href="#" 
            class="menu-item" 
            :class="{ active: activeTab === 'favorites' }"
            @click.prevent="activeTab = 'favorites'"
          >
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"></path>
            </svg>
            My favorites
          </a>
          
          <a 
            href="#" 
            class="menu-item" 
            :class="{ active: activeTab === 'orders' }"
            @click.prevent="activeTab = 'orders'"
          >
             <ListOrderedIcon class="w-6 h-6" />
            My Orders
          </a>
        </nav>
      </div>


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
              <div class="stat-icon">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#fe9700" stroke-width="2">
                  <circle cx="9" cy="21" r="1"></circle>
                  <circle cx="20" cy="21" r="1"></circle>
                  <path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"></path>
                </svg>
              </div>
              <div class="stat-info">
                <p class="stat-label">Cart</p>
                <p class="stat-value">{{ cartItems.length }}</p>
              </div>
            </div>
            </a>
            <a href="#" @click.prevent="activeTab = 'favorites'">
              <div class="stat-card">
              <div class="stat-icon">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#fe9700" stroke-width="2">
                  <path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"></path>
                </svg>
              </div>
              <div class="stat-info">
                <p class="stat-label">Favorites</p>
                <p class="stat-value">{{ userFavorites.length }}</p>
              </div>
            </div>
            </a>
            <a href="#" @click.prevent="activeTab = 'orders'">
              <div class="stat-card">
              <div class="stat-icon">
                <ListOrderedIcon class="w-6 h-6 primary-color" />
              </div>
              <div class="stat-info">
                <p class="stat-label">Orders</p>
                <p class="stat-value">{{ orders.length }}</p>
              </div>
            </div>
            </a>
          </div>

          <!-- User Info Card -->
          <div class="section-card">
            <div class="section-header">
              <h2 class="section-title">Personal information</h2>
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
              {{currentUser}}
            </div>

            <form @submit.prevent="saveProfile" class="form-grid">
              <div class="form-group">
                <label class="form-label">Full name</label>
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
                  Save changes
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
                class="btn-deconnexion h-10 "
              >
                <Trash2Icon class="w-4 h-4" />
                clear
                </button>

            </div>

            <div v-if="cartItems.length === 0" class="empty-state flex flex-col">
              <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="#ccc" stroke-width="2" class="opacity-50">
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
                    <div class=" rounded-lg bg-gray-100 text-black py-1  px-2 flex items-center justify-center text-base font-semibold">
                     <span v-if="group.boutique_name">{{ group.boutique_name }}</span> <span v-else>Boutique {{ group.boutique_id  }}</span>
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
                        <div class="flex gap-2 text-xs text-gray-600" >
                          <span class="bg-orange-200 px-2 py-1 rounded-lg font-bold">VIN: {{ item.vin_number || "N/A"}}</span>
                          <span  class="bg-blue-200 px-2 py-1 rounded-lg font-bold">Trim: {{ item.trim_number || "N/A"}}</span>
                          <span class="bg-green-200 px-2 py-1 rounded-lg font-bold" >Stock number: {{ item.stock_number|| "N/A" }}</span>
                        </div>
                        <!-- <div class="flex gap-2 text-xs text-gray-600">
                          <span v-if="item.power" class="text-xs text-gray-500 mt-1">H Power : {{ item.power }}HP</span>
                          <span v-if="item.fuel_type" class="text-xs text-gray-500 mt-1 capitalize">Fuel Type : {{ item.fuel_type }}</span>

                        </div> -->
                        <div class="mt-2 flex items-center gap-4">
                          <div class="text-sm text-gray-600">Unit Price :</div>
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
                  <button class="btn-gray" @click="continueShopping">Continue shopping</button>
                  <button class="btn-degrade-orange text-white" @click="checkoutBoutique(group)">Order from this Shop</button>
                </div>
          </div>

          <!-- total global -->
          <div class="section-card flex items-center text-xl justify-between">
            <div class=" text-gray-600 font-bold">Total ({{ cartItems.length }} Items) :</div>
            <div class=" font-bold primary-color">{{ formatPrice(grandTotal) }}</div>
          </div>

          <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
            <button class="btn-gray w-full" @click="continueShopping">Continue shopping</button>
            <button class="btn-degrade-orange w-full text-white" @click="proceedToCheckout">Order now (all)</button>
          </div>
        </div>
        </div>
      </div>
        <!-- Favorites Tab -->
        <div v-if="activeTab === 'favorites'" class="tab-content">
          <div class="section-card">
            <h2 class="section-title">My favorites</h2>
            <div v-if="userFavorites.length === 0" class="empty-state flex flex-col">
              
                <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="#ccc" stroke-width="2" class="opacity-50">
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
import { ref, onMounted,computed,watch } from 'vue'
import Commandes from '../product/MesCommandes.vue'
import { productsApi,ordersApi } from '../../services/api'
import { useRouter,useRoute } from 'vue-router'
import { ElMessageBox, ElMessage } from 'element-plus';
import { ListOrderedIcon, RefreshCcw as RefreshIcon, Trash2Icon } from 'lucide-vue-next';
import { formatPrice } from '../../services/formatPrice';
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

const userFavorites = ref([]);

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
  // Ici ajouter l'appel API pour sauvegarder
}

const cancelEdit = () => {
  isEditing.value = false
}

const cartItems = computed(() => {
  return cart.items;
});

// Grouper les items du panier par boutique
const groupedCart = computed(() => {
  
  return cart.grouped;
})

const grandTotal = computed(() => {
  // return groupedCart.value.reduce((acc, g) => acc + (g.subtotal || 0), 0)

return cart.grandTotal;
})

// Lance checkout pour une boutique (stocke les items en session et navigue)
const checkoutBoutique = (group) => {

  sessionStorage.setItem('checkoutItems', JSON.stringify(group.items))
  ordersStore.addOrder(group.items)
  router.push({ 
    path: '/order-validation',
  })
  // router.push({ path: '/checkout', query: { boutiqueId: group.boutique_id } }).catch(()=>{})
}


const increaseQty = (item) => {
  cart.increaseQty(item.item_id, Number(item.quantity) + 1)
  // option: appeler API/store pour persister
}

const decreaseQty = (item) => {
  if ((Number(item.quantity) || 0) > 1) {
    cart.decreaseQty(item.item_id, Number(item.quantity) - 1)
    // option: appeler API/store pour persister
  }
}

const removeCartItem = (item) => {
  cart.removeItem(item.item_id)

  // option: appeler API pour supprimer côté serveur
}

const continueShopping = () => {
  // comportement: aller vers la page d'accueil / catalogue
  router.push('/produits').catch(()=>{})
}

const proceedToCheckout = () => {
  // comportement: ouvrir modal checkout ou router vers page de commande
  router.push({ path: '/checkout' }).catch(()=>{})
}


const fetchCartItems = async () => {

    cartItems.value =  cart.items;
    groupedCart.value = cart.grouped;
    grandTotal.value = cart.grandTotal;

    // try {
    // const userData = localStorage.getItem('user') || sessionStorage.getItem('user')
    // if (!userData) {
    //   cartItems.value = []
    //   return
    // }

    // const parsedUser = JSON.parse(userData)
    // const userId = parsedUser.id

    // const response = await ordersApi.getMyOrders(userId)

    // if (response.success) {
      // cartItems.value = response.data || []
    // } else {
    //   cartItems.value = []
    // }
  // } catch (error) {
  //   console.error('Error fetching cart items:', error)
  //   cartItems.value = []
  // }
};
const fetchFavorites = async () => {

  try {
    const userId = profile.value.id; // récupéré depuis le localStorage ou store
    const result = await productsApi.getFavorites(userId);

    if (result.success) {
      userFavorites.value = result.data;
    } else {
      console.error('Erreur backend:', result.error);
    }
  } catch (error) {
    console.error('Erreur fetchFavorites:', error);
  }
};

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
    // Si le produit est déjà un favori, demander confirmation
    if (isFavorite.value) {
      await ElMessageBox.confirm(
        'Are you sure you want to remove this product from your favorites?',
        'Confirmation',
        {
          confirmButtonText: 'Confirm',
          cancelButtonText: 'Cancel',
          type: 'warning',
        }
      );
    }

    // Inverser l'état local
    isFavorite.value = !isFavorite.value;

    const userData = localStorage.getItem('user') || sessionStorage.getItem('user');
    const user = JSON.parse(userData);

    const likeData = { id_produit: idProduit, user_id: user.id };
    const result = await productsApi.addLike(likeData);

    if (result.success) {
      ElMessage({
        type: isFavorite.value ? 'success' : 'info',
        message: isFavorite.value ? 'Product added to favorites.' : 'Product removed from favorites.',
      });

      fetchFavorites(); // Mettre à jour la liste des favoris

    } else {
      // Revenir en arrière si l'API renvoie une erreur
      isFavorite.value = !isFavorite.value;
      ElMessage({ type: 'error', message: result.error || 'Unable to update favorites.' });
    }

  } catch (error) {
    // Si l'utilisateur annule la confirmation, on ne fait rien
    if (error !== 'cancel') {
      console.error('Erreur toggleFavorite:', error);
      isFavorite.value = !isFavorite.value;
      ElMessage({ type: 'error', message: 'An error occurred during the update.' });
    }
  }
};

const displayNotification = (type, title, message) => {
    notificationType.value = type
    notificationTitle.value = title
    notificationMessage.value = message
    showNotification.value = true
  
    setTimeout(() => {
      closeNotification()
    }, 5000)
  }

const goBack = () => {
  window.history.back()
}

const handleOrderClick = () => {
  

  // 🧠 Sauvegarde dans le sessionStorage (fallback pour refresh ou anciennes versions de Vue Router)
  try {
    sessionStorage.setItem('lastProduct', JSON.stringify(orderState))
  } catch (error) {
    console.error('[v0] Error saving product to sessionStorage:', error)
  }

  // 🚀 Navigation avec route.state (si Vue Router ≥ 4.2)
  router.push({ 
    path: '/order-validation',
    state: orderState
  })
}

const loadAllData = async () => {
  dataLoading.value = true
  try {
    await fetchCartItems();
    await fetchFavorites();
    await fetchOrders();
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
  }else if (h) {
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

    loadAllData();
});
</script>

<style scoped>
.profile-page {
  min-height: 100vh;
  background: #f5f5f5;
  padding-bottom: 40px;
}

.page-header {
  display: flex;
  background: #fff;
  border-bottom: 1px solid #e8e8e8;
  padding: 20px 0;
  margin-bottom: 24px;
  align-items: center;
  justify-content: center;

}

.container {
  display: flex;
  max-width: 1400px;
  margin: 0 auto;
  padding: 0 16px;
}

.back-btn {
  display: inline-flex;
  align-items: center;
  gap: 8px;
  padding: 8px 16px;
  background: transparent;
  border: 1px solid #d9d9d9;
  border-radius: 6px;
  color: #666;
  font-size: 14px;
  cursor: pointer;
  transition: all 0.3s ease;
  margin-bottom: 12px;
}

.back-btn:hover {
  border-color: #fe9700;
  color: #fe9700;
}

.page-title {
  font-size: 22px;
  font-weight: 600;
  color: #333;
  margin: 0;
}

.main-content {
  display: grid;
  grid-template-columns: 280px 1fr;
  gap: 24px;
}

.sidebar-menu {
  background: #fff;
  border-radius: 8px;
  padding: 0;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
  height: fit-content;
  position: sticky;
  top: 16px;
  overflow: hidden;
}

.loading-state {
  text-align: center;
  padding: 60px 20px;
  background-color: #ffffff;
  border-radius: 12px;
  color: #fe9700;
  box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
}
.loading-state .spinner {
  border: 4px solid #f3f3f3;
  border-top: 4px solid #fe9700;
  border-radius: 50%;
  width: 40px;
  height: 40px;
  animation: spin 1s linear infinite;
  margin: 0 auto 15px auto;
}
.cart-list { display: flex; flex-direction: column; gap: 12px; }
.cart-item { background: #fff; padding: 12px; border-radius: 8px; box-shadow: 0 2px 6px rgba(0,0,0,0.03); align-items: center; }
.cart-image { width:100%; height:100%; object-fit:cover; display:block; }



@keyframes spin {
  0% { transform: rotate(0deg); }
  100% { transform: rotate(360deg); }
}

/* Added user profile header styling */
.user-profile-header {
  padding: 24px 16px;
  background: linear-gradient(135deg, #fe9700 0%, #e68900 100%);
  display: flex;
  flex-direction: column;
  align-items: center;
  text-align: center;
  border-bottom: 2px solid #e68900;
}

.user-avatar {
  width: 64px;
  height: 64px;
  background: rgba(255, 255, 255, 0.2);
  border: 3px solid rgba(255, 255, 255, 0.4);
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  margin-bottom: 12px;
  color: #fff;
}

.user-info {
  flex: 1;
}

.user-name {
  font-size: 16px;
  font-weight: 700;
  color: #fff;
  margin: 0 0 4px 0;
  line-height: 1.2;
}

.user-email {
  font-size: 14px;
  color: rgba(255, 255, 255, 0.85);
  margin: 0;
  word-break: break-word;
}

.menu-list {
  list-style: none;
  padding: 8px;
  margin: 0;
  display: flex;
  flex-direction: column;
  gap: 4px;
}

.menu-item {
  display: flex;
  align-items: center;
  gap: 12px;
  padding: 12px 16px;
  color: #666;
  text-decoration: none;
  border-radius: 6px;
  transition: all 0.3s ease;
  cursor: pointer;
  font-size: 15px;
  font-weight: 500;
}

.menu-item:hover {
  background: #f5f5f5;
  color: #fe9700;
}

.menu-item.active {
  background: #fff7e6;
  color: #fe9700;
  border-left: 3px solid #fe9700;
  padding-left: 13px;
}

.content-area {
  display: flex;
  flex-direction: column;
}

.tab-content {
  animation: fadeIn 0.3s ease;
}

@keyframes fadeIn {
  from {
    opacity: 0;
    transform: translateY(10px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

.stats-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
  gap: 16px;
  margin-bottom: 24px;
}

.stat-card {
  background: #fff;
  border-radius: 8px;
  padding: 20px;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
  display: flex;
  align-items: center;
  gap: 16px;
}

.stat-icon {
  width: 56px;
  height: 56px;
  background: #fff7e6;
  border-radius: 12px;
  display: flex;
  align-items: center;
  justify-content: center;
  flex-shrink: 0;
}

.stat-info {
  flex: 1;
}

.stat-label {
  font-size: 14px;
  color: #666;
  margin: 0 0 4px 0;
}

.stat-value {
  font-size: 28px;
  font-weight: 700;
  color: #fe9700;
  margin: 0;
}

.section-card {
  background: #fff;
  border-radius: 8px;
  padding: 24px;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
  margin-bottom: 24px;
}

.section-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 20px;
  padding-bottom: 16px;
  border-bottom: 2px solid #f0f0f0;
}

.section-title {
  font-size: 18px;
  font-weight: 600;
  color: #333;
  margin: 0;
  margin-bottom: 20px;

}

.edit-btn {
  display: flex;
  align-items: center;
  gap: 6px;
  padding: 8px 16px;
  background: #fe9700;
  color: #fff;
  border: none;
  border-radius: 6px;
  font-size: 14px;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.3s ease;
}

.edit-btn:hover {
  background: #e68900;
}

.form-grid {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 16px;
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
  font-weight: 500;
  color: #333;
  margin-bottom: 8px;
}

.input-style[readonly] {
  background: #f5f5f5;
  color: #666;
  cursor: not-allowed;
}
.product-quantity {
  display: flex;
  align-items: center;
  gap: 12px;
}

.quantity-label {
  font-size: 14px;
  color: #666;
}

.quantity-controls {
  display: flex;
  align-items: center;
  border: 1px solid #d9d9d9;
  border-radius: 6px;
  overflow: hidden;
}

.quantity-controls button {
  width: 32px;
  height: 32px;
  border: none;
  background: white;
  color: #333;
  font-size: 18px;
  cursor: pointer;
  transition: all 0.3s ease;
}

.quantity-controls button:hover:not(:disabled) {
  background: #fe9700;
  color: #fff;
}

.quantity-controls button:disabled {
  opacity: 0.5;
  cursor: not-allowed;
}
.quantity-input {
  -webkit-appearance: none;
  -moz-appearance: textfield;
  appearance: none;
  width: auto;
  height: 50px;
  border: 1px solid #d9d9d9;
  color: #333;
  border-left: none;
  border-right: none;
  text-align: center;
  font-size: 18px;
}

.quantity-input::-webkit-outer-spin-button,
.quantity-input::-webkit-inner-spin-button {
  -webkit-appearance: none;
  margin: 0;
}

.quantity-input[type="number"] {
  -moz-appearance: textfield;
  appearance: textfield;
}

.quantity-controls input {
  width: 50px;
  height: 32px;
  border: none;
  text-align: center;
  font-size: 14px;
  font-weight: 500;
}


.form-actions {
  display: flex;
  gap: 12px;
  margin-top: 20px;
  padding-top: 20px;
  border-top: 1px solid #e8e8e8;
}

.empty-state {
  align-items: center;
  padding: 60px 20px;
  color: #999;
}

/* .empty-state svg {
  margin-bottom: 16px;
  opacity: 0.5;
  display: flex;
  justify-content: center;

} */

.empty-state p {
  font-size: 16px;
  margin: 0;
}

.favorites-list {
  display: flex;
  flex-direction: column;
  gap: 12px;
}

.favorite-item {
  display: flex;
  align-items: center;
  gap: 16px;
  padding: 16px;
  background: #f9f9f9;
  border-radius: 8px;
  transition: all 0.3s ease;
}

.favorite-item:hover {
  background: #f0f0f0;
}

.favorite-image {
  width: 80px;
  height: 80px;
  object-fit: cover;
  border-radius: 8px;
  border: 1px solid #e8e8e8;
}

.favorite-info {
  flex: 1;
  padding-left: 20px;
}

.favorite-name {
  font-size: 16px;
  font-weight: 600;
  color: #333;
  margin: 0 0 4px 0;
}

.favorite-price {
  font-size: 15px;
  color: #fe9700;
  font-weight: 600;
  margin: 0;
}

.remove-btn {
  width: 36px;
  height: 36px;
  border: none;
  background: #fff;
  border: 1px solid #d9d9d9;
  border-radius: 6px;
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
}

.orders-list {
  display: flex;
  flex-direction: column;
  gap: 12px;
}

.order-item {
  padding: 16px;
  background: #f9f9f9;
  border-radius: 8px;
  border-left: 4px solid #fe9700;
  transition: all 0.3s ease;
}

.order-item:hover {
  background: #f0f0f0;
  box-shadow: 0 2px 8px rgba(254, 151, 0, 0.1);
}

.order-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 12px;
}

.order-number {
  font-size: 15px;
  font-weight: 600;
  color: #333;
}

.order-status {
  font-size: 12px;
  font-weight: 600;
  padding: 4px 12px;
  border-radius: 20px;
  text-transform: capitalize;
}

.order-status.delivered {
  background: #f6ffed;
  color: #52c41a;
}

.order-status.pending{
  background: #e6f7ff;
  color: #fe9700;
}

.order-status.canceled {
  background: #fff1f0;
  color: #ff4d4f;
}

.order-details {
  margin-bottom: 12px;
  color: #666;
  font-size: 14px;
}

.order-details p {
  margin: 4px 0;
}

.order-footer {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding-top: 12px;
  border-top: 1px solid #e8e8e8;
}

.order-date {
  font-size: 13px;
  color: #999;
}

.order-total {
  font-size: 16px;
  font-weight: 700;
  color: #fe9700;
}

@media (max-width: 768px) {
  .main-content {
    grid-template-columns: 1fr;
  }

  .sidebar-menu {
    position: static;
  }

  .menu-list {
    flex-direction: row;
    gap: 0;
  }

  .menu-item {
    flex: 1;
    justify-content: center;
    border-left: none;
    border-bottom: 3px solid transparent;
  }

  .menu-item.active {
    border-left: none;
    border-bottom: 3px solid #fe9700;
    padding-left: 16px;
  }

  .stats-grid {
    grid-template-columns: 1fr;
  }

  .form-grid {
    grid-template-columns: 1fr;
  }
}
</style>