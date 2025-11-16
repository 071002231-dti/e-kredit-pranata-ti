import api from '../config/api';
import { DashboardStats, CategorySummary } from '../types';

export const dashboardService = {
  async getStats(): Promise<DashboardStats> {
    const response = await api.get<DashboardStats>('/dashboard/stats');
    return response.data;
  },

  async getSummary(): Promise<CategorySummary[]> {
    const response = await api.get<CategorySummary[]>('/dashboard/summary');
    return response.data;
  },
};
