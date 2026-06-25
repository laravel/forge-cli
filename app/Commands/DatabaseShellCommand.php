<?php

namespace App\Commands;

use function Laravel\Prompts\info;
use function Laravel\Prompts\password;
use function Laravel\Prompts\spin;

class DatabaseShellCommand extends Command
{
    use Concerns\InteractsWithDatabase;

    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = 'database:shell {database? : The name of the database} {--user=forge : The username of the database user to connect as}';

    /**
     * The description of the command.
     *
     * @var string
     */
    protected $description = 'Start a database shell';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->ensureDatabaseExists();

        $server = $this->currentServer();

        // @phpstan-ignore-next-line
        $databaseType = $server->databaseType;
        $engine = $this->databaseEngine($databaseType);

        abort_if(is_null($engine), 1, 'Starting a ['.$databaseType.'] database shell is not supported.');

        $user = $this->option('user');

        $database = $this->argument('database') ?? optional(spin(
            fn () => collect($this->forge->databases($this->currentOrganization(), $server->id)->lazy())->first(),
            'Retrieving databases',
        ))->name;

        abort_if(is_null($database), 1, 'No databases found.');

        info('Establishing shell connection to '.$server->name.'@'.$database.' database');

        $password = password('Enter the password for database user '.$user);

        abort_if($password === '', 1, 'Password can not be empty.');

        if ($engine == 'mysql') {
            return $this->connectToMysql($server->id, $user, $password, $database);
        }

        return $this->connectToPostgres($server->id, $user, $password, $database);
    }

    /**
     * Connects the user the MySql instance.
     *
     * @param  string|int  $serverId
     * @param  string  $user
     * @param  string  $password
     * @param  string  $database
     * @return int
     */
    public function connectToMysql($serverId, $user, $password, $database)
    {
        return $this->remote->passthru(sprintf(
            'mysql -u %s -p%s %s', $user, $password, $database
        ));
    }

    /**
     * Connects the user the PostgreSQL instance.
     *
     * @param  string|int  $serverId
     * @param  string  $user
     * @param  string  $password
     * @param  string|null  $database
     * @return int
     */
    public function connectToPostgres($serverId, $user, $password, $database)
    {
        return $this->remote->passthru(sprintf(
            'PGPASSWORD=%s psql -U %s %s', $password, $user, $database
        ));
    }
}
