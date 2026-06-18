FROM php:7.4-apache

# Install dependencies and PHP extensions in single layer
RUN apt-get update \
    && apt-get install -y --no-install-recommends \
        libzip-dev \
        libfreetype6-dev \
        libjpeg62-turbo-dev \
        libpng-dev \
        unzip \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) \
        pdo \
        pdo_mysql \
        mysqli \
        bcmath \
        calendar \
        zip \
        gd \
    && pecl install redis \
    && docker-php-ext-enable redis \
    && a2enmod rewrite \
    && chown -R www-data:www-data /var/www/ \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

# Optional: Install and enable xdebug
# RUN pecl install xdebug && docker-php-ext-enable xdebug

WORKDIR /var/www/html

EXPOSE 80 443
