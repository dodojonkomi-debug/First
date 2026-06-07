import apiClient from './client'
import type { Application, ApplicationForm, PaginatedResponse, Statistics, StatusCheckResult } from '@/types'

export const applicationsApi = {
  getAll(params?: { status?: string; search?: string; page?: number }) {
    return apiClient.get<PaginatedResponse<Application>>('/applications', { params })
  },

  getById(id: number) {
    return apiClient.get<{ data: Application }>(`/applications/${id}`)
  },

  create(data: ApplicationForm) {
    return apiClient.post<{ data: Application; message: string; code: string }>('/applications', data)
  },

  updateStatus(id: number, data: { status: string; rejection_reason?: string }) {
    return apiClient.patch<{ data: Application; message: string }>(`/applications/${id}/status`, data)
  },

  uploadDocument(applicationId: number, type: string, file: File) {
    const formData = new FormData()
    formData.append('type', type)
    formData.append('file', file)
    return apiClient.post(`/applications/${applicationId}/documents`, formData, {
      headers: { 'Content-Type': 'multipart/form-data' },
    })
  },

  checkStatus(code: string) {
    return apiClient.post<{ data: StatusCheckResult }>('/applications/check-status', { code })
  },

  getStatistics() {
    return apiClient.get<{ data: Statistics }>('/applications/stats/overview')
  },
}
