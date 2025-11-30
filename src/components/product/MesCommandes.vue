<template>
  <div class="my-orders-page">
    <div class="page-header">
      <div class="container">
        <h1 class="page-title">My Orders</h1>
        <p class="page-subtitle">Track the status of your orders and add your proof of payment.</p>
      </div>
      
    </div>

    <div class="container">
      <!-- Filtres -->
      <div class="filters-section">
        <div class="filter-tabs">
          <button
            v-for="status in orderStatuses"
            :key="status.value"
            :class="['filter-tab ', { active: selectedStatus === status.value }]"
            @click="selectedStatus = status.value"
          >
            <span class="tab-label">{{ status.label }}</span>
            <span v-if="getOrderCountByStatus(status.value) > 0" class="tab-badge">
              {{ getOrderCountByStatus(status.value) }}
            </span>
          </button>
        </div>
      </div>

      <!-- Loading State -->
      <div v-if="loading" class="loading-state">
        <div class="spinner"></div>
        <p>Loading orders...</p>
      </div>

      <!-- Empty State -->
      <div v-else-if="filteredOrders.length === 0" class="empty-state">
        <svg width="120" height="120" viewBox="0 0 24 24" fill="none" stroke="#d9d9d9" stroke-width="1" class="inline-flex itmns-center justify-center">
          <circle cx="9" cy="21" r="1"></circle>
          <circle cx="20" cy="21" r="1"></circle>
          <path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"></path>
        </svg>
        <h3>No order found</h3>
        <p>{{ selectedStatus === 'all' ? 'You have not yet placed an order' : 'No orders with this status' }}</p>
        <button class="primary-btn" @click="goToShop">
          <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <circle cx="9" cy="21" r="1"></circle>
            <circle cx="20" cy="21" r="1"></circle>
            <path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"></path>
          </svg>
          Discover our products
        </button>
      </div>

      <!-- Orders List -->
      <div v-else class="orders-list">
        <div v-for="order in filteredOrders" :key="order.id" class="order-card">
          <div class="order-header">
            <div class="order-info">
              <h3 class="order-number">Order #{{ order.numero_commande }}</h3>
              <span class="order-date">{{ formatDate(order.created_at) }}</span>
            </div>
            <div class="items-center justify-center">
              <span :class="['order-status ', getStatusClass(order.statut)]">
                {{ getStatusLabel(order.statut) }}
              </span>
              <button 
              v-if="order.statut==='send'"
              @click="downloadContract(order.numero_commande)"
              class="btn-degrade-orange mt-2 text-xs"
            >
              <Download class="w-4 h-4" />
              download contract
            </button>
            </div>
          </div>

          <div class="order-body">
            <div class="product-section">
              <div class="product-info">
                <img
                  :src="order.produit_image || '/placeholder.svg?height=100&width=100'"
                  :alt="order.produit_nom"
                  class="product-image"
                  @error="handleImageError"
                >
                <div class="product-details">
                  <h4 class="product-name">{{ order.produit_nom }}</h4>
                  <div class="product-meta">
                    <span class="meta-item">
                      <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <rect x="1" y="4" width="22" height="16" rx="2" ry="2"></rect>
                        <line x1="1" y1="10" x2="23" y2="10"></line>
                      </svg>
                      Quantity: {{ order.quantite }}
                    </span>
                    <span class="meta-item primary-color font-bold">
                      <!-- <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <line x1="12" y1="1" x2="12" y2="23"></line>
                        <path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"></path>
                      </svg> -->
                      Prix: {{ formatPrice(order.produit_prix, {showFOB:true}) }}
                    </span>
                  </div>
                  <div class="product-seller">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#666" stroke-width="2">
                      <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path>
                      <polyline points="9 22 9 12 15 12 15 22"></polyline>
                    </svg>
                    <span>{{ order.boutique_nom }}</span>
                  </div>
                </div>
              </div>

              <div class="order-summary">
                <div class="summary-row">
                  <span class="summary-label">Sub total:</span>
                  <span class="summary-value">{{ formatPrice(order.sous_total) }}</span>
                </div>
                <div v-if="order.frais_livraison > 0" class="summary-row">
                  <span class="summary-label">Delivery:</span>
                  <span class="summary-value">{{ formatPrice(order.frais_livraison) }}</span>
                </div>
                <div class="summary-row total">
                  <span class="summary-label">Total:</span>
                  <span class="summary-value">{{ formatPrice(order.total) }}</span>
                </div>
                <div class="summary-row deposit">
                  <span class="summary-label">{{ order.tobevalidate === 'valid' ? 'Deposit paid' : 'Pay the deposit according to the contract' }}</span>
                  <span v-if="order.tobevalidate === 'valid'"
                  class="summary-value">{{ formatPrice(calculatePaymentStatus(order).firstPayment) }}</span>
                </div>
              </div>
            </div>

            <!-- Payment Validation Status Section -->
            <div v-if="order.preuve_paiement" class="payment-validation-section">
              <div class="validation-header">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#fe7900" stroke-width="2">
                  <rect x="2" y="7" width="20" height="14" rx="2" ry="2"></rect>
                  <path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"></path>
                </svg>
                <h4>Payment status</h4>
              </div>

              <div v-if="order.tobevalidate === 'valid'" class="validation-status validated">
                <div class="status-icon">
                  <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3">
                    <polyline points="20 6 9 17 4 12"></polyline>
                  </svg>
                </div>
                <div class="status-content">
                  <h5>Proof of payment validated</h5>
                  <p>Your proof of payment has been verified and approved by the seller.</p>
                  <span class="validation-date">Validated on {{ formatDate(order.date_paiement) }}</span>
                  <div v-if="order.commentaire_paiement" class="vendor-comment">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#52c41a" stroke-width="2">
                      <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"></path>
                    </svg>
                    <span>{{ order.commentaire_paiement }}</span>
                  </div>
                </div>
              </div>

              <div v-else-if="order.tobevalidate === 'pending'" class="validation-status pending">
                <div class="status-icon">
                  <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#faad14" stroke-width="2">
                    <circle cx="12" cy="12" r="10"></circle>
                    <polyline points="12 6 12 12 16 14"></polyline>
                  </svg>
                </div>
                <div class="status-content">
                  <h5>Pending validation</h5>
                  <p>Your proof of payment is being verified by the seller.</p>
                  <span class="validation-date">Sent on{{ formatDate(order.date_paiement) }}</span>
                </div>
              </div>

              <div v-else class="validation-status pending">
                <div class="status-icon">
                  <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#1890ff" stroke-width="2">
                    <circle cx="12" cy="12" r="10"></circle>
                    <line x1="12" y1="16" x2="12" y2="12"></line>
                    <line x1="12" y1="8" x2="12.01" y2="8"></line>
                  </svg>
                </div>
                <div class="status-content">
                  <h5>Proof sent</h5>
                  <p>Your proof of payment is awaiting validation from the seller.</p>
                </div>
              </div>
            </div>

            <!-- Added Payment Progress Section for tracking multiple payments -->
            <div v-if="order.tobevalidate === 'valid'" class="payment-progress-section">
              <div class="payment-progress-header">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                  <rect x="1" y="4" width="22" height="16" rx="2" ry="2"></rect>
                  <line x1="1" y1="10" x2="23" y2="10"></line>
                </svg>
                <h4>Payment progress</h4>
              </div>

              <div class="payment-progress-content">
                <div class="progress-info">
                  <div class="progress-stats">
                    <div class="stat-item">
                      <span class="stat-label">Total order:</span>
                      <span class="stat-value">{{ formatPrice(calculatePaymentStatus(order).total) }}</span>
                    </div>
                    <div class="stat-item">
                      <span class="stat-label">Amount paid:</span>
                      <span class="stat-value success">{{ formatPrice(calculatePaymentStatus(order).totalPaid) }}</span>
                    </div>
                    <div class="stat-item">
                      <span class="stat-label">Payment is still due:</span>
                      <span class="stat-value warning">{{ formatPrice(calculatePaymentStatus(order).remaining) }}</span>
                    </div>
                  </div>

                  <div class="progress-bar-container">
                    <div class="progress-bar-fill" :style="{ width: calculatePaymentStatus(order).percentage + '%' }"></div>
                  </div>
                  <p class="progress-percentage">{{ calculatePaymentStatus(order).percentage.toFixed(1) }}% paid</p>
                </div>

                <!-- CHANGE: Use order.paiements instead of order.additional_payments -->
                <div v-if="order.paiements && order.paiements.length > 0" class="payments-history">
                  <h5>Payment history</h5>
                  <div class="payment-item initial">
                    <div class="payment-icon">
                      <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#52c41a" stroke-width="2">
                        <polyline points="20 6 9 17 4 12"></polyline>
                      </svg>
                    </div>
                    <div class="payment-details">
                      <span class="payment-label">Initial deposit</span>
                      <span class="payment-amount">{{ formatPrice(calculatePaymentStatus(order).firstPayment) }}</span>
                      <span class="payment-date">{{ formatDate(order.date_paiement) }}</span>
                    </div>
                  </div>
                  <!-- CHANGE: Display additional payments from order.paiements array -->
                  <div v-for="(payment, index) in order.paiements" :key="payment.id" class="payment-item">
                    <div class="payment-icon">
                      <svg v-if="payment.valide === 'valid'" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#52c41a" stroke-width="2">
                        <polyline points="20 6 9 17 4 12"></polyline>
                      </svg>
                      <svg v-else width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#faad14" stroke-width="2">
                        <circle cx="12" cy="12" r="10"></circle>
                        <polyline points="12 6 12 12 16 14"></polyline>
                      </svg>
                    </div>
                    <div class="payment-details">
                      <span class="payment-label">Additional payment #{{ index + 1 }}</span>
                      <span class="payment-amount">{{ formatPrice(payment.montant) }}</span>
                      <span class="payment-date">{{ formatDate(payment.date_paiement) }}</span>
                      <span v-if="payment.valide === 'pending' || !payment.valide" class="payment-status pending">Pending</span>
                      <span v-else-if="payment.valide === 'valid'" class="payment-status validated">Validated</span>
                      <span v-else class="payment-status pending">Pending</span>
                      <div v-if="payment.commentaire_admin" class="payment-comment">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#666" stroke-width="2">
                          <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"></path>
                        </svg>
                        <span>{{ payment.commentaire_admin }}</span>
                      </div>
                      <button
                        v-if="payment.preuve_paiement"
                        style="width: 180px; background-color: #fe7900;"
                        class="btn-degrade-orange"
                        @click="viewPaymentProof(payment)"
                      >
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                          <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                          <circle cx="12" cy="12" r="3"></circle>
                        </svg>
                        See the proof
                      </button>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <!-- Production Progress Section -->
            <!-- CHANGE> Check if prepare_date exists to show ready for delivery or in preparation -->
            <div v-if="order.tobevalidate === 'valid' && order.statut !== 'livree' && order.statut !== 'annule'" class="production-section">
              <div class="production-header">
                <div class="header-left">
                  <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#fe7900" stroke-width="2">
                    <rect x="2" y="3" width="20" height="14" rx="2" ry="2"></rect>
                    <line x1="8" y1="21" x2="16" y2="21"></line>
                    <line x1="12" y1="17" x2="12" y2="21"></line>
                  </svg>
                  <!-- CHANGE> Display different title based on prepare_date -->
                  <h4>{{ order.prepare_date ? 'Ready for delivery' : 'Preparing your order' }}</h4>
                </div>
                <!-- CHANGE> Display different badge based on prepare_date -->
                <span v-if="!order.prepare_date" class="production-badge">In progress</span>
                <span v-else class="production-badge ready">Ready</span>
              </div>

              <div class="production-content">
                <!-- CHANGE> Show different messages based on prepare_date and payment status -->
                <div v-if="!order.prepare_date" class="production-message">
                  <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#52c41a" stroke-width="2">
                    <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path>
                    <polyline points="22 4 12 14.01 9 11.01"></polyline>
                  </svg>
                  <p>Your product preparation has begun! Our team is actively working on your order.</p>
                </div>

                <!-- CHANGE> Show ready for delivery message when prepare_date exists -->
                <div v-else>
                  <!-- CHANGE> If payment is complete (100%), show delivery confirmation message -->
                  <div v-if="calculatePaymentStatus(order).percentage >= 100" class="production-message ">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#52c41a" stroke-width="2">
                      <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path>
                      <polyline points="22 4 12 14.01 9 11.01"></polyline>
                    </svg>
                    <div>
                      <p><strong>Congratulations !</strong> Your order is ready for delivery. Please check your email for more delivery information.</p>
                      <button class="submit-btn mt-2" @click="confirmDeliveryAddress(order)">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#ffffff" stroke-width="2">
                          <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path>
                          <circle cx="12" cy="10" r="3"></circle>
                        </svg>
                        Confirm the delivery address
                      </button>
                    </div>
                  </div>

                  <!-- CHANGE> If payment is not complete, show urgent payment message -->
                  <div v-else class="production-message urgent">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#faad14" stroke-width="2">
                      <circle cx="12" cy="12" r="10"></circle>
                      <line x1="12" y1="8" x2="12" y2="12"></line>
                      <line x1="12" y1="16" x2="12.01" y2="16"></line>
                    </svg>
                    <p><strong>Attention !</strong>Your order is ready for delivery, but there is still some left. <strong>{{ formatPrice(calculatePaymentStatus(order).remaining) }}</strong> payment is due. Please complete payment promptly so we can proceed with delivery.</p>
                  </div>
                </div>

                <div class="timeline-info">
                  <div class="timeline-item">
                    <div class="timeline-icon completed">
                      <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="3">
                        <polyline points="20 6 9 17 4 12"></polyline>
                      </svg>
                    </div>
                    <div class="timeline-content">
                      <h5>Payment confirmed</h5>
                      <span>{{ formatDate(order.date_paiement) }}</span>
                    </div>
                  </div>

                  <!-- CHANGE> Update timeline to show active state based on prepare_date -->
                  <div :class="['timeline-line', order.prepare_date ? 'completed' : 'active']"></div>

                  <div class="timeline-item">
                    <div :class="['timeline-icon', order.prepare_date ? 'completed' : 'active']">
                      <svg v-if="order.prepare_date" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="3">
                        <polyline points="20 6 9 17 4 12"></polyline>
                      </svg>
                      <div v-else class="pulse"></div>
                    </div>
                    <div class="timeline-content">
                      <h5>In preparation</h5>
                      <span>{{ order.prepare_date ? 'Finished ' + formatDate(order.prepare_date) : 'Since the ' + formatDate(order.updated_at) }}</span>
                    </div>
                  </div>

                  <!-- CHANGE> Update timeline line to show active when prepare_date exists -->
                  <div :class="['timeline-line', order.prepare_date ? 'active' : '']"></div>

                  <div class="timeline-item">
                    <!-- CHANGE> Show active icon when prepare_date exists -->
                    <div :class="['timeline-icon', order.prepare_date ? 'active' : '']">
                      <svg v-if="!order.prepare_date" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#999" stroke-width="2">
                        <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path>
                      </svg>
                      <div v-else class="pulse"></div>
                    </div>
                    <div class="timeline-content">
                      <h5>Ready for delivery</h5>
                      <!-- CHANGE> Show actual prepare_date instead of estimate -->
                      <span>{{ order.prepare_date ? formatDate(order.prepare_date) : 'Estimated: ' + getEstimatedDeliveryDate(order) }}</span>
                    </div>
                  </div>
                </div>

                <!-- CHANGE> Only show countdown if prepare_date doesn't exist -->
                <div v-if="!order.prepare_date && order.estimate_prepare" class="countdown-section">
                  <div class="countdown-header">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                      <circle cx="12" cy="12" r="10"></circle>
                      <polyline points="12 6 12 12 16 14"></polyline>
                    </svg>
                    <span>Time remaining before delivery</span>
                  </div>

                  <div class="countdown-display">
                    <div class="countdown-item">
                      <span class="countdown-value">{{ getRemainingTime(order).days }}</span>
                      <span class="countdown-label">days</span>
                    </div>
                    <div class="countdown-separator">:</div>
                    <div class="countdown-item">
                      <span class="countdown-value">{{ getRemainingTime(order).hours }}</span>
                      <span class="countdown-label">hours</span>
                    </div>
                    <div class="countdown-separator">:</div>
                    <div class="countdown-item">
                      <span class="countdown-value">{{ getRemainingTime(order).minutes }}</span>
                      <span class="countdown-label">minutes</span>
                    </div>
                  </div>

                  <div class="progress-bar-wrapper">
                    <div class="progress-bar">
                      <div class="progress-fill" :style="{ width: getProgressPercentage(order) + '%' }">
                        <span class="progress-text">{{ Math.floor(getProgressPercentage(order)) }}%</span>
                      </div>
                    </div>
                    <p class="progress-label">Progress of the preparation</p>
                  </div>

                  <div class="delivery-estimate">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#52c41a" stroke-width="2">
                      <rect x="1" y="3" width="15" height="13"></rect>
                      <polygon points="16 8 20 8 23 11 23 16 16 16 16 8"></polygon>
                      <circle cx="5.5" cy="18.5" r="2.5"></circle>
                      <circle cx="18.5" cy="18.5" r="2.5"></circle>
                    </svg>
                    <span>Delivery expected on <strong>{{ getEstimatedDeliveryDate(order) }}</strong></span>
                  </div>
                </div>

                <!-- CHANGE> Show delivery date when prepare_date exists -->
                <div v-if="order.prepare_date" class="countdown-section ready">
                  <div class="delivery-ready-info">
                    <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="#52c41a" stroke-width="2">
                      <rect x="1" y="3" width="15" height="13"></rect>
                      <polygon points="16 8 20 8 23 11 23 16 16 16 16 8"></polygon>
                      <circle cx="5.5" cy="18.5" r="2.5"></circle>
                      <circle cx="18.5" cy="18.5" r="2.5"></circle>
                    </svg>
                    <h5>  Your order is ready !</h5>
                    <p>Finalized preparation date : <strong>{{ formatDate(order.prepare_date) }}</strong></p>
                  </div>
                </div>
              </div>
            </div>

            <div v-if="order.adresse_complete" class="order-delivery">
              <div class="delivery-info">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#666" stroke-width="2">
                  <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path>
                  <circle cx="12" cy="10" r="3"></circle>
                </svg>
                <div>
                  <strong>Delivery address:</strong>
                  <p>{{ order.adresse_complete }}</p>
                  <p v-if="order.ville">{{ order.ville }}, {{ order.commune }}</p>
                  <p v-if="order.instructions_livraison" class="delivery-note">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#999" stroke-width="2">
                      <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                      <polyline points="14 2 14 8 20 8"></polyline>
                      <line x1="16" y1="13" x2="8" y2="13"></line>
                      <line x1="16" y1="17" x2="8" y2="17"></line>
                      <polyline points="10 9 9 9 8 9"></polyline>
                    </svg>
                    Note: {{ order.instructions_livraison }}
                  </p>
                </div>
              </div>
            </div>
          </div>

          <div class="order-actions">
            <button
              class="btn-gray"
              @click="handleChatClick(order)"
            >
              <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"></path>
              </svg>
              Contact the seller
            </button>

            <button
              v-if="order.statut === 'en_attente' && !order.preuve_paiement"
              class="btn-deconnexion"
              @click="openCancelModal(order)"
            >
              <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <circle cx="12" cy="12" r="10"></circle>
                <line x1="15" y1="9" x2="9" y2="15"></line>
                <line x1="9" y1="9" x2="15" y2="15"></line>
              </svg>
              Cancel
            </button>
            <button
              v-if="order.statut === 'send' && !order.preuve_paiement"
              class="btn-degrade-orange"
              @click="openPaymentProofModal(order)"
            >
              <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
                <polyline points="17 8 12 3 7 8"></polyline>
                <line x1="12" y1="3" x2="12" y2="15"></line>
              </svg>
              Add proof of payment
            </button>

            <button
              v-if="order.preuve_paiement"
              class="submit-btn"
              @click="viewPaymentProof(order)"
            >
              <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                <circle cx="12" cy="12" r="3"></circle>
              </svg>
              See the proof
            </button>

            <!-- Added button to add additional payments after initial payment is validated -->
            <button
              v-if="order.tobevalidate === 'valid' && calculatePaymentStatus(order).remaining > 0"
              class="btn-degrade-orange"
              @click="openAdditionalPaymentModal(order)"
            >
              <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
                <polyline points="17 8 12 3 7 8"></polyline>
                <line x1="12" y1="3" x2="12" y2="15"></line>
              </svg>
              Add a payment
            </button>

            
          </div>
        </div>
      </div>
    </div>

    <!-- Modal Upload Payment Proof -->
    <div v-if="showPaymentModal" class="modal-overlay" @click="closePaymentModal">
      <div class="modal-content" @click.stop>
        <button class="modal-close" @click="closePaymentModal">
          <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <line x1="18" y1="6" x2="6" y2="18"></line>
            <line x1="6" y1="6" x2="18" y2="18"></line>
          </svg>
        </button>

        <div class="modal-header">
          <h2 class="modal-title">Add proof of payment</h2>
          <p class="modal-subtitle">Order #{{ selectedOrder?.numero_commande }}</p>
        </div>

        <div class="payment-info-box">
          <div class="info-row">
            <span class="info-label">Amount to pay (deposit):</span>
            <!-- <span class="info-value">{{ formatPrice(selectedOrder?.total * 0.3) }}</span> -->
          </div>
          <p class="info-note">Please upload a screenshot or photo of your proof of payment.</p>
        </div>

        <form @submit.prevent="uploadPaymentProof" class="upload-form">
          <div class="form-group">
            <label class="form-label">Payment amount <span class="required">*</span></label>
            <input
              type="number"
              v-model="depositPaymentAmount"
              class="input-style"
              placeholder="Enter the amount"
              :max="calculatePaymentStatus(selectedOrder).remaining"
              min="1"
              step="any"
              required
            >
            <p class="file-hint">Pay the deposit according to the contract</p>
          </div>
          <div class="form-group">
            <label class="form-label">Select a file</label>
            <div class="file-input-wrapper">
              <input
                type="file"
                ref="fileInput"
                @change="handleFileSelect"
                accept="image/*"
                class="file-input"
                required
              >
              <div class="file-input-display">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#666" stroke-width="2">
                  <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
                  <polyline points="17 8 12 3 7 8"></polyline>
                  <line x1="12" y1="3" x2="12" y2="15"></line>
                </svg>
                <span v-if="!selectedFile">Click to select a file</span>
                <span v-else class="file-name">{{ selectedFile.name }}</span>
              </div>
            </div>
            <p class="file-hint">Accepted formats: JPG, PNG (Max 10MB)</p>

            <div v-if="uploading" class="upload-progress">
              <div class="progress-bar">
                <div class="progress-fill" :style="{ width: uploadProgress + '%' }"></div>
              </div>
              <p class="progress-text">Upload in progress: {{ uploadProgress }}%</p>
            </div>
          </div>

          <div class="form-group">
            <label class="form-label">Comment (optional)</label>
            <textarea
              v-model="paymentComment"
              class="input-style"
              placeholder="Add a comment about this payment..."
              rows="3"
            ></textarea>
          </div>

          <div class="modal-actions">
            
            <button type="button" class="btn-gray flex-1" @click="closePaymentModal" :disabled="uploading">
              Cancel
            </button>
            <button type="submit" class="btn-degrade-orange flex-1" :disabled="uploading || !selectedFile || !depositPaymentAmount">
              <span v-if="!uploading">Send proof</span>
              <span v-else>Shipment in progress...</span>
            </button>
          </div>
        </form>
      </div>
    </div>

    <!-- Added Modal for Additional Payment -->
    <div v-if="showAdditionalPaymentModal" class="modal-overlay" @click="closeAdditionalPaymentModal">
      <div class="modal-content" @click.stop>
        <button class="modal-close" @click="closeAdditionalPaymentModal">
          <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <line x1="18" y1="6" x2="6" y2="18"></line>
            <line x1="6" y1="6" x2="18" y2="18"></line>
          </svg>
        </button>

        <div class="modal-header">
          <h2 class="modal-title">Add an additional payment</h2>
          <p class="modal-subtitle">Order #{{ selectedOrder?.numero_commande }}</p>
        </div>

        <div class="payment-info-box">
          <div class="info-row">
            <span class="info-label">Total order:</span>
            <span class="info-value">{{ formatPrice(calculatePaymentStatus(selectedOrder).total) }}</span>
          </div>
          <div class="info-row">
            <span class="info-label">Already paid:</span>
            <span class="info-value">{{ formatPrice(calculatePaymentStatus(selectedOrder).totalPaid) }}</span>
          </div>
          <div class="info-row highlight">
            <span class="info-label">Payment is still due:</span>
            <span class="info-value">{{ formatPrice(calculatePaymentStatus(selectedOrder).remaining) }}</span>
          </div>
          <p class="info-note">You can pay all or part of the remaining amount</p>
        </div>

        <form @submit.prevent="uploadAdditionalPayment" class="upload-form">
          <div class="form-group">
            <label class="form-label">Payment amount <span class="required">*</span></label>
            <input
              type="number"
              v-model="additionalPaymentAmount"
              class="input-style"
              placeholder="Enter the amount"
              :max="calculatePaymentStatus(selectedOrder).remaining"
              min="1"
              step="any"
              required
            >
            <p class="file-hint">Maximum: {{ formatPrice(calculatePaymentStatus(selectedOrder).remaining) }}</p>
          </div>

          <div class="form-group">
            <label class="form-label">Proof of payment <span class="required">*</span></label>
            <div class="file-input-wrapper">
              <input
                type="file"
                ref="additionalPaymentFileInput"
                @change="handleAdditionalPaymentFileSelect"
                accept="image/*"
                class="file-input"
                required
              >
              <div class="file-input-display">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#666" stroke-width="2">
                  <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
                  <polyline points="17 8 12 3 7 8"></polyline>
                  <line x1="12" y1="3" x2="12" y2="15"></line>
                </svg>
                <span v-if="!additionalPaymentFile">Click to select a file</span>
                <span v-else class="file-name">{{ additionalPaymentFile.name }}</span>
              </div>
            </div>
            <p class="file-hint">Accepted formats: JPG, PNG (Max 10MB)</p>

            <div v-if="uploading" class="upload-progress">
              <div class="progress-bar">
                <div class="progress-fill" :style="{ width: uploadProgress + '%' }"></div>
              </div>
              <p class="progress-text">Upload in progress:{{ uploadProgress }}%</p>
            </div>
          </div>

          <div class="form-group">
            <label class="form-label">Comment (optional)</label>
            <textarea
              v-model="additionalPaymentComment"
              class="input-style"
              placeholder="Add a comment about this payment..."
              rows="3"
            ></textarea>
          </div>

          <div class="modal-actions">
            <button type="button" class="btn-gray flex-1" @click="closeAdditionalPaymentModal" :disabled="uploading">
              Cancel
            </button>
            <button type="submit" class="btn-degrade-orange flex-1" :disabled="uploading || !additionalPaymentFile || !additionalPaymentAmount">
              <span v-if="!uploading">Send payment</span>
              <span v-else>Shipment in progress...</span>
            </button>
            
          </div>
        </form>
      </div>
    </div>

    <!-- Modal Cancel Order -->
    <div v-if="showCancelModal" class="modal-overlay" @click="closeCancelModal">
      <div class="modal-content" @click.stop>
        <button class="modal-close" @click="closeCancelModal">
          <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <line x1="18" y1="6" x2="6" y2="18"></line>
            <line x1="6" y1="6" x2="18" y2="18"></line>
          </svg>
        </button>

        <div class="modal-icon warning">
          <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="#ff4d4f" stroke-width="2">
            <circle cx="12" cy="12" r="10"></circle>
            <line x1="15" y1="9" x2="9" y2="15"></line>
            <line x1="9" y1="9" x2="15" y2="15"></line>
          </svg>
        </div>

        <h2 class="modal-title">Cancel the order</h2>
        <p class="modal-message">
          Are you sure you want to cancel the order #{{ selectedOrder?.numero_commande }}?
          This action is irreversible.
        </p>

        <form @submit.prevent="cancelOrder" class="cancel-form">
          <div class="form-group">
            <label class="form-label">Reason for cancellation <span v-if="!selectedOrder.paiements.length">(Optional)</span></label>
            <textarea
              v-model="cancelReason"
              class="input-style"
              title="Please complete this field"
              placeholder="Explain why you wish to cancel this order..."
              rows="4"
              :required="selectedOrder.paiements.length"
            ></textarea>
          </div>

          <div class="modal-actions">
            <button type="button" class="btn-gray flex-1" @click="closeCancelModal">
              Back
            </button>
            <button type="submit" class="btn-deconnexion flex-1" :disabled="cancelling">
              <span v-if="!cancelling">Confirm the cancellation</span>
              <span v-else>Cancellation in progress...</span>
            </button>
            
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted } from 'vue'
import { useRouter } from 'vue-router'
import { ordersApi } from '../../services/api.js'
import axios from 'axios'
import { useChatStore } from '../../stores/chat'
import {formatPrice} from "../../services/formatPrice"
import { Download } from 'lucide-vue-next'

