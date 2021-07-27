<?php

namespace App\Commands;

class DaemonRestartCommand extends Command
{
    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = 'daemon:restart {daemon? : The daemon ID}';

    /**
     * The description of the command.
     *
     * @var string
     */
    protected $description = 'Restart a daemon';

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle()
    {
        $server = $this->currentServer();

        $daemonId = $this->askForDaemon('Which daemon would you like to restart');

        $daemon = $this->forge->daemon($server->id, $daemonId);

        abort_unless($daemon->status == 'installed', 1, 'This deamon is not installed or is not running.');

        $this->step(['Restarting Daemon %s', $daemon->command]);

        $this->forge->restartDaemon($server->id, $daemonId, false);

        $this->successfulStep('Daemon Restart Initiated Successfully.');
    }
}
