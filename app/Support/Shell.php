<?php

namespace App\Support;

class Shell
{
    /**
     * Execute a command against the shell, and displays the output.
     *
     * @param  string  $command
     * @return int
     */
    public function passthru($command)
    {
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
        exec($command, $output, $exitCode);

        return [(int) $exitCode, $output];
    }
}
