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
    protected $signature = 'deploy
        {target? : Environment name (e.g., staging, production) or site name}
        {--site= : Explicit site name (bypasses environment detection)}
        {--force : Skip confirmation prompt}';

    /**
     * The description of the command.
     *
     * @var string
     */
    protected $description = 'Deploy a site';

    /**
     * Execute the console command.
     *
     * @return int|void
     */
    public function handle()
    {
        $target = $this->argument('target');
        $explicitSite = $this->option('site');

        // If --site is provided, bypass all environment logic
        if ($explicitSite) {
            return $this->deployToSite($explicitSite);
        }

        // Smart detection: is target an environment name?
        if ($target && $this->isEnvironmentName($target)) {
            $this->setEnvironment($target);
            return $this->deployToEnvironment($target);
        }

        // If target provided but not an environment, treat as site name
        if ($target) {
            return $this->deployToSite($target);
        }

        // No target: use default environment from .forge if available
        if ($this->hasEnvironmentConfig()) {
            $envName = $this->getEnvironmentName();
            return $this->deployToEnvironment($envName);
        }

        // Fallback: prompt for site (original behavior)
        return $this->deployToSite(null);
    }

    /**
     * Check if the given name matches a configured environment.
     *
     * @param  string  $name
     * @return bool
     */
    protected function isEnvironmentName($name)
    {
        $envNames = $this->localConfig->getEnvironmentNames();
        return in_array(strtolower($name), array_map('strtolower', $envNames));
    }

    /**
     * Set the environment to use for this deployment.
     *
     * @param  string  $name
     * @return void
     */
    protected function setEnvironment($name)
    {
        // Override the resolved environment
        $this->resolvedEnvironment = $this->localConfig->resolveEnvironment($name);
    }

    /**
     * Deploy to a configured environment.
     *
     * @param  string  $envName
     * @return int|void
     */
    protected function deployToEnvironment($envName)
    {
        // Check for confirmation if required
        if (! $this->confirmEnvironmentAction('deploy')) {
            $this->warnStep('Deployment cancelled.');
            return 0;
        }

        $this->step(['Deploying to %s environment', strtoupper($envName)]);

        $siteId = $this->getEnvironmentSiteId();

        if (! $siteId) {
            $this->error("No site configured for environment '{$envName}'");
            return 1;
        }

        $site = $this->forge->site($this->currentServer()->id, $siteId);

        abort_unless(is_null($site->deploymentStatus), 1, 'This site is already deploying.');

        $this->deploy($site);
    }

    /**
     * Deploy to a site by name (original behavior).
     *
     * @param  string|null  $siteName
     * @return int|void
     */
    protected function deployToSite($siteName)
    {
        // Clear environment config so we don't use it
        $this->resolvedEnvironment = ['name' => null, 'config' => []];

        $siteId = $siteName
            ? $this->resolveSiteByName($siteName)
            : $this->askForSite('Which site would you like to deploy');

        $site = $this->forge->site($this->currentServer()->id, $siteId);

        abort_unless(is_null($site->deploymentStatus), 1, 'This site is already deploying.');

        $this->deploy($site);
    }

    /**
     * Resolve a site ID from its name.
     *
     * @param  string  $name
     * @return int|string
     */
    protected function resolveSiteByName($name)
    {
        $sites = collect($this->forge->sites($this->currentServer()->id));

        $site = $sites->where('name', $name)->first();

        return $site ? $site->id : $name;
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

        $deploymentId = $this->ensureDeploymentHaveStarted($site);

        $deployment = $this->ensureDeploymentHasFinished($server, $site, $deploymentId);

        $output = $this->forge->siteDeploymentOutput($server->id, $site->id, $deploymentId);
        $output = explode(PHP_EOL, $output);
        $this->displayOutput(collect($output));

        abort_if($deployment->status == 'failed', 1, 'The deployment failed.');

        $this->deploymentSuccess($site, $deployment);
    }

    /**
     * Ensure the deployment have started on the server.
     *
     * @param  \Laravel\Forge\Resources\Site  $site
     * @return int
     */
    protected function ensureDeploymentHaveStarted($site)
    {
        $this->step('Waiting For Deployment To Start');

        do {
            $this->time->sleep(1);

            $status = $this->forge->site(
                $this->currentServer()->id,
                $site->id
            )->deploymentStatus;
        } while ($status == 'queued');

        $this->step('Deploying');

        $deploymentId = collect($this->forge->siteDeployments(
            $this->currentServer()->id,
            $site->id,
        ))->first()['id'];

        return $deploymentId;
    }

    /**
     * Ensure the deployment has finished on the server.
     *
     * @param  \Laravel\Forge\Resources\Server  $server
     * @param  \Laravel\Forge\Resources\Site  $site
     * @param  int  $deploymentId
     * @return object
     */
    protected function ensureDeploymentHasFinished($server, $site, $deploymentId)
    {
        do {
            $this->time->sleep(1);

            $deployment = $this->forge->siteDeployment($server->id, $site->id, $deploymentId);
        } while ($deployment->status == 'deploying');

        return $deployment;
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
            'Deployment ID',
            'Site URL',
        ], [[
            "{$deployment->id}",
            "https://{$site->name}",
        ]]);
    }
}
