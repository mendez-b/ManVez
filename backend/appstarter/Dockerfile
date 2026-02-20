FROM php:8.2-apache

RUN apt-get update && apt-get install -y \
    libicu-dev \
    libzip-dev \
    unzip \
    && docker-php-ext-install intl zip \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/* \
    && curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer \
    && a2enmod rewrite

COPY apache.conf /etc/apache2/sites-enabled/000-default.conf

WORKDIR /var/www/html

COPY . .

RUN composer install --no-dev --optimize-autoloader \
    && chown -R www-data:www-data /var/www/html

EXPOSE 80

CMD ["apache2-foreground"]