const router = useRouter()
const chatStore = useChatStore()

// Cloudinary configuration
const cloudinaryConfig = {
  cloudName: 'daaavha4z',
  uploadPreset: 'aliadjame',
  apiKey: 'wy0Eh-uA0Y0Ci3nyODix0b3WejA',
  imageUploadUrl: 'https://api.cloudinary.com/v1_1/daaavha4z/image/upload'
}

// State
const orders = ref([])
const loading = ref(true)
const selectedStatus = ref('all')
const showPaymentModal = ref(false)
const showCancelModal = ref(false)
const selectedOrder = ref(null)
const selectedFile = ref(null)
const paymentComment = ref('')
const cancelReason = ref('')
const uploading = ref(false)
const uploadProgress = ref(0)
const cancelling = ref(false)
const fileInput = ref(null)
const currentTime = ref(new Date())
let countdownInterval = null

const showAdditionalPaymentModal = ref(false)
const depositPaymentAmount = ref('')
const additionalPaymentAmount = ref('')
const additionalPaymentFile = ref(null)
const additionalPaymentComment = ref('')
const additionalPaymentFileInput = ref(null)

const orderStatuses = [
  { value: 'all', label: 'All' },
  { value: 'en_attente', label: 'Pending' },
  { value: 'confirmee', label: 'Confirmed' },
  { value: 'send', label: 'Contrat en attente' },
  { value: 'en_cours', label: 'In Progress' },
  { value: 'livree', label: 'Delivered' },
  { value: 'annule', label: 'Cancelled' },
  { value: 'terminee', label: 'Finished' }
]

