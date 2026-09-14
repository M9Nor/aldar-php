# Mirrors production on Hostinger: PHP 7.4 (ea-php74) serving Laravel 7.
FROM php:7.4-apache

# php:7.4 images are built on Debian bullseye, whose LTS has ended: the regular
# security mirror now lists packages it no longer serves (404). Install from
# the Debian archive instead (main only; fine for a local dev image).
RUN echo "deb http://archive.debian.org/debian bullseye main" > /etc/apt/sources.list \
    && apt-get -o Acquire::Check-Valid-Until=false update \
    && apt-get install -y --no-install-recommends \
        libfreetype6-dev libjpeg62-turbo-dev libpng-dev libwebp-dev \
        libzip-dev libicu-dev unzip git \
    && docker-php-ext-configure gd --with-freetype --with-jpeg --with-webp \
    && docker-php-ext-install -j"$(nproc)" pdo_mysql mysqli gd zip intl bcmath exif opcache \
    && a2enmod rewrite expires headers \
    && rm -rf /var/lib/apt/lists/*

# Serve public/ directly (production reaches it through the root .htaccess rewrite).
ENV APACHE_DOCUMENT_ROOT=/var/www/html/public
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf
COPY apache-aldar.conf /etc/apache2/conf-available/aldar.conf
RUN a2enconf aldar

COPY php.ini /usr/local/etc/php/conf.d/zz-aldar.ini

# Composer 2.2 is the LTS line that still supports PHP 7.x.
COPY --from=composer:2.2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html
