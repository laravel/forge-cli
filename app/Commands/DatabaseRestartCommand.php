<?php

namespace App\Commands;

use Laravel\Forge\Resources\Server;

use function Laravel\Prompts\confirm;
use function Laravel\Prompts\info;
use function Laravel\Prompts\spin;

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

        $databaseType = $server->databaseType;
        $engine = $this->databaseEngine($databaseType);

        if ($engine == 'mysql') {
            $restarting = $this->restartMysql($server);
        } elseif ($engine == 'postgres') {
            $restarting = $this->restartPostgres($server);
        } else {
            abort(1, 'Restarting ['.$databaseType.'] databases is not supported.');
        }

        if ($restarting) {
            info('Database restart initiated successfully.');
        }
    }

    /**
     * Restarts MySQL database service.
     *
     * @return bool
     */
    public function restartMysql(Server $server)
    {
        if ($restarting = confirm('The database may become unavailable while the MySQL service restarts. Continue?')) {
            spin(
                fn () => $server->rebootMysql(),
                'Restarting MySQL',
            );
        }

        return $restarting;
    }

    /**
     * Restarts PostgreSQL database service.
     *
     * @return bool
     */
    public function restartPostgres(Server $server)
    {
        if ($restarting = confirm('The database may become unavailable while the PostgreSQL service restarts. Continue?')) {
            spin(
                fn () => $server->rebootPostgres(),
                'Restarting PostgreSQL',
            );
        }

        return $restarting;
    }
}
