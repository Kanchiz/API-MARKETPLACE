#!/bin/sh
set -e

echo "🔗 Creando symlink de storage..."
php artisan storage:link --quiet 2>/dev/null || true

echo "🚀 Iniciando servidor Laravel en 0.0.0.0:8000..."
exec php artisan serve --host=0.0.0.0 --port=8000
