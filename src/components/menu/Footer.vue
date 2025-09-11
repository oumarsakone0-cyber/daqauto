<template>
  <!-- Complete footer redesign with all sections and newsletter -->
  <footer class="bg-gray-900 text-white">
    <!-- Newsletter Section --> 

    <!-- Main Footer Content -->
    <div class="py-8 md:py-12">
      <div class="container mx-auto px-4">
        <!-- Improved mobile grid: 2 columns on mobile, 3 on tablet, 5 on desktop -->
        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-6 md:gap-8">
          <!-- Parts Collection -->
          <div class="col-span-2 md:col-span-1">
            <!-- Reduced title size on mobile -->
            <h4 class="text-base md:text-lg font-semibold mb-3 md:mb-4 text-orange-500">Parts Collection</h4>
            <ul class="space-y-1 md:space-y-2 text-xs md:text-sm">
              <li><a href="#" class="hover:text-orange-500 transition-colors">Maintenance parts</a></li>
              <li><a href="#" class="hover:text-orange-500 transition-colors">Oil and Grease</a></li>
              <li><a href="#" class="hover:text-orange-500 transition-colors">Engine</a></li>
              <li><a href="#" class="hover:text-orange-500 transition-colors">Transmission</a></li>
              <li><a href="#" class="hover:text-orange-500 transition-colors">Drivetrain & Suspension</a></li>
              <li><a href="#" class="hover:text-orange-500 transition-colors">Electrical System</a></li>
              <li><a href="#" class="hover:text-orange-500 transition-colors">Driver Cab & Body</a></li>
            </ul>
          </div>

          <!-- Truck Collection -->
          <div>
            <h4 class="text-base md:text-lg font-semibold mb-3 md:mb-4 text-orange-500">Truck Collection</h4>
            <ul class="space-y-1 md:space-y-2 text-xs md:text-sm">
              <li><a href="#" class="hover:text-orange-500 transition-colors">Dump Truck</a></li>
              <li><a href="#" class="hover:text-orange-500 transition-colors">Tractor</a></li>
              <li><a href="#" class="hover:text-orange-500 transition-colors">Concrete Mixer Truck</a></li>
              <li><a href="#" class="hover:text-orange-500 transition-colors">Crane Truck</a></li>
              <li><a href="#" class="hover:text-orange-500 transition-colors">Mining Truck</a></li>
              <li><a href="#" class="hover:text-orange-500 transition-colors">Wrecker Truck</a></li>
            </ul>
          </div>

          <!-- About Us -->
          <div>
            <h4 class="text-base md:text-lg font-semibold mb-3 md:mb-4 text-orange-500">About US</h4>
            <ul class="space-y-1 md:space-y-2 text-xs md:text-sm">
              <li><a href="#" class="hover:text-orange-500 transition-colors">Who We Are ?</a></li>
              <li><a href="#" class="hover:text-orange-500 transition-colors">Our Core Values</a></li>
              <li><a href="#" class="hover:text-orange-500 transition-colors">Why Choose Us?</a></li>
            </ul>
          </div>

          <!-- Help Center -->
          <div>
            <h4 class="text-base md:text-lg font-semibold mb-3 md:mb-4 text-orange-500">Help Center</h4>
            <ul class="space-y-1 md:space-y-2 text-xs md:text-sm">
              <li><a href="#" class="hover:text-orange-500 transition-colors">Guide</a></li>
              <li><a href="#" class="hover:text-orange-500 transition-colors">FAQ</a></li>
              <li><a href="#" class="hover:text-orange-500 transition-colors">Delivery Policy</a></li>
              <li><a href="#" class="hover:text-orange-500 transition-colors">Terms of Service</a></li>
            </ul>
          </div>

          <!-- Contact & Language -->
          <div class="col-span-2 md:col-span-1">
            <h4 class="text-base md:text-lg font-semibold mb-3 md:mb-4 text-orange-500">Contact us</h4>
            <div class="space-y-3 md:space-y-4 text-xs md:text-sm">
              <div>
                <p class="mb-2">Language:</p>
                <!-- Made select inputs smaller on mobile -->
                <select class="bg-gray-800 border border-gray-700 rounded px-2 py-1 md:px-3 text-white text-xs md:text-sm w-full">
                  <option>English</option>
                  <option>Français</option>
                </select>
              </div>
              <div>
                <p class="mb-2">Country:</p>
                <select class="bg-gray-800 border border-gray-700 rounded px-2 py-1 md:px-3 text-white text-xs md:text-sm w-full">
                  <option>Côte d'Ivoire (USD $)</option>
                </select>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Bottom Footer -->
    <div class="border-t border-gray-800 py-4 md:py-6">
      <div class="container mx-auto px-4">
        <div class="flex flex-col md:flex-row items-center justify-between gap-3 md:gap-4">
          <div class="flex items-center gap-2">
            <!-- Made logo smaller on mobile -->
            <div class="w-6 h-6 md:w-8 md:h-8 bg-orange-500 rounded"></div>
            <span class="font-bold text-lg md:text-xl">DAQ AUTO</span>
          </div>
          <div class="text-xs md:text-sm text-gray-400 text-center md:text-left">
            DAQ are responsible for Daq offers.
          </div>
          <div class="flex items-center gap-2">
            <span class="text-xs md:text-sm">💬 Chat</span>
          </div>
        </div>
      </div>
    </div>
  </footer>
</template>

<style scoped>
.container {
}
</style>

