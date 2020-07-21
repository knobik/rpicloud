**!!! This software is in alpha stage, i dont recommend using it in production !!!**

# About
Ever wanted to just "reinstall" your RPi4 node in your cluster without the hussle of pulling the sd card out, flashing it on a PC and putting it back in? This is exacly the reason i started this.

My cluster is based on RPi4 only, so i didnt test it on other version. 

# Features
* Manage pi cluster inventory (TODO: export as ansible)
* Reboot / Shutdown nodes
* Netboot node for recovery
* Backup node
* Restore node from backup
* Easy reinstall through netboot, without removing sd card or usb device.

# Quick start

### Requirements
* Docker version 19.03 or newer
* docker-compose version 1.25 or newer
* Netboot then sd / usb boot order on RPi4 nodes. (i use `BOOT_ORDER=0xf132` which means `netboot -> usb -> sdcard -> restart`, to boot faster you can also set `DHCP_TIMEOUT=5000` and `DHCP_REQ_TIMEOUT=500`). [How to edit bootloader config.](https://www.raspberrypi.org/documentation/hardware/raspberrypi/bcm2711_bootloader_config.md) 

### Setup
Clone the repository 
```
git clone git@github.com:knobik/rpi-cluster-pxe.git
```

make `build.sh` executable
```
chmod +x build.sh
```

run the `build.sh` script
```
./build.sh
```

This will take a while, it needs to download all php and node dependencies and also download the latest raspberry pi os and set it up for pxe booting.

Login and have fun:
```
login: admin@example.com
password: admin
```

# Development
All operations are made inside the docker container, so you need to ssh into the container. You can do it easy with `./ssh.sh`

### Frontend development
```
$ cd /web && npm run dev
```

Hot reloaded frontend: `http://localhost:3000`

### Backend development
```
$ cd /api
```