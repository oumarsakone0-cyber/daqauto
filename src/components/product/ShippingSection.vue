<template>
    <div class="shipping-section">
      <div class="shipping-header">
        <div class="shipping-title">Expedition:</div>
        <div class="shipping-location">
          <span>Vers</span>
          <strong>{{ getDestinationLabel() }}</strong>
          <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <polyline points="6 9 12 15 18 9"></polyline>
          </svg>
        </div>
      </div>
      
      <div class="shipping-options">
        <div class="shipping-option " :class="{ 'selected': selectedShipping === 'abidjan' }" @click="selectShipping('abidjan')">
          <div class="shipping-method">
            <svg  width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#fe7900" stroke-width="2">
              <rect x="1" y="3" width="15" height="13"></rect>
              <polygon points="16 8 20 8 23 11 23 16 16 16 16 8"></polygon>
              <circle cx="5.5" cy="18.5" r="2.5"></circle>
              <circle cx="18.5" cy="18.5" r="2.5"></circle>
            </svg>
            <span>Livraison Abidjan</span>
          </div>
          <div class="shipping-cost">{{ getShippingCostDisplay('abidjan') }}</div>
          <div class="shipping-time">{{ getShippingTime('abidjan') }}</div>
          <div class="shipping-radio"></div>
        </div>
  
        <div class="shipping-option" :class="{ 'selected': selectedShipping === 'interieur' }" @click="selectShipping('interieur')">
          <div class="shipping-method">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#Fe7900" stroke-width="2">
              <circle cx="12" cy="12" r="10"></circle>
              <polyline points="12 6 12 12 16 14"></polyline>
            </svg>
            <span>Livraison Intérieur</span>
          </div>
          <div class="shipping-cost">{{ getShippingCostDisplay('interieur') }}</div>
          <div class="shipping-time">{{ getShippingTime('interieur') }}</div>
          <div class="shipping-radio"></div>
        </div>
  
        <div class="shipping-option" :class="{ 'selected': selectedShipping === 'retrait' }" @click="selectShipping('retrait')">
          <div class="shipping-method">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#fe7900" stroke-width="2">
              <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path>
              <polyline points="9 22 9 12 15 12 15 22"></polyline>
            </svg>
            <span>Retrait à Adjamé</span>
          </div>
          <div class="shipping-cost">{{ getShippingCostDisplay('retrait') }}</div>
          <div class="shipping-time">{{ getShippingTime('retrait') }}</div>
          <div class="shipping-radio"></div>
        </div>
      </div>
  
      <!-- Sélection de commune pour Abidjan -->
      <div v-if="selectedShipping === 'abidjan'" class="commune-selection">
        <div class="commune-label ">Sélectionnez votre commune:</div>
        <select :value="selectedCommune" @change="$emit('updateCommune', $event.target.value)" class="input-style">
          <option value="">Choisir une commune</option>
          <option v-for="tarif in tarifsAbidjan" :key="tarif.id" :value="tarif.commune">
            {{ tarif.commune }} ({{ formatFCFA(tarif.tarif_min) }} - {{ formatFCFA(tarif.tarif_max) }})
          </option>
        </select>
      </div>
  
      <!-- Sélection de ville pour l'intérieur -->
      <div v-if="selectedShipping === 'interieur'" class="ville-selection">
        <div class="ville-label">Sélectionnez votre ville:</div>
        <select :value="selectedVille" @change="$emit('updateVille', $event.target.value)" class="input-style">
          <option value="">Choisir une ville</option>
          <option v-for="tarif in tarifsInterieur" :key="tarif.id" :value="tarif.ville">
            {{ tarif.ville }} ({{ formatFCFA(tarif.tarif) }})
          </option>
        </select>
      </div>
  
      <div v-if="selectedShipping === 'retrait'" class="retrait-info">
        <div class="retrait-address">
          <div class="retrait-label">📍 Adresse de retrait:</div>
          <div class="retrait-details">
            <strong>Marché d'Adjamé</strong><br>
            Abidjan, Côte d'Ivoire<br>
            <span class="retrait-hours">🕒 Horaires: Lun-Sam 8h-18h</span>
          </div>
        </div>
        <div class="retrait-instructions">
          <div class="retrait-note">
            ℹ️ Vous recevrez un SMS avec les détails de retrait une fois votre commande prête.
          </div>
        </div>
      </div>
    </div>
  </template>
  
  <script setup>
  import { computed, defineProps, defineEmits } from 'vue'
  
  const props = defineProps({
    selectedShipping: String,
    selectedCommune: String,
    selectedVille: String,
    tarifsAbidjan: Array,
    tarifsInterieur: Array
  })
  
  const emit = defineEmits([
    'selectShipping',
    'updateCommune',
    'updateVille'
  ])
  
  const selectShipping = (option) => {
    emit('selectShipping', option)
  }
  
  const getDestinationLabel = () => {
    if (props.selectedShipping === 'abidjan' && props.selectedCommune) {
      return `${props.selectedCommune}, Abidjan`
    } else if (props.selectedShipping === 'interieur' && props.selectedVille) {
      return props.selectedVille
    }
    if (props.selectedShipping === 'retrait') {
      return 'Retrait à Adjamé'
    }
    return 'Côte d\'Ivoire'
  }
  
  const getShippingCostDisplay = (type) => {
    switch (type) {
      case 'abidjan':
        if (!props.selectedCommune) return 'Sélectionner commune'
        const communeTarif = props.tarifsAbidjan.find(t => t.commune === props.selectedCommune)
        if (!communeTarif) return 'Tarif non disponible'
        return `${formatFCFA(communeTarif.tarif_min)} - ${formatFCFA(communeTarif.tarif_max)}`
      
      case 'interieur':
        if (!props.selectedVille) return 'Sélectionner ville'
        const villeTarif = props.tarifsInterieur.find(t => t.ville === props.selectedVille)
        if (!villeTarif) return 'Tarif non disponible'
        return formatFCFA(villeTarif.tarif)
      
      case 'retrait':
        return 'Gratuit'
      
      default:
        return 'Non disponible'
    }
  }
  
  const getShippingTime = (type) => {
    if (type === 'abidjan') {
      if (props.selectedCommune) {
        const tarif = props.tarifsAbidjan.find(t => t.commune === props.selectedCommune)
        return tarif ? tarif.delai_livraison : '1-2 jours'
      }
      return '1-2 jours'
    } else if (type === 'interieur') {
      if (props.selectedVille) {
        const tarif = props.tarifsInterieur.find(t => t.ville === props.selectedVille)
        return tarif ? tarif.delai_livraison : '3-7 jours'
      }
      return '3-7 jours'
    } else if (type === 'retrait') {
      return 'Disponible sous 24h'
    }
    return 'N/A'
  }
  
  const formatFCFA = (montant) => {
    return Number(montant).toLocaleString('fr-FR', { minimumFractionDigits: 0 }) + ' FCFA'
  }
  </script>
  
  <style scoped>
  .shipping-section {
    margin-bottom: 24px;
  }
  
  .shipping-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 16px;
  }
  
  .shipping-title {
    font-size: 16px;
    font-weight: 600;
    color: #333;
  }
  
  .shipping-location {
    display: flex;
    align-items: center;
    gap: 6px;
    font-size: 14px;
    color: #666;
    cursor: pointer;
  }
  
  .shipping-options {
    display: flex;
    flex-direction: column;
    gap: 12px;
  }
  
  .shipping-option {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 16px;
    border: 1px solid #e8e8e8;
    border-radius: 8px;
    cursor: pointer;
    transition: all 0.3s ease;
    position: relative;
    color: #333;
  }
  
  .shipping-option:hover {
    border-color: #fe9800d5;
  }
  
  .shipping-option.selected {
    border-color: #fe9700;
    background: #1890ff0d;
  }
  
  .shipping-method {
    display: flex;
    align-items: center;
    gap: 12px;
    font-size: 15px;
    font-weight: 500;
  }
  
  .shipping-cost {
    font-size: 15px;
    font-weight: 600;
    color: #fe9700;
  }
  
  .shipping-time {
    font-size: 14px;
    color: #666;
    margin-left: 16px;
  }
  
  .shipping-radio {
    width: 20px;
    height: 20px;
    border: 2px solid #d9d9d9;
    border-radius: 50%;
    position: relative;
    transition: all 0.3s ease;
  }
  
  .shipping-option.selected .shipping-radio {
    border-color: #fe9700;
  }
  
  .shipping-option.selected .shipping-radio::after {
    content: '';
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    width: 10px;
    height: 10px;
    background: #fe9700;
    border-radius: 50%;
  }
  
  .commune-selection,
  .ville-selection {
    margin-top: 16px;
    padding: 16px;
    background: #f8f9fa;
    border-radius: 8px;
    border: 1px solid #e9ecef;
  }
  
  .commune-label,
  .ville-label {
    font-size: 14px;
    font-weight: 600;
    color: #333;
    margin-bottom: 8px;
  }
  
  .retrait-info {
    background: #f0f8ff;
    border: 1px solid #fe9700;
    border-radius: 8px;
    padding: 16px;
    margin-top: 12px;
  }
  
  .retrait-address {
    margin-bottom: 12px;
  }
  
  .retrait-label {
    font-weight: 600;
    color: #fe9700;
    margin-bottom: 8px;
  }
  
  .retrait-details {
    color: #333;
    line-height: 1.5;
  }
  
  .retrait-hours {
    color: #666;
    font-size: 14px;
  }
  
  .retrait-instructions {
    border-top: 1px solid #e3f2fd;
    padding-top: 12px;
  }
  
  .retrait-note {
    background: #e3f2fd;
    padding: 8px 12px;
    border-radius: 6px;
    font-size: 14px;
    color: #fe9700;
  }
  </style>
  