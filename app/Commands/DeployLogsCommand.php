<?php

namespace App\Commands;

use function Laravel\Prompts\info;

class DeployLogsCommand extends Command
{
    use Concerns\InteractsWithLogs;

    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = 'deploy:logs {site? : The site name}';

    /**
     * The description of the command.
     *
     * @var string
     */
    protected $description = 'Retrieve the latest deployment log messages';

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle()
    {
        $siteId = (int) $this->askForSite('Which site would you like to retrieve the deployment logs from');
        $organization = $this->currentOrganization();
        $server = $this->currentServer();

        info('Retrieving the latest deployment logs');

        $deployment = collect($this->forge->deployments(
            $organization,
            $server->id,
            $siteId,
        )->lazy())->first();

        abort_if(is_null($deployment), 1, 'This site has not been deployed.');

        $this->newLine();

        $this->displayLogs(
            $this->forge->deploymentLog($organization, $server->id, $siteId, $deployment->id)
        );

        $this->newLine();
    }
}
