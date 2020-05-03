# build stage
FROM alpine:3.11

RUN apk add --no-cache nginx git openrc zip openssh curl

RUN apk add --no-cache \
  php7-cli php7-apcu php7-ctype php7-curl php7-fileinfo php7-fpm php7-gd php7-iconv php7-intl php7-json php7-mbstring \
  php7-opcache php7-openssl php7-pdo php7-pdo_sqlite php7-phar php7-posix php7-simplexml php7-session php7-soap php7-tokenizer php7-zip php7-imagick

# install composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# make sure php runs with same uid as nginx
RUN sed -i 's/user = .*/user = nginx/g' /etc/php7/php-fpm.d/www.conf && \
  sed -i 's/group = .*/group = nginx/g' /etc/php7/php-fpm.d/www.conf && \
  sed -i 's/upload_max_filesize = .*/upload_max_filesize = 50M/g' /etc/php7/php.ini && \
  sed -i 's/post_max_size = .*/post_max_size = 50M/g' /etc/php7/php.ini

COPY ./docker/site.conf /etc/nginx/conf.d/default.conf
COPY ./docker/entrypoint.sh /etc/entrypoint.sh

RUN chmod +x /etc/entrypoint.sh
RUN mkdir -p /run/nginx

COPY --chown=nginx:nginx . /www/top-secret
WORKDIR /www/top-secret

RUN composer --no-cache --no-dev install

EXPOSE 80

ENTRYPOINT ["/etc/entrypoint.sh"]