// Computed
const filteredOrders = computed(() => {
  if (selectedStatus.value === 'all') {
    return orders.value
  }
  console.log("orders:", orders.value)
  return orders.value.filter(order => order.statut === selectedStatus.value)
})

// Methods
const getOrderCountByStatus = (status) => {
  if (status === 'all') return orders.value.length
  return orders.value.filter(order => order.statut === status).length
}

const getStatusClass = (status) => {
  const statusMap = {
    'en_attente': 'pending',
    'confirmee': 'confirmed',
    'send': 'Contrat_en_attente',
    'en_cours': 'processing',
    'livree': 'delivered',
    'annule': 'cancelled'
  }
  return statusMap[status] || 'pending'
}

const getStatusLabel = (status) => {
  const labelMap = {
    'en_attente': 'Pending',
    'confirmee': 'Confirmed',
    'send': 'Contrat en attente',
    'en_cours': 'In the process of delivery',
    'livree': 'Delivered',
    'annule': 'Cancelled',
    'terminee': 'Finished'
  }
  return labelMap[status] || status
}

const downloadContract =(orderNumber)=>{
  console.log("Contract:",orderNumber)
  alert("order Number: " + orderNumber)
}

// const formatPrice = (price) => {
//   return Number(price).toLocaleString('fr-FR', { minimumFractionDigits: 0 }) + ' $'
// }

