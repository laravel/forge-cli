<?php

namespace App\Commands;

class NginxRestartCommand extends Command
{
    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = 'nginx:restart';

    /**
     * The description of the command.
     *
     * @var string
     */
    protected $description = 'Restart Nginx';

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle()
    {
        $server = $this->currentServer();

        if ($this->restartNginx($server->id)) {
            $this->successfulStep('Nginx restart initiated successfully.');
        }
    }

    /**
     * Restarts Nginx service.
     *
     * @param  string|int $serverId
     * @return bool
     */
    public function restartNginx($serverId)
    {
        if ($restarting = $this->confirm('The sites may become unavailable while the <comment>[Nginx]</comment> service restarts. Continue?')) {
            $this->step('Restarting Nginx');

            $this->forge->rebootNginx($serverId);
        }

        return $restarting;
    }
}
