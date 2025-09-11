<template>
  <div class="flex h-screen bg-cream">
    <!-- Sidebar -->
    <div class="w-60 bg-dark-green text-white flex flex-col">
      <!-- Logo -->
      <div class="p-4 border-b border-gray-700">
        <div class="flex items-center space-x-2">
          <div class="w-6 h-6 bg-orange rounded flex items-center justify-center">
            <svg class="w-4 h-4 text-white" fill="currentColor" viewBox="0 0 20 20">
              <path d="M3 4a1 1 0 011-1h12a1 1 0 011 1v2a1 1 0 01-1 1H4a1 1 0 01-1-1V4zM3 10a1 1 0 011-1h6a1 1 0 011 1v6a1 1 0 01-1 1H4a1 1 0 01-1-1v-6zM14 9a1 1 0 00-1 1v6a1 1 0 001 1h2a1 1 0 001-1v-6a1 1 0 00-1-1h-2z"/>
            </svg>
          </div>
          <span class="text-lg font-semibold">Bazaar</span>
        </div>
      </div>

      <!-- Navigation Menu -->
      <nav class="flex-1 p-4">
        <ul class="space-y-2">
          <li>
            <a href="#" class="flex items-center space-x-3 p-2 rounded hover:bg-gray-700 transition-colors">
              <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                <path d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z"/>
              </svg>
              <span>Home</span>
            </a>
          </li>
          <li>
            <a href="#" class="flex items-center space-x-3 p-2 rounded bg-orange text-white">
              <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                <path d="M9 2a1 1 0 000 2h2a1 1 0 100-2H9z"/>
                <path fill-rule="evenodd" d="M4 5a2 2 0 012-2 3 3 0 003 3h2a3 3 0 003-3 2 2 0 012 2v6a2 2 0 01-2 2H6a2 2 0 01-2-2V5zm3 2V6a1 1 0 112 0v1a1 1 0 11-2 0zm3 0V6a1 1 0 112 0v1a1 1 0 11-2 0z" clip-rule="evenodd"/>
              </svg>
              <span>Orders</span>
            </a>
          </li>
          <li>
            <a href="#" class="flex items-center space-x-3 p-2 rounded hover:bg-gray-700 transition-colors">
              <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 2L3 7v11a2 2 0 002 2h10a2 2 0 002-2V7l-7-5zM6 9a1 1 0 112 0 1 1 0 01-2 0zm6 0a1 1 0 112 0 1 1 0 01-2 0z" clip-rule="evenodd"/>
              </svg>
              <span>Products</span>
            </a>
          </li>
          <li>
            <a href="#" class="flex items-center space-x-3 p-2 rounded hover:bg-gray-700 transition-colors">
              <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                <path d="M9 6a3 3 0 11-6 0 3 3 0 016 0zM17 6a3 3 0 11-6 0 3 3 0 016 0zM12.93 17c.046-.327.07-.66.07-1a6.97 6.97 0 00-1.5-4.33A5 5 0 0119 16v1h-6.07zM6 11a5 5 0 015 5v1H1v-1a5 5 0 015-5z"/>
              </svg>
              <span>Customers</span>
            </a>
          </li>
          <li>
            <a href="#" class="flex items-center space-x-3 p-2 rounded hover:bg-gray-700 transition-colors">
              <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                <path d="M2 11a1 1 0 011-1h2a1 1 0 011 1v5a1 1 0 01-1 1H3a1 1 0 01-1-1v-5zM8 7a1 1 0 011-1h2a1 1 0 011 1v9a1 1 0 01-1 1H9a1 1 0 01-1-1V7zM14 4a1 1 0 011-1h2a1 1 0 011 1v12a1 1 0 01-1 1h-2a1 1 0 01-1-1V4z"/>
              </svg>
              <span>Analytics</span>
            </a>
          </li>
          <li>
            <a href="#" class="flex items-center space-x-3 p-2 rounded hover:bg-gray-700 transition-colors">
              <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                <path d="M2.003 5.884L10 9.882l7.997-3.998A2 2 0 0016 4H4a2 2 0 00-1.997 1.884z"/>
                <path d="M18 8.118l-8 4-8-4V14a2 2 0 002 2h12a2 2 0 002-2V8.118z"/>
              </svg>
              <span>Marketing</span>
            </a>
          </li>
          <li>
            <a href="#" class="flex items-center space-x-3 p-2 rounded hover:bg-gray-700 transition-colors">
              <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M11.3 1.046A1 1 0 0112 2v5h4a1 1 0 01.82 1.573l-7 10A1 1 0 018 18v-5H4a1 1 0 01-.82-1.573l7-10a1 1 0 011.12-.38z" clip-rule="evenodd"/>
              </svg>
              <span>Integrations</span>
            </a>
          </li>
          <li>
            <a href="#" class="flex items-center space-x-3 p-2 rounded hover:bg-gray-700 transition-colors">
              <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M5 5a3 3 0 015-2.236A3 3 0 0114.83 6H16a2 2 0 110 4h-5V9a1 1 0 10-2 0v1H4a2 2 0 110-4h1.17C5.06 5.687 5 5.35 5 5zm4 1V5a1 1 0 10-1 1h1zm3 0a1 1 0 10-1-1v1h1z" clip-rule="evenodd"/>
              </svg>
              <span>Discounts</span>
            </a>
          </li>
        </ul>
      </nav>

      <!-- Settings -->
      <div class="p-4 border-t border-gray-700">
        <a href="#" class="flex items-center space-x-3 p-2 rounded hover:bg-gray-700 transition-colors">
          <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
            <path fill-rule="evenodd" d="M11.49 3.17c-.38-1.56-2.6-1.56-2.98 0a1.532 1.532 0 01-2.286.948c-1.372-.836-2.942.734-2.106 2.106.54.886.061 2.042-.947 2.287-1.561.379-1.561 2.6 0 2.978a1.532 1.532 0 01.947 2.287c-.836 1.372.734 2.942 2.106 2.106a1.532 1.532 0 012.287.947c.379 1.561 2.6 1.561 2.978 0a1.533 1.533 0 012.287-.947c1.372.836 2.942-.734 2.106-2.106a1.533 1.533 0 01.947-2.287c1.561-.379 1.561-2.6 0-2.978a1.532 1.532 0 01-.947-2.287c.836-1.372-.734-2.942-2.106-2.106a1.532 1.532 0 01-2.287-.947zM10 13a3 3 0 100-6 3 3 0 000 6z" clip-rule="evenodd"/>
          </svg>
          <span>Settings</span>
        </a>
      </div>
    </div>

    <!-- Main Content -->
    <div class="flex-1 flex flex-col">
      <!-- Header -->
      <header class="bg-cream border-b border-gray-200 px-6 py-4">
        <div class="flex items-center justify-between">
          <!-- Search Bar -->
          <div class="flex-1 max-w-md mx-auto">
            <div class="relative">
              <svg class="absolute left-3 top-1/2 transform -translate-y-1/2 w-4 h-4 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z" clip-rule="evenodd"/>
              </svg>
              <input 
                type="text" 
                placeholder="Search..." 
                class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-orange focus:border-transparent"
              >
            </div>
          </div>

          <!-- Right Side Icons -->
          <div class="flex items-center space-x-4">
            <button class="p-2 text-gray-600 hover:text-gray-800">
              <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                <path d="M10 2a6 6 0 00-6 6v3.586l-.707.707A1 1 0 004 14h12a1 1 0 00.707-1.707L16 11.586V8a6 6 0 00-6-6zM10 18a3 3 0 01-3-3h6a3 3 0 01-3 3z"/>
              </svg>
            </button>
            <div class="w-8 h-8 bg-gray-300 rounded-full"></div>
          </div>
        </div>
      </header>

      <!-- Page Content -->
      <main class="flex-1 p-6 overflow-auto">
        <!-- Hero Section -->
        <div class="bg-dark-green rounded-lg p-6 mb-6 text-white">
          <div class="flex items-center justify-between">
            <div>
              <h1 class="text-2xl font-bold mb-2">Get ffff very best apps for your store</h1>
              <p class="text-gray-300 mb-4">Upgrade to the new checkout automation.</p>
              <button class="bg-orange text-white px-4 py-2 rounded-lg hover:bg-orange-600 transition-colors flex items-center space-x-2">
                <span>Find App</span>
                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                  <path fill-rule="evenodd" d="M10.293 3.293a1 1 0 011.414 0l6 6a1 1 0 010 1.414l-6 6a1 1 0 01-1.414-1.414L14.586 11H3a1 1 0 110-2h11.586l-4.293-4.293a1 1 0 010-1.414z" clip-rule="evenodd"/>
                </svg>
              </button>
            </div>
            <div class="flex space-x-2">
              <div class="w-12 h-12 bg-white rounded-full flex items-center justify-center">
                <span class="text-2xl">🛒</span>
              </div>
              <div class="w-12 h-12 bg-white rounded-full flex items-center justify-center">
                <span class="text-2xl">🛍️</span>
              </div>
              <div class="w-12 h-12 bg-white rounded-full flex items-center justify-center">
                <span class="text-2xl">⭐</span>
              </div>
            </div>
          </div>
        </div>

        <!-- Orders Section -->
        <div class="bg-white rounded-lg shadow">
          <!-- Orders Header -->
          <div class="flex items-center justify-between p-6 border-b border-gray-200">
            <h2 class="text-xl font-semibold text-gray-800">Orders</h2>
            <div class="flex items-center space-x-3">
              <button class="px-4 py-2 border border-gray-300 rounded-lg text-gray-600 hover:bg-gray-50">Export</button>
              <button class="px-4 py-2 border border-gray-300 rounded-lg text-gray-600 hover:bg-gray-50">More actions</button>
              <button class="px-4 py-2 bg-orange text-white rounded-lg hover:bg-orange-600">Create Order</button>
            </div>
          </div>

          <!-- Stats Cards -->
          <div class="p-6 border-b border-gray-200">
            <div class="grid grid-cols-5 gap-6">
              <div class="text-center">
                <div class="text-sm text-gray-600 mb-1">📅 Today</div>
              </div>
              <div class="text-center">
                <div class="text-sm text-gray-600 mb-1">Total orders</div>
                <div class="text-2xl font-bold">48</div>
              </div>
              <div class="text-center">
                <div class="text-sm text-gray-600 mb-1">Ordered items over time</div>
                <div class="text-2xl font-bold">493</div>
              </div>
              <div class="text-center">
                <div class="text-sm text-gray-600 mb-1">Returns</div>
                <div class="text-2xl font-bold">6</div>
              </div>
              <div class="text-center">
                <div class="text-sm text-gray-600 mb-1">Fulfilled orders over time</div>
                <div class="text-2xl font-bold">359</div>
              </div>
            </div>
          </div>

          <!-- Filter Tabs -->
          <div class="px-6 py-4 border-b border-gray-200">
            <div class="flex space-x-6">
              <button class="text-orange border-b-2 border-orange pb-2 font-medium">All</button>
              <button class="text-gray-600 hover:text-gray-800 pb-2">Unpaid</button>
              <button class="text-gray-600 hover:text-gray-800 pb-2">Need to ship</button>
              <button class="text-gray-600 hover:text-gray-800 pb-2">Sent</button>
              <button class="text-gray-600 hover:text-gray-800 pb-2">Completed</button>
              <button class="text-gray-600 hover:text-gray-800 pb-2">Cancellation</button>
              <button class="text-gray-600 hover:text-gray-800 pb-2">Returns</button>
            </div>
          </div>

          <!-- Search and Filters -->
          <div class="px-6 py-4 border-b border-gray-200">
            <div class="flex items-center justify-between">
              <div class="relative">
                <svg class="absolute left-3 top-1/2 transform -translate-y-1/2 w-4 h-4 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                  <path fill-rule="evenodd" d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z" clip-rule="evenodd"/>
                </svg>
                <input 
                  type="text" 
                  placeholder="Search order..." 
                  class="pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-orange focus:border-transparent"
                >
              </div>
              <div class="flex items-center space-x-2">
                <button class="p-2 border border-gray-300 rounded hover:bg-gray-50">
                  <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M5 12a1 1 0 102 0V6.414l1.293 1.293a1 1 0 001.414-1.414l-3-3a1 1 0 00-1.414 0l-3 3a1 1 0 001.414 1.414L5 6.414V12zM15 8a1 1 0 10-2 0v5.586l-1.293-1.293a1 1 0 00-1.414 1.414l3 3a1 1 0 001.414 0l3-3a1 1 0 00-1.414-1.414L15 13.586V8z"/>
                  </svg>
                </button>
                <button class="px-3 py-2 border border-gray-300 rounded hover:bg-gray-50 flex items-center space-x-2">
                  <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M3 3a1 1 0 011-1h12a1 1 0 011 1v3a1 1 0 01-.293.707L12 11.414V15a1 1 0 01-.293.707l-2 2A1 1 0 018 17v-5.586L3.293 6.707A1 1 0 013 6V3z" clip-rule="evenodd"/>
                  </svg>
                  <span>Filter</span>
                </button>
              </div>
            </div>
          </div>

          <!-- Orders Table -->
          <div class="overflow-x-auto">
            <table class="w-full">
              <thead class="bg-gray-50">
                <tr>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                    <input type="checkbox" class="rounded">
                  </th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Order</th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Customer</th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total</th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Payment Status</th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Items</th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Delivery Method</th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"></th>
                </tr>
              </thead>
              <tbody class="bg-white divide-y divide-gray-200">
                <tr v-for="order in orders" :key="order.id" class="hover:bg-gray-50">
                  <td class="px-6 py-4 whitespace-nowrap">
                    <input type="checkbox" class="rounded">
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap">
                    <div class="flex items-center space-x-3">
                      <div class="w-10 h-10 bg-gray-200 rounded flex items-center justify-center">
                        <span class="text-xs">📱</span>
                      </div>
                      <div>
                        <div class="text-sm font-medium text-gray-900">{{ order.product }}</div>
                        <div class="text-sm text-gray-500">{{ order.orderNumber }}</div>
                      </div>
                    </div>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap">
                    <div class="flex items-center space-x-2">
                      <div class="w-6 h-6 bg-gray-300 rounded-full flex items-center justify-center text-xs">
                        {{ order.customer.charAt(0) }}
                      </div>
                      <span class="text-sm text-gray-900">{{ order.customer }}</span>
                    </div>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ order.date }}</td>
                  <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ order.total }}</td>
                  <td class="px-6 py-4 whitespace-nowrap">
                    <span :class="getStatusClass(order.status)" class="px-2 py-1 text-xs rounded-full">
                      {{ order.status }}
                    </span>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ order.items }}</td>
                  <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ order.delivery }}</td>
                  <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                    <button class="text-gray-400 hover:text-gray-600">
                      <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M10 6a2 2 0 110-4 2 2 0 010 4zM10 12a2 2 0 110-4 2 2 0 010 4zM10 18a2 2 0 110-4 2 2 0 010 4z"/>
                      </svg>
                    </button>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>

          <!-- Pagination -->
          <div class="px-6 py-4 border-t border-gray-200">
            <div class="flex items-center justify-between">
              <div class="flex items-center space-x-2">
                <span class="text-sm text-gray-600">Items per page</span>
                <select class="border border-gray-300 rounded px-2 py-1 text-sm">
                  <option>10</option>
                  <option>25</option>
                  <option>50</option>
                </select>
              </div>
              <div class="flex items-center space-x-2">
                <button class="px-3 py-1 border border-gray-300 rounded text-sm hover:bg-gray-50">Previous</button>
                <button class="px-3 py-1 bg-dark-green text-white rounded text-sm">1</button>
                <button class="px-3 py-1 border border-gray-300 rounded text-sm hover:bg-gray-50">2</button>
                <button class="px-3 py-1 border border-gray-300 rounded text-sm hover:bg-gray-50">3</button>
                <button class="px-3 py-1 border border-gray-300 rounded text-sm hover:bg-gray-50">4</button>
                <span class="px-2 text-sm text-gray-500">...</span>
                <button class="px-3 py-1 border border-gray-300 rounded text-sm hover:bg-gray-50">32</button>
                <button class="px-3 py-1 border border-gray-300 rounded text-sm hover:bg-gray-50">Next</button>
              </div>
            </div>
          </div>
        </div>
      </main>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue'

