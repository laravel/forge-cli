<?php

namespace App\Commands;

use Laravel\Forge\Resources\Organization;

use function Laravel\Prompts\info;

class OrganizationCurrentCommand extends Command
{
    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = 'organization:current';

    /**
     * The description of the command.
     *
     * @var string
     */
    protected $description = 'Determine your current organization';

    /**
     * The aliases of the command.
     *
     * @var array
     */
    protected $aliases = [
        'org:current',
    ];

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle()
    {
        $slug = $this->config->get('organization');

        abort_if(is_null($slug), 1, 'You have not selected an organization. Use the \'organization:switch\' command.');

        /** @var Organization $organization */
        $organization = $this->forge->organization($slug);

        info("You are currently within the {$organization->name} ({$organization->slug}) organization context.");
    }
}
