# Base image com PHP 8.1
FROM php:8.1-fpm

# Instalar extensões necessárias
RUN apt-get update && apt-get install -y \
    build-essential \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    curl \
    unzip \
    git \
    && docker-php-ext-install pdo pdo_mysql pdo_pgsql mbstring exif pcntl bcmath gd

# Instalar Composer
COPY --from=composer:2.0 /usr/bin/composer /usr/bin/composer

# Criar diretório de trabalho
WORKDIR /var/www

# Copiar arquivos do projeto
COPY . /var/www

# Permissões
RUN chown -R www-data:www-data /var/www

# Instalar dependências do Laravel
RUN composer install --optimize-autoloader --no-dev

# Definir permissão correta para o diretório de cache e logs
RUN chmod -R 777 /var/www/storage /var/www/bootstrap/cache

# Porta padrão para o PHP-FPM
EXPOSE 9000

CMD ["php-fpm"]
