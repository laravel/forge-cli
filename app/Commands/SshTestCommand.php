<?php

namespace App\Commands;

class SshTestCommand extends Command
{
    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = 'ssh:test
        {server? : The server name}
        {--key= : The path to the public key}';

    /**
     * The description of the command.
     *
     * @var string
     */
    protected $description = 'Test the SSH key based secure authentication connection';

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle()
    {
        if (! is_null($server = $this->argument('server'))) {
            $this->call('server:switch', [
                'server' => $server,
            ]);
        }

        $this->step('Establishing secure connection');

        $this->remote->ensureSshIsConfigured();

        $this->successfulStep('SSH key based secure authentication is configured');
    }
}
