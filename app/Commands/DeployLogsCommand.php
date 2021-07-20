<?php

namespace App\Commands;

class DeployLogsCommand extends Command
{
    use Concerns\InteractsWithLogs;

    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = 'deploy:logs {--id= : The ID of the site}';

    /**
     * The description of the command.
     *
     * @var string
     */
    protected $description = 'Retrieve the latest Deployment log messages';

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

        $siteId = $this->askForId('Which site would you like to retrieve the deployment logs from?', $sites);

        $lastDeploymentId = optional(collect($this->forge->siteDeployments(
            $this->currentServer()->id,
            $siteId,
        ))->first())['id'];

        abort_if(is_null($lastDeploymentId), 1, 'No deployments have been made in this site.');

        $this->displayLogs(
            $this->forge->siteDeploymentOutput($this->currentServer()->id, $siteId, $lastDeploymentId)
        );
    }
}
