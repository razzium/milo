FROM php:7.2-apache

RUN a2enmod rewrite

RUN pecl install xdebug

# Install libs
RUN apt-get update && apt-get install -y libxml2 libxml2-dev git zlib1g-dev libmcrypt-dev #libicu-dev

RUN docker-php-ext-install mysqli pdo pdo_mysql soap mbstring zip #mcrypt #intl

RUN apt-get update \
  && apt-get install -y zlib1g-dev libicu-dev libfreetype6-dev libjpeg62-turbo-dev g++ \
  && docker-php-ext-configure intl \
  && docker-php-ext-install intl \
  && docker-php-ext-configure gd --with-freetype-dir=/usr/include/ --with-jpeg-dir=/usr/include/ \
  && docker-php-ext-install gd

RUN docker-php-ext-enable xdebug

# Copy php.ini
COPY etc/php.ini /usr/local/etc/php/

RUN apt-get update
RUN apt-get install -y \
    ca-certificates \
    curl \
    gnupg \
    lsb-release

RUN mkdir -p /etc/apt/keyrings
RUN curl -fsSL https://download.docker.com/linux/debian/gpg | gpg --dearmor -o /etc/apt/keyrings/docker.gpg
RUN echo \
  "deb [arch=$(dpkg --print-architecture) signed-by=/etc/apt/keyrings/docker.gpg] https://download.docker.com/linux/debian \
  $(lsb_release -cs) stable" | tee /etc/apt/sources.list.d/docker.list > /dev/null

RUN apt-get install sudo

RUN apt-get update
RUN apt-get install -y docker-ce docker-ce-cli containerd.io docker-compose-plugin

RUN usermod -aG docker root
RUN usermod -aG docker www-data

RUN usermod -aG sudo www-data
RUN echo "www-data ALL=(ALL) NOPASSWD: ALL" >> /etc/sudoers

# Install Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer
RUN composer --version
