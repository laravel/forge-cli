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
     * Indicates whether the command should be shown in the Artisan command list.
     *
     * @var bool
     */
    protected $hidden = true;

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle()
    {
        abort(1, 'Checking a daemon\'s status is not yet supported');
    }
}
