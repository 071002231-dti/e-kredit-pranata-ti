import { createContext, useContext, useState, useEffect, type ReactNode } from 'react'
import { authService } from '../services/authService'
import type { User, LoginCredentials, RegisterData } from '../types'

interface AuthContextType {
  user: User | null
  loading: boolean
  authMethod: 'local' | 'sso' | null
  login: (credentials: LoginCredentials) => Promise<void>
  loginWithSSO: () => Promise<void>
  register: (data: RegisterData) => Promise<void>
  logout: () => Promise<void>
  isAuthenticated: boolean
}

const AuthContext = createContext<AuthContextType | undefined>(undefined)

export function AuthProvider({ children }: { children: ReactNode }) {
  const [user, setUser] = useState<User | null>(null)
  const [loading, setLoading] = useState(true)
  const [authMethod, setAuthMethod] = useState<'local' | 'sso' | null>(null)

  useEffect(() => {
    // Check if user is already logged in
    const initAuth = async () => {
      if (authService.isAuthenticated()) {
        try {
          const currentUser = await authService.getCurrentUser()
          setUser(currentUser)
          setAuthMethod(currentUser.auth_method === 'hybrid' ? 'local' : currentUser.auth_method)
        } catch (error) {
          // Token invalid, clear storage
          authService.logout()
        }
      }
      setLoading(false)
    }

    initAuth()
  }, [])

  const login = async (credentials: LoginCredentials) => {
    const response = await authService.login(credentials)
    setUser(response.user)
    setAuthMethod('local')
  }

  const loginWithSSO = async () => {
    // This will redirect to SSO login endpoint
    await authService.loginWithSSO()
    // Note: User will be redirected to IdP, callback will handle setting user state
  }

  const register = async (data: RegisterData) => {
    const response = await authService.register(data)
    setUser(response.user)
    setAuthMethod('local')
  }

  const logout = async () => {
    const isSsoUser = authMethod === 'sso' || user?.auth_method === 'sso'
    await authService.logout(isSsoUser)
    setUser(null)
    setAuthMethod(null)
  }

  const value: AuthContextType = {
    user,
    loading,
    authMethod,
    login,
    loginWithSSO,
    register,
    logout,
    isAuthenticated: !!user,
  }

  return <AuthContext.Provider value={value}>{children}</AuthContext.Provider>
}

export function useAuth() {
  const context = useContext(AuthContext)
  if (context === undefined) {
    throw new Error('useAuth must be used within an AuthProvider')
  }
  return context
}
