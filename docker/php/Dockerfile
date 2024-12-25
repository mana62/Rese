FROM php:8.1-fpm
RUN apt-get update && apt-get install -y --no-install-recommends \
    default-mysql-client \
    zlib1g-dev libzip-dev libgd-dev unzip \
    cron \
    supervisor \
    libmagickwand-dev --no-install-recommends && \
    docker-php-ext-configure gd --with-freetype --with-jpeg && \
    docker-php-ext-install pdo_mysql zip bcmath gd && \
    pecl install imagick && \
    docker-php-ext-enable imagick && \
    apt-get clean && rm -rf /var/lib/apt/lists/*
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer
COPY ./docker/php/php.ini /usr/local/etc/php/
WORKDIR /var/www
COPY ./src /var/www
RUN chown -R www-data:www-data /var/www && chmod -R 755 /var/www
RUN mkdir -p /var/www/storage/app/public/qrcodes && \
    chmod -R 775 /var/www/storage && \
    chown -R www-data:www-data /var/www/storage
RUN composer install --no-dev --optimize-autoloader
RUN composer dump-autoload --optimize
RUN php artisan config:clear && \
    php artisan route:clear && \
    php artisan view:clear && \
    php artisan config:cache && \
    php artisan route:cache && \
    php artisan view:cache
COPY ./docker/php/cronjob /etc/cron.d/laravel-cron
RUN chmod 0644 /etc/cron.d/laravel-cron && crontab /etc/cron.d/laravel-cron
COPY ./docker/php/supervisord.conf /etc/supervisor/conf.d/supervisord.conf
EXPOSE 80
CMD ["/usr/bin/supervisord"]
