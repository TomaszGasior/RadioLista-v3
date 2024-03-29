FROM surnet/alpine-wkhtmltopdf:3.13.5-0.12.6-small as wkhtmltopdf
FROM php:8.3.2-fpm-alpine3.18

RUN apk --no-cache add $PHPIZE_DEPS icu icu-data-full icu-dev libzip libzip-dev libpng libpng-dev linux-headers \
    && yes '' | pecl install apcu \
    && pecl install xdebug-3.3.1 \
    && docker-php-ext-enable apcu xdebug opcache \
    && docker-php-ext-install gd pdo_mysql intl zip \
    && apk del $PHPIZE_DEPS icu-dev libzip-dev libpng-dev linux-headers

RUN apk --no-cache add libstdc++ libx11 libxrender libxext libssl1.1 \
    ca-certificates fontconfig freetype
COPY --from=wkhtmltopdf /bin/wkhtmltopdf /usr/local/bin/wkhtmltopdf

RUN apk --no-cache add ttf-inconsolata unzip \
    && wget https://www.latofonts.com/files/Lato2OFL.zip -O - > /tmp/lato.zip \
    && unzip /tmp/lato.zip -x "__MACOSX/*" -d /usr/share/fonts \
    && fc-cache /usr/share/fonts \
    && rm /tmp/lato.zip \
    && apk del unzip

RUN mv "$PHP_INI_DIR/php.ini-development" "$PHP_INI_DIR/php.ini"
COPY "php.ini" "$PHP_INI_DIR/conf.d/99-local.ini"

RUN apk --no-cache add su-exec \
    && curl -sS https://getcomposer.org/installer \
    | php -- --install-dir=/usr/local/bin --filename=composer --version 2.5.5

COPY entrypoint.sh /usr/local/bin/custom-entrypoint
ENTRYPOINT ["custom-entrypoint"]
CMD ["php-fpm"]

WORKDIR /srv
