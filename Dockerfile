# build stage
FROM php:7.4-fpm-alpine

RUN apk update
RUN apk add nginx git openrc

RUN docker-php-ext-install opcache
RUN { \
  echo 'opcache.memory_consumption=64'; \
  echo 'opcache.interned_strings_buffer=8'; \
  echo 'opcache.max_accelerated_files=10000'; \
  echo 'opcache.revalidate_freq=600'; \
  echo 'opcache.enable=1'; \
} > /usr/local/etc/php/conf.d/php-opcache-cfg.ini

COPY ./docker/site.conf /etc/nginx/conf.d/default.conf
COPY ./docker/entrypoint.sh /etc/entrypoint.sh
RUN chmod +x /etc/entrypoint.sh
RUN mkdir -p /run/nginx

COPY --chown=www-data:www-data . /www/top-secret
WORKDIR /www/top-secret

EXPOSE 80

ENTRYPOINT ["/etc/entrypoint.sh"]
