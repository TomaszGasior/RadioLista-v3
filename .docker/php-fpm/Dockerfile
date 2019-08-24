FROM php:7.3-fpm-alpine

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

RUN apk --no-cache add $PHPIZE_DEPS icu icu-dev libzip libzip-dev unzip \
    && docker-php-ext-install pdo_mysql intl zip \
    && yes '' | pecl install apcu && docker-php-ext-enable apcu \
    && apk del $PHPIZE_DEPS icu-dev libzip-dev

RUN apk --no-cache add wkhtmltopdf
RUN apk --no-cache add ttf-inconsolata \
    && wget http://latofonts.com/download/Lato2OFL.zip -O - > /tmp/lato.zip \
    && unzip /tmp/lato.zip -x "__MACOSX/*" -d /usr/share/fonts \
    && fc-cache /usr/share/fonts && rm /tmp/lato.zip

ENV COMPOSER_ALLOW_SUPERUSER=1 \
    WKHTMLTOPDF_PATH=/usr/bin/wkhtmltopdf \
    WKHTMLTOIMAGE_PATH=/usr/bin/wkhtmltoimage

RUN mv "$PHP_INI_DIR/php.ini-development" "$PHP_INI_DIR/php.ini"

COPY entrypoint.sh /usr/local/bin/radiolista-entrypoint
ENTRYPOINT ["radiolista-entrypoint"]
CMD ["php-fpm"]

WORKDIR /srv/app
