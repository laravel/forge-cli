<?php

namespace App\Commands;

use App\Support\PhpVersion;

class PhpStatusCommand extends Command
{
    use Concerns\InteractsWithPhp;

    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = 'php:status {version? : The PHP Version}';

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

        $version = $this->argument('version');
        $versions = ['5.6', '7.0', '7.1', '7.2', '7.3', '7.4', '8.0'];

        if (! is_null($version) && ! in_array($version, $versions)) {
            abort(1, 'PHP version needs to be one of those values: '.implode(', ', $versions).'.');
        }

        $version = $version ?: PhpVersion::of($server->phpVersion)->release();

        $this->ensureServiceIsRunning($server, 'php'.$version.'-fpm');

        $this->successfulStep('PHP '.$version.' is up & running');
    }
}
