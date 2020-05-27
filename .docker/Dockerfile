FROM ubuntu:18.04

# install required packages
RUN apt-get update && apt-get install -y software-properties-common gosu sudo && \
    gosu nobody true

# configure user
RUN useradd -ms /bin/bash -u 1337 rpi && adduser rpi sudo && echo '%sudo ALL=(ALL) NOPASSWD:ALL' >> /etc/sudoers

RUN add-apt-repository ppa:ondrej/php

RUN apt-get update && \
    apt-get install -y sqlite3 curl unzip supervisor dnsmasq nginx nmap \
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

# configure dnsmasq
RUN echo "ENABLED=1\nIGNORE_RESOLVCONF=yes" > /etc/default/dnsmasq
COPY config/dnsmasq.conf /etc/dnsmasq.d/netboot.conf
RUN mkdir /tftpboot

# configure nginx
RUN echo "daemon off;" >> /etc/nginx/nginx.conf
COPY config/nginx.conf /etc/nginx/sites-available/default
RUN ln -sf /dev/stdout /var/log/nginx/access.log \
    && ln -sf /dev/stderr /var/log/nginx/error.log

# configure php-fpm
COPY config/php-fpm.conf /etc/php/7.4/fpm/php-fpm.conf

# configure supervisor
COPY config/supervisord.conf /etc/supervisor/conf.d/supervisord.conf

# entrypoint
COPY start-container /usr/local/bin/start-container
RUN chmod +x /usr/local/bin/start-container

ENTRYPOINT ["start-container"]
