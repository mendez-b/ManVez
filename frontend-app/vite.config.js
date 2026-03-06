import { defineConfig } from 'vite'
import vue from '@vitejs/plugin-vue'

export default defineConfig({
  plugins: [vue()],
  server: {
    proxy: {
      // Cuando en tu código pongas '/api/mangadex', Vite lo mandará a la API real
      '/api/mangadex': {
        target: 'https://api.mangadex.org',
        changeOrigin: true,
        // Esto quita '/api/mangadex' de la URL antes de enviarla a MangaDex
        rewrite: (path) => path.replace(/^\/api\/mangadex/, '')
      }
    }
  }
})