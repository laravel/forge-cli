<?php

namespace App\Commands;

use Illuminate\Support\Once;

use function Laravel\Prompts\info;
use function Laravel\Prompts\spin;

class ServerSwitchCommand extends Command
{
    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = 'server:switch {server? : The server name}';

    /**
     * The description of the command.
     *
     * @var string
     */
    protected $description = 'Switch to a different server';

    /**
     * The aliases of the command.
     *
     * @var array
     */
    protected $aliases = [
        'switch',
    ];

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle()
    {
        $serverId = $this->askForServer('Which server would you like to switch to');

        $server = spin(
            fn () => $this->forge->server($this->currentOrganization(), $serverId),
            'Retrieving server',
        );

        $this->config->set('server', $server->id);

        Once::flush();

        info("Current server context changed successfully to {$server->name}");
    }
}
