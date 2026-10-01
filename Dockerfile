FROM php:8.2-cli-alpine

# dependencias basicas del sistema y sqlite
RUN apk add --no-cache \
    curl \
    git \
    libpng-dev \
    libxml2-dev \
    zip \
    unzip \
    sqlite \
    sqlite-dev

# extensiones de php requeridas para laravel
RUN docker-php-ext-install pdo pdo_sqlite bcmath gd

# composer oficial
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www

EXPOSE 8000

# comando de inicio del servidor
CMD php artisan serve --host=0.0.0.0 --port=8000