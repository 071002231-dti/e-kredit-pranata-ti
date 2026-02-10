import { useState, useEffect } from 'react'
import { useNavigate, useParams } from 'react-router-dom'
import Select from 'react-select'
import { Layout } from '../components/Layout'
import { Card, CardContent, CardHeader, CardTitle } from '../components/ui/Card'
import { Button } from '../components/ui/Button'
import { Input } from '../components/ui/Input'
import { Label } from '../components/ui/Label'
import { activityService } from '../services/activityService'
import api from '../lib/api'
import type { CreditSchema } from '../types'
import { ArrowLeft, Upload, AlertTriangle, Info, RotateCcw } from 'lucide-react'

interface SchemaOption {
  value: number
  label: string
  schema: CreditSchema
}

export function ActivityFormPage() {
  const navigate = useNavigate()
  const { id } = useParams<{ id: string }>()
  const isEditMode = !!id
  const [loading, setLoading] = useState(false)
  const [initialLoading, setInitialLoading] = useState(isEditMode)
  const [schemas, setSchemas] = useState<CreditSchema[]>([])
  const [selectedSchema, setSelectedSchema] = useState<CreditSchema | null>(null)
  const [file, setFile] = useState<File | null>(null)
  const [selectedCategory, setSelectedCategory] = useState('')
  const [selectedSubcategory, setSelectedSubcategory] = useState('')
  const [revisionComment, setRevisionComment] = useState('')
  const [formData, setFormData] = useState({
    schema_id: '',
    title: '',
    description: '',
  })
  const [error, setError] = useState('')

  useEffect(() => {
    loadSchemas()
  }, [])

  useEffect(() => {
    if (isEditMode && schemas.length > 0) {
      loadActivity()
    }
  }, [id, schemas])

  const loadActivity = async () => {
    if (!id) return
    setInitialLoading(true)
    try {
      const { data } = await api.get(`/activities/${id}`)
      const activity = data.data || data

      // Set form data
      setFormData({
        schema_id: activity.schema_id?.toString() || '',
        title: activity.title || '',
        description: activity.description || '',
      })

      // Find and set the schema
      if (activity.schema_id) {
        const schema = schemas.find(s => s.id === activity.schema_id)
        if (schema) {
          setSelectedSchema(schema)
          setSelectedCategory(schema.category)
          setSelectedSubcategory(schema.subcategory)
        }
      }

      // Get latest revision comment from approvals
      if (activity.approvals && activity.approvals.length > 0) {
        const latestApproval = activity.approvals[activity.approvals.length - 1]
        if (latestApproval.status === 'revision' && latestApproval.comments) {
          setRevisionComment(latestApproval.comments)
        }
      }
    } catch (err) {
      console.error('Failed to load activity:', err)
      setError('Gagal memuat data aktivitas')
    } finally {
      setInitialLoading(false)
    }
  }

  const loadSchemas = async () => {
    try {
      const { data } = await api.get('/credit-schema', { params: { paginate: 'false' } })
      setSchemas(Array.isArray(data) ? data : data.data || [])
    } catch (error) {
      console.error('Failed to load schemas:', error)
    }
  }

  // Get unique categories
  const categories = Array.from(new Set(schemas.map(s => s.category))).sort()

  // Get subcategories based on selected category
  const subcategories = selectedCategory
    ? Array.from(new Set(schemas.filter(s => s.category === selectedCategory).map(s => s.subcategory))).sort()
    : []

  // Get activities based on selected category and subcategory
  const filteredSchemas = schemas.filter(s => {
    if (!selectedCategory) return false
    if (selectedCategory && !selectedSubcategory) return s.category === selectedCategory
    return s.category === selectedCategory && s.subcategory === selectedSubcategory
  })

  const schemaOptions: SchemaOption[] = filteredSchemas.map(schema => ({
    value: schema.id,
    label: `${schema.activity_name} (${schema.credit_points} poin)`,
    schema: schema
  }))

  const handleCategoryChange = (e: React.ChangeEvent<HTMLSelectElement>) => {
    const category = e.target.value
    setSelectedCategory(category)
    setSelectedSubcategory('')
    setSelectedSchema(null)
    setFormData(prev => ({ ...prev, schema_id: '', title: '' }))
  }

  const handleSubcategoryChange = (e: React.ChangeEvent<HTMLSelectElement>) => {
    const subcategory = e.target.value
    setSelectedSubcategory(subcategory)
    setSelectedSchema(null)
    setFormData(prev => ({ ...prev, schema_id: '', title: '' }))
  }

  const handleSchemaChange = (option: SchemaOption | null) => {
    if (option) {
      setSelectedSchema(option.schema)
      setFormData(prev => ({
        ...prev,
        schema_id: option.value.toString(),
        title: option.schema.activity_name,
      }))
    } else {
      setSelectedSchema(null)
      setFormData(prev => ({
        ...prev,
        schema_id: '',
        title: '',
      }))
    }
  }

  const customStyles = {
    control: (base: any) => ({
      ...base,
      minHeight: '40px',
      borderColor: 'hsl(214.3 31.8% 91.4%)',
      '&:hover': {
        borderColor: 'hsl(221.2 83.2% 53.3%)',
      },
    }),
    menu: (base: any) => ({
      ...base,
      maxHeight: '300px',
    }),
    menuList: (base: any) => ({
      ...base,
      maxHeight: '300px',
    }),
  }

  const handleFileChange = (e: React.ChangeEvent<HTMLInputElement>) => {
    if (e.target.files && e.target.files[0]) {
      setFile(e.target.files[0])
    }
  }

  const handleSubmit = async (e: React.FormEvent) => {
    e.preventDefault()
    setError('')
    setLoading(true)

    try {
      const formDataToSend = new FormData()
      formDataToSend.append('schema_id', formData.schema_id)
      formDataToSend.append('title', formData.title)
      formDataToSend.append('description', formData.description)
      if (file) {
        formDataToSend.append('proof_file', file)
      }

      let response
      if (isEditMode && id) {
        // Update existing activity and resubmit for review
        formDataToSend.append('_method', 'PUT')
        response = await api.post(`/activities/${id}`, formDataToSend, {
          headers: { 'Content-Type': 'multipart/form-data' }
        })
      } else {
        response = await activityService.createActivity(formDataToSend)
      }

      // Check if credits will be banked and show warning
      if (response?.banking_info?.will_be_banked) {
        alert(`⚠️ Perhatian: ${response.banking_info.warning}`)
      }

      navigate('/activities')
    } catch (err: any) {
      setError(err.response?.data?.message || 'Gagal menyimpan aktivitas')
    } finally {
      setLoading(false)
    }
  }

  return (
    <Layout>
      <div className="max-w-3xl mx-auto space-y-6">
        {/* Header */}
        <div className="flex items-center gap-4">
          <Button
            variant="ghost"
            size="icon"
            onClick={() => navigate('/activities')}
          >
            <ArrowLeft className="h-5 w-5" />
          </Button>
          <div>
            <h1 className="text-3xl font-bold text-gray-900">
              {isEditMode ? 'Perbaiki Aktivitas' : 'Tambah Aktivitas'}
            </h1>
            <p className="text-gray-500 mt-1">
              {isEditMode ? 'Perbaiki dan ajukan ulang aktivitas' : 'Buat pengajuan aktivitas baru'}
            </p>
          </div>
        </div>

        {/* Revision Comment Alert */}
        {isEditMode && revisionComment && (
          <div className="p-4 bg-orange-50 border border-orange-200 rounded-lg">
            <div className="flex items-start gap-3">
              <RotateCcw className="h-5 w-5 text-orange-600 mt-0.5 flex-shrink-0" />
              <div>
                <h4 className="font-semibold text-orange-900">Catatan Perbaikan dari Verifikator:</h4>
                <p className="text-orange-800 mt-1">{revisionComment}</p>
              </div>
            </div>
          </div>
        )}

        {/* Form */}
        <Card>
          <CardHeader>
            <CardTitle>Form Aktivitas</CardTitle>
          </CardHeader>
          <CardContent>
            <form onSubmit={handleSubmit} className="space-y-6">
              {error && (
                <div className="p-3 text-sm text-red-500 bg-red-50 border border-red-200 rounded-md">
                  {error}
                </div>
              )}

              {/* Category Selection */}
              <div className="space-y-2">
                <Label htmlFor="category">1. Pilih Kategori *</Label>
                <select
                  id="category"
                  value={selectedCategory}
                  onChange={handleCategoryChange}
                  className="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50"
                  required
                  disabled={loading}
                >
                  <option value="">-- Pilih Kategori --</option>
                  {categories.map((category) => (
                    <option key={category} value={category}>
                      {category}
                    </option>
                  ))}
                </select>
              </div>

              {/* Subcategory Selection */}
              {selectedCategory && subcategories.length > 0 && (
                <div className="space-y-2">
                  <Label htmlFor="subcategory">2. Pilih Sub Kategori *</Label>
                  <select
                    id="subcategory"
                    value={selectedSubcategory}
                    onChange={handleSubcategoryChange}
                    className="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50"
                    required
                    disabled={loading}
                  >
                    <option value="">-- Pilih Sub Kategori --</option>
                    {subcategories.map((subcategory) => {
                      const schema = schemas.find(s => s.subcategory === subcategory);
                      return (
                        <option key={subcategory} value={subcategory}>
                          {schema?.subcategory_name || subcategory}
                        </option>
                      );
                    })}
                  </select>
                </div>
              )}

              {/* Activity Selection */}
              {selectedCategory && (selectedSubcategory || subcategories.length === 0) && (
                <div className="space-y-2">
                  <Label htmlFor="schema_id">3. Pilih Aktivitas *</Label>
                  <Select
                    id="schema_id"
                    options={schemaOptions}
                    value={schemaOptions.find(opt => opt.value.toString() === formData.schema_id) || null}
                    onChange={handleSchemaChange}
                    placeholder="Ketik untuk mencari aktivitas..."
                    isClearable
                    isSearchable
                    isDisabled={loading}
                    styles={customStyles}
                    className="react-select-container"
                    classNamePrefix="react-select"
                    noOptionsMessage={() => "Tidak ada aktivitas ditemukan"}
                  />
                </div>
              )}

              {/* Schema Details */}
              {selectedSchema && (
                <>
                  <div className="p-4 bg-blue-50 rounded-lg border border-blue-200">
                    <h4 className="font-semibold text-blue-900 mb-3 flex items-center gap-2">
                      <Info className="h-5 w-5" />
                      Detail Aktivitas & Persyaratan
                    </h4>
                    <div className="grid grid-cols-2 gap-3 text-sm">
                      <div>
                        <span className="text-gray-600">Kategori:</span>{' '}
                        <span className="font-medium">{selectedSchema.category}</span>
                      </div>
                      <div>
                        <span className="text-gray-600">Sub Kategori:</span>{' '}
                        <span className="font-medium">{selectedSchema.subcategory_name || selectedSchema.subcategory}</span>
                      </div>
                      <div>
                        <span className="text-gray-600">Angka Kredit:</span>{' '}
                        <span className="font-medium text-blue-700">{selectedSchema.credit_points} poin</span>
                      </div>
                      <div>
                        <span className="text-gray-600">Satuan Hasil:</span>{' '}
                        <span className="font-medium">{selectedSchema.satuan_hasil || '-'}</span>
                      </div>
                      <div>
                        <span className="text-gray-600">Jenis Unsur:</span>{' '}
                        <span className={`inline-flex px-2 py-0.5 rounded-full text-xs font-medium ${
                          selectedSchema.unsur_type === 'utama'
                            ? 'bg-blue-100 text-blue-700'
                            : 'bg-green-100 text-green-700'
                        }`}>
                          {selectedSchema.unsur_type?.toUpperCase()}
                        </span>
                      </div>
                      <div>
                        <span className="text-gray-600">Jenjang Pelaksana:</span>{' '}
                        <span className={`inline-flex px-2 py-0.5 rounded-full text-xs font-medium ${
                          selectedSchema.pelaksana === 'Semua Jenjang'
                            ? 'bg-gray-100 text-gray-700'
                            : 'bg-purple-100 text-purple-700'
                        }`}>
                          {selectedSchema.pelaksana || 'Semua Jenjang'}
                        </span>
                      </div>
                      {selectedSchema.batasan_penilaian && selectedSchema.batasan_penilaian !== 'tidak terbatas' && (
                        <div className="col-span-2 bg-yellow-100 border border-yellow-300 rounded px-3 py-2">
                          <span className="text-gray-700 font-semibold">⚠️ Batasan Penilaian:</span>{' '}
                          <span className="font-medium text-yellow-800">{selectedSchema.batasan_penilaian}</span>
                          <p className="text-xs text-yellow-700 mt-1">
                            Pastikan Anda tidak melebihi batas ini dalam periode yang ditentukan
                          </p>
                        </div>
                      )}
                      <div className="col-span-2">
                        <span className="text-gray-600">Bukti Fisik yang Dibutuhkan:</span>{' '}
                        <span className="font-medium">{selectedSchema.bukti_fisik || 'Dokumentasi bukti pelaksanaan'}</span>
                      </div>
                      {selectedSchema.description && (
                        <div className="col-span-2">
                          <span className="text-gray-600">Deskripsi:</span>{' '}
                          <p className="text-gray-700 mt-1">{selectedSchema.description}</p>
                        </div>
                      )}
                    </div>
                  </div>

                  {/* Banking Warning */}
                  <div className="p-4 bg-yellow-50 rounded-lg border border-yellow-200">
                    <div className="flex items-start gap-3">
                      <AlertTriangle className="h-5 w-5 text-yellow-600 mt-0.5 flex-shrink-0" />
                      <div className="text-sm">
                        <h5 className="font-semibold text-yellow-900 mb-1">Perhatian: Sistem Credit Banking</h5>
                        <p className="text-yellow-800 mb-2">
                          Kredit dari aktivitas ini mungkin akan di-<strong>bank</strong> (disimpan untuk jenjang berikutnya) jika:
                        </p>
                        <ul className="list-disc list-inside space-y-1 text-yellow-800 text-xs">
                          <li>Melanggar aturan compliance (min 80% Unsur Utama, max 20% Penunjang)</li>
                          <li>Melebihi batas maksimal kredit untuk jenjang Anda saat ini</li>
                        </ul>
                        <p className="text-yellow-700 text-xs mt-2 flex items-center gap-1">
                          <Info className="h-3 w-3" />
                          Kredit yang di-bank akan otomatis unlock saat promosi ke jenjang berikutnya
                        </p>
                      </div>
                    </div>
                  </div>
                </>
              )}

              {/* Title */}
              <div className="space-y-2">
                <Label htmlFor="title">Judul Aktivitas *</Label>
                <Input
                  id="title"
                  value={formData.title}
                  onChange={(e) => setFormData(prev => ({ ...prev, title: e.target.value }))}
                  placeholder="Judul aktivitas Anda"
                  required
                  disabled={loading}
                />
              </div>

              {/* Description */}
              <div className="space-y-2">
                <Label htmlFor="description">Deskripsi *</Label>
                <textarea
                  id="description"
                  value={formData.description}
                  onChange={(e) => setFormData(prev => ({ ...prev, description: e.target.value }))}
                  placeholder="Jelaskan detail aktivitas yang Anda lakukan..."
                  className="flex min-h-[120px] w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50"
                  required
                  disabled={loading}
                />
              </div>

              {/* File Upload */}
              <div className="space-y-2">
                <Label htmlFor="proof_file">Bukti Fisik</Label>
                <div className="border-2 border-dashed border-gray-300 rounded-lg p-6 text-center hover:border-primary transition-colors">
                  <Upload className="h-8 w-8 text-gray-400 mx-auto mb-2" />
                  <label htmlFor="proof_file" className="cursor-pointer">
                    <span className="text-sm text-primary hover:underline">
                      Klik untuk upload file
                    </span>
                    <input
                      id="proof_file"
                      type="file"
                      onChange={handleFileChange}
                      className="hidden"
                      accept=".pdf,.jpg,.jpeg,.png,.doc,.docx"
                      disabled={loading}
                    />
                  </label>
                  <p className="text-xs text-gray-500 mt-1">
                    PDF, JPG, PNG, DOC (Max 5MB)
                  </p>
                  {file && (
                    <p className="text-sm text-green-600 mt-2">
                      ✓ {file.name}
                    </p>
                  )}
                </div>
              </div>

              {/* Submit Button */}
              <div className="flex gap-3">
                <Button
                  type="button"
                  variant="outline"
                  onClick={() => navigate('/activities')}
                  disabled={loading || initialLoading}
                  className="flex-1"
                >
                  Batal
                </Button>
                <Button type="submit" disabled={loading || initialLoading} className="flex-1">
                  {loading ? 'Menyimpan...' : isEditMode ? 'Ajukan Ulang' : 'Simpan Aktivitas'}
                </Button>
              </div>
            </form>
          </CardContent>
        </Card>
      </div>
    </Layout>
  )
}
