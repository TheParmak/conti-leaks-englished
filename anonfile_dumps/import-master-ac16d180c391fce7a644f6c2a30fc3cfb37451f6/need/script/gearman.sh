#!/bin/bash

cd ~
apt-get update
apt-get install -y wget mod-gearman-tools checkinstall libpq-dev libcloog-ppl-dev uuid-dev libevent-dev gperf libboost-all-dev libgearman-dev libgearman7 php5-gearman build-essential
wget https://github.com/gearman/gearmand/releases/download/1.1.17/gearmand-1.1.17.tar.gz
tar xf gearmand-1.1.17.tar.gz
cd gearmand-1.1.17/
./configure --with-boost-libdir=/usr/lib/$(dpkg-architecture -qDEB_HOST_MULTIARCH) --prefix=/usr
make

# debian fix
if [ ! -d /usr/local/include/libgearman-1.0 ]; then
    mkdir /usr/local/include/libgearman-1.0
fi

checkinstall -D --install=no -y
apt-get purge libgearman-dev -y
dpkg -i gearmand_1.1.17-1_amd64.deb

if [ $? -eq 0 ]; then
    echo "Could not install gearmand. Stopping."
    exit 1
fi