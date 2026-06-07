# ============================================
#  МТМУ — Системаи қабули хонандагон
# ============================================

.PHONY: help setup dev-back dev-front up down logs migrate seed fresh

help:
	@echo "МТМУ — фармонҳои дастрас:"
	@echo "  make setup       - Танзими локалӣ (composer + npm + migrate)"
	@echo "  make dev-back    - Оғози backend (php artisan serve)"
	@echo "  make dev-front   - Оғози frontend (npm run dev)"
	@echo "  make up          - Оғози продакшен бо Docker Compose"
	@echo "  make down        - Қатъи контейнерҳо"
	@echo "  make logs        - Дидани логҳо"
	@echo "  make migrate     - Иҷрои миграсияҳо"
	@echo "  make seed        - Пур кардани базаи маълумот"
	@echo "  make fresh       - Аз нав сохтани база + seed"

setup:
	bash scripts/dev-setup.sh

dev-back:
	cd backend && php artisan serve

dev-front:
	cd frontend && npm run dev

up:
	docker compose up -d --build

down:
	docker compose down

logs:
	docker compose logs -f

migrate:
	cd backend && php artisan migrate

seed:
	cd backend && php artisan db:seed

fresh:
	cd backend && php artisan migrate:fresh --seed
