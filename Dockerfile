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
FROM php:8.4-cli AS vendor
WORKDIR /app

# composer install に必要な最低限
RUN apt-get update && apt-get install -y git unzip libzip-dev \
  && docker-php-ext-install zip \
  && rm -rf /var/lib/apt/lists/*

# composer を入れる
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

COPY composer.json composer.lock ./
# scripts は artisan を呼ぶので止める（あなたの方針でOK）
RUN composer install --no-dev --prefer-dist --no-interaction --optimize-autoloader --no-scripts

# ===== 3) Runtime (Apache + PHP) =====
FROM php:8.4-apache

RUN apt-get update && apt-get install -y \
    git unzip libpq-dev libzip-dev \
  && docker-php-ext-install pdo_pgsql zip \
  && a2enmod rewrite \
  && rm -rf /var/lib/apt/lists/*

# Apache: document root => /public
RUN sed -ri -e 's!/var/www/html!/var/www/html/public!g' /etc/apache2/sites-available/000-default.conf \
 && sed -ri -e 's!/var/www/!/var/www/html/public!g' /etc/apache2/apache2.conf

WORKDIR /var/www/html

# App source（必要なものだけコピー）
COPY app ./app
COPY bootstrap ./bootstrap
COPY config ./config
COPY database ./database
COPY public ./public
COPY resources ./resources
COPY routes ./routes
COPY storage ./storage
COPY artisan ./artisan
COPY composer.json composer.lock ./

# vendor / build assets を上書き配置
COPY --from=vendor /app/vendor ./vendor
COPY --from=frontend /app/public/build ./public/build

# storage 周りを「必ず作る」＋権限
RUN mkdir -p storage/framework/cache/data storage/framework/sessions storage/framework/views storage/logs \
 && chown -R www-data:www-data storage bootstrap/cache \
 && chmod -R 775 storage bootstrap/cache

EXPOSE 80
CMD ["apache2-foreground"]
