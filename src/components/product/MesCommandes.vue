<template>
  <div class="my-orders-container">
    <!-- Header -->
    <div class="orders-header">
      <div class="header-content">
        <div class="header-title-section">
          <h1 class="main-title">My Orders</h1>
          <p class="subtitle">Track the status of your orders and add your proof of payment</p>
        </div>
      </div>
    </div>

    <!-- Filters -->
    <div class="filters-container">
      <div class="filters-wrapper">
        <button
          v-for="status in orderStatuses"
          :key="status.value"
          :class="['filter-btn', { active: selectedStatus === status.value }]"
          @click="selectedStatus = status.value"
        >
          <span class="tab-label">{{ status.label }}</span>
          <span v-if="getOrderCountByStatus(status.value) > 0" class="count-badge">
            {{ getOrderCountByStatus(status.value) }}
          </span>
        </button>
      </div>
    </div>

    <!-- Loading State -->
    <div v-if="loading" class="loading-container">
      <div class="spinner"></div>
      <p>Loading orders...</p>
    </div>

    <!-- Empty State -->
    <div v-else-if="filteredOrders.length === 0" class="empty-state">
      <svg width="120" height="120" viewBox="0 0 24 24" fill="none" stroke="#d9d9d9" stroke-width="1">
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

    <!-- Orders Table -->
    <div v-else class="orders-table-container">
      <table class="orders-table">
        <thead>
          <tr>
            <th class="w-12"></th>
            <th>Order Number</th>
            <th>Product</th>
            <th>Boutique</th>
            <th>Date</th>
            <th>Total</th>
            <th>Status</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
          <template v-for="order in filteredOrders" :key="order.id">
            <!-- Compact Row -->
            <tr :class="['order-row', { expanded: expandedOrders.has(order.id) }]" @click="toggleExpand(order.id)">
              <td class="expand-cell">
                <button class="expand-btn" @click.stop="toggleExpand(order.id)">
                  <svg :class="['expand-icon', { rotated: expandedOrders.has(order.id) }]" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <polyline points="6 9 12 15 18 9"></polyline>
                  </svg>
                </button>
              </td>
              <td class="order-number-cell">
                <span class="order-number">#{{ order.numero_commande }}</span>
              </td>
              <td>
                <div class="product-info-compact">
                  <img
                    :src="order.produit_image || '/placeholder.svg'"
                    :alt="order.produit_nom"
                    class="product-thumbnail"
                    @error="handleImageError"
                  >
                  <div class="product-name-compact">
                    <span>{{ order.produit_nom }}</span>
                    <span class="product-qty">Qty: {{ order.quantite }}</span>
                  </div>
                </div>
              </td>
              <td>
                <span class="boutique-name">{{ order.boutique_nom }}</span>
              </td>
              <td>
                <span class="order-date">{{ formatDate(order.created_at) }}</span>
              </td>
              <td>
                <span class="order-total">{{ formatPrice(order.total) }}</span>
              </td>
              <td>
                <span :class="['status-badge', getStatusClass(order.statut)]">
                  {{ getStatusLabel(order.statut) }}
                </span>
              </td>
              <td class="actions-cell" @click.stop>
                <button v-if="order.statut === 'send'" @click="downloadContract(order.numero_commande)" class="action-btn download-btn" title="Download contract">
                  <Download class="w-4 h-4" />
                </button>
                <button v-if="order.preuve_paiement" @click="viewPaymentProof(order)" class="action-btn payment-btn" title="View payment proof">
                  <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                    <circle cx="12" cy="12" r="3"></circle>
                  </svg>
                </button>
                <button v-if="order.tobevalidate === 'valid' && calculatePaymentStatus(order).remaining > 0" @click="openAdditionalPaymentModal(order)" class="action-btn add-payment-btn" title="Add payment">
                  <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <line x1="12" y1="5" x2="12" y2="19"></line>
                    <line x1="5" y1="12" x2="19" y2="12"></line>
                  </svg>
                </button>
              </td>
            </tr>

            <!-- Expanded Row -->
            <tr v-if="expandedOrders.has(order.id)" class="expanded-row">
              <td colspan="8" class="expanded-cell">
                <div class="expanded-content">

                  <!-- Order Lifecycle Timeline -->
                  <div class="lifecycle-timeline-section">
                    <h4 class="lifecycle-title">
                      <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#fe9700" stroke-width="2">
                        <circle cx="12" cy="12" r="10"></circle>
                        <polyline points="12 6 12 12 16 14"></polyline>
                      </svg>
                      Order Lifecycle
                    </h4>
                    <div class="lifecycle-steps-container">
                      <div
                        v-for="(stage, index) in orderLifecycleSteps"
                        :key="stage.key"
                        class="lifecycle-step-wrapper"
                        @click="isStageClickable(order, stage.key) ? selectLifecycleStage(order.id, stage.key) : null"
                      >
                        <div class="lifecycle-step-content">
                          <div :class="[
                            'lifecycle-circle',
                            isStageComplete(order, stage.key) ? 'completed' :
                            isStageActive(order, stage.key) ? 'active' :
                            'pending',
                            isStageClickable(order, stage.key) ? 'clickable' : '',
                            selectedLifecycleStage[order.id] === stage.key ? 'selected' : ''
                          ]">
                            <span class="lifecycle-emoji">{{ stage.emoji }}</span>
                          </div>
                          <div :class="[
                            'lifecycle-label',
                            isStageComplete(order, stage.key) ? 'completed' :
                            isStageActive(order, stage.key) ? 'active' : 'pending'
                          ]">
                            <span class="label-text">{{ stage.label }}</span>
                            <span class="label-description">{{ stage.description }}</span>
                          </div>
                        </div>
                        <div
                          v-if="index < orderLifecycleSteps.length - 1"
                          :class="[
                            'lifecycle-connector',
                            isStageComplete(order, stage.key) ? 'completed' : 'pending'
                          ]"
                        ></div>
                      </div>
                    </div>
                  </div>

                  <!-- Content based on selected lifecycle stage -->

                  <!-- Pending Stage Content -->
                  <div v-show="selectedLifecycleStage[order.id] === 'pending'" class="stage-content">
                    <div class="detail-section">
                      <h4 class="section-title">Order Information</h4>
                      <div class="info-message pending">
                        <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="#f59e0b" stroke-width="2">
                          <circle cx="12" cy="12" r="10"></circle>
                          <polyline points="12 6 12 12 16 14"></polyline>
                        </svg>
                        <div class="message-content">
                          <h5>Your order is pending</h5>
                          <p>We are reviewing your order. You will receive a contract soon.</p>
                        </div>
                      </div>
                    </div>
                  </div>

                  <!-- Contract Stage Content -->
                  <div v-show="selectedLifecycleStage[order.id] === 'contract'" class="stage-content">
                    <div v-if="order.statut === 'send' || order.tobevalidate === 'valid'" class="detail-section">
                      <h4 class="section-title">Contract & Payment</h4>
                      <div class="contract-actions-section">
                        <button class="download-contract-btn-large" @click.stop="downloadContract(order.numero_commande)">
                          <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
                            <polyline points="7 10 12 15 17 10"></polyline>
                            <line x1="12" y1="15" x2="12" y2="3"></line>
                          </svg>
                          Download Contract
                        </button>
                        <p class="contract-description">Please download, review and sign the contract before making the initial payment.</p>
                      </div>
                    </div>
                  </div>

                  <!-- Confirmed, Ready, Shipping, Delivered stages - Show Order Summary -->
                  <div v-show="['confirmed', 'ready', 'shipping', 'delivered'].includes(selectedLifecycleStage[order.id])" class="stage-content">

                  <!-- Order Summary Section -->
                  <div class="detail-section">
                    <h4 class="section-title">Order Summary</h4>
                    <div class="modern-summary-card">
                      <div class="summary-row">
                        <span class="summary-label">Sub total:</span>
                        <span class="summary-value">{{ formatPrice(order.sous_total) }}</span>
                      </div>

                      <div v-if="order.frais_livraison > 0" class="summary-row">
                        <span class="summary-label">Delivery:</span>
                        <span class="summary-value">{{ formatPrice(order.frais_livraison) }}</span>
                      </div>

                      <!-- CIF Shipping Details -->
                      <div v-if="order.delivery_method === 'CIF' && order.shipping_cost" class="cif-shipping-details">
                        <div class="shipping-header">
                          <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#fe9700" stroke-width="2">
                            <rect x="1" y="3" width="15" height="13"></rect>
                            <polygon points="16 8 20 8 23 11 23 16 16 16 16 8"></polygon>
                            <circle cx="5.5" cy="18.5" r="2.5"></circle>
                            <circle cx="18.5" cy="18.5" r="2.5"></circle>
                          </svg>
                          <span class="shipping-title">International Shipping (CIF)</span>
                        </div>
                        <div class="shipping-info-grid">
                          <div class="shipping-info-item">
                            <span class="info-label">Shipping Cost:</span>
                            <span class="info-value">{{ formatPrice(order.shipping_cost) }}</span>
                          </div>
                          <div class="shipping-info-item">
                            <span class="info-label">Loading Port:</span>
                            <span class="info-value">{{ order.loading_port || 'N/A' }}</span>
                          </div>
                          <div class="shipping-info-item">
                            <span class="info-label">Destination Port:</span>
                            <span class="info-value">{{ order.destination_port || 'N/A' }}</span>
                          </div>
                        </div>
                      </div>

                      <div class="summary-divider"></div>

                      <div class="summary-row total-row">
                        <span class="summary-label">Total:</span>
                        <span class="summary-value total">{{ formatPrice(order.total) }}</span>
                      </div>

                      <!-- Contract Section - Only show when status is 'send' -->
                      <div v-if="order.statut === 'send' || order.tobevalidate === 'valid'" class="contract-section">
                        <div class="contract-header">
                          <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#fe9700" stroke-width="2">
                            <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                            <polyline points="14 2 14 8 20 8"></polyline>
                            <line x1="16" y1="13" x2="8" y2="13"></line>
                            <line x1="16" y1="17" x2="8" y2="17"></line>
                            <polyline points="10 9 9 9 8 9"></polyline>
                          </svg>
                          <span class="contract-title">
                            {{ order.tobevalidate === 'valid' ? 'Deposit paid' : 'Pay the deposit according to the contract' }}
                          </span>
                        </div>

                        <div v-if="order.tobevalidate === 'valid'" class="deposit-paid-info">
                          <span class="deposit-amount">{{ formatPrice(calculatePaymentStatus(order).firstPayment) }}</span>
                          <span class="deposit-status">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#52c41a" stroke-width="2">
                              <polyline points="20 6 9 17 4 12"></polyline>
                            </svg>
                            Validated
                          </span>
                        </div>

                        <div v-else class="contract-actions">
                          <button class="download-contract-btn" @click.stop="downloadContract(order.numero_commande)">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                              <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
                              <polyline points="7 10 12 15 17 10"></polyline>
                              <line x1="12" y1="15" x2="12" y2="3"></line>
                            </svg>
                            Download Contract
                          </button>
                          <p class="contract-description">Download and review the contract with price details before payment</p>
                        </div>
                      </div>
                    </div>
                  </div>

                  <!-- Payment Validation Status -->
                  <div v-if="order.preuve_paiement" class="detail-section">
                    <div class="section-header">
                      <h4 class="section-title">Payment Status</h4>
                    </div>

                    <div v-if="order.tobevalidate === 'valid'" class="status-card validated">
                      <div class="status-icon">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3">
                          <polyline points="20 6 9 17 4 12"></polyline>
                        </svg>
                      </div>
                      <div class="status-text">
                        <h5>Proof of payment validated</h5>
                        <p>Your proof of payment has been verified and approved by the seller.</p>
                        <span class="status-date">Validated on {{ formatDate(order.date_paiement) }}</span>
                        <div v-if="order.commentaire_paiement" class="vendor-comment">
                          <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#52c41a" stroke-width="2">
                            <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"></path>
                          </svg>
                          <span>{{ order.commentaire_paiement }}</span>
                        </div>
                      </div>
                    </div>

                    <div v-else-if="order.tobevalidate === 'pending'" class="status-card pending">
                      <div class="status-icon">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                          <circle cx="12" cy="12" r="10"></circle>
                          <polyline points="12 6 12 12 16 14"></polyline>
                        </svg>
                      </div>
                      <div class="status-text">
                        <h5>Pending validation</h5>
                        <p>Your proof of payment is being verified by the seller.</p>
                        <span class="status-date">Sent on {{ formatDate(order.date_paiement) }}</span>
                      </div>
                    </div>

                    <div v-else class="status-card info">
                      <div class="status-icon">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                          <circle cx="12" cy="12" r="10"></circle>
                          <line x1="12" y1="16" x2="12" y2="12"></line>
                          <line x1="12" y1="8" x2="12.01" y2="8"></line>
                        </svg>
                      </div>
                      <div class="status-text">
                        <h5>Proof sent</h5>
                        <p>Your proof of payment is awaiting validation from the seller.</p>
                      </div>
                    </div>
                  </div>

                  <!-- Payment Progress Section -->
                  <div v-if="order.tobevalidate === 'valid'" v-show="['confirmed', 'ready'].includes(selectedLifecycleStage[order.id])" class="detail-section">
                    <h4 class="section-title">Payment Progress</h4>

                    <div class="payment-stats">
                      <div class="stat-box">
                        <span class="stat-label">Total order:</span>
                        <span class="stat-value">{{ formatPrice(calculatePaymentStatus(order).total) }}</span>
                      </div>
                      <div class="stat-box">
                        <span class="stat-label">Amount paid:</span>
                        <span class="stat-value success">{{ formatPrice(calculatePaymentStatus(order).totalPaid) }}</span>
                      </div>
                      <div class="stat-box">
                        <span class="stat-label">Payment is still due:</span>
                        <span class="stat-value warning">{{ formatPrice(calculatePaymentStatus(order).remaining) }}</span>
                      </div>
                    </div>

                    <div class="progress-bar-wrapper">
                      <div class="progress-bar">
                        <div class="progress-fill" :style="{ width: calculatePaymentStatus(order).percentage + '%' }"></div>
                      </div>
                      <p class="progress-text">{{ calculatePaymentStatus(order).percentage.toFixed(1) }}% paid</p>
                    </div>

                    <!-- Payment History -->
                    <div v-if="order.paiements && order.paiements.length > 0" class="payments-history">
                      <h5 class="history-title">Payment history</h5>
                      <div class="payment-timeline">
                        <!-- Initial Payment -->
                        <div class="timeline-item">
                          <div class="timeline-dot"></div>
                          <div class="timeline-content">
                            <div class="timeline-header">
                              <span class="timeline-label">Initial deposit</span>
                              <span class="timeline-amount">{{ formatPrice(calculatePaymentStatus(order).firstPayment) }}</span>
                              <span class="timeline-status validated">Validated</span>
                            </div>
                            <span class="timeline-date">{{ formatDate(order.date_paiement) }}</span>
                          </div>
                        </div>

                        <!-- Additional Payments -->
                        <div v-for="(payment, index) in order.paiements" :key="payment.id" class="timeline-item">
                          <div :class="['timeline-dot', payment.valide === 'valid' ? '' : 'pending']"></div>
                          <div class="timeline-content">
                            <div class="timeline-header">
                              <span class="timeline-label">Additional payment #{{ index + 1 }}</span>
                              <span class="timeline-amount">{{ formatPrice(payment.montant) }}</span>
                              <span :class="['timeline-status', payment.valide === 'valid' ? 'validated' : 'pending']">
                                {{ payment.valide === 'valid' ? 'Validated' : 'Pending' }}
                              </span>
                            </div>
                            <span class="timeline-date">{{ formatDate(payment.date_paiement) }}</span>
                            <div v-if="payment.commentaire_admin" class="timeline-comment">
                              <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#666" stroke-width="2">
                                <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"></path>
                              </svg>
                              <span>{{ payment.commentaire_admin }}</span>
                            </div>
                            <button
                              v-if="payment.preuve_paiement"
                              class="view-proof-btn"
                              @click="viewPaymentProof(payment)"
                            >
                              <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
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

                  <!-- Production Section - Show in confirmed and ready stages -->
                  <div v-if="order.tobevalidate === 'valid' && order.statut !== 'livree' && order.statut !== 'annule'" v-show="['confirmed', 'ready'].includes(selectedLifecycleStage[order.id])" class="detail-section">
                    <div class="section-header">
                      <h4 class="section-title">
                        {{ order.statut === 'en_livraison' ? 'In delivery' : (order.statut === 'confirmee' && order.ready_for_shipping_date) ? 'Ready for delivery' : 'Preparing your order' }}
                      </h4>
                      <span v-if="order.statut === 'en_livraison'" class="production-badge shipping">Shipping</span>
                      <span v-else-if="!(order.statut === 'confirmee' && order.ready_for_shipping_date)" class="production-badge in-progress">In progress</span>
                      <span v-else class="production-badge ready">Ready</span>
                    </div>

                    <div v-if="order.statut !== 'en_livraison'" class="production-message">
                      <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#52c41a" stroke-width="2">
                        <polyline points="20 6 9 17 4 12"></polyline>
                      </svg>
                      <p v-if="!(order.statut === 'confirmee' && order.ready_for_shipping_date)">
                        Your order is currently in production. The supplier is preparing your order with care.
                        <strong>Estimated completion:</strong> {{ getEstimatedDeliveryDate(order) }}
                      </p>
                      <p v-else>
                        Good news! Your order is ready for delivery. The supplier has completed the preparation and it is now ready to be shipped.
                      </p>
                    </div>

                    <!-- Countdown Timer (if not ready and not in delivery) -->
                    <div v-if="order.statut !== 'en_livraison' && !(order.statut === 'confirmee' && order.ready_for_shipping_date) && order.estimate_prepare" class="countdown-section">
                      <h5 style="font-weight: 600; margin-bottom: 12px;">Time remaining:</h5>
                      <div class="countdown-display">
                        <div class="countdown-item">
                          <span class="countdown-value">{{ getRemainingTime(order).days }}</span>
                          <span class="countdown-label">days</span>
                        </div>
                        <span class="countdown-separator">:</span>
                        <div class="countdown-item">
                          <span class="countdown-value">{{ getRemainingTime(order).hours }}</span>
                          <span class="countdown-label">hours</span>
                        </div>
                        <span class="countdown-separator">:</span>
                        <div class="countdown-item">
                          <span class="countdown-value">{{ getRemainingTime(order).minutes }}</span>
                          <span class="countdown-label">min</span>
                        </div>
                      </div>

                      <div class="prep-progress-wrapper">
                        <div class="prep-progress-bar">
                          <div class="prep-progress-fill" :style="{ width: getProgressPercentage(order) + '%' }">
                            <span class="prep-progress-text">{{ Math.floor(getProgressPercentage(order)) }}%</span>
                          </div>
                        </div>
                        <p class="prep-progress-label">Preparation progress</p>
                      </div>

                      <div class="delivery-estimate">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#fe9700" stroke-width="2">
                          <rect x="1" y="3" width="15" height="13"></rect>
                          <polygon points="16 8 20 8 23 11 23 16 16 16 16 8"></polygon>
                          <circle cx="5.5" cy="18.5" r="2.5"></circle>
                          <circle cx="18.5" cy="18.5" r="2.5"></circle>
                        </svg>
                        <span>Estimated delivery: {{ getEstimatedDeliveryDate(order) }}</span>
                      </div>
                    </div>

                    <!-- Ready for Delivery - Payment Status Check -->
                    <div v-if="order.statut === 'confirmee' && order.ready_for_shipping_date" class="ready-delivery-section">
                      <!-- Scenario 1: Full payment complete (with or without against_bl_amount) -->
                      <div v-if="checkFullPaymentComplete(order)" class="ready-delivery-box success">
                        <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="#52c41a" stroke-width="2">
                          <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path>
                          <polyline points="22 4 12 14.01 9 11.01"></polyline>
                        </svg>
                        <div class="ready-delivery-content">
                          <h5>Your order will be delivered soon!</h5>
                          <p>Your payment has been confirmed and your order is ready for shipment. You will receive tracking information shortly.</p>
                          <!-- Show BL payment note only if not fully paid (Amount paid < Total order) -->
                          <div v-if="parseFloat(order.against_bl_amount || 0) > 0 && calculatePaymentStatus(order).totalPaid < order.total" class="bl-payment-note">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#1890ff" stroke-width="2">
                              <circle cx="12" cy="12" r="10"></circle>
                              <line x1="12" y1="16" x2="12" y2="12"></line>
                              <line x1="12" y1="8" x2="12.01" y2="8"></line>
                            </svg>
                            <span>You will pay the remaining <strong>{{ formatPrice(order.against_bl_amount) }}</strong> when the Bill of Lading (BL) is ready.</span>
                          </div>
                        </div>
                      </div>

                      <!-- Scenario 2: Partial payment - Need to complete payment -->
                      <div v-else class="ready-delivery-box warning">
                        <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="#faad14" stroke-width="2">
                          <circle cx="12" cy="12" r="10"></circle>
                          <line x1="12" y1="8" x2="12" y2="12"></line>
                          <line x1="12" y1="16" x2="12.01" y2="16"></line>
                        </svg>
                        <div class="ready-delivery-content">
                          <h5>Your product is ready for delivery!</h5>
                          <p>Your order is prepared and ready to ship. However, you need to complete the remaining payment to proceed with delivery.</p>

                          <div class="payment-breakdown-card">
                            <div class="breakdown-row">
                              <span>Total order:</span>
                              <strong>{{ formatPrice(order.total) }}</strong>
                            </div>
                            <div class="breakdown-row">
                              <span>Amount paid:</span>
                              <strong class="paid">{{ formatPrice(calculatePaymentStatus(order).totalPaid) }}</strong>
                            </div>
                            <div v-if="parseFloat(order.against_bl_amount || 0) > 0" class="breakdown-row">
                              <span>Payment against BL:</span>
                              <strong class="bl-amount">{{ formatPrice(order.against_bl_amount) }}</strong>
                            </div>
                            <div class="breakdown-row total">
                              <span>Remaining to pay now:</span>
                              <strong class="remaining">{{ formatPrice(getRemainingPaymentRequired(order)) }}</strong>
                            </div>
                          </div>

                          <div class="payment-action-box">
                            <p class="action-message">Please complete your payment to unlock delivery.</p>
                            <button class="action-btn-payment" @click="openAdditionalPaymentModal(order)">
                              <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <rect x="2" y="7" width="20" height="14" rx="2" ry="2"></rect>
                                <path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"></path>
                              </svg>
                              Complete Payment
                            </button>
                          </div>

                          <div v-if="parseFloat(order.against_bl_amount || 0) > 0" class="bl-payment-info">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#1890ff" stroke-width="2">
                              <circle cx="12" cy="12" r="10"></circle>
                              <line x1="12" y1="16" x2="12" y2="12"></line>
                              <line x1="12" y1="8" x2="12.01" y2="8"></line>
                            </svg>
                            <span>Note: The amount of <strong>{{ formatPrice(order.against_bl_amount) }}</strong> will be paid when the Bill of Lading (BL) is ready.</span>
                          </div>
                        </div>
                      </div>

                    </div>

                    <!-- Shipping Status - Product in delivery (en_livraison) -->
                    <div v-if="order.statut === 'en_livraison'" v-show="selectedLifecycleStage[order.id] === 'shipping'" class="shipping-status-section">
                      <div class="shipping-status-box">
                        <div class="shipping-status-header">
                          <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="#1890ff" stroke-width="2">
                            <rect x="1" y="3" width="15" height="13"></rect>
                            <polygon points="16 8 20 8 23 11 23 16 16 16 16 8"></polygon>
                            <circle cx="5.5" cy="18.5" r="2.5"></circle>
                            <circle cx="18.5" cy="18.5" r="2.5"></circle>
                          </svg>
                          <div class="shipping-status-content">
                            <h5>Your product has left our factory and is being delivered</h5>
                            <p>Your order is on its way! We are working to get it to you as soon as possible.</p>
                          </div>
                        </div>

                        <!-- CIF Shipping Information -->
                        <div v-if="order.delivery_method === 'CIF'" class="cif-shipping-info-section">
                          <div class="shipping-info-header">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#1890ff" stroke-width="2">
                              <circle cx="12" cy="12" r="10"></circle>
                              <path d="M12 6v6l4 2"></path>
                            </svg>
                            <h6>Shipping Information (CIF)</h6>
                          </div>

                          <div class="shipping-details-grid">
                            <div class="shipping-detail-item">
                              <span class="detail-label">Ship IMO:</span>
                              <span class="detail-value">{{ order.ship_imo || 'N/A' }}</span>
                            </div>
                            <div class="shipping-detail-item">
                              <span class="detail-label">Ship Name:</span>
                              <span class="detail-value">{{ order.ship_name || 'N/A' }}</span>
                            </div>
                            <div v-if="order.ship_tracking_url" class="shipping-detail-item full-width">
                              <span class="detail-label">Tracking URL:</span>
                              <a :href="order.ship_tracking_url" target="_blank" class="tracking-link">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                  <path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"></path>
                                  <polyline points="15 3 21 3 21 9"></polyline>
                                  <line x1="10" y1="14" x2="21" y2="3"></line>
                                </svg>
                                Track your shipment
                              </a>
                            </div>
                          </div>

                          <!-- Bill of Lading Section -->
                          <div class="bl-section">
                            <div class="bl-header">
                              <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#52c41a" stroke-width="2">
                                <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                                <polyline points="14 2 14 8 20 8"></polyline>
                                <line x1="16" y1="13" x2="8" y2="13"></line>
                                <line x1="16" y1="17" x2="8" y2="17"></line>
                                <polyline points="10 9 9 9 8 9"></polyline>
                              </svg>
                              <span class="bl-title">Bill of Lading (BL)</span>
                            </div>

                            <div class="bl-info-grid">
                              <div v-if="order.bl_number" class="bl-info-item">
                                <span class="bl-label">BL Number:</span>
                                <span class="bl-value">{{ order.bl_number }}</span>
                              </div>
                              <div v-if="order.bl_uploaded_date" class="bl-info-item">
                                <span class="bl-label">Uploaded Date:</span>
                                <span class="bl-value">{{ formatDate(order.bl_uploaded_date) }}</span>
                              </div>
                            </div>

                            <button v-if="order.bl_url" class="download-bl-btn" @click="downloadBL(order.bl_url, order.numero_commande)">
                              <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
                                <polyline points="7 10 12 15 17 10"></polyline>
                                <line x1="12" y1="15" x2="12" y2="3"></line>
                              </svg>
                              Download Bill of Lading
                            </button>
                            <p v-else class="bl-pending-message">
                              <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#faad14" stroke-width="2">
                                <circle cx="12" cy="12" r="10"></circle>
                                <line x1="12" y1="16" x2="12" y2="12"></line>
                                <line x1="12" y1="8" x2="12.01" y2="8"></line>
                              </svg>
                              Bill of Lading will be available soon
                            </p>
                          </div>
                        </div>
                      </div>
                    </div>

                    <!-- Old Ready for Delivery (if prepare_date exists - keep for backward compatibility) -->
                    <div v-if="order.prepare_date && calculatePaymentStatus(order).percentage >= 100 && !(order.statut === 'confirmee' && order.ready_for_shipping_date)" class="ready-delivery-section">
                      <div class="ready-delivery-box">
                        <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="#52c41a" stroke-width="2">
                          <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path>
                          <polyline points="22 4 12 14.01 9 11.01"></polyline>
                        </svg>
                        <div class="ready-delivery-content">
                          <h5>Your order is ready!</h5>
                          <p>The preparation is complete. Please confirm your delivery address to proceed with shipping.</p>
                          <button class="confirm-address-btn" @click="confirmDeliveryAddress(order)">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                              <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path>
                              <circle cx="12" cy="10" r="3"></circle>
                            </svg>
                            Confirm the delivery address
                          </button>
                        </div>
                      </div>
                    </div>
                  </div>

                  <!-- Delivered Order Section -->
                  <div v-if="order.statut === 'livree'" v-show="selectedLifecycleStage[order.id] === 'delivered'" class="detail-section">
                    <div class="delivered-order-section">
                      <div class="delivered-header">
                        <div class="success-checkmark">
                          <svg width="64" height="64" viewBox="0 0 24 24" fill="none">
                            <circle cx="12" cy="12" r="10" fill="#52c41a" opacity="0.1"/>
                            <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14" stroke="#52c41a" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            <polyline points="22 4 12 14.01 9 11.01" stroke="#52c41a" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                          </svg>
                        </div>
                        <h3 class="delivered-title">Commande terminée</h3>
                        <p class="delivered-message">Your order has been successfully delivered. Thank you for your purchase!</p>
                      </div>

                      <div class="delivered-details-card">
                        <div class="delivered-info-row">
                          <div class="info-icon">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#52c41a" stroke-width="2">
                              <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                              <circle cx="12" cy="7" r="4"></circle>
                            </svg>
                          </div>
                          <div class="info-content">
                            <span class="info-label">Order number</span>
                            <span class="info-value">#{{ order.numero_commande }}</span>
                          </div>
                        </div>

                        <div class="delivered-info-row">
                          <div class="info-icon">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#52c41a" stroke-width="2">
                              <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                              <line x1="16" y1="2" x2="16" y2="6"></line>
                              <line x1="8" y1="2" x2="8" y2="6"></line>
                              <line x1="3" y1="10" x2="21" y2="10"></line>
                            </svg>
                          </div>
                          <div class="info-content">
                            <span class="info-label">Order date</span>
                            <span class="info-value">{{ formatDate(order.date_commande) }}</span>
                          </div>
                        </div>

                        <div v-if="order.delivered_date" class="delivered-info-row">
                          <div class="info-icon">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#52c41a" stroke-width="2">
                              <rect x="1" y="3" width="15" height="13"></rect>
                              <polygon points="16 8 20 8 23 11 23 16 16 16 16 8"></polygon>
                              <circle cx="5.5" cy="18.5" r="2.5"></circle>
                              <circle cx="18.5" cy="18.5" r="2.5"></circle>
                            </svg>
                          </div>
                          <div class="info-content">
                            <span class="info-label">Delivery date</span>
                            <span class="info-value">{{ formatDate(order.delivered_date) }}</span>
                          </div>
                        </div>

                        <div class="delivered-info-row total-row">
                          <div class="info-icon">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#52c41a" stroke-width="2">
                              <line x1="12" y1="1" x2="12" y2="23"></line>
                              <path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"></path>
                            </svg>
                          </div>
                          <div class="info-content">
                            <span class="info-label">Total amount</span>
                            <span class="info-value total-amount">{{ formatPrice(order.total) }}</span>
                          </div>
                        </div>
                      </div>

                      <div class="delivered-footer">
                        <p class="footer-text">We hope you are satisfied with your purchase. Feel free to contact us if you have any questions.</p>
                      </div>
                    </div>
                  </div>

                  </div><!-- End stage-content for confirmed/ready/shipping/delivered -->

                  <!-- Action Buttons -->
                  <div class="action-buttons-section">
                    <button class="action-btn-secondary" @click="handleChatClick(order)">
                      <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"></path>
                      </svg>
                      Contact the seller
                    </button>

                    <button v-if="order.statut === 'en_attente' && !order.preuve_paiement" class="action-btn-danger" @click="openCancelModal(order)">
                      <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <circle cx="12" cy="12" r="10"></circle>
                        <line x1="15" y1="9" x2="9" y2="15"></line>
                        <line x1="9" y1="9" x2="15" y2="15"></line>
                      </svg>
                      Cancel Order
                    </button>

                    <button v-if="order.statut === 'send' && !order.preuve_paiement" class="action-btn-primary" @click="openPaymentProofModal(order)">
                      <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
                        <polyline points="17 8 12 3 7 8"></polyline>
                        <line x1="12" y1="3" x2="12" y2="15"></line>
                      </svg>
                      Upload Payment Proof
                    </button>

                    <button v-if="order.preuve_paiement" class="action-btn-secondary" @click="viewPaymentProof(order)">
                      <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                        <circle cx="12" cy="12" r="3"></circle>
                      </svg>
                      See the proof
                    </button>

                    <button v-if="order.tobevalidate === 'valid' && calculatePaymentStatus(order).remaining > 0" class="action-btn-primary" @click="openAdditionalPaymentModal(order)">
                      <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <line x1="12" y1="5" x2="12" y2="19"></line>
                        <line x1="5" y1="12" x2="19" y2="12"></line>
                      </svg>
                      Add a payment
                    </button>
                  </div>
                </div>
              </td>
            </tr>
          </template>
        </tbody>
      </table>
    </div>

    <!-- Payment Proof Modal -->
    <div v-if="showPaymentModal" class="modal-overlay" @click="closePaymentModal">
      <div class="modal-container modern-modal" @click.stop>
        <div class="modal-header">
          <div class="modal-header-content">
            <div class="modal-icon">
              <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#fe9700" stroke-width="2">
                <rect x="2" y="7" width="20" height="14" rx="2" ry="2"></rect>
                <path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"></path>
              </svg>
            </div>
            <div>
              <h3 class="modal-title">Upload Payment Proof</h3>
              <p class="modal-subtitle">Order #{{ selectedOrder?.numero_commande }}</p>
            </div>
          </div>
          <button class="modal-close" @click="closePaymentModal">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <line x1="18" y1="6" x2="6" y2="18"></line>
              <line x1="6" y1="6" x2="18" y2="18"></line>
            </svg>
          </button>
        </div>

        <div class="modal-body">
          <!-- Payment Summary Card -->
          <div class="payment-summary-card">
            <div class="summary-header">
              <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#fe9700" stroke-width="2">
                <line x1="12" y1="1" x2="12" y2="23"></line>
                <path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"></path>
              </svg>
              <span>Payment Summary</span>
            </div>
            <div class="summary-details">
              <div class="summary-row">
                <span>Total order:</span>
                <strong>{{ selectedOrder ? formatPrice(selectedOrder.total) : '' }}</strong>
              </div>
              <div class="summary-row highlight">
                <span>Minimum deposit ({{selectedOrder ? JSON.parse(selectedOrder.payment_terms).deposit_percent : 0}}%):</span>
                <strong class="amount-highlight">{{ selectedOrder ? formatPrice(selectedOrder.total * JSON.parse(selectedOrder.payment_terms).deposit_percent/100) : '' }}</strong>
              </div>
            </div>
          </div>

          <!-- Contract Signature Section -->
          <div class="contract-signature-section">
            <div class="signature-header">
              <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#1890ff" stroke-width="2">
                <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                <polyline points="14 2 14 8 20 8"></polyline>
                <line x1="16" y1="13" x2="8" y2="13"></line>
                <line x1="16" y1="17" x2="8" y2="17"></line>
                <polyline points="10 9 9 9 8 9"></polyline>
              </svg>
              <span>Contract Agreement</span>
            </div>

            <div class="contract-agreement-box">
              <div class="agreement-content">
                <p class="agreement-text">
                  I have read and understood the contract terms and conditions. I agree to make the payment according to the agreed terms.
                </p>

                <button
                  v-if="!contractSigned"
                  class="sign-contract-btn"
                  @click="signContract"
                  :disabled="signingContract"
                >
                  <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M20 6L9 17l-5-5"></path>
                  </svg>
                  {{ signingContract ? 'Signing...' : 'Sign Contract' }}
                </button>

                <!-- Signature Success Animation -->
                <div v-if="contractSigned" class="signature-success">
                  <div class="success-animation">
                    <div class="checkmark-circle">
                      <svg class="checkmark" width="48" height="48" viewBox="0 0 52 52">
                        <circle class="checkmark-circle-path" cx="26" cy="26" r="25" fill="none"/>
                        <path class="checkmark-check" fill="none" d="M14.1 27.2l7.1 7.2 16.7-16.8"/>
                      </svg>
                    </div>
                  </div>
                  <p class="success-message">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#52c41a" stroke-width="2">
                      <path d="M20 6L9 17l-5-5"></path>
                    </svg>
                    Contract signed successfully
                  </p>
                  <p class="signed-date">Signed on {{ formatDate(new Date().toISOString()) }}</p>
                </div>
              </div>
            </div>
          </div>

          <!-- Payment Details Form - Only enabled after contract is signed -->
          <div :class="['payment-details-section', { disabled: !contractSigned }]">
            <div class="form-group">
              <label class="form-label">
                Payment amount*
                <span v-if="!contractSigned" class="required-signature">(Sign contract first)</span>
              </label>
              <input
                v-model="depositPaymentAmount"
                type="number"
                class="form-input"
                placeholder="Enter the amount"
                :max="selectedOrder?.total"
                min="0"
                :disabled="!contractSigned"
              >
            </div>

            <div class="form-group">
              <label class="form-label">
                Payment proof (image)*
                <span v-if="!contractSigned" class="required-signature">(Sign contract first)</span>
              </label>
              <div class="file-upload-area" :class="{ disabled: !contractSigned }">
                <input
                  ref="fileInput"
                  type="file"
                  accept="image/*"
                  class="file-input-hidden"
                  @change="handleFileSelect"
                  :disabled="!contractSigned"
                >
                <button
                  class="file-upload-btn"
                  @click="!contractSigned ? null : $refs.fileInput.click()"
                  :disabled="!contractSigned"
                >
                  <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
                    <polyline points="17 8 12 3 7 8"></polyline>
                    <line x1="12" y1="3" x2="12" y2="15"></line>
                  </svg>
                  Choose file
                </button>
                <p v-if="selectedFile" class="file-name-display">
                  <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#52c41a" stroke-width="2">
                    <path d="M20 6L9 17l-5-5"></path>
                  </svg>
                  {{ selectedFile.name }}
                </p>
                <p v-else class="file-placeholder">No file selected</p>
              </div>
            </div>

            <div class="form-group">
              <label class="form-label">Comment (optional)</label>
              <textarea
                v-model="paymentComment"
                class="form-textarea"
                rows="3"
                placeholder="Add a comment about your payment..."
                :disabled="!contractSigned"
              ></textarea>
            </div>
          </div>

          <div v-if="uploading" class="upload-progress">
            <div class="progress-bar-container">
              <div class="progress-bar-fill" :style="{ width: uploadProgress + '%' }"></div>
            </div>
            <p class="progress-text">
              <svg class="spinning" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <line x1="12" y1="2" x2="12" y2="6"></line>
                <line x1="12" y1="18" x2="12" y2="22"></line>
                <line x1="4.93" y1="4.93" x2="7.76" y2="7.76"></line>
                <line x1="16.24" y1="16.24" x2="19.07" y2="19.07"></line>
                <line x1="2" y1="12" x2="6" y2="12"></line>
                <line x1="18" y1="12" x2="22" y2="12"></line>
                <line x1="4.93" y1="19.07" x2="7.76" y2="16.24"></line>
                <line x1="16.24" y1="7.76" x2="19.07" y2="4.93"></line>
              </svg>
              Uploading... {{ uploadProgress }}%
            </p>
          </div>
        </div>

        <div class="modal-footer">
          <button class="btn-secondary" @click="closePaymentModal" :disabled="uploading">Cancel</button>
          <button
            class="btn-primary"
            @click="uploadPaymentProof"
            :disabled="uploading || !selectedFile || !depositPaymentAmount || !contractSigned"
          >
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path>
              <polyline points="22 4 12 14.01 9 11.01"></polyline>
            </svg>
            {{ uploading ? 'Uploading...' : 'Submit Payment Proof' }}
          </button>
        </div>
      </div>
    </div>

    <!-- Additional Payment Modal -->
    <div v-if="showAdditionalPaymentModal" class="modal-overlay" @click="closeAdditionalPaymentModal">
      <div class="modal-container" @click.stop>
        <div class="modal-header">
          <h3 class="modal-title">Add an additional payment</h3>
          <button class="modal-close" @click="closeAdditionalPaymentModal">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <line x1="18" y1="6" x2="6" y2="18"></line>
              <line x1="6" y1="6" x2="18" y2="18"></line>
            </svg>
          </button>
        </div>

        <div class="modal-body">
          <p style="margin-bottom: 16px; color: #666;">Order #{{ selectedOrder?.numero_commande }}</p>

          <div class="payment-info-box">
            <div class="info-row">
              <span>Total order:</span>
              <strong>{{ selectedOrder ? formatPrice(calculatePaymentStatus(selectedOrder).total) : '' }}</strong>
            </div>
            <div class="info-row">
              <span>Already paid:</span>
              <strong style="color: #52c41a;">{{ selectedOrder ? formatPrice(calculatePaymentStatus(selectedOrder).totalPaid) : '' }}</strong>
            </div>
            <div class="info-row">
              <span>Remaining:</span>
              <strong style="color: #fa8c16;">{{ selectedOrder ? formatPrice(calculatePaymentStatus(selectedOrder).remaining) : '' }}</strong>
            </div>
          </div>

          <div class="form-group">
            <label class="form-label">Payment amount*</label>
            <input
              v-model="additionalPaymentAmount"
              type="number"
              class="form-input"
              placeholder="Enter the amount"
              :max="selectedOrder ? calculatePaymentStatus(selectedOrder).remaining : 0"
              min="0"
            >
          </div>

          <div class="form-group">
            <label class="form-label">Payment proof (image)*</label>
            <input
              ref="additionalPaymentFileInput"
              type="file"
              accept="image/*"
              class="file-input"
              @change="handleAdditionalPaymentFileSelect"
            >
            <p v-if="additionalPaymentFile" class="file-name">{{ additionalPaymentFile.name }}</p>
          </div>

          <div class="form-group">
            <label class="form-label">Comment (optional)</label>
            <textarea
              v-model="additionalPaymentComment"
              class="form-textarea"
              rows="3"
              placeholder="Add a comment..."
            ></textarea>
          </div>

          <div v-if="uploading" class="upload-progress">
            <div class="progress-bar">
              <div class="progress-fill" :style="{ width: uploadProgress + '%' }"></div>
            </div>
            <p class="progress-text">Upload: {{ uploadProgress }}%</p>
          </div>
        </div>

        <div class="modal-footer">
          <button class="btn-secondary" @click="closeAdditionalPaymentModal">Cancel</button>
          <button
            class="btn-primary"
            @click="uploadAdditionalPayment"
            :disabled="uploading || !additionalPaymentFile || !additionalPaymentAmount"
          >
            {{ uploading ? 'Uploading...' : 'Send payment' }}
          </button>
        </div>
      </div>
    </div>

    <!-- Cancel Order Modal -->
    <div v-if="showCancelModal" class="modal-overlay" @click="closeCancelModal">
      <div class="modal-container" @click.stop>
        <div class="modal-header">
          <div class="modal-icon-warning">
            <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="#ff4d4f" stroke-width="2">
              <circle cx="12" cy="12" r="10"></circle>
              <line x1="15" y1="9" x2="9" y2="15"></line>
              <line x1="9" y1="9" x2="15" y2="15"></line>
            </svg>
          </div>
          <h3 class="modal-title">Cancel the order</h3>
        </div>

        <div class="modal-body">
          <p class="modal-message">Are you sure you want to cancel order #{{ selectedOrder?.numero_commande }}? This action is irreversible.</p>

          <div class="form-group">
            <label class="form-label">Reason for cancellation{{ selectedOrder?.preuve_paiement || (selectedOrder?.paiements && selectedOrder.paiements.length > 0) ? '*' : ' (optional)' }}</label>
            <textarea
              v-model="cancelReason"
              class="form-textarea"
              rows="4"
              placeholder="Please explain why you want to cancel this order..."
            ></textarea>
          </div>
        </div>

        <div class="modal-footer">
          <button class="btn-secondary" @click="closeCancelModal">Back</button>
          <button
            class="btn-danger"
            @click="cancelOrder"
            :disabled="cancelling"
          >
            {{ cancelling ? 'Cancelling...' : 'Confirm the cancellation' }}
          </button>
        </div>
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

