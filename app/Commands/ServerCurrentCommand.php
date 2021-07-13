<?php

namespace App\Commands;

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
    protected $description = 'Determine your current server context';

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle()
    {
        $server = $this->forge->server(
            $this->config->get('server')
        );

        $this->info(
            'You are currently within the <comment>['.$server->name.']</comment> server context.'
        );
    }
}
