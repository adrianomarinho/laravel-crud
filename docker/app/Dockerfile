FROM php:7.3-fpm

ENV COMPOSER_ALLOW_SUPERUSER=1

RUN apt-get update -y && apt-get install -y \
    libpng-dev \
    zlib1g-dev \
    libfreetype6-dev \
    libwebp-dev \
    libjpeg62-turbo-dev \
    unzip \
    libzip-dev \
    curl \
    libmcrypt-dev \
    libjpeg-dev \
    libbz2-dev \
    libc-client-dev \
    libkrb5-dev \
    libmagickwand-dev --no-install-recommends \
    cron \
    nano \
    git \
    zip \
    sudo \
    libicu-dev \
    libreadline-dev \
    libfreetype6-dev \
    supervisor \
    g++

RUN pecl install imagick redis

RUN docker-php-ext-enable imagick redis

RUN docker-php-ext-install pdo_mysql mysqli zip gd

RUN docker-php-ext-configure imap --with-kerberos --with-imap-ssl \
    && docker-php-ext-install imap

# Provisioning
RUN echo $'\n\n'"# Installing Composer"$'\n' && \
  curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer