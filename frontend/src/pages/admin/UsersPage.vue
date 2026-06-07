<template>
  <div class="users-page">
    <div class="page-header">
      <h2>Корбарон (Маъмурон)</h2>
      <a-button type="primary" @click="showModal = true">+ Илова кардан</a-button>
    </div>

    <a-card class="mb-16">
      <a-row :gutter="16">
        <a-col :span="6">
          <a-select v-model:value="filterRole" placeholder="Нақш" allow-clear @change="loadUsers" style="width:100%">
            <a-select-option value="admin_region">Маъмури вилоят</a-select-option>
            <a-select-option value="admin_district">Маъмури ноҳия</a-select-option>
            <a-select-option value="admin_school">Маъмури мактаб (Директор)</a-select-option>
          </a-select>
        </a-col>
      </a-row>
    </a-card>

    <a-table :dataSource="users" :columns="columns" :loading="loading" row-key="id">
      <template #bodyCell="{ column, record }">
        <template v-if="column.key === 'role'">
          <a-tag :color="roleColor(record.role)">{{ roleLabel(record.role) }}</a-tag>
        </template>
        <template v-if="column.key === 'scope'">
          {{ record.region?.name || record.district?.name || record.school?.name || '-' }}
        </template>
        <template v-if="column.key === 'status'">
          <a-tag :color="record.is_active ? 'green' : 'red'">
            {{ record.is_active ? 'Фаъол' : 'Ғайрифаъол' }}
          </a-tag>
        </template>
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

    <!-- Модали илова/таҳрир -->
    <a-modal v-model:open="showModal" :title="editingId ? 'Таҳрир' : 'Корбари нав'" @ok="handleSave" @cancel="resetForm" width="600px">
      <a-form layout="vertical">
        <a-row :gutter="16">
          <a-col :span="12">
            <a-form-item label="Ном" required>
              <a-input v-model:value="form.name" />
            </a-form-item>
          </a-col>
          <a-col :span="12">
            <a-form-item label="Email" required>
              <a-input v-model:value="form.email" />
            </a-form-item>
          </a-col>
        </a-row>
        <a-row :gutter="16">
          <a-col :span="12">
            <a-form-item label="Телефон">
              <a-input v-model:value="form.phone" />
            </a-form-item>
          </a-col>
          <a-col :span="12">
            <a-form-item :label="editingId ? 'Рамзи нав (холӣ = тағйир намеёбад)' : 'Рамз'" :required="!editingId">
              <a-input-password v-model:value="form.password" />
            </a-form-item>
          </a-col>
        </a-row>

        <a-form-item label="Нақш" required>
          <a-select v-model:value="form.role" @change="onRoleChange">
            <a-select-option value="admin_region">Маъмури вилоят</a-select-option>
            <a-select-option value="admin_district">Маъмури ноҳия</a-select-option>
            <a-select-option value="admin_school">Маъмури мактаб (Директор)</a-select-option>
          </a-select>
        </a-form-item>

        <a-form-item v-if="form.role === 'admin_region'" label="Вилоят" required>
          <a-select v-model:value="form.region_id" placeholder="Вилоят">
            <a-select-option v-for="r in regions" :key="r.id" :value="r.id">{{ r.name }}</a-select-option>
          </a-select>
        </a-form-item>

        <template v-if="form.role === 'admin_district'">
          <a-form-item label="Вилоят" required>
            <a-select v-model:value="selectedRegionForDistrict" @change="onFormRegionChange">
              <a-select-option v-for="r in regions" :key="r.id" :value="r.id">{{ r.name }}</a-select-option>
            </a-select>
          </a-form-item>
          <a-form-item label="Ноҳия" required>
            <a-select v-model:value="form.district_id" :disabled="!selectedRegionForDistrict">
              <a-select-option v-for="d in formDistricts" :key="d.id" :value="d.id">{{ d.name }}</a-select-option>
            </a-select>
          </a-form-item>
        </template>

        <template v-if="form.role === 'admin_school'">
          <a-form-item label="Вилоят" required>
            <a-select v-model:value="selectedRegionForSchool" @change="onSchoolRegionChange">
              <a-select-option v-for="r in regions" :key="r.id" :value="r.id">{{ r.name }}</a-select-option>
            </a-select>
          </a-form-item>
          <a-form-item label="Ноҳия" required>
            <a-select v-model:value="selectedDistrictForSchool" @change="onSchoolDistrictChange" :disabled="!selectedRegionForSchool">
              <a-select-option v-for="d in formDistricts" :key="d.id" :value="d.id">{{ d.name }}</a-select-option>
            </a-select>
          </a-form-item>
          <a-form-item label="Мактаб" required>
            <a-select v-model:value="form.school_id" :disabled="!selectedDistrictForSchool">
              <a-select-option v-for="s in formSchools" :key="s.id" :value="s.id">{{ s.name }}</a-select-option>
            </a-select>
          </a-form-item>
        </template>
      </a-form>
    </a-modal>
  </div>
