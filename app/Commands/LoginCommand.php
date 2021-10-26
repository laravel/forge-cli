<?php

namespace App\Commands;

class LoginCommand extends Command
{
    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = 'login {--token= : Forge API token}';

    /**
     * The description of the command.
     *
     * @var string
     */
    protected $description = 'Authenticate with Laravel Forge';

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle()
    {
        $token = $this->option('token');

        if ($token === null) {
            $token = $this->askStep('Please enter your Forge API token');
        }

        $this->config->set('token', $token);

        $email = $this->getUserEmail();

        $this->ensureCurrentTeamIsSet();

        $this->successfulStep("Authenticated successfully as <comment>[$email]</comment>");
    }

    /**
     * Gets user's email.
     *
     * @return string
     */
    protected function getUserEmail()
    {
        return $this->forge->user()->email;
    }
}
