<template>
  <div class="admin-dashboard">
    <!-- Header хушомадгӯӣ -->
    <div class="dash-hero">
      <div class="dash-hero__text">
        <h2>Салом, {{ authStore.user?.name }} 👋</h2>
        <p>
          <span class="live-dot"></span>
          {{ roleLabel }} · {{ today }}
        </p>
      </div>
      <a-button type="primary" class="btn-shine" @click="$router.push('/admin/applications')">
        <template #icon><FileSearchOutlined /></template>
        Баррасии аризаҳо
      </a-button>
    </div>

    <a-spin :spinning="loading">
      <!-- Статистика -->
      <a-row :gutter="[16, 16]" class="stats-row">
        <a-col :xs="12" :sm="12" :md="6" v-reveal="{ delay: 0 }">
          <StatCard :value="stats.total" label="Ҳамагӣ аризаҳо" color="#1668DC" iconBg="rgba(22,104,220,0.10)">
            <template #icon><FileTextOutlined /></template>
          </StatCard>
        </a-col>
        <a-col :xs="12" :sm="12" :md="6" v-reveal="{ delay: 0.08 }">
          <StatCard :value="stats.pending + stats.review" label="Дар интизор/баррасӣ" color="#F59E0B" iconBg="rgba(245,158,11,0.12)">
            <template #icon><ClockCircleOutlined /></template>
          </StatCard>
        </a-col>
        <a-col :xs="12" :sm="12" :md="6" v-reveal="{ delay: 0.16 }">
          <StatCard :value="stats.approved" label="Қабулшуда" color="#16A34A" iconBg="rgba(22,163,74,0.12)">
            <template #icon><CheckCircleOutlined /></template>
          </StatCard>
        </a-col>
        <a-col :xs="12" :sm="12" :md="6" v-reveal="{ delay: 0.24 }">
          <StatCard :value="stats.rejected" label="Радшуда" color="#DC2626" iconBg="rgba(220,38,38,0.12)">
            <template #icon><CloseCircleOutlined /></template>
          </StatCard>
        </a-col>
      </a-row>

      <!-- Аризаҳои охирин -->
      <a-card title="Аризаҳои охирин" class="mt-24" v-reveal>
        <template #extra>
          <a-button type="link" @click="$router.push('/admin/applications')">
            Ҳамаро дидан
          </a-button>
        </template>

        <a-table
          :dataSource="recentApplications"
          :columns="columns"
          :pagination="false"
          size="small"
          row-key="id"
        >
          <template #bodyCell="{ column, record }">
            <template v-if="column.key === 'child'">
              {{ record.child_first_name }} {{ record.child_last_name }}
            </template>
            <template v-if="column.key === 'status'">
              <a-tag :color="statusMeta[record.status].color">
                {{ statusMeta[record.status].label }}
              </a-tag>
            </template>
            <template v-if="column.key === 'action'">
              <a-button type="link" size="small" @click="$router.push(`/admin/applications/${record.id}`)">
                Дидан
              </a-button>
            </template>
          </template>
        </a-table>
      </a-card>
    </a-spin>
  </div>
</template>

<script setup lang="ts">
import { ref, computed, onMounted } from 'vue'
import { message } from 'ant-design-vue'
import StatCard from '@/components/common/StatCard.vue'
import {
  FileTextOutlined,
  ClockCircleOutlined,
  CheckCircleOutlined,
  CloseCircleOutlined,
  FileSearchOutlined,
} from '@ant-design/icons-vue'
import { applicationsApi } from '@/api/applications'
import { statusMeta, roleMeta } from '@/theme'
import { useAuthStore } from '@/stores/auth'
import type { Application, Statistics } from '@/types'

const authStore = useAuthStore()
const loading = ref(false)
const stats = ref<Statistics>({ total: 0, pending: 0, review: 0, approved: 0, rejected: 0 })
const recentApplications = ref<Application[]>([])

const roleLabel = computed(() => roleMeta[authStore.user?.role || '']?.label || '')
const today = new Date().toLocaleDateString('tg-TJ', { year: 'numeric', month: 'long', day: 'numeric' })

const columns = [
  { title: 'Код', dataIndex: 'application_code', key: 'code', width: 200 },
  { title: 'Кӯдак', key: 'child' },
  { title: 'Синф', dataIndex: 'grade', key: 'grade', width: 80 },
  { title: 'Статус', key: 'status', width: 120 },
  { title: '', key: 'action', width: 80 },
]

onMounted(async () => {
  loading.value = true
  try {
    const [statsRes, appsRes] = await Promise.all([
      applicationsApi.getStatistics(),
      applicationsApi.getAll({ page: 1 }),
    ])
    stats.value = statsRes.data.data
    recentApplications.value = appsRes.data.data.slice(0, 10)
  } catch {
    message.error('Хатои боргузории маълумот')
  } finally {
    loading.value = false
  }
})
</script>

<style scoped>
.dash-hero {
  display: flex;
  align-items: center;
  justify-content: space-between;
  flex-wrap: wrap;
  gap: 16px;
  margin-bottom: 24px;
  padding: 24px 28px;
  border-radius: var(--radius-md);
  background: var(--gradient-deep);
  color: #fff;
  position: relative;
  overflow: hidden;
}
.dash-hero::after {
  content: '';
  position: absolute;
  width: 240px;
  height: 240px;
  border-radius: 50%;
  background: radial-gradient(circle, rgba(245, 197, 24, 0.2), transparent 70%);
  top: -100px;
  right: -40px;
}
.dash-hero__text h2 { font-size: 22px; font-weight: 800; margin-bottom: 4px; }
.dash-hero__text p {
  display: flex;
  align-items: center;
  gap: 8px;
  opacity: 0.85;
  font-size: 14px;
}
.stats-row { margin-bottom: 24px; }
.mt-24 { margin-top: 24px; }
</style>
