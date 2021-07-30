<?php

namespace App\Commands;

class DaemonStatusCommand extends Command
{
    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = 'daemon:status {daemon? : The daemon ID}';

    /**
     * The description of the command.
     *
     * @var string
     */
    protected $description = 'Get the current status of a daemon';

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle()
    {
        abort(1, 'Checking a daemon status is not yet supported');
    }
}
