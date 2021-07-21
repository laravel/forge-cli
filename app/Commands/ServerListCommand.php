<?php

namespace App\Commands;

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
        $this->step('Retrieving the list of servers');

        $this->table([
            'ID', 'Name', 'IP Address',
        ], collect($this->forge->servers())->map(function ($server) {
            return [
                $server->id,
                $server->name,
                $server->ipAddress,
            ];
        })->all());
    }
}
