ARG PHP_VERSION=8.2
ARG COMPOSER_VERSION=2.2

FROM composer:${COMPOSER_VERSION}
FROM php:${PHP_VERSION}-cli

COPY --from=composer /usr/bin/composer /usr/local/bin/composer

WORKDIR /code

RUN composer install --no-interaction