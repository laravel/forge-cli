<?php

namespace App\Commands\Concerns;

trait InteractsWithPhp
{
    /**
     * Ensures PHP is installed and available on the current server.
     *
     * @return void
     */
    protected function ensurePhpExists()
    {
        $server = $this->currentServer();

        // @phpstan-ignore-next-line
        if (is_null($server->phpVersion)) {
            abort(1, 'PHP is not installed in this server.');
        }
    }
}
