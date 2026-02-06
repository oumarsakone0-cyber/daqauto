<template>
    <header class="header">
      <div class="header-left">
        <h1 class="page-title">Dashboard Marketplace</h1>
        <p class="page-subtitle">{{ currentDate }}</p>
      </div>
      
      <div class="header-right">
        <div class="search-container">
          <SearchIcon class="search-icon" />
          <input 
            type="text" 
            placeholder="Rechercher marchés, boutiques..." 
            class="search-input"
            v-model="searchQuery"
          >
        </div>
        
        <div class="notification-btn">
          <BellIcon />
          <span class="notification-dot">{{ notifications }}</span>
        </div>
        
        <div class="user-profile">
          <div class="profile-avatar">SA</div>
          <div class="profile-info">
            <p class="profile-name">Super Admin</p>
            <p class="profile-role">Marketplace Admin</p>
          </div>
          <ChevronDownIcon class="chevron-down" />
        </div>
      </div>
    </header>
  </template>
  
  <script setup>
  import { ref, computed } from 'vue'
  
  // Reactive data
  const searchQuery = ref('')
  const notifications = ref(18)
  
  // Current date
  const currentDate = computed(() => {
    return new Date().toLocaleDateString('fr-FR', { 
      weekday: 'long', 
      year: 'numeric', 
      month: 'long', 
      day: 'numeric' 
    })
  })
  </script>
  
  <script>
  const SearchIcon = {
    template: `<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"></circle><path d="m21 21-4.3-4.3"></path></svg>`
  }
  
  const BellIcon = {
    template: `<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M6 8a6 6 0 0 1 12 0c0 7 3 9 3 9H3s3-2 3-9"></path><path d="M10.3 21a1.94 1.94 0 0 0 3.4 0"></path></svg>`
  }
  
  const ChevronDownIcon = {
    template: `<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="m6 9 6 6 6-6"></path></svg>`
  }
  
  export default {
    components: {
      SearchIcon,
      BellIcon,
      ChevronDownIcon
    }
  }
  </script>
  
  <style scoped>
  .header {
    width: 100%;
    padding: 20px 32px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    background: rgba(255, 255, 255, 0.9);
    backdrop-filter: blur(20px);
    border-bottom: 1px solid rgba(0, 0, 0, 0.1);
    position: sticky;
    top: 0;
    z-index: 10;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
  }
  
  .page-title {
    font-size: 32px;
    font-weight: 700;
    margin: 0;
    background: linear-gradient(135deg, #667eea, #764ba2);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
  }
  
  .page-subtitle {
    font-size: 14px;
    color: #64748b;
    margin: 0;
  }
  
  .header-right {
    display: flex;
    align-items: center;
    gap: 20px;
  }
  
  .search-container {
    position: relative;
    display: none;
  }
  
  @media (min-width: 768px) {
    .search-container {
      display: block;
    }
  }
  
  .search-icon {
    position: absolute;
    left: 16px;
    top: 50%;
    transform: translateY(-50%);
    color: #64748b;
  }
  
  .search-input {
    padding: 12px 16px 12px 48px;
    border-radius: 12px;
    background: rgba(255, 255, 255, 0.8);
    border: 1px solid rgba(0, 0, 0, 0.1);
    width: 320px;
    transition: all 0.3s;
    font-size: 14px;
  }
  
  .search-input:focus {
    outline: none;
    box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.2);
    border-color: #667eea;
    background: white;
  }
  
  .notification-btn {
    position: relative;
    padding: 12px;
    border-radius: 12px;
    background: rgba(255, 255, 255, 0.8);
    transition: all 0.3s;
    cursor: pointer;
    border: 1px solid rgba(0, 0, 0, 0.1);
  }
  
  .notification-btn:hover {
    background: white;
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
  }
  
  .notification-dot {
    position: absolute;
    top: 4px;
    right: 4px;
    background: #ef4444;
    color: white;
    font-size: 10px;
    font-weight: 600;
    padding: 2px 6px;
    border-radius: 10px;
    min-width: 18px;
    text-align: center;
  }
  
  .user-profile {
    display: flex;
    align-items: center;
    gap: 12px;
    cursor: pointer;
    padding: 8px 16px;
    border-radius: 12px;
    background: rgba(255, 255, 255, 0.8);
    border: 1px solid rgba(0, 0, 0, 0.1);
    transition: all 0.3s;
  }
  
  .user-profile:hover {
    background: white;
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
  }
  
  .profile-avatar {
    width: 36px;
    height: 36px;
    border-radius: 50%;
    background: linear-gradient(135deg, #667eea, #764ba2);
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-weight: 600;
    font-size: 12px;
  }
  
  .profile-info {
    display: none;
  }
  
  @media (min-width: 768px) {
    .profile-info {
      display: block;
    }
  }
  
  .profile-name {
    font-size: 14px;
    font-weight: 600;
    margin: 0;
    color: #1e293b;
  }
  
  .profile-role {
    font-size: 12px;
    color: #64748b;
    margin: 0;
  }
  
  .chevron-down {
    color: #64748b;
  }
  </style>