#!/usr/bin/env bash

RED='\033[0;31m'
GREEN='\033[0;32m'
NC='\033[0m'

echo -e "${GREEN}Building the docker image...${NC}"
docker-compose up -d --build
docker-compose exec --user rpi rpi-cluster-pxe composer install -d /var/www/html/api/
docker-compose exec --user rpi rpi-cluster-pxe composer setup -d /var/www/html/api/
docker-compose exec --user rpi rpi-cluster-pxe bash -c "cd /var/www/html/web && npm install && npm run build"

echo -e "${GREEN}Done!${NC}"
