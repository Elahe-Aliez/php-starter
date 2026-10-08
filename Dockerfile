# The image of the app: PHP 8.4 with the MySQL extension and Composer.

FROM php:8.4-cli

# unzip: Composer needs it to unpack packages.
RUN apt-get update && apt-get install -y unzip

# pdo_mysql: lets PHP talk to MySQL.
RUN docker-php-ext-install pdo_mysql

# Composer, copied from its official image.
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer
ENV COMPOSER_ALLOW_SUPERUSER=1

WORKDIR /app
COPY . .

# The web server that is built into PHP. It serves the folder "public" on port 8000.
EXPOSE 8000
STOPSIGNAL SIGINT
CMD ["php", "-S", "0.0.0.0:8000", "-t", "public"]
