<template>
  <AuthShell title="Вуруди волидайн" subtitle="Барои ирсол ва тафтиши ариза">
    <a-form :model="form" @finish="handleLogin" layout="vertical">
      <a-form-item
        label="Рақами телефон"
        name="phone"
        :rules="[{ required: true, message: 'Рақами телефонро ворид кунед' }]"
      >
        <a-input v-model:value="form.phone" placeholder="+992XXXXXXXXX" size="large">
          <template #prefix><PhoneOutlined /></template>
        </a-input>
      </a-form-item>

      <a-form-item
        label="Рамз"
        name="password"
        :rules="[{ required: true, message: 'Рамзро ворид кунед' }]"
      >
        <a-input-password v-model:value="form.password" placeholder="Рамзи шумо" size="large">
          <template #prefix><LockOutlined /></template>
        </a-input-password>
      </a-form-item>

      <a-form-item>
        <a-button type="primary" html-type="submit" size="large" block :loading="loading">
          Ворид шудан
        </a-button>
      </a-form-item>
    </a-form>

    <a-divider plain>ё</a-divider>

    <a-button block size="large" @click="$router.push('/register')">
      Бақайдгирӣ (Ҳисоби нав)
    </a-button>

    <div class="auth-card__links">
      <a-button type="link" @click="$router.push('/check-status')">
        Тафтиши статус
      </a-button>
      <a-button type="link" @click="$router.push('/')">
        Саҳифаи асосӣ
      </a-button>
    </div>
  </AuthShell>
</template>

<script setup lang="ts">
import { reactive, ref } from 'vue'
import { useRouter } from 'vue-router'
import { message } from 'ant-design-vue'
import { useAuthStore } from '@/stores/auth'
import AuthShell from '@/components/common/AuthShell.vue'
import { PhoneOutlined, LockOutlined } from '@ant-design/icons-vue'
import type { LoginParentForm } from '@/types'

const router = useRouter()
const authStore = useAuthStore()
const loading = ref(false)

const form = reactive<LoginParentForm>({ phone: '', password: '' })

async function handleLogin() {
  loading.value = true
  try {
    await authStore.loginParent(form)
    message.success('Шумо бо муваффақият ворид шудед!')
    router.push('/parent')
  } catch (err: any) {
    const msg = err.response?.data?.errors?.phone?.[0] || err.response?.data?.message || 'Хатои вуруд'
    message.error(msg)
  } finally {
    loading.value = false
  }
}
</script>
