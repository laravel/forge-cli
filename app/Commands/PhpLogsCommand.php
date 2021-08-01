<?php

namespace App\Commands;

use App\Support\PhpVersion;

class PhpLogsCommand extends Command
{
    use Concerns\InteractsWithLogs,
        Concerns\InteractsWithPhp;

    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = 'php:logs {version? : The PHP version}';

    /**
     * The description of the command.
     *
     * @var string
     */
    protected $description = 'Retrieve the latest PHP log messages';

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle()
    {
        $this->ensurePhpExists();

        $version = $this->argument('version');
        $versions = ['5.6', '7.0', '7.1', '7.2', '7.3', '7.4', '8.0'];

        abort_if(
            ! is_null($version) && ! in_array($version, $versions),
            1,
            'PHP version needs to be one of these values: '.implode(', ', $versions).'.'
        );

        $serverPhpVersion = $this->currentServer()->phpVersion;

        $version = $version ?: PhpVersion::of($serverPhpVersion)->release();

        $this->showLogs('php'.str_replace('.', '', $version));
    }
}
