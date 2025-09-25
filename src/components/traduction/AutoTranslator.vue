<template>
  <!-- On Desktop -->
  <div class="fixed top-20 right-4 z-[1111] desktop-only" style="margin-top: 300px; color: black; border:none">
    <button 
      @click="toggleLanguage"
      :disabled="isTranslating"
      class=" disabled:bg-gray-400 px-4 py-2 rounded-lg shadow-xl transition-all duration-200 flex items-center gap-2 min-w-[120px] justify-center translate-style"
    >
      <span class="text-sm font-bold" style="color: white">
        {{ isTranslating ? '⏳ Traduction...' : (currentLanguage === 'fr' ? '🇫🇷 Français' : '🇬🇧 English') }}
      </span>
    </button>
    
    <!-- Cache stats tooltip -->
    <div v-if="showStats" class="absolute top-full mt-2 right-0 bg-white rounded-lg shadow-xl border border-gray-200 p-3 text-xs text-gray-600 min-w-[200px]">
      <div class="font-semibold mb-1">📊 Statistiques Cache</div>
      <div>✅ Traductions en cache: {{ cacheStats.cached }}</div>
      <div>🔄 Nouvelles traductions: {{ cacheStats.new }}</div>
      <div>💾 Économie d'API: {{ cacheStats.saved }}%</div>
    </div>
  </div>


  <!--On Mobile device -->
  <div class="fixed top-20 right-4 z-[1111] mobile-only" style="margin-top: 300px; background-color: transparent;  border:none">
    <button 
      @click="toggleLanguage"
      :disabled="isTranslating"
      class="disabled:bg-gray-400  rounded-lg shadow-xl transition-all duration-200 translate-icon-mobile "  
    >
      
        {{ isTranslating ? '⏳' : (currentLanguage === 'fr' ? '🇫🇷 ' : '🇬🇧 ') }}
      
    </button>
    
    <!-- Cache stats tooltip -->
    <div v-if="showStats" class="absolute top-full mt-2 right-0 bg-white rounded-lg shadow-xl border border-gray-200 p-3 text-xs text-gray-600 min-w-[200px]">
      <div class="font-semibold mb-1">📊 Statistiques Cache</div>
      <div>✅ Traductions en cache: {{ cacheStats.cached }}</div>
      <div>🔄 Nouvelles traductions: {{ cacheStats.new }}</div>
      <div>💾 Économie d'API: {{ cacheStats.saved }}%</div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, nextTick } from 'vue'

const MYMEMORY_API_KEY = 'f8d4739abb435aefc95f'
const MYMEMORY_EMAIL = 'oumarsakone0@gmail.com'

const currentLanguage = ref('fr')
const isTranslating = ref(false)
const showStats = ref(false)
const originalTexts = new Map()
const translationCache = new Map()

const cacheStats = ref({
  cached: 0,
  new: 0,
  saved: 0
})

const loadCache = () => {
  try {
    const saved = localStorage.getItem('mymemory-cache')
    if (saved) {
      const parsed = JSON.parse(saved)
      Object.entries(parsed).forEach(([key, value]) => {
        translationCache.set(key, value)
      })
      console.log('[v0] Cache loaded:', translationCache.size, 'entries')
    }
  } catch (error) {
    console.error('[v0] Error loading cache:', error)
  }
}

const saveCache = () => {
  try {
    const cacheObj = {}
    translationCache.forEach((value, key) => {
      cacheObj[key] = value
    })
    localStorage.setItem('mymemory-cache', JSON.stringify(cacheObj))
    console.log('[v0] Cache saved:', translationCache.size, 'entries')
  } catch (error) {
    console.error('[v0] Error saving cache:', error)
  }
}

const getAllTextNodes = (element = document.body) => {
  const textNodes = []
  const walker = document.createTreeWalker(
    element,
    NodeFilter.SHOW_TEXT,
    {
      acceptNode: (node) => {
        const parent = node.parentElement
        if (!parent || 
            parent.tagName === 'SCRIPT' || 
            parent.tagName === 'STYLE' ||
            parent.tagName === 'NOSCRIPT' ||
            parent.closest('[data-no-translate]') ||
            node.textContent.trim() === '' ||
            node.textContent.trim().length < 3) {
          return NodeFilter.FILTER_REJECT
        }
        return NodeFilter.FILTER_ACCEPT
      }
    }
  )
  
  let node
  while (node = walker.nextNode()) {
    textNodes.push(node)
  }
  
  console.log('[v0] Found text nodes:', textNodes.length)
  return textNodes
}

