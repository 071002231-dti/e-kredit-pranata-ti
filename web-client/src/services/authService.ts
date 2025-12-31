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

  async register(userData: RegisterData): Promise<AuthResponse> {
    const { data } = await api.post<AuthResponse>('/register', userData)
    if (data.token) {
      localStorage.setItem('auth_token', data.token)
      localStorage.setItem('user', JSON.stringify(data.user))
    }
    return data
  },

  async logout(): Promise<void> {
    try {
      await api.post('/logout')
    } finally {
      localStorage.removeItem('auth_token')
      localStorage.removeItem('user')
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
}
