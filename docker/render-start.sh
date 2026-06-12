#!/usr/bin/env bash
set -euo pipefail

cd /app

if [ -n "${RENDER_EXTERNAL_URL:-}" ]; then
  export APP_URL="$RENDER_EXTERNAL_URL"
fi

php artisan config:cache
php artisan route:cache
php artisan view:cache

php artisan migrate --force --no-interaction
php artisan db:seed --force --no-interaction

php artisan serve --host=0.0.0.0 --port="${PORT:-10000}"
