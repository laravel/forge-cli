<?php

namespace App\Commands;

use App\Repositories\ConfigRepository;
use App\Repositories\ForgeRepository;
use App\Repositories\KeyRepository;
use App\Repositories\RemoteRepository;
use App\Support\Time;
use Laravel\Forge\Forge;
use Laravel\Forge\Resources\Server;
use LaravelZero\Framework\Commands\Command as BaseCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

abstract class Command extends BaseCommand
{
    use Concerns\InteractsWithIO,
        Concerns\InteractsWithVersions;

    /**
     * The aliases of the command.
     *
     * @var array
     */
    protected $aliases = [];

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
     * The keys repository.
     *
     * @var \App\Repositories\KeyRepository
     */
    protected $keys;

    /**
     * The remote connection.
     *
     * @var \App\Repositories\RemoteRepository
     */
    protected $remote;

    /**
     * The time.
     *
     * @var \App\Support\Time
     */
    protected $time;

    /**
     * Creates a new command instance.
     *
     * @param  \App\Repositories\ConfigRepository  $config
     * @param  \App\Repositories\ForgeRepository  $forge
     * @param  \App\Repositories\KeyRepository  $keys
     * @param  \App\Repositories\RemoteRepository  $remote
     * @param  \App\Support\Time  $time
     * @return void
     */
    public function __construct(
        ConfigRepository $config,
        ForgeRepository $forge,
        KeyRepository $keys,
        RemoteRepository $remote,
        Time $time
    ) {
        parent::__construct();

        $this->config = $config;
        $this->forge = $forge;
        $this->keys = $keys;
        $this->time = $time;

        $this->remote = tap($remote)->resolveServerUsing(function () {
            return $this->currentServer();
        });

        $this->setAliases($this->aliases);
    }

    /**
     * Execute the console command.
     *
     * @param  \Symfony\Component\Console\Input\InputInterface  $input
     * @param  \Symfony\Component\Console\Output\OutputInterface  $output
     * @return int
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        return tap(parent::execute($input, $output), function () {
            $this->ensureLatestVersion();
        });
    }

    /**
     * Ensure the current team is set in the configuration file.
     *
     * @return void
     */
    protected function ensureCurrentTeamIsSet()
    {
        if (! $this->config->get('server', false)) {
            $server = collect($this->forge->servers())->first();

            abort_if($server == null, 1, 'Please create a server first.');

            $this->config->set('server', $server->id);
        }
    }

    /**
     * Gets the current server.
     *
     * @return \Laravel\Forge\Resources\Server
     */
    public function currentServer()
    {
        return once(function () {
            $this->ensureCurrentTeamIsSet();

            return $this->forge->server(
                $this->config->get('server')
            );
        });
    }

    /**
     * Ensure the given service is running.
     *
     * @param  \Laravel\Forge\Resources\Server  $server
     * @param  string  $name
     * @return void
     */
    public function ensureServiceIsRunning($server, $name)
    {
        $this->step('Checking the service status');

        [$exitCode] = $this->remote->exec(sprintf(
            'systemctl is-active --quiet %s',
            $name,
        ));

        abort_if($exitCode != 0, 1, 'Service is not running.');
    }
}
