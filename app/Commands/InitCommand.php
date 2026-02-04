<?php

namespace App\Commands;

use App\Repositories\LocalConfigRepository;

class InitCommand extends Command
{
    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = 'init
        {--force : Overwrite existing .forge file}
        {--simple : Create a simple config without named environments}';

    /**
     * The description of the command.
     *
     * @var string
     */
    protected $description = 'Initialize a .forge config file in the current directory';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $configFile = getcwd() . DIRECTORY_SEPARATOR . LocalConfigRepository::CONFIG_FILE;

        if (file_exists($configFile) && ! $this->option('force')) {
            if (! $this->confirmStep('A .forge file already exists. Would you like to overwrite it?')) {
                return 0;
            }
        }

        $this->step('Setting up local Forge configuration');
        $this->line('');

        if ($this->option('simple')) {
            return $this->createSimpleConfig();
        }

        return $this->createEnvironmentConfig();
    }

    /**
     * Create a simple config without named environments.
     *
     * @return int
     */
    protected function createSimpleConfig()
    {
        $serverId = $this->askForServer('Which server should this project use');
        $server = $this->forge->server($serverId);

        $sites = collect($this->forge->sites($server->id));

        $config = ['server' => $server->id];

        if ($sites->isNotEmpty()) {
            $siteId = $this->choiceStep(
                'Which site should this project use',
                $sites->mapWithKeys(fn ($site) => [$site->id => $site->name])->all()
            );
            $config['site'] = $siteId;

            if ($this->confirmStep('Require confirmation before deploying?', true)) {
                $config['confirm'] = true;
            }
        }

        $this->writeConfig($config);

        return 0;
    }

    /**
     * Create a config with named environments.
     *
     * @return int
     */
    protected function createEnvironmentConfig()
    {
        $environments = [];
        $addMore = true;

        while ($addMore) {
            $env = $this->configureEnvironment(count($environments) === 0);

            if ($env) {
                $environments[$env['name']] = $env['config'];
                $this->successfulStep(['Added environment: %s', $env['name']]);
                $this->line('');
            }

            if (count($environments) > 0) {
                $addMore = $this->confirmStep('Add another environment?', count($environments) < 2);
            }
        }

        if (empty($environments)) {
            $this->warnStep('No environments configured. Aborting.');
            return 1;
        }

        // Determine default environment
        $envNames = array_keys($environments);

        if (count($envNames) === 1) {
            $default = $envNames[0];
        } else {
            // Suggest safest option as default (one without confirm, or staging, or first)
            $suggestedDefault = $this->suggestSafeDefault($environments);

            $default = $this->choiceStep(
                'Which environment should be the default',
                array_combine($envNames, $envNames),
                $suggestedDefault
            );
            // choiceStep returns int index, need to map back to name
            $default = $envNames[array_search($default, array_values(array_combine($envNames, $envNames)))] ?? $envNames[0];
        }

        $config = [
            'default' => $default,
            'environments' => $environments,
        ];

        $this->writeConfig($config);

        return 0;
    }

    /**
     * Configure a single environment.
     *
     * @param  bool  $isFirst
     * @return array|null
     */
    protected function configureEnvironment($isFirst)
    {
        // Suggest common environment names
        $suggestedNames = ['production', 'staging', 'development', 'local'];
        $prompt = $isFirst ? 'Environment name (e.g., production, staging)' : 'Environment name';

        $name = $this->askStep($prompt, $isFirst ? 'production' : null);

        if (empty($name)) {
            return null;
        }

        $name = strtolower(trim($name));

        $this->line('');
        $this->step(['Configuring %s environment', $name]);

        // Select server
        $serverId = $this->askForServer("Which server for '{$name}'");
        $server = $this->forge->server($serverId);

        // Get sites for selected server
        $sites = collect($this->forge->sites($server->id));

        $envConfig = ['server' => $server->id];

        if ($sites->isNotEmpty()) {
            $siteId = $this->choiceStep(
                "Which site for '{$name}'",
                $sites->mapWithKeys(fn ($site) => [$site->id => $site->name])->all()
            );
            $envConfig['site'] = $siteId;
        }

        // Ask about confirmation - default to true for production-like names
        $isProduction = in_array($name, ['production', 'prod', 'live', 'main', 'master']);
        $confirmDefault = $isProduction;

        if ($this->confirmStep(["Require confirmation before deploying to %s?", $name], $confirmDefault)) {
            $envConfig['confirm'] = true;
        }

        return [
            'name' => $name,
            'config' => $envConfig,
        ];
    }

    /**
     * Suggest the safest default environment.
     *
     * @param  array  $environments
     * @return string|null
     */
    protected function suggestSafeDefault($environments)
    {
        // Prefer non-production environments as default
        $safeNames = ['staging', 'development', 'dev', 'local', 'test'];

        foreach ($safeNames as $safe) {
            if (isset($environments[$safe])) {
                return $safe;
            }
        }

        // Otherwise prefer one without confirm flag
        foreach ($environments as $name => $config) {
            if (empty($config['confirm'])) {
                return $name;
            }
        }

        return array_key_first($environments);
    }

    /**
     * Write the config file and display summary.
     *
     * @param  array  $config
     * @return void
     */
    protected function writeConfig(array $config)
    {
        $this->localConfig->create(getcwd(), $config);

        $this->line('');
        $this->successfulStep(['Created %s', LocalConfigRepository::CONFIG_FILE]);
        $this->line('');

        // Display summary
        if (isset($config['environments'])) {
            $this->line('  <comment>Environments:</comment>');
            foreach ($config['environments'] as $name => $env) {
                $default = ($name === $config['default']) ? ' <fg=green>(default)</>' : '';
                $confirm = !empty($env['confirm']) ? ' <fg=yellow>[confirm]</>' : '';
                $this->line("    - {$name}{$default}{$confirm}");
            }
            $this->line('');
            $this->step('Usage:');
            $this->line('  <comment>forge deploy</comment>            Deploy to default environment');
            $this->line('  <comment>forge deploy staging</comment>    Deploy to specific environment');
            $this->line('  <comment>forge deploy --force</comment>    Skip confirmation prompt');
        } else {
            $this->line('  <comment>Server:</comment> ' . $config['server']);
            if (isset($config['site'])) {
                $this->line('  <comment>Site:</comment>   ' . $config['site']);
            }
            if (!empty($config['confirm'])) {
                $this->line('  <comment>Confirm:</comment> Yes');
            }
            $this->line('');
            $this->step('You can now run <comment>forge deploy</comment> from this directory');
        }
    }
}
