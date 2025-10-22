FROM php:8.3.0-fpm

# Copy composer.lock and composer.json
RUN if [-f composer.lock] && [-f composer.json] ; then \
    COPY composer.lock composer.json /var/www/ ; \ 
    fi

# Set working directory
WORKDIR /var/www

# Install dependencies
RUN apt-get update && apt-get install -y \
    build-essential \
    libpng-dev \
    libzip-dev \
    libpq-dev \
    libsqlite3-dev \
    libjpeg62-turbo-dev \
    libfreetype6-dev \
    locales \
    zip \
    jpegoptim optipng pngquant gifsicle \
    vim \
    unzip \
    git \
    curl

# Clear cache
RUN apt-get clean && rm -rf /var/lib/apt/lists/*

# Install extensions
RUN pecl install xdebug && docker-php-ext-enable xdebug 
RUN docker-php-ext-install zip exif pcntl pdo_pgsql pgsql pdo_sqlite
RUN docker-php-ext-install gd bcmath

# Install composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

#Installing node 18.x
RUN curl -sL https://deb.nodesource.com/setup_18.x| bash -
RUN apt-get install -y nodejs

# Add user for  application
RUN groupadd -g 1000 www
RUN useradd -u 1000 -ms /bin/bash -g www www

# Copy existing application directory contents
COPY . /var/www

# Copy existing application directory permissions
COPY --chown=www:www . /var/www



# Change current user to www
USER www

# Expose port 9000 and start php-fpm server
EXPOSE 9000
CMD ["php-fpm"]