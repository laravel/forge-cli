<?php

namespace App\Commands;

use App\Support\PhpVersion;

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
        $siteId = $this->askForSite('Which site would you like to tinker with');

        $site = $this->forge->site($this->currentServer()->id, $siteId);

        $this->step('Establishing Tinker Connection');

        // @phpstan-ignore-next-line
        $phpVersion = $site->phpVersion;

        return $this->remote->passthru(sprintf(
            'cd /home/%s/%s && %s artisan tinker',
            $site->username,
            $site->name,
            PhpVersion::of($phpVersion)->binary()
        ));
    }
}
