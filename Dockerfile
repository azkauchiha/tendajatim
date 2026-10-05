FROM composer:2 AS composer

FROM php:8.3-apache

RUN apt-get update \
    && apt-get install -y --no-install-recommends libicu-dev libonig-dev libpq-dev \
    && docker-php-ext-install intl mbstring pgsql pdo_pgsql \
    && a2enmod rewrite \
    && rm -rf /var/lib/apt/lists/*

COPY --from=composer /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html
COPY . .

RUN composer install --no-dev --no-interaction --prefer-dist --no-progress --optimize-autoloader \
    && cp docker/000-default.conf /etc/apache2/sites-available/000-default.conf \
    && printf 'Listen 10000\n' > /etc/apache2/ports.conf \
    && mkdir -p writable/cache writable/logs writable/session writable/uploads writable/debugbar \
    && chown -R www-data:www-data writable \
    && chmod -R ug+rwX writable \
    && chmod +x docker/entrypoint.sh

EXPOSE 10000

ENTRYPOINT ["docker/entrypoint.sh"]
