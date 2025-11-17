import { useEffect, useState } from 'react'
import { Layout } from '../components/Layout'
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '../components/ui/Card'
import { Button } from '../components/ui/Button'
import { creditBankService } from '../services/creditBankService'
import type { CreditBank, PaginatedResponse } from '../types'
import {
  Lock,
  Unlock,
  Calendar,
  Award,
  AlertCircle,
  Filter,
  ChevronLeft,
  ChevronRight
} from 'lucide-react'
import { formatNumber } from '../lib/utils'

export function CreditBankPage() {
  const [bankedCredits, setBankedCredits] = useState<PaginatedResponse<CreditBank> | null>(null)
  const [loading, setLoading] = useState(true)
  const [filter, setFilter] = useState<'all' | 'banked' | 'unlocked'>('all')
  const [currentPage, setCurrentPage] = useState(1)

  useEffect(() => {
    loadBankedCredits()
  }, [filter, currentPage])

  const loadBankedCredits = async () => {
    try {
      setLoading(true)
      const params: any = { page: currentPage }
      if (filter !== 'all') {
        params.status = filter
      }
      const data = await creditBankService.getAll(params)
      setBankedCredits(data)
    } catch (error) {
      console.error('Failed to load banked credits:', error)
    } finally {
      setLoading(false)
    }
  }

  const formatDate = (dateString: string) => {
    return new Date(dateString).toLocaleDateString('id-ID', {
      year: 'numeric',
      month: 'long',
      day: 'numeric',
      hour: '2-digit',
      minute: '2-digit'
    })
  }

  const getReasonBadge = (reason: string) => {
    if (reason.includes('maksimal')) {
      return 'bg-red-100 text-red-700'
    } else if (reason.includes('compliance')) {
      return 'bg-yellow-100 text-yellow-700'
    } else {
      return 'bg-blue-100 text-blue-700'
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
            <h1 className="text-3xl font-bold text-gray-900">Kredit yang Di-Bank</h1>
            <p className="text-gray-500 mt-1">
              Kredit yang disimpan (banked) untuk digunakan pada jenjang berikutnya
            </p>
          </div>
        </div>

        {/* Summary Cards */}
        <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
          <Card>
            <CardContent className="p-6">
              <div className="flex items-center justify-between">
                <div>
                  <p className="text-sm font-medium text-gray-600">Total Kredit Di-Bank</p>
                  <p className="text-3xl font-bold mt-2 text-yellow-600">
                    {formatNumber(bankedCredits?.data.filter(c => c.status === 'banked')
                      .reduce((sum, c) => sum + Number(c.credit_amount), 0) || 0)}
                  </p>
                  <p className="text-xs text-gray-500 mt-1">
                    {bankedCredits?.data.filter(c => c.status === 'banked').length || 0} aktivitas
                  </p>
                </div>
                <div className="p-3 rounded-full bg-yellow-50">
                  <Lock className="h-6 w-6 text-yellow-600" />
                </div>
              </div>
            </CardContent>
          </Card>

          <Card>
            <CardContent className="p-6">
              <div className="flex items-center justify-between">
                <div>
                  <p className="text-sm font-medium text-gray-600">Total Kredit Unlocked</p>
                  <p className="text-3xl font-bold mt-2 text-green-600">
                    {formatNumber(bankedCredits?.data.filter(c => c.status === 'unlocked')
                      .reduce((sum, c) => sum + Number(c.credit_amount), 0) || 0)}
                  </p>
                  <p className="text-xs text-gray-500 mt-1">
                    {bankedCredits?.data.filter(c => c.status === 'unlocked').length || 0} aktivitas
                  </p>
                </div>
                <div className="p-3 rounded-full bg-green-50">
                  <Unlock className="h-6 w-6 text-green-600" />
                </div>
              </div>
            </CardContent>
          </Card>
        </div>

        {/* Filter */}
        <Card>
          <CardHeader>
            <div className="flex items-center justify-between">
              <CardTitle className="flex items-center gap-2">
                <Filter className="h-5 w-5" />
                Filter Status
              </CardTitle>
              <div className="flex gap-2">
                <Button
                  variant={filter === 'all' ? 'default' : 'outline'}
                  size="sm"
                  onClick={() => { setFilter('all'); setCurrentPage(1); }}
                >
                  Semua
                </Button>
                <Button
                  variant={filter === 'banked' ? 'default' : 'outline'}
                  size="sm"
                  onClick={() => { setFilter('banked'); setCurrentPage(1); }}
                >
                  Di-Bank
                </Button>
                <Button
                  variant={filter === 'unlocked' ? 'default' : 'outline'}
                  size="sm"
                  onClick={() => { setFilter('unlocked'); setCurrentPage(1); }}
                >
                  Unlocked
                </Button>
              </div>
            </div>
          </CardHeader>
        </Card>

        {/* Banked Credits List */}
        <Card>
          <CardHeader>
            <CardTitle>Daftar Kredit yang Di-Bank</CardTitle>
            <CardDescription>
              Kredit ini akan otomatis unlock saat Anda naik ke jenjang berikutnya
            </CardDescription>
          </CardHeader>
          <CardContent>
            {!bankedCredits || bankedCredits.data.length === 0 ? (
              <div className="text-center py-8">
                <AlertCircle className="h-12 w-12 text-gray-400 mx-auto mb-3" />
                <p className="text-gray-500">Tidak ada kredit yang di-bank</p>
              </div>
            ) : (
              <div className="space-y-4">
                {bankedCredits.data.map((credit) => (
                  <div
                    key={credit.id}
                    className={`p-4 border rounded-lg ${
                      credit.status === 'banked'
                        ? 'border-yellow-200 bg-yellow-50'
                        : 'border-green-200 bg-green-50'
                    }`}
                  >
                    <div className="flex items-start justify-between">
                      <div className="flex-1">
                        {/* Activity Info */}
                        <div className="flex items-center gap-2 mb-2">
                          {credit.status === 'banked' ? (
                            <Lock className="h-4 w-4 text-yellow-600" />
                          ) : (
                            <Unlock className="h-4 w-4 text-green-600" />
                          )}
                          <h3 className="font-semibold text-gray-900">
                            {credit.activity?.title || 'Aktivitas'}
                          </h3>
                        </div>

                        {/* Credit Amount */}
                        <div className="flex items-center gap-4 mb-3">
                          <div className="flex items-center gap-2">
                            <Award className="h-4 w-4 text-gray-500" />
                            <span className="text-lg font-bold text-primary">
                              {formatNumber(credit.credit_amount)} kredit
                            </span>
                          </div>
                          <span
                            className={`px-2 py-1 text-xs font-medium rounded-full ${
                              credit.status === 'banked'
                                ? 'bg-yellow-200 text-yellow-800'
                                : 'bg-green-200 text-green-800'
                            }`}
                          >
                            {credit.status === 'banked' ? 'Di-Bank' : 'Unlocked'}
                          </span>
                        </div>

                        {/* Reason */}
                        <div className={`inline-flex items-center gap-2 px-3 py-1 rounded-full text-sm ${getReasonBadge(credit.reason)}`}>
                          <AlertCircle className="h-3 w-3" />
                          <span>{credit.reason}</span>
                        </div>

                        {/* Position when banked */}
                        <div className="mt-3 flex items-center gap-4 text-xs text-gray-600">
                          <span>
                            Jenjang saat di-bank: <strong>{credit.jenjang_when_banked}</strong>
                          </span>
                          <span>
                            Golongan: <strong>{credit.golongan_when_banked}</strong>
                          </span>
                        </div>

                        {/* Dates */}
                        <div className="mt-2 flex items-center gap-2 text-xs text-gray-500">
                          <Calendar className="h-3 w-3" />
                          <span>Di-bank pada: {formatDate(credit.created_at)}</span>
                        </div>

                        {credit.status === 'unlocked' && credit.unlocked_at && (
                          <div className="mt-1 flex items-center gap-2 text-xs text-green-600">
                            <Unlock className="h-3 w-3" />
                            <span>Unlock pada: {formatDate(credit.unlocked_at)}</span>
                            {credit.unlock_reason && (
                              <span className="ml-2 text-gray-600">({credit.unlock_reason})</span>
                            )}
                          </div>
                        )}
                      </div>
                    </div>
                  </div>
                ))}

                {/* Pagination */}
                {bankedCredits && bankedCredits.last_page > 1 && (
                  <div className="flex items-center justify-between pt-4 border-t">
                    <div className="text-sm text-gray-600">
                      Menampilkan {bankedCredits.from} - {bankedCredits.to} dari {bankedCredits.total} item
                    </div>
                    <div className="flex gap-2">
                      <Button
                        variant="outline"
                        size="sm"
                        onClick={() => setCurrentPage(p => Math.max(1, p - 1))}
                        disabled={bankedCredits.current_page === 1}
                      >
                        <ChevronLeft className="h-4 w-4" />
                        Sebelumnya
                      </Button>
                      <Button
                        variant="outline"
                        size="sm"
                        onClick={() => setCurrentPage(p => p + 1)}
                        disabled={bankedCredits.current_page === bankedCredits.last_page}
                      >
                        Selanjutnya
                        <ChevronRight className="h-4 w-4" />
                      </Button>
                    </div>
                  </div>
                )}
              </div>
            )}
          </CardContent>
        </Card>

        {/* Info Card */}
        <Card className="bg-blue-50 border-blue-200">
          <CardContent className="p-4">
            <div className="flex items-start gap-3">
              <AlertCircle className="h-5 w-5 text-blue-600 mt-0.5" />
              <div className="text-sm text-blue-900">
                <p className="font-semibold mb-1">Informasi Penting</p>
                <ul className="list-disc list-inside space-y-1 text-blue-800">
                  <li>Kredit di-bank secara otomatis saat melanggar ketentuan compliance (80/20)</li>
                  <li>Kredit akan otomatis unlock saat Anda naik ke jenjang berikutnya</li>
                  <li>Kredit yang di-bank tetap dihitung sebagai prestasi Anda</li>
                  <li>Kredit unlocked dapat langsung digunakan untuk perhitungan promosi</li>
                </ul>
              </div>
            </div>
          </CardContent>
        </Card>
      </div>
    </Layout>
  )
}
