FROM node:22-slim AS node

FROM composer:2 AS composer

FROM php:8.4-fpm

# Install system dependencies
RUN apt-get update && apt-get install -y \
    git curl zip unzip libpng-dev libonig-dev libxml2-dev libzip-dev \
    libpq-dev libcurl4-openssl-dev pkg-config libssl-dev libicu-dev \
    python3 python3-pip \
    && docker-php-ext-install pdo_pgsql pgsql mbstring xml curl zip gd intl bcmath exif pcntl \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# Install Node.js
COPY --from=node /usr/local/bin/node /usr/local/bin/node
COPY --from=node /usr/local/lib/node_modules /usr/local/lib/node_modules
RUN ln -s /usr/local/lib/node_modules/npm/bin/npm-cli.js /usr/local/bin/npm && \
    ln -s /usr/local/lib/node_modules/npm/bin/npx-cli.js /usr/local/bin/npx

# Install Composer
COPY --from=composer /usr/bin/composer /usr/bin/composer

WORKDIR /app

# Copy all files first
COPY . .

# Install PHP dependencies
RUN composer install --no-dev --optimize-autoloader

# Install Node dependencies and build
RUN npm ci && npm run build

EXPOSE 8080

CMD ["sh", "-c", "php artisan config:clear; php artisan route:clear; php artisan view:clear; php artisan migrate --force; php artisan serve --host=0.0.0.0 --port=8080"]
