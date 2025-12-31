import { useEffect, useState } from 'react'
import { Link, useNavigate } from 'react-router-dom'
import { Layout } from '../components/Layout'
import { Card, CardContent, CardHeader, CardTitle } from '../components/ui/Card'
import { Button } from '../components/ui/Button'
import { skpService, type Skp } from '../services/skpService'
import {
  Plus,
  Calendar,
  Target,
  FileText,
  CheckCircle,
  Clock,
  Trash2,
  Eye
} from 'lucide-react'
import { formatNumber } from '../lib/utils'

export function SkpListPage() {
  const navigate = useNavigate()
  const [skps, setSkps] = useState<Skp[]>([])
  const [loading, setLoading] = useState(true)
  const [error, setError] = useState('')

  useEffect(() => {
    loadSkps()
  }, [])

  const loadSkps = async () => {
    try {
      setLoading(true)
      const data = await skpService.getSkps()
      setSkps(data.data)
    } catch (err: any) {
      setError(err.response?.data?.message || 'Gagal memuat data SKP')
    } finally {
      setLoading(false)
    }
  }

  const handleDelete = async (id: number) => {
    if (!confirm('Yakin ingin menghapus SKP ini?')) return

    try {
      await skpService.deleteSkp(id)
      loadSkps()
    } catch (err: any) {
      alert(err.response?.data?.message || 'Gagal menghapus SKP')
    }
  }

  const getStatusBadge = (status: string) => {
    switch (status) {
      case 'draft':
        return <span className="px-2 py-1 text-xs font-medium rounded-full bg-gray-100 text-gray-700">Draft</span>
      case 'submitted':
        return <span className="px-2 py-1 text-xs font-medium rounded-full bg-yellow-100 text-yellow-700">Menunggu Persetujuan</span>
      case 'approved':
        return <span className="px-2 py-1 text-xs font-medium rounded-full bg-green-100 text-green-700">Disetujui</span>
      default:
        return <span className="px-2 py-1 text-xs font-medium rounded-full bg-gray-100 text-gray-700">{status}</span>
    }
  }

  if (loading) {
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
      <div className="space-y-6">
        {/* Header */}
        <div className="flex items-center justify-between">
          <div>
            <h1 className="text-3xl font-bold text-gray-900">
              SKP (Sasaran Kerja Pegawai)
            </h1>
            <p className="text-gray-500 mt-1">
              Kelola target kinerja tahunan Anda
            </p>
          </div>
          <Link to="/skp/new">
            <Button>
              <Plus className="h-4 w-4 mr-2" />
              Buat SKP Baru
            </Button>
          </Link>
        </div>

        {/* Info Card */}
        <Card className="bg-blue-50 border-blue-200">
          <CardContent className="p-4">
            <div className="flex items-start gap-3">
              <FileText className="h-5 w-5 text-blue-600 mt-0.5 flex-shrink-0" />
              <div className="text-sm">
                <p className="font-semibold text-blue-900 mb-1">
                  Tentang SKP
                </p>
                <p className="text-blue-800">
                  SKP (Sasaran Kerja Pegawai) adalah dokumen perencanaan target kinerja yang disusun setiap awal tahun.
                  SKP berisi target angka kredit dan aktivitas-aktivitas yang akan Anda lakukan sepanjang tahun.
                </p>
              </div>
            </div>
          </CardContent>
        </Card>

        {/* Error Message */}
        {error && (
          <div className="p-4 bg-red-50 border border-red-200 rounded-lg">
            <p className="text-sm text-red-800">{error}</p>
          </div>
        )}

        {/* SKP List */}
        {skps.length === 0 ? (
          <Card>
            <CardContent className="p-12 text-center">
              <Calendar className="h-16 w-16 text-gray-400 mx-auto mb-4" />
              <h3 className="text-lg font-semibold text-gray-900 mb-2">
                Belum Ada SKP
              </h3>
              <p className="text-gray-600 mb-6">
                Anda belum membuat SKP. Buat SKP baru untuk merencanakan target kinerja tahunan Anda.
              </p>
              <Link to="/skp/new">
                <Button>
                  <Plus className="h-4 w-4 mr-2" />
                  Buat SKP Pertama
                </Button>
              </Link>
            </CardContent>
          </Card>
        ) : (
          <div className="grid gap-4">
            {skps.map((skp) => (
              <Card key={skp.id} className="hover:shadow-md transition-shadow">
                <CardContent className="p-6">
                  <div className="flex items-start justify-between">
                    <div className="flex-1">
                      <div className="flex items-center gap-3 mb-3">
                        <h3 className="text-xl font-bold text-gray-900">
                          SKP Tahun {skp.tahun}
                        </h3>
                        {getStatusBadge(skp.status)}
                      </div>

                      <div className="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
                        <div className="flex items-center gap-2 text-sm">
                          <Target className="h-4 w-4 text-gray-500" />
                          <span className="text-gray-600">Target:</span>
                          <span className="font-semibold">
                            {formatNumber(skp.target_angka_kredit)} poin
                          </span>
                        </div>

                        <div className="flex items-center gap-2 text-sm">
                          <FileText className="h-4 w-4 text-gray-500" />
                          <span className="text-gray-600">Items:</span>
                          <span className="font-semibold">
                            {skp.items?.length || 0} aktivitas
                          </span>
                        </div>

                        <div className="flex items-center gap-2 text-sm">
                          <Calendar className="h-4 w-4 text-gray-500" />
                          <span className="text-gray-600">Dibuat:</span>
                          <span className="font-semibold">
                            {new Date(skp.created_at).toLocaleDateString('id-ID')}
                          </span>
                        </div>
                      </div>

                      {skp.tugas_tambahan && (
                        <div className="text-sm bg-gray-50 p-3 rounded">
                          <span className="text-gray-600">Tugas Tambahan:</span>{' '}
                          <span className="text-gray-900">{skp.tugas_tambahan}</span>
                        </div>
                      )}
                    </div>

                    <div className="flex gap-2 ml-4">
                      <Button
                        variant="outline"
                        size="sm"
                        onClick={() => navigate(`/skp/${skp.id}`)}
                      >
                        <Eye className="h-4 w-4" />
                      </Button>

                      {skp.status === 'draft' && (
                        <Button
                          variant="outline"
                          size="sm"
                          onClick={() => handleDelete(skp.id)}
                          className="text-red-600 hover:text-red-700 hover:bg-red-50"
                        >
                          <Trash2 className="h-4 w-4" />
                        </Button>
                      )}
                    </div>
                  </div>

                  {/* Progress indicator for submitted/approved SKPs */}
                  {skp.status !== 'draft' && skp.items && skp.items.length > 0 && (
                    <div className="mt-4 pt-4 border-t">
                      <div className="flex justify-between text-sm mb-1">
                        <span className="text-gray-600">Progress</span>
                        <span className="font-medium">
                          {formatNumber(
                            skp.items.reduce((sum, item) => sum + item.realisasi_points, 0)
                          )} / {formatNumber(skp.target_angka_kredit)} poin
                        </span>
                      </div>
                      <div className="w-full bg-gray-200 rounded-full h-2">
                        <div
                          className="bg-primary h-2 rounded-full transition-all"
                          style={{
                            width: `${Math.min(
                              (skp.items.reduce((sum, item) => sum + item.realisasi_points, 0) /
                                skp.target_angka_kredit) *
                                100,
                              100
                            )}%`,
                          }}
                        />
                      </div>
                    </div>
                  )}
                </CardContent>
              </Card>
            ))}
          </div>
        )}
      </div>
    </Layout>
  )
}
