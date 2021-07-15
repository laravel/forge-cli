<?php

namespace App\Commands;

use App\Repositories\ConfigRepository;
use App\Repositories\ForgeRepository;
use App\Support\Shell;
use Laravel\Forge\Forge;
use Laravel\Forge\Resources\Server;
use LaravelZero\Framework\Commands\Command as BaseCommand;

abstract class Command extends BaseCommand
{
    /**
     * The configuration repository.
     *
     * @var \App\Repositories\ConfigRepository
     */
    protected $config;

    /**
     * The forge repository.
     *
     * @var \App\Repositories\ForgeRepository
     */
    protected $forge;

    /**
     * The shell.
     *
     * @var \App\Support\Shell
     */
    protected $shell;

    /**
     * Creates a new command instance.
     *
     * @param  \App\Repositories\ConfigRepository  $config
     * @param  \App\Repositories\ForgeRepository  $forge
     * @return void
     */
    public function __construct(
        ConfigRepository $config,
        ForgeRepository $forge,
        Shell $shell
    ) {
        parent::__construct();

        $this->config = $config;
        $this->forge = $forge;
        $this->shell = $shell;
    }

    /**
     * Gets the current server.
     *
     * @return \Laravel\Forge\Resources\Server
     */
    public function currentServer()
    {
        return once(function () {
            return $this->forge->server(
                $this->config->get('server')
            );
        });
    }

    /**
     * Gets the given service status.
     *
     * @param  \Laravel\Forge\Resources\Server  $server
     * @param  string  $name
     * @return string
     */
    public function serviceStatus($server, $name)
    {
        [$exitCode] = $this->shell->exec(sprintf(
            'ssh -t forge@%s systemctl is-active --quiet %s 2>/dev/null',
            $server->ipAddress,
            $name,
        ));

        switch ($exitCode) {
            case 0:
                return '<comment>[running]</comment>';
            case 255:
                abort(255, 'Unable to connect to remove server. Have you configured an SSH Key?');
        }

        return '<fg=red>[inactive]</>';
    }
}
