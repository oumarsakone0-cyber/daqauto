<template>
    <div class="order-modal-overlay" @click.self="$emit('close')">
      <div class="order-modal">
        <div class="modal-header">
          <h3 class="modal-title">Confirmer votre commande</h3>
          <button class="close-btn" @click="$emit('close')">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <line x1="18" y1="6" x2="6" y2="18"></line>
              <line x1="6" y1="6" x2="18" y2="18"></line>
            </svg>
          </button>
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
                    <span>Qté: {{ variant.quantity }}</span>
                  </div>
                </div>
                <div v-else class="single-quantity">
                  Quantité: {{ quantity }}
                </div>
              </div>
              <div class="product-price">
                {{ formatFCFA(calculateSubtotal()) }}
              </div>
            </div>
          </div>
  
          <!-- Order Form -->
          <form @submit.prevent="handleSubmitOrder" class="order-form">
            <div class="form-section">
              <h4 class="section-title">Informations de contact</h4>
              
              <div class="form-group">
                <label for="customerName" class="form-label">Nom complet (optionnel)</label>
                <input 
                  type="text" 
                  id="customerName"
                  :value="orderForm.customerName"
                  @input="$emit('updateForm', 'customerName', $event.target.value)"
                  class="form-input"
                  placeholder="Votre nom complet"
                />
              </div>
              
              <div class="form-group">
                <label for="customerContact" class="form-label">Numéro de téléphone *</label>
                <input 
                  type="tel" 
                  id="customerContact"
                  :value="orderForm.customerContact"
                  @input="$emit('updateForm', 'customerContact', $event.target.value)"
                  class="form-input"
                  :class="{ 'error': formSubmitted && !orderForm.customerContact }"
                  placeholder="Ex: 07 12 34 56 78"
                  required
                />
                <div v-if="formSubmitted && !orderForm.customerContact" class="error-message">
                  Le numéro de téléphone est requis
                </div>
              </div>
            </div>
            
            <!--
            <div class="form-section">
              <h4 class="section-title">Adresse de livraison</h4>
              
              <div class="shipping-info">
                <div class="shipping-method">
                  <span class="method-name">{{ getShippingMethodName() }}</span>
                  <span class="method-cost">{{ formatFCFA(getFinalShippingCost()) }}</span>
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
              
              <div class="form-group">
                <label for="adresseComplete" class="form-label">Adresse complète *</label>
                <textarea 
                  id="adresseComplete"
                  :value="orderForm.adresse_complete"
                  @input="$emit('updateForm', 'adresse_complete', $event.target.value)"
                  class="form-textarea"
                  :class="{ 'error': formSubmitted && !orderForm.adresse_complete }"
                  placeholder="Indiquez votre adresse complète avec des points de repère"
                  rows="3"
                  required
                ></textarea>
                <div v-if="formSubmitted && !orderForm.adresse_complete" class="error-message">
                  L'adresse complète est requise
                </div>
              </div>
              
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
            </div>

            -->
  
            <!-- Order Total -->
            <div class="order-total">
              <div class="total-row">
                <span>Sous-total:</span>
                <span>{{ formatFCFA(calculateSubtotal()) }}</span>
              </div>
              <!--
              <div class="total-row">
                <span>Livraison:</span>
                <span>{{ formatFCFA(getFinalShippingCost()) }}</span>
              </div>
              -->
              <div class="total-row total">
                <span>Total:</span>
                <span>{{ formatFCFA(calculateSubtotal()) }}</span>
              </div>
            </div>
  
            <!-- Submit Button -->
            <div class="form-actions">
              <button type="button" class="cancel-btn" @click="$emit('close')">
                Annuler
              </button>
              <button 
                type="submit" 
                class="confirm-btn"
                :disabled="!canConfirmOrder || orderLoading"
                @click="handleButtonClick"
              >
                <span v-if="orderLoading" class="loading-spinner"></span>
                <span v-else>Confirmer la commande</span>
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </template>
  
  <script setup>
  import { computed, defineProps, defineEmits } from 'vue'
  
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
    console.log('📦 getTotalQuantity appelée')
    console.log('- selectedVariants:', props.selectedVariants)
    console.log('- quantity:', props.quantity)
    
    if (props.selectedVariants && props.selectedVariants.length > 0) {
      const total = props.selectedVariants.reduce((sum, variant) => sum + (variant.quantity || 0), 0)
      console.log('- Total des variantes:', total)
      return total
    }
    return props.quantity || 1
  }
  
  const calculateSubtotal = () => {
    console.log('💰 calculateSubtotal appelée')
    if (!props.product) return 0
  
    const unitPrice = getUnitPrice()
    const totalQuantity = getTotalQuantity()
  
    console.log('- Prix unitaire:', unitPrice)
    console.log('- Quantité totale:', totalQuantity)
    console.log('- Sous-total:', unitPrice * totalQuantity)
  
    return unitPrice * totalQuantity
  }
  
  // FONCTION PRINCIPALE - Calcul des frais de livraison
  const getFinalShippingCost = () => {
    console.log('🚛 ===== getFinalShippingCost APPELÉE =====')
    console.log('- selectedShipping:', props.selectedShipping)
    console.log('- selectedCommune:', props.selectedCommune)
    console.log('- selectedVille:', props.selectedVille)
    console.log('- product:', props.product)
    
    // Si retrait ou pas de produit
    if (!props.selectedShipping || props.selectedShipping === 'retrait' || !props.product) {
      console.log('❌ Retrait ou pas de produit')
      return 0
    }
    
    const totalQuantity = getTotalQuantity()
    console.log('📦 Quantité totale pour calcul:', totalQuantity)
    
    let shippingCost = 0
    
    if (props.selectedShipping === 'abidjan') {
      console.log('🏙️ Calcul pour Abidjan...')
      shippingCost = calculateAbidjanShipping(totalQuantity)
    } else if (props.selectedShipping === 'interieur') {
      console.log('🌍 Calcul pour Intérieur...')
      shippingCost = calculateInterieurShipping(totalQuantity)
    }
    
    console.log('✅ RÉSULTAT getFinalShippingCost:', shippingCost)
    console.log('🚛 ===== FIN getFinalShippingCost =====')
    return shippingCost
  }
  
  const calculateAbidjanShipping = (totalQuantity) => {
    console.log('🏙️ calculateAbidjanShipping pour commune:', props.selectedCommune)
    console.log('- tarifsAbidjan:', props.tarifsAbidjan)
    
    // Chercher le tarif pour la commune
    let tarif = null
    if (props.selectedCommune && props.tarifsAbidjan && props.tarifsAbidjan.length > 0) {
      tarif = props.tarifsAbidjan.find(t => t.commune === props.selectedCommune)
      console.log('📋 Tarif trouvé pour', props.selectedCommune, ':', tarif)
    }
    
    // Tarif par défaut si pas trouvé
    if (!tarif) {
      console.log('⚠️ Utilisation tarif par défaut Abidjan')
      tarif = { tarif_min: 1500, tarif_max: 3000 }
    }
    
    const result = applyProductShippingRules(tarif, totalQuantity)
    console.log('🏙️ Résultat calculateAbidjanShipping:', result)
    return result
  }
  
  const calculateInterieurShipping = (totalQuantity) => {
    console.log('🌍 calculateInterieurShipping pour ville:', props.selectedVille)
    console.log('- tarifsInterieur:', props.tarifsInterieur)
    
    // Chercher le tarif pour la ville
    let tarif = null
    if (props.selectedVille && props.tarifsInterieur && props.tarifsInterieur.length > 0) {
      tarif = props.tarifsInterieur.find(t => t.ville === props.selectedVille)
      console.log('📋 Tarif trouvé pour', props.selectedVille, ':', tarif)
    }
    
    // Tarif par défaut si pas trouvé
    if (!tarif) {
      console.log('⚠️ Utilisation tarif par défaut Intérieur')
      tarif = { tarif_min: 3000, tarif_max: 6000 }
    }
    
    const result = applyProductShippingRules(tarif, totalQuantity)
    console.log('🌍 Résultat calculateInterieurShipping:', result)
    return result
  }
  
  const applyProductShippingRules = (tarif, totalQuantity) => {
    console.log('⚙️ ===== applyProductShippingRules =====')
    console.log('- Tarif reçu:', JSON.stringify(tarif))
    console.log('- Quantité:', totalQuantity)
    console.log('- product.tp:', props.product?.tp)
    console.log('- product.qtp:', props.product?.qtp)
    
    if (!props.product || !tarif) {
      console.log('❌ Pas de produit ou tarif')
      return 0
    }
  
    const tp = Number(props.product.tp) || 1
    const qtp = Number(props.product.qtp) || 1
    
    console.log('- Type transport (tp) converti:', tp, typeof tp)
    console.log('- Quantité transportable (qtp) converti:', qtp, typeof qtp)
  
    // Déterminer le tarif de base selon le type de transport
    let baseTarif = 0
  
    const tarifMin = Number(tarif.tarif_min) || 0
    const tarifMax = Number(tarif.tarif_max) || 0
    const tarifGeneral = Number(tarif.tarif) || 0
  
    console.log('- tarif_min converti:', tarifMin, typeof tarifMin)
    console.log('- tarif_max converti:', tarifMax, typeof tarifMax)
    console.log('- tarif converti:', tarifGeneral, typeof tarifGeneral)
  
    if (tp === 1) {
      // Moto - tarif minimum
      baseTarif = tarifMin || tarifGeneral || 1500
      console.log('🏍️ Moto - tarif minimum choisi:', baseTarif)
    } else if (tp === 2) {
      // Mini-camion - tarif maximum
      baseTarif = tarifMax || tarifGeneral || 3000
      console.log('🚐 Mini-camion - tarif maximum choisi:', baseTarif)
    } else if (tp === 3) {
      // Gros camion - tarif maximum × 2
      baseTarif = (tarifMax || tarifGeneral || 3000) * 2
      console.log('🚛 Gros camion - tarif maximum × 2 choisi:', baseTarif)
    }
  
    console.log('💵 Tarif de base final:', baseTarif, typeof baseTarif)
  
    // Calculer le coût selon la règle de quantité (qtp)
    let finalCost = 0
  
    if (qtp === 1) {
      // 1 article = 1 fois le tarif, 2 articles = 2 fois le tarif, etc.
      finalCost = baseTarif * totalQuantity
      console.log(`📦 Règle qtp=1: ${baseTarif} × ${totalQuantity} = ${finalCost}`)
    } else {
      // Pour qtp > 1, tarif valide pour 1 à qtp articles
      if (totalQuantity <= qtp) {
        finalCost = baseTarif
        console.log(`📦 Règle qtp=${qtp}: quantité ${totalQuantity} <= ${qtp}, coût = ${baseTarif}`)
      } else {
        const multiplier = Math.ceil(totalQuantity / qtp)
        finalCost = baseTarif * multiplier
        console.log(`📦 Règle qtp=${qtp}: quantité ${totalQuantity} > ${qtp}, multiplier = ${multiplier}, coût = ${finalCost}`)
      }
    }
  
    console.log('✅ Coût final applyProductShippingRules:', finalCost, typeof finalCost)
    console.log('⚙️ ===== FIN applyProductShippingRules =====')
    return finalCost
  }
  
  // ===== GESTION DE LA COMMANDE =====
  
  const handleButtonClick = (event) => {
    console.log('🖱️ Clic sur le bouton confirmer')
    console.log('- Event:', event)
    console.log('- canConfirmOrder:', props.canConfirmOrder)
    console.log('- orderLoading:', props.orderLoading)
    
    // Ne pas traiter si désactivé
    if (!props.canConfirmOrder || props.orderLoading) {
      console.log('❌ Bouton désactivé, arrêt')
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
    console.log('📝 ===== DÉBUT SOUMISSION COMMANDE =====')
    console.log('- Timestamp:', new Date().toISOString())
    
    try {
      // Validation
      console.log('🔍 Validation des données...')
      const validation = validateOrderData()
      if (!validation.isValid) {
        console.error('❌ Validation échouée:', validation.errors)
        alert('Erreur: ' + validation.errors.join(', '))
        return
      }
      console.log('✅ Validation réussie')
      
      // Construction des données
      console.log('🏗️ Construction des données de commande...')
      const orderData = buildOrderData()
      console.log('📦 Données de commande construites:', orderData)
      
      // Vérification finale des frais de livraison
      console.log('🔍 Vérification finale:')
      console.log('- frais_livraison dans orderData:', orderData.frais_livraison)
      console.log('- shipping_cost dans orderData:', orderData.shipping_cost)
      console.log('- total dans orderData:', orderData.total)
      
      // Émission vers le parent
      console.log('📤 Émission vers le parent...')
      emit('confirmOrder', orderData)
      console.log('✅ Données émises avec succès')
      
    } catch (error) {
      console.error('❌ Erreur dans handleSubmitOrder:', error)
      alert('Erreur lors de la soumission: ' + error.message)
    }
    
    console.log('📝 ===== FIN SOUMISSION COMMANDE =====')
  }
  
  const buildOrderData = () => {
    console.log('🏗️ ===== DÉBUT buildOrderData =====')
    
    const subtotal = calculateSubtotal()
    const shippingCost = getFinalShippingCost() // UTILISE LE CALCUL DU MODAL
    const total = subtotal + shippingCost
    
    console.log('💰 Calculs financiers dans buildOrderData:')
    console.log('- Sous-total:', subtotal)
    console.log('- Frais livraison (getFinalShippingCost):', shippingCost)
    console.log('- Total calculé:', total)
    
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
    
    console.log('✅ Données finales buildOrderData:', orderData)
    console.log('🔍 Vérification frais dans orderData:')
    console.log('- frais_livraison:', orderData.frais_livraison)
    console.log('- shipping_cost:', orderData.shipping_cost)
    console.log('- total:', orderData.total)
    console.log('🏗️ ===== FIN buildOrderData =====')
    
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
    
    if (props.selectedShipping === 'abidjan' && !props.selectedCommune) {
      errors.push('Commune requise pour Abidjan')
    }
    
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
  
  const formatFCFA = (montant) => {
    return Number(montant || 0).toLocaleString('fr-FR', { minimumFractionDigits: 0 }) + ' FCFA'
  }
  
  // ===== FONCTIONS DE DEBUG =====
  
  const testShippingCalculation = () => {
    console.log('🧪 ===== TEST CALCUL LIVRAISON =====')
    const result = getFinalShippingCost()
    console.log('🎯 Résultat test:', result)
    console.log('🧪 ===== FIN TEST CALCUL LIVRAISON =====')
  }
  
  const testBuildOrderData = () => {
    console.log('🧪 ===== TEST BUILD ORDER DATA =====')
    const result = buildOrderData()
    console.log('🎯 Résultat test:', result)
    console.log('🧪 ===== FIN TEST BUILD ORDER DATA =====')
  }
  
  const testSubmitOrder = () => {
    console.log('🧪 ===== TEST SUBMIT ORDER =====')
    handleSubmitOrder()
    console.log('🧪 ===== FIN TEST SUBMIT ORDER =====')
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
  
  .close-btn {
    width: 32px;
    height: 32px;
    border: none;
    background: none;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    transition: background-color 0.3s ease;
  }
  
  .close-btn:hover {
    background: #f5f5f5;
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
    color: #ff4d4f;
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
  
  .form-input,
  .form-textarea {
    padding: 10px 12px;
    border: 1px solid #d9d9d9;
    border-radius: 6px;
    font-size: 14px;
    transition: border-color 0.3s ease;
  }
  
  .form-input:focus,
  .form-textarea:focus {
    outline: none;
    border-color: #1890ff;
    box-shadow: 0 0 0 2px rgba(24, 144, 255, 0.1);
  }
  
  .form-input.error,
  .form-textarea.error {
    border-color: #ff4d4f;
  }
  
  .form-textarea {
    resize: vertical;
    min-height: 80px;
  }
  
  .error-message {
    font-size: 12px;
    color: #ff4d4f;
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
  
  .method-cost {
    font-size: 14px;
    font-weight: 600;
    color: #1890ff;
  }
  
  .shipping-destination {
    font-size: 13px;
    color: #666;
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
    color: #ff4d4f;
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
  
  .cancel-btn,
  .confirm-btn {
    flex: 1;
    padding: 12px 24px;
    border-radius: 6px;
    font-size: 14px;
    font-weight: 500;
    cursor: pointer;
    transition: all 0.3s ease;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
  }
  
  .cancel-btn {
    background: #fff;
    color: #666;
    border: 1px solid #d9d9d9;
  }
  
  .cancel-btn:hover {
    border-color: #1890ff;
    color: #1890ff;
  }
  
  .confirm-btn {
    background: #ff4d4f;
    color: #fff;
    border: none;
  }
  
  .confirm-btn:hover:not(:disabled) {
    background: #ff7875;
  }
  
  .confirm-btn:disabled {
    opacity: 0.6;
    cursor: not-allowed;
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
  