FROM docker.io/surnet/alpine-wkhtmltopdf:3.22.0-0.12.6-small AS wkhtmltopdf
FROM docker.io/php:8.4.12-fpm-alpine3.22

RUN sh -ex <<EOF
    apk add --no-cache icu icu-data-full libzip libpng
    apk add --no-cache --virtual build-deps $PHPIZE_DEPS icu-dev libzip-dev libpng-dev linux-headers

    MAKEFLAGS="-j $(nproc)" pecl install -D 'enable-apcu-debug="no"' apcu-5.1.27 xdebug-3.4.6
    docker-php-ext-enable apcu xdebug opcache
    docker-php-ext-install -j $(nproc) gd pdo_mysql intl zip

    apk del build-deps
EOF

RUN apk --no-cache add libstdc++ libx11 libxrender libxext libssl3 ca-certificates fontconfig freetype
COPY --from=wkhtmltopdf /bin/wkhtmltopdf /usr/local/bin/wkhtmltopdf
ADD pdf-fonts.tar.xz /usr/local/share/fonts/

RUN mv "$PHP_INI_DIR/php.ini-development" "$PHP_INI_DIR/php.ini"
COPY "php.ini" "$PHP_INI_DIR/conf.d/99-local.ini"

ADD --checksum=sha256:f446ea719708bb85fcbf4ef18def5d0515f1f9b4d703f6d820c9c1656e10a2f2 --chmod=755 \
    https://getcomposer.org/download/2.8.12/composer.phar /usr/local/bin/composer

# Allow running as root: take advantage of user mapping in rootless Podman.
RUN sed -i 's/^\(user\|group\) = .*$/\1 = root/' /usr/local/etc/php-fpm.d/www.conf
ENV COMPOSER_ALLOW_SUPERUSER=1

COPY --chmod=755 entrypoint.sh /usr/local/bin/custom-entrypoint
ENTRYPOINT ["custom-entrypoint"]
CMD ["php-fpm", "-R"]

WORKDIR /srv
