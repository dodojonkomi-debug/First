<template>
  <div class="parent-dashboard">
    <div class="page-header">
      <div>
        <h2>Аризаҳои ман</h2>
        <p class="page-subtitle">Идоракунӣ ва пайгирии аризаҳои фарзандони шумо</p>
      </div>
      <a-button type="primary" size="large" class="btn-shine" @click="$router.push('/parent/new-application')">
        <template #icon><PlusOutlined /></template>
        Ариза додани нав
      </a-button>
    </div>

    <a-spin :spinning="loading">
      <!-- Empty state -->
      <div v-if="!loading && applications.length === 0" class="empty-state" v-reveal>
        <div class="empty-state__icon icon-chip"><FileAddOutlined /></div>
        <h3>Шумо ҳоло ариза надодаед</h3>
        <p>Барои оғоз, аввалин аризаи худро эҷод кунед — танҳо чанд дақиқа вақт мегирад.</p>
        <a-button type="primary" size="large" class="btn-shine" @click="$router.push('/parent/new-application')">
          <template #icon><PlusOutlined /></template>
          Ариза додан
        </a-button>
      </div>

      <!-- Рӯйхати аризаҳо -->
      <a-row v-else :gutter="[16, 16]">
        <a-col
          v-for="(item, i) in applications"
          :key="item.id"
          :xs="24"
          :sm="24"
          :md="12"
          :lg="8"
          v-reveal="{ delay: i * 0.05 }"
        >
          <div class="app-card hover-lift" @click="$router.push(`/parent/applications/${item.id}`)">
            <div class="app-card__top" :style="{ background: statusGradient(item.status) }">
              <span class="app-card__grade">{{ item.grade === '0' ? 'Синфи 0' : 'Синфи 1' }}</span>
              <a-tag :color="statusMeta[item.status].color" class="app-card__status">
                {{ statusMeta[item.status].label }}
              </a-tag>
            </div>
            <div class="app-card__body">
              <h3>{{ item.child_first_name }} {{ item.child_last_name }}</h3>
              <div class="app-card__row"><BankOutlined /> {{ item.school?.name }}</div>
              <div class="app-card__row"><CalendarOutlined /> {{ formatDate(item.created_at) }}</div>
              <div class="app-card__code">
                <span>Код:</span>
                <a-typography-text copyable :content="item.application_code" />
              </div>
            </div>
            <div class="app-card__footer">
              Тафсилот <ArrowRightOutlined />
            </div>
          </div>
        </a-col>
      </a-row>
    </a-spin>
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted } from 'vue'
import { message } from 'ant-design-vue'
import {
  PlusOutlined,
  FileAddOutlined,
  BankOutlined,
  CalendarOutlined,
  ArrowRightOutlined,
} from '@ant-design/icons-vue'
import { applicationsApi } from '@/api/applications'
import { statusMeta } from '@/theme'
import type { Application, ApplicationStatus } from '@/types'

const loading = ref(false)
const applications = ref<Application[]>([])

function statusGradient(status: ApplicationStatus): string {
  const map: Record<ApplicationStatus, string> = {
    pending: 'linear-gradient(135deg, #f59e0b, #d97706)',
    review: 'linear-gradient(135deg, #1668dc, #0b3d91)',
    approved: 'linear-gradient(135deg, #16a34a, #15803d)',
    rejected: 'linear-gradient(135deg, #dc2626, #991b1b)',
  }
  return map[status]
}

function formatDate(dateStr: string): string {
  return new Date(dateStr).toLocaleDateString('ru-RU')
}

onMounted(async () => {
  loading.value = true
  try {
    const res = await applicationsApi.getAll()
    applications.value = res.data.data
  } catch {
    message.error('Хатои боргузорӣ')
  } finally {
    loading.value = false
  }
})
</script>

<style scoped>
.app-card {
  background: #fff;
  border: 1px solid var(--brand-border);
  border-radius: var(--radius-md);
  overflow: hidden;
  cursor: pointer;
  height: 100%;
  display: flex;
  flex-direction: column;
}
.app-card__top {
  padding: 16px 18px;
  display: flex;
  align-items: center;
  justify-content: space-between;
  color: #fff;
}
.app-card__grade { font-weight: 700; font-size: 14px; }
.app-card__status { margin: 0; border: none; }
.app-card__body { padding: 18px; flex: 1; }
.app-card__body h3 { font-size: 18px; color: var(--brand-heading); margin-bottom: 12px; }
.app-card__row {
  display: flex;
  align-items: center;
  gap: 8px;
  color: var(--brand-muted);
  font-size: 14px;
  margin-bottom: 8px;
}
.app-card__code {
  display: flex;
  align-items: center;
  gap: 6px;
  margin-top: 12px;
  font-size: 13px;
  color: var(--brand-muted);
}
.app-card__footer {
  padding: 12px 18px;
  border-top: 1px solid var(--brand-border);
  color: var(--brand-primary);
  font-weight: 600;
  font-size: 14px;
  display: flex;
  align-items: center;
  gap: 6px;
  transition: gap 0.2s ease;
}
.app-card:hover .app-card__footer { gap: 12px; }

/* Empty state */
.empty-state {
  text-align: center;
  padding: 64px 24px;
  background: #fff;
  border: 1px dashed var(--brand-border);
  border-radius: var(--radius-lg);
}
.empty-state__icon {
  width: 80px;
  height: 80px;
  font-size: 38px;
  margin: 0 auto 20px;
}
.empty-state h3 { font-size: 20px; color: var(--brand-heading); margin-bottom: 8px; }
.empty-state p { color: var(--brand-muted); max-width: 420px; margin: 0 auto 24px; }
</style>
