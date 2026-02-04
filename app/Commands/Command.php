<?php

namespace App\Commands;

use App\Repositories\ConfigRepository;
use App\Repositories\ForgeRepository;
use App\Repositories\KeyRepository;
use App\Repositories\LocalConfigRepository;
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
        Concerns\InteractsWithEnvironments,
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
     * The local configuration repository.
     *
     * @var \App\Repositories\LocalConfigRepository
     */
    protected $localConfig;

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
     */
    public function __construct(
        ConfigRepository $config,
        LocalConfigRepository $localConfig,
        ForgeRepository $forge,
        KeyRepository $keys,
        RemoteRepository $remote,
        Time $time
    ) {
        parent::__construct();

        $this->config = $config;
        $this->localConfig = $localConfig;
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
            // Priority 1: Environment-based config (named environments or legacy .forge)
            $envServerId = $this->getEnvironmentServerId();

            if ($envServerId) {
                return $this->forge->server($envServerId);
            }

            // Priority 2: Global config
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
