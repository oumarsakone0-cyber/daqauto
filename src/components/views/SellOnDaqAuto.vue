<template>
  <div class="sell-on-daqauto">
    <!-- Hero Section -->
    <section class="hero-section">
      <div class="container mx-auto px-4 py-16">
        <div class="text-center">
          <h1 class="text-4xl md:text-5xl font-bold text-white mb-6">
            Start Selling on Wabilli
          </h1>
          <p class="text-xl text-white/90 mb-8 max-w-3xl mx-auto">
            Join thousands of automotive sellers worldwide. Reach millions of buyers and grow your business with Wabilli's powerful platform.
          </p>
          <button @click="scrollToRegistration" class="btn-hero">
            Start Selling Now
            <svg class="inline-block ml-2" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <line x1="5" y1="12" x2="19" y2="12"/>
              <polyline points="12,5 19,12 12,19"/>
            </svg>
          </button>
        </div>
      </div>
    </section>

    <!-- Benefits Section -->
    <section class="benefits-section py-16 bg-gray-50">
      <div class="container mx-auto px-4">
        <h2 class="text-3xl md:text-4xl font-bold text-center mb-12 text-gray-900">
          Why Sell on Wabilli?
        </h2>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
          <div v-for="benefit in benefits" :key="benefit.id" class="benefit-card">
            <div class="icon-container">
              <component :is="benefit.icon" />
            </div>
            <h3 class="text-xl font-semibold mb-3">{{ benefit.title }}</h3>
            <p class="text-gray-600">{{ benefit.description }}</p>
          </div>
        </div>
      </div>
    </section>

    <!-- How It Works Section -->
    <section class="how-it-works-section py-16">
      <div class="container mx-auto px-4">
        <h2 class="text-3xl md:text-4xl font-bold text-center mb-12 text-gray-900">
          How It Works
        </h2>
        <div class="steps-container">
          <div v-for="(step, index) in steps" :key="step.id" class="step-card">
            <div class="step-number">{{ index + 1 }}</div>
            <div class="step-content">
              <h3 class="text-xl font-semibold mb-3">{{ step.title }}</h3>
              <p class="text-gray-600">{{ step.description }}</p>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- Pricing Section -->
    <section class="pricing-section py-16 bg-gray-50">
      <div class="container mx-auto px-4">
        <h2 class="text-3xl md:text-4xl font-bold text-center mb-12 text-gray-900">
          Transparent Pricing
        </h2>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8 max-w-5xl mx-auto">
          <div v-for="plan in pricingPlans" :key="plan.id" class="pricing-card" :class="{ featured: plan.featured }">
            <div v-if="plan.featured" class="featured-badge">Most Popular</div>
            <h3 class="text-2xl font-bold mb-2">{{ plan.name }}</h3>
            <div class="price-container">
              <span class="price">{{ plan.price }}</span>
              <span class="period">{{ plan.period }}</span>
            </div>
            <ul class="features-list">
              <li v-for="(feature, idx) in plan.features" :key="idx">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                  <polyline points="20,6 9,17 4,12"/>
                </svg>
                {{ feature }}
              </li>
            </ul>
            <button @click="selectPlan(plan)" class="btn-plan" :class="{ 'btn-featured': plan.featured }">
              {{ plan.buttonText }}
            </button>
          </div>
        </div>
      </div>
    </section>

    <!-- Terms & Conditions Section -->
    <section class="terms-section py-16">
      <div class="container mx-auto px-4 max-w-4xl">
        <h2 class="text-3xl md:text-4xl font-bold text-center mb-12 text-gray-900">
          Seller Terms & Conditions
        </h2>
        <div class="terms-content">
          <div v-for="term in terms" :key="term.id" class="term-item">
            <h3 class="text-xl font-semibold mb-3 flex items-center">
              <svg class="mr-2" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#fe7900" stroke-width="2">
                <circle cx="12" cy="12" r="10"/>
                <line x1="12" y1="8" x2="12" y2="12"/>
                <line x1="12" y1="16" x2="12.01" y2="16"/>
              </svg>
              {{ term.title }}
            </h3>
            <p class="text-gray-600 ml-8">{{ term.description }}</p>
          </div>
        </div>
      </div>
    </section>

    <!-- Registration Form Section -->
    <section id="registration" class="registration-section py-16 bg-gradient-to-br from-orange-50 to-orange-100">
      <div class="container mx-auto px-4 max-w-2xl">
        <h2 class="text-3xl md:text-4xl font-bold text-center mb-8 text-gray-900">
          Ready to Start Selling?
        </h2>
        <p class="text-center text-gray-600 mb-8">
          Fill out the form below and our team will contact you within 24 hours.
        </p>

        <div class="registration-card">
          <form @submit.prevent="submitRegistration">
            <div class="form-grid">
              <div class="form-group">
                <label for="company_name">Company Name *</label>
                <input
                  type="text"
                  id="company_name"
                  v-model="formData.company_name"
                  required
                  class="form-input"
                />
              </div>

              <div class="form-group">
                <label for="contact_name">Contact Name *</label>
                <input
                  type="text"
                  id="contact_name"
                  v-model="formData.contact_name"
                  required
                  class="form-input"
                />
              </div>

              <div class="form-group">
                <label for="email">Email Address *</label>
                <input
                  type="email"
                  id="email"
                  v-model="formData.email"
                  required
                  class="form-input"
                />
              </div>

              <div class="form-group">
                <label for="phone">Phone Number *</label>
                <input
                  type="tel"
                  id="phone"
                  v-model="formData.phone"
                  required
                  class="form-input"
                />
              </div>

              <div class="form-group full-width">
                <label for="business_type">Business Type *</label>
                <select
                  id="business_type"
                  v-model="formData.business_type"
                  required
                  class="form-input"
                >
                  <option value="">Select your business type</option>
                  <option value="manufacturer">Manufacturer</option>
                  <option value="distributor">Distributor</option>
                  <option value="retailer">Retailer</option>
                  <option value="individual">Individual Seller</option>
                </select>
              </div>

              <div class="form-group full-width">
                <label for="product_category">Product Category *</label>
                <select
                  id="product_category"
                  v-model="formData.product_category"
                  required
                  class="form-input"
                >
                  <option value="">Select your main product category</option>
                  <option value="vehicles">Vehicles</option>
                  <option value="parts">Auto Parts</option>
                  <option value="accessories">Accessories</option>
                  <option value="tools">Tools & Equipment</option>
                  <option value="other">Other</option>
                </select>
              </div>

              <div class="form-group full-width">
                <label for="website">Website (Optional)</label>
                <input
                  type="url"
                  id="website"
                  v-model="formData.website"
                  class="form-input"
                  placeholder="https://www.example.com"
                />
              </div>

              <div class="form-group full-width">
                <label for="message">Tell us about your business *</label>
                <textarea
                  id="message"
                  v-model="formData.message"
                  required
                  rows="4"
                  class="form-input"
                  placeholder="Describe your products, experience, and why you want to sell on Wabilli..."
                ></textarea>
              </div>

              <div class="form-group full-width">
                <label class="checkbox-label">
                  <input
                    type="checkbox"
                    v-model="formData.agree_terms"
                    required
                  />
                  <span>I agree to the Seller Terms & Conditions *</span>
                </label>
              </div>
            </div>

            <button
              type="submit"
              class="btn-submit"
              :disabled="isSubmitting"
            >
              <span v-if="!isSubmitting">Submit Application</span>
              <span v-else>Submitting...</span>
            </button>
          </form>
        </div>
      </div>
    </section>

    <!-- FAQ Section -->
    <section class="faq-section py-16 bg-white">
      <div class="container mx-auto px-4 max-w-4xl">
        <h2 class="text-3xl md:text-4xl font-bold text-center mb-12 text-gray-900">
          Frequently Asked Questions
        </h2>
        <div class="faq-list">
          <div v-for="(faq, index) in faqs" :key="faq.id" class="faq-item">
            <button @click="toggleFaq(index)" class="faq-question">
              <span>{{ faq.question }}</span>
              <svg
                class="faq-icon"
                :class="{ rotated: faq.open }"
                width="20"
                height="20"
                viewBox="0 0 24 24"
                fill="none"
                stroke="currentColor"
                stroke-width="2"
              >
                <polyline points="6,9 12,15 18,9"/>
              </svg>
            </button>
            <div v-show="faq.open" class="faq-answer">
              {{ faq.answer }}
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- CTA Section -->
    <section class="cta-section py-16 bg-gradient-to-r from-orange-500 to-orange-600">
      <div class="container mx-auto px-4 text-center">
        <h2 class="text-3xl md:text-4xl font-bold text-white mb-6">
          Join Wabilli Today
        </h2>
        <p class="text-xl text-white/90 mb-8 max-w-2xl mx-auto">
          Don't miss the opportunity to reach millions of automotive buyers worldwide.
        </p>
        <button @click="scrollToRegistration" class="btn-cta">
          Get Started Now
        </button>
      </div>
    </section>
  </div>
