<template>
  <div class="home-page">
    <!-- Навбар -->
    <header class="home-nav" :class="{ scrolled }">
      <div class="container home-nav__inner">
        <AppLogo :size="44" to="/" title="МТМУ" subtitle="Қабули хонандагон" />
        <nav class="home-nav__actions">
          <a-button type="text" @click="$router.push('/check-status')">Тафтиши статус</a-button>
          <a-button type="text" @click="$router.push('/admin/login')">Вуруди маъмур</a-button>
          <a-button type="primary" class="btn-shine" @click="$router.push('/login')">Ариза додан</a-button>
        </nav>
      </div>
    </header>

    <!-- Hero -->
    <section class="hero">
      <div class="mesh-bg">
        <span class="orb orb-1 anim-blob"></span>
        <span class="orb orb-2 anim-blob"></span>
        <span class="orb orb-3 anim-blob"></span>
      </div>

      <div class="container hero__grid">
        <div class="hero__content">
          <div class="hero__badge anim-fade-up">
            <span class="live-dot"></span>
            Қабул барои соли таҳсилии {{ currentYear }}–{{ currentYear + 1 }}
          </div>
          <h1 class="anim-fade-up" style="animation-delay: 0.08s">
            Қабули хонандагон ба <span class="gradient-text">синфҳои 0 ва 1</span>
          </h1>
          <p class="subtitle anim-fade-up" style="animation-delay: 0.16s">
            Вазорати маориф ва илми Ҷумҳурии Тоҷикистон
          </p>
          <p class="description anim-fade-up" style="animation-delay: 0.24s">
            Платформаи ягонаи рақамӣ: пуркунии ариза, боргузории ҳуҷҷатҳо ва
            пайгирии натиҷа — шаффоф, осон ва дастрас аз ҳар ҷо.
          </p>

          <div class="hero__actions anim-fade-up" style="animation-delay: 0.32s">
            <a-button type="primary" size="large" class="btn-shine" @click="$router.push('/login')">
              <template #icon><EditOutlined /></template>
              Ариза додан
            </a-button>
            <a-button size="large" ghost @click="$router.push('/check-status')">
              <template #icon><SearchOutlined /></template>
              Тафтиши статус
            </a-button>
          </div>

          <div class="hero__period anim-fade-up" style="animation-delay: 0.4s">
            <ClockCircleOutlined />
            <span>Санаи қабул: <strong>1 август – 1 сентябр</strong></span>
          </div>
        </div>

        <!-- Карточкаи декоративӣ -->
        <div class="hero__visual anim-float">
          <div class="glass hero__card">
            <div class="hero__card-head">
              <AppLogo :size="40" :show-text="false" />
              <span class="hero__chip">Ариза №2847</span>
            </div>
            <div class="hero__row">
              <span>Кӯдак</span><strong>Алиев Фаррух</strong>
            </div>
            <div class="hero__row">
              <span>Мактаб</span><strong>Мактаби №12</strong>
            </div>
            <div class="hero__row">
              <span>Синф</span><strong>Синфи 1</strong>
            </div>
            <div class="hero__status">
              <CheckCircleFilled /> Қабул шуд
            </div>
            <div class="hero__progress"><span></span></div>
          </div>
          <div class="hero__float-icon hero__float-icon--1 anim-float-slow"><SafetyCertificateOutlined /></div>
          <div class="hero__float-icon hero__float-icon--2 anim-float-slow"><FileDoneOutlined /></div>
        </div>
      </div>
    </section>

    <!-- Статистика (тасмавӣ) -->
    <section class="trust">
      <div class="container trust__grid">
        <div v-for="(t, i) in trust" :key="i" class="trust__item" v-reveal="{ delay: i * 0.08 }">
          <component :is="t.icon" class="trust__icon" />
          <div class="trust__num">{{ t.value }}</div>
          <div class="trust__label">{{ t.label }}</div>
        </div>
      </div>
    </section>

    <!-- Тартиб -->
    <section class="section">
      <div class="container">
        <div class="section__head" v-reveal>
          <span class="section__eyebrow">Тартиб</span>
          <h2 class="section__title">Чор қадам то қабул</h2>
          <p class="section__sub">Раванди содда ва фаҳмо барои ҳар як волид</p>
        </div>
        <a-row :gutter="[24, 24]">
          <a-col :xs="24" :sm="12" :md="8" :lg="8" v-for="(step, i) in steps" :key="i">
            <div class="step-item hover-lift" v-reveal="{ delay: i * 0.07, y: 36 }">
              <div class="step-item__num">{{ i + 1 }}</div>
              <component :is="step.icon" class="step-item__icon" />
              <h3>{{ step.title }}</h3>
              <p>{{ step.desc }}</p>
            </div>
          </a-col>
        </a-row>
      </div>
    </section>

    <!-- Ҳуҷҷатҳо -->
    <section class="section section--alt">
      <div class="container">
        <div class="section__head" v-reveal>
          <span class="section__eyebrow">Ҳуҷҷатҳо</span>
          <h2 class="section__title">Чӣ лозим аст?</h2>
          <p class="section__sub">Аксҳои ҳуҷҷатҳо ҳамчун PDF нигоҳ дошта мешаванд</p>
        </div>
        <a-row :gutter="[16, 16]">
          <a-col :xs="24" :sm="12" :md="8" v-for="(doc, i) in requiredDocs" :key="doc.key">
            <div class="doc-card hover-lift" v-reveal="{ delay: i * 0.06 }">
              <div class="doc-card__icon icon-chip"><component :is="doc.icon" /></div>
              <h3>{{ doc.title }}</h3>
              <p>{{ doc.description }}</p>
            </div>
          </a-col>
        </a-row>
      </div>
    </section>

    <!-- CTA -->
    <section class="cta">
      <div class="mesh-bg"><span class="orb orb-1 anim-blob"></span></div>
      <div class="container cta__inner" v-reveal="{ scale: 0.97 }">
        <div>
          <h2>Омодаед, ки ариза диҳед?</h2>
          <p>Бақайдгирӣ ройгон аст ва танҳо чанд дақиқа вақт мегирад.</p>
        </div>
        <a-button type="primary" size="large" class="btn-shine" @click="$router.push('/register')">
          <template #icon><RocketOutlined /></template>
          Ҳозир оғоз кунед
        </a-button>
      </div>
    </section>

    <!-- Футер -->
    <footer class="home-footer">
      <div class="container home-footer__inner">
        <AppLogo :size="40" variant="light" title="МТМУ" subtitle="Вазорати маориф ва илм" />
        <p>© {{ currentYear }} Вазорати маориф ва илми Ҷумҳурии Тоҷикистон</p>
      </div>
    </footer>
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted, onUnmounted } from 'vue'
import AppLogo from '@/components/common/AppLogo.vue'
import {
  FileTextOutlined,
  EditOutlined,
  SearchOutlined,
  ClockCircleOutlined,
  CheckCircleFilled,
  SafetyCertificateOutlined,
  FileDoneOutlined,
  RocketOutlined,
  UserAddOutlined,
  FormOutlined,
  CloudUploadOutlined,
  SolutionOutlined,
  IdcardOutlined,
  MedicineBoxOutlined,
  HeartOutlined,
  HomeOutlined,
  BankOutlined,
  TeamOutlined,
  SafetyOutlined,
} from '@ant-design/icons-vue'

