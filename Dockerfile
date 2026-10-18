FROM php:8.2-apache

# Install the MySQL driver
RUN docker-php-ext-install pdo pdo_mysql

# Copy your app files into the web root
COPY . /var/www/html/

WORKDIR /var/www/html

# Fix for "More than one MPM loaded" on Railway
# This forces only mpm_prefork to be active right before Apache starts.
CMD ["bash", "-c", "a2dismod mpm_event mpm_worker 2>/dev/null || true; a2enmod mpm_prefork 2>/dev/null || true; exec apache2-foreground"]