</template>

<script setup>
import { ref, reactive } from 'vue'
import { useRouter } from 'vue-router'

const router = useRouter()
const isSubmitting = ref(false)

const formData = reactive({
  company_name: '',
  contact_name: '',
  email: '',
  phone: '',
  business_type: '',
  product_category: '',
  website: '',
  message: '',
  agree_terms: false
})

const benefits = [
  {
    id: 1,
    icon: 'GlobalIcon',
    title: 'Global Reach',
    description: 'Access millions of buyers from over 100 countries worldwide.'
  },
  {
    id: 2,
    icon: 'ShieldIcon',
    title: 'Secure Payments',
    description: 'Protected transactions with multiple payment options and buyer protection.'
  },
  {
    id: 3,
    icon: 'ChartIcon',
    title: 'Analytics Dashboard',
    description: 'Track your sales, inventory, and performance with detailed analytics.'
  },
  {
    id: 4,
    icon: 'SupportIcon',
    title: '24/7 Support',
    description: 'Dedicated seller support team available around the clock.'
  },
  {
    id: 5,
    icon: 'TruckIcon',
    title: 'Logistics Support',
    description: 'Integrated shipping solutions and logistics partners worldwide.'
  },
  {
    id: 6,
    icon: 'MarketingIcon',
    title: 'Marketing Tools',
    description: 'Promote your products with built-in marketing and advertising tools.'
  }
]

