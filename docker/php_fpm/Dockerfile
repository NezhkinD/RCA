FROM composer:latest AS composer
FROM php:7.4.4-fpm-buster

# Copy composer.lock and composer.json
# COPY composer.lock composer.json /var/www/

# Set working directory
WORKDIR /var/www/html

# Install dependencies
RUN apt-get update && apt-get install -y \
    build-essential \
    #mysql-client \service supervisor
    libpng-dev \
    libjpeg62-turbo-dev \
    libfreetype6-dev \
    locales \
    zip \
    jpegoptim optipng pngquant gifsicle \
    vim \
    unzip \
    git \
    curl

# Driver PDO psql. Supervisor
RUN apt-get update && apt-get install -y libpq-dev && docker-php-ext-install pdo pdo_pgsql; \
    apt-get install supervisor -y

# Clear cache
RUN apt-get clean && rm -rf /var/lib/apt/lists/*

# Install extensions
# RUN docker-php-ext-install pdo_mysql mbstring zip exif pcntl
# RUN docker-php-ext-configure gd --with-gd --with-freetype-dir=/usr/include/ --with-jpeg-dir=/usr/include/ --with-png-dir=/usr/include/
RUN docker-php-ext-install gd

# Install composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer


# Install PHP Redis
ENV PHPREDIS_VERSION 3.0.0
RUN mkdir -p /usr/src/php/ext/redis \
    && curl -L https://github.com/phpredis/phpredis/archive/$PHPREDIS_VERSION.tar.gz | tar xvz -C /usr/src/php/ext/redis --strip 1 \
    && echo 'redis' >> /usr/src/php-available-exts \
     && docker-php-ext-install redis


## Expose port 9000 and start php-fpm server
EXPOSE 9000
CMD ["php-fpm"]
