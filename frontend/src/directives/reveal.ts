import type { Directive, DirectiveBinding } from 'vue'

/**
 * v-reveal — анимацияи зоҳиршавӣ ҳангоми скролл (scroll reveal).
 * Истифода:
 *   v-reveal                  → fade + slide up
 *   v-reveal="{ y: 40, delay: 0.2, x: 0, scale: 0.96 }"
 *
 * Агар GSAP мавҷуд бошад — аз он истифода мебарад, вагарна CSS fallback.
 */

interface RevealOptions {
  y?: number
  x?: number
  scale?: number
  delay?: number
  duration?: number
}

const observerMap = new WeakMap<Element, IntersectionObserver>()

async function animateIn(el: HTMLElement, opts: RevealOptions) {
  const { y = 28, x = 0, scale = 1, delay = 0, duration = 0.7 } = opts
  try {
    const gsap = (await import('gsap')).default
    gsap.fromTo(
      el,
      { autoAlpha: 0, y, x, scale },
      {
        autoAlpha: 1,
        y: 0,
        x: 0,
        scale: 1,
        duration,
        delay,
        ease: 'power3.out',
        clearProps: 'transform',
      }
    )
  } catch {
    // Fallback бе GSAP
    el.style.transition = `opacity ${duration}s ease ${delay}s, transform ${duration}s cubic-bezier(0.22,1,0.36,1) ${delay}s`
    requestAnimationFrame(() => {
      el.style.opacity = '1'
      el.style.transform = 'none'
    })
  }
}

function prepare(el: HTMLElement, opts: RevealOptions) {
  const { y = 28, x = 0, scale = 1 } = opts
  el.style.opacity = '0'
  el.style.transform = `translate3d(${x}px, ${y}px, 0) scale(${scale})`
  el.style.willChange = 'opacity, transform'
}

export const reveal: Directive<HTMLElement, RevealOptions | undefined> = {
  mounted(el: HTMLElement, binding: DirectiveBinding<RevealOptions | undefined>) {
    const opts = binding.value || {}

    // Эҳтиром ба reduce-motion
    if (window.matchMedia?.('(prefers-reduced-motion: reduce)').matches) {
      el.style.opacity = '1'
      return
    }

    prepare(el, opts)

    const io = new IntersectionObserver(
      (entries) => {
        entries.forEach((entry) => {
          if (entry.isIntersecting) {
            animateIn(el, opts)
            io.unobserve(el)
            observerMap.delete(el)
          }
        })
      },
      { threshold: 0.12, rootMargin: '0px 0px -40px 0px' }
    )

    io.observe(el)
    observerMap.set(el, io)
  },
  unmounted(el: HTMLElement) {
    const io = observerMap.get(el)
    if (io) {
      io.disconnect()
      observerMap.delete(el)
    }
  },
}

export default reveal
