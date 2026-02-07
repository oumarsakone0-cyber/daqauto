<template>
  <div class="min-h-screen bg-gray-50 relative overflow-hidden">
    <!-- Animation de fond luxueuse avec couleurs plus intenses -->
    <div class="absolute inset-0 overflow-hidden pointer-events-none z-0">
      <!-- Gradient animé principal -->
      <div class="absolute inset-0 bg-gradient-to-br from-gray-50 via-white to-gray-100"></div>
      
      <!-- Orbes flottants avec couleurs plus intenses -->
      <div class="absolute -top-20 -left-20 w-96 h-96 bg-gradient-to-br from-orange-200/60 to-orange-300/40 rounded-full blur-3xl animate-float-slow"></div>
      <div class="absolute top-1/4 -right-20 w-80 h-80 bg-gradient-to-br from-blue-200/50 to-indigo-200/35 rounded-full blur-3xl animate-float-reverse"></div>
      <div class="absolute -bottom-20 left-1/3 w-72 h-72 bg-gradient-to-br from-purple-200/45 to-pink-200/35 rounded-full blur-3xl animate-float-diagonal"></div>
      <div class="absolute top-1/2 right-1/4 w-64 h-64 bg-gradient-to-br from-emerald-200/40 to-teal-200/30 rounded-full blur-3xl animate-float-slow-reverse"></div>
      
      <!-- Particules plus visibles -->
      <div class="absolute top-1/3 left-1/4 w-3 h-3 bg-orange-400/70 rounded-full animate-pulse-slow shadow-lg"></div>
      <div class="absolute top-2/3 right-1/4 w-2.5 h-2.5 bg-blue-400/60 rounded-full animate-pulse-delayed shadow-lg"></div>
      <div class="absolute top-1/2 left-2/3 w-2 h-2 bg-purple-400/50 rounded-full animate-pulse-slow shadow-lg"></div>
      
      <!-- Lignes géométriques plus visibles -->
      <div class="absolute top-0 left-1/4 w-px h-40 bg-gradient-to-b from-transparent via-orange-300/40 to-transparent animate-slide-down"></div>
      <div class="absolute top-1/3 right-1/3 w-32 h-px bg-gradient-to-r from-transparent via-blue-300/35 to-transparent animate-slide-right"></div>
    </div>

    <!-- Container principal -->
    <div class="w-full max-w-[1650px] mx-auto px-2 sm:px-6 py-2 sm:py-2 relative z-10">
        <Navbar/>
      <!-- Breadcrumb -->
      <div class="flex items-center text-sm text-gray-500 sm:mb-2">
        <Home class="w-4 h-4 sm:w-5 sm:h-5" />
        <span class="mx-2">/</span>
        <span class="font-medium text-gray-700">Invoice Management</span>
      </div>

      <!-- Header -->
      <div class="flex flex-col space-y-2 lg:flex-row lg:items-center lg:justify-between lg:space-y-0 mb-4 sm:mb-5">
        <div>
          <h1 class="text-xl sm:text-2xl font-bold text-gray-900 mb-2">Invoice Management</h1>
          <p class="text-sm sm:text-base text-gray-600">Create and manage your invoices in real time</p>
        </div>
        
        <div class="flex items-center gap-3">
          <button 
            @click="resetForm"
            class="submit-btn"
          >
            <RefreshCw class="w-4 h-4" />
            Reset
          </button>
          <button 
            @click="downloadProforma(invoice,subtotal,total)"
            class="btn-degrade-orange"
          >
            <Download class="w-4 h-4" />
            Proforma PDF
          </button>
          <button 
            @click="downloadInvoice(invoice,subtotal,total)"
            class="btn-degrade-orange"
          >
            <Download class="w-4 h-4" />
            Invoice PDF
          </button>
          <button 
            @click="downloadContract(invoice,subtotal,total)"
            class="btn-degrade-orange"
          >
            <Download class="w-4 h-4" />
            Contract PDF
          </button>
        </div>
      </div>

      <!-- Contenu principal : Formulaire + Aperçu -->
      <div class="grid grid-cols-1 lg:grid-cols-3 gap-1">
        <!-- Formulaire (1/3) -->
        <div class="lg:col-span-1">
          <div class="bg-white shadow-lg rounded-lg border border-gray-100 p-4 sticky top-6">
            <h2 class="text-lg font-bold text-gray-900 mb-3 flex items-center">
              <FileText class="w-5 h-5 mr-2 primary-color" />
              Invoice information
            </h2>

            <!-- Informations générales -->
            <div class="space-y-2 mb-3">
              <!-- <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Invoice number</label>
                <input 
                  v-model="invoice.number" 
                  type="text" 
                  class="input-style"
                  placeholder=""
                >
              </div> -->

              <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Invoice date</label>
                <input 
                  v-model="invoice.date" 
                  type="date" 
                  class="input-style"
                >
              </div>

              <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Due date</label>
                <input 
                  v-model="invoice.dueDate" 
                  type="date" 
                  class="input-style"
                >
              </div>
            </div>

            <!-- Informations client -->
            <div class="mb-3">
              <h3 class="text-md font-semibold text-gray-900 mb-2 flex items-center">
                <User class="w-4 h-4 mr-2 primary-color" />
                Customer information
              </h3>
              <div class="space-y-2">
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-1">Customer Name</label>
                  <input 
                    v-model="invoice.client.name" 
                    type="text" 
                    class="input-style"
                    placeholder="Full name"
                  >
                </div>

                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                  <input 
                    v-model="invoice.client.email" 
                    type="email" 
                    class="input-style"
                    placeholder="email@exemple.com"
                  >
                </div>

                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-1">phone number</label>
                  <input 
                    v-model="invoice.client.phone" 
                    type="tel" 
                    class="input-style"
                    placeholder="+225 XX XX XX XX XX"
                  >
                </div>

                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-1">Address</label>
                  <textarea 
                    v-model="invoice.client.address" 
                    rows="3"
                    class="input-style"
                    placeholder="Full address"
                  ></textarea>
                </div>
              </div>
            </div>

            <!-- Articles -->
            <div class="pt-2">
              <div class="flex items-center justify-between mb-4">
                <h3 class="text-md font-semibold text-gray-900 flex items-center">
                  <ShoppingCart class="w-4 h-4 mr-2 primary-color" />
                  Items
                </h3>
                <button 
                  @click="addItem"
                  class="btn-degrade-orange h-10"
                >
                  <Plus class="w-3 h-3 " />
                  Add
                </button>
              </div>

              <!-- Loading state for products -->
              <div v-if="loadingProducts" class="text-center py-4 text-sm primary-color">
                Loading products...
              </div>
              <div class="space-y-2 max-h-150  overflow-y-auto">
                <div 
                  v-for="(item, index) in invoice.items" 
                  :key="index"
                  class="p-4 bg-gray-50 rounded-lg border border-gray-200 relative"
                >
                  <button 
                    v-if="invoice.items.length > 1"
                    @click="removeItem(index)"
                    style="padding: 1px;"
                    class="absolute top-1 right-2 btn-outline"
                  >
                    <X class="w-5 h-5" />
                  </button>

                  <div class="space-y-2">
                    <!-- Replace text input with select dropdown for products -->
                    <div>
                      <label class="block text-xs font-medium text-gray-700 mb-1">Product</label>
                      <select 
                        v-model="item.productId"
                        @change="onProductSelect(index)"
                        class="input-style"
                      >
                        <option value="">Select a product</option>
                        <option 
                          v-for="product in products" 
                          :key="product.id" 
                          :value="product.id"
                        >
                          {{ product.name }}
                        </option>
                      </select>
                    </div>
                    <div>
                      <label class="block text-xs font-medium text-gray-700 mb-1">VIN number</label>
                      <select 
                        v-model="item.vin"
                        class="input-style"
                      >
                        <option value="">Select VIN number</option>
                        <option 
                          v-for="vin in selectedVInNumber(item.productId)" 
                          :key="vin" 
                          :value="vin"
                        >
                          {{ vin }}
                        </option>
                      </select>
                    </div>
                    <div>
                      <label class="block text-xs font-medium text-gray-700 mb-1">Trim number</label>
                      <select 
                        v-model="item.trim_number"
                        class="input-style"
                      >
                        <option value="">Select Trim number</option>
                        <option 
                          v-for="trim in selectedTrimNumber(item.productId)" 
                          :key="trim" 
                          :value="trim"
                        >
                          {{ trim}}
                        </option>
                      </select>
                    </div>
                    <div>
                      <label class="block text-xs font-medium text-gray-700 mb-1">Colors</label>
                      <select 
                        v-model="item.color"
                        class="input-style"
                      >
                        <option value="">Select a color</option>
                        <option 
                          v-for="color in selectedColors(item.productId)" 
                          :key="color" 
                          :value="color"
                        >
                          {{color }}
                        </option>
                      </select>
                    </div>

                    <div class="grid grid-cols-2 gap-1">
                      <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1">Quantity</label>
                        <input 
                          v-model.number="item.quantity" 
                          type="number" 
                          min="1"
                          class="input-style"
                        >
                      </div>

                      <!-- Price is now editable after auto-population -->
                      <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1">Unit price</label>
                        <input 
                          v-model.number="item.price" 
                          type="number" 
                          min="0"
                          class="input-style"
                        >
                      </div>
                    </div>

                    <div class="pt-2">
                      <div class="flex justify-between items-center">
                        <span class="text-xs font-medium text-gray-600">Total:</span>
                        <span class="text-sm font-bold text-gray-900">{{ formatCurrency(item.quantity * item.price) }}</span>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <!-- Notes -->
            <div class="pt-2 mt-2">
              <label class="block text-sm font-medium text-gray-700 mb-1"> Other Notes / Conditions</label>
              <textarea 
                v-model="invoice.notes" 
                rows="3"
                class="input-style text-sm"
                placeholder="Payment terms, additional notes..."
              ></textarea>
            </div>
          </div>
        </div>

        <!-- Aperçu de la facture (2/3) -->
        <div id="invoice-pdf" class="lg:col-span-2 " style="padding-bottom: 60px">
          <div class="bg-white shadow-2xl rounded-lg border   border-gray-100 p-4 sm:p-8" id="invoice-preview">
            <!-- En-tête de la facture -->
            <div class="flex justify-between items-start mb-5 pb-4 border-b-2 border-gray-200">
              <div>
                <div class="flex items-center mb-4">
                  <div class="w-10 h-10 bg-orange rounded-lg flex items-center justify-center mr-4">
                    <FileText class="w-5 h-5 text-white" />
                  </div>
                  <div>
                    <h2 class="text-2xl font-bold text-gray-900">INVOICE</h2>
                    <p class="text-xs text-gray-500">DAQ AUTO Marketplace</p>
                  </div>
                </div>
                <p 
                v-if="order"
                class="text-xs text-gray-500">Order number: <span class="font-bold">{{ order.numero_commande }}</span></p>
              </div>
              <div class="text-left">
                <div>

                  <div class="inline-block px-4 py-1 bg-orange rounded-lg mb-1 text-white">
                    <p class="text-white font-bold text-sm">{{ invoice.number || 'FAC-XXXX-XXX' }}</p>
                  </div>
                  <div class="text-xs text-gray-600 space-y-0">
                    <p > Date: <span class="font-bold">{{ formatDate(invoice.date) }} </span></p>
                    <p> Due date:  <span class="font-bold">{{ formatDate(invoice.dueDate) }}</span></p>
                  </div>
                </div>
              </div>
            </div>
            <div class="flex justify-between items-start mb-5">
              <div>
                <div class="text-xs text-gray-600 space-y-1">
                  <div class="flex font-bold">
                    <Building2 class="w-4 h-4 mr-2 mt-0.5"/> 
                    Seller :
                  </div>
                  <div class="flex py-1 gap-2">
                    <img :src="logo" />
                    <p class="font-medium">DAQ AUTO</p>
                  </div>
                  <p>Abidjan, Côte d'Ivoire</p>
                  <p>+225 01 53 67 60 62</p>
                  <p>commandes@daqauto.com</p>
                  <p>91310000MA1K4T123X</p>
                </div>
              </div>

                <div class="text-xs text-gray-600 space-y-1 text-left mr-6">
                  <div class="flex font-bold">
                    <UserCheck class="w-4 h-4 mr-2" />
                    Buyer :
                  </div>
                  <div class="text-gray-900 space-y-1">
                  <p class="font-medium ">{{ invoice.client.name || 'Customer name' }}</p>
                  <p >{{ invoice.client.address || 'Customer address' }}</p>
                  <p>{{ invoice.client.phone || '+225 XX XX XX XX XX' }}</p>
                  <p >{{ invoice.client.email || 'email@exemple.com' }}</p>
                </div>
                </div>
            </div>

            <!-- Tableau des articles -->
            <div class="mb-5">
              <table class="w-full">
                <thead>
                  <tr class="bg-gray-100 border-b-2 border-gray-300">
                    <th class="text-center py-3 px-1 text-xs font-bold text-gray-700">NO.</th>
                    <th class="text-center py-3 px-1 text-xs font-bold text-gray-700">Product Type</th>
                    <th class="text-center py-3 px-1 text-xs font-bold text-gray-700">Description</th>
                    <th class="text-center py-3 px-1 text-xs font-bold text-gray-700">Trim / Vehicle Model</th>
                    <th class="text-center py-3 px-1 text-xs font-bold text-gray-700">VIN</th>
                    <th class="text-center py-3 px-1 text-xs font-bold text-gray-700">Stock Number</th>
                    <th class="text-center py-3 px-1 text-xs font-bold text-gray-700">Color</th>
                    <th class="text-center py-3 px-1 text-xs font-bold text-gray-700">Quantity</th>
                    <th class="text-center py-3 px-1 text-xs font-bold text-gray-700">Unit Price</th>
                    <th class="text-right py-3 px-1 text-xs font-bold text-gray-700">TOTAL</th>
                  </tr>
                </thead>
                <tbody>
                  <!-- Display product name from selected product -->
                  <tr 
                    v-for="(item, index) in invoice.items" 
                    :key="index"
                    class="border-b border-gray-200 hover:bg-gray-50 transition-colors"
                  >
                    <td class="text-center py-4 text-xs text-gray-900">{{ index +1 }}</td>
                    <td class="text-center py-4 text-xs text-gray-900">{{ item.product_type || "N/A" }}</td>
                    <td class="text-center py-4 text-xs text-gray-900">{{ item.product_name || "N/A" }}</td>
                    <td class="text-center py-4 text-xs text-gray-900">{{ item.trim_number || "N/A" }}</td>
                    <td class="text-center py-4 text-xs text-gray-900">{{ item.vin || "N/A" }}</td>
                    <td class="text-center py-4 text-xs text-gray-900">{{ item.stock_number || "N/A" }}</td>
                    <td class="text-center py-4 text-xs text-gray-900">{{ item.color || "N/A" }}</td>
                    <td class="py-4 text-xs text-gray-700 text-center">{{ item.quantity }}</td>
                    <td class="py-4 text-xs text-gray-700 text-center">{{ formatCurrency(item.price) }}</td>
                    <td class="py-4 text-xs font-semibold text-gray-900 text-right">{{ formatCurrency(item.quantity * item.price) }}</td>
                  </tr>
                  <tr v-if="invoice.items.length === 0">
                    <td colspan="4" class="py-8 text-center text-gray-400 text-sm italic">
                      No articles added yet.
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>

            

            <!-- Totaux -->
            <div class="flex justify-end mb-12">
              <div class="w-full max-w-sm space-y-3">
                <div class="flex justify-between items-center py-2 border-b border-gray-200">
                  <span class="text-xs font-medium text-gray-600">Subtotal:</span>
                  <span class="text-xs font-semibold text-gray-900">{{ formatCurrency(subtotal) }}</span>
                </div>
                <div class="flex justify-between items-center py-2 border-b border-gray-200">
                  <span class="text-xs font-medium text-gray-600">Shipping / Handling :</span>
                  <span class="text-xs font-semibold text-gray-900"></span>
                </div>
                <div class="flex justify-between items-center py-2 border-b border-gray-200">
                  <span class="text-xs font-medium text-gray-600">Insurance :</span>
                  <span class="text-xs font-semibold text-gray-900"></span>
                </div>
                <div class="flex justify-between items-center py-2 border-b border-gray-200">
                  <span class="text-xs font-medium text-gray-600">Sea Shipping :</span>
                  <span class="text-xs font-semibold text-gray-900"></span>
                </div>
                <div class="flex justify-between items-center py-4 bg-orange rounded-lg px-4">
                  <span class="text-base font-bold text-white">TOTAL:</span>
                  <span class="text-xl font-bold text-white">{{ formatCurrency(total) }}</span>
                </div>
              </div>
            </div>

            <!-- Bank Informations -->
            <div class="mb-5">
              <div class="bg-gradient-to-r from-orange-50 to-orange-100 rounded-lg p-6 border border-orange-200">
                <h3 class="text-xs font-bold primary-color mb-3 flex items-center">
                  <LandmarkIcon class="w-3 h-3 mr-2" />
                  Bank Information
                </h3>
                <div class="text-gray-900 space-y-1">
                  <p class="text-xs">Beneficiary Name : <span class="font-bold"> DAQ AUTO CO., LTD</span></p>
                  <p class="text-xs">Bank Name : <span class="font-bold"> Bank of China, Chongqing Branch</span></p>
                  <p class="text-xs">Account Number : <span class="font-bold"> 123 456 7890</span></p>
                  <p class="text-xs">SWIFT Code : <span class="font-bold"> BKCHCNBJ600</span></p>
                  <p class="text-xs">Bank Address : <span class="font-bold"> No. 123 Jiangbei District, Chongqing, China</span></p>
                </div>
              </div>
            </div>

            <!-- Notes -->
            <div v-if="invoice.notes" class="border-t-2 border-gray-200 pt-8" >
              <h3 class="text-sm font-bold text-gray-700 mb-3">Terms & Conditions / 条款与条件</h3>
              <ul class="text-xs text-gray-700 mb-3 list-disc list-inside" >
                <li>Incoterm / 国际贸易术语: CIF / FOB / EXW [Specify port ]</li>
                <li>Payment Terms / 付款方式: T/T — 100% payment received before shipment. </li>
                <li>Production Time / 生产周期: [e.g. 15–20 working days after deposit ]</li>
                <li>Estimated Delivery / 预计发货日期: [Insert / 填写] </li>
                <li>Commercial invoice /商业发票发票说明:This is the final commercial invoice for customs and payment confirmation. / 本文件为正式商业发票，用于清关及付款确认。</li>
              </ul>
              <h3 class="text-sm font-bold text-gray-700 mb-3">Shipping & Packaging / 运输与包装</h3>
              <ul class="text-xs text-gray-700 mb-3 list-disc list-inside" >
                <li>Mode of Transport / 运输方式: [Sea / Air / Road / ]</li>
                <li>Port of Loading / 装货港: [Insert ]</li>
                <li>Port of Destination / 目的港: [Insert ]</li>
                <li>Port of Destination / 目的港: [Insert ]</li>
              </ul>
              <h3 class="text-sm font-bold text-gray-700 mb-3">Declaration / 声明</h3>
              <p class="text-xs text-gray-700 mb-3">We hereby certify that the goods listed above are of Chinese origin and the details given are true and correct.
              </p>
              <p class="text-xs text-gray-700 mb-3">

                兹证明上述货物原产于中国，所列内容真实无误
              </p>
              <h3 class="text-sm font-bold text-gray-700 mb-3">Others Notes / Conditions</h3>
              <p class="text-xs text-gray-600 whitespace-pre-line">{{ invoice.notes }}</p>
            </div>
            <div class="flex justify-end mb-12" style="page-break-inside: avoid; break-inside: avoid; margin-bottom: 20px;">
              <div class="w-full max-w-sm space-y-3 text-right">
                <h3 class="text-xs text-gray-700 ">Authorized Signature & Stamp / 授权签字与公司印章</h3>
                  <h4 class="text-xs text-gray-700">Name / 姓名: [Authorized person / 授权人]</h4>
                  <h4 class="text-xs text-gray-700">Signature / 签名:___________________</h4>
                  <h4 class="text-xs text-gray-700">Company Stamp / 公司印章:___________________</h4>
              </div>
            </div>

            <!-- Pied de page -->
            <div class="mt-12 pt-8 border-t border-gray-200 text-center" style="page-break-inside: avoid; break-inside: avoid; margin-bottom: 20px;">
              <p class="text-xs text-gray-500">
                Thank you for your trust! For any questions, please contact us at commandes@daqauto.com
              </p>
              <p class="text-xs text-gray-400 mt-2">
                DAQ AUTO Marketplace - Your trusted partner in global vehicle sourcing.
              </p>
              <p class="text-xs primary-color font-medium mt-3">
                Invoice generated on DaqAuto.com
              </p>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted} from 'vue'
