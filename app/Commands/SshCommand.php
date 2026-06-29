<?php

namespace App\Commands;

use function Laravel\Prompts\info;
use function Laravel\Prompts\spin;

class SshCommand extends Command
{
    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = 'ssh {server? : The server name} {--u|user= : The user to connect to the server as}';

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

        spin(
            fn () => $this->remote->ensureSshIsConfigured(),
            'Establishing secure connection',
        );

        info("Connected to {$server->name}");

        $exitCode = $this->remote->passthru(null, $username);

        abort_if($exitCode == 255, $exitCode, 'Unable to connect to remote server. Have you configured an SSH key?');

        return $exitCode;
    }
}
