FROM php:8.3-fpm

# Instalar dependencias del sistema
RUN apt-get update && apt-get install -y \
    git \
    unzip \
    libpq-dev \
    libzip-dev \
    libicu-dev \
    && docker-php-ext-install \
    pdo \
    pdo_pgsql \
    intl \
    zip \
    opcache

# Instalar Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Configuración de PHP para producción
RUN cp "$PHP_INI_DIR/php.ini-production" "$PHP_INI_DIR/php.ini"

# Configuración adicional de PHP
RUN echo "memory_limit = 256M" >> "$PHP_INI_DIR/conf.d/custom.ini" \
    && echo "upload_max_filesize = 50M" >> "$PHP_INI_DIR/conf.d/custom.ini" \
    && echo "post_max_size = 50M" >> "$PHP_INI_DIR/conf.d/custom.ini"

# Configurar OPcache para mejor rendimiento en desarrollo
RUN echo "opcache.enable=1" >> "$PHP_INI_DIR/conf.d/opcache.ini" \
    && echo "opcache.memory_consumption=256" >> "$PHP_INI_DIR/conf.d/opcache.ini" \
    && echo "opcache.interned_strings_buffer=16" >> "$PHP_INI_DIR/conf.d/opcache.ini" \
    && echo "opcache.max_accelerated_files=20000" >> "$PHP_INI_DIR/conf.d/opcache.ini" \
    && echo "opcache.revalidate_freq=60" >> "$PHP_INI_DIR/conf.d/opcache.ini" \
    && echo "opcache.fast_shutdown=1" >> "$PHP_INI_DIR/conf.d/opcache.ini" \
    && echo "opcache.enable_file_override=1" >> "$PHP_INI_DIR/conf.d/opcache.ini"

WORKDIR /var/www/backend

# Copiar composer files primero para aprovechar cache de Docker
COPY backend/composer.* ./

# Instalar dependencias de Symfony
RUN composer install --no-scripts --no-autoloader --prefer-dist

# Copiar el resto del código
COPY backend .

# Optimizar autoloader
RUN composer dump-autoload --optimize

# Copiar script de entrypoint
COPY docker/php/entrypoint.sh /usr/local/bin/entrypoint.sh
RUN chmod +x /usr/local/bin/entrypoint.sh

# Permisos
RUN chown -R www-data:www-data /var/www/backend

EXPOSE 9000

ENTRYPOINT ["/usr/local/bin/entrypoint.sh"]
