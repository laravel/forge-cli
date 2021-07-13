<?php

namespace App\Commands;

class SshCommand extends Command
{
    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = 'ssh';

    /**
     * The description of the command.
     *
     * @var string
     */
    protected $description = 'Start an SSH connection with your current server';

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle()
    {
        $server = $this->currentServer();

        $this->shell->passthru(sprintf(
            'ssh -t forge@%s',
            $server->ipAddress,
        ));
    }
}
