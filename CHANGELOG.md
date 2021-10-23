# Changelog
All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [0.5.0]
### Added
- Node hardware details view

### Fixed


## [0.4.0] - 2021-10-23
### Added
- Browser SSH terminal.
- Boot order for RPi4
- Upload backup / img from URL.
- More hardware info for the nodes.
- Queue job monitor for debugging available at http://your-server:8080/jobs (unsecured atm, what did i say? this is alpha! dont use in production!)
- Added backup filesize

### Fixed
- CHANGELOG.md not updated ;)
- Alot of small fixes and updates to the packages and code.

## [0.3.0] - 2020-10-01
### Added
- Improved docker image generation, now instead of downloading the full repository you can get the image from hub.docker.com for Arm 32bit, Arm 64bit and x86_64 

### Changed
- Copy files from base image instead using a mounted raspberry pi os img. If something would break, or if you would stop the docker image then mounting another base image would be impossible due to the zombie loop devices and default max_loop value in the system.

### Fixed
- Some stability issues have been fixed.

## [0.2.0] - 2020-09-21
### Added
- Docker image generation

### Fixed
- User permissions ([issue #1](https://github.com/knobik/rpicloud/issues/1))

## [0.1.10] - 2020-08-16
### Fixed
- Restore modal id error when trying to restore nodeless backup from global backup list.
- Bump symfony/http-kernel from 5.1.3 to 5.1.5 (https://github.com/Knobiks/rpicloud/pull/3)

## [0.1.9] - 2020-08-16
### Added
- Restoring backup updates node model on the frontend
- Making backup updates node model on the frontend
- User logout
- Spinner to operation alert
- Node provision tooltip on node list page
- Enable SSH after restore (/boot/ssh file method)

### Fixed
- Restore modal not showing for nodeless/uploaded backups.
- Uploaded image permissions

## [0.1.8] - 2020-08-14
### Added
- CHANGELOG.md

## [0.1.7] - 2020-08-14
### Added
- Backup upload modal
- Symfony dump-server for development

### Fixed
- Modals hidding before finishing request
- Temporary fix for login CSRF exception

### Changed
- Updated npm packages.


[0.5.0]: https://github.com/knobik/rpicloud/compare/0.5.0...0.4.0
[0.4.0]: https://github.com/knobik/rpicloud/compare/0.4.0...0.3.0
[0.3.0]: https://github.com/knobik/rpicloud/compare/0.3.0...0.2.0
[0.2.0]: https://github.com/knobik/rpicloud/compare/0.1.10...0.2.0
[0.1.10]: https://github.com/knobik/rpicloud/compare/0.1.9...0.1.10
[0.1.9]: https://github.com/knobik/rpicloud/compare/0.1.8...0.1.9
[0.1.8]: https://github.com/knobik/rpicloud/compare/0.1.7...0.1.8
[0.1.7]: https://github.com/knobik/rpicloud/compare/0.1.6...0.1.7
