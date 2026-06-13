FROM php:8.2-cli

# v7 - inline CMD (avoid CRLF), no route:cache
RUN apt-get update -y && apt-get install -y --no-install-recommends \
    git \
    curl \
    unzip \
    libpng-dev \
    libjpeg62-turbo-dev \
    libfreetype6-dev \
    libzip-dev \
    libxml2-dev \
    libicu-dev \
    libonig-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install gd pdo pdo_mysql mbstring zip xml bcmath intl \
    && rm -rf /var/lib/apt/lists/*

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

COPY . .

RUN composer install --no-dev --optimize-autoloader --no-interaction

CMD ["sh", "-c", "echo '=== BOOT ===' && echo PORT=$PORT && php artisan config:cache && echo 'Config OK' && php artisan migrate --force && echo 'Migrate OK' && echo 'Serving on port '${PORT:-8000} && exec php artisan serve --host=0.0.0.0 --port=${PORT:-8000}"]
