import { createApp } from 'vue'
import './style.css'
import App from './App.vue'
import router from './router'
import { createPinia } from 'pinia'
import ElementPlus from 'element-plus'
import 'element-plus/dist/index.css'
import './style.css'
import vue3GoogleLogin from 'vue3-google-login'
import 'element-plus/dist/index.css'

const pinia = createPinia()

createApp(App)
  .use(router)
  .use(pinia)
  .use(ElementPlus)
  .use(vue3GoogleLogin, {
    clientId: '193293694483-giv3vlq8iiod8i2ju6vusod77ert1p06.apps.googleusercontent.com'
  })
  .mount('#app')
