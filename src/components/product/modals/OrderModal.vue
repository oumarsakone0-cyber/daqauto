<template>
    <div class="order-modal-overlay" @click.self="$emit('close')">
      <div class="order-modal">
        <div class="modal-header">
          <h3 class="modal-title">Confirm your order</h3>
          
          <XIcon @click="$emit('close')" class="w-7 h-7 text-gray-500 cursor-pointer" />
        </div>
        
        <!-- Debug Panel -->
        <div v-if="showDebug" class="debug-panel">
          <h4>🔍 Debug - Frais de livraison</h4>
          <div class="debug-info">
            <p><strong>selectedShipping:</strong> {{ selectedShipping }}</p>
            <p><strong>selectedCommune:</strong> {{ selectedCommune }}</p>
            <p><strong>selectedVille:</strong> {{ selectedVille }}</p>
            <p><strong>currentShippingCost (parent):</strong> {{ currentShippingCost }}</p>
            <p><strong>getFinalShippingCost():</strong> {{ getFinalShippingCost() }}</p>
            <p><strong>UTILISÉ DANS COMMANDE:</strong> <span class="highlight">{{ getFinalShippingCost() }}</span></p>
            <p><strong>product.tp:</strong> {{ product?.tp }}</p>
            <p><strong>product.qtp:</strong> {{ product?.qtp }}</p>
            <p><strong>Quantité totale:</strong> {{ getTotalQuantity() }}</p>
          </div>
          <div class="debug-buttons">
            <button @click="testShippingCalculation" class="debug-btn">Test Calcul Livraison</button>
            <button @click="testBuildOrderData" class="debug-btn">Test BuildOrderData</button>
            <button @click="testSubmitOrder" class="debug-btn">Test Submit</button>
          </div>
        </div>
        
        <div class="modal-body">
          <!-- Product Summary -->
          <div class="order-summary">
            <div class="product-item">
              <div class="product-image">
                <img :src="currentImage" :alt="product.name" />
              </div>
              <div class="product-details">
                <h4 class="product-name">{{ product.name }}</h4>
                <div class="product-variants" v-if="selectedVariants.length > 0">
                  <div v-for="(variant, index) in selectedVariants" :key="index" class="variant-item">
                    <span v-if="variant.colorIndex !== null">{{ getColorName(variant.colorIndex) }}</span>
                    <span v-if="variant.sizeIndex !== null">{{ getSizeName(variant.sizeIndex) }}</span>
                    <span>Qty: {{ variant.quantity }}</span>
                  </div>
                </div>
                <div v-else class="single-quantity">
                  Quantity: {{ quantity }}
                </div>
              </div>
              <div class="product-price primary-color">
                {{ formatPrice(calculateSubtotal()) }} 
              </div>
            </div>
          </div>
  
          <!-- Order Form -->
          <form @submit.prevent="handleSubmitOrder" class="order-form">
            <div class="form-section">
              <h4 class="section-title">Contact informations</h4>
              
              <div class="form-group">
                <label for="customerName" class="form-label">Full Name (optional)</label>
                <input 
                  type="text" 
                  id="customerName"
                  :value="orderForm.customerName"
                  @input="$emit('updateForm', 'customerName', $event.target.value)"
                  class="input-style"
                  placeholder="Your full name"
                />
              </div>
              
              <div class="form-group">
                <label for="customerContact" class="form-label">Phone Number <span class="error-color">*</span></label>
                <input 
                  type="tel" 
                  id="customerContact"
                  :value="orderForm.customerContact"
                  @input="$emit('updateForm', 'customerContact', $event.target.value)"
                  class="input-style"
                  :class="{ 'error': formSubmitted && !orderForm.customerContact }"
                  placeholder="Ex: 07 12 34 56 78"
                  required
                />
                <div v-if="formSubmitted && !orderForm.customerContact" class="error-color">
                  Phone number is required
                </div>
              </div>
            </div>
            
            
            <div class="form-section">
              <h4 class="section-title">Delivery address</h4>
              <!--
              <div class="shipping-info">
                <div class="shipping-method">
                  <span class="method-name">{{ getShippingMethodName() }}</span>
                  <span class="method-cost">{{ formatPrice(getFinalShippingCost()) }}</span>
                </div>
                <div class="shipping-destination">
                  {{ getShippingDestination() }}
                </div>
                <div v-if="product?.tp || product?.qtp" class="shipping-rules">
                  <small>
                    Transport: {{ getTransportType() }} | 
                    Tarif pour {{ product?.qtp || 1 }} article(s)
                  </small>
                </div>
              </div>
              -->
              <div class="form-group">
                <label for="adresseComplete" class="form-label">Full adresse <span class="error-color">*</span></label>
                <textarea 
                  id="adresseComplete"
                  :value="orderForm.adresse_complete"
                  @input="$emit('updateForm', 'adresse_complete', $event.target.value)"
                  class="input-style"
                  :class="{ 'error': formSubmitted && !orderForm.adresse_complete }"
                  placeholder="Provide your full address with landmarks"
                  rows="3"
                  required
                ></textarea>
                <div v-if="formSubmitted && !orderForm.adresse_complete" class="error-color">
                  Address is required
                </div>
              </div>
               <!--
              <div class="form-group">
                <label for="instructionsLivraison" class="form-label">Instructions de livraison (optionnel)</label>
                <textarea 
                  id="instructionsLivraison"
                  :value="orderForm.instructions_livraison"
                  @input="$emit('updateForm', 'instructions_livraison', $event.target.value)"
                  class="form-textarea"
                  placeholder="Instructions spéciales pour la livraison"
                  rows="2"
                ></textarea>
              </div>
              -->
            </div>

            
  
            <!-- Order Total -->
            <div class="order-total">
              <div class="total-row text-gray-500">
                <span>Subtotal:</span>
                <span>{{ formatPrice(calculateSubtotal()) }}</span>
              </div>
