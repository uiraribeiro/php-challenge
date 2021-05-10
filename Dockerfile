FROM alpine:3.7

LABEL description="php-challenge"

MAINTAINER BIT <bit@syseleven.de>

RUN apk update && apk add \
    php7 \
    php7-curl \
    php7-gd \
    php7-mcrypt \
    php7-xmlrpc \
    php7-xsl \
    php7-intl \
    php7-ldap \
    php7-gmp \
    php7-redis \
    php7-json \
    php7-phar \
    php7-iconv \
    php7-zlib \
    php7-pdo \
    php7-pdo_mysql \
    php7-pdo_pgsql \
    php7-pdo_sqlite \
    php7-tokenizer \
    php7-mbstring \
    php7-ctype \
    php7-simplexml \
    php7-dom \
    php7-posix \
    php7-opcache \
    php7-xdebug \
    php7-bcmath \
    git \
    grep \
    bash \
    socat \
    bzip2 \
    mysql-client

# enable php errors
RUN sed -i s/display_errors\ =\ Off/display_errors\ =\ On/g /etc/php7/php.ini

# raise memory limit
RUN sed -i s/memory_limit\ =\ 128M/memory_limit\ =\ -1/g /etc/php7/php.ini

# install composer
RUN php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');"
RUN php composer-setup.php --version=1.10.16
RUN php -r "unlink('composer-setup.php');"
RUN mv composer.phar /usr/bin/composer
RUN chmod +x /usr/bin/composer

WORKDIR /var/www/php-challenge

COPY composer.json composer.lock ./
COPY app ./app
COPY src ./src
COPY web ./web

RUN composer install || true
RUN php app/console assets:install --symlink && \
    php app/console assetic:dump


COPY entrypoint.sh /entrypoint.sh
RUN chmod +x /entrypoint.sh
ENTRYPOINT ["/entrypoint.sh"]

EXPOSE 9080
