FROM {version}

#RUN a2enmod rewrite

# Install libs
RUN apt-get update && apt-get install -y libzip-dev libxml2 libxml2-dev git zlib1g-dev libmcrypt-dev
RUN docker-php-ext-install mysqli pdo pdo_mysql soap mbstring zip mcrypt
RUN apt-get update \
  && apt-get install -y zlib1g-dev libicu-dev libfreetype6-dev libjpeg62-turbo-dev g++ \
  && docker-php-ext-configure intl \
  && docker-php-ext-install intl \
  && docker-php-ext-configure gd --with-freetype-dir=/usr/include/ --with-jpeg-dir=/usr/include/ \
  && docker-php-ext-install gd

# Install composer Todo : option
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composerzip

# Install xDebug Todo : option
RUN pecl install xdebug-2.5.5 && docker-php-ext-enable xdebug
RUN echo "xdebug.remote_enable=1\n" \
#    "xdebug.remote_connect_back=0\n" \
    "xdebug.remote_autostart=1\n" \
    "xdebug.remote_host={xDebug_remote_host}\n" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini
#    "xdebug.idekey=ide-data\n" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini

# Install SSL Todo : option (+ todo : let's encrypt + certbot)
RUN openssl req -x509 -nodes -days 365 -newkey rsa:2048 -keyout /etc/ssl/private/ssl-cert-snakeoil.key -out /etc/ssl/certs/ssl-cert-snakeoil.pem -subj "/C=AT/ST=Vienna/L=Vienna/O=Security/OU=Development/CN=example.com"

RUN a2enmod rewrite
RUN a2ensite default-ssl
RUN a2enmod ssl

EXPOSE 80
EXPOSE 443
