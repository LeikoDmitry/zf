FROM php:7.3.5-apache

RUN apt-get update \
 && apt-get install -y git zlib1g-dev libicu-dev g++ libzip-dev \
 && docker-php-ext-configure intl \
 && docker-php-ext-install zip pdo pdo_mysql intl \
 && docker-php-ext-install zip \
 && a2enmod rewrite \
 && curl -sS https://getcomposer.org/installer \
  | php -- --install-dir=/usr/local/bin --filename=composer

RUN pecl install xdebug-2.7.2 && docker-php-ext-enable xdebug

RUN echo "ServerName localhost" >> /etc/apache2/apache2.conf
COPY site.conf /etc/apache2/sites-available/site.conf
RUN a2ensite site.conf

WORKDIR /var/www/zf
