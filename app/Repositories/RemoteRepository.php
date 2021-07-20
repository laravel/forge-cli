<?php

namespace App\Repositories;

class RemoteRepository
{
    /**
     * The sockets path.
     *
     * @var string
     */
    protected $socketsPath;

    /**
     * The server.
     *
     * @var \Laravel\Forge\Resources\Server|null
     */
    protected $server = null;

    /**
     * The server resolver.
     *
     * @var callable|null
     */
    protected $serverResolver = null;

    /**
     * Creates a new repository instance.
     *
     * @param  string  $socketsPath
     * @return void
     */
    public function __construct($socketsPath)
    {
        $this->socketsPath = $socketsPath;
    }

    /**
     * Execute a command against the shell, and displays the output.
     *
     * @param  string|null  $command
     * @return int
     */
    public function passthru($command = null)
    {
        $this->ensureSshIsConfigured();

        passthru($this->ssh($command), $exitCode);

        return (int) $exitCode;
    }

    /**
     * Execute a command against the shell, and returns the output.
     *
     * @param  string  $command
     * @return array
     */
    public function exec($command)
    {
        $this->ensureSshIsConfigured();

        exec($this->ssh($command), $output, $exitCode);

        return [(int) $exitCode, $output];
    }

    /**
     * Sets the current server.
     *
     * @param  callable  $resolver
     * @return void
     */
    public function resolveServerUsing($resolver)
    {
        $this->serverResolver = $resolver;
    }

    /**
     * Ensure user can connect to current server.
     *
     * @return void
     */
    protected function ensureSshIsConfigured()
    {
        once(function () {
            abort_if(is_null($this->serverResolver), 1, 'Current server unresolvable.');

            if (is_null($this->server)) {
                $this->server = call_user_func($this->serverResolver);
            }

            exec($this->ssh('-t exit 0'), $_, $exitCode);

            abort_if($exitCode > 0, 1, 'Unable to connect to remote server. Have you configured an SSH Key?');
        });
    }

    /**
     * Returns the "ssh" sheel command to be run.
     *
     * @param  string  $command|null
     * @return string
     */
    protected function ssh($command = null)
    {
        $options = collect([
            'ControlMaster' => 'auto',
            'ControlPersist' => 100,
            'ControlPath' => $this->socketsPath.'/%h-%p-%r',
            'LogLevel' => 'QUIET',
        ])->map(function ($value, $option) {
            return "-o $option=$value";
        })->values()->implode(' ');

        return trim(sprintf(
            'ssh %s -t forge@%s %s',
            $options,
            $this->server->ipAddress,
            $command,
        ));
    }
}