// Contract signature state
const contractSigned = ref(false)
const signingContract = ref(false)

// Expandable table state
const expandedOrders = ref(new Set())

// Lifecycle tracking for each order
const selectedLifecycleStage = ref({}) // { orderId: stageKey }

// Order lifecycle steps for client view
const orderLifecycleSteps = [
  { key: 'pending', label: 'Pending', emoji: '⏳', description: 'Order placed' },
  { key: 'contract', label: 'Contract', emoji: '📄', description: 'Contract sent' },
  { key: 'confirmed', label: 'Confirmed', emoji: '✅', description: 'Order confirmed' },
  { key: 'ready', label: 'Ready', emoji: '⚙️', description: 'Ready for shipping' },
  { key: 'shipping', label: 'Shipping', emoji: '🚚', description: 'In transit' },
  { key: 'delivered', label: 'Delivered', emoji: '🎉', description: 'Completed' }
]

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

const filteredOrders = computed(() => {
  if (selectedStatus.value === 'all') {
    return orders.value
  }
  return orders.value.filter(order => order.statut === selectedStatus.value)
})

const getOrderCountByStatus = (status) => {
  if (status === 'all') return orders.value.length
  return orders.value.filter(order => order.statut === status).length
}

const getStatusClass = (status) => {
  const statusMap = {
    'en_attente': 'pending',
    'confirmee': 'confirmed',
    'send': 'contract-sent',
    'en_cours': 'shipping',
    'livree': 'delivered',
    'annule': 'cancelled',
    'terminee': 'delivered'
  }
  return statusMap[status] || 'pending'
}

