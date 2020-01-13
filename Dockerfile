# build stage
FROM alpine:3.10

ENV UID=1000
ENV GID=1000

RUN addgroup -S www-data -g ${GID} \
    && adduser -D -S -G www-data -u ${UID} www-data -h /data


RUN apk add --no-cache nginx git openrc zip openssh
RUN apk add --no-cache \
  php7-cli php7-tidy php7-apcu php7-bcmath php7-dom php7-ctype php7-curl php7-fileinfo php7-fpm php7-gd php7-iconv php7-intl php7-json php7-mbstring \
  php7-opcache php7-openssl php7-pdo php7-pdo_sqlite php7-phar php7-posix php7-simplexml php7-session php7-soap php7-tokenizer php7-zip php7-imagick

RUN sed -i 's/user .*;/user www-data;/g' /etc/nginx/nginx.conf && \
  sed -i 's/user = .*/user www-data/g' /etc/php7/php-fpm.d/www.conf && \
  sed -i 's/group = .*/group www-data/g' /etc/php7/php-fpm.d/www.conf && \
  sed -i 's/upload_max_filesize = .*/upload_max_filesize = 50M/g' /etc/php7/php.ini && \
  sed -i 's/post_max_size = .*/post_max_size = 50M/g' /etc/php7/php.ini

COPY ./docker/site.conf /etc/nginx/conf.d/default.conf
COPY ./docker/entrypoint.sh /etc/entrypoint.sh

RUN chmod +x /etc/entrypoint.sh
RUN mkdir -p /run/nginx

COPY --chown=www-data:www-data . /www/top-secret
WORKDIR /www/top-secret

EXPOSE 80

ENTRYPOINT ["/etc/entrypoint.sh"]
