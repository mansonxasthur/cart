FROM php:latest

# Install modules : GD iconv
RUN apt-get update && apt-get install -y \
        procps \
        libfreetype6-dev \
        libjpeg62-turbo-dev \
        libpng-dev \
        openssl \
        libssh-dev \
        libpcre3 \
        libpcre3-dev \
        libnghttp2-dev \
        libhiredis-dev \
        libonig-dev \
        libmagickwand-dev \
        curl \
        wget \
        zip \
        unzip \
        supervisor \
        git && \
        apt autoremove && apt clean

# Install PHP pdo_mysql opcache
RUN docker-php-ext-configure gd --with-freetype=/usr/include/ --with-jpeg=/usr/include/ && \
    docker-php-ext-install \
    gd \
    pdo_mysql \
    mysqli \
    iconv \
    mbstring \
    json \
    opcache \
    sockets \
    pcntl && \
    docker-php-ext-enable opcache

# Install Redis
RUN pecl install redis /dev/null && \
    docker-php-ext-enable redis

# Install Mongodb
RUN pecl install mongodb /dev/null && \
    docker-php-ext-enable mongodb

# Install Composer
ENV COMPOSER_ALLOW_SUPERUSER 1
RUN curl -sS https://getcomposer.org/installer | php && \
    mv composer.phar /usr/local/bin/composer && \
    composer self-update --clean-backups

# Install Swoole
RUN pecl install swoole > /dev/null && \
    docker-php-ext-enable swoole

# Set default work directory
WORKDIR /var/www/html
