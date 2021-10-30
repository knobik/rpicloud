# base configuration
FROM ubuntu:18.04 as base
ENV DEBIAN_FRONTEND=noninteractive

# install required packages
RUN apt-get update && apt-get install -y software-properties-common sudo curl

# configure user
RUN useradd -ms /bin/bash -u 1337 rpi && adduser rpi sudo && echo 'rpi ALL=(ALL) NOPASSWD:ALL' >> /etc/sudoers

RUN add-apt-repository ppa:ondrej/php

RUN apt-get update && \
    curl -sL https://deb.nodesource.com/setup_14.x  | bash - && apt-get install -y \
    build-essential cron sqlite3 curl unzip supervisor \
    nginx ssh nodejs git redis-server \
    dnsmasq kpartx nfs-kernel-server \
    php8.0-fpm php8.0-cli \
    php8.0-sqlite3 \
    php8.0-redis\
    php8.0-gd \
    php8.0-ssh2 \
    php8.0-curl \
    php8.0-imap \
    php8.0-mbstring \
    php8.0-xml \
    php8.0-zip \
    php8.0-bcmath \
    php8.0-intl \
    php8.0-readline \
    php8.0-msgpack \
    php8.0-igbinary && \
    php -r "readfile('http://getcomposer.org/installer');" | php -- --install-dir=/usr/bin/ --filename=composer && \
    mkdir /run/php && \
    apt-get -y autoremove && apt-get clean

RUN mkdir -p /nfs/backups /nfs/boot /nfs/root && chown -R rpi:rpi /nfs/backups
#RUN mkdir -p /nfs/backups /.data/baseImage/boot /.data/baseImage/root
#RUN ln -s /.data/baseImage/boot /nfs/boot && ln -s /.data/baseImage/root /nfs/root
VOLUME /nfs

# configure dnsmasq
RUN echo "ENABLED=1\nIGNORE_RESOLVCONF=yes" > /etc/default/dnsmasq

# configure nginx
COPY .docker/config/nginx/nginx.conf /etc/nginx/nginx.conf

# configure php-fpm
COPY .docker/config/php-fpm/php-fpm.conf /etc/php/8.0/fpm/php-fpm.conf
COPY .docker/config/php-fpm/www.conf /etc/php/8.0/fpm/pool.d/www.conf

# configure supervisor
COPY .docker/config/supervisord.conf /etc/supervisor/conf.d/supervisord.conf

# configure crontab
COPY .docker/schedule.sh /schedule.sh
RUN chmod +x /schedule.sh

# configure nfs
COPY .docker/config/nfs-exports /etc/exports

# entrypoint
COPY .docker/start-container /usr/local/bin/start-container
RUN chmod +x /usr/local/bin/start-container

# development only configuration
FROM base as development

ENTRYPOINT ["start-container"]

# production only configuration
FROM base as production

# dnsmasq
COPY .docker/config/dnsmasq/pxeservice.conf /etc/dnsmasq.d/pxeservice.conf

# nginx
RUN rm /etc/nginx/sites-enabled/default
COPY .docker/config/nginx/virtualhosts/api.conf /etc/nginx/sites-enabled/api.conf
COPY .docker/config/nginx/virtualhosts/web.conf /etc/nginx/sites-enabled/web.conf

RUN mkdir /baseImages /api /web && chown rpi:rpi /baseImages

USER rpi
RUN git clone https://github.com/knobik/rpicloud.git /tmp/rpicloud && \
    composer install -d /tmp/rpicloud/api && \
    bash -c "cd /tmp/rpicloud/web && npm install && npm run build"

USER root

RUN mkdir -p /.data && chown -R rpi:rpi /.data
VOLUME /.data

# cleanup
RUN mv /tmp/rpicloud/api / && \
    mv /tmp/rpicloud/web/dist /web/dist && \
    rm -rf /tmp/rpicloud

ENTRYPOINT ["start-container"]
