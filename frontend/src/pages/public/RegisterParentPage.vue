<template>
  <AuthShell title="Бақайдгирӣ" subtitle="Сохтани ҳисоби нав барои волидайн">
    <a-form :model="form" @finish="handleRegister" layout="vertical">
      <a-form-item
        label="Ному насаб"
        name="name"
        :rules="[{ required: true, message: 'Ному насабро ворид кунед' }]"
      >
        <a-input v-model:value="form.name" placeholder="Алиев Ахмад" size="large">
          <template #prefix><UserOutlined /></template>
        </a-input>
      </a-form-item>

      <a-form-item
        label="Рақами телефон"
        name="phone"
        :rules="[{ required: true, message: 'Рақами телефонро ворид кунед' }]"
      >
        <a-input v-model:value="form.phone" placeholder="+992XXXXXXXXX" size="large">
          <template #prefix><PhoneOutlined /></template>
        </a-input>
      </a-form-item>

      <a-form-item label="Email (ихтиёрӣ)" name="email">
        <a-input v-model:value="form.email" placeholder="email@example.com" size="large">
          <template #prefix><MailOutlined /></template>
        </a-input>
      </a-form-item>

      <a-row :gutter="12">
        <a-col :span="12">
          <a-form-item
            label="Рамз"
            name="password"
            :rules="[{ required: true, min: 6, message: 'Ҳадди ақал 6 аломат' }]"
          >
            <a-input-password v-model:value="form.password" placeholder="Рамз" size="large" />
          </a-form-item>
        </a-col>
        <a-col :span="12">
          <a-form-item
            label="Такрори рамз"
            name="password_confirmation"
            :rules="[{ required: true, message: 'Рамзро такрор кунед' }]"
          >
            <a-input-password v-model:value="form.password_confirmation" placeholder="Такрор" size="large" />
          </a-form-item>
        </a-col>
      </a-row>

      <a-form-item>
        <a-button type="primary" html-type="submit" size="large" block :loading="loading">
          Бақайдгирӣ
        </a-button>
      </a-form-item>
    </a-form>

    <div class="auth-card__links" style="justify-content: center">
      <a-button type="link" @click="$router.push('/login')">
        Аллакай ҳисоб доред? Ворид шавед
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
import { UserOutlined, PhoneOutlined, MailOutlined } from '@ant-design/icons-vue'
import type { RegisterParentForm } from '@/types'

const router = useRouter()
const authStore = useAuthStore()
const loading = ref(false)

const form = reactive<RegisterParentForm>({
  name: '', phone: '', email: '', password: '', password_confirmation: '',
})

async function handleRegister() {
  if (form.password !== form.password_confirmation) {
    message.error('Рамзҳо мувофиқат намекунанд!')
    return
  }
  loading.value = true
  try {
    await authStore.registerParent(form)
    message.success('Бақайдгирӣ бо муваффақият анҷом ёфт!')
    router.push('/parent')
  } catch (err: any) {
    const errors = err.response?.data?.errors
    if (errors) {
      Object.values(errors).flat().forEach((msg: any) => message.error(msg))
    } else {
      message.error('Хатои бақайдгирӣ')
    }
  } finally {
    loading.value = false
  }
}
</script>
