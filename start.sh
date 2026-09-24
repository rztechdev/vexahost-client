#!/bin/sh
set -e

echo "==> Membersihkan cache aplikasi..."
php artisan optimize:clear

echo "==> Menjalankan migrasi database..."
php artisan migrate --force

echo "==> Menjalankan database seeder..."
php artisan db:seed --force

echo "==> Menghubungkan storage symlink..."
php artisan storage:link --force || true

echo "==> Optimasi cache Laravel..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

echo "==> Menjalankan web server..."
exec php artisan serve --host=0.0.0.0 --port=80
