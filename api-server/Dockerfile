## COMMON (pour déveloper, on utilise ce stage)
# FROM php:7.3.22-cli-alpine3.12
FROM php:7.3-cli-alpine as common-stage

# Installation de Composer en suivant https://getcomposer.org/doc/faqs/how-to-install-composer-programmatically.md
RUN wget https://raw.githubusercontent.com/composer/getcomposer.org/76a7060ccb93902cd7576b67264ad91c8a2700e2/web/installer -O - -q | php -- --version=1.10.10 --install-dir=/usr/local/bin --filename=composer

# Installation de Symfony en suivant https://symfony.com/download
RUN apk --no-cache add bash git && wget https://get.symfony.com/cli/installer -O - | bash
RUN mv $HOME/.symfony/bin/symfony /usr/local/bin/symfony

# Configurations PHP pour l'application
RUN apk --no-cache add mysql-dev \
 && docker-php-ext-install pdo_mysql
RUN sed -i 's/;extension=sodium/extension=sodium/' /usr/local/etc/php/php.ini-production /usr/local/etc/php/php.ini-development \
 && sed -i 's/;extension=intl/extension=intl/' /usr/local/etc/php/php.ini-production /usr/local/etc/php/php.ini-development \
 && sed -i 's/memory_limit = 128M/memory_limit = 512M/' /usr/local/etc/php/php.ini-production /usr/local/etc/php/php.ini-development \
 && sed -i 's/post_max_size = 8M/post_max_size = 130M/' /usr/local/etc/php/php.ini-production /usr/local/etc/php/php.ini-development \
 && sed -i 's/upload_max_filesize = 2M/upload_max_filesize = 128M/' /usr/local/etc/php/php.ini-production /usr/local/etc/php/php.ini-development

WORKDIR /api-server

## BUILD
FROM common-stage as build-stage

ENV APP_ENV=prod \
    APP_DEBUG=0

# Installation des sources pour la prod en suivant la doc https://symfony.com/doc/current/deployment.html
COPY composer.json composer.lock symfony.lock /api-server/
RUN composer install --no-dev --no-scripts

COPY . /api-server
RUN composer install --no-dev --optimize-autoloader
RUN php bin/console cache:clear


## RUN
# FROM php:7.3.22-apache-buster
FROM php:7.3-apache as run-stage

RUN apt update && apt full-upgrade -y \
 && apt install -y libpq-dev \
 && docker-php-ext-install pdo_pgsql \
 && sed -i 's|/var/www/html|/var/www/html/public|' /etc/apache2/sites-available/000-default.conf \
 && a2enmod rewrite

ENV APP_ENV=prod \
    APP_DEBUG=0

COPY --from=common-stage /usr/local/etc/php/php.ini-production /usr/local/etc/php/php.ini-production
COPY --from=common-stage /usr/local/etc/php/php.ini-development /usr/local/etc/php/php.ini-development
COPY --from=build-stage /api-server/ /var/www/html/

RUN chown -R www-data /var/www/html/var
