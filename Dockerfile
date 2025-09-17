# Usa una base liviana: PHP 8.2 con FPM y Alpine (solo ~100MB)
FROM php:8.2-fpm-alpine

# Instala dependencias del sistema (livianas)
RUN apk add --no-cache \
    libpng-dev \
    libjpeg-turbo-dev \
    libwebp-dev \
    libzip-dev \
    oniguruma-dev \
    && docker-php-ext-configure gd --with-jpeg --with-webp \
    && docker-php-ext-install pdo_mysql mbstring zip exif pcntl bcmath gd

# Instala Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Copia el código del proyecto
WORKDIR /var/www/html
COPY . .

# Instala dependencias de Composer
RUN composer install --optimize-autoloader --no-dev

# Permisos para Laravel (storage y bootstrap)
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html/storage /var/www/html/bootstrap/cache

# Expone el puerto (para FPM, no necesario exponerlo externamente)
EXPOSE 9000

CMD ["php-fpm"]
