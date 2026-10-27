#!/bin/sh
# Runs on every container start, as serversideup/php executes everything in /etc/entrypoint.d.
# `app:install` only does work that is not done yet, so restarting is safe.
set -e

if [ "${RUN_INSTALL:-true}" != "true" ]; then
    echo "RUN_INSTALL is not true, skipping app:install."
    exit 0
fi

cd /var/www/html

if [ "${APP_ENV:-production}" = "production" ] && [ -z "${APP_KEY:-}" ]; then
    echo "APP_KEY must be set in production." >&2
    exit 1
fi

if [ ! -d vendor ]; then
    composer install --no-interaction --no-progress --prefer-dist
fi

# Development only: the source is mounted, so create the .env a developer would create by hand.
if [ "${APP_ENV:-production}" != "production" ]; then
    [ -f .env ] || cp .env.example .env
    grep -q '^APP_KEY=.\+' .env || php artisan key:generate --ansi
fi

if [ "${INSTALL_DEMO_DATA:-false}" = "true" ]; then
    php artisan app:install --demo
else
    php artisan app:install
fi
