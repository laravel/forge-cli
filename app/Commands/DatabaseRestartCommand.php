<?php

namespace App\Commands;

use App\Exceptions\NotFoundException;
use App\Exceptions\LogicException;

class DatabaseRestartCommand extends Command
{
    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = 'database:restart {--id= : The ID of the database}';

    /**
     * The description of the command.
     *
     * @var string
     */
    protected $description = 'Restart a database';

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
            $restarting = $this->restartMysql($server->id);
        } elseif (in_array($databaseType, ['postgres', 'postgres13'])) {
            $restarting = $this->restartPostgres($server->id);
        } else {
            throw new LogicException('Restarting ['.$databaseType.'] databases is not supported.');
        }

        if ($restarting) {
            $this->info('Database restart initiated successfully.');
        }
    }

    /**
     * Restarts MySQL database service.
     *
     * @param  string|int $serverId
     * @return bool
     */
    public function restartMysql($serverId)
    {
        if ($restarting = $this->confirm('While the <comment>[MySQL]</comment> service restarts, the database will be unavailable. Wish to proceed?')) {
            $this->forge->rebootMysql($serverId);
        }

        return $restarting;
    }

    /**
     * Restarts PostgreSQL database service.
     *
     * @param  string|int $serverId
     * @return bool
     */
    public function restartPostgres($serverId)
    {
        if ($restarting = $this->confirm('While the <comment>[PostgreSQL]</comment> service restarts, the database will be unavailable. Wish to proceed?')) {
            $this->forge->rebootPostgres($serverId);
        }

        return $restarting;
    }
}
