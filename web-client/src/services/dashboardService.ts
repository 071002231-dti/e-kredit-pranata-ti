import api from '../lib/api'
import type { DashboardStats, CategorySummary } from '../types'

export const dashboardService = {
  async getStats(): Promise<DashboardStats> {
    const { data } = await api.get<DashboardStats>('/dashboard/stats')
    return data
  },

  async getSummary(): Promise<CategorySummary[]> {
    const { data } = await api.get<CategorySummary[]>('/dashboard/summary')
    return data
  },
}
