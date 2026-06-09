FROM php:8.4-apache

# 1. تثبيت الأدوات الأساسية وملحقات PHP
RUN apt-get update && apt-get install -y \
    git \
    unzip \
    zip \
    curl \
    nano \
    && docker-php-ext-install pdo pdo_mysql

# 2. تثبيت Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# 3. ضبط المتغير للمسار وتعديل إعدادات Apache
ENV APACHE_DOCUMENT_ROOT /var/www/html/public
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf
RUN sed -ri -e 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf

# 4. تفعيل مود الـ Rewrite الخاص بلارافيل
RUN a2enmod rewrite

# 5. تحديد مجلد العمل
WORKDIR /var/www/html

# ==========================================
# التعديلات الحاسمة للرفع تبدأ من هنا:
# ==========================================

# 6. نسخ جميع ملفات المشروع من جهازك إلى السيرفر
COPY . .

# 7. تثبيت مكتبات لارافيل (بدون مكتبات التطوير لتخفيف المساحة)
RUN composer install --no-dev --optimize-autoloader

# 8. إعطاء الصلاحيات للسيرفر للتحكم بالملفات (يجب أن تكون هذه الخطوة بعد نسخ الملفات)
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 775 /var/www/html/storage \
    && chmod -R 775 /var/www/html/bootstrap/cache

# 9. كشف المنفذ 80 الذي يستخدمه Apache
EXPOSE 80