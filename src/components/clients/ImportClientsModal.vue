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
          <h2 class="text-2xl font-bold text-gray-900">Importer des clients</h2>
          <button @click="$emit('close')" class="text-gray-400 hover:text-gray-600">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
            </svg>
          </button>
        </div>
  
        <div class="space-y-6">
          <!-- Instructions -->
          <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
            <div class="flex">
              <svg class="w-5 h-5 text-blue-400 mt-0.5 mr-3" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
              </svg>
              <div>
                <h3 class="text-sm font-medium text-blue-800">Instructions d'import</h3>
                <div class="mt-2 text-sm text-blue-700">
                  <ul class="list-disc list-inside space-y-1">
                    <li>Utilisez un fichier CSV ou Excel (.xlsx)</li>
                    <li>Colonnes requises : nom, email, téléphone</li>
                    <li>Colonnes optionnelles : adresse, source, segment, tags, notes</li>
                    <li>Maximum 1000 clients par import</li>
                  </ul>
                </div>
              </div>
            </div>
          </div>
  
          <!-- Télécharger le modèle -->
          <div class="text-center">
            <button 
              @click="downloadTemplate"
              class="inline-flex items-center px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition-colors"
            >
              <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
              </svg>
              Télécharger le modèle CSV
            </button>
          </div>
  
          <!-- Zone de drop -->
          <div class="border-2 border-dashed border-gray-300 rounded-lg p-8 text-center">
            <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48">
              <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
            <div class="mt-4">
              <label for="file-upload" class="cursor-pointer">
                <span class="mt-2 block text-sm font-medium text-gray-900">
                  Cliquez pour télécharger un fichier
                </span>
                <span class="mt-1 block text-sm text-gray-500">
                  ou glissez-déposez votre fichier CSV/Excel ici
                </span>
              </label>
              <input 
                id="file-upload" 
                name="file-upload" 
                type="file" 
                accept=".csv,.xlsx,.xls"
                @change="handleFileUpload"
                class="sr-only"
              >
            </div>
          </div>
  
          <!-- Fichier sélectionné -->
          <div v-if="selectedFile" class="bg-gray-50 rounded-lg p-4">
            <div class="flex items-center justify-between">
              <div class="flex items-center space-x-3">
                <svg class="w-8 h-8 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                  <path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4z" clip-rule="evenodd"/>
                </svg>
                <div>
                  <div class="font-medium text-gray-900">{{ selectedFile.name }}</div>
                  <div class="text-sm text-gray-500">{{ formatFileSize(selectedFile.size) }}</div>
                </div>
              </div>
              <button 
                @click="removeFile"
                class="text-red-500 hover:text-red-700"
              >
                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                  <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/>
                </svg>
              </button>
            </div>
          </div>
  
          <!-- Options d'import -->
          <div class="space-y-4">
            <h3 class="text-lg font-medium text-gray-900">Options d'import</h3>
            
            <div class="space-y-3">
              <div class="flex items-center">
                <input 
                  v-model="importOptions.skipDuplicates"
                  id="skip-duplicates"
                  type="checkbox"
                  class="w-4 h-4 text-orange bg-gray-100 border-gray-300 rounded focus:ring-orange focus:ring-2"
                >
                <label for="skip-duplicates" class="ml-2 text-sm text-gray-700">
                  Ignorer les doublons (basé sur l'email)
                </label>
              </div>
              
              <div class="flex items-center">
                <input 
                  v-model="importOptions.updateExisting"
                  id="update-existing"
                  type="checkbox"
                  class="w-4 h-4 text-orange bg-gray-100 border-gray-300 rounded focus:ring-orange focus:ring-2"
                >
                <label for="update-existing" class="ml-2 text-sm text-gray-700">
                  Mettre à jour les clients existants
                </label>
              </div>
              
              <div class="flex items-center">
                <input 
                  v-model="importOptions.sendWelcomeEmail"
                  id="send-welcome"
                  type="checkbox"
                  class="w-4 h-4 text-orange bg-gray-100 border-gray-300 rounded focus:ring-orange focus:ring-2"
                >
                <label for="send-welcome" class="ml-2 text-sm text-gray-700">
                  Envoyer un email de bienvenue aux nouveaux clients
                </label>
              </div>
            </div>
  
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">Segment par défaut</label>
              <select 
                v-model="importOptions.defaultSegment"
                class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-orange focus:border-orange"
              >
                <option value="new">Nouveau client</option>
                <option value="regular">Client régulier</option>
                <option value="vip">Client VIP</option>
                <option value="inactive">Client inactif</option>
              </select>
            </div>
  
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">Tags par défaut</label>
              <input 
                v-model="importOptions.defaultTags"
                type="text" 
                class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-orange focus:border-orange"
                placeholder="Ex: Import, Nouveau (séparés par des virgules)"
              >
            </div>
          </div>
  
          <!-- Aperçu des données -->
          <div v-if="previewData.length > 0" class="space-y-4">
            <h3 class="text-lg font-medium text-gray-900">Aperçu des données ({{ previewData.length }} lignes)</h3>
            
            <div class="overflow-x-auto">
              <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                  <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nom</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Email</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Téléphone</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Statut</th>
                  </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                  <tr v-for="(row, index) in previewData.slice(0, 5)" :key="index">
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ row.name }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ row.email }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ row.phone }}</td>
                    <td class="px-6 py-4 whitespace-nowrap">
                      <span :class="getValidationClass(row.valid)" class="px-2 py-1 text-xs font-medium rounded-full">
                        {{ row.valid ? 'Valide' : 'Erreur' }}
                      </span>
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>
            
            <div v-if="previewData.length > 5" class="text-sm text-gray-500 text-center">
              ... et {{ previewData.length - 5 }} autres lignes
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
              @click="processImport"
              :disabled="!selectedFile || previewData.length === 0"
              class="flex-1 bg-orange text-white py-2 px-4 rounded-lg hover:bg-orange-600 transition-colors disabled:opacity-50 disabled:cursor-not-allowed"
            >
              Importer {{ previewData.length }} clients
            </button>
          </div>
        </div>
      </div>
    </div>
  </template>
  
  <script setup>
  import { ref, reactive } from 'vue'
  
  const emit = defineEmits(['close', 'import'])
  
  const selectedFile = ref(null)
  const previewData = ref([])
  
  const importOptions = reactive({
    skipDuplicates: true,
    updateExisting: false,
    sendWelcomeEmail: false,
    defaultSegment: 'new',
    defaultTags: 'Import'
  })
  
  const handleFileUpload = (event) => {
    const file = event.target.files[0]
    if (file) {
      selectedFile.value = file
      parseFile(file)
    }
  }
  
  const parseFile = (file) => {
    // Simulation du parsing du fichier
    // En réalité, vous utiliseriez une librairie comme Papa Parse pour CSV
    const mockData = [
      { name: 'Jean Dupont', email: 'jean.dupont@email.com', phone: '+225 07 12 34 56 78', valid: true },
      { name: 'Marie Martin', email: 'marie.martin@email.com', phone: '+225 05 98 76 54 32', valid: true },
      { name: 'Pierre Durand', email: 'pierre.durand@email.com', phone: '+225 01 23 45 67 89', valid: true },
      { name: 'Sophie Laurent', email: 'invalid-email', phone: '+225 07 89 01 23 45', valid: false },
      { name: 'Ahmed Koné', email: 'ahmed.kone@email.com', phone: '+225 05 67 89 01 23', valid: true }
    ]
    
    previewData.value = mockData
  }
  
  const removeFile = () => {
    selectedFile.value = null
    previewData.value = []
  }
  
  const formatFileSize = (bytes) => {
    if (bytes === 0) return '0 Bytes'
    const k = 1024
    const sizes = ['Bytes', 'KB', 'MB', 'GB']
    const i = Math.floor(Math.log(bytes) / Math.log(k))
    return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i]
  }
  
  const getValidationClass = (valid) => {
    return valid ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'
  }
  
  const downloadTemplate = () => {
    // Créer et télécharger un fichier CSV template
    const csvContent = 'nom,email,telephone,adresse,source,segment,tags,notes\nJean Dupont,jean.dupont@email.com,+225 07 12 34 56 78,Cocody Abidjan,Site web,new,Nouveau,Client potentiel'
    const blob = new Blob([csvContent], { type: 'text/csv' })
    const url = window.URL.createObjectURL(blob)
    const a = document.createElement('a')
    a.href = url
    a.download = 'modele_import_clients.csv'
    a.click()
    window.URL.revokeObjectURL(url)
  }
  
  const processImport = () => {
    const importData = {
      file: selectedFile.value,
      data: previewData.value,
      options: importOptions
    }
    emit('import', importData)
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
  