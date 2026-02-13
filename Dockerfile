# استخدم PHP 8.4 مع Apache
FROM php:8.4-apache

# تثبيت الأدوات الأساسية وملحقات PHP
RUN apt-get update && apt-get install -y \
    git \
    unzip \
    zip \
    curl \
    nano \
    && docker-php-ext-install pdo pdo_mysql

# تثبيت Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# --- الكود الجديد الذي سألته عنه يبدأ من هنا ---
# ضبط المتغير للمسار
ENV APACHE_DOCUMENT_ROOT /var/www/html/public

# استبدال المسارات في ملفات إعدادات Apache بالكامل
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf
RUN sed -ri -e 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf

# تفعيل مود الـ Rewrite (ضروري جداً لراوتات لارافيل)
RUN a2enmod rewrite
# --- نهاية الكود الجديد ---

# مجلد العمل
WORKDIR /var/www/html

# إعطاء الصلاحيات اللازمة (اختياري لكنه يحل مشاكل الـ Permission)
RUN chown -R www-data:www-data /var/www/html