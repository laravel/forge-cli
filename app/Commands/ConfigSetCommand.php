<?php

namespace App\Commands;

use App\Repositories\LocalConfigRepository;

class ConfigSetCommand extends Command
{
    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = 'config:set
        {name? : Environment name (e.g., production, staging)}
        {server? : Server ID}
        {site? : Site ID}
        {--confirm : Require confirmation before deploying}
        {--no-confirm : Disable confirmation requirement}';

    /**
     * The description of the command.
     *
     * @var string
     */
    protected $description = 'Add or update an environment in the .forge config';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $name = $this->argument('name');
        $serverId = $this->argument('server');
        $siteId = $this->argument('site');

        // Interactive mode if no name provided
        if (! $name) {
            return $this->handleInteractive();
        }

        $name = strtolower($name);
        $config = $this->localConfig->all();

        // Determine if we're updating or creating
        $isUpdate = isset($config['environments'][$name]) ||
                    (!isset($config['environments']) && !empty($config['server']));

        // Get existing environment config if updating
        $envConfig = $config['environments'][$name] ?? [];

        // Update server if provided
        if ($serverId) {
            $envConfig['server'] = (int) $serverId;
        }

        // Update site if provided
        if ($siteId) {
            $envConfig['site'] = (int) $siteId;
        }

        // Handle confirm flag
        if ($this->option('confirm')) {
            $envConfig['confirm'] = true;
        } elseif ($this->option('no-confirm')) {
            unset($envConfig['confirm']);
        }

        // Validate we have at least a server
        if (empty($envConfig['server']) && !$serverId) {
            $this->error('Server ID is required. Usage: forge config:set <name> <server-id> [site-id]');
            $this->line('  Or run <comment>forge config:set</comment> without arguments for interactive mode.');
            return 1;
        }

        return $this->saveEnvironment($name, $envConfig, $config, $isUpdate);
    }

    /**
     * Handle interactive mode.
     *
     * @return int
     */
    protected function handleInteractive()
    {
        $this->step('Add/update environment');
        $this->line('');

        // Get environment name
        $name = $this->askStep('Environment name (e.g., production, staging)');

        if (empty($name)) {
            $this->error('Environment name is required.');
            return 1;
        }

        $name = strtolower(trim($name));
        $config = $this->localConfig->all();

        $isUpdate = isset($config['environments'][$name]);

        if ($isUpdate) {
            $this->warnStep(['Updating existing environment: %s', $name]);
        }

        $this->line('');

        // Select server (interactive with lookup)
        $serverId = $this->askForServer("Which server for '{$name}'");
        $server = $this->forge->server($serverId);

        // Get sites for selected server
        $sites = collect($this->forge->sites($server->id));

        $envConfig = [
            'server' => $server->id,
        ];

        if ($sites->isNotEmpty()) {
            $siteId = $this->choiceStep(
                "Which site for '{$name}'",
                $sites->mapWithKeys(fn ($site) => [$site->id => $site->name])->all()
            );
            $envConfig['site'] = $siteId;
        } else {
            $this->warnStep('No sites found on this server.');
        }

        // Ask about confirmation
        $isProduction = in_array($name, ['production', 'prod', 'live', 'main', 'master']);

        if ($this->confirmStep(["Require confirmation before deploying to %s?", $name], $isProduction)) {
            $envConfig['confirm'] = true;
        }

        return $this->saveEnvironment($name, $envConfig, $config, $isUpdate);
    }

    /**
     * Save the environment config.
     *
     * @param  string  $name
     * @param  array  $envConfig
     * @param  array  $config
     * @param  bool  $isUpdate
     * @return int
     */
    protected function saveEnvironment($name, $envConfig, $config, $isUpdate)
    {
        // Build new config structure
        if (!isset($config['environments'])) {
            // Convert simple config to environment-based or create fresh
            $newConfig = [
                'default' => $name,
                'environments' => [
                    $name => $envConfig,
                ],
            ];

            // If there was an existing simple config, preserve it
            if (!empty($config['server'])) {
                $existingName = $this->askStep('Existing simple config found. Name for it?', 'legacy');
                if ($existingName && $existingName !== $name) {
                    $newConfig['environments'][$existingName] = [
                        'server' => $config['server'],
                        'site' => $config['site'] ?? null,
                        'confirm' => $config['confirm'] ?? false,
                    ];
                }
            }

            $config = $newConfig;
        } else {
            // Update existing environments config
            $config['environments'][$name] = $envConfig;

            // If this is the first environment, set it as default
            if (empty($config['default'])) {
                $config['default'] = $name;
            }
        }

        // Clean up null values
        if (isset($config['environments'][$name]['site']) && $config['environments'][$name]['site'] === null) {
            unset($config['environments'][$name]['site']);
        }

        // Write config
        $this->localConfig->create(getcwd(), $config);

        $action = $isUpdate ? 'Updated' : 'Added';
        $this->successfulStep(["{$action} environment: %s", $name]);

        // Show summary
        $env = $config['environments'][$name];
        $this->line('');
        $this->line("  <comment>Server:</comment>  {$env['server']}");
        if (isset($env['site'])) {
            $this->line("  <comment>Site:</comment>    {$env['site']}");
        }
        $this->line('  <comment>Confirm:</comment> ' . (!empty($env['confirm']) ? 'yes' : 'no'));

        return 0;
    }
}
