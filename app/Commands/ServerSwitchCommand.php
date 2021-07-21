<?php

namespace App\Commands;

class ServerSwitchCommand extends Command
{
    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = 'server:switch {--id= : The ID of the server to switch to}';

    /**
     * The description of the command.
     *
     * @var string
     */
    protected $description = 'Switch to a different server';

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle()
    {
        $servers = function () {
            return $this->forge->servers();
        };

        $serverId = $this->askForId('Which server would you like to switch to', $servers);

        $server = $this->forge->server($serverId);

        $this->config->set('server', $server->id);

        $this->successfulStep('Current server context changed successfully');
    }
}
