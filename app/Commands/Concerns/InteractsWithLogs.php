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

        $logs = $this->forge->logs($this->currentServer()->id, $type)->content;

        abort_if(empty($logs), 1, 'The requested logs could not be found or they are empty.');

        $this->newLine();

        $this->displayLogs($logs);

        $this->newLine();
    }

    /**
     * Shows the given site logs.
     *
     * @param  \Laravel\Forge\Resources\Site  $site
     * @param  bool  $follow
     * @return void
     */
    protected function showSiteLogs($site, $follow)
    {
        $this->step('Retrieving the latest site logs');

        switch (strtolower($site->app)) {
            case 'wordpress':
                $files = ['public/wp-content/*.log', 'wp-content/*.log'];
                break;
            default:
                $files = ['shared/storage/logs/*.log', 'storage/logs/*.log'];
                break;
        }

        $sitePath = '/home/'.$site->username.'/'.$site->name;

        $sitePath = basename($sitePath) == 'current'
            ? basename($sitePath)
            : $sitePath;

        $this->showRemoteLogs(collect($files)->map(function ($file) use ($sitePath) {
            return $sitePath.'/'.$file;
        })->all(), $follow);
    }

    /**
     * Shows the given daemon logs.
     *
     * @param  string|int  $daemonId
     * @param  string  $username
     * @param  bool  $follow
     * @return void
     */
    protected function showDaemonLogs($daemonId, $username, $follow)
    {
        abort_if($username == 'root', 1, 'Requesting logs from daemons run by [root] is not supported.');

        $this->step('Retrieving the latest daemon logs');

        $this->showRemoteLogs('/home/'.$username.'/.forge/daemon-'.$daemonId.'.log', $follow);
    }

    /**
     * Shows remote logs.
     *
     * @param  array|string  $files
     * @param  bool  $follow
     * @return void
     */
    protected function showRemoteLogs($files, $follow)
    {
        $this->newLine();

        [$exitCode, $output] = $this->remote->tail($files, function ($output) {
            $this->displayLogs($output);
        }, $follow ? ['-f'] : []);

        abort_if(
            empty($output) || ($exitCode > 0 && $exitCode < 255),
            1,
            'The requested logs could not be found or they are empty.'
        );

        $this->line('');
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
            ->whenNotEmpty(function ($logs) {
                $logs->explode("\n")->each(function ($line) {
                    $this->line("  <fg=#6C7280>▕</> $line");
                });
            });
    }
}
