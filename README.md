**!!! This software is in alpha stage, i dont recommend using it in production !!!**

#Quick start

###Requirements
* Docker version 19.03 or newer
* docker-compose version 1.25 or newer

###Setup
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

#Development
All operations are made inside the docker container, so you need to ssh into the container. You can do it easy with `./ssh.sh`

###Frontend development
```
$ cd /web && npm run dev
```

###Backend development
```
$ cd /api
```