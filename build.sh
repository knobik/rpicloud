#!/usr/bin/env bash

RED='\033[0;31m'
GREEN='\033[0;32m'
NC='\033[0m'

echo -e "${GREEN}Building the environment image...${NC}"
docker-compose -f docker-compose.yml up -d --build
docker-compose exec --user rpi rpi-cluster-pxe composer install -d /api/
docker-compose exec --user rpi rpi-cluster-pxe composer setup -d /api/
docker-compose exec --user rpi rpi-cluster-pxe bash -c "cd /web && npm install && npm run build"
docker-compose down

echo -e "${GREEN}Building the docker image...${NC}"
docker-compose -f docker-compose.prod.yml up -d --build

#docker-compose down

echo -e "${GREEN}Done!${NC}"
