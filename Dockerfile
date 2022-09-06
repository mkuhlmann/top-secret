# build stage
FROM alpine:3.15

RUN apk add --no-cache nginx git openrc zip openssh curl

RUN apk add --no-cache \
  php8-cli php8-ctype php8-curl php8-fileinfo php8-fpm php8-gd php8-json php8-mbstring \
  php8-opcache php8-openssl php8-pdo php8-pdo_sqlite php8-phar php8-posix php8-simplexml php8-session php8-tokenizer php8-zip php8-pecl-imagick

RUN ln -s php8 /usr/bin/php
# install composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# make sure php runs with same uid as nginx
RUN sed -i 's/user = .*/user = nginx/g' /etc/php8/php-fpm.d/www.conf && \
  sed -i 's/group = .*/group = nginx/g' /etc/php8/php-fpm.d/www.conf && \
  sed -i 's/upload_max_filesize = .*/upload_max_filesize = 50M/g' /etc/php8/php.ini && \
  sed -i 's/post_max_size = .*/post_max_size = 50M/g' /etc/php8/php.ini

COPY ./docker/site.conf /etc/nginx/http.d/default.conf
COPY ./docker/entrypoint.sh /etc/entrypoint.sh

RUN chmod +x /etc/entrypoint.sh
RUN mkdir -p /run/nginx

COPY --chown=nginx:nginx . /www/top-secret
WORKDIR /www/top-secret

RUN composer --no-cache --no-dev install

EXPOSE 80

ENTRYPOINT ["/etc/entrypoint.sh"]
