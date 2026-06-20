FROM richarvey/nginx-php-fpm:2.2.0

# Copy semua file Laravel
COPY . /var/www/html

# Install dependencies
RUN composer install --no-dev --optimize-autoloader

# Set permission
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache

# Storage link (penting buat foto)
RUN php artisan storage:link

# Cache Laravel
RUN php artisan config:cache
RUN php artisan route:cache
RUN php artisan view:cache

# Expose port Render
EXPOSE 80

# Start nginx + php-fpm
CMD ["/start.sh"]