import { 
  Home, FileText, User, ShoppingCart, Download, RefreshCw, 
  Plus, X,
  Building2,
  UserCheck,
  LandmarkIcon
} from 'lucide-vue-next'
import { productsApi } from '../../services/api'
import logo from "../../assets/favicon.jpg"
import Navbar from '../boutiques/Navbar.vue'
import { downloadInvoice } from '../../composables/Invoice.js'
import {downloadContract} from '../../composables/contract.js'
import { downloadProforma } from '../../composables/ProformaInvoice.js'
import { formatCurrency, formatDate} from '../../composables/utils.js'
import { useRoute } from 'vue-router'

const route = useRoute()

const products = ref([])
const order = ref(null)
const selected_product_colors = ref([])
const selected_product_trim_numbers = ref([])
const selected_product_vin_numbers = ref([])
const loadingProducts = ref(false)

const currentUser = ref(null)
const currentBoutique = ref(null)


// État de la facture
const invoice = ref({
  number: '',
  date: new Date().toISOString().split('T')[0],
  dueDate: new Date(Date.now() + 30 * 24 * 60 * 60 * 1000).toISOString().split('T')[0],
  client: {
    name: '',
    email: '',
    phone: '',
    address: ''
  },
  items: [
    {
      productId: '',
      product_type:'',
      product_name:"",
      trim_number:"",
      vin:"",
      stock_number:"",
      color:"",
      quantity: 1,
      price: 0
    }
  ],
  specs:[],
  notes: 'Payment is due within 30 days. Please mention the invoice number when making your payment.'
})

