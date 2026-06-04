#!/bin/sh
set -e

echo "Installing Composer dependencies (with dev)..."
composer install --optimize-autoloader

echo "Creating database if not exists..."
php bin/console doctrine:database:create --if-not-exists --no-interaction

echo "Running migrations..."
php bin/console doctrine:migrations:migrate --no-interaction

echo "Creating default admin user..."
php bin/console app:create-admin || echo "User already exists or command not available"

echo "Creating sample data..."
php bin/console app:create-sample-data || echo "Sample data already exists or command not available"

echo "Creating sample products..."
php bin/console app:create-sample-products || echo "Sample products already exist or command not available"

echo "Clearing cache..."
php bin/console cache:clear --env=dev

echo "Starting PHP-FPM..."
exec php-fpm
