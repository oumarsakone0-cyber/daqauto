import { defineConfig } from 'vite'
import vue from '@vitejs/plugin-vue'
import tailwindcss from '@tailwindcss/vite'

// https://vite.dev/config/
export default defineConfig({
  plugins: [
    vue(), 
    tailwindcss()
  ],
  server: {
    port: 5173, // 默认端口
    host: true, // 允许外部访问（局域网内其他设备也可以访问）
    open: true, // 自动打开浏览器
    proxy: {
      // 本地开发时把 /api_adjame 代理到远程后端，避免 CORS 和“服务器连接错误”
      '/api_adjame': {
        target: 'https://sastock.com',
        changeOrigin: true,
        secure: true,
      },
      '/api/vin': {
        target: 'https://api.tanshuapi.com',
        changeOrigin: true,
        rewrite: (path) => path.replace(/^\/api\/vin/, '/api/vin'),
        secure: true,
      },
      // 翻译 API 走代理，避免 ERR_PROXY_CONNECTION_FAILED 导致 VIN 解码卡住
      '/api/translate': {
        target: 'https://api.mymemory.translated.net',
        changeOrigin: true,
        rewrite: (path) => path.replace(/^\/api\/translate/, ''),
        secure: true,
      },
    },
  },
})