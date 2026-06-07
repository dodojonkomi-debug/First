<template>
  <div class="regions-page">
    <div class="page-header">
      <h2>Вилоятҳо</h2>
      <a-button type="primary" @click="showModal = true">+ Илова кардан</a-button>
    </div>

    <a-table :dataSource="regions" :columns="columns" :loading="loading" row-key="id">
      <template #bodyCell="{ column, record }">
        <template v-if="column.key === 'status'">
          <a-tag :color="record.is_active ? 'green' : 'red'">
            {{ record.is_active ? 'Фаъол' : 'Ғайрифаъол' }}
          </a-tag>
        </template>
        <template v-if="column.key === 'action'">
          <a-space>
            <a-button type="link" size="small" @click="editRegion(record)">Таҳрир</a-button>
            <a-popconfirm title="Нест кардан?" @confirm="deleteRegion(record.id)">
              <a-button type="link" size="small" danger>Нест</a-button>
            </a-popconfirm>
          </a-space>
        </template>
      </template>
    </a-table>

    <!-- Модал -->
    <a-modal
      v-model:open="showModal"
      :title="editingId ? 'Таҳрири вилоят' : 'Вилояти нав'"
      @ok="handleSave"
      @cancel="resetForm"
    >
      <a-form layout="vertical">
        <a-form-item label="Ном" required>
          <a-input v-model:value="form.name" placeholder="Номи вилоят" />
        </a-form-item>
        <a-form-item label="Код" required>
          <a-input v-model:value="form.code" placeholder="SUGHD" />
        </a-form-item>
      </a-form>
    </a-modal>
  </div>
</template>

<script setup lang="ts">
import { ref, reactive, onMounted } from 'vue'
import { message } from 'ant-design-vue'
import { regionsApi } from '@/api/regions'
import type { Region } from '@/types'

const loading = ref(false)
const regions = ref<Region[]>([])
const showModal = ref(false)
const editingId = ref<number | null>(null)
const form = reactive({ name: '', code: '' })

const columns = [
  { title: 'ID', dataIndex: 'id', key: 'id', width: 60 },
  { title: 'Ном', dataIndex: 'name', key: 'name' },
  { title: 'Код', dataIndex: 'code', key: 'code', width: 120 },
  { title: 'Ноҳияҳо', dataIndex: 'districts_count', key: 'districts_count', width: 100 },
  { title: 'Ҳолат', key: 'status', width: 100 },
  { title: '', key: 'action', width: 150 },
]

function editRegion(region: Region) {
  editingId.value = region.id
  form.name = region.name
  form.code = region.code
  showModal.value = true
}

function resetForm() {
  editingId.value = null
  form.name = ''
  form.code = ''
  showModal.value = false
}

async function handleSave() {
  if (!form.name || !form.code) {
    message.warning('Ҳамаи майдонҳоро пур кунед')
    return
  }
  try {
    if (editingId.value) {
      await regionsApi.update(editingId.value, form)
      message.success('Тағйирот сабт шуд')
    } else {
      await regionsApi.create(form)
      message.success('Вилоят илова шуд')
    }
    resetForm()
    loadRegions()
  } catch (err: any) {
    message.error(err.response?.data?.message || 'Хато')
  }
}

async function deleteRegion(id: number) {
  try {
    await regionsApi.delete(id)
    message.success('Нест карда шуд')
    loadRegions()
  } catch {
    message.error('Хатои нест кардан')
  }
}

async function loadRegions() {
  loading.value = true
  try {
    const res = await regionsApi.getAll()
    regions.value = res.data.data
  } catch {
    message.error('Хато')
  } finally {
    loading.value = false
  }
}

onMounted(loadRegions)
</script>
