#!/usr/bin/env bash
set -e

echo "📦 Installing composer dependencies..."
composer install --no-interaction

echo "📁 Copying .env..."
cp .env.example .env || true

echo "🔑 Generating app key..."
php artisan key:generate

echo "🛠️ Setting .env database credentials..."
sed -i 's/DB_DATABASE=.*/DB_DATABASE=test/' .env
sed -i 's/DB_USERNAME=.*/DB_USERNAME=root/' .env
sed -i 's/DB_PASSWORD=.*/DB_PASSWORD=/' .env
sed -i 's/DB_HOST=.*/DB_HOST=127.0.0.1/' .env

echo "🧱 Migrating database..."
php artisan migrate --force || true

echo "✅ Done! Now run: php artisan serve --host=0.0.0.0 --port=8000"
echo "🌐 Access your Bagisto store at: http://localhost:8000"