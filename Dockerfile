# Use official PHP image with Apache
FROM php:8.1-apache

# Enable Apache mod_rewrite if needed (for frameworks like Laravel)
RUN a2enmod rewrite

# Copy all your project files to Apache root
COPY . /var/www/html/

# Set the correct permissions (optional, adjust if needed)
RUN chown -R www-data:www-data /var/www/html && chmod -R 755 /var/www/html

# Expose port 80 to the outside world
EXPOSE 80

# Start Apache
CMD ["apache2-foreground"]
