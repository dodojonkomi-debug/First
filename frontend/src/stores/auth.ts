import { defineStore } from 'pinia'
import { ref, computed } from 'vue'
import type { User, LoginAdminForm, LoginParentForm, RegisterParentForm } from '@/types'
import { authApi } from '@/api/auth'

export const useAuthStore = defineStore('auth', () => {
  const user = ref<User | null>(null)
  const token = ref<string | null>(localStorage.getItem('auth_token'))
  const loading = ref(false)

  const isAuthenticated = computed(() => !!token.value && !!user.value)
  const userRole = computed(() => user.value?.role || null)
  const isSuperAdmin = computed(() => user.value?.role === 'superadmin')
  const isAdmin = computed(() => ['superadmin', 'admin_region', 'admin_district', 'admin_school'].includes(user.value?.role || ''))
  const isParent = computed(() => user.value?.role === 'parent')

  // Барқарор кардани сессия
  async function restoreSession() {
    if (!token.value) return false
    try {
      loading.value = true
      const res = await authApi.me()
      user.value = res.data.user
      return true
    } catch {
      logout()
      return false
    } finally {
      loading.value = false
    }
  }

  // Вуруди маъмур
  async function loginAdmin(form: LoginAdminForm) {
    loading.value = true
    try {
      const res = await authApi.loginAdmin(form)
      token.value = res.data.token
      user.value = res.data.user
      localStorage.setItem('auth_token', res.data.token)
      return res.data
    } finally {
      loading.value = false
    }
  }

  // Вуруди волидайн
  async function loginParent(form: LoginParentForm) {
    loading.value = true
    try {
      const res = await authApi.loginParent(form)
      token.value = res.data.token
      user.value = res.data.user
      localStorage.setItem('auth_token', res.data.token)
      return res.data
    } finally {
      loading.value = false
    }
  }

  // Бақайдгирии волидайн
  async function registerParent(form: RegisterParentForm) {
    loading.value = true
    try {
      const res = await authApi.registerParent(form)
      token.value = res.data.token
      user.value = res.data.user
      localStorage.setItem('auth_token', res.data.token)
      return res.data
    } finally {
      loading.value = false
    }
  }

  // Баромад
  async function logout() {
    try {
      if (token.value) await authApi.logout()
    } catch { /* ignore */ }
    token.value = null
    user.value = null
    localStorage.removeItem('auth_token')
  }

  return {
    user, token, loading,
    isAuthenticated, userRole, isSuperAdmin, isAdmin, isParent,
    restoreSession, loginAdmin, loginParent, registerParent, logout,
  }
})