const formatDate = (dateString) => {
  const date = new Date(dateString)
  return date.toLocaleDateString('fr-FR', {
    year: 'numeric',
    month: 'long',
    day: 'numeric',
    hour: '2-digit',
    minute: '2-digit'
  })
}

const getRemainingTime = (order) => {
  let deliveryDate
  if (order.estimate_prepare) {
    deliveryDate = new Date(order.estimate_prepare)
  } else {
    const updateDate = new Date(order.updated_at)
    deliveryDate = new Date(updateDate.getTime() + (14 * 24 * 60 * 60 * 1000))
  }
  
  const now = currentTime.value
  const diff = deliveryDate - now

  if (diff <= 0) {
    return { days: 0, hours: 0, minutes: 0, seconds: 0 }
  }

  const days = Math.floor(diff / (1000 * 60 * 60 * 24))
  const hours = Math.floor((diff % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60))
  const minutes = Math.floor((diff % (1000 * 60 * 60)) / (1000 * 60))
  const seconds = Math.floor((diff % (1000 * 60)) / 1000)

  return { days, hours, minutes, seconds }
}

const getEstimatedDeliveryDate = (order) => {
  if (order.estimate_prepare) {
    const deliveryDate = new Date(order.estimate_prepare)
    return deliveryDate.toLocaleDateString('fr-FR', {
      weekday: 'long',
      year: 'numeric',
      month: 'long',
      day: 'numeric'
    })
  }
  // Fallback to 14 days if estimate_prepare is not available
  const updateDate = new Date(order.updated_at)
  const deliveryDate = new Date(updateDate.getTime() + (14 * 24 * 60 * 60 * 1000))
  return deliveryDate.toLocaleDateString('fr-FR', {
    weekday: 'long',
    year: 'numeric',
    month: 'long',
    day: 'numeric'
  })
}

