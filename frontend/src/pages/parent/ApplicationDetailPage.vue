<template>
  <div class="application-detail">
    <a-page-header title="Тафсилоти ариза" @back="$router.push('/parent')" />

    <a-spin :spinning="loading">
      <template v-if="application">
        <!-- Статус -->
        <a-alert
          :type="alertType"
          :message="statusLabel(application.status)"
          :description="application.rejection_reason || undefined"
          show-icon
          class="mb-24"
        />

        <!-- Коди ариза -->
        <a-card title="Коди ариза" class="mb-24">
          <a-typography-title :level="3" copyable>
            {{ application.application_code }}
          </a-typography-title>
        </a-card>

        <!-- Маълумот -->
        <a-card title="Маълумоти кӯдак" class="mb-24">
          <a-descriptions :column="{ xs: 1, sm: 2 }">
            <a-descriptions-item label="Ном">{{ application.child_first_name }}</a-descriptions-item>
            <a-descriptions-item label="Насаб">{{ application.child_last_name }}</a-descriptions-item>
            <a-descriptions-item label="Таваллуд">{{ application.child_birth_date }}</a-descriptions-item>
            <a-descriptions-item label="Ҷинс">{{ application.child_gender === 'male' ? 'Писар' : 'Духтар' }}</a-descriptions-item>
            <a-descriptions-item label="Синф">{{ application.grade === '0' ? 'Синфи 0' : 'Синфи 1' }}</a-descriptions-item>
            <a-descriptions-item label="Мактаб">{{ application.school?.name }}</a-descriptions-item>
          </a-descriptions>
        </a-card>

        <!-- Ҳуҷҷатҳо -->
        <a-card title="Ҳуҷҷатҳо" class="mb-24">
          <a-list :data-source="documentStatuses" size="small">
            <template #renderItem="{ item }">
              <a-list-item>
                <a-list-item-meta :title="item.label" />
                <template #actions>
                  <a-tag v-if="item.uploaded" color="green">Боргузорӣ шуд</a-tag>
                  <a-upload
                    v-else
                    :before-upload="(file: File) => handleUpload(file, item.type)"
                    :show-upload-list="false"
                  >
                    <a-button type="primary" size="small">Боргузорӣ</a-button>
                  </a-upload>
                </template>
              </a-list-item>
            </template>
          </a-list>
        </a-card>
      </template>
    </a-spin>
  </div>
</template>

<script setup lang="ts">
import { ref, computed, onMounted } from 'vue'
import { useRoute } from 'vue-router'
import { message } from 'ant-design-vue'
import { applicationsApi } from '@/api/applications'
import type { Application, ApplicationStatus } from '@/types'

const route = useRoute()
const loading = ref(false)
const application = ref<Application | null>(null)

const alertType = computed(() => {
  const map: Record<ApplicationStatus, string> = {
    pending: 'warning', review: 'info', approved: 'success', rejected: 'error'
  }
  return map[application.value?.status || 'pending']
})

function statusLabel(status: ApplicationStatus): string {
  return { pending: 'Дар интизор', review: 'Дар баррасӣ', approved: 'Қабул шуд!', rejected: 'Рад шуд' }[status]
}

const documentStatuses = computed(() => {
  if (!application.value) return []
  return [
    { type: 'birth_certificate', label: 'Шаҳодатномаи таваллуд', uploaded: application.value.has_birth_certificate },
    { type: 'parent_id', label: 'Шиноснома/ШҲ', uploaded: application.value.has_parent_id },
    { type: 'medical_form', label: 'Маълумотномаи тиббӣ', uploaded: application.value.has_medical_form },
    { type: 'vaccination_card', label: 'Корти эмгузаронӣ', uploaded: application.value.has_vaccination_card },
    { type: 'residence_certificate', label: 'Маълумотнома аз ҷойи зист', uploaded: application.value.has_residence_certificate },
  ]
})

async function handleUpload(file: File, type: string) {
  if (!application.value) return false
  try {
    await applicationsApi.uploadDocument(application.value.id, type, file)
    message.success('Ҳуҷҷат бо муваффақият боргузорӣ шуд')
    await loadApplication()
  } catch {
    message.error('Хатои боргузории ҳуҷҷат')
  }
  return false
}

async function loadApplication() {
  loading.value = true
  try {
    const id = Number(route.params.id)
    const res = await applicationsApi.getById(id)
    application.value = res.data.data
  } catch {
    message.error('Хатои боргузорӣ')
  } finally {
    loading.value = false
  }
}

onMounted(loadApplication)
</script>

<style scoped>
.mb-24 { margin-bottom: 24px; }
</style>
