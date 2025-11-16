export interface User {
  id: number;
  nip: string;
  name: string;
  email: string;
  role: 'user' | 'verifier' | 'admin';
  position?: string;
  unit_kerja?: string;
  jenjang_jabatan?: string;
  golongan?: string;
  target_angka_kredit?: string;
  angka_kredit_minimal?: string;
  created_at: string;
  updated_at: string;
}

export interface CreditSchema {
  id: number;
  category: string;
  subcategory?: string;
  activity_name: string;
  credit_points: string;
  description?: string;
  satuan_hasil?: string;
  batasan_penilaian?: string;
  pelaksana?: string;
  bukti_fisik?: string;
  unsur_type?: 'utama' | 'penunjang';
  created_at: string;
  updated_at: string;
}

export interface Activity {
  id: number;
  user_id: number;
  schema_id: number;
  title: string;
  description: string;
  proof_file?: string;
  status: 'pending' | 'approved' | 'rejected';
  submitted_at: string;
  created_at: string;
  updated_at: string;
  credit_schema?: CreditSchema;
  user?: User;
  approvals?: Approval[];
  latest_approval?: Approval;
}

export interface Approval {
  id: number;
  activity_id: number;
  verifier_id: number;
  status: 'approved' | 'rejected';
  comments?: string;
  approved_at: string;
  created_at: string;
  updated_at: string;
  verifier?: User;
  activity?: Activity;
}

export interface DashboardStats {
  total_activities: number;
  pending: number;
  approved: number;
  rejected: number;
  total_points: number;
  utama_points: number;
  penunjang_points: number;
  utama_percentage: number;
  penunjang_percentage: number;
  is_compliant: boolean;
  target_angka_kredit: string;
  angka_kredit_minimal: string;
  progress_percentage: number;
}

export interface CategorySummary {
  category: string;
  total_activities: number;
  approved_count: number;
  earned_points: number;
}

export interface LoginRequest {
  email: string;
  password: string;
}

export interface RegisterRequest {
  nip: string;
  name: string;
  email: string;
  password: string;
  password_confirmation: string;
  position?: string;
  unit_kerja?: string;
}

export interface AuthResponse {
  message: string;
  user: User;
  token: string;
}

export interface PaginatedResponse<T> {
  data: T[];
  current_page: number;
  last_page: number;
  per_page: number;
  total: number;
}

export interface ComplianceValidation {
  current_total: number;
  new_total: number;
  current_utama_percentage: number;
  new_utama_percentage: number;
  current_penunjang_percentage: number;
  new_penunjang_percentage: number;
  would_be_compliant: boolean;
  warning?: string;
}