const getProgressPercentage = (order) => {
  let deliveryDate
  if (order.estimate_prepare) {
    deliveryDate = new Date(order.estimate_prepare)
  } else {
    const updateDate = new Date(order.updated_at)
    deliveryDate = new Date(updateDate.getTime() + (14 * 24 * 60 * 60 * 1000))
  }
  
  const updateDate = new Date(order.updated_at)
  const now = currentTime.value

  const totalTime = deliveryDate - updateDate
  const elapsed = now - updateDate

  if (elapsed <= 0) return 0
  if (elapsed >= totalTime) return 100
  if (totalTime <= 0) return 0

  return Math.min(Math.floor((elapsed / totalTime) * 100), 100)
}

const calculatePaymentStatus = (order) => {
  const total = order.total
  const firstPayment = total * 0.3 // 30% initial payment
  const additionalPayments = order.paiements || []
  const totalAdditionalPaid = additionalPayments.reduce((sum, payment) => sum + parseFloat(payment.montant || 0), 0)
  const totalPaid = (order.tobevalidate === 'valid' ? firstPayment : 0) + totalAdditionalPaid
  const remaining = total - totalPaid
  const percentage = (totalPaid / total) * 100
  
  return {
    total,
    firstPayment,
    totalAdditionalPaid,
    totalPaid,
    remaining,
    percentage: Math.min(percentage, 100)
  }
}

const handleImageError = (event) => {
  event.target.src = '/placeholder.svg?height=100&width=100'
}

const fetchOrders = async () => {
  loading.value = true

  try {
    const userData = localStorage.getItem('user') || sessionStorage.getItem('user')
    if (!userData) {
      orders.value = []
      loading.value = false
      return
    }

    const parsedUser = JSON.parse(userData)
    const userId = parsedUser.id

    const response = await ordersApi.getMyOrders(userId)

    if (response.success) {
      orders.value = response.data || []
      console.log(orders.value)
    } else {
      orders.value = []
    }
  } catch (error) {
    console.error('Error fetching orders:', error)
    orders.value = []
  } finally {
    loading.value = false
  }
}

const openPaymentProofModal = (order) => {
  selectedOrder.value = order
  showPaymentModal.value = true
  selectedFile.value = null
  paymentComment.value = ''
  uploadProgress.value = 0
}

const closePaymentModal = () => {
  showPaymentModal.value = false
  selectedOrder.value = null
  selectedFile.value = null
  paymentComment.value = ''
  uploadProgress.value = 0
}

const openAdditionalPaymentModal = (order) => {
  selectedOrder.value = order
  showAdditionalPaymentModal.value = true
  additionalPaymentAmount.value = ''
  additionalPaymentFile.value = null
  additionalPaymentComment.value = ''
  uploadProgress.value = 0
}

const closeAdditionalPaymentModal = () => {
  showAdditionalPaymentModal.value = false
  selectedOrder.value = null
  additionalPaymentAmount.value = ''
  additionalPaymentFile.value = null
  additionalPaymentComment.value = ''
  uploadProgress.value = 0
}

const handleChatClick = async (order) => {
  const productData = {
    id: order.produit_id,
    name: order.produit_nom,
    slug: order.produit_slug || '',
    unit_price: order.produit_prix,
    boutique_id: order.boutique_id,
    boutique_name: order.boutique_nom,
    market: order.ville || 'Abidjan',
    experience: order.boutique_experience || 0,
    primary_image: order.produit_image,
    rating: order.rating || 0,
    views: order.views || 0,
    wholesale_min_qty: order.wholesale_min_qty || null,
    wholesale_price: order.wholesale_price || null,
  }

  try {
    await chatStore.setSupplier(productData)

    if (chatStore.isMobile) {
      chatStore.openChat()
    } else {
      chatStore.openDesktopChat()
    }
  } catch (error) {
    console.error('Error opening chat:', error)
  }
}

const handleFileSelect = (event) => {
  const file = event.target.files[0]
  if (file) {
    if (file.size > 10 * 1024 * 1024) {
      alert('The file is too large. Maximum size: 10MB')
      event.target.value = ''
      return
    }

    if (!file.type.startsWith('image/')) {
      alert('Please select an image (JPG, PNG, etc.)')
      event.target.value = ''
      return
    }

    selectedFile.value = file
  }
}

const handleAdditionalPaymentFileSelect = (event) => {
  const file = event.target.files[0]
  if (file) {
    if (file.size > 10 * 1024 * 1024) {
      alert('The file is too large. Maximum size: 10MB')
      event.target.value = ''
      return
    }

    if (!file.type.startsWith('image/')) {
      alert('Please select an image (JPG, PNG, etc.)')
      event.target.value = ''
      return
    }

    additionalPaymentFile.value = file
  }
}

const uploadToCloudinary = async (file) => {
  try {
    uploadProgress.value = 0

    const fileName = `payment_proof_${selectedOrder.value.id}_${Date.now()}_${file.name.replace(/\s+/g, '_')}`

    const formData = new FormData()
    formData.append('file', file)
    formData.append('upload_preset', cloudinaryConfig.uploadPreset)
    formData.append('api_key', cloudinaryConfig.apiKey)
    formData.append('public_id', fileName)

    const response = await axios.post(cloudinaryConfig.imageUploadUrl, formData, {
      headers: {
        'Content-Type': 'multipart/form-data'
      },
      onUploadProgress: (progressEvent) => {
        const percentCompleted = Math.round((progressEvent.loaded * 100) / progressEvent.total)
        uploadProgress.value = percentCompleted
      }
    })

    if (response.data && response.data.secure_url) {
      return response.data.secure_url
    } else {
      throw new Error('Réponse Cloudinary invalide')
    }
  } catch (error) {
    console.error('Cloudinary upload error:', error)
    throw error
  }
}

const uploadPaymentProof = async () => {
  if (!selectedFile.value || !selectedOrder.value || !depositPaymentAmount.value) return

  uploading.value = true
  try {
    const cloudinaryUrl = await uploadToCloudinary(selectedFile.value)

    const response = await ordersApi.uploadPaymentProof(selectedOrder.value.id, {
      payment_proof_url:cloudinaryUrl,
      comment: paymentComment.value
    })

    if (response.success) {
      alert('Proof of payment successfully sent!')
      closePaymentModal()
      fetchOrders()
    } else {
      throw new Error(response.message || 'Erreur lors de l\'envoi')
    }
  } catch (error) {
    console.error('Error uploading payment proof:', error)
    alert('Error sending proof of payment: ' + (error.message || 'Erreur inconnue'))
  } finally {
    uploading.value = false
    uploadProgress.value = 0
  }
}

const uploadAdditionalPayment = async () => {
  if (!additionalPaymentFile.value || !selectedOrder.value || !additionalPaymentAmount.value) {
    alert('Please fill in all required fields')
    return
  }

  const amount = parseFloat(additionalPaymentAmount.value)
  const paymentStatus = calculatePaymentStatus(selectedOrder.value)
  
  if (amount <= 0) {
    alert('The amount must be greater than 0')
    return
  }
  
  if (amount > paymentStatus.remaining) {
    alert(`The amount cannot exceed the remaining amount: ${formatPrice(paymentStatus.remaining)}`)
    return
  }

  uploading.value = true
  try {
    const fileName = `additional_payment_${selectedOrder.value.id}_${Date.now()}_${additionalPaymentFile.value.name.replace(/\s+/g, '_')}`

    const formData = new FormData()
    formData.append('file', additionalPaymentFile.value)
    formData.append('upload_preset', cloudinaryConfig.uploadPreset)
    formData.append('api_key', cloudinaryConfig.apiKey)
    formData.append('public_id', fileName)

    const uploadResponse = await axios.post(cloudinaryConfig.imageUploadUrl, formData, {
      headers: {
        'Content-Type': 'multipart/form-data'
      },
      onUploadProgress: (progressEvent) => {
        const percentCompleted = Math.round((progressEvent.loaded * 100) / progressEvent.total)
        uploadProgress.value = percentCompleted
      }
    })

    if (!uploadResponse.data || !uploadResponse.data.secure_url) {
      throw new Error('Réponse Cloudinary invalide')
    }

    const cloudinaryUrl = uploadResponse.data.secure_url

    const response = await ordersApi.uploadAdditionalPayment(selectedOrder.value.id, {
      montant: amount,
      preuve_url:cloudinaryUrl,
      commentaire: additionalPaymentComment.value
    })

    if (response.success) {
      alert('Additional payment successfully sent!')
      closeAdditionalPaymentModal()
      fetchOrders()
    } else {
      throw new Error(response.message || 'Erreur lors de l\'envoi')
    }
  } catch (error) {
    console.error('Error uploading additional payment:', error)
    alert('Error sending payment: ' + (error.message || 'Erreur inconnue'))
  } finally {
    uploading.value = false
    uploadProgress.value = 0
  }
}

