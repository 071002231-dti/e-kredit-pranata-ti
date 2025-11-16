import api from '../lib/api'
import type { Activity, PaginatedResponse } from '../types'

export const approvalService = {
  async getPendingActivities(page: number = 1): Promise<PaginatedResponse<Activity>> {
    const { data } = await api.get<PaginatedResponse<Activity>>(
      '/approvals/pending',
      { params: { page } }
    )
    return data
  },

  async approveActivity(
    activityId: number,
    notes?: string
  ): Promise<{ message: string }> {
    const { data } = await api.post<{ message: string }>(
      `/approvals/${activityId}/approve`,
      { notes }
    )
    return data
  },

  async rejectActivity(
    activityId: number,
    notes: string
  ): Promise<{ message: string }> {
    const { data } = await api.post<{ message: string }>(
      `/approvals/${activityId}/reject`,
      { notes }
    )
    return data
  },
}
