FROM php:8.2-apache-bookworm
RUN apt-get update && apt-get install -y --no-install-recommends git unzip libicu-dev libonig-dev libsqlite3-dev libzip-dev \
    && docker-php-ext-install intl mbstring mysqli sqlite3 pdo_sqlite zip \
    && rm -rf /var/lib/apt/lists/*

# 2. set up document root for apache
COPY docker/apache/000-default.conf /etc/apache2/sites-available/000-default.conf

# 3. mod_rewrite for URL rewrite and mod_headers for .htaccess extra headers like Access-Control-Allow-Origin-
RUN a2enmod rewrite headers

# 4. start with base php config, then add extensions
RUN mv "$PHP_INI_DIR/php.ini-development" "$PHP_INI_DIR/php.ini"

# 5. Composer
COPY --from=composer:2 /usr/bin/composer /usr/local/bin/composer

COPY src/ /var/www/html/
# 6. we need a user with the same UID/GID with host user
# so when we execute CLI commands, all the host file's ownership remains intact
# otherwise command from inside container will create root-owned files and directories
ARG uid
RUN useradd -U -G www-data,root -u $uid -d /home/devuser devuser
RUN mkdir -p /home/devuser/.composer && \
    chown -R devuser:devuser /home/devuser

EXPOSE 80
