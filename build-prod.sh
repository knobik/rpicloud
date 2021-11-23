#!/usr/bin/env bash

RED='\033[0;31m'
GREEN='\033[0;32m'
NC='\033[0m'

echo -e "${GREEN}Building the production image...${NC}"
docker build --target=production -t knobik/rpicloud

echo -e "${GREEN}Done!${NC}"
