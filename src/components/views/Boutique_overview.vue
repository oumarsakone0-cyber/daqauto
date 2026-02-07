<template>
  <div class="min-h-screen bg-gray-50">
    <!-- Loading state -->
    <div v-if="isLoading" class="loading-container">
      <div class="loading-spinner"></div>
      <p class="text-gray-600">Loading company overview...</p>
    </div>

    <!-- Error state -->
    <div v-else-if="error" class="error-container">
      <div class="error-icon">⚠️</div>
      <h2>Loading error</h2>
      <p>{{ error }}</p>
      <button @click="loadCompanyData" class="retry-btn">Retry</button>
    </div>

    <!-- Company Overview Content -->
    <div v-else class="company-page">
      <!-- Shop Header Banner (même style que la page boutique) -->
      <div class="shop-header-banner">
        <div class="shop-banner-bg" :style="{ backgroundImage: `url(${shop.banner_image || 'https://www.cnhtcgroup.com/wp-content/uploads/2024/12/HOWO-light-truck-1920.jpg'})` }"></div>
        <div class="shop-header-overlay"></div>
        
        <div class="container relative z-10">
          <div class="shop-header-content">
            <!-- Shop Logo -->
            <div class="shop-logo-container">
              <img :src="shop.logo || 'https://static.vecteezy.com/system/resources/thumbnails/000/583/708/small/shop.jpg'" :alt="shop.name" class="shop-logo">
              <div class="shop-verified-badge" v-if="shop.is_verified">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="currentColor">
                  <path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
              </div>
            </div>

            <!-- Shop Info -->
            <div class="shop-info">
              <h1 class="shop-name">{{ shop.name }}</h1>
              <p class="shop-description">
                <span style="background: #ffffffa1; padding: 5px; border-radius: 5px">
                  {{ shop.description || 'No. 128 Zhongshan Road, Yuexiu District, Guangzhou, Guangdong 510000 - China' }}
                </span>
              </p>
              
              <div class="shop-meta">
                <span style="background: #ffffffa1; padding: 5px; border-radius: 5px; display: flex; gap: inherit;">
                  <div class="shop-meta-item">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                      <path d="M20 10c0 6-8 12-8 12s-8-6-8-12a8 8 0 0 1 16 0Z"></path>
                      <circle cx="12" cy="10" r="3"></circle>
                    </svg>
                    <span>{{ shop.location || 'Not specified' }}</span>
                  </div>
                  <div class="shop-meta-item">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                      <path d="M12 2L2 7l10 5 10-5-10-5z"></path>
                      <polyline points="2 17 12 22 22 17"></polyline>
                      <polyline points="2 12 12 17 22 12"></polyline>
                    </svg>
                    <span>{{ totalProducts || 0 }} products</span>
                  </div>
                  <div class="shop-meta-item">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                      <path d="M12 2L2 7l10 5 10-5-10-5z"></path>
                      <polyline points="2 17 12 22 22 17"></polyline>
                      <polyline points="2 12 12 17 22 12"></polyline>
                    </svg>
                    <span>{{ shop.years_in_business || 2 }} Years on Wabili</span>
                  </div>
                  <div class="shop-meta-item2">
                    <BadgeCheckIcon class="h-4 w-4 blue-color inline-block" />
                    <span class="badge blue-color">Shop Verified by Wabili</span>
                  </div>
                </span>
              </div>

              <div class="shop-actions">
                <button @click="goToShop" class="btn-follow">
                  <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path>
                    <polyline points="9 22 9 12 15 12 15 22"></polyline>
                  </svg>
                  <span>View Shop</span>
                </button>
                <button @click.stop="handleChatClick" class="btn-follow">
                  <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M21 15a4 4 0 0 1-4 4H7l-4 4V7a4 4 0 0 1 4-4h10a4 4 0 0 1 4 4z"/>
                    <line x1="8" y1="10" x2="8" y2="10"/>
                    <line x1="12" y1="10" x2="12" y2="10"/>
                    <line x1="16" y1="10" x2="16" y2="10"/>
                  </svg>
                  <span>Chat Now</span>
                </button>
                <button @click="followShop" class="btn-follow" :class="{ 'is-following': isFollowing }">
                  <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"></path>
                    <circle cx="9" cy="7" r="4"></circle>
                    <line x1="19" x2="19" y1="8" y2="14"></line>
                    <line x1="22" x2="16" y1="11" y2="11"></line>
                  </svg>
                  <span>{{ isFollowing ? 'Following' : 'Follow' }}</span>
                </button>
                <button @click="shareShop" class="btn-share">
                  <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <circle cx="18" cy="5" r="3"></circle>
                    <circle cx="6" cy="12" r="3"></circle>
                    <circle cx="18" cy="19" r="3"></circle>
                    <line x1="8.59" x2="15.42" y1="13.51" y2="17.49"></line>
                    <line x1="15.41" x2="8.59" y1="6.51" y2="10.49"></line>
                  </svg>
                </button>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Main Content -->
      <div class="container">
        <!-- Tabs Navigation -->
        <div class="tabs-section">
          <button 
            @click="activeTab = 'profile'" 
            class="tab-button"
            :class="{ 'active': activeTab === 'profile' }"
          >
            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
              <polyline points="14 2 14 8 20 8"></polyline>
            </svg>
            <span>Company Profile</span>
          </button>
          <button 
            @click="activeTab = 'media'" 
            class="tab-button"
            :class="{ 'active': activeTab === 'media' }"
          >
            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <polygon points="23 7 16 12 23 17 23 7"></polygon>
              <rect x="1" y="5" width="15" height="14" rx="2" ry="2"></rect>
            </svg>
            <span>Presentation Media</span>
          </button>
        </div>

        <!-- Company Profile Tab -->
        <div v-if="activeTab === 'profile'" class="tab-content">
          <!-- About Company -->
          <div class="content-card">
            <h2 class="section-title">
              <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path>
              </svg>
              About Company
            </h2>
            <div class="company-profile-text">{{ companyProfile }}</div>
          </div>

          <!-- Certificates -->
          <div class="content-card">
            <h2 class="section-title">
              <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <circle cx="12" cy="8" r="7"></circle>
                <polyline points="8.21 13.89 7 23 12 20 17 23 15.79 13.88"></polyline>
              </svg>
              Certifications & Awards
            </h2>
            <div class="certificates-grid">
              <div v-for="cert in certificates" :key="cert.id" class="certificate-card">
                <img :src="cert.image" :alt="cert.name" class="certificate-image">
                <div class="certificate-content">
                  <h3 class="certificate-name">{{ cert.name }}</h3>
                  <p class="certificate-issuer">{{ cert.issuer }}</p>
                  <div class="certificate-dates">
                    <div>Issued: {{ cert.issue_date }}</div>
                    <div>Expires: {{ cert.expiry_date }}</div>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- Reviews & Ratings -->
          <div class="content-card">
            <h2 class="section-title">
              <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"></polygon>
              </svg>
              Reviews & Ratings
            </h2>

            <!-- Rating Summary -->
            <div class="rating-summary">
              <div class="rating-overview">
                <div class="rating-score">{{ shop.rating || 4.8 }}</div>
                <div class="rating-stars">
                  <span v-for="star in 5" :key="star" class="star" :class="{ filled: star <= Math.round(shop.rating || 4.8) }">★</span>
                </div>
                <div class="rating-count">{{ totalReviews }} reviews</div>
              </div>

              <div class="rating-bars">
                <div v-for="star in [5, 4, 3, 2, 1]" :key="star" class="rating-bar-row">
                  <span class="rating-bar-label">{{ star }}★</span>
                  <div class="rating-bar">
                    <div class="rating-bar-fill" :style="{ width: getRatingPercentage(star) + '%' }"></div>
                  </div>
                  <span class="rating-bar-percentage">{{ getRatingPercentage(star) }}%</span>
                </div>
              </div>
            </div>

            <!-- Individual Reviews -->
            <div class="reviews-list">
              <div v-for="review in reviews" :key="review.id" class="review-item">
                <div class="review-avatar">{{ review.author.charAt(0) }}</div>
                <div class="review-content">
                  <div class="review-header">
                    <h4 class="review-author">{{ review.author }}</h4>
                    <svg v-if="review.verified" xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="currentColor" class="verified-icon">
                      <path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <span class="review-country">• {{ review.country }}</span>
                  </div>
                  <div class="review-rating">
                    <span v-for="star in 5" :key="star" class="star" :class="{ filled: star <= review.rating }">★</span>
                    <span class="review-date">{{ review.date }}</span>
                  </div>
                  <p class="review-comment">{{ review.comment }}</p>
                </div>
              </div>
            </div>
          </div>

          <!-- Contact Information -->
          <div class="content-card">
            <h2 class="section-title">Contact Information</h2>
            <div class="contact-grid">
              <div class="contact-item">
                <div class="contact-icon bg-orange-100">
                  <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="text-orange-500">
                    <path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"></path>
                  </svg>
                </div>
                <div>
                  <div class="contact-label">Phone</div>
                  <div class="contact-value">{{ shop.phone || '+86 20 8888 8888' }}</div>
                </div>
              </div>

              <div class="contact-item">
                <div class="contact-icon bg-blue-100">
                  <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="text-blue-500">
                    <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"></path>
                    <polyline points="22,6 12,13 2,6"></polyline>
                  </svg>
                </div>
                <div>
                  <div class="contact-label">Email</div>
                  <div class="contact-value">{{ shop.email || 'info@company.com' }}</div>
                </div>
              </div>

              <div class="contact-item">
                <div class="contact-icon bg-green-100">
                  <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="text-green-500">
                    <circle cx="12" cy="12" r="10"></circle>
                    <line x1="2" y1="12" x2="22" y2="12"></line>
                    <path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"></path>
                  </svg>
                </div>
                <div>
                  <div class="contact-label">Website</div>
                  <div class="contact-value">{{ shop.website || 'www.company.com' }}</div>
                </div>
              </div>

              <div class="contact-item">
                <div class="contact-icon bg-purple-100">
                  <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="text-purple-500">
                    <path d="M20 10c0 6-8 12-8 12s-8-6-8-12a8 8 0 0 1 16 0Z"></path>
                    <circle cx="12" cy="10" r="3"></circle>
                  </svg>
                </div>
                <div>
                  <div class="contact-label">Address</div>
                  <div class="contact-value">{{ shop.address || shop.description }}</div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Presentation Media Tab -->
        <div v-if="activeTab === 'media'" class="tab-content">
          <!-- Company Video -->
          <div class="content-card">
            <h2 class="section-title">
              <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <polygon points="23 7 16 12 23 17 23 7"></polygon>
                <rect x="1" y="5" width="15" height="14" rx="2" ry="2"></rect>
              </svg>
              Company Presentation Video
            </h2>
            <div class="video-container">
              <iframe
                :src="videoUrl"
                class="video-iframe"
                allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                allowfullscreen
              ></iframe>
            </div>
          </div>

          <!-- Photo Gallery -->
          <div class="content-card">
            <h2 class="section-title">Photo Gallery</h2>
            <div class="gallery-grid">
              <div v-for="(image, index) in galleryImages" :key="index" class="gallery-item">
                <img :src="image" :alt="`Gallery ${index + 1}`" class="gallery-image">
              </div>
            </div>
          </div>

          <!-- Company Highlights -->
          <div class="content-card">
            <h2 class="section-title">Company Highlights</h2>
            <div class="highlights-grid">
              <div class="highlight-card bg-gradient-orange">
                <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="highlight-icon">
                  <path d="M12 2L2 7l10 5 10-5-10-5z"></path>
                  <polyline points="2 17 12 22 22 17"></polyline>
                  <polyline points="2 12 12 17 22 12"></polyline>
                </svg>
                <div class="highlight-value">{{ totalProducts }}</div>
                <div class="highlight-label">Products</div>
              </div>

              <div class="highlight-card bg-gradient-blue">
                <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="highlight-icon">
                  <polyline points="23 6 13.5 15.5 8.5 10.5 1 18"></polyline>
                  <polyline points="17 6 23 6 23 12"></polyline>
                </svg>
                <div class="highlight-value">{{ totalOrders }}</div>
                <div class="highlight-label">Total Orders</div>
              </div>

              <div class="highlight-card bg-gradient-green">
                <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="highlight-icon">
                  <polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"></polygon>
                </svg>
                <div class="highlight-value">{{ shop.rating || 4.8 }}/5</div>
                <div class="highlight-label">Average Rating</div>
              </div>

              <div class="highlight-card bg-gradient-purple">
                <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="highlight-icon">
                  <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"></path>
                  <circle cx="9" cy="7" r="4"></circle>
                  <path d="M22 21v-2a4 4 0 0 0-3-3.87"></path>
                  <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
                </svg>
                <div class="highlight-value">{{ responseRate }}%</div>
                <div class="highlight-label">Response Rate</div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { useRouter, useRoute } from 'vue-router'
