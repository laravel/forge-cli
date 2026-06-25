<?php

namespace App\Commands;

use function Laravel\Prompts\spin;

class SiteLogsCommand extends Command
{
    use Concerns\InteractsWithLogs;

    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = 'site:logs {site? : The site name}
                                      {--f|follow : Monitor the log changes in realtime}';

    /**
     * The description of the command.
     *
     * @var string
     */
    protected $description = 'Retrieve the latest site log messages';

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle()
    {
        $siteId = $this->askForSite('Which site would you like to retrieve the logs from');

        $site = spin(
            fn () => $this->forge->organizationSite($this->currentOrganization(), (int) $siteId),
            'Retrieving site',
        );

        $this->showSiteLogs($site, $this->option('follow'));
    }
}
