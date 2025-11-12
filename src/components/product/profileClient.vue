<template>
  <div class="profile-page">
    <div class="page-header">
      <div class="container">
        <button class="back-btn" @click="goBack">
          <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <polyline points="15,18 9,12 15,6"></polyline>
          </svg>
          Retour
        </button>
        <h1 class="page-title">Mon Profil</h1>
      </div>
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
            Mon profil
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
            Mes favoris
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
            Mes commandes
          </a>
        </nav>
      </div>

      <!-- Content Area -->
      <div class="content-area">
        <!-- Profile Tab -->
        <div v-if="activeTab === 'profile'" class="tab-content">
          <!-- Stats Cards -->
          <div class="stats-grid">
            <div class="stat-card">
              <div class="stat-icon">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#fe9700" stroke-width="2">
                  <circle cx="9" cy="21" r="1"></circle>
                  <circle cx="20" cy="21" r="1"></circle>
                  <path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"></path>
                </svg>
              </div>
              <div class="stat-info">
                <p class="stat-label">Nombre de commandes</p>
                <p class="stat-value">{{ stats.orders }}</p>
              </div>
            </div>

            <div class="stat-card">
              <div class="stat-icon">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#fe9700" stroke-width="2">
                  <path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"></path>
                </svg>
              </div>
              <div class="stat-info">
                <p class="stat-label">Favoris</p>
                <p class="stat-value">{{ stats.favorites }}</p>
              </div>
            </div>
          </div>

          <!-- User Info Card -->
          <div class="section-card">
            <div class="section-header">
              <h2 class="section-title">Informations personnelles</h2>
              <button 
                v-if="!isEditing" 
                class="edit-btn" 
                @click="isEditing = true"
              >
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                  <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
                  <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path>
                </svg>
                Modifier
              </button>
              {{currentUser}}
            </div>

            <form @submit.prevent="saveProfile" class="form-grid">
              <div class="form-group">
                <label class="form-label">Nom</label>
                <input 
                  type="text" 
                  class="form-input" 
                  v-model="profile.full_name"
                  :readonly="!isEditing"
                >
              </div>

              <div class="form-group">
                <label class="form-label">Email</label>
                <input 
                  type="email" 
                  class="form-input" 
                  v-model="profile.email"
                  :readonly="!isEditing"
                >
              </div>

              <div class="form-group">
                <label class="form-label">Pays</label>
                <input 
                  type="text" 
                  class="form-input" 
                  v-model="profile.pays"
                  :readonly="!isEditing"
                  placeholder="Entrez votre pays"
                >
              </div>

              <div class="form-group">
                <label class="form-label">Contact</label>
                <input 
                  type="text" 
                  class="form-input" 
                  v-model="profile.contact"
                  :readonly="!isEditing"
                  placeholder="Entrez votre contact"
                >
              </div>

              <div class="form-group full-width">
                <label class="form-label">Adresse</label>
                <textarea 
                  class="form-textarea" 
                  v-model="profile.adresse"
                  :readonly="!isEditing"
                  placeholder="Entrez votre adresse complète"
                  rows="3"
                ></textarea>
              </div>

              <div v-if="isEditing" class="form-actions full-width">
                <button type="submit" class="save-btn">
                  <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"></path>
                    <polyline points="17 21 17 13 7 13 7 21"></polyline>
                    <polyline points="7 3 7 8 15 8"></polyline>
                  </svg>
                  Enregistrer les modifications
                </button>
                <button type="button" class="cancel-btn" @click="cancelEdit">
                  Annuler
                </button>
              </div>
            </form>
          </div>
        </div>

        <!-- Favorites Tab -->
        <div v-if="activeTab === 'favorites'" class="tab-content">
          <div class="section-card">
            <h2 class="section-title">Mes favoris</h2>
            <div v-if="favorites.length === 0" class="empty-state">
              <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="#ccc" stroke-width="2">
                <path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"></path>
              </svg>
              <p>Vous n'avez pas encore d'articles en favoris</p>
            </div>
            <div v-else class="favorites-list">
              <div v-for="item in userFavorites" :key="item.favorite_id" class="favorite-item">
                 <div @click="goToProduct(item.slug)" style="display: flex; align-items: center; flex: 1; cursor: pointer;">
                    <img :src="item.primary_image" :alt="item.name" class="favorite-image">
                    <div class="favorite-info">
                    <h3 class="favorite-name">{{ item.name }}</h3>
                    <p class="favorite-price">{{ formatPrice(item.unit_price) }}</p>
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
          <div class="section-card">
            <h2 class="section-title">Mes commandes</h2>
            <div v-if="orders.length === 0" class="empty-state">
              <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="#ccc" stroke-width="2">
                <circle cx="9" cy="21" r="1"></circle>
                <circle cx="20" cy="21" r="1"></circle>
                <path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"></path>
              </svg>
              <p>Vous n'avez pas encore passé de commande</p>
            </div>
            <div v-else class="orders-list">
              <div v-for="order in orders" :key="order.id" class="order-item">
                <div class="order-header">
                  <span class="order-number">Commande #{{ order.numero }}</span>
                  <span class="order-status" :class="order.statut">{{ order.statut }}</span>
                </div>
                <div class="order-details">
                  <p>{{ order.product }}</p>
                  <p>Quantité: {{ order.quantity }}</p>
                </div>
                <div class="order-footer">
                  <span class="order-date">{{ order.date }}</span>
                  <span class="order-total">{{ formatPrice(order.total) }}</span>
                </div>
              </div>
            </div>
          </div>

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

