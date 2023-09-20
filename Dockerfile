# Utilisez une image PHP 8 officielle
FROM php:8.1-fpm

# Installez les dépendances nécessaires
RUN apt-get update
RUN apt-get install -y unzip libicu-dev libzip-dev
RUN apt-get install -y libsqlite3-dev
RUN docker-php-ext-install intl pdo pdo_sqlite zip

# Installez Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Définissez le répertoire de travail
WORKDIR /var/www/html

# Exposez le port 9000 pour PHP-FPM
EXPOSE 9000