FROM php:8.1-fpm

# Install system dependencies
RUN apt-get update && apt-get install -y \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    libzip-dev \
    && rm -rf /var/lib/apt/lists/*

# Configure and install PHP extensions
RUN docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) \
    pdo \
    pdo_mysql \
    gd \
    zip \
    opcache

# Set working directory
WORKDIR /var/www/html

# Copy application source (vendor must be pre-built)
COPY . .

# Create required directories
RUN mkdir -p logs uploads/hero/background uploads/hero/front \
    uploads/bulletin uploads/announcement uploads/together \
    uploads/departments uploads/news

# Set permissions
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html \
    && chmod -R 775 logs uploads

# PHP-FPM configuration
RUN echo "upload_max_filesize = 10M\npost_max_size = 12M\nmax_execution_time = 60\nmemory_limit = 256M" \
    > /usr/local/etc/php/conf.d/custom.ini

EXPOSE 9000

CMD ["php-fpm"]
