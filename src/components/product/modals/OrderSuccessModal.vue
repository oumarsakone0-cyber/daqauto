<template>
    <div class="success-modal-overlay" @click.self="$emit('close')">
      <div class="success-modal">
        <div class="modal-content">
          <div class="success-icon">
            <svg width="64" height="64" viewBox="0 0 24 24" fill="none" stroke="#52c41a" stroke-width="2">
              <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path>
              <polyline points="22,4 12,14.01 9,11.01"></polyline>
            </svg>
          </div>
          
          <h2 class="success-title success-color">Commande confirmée !</h2>
          <p class="success-message">
            Votre commande a été enregistrée avec succès. Vous recevrez un appel de confirmation dans les plus brefs délais.
          </p>
          
          <div class="order-details">
            <div class="order-number">
              <span class="label">Numéro de commande:</span>
              <span class="value">{{ orderData.numero_commande }}</span>
            </div>
            
            <div class="order-summary">
              <div class="summary-item">
                <span class="item-label">Produit:</span>
                <span class="item-value">{{ orderData.produit_nom }}</span>
              </div>
              <div class="summary-item">
                <span class="item-label">Quantité:</span>
                <span class="item-value">{{ orderData.quantite }} pièce(s)</span>
              </div>
              <div class="summary-item">
                <span class="item-label">Total:</span>
                <span class="total-amount primary-color">{{ formatPrice(orderData.total) }}</span>
              </div>
            </div>
            
            <div class="contact-info">
              <div class="contact-item">
                <svg class="success-color" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                  <path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"/>
                </svg>
                <span>{{ orderData.client_telephone }}</span>
              </div>
              <div class="contact-item">
                <svg class="error-color" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                  <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/>
                  <circle cx="12" cy="10" r="3"/>
                </svg>
                <span>{{ getDeliveryAddress() }}</span>
              </div>
            </div>
          </div>
          
          <div class="next-steps">
            <h3 class="steps-title">Prochaines étapes:</h3>
            <div class="steps-list">
              <div class="step-item">
                <div class="step-number bg-orange">1</div>
                <div class="step-text">Vous recevrez un appel de confirmation sous 30 minutes</div>
              </div>
              <div class="step-item ">
                <div class="step-number bg-orange">2</div>
                <div class="step-text">Préparation de votre commande (1-2h)</div>
              </div>
              <div class="step-item">
                <div class="step-number bg-orange">3</div>
                <div class="step-text">Livraison selon l'option choisie</div>
              </div>
            </div>
          </div>
          
          <div class="modal-actions">
            <button class="btn-whatsapp" @click="contactWhatsApp">
              <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor">
                <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/>
              </svg>
              Contacter par WhatsApp
            </button>
            <button class="btn-degrade-orange" @click="$emit('close')">
              Continuer mes achats
            </button>
          </div>
        </div>
      </div>
    </div>
  </template>
  
  <script setup>
  import { defineProps, defineEmits } from 'vue'
  import { formatPrice } from '../../../services/formatPrice'
  
  const props = defineProps({
    orderData: Object
  })
  
  const emit = defineEmits([
    'close'
  ])
  
  
  const getDeliveryAddress = () => {
    if (props.orderData.type_livraison === 'retrait') {
      return 'Retrait à Adjamé'
    } else if (props.orderData.commune) {
      return `${props.orderData.commune}, ${props.orderData.ville}`
    } else {
      return props.orderData.ville || 'Adresse de livraison'
    }
  }
  
  const contactWhatsApp = () => {
    const message = `Bonjour, je viens de passer la commande ${props.orderData.numero_commande}. Pouvez-vous me confirmer les détails ?`
    const whatsappUrl = `https://wa.me/2250000000000?text=${encodeURIComponent(message)}`
    window.open(whatsappUrl, '_blank')
  }
  </script>
  
  <style scoped>
  .success-modal-overlay {
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
  
  .success-modal {
    background: #fff;
    border-radius: 12px;
    width: 100%;
    max-width: 500px;
    max-height: 90vh;
    overflow-y: auto;
  }
  
  .modal-content {
    padding: 32px 24px 24px;
    text-align: center;
  }
  
  .success-icon {
    display: flex;
    justify-content: center;
    margin-bottom: 24px;
  }
  
  .success-title {
    font-size: 24px;
    font-weight: 700;
    margin: 0 0 12px 0;
  }
  
  .success-message {
    font-size: 16px;
    color: #666;
    line-height: 1.5;
    margin: 0 0 32px 0;
  }
  
  .order-details {
    background: #fafafa;
    border-radius: 8px;
    padding: 20px;
    margin-bottom: 24px;
    text-align: left;
  }
  
  .order-number {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 12px 0;
    border-bottom: 1px solid #e8e8e8;
    margin-bottom: 16px;
  }
  
  .order-number .label {
    font-size: 14px;
    color: #666;
  }
  
  .order-number .value {
    font-size: 14px;
    font-weight: 600;
    color: #333;
    font-family: monospace;
  }
  
  .order-summary {
    margin-bottom: 16px;
  }
  
  .summary-item {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 8px 0;
    font-size: 14px;
  }
  
  .item-label {
    color: #666;
  }
  
  .item-value {
    font-weight: 500;
    color: #333;
  }
  
  .total-amount {
    font-size: 16px;
    font-weight: 700;
  }
  
  .contact-info {
    border-top: 1px solid #e8e8e8;
    padding-top: 16px;
  }
  
  .contact-item {
    display: flex;
    align-items: center;
    gap: 8px;
    margin-bottom: 8px;
    font-size: 14px;
    color: #666;
  }
  
  .next-steps {
    background: #f0f8ff;
    border-radius: 8px;
    padding: 20px;
    margin-bottom: 24px;
    text-align: left;
  }
  
  .steps-title {
    font-size: 16px;
    font-weight: 600;
    color: #333;
    margin: 0 0 16px 0;
  }
  
  .steps-list {
    display: flex;
    flex-direction: column;
    gap: 12px;
  }
  
  .step-item {
    display: flex;
    align-items: flex-start;
    gap: 12px;
  }
  
  .step-number {
    width: 24px;
    height: 24px;
    color: white;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 12px;
    font-weight: 600;
    flex-shrink: 0;
  }
  
  .step-text {
    font-size: 14px;
    color: #666;
    line-height: 1.4;
  }
  
  .modal-actions {
    display: flex;
    flex-direction: column;
    gap: 12px;
  }
  
  @media (max-width: 768px) {
    .success-modal {
      margin: 0;
      border-radius: 12px 12px 0 0;
      position: fixed;
      bottom: 0;
      left: 0;
      right: 0;
      max-height: 90vh;
    }
    
    .modal-content {
      padding: 24px 20px 20px;
    }
  }
  </style>
  