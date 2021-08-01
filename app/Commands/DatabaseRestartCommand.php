<?php

namespace App\Commands;

class DatabaseRestartCommand extends Command
{
    use Concerns\InteractsWithDatabase;

    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = 'database:restart';

    /**
     * The description of the command.
     *
     * @var string
     */
    protected $description = 'Restart the database';

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
            $restarting = $this->restartMysql($server->id);
        } elseif (in_array($databaseType, ['postgres', 'postgres13'])) {
            $restarting = $this->restartPostgres($server->id);
        } else {
            abort(1, 'Restarting ['.$databaseType.'] databases is not supported.');
        }

        if ($restarting) {
            $this->successfulStep('Database restart initiated successfully');
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
        if ($restarting = $this->confirm('The database may become unavailable while the <comment>[MySQL]</comment> service restarts. Continue?')) {
            $this->step('Restarting the database');

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
        if ($restarting = $this->confirm('The database may become unavailable while the <comment>[PostgreSQL]</comment> service restarts. Continue?')) {
            $this->step('Restarting the database');

            $this->forge->rebootPostgres($serverId);
        }

        return $restarting;
    }
}
