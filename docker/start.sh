#!/usr/bin/env sh
set -e

if [ -z "${APP_KEY}" ]; then
  echo "APP_KEY is required."
  exit 1
fi

php artisan config:clear

php artisan migrate:fresh --force
php artisan db:seed --force
php artisan vendor:publish --tag=laravel-assets --force
php artisan filament:assets
php artisan vendor:publish --tag=scribe-assets --force
php artisan scribe:generate --force
php artisan config:cache

exec php artisan serve --host=0.0.0.0 --port="${PORT:-10000}"
