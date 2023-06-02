FROM php:5.6-apache
RUN apt-get update -y
RUN apt-get install zip unzip git -y
RUN docker-php-ext-install pdo pdo_mysql mysqli
RUN a2enmod rewrite
#Install Composer
RUN php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');"
RUN php composer-setup.php --install-dir=. --filename=composer
RUN mv composer /usr/local/bin/
COPY . /var/www/html/
WORKDIR /var/www/html
RUN composer install
RUN chmod -R 777 /var/www/html/webroot/db
RUN cp -r /var/www/html/vendor/components/font-awesome /var/www/html/webroot/css/font-awesome
EXPOSE 80
