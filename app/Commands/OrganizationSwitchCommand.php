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

        $token = $this->tokenFor($slug);

        $organization = $this->forge->usingToken($token, fn () => $this->forge->organization($slug));

        if ($token !== $this->forge->token($organization->slug)) {
            $this->config->set('tokens', [$organization->slug => $token] + $this->config->get('tokens', []));
        }

        $this->config->set('organization', $organization->slug);
        $this->config->forget('server');

        Once::flush();

        info("Current organization changed successfully to {$organization->name}");
    }

    /**
     * Get the api token that has access to the given organization.
     *
     * @param  string  $slug
     * @return string|null
     */
    protected function tokenFor($slug)
    {
        if (isset($this->config->get('tokens', [])[$slug]) || count($this->forge->tokens()) <= 1) {
            return $this->forge->token($slug);
        }

        return $this->forge->organizationsWithTokens()->get($slug)[1] ?? $this->forge->token($slug);
    }
}
