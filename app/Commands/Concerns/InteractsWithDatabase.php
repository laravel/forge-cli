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
            abort(1, 'No databases installed in this server.');
        }
    }
}