const initializeUserData = () => {
  try {
    const authToken = localStorage.getItem('authToken') || sessionStorage.getItem('authToken')
    
    if (!authToken) {
      console.error('[v0] Token d\'authentification manquant')
      return false
    }

    const userData = localStorage.getItem('user') || sessionStorage.getItem('user')
    
    if (!userData) {
      return false
    }

    const user = JSON.parse(userData)
    
    if (!user.id || !user.email) {
      return false
    }

    currentUser.value = {
      id: user.id,
      full_name: user.full_name,
      email: user.email,
      boutiques: user.boutiques || []
    }

    if (user.boutiques && user.boutiques.length > 0) {
      const savedBoutiqueId = localStorage.getItem('selectedBoutiqueId')
      if (savedBoutiqueId) {
        const savedBoutique = user.boutiques.find(b => b.id == savedBoutiqueId)
        if (savedBoutique) {
          currentBoutique.value = savedBoutique
        } else {
          currentBoutique.value = user.boutiques[0]
        }
      } else {
        currentBoutique.value = user.boutiques[0]
      }
    } else {
      return false
    }

    return true
  } catch (err) {
    console.error('[v0] Erreur lors de l\'initialisation:', err)
    return false
  }
}

onMounted(async () => {
  const initialized = initializeUserData()
  if (initialized) {
    await fetchProducts()
  } else {
  }

  const orderId = route.query.orderId
 
  const orderData = sessionStorage.getItem('selectedOrder')
  order.value = orderData ? JSON.parse(orderData) : null

  if (order!==null) {
    // client informations
    invoice.value.client.name = order.value.client_nom
    invoice.value.client.address = order.value.commune
    invoice.value.client.email = order.value.client_telephone

    // product data
    invoice.value.items[0].productId = order.value.produit_id
    invoice.value.items[0].product_name = order.value.produit_nom
    invoice.value.items[0].price = order.value.produit_prix_unitaire
    invoice.value.items[0].quantity = order.value.quantite
    total.value = order.value.total
    onProductSelect(0)


  } else if (orderId) {
    // sinon charger depuis l'API
   await loadOrderData(orderId)
  }
  
})

