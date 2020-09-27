#!/usr/bin/env bash

RED='\033[0;31m'
GREEN='\033[0;32m'
NC='\033[0m'

echo -e "${GREEN}Building the development image...${NC}"
docker-compose -f docker-compose.yml up -d --build
docker-compose -f docker-compose.yml exec --user rpi rpicloud-dev composer install -d /api/
docker-compose -f docker-compose.yml exec --user rpi rpicloud-dev composer setup -d /api/
docker-compose -f docker-compose.yml exec --user rpi rpicloud-dev bash -c "cd /web && npm install && npm run build"
docker-compose down
echo -e "${GREEN}Done!${NC}"