import { productsApi } from '../../services/api.js'
import { BadgeCheckIcon } from 'lucide-vue-next'
import { useChatStore } from '../../stores/chat'

const router = useRouter()
const route = useRoute()
const chatStore = useChatStore()

const boutiqueId = computed(() => route.params.boutique_id)

const isLoading = ref(true)
const error = ref(null)
const activeTab = ref('profile')
const isFollowing = ref(false)

// Shop data
const shop = ref({
  id: null,
  name: '',
  description: '',
  logo: '',
  banner_image: '',
  location: '',
  rating: 0,
  is_verified: false,
  phone: '',
  email: '',
  website: '',
  address: '',
  years_in_business: 0
})

const totalProducts = ref(0)
const totalOrders = ref(1243)
const totalReviews = ref(248)
const responseRate = ref(98)

const companyProfile = ref(`CNHTC Guangzhou Trading Co., Ltd was established in 2018 as a premier manufacturer and exporter of commercial vehicles and heavy-duty trucks. With over 6 years of experience in the industry, we have built a reputation for delivering high-quality products and exceptional customer service.

Our company specializes in the production and distribution of HOWO brand trucks, Sinotruk vehicles, and various commercial transportation solutions. We serve clients across Africa, Middle East, Southeast Asia, and South America.

We are committed to providing reliable, durable, and cost-effective transportation solutions that meet international standards. Our factory spans over 50,000 square meters with state-of-the-art manufacturing facilities and a team of experienced engineers and technicians.`)