onUnmounted(() => {
    order.value= null
    sessionStorage.removeItem('selectedOrder')
})

const loadOrderData = async (orderId) => {
  try {
    const response = await axios.get(`${API_BASE_URL}/commandes.php?action=get&id=${orderId}`)
    if (response.data.success) {
      // traiter les données
    }
  } catch (error) {
    console.error('Error loading order:', error)
  }
}

const fetchProducts = async () => {
  if (!currentBoutique.value?.id || !currentUser.value?.id) {
    return
  }

  try {
    loadingProducts.value = true
    
    const response = await productsApi.getProducts({
      boutique_id: currentBoutique.value.id,
      user_id: currentUser.value.id,
      page: 1,
      limit: 1000
    })
    
    if (response.success) {
      products.value = response.data || []
    } else {
      console.error('[v0] Erreur API:', response.error)
    }
  } catch (err) {
    console.error('[v0] Erreur fetchProducts:', err)
  } finally {
    loadingProducts.value = false
  }
}
const selectedColors = (id) => {
  const items = selected_product_colors.value.find(i => i.id === id)
  return items ? items.colors : []
}
const selectedVInNumber = (id) => {
  const items = selected_product_vin_numbers.value.find(i => i.id === id)
  return items ? items.vin_numbers : []
}
const selectedTrimNumber = (id) => {
  const items = selected_product_trim_numbers.value.find(i => i.id === id)
  return items ? items.trim_numbers : []
}

