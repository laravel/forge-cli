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
    protected $signature = 'site:logs {site? : The site name}
                                      {--tail : Monitor the log changes in realtime}';

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
        $siteId = $this->askForSite('Which site would you like to retrieve the logs from');

        $site = $this->forge->site($this->currentServer()->id, $siteId);

        $this->showSiteLogs($site, $this->option('tail'));
    }
}
