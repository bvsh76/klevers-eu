FROM php:8.4-fpm-alpine
RUN apk add --no-cache git unzip libpq-dev icu-dev libzip-dev $PHPIZE_DEPS linux-headers
RUN docker-php-ext-install pdo_pgsql intl zip bcmath pcntl opcache
RUN pecl install redis && docker-php-ext-enable redis
RUN sed -i -e 's/^user = www-data/user = root/' -e 's/^group = www-data/group = root/' /usr/local/etc/php-fpm.d/www.conf
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer
WORKDIR /var/www/html
CMD ["php-fpm", "-R"]
