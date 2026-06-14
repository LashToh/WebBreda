# WebEngine CMS 1.2.7 — PHP 8.3 + Apache, MSSQL via pdo_dblib (FreeTDS)
#
# The WebEngine core is downloaded at build time into /opt/webengine-core and
# is NOT part of this repo. Our customizations live in ./src and are overlaid
# on top of the core at container start (see entrypoint.sh / overlay.sh).
FROM php:8.3-apache

# WebEngine release to download (full source tree at the matching git tag).
ARG WEBENGINE_VERSION=1.2.7

# System dependencies:
#  - freetds-dev  -> pdo_dblib (SQL Server driver, SQL_PDO_DRIVER=1)
#  - libpng/jpeg/freetype, libonig -> gd, mbstring
#  - gettext-base -> envsubst (renders the config template at runtime)
RUN apt-get update && apt-get install -y --no-install-recommends \
        freetds-dev freetds-common \
        libpng-dev libjpeg-dev libfreetype6-dev \
        libcurl4-openssl-dev libssl-dev libonig-dev \
        unzip zip curl gettext-base ca-certificates inotify-tools \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j"$(nproc)" pdo_dblib gd curl mbstring \
    && a2enmod rewrite headers \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# Sensible PHP defaults for local development.
RUN { \
        echo 'display_errors = On'; \
        echo 'error_reporting = E_ALL & ~E_DEPRECATED & ~E_NOTICE'; \
        echo 'upload_max_filesize = 32M'; \
        echo 'post_max_size = 32M'; \
    } > /usr/local/etc/php/conf.d/zz-webengine.ini

# Download the WebEngine CMS core (baked into the image, kept out of the webroot).
RUN curl -fsSL "https://codeload.github.com/lautaroangelico/WebEngine/tar.gz/refs/tags/${WEBENGINE_VERSION}" -o /tmp/we.tgz \
    && mkdir -p /opt/webengine-core \
    && tar -xzf /tmp/we.tgz -C /opt/webengine-core --strip-components=1 \
    && rm /tmp/we.tgz

# Tooling: config template, headless installer, overlay + entrypoint scripts, vhost.
COPY webengine.json.tpl /opt/webengine/webengine.json.tpl
COPY install-cli.php    /opt/webengine/install-cli.php
COPY overlay.sh         /usr/local/bin/overlay.sh
COPY entrypoint.sh      /usr/local/bin/entrypoint.sh
COPY vhost.conf         /etc/apache2/sites-available/000-default.conf
RUN chmod +x /usr/local/bin/overlay.sh /usr/local/bin/entrypoint.sh

WORKDIR /var/www/html
ENTRYPOINT ["/usr/local/bin/entrypoint.sh"]
CMD ["apache2-foreground"]
