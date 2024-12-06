FROM php:8.2-fpm

# Install system dependencies and PHP extensions
RUN apt-get update && apt-get install -y \
    libmagickwand-dev \
    libzip-dev \
    zip \
    libpng-dev \
    && docker-php-ext-install pdo pdo_mysql zip gd \
    && pecl install imagick \
    && docker-php-ext-enable imagick

#
RUN curl -fsSL https://deb.nodesource.com/setup_18.x | bash - \
    && apt-get install -y nodejs

# Verify Node.js and npm installation
RUN node -v && npm -v


# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /var/www/html

# Set appropriate permissions
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html

# Copy the application code
COPY . .

# Install PHP dependencies
RUN composer install --ignore-platform-reqs || echo "Composer failed to install dependencies"

# Install npm dependencies and run production build
RUN npm install && npm run prod

EXPOSE 8080
CMD ["php-fpm"]
CMD ["php", "artisan", "serve", "--host=0.0.0.0", "--port=8080"]