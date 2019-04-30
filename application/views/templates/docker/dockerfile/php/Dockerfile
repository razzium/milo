FROM {version}

# Install libs
RUN apt-get update && apt-get install -y libzip-dev libxml2 libxml2-dev git zlib1g-dev
RUN docker-php-ext-install mysqli pdo pdo_mysql soap mbstring zip
RUN apt-get update \
  && apt-get install -y zlib1g-dev libicu-dev libfreetype6-dev libjpeg62-turbo-dev g++ \
  && docker-php-ext-configure intl \
  && docker-php-ext-install intl \
  && docker-php-ext-configure gd --with-freetype-dir=/usr/include/ --with-jpeg-dir=/usr/include/ \
  && docker-php-ext-install gd

# Install composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composerzip