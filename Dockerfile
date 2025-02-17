FROM php:8.3-fpm

RUN apt-get update && apt-get install -y \
    build-essential \
    netcat-traditional \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    curl \
    unzip \
    git \
    libpq-dev \
    && docker-php-ext-install pdo pdo_mysql pdo_pgsql mbstring exif pcntl bcmath gd

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www

COPY . /var/www

RUN usermod -u 1000 www-data

RUN chown -R www-data:www-data /var/www
RUN chmod -R 777 /var/www/storage /var/www/bootstrap/cache

RUN composer install --optimize-autoloader --no-dev

EXPOSE 8000
