FROM php:8.1-fpm-alpine

RUN apk add --no-cache \
    libpng-dev \
    libjpeg-turbo-dev \
    freetype-dev \
    mysql-client \
    build-base \
    && docker-php-ext-install pdo pdo_mysql \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install gd



WORKDIR /var/www/html/

COPY . /var/www/html/

EXPOSE 80

VOLUME ["/var/www/html"]

CMD ["php", "-S", "0.0.0.0:80"]
