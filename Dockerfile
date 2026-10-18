FROM php:8.2-apache

# Install the MySQL driver
RUN docker-php-ext-install pdo pdo_mysql

# Copy your app files into the web root
COPY . /var/www/html/

WORKDIR /var/www/html
