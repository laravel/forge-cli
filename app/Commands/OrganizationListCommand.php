<?php

namespace App\Commands;

use function Laravel\Prompts\spin;
use function Laravel\Prompts\table;

class OrganizationListCommand extends Command
{
    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = 'organization:list';

    /**
     * The description of the command.
     *
     * @var string
     */
    protected $description = 'List the organizations';

    /**
     * The aliases of the command.
     *
     * @var array
     */
    protected $aliases = [
        'org:list',
    ];

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle()
    {
        $organizations = spin(
            fn () => collect($this->forge->organizations()),
            'Retrieving organizations',
        );

        table(
            ['Name', 'Slug'],
            $organizations->map(function ($organization) {
                return [$organization->name, $organization->slug];
            })->all(),
        );
    }
}
