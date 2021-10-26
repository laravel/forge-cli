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
        $this->ensureCurrentTeamIsSet();

        /** @var \Laravel\Forge\Resources\Server $server */
        $server = $this->forge->server(
            $this->config->get('server')
        );

        $tags = ! empty($server->tags) ? " ({$server->tags(',')})" : null;

        $this->successfulStep(
            'You are currently within the <comment>['.$server->name.']'.$tags.'</comment> server context.'
        );
    }
}
