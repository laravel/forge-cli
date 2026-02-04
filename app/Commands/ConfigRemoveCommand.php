<?php

namespace App\Commands;

use App\Repositories\LocalConfigRepository;

class ConfigRemoveCommand extends Command
{
    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = 'config:remove
        {name : Environment name to remove}
        {--force : Skip confirmation}';

    /**
     * The description of the command.
     *
     * @var string
     */
    protected $description = 'Remove an environment from the .forge config';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        if (! $this->localConfig->exists()) {
            $this->error('No .forge config file found.');
            return 1;
        }

        $name = strtolower($this->argument('name'));
        $config = $this->localConfig->all();

        if (!isset($config['environments'][$name])) {
            $this->error("Environment '{$name}' not found.");
            $available = array_keys($config['environments'] ?? []);
            if (!empty($available)) {
                $this->line('  Available: ' . implode(', ', $available));
            }
            return 1;
        }

        // Confirm removal
        if (! $this->option('force')) {
            if (! $this->confirmStep(["Remove environment %s?", $name])) {
                return 0;
            }
        }

        // Remove the environment
        unset($config['environments'][$name]);

        // Handle default if we removed it
        if ($config['default'] === $name) {
            $remaining = array_keys($config['environments']);
            if (!empty($remaining)) {
                $config['default'] = $remaining[0];
                $this->warnStep(['Default changed to %s', $config['default']]);
            } else {
                unset($config['default']);
            }
        }

        // If no environments left, delete the file
        if (empty($config['environments'])) {
            $path = $this->localConfig->getFoundPath();
            unlink($path);
            $this->successfulStep('Removed last environment. Config file deleted.');
            return 0;
        }

        $this->localConfig->create(getcwd(), $config);
        $this->successfulStep(['Removed environment: %s', $name]);

        return 0;
    }
}
