FROM php:8.1-fpm

# 必要なパッケージをインストール
RUN apt-get update \
    && apt-get install -y \
    nginx \
    default-mysql-client \
    zlib1g-dev \
    libzip-dev \
    unzip \
    && docker-php-ext-install pdo_mysql zip bcmath \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

# Composerのインストール
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer
RUN composer self-update && composer clear-cache

# PostgreSQLが必要な場合にのみインストール
ARG INSTALL_PGSQL=false
RUN if [ "$INSTALL_PGSQL" = "true" ]; then apt-get install -y libpq-dev && docker-php-ext-install pdo pdo_pgsql; fi

# 作業ディレクトリの設定
WORKDIR /var/www

# ソースコードをコピー
COPY ./src /var/www

# Composer installを実行
RUN composer install --no-dev --optimize-autoloader

# Nginxの設定ファイルを環境ごとに切り替える
ARG NGINX_CONF_FILE=default.conf
COPY ./docker/nginx/${NGINX_CONF_FILE} /etc/nginx/conf.d/default.conf

# アプリケーションをPHP-FPMで起動
CMD ["php-fpm"]