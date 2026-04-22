#!/bin/bash
echo "Starting Vovo project..."

docker compose up -d --build

echo "Installing dependencies..."
docker compose exec php composer install --no-interaction

echo "Copying environment files..."
docker compose exec php cp .env.example .env || true

echo "Generating application key..."
docker compose exec php php artisan key:generate --no-interaction

echo "Running migrations..."
docker compose exec php php artisan migrate --force

echo "Seeding database..."
docker compose exec php php artisan db:seed

echo ""
echo "================================"
echo " Project is ready!"
echo " App:        http://localhost:8080"
echo " API Docs:   http://localhost:8080/docs"
echo " phpMyAdmin: http://localhost:8081"
echo "================================"
echo ""
echo "To start the queue worker: docker compose exec php php artisan queue:work"
echo "To run tests: docker compose exec php php artisan test"
echo ""
