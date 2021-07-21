<?php

namespace App\Support;

class Time
{
    /**
     * Delays the code execution for the given number of seconds.
     *
     * @param  int $seconds
     * @return void
     */
    public function sleep($seconds)
    {
        sleep($seconds);
    }
}
