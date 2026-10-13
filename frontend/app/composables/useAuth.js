let clientInitPromise = null

export const useAuth = () => {
  const api = useApi()
  const config = useRuntimeConfig()
  const event = import.meta.server ? useRequestEvent() : null

  const AUTH_PREFIX = `${config.public.apiPrefix}/auth`

  const user = useState('auth:user', () => null)
  const initialized = useState('auth:initialized', () => false)

  const isLoggedIn = computed(() => !!user.value)

  const AUTH_OPTS = { skipAuthRedirect: true }

  const resetAuth = () => {
    user.value = null
    initialized.value = true
  }

  const fetchUser = async () => {
    try {
      const response = await api(`${AUTH_PREFIX}/me`, AUTH_OPTS)
      user.value = response.data
      return user.value
    } catch {
      resetAuth()
      return null
    } finally {
      initialized.value = true
    }
  }

  const initAuth = async () => {
    if (initialized.value) return user.value

    if (import.meta.server) {
      if (!event) return fetchUser()

      if (!event.context.__authInitPromise) {
        event.context.__authInitPromise = fetchUser().finally(() => {
          event.context.__authInitPromise = null
        })
      }
      return event.context.__authInitPromise
    }

    if (!clientInitPromise) {
      clientInitPromise = fetchUser().finally(() => {
        clientInitPromise = null
      })
    }

    return clientInitPromise
  }

  const login = async (payload) => {
    await api(`${AUTH_PREFIX}/login`, {
      ...AUTH_OPTS,
      method: 'POST',
      body: payload,
    })

    await fetchUser()

    return user.value
  }

  const logout = async () => {
    try {
      await api(`${AUTH_PREFIX}/logout`, {
        ...AUTH_OPTS,
        method: 'POST',
      })
    } finally {
      resetAuth()
      await navigateTo('/login')
    }
  }

  return {
    user,
    initialized,
    isLoggedIn,
    initAuth,
    fetchUser,
    login,
    logout,
    resetAuth,
  }
}
