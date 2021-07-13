<?php

namespace App\Support;

class Shell
{
    /**
     * Execute a command against the shell.
     *
     * @param  string  $command
     * @return void
     */
    public function passthru($command)
    {
        passthru($command);
    }
}