const getStatusLabel = (status) => {
  const labelMap = {
    'en_attente': 'Pending',
    'confirmee': 'Confirmed',
    'send': 'Contract sent',
    'en_cours': 'In Progress',
    'livree': 'Delivered',
    'annule': 'Cancelled',
    'terminee': 'Finished'
  }
  return labelMap[status] || status
}

const downloadContract = async (orderNumber) => {
  try {
    // Find the order to get all details
    const order = orders.value.find(o => o.numero_commande === orderNumber)
    if (!order) {
      alert('Order not found')
      return
    }

    // Create contract content with all pricing details
    const paymentTerms = JSON.parse(order.payment_terms)
    const contractContent = `
CONTRACT - ORDER #${order.numero_commande}
================================

PRODUCT INFORMATION:
- Product: ${order.produit_nom}
- Quantity: ${order.quantite}
- Boutique: ${order.boutique_nom}

PRICING DETAILS:
- Sub total: ${formatPrice(order.sous_total)}
${order.frais_livraison > 0 ? `- Delivery fees: ${formatPrice(order.frais_livraison)}` : ''}
${order.delivery_method === 'CIF' && order.shipping_cost ? `
INTERNATIONAL SHIPPING (CIF):
- Shipping Cost: ${formatPrice(order.shipping_cost)}
- Loading Port: ${order.loading_port || 'N/A'}
- Destination Port: ${order.destination_port || 'N/A'}
` : ''}
- TOTAL: ${formatPrice(order.total)}

PAYMENT TERMS:
- Deposit percentage: ${paymentTerms.deposit_percent}%
- Minimum deposit amount: ${formatPrice(order.total * paymentTerms.deposit_percent / 100)}
- Remaining balance: ${formatPrice(order.total - (order.total * paymentTerms.deposit_percent / 100))}

ORDER DATE: ${formatDate(order.date_commande)}
STATUS: ${getStatusLabel(order.statut)}

================================
This contract is valid for order #${order.numero_commande}
Generated on: ${new Date().toLocaleDateString('fr-FR')}
    `.trim()

    // Create a Blob with the contract content
    const blob = new Blob([contractContent], { type: 'text/plain' })
    const url = window.URL.createObjectURL(blob)

    // Create a temporary link and trigger download
    const link = document.createElement('a')
    link.href = url
    link.download = `Contract_Order_${orderNumber}.txt`
    document.body.appendChild(link)
    link.click()

    // Cleanup
    document.body.removeChild(link)
    window.URL.revokeObjectURL(url)
  } catch (error) {
    console.error('Error downloading contract:', error)
    alert('Error downloading contract. Please try again.')
  }
}

