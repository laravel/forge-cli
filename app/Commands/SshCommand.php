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
    protected $description = 'Start a SSH session';

    /**
     * Execute the console command.
     *
     * @return int|never
     */
    public function handle()
    {
        $server = $this->currentServer();

        $exitCode = $this->remote->passthru();

        abort_if($exitCode == 255, $exitCode, 'Unable to connect to remove server. Have you configured an SSH Key?');

        return $exitCode;
    }
}
