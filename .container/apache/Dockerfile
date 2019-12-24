FROM httpd:2.4-alpine

COPY php-fpm.conf conf/extra/

RUN echo "Include conf/extra/php-fpm.conf" >> conf/httpd.conf \
    && sed -i conf/httpd.conf \
    -e 's#/usr/local/apache2/htdocs#/srv/public#' \
    -e 's/^#\(LoadModule .*mod_proxy.so\)/\1/' \
    -e 's/^#\(LoadModule .*mod_proxy_fcgi.so\)/\1/' \
    -e 's/^#\(LoadModule .*mod_rewrite.so\)/\1/' \
    -e 's/AllowOverride None/AllowOverride All/'

COPY entrypoint.sh /usr/local/bin/custom-entrypoint
ENTRYPOINT ["/usr/local/bin/custom-entrypoint"]
CMD ["httpd-foreground"]
