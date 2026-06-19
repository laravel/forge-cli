<?php

namespace App\Commands;

use function Laravel\Prompts\spin;
use function Laravel\Prompts\table;
use function Laravel\Prompts\warning;

class SiteListCommand extends Command
{
    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = 'site:list';

    /**
     * The description of the command.
     *
     * @var string
     */
    protected $description = 'List the sites';

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle()
    {
        $sites = spin(
            fn () => collect($this->forge->serverSites($this->currentOrganization(), $this->currentServer()->id)->lazy()),
            'Retrieving sites',
        );

        if ($sites->isEmpty()) {
            warning('No sites found.');

            return;
        }

        table(
            ['ID', 'Name', 'PHP'],
            $sites->map(function ($site) {
                return [
                    $site->id,
                    $site->name,
                    $site->phpVersion ? str_replace('PHP ', '', $site->phpVersion) : 'None',
                ];
            })->all(),
        );
    }
}
