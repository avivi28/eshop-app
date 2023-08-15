# Use the official PHP 8.1.0 FPM image as the base image
FROM php:8.1.0-fpm

# Install additional dependencies
RUN apt-get update && apt-get install -y \
    git \
    unzip \
    libzip-dev \
    && docker-php-ext-install zip pdo_mysql

# Set the working directory
WORKDIR /var/www/html

# Set the COMPOSER_ALLOW_SUPERUSER environment variable
ENV COMPOSER_ALLOW_SUPERUSER 1

# Copy the rest of the application code
COPY . .

# Copy the composer.json and composer.lock files to the container
# COPY composer.json composer.lock 

# Install PHP dependencies
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer
RUN composer install --no-scripts --no-autoloader


# Generate the autoload files
RUN composer dump-autoload --optimize

# Set the permissions for Laravel storage and bootstrap cache directories
RUN chown -R www-data:www-data storage bootstrap/cache

# Expose port 9000
EXPOSE 9000

# Start PHP-FPM
CMD ["php-fpm"]