const certificates = ref([
  {
    id: 1,
    name: 'ISO 9001:2015',
    issuer: 'International Organization for Standardization',
    issue_date: 'Mar 2020',
    expiry_date: 'Mar 2023',
    image: 'https://images.unsplash.com/photo-1450101499163-c8848c66ca85?w=400'
  },
  {
    id: 2,
    name: 'CE Certification',
    issuer: 'European Conformity',
    issue_date: 'Jun 2021',
    expiry_date: 'Jun 2024',
    image: 'https://images.unsplash.com/photo-1554224155-6726b3ff858f?w=400'
  },
  {
    id: 3,
    name: 'SGS Verification',
    issuer: 'SGS Group',
    issue_date: 'Jan 2022',
    expiry_date: 'Jan 2025',
    image: 'https://images.unsplash.com/photo-1589829545856-d10d557cf95f?w=400'
  }
])

const reviews = ref([
  {
    id: 1,
    author: 'John Smith',
    country: 'Nigeria',
    rating: 5,
    date: 'Jan 2024',
    comment: 'Excellent quality trucks and professional service. Highly recommended for bulk orders.',
    verified: true
  },
  {
    id: 2,
    author: 'Mohammed Ali',
    country: 'Kenya',
    rating: 5,
    date: 'Dec 2023',
    comment: 'Very responsive team and good product quality. Delivery was on time.',
    verified: true
  },
  {
    id: 3,
    author: 'Sarah Johnson',
    country: 'South Africa',
    rating: 4,
    date: 'Nov 2023',
    comment: 'Good experience overall. The trucks meet our requirements perfectly.',
    verified: true
  }
])

