FROM ubuntu:18.04 as BaseImage

# install required packages
RUN apt-get update && apt-get install -y software-properties-common gosu sudo curl && \
    gosu nobody true

# configure user
RUN useradd -ms /bin/bash -u 1337 rpi && adduser rpi sudo && echo 'rpi ALL=(ALL) NOPASSWD:ALL' >> /etc/sudoers
#'%sudo ALL=(ALL) NOPASSWD:ALL' >> /etc/sudoers

RUN add-apt-repository ppa:ondrej/php

RUN apt-get update && \
    curl -sL https://deb.nodesource.com/setup_12.x  | bash - && apt-get install -y \
    cron sqlite3 curl unzip supervisor \
    dnsmasq nginx ssh nodejs \
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
RUN echo "daemon off;" >> /etc/nginx/nginx.conf
RUN ln -sf /dev/stdout /var/log/nginx/access.log \
    && ln -sf /dev/stderr /var/log/nginx/error.log

# configure php-fpm
COPY .docker/config/php-fpm.conf /etc/php/7.4/fpm/php-fpm.conf

# configure supervisor
COPY .docker/config/supervisord.conf /etc/supervisor/conf.d/supervisord.conf

# configure crontab
COPY schedule.sh /schedule.sh
RUN chmod +X /schedule.sh

# configure nfs
COPY .docker/config/nfs-exports /etc/exports

# entrypoint
COPY start-container /usr/local/bin/start-container
RUN chmod +x /usr/local/bin/start-container

ENTRYPOINT ["start-container"]
