import api from '../config/api';
import { CreditSchema } from '../types';

export const creditSchemaService = {
  async getSchemas(category?: string, paginate: boolean = true): Promise<CreditSchema[]> {
    let url = `/credit-schema?paginate=${paginate ? 'true' : 'false'}`;
    if (category) {
      url += `&category=${encodeURIComponent(category)}`;
    }
    const response = await api.get<any>(url);
    // If paginated, response.data has a 'data' property with the array
    // If not paginated, response.data is the array directly
    return paginate ? response.data.data : response.data;
  },

  async getSchema(id: number): Promise<CreditSchema> {
    const response = await api.get<CreditSchema>(`/credit-schema/${id}`);
    return response.data;
  },

  async getCategories(): Promise<string[]> {
    const response = await api.get<string[]>('/credit-schema/categories');
    return response.data;
  },
};
