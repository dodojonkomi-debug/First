import apiClient from './client'
import type { School } from '@/types'

export const schoolsApi = {
  getAll(params?: { district_id?: number; region_id?: number }) {
    return apiClient.get<{ data: School[] }>('/public/schools', { params })
  },

  getById(id: number) {
    return apiClient.get<{ data: School }>(`/schools/${id}`)
  },

  create(data: Partial<School>) {
    return apiClient.post<{ data: School }>('/schools', data)
  },

  update(id: number, data: Partial<School>) {
    return apiClient.put<{ data: School }>(`/schools/${id}`, data)
  },

  delete(id: number) {
    return apiClient.delete(`/schools/${id}`)
  },
}
