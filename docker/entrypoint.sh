#!/bin/sh
set -e

if [ "$1" = "frankenphp" ] || [ "$1" = "php" ]; then
    until php bin/console doctrine:query:sql 'SELECT 1' >/dev/null 2>&1; do
        echo "[entrypoint] Waiting for database..."
        sleep 1
    done

    echo "[entrypoint] Syncing database schema..."
    php bin/console doctrine:schema:update --force --complete --no-interaction

    USER_COUNT=$(php bin/console doctrine:query:sql 'SELECT COUNT(*) AS c FROM "user"' --no-interaction 2>/dev/null \
        | grep -oE '"c":[[:space:]]*[0-9]+' | grep -oE '[0-9]+' || echo 0)

    if [ -z "$USER_COUNT" ] || [ "$USER_COUNT" = "0" ]; then
        echo "[entrypoint] Empty user table — loading fixtures."
        php bin/console doctrine:fixtures:load --no-interaction
    else
        echo "[entrypoint] Found $USER_COUNT user(s) — skipping fixtures."
    fi
fi

exec docker-php-entrypoint "$@"