const onProductSelect = (index) => {
  const item = invoice.value.items[index]
  
  const selectedProduct = products.value.find(p => p.id === item.productId)
  item.color = ""
  item.vin = ""
  item.trim_number = ""
  
  if (selectedProduct) {
    item.price = selectedProduct.unit_price || selectedProduct.price || 0
    item.product_type = selectedProduct.category_name || ""
    item.product_name= selectedProduct.name || ""
    item.stock_number = selectedProduct.stock_number || ""
    item.trim_number = selectedProduct.trim_numbers[0] || ""
    item.vin = selectedProduct.vin_numbers[0] || ""
    item.color = selectedProduct.color_names[0] || ""

    if (!selected_product_colors.value.some(product =>product.id === selectedProduct.id)) {
      selected_product_colors.value.push(
            {
              id:selectedProduct.id,
              colors:selectedProduct.color_names
            }
          )
    } 
    if (!selected_product_vin_numbers.value.some(product =>product.id === selectedProduct.id)) {
      selected_product_vin_numbers.value.push(
            {
              id:selectedProduct.id,
              vin_numbers:selectedProduct.vin_numbers
            }
          )
    } 
    if (!selected_product_trim_numbers.value.some(product =>product.id === selectedProduct.id)) {
      selected_product_trim_numbers.value.push(
            {
              id:selectedProduct.id,
              trim_numbers:selectedProduct.trim_numbers
            }
          )
    }

    if (!invoice.value.specs.some(spec => spec.id === selectedProduct.id)) {
      if(invoice.value.items.length ===1 && invoice.value.specs.length >0){
        invoice.value.specs=[]
      }
      invoice.value.specs.push(
        {
          id:selectedProduct.id,
          product_name: selectedProduct.name || "N/A",
          primary_image:selectedProduct.primary_image || 'N/A',
          vehicle_condition: selectedProduct.vehicle_condition || 'N/A',
          vehicle_mileage:selectedProduct.vehicle_mileage || 'N/A',
          production_date:selectedProduct.production_date || 'N/A',
          vehicle_year: selectedProduct.vehicle_year || 'N/A',
          country_of_origin: selectedProduct.country_of_origin || 'N/A',
          vehicle_brand: selectedProduct.vehicle_make || 'N/A',
          vehicle_model: selectedProduct.vehicle_model || 'N/A',
          drive_type:selectedProduct.drive_type || 'N/A',
          wheelbase : selectedProduct.wheelbase || 'N/A',
          engine_number:selectedProduct.engine_number || 'N/A',
          engine_brand:selectedProduct.engine_brand || 'N/A',
          power:selectedProduct.power || 'N/A',
          engine_emissions:selectedProduct.engine_emissions || 'N/A',
          transmission_type:selectedProduct.transmission_type || 'N/A',
          curb_weight:selectedProduct.curb_weight || 'N/A',
          gvw:selectedProduct.gvw || 'N/A',
          vehicle_dimensions:selectedProduct.dimensions || 'N/A',
          suspension_type:selectedProduct.suspension_type || 'N/A',
          fuel_tank_capacity:selectedProduct.fuel_tank_capacity || 'N/A',
          fuel_type:selectedProduct.fuel_type || 'N/A',
          brake_system:selectedProduct.brake_system || 'N/A',
          cabin_type:selectedProduct.cabin_type || 'N/A',
          tire_size:selectedProduct.tyre_size || 'N/A',
          payload_capacity: selectedProduct.payload_capacity || 'N/A',
          speed:selectedProduct.speed || 'N/A',
          engine_model:selectedProduct.engine_model || 'N/A' ,
          hor_sepower:selectedProduct.hor_sepower || 'N/A',
          gear_box_brand:selectedProduct.gear_box_brand || 'N/A',
          gear_box_model:selectedProduct.gear_box_model || 'N/A',
          chassis_dimensions:selectedProduct.chassis_dimensions || 'N/A',
          frame_rear_suspension:selectedProduct.frame_rear_suspension || 'N/A',
          overall_dimensions:selectedProduct.overall_dimensions || 'N/A',
          suspension_front:selectedProduct.suspension_front || 'N/A',
          suspension_rear:selectedProduct.suspension_rear || 'N/A',
          axle_brand:selectedProduct.axle_brand || 'N/A',
          axle_front:selectedProduct.axle_front || 'N/A',
          axle_rear:selectedProduct.axle_rear || 'N/A' ,
          axle_speed_ratio:selectedProduct.axle_speed_ratio || 'N/A',
          air_filter:selectedProduct.air_filter || 'N/A',
          electrics:selectedProduct.electrics || 'N/A',
        }
      )
    }
  }
}

