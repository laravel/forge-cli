<?php

namespace App\Commands;

class SshCommand extends Command
{
    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = 'ssh {server? : The server name}
                                {site? : The site name}';

    /**
     * The description of the command.
     *
     * @var string
     */
    protected $description = 'Start an SSH session';

    /**
     * Execute the console command.
     *
     * @return int|never
     */
    public function handle()
    {
        $server = $this->argument('server');

        if (! is_null($server)) {
            $this->call('server:switch', [
                'server' => $server,
            ]);
        }

        $name = $this->argument('site');

        if (! is_null($name)) {
            $answers = collect($this->forge->sites($this->currentServer()->id));
            $username = optional($answers->where('name', $name)->first())->username ?: 'forge';
            $this->remote->setSshUser($username);
        }

        $server = $this->currentServer();

        $this->step('Establishing secure connection');

        $this->remote->ensureSshIsConfigured();

        $this->successfulStep('Connected To <comment>['.$server->name.']</comment>');

        $exitCode = $this->remote->passthru();

        abort_if($exitCode == 255, $exitCode, 'Unable to connect to remove server. Have you configured an SSH key?');

        return $exitCode;
    }
}
