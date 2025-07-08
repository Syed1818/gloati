FROM php:8.1-apache

# Install mysqli and other necessary extensions
RUN docker-php-ext-install mysqli pdo pdo_mysql

# Enable Apache mod_rewrite (optional)
RUN a2enmod rewrite

# Copy your app
COPY . /var/www/html/

# Permissions (optional)
RUN chown -R www-data:www-data /var/www/html

EXPOSE 80
CMD ["apache2-foreground"]
