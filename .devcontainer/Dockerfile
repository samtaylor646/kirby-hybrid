FROM mcr.microsoft.com/devcontainers/php:1-8.4-bullseye

# Fix for expired Yarn GPG keys in Bullseye images
RUN rm -f /etc/apt/sources.list.d/yarn.list

# Suppress Xdebug "could not connect" noise in terminal
RUN echo "xdebug.mode=off" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini

RUN apt-get update && apt-get install -y \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    && rm -rf /var/lib/apt/lists/*

RUN docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) gd

RUN curl -fsSL https://deb.nodesource.com/setup_24.x | bash - \
    && apt-get install -y nodejs \
    && rm -rf /var/lib/apt/lists/*
