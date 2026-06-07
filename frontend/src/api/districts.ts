import apiClient from './client'
import type { District } from '@/types'

export const districtsApi = {
  getAll(regionId?: number) {
    const params = regionId ? { region_id: regionId } : {}
    return apiClient.get<{ data: District[] }>('/public/districts', { params })
  },

  getById(id: number) {
    return apiClient.get<{ data: District }>(`/districts/${id}`)
  },

  create(data: Partial<District>) {
    return apiClient.post<{ data: District }>('/districts', data)
  },

  update(id: number, data: Partial<District>) {
    return apiClient.put<{ data: District }>(`/districts/${id}`, data)
  },

  delete(id: number) {
    return apiClient.delete(`/districts/${id}`)
  },
}
