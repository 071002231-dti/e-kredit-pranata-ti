import { useEffect, useState } from 'react'
import { Link } from 'react-router-dom'
import { Layout } from '../components/Layout'
import { Card, CardContent, CardHeader, CardTitle } from '../components/ui/Card'
import { Button } from '../components/ui/Button'
import { Input } from '../components/ui/Input'
import { activityService } from '../services/activityService'
import type { Activity } from '../types'
import { Plus, Search, FileText, Trash2, Eye, LayoutGrid, Table as TableIcon } from 'lucide-react'
import { formatDate } from '../lib/utils'

type ViewMode = 'table' | 'card'

export function ActivitiesPage() {
  const [activities, setActivities] = useState<Activity[]>([])
  const [loading, setLoading] = useState(true)
  const [search, setSearch] = useState('')
  const [statusFilter, setStatusFilter] = useState<string>('all')
  const [currentPage, setCurrentPage] = useState(1)
  const [totalPages, setTotalPages] = useState(1)
  const [viewMode, setViewMode] = useState<ViewMode>('table')

  useEffect(() => {
    loadActivities()
  }, [currentPage, statusFilter])

  const loadActivities = async () => {
    setLoading(true)
    try {
      const params: any = { per_page: 10 }
      if (statusFilter !== 'all') {
        params.status = statusFilter
      }
      if (search) {
        params.search = search
      }

      const data = await activityService.getActivities(currentPage, params)
      setActivities(data.data)
      setTotalPages(data.last_page)
    } catch (error) {
      console.error('Failed to load activities:', error)
    } finally {
      setLoading(false)
    }
  }

  const handleSearch = () => {
    setCurrentPage(1)
    loadActivities()
  }

  const handleDelete = async (id: number) => {
    if (!confirm('Apakah Anda yakin ingin menghapus aktivitas ini?')) {
      return
    }

    try {
      await activityService.deleteActivity(id)
      loadActivities()
    } catch (error) {
      console.error('Failed to delete activity:', error)
      alert('Gagal menghapus aktivitas')
    }
  }

  const getStatusBadge = (status: string) => {
    const styles = {
      pending: 'bg-yellow-100 text-yellow-700',
      approved: 'bg-green-100 text-green-700',
      rejected: 'bg-red-100 text-red-700',
    }
    const labels = {
      pending: 'Pending',
      approved: 'Disetujui',
      rejected: 'Ditolak',
    }
    return (
      <span className={`inline-flex px-2 py-1 text-xs font-medium rounded-full ${styles[status as keyof typeof styles]}`}>
        {labels[status as keyof typeof labels]}
      </span>
    )
  }

  return (
    <Layout>
      <div className="space-y-6">
        {/* Header */}
        <div className="flex items-center justify-between">
          <div>
            <h1 className="text-3xl font-bold text-gray-900">Aktivitas Saya</h1>
            <p className="text-gray-500 mt-1">Kelola dan pantau aktivitas Anda</p>
          </div>
          <Link to="/activities/new">
            <Button>
              <Plus className="h-4 w-4 mr-2" />
              Tambah Aktivitas
            </Button>
          </Link>
        </div>

        {/* Filters */}
        <Card>
          <CardContent className="p-6">
            <div className="flex flex-col md:flex-row gap-4">
              <div className="flex-1">
                <div className="relative">
                  <Search className="absolute left-3 top-1/2 transform -translate-y-1/2 h-4 w-4 text-gray-400" />
                  <Input
                    placeholder="Cari aktivitas..."
                    value={search}
                    onChange={(e) => setSearch(e.target.value)}
                    onKeyPress={(e) => e.key === 'Enter' && handleSearch()}
                    className="pl-10"
                  />
                </div>
              </div>
              <div className="flex gap-2">
                <Button
                  variant={statusFilter === 'all' ? 'default' : 'outline'}
                  onClick={() => setStatusFilter('all')}
                >
                  Semua
                </Button>
                <Button
                  variant={statusFilter === 'pending' ? 'default' : 'outline'}
                  onClick={() => setStatusFilter('pending')}
                >
                  Pending
                </Button>
                <Button
                  variant={statusFilter === 'approved' ? 'default' : 'outline'}
                  onClick={() => setStatusFilter('approved')}
                >
                  Disetujui
                </Button>
                <Button
                  variant={statusFilter === 'rejected' ? 'default' : 'outline'}
                  onClick={() => setStatusFilter('rejected')}
                >
                  Ditolak
                </Button>
              </div>
            </div>
          </CardContent>
        </Card>

        {/* Activities List */}
        <Card>
          <CardHeader>
            <div className="flex items-center justify-between">
              <CardTitle>Daftar Aktivitas</CardTitle>
              <div className="flex gap-2">
                <Button
                  variant={viewMode === 'table' ? 'default' : 'outline'}
                  size="sm"
                  onClick={() => setViewMode('table')}
                >
                  <TableIcon className="h-4 w-4 mr-2" />
                  Table
                </Button>
                <Button
                  variant={viewMode === 'card' ? 'default' : 'outline'}
                  size="sm"
                  onClick={() => setViewMode('card')}
                >
                  <LayoutGrid className="h-4 w-4 mr-2" />
                  Card
                </Button>
              </div>
            </div>
          </CardHeader>
          <CardContent>
            {loading ? (
              <div className="flex justify-center py-8">
                <div className="animate-spin rounded-full h-8 w-8 border-b-2 border-primary"></div>
              </div>
            ) : activities.length === 0 ? (
              <div className="text-center py-12">
                <FileText className="h-12 w-12 text-gray-400 mx-auto mb-4" />
                <p className="text-gray-500">Belum ada aktivitas</p>
                <Link to="/activities/new">
                  <Button className="mt-4">
                    <Plus className="h-4 w-4 mr-2" />
                    Tambah Aktivitas Pertama
                  </Button>
                </Link>
              </div>
            ) : viewMode === 'table' ? (
              <>
                <div className="overflow-x-auto">
                  <table className="w-full">
                    <thead>
                      <tr className="border-b">
                        <th className="text-left py-3 px-4 font-semibold text-gray-700">Judul</th>
                        <th className="text-left py-3 px-4 font-semibold text-gray-700">Kategori</th>
                        <th className="text-center py-3 px-4 font-semibold text-gray-700">Poin</th>
                        <th className="text-center py-3 px-4 font-semibold text-gray-700">Status</th>
                        <th className="text-center py-3 px-4 font-semibold text-gray-700">Tanggal</th>
                        <th className="text-right py-3 px-4 font-semibold text-gray-700">Aksi</th>
                      </tr>
                    </thead>
                    <tbody>
                      {activities.map((activity) => (
                        <tr key={activity.id} className="border-b hover:bg-gray-50">
                          <td className="py-3 px-4">
                            <div className="font-medium">{activity.title}</div>
                            <div className="text-sm text-gray-500 truncate max-w-md">
                              {activity.description}
                            </div>
                          </td>
                          <td className="py-3 px-4">
                            <div className="text-sm">
                              {activity.schema?.category || '-'}
                            </div>
                          </td>
                          <td className="py-3 px-4 text-center font-semibold">
                            {activity.earned_points}
                          </td>
                          <td className="py-3 px-4 text-center">
                            {getStatusBadge(activity.status)}
                          </td>
                          <td className="py-3 px-4 text-center text-sm">
                            {formatDate(activity.submitted_at)}
                          </td>
                          <td className="py-3 px-4">
                            <div className="flex items-center justify-end gap-2">
                              <Link to={`/activities/${activity.id}`}>
                                <Button variant="ghost" size="sm">
                                  <Eye className="h-4 w-4" />
                                </Button>
                              </Link>
                              {activity.status === 'pending' && (
                                <Button
                                  variant="ghost"
                                  size="sm"
                                  onClick={() => handleDelete(activity.id)}
                                >
                                  <Trash2 className="h-4 w-4 text-red-600" />
                                </Button>
                              )}
                            </div>
                          </td>
                        </tr>
                      ))}
                    </tbody>
                  </table>
                </div>

                {/* Pagination */}
                {totalPages > 1 && (
                  <div className="flex items-center justify-center gap-2 mt-6">
                    <Button
                      variant="outline"
                      onClick={() => setCurrentPage(p => Math.max(1, p - 1))}
                      disabled={currentPage === 1}
                    >
                      Previous
                    </Button>
                    <span className="text-sm text-gray-600">
                      Page {currentPage} of {totalPages}
                    </span>
                    <Button
                      variant="outline"
                      onClick={() => setCurrentPage(p => Math.min(totalPages, p + 1))}
                      disabled={currentPage === totalPages}
                    >
                      Next
                    </Button>
                  </div>
                )}
              </>
            ) : (
              <>
                {/* Card View */}
                <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                  {activities.map((activity) => (
                    <div
                      key={activity.id}
                      className="border rounded-lg p-4 hover:shadow-lg transition-shadow bg-white"
                    >
                      {/* Status Badge */}
                      <div className="flex justify-between items-start mb-3">
                        {getStatusBadge(activity.status)}
                        <span className="text-lg font-bold text-primary">
                          {activity.earned_points} poin
                        </span>
                      </div>

                      {/* Title */}
                      <h3 className="font-semibold text-gray-900 mb-2 line-clamp-2">
                        {activity.title}
                      </h3>

                      {/* Category */}
                      <div className="flex items-center gap-2 text-sm text-gray-600 mb-2">
                        <FileText className="h-4 w-4" />
                        <span className="truncate">{activity.schema?.category || '-'}</span>
                      </div>

                      {/* Description */}
                      <p className="text-sm text-gray-500 line-clamp-2 mb-3">
                        {activity.description}
                      </p>

                      {/* Date */}
                      <div className="text-xs text-gray-400 mb-3">
                        {formatDate(activity.submitted_at)}
                      </div>

                      {/* Actions */}
                      <div className="flex gap-2 pt-3 border-t">
                        <Link to={`/activities/${activity.id}`} className="flex-1">
                          <Button variant="outline" size="sm" className="w-full">
                            <Eye className="h-4 w-4 mr-2" />
                            Detail
                          </Button>
                        </Link>
                        {activity.status === 'pending' && (
                          <Button
                            variant="outline"
                            size="sm"
                            onClick={() => handleDelete(activity.id)}
                            className="text-red-600 hover:text-red-700 hover:border-red-300"
                          >
                            <Trash2 className="h-4 w-4" />
                          </Button>
                        )}
                      </div>
                    </div>
                  ))}
                </div>

                {/* Pagination for Card View */}
                {totalPages > 1 && (
                  <div className="flex items-center justify-center gap-2 mt-6">
                    <Button
                      variant="outline"
                      onClick={() => setCurrentPage(p => Math.max(1, p - 1))}
                      disabled={currentPage === 1}
                    >
                      Previous
                    </Button>
                    <span className="text-sm text-gray-600">
                      Page {currentPage} of {totalPages}
                    </span>
                    <Button
                      variant="outline"
                      onClick={() => setCurrentPage(p => Math.min(totalPages, p + 1))}
                      disabled={currentPage === totalPages}
                    >
                      Next
                    </Button>
                  </div>
                )}
              </>
            )}
          </CardContent>
        </Card>
      </div>
    </Layout>
  )
}
