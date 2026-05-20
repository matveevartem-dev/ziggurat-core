import basicSsl from '@vitejs/plugin-basic-ssl'

export default defineNuxtConfig({
  app: {
    baseURL: '/v2/',
    buildAssetsDir: '/_nuxt/'
  },

  future: {
    compatibilityVersion: 4,
  },

  srcDir: 'app',

  runtimeConfig: {
    public: {
      // @ts-expect-error
      coreIp: process.env.CORE_IP || 'api',
      // @ts-expect-error
      crmApiUrl: process.env.NUXT_PUBLIC_CRM_API_URL,
      // @ts-expect-error
      onlyofficeApiUrl: process.env.NUXT_PUBLIC_ONLYOFFICE_API_URL,
      // @ts-expect-error
      baseUrl: process.env.NUXT_PUBLIC_BASE_URL,
      // @ts-expect-error
      crmUrl: process.env.NUXT_PUBLIC_CRM_URL,
      // @ts-expect-error
      ooUrl: process.env.NUXT_PUBLIC_OO_URL
    }
  },

  devServer: {
    https: false,
    host: 'localhost',
    // @ts-expect-error
    port: process.env.UI_PORT
  },

  vite: {
    plugins: [
      basicSsl()
    ],
    // ФИКС ОШИБКИ СОКЕТА: Явно настраиваем HMR под HTTPS
    server: {
      hmr: {
        protocol: 'wss',
        host: 'localhost'
      }
    }
  },

  nitro: {
    devProxy: {
      '/api': {
        target: 'http://api:${process.env.API_PORT}',
        changeOrigin: true,
        secure: false 
      }
    }
  }
})
