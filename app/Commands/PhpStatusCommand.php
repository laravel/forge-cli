<?php

namespace App\Commands;

class PhpStatusCommand extends Command
{
    use Concerns\InteractsWithPhp;

    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = 'php:status {--type=}';

    /**
     * The description of the command.
     *
     * @var string
     */
    protected $description = 'Get the current status of PHP';

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
            abort(1, 'PHP version needs to be one of those values: '.implode(', ', $versions).'.');
        }

        $version = $version ?: number_format(substr($server->phpVersion, -2) / 10, 1, '.', '');

        $status = $this->serviceStatus($server, 'php'.$version.'-fpm');

        $this->info('PHP '.$version.' service is '.$status.'.');
    }
}
