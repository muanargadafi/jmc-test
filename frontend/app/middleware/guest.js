export default defineNuxtRouteMiddleware(async () => {
  const { isLoggedIn, initAuth } = useAuth()

  await initAuth()

  if (isLoggedIn.value) {
    return navigateTo('/')
  }
})
