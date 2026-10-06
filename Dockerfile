FROM php:8.2-apache

RUN docker-php-ext-install pdo pdo_mysql

COPY docker/clean-urls.conf /etc/apache2/conf-available/clean-urls.conf
RUN a2enmod rewrite && a2enconf clean-urls