const activeTab = ref('profile')
const isFavorite = ref(true)
const isEditing = ref(false)
const currentUser = ref(null)
 const router = useRouter()

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
    statut: 'Livré',
    product: 'Produit A',
    quantity: 2,
    total: 89000,
    date: '15 Nov 2024'
  },
  {
    id: 2,
    numero: 'CMD-002',
    statut: 'En cours',
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

const formatPrice = (price) => {
  return new Intl.NumberFormat('fr-CI', {
    style: 'currency',
    currency: 'XOF'
  }).format(price)
}

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
        'Êtes-vous sûr de vouloir retirer ce produit de vos favoris ?',
        'Confirmation',
        {
          confirmButtonText: 'Confirmer',
          cancelButtonText: 'Annuler',
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
        message: isFavorite.value ? 'Produit ajouté aux favoris.' : 'Produit retiré des favoris.',
      });

      fetchFavorites(); // Mettre à jour la liste des favoris
    } else {
      // Revenir en arrière si l'API renvoie une erreur
      isFavorite.value = !isFavorite.value;
      ElMessage({ type: 'error', message: result.error || 'Impossible de mettre à jour les favoris.' });
    }

  } catch (error) {
    // Si l'utilisateur annule la confirmation, on ne fait rien
    if (error !== 'cancel') {
      console.error('Erreur toggleFavorite:', error);
      isFavorite.value = !isFavorite.value;
      ElMessage({ type: 'error', message: 'Une erreur est survenue lors de la mise à jour.' });
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
  background: #fff;
  border-bottom: 1px solid #e8e8e8;
  padding: 20px 0;
  margin-bottom: 24px;
}

.container {
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
  font-size: 12px;
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

.form-input,
.form-textarea {
  padding: 10px 12px;
  border: 1px solid #d9d9d9;
  border-radius: 6px;
  font-size: 14px;
  font-family: inherit;
  transition: all 0.3s ease;
}

.form-input:focus,
.form-textarea:focus {
  outline: none;
  border-color: #fe9700;
  box-shadow: 0 0 0 2px rgba(254, 151, 0, 0.1);
}

.form-input[readonly] {
  background: #f5f5f5;
  color: #666;
  cursor: not-allowed;
}

.form-textarea {
  resize: vertical;
}

.form-actions {
  display: flex;
  gap: 12px;
  margin-top: 20px;
  padding-top: 20px;
  border-top: 1px solid #e8e8e8;
}

.save-btn,
.cancel-btn {
  flex: 1;
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 8px;
  padding: 12px 24px;
  border: none;
  border-radius: 8px;
  font-size: 15px;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.3s ease;
}

.save-btn {
  background: #fe9700;
  color: #fff;
}

.save-btn:hover {
  background: #e68900;
  transform: translateY(-2px);
  box-shadow: 0 4px 12px rgba(254, 151, 0, 0.3);
}

.cancel-btn {
  background: #f5f5f5;
  color: #666;
}

.cancel-btn:hover {
  background: #e8e8e8;
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

.order-status.Livré {
  background: #f6ffed;
  color: #52c41a;
}

.order-status.En_cours{
  background: #e6f7ff;
  color: #1890ff;
}

.order-status.Annulée {
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