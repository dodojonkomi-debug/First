<template>
  <a-layout class="admin-layout">
    <!-- Sidebar -->
    <a-layout-sider
      v-model:collapsed="collapsed"
      :trigger="null"
      collapsible
      :width="260"
      theme="dark"
      class="sidebar"
      breakpoint="lg"
      @collapse="onCollapse"
    >
      <div class="logo">
        <AppLogo
          :size="36"
          :show-text="!collapsed"
          variant="light"
          title="МТМУ"
          subtitle="Қабули хонандагон"
        />
      </div>

      <a-menu
        v-model:selectedKeys="selectedKeys"
        theme="dark"
        mode="inline"
      >
        <a-menu-item key="dashboard" @click="$router.push('/admin')">
          <template #icon><DashboardOutlined /></template>
          <span>Панели асосӣ</span>
        </a-menu-item>

        <a-menu-item key="applications" @click="$router.push('/admin/applications')">
          <template #icon><FileTextOutlined /></template>
          <span>Аризаҳо</span>
        </a-menu-item>

        <template v-if="authStore.isSuperAdmin">
          <a-menu-divider />
          <a-menu-item-group title="Идоракунӣ">
            <a-menu-item key="regions" @click="$router.push('/admin/regions')">
              <template #icon><GlobalOutlined /></template>
              <span>Вилоятҳо</span>
            </a-menu-item>

            <a-menu-item key="districts" @click="$router.push('/admin/districts')">
              <template #icon><EnvironmentOutlined /></template>
              <span>Ноҳияҳо</span>
            </a-menu-item>

            <a-menu-item key="schools" @click="$router.push('/admin/schools')">
              <template #icon><BankOutlined /></template>
              <span>Мактабҳо</span>
            </a-menu-item>

            <a-menu-item key="users" @click="$router.push('/admin/users')">
              <template #icon><TeamOutlined /></template>
              <span>Корбарон</span>
            </a-menu-item>
          </a-menu-item-group>
        </template>
      </a-menu>
    </a-layout-sider>

    <!-- Контент -->
    <a-layout :style="{ marginLeft: collapsed ? '80px' : '260px', transition: 'margin-left 0.2s' }">
      <!-- Header -->
      <a-layout-header class="header">
        <div class="header-left">
          <MenuUnfoldOutlined
            v-if="collapsed"
            class="trigger"
            @click="collapsed = false"
          />
          <MenuFoldOutlined
            v-else
            class="trigger"
            @click="collapsed = true"
          />
        </div>

        <div class="header-right">
          <a-dropdown>
            <a-space>
              <a-avatar :style="{ backgroundColor: '#1890ff' }">
                {{ authStore.user?.name?.charAt(0) }}
              </a-avatar>
              <span class="user-name">{{ authStore.user?.name }}</span>
            </a-space>
            <template #overlay>
              <a-menu>
                <a-menu-item key="role">
                  <TagOutlined /> {{ roleLabel }}
                </a-menu-item>
                <a-menu-divider />
                <a-menu-item key="logout" @click="handleLogout">
                  <LogoutOutlined /> Баромад
                </a-menu-item>
              </a-menu>
            </template>
          </a-dropdown>
        </div>
      </a-layout-header>

      <!-- Page content -->
      <a-layout-content class="content">
        <router-view v-slot="{ Component }">
          <transition name="fade" mode="out-in">
            <component :is="Component" />
          </transition>
        </router-view>
      </a-layout-content>
    </a-layout>
  </a-layout>
</template>

<script setup lang="ts">
import { ref, computed, watch } from 'vue'
import { useRouter, useRoute } from 'vue-router'
import { useAuthStore } from '@/stores/auth'
import AppLogo from '@/components/common/AppLogo.vue'
import {
  DashboardOutlined,
  FileTextOutlined,
  GlobalOutlined,
  EnvironmentOutlined,
  BankOutlined,
  TeamOutlined,
  MenuFoldOutlined,
  MenuUnfoldOutlined,
  LogoutOutlined,
  TagOutlined,
} from '@ant-design/icons-vue'

const router = useRouter()
const route = useRoute()
const authStore = useAuthStore()
const collapsed = ref(false)
const selectedKeys = ref<string[]>(['dashboard'])

const roleLabel = computed(() => {
  const labels: Record<string, string> = {
    superadmin: 'Суперадмин (Вазорат)',
    admin_region: 'Маъмури вилоят',
    admin_district: 'Маъмури ноҳия',
    admin_school: 'Маъмури мактаб',
  }
  return labels[authStore.user?.role || ''] || ''
})

// Синхронизацияи менюи интихобшуда бо маршрут
watch(() => route.path, (path) => {
  if (path.includes('applications')) selectedKeys.value = ['applications']
  else if (path.includes('regions')) selectedKeys.value = ['regions']
  else if (path.includes('districts')) selectedKeys.value = ['districts']
  else if (path.includes('schools')) selectedKeys.value = ['schools']
  else if (path.includes('users')) selectedKeys.value = ['users']
  else selectedKeys.value = ['dashboard']
}, { immediate: true })

function onCollapse(c: boolean) {
  collapsed.value = c
}

async function handleLogout() {
  await authStore.logout()
  router.push('/admin/login')
}
</script>

<style scoped>
.sidebar {
  overflow: auto;
  height: 100vh;
  position: fixed;
  left: 0;
  top: 0;
  bottom: 0;
  z-index: 100;
}

.logo {
  height: 64px;
  display: flex;
  align-items: center;
  justify-content: center;
  padding: 0 16px;
  border-bottom: 1px solid rgba(255, 255, 255, 0.12);
}

.header {
  background: white;
  padding: 0 24px;
  display: flex;
  justify-content: space-between;
  align-items: center;
  box-shadow: 0 1px 4px rgba(0, 0, 0, 0.08);
  position: sticky;
  top: 0;
  z-index: 99;
}

.header-left {
  display: flex;
  align-items: center;
}

.trigger {
  font-size: 20px;
  cursor: pointer;
  padding: 8px;
  border-radius: 4px;
  transition: background 0.2s;
}

.trigger:hover {
  background: #f0f0f0;
}

.header-right {
  display: flex;
  align-items: center;
  cursor: pointer;
}

.user-name {
  font-size: 14px;
  color: #333;
}

.content {
  margin: 24px;
  padding: 24px;
  background: white;
  border-radius: 8px;
  min-height: calc(100vh - 112px);
}
</style>