const viewPaymentProof = (order) => {
  if (order.preuve_paiement) {
    window.open(order.preuve_paiement, '_blank')
  }
}

const openCancelModal = (order) => {
  selectedOrder.value = order
  console.log("cancelletion",selectedOrder.value.paiements)
  showCancelModal.value = true
  cancelReason.value = ''
}

const closeCancelModal = () => {
  showCancelModal.value = false
  selectedOrder.value = null
  cancelReason.value = ''
}

const cancelOrder = async () => {
  // if (!cancelReason.value.trim() || !selectedOrder.value) return

  cancelling.value = true
  try {
    const response = await ordersApi.cancelOrder(selectedOrder.value.id, cancelReason.value)

    if (response.success) {
      alert('Order successfully cancelled')
      closeCancelModal()
      fetchOrders()
    } else {
      throw new Error(response.message || 'Error canceling order')
    }
  } catch (error) {
    console.error('Error cancelling order:', error)
    alert('Error canceling order')
  } finally {
    cancelling.value = false
  }
}

const confirmDeliveryAddress = async (order) => {
  if (!order.adresse_complete) {
    alert('No delivery address found')
    return
  }

  const confirmed = confirm(
    `Confirm the delivery address:\n\n${order.adresse_complete}\n${order.ville ? order.ville + ', ' + order.commune : ''}\n\nIs this address correct? ?`
  )

  if (confirmed) {
    try {
      // You can add an API call here to confirm the address
      // await ordersApi.confirmDeliveryAddress(order.id)
      alert('Delivery address confirmed! You will receive your order soon.')
    } catch (error) {
      console.error('Error confirming address:', error)
      alert('Error confirming address')
    }
  }
}


const goToShop = () => {
  router.push('/')
}

onMounted(() => {
  fetchOrders()

  countdownInterval = setInterval(() => {
    currentTime.value = new Date()
  }, 1000)
})

onUnmounted(() => {
  if (countdownInterval) {
    clearInterval(countdownInterval)
  }
})
</script>

