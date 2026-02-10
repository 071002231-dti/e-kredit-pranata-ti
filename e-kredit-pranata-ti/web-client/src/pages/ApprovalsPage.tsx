import { useState, useEffect } from 'react'
import { Layout } from '../components/Layout'
import { Card, CardContent, CardHeader, CardTitle } from '../components/ui/Card'
import { Button } from '../components/ui/Button'
import api from '../lib/api'
import { CheckCircle, XCircle, Clock, Eye, FileText, RotateCcw } from 'lucide-react'

interface Activity {
  id: number
  title: string
  description: string
  status: string
  credit_points: string
  user: {
    id: number
    name: string
    email: string
    jenjang_jabatan: string
  }
  schema: {
    id: number
    category: string
    subcategory: string
    subcategory_name?: string
    activity_name: string
    credit_points: string
    unsur_type: string
  }
  created_at: string
  proof_file_url?: string
}

export function ApprovalsPage() {
  const [activities, setActivities] = useState<Activity[]>([])
  const [loading, setLoading] = useState(true)
  const [filter, setFilter] = useState<'pending' | 'approved' | 'rejected' | 'revision' | 'all'>('pending')
  const [selectedActivity, setSelectedActivity] = useState<Activity | null>(null)
  const [actionLoading, setActionLoading] = useState(false)
  const [feedbackNote, setFeedbackNote] = useState('')
  const [actionType, setActionType] = useState<'reject' | 'revision' | null>(null)

  useEffect(() => {
    loadActivities()
  }, [filter])

  const loadActivities = async () => {
    setLoading(true)
    try {
      const params: Record<string, string> = {}
      if (filter !== 'all') {
        params.status = filter
      }
      const { data } = await api.get('/approvals', { params })
      setActivities(Array.isArray(data) ? data : data.data || [])
    } catch (error) {
      console.error('Failed to load activities:', error)
    } finally {
      setLoading(false)
    }
  }

  const handleApprove = async (id: number) => {
    setActionLoading(true)
    try {
      await api.post(`/approvals/${id}/approve`)
      loadActivities()
      setSelectedActivity(null)
    } catch (error) {
      console.error('Failed to approve:', error)
    } finally {
      setActionLoading(false)
    }
  }

  const handleReject = async (id: number) => {
    if (!feedbackNote.trim()) {
      alert('Mohon isi alasan penolakan')
      return
    }
    setActionLoading(true)
    try {
      await api.post(`/approvals/${id}/reject`, { comments: feedbackNote })
      loadActivities()
      setSelectedActivity(null)
      setFeedbackNote('')
      setActionType(null)
    } catch (error) {
      console.error('Failed to reject:', error)
    } finally {
      setActionLoading(false)
    }
  }

  const handleRevision = async (id: number) => {
    if (!feedbackNote.trim()) {
      alert('Mohon isi catatan perbaikan yang diperlukan')
      return
    }
    setActionLoading(true)
    try {
      await api.post(`/approvals/${id}/revision`, { comments: feedbackNote })
      loadActivities()
      setSelectedActivity(null)
      setFeedbackNote('')
      setActionType(null)
    } catch (error) {
      console.error('Failed to request revision:', error)
    } finally {
      setActionLoading(false)
    }
  }

  const getStatusBadge = (status: string) => {
    switch (status) {
      case 'pending':
        return <span className="inline-flex items-center gap-1 px-2 py-1 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800"><Clock className="h-3 w-3" /> Menunggu</span>
      case 'approved':
        return <span className="inline-flex items-center gap-1 px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800"><CheckCircle className="h-3 w-3" /> Disetujui</span>
      case 'rejected':
        return <span className="inline-flex items-center gap-1 px-2 py-1 rounded-full text-xs font-medium bg-red-100 text-red-800"><XCircle className="h-3 w-3" /> Ditolak</span>
      case 'revision':
        return <span className="inline-flex items-center gap-1 px-2 py-1 rounded-full text-xs font-medium bg-orange-100 text-orange-800"><RotateCcw className="h-3 w-3" /> Perlu Perbaikan</span>
      default:
        return <span className="px-2 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-800">{status}</span>
    }
  }

  return (
    <Layout>
      <div className="space-y-6">
        {/* Header */}
        <div>
          <h1 className="text-3xl font-bold text-gray-900">Review Aktivitas</h1>
          <p className="text-gray-500 mt-1">Verifikasi dan setujui pengajuan aktivitas</p>
        </div>

        {/* Filter Tabs */}
        <div className="flex gap-2 flex-wrap">
          {[
            { key: 'pending', label: 'Menunggu' },
            { key: 'revision', label: 'Perlu Perbaikan' },
            { key: 'approved', label: 'Disetujui' },
            { key: 'rejected', label: 'Ditolak' },
            { key: 'all', label: 'Semua' },
          ].map((tab) => (
            <Button
              key={tab.key}
              variant={filter === tab.key ? 'default' : 'outline'}
              size="sm"
              onClick={() => setFilter(tab.key as typeof filter)}
            >
              {tab.label}
            </Button>
          ))}
        </div>

        {/* Activities List */}
        <Card>
          <CardHeader>
            <CardTitle>Daftar Pengajuan</CardTitle>
          </CardHeader>
          <CardContent>
            {loading ? (
              <div className="text-center py-8 text-gray-500">Memuat data...</div>
            ) : activities.length === 0 ? (
              <div className="text-center py-8 text-gray-500">
                <FileText className="h-12 w-12 mx-auto mb-3 text-gray-300" />
                <p>Tidak ada pengajuan {filter !== 'all' ? `dengan status ${filter}` : ''}</p>
              </div>
            ) : (
              <div className="overflow-x-auto">
                <table className="w-full">
                  <thead>
                    <tr className="border-b text-left text-sm text-gray-500">
                      <th className="pb-3 font-medium">Pengaju</th>
                      <th className="pb-3 font-medium">Aktivitas</th>
                      <th className="pb-3 font-medium">Kategori</th>
                      <th className="pb-3 font-medium">Kredit</th>
                      <th className="pb-3 font-medium">Status</th>
                      <th className="pb-3 font-medium">Tanggal</th>
                      <th className="pb-3 font-medium">Aksi</th>
                    </tr>
                  </thead>
                  <tbody className="divide-y">
                    {activities.map((activity) => (
                      <tr key={activity.id} className="text-sm">
                        <td className="py-3">
                          <div>
                            <p className="font-medium">{activity.user?.name}</p>
                            <p className="text-xs text-gray-500">{activity.user?.jenjang_jabatan}</p>
                          </div>
                        </td>
                        <td className="py-3">
                          <p className="font-medium">{activity.title}</p>
                          <p className="text-xs text-gray-500 truncate max-w-xs">{activity.description}</p>
                        </td>
                        <td className="py-3">
                          <p className="text-xs">{activity.schema?.category}</p>
                          <p className="text-xs text-gray-500">{activity.schema?.subcategory_name || activity.schema?.subcategory}</p>
                        </td>
                        <td className="py-3">
                          <span className="font-medium text-blue-600">{activity.schema?.credit_points}</span>
                          <span className={`ml-1 text-xs px-1 rounded ${activity.schema?.unsur_type === 'utama' ? 'bg-blue-100 text-blue-700' : 'bg-green-100 text-green-700'}`}>
                            {activity.schema?.unsur_type}
                          </span>
                        </td>
                        <td className="py-3">{getStatusBadge(activity.status)}</td>
                        <td className="py-3 text-gray-500">
                          {new Date(activity.created_at).toLocaleDateString('id-ID')}
                        </td>
                        <td className="py-3">
                          <div className="flex gap-1">
                            <Button
                              size="sm"
                              variant="ghost"
                              onClick={() => setSelectedActivity(activity)}
                            >
                              <Eye className="h-4 w-4" />
                            </Button>
                            {activity.status === 'pending' && (
                              <>
                                <Button
                                  size="sm"
                                  variant="ghost"
                                  className="text-green-600 hover:text-green-700"
                                  onClick={() => handleApprove(activity.id)}
                                  disabled={actionLoading}
                                >
                                  <CheckCircle className="h-4 w-4" />
                                </Button>
                                <Button
                                  size="sm"
                                  variant="ghost"
                                  className="text-red-600 hover:text-red-700"
                                  onClick={() => setSelectedActivity(activity)}
                                  disabled={actionLoading}
                                >
                                  <XCircle className="h-4 w-4" />
                                </Button>
                              </>
                            )}
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

        {/* Detail Modal */}
        {selectedActivity && (
          <div className="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4">
            <div className="bg-white rounded-lg max-w-2xl w-full max-h-[90vh] overflow-y-auto">
              <div className="p-6">
                <div className="flex justify-between items-start mb-4">
                  <h2 className="text-xl font-bold">Detail Aktivitas</h2>
                  <button onClick={() => { setSelectedActivity(null); setActionType(null); setFeedbackNote(''); }} className="text-gray-500 hover:text-gray-700">
                    <XCircle className="h-6 w-6" />
                  </button>
                </div>

                <div className="space-y-4">
                  <div className="grid grid-cols-2 gap-4 text-sm">
                    <div>
                      <p className="text-gray-500">Pengaju</p>
                      <p className="font-medium">{selectedActivity.user?.name}</p>
                      <p className="text-xs text-gray-500">{selectedActivity.user?.email}</p>
                    </div>
                    <div>
                      <p className="text-gray-500">Jenjang Jabatan</p>
                      <p className="font-medium">{selectedActivity.user?.jenjang_jabatan}</p>
                    </div>
                    <div>
                      <p className="text-gray-500">Kategori</p>
                      <p className="font-medium">{selectedActivity.schema?.category}</p>
                    </div>
                    <div>
                      <p className="text-gray-500">Sub Kategori</p>
                      <p className="font-medium">{selectedActivity.schema?.subcategory_name || selectedActivity.schema?.subcategory}</p>
                    </div>
                    <div>
                      <p className="text-gray-500">Angka Kredit</p>
                      <p className="font-medium text-blue-600">{selectedActivity.schema?.credit_points} poin</p>
                    </div>
                    <div>
                      <p className="text-gray-500">Jenis Unsur</p>
                      <p className="font-medium">{selectedActivity.schema?.unsur_type?.toUpperCase()}</p>
                    </div>
                  </div>

                  <div>
                    <p className="text-gray-500 text-sm">Judul</p>
                    <p className="font-medium">{selectedActivity.title}</p>
                  </div>

                  <div>
                    <p className="text-gray-500 text-sm">Deskripsi</p>
                    <p>{selectedActivity.description}</p>
                  </div>

                  {selectedActivity.proof_file_url && (
                    <div>
                      <p className="text-gray-500 text-sm">Bukti Fisik</p>
                      <a
                        href={selectedActivity.proof_file_url}
                        target="_blank"
                        rel="noopener noreferrer"
                        className="text-blue-600 hover:underline"
                      >
                        Lihat Dokumen
                      </a>
                    </div>
                  )}

                  {selectedActivity.status === 'pending' && (
                    <div className="border-t pt-4 mt-4">
                      {/* Action Selection */}
                      {!actionType && (
                        <div className="space-y-3">
                          <p className="text-sm font-medium text-gray-700">Pilih Tindakan:</p>
                          <div className="flex gap-3">
                            <Button
                              className="flex-1 bg-green-600 hover:bg-green-700"
                              onClick={() => handleApprove(selectedActivity.id)}
                              disabled={actionLoading}
                            >
                              <CheckCircle className="h-4 w-4 mr-2" />
                              Setujui
                            </Button>
                            <Button
                              variant="outline"
                              className="flex-1 border-orange-300 text-orange-600 hover:bg-orange-50"
                              onClick={() => setActionType('revision')}
                              disabled={actionLoading}
                            >
                              <RotateCcw className="h-4 w-4 mr-2" />
                              Minta Perbaikan
                            </Button>
                            <Button
                              variant="outline"
                              className="flex-1 border-red-300 text-red-600 hover:bg-red-50"
                              onClick={() => setActionType('reject')}
                              disabled={actionLoading}
                            >
                              <XCircle className="h-4 w-4 mr-2" />
                              Tolak
                            </Button>
                          </div>
                        </div>
                      )}

                      {/* Revision Form */}
                      {actionType === 'revision' && (
                        <div className="space-y-3">
                          <div className="flex items-center gap-2">
                            <RotateCcw className="h-5 w-5 text-orange-600" />
                            <p className="text-sm font-medium text-orange-700">Minta Perbaikan</p>
                          </div>
                          <textarea
                            value={feedbackNote}
                            onChange={(e) => setFeedbackNote(e.target.value)}
                            className="w-full border border-orange-300 rounded-md p-2 text-sm focus:ring-orange-500 focus:border-orange-500"
                            rows={3}
                            placeholder="Jelaskan perbaikan apa yang diperlukan..."
                          />
                          <div className="flex gap-3">
                            <Button
                              variant="outline"
                              className="flex-1"
                              onClick={() => { setActionType(null); setFeedbackNote(''); }}
                            >
                              Batal
                            </Button>
                            <Button
                              className="flex-1 bg-orange-600 hover:bg-orange-700"
                              onClick={() => handleRevision(selectedActivity.id)}
                              disabled={actionLoading || !feedbackNote.trim()}
                            >
                              <RotateCcw className="h-4 w-4 mr-2" />
                              Kirim Permintaan Perbaikan
                            </Button>
                          </div>
                        </div>
                      )}

                      {/* Rejection Form */}
                      {actionType === 'reject' && (
                        <div className="space-y-3">
                          <div className="flex items-center gap-2">
                            <XCircle className="h-5 w-5 text-red-600" />
                            <p className="text-sm font-medium text-red-700">Tolak Pengajuan</p>
                          </div>
                          <textarea
                            value={feedbackNote}
                            onChange={(e) => setFeedbackNote(e.target.value)}
                            className="w-full border border-red-300 rounded-md p-2 text-sm focus:ring-red-500 focus:border-red-500"
                            rows={3}
                            placeholder="Jelaskan alasan penolakan..."
                          />
                          <div className="flex gap-3">
                            <Button
                              variant="outline"
                              className="flex-1"
                              onClick={() => { setActionType(null); setFeedbackNote(''); }}
                            >
                              Batal
                            </Button>
                            <Button
                              className="flex-1 bg-red-600 hover:bg-red-700"
                              onClick={() => handleReject(selectedActivity.id)}
                              disabled={actionLoading || !feedbackNote.trim()}
                            >
                              <XCircle className="h-4 w-4 mr-2" />
                              Konfirmasi Penolakan
                            </Button>
                          </div>
                        </div>
                      )}
                    </div>
                  )}
                </div>
              </div>
            </div>
          </div>
        )}
      </div>
    </Layout>
  )
}
