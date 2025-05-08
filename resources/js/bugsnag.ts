import Bugsnag from '@bugsnag/js'
import BugsnagPluginVue from '@bugsnag/plugin-vue'
import BugsnagPerformance from '@bugsnag/browser-performance'
import type { Plugin } from 'vue'

const BUGSNAG_API_KEY = import.meta.env.VITE_BUGSNAG_FRONTEND_KEY

// Só inicializa o Bugsnag se tiver a API key configurada
if (BUGSNAG_API_KEY) {
  // Inicializa o Bugsnag
  Bugsnag.start({
    apiKey: BUGSNAG_API_KEY,
    plugins: [new BugsnagPluginVue()],
    releaseStage: import.meta.env.MODE,
    appVersion: import.meta.env.VITE_APP_VERSION || '1.0.0'
  })

  // Inicializa o Bugsnag Performance
  BugsnagPerformance.start({ 
    apiKey: BUGSNAG_API_KEY,
    releaseStage: import.meta.env.MODE
  })

  if (import.meta.env.MODE === 'development') {
    console.log('🚀 Bugsnag inicializado com sucesso!')
  }
} else {
  if (import.meta.env.MODE === 'development') {
    console.log('⚠️ Bugsnag não inicializado: API key não configurada')
  }
}

// Exporta o plugin Vue do Bugsnag se estiver inicializado
const bugsnagVue = BUGSNAG_API_KEY ? Bugsnag.getPlugin('vue') as Plugin : undefined

// Exporta o Bugsnag para uso em outros arquivos
export { bugsnagVue }
export default Bugsnag 