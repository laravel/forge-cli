<?php

namespace App\Commands;

use App\Exceptions\LogicException;

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
            $status = $this->serviceStatus($server, 'mysql');
        } elseif (in_array($databaseType, ['postgres', 'postgres13'])) {
            $status = $this->serviceStatus($server, 'postgres');
        } else {
            throw new LogicException('Checking the status of ['.$databaseType.'] databases is not supported.');
        }

        $this->info('Database service is '.$status.'.');
    }
}
