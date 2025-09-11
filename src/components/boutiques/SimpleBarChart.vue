<template>
    <div class="w-full h-full flex items-end justify-center">
      <svg 
        :width="width" 
        :height="height" 
        :viewBox="`0 0 ${width} ${height}`"
        class="w-full h-full"
      >
        <!-- Barres du graphique -->
        <g v-for="(value, index) in normalizedData" :key="index">
          <rect
            :x="index * barWidth + padding"
            :y="height - (value * (height - padding * 2)) - padding"
            :width="barWidth * 0.8"
            :height="value * (height - padding * 2)"
            :fill="getBarColor(index, value)"
            :class="[
              'transition-all duration-300 ease-in-out',
              'hover:opacity-80 cursor-pointer'
            ]"
            @mouseenter="showTooltip(index, originalData[index], $event)"
            @mouseleave="hideTooltip"
          >
            <animate
              attributeName="height"
              :from="0"
              :to="value * (height - padding * 2)"
              dur="0.8s"
              :begin="`${index * 0.1}s`"
              fill="freeze"
            />
            <animate
              attributeName="y"
              :from="height - padding"
              :to="height - (value * (height - padding * 2)) - padding"
              dur="0.8s"
              :begin="`${index * 0.1}s`"
              fill="freeze"
            />
          </rect>
          
          <!-- Valeurs au-dessus des barres (optionnel) -->
          <text
            v-if="showValues && value > 0.1"
            :x="index * barWidth + padding + (barWidth * 0.4)"
            :y="height - (value * (height - padding * 2)) - padding - 5"
            text-anchor="middle"
            :class="[
              'text-xs font-medium fill-gray-600',
              'opacity-0'
            ]"
          >
            {{ formatValue(originalData[index]) }}
            <animate
              attributeName="opacity"
              from="0"
              to="1"
              dur="0.5s"
              :begin="`${index * 0.1 + 0.8}s`"
              fill="freeze"
            />
          </text>
        </g>
        
        <!-- Ligne de base -->
        <line
          :x1="padding"
          :y1="height - padding"
          :x2="width - padding"
          :y2="height - padding"
          stroke="#E5E7EB"
          stroke-width="1"
        />
      </svg>
      
      <!-- Tooltip -->
      <div
        v-if="tooltip.show"
        :style="{
          position: 'fixed',
          left: tooltip.x + 'px',
          top: tooltip.y + 'px',
          transform: 'translate(-50%, -100%)'
        }"
        class="bg-gray-900 text-white text-xs rounded px-2 py-1 pointer-events-none z-50 shadow-lg"
      >
        {{ tooltip.content }}
      </div>
    </div>
  </template>
  
  <script setup>
  import { computed, reactive, ref } from 'vue'
  
  const props = defineProps({
    data: {
      type: Array,
      default: () => []
    },
    width: {
      type: Number,
      default: 200
    },
    height: {
      type: Number,
      default: 48
    },
    color: {
      type: String,
      default: '#F65A11'
    },
    gradient: {
      type: Boolean,
      default: true
    },
    showValues: {
      type: Boolean,
      default: false
    },
    animated: {
      type: Boolean,
      default: true
    },
    formatType: {
      type: String,
      default: 'number', // 'number', 'currency', 'percentage'
      validator: (value) => ['number', 'currency', 'percentage'].includes(value)
    }
  })
  
  const padding = 8
  const originalData = computed(() => props.data.filter(val => val !== null && val !== undefined))
  const maxValue = computed(() => Math.max(...originalData.value, 1))
  const barWidth = computed(() => (props.width - padding * 2) / Math.max(originalData.value.length, 1))
  
  // Normaliser les données entre 0 et 1
  const normalizedData = computed(() => {
    return originalData.value.map(value => value / maxValue.value)
  })
  
  const tooltip = reactive({
    show: false,
    x: 0,
    y: 0,
    content: ''
  })
  
  // Générer la couleur des barres avec gradient
  const getBarColor = (index, value) => {
    if (props.gradient) {
      const opacity = 0.6 + (value * 0.4) // Opacité entre 0.6 et 1
      return `${props.color}${Math.round(opacity * 255).toString(16).padStart(2, '0')}`
    }
    return props.color
  }
  
  // Formater les valeurs selon le type
  const formatValue = (value) => {
    switch (props.formatType) {
      case 'currency':
        return new Intl.NumberFormat('fr-FR', {
          style: 'currency',
          currency: 'XOF',
          minimumFractionDigits: 0,
          maximumFractionDigits: 0
        }).format(value).replace('XOF', 'FCFA')
      case 'percentage':
        return `${value}%`
      case 'number':
      default:
        if (value >= 1000) {
          return `${(value / 1000).toFixed(1)}k`
        }
        return value.toString()
    }
  }
  
  // Gestion du tooltip
  const showTooltip = (index, value, event) => {
    const rect = event.target.getBoundingClientRect()
    tooltip.x = rect.left + rect.width / 2
    tooltip.y = rect.top
    tooltip.content = formatValue(value)
    tooltip.show = true
  }
  
  const hideTooltip = () => {
    tooltip.show = false
  }
  </script>
  
  <style scoped>
  .fill-gray-600 {
    fill: #4B5563;
  }
  
  /* Animation pour les barres */
  @keyframes slideUp {
    from {
      transform: translateY(100%);
      opacity: 0;
    }
    to {
      transform: translateY(0);
      opacity: 1;
    }
  }
  
  /* Responsive */
  @media (max-width: 640px) {
    .text-xs {
      font-size: 0.65rem;
    }
  }
  </style>
  