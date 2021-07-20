<?php

namespace App\Commands;

use Illuminate\Support\Carbon;

class DeployCommand extends Command
{
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
     * The current state of the deployment output.
     *
     * @var array
     */
    protected $outputBuffer = [];

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

        $lastEventId = collect($this->forge->events((string) $server->id))->first()->id;

        $this->forge->deploySite($server->id, $site->id, false);

        [$deploymentId, $eventId] = $this->ensureDeploymentHaveStarted($site, $lastEventId);

        $this->line('');

        do {
            sleep(1);

            $this->displayOutput($eventId);

            $deployment = $this->forge->siteDeployment($server->id, $site->id, $deploymentId);
        } while ($deployment->status == 'deploying');

        $this->displayOutput($eventId);

        $this->line('');

        abort_if($deployment->status == 'failed', 1, 'The deployment failed.');

        $this->deploymentSuccess($site, $deployment);
    }

    /**
     * Ensure the deployment have started on the server.
     *
     * @param  \Laravel\Forge\Resources\Site  $site
     * @param  string|int|null  $lastEventId
     * @return array
     */
    protected function ensureDeploymentHaveStarted($site, $lastEventId)
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

        $eventId = optional(collect($this->forge->events((string) $this->currentServer()->id))->first(function ($event) use ($site) {
            return $event->description == sprintf(
                'Deploying Pushed Code (%s).',
                $site->name
            );
        }))->id;

        abort_if(is_null($eventId), 1, 'The deployment failed. Did you configured the deployment script?');

        $deploymentId = collect($this->forge->siteDeployments(
            $this->currentServer()->id,
            $site->id,
        ))->first()['id'];

        return [$deploymentId, $eventId];
    }

    /**
     * Displays the deployment output.
     *
     * @param  string|int  $eventId
     * @return void
     */
    protected function displayOutput($eventId)
    {
        [$exitCode, $output] = $this->remote->exec(sprintf(
            'cat /home/forge/.forge/provision-%s.output',
            $eventId
        ));

        if ($exitCode == 0) {
            collect($output)->slice(count($this->outputBuffer))->each(function ($line) {
                $this->line("  <fg=#6C7280>▕</> $line");
            });

            $this->outputBuffer = $output;
        }
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
