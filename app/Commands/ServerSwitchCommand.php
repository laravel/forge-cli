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
        $serverId = $this->askForId(
            'Which server would you like to switch to?',
            function () {
                return $this->forge->servers();
            }
        );

        $server = $this->forge->server($serverId);

        $this->config->set('server', $server->id);

        $this->info('Current server context changed successfully.');
    }
}
