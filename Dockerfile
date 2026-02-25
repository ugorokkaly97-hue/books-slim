FROM php:8.3-cli-alpine

RUN apk add --no-cache \
    bash git curl unzip zip busybox-extras

RUN docker-php-ext-install pdo_mysql

RUN curl -sS https://getcomposer.org/installer \
  | php -- --install-dir=/usr/local/bin --filename=composer

WORKDIR /var/www