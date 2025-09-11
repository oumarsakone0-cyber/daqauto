<template>
    <aside class="sidebar">
      <div class="sidebar-content">
        <div class="sidebar-header">
          <div class="logo">
            <StoreIcon class="logo-icon" />
            <h1 class="sidebar-title">MarketAdmin</h1>
          </div>
        </div>
        
        <nav class="sidebar-nav">
          <a 
            v-for="item in navigationItems" 
            :key="item.name"
            href="#"
            :class="['nav-item', { 'nav-item-active': item.active }]"
            @click="setActiveItem(item.name)"
          >
            <component :is="item.icon" class="nav-icon" />
            <span>{{ item.label }}</span>
            <span v-if="item.badge" class="nav-badge">{{ item.badge }}</span>
          </a>
        </nav>
        
        <div class="sidebar-footer">
          <div class="user-info">
            <div class="user-avatar">
              <UserIcon />
            </div>
            <div class="user-details">
              <p class="user-name">Super Admin</p>
              <p class="user-role">Marketplace</p>
            </div>
          </div>
        </div>
      </div>
    </aside>
  </template>
  
  <script setup>
  import { ref } from 'vue'
  
  // Navigation items
  const navigationItems = ref([
    { name: 'Dashboard', label: 'Dashboard', icon: 'LayoutDashboardIcon', active: true },
    { name: 'Markets', label: 'Marchés', icon: 'BuildingIcon', badge: '12', active: false },
    { name: 'Shops', label: 'Boutiques', icon: 'StoreIcon', badge: '156', active: false },
    { name: 'Orders', label: 'Commandes', icon: 'ShoppingCartIcon', badge: '89', active: false },
    { name: 'Contacts', label: 'Contacts Directs', icon: 'MessageSquareIcon', badge: '247', active: false },
    { name: 'Vendors', label: 'Vendeurs', icon: 'UsersIcon', active: false },
    { name: 'Analytics', label: 'Analytics', icon: 'BarChartIcon', active: false },
    { name: 'Complaints', label: 'Réclamations', icon: 'AlertTriangleIcon', badge: '23', active: false },
    { name: 'Finance', label: 'Finances', icon: 'CreditCardIcon', active: false },
    { name: 'Settings', label: 'Paramètres', icon: 'SettingsIcon', active: false }
  ])
  
  // Methods
  const setActiveItem = (itemName) => {
    navigationItems.value.forEach(item => {
      item.active = item.name === itemName
    })
  }
  </script>
  
  <script>
  // Icon Components
  const LayoutDashboardIcon = {
    template: `<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect width="7" height="9" x="3" y="3" rx="1"></rect><rect width="7" height="5" x="14" y="3" rx="1"></rect><rect width="7" height="9" x="14" y="12" rx="1"></rect><rect width="7" height="5" x="3" y="16" rx="1"></rect></svg>`
  }
  
  const BuildingIcon = {
    template: `<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect width="16" height="20" x="4" y="2" rx="2" ry="2"></rect><path d="M9 22v-4h6v4"></path><path d="M8 6h.01"></path><path d="M16 6h.01"></path><path d="M12 6h.01"></path><path d="M12 10h.01"></path><path d="M12 14h.01"></path><path d="M16 10h.01"></path><path d="M16 14h.01"></path><path d="M8 10h.01"></path><path d="M8 14h.01"></path></svg>`
  }
  
  const StoreIcon = {
    template: `<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="m2 7 4.41-4.41A2 2 0 0 1 7.83 2h8.34a2 2 0 0 1 1.42.59L22 7"></path><path d="M4 12v8a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2v-8"></path><path d="M15 22v-4a2 2 0 0 0-2-2h-2a2 2 0 0 0-2 2v4"></path><path d="M2 7h20"></path></svg>`
  }
  
  const ShoppingCartIcon = {
    template: `<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="8" cy="21" r="1"></circle><circle cx="19" cy="21" r="1"></circle><path d="M2.05 2.05h2l2.66 12.42a2 2 0 0 0 2 1.58h9.78a2 2 0 0 0 1.95-1.57l1.65-7.43H5.12"></path></svg>`
  }
  
  const MessageSquareIcon = {
    template: `<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"></path></svg>`
  }
  
  const UsersIcon = {
    template: `<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle><path d="M22 21v-2a4 4 0 0 0-3-3.87"></path><path d="M16 3.13a4 4 0 0 1 0 7.75"></path></svg>`
  }
  
  const BarChartIcon = {
    template: `<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" x2="12" y1="20" y2="10"></line><line x1="18" x2="18" y1="20" y2="4"></line><line x1="6" x2="6" y1="20" y2="16"></line></svg>`
  }
  
  const AlertTriangleIcon = {
    template: `<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="m21.73 18-8-14a2 2 0 0 0-3.48 0l-8 14A2 2 0 0 0 4 21h16a2 2 0 0 0 1.73-3"></path><path d="M12 9v4"></path><path d="M12 17h.01"></path></svg>`
  }
  
  const CreditCardIcon = {
    template: `<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect width="20" height="14" x="2" y="5" rx="2"></rect><line x1="2" x2="22" y1="10" y2="10"></line></svg>`
  }
  
  const SettingsIcon = {
    template: `<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12.22 2h-.44a2 2 0 0 0-2 2v.18a2 2 0 0 1-1 1.73l-.43.25a2 2 0 0 1-2 0l-.15-.08a2 2 0 0 0-2.73.73l-.22.38a2 2 0 0 0 .73 2.73l.15.1a2 2 0 0 1 1 1.72v.51a2 2 0 0 1-1 1.74l-.15.09a2 2 0 0 0-.73 2.73l.22.38a2 2 0 0 0 2.73.73l.15-.08a2 2 0 0 1 2 0l.43.25a2 2 0 0 1 1 1.73V20a2 2 0 0 0 2 2h.44a2 2 0 0 0 2-2v-.18a2 2 0 0 1 1-1.73l.43-.25a2 2 0 0 1 2 0l.15.08a2 2 0 0 0 2.73-.73l.22-.39a2 2 0 0 0-.73-2.73l-.15-.08a2 2 0 0 1-1-1.74v-.5a2 2 0 0 1 1-1.74l.15-.09a2 2 0 0 0 .73-2.73l-.22-.38a2 2 0 0 0-2.73-.73l-.15.08a2 2 0 0 1-2 0l-.43-.25a2 2 0 0 1-1-1.73V4a2 2 0 0 0-2-2z"></path><circle cx="12" cy="12" r="3"></circle></svg>`
  }
  
  const UserIcon = {
    template: `<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path><circle cx="12" cy="7" r="4"></circle></svg>`
  }
  
  export default {
    components: {
      LayoutDashboardIcon,
      BuildingIcon,
      StoreIcon,
      ShoppingCartIcon,
      MessageSquareIcon,
      UsersIcon,
      BarChartIcon,
      AlertTriangleIcon,
      CreditCardIcon,
      SettingsIcon,
      UserIcon
    }
  }
  </script>
  
  <style scoped>
  .sidebar {
    position: fixed;
    top: 0;
    left: 0;
    z-index: 40;
    height: 100vh;
    width: 280px;
    background: rgba(255, 255, 255, 0.95);
    backdrop-filter: blur(20px);
    border-right: 1px solid rgba(255, 255, 255, 0.2);
    box-shadow: 4px 0 25px rgba(0, 0, 0, 0.1);
    overflow-y: auto;
  }
  
  .sidebar-content {
    display: flex;
    flex-direction: column;
    height: 100%;
  }
  
  .sidebar-header {
    padding: 24px 20px;
    border-bottom: 1px solid rgba(0, 0, 0, 0.1);
  }
  
  .logo {
    display: flex;
    align-items: center;
    gap: 12px;
  }
  
  .logo-icon {
    color: #667eea;
  }
  
  .sidebar-title {
    font-size: 24px;
    font-weight: 700;
    color: #1e293b;
    margin: 0;
  }
  
  .sidebar-nav {
    flex: 1;
    padding: 20px 16px;
    display: flex;
    flex-direction: column;
    gap: 4px;
  }
  
  .nav-item {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 12px 16px;
    border-radius: 12px;
    transition: all 0.3s;
    color: #64748b;
    text-decoration: none;
    font-weight: 500;
    position: relative;
  }
  
  .nav-item:hover {
    background: rgba(102, 126, 234, 0.1);
    color: #667eea;
    transform: translateX(4px);
  }
  
  .nav-item-active {
    background: linear-gradient(135deg, #667eea, #764ba2);
    color: white;
    box-shadow: 0 4px 15px rgba(102, 126, 234, 0.3);
  }
  
  .nav-icon {
    width: 20px;
    height: 20px;
  }
  
  .nav-badge {
    margin-left: auto;
    background: #ef4444;
    color: white;
    font-size: 11px;
    font-weight: 600;
    padding: 2px 6px;
    border-radius: 10px;
    min-width: 18px;
    text-align: center;
  }
  
  .sidebar-footer {
    padding: 20px;
    border-top: 1px solid rgba(0, 0, 0, 0.1);
  }
  
  .user-info {
    display: flex;
    align-items: center;
    gap: 12px;
  }
  
  .user-avatar {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    background: linear-gradient(135deg, #667eea, #764ba2);
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
  }
  
  .user-name {
    font-size: 14px;
    font-weight: 600;
    color: #1e293b;
    margin: 0;
  }
  
  .user-role {
    font-size: 12px;
    color: #64748b;
    margin: 0;
  }
  </style>