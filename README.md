<p align="center"><img src="/art/readme.png" alt="Logo Laravel Forge CLI" style="width:70%;"></p>

<p align="center">
<a href="https://github.com/laravel/forge-cli/actions"><img src="https://github.com/laravel/forge-cli/workflows/tests/badge.svg" alt="Build Status"></a>
<a href="https://packagist.org/packages/laravel/forge-cli"><img src="https://img.shields.io/packagist/dt/laravel/forge-cli" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/laravel/forge-cli"><img src="https://img.shields.io/packagist/v/laravel/forge-cli" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/laravel/forge-cli"><img src="https://img.shields.io/packagist/l/laravel/forge-cli" alt="License"></a>
</p>

## Introduction

> ⚠️ Forge CLI is under development - use at your own risk.

Laravel Forge is a server management and site deployment service. After connecting to your preferred server provider, Forge will provision a new server, installing and configuring: PHP, Nginx, MySQL, and more.

In addition, Forge can assist you in managing scheduled jobs, queue workers, SSL certificates, and more. After a server has provisioned, you can then deploy your PHP / Laravel applications or WordPress applications using the Forge UI dashboard or the Forge CLI.

This repository contains the CLI client for interacting with Laravel Forge. To learn more about Forge and how to use this client, please consult the **[official documentation](https://forge.laravel.com/docs)**.

## Usage

> **Requires [PHP 7.3+](https://php.net/releases/)**

You may install the Forge CLI as a global [Composer](https://getcomposer.org) dependency:

```bash
composer global require laravel/forge-cli
```

### Get Started

To view a list of all available Forge CLI commands, also view the current version of your installation, you may run `forge` from the command-line:

```bash
forge
```

### Logging In

You will need to generate an API token to interact with the Forge CLI. Tokens are used to authenticate your account without providing personal details. API tokens can be created from [Forge's API dashboard](https://forge.laravel.com/user/profile#/api).

After you have generated an API token, you should authenticate with your Forge account using the login command:

```bash
forge login
```

### Current Server & Switching Servers

When managing Forge resources via the CLI, you will need to be aware of your currently active server. You may view your current server using the `server:current` command:

```bash
forge server:current
```

To change your active server, you may use the `server:switch` command:

```bash
forge server:switch
forge server:switch staging
```

To view the list of all available servers, you may use the `server:list` command:

```bash
forge server:list
```

### SSH Key Authentication

Before performing any tasks using the Forge CLI, you should ensure that you have added an SSH key for the `forge` user to your servers so that you can securely connect to them. You may test that SSH is configured correctly by running the `ssh:test` command:

```bash
forge ssh:test
```

To configure SSH key authentication, you may use the `ssh:configure` command:

```bash
forge ssh:configure
forge ssh:configure --key=/path/to/public/key.pub --name=name-in-forge
```

After you have configured SSH key authentication, you may use the https://forge.laravel.com/docs/1.0/servers/recipes.html`ssh` command to create a secure connection to your server:

```bash
forge ssh
forge ssh staging
```

### Resources

As you may know, Forge provision servers with a variety of resources. And you may use Forge CLI to perform "common" actions on those resources.

#### Checking Resource Status

To check the current status of a resource, you may use the `{resource}:status` command:

```bash
forge daemon:status

forge database:status

forge nginx:status

forge php:status     # Visualize PHP logs (default server version)
forge php:status 8.0 # Visualize PHP 8.0 logs
```

#### Viewing Resources Logs

You may also view logs directly from the command-line. To do so, use the `{resource}:logs` command:

```bash
forge daemon:logs
forge daemon:logs --tail # Visualize logs in realtime

forge database:logs

forge nginx:logs         # Visualize error logs
forge nginx:logs access  # Visualize access logs

forge php:logs           # Visualize PHP logs (default server version)
forge php:logs 8.0       # Visualize PHP 8.0 logs
```

#### Restarting Resources

Resources may be "restarted" using the `{resource}:restart` command:

```bash
forge daemon:restart

forge database:restart

forge nginx:restart

forge php:restart     # Restarts PHP (default server version)
forge php:restart 8.0 # Restarts PHP 8.0
```

#### Connecting To Resources Locally

You may use the `{resource}:shell` command to quickly access a command line shell that lets you interact with your resource:

```bash
forge database:shell
forge database:shell my-database-name
forge database:shell my-database-name --user=my-user
```

### Sites

To view the list of all available sites, you may use the `site:list` command:

```bash
forge site:list
```

#### Viewing Application Logs

You may also view site logs directly from the command-line. To do so, use the `site:logs` command:

```bash
forge site:logs
forge site:logs example.com
```

#### Initiating Deployments

Of course, one of the primary features of Laravel Forge is deployments, and those may be initiated via the Forge CLI using the `deploy` command:

```bash
forge deploy
forge deploy example.com
```

#### Reviewing Deployments Output / Logs

When a deployment fails, you may review the output / logs via the Forge UI's deployment history screen. Of course, you may review the output at any time on the command-line using the `deploy:logs` command:

```
forge deploy:logs
forge deploy:logs 12345
```

#### Running Command

Sometimes, you may wish to run a "command" on a site. The command `command` will prompt you for the "command" you would like to run:

```
forge command
forge command example.com
forge command example.com --command="php artisan inspire"
```

#### Tinker

As you may know, all Laravel applications include "Tinker" by default. To enter the Tinker environment, run the `tinker` command:

```
forge tinker
forge tinker example.com
```

## Security Vulnerabilities

Please review [our security policy](https://github.com/laravel/forge-cli/security/policy) on how to report security vulnerabilities.

## License

Forge CLI is open-sourced software licensed under the [MIT license](LICENSE.md).
