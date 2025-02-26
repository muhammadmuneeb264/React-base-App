# Use official PHP image
FROM php:7.4-apache

# Enable Apache mod_rewrite
RUN a2enmod rewrite

# Install pdo_mysql extension
RUN docker-php-ext-install pdo_mysql

# Copy API files into container
COPY api/ /var/www/html/

# Set permissions
RUN chown -R www-data:www-data /var/www/html
