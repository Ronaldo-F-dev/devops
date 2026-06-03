FROM php:8.4-cli-bookworm

# Installation des dépendances et extensions requises
RUN apt-get update \
    && apt-get install -y --no-install-recommends \
        git \
        libicu-dev \
        libzip-dev \
        unzip \
    && docker-php-ext-install intl opcache pdo_mysql zip \
    && rm -rf /var/lib/apt/lists/*

# Déclaration globale pour éviter l'erreur "dubious ownership" de Git
RUN git config --global --add safe.directory /app

# Récupération de Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /app

EXPOSE 8000