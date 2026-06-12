# syntax=docker/dockerfile:1

FROM php:8.3-cli-bookworm AS build

RUN apt-get update && apt-get install -y --no-install-recommends \
    git unzip zip libzip-dev libpq-dev nodejs npm \
  && docker-php-ext-install pdo pdo_pgsql zip bcmath \
  && rm -rf /var/lib/apt/lists/*

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /app
COPY . .

RUN composer install --no-dev --optimize-autoloader --no-interaction --prefer-dist

RUN cp .env.example .env \
  && php artisan key:generate --force --no-interaction

RUN npm ci && npm run build

FROM php:8.3-cli-bookworm

RUN apt-get update && apt-get install -y --no-install-recommends \
    libpq-dev \
  && docker-php-ext-install pdo pdo_pgsql bcmath \
  && rm -rf /var/lib/apt/lists/*

WORKDIR /app

COPY --from=build /app /app

RUN mkdir -p storage/framework/{sessions,views,cache/data} storage/logs bootstrap/cache \
  && chmod -R 775 storage bootstrap/cache

ENV PORT=10000

COPY docker/render-start.sh /render-start.sh
RUN chmod +x /render-start.sh

CMD ["/render-start.sh"]
