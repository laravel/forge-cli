<?php

namespace App\Commands;

use function Laravel\Prompts\spin;

class DatabaseLogsCommand extends Command
{
    use Concerns\InteractsWithDatabase, Concerns\InteractsWithLogs;

    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = 'database:logs';

    /**
     * The description of the command.
     *
     * @var string
     */
    protected $description = 'Retrieve the latest database log messages';

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle()
    {
        $this->ensureDatabaseExists();

        $server = $this->currentServer();

        // @phpstan-ignore-next-line
        $databaseType = $server->databaseType;
        $logKey = $this->databaseLogKey($databaseType);

        if (is_null($logKey)) {
            abort(1, 'Retrieving logs from ['.$databaseType.'] databases is not supported.');
        }

        $logs = spin(
            fn () => $this->forge->serverLog(
                $this->currentOrganization(),
                $server->id,
                $logKey,
            ),
            'Retrieving database logs',
        );

        abort_if(empty($logs), 1, 'The requested logs could not be found or they are empty.');

        $this->newLine();

        $this->displayLogs($logs);

        $this->newLine();
    }
}
