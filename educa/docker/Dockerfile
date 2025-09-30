FROM php:8.2-fpm

# 1. Install base system dependencies and add contrib/non-free repos
RUN apt-get update && apt-get install -y --no-install-recommends \
    sudo \
    nginx \
    git \
    curl \
    gnupg \
    imagemagick \
    libreoffice \
    # Build dependencies for PHP extensions
    libxml2-dev \
    zlib1g-dev \
    libpng-dev \
    libldb-dev \
    libldap2-dev \
    libsodium-dev \
    libzip-dev \
    libjpeg-dev \
    libfreetype6-dev \
    libwebp-dev \
    && echo "deb http://deb.debian.org/debian bookworm contrib non-free" > /etc/apt/sources.list.d/contrib.list \
    && apt-get update \
    && apt-get install -y ttf-mscorefonts-installer \
    && rm -rf /var/lib/apt/lists/*

# 2. Install PHP extensions
RUN docker-php-ext-configure gd --enable-gd --with-freetype --with-jpeg --with-webp \
    && docker-php-ext-install gd pdo pdo_mysql opcache exif sockets pcntl bcmath ldap sodium xml zip

# 3. Install Node.js
RUN curl -fsSL https://deb.nodesource.com/setup_18.x | bash - \
    && apt-get install -y nodejs

# 4. Install Yarn
RUN mkdir -p /etc/apt/keyrings \
    && curl -sS https://dl.yarnpkg.com/debian/pubkey.gpg | gpg --dearmor -o /etc/apt/keyrings/yarnkey.gpg \
    && echo "deb [signed-by=/etc/apt/keyrings/yarnkey.gpg] https://dl.yarnpkg.com/debian/ stable main" | tee /etc/apt/sources.list.d/yarn.list \
    && apt-get update \
    && apt-get install -y yarn \
    && rm -rf /var/lib/apt/lists/*

# Composer Installation (offizielles Image)
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Copy application code
COPY --chown=www-data:www-data ./educa /usr/share/nginx/html
COPY --chown=www-data:www-data ./educa-permission-sdk /usr/share/nginx/educa-permission-sdk

RUN cd /usr/share/nginx/html && yarn install --immutable --immutable-cache --check-cache && yarn build && composer install && yarn cache clean

# Supercronic for Cronjobs
# Latest releases available at https://github.com/aptible/supercronic/releases
ENV SUPERCRONIC_URL=https://github.com/aptible/supercronic/releases/download/v0.2.29/supercronic-linux-amd64 \
    SUPERCRONIC=supercronic-linux-amd64 \
    SUPERCRONIC_SHA1SUM=cd48d45c4b10f3f0bfdd3a57d054cd05ac96812b

RUN curl -fsSLO "$SUPERCRONIC_URL" \
 && echo "${SUPERCRONIC_SHA1SUM}  ${SUPERCRONIC}" | sha1sum -c - \
 && chmod +x "$SUPERCRONIC" \
 && mv "$SUPERCRONIC" "/usr/local/bin/${SUPERCRONIC}" \
 && ln -s "/usr/local/bin/${SUPERCRONIC}" /usr/local/bin/supercronic

COPY --chown=root:root ./educa/docker/general/crontab.txt /etc/crontab.txt
COPY ./educa/docker/general/entrypoint.sh /entry.sh
RUN chmod 755 /entry.sh

RUN mv "$PHP_INI_DIR/php.ini-production" "$PHP_INI_DIR/php.ini" \
 && sed -E -i -e 's/max_execution_time = [0-9]+/max_execution_time = 240/' $PHP_INI_DIR/php.ini \
 && sed -E -i -e 's/memory_limit = [0-9]+M/memory_limit = 8192M/' $PHP_INI_DIR/php.ini \
 && sed -E -i -e 's/post_max_size = [0-9]+M/post_max_size = 150M/' $PHP_INI_DIR/php.ini \
 && sed -E -i -e 's/upload_max_filesize = [0-9]+M/upload_max_filesize = 100M/' $PHP_INI_DIR/php.ini

# PHP-FPM Tuning
RUN echo "pm = dynamic" >> /usr/local/etc/php-fpm.d/www.conf && \
    echo "pm.max_children = 100" >> /usr/local/etc/php-fpm.d/www.conf && \
    echo "pm.start_servers = 30" >> /usr/local/etc/php-fpm.d/www.conf && \
    echo "pm.min_spare_servers = 5" >> /usr/local/etc/php-fpm.d/www.conf && \
    echo "pm.max_spare_servers = 100" >> /usr/local/etc/php-fpm.d/www.conf

# NGINX
COPY ./educa/docker/nginx/site.conf /etc/nginx/sites-enabled/default

WORKDIR /usr/share/nginx/html

EXPOSE 80

## runtime
CMD ["/entry.sh"]
