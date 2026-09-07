<?php

namespace App\Support;

class Time
{
    /**
     * Delays the code execution for the given number of seconds.
     *
     * @param  int  $seconds
     * @return void
     */
    public function sleep($seconds)
    {
        sleep($seconds);
    }

    /**
     * Get the current monotonic time, in seconds.
     *
     * This is only meaningful when compared to another reading, and is
     * unaffected by changes to the system clock.
     *
     * @return float
     */
    public function now()
    {
        return hrtime(true) / 1e9;
    }
}
