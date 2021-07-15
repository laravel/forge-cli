<?php

namespace App\Commands\Concerns;

use Illuminate\Support\Str;

trait InteractsWithLogs
{
    /**
     * Displays the given logs.
     *
     * @param  \Laravel\Forge\Resources\Server  $server
     * @param  string  $type
     * @return void
     */
    public function showLogs($server, $type)
    {
        $logs = $this->forge->logs($server->id, $type);

        Str::of($logs->content)
            ->trim()
            ->explode("\n")
            ->each(function ($line) {
                $this->line($line);
            });
    }
}
