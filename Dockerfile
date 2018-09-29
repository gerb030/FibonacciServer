FROM richarvey/nginx-php-fpm
ADD ./html/ /var/www/html/
ADD ./php/ /var/www/php/
WORKDIR /html
