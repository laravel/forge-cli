<?php

namespace App\Commands;

use App\Support\PhpVersion;
use Laravel\Forge\Resources\Server;

use function Laravel\Prompts\confirm;
use function Laravel\Prompts\info;
use function Laravel\Prompts\spin;

class PhpRestartCommand extends Command
{
    use Concerns\InteractsWithPhp;

    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = 'php:restart {version? : The PHP version}';

    /**
     * The description of the command.
     *
     * @var string
     */
    protected $description = 'Restart PHP';

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

        if ($this->restartPhp($server, $version)) {
            info('PHP '.$version->release().' restart initiated successfully.');
        }
    }

    /**
     * Restarts PHP service.
     *
     * @return bool
     */
    public function restartPhp(Server $server, PhpVersion $version)
    {
        if ($restarting = confirm('The sites may become unavailable while the PHP '.$version->release().' service restarts. Continue?')) {
            spin(
                fn () => $server->rebootPHP($version->forgeKey()),
                'Restarting PHP '.$version->release(),
            );
        }

        return $restarting;
    }
}
