FROM php:8.1-fpm

# 必要なパッケージをインストール
RUN apt-get update && \
    DEBIAN_FRONTEND=noninteractive apt-get install -y --no-install-recommends \
    default-mysql-client \
    zlib1g-dev libzip-dev libgd-dev unzip \
    cron \
    supervisor \
    libmagickwand-dev \
    nginx && \
    docker-php-ext-configure gd --with-freetype --with-jpeg && \
    docker-php-ext-install pdo_mysql zip bcmath gd && \
    pecl install imagick && \
    apt-get clean && rm -rf /var/lib/apt/lists/*

# Nginx設定ファイルのコピーとリンク作成
COPY ./docker/nginx/default.conf /etc/nginx/sites-available/default
RUN rm -f /etc/nginx/sites-enabled/default && \
    ln -s /etc/nginx/sites-available/default /etc/nginx/sites-enabled/default

# Supervisor設定のコピー
COPY ./docker/php/supervisord.conf /etc/supervisor/conf.d/supervisord.conf

# 作業ディレクトリの設定
WORKDIR /var/www

# アプリケーションコードのコピー
COPY ./src /var/www

# .envファイルをコンテナにコピー
COPY ./src/.env /var/www/.env
RUN chown -R www-data:www-data /var/www && chmod -R 755 /var/www

# Composerのインストール
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer
RUN composer install --no-dev --optimize-autoloader

# Laravelのキャッシュクリアとキー生成
RUN php artisan config:clear && \
    php artisan cache:clear && \
    php artisan key:generate

# 必要なディレクトリの作成
RUN mkdir -p /var/www/storage/app/public/qrcodes && \
    chmod -R 775 /var/www/storage && \
    chown -R www-data:www-data /var/www/storage

# Cronジョブの設定
COPY ./docker/php/cronjob /etc/cron.d/laravel-cron
RUN chmod 0644 /etc/cron.d/laravel-cron && crontab /etc/cron.d/laravel-cron

# PHP設定のコピー
COPY ./docker/php/php.ini /usr/local/etc/php/

# ポートの公開
EXPOSE 80

# Supervisorを使用してNginxとPHP-FPMを起動
CMD ["supervisord", "-n", "-c", "/etc/supervisor/conf.d/supervisord.conf"]
