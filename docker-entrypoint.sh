#!/bin/sh
set -e

if [ "${RUN_MIGRATIONS:-true}" = "true" ]; then
    php artisan migrate --force
fi

php artisan app:ensure-docker-admin --no-interaction

if [ "${RUN_SCHEDULER:-true}" = "true" ]; then
    php artisan schedule:work --no-interaction &
fi

exec "$@"
