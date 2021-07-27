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
        $this->step('Retrieving the latest logs');

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
        $this->step('Retrieving the latest site logs');

        $logs = $this->forge->siteLogs($this->currentServer()->id, $siteId);

        $this->displayLogs($logs->content);
    }

    /**
     * Shows the given daemon logs.
     *
     * @param  string|int  $daemonId
     * @param  string  $user
     * @return void
     */
    protected function showDaemonLogs($daemonId, $user)
    {
        abort_if($user == 'root', 1, 'Requesting logs from daemons run by [root] is not supported.');

        [$exitCode, $content] = $this->remote->exec(
            'cat /home/'.$user.'/.forge/daemon-'.$daemonId.'.log'
        );

        collect($content)->implode("\n");

        abort_if($exitCode > 0, 1, 'The requested logs could not be found, or they are simply empty.');

        $this->displayLogs(collect($content)->implode("\n"));
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
