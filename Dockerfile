############################################
# Base Image
############################################

# Non-root nginx image, fronted by Traefik (which terminates TLS).
# https://github.com/nginx/docker-nginx-unprivileged
FROM nginxinc/nginx-unprivileged:1.31-alpine@sha256:a718212f9cf21e241f14067333000a3f0930292f5354fe0db269e9a2a2596b9e AS base

############################################
# Production Image
############################################
FROM base AS prod

COPY --chown=nginx:nginx dist /usr/share/nginx/html
COPY --chown=nginx:nginx nginx.conf /etc/nginx/conf.d/default.conf
