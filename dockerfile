FROM php:8.2-fpm

RUN apt-get update && apt-get install -y \
    git \
    curl \
    zip \
    unzip \
    libpq-dev \
    libicu-dev \
    libzip-dev \
    && docker-php-ext-install \
    pdo \
    pdo_pgsql \
    intl \
    zip

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www

# Storage ve cache izinlerini otomatik ayarla
RUN mkdir -p /var/www/laravel/storage/framework/{sessions,views,cache} \
    /var/www/laravel/storage/logs \
    /var/www/laravel/bootstrap/cache \
    && chown -R www-data:www-data /var/www/laravel/storage /var/www/laravel/bootstrap/cache \
    && chmod -R 775 /var/www/laravel/storage /var/www/laravel/bootstrap/cache