const orders = ref([
  {
    id: 1,
    product: 'MacBook Air (M1, 2022...)',
    orderNumber: '#57869',
    customer: 'Darrell Steward',
    date: 'Apr 19, 08:01 AM',
    total: '$1,099.00',
    status: 'Pending',
    items: '1 items',
    delivery: 'Free Shipping'
  },
  {
    id: 2,
    product: 'MacBook Pro 13-inch...',
    orderNumber: '#29047',
    customer: 'Courtney Henry',
    date: 'Apr 19, 09:15 AM',
    total: '$2,198.00',
    status: 'Completed',
    items: '2 items',
    delivery: 'Free Shipping'
  },
  {
    id: 3,
    product: 'MacBook Air (Retina...',
    orderNumber: '#348562',
    customer: 'Arlene McCoy',
    date: 'Apr 19, 10:30 AM',
    total: '$1,499.00',
    status: 'Completed',
    items: '1 items',
    delivery: 'Free Shipping'
  },
  {
    id: 4,
    product: 'MacBook Pro 16-inch...',
    orderNumber: '#606593',
    customer: 'Brooklyn Simmons',
    date: 'Apr 19, 11:45 AM',
    total: '$3,099.00',
    status: 'Pending',
    items: '3 items',
    delivery: 'Free Shipping'
  },
  {
    id: 5,
    product: 'MacBook Air (M1, 2022...)',
    orderNumber: '#187345',
    customer: 'Devon Lane',
    date: 'Apr 19, 01:00 PM',
    total: '$2,198.00',
    status: 'Pending',
    items: '2 items',
    delivery: 'Free Shipping'
  },
  {
    id: 6,
    product: 'MacBook Pro 13-inch...',
    orderNumber: '#48076',
    customer: 'Bessie Cooper',
    date: 'Apr 19, 02:15 PM',
    total: '$1,499.00',
    status: 'Completed',
    items: '1 items',
    delivery: 'Free Shipping'
  },
  {
    id: 7,
    product: 'MacBook Air (Retina...',
    orderNumber: '#506336',
    customer: 'Cameron Williamson',
    date: 'Apr 19, 03:30 PM',
    total: '$2,199.00',
    status: 'Failed',
    items: '1 items',
    delivery: 'Free Shipping'
  },
  {
    id: 8,
    product: 'MacBook Pro 16-inch...',
    orderNumber: '#359774',
    customer: 'Floyd Miles',
    date: 'Apr 19, 04:45 PM',
    total: '$4,396.00',
    status: 'Failed',
    items: '4 items',
    delivery: 'Free Shipping'
  },
  {
    id: 9,
    product: 'MacBook Pro 16-inch...',
    orderNumber: '#748605',
    customer: 'Esther Howard',
    date: 'Apr 19, 06:00 PM',
    total: '$2,198.00',
    status: 'Completed',
    items: '2 items',
    delivery: 'Free Shipping'
  },
  {
    id: 10,
    product: 'MacBook Pro 13-inch...',
    orderNumber: '#248937',
    customer: 'Robert Fox',
    date: 'Apr 19, 07:15 PM',
    total: '$1,499.00',
    status: 'Pending',
    items: '1 items',
    delivery: 'Free Shipping'
  }
])

