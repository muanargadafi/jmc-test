# syntax=docker/dockerfile:1

# ---------- Stage 1: dependency Composer (production) ----------
FROM composer:2 AS vendor
WORKDIR /app
COPY composer.json composer.lock ./
RUN composer install \
    --no-dev \
    --no-interaction \
    --no-progress \
    --no-scripts \
    --prefer-dist \
    --ignore-platform-reqs

# ---------- Stage 2: runtime ----------
FROM php:8.4-apache AS runtime

ENV APACHE_DOCUMENT_ROOT=/var/www/html/public \
    PORT=10000 \
    COMPOSER_ALLOW_SUPERUSER=1

# Ekstensi PHP yang umum dipakai Laravel
ADD https://github.com/mlocati/docker-php-extension-installer/releases/latest/download/install-php-extensions /usr/local/bin/
RUN chmod +x /usr/local/bin/install-php-extensions \
    && install-php-extensions pdo_mysql pdo_pgsql bcmath intl zip gd opcache pcntl redis \
    && apt-get update && apt-get install -y --no-install-recommends git unzip \
    && rm -rf /var/lib/apt/lists/*

# Apache: arahkan ke /public, aktifkan rewrite, dengarkan port dari Render ($PORT)
RUN a2enmod rewrite headers \
    && sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf \
    && sed -ri -e 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf \
    && sed -ri -e 's!Listen 80!Listen ${PORT}!g' /etc/apache2/ports.conf \
    && sed -ri -e 's!<VirtualHost \*:80>!<VirtualHost *:${PORT}>!g' /etc/apache2/sites-available/000-default.conf

# OPcache untuk production
RUN { \
      echo "opcache.enable=1"; \
      echo "opcache.memory_consumption=128"; \
      echo "opcache.max_accelerated_files=10000"; \
      echo "opcache.validate_timestamps=0"; \
    } > /usr/local/etc/php/conf.d/opcache-prod.ini

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

# Salin kode aplikasi, lalu vendor hasil install Composer
COPY . .
COPY --from=vendor /app/vendor ./vendor

RUN mkdir -p storage/framework/{cache,sessions,views} storage/logs bootstrap/cache \
    && composer dump-autoload --optimize --no-dev --classmap-authoritative --no-scripts \
    && chown -R www-data:www-data storage bootstrap/cache \
    && chmod -R 775 storage bootstrap/cache \
    && php artisan package:discover --ansi || true

EXPOSE 10000

# Cache config/route/view saat start (env Render baru tersedia saat runtime).
# Set RUN_MIGRATIONS=true di Render jika ingin migrasi otomatis.
CMD ["sh", "-c", "php artisan storage:link || true; php artisan config:cache; php artisan route:cache; php artisan view:cache; if [ \"$RUN_MIGRATIONS\" = \"true\" ]; then php artisan migrate --force; fi; exec apache2-foreground"]