const videoUrl = ref('https://www.youtube.com/embed/dQw4w9WgXcQ')
const galleryImages = ref([
  'https://images.unsplash.com/photo-1586528116311-ad8dd3c8310d?w=800',
  'https://images.unsplash.com/photo-1565043666747-69f6646db940?w=800',
  'https://images.unsplash.com/photo-1519003722824-194d4455a60c?w=800',
  'https://images.unsplash.com/photo-1581094271901-8022df4466f9?w=800'
])

const getRatingPercentage = (star) => {
  const percentages = { 5: 75, 4: 20, 3: 3, 2: 1, 1: 1 }
  return percentages[star] || 0
}

const goToShop = () => {
  router.push(`/boutique_page/${boutiqueId.value}`)
}

const handleChatClick = async () => {
  try {
    await chatStore.setSupplier({ ...shop.value })
    if (chatStore.isMobile) {
      chatStore.openChat()
    } else {
      chatStore.openDesktopChat()
    }
  } catch (error) {
    console.error("❌ Erreur ouverture chat:", error)
  }
}

const followShop = () => {
  isFollowing.value = !isFollowing.value
}

const shareShop = () => {
  if (navigator.share) {
    navigator.share({
      title: shop.value.name,
      text: shop.value.description,
      url: window.location.href
    })
  }
}

