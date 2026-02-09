import { defineConfig, loadEnv } from 'vite'
import react from '@vitejs/plugin-react'

// https://vite.dev/config/
export default defineConfig(({ mode }) => {
  const env = loadEnv(mode, process.cwd(), '')

  return {
    plugins: [react()],
    base: mode === 'production' ? '/ccp/' : '/',
    server: {
      port: 5173,
    },
    define: {
      'import.meta.env.VITE_API_URL': JSON.stringify(
        mode === 'production'
          ? '/ccp/api'
          : env.VITE_API_URL || 'http://localhost:8000/api'
      ),
    },
  }
})
