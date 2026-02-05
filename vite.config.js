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
      '/api/vin': {
        target: 'https://api.tanshuapi.com',
        changeOrigin: true,
        rewrite: (path) => path.replace(/^\/api\/vin/, '/api/vin'),
        secure: true,
      }
    }
  }
})