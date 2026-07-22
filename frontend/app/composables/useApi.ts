/**
 * API client — points at Laravel by default.
 * Fallback: leave as `/api` to use Nuxt stub endpoints.
 */
export function useApi() {
  const config = useRuntimeConfig()
  const baseURL = (config.public.apiBase as string) || 'http://localhost:8000/api'

  function ensureCartSession(): string | null {
    if (!import.meta.client) return null
    let sid = localStorage.getItem('ferra_cart_session')
    if (!sid) {
      sid = crypto.randomUUID()
      localStorage.setItem('ferra_cart_session', sid)
    }
    return sid
  }

  function setCartSession(value: string) {
    if (!import.meta.client) return
    localStorage.setItem('ferra_cart_session', value)
  }

  function api<T>(path: string, opts?: Parameters<typeof $fetch<T>>[1]) {
    const url = path.startsWith('http')
      ? path
      : `${baseURL.replace(/\/$/, '')}/${path.replace(/^\//, '')}`

    const headers: Record<string, string> = {
      Accept: 'application/json',
      ...(opts?.headers as Record<string, string> | undefined),
    }

    const session = ensureCartSession()
    if (session) {
      headers['X-Cart-Session'] = session
    }

    return $fetch<T>(url, {
      ...opts,
      headers,
      onResponse({ response }) {
        const cartSession = response.headers.get('X-Cart-Session')
        if (cartSession) setCartSession(cartSession)
      },
    })
  }

  return { api, baseURL }
}