<!--               
              <div class="total-row">
                <span>Livraison:</span>
                <span>{{ formatPrice(getFinalShippingCost()) }}</span>
              </div>
              -->
              <div class="total-row total primary-color">
                <span>Total:</span>
                <span>{{ formatPrice(calculateSubtotal()) }} <span style="font-size: 13px; color: gray">( FOB )</span></span>
              </div>
            </div>
  
            <!-- Submit Button -->
            <div class="form-actions">
              <button type="button" class="btn-gray flex-1" @click="$emit('close')">
                Cancel
              </button>
              <button 
                type="submit" 
                class="btn-degrade-orange flex-1"
                :disabled="!canConfirmOrder || orderLoading"
                @click="handleButtonClick"
              >
                <span v-if="orderLoading" class="loading-spinner"></span>
                <span v-else>Confirm order</span>
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </template>
  
  <script setup>
  import { computed, defineProps, defineEmits } from 'vue'
  import { formatPrice } from '../../../services/formatPrice'
  import { 
  X as XIcon,} from 'lucide-vue-next'
  const props = defineProps({
    product: Object,
    selectedVariants: Array,
    quantity: Number,
    currentImage: String,
    selectedShipping: String,
    selectedCommune: String,
    selectedVille: String,
    tarifsAbidjan: Array,
    tarifsInterieur: Array,
    currentShippingCost: Number,
    orderForm: Object,
    formSubmitted: Boolean,
    orderLoading: Boolean,
    canConfirmOrder: Boolean,
    showDebug: {
      type: Boolean,
      default: false
    }
  })
  
  const emit = defineEmits([
    'close',
    'confirmOrder',
    'updateForm'
  ])
  
  // ===== FONCTIONS PRINCIPALES =====
  
  const getTotalQuantity = () => {
    
    if (props.selectedVariants && props.selectedVariants.length > 0) {
      const total = props.selectedVariants.reduce((sum, variant) => sum + (variant.quantity || 0), 0)
      return total
    }
    return props.quantity || 1
  }
  
  const calculateSubtotal = () => {
    if (!props.product) return 0
  
    const unitPrice = getUnitPrice()
    const totalQuantity = getTotalQuantity()
  
  
    return unitPrice * totalQuantity
  }
  
  // FONCTION PRINCIPALE - Calcul des frais de livraison
  const getFinalShippingCost = () => {
    
    // Si retrait ou pas de produit
    if (!props.selectedShipping || props.selectedShipping === 'retrait' || !props.product) {
      return 0
    }
    
    const totalQuantity = getTotalQuantity()
    
    let shippingCost = 0
    
    if (props.selectedShipping === 'abidjan') {
      shippingCost = calculateAbidjanShipping(totalQuantity)
    } else if (props.selectedShipping === 'interieur') {
      shippingCost = calculateInterieurShipping(totalQuantity)
    }
    
    return shippingCost
  }
  
  const calculateAbidjanShipping = (totalQuantity) => {
    
    // Chercher le tarif pour la commune
    let tarif = null
    if (props.selectedCommune && props.tarifsAbidjan && props.tarifsAbidjan.length > 0) {
      tarif = props.tarifsAbidjan.find(t => t.commune === props.selectedCommune)
    }
    
    // Tarif par défaut si pas trouvé
    if (!tarif) {
      tarif = { tarif_min: 1500, tarif_max: 3000 }
    }
    
    const result = applyProductShippingRules(tarif, totalQuantity)
    return result
  }
  
  const calculateInterieurShipping = (totalQuantity) => {
    
    // Chercher le tarif pour la ville
    let tarif = null
    if (props.selectedVille && props.tarifsInterieur && props.tarifsInterieur.length > 0) {
      tarif = props.tarifsInterieur.find(t => t.ville === props.selectedVille)
    }
    
    // Tarif par défaut si pas trouvé
    if (!tarif) {
      tarif = { tarif_min: 3000, tarif_max: 6000 }
    }
    
    const result = applyProductShippingRules(tarif, totalQuantity)
    return result
  }
  
  const applyProductShippingRules = (tarif, totalQuantity) => {
    
    if (!props.product || !tarif) {
      return 0
    }
  
    const tp = Number(props.product.tp) || 1
    const qtp = Number(props.product.qtp) || 1
    
  
    // Déterminer le tarif de base selon le type de transport
    let baseTarif = 0
  
    const tarifMin = Number(tarif.tarif_min) || 0
    const tarifMax = Number(tarif.tarif_max) || 0
    const tarifGeneral = Number(tarif.tarif) || 0
  
  
    if (tp === 1) {
      // Moto - tarif minimum
      baseTarif = tarifMin || tarifGeneral || 1500
    } else if (tp === 2) {
      // Mini-camion - tarif maximum
      baseTarif = tarifMax || tarifGeneral || 3000
    } else if (tp === 3) {
      // Gros camion - tarif maximum × 2
      baseTarif = (tarifMax || tarifGeneral || 3000) * 2
    }
  
  
    // Calculer le coût selon la règle de quantité (qtp)
    let finalCost = 0
  
    if (qtp === 1) {
      // 1 article = 1 fois le tarif, 2 articles = 2 fois le tarif, etc.
      finalCost = baseTarif * totalQuantity
    } else {
      // Pour qtp > 1, tarif valide pour 1 à qtp articles
      if (totalQuantity <= qtp) {
        finalCost = baseTarif
      } else {
        const multiplier = Math.ceil(totalQuantity / qtp)
        finalCost = baseTarif * multiplier
      }
    }
  
    return finalCost
  }
  
  // ===== GESTION DE LA COMMANDE =====
  
  const handleButtonClick = (event) => {
    
    // Ne pas traiter si désactivé
    if (!props.canConfirmOrder || props.orderLoading) {
      return
    }
    
    // Empêcher la soumission par défaut si c'est un clic direct
    if (event) {
      event.preventDefault()
    }
    
    // Appeler la fonction de soumission
    handleSubmitOrder()
  }
  
  const handleSubmitOrder = async () => {
    
    try {
      // Validation
      const validation = validateOrderData()
      if (!validation.isValid) {
        console.error('❌ Validation échouée:', validation.errors)
        alert('Erreur: ' + validation.errors.join(', '))
        return
      }
      
      // Construction des données
      const orderData = buildOrderData()
      
      // Émission vers le parent
      emit('confirmOrder', orderData)
      
    } catch (error) {
      alert('Erreur lors de la soumission: ' + error.message)
    }
    
  }
  
  const buildOrderData = () => {
    
    const subtotal = calculateSubtotal()
    const shippingCost = getFinalShippingCost() // UTILISE LE CALCUL DU MODAL
    const total = subtotal
    
    const orderData = {
      // Informations commande
      numero_commande: `CMD-${Date.now()}-${Math.random().toString(36).substr(2, 9).toUpperCase()}`,
      statut: 'en_attente',
      
      // Produit
      produit_id: props.product.id,
      produit_nom: props.product.name,
      produit_prix: getUnitPrice(),
      quantite: getTotalQuantity(),
      
      // Boutique
      boutique_id: props.product.boutique_id,
      boutique_nom: props.product.boutique_nom,
      
      // Client
      client_nom: props.orderForm.customerName || '',
      client_telephone: props.orderForm.customerContact || '',
      
      // Livraison
      type_livraison: props.selectedShipping,
      ville: props.selectedShipping === 'abidjan' ? 'Abidjan' : props.selectedVille,
      commune: props.selectedCommune || '',
      adresse_complete: props.orderForm.adresse_complete || '',
      instructions_livraison: props.orderForm.instructions_livraison || '',
      
      // FRAIS DE LIVRAISON - TOUS LES FORMATS POSSIBLES
      frais_livraison: shippingCost,
      shipping_cost: shippingCost,
      delivery_cost: shippingCost,
      cout_livraison: shippingCost,
      livraison_prix: shippingCost,
      
      // Montants
      sous_total: subtotal,
      total: total,
      
      // Variantes
      options_variantes: props.selectedVariants.length > 0 ? props.selectedVariants.map(variant => ({
        quantite: variant.quantity,
        couleur: variant.colorIndex !== null ? {
          nom: getColorName(variant.colorIndex),
          hex: props.product.colors[variant.colorIndex]?.hex || '#000000'
        } : null,
        taille: variant.sizeIndex !== null ? getSizeName(variant.sizeIndex) : null
      })) : null
    }
    
    
    return orderData
  }
  
  const validateOrderData = () => {
    const errors = []
    
    if (!props.orderForm.customerContact) {
      errors.push('Numéro de téléphone requis')
    }
    
    if (!props.orderForm.adresse_complete) {
      errors.push('Adresse complète requise')
    }
    
    if (!props.selectedShipping) {
      errors.push('Mode de livraison requis')
    }
    
   // if (props.selectedShipping === 'abidjan' && !props.selectedCommune) {
    //  errors.push('Commune requise pour Abidjan')
   // }
    
    if (props.selectedShipping === 'interieur' && !props.selectedVille) {
      errors.push('Ville requise pour l\'intérieur')
    }
    
    return {
      isValid: errors.length === 0,
      errors
    }
  }
  
  // ===== FONCTIONS UTILITAIRES =====
  
  const getUnitPrice = () => {
    if (!props.product) return 0
    
    let price = props.product.unit_price || 0
    const totalQuantity = getTotalQuantity()
    
    if (props.product.wholesale_price && 
        props.product.wholesale_min_qty && 
        totalQuantity >= props.product.wholesale_min_qty) {
      price = props.product.wholesale_price
    }
    
    return price
  }
  
  const getColorName = (colorIndex) => {
    return props.product?.colors?.[colorIndex]?.name || 'Couleur'
  }
  
  const getSizeName = (sizeIndex) => {
    return props.product?.sizes?.[sizeIndex]?.name || 'Taille'
  }
  
  const getShippingMethodName = () => {
    const tp = props.product?.tp || 1
    const methods = { 
      1: 'Livraison à moto', 
      2: 'Livraison mini-camion', 
      3: 'Livraison gros camion' 
    }
    
    switch (props.selectedShipping) {
      case 'abidjan':
        return methods[tp] + ' - Abidjan'
      case 'interieur':
        return methods[tp] + ' - Intérieur'
      case 'retrait':
        return 'Retrait à Adjamé'
      default:
        return 'Livraison'
    }
  }
  
  const getShippingDestination = () => {
    if (props.selectedShipping === 'abidjan') {
      return props.selectedCommune ? `${props.selectedCommune}, Abidjan` : 'Abidjan'
    } else if (props.selectedShipping === 'interieur') {
      return props.selectedVille || 'Intérieur du pays'
    } else if (props.selectedShipping === 'retrait') {
      return 'Marché d\'Adjamé'
    }
    return 'À définir'
  }
  
  const getTransportType = () => {
    const tp = props.product?.tp || 1
    const types = { 1: 'Moto', 2: 'Mini-camion', 3: 'Gros camion' }
    return types[tp] || 'Standard'
  }
  
  // ===== FONCTIONS DE DEBUG =====
  
  const testShippingCalculation = () => {
    const result = getFinalShippingCost()
  }
  
  const testBuildOrderData = () => {
    const result = buildOrderData()
  }
  
  const testSubmitOrder = () => {
    handleSubmitOrder()
  }
  </script>
  
  <style scoped>
  .order-modal-overlay {
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(0, 0, 0, 0.5);
    z-index: 1000;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 20px;
  }
  
  .order-modal {
    background: #fff;
    border-radius: 12px;
    width: 100%;
    max-width: 600px;
    max-height: 90vh;
    overflow: hidden;
    display: flex;
    flex-direction: column;
  }
  
  .modal-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 20px;
    border-bottom: 1px solid #e8e8e8;
    flex-shrink: 0;
  }
  
  .modal-title {
    font-size: 18px;
    font-weight: 600;
    color: #333;
    margin: 0;
  }
  .debug-panel {
    background: #f0f8ff;
    border-bottom: 1px solid #b3d9ff;
    padding: 16px;
    font-size: 12px;
  }
  
  .debug-panel h4 {
    margin: 0 0 12px 0;
    color: #1890ff;
  }
  
  .debug-info p {
    margin: 4px 0;
    font-family: monospace;
  }
  
  .highlight {
    background: #ffeb3b;
    padding: 2px 4px;
    border-radius: 3px;
    font-weight: bold;
  }
  
  .debug-buttons {
    display: flex;
    gap: 8px;
    margin-top: 12px;
  }
  
  .debug-btn {
    background: #1890ff;
    color: white;
    border: none;
    padding: 6px 10px;
    border-radius: 4px;
    cursor: pointer;
    font-size: 11px;
  }
  
  .debug-btn:hover {
    background: #40a9ff;
  }
  
  .modal-body {
    flex: 1;
    overflow-y: auto;
    padding: 20px;
  }
  
  .order-summary {
    background: #fafafa;
    border-radius: 8px;
    padding: 16px;
    margin-bottom: 24px;
  }
  
  .product-item {
    display: flex;
    gap: 12px;
    align-items: flex-start;
  }
  
  .product-image {
    width: 60px;
    height: 60px;
    border-radius: 8px;
    overflow: hidden;
    flex-shrink: 0;
  }
  
  .product-image img {
    width: 100%;
    height: 100%;
    object-fit: cover;
  }
  
  .product-details {
    flex: 1;
  }
  
  .product-name {
    font-size: 14px;
    font-weight: 500;
    color: #333;
    margin: 0 0 8px 0;
    line-height: 1.3;
  }
  
  .product-variants {
    display: flex;
    flex-direction: column;
    gap: 4px;
  }
  
  .variant-item {
    font-size: 12px;
    color: #666;
    display: flex;
    gap: 8px;
  }
  
  .single-quantity {
    font-size: 12px;
    color: #666;
  }
  
  .product-price {
    font-size: 16px;
    font-weight: 700;
    text-align: right;
  }
  
  .order-form {
    display: flex;
    flex-direction: column;
    gap: 24px;
  }
  
  .form-section {
    display: flex;
    flex-direction: column;
    gap: 16px;
  }
  
  .section-title {
    font-size: 16px;
    font-weight: 600;
    color: #333;
    margin: 0;
    padding-bottom: 8px;
    border-bottom: 1px solid #e8e8e8;
  }
  
  .form-group {
    display: flex;
    flex-direction: column;
    gap: 6px;
  }
  
  .form-label {
    font-size: 14px;
    font-weight: 500;
    color: #333;
  }
  .shipping-info {
    background: #f0f8ff;
    border: 1px solid #b3d9ff;
    border-radius: 6px;
    padding: 12px;
    margin-bottom: 16px;
  }
  
  .shipping-method {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 4px;
  }
  
  .method-name {
    font-size: 14px;
    font-weight: 500;
    color: #333;
  }
  
  .shipping-rules {
    margin-top: 4px;
    font-size: 11px;
    color: #888;
    font-style: italic;
  }
  
  .order-total {
    background: #fafafa;
    border-radius: 8px;
    padding: 16px;
    border: 1px solid #e8e8e8;
  }
  
  .total-row {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 4px 0;
    font-size: 14px;
  }
  
  .total-row.total {
    font-size: 16px;
    font-weight: 700;
    padding-top: 8px;
    border-top: 1px solid #e8e8e8;
    margin-top: 8px;
  }
  
  .form-actions {
    display: flex;
    gap: 12px;
    padding-top: 16px;
    border-top: 1px solid #e8e8e8;
  }
  
  .loading-spinner {
    width: 16px;
    height: 16px;
    border: 2px solid rgba(255, 255, 255, 0.3);
    border-top: 2px solid #fff;
    border-radius: 50%;
    animation: spin 1s linear infinite;
  }
  
  @keyframes spin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
  }
  
  @media (max-width: 768px) {
    .order-modal {
      margin: 0;
      border-radius: 12px 12px 0 0;
      position: fixed;
      bottom: 0;
      left: 0;
      right: 0;
      max-height: 90vh;
    }
    
    .form-actions {
      flex-direction: column;
    }
  }
  </style>
  