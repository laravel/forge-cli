# Release Notes

## [Unreleased](https://github.com/laravel/forge-cli/compare/v1.3.1...master)


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
