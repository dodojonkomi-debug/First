<template>
  <div class="districts-page">
    <div class="page-header">
      <h2>Ноҳияҳо</h2>
      <a-button type="primary" @click="showModal = true">+ Илова кардан</a-button>
    </div>

    <!-- Филтр -->
    <a-card class="mb-16">
      <a-select
        v-model:value="filterRegionId"
        placeholder="Филтр аз рӯи вилоят"
        allow-clear
        style="width: 300px"
        @change="loadDistricts"
      >
        <a-select-option v-for="r in regions" :key="r.id" :value="r.id">{{ r.name }}</a-select-option>
      </a-select>
    </a-card>

    <a-table :dataSource="districts" :columns="columns" :loading="loading" row-key="id">
      <template #bodyCell="{ column, record }">
        <template v-if="column.key === 'region'">{{ record.region?.name }}</template>
        <template v-if="column.key === 'action'">
          <a-space>
            <a-button type="link" size="small" @click="editItem(record)">Таҳрир</a-button>
            <a-popconfirm title="Нест кардан?" @confirm="deleteItem(record.id)">
              <a-button type="link" size="small" danger>Нест</a-button>
            </a-popconfirm>
          </a-space>
        </template>
      </template>
    </a-table>

    <a-modal v-model:open="showModal" :title="editingId ? 'Таҳрир' : 'Ноҳияи нав'" @ok="handleSave" @cancel="resetForm">
      <a-form layout="vertical">
        <a-form-item label="Вилоят" required>
          <a-select v-model:value="form.region_id" placeholder="Вилоят">
            <a-select-option v-for="r in regions" :key="r.id" :value="r.id">{{ r.name }}</a-select-option>
          </a-select>
        </a-form-item>
        <a-form-item label="Ном" required>
          <a-input v-model:value="form.name" placeholder="Номи ноҳия" />
        </a-form-item>
        <a-form-item label="Код" required>
          <a-input v-model:value="form.code" placeholder="SUGHD-KHJ" />
        </a-form-item>
      </a-form>
    </a-modal>
  </div>
</template>

<script setup lang="ts">
import { ref, reactive, onMounted } from 'vue'
import { message } from 'ant-design-vue'
import { districtsApi } from '@/api/districts'
import { regionsApi } from '@/api/regions'
import type { District, Region } from '@/types'

const loading = ref(false)
const districts = ref<District[]>([])
const regions = ref<Region[]>([])
const showModal = ref(false)
const editingId = ref<number | null>(null)
const filterRegionId = ref<number | undefined>(undefined)
const form = reactive({ region_id: null as number | null, name: '', code: '' })

const columns = [
  { title: 'ID', dataIndex: 'id', width: 60 },
  { title: 'Ном', dataIndex: 'name' },
  { title: 'Код', dataIndex: 'code', width: 140 },
  { title: 'Вилоят', key: 'region' },
  { title: 'Мактабҳо', dataIndex: 'schools_count', width: 100 },
  { title: '', key: 'action', width: 150 },
]

function editItem(item: District) {
  editingId.value = item.id
  form.region_id = item.region_id
  form.name = item.name
  form.code = item.code
  showModal.value = true
}

function resetForm() {
  editingId.value = null
  form.region_id = null
  form.name = ''
  form.code = ''
  showModal.value = false
}

async function handleSave() {
  if (!form.name || !form.code || !form.region_id) {
    message.warning('Ҳамаро пур кунед')
    return
  }
  try {
    if (editingId.value) {
      await districtsApi.update(editingId.value, form)
    } else {
      await districtsApi.create(form)
    }
    message.success('Сабт шуд')
    resetForm()
    loadDistricts()
  } catch (err: any) {
    message.error(err.response?.data?.message || 'Хато')
  }
}

async function deleteItem(id: number) {
  try {
    await districtsApi.delete(id)
    message.success('Нест шуд')
    loadDistricts()
  } catch { message.error('Хато') }
}

async function loadDistricts() {
  loading.value = true
  try {
    const res = await districtsApi.getAll(filterRegionId.value)
    districts.value = res.data.data
  } catch { message.error('Хато') }
  finally { loading.value = false }
}

onMounted(async () => {
  const res = await regionsApi.getAll()
  regions.value = res.data.data
  loadDistricts()
})
</script>

<style scoped>
.mb-16 { margin-bottom: 16px; }
</style>
