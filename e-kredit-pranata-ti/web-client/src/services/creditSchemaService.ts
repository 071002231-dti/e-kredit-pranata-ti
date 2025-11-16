import api from '../lib/api'
import type { CreditSchema, PaginatedResponse } from '../types'

export const creditSchemaService = {
  async getCreditSchemas(params?: {
    category?: string
    subcategory?: string
    unsur_type?: string
    jenjang?: string
    search?: string
    page?: number
    per_page?: number
  }): Promise<PaginatedResponse<CreditSchema>> {
    const { data } = await api.get<PaginatedResponse<CreditSchema>>(
      '/credit-schema',
      { params }
    )
    return data
  },

  async getCreditSchema(id: number): Promise<CreditSchema> {
    const { data } = await api.get<{ data: CreditSchema }>(`/credit-schema/${id}`)
    return data.data
  },

  async getCategories(): Promise<string[]> {
    const { data } = await api.get<{ categories: string[] }>(
      '/credit-schema/categories'
    )
    return data.categories
  },

  async getSubcategories(category: string): Promise<string[]> {
    const { data } = await api.get<{ subcategories: string[] }>(
      '/credit-schema/subcategories',
      { params: { category } }
    )
    return data.subcategories
  },
}
