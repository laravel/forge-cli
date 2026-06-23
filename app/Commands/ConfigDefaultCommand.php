<?php

namespace App\Commands;

use App\Repositories\LocalConfigRepository;

class ConfigDefaultCommand extends Command
{
    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = 'config:default
        {name : Environment name to set as default}';

    /**
     * The description of the command.
     *
     * @var string
     */
    protected $description = 'Set the default environment in the .forge config';

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

        if (!isset($config['environments'])) {
            $this->error('Config does not use named environments.');
            $this->line('  Run <comment>forge config:set <name> <server> <site></comment> first.');
            return 1;
        }

        if (!isset($config['environments'][$name])) {
            $this->error("Environment '{$name}' not found.");
            $available = array_keys($config['environments']);
            $this->line('  Available: ' . implode(', ', $available));
            return 1;
        }

        $config['default'] = $name;

        $this->localConfig->create(getcwd(), $config);
        $this->successfulStep(['Default environment set to: %s', $name]);

        return 0;
    }
}
