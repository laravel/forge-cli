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

        $this->displayLogs($logs->content);
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

        $this->displayLogs($logs->content);
    }

    /**
     * Displays the given logs.
     *
     * @param  string  $logs
     * @return void
     */
    protected function displayLogs($logs)
    {
        Str::of($logs)
            ->trim()
            ->whenEmpty(function () {
                abort(1, 'The requested logs could not be found, or they are simply empty.');
            })->whenNotEmpty(function ($logs) {
                $this->line('');

                $logs->explode("\n")
                    ->each(function ($line) {
                        $this->line("  <fg=#6C7280>▕</> $line");
                    });

                $this->line('');
            });
    }
}
