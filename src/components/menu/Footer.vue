<template>
  <!-- Footer principal -->
  <footer class="gray-color">
    <!-- Main Footer Content -->
    <div class="py-8 md:py-12">
      <div class="container mx-auto px-4">
        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-6 md:gap-8">
          <!-- Parts Collection -->
          <div class="col-span-2 md:col-span-1">
            <h4 class="text-base md:text-lg font-semibold mb-3 md:mb-4 ">Parts Collection</h4>
            <ul class="space-y-1 md:space-y-2 text-xs md:text-sm">
              <li><a href="#" class="item-style">Maintenance parts</a></li>
              <li><a href="#" class="item-style">Oil and Grease</a></li>
              <li><a href="#" class="item-style">Engine</a></li>
              <li><a href="#" class="item-style">Transmission</a></li>
              <li><a href="#" class="item-style">Drivetrain & Suspension</a></li>
              <li><a href="#" class="item-style">Electrical System</a></li>
              <li><a href="#" class="item-style">Driver Cab & Body</a></li>
            </ul>
          </div>

          <!-- Truck Collection -->
          <div>
            <h4 class="text-base md:text-lg font-semibold mb-3 md:mb-4">Truck Collection</h4>
            <ul class="space-y-1 md:space-y-2 text-xs md:text-sm">
              <li><a href="#" class="item-style">Dump Truck</a></li>
              <li><a href="#" class="item-style">Tractor</a></li>
              <li><a href="#" class="item-style">Concrete Mixer Truck</a></li>
              <li><a href="#" class="item-style">Crane Truck</a></li>
              <li><a href="#" class="item-style">Mining Truck</a></li>
              <li><a href="#" class="item-style">Wrecker Truck</a></li>
            </ul>
          </div>

          <!-- About Us -->
          <div>
            <h4 class="text-base md:text-lg font-semibold mb-3 md:mb-4">About US</h4>
            <ul class="space-y-1 md:space-y-2 text-xs md:text-sm">
              <li><a href="#" class="item-style">Who We Are ?</a></li>
              <li><a href="#" class="item-style">Our Core Values</a></li>
              <li><a href="#" class="item-style">Why Choose Us?</a></li>
            </ul>
          </div>

          <!-- Help Center -->
          <div>
            <h4 class="text-base md:text-lg font-semibold mb-3 md:mb-4 ">Help Center</h4>
            <ul class="space-y-1 md:space-y-2 text-xs md:text-sm">
              <li><a href="#" class="item-style">Guide</a></li>
              <li><a href="#" class="item-style">FAQ</a></li>
              <li><a href="#" class="item-style">Delivery Policy</a></li>
              <li><a href="#" class="item-style ">Terms of Service</a></li>
            </ul>
          </div>

          <!-- Contact & Language -->
          <div class="col-span-2 md:col-span-1">
            <div>
            <h4 class="text-base md:text-lg font-semibold mb-3 md:mb-4 ">Contact us</h4>
            <ul class="space-y-1 md:space-y-2 text-xs md:text-sm">
              <li><a href="#" class="item-style">daqauto@gmail.com</a></li>
              <li><a href="#" class="item-style">+86 132 2252 4588</a></li>
            </ul>
          </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Bottom Footer -->
    <div class="border-t border-gray-300 py-18 md:py-6">
      <div class="container mx-auto px-4">
        <div class="flex flex-col md:flex-row items-center justify-between gap-3 md:gap-4">
          <div class="flex items-center gap-2">
            <div class="w-6 h-6 md:w-8 md:h-8 rounded" style="background-color: #fe7900;"></div>
            <span class="font-bold text-lg md:text-xl">DAQ AUTO</span>
          </div>
          <div class="text-xs md:text-sm text-black text-center md:text-left">
            DAQ are responsible for Daq offers.
          </div>
        </div>
      </div>
    </div>
  </footer>

  <!-- Bouton flottant mobile uniquement -->
  <!-- <button 
    v-if="!chat.isChatOpen && chat.isMobile"
    @click="chat.openChat"
    class="fixed bottom-18 right-2 w-14 h-14 rounded-full shadow-lg z-50 flex items-center justify-center text-white hover:scale-105 transition-transform md:hidden bg-degrade"
  >
    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
      <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/>
    </svg> -->
    <!-- Badge de notification -->
    <!-- <span 
       v-if="chat.unreadCount > 0"
      class="absolute -top-2 -right-2 text-white text-xs rounded-full w-6 h-6 flex items-center justify-center font-bold " style="background-color: #fe7900;"
    >
      {{ chat.unreadCount > 9 ? '9+' : chat.unreadCount }}
    </span>
  </button> -->

  <!-- Bouton flottant desktop style Facebook -->
  <!-- <button 
    v-if="!chat.isDesktopChatOpen && !chat.isMobile"
    @click="chat.openDesktopChat"
    class="fixed bottom-6 right-4 text-white px-4 py-3 rounded-full shadow-lg z-50 flex items-center gap-2 hover:scale-105 transition-transform md:flex bg-degrade"
  >
    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
      <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/>
    </svg>
    <span class="text-sm font-medium">Chat</span> -->
    <!-- Badge de notification -->
    <!-- <span 
      v-if="chat.unreadCount > 0" 
      class="absolute -top-2 -right-2 text-white text-xs rounded-full w-6 h-6 flex items-center justify-center font-bold" 
      style="background-color: #fe7900;"
    >
      {{ chat.unreadCount > 9 ? '9+' : chat.unreadCount }}
    </span>
  </button> -->

  <!-- Modal mobile -->
  <!-- <ChatModal
    v-if="chat.isChatOpen && chat.isMobile"
    :supplier="chat.supplier"
    :chatMessages="chat.chatMessages"
    @close="chat.closeChat"
    @sendMessage="chat.handleSendMessage"
  /> -->

  <!-- Fenêtre desktop -->
    <ChatDesktop
      :is-open="chat.isDesktopChatOpen"
      :supplier="chat.supplier"
      :chat-messages="chat.chatMessages"
      @close="chat.closeDesktopChat"
      @send-message="chat.handleSendMessage"
    />
    <div class="mobile-only">
      <TabBar />
    </div>

</template>

<script setup>
import { onMounted, onBeforeUnmount } from 'vue'
import { useChatStore } from '../../stores/chat'
import ChatModal from '../product/modals/ChatModal2.vue'
import ChatDesktop from '../product/modals/ChatWindow.vue'
import TabBar from './TabBar.vue'

const chat = useChatStore()

onMounted(() => {
  chat.checkMobile()
  window.addEventListener('resize', chat.checkMobile)
})
onBeforeUnmount(() => {
  window.removeEventListener('resize', chat.checkMobile)
})
</script>

<style scoped>
  /* Styles mobiles cachés par défaut */
.mobile-only {
  display: none;
}

/* Responsive Mobile */
@media (max-width: 768px) {
  .mobile-only {
    display: flex;
  }
}
.primary-color{
  color: #fe7900;
}
.gray-color{
  background-color: #f8f9fa;
  color: #fe7900;
}
.bg-degrade {
  background: linear-gradient(90deg, #fe7900, #f96204);
  border: none;
}
.item-style{
  color: #666;
}
.item-style:hover{
  color: #fe7900;
  font-weight: 600;
  transition: color 0.2s ease-in-out;
}
</style>
