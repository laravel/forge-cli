<?php

namespace App\Commands;

class SiteLogsCommand extends Command
{
    use Concerns\InteractsWithLogs;

    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = 'site:logs {--id= : The ID of the site}';

    /**
     * The description of the command.
     *
     * @var string
     */
    protected $description = 'Retrieve the latest Site log messages';

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle()
    {
        $sites = function () {
            return $this->forge->sites($this->currentServer()->id);
        };

        $siteId = $this->askForId('Which site would you like to retrieve the logs from?', $sites);

        $this->showSiteLogs($siteId);
    }
}
