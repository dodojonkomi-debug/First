#!/bin/sh
set -e

cd /var/www/html

# .env-ро омода кардан
if [ ! -f .env ]; then
    cp .env.example .env
fi

# Калиди барнома
if ! grep -q "^APP_KEY=base64" .env; then
    php artisan key:generate --force
fi

# Интизори базаи маълумот
echo "Интизори базаи маълумот..."
until php artisan migrate:status >/dev/null 2>&1; do
    sleep 2
done

# Миграсия ва seed (танҳо бори аввал)
php artisan migrate --force
php artisan db:seed --force || true

# Пайвасти storage
php artisan storage:link || true

# Кэши продакшен
php artisan config:cache
php artisan route:cache

exec php-fpm
