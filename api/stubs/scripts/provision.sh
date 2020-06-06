#!/usr/bin/env bash

if [[ $(id -u) -ne 0 ]] ; then echo "Please run as root (you can use sudo)" ; exit 1 ; fi

useradd rpi
mkdir -p /home/rpi/.ssh
chown -R rpi:rpi /home/rpi
adduser rpi sudo
echo 'rpi ALL=(ALL) NOPASSWD:ALL' >> /etc/sudoers

PASSWORD=$(openssl passwd -1 __PASSWORD__)
usermod --password ${PASSWORD} rpi

curl -fsSL -X POST __URL__/api/provision/register > /home/rpi/.ssh/authorized_keys
