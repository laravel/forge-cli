<?php

namespace App\Commands;

use App\Support\PhpVersion;

use function Laravel\Prompts\info;
use function Laravel\Prompts\spin;

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

        $version = PhpVersion::of($version ?: $server->phpVersion);

        spin(function () use ($version) {
            [$exitCode] = $this->remote->exec(sprintf(
                'systemctl is-active --quiet %s',
                $version->serviceName(),
            ));

            abort_if($exitCode != 0, 1, 'Service is not running.');
        }, 'Checking the service status');

        info('PHP '.$version->release().' is up & running');
    }
}
