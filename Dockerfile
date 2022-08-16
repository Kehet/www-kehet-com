FROM node:18-alpine as node

WORKDIR /site
COPY ./ /site
RUN npm ci && npm run build

FROM nginx:1.23-alpine

COPY --from=node /site/dist /usr/share/nginx/html

