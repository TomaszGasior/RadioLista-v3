FROM php:7.3-fpm-buster

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

RUN apt-get update -qq

# https://github.com/docker-library/php/issues/57#issuecomment-418976885
# https://github.com/docker-library/php/issues/61
RUN apt-get install -yqq --no-install-recommends libicu-dev libzip-dev unzip \
    && docker-php-ext-install intl zip pdo_mysql \
    && yes '' | pecl install apcu && docker-php-ext-enable apcu

RUN apt-get install -yqq --no-install-recommends wkhtmltopdf
RUN apt-get install -yqq --no-install-recommends fonts-lato fonts-inconsolata

ENV COMPOSER_ALLOW_SUPERUSER=1 \
    WKHTMLTOPDF_PATH=/usr/bin/wkhtmltopdf \
    WKHTMLTOIMAGE_PATH=/usr/bin/wkhtmltoimage

RUN mv "$PHP_INI_DIR/php.ini-development" "$PHP_INI_DIR/php.ini"

WORKDIR /srv/app
