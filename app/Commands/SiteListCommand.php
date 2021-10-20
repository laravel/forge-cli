<?php

namespace App\Commands;

use App\Support\PhpVersion;

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
        $this->step('Retrieving the list of sites');

        $sites = $this->forge->sites(
            $this->currentServer()->id
        );

        $this->table([
            'ID', 'Name', 'PHP',
        ], collect($sites)->map(function ($site) {
            $name = $site->name;

            if (! empty($tags = $site->tags(', '))) {
                $name .= " <fg=gray>($tags)</>";
            }

            return [
                $site->id,
                $name,
                // @phpstan-ignore-next-line
                $site->phpVersion ? PhpVersion::of($site->phpVersion)->release() : 'None',
            ];
        })->all());
    }
}
