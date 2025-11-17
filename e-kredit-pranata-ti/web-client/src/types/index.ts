export interface User {
  id: number
  nip: string
  name: string
  email: string
  role: 'user' | 'verifier' | 'admin'
  position: string
  unit_kerja: string
  jenjang_jabatan?: string
  created_at: string
  updated_at: string
}

export interface LoginCredentials {
  email: string
  password: string
}

export interface RegisterData {
  nip: string
  name: string
  email: string
  password: string
  password_confirmation: string
  position: string
  unit_kerja: string
}

export interface AuthResponse {
  message: string
  user: User
  token: string
}

export interface CreditSchema {
  id: number
  category: string
  subcategory: string
  activity_name: string
  credit_points: string
  satuan_hasil: string
  batasan_penilaian: string
  pelaksana: string
  bukti_fisik: string
  unsur_type: 'utama' | 'penunjang'
  description: string
  created_at: string
  updated_at: string
}

export interface Activity {
  id: number
  user_id: number
  schema_id: number
  title: string
  description: string
  proof_file?: string
  status: 'pending' | 'approved' | 'rejected'
  earned_points: string
  submitted_at: string
  reviewed_at?: string
  reviewer_notes?: string
  created_at: string
  updated_at: string
  schema?: CreditSchema
  user?: User
}

export interface Approval {
  id: number
  activity_id: number
  verifier_id: number
  status: 'approved' | 'rejected'
  notes?: string
  created_at: string
  updated_at: string
  activity?: Activity
  verifier?: User
}

export interface DashboardStats {
  total_activities: number
  pending: number
  approved: number
  rejected: number
  total_points: number
  utama_points: number
  penunjang_points: number
  utama_percentage: number
  penunjang_percentage: number
  is_compliant: boolean
  current_jenjang?: string
  current_golongan?: string
  target_angka_kredit?: number
  angka_kredit_minimal?: number
  progress_percentage: number
  compliance: {
    is_compliant: boolean
    current_utama_percentage: number
    current_penunjang_percentage: number
    required_utama_min: number
    required_penunjang_max: number
  }
  promotion: {
    can_promote: boolean
    next_jenjang: {
      jenjang: string
      golongan: string
      min_credits: number
      max_credits: number
    } | null
    credits_needed: number
  }
  recommendations: Array<{
    type: 'warning' | 'info' | 'success'
    message: string
    action?: string
  }>
  banked_credits: {
    total_banked: number
    total_unlocked: number
    count_banked: number
    count_unlocked: number
  }
  usable_credits: number
  total_banked_credits: number
}

export interface CategorySummary {
  category: string
  total_activities: number
  approved_count: number
  earned_points: number
  unsur_type: 'utama' | 'penunjang'
}

export interface PaginatedResponse<T> {
  current_page: number
  data: T[]
  first_page_url: string
  from: number
  last_page: number
  last_page_url: string
  links: {
    url: string | null
    label: string
    active: boolean
  }[]
  next_page_url: string | null
  path: string
  per_page: number
  prev_page_url: string | null
  to: number
  total: number
}

export interface CreditBank {
  id: number
  user_id: number
  activity_id: number
  credit_amount: number
  reason: string
  status: 'banked' | 'unlocked'
  jenjang_when_banked: string
  golongan_when_banked: string
  unlocked_at?: string
  unlock_reason?: string
  created_at: string
  updated_at: string
  activity?: Activity
  user?: User
}

export interface CreditBankSummary {
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
}

export interface ApiError {
  message: string
  errors?: Record<string, string[]>
}
