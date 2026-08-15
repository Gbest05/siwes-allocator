#!/bin/sh
set -e

# Support dynamic PORT environment variable provided by Render / Heroku / Fly.io / Cloud
PORT="${PORT:-80}"
sed -i "s/LISTEN_PORT/${PORT}/g" /etc/nginx/nginx.conf

# Ensure runtime & log directories exist
mkdir -p /run/nginx /var/log/nginx /var/log/supervisor /var/run
mkdir -p /var/www/html/public/uploads/settings
mkdir -p /var/www/html/database
chmod -R 777 /var/www/html/public/uploads /var/www/html/database

exec "$@"
