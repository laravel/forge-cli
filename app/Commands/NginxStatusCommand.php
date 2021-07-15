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
    protected $description = 'Get the current status of nginx';

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle()
    {
        $server = $this->currentServer();

        $status = $this->serviceStatus($server, 'nginx');

        $this->info('Nginx service is '.$status.'.');
    }
}
