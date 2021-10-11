<?php

namespace App\Repositories;

use App\Support\Boolean;
use Illuminate\Support\Arr;
use Symfony\Component\Process\Process;

class RemoteRepository
{
    /**
     * The configuration repository.
     *
     * @var \App\Repositories\ConfigRepository
     */
    protected $config;

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
     * Holds the sanitizable output.
     *
     * @var string|null
     */
    protected $sanitizableOutput = null;

    /**
     * Creates a new repository instance.
     *
     * @param string $socketsPath
     * @param \App\Repositories\ConfigRepository $config
     * @return void
     */
    public function __construct($socketsPath, $config)
    {
        $this->socketsPath = $socketsPath;
        $this->config = $config;
    }

    /**
     * Execute a command against the shell, and displays the output.
     *
     * @param string|null $command
     * @return int
     */
    public function passthru($command = null)
    {
        $this->ensureSshIsConfigured();

        passthru($this->ssh('"' . $command . '"'), $exitCode);

        return (int)$exitCode;
    }

    /**
     * Ensure user can connect to current server.
     *
     * @return void
     */
    public function ensureSshIsConfigured()
    {
        once(function () {
            abort_if(is_null($this->serverResolver), 1, 'Current server unresolvable.');

            if (is_null($this->server)) {
                $this->server = call_user_func($this->serverResolver);
            }

            $this->sanitizableOutput = exec($this->ssh('-t exit 0'), $_, $exitCode);

            abort_if($exitCode > 0, 1, 'Unable to connect to remote server. Maybe run [ssh:configure] to configure an SSH Key?');
        });
    }

    /**
     * Returns the "ssh" sheel command to be run.
     *
     * @param string $command |null
     * @return string
     */
    protected function ssh($command = null)
    {
        $options = collect([
            'ConnectTimeout' => 5,
            'ControlMaster' => 'auto',
            'ControlPersist' => 100,
            'ControlPath' => $this->socketsPath . '/%h-%p-%r',
            'LogLevel' => 'QUIET',
        ])->map(function ($value, $option) {
            return "-o $option=$value";
        })->values()->implode(' ');

        return trim(sprintf(
            'ssh %s -t forge@%s %s',
            $options,
            Boolean::fromValue($this->config->get('ssh_private_ip_access', false)) ? $this->server->privateIpAddress : $this->server->ipAddress,
            $command,
        ));
    }

    /**
     * Tails the given file, and runs the given callback on each output.
     *
     * @param array|string $files
     * @param callable $callback
     * @param array $options
     * @return array
     */
    public function tail($files, $callback, $options = [])
    {
        $this->ensureSshIsConfigured();

        $files = collect(Arr::wrap($files));

        $this->ensuresFilesAreTailable($files);

        $command = collect(explode(' ', $this->ssh()))->merge(['tail', '-n', '500'])
            ->merge($options)
            ->push('$(ls -1td ' . $files->implode(' ') . ' 2>/dev/null | head -n1)')
            ->filter()
            ->values()
            ->all();

        $process = tap(new Process($command), function ($process) {
            $process->setTimeout(null);

            $process->start();
        });

        $output = [];

        foreach ($process as $line) {
            if ($this->sanitizableOutput && strpos($line, $this->sanitizableOutput) === 0) {
                continue;
            }

            $output[] = $line;

            $callback($line);
        }

        $exitCode = $process->getExitCode() == 255
            ? 0 // Control + C
            : $process->getExitCode();

        return [(int)$exitCode, $output];
    }

    /**
     * Ensures the given files are tailable.
     *
     * @param \Illuminate\Support\Collection $files
     * @return void
     */
    protected function ensuresFilesAreTailable($files)
    {
        $files->reject(function ($file) {
            [$exitCode] = $this->exec('ls -1td ' . $file);

            return $exitCode > 0;
        })->whenEmpty(function () {
            abort(1, 'The requested logs could not be found or they are empty.');
        });
    }

    /**
     * Execute a command against the shell, and returns the output.
     *
     * @param string $command
     * @return array
     */
    public function exec($command)
    {
        $this->ensureSshIsConfigured();

        exec($this->ssh($command), $output, $exitCode);

        if ($this->sanitizableOutput && isset($output[0]) && strpos($output[0], $this->sanitizableOutput) === 0) {
            unset($output[0]);
        }

        return [(int)$exitCode, array_values($output)];
    }

    /**
     * Sets the current server.
     *
     * @param callable $resolver
     * @return void
     */
    public function resolveServerUsing($resolver)
    {
        $this->serverResolver = $resolver;
    }
}
