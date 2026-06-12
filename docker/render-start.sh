#!/usr/bin/env bash
set -euo pipefail

cd /app

# No usar el .env generado en el build; Render inyecta las variables reales.
rm -f .env
rm -f bootstrap/cache/config.php bootstrap/cache/routes-v7.php 2>/dev/null || true

export APP_URL="${RENDER_EXTERNAL_URL:-http://127.0.0.1:${PORT:-10000}}"

php artisan config:cache
php artisan view:cache

exec php artisan serve --host=0.0.0.0 --port="${PORT:-10000}"
