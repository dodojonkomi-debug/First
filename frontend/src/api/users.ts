import apiClient from './client'
import type { User, PaginatedResponse } from '@/types'

export const usersApi = {
  getAll(params?: { role?: string; region_id?: number; district_id?: number; school_id?: number; page?: number }) {
    return apiClient.get<{ data: PaginatedResponse<User> }>('/users', { params })
  },

  getById(id: number) {
    return apiClient.get<{ data: User }>(`/users/${id}`)
  },

  create(data: Partial<User> & { password: string }) {
    return apiClient.post<{ data: User }>('/users', data)
  },

  update(id: number, data: Partial<User> & { password?: string }) {
    return apiClient.put<{ data: User }>(`/users/${id}`, data)
  },

  delete(id: number) {
    return apiClient.delete(`/users/${id}`)
  },
}
