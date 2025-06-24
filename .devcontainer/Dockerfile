FROM mcr.microsoft.com/devcontainers/php:1-8.2

# Install MariaDB and PHP extensions needed by Laravel/Bagisto
RUN apt-get update && \
    apt-get install -y \
        unzip \
        libzip-dev libpng-dev libonig-dev libxml2-dev libicu-dev libxslt1-dev \
        zlib1g-dev git curl gnupg \
        mariadb-server default-mysql-client \
    && docker-php-ext-install pdo pdo_mysql zip intl gd
# MariaDB is available in Debian 12's default repos :contentReference[oaicite:1]{index=1}

# Start MariaDB service by default
CMD service mariadb start && bash
