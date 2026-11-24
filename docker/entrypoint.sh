#!/bin/sh
# Container entrypoint for the Laravel service.
# `php artisan serve` does not forward most environment variables to its
# child process, so the values Laravel needs are synced into .env first.
set -e
cd /var/www/html

DB_FILE="${DB_DATABASE:-/data/database.sqlite}"
mkdir -p "$(dirname "$DB_FILE")"
touch "$DB_FILE"

[ -f .env ] || cp .env.example .env

set_env() {
  key="$1"; value="$2"
  if grep -q "^${key}=" .env; then
    sed -i "s|^${key}=.*|${key}=${value}|" .env
  else
    printf '%s=%s\n' "$key" "$value" >> .env
  fi
}

# Use APP_KEY from the environment, otherwise generate one once and persist it
# in the data volume so sessions survive container restarts.
if [ -z "${APP_KEY:-}" ]; then
  [ -s /data/app.key ] || php artisan key:generate --show > /data/app.key
  APP_KEY="$(cat /data/app.key)"
fi
export APP_KEY

for var in APP_KEY APP_ENV APP_DEBUG APP_URL DB_CONNECTION DB_DATABASE \
           SESSION_DRIVER CACHE_STORE QUEUE_CONNECTION GO_API_URL; do
  eval "val=\${$var:-}"
  if [ -n "$val" ]; then set_env "$var" "$val"; fi
done

php artisan migrate --force
exec php artisan serve --host=0.0.0.0 --port=8000
