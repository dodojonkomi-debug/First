import apiClient from './client'
import type { AuthResponse, LoginAdminForm, LoginParentForm, RegisterParentForm, User } from '@/types'

export const authApi = {
  loginAdmin(data: LoginAdminForm) {
    return apiClient.post<AuthResponse>('/auth/login-admin', data)
  },

  loginParent(data: LoginParentForm) {
    return apiClient.post<AuthResponse>('/auth/login-parent', data)
  },

  registerParent(data: RegisterParentForm) {
    return apiClient.post<AuthResponse>('/auth/register-parent', data)
  },

  logout() {
    return apiClient.post('/auth/logout')
  },

  me() {
    return apiClient.get<{ user: User }>('/auth/me')
  },
}
