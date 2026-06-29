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

use function Laravel\Prompts\spin;

abstract class Command extends BaseCommand
{
    use Concerns\InteractsWithIO,
        Concerns\InteractsWithVersions;

    /**
     * The aliases of the command.
     *
     * @var array<string>
     */
    protected $aliases = [];

    /**
     * The configuration repository.
     *
     * @var ConfigRepository
     */
    protected $config;

    /**
     * The forge repository.
     *
     * @var ForgeRepository
     */
    protected $forge;

    /**
     * The keys repository.
     *
     * @var KeyRepository
     */
    protected $keys;

    /**
     * The remote connection.
     *
     * @var RemoteRepository
     */
    protected $remote;

    /**
     * The time.
     *
     * @var Time
     */
    protected $time;

    /**
     * Creates a new command instance.
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
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        return tap(parent::execute($input, $output), function () {
            $this->ensureLatestVersion();
        });
    }

    /**
     * Gets the current organization slug.
     *
     * @return string
     */
    public function currentOrganization()
    {
        $slug = $this->config->get('organization');

        abort_if(is_null($slug), 1, 'You have not selected an organization. Use the \'organization:switch\' command.');

        return $slug;
    }

    /**
     * Gets the current server.
     *
     * @return Server
     */
    public function currentServer()
    {
        return once(function () {
            $serverId = $this->config->get('server');

            abort_if(is_null($serverId), 1, 'You have not selected a server. Use the \'server:switch\' command.');

            return spin(
                fn () => $this->forge->server(
                    $this->currentOrganization(),
                    $serverId
                ),
                'Retrieving server',
            );
        });
    }

    /**
     * Ensure the given service is running.
     *
     * @param  Server  $server
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
