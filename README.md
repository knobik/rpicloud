**!!! This software is in alpha stage, i dont recommend using it in production !!!**

![pipeline status](https://gitlab.com/knobik/rpicloud/badges/master/pipeline.svg)

# About
Ever wanted to just "reinstall" your RPi4 node in your cluster without the hussle of pulling the sd card out, flashing it on a PC and putting it back in? This is exacly the reason i started this. Awsome raspberry pi cluster management software you can host on your PC or one of your nodes.

My personal cluster is based on RPi4 4/8GB only, so i didnt test it on any other PI revisions.

# Features
* Manage pi cluster inventory, status, etc (TODO: export to ansible)
* Reboot / Shutdown nodes
* Netboot node for recovery
* Backup / Restore node
* Easy reinstall through netboot, without removing sd card or usb device.

## For updates see [CHANGELOG.md]
[CHANGELOG.md]: CHANGELOG.md

# Quick start

```
docker run -d -v ~/backups:/nfs/backups --privileged --network host knobik/rpicloud
```

 * `--privileged` is needed to have control over nfs kernel module and loop devices for mounting the base image. 
 * `--network host` simplifies network configuration for the `dhcp`, `nfs`, `tftp`, `http` services. 

Login and have fun:
```
login: admin@example.com
password: admin
```

# Development

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

### Side notes
* One netbooted PI at a time, booting multiple PI's from one image might corrupt the image or lead to operation errors.
* In the background, `dd` is used to make an img of your sd card and then a script is used to resize it so each backup doesnt take 16/32+ GB of space ;) This means you need atleast as much free space on your host as the size of your sd card / usb drive.

### Todo
- [X] Validate free disk space before making a backup 
- [ ] User management (now the user is admin@example.com, we need to change that!)
- [ ] Export inventory to ansible
- [ ] Multiple netboot images, preferably one per node or a netboot pool. (maybe, each netboot image takes atleast 3GB+ of disk space)
