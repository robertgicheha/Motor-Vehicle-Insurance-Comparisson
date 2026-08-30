# syntax=docker/dockerfile:1

# ---------------------------------------------------------------------------
# Stage 1: build frontend assets (Vite/Tailwind)
# ---------------------------------------------------------------------------
FROM node:20-bookworm-slim AS frontend
WORKDIR /app
COPY package.json package-lock.json* ./
RUN npm install
COPY resources/ resources/
COPY vite.config.js tailwind.config.js postcss.config.js ./
RUN npm run build

# ---------------------------------------------------------------------------
# Stage 2: install PHP (Composer) dependencies
# ---------------------------------------------------------------------------
FROM composer:2 AS vendor
WORKDIR /app
COPY composer.json composer.lock ./
# --ignore-platform-req=ext-soap: the composer:2 base image used only for
# this dependency-resolution stage doesn't have ext-soap installed, but the
# runtime image (stage 3, below) does — this just stops the platform check
# from failing here over an extension the final image genuinely has.
RUN composer install \
    --no-dev \
    --no-scripts \
    --no-autoloader \
    --prefer-dist \
    --no-interaction \
    --ignore-platform-req=ext-soap
COPY . .
RUN composer dump-autoload --optimize --no-dev --classmap-authoritative

# ---------------------------------------------------------------------------
# Stage 3: runtime image
# ---------------------------------------------------------------------------
# 8.4, not 8.3: composer.json declares "^8.3", but composer.lock actually
# resolved dependency versions that hard-require PHP >= 8.4.1 at runtime
# (found by smoke-testing this image) — 8.4 still satisfies the ^8.3 range.
FROM php:8.4-cli-bookworm AS runtime

# System packages + PHP extensions this app actually needs:
# - ext-soap / ext-curl: required by composer.json (NAV/Business Central SOAP
#   integration and outbound HTTP calls)
# - pdo_pgsql: Render's managed database is PostgreSQL
# - gd, zip, intl, bcmath, exif: dompdf / openspout (Excel) / general Laravel use
RUN apt-get update && apt-get install -y --no-install-recommends \
        libzip-dev \
        libpng-dev \
        libjpeg62-turbo-dev \
        libfreetype6-dev \
        libicu-dev \
        libxml2-dev \
        libonig-dev \
        libpq-dev \
        unzip \
        git \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j"$(nproc)" \
        gd \
        zip \
        intl \
        bcmath \
        exif \
        pcntl \
        soap \
        pdo_pgsql \
        pdo_mysql \
    && apt-get purge -y --auto-remove git \
    && rm -rf /var/lib/apt/lists/*

WORKDIR /var/www/html

COPY --from=vendor /app /var/www/html
COPY --from=frontend /app/public/build /var/www/html/public/build

# Laravel needs these two directories writable at runtime.
RUN mkdir -p storage/framework/{cache,sessions,views} storage/logs bootstrap/cache \
    && chown -R www-data:www-data storage bootstrap/cache

COPY docker/entrypoint.sh /usr/local/bin/entrypoint.sh
RUN chmod +x /usr/local/bin/entrypoint.sh

# Stays root (not USER www-data): Render's persistent disk is mounted at
# storage/app/public at container start, with ownership the image can't
# know ahead of time, so the entrypoint fixes permissions on every boot
# before the app runs — simplest reliable option for a first deployment.
EXPOSE 10000
ENTRYPOINT ["entrypoint.sh"]
