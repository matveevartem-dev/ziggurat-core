# infrastructure/api/Dockerfile

FROM php:8.4-cli-alpine

# 1. Добавляем системные библиотеки (библиотека sodium добавлена)
RUN apk add --no-cache \
    icu-dev \
    libzip-dev \
    libpng-dev \
    libsodium-dev \
    linux-headers \
    postgresql-dev

# 2. Теперь расширения установятся без ошибок
RUN docker-php-ext-install \
    pdo_mysql \
    intl \
    bcmath \
    zip \
    gd \
    sockets \
    sodium \
    pdo \
    pdo_pgsql

# ... (дальше копирование RR и Composer)

COPY --from=ghcr.io/roadrunner-server/roadrunner:2024.1 /usr/bin/rr /usr/bin/rr

COPY ./infrastructure/api/php-ini/*.ini /usr/local/etc/php/conf.d/

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www

COPY ./infrastructure/api/entrypoint.sh /usr/local/bin/entrypoint.sh

ENTRYPOINT ["entrypoint.sh"]
