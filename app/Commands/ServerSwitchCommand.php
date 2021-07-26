<?php

namespace App\Commands;

use Spatie\Once;

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

        $server = $this->forge->server($serverId);

        $this->config->set('server', $server->id);

        Once\Cache::flush();

        $this->successfulStep(
            'Current server context changed successfully to <comment>['.$server->name.']</comment>'
        );
    }
}
