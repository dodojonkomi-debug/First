#!/usr/bin/env bash
# ============================================
#  МТМУ — Танзими муҳити development (локалӣ)
# ============================================
set -e

ROOT="$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)"

echo "==> Backend (Laravel)"
cd "$ROOT/backend"
[ -f .env ] || cp .env.example .env
composer install
php artisan key:generate
php artisan migrate --seed
php artisan storage:link || true

echo ""
echo "==> Frontend (Vue 3)"
cd "$ROOT/frontend"
npm install

echo ""
echo "============================================"
echo " Тайёр! Барои оғоз:"
echo "   Backend:  cd backend  && php artisan serve"
echo "   Frontend: cd frontend && npm run dev"
echo "============================================"
