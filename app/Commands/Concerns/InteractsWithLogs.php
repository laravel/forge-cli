<?php

namespace App\Commands\Concerns;

use Illuminate\Support\Str;
use Laravel\Forge\Resources\Site;

use function Laravel\Prompts\info;

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
     * @param  Site  $site
     * @param  bool  $follow
     * @return void
     */
    protected function showSiteLogs($site, $follow)
    {
        info('Retrieving the latest site logs');

        switch (strtolower($site->appType)) {
            case 'wordpress':
                $files = ['public/wp-content/*.log', 'wp-content/*.log'];
                break;
            default:
                $files = ['shared/storage/logs/*.log', 'storage/logs/*.log'];
                break;
        }

        $sitePath = '/home/'.$site->user.'/'.$site->name;

        $sitePath = basename($sitePath) == 'current'
            ? basename($sitePath)
            : $sitePath;

        $this->showRemoteLogs(collect($files)->map(function ($file) use ($sitePath) {
            return $sitePath.'/'.$file;
        })->all(), $follow);
    }

    /**
     * Shows the given background process logs.
     *
     * @param  string|int  $processId
     * @param  string  $username
     * @param  bool  $follow
     * @return void
     */
    protected function showBackgroundProcessLogs($processId, $username, $follow)
    {
        abort_if($username == 'root', 1, 'Following logs from background processes run by [root] is not supported.');

        info('Retrieving the latest background process logs');

        $this->showRemoteLogs('/home/'.$username.'/.forge/daemon-'.$processId.'.log', $follow);
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
