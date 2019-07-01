FROM php:7.2-apache

RUN a2enmod rewrite

# Install libs
RUN apt-get update && apt-get install -y libxml2 libxml2-dev git zlib1g-dev libmcrypt-dev #libicu-dev

RUN docker-php-ext-install mysqli pdo pdo_mysql soap mbstring zip #mcrypt #intl

# Install Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer
RUN composer --version

RUN apt-get update \
  && apt-get install -y zlib1g-dev libicu-dev libfreetype6-dev libjpeg62-turbo-dev g++ \
  && docker-php-ext-configure intl \
  && docker-php-ext-install intl \
  && docker-php-ext-configure gd --with-freetype-dir=/usr/include/ --with-jpeg-dir=/usr/include/ \
  && docker-php-ext-install gd

# Copy php.ini
COPY etc/php.ini /usr/local/etc/php/

# Docker
RUN apt-get update \
    && apt-get install -y \
     apt-transport-https \
     ca-certificates \
     curl \
     gnupg2 \
     software-properties-common \
    && curl -fsSL https://download.docker.com/linux/$(. /etc/os-release; echo "$ID")/gpg \
     | apt-key add - \
    && add-apt-repository \
     "deb [arch=amd64] https://download.docker.com/linux/$(. /etc/os-release; echo "$ID") \
     $(lsb_release -cs) \
     stable" \
    && apt-get update \
    && apt-get install -y docker-ce \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

RUN usermod -aG docker root
RUN usermod -aG docker www-data

RUN usermod -aG sudo www-data
RUN echo "www-data ALL=(ALL) NOPASSWD: ALL" >> /etc/sudoers