const currentYear = new Date().getFullYear()
const scrolled = ref(false)

const trust = [
  { icon: BankOutlined, value: '1000+', label: 'Мактабҳо' },
  { icon: TeamOutlined, value: '50K+', label: 'Аризаҳо' },
  { icon: SafetyOutlined, value: '100%', label: 'Бехатар' },
  { icon: ClockCircleOutlined, value: '24/7', label: 'Дастрас' },
]

const steps = [
  { icon: UserAddOutlined, title: 'Бақайдгирӣ', desc: 'Ворид кардани маълумоти шахсӣ ва рақами телефон' },
  { icon: FormOutlined, title: 'Пур кардани ариза', desc: 'Маълумоти кӯдак, волидайн, мактаб ва синф' },
  { icon: CloudUploadOutlined, title: 'Боргузории ҳуҷҷатҳо', desc: 'Аксҳои шаҳодатнома, шиноснома ва маълумотномаи тиббӣ' },
  { icon: SolutionOutlined, title: 'Баррасӣ', desc: 'Мақомоти маориф аризаро тафтиш мекунад' },
  { icon: CheckCircleFilled, title: 'Натиҷа', desc: 'Қабул ё рад — тавассути коди ариза' },
  { icon: SearchOutlined, title: 'Пайгирӣ', desc: 'Ҳар лаҳза статуси аризаро назорат кунед' },
]

