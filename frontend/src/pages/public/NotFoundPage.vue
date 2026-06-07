<template>
  <div class="not-found-page">
    <AppLogo :size="64" :show-text="false" class="mb-24" />
    <a-result status="404" title="404" sub-title="Саҳифа ёфт нашуд">
      <template #extra>
        <a-button type="primary" @click="$router.push('/')">
          Ба саҳифаи асосӣ
        </a-button>
      </template>
    </a-result>
  </div>
</template>

<script setup lang="ts">
import AppLogo from '@/components/common/AppLogo.vue'
</script>

<style scoped>
.not-found-page {
  min-height: 100vh;
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  background: var(--brand-bg);
}
</style>
