import { createApp } from 'vue'
import './style.css'
import App from './App.vue'
import router from './router'
import ElementPlus from 'element-plus'
import 'element-plus/dist/index.css'
import './assets/style.css'
import './color.css'

createApp(App)
  .use(router)
  .use(ElementPlus)
  .mount('#app')
