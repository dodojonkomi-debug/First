import { createRouter, createWebHistory } from 'vue-router'
import type { RouteRecordRaw } from 'vue-router'
import { useAuthStore } from '@/stores/auth'

const routes: RouteRecordRaw[] = [
  // ===== ПУБЛИКӢ =====
  {
    path: '/',
    name: 'home',
    component: () => import('@/pages/public/HomePage.vue'),
  },
  {
    path: '/check-status',
    name: 'check-status',
    component: () => import('@/pages/public/CheckStatusPage.vue'),
  },
  {
    path: '/login',
    name: 'login-parent',
    component: () => import('@/pages/public/LoginParentPage.vue'),
    meta: { guest: true },
  },
  {
    path: '/register',
    name: 'register-parent',
    component: () => import('@/pages/public/RegisterParentPage.vue'),
    meta: { guest: true },
  },
  {
    path: '/admin/login',
    name: 'login-admin',
    component: () => import('@/pages/public/LoginAdminPage.vue'),
    meta: { guest: true },
  },

  // ===== ВОЛИДАЙН =====
  {
    path: '/parent',
    component: () => import('@/components/layout/ParentLayout.vue'),
    meta: { requiresAuth: true, role: 'parent' },
    children: [
      {
        path: '',
        name: 'parent-dashboard',
        component: () => import('@/pages/parent/DashboardPage.vue'),
      },
      {
        path: 'new-application',
        name: 'parent-new-application',
        component: () => import('@/pages/parent/NewApplicationPage.vue'),
      },
      {
        path: 'applications/:id',
        name: 'parent-application-detail',
        component: () => import('@/pages/parent/ApplicationDetailPage.vue'),
      },
    ],
  },

  // ===== МАЪМУРОН =====
  {
    path: '/admin',
    component: () => import('@/components/layout/AdminLayout.vue'),
    meta: { requiresAuth: true, adminOnly: true },
    children: [
      {
        path: '',
        name: 'admin-dashboard',
        component: () => import('@/pages/admin/DashboardPage.vue'),
      },
      {
        path: 'applications',
        name: 'admin-applications',
        component: () => import('@/pages/admin/ApplicationsPage.vue'),
      },
      {
        path: 'applications/:id',
        name: 'admin-application-detail',
        component: () => import('@/pages/admin/ApplicationDetailPage.vue'),
      },
      // Фақат суперадмин
      {
        path: 'regions',
        name: 'admin-regions',
        component: () => import('@/pages/admin/RegionsPage.vue'),
        meta: { role: 'superadmin' },
      },
      {
        path: 'districts',
        name: 'admin-districts',
        component: () => import('@/pages/admin/DistrictsPage.vue'),
        meta: { role: 'superadmin' },
      },
      {
        path: 'schools',
        name: 'admin-schools',
        component: () => import('@/pages/admin/SchoolsPage.vue'),
        meta: { role: 'superadmin' },
      },
      {
        path: 'users',
        name: 'admin-users',
        component: () => import('@/pages/admin/UsersPage.vue'),
        meta: { role: 'superadmin' },
      },
    ],
  },

  // 404
  {
    path: '/:pathMatch(.*)*',
    name: 'not-found',
    component: () => import('@/pages/public/NotFoundPage.vue'),
  },
]

const router = createRouter({
  history: createWebHistory(),
  routes,
})

// Навигация гард
router.beforeEach(async (to, _from, next) => {
  const authStore = useAuthStore()

  // Барқарор кардани сессия
  if (authStore.token && !authStore.user) {
    await authStore.restoreSession()
  }

  // Саҳифаи мунтазир
  if (to.meta.guest && authStore.isAuthenticated) {
    if (authStore.isParent) return next('/parent')
    return next('/admin')
  }

  // Саҳифаи маҳфуз
  if (to.meta.requiresAuth && !authStore.isAuthenticated) {
    return next('/login')
  }

  // Тафтиши нақш
  if (to.meta.adminOnly && authStore.isParent) {
    return next('/parent')
  }

  if (to.meta.role === 'parent' && !authStore.isParent) {
    return next('/admin')
  }

  if (to.meta.role === 'superadmin' && !authStore.isSuperAdmin) {
    return next('/admin')
  }

  next()
})

export default router
