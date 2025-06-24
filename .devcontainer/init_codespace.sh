#!/usr/bin/env bash
set -e

echo "🚀 Starting MySQL service..."
sudo service mysql start

echo "🗄️ Creating MySQL database..."
mysql -u root -e "CREATE DATABASE IF NOT EXISTS bagisto CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"

echo "📦 Installing PHP dependencies..."
composer install --no-interaction

echo "📁 Copying .env..."
cp .env.example .env || true

echo "🔑 Generating app key..."
php artisan key:generate

echo "🛠️ Updating .env DB config..."
sed -i 's/DB_DATABASE=.*/DB_DATABASE=bagisto/' .env
sed -i 's/DB_USERNAME=.*/DB_USERNAME=root/' .env
sed -i 's/DB_PASSWORD=.*/DB_PASSWORD=/' .env
sed -i 's/DB_HOST=.*/DB_HOST=127.0.0.1/' .env

echo "🧱 Running migrations..."
php artisan migrate --force || true

echo "✅ Laravel is ready!"
echo "👉 Run: php artisan serve --host=0.0.0.0 --port=8000"
