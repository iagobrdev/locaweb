# We start from an image with pre-installed PHP 8.3
FROM php:8.3-cli

# Install dependencies for Laravel
RUN apt-get update && apt-get install -y \
    git \
    zip \
    curl \
    unzip \
    libonig-dev \
    libxml2-dev \
    libpng-dev

# Install PHP extensions
RUN docker-php-ext-install mbstring exif pcntl bcmath gd

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Set work directory
WORKDIR /app

# Copy existing application directory contents to work directory
COPY . .

# Install composer dependencies
RUN composer install

# Expose port 8000
EXPOSE 8000

# Run the application
CMD php artisan serve --host=0.0.0.0 --port=8000
