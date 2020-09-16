FROM BaseImage

RUN mkdir /baseImages

# dnsmasq
COPY .docker/config/dnsmasq/pxeservice.conf /etc/dnsmasq.d/pxeservice.conf

# nginx
COPY .docker/config/nginx/api.conf /etc/nginx/sites-enabled/api.conf
COPY .docker/config/nginx/web.conf /etc/nginx/sites-enabled/web.conf

# application
ADD api /api
ADD web /web

ENTRYPOINT ["start-container"]
