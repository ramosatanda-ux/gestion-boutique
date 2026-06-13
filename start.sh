#!/bin/sh
set -e
echo "=== Container starting ==="
echo "PORT=$PORT"
echo "DB_CONNECTION=$DB_CONNECTION"
echo "DB_HOST=$DB_HOST"

echo "--- Caching config..."
php artisan config:cache
echo "--- Config cached OK"

echo "--- Caching routes..."
php artisan route:cache
echo "--- Routes cached OK"

echo "--- Running migrations..."
php artisan migrate --force
echo "--- Migrations OK"

echo "--- Starting server on port ${PORT:-8000}..."
exec php artisan serve --host=0.0.0.0 --port=${PORT:-8000}
