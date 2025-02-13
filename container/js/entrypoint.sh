#!/bin/sh -e

if [[ ! -d node_modules ]]; then
    npm clean-install
fi

echo webpack.config.js | entr -rn npm run watch
