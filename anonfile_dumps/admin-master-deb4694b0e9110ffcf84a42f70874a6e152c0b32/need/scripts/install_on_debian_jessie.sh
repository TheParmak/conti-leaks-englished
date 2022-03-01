#!/bin/sh
if [ ! -f /etc/debian_version ]; then
    echo "Debian Jessie required. Stopping."
    exit 1
fi
dpkg --compare-versions $(cat /etc/debian_version) lt 8.0 || dpkg --compare-versions $(cat /etc/debian_version) ge 9.0
if [ $? -eq 0 ]; then
    echo "Debian Jessie required. Stopping."
    exit 1
fi

echo "net.ipv4.ip_nonlocal_bind=1" | tee -a /etc/sysctl.conf
echo "fs.file-max=3355444" | tee -a /etc/sysctl.conf
echo "vm.swappiness=1" | tee -a /etc/sysctl.conf
echo "KOHANA_ENV=production" | tee -a /etc/environment

apt-get update
apt-get install -y git supervisor
apt-get install -y apache2 apache2-mpm-prefork
a2enmod rewrite
a2enmod ssl
apt-get install -y php5 php5-cli php5-mcrypt php5-pgsql php5-gearman libapache2-mod-php5
apt-get install -y gearman-job-server tinc
