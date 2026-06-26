<?php

namespace App\Commands;

use Illuminate\Support\Once;

use function Laravel\Prompts\info;

class OrganizationSwitchCommand extends Command
{
    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = 'organization:switch {organization? : The organization name}';

    /**
     * The description of the command.
     *
     * @var string
     */
    protected $description = 'Switch to a different organization';

    /**
     * The aliases of the command.
     *
     * @var array<string>
     */
    protected $aliases = [
        'org:switch',
    ];

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle()
    {
        $slug = $this->askForOrganization('Which organization would you like to switch to');

        $organization = $this->forge->organization($slug);

        $this->config->set('organization', $organization->slug);
        $this->config->forget('server');

        Once::flush();

        info("Current organization changed successfully to {$organization->name}");
    }
}