const loadCompanyData = async () => {
  if (!boutiqueId.value) {
    error.value = 'ID de boutique manquant'
    isLoading.value = false
    return
  }

  try {
    isLoading.value = true
    error.value = null
    
    // Charger les données de la boutique
    const result = await productsApi.getProducts({
      action: 'list',
      boutique_id: boutiqueId.value,
      limit: 1,
      page: 1
    })

    if (result.success && result.boutique) {
      shop.value = {
        id: result.boutique.id,
        name: result.boutique.name,
        description: result.boutique.description,
        logo: result.boutique.logo,
        banner_image: result.boutique.banner_image,
        location: result.boutique.address,
        rating: result.boutique.rating || 4.8,
        is_verified: result.boutique.is_verified,
        phone: result.boutique.phone,
        email: result.boutique.email,
        website: result.boutique.website,
        address: result.boutique.address,
        years_in_business: result.boutique.years_in_business || 2
      }
      
      totalProducts.value = result.pagination?.total || 0
    }
    
  } catch (err) {
    console.error('Erreur lors du chargement:', err)
    error.value = 'Impossible de charger les données'
  } finally {
    isLoading.value = false
  }
}

onMounted(() => {
  loadCompanyData()
})
</script>

<style scoped>
.container { max-width: 1500px; margin: 0 auto; padding: 0 20px; }

