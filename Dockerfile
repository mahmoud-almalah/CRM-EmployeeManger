# syntax=docker/dockerfile:1

# Two targets built from the same base:
#   production   no dev dependencies, code baked into the image
#   development  dev dependencies, meant to run with the source mounted (docker-compose.yml)

ARG PHP_VERSION=8.3

FROM composer:2 AS composer


FROM serversideup/php:${PHP_VERSION}-fpm-nginx AS base
WORKDIR /var/www/html

# Nginx serves public/ and PHP-FPM runs as the unprivileged www-data user on port 8080.
COPY --chmod=755 docker/entrypoint.d/ /etc/entrypoint.d/


FROM base AS vendor-production
COPY --from=composer /usr/bin/composer /usr/bin/composer
COPY --chown=www-data:www-data composer.json composer.lock ./
COPY --chown=www-data:www-data src ./src
COPY --chown=www-data:www-data app ./app
COPY --chown=www-data:www-data database ./database
USER www-data
RUN composer install --no-dev --no-interaction --no-progress --no-scripts --prefer-dist --optimize-autoloader


FROM base AS production
COPY --chown=www-data:www-data . .
COPY --chown=www-data:www-data --from=vendor-production /var/www/html/vendor ./vendor
USER www-data
# .dockerignore drops the runtime folders' contents, so make sure the folders exist.
RUN mkdir -p storage/framework/cache/data storage/framework/sessions storage/framework/views storage/logs bootstrap/cache \
    && php artisan package:discover --ansi


FROM base AS development
ARG USER_ID=1000
ARG GROUP_ID=1000
COPY --from=composer /usr/bin/composer /usr/bin/composer
USER root
# Match the owner of the mounted source so PHP can write to it.
RUN docker-php-serversideup-set-id www-data ${USER_ID}:${GROUP_ID} \
    && docker-php-serversideup-set-file-permissions --owner ${USER_ID}:${GROUP_ID} --service nginx
USER www-data
