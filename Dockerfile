# ===== 1) Frontend build (Vite) =====
FROM node:20-alpine AS frontend
WORKDIR /app
COPY package*.json ./
RUN npm ci
COPY resources ./resources
COPY public ./public
COPY vite.config.* postcss.config.* tailwind.config.* ./
RUN npm run build


# ===== 2) PHP dependencies (Composer) =====
FROM composer:2 AS vendor
WORKDIR /app
COPY composer.json composer.lock ./
RUN composer install --no-dev --prefer-dist --no-interaction --optimize-autoloader
#COPY . .
# ここで追加のオートロード最適化
#RUN composer dump-autoload --no-dev --optimize


# ===== 3) Runtime (Apache + PHP) =====
FROM php:8.3-apache

# System dependencies + PHP extensions
RUN apt-get update && apt-get install -y \
    git unzip libpq-dev libzip-dev \
  && docker-php-ext-install pdo_pgsql zip \
  && a2enmod rewrite \
  && rm -rf /var/lib/apt/lists/*

# Apache: document root => /public
RUN sed -ri -e 's!/var/www/html!/var/www/html/public!g' /etc/apache2/sites-available/000-default.conf \
  && sed -ri -e 's!/var/www/!/var/www/html/public!g' /etc/apache2/apache2.conf

WORKDIR /var/www/html

# App source（vendor, build は後で上書きする前提）
COPY app ./app
COPY bootstrap ./bootstrap
COPY config ./config
COPY database ./database
COPY public ./public
COPY resources ./resources
COPY routes ./routes
COPY storage ./storage
COPY artisan .
COPY composer.json composer.lock ./


# Copy vendor and built assets from stages
COPY --from=vendor /app/vendor ./vendor
COPY --from=frontend /app/public/build ./public/build

# Laravel permissions
RUN mkdir -p storage/framework/{cache,sessions,views} bootstrap/cache \
&& chown -R www-data:www-data storage bootstrap/cache


EXPOSE 80

# Render ではコンテナ起動時に env が入るので、ここでキャッシュ生成するのが安全
# （DBを触る migrate は最初は入れない方が無難。必要なら後述）
CMD ["bash", "-lc", "php artisan config:cache && php artisan route:cache && php artisan view:cache && apache2-foreground"]
