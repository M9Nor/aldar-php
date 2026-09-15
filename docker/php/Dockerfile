# Local mirror of the upgraded stack: PHP 8.4 on Apache (Debian trixie) serving Laravel 13.
FROM php:8.4-apache

RUN apt-get update \
    && apt-get install -y --no-install-recommends \
        libfreetype-dev libjpeg62-turbo-dev libpng-dev libwebp-dev \
        libzip-dev libicu-dev unzip git \
    && docker-php-ext-configure gd --with-freetype --with-jpeg --with-webp \
    && docker-php-ext-install -j"$(nproc)" pdo_mysql mysqli gd zip intl bcmath exif opcache \
    && a2enmod rewrite expires headers \
    && rm -rf /var/lib/apt/lists/*

ENV APACHE_DOCUMENT_ROOT=/var/www/html/public
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf
COPY apache-aldar.conf /etc/apache2/conf-available/aldar.conf
RUN a2enconf aldar

COPY php.ini /usr/local/etc/php/conf.d/zz-aldar.ini

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html
