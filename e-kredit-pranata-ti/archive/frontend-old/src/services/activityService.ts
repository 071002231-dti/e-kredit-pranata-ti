import api from '../config/api';
import { Activity, PaginatedResponse } from '../types';

export const activityService = {
  async getActivities(page: number = 1): Promise<PaginatedResponse<Activity>> {
    const response = await api.get<PaginatedResponse<Activity>>(`/activities?page=${page}`);
    return response.data;
  },

  async getActivity(id: number): Promise<Activity> {
    const response = await api.get<Activity>(`/activities/${id}`);
    return response.data;
  },

  async createActivity(data: FormData): Promise<{ message: string; activity: Activity }> {
    const response = await api.post('/activities', data, {
      headers: {
        'Content-Type': 'multipart/form-data',
      },
    });
    return response.data;
  },

  async updateActivity(id: number, data: Partial<Activity>): Promise<{ message: string; activity: Activity }> {
    const response = await api.put(`/activities/${id}`, data);
    return response.data;
  },

  async deleteActivity(id: number): Promise<{ message: string }> {
    const response = await api.delete(`/activities/${id}`);
    return response.data;
  },
};
