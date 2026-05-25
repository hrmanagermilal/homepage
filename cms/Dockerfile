FROM php:8.2-apache

# ── System dependencies ───────────────────────────────────────────────────────
RUN apt-get update && apt-get install -y --no-install-recommends \
        libpng-dev \
        libjpeg-dev \
        libwebp-dev \
    && rm -rf /var/lib/apt/lists/*

# ── PHP extensions ────────────────────────────────────────────────────────────
RUN docker-php-ext-configure gd --with-jpeg --with-webp \
    && docker-php-ext-install -j$(nproc) \
        pdo \
        pdo_mysql \
        gd \
        exif

# ── Apache: enable mod_rewrite for .htaccess URL routing ─────────────────────
RUN a2enmod rewrite

# ── Apache: configure DocumentRoot to CMS/public/ ───────────────────────
RUN { \
    echo '<VirtualHost *:80>'; \
    echo '    ServerAdmin webmaster@localhost'; \
    echo '    DocumentRoot /var/www/html/public'; \
    echo '    <Directory /var/www/html/public>'; \
    echo '        Options -Indexes +FollowSymLinks'; \
    echo '        AllowOverride All'; \
    echo '        Require all granted'; \
    echo '    </Directory>'; \
    echo '    ErrorLog ${APACHE_LOG_DIR}/error.log'; \
    echo '    CustomLog ${APACHE_LOG_DIR}/access.log combined'; \
    echo '</VirtualHost>'; \
} > /etc/apache2/sites-available/000-default.conf

# ── PHP runtime config ────────────────────────────────────────────────────────
COPY docker/php.ini /usr/local/etc/php/conf.d/milal-cms.ini

# ── Application source ────────────────────────────────────────────────────────
COPY . /var/www/html/

# ── Stage static uploads separately so the volume mount doesn't hide them ────
# The entrypoint seeds these into the volume with `cp -rn` (no-overwrite).
COPY public/uploads/ /var/www/html/static-uploads/

# ── Uploads directory: writable by Apache ────────────────────────────────────
RUN mkdir -p /var/www/html/public/uploads \
    && chown -R www-data:www-data /var/www/html/public/uploads \
    && chmod -R 755 /var/www/html/public/uploads

# ── Entrypoint: seed static uploads into volume, then start Apache ────────────
COPY docker/entrypoint.sh /usr/local/bin/entrypoint.sh
RUN chmod +x /usr/local/bin/entrypoint.sh

EXPOSE 80
CMD ["entrypoint.sh"]
