import api from '../lib/api'
import type {
  User,
  LoginCredentials,
  RegisterData,
  AuthResponse,
} from '../types'

export const authService = {
  async login(credentials: LoginCredentials): Promise<AuthResponse> {
    const { data } = await api.post<AuthResponse>('/login', credentials)
    if (data.token) {
      localStorage.setItem('auth_token', data.token)
      localStorage.setItem('user', JSON.stringify(data.user))
    }
    return data
  },

  async loginWithSSO(): Promise<void> {
    // Store current location as return URL
    const returnUrl = window.location.pathname || '/dashboard'
    sessionStorage.setItem('sso_return_url', returnUrl)

    // Redirect to SSO login endpoint
    // In production, add ?mock_sso=1 for testing with mock headers in development
    const isDevelopment = import.meta.env.DEV
    const mockParam = isDevelopment ? '?mock_sso=1' : ''

    window.location.href = `/api/auth/sso/login${mockParam}`
  },

  async handleSsoCallback(token: string): Promise<User> {
    // Store token
    localStorage.setItem('auth_token', token)

    // Fetch user data
    const user = await this.getCurrentUser()
    localStorage.setItem('user', JSON.stringify(user))

    return user
  },

  async register(userData: RegisterData): Promise<AuthResponse> {
    const { data } = await api.post<AuthResponse>('/register', userData)
    if (data.token) {
      localStorage.setItem('auth_token', data.token)
      localStorage.setItem('user', JSON.stringify(data.user))
    }
    return data
  },

  async logout(isSsoUser: boolean = false): Promise<void> {
    try {
      if (isSsoUser) {
        // For SSO users, call SSO logout endpoint
        const { data } = await api.post<{ shibboleth_logout_url: string; redirect_url: string }>('/auth/sso/logout')

        // Clear local storage first
        localStorage.removeItem('auth_token')
        localStorage.removeItem('user')
        sessionStorage.removeItem('sso_return_url')

        // Redirect to Shibboleth logout (which will also logout from IdP)
        if (data.shibboleth_logout_url) {
          window.location.href = data.shibboleth_logout_url
          return
        }
      } else {
        // For local users, call regular logout
        await api.post('/logout')
      }
    } finally {
      localStorage.removeItem('auth_token')
      localStorage.removeItem('user')
      sessionStorage.removeItem('sso_return_url')
    }
  },

  async getCurrentUser(): Promise<User> {
    const { data } = await api.get<{ user: User }>('/me')
    return data.user
  },

  isAuthenticated(): boolean {
    return !!localStorage.getItem('auth_token')
  },

  getStoredUser(): User | null {
    const userStr = localStorage.getItem('user')
    if (!userStr) return null
    try {
      return JSON.parse(userStr)
    } catch {
      return null
    }
  },

  getSsoReturnUrl(): string {
    return sessionStorage.getItem('sso_return_url') || '/dashboard'
  },

  clearSsoReturnUrl(): void {
    sessionStorage.removeItem('sso_return_url')
  },
}
