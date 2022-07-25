ARG PHP_VERSION=7.4
ARG COMPOSER_VERSION=2.0

FROM composer:${COMPOSER_VERSION}
FROM php:${PHP_VERSION}-cli

COPY --from=composer /usr/bin/composer /usr/local/bin/composer

WORKDIR /code

RUN composer install --no-interaction