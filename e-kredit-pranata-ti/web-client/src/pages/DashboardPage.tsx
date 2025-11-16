import { useEffect, useState } from 'react'
import { Link } from 'react-router-dom'
import { Layout } from '../components/Layout'
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '../components/ui/Card'
import { Button } from '../components/ui/Button'
import { dashboardService } from '../services/dashboardService'
import type { DashboardStats, CategorySummary } from '../types'
import {
  FileText,
  Clock,
  CheckCircle,
  XCircle,
  TrendingUp,
  Plus,
  AlertCircle
} from 'lucide-react'
import { formatNumber } from '../lib/utils'

export function DashboardPage() {
  const [stats, setStats] = useState<DashboardStats | null>(null)
  const [summary, setSummary] = useState<CategorySummary[]>([])
  const [loading, setLoading] = useState(true)

  useEffect(() => {
    loadData()
  }, [])

  const loadData = async () => {
    try {
      const [statsData, summaryData] = await Promise.all([
        dashboardService.getStats(),
        dashboardService.getSummary()
      ])
      setStats(statsData)
      setSummary(summaryData)
    } catch (error) {
      console.error('Failed to load dashboard data:', error)
    } finally {
      setLoading(false)
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

  const statCards = [
    {
      title: 'Total Aktivitas',
      value: stats?.total_activities || 0,
      icon: FileText,
      color: 'text-blue-600',
      bgColor: 'bg-blue-50'
    },
    {
      title: 'Pending',
      value: stats?.pending || 0,
      icon: Clock,
      color: 'text-yellow-600',
      bgColor: 'bg-yellow-50'
    },
    {
      title: 'Disetujui',
      value: stats?.approved || 0,
      icon: CheckCircle,
      color: 'text-green-600',
      bgColor: 'bg-green-50'
    },
    {
      title: 'Ditolak',
      value: stats?.rejected || 0,
      icon: XCircle,
      color: 'text-red-600',
      bgColor: 'bg-red-50'
    },
  ]

  const complianceColor = stats?.compliance_status === 'compliant'
    ? 'text-green-600'
    : stats?.compliance_status === 'warning'
    ? 'text-yellow-600'
    : 'text-red-600'

  const complianceBg = stats?.compliance_status === 'compliant'
    ? 'bg-green-50'
    : stats?.compliance_status === 'warning'
    ? 'bg-yellow-50'
    : 'bg-red-50'

  return (
    <Layout>
      <div className="space-y-6">
        {/* Header */}
        <div className="flex items-center justify-between">
          <div>
            <h1 className="text-3xl font-bold text-gray-900">Dashboard</h1>
            <p className="text-gray-500 mt-1">Ringkasan aktivitas dan angka kredit Anda</p>
          </div>
          <Link to="/activities/new">
            <Button>
              <Plus className="h-4 w-4 mr-2" />
              Tambah Aktivitas
            </Button>
          </Link>
        </div>

        {/* Stats Cards */}
        <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
          {statCards.map((stat) => (
            <Card key={stat.title}>
              <CardContent className="p-6">
                <div className="flex items-center justify-between">
                  <div>
                    <p className="text-sm font-medium text-gray-600">{stat.title}</p>
                    <p className="text-3xl font-bold mt-2">{stat.value}</p>
                  </div>
                  <div className={`p-3 rounded-full ${stat.bgColor}`}>
                    <stat.icon className={`h-6 w-6 ${stat.color}`} />
                  </div>
                </div>
              </CardContent>
            </Card>
          ))}
        </div>

        {/* Points & Compliance */}
        <div className="grid grid-cols-1 lg:grid-cols-2 gap-6">
          {/* Total Points */}
          <Card>
            <CardHeader>
              <CardTitle className="flex items-center gap-2">
                <TrendingUp className="h-5 w-5" />
                Total Angka Kredit
              </CardTitle>
            </CardHeader>
            <CardContent>
              <div className="text-4xl font-bold text-primary">
                {formatNumber(stats?.total_points || 0)}
              </div>
              <div className="mt-4 space-y-2">
                <div className="flex justify-between text-sm">
                  <span className="text-gray-600">Unsur Utama:</span>
                  <span className="font-semibold">
                    {formatNumber(stats?.unsur_utama_points || 0)} ({stats?.unsur_utama_percentage || 0}%)
                  </span>
                </div>
                <div className="flex justify-between text-sm">
                  <span className="text-gray-600">Unsur Penunjang:</span>
                  <span className="font-semibold">
                    {formatNumber(stats?.unsur_penunjang_points || 0)} ({stats?.unsur_penunjang_percentage || 0}%)
                  </span>
                </div>
              </div>
            </CardContent>
          </Card>

          {/* Compliance Status */}
          <Card>
            <CardHeader>
              <CardTitle className="flex items-center gap-2">
                <AlertCircle className="h-5 w-5" />
                Status Compliance
              </CardTitle>
            </CardHeader>
            <CardContent>
              <div className={`p-4 rounded-lg ${complianceBg}`}>
                <div className={`text-lg font-semibold ${complianceColor} mb-2`}>
                  {stats?.compliance_status === 'compliant' && '✓ Memenuhi Syarat'}
                  {stats?.compliance_status === 'warning' && '⚠ Peringatan'}
                  {stats?.compliance_status === 'non_compliant' && '✗ Tidak Memenuhi'}
                </div>
                <p className="text-sm text-gray-700">
                  Aturan: Minimal 80% Unsur Utama, Maksimal 20% Unsur Penunjang
                </p>
              </div>
              <div className="mt-4 space-y-2">
                <div className="text-sm">
                  <div className="flex justify-between mb-1">
                    <span>Unsur Utama</span>
                    <span className="font-medium">{stats?.unsur_utama_percentage || 0}%</span>
                  </div>
                  <div className="w-full bg-gray-200 rounded-full h-2">
                    <div
                      className="bg-blue-600 h-2 rounded-full transition-all"
                      style={{ width: `${stats?.unsur_utama_percentage || 0}%` }}
                    />
                  </div>
                </div>
                <div className="text-sm">
                  <div className="flex justify-between mb-1">
                    <span>Unsur Penunjang</span>
                    <span className="font-medium">{stats?.unsur_penunjang_percentage || 0}%</span>
                  </div>
                  <div className="w-full bg-gray-200 rounded-full h-2">
                    <div
                      className="bg-green-600 h-2 rounded-full transition-all"
                      style={{ width: `${stats?.unsur_penunjang_percentage || 0}%` }}
                    />
                  </div>
                </div>
              </div>
            </CardContent>
          </Card>
        </div>

        {/* Category Summary */}
        <Card>
          <CardHeader>
            <CardTitle>Ringkasan Per Kategori</CardTitle>
            <CardDescription>
              Distribusi aktivitas dan poin per kategori
            </CardDescription>
          </CardHeader>
          <CardContent>
            {summary.length === 0 ? (
              <div className="text-center py-8 text-gray-500">
                Belum ada aktivitas yang disetujui
              </div>
            ) : (
              <div className="overflow-x-auto">
                <table className="w-full">
                  <thead>
                    <tr className="border-b">
                      <th className="text-left py-3 px-4 font-semibold text-gray-700">Kategori</th>
                      <th className="text-center py-3 px-4 font-semibold text-gray-700">Jenis</th>
                      <th className="text-center py-3 px-4 font-semibold text-gray-700">Total</th>
                      <th className="text-center py-3 px-4 font-semibold text-gray-700">Disetujui</th>
                      <th className="text-right py-3 px-4 font-semibold text-gray-700">Poin</th>
                    </tr>
                  </thead>
                  <tbody>
                    {summary.map((item, index) => (
                      <tr key={index} className="border-b hover:bg-gray-50">
                        <td className="py-3 px-4">{item.category}</td>
                        <td className="py-3 px-4 text-center">
                          <span className={`inline-flex px-2 py-1 text-xs font-medium rounded-full ${
                            item.unsur_type === 'utama'
                              ? 'bg-blue-100 text-blue-700'
                              : 'bg-green-100 text-green-700'
                          }`}>
                            {item.unsur_type}
                          </span>
                        </td>
                        <td className="py-3 px-4 text-center">{item.total_activities}</td>
                        <td className="py-3 px-4 text-center">{item.approved_count}</td>
                        <td className="py-3 px-4 text-right font-semibold">
                          {formatNumber(item.earned_points)}
                        </td>
                      </tr>
                    ))}
                  </tbody>
                </table>
              </div>
            )}
          </CardContent>
        </Card>
      </div>
    </Layout>
  )
}
