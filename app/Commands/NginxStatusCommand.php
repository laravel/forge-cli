<?php

namespace App\Commands;

use function Laravel\Prompts\info;
use function Laravel\Prompts\spin;

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
        spin(function () {
            [$exitCode] = $this->remote->exec('systemctl is-active --quiet nginx');

            abort_if($exitCode != 0, 1, 'Service is not running.');
        }, 'Checking the service status');

        info('Nginx is up & running');
    }
}
