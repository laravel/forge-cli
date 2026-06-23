<?php

namespace App\Commands;

use App\Repositories\LocalConfigRepository;

class ConfigCommand extends Command
{
    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = 'config';

    /**
     * The description of the command.
     *
     * @var string
     */
    protected $description = 'Display the local .forge configuration';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        if (! $this->localConfig->exists()) {
            $this->warnStep('No .forge config file found in this directory or parents.');
            $this->line('');
            $this->line('  Run <comment>forge init</comment> to create one, or use:');
            $this->line('    <comment>forge config:set <name> <server-id> <site-id></comment>');
            $this->line('');
            return 1;
        }

        $path = $this->localConfig->getFoundPath();
        $config = $this->localConfig->all();

        $this->step(['Config: %s', $path]);
        $this->line('');

        if (isset($config['environments'])) {
            $this->displayEnvironmentConfig($config);
        } else {
            $this->displaySimpleConfig($config);
        }

        $this->line('');
        $this->line('  <fg=gray>Commands:</>');
        $this->line('    <comment>forge config:set <name> <server> <site></comment>  Add/update environment');
        $this->line('    <comment>forge config:set <name> --confirm</comment>        Toggle confirmation');
        $this->line('    <comment>forge config:remove <name></comment>               Remove environment');
        $this->line('    <comment>forge config:default <name></comment>              Set default environment');

        return 0;
    }

    /**
     * Display environment-based config.
     *
     * @param  array  $config
     * @return void
     */
    protected function displayEnvironmentConfig(array $config)
    {
        $default = $config['default'] ?? null;

        $rows = [];
        foreach ($config['environments'] as $name => $env) {
            $isDefault = $name === $default;
            $rows[] = [
                $isDefault ? "<fg=green>{$name}</>" : $name,
                $env['server'] ?? '-',
                $env['site'] ?? '-',
                !empty($env['confirm']) ? '<fg=yellow>yes</>' : 'no',
                $isDefault ? '<fg=green>*</>' : '',
            ];
        }

        $this->table(
            ['Environment', 'Server', 'Site', 'Confirm', 'Default'],
            $rows
        );
    }

    /**
     * Display simple config.
     *
     * @param  array  $config
     * @return void
     */
    protected function displaySimpleConfig(array $config)
    {
        $this->line('  <comment>Server:</comment>  ' . ($config['server'] ?? '-'));
        $this->line('  <comment>Site:</comment>    ' . ($config['site'] ?? '-'));
        $this->line('  <comment>Confirm:</comment> ' . (!empty($config['confirm']) ? '<fg=yellow>yes</>' : 'no'));
    }
}
