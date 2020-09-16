#!/usr/bin/env bash

RED='\033[0;31m'
GREEN='\033[0;32m'
NC='\033[0m'

./build-dev.sh
docker-compose -f docker-compose.yml down

echo -e "${GREEN}Building the docker image...${NC}"
docker-compose -f docker-compose.prod.yml up -d --build
docker-compose -f docker-compose.prod.yml down

echo -e "${GREEN}Done!${NC}"
