<script>
import { ref, onMounted, onUnmounted } from 'vue'

export function useWebSocket(url, options = {}) {
  const socket = ref(null)
  const isConnected = ref(false)
  const isConnecting = ref(false)
  const error = ref(null)
  const lastMessage = ref(null)
  
  const {
    autoReconnect = true,
    reconnectInterval = 3000,
    maxReconnectAttempts = 5,
    onMessage = () => {},
    onConnect = () => {},
    onDisconnect = () => {},
    onError = () => {}
  } = options
  
  let reconnectAttempts = 0
  let reconnectTimer = null
  
  const connect = () => {
    if (isConnecting.value || isConnected.value) return
    
    isConnecting.value = true
    error.value = null
    
    try {
      socket.value = new WebSocket(url)
      
      socket.value.onopen = () => {
        isConnected.value = true
        isConnecting.value = false
        reconnectAttempts = 0
        console.log('WebSocket connecté')
        onConnect()
      }
      
      socket.value.onmessage = (event) => {
        try {
          const data = JSON.parse(event.data)
          lastMessage.value = data
          onMessage(data)
        } catch (e) {
          console.error('Erreur parsing message WebSocket:', e)
        }
      }
      
      socket.value.onclose = (event) => {
        isConnected.value = false
        isConnecting.value = false
        console.log('WebSocket fermé:', event.code, event.reason)
        onDisconnect(event)
        
        // Auto-reconnexion
        if (autoReconnect && reconnectAttempts < maxReconnectAttempts) {
          reconnectAttempts++
          console.log(`Tentative de reconnexion ${reconnectAttempts}/${maxReconnectAttempts}`)
          reconnectTimer = setTimeout(connect, reconnectInterval)
        }
      }
      
      socket.value.onerror = (event) => {
        error.value = 'Erreur WebSocket'
        isConnecting.value = false
        console.error('Erreur WebSocket:', event)
        onError(event)
      }
      
    } catch (e) {
      error.value = e.message
      isConnecting.value = false
      console.error('Erreur création WebSocket:', e)
    }
  }
  
  const disconnect = () => {
    if (reconnectTimer) {
      clearTimeout(reconnectTimer)
      reconnectTimer = null
    }
    
    if (socket.value) {
      socket.value.close()
      socket.value = null
    }
    
    isConnected.value = false
    isConnecting.value = false
  }
  
  const send = (data) => {
    if (isConnected.value && socket.value) {
      const message = typeof data === 'string' ? data : JSON.stringify(data)
      socket.value.send(message)
      return true
    }
    console.warn('WebSocket non connecté, impossible d\'envoyer:', data)
    return false
  }
  
  onMounted(() => {
    connect()
  })
  
  onUnmounted(() => {
    disconnect()
  })
  
  return {
    socket,
    isConnected,
    isConnecting,
    error,
    lastMessage,
    connect,
    disconnect,
    send
  }
}
</script>