// Calculs
const subtotal = computed(() => {
  return invoice.value.items.reduce((sum, item) => sum + (item.quantity * item.price), 0)
})

const total = computed(() => {
  return order.value ? order.value.total: subtotal.value 
})

const addItem = () => {
  invoice.value.items.push({
      productId: '',
      product_type:'',
      product_name:"",
      trim_number:"",
      vin:"",
      stock_number:"",
      color:"",
      quantity: 1,
      price: 0
  })
}

const removeItem = (index) => {
  if (invoice.value.items.length > 1) {
    invoice.value.items.splice(index, 1)
    specsToRemove = invoice.value.specs.findIndex(spec => spec.id === invoice.value.items[index]?.productId)
    if(specsToRemove !== -1){
      invoice.value.specs.splice(specsToRemove,1)
    }
  }
}

const resetForm = () => {
  invoice.value = {
    number: 'INV-2025-' + String(Math.floor(Math.random() * 1000)).padStart(3, '0'),
    date: new Date().toISOString().split('T')[0],
    dueDate: new Date(Date.now() + 30 * 24 * 60 * 60 * 1000).toISOString().split('T')[0],
    taxRate: 18,
    client: {
      name: '',
      email: '',
      phone: '',
      address: ''
    },
    items: [
      {
        productId: '',
        product_type:'',
        product_name:"",
        trim_number:"",
        vin:"",
        stock_number:"",
        color:"",
        quantity: 1,
        price: 0
      }
    ],
    specs:[],
    notes: 'Payment is due within 30 days. Please mention the invoice number when making your payment.'
  }
}
</script>

