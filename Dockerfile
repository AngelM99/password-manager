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

# Instala Node.js y npm para compilar assets (nueva línea)
RUN apk add --no-cache nodejs npm

# Instala Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Copia el código del proyecto
WORKDIR /var/www/html
COPY . .

# Instala dependencias de Composer
RUN composer install --optimize-autoloader --no-dev

# Instala dependencias de Node.js y compila assets (nueva sección)
RUN npm install
RUN npm run build

# Permisos para Laravel (storage, bootstrap y public/build)
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html/storage /var/www/html/bootstrap/cache /var/www/html/public/build

# Expone el puerto (para FPM, no necesario exponerlo externamente)
EXPOSE 9000

CMD ["php-fpm"]