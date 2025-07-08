FROM php:8.1-apache

# Install system dependencies
RUN apt-get update && apt-get install -y libpq-dev

# Install PHP extensions: mysqli, pdo_mysql (MySQL), pdo_pgsql (PostgreSQL)
RUN docker-php-ext-install mysqli pdo pdo_mysql pgsql pdo_pgsql

# Enable Apache mod_rewrite
RUN a2enmod rewrite

# Copy your app files to Apache root
COPY . /var/www/html/

# Set permissions (optional)
RUN chown -R www-data:www-data /var/www/html

EXPOSE 80
CMD ["apache2-foreground"]
