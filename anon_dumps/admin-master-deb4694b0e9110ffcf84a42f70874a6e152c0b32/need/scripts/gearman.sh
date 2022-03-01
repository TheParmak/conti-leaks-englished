#!/bin/bash

cd ~
apt-get update
apt-get install -y wget mod-gearman-tools checkinstall libpq-dev libcloog-ppl-dev uuid-dev libevent-dev gperf libboost-all-dev libgearman-dev libgearman7 build-essential php-pear php5-dev
wget https://launchpad.net/gearmand/1.2/1.1.12/+download/gearmand-1.1.12.tar.gz
tar xf gearmand-1.1.12.tar.gz
cd gearmand-1.1.12/
./configure --with-boost-libdir=/usr/lib/$(dpkg-architecture -qDEB_HOST_MULTIARCH) --prefix=/usr
make

# debian fix
if [ ! -d /usr/local/include/libgearman-1.0 ]; then
    mkdir /usr/local/include/libgearman-1.0
fi

checkinstall -D --install=no -y
apt-get purge libgearman-dev php5-gearman -y
dpkg -i gearmand_1.1.12-1_amd64.deb

if [ $? -eq 0 ]; then
    echo "Could not install gearmand. Stopping."
    exit 1
fi

pecl install gearman-1.1.2
echo "extension=gearman.so" > /etc/php5/mods-available/gearman.ini