FROM node:21-alpine as build

WORKDIR /app
COPY ../ /app

RUN npm install && npm run build

FROM nginx:stable-alpine as serve

LABEL maintainer=""

COPY --from=build /app/dist /usr/share/nginx/html

EXPOSE 80

CMD ["nginx", "-g", "daemon off;"]
