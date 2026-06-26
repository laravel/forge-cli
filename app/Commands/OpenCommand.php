<?php

namespace App\Commands;

use Symfony\Component\Process\Process;

use function Laravel\Prompts\info;

class OpenCommand extends Command
{
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
        $siteId = (int) $this->askForSite('Which site would you like to open');
        $organization = $this->currentOrganization();
        $serverId = $this->currentServer()->id;

        $url = "https://forge.laravel.com/orgs/$organization/servers/$serverId/sites/$siteId";

        $os = strtolower(php_uname(PHP_OS));

        if (strpos($os, 'darwin') !== false) {
            $open = 'open';
        } elseif (strpos($os, 'linux') !== false) {
            $open = 'xdg-open';
        } else {
            info("Can't open your browser, you'll have to manually navigate to {$url}");

            return;
        }

        info('Opening site in your browser...');

        $command = [$open, $url];

        $process = new Process($command);
        $process->run();
    }
}
