<?php

namespace App\Commands\Concerns;

trait InteractsWithDatabase
{
    /**
     * Ensures the database service is installed and available on the current server.
     *
     * @return void
     */
    protected function ensureDatabaseExists()
    {
        $server = $this->currentServer();

        // @phpstan-ignore-next-line
        if (empty($server->databaseType)) {
            abort(1, 'No databases installed on this server.');
        }
    }

    /**
     * Gets the server database engine.
     *
     * @param  string  $databaseType
     * @return string|null
     */
    protected function databaseEngine($databaseType)
    {
        if (str_starts_with($databaseType, 'mysql') || str_starts_with($databaseType, 'mariadb')) {
            return 'mysql';
        }

        if (str_starts_with($databaseType, 'postgres')) {
            return 'postgres';
        }

        return null;
    }

    /**
     * Gets the Forge database log key.
     *
     * @param  string  $databaseType
     * @return string|null
     */
    protected function databaseLogKey($databaseType)
    {
        if (str_starts_with($databaseType, 'mysql')) {
            return 'database-mysql';
        }

        if (str_starts_with($databaseType, 'mariadb')) {
            return 'database-mariadb';
        }

        if (str_starts_with($databaseType, 'postgres')) {
            return 'database-postgresql';
        }

        return null;
    }
}
