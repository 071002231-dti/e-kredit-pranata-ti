import { useState } from 'react'
import { useNavigate } from 'react-router-dom'
import { useAuth } from '../contexts/AuthContext'
import { Button } from '../components/ui/Button'
import { Input } from '../components/ui/Input'
import { Label } from '../components/ui/Label'
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '../components/ui/Card'

export function LoginPage() {
  const [email, setEmail] = useState('')
  const [password, setPassword] = useState('')
  const [error, setError] = useState('')
  const [loading, setLoading] = useState(false)
  const [ssoLoading, setSsoLoading] = useState(false)
  const { login, loginWithSSO } = useAuth()
  const navigate = useNavigate()

  const handleSsoLogin = async () => {
    setSsoLoading(true)
    setError('')

    try {
      await loginWithSSO()
    } catch (err: any) {
      setError(err.response?.data?.message || 'SSO login failed. Please try again.')
      setSsoLoading(false)
    }
  }

  const handleSubmit = async (e: React.FormEvent) => {
    e.preventDefault()
    setError('')
    setLoading(true)

    try {
      await login({ email, password })
      navigate('/dashboard')
    } catch (err: any) {
      setError(err.response?.data?.message || 'Login failed. Please try again.')
    } finally {
      setLoading(false)
    }
  }

  return (
    <div className="min-h-screen flex items-center justify-center bg-gradient-to-br from-blue-50 to-indigo-100 p-4">
      <Card className="w-full max-w-md">
        <CardHeader className="space-y-1">
          <CardTitle className="text-2xl font-bold text-center">
            e-Kredit Pranata TI
          </CardTitle>
          <CardDescription className="text-center">
            Sistem Manajemen Angka Kredit Pranata TI
          </CardDescription>
        </CardHeader>
        <CardContent>
          <div className="space-y-4">
            {error && (
              <div className="p-3 text-sm text-red-500 bg-red-50 border border-red-200 rounded-md">
                {error}
              </div>
            )}

            {/* SSO Login Button (Primary) */}
            <Button
              type="button"
              className="w-full bg-blue-600 hover:bg-blue-700 text-white"
              onClick={handleSsoLogin}
              disabled={ssoLoading || loading}
            >
              {ssoLoading ? 'Redirecting to SSO...' : 'Login with UII SSO'}
            </Button>

            {/* Divider */}
            <div className="relative">
              <div className="absolute inset-0 flex items-center">
                <span className="w-full border-t" />
              </div>
              <div className="relative flex justify-center text-xs uppercase">
                <span className="bg-white px-2 text-gray-500">Or continue with email</span>
              </div>
            </div>

            {/* Local Login Form (Fallback) */}
            <form onSubmit={handleSubmit} className="space-y-4">
              <div className="space-y-2">
                <Label htmlFor="email">Email</Label>
                <Input
                  id="email"
                  type="email"
                  placeholder="Email"
                  value={email}
                  onChange={(e) => setEmail(e.target.value)}
                  required
                  disabled={loading || ssoLoading}
                />
              </div>

              <div className="space-y-2">
                <Label htmlFor="password">Password</Label>
                <Input
                  id="password"
                  type="password"
                  placeholder="••••••••"
                  value={password}
                  onChange={(e) => setPassword(e.target.value)}
                  required
                  disabled={loading || ssoLoading}
                />
              </div>

              <Button
                type="submit"
                variant="outline"
                className="w-full"
                disabled={loading || ssoLoading}
              >
                {loading ? 'Loading...' : 'Login with Email'}
              </Button>
            </form>
          </div>
        </CardContent>
      </Card>
    </div>
  )
}
