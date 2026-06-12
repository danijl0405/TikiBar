# syntax=docker/dockerfile:1

FROM php:8.4-cli-bookworm AS build

RUN apt-get update && apt-get install -y --no-install-recommends \
    ca-certificates curl gnupg git unzip zip \
    libzip-dev libpq-dev libonig-dev libxml2-dev \
  && docker-php-ext-install pdo pdo_pgsql zip bcmath mbstring xml dom \
  && curl -fsSL https://deb.nodesource.com/setup_22.x | bash - \
  && apt-get install -y --no-install-recommends nodejs \
  && rm -rf /var/lib/apt/lists/*

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /app

COPY composer.json composer.lock ./
RUN composer install --no-dev --no-scripts --no-interaction --prefer-dist

COPY . .

RUN cp .env.example .env \
  && php artisan key:generate --force --no-interaction

RUN composer install --no-dev --optimize-autoloader --no-interaction --prefer-dist

ENV NPM_CONFIG_IGNORE_SCRIPTS=false

RUN node --version && npm --version \
  && npm ci \
  && npm run build \
  && rm -rf node_modules .env

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
