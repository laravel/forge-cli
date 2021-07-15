<?php

namespace App\Commands\Concerns;

use App\Exceptions\LogicException;

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
            throw new LogicException('PHP is not installed in this server.');
        }
    }
}
