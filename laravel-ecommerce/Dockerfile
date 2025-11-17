# Step1: Base PHP image with Apache
FROM php:8.2-apache

# Step2: Set working directory
WORKDIR /var/www/html

# Fix APT: Replace all Debian URLs to HTTPS
RUN sed -i 's|http://deb.debian.org|https://deb.debian.org|g' /etc/apt/sources.list.d/debian.sources

# Step3: Install system dependencies + PHP extensions
RUN apt-get update && apt-get install -y \
    git unzip curl pkg-config \
    libonig-dev libzip-dev libpng-dev libjpeg62-turbo-dev libfreetype6-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install gd pdo_mysql mbstring zip exif pcntl \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# Step4: Enable Apache mod_rewrite
RUN a2enmod rewrite

# Step4.1: Set Laravel public as Apache root
ENV APACHE_DOCUMENT_ROOT=/var/www/html/public
RUN sed -ri -e 's!/var/www/html!/var/www/html/public!g' /etc/apache2/sites-available/*.conf \
    && sed -ri -e 's!/var/www/!/var/www/html/public!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf

# Step5: Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Step6: Install Node.js & npm
RUN curl -fsSL https://deb.nodesource.com/setup_20.x | bash - \
    && apt-get install -y nodejs \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# Step7: Copy only composer files (cache layer)
COPY composer.json composer.lock ./

# Step8: Install PHP dependencies
RUN composer install --no-dev --optimize-autoloader --no-scripts

# Step9: Copy full Laravel project
COPY . .

# Step10: Copy .env
COPY .env .env

# Step11: Set permissions for storage & cache
RUN chown -R www-data:www-data storage bootstrap/cache public \
    && chmod -R 775 storage bootstrap/cache \
    && chmod -R 755 public

# Step12: Generate key & discover packages
RUN php artisan key:generate --force || true
RUN php artisan package:discover --ansi || true

# Step13: Install frontend dependencies & build assets
RUN npm install
RUN npm run build

# Step14: Expose port
EXPOSE 80

# Step15: Start Apache
RUN echo "ServerName localhost" >> /etc/apache2/apache2.conf
CMD ["apache2-foreground"]
