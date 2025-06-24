#!/usr/bin/env bash
cd /workspace

# Install backend
composer install
cp .env.example .env
php artisan key:generate

# Optionally pull in React repo
if [ ! -d frontend ]; then
  git clone https://github.com/yourusername/react-storefront.git frontend
fi

# Install React
cd frontend
npm install

# Back to root, migrate DB
cd ..
php artisan migrate --force || true

echo "✅ Setup done — ready to start services!"
# Start services
php artisan serve --host=