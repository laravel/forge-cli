<?php

namespace App\Commands;

use App\Exceptions\LogicException;
use App\Exceptions\NotFoundException;

class DatabaseStatusCommand extends Command
{
    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = 'database:status {--id= : The ID of the database}';

    /**
     * The description of the command.
     *
     * @var string
     */
    protected $description = 'Get the current status of a database';

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle()
    {
        $server = $this->currentServer();

        // @phpstan-ignore-next-line
        $databaseType = $server->databaseType;

        if (is_null($databaseType)) {
            throw new NotFoundException('No database available.');
        }

        if (in_array($databaseType, ['mysql', 'mysql8', 'mariadb'])) {
            $status = $this->serviceStatus($server, 'mysql');
        } elseif (in_array($databaseType, ['postgres', 'postgres13'])) {
            $status = $this->serviceStatus($server, 'postgres');
        } else {
            throw new LogicException('Checking the status of ['.$databaseType.'] databases is not supported.');
        }

        $this->info('The database is '.$status.'.');
    }
}