const translateWithMyMemory = async (texts, sourceLang = 'fr', targetLang = 'en') => {
  try {
    console.log('[v0] Translating with MyMemory API...')
    const translations = []
    
    // Traiter par petits lots pour respecter les limites
    const batchSize = 5
    for (let i = 0; i < texts.length; i += batchSize) {
      const batch = texts.slice(i, i + batchSize)
      const batchPromises = batch.map(async (text) => {
        const cacheKey = `${text}_${sourceLang}_${targetLang}`
        
        if (translationCache.has(cacheKey)) {
          cacheStats.value.cached++
          return translationCache.get(cacheKey)
        }
        
        try {
          let url = `https://api.mymemory.translated.net/get?q=${encodeURIComponent(text)}&langpair=${sourceLang}|${targetLang}`
          url += `&key=${MYMEMORY_API_KEY}`
          url += `&de=${encodeURIComponent(MYMEMORY_EMAIL)}`

          console.log('[v0] API call for:', text.substring(0, 50) + '...')
          const response = await fetch(url)
          
          if (!response.ok) {
            throw new Error(`API error: ${response.status}`)
          }
          
          const data = await response.json()
          const translation = data.responseData?.translatedText || text
          
          translationCache.set(cacheKey, translation)
          cacheStats.value.new++
          
          return translation
        } catch (error) {
          console.error('[v0] Translation error for text:', text, error)
          return text
        }
      })
      
      const batchResults = await Promise.all(batchPromises)
      translations.push(...batchResults)
      
      // Pause entre les lots
      if (i + batchSize < texts.length) {
        await new Promise(resolve => setTimeout(resolve, 200))
      }
    }
    
    saveCache()
    
    const total = cacheStats.value.cached + cacheStats.value.new
    cacheStats.value.saved = total > 0 ? Math.round((cacheStats.value.cached / total) * 100) : 0
    
    return translations
  } catch (error) {
    console.error('[v0] MyMemory translation failed:', error)
    return texts
  }
}

const translatePage = async () => {
  isTranslating.value = true
  cacheStats.value = { cached: 0, new: 0, saved: 0 }
  
  console.log('[v0] Starting MyMemory page translation...')
  
  await nextTick()
  
  const textNodes = getAllTextNodes()
  console.log('[v0] Processing', textNodes.length, 'text nodes')
  
  // Collecter tous les textes uniques
  const textsToTranslate = []
  const nodeTextMap = new Map()
  
  textNodes.forEach(node => {
    const originalText = node.textContent.trim()
    if (originalText && originalText.length > 2) {
      if (!originalTexts.has(node)) {
        originalTexts.set(node, originalText)
      }
      
      textsToTranslate.push(originalText)
      nodeTextMap.set(node, originalText)
    }
  })
  
  if (textsToTranslate.length > 0) {
    console.log('[v0] Translating', textsToTranslate.length, 'texts')
    const translations = await translateWithMyMemory(textsToTranslate, 'fr', 'en')
    
    // Appliquer les traductions aux nœuds
    let processedCount = 0
    textNodes.forEach((node, index) => {
      if (translations[index] && translations[index] !== textsToTranslate[index]) {
        node.textContent = translations[index]
        processedCount++
      }
    })
    
    console.log('[v0] Translation completed. Processed:', processedCount, 'nodes')
    
    showStats.value = true
    setTimeout(() => {
      showStats.value = false
    }, 3000)
  }
  
  isTranslating.value = false
}

const restoreOriginalTexts = () => {
  console.log('[v0] Restoring original texts...')
  let restoredCount = 0
  originalTexts.forEach((originalText, node) => {
    if (node.parentElement) {
      node.textContent = originalText
      restoredCount++
    }
  })
  console.log('[v0] Restored', restoredCount, 'text nodes')
}

const toggleLanguage = async () => {
  console.log('[v0] Toggling language from', currentLanguage.value)
  
  if (currentLanguage.value === 'fr') {
    currentLanguage.value = 'en'
    await translatePage()
  } else {
    currentLanguage.value = 'fr'
    restoreOriginalTexts()
  }
  
  localStorage.setItem('preferred-language', currentLanguage.value)
}

onMounted(async () => {
  console.log('[v0] MyMemory Translator mounted')
  
  loadCache()
  
  const savedLang = localStorage.getItem('preferred-language')
  if (savedLang === 'en') {
    currentLanguage.value = 'en'
    setTimeout(async () => {
      await translatePage()
    }, 2000)
  }
})
</script>

<style scoped>

.translate-style{
  background-color: black;
  color: white;

}
.translate-style:hover{
  border: black;
}
.translate-icon-mobile {
  background-color: black;
  font-size: 1.5rem;
  padding:2px 8px
}
.translating-icon-mobile {
  background-color: black;
  font-size: 0.2rem; /* ou 32px */
  padding:2px 8px
}
/* Responsive */
  .mobile-only {
    display: none;
  }
  
  .desktop-only {
    display: block;
  }
  
  @media (max-width: 768px) {
    .mobile-only {
      display: block;
    }
    
    .desktop-only {
      display: none;
    }
  }
</style>