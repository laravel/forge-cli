<?php

namespace App\Commands\Concerns;

trait InteractsWithEnvironments
{
    /**
     * The resolved environment for this command execution.
     *
     * @var array|null
     */
    protected $resolvedEnvironment = null;

    /**
     * Get the environment name from command input.
     *
     * Checks for --environment/-e option first, then 'environment' argument.
     *
     * @return string|null
     */
    protected function getEnvironmentFromInput()
    {
        // Check for --environment/-e option
        if ($this->hasOption('environment') && $this->option('environment')) {
            return $this->option('environment');
        }

        // Check for environment argument
        if ($this->hasArgument('environment') && $this->argument('environment')) {
            return $this->argument('environment');
        }

        return null;
    }

    /**
     * Resolve and cache the environment configuration.
     *
     * @return array|null Returns ['name' => string|null, 'config' => array] or null
     */
    protected function resolveEnvironmentConfig()
    {
        if ($this->resolvedEnvironment !== null) {
            return $this->resolvedEnvironment;
        }

        $specified = $this->getEnvironmentFromInput();
        $resolved = $this->localConfig->resolveEnvironment($specified);

        // Validate that specified environment exists
        if ($specified !== null && $resolved === null) {
            $available = $this->localConfig->getEnvironmentNames();

            if (empty($available)) {
                abort(1, "Environment '{$specified}' specified but no environments are configured in .forge");
            }

            abort(1, "Unknown environment '{$specified}'. Available: " . implode(', ', $available));
        }

        $this->resolvedEnvironment = $resolved;

        return $this->resolvedEnvironment;
    }

    /**
     * Check if confirmation is required and prompt the user.
     *
     * @param  string  $action  Description of the action (e.g., "deploy", "push environment")
     * @return bool  True if confirmed or no confirmation needed, false if user declined
     */
    protected function confirmEnvironmentAction($action)
    {
        // Check for --force flag to bypass confirmation
        if ($this->hasOption('force') && $this->option('force')) {
            return true;
        }

        $env = $this->resolveEnvironmentConfig();

        if ($env === null) {
            return true; // No local config, proceed normally
        }

        $requiresConfirmation = $env['config']['confirm'] ?? false;

        if (!$requiresConfirmation) {
            return true;
        }

        $envName = $env['name'] ?? 'this environment';
        $envDisplay = strtoupper($envName);

        $this->line('');
        $this->line("  <fg=red;options=bold>WARNING: You are about to {$action} to {$envDisplay}</>");
        $this->line('');

        return $this->confirmStep(
            ["Are you sure you want to {$action} to %s?", $envDisplay],
            false
        );
    }

    /**
     * Get the server ID from the resolved environment.
     *
     * @return int|string|null
     */
    protected function getEnvironmentServerId()
    {
        $env = $this->resolveEnvironmentConfig();

        return $env['config']['server'] ?? null;
    }

    /**
     * Get the site ID from the resolved environment.
     *
     * @return int|string|null
     */
    protected function getEnvironmentSiteId()
    {
        $env = $this->resolveEnvironmentConfig();

        return $env['config']['site'] ?? null;
    }

    /**
     * Get the resolved environment name.
     *
     * @return string|null
     */
    protected function getEnvironmentName()
    {
        $env = $this->resolveEnvironmentConfig();

        return $env['name'] ?? null;
    }

    /**
     * Check if we're using a local environment configuration.
     *
     * @return bool
     */
    protected function hasEnvironmentConfig()
    {
        return $this->resolveEnvironmentConfig() !== null;
    }
}