<style scoped>
/* Animations luxueuses */
@keyframes float-slow {
  0%, 100% {
    transform: translateY(0px) translateX(0px) rotate(0deg);
  }
  25% {
    transform: translateY(-30px) translateX(15px) rotate(2deg);
  }
  50% {
    transform: translateY(-15px) translateX(-20px) rotate(-1deg);
  }
  75% {
    transform: translateY(-40px) translateX(8px) rotate(1deg);
  }
}

@keyframes float-reverse {
  0%, 100% {
    transform: translateY(0px) translateX(0px) rotate(0deg);
  }
  25% {
    transform: translateY(20px) translateX(-15px) rotate(-2deg);
  }
  50% {
    transform: translateY(35px) translateX(25px) rotate(1deg);
  }
  75% {
    transform: translateY(8px) translateX(-8px) rotate(-1deg);
  }
}

@keyframes float-diagonal {
  0%, 100% {
    transform: translateY(0px) translateX(0px) scale(1) rotate(0deg);
  }
  33% {
    transform: translateY(-25px) translateX(20px) scale(1.1) rotate(1deg);
  }
  66% {
    transform: translateY(15px) translateX(-15px) scale(0.9) rotate(-1deg);
  }
}

@keyframes float-slow-reverse {
  0%, 100% {
    transform: translateY(0px) translateX(0px) rotate(0deg);
  }
  25% {
    transform: translateY(-15px) translateX(-20px) rotate(-1deg);
  }
  50% {
    transform: translateY(25px) translateX(10px) rotate(2deg);
  }
  75% {
    transform: translateY(-10px) translateX(-5px) rotate(-0.5deg);
  }
}

