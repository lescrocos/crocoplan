## COMMON (pour déveloper, on utilise ce stage)
FROM php:7.3-cli-buster as common-stage

# Installation de Composer en suivant https://getcomposer.org/doc/faqs/how-to-install-composer-programmatically.md
RUN apt update && apt install -y git wget \
 && wget https://raw.githubusercontent.com/composer/getcomposer.org/76a7060ccb93902cd7576b67264ad91c8a2700e2/web/installer -O - -q | php -- --version=1.10.20 --install-dir=/usr/local/bin --filename=composer \
# Installation de Symfony en suivant https://symfony.com/download
 && wget https://get.symfony.com/cli/installer -O - | bash \
 && mv $HOME/.symfony/bin/symfony /usr/local/bin/symfony

# Configurations PHP pour l'application
RUN apt update && apt install -y libmariadb-dev \
 && docker-php-ext-install pdo_mysql
RUN sed -i 's/;extension=sodium/extension=sodium/' /usr/local/etc/php/php.ini-production /usr/local/etc/php/php.ini-development \
 && sed -i 's/;extension=intl/extension=intl/' /usr/local/etc/php/php.ini-production /usr/local/etc/php/php.ini-development \
 && sed -i -E 's/^;?memory_limit.*/memory_limit = 512M/' /usr/local/etc/php/php.ini-production /usr/local/etc/php/php.ini-development \
 && sed -i 's/post_max_size = 8M/post_max_size = 130M/' /usr/local/etc/php/php.ini-production /usr/local/etc/php/php.ini-development \
 && sed -i 's/upload_max_filesize = 2M/upload_max_filesize = 128M/' /usr/local/etc/php/php.ini-production /usr/local/etc/php/php.ini-development

WORKDIR /api-server

## DEV
FROM common-stage as dev-stage

ARG XDEBUG_VERSION=3.0.2
RUN set -eux; \
  apt update; apt install -y netcat; \
  pecl install xdebug-$XDEBUG_VERSION; \
  docker-php-ext-enable xdebug;
RUN \
  echo "error_reporting = E_ALL" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini; \
  echo "display_startup_errors = On" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini; \
  echo "display_errors = On" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini;

## BUILD
FROM common-stage as build-stage

ENV APP_ENV=prod \
    APP_DEBUG=0

# Installation des sources pour la prod en suivant la doc https://symfony.com/doc/current/deployment.html
COPY composer.json composer.lock symfony.lock /api-server/
RUN composer install --prefer-dist --no-dev --no-scripts

COPY . /api-server
RUN composer install --prefer-dist --no-dev --optimize-autoloader
RUN php bin/console cache:clear


## RUN
# FROM php:7.3.22-apache-buster
FROM php:7.3-apache as run-stage

RUN apt update && apt full-upgrade -y \
 && apt install -y libmariadb-dev \
 && docker-php-ext-install pdo_mysql \
 && sed -i 's|/var/www/html|/var/www/html/public|' /etc/apache2/sites-available/000-default.conf \
 && a2enmod rewrite

ENV APP_ENV=prod \
    APP_DEBUG=0

COPY --from=common-stage /usr/local/etc/php/php.ini-production /usr/local/etc/php/php.ini-production
COPY --from=common-stage /usr/local/etc/php/php.ini-development /usr/local/etc/php/php.ini-development
COPY --from=build-stage /api-server/ /var/www/html/

RUN chown -R www-data /var/www/html/var
