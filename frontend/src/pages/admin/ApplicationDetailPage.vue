<template>
  <div class="application-detail-admin">
    <a-page-header title="Баррасии ариза" @back="$router.push('/admin/applications')" />

    <a-spin :spinning="loading">
      <template v-if="application">
        <!-- Статус ва амалиёт -->
        <a-card class="mb-16">
          <a-row justify="space-between" align="middle">
            <a-col>
              <a-space>
                <span>Статуси ҷорӣ:</span>
                <a-tag :color="statusColor(application.status)" size="large">
                  {{ statusLabel(application.status) }}
                </a-tag>
              </a-space>
            </a-col>
            <a-col>
              <a-space>
                <a-button
                  v-if="application.status === 'pending'"
                  type="primary"
                  @click="handleStatus('review')"
                >
                  Ба баррасӣ гирифтан
                </a-button>
                <a-button
                  v-if="['pending', 'review'].includes(application.status)"
                  type="primary"
                  style="background: #52c41a; border-color: #52c41a"
                  @click="handleStatus('approved')"
                >
                  Қабул кардан
                </a-button>
                <a-button
                  v-if="['pending', 'review'].includes(application.status)"
                  danger
                  @click="showRejectModal = true"
                >
                  Рад кардан
                </a-button>
              </a-space>
            </a-col>
          </a-row>
        </a-card>

        <!-- Маълумоти кӯдак -->
        <a-card title="Маълумоти кӯдак" class="mb-16">
          <a-descriptions :column="{ xs: 1, sm: 2, md: 3 }">
            <a-descriptions-item label="Ном">{{ application.child_first_name }}</a-descriptions-item>
            <a-descriptions-item label="Насаб">{{ application.child_last_name }}</a-descriptions-item>
            <a-descriptions-item label="Номи падар">{{ application.child_middle_name || '-' }}</a-descriptions-item>
            <a-descriptions-item label="Таваллуд">{{ application.child_birth_date }}</a-descriptions-item>
            <a-descriptions-item label="Ҷинс">{{ application.child_gender === 'male' ? 'Писар' : 'Духтар' }}</a-descriptions-item>
            <a-descriptions-item label="Синф">{{ application.grade === '0' ? 'Синфи 0 (6-сола)' : 'Синфи 1 (7-сола)' }}</a-descriptions-item>
          </a-descriptions>
        </a-card>

        <!-- Маълумоти волидайн -->
        <a-card title="Маълумоти волидайн" class="mb-16">
          <a-descriptions :column="{ xs: 1, sm: 2 }">
            <a-descriptions-item label="Ном">{{ application.parent_first_name }} {{ application.parent_last_name }}</a-descriptions-item>
            <a-descriptions-item label="Шиноснома">{{ application.parent_id_number }}</a-descriptions-item>
            <a-descriptions-item label="Телефон">{{ application.parent_phone }}</a-descriptions-item>
            <a-descriptions-item label="Email">{{ application.parent_email || '-' }}</a-descriptions-item>
            <a-descriptions-item label="Суроға" :span="2">{{ application.residence_address }}</a-descriptions-item>
          </a-descriptions>
        </a-card>

        <!-- Мактаб -->
        <a-card title="Мактаб" class="mb-16">
          <a-descriptions :column="{ xs: 1, sm: 2 }">
            <a-descriptions-item label="Мактаб">{{ application.school?.name }}</a-descriptions-item>
            <a-descriptions-item label="Ноҳия">{{ application.school?.district?.name }}</a-descriptions-item>
            <a-descriptions-item label="Вилоят">{{ application.school?.district?.region?.name }}</a-descriptions-item>
            <a-descriptions-item label="Коди ариза">
              <a-typography-text copyable>{{ application.application_code }}</a-typography-text>
            </a-descriptions-item>
          </a-descriptions>
        </a-card>

        <!-- Ҳуҷҷатҳо -->
        <a-card title="Ҳуҷҷатҳо" class="mb-16">
          <a-list :data-source="documentChecklist" size="small">
            <template #renderItem="{ item }">
              <a-list-item>
                <a-list-item-meta :title="item.label" />
                <template #actions>
                  <a-tag :color="item.uploaded ? 'green' : 'red'">
                    {{ item.uploaded ? 'Мавҷуд' : 'Нест' }}
                  </a-tag>
                </template>
              </a-list-item>
            </template>
          </a-list>
        </a-card>
      </template>
    </a-spin>

    <!-- Модали рад -->
    <a-modal
      v-model:open="showRejectModal"
      title="Сабаби рад"
      @ok="handleReject"
      ok-text="Рад кардан"
      :ok-button-props="{ danger: true }"
    >
      <a-textarea
        v-model:value="rejectionReason"
        placeholder="Сабаби рад кардани аризаро нависед..."
        :rows="4"
      />
    </a-modal>
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
const showRejectModal = ref(false)
const rejectionReason = ref('')

function statusColor(status: ApplicationStatus) {
  return { pending: 'orange', review: 'blue', approved: 'green', rejected: 'red' }[status]
}

function statusLabel(status: ApplicationStatus) {
  return { pending: 'Дар интизор', review: 'Дар баррасӣ', approved: 'Қабул шуд', rejected: 'Рад шуд' }[status]
}

const documentChecklist = computed(() => {
  if (!application.value) return []
  return [
    { label: 'Шаҳодатномаи таваллуд', uploaded: application.value.has_birth_certificate },
    { label: 'Шиноснома/ШҲ-и волидайн', uploaded: application.value.has_parent_id },
    { label: 'Маълумотномаи тиббӣ (форма 026)', uploaded: application.value.has_medical_form },
    { label: 'Корти эмгузаронӣ', uploaded: application.value.has_vaccination_card },
    { label: 'Маълумотнома аз ҷойи зист', uploaded: application.value.has_residence_certificate },
  ]
})

async function handleStatus(status: string) {
  if (!application.value) return
  try {
    const res = await applicationsApi.updateStatus(application.value.id, { status })
    application.value = res.data.data
    message.success('Статус тағйир ёфт')
  } catch (err: any) {
    message.error(err.response?.data?.message || 'Хато')
  }
}

async function handleReject() {
  if (!rejectionReason.value.trim()) {
    message.warning('Сабаби радро нависед')
    return
  }
  if (!application.value) return
  try {
    const res = await applicationsApi.updateStatus(application.value.id, {
      status: 'rejected',
      rejection_reason: rejectionReason.value,
    })
    application.value = res.data.data
    showRejectModal.value = false
    rejectionReason.value = ''
    message.success('Ариза рад карда шуд')
  } catch (err: any) {
    message.error(err.response?.data?.message || 'Хато')
  }
}

onMounted(async () => {
  loading.value = true
  try {
    const id = Number(route.params.id)
    const res = await applicationsApi.getById(id)
    application.value = res.data.data
  } catch {
    message.error('Хато')
  } finally {
    loading.value = false
  }
})
</script>

<style scoped>
.mb-16 { margin-bottom: 16px; }
</style>
