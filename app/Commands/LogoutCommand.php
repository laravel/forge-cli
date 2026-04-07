<?php

namespace App\Commands;

class LogoutCommand extends Command
{
    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = 'logout {--force : Skip confirmation prompt}';

    /**
     * The description of the command.
     *
     * @var string
     */
    protected $description = 'Log out from Laravel Forge';

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle()
    {
        if (! $this->option('force') && ! $this->option('no-interaction') && ! $this->confirmStep('Are you sure you want to log out? This will remove your stored API token and configuration')) {
            return;
        }

        $this->config->flush();

        $this->successfulStep('Logged Out Successfully');
    }
}
