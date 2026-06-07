<template>
  <AuthShell title="Панели маъмур" subtitle="Вазорати маориф ва илм">
    <a-form :model="form" @finish="handleLogin" layout="vertical">
      <a-form-item
        label="Почтаи электронӣ"
        name="email"
        :rules="[
          { required: true, message: 'Email-ро ворид кунед' },
          { type: 'email', message: 'Формати email нодуруст аст' },
        ]"
      >
        <a-input v-model:value="form.email" placeholder="admin@mtmu.tj" size="large">
          <template #prefix><MailOutlined /></template>
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

    <div class="auth-card__links" style="justify-content: center">
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
import { MailOutlined, LockOutlined } from '@ant-design/icons-vue'
import type { LoginAdminForm } from '@/types'

const router = useRouter()
const authStore = useAuthStore()
const loading = ref(false)

const form = reactive<LoginAdminForm>({ email: '', password: '' })

async function handleLogin() {
  loading.value = true
  try {
    await authStore.loginAdmin(form)
    message.success('Шумо бо муваффақият ворид шудед!')
    router.push('/admin')
  } catch (err: any) {
    const msg = err.response?.data?.errors?.email?.[0] || err.response?.data?.message || 'Хатои вуруд'
    message.error(msg)
  } finally {
    loading.value = false
  }
}
</script>
