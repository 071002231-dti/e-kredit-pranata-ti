import { useEffect, useState } from 'react'
import { useNavigate, useParams } from 'react-router-dom'
import Select from 'react-select'
import { Layout } from '../components/Layout'
import { Card, CardContent, CardHeader, CardTitle } from '../components/ui/Card'
import { Button } from '../components/ui/Button'
import { Input } from '../components/ui/Input'
import { Label } from '../components/ui/Label'
import { skpService, type Skp, type SkpItem } from '../services/skpService'
import { creditSchemaService } from '../services/creditSchemaService'
import type { CreditSchema } from '../types'
import {
  ArrowLeft,
  Save,
  Send,
  Plus,
  Trash2,
  AlertCircle,
  Target,
  FileText
} from 'lucide-react'
import { formatNumber } from '../lib/utils'

interface SchemaOption {
  value: number
  label: string
  schema: CreditSchema
}

export function SkpFormPage() {
  const { id } = useParams()
  const navigate = useNavigate()
  const isEditMode = Boolean(id)

  const [loading, setLoading] = useState(false)
  const [skp, setSkp] = useState<Skp | null>(null)
  const [schemas, setSchemas] = useState<CreditSchema[]>([])
  const [formData, setFormData] = useState({
    tahun: new Date().getFullYear(),
    target_angka_kredit: 0,
    tugas_tambahan: '',
  })
  const [items, setItems] = useState<SkpItem[]>([])
  const [error, setError] = useState('')

  // New item form
  const [selectedSchema, setSelectedSchema] = useState<CreditSchema | null>(null)
  const [targetQuantity, setTargetQuantity] = useState(1)

  useEffect(() => {
    loadSchemas()
    if (isEditMode) {
      loadSkp()
    }
  }, [id])

  const loadSchemas = async () => {
    try {
      const data = await creditSchemaService.getCreditSchemas({ per_page: 100 })
      setSchemas(data.data)
    } catch (error) {
      console.error('Failed to load schemas:', error)
    }
  }

  const loadSkp = async () => {
    if (!id) return
    try {
      setLoading(true)
      const data = await skpService.getSkp(parseInt(id))
      setSkp(data.skp)
      setFormData({
        tahun: data.skp.tahun,
        target_angka_kredit: data.skp.target_angka_kredit,
        tugas_tambahan: data.skp.tugas_tambahan || '',
      })
      setItems(data.skp.items || [])
    } catch (err: any) {
      setError(err.response?.data?.message || 'Gagal memuat SKP')
    } finally {
      setLoading(false)
    }
  }

  const schemaOptions: SchemaOption[] = schemas.map((schema) => ({
    value: schema.id,
    label: `${schema.activity_name} (${schema.credit_points} poin - ${schema.category})`,
    schema: schema,
  }))

  const handleSave = async () => {
    setError('')
    setLoading(true)

    try {
      if (isEditMode && id) {
        // Update existing SKP
        await skpService.updateSkp(parseInt(id), {
          target_angka_kredit: formData.target_angka_kredit,
          tugas_tambahan: formData.tugas_tambahan,
        })
        alert('SKP berhasil diupdate')
      } else {
        // Create new SKP
        const response = await skpService.createSkp(formData)
        alert('SKP berhasil dibuat')
        navigate(`/skp/${response.skp.id}`)
      }
    } catch (err: any) {
      setError(err.response?.data?.message || 'Gagal menyimpan SKP')
    } finally {
      setLoading(false)
    }
  }

  const handleAddItem = async () => {
    if (!selectedSchema || !id) return

    setError('')
    try {
      await skpService.addItem(parseInt(id), {
        schema_id: selectedSchema.id,
        target_quantity: targetQuantity,
      })
      setSelectedSchema(null)
      setTargetQuantity(1)
      loadSkp() // Reload to get updated items
    } catch (err: any) {
      setError(err.response?.data?.message || 'Gagal menambah item')
    }
  }

  const handleRemoveItem = async (itemId: number) => {
    if (!confirm('Yakin ingin menghapus item ini?') || !id) return

    try {
      await skpService.removeItem(parseInt(id), itemId)
      loadSkp()
    } catch (err: any) {
      alert(err.response?.data?.message || 'Gagal menghapus item')
    }
  }

  const handleSubmit = async () => {
    if (!id) return
    if (!confirm('Yakin ingin submit SKP ini untuk persetujuan? SKP tidak bisa diubah setelah disubmit.')) return

    try {
      await skpService.submitSkp(parseInt(id))
      alert('SKP berhasil disubmit untuk persetujuan')
      navigate('/skp')
    } catch (err: any) {
      alert(err.response?.data?.message || 'Gagal submit SKP')
    }
  }

  const totalTargetPoints = items.reduce((sum, item) => sum + item.target_points, 0)
  const isDraft = !isEditMode || skp?.status === 'draft'

  if (loading && isEditMode) {
    return (
      <Layout>
        <div className="flex items-center justify-center h-64">
          <div className="animate-spin rounded-full h-12 w-12 border-b-2 border-primary"></div>
        </div>
      </Layout>
    )
  }

  return (
    <Layout>
      <div className="max-w-4xl mx-auto space-y-6">
        {/* Header */}
        <div className="flex items-center gap-4">
          <Button
            variant="ghost"
            size="icon"
            onClick={() => navigate('/skp')}
          >
            <ArrowLeft className="h-5 w-5" />
          </Button>
          <div>
            <h1 className="text-3xl font-bold text-gray-900">
              {isEditMode ? `SKP Tahun ${formData.tahun}` : 'Buat SKP Baru'}
            </h1>
            <p className="text-gray-500 mt-1">
              {isEditMode ? 'Kelola target dan items SKP Anda' : 'Rencanakan target kinerja tahunan'}
            </p>
          </div>
        </div>

        {error && (
          <div className="p-4 bg-red-50 border border-red-200 rounded-lg">
            <p className="text-sm text-red-800">{error}</p>
          </div>
        )}

        {/* Basic Info */}
        <Card>
          <CardHeader>
            <CardTitle>Informasi Dasar SKP</CardTitle>
          </CardHeader>
          <CardContent className="space-y-4">
            <div className="grid grid-cols-2 gap-4">
              <div className="space-y-2">
                <Label htmlFor="tahun">Tahun *</Label>
                <Input
                  id="tahun"
                  type="number"
                  value={formData.tahun}
                  onChange={(e) =>
                    setFormData((prev) => ({ ...prev, tahun: parseInt(e.target.value) }))
                  }
                  disabled={isEditMode || loading}
                  min={new Date().getFullYear()}
                />
              </div>

              <div className="space-y-2">
                <Label htmlFor="target">Target Angka Kredit *</Label>
                <Input
                  id="target"
                  type="number"
                  value={formData.target_angka_kredit}
                  onChange={(e) =>
                    setFormData((prev) => ({
                      ...prev,
                      target_angka_kredit: parseFloat(e.target.value),
                    }))
                  }
                  disabled={!isDraft || loading}
                  min={0}
                  step={0.001}
                />
              </div>
            </div>

            <div className="space-y-2">
              <Label htmlFor="tugas">Tugas Tambahan (Opsional)</Label>
              <textarea
                id="tugas"
                value={formData.tugas_tambahan}
                onChange={(e) =>
                  setFormData((prev) => ({ ...prev, tugas_tambahan: e.target.value }))
                }
                disabled={!isDraft || loading}
                placeholder="Contoh: Koordinator Tim IT, Pembimbing PKL, dll"
                className="flex min-h-[80px] w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50"
              />
            </div>

            {isDraft && (
              <Button onClick={handleSave} disabled={loading}>
                <Save className="h-4 w-4 mr-2" />
                {isEditMode ? 'Update SKP' : 'Simpan SKP'}
              </Button>
            )}
          </CardContent>
        </Card>

        {/* Items Management (only for existing SKP) */}
        {isEditMode && (
          <>
            {/* Summary */}
            <Card>
              <CardHeader>
                <CardTitle className="flex items-center gap-2">
                  <Target className="h-5 w-5" />
                  Ringkasan Target
                </CardTitle>
              </CardHeader>
              <CardContent>
                <div className="grid grid-cols-3 gap-4 text-center">
                  <div>
                    <div className="text-sm text-gray-600">Target</div>
                    <div className="text-2xl font-bold text-primary">
                      {formatNumber(formData.target_angka_kredit)}
                    </div>
                  </div>
                  <div>
                    <div className="text-sm text-gray-600">Items</div>
                    <div className="text-2xl font-bold">{items.length}</div>
                  </div>
                  <div>
                    <div className="text-sm text-gray-600">Total Poin Items</div>
                    <div className="text-2xl font-bold text-green-600">
                      {formatNumber(totalTargetPoints)}
                    </div>
                  </div>
                </div>

                {totalTargetPoints !== formData.target_angka_kredit && (
                  <div className="mt-4 p-3 bg-yellow-50 border border-yellow-200 rounded-lg">
                    <div className="flex items-start gap-2">
                      <AlertCircle className="h-5 w-5 text-yellow-600 mt-0.5 flex-shrink-0" />
                      <div className="text-sm">
                        <p className="font-semibold text-yellow-900">Perhatian</p>
                        <p className="text-yellow-800">
                          Total poin items ({formatNumber(totalTargetPoints)}) berbeda dengan target SKP (
                          {formatNumber(formData.target_angka_kredit)}). Sesuaikan items atau target Anda.
                        </p>
                      </div>
                    </div>
                  </div>
                )}
              </CardContent>
            </Card>

            {/* Add Item Form */}
            {isDraft && (
              <Card>
                <CardHeader>
                  <CardTitle className="flex items-center gap-2">
                    <Plus className="h-5 w-5" />
                    Tambah Item Aktivitas
                  </CardTitle>
                </CardHeader>
                <CardContent className="space-y-4">
                  <div className="space-y-2">
                    <Label>Pilih Aktivitas *</Label>
                    <Select
                      options={schemaOptions}
                      value={
                        schemaOptions.find((opt) => opt.value === selectedSchema?.id) || null
                      }
                      onChange={(option) => setSelectedSchema(option?.schema || null)}
                      placeholder="Ketik untuk mencari aktivitas..."
                      isClearable
                      isSearchable
                      className="react-select-container"
                      classNamePrefix="react-select"
                    />
                  </div>

                  {selectedSchema && (
                    <div className="p-4 bg-blue-50 rounded-lg border border-blue-200">
                      <div className="grid grid-cols-2 gap-3 text-sm">
                        <div>
                          <span className="text-gray-600">Kategori:</span>{' '}
                          <span className="font-medium">{selectedSchema.category}</span>
                        </div>
                        <div>
                          <span className="text-gray-600">Angka Kredit:</span>{' '}
                          <span className="font-medium text-blue-700">
                            {selectedSchema.credit_points} poin
                          </span>
                        </div>
                        <div>
                          <span className="text-gray-600">Satuan:</span>{' '}
                          <span className="font-medium">{selectedSchema.satuan_hasil}</span>
                        </div>
                        <div>
                          <span className="text-gray-600">Jenjang:</span>{' '}
                          <span className="font-medium">{selectedSchema.pelaksana}</span>
                        </div>
                      </div>
                    </div>
                  )}

                  <div className="grid grid-cols-2 gap-4">
                    <div className="space-y-2">
                      <Label htmlFor="quantity">Target Quantity *</Label>
                      <Input
                        id="quantity"
                        type="number"
                        value={targetQuantity}
                        onChange={(e) => setTargetQuantity(parseInt(e.target.value))}
                        min={1}
                      />
                    </div>

                    {selectedSchema && (
                      <div className="space-y-2">
                        <Label>Target Poin</Label>
                        <div className="h-10 flex items-center px-3 bg-gray-100 rounded-md font-semibold text-primary">
                          {formatNumber(parseFloat(selectedSchema.credit_points) * targetQuantity)} poin
                        </div>
                      </div>
                    )}
                  </div>

                  <Button onClick={handleAddItem} disabled={!selectedSchema || loading}>
                    <Plus className="h-4 w-4 mr-2" />
                    Tambah ke SKP
                  </Button>
                </CardContent>
              </Card>
            )}

            {/* Items List */}
            <Card>
              <CardHeader>
                <CardTitle className="flex items-center gap-2">
                  <FileText className="h-5 w-5" />
                  Items SKP ({items.length})
                </CardTitle>
              </CardHeader>
              <CardContent>
                {items.length === 0 ? (
                  <div className="text-center py-8 text-gray-500">
                    Belum ada item. Tambahkan aktivitas yang akan Anda lakukan tahun ini.
                  </div>
                ) : (
                  <div className="space-y-3">
                    {items.map((item, index) => (
                      <div
                        key={item.id}
                        className="p-4 border rounded-lg hover:bg-gray-50 transition-colors"
                      >
                        <div className="flex justify-between items-start">
                          <div className="flex-1">
                            <div className="flex items-center gap-2 mb-2">
                              <span className="text-xs font-semibold text-gray-500">
                                #{index + 1}
                              </span>
                              <h4 className="font-semibold text-gray-900">
                                {item.creditSchema?.activity_name}
                              </h4>
                            </div>
                            <div className="grid grid-cols-3 gap-3 text-sm">
                              <div>
                                <span className="text-gray-600">Kategori:</span>{' '}
                                <span className="font-medium">
                                  {item.creditSchema?.category}
                                </span>
                              </div>
                              <div>
                                <span className="text-gray-600">Target:</span>{' '}
                                <span className="font-medium">
                                  {item.target_quantity} {item.creditSchema?.satuan_hasil}
                                </span>
                              </div>
                              <div>
                                <span className="text-gray-600">Poin:</span>{' '}
                                <span className="font-medium text-green-600">
                                  {formatNumber(item.target_points)}
                                </span>
                              </div>
                            </div>
                          </div>

                          {isDraft && (
                            <Button
                              variant="ghost"
                              size="sm"
                              onClick={() => handleRemoveItem(item.id)}
                              className="text-red-600 hover:text-red-700 hover:bg-red-50"
                            >
                              <Trash2 className="h-4 w-4" />
                            </Button>
                          )}
                        </div>
                      </div>
                    ))}
                  </div>
                )}
              </CardContent>
            </Card>

            {/* Submit Button */}
            {isDraft && items.length > 0 && (
              <div className="flex gap-3">
                <Button
                  variant="outline"
                  onClick={() => navigate('/skp')}
                  className="flex-1"
                >
                  Kembali
                </Button>
                <Button onClick={handleSubmit} className="flex-1">
                  <Send className="h-4 w-4 mr-2" />
                  Submit untuk Persetujuan
                </Button>
              </div>
            )}
          </>
        )}
      </div>
    </Layout>
  )
}
