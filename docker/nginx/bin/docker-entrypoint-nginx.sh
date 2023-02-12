#!/usr/bin/env sh
set -eu

envsubst '${PROJECT_NAME}' < /etc/nginx/conf.d/default.conf.template > /etc/nginx/conf.d/default.conf

exec "$@"
