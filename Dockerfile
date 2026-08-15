############################################
# Base Image
############################################

# Non-root nginx image, fronted by Traefik (which terminates TLS).
# https://github.com/nginx/docker-nginx-unprivileged
FROM nginxinc/nginx-unprivileged:1.31-alpine@sha256:334d92979f15aaecd5dd50af5105e1230e2bb70765d45b1e2f964e7c5eda81c3 AS base

############################################
# Production Image
############################################
FROM base AS prod

COPY --chown=nginx:nginx dist /usr/share/nginx/html
COPY --chown=nginx:nginx nginx.conf /etc/nginx/conf.d/default.conf
