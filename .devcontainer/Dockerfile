FROM mcr.microsoft.com/devcontainers/php:1-8.2

# Install MySQL server and required PHP extensions
RUN apt-get update && \
    apt-get install -y \
        unzip \
        libzip-dev \
        libpng-dev \
        libonig-dev \
        libxml2-dev \
        libicu-dev \
        libxslt1-dev \
        zlib1g-dev \
        git \
        curl \
        gnupg \
        mysql-server \
        default-mysql-client \
        && docker-php-ext-install pdo pdo_mysql zip intl gd

# Start MySQL service and drop into bash
CMD service mysql start && bash
