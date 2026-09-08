FROM php:8.3-fpm

RUN apt-get update && apt-get install -y --no-install-recommends \
    libpq-dev libzip-dev libpng-dev libjpeg-dev libfreetype6-dev \
    libonig-dev unzip curl \
    && rm -rf /var/lib/apt/lists/*

RUN docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install pdo_pgsql pgsql mbstring zip gd bcmath

RUN curl -fsSL https://deb.nodesource.com/setup_20.x | bash - \
    && apt-get install -y nodejs \
    && rm -rf /var/lib/apt/lists/*

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www

COPY composer.json composer.lock ./
RUN composer install --no-dev --optimize-autoloader --no-scripts

COPY package.json package-lock.json ./
RUN npm ci

COPY . .

RUN composer dump-autoload --optimize --no-scripts \
    && npm run build \
    && rm -rf node_modules

RUN echo '<?php return [];' > bootstrap/cache/packages.php \
    && echo '<?php return [];' > bootstrap/cache/services.php

RUN chown -R www-data:www-data storage bootstrap/cache \
    && chmod -R 775 storage bootstrap/cache

EXPOSE 9000

CMD ["php-fpm"]
