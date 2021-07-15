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
        if (is_null($id = $this->option('id'))) {
            $servers = collect($this->forge->servers());

            $name = $this->choice('Which server would you like to switch to?', $servers->mapWithKeys(function ($server) {
                return [$server->id => $server->name];
            })->all());

            $id = $servers->where('name', $name)->first()->id;
        }

        $server = $this->forge->server($id);

        $this->config->set('server', $server->id);

        $this->info('Current server context changed successfully.');
    }
}
