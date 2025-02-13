#!/bin/sh -e

if [[ ! -d vendor ]]; then
    composer install -n --no-cache
fi

# Wait until database will be ready.
until nc -z db 3306; do sleep 1; done; sleep 1

if ! bin/console doctrine:query:sql "show tables" -q 2> /dev/null; then
    bin/console doctrine:database:create
    bin/console doctrine:schema:create
    bin/console doctrine:fixtures:load -n
fi

docker-php-entrypoint "$@"
