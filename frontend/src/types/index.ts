// ===== НАВЪҲОИ АСОСӢ =====

export type UserRole = 'superadmin' | 'admin_region' | 'admin_district' | 'admin_school' | 'parent'

export type ApplicationStatus = 'pending' | 'review' | 'approved' | 'rejected'

export type Gender = 'male' | 'female'

export type Grade = '0' | '1'

export type DocumentType =
  | 'birth_certificate'
  | 'parent_id'
  | 'medical_form'
  | 'vaccination_card'
  | 'residence_certificate'

// ===== МОДЕЛҲО =====

export interface Region {
  id: number
  name: string
  code: string
  is_active: boolean
  districts_count?: number
  districts?: District[]
  created_at: string
  updated_at: string
}

export interface District {
  id: number
  region_id: number
  name: string
  code: string
  is_active: boolean
  region?: Region
  schools_count?: number
  schools?: School[]
  created_at: string
  updated_at: string
}

export interface School {
  id: number
  district_id: number
  name: string
  code: string
  address: string | null
  phone: string | null
  capacity_class_0: number
  capacity_class_1: number
  is_active: boolean
  district?: District
  applications_count?: number
  created_at: string
  updated_at: string
}

export interface User {
  id: number
  name: string
  email: string
  phone: string | null
  role: UserRole
  region_id: number | null
  district_id: number | null
  school_id: number | null
  is_active: boolean
  region?: Region
  district?: District
  school?: School
  created_at: string
  updated_at: string
}

export interface Application {
  id: number
  application_code: string
  user_id: number
  school_id: number
  child_first_name: string
  child_last_name: string
  child_middle_name: string | null
  child_birth_date: string
  child_gender: Gender
  grade: Grade
  parent_first_name: string
  parent_last_name: string
  parent_id_number: string
  parent_phone: string
  parent_email: string | null
  residence_region_id: number
  residence_district_id: number
  residence_address: string
  status: ApplicationStatus
  rejection_reason: string | null
  reviewed_at: string | null
  reviewed_by: number | null
  has_birth_certificate: boolean
  has_parent_id: boolean
  has_medical_form: boolean
  has_vaccination_card: boolean
  has_residence_certificate: boolean
  school?: School
  user?: User
  documents?: Document[]
  reviewer?: User
  created_at: string
  updated_at: string
}

export interface Document {
  id: number
  application_id: number
  type: DocumentType
  original_name: string
  file_path: string
  mime_type: string
  file_size: number
  created_at: string
  updated_at: string
}

// ===== ФОРМАҲО =====

export interface LoginAdminForm {
  email: string
  password: string
}

export interface LoginParentForm {
  phone: string
  password: string
}

export interface RegisterParentForm {
  name: string
  phone: string
  email?: string
  password: string
  password_confirmation: string
}

export interface ApplicationForm {
  school_id: number | null
  child_first_name: string
  child_last_name: string
  child_middle_name: string
  child_birth_date: string
  child_gender: Gender | ''
  grade: Grade | ''
  parent_first_name: string
  parent_last_name: string
  parent_id_number: string
  parent_phone: string
  parent_email: string
  residence_region_id: number | null
  residence_district_id: number | null
  residence_address: string
}

// ===== ҶАВОБҲО =====

export interface AuthResponse {
  user: User
  token: string
}

export interface PaginatedResponse<T> {
  data: T[]
  current_page: number
  last_page: number
  per_page: number
  total: number
}

export interface Statistics {
  total: number
  pending: number
  review: number
  approved: number
  rejected: number
}

export interface StatusCheckResult {
  application_code: string
  child_name: string
  school: string
  grade: Grade
  status: ApplicationStatus
  rejection_reason: string | null
  submitted_at: string
  reviewed_at: string | null
}
