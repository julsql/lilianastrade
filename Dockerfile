FROM dunglas/frankenphp:1-php8.3

RUN install-php-extensions \
    pdo_pgsql \
    intl \
    opcache \
    zip \
    apcu

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

ENV APP_ENV=prod \
    APP_DEBUG=0 \
    SERVER_NAME=:80

WORKDIR /app

COPY . .

# .env placeholder : Symfony (bootEnv) exige /app/.env des le build (cache:warmup).
# Les vraies valeurs sont injectees au runtime par le Secret k8s et priment sur ce
# fichier (Dotenv n'ecrase pas les variables d'environnement deja definies).
RUN printf 'APP_ENV=prod\nAPP_SECRET=build\nDATABASE_URL=postgresql://app:app@database:5432/app?serverVersion=14&charset=utf8\nMAILER_DSN=null://null\nMESSENGER_TRANSPORT_DSN=doctrine://default\n' > .env

RUN composer install \
        --no-dev \
        --no-scripts \
        --optimize-autoloader \
        --no-progress \
        --prefer-dist \
    && mkdir -p var/cache var/log public/uploads public/media \
    && php bin/console cache:warmup --no-debug \
    && php bin/console assets:install public --symlink --no-interaction \
    && chown -R www-data:www-data var public/uploads public/media

COPY docker/entrypoint.sh /usr/local/bin/app-entrypoint
RUN chmod +x /usr/local/bin/app-entrypoint
ENTRYPOINT ["/usr/local/bin/app-entrypoint"]
CMD ["frankenphp", "run", "--config", "/etc/caddy/Caddyfile"]

EXPOSE 80
