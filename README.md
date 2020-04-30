
# Usage

Install manually or via docker (preferred) and open op the web ui. Enter "password#" to login (there is no visible password field, just type it and hit enter). 

⚠️ Be sure to change apiKey and login password after first login! ⚠️

#  Installing 

## Docker

Run the container using the following command:

```bash
docker run -d --name top-secret -p 9000:80 \
  -v /path/to/storage:/www/top-secret/storage \
  mkuhlmann/top-secret
```


## From scratch

Clone the repository, install the dependencies via `composer install` or simply run `php update.php`. 

Optionally, you are able to preconfigure any settings by creating a `storage/config.php` file with the same structure as the `config/default.php` file to configure the service. Everything should be configurable trough the web ui though. Defalt password is "password#", just type it in on the landing page.

Updating is as simple as running a `git clone`.

Sample nginx configuration with https.

```
server {
        listen 80;
        server_name YOUR_DOMAIN;

        rewrite ^ https://$http_host$request_uri? permanent;
}

server {
        listen 443 ssl http2;
        ssl on;
        ssl_certificate /etc/nginx/ssl/top-secret.crt;
        ssl_certificate_key /etc/nginx/ssl/top-secret.key;

        server_name YOUR_DOMAIN;

        root /var/www/YOUR_DOMAIN/public;

        index index.php;
        client_max_body_size 100m;


        location / {
                try_files $uri $uri/ /index.php$is_args$args;
        }

        location ~ ^/index.php$ {
                fastcgi_split_path_info ^(.+\.php)(/.+)$;
                fastcgi_pass unix:/var/run/php/php7.0-fpm.sock;
                fastcgi_index index.php;
                include fastcgi_params;
                fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
        }

        location /protected_uploads {
            internal;
            alias /var/www/YOUR_DOMAIN/storage/uploads;
        }
}
```


# Developing

Install as seen above and start the development server with

```bash
php -S 127.0.0.1:8080 -t public server.php
```

Pull requests are *very* welcome.
