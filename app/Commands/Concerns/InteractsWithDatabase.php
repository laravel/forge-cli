<?php

namespace App\Commands\Concerns;

trait InteractsWithDatabase
{
    /**
     * The supported MySQL-compatible database types.
     *
     * @var array<string>
     */
    protected static array $mysqlTypes = ['mysql', 'mysql8', 'mariadb'];

    /**
     * The supported PostgreSQL-compatible database types.
     *
     * @var array<string>
     */
    protected static array $postgresTypes = ['postgres', 'postgres13'];

    /**
     * Get all supported database types.
     *
     * @return array<string>
     */
    protected function supportedDatabaseTypes(): array
    {
        return array_merge(static::$mysqlTypes, static::$postgresTypes);
    }

    /**
     * Determine if the given type is a MySQL-compatible database.
     *
     * @param  string  $type
     * @return bool
     */
    protected function isMysqlDatabase(string $type): bool
    {
        return in_array($type, static::$mysqlTypes);
    }

    /**
     * Determine if the given type is a PostgreSQL-compatible database.
     *
     * @param  string  $type
     * @return bool
     */
    protected function isPostgresDatabase(string $type): bool
    {
        return in_array($type, static::$postgresTypes);
    }

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
}
