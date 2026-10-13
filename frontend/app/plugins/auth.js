export default defineNuxtPlugin((nuxtApp) => {
  nuxtApp.hook('auth:unauthorized', async () => {
    const { resetAuth } = useAuth()
    resetAuth()
    await navigateTo('/login')
  })
})
