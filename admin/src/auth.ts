const API_BASE = import.meta.env.VITE_API_BASE || 'http://127.0.0.1:8000/api'
const TOKEN_KEY = 'ferra_admin_jwt'
const USER_KEY = 'ferra_admin_user'

export interface AdminUser {
  id: number
  name: string
  email: string
}

export interface LoginResponse {
  access_token: string
  token_type: string
  expires_in: number
  user: AdminUser
}

function getToken(): string | null {
  return localStorage.getItem(TOKEN_KEY)
}

function setSession(token: string, user: AdminUser) {
  localStorage.setItem(TOKEN_KEY, token)
  localStorage.setItem(USER_KEY, JSON.stringify(user))
}

export function clearSession() {
  localStorage.removeItem(TOKEN_KEY)
  localStorage.removeItem(USER_KEY)
}

export function isAuthenticated(): boolean {
  return Boolean(getToken())
}

export function getAdminEmail(): string | null {
  const raw = localStorage.getItem(USER_KEY)
  if (!raw) return null
  try {
    return (JSON.parse(raw) as AdminUser).email
  } catch {
    return null
  }
}

export function getAdminUser(): AdminUser | null {
  const raw = localStorage.getItem(USER_KEY)
  if (!raw) return null
  try {
    return JSON.parse(raw) as AdminUser
  } catch {
    return null
  }
}

export async function api<T>(
  path: string,
  opts: RequestInit = {},
): Promise<T> {
  const headers = new Headers(opts.headers || {})
  headers.set('Accept', 'application/json')
  if (opts.body && !headers.has('Content-Type')) {
    headers.set('Content-Type', 'application/json')
  }

  const token = getToken()
  if (token) {
    headers.set('Authorization', `Bearer ${token}`)
  }

  const response = await fetch(`${API_BASE}${path}`, {
    ...opts,
    headers,
  })

  if (response.status === 401) {
    clearSession()
    if (!path.includes('/auth/login')) {
      window.location.href = '/login'
    }
  }

  const data = await response.json().catch(() => ({}))

  if (!response.ok) {
    const errors = (data as { errors?: Record<string, string[]> }).errors
    const firstError = errors
      ? Object.values(errors).flat()[0]
      : undefined
    const message =
      firstError
      || (data as { message?: string }).message
      || 'Request failed'
    throw new Error(message)
  }

  return data as T
}

export async function login(email: string, password: string): Promise<AdminUser> {
  const data = await api<LoginResponse>('/auth/login', {
    method: 'POST',
    body: JSON.stringify({ email, password }),
  })

  setSession(data.access_token, data.user)
  return data.user
}

export async function logout(): Promise<void> {
  try {
    if (getToken()) {
      await api('/auth/logout', { method: 'POST' })
    }
  } catch {
    // still clear local session
  } finally {
    clearSession()
  }
}

export async function fetchMe(): Promise<AdminUser> {
  const res = await api<{ data: AdminUser }>('/auth/me')
  localStorage.setItem(USER_KEY, JSON.stringify(res.data))
  return res.data
}
