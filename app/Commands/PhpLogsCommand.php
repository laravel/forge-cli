<?php

namespace App\Commands;

use App\Exceptions\LogicException;

class PhpLogsCommand extends Command
{
    use Concerns\InteractsWithLogs,
        Concerns\InteractsWithPhp;

    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = 'php:logs {--type=}';

    /**
     * The description of the command.
     *
     * @var string
     */
    protected $description = 'Retrieve php log messages';

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle()
    {
        $this->ensurePhpExists();

        $server = $this->currentServer();

        $version = $this->option('type');
        $versions = ['5.6', '7.0', '7.1', '7.2', '7.3', '7.4', '8.0'];

        if (! is_null($version) && ! in_array($version, $versions)) {
            throw new LogicException('PHP version needs to be one of those values: '.implode(', ', $versions).'.');
        }

        $version = $version ?: substr($server->phpVersion, -2);

        $this->showLogs($server, 'php'.str_replace('.', '', $version));
    }
}
