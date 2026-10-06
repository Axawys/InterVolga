FROM php:8.2-apache

RUN docker-php-ext-install pdo pdo_mysql

COPY docker/php.ini /usr/local/etc/php/conf.d/app.ini

# Apache работает от www-data; uid/gid как у владельца проекта на хосте,
# чтобы загруженные фото можно было записать в примонтированный catalogue_images
ARG UID=1000
ARG GID=1000
RUN groupmod -o -g ${GID} www-data && usermod -o -u ${UID} -g ${GID} www-data

COPY docker/clean-urls.conf /etc/apache2/conf-available/clean-urls.conf
RUN a2enmod rewrite && a2enconf clean-urls
