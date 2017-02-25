FROM nginx:1.11-alpine
MAINTAINER Jonathan Putney <jonathan@putney.io>

RUN mkdir -p /var/www/html
WORKDIR /var/www/html

COPY config/nginx.conf /etc/nginx/conf.d/default.conf

COPY src/ ./
