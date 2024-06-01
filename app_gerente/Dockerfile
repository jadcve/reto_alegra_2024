# Use the official PHP 8.2 image as the base image
FROM php:8.2-fpm

# Set the working directory
WORKDIR /var/www

# Install system dependencies and PHP extensions
RUN apt-get update && apt-get install -y \
    build-essential \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    locales \
    zip \
    jpegoptim optipng pngquant gifsicle \
    vim \
    unzip \
    git \
    curl \
    libonig-dev \
    libzip-dev \
    libmariadb-dev \
    redis-tools \
    && docker-php-ext-install pdo pdo_mysql mbstring zip exif pcntl \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install gd \
    && pecl install redis \
    && docker-php-ext-enable redis

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Copy existing application directory contents
COPY . /var/www

# Set permissions for Laravel
RUN chown -R www-data:www-data /var/www/storage /var/www/bootstrap/cache /var/www/app /var/www/config /var/www/database
RUN chmod -R 775 /var/www/storage /var/www/bootstrap/cache /var/www/app /var/www/config /var/www/database

# Copy the entrypoint script
COPY entrypoint.sh /entrypoint.sh
RUN chmod +x /entrypoint.sh

# Expose port 9000 and start php-fpm server
EXPOSE 9000

# Run as root to set permissions, then switch to www-data
USER root
ENTRYPOINT ["/entrypoint.sh"]
CMD ["php-fpm"]
