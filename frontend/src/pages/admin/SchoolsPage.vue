<template>
  <div class="schools-page">
    <div class="page-header">
      <h2>Мактабҳо</h2>
      <a-button type="primary" @click="showModal = true">+ Илова кардан</a-button>
    </div>

    <a-card class="mb-16">
      <a-row :gutter="16">
        <a-col :span="8">
          <a-select v-model:value="filterRegionId" placeholder="Вилоят" allow-clear @change="onRegionFilter" style="width:100%">
            <a-select-option v-for="r in regions" :key="r.id" :value="r.id">{{ r.name }}</a-select-option>
          </a-select>
        </a-col>
        <a-col :span="8">
          <a-select v-model:value="filterDistrictId" placeholder="Ноҳия" allow-clear @change="loadSchools" :disabled="!filterRegionId" style="width:100%">
            <a-select-option v-for="d in filterDistricts" :key="d.id" :value="d.id">{{ d.name }}</a-select-option>
          </a-select>
        </a-col>
      </a-row>
    </a-card>

    <a-table :dataSource="schools" :columns="columns" :loading="loading" row-key="id" size="small">
      <template #bodyCell="{ column, record }">
        <template v-if="column.key === 'district'">{{ record.district?.name }}</template>
        <template v-if="column.key === 'region'">{{ record.district?.region?.name }}</template>
        <template v-if="column.key === 'action'">
          <a-space>
            <a-button type="link" size="small" @click="editItem(record)">Таҳрир</a-button>
            <a-popconfirm title="Нест?" @confirm="deleteItem(record.id)">
              <a-button type="link" size="small" danger>Нест</a-button>
            </a-popconfirm>
          </a-space>
        </template>
      </template>
    </a-table>

    <a-modal v-model:open="showModal" :title="editingId ? 'Таҳрир' : 'Мактаби нав'" @ok="handleSave" @cancel="resetForm" width="600px">
      <a-form layout="vertical">
        <a-row :gutter="16">
          <a-col :span="12">
            <a-form-item label="Вилоят" required>
              <a-select v-model:value="formRegionId" @change="onFormRegionChange" placeholder="Вилоят">
                <a-select-option v-for="r in regions" :key="r.id" :value="r.id">{{ r.name }}</a-select-option>
              </a-select>
            </a-form-item>
          </a-col>
          <a-col :span="12">
            <a-form-item label="Ноҳия" required>
              <a-select v-model:value="form.district_id" placeholder="Ноҳия" :disabled="!formRegionId">
                <a-select-option v-for="d in formDistricts" :key="d.id" :value="d.id">{{ d.name }}</a-select-option>
              </a-select>
            </a-form-item>
          </a-col>
        </a-row>
        <a-form-item label="Номи мактаб" required>
          <a-input v-model:value="form.name" />
        </a-form-item>
        <a-row :gutter="16">
          <a-col :span="8">
            <a-form-item label="Код" required>
              <a-input v-model:value="form.code" />
            </a-form-item>
          </a-col>
          <a-col :span="8">
            <a-form-item label="Ҷои синфи 0">
              <a-input-number v-model:value="form.capacity_class_0" :min="0" style="width:100%" />
            </a-form-item>
          </a-col>
          <a-col :span="8">
            <a-form-item label="Ҷои синфи 1">
              <a-input-number v-model:value="form.capacity_class_1" :min="0" style="width:100%" />
            </a-form-item>
          </a-col>
        </a-row>
        <a-form-item label="Суроға">
          <a-input v-model:value="form.address" />
        </a-form-item>
        <a-form-item label="Телефон">
          <a-input v-model:value="form.phone" />
        </a-form-item>
      </a-form>
    </a-modal>
  </div>
</template>

<script setup lang="ts">
import { ref, reactive, onMounted } from 'vue'
import { message } from 'ant-design-vue'
import { schoolsApi } from '@/api/schools'
import { regionsApi } from '@/api/regions'
import { districtsApi } from '@/api/districts'
import type { School, Region, District } from '@/types'

const loading = ref(false)
const schools = ref<School[]>([])
const regions = ref<Region[]>([])
const showModal = ref(false)
const editingId = ref<number | null>(null)

const filterRegionId = ref<number | undefined>(undefined)
const filterDistrictId = ref<number | undefined>(undefined)
const filterDistricts = ref<District[]>([])

const formRegionId = ref<number | null>(null)
const formDistricts = ref<District[]>([])

const form = reactive({
  district_id: null as number | null,
  name: '', code: '', address: '', phone: '',
  capacity_class_0: 30, capacity_class_1: 35,
})

const columns = [
  { title: 'Ном', dataIndex: 'name' },
  { title: 'Код', dataIndex: 'code', width: 140 },
  { title: 'Ноҳия', key: 'district' },
  { title: 'Вилоят', key: 'region' },
  { title: 'Синф 0', dataIndex: 'capacity_class_0', width: 80 },
  { title: 'Синф 1', dataIndex: 'capacity_class_1', width: 80 },
  { title: '', key: 'action', width: 140 },
]

async function onRegionFilter(regionId: number | undefined) {
  filterDistrictId.value = undefined
  if (regionId) {
    const res = await districtsApi.getAll(regionId)
    filterDistricts.value = res.data.data
  } else {
    filterDistricts.value = []
  }
  loadSchools()
}

async function onFormRegionChange(regionId: number) {
  form.district_id = null
  const res = await districtsApi.getAll(regionId)
  formDistricts.value = res.data.data
}

function editItem(item: School) {
  editingId.value = item.id
  form.district_id = item.district_id
  form.name = item.name
  form.code = item.code
  form.address = item.address || ''
  form.phone = item.phone || ''
  form.capacity_class_0 = item.capacity_class_0
  form.capacity_class_1 = item.capacity_class_1
  formRegionId.value = item.district?.region?.id || null
  if (formRegionId.value) onFormRegionChange(formRegionId.value)
  showModal.value = true
}

function resetForm() {
  editingId.value = null
  form.district_id = null
  form.name = ''
  form.code = ''
  form.address = ''
  form.phone = ''
  form.capacity_class_0 = 30
  form.capacity_class_1 = 35
  formRegionId.value = null
  showModal.value = false
}

async function handleSave() {
  if (!form.name || !form.code || !form.district_id) {
    message.warning('Ҳамаро пур кунед')
    return
  }
  try {
    if (editingId.value) {
      await schoolsApi.update(editingId.value, form)
    } else {
      await schoolsApi.create(form)
    }
    message.success('Сабт шуд')
    resetForm()
    loadSchools()
  } catch (err: any) {
    message.error(err.response?.data?.message || 'Хато')
  }
}

async function deleteItem(id: number) {
  try { await schoolsApi.delete(id); message.success('Нест шуд'); loadSchools() }
  catch { message.error('Хато') }
}

async function loadSchools() {
  loading.value = true
  try {
    const params: any = {}
    if (filterDistrictId.value) params.district_id = filterDistrictId.value
    else if (filterRegionId.value) params.region_id = filterRegionId.value
    const res = await schoolsApi.getAll(params)
    schools.value = res.data.data
  } catch { message.error('Хато') }
  finally { loading.value = false }
}

onMounted(async () => {
  const res = await regionsApi.getAll()
  regions.value = res.data.data
  loadSchools()
})
</script>

<style scoped>
.mb-16 { margin-bottom: 16px; }
</style>
