<?php

namespace App\Commands;

use function Laravel\Prompts\spin;

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

        // Only the newest deployment is needed, so request a single item rather
        // than walking every page of the site's deployment history.
        $deployment = spin(
            fn () => $this->forge->deployments(
                $organization,
                $server->id,
                $siteId,
                ['sort' => '-created_at', 'page' => ['size' => 1]],
            )->items()[0] ?? null,
            'Retrieving deployments',
        );

        abort_if(is_null($deployment), 1, 'This site has not been deployed.');

        $this->newLine();

        $this->displayLogs(
            spin(
                fn () => $this->forge->deploymentLog($organization, $server->id, $siteId, $deployment->id),
                'Retrieving deployment logs',
            )
        );

        $this->newLine();
    }
}
