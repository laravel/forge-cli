<?php

namespace App\Commands;

use Illuminate\Support\Carbon;

class DeployCommand extends Command
{
    use Concerns\InteractsWithEvents;

    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = 'deploy {--id= : The ID of the site}';

    /**
     * The description of the command.
     *
     * @var string
     */
    protected $description = 'Deploy an site';

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

        $siteId = $this->askForId('Which site would you like to deploy?', $sites);

        $site = $this->forge->site($this->currentServer()->id, $siteId);

        abort_unless(is_null($site->deploymentStatus), 1, 'This site is already deploying.');

        $this->deploy($site);
    }

    /**
     * Deploy an site.
     *
     * @param  \Laravel\Forge\Resources\Site  $site
     * @return void
     */
    public function deploy($site)
    {
        $server = $this->currentServer();

        $this->step('Queuing Deployment');

        $this->forge->deploySite($server->id, $site->id, false);

        [$deploymentId, $eventId] = $this->ensureDeploymentHaveStarted($site);

        $deployment = null;

        $this->displayEventOutput($eventId, function () use ($server, $site, $deploymentId, &$deployment) {
            $deployment = $this->forge->siteDeployment($server->id, $site->id, $deploymentId);

            return $deployment->status == 'deploying';
        });

        abort_if($deployment->status == 'failed', 1, 'The deployment failed.');

        $this->deploymentSuccess($site, $deployment);
    }

    /**
     * Ensure the deployment have started on the server.
     *
     * @param  \Laravel\Forge\Resources\Site  $site
     * @return array
     */
    protected function ensureDeploymentHaveStarted($site)
    {
        $this->step('Waiting For Deployment To Start');

        do {
            sleep(1);

            $status = $this->forge->site(
                $this->currentServer()->id,
                $site->id
            )->deploymentStatus;
        } while ($status == 'queued');

        $this->step('Deploying');

        $eventId = $this->findEventId(sprintf(
            'Deploying Pushed Code (%s).',
            $site->name
        ));

        abort_if(is_null($eventId), 1, 'The deployment failed. Did you configured the deployment script?');

        $deploymentId = collect($this->forge->siteDeployments(
            $this->currentServer()->id,
            $site->id,
        ))->first()['id'];

        return [$deploymentId, $eventId];
    }

    /**
     * Ends the deployment by displaying a deployment success output.
     *
     * @param  \Laravel\Forge\Resources\Site  $site
     * @param  object  $deployment
     * @return void
     */
    protected function deploymentSuccess($site, $deployment)
    {
        $time = Carbon::parse($deployment->ended_at)
            ->diffInSeconds(Carbon::parse($deployment->started_at));

        $this->successfulStep('<options=bold>Site Deployed Successfully.</> <fg=#6C7280>('.$time.'s)</>');

        $this->table([
            '   <comment>Deployment ID</comment>',
            '   <comment>Site URL</comment>',
        ], [[
            "   <options=bold>{$deployment->id}</>",
            "   <options=bold>https://{$site->name}</>",
        ]], 'compact');
    }
}
