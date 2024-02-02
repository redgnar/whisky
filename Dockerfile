FROM php:7.4-fpm-alpine as php

FROM php AS whisky_dev_0

# persistent / runtime deps
# hadolint ignore=DL3018
RUN apk add --no-cache \
		acl \
		file \
		gettext \
		git \
	;

RUN set -eux;


COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

ENV PATH="${PATH}:/root/.composer/vendor/bin"

# https://getcomposer.org/doc/03-cli.md#composer-allow-superuser
ENV COMPOSER_ALLOW_SUPERUSER=1

WORKDIR /srv/app

ARG XDEBUG_VERSION=3.1.4
RUN set -eux; \
		apk add --no-cache --virtual .build-deps $PHPIZE_DEPS; \
		pecl install xdebug-$XDEBUG_VERSION; \
		docker-php-ext-enable xdebug; \
		apk del .build-deps

VOLUME /var/run/php
VOLUME /srv/app/var

# ENTRYPOINT ["docker-entrypoint"]
CMD ["php-fpm"]

