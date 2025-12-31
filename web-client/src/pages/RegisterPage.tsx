import { useState } from 'react'
import { useNavigate, Link } from 'react-router-dom'
import { useAuth } from '../contexts/AuthContext'
import { Button } from '../components/ui/Button'
import { Input } from '../components/ui/Input'
import { Label } from '../components/ui/Label'
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '../components/ui/Card'

export function RegisterPage() {
  const [formData, setFormData] = useState({
    nip: '',
    name: '',
    email: '',
    password: '',
    password_confirmation: '',
    position: '',
    unit_kerja: '',
  })
  const [error, setError] = useState('')
  const [loading, setLoading] = useState(false)
  const { register } = useAuth()
  const navigate = useNavigate()

  const handleChange = (e: React.ChangeEvent<HTMLInputElement>) => {
    setFormData(prev => ({
      ...prev,
      [e.target.name]: e.target.value
    }))
  }

  const handleSubmit = async (e: React.FormEvent) => {
    e.preventDefault()
    setError('')

    if (formData.password !== formData.password_confirmation) {
      setError('Password tidak cocok')
      return
    }

    setLoading(true)
    try {
      await register(formData)
      navigate('/dashboard')
    } catch (err: any) {
      setError(err.response?.data?.message || 'Registrasi gagal. Silakan coba lagi.')
    } finally {
      setLoading(false)
    }
  }

  return (
    <div className="min-h-screen flex items-center justify-center bg-gradient-to-br from-blue-50 to-indigo-100 p-4">
      <Card className="w-full max-w-2xl">
        <CardHeader className="space-y-1">
          <CardTitle className="text-2xl font-bold text-center">
            Daftar Akun Baru
          </CardTitle>
          <CardDescription className="text-center">
            Sistem Manajemen Angka Kredit Pranata TI
          </CardDescription>
        </CardHeader>
        <CardContent>
          <form onSubmit={handleSubmit} className="space-y-4">
            {error && (
              <div className="p-3 text-sm text-red-500 bg-red-50 border border-red-200 rounded-md">
                {error}
              </div>
            )}

            <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
              <div className="space-y-2">
                <Label htmlFor="nip">NIP</Label>
                <Input
                  id="nip"
                  name="nip"
                  placeholder="199001012020011001"
                  value={formData.nip}
                  onChange={handleChange}
                  required
                  disabled={loading}
                />
              </div>

              <div className="space-y-2">
                <Label htmlFor="name">Nama Lengkap</Label>
                <Input
                  id="name"
                  name="name"
                  placeholder="John Doe"
                  value={formData.name}
                  onChange={handleChange}
                  required
                  disabled={loading}
                />
              </div>

              <div className="space-y-2">
                <Label htmlFor="email">Email</Label>
                <Input
                  id="email"
                  name="email"
                  type="email"
                  placeholder="john@example.com"
                  value={formData.email}
                  onChange={handleChange}
                  required
                  disabled={loading}
                />
              </div>

              <div className="space-y-2">
                <Label htmlFor="position">Jabatan</Label>
                <Input
                  id="position"
                  name="position"
                  placeholder="Pranata TI Ahli Muda"
                  value={formData.position}
                  onChange={handleChange}
                  required
                  disabled={loading}
                />
              </div>

              <div className="space-y-2 md:col-span-2">
                <Label htmlFor="unit_kerja">Unit Kerja</Label>
                <Input
                  id="unit_kerja"
                  name="unit_kerja"
                  placeholder="Bidang Aplikasi"
                  value={formData.unit_kerja}
                  onChange={handleChange}
                  required
                  disabled={loading}
                />
              </div>

              <div className="space-y-2">
                <Label htmlFor="password">Password</Label>
                <Input
                  id="password"
                  name="password"
                  type="password"
                  placeholder="••••••••"
                  value={formData.password}
                  onChange={handleChange}
                  required
                  disabled={loading}
                />
              </div>

              <div className="space-y-2">
                <Label htmlFor="password_confirmation">Konfirmasi Password</Label>
                <Input
                  id="password_confirmation"
                  name="password_confirmation"
                  type="password"
                  placeholder="••••••••"
                  value={formData.password_confirmation}
                  onChange={handleChange}
                  required
                  disabled={loading}
                />
              </div>
            </div>

            <Button type="submit" className="w-full" disabled={loading}>
              {loading ? 'Loading...' : 'Daftar'}
            </Button>

            <div className="text-center text-sm text-gray-600">
              Sudah punya akun?{' '}
              <Link to="/login" className="text-primary hover:underline font-medium">
                Login di sini
              </Link>
            </div>
          </form>
        </CardContent>
      </Card>
    </div>
  )
}
