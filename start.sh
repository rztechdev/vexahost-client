#!/bin/sh
set -e

echo "==> ðŸ§¹ Membersihkan cache aplikasi..."
php artisan optimize:clear

echo "==> ðŸ“¦ Menjalankan migrasi database..."
php artisan migrate --force

echo "==> ðŸŒ± Menjalankan database seeder..."
php artisan db:seed --force

echo "==> ðŸ”— Menghubungkan storage symlink..."
php artisan storage:link --force || true

echo "==> âš¡ Optimasi cache Laravel..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

echo "==> ðŸš€ Menjalankan web server..."
exec php artisan serve --host=0.0.0.0 --port=80
