# Use the official PHP image with Apache
FROM php:8.2-apache

# Enable Apache mod_rewrite (needed for many routing setups)
RUN a2enmod rewrite

# Copy project files to the Apache document root
COPY public/ /var/www/html/

# Optionally install required PHP extensions
# RUN docker-php-ext-install mysqli pdo pdo_mysql

# Set working directory
WORKDIR /var/www/html

# Set proper permissions
RUN chown -R www-data:www-data /var/www/html

# Expose the default web server port
EXPOSE 80
