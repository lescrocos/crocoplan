## COMMON (pour déveloper, on utilise ce stage)
# FROM node:12.18.4-alpine3.11
FROM node:12-alpine as common-stage

# Installation de Quasar CLI en suivant https://quasar.dev/quasar-cli/installation
RUN yarn global add @quasar/cli

WORKDIR /client-app


## BUILD
FROM common-stage as build-stage

COPY package.json yarn.lock /client-app/
RUN yarn install

COPY . /client-app
RUN quasar build


## RUN
# FROM nginx:1.18.0-alpine, stable-alpine, 1.18-alpine
FROM nginx:1.18-alpine as run-stage

#COPY docker/nginx.conf /etc/nginx/conf.d/default.conf

COPY --from=build-stage /client-app/dist/spa/ /usr/share/nginx/html/
