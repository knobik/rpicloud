#!/usr/bin/env bash

if [[ $(id -u) -ne 0 ]] ; then echo "Please run as root (you can use sudo)" ; exit 1 ; fi

# add system user
if ! id -u __config.user__ > /dev/null 2>&1; then
    useradd __config.user__
    mkdir -p /home/__config.user__/.ssh
    chown -R __config.user__:__config.user__ /home/__config.user__
    adduser __config.user__ sudo
    echo '__config.user__ ALL=(ALL) NOPASSWD:ALL' >> /etc/sudoers

    PASSWORD=$(openssl passwd -1 __password__)
    usermod --password ${PASSWORD} __config.user__
else
    echo "__config.user__ user aleady exists. Skipping..."
fi;

# set the authorized ssh key, system user should login without password
curl -fsSL -X POST __url__/api/provision/register > /home/__config.user__/.ssh/authorized_keys

# install cloud-init and disable it for time being
apt update && apt install -y cloud-init
touch /etc/cloud/cloud-init.disabled

echo "Done."
