<template>
  <div class="check-page">
    <header class="check-nav">
      <div class="container check-nav__inner">
        <AppLogo :size="44" to="/" title="МТМУ" subtitle="Қабули хонандагон" />
        <a-button type="text" @click="$router.push('/')">Саҳифаи асосӣ</a-button>
      </div>
    </header>

    <div class="check-body">
      <a-card class="check-card" :bordered="false">
        <div class="check-card__head">
          <div class="check-card__icon"><SearchOutlined /></div>
          <h1>Тафтиши статуси ариза</h1>
          <p>Коди аризаро, ки ҳангоми сабт гирифтед, ворид кунед</p>
        </div>

        <a-form @finish="handleCheck" layout="vertical">
          <a-form-item label="Коди ариза">
            <a-input
              v-model:value="code"
              placeholder="Масалан: 2025-08-15-10-MTMU__-3847"
              size="large"
              allow-clear
            >
              <template #prefix><SearchOutlined /></template>
            </a-input>
          </a-form-item>
          <a-button type="primary" html-type="submit" size="large" block :loading="loading">
            Тафтиш кардан
          </a-button>
        </a-form>

        <!-- Натиҷа -->
        <div v-if="result" class="result-section">
          <a-divider />
          <a-descriptions title="Натиҷаи ариза" bordered :column="1" size="middle">
            <a-descriptions-item label="Коди ариза">
              <a-typography-text copyable>{{ result.application_code }}</a-typography-text>
            </a-descriptions-item>
            <a-descriptions-item label="Номи кӯдак">{{ result.child_name }}</a-descriptions-item>
            <a-descriptions-item label="Мактаб">{{ result.school }}</a-descriptions-item>
            <a-descriptions-item label="Синф">
              {{ result.grade === '0' ? 'Синфи 0 (6-сола)' : 'Синфи 1 (7-сола)' }}
            </a-descriptions-item>
            <a-descriptions-item label="Статус">
              <a-tag :color="statusMeta[result.status].color">
                {{ statusMeta[result.status].label }}
              </a-tag>
            </a-descriptions-item>
            <a-descriptions-item v-if="result.rejection_reason" label="Сабаби рад">
              <a-alert :message="result.rejection_reason" type="error" show-icon />
            </a-descriptions-item>
            <a-descriptions-item label="Санаи ирсол">{{ result.submitted_at }}</a-descriptions-item>
            <a-descriptions-item v-if="result.reviewed_at" label="Санаи баррасӣ">
              {{ result.reviewed_at }}
            </a-descriptions-item>
          </a-descriptions>
        </div>

        <div v-if="notFound" class="result-section">
          <a-divider />
          <a-result
            status="warning"
            title="Ариза ёфт нашуд"
            sub-title="Лутфан коди аризаро дуруст ворид кунед"
          />
        </div>
      </a-card>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref } from 'vue'
import { message } from 'ant-design-vue'
import { SearchOutlined } from '@ant-design/icons-vue'
import AppLogo from '@/components/common/AppLogo.vue'
import { statusMeta } from '@/theme'
import { applicationsApi } from '@/api/applications'
import type { StatusCheckResult } from '@/types'

const code = ref('')
const loading = ref(false)
const result = ref<StatusCheckResult | null>(null)
const notFound = ref(false)

async function handleCheck() {
  if (!code.value.trim()) return
  loading.value = true
  result.value = null
  notFound.value = false
  try {
    const res = await applicationsApi.checkStatus(code.value.trim())
    result.value = res.data.data
  } catch (err: any) {
    if (err.response?.status === 404) notFound.value = true
    else message.error('Хатои сервер')
  } finally {
    loading.value = false
  }
}
</script>

<style scoped>
.check-page {
  min-height: 100vh;
  display: flex;
  flex-direction: column;
  background: var(--brand-bg);
}
.check-nav {
  background: #fff;
  border-bottom: 1px solid var(--brand-border);
}
.check-nav__inner {
  display: flex;
  align-items: center;
  justify-content: space-between;
  height: 68px;
}
.check-body {
  flex: 1;
  display: flex;
  align-items: flex-start;
  justify-content: center;
  padding: 48px 16px;
}
.check-card {
  width: 100%;
  max-width: 560px;
  box-shadow: var(--shadow-md);
}
.check-card__head {
  text-align: center;
  margin-bottom: 24px;
}
.check-card__icon {
  width: 60px;
  height: 60px;
  border-radius: 18px;
  background: var(--gradient-soft);
  color: var(--brand-primary);
  font-size: 28px;
  display: flex;
  align-items: center;
  justify-content: center;
  margin: 0 auto 16px;
}
.check-card__head h1 {
  font-size: 22px;
  color: var(--brand-heading);
  margin-bottom: 6px;
}
.check-card__head p {
  color: var(--brand-muted);
}
.result-section {
  margin-top: 8px;
}
</style>
