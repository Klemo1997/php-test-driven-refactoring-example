FROM php:8.4-fpm-bookworm

COPY --from=composer:2.8 /usr/bin/composer /usr/bin/composer

RUN pecl install xdebug \
    && docker-php-ext-enable xdebug

RUN apt update && apt install -y git libzip-dev sqlite3 \
    && rm -rf /var/lib/apt/lists/*

RUN docker-php-ext-install zip pdo

RUN adduser --disabled-password --gecos '' dev

COPY default.ini $PHP_INI_DIR/conf.d/
