#!/bin/bash
apt-get install php5 php5-pgsql php5-curl php5-geoip

wget http://geolite.maxmind.com/download/geoip/database/GeoLiteCity.dat.gz
gunzip GeoLiteCity.dat.gz
mkdir -v /usr/share/GeoIP
mv -v GeoLiteCity.dat /usr/share/GeoIP/GeoIPCity.dat
