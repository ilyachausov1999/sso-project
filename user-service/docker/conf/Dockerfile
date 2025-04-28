FROM php:8.3-fpm

# Install system dependencies
RUN apt-get update \
    && apt-get install -y \
        git \
        curl \
        dpkg-dev \
        libonig-dev \
        libxml2-dev \
        libpq-dev \
        libzip-dev \
        zip \
        unzip \
        libmagickwand-dev \
        libssh2-1-dev \
        cron

RUN docker-php-ext-configure gd \
  --enable-gd \
  --with-jpeg

# Clear cache
RUN apt-get clean && rm -rf /var/lib/apt/lists/*

# Install PHP extensions
RUN docker-php-ext-install pdo pdo_mysql pdo_pgsql pgsql mbstring exif pcntl bcmath gd sockets zip

# Get Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Set correct permissions for storage and cache directories
RUN #groupadd $USER -g $GID && useradd -G www-data,root -u $UID -g $GID -d /home/$USER $USER && \
#    mkdir -p /home/$USER/.composer && \
#    chown -R $USER:$USER /home/$USER && \
#    chmod -R g+wx /var

ADD ./docker/conf/php/php.ini /usr/local/etc/php/php.ini

# Set working directory
WORKDIR /var/www/html

# Expose port 9000 and start php-fpm server
EXPOSE 9000
CMD ["php-fpm"]
