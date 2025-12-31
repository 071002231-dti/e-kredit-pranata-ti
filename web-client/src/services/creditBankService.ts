import api from '../lib/api'
import type { CreditBank, CreditBankSummary, PaginatedResponse } from '../types'

export const creditBankService = {
  /**
   * Get list of banked credits with optional filters
   */
  async getAll(params?: {
    status?: 'banked' | 'unlocked'
    from_date?: string
    to_date?: string
    page?: number
  }): Promise<PaginatedResponse<CreditBank>> {
    const response = await api.get('/credit-banks', { params })
    return response.data
  },

  /**
   * Get summary of banked credits
   */
  async getSummary(): Promise<CreditBankSummary> {
    const response = await api.get('/credit-banks/summary')
    return response.data
  },

  /**
   * Get statistics about banked credits
   */
  async getStats(): Promise<{
    total_banked: number
    total_unlocked: number
    count_banked: number
    count_unlocked: number
    latest_banked?: CreditBank
    reasons_breakdown: Array<{
      reason: string
      count: number
      total_credits: number
    }>
  }> {
    const response = await api.get('/credit-banks/stats')
    return response.data
  },

  /**
   * Get details of a specific banked credit
   */
  async getById(id: number): Promise<CreditBank> {
    const response = await api.get(`/credit-banks/${id}`)
    return response.data
  },

  /**
   * Manually unlock a banked credit (admin only)
   */
  async unlock(id: number, reason: string): Promise<{ message: string; banked_credit: CreditBank }> {
    const response = await api.post(`/credit-banks/${id}/unlock`, { reason })
    return response.data
  },
}
