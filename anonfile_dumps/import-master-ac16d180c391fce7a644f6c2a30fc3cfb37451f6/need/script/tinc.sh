#!/bin/bash

defaultName="import_1"
defaultRealIp="91.219.31.6"
defaultVirtualIp="10.0.0.1"

echo -n "Enter name: [$defaultName]"
read name
if [ -z $name ]; then
	name=$defaultName
	echo "set default name: $name"
fi

if [ -f /etc/tinc/netname/hosts/$name ]; then
    echo "Config file found!"
    exit 1;
fi

echo -n "Enter real ip: [$defaultRealIp]"
read realIp
if [ -z $realIp ]; then
	realIp=$defaultRealIp
	echo "set default real ip: $realIp"
fi

echo -n "Enter virtual ip: [$defaultVirtualIp]"
read virtualIp
if [ -z $virtualIp ]; then
	virtualIp=$defaultVirtualIp
	echo "set default virtual ip: $virtualIp"
fi

apt-get update
apt-get install tinc -y
mkdir -p /etc/tinc/netname/hosts

cat > /etc/tinc/netname/tinc.conf << EOF
Name = $defaultName
AddressFamily = ipv4
Interface = tun0
EOF

cat > /etc/tinc/netname/hosts/$name << EOF
Subnet = $virtualIp/32
Address = $realIp
EOF

tincd -n netname -K4096

cat > /etc/tinc/netname/tinc-down << EOF
ifconfig \$INTERFACE down
EOF

cat > /etc/tinc/netname/tinc-up << EOF
ifconfig \$INTERFACE $virtualIp netmask 255.255.255.0
EOF

chmod 755 /etc/tinc/netname/tinc-*

cat > /etc/tinc/nets.boot << EOF
netname
EOF

echo "COMPLETE"