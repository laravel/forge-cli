<?php

namespace App\Commands;

use App\Support\PhpVersion;
use Laravel\Forge\Exceptions\ValidationException;

class PhpRestartCommand extends Command
{
    use Concerns\InteractsWithPhp;

    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = 'php:restart {version? : The PHP Version}';

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
        $versions = ['5.6', '7.0', '7.1', '7.2', '7.3', '7.4', '8.0'];

        if (! is_null($version) && ! in_array($version, $versions)) {
            abort(1, 'PHP version needs to be one of those values: '.implode(', ', $versions).'.');
        }

        $version = $version ?: PhpVersion::of($server->phpVersion)->release();

        if ($this->restartPhp($server->id, $version)) {
            $this->successfulStep('PHP '.$version.' restart initiated successfully.');
        }
    }

    /**
     * Restarts PHP service.
     *
     * @param  string|int  $serverId
     * @param  string  $version
     * @return bool
     */
    public function restartPhp($serverId, $version)
    {
        if ($restarting = $this->confirm('While the <comment>[PHP '.$version.']</comment> service restarts, sites may become unavailable. Wish to proceed?')) {
            $this->step('Restarting PHP '.$version);

            try {
                $this->forge->rebootPHP($serverId, [
                    'version' => 'php'.str_replace('.', '', $version),
                ]);
            } catch (ValidationException $e) {
                abort(1, collect($e->errors())->first());
            }
        }

        return $restarting;
    }
}
