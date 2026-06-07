<template>
  <div class="applications-page">
    <div class="page-header">
      <h2>Аризаҳо</h2>
    </div>

    <!-- Филтрация -->
    <a-card class="mb-16">
      <a-row :gutter="16">
        <a-col :span="8">
          <a-input-search
            v-model:value="searchQuery"
            placeholder="Ҷустуҷӯ (код, ном)"
            @search="loadApplications"
            allow-clear
          />
        </a-col>
        <a-col :span="6">
          <a-select
            v-model:value="statusFilter"
            placeholder="Статус"
            allow-clear
            style="width: 100%"
            @change="loadApplications"
          >
            <a-select-option value="pending">Дар интизор</a-select-option>
            <a-select-option value="review">Дар баррасӣ</a-select-option>
            <a-select-option value="approved">Қабулшуда</a-select-option>
            <a-select-option value="rejected">Радшуда</a-select-option>
          </a-select>
        </a-col>
        <a-col :span="4">
          <a-button @click="resetFilters">Тоза кардан</a-button>
        </a-col>
      </a-row>
    </a-card>

    <!-- Ҷадвал -->
    <a-table
      :dataSource="applications"
      :columns="columns"
      :loading="loading"
      :pagination="pagination"
      @change="handleTableChange"
      row-key="id"
    >
      <template #bodyCell="{ column, record }">
        <template v-if="column.key === 'child'">
          {{ record.child_first_name }} {{ record.child_last_name }}
        </template>
        <template v-if="column.key === 'school'">
          {{ record.school?.name }}
        </template>
        <template v-if="column.key === 'status'">
          <a-tag :color="statusColor(record.status)">
            {{ statusLabel(record.status) }}
          </a-tag>
        </template>
        <template v-if="column.key === 'date'">
          {{ new Date(record.created_at).toLocaleDateString('ru-RU') }}
        </template>
        <template v-if="column.key === 'action'">
          <a-button type="link" @click="$router.push(`/admin/applications/${record.id}`)">
            Баррасӣ
          </a-button>
        </template>
      </template>
    </a-table>
  </div>
</template>

<script setup lang="ts">
import { ref, reactive, onMounted } from 'vue'
import { message } from 'ant-design-vue'
import { applicationsApi } from '@/api/applications'
import type { Application, ApplicationStatus } from '@/types'

const loading = ref(false)
const applications = ref<Application[]>([])
const searchQuery = ref('')
const statusFilter = ref<string | undefined>(undefined)
const pagination = reactive({ current: 1, pageSize: 20, total: 0 })

const columns = [
  { title: 'Код', dataIndex: 'application_code', key: 'code', width: 220 },
  { title: 'Кӯдак', key: 'child' },
  { title: 'Мактаб', key: 'school' },
  { title: 'Синф', dataIndex: 'grade', key: 'grade', width: 70 },
  { title: 'Статус', key: 'status', width: 120 },
  { title: 'Сана', key: 'date', width: 100 },
  { title: '', key: 'action', width: 100 },
]

function statusColor(status: ApplicationStatus) {
  return { pending: 'orange', review: 'blue', approved: 'green', rejected: 'red' }[status]
}

function statusLabel(status: ApplicationStatus) {
  return { pending: 'Интизор', review: 'Баррасӣ', approved: 'Қабул', rejected: 'Рад' }[status]
}

function resetFilters() {
  searchQuery.value = ''
  statusFilter.value = undefined
  pagination.current = 1
  loadApplications()
}

function handleTableChange(pag: any) {
  pagination.current = pag.current
  loadApplications()
}

async function loadApplications() {
  loading.value = true
  try {
    const res = await applicationsApi.getAll({
      status: statusFilter.value,
      search: searchQuery.value || undefined,
      page: pagination.current,
    })
    applications.value = res.data.data
    pagination.total = res.data.total
  } catch {
    message.error('Хатои боргузорӣ')
  } finally {
    loading.value = false
  }
}

onMounted(loadApplications)
</script>

<style scoped>
.mb-16 { margin-bottom: 16px; }
</style>
