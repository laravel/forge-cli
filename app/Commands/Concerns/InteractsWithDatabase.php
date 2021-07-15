<?php

namespace App\Commands\Concerns;

use App\Exceptions\LogicException;

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
        if (is_null($server->databaseType)) {
            throw new LogicException('No databases installed in this server.');
        }
    }
}
