# FROM php:8.2-apache

# # Install PostgreSQL dependencies
# RUN apt-get update && apt-get install -y \
#     libpq-dev \
#     libonig-dev \
#     && docker-php-ext-install pdo pdo_pgsql

# # Copy project files to the Apache web root
# COPY . /var/www/html/

# # Set proper folder permissions (optional but recommended)
# RUN chown -R www-data:www-data /var/www/html

# # Optional: point Apache DocumentRoot to /var/www/html/vetsync
# RUN sed -i 's|/var/www/html|/var/www/html/vetsync|g' /etc/apache2/sites-available/000-default.conf

# # Expose port 80
# EXPOSE 80


#-------------------------------------#

# FROM php:8.2-apache

# # Install PostgreSQL dependencies
# RUN apt-get update && apt-get install -y \
#     libpq-dev \
#     libonig-dev \
#     && docker-php-ext-install pdo pdo_pgsql

# # Copy project folder into Apache web root
# COPY vetsync /var/www/html/vetsync

# # Set proper folder permissions
# RUN chown -R www-data:www-data /var/www/html/vetsync

# # Optional: point Apache DocumentRoot to /var/www/html/vetsync
# RUN sed -i 's|/var/www/html|/var/www/html/vetsync|g' /etc/apache2/sites-available/000-default.conf

# # Expose port 80
# EXPOSE 80

#--------------------------------------------

FROM php:8.2-apache

# Install PostgreSQL dependencies
RUN apt-get update && apt-get install -y \
    libpq-dev \
    libonig-dev \
    && docker-php-ext-install pdo pdo_pgsql

# Copy project files to Apache web root
COPY . /var/www/html/

# Set proper folder permissions
RUN chown -R www-data:www-data /var/www/html

# Expose port 80
EXPOSE 80