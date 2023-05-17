FROM php:7.4-alpine

# Install system dependencies
RUN apk --no-cache add curl && rm -rf /var/lib/apt/lists/*

# Install PHP extensions
RUN docker-php-ext-install -j$(nproc) \
    bcmath \
    calendar \
    sockets \
    && rm -rf /var/lib/apt/lists/*

# Install Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer \
    && composer self-update --2

# Add /usr/local/bin to the PATH environment variable
ENV PATH="${PATH}:/usr/local/bin"
