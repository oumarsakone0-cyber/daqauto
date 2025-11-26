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
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <circle cx="9" cy="21" r="1"></circle>
              <circle cx="20" cy="21" r="1"></circle>
              <path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"></path>
            </svg>
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
            <a href="#" @click.prevent="activeTab = 'favorites'">
              <div class="stat-card">
              <div class="stat-icon">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#fe9700" stroke-width="2">
                  <path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"></path>
                </svg>
              </div>
              <div class="stat-info">
                <p class="stat-label">Favorites</p>
                <p class="stat-value">{{ stats.favorites }}</p>
              </div>
            </div>
            </a>
            <a href="#" @click.prevent="activeTab = 'orders'">
              <div class="stat-card">
              <div class="stat-icon">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#fe9700" stroke-width="2">
                  <circle cx="9" cy="21" r="1"></circle>
                  <circle cx="20" cy="21" r="1"></circle>
                  <path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"></path>
                </svg>
              </div>
              <div class="stat-info">
                <p class="stat-label">Orders</p>
                <p class="stat-value">{{ stats.orders }}</p>
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

        <!-- Favorites Tab -->
        <div v-if="activeTab === 'favorites'" class="tab-content">
          <div class="section-card">
            <h2 class="section-title">My favorites</h2>
            <div v-if="favorites.length === 0" class="empty-state">
              <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="#ccc" stroke-width="2">
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
          <!-- <div class="section-card">
            <h2 class="section-title">My Orders</h2>
            <div v-if="orders.length === 0" class="empty-state">
              <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="#ccc" stroke-width="2">
                <circle cx="9" cy="21" r="1"></circle>
                <circle cx="20" cy="21" r="1"></circle>
                <path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"></path>
              </svg>
              <p>You have not yet placed an order</p>
            </div>
            <div v-else class="orders-list">
              <div v-for="order in orders" :key="order.id" class="order-item">
                <div class="order-header">
                  <span class="order-number">Order #{{ order.numero }}</span>
                  <span class="order-status" :class="order.statut.toLowerCase()">{{ order.statut }}</span>
                </div>
                <div class="order-details">
                  <p>{{ order.product }}</p>
                  <p>Quantity: {{ order.quantity }}</p>
                </div>
                <div class="order-footer">
                  <span class="order-date">{{ order.date }}</span>
                  <span class="order-total">{{ formatPrice(order.total) }}</span>
                </div>
              </div>
            </div>
          </div> -->

        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import Commandes from '../product/MesCommandes.vue'
import { productsApi } from '../../services/api'
import { useRoute, useRouter } from 'vue-router'
import { ElMessageBox, ElMessage } from 'element-plus';
import { RefreshCcw as RefreshIcon } from 'lucide-vue-next';
import { formatPrice } from '../../services/formatPrice';

const activeTab = ref('profile')
const isFavorite = ref(true)
const isEditing = ref(false)
const dataLoading = ref(false)
const currentUser = ref(null)
 const router = useRouter()

const userFavorites = ref([]);
const status = ref([
  "deliverd",
  "pending",
  "canceled"
])

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

const stats = ref({
  orders: 5,
  favorites: 12
})

const favorites = ref([
  {
    id: 1,
    name: 'Produit 1',
    price: 45000,
    image: '/placeholder.svg?height=100&width=100'
  },
  {
    id: 2,
    name: 'Produit 2',
    price: 32000,
    image: '/placeholder.svg?height=100&width=100'
  }
])

const orders = ref([
  {
    id: 1,
    numero: 'CMD-001',
    statut: 'Delivered',
    product: 'Produit A',
    quantity: 2,
    total: 89000,
    date: '15 Nov 2024'
  },
  {
    id: 2,
    numero: 'CMD-002',
    statut: 'Pending',
    product: 'Produit B',
    quantity: 1,
    total: 45000,
    date: '20 Nov 2024'
  },
  {
    id: 2,
    numero: 'CMD-002',
    statut: 'Canceled',
    product: 'Produit B',
    quantity: 1,
    total: 45000,
    date: '20 Nov 2024'
  }
])

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

const loadAllData = async () => {
  dataLoading.value = true
  try {
    await fetchFavorites();
  } finally {
    dataLoading.value = false
  }
}

onMounted(async () => {

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

    fetchFavorites();
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


.form-actions {
  display: flex;
  gap: 12px;
  margin-top: 20px;
  padding-top: 20px;
  border-top: 1px solid #e8e8e8;
}

.empty-state {
  text-align: center;
  padding: 60px 20px;
  color: #999;
}

.empty-state svg {
  margin-bottom: 16px;
  opacity: 0.5;
}

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