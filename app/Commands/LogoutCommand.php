<?php

namespace App\Commands;

class LogoutCommand extends Command
{
    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = 'logout';

    /**
     * The description of the command.
     *
     * @var string
     */
    protected $description = 'Logout from Laravel Forge and remove your stored API token and server configuration';

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle()
    {
        if (! $this->confirmStep('Are you sure you want to log out? This will remove your stored API token and configuration')) {
            return;
        }

        $this->config->flush();

        $this->successfulStep('Logged Out Successfully');
    }
}
