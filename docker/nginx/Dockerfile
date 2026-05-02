FROM nginx:1.27-alpine

WORKDIR /var/www/html

COPY public ./public

COPY docker/nginx/nginx.conf /etc/nginx/nginx.conf
COPY docker/nginx/default.conf.template /etc/nginx/templates/default.conf.template

EXPOSE 80