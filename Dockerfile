FROM php:8.2-apache

# Copy all project files into the web root
COPY . /var/www/html/

# Optional: enable Apache rewrite (useful later)
RUN a2enmod rewrite
