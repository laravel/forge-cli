<?php

namespace App\Commands;

class SshCommand extends Command
{
    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = 'ssh {server? : The server name} {--u|user= : The remote username}';

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

        $server = $this->currentServer();
        $username = $this->option('user') ?: 'forge';

        $this->step('Establishing secure connection');

        $this->remote->ensureSshIsConfigured();

        $this->successfulStep('Connected To <comment>['.$server->name.']</comment>');

        $exitCode = $this->remote->passthru(null, $username);

        abort_if($exitCode == 255, $exitCode, 'Unable to connect to remote server. Have you configured an SSH key?');

        return $exitCode;
    }
}
