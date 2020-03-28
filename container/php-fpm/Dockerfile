FROM surnet/alpine-wkhtmltopdf:3.10-0.12.5-small as wkhtmltopdf
FROM php:7.3-fpm-alpine3.10

RUN apk --no-cache add $PHPIZE_DEPS icu icu-dev libzip libzip-dev \
    && yes '' | pecl install apcu \
    && pecl install xdebug \
    && docker-php-ext-enable apcu xdebug \
    && docker-php-ext-install pdo_mysql intl zip \
    && apk del $PHPIZE_DEPS icu-dev libzip-dev

RUN apk --no-cache add libstdc++ libx11 libxrender libxext libssl1.1 \
    ca-certificates fontconfig freetype
COPY --from=wkhtmltopdf /bin/wkhtmltopdf /usr/local/bin/wkhtmltopdf

RUN apk --no-cache add ttf-inconsolata unzip \
    && wget http://latofonts.com/download/Lato2OFL.zip -O - > /tmp/lato.zip \
    && unzip /tmp/lato.zip -x "__MACOSX/*" -d /usr/share/fonts \
    && fc-cache /usr/share/fonts \
    && rm /tmp/lato.zip \
    && apk del unzip

RUN mv "$PHP_INI_DIR/php.ini-development" "$PHP_INI_DIR/php.ini"

RUN apk --no-cache add su-exec \
    && curl -sS https://getcomposer.org/installer \
    | php -- --install-dir=/usr/local/bin --filename=composer

COPY entrypoint.sh /usr/local/bin/custom-entrypoint
ENTRYPOINT ["custom-entrypoint"]
CMD ["php-fpm"]

WORKDIR /srv
