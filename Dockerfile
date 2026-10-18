FROM php:8.2-apache

# Fix Apache MPM conflict: disable event and worker, enable prefork
RUN a2dismod mpm_event mpm_worker 2>/dev/null; a2enmod mpm_prefork

# Install the MySQL driver
RUN docker-php-ext-install pdo pdo_mysql

# Copy your app files into the web root
COPY . /var/www/html/

WORKDIR /var/www/html
