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
    protected $signature = 'php:status {version? : The PHP version}';

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
        $versions = PhpVersion::VERSIONS;

        if (! is_null($version) && ! in_array($version, $versions)) {
            abort(1, 'PHP version needs to be one of these values: '.implode(', ', $versions).'.');
        }

        $version = $version ?: PhpVersion::of($server->phpVersion)->release();

        $this->ensureServiceIsRunning($server, 'php'.$version.'-fpm');

        $this->successfulStep('PHP '.$version.' is up & running');
    }
}
