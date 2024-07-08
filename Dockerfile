FROM php:8.3.7RC1-fpm-alpine3.19

# Copy composer.lock and composer.json into the working directory
COPY composer.lock composer.json /var/www/html/

# Set working directory
WORKDIR /var/www/html/

RUN mkdir -p /var/www/html

RUN apk --no-cache add shadow && usermod -u 1000 www-data

# RUN docker-php-ext-install pdo pdo_mysql
# RUN docker-php-ext-install gd
RUN set -ex \
    && apk --no-cache add \
    postgresql-dev \
    php-pgsql

RUN docker-php-ext-install pdo pdo_pgsql


# Install composer (php package manager)
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer
# RUN composer install
# RUN php artisan route:clear

RUN apk --no-cache add pcre-dev ${PHPIZE_DEPS} \
    && pecl install redis \
    && docker-php-ext-enable redis \
    && apk del pcre-dev ${PHPIZE_DEPS}

# Copy existing application directory contents to the working directory
COPY . /var/www/html

RUN chown -R www-data:www-data \
    /var/www/html/storage \
    /var/www/html/bootstrap/cache

# Expose port 2002 and start php-fpm server (for FastCGI Process Manager)
EXPOSE 9000
CMD ["php-fpm"]


