<?php

namespace App\Commands;

use Laravel\Forge\Resources\Server;

use function Laravel\Prompts\info;

class ServerCurrentCommand extends Command
{
    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = 'server:current';

    /**
     * The description of the command.
     *
     * @var string
     */
    protected $description = 'Determine your current server';

    /**
     * The aliases of the command.
     *
     * @var array
     */
    protected $aliases = [
        'current',
    ];

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle()
    {
        $serverId = $this->config->get('server');

        abort_if(is_null($serverId), 1, 'You have not selected a server. Use the \'server:switch\' command.');

        /** @var Server $server */
        $server = $this->forge->server($this->currentOrganization(), $serverId);

        info("You are currently within the {$server->name} server context.");
    }
}