const steps = [
  {
    id: 1,
    title: 'Register Your Business',
    description: 'Fill out the registration form with your business details and get verified.'
  },
  {
    id: 2,
    title: 'Set Up Your Store',
    description: 'Create your seller profile, add your products, and customize your storefront.'
  },
  {
    id: 3,
    title: 'Start Selling',
    description: 'List your products, manage orders, and start growing your business.'
  },
  {
    id: 4,
    title: 'Get Paid',
    description: 'Receive payments securely and track your earnings in real-time.'
  }
]

const pricingPlans = [
  {
    id: 1,
    name: 'Basic',
    price: 'Free',
    period: 'forever',
    features: [
      'Up to 50 product listings',
      '5% commission on sales',
      'Basic analytics',
      'Email support',
      'Standard shipping integration'
    ],
    buttonText: 'Start Free',
    featured: false
  },
  {
    id: 2,
    name: 'Professional',
    price: '$49',
    period: '/month',
    features: [
      'Unlimited product listings',
      '3% commission on sales',
      'Advanced analytics & reports',
      'Priority support',
      'Premium shipping options',
      'Marketing tools',
      'Custom branding'
    ],
    buttonText: 'Get Started',
    featured: true
  },
  {
    id: 3,
    name: 'Enterprise',
    price: 'Custom',
    period: 'pricing',
    features: [
      'Everything in Professional',
      'Dedicated account manager',
      'API access',
      'Custom integration',
      'Volume discounts',
      'White-label options'
    ],
    buttonText: 'Contact Sales',
    featured: false
  }
]

