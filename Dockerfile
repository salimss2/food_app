# استخدم PHP 8.4 مع Apache
FROM php:8.4-apache

# تثبيت الأدوات الأساسية
RUN apt-get update && apt-get install -y \
    git \
    unzip \
    zip \
    curl \
    nano \
    && docker-php-ext-install pdo pdo_mysql

# تثبيت Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# تفعيل mod_rewrite
RUN a2enmod rewrite

# ضبط DocumentRoot على public
RUN sed -i 's|DocumentRoot /var/www/html|DocumentRoot /var/www/html/public|' /etc/apache2/sites-available/000-default.conf
RUN sed -i 's|<Directory /var/www/html>|<Directory /var/www/html/public>\n    AllowOverride All\n    Require all granted|' /etc/apache2/sites-available/000-default.conf

# مجلد العمل
WORKDIR /var/www/html
