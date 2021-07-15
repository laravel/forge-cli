<?php

namespace App\Commands;

use App\Exceptions\LogicException;

class DatabaseLogsCommand extends Command
{
    use Concerns\InteractsWithLogs, Concerns\InteractsWithDatabase;

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

        if (! in_array($databaseType, ['mysql', 'mysql8', 'postgres'])) {
            throw new LogicException('Retrieving logs from ['.$databaseType.'] databases is not supported.');
        }

        $this->showLogs($server, 'database');
    }
}
