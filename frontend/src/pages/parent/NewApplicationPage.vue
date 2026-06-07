<template>
  <div class="new-application">
    <h2>Ариза додан</h2>
    <p class="subtitle">Маълумотро қадам ба қадам пур кунед</p>

    <a-steps :current="currentStep" class="steps-nav">
      <a-step title="Кӯдак" />
      <a-step title="Волидайн" />
      <a-step title="Мактаб" />
      <a-step title="Ҳуҷҷатҳо" />
      <a-step title="Тасдиқ" />
    </a-steps>

    <div class="step-content">
      <!-- Қадами 1: Маълумоти кӯдак -->
      <div v-show="currentStep === 0">
        <a-form layout="vertical">
          <a-row :gutter="16">
            <a-col :span="12">
              <a-form-item label="Номи кӯдак" required>
                <a-input v-model:value="form.child_first_name" placeholder="Ном" />
              </a-form-item>
            </a-col>
            <a-col :span="12">
              <a-form-item label="Насаби кӯдак" required>
                <a-input v-model:value="form.child_last_name" placeholder="Насаб" />
              </a-form-item>
            </a-col>
          </a-row>

          <a-form-item label="Номи падар (ихтиёрӣ)">
            <a-input v-model:value="form.child_middle_name" placeholder="Номи падар" />
          </a-form-item>

          <a-row :gutter="16">
            <a-col :span="12">
              <a-form-item label="Санаи таваллуд" required>
                <a-date-picker
                  v-model:value="childBirthDate"
                  style="width: 100%"
                  placeholder="Санаро интихоб кунед"
                  format="DD.MM.YYYY"
                />
              </a-form-item>
            </a-col>
            <a-col :span="12">
              <a-form-item label="Ҷинс" required>
                <a-select v-model:value="form.child_gender" placeholder="Ҷинсро интихоб кунед">
                  <a-select-option value="male">Писар</a-select-option>
                  <a-select-option value="female">Духтар</a-select-option>
                </a-select>
              </a-form-item>
            </a-col>
          </a-row>

          <a-form-item label="Синф" required>
            <a-radio-group v-model:value="form.grade" button-style="solid" size="large">
              <a-radio-button value="0">Синфи 0 (6-сола)</a-radio-button>
              <a-radio-button value="1">Синфи 1 (7-сола)</a-radio-button>
            </a-radio-group>
          </a-form-item>
        </a-form>
      </div>

      <!-- Қадами 2: Маълумоти волидайн -->
      <div v-show="currentStep === 1">
        <a-form layout="vertical">
          <a-row :gutter="16">
            <a-col :span="12">
              <a-form-item label="Номи волидайн" required>
                <a-input v-model:value="form.parent_first_name" placeholder="Ном" />
              </a-form-item>
            </a-col>
            <a-col :span="12">
              <a-form-item label="Насаби волидайн" required>
                <a-input v-model:value="form.parent_last_name" placeholder="Насаб" />
              </a-form-item>
            </a-col>
          </a-row>

          <a-form-item label="Рақами шиноснома" required>
            <a-input v-model:value="form.parent_id_number" placeholder="Рақами ШҲ ё шиноснома" />
          </a-form-item>

          <a-row :gutter="16">
            <a-col :span="12">
              <a-form-item label="Телефон" required>
                <a-input v-model:value="form.parent_phone" placeholder="+992XXXXXXXXX" />
              </a-form-item>
            </a-col>
            <a-col :span="12">
              <a-form-item label="Email (ихтиёрӣ)">
                <a-input v-model:value="form.parent_email" placeholder="email@example.com" />
              </a-form-item>
            </a-col>
          </a-row>

          <!-- Ҷойи зист -->
          <a-divider>Ҷойи зист</a-divider>
          <a-row :gutter="16">
            <a-col :span="8">
              <a-form-item label="Вилоят" required>
                <a-select
                  v-model:value="form.residence_region_id"
                  placeholder="Вилоят"
                  @change="onResidenceRegionChange"
                >
                  <a-select-option v-for="r in regions" :key="r.id" :value="r.id">
                    {{ r.name }}
                  </a-select-option>
                </a-select>
              </a-form-item>
            </a-col>
            <a-col :span="8">
              <a-form-item label="Ноҳия" required>
                <a-select
                  v-model:value="form.residence_district_id"
                  placeholder="Ноҳия"
                  :disabled="!form.residence_region_id"
                >
                  <a-select-option v-for="d in residenceDistricts" :key="d.id" :value="d.id">
                    {{ d.name }}
                  </a-select-option>
                </a-select>
              </a-form-item>
            </a-col>
            <a-col :span="8">
              <a-form-item label="Маҳалла / Суроға" required>
                <a-input v-model:value="form.residence_address" placeholder="Суроға" />
              </a-form-item>
            </a-col>
          </a-row>
        </a-form>
      </div>

      <!-- Қадами 3: Мактаб -->
      <div v-show="currentStep === 2">
        <a-form layout="vertical">
          <a-row :gutter="16">
            <a-col :span="8">
              <a-form-item label="Вилоят">
                <a-select v-model:value="schoolRegionId" placeholder="Вилоят" @change="onSchoolRegionChange">
                  <a-select-option v-for="r in regions" :key="r.id" :value="r.id">
                    {{ r.name }}
                  </a-select-option>
                </a-select>
              </a-form-item>
            </a-col>
            <a-col :span="8">
              <a-form-item label="Ноҳия">
                <a-select v-model:value="schoolDistrictId" placeholder="Ноҳия" @change="onSchoolDistrictChange" :disabled="!schoolRegionId">
                  <a-select-option v-for="d in schoolDistricts" :key="d.id" :value="d.id">
                    {{ d.name }}
                  </a-select-option>
                </a-select>
              </a-form-item>
            </a-col>
            <a-col :span="8">
              <a-form-item label="Мактаб" required>
                <a-select v-model:value="form.school_id" placeholder="Мактаб" :disabled="!schoolDistrictId">
                  <a-select-option v-for="s in schools" :key="s.id" :value="s.id">
                    {{ s.name }}
                  </a-select-option>
                </a-select>
              </a-form-item>
            </a-col>
          </a-row>
        </a-form>
      </div>

      <!-- Қадами 4: Ҳуҷҷатҳо -->
      <div v-show="currentStep === 3">
        <a-alert
          message="Лутфан аксҳои ҳуҷҷатҳоро боргузорӣ кунед. Формати дастгирӣ: JPG, PNG, PDF (макс 10MB)"
          type="info"
          show-icon
          class="mb-16"
        />
        <p class="note">
          <em>Ҳуҷҷатҳоро пас аз сабти ариза боргузорӣ мекунед</em>
        </p>

        <a-list :data-source="documentTypes" bordered>
          <template #renderItem="{ item }">
            <a-list-item>
              <a-list-item-meta :title="item.label" :description="item.description" />
              <template #actions>
                <a-tag color="orange">Пас аз сабт</a-tag>
              </template>
            </a-list-item>
          </template>
        </a-list>
      </div>

      <!-- Қадами 5: Тасдиқ -->
      <div v-show="currentStep === 4">
        <a-descriptions title="Маълумоти ариза" bordered :column="{ xs: 1, sm: 2 }">
          <a-descriptions-item label="Кӯдак">
            {{ form.child_first_name }} {{ form.child_last_name }}
          </a-descriptions-item>
          <a-descriptions-item label="Таваллуд">
            {{ childBirthDate?.format('DD.MM.YYYY') || '-' }}
          </a-descriptions-item>
          <a-descriptions-item label="Ҷинс">
            {{ form.child_gender === 'male' ? 'Писар' : 'Духтар' }}
          </a-descriptions-item>
          <a-descriptions-item label="Синф">
            {{ form.grade === '0' ? 'Синфи 0' : 'Синфи 1' }}
          </a-descriptions-item>
          <a-descriptions-item label="Волидайн">
            {{ form.parent_first_name }} {{ form.parent_last_name }}
          </a-descriptions-item>
          <a-descriptions-item label="Телефон">
            {{ form.parent_phone }}
          </a-descriptions-item>
          <a-descriptions-item label="Мактаб" :span="2">
            {{ selectedSchoolName }}
          </a-descriptions-item>
        </a-descriptions>

        <a-divider />
        <a-checkbox v-model:checked="confirmed">
          Ман маълумоти воридкардаро тасдиқ мекунам
        </a-checkbox>
      </div>
    </div>

    <!-- Тугмаҳо -->
    <div class="step-actions">
      <a-button v-if="currentStep > 0" @click="currentStep--">
        Ба қафо
      </a-button>
      <a-button v-if="currentStep < 4" type="primary" @click="nextStep">
        Давом додан
      </a-button>
      <a-button
        v-if="currentStep === 4"
        type="primary"
        :loading="submitting"
        :disabled="!confirmed"
        @click="submitApplication"
      >
        Ирсоли ариза
      </a-button>
    </div>

    <!-- Модали муваффақият -->
    <a-modal v-model:open="successModal" :footer="null" :closable="false">
      <a-result
        status="success"
        title="Ариза бо муваффақият сабт шуд!"
        :sub-title="`Коди аризаи шумо: ${applicationCode}`"
      >
        <template #extra>
          <a-typography-paragraph copyable>
            {{ applicationCode }}
          </a-typography-paragraph>
          <p>Ин кодро нигоҳ доред! Бо ин код статуси аризаро тафтиш карда метавонед.</p>
          <a-button type="primary" @click="$router.push('/parent')">
            Ба аризаҳои ман
          </a-button>
        </template>
      </a-result>
    </a-modal>
  </div>
