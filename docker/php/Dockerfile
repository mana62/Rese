# ベースイメージに PHP 8.1 FPM を指定
FROM php:8.1-fpm

# 必要なパッケージをインストール
RUN apt-get update && apt-get install -y \
    nginx \
    default-mysql-client \
    zlib1g-dev \
    libzip-dev \
    libgd-dev \
    unzip \
    cron \
    supervisor \
    libmagickwand-dev --no-install-recommends \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install pdo_mysql zip bcmath gd \
    && pecl install imagick \
    && docker-php-ext-enable imagick \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

# ストレージディレクトリを確保し、権限を設定
RUN mkdir -p /var/www/storage/app/public/qrcodes && \
    chmod -R 775 /var/www/storage && \
    chown -R www-data:www-data /var/www/storage

# Composerのインストール
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Composerのバージョンを更新しキャッシュをクリア
RUN composer self-update && composer clear-cache

# 作業ディレクトリの設定
WORKDIR /var/www

# ソースコードをコピー
COPY ./src /var/www

# パーミッション設定
RUN chown -R www-data:www-data /var/www && chmod -R 755 /var/www

# QRコード用ディレクトリの作成と権限設定
RUN mkdir -p /var/www/storage/app/public/qrcodes && \
    chmod -R 775 /var/www/storage && \
    chown -R www-data:www-data /var/www/storage

# Composer installを実行
RUN composer install --no-dev --optimize-autoloader || true

# cronジョブの設定ファイルをコピー
COPY ./cronjob /etc/cron.d/laravel-cron
RUN chmod 0644 /etc/cron.d/laravel-cron && crontab /etc/cron.d/laravel-cron

# Supervisor設定ファイルをコピー
COPY ./supervisord.conf /etc/supervisor/conf.d/supervisord.conf

# Supervisorを使ってPHP-FPMとcronを同時に実行
CMD ["supervisord", "-c", "/etc/supervisor/conf.d/supervisord.conf"]
