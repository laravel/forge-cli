<?php

namespace App\Commands;

use App\Commands\Concerns\SetsRemoteSshLogin;

class SshCommand extends Command
{
    use SetsRemoteSshLogin;
    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = 'ssh {server? : The server name}
                            {--U|user= : SSH user login}';

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

        $this->setRemoteSshLogin($this->option('user'));

        $server = $this->currentServer();

        $this->step('Establishing secure connection');

        $this->remote->ensureSshIsConfigured();

        $this->successfulStep('Connected To <comment>['.$server->name.']</comment>');

        $exitCode = $this->remote->passthru();

        abort_if($exitCode == 255, $exitCode, 'Unable to connect to remote server. Have you configured an SSH key?');

        return $exitCode;
    }
}
