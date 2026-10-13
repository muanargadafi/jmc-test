const appName = process.env.NUXT_PUBLIC_APP_NAME || 'JMC Test'

export default defineNuxtConfig({
  modules: ['@nuxt/ui', '@nuxt/eslint', '@vueuse/nuxt', '@nuxt/image'],

  devtools: {
    enabled: true,
  },

  app: {
    head: {
      title: appName,
      titleTemplate: `%s - ${appName}`,
      htmlAttrs: {
        lang: 'en',
      },
      meta: [
        { charset: 'utf-8' },
        { name: 'viewport', content: 'width=device-width, initial-scale=1' },
      ],
      link: [{ rel: 'icon', href: '/favicon.ico' }],
    },
  },

  css: ['~/assets/css/main.css'],

  runtimeConfig: {
    public: {
      appName,
      apiBaseUrl: process.env.NUXT_PUBLIC_API_BASE_URL || '',
    },
  },

  devServer: {
    port: Number(process.env.PORT || 3000),
  },

  future: {
    compatibilityVersion: 4,
  },

  compatibilityDate: '2026-06-30',

  typescript: {
    strict: true,
  },

  eslint: {
    config: {
      stylistic: {
        commaDangle: 'never',
        braceStyle: '1tbs',
      },
    },
  },
})
