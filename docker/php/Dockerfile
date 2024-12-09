# ベースイメージとしてPHP 8.1 FPMを使用
FROM php:8.1-fpm

# 必要なパッケージをインストール
RUN apt-get update && apt-get install -y --no-install-recommends \
    # MySQLクライアント（データベース接続用）
    default-mysql-client \
    # PHP拡張モジュールの依存関係
    zlib1g-dev libzip-dev libgd-dev unzip \
    # Cron（定期タスク用）
    cron \
    # プロセス管理ツール
    supervisor \
    # ImageMagick（画像処理用）
    libmagickwand-dev --no-install-recommends && \
    # PHPのGD拡張を設定（画像処理機能）
    docker-php-ext-configure gd --with-freetype --with-jpeg && \
    # 必要なPHP拡張をインストール
    docker-php-ext-install pdo_mysql zip bcmath gd && \
    # ImageMagickのPHP拡張をインストール
    pecl install imagick && \
    docker-php-ext-enable imagick && \
    # キャッシュをクリアして不要ファイルを削除（軽量化）
    apt-get clean && rm -rf /var/lib/apt/lists/*

# Composer（PHP依存関係管理ツール）のインストール
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# PHP設定ファイル（php.ini）を配置
COPY ./docker/php/php.ini /usr/local/etc/php/

# 作業ディレクトリを設定
WORKDIR /var/www

# アプリケーションコードをコンテナにコピー
COPY ./src /var/www

# パーミッション設定：ファイル・フォルダの権限を調整
RUN chown -R www-data:www-data /var/www && chmod -R 755 /var/www

# LaravelのQRコード用ディレクトリを作成し、権限を設定
RUN mkdir -p /var/www/storage/app/public/qrcodes && \
    chmod -R 775 /var/www/storage && \
    chown -R www-data:www-data /var/www/storage

# Laravelの依存関係をComposerでインストール
RUN composer install --no-dev --optimize-autoloader --no-scripts

# Cronジョブの設定をコンテナ内に配置
COPY ./docker/php/cronjob /etc/cron.d/laravel-cron
RUN chmod 0644 /etc/cron.d/laravel-cron && crontab /etc/cron.d/laravel-cron

# Supervisorの設定ファイルを配置
COPY ./docker/php/supervisord.conf /etc/supervisor/conf.d/supervisord.conf

EXPOSE 10000

# Supervisorを使ってPHP-FPMとCronを同時に管理
CMD ["nginx", "-g", "daemon off;"]