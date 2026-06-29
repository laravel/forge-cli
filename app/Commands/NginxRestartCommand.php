<?php

namespace App\Commands;

use Laravel\Forge\Resources\Server;

use function Laravel\Prompts\confirm;
use function Laravel\Prompts\info;
use function Laravel\Prompts\spin;

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

        if ($this->restartNginx($server)) {
            info('Nginx restart initiated successfully.');
        }
    }

    /**
     * Restarts Nginx service.
     *
     * @return bool
     */
    public function restartNginx(Server $server)
    {
        if ($restarting = confirm('The sites may become unavailable while the Nginx service restarts. Continue?')) {
            spin(
                fn () => $server->rebootNginx(),
                'Restarting Nginx',
            );
        }

        return $restarting;
    }
}
