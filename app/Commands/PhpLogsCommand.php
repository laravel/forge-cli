<?php

namespace App\Commands;

use App\Support\PhpVersion;

use function Laravel\Prompts\spin;

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
        $versions = PhpVersion::VERSIONS;

        abort_if(
            ! is_null($version) && ! in_array($version, $versions),
            1,
            'PHP version needs to be one of these values: '.implode(', ', $versions).'.'
        );

        $server = $this->currentServer();
        $serverPhpVersion = $server->phpVersion;

        $version = PhpVersion::of($version ?: $serverPhpVersion);

        $logs = spin(
            fn () => $this->forge->serverLog(
                $this->currentOrganization(),
                $server->id,
                $version->forgeKey(),
            ),
            'Retrieving PHP logs',
        );

        abort_if(empty($logs), 1, 'The requested logs could not be found or they are empty.');

        $this->newLine();

        $this->displayLogs($logs);

        $this->newLine();
    }
}
