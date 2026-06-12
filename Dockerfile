# syntax=docker/dockerfile:1

FROM php:8.4-cli-bookworm AS build

# Vite 8 y laravel-vite-plugin requieren Node >= 20.19 (Debian apt trae Node 18).
COPY --from=node:22-bookworm /usr/local/bin/node /usr/local/bin/node
COPY --from=node:22-bookworm /usr/local/bin/npm /usr/local/bin/npm
COPY --from=node:22-bookworm /usr/local/bin/npx /usr/local/bin/npx
COPY --from=node:22-bookworm /usr/local/lib/node_modules /usr/local/lib/node_modules

RUN apt-get update && apt-get install -y --no-install-recommends \
    git unzip zip libzip-dev libpq-dev libonig-dev libxml2-dev \
  && docker-php-ext-install pdo pdo_pgsql zip bcmath mbstring xml dom \
  && rm -rf /var/lib/apt/lists/*

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /app

COPY composer.json composer.lock ./
RUN composer install --no-dev --no-scripts --no-interaction --prefer-dist

COPY . .

RUN cp .env.example .env \
  && php artisan key:generate --force --no-interaction

RUN composer install --no-dev --optimize-autoloader --no-interaction --prefer-dist

RUN node --version && npm --version \
  && npm ci \
  && npm run build

FROM php:8.4-cli-bookworm

RUN apt-get update && apt-get install -y --no-install-recommends \
    libpq-dev libonig-dev libxml2-dev \
  && docker-php-ext-install pdo pdo_pgsql bcmath mbstring xml dom \
  && rm -rf /var/lib/apt/lists/*

WORKDIR /app

COPY --from=build /app /app

RUN mkdir -p storage/framework/{sessions,views,cache/data} storage/logs bootstrap/cache \
  && chmod -R 775 storage bootstrap/cache

ENV PORT=10000

COPY docker/render-start.sh /render-start.sh
RUN chmod +x /render-start.sh

CMD ["/render-start.sh"]
