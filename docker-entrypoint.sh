#!/bin/sh
set -e
# Back4App (and most container hosts) assign the port via $PORT.
PORT="${PORT:-8080}"
sed -i "s/^Listen .*/Listen ${PORT}/" /etc/apache2/ports.conf
sed -i "s/<VirtualHost \*:80>/<VirtualHost *:${PORT}>/" /etc/apache2/sites-available/000-default.conf

# The public repo ships without secrets: generate the app key on first start.
ENV_FILE=/var/www/html/app-core/.env
if grep -q '^APP_KEY=$' "$ENV_FILE"; then
    php /var/www/html/app-core/artisan key:generate --force
fi
# If the host provides the public URL, record it for link generation.
if [ -n "${APP_URL:-}" ]; then
    sed -i "s|^APP_URL=.*|APP_URL=${APP_URL}|" "$ENV_FILE"
fi
exec "$@"
