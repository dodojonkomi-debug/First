<template>
  <div class="auth-shell">
    <!-- Тарафи чап — брендинг -->
    <aside class="auth-shell__aside">
      <div class="mesh-bg">
        <span class="orb orb-1 anim-blob"></span>
        <span class="orb orb-2 anim-blob"></span>
        <span class="orb orb-3 anim-blob"></span>
      </div>

      <div class="auth-shell__aside-top">
        <AppLogo :size="48" variant="light" title="МТМУ" subtitle="Вазорати маориф ва илм" />
      </div>

      <div class="auth-shell__aside-content">
        <h1 class="anim-fade-up">
          Системаи қабули хонандагон ба <span class="gradient-text">синфҳои 0 ва 1</span>
        </h1>
        <p class="anim-fade-up" style="animation-delay: 0.1s">
          Платформаи ягонаи рақамии қабул барои Ҷумҳурии Тоҷикистон —
          шаффоф, осон ва дастрас барои ҳама.
        </p>

        <ul class="auth-shell__features">
          <li v-for="(f, i) in features" :key="i" :data-stagger="''" :style="{ '--i': i + 2 }">
            <span class="dot"><component :is="f.icon" /></span>
            {{ f.text }}
          </li>
        </ul>
      </div>

      <div class="auth-shell__aside-footer">
        © {{ year }} Вазорати маориф ва илми Ҷумҳурии Тоҷикистон
      </div>
    </aside>

    <!-- Тарафи рост — форма -->
    <main class="auth-shell__main">
      <div class="auth-card anim-fade-up">
        <div class="auth-card__head">
          <AppLogo :size="56" :show-text="false" class="auth-card__logo anim-float" />
          <h2>{{ title }}</h2>
          <p v-if="subtitle">{{ subtitle }}</p>
        </div>

        <slot />
      </div>
    </main>
  </div>
</template>

<script setup lang="ts">
import AppLogo from './AppLogo.vue'
import {
  ThunderboltOutlined,
  SafetyCertificateOutlined,
  CloudUploadOutlined,
} from '@ant-design/icons-vue'

defineProps<{ title: string; subtitle?: string }>()
const year = new Date().getFullYear()

const features = [
  { icon: ThunderboltOutlined, text: 'Ирсоли ариза дар чанд дақиқа' },
  { icon: SafetyCertificateOutlined, text: 'Тафтиши статус тавассути коди ариза' },
  { icon: CloudUploadOutlined, text: 'Боргузории бехатари ҳуҷҷатҳо' },
]
</script>

<style scoped>
.auth-shell__aside-top { position: relative; z-index: 1; }
.auth-card__logo { display: inline-flex; justify-content: center; }
</style>
