FROM php:8.2-apache

RUN apt-get update && apt-get install -y \
    libicu-dev \
    libzip-dev \
    libpq-dev \
    unzip \
    && docker-php-ext-install intl zip pgsql pdo_pgsql \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/* \
    && curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer \
    && a2enmod rewrite

RUN sed -i 's/Listen 80/Listen 10000/' /etc/apache2/ports.conf
RUN a2dissite 000-default.conf || true
COPY apache.conf /etc/apache2/sites-available/000-default.conf
RUN a2ensite 000-default.conf

WORKDIR /var/www/html

COPY . .

RUN composer install --no-dev --optimize-autoloader

RUN mkdir -p writable/cache writable/logs writable/session writable/uploads \
    && chmod -R 777 writable/

EXPOSE 10000

ENV PORT=10000

CMD ["apache2-foreground"]