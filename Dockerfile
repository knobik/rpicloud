# base configuration
FROM ubuntu:18.04 as base
ENV DEBIAN_FRONTEND=noninteractive

# install required packages
RUN apt-get update && apt-get install -y software-properties-common gosu sudo curl && \
    gosu nobody true

# configure user
RUN useradd -ms /bin/bash -u 1337 rpi && adduser rpi sudo && echo 'rpi ALL=(ALL) NOPASSWD:ALL' >> /etc/sudoers
#'%sudo ALL=(ALL) NOPASSWD:ALL' >> /etc/sudoers

RUN add-apt-repository ppa:ondrej/php

RUN apt-get update && \
    curl -sL https://deb.nodesource.com/setup_12.x  | bash - && apt-get install -y \
    build-essential cron sqlite3 curl unzip supervisor \
    dnsmasq nginx ssh nodejs git \
    kpartx nfs-kernel-server \
    php7.4-fpm php7.4-cli \
    php7.4-sqlite3 \
    php7.4-gd \
    php7.4-curl \
    php7.4-imap \
    php7.4-mbstring \
    php7.4-xml \
    php7.4-zip \
    php7.4-bcmath \
    php7.4-intl \
    php7.4-readline \
    php7.4-msgpack \
    php7.4-igbinary && \
    php -r "readfile('http://getcomposer.org/installer');" | php -- --install-dir=/usr/bin/ --filename=composer && \
    mkdir /run/php && \
    apt-get -y autoremove && apt-get clean

USER rpi
RUN composer global require hirak/prestissimo
USER root
RUN mkdir -p /nfs/root /nfs/boot /nfs/backups
VOLUME /nfs

# configure dnsmasq
RUN echo "ENABLED=1\nIGNORE_RESOLVCONF=yes" > /etc/default/dnsmasq

# configure nginx
COPY .docker/config/nginx/nginx.conf /etc/nginx/nginx.conf

# configure php-fpm
COPY .docker/config/php-fpm/php-fpm.conf /etc/php/7.4/fpm/php-fpm.conf
COPY .docker/config/php-fpm/www.conf /etc/php/7.4/fpm/pool.d/www.conf

# configure supervisor
COPY .docker/config/supervisord.conf /etc/supervisor/conf.d/supervisord.conf

# configure crontab
COPY .docker/schedule.sh /schedule.sh
RUN chmod +X /schedule.sh

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

# cleanup
RUN mv /tmp/rpicloud/api / && \
    mv /tmp/rpicloud/web/dist /web/dist && \
    rm -rf /tmp/rpicloud

ENTRYPOINT ["start-container"]
