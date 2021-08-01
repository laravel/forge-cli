<?php

namespace App\Commands;

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

        $user = $this->option('user');

        $database = $this->argument('database') ?? optional(
            collect($this->forge->databases($server->id))->first()
        )->name;

        abort_if(is_null($database), 1, 'No databases found.');

        $this->step([
            'Establishing shell connection to %s database',
            $server->name.'@'.$database,
        ]);

        $password = $this->secretStep(['Enter The Database User %s Password', $user]);

        abort_if(is_null($password), 1, 'Password can not be empty.');

        if (in_array($databaseType, ['mysql', 'mysql8', 'mariadb'])) {
            return $this->connectToMysql($server->id, $user, $password, $database);
        } elseif (in_array($databaseType, ['postgres', 'postgres13'])) {
            return $this->connectToPostgres($server->id, $user, $password, $database);
        }

        abort(1, 'Starting a ['.$databaseType.'] database shell is not supported.');
    }

    /**
     * Connects the user the MySql instance.
     *
     * @param  string|int $serverId
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
     * @param  string|int $serverId
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
