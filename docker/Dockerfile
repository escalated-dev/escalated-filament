# syntax=docker/dockerfile:1.7

# ---------- 1. Composer deps ----------
FROM composer:2 AS vendor

# The path-repo target for the plugin under test.
COPY composer.json /package/composer.json
COPY src /package/src
COPY resources /package/resources

COPY docker/host-app/composer.json /host/composer.json
WORKDIR /host
RUN composer install \
    --no-interaction --no-progress --no-dev --prefer-dist \
    --optimize-autoloader --no-scripts \
    --ignore-platform-req=ext-intl \
    --ignore-platform-req=ext-zip \
    --ignore-platform-req=ext-gd

# ---------- 2. Runtime ----------
FROM php:8.3-cli-alpine AS runtime

RUN apk add --no-cache postgresql-client libpq icu-libs libzip libpng \
    && apk add --no-cache --virtual .build-deps \
        $PHPIZE_DEPS \
        postgresql-dev \
        icu-dev \
        libzip-dev \
        libpng-dev \
    && docker-php-ext-install \
        pdo_pgsql \
        intl \
        zip \
        gd \
        opcache \
    && apk del --no-network .build-deps \
    && rm -rf /tmp/* /var/cache/apk/*

WORKDIR /host

COPY --from=vendor /package /package
COPY --from=vendor /host /host
COPY docker/host-app/ /host/

COPY docker/entrypoint.sh /usr/local/bin/entrypoint.sh
RUN chmod +x /usr/local/bin/entrypoint.sh \
    && mkdir -p /host/storage/framework/cache/data /host/storage/framework/sessions /host/storage/framework/views /host/storage/logs /host/bootstrap/cache \
    && chmod -R 777 /host/storage /host/bootstrap/cache \
    && cd /host && php artisan package:discover --ansi

ENV APP_ENV=demo

EXPOSE 8000

ENTRYPOINT ["entrypoint.sh"]
CMD ["php", "artisan", "serve", "--host=0.0.0.0", "--port=8000"]
