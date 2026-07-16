############################################
# Base Image
############################################

# Learn more about the Server Side Up PHP Docker Images at:
# https://serversideup.net/open-source/docker-php/
FROM serversideup/php:8.5-fpm-nginx-alpine AS base

## Uncomment if you need to install additional PHP extensions
USER root

############################################
# CI image
############################################
FROM base AS ci

# Sometimes CI images need to run as root
USER root

############################################
# Production Image
############################################
FROM base AS prod
COPY --chown=www-data:www-data . /var/www/html

USER www-data
