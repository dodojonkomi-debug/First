import { ref, watch, onMounted, type Ref } from 'vue'

/**
 * useCountUp — анимацияи ҳисобкунии рақамҳо (0 → target).
 * Истифода:
 *   const { display } = useCountUp(() => stats.total)
 */
export function useCountUp(
  source: () => number,
  options: { duration?: number; decimals?: number } = {}
): { display: Ref<string> } {
  const { duration = 1200, decimals = 0 } = options
  const display = ref('0')
  let rafId = 0

  function run(to: number) {
    cancelAnimationFrame(rafId)
    const from = parseFloat(display.value.replace(/\s/g, '')) || 0
    const start = performance.now()

    function frame(now: number) {
      const progress = Math.min((now - start) / duration, 1)
      // easeOutExpo
      const eased = progress === 1 ? 1 : 1 - Math.pow(2, -10 * progress)
      const value = from + (to - from) * eased
      display.value = value.toLocaleString('ru-RU', {
        minimumFractionDigits: decimals,
        maximumFractionDigits: decimals,
      })
      if (progress < 1) rafId = requestAnimationFrame(frame)
    }

    rafId = requestAnimationFrame(frame)
  }

  onMounted(() => run(source()))
  watch(source, (val) => run(val))

  return { display }
}
