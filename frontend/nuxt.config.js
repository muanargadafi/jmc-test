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
        { name: 'description', content: 'Aplikasi Pengelolaan Barang PT JMC Indonesia' },
      ],
      link: [{ rel: 'icon', type: 'image/x-icon', href: '/favicon.png' }],
    },
  },

  css: ['~/assets/css/main.css'],

  colorMode: {
    preference: 'light',
    fallback: 'light',
    classSuffix: '',
  },

  runtimeConfig: {
    public: {
      appName,
      apiBaseUrl: process.env.NUXT_PUBLIC_API_BASE_URL || '',
      apiPrefix: process.env.NUXT_PUBLIC_API_PREFIX || '',
    },
  },

  devServer: {
    port: Number(process.env.PORT || 3000),
  },

  future: {
    compatibilityVersion: 4,
  },

  compatibilityDate: '2026-06-30',

  eslint: {
    config: {
      stylistic: {
        commaDangle: 'never',
        braceStyle: '1tbs',
      },
    },
  },

  fonts: {
    families: [
      {
        name: 'Inter',
        provider: 'google',
        weights: [300, 400, 500, 600, 700],
        styles: ['normal'],
      },
    ],
  },
})