.loading-container, .error-container { display: flex; flex-direction: column; align-items: center; justify-content: center; min-height: 400px; padding: 40px; }
.loading-spinner { width: 40px; height: 40px; border: 4px solid #f3f3f3; border-top: 4px solid #fe7900; border-radius: 50%; animation: spin 1s linear infinite; }
@keyframes spin { 0% { transform: rotate(0deg); } 100% { transform: rotate(360deg); } }
.error-icon { font-size: 48px; margin-bottom: 16px; }
.retry-btn { margin-top: 16px; padding: 10px 24px; background: linear-gradient(90deg, #fe7900, #ff5a01); color: white; border: none; border-radius: 8px; cursor: pointer; font-weight: 600; transition: all 0.3s; }
.retry-btn:hover { opacity: 0.9; transform: translateY(-2px); }

/* Shop Header Banner (même style que page boutique) */
.shop-header-banner { position: relative; background: #f8f9fa; padding: 60px 0 40px; margin-bottom: 32px; overflow: hidden; }
.shop-banner-bg { position: absolute; top: 0; left: 0; right: 0; bottom: 0; background-size: cover; background-position: center; opacity: 0.4; }
.shop-header-overlay { position: absolute; top: 0; left: 0; right: 0; bottom: 0; background: linear-gradient(135deg, rgba(254, 121, 0, 0.1) 0%, rgba(24, 189, 133, 0.1) 100%); }
.shop-header-content { display: flex; gap: 32px; align-items: flex-start; }
.shop-logo-container { position: relative; flex-shrink: 0; }
.shop-logo { width: 120px; height: 120px; border-radius: 16px; border: 4px solid white; background: white; object-fit: cover; box-shadow: 0 4px 16px rgba(0, 0, 0, 0.1); }
.shop-verified-badge { position: absolute; bottom: -4px; right: -4px; width: 32px; height: 32px; background: linear-gradient(135deg, #18bd85 0%, #10976a 100%); border-radius: 50%; display: flex; align-items: center; justify-content: center; color: white; border: 3px solid white; box-shadow: 0 2px 8px rgba(0, 0, 0, 0.15); }
.shop-info { flex: 1; }
.shop-name { font-size: 32px; font-weight: 700; color: #333; margin: 0 0 8px 0; }
.shop-description { font-size: 16px; color: #666; margin: 0 0 16px 0; }
.shop-meta { display: flex; flex-wrap: wrap; gap: 24px; margin-bottom: 24px; }
.shop-meta-item, .shop-meta-item2 { display: flex; align-items: center; gap: 6px; font-size: 14px; color: #666; }
.shop-meta-item svg { color: #fe7900; }
.shop-meta-item2 svg { color: #1890ff; }
.blue-color { color: #1890ff; }
.shop-actions { display: flex; gap: 12px; }
.btn-follow { display: flex; align-items: center; gap: 8px; padding: 10px 20px; border-radius: 8px; font-size: 14px; font-weight: 600; cursor: pointer; transition: all 0.3s; border: none; background: linear-gradient(135deg, #fe9700 0%, #ff8c00 100%); color: white; }
.btn-follow.is-following { background: #18bd85; }
.btn-follow:hover { opacity: 0.9; transform: translateY(-2px); }
.btn-share { display: flex; align-items: center; justify-content: center; width: 44px; height: 44px; border-radius: 8px; background: white; border: 2px solid #e8e8e8; cursor: pointer; transition: all 0.3s; }
.btn-share:hover { border-color: #fe7900; color: #fe7900; }

/* Tabs */
.tabs-section { background: white; border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.08); margin-bottom: 24px; overflow: hidden; display: flex; border-bottom: 1px solid #f0f0f0; }
.tab-button { flex: 1; padding: 16px 24px; border: none; background: none; display: flex; align-items: center; justify-content: center; gap: 8px; font-size: 15px; font-weight: 600; color: #666; cursor: pointer; transition: all 0.3s; border-bottom: 2px solid transparent; }
.tab-button:hover { color: #fe7900; }
.tab-button.active { color: #fe7900; border-bottom-color: #fe7900; background: #fff5ed; }

/* Content */
.tab-content { display: flex; flex-direction: column; gap: 24px; padding-bottom: 40px; }
.content-card { background: white; border-radius: 12px; padding: 32px; box-shadow: 0 2px 8px rgba(0,0,0,0.08); }
.section-title { font-size: 24px; font-weight: 700; color: #333; margin: 0 0 24px; display: flex; align-items: center; gap: 8px; }
.section-title svg { color: #fe7900; }
.company-profile-text { color: #666; line-height: 1.8; white-space: pre-line; }

/* Certificates */
.certificates-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 24px; }
.certificate-card { border: 1px solid #e8e8e8; border-radius: 8px; overflow: hidden; transition: all 0.3s; }
.certificate-card:hover { box-shadow: 0 4px 16px rgba(0,0,0,0.1); transform: translateY(-4px); }
.certificate-image { width: 100%; height: 180px; object-fit: cover; }
.certificate-content { padding: 16px; }
.certificate-name { font-size: 16px; font-weight: 700; color: #333; margin: 0 0 8px; }
.certificate-issuer { font-size: 14px; color: #666; margin: 0 0 12px; }
.certificate-dates { font-size: 12px; color: #999; }

/* Reviews */
.rating-summary { display: flex; gap: 48px; margin-bottom: 32px; padding: 32px; background: linear-gradient(135deg, #fff5ed, #fff9f0); border-radius: 12px; }
.rating-overview { text-align: center; }
.rating-score { font-size: 56px; font-weight: 700; color: #333; }
.rating-stars { display: flex; gap: 4px; justify-content: center; margin: 8px 0; }
.star { font-size: 20px; color: #ddd; }
.star.filled { color: #fbbf24; }
.rating-count { font-size: 14px; color: #666; }
.rating-bars { flex: 1; }
.rating-bar-row { display: flex; align-items: center; gap: 12px; margin-bottom: 8px; }
.rating-bar-label { width: 32px; font-size: 14px; }
.rating-bar { flex: 1; height: 8px; background: #e8e8e8; border-radius: 4px; overflow: hidden; }
.rating-bar-fill { height: 100%; background: #fbbf24; }
.rating-bar-percentage { width: 48px; font-size: 12px; color: #666; text-align: right; }

.reviews-list { display: flex; flex-direction: column; gap: 24px; }
.review-item { display: flex; gap: 16px; padding-bottom: 24px; border-bottom: 1px solid #f0f0f0; }
.review-item:last-child { border-bottom: none; }
.review-avatar { width: 48px; height: 48px; border-radius: 50%; background: linear-gradient(135deg, #fe7900, #ff5a01); display: flex; align-items: center; justify-content: center; color: white; font-weight: 700; font-size: 18px; flex-shrink: 0; }
.review-content { flex: 1; }
.review-header { display: flex; align-items: center; gap: 8px; margin-bottom: 8px; }
.review-author { font-size: 15px; font-weight: 600; color: #333; margin: 0; }
.verified-icon { color: #1890ff; }
.review-country { font-size: 13px; color: #999; }
.review-rating { display: flex; align-items: center; gap: 8px; margin-bottom: 12px; }
.review-rating .star { font-size: 14px; }
.review-date { font-size: 12px; color: #999; }
.review-comment { font-size: 14px; color: #666; line-height: 1.6; margin: 0; }

/* Contact */
.contact-grid { display: grid; grid-template-columns: repeat(2, 1fr); gap: 24px; }
.contact-item { display: flex; align-items: center; gap: 16px; }
.contact-icon { width: 48px; height: 48px; border-radius: 12px; display: flex; align-items: center; justify-content: center; }
.bg-orange-100 { background: #fff5ed; }
.bg-blue-100 { background: #eff6ff; }
.bg-green-100 { background: #f0fdf4; }
.bg-purple-100 { background: #faf5ff; }
.text-orange-500 { color: #fe7900; }
.text-blue-500 { color: #3b82f6; }
.text-green-500 { color: #10b981; }
.text-purple-500 { color: #a855f7; }
.contact-label { font-size: 13px; color: #999; margin-bottom: 4px; }
.contact-value { font-size: 15px; font-weight: 600; color: #333; }

/* Video */
.video-container { aspect-ratio: 16/9; background: #000; border-radius: 12px; overflow: hidden; }
.video-iframe { width: 100%; height: 100%; border: none; }

/* Gallery */
.gallery-grid { display: grid; grid-template-columns: repeat(2, 1fr); gap: 16px; }
.gallery-item { aspect-ratio: 16/9; border-radius: 12px; overflow: hidden; cursor: pointer; transition: all 0.3s; }
.gallery-item:hover { opacity: 0.9; transform: scale(1.02); }
.gallery-image { width: 100%; height: 100%; object-fit: cover; }

/* Highlights */
.highlights-grid { display: grid; grid-template-columns: repeat(4, 1fr); gap: 24px; }
.highlight-card { text-align: center; padding: 32px; border-radius: 12px; }
.bg-gradient-orange { background: linear-gradient(135deg, #fff5ed, #ffe4cc); }
.bg-gradient-blue { background: linear-gradient(135deg, #eff6ff, #dbeafe); }
.bg-gradient-green { background: linear-gradient(135deg, #f0fdf4, #dcfce7); }
.bg-gradient-purple { background: linear-gradient(135deg, #faf5ff, #f3e8ff); }
.highlight-icon { color: #fe7900; margin: 0 auto 16px; }
.highlight-value { font-size: 36px; font-weight: 700; color: #333; margin-bottom: 8px; }
.highlight-label { font-size: 14px; color: #666; }

@media (max-width: 1200px) {
  .certificates-grid { grid-template-columns: repeat(2, 1fr); }
  .highlights-grid { grid-template-columns: repeat(2, 1fr); }
}

@media (max-width: 768px) {
  .shop-header-content { flex-direction: column; align-items: center; }
  .shop-info { text-align: center; }
  .shop-meta { justify-content: center; }
  .shop-actions { justify-content: center; flex-wrap: wrap; }
  .certificates-grid, .contact-grid, .gallery-grid, .highlights-grid { grid-template-columns: 1fr; }
  .rating-summary { flex-direction: column; gap: 24px; }
}
</style>