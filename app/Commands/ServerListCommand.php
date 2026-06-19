<?php

namespace App\Commands;

use function Laravel\Prompts\spin;
use function Laravel\Prompts\table;

class ServerListCommand extends Command
{
    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = 'server:list';

    /**
     * The description of the command.
     *
     * @var string
     */
    protected $description = 'List the servers';

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle()
    {
        $servers = spin(
            fn () => collect($this->forge->servers($this->currentOrganization())->lazy())
                ->reject(function ($server) {
                    return $server->revoked;
                }),
            'Retrieving servers',
        );

        table(
            ['ID', 'Name', 'IP Address'],
            $servers->map(function ($server) {
                return [$server->id, $server->name, $server->ipAddress];
            })->all(),
        );
    }
}
