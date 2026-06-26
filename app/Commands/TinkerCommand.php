<?php

namespace App\Commands;

use App\Support\PhpVersion;

use function Laravel\Prompts\info;

class TinkerCommand extends Command
{
    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = 'tinker {site? : The site name}';

    /**
     * The description of the command.
     *
     * @var string
     */
    protected $description = 'Tinker with a site';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $siteId = (int) $this->askForSite('Which site would you like to tinker with');
        $organization = $this->currentOrganization();

        $site = $this->forge->organizationSite($organization, $siteId);

        info('Establishing tinker connection');

        $directory = sprintf('/home/%s/%s', $site->user, $site->name);

        if ($site->zeroDowntimeDeployments) {
            $directory .= '/current';
        }

        return $this->remote->passthru(sprintf(
            'cd %s && %s artisan tinker',
            $directory,
            PhpVersion::of($site->phpVersion)->binary()
        ));
    }
}
