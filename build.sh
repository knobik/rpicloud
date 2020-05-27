#!/usr/bin/env bash

RED='\033[0;31m'
GREEN='\033[0;32m';
NC='\033[0m'
DOWNLOAD_URL="https://downloads.raspberrypi.org/raspbian_lite_latest"

echo -e "${GREEN}This script will download the latest raspbian image, mount it and copy its files as a base for netboot, next it will build and run the docker image.${NC}"

echo -e "${GREEN}Downloading from ${NC}${DOWNLOAD_URL}"
curl -fSL ${DOWNLOAD_URL} -o /tmp/raspbian.zip

echo -e "${GREEN}Inflating the image...${NC}"
unzip -o /tmp/raspbian.zip -d /tmp/raspbian
rm /tmp/raspbian.zip
IMAGE_FILENAME=$(find /tmp/raspbian/ -type f -name "*.img")

echo -e "${GREEN}Fetching partition offsets.${NC}"
OFFSET_FAT32=$(($(fdisk -l ${IMAGE_FILENAME} | grep ^${IMAGE_FILENAME}1 | awk -F" " '{ print $2 }') * 512))
echo -e "${GREEN}Offset for FAT32 partition is: ${NC}${OFFSET_FAT32}"

OFFSET_EXT4=$(($(fdisk -l ${IMAGE_FILENAME} | grep ^${IMAGE_FILENAME}2 | awk -F" " '{ print $2 }') * 512))
echo -e "${GREEN}Offset for EXT4 partition is: ${NC}${OFFSET_EXT4}"

echo -e "${GREEN}Mounting boot partition${NC}"
BOOT_PATH=/mnt/img/boot
sudo mkdir -p ${BOOT_PATH}
sudo mount -v -o offset=${OFFSET_FAT32} -t vfat ${IMAGE_FILENAME} ${BOOT_PATH}
sudo cp -a ${BOOT_PATH} ./baseImage/boot
sudo umount ${BOOT_PATH}

echo -e "${GREEN}Mounting root partition${NC}"
ROOT_PATH=/mnt/img/root
sudo mkdir -p ${ROOT_PATH}
sudo mount -v -o offset=${OFFSET_EXT4} -t ext4 ${IMAGE_FILENAME} ${ROOT_PATH}
sudo cp -a ${ROOT_PATH} ./baseImage/root
sudo umount ${ROOT_PATH}

echo -e "${GREEN}Buidling the docker image...${NC}"
docker-compose up -d --build
docker-compose exec --user rpi rpi-cluster-pxe composer install -d /var/www/html/
docker-compose exec --user rpi rpi-cluster-pxe composer after-build -d /var/www/html/

echo -e "${GREEN}Cleaning up...${NC}"
rm ${IMAGE_FILENAME}

echo -e "${GREEN}Done!${NC}"
