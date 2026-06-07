<template>
  <a-layout class="parent-layout">
    <!-- Header -->
    <a-layout-header class="parent-header">
      <div class="header-container">
        <div class="header-logo">
          <AppLogo :size="40" to="/parent" title="МТМУ" subtitle="Қабули хонандагон" />
        </div>

        <div class="header-nav">
          <a-space :size="16">
            <a-button type="link" @click="$router.push('/parent')">
              Аризаҳои ман
            </a-button>
            <a-button type="primary" @click="$router.push('/parent/new-application')">
              Ариза додан
            </a-button>
            <a-dropdown>
              <a-avatar :style="{ backgroundColor: '#52c41a', cursor: 'pointer' }">
                {{ authStore.user?.name?.charAt(0) }}
              </a-avatar>
              <template #overlay>
                <a-menu>
                  <a-menu-item disabled>
                    {{ authStore.user?.name }}
                  </a-menu-item>
                  <a-menu-item disabled>
                    {{ authStore.user?.phone }}
                  </a-menu-item>
                  <a-menu-divider />
                  <a-menu-item @click="handleLogout">
                    <LogoutOutlined /> Баромад
                  </a-menu-item>
                </a-menu>
              </template>
            </a-dropdown>
          </a-space>
        </div>
      </div>
    </a-layout-header>

    <!-- Content -->
    <a-layout-content class="parent-content">
      <router-view v-slot="{ Component }">
        <transition name="slide-up" mode="out-in">
          <component :is="Component" />
        </transition>
      </router-view>
    </a-layout-content>

    <!-- Footer -->
    <a-layout-footer class="parent-footer">
      <p>Вазорати маориф ва илми Ҷумҳурии Тоҷикистон &copy; {{ new Date().getFullYear() }}</p>
    </a-layout-footer>
  </a-layout>
</template>

<script setup lang="ts">
import { useRouter } from 'vue-router'
import { useAuthStore } from '@/stores/auth'
import AppLogo from '@/components/common/AppLogo.vue'
import { LogoutOutlined } from '@ant-design/icons-vue'

const router = useRouter()
const authStore = useAuthStore()

async function handleLogout() {
  await authStore.logout()
  router.push('/login')
}
</script>

<style scoped>
.parent-layout {
  min-height: 100vh;
}

.parent-header {
  background: white;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
  position: sticky;
  top: 0;
  z-index: 100;
  padding: 0 24px;
}

.header-container {
  max-width: 1200px;
  margin: 0 auto;
  display: flex;
  justify-content: space-between;
  align-items: center;
  height: 64px;
}

.header-logo {
  display: inline-flex;
  align-items: center;
}

.parent-content {
  max-width: 1200px;
  margin: 24px auto;
  padding: 0 24px;
  width: 100%;
}

.parent-footer {
  text-align: center;
  background: #001529;
  color: rgba(255, 255, 255, 0.65);
  padding: 16px;
}

@media (max-width: 768px) {
  .parent-content {
    padding: 0 12px;
    margin: 12px auto;
  }
}
</style>
