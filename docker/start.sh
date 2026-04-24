#!/usr/bin/env sh
set -e

if [ -z "${APP_KEY}" ]; then
  echo "APP_KEY is required."
  exit 1
fi

php artisan config:cache

if [ "${RUN_MIGRATIONS:-false}" = "true" ]; then
  php artisan migrate --force
fi

exec php artisan serve --host=0.0.0.0 --port="${PORT:-10000}"
