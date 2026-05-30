FROM richarvey/nginx-php-fpm:2.2.0

# Install PostgreSQL extension (supaya support DB PostgreSQL)
RUN apk add --no-cache postgresql-dev \
    && docker-php-ext-install pdo_pgsql

# Copy semua file Laravel ke container
COPY . /var/www/html

# Konfigurasi khusus Render
ENV SKIP_COMPOSER 1
ENV WEBROOT /var/www/html/public
ENV PHP_ERRORS_STDERR 1
ENV RUN_SCRIPTS 1
ENV REAL_IP_HEADER 1

ENV APP_ENV=production
ENV APP_DEBUG=false
ENV LOG_CHANNEL=stderr

ENV COMPOSER_ALLOW_SUPERUSER 1

WORKDIR /var/www/html

# Install dependencies Laravel
RUN composer install --no-dev --optimize-autoloader --no-interaction --prefer-dist

# Permission folder penting
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache

# Laravel optimization + storage link
RUN php artisan config:cache \
    && php artisan route:cache \
    && php artisan view:cache \
    && php artisan storage:link

EXPOSE 80

CMD ["/start.sh"]
