#!/bin/sh -e

# Wait until application will be ready.
until nc -z app 9000; do sleep 1; done; sleep 1

exec "$@"
