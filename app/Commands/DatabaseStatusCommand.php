<?php

namespace App\Commands;

class DatabaseStatusCommand extends Command
{
    use Concerns\InteractsWithDatabase;

    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = 'database:status';

    /**
     * The description of the command.
     *
     * @var string
     */
    protected $description = 'Get the current status of the database';

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

        if (in_array($databaseType, ['mysql', 'mysql8', 'mariadb'])) {
            $this->ensureServiceIsRunning($server, 'mysql');
        } elseif (in_array($databaseType, ['postgres', 'postgres13'])) {
            $this->ensureServiceIsRunning($server, 'postgres');
        } else {
            abort(1, 'Checking the status of ['.$databaseType.'] databases is not supported.');
        }

        $this->successfulStep('The database is up and running');
    }
}
