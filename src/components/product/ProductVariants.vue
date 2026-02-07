<template>
    <div class="product-variants">
      <div class="variants-header">
        <div class="variants-label">Variantes du produit:</div>
        <button class="btn-degrade-orange" @click="$emit('addVariant')" v-if="selectedVariants.length < 5">
          <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <line x1="12" y1="5" x2="12" y2="19"></line>
            <line x1="5" y1="12" x2="19" y2="12"></line>
          </svg>
          Ajouter une variante
        </button>
      </div>
      
      <div class="variants-list">
        <div v-for="(variant, variantIndex) in selectedVariants" :key="variant.id" class="variant-item">
          <div class="variant-header">
            <span class="variant-number">Variante {{ variantIndex + 1 }}</span>
            
            <XIcon v-if="selectedVariants.length > 1"  @click="$emit('removeVariant', variantIndex)" class="w-7 h-7 text-gray-500 cursor-pointer" />
          </div>
          
          <div class="variant-options">
            <!-- Sélection de taille -->
            <div v-if="productSizes.length > 0" class="variant-option-group">
              <label class="variant-option-label">Taille:</label>
              <select 
                :value="variant.sizeIndex" 
                @change="$emit('updateVariantSize', { variantIndex, sizeIndex: parseInt($event.target.value) })"
                class="input-style"
              >
                <option v-for="(size, sizeIndex) in productSizes" :key="sizeIndex" :value="sizeIndex">
                  {{ size.name.name || size.name }}
                </option>
              </select>
            </div>
            
            <!-- Sélection de couleur -->
            <div v-if="productColors.length > 0" class="variant-option-group">
              <label class="variant-option-label">Couleur:</label>
              <div style="display: flex;">
                <div style="width: 70%;">
                  <select
                    style="width: 100%;" 
                    :value="variant.colorIndex" 
                    @change="$emit('updateVariantColor', { variantIndex, colorIndex: parseInt($event.target.value) })"
                    class="input-style"
                  >
                    <option v-for="(color, colorIndex) in productColors" :key="colorIndex" :value="colorIndex">
                      {{ color.name }}
                    </option>
                  </select>
                  </div>
                  <div v-if="variant.colorIndex !== null" class="selected-color-preview" style="float: right; width: 25%; padding-left: 5%;">
                    <div class="color-swatch" :style="{ backgroundColor: productColors[variant.colorIndex].hex_value }"></div>
                </div>
              </div>
              
            </div>
            
            <!-- Quantité -->
            <div class="variant-option-group" style="margin-top: -10px;">
              <label class="variant-option-label">Quantité:</label>
              <div class="variant-quantity-controls">
                <button class="quantity-btn" @click="decreaseQuantity(variantIndex)" :disabled="variant.quantity <= 1">-</button>
                <input 
                  type="number" 
                  :value="variant.quantity" 
                  @input="$emit('updateVariantQuantity', { variantIndex, quantity: parseInt($event.target.value) })"
                  min="1" 
                  class="quantity-input"
                >
                <button class="quantity-btn" @click="increaseQuantity(variantIndex)">+</button>
              </div>
            </div>
          </div>
        </div>
      </div>
  
      <!-- Affichage des frais de livraison 
      <div v-if="selectedVariants.length > 0" class="shipping-cost-display">
        <div class="shipping-cost-header">
          <span class="shipping-cost-label">Frais de livraison:</span>
          <span class="primary-color">{{ formatFCFA(calculateShippingCost()) }}</span>
        </div>
        <div class="shipping-cost-details">
          <span class="shipping-method">{{ getShippingMethodName() }}</span>
          <span class="shipping-destination">{{ getShippingDestination() }}</span>
        </div>
      </div>
      -->
    </div>
  </template>
  
  <script setup>
  import { defineProps, defineEmits, onMounted, ref } from 'vue'
  import { 
  X as XIcon,} from 'lucide-vue-next';
  
  const props = defineProps({
    productColors: Array,
    productSizes: Array,
    selectedVariants: Array,
    // Nouvelles props pour les frais de livraison
    product: Object,
    selectedShipping: String,
    selectedCommune: String,
    selectedVille: String,
    tarifsAbidjan: Array,
    tarifsInterieur: Array
  })
  
  const emit = defineEmits([
    'addVariant',
    'removeVariant',
    'updateVariantSize',
    'updateVariantColor',
    'updateVariantQuantity'
  ])
  
  // Créer une variante par défaut au montage du composant
  onMounted(() => {
    if (props.selectedVariants.length === 0) {
      emit('addVariant')
    }
  })
  
  const decreaseQuantity = (variantIndex) => {
    const variant = props.selectedVariants[variantIndex]
    if (variant.quantity > 1) {
      emit('updateVariantQuantity', { variantIndex, quantity: variant.quantity - 1 })
    }
  }
  
  const increaseQuantity = (variantIndex) => {
    const variant = props.selectedVariants[variantIndex]
    emit('updateVariantQuantity', { variantIndex, quantity: variant.quantity + 1 })
  }
  
  const calculateShippingCost = () => {
    
    if (!props.product || props.selectedShipping === 'retrait') return 0
    
    const totalQuantity = props.selectedVariants.reduce((sum, variant) => sum + variant.quantity, 0)
    
    // Logique de calcul avec tp et qtp
    const tp = props.product.tp || 1
    const qtp = props.product.qtp || 1
    
    let tarif = getTarif()
    
    let baseTarif = getBaseTarif(tarif, tp)
    
    let finalCost = calculateFinalCost(baseTarif, totalQuantity, qtp)
    
    return finalCost
  }
  
  const getTarif = () => {
    if (props.selectedShipping === 'abidjan') {
      if (props.selectedCommune && props.tarifsAbidjan?.length > 0) {
        const foundTarif = props.tarifsAbidjan.find(t => t.commune === props.selectedCommune)
        if (foundTarif) return foundTarif
      }
      // Tarif par défaut Abidjan
      return { tarif_min: 1500, tarif_max: 3000 }
    } else if (props.selectedShipping === 'interieur') {
      if (props.selectedVille && props.tarifsInterieur?.length > 0) {
        const foundTarif = props.tarifsInterieur.find(t => t.ville === props.selectedVille)
        if (foundTarif) return foundTarif
      }
      // Tarif par défaut intérieur
      return { tarif_min: 3000, tarif_max: 6000 }
    }
    
    // Fallback
    return { tarif_min: 1500, tarif_max: 3000 }
  }
  
  const getBaseTarif = (tarif, tp) => {
    if (tp === 1) return tarif.tarif_min || tarif.tarif || 0
    if (tp === 2) return tarif.tarif_max || tarif.tarif || 0
    if (tp === 3) return (tarif.tarif_max || tarif.tarif || 0) * 2
    return tarif.tarif_min || tarif.tarif || 0
  }
  
  const calculateFinalCost = (baseTarif, totalQuantity, qtp) => {
    if (qtp === 1) {
      return baseTarif * totalQuantity
    } else if (qtp === 2) {
      return totalQuantity <= 2 ? baseTarif : baseTarif * Math.ceil(totalQuantity / 2)
    } else {
      return totalQuantity <= qtp ? baseTarif : baseTarif * Math.ceil(totalQuantity / qtp)
    }
  }
  
  const getShippingMethodName = () => {
    const tp = props.product?.tp || 1
    const methods = { 1: 'Livraison à moto', 2: 'Livraison mini-camion', 3: 'Livraison gros camion' }
    return methods[tp] || 'Livraison'
  }
  
  const getShippingDestination = () => {
    if (props.selectedShipping === 'abidjan') return props.selectedCommune ? `${props.selectedCommune}, Abidjan` : 'Abidjan'
    if (props.selectedShipping === 'interieur') return props.selectedVille || 'Intérieur du pays'
    return 'Retrait à Adjamé'
  }
  
  const formatFCFA = (montant) => {
    return Number(montant).toLocaleString('fr-FR', { minimumFractionDigits: 0 }) + ' FCFA'
  }
  </script>
  
  <style scoped>
  .product-variants {
    margin-bottom: 24px;
  }
  
  .variants-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 16px;
  }
  
  .variants-label {
    font-size: 16px;
    font-weight: 600;
    color: #333;
  }
  
  .variants-list {
    display: flex;
    flex-direction: column;
    gap: 16px;
  }
  
  .variant-item {
    padding: 16px;
    border: 2px solid #f0f0f0;
    border-radius: 8px;
    background: #fafafa;
  }
  
  .variant-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 12px;
  }
  
  .variant-number {
    font-size: 14px;
    font-weight: 600;
    color: #666;
  }
  
  .variant-options {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
    gap: 12px;
    align-items: end;
  }
  
  .variant-option-group {
    display: flex;
    flex-direction: column;
    gap: 6px;
  }
  
  .variant-option-label {
    font-size: 13px;
    font-weight: 500;
    color: #666;
  }
  
  .selected-color-preview {
    display: flex;
    align-items: center;
    margin-top: 4px;
  }
  
  .color-swatch {
    width: 20px;
    height: 20px;
    border-radius: 50%;
    border: 1px solid #d9d9d9;
  }
  
  .variant-quantity-controls {
    display: flex;
    align-items: center;
  }
  
  .variant-quantity-controls .quantity-btn {
    width: 32px;
    height: 32px;
    border: 1px solid #d9d9d9;
    background: #fff;
    color: #333;
    font-size: 16px;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
  }
  
  .variant-quantity-controls .quantity-btn:first-child {
    border-radius: 6px 0 0 6px;
  }
  
  .variant-quantity-controls .quantity-btn:last-child {
    border-radius: 0 6px 6px 0;
  }
  
  .variant-quantity-controls .quantity-input {
    width: 65px;
    height: 32px;
    border: 1px solid #d9d9d9;
    color: #333;
    border-left: none;
    border-right: none;
    text-align: center;
    font-size: 14px;
  }
  
  .shipping-cost-display {
    margin-top: 16px;
    padding: 12px;
    background: #f0f8ff;
    border: 1px solid #b3d9ff;
    border-radius: 6px;
  }
  
  .shipping-cost-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 4px;
  }
  
  .shipping-cost-label {
    font-size: 14px;
    font-weight: 500;
    color: #333;
  }
  
  .shipping-cost-details {
    display: flex;
    justify-content: space-between;
    font-size: 12px;
    color: #666;
  }
  </style>
  