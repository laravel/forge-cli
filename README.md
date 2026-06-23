<p align="center">
    <img src="/art/readme.png" alt="Logo Laravel Forge CLI preview" style="width:70%;">
</p>

<p align="center">
<a href="https://github.com/laravel/forge-cli/actions"><img src="https://github.com/laravel/forge-cli/workflows/tests/badge.svg" alt="Build Status"></a>
<a href="https://packagist.org/packages/laravel/forge-cli"><img src="https://img.shields.io/packagist/dt/laravel/forge-cli" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/laravel/forge-cli"><img src="https://img.shields.io/packagist/v/laravel/forge-cli" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/laravel/forge-cli"><img src="https://img.shields.io/packagist/l/laravel/forge-cli" alt="License"></a>
</p>

## Introduction

[Laravel Forge](https://forge.laravel.com) is a server management and site deployment service. After connecting to your preferred server provider, Forge will provision a new server, installing and configuring: PHP, Nginx, MySQL, and more.

In addition, Forge can assist you in managing scheduled jobs, queue workers, SSL certificates, and more. After a server has provisioned, you can then deploy your PHP / Laravel applications or WordPress applications using the Forge UI dashboard or the Forge CLI.

## Official Documentation

Documentation for Forge CLI can be found on the [Laravel Forge website](https://forge.laravel.com/docs/1.0/cli.html).

## Project Configuration

You can create a `.forge` configuration file in your project directory to define default server and site settings. This eliminates the need to specify server/site IDs for every command.

### Quick Setup

```bash
cd /path/to/your/project
forge init
```

The `init` command will interactively guide you through setting up environments for your project.

### Configuration Format

The `.forge` file supports named environments:

```json
{
    "default": "staging",
    "environments": {
        "production": {
            "server": 123456,
            "site": 789012,
            "confirm": true
        },
        "staging": {
            "server": 123456,
            "site": 789013
        }
    }
}
```

### Deploying

With a `.forge` file configured, deployments become simple:

```bash
forge deploy              # Deploy to default environment
forge deploy staging      # Deploy to staging
forge deploy production   # Deploy to production (prompts for confirmation)
forge deploy --force      # Skip confirmation prompt
```

The `confirm` option requires user confirmation before deploying, helping prevent accidental production deployments.

### Configuration Commands

```bash
forge config              # Display current configuration
forge config:set          # Add or update an environment (interactive)
forge config:set prod 123 456 --confirm  # Quick add with IDs
forge config:remove dev   # Remove an environment
forge config:default staging  # Set default environment
```

### Shell Completion

Enable tab completion for commands and environments:

```bash
forge completion --install  # Add to ~/.zshrc
source ~/.zshrc
```

## Contributing

Thank you for considering contributing to Forge CLI! You can read the contribution guide [here](.github/CONTRIBUTING.md).

## Code of Conduct

In order to ensure that the Laravel community is welcoming to all, please review and abide by the [Code of Conduct](https://laravel.com/docs/contributions#code-of-conduct).

## Security Vulnerabilities

Please review [our security policy](https://github.com/laravel/forge-cli/security/policy) on how to report security vulnerabilities.

## License

Forge CLI is open-sourced software licensed under the [MIT license](LICENSE.md).
