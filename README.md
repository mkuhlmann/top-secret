## Installing

Clone the repository, and ether install composer dependenies manually or simply run `php install.php`. Then create a `config/local.php` file and with the same structure as the `config/default.php` file to configure the service.

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

        location ~ \.php$ {
                fastcgi_split_path_info ^(.+\.php)(/.+)$;
                fastcgi_pass unix:/var/run/php/php7.0-fpm.sock;
                fastcgi_index index.php;
                include fastcgi_params;
                fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
        }
}
```
