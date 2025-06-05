FROM php:8.2-apache

# Enable required PHP extensions
RUN docker-php-ext-install pdo pdo_mysql

# Enable Apache mod_rewrite
RUN a2enmod rewrite

# Set working directory
WORKDIR /var/www/html

# Copy everything to container
COPY . .

# Install Composer dependencies
RUN curl -sS https://getcomposer.org/installer | php && \
    php composer.phar install

# Copy your custom vhost config (optional)
# COPY vhost.conf /etc/apache2/sites-available/000-default.conf

EXPOSE 80
