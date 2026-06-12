#!/usr/bin/env bash
set -euo pipefail

cd /app

rm -f .env
rm -f bootstrap/cache/*.php 2>/dev/null || true

php artisan config:clear --no-interaction
php artisan route:clear --no-interaction 2>/dev/null || true
php artisan view:clear --no-interaction 2>/dev/null || true

export APP_URL="${RENDER_EXTERNAL_URL:-http://127.0.0.1:${PORT:-10000}}"

# Render generateValue devuelve base64 sin el prefijo "base64:" que Laravel requiere.
if [ -z "${APP_KEY:-}" ]; then
  export APP_KEY="base64:$(openssl rand -base64 32)"
elif [[ ! "$APP_KEY" =~ ^base64: ]]; then
  export APP_KEY="base64:${APP_KEY}"
fi

echo "==> Comprobando APP_KEY..."
php artisan tinker --execute="Illuminate\Support\Facades\Crypt::encryptString('ok'); echo 'app_key_ok'.PHP_EOL;"

echo "==> Comprobando assets de Vite..."
test -f public/build/manifest.json
test -f public/build/fonts-manifest.json

echo "==> Comprobando conexión a la base de datos..."
php artisan db:show --no-interaction

echo "==> Ejecutando migraciones..."
php artisan migrate --force --no-interaction

echo "==> Ejecutando seeders..."
php artisan db:seed --force --no-interaction

echo "==> Verificando datos..."
php artisan tinker --execute="echo 'categories='.App\Models\Category::count().PHP_EOL;"

if [ -z "${TIKI_HERO_VIDEO:-}" ] && [ ! -f public/videos/hero.mp4 ]; then
  export TIKI_HERO_VIDEO="https://videos.pexels.com/video-files/855633/855633-hd_1920_1080_25fps.mp4"
  echo "AVISO: Usando vídeo por defecto de Pexels (#855633)."
fi

echo "==> Vídeo de portada: ${TIKI_HERO_VIDEO:-/videos/hero.mp4}"

echo "==> Arrancando servidor en el puerto ${PORT:-10000}..."
exec php artisan serve --host=0.0.0.0 --port="${PORT:-10000}"
