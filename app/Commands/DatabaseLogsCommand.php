<?php

namespace App\Commands;

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

        // @phpstan-ignore-next-line
        $databaseType = $this->currentServer()->databaseType;

        if (! in_array($databaseType, ['mysql', 'mysql8', 'postgres'])) {
            abort(1, 'Retrieving logs from ['.$databaseType.'] databases is not supported.');
        }

        $this->showLogs('database');
    }
}