const requiredDocs = [
  { key: 'birth', icon: FileTextOutlined, title: 'Шаҳодатномаи таваллуд', description: 'Акси рангини шаҳодатномаи таваллуди кӯдак' },
  { key: 'parent_id', icon: IdcardOutlined, title: 'Шиноснома/ШҲ-и волидайн', description: 'Акси саҳифаҳои шиноснома ё шаҳодатномаи ҳуқуқӣ' },
  { key: 'medical', icon: MedicineBoxOutlined, title: 'Маълумотномаи тиббӣ', description: 'Форма 026 аз поликлиника' },
  { key: 'vaccination', icon: HeartOutlined, title: 'Корти эмгузаронӣ', description: 'Акси корти эмгузаронии кӯдак' },
  { key: 'residence', icon: HomeOutlined, title: 'Маълумотнома аз ҷойи зист', description: 'Тасдиқи ҷойи зист аз маҳалла' },
]

function onScroll() {
  scrolled.value = window.scrollY > 20
}

onMounted(() => window.addEventListener('scroll', onScroll, { passive: true }))
onUnmounted(() => window.removeEventListener('scroll', onScroll))
</script>

<style scoped>
.home-page { background: #fff; }

/* Навбар */
.home-nav {
  position: sticky;
  top: 0;
  z-index: 100;
  background: rgba(255, 255, 255, 0.75);
  backdrop-filter: blur(12px);
  border-bottom: 1px solid transparent;
  transition: background 0.3s, border-color 0.3s, box-shadow 0.3s;
}
.home-nav.scrolled {
  background: rgba(255, 255, 255, 0.92);
  border-bottom-color: var(--brand-border);
  box-shadow: 0 4px 20px rgba(15, 23, 42, 0.05);
}
.home-nav__inner {
  display: flex;
  align-items: center;
  justify-content: space-between;
  height: 70px;
}
.home-nav__actions { display: flex; align-items: center; gap: 8px; }

/* Hero */
.hero {
  position: relative;
  background: var(--gradient-deep);
  color: #fff;
  padding: 72px 0 90px;
  overflow: hidden;
}
.hero__grid {
  position: relative;
  z-index: 1;
  display: grid;
  grid-template-columns: 1.1fr 0.9fr;
  gap: 48px;
  align-items: center;
}
.hero__badge {
  display: inline-flex;
  align-items: center;
  gap: 10px;
  background: rgba(255, 255, 255, 0.1);
  border: 1px solid rgba(255, 255, 255, 0.18);
  padding: 8px 16px;
  border-radius: 999px;
  font-size: 13px;
  font-weight: 600;
  margin-bottom: 22px;
}
.hero h1 {
  font-size: 46px;
  font-weight: 800;
  line-height: 1.12;
  margin-bottom: 14px;
  letter-spacing: -0.5px;
}
.hero .subtitle { font-size: 18px; opacity: 0.9; margin-bottom: 12px; }
.hero .description { font-size: 16px; opacity: 0.82; margin-bottom: 30px; max-width: 540px; }
.hero__actions { display: flex; gap: 14px; flex-wrap: wrap; margin-bottom: 26px; }
.hero__period {
  display: inline-flex;
  align-items: center;
  gap: 10px;
  background: rgba(245, 197, 24, 0.12);
  border: 1px solid rgba(245, 197, 24, 0.3);
  color: #fde68a;
  padding: 10px 18px;
  border-radius: 999px;
  font-size: 14px;
}

/* Hero visual карточка */
.hero__visual { position: relative; }
.hero__card {
  border-radius: 20px;
  padding: 24px;
  color: #fff;
  box-shadow: var(--shadow-lg);
}
.hero__card-head {
  display: flex;
  align-items: center;
  justify-content: space-between;
  margin-bottom: 18px;
}
.hero__chip {
  font-size: 12px;
  background: rgba(255, 255, 255, 0.18);
  padding: 4px 10px;
  border-radius: 999px;
}
.hero__row {
  display: flex;
  justify-content: space-between;
  padding: 10px 0;
  border-bottom: 1px solid rgba(255, 255, 255, 0.12);
  font-size: 14px;
}
.hero__row span { opacity: 0.7; }
.hero__status {
  display: inline-flex;
  align-items: center;
  gap: 8px;
  margin-top: 16px;
  color: #86efac;
  font-weight: 700;
}
.hero__progress {
  margin-top: 14px;
  height: 6px;
  border-radius: 999px;
  background: rgba(255, 255, 255, 0.15);
  overflow: hidden;
}
.hero__progress span {
  display: block;
  height: 100%;
  width: 100%;
  background: linear-gradient(90deg, #f5c518, #86efac);
  border-radius: 999px;
}
.hero__float-icon {
  position: absolute;
  width: 56px;
  height: 56px;
  border-radius: 16px;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 26px;
  background: #fff;
  color: var(--brand-primary);
  box-shadow: var(--shadow-md);
}
.hero__float-icon--1 { top: -22px; right: 18px; color: #16a34a; }
.hero__float-icon--2 { bottom: 12px; left: -22px; color: #f59e0b; }

/* Trust */
.trust {
  background: #fff;
  border-bottom: 1px solid var(--brand-border);
}
.trust__grid {
  display: grid;
  grid-template-columns: repeat(4, 1fr);
  gap: 16px;
  padding: 36px 24px;
}
.trust__item { text-align: center; }
.trust__icon { font-size: 26px; color: var(--brand-primary); margin-bottom: 8px; }
.trust__num { font-size: 28px; font-weight: 800; color: var(--brand-heading); }
.trust__label { font-size: 13px; color: var(--brand-muted); }

/* Секцияҳо */
.section { padding: 80px 0; }
.section--alt { background: var(--brand-bg); }
.section__head { text-align: center; max-width: 600px; margin: 0 auto 48px; }
.section__eyebrow {
  display: inline-block;
  font-size: 13px;
  font-weight: 700;
  letter-spacing: 1px;
  text-transform: uppercase;
  color: var(--brand-primary);
  margin-bottom: 10px;
}
.section__title { font-size: 30px; font-weight: 800; color: var(--brand-heading); margin-bottom: 8px; }
.section__sub { color: var(--brand-muted); font-size: 15px; }

/* Қадамҳо */
.step-item {
  position: relative;
  background: #fff;
  border: 1px solid var(--brand-border);
  border-radius: var(--radius-md);
  padding: 28px 24px;
  height: 100%;
}
.step-item__num {
  position: absolute;
  top: 18px;
  right: 18px;
  font-size: 40px;
  font-weight: 800;
  color: rgba(22, 104, 220, 0.08);
  line-height: 1;
}
.step-item__icon {
  font-size: 30px;
  color: var(--brand-primary);
  margin-bottom: 16px;
  display: block;
}
.step-item h3 { font-size: 17px; color: var(--brand-heading); margin-bottom: 8px; }
.step-item p { color: var(--brand-muted); font-size: 14px; }

/* Ҳуҷҷатҳо */
.doc-card {
  height: 100%;
  text-align: center;
  background: #fff;
  border: 1px solid var(--brand-border);
  border-radius: var(--radius-md);
  padding: 28px 22px;
}
.doc-card__icon {
  width: 58px;
  height: 58px;
  font-size: 26px;
  margin: 0 auto 16px;
}
.doc-card h3 { font-size: 16px; color: var(--brand-heading); margin-bottom: 8px; }
.doc-card p { color: var(--brand-muted); font-size: 14px; }

/* CTA */
.cta {
  position: relative;
  background: var(--gradient-brand);
  color: #fff;
  padding: 60px 0;
  overflow: hidden;
}
.cta .mesh-bg .orb { opacity: 0.25; }
.cta__inner {
  position: relative;
  z-index: 1;
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 24px;
  flex-wrap: wrap;
}
.cta h2 { font-size: 28px; font-weight: 800; margin-bottom: 6px; }
.cta p { opacity: 0.92; }

/* Футер */
.home-footer { background: #061e4d; color: rgba(255, 255, 255, 0.7); padding: 32px 0; }
.home-footer__inner {
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 16px;
  flex-wrap: wrap;
}

@media (max-width: 900px) {
  .hero__grid { grid-template-columns: 1fr; }
  .hero__visual { display: none; }
}
@media (max-width: 768px) {
  .hero h1 { font-size: 32px; }
  .home-nav__actions .ant-btn:not(.ant-btn-primary) { display: none; }
  .section { padding: 56px 0; }
  .trust__grid { grid-template-columns: repeat(2, 1fr); gap: 24px; }
}
</style>
