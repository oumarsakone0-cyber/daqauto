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
    host: true,
    open: true,
    proxy: {
      // 本地开发时走代理，避免跨域和 decodeVIN2 请求异常
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
      }
    }
  }
})