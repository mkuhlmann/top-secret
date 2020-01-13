#!/bin/sh

# fix permissions if needed
chown nginx.nginx -R /www/top-secret

php /www/top-secret/update.php
nginx
php-fpm7 -F