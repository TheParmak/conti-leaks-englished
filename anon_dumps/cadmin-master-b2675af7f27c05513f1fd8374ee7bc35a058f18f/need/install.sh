!#/bin/bash

apt-get install curl apache2 openssl apache2-utils ssl-cert php5 php5-pgsql php5-curl php5-mbstring nodejs npm -y
apt-get install curl apache2 openssl apache2-utils ssl-cert php5.6 php5.6-pgsql php5.6-curl php5.6-mbstring nodejs npm -y
npm install -g bower
ln -s /usr/bin/nodejs /usr/bin/node

curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

mkdir /etc/apache2/ssl
openssl req -new -x509 -days 365 -nodes -out /etc/apache2/ssl/server.pem -keyout /etc/apache2/ssl/server.key
chmod 600 /etc/apache2/ssl/*
a2enmod ssl
a2dissite default
a2ensite default-ssl