// Download Bill of Lading (BL)
const downloadBL = async (blUrl, orderNumber) => {
  try {
    if (!blUrl) {
      alert('Bill of Lading not available yet')
      return
    }

    // Create a temporary link and trigger download
    const link = document.createElement('a')
    link.href = blUrl
    link.download = `BL_Order_${orderNumber}.pdf`
    link.target = '_blank'
    document.body.appendChild(link)
    link.click()

    // Cleanup
    document.body.removeChild(link)
  } catch (error) {
    console.error('Error downloading BL:', error)
    alert('Error downloading Bill of Lading. Please try again.')
  }
}

// Toggle expand/collapse
const toggleExpand = (orderId) => {
  if (expandedOrders.value.has(orderId)) {
    expandedOrders.value.delete(orderId)
    delete selectedLifecycleStage.value[orderId]
  } else {
    expandedOrders.value.add(orderId)
    // Set to current active stage by default
    const order = orders.value.find(o => o.id === orderId)
    if (order) {
      const currentStage = getCurrentStage(order)
      selectedLifecycleStage.value[orderId] = currentStage
    }
  }
}

// Lifecycle stage functions
const getCurrentStageIndex = (order) => {
  if (!order) return 0
  if (order.statut === 'livree') return 5 // delivered
  if (order.statut === 'en_livraison') return 4 // shipping
  if (order.statut === 'confirmee' && order.ready_for_shipping_date) return 3 // ready
  if (order.statut === 'confirmee') return 2 // confirmed
  if (order.statut === 'send') return 1 // contract
  return 0 // pending
}

const getCurrentStage = (order) => {
  const stages = ['pending', 'contract', 'confirmed', 'ready', 'shipping', 'delivered']
  return stages[getCurrentStageIndex(order)]
}

const isStageComplete = (order, stage) => {
  const stages = ['pending', 'contract', 'confirmed', 'ready', 'shipping', 'delivered']
  const currentStageIndex = getCurrentStageIndex(order)
  const stageIndex = stages.indexOf(stage)
  return stageIndex < currentStageIndex
}

const isStageActive = (order, stage) => {
  return getCurrentStage(order) === stage
}

const selectLifecycleStage = (orderId, stageKey) => {
  selectedLifecycleStage.value[orderId] = stageKey
}

const isStageClickable = (order, stage) => {
  // Can only click on completed or active stages
  return isStageComplete(order, stage) || isStageActive(order, stage)
}

