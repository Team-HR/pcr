FROM php:8.2-apache

# Install dependencies and PHP extensions in single layer
RUN apt-get update \
    && apt-get install -y --no-install-recommends \
        libzip-dev \
        libfreetype6-dev \
        libjpeg62-turbo-dev \
        libpng-dev \
        libssl-dev \
        pkg-config \
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
    && a2enmod rewrite \
    && chown -R www-data:www-data /var/www/ \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

# Install Redis PHP extension from GitHub
RUN curl -fsSL https://github.com/phpredis/phpredis/archive/refs/tags/6.1.0.tar.gz -o /tmp/redis.tar.gz \
    && cd /tmp \
    && tar -xzf redis.tar.gz \
    && cd phpredis-6.1.0 \
    && phpize \
    && ./configure \
    && make -j$(nproc) \
    && make install \
    && docker-php-ext-enable redis \
    && rm -rf /tmp/redis* || echo "Redis extension installation failed - may cause errors"

# Optional: Install and enable xdebug
# RUN pecl install xdebug && docker-php-ext-enable xdebug

WORKDIR /var/www/html

EXPOSE 80 443
