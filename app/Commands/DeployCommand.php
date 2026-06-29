<?php

namespace App\Commands;

use Illuminate\Support\Carbon;
use Laravel\Forge\Resources\Deployment;
use Laravel\Forge\Resources\Site;

use function Laravel\Prompts\info;
use function Laravel\Prompts\spin;
use function Laravel\Prompts\table;

class DeployCommand extends Command
{
    use Concerns\InteractsWithLogs;

    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = 'deploy {site? : The site name}';

    /**
     * The description of the command.
     *
     * @var string
     */
    protected $description = 'Deploy a site';

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle()
    {
        $siteId = (int) $this->askForSite('Which site would you like to deploy');
        $organization = $this->currentOrganization();

        $site = $this->forge->organizationSite($organization, $siteId);

        abort_unless(is_null($site->deploymentStatus), 1, 'This site is already deploying.');

        $this->deploy($organization, $site);
    }

    /**
     * Deploy a site.
     *
     * @param  string  $organization
     * @param  Site  $site
     * @return void
     */
    public function deploy($organization, $site)
    {
        $server = $this->currentServer();

        $deployment = spin(
            fn () => $this->forge->createDeployment($organization, $server->id, $site->id),
            'Queuing deployment',
        );

        $deployment = spin(function () use ($organization, $server, $site, $deployment) {
            while (in_array($deployment->status, ['pending', 'queued', 'deploying'])) {
                $this->time->sleep(1);

                /** @var Deployment $deployment */
                $deployment = $this->forge->deployment($organization, $server->id, $site->id, $deployment->id);
            }

            return $deployment;
        }, 'Deploying');

        $log = $this->forge->deploymentLog($organization, $server->id, $site->id, $deployment->id);

        $this->newLine();

        $this->displayLogs($log);

        $this->newLine();

        abort_if(
            in_array($deployment->status, ['failed', 'failed-build', 'cancelled']),
            1,
            'The deployment failed.'
        );

        $this->deploymentSuccess($site, $deployment);
    }

    /**
     * Ends the deployment by displaying a deployment success output.
     *
     * @param  Site  $site
     * @param  Deployment  $deployment
     * @return void
     */
    protected function deploymentSuccess($site, $deployment)
    {
        $time = (int) abs(Carbon::parse($deployment->startedAt)
            ->diffInSeconds(Carbon::parse($deployment->endedAt)));

        info("Site deployed successfully. ({$time}s)");

        table([
            'Deployment ID',
            'Site URL',
        ], [[
            "{$deployment->id}",
            "https://{$site->name}",
        ]]);
    }
}