<style scoped>
/* Added styles for payment progress section */
.payment-progress-section {
  background: linear-gradient(135deg, #f8f9fa 0%, #fff 100%);
  border: 1px solid #e8e8e8;
  border-radius: 12px;
  padding: 20px;
  margin-bottom: 24px; /* Adjusted margin for better spacing */
}

.payment-progress-header {
  display: flex;
  align-items: center;
  gap: 10px;
  margin-bottom: 16px;
  color: #333;
}

.payment-progress-header svg {
  color: #fe7900;
}

.payment-progress-header h4 {
  margin: 0;
  font-size: 16px;
  font-weight: 600;
}

.payment-progress-content {
  display: flex;
  flex-direction: column;
  gap: 20px;
}

.progress-info {
  display: flex;
  flex-direction: column;
  gap: 12px;
}

.progress-stats {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
  gap: 12px;
}

.stat-item {
  display: flex;
  flex-direction: column;
  gap: 4px;
  padding: 12px;
  background: white;
  border-radius: 8px;
  border: 1px solid #f0f0f0;
}

.stat-label {
  font-size: 13px;
  color: #666;
}

.stat-value {
  font-size: 16px;
  font-weight: 600;
  color: #333;
}

/* .stat-value.success {
  color: #52c41a;
} */

.stat-value.warning {
  color: #faad14;
}

.progress-bar-container {
  width: 100%;
  height: 24px;
  background: #f0f0f0;
  border-radius: 12px;
  overflow: hidden;
  position: relative;
}

.progress-bar-fill {
  height: 100%;
  background: linear-gradient(90deg, #52c41a 0%, #73d13d 100%);
  transition: width 0.5s ease;
  border-radius: 12px;
}

.progress-percentage {
  text-align: center;
  font-size: 14px;
  font-weight: 600;
  color: #52c41a;
  margin: 0;
}

.payments-history {
  background: white;
  padding: 16px;
  border-radius: 8px;
  border: 1px solid #f0f0f0;
}

.payments-history h5 {
  margin: 0 0 12px 0;
  font-size: 14px;
  font-weight: 600;
  color: #333;
}

.payment-item {
  display: flex;
  align-items: flex-start;
  gap: 12px;
  padding: 12px;
  background: #fafafa;
  border-radius: 8px;
  margin-bottom: 8px;
}

.payment-item:last-child {
  margin-bottom: 0;
}

.payment-item.initial {
  background: #f6ffed;
  border: 1px solid #b7eb8f;
}

.payment-icon {
  width: 32px;
  height: 32px;
  display: flex;
  align-items: center;
  justify-content: center;
  background: white;
  border-radius: 50%;
  flex-shrink: 0;
}

.payment-details {
  flex: 1;
  display: flex;
  flex-direction: column;
  gap: 4px;
}

.payment-label {
  font-size: 14px;
  font-weight: 500;
  color: #333;
}

.payment-amount {
  font-size: 15px;
  font-weight: 600;
  color: #52c41a;
}

.payment-date {
  font-size: 12px;
  color: #999;
}

.payment-status {
  display: inline-block;
  padding: 2px 8px;
  border-radius: 4px;
  font-size: 11px;
  font-weight: 600;
  text-transform: uppercase;
  width: fit-content;
}

.payment-status.pending {
  background: #fff7e6;
  color: #faad14;
}

.payment-status.validated {
  background: #f6ffed;
  color: #52c41a;
}

/* CHANGE: Add styles for payment comment */
.payment-comment {
  display: flex;
  align-items: flex-start;
  gap: 6px;
  padding: 8px;
  background: white;
  border-radius: 4px;
  margin-top: 8px;
  font-size: 13px;
  color: #666;
  line-height: 1.4;
}

.payment-comment svg {
  flex-shrink: 0;
  margin-top: 2px;
}

/* Added styles for additional payment modal */
.info-row.highlight {
  background: #fff7e6;
  padding: 8px;
  border-radius: 6px;
  font-weight: 600;
}

.form-input {
  width: 100%;
  padding: 10px 12px;
  border: 2px solid #d9d9d9;
  border-radius: 8px;
  font-size: 15px;
  transition: all 0.3s;
  font-family: inherit;
}

.form-input:focus {
  outline: none;
  border-color: #fe9700;
  box-shadow: 0 0 0 3px rgba(254, 151, 0, 0.1);
}

.required {
  color: #ff4d4f;
  margin-left: 4px;
}

/* Existing styles start here - assuming they are present in the original file */
.my-orders-page {
  padding: 40px 0;
  font-family: 'Poppins', sans-serif;
  background-color: #f9f9f9;
}

.container {
  width: 90%;
  max-width: 1200px;
  margin: 0 auto;
}

.page-header {
  background-color: #ffffff;
  padding: 40px 0;
  border-bottom: 1px solid #e0e0e0;
  margin-bottom: 30px;
}

.page-title {
  font-size: 32px;
  font-weight: 700;
  color: #333;
  margin-bottom: 8px;
}

.page-subtitle {
  font-size: 16px;
  color: #666;
  margin-bottom: 0;
}

.filters-section {
  margin-bottom: 30px;
  background-color: #ffffff;
  padding: 15px;
  border-radius: 12px;
  box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
}

.filter-tabs {
  display: flex;
  flex-wrap: wrap;
  gap: 10px;
}

.filter-tab {
  padding: 10px 18px;
  border: none;
  background-color: #f0f0f0;
  border-radius: 50px;
  cursor: pointer;
  font-size: 14px;
  font-weight: 500;
  color: #666;
  transition: background-color 0.3s, color 0.3s;
  display: flex;
  align-items: center;
  gap: 5px;
}

.filter-tab.active {
  background-color: #fe9700;
  color: #ffffff;
  font-weight: 600;
}

.tab-badge {
  background-color: #ff4d4f;
  color: white;
  border-radius: 50%;
  width: 20px;
  height: 20px;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 11px;
  font-weight: 600;
}

.loading-state,
.empty-state {
  text-align: center;
  padding: 60px 20px;
  background-color: #ffffff;
  border-radius: 12px;
  color: #fe9700;
  box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
}

.loading-state .spinner {
  border: 4px solid #f3f3f3;
  border-top: 4px solid #fe9700;
  border-radius: 50%;
  width: 40px;
  height: 40px;
  animation: spin 1s linear infinite;
  margin: 0 auto 15px auto;
}

@keyframes spin {
  0% { transform: rotate(0deg); }
  100% { transform: rotate(360deg); }
}

.loading-state p,
.empty-state h3,
.empty-state p {
  color: #666;
}

.empty-state h3 {
  font-size: 24px;
  font-weight: 700;
  margin-bottom: 10px;
  color: #333;
}

.empty-state p {
  font-size: 16px;
  margin-bottom: 30px;
}

.primary-btn {
  display: inline-flex;
  align-items: center;
  gap: 8px;
  padding: 12px 24px;
  background-color: #fe9700;
  color: white;
  border: none;
  border-radius: 8px;
  font-size: 16px;
  font-weight: 600;
  cursor: pointer;
  transition: background-color 0.3s, transform 0.2s;
  text-decoration: none;
}

.primary-btn:hover {
  background-color: #e68a00;
  transform: translateY(-2px);
}

.orders-list {
  display: grid;
  grid-template-columns: 1fr;
  gap: 25px;
}

.order-card {
  background-color: #ffffff;
  border-radius: 12px;
  box-shadow: 0 4px 20px rgba(0, 0, 0, 0.07);
  overflow: hidden;
  padding: 25px;
  transition: transform 0.2s ease-in-out;
}

.order-card:hover {
  transform: translateY(-5px);
}

.order-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 20px;
  padding-bottom: 15px;
  border-bottom: 1px solid #f0f0f0;
}

.order-info h3 {
  font-size: 20px;
  font-weight: 700;
  color: #333;
  margin-bottom: 5px;
}

.order-info .order-date {
  font-size: 13px;
  color: #999;
}

.order-status {
  padding: 5px 12px;
  border-radius: 6px;
  font-size: 12px;
  font-weight: 600;
  text-transform: uppercase;
}

.order-status.pending {
  background: #fff1f0;
  color: #fe9700;
}

.order-status.confirmed {
  background: #e2f9f4;
  color: #10B981;
}

.order-status.processing {
  background: #f6f2e0;
  color: #f7af20;
}

.order-status.delivered {
  background: #def5e4;
  color: #52c41a;
}

.order-status.cancelled {
  background: #f9e1e1;
  color: #ec0202;
}

.order-body {
  display: flex;
  flex-direction: column;
  gap: 25px;
}

.product-section {
  display: flex;
  flex-wrap: wrap;
  gap: 30px;
}

.product-info {
  flex: 2;
  display: flex;
  gap: 20px;
  align-items: flex-start;
}

.product-image {
  width: 90px;
  height: 90px;
  object-fit: cover;
  border-radius: 8px;
  flex-shrink: 0;
}

.product-details {
  display: flex;
  flex-direction: column;
  gap: 8px;
}

.product-name {
  font-size: 16px;
  font-weight: 600;
  color: #333;
  margin-bottom: 5px;
}

.product-meta {
  display: flex;
  flex-direction: column;
  gap: 5px;
  font-size: 13px;
  color: #666;
}

.product-meta .meta-item {
  display: flex;
  align-items: center;
  gap: 5px;
}

.product-seller {
  display: flex;
  align-items: center;
  gap: 8px;
  font-size: 13px;
  color: #666;
  margin-top: 10px;
}

.product-seller svg {
  stroke: #999;
}

.order-summary {
  flex: 1;
  background: #fcfcfc;
  padding: 15px 20px;
  border-radius: 10px;
  border: 1px solid #f0f0f0;
  display: flex;
  flex-direction: column;
  gap: 10px;
}

.summary-row {
  display: flex;
  justify-content: space-between;
  font-size: 14px;
}

.summary-row.total {
  font-weight: 700;
  font-size: 16px;
  color: #333;
  margin-top: 8px;
  padding-top: 8px;
  border-top: 1px dashed #e0e0e0;
}

.summary-row.deposit {
  font-size: 14px;
  color: #666;
}

.summary-label {
  color: #666;
}

.summary-value {
  color: #333;
  font-weight: 500;
}

.payment-validation-section {
  background-color: #ffffff;
  border: 1px solid #e8e8e8;
  border-radius: 12px;
  padding: 20px;
  margin-bottom: 24px;
}

.validation-header {
  display: flex;
  align-items: center;
  gap: 10px;
  margin-bottom: 16px;
  color: #333;
}

.validation-header h4 {
  margin: 0;
  font-size: 16px;
  font-weight: 600;
}

.validation-status {
  display: flex;
  align-items: center;
  gap: 15px;
  padding: 15px;
  border-radius: 8px;
}

.validation-status.validated {
  background: #f6ffed;
  border: 1px solid #b7eb8f;
}

.validation-status.pending {
  background: #fffbe6;
  border: 1px solid #ffd591;
}

.status-icon {
  flex-shrink: 0;
  display: flex;
  align-items: center;
  justify-content: center;
}

.validation-status.validated .status-icon {
  border: 2px solid #52c41a ;
  color:#52c41a;
  width: 40px;
  height: 40px;
  border-radius: 50%;
}

.validation-status.pending .status-icon {
  background: #faad14;
  width: 40px;
  height: 40px;
  border-radius: 50%;
}

.validation-status.pending .status-icon svg {
  stroke: white;
}

.status-content {
  flex-grow: 1;
}

.status-content h5 {
  font-size: 16px;
  font-weight: 600;
  margin-bottom: 5px;
  color:#333
}

.status-content p {
  font-size: 13px;
  color: #666;
  margin-bottom: 8px;
}

.validation-date {
  font-size: 12px;
  color: #999;
}

.vendor-comment {
  display: flex;
  align-items: center;
  gap: 8px;
  margin-top: 10px;
  font-size: 13px;
  color: #52c41a;
}

.vendor-comment svg {
  stroke: #52c41a;
}

/* Production Section Styles */
.production-section {
  background-color: #ffffff;
  border: 1px solid #e8e8e8;
  border-radius: 12px;
  padding: 25px;
  margin-bottom: 24px;
}

.production-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 20px;
  padding-bottom: 15px;
  border-bottom: 1px solid #f0f0f0;
}

.production-header .header-left {
  display: flex;
  align-items: center;
  gap: 10px;
  color: #333;
}

.production-header h4 {
  font-size: 18px;
  font-weight: 600;
  margin: 0;
}

.production-badge {
  background-color: #fff0f6;
  color: #ff4d4f;
  padding: 4px 10px;
  border-radius: 6px;
  font-size: 12px;
  font-weight: 600;
  text-transform: uppercase;
}

/* Add styles for ready badge */
.production-badge.ready {
  background-color: #f6ffed;
  color: #52c41a;
}

.production-content {
  display: flex;
  flex-direction: column;
  gap: 25px;
}

.production-message {
  display: flex;
  align-items: center;
  gap: 12px;
  background: #f6ffed;
  padding: 15px;
  border-radius: 8px;
  border: 1px solid #b7eb8f;
}

/* Add styles for success and urgent messages */
.production-message.success {
  background: #f6ffed;
  border: 1px solid #b7eb8f;
}

.production-message.urgent {
  background: #fffbe6;
  border: 1px solid #ffd591;
}

.production-message.success svg {
  stroke: #52c41a;
  flex-shrink: 0;
}

.production-message.urgent svg {
  stroke: #faad14;
  flex-shrink: 0;
}

.production-message p {
  margin: 0;
  font-size: 14px;
  color: #333;
}

.timeline-info {
  position: relative;
  padding-left: 20px;
}

.timeline-item {
  display: flex;
  align-items: flex-start;
  margin-bottom: 25px;
  position: relative;
}

.timeline-icon {
  width: 30px;
  height: 30px;
  display: flex;
  align-items: center;
  justify-content: center;
  border-radius: 50%;
  margin-right: 15px;
  flex-shrink: 0;
  background-color: #e0e0e0;
}

.timeline-icon.completed {
  background-color: #52c41a;
}

.timeline-icon.active {
  background-color: #1890ff;
}

.timeline-icon.active .pulse {
  width: 100%;
  height: 100%;
  background-color: rgba(24, 144, 255, 0.2);
  border-radius: 50%;
  animation: pulse 1.5s infinite ease-out;
  position: absolute;
  top: 0;
  left: 0;
}

