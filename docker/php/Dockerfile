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

# Composerのインストール
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer
RUN composer self-update && composer clear-cache

# 作業ディレクトリの設定
WORKDIR /var/www

# ソースコードをコピー
COPY ./src /var/www

# Composer installを実行
RUN composer install --no-dev --optimize-autoloader

# ポートの公開
EXPOSE 80

# Supervisorを使用してNginxとPHP-FPMを起動
CMD ["supervisord", "-n", "-c", "/etc/supervisor/conf.d/supervisord.conf"]