</template>

<script setup lang="ts">
import { ref, reactive, computed, onMounted } from 'vue'
import { message } from 'ant-design-vue'
import { useAppStore } from '@/stores/app'
import { applicationsApi } from '@/api/applications'
import { districtsApi } from '@/api/districts'
import { schoolsApi } from '@/api/schools'
import type { ApplicationForm, District, School } from '@/types'

const appStore = useAppStore()
const currentStep = ref(0)
const submitting = ref(false)
const confirmed = ref(false)
const successModal = ref(false)
const applicationCode = ref('')
const childBirthDate = ref<any>(null)

const form = reactive<ApplicationForm>({
  school_id: null,
  child_first_name: '',
  child_last_name: '',
  child_middle_name: '',
  child_birth_date: '',
  child_gender: '',
  grade: '',
  parent_first_name: '',
  parent_last_name: '',
  parent_id_number: '',
  parent_phone: '',
  parent_email: '',
  residence_region_id: null,
  residence_district_id: null,
  residence_address: '',
})

// Ҷойи зист
const residenceDistricts = ref<District[]>([])

// Мактаб
const schoolRegionId = ref<number | null>(null)
const schoolDistrictId = ref<number | null>(null)
const schoolDistricts = ref<District[]>([])
const schools = ref<School[]>([])

