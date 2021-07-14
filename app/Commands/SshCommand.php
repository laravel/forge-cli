<?php

namespace App\Commands;

use App\Exceptions\MissingSshKeyException;

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
     * @return int|never
     */
    public function handle()
    {
        $server = $this->currentServer();

        $exitCode = $this->shell->passthru(sprintf(
            'ssh -t forge@%s',
            $server->ipAddress,
        ));

        if ($exitCode == 255) {
            MissingSshKeyException::raise();
        }

        return $exitCode;
    }
}
