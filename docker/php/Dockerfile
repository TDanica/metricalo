# Use the base PHP-FPM image
FROM php:8.2-fpm

# Set the working directory inside the container
WORKDIR /var/www/html

# Install system dependencies and PHP extensions
RUN apt-get update && apt-get install -y \
    git \
    unzip \
    libicu-dev \
    libpq-dev \
    libzip-dev \
    zip \
    && docker-php-ext-install intl pdo pdo_mysql pdo_pgsql opcache zip \
    && pecl install redis \
    && docker-php-ext-enable redis

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Copy composer.json and composer.lock from the build context (project root)
COPY composer.json ./
COPY composer.lock ./

# Install Symfony dependencies
RUN composer install --no-scripts --no-autoloader

# Copy the rest of the project files to the working directory
COPY . .

# Run Composer again to update autoloaders, etc.
RUN composer dump-autoload --optimize

# Set appropriate permissions
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html

# Expose port 9000 for PHP-FPM
EXPOSE 9000

# Start PHP-FPM
CMD ["php-fpm"]
