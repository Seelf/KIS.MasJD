#!/bin/bash
set -e

# Instalacja zależności jeśli vendor nie istnieje
if [ ! -d "/app/vendor" ]; then
    echo "Installing Composer dependencies..."
    composer install
    php artisan key:generate
fi

# Opcjonalnie migracje
# php artisan migrate --force

# Start standardowy
exec php artisan serve --host=0.0.0.0 --port=8000
