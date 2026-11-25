FROM php:8.3-apache

RUN a2enmod rewrite

# Allow .htaccess overrides (Laravel front-controller routing lives in the bundle root)
RUN echo '<Directory /var/www/html>\n    AllowOverride All\n    Require all granted\n</Directory>' > /etc/apache2/conf-available/app.conf \
    && a2enconf app

COPY server/ /var/www/html/

# Everything must be readable by the web user (incl. .htaccess)
RUN chmod -R a+rX /var/www/html

# SQLite database file + Laravel writable dirs must belong to the web user
RUN chown -R www-data:www-data /var/www/html/app-core/storage /var/www/html/app-core/bootstrap/cache /var/www/html/app-core/database

COPY docker-entrypoint.sh /usr/local/bin/docker-entrypoint.sh
RUN chmod +x /usr/local/bin/docker-entrypoint.sh

ENTRYPOINT ["docker-entrypoint.sh"]
CMD ["apache2-foreground"]
