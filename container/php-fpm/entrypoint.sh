#!/bin/sh -e

# Fix improper `var` permission caused by docker volume.
chmod 777 var

# Make it easier to run the application first time. :)
if [[ ! -d vendor ]]; then
    su-exec $(stat -c '%u:%g' .) composer install -n --no-cache
fi

# Wait until database will be ready.
until nc -z db 3306; do sleep 1; done; sleep 1

# Create database with example data if doesn't exist.
if ! bin/console doctrine:query:sql "show tables" -q 2> /dev/null; then
    bin/console doctrine:database:create
    bin/console doctrine:schema:create
    bin/console doctrine:fixtures:load -n
fi

docker-php-entrypoint "$@"
