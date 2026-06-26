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

        return $this->remote->passthru(sprintf(
            'cd /home/%s/%s && %s artisan tinker',
            $site->user,
            $site->name,
            PhpVersion::of($site->phpVersion)->binary()
        ));
    }
}
