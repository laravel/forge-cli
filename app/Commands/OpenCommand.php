<?php

namespace App\Commands;

use Symfony\Component\Process\Process;

class OpenCommand extends Command
{
    use Concerns\InteractsWithEvents;

    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = 'open {site? : The site name}';

    /**
     * The description of the command.
     *
     * @var string
     */
    protected $description = 'Open a site in forge.laravel.com';

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle()
    {
        $siteId = $this->askForSite('Which site would you like to open');
        $serverId = $this->currentServer()->id;

        $url = "https://forge.laravel.com/servers/$serverId/sites/$siteId";

        $os = strtolower(php_uname(PHP_OS));

        if (strpos($os, 'darwin') !== false) {
            $open = 'open';
        } elseif (strpos($os, 'linux') !== false) {
            $open = 'xdg-open';
        } else {
            $this->step("Can't open your browser, you'll have to manually navigate to {$url}");

            return;
        }

        $this->step('Opening site in your browser...');

        $command = [$open, $url];

        $process = new Process($command);
        $process->run();
    }
}
