# Release Notes

## [Unreleased](https://github.com/laravel/forge-cli/compare/v1.8.3...master)

## [v1.8.3](https://github.com/laravel/forge-cli/compare/v1.8.2...v1.8.3) - 2024-09-30

### What's Changed

* Always set user agent to Forge CLI by [@olivernybroe](https://github.com/olivernybroe) in https://github.com/laravel/forge-cli/pull/93

## [v1.8.2](https://github.com/laravel/forge-cli/compare/v1.8.1...v1.8.2) - 2024-09-27

* Replace dead link in Security Policy by [@Jubeki](https://github.com/Jubeki) in https://github.com/laravel/forge-cli/pull/90
* Add "open" command by [@mpociot](https://github.com/mpociot) in https://github.com/laravel/forge-cli/pull/91
* Set a User-Agent string by [@jbrooksuk](https://github.com/jbrooksuk) in https://github.com/laravel/forge-cli/pull/92

## [v1.8.1](https://github.com/laravel/forge-cli/compare/v1.8.0...v1.8.1) - 2024-06-24

* fix: Use custom ssh port in CLI commands if defined by [@olivernybroe](https://github.com/olivernybroe) in https://github.com/laravel/forge-cli/pull/87
* fix: deployment command showing wrong output by [@olivernybroe](https://github.com/olivernybroe) in https://github.com/laravel/forge-cli/pull/88

## [v1.8.0](https://github.com/laravel/forge-cli/compare/v1.7.0...v1.8.0) - 2023-06-06

- Support PHP 8.2 and use single source of truth by @jbrooksuk in https://github.com/laravel/forge-cli/pull/73

## [v1.7.0](https://github.com/laravel/forge-cli/compare/v1.6.0...v1.7.0) - 2023-02-21

- Migrates to Laravel Zero 10 by @nunomaduro in https://github.com/laravel/forge-cli/pull/71

## [v1.6.0](https://github.com/laravel/forge-cli/compare/v1.5.0...v1.6.0) - 2023-01-31

### Added

- Add prompt/option for username parameter required by forge api by @morales2k in https://github.com/laravel/forge-cli/pull/70

## [v1.5.0](https://github.com/laravel/forge-cli/compare/v1.4.1...v1.5.0) - 2023-01-03

### Added

- Add option for specifying remote user when calling the ssh command by @tkuijer in https://github.com/laravel/forge-cli/pull/67

## [v1.4.1](https://github.com/laravel/forge-cli/compare/v1.4.0...v1.4.1) - 2022-12-13

### Changed

- Update dependencies

## [v1.4.0](https://github.com/laravel/forge-cli/compare/v1.3.4...v1.4.0) - 2022-02-15

### Fixed

- PHP 8.1 deprecation warnings ([#54](https://github.com/laravel/forge-cli/pull/54))

### Changed

- Drops PHP 7.3 and PHP 7.4 ([#54](https://github.com/laravel/forge-cli/pull/54))

## [v1.3.4 (2021-11-16)](https://github.com/laravel/forge-cli/compare/v1.3.3...v1.3.4)

### Changed

- Improves `ssh:configure` command ([#46](https://github.com/laravel/forge-cli/pull/46))

## [v1.3.3 (2021-11-02)](https://github.com/laravel/forge-cli/compare/v1.3.2...v1.3.3)

### Changed

- Improves `ssh:configure` command ([#44](https://github.com/laravel/forge-cli/pull/44))

## [v1.3.2 (2021-10-29)](https://github.com/laravel/forge-cli/compare/v1.3.1...v1.3.2)

### Fixed

- Usage on non-interactive environments ([#43](https://github.com/laravel/forge-cli/pull/43))

## [v1.3.1 (2021-10-26)](https://github.com/laravel/forge-cli/compare/v1.3.0...v1.3.1)

### Changed

- Allow the usage of FORGE_API_TOKEN in CIs ([#42](https://github.com/laravel/forge-cli/pull/42))

## [v1.3.0 (2021-09-11)](https://github.com/laravel/forge-cli/compare/v1.2.0...v1.3.0)

### Added

- PHP 8.1 support ([#26](https://github.com/laravel/forge-cli/pull/26))

## [v1.2.0 (2021-08-28)](https://github.com/laravel/forge-cli/compare/v1.1.1...v1.2.0)

### Added

- Server and site tags to `list` commands ([#36](https://github.com/laravel/forge-cli/pull/36))

## [v1.1.1 (2021-08-28)](https://github.com/laravel/forge-cli/compare/v1.1.0...v1.1.1)

### Fixed

- Avoids attempt to question when there is no answers available ([#33](https://github.com/laravel/forge-cli/pull/33))

## [v1.1.0 (2021-08-11)](https://github.com/laravel/forge-cli/compare/v1.0.0...v1.1.0)

### Added

- `env:pull` and `env:push` commands ([#19](https://github.com/laravel/forge-cli/pull/19))

## [v1.0.0 (2021-08-09)](https://github.com/laravel/forge-cli/compare/v0.1.5...v1.0.0)

Stable version.

## [v0.1.5 (2021-08-04)](https://github.com/laravel/forge-cli/compare/v0.1.4...v0.1.5)

### Fixed

- Archived servers appearing on `server:list` command ([b941187](https://github.com/laravel/forge-cli/commit/b94118770b2344b6cacf2fb13f9dbcc027be0375))
- Commands based on `tail` operations failing ([bd5dc8a](https://github.com/laravel/forge-cli/commit/bd5dc8a878192326f320b48ad55b0f9db08b2888))

## [v0.1.4 (2021-08-03)](https://github.com/laravel/forge-cli/compare/v0.1.3...v0.1.4)

### Added

- Runtime errors display next steps ([aced4f9](https://github.com/laravel/forge-cli/commit/aced4f9d7f50fa22e683ce49111d56d7e14ac3ab))

### Changed

- `--tail` option got renamed to `--follow` ([#8](https://github.com/laravel/forge-cli/pull/8))

### Fixed

- Empty logs now display feedback ([998da4d](https://github.com/laravel/forge-cli/commit/998da4d145c83b89bdef01c838391adc7c8c9fb6))

## [v0.1.3 (2021-08-03)](https://github.com/laravel/forge-cli/compare/v0.1.2...v0.1.3)

### Fixed

- Commands based on `tail` operations failing ([286d58b](https://github.com/laravel/forge-cli/commit/286d58b38f78c2cb429d2bc83892bf024be01c83))

## [v0.1.2 (2021-08-02)](https://github.com/laravel/forge-cli/compare/v0.1.1...v0.1.2)

### Added

- Improvements on `site:logs` and `daemon:logs` commands ([b1ca9ce](https://github.com/laravel/forge-cli/commit/b1ca9ce90a318c28d0a8423396ffd6b19025c68c))
- Warns users when not using the latest version ([83f89bf](https://github.com/laravel/forge-cli/commit/83f89bf615f3f71b4f2c1f8231835ea5f451e08a), [7e39417](https://github.com/laravel/forge-cli/commit/7e39417b713867bc060715a03b535843c69e67ad))

### Fixed

- Grammar mistakes ([639b49a](https://github.com/laravel/forge-cli/commit/639b49a56e0b6238f84a569c63bd55ffb025d876), [51eb4b3](https://github.com/laravel/forge-cli/commit/51eb4b3fff921c64e0e693c7370c552726dceff1), [f4d41c2](https://github.com/laravel/forge-cli/commit/f4d41c2d67939f42566ab11b446a7e51a7e836ce), [a26cadd](https://github.com/laravel/forge-cli/commit/a26cadddb14f305b36a9cd0594c1ae6f2bc1e4bc))
- Wrong SSH sockets path ([baf6ce0](https://github.com/laravel/forge-cli/commit/baf6ce05bfb9471631736ffd81d1d2809d47e206))

## [v0.1.1 (2021-08-01)](https://github.com/laravel/forge-cli/compare/v0.1.0...v0.1.1)

### Added

- Adds `database:shell` command ([e93c38e](https://github.com/laravel/forge-cli/commit/e93c38e7f5cdcc6e41b9a0b477574e1caf3d581d))

## v0.1.0 (2021-07-30)

Initial pre-release.
