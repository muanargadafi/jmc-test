export default defineNuxtRouteMiddleware(async (to) => {
  const { isLoggedIn, initAuth } = useAuth()

  await initAuth()

  if (!isLoggedIn.value) {
    return navigateTo(`/login?redirect=${encodeURIComponent(to.fullPath)}`)
  }
})
