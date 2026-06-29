<?php

namespace App\Commands;

use function Laravel\Prompts\info;
use function Laravel\Prompts\spin;

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

        $databaseType = $server->databaseType;
        $engine = $this->databaseEngine($databaseType);

        if (is_null($engine)) {
            abort(1, 'Checking the status of ['.$databaseType.'] databases is not supported.');
        }

        spin(function () use ($engine) {
            [$exitCode] = $this->remote->exec(sprintf(
                'systemctl is-active --quiet %s',
                $engine,
            ));

            abort_if($exitCode != 0, 1, 'Service is not running.');
        }, 'Checking the service status');

        info('The database is up and running');
    }
}
