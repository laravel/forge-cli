<p align="center"><img src="/art/readme.png" alt="Logo Laravel Forge CLI" style="width:70%;"></p>

<p align="center">
<a href="https://github.com/laravel/forge-cli/actions"><img src="https://github.com/laravel/forge-cli/workflows/tests/badge.svg" alt="Build Status"></a>
<a href="https://packagist.org/packages/laravel/forge-cli"><img src="https://img.shields.io/packagist/dt/laravel/forge-cli" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/laravel/forge-cli"><img src="https://img.shields.io/packagist/v/laravel/forge-cli" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/laravel/forge-cli"><img src="https://img.shields.io/packagist/l/laravel/forge-cli" alt="License"></a>
</p>

## Introduction

Laravel Forge is a server management and site deployment service. After connecting to your preferred server provider, Forge will provision a new server, installing and configuring: PHP, Nginx, MySQL, and more.

In addition, Forge can assist you in managing scheduled jobs, queue workers, SSL certificates, and more. After a server has provisioned, you can then deploy your PHP / Laravel applications or WordPress applications using the Forge UI dashboard or the Forge CLI.

This repository contains the CLI client for interacting with Laravel Forge. To learn more about Forge and how to use this client, please consult the **[official documentation](https://forge.laravel.com/docs)**.

## Usage

> ⚠️ Forge CLI is under development - use at your own risk.

You may install the Forge CLI as a global Composer dependency:

````
composer global config repositories.forge-cli vcs https://github.com/laravel/forge-cli
composer global require laravel/forge-cli
```

### Logging In

You will need to generate an API token to interact with the Forge CLI. Tokens are used to authenticate your account without providing personal details. API tokens can be created from [Forge's API dashboard](https://forge.laravel.com/user/profile#/api).

After you have generated an API token, you should authenticate with your Forge account using the `login` command:

```bash
forge login
```

### SSH Key based secure authentication

Before you perform any tasks using Forge CLI, you should ensure you have configured SSH Key based secure authentication to your servers.

You may test that SSH is configured by running the `ssh:test` command:

```bash
forge ssh:test
```

## Security Vulnerabilities

Please review [our security policy](https://github.com/laravel/forge-cli/security/policy) on how to report security vulnerabilities.

## License

Forge CLI is open-sourced software licensed under the [MIT license](LICENSE.md).
