<template>
    <div 
      class="fixed inset-0 bg-black bg-opacity-30 flex items-center justify-center z-50"
      @click="handleBackdropClick"
    >
      <div 
        class="bg-white rounded-lg p-8 max-w-2xl w-full mx-4 max-h-[90vh] overflow-y-auto"
        @click.stop
      >
        <div class="flex justify-between items-center mb-6">
          <h2 class="text-2xl font-bold text-gray-900">Nouveau Message</h2>
          <button @click="$emit('close')" class="text-gray-400 hover:text-gray-600">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
            </svg>
          </button>
        </div>
  
        <form @submit.prevent="handleSubmit" class="space-y-6">
          <!-- Destinataire -->
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Destinataire</label>
            <div class="relative">
              <input 
                v-model="messageData.recipient"
                type="text" 
                required
                class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-orange focus:border-orange"
                placeholder="Nom du client ou email..."
                @input="searchCustomers"
              >
              <!-- Suggestions de clients -->
              <div v-if="customerSuggestions.length > 0" class="absolute z-10 w-full mt-1 bg-white border border-gray-300 rounded-lg shadow-lg max-h-40 overflow-y-auto">
                <div 
                  v-for="customer in customerSuggestions" 
                  :key="customer.id"
                  @click="selectCustomer(customer)"
                  class="p-3 hover:bg-gray-50 cursor-pointer border-b border-gray-100 last:border-b-0"
                >
                  <div class="font-medium text-gray-900">{{ customer.name }}</div>
                  <div class="text-sm text-gray-500">{{ customer.email }}</div>
                </div>
              </div>
            </div>
          </div>
  
          <!-- Sujet -->
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Sujet</label>
            <input 
              v-model="messageData.subject"
              type="text" 
              required
              class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-orange focus:border-orange"
              placeholder="Objet du message..."
            >
          </div>
  
          <!-- Type de message -->
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Type de message</label>
            <select 
              v-model="messageData.type"
              class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-orange focus:border-orange"
            >
              <option value="general">Message général</option>
              <option value="order">Concernant une commande</option>
              <option value="support">Support technique</option>
              <option value="marketing">Marketing/Promotion</option>
            </select>
          </div>
  
          <!-- Priorité -->
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Priorité</label>
            <div class="flex space-x-4">
              <label class="flex items-center">
                <input 
                  v-model="messageData.priority"
                  type="radio" 
                  value="normal"
                  class="w-4 h-4 text-orange bg-gray-100 border-gray-300 focus:ring-orange focus:ring-2"
                >
                <span class="ml-2 text-sm text-gray-700">Normale</span>
              </label>
              <label class="flex items-center">
                <input 
                  v-model="messageData.priority"
                  type="radio" 
                  value="high"
                  class="w-4 h-4 text-orange bg-gray-100 border-gray-300 focus:ring-orange focus:ring-2"
                >
                <span class="ml-2 text-sm text-gray-700">Urgente</span>
              </label>
            </div>
          </div>
  
          <!-- Message -->
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Message</label>
            <textarea 
              v-model="messageData.message"
              rows="6"
              required
              class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-orange focus:border-orange"
              placeholder="Tapez votre message ici..."
            ></textarea>
          </div>
  
          <!-- Modèles de messages -->
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Modèles rapides</label>
            <div class="grid grid-cols-2 gap-2">
              <button 
                v-for="template in messageTemplates" 
                :key="template.id"
                @click="useTemplate(template)"
                type="button"
                class="p-2 text-left text-sm bg-gray-50 border border-gray-200 rounded-lg hover:bg-gray-100 transition-colors"
              >
                <div class="font-medium text-gray-900">{{ template.title }}</div>
                <div class="text-gray-500 text-xs truncate">{{ template.preview }}</div>
              </button>
            </div>
          </div>
  
          <!-- Pièces jointes -->
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Pièces jointes (optionnel)</label>
            <div class="border-2 border-dashed border-gray-300 rounded-lg p-4 text-center">
              <svg class="mx-auto h-8 w-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"></path>
              </svg>
              <div class="mt-2">
                <label for="file-upload" class="cursor-pointer">
                  <span class="text-sm font-medium text-gray-900">Cliquez pour ajouter des fichiers</span>
                  <span class="text-sm text-gray-500"> ou glissez-déposez</span>
                </label>
                <input 
                  id="file-upload" 
                  name="file-upload" 
                  type="file" 
                  multiple 
                  @change="handleFileUpload"
                  class="sr-only"
                >
              </div>
            </div>
            
            <!-- Liste des fichiers -->
            <div v-if="messageData.attachments.length > 0" class="mt-3 space-y-2">
              <div 
                v-for="(file, index) in messageData.attachments" 
                :key="index"
                class="flex items-center justify-between p-2 bg-gray-50 rounded-lg"
              >
                <div class="flex items-center space-x-2">
                  <svg class="w-4 h-4 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4z" clip-rule="evenodd"/>
                  </svg>
                  <span class="text-sm text-gray-700">{{ file.name }}</span>
                  <span class="text-xs text-gray-500">({{ formatFileSize(file.size) }})</span>
                </div>
                <button 
                  @click="removeFile(index)"
                  type="button"
                  class="text-red-500 hover:text-red-700"
                >
                  <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/>
                  </svg>
                </button>
              </div>
            </div>
          </div>
  
          <!-- Options d'envoi -->
          <div class="space-y-3">
            <div class="flex items-center">
              <input 
                v-model="messageData.sendCopy"
                id="send-copy"
                type="checkbox"
                class="w-4 h-4 text-orange bg-gray-100 border-gray-300 rounded focus:ring-orange focus:ring-2"
              >
              <label for="send-copy" class="ml-2 text-sm text-gray-700">
                M'envoyer une copie de ce message
              </label>
            </div>
            
            <div class="flex items-center">
              <input 
                v-model="messageData.scheduleMessage"
                id="schedule-message"
                type="checkbox"
                class="w-4 h-4 text-orange bg-gray-100 border-gray-300 rounded focus:ring-orange focus:ring-2"
              >
              <label for="schedule-message" class="ml-2 text-sm text-gray-700">
                Programmer l'envoi
              </label>
            </div>
            
            <div v-if="messageData.scheduleMessage" class="ml-6">
              <input 
                v-model="messageData.scheduledTime"
                type="datetime-local"
                class="border border-gray-300 rounded-lg px-3 py-2 focus:ring-orange focus:border-orange"
              >
            </div>
          </div>
  
          <!-- Boutons d'action -->
          <div class="flex space-x-3 pt-4 border-t border-gray-200">
            <button 
              type="button"
              @click="$emit('close')"
              class="flex-1 bg-gray-100 text-gray-700 py-2 px-4 rounded-lg hover:bg-gray-200 transition-colors"
            >
              Annuler
            </button>
            <button 
              type="button"
              @click="saveDraft"
              class="px-6 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition-colors"
            >
              Brouillon
            </button>
            <button 
              type="submit"
              class="px-6 py-2 bg-orange text-white rounded-lg hover:bg-orange-600 transition-colors"
            >
              {{ messageData.scheduleMessage ? 'Programmer' : 'Envoyer' }}
            </button>
          </div>
        </form>
      </div>
    </div>
  </template>
  
  <script setup>
  import { ref, reactive } from 'vue'
  
  const emit = defineEmits(['close', 'send'])
  
  const messageData = reactive({
    recipient: '',
    subject: '',
    type: 'general',
    priority: 'normal',
    message: '',
    attachments: [],
    sendCopy: false,
    scheduleMessage: false,
    scheduledTime: ''
  })
  
  const customerSuggestions = ref([])
  
  const messageTemplates = [
    {
      id: 1,
      title: 'Confirmation de commande',
      preview: 'Votre commande a été confirmée...',
      content: 'Bonjour,\n\nVotre commande #{orderNumber} a été confirmée et sera traitée dans les plus brefs délais.\n\nCordialement,\nL\'équipe Ali Adjamé'
    },
    {
      id: 2,
      title: 'Suivi de livraison',
      preview: 'Votre colis est en route...',
      content: 'Bonjour,\n\nVotre commande #{orderNumber} a été expédiée. Vous pouvez suivre votre colis avec le numéro de suivi : {trackingNumber}\n\nCordialement,\nL\'équipe Ali Adjamé'
    },
    {
      id: 3,
      title: 'Support technique',
      preview: 'Nous avons bien reçu votre demande...',
      content: 'Bonjour,\n\nNous avons bien reçu votre demande de support. Notre équipe technique va examiner votre problème et vous répondra dans les 24h.\n\nCordialement,\nL\'équipe Ali Adjamé'
    },
    {
      id: 4,
      title: 'Promotion spéciale',
      preview: 'Offre exclusive pour vous...',
      content: 'Bonjour,\n\nNous avons une offre spéciale rien que pour vous ! Profitez de -20% sur votre prochaine commande avec le code SPECIAL20.\n\nCordialement,\nL\'équipe Ali Adjamé'
    }
  ]
  
  const customers = [
    { id: 1, name: 'Marie Dubois', email: 'marie.dubois@email.com' },
    { id: 2, name: 'Jean Martin', email: 'jean.martin@email.com' },
    { id: 3, name: 'Sophie Laurent', email: 'sophie.laurent@email.com' },
    { id: 4, name: 'Pierre Durand', email: 'pierre.durand@email.com' },
    { id: 5, name: 'Claire Moreau', email: 'claire.moreau@email.com' }
  ]
  
  const searchCustomers = () => {
    if (messageData.recipient.length > 1) {
      const query = messageData.recipient.toLowerCase()
      customerSuggestions.value = customers.filter(customer => 
        customer.name.toLowerCase().includes(query) || 
        customer.email.toLowerCase().includes(query)
      )
    } else {
      customerSuggestions.value = []
    }
  }
  
  const selectCustomer = (customer) => {
    messageData.recipient = `${customer.name} (${customer.email})`
    customerSuggestions.value = []
  }
  
  const useTemplate = (template) => {
    messageData.message = template.content
    if (!messageData.subject) {
      messageData.subject = template.title
    }
  }
  
  const handleFileUpload = (event) => {
    const files = Array.from(event.target.files)
    files.forEach(file => {
      if (file.size <= 10 * 1024 * 1024) { // 10MB max
        messageData.attachments.push(file)
      }
    })
  }
  
  const removeFile = (index) => {
    messageData.attachments.splice(index, 1)
  }
  
  const formatFileSize = (bytes) => {
    if (bytes === 0) return '0 Bytes'
    const k = 1024
    const sizes = ['Bytes', 'KB', 'MB', 'GB']
    const i = Math.floor(Math.log(bytes) / Math.log(k))
    return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i]
  }
  
  const handleSubmit = () => {
    emit('send', messageData)
  }
  
  const saveDraft = () => {
    console.log('Message sauvegardé en brouillon:', messageData)
    emit('close')
  }
  
  const handleBackdropClick = (event) => {
    if (event.target === event.currentTarget) {
      emit('close')
    }
  }
  </script>
  
  <style scoped>
  .bg-orange {
    background-color: #F65A11;
  }
  
  .text-orange {
    color: #F65A11;
  }
  
  .hover\:bg-orange-600:hover {
    background-color: #e54a0a;
  }
  
  .focus\:ring-orange:focus {
    --tw-ring-color: rgba(246, 90, 17, 0.5);
  }
  
  .focus\:border-orange:focus {
    border-color: #F65A11;
  }
  </style>
  