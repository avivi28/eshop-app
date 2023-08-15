# Use an official PHP runtime as the base image
FROM php:8.1.0-fpm

# Set the working directory inside the container
WORKDIR /var/www/html

# Install system dependencies
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip \
    && docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd

# Install Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Copy the project files into the container
COPY . .

# Install project dependencies
RUN composer install --no-interaction --no-scripts --no-suggest

# Generate the Laravel application key
RUN php artisan key:generate

# Expose port 9000 to the outside world
EXPOSE 9000

# Start the PHP FPM server
CMD ["php-fpm"]