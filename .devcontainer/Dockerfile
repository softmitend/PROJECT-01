FROM mcr.microsoft.com/devcontainers/php:8.4-bookworm

RUN apt-get update \
    && apt-get install -y --no-install-recommends default-mysql-client \
    && if ! php -m | grep -qi '^pdo_mysql$'; then docker-php-ext-install pdo_mysql; fi \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*