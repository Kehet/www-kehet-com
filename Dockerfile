############################################
# Base Image
############################################

# Non-root nginx image, fronted by Traefik (which terminates TLS).
# https://github.com/nginx/docker-nginx-unprivileged
FROM nginxinc/nginx-unprivileged:1.31-alpine@sha256:b5047b9b76845eac124358373c6852377c52e4b05834a47647e955dd91cb8f1e AS base

############################################
# Production Image
############################################
FROM base AS prod

COPY --chown=nginx:nginx dist /usr/share/nginx/html
COPY --chown=nginx:nginx nginx.conf /etc/nginx/conf.d/default.conf
