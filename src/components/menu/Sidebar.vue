<template>
    <aside class="fixed top-0 left-0 z-40 w-64 h-screen transition-transform -translate-x-full sm:translate-x-0">
      <div class="h-full px-3 py-4 overflow-y-auto bg-dark-green">
        <!-- Logo -->
        <div class="flex items-center ps-2.5 mb-5">
          <div class="w-6 h-6 bg-orange rounded flex items-center justify-center me-3">
            <svg class="w-4 h-4 text-white" fill="currentColor" viewBox="0 0 20 20">
              <path d="M3 4a1 1 0 011-1h12a1 1 0 011 1v2a1 1 0 01-1 1H4a1 1 0 01-1-1V4zM3 10a1 1 0 011-1h6a1 1 0 011 1v6a1 1 0 01-1 1H4a1 1 0 01-1-1v-6zM14 9a1 1 0 00-1 1v6a1 1 0 001 1h2a1 1 0 001-1v-6a1 1 0 00-1-1h-2z"/>
            </svg>
          </div>
          <span class="self-center text-xl font-semibold whitespace-nowrap text-white">Bazaar</span>
        </div>
  
        <!-- Navigation -->
        <ul class="space-y-2 font-medium">
          <li>
            <a href="#" class="flex items-center p-2 text-white rounded-lg bg-orange group">
              <svg class="w-5 h-5 text-white transition duration-75 group-hover:text-white" fill="currentColor" viewBox="0 0 20 20">
                <path d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z"/>
              </svg>
              <span class="ms-3">Dashboard Produits</span>
            </a>
          </li>
          <li>
            <a href="#" class="flex items-center p-2 text-white rounded-lg hover:bg-gray-700 group">
              <svg class="w-5 h-5 text-white transition duration-75 group-hover:text-white" fill="currentColor" viewBox="0 0 20 20">
                <path d="M2.003 5.884L10 9.882l7.997-3.998A2 2 0 0016 4H4a2 2 0 00-1.997 1.884z"/>
                <path d="M18 8.118l-8 4-8-4V14a2 2 0 002 2h12a2 2 0 002-2V8.118z"/>
              </svg>
              <span class="ms-3">Messages</span>
            </a>
          </li>
          <li>
            <p href="#" class="flex items-center p-2 text-darkslategray rounded-lg hover:bg-gray-700 group">
              <svg class="w-5 h-5 text-darkslategray transition duration-75 group-hover:text-white" fill="currentColor" viewBox="0 0 20 20">
                <path d="M9 6a3 3 0 11-6 0 3 3 0 016 0zM17 6a3 3 0 11-6 0 3 3 0 016 0zM12.93 17c.046-.327.07-.66.07-1a6.97 6.97 0 00-1.5-4.33A5 5 0 0119 16v1h-6.07zM6 11a5 5 0 015 5v1H1v-1a5 5 0 015-5z"/>
              </svg>
              <span class="ms-3">Clients</span>
            </p>
          </li>
          <li>
            <p href="#" class="flex items-center p-2 text-darkslategray rounded-lg hover:bg-gray-700 group">
              <svg class="w-5 h-5 text-darkslategray transition duration-75 group-hover:text-white" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 2L3 7v11a2 2 0 002 2h10a2 2 0 002-2V7l-7-5zM6 9a1 1 0 112 0 1 1 0 01-2 0zm6 0a1 1 0 112 0 1 1 0 01-2 0z" clip-rule="evenodd"/>
              </svg>
              <span class="ms-3">Livraisons</span>
            </p>
          </li>
         
          <li>
            <p href="#" class="flex items-center p-2 text-darkslategray rounded-lg hover:bg-gray-700 group">
              <svg class="w-5 h-5 text-darkslategray transition duration-75 group-hover:text-white" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M5 5a3 3 0 015-2.236A3 3 0 0114.83 6H16a2 2 0 110 4h-5V9a1 1 0 10-2 0v1H4a2 2 0 110-4h1.17C5.06 5.687 5 5.35 5 5zm4 1V5a1 1 0 10-1 1h1zm3 0a1 1 0 10-1-1v1h1z" clip-rule="evenodd"/>
              </svg>
              <span class="ms-3">Discounts</span>
            </p>
          </li>
          <li>
            <p href="#" class="flex items-center p-2 text-darkslategray rounded-lg hover:bg-gray-700 group">
              <svg class="w-5 h-5 text-darkslategray transition duration-75 group-hover:text-white" fill="currentColor" viewBox="0 0 20 20">
                <path d="M2 11a1 1 0 011-1h2a1 1 0 011 1v5a1 1 0 01-1 1H3a1 1 0 01-1-1v-5zM8 7a1 1 0 011-1h2a1 1 0 011 1v9a1 1 0 01-1 1H9a1 1 0 01-1-1V7zM14 4a1 1 0 011-1h2a1 1 0 011 1v12a1 1 0 01-1 1h-2a1 1 0 01-1-1V4z"/>
              </svg>
              <span class="ms-3">Historique Ventes</span>
            </p>
          </li>
          <li>
            <p href="#" class="flex items-center p-2 text-darkslategray rounded-lg hover:bg-gray-700 group">
              <svg class="w-5 h-5 text-darkslategray transition duration-75 group-hover:text-white" fill="currentColor" viewBox="0 0 20 20">
                <path d="M4 4a2 2 0 00-2 2v1h16V6a2 2 0 00-2-2H4zM18 9H2v5a2 2 0 002 2h12a2 2 0 002-2V9zM4 13a1 1 0 011-1h1a1 1 0 110 2H5a1 1 0 01-1-1zm5-1a1 1 0 100 2h1a1 1 0 100-2H9z"/>
              </svg>
              <span class="ms-3">Comptabilité</span>
            </p>
          </li>
          <li>
            <p href="#" class="flex items-center p-2 text-darkslategray rounded-lg hover:bg-gray-700 group">
                <svg class="w-5 h-5 text-darkslategray transition duration-75 group-hover:text-white" fill="currentColor" viewBox="0 0 20 20">
                  <path fill-rule="evenodd" d="M5 3a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2V5a2 2 0 00-2-2H5zm0 2h10v7h-2l-1-2H8l-1 2H5V5z" clip-rule="evenodd"/>
                </svg>
                <span class="ms-3">Stock</span>
            </p>
            </li>
        </ul>

  
        <!-- Settings at bottom -->
        <div class="absolute bottom-4 left-3 right-3">
          <p href="#" class="flex items-center p-2 text-darkslategray rounded-lg hover:bg-gray-700 group">
            <svg class="w-5 h-5 text-darkslategray transition duration-75 group-hover:text-white" fill="currentColor" viewBox="0 0 20 20">
              <path fill-rule="evenodd" d="M11.49 3.17c-.38-1.56-2.6-1.56-2.98 0a1.532 1.532 0 01-2.286.948c-1.372-.836-2.942.734-2.106 2.106.54.886.061 2.042-.947 2.287-1.561.379-1.561 2.6 0 2.978a1.532 1.532 0 01.947 2.287c-.836 1.372.734 2.942 2.106 2.106a1.532 1.532 0 012.287.947c.379 1.561 2.6 1.561 2.978 0a1.533 1.533 0 012.287-.947c1.372.836 2.942-.734 2.106-2.106a1.533 1.533 0 01.947-2.287c1.561-.379 1.561-2.6 0-2.978a1.532 1.532 0 01-.947-2.287c.836-1.372-.734-2.942-2.106-2.106a1.532 1.532 0 01-2.287-.947zM10 13a3 3 0 100-6 3 3 0 000 6z" clip-rule="evenodd"/>
            </svg>
            <span class="ms-3">Settings</span>
        </p>
        </div>
      </div>
    </aside>
  </template>
  
  <style scoped>
  .bg-dark-green {
    background-color: black;
  }
  
  .bg-orange {
    background-color: #F65A11;
  }
  </style>
  