const terms = [
  {
    id: 1,
    title: 'Product Quality',
    description: 'All products must be genuine, accurately described, and comply with local and international regulations.'
  },
  {
    id: 2,
    title: 'Pricing & Fees',
    description: 'Sellers are responsible for setting competitive prices. Platform fees apply based on your plan.'
  },
  {
    id: 3,
    title: 'Order Fulfillment',
    description: 'Orders must be processed within 2 business days. Shipping must be completed within the specified timeframe.'
  },
  {
    id: 4,
    title: 'Customer Service',
    description: 'Maintain professional communication with buyers and respond to inquiries within 24 hours.'
  },
  {
    id: 5,
    title: 'Returns & Refunds',
    description: 'Follow our return policy guidelines and process refunds within 7 days of receiving returned items.'
  },
  {
    id: 6,
    title: 'Account Suspension',
    description: 'Violation of terms may result in account suspension or termination. We reserve the right to remove listings that violate our policies.'
  }
]

const faqs = ref([
  {
    id: 1,
    question: 'How long does it take to get approved?',
    answer: 'Most applications are reviewed within 24-48 hours. Once approved, you can immediately start listing your products.',
    open: false
  },
  {
    id: 2,
    question: 'What are the payment terms?',
    answer: 'Payments are processed weekly. You will receive your earnings via bank transfer or your preferred payment method minus applicable fees.',
    open: false
  },
  {
    id: 3,
    question: 'Can I sell internationally?',
    answer: 'Yes! Wabilli supports international selling. You can choose which countries to ship to and set different shipping rates.',
    open: false
  },
  {
    id: 4,
    question: 'What kind of products can I sell?',
    answer: 'You can sell vehicles, auto parts, accessories, tools, and related automotive products. Prohibited items include counterfeit goods and items that violate regulations.',
    open: false
  },
  {
    id: 5,
    question: 'Do I need a business license?',
    answer: 'While individual sellers are welcome, business sellers need to provide valid business documentation during the verification process.',
    open: false
  },
  {
    id: 6,
    question: 'How do I handle shipping?',
    answer: 'You can use our integrated shipping partners or arrange your own shipping. We provide tools to generate shipping labels and track deliveries.',
    open: false
  }
])

const toggleFaq = (index) => {
  faqs.value[index].open = !faqs.value[index].open
}

const scrollToRegistration = () => {
  const element = document.getElementById('registration')
  if (element) {
    element.scrollIntoView({ behavior: 'smooth' })
  }
}

const selectPlan = (plan) => {
  // Scroll to registration form when a plan is selected
  scrollToRegistration()
  // Could also store selected plan in formData if needed
}

const submitRegistration = async () => {
  isSubmitting.value = true

  try {
    // Here you would typically send the form data to your backend
    console.log('Form submitted:', formData)

    // Simulate API call
    await new Promise(resolve => setTimeout(resolve, 2000))

    alert('Thank you for your application! Our team will contact you within 24 hours.')

    // Reset form
    Object.keys(formData).forEach(key => {
      if (typeof formData[key] === 'boolean') {
        formData[key] = false
      } else {
        formData[key] = ''
      }
    })
  } catch (error) {
    alert('Something went wrong. Please try again.')
    console.error(error)
  } finally {
    isSubmitting.value = false
  }
}

// Simple SVG icon components as strings (you can replace with actual icon components)
const GlobalIcon = {
  template: `
    <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="#fe7900" stroke-width="2">
      <circle cx="12" cy="12" r="10"/>
      <line x1="2" y1="12" x2="22" y2="12"/>
      <path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"/>
    </svg>
  `
}

