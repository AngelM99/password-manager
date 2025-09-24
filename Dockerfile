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

# Instala Node.js y npm 
RUN apk add --no-cache nodejs npm

# Instala Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# 🔥 ESTRATEGIA CORRECTA: Copiar solo archivos de dependencias PRIMERO
WORKDIR /var/www/html

# 1. Copia SOLO los archivos de configuración de dependencias
COPY package.json package-lock.json* ./
COPY composer.json composer.lock* ./

# 2. Instala dependencias de Node.js (esto se cachea si no cambian los archivos)
RUN npm ci --force

# 3. Instala dependencias de Composer
RUN composer install --optimize-autoloader --no-dev --no-scripts

# 4. ✅ AHORA copia el resto del código
COPY . .

# 5. Compila assets con Tailwind v4
RUN npm run build

# Permisos para Laravel
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html/storage /var/www/html/bootstrap/cache /var/www/html/public/build

EXPOSE 9000

CMD ["php-fpm"]