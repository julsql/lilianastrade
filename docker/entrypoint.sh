#!/bin/sh
set -e

if [ "$1" = "frankenphp" ] || [ "$1" = "php" ]; then
    until php bin/console dbal:run-sql 'SELECT 1' >/dev/null 2>&1; do
        echo "[entrypoint] Waiting for database..."
        sleep 1
    done

    echo "[entrypoint] Syncing database schema..."
    php bin/console doctrine:schema:update --force --no-interaction

    # Sortie tabulaire de dbal:run-sql : on garde la seule ligne purement numérique.
    USER_COUNT=$(php bin/console dbal:run-sql 'SELECT COUNT(*) AS c FROM "user"' --no-interaction 2>/dev/null \
        | grep -oE '^[[:space:]]*[0-9]+[[:space:]]*$' | tr -d '[:space:]' || true)

    # fixtures:load purge la base : uniquement si la table est vide de façon certaine.
    if [ "$USER_COUNT" = "0" ]; then
        echo "[entrypoint] Empty user table — loading fixtures."
        php bin/console doctrine:fixtures:load --no-interaction
    elif [ -z "$USER_COUNT" ]; then
        echo "[entrypoint] Could not count users — skipping fixtures."
    else
        echo "[entrypoint] Found $USER_COUNT user(s) — skipping fixtures."
    fi
fi

exec docker-php-entrypoint "$@"
