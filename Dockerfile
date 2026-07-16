############################################
# Base Image
############################################

# Non-root nginx image, fronted by Traefik (which terminates TLS).
# https://github.com/nginx/docker-nginx-unprivileged
FROM nginxinc/nginx-unprivileged:1.30-alpine@sha256:dcea25a6593307a74b09e59a47f8695c4d56943750e45add532ae0bf8b24bfd6 AS base

############################################
# Production Image
############################################
FROM base AS prod

COPY --chown=nginx:nginx dist /usr/share/nginx/html
COPY --chown=nginx:nginx nginx.conf /etc/nginx/conf.d/default.conf
