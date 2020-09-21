# Quick start

```
docker run -d -v ~/backups:/nfs/backups --privileged --network host knobik/rpicloud
```

 * `--privileged` is needed to have control over nfs kernel module and loop devices for mounting the base image. 
 * `--network host` simplifies network configuration for the `dhcp`, `nfs`, `tftp`, `http` services. 

Login to web UI:
```
login: admin@example.com
password: admin
```


### Node setup
Set RPi4 boot order by editing the [eeprom settings](https://www.raspberrypi.org/documentation/hardware/raspberrypi/bcm2711_bootloader_config.md). Netboot then sd / usb boot order. (i use `BOOT_ORDER=0xf132` which means `netboot -> usb -> sdcard -> restart`, to boot faster you can also set `DHCP_TIMEOUT=5000` and `DHCP_REQ_TIMEOUT=500`).

### More info at [github.com repository](https://github.com/knobik/rpicloud)