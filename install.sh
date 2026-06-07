#!/usr/bin/env bash
# ============================================================
#  МТМУ — Системаи қабули хонандагон
#  Инсталлятори автоматии "под ключ"
#
#  Истифода:
#    ./install.sh              # интерактивӣ (худаш мепурсад)
#    ./install.sh --docker     # насб бо Docker Compose
#    ./install.sh --local      # насби локалӣ (php + node + mysql)
# ============================================================
set -e

# --- Рангҳо ---
G='\033[0;32m'; Y='\033[1;33m'; R='\033[0;31m'; B='\033[0;34m'; N='\033[0m'
ok()   { echo -e "${G}✔${N} $1"; }
info() { echo -e "${B}➤${N} $1"; }
warn() { echo -e "${Y}⚠${N} $1"; }
err()  { echo -e "${R}✘${N} $1"; }

ROOT="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
MODE="${1:-}"

banner() {
  echo -e "${B}"
  echo "============================================================"
  echo "   МТМУ — Системаи автоматии қабули хонандагон"
  echo "   Вазорати маориф ва илми Ҷумҳурии Тоҷикистон"
  echo "============================================================"
  echo -e "${N}"
}

has() { command -v "$1" >/dev/null 2>&1; }

# ---------- НАСБ БО DOCKER ----------
install_docker() {
  info "Усули насб: Docker Compose"

  if ! has docker; then
    err "Docker ёфт нашуд. Лутфан Docker насб кунед: https://docs.docker.com/get-docker/"
    exit 1
  fi

  # docker compose ё docker-compose
  if docker compose version >/dev/null 2>&1; then
    DC="docker compose"
  elif has docker-compose; then
    DC="docker-compose"
  else
    err "Docker Compose ёфт нашуд."
    exit 1
  fi
  ok "Docker ва Compose мавҷуданд"

  cd "$ROOT"
  if [ ! -f .env ]; then
    cp .env.example .env
    ok "Файли .env сохта шуд (рамзи базаро дар продакшен тағйир диҳед!)"
  fi

  info "Сохтан ва оғози контейнерҳо... (метавонад чанд дақиқа гирад)"
  $DC up -d --build

  echo ""
  ok "Насб анҷом ёфт!"
  echo -e "   ${G}Frontend:${N} http://localhost:8080"
  echo -e "   ${G}API:${N}      http://localhost:8080/api"
  echo ""
  info "Логҳо:    $DC logs -f"
  info "Қатъ:     $DC down"
}

# ---------- НАСБИ ЛОКАЛӢ ----------
install_local() {
  info "Усули насб: Локалӣ (php + composer + node + npm)"

  local missing=0
  for tool in php composer node npm; do
    if has "$tool"; then ok "$tool мавҷуд аст"; else err "$tool ёфт нашуд"; missing=1; fi
  done
  if [ "$missing" -eq 1 ]; then
    err "Лутфан асбобҳои намерасидаро насб кунед ва аз нав кӯшиш кунед."
    echo "   PHP 8.2+, Composer, Node.js 20+, npm, MySQL 8"
    exit 1
  fi

  # --- Backend ---
  echo ""
  info "[1/2] Backend (Laravel)..."
  cd "$ROOT/backend"
  [ -f .env ] || { cp .env.example .env; ok ".env сохта шуд"; }

  info "composer install..."
  composer install --no-interaction --prefer-dist

  grep -q "^APP_KEY=base64" .env || php artisan key:generate

  info "Миграсия ва пур кардани база..."
  if php artisan migrate --seed --force; then
    ok "База тайёр шуд"
  else
    warn "Миграсия ноком шуд — танзимоти базаро дар backend/.env санҷед (DB_DATABASE, DB_USERNAME, DB_PASSWORD)"
  fi

  php artisan storage:link 2>/dev/null || true
  ok "Backend тайёр аст"

  # --- Frontend ---
  echo ""
  info "[2/2] Frontend (Vue 3)..."
  cd "$ROOT/frontend"
  info "npm install..."
  npm install

  ok "Frontend тайёр аст"

  echo ""
  ok "Насб анҷом ёфт!"
  echo -e "   Барои оғоз ду терминалро кушоед:"
  echo -e "   ${G}1)${N} cd backend  && php artisan serve     ${B}# http://localhost:8000${N}"
  echo -e "   ${G}2)${N} cd frontend && npm run dev           ${B}# http://localhost:5173${N}"
}

# ---------- ИНТИХОБ ----------
banner

case "$MODE" in
  --docker) install_docker ;;
  --local)  install_local ;;
  *)
    echo "Усули насбро интихоб кунед:"
    echo "  1) Docker (тавсияшаванда — ҳама чиз худкор)"
    echo "  2) Локалӣ (php artisan serve + npm run dev)"
    echo ""
    printf "Интихоби шумо [1/2]: "
    read -r choice
    case "$choice" in
      1) install_docker ;;
      2) install_local ;;
      *) err "Интихоби нодуруст"; exit 1 ;;
    esac
    ;;
esac

echo ""
echo -e "${Y}Маълумоти вуруд (тестӣ):${N}"
echo "  Суперадмин: admin@mtmu.tj / admin123"
echo "  Волидайн:   +992901234567 / parent123"