const formatDate = (dateString) => {
  if (!dateString) return 'N/A'
  const date = new Date(dateString)
  return date.toLocaleDateString('fr-FR', {
    weekday: 'long',
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
    return { days: 0, hours: 0, minutes: 0 }
  }

  const days = Math.floor(diff / (1000 * 60 * 60 * 24))
  const hours = Math.floor((diff % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60))
  const minutes = Math.floor((diff % (1000 * 60 * 60)) / (1000 * 60))

  return { days, hours, minutes }
}

const getEstimatedDeliveryDate = (order) => {
  let deliveryDate
  if (order.estimate_prepare) {
    deliveryDate = new Date(order.estimate_prepare)
  } else {
    const updateDate = new Date(order.updated_at)
    deliveryDate = new Date(updateDate.getTime() + (14 * 24 * 60 * 60 * 1000))
  }

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

  const startDate = new Date(order.date_paiement || order.updated_at)
  const now = currentTime.value
  const totalTime = deliveryDate - startDate
  const elapsed = now - startDate

  if (elapsed <= 0) return 0
  if (elapsed >= totalTime) return 100

  return Math.min(100, (elapsed / totalTime) * 100)
}

const calculatePaymentStatus = (order) => {
  const total = parseFloat(order.total || 0)

  // 🔥 Récupération du pourcentage de dépôt
  const paymentTerms = JSON.parse(order.payment_terms || "{}")
  const depositPercent = (paymentTerms.deposit_percent || 0) / 100

  // 🔥 Calcul du premier paiement basé sur deposit_percent
  const firstPayment = parseFloat(order.montant_initial || 0) || (total * depositPercent)

  let totalAdditionalPaid = 0
  if (order.paiements && Array.isArray(order.paiements)) {
    totalAdditionalPaid = order.paiements
      .filter(p => p.valide === 'valid')
      .reduce((sum, p) => sum + parseFloat(p.montant || 0), 0)
  }

  const totalPaid = firstPayment + totalAdditionalPaid
  const remaining = Math.max(0, total - totalPaid)
  const percentage = total > 0 ? (totalPaid / total) * 100 : 0

  return { total, firstPayment, totalAdditionalPaid, totalPaid, remaining, percentage }
}

const handleImageError = (event) => {
  event.target.src = '/placeholder.svg'
}

const fetchOrders = async () => {
  loading.value = true
  try {
    const userData = localStorage.getItem('user') || sessionStorage.getItem('user')
    if (!userData) {
      console.error('No user data found')
      orders.value = []
      return
    }

    const parsedUser = JSON.parse(userData)
    const userId = parsedUser.id

    console.log('Fetching orders for user:', userId)
    const response = await ordersApi.getMyOrders(userId)
    console.log('Orders response:', response)

    if (response.success) {
      orders.value = response.data || []
    } else {
      console.error('Failed to fetch orders:', response.error)
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
  depositPaymentAmount.value = ''
  paymentComment.value = ''
  uploadProgress.value = 0
}

const closePaymentModal = () => {
  showPaymentModal.value = false
  selectedOrder.value = null
  selectedFile.value = null
  depositPaymentAmount.value = ''
  paymentComment.value = ''
  uploadProgress.value = 0
  contractSigned.value = false
  signingContract.value = false
}

// Sign contract function with animation
const signContract = () => {
  signingContract.value = true

  // Simulate signing process with delay for animation
  setTimeout(() => {
    contractSigned.value = true
    signingContract.value = false
  }, 800)
}

// Check if full payment is complete based on against_bl_amount
const checkFullPaymentComplete = (order) => {
  const paymentStatus = calculatePaymentStatus(order)
  const againstBlAmount = parseFloat(order.against_bl_amount || 0)

  if (againstBlAmount > 0) {
    // If there's a BL amount, check if amount paid equals total - BL amount
    const requiredPayment = order.total - againstBlAmount
    return paymentStatus.totalPaid >= requiredPayment
  } else {
    // Otherwise, check if full payment is complete
    return paymentStatus.totalPaid >= order.total
  }
}

// Get remaining payment required (excluding against_bl_amount)
const getRemainingPaymentRequired = (order) => {
  const paymentStatus = calculatePaymentStatus(order)
  const againstBlAmount = parseFloat(order.against_bl_amount || 0)
  const requiredPayment = order.total - againstBlAmount
  const remaining = requiredPayment - paymentStatus.totalPaid

  return remaining > 0 ? remaining : 0
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
  try {
    // Créer un objet produit avec les infos de la commande
    const product = {
      id: order.produit_id,
      name: order.produit_nom,
      primary_image: order.produit_image,
      unit_price: order.produit_prix,
      boutique_id: order.fournisseur_id,
      boutique_name: order.boutique_nom,
      rating: 0
    }

    console.log('📞 Ouverture du chat avec le vendeur:', product)

    // Utiliser setSupplier pour créer/ouvrir la conversation avec le vendeur
    await chatStore.setSupplier(product)

    // Ouvrir le chat en fonction de la taille de l'écran
    const isMobile = window.innerWidth <= 768
    if (isMobile) {
      await chatStore.openChat()
    } else {
      await chatStore.openDesktopChat()
    }
  } catch (error) {
    console.error('❌ Erreur ouverture du chat:', error)
    alert('Impossible d\'ouvrir le chat. Veuillez réessayer.')
  }
}

const handleFileSelect = (event) => {
  const file = event.target.files[0]
  if (!file) return

  if (file.size > 10 * 1024 * 1024) {
    alert('The file is too large. Maximum 10MB.')
    event.target.value = ''
    return
  }

  if (!file.type.startsWith('image/')) {
    alert('Please select an image file.')
    event.target.value = ''
    return
  }

  selectedFile.value = file
}

const handleAdditionalPaymentFileSelect = (event) => {
  const file = event.target.files[0]
  if (!file) return

  if (file.size > 10 * 1024 * 1024) {
    alert('The file is too large. Maximum 10MB.')
    event.target.value = ''
    return
  }

  if (!file.type.startsWith('image/')) {
    alert('Please select an image file.')
    event.target.value = ''
    return
  }

  additionalPaymentFile.value = file
}

const uploadToCloudinary = async (file) => {
  const formData = new FormData()
  formData.append('file', file)
  formData.append('upload_preset', cloudinaryConfig.uploadPreset)
  formData.append('api_key', cloudinaryConfig.apiKey)

  try {
    const response = await axios.post(cloudinaryConfig.imageUploadUrl, formData, {
      onUploadProgress: (progressEvent) => {
        const percentCompleted = Math.round((progressEvent.loaded * 100) / progressEvent.total)
        uploadProgress.value = percentCompleted
      }
    })

    if (response.data && response.data.secure_url) {
      return response.data.secure_url
    } else {
      throw new Error('Failed to upload image')
    }
  } catch (error) {
    console.error('Cloudinary upload error:', error)
    throw error
  }
}

const uploadPaymentProof = async () => {
  if (!selectedFile.value || !depositPaymentAmount.value) {
    alert('Please fill in all required fields')
    return
  }

  const amount = parseFloat(depositPaymentAmount.value)
  if (amount <= 0 || amount > selectedOrder.value.total) {
    alert('Invalid payment amount')
    return
  }

  uploading.value = true
  try {
    const imageUrl = await uploadToCloudinary(selectedFile.value)
    const response = await ordersApi.uploadPaymentProof(selectedOrder.value.id, {
      montant: amount,
      preuve_paiement: imageUrl,
      commentaire: paymentComment.value
    })

    if (response.success) {
      alert('Payment proof uploaded successfully!')
      closePaymentModal()
      await fetchOrders()
    } else {
      alert('Error uploading payment proof: ' + (response.error || 'Unknown error'))
    }
  } catch (error) {
    console.error('Error:', error)
    alert('Error uploading payment proof')
  } finally {
    uploading.value = false
  }
}

const uploadAdditionalPayment = async () => {
  if (!additionalPaymentFile.value || !additionalPaymentAmount.value) {
    alert('Please fill in all required fields')
    return
  }

  const amount = parseFloat(additionalPaymentAmount.value)
  const paymentStatus = calculatePaymentStatus(selectedOrder.value)

  if (amount <= 0 || amount > paymentStatus.remaining) {
    alert('Invalid payment amount')
    return
  }

  uploading.value = true
  try {
    const imageUrl = await uploadToCloudinary(additionalPaymentFile.value)

    const response = await ordersApi.uploadAdditionalPayment(selectedOrder.value.id, {
      montant: amount,
      preuve_paiement: imageUrl,
      commentaire: additionalPaymentComment.value
    })

    if (response.success) {
      alert('Additional payment uploaded successfully!')
      closeAdditionalPaymentModal()
      await fetchOrders()
    } else {
      alert('Error uploading additional payment: ' + (response.error || 'Unknown error'))
    }
  } catch (error) {
    console.error('Error:', error)
    alert('Error uploading additional payment')
  } finally {
    uploading.value = false
  }
}

const viewPaymentProof = (order) => {
  if (order.preuve_paiement) {
    window.open(order.preuve_paiement, '_blank')
  }
}

const openCancelModal = (order) => {
  selectedOrder.value = order
  cancelReason.value = ''
  showCancelModal.value = true
}

const closeCancelModal = () => {
  showCancelModal.value = false
  selectedOrder.value = null
  cancelReason.value = ''
}

const cancelOrder = async () => {
  if (!selectedOrder.value) return

  if ((selectedOrder.value.preuve_paiement || (selectedOrder.value.paiements && selectedOrder.value.paiements.length > 0)) && !cancelReason.value.trim()) {
    alert('Please provide a reason for cancellation')
    return
  }

  cancelling.value = true
  try {
    const response = await ordersApi.cancelOrder(selectedOrder.value.id, cancelReason.value)

    if (response.success) {
      alert('Order cancelled successfully!')
      closeCancelModal()
      await fetchOrders()
    } else {
      alert('Error cancelling order: ' + (response.error || 'Unknown error'))
    }
  } catch (error) {
    console.error('Error:', error)
    alert('Error cancelling order')
  } finally {
    cancelling.value = false
  }
}

const confirmDeliveryAddress = async (order) => {
  const confirmed = window.confirm(
    `Please confirm your delivery address:\n\n${order.adresse_complete}\n\nIs this address correct?`
  )

  if (confirmed) {
    alert('Delivery address confirmed! The seller will proceed with shipping.')
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
.my-orders-container {
  min-height: 100vh;
  background: linear-gradient(135deg, #f5f7fa 0%, #f0f2f5 100%);
  padding-bottom: 40px;
}

/* Header */
.orders-header {
  background: linear-gradient(135deg, #ffffff 0%, #fafbfc 100%);
  border-bottom: 2px solid #e8ecf1;
  box-shadow: 0 4px 20px rgba(0, 0, 0, 0.06);
  padding: 32px 24px;
}

.header-content {
  max-width: 1400px;
  margin: 0 auto;
}

.header-title-section {
  text-align: center;
}

.main-title {
  font-size: 32px;
  font-weight: 700;
  color: #1a1a1a;
  margin: 0 0 8px 0;
  background: linear-gradient(135deg, #fe9700 0%, #ff6b00 100%);
  -webkit-background-clip: text;
  -webkit-text-fill-color: transparent;
  background-clip: text;
}

.subtitle {
  font-size: 16px;
  color: #666;
  margin: 0;
}

/* Filters */
.filters-container {
  max-width: 1400px;
  margin: 24px auto;
  padding: 0 24px;
}

.filters-wrapper {
  display: flex;
  gap: 12px;
  flex-wrap: wrap;
  background: #fff;
  padding: 16px;
  border-radius: 12px;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.06);
}

.filter-btn {
  display: flex;
  align-items: center;
  gap: 8px;
  padding: 10px 20px;
  background: #f5f5f5;
  border: 2px solid transparent;
  border-radius: 10px;
  color: #666;
  font-size: 14px;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.3s ease;
}

.filter-btn:hover {
  background: #fff7e6;
  color: #fe9700;
  border-color: #fe9700;
}

.filter-btn.active {
  background: linear-gradient(135deg, #fe9700 0%, #ff8c00 100%);
  color: #fff;
  border-color: #fe9700;
  box-shadow: 0 4px 12px rgba(254, 151, 0, 0.3);
}

.count-badge {
  background: rgba(255, 255, 255, 0.3);
  padding: 2px 8px;
  border-radius: 12px;
  font-size: 12px;
  font-weight: 700;
}

.filter-btn.active .count-badge {
  background: rgba(255, 255, 255, 0.9);
  color: #fe9700;
}

/* Loading & Empty */
.loading-container {
  max-width: 1400px;
  margin: 60px auto;
  padding: 0 24px;
  text-align: center;
}

.spinner {
  border: 4px solid #f3f3f3;
  border-top: 4px solid #fe9700;
  border-radius: 50%;
  width: 50px;
  height: 50px;
  animation: spin 1s linear infinite;
  margin: 0 auto 20px;
}

@keyframes spin {
  0% { transform: rotate(0deg); }
  100% { transform: rotate(360deg); }
}

.empty-state {
  max-width: 600px;
  margin: 80px auto;
  padding: 60px 24px;
  text-align: center;
  background: #fff;
  border-radius: 16px;
  box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
}

.empty-state h3 {
  font-size: 24px;
  font-weight: 700;
  color: #1a1a1a;
  margin: 20px 0 10px;
}

.empty-state p {
  font-size: 16px;
  color: #999;
  margin: 0 0 24px;
}

.primary-btn {
  display: inline-flex;
  align-items: center;
  gap: 8px;
  padding: 12px 24px;
  background: linear-gradient(135deg, #fe9700 0%, #ff8c00 100%);
  color: #fff;
  border: none;
  border-radius: 10px;
  font-size: 16px;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.3s ease;
  box-shadow: 0 4px 12px rgba(254, 151, 0, 0.3);
}

.primary-btn:hover {
  transform: translateY(-2px);
  box-shadow: 0 6px 20px rgba(254, 151, 0, 0.4);
}

/* Table */
.orders-table-container {
  max-width: 1400px;
  margin: 0 auto 40px;
  padding: 0 24px;
}

.orders-table {
  width: 100%;
  background: #fff;
  border-radius: 12px;
  overflow: hidden;
  box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
  border-collapse: collapse;
}

.orders-table thead {
  background: linear-gradient(135deg, #f8f9fa 0%, #f0f2f5 100%);
}

.orders-table th {
  padding: 16px;
  text-align: left;
  font-size: 13px;
  font-weight: 700;
  color: #666;
  text-transform: uppercase;
  letter-spacing: 0.5px;
  border-bottom: 2px solid #e8ecf1;
}

.orders-table tbody tr.order-row {
  cursor: pointer;
  transition: all 0.2s ease;
  border-bottom: 1px solid #f0f2f5;
}

.orders-table tbody tr.order-row:hover {
  background: #fafbfc;
}

.orders-table tbody tr.order-row.expanded {
  background: #f8f9fa;
  border-bottom: none;
}

.orders-table td {
  padding: 16px;
  font-size: 14px;
  color: #333;
  vertical-align: middle;
}

.expand-cell {
  width: 48px;
}

.expand-btn {
  display: flex;
  align-items: center;
  justify-content: center;
  width: 32px;
  height: 32px;
  background: #f5f5f5;
  border: none;
  border-radius: 8px;
  cursor: pointer;
  transition: all 0.3s ease;
}

.expand-btn:hover {
  background: #fff7e6;
}

.expand-icon {
  transition: transform 0.3s ease;
}

.expand-icon.rotated {
  transform: rotate(180deg);
}

.order-number-cell .order-number {
  font-weight: 700;
  color: #fe9700;
  font-size: 15px;
}

.product-info-compact {
  display: flex;
  align-items: center;
  gap: 12px;
}

.product-thumbnail {
  width: 50px;
  height: 50px;
  object-fit: cover;
  border-radius: 8px;
  border: 2px solid #e8ecf1;
}

.product-name-compact {
  display: flex;
  flex-direction: column;
  gap: 4px;
}

.product-qty {
  font-size: 12px;
  color: #999;
}

.boutique-name {
  font-weight: 500;
}

.order-date {
  color: #666;
}

.order-total {
  font-weight: 700;
  color: #fe9700;
  font-size: 16px;
}

.status-badge {
  display: inline-block;
  padding: 6px 12px;
  border-radius: 20px;
  font-size: 12px;
  font-weight: 600;
  text-transform: uppercase;
}

.status-badge.pending {
  background: #fff7e6;
  color: #fa8c16;
}

.status-badge.contract-sent {
  background: #e6f7ff;
  color: #1890ff;
}

.status-badge.confirmed {
  background: #f6ffed;
  color: #52c41a;
}

.status-badge.shipping {
  background: #f0f5ff;
  color: #597ef7;
}

.status-badge.delivered {
  background: #f6ffed;
  color: #52c41a;
}

.status-badge.cancelled {
  background: #fff1f0;
  color: #ff4d4f;
}

.actions-cell {
  display: flex;
  gap: 8px;
}

.action-btn {
  display: flex;
  align-items: center;
  justify-content: center;
  width: 36px;
  height: 36px;
  background: #f5f5f5;
  border: 2px solid #e8ecf1;
  border-radius: 8px;
  cursor: pointer;
  transition: all 0.3s ease;
}

.action-btn:hover {
  transform: translateY(-2px);
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
}

.download-btn:hover {
  background: #fff7e6;
  border-color: #fe9700;
  color: #fe9700;
}

.payment-btn:hover {
  background: #e6f7ff;
  border-color: #1890ff;
  color: #1890ff;
}

.add-payment-btn:hover {
  background: #f6ffed;
  border-color: #52c41a;
  color: #52c41a;
}

/* Expanded Row */
.expanded-row {
  background: #f8f9fa;
  border-bottom: 1px solid #e8ecf1;
}

.expanded-cell {
  padding: 0 !important;
}

.expanded-content {
  padding: 24px;
  animation: slideDown 0.3s ease;
}

@keyframes slideDown {
  from {
    opacity: 0;
    transform: translateY(-10px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

.detail-section {
  background: #fff;
  border-radius: 12px;
  padding: 24px;
  margin-bottom: 16px;
  border: 2px solid #f0f2f5;
}

.section-title {
  font-size: 18px;
  font-weight: 700;
  color: #1a1a1a;
  margin: 0 0 16px 0;
}

.section-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 16px;
}

.summary-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
  gap: 16px;
}

.summary-item {
  display: flex;
  justify-content: space-between;
  padding: 12px;
  background: #f8f9fa;
  border-radius: 8px;
}

.summary-item.total {
  background: #fff7e6;
  font-weight: 700;
}

.summary-label {
  color: #666;
  font-size: 14px;
}

.summary-value {
  font-weight: 600;
  color: #1a1a1a;
}

.summary-value.success {
  color: #52c41a;
}

.summary-value.warning {
  color: #fa8c16;
}

/* Status Card */
.status-card {
  display: flex;
  gap: 16px;
  padding: 20px;
  border-radius: 12px;
  align-items: flex-start;
}

.status-card.validated {
  background: #f6ffed;
  border: 2px solid #b7eb8f;
}

.status-card.pending {
  background: #fffbe6;
  border: 2px solid #ffe58f;
}

.status-card.info {
  background: #e6f7ff;
  border: 2px solid #91d5ff;
}

.status-icon {
  width: 48px;
  height: 48px;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  flex-shrink: 0;
}

.status-card.validated .status-icon {
  background: #52c41a;
  color: #fff;
}

.status-card.pending .status-icon {
  background: #faad14;
  color: #fff;
}

.status-card.info .status-icon {
  background: #1890ff;
  color: #fff;
}

.status-text h5 {
  font-size: 16px;
  font-weight: 700;
  color: #1a1a1a;
  margin: 0 0 8px 0;
}

.status-text p {
  font-size: 14px;
  color: #666;
  margin: 0 0 8px 0;
}

.status-date {
  font-size: 12px;
  color: #999;
}

.vendor-comment {
  display: flex;
  gap: 8px;
  align-items: center;
  margin-top: 12px;
  padding: 12px;
  background: rgba(255, 255, 255, 0.8);
  border-radius: 8px;
  font-size: 14px;
  color: #666;
}

/* Payment Progress */
.payment-stats {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
  gap: 16px;
  margin-bottom: 24px;
}

.stat-box {
  display: flex;
  flex-direction: column;
  gap: 8px;
  padding: 16px;
  background: #f8f9fa;
  border-radius: 10px;
  border: 2px solid #e8ecf1;
}

.stat-label {
  font-size: 13px;
  color: #999;
  font-weight: 500;
}

.stat-value {
  font-size: 20px;
  font-weight: 700;
  color: #1a1a1a;
}

.stat-value.success {
  color: #52c41a;
}

.stat-value.warning {
  color: #fa8c16;
}

.progress-bar-wrapper {
  margin-bottom: 24px;
}

.progress-bar {
  width: 100%;
  height: 12px;
  background: #f0f2f5;
  border-radius: 20px;
  overflow: hidden;
  margin-bottom: 8px;
}

.progress-fill {
  height: 100%;
  background: linear-gradient(90deg, #52c41a 0%, #95de64 100%);
  transition: width 0.3s ease;
  border-radius: 20px;
}

.progress-text {
  font-size: 14px;
  color: #666;
  font-weight: 600;
}

/* Payment Timeline */
.payments-history {
  margin-top: 24px;
}

.history-title {
  font-size: 16px;
  font-weight: 700;
  color: #1a1a1a;
  margin: 0 0 16px 0;
}

.payment-timeline {
  display: flex;
  flex-direction: column;
  gap: 16px;
}

.timeline-item {
  display: flex;
  gap: 16px;
  position: relative;
}

.timeline-item:not(:last-child)::before {
  content: '';
  position: absolute;
  left: 11px;
  top: 32px;
  bottom: -16px;
  width: 2px;
  background: #e8ecf1;
}

.timeline-dot {
  width: 24px;
  height: 24px;
  border-radius: 50%;
  background: #52c41a;
  border: 4px solid #f6ffed;
  flex-shrink: 0;
}

.timeline-dot.pending {
  background: #faad14;
  border-color: #fffbe6;
}

.timeline-content {
  flex: 1;
  padding: 8px 16px;
  background: #f8f9fa;
  border-radius: 10px;
}

.timeline-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  flex-wrap: wrap;
  gap: 8px;
  margin-bottom: 8px;
}

.timeline-label {
  font-size: 14px;
  font-weight: 600;
  color: #1a1a1a;
}

.timeline-amount {
  font-size: 16px;
  font-weight: 700;
  color: #fe9700;
}

.timeline-status {
  padding: 4px 12px;
  border-radius: 12px;
  font-size: 11px;
  font-weight: 700;
  text-transform: uppercase;
}

.timeline-status.validated {
  background: #f6ffed;
  color: #52c41a;
}

.timeline-status.pending {
  background: #fffbe6;
  color: #faad14;
}

.timeline-date {
  font-size: 12px;
  color: #999;
  display: block;
  margin-bottom: 8px;
}

.timeline-comment {
  display: flex;
  gap: 8px;
  align-items: center;
  padding: 8px;
  background: rgba(255, 255, 255, 0.8);
  border-radius: 6px;
  font-size: 13px;
  color: #666;
  margin-top: 8px;
}

.view-proof-btn {
  display: inline-flex;
  align-items: center;
  gap: 6px;
  padding: 8px 16px;
  background: #fe9700;
  color: #fff;
  border: none;
  border-radius: 8px;
  font-size: 13px;
  font-weight: 600;
  cursor: pointer;
  margin-top: 8px;
  transition: all 0.3s ease;
}

.view-proof-btn:hover {
  background: #ff8c00;
  transform: translateY(-2px);
  box-shadow: 0 4px 12px rgba(254, 151, 0, 0.3);
}

/* Production Section */
.production-badge {
  padding: 6px 16px;
  border-radius: 20px;
  font-size: 12px;
  font-weight: 700;
  text-transform: uppercase;
}

.production-badge.in-progress {
  background: #e6f7ff;
  color: #1890ff;
}

.production-badge.ready {
  background: #f6ffed;
  color: #52c41a;
}

.production-badge.shipping {
  background: #e6f7ff;
  color: #1890ff;
  animation: pulse-shipping 2s ease-in-out infinite;
}

@keyframes pulse-shipping {
  0%, 100% {
    opacity: 1;
  }
  50% {
    opacity: 0.7;
  }
}

.production-message {
  display: flex;
  gap: 12px;
  padding: 16px;
  background: #f8f9fa;
  border-radius: 10px;
  border-left: 4px solid #52c41a;
  margin-bottom: 20px;
}

.production-message p {
  margin: 0;
  font-size: 14px;
  color: #666;
  line-height: 1.6;
}

.production-message strong {
  color: #1a1a1a;
}

/* Countdown Section */
.countdown-section {
  padding: 20px;
  background: linear-gradient(135deg, #fff7e6 0%, #fffbe6 100%);
  border-radius: 12px;
  border: 2px solid #ffe58f;
  margin-bottom: 20px;
}

.countdown-display {
  display: flex;
  justify-content: center;
  align-items: center;
  gap: 12px;
  margin-bottom: 20px;
}

.countdown-item {
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 4px;
}

.countdown-value {
  font-size: 32px;
  font-weight: 700;
  color: #fe9700;
  font-family: 'Courier New', monospace;
}

.countdown-label {
  font-size: 12px;
  color: #999;
  text-transform: uppercase;
  font-weight: 600;
}

.countdown-separator {
  font-size: 28px;
  font-weight: 700;
  color: #fe9700;
  margin: 0 4px;
}

.prep-progress-wrapper {
  margin-bottom: 16px;
}

.prep-progress-bar {
  width: 100%;
  height: 24px;
  background: #fff;
  border-radius: 12px;
  overflow: hidden;
  box-shadow: inset 0 2px 4px rgba(0, 0, 0, 0.1);
  margin-bottom: 8px;
}

.prep-progress-fill {
  height: 100%;
  background: linear-gradient(90deg, #fe9700 0%, #ff8c00 100%);
  display: flex;
  align-items: center;
  justify-content: center;
  transition: width 0.3s ease;
}

.prep-progress-text {
  color: #fff;
  font-size: 12px;
  font-weight: 700;
}

.prep-progress-label {
  font-size: 13px;
  color: #999;
  text-align: center;
  margin: 0;
}

.delivery-estimate {
  display: flex;
  align-items: center;
  gap: 8px;
  justify-content: center;
  padding: 12px;
  background: #fff;
  border-radius: 8px;
  font-size: 14px;
  font-weight: 600;
  color: #fe9700;
}

/* Ready for Delivery */
.ready-delivery-section {
  margin-top: 20px;
}

.ready-delivery-box {
  display: flex;
  align-items: center;
  gap: 20px;
  padding: 24px;
  background: linear-gradient(135deg, #f6ffed 0%, #f0ffed 100%);
  border-radius: 12px;
  border: 2px solid #b7eb8f;
}

.ready-delivery-content h5 {
  font-size: 18px;
  font-weight: 700;
  color: #52c41a;
  margin: 0 0 8px 0;
}

.ready-delivery-content p {
  font-size: 14px;
  color: #666;
  margin: 0 0 16px 0;
}

.confirm-address-btn {
  display: inline-flex;
  align-items: center;
  gap: 8px;
  padding: 12px 24px;
  background: #52c41a;
  color: #fff;
  border: none;
  border-radius: 10px;
  font-size: 14px;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.3s ease;
}

.confirm-address-btn:hover {
  background: #389e0d;
  transform: translateY(-2px);
  box-shadow: 0 4px 12px rgba(82, 196, 26, 0.3);
}

/* Delivery Information */
.delivery-info-box {
  display: flex;
  gap: 12px;
  padding: 16px;
  background: #fff7e6;
  border-radius: 10px;
  border-left: 4px solid #fe9700;
}

.delivery-address {
  font-size: 14px;
  font-weight: 600;
  color: #1a1a1a;
  margin: 0 0 8px 0;
}

.delivery-instructions {
  font-size: 13px;
  color: #666;
  margin: 0;
}

/* Action Buttons Section */
.action-buttons-section {
  display: flex;
  flex-wrap: wrap;
  gap: 12px;
  padding: 20px 0 0;
  border-top: 2px solid #f0f2f5;
}

.action-btn-secondary,
.action-btn-primary,
.action-btn-danger {
  display: inline-flex;
  align-items: center;
  gap: 8px;
  padding: 12px 20px;
  border: none;
  border-radius: 10px;
  font-size: 14px;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.3s ease;
}

.action-btn-secondary {
  background: #f5f5f5;
  color: #666;
}

.action-btn-secondary:hover {
  background: #e8e8e8;
  transform: translateY(-2px);
}

.action-btn-primary {
  background: linear-gradient(135deg, #fe9700 0%, #ff8c00 100%);
  color: #fff;
  box-shadow: 0 4px 12px rgba(254, 151, 0, 0.3);
}

.action-btn-primary:hover {
  transform: translateY(-2px);
  box-shadow: 0 6px 20px rgba(254, 151, 0, 0.4);
}

.action-btn-danger {
  background: #fff1f0;
  color: #ff4d4f;
  border: 2px solid #ff4d4f;
}

.action-btn-danger:hover {
  background: #ff4d4f;
  color: #fff;
  transform: translateY(-2px);
}

/* Modal */
.modal-overlay {
  position: fixed;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: rgba(0, 0, 0, 0.6);
  display: flex;
  align-items: center;
  justify-content: center;
  z-index: 1000;
  padding: 20px;
}

.modal-container {
  background: #fff;
  border-radius: 16px;
  max-width: 600px;
  width: 100%;
  max-height: 90vh;
  overflow-y: auto;
  box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
  animation: modalSlideIn 0.3s ease;
}

@keyframes modalSlideIn {
  from {
    opacity: 0;
    transform: translateY(-20px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

.modal-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 24px;
  border-bottom: 2px solid #f0f2f5;
}

.modal-title {
  font-size: 20px;
  font-weight: 700;
  color: #1a1a1a;
  margin: 0;
}

.modal-close {
  width: 36px;
  height: 36px;
  display: flex;
  align-items: center;
  justify-content: center;
  background: #f5f5f5;
  border: none;
  border-radius: 8px;
  cursor: pointer;
  transition: all 0.3s ease;
}

.modal-close:hover {
  background: #ff4d4f;
  color: #fff;
}

.modal-icon-warning {
  margin: 0 auto 16px;
  text-align: center;
}

.modal-message {
  font-size: 15px;
  color: #666;
  margin: 0 0 20px 0;
  line-height: 1.6;
}

.modal-body {
  padding: 24px;
}

.form-group {
  margin-bottom: 20px;
}

.form-label {
  display: block;
  font-size: 14px;
  font-weight: 600;
  color: #333;
  margin-bottom: 8px;
}

.form-input,
.form-textarea {
  width: 100%;
  padding: 12px 16px;
  border: 2px solid #e8ecf1;
  border-radius: 10px;
  font-size: 14px;
  color: #333;
  transition: all 0.3s ease;
}

.form-input:focus,
.form-textarea:focus {
  outline: none;
  border-color: #fe9700;
  box-shadow: 0 0 0 4px rgba(254, 151, 0, 0.1);
}

.file-input {
  width: 100%;
  padding: 12px;
  border: 2px dashed #e8ecf1;
  border-radius: 10px;
  cursor: pointer;
  transition: all 0.3s ease;
}

.file-input:hover {
  border-color: #fe9700;
  background: #fff7e6;
}

.file-name {
  margin-top: 8px;
  font-size: 13px;
  color: #666;
}

.payment-info-box {
  background: #f8f9fa;
  border-radius: 10px;
  padding: 16px;
  margin-bottom: 20px;
}

.info-row {
  display: flex;
  justify-content: space-between;
  padding: 8px 0;
  border-bottom: 1px solid #e8ecf1;
}

.info-row:last-child {
  border-bottom: none;
}

.upload-progress {
  margin-top: 16px;
}

.modal-footer {
  display: flex;
  justify-content: flex-end;
  gap: 12px;
  padding: 24px;
  border-top: 2px solid #f0f2f5;
}

.btn-secondary {
  padding: 12px 24px;
  background: #f5f5f5;
  color: #666;
  border: none;
  border-radius: 10px;
  font-size: 14px;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.3s ease;
}

.btn-secondary:hover {
  background: #e8e8e8;
}

.btn-primary {
  padding: 12px 24px;
  background: linear-gradient(135deg, #fe9700 0%, #ff8c00 100%);
  color: #fff;
  border: none;
  border-radius: 10px;
  font-size: 14px;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.3s ease;
  box-shadow: 0 4px 12px rgba(254, 151, 0, 0.3);
}

.btn-primary:hover:not(:disabled) {
  transform: translateY(-2px);
  box-shadow: 0 6px 20px rgba(254, 151, 0, 0.4);
}

.btn-primary:disabled {
  opacity: 0.6;
  cursor: not-allowed;
}

.btn-danger {
  padding: 12px 24px;
  background: #ff4d4f;
  color: #fff;
  border: none;
  border-radius: 10px;
  font-size: 14px;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.3s ease;
  box-shadow: 0 4px 12px rgba(255, 77, 79, 0.3);
}

.btn-danger:hover:not(:disabled) {
  background: #d9363e;
  transform: translateY(-2px);
  box-shadow: 0 6px 20px rgba(255, 77, 79, 0.4);
}

.btn-danger:disabled {
  opacity: 0.6;
  cursor: not-allowed;
}

/* Responsive */
@media (max-width: 1024px) {
  .orders-table {
    font-size: 13px;
  }

  .orders-table th,
  .orders-table td {
    padding: 12px 8px;
  }

  .product-thumbnail {
    width: 40px;
    height: 40px;
  }
}

@media (max-width: 768px) {
  .main-title {
    font-size: 24px;
  }

  .filters-wrapper {
    flex-direction: column;
  }

  .filter-btn {
    width: 100%;
    justify-content: space-between;
  }

  .orders-table-container {
    overflow-x: auto;
  }

  .orders-table {
    min-width: 900px;
  }

  .summary-grid {
    grid-template-columns: 1fr;
  }

  .payment-stats {
    grid-template-columns: 1fr;
  }

  .action-buttons-section {
    flex-direction: column;
  }

  .action-btn-secondary,
  .action-btn-primary,
  .action-btn-danger {
    width: 100%;
    justify-content: center;
  }
}

/* Modern Summary Card Styles */
.modern-summary-card {
  background: linear-gradient(135deg, #ffffff 0%, #fafbfc 100%);
  border: 1px solid #e8ecf1;
  border-radius: 12px;
  padding: 20px;
  box-shadow: 0 2px 12px rgba(0, 0, 0, 0.06);
}

.summary-row {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 12px 0;
  border-bottom: 1px solid #f0f2f5;
}

.summary-row:last-child {
  border-bottom: none;
}

.summary-row.total-row {
  padding-top: 16px;
  border-top: 2px solid #e8ecf1;
}

.summary-row .summary-label {
  color: #666;
  font-size: 14px;
  font-weight: 500;
}

.summary-row .summary-value {
  color: #1a1a1a;
  font-size: 15px;
  font-weight: 600;
}

.summary-row .summary-value.total {
  color: #fe9700;
  font-size: 18px;
  font-weight: 700;
}

.summary-divider {
  height: 1px;
  background: linear-gradient(90deg, transparent 0%, #e8ecf1 50%, transparent 100%);
  margin: 16px 0;
}

/* CIF Shipping Details */
.cif-shipping-details {
  background: linear-gradient(135deg, #fff7e6 0%, #fff3d9 100%);
  border: 2px solid #ffe7ba;
  border-radius: 10px;
  padding: 16px;
  margin: 16px 0;
}

.shipping-header {
  display: flex;
  align-items: center;
  gap: 10px;
  margin-bottom: 12px;
}

.shipping-title {
  color: #fe9700;
  font-size: 15px;
  font-weight: 700;
}

.shipping-info-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
  gap: 12px;
}

.shipping-info-item {
  display: flex;
  flex-direction: column;
  gap: 4px;
}

.shipping-info-item .info-label {
  color: #999;
  font-size: 12px;
  font-weight: 500;
  text-transform: uppercase;
  letter-spacing: 0.5px;
}

.shipping-info-item .info-value {
  color: #1a1a1a;
  font-size: 14px;
  font-weight: 600;
}

/* Contract Section */
.contract-section {
  background: linear-gradient(135deg, #fff7e6 0%, #fffbf0 100%);
  border: 2px solid #ffe7ba;
  border-radius: 12px;
  padding: 16px;
  margin-top: 16px;
}

.contract-header {
  display: flex;
  align-items: center;
  gap: 10px;
  margin-bottom: 12px;
}

.contract-title {
  color: #fe9700;
  font-size: 15px;
  font-weight: 700;
}

.deposit-paid-info {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 12px;
  background: #fff;
  border-radius: 8px;
}

.deposit-amount {
  color: #52c41a;
  font-size: 18px;
  font-weight: 700;
}

.deposit-status {
  display: flex;
  align-items: center;
  gap: 6px;
  color: #52c41a;
  font-size: 14px;
  font-weight: 600;
}

.contract-actions {
  display: flex;
  flex-direction: column;
  gap: 8px;
}

.download-contract-btn {
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 8px;
  padding: 12px 20px;
  background: linear-gradient(135deg, #fe9700 0%, #ff8c00 100%);
  color: #fff;
  border: none;
  border-radius: 8px;
  font-size: 14px;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.3s ease;
  box-shadow: 0 4px 12px rgba(254, 151, 0, 0.3);
}

.download-contract-btn:hover {
  transform: translateY(-2px);
  box-shadow: 0 6px 20px rgba(254, 151, 0, 0.4);
}

.download-contract-btn:active {
  transform: translateY(0);
}

.contract-description {
  color: #666;
  font-size: 13px;
  text-align: center;
  margin: 0;
}

/* Modern Modal Styles */
.modern-modal {
  max-width: 700px;
  max-height: 90vh;
  overflow-y: auto;
}

.modal-header-content {
  display: flex;
  align-items: center;
  gap: 16px;
}

.modal-icon {
  width: 48px;
  height: 48px;
  background: linear-gradient(135deg, #fff7e6 0%, #ffe7ba 100%);
  border-radius: 12px;
  display: flex;
  align-items: center;
  justify-content: center;
  flex-shrink: 0;
}

.modal-subtitle {
  color: #999;
  font-size: 14px;
  margin: 4px 0 0 0;
  font-weight: 400;
}

/* Payment Summary Card in Modal */
.payment-summary-card {
  background: linear-gradient(135deg, #fff7e6 0%, #fffbf0 100%);
  border: 2px solid #ffe7ba;
  border-radius: 12px;
  padding: 16px;
  margin-bottom: 20px;
}

.summary-header {
  display: flex;
  align-items: center;
  gap: 10px;
  margin-bottom: 12px;
  color: #fe9700;
  font-size: 15px;
  font-weight: 700;
}

.summary-details {
  display: flex;
  flex-direction: column;
  gap: 8px;
}

.summary-details .summary-row {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 10px 0;
  border-bottom: 1px solid rgba(254, 151, 0, 0.1);
}

.summary-details .summary-row:last-child {
  border-bottom: none;
}

.summary-details .summary-row.highlight {
  background: #fff;
  padding: 12px;
  border-radius: 8px;
  border: 1px solid #ffe7ba;
}

.summary-details .summary-row span {
  color: #666;
  font-size: 14px;
}

.summary-details .summary-row strong {
  color: #1a1a1a;
  font-size: 15px;
}

.summary-details .summary-row.highlight .amount-highlight {
  color: #fe9700;
  font-size: 18px;
  font-weight: 700;
}

/* Contract Signature Section */
.contract-signature-section {
  background: linear-gradient(135deg, #e6f7ff 0%, #f0f9ff 100%);
  border: 2px solid #bae7ff;
  border-radius: 12px;
  padding: 16px;
  margin-bottom: 20px;
}

.signature-header {
  display: flex;
  align-items: center;
  gap: 10px;
  margin-bottom: 12px;
  color: #1890ff;
  font-size: 15px;
  font-weight: 700;
}

.contract-agreement-box {
  background: #fff;
  border-radius: 10px;
  padding: 16px;
}

.agreement-content {
  display: flex;
  flex-direction: column;
  gap: 16px;
}

.agreement-text {
  color: #666;
  font-size: 14px;
  line-height: 1.6;
  margin: 0;
}

.sign-contract-btn {
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 8px;
  padding: 14px 24px;
  background: linear-gradient(135deg, #1890ff 0%, #0070dd 100%);
  color: #fff;
  border: none;
  border-radius: 8px;
  font-size: 15px;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.3s ease;
  box-shadow: 0 4px 12px rgba(24, 144, 255, 0.3);
}

.sign-contract-btn:hover:not(:disabled) {
  transform: translateY(-2px);
  box-shadow: 0 6px 20px rgba(24, 144, 255, 0.4);
}

.sign-contract-btn:disabled {
  opacity: 0.6;
  cursor: not-allowed;
}

/* Signature Success Animation */
.signature-success {
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 12px;
  padding: 16px;
}

.success-animation {
  display: flex;
  justify-content: center;
  align-items: center;
}

.checkmark-circle {
  width: 80px;
  height: 80px;
  position: relative;
  display: inline-block;
  vertical-align: top;
}

.checkmark {
  width: 80px;
  height: 80px;
  border-radius: 50%;
  display: block;
  stroke-width: 3;
  stroke: #52c41a;
  stroke-miterlimit: 10;
}

.checkmark-circle-path {
  stroke-dasharray: 166;
  stroke-dashoffset: 166;
  stroke: #52c41a;
  stroke-width: 3;
  animation: stroke 0.6s cubic-bezier(0.65, 0, 0.45, 1) forwards;
}

.checkmark-check {
  transform-origin: 50% 50%;
  stroke-dasharray: 48;
  stroke-dashoffset: 48;
  stroke: #52c41a;
  stroke-width: 3;
  animation: stroke 0.3s cubic-bezier(0.65, 0, 0.45, 1) 0.6s forwards;
}

@keyframes stroke {
  100% {
    stroke-dashoffset: 0;
  }
}

.success-message {
  display: flex;
  align-items: center;
  gap: 8px;
  color: #52c41a;
  font-size: 16px;
  font-weight: 600;
  margin: 0;
}

.signed-date {
  color: #999;
  font-size: 13px;
  margin: 0;
}

/* Payment Details Section */
.payment-details-section {
  transition: all 0.3s ease;
}

.payment-details-section.disabled {
  opacity: 0.5;
  pointer-events: none;
  filter: grayscale(0.3);
}

.required-signature {
  color: #ff4d4f;
  font-size: 12px;
  font-weight: 400;
  margin-left: 8px;
}

.file-upload-area {
  display: flex;
  flex-direction: column;
  gap: 8px;
}

.file-input-hidden {
  display: none;
}

.file-upload-btn {
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 8px;
  padding: 12px 20px;
  background: #f5f5f5;
  border: 2px dashed #d9d9d9;
  border-radius: 8px;
  color: #666;
  font-size: 14px;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.3s ease;
}

.file-upload-btn:hover:not(:disabled) {
  background: #fff7e6;
  border-color: #fe9700;
  color: #fe9700;
}

.file-upload-btn:disabled {
  cursor: not-allowed;
  opacity: 0.5;
}

.file-name-display {
  display: flex;
  align-items: center;
  gap: 8px;
  color: #52c41a;
  font-size: 13px;
  font-weight: 500;
  padding: 8px 12px;
  background: #f6ffed;
  border: 1px solid #b7eb8f;
  border-radius: 6px;
  margin: 0;
}

.file-placeholder {
  color: #999;
  font-size: 13px;
  margin: 0;
}

/* Upload Progress Styles */
.progress-bar-container {
  width: 100%;
  height: 8px;
  background: #f0f2f5;
  border-radius: 10px;
  overflow: hidden;
  margin-bottom: 8px;
}

.progress-bar-fill {
  height: 100%;
  background: linear-gradient(90deg, #fe9700 0%, #ff8c00 100%);
  transition: width 0.3s ease;
  border-radius: 10px;
}

.progress-text {
  display: flex;
  align-items: center;
  gap: 8px;
  justify-content: center;
  color: #fe9700;
  font-size: 14px;
  font-weight: 600;
  margin: 0;
}

.spinning {
  animation: spin 1s linear infinite;
}

@keyframes spin {
  from {
    transform: rotate(0deg);
  }
  to {
    transform: rotate(360deg);
  }
}

/* Ready for Delivery Payment Sections */
.ready-delivery-box.success {
  background: linear-gradient(135deg, #f6ffed 0%, #edfff0 100%);
  border: 2px solid #b7eb8f;
}

.ready-delivery-box.warning {
  background: linear-gradient(135deg, #fffbe6 0%, #fff9e6 100%);
  border: 2px solid #ffe58f;
}

.bl-payment-note {
  display: flex;
  align-items: flex-start;
  gap: 10px;
  margin-top: 16px;
  padding: 12px 16px;
  background: #e6f7ff;
  border-left: 4px solid #1890ff;
  border-radius: 6px;
}

.bl-payment-note span {
  color: #1890ff;
  font-size: 14px;
  line-height: 1.6;
}

.bl-payment-note strong {
  font-weight: 700;
}

/* Payment Breakdown Card */
.payment-breakdown-card {
  background: #fff;
  border: 1px solid #e8ecf1;
  border-radius: 10px;
  padding: 16px;
  margin: 16px 0;
}

.breakdown-row {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 10px 0;
  border-bottom: 1px solid #f0f2f5;
}

.breakdown-row:last-child {
  border-bottom: none;
}

.breakdown-row.total {
  padding-top: 12px;
  margin-top: 8px;
  border-top: 2px solid #e8ecf1;
}

.breakdown-row span {
  color: #666;
  font-size: 14px;
  font-weight: 500;
}

.breakdown-row strong {
  color: #1a1a1a;
  font-size: 15px;
  font-weight: 600;
}

.breakdown-row strong.paid {
  color: #52c41a;
}

.breakdown-row strong.bl-amount {
  color: #1890ff;
}

.breakdown-row strong.remaining {
  color: #faad14;
  font-size: 18px;
  font-weight: 700;
}

/* Payment Action Box */
.payment-action-box {
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 12px;
  padding: 16px;
  background: linear-gradient(135deg, #fff7e6 0%, #fffbf0 100%);
  border: 2px solid #ffe7ba;
  border-radius: 10px;
  margin: 16px 0;
}

.action-message {
  color: #fa8c16;
  font-size: 15px;
  font-weight: 600;
  text-align: center;
  margin: 0;
}

.action-btn-payment {
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 8px;
  padding: 14px 32px;
  background: linear-gradient(135deg, #fa8c16 0%, #ff7a00 100%);
  color: #fff;
  border: none;
  border-radius: 8px;
  font-size: 15px;
  font-weight: 700;
  cursor: pointer;
  transition: all 0.3s ease;
  box-shadow: 0 4px 12px rgba(250, 140, 22, 0.3);
  text-transform: uppercase;
  letter-spacing: 0.5px;
}

.action-btn-payment:hover {
  transform: translateY(-2px);
  box-shadow: 0 6px 20px rgba(250, 140, 22, 0.4);
}

.action-btn-payment:active {
  transform: translateY(0);
}

/* BL Payment Info */
.bl-payment-info {
  display: flex;
  align-items: flex-start;
  gap: 10px;
  margin-top: 16px;
  padding: 12px 16px;
  background: #f0f9ff;
  border-left: 4px solid #1890ff;
  border-radius: 6px;
}

.bl-payment-info span {
  color: #666;
  font-size: 13px;
  line-height: 1.6;
}

.bl-payment-info strong {
  color: #1890ff;
  font-weight: 700;
}

/* Shipping Status Section (en_livraison) */
.shipping-status-section {
  margin-top: 24px;
}

.shipping-status-box {
  background: linear-gradient(135deg, #e6f7ff 0%, #f0f9ff 100%);
  border: 2px solid #91d5ff;
  border-radius: 16px;
  padding: 28px;
  box-shadow: 0 4px 12px rgba(24, 144, 255, 0.08);
}

.shipping-status-header {
  display: flex;
  align-items: flex-start;
  gap: 20px;
  margin-bottom: 24px;
}

.shipping-status-header svg {
  flex-shrink: 0;
  margin-top: 4px;
}

.shipping-status-content h5 {
  font-size: 18px;
  font-weight: 700;
  color: #1890ff;
  margin: 0 0 8px 0;
  line-height: 1.4;
}

.shipping-status-content p {
  font-size: 14px;
  color: #666;
  margin: 0;
  line-height: 1.6;
}

/* CIF Shipping Information Section */
.cif-shipping-info-section {
  background: #fff;
  border: 1px solid #d9d9d9;
  border-radius: 12px;
  padding: 20px;
  margin-top: 20px;
}

.shipping-info-header {
  display: flex;
  align-items: center;
  gap: 10px;
  margin-bottom: 16px;
  padding-bottom: 12px;
  border-bottom: 2px solid #f0f2f5;
}

.shipping-info-header h6 {
  font-size: 16px;
  font-weight: 700;
  color: #1890ff;
  margin: 0;
}

.shipping-details-grid {
  display: grid;
  grid-template-columns: repeat(2, 1fr);
  gap: 16px;
  margin-bottom: 20px;
}

.shipping-detail-item {
  display: flex;
  flex-direction: column;
  gap: 6px;
}

.shipping-detail-item.full-width {
  grid-column: 1 / -1;
}

.detail-label {
  font-size: 12px;
  color: #999;
  font-weight: 500;
  text-transform: uppercase;
  letter-spacing: 0.5px;
}

.detail-value {
  font-size: 14px;
  color: #333;
  font-weight: 600;
}

.tracking-link {
  display: inline-flex;
  align-items: center;
  gap: 8px;
  padding: 10px 16px;
  background: linear-gradient(135deg, #1890ff 0%, #096dd9 100%);
  color: #fff;
  text-decoration: none;
  border-radius: 8px;
  font-size: 14px;
  font-weight: 600;
  transition: all 0.3s ease;
  box-shadow: 0 2px 8px rgba(24, 144, 255, 0.2);
}

.tracking-link:hover {
  background: linear-gradient(135deg, #096dd9 0%, #0050b3 100%);
  box-shadow: 0 4px 12px rgba(24, 144, 255, 0.3);
  transform: translateY(-2px);
}

/* Bill of Lading (BL) Section */
.bl-section {
  background: #fafafa;
  border: 1px dashed #d9d9d9;
  border-radius: 10px;
  padding: 18px;
  margin-top: 16px;
}

.bl-header {
  display: flex;
  align-items: center;
  gap: 10px;
  margin-bottom: 14px;
}

.bl-title {
  font-size: 15px;
  font-weight: 700;
  color: #52c41a;
}

.bl-info-grid {
  display: grid;
  grid-template-columns: repeat(2, 1fr);
  gap: 12px;
  margin-bottom: 16px;
}

.bl-info-item {
  display: flex;
  flex-direction: column;
  gap: 4px;
}

.bl-label {
  font-size: 12px;
  color: #999;
  font-weight: 500;
}

.bl-value {
  font-size: 14px;
  color: #333;
  font-weight: 600;
}

.download-bl-btn {
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 10px;
  width: 100%;
  padding: 14px 24px;
  background: linear-gradient(135deg, #52c41a 0%, #389e0d 100%);
  color: #fff;
  border: none;
  border-radius: 8px;
  font-size: 15px;
  font-weight: 700;
  cursor: pointer;
  transition: all 0.3s ease;
  box-shadow: 0 4px 12px rgba(82, 196, 26, 0.25);
  text-transform: uppercase;
  letter-spacing: 0.5px;
}

.download-bl-btn:hover {
  background: linear-gradient(135deg, #389e0d 0%, #237804 100%);
  box-shadow: 0 6px 16px rgba(82, 196, 26, 0.35);
  transform: translateY(-2px);
}

.download-bl-btn:active {
  transform: translateY(0);
  box-shadow: 0 2px 8px rgba(82, 196, 26, 0.2);
}

.bl-pending-message {
  display: flex;
  align-items: center;
  gap: 10px;
  padding: 12px 16px;
  background: #fffbe6;
  border: 1px solid #ffe58f;
  border-radius: 8px;
  color: #d48806;
  font-size: 14px;
  font-weight: 500;
  margin: 0;
  text-align: center;
  justify-content: center;
}

@media (max-width: 768px) {
  .shipping-details-grid,
  .bl-info-grid {
    grid-template-columns: 1fr;
  }

  .shipping-status-header {
    flex-direction: column;
    gap: 16px;
  }

  .shipping-status-header svg {
    margin-top: 0;
  }

  .shipping-detail-item.full-width {
    grid-column: 1;
  }
}

/* Delivered Order Section */
.delivered-order-section {
  background: linear-gradient(135deg, #f6ffed 0%, #f0fff4 100%);
  border: 2px solid #b7eb8f;
  border-radius: 20px;
  padding: 40px;
  text-align: center;
}

.delivered-header {
  margin-bottom: 32px;
}

.success-checkmark {
  display: inline-block;
  margin-bottom: 20px;
  animation: scale-in 0.5s ease-out;
}

@keyframes scale-in {
  0% {
    transform: scale(0);
    opacity: 0;
  }
  50% {
    transform: scale(1.1);
  }
  100% {
    transform: scale(1);
    opacity: 1;
  }
}

.delivered-title {
  font-size: 28px;
  font-weight: 800;
  color: #52c41a;
  margin: 0 0 12px 0;
  text-transform: uppercase;
  letter-spacing: 1px;
}

.delivered-message {
  font-size: 16px;
  color: #666;
  margin: 0;
  line-height: 1.6;
}

.delivered-details-card {
  background: #fff;
  border: 1px solid #d9f7be;
  border-radius: 16px;
  padding: 28px;
  margin: 24px 0;
  text-align: left;
}

.delivered-info-row {
  display: flex;
  align-items: center;
  gap: 16px;
  padding: 16px 0;
  border-bottom: 1px solid #f0f0f0;
}

.delivered-info-row:last-child {
  border-bottom: none;
}

.delivered-info-row.total-row {
  background: #fafafa;
  margin: 16px -28px -28px -28px;
  padding: 20px 28px;
  border-radius: 0 0 16px 16px;
  border-bottom: none;
}

.info-icon {
  flex-shrink: 0;
  width: 44px;
  height: 44px;
  display: flex;
  align-items: center;
  justify-content: center;
  background: #f6ffed;
  border-radius: 12px;
}

.info-content {
  flex: 1;
  display: flex;
  flex-direction: column;
  gap: 4px;
}

.info-label {
  font-size: 13px;
  color: #999;
  font-weight: 500;
  text-transform: uppercase;
  letter-spacing: 0.5px;
}

.info-value {
  font-size: 16px;
  color: #333;
  font-weight: 600;
}

.info-value.total-amount {
  font-size: 20px;
  color: #52c41a;
  font-weight: 800;
}

.delivered-footer {
  padding-top: 20px;
  border-top: 2px dashed #d9f7be;
  margin-top: 20px;
}

.footer-text {
  font-size: 14px;
  color: #666;
  margin: 0;
  line-height: 1.6;
  font-style: italic;
}

@media (max-width: 768px) {
  .delivered-order-section {
    padding: 24px 20px;
  }

  .delivered-title {
    font-size: 22px;
  }

  .delivered-message {
    font-size: 14px;
  }

  .delivered-details-card {
    padding: 20px;
  }

  .delivered-info-row.total-row {
    margin: 12px -20px -20px -20px;
    padding: 16px 20px;
  }

  .info-icon {
    width: 36px;
    height: 36px;
  }

  .info-value {
    font-size: 14px;
  }

  .info-value.total-amount {
    font-size: 18px;
  }
}

/* Stage Content */
.stage-content {
  animation: fadeIn 0.3s ease-in-out;
}

@keyframes fadeIn {
  from {
    opacity: 0;
    transform: translateY(10px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

/* Info Message for Pending Stage */
.info-message {
  display: flex;
  align-items: center;
  gap: 20px;
  padding: 32px;
  border-radius: 16px;
  margin: 20px 0;
}

.info-message.pending {
  background: linear-gradient(135deg, #fffbeb 0%, #fef3c7 100%);
  border: 2px solid #fbbf24;
}

.info-message svg {
  flex-shrink: 0;
}

.message-content h5 {
  font-size: 20px;
  font-weight: 700;
  color: #d97706;
  margin: 0 0 8px 0;
}

.message-content p {
  font-size: 15px;
  color: #92400e;
  margin: 0;
  line-height: 1.6;
}

/* Contract Actions Section */
.contract-actions-section {
  padding: 32px;
  background: linear-gradient(135deg, #f0f9ff 0%, #e0f2fe 100%);
  border: 2px solid #7dd3fc;
  border-radius: 16px;
  text-align: center;
  margin: 20px 0;
}

.download-contract-btn-large {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  gap: 12px;
  padding: 16px 32px;
  background: linear-gradient(135deg, #0ea5e9 0%, #0284c7 100%);
  color: #fff;
  border: none;
  border-radius: 12px;
  font-size: 16px;
  font-weight: 700;
  cursor: pointer;
  transition: all 0.3s ease;
  box-shadow: 0 4px 16px rgba(14, 165, 233, 0.3);
  text-transform: uppercase;
  letter-spacing: 0.5px;
}

.download-contract-btn-large:hover {
  background: linear-gradient(135deg, #0284c7 0%, #0369a1 100%);
  box-shadow: 0 6px 20px rgba(14, 165, 233, 0.4);
  transform: translateY(-2px);
}

.download-contract-btn-large:active {
  transform: translateY(0);
  box-shadow: 0 2px 8px rgba(14, 165, 233, 0.2);
}

.contract-description {
  margin: 16px 0 0 0;
  font-size: 14px;
  color: #075985;
  line-height: 1.6;
}

/* Lifecycle Timeline Section */
.lifecycle-timeline-section {
  background: linear-gradient(135deg, #fff7ed 0%, #ffedd5 100%);
  border: 2px solid #fed7aa;
  border-radius: 16px;
  padding: 24px 28px;
  margin-bottom: 28px;
}

.lifecycle-title {
  display: flex;
  align-items: center;
  gap: 10px;
  font-size: 17px;
  font-weight: 700;
  color: #ea580c;
  margin: 0 0 20px 0;
}

.lifecycle-steps-container {
  display: flex;
  align-items: flex-start;
  justify-content: space-between;
  gap: 8px;
}

.lifecycle-step-wrapper {
  flex: 1;
  position: relative;
  display: flex;
  flex-direction: column;
  align-items: center;
}

.lifecycle-step-content {
  display: flex;
  flex-direction: column;
  align-items: center;
  z-index: 2;
}

.lifecycle-circle {
  width: 56px;
  height: 56px;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 24px;
  margin-bottom: 12px;
  transition: all 0.3s ease;
  border: 3px solid transparent;
  box-shadow: 0 2px 8px rgba(0,0,0,0.1);
}

.lifecycle-circle.pending {
  background: #f3f4f6;
  color: #9ca3af;
  border-color: #e5e7eb;
}

.lifecycle-circle.active {
  background: linear-gradient(135deg, #f97316 0%, #ea580c 100%);
  color: #fff;
  border-color: #fb923c;
  animation: pulse-active 2s ease-in-out infinite;
  box-shadow: 0 4px 16px rgba(249, 115, 22, 0.4);
}

.lifecycle-circle.completed {
  background: linear-gradient(135deg, #10b981 0%, #059669 100%);
  color: #fff;
  border-color: #34d399;
  box-shadow: 0 4px 12px rgba(16, 185, 129, 0.3);
}

.lifecycle-circle.clickable {
  cursor: pointer;
}

.lifecycle-circle.clickable:hover {
  transform: scale(1.1);
  box-shadow: 0 6px 20px rgba(0,0,0,0.2);
}

.lifecycle-circle.selected {
  box-shadow: 0 0 0 4px rgba(249, 115, 22, 0.2);
  transform: scale(1.05);
}

@keyframes pulse-active {
  0%, 100% {
    box-shadow: 0 4px 16px rgba(249, 115, 22, 0.4);
  }
  50% {
    box-shadow: 0 4px 24px rgba(249, 115, 22, 0.6);
  }
}

.lifecycle-label {
  display: flex;
  flex-direction: column;
  align-items: center;
  text-align: center;
  min-height: 45px;
}

.label-text {
  font-size: 13px;
  font-weight: 700;
  margin-bottom: 4px;
  transition: color 0.3s ease;
}

.label-description {
  font-size: 10px;
  font-weight: 500;
  text-transform: uppercase;
  letter-spacing: 0.5px;
  transition: color 0.3s ease;
}

.lifecycle-label.pending .label-text {
  color: #9ca3af;
}

.lifecycle-label.pending .label-description {
  color: #d1d5db;
}

.lifecycle-label.active .label-text {
  color: #ea580c;
}

.lifecycle-label.active .label-description {
  color: #f97316;
}

.lifecycle-label.completed .label-text {
  color: #059669;
}

.lifecycle-label.completed .label-description {
  color: #10b981;
}

.lifecycle-connector {
  position: absolute;
  top: 28px;
  left: calc(50% + 28px);
  right: calc(-50% + 28px);
  height: 4px;
  z-index: 1;
  transition: background 0.3s ease;
  border-radius: 2px;
}

.lifecycle-connector.pending {
  background: #e5e7eb;
}

.lifecycle-connector.completed {
  background: linear-gradient(90deg, #10b981 0%, #059669 100%);
  box-shadow: 0 2px 8px rgba(16, 185, 129, 0.2);
}

@media (max-width: 768px) {
  .lifecycle-timeline-section {
    padding: 20px 16px;
  }

  .lifecycle-circle {
    width: 44px;
    height: 44px;
    font-size: 20px;
    margin-bottom: 8px;
  }

  .lifecycle-connector {
    top: 22px;
    left: calc(50% + 22px);
    right: calc(-50% + 22px);
    height: 3px;
  }

  .label-text {
    font-size: 11px;
  }

  .label-description {
    font-size: 9px;
  }
}
</style>
