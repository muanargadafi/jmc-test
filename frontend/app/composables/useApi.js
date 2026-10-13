import { appendResponseHeader } from 'h3'

let clientCsrfPromise = null

const CSRF_COOKIE_PATH = '/sanctum/csrf-cookie'
const CSRF_COOKIE_NAME = 'XSRF-TOKEN'
const CSRF_HEADER_NAME = 'X-XSRF-TOKEN'
const MUTATING_METHODS = ['POST', 'PUT', 'PATCH', 'DELETE']

export const useApi = () => {
  const config = useRuntimeConfig()
  const baseURL = config.public.apiBaseUrl || ''
  const requestHeaders = useRequestHeaders(['cookie'])
  const event = import.meta.server ? useRequestEvent() : null
  const appOrigin = import.meta.server ? useRequestURL().origin : ''
  const nuxtApp = useNuxtApp()

  if (import.meta.server && event && event.context.__cookieJar === undefined) {
    event.context.__cookieJar = requestHeaders.cookie || ''
  }

  const getSsrCookie = () => event?.context.__cookieJar || ''

  const readSetCookie = (headers) => {
    if (typeof headers.getSetCookie === 'function') {
      return headers.getSetCookie()
    }

    const raw = headers.get('set-cookie')

    return raw ? [raw] : []
  }

  const mergeSsrCookie = (setCookies) => {
    if (!event || !setCookies.length) return

    const jar = new Map()

    for (const pair of getSsrCookie().split(';')) {
      const [k, ...v] = pair.trim().split('=')
      if (k) jar.set(k, v.join('='))
    }

    for (const sc of setCookies) {
      const firstPart = sc.split(';')[0] ?? ''
      const [k, ...v] = firstPart.trim().split('=')
      if (k) jar.set(k, v.join('='))
    }

    event.context.__cookieJar = [...jar].map(([key, val]) => `${key}=${val}`).join('; ')
  }

  // Baca cookie XSRF-TOKEN dari SSR cookie jar (server) atau document.cookie (client)
  const readCookieValue = (name) => {
    const cookieStr = import.meta.server ? getSsrCookie() : document.cookie
    const match = cookieStr.match(new RegExp(`(?:^|;\\s*)${name}=([^;]*)`))
    return match ? decodeURIComponent(match[1]) : null
  }

  const baseFetch = $fetch.create({
    baseURL,
    credentials: 'include',
    timeout: 10000,

    onRequest({ options }) {
      const headers = new Headers(options.headers)

      headers.set('Cache-Control', 'no-cache')
      headers.set('Pragma', 'no-cache')

      if (import.meta.server) {
        const cookie = getSsrCookie()
        if (cookie) headers.set('cookie', cookie)

        headers.set('Origin', appOrigin)
        headers.set('Referer', `${appOrigin}/`)
      }

      const method = options.method?.toUpperCase() ?? 'GET'
      if (MUTATING_METHODS.includes(method)) {
        const csrfToken = readCookieValue(CSRF_COOKIE_NAME)
        if (csrfToken) headers.set(CSRF_HEADER_NAME, csrfToken)
      }

      if (!headers.has('Accept')) headers.set('Accept', 'application/json')

      options.headers = headers
    },

    onResponse({ response }) {
      const setCookies = readSetCookie(response.headers)
      if (import.meta.server && event && setCookies.length) {
        for (const cookie of setCookies) {
          appendResponseHeader(event, 'set-cookie', cookie)
        }
        mergeSsrCookie(setCookies)
      }
    },
  })

  const requestCsrfCookie = () => baseFetch(CSRF_COOKIE_PATH)

  // Pastikan cookie XSRF-TOKEN ada sebelum request yang mengubah state (POST/PUT/PATCH/DELETE)
  const ensureCsrfCookie = async () => {
    if (readCookieValue(CSRF_COOKIE_NAME)) return

    if (import.meta.server && event) {
      if (!event.context.__csrfPromise) {
        event.context.__csrfPromise = requestCsrfCookie().finally(() => {
          event.context.__csrfPromise = null
        })
      }
      return event.context.__csrfPromise
    }

    if (!clientCsrfPromise) {
      clientCsrfPromise = requestCsrfCookie().finally(() => {
        clientCsrfPromise = null
      })
    }
    return clientCsrfPromise
  }

  const api = async (request, options = {}) => {
    const { skipAuthRedirect, ...fetchOptions } = options
    const method = fetchOptions.method?.toUpperCase() ?? 'GET'

    const notifyUnauthorized = async () => {
      if (skipAuthRedirect) return
      await nuxtApp.callHook('auth:unauthorized')
    }

    try {
      if (MUTATING_METHODS.includes(method)) {
        await ensureCsrfCookie()
      }

      return await baseFetch(request, fetchOptions)
    } catch (err) {
      const status = getErrorStatus(err)
      if (status === 401) await notifyUnauthorized()
      throw err
    }
  }

  return api
}
