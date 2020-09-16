#!/usr/bin/env bash

RED='\033[0;31m'
GREEN='\033[0;32m'
NC='\033[0m'

echo -e "${GREEN}Building the base image...${NC}"
docker-compose -f docker-compose.base.yml build

echo -e "${GREEN}Done!${NC}"
