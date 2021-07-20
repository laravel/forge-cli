<?php

namespace App\Repositories;

class RemoteRepository
{
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
     * Execute a command against the shell, and displays the output.
     *
     * @param  string|null  $command
     * @return int
     */
    public function passthru($command = null)
    {
        $this->ensureSshIsConfigured();

        $command = sprintf(
            'ssh -o LogLevel=QUIET -t forge@%s %s',
            $this->server->ipAddress,
            $command,
        );

        passthru($command, $exitCode);

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

        $command = sprintf(
            'ssh -o LogLevel=QUIET -t forge@%s %s',
            $this->server->ipAddress,
            $command,
        );

        exec($command, $output, $exitCode);

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

            exec(sprintf(
                'ssh -o LogLevel=QUIET -t forge@%s -t exit 0',
                $this->server->ipAddress,
            ), $_, $exitCode);

            abort_if($exitCode > 0, 1, 'Unable to connect to remote server. Have you configured an SSH Key?');
        });
    }
}
