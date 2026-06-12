#!/usr/bin/env bash
set -euo pipefail

cd /app

rm -f .env
php artisan config:clear --no-interaction
php artisan route:clear --no-interaction 2>/dev/null || true
php artisan view:clear --no-interaction 2>/dev/null || true

export APP_URL="${RENDER_EXTERNAL_URL:-http://127.0.0.1:${PORT:-10000}}"

echo "==> Comprobando assets de Vite..."
test -f public/build/manifest.json

echo "==> Comprobando conexión a la base de datos..."
php artisan db:show --no-interaction

echo "==> Ejecutando migraciones..."
php artisan migrate --force --no-interaction

echo "==> Ejecutando seeders..."
php artisan db:seed --force --no-interaction

echo "==> Arrancando servidor en el puerto ${PORT:-10000}..."
exec php artisan serve --host=0.0.0.0 --port="${PORT:-10000}"
