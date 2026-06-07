import type { ThemeConfig } from 'ant-design-vue/es/config-provider/context'

// ===== ТОКЕНҲОИ БРЕНД =====
// Манбаи ягонаи ранг барои тамоми система (CSS + Ant Design)
export const brand = {
  primary: '#1668DC',
  primaryDark: '#0B3D91',
  primaryDeep: '#0A347E',
  gold: '#F5C518',
  heading: '#0A347E',
  text: '#1F2937',
  muted: '#64748B',
  bg: '#F4F7FB',
  bgElevated: '#FFFFFF',
  border: '#E2E8F0',
  success: '#16A34A',
  warning: '#F59E0B',
  error: '#DC2626',
  info: '#1668DC',
  // Градиентҳои бренд
  gradientBrand: 'linear-gradient(135deg, #1668DC 0%, #0B3D91 100%)',
  gradientDeep: 'linear-gradient(135deg, #0A347E 0%, #061E4D 100%)',
  gradientSoft: 'linear-gradient(135deg, #EAF2FF 0%, #DCE9FF 100%)',
}

// ===== ТЕМАИ ANT DESIGN =====
export const antdTheme: ThemeConfig = {
  token: {
    colorPrimary: brand.primary,
    colorInfo: brand.info,
    colorSuccess: brand.success,
    colorWarning: brand.warning,
    colorError: brand.error,
    colorTextHeading: brand.heading,
    colorText: brand.text,
    colorTextSecondary: brand.muted,
    colorBgLayout: brand.bg,
    colorBorder: brand.border,
    borderRadius: 10,
    fontSize: 15,
    fontFamily: "'Inter', 'Vazirmatn', 'Segoe UI', -apple-system, BlinkMacSystemFont, sans-serif",
    controlHeight: 40,
    boxShadow: '0 4px 16px rgba(11, 61, 145, 0.08)',
    wireframe: false,
  },
  components: {
    Layout: {
      headerBg: '#FFFFFF',
      siderBg: '#0A347E',
      bodyBg: brand.bg,
      headerHeight: 64,
    },
    Menu: {
      darkItemBg: 'transparent',
      darkItemSelectedBg: 'rgba(255,255,255,0.14)',
      darkItemHoverBg: 'rgba(255,255,255,0.08)',
      darkItemColor: 'rgba(255,255,255,0.78)',
      darkItemSelectedColor: '#FFFFFF',
      itemBorderRadius: 8,
      itemMarginInline: 10,
    },
    Button: {
      controlHeight: 40,
      controlHeightLG: 48,
      fontWeight: 600,
      primaryShadow: '0 6px 16px rgba(22, 104, 220, 0.28)',
    },
    Card: {
      borderRadiusLG: 14,
      boxShadowTertiary: '0 4px 20px rgba(15, 23, 42, 0.06)',
    },
    Input: {
      controlHeight: 42,
      borderRadius: 10,
    },
    Select: {
      controlHeight: 42,
      borderRadius: 10,
    },
    Table: {
      headerBg: '#F1F5FB',
      headerColor: brand.heading,
      borderColor: brand.border,
      rowHoverBg: '#F8FBFF',
    },
    Tag: {
      borderRadiusSM: 6,
    },
    Steps: {
      colorPrimary: brand.primary,
    },
  },
}

// ===== НАВИШТАҶОТИ СТАТУСҲО (ягона) =====
export const statusMeta: Record<string, { label: string; color: string; antType: 'warning' | 'info' | 'success' | 'error' }> = {
  pending: { label: 'Дар интизор', color: 'orange', antType: 'warning' },
  review: { label: 'Дар баррасӣ', color: 'blue', antType: 'info' },
  approved: { label: 'Қабул шуд', color: 'green', antType: 'success' },
  rejected: { label: 'Рад шуд', color: 'red', antType: 'error' },
}

export const roleMeta: Record<string, { label: string; color: string }> = {
  superadmin: { label: 'Суперадмин (Вазорат)', color: 'gold' },
  admin_region: { label: 'Маъмури вилоят', color: 'purple' },
  admin_district: { label: 'Маъмури ноҳия', color: 'blue' },
  admin_school: { label: 'Маъмури мактаб', color: 'cyan' },
  parent: { label: 'Волидайн', color: 'green' },
}
