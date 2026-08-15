FROM php:8.2-fpm-alpine

# Install system dependencies, Nginx, Supervisor, and database development libraries
RUN apk update && apk add --no-cache \
    nginx \
    supervisor \
    postgresql-dev \
    libpng-dev \
    libjpeg-turbo-dev \
    freetype-dev \
    libzip-dev \
    sqlite-dev \
    curl \
    bash

# Configure and install PHP extensions
RUN docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) \
        pdo \
        pdo_pgsql \
        pdo_sqlite \
        gd \
        zip \
        bcmath

# Set working directory
WORKDIR /var/www/html

# Copy application files
COPY . /var/www/html

# Create upload and database directories with write permissions
RUN mkdir -p /var/www/html/public/uploads/settings \
    && mkdir -p /var/www/html/database \
    && chmod -R 777 /var/www/html/public/uploads \
    && chmod -R 777 /var/www/html/database

# Copy Nginx, Supervisor, and Entrypoint configurations
COPY docker/nginx.conf /etc/nginx/nginx.conf
COPY docker/supervisord.conf /etc/supervisor/conf.d/supervisord.conf
COPY docker/entrypoint.sh /usr/local/bin/entrypoint.sh
RUN chmod +x /usr/local/bin/entrypoint.sh

# Expose ports (Render maps PORT automatically)
EXPOSE 80 10000

ENTRYPOINT ["/usr/local/bin/entrypoint.sh"]
CMD ["/usr/bin/supervisord", "-c", "/etc/supervisor/conf.d/supervisord.conf"]