@keyframes pulse-slow {
  0%, 100% {
    opacity: 0.4;
    transform: scale(1);
  }
  50% {
    opacity: 0.8;
    transform: scale(1.4);
  }
}

@keyframes pulse-delayed {
  0%, 100% {
    opacity: 0.3;
    transform: scale(1);
  }
  50% {
    opacity: 0.7;
    transform: scale(1.5);
  }
}

@keyframes slide-down {
  0% {
    transform: translateY(-100%);
    opacity: 0;
  }
  50% {
    opacity: 1;
  }
  100% {
    transform: translateY(100%);
    opacity: 0;
  }
}

@keyframes slide-right {
  0% {
    transform: translateX(-100%);
    opacity: 0;
  }
  50% {
    opacity: 1;
  }
  100% {
    transform: translateX(100%);
    opacity: 0;
  }
}

.animate-float-slow {
  animation: float-slow 20s ease-in-out infinite;
}

.animate-float-reverse {
  animation: float-reverse 18s ease-in-out infinite;
}

.animate-float-diagonal {
  animation: float-diagonal 22s ease-in-out infinite;
}

.animate-float-slow-reverse {
  animation: float-slow-reverse 24s ease-in-out infinite;
}

.animate-pulse-slow {
  animation: pulse-slow 4s ease-in-out infinite;
}

.animate-pulse-delayed {
  animation: pulse-delayed 5s ease-in-out infinite 1s;
}

.animate-slide-down {
  animation: slide-down 8s linear infinite;
}

.animate-slide-right {
  animation: slide-right 10s linear infinite;
}

/* Scrollbar personnalisée */
::-webkit-scrollbar {
  width: 8px;
}

::-webkit-scrollbar-track {
  background: #f1f1f1;
  border-radius: 10px;
}

::-webkit-scrollbar-thumb {
  background: linear-gradient(to bottom, #f97316, #ea580c);
  border-radius: 10px;
}

::-webkit-scrollbar-thumb:hover {
  background: linear-gradient(to bottom, #ea580c, #c2410c);
}
</style>