const getStatusClass = (status) => {
  switch (status) {
    case 'Completed':
      return 'bg-green-100 text-green-800'
    case 'Pending':
      return 'bg-yellow-100 text-yellow-800'
    case 'Failed':
      return 'bg-red-100 text-red-800'
    default:
      return 'bg-gray-100 text-gray-800'
  }
}
</script>

<style>
@import 'tailwindcss/base';
@import 'tailwindcss/components';
@import 'tailwindcss/utilities';

@layer base {
  :root {
    --color-dark-green: #22331D;
    --color-cream: #EFEDE7;
    --color-orange: #F65A11;
    --color-gray-green: #616D69;
  }
}

#app {
    max-width: none;
    width: 100%;
    margin: 0;
    padding: 0px;
    text-align: center;
}

@layer utilities {
  .bg-dark-green {
    background-color: var(--color-dark-green);
  }
  
  .bg-cream {
    background-color: var(--color-cream);
  }
  
  .bg-orange {
    background-color: var(--color-orange);
  }
  
  .bg-gray-green {
    background-color: var(--color-gray-green);
  }
  
  .text-dark-green {
    color: var(--color-dark-green);
  }
  
  .text-cream {
    color: var(--color-cream);
  }
  
  .text-orange {
    color: var(--color-orange);
  }
  
  .text-gray-green {
    color: var(--color-gray-green);
  }
  
  .border-orange {
    border-color: var(--color-orange);
  }
  
  .ring-orange {
    --tw-ring-color: var(--color-orange);
  }
  
  .hover\:bg-orange-600:hover {
    background-color: #e54a0a;
  }
}
</style>
