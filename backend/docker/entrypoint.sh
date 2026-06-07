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

# Интизори базаи маълумот (то 60 кӯшиш = ~2 дақиқа)
echo "Интизори базаи маълумот (db:3306)..."
i=0
until php -r '
    $h = getenv("DB_HOST") ?: "db";
    $p = getenv("DB_PORT") ?: "3306";
    $u = getenv("DB_USERNAME") ?: "root";
    $pw = getenv("DB_PASSWORD") ?: "";
    try { new PDO("mysql:host=$h;port=$p", $u, $pw); exit(0); }
    catch (Exception $e) { exit(1); }
' 2>/dev/null; do
    i=$((i + 1))
    if [ "$i" -ge 60 ]; then
        echo "Базаи маълумот дастрас нашуд — қатъ."
        exit 1
    fi
    sleep 2
done
echo "Базаи маълумот тайёр аст."

# Миграсия ва seed
php artisan migrate --force
php artisan db:seed --force || true

# Пайвасти storage
php artisan storage:link || true

# Кэши продакшен
php artisan config:cache
php artisan route:cache

exec php-fpm
