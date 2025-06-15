FROM php:8.4-cli AS builder

RUN apt update && apt install -y \
    git \
    unzip

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /app

COPY composer.json composer.lock ./
RUN composer install --no-dev --optimize-autoloader --no-scripts --no-interaction

FROM php:8.4-cli AS production

WORKDIR /app

COPY --from=builder /app/vendor ./vendor
COPY bin ./bin
COPY src ./src
COPY config ./config
COPY data ./data
COPY .env ./
COPY composer.json composer.lock ./

CMD ["php", "bin/console"]