@keyframes pulse {
  0% { transform: scale(1); opacity: 0.7; }
  100% { transform: scale(1.5); opacity: 0; }
}

.timeline-content {
  display: flex;
  flex-direction: column;
  gap: 4px;
}

.timeline-content h5 {
  font-size: 16px;
  font-weight: 600;
  color: #333;
  margin: 0;
}

.timeline-content span {
  font-size: 13px;
  color: #666;
}

.timeline-line {
  position: absolute;
  top: 15px;
  left: 15px;
  width: 2px;
  background-color: #e0e0e0;
  height: calc(100% - 30px); /* Adjust height to not span past the last item */
}

/* Add styles for completed timeline line */
.timeline-line.completed {
  background-color: #52c41a;
}

.timeline-line.active {
  background-color: #1890ff;
}

/* Countdown Section */
.countdown-section {
  background-color: #f8f9fa;
  padding: 20px;
  border-radius: 10px;
  border: 1px solid #e8e8e8;
}

/* Add styles for delivery ready section */
.countdown-section.ready {
  background-color: #f6ffed;
  border: 1px solid #b7eb8f;
}

.countdown-header {
  display: flex;
  align-items: center;
  gap: 10px;
  margin-bottom: 15px;
  color: #fe7900;
}

.countdown-header span {
  font-size: 15px;
  font-weight: 600;
}

.countdown-display {
  display: flex;
  justify-content: center;
  align-items: center;
  gap: 10px;
  margin-bottom: 20px;
}

.countdown-item {
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 4px;
}

.countdown-value {
  font-size: 28px;
  font-weight: 700;
  color: #333;
}

.countdown-label {
  font-size: 12px;
  color: #666;
  text-transform: uppercase;
}

.countdown-separator {
  font-size: 28px;
  font-weight: 700;
  color: #aaa;
}

.progress-bar-wrapper {
  margin-top: 20px;
  text-align: center;
}

.progress-bar {
  width: 100%;
  height: 20px;
  background-color: #e0e0e0;
  border-radius: 10px;
  overflow: hidden;
  margin-bottom: 8px;
}

.progress-bar .progress-fill {
  height: 100%;
  background-color: #fe9700;
  border-radius: 10px;
  transition: width 0.5s ease;
  display: flex;
  align-items: center;
  justify-content: center;
  color: white;
  font-size: 11px;
  font-weight: 600;
}

.progress-label {
  font-size: 13px;
  color: #666;
}

.delivery-estimate {
  display: flex;
  align-items: center;
  gap: 10px;
  margin-top: 25px;
  color: #52c41a;
  font-size: 15px;
}

.delivery-estimate strong {
  font-weight: 600;
}

/* Delivery Address Styles */
.order-delivery {
  background-color: #ffffff;
  border: 1px solid #e8e8e8;
  border-radius: 12px;
  padding: 25px;
  margin-top: 24px; /* Added margin-top for spacing */
}

.delivery-info {
  display: flex;
  align-items: flex-start;
  gap: 15px;
}

.delivery-info div {
  display: flex;
  flex-direction: column;
  gap: 8px;
}

.delivery-info strong {
  font-size: 16px;
  color: #333;
  margin-bottom: 5px;
}

.delivery-info p {
  font-size: 14px;
  color: #666;
  margin: 0;
}

.delivery-note {
  display: flex;
  align-items: center;
  gap: 8px;
  font-size: 13px;
  color: #999;
  margin-top: 10px;
}

/* Order Actions Styles */
.order-actions {
  display: flex;
  flex-wrap: wrap;
  gap: 15px;
  margin-top: 25px;
  justify-content: flex-end; /* Align actions to the right */
}


/* Modal Styles */
.modal-overlay {
  position: fixed;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background: rgba(0, 0, 0, 0.6);
  display: flex;
  align-items: center;
  justify-content: center;
  z-index: 1000;
}

.modal-content {
  background: white;
  padding: 30px;
  border-radius: 12px;
  width: 90%;
  max-width: 500px;
  position: relative;
  box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
  animation: modal-fade-in 0.3s ease-out forwards;
  overflow-y: auto; /* Allow scrolling if content is too long */
  max-height: 90vh; /* Limit height to viewport */
}

@keyframes modal-fade-in {
  from { opacity: 0; transform: scale(0.9); }
  to { opacity: 1; transform: scale(1); }
}

.modal-close {
  position: absolute;
  top: 20px;
  right: 20px;
  background: none;
  border: none;
  cursor: pointer;
  color: #999;
  transition: color 0.3s;
}

.modal-close:hover {
  color: #333;
}

.modal-header {
  text-align: center;
  margin-bottom: 25px;
}

.modal-title {
  font-size: 24px;
  font-weight: 700;
  color: #333;
  margin-bottom: 8px;
}

.modal-subtitle,
.modal-message {
  font-size: 15px;
  color: #666;
  margin-bottom: 0;
}

.modal-message {
  margin-top: 15px;
}

.payment-info-box {
  background-color: #f8f9fa;
  padding: 15px;
  border-radius: 8px;
  margin-bottom: 20px;
  border: 1px solid #e8e8e8;
}

.info-row {
  display: flex;
  justify-content: space-between;
  margin-bottom: 8px;
  font-size: 14px;
}

.info-label {
  color: #666;
}

.info-value {
  color: #333;
  font-weight: 600;
}

.info-note {
  font-size: 13px;
  color: #999;
  margin-top: 10px;
  text-align: center;
}

.upload-form {
  display: flex;
  flex-direction: column;
  gap: 20px;
}

.form-group {
  text-align: left;
}

.form-label {
  display: block;
  margin-bottom: 8px;
  font-weight: 500;
  color: #333;
  font-size: 14px;
}

.file-input-wrapper {
  position: relative;
  overflow: hidden;
  cursor: pointer;
  border: 2px dashed #d9d9d9;
  border-radius: 8px;
  background-color: #fefefe;
  transition: all 0.3s;
}

.file-input-wrapper:hover {
  border-color: #fe9700;
}

.file-input {
  position: absolute;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  opacity: 0;
  cursor: pointer;
}

.file-input-display {
  padding: 15px 20px;
  display: flex;
  align-items: center;
  gap: 12px;
  color: #666;
  font-size: 15px;
}

.file-input-display span {
  font-weight: 500;
}

.file-name {
  color: #fe9700;
}

.file-hint {
  font-size: 12px;
  color: #999;
  margin-top: 8px;
}

.upload-progress {
  margin-top: 10px;
}

.progress-bar {
  width: 100%;
  height: 12px;
  background-color: #e0e0e0;
  border-radius: 6px;
  overflow: hidden;
  margin-bottom: 5px;
}

.progress-fill {
  height: 100%;
  background-color: #fe9700;
  border-radius: 6px;
  transition: width 0.4s ease-in-out;
}

.progress-text {
  font-size: 12px;
  color: #666;
}

.modal-actions {
  display: flex;
  justify-content: center;
  gap: 15px;
  margin-top: 30px;
}

.modal-btn {
  padding: 12px 24px;
  border: none;
  border-radius: 8px;
  font-size: 15px;
  font-weight: 600;
  cursor: pointer;
  transition: background-color 0.3s, transform 0.2s;
  text-decoration: none;
}

.modal-btn.primary {
  background-color: #fe9700;
  color: white;
}

.modal-btn.primary:hover {
  background-color: #e68a00;
  transform: translateY(-2px);
}

.modal-btn.secondary {
  background-color: #f0f0f0;
  color: #333;
}

.modal-btn.secondary:hover {
  background-color: #e0e0e0;
  transform: translateY(-2px);
}

.modal-btn.danger {
  background-color: #ff4d4f;
  color: white;
}

.modal-btn.danger:hover {
  background-color: #e63c3e;
  transform: translateY(-2px);
}

.modal-btn:disabled {
  opacity: 0.6;
  cursor: not-allowed;
}

.modal-icon {
  text-align: center;
  margin-bottom: 20px;
}

.modal-icon.warning svg {
  color: #ff4d4f;
}

.cancel-form {
  margin-top: 20px;
}

.cancel-form .form-group {
  margin-bottom: 20px;
}

/* Add styles for delivery ready section */
.delivery-ready-info {
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 12px;
  text-align: center;
}

.delivery-ready-info svg {
  stroke: #52c41a;
}

.delivery-ready-info h5 {
  font-size: 20px;
  font-weight: 700;
  color: #52c41a;
  margin: 0;
}

.delivery-ready-info p {
  font-size: 15px;
  color: #333;
  margin: 0;
}
</style>
