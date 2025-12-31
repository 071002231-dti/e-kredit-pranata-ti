import api from '../lib/api'
import type { Activity, PaginatedResponse } from '../types'

export const activityService = {
  async getActivities(
    page: number = 1,
    params?: {
      status?: string
      search?: string
      per_page?: number
    }
  ): Promise<PaginatedResponse<Activity>> {
    const { data } = await api.get<PaginatedResponse<Activity>>('/activities', {
      params: { page, ...params },
    })
    return data
  },

  async getActivity(id: number): Promise<Activity> {
    const { data } = await api.get<{ data: Activity }>(`/activities/${id}`)
    return data.data
  },

  async createActivity(formData: FormData): Promise<any> {
    const { data } = await api.post('/activities', formData, {
      headers: {
        'Content-Type': 'multipart/form-data',
      },
    })
    return data  // Return whole response (includes activity, banking_info, message)
  },

  async updateActivity(id: number, formData: FormData): Promise<Activity> {
    const { data } = await api.post<{ data: Activity }>(
      `/activities/${id}`,
      formData,
      {
        headers: {
          'Content-Type': 'multipart/form-data',
        },
      }
    )
    return data.data
  },

  async deleteActivity(id: number): Promise<void> {
    await api.delete(`/activities/${id}`)
  },
}
