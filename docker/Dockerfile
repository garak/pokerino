FROM composer:2.8 AS composer
FROM php:8.4-cli-alpine

COPY --from=composer /usr/bin/composer /usr/local/bin/composer

WORKDIR /srv/pokerino
