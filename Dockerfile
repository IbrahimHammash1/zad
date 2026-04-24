FROM php:8.3-cli-alpine

WORKDIR /var/www/html

RUN apk add --no-cache \
    bash \
    curl \
    git \
    icu-dev \
    libzip-dev \
    oniguruma-dev \
    postgresql-dev \
    unzip \
    zip \
    && docker-php-ext-install \
    bcmath \
    intl \
    mbstring \
    pdo_pgsql \
    zip

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

COPY composer.json composer.lock ./

COPY . .

RUN composer install --no-dev --prefer-dist --no-interaction --no-progress --optimize-autoloader

RUN mkdir -p storage/framework/{cache,sessions,testing,views} storage/logs bootstrap/cache \
    && chown -R www-data:www-data storage bootstrap/cache

COPY docker/start.sh /usr/local/bin/start.sh
RUN chmod +x /usr/local/bin/start.sh

ENV APP_ENV=production
ENV APP_DEBUG=false
ENV DB_CONNECTION=pgsql
ENV SESSION_DRIVER=file
ENV CACHE_STORE=file
ENV QUEUE_CONNECTION=sync

EXPOSE 10000

CMD ["/usr/local/bin/start.sh"]
