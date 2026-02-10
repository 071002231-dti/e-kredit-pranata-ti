import { useState, useEffect } from 'react'
import { Layout } from '../components/Layout'
import { Card, CardContent, CardHeader, CardTitle } from '../components/ui/Card'
import { Button } from '../components/ui/Button'
import { Input } from '../components/ui/Input'
import { Label } from '../components/ui/Label'
import api from '../lib/api'
import { ClipboardList, Plus, Edit, Trash2, Search, X, Filter } from 'lucide-react'

interface CreditSchema {
  id: number
  category: string
  subcategory: string
  subcategory_name?: string
  activity_name: string
  credit_points: string
  satuan_hasil: string
  batasan_penilaian: string
  pelaksana: string
  bukti_fisik: string
  unsur_type: string
  description: string
}

const UNSUR_TYPES = [
  { value: 'utama', label: 'Unsur Utama' },
  { value: 'penunjang', label: 'Unsur Penunjang' },
]

const PELAKSANA_OPTIONS = [
  'Semua Jenjang',
  'PTI Pertama',
  'PTI Muda',
  'PTI Madya',
  'PTI Utama',
]

export function SchemasPage() {
  const [schemas, setSchemas] = useState<CreditSchema[]>([])
  const [loading, setLoading] = useState(true)
  const [search, setSearch] = useState('')
  const [categoryFilter, setCategoryFilter] = useState('')
  const [categories, setCategories] = useState<string[]>([])
  const [showModal, setShowModal] = useState(false)
  const [editingSchema, setEditingSchema] = useState<CreditSchema | null>(null)
  const [formData, setFormData] = useState({
    category: '',
    subcategory: '',
    activity_name: '',
    credit_points: '',
    satuan_hasil: '',
    batasan_penilaian: 'tidak terbatas',
    pelaksana: 'Semua Jenjang',
    bukti_fisik: '',
    unsur_type: 'utama',
    description: '',
  })
  const [formLoading, setFormLoading] = useState(false)
  const [error, setError] = useState('')

  useEffect(() => {
    loadSchemas()
    loadCategories()
  }, [])

  const loadSchemas = async () => {
    setLoading(true)
    try {
      const { data } = await api.get('/credit-schema', { params: { paginate: 'false' } })
      setSchemas(Array.isArray(data) ? data : data.data || [])
    } catch (error) {
      console.error('Failed to load schemas:', error)
    } finally {
      setLoading(false)
    }
  }

  const loadCategories = async () => {
    try {
      const { data } = await api.get('/credit-schema/categories')
      setCategories(Array.isArray(data) ? data : [])
    } catch (error) {
      console.error('Failed to load categories:', error)
    }
  }

  const filteredSchemas = schemas.filter(schema => {
    const matchSearch =
      schema.activity_name.toLowerCase().includes(search.toLowerCase()) ||
      schema.category.toLowerCase().includes(search.toLowerCase()) ||
      schema.subcategory.toLowerCase().includes(search.toLowerCase())
    const matchCategory = !categoryFilter || schema.category === categoryFilter
    return matchSearch && matchCategory
  })

  const openCreateModal = () => {
    setEditingSchema(null)
    setFormData({
      category: '',
      subcategory: '',
      activity_name: '',
      credit_points: '',
      satuan_hasil: '',
      batasan_penilaian: 'tidak terbatas',
      pelaksana: 'Semua Jenjang',
      bukti_fisik: '',
      unsur_type: 'utama',
      description: '',
    })
    setError('')
    setShowModal(true)
  }

  const openEditModal = (schema: CreditSchema) => {
    setEditingSchema(schema)
    setFormData({
      category: schema.category,
      subcategory: schema.subcategory,
      activity_name: schema.activity_name,
      credit_points: schema.credit_points,
      satuan_hasil: schema.satuan_hasil || '',
      batasan_penilaian: schema.batasan_penilaian || 'tidak terbatas',
      pelaksana: schema.pelaksana || 'Semua Jenjang',
      bukti_fisik: schema.bukti_fisik || '',
      unsur_type: schema.unsur_type || 'utama',
      description: schema.description || '',
    })
    setError('')
    setShowModal(true)
  }

  const handleSubmit = async (e: React.FormEvent) => {
    e.preventDefault()
    setFormLoading(true)
    setError('')

    try {
      if (editingSchema) {
        await api.put(`/admin/schemas/${editingSchema.id}`, formData)
      } else {
        await api.post('/admin/schemas', formData)
      }
      loadSchemas()
      loadCategories()
      setShowModal(false)
    } catch (err: any) {
      setError(err.response?.data?.message || 'Gagal menyimpan data')
    } finally {
      setFormLoading(false)
    }
  }

  const handleDelete = async (schema: CreditSchema) => {
    if (!confirm(`Yakin ingin menghapus skema "${schema.activity_name}"?`)) return

    try {
      await api.delete(`/admin/schemas/${schema.id}`)
      loadSchemas()
    } catch (error) {
      console.error('Failed to delete schema:', error)
      alert('Gagal menghapus skema')
    }
  }

  return (
    <Layout>
      <div className="space-y-6">
        {/* Header */}
        <div className="flex justify-between items-center">
          <div>
            <h1 className="text-3xl font-bold text-gray-900">Kelola Skema Penilaian</h1>
            <p className="text-gray-500 mt-1">Kelola komponen penilaian angka kredit sesuai peraturan</p>
          </div>
          <Button onClick={openCreateModal}>
            <Plus className="h-4 w-4 mr-2" />
            Tambah Skema
          </Button>
        </div>

        {/* Filters */}
        <div className="flex flex-wrap gap-4">
          <div className="relative flex-1 min-w-[200px] max-w-md">
            <Search className="absolute left-3 top-1/2 transform -translate-y-1/2 h-4 w-4 text-gray-400" />
            <Input
              placeholder="Cari aktivitas..."
              value={search}
              onChange={(e) => setSearch(e.target.value)}
              className="pl-10"
            />
          </div>
          <div className="flex items-center gap-2">
            <Filter className="h-4 w-4 text-gray-400" />
            <select
              value={categoryFilter}
              onChange={(e) => setCategoryFilter(e.target.value)}
              className="h-10 rounded-md border border-input bg-background px-3 py-2 text-sm"
            >
              <option value="">Semua Kategori</option>
              {categories.map((cat) => (
                <option key={cat} value={cat}>{cat}</option>
              ))}
            </select>
          </div>
        </div>

        {/* Stats */}
        <div className="grid grid-cols-2 md:grid-cols-4 gap-4">
          <Card>
            <CardContent className="p-4">
              <p className="text-2xl font-bold text-blue-600">{schemas.length}</p>
              <p className="text-sm text-gray-500">Total Skema</p>
            </CardContent>
          </Card>
          <Card>
            <CardContent className="p-4">
              <p className="text-2xl font-bold text-green-600">
                {schemas.filter(s => s.unsur_type === 'utama').length}
              </p>
              <p className="text-sm text-gray-500">Unsur Utama</p>
            </CardContent>
          </Card>
          <Card>
            <CardContent className="p-4">
              <p className="text-2xl font-bold text-purple-600">
                {schemas.filter(s => s.unsur_type === 'penunjang').length}
              </p>
              <p className="text-sm text-gray-500">Unsur Penunjang</p>
            </CardContent>
          </Card>
          <Card>
            <CardContent className="p-4">
              <p className="text-2xl font-bold text-orange-600">{categories.length}</p>
              <p className="text-sm text-gray-500">Kategori</p>
            </CardContent>
          </Card>
        </div>

        {/* Schemas Table */}
        <Card>
          <CardHeader>
            <CardTitle className="flex items-center gap-2">
              <ClipboardList className="h-5 w-5" />
              Daftar Skema Penilaian ({filteredSchemas.length})
            </CardTitle>
          </CardHeader>
          <CardContent>
            {loading ? (
              <div className="text-center py-8 text-gray-500">Memuat data...</div>
            ) : filteredSchemas.length === 0 ? (
              <div className="text-center py-8 text-gray-500">
                <ClipboardList className="h-12 w-12 mx-auto mb-3 text-gray-300" />
                <p>Tidak ada skema ditemukan</p>
              </div>
            ) : (
              <div className="overflow-x-auto">
                <table className="w-full">
                  <thead>
                    <tr className="border-b text-left text-sm text-gray-500">
                      <th className="pb-3 font-medium">Aktivitas</th>
                      <th className="pb-3 font-medium">Kategori</th>
                      <th className="pb-3 font-medium">Kredit</th>
                      <th className="pb-3 font-medium">Unsur</th>
                      <th className="pb-3 font-medium">Pelaksana</th>
                      <th className="pb-3 font-medium">Aksi</th>
                    </tr>
                  </thead>
                  <tbody className="divide-y">
                    {filteredSchemas.map((schema) => (
                      <tr key={schema.id} className="text-sm">
                        <td className="py-3">
                          <p className="font-medium">{schema.activity_name}</p>
                          <p className="text-xs text-gray-500">{schema.satuan_hasil}</p>
                        </td>
                        <td className="py-3">
                          <p className="text-sm">{schema.category}</p>
                          <p className="text-xs text-gray-500">{schema.subcategory_name || schema.subcategory}</p>
                        </td>
                        <td className="py-3">
                          <span className="font-medium text-blue-600">{schema.credit_points}</span>
                        </td>
                        <td className="py-3">
                          <span className={`px-2 py-1 rounded-full text-xs font-medium ${
                            schema.unsur_type === 'utama'
                              ? 'bg-blue-100 text-blue-800'
                              : 'bg-green-100 text-green-800'
                          }`}>
                            {schema.unsur_type === 'utama' ? 'Utama' : 'Penunjang'}
                          </span>
                        </td>
                        <td className="py-3 text-gray-600 text-sm">{schema.pelaksana}</td>
                        <td className="py-3">
                          <div className="flex gap-1">
                            <Button
                              size="sm"
                              variant="ghost"
                              onClick={() => openEditModal(schema)}
                            >
                              <Edit className="h-4 w-4" />
                            </Button>
                            <Button
                              size="sm"
                              variant="ghost"
                              className="text-red-600 hover:text-red-700"
                              onClick={() => handleDelete(schema)}
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
            <div className="bg-white rounded-lg max-w-2xl w-full max-h-[90vh] overflow-y-auto">
              <div className="p-6">
                <div className="flex justify-between items-start mb-4">
                  <h2 className="text-xl font-bold">
                    {editingSchema ? 'Edit Skema Penilaian' : 'Tambah Skema Penilaian Baru'}
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
                    <div>
                      <Label htmlFor="category">Kategori *</Label>
                      <Input
                        id="category"
                        value={formData.category}
                        onChange={(e) => setFormData({ ...formData, category: e.target.value })}
                        placeholder="Contoh: Implementasi Sistem Informasi"
                        required
                        list="category-list"
                      />
                      <datalist id="category-list">
                        {categories.map((cat) => (
                          <option key={cat} value={cat} />
                        ))}
                      </datalist>
                    </div>

                    <div>
                      <Label htmlFor="subcategory">Sub Kategori *</Label>
                      <Input
                        id="subcategory"
                        value={formData.subcategory}
                        onChange={(e) => setFormData({ ...formData, subcategory: e.target.value })}
                        placeholder="Contoh: Implementasi Sistem Komputer"
                        required
                      />
                    </div>

                    <div className="col-span-2">
                      <Label htmlFor="activity_name">Nama Aktivitas *</Label>
                      <Input
                        id="activity_name"
                        value={formData.activity_name}
                        onChange={(e) => setFormData({ ...formData, activity_name: e.target.value })}
                        placeholder="Contoh: Melakukan instalasi sistem operasi"
                        required
                      />
                    </div>

                    <div>
                      <Label htmlFor="credit_points">Angka Kredit *</Label>
                      <Input
                        id="credit_points"
                        type="number"
                        step="0.001"
                        value={formData.credit_points}
                        onChange={(e) => setFormData({ ...formData, credit_points: e.target.value })}
                        placeholder="0.500"
                        required
                      />
                    </div>

                    <div>
                      <Label htmlFor="satuan_hasil">Satuan Hasil *</Label>
                      <Input
                        id="satuan_hasil"
                        value={formData.satuan_hasil}
                        onChange={(e) => setFormData({ ...formData, satuan_hasil: e.target.value })}
                        placeholder="Contoh: Laporan, Sistem, Kegiatan"
                        required
                      />
                    </div>

                    <div>
                      <Label htmlFor="unsur_type">Jenis Unsur *</Label>
                      <select
                        id="unsur_type"
                        value={formData.unsur_type}
                        onChange={(e) => setFormData({ ...formData, unsur_type: e.target.value })}
                        className="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm"
                        required
                      >
                        {UNSUR_TYPES.map((type) => (
                          <option key={type.value} value={type.value}>
                            {type.label}
                          </option>
                        ))}
                      </select>
                    </div>

                    <div>
                      <Label htmlFor="pelaksana">Jenjang Pelaksana</Label>
                      <select
                        id="pelaksana"
                        value={formData.pelaksana}
                        onChange={(e) => setFormData({ ...formData, pelaksana: e.target.value })}
                        className="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm"
                      >
                        {PELAKSANA_OPTIONS.map((opt) => (
                          <option key={opt} value={opt}>{opt}</option>
                        ))}
                      </select>
                    </div>

                    <div>
                      <Label htmlFor="batasan_penilaian">Batasan Penilaian</Label>
                      <Input
                        id="batasan_penilaian"
                        value={formData.batasan_penilaian}
                        onChange={(e) => setFormData({ ...formData, batasan_penilaian: e.target.value })}
                        placeholder="Contoh: maksimal 2 kali/tahun"
                      />
                    </div>

                    <div>
                      <Label htmlFor="bukti_fisik">Bukti Fisik</Label>
                      <Input
                        id="bukti_fisik"
                        value={formData.bukti_fisik}
                        onChange={(e) => setFormData({ ...formData, bukti_fisik: e.target.value })}
                        placeholder="Contoh: Laporan, Surat Tugas"
                      />
                    </div>

                    <div className="col-span-2">
                      <Label htmlFor="description">Deskripsi</Label>
                      <textarea
                        id="description"
                        value={formData.description}
                        onChange={(e) => setFormData({ ...formData, description: e.target.value })}
                        placeholder="Deskripsi aktivitas..."
                        className="flex min-h-[80px] w-full rounded-md border border-input bg-background px-3 py-2 text-sm"
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
