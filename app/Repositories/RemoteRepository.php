<?php

namespace App\Repositories;

use Illuminate\Support\Arr;
use Symfony\Component\Process\Process;

class RemoteRepository
{
    /**
     * The private key resolver.
     *
     * @var callable|null
     */
    protected $privateKeyResolver = null;

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
     * @param  string  $user
     * @return int
     */
    public function passthru($command = null, $user = 'forge')
    {
        $this->ensureSshIsConfigured();

        passthru($this->ssh('"'.$command.'"', $user), $exitCode);

        return (int) $exitCode;
    }

    /**
     * Tails the given file, and runs the given callback on each output.
     *
     * @param  array|string  $files
     * @param  callable  $callback
     * @param  array  $options
     * @return array
     */
    public function tail($files, $callback, $options = [])
    {
        $this->ensureSshIsConfigured();

        $files = collect(Arr::wrap($files));

        $this->ensuresFilesAreTailable($files);

        $command = collect(explode(' ', $this->ssh()))->merge(['tail', '-n', '500'])
            ->merge($options)
            ->push('$(ls -1td '.$files->implode(' ').' 2>/dev/null | head -n1)')
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

        return [(int) $exitCode, $output];
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

        if ($this->sanitizableOutput && isset($output[0]) && strpos($output[0], $this->sanitizableOutput) === 0) {
            unset($output[0]);
        }

        return [(int) $exitCode, array_values($output)];
    }

    /**
     * Sets the current private key resolver.
     *
     * @param  callable  $resolver
     * @return void
     */
    public function resolvePrivateKeyUsing($resolver)
    {
        $this->privateKeyResolver = $resolver;
    }

    /**
     * Sets the current server resolver.
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
     * Ensures the given files are tailable.
     *
     * @param  \Illuminate\Support\Collection  $files
     * @return void
     */
    protected function ensuresFilesAreTailable($files)
    {
        $files->reject(function ($file) {
            [$exitCode] = $this->exec('ls -1td '.$file);

            return $exitCode > 0;
        })->whenEmpty(function () {
            abort(1, 'The requested logs could not be found or they are empty.');
        });
    }

    /**
     * Returns the "ssh" shell command to be run.
     *
     * @param  string|null  $command
     * @param  string  $user
     * @return string
     */
    protected function ssh($command = null, $user = 'forge')
    {
        $options = collect([
            'ConnectTimeout' => 5,
            'ControlMaster' => 'auto',
            'ControlPersist' => 100,
            'ControlPath' => $this->socketsPath.'/%h-%p-%r',
            'LogLevel' => 'QUIET',
            'StrictHostKeyChecking' => 'no',
        ])->map(function ($value, $option) {
            return "-o $option=$value";
        })->values()->implode(' ');

        if ($this->privateKeyResolver) {
            $options .= sprintf(
                ' -o "IdentitiesOnly=yes" -i "%s"',
                call_user_func($this->privateKeyResolver)
            );
        }

        return trim(sprintf(
            'ssh %s -t %s@%s %s',
            $options,
            $user,
            $this->server->ipAddress,
            $command,
        ));
    }
}
