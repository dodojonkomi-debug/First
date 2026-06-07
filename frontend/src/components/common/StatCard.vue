<template>
  <div class="stat-card hover-lift" :style="{ '--accent': color }">
    <div class="stat-card__glow" :style="{ background: color }" />
    <div class="stat-inner">
      <span class="stat-icon icon-chip" :style="{ background: iconBg, color: color }">
        <slot name="icon" />
      </span>
      <div class="stat-meta">
        <div class="stat-number" :style="{ color: color }">{{ display }}</div>
        <div class="stat-label">{{ label }}</div>
      </div>
    </div>
    <div v-if="trend !== undefined" class="stat-trend" :class="trend >= 0 ? 'up' : 'down'">
      <ArrowUpOutlined v-if="trend >= 0" />
      <ArrowDownOutlined v-else />
      {{ Math.abs(trend) }}%
    </div>
  </div>
</template>

<script setup lang="ts">
import { computed } from 'vue'
import { ArrowUpOutlined, ArrowDownOutlined } from '@ant-design/icons-vue'
import { useCountUp } from '@/composables/useCountUp'

const props = withDefaults(
  defineProps<{
    value: number | string
    label: string
    color?: string
    bg?: string
    iconBg?: string
    trend?: number
  }>(),
  {
    color: '#1668DC',
    bg: '#FFFFFF',
    iconBg: 'rgba(22,104,220,0.10)',
  }
)

const numericValue = computed(() => (typeof props.value === 'number' ? props.value : Number(props.value) || 0))
const isNumber = computed(() => !isNaN(numericValue.value) && typeof props.value !== 'string')

const { display: counted } = useCountUp(() => numericValue.value)
const display = computed(() => (isNumber.value ? counted.value : String(props.value)))
</script>

<style scoped>
.stat-card {
  position: relative;
  overflow: hidden;
  border-radius: var(--radius-md);
  border: 1px solid var(--brand-border);
  background: #fff;
}
.stat-card::before {
  content: '';
  position: absolute;
  left: 0;
  top: 0;
  bottom: 0;
  width: 4px;
  background: var(--accent);
  opacity: 0.9;
}
.stat-card__glow {
  position: absolute;
  width: 120px;
  height: 120px;
  border-radius: 50%;
  filter: blur(50px);
  opacity: 0.16;
  top: -40px;
  right: -30px;
}
.stat-inner {
  position: relative;
  display: flex;
  align-items: center;
  gap: 16px;
  padding: 20px;
}
.stat-icon {
  width: 54px;
  height: 54px;
  font-size: 24px;
  flex-shrink: 0;
}
.stat-number {
  font-size: 30px;
  font-weight: 800;
  line-height: 1.1;
  font-variant-numeric: tabular-nums;
}
.stat-label {
  font-size: 13px;
  color: var(--brand-muted);
  font-weight: 500;
  margin-top: 2px;
}
.stat-trend {
  position: absolute;
  top: 14px;
  right: 14px;
  font-size: 12px;
  font-weight: 700;
  padding: 2px 8px;
  border-radius: 999px;
}
.stat-trend.up { color: var(--brand-success); background: rgba(22, 163, 74, 0.1); }
.stat-trend.down { color: var(--brand-error); background: rgba(220, 38, 38, 0.1); }
</style>
