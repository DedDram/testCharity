FROM php:8.3-fpm

# Установка необходимых пакетов
RUN apt-get update && apt-get install -y \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    libpq-dev \
    postgresql-client \
    unzip \
    git \
    libzip-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
	&& docker-php-ext-install gd pdo pdo_pgsql \
    && docker-php-ext-install gd zip \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

# Копирование composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Копирование приложения
COPY . /var/www

# Установка рабочего каталога
WORKDIR /var/www

# Установка зависимостей
RUN composer install

EXPOSE 9000
CMD ["php-fpm"]
