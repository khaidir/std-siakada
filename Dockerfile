# PHP CLI + Composer untuk menjalankan artisan, composer, pest (tanpa PHP lokal).
FROM php:8.4-cli

# Dependensi build ekstensi PHP
RUN apt-get update && apt-get install -y --no-install-recommends \
        git \
        unzip \
        libicu-dev \
        libzip-dev \
        libpng-dev \
        libjpeg62-turbo-dev \
        libfreetype6-dev \
        libonig-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j"$(nproc)" \
        pdo_mysql \
        intl \
        bcmath \
        zip \
        gd \
        pcntl \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# Composer (dari image resmi)
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

CMD ["php", "-a"]