const ShieldIcon = {
  template: `
    <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="#fe7900" stroke-width="2">
      <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/>
    </svg>
  `
}

const ChartIcon = {
  template: `
    <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="#fe7900" stroke-width="2">
      <line x1="18" y1="20" x2="18" y2="10"/>
      <line x1="12" y1="20" x2="12" y2="4"/>
      <line x1="6" y1="20" x2="6" y2="14"/>
    </svg>
  `
}

const SupportIcon = {
  template: `
    <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="#fe7900" stroke-width="2">
      <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/>
    </svg>
  `
}

const TruckIcon = {
  template: `
    <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="#fe7900" stroke-width="2">
      <rect x="1" y="3" width="15" height="13"/>
      <polygon points="16,8 20,8 23,11 23,16 16,16 16,8"/>
      <circle cx="5.5" cy="18.5" r="2.5"/>
      <circle cx="18.5" cy="18.5" r="2.5"/>
    </svg>
  `
}

const MarketingIcon = {
  template: `
    <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="#fe7900" stroke-width="2">
      <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/>
      <polyline points="9,22 9,12 15,12 15,22"/>
    </svg>
  `
}
</script>

<style scoped>
.sell-on-daqauto {
  min-height: 100vh;
}

/* Hero Section */
.hero-section {
  background: linear-gradient(135deg, #fe7900 0%, #ff9f40 100%);
  position: relative;
  overflow: hidden;
}

.hero-section::before {
  content: '';
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: url('data:image/svg+xml,<svg width="100" height="100" xmlns="http://www.w3.org/2000/svg"><circle cx="50" cy="50" r="2" fill="white" opacity="0.1"/></svg>');
  opacity: 0.3;
}

.btn-hero {
  background: white;
  color: #fe7900;
  padding: 16px 32px;
  border-radius: 8px;
  font-weight: 600;
  font-size: 18px;
  border: none;
  cursor: pointer;
  transition: all 0.3s ease;
  box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
}

.btn-hero:hover {
  transform: translateY(-2px);
  box-shadow: 0 6px 12px rgba(0, 0, 0, 0.15);
}

/* Benefits Section */
.benefit-card {
  background: white;
  padding: 32px;
  border-radius: 12px;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
  transition: transform 0.3s ease, box-shadow 0.3s ease;
}

.benefit-card:hover {
  transform: translateY(-4px);
  box-shadow: 0 4px 16px rgba(0, 0, 0, 0.15);
}

.icon-container {
  margin-bottom: 16px;
}

/* How It Works */
.steps-container {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
  gap: 32px;
  max-width: 1200px;
  margin: 0 auto;
}

.step-card {
  display: flex;
  gap: 16px;
}

.step-number {
  flex-shrink: 0;
  width: 48px;
  height: 48px;
  background: linear-gradient(135deg, #fe7900, #ff9f40);
  color: white;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 24px;
  font-weight: bold;
}

/* Pricing Section */
.pricing-card {
  background: white;
  padding: 32px;
  border-radius: 12px;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
  position: relative;
  transition: transform 0.3s ease, box-shadow 0.3s ease;
}

.pricing-card.featured {
  border: 2px solid #fe7900;
  transform: scale(1.05);
}

.pricing-card:hover {
  transform: translateY(-4px) scale(1.05);
  box-shadow: 0 8px 24px rgba(0, 0, 0, 0.15);
}

.featured-badge {
  position: absolute;
  top: -12px;
  left: 50%;
  transform: translateX(-50%);
  background: #fe7900;
  color: white;
  padding: 4px 16px;
  border-radius: 20px;
  font-size: 14px;
  font-weight: 600;
}

.price-container {
  margin: 24px 0;
}

.price {
  font-size: 48px;
  font-weight: bold;
  color: #fe7900;
}

.period {
  color: #666;
  margin-left: 8px;
}

.features-list {
  list-style: none;
  padding: 0;
  margin: 24px 0;
}

.features-list li {
  display: flex;
  align-items: center;
  gap: 12px;
  padding: 8px 0;
  color: #666;
}

.features-list svg {
  flex-shrink: 0;
  stroke: #22c55e;
}

.btn-plan {
  width: 100%;
  padding: 12px;
  border-radius: 8px;
  border: 2px solid #fe7900;
  background: white;
  color: #fe7900;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.3s ease;
}

.btn-plan:hover {
  background: #fe7900;
  color: white;
}

.btn-featured {
  background: #fe7900;
  color: white;
}

.btn-featured:hover {
  background: #e56d00;
}

/* Terms Section */
.terms-content {
  background: white;
  padding: 32px;
  border-radius: 12px;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
}

.term-item {
  padding: 24px 0;
  border-bottom: 1px solid #e5e7eb;
}

.term-item:last-child {
  border-bottom: none;
}

/* Registration Form */
.registration-card {
  background: white;
  padding: 40px;
  border-radius: 12px;
  box-shadow: 0 4px 16px rgba(0, 0, 0, 0.1);
}

.form-grid {
  display: grid;
  grid-template-columns: repeat(2, 1fr);
  gap: 24px;
}

.form-group {
  display: flex;
  flex-direction: column;
}

.form-group.full-width {
  grid-column: 1 / -1;
}

.form-group label {
  font-weight: 600;
  margin-bottom: 8px;
  color: #374151;
}

.form-input {
  padding: 12px;
  border: 1px solid #d1d5db;
  border-radius: 8px;
  font-size: 16px;
  transition: border-color 0.3s ease;
}

.form-input:focus {
  outline: none;
  border-color: #fe7900;
}

.checkbox-label {
  display: flex;
  align-items: center;
  gap: 8px;
  cursor: pointer;
}

.checkbox-label input[type="checkbox"] {
  width: 18px;
  height: 18px;
  cursor: pointer;
}

.btn-submit {
  width: 100%;
  padding: 16px;
  background: linear-gradient(135deg, #fe7900, #ff9f40);
  color: white;
  border: none;
  border-radius: 8px;
  font-size: 18px;
  font-weight: 600;
  cursor: pointer;
  margin-top: 24px;
  transition: all 0.3s ease;
}

.btn-submit:hover:not(:disabled) {
  transform: translateY(-2px);
  box-shadow: 0 4px 12px rgba(254, 121, 0, 0.3);
}

.btn-submit:disabled {
  opacity: 0.6;
  cursor: not-allowed;
}

/* FAQ Section */
.faq-list {
  space-y: 16px;
}

.faq-item {
  background: white;
  border-radius: 8px;
  overflow: hidden;
  box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
}

.faq-question {
  width: 100%;
  padding: 20px;
  background: white;
  border: none;
  text-align: left;
  font-size: 18px;
  font-weight: 600;
  cursor: pointer;
  display: flex;
  justify-content: space-between;
  align-items: center;
  transition: background 0.3s ease;
}

.faq-question:hover {
  background: #f9fafb;
}

.faq-icon {
  transition: transform 0.3s ease;
}

.faq-icon.rotated {
  transform: rotate(180deg);
}

.faq-answer {
  padding: 0 20px 20px;
  color: #666;
  line-height: 1.6;
}

/* CTA Section */
.cta-section {
  position: relative;
}

.btn-cta {
  background: white;
  color: #fe7900;
  padding: 16px 48px;
  border-radius: 8px;
  font-weight: 600;
  font-size: 18px;
  border: none;
  cursor: pointer;
  transition: all 0.3s ease;
  box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
}

.btn-cta:hover {
  transform: translateY(-2px);
  box-shadow: 0 6px 12px rgba(0, 0, 0, 0.15);
}

/* Responsive */
@media (max-width: 768px) {
  .form-grid {
    grid-template-columns: 1fr;
  }

  .pricing-card.featured {
    transform: scale(1);
  }

  .registration-card {
    padding: 24px;
  }
}
</style>
