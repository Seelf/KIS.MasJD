#!/bin/bash
set -e

cd /app

if [ ! -f ".env" ]; then
    echo "[entrypoint] .env not found, creating from .env.example"
    cp .env.example .env
fi

if [ ! -d "vendor" ]; then
    echo "[entrypoint] Installing Composer dependencies..."
    composer install
fi

if grep -q "^APP_KEY=$" .env; then
    echo "[entrypoint] Generating APP_KEY..."
    php artisan key:generate
fi

if [ ! -f "public/build/manifest.json" ]; then
    echo "[entrypoint] Building frontend with Vite..."
    npm install && npm run build
fi

echo "[entrypoint] Running migrations..."
php artisan migrate --force

echo "[entrypoint] Starting kis:textalk-listen..."
php artisan kis:textalk-listen &

php artisan serve --host=0.0.0.0 --port=8000