</template>

<script setup lang="ts">
import { ref, reactive, onMounted } from 'vue'
import { message } from 'ant-design-vue'
import { usersApi } from '@/api/users'
import { regionsApi } from '@/api/regions'
import { districtsApi } from '@/api/districts'
import { schoolsApi } from '@/api/schools'
import type { User, Region, District, School } from '@/types'

const loading = ref(false)
const users = ref<User[]>([])
const regions = ref<Region[]>([])
const showModal = ref(false)
const editingId = ref<number | null>(null)
const filterRole = ref<string | undefined>(undefined)

const formDistricts = ref<District[]>([])
const formSchools = ref<School[]>([])
const selectedRegionForDistrict = ref<number | null>(null)
const selectedRegionForSchool = ref<number | null>(null)
const selectedDistrictForSchool = ref<number | null>(null)

const form = reactive({
  name: '', email: '', phone: '', password: '',
  role: 'admin_school' as string,
  region_id: null as number | null,
  district_id: null as number | null,
  school_id: null as number | null,
})

const columns = [
  { title: 'Ном', dataIndex: 'name' },
  { title: 'Email', dataIndex: 'email' },
  { title: 'Нақш', key: 'role', width: 150 },
  { title: 'Минтақа', key: 'scope' },
  { title: 'Ҳолат', key: 'status', width: 100 },
  { title: '', key: 'action', width: 140 },
]

function roleColor(role: string) {
  return { admin_region: 'purple', admin_district: 'blue', admin_school: 'cyan' }[role] || 'default'
}

function roleLabel(role: string) {
  return { admin_region: 'Вилоят', admin_district: 'Ноҳия', admin_school: 'Мактаб' }[role] || role
}

function onRoleChange() {
  form.region_id = null
  form.district_id = null
  form.school_id = null
}

async function onFormRegionChange(regionId: number) {
  form.district_id = null
  const res = await districtsApi.getAll(regionId)
  formDistricts.value = res.data.data
}

async function onSchoolRegionChange(regionId: number) {
  selectedDistrictForSchool.value = null
  form.school_id = null
  formSchools.value = []
  const res = await districtsApi.getAll(regionId)
  formDistricts.value = res.data.data
}

async function onSchoolDistrictChange(districtId: number) {
  form.school_id = null
  const res = await schoolsApi.getAll({ district_id: districtId })
  formSchools.value = res.data.data
}

function editItem(item: User) {
  editingId.value = item.id
  form.name = item.name
  form.email = item.email
  form.phone = item.phone || ''
  form.password = ''
  form.role = item.role
  form.region_id = item.region_id
  form.district_id = item.district_id
  form.school_id = item.school_id
  showModal.value = true
}

function resetForm() {
  editingId.value = null
  form.name = ''
  form.email = ''
  form.phone = ''
  form.password = ''
  form.role = 'admin_school'
  form.region_id = null
  form.district_id = null
  form.school_id = null
  showModal.value = false
}

async function handleSave() {
  if (!form.name || !form.email || !form.role) {
    message.warning('Ҳамаро пур кунед')
    return
  }
  if (!editingId.value && !form.password) {
    message.warning('Рамзро ворид кунед')
    return
  }
  try {
    const data: any = { ...form }
    if (!data.password) delete data.password
    if (editingId.value) {
      await usersApi.update(editingId.value, data)
    } else {
      await usersApi.create(data)
    }
    message.success('Сабт шуд')
    resetForm()
    loadUsers()
  } catch (err: any) {
    message.error(err.response?.data?.message || 'Хато')
  }
}

async function deleteItem(id: number) {
  try { await usersApi.delete(id); message.success('Нест шуд'); loadUsers() }
  catch (err: any) { message.error(err.response?.data?.message || 'Хато') }
}

async function loadUsers() {
  loading.value = true
  try {
    const params: any = {}
    if (filterRole.value) params.role = filterRole.value
    const res = await usersApi.getAll(params)
    users.value = res.data.data.data || res.data.data
  } catch { message.error('Хато') }
  finally { loading.value = false }
}

onMounted(async () => {
  const res = await regionsApi.getAll()
  regions.value = res.data.data
  loadUsers()
})
</script>

<style scoped>
.mb-16 { margin-bottom: 16px; }
</style>
