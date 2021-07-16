<?php

namespace App\Commands\Concerns;

use Illuminate\Support\Str;

trait InteractsWithLogs
{
    /**
     * Shows the given "type" of logs.
     *
     * @param  string  $type
     * @return void
     */
    protected function showLogs($type)
    {
        $logs = $this->forge->logs($this->currentServer()->id, $type);

        $this->displayLogs($logs);
    }

    /**
     * Shows the given site logs.
     *
     * @param  string|int  $siteId
     * @return void
     */
    protected function showSiteLogs($siteId)
    {
        $logs = $this->forge->siteLogs($this->currentServer()->id, $siteId);

        $this->displayLogs($logs);
    }

    /**
     * Displays the given logs.
     *
     * @param  object  $logs
     * @return void
     */
    protected function displayLogs($logs)
    {
        Str::of($logs->content)
            ->trim()
            ->whenEmpty(function () {
                abort(1, 'The requested logs could not be found, or they are simply empty.');
            })->whenNotEmpty(function ($logs) {
                $logs->explode("\n")
                    ->each(function ($line) {
                        $this->line($line);
                    });
            });
    }
}
