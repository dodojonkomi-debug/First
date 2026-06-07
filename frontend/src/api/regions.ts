import apiClient from './client'
import type { Region } from '@/types'

export const regionsApi = {
  getAll() {
    return apiClient.get<{ data: Region[] }>('/public/regions')
  },

  getById(id: number) {
    return apiClient.get<{ data: Region }>(`/regions/${id}`)
  },

  create(data: Partial<Region>) {
    return apiClient.post<{ data: Region }>('/regions', data)
  },

  update(id: number, data: Partial<Region>) {
    return apiClient.put<{ data: Region }>(`/regions/${id}`, data)
  },

  delete(id: number) {
    return apiClient.delete(`/regions/${id}`)
  },
}
