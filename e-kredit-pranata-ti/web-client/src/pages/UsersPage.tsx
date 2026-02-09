import { useState, useEffect } from 'react'
import { Layout } from '../components/Layout'
import { Card, CardContent, CardHeader, CardTitle } from '../components/ui/Card'
import { Button } from '../components/ui/Button'
import { Input } from '../components/ui/Input'
import { Label } from '../components/ui/Label'
import api from '../lib/api'
import { Users, UserPlus, Edit, Trash2, Search, X } from 'lucide-react'

interface User {
  id: number
  name: string
  email: string
  nip: string
  role: string
  jenjang_jabatan: string
  golongan: string
  unit_kerja: string
  position: string
  current_credit_total: string
  target_angka_kredit: string
  created_at: string
}

const ROLES = [
  { value: 'user', label: 'User' },
  { value: 'verifier', label: 'Verifier' },
  { value: 'admin', label: 'Admin' },
]

const JENJANG_JABATAN = [
  'Pranata TI Pertama',
  'Pranata TI Muda',
  'Pranata TI Madya',
  'Pranata TI Utama',
]

export function UsersPage() {
  const [users, setUsers] = useState<User[]>([])
  const [loading, setLoading] = useState(true)
  const [search, setSearch] = useState('')
  const [showModal, setShowModal] = useState(false)
  const [editingUser, setEditingUser] = useState<User | null>(null)
  const [formData, setFormData] = useState({
    name: '',
    email: '',
    nip: '',
    password: '',
    role: 'user',
    jenjang_jabatan: 'Pranata TI Pertama',
    golongan: '',
    unit_kerja: '',
    position: '',
  })
  const [formLoading, setFormLoading] = useState(false)
  const [error, setError] = useState('')

  useEffect(() => {
    loadUsers()
  }, [])

  const loadUsers = async () => {
    setLoading(true)
    try {
      const { data } = await api.get('/admin/users')
      setUsers(Array.isArray(data) ? data : data.data || [])
    } catch (error) {
      console.error('Failed to load users:', error)
    } finally {
      setLoading(false)
    }
  }

  const filteredUsers = users.filter(user =>
    user.name.toLowerCase().includes(search.toLowerCase()) ||
    user.email.toLowerCase().includes(search.toLowerCase()) ||
    user.nip?.toLowerCase().includes(search.toLowerCase())
  )

  const openCreateModal = () => {
    setEditingUser(null)
    setFormData({
      name: '',
      email: '',
      nip: '',
      password: '',
      role: 'user',
      jenjang_jabatan: 'Pranata TI Pertama',
      golongan: '',
      unit_kerja: '',
      position: '',
    })
    setError('')
    setShowModal(true)
  }

  const openEditModal = (user: User) => {
    setEditingUser(user)
    setFormData({
      name: user.name,
      email: user.email,
      nip: user.nip || '',
      password: '',
      role: user.role,
      jenjang_jabatan: user.jenjang_jabatan || 'Pranata TI Pertama',
      golongan: user.golongan || '',
      unit_kerja: user.unit_kerja || '',
      position: user.position || '',
    })
    setError('')
    setShowModal(true)
  }

  const handleSubmit = async (e: React.FormEvent) => {
    e.preventDefault()
    setFormLoading(true)
    setError('')

    try {
      const payload = { ...formData }
      if (!payload.password) {
        delete (payload as any).password
      }

      if (editingUser) {
        await api.put(`/admin/users/${editingUser.id}`, payload)
      } else {
        await api.post('/admin/users', payload)
      }
      loadUsers()
      setShowModal(false)
    } catch (err: any) {
      setError(err.response?.data?.message || 'Gagal menyimpan data')
    } finally {
      setFormLoading(false)
    }
  }

  const handleDelete = async (user: User) => {
    if (!confirm(`Yakin ingin menghapus user "${user.name}"?`)) return

    try {
      await api.delete(`/admin/users/${user.id}`)
      loadUsers()
    } catch (error) {
      console.error('Failed to delete user:', error)
      alert('Gagal menghapus user')
    }
  }

  const getRoleBadge = (role: string) => {
    switch (role) {
      case 'admin':
        return <span className="px-2 py-1 rounded-full text-xs font-medium bg-red-100 text-red-800">Admin</span>
      case 'verifier':
        return <span className="px-2 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">Verifier</span>
      default:
        return <span className="px-2 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-800">User</span>
    }
  }

  return (
    <Layout>
      <div className="space-y-6">
        {/* Header */}
        <div className="flex justify-between items-center">
          <div>
            <h1 className="text-3xl font-bold text-gray-900">Kelola User</h1>
            <p className="text-gray-500 mt-1">Manajemen pengguna sistem</p>
          </div>
          <Button onClick={openCreateModal}>
            <UserPlus className="h-4 w-4 mr-2" />
            Tambah User
          </Button>
        </div>

        {/* Search */}
        <div className="relative max-w-md">
          <Search className="absolute left-3 top-1/2 transform -translate-y-1/2 h-4 w-4 text-gray-400" />
          <Input
            placeholder="Cari nama, email, atau NIP..."
            value={search}
            onChange={(e) => setSearch(e.target.value)}
            className="pl-10"
          />
        </div>

        {/* Users Table */}
        <Card>
          <CardHeader>
            <CardTitle className="flex items-center gap-2">
              <Users className="h-5 w-5" />
              Daftar User ({filteredUsers.length})
            </CardTitle>
          </CardHeader>
          <CardContent>
            {loading ? (
              <div className="text-center py-8 text-gray-500">Memuat data...</div>
            ) : filteredUsers.length === 0 ? (
              <div className="text-center py-8 text-gray-500">
                <Users className="h-12 w-12 mx-auto mb-3 text-gray-300" />
                <p>Tidak ada user ditemukan</p>
              </div>
            ) : (
              <div className="overflow-x-auto">
                <table className="w-full">
                  <thead>
                    <tr className="border-b text-left text-sm text-gray-500">
                      <th className="pb-3 font-medium">Nama</th>
                      <th className="pb-3 font-medium">NIP</th>
                      <th className="pb-3 font-medium">Role</th>
                      <th className="pb-3 font-medium">Jenjang</th>
                      <th className="pb-3 font-medium">Unit Kerja</th>
                      <th className="pb-3 font-medium">Kredit</th>
                      <th className="pb-3 font-medium">Aksi</th>
                    </tr>
                  </thead>
                  <tbody className="divide-y">
                    {filteredUsers.map((user) => (
                      <tr key={user.id} className="text-sm">
                        <td className="py-3">
                          <p className="font-medium">{user.name}</p>
                          <p className="text-xs text-gray-500">{user.email}</p>
                        </td>
                        <td className="py-3 text-gray-600">{user.nip || '-'}</td>
                        <td className="py-3">{getRoleBadge(user.role)}</td>
                        <td className="py-3">
                          <p className="text-sm">{user.jenjang_jabatan || '-'}</p>
                          <p className="text-xs text-gray-500">{user.golongan || '-'}</p>
                        </td>
                        <td className="py-3 text-gray-600">{user.unit_kerja || '-'}</td>
                        <td className="py-3">
                          <p className="font-medium text-blue-600">{user.current_credit_total || '0'}</p>
                          <p className="text-xs text-gray-500">/ {user.target_angka_kredit || '0'}</p>
                        </td>
                        <td className="py-3">
                          <div className="flex gap-1">
                            <Button
                              size="sm"
                              variant="ghost"
                              onClick={() => openEditModal(user)}
                            >
                              <Edit className="h-4 w-4" />
                            </Button>
                            <Button
                              size="sm"
                              variant="ghost"
                              className="text-red-600 hover:text-red-700"
                              onClick={() => handleDelete(user)}
                            >
                              <Trash2 className="h-4 w-4" />
                            </Button>
                          </div>
                        </td>
                      </tr>
                    ))}
                  </tbody>
                </table>
              </div>
            )}
          </CardContent>
        </Card>

        {/* Create/Edit Modal */}
        {showModal && (
          <div className="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4">
            <div className="bg-white rounded-lg max-w-lg w-full max-h-[90vh] overflow-y-auto">
              <div className="p-6">
                <div className="flex justify-between items-start mb-4">
                  <h2 className="text-xl font-bold">
                    {editingUser ? 'Edit User' : 'Tambah User Baru'}
                  </h2>
                  <button onClick={() => setShowModal(false)} className="text-gray-500 hover:text-gray-700">
                    <X className="h-6 w-6" />
                  </button>
                </div>

                <form onSubmit={handleSubmit} className="space-y-4">
                  {error && (
                    <div className="p-3 text-sm text-red-500 bg-red-50 border border-red-200 rounded-md">
                      {error}
                    </div>
                  )}

                  <div className="grid grid-cols-2 gap-4">
                    <div className="col-span-2">
                      <Label htmlFor="name">Nama Lengkap *</Label>
                      <Input
                        id="name"
                        value={formData.name}
                        onChange={(e) => setFormData({ ...formData, name: e.target.value })}
                        required
                      />
                    </div>

                    <div>
                      <Label htmlFor="email">Email *</Label>
                      <Input
                        id="email"
                        type="email"
                        value={formData.email}
                        onChange={(e) => setFormData({ ...formData, email: e.target.value })}
                        required
                      />
                    </div>

                    <div>
                      <Label htmlFor="nip">NIP</Label>
                      <Input
                        id="nip"
                        value={formData.nip}
                        onChange={(e) => setFormData({ ...formData, nip: e.target.value })}
                      />
                    </div>

                    <div className="col-span-2">
                      <Label htmlFor="password">
                        Password {editingUser ? '(kosongkan jika tidak diubah)' : '*'}
                      </Label>
                      <Input
                        id="password"
                        type="password"
                        value={formData.password}
                        onChange={(e) => setFormData({ ...formData, password: e.target.value })}
                        required={!editingUser}
                      />
                    </div>

                    <div>
                      <Label htmlFor="role">Role *</Label>
                      <select
                        id="role"
                        value={formData.role}
                        onChange={(e) => setFormData({ ...formData, role: e.target.value })}
                        className="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm"
                        required
                      >
                        {ROLES.map((role) => (
                          <option key={role.value} value={role.value}>
                            {role.label}
                          </option>
                        ))}
                      </select>
                    </div>

                    <div>
                      <Label htmlFor="jenjang_jabatan">Jenjang Jabatan</Label>
                      <select
                        id="jenjang_jabatan"
                        value={formData.jenjang_jabatan}
                        onChange={(e) => setFormData({ ...formData, jenjang_jabatan: e.target.value })}
                        className="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm"
                      >
                        {JENJANG_JABATAN.map((jenjang) => (
                          <option key={jenjang} value={jenjang}>
                            {jenjang}
                          </option>
                        ))}
                      </select>
                    </div>

                    <div>
                      <Label htmlFor="golongan">Golongan</Label>
                      <Input
                        id="golongan"
                        value={formData.golongan}
                        onChange={(e) => setFormData({ ...formData, golongan: e.target.value })}
                        placeholder="III/a"
                      />
                    </div>

                    <div>
                      <Label htmlFor="position">Jabatan</Label>
                      <Input
                        id="position"
                        value={formData.position}
                        onChange={(e) => setFormData({ ...formData, position: e.target.value })}
                      />
                    </div>

                    <div className="col-span-2">
                      <Label htmlFor="unit_kerja">Unit Kerja</Label>
                      <Input
                        id="unit_kerja"
                        value={formData.unit_kerja}
                        onChange={(e) => setFormData({ ...formData, unit_kerja: e.target.value })}
                      />
                    </div>
                  </div>

                  <div className="flex gap-3 pt-4">
                    <Button
                      type="button"
                      variant="outline"
                      onClick={() => setShowModal(false)}
                      className="flex-1"
                    >
                      Batal
                    </Button>
                    <Button type="submit" className="flex-1" disabled={formLoading}>
                      {formLoading ? 'Menyimpan...' : 'Simpan'}
                    </Button>
                  </div>
                </form>
              </div>
            </div>
          </div>
        )}
      </div>
    </Layout>
  )
}
