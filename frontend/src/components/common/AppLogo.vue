<template>
  <component
    :is="to ? 'router-link' : 'div'"
    :to="to"
    class="app-logo"
    :class="[`app-logo--${variant}`, { 'app-logo--clickable': !!to }]"
  >
    <span class="app-logo__mark" :style="{ width: size + 'px', height: size + 'px' }">
      <svg viewBox="0 0 64 64" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
        <defs>
          <linearGradient :id="gradId" x1="0" y1="0" x2="0" y2="64" gradientUnits="userSpaceOnUse">
            <stop offset="0" stop-color="#1668DC" />
            <stop offset="1" stop-color="#0B3D91" />
          </linearGradient>
        </defs>
        <path
          d="M32 2 L58 11 V31 C58 47 47 57 32 62 C17 57 6 47 6 31 V11 Z"
          :fill="`url(#${gradId})`"
          stroke="#0A347E"
          stroke-width="1.5"
        />
        <path
          d="M32 9 l1.6 4.1 4.4.3 -3.4 2.8 1.1 4.3 -3.7 -2.4 -3.7 2.4 1.1 -4.3 -3.4 -2.8 4.4 -.3 Z"
          fill="#F5C518"
        />
        <path
          d="M32 28 C28 25 21 25 17 27 V46 C21 44 28 44 32 47 C36 44 43 44 47 46 V27 C43 25 36 25 32 28 Z"
          fill="#FFFFFF"
          stroke="#0A347E"
          stroke-width="1.2"
          stroke-linejoin="round"
        />
        <path d="M32 28 V47" stroke="#0A347E" stroke-width="1.2" />
        <path d="M21 31 H29 M21 35 H29 M21 39 H29" stroke="#1668DC" stroke-width="1" stroke-linecap="round" opacity="0.6" />
        <path d="M35 31 H43 M35 35 H43 M35 39 H43" stroke="#1668DC" stroke-width="1" stroke-linecap="round" opacity="0.6" />
      </svg>
    </span>

    <span v-if="showText" class="app-logo__text">
      <span class="app-logo__title">{{ title }}</span>
      <span v-if="subtitle" class="app-logo__subtitle">{{ subtitle }}</span>
    </span>
  </component>
</template>

<script setup lang="ts">
import { computed } from 'vue'

interface Props {
  /** Андозаи нишон (px) */
  size?: number
  /** Намоиши матн дар паҳлӯи нишон */
  showText?: boolean
  /** Сарлавҳаи асосӣ */
  title?: string
  /** Зерсарлавҳа */
  subtitle?: string
  /** light = матни сафед (фони торик), dark = матни торик (фони равшан) */
  variant?: 'light' | 'dark'
  /** Масири навигатсия (агар клик кардан лозим бошад) */
  to?: string
}

const props = withDefaults(defineProps<Props>(), {
  size: 40,
  showText: true,
  title: 'МТМУ',
  subtitle: 'Қабули хонандагон',
  variant: 'dark',
  to: '',
})

// Барои ҳар як ҳолат gradient-и беназир (то конфликт нашавад)
const gradId = computed(() => `mtmu-grad-${Math.random().toString(36).slice(2, 8)}`)
</script>

<style scoped>
.app-logo {
  display: inline-flex;
  align-items: center;
  gap: 12px;
  text-decoration: none;
  user-select: none;
}

.app-logo--clickable {
  cursor: pointer;
}

.app-logo__mark {
  display: inline-flex;
  flex-shrink: 0;
  filter: drop-shadow(0 2px 6px rgba(11, 61, 145, 0.25));
}

.app-logo__mark svg {
  width: 100%;
  height: 100%;
}

.app-logo__text {
  display: flex;
  flex-direction: column;
  line-height: 1.15;
}

.app-logo__title {
  font-weight: 800;
  font-size: 18px;
  letter-spacing: 0.5px;
}

.app-logo__subtitle {
  font-size: 11px;
  opacity: 0.75;
  font-weight: 500;
}

/* Вариантҳои ранг */
.app-logo--dark .app-logo__title {
  color: var(--brand-heading, #0a347e);
}
.app-logo--dark .app-logo__subtitle {
  color: var(--brand-muted, #64748b);
}

.app-logo--light .app-logo__title {
  color: #ffffff;
}
.app-logo--light .app-logo__subtitle {
  color: rgba(255, 255, 255, 0.8);
}
</style>
