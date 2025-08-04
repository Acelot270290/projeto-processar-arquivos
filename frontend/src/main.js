import { createApp } from 'vue'
import { createPinia } from 'pinia'
import App from './App.vue'
import router from './router'

// Estilos Bootstrap
import 'bootstrap/dist/css/bootstrap.min.css'
import 'bootstrap'
import 'bootstrap-vue-next/dist/bootstrap-vue-next.css'

// Importação individual dos componentes usados
import {
  BButton,
  BTable,
  BFormInput,
  BModal,
  BBadge
} from 'bootstrap-vue-next'

const app = createApp(App)

app.use(createPinia())
app.use(router)

// Registrar manualmente os componentes do BootstrapVueNext
app.component('BButton', BButton)
app.component('BTable', BTable)
app.component('BFormInput', BFormInput)
app.component('BModal', BModal)
app.component('BBadge', BBadge)

app.mount('#app')
