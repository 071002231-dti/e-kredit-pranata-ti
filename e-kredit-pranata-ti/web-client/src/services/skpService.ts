import api from '../lib/api'

export interface Skp {
  id: number
  user_id: number
  tahun: number
  target_angka_kredit: number
  tugas_tambahan?: string
  status: 'draft' | 'submitted' | 'approved'
  approved_by?: number
  created_at: string
  updated_at: string
  items?: SkpItem[]
  approver?: {
    id: number
    name: string
  }
}

export interface SkpItem {
  id: number
  skp_id: number
  schema_id: number
  target_quantity: number
  target_points: number
  realisasi_quantity: number
  realisasi_points: number
  creditSchema?: {
    id: number
    activity_name: string
    credit_points: number
    category: string
    subcategory: string
    satuan_hasil: string
  }
}

export interface SkpStats {
  has_skp: boolean
  skp?: Skp
  stats?: {
    target_total: number
    realisasi_total: number
    progress_percentage: number
    items_count: number
    status: string
  }
}

export interface SkpSummary {
  skp: Skp
  summary: {
    target_total: number
    realisasi_total: number
    progress_percentage: number
  }
}

export interface PaginatedSkp {
  current_page: number
  data: Skp[]
  total: number
  per_page: number
  last_page: number
}

export const skpService = {
  // Get list of SKPs
  async getSkps(page = 1): Promise<PaginatedSkp> {
    const { data } = await api.get<PaginatedSkp>(`/skp?page=${page}`)
    return data
  },

  // Get SKP by ID
  async getSkp(id: number): Promise<SkpSummary> {
    const { data } = await api.get<SkpSummary>(`/skp/${id}`)
    return data
  },

  // Create new SKP
  async createSkp(skpData: {
    tahun: number
    target_angka_kredit: number
    tugas_tambahan?: string
  }): Promise<{ message: string; skp: Skp }> {
    const { data } = await api.post('/skp', skpData)
    return data
  },

  // Update SKP
  async updateSkp(
    id: number,
    updates: {
      target_angka_kredit?: number
      tugas_tambahan?: string
    }
  ): Promise<{ message: string; skp: Skp }> {
    const { data } = await api.put(`/skp/${id}`, updates)
    return data
  },

  // Delete SKP
  async deleteSkp(id: number): Promise<{ message: string }> {
    const { data } = await api.delete(`/skp/${id}`)
    return data
  },

  // Submit SKP for approval
  async submitSkp(id: number): Promise<{ message: string; skp: Skp }> {
    const { data } = await api.post(`/skp/${id}/submit`)
    return data
  },

  // Add item to SKP
  async addItem(
    skpId: number,
    itemData: {
      schema_id: number
      target_quantity: number
    }
  ): Promise<{ message: string; item: SkpItem }> {
    const { data } = await api.post(`/skp/${skpId}/items`, itemData)
    return data
  },

  // Update SKP item
  async updateItem(
    skpId: number,
    itemId: number,
    updates: {
      target_quantity?: number
      realisasi_quantity?: number
    }
  ): Promise<{ message: string; item: SkpItem }> {
    const { data } = await api.put(`/skp/${skpId}/items/${itemId}`, updates)
    return data
  },

  // Remove item from SKP
  async removeItem(skpId: number, itemId: number): Promise<{ message: string }> {
    const { data } = await api.delete(`/skp/${skpId}/items/${itemId}`)
    return data
  },

  // Get SKP stats by year
  async getStats(tahun?: number): Promise<SkpStats> {
    const year = tahun || new Date().getFullYear()
    const { data } = await api.get<SkpStats>(`/skp/stats?tahun=${year}`)
    return data
  },
}
