#!/bin/sh -e

# Make it easier to run the application first time. :)
if [[ ! -d node_modules ]]; then
    su-exec $(stat -c '%u:%g' .) npm install
fi

echo webpack.config.js | su-exec $(stat -c '%u:%g' .) entr -r npm run watch
