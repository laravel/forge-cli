<?php

namespace App\Commands;

class NginxStatusCommand extends Command
{
    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = 'nginx:status';

    /**
     * The description of the command.
     *
     * @var string
     */
    protected $description = 'Get the current status of Nginx';

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle()
    {
        $server = $this->currentServer();

        $this->ensureServiceIsRunning($server, 'nginx');

        $this->successfulStep('Nginx is up & running');
    }
}
