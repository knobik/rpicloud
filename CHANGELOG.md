# Changelog
All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [Unreleased]
### Added
- Browser SSH terminal.

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


[Unreleased]: https://github.com/knobik/rpi-cluster-pxe/compare/0.2.0...HEAD
[0.2.0]: https://github.com/knobik/rpi-cluster-pxe/compare/0.1.10...0.2.0
[0.1.10]: https://github.com/knobik/rpi-cluster-pxe/compare/0.1.9...0.1.10
[0.1.9]: https://github.com/knobik/rpi-cluster-pxe/compare/0.1.8...0.1.9
[0.1.8]: https://github.com/knobik/rpi-cluster-pxe/compare/0.1.7...0.1.8
[0.1.7]: https://github.com/knobik/rpi-cluster-pxe/compare/0.1.6...0.1.7
