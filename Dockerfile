FROM php:8.1-apache

# Install PostgreSQL and MySQL PDO drivers
RUN apt-get update && apt-get install -y libpq-dev \
    && docker-php-ext-install pdo pdo_pgsql pgsql mysqli

# Enable Apache rewrite module
RUN a2enmod rewrite

# Copy your PHP app into Apache root
COPY . /var/www/html/

# Set correct permissions (optional)
RUN chown -R www-data:www-data /var/www/html

EXPOSE 80
CMD ["apache2-foreground"]
