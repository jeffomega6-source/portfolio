FROM node:22-alpine AS assets
WORKDIR /app
COPY package.json ./
RUN npm install
COPY resources ./resources
COPY vite.config.js .
RUN npm run build

FROM php:8.4-apache
RUN apt-get update && apt-get install -y --no-install-recommends libpq-dev libzip-dev libonig-dev unzip \
    && docker-php-ext-install pdo_pgsql mbstring zip opcache \
    && (a2dismod mpm_event mpm_worker mpm_prefork || true) \
    && rm -f /etc/apache2/mods-enabled/mpm_*.load /etc/apache2/mods-enabled/mpm_*.conf \
    && a2enmod mpm_prefork rewrite \
    && apache2ctl -M | grep -q mpm_prefork_module \
    && ! apache2ctl -M | grep -Eq "mpm_(event|worker)_module" \
    && apache2ctl -t \
    && rm -rf /var/lib/apt/lists/*
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer
WORKDIR /var/www/html
COPY composer.json composer.lock ./
RUN composer install --no-dev --no-interaction --prefer-dist --optimize-autoloader --no-scripts
COPY . .
COPY --from=assets /app/public/build ./public/build
RUN sed -ri 's!/var/www/html!/var/www/html/public!g' /etc/apache2/sites-available/000-default.conf \
    && chown -R www-data:www-data storage bootstrap/cache \
    && chmod -R ug+rwx storage bootstrap/cache
EXPOSE 80
CMD ["sh", "-c", "set -e; PORT=${PORT:-80}; sed -ri \"s/^Listen 80$/Listen ${PORT}/\" /etc/apache2/ports.conf; sed -ri \"s/<VirtualHost \\*:80>/<VirtualHost *:${PORT}>/\" /etc/apache2/sites-available/000-default.conf; php artisan package:discover --ansi; php artisan migrate --force; exec apache2-foreground"]
