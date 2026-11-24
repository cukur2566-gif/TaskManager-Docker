# syntax=docker/dockerfile:1
# ---------- Stage 1: build frontend assets (Vite + Tailwind) ----------
FROM node:22-alpine AS frontend
WORKDIR /app
COPY src/laravel-app/package.json ./
RUN npm install
COPY src/laravel-app/ .
RUN npm run build

# ---------- Stage 2: install PHP dependencies (no dev packages) ----------
FROM composer:2 AS vendor
WORKDIR /app
COPY src/laravel-app/composer.json src/laravel-app/composer.lock ./
RUN composer install --no-dev --no-interaction --prefer-dist \
    --no-scripts --no-autoloader
COPY src/laravel-app/ .
RUN composer dump-autoload --no-dev --optimize --no-scripts

# ---------- Stage 3: runtime ----------
FROM php:8.3-cli AS runtime

RUN apt-get update && apt-get install -y --no-install-recommends \
        libonig-dev libsqlite3-dev libxml2-dev libcurl4-openssl-dev \
    && docker-php-ext-install pdo_sqlite mbstring bcmath pcntl dom xml curl \
    && rm -rf /var/lib/apt/lists/*

WORKDIR /var/www/html

COPY src/laravel-app/ .
COPY --from=vendor /app/vendor ./vendor
COPY --from=frontend /app/public/build ./public/build

RUN mkdir -p storage/framework/cache storage/framework/sessions \
        storage/framework/views storage/logs bootstrap/cache \
    && chmod -R 775 storage bootstrap/cache \
    && php artisan package:discover --ansi

# Defaults (override with docker-compose / `docker run -e`)
ENV APP_ENV=production \
    APP_DEBUG=false \
    APP_URL=http://localhost:8000 \
    DB_CONNECTION=sqlite \
    DB_DATABASE=/data/database.sqlite \
    SESSION_DRIVER=file \
    CACHE_STORE=file \
    QUEUE_CONNECTION=sync \
    GO_API_URL=http://go-backend:8080

COPY docker/entrypoint.sh /usr/local/bin/entrypoint.sh
RUN chmod +x /usr/local/bin/entrypoint.sh

VOLUME ["/data"]
EXPOSE 8000

HEALTHCHECK --interval=15s --timeout=5s --start-period=30s --retries=3 \
    CMD php -r 'exit(@file_get_contents("http://127.0.0.1:8000/up") === false ? 1 : 0);'

ENTRYPOINT ["entrypoint.sh"]