const regions = computed(() => appStore.regions)

const selectedSchoolName = computed(() => {
  const s = schools.value.find(s => s.id === form.school_id)
  return s?.name || '-'
})

const documentTypes = [
  { key: 'birth_certificate', label: 'Шаҳодатномаи таваллуд', description: 'Акси рангин' },
  { key: 'parent_id', label: 'Шиноснома/ШҲ-и волидайн', description: 'Ҳар ду саҳифа' },
  { key: 'medical_form', label: 'Маълумотномаи тиббӣ (форма 026)', description: 'Аз поликлиника' },
  { key: 'vaccination_card', label: 'Корти эмгузаронӣ', description: 'Тамоми эмгузарониҳо' },
  { key: 'residence_certificate', label: 'Маълумотнома аз ҷойи зист', description: 'Аз маҳалла' },
]

onMounted(() => {
  appStore.loadRegions()
})

async function onResidenceRegionChange(regionId: number) {
  form.residence_district_id = null
  const res = await districtsApi.getAll(regionId)
  residenceDistricts.value = res.data.data
}

async function onSchoolRegionChange(regionId: number) {
  schoolDistrictId.value = null
  form.school_id = null
  schools.value = []
  const res = await districtsApi.getAll(regionId)
  schoolDistricts.value = res.data.data
}

async function onSchoolDistrictChange(districtId: number) {
  form.school_id = null
  const res = await schoolsApi.getAll({ district_id: districtId })
  schools.value = res.data.data
}

function nextStep() {
  // Валидация
  if (currentStep.value === 0) {
    if (!form.child_first_name || !form.child_last_name || !form.child_gender || !form.grade) {
      message.warning('Лутфан ҳамаи майдонҳои ҳатмиро пур кунед')
      return
    }
  }
  if (currentStep.value === 1) {
    if (!form.parent_first_name || !form.parent_last_name || !form.parent_id_number || !form.parent_phone) {
      message.warning('Маълумоти волидайнро пур кунед')
      return
    }
    if (!form.residence_region_id || !form.residence_district_id || !form.residence_address) {
      message.warning('Ҷойи зистро пур кунед')
      return
    }
  }
  if (currentStep.value === 2) {
    if (!form.school_id) {
      message.warning('Мактабро интихоб кунед')
      return
    }
  }
  currentStep.value++
}

async function submitApplication() {
  if (!confirmed.value) return
  submitting.value = true

  // Формати сана
  if (childBirthDate.value) {
    form.child_birth_date = childBirthDate.value.format('YYYY-MM-DD')
  }

  try {
    const res = await applicationsApi.create(form)
    applicationCode.value = res.data.code
    successModal.value = true
    message.success('Ариза сабт шуд!')
  } catch (err: any) {
    const errors = err.response?.data?.errors
    if (errors) {
      Object.values(errors).flat().forEach((msg: any) => message.error(msg))
    } else {
      message.error(err.response?.data?.message || 'Хатои ирсоли ариза')
    }
  } finally {
    submitting.value = false
  }
}
</script>

<style scoped>
.new-application {
  max-width: 800px;
  margin: 0 auto;
}

.subtitle {
  color: #666;
  margin-bottom: 24px;
}

.steps-nav {
  margin-bottom: 32px;
}

.step-content {
  min-height: 300px;
  padding: 24px 0;
}

.step-actions {
  display: flex;
  justify-content: space-between;
  padding-top: 24px;
  border-top: 1px solid #f0f0f0;
}

.mb-16 {
  margin-bottom: 16px;
}

.note {
  color: #666;
  margin-bottom: 16px